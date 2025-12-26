/**
 * Performance Monitor - JavaScript
 * @package Nexus_Pro
 * @since 3.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        $('#nexus-run-performance-test').on('click', function() {
            runPerformanceTest();
        });
    });

    function runPerformanceTest() {
        var $button = $('#nexus-run-performance-test');
        $button.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Running...');

        $.ajax({
            url: nexusPerformance.ajaxUrl,
            type: 'POST',
            data: {action: 'nexus_run_performance_test', nonce: nexusPerformance.nonce},
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error running test');
                    $button.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Run Performance Test');
                }
            },
            error: function() {
                alert('Error running test');
                $button.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Run Performance Test');
            }
        });
    }

})(jQuery);
