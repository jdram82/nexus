/**
 * Form Builder JavaScript
 * Nexus Pro - Drag & Drop Form Builder
 */

(function($) {
    'use strict';

    const NexusFormBuilder = {
        currentField: null,

        /**
         * Initialize
         */
        init: function() {
            this.initSortable();
            this.fieldTypeHandlers();
            this.fieldSettingsHandlers();
            this.fieldActions();
            this.formSettingsTabs();
            this.saveForm();
        },

        /**
         * Initialize Sortable (Drag & Drop)
         */
        initSortable: function() {
            const self = this;

            // Make field types draggable
            $('.field-type-list').sortable({
                connectWith: '.builder-canvas',
                helper: 'clone',
                revert: 'invalid',
                placeholder: 'field-placeholder',
                start: function(e, ui) {
                    ui.placeholder.height(ui.item.height());
                }
            });

            // Make canvas sortable
            $('.builder-canvas').sortable({
                items: '.form-field-builder',
                handle: '.field-header',
                placeholder: 'field-placeholder',
                tolerance: 'pointer',
                update: function() {
                    self.updateFieldOrder();
                },
                receive: function(e, ui) {
                    const fieldType = ui.item.data('type');
                    self.addField(fieldType, ui.item);
                }
            }).droppable({
                accept: '.field-type-item',
                hoverClass: 'drag-over',
                drop: function(e, ui) {
                    // Handled by sortable receive
                }
            });
        },

        /**
         * Add Field to Canvas
         */
        addField: function(fieldType, placeholder) {
            const fieldId = 'field_' + Date.now();
            const fieldHTML = this.getFieldHTML(fieldType, fieldId);

            placeholder.replaceWith(fieldHTML);
            this.updateFieldOrder();
            $('.builder-canvas').removeClass('empty');
        },

        /**
         * Get Field HTML
         */
        getFieldHTML: function(type, id) {
            const templates = {
                text: this.createFieldTemplate(id, 'text', 'Text Input', '<input type="text" placeholder="Enter text">'),
                email: this.createFieldTemplate(id, 'email', 'Email', '<input type="email" placeholder="email@example.com">'),
                tel: this.createFieldTemplate(id, 'tel', 'Phone', '<input type="tel" placeholder="(123) 456-7890">'),
                textarea: this.createFieldTemplate(id, 'textarea', 'Textarea', '<textarea placeholder="Enter your message"></textarea>'),
                select: this.createFieldTemplate(id, 'select', 'Select', '<select><option>Option 1</option></select>'),
                radio: this.createFieldTemplate(id, 'radio', 'Radio Buttons', '<div class="radio-group"><label><input type="radio" name="radio"> Option 1</label></div>'),
                checkbox: this.createFieldTemplate(id, 'checkbox', 'Checkboxes', '<div class="checkbox-group"><label><input type="checkbox"> Option 1</label></div>'),
                file: this.createFieldTemplate(id, 'file', 'File Upload', '<input type="file">')
            };

            return templates[type] || templates.text;
        },

        /**
         * Create Field Template
         */
        createFieldTemplate: function(id, type, label, preview) {
            return `
                <div class="form-field-builder" data-field-id="${id}" data-field-type="${type}">
                    <div class="field-header">
                        <span class="field-type-icon">
                            <span class="dashicons dashicons-editor-${type === 'textarea' ? 'paragraph' : 'textcolor'}"></span>
                            ${label}
                        </span>
                        <div class="field-actions">
                            <button type="button" class="edit-field" title="Edit">
                                <span class="dashicons dashicons-edit"></span>
                            </button>
                            <button type="button" class="duplicate-field" title="Duplicate">
                                <span class="dashicons dashicons-admin-page"></span>
                            </button>
                            <button type="button" class="delete-field delete" title="Delete">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </div>
                    </div>
                    <div class="field-preview">
                        <label>${label}</label>
                        ${preview}
                    </div>
                </div>
            `;
        },

        /**
         * Field Type Handlers
         */
        fieldTypeHandlers: function() {
            // Click to add field
            $('.field-type-item').on('click', function() {
                const fieldType = $(this).data('type');
                const fieldId = 'field_' + Date.now();
                const fieldHTML = NexusFormBuilder.getFieldHTML(fieldType, fieldId);
                
                $('.builder-canvas').append(fieldHTML).removeClass('empty');
                NexusFormBuilder.updateFieldOrder();
            });
        },

        /**
         * Field Actions
         */
        fieldActions: function() {
            const self = this;

            // Edit field
            $(document).on('click', '.edit-field', function() {
                const field = $(this).closest('.form-field-builder');
                self.editField(field);
            });

            // Duplicate field
            $(document).on('click', '.duplicate-field', function() {
                const field = $(this).closest('.form-field-builder');
                const clone = field.clone();
                const newId = 'field_' + Date.now();
                clone.attr('data-field-id', newId);
                clone.insertAfter(field);
                self.updateFieldOrder();
            });

            // Delete field
            $(document).on('click', '.delete-field', function() {
                if (confirm('Are you sure you want to delete this field?')) {
                    $(this).closest('.form-field-builder').remove();
                    self.updateFieldOrder();
                    
                    if (!$('.builder-canvas .form-field-builder').length) {
                        $('.builder-canvas').addClass('empty');
                    }
                }
            });
        },

        /**
         * Edit Field
         */
        editField: function(field) {
            this.currentField = field;
            const fieldType = field.data('field-type');
            const fieldId = field.data('field-id');

            // Highlight active field
            $('.form-field-builder').removeClass('active');
            field.addClass('active');

            // Load settings
            this.loadFieldSettings(fieldType, fieldId);
        },

        /**
         * Load Field Settings
         */
        loadFieldSettings: function(fieldType, fieldId) {
            const settings = $('.builder-settings');
            settings.removeClass('no-field');

            // Build settings form
            let html = '<h4>Field Settings</h4>';
            html += '<div class="settings-group">';
            html += '<label>Field Label</label>';
            html += '<input type="text" name="label" value="" placeholder="Enter field label">';
            html += '</div>';

            html += '<div class="settings-group">';
            html += '<label>Field Name</label>';
            html += '<input type="text" name="name" value="' + fieldId + '" placeholder="field_name">';
            html += '</div>';

            html += '<div class="settings-group">';
            html += '<label>Placeholder</label>';
            html += '<input type="text" name="placeholder" value="" placeholder="Placeholder text">';
            html += '</div>';

            html += '<div class="settings-group">';
            html += '<div class="checkbox-wrapper">';
            html += '<input type="checkbox" name="required" id="field-required">';
            html += '<label for="field-required">Required Field</label>';
            html += '</div>';
            html += '</div>';

            // Add options editor for select/radio/checkbox
            if (['select', 'radio', 'checkbox'].includes(fieldType)) {
                html += '<div class="settings-group">';
                html += '<label>Options</label>';
                html += '<div class="field-options-editor">';
                html += '<div class="option-item">';
                html += '<input type="text" placeholder="Option 1" value="Option 1">';
                html += '<button type="button" class="remove-option">×</button>';
                html += '</div>';
                html += '</div>';
                html += '<button type="button" class="add-option-btn">+ Add Option</button>';
                html += '</div>';
            }

            settings.html(html);
        },

        /**
         * Field Settings Handlers
         */
        fieldSettingsHandlers: function() {
            const self = this;

            // Update field on setting change
            $(document).on('input change', '.builder-settings input, .builder-settings select, .builder-settings textarea', function() {
                if (self.currentField) {
                    self.updateFieldPreview();
                }
            });

            // Add option
            $(document).on('click', '.add-option-btn', function() {
                const editor = $(this).prev('.field-options-editor');
                const optionNum = editor.find('.option-item').length + 1;
                editor.append(
                    '<div class="option-item">' +
                    '<input type="text" placeholder="Option ' + optionNum + '">' +
                    '<button type="button" class="remove-option">×</button>' +
                    '</div>'
                );
            });

            // Remove option
            $(document).on('click', '.remove-option', function() {
                $(this).closest('.option-item').remove();
            });
        },

        /**
         * Update Field Preview
         */
        updateFieldPreview: function() {
            if (!this.currentField) return;

            const label = $('.builder-settings input[name="label"]').val();
            const placeholder = $('.builder-settings input[name="placeholder"]').val();

            // Update label
            this.currentField.find('.field-preview label').text(label || 'Field Label');

            // Update placeholder
            const input = this.currentField.find('.field-preview input, .field-preview textarea');
            if (placeholder) {
                input.attr('placeholder', placeholder);
            }
        },

        /**
         * Update Field Order
         */
        updateFieldOrder: function() {
            const fields = [];
            $('.builder-canvas .form-field-builder').each(function(index) {
                fields.push({
                    id: $(this).data('field-id'),
                    type: $(this).data('field-type'),
                    order: index
                });
            });
            // Store in hidden field or trigger save
            console.log('Field order updated:', fields);
        },

        /**
         * Form Settings Tabs
         */
        formSettingsTabs: function() {
            $('.form-settings-tabs button').on('click', function() {
                $('.form-settings-tabs button').removeClass('active');
                $(this).addClass('active');

                const tab = $(this).data('tab');
                $('.settings-tab-content').removeClass('active');
                $('.settings-tab-content[data-tab="' + tab + '"]').addClass('active');
            });
        },

        /**
         * Save Form
         */
        saveForm: function() {
            $('.save-form-btn').on('click', function() {
                const formData = NexusFormBuilder.getFormData();
                
                // AJAX save
                $.post(ajaxurl, {
                    action: 'nexus_save_form',
                    nonce: nexusFormBuilder.nonce,
                    form_id: $('#post_ID').val(),
                    form_data: JSON.stringify(formData)
                }, function(response) {
                    if (response.success) {
                        alert('Form saved successfully!');
                    }
                });
            });
        },

        /**
         * Get Form Data
         */
        getFormData: function() {
            const fields = [];
            $('.builder-canvas .form-field-builder').each(function() {
                fields.push({
                    id: $(this).data('field-id'),
                    type: $(this).data('field-type'),
                    label: $(this).find('.field-preview label').text(),
                    required: false // Get from settings
                });
            });
            return { fields: fields };
        }
    };

    /**
     * Initialize on Document Ready
     */
    $(document).ready(function() {
        if ($('.nexus-form-builder').length) {
            NexusFormBuilder.init();
        }
    });

})(jQuery);