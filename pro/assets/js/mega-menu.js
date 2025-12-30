/**
 * Mega Menu - Admin JavaScript
 * 
 * @package Nexus_Pro
 * @since 3.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Initialize color pickers
        initColorPickers();

        // Toggle mega menu settings visibility
        toggleMegaMenuSettings();

        // Handle menu item additions
        $(document).on('menu-item-added', function() {
            initColorPickers();
            toggleMegaMenuSettings();
            initColumnPreview();
        });

        // Icon preview
        handleIconPreview();

        // Initialize icon picker
        initIconPicker();

        // Initialize column preview
        initColumnPreview();
    });

    /**
     * Initialize color pickers
     */
    function initColorPickers() {
        $('.nexus-color-picker').each(function() {
            var $picker = $(this);
            
            if (!$picker.hasClass('wp-color-picker-initialized')) {
                $picker.wpColorPicker({
                    change: function(event, ui) {
                        var color = ui.color.toString();
                        $picker.val(color).trigger('change');
                    },
                    clear: function() {
                        $picker.val('').trigger('change');
                    }
                });
                $picker.addClass('wp-color-picker-initialized');
            }
        });
    }

    /**
     * Toggle mega menu settings
     */
    function toggleMegaMenuSettings() {
        $(document).on('change', 'input[name^="menu-item-nexus-mega"]', function() {
            var $checkbox = $(this);
            var $menuItem = $checkbox.closest('.menu-item');
            var $columnsField = $menuItem.find('.nexus-mega-columns');

            if ($checkbox.is(':checked')) {
                $columnsField.slideDown(200);
            } else {
                $columnsField.slideUp(200);
            }
        });

        // Initialize on page load
        $('input[name^="menu-item-nexus-mega"]:checked').each(function() {
            $(this).closest('.menu-item').find('.nexus-mega-columns').show();
        });
    }

    /**
     * Handle icon preview
     */
    function handleIconPreview() {
        $(document).on('input', 'input[name^="menu-item-nexus-icon"]', function() {
            var $input = $(this);
            var iconClass = $input.val();
            var $preview = $input.siblings('.nexus-icon-preview');

            if (!$preview.length) {
                $preview = $('<span class="nexus-icon-preview dashicons"></span>');
                $input.after($preview);
            }

            if (iconClass) {
                $preview.attr('class', 'nexus-icon-preview dashicons ' + iconClass).show();
            } else {
                $preview.hide();
            }
        });

        // Initialize on page load
        $('input[name^="menu-item-nexus-icon"]').each(function() {
            var $input = $(this);
            var iconClass = $input.val();

            if (iconClass) {
                var $preview = $('<span class="nexus-icon-preview dashicons ' + iconClass + '"></span>');
                $input.after($preview);
            }
        });
    }

    /**
     * Badge preview
     */
    $(document).on('input', 'input[name^="menu-item-nexus-badge"]', function() {
        var $input = $(this);
        var badgeText = $input.val();
        var $menuItem = $input.closest('.menu-item');
        var $colorInput = $menuItem.find('input[name^="menu-item-nexus-badge-color"]');
        var badgeColor = $colorInput.val() || '#e74c3c';

        var $preview = $input.siblings('.nexus-badge-preview');

        if (!$preview.length) {
            $preview = $('<span class="nexus-badge-preview"></span>');
            $input.after($preview);
        }

        if (badgeText) {
            $preview
                .text(badgeText)
                .css('background-color', badgeColor)
                .css({
                    'display': 'inline-block',
                    'padding': '2px 8px',
                    'margin-left': '8px',
                    'border-radius': '10px',
                    'font-size': '10px',
                    'font-weight': 'bold',
                    'color': '#fff',
                    'text-transform': 'uppercase'
                })
                .show();
        } else {
            $preview.hide();
        }
    });

    /**
     * Update badge preview color
     */
    $(document).on('change', '.nexus-color-picker', function() {
        var $colorInput = $(this);
        var $menuItem = $colorInput.closest('.menu-item');
        var $badgeInput = $menuItem.find('input[name^="menu-item-nexus-badge"]');
        var $preview = $badgeInput.siblings('.nexus-badge-preview');
        var badgeColor = $colorInput.val() || '#e74c3c';

        if ($preview.length && $preview.is(':visible')) {
            $preview.css('background-color', badgeColor);
        }
    });

    /**
     * Menu item drag and drop
     */
    var menuItemDepth = 0;
    
    $(document).on('sortstart', '#menu-to-edit', function(event, ui) {
        menuItemDepth = ui.item.menuItemDepth();
    });

    $(document).on('sortstop', '#menu-to-edit', function(event, ui) {
        var newDepth = ui.item.menuItemDepth();
        
        // If moved to top level, show mega menu option
        if (newDepth === 0 && menuItemDepth !== 0) {
            ui.item.find('.nexus-mega-toggle').slideDown(200);
        }
        // If moved from top level, hide mega menu option
        else if (newDepth !== 0 && menuItemDepth === 0) {
            ui.item.find('.nexus-mega-toggle').slideUp(200);
            ui.item.find('input[name^="menu-item-nexus-mega"]').prop('checked', false);
            ui.item.find('.nexus-mega-columns').hide();
        }
    });

    /**
     * Accessibility enhancements
     */
    $(document).on('keydown', '.nexus-mega-menu-settings input, .nexus-mega-menu-settings select', function(e) {
        if (e.key === 'Escape') {
            $(this).blur();
        }
    });

    /**
     * Auto-save indicator
     */
    var saveTimeout;
    
    $(document).on('change', '.nexus-mega-menu-settings input, .nexus-mega-menu-settings select', function() {
        var $field = $(this);
        var $menuItem = $field.closest('.menu-item');
        var $indicator = $menuItem.find('.nexus-save-indicator');

        if (!$indicator.length) {
            $indicator = $('<span class="nexus-save-indicator" style="color: #46b450; margin-left: 5px; display: none;">✓ Saved</span>');
            $field.after($indicator);
        }

        clearTimeout(saveTimeout);
        
    /**
     * Initialize icon picker
     */
    function initIconPicker() {
        // Create icon picker modal (only once)
        if (!$('#nexus-icon-picker-modal').length) {
            createIconPickerModal();
        }

        // Add icon picker buttons to icon fields
        $('input[name^="menu-item-nexus-icon"]').each(function() {
            var $input = $(this);
            
            if (!$input.siblings('.nexus-icon-picker-button').length) {
                var $button = $('<button type="button" class="nexus-icon-picker-button">Choose Icon</button>');
                $input.after($button);

                $button.on('click', function(e) {
                    e.preventDefault();
                    openIconPicker($input);
                });
            }
        });
    }

    /**
     * Create icon picker modal
     */
    function createIconPickerModal() {
        var dashicons = getDashicons();
        
        var modalHTML = '<div id="nexus-icon-picker-overlay" class="nexus-dashicons-picker-overlay"></div>';
        modalHTML += '<div id="nexus-icon-picker-modal" class="nexus-dashicons-picker-modal">';
        modalHTML += '  <div class="nexus-dashicons-picker-header">';
        modalHTML += '    <h3>Choose an Icon</h3>';
        modalHTML += '    <button type="button" class="nexus-dashicons-picker-close">&times;</button>';
        modalHTML += '  </div>';
        modalHTML += '  <div class="nexus-dashicons-picker-search">';
        modalHTML += '    <input type="text" placeholder="Search icons..." id="nexus-icon-search" />';
        modalHTML += '  </div>';
        modalHTML += '  <div class="nexus-dashicons-picker-body">';
        modalHTML += '    <div class="nexus-dashicons-grid">';
        
        dashicons.forEach(function(icon) {
            modalHTML += '<div class="nexus-dashicon-item" data-icon="' + icon + '" title="' + icon + '">';
            modalHTML += '  <span class="dashicons ' + icon + '"></span>';
            modalHTML += '</div>';
        });
        
        modalHTML += '    </div>';
        modalHTML += '  </div>';
        modalHTML += '</div>';

        $('body').append(modalHTML);

        // Close modal handlers
        $('#nexus-icon-picker-overlay, .nexus-dashicons-picker-close').on('click', closeIconPicker);

        // Search functionality
        $('#nexus-icon-search').on('input', function() {
            var searchTerm = $(this).val().toLowerCase();
            
            $('.nexus-dashicon-item').each(function() {
                var iconName = $(this).data('icon').toLowerCase();
                
                if (iconName.indexOf(searchTerm) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    }

    /**
     * Open icon picker
     */
    var currentIconInput;
    
    function openIconPicker($input) {
        currentIconInput = $input;
        
        $('#nexus-icon-picker-modal, #nexus-icon-picker-overlay').addClass('active');
        $('#nexus-icon-search').val('').focus();
        
        // Highlight selected icon
        var currentIcon = $input.val();
        $('.nexus-dashicon-item').removeClass('selected');
        
        if (currentIcon) {
            $('.nexus-dashicon-item[data-icon="' + currentIcon + '"]').addClass('selected');
        }

        // Icon selection
        $('.nexus-dashicon-item').off('click').on('click', function() {
            var icon = $(this).data('icon');
            currentIconInput.val(icon).trigger('input');
            closeIconPicker();
        });
    }

    /**
     * Close icon picker
     */
    function closeIconPicker() {
        $('#nexus-icon-picker-modal, #nexus-icon-picker-overlay').removeClass('active');
        $('#nexus-icon-search').val('');
        $('.nexus-dashicon-item').show();
    }

    /**
     * Get Dashicons list
     */
    function getDashicons() {
        return [
            'dashicons-admin-appearance', 'dashicons-admin-collapse', 'dashicons-admin-comments',
            'dashicons-admin-customizer', 'dashicons-admin-generic', 'dashicons-admin-home',
            'dashicons-admin-links', 'dashicons-admin-media', 'dashicons-admin-multisite',
            'dashicons-admin-network', 'dashicons-admin-page', 'dashicons-admin-plugins',
            'dashicons-admin-post', 'dashicons-admin-settings', 'dashicons-admin-site',
            'dashicons-admin-tools', 'dashicons-admin-users', 'dashicons-album',
            'dashicons-align-center', 'dashicons-align-left', 'dashicons-align-none',
            'dashicons-align-right', 'dashicons-analytics', 'dashicons-archive',
            'dashicons-arrow-down', 'dashicons-arrow-down-alt', 'dashicons-arrow-down-alt2',
            'dashicons-arrow-left', 'dashicons-arrow-left-alt', 'dashicons-arrow-left-alt2',
            'dashicons-arrow-right', 'dashicons-arrow-right-alt', 'dashicons-arrow-right-alt2',
            'dashicons-arrow-up', 'dashicons-arrow-up-alt', 'dashicons-arrow-up-alt2',
            'dashicons-art', 'dashicons-awards', 'dashicons-backup', 'dashicons-book',
            'dashicons-book-alt', 'dashicons-building', 'dashicons-businessman',
            'dashicons-calendar', 'dashicons-calendar-alt', 'dashicons-camera',
            'dashicons-camera-alt', 'dashicons-carrot', 'dashicons-cart', 'dashicons-category',
            'dashicons-chart-area', 'dashicons-chart-bar', 'dashicons-chart-line',
            'dashicons-chart-pie', 'dashicons-clipboard', 'dashicons-clock', 'dashicons-cloud',
            'dashicons-cloud-saved', 'dashicons-cloud-upload', 'dashicons-code-standards',
            'dashicons-coffee', 'dashicons-cog', 'dashicons-controls-back',
            'dashicons-controls-forward', 'dashicons-controls-pause', 'dashicons-controls-play',
            'dashicons-controls-repeat', 'dashicons-controls-skipback',
            'dashicons-controls-skipforward', 'dashicons-controls-volumeoff',
            'dashicons-controls-volumeon', 'dashicons-dashboard', 'dashicons-database',
            'dashicons-desktop', 'dashicons-dismiss', 'dashicons-download', 'dashicons-edit',
            'dashicons-editor-aligncenter', 'dashicons-editor-alignleft',
            'dashicons-editor-alignright', 'dashicons-editor-bold', 'dashicons-editor-code',
            'dashicons-editor-customchar', 'dashicons-editor-expand', 'dashicons-editor-help',
            'dashicons-editor-indent', 'dashicons-editor-insertmore', 'dashicons-editor-italic',
            'dashicons-editor-justify', 'dashicons-editor-ol', 'dashicons-editor-outdent',
            'dashicons-editor-paragraph', 'dashicons-editor-paste-text', 'dashicons-editor-paste-word',
            'dashicons-editor-quote', 'dashicons-editor-removeformatting', 'dashicons-editor-rtl',
            'dashicons-editor-spellcheck', 'dashicons-editor-strikethrough', 'dashicons-editor-table',
            'dashicons-editor-textcolor', 'dashicons-editor-ul', 'dashicons-editor-underline',
            'dashicons-editor-unlink', 'dashicons-editor-video', 'dashicons-email',
            'dashicons-email-alt', 'dashicons-embed-audio', 'dashicons-embed-generic',
            'dashicons-embed-photo', 'dashicons-embed-post', 'dashicons-embed-video',
            'dashicons-exit', 'dashicons-external', 'dashicons-facebook', 'dashicons-feedback',
            'dashicons-filter', 'dashicons-flag', 'dashicons-format-aside', 'dashicons-format-audio',
            'dashicons-format-chat', 'dashicons-format-gallery', 'dashicons-format-image',
            'dashicons-format-quote', 'dashicons-format-status', 'dashicons-format-video',
            'dashicons-forms', 'dashicons-games', 'dashicons-google', 'dashicons-grid-view',
            'dashicons-groups', 'dashicons-hammer', 'dashicons-heart', 'dashicons-hidden',
            'dashicons-id', 'dashicons-id-alt', 'dashicons-image-crop', 'dashicons-image-filter',
            'dashicons-image-flip-horizontal', 'dashicons-image-flip-vertical', 'dashicons-image-rotate',
            'dashicons-image-rotate-left', 'dashicons-image-rotate-right', 'dashicons-images-alt',
            'dashicons-images-alt2', 'dashicons-index-card', 'dashicons-info', 'dashicons-laptop',
            'dashicons-layout', 'dashicons-leftright', 'dashicons-lightbulb', 'dashicons-list-view',
            'dashicons-location', 'dashicons-location-alt', 'dashicons-lock', 'dashicons-marker',
            'dashicons-media-archive', 'dashicons-media-audio', 'dashicons-media-code',
            'dashicons-media-default', 'dashicons-media-document', 'dashicons-media-interactive',
            'dashicons-media-spreadsheet', 'dashicons-media-text', 'dashicons-media-video',
            'dashicons-megaphone', 'dashicons-menu', 'dashicons-menu-alt', 'dashicons-migrate',
            'dashicons-minus', 'dashicons-money', 'dashicons-move', 'dashicons-money-alt',
            'dashicons-networking', 'dashicons-no', 'dashicons-no-alt', 'dashicons-palmtree',
            'dashicons-paperclip', 'dashicons-performance', 'dashicons-phone', 'dashicons-playlist-audio',
            'dashicons-playlist-video', 'dashicons-plus', 'dashicons-plus-alt', 'dashicons-portfolio',
            'dashicons-post-status', 'dashicons-pressthis', 'dashicons-products', 'dashicons-randomize',
            'dashicons-redo', 'dashicons-rest-api', 'dashicons-rss', 'dashicons-saved',
            'dashicons-schedule', 'dashicons-screenoptions', 'dashicons-search', 'dashicons-share',
            'dashicons-share-alt', 'dashicons-share-alt2', 'dashicons-shield', 'dashicons-shield-alt',
            'dashicons-smartphone', 'dashicons-smiley', 'dashicons-sort', 'dashicons-sos',
            'dashicons-star-empty', 'dashicons-star-filled', 'dashicons-star-half',
            'dashicons-store', 'dashicons-superhero', 'dashicons-superhero-alt', 'dashicons-tablet',
            'dashicons-tag', 'dashicons-tagcloud', 'dashicons-testimonial', 'dashicons-text',
            'dashicons-thumbs-down', 'dashicons-thumbs-up', 'dashicons-tickets', 'dashicons-tickets-alt',
            'dashicons-tide', 'dashicons-translation', 'dashicons-trash', 'dashicons-twitter',
            'dashicons-undo', 'dashicons-universal-access', 'dashicons-universal-access-alt',
            'dashicons-unlock', 'dashicons-update', 'dashicons-update-alt', 'dashicons-upload',
            'dashicons-vault', 'dashicons-video-alt', 'dashicons-video-alt2', 'dashicons-video-alt3',
            'dashicons-visibility', 'dashicons-warning', 'dashicons-welcome-add-page',
            'dashicons-welcome-comments', 'dashicons-welcome-learn-more', 'dashicons-welcome-view-site',
            'dashicons-welcome-widgets-menus', 'dashicons-welcome-write-blog', 'dashicons-wordpress',
            'dashicons-wordpress-alt', 'dashicons-yes', 'dashicons-yes-alt'
        ];
    }

    /**
     * Initialize column preview
     */
    function initColumnPreview() {
        $('select[name^="menu-item-nexus-columns"]').each(function() {
            var $select = $(this);
            var $menuItem = $select.closest('.menu-item');
            
            if (!$menuItem.find('.nexus-column-preview').length) {
                var $preview = createColumnPreview($select.val() || 4);
                $select.closest('p').after($preview);
            }

            $select.off('change.columnPreview').on('change.columnPreview', function() {
                var columns = $(this).val();
                $menuItem.find('.nexus-column-preview').replaceWith(createColumnPreview(columns));
            });
        });
    }

    /**
     * Create column preview
     */
    function createColumnPreview(columns) {
        var $preview = $('<div class="nexus-column-preview"></div>');
        $preview.append('<span class="nexus-column-preview-label">Column Layout Preview:</span>');
        
        var $grid = $('<div class="nexus-column-preview-grid columns-' + columns + '"></div>');
        
        for (var i = 1; i <= columns; i++) {
            $grid.append('<div class="nexus-column-preview-item">Col ' + i + '</div>');
        }
        
        $preview.append($grid);
        
        return $preview;
    }

        $indicator.text('Saving...').css('color', '#999').fadeIn(200);

        saveTimeout = setTimeout(function() {
            $indicator.text('✓ Saved').css('color', '#46b450').fadeOut(2000);
        }, 500);
    });

})(jQuery);
