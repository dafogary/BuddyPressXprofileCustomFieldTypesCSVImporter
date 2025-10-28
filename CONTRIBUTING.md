# Contributing to BuddyPress Xprofile CSV Importer

Thank you for your interest in contributing to this project! This guide will help you get started.

## Development Setup

### Prerequisites

- WordPress 5.0+
- PHP 7.2+
- Composer
- BuddyPress plugin
- BuddyPress Xprofile Custom Field Types plugin

### Local Development Environment

1. **Clone the repository**
   ```bash
   git clone https://github.com/dafogary/BuddyPressXprofileCustomFieldTypesCSVImporter.git
   cd BuddyPressXprofileCustomFieldTypesCSVImporter
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set up WordPress**
   - Install WordPress locally (using Local, XAMPP, or similar)
   - Install and activate BuddyPress
   - Install and activate BuddyPress Xprofile Custom Field Types
   - Copy this plugin to `wp-content/plugins/`
   - Activate the plugin

4. **Enable debugging**
   Add to `wp-config.php`:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ```

## Code Standards

### PHP

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- Use meaningful variable and function names
- Add PHPDoc comments to all functions and classes
- Sanitize all input, escape all output
- Use WordPress functions when available

### JavaScript

- Follow [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/)
- Use jQuery for DOM manipulation (WordPress standard)
- Avoid global variables
- Comment complex logic

### CSS

- Follow [WordPress CSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/)
- Use meaningful class names
- Keep specificity low
- Group related styles together

## File Structure

```
bp-xprofile-csv-importer/
├── assets/
│   ├── css/          # Stylesheets
│   └── js/           # JavaScript files
├── examples/         # Sample files
├── includes/         # PHP class files
├── languages/        # Translation files (future)
└── bp-xprofile-csv-importer.php  # Main plugin file
```

## Making Changes

### Branch Naming

- Feature: `feature/description`
- Bug fix: `fix/description`
- Enhancement: `enhancement/description`

### Commit Messages

Follow conventional commits:
- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation changes
- `style:` Code style changes (formatting)
- `refactor:` Code refactoring
- `test:` Adding tests
- `chore:` Maintenance tasks

Example:
```
feat: add support for XML file import
fix: resolve issue with conditional field export
docs: update installation instructions
```

## Testing

### Manual Testing

1. Test import with various file formats (CSV, XLS, XLSX, ODS)
2. Test export with different categories and formats
3. Verify conditional fields work correctly
4. Test error handling with invalid files
5. Check UI responsiveness

### Test Cases to Cover

- [ ] Import valid CSV file
- [ ] Import valid XLS/XLSX/ODS file
- [ ] Import with conditional fields
- [ ] Import to specific category
- [ ] Export all fields as CSV
- [ ] Export specific category as XLS/XLSX/ODS
- [ ] Export with conditional fields
- [ ] Handle invalid file format
- [ ] Handle malformed CSV
- [ ] Handle permission errors
- [ ] Verify category dropdown population
- [ ] Test AJAX error handling

## Pull Request Process

1. **Create a fork** of the repository
2. **Create a feature branch** from `main`
3. **Make your changes** following the code standards
4. **Test thoroughly** using the test cases above
5. **Update documentation** if needed
6. **Commit your changes** with clear commit messages
7. **Push to your fork**
8. **Create a Pull Request** with:
   - Clear title describing the change
   - Description of what was changed and why
   - Screenshots if UI changes were made
   - Any testing performed

## Feature Requests & Bug Reports

### Bug Reports

Include:
- WordPress version
- PHP version
- BuddyPress version
- Plugin version
- Steps to reproduce
- Expected behavior
- Actual behavior
- Error messages (from debug.log)
- Screenshots if applicable

### Feature Requests

Include:
- Clear description of the feature
- Use case / why it's needed
- Proposed implementation (optional)
- Any related issues or discussions

## Code Review Process

All submissions require review before merging:
- Code follows WordPress standards
- Functionality works as described
- No breaking changes (or properly documented)
- Documentation updated if needed
- No security vulnerabilities introduced

## Questions?

Feel free to open an issue for:
- Questions about the codebase
- Clarification on contributing process
- Discussion of new features

## License

By contributing, you agree that your contributions will be licensed under the GPL-2.0+ License.
