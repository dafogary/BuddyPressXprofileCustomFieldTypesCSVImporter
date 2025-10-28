# Installation Guide

## Quick Start

### 1. Install the Plugin

**Option A: Manual Installation**
1. Download or clone this repository
2. Upload the folder to `/wp-content/plugins/`
3. Navigate to the plugin directory in terminal

**Option B: Clone with Git**
```bash
cd /path/to/wordpress/wp-content/plugins/
git clone https://github.com/dafogary/BuddyPressXprofileCustomFieldTypesCSVImporter.git bp-xprofile-csv-importer
cd bp-xprofile-csv-importer
```

### 2. Install Dependencies

For XLS, XLSX, and ODS support (optional but recommended):

```bash
composer install
```

If you don't have Composer installed, download it from https://getcomposer.org/

**Note:** If you only need CSV support, you can skip this step.

### 3. Activate Required Plugins

Before activating this plugin, make sure the following plugins are installed and activated:

1. **BuddyPress** - The base social networking plugin
2. **BuddyPress Xprofile Custom Field Types** - Adds additional field types to BuddyPress profiles

**Optional:**
3. **Conditional Profile Fields for BuddyPress** - For conditional field support

### 4. Activate the Plugin

1. Go to WordPress Admin → Plugins
2. Find "BuddyPress Xprofile Custom Field Types CSV Importer"
3. Click "Activate"

### 5. Access the Plugin

After activation:
1. Go to WordPress Admin menu
2. Look for "BP Xprofile Importer" menu item
3. Click to access the import/export interface

## Troubleshooting

### "Required plugins not found" Error

If you see an error about missing plugins:
1. Make sure BuddyPress is installed and activated
2. Make sure BuddyPress Xprofile Custom Field Types is installed and activated
3. Refresh the WordPress admin page

### XLS/XLSX/ODS Files Not Working

If spreadsheet imports fail:
1. Make sure you've run `composer install`
2. Check that the `vendor` directory exists in the plugin folder
3. Verify PHPSpreadsheet is installed: `composer show phpoffice/phpspreadsheet`

### File Upload Errors

If file uploads fail:
1. Check your PHP upload limits in `php.ini`:
   - `upload_max_filesize`
   - `post_max_size`
2. Check WordPress media settings
3. Ensure the uploads directory is writable

### Permissions Issues

If you get permission errors:
1. Make sure the WordPress uploads directory is writable:
   ```bash
   chmod 755 wp-content/uploads
   ```
2. Check that PHP can create directories

## System Requirements

- **WordPress:** 5.0 or higher
- **PHP:** 7.2 or higher
- **MySQL:** 5.6 or higher (or MariaDB 10.0+)
- **Required Plugins:**
  - BuddyPress
  - BuddyPress Xprofile Custom Field Types
- **Optional Plugins:**
  - Conditional Profile Fields for BuddyPress

## Next Steps

After installation:
1. Review the [README.md](README.md) for usage instructions
2. Check the example CSV file in `examples/sample-import.csv`
3. Try a test import with the sample file
4. Export your existing fields to understand the format

## Getting Help

If you encounter any issues:
1. Check the troubleshooting section above
2. Review the plugin documentation in README.md
3. Check the WordPress debug log for error messages
4. Visit the GitHub repository for issues and support
