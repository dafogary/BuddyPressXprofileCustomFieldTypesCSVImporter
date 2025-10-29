<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Small helper to add conditional relationships into
 * the Conditional Profile Fields table (if plugin exists).
 */
class BPXP_Conditional {

	protected $table;

	public function __construct() {
		global $wpdb;
		$this->table = $wpdb->prefix . 'bp_xprofile_conditional_fields';
	}

	/**
	 * Try to create relation from import row.
	 * $row is associative array containing parent_field and show_if_value
	 */
	public function maybe_create_relation_from_row( $child_field_id, $row ) {
		global $wpdb;

		if ( empty( $row['parent_field'] ) || empty( $row['show_if_value'] ) ) {
			return false;
		}

		$parent = $row['parent_field'];

		// Resolve parent id (try numeric or lookup by name)
		$parent_id = is_numeric( $parent ) ? intval( $parent ) : $this->find_field_id_by_name( $parent );
		if ( ! $parent_id ) {
			return false;
		}

		$option_value = $row['show_if_value'];

		// Insert only if table exists
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$this->table}'" ) !== $this->table ) {
			// Conditional plugin not installed or table name differs
			return false;
		}

		$exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(1) FROM {$this->table} WHERE field_id = %d AND parent_field_id = %d AND option_value = %s", $child_field_id, $parent_id, $option_value ) );
		if ( $exists ) {
			return true;
		}

		$inserted = $wpdb->insert(
			$this->table,
			[
				'field_id' => $child_field_id,
				'parent_field_id' => $parent_id,
				'option_value' => $option_value,
			],
			[ '%d', '%d', '%s' ]
		);

		return (bool) $inserted;
	}

	protected function find_field_id_by_name( $name ) {
		global $wpdb;
		$table = $wpdb->prefix . 'bp_xprofile_fields';
		$id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table WHERE LOWER(name) = %s LIMIT 1", strtolower( $name ) ) );
		return $id ? intval( $id ) : 0;
	}
}
