/**
 * Theme Builder JavaScript
 * 
 * @package Nexus_Pro
 * @since 3.0.0
 */

(function($) {
    'use strict';

    const NexusBuilder = {
        
        /**
         * Initialize builder
         */
        init: function() {
            if (!window.nexusBuilder) {
                return;
            }

            this.postId = nexusBuilder.postId;
            this.widgets = nexusBuilder.widgets;
            this.history = [];
            this.historyIndex = -1;
            this.maxHistory = 50;

            this.bindEvents();
            this.initDragDrop();
            this.loadContent();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            const self = this;

            // Save button
            $('#nexus-save-builder').on('click', function(e) {
                e.preventDefault();
                self.saveContent();
            });

            // Device switcher
            $('.device-btn').on('click', function(e) {
                e.preventDefault();
                const device = $(this).data('device');
                self.switchDevice(device);
            });

            // History buttons
            $('#nexus-history-undo').on('click', function(e) {
                e.preventDefault();
                self.undo();
            });

            $('#nexus-history-redo').on('click', function(e) {
                e.preventDefault();
                self.redo();
            });

            // Widget controls
            $(document).on('click', '.widget-control', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const action = $(this).data('action');
                const widget = $(this).closest('.nexus-widget');
                self.handleWidgetAction(action, widget);
            });

            // Section controls
            $(document).on('click', '.section-control', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const action = $(this).data('action');
                const section = $(this).closest('.nexus-section');
                self.handleSectionAction(action, section);
            });

            // Widget search
            $('.widget-search').on('input', function() {
                const search = $(this).val().toLowerCase();
                self.filterWidgets(search);
            });

            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                // Ctrl/Cmd + S - Save
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    self.saveContent();
                }
                
                // Ctrl/Cmd + Z - Undo
                if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
                    e.preventDefault();
                    self.undo();
                }
                
                // Ctrl/Cmd + Shift + Z - Redo
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'z') {
                    e.preventDefault();
                    self.redo();
                }
            });
        },

        /**
         * Initialize drag and drop
         */
        initDragDrop: function() {
            const self = this;

            // Make widgets draggable
            $('.widget-item').draggable({
                helper: 'clone',
                appendTo: 'body',
                zIndex: 10000,
                cursor: 'move',
                revert: 'invalid',
                start: function() {
                    $('.widget-drop-zone').addClass('drag-active');
                },
                stop: function() {
                    $('.widget-drop-zone').removeClass('drag-active');
                }
            });

            // Make drop zones droppable
            $('.widget-drop-zone').droppable({
                accept: '.widget-item, .nexus-widget',
                hoverClass: 'drop-hover',
                drop: function(event, ui) {
                    const widgetType = ui.draggable.data('widget-type');
                    const columnId = $(this).closest('.nexus-column').data('column-id');
                    
                    if (widgetType) {
                        self.addWidget(widgetType, $(this));
                    }
                }
            });

            // Make widgets sortable within columns
            $('.widget-drop-zone').sortable({
                connectWith: '.widget-drop-zone',
                placeholder: 'widget-placeholder',
                handle: '.nexus-widget',
                cursor: 'move',
                tolerance: 'pointer',
                update: function() {
                    self.addToHistory();
                }
            });
        },

        /**
         * Add widget to drop zone
         */
        addWidget: function(type, $dropZone) {
            const widget = this.createWidget(type);
            
            // Remove placeholder if exists
            $dropZone.find('.drop-placeholder').remove();
            
            // Add widget
            $dropZone.append(widget);
            
            // Add to history
            this.addToHistory();
            
            // Show notification
            this.showNotification('Widget added successfully', 'success');
        },

        /**
         * Create widget element
         */
        createWidget: function(type) {
            const widgetId = 'widget-' + Date.now();
            const widgetData = this.widgets.find(w => w.type === type) || {};
            
            const $widget = $(`
                <div class="nexus-widget" data-widget-id="${widgetId}" data-widget-type="${type}">
                    <div class="widget-controls">
                        <button class="widget-control" data-action="edit" title="${nexusBuilder.i18n.editWidget}">
                            <span class="dashicons dashicons-edit"></span>
                        </button>
                        <button class="widget-control" data-action="duplicate" title="${nexusBuilder.i18n.duplicateWidget}">
                            <span class="dashicons dashicons-admin-page"></span>
                        </button>
                        <button class="widget-control" data-action="delete" title="${nexusBuilder.i18n.deleteWidget}">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                    <div class="widget-content">
                        ${this.getWidgetDefaultContent(type)}
                    </div>
                </div>
            `);
            
            return $widget;
        },

        /**
         * Get default widget content
         */
        getWidgetDefaultContent: function(type) {
            const defaults = {
                'heading': '<h2 class="nexus-heading">Your Heading</h2>',
                'text': '<div class="nexus-text-editor"><p>Your text content here...</p></div>',
                'button': '<a href="#" class="nexus-button button-primary button-medium">Click Here</a>',
                'image': '<div class="nexus-image-placeholder">Select an image</div>',
                'icon': '<div class="nexus-icon"><span class="dashicons dashicons-star-filled" style="font-size: 40px; color: #2271b1;"></span></div>',
                'icon-box': `
                    <div class="nexus-icon-box">
                        <div class="icon-box-icon"><span class="dashicons dashicons-star-filled"></span></div>
                        <h3 class="icon-box-title">Feature Title</h3>
                        <p class="icon-box-description">Feature description goes here</p>
                    </div>
                `,
                'counter': `
                    <div class="nexus-counter">
                        <div class="counter-number" data-count="100">0</div>
                        <div class="counter-title">Counter Title</div>
                    </div>
                `,
                'progress-bar': `
                    <div class="nexus-progress-bar">
                        <div class="progress-title">Skill Name</div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: 75%; background-color: #2271b1;">
                                <span class="progress-percentage">75%</span>
                            </div>
                        </div>
                    </div>
                `
            };
            
            return defaults[type] || `<p>Widget: ${type}</p>`;
        },

        /**
         * Handle widget actions
         */
        handleWidgetAction: function(action, $widget) {
            switch(action) {
                case 'edit':
                    this.editWidget($widget);
                    break;
                case 'duplicate':
                    this.duplicateWidget($widget);
                    break;
                case 'delete':
                    this.deleteWidget($widget);
                    break;
            }
        },

        /**
         * Edit widget
         */
        editWidget: function($widget) {
            const widgetType = $widget.data('widget-type');
            const widgetId = $widget.data('widget-id');
            
            // Highlight selected widget
            $('.nexus-widget').removeClass('is-selected');
            $widget.addClass('is-selected');
            
            // Load settings panel
            this.loadWidgetSettings(widgetType, $widget);
        },

        /**
         * Load widget settings panel
         */
        loadWidgetSettings: function(type, $widget) {
            const $panel = $('#widget-settings');
            
            // Clear current settings
            $panel.empty();
            
            // Add settings based on widget type
            const settings = this.getWidgetSettings(type, $widget);
            $panel.html(settings);
            
            // Bind setting changes
            $panel.find('.settings-input, .settings-select').on('change', function() {
                const setting = $(this).data('setting');
                const value = $(this).val();
                // Update widget
                // This would update the widget's appearance in real-time
            });
        },

        /**
         * Get widget settings HTML
         */
        getWidgetSettings: function(type, $widget) {
            let html = `<h4 style="margin-top: 0;">Edit ${type}</h4>`;
            
            // Common settings for different widget types
            if (type === 'heading') {
                html += `
                    <div class="settings-group">
                        <label class="settings-label">Text</label>
                        <input type="text" class="settings-input" data-setting="text" value="Your Heading">
                    </div>
                    <div class="settings-group">
                        <label class="settings-label">Tag</label>
                        <select class="settings-select" data-setting="tag">
                            <option value="h1">H1</option>
                            <option value="h2" selected>H2</option>
                            <option value="h3">H3</option>
                            <option value="h4">H4</option>
                        </select>
                    </div>
                    <div class="settings-group">
                        <label class="settings-label">Alignment</label>
                        <select class="settings-select" data-setting="align">
                            <option value="left" selected>Left</option>
                            <option value="center">Center</option>
                            <option value="right">Right</option>
                        </select>
                    </div>
                `;
            } else if (type === 'button') {
                html += `
                    <div class="settings-group">
                        <label class="settings-label">Button Text</label>
                        <input type="text" class="settings-input" data-setting="text" value="Click Here">
                    </div>
                    <div class="settings-group">
                        <label class="settings-label">Link URL</label>
                        <input type="url" class="settings-input" data-setting="url" value="#">
                    </div>
                    <div class="settings-group">
                        <label class="settings-label">Style</label>
                        <select class="settings-select" data-setting="style">
                            <option value="primary" selected>Primary</option>
                            <option value="secondary">Secondary</option>
                        </select>
                    </div>
                `;
            }
            
            return html;
        },

        /**
         * Duplicate widget
         */
        duplicateWidget: function($widget) {
            const $clone = $widget.clone();
            $clone.attr('data-widget-id', 'widget-' + Date.now());
            $widget.after($clone);
            this.addToHistory();
            this.showNotification('Widget duplicated', 'success');
        },

        /**
         * Delete widget
         */
        deleteWidget: function($widget) {
            if (confirm(nexusBuilder.i18n.confirmDelete)) {
                $widget.fadeOut(300, function() {
                    $(this).remove();
                });
                this.addToHistory();
                this.showNotification('Widget deleted', 'success');
            }
        },

        /**
         * Handle section actions
         */
        handleSectionAction: function(action, $section) {
            switch(action) {
                case 'edit':
                    this.editSection($section);
                    break;
                case 'duplicate':
                    this.duplicateSection($section);
                    break;
                case 'delete':
                    this.deleteSection($section);
                    break;
            }
        },

        /**
         * Switch device view
         */
        switchDevice: function(device) {
            $('.device-btn').removeClass('active');
            $(`.device-btn[data-device="${device}"]`).addClass('active');
            
            $('.nexus-builder-canvas').removeClass('device-desktop device-tablet device-mobile');
            $('.nexus-builder-canvas').addClass('device-' + device);
        },

        /**
         * Filter widgets
         */
        filterWidgets: function(search) {
            $('.widget-item').each(function() {
                const name = $(this).find('.widget-name').text().toLowerCase();
                if (name.indexOf(search) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        },

        /**
         * Save content
         */
        saveContent: function() {
            const self = this;
            const $button = $('#nexus-save-builder');
            const buttonText = $button.html();
            
            // Get builder data
            const data = this.getBuilderData();
            
            // Update button state
            $button.prop('disabled', true).html('<span class="dashicons dashicons-update"></span> ' + nexusBuilder.i18n.saving);
            
            // AJAX save
            $.ajax({
                url: nexusBuilder.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'nexus_save_builder_content',
                    nonce: nexusBuilder.nonce,
                    post_id: this.postId,
                    data: JSON.stringify(data),
                    content: this.getRenderedContent()
                },
                success: function(response) {
                    if (response.success) {
                        self.showNotification(nexusBuilder.i18n.saved, 'success');
                        $button.html('<span class="dashicons dashicons-yes"></span> ' + nexusBuilder.i18n.saved);
                        
                        setTimeout(function() {
                            $button.html(buttonText);
                        }, 2000);
                    } else {
                        self.showNotification(response.data.message || nexusBuilder.i18n.error, 'error');
                    }
                },
                error: function() {
                    self.showNotification(nexusBuilder.i18n.error, 'error');
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        },

        /**
         * Load content
         */
        loadContent: function() {
            const self = this;
            
            $.ajax({
                url: nexusBuilder.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'nexus_load_builder_content',
                    nonce: nexusBuilder.nonce,
                    post_id: this.postId
                },
                success: function(response) {
                    if (response.success && response.data.data) {
                        self.renderBuilderData(response.data.data);
                    }
                }
            });
        },

        /**
         * Get builder data
         */
        getBuilderData: function() {
            const data = {
                sections: []
            };
            
            $('#nexus-canvas .nexus-section').each(function() {
                const section = {
                    id: $(this).data('section-id'),
                    columns: []
                };
                
                $(this).find('.nexus-column').each(function() {
                    const column = {
                        id: $(this).data('column-id'),
                        width: $(this).css('width'),
                        widgets: []
                    };
                    
                    $(this).find('.nexus-widget').each(function() {
                        const widget = {
                            id: $(this).data('widget-id'),
                            type: $(this).data('widget-type'),
                            settings: {}
                        };
                        column.widgets.push(widget);
                    });
                    
                    section.columns.push(column);
                });
                
                data.sections.push(section);
            });
            
            return data;
        },

        /**
         * Get rendered content (for fallback)
         */
        getRenderedContent: function() {
            return $('#nexus-canvas').html();
        },

        /**
         * Add to history
         */
        addToHistory: function() {
            const data = this.getBuilderData();
            
            // Remove future history if we're not at the end
            if (this.historyIndex < this.history.length - 1) {
                this.history = this.history.slice(0, this.historyIndex + 1);
            }
            
            // Add new state
            this.history.push(JSON.stringify(data));
            this.historyIndex++;
            
            // Limit history size
            if (this.history.length > this.maxHistory) {
                this.history.shift();
                this.historyIndex--;
            }
            
            this.updateHistoryButtons();
        },

        /**
         * Undo
         */
        undo: function() {
            if (this.historyIndex > 0) {
                this.historyIndex--;
                const data = JSON.parse(this.history[this.historyIndex]);
                this.renderBuilderData(data);
                this.updateHistoryButtons();
            }
        },

        /**
         * Redo
         */
        redo: function() {
            if (this.historyIndex < this.history.length - 1) {
                this.historyIndex++;
                const data = JSON.parse(this.history[this.historyIndex]);
                this.renderBuilderData(data);
                this.updateHistoryButtons();
            }
        },

        /**
         * Update history buttons state
         */
        updateHistoryButtons: function() {
            $('#nexus-history-undo').prop('disabled', this.historyIndex <= 0);
            $('#nexus-history-redo').prop('disabled', this.historyIndex >= this.history.length - 1);
        },

        /**
         * Render builder data
         */
        renderBuilderData: function(data) {
            // Implementation would rebuild the canvas from data
            // For now, just reinitialize drag/drop
            this.initDragDrop();
        },

        /**
         * Show notification
         */
        showNotification: function(message, type) {
            const $notification = $(`
                <div class="builder-notification builder-notification-${type}">
                    ${message}
                </div>
            `);
            
            $('body').append($notification);
            
            setTimeout(function() {
                $notification.addClass('show');
            }, 100);
            
            setTimeout(function() {
                $notification.removeClass('show');
                setTimeout(function() {
                    $notification.remove();
                }, 300);
            }, 3000);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        NexusBuilder.init();
    });

})(jQuery);
