<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BPXP_Exporter {

	/**
	 * Export fields for $group_id (int). Outputs spreadsheet directly.
	 */
	public function export_group( $group_id = 0 ) {
		$groups = bpxpi_get_field_groups();
		if ( empty( $groups ) ) {
			wp_die( __( 'BuddyPress/BuddyBoss XProfile not available or no field groups found', 'bp-xprofile-importer' ) );
		}

		$fields = $this->get_fields_by_group( $group_id );

		$spreadsheet = null;
		$use_phpspreadsheet = class_exists( 'PhpOffice\PhpSpreadsheet\Spreadsheet' );

		if ( $use_phpspreadsheet ) {
			$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$header = [ 'Field Name', 'Type', 'Description', 'Is_Required', 'Options', 'Order', 'Group', 'Can_Delete', 'Parent_Field', 'Show_If_Value' ];
			$sheet->fromArray( $header, NULL, 'A1' );

			$row = 2;
			foreach ( $fields as $f ) {
				$options = '';
				if ( ! empty( $f->options ) && is_array( $f->options ) ) {
					// Handle both simple array and structured options with order
					$option_texts = [];
					foreach ( $f->options as $option ) {
						if ( is_string( $option ) ) {
							// Simple string option
							$option_texts[] = $option;
						} elseif ( is_array( $option ) && isset( $option['option_value'] ) ) {
							// Structured option with possible order
							$option_texts[] = $option['option_value'];
						} elseif ( is_object( $option ) && isset( $option->name ) ) {
							// BuddyPress option object
							$option_texts[] = $option->name;
						}
					}
					$options = implode( ',', $option_texts );
				}
				$parent_field = $this->get_conditional_parent_for_field( $f->id );
				$show_if = $this->get_conditional_value_for_field( $f->id );

				$sheet->setCellValueExplicit( 'A' . $row, $f->name, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING );
				$sheet->setCellValue( 'B' . $row, $f->type );
				$sheet->setCellValue( 'C' . $row, $f->description );
				$sheet->setCellValue( 'D' . $row, $f->is_required ? '1' : '0' );
				$sheet->setCellValue( 'E' . $row, $options );
				$sheet->setCellValue( 'F' . $row, isset( $f->field_order ) ? intval( $f->field_order ) : '' );
				$sheet->setCellValue( 'G' . $row, $group_id );
				$sheet->setCellValue( 'H' . $row, isset( $f->can_delete ) ? ( $f->can_delete ? '1' : '0' ) : '1' );
				$sheet->setCellValue( 'I' . $row, $parent_field );
				$sheet->setCellValue( 'J' . $row, $show_if );
				$row++;
			}

			$filename = 'bpxp-export-group-' . $group_id . '.xlsx';
			header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
			header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
			$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx( $spreadsheet );
			$writer->save( 'php://output' );
			exit;
		}

		// Fallback: CSV
		$filename = 'bpxp-export-group-' . $group_id . '.csv';
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		$out = fopen( 'php://output', 'w' );
		fputcsv( $out, [ 'Field Name', 'Type', 'Description', 'Is_Required', 'Options', 'Order', 'Group', 'Can_Delete', 'Parent_Field', 'Show_If_Value' ] );

		foreach ( $fields as $f ) {
			$options = '';
			if ( ! empty( $f->options ) && is_array( $f->options ) ) {
				// Handle both simple array and structured options with order
				$option_texts = [];
				foreach ( $f->options as $option ) {
					if ( is_string( $option ) ) {
						// Simple string option
						$option_texts[] = $option;
					} elseif ( is_array( $option ) && isset( $option['option_value'] ) ) {
						// Structured option with possible order
						$option_texts[] = $option['option_value'];
					} elseif ( is_object( $option ) && isset( $option->name ) ) {
						// BuddyPress option object
						$option_texts[] = $option->name;
					}
				}
				$options = implode( ',', $option_texts );
			}
			$parent_field = $this->get_conditional_parent_for_field( $f->id );
			$show_if = $this->get_conditional_value_for_field( $f->id );

			fputcsv( $out, [
				$f->name,
				$f->type,
				$f->description,
				$f->is_required ? '1' : '0',
				$options,
				isset( $f->field_order ) ? intval( $f->field_order ) : '',
				$group_id,
				isset( $f->can_delete ) ? ( $f->can_delete ? '1' : '0' ) : '1',
				$parent_field,
				$show_if,
			] );
		}
		fclose( $out );
		exit;
	}

	protected function get_fields_by_group( $group_id ) {
		$args = [
			'group_id' => intval( $group_id ),
		];

		// BuddyPress function to get fields:
		if ( function_exists( 'bp_xprofile_get_fields' ) ) {
			return bp_xprofile_get_fields( $args );
		}

		// Fallback: try global
		if ( class_exists( 'BP_XProfile_Field' ) ) {
			// Not implementing full query — return empty
			return [];
		}

		return [];
	}

	protected function get_conditional_parent_for_field( $field_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'bp_xprofile_conditional_fields';
		$parent = $wpdb->get_var( $wpdb->prepare( "SELECT parent_field_id FROM $table WHERE field_id = %d LIMIT 1", $field_id ) );
		return $parent ? $parent : '';
	}

	protected function get_conditional_value_for_field( $field_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'bp_xprofile_conditional_fields';
		$value = $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM $table WHERE field_id = %d LIMIT 1", $field_id ) );
		return $value ? $value : '';
	}
}
