<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Simple logger for debug (uses error_log).
 * Replace with proper logging for production.
 */
function bpxpi_log( $message ) {
	if ( WP_DEBUG ) {
		if ( is_array( $message ) || is_object( $message ) ) {
			error_log( print_r( $message, true ) );
		} else {
			error_log( $message );
		}
	}
}

/**
 * Allowed upload mime types for spreadsheets (basic).
 * PhpSpreadsheet can handle many types; this is conservative.
 */
function bpxpi_allowed_mime_types() {
	return [
		'text/csv' => 'csv',
		'text/plain' => 'csv',
		'application/vnd.ms-excel' => 'xls',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
		'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
	];
}

/**
 * Get XProfile field groups - compatible with both BuddyPress and BuddyBoss
 */
function bpxpi_get_field_groups() {
	// Debug logging
	bpxpi_log( 'bpxpi_get_field_groups() called' );
	
	// Try the standard BuddyPress function first
	if ( function_exists( 'xprofile_get_field_groups' ) ) {
		$groups = xprofile_get_field_groups();
		bpxpi_log( 'xprofile_get_field_groups() found ' . count( $groups ) . ' groups' );
		return $groups;
	}
	
	// Try using the BP_XProfile_Group class directly
	if ( class_exists( 'BP_XProfile_Group' ) ) {
		bpxpi_log( 'Using BP_XProfile_Group class' );
		$groups = BP_XProfile_Group::get( array( 'fetch_fields' => true ) );
		bpxpi_log( 'BP_XProfile_Group::get() found ' . count( $groups ) . ' groups' );
		return $groups;
	}
	
	// Fallback: try to get groups from database directly
	global $wpdb;
	
	// Check if the xprofile groups table exists
	$table_name = $wpdb->prefix . 'bp_xprofile_groups';
	$table_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) );
	
	bpxpi_log( 'Checking table: ' . $table_name . ', exists: ' . ( $table_exists ? 'yes' : 'no' ) );
	
	if ( $table_exists ) {
		$groups = $wpdb->get_results( 
			"SELECT * FROM {$table_name} ORDER BY group_order ASC, id ASC"
		);
		
		bpxpi_log( 'Database query found ' . count( $groups ) . ' groups' );
		
		// Convert to objects with expected properties
		$result = array();
		foreach ( $groups as $group ) {
			$obj = new stdClass();
			$obj->id = $group->id;
			$obj->name = $group->name;
			$obj->description = isset( $group->description ) ? $group->description : '';
			$obj->group_order = isset( $group->group_order ) ? $group->group_order : 0;
			$result[] = $obj;
		}
		
		return $result;
	}
	
	bpxpi_log( 'No XProfile groups found' );
	return array();
}

/**
 * Debug function to show current BuddyPress/BuddyBoss status
 */
function bpxpi_debug_bp_status() {
	// Include the plugin functions if not already loaded
	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	
	$debug = array(
		'xprofile_get_field_groups function exists' => function_exists( 'xprofile_get_field_groups' ),
		'BP_XProfile_Group class exists' => class_exists( 'BP_XProfile_Group' ),
		'BP_XProfile_Field class exists' => class_exists( 'BP_XProfile_Field' ),
		'buddypress plugin active' => function_exists( 'is_plugin_active' ) ? is_plugin_active( 'buddypress/bp-loader.php' ) : false,
		'buddyboss platform active' => function_exists( 'is_plugin_active' ) ? is_plugin_active( 'buddyboss-platform/bp-loader.php' ) : false,
	);
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'bp_xprofile_groups';
	$debug['xprofile groups table exists'] = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) ? true : false;
	
	if ( $debug['xprofile groups table exists'] ) {
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );
		$debug['xprofile groups count in DB'] = $count;
	}
	
	return $debug;
}
