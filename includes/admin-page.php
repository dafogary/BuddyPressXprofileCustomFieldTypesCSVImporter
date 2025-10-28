<?php
/**
 * Admin page template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="bp-xprofile-importer-container">
        
        <!-- Import Section -->
        <div class="card">
            <h2><?php _e('Import Profile Fields', 'bp-xprofile-csv-importer'); ?></h2>
            <p><?php _e('Import BuddyPress Xprofile custom fields from CSV, XLS, or ODS files.', 'bp-xprofile-csv-importer'); ?></p>
            
            <form id="bp-xprofile-import-form" enctype="multipart/form-data">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="import-file"><?php _e('Select File', 'bp-xprofile-csv-importer'); ?></label>
                        </th>
                        <td>
                            <input type="file" id="import-file" name="file" accept=".csv,.xls,.xlsx,.ods" required>
                            <p class="description">
                                <?php _e('Accepted formats: CSV, XLS, XLSX, ODS', 'bp-xprofile-csv-importer'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="import-category"><?php _e('Target Category', 'bp-xprofile-csv-importer'); ?></label>
                        </th>
                        <td>
                            <select id="import-category" name="category_id">
                                <option value=""><?php _e('Select a category...', 'bp-xprofile-csv-importer'); ?></option>
                            </select>
                            <p class="description">
                                <?php _e('Select the field group/category to import fields into', 'bp-xprofile-csv-importer'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="import-conditional"><?php _e('Enable Conditional Fields', 'bp-xprofile-csv-importer'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="import-conditional" name="enable_conditional" value="1">
                            <p class="description">
                                <?php _e('Import conditional field settings if available in the file', 'bp-xprofile-csv-importer'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php _e('Import Fields', 'bp-xprofile-csv-importer'); ?>
                    </button>
                    <span class="spinner"></span>
                </p>
            </form>
            
            <div id="import-results" class="notice" style="display:none;"></div>
        </div>
        
        <!-- Export Section -->
        <div class="card">
            <h2><?php _e('Export Profile Fields', 'bp-xprofile-csv-importer'); ?></h2>
            <p><?php _e('Export BuddyPress Xprofile custom fields to CSV, XLS, or ODS files.', 'bp-xprofile-csv-importer'); ?></p>
            
            <form id="bp-xprofile-export-form">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="export-category"><?php _e('Category', 'bp-xprofile-csv-importer'); ?></label>
                        </th>
                        <td>
                            <select id="export-category" name="category_id">
                                <option value="0"><?php _e('All Categories', 'bp-xprofile-csv-importer'); ?></option>
                            </select>
                            <p class="description">
                                <?php _e('Select a specific category or export all fields', 'bp-xprofile-csv-importer'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="export-format"><?php _e('Export Format', 'bp-xprofile-csv-importer'); ?></label>
                        </th>
                        <td>
                            <select id="export-format" name="format">
                                <option value="csv">CSV</option>
                                <option value="xls">XLS (Excel 97-2003)</option>
                                <option value="xlsx">XLSX (Excel 2007+)</option>
                                <option value="ods">ODS (OpenDocument)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="export-conditional"><?php _e('Include Conditional Fields', 'bp-xprofile-csv-importer'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="export-conditional" name="include_conditional" value="1" checked>
                            <p class="description">
                                <?php _e('Include conditional field settings in the export', 'bp-xprofile-csv-importer'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php _e('Export Fields', 'bp-xprofile-csv-importer'); ?>
                    </button>
                    <span class="spinner"></span>
                </p>
            </form>
            
            <div id="export-results" class="notice" style="display:none;"></div>
        </div>
        
        <!-- Help Section -->
        <div class="card">
            <h2><?php _e('CSV File Format', 'bp-xprofile-csv-importer'); ?></h2>
            <p><?php _e('Your import file should contain the following columns:', 'bp-xprofile-csv-importer'); ?></p>
            <ul>
                <li><strong>field_name:</strong> <?php _e('Name of the profile field', 'bp-xprofile-csv-importer'); ?></li>
                <li><strong>field_type:</strong> <?php _e('Type of field (textbox, textarea, selectbox, multiselectbox, radio, checkbox, datebox, etc.)', 'bp-xprofile-csv-importer'); ?></li>
                <li><strong>description:</strong> <?php _e('Field description (optional)', 'bp-xprofile-csv-importer'); ?></li>
                <li><strong>required:</strong> <?php _e('Whether the field is required (yes/no)', 'bp-xprofile-csv-importer'); ?></li>
                <li><strong>options:</strong> <?php _e('Comma-separated list of options for select/radio/checkbox fields', 'bp-xprofile-csv-importer'); ?></li>
                <li><strong>conditional_parent:</strong> <?php _e('Parent field for conditional visibility (optional)', 'bp-xprofile-csv-importer'); ?></li>
                <li><strong>conditional_value:</strong> <?php _e('Parent field value that triggers visibility (optional)', 'bp-xprofile-csv-importer'); ?></li>
            </ul>
            
            <h3><?php _e('Example CSV Format', 'bp-xprofile-csv-importer'); ?></h3>
            <pre>field_name,field_type,description,required,options,conditional_parent,conditional_value
"Full Name",textbox,"Enter your full name",yes,"","",""
"Country",selectbox,"Select your country",yes,"USA,Canada,UK,Other","",""
"State",selectbox,"Select your state",no,"CA,NY,TX,FL","Country","USA"</pre>
        </div>
        
    </div>
</div>
