<?php
/**
 * Plugin Name: BuddyPress XProfile Importer
 * Description: Import and export BuddyPress XProfile fields (with support for conditional fields). Starter plugin.
 * Version: 0.1.6
 * Author: Gary Foster - DAFO Creative LLC/Ltd
 * Text Domain: bp-xprofile-importer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BPXPI_PATH', plugin_dir_path( __FILE__ ) );
define( 'BPXPI_URL', plugin_dir_url( __FILE__ ) );

require_once BPXPI_PATH . 'includes/helper.php';
require_once BPXPI_PATH . 'includes/class-bpxp-importer.php';
require_once BPXPI_PATH . 'includes/class-bpxp-exporter.php';
require_once BPXPI_PATH . 'includes/class-bpxp-conditional.php';
require_once BPXPI_PATH . 'admin/class-bpxp-admin-page.php';

add_action( 'plugins_loaded', function() {
	// Always instantiate admin page for administrators
	if ( is_admin() ) {
		new BPXP_Admin_Page();
	}
} );
