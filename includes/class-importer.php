<?php
/**
 * CSV Importer Handler Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class BP_Xprofile_CSV_Importer_Handler {
    
    /**
     * Import fields from uploaded file
     */
    public function import_from_file($file) {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return new WP_Error('no_file', __('No file uploaded', 'bp-xprofile-csv-importer'));
        }
        
        // Check file type
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, array('csv', 'xls', 'xlsx', 'ods'))) {
            return new WP_Error('invalid_file', __('Invalid file format. Please upload CSV, XLS, XLSX, or ODS file.', 'bp-xprofile-csv-importer'));
        }
        
        // Parse the file based on extension
        $data = $this->parse_file($file['tmp_name'], $file_ext);
        
        if (is_wp_error($data)) {
            return $data;
        }
        
        // Import the fields
        return $this->import_fields($data);
    }
    
    /**
     * Parse file based on format
     */
    private function parse_file($file_path, $extension) {
        if ($extension === 'csv') {
            return $this->parse_csv($file_path);
        } else {
            return $this->parse_spreadsheet($file_path, $extension);
        }
    }
    
    /**
     * Parse CSV file
     */
    private function parse_csv($file_path) {
        $data = array();
        $header = null;
        
        if (($handle = fopen($file_path, 'r')) !== false) {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                if (!$header) {
                    $header = array_map('trim', $row);
                } else {
                    if (count($row) === count($header)) {
                        $data[] = array_combine($header, array_map('trim', $row));
                    }
                }
            }
            fclose($handle);
        }
        
        if (empty($data)) {
            return new WP_Error('empty_file', __('The file is empty or could not be parsed', 'bp-xprofile-csv-importer'));
        }
        
        return $data;
    }
    
    /**
     * Parse spreadsheet file (XLS, XLSX, ODS)
     */
    private function parse_spreadsheet($file_path, $extension) {
        // Check if PHPSpreadsheet is available
        if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
            // Try to load via composer autoload if available
            $autoload_path = BP_XPROFILE_CSV_IMPORTER_PLUGIN_DIR . 'vendor/autoload.php';
            if (file_exists($autoload_path)) {
                require_once $autoload_path;
            } else {
                return new WP_Error('missing_library', __('PHPSpreadsheet library is required to read XLS/XLSX/ODS files. Please install it via Composer or use CSV format.', 'bp-xprofile-csv-importer'));
            }
        }
        
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            if (empty($rows)) {
                return new WP_Error('empty_file', __('The file is empty', 'bp-xprofile-csv-importer'));
            }
            
            $header = array_map('trim', array_shift($rows));
            $data = array();
            
            foreach ($rows as $row) {
                if (count($row) === count($header)) {
                    $data[] = array_combine($header, array_map(function($val) {
                        return is_null($val) ? '' : trim((string)$val);
                    }, $row));
                }
            }
            
            return $data;
            
        } catch (Exception $e) {
            return new WP_Error('parse_error', sprintf(
                __('Error parsing spreadsheet: %s', 'bp-xprofile-csv-importer'),
                $e->getMessage()
            ));
        }
    }
    
    /**
     * Import fields from parsed data
     */
    private function import_fields($data) {
        $imported = 0;
        $skipped = 0;
        $errors = array();
        
        // Get category from POST if set
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 1;
        $enable_conditional = isset($_POST['enable_conditional']) && $_POST['enable_conditional'] === '1';
        
        foreach ($data as $index => $row) {
            $result = $this->import_single_field($row, $category_id, $enable_conditional);
            
            if (is_wp_error($result)) {
                $skipped++;
                $errors[] = sprintf(
                    __('Row %d: %s', 'bp-xprofile-csv-importer'),
                    $index + 2,
                    $result->get_error_message()
                );
            } else {
                $imported++;
            }
        }
        
        return array(
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors
        );
    }
    
    /**
     * Import a single field
     */
    private function import_single_field($row, $group_id, $enable_conditional = false) {
        // Validate required fields
        if (empty($row['field_name'])) {
            return new WP_Error('missing_name', __('Field name is required', 'bp-xprofile-csv-importer'));
        }
        
        if (empty($row['field_type'])) {
            return new WP_Error('missing_type', __('Field type is required', 'bp-xprofile-csv-importer'));
        }
        
        // Check if BuddyPress Xprofile functions are available
        if (!function_exists('xprofile_insert_field')) {
            return new WP_Error('bp_not_active', __('BuddyPress is not active', 'bp-xprofile-csv-importer'));
        }
        
        // Prepare field data
        $field_data = array(
            'field_group_id' => $group_id,
            'name' => sanitize_text_field($row['field_name']),
            'type' => sanitize_text_field($row['field_type']),
            'description' => isset($row['description']) ? sanitize_textarea_field($row['description']) : '',
            'is_required' => isset($row['required']) && in_array(strtolower($row['required']), array('yes', '1', 'true')),
        );
        
        // Insert the field
        $field_id = xprofile_insert_field($field_data);
        
        if (!$field_id) {
            return new WP_Error('insert_failed', __('Failed to create field', 'bp-xprofile-csv-importer'));
        }
        
        // Add options for select/radio/checkbox fields
        if (isset($row['options']) && !empty($row['options'])) {
            $options = array_map('trim', explode(',', $row['options']));
            $this->add_field_options($field_id, $options);
        }
        
        // Add conditional field settings if enabled
        if ($enable_conditional && isset($row['conditional_parent']) && !empty($row['conditional_parent'])) {
            $this->add_conditional_settings($field_id, $row);
        }
        
        return $field_id;
    }
    
    /**
     * Add options to a field
     */
    private function add_field_options($field_id, $options) {
        if (empty($options)) {
            return;
        }
        
        // Get the field object
        $field = xprofile_get_field($field_id);
        
        if (!$field) {
            return;
        }
        
        // Add each option
        $option_order = 1;
        foreach ($options as $option) {
            if (!empty($option)) {
                $field->add_child(array(
                    'name' => $option,
                    'option_order' => $option_order++
                ));
            }
        }
    }
    
    /**
     * Add conditional field settings
     */
    private function add_conditional_settings($field_id, $row) {
        // This requires the Conditional Profile Fields for BuddyPress plugin
        if (!class_exists('BP_XProfile_Conditional_Fields')) {
            return;
        }
        
        // Find parent field by name
        $parent_field_name = $row['conditional_parent'];
        $parent_field = $this->get_field_by_name($parent_field_name);
        
        if (!$parent_field) {
            return;
        }
        
        // Get conditional value
        $conditional_value = isset($row['conditional_value']) ? $row['conditional_value'] : '';
        
        // Save conditional settings as field meta
        bp_xprofile_update_field_meta($field_id, 'conditional_field_enabled', '1');
        bp_xprofile_update_field_meta($field_id, 'conditional_field_parent', $parent_field->id);
        bp_xprofile_update_field_meta($field_id, 'conditional_field_value', $conditional_value);
    }
    
    /**
     * Get field by name
     */
    private function get_field_by_name($name) {
        global $wpdb;
        $bp = buddypress();
        
        $field_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$bp->profile->table_name_fields} WHERE name = %s LIMIT 1",
            $name
        ));
        
        if ($field_id) {
            return xprofile_get_field($field_id);
        }
        
        return null;
    }
}
