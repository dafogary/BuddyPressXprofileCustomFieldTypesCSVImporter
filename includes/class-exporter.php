<?php
/**
 * CSV Exporter Handler Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class BP_Xprofile_CSV_Exporter_Handler {
    
    /**
     * Export fields to file
     */
    public function export_to_file($category_id = 0, $format = 'csv') {
        // Get fields to export
        $fields = $this->get_fields_to_export($category_id);
        
        if (empty($fields)) {
            return new WP_Error('no_fields', __('No fields found to export', 'bp-xprofile-csv-importer'));
        }
        
        // Prepare data for export
        $data = $this->prepare_export_data($fields);
        
        // Generate file based on format
        return $this->generate_export_file($data, $format, $category_id);
    }
    
    /**
     * Get fields to export
     */
    private function get_fields_to_export($category_id) {
        $fields = array();
        
        if (!function_exists('bp_xprofile_get_groups')) {
            return $fields;
        }
        
        // Get field groups
        $args = array(
            'fetch_fields' => true
        );
        
        if ($category_id > 0) {
            $args['profile_group_id'] = $category_id;
        }
        
        $groups = bp_xprofile_get_groups($args);
        
        foreach ($groups as $group) {
            if (!empty($group->fields)) {
                foreach ($group->fields as $field) {
                    $fields[] = array(
                        'field' => $field,
                        'group' => $group
                    );
                }
            }
        }
        
        return $fields;
    }
    
    /**
     * Prepare data for export
     */
    private function prepare_export_data($fields) {
        $data = array();
        $include_conditional = isset($_POST['include_conditional']) && $_POST['include_conditional'] === '1';
        
        foreach ($fields as $item) {
            $field = $item['field'];
            $group = $item['group'];
            
            $row = array(
                'field_name' => $field->name,
                'field_type' => $field->type,
                'description' => $field->description,
                'required' => $field->is_required ? 'yes' : 'no',
                'group_name' => $group->name,
                'options' => $this->get_field_options($field),
            );
            
            // Add conditional field data if enabled
            if ($include_conditional) {
                $conditional_data = $this->get_conditional_data($field->id);
                $row['conditional_parent'] = $conditional_data['parent'];
                $row['conditional_value'] = $conditional_data['value'];
            } else {
                $row['conditional_parent'] = '';
                $row['conditional_value'] = '';
            }
            
            $data[] = $row;
        }
        
        return $data;
    }
    
    /**
     * Get field options as comma-separated string
     */
    private function get_field_options($field) {
        if (empty($field->type_obj->accepted_values)) {
            return '';
        }
        
        $options = array();
        
        if (isset($field->data) && is_object($field->data) && isset($field->data->children)) {
            foreach ($field->data->children as $child) {
                $options[] = $child->name;
            }
        }
        
        return implode(',', $options);
    }
    
    /**
     * Get conditional field data
     */
    private function get_conditional_data($field_id) {
        $data = array(
            'parent' => '',
            'value' => ''
        );
        
        // Check if conditional fields are enabled
        $conditional_enabled = bp_xprofile_get_meta($field_id, 'field', 'conditional_field_enabled', true);
        
        if ($conditional_enabled !== '1') {
            return $data;
        }
        
        // Get parent field ID
        $parent_id = bp_xprofile_get_meta($field_id, 'field', 'conditional_field_parent', true);
        
        if ($parent_id) {
            $parent_field = xprofile_get_field($parent_id);
            if ($parent_field) {
                $data['parent'] = $parent_field->name;
            }
        }
        
        // Get conditional value
        $data['value'] = bp_xprofile_get_meta($field_id, 'field', 'conditional_field_value', true);
        
        return $data;
    }
    
    /**
     * Generate export file
     */
    private function generate_export_file($data, $format, $category_id) {
        // Create uploads directory if it doesn't exist
        $upload_dir = wp_upload_dir();
        $export_dir = $upload_dir['basedir'] . '/bp-xprofile-exports';
        
        if (!file_exists($export_dir)) {
            wp_mkdir_p($export_dir);
        }
        
        // Generate filename
        $timestamp = current_time('Y-m-d_H-i-s');
        $category_slug = $category_id > 0 ? 'category-' . $category_id : 'all-fields';
        $filename = 'bp-xprofile-export_' . $category_slug . '_' . $timestamp . '.' . $format;
        $file_path = $export_dir . '/' . $filename;
        
        // Generate file based on format
        if ($format === 'csv') {
            $result = $this->generate_csv($data, $file_path);
        } else {
            $result = $this->generate_spreadsheet($data, $file_path, $format);
        }
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        // Return file URL and path
        $file_url = $upload_dir['baseurl'] . '/bp-xprofile-exports/' . $filename;
        
        return array(
            'file_url' => $file_url,
            'file_path' => $file_path,
            'filename' => $filename
        );
    }
    
    /**
     * Generate CSV file
     */
    private function generate_csv($data, $file_path) {
        if (empty($data)) {
            return new WP_Error('no_data', __('No data to export', 'bp-xprofile-csv-importer'));
        }
        
        $handle = fopen($file_path, 'w');
        
        if ($handle === false) {
            return new WP_Error('file_error', __('Could not create export file', 'bp-xprofile-csv-importer'));
        }
        
        // Write header
        fputcsv($handle, array_keys($data[0]));
        
        // Write data rows
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        
        return true;
    }
    
    /**
     * Generate spreadsheet file (XLS, XLSX, ODS)
     */
    private function generate_spreadsheet($data, $file_path, $format) {
        // Check if PHPSpreadsheet is available
        if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            $autoload_path = BP_XPROFILE_CSV_IMPORTER_PLUGIN_DIR . 'vendor/autoload.php';
            if (file_exists($autoload_path)) {
                require_once $autoload_path;
            } else {
                return new WP_Error('missing_library', __('PHPSpreadsheet library is required to export to XLS/XLSX/ODS. Please install it via Composer or use CSV format.', 'bp-xprofile-csv-importer'));
            }
        }
        
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set header row
            $header = array_keys($data[0]);
            $col = 'A';
            foreach ($header as $heading) {
                $sheet->setCellValue($col . '1', $heading);
                $sheet->getStyle($col . '1')->getFont()->setBold(true);
                $col++;
            }
            
            // Add data rows
            $row_num = 2;
            foreach ($data as $row) {
                $col = 'A';
                foreach ($row as $value) {
                    $sheet->setCellValue($col . $row_num, $value);
                    $col++;
                }
                $row_num++;
            }
            
            // Auto-size columns
            foreach (range('A', $col) as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }
            
            // Determine writer type
            $writer_map = array(
                'xls' => 'Xls',
                'xlsx' => 'Xlsx',
                'ods' => 'Ods'
            );
            
            $writer_type = isset($writer_map[$format]) ? $writer_map[$format] : 'Xlsx';
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, $writer_type);
            $writer->save($file_path);
            
            return true;
            
        } catch (Exception $e) {
            return new WP_Error('export_error', sprintf(
                __('Error creating spreadsheet: %s', 'bp-xprofile-csv-importer'),
                $e->getMessage()
            ));
        }
    }
}
