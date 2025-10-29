<?php
/**
 * Plugin Name: BuddyPress XProfile Importer
 * Description: Import and export BuddyPress XProfile fields (with support for conditional fields). Starter plugin.
 * Version: 0.1.0
 * Author: Gary Foster - DAFO Creative LLC/Ltd
 * Text Domain: bp-xprofile-importer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BPXPI_PATH', plugin_dir_path( __FILE__ ) );
define( 'BPXPI_URL', plugin_dir_url( __FILE__ ) );

require_once BPXPI_PATH . 'includes/helpers.php';
require_once BPXPI_PATH . 'includes/class-bpxp-importer.php';
require_once BPXPI_PATH . 'includes/class-bpxp-exporter.php';
require_once BPXPI_PATH . 'includes/class-bpxp-conditional.php';
require_once BPXPI_PATH . 'admin/class-bpxp-admin-page.php';

add_action( 'plugins_loaded', function() {
	// show admin notice if BuddyPress not active
	if ( ! function_exists( 'xprofile_get_field_groups' ) && ! class_exists( 'BP_XProfile_Field' ) ) {
		add_action( 'admin_notices', function() {
			echo '<div class="error"><p><strong>BuddyPress XProfile Importer:</strong> BuddyPress or BuddyBoss must be active for this plugin to work. Note this has only been tested with BuddyBoss.</p></div>';
		} );

		return;
	}

	// instantiate admin page
	if ( is_admin() ) {
		new BPXP_Admin_Page();
	}
} );
