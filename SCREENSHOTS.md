# Plugin Screenshots and UI Overview

## Admin Interface

The plugin adds a dedicated menu item "BP Xprofile Importer" to the WordPress admin menu.

### Main Admin Page

The admin page is divided into three main sections:

#### 1. Import Profile Fields Section

This section allows you to import profile fields from files:

**Fields:**
- **Select File**: Upload CSV, XLS, XLSX, or ODS files
- **Target Category**: Dropdown to select which field group to import into
- **Enable Conditional Fields**: Checkbox to enable import of conditional field settings

**Features:**
- Drag-and-drop file upload support
- Real-time validation
- Progress indicator during import
- Detailed success/error messages
- Shows count of imported and skipped fields

#### 2. Export Profile Fields Section

This section allows you to export existing profile fields:

**Fields:**
- **Category**: Dropdown to select specific category or "All Categories"
- **Export Format**: Choose between CSV, XLS, XLSX, or ODS
- **Include Conditional Fields**: Checkbox to include conditional settings in export

**Features:**
- One-click export
- Direct download link after export
- Progress indicator
- Organized file naming with timestamps

#### 3. Help Section

Provides comprehensive documentation including:

- **CSV File Format**: Detailed column descriptions
- **Field Types**: List of supported field types
- **Example Format**: Sample CSV structure
- **Conditional Fields**: How to set up conditional relationships

### User Interface Elements

**Styling:**
- Clean, modern interface matching WordPress admin design
- Responsive layout
- Clear section separation with cards
- Color-coded success/error messages
- Inline help text for all fields

**Interactions:**
- AJAX-powered (no page reloads)
- Loading spinners during operations
- Immediate feedback on actions
- Disabled buttons during processing to prevent duplicate submissions

## Category Dropdown Population

The category dropdowns are automatically populated with:
- All existing BuddyPress profile field groups
- Real-time fetching via AJAX
- Displays group name and ID
- Option to export all categories at once

## File Format Examples

### Import File
Columns displayed in the help section:
```
field_name | field_type | description | required | options | conditional_parent | conditional_value
```

### Export File
Exports include all field data plus:
- Group/category name
- All field metadata
- Conditional relationships (if enabled)

## Error Handling

The plugin provides clear error messages for:
- Missing required plugins
- Invalid file formats
- Parse errors
- Permission issues
- Database errors

All errors are displayed inline with helpful explanations.

## Technical Features

- **Security**: Nonce verification on all AJAX requests
- **Permissions**: Checks for 'manage_options' capability
- **Sanitization**: All input is sanitized before processing
- **Validation**: Comprehensive validation of file data
- **Internationalization**: All strings are translatable

## Access Control

The plugin is only accessible to:
- WordPress administrators (users with 'manage_options' capability)
- Accessed via WordPress admin menu at: `Admin → BP Xprofile Importer`

## File Storage

Exported files are stored in:
```
wp-content/uploads/bp-xprofile-exports/
```

Files are named with:
- Descriptive prefix
- Category identifier
- Timestamp
- Appropriate file extension

Example: `bp-xprofile-export_category-2_2025-10-28_14-30-45.xlsx`
