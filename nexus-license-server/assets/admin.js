/**
 * Nexus License Server - Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        /**
         * Generate License Form Submit
         */
        $('#nls-generate-form').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $button = $form.find('button[type="submit"]');
            const originalText = $button.html();
            
            // Get form data
            const formData = {
                action: 'nls_generate_license',
                nonce: nlsData.nonce,
                tier: $('#tier').val(),
                customer_name: $('#customer_name').val(),
                customer_email: $('#customer_email').val(),
                max_activations: $('#max_activations').val(),
                expires_at: $('#expires_at').val() || null
            };
            
            // Show loading state
            $button.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Generating...');
            
            // AJAX request
            $.post(nlsData.ajaxUrl, formData, function(response) {
                if (response.success) {
                    // Show success message
                    $('#nls-new-license-key').text(response.data.license_key);
                    $('#nls-generated-license').slideDown();
                    
                    // Reset form
                    $form[0].reset();
                    $('#expires_at').val(getDefaultExpiryDate());
                    
                    // Refresh table
                    refreshLicensesTable();
                    
                    // Hide message after 10 seconds
                    setTimeout(function() {
                        $('#nls-generated-license').slideUp();
                    }, 10000);
                } else {
                    alert('Error: ' + (response.data.message || 'Failed to generate license'));
                }
            }).fail(function() {
                alert('Network error. Please try again.');
            }).always(function() {
                $button.prop('disabled', false).html(originalText);
            });
        });
        
        /**
         * Copy License Key to Clipboard
         */
        $(document).on('click', '#nls-copy-license, .nls-copy-btn', function(e) {
            e.preventDefault();
            
            let text;
            if ($(this).attr('id') === 'nls-copy-license') {
                text = $('#nls-new-license-key').text();
            } else {
                text = $(this).data('key');
            }
            
            copyToClipboard(text);
            
            // Show feedback
            const $btn = $(this);
            const originalText = $btn.html();
            $btn.html('<span class="dashicons dashicons-yes"></span> Copied!');
            setTimeout(function() {
                $btn.html(originalText);
            }, 2000);
        });
        
        /**
         * Toggle License Status
         */
        $(document).on('click', '.nls-toggle-status', function() {
            const $btn = $(this);
            const licenseId = $btn.data('id');
            const currentStatus = $btn.data('status');
            const newStatus = currentStatus === 'active' ? 'suspended' : 'active';
            
            if (!confirm('Are you sure you want to ' + (newStatus === 'active' ? 'activate' : 'suspend') + ' this license?')) {
                return;
            }
            
            const originalText = $btn.text();
            $btn.prop('disabled', true).text('Updating...');
            
            $.post(nlsData.ajaxUrl, {
                action: 'nls_update_license',
                nonce: nlsData.nonce,
                license_id: licenseId,
                status: newStatus
            }, function(response) {
                if (response.success) {
                    // Update UI
                    $btn.data('status', newStatus);
                    $btn.text(newStatus === 'active' ? 'Suspend' : 'Activate');
                    
                    const $statusBadge = $btn.closest('tr').find('.status-badge');
                    $statusBadge
                        .removeClass('status-active status-suspended status-inactive')
                        .addClass('status-' + newStatus)
                        .text(newStatus);
                    
                    // Refresh stats
                    refreshLicensesTable();
                } else {
                    alert('Error: ' + (response.data.message || 'Failed to update license'));
                }
            }).fail(function() {
                alert('Network error. Please try again.');
            }).always(function() {
                $btn.prop('disabled', false);
            });
        });
        
        /**
         * Delete License
         */
        $(document).on('click', '.nls-delete', function() {
            const $btn = $(this);
            const licenseId = $btn.data('id');
            
            if (!confirm('Are you sure you want to delete this license? This action cannot be undone.')) {
                return;
            }
            
            const $row = $btn.closest('tr');
            $row.addClass('nls-loading');
            
            $.post(nlsData.ajaxUrl, {
                action: 'nls_delete_license',
                nonce: nlsData.nonce,
                license_id: licenseId
            }, function(response) {
                if (response.success) {
                    $row.fadeOut(300, function() {
                        $(this).remove();
                    });
                    
                    // Refresh stats
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    alert('Error: ' + (response.data.message || 'Failed to delete license'));
                    $row.removeClass('nls-loading');
                }
            }).fail(function() {
                alert('Network error. Please try again.');
                $row.removeClass('nls-loading');
            });
        });
        
        /**
         * Refresh Licenses Table
         */
        $('#nls-refresh').on('click', function() {
            location.reload();
        });
        
        /**
         * Search Licenses
         */
        let searchTimeout;
        $('#nls-search').on('keyup', function() {
            clearTimeout(searchTimeout);
            const searchTerm = $(this).val().toLowerCase();
            
            searchTimeout = setTimeout(function() {
                $('#nls-licenses-tbody tr').each(function() {
                    const $row = $(this);
                    const text = $row.text().toLowerCase();
                    
                    if (text.indexOf(searchTerm) > -1) {
                        $row.show();
                    } else {
                        $row.hide();
                    }
                });
            }, 300);
        });
        
        /**
         * Helper Functions
         */
        
        function copyToClipboard(text) {
            // Try modern clipboard API first
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).catch(function() {
                    fallbackCopy(text);
                });
            } else {
                fallbackCopy(text);
            }
        }
        
        function fallbackCopy(text) {
            const $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
        }
        
        function refreshLicensesTable() {
            // Simple reload for now - could be made more sophisticated with AJAX
            setTimeout(function() {
                location.reload();
            }, 1000);
        }
        
        function getDefaultExpiryDate() {
            const date = new Date();
            date.setFullYear(date.getFullYear() + 1);
            return date.toISOString().split('T')[0];
        }
        
    });
    
})(jQuery);

/**
 * CSS for spinning icon
 */
const style = document.createElement('style');
style.innerHTML = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .spin {
        animation: spin 1s linear infinite;
        display: inline-block;
    }
`;
document.head.appendChild(style);
