# BuddyPress Xprofile Custom Field Types CSV Importer

A WordPress plugin that allows you to import and export BuddyPress Xprofile Custom Field Types from CSV, XLS, XLSX, and ODS files. This plugin supports multiple field categories and works alongside the Conditional Profile Fields for BuddyPress plugin.

## Features

- **Import Profile Fields**: Import BuddyPress Xprofile custom fields from CSV, XLS, XLSX, or ODS files
- **Export Profile Fields**: Export existing fields to CSV, XLS, XLSX, or ODS formats
- **Category Support**: Organize fields by categories (field groups)
- **Conditional Fields**: Support for Conditional Profile Fields for BuddyPress plugin
- **Flexible Field Types**: Supports all BuddyPress Xprofile field types including custom types
- **User-Friendly Interface**: Easy-to-use admin interface with dropdown selectors

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- BuddyPress plugin (active)
- BuddyPress Xprofile Custom Field Types plugin (active)
- Conditional Profile Fields for BuddyPress plugin (optional, for conditional field support)

## Installation

1. Download the plugin files
2. Upload the plugin folder to `/wp-content/plugins/`
3. Run `composer install` in the plugin directory to install PHPSpreadsheet library (required for XLS/XLSX/ODS support)
4. Activate the plugin through the 'Plugins' menu in WordPress
5. Navigate to 'BP Xprofile Importer' in the WordPress admin menu

### Installing Dependencies

For XLS, XLSX, and ODS file support, you need to install PHPSpreadsheet via Composer:

```bash
cd wp-content/plugins/bp-xprofile-csv-importer
composer install
```

If you only need CSV support, you can skip this step.

## Usage

### Importing Fields

1. Go to **BP Xprofile Importer** in the WordPress admin menu
2. In the "Import Profile Fields" section:
   - Select your CSV, XLS, XLSX, or ODS file
   - Choose the target category (field group)
   - Enable "Conditional Fields" if your file contains conditional field settings
   - Click "Import Fields"

### Exporting Fields

1. Go to **BP Xprofile Importer** in the WordPress admin menu
2. In the "Export Profile Fields" section:
   - Select a specific category or "All Categories"
   - Choose your desired export format (CSV, XLS, XLSX, ODS)
   - Enable "Include Conditional Fields" to export conditional settings
   - Click "Export Fields"
   - Download the generated file

## File Format

Your import file should contain the following columns:

- **field_name**: Name of the profile field (required)
- **field_type**: Type of field - textbox, textarea, selectbox, multiselectbox, radio, checkbox, datebox, etc. (required)
- **description**: Field description (optional)
- **required**: Whether the field is required - yes/no (optional)
- **options**: Comma-separated list of options for select/radio/checkbox fields (optional)
- **conditional_parent**: Parent field name for conditional visibility (optional)
- **conditional_value**: Parent field value that triggers visibility (optional)

### Example CSV Format

```csv
field_name,field_type,description,required,options,conditional_parent,conditional_value
"Full Name",textbox,"Enter your full name",yes,"","",""
"Country",selectbox,"Select your country",yes,"USA,Canada,UK,Other","",""
"State",selectbox,"Select your state",no,"CA,NY,TX,FL","Country","USA"
"Province",selectbox,"Select your province",no,"ON,BC,QC,AB","Country","Canada"
```

## Supported Field Types

The plugin supports all BuddyPress field types, including:

- textbox
- textarea
- selectbox
- multiselectbox
- radio
- checkbox
- datebox
- url
- number
- And any custom field types added by BuddyPress Xprofile Custom Field Types plugin

## Conditional Fields

If you have the Conditional Profile Fields for BuddyPress plugin installed, you can:

- Import conditional field settings from your files
- Export existing conditional field configurations
- Set up parent-child relationships between fields
- Define visibility conditions based on parent field values

## Support

For issues, questions, or contributions, please visit:
https://github.com/dafogary/BuddyPressXprofileCustomFieldTypesCSVImporter

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed by dafogary