#!/bin/bash

# Plugin Structure Validation Script
# This script checks that all required plugin files are present

echo "==================================="
echo "Plugin Structure Validation"
echo "==================================="
echo ""

# Get the directory where this script is located
PLUGIN_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PLUGIN_DIR"

# Check main plugin file
echo "✓ Checking main plugin file..."
if [ -f "bp-xprofile-csv-importer.php" ]; then
    echo "  ✓ bp-xprofile-csv-importer.php exists"
    php -l bp-xprofile-csv-importer.php > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        echo "  ✓ PHP syntax is valid"
    else
        echo "  ✗ PHP syntax error found"
        exit 1
    fi
else
    echo "  ✗ Main plugin file missing"
    exit 1
fi

echo ""
echo "✓ Checking required directories..."
for dir in includes assets assets/css assets/js examples; do
    if [ -d "$dir" ]; then
        echo "  ✓ $dir/ exists"
    else
        echo "  ✗ $dir/ missing"
        exit 1
    fi
done

echo ""
echo "✓ Checking include files..."
for file in includes/admin-page.php includes/class-importer.php includes/class-exporter.php; do
    if [ -f "$file" ]; then
        echo "  ✓ $file exists"
        php -l "$file" > /dev/null 2>&1
        if [ $? -ne 0 ]; then
            echo "  ✗ PHP syntax error in $file"
            exit 1
        fi
    else
        echo "  ✗ $file missing"
        exit 1
    fi
done

echo ""
echo "✓ Checking asset files..."
for file in assets/css/admin.css assets/js/admin.js; do
    if [ -f "$file" ]; then
        echo "  ✓ $file exists"
    else
        echo "  ✗ $file missing"
        exit 1
    fi
done

echo ""
echo "✓ Checking documentation files..."
for file in README.md INSTALL.md CHANGELOG.md composer.json; do
    if [ -f "$file" ]; then
        echo "  ✓ $file exists"
    else
        echo "  ✗ $file missing"
        exit 1
    fi
done

echo ""
echo "✓ Checking example files..."
if [ -f "examples/sample-import.csv" ]; then
    echo "  ✓ sample-import.csv exists"
else
    echo "  ✗ sample-import.csv missing"
    exit 1
fi

echo ""
echo "==================================="
echo "All checks passed! ✓"
echo "==================================="
echo ""
echo "Plugin structure is valid and ready for use."
echo ""
echo "Next steps:"
echo "1. Run 'composer install' to install PHPSpreadsheet"
echo "2. Copy plugin to WordPress plugins directory"
echo "3. Activate required plugins (BuddyPress, etc.)"
echo "4. Activate this plugin"
