/**
 * A/B Testing - Frontend JavaScript
 * 
 * @package Nexus_Pro
 * @since 3.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        if (typeof nexusABTests === 'undefined' || !nexusABTests.tests) {
            return;
        }

        // Track conversions for click goals
        $(document).on('click', '.nexus-ab-test a, .nexus-ab-test button', function() {
            var $test = $(this).closest('.nexus-ab-test');
            var testId = $test.data('test-id');
            var variant = $test.data('variant');

            if (testId && variant) {
                trackConversion(testId, variant);
            }
        });

        // Track time on page goals
        var timeTracking = {};
        $('.nexus-ab-test').each(function() {
            var testId = $(this).data('test-id');
            if (testId) {
                timeTracking[testId] = Date.now();
            }
        });

        // Track on page unload
        $(window).on('beforeunload', function() {
            $.each(timeTracking, function(testId, startTime) {
                var timeSpent = (Date.now() - startTime) / 1000;
                
                // If user spent more than 30 seconds, count as conversion
                if (timeSpent > 30) {
                    var $test = $('.nexus-ab-test[data-test-id="' + testId + '"]');
                    var variant = $test.data('variant');
                    
                    if (variant) {
                        trackConversion(testId, variant, false);
                    }
                }
            });
        });

    });

    /**
     * Track conversion
     */
    function trackConversion(testId, variant, async = true) {
        $.ajax({
            url: nexusABTests.ajaxUrl,
            type: 'POST',
            async: async,
            data: {
                action: 'nexus_ab_track_conversion',
                nonce: nexusABTests.nonce,
                test_id: testId,
                variant: variant
            }
        });
    }

})(jQuery);
