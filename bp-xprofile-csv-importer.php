<?php
/**
 * Plugin Name: BuddyPress Xprofile Custom Field Types CSV Importer
 * Plugin URI: https://github.com/dafogary/BuddyPressXprofileCustomFieldTypesCSVImporter
 * Description: Import and export BuddyPress Xprofile Custom Field Types from CSV/XLS/ODS files with support for categories and conditional fields
 * Version: 1.0.0
 * Author: dafogary
 * Author URI: https://github.com/dafogary
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: bp-xprofile-csv-importer
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('BP_XPROFILE_CSV_IMPORTER_VERSION', '1.0.0');
define('BP_XPROFILE_CSV_IMPORTER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BP_XPROFILE_CSV_IMPORTER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BP_XPROFILE_CSV_IMPORTER_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class BP_Xprofile_CSV_Importer {
    
    /**
     * Instance of this class.
     */
    protected static $instance = null;
    
    /**
     * Initialize the plugin
     */
    private function __construct() {
        // Load plugin text domain
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
        
        // Check for required plugins
        add_action('admin_init', array($this, 'check_dependencies'));
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Enqueue admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Register AJAX handlers
        add_action('wp_ajax_bp_xprofile_import_csv', array($this, 'ajax_import_csv'));
        add_action('wp_ajax_bp_xprofile_export_csv', array($this, 'ajax_export_csv'));
        add_action('wp_ajax_bp_xprofile_get_categories', array($this, 'ajax_get_categories'));
    }
    
    /**
     * Return an instance of this class.
     */
    public static function get_instance() {
        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    /**
     * Load plugin text domain
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'bp-xprofile-csv-importer',
            false,
            dirname(BP_XPROFILE_CSV_IMPORTER_PLUGIN_BASENAME) . '/languages/'
        );
    }
    
    /**
     * Check if required plugins are active
     */
    public function check_dependencies() {
        $dependencies = array();
        
        // Check for BuddyPress
        if (!class_exists('BuddyPress')) {
            $dependencies[] = 'BuddyPress';
        }
        
        // Check for BuddyPress Xprofile Custom Field Types
        if (!class_exists('BPXProfileCFTR')) {
            $dependencies[] = 'BuddyPress Xprofile Custom Field Types';
        }
        
        if (!empty($dependencies)) {
            add_action('admin_notices', function() use ($dependencies) {
                echo '<div class="error"><p>';
                printf(
                    __('BuddyPress Xprofile CSV Importer requires the following plugins to be installed and activated: %s', 'bp-xprofile-csv-importer'),
                    implode(', ', $dependencies)
                );
                echo '</p></div>';
            });
        }
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('BP Xprofile Importer', 'bp-xprofile-csv-importer'),
            __('BP Xprofile Importer', 'bp-xprofile-csv-importer'),
            'manage_options',
            'bp-xprofile-csv-importer',
            array($this, 'render_admin_page'),
            'dashicons-upload',
            80
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_bp-xprofile-csv-importer' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'bp-xprofile-csv-importer-admin',
            BP_XPROFILE_CSV_IMPORTER_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            BP_XPROFILE_CSV_IMPORTER_VERSION
        );
        
        wp_enqueue_script(
            'bp-xprofile-csv-importer-admin',
            BP_XPROFILE_CSV_IMPORTER_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            BP_XPROFILE_CSV_IMPORTER_VERSION,
            true
        );
        
        wp_localize_script('bp-xprofile-csv-importer-admin', 'bpXprofileImporter', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bp_xprofile_importer_nonce'),
            'strings' => array(
                'importSuccess' => __('Import completed successfully!', 'bp-xprofile-csv-importer'),
                'importError' => __('Import failed. Please check the file format.', 'bp-xprofile-csv-importer'),
                'exportSuccess' => __('Export completed successfully!', 'bp-xprofile-csv-importer'),
                'exportError' => __('Export failed. Please try again.', 'bp-xprofile-csv-importer'),
            )
        ));
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        include BP_XPROFILE_CSV_IMPORTER_PLUGIN_DIR . 'includes/admin-page.php';
    }
    
    /**
     * AJAX handler for CSV import
     */
    public function ajax_import_csv() {
        check_ajax_referer('bp_xprofile_importer_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized access', 'bp-xprofile-csv-importer')));
        }
        
        require_once BP_XPROFILE_CSV_IMPORTER_PLUGIN_DIR . 'includes/class-importer.php';
        
        $importer = new BP_Xprofile_CSV_Importer_Handler();
        $result = $importer->import_from_file($_FILES['file']);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        wp_send_json_success(array(
            'message' => __('Import completed successfully', 'bp-xprofile-csv-importer'),
            'imported' => $result['imported'],
            'skipped' => $result['skipped']
        ));
    }
    
    /**
     * AJAX handler for CSV export
     */
    public function ajax_export_csv() {
        check_ajax_referer('bp_xprofile_importer_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized access', 'bp-xprofile-csv-importer')));
        }
        
        require_once BP_XPROFILE_CSV_IMPORTER_PLUGIN_DIR . 'includes/class-exporter.php';
        
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        $format = isset($_POST['format']) ? sanitize_text_field($_POST['format']) : 'csv';
        
        $exporter = new BP_Xprofile_CSV_Exporter_Handler();
        $result = $exporter->export_to_file($category_id, $format);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        wp_send_json_success(array(
            'file_url' => $result['file_url'],
            'file_path' => $result['file_path']
        ));
    }
    
    /**
     * AJAX handler to get categories
     */
    public function ajax_get_categories() {
        check_ajax_referer('bp_xprofile_importer_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized access', 'bp-xprofile-csv-importer')));
        }
        
        $categories = array();
        
        if (function_exists('bp_xprofile_get_groups')) {
            $groups = bp_xprofile_get_groups(array('fetch_fields' => false));
            
            foreach ($groups as $group) {
                $categories[] = array(
                    'id' => $group->id,
                    'name' => $group->name,
                    'description' => $group->description
                );
            }
        }
        
        wp_send_json_success(array('categories' => $categories));
    }
}

/**
 * Initialize the plugin
 */
function bp_xprofile_csv_importer_init() {
    return BP_Xprofile_CSV_Importer::get_instance();
}

// Start the plugin
add_action('plugins_loaded', 'bp_xprofile_csv_importer_init');
