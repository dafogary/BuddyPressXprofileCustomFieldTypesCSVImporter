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
