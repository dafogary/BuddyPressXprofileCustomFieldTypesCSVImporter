# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-10-28

### Added
- Initial release of BuddyPress Xprofile Custom Field Types CSV Importer
- Import functionality for CSV, XLS, XLSX, and ODS files
- Export functionality to CSV, XLS, XLSX, and ODS formats
- Support for all BuddyPress Xprofile field types
- Support for BuddyPress Xprofile Custom Field Types
- Category/field group selection for import and export
- Conditional field support (compatible with Conditional Profile Fields for BuddyPress)
- User-friendly admin interface with AJAX operations
- Real-time category dropdown population
- Comprehensive error handling and validation
- Sample CSV file for reference
- Full documentation (README, INSTALL, SCREENSHOTS)
- Internationalization support (translation-ready)
- Security features (nonce verification, capability checks, input sanitization)

### Features
- **Import Profile Fields**
  - Upload files via file input
  - Select target category for imported fields
  - Optional conditional field import
  - Detailed import results (imported/skipped counts)
  - Error reporting for failed imports

- **Export Profile Fields**
  - Export all fields or specific category
  - Multiple export formats (CSV, XLS, XLSX, ODS)
  - Optional conditional field data in exports
  - Direct download links
  - Timestamped file naming

- **Field Support**
  - All standard BuddyPress field types
  - Custom field types from BuddyPress Xprofile Custom Field Types
  - Field options for select/radio/checkbox types
  - Required/optional field settings
  - Field descriptions

- **Conditional Fields**
  - Parent-child field relationships
  - Conditional visibility based on parent values
  - Import and export of conditional settings
  - Compatible with Conditional Profile Fields plugin

### Technical
- PHP 7.2+ compatibility
- WordPress 5.0+ compatibility
- Composer dependency management
- PHPSpreadsheet library integration
- AJAX-powered admin interface
- Secure file handling
- Proper WordPress coding standards
- Clean, maintainable code structure

### Documentation
- Comprehensive README with usage examples
- Installation guide with troubleshooting
- UI/UX documentation
- Sample import file
- Inline help in admin interface

## [Unreleased]

### Planned Features
- Bulk field operations (delete, update)
- Field preview before import
- Import validation with detailed warnings
- Support for field ordering
- Import/export of field visibility settings
- Import/export of member types restrictions
- Support for custom field meta data
- Scheduled exports
- Import history tracking
- Undo/rollback functionality
