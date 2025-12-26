/**
 * SEO Tools - JavaScript
 * @package Nexus_Pro
 * @since 3.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Character count for SEO fields
        $('#nexus_seo_title').on('input', function() {
            var length = $(this).val().length;
            var status = length >= 50 && length <= 60 ? 'good' : 'warning';
            $(this).next('.description').html('Characters: ' + length + ' (recommended: 50-60)').css('color', status === 'good' ? '#155724' : '#856404');
        });

        $('#nexus_seo_description').on('input', function() {
            var length = $(this).val().length;
            var status = length >= 150 && length <= 160 ? 'good' : 'warning';
            $(this).next('.description').html('Characters: ' + length + ' (recommended: 150-160)').css('color', status === 'good' ? '#155724' : '#856404');
        });

        $('#nexus-regenerate-sitemap').on('click', function() {
            if (confirm('Regenerate sitemap?')) {
                location.href = location.href + '&regenerate=1';
            }
        });
    });

})(jQuery);
