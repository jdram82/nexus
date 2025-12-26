/**
 * Client Portal JavaScript
 * Nexus Pro - Portal Interactions
 */

(function($) {
    'use strict';

    const NexusPortal = {
        /**
         * Initialize
         */
        init: function() {
            this.mobileNav();
            this.profileUpload();
            this.activityFeed();
            this.statsAnimation();
            this.downloadTracking();
        },

        /**
         * Mobile Navigation
         */
        mobileNav: function() {
            // Add mobile toggle button
            if ($(window).width() < 992 && !$('.portal-nav-toggle').length) {
                $('.portal-sidebar').prepend(
                    '<button class="portal-nav-toggle">' +
                    '<span class="dashicons dashicons-menu"></span>' +
                    'Menu' +
                    '</button>'
                );
            }

            // Toggle navigation
            $(document).on('click', '.portal-nav-toggle', function() {
                $('.portal-nav').slideToggle(300);
                $(this).toggleClass('active');
            });

            // Close nav on link click (mobile)
            if ($(window).width() < 992) {
                $('.portal-nav a').on('click', function() {
                    $('.portal-nav').slideUp(300);
                    $('.portal-nav-toggle').removeClass('active');
                });
            }
        },

        /**
         * Profile Avatar Upload
         */
        profileUpload: function() {
            const uploadInput = $('.profile-avatar-upload input[type="file"]');
            const avatarImg = $('.profile-avatar-upload img');

            uploadInput.on('change', function(e) {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        avatarImg.attr('src', event.target.result);
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert('Please select a valid image file.');
                }
            });
        },

        /**
         * Activity Feed Loading
         */
        activityFeed: function() {
            const loadMoreBtn = $('.load-more-activity');
            
            loadMoreBtn.on('click', function(e) {
                e.preventDefault();
                const btn = $(this);
                const page = btn.data('page') || 2;

                btn.text('Loading...').prop('disabled', true);

                $.ajax({
                    url: nexusPortalData.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'nexus_load_activity',
                        nonce: nexusPortalData.nonce,
                        page: page
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.activity-feed').append(response.data.html);
                            btn.data('page', page + 1);
                            
                            if (!response.data.has_more) {
                                btn.remove();
                            }
                        }
                    },
                    complete: function() {
                        btn.text('Load More').prop('disabled', false);
                    }
                });
            });
        },

        /**
         * Animate Stats on Scroll
         */
        statsAnimation: function() {
            const stats = $('.stat-card');
            if (!stats.length) return;

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const card = $(entry.target);
                        const value = card.find('.stat-value');
                        const finalValue = parseInt(value.text());
                        
                        // Animate counter
                        $({ counter: 0 }).animate({ counter: finalValue }, {
                            duration: 1000,
                            easing: 'swing',
                            step: function() {
                                value.text(Math.ceil(this.counter));
                            },
                            complete: function() {
                                value.text(finalValue);
                            }
                        });
                        
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            stats.each(function() {
                observer.observe(this);
            });
        },

        /**
         * Download Tracking
         */
        downloadTracking: function() {
            $('.download-btn').on('click', function(e) {
                const downloadId = $(this).data('download-id');
                const fileName = $(this).data('file-name');

                // Track download via AJAX
                $.post(nexusPortalData.ajaxurl, {
                    action: 'nexus_track_download',
                    nonce: nexusPortalData.nonce,
                    download_id: downloadId,
                    file_name: fileName
                });

                // Show notification
                NexusPortal.showNotification('Download started: ' + fileName, 'success');
            });
        },

        /**
         * Show Notification
         */
        showNotification: function(message, type = 'info') {
            const notification = $(
                '<div class="portal-notification ' + type + '">' +
                '<span class="dashicons dashicons-yes"></span>' +
                message +
                '</div>'
            );

            $('body').append(notification);

            setTimeout(function() {
                notification.addClass('show');
            }, 100);

            setTimeout(function() {
                notification.removeClass('show');
                setTimeout(function() {
                    notification.remove();
                }, 300);
            }, 3000);
        },

        /**
         * Project Filters
         */
        projectFilters: function() {
            $('.project-filter').on('change', function() {
                const status = $(this).val();
                const cards = $('.project-card');

                if (status === 'all') {
                    cards.show();
                } else {
                    cards.hide();
                    $('.project-card .project-status.' + status).closest('.project-card').show();
                }
            });
        },

        /**
         * Support Ticket Form
         */
        ticketForm: function() {
            $('.support-ticket-form').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const formData = new FormData(this);
                formData.append('action', 'nexus_submit_ticket');
                formData.append('nonce', nexusPortalData.nonce);

                $.ajax({
                    url: nexusPortalData.ajaxurl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            NexusPortal.showNotification('Ticket submitted successfully!', 'success');
                            form[0].reset();
                        } else {
                            NexusPortal.showNotification(response.data.message, 'error');
                        }
                    },
                    error: function() {
                        NexusPortal.showNotification('An error occurred. Please try again.', 'error');
                    }
                });
            });
        }
    };

    /**
     * Initialize on Document Ready
     */
    $(document).ready(function() {
        if ($('.nexus-client-portal').length) {
            NexusPortal.init();
        }
    });

    /**
     * Reinitialize on Window Resize
     */
    $(window).on('resize', function() {
        if ($('.nexus-client-portal').length) {
            NexusPortal.mobileNav();
        }
    });

})(jQuery);