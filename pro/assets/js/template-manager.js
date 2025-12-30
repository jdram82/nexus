/**
 * Template Manager - JavaScript
 *
 * @package Nexus_Pro
 * @subpackage Templates
 * @since 3.0.0
 */

(function($) {
    'use strict';

    /**
     * Template Manager
     */
    var NexusTemplateManager = {
        
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initCategoryFilter();
            this.loadTemplates();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            var self = this;

            // Category filter
            $(document).on('click', '.category-filter-item', function() {
                $(this).toggleClass('active').siblings().removeClass('active');
                self.loadTemplates();
            });

            // Search
            $(document).on('input', '#template-search', function() {
                clearTimeout(self.searchTimeout);
                self.searchTimeout = setTimeout(function() {
                    self.loadTemplates();
                }, 300);
            });

            // Type filter
            $(document).on('change', '#template-type', function() {
                self.loadTemplates();
            });

            // Preview template
            $(document).on('click', '.preview-template', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var templateId = $(this).closest('.template-card').data('template-id');
                self.previewTemplate(templateId);
            });

            // Import template
            $(document).on('click', '.import-template', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var templateId = $(this).closest('.template-card').data('template-id');
                self.importTemplate(templateId);
            });

            // Export template
            $(document).on('click', '#export-template', function(e) {
                e.preventDefault();
                self.exportTemplate();
            });

            // Sync cloud
            $(document).on('click', '#sync-cloud', function(e) {
                e.preventDefault();
                self.syncCloud();
            });

            // Close modal
            $(document).on('click', '.template-preview-close, .template-preview-modal', function(e) {
                if ($(e.target).hasClass('template-preview-modal') || $(e.target).hasClass('template-preview-close')) {
                    self.closePreview();
                }
            });

            // ESC key to close modal
            $(document).on('keyup', function(e) {
                if (e.key === 'Escape') {
                    self.closePreview();
                }
            });

            // Tab switching
            $(document).on('click', '.nav-tab', function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                $('.tab-content').removeClass('active');
                $(target).addClass('active');
            });
        },

        /**
         * Initialize category filter
         */
        initCategoryFilter: function() {
            var categories = [
                { id: 'all', name: 'All Templates', icon: 'dashicons-portfolio' },
                { id: 'business', name: 'Business', icon: 'dashicons-building' },
                { id: 'saas', name: 'SaaS', icon: 'dashicons-cloud' },
                { id: 'ecommerce', name: 'E-commerce', icon: 'dashicons-cart' },
                { id: 'portfolio', name: 'Portfolio', icon: 'dashicons-images-alt2' },
                { id: 'blog', name: 'Blog', icon: 'dashicons-edit' },
                { id: 'documentation', name: 'Documentation', icon: 'dashicons-media-document' },
                { id: 'landing', name: 'Landing Page', icon: 'dashicons-megaphone' },
                { id: 'marketing', name: 'Marketing', icon: 'dashicons-chart-line' },
                { id: 'education', name: 'Education', icon: 'dashicons-book' },
                { id: 'events', name: 'Events', icon: 'dashicons-tickets-alt' }
            ];

            var $container = $('.category-filter');
            if ($container.length === 0) return;

            $container.empty();
            categories.forEach(function(cat) {
                var $item = $('<div>')
                    .addClass('category-filter-item')
                    .attr('data-category', cat.id)
                    .html('<span class="dashicons ' + cat.icon + '"></span> ' + cat.name + ' <span class="count">0</span>');
                
                if (cat.id === 'all') {
                    $item.addClass('active');
                }
                
                $container.append($item);
            });
        },

        /**
         * Load templates
         */
        loadTemplates: function() {
            var self = this;
            var $grid = $('.templates-grid');
            var category = $('.category-filter-item.active').data('category') || 'all';
            var search = $('#template-search').val() || '';
            var type = $('#template-type').val() || 'all';

            $grid.html('<div class="loading"><span class="spinner is-active"></span><p>Loading templates...</p></div>');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nexus_browse_templates',
                    category: category,
                    search: search,
                    type: type,
                    nonce: nexusTemplates.nonce
                },
                success: function(response) {
                    if (response.success && response.data.templates) {
                        self.renderTemplates(response.data.templates);
                        self.updateCategoryCounts(response.data.categories);
                    } else {
                        $grid.html('<div class="no-templates"><p>No templates found.</p></div>');
                    }
                },
                error: function() {
                    $grid.html('<div class="no-templates"><p>Error loading templates. Please try again.</p></div>');
                }
            });
        },

        /**
         * Render templates
         */
        renderTemplates: function(templates) {
            var $grid = $('.templates-grid');
            $grid.empty();

            if (templates.length === 0) {
                $grid.html('<div class="no-templates"><p>No templates found matching your criteria.</p></div>');
                return;
            }

            templates.forEach(function(template) {
                var $card = $('<div>')
                    .addClass('template-card')
                    .attr('data-template-id', template.id);

                var thumbnail = template.thumbnail 
                    ? '<img src="' + template.thumbnail + '" alt="' + template.name + '">'
                    : '<div class="template-placeholder"><span class="dashicons dashicons-image-filter"></span></div>';

                var badges = '';
                if (template.type) {
                    badges += '<span class="badge">' + template.type + '</span>';
                }
                if (template.category) {
                    badges += '<span class="category-badge">' + template.category + '</span>';
                }
                if (template.cloud) {
                    badges += '<span class="cloud-badge">Cloud Synced</span>';
                }

                $card.html(
                    thumbnail +
                    '<div class="template-info">' +
                        '<h3>' + template.name + '</h3>' +
                        '<p>' + template.description + '</p>' +
                        '<div class="template-meta">' + badges + '</div>' +
                    '</div>' +
                    '<div class="template-actions-buttons">' +
                        '<button class="button preview-template" aria-label="Preview ' + template.name + '">Preview</button>' +
                        '<button class="button button-primary import-template" aria-label="Import ' + template.name + '">Import</button>' +
                    '</div>'
                );

                $grid.append($card);
            });
        },

        /**
         * Update category counts
         */
        updateCategoryCounts: function(categories) {
            $('.category-filter-item').each(function() {
                var catId = $(this).data('category');
                var count = categories[catId] || 0;
                $(this).find('.count').text(count);
            });
        },

        /**
         * Preview template
         */
        previewTemplate: function(templateId) {
            var self = this;
            var $modal = $('.template-preview-modal');

            if ($modal.length === 0) {
                $modal = $('<div class="template-preview-modal">' +
                    '<div class="template-preview-content">' +
                        '<div class="template-preview-header">' +
                            '<h2>Template Preview</h2>' +
                            '<button class="template-preview-close">&times;</button>' +
                        '</div>' +
                        '<div class="template-preview-body"></div>' +
                        '<div class="template-preview-footer">' +
                            '<button class="button button-secondary template-preview-close">Close</button>' +
                            '<button class="button button-primary import-template-preview">Import Template</button>' +
                        '</div>' +
                    '</div>' +
                '</div>');
                $('body').append($modal);
            }

            $modal.find('.template-preview-body').html('<div class="loading"><span class="spinner is-active"></span><p>Loading preview...</p></div>');
            $modal.addClass('active');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nexus_preview_template',
                    template_id: templateId,
                    nonce: nexusTemplates.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var html = '<div class="template-preview-info">' +
                            '<h3>' + response.data.name + '</h3>' +
                            '<p>' + response.data.description + '</p>' +
                            '<div class="import-options">' +
                                '<label><input type="checkbox" name="import_settings" checked> Import Settings</label>' +
                                '<label><input type="checkbox" name="import_content" checked> Import Content</label>' +
                                '<label><input type="checkbox" name="import_media" checked> Import Media</label>' +
                            '</div>' +
                        '</div>';

                        if (response.data.preview_url) {
                            html += '<iframe src="' + response.data.preview_url + '"></iframe>';
                        } else if (response.data.html) {
                            html += '<div class="template-preview-html">' + response.data.html + '</div>';
                        }

                        $modal.find('.template-preview-body').html(html);
                        $modal.find('.import-template-preview').data('template-id', templateId);
                    } else {
                        $modal.find('.template-preview-body').html('<p>Error loading preview.</p>');
                    }
                }
            });
        },

        /**
         * Close preview
         */
        closePreview: function() {
            $('.template-preview-modal').removeClass('active');
        },

        /**
         * Import template
         */
        importTemplate: function(templateId) {
            var self = this;
            
            if (!confirm('Import this template? This will create a new page with the template content.')) {
                return;
            }

            var options = {
                import_settings: true,
                import_content: true,
                import_media: true
            };

            // Get options from preview modal if open
            var $modal = $('.template-preview-modal.active');
            if ($modal.length > 0) {
                options.import_settings = $modal.find('[name="import_settings"]').is(':checked');
                options.import_content = $modal.find('[name="import_content"]').is(':checked');
                options.import_media = $modal.find('[name="import_media"]').is(':checked');
            }

            // Show loading
            var $button = $('.import-template[data-template-id="' + templateId + '"]');
            var originalText = $button.text();
            $button.prop('disabled', true).html('<span class="spinner is-active"></span> Importing...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nexus_import_template',
                    template_id: templateId,
                    options: options,
                    nonce: nexusTemplates.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.showMessage('Template imported successfully!', 'success');
                        self.closePreview();
                        
                        // Redirect to edit page
                        if (response.data.page_id) {
                            window.location.href = nexusTemplates.adminUrl + 'post.php?post=' + response.data.page_id + '&action=edit';
                        }
                    } else {
                        self.showMessage(response.data.message || 'Error importing template.', 'error');
                        $button.prop('disabled', false).text(originalText);
                    }
                },
                error: function() {
                    self.showMessage('Error importing template. Please try again.', 'error');
                    $button.prop('disabled', false).text(originalText);
                }
            });
        },

        /**
         * Export template
         */
        exportTemplate: function() {
            var self = this;
            var pageId = $('#export-page-id').val();

            if (!pageId) {
                alert('Please select a page to export.');
                return;
            }

            var $button = $('#export-template');
            var originalText = $button.text();
            $button.prop('disabled', true).html('<span class="spinner is-active"></span> Exporting...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nexus_export_template',
                    page_id: pageId,
                    nonce: nexusTemplates.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Download JSON file
                        var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(response.data.template, null, 2));
                        var downloadAnchorNode = document.createElement('a');
                        downloadAnchorNode.setAttribute("href", dataStr);
                        downloadAnchorNode.setAttribute("download", response.data.filename);
                        document.body.appendChild(downloadAnchorNode);
                        downloadAnchorNode.click();
                        downloadAnchorNode.remove();

                        self.showMessage('Template exported successfully!', 'success');
                    } else {
                        self.showMessage(response.data.message || 'Error exporting template.', 'error');
                    }
                    
                    $button.prop('disabled', false).text(originalText);
                },
                error: function() {
                    self.showMessage('Error exporting template. Please try again.', 'error');
                    $button.prop('disabled', false).text(originalText);
                }
            });
        },

        /**
         * Sync with cloud
         */
        syncCloud: function() {
            var self = this;
            var $button = $('#sync-cloud');
            var originalText = $button.text();
            $button.prop('disabled', true).html('<span class="spinner is-active"></span> Syncing...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'nexus_sync_cloud',
                    nonce: nexusTemplates.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.showMessage(response.data.message || 'Cloud sync successful!', 'success');
                        self.loadTemplates();
                    } else {
                        self.showMessage(response.data.message || 'Error syncing with cloud.', 'error');
                    }
                    
                    $button.prop('disabled', false).text(originalText);
                },
                error: function() {
                    self.showMessage('Error syncing with cloud. Please try again.', 'error');
                    $button.prop('disabled', false).text(originalText);
                }
            });
        },

        /**
         * Show message
         */
        showMessage: function(message, type) {
            var $message = $('<div class="template-message ' + type + '">' + message + '</div>');
            $('.nexus-templates-wrap').prepend($message);
            
            setTimeout(function() {
                $message.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        if ($('.nexus-templates-wrap').length > 0) {
            NexusTemplateManager.init();
        }
    });

})(jQuery);
