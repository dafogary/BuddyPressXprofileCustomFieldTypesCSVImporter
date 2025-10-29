<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PhpOffice\PhpSpreadsheet\IOFactory;

class BPXP_Importer {

	protected $file;
	protected $data;
	protected $group_id;

	public function __construct( $file_path ) {
		$this->file = $file_path;
	}

	/**
	 * Read file into array of rows (associative: header => value)
	 */
	public function parse() {
		$ext = strtolower( pathinfo( $this->file, PATHINFO_EXTENSION ) );

		// If PhpSpreadsheet exists and file is xlsx/xls/ods -> use it
		if ( class_exists( 'PhpOffice\PhpSpreadsheet\IOFactory' ) && in_array( $ext, [ 'xls', 'xlsx', 'ods' ] ) ) {
			return $this->parse_with_phpspreadsheet();
		}

		// Fallback: parse CSV-like files
		return $this->parse_csv();
	}

	protected function parse_with_phpspreadsheet() {
		try {
			$spreadsheet = IOFactory::load( $this->file );
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray(null, true, true, true);

			if ( empty( $rows ) ) {
				return [];
			}

			// First row is header
			$header = array_values( $rows[1] );
			$out = [];
			for ( $i = 2, $len = count( $rows ); $i <= $len; $i++ ) {
				$row = array_values( $rows[$i] );
				$item = [];
				foreach ( $header as $j => $col ) {
					$colname = strtolower( trim( $col ) );
					$item[ $colname ] = isset( $row[$j] ) ? trim( $row[$j] ) : '';
				}
				$out[] = $item;
			}
			$this->data = $out;
			return $out;
		} catch ( Exception $e ) {
			bpxpi_log( 'PhpSpreadsheet error: ' . $e->getMessage() );
			return [];
		}
	}

	protected function parse_csv() {
		$out = [];
		if ( ( $handle = fopen( $this->file, 'r' ) ) !== false ) {
			$header = fgetcsv( $handle );
			if ( ! $header ) {
				return [];
			}
			$header = array_map( function ( $h ) { return strtolower( trim( $h ) ); }, $header );
			while ( ( $row = fgetcsv( $handle ) ) !== false ) {
				$item = [];
				foreach ( $header as $i => $col ) {
					$item[ $col ] = isset( $row[ $i ] ) ? trim( $row[ $i ] ) : '';
				}
				$out[] = $item;
			}
			fclose( $handle );
		}
		$this->data = $out;
		return $out;
	}

	/**
	 * Import parsed rows into BuddyPress XProfile fields.
	 *
	 * Expected column names (case-insensitive):
	 *  - field name, name
	 *  - type
	 *  - description
	 *  - is_required (1/0 or yes/no)
	 *  - options (comma separated - for selectbox, radio etc)
	 *  - order (int)
	 *  - group (group id or name)
	 *  - parent_field (field name or id) -- used for conditional plugin mapping later
	 *  - show_if_value (value that triggers visibility)
	 *
	 * Returns array with results per row.
	 */
	public function import( $default_group_id = 0 ) {
		if ( empty( $this->data ) ) {
			$this->parse();
		}

		if ( empty( $this->data ) ) {
			return new WP_Error( 'empty', __( 'No data to import', 'bp-xprofile-importer' ) );
		}

		$results = [];

		foreach ( $this->data as $row ) {
			$name = isset( $row['field name'] ) ? $row['field name'] : ( isset( $row['name'] ) ? $row['name'] : '' );
			if ( empty( $name ) ) {
				$results[] = [ 'status' => 'skip', 'reason' => 'missing_name', 'row' => $row ];
				continue;
			}

			$type = isset( $row['type'] ) ? $row['type'] : 'text';
			$description = isset( $row['description'] ) ? $row['description'] : '';
			$is_required = isset( $row['is_required'] ) ? filter_var( $row['is_required'], FILTER_VALIDATE_BOOLEAN ) : false;
			$options = isset( $row['options'] ) ? $row['options'] : '';
			$order = isset( $row['order'] ) ? intval( $row['order'] ) : 0;
			$group = isset( $row['group'] ) ? $row['group'] : $default_group_id;

			// Resolve group id (string name -> id)
			$group_id = $this->resolve_group_id( $group ) ?: $default_group_id;

			$field = new BP_XProfile_Field();
			$field->group_id = $group_id;
			$field->name = sanitize_text_field( $name );
			$field->description = sanitize_textarea_field( $description );
			$field->type = sanitize_text_field( $type );
			$field->is_required = $is_required ? 1 : 0;
			$field->order_by = 'custom';
			if ( $order ) {
				$field->sort_order = $order;
			}

			// For selectable options, set the 'options' param (BuddyPress expects choices when saving values)
			if ( ! empty( $options ) ) {
				$field->data = [];
				$field->options = array_map( 'trim', explode( ',', $options ) );
			}

			try {
				$field_id = $field->save();

				$results[] = [
					'status' => 'created',
					'field_id' => $field_id,
					'name' => $name,
				];

				// If there is conditional info present, queue for conditional processing.
				if ( ! empty( $row['parent_field'] ) && ! empty( $row['show_if_value'] ) ) {
					// Let conditional helper handle relationship later or now.
					$bpxp_cond = new BPXP_Conditional();
					$bpxp_cond->maybe_create_relation_from_row( $field_id, $row );
				}

			} catch ( Exception $e ) {
				$results[] = [ 'status' => 'error', 'error' => $e->getMessage(), 'row' => $row ];
			}
		}

		return $results;
	}

	protected function resolve_group_id( $group ) {
		if ( empty( $group ) ) {
			return 0;
		}
		// If numeric, return as int
		if ( is_numeric( $group ) ) {
			return intval( $group );
		}

		// Try to find group by name
		if ( function_exists( 'xprofile_get_field_groups' ) ) {
			$groups = xprofile_get_field_groups();
			foreach ( $groups as $g ) {
				if ( strtolower( $g->name ) === strtolower( $group ) ) {
					return intval( $g->id );
				}
			}
		}

		return 0;
	}
}
