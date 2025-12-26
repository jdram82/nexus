/**
 * Forms JavaScript
 * Nexus Pro - Form Validation & Submission
 */

(function($) {
    'use strict';

    const NexusForms = {
        /**
         * Initialize
         */
        init: function() {
            this.formValidation();
            this.formSubmission();
            this.fileUpload();
            this.characterCount();
        },

        /**
         * Form Validation
         */
        formValidation: function() {
            $('.nexus-form').each(function() {
                const form = $(this);

                // Real-time validation
                form.find('input, textarea, select').on('blur', function() {
                    NexusForms.validateField($(this));
                });

                // Clear error on focus
                form.find('input, textarea, select').on('focus', function() {
                    $(this).closest('.form-field').removeClass('has-error');
                    $(this).siblings('.field-error').hide();
                });

                // Submit validation
                form.on('submit', function(e) {
                    if (!NexusForms.validateForm($(this))) {
                        e.preventDefault();
                        return false;
                    }
                });
            });
        },

        /**
         * Validate Single Field
         */
        validateField: function(field) {
            const fieldWrapper = field.closest('.form-field');
            const fieldType = field.attr('type');
            const fieldValue = field.val().trim();
            const isRequired = field.prop('required');
            let isValid = true;
            let errorMessage = '';

            // Required validation
            if (isRequired && !fieldValue) {
                isValid = false;
                errorMessage = 'This field is required';
            }

            // Email validation
            if (fieldType === 'email' && fieldValue) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(fieldValue)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid email address';
                }
            }

            // Phone validation
            if (fieldType === 'tel' && fieldValue) {
                const phoneRegex = /^[+]?[(]?[0-9]{3}[)]?[-\s.]?[0-9]{3}[-\s.]?[0-9]{4,6}$/;
                if (!phoneRegex.test(fieldValue.replace(/\s/g, ''))) {
                    isValid = false;
                    errorMessage = 'Please enter a valid phone number';
                }
            }

            // URL validation
            if (fieldType === 'url' && fieldValue) {
                try {
                    new URL(fieldValue);
                } catch (_) {
                    isValid = false;
                    errorMessage = 'Please enter a valid URL';
                }
            }

            // Display validation result
            if (!isValid) {
                fieldWrapper.addClass('has-error').removeClass('has-success');
                fieldWrapper.find('.field-error').remove();
                field.after('<span class="field-error">' + errorMessage + '</span>');
            } else if (fieldValue) {
                fieldWrapper.addClass('has-success').removeClass('has-error');
                fieldWrapper.find('.field-error').remove();
            } else {
                fieldWrapper.removeClass('has-error has-success');
            }

            return isValid;
        },

        /**
         * Validate Entire Form
         */
        validateForm: function(form) {
            let isValid = true;
            const fields = form.find('input[required], textarea[required], select[required]');

            fields.each(function() {
                if (!NexusForms.validateField($(this))) {
                    isValid = false;
                }
            });

            if (!isValid) {
                // Focus first error field
                const firstError = form.find('.has-error').first();
                if (firstError.length) {
                    $('html, body').animate({
                        scrollTop: firstError.offset().top - 100
                    }, 300);
                    firstError.find('input, textarea, select').focus();
                }
            }

            return isValid;
        },

        /**
         * Form Submission (AJAX)
         */
        formSubmission: function() {
            $('.nexus-form').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                const formData = new FormData(this);

                // Add action and nonce
                formData.append('action', 'nexus_submit_form');
                formData.append('nonce', nexusFormsData.nonce);

                // Disable submit button
                submitBtn.prop('disabled', true).text('Submitting...');
                form.addClass('form-loading');

                // Remove previous messages
                form.find('.form-message').remove();

                $.ajax({
                    url: nexusFormsData.ajaxurl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            form.prepend(
                                '<div class="form-message success">' + response.data.message + '</div>'
                            );

                            // Reset form
                            form[0].reset();
                            form.find('.form-field').removeClass('has-success has-error');

                            // Scroll to message
                            $('html, body').animate({
                                scrollTop: form.offset().top - 100
                            }, 300);

                            // Trigger custom event
                            form.trigger('nexusFormSuccess', [response.data]);
                        } else {
                            // Show error message
                            form.prepend(
                                '<div class="form-message error">' + response.data.message + '</div>'
                            );

                            // Display field errors
                            if (response.data.errors) {
                                $.each(response.data.errors, function(fieldName, errorMsg) {
                                    const field = form.find('[name="' + fieldName + '"]');
                                    const fieldWrapper = field.closest('.form-field');
                                    fieldWrapper.addClass('has-error');
                                    field.after('<span class="field-error">' + errorMsg + '</span>');
                                });
                            }
                        }
                    },
                    error: function() {
                        form.prepend(
                            '<div class="form-message error">An error occurred. Please try again.</div>'
                        );
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text(submitBtn.data('text') || 'Submit');
                        form.removeClass('form-loading');
                    }
                });
            });
        },

        /**
         * File Upload Handler
         */
        fileUpload: function() {
            $('input[type="file"]').on('change', function() {
                const files = this.files;
                const wrapper = $(this).closest('.file-upload-wrapper');
                let fileInfo = wrapper.find('.file-info');

                if (!fileInfo.length) {
                    fileInfo = $('<div class="file-info"></div>');
                    wrapper.append(fileInfo);
                }

                if (files.length > 0) {
                    let info = files.length + ' file(s) selected: ';
                    for (let i = 0; i < files.length; i++) {
                        info += files[i].name;
                        if (i < files.length - 1) info += ', ';
                    }
                    fileInfo.text(info);
                } else {
                    fileInfo.text('');
                }
            });
        },

        /**
         * Character Count for Textareas
         */
        characterCount: function() {
            $('textarea[maxlength]').each(function() {
                const maxLength = $(this).attr('maxlength');
                const counter = $('<div class="character-count">0 / ' + maxLength + '</div>');
                $(this).after(counter);

                $(this).on('input', function() {
                    const currentLength = $(this).val().length;
                    counter.text(currentLength + ' / ' + maxLength);

                    if (currentLength >= maxLength * 0.9) {
                        counter.css('color', '#dc3545');
                    } else {
                        counter.css('color', '');
                    }
                });
            });
        }
    };

    /**
     * Initialize on Document Ready
     */
    $(document).ready(function() {
        NexusForms.init();
    });

    // Make accessible globally
    window.NexusForms = NexusForms;

})(jQuery);