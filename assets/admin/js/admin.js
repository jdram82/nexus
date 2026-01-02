/**
 * Nexus Admin JavaScript
 * 
 * @package Nexus
 * @since 3.1.0
 */

(function($) {
    'use strict';

    var NexusAdmin = {
        
        /**
         * Initialize
         */
        init: function() {
            this.templateFilters();
            this.templateImport();
            this.bindEvents();
        },

        /**
         * Template filters
         */
        templateFilters: function() {
            $('.filter-btn').on('click', function() {
                var filter = $(this).data('filter');
                
                // Update active state
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                
                // Filter templates
                if (filter === 'all') {
                    $('.template-item').show();
                } else if (filter === 'premium') {
                    $('.template-item').hide();
                    $('.template-item.premium-template').show();
                } else {
                    $('.template-item').hide();
                    $('.template-item.category-' + filter).show();
                }
            });
        },

        /**
         * Template import
         */
        templateImport: function() {
            var self = this;
            
            // Show import modal
            $('.template-import').on('click', function() {
                var templateId = $(this).data('template');
                $('#nexus-import-modal').data('template', templateId).fadeIn();
            });
            
            // Close modal
            $('.modal-close, .cancel-import').on('click', function() {
                $('#nexus-import-modal').fadeOut();
            });
            
            // Confirm import
            $('.confirm-import').on('click', function() {
                self.doImport();
            });
            
            // Template preview
            $('.template-preview-btn').on('click', function() {
                var templateId = $(this).data('template');
                // Open preview in new tab (implement preview logic)
                alert('Template preview: ' + templateId);
            });
        },

        /**
         * Perform template import
         */
        doImport: function() {
            var templateId = $('#nexus-import-modal').data('template');
            var options = {
                content: $('input[name="import_content"]').is(':checked'),
                customizer: $('input[name="import_customizer"]').is(':checked'),
                widgets: $('input[name="import_widgets"]').is(':checked')
            };
            
            // Show progress
            $('.import-options, .import-warning, .modal-actions').hide();
            $('.import-progress').show();
            
            // Simulate import (replace with actual AJAX call)
            var progress = 0;
            var interval = setInterval(function() {
                progress += 10;
                $('.progress-fill').css('width', progress + '%');
                
                if (progress >= 100) {
                    clearInterval(interval);
                    setTimeout(function() {
                        alert('Template imported successfully!');
                        $('#nexus-import-modal').fadeOut();
                        $('.import-options, .import-warning, .modal-actions').show();
                        $('.import-progress').hide();
                        $('.progress-fill').css('width', '0%');
                    }, 500);
                }
            }, 200);
            
            // Actual AJAX import would be:
            /*
            $.ajax({
                url: nexusAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'nexus_import_template',
                    nonce: nexusAdmin.nonce,
                    template: templateId,
                    options: options
                },
                success: function(response) {
                    if (response.success) {
                        alert('Template imported successfully!');
                        location.reload();
                    } else {
                        alert('Import failed: ' + response.data);
                    }
                }
            });
            */
        },

        /**
         * Bind other events
         */
        bindEvents: function() {
            // Close modal on overlay click
            $(document).on('click', '.nexus-modal', function(e) {
                if (e.target === this) {
                    $(this).fadeOut();
                }
            });
            
            // Prevent form submission on Enter in text fields
            $('.nexus-admin-wrap input[type="text"]').on('keypress', function(e) {
                if (e.which === 13 && !$(this).closest('form').find('[type="submit"]').is(':focus')) {
                    e.preventDefault();
                }
            });
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        NexusAdmin.init();
    });

})(jQuery);
