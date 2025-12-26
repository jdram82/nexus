/**
 * Admin JavaScript
 * Nexus Pro - WordPress Admin Interface
 */

(function($) {
    'use strict';

    const NexusProAdmin = {
        /**
         * Initialize
         */
        init: function() {
            this.toggleSwitches();
            this.submissionModal();
            this.licenseForm();
            this.settingsTabs();
            this.colorPickers();
            this.confirmDialogs();
        },

        /**
         * Toggle Switches
         */
        toggleSwitches: function() {
            $('.nexus-toggle input').on('change', function() {
                const feature = $(this).data('feature');
                const enabled = $(this).is(':checked');

                // Save via AJAX
                $.post(ajaxurl, {
                    action: 'nexus_toggle_feature',
                    nonce: nexusProAdmin.nonce,
                    feature: feature,
                    enabled: enabled ? 1 : 0
                }, function(response) {
                    if (response.success) {
                        NexusProAdmin.showNotice(
                            feature + ' ' + (enabled ? 'enabled' : 'disabled'),
                            'success'
                        );
                    }
                });
            });
        },

        /**
         * Submission Modal
         */
        submissionModal: function() {
            // Open modal
            $('.view-submission').on('click', function(e) {
                e.preventDefault();
                const submissionId = $(this).data('submission-id');
                
                NexusProAdmin.loadSubmission(submissionId);
            });

            // Close modal
            $('.nexus-modal-close, .nexus-modal-overlay').on('click', function(e) {
                if (e.target === this) {
                    $('.nexus-modal-overlay').removeClass('active');
                }
            });

            // Close on ESC key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $('.nexus-modal-overlay').removeClass('active');
                }
            });
        },

        /**
         * Load Submission Data
         */
        loadSubmission: function(submissionId) {
            const modal = $('.nexus-modal-overlay');
            const body = modal.find('.nexus-modal-body');

            body.html('<p>Loading...</p>');
            modal.addClass('active');

            $.post(ajaxurl, {
                action: 'nexus_get_submission',
                nonce: nexusProAdmin.nonce,
                submission_id: submissionId
            }, function(response) {
                if (response.success) {
                    let html = '<ul class="submission-data">';
                    
                    $.each(response.data.data, function(key, value) {
                        html += '<li><strong>' + key + '</strong><span>' + value + '</span></li>';
                    });
                    
                    html += '<li><strong>Submitted At</strong><span>' + response.data.created_at + '</span></li>';
                    html += '<li><strong>IP Address</strong><span>' + response.data.ip_address + '</span></li>';
                    html += '</ul>';
                    
                    body.html(html);
                } else {
                    body.html('<p>Error loading submission.</p>');
                }
            });
        },

        /**
         * License Form
         */
        licenseForm: function() {
            $('.nexus-license-form').on('submit', function() {
                const btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true).text('Processing...');
            });

            // Copy license key
            $('.copy-license-key').on('click', function() {
                const key = $(this).data('key');
                navigator.clipboard.writeText(key).then(function() {
                    NexusProAdmin.showNotice('License key copied!', 'success');
                });
            });
        },

        /**
         * Settings Tabs
         */
        settingsTabs: function() {
            $('.nav-tab-wrapper .nav-tab').on('click', function(e) {
                e.preventDefault();
                
                const tab = $(this).data('tab');
                
                // Update active tab
                $('.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                
                // Show corresponding content
                $('.settings-tab-content').hide();
                $('#tab-' + tab).show();
                
                // Update URL hash
                window.location.hash = tab;
            });

            // Activate tab from URL hash
            if (window.location.hash) {
                const tab = window.location.hash.substring(1);
                $('.nav-tab[data-tab="' + tab + '"]').click();
            }
        },

        /**
         * Color Pickers
         */
        colorPickers: function() {
            if ($.fn.wpColorPicker) {
                $('.color-picker').wpColorPicker();
            }
        },

        /**
         * Confirm Dialogs
         */
        confirmDialogs: function() {
            $('.needs-confirmation').on('click', function(e) {
                const message = $(this).data('confirm') || 'Are you sure?';
                if (!confirm(message)) {
                    e.preventDefault();
                    return false;
                }
            });
        },

        /**
         * Show Admin Notice
         */
        showNotice: function(message, type = 'info') {
            const notice = $(
                '<div class="notice notice-' + type + ' is-dismissible">' +
                '<p>' + message + '</p>' +
                '<button type="button" class="notice-dismiss">' +
                '<span class="screen-reader-text">Dismiss</span>' +
                '</button>' +
                '</div>'
            );

            $('.wrap h1').after(notice);

            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);

            // Manual dismiss
            notice.find('.notice-dismiss').on('click', function() {
                notice.fadeOut(function() {
                    $(this).remove();
                });
            });
        },

        /**
         * Export Data
         */
        exportData: function() {
            $('.export-data-btn').on('click', function(e) {
                e.preventDefault();
                
                const dataType = $(this).data('type');
                const btn = $(this);
                
                btn.text('Exporting...').prop('disabled', true);

                $.post(ajaxurl, {
                    action: 'nexus_export_data',
                    nonce: nexusProAdmin.nonce,
                    type: dataType
                }, function(response) {
                    if (response.success) {
                        // Trigger download
                        const blob = new Blob([response.data.content], { type: 'text/csv' });
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = response.data.filename;
                        link.click();
                        
                        NexusProAdmin.showNotice('Export completed!', 'success');
                    } else {
                        NexusProAdmin.showNotice('Export failed. Please try again.', 'error');
                    }
                }, 'json').always(function() {
                    btn.text('Export').prop('disabled', false);
                });
            });
        },

        /**
         * Bulk Actions
         */
        bulkActions: function() {
            $('.bulk-action-btn').on('click', function() {
                const action = $('#bulk-action-selector').val();
                const selected = [];

                $('.submission-checkbox:checked').each(function() {
                    selected.push($(this).val());
                });

                if (!selected.length) {
                    alert('Please select at least one item.');
                    return;
                }

                if (!confirm('Are you sure you want to ' + action + ' ' + selected.length + ' items?')) {
                    return;
                }

                $.post(ajaxurl, {
                    action: 'nexus_bulk_action',
                    nonce: nexusProAdmin.nonce,
                    bulk_action: action,
                    items: selected
                }, function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        NexusProAdmin.showNotice('Bulk action failed.', 'error');
                    }
                });
            });

            // Select all checkbox
            $('#select-all-submissions').on('change', function() {
                $('.submission-checkbox').prop('checked', $(this).is(':checked'));
            });
        },

        /**
         * Chart Initialization (if Chart.js is loaded)
         */
        initCharts: function() {
            if (typeof Chart === 'undefined') return;

            const statsCanvas = $('#stats-chart');
            if (!statsCanvas.length) return;

            new Chart(statsCanvas, {
                type: 'line',
                data: {
                    labels: nexusProAdmin.chartData.labels,
                    datasets: [{
                        label: 'Submissions',
                        data: nexusProAdmin.chartData.data,
                        borderColor: '#2271b1',
                        backgroundColor: 'rgba(34, 113, 177, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    };

    /**
     * Initialize on Document Ready
     */
    $(document).ready(function() {
        NexusProAdmin.init();
    });

})(jQuery);