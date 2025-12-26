/**
 * Header Builder JavaScript
 * Nexus Pro - Header Customizer & Mobile Menu
 */

(function($) {
    'use strict';

    const NexusHeaderBuilder = {
        /**
         * Initialize
         */
        init: function() {
            this.mobileMenu();
            this.stickyHeader();
            this.searchToggle();
            this.dropdownMenu();
        },

        /**
         * Mobile Menu Toggle
         */
        mobileMenu: function() {
            const toggleBtn = $('.mobile-menu-toggle');
            const menu = $('.header-element-menu');

            toggleBtn.on('click', function(e) {
                e.preventDefault();
                menu.toggleClass('active');
                $(this).toggleClass('active');
                
                // Animate hamburger
                const spans = $(this).find('span');
                if ($(this).hasClass('active')) {
                    spans.eq(0).css('transform', 'rotate(45deg) translateY(10px)');
                    spans.eq(1).css('opacity', '0');
                    spans.eq(2).css('transform', 'rotate(-45deg) translateY(-10px)');
                } else {
                    spans.css({'transform': '', 'opacity': ''});
                }
            });

            // Close menu on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.header-element-menu, .mobile-menu-toggle').length) {
                    menu.removeClass('active');
                    toggleBtn.removeClass('active');
                    toggleBtn.find('span').css({'transform': '', 'opacity': ''});
                }
            });
        },

        /**
         * Sticky Header
         */
        stickyHeader: function() {
            const header = $('.nexus-header-builder.header-style-sticky');
            if (!header.length) return;

            let lastScroll = 0;
            const headerHeight = header.outerHeight();

            $(window).on('scroll', function() {
                const currentScroll = $(this).scrollTop();

                if (currentScroll > headerHeight) {
                    header.addClass('scrolled');
                } else {
                    header.removeClass('scrolled');
                }

                lastScroll = currentScroll;
            });
        },

        /**
         * Search Toggle
         */
        searchToggle: function() {
            const searchForm = $('.header-element-search form');
            const searchInput = searchForm.find('input[type="search"]');

            // Focus animation
            searchInput.on('focus', function() {
                $(this).closest('.header-element-search').addClass('focused');
            });

            searchInput.on('blur', function() {
                if (!$(this).val()) {
                    $(this).closest('.header-element-search').removeClass('focused');
                }
            });

            // Submit handler
            searchForm.on('submit', function(e) {
                if (!searchInput.val().trim()) {
                    e.preventDefault();
                    searchInput.focus();
                }
            });
        },

        /**
         * Dropdown Menu
         */
        dropdownMenu: function() {
            const menuItems = $('.header-element-menu nav ul li');

            menuItems.each(function() {
                if ($(this).find('ul').length) {
                    $(this).addClass('has-dropdown');
                    
                    // Add dropdown indicator
                    if (!$(this).find('.dropdown-indicator').length) {
                        $(this).find('> a').append('<span class="dropdown-indicator">▼</span>');
                    }
                }
            });

            // Mobile dropdown toggle
            if ($(window).width() < 768) {
                $('.header-element-menu nav ul li.has-dropdown > a').on('click', function(e) {
                    e.preventDefault();
                    $(this).next('ul').slideToggle(200);
                    $(this).parent().toggleClass('open');
                });
            }
        }
    };

    /**
     * Customizer Live Preview
     */
    if (typeof wp !== 'undefined' && wp.customize) {
        wp.customize('nexus_header_style', function(value) {
            value.bind(function(to) {
                const header = $('.nexus-header-builder');
                header.removeClass('header-style-default header-style-transparent header-style-sticky header-style-custom');
                header.addClass('header-style-' + to);
            });
        });

        // Live preview for element visibility
        const rows = ['top', 'main', 'bottom'];
        const columns = ['left', 'center', 'right'];

        rows.forEach(function(row) {
            columns.forEach(function(column) {
                const settingName = 'nexus_header_' + row + '_' + column;
                wp.customize(settingName, function(value) {
                    value.bind(function(to) {
                        const container = $('.header-row-' + row + ' .header-column-' + column);
                        container.empty();
                        
                        if (to && to.length) {
                            to.forEach(function(element) {
                                container.append('<div class="header-element header-element-' + element + '">Loading...</div>');
                            });
                            // Trigger refresh
                            wp.customize.preview.send('refresh');
                        }
                    });
                });
            });
        });
    }

    /**
     * Initialize on Document Ready
     */
    $(document).ready(function() {
        NexusHeaderBuilder.init();
    });

    /**
     * Reinitialize on Window Resize
     */
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            NexusHeaderBuilder.dropdownMenu();
        }, 250);
    });

})(jQuery);