/* UL-NEC Admin JavaScript */

jQuery(document).ready(function($) {
    // Test Supabase connection
    $('#ulnec-test-connection').on('click', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: ulnecAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'ulnec_test_connection',
                nonce: ulnecAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                }
            }
        });
    });
});
