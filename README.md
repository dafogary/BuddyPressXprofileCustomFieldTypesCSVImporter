# BuddyPress XProfile Importer

**Version:** 0.1.0  
**Author:** Your Name  
**License:** [AGPL-3.0-or-later](https://www.gnu.org/licenses/agpl-3.0.en.html)  
**Requires at least:** WordPress 6.0  
**Tested up to:** WordPress 6.7+  
**Requires PHP:** 7.4+  
**Tags:** buddypress, buddyboss, importer, xprofile, conditional fields, spreadsheets  

---

## Overview

**BuddyPress XProfile Importer** is a WordPress plugin that allows administrators to **import and export BuddyPress Extended Profile (XProfile) fields** using spreadsheet files such as `.xls`, `.xlsx`, `.ods`, or `.csv`.

It fully supports:
- **XProfile Custom Field Types** plugin (for extra field types)
- **Conditional Profile Fields for BuddyPress** (for show/hide logic)
- Multiple **field groups (categories)**
- Bulk **import/export** of profile questions and structure

The plugin adds an admin interface under  
**Dashboard → Users → XProfile Import/Export**  
where you can upload spreadsheets or download existing field data.

---

## Features

✅ Import fields from `.csv`, `.xls`, `.xlsx`, or `.ods`  
✅ Export fields by category/group  
✅ Support for BuddyPress **custom field types**  
✅ Integration with **Conditional Profile Fields for BuddyPress**  
✅ Field group mapping via “Group” column or dropdown  
✅ Lightweight UI for admins  
✅ PhpSpreadsheet library support for complex formats  

---

## Prerequisites

Before using this plugin, make sure your WordPress environment meets the following requirements:

| Requirement | Minimum Version | Notes |
|--------------|----------------|-------|
| **WordPress** | 6.0+ | Tested with latest BuddyPress versions |
| **PHP** | 7.4+ | Recommended 8.0 or higher |
| **BuddyPress** | 10.0+ | Must have the **Extended Profiles** component enabled |
| **XProfile Custom Field Types** (optional) | latest | For importing extra field types |
| **Conditional Profile Fields for BuddyPress** (optional) | latest | Enables conditional field logic |
| **Composer** (optional) | — | Needed if you want full `.xls`/`.xlsx`/`.ods` support |

---

## Installation

### Option 1 — Manual install
1. Upload the plugin folder `bp-xprofile-importer` to your `/wp-content/plugins/` directory.  
2. Activate it via **Dashboard → Plugins → Installed Plugins → Activate**.  
3. Go to **Users → XProfile Import/Export** to start using it.

### Option 2 — Using Composer (recommended)
If you want `.xls`, `.xlsx`, and `.ods` file support (beyond CSV):

```bash
cd wp-content/plugins/bp-xprofile-importer
composer require phpoffice/phpspreadsheet
```
Without PhpSpreadsheet, the plugin still works with .csv files.

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
### Importing Fields

<ol>
  <li>Go to Users → XProfile Import/Export.</li>
  <li>Upload a spreadsheet (.csv, .xls, .xlsx, or .ods).</li>
  <li>(Optional) Select a default field group if your sheet doesn’t specify one.</li>
  <li>Click Import Fields.</li>
  <li>Click Import Fields.</li>
  <li>A summary of results (created/skipped/errors) will display after import.</li>
</ol>

### Exporting Fields

<ol>
<li>Go to Users → XProfile Import/Export.</li>
<li>Choose the field group you want to export.</li>
<li>Click Export Selected Group.</li>
<li>The plugin will generate and download an .xlsx or .csv file containing the fields.</li>
</ol>

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
bp-xprofile-importer/
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
│   ├── helpers.php                 # Shared helpers
│   └── vendor/                     # PhpSpreadsheet (if installed)
└── assets/
    ├── css/admin.css               # Minimal admin styles
    └── js/admin.js                 # JS confirm logic
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
