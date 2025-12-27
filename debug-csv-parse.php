<?php
/**
 * Debug script to test CSV parsing
 * Run this from WordPress admin or via WP-CLI to see what data is being parsed
 */

// Include WordPress
if ( ! defined( 'ABSPATH' ) ) {
	// Adjust this path to your WordPress installation
	require_once( dirname( __FILE__ ) . '/../../../wp-load.php' );
}

// Include the importer class
require_once( dirname( __FILE__ ) . '/includes/class-bpxp-importer.php' );
require_once( dirname( __FILE__ ) . '/includes/helper.php' );

echo "<h2>CSV Debug Test</h2>\n";

$csv_file = dirname( __FILE__ ) . '/testset/Base questions.csv';

if ( ! file_exists( $csv_file ) ) {
	echo "Error: CSV file not found at {$csv_file}\n";
	exit;
}

echo "<h3>CSV File Content:</h3>\n";
echo "<pre>" . htmlspecialchars( file_get_contents( $csv_file ) ) . "</pre>\n";

echo "<h3>Parsed Data:</h3>\n";

$importer = new BPXP_Importer( $csv_file );
$data = $importer->parse();

echo "<pre>";
print_r( $data );
echo "</pre>";

echo "<h3>Order Values Analysis:</h3>\n";
echo "<table border='1'>\n";
echo "<tr><th>Field Name</th><th>Raw Order Value</th><th>Parsed Order (intval)</th><th>Is Empty?</th></tr>\n";

foreach ( $data as $row ) {
	$name = isset( $row['field name'] ) ? $row['field name'] : ( isset( $row['name'] ) ? $row['name'] : 'Unknown' );
	$raw_order = isset( $row['order'] ) ? $row['order'] : 'NOT SET';
	$parsed_order = isset( $row['order'] ) ? intval( $row['order'] ) : 'N/A';
	$is_empty = isset( $row['order'] ) ? ( $row['order'] === '' ? 'YES' : 'NO' ) : 'NOT SET';
	
	echo "<tr>";
	echo "<td>" . htmlspecialchars( $name ) . "</td>";
	echo "<td>" . htmlspecialchars( $raw_order ) . "</td>";
	echo "<td>" . htmlspecialchars( $parsed_order ) . "</td>";
	echo "<td>" . htmlspecialchars( $is_empty ) . "</td>";
	echo "</tr>\n";
}

echo "</table>\n";
?>