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
        });

        // Icon preview
        handleIconPreview();
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
        
        $indicator.text('Saving...').css('color', '#999').fadeIn(200);

        saveTimeout = setTimeout(function() {
            $indicator.text('✓ Saved').css('color', '#46b450').fadeOut(2000);
        }, 500);
    });

})(jQuery);
