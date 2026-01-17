/* UL-NEC Frontend JavaScript */

jQuery(document).ready(function($) {
    // Add frontend interactivity here
    
    // Example: Copy license key
    $('.ulnec-copy-license').on('click', function(e) {
        e.preventDefault();
        var licenseKey = $(this).data('license');
        navigator.clipboard.writeText(licenseKey);
        alert('License key copied to clipboard!');
    });
});
