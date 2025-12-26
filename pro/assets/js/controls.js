/**
 * Advanced Controls JavaScript
 * 
 * @package Nexus_Pro
 * @since 3.0.0
 */

(function($, wp) {
    'use strict';

    const NexusControls = {

        /**
         * Initialize controls
         */
        init: function() {
            this.initTypographyControls();
            this.initGradientControls();
            this.initShadowControls();
            this.initBorderControls();
            this.initSpacingControls();
            this.initIconPickerControls();
        },

        /**
         * Typography Control
         */
        initTypographyControls: function() {
            $('.nexus-typography-control').each(function() {
                const $control = $(this);
                const $hidden = $control.find('.typography-value');

                // Update on field change
                $control.find('select, input').on('change input', function() {
                    const settings = {};
                    $control.find('[data-setting]').each(function() {
                        const key = $(this).data('setting');
                        const value = $(this).val();
                        settings[key] = value;
                    });
                    $hidden.val(JSON.stringify(settings)).trigger('change');
                });
            });
        },

        /**
         * Gradient Control
         */
        initGradientControls: function() {
            $('.nexus-gradient-control').each(function() {
                const $control = $(this);
                const $hidden = $control.find('.gradient-value');
                const $preview = $control.find('.gradient-preview');
                const $angleField = $control.find('.gradient-angle-field');

                // Initialize color pickers
                $control.find('.color-picker').wpColorPicker({
                    change: function() {
                        updateGradient();
                    }
                });

                // Sync range and number inputs
                $control.find('.gradient-angle').on('input', function() {
                    $control.find('.gradient-angle-number').val($(this).val());
                    updateGradient();
                });

                $control.find('.gradient-angle-number').on('input', function() {
                    $control.find('.gradient-angle').val($(this).val());
                    updateGradient();
                });

                $control.find('.gradient-color1-pos').on('input', function() {
                    $control.find('.gradient-color1-pos-number').val($(this).val());
                    updateGradient();
                });

                $control.find('.gradient-color1-pos-number').on('input', function() {
                    $control.find('.gradient-color1-pos').val($(this).val());
                    updateGradient();
                });

                $control.find('.gradient-color2-pos').on('input', function() {
                    $control.find('.gradient-color2-pos-number').val($(this).val());
                    updateGradient();
                });

                $control.find('.gradient-color2-pos-number').on('input', function() {
                    $control.find('.gradient-color2-pos').val($(this).val());
                    updateGradient();
                });

                // Gradient type change
                $control.find('.gradient-type').on('change', function() {
                    if ($(this).val() === 'radial') {
                        $angleField.hide();
                    } else {
                        $angleField.show();
                    }
                    updateGradient();
                });

                function updateGradient() {
                    const settings = {};
                    $control.find('[data-setting]').each(function() {
                        settings[$(this).data('setting')] = $(this).val();
                    });

                    // Update preview
                    let gradient;
                    if (settings.type === 'radial') {
                        gradient = `radial-gradient(circle, ${settings['color1']} ${settings['color1-pos']}%, ${settings['color2']} ${settings['color2-pos']}%)`;
                    } else {
                        gradient = `linear-gradient(${settings.angle}deg, ${settings['color1']} ${settings['color1-pos']}%, ${settings['color2']} ${settings['color2-pos']}%)`;
                    }
                    $preview.css('background', gradient);

                    // Update hidden value
                    $hidden.val(JSON.stringify(settings)).trigger('change');
                }
            });
        },

        /**
         * Shadow Control
         */
        initShadowControls: function() {
            $('.nexus-shadow-control').each(function() {
                const $control = $(this);
                const $hidden = $control.find('.shadow-value');
                const $preview = $control.find('.shadow-preview');

                // Initialize color picker
                $control.find('.color-picker').wpColorPicker({
                    change: function() {
                        updateShadow();
                    }
                });

                // Sync range and number inputs
                $control.find('input[type="range"]').on('input', function() {
                    const setting = $(this).data('setting');
                    $control.find(`.shadow-${setting}-number`).val($(this).val());
                    updateShadow();
                });

                $control.find('input[type="number"]').on('input', function() {
                    const classes = $(this).attr('class').split(' ');
                    const rangeClass = classes.find(c => c.startsWith('shadow-') && !c.endsWith('-number'));
                    if (rangeClass) {
                        $control.find(`.${rangeClass}`).val($(this).val());
                    }
                    updateShadow();
                });

                // Inset checkbox
                $control.find('.shadow-inset').on('change', function() {
                    updateShadow();
                });

                function updateShadow() {
                    const settings = {};
                    $control.find('[data-setting]').each(function() {
                        const key = $(this).data('setting');
                        if ($(this).attr('type') === 'checkbox') {
                            settings[key] = $(this).is(':checked');
                        } else {
                            settings[key] = $(this).val();
                        }
                    });

                    // Update preview
                    const shadow = `${settings.inset ? 'inset ' : ''}${settings.horizontal}px ${settings.vertical}px ${settings.blur}px ${settings.spread}px ${settings.color}`;
                    $preview.css('box-shadow', shadow);

                    // Update hidden value
                    $hidden.val(JSON.stringify(settings)).trigger('change');
                }
            });
        },

        /**
         * Border Control
         */
        initBorderControls: function() {
            $('.nexus-border-control').each(function() {
                const $control = $(this);
                const $hidden = $control.find('.border-value');
                const $preview = $control.find('.border-preview');

                // Initialize color picker
                $control.find('.color-picker').wpColorPicker({
                    change: function() {
                        updateBorder();
                    }
                });

                // Sync range and number inputs
                $control.find('.border-width, .border-radius').on('input', function() {
                    const classes = $(this).attr('class').split(' ');
                    const baseClass = classes[0];
                    $control.find(`.${baseClass}-number`).val($(this).val());
                    updateBorder();
                });

                $control.find('.border-width-number, .border-radius-number').on('input', function() {
                    const classes = $(this).attr('class').split(' ');
                    const baseClass = classes[0].replace('-number', '');
                    $control.find(`.${baseClass}`).val($(this).val());
                    updateBorder();
                });

                // Border style change
                $control.find('.border-style').on('change', function() {
                    updateBorder();
                });

                function updateBorder() {
                    const settings = {};
                    $control.find('[data-setting]').each(function() {
                        settings[$(this).data('setting')] = $(this).val();
                    });

                    // Update preview
                    $preview.css({
                        'border': `${settings.width}px ${settings.style} ${settings.color}`,
                        'border-radius': `${settings.radius}px`
                    });

                    // Update hidden value
                    $hidden.val(JSON.stringify(settings)).trigger('change');
                }
            });
        },

        /**
         * Spacing Control
         */
        initSpacingControls: function() {
            $('.nexus-spacing-control').each(function() {
                const $control = $(this);
                const $hidden = $control.find('.spacing-value');
                const $linkBtn = $control.find('.spacing-link-btn');
                const $inputs = $control.find('.spacing-input');
                let isLinked = $linkBtn.hasClass('is-linked');

                // Link/Unlink toggle
                $linkBtn.on('click', function(e) {
                    e.preventDefault();
                    isLinked = !isLinked;
                    $(this).toggleClass('is-linked', isLinked);
                    
                    if (isLinked) {
                        const firstValue = $inputs.first().val();
                        $inputs.val(firstValue);
                        updateSpacing();
                    }
                });

                // Input changes
                $inputs.on('input', function() {
                    if (isLinked) {
                        const value = $(this).val();
                        $inputs.val(value);
                    }
                    updateSpacing();
                });

                function updateSpacing() {
                    const settings = {linked: isLinked};
                    $control.find('[data-setting]').each(function() {
                        const key = $(this).data('setting');
                        if (key !== 'linked') {
                            settings[key] = $(this).val();
                        }
                    });

                    // Update preview values
                    $control.find('.spacing-preview-top').text(settings.top);
                    $control.find('.spacing-preview-right').text(settings.right);
                    $control.find('.spacing-preview-bottom').text(settings.bottom);
                    $control.find('.spacing-preview-left').text(settings.left);

                    // Update hidden value
                    $hidden.val(JSON.stringify(settings)).trigger('change');
                }
            });
        },

        /**
         * Icon Picker Control
         */
        initIconPickerControls: function() {
            $('.nexus-icon-picker-control').each(function() {
                const $control = $(this);
                const $hidden = $control.find('.icon-value');
                const $display = $control.find('.selected-icon-display');
                const $selectBtn = $control.find('.select-icon-btn');
                const $removeBtn = $control.find('.remove-icon-btn');
                const $modal = $control.find('.icon-picker-modal');
                const $search = $modal.find('.icon-search');
                const $closeBtn = $modal.find('.close-modal-btn');
                const $icons = $modal.find('.icon-option');

                // Open modal
                $selectBtn.on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $modal.show();
                    $search.focus();
                });

                // Close modal
                $closeBtn.on('click', function(e) {
                    e.preventDefault();
                    $modal.hide();
                });

                // Close on outside click
                $(document).on('click', function(e) {
                    if (!$control.is(e.target) && $control.has(e.target).length === 0) {
                        $modal.hide();
                    }
                });

                // Search icons
                $search.on('input', function() {
                    const search = $(this).val().toLowerCase();
                    $icons.each(function() {
                        const icon = $(this).data('icon').toLowerCase();
                        if (icon.indexOf(search) !== -1) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                });

                // Select icon
                $icons.on('click', function(e) {
                    e.preventDefault();
                    const icon = $(this).data('icon');
                    
                    // Update display
                    $selectBtn.html(`<span class="dashicons ${icon}"></span>`);
                    
                    // Show remove button if not exists
                    if ($removeBtn.length === 0) {
                        $display.append(`
                            <button type="button" class="remove-icon-btn" title="Remove Icon">
                                <span class="dashicons dashicons-no-alt"></span>
                            </button>
                        `);
                    }
                    
                    // Update hidden value
                    $hidden.val(icon).trigger('change');
                    
                    // Close modal
                    $modal.hide();
                });

                // Remove icon
                $display.on('click', '.remove-icon-btn', function(e) {
                    e.preventDefault();
                    $selectBtn.html('<span class="placeholder">Select Icon</span>');
                    $(this).remove();
                    $hidden.val('').trigger('change');
                });
            });
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        NexusControls.init();
    });

    // Re-initialize when customizer sections are expanded
    if (wp.customize) {
        wp.customize.bind('ready', function() {
            NexusControls.init();
        });
    }

})(jQuery, wp);
