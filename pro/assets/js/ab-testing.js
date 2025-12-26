/**
 * A/B Testing - Admin JavaScript
 * 
 * @package Nexus_Pro
 * @since 3.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Open modal
        $(document).on('click', '#nexus-create-ab-test, .nexus-create-ab-test-trigger', function(e) {
            e.preventDefault();
            $('#nexus-ab-test-modal').fadeIn(300);
        });

        // Close modal
        $(document).on('click', '.nexus-modal-close', function() {
            $('#nexus-ab-test-modal').fadeOut(300);
        });

        // Close modal on outside click
        $(document).on('click', '.nexus-modal', function(e) {
            if (e.target === this) {
                $(this).fadeOut(300);
            }
        });

        // Create test
        $('#nexus-ab-test-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $button = $form.find('button[type="submit"]');
            var buttonText = $button.text();

            $button.prop('disabled', true).text(nexusABTesting.i18n.creating || 'Creating...');

            var formData = {
                action: 'nexus_create_ab_test',
                nonce: nexusABTesting.nonce,
                test_name: $('#test_name').val(),
                test_type: $('#test_type').val(),
                variant_a: tinyMCE.get('variant_a') ? tinyMCE.get('variant_a').getContent() : $('#variant_a').val(),
                variant_b: tinyMCE.get('variant_b') ? tinyMCE.get('variant_b').getContent() : $('#variant_b').val(),
                goal_type: $('#goal_type').val()
            };

            $.ajax({
                url: nexusABTesting.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(response.data.message || nexusABTesting.i18n.error);
                        $button.prop('disabled', false).text(buttonText);
                    }
                },
                error: function() {
                    alert(nexusABTesting.i18n.error);
                    $button.prop('disabled', false).text(buttonText);
                }
            });
        });

        // End test
        $(document).on('click', '.nexus-end-test', function(e) {
            e.preventDefault();

            if (!confirm(nexusABTesting.i18n.confirmEnd)) {
                return;
            }

            var $button = $(this);
            var testId = $button.data('test-id');
            var buttonText = $button.text();

            $button.prop('disabled', true).text(nexusABTesting.i18n.ending || 'Ending...');

            $.ajax({
                url: nexusABTesting.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'nexus_end_ab_test',
                    nonce: nexusABTesting.nonce,
                    test_id: testId
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(response.data.message || nexusABTesting.i18n.error);
                        $button.prop('disabled', false).text(buttonText);
                    }
                },
                error: function() {
                    alert(nexusABTesting.i18n.error);
                    $button.prop('disabled', false).text(buttonText);
                }
            });
        });

    });

})(jQuery);
