/**
 * Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Load categories on page load
        loadCategories();
        
        /**
         * Load categories via AJAX
         */
        function loadCategories() {
            $.ajax({
                url: bpXprofileImporter.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bp_xprofile_get_categories',
                    nonce: bpXprofileImporter.nonce
                },
                success: function(response) {
                    if (response.success && response.data.categories) {
                        populateCategoryDropdowns(response.data.categories);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading categories:', error);
                }
            });
        }
        
        /**
         * Populate category dropdowns
         */
        function populateCategoryDropdowns(categories) {
            var importSelect = $('#import-category');
            var exportSelect = $('#export-category');
            
            // Clear existing options (except first)
            importSelect.find('option:not(:first)').remove();
            exportSelect.find('option:not(:first)').remove();
            
            // Add categories
            $.each(categories, function(index, category) {
                importSelect.append(
                    $('<option></option>')
                        .val(category.id)
                        .text(category.name)
                );
                exportSelect.append(
                    $('<option></option>')
                        .val(category.id)
                        .text(category.name)
                );
            });
        }
        
        /**
         * Handle import form submission
         */
        $('#bp-xprofile-import-form').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var spinner = form.find('.spinner');
            var resultsDiv = $('#import-results');
            
            // Validate file
            var fileInput = $('#import-file')[0];
            if (!fileInput.files || fileInput.files.length === 0) {
                showMessage(resultsDiv, 'error', 'Please select a file to import.');
                return;
            }
            
            // Validate category
            var categoryId = $('#import-category').val();
            if (!categoryId) {
                showMessage(resultsDiv, 'error', 'Please select a category.');
                return;
            }
            
            // Prepare form data
            var formData = new FormData();
            formData.append('action', 'bp_xprofile_import_csv');
            formData.append('nonce', bpXprofileImporter.nonce);
            formData.append('file', fileInput.files[0]);
            formData.append('category_id', categoryId);
            formData.append('enable_conditional', $('#import-conditional').is(':checked') ? '1' : '0');
            
            // Show loading state
            submitBtn.prop('disabled', true);
            spinner.addClass('is-active');
            resultsDiv.hide();
            
            // Send AJAX request
            $.ajax({
                url: bpXprofileImporter.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        var message = bpXprofileImporter.strings.importSuccess;
                        message += '<br>Imported: ' + response.data.imported;
                        message += '<br>Skipped: ' + response.data.skipped;
                        
                        if (response.data.errors && response.data.errors.length > 0) {
                            message += '<br><br><strong>Errors:</strong>';
                            message += '<ul class="error-list">';
                            $.each(response.data.errors, function(index, error) {
                                message += '<li>' + error + '</li>';
                            });
                            message += '</ul>';
                        }
                        
                        showMessage(resultsDiv, 'success', message);
                        
                        // Reset form
                        form[0].reset();
                    } else {
                        showMessage(resultsDiv, 'error', response.data.message || bpXprofileImporter.strings.importError);
                    }
                },
                error: function(xhr, status, error) {
                    showMessage(resultsDiv, 'error', bpXprofileImporter.strings.importError + ' ' + error);
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    spinner.removeClass('is-active');
                }
            });
        });
        
        /**
         * Handle export form submission
         */
        $('#bp-xprofile-export-form').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var spinner = form.find('.spinner');
            var resultsDiv = $('#export-results');
            
            var categoryId = $('#export-category').val();
            var format = $('#export-format').val();
            var includeConditional = $('#export-conditional').is(':checked') ? '1' : '0';
            
            // Show loading state
            submitBtn.prop('disabled', true);
            spinner.addClass('is-active');
            resultsDiv.hide();
            
            // Send AJAX request
            $.ajax({
                url: bpXprofileImporter.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bp_xprofile_export_csv',
                    nonce: bpXprofileImporter.nonce,
                    category_id: categoryId,
                    format: format,
                    include_conditional: includeConditional
                },
                success: function(response) {
                    if (response.success) {
                        var message = bpXprofileImporter.strings.exportSuccess;
                        message += '<br><a href="' + response.data.file_url + '" class="download-link" download>Download File</a>';
                        showMessage(resultsDiv, 'success', message);
                    } else {
                        showMessage(resultsDiv, 'error', response.data.message || bpXprofileImporter.strings.exportError);
                    }
                },
                error: function(xhr, status, error) {
                    showMessage(resultsDiv, 'error', bpXprofileImporter.strings.exportError + ' ' + error);
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    spinner.removeClass('is-active');
                }
            });
        });
        
        /**
         * Show message in results div
         */
        function showMessage(div, type, message) {
            div.removeClass('notice-success notice-error')
               .addClass('notice-' + type)
               .html('<p>' + message + '</p>')
               .show();
        }
        
    });
    
})(jQuery);
