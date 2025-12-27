# BuddyPress XProfile Importer

**Version:** 0.1.4
**Author:** Gary Foster - DAFO Creative LLC/Ltd  
**License:** [AGPL-3.0-or-later](https://www.gnu.org/licenses/agpl-3.0.en.html)  
**Requires at least:** WordPress 6.0  
**Tested up to:** WordPress 6.7+  
**Requires PHP:** 7.4+  
**Compatible with:** BuddyPress, BuddyBoss Platform  
**Tags:** buddypress, buddyboss, importer, xprofile, conditional fields, spreadsheets  

---

## Overview

**BuddyPress XProfile Importer** is a WordPress plugin that allows administrators to **import and export BuddyPress Extended Profile (XProfile) fields** using spreadsheet files such as `.xls`, `.xlsx`, `.ods`, or `.csv`.

**🔍 How to Access:** After activation, go to **WordPress Admin → Users → XProfile Import/Export**

It fully supports:
- **BuddyPress** and **BuddyBoss Platform**
- **XProfile Custom Field Types** plugin (for extra field types)
- **Conditional Profile Fields for BuddyPress** (for show/hide logic)
- Multiple **field groups (categories)**
- Bulk **import/export** of profile questions and structure

---

## Quick Start Guide

### 1. **Activate the Plugin**
- Upload and activate the plugin through WordPress admin
- The plugin will automatically detect BuddyPress or BuddyBoss

### 2. **Find the Importer**
- Go to your **WordPress Admin Dashboard**
- Navigate to **Users** in the left sidebar
- Click on **XProfile Import/Export**

### 3. **Can't Find It?**
If you don't see "XProfile Import/Export" under Users, check:
- ✅ BuddyPress or BuddyBoss is active and properly configured
- ✅ Extended Profiles component is enabled in BuddyPress/BuddyBoss
- ✅ You have Administrator privileges
- ✅ Plugin activation was successful (no fatal errors)

### 4. **Start with CSV Files**
- The plugin works immediately with `.csv` files (no additional setup needed)
- Create your spreadsheet in Excel/Google Sheets and save as CSV
- For `.xlsx`/`.xls` support, see the advanced installation section below

---

## Features

✅ **Built-in CSV support** (no additional libraries needed)  
✅ Import fields from `.csv`, `.xls`, `.xlsx`, or `.ods` (requires PhpSpreadsheet)  
✅ Export fields by category/group  
✅ Support for BuddyPress **custom field types**  
✅ Integration with **Conditional Profile Fields for BuddyPress**  
✅ Field group mapping via "Group" column or dropdown  
✅ Lightweight UI for admins  
✅ Optional PhpSpreadsheet library support for complex formats

---

## Prerequisites

Before using this plugin, make sure your WordPress environment meets the following requirements:

| Requirement | Minimum Version | Notes |
|--------------|----------------|-------|
| **WordPress** | 6.0+ | Tested with latest BuddyPress/BuddyBoss versions |
| **PHP** | 7.4+ | Recommended 8.0 or higher |
| **BuddyPress OR BuddyBoss** | 10.0+ / Latest | Must have the **Extended Profiles** component enabled |
| **XProfile Custom Field Types** (optional) | latest | For importing extra field types |
| **Conditional Profile Fields for BuddyPress** (optional) | latest | Enables conditional field logic |
| **Composer** (optional) | — | Needed if you want full `.xls`/`.xlsx`/`.ods` support |

---

## Troubleshooting

### "I can't find the XProfile Import/Export menu"

**Check these items in order:**

1. **Plugin Activation Status**
   - Go to **Plugins → Installed Plugins**
   - Ensure "BuddyPress XProfile Importer" shows as "Active"
   - If not active, click "Activate"

2. **BuddyPress/BuddyBoss Setup**
   - Ensure BuddyPress or BuddyBoss Platform is active
   - Go to **BuddyPress/BuddyBoss → Components** 
   - Verify "Extended Profiles" component is enabled

3. **User Permissions**
   - You must be logged in as an Administrator
   - Only users with "manage_options" capability can see the menu

4. **Check for Errors**
   - If WP_DEBUG is enabled, check for any PHP errors
   - Look in **Tools → Site Health** for any plugin conflicts

5. **Menu Location**
   - The menu appears under **Users → XProfile Import/Export**
   - NOT under BuddyPress or BuddyBoss menus

### "Plugin shows error about BuddyPress not being available"

This means the plugin cannot detect your BuddyPress/BuddyBoss installation:

1. **Enable Debug Mode** (temporarily):
   ```php
   // Add to wp-config.php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```

2. **Visit the Plugin Page**: Go to Users → XProfile Import/Export
   - If debug is enabled, you'll see diagnostic information
   - This shows what BuddyPress/BuddyBoss components are detected

3. **Common Solutions**:
   - Deactivate and reactivate BuddyPress/BuddyBoss
   - Clear any caching plugins
   - Check that database tables exist (wp_bp_xprofile_groups)

---

## Installation

### 🎯 Quick Decision Guide:
- **Just want to import/export data?** → Use **Option 1** (CSV files work great!)
- **Need Excel (.xlsx) support?** → Try **Option 2** (requires technical setup)
- **On shared hosting?** → Stick with **Option 1** (easiest)
- **Have VPS/dedicated server?** → **Option 2** gives you more file format options

### Option 1 — Manual install (Recommended for most users)
1. Upload the plugin folder to your `/wp-content/plugins/` directory.  
2. Activate it via **Dashboard → Plugins → Installed Plugins → Activate**.  
3. **Important**: Make sure BuddyPress or BuddyBoss is active and Extended Profiles component is enabled.
4. Go to **Users → XProfile Import/Export** to start using it.

### Option 2 — Using Composer (recommended for advanced file support)
If you want `.xls`, `.xlsx`, and `.ods` file support (beyond CSV):

**📦 Good News:** A `composer.json` file is already included in the plugin!

#### Do I need to install Composer?

**Shared Hosting (most common):**
- ❌ **You probably DON'T have Composer access**
- ✅ **Skip to "Alternative Installation" below**
- ✅ **Or just use CSV files** (works perfectly without any setup)

**VPS/Dedicated Server/Local Development:**
- ✅ **You can install Composer system-wide**
- ✅ **Follow the steps below**

#### Step 1: Check if Composer is Available
```bash
# Test if Composer is already installed
composer --version

# If you see a version number, you're good to go!
# If you get "command not found", see installation options below
```

#### Step 2: Install Composer (if needed)

**On Ubuntu/Debian:**
```bash
sudo apt-get update
sudo apt-get install composer
```

**On CentOS/RHEL:**
```bash
sudo yum install composer
# or on newer versions:
sudo dnf install composer
```

**Manual Installation (any Linux) - recommended:**
```bash
# Download and install Composer globally
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
```

#### Step 3: Install Required PHP Extensions
First, ensure you have the required PHP extensions. If you are using shared hosting, you should be able to turn php-zip, php-xml and php-mbstring on in cPanel's "Select PHP version". If not on most systems:

**Ubuntu/Debian:**
```bash
sudo apt-get install php-zip php-xml php-mbstring
```

**CentOS/RHEL:**
```bash
sudo yum install php-zip php-xml php-mbstring
# or on newer versions:
sudo dnf install php-zip php-xml php-mbstring
```

**cPanel/Shared Hosting:**
- Go to your hosting control panel
- Look for "PHP Extensions" or "PHP Configuration"
- Enable: `zip`, `xml`, `mbstring`

#### Step 4: Install PhpSpreadsheet
```bash
cd /path/to/wp-content/plugins/BuddyPressXprofileCustomFieldTypesCSVImporter

# Install PhpSpreadsheet (composer.json is already included)
composer install --no-dev

# Or if you want to add it to requirements:
composer require phpoffice/phpspreadsheet
```

#### Alternative Installation (No Composer Required)
If you're on shared hosting or can't install Composer:

**Option A: Use CSV Only (Recommended)**
- The plugin works perfectly with `.csv` files
- No additional setup needed
- Create spreadsheets in Excel/Google Sheets, save as CSV

**Option B: Manual PhpSpreadsheet Installation**
1. Download PhpSpreadsheet: https://github.com/PHPOffice/PhpSpreadsheet/releases
2. Extract to: `wp-content/plugins/BuddyPressXprofileCustomFieldTypesCSVImporter/vendor/`
3. The structure should be: `vendor/phpoffice/phpspreadsheet/`

**Option C: Pre-built Plugin Version**
- Some hosting providers offer pre-built WordPress plugins with dependencies included
- Check if your host has a "plugin marketplace" with this plugin

#### Step 3: Alternative Installation (if Composer fails)
If you can't install via Composer, you can:

1. **Use CSV files only** - The plugin works perfectly with `.csv` files without any additional libraries
2. **Download PhpSpreadsheet manually**:
   - Download from: https://github.com/PHPOffice/PhpSpreadsheet/releases
   - Extract to `vendor/phpoffice/phpspreadsheet/` in the plugin directory

#### Troubleshooting Composer Issues

**"ext-zip is missing":**
```bash
# Check if zip extension is installed
php -m | grep zip

# If not listed, install it (see Step 1 above)
```

**"No composer.json present":**
```bash
# This shouldn't happen anymore since composer.json is included
# But if it does, make sure you're in the right directory
cd /path/to/wp-content/plugins/BuddyPressXprofileCustomFieldTypesCSVImporter

# The composer.json file should already exist
ls -la composer.json

# Install dependencies
composer install --no-dev
```

**Alternative: Skip PhpSpreadsheet entirely**
The plugin works great with CSV files only. You can:
- Use Google Sheets or Excel to save your data as `.csv`
- Upload CSV files directly - no additional setup needed!

**Note**: After installing via Composer, you may need to deactivate and reactivate the plugin.

---

## Spreadsheet Format

Your spreadsheet must include a header row.

The importer expects the following columns (case-insensitive):

| Column Name     | Description                                                   | Example                    |
| --------------- | ------------------------------------------------------------- | -------------------------- |
| `Field Name`    | Name of the profile field                                     | Favorite Color             |
| `Type`          | BuddyPress field type (`textbox`, `selectbox`, `radio`, etc.) | selectbox                  |
| `Description`   | Optional description for the field                            | Choose your favorite color |
| `Is_Required`   | Whether field is required (1, 0, yes, no)                     | 1                          |
| `Options`       | Comma-separated list (for selectbox, radio, etc.)             | Red,Blue,Green             |
| `Order`         | Display order (integer)                                       | 1                          |
| `Group`         | XProfile group/category name or ID                            | Preferences                |
| `Parent_Field`  | Parent field name or ID (for conditional logic)               | Gender                     |
| `Show_If_Value` | Value that triggers display of the field                      | Female                     |

---

## Example.csv

```bash
Field Name,Type,Description,Is_Required,Options,Order,Group,Parent_Field,Show_If_Value
Favorite Color,selectbox,Choose color,0,Red,Blue,Green,1,Preferences,Gender,Female
Hobby,textbox,Your favorite hobby,1,,2,About Me,,
```
---

## Usage

### Step 1: Access the Importer
1. **Log into WordPress Admin** as an Administrator
2. **Navigate to Users** in the left sidebar
3. **Click "XProfile Import/Export"**

*If you don't see this menu item, see the Troubleshooting section above.*

### Step 2: Importing Fields

1. **Prepare your spreadsheet** with the required columns (see format below)
2. **Upload the file** (.csv, .xls, .xlsx, or .ods)
3. **(Optional)** Select a default field group if your sheet doesn't specify one
4. **Click "Import Fields"**
5. **Review the results** - a summary will show created/skipped/error counts

### Step 3: Exporting Fields

1. **Choose the field group** you want to export from the dropdown
2. **Click "Export Selected Group"**
3. **Download starts automatically** - the plugin generates an .xlsx or .csv file

---

## Conditional Fields Support

If you have Conditional Profile Fields for BuddyPress installed, the importer will automatically create conditional links between fields if these columns are provided:

<ul>
<li>Parent_Field</li>
<li>Show_If_Value</li>
</ul>

Example:
> If you have “Favorite Color” with Parent_Field = Gender and Show_If_Value = Female, it will only appear when the user selects “Female” in “Gender”.

---

## File Structure

```bash
BuddyPressXprofileCustomFieldTypesCSVImporter/
│
├── bp-xprofile-importer.php        # Main plugin bootstrap
├── admin/
│   ├── class-bpxp-admin-page.php   # Admin UI & form handling
│   └── views/
│       └── import-page.php         # Admin HTML template
├── includes/
│   ├── class-bpxp-importer.php     # Import logic
│   ├── class-bpxp-exporter.php     # Export logic
│   ├── class-bpxp-conditional.php  # Conditional logic integration
│   └── helper.php                  # Shared helpers & BuddyBoss compatibility
├── assets/
│   └── js/
│       ├── admin.css               # Minimal admin styles
│       └── admin.js                # JS confirm logic
└── vendor/ (optional)              # PhpSpreadsheet (if installed via Composer)
```

---

## Security

* Only administrators (manage_options capability) can use the import/export interface.
* Nonces and WordPress file upload API are used for verification.
* Input data is sanitized before field creation.
* It’s recommended to test imports on a staging site first before applying to production.

---

## Extending the Plugin

You can extend this starter by:
* Adding a preview/mapping screen before importing.
* Allowing field updates (not just creation).
* Handling field options arrays more deeply (multi-level).
* Integrating with WP-CLI for command-line imports.
* Adding batch processing for very large spreadsheets.

---

## Contributions are welcome!
If you'd like to improve the plugin, please:

<ol>
  <li>Fork the repository.</li>
  <li>Create a feature branch (git checkout -b feature/your-feature).</li>
  <li>Commit your changes and push your branch.</li>
  <li>Open a Pull Request describing your improvement.</li>
</ol>

This project is open-source and released under the GNU Affero General Public License v3.0 or later (AGPL-3.0+). By contributing, you agree that your code will be distributed under the same license.

---

## License

BuddyPress XProfile Importer is released under the [GNU Affero General Public License v3.0 or later (AGPL-3.0+)](https://www.gnu.org/licenses/agpl-3.0.en.html)

> You are free to use, study, modify, and distribute this software - but if you deploy or distribute modified versions publicly,
you must make the modified source code available under the same AGPL license.
