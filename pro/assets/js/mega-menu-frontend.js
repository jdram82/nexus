/**
 * Mega Menu - Frontend JavaScript
 * 
 * @package Nexus_Pro
 * @since 3.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initMegaMenu();
        handleMobileMenu();
        handleAccessibility();
    });

    /**
     * Initialize mega menu
     */
    function initMegaMenu() {
        var $megaMenuItems = $('.nexus-has-mega-menu');

        if (!$megaMenuItems.length) {
            return;
        }

        $megaMenuItems.each(function() {
            var $item = $(this);
            var $megaMenu = $item.find('> .nexus-mega-menu');
            var hoverTimeout;

            // Mouse enter
            $item.on('mouseenter', function() {
                clearTimeout(hoverTimeout);
                
                hoverTimeout = setTimeout(function() {
                    $megaMenu.stop(true, true).fadeIn(300);
                    $item.addClass('mega-menu-active');
                }, 200);
            });

            // Mouse leave
            $item.on('mouseleave', function() {
                clearTimeout(hoverTimeout);
                
                $megaMenu.stop(true, true).fadeOut(200);
                $item.removeClass('mega-menu-active');
            });

            // Position mega menu
            positionMegaMenu($item, $megaMenu);
        });

        // Reposition on window resize
        var resizeTimeout;
        $(window).on('resize', function() {
            clearTimeout(resizeTimeout);
            
            resizeTimeout = setTimeout(function() {
                $megaMenuItems.each(function() {
                    var $item = $(this);
                    var $megaMenu = $item.find('> .nexus-mega-menu');
                    positionMegaMenu($item, $megaMenu);
                });
            }, 250);
        });
    }

    /**
     * Position mega menu
     */
    function positionMegaMenu($item, $megaMenu) {
        if ($(window).width() <= 782) {
            return; // Skip positioning on mobile
        }

        var $nav = $item.closest('nav, .menu');
        var navWidth = $nav.outerWidth();
        var navOffset = $nav.offset().left;
        var itemOffset = $item.offset().left;
        var relativeOffset = itemOffset - navOffset;

        // Center the mega menu
        var megaMenuWidth = $megaMenu.outerWidth();
        var leftPosition = relativeOffset - (megaMenuWidth / 2) + ($item.outerWidth() / 2);

        // Prevent overflow on left
        if (leftPosition < 0) {
            leftPosition = 0;
        }

        // Prevent overflow on right
        if (leftPosition + megaMenuWidth > navWidth) {
            leftPosition = navWidth - megaMenuWidth;
        }

        $megaMenu.css('left', leftPosition + 'px');
    }

    /**
     * Handle mobile menu
     */
    function handleMobileMenu() {
        if ($(window).width() > 782) {
            return;
        }

        var $megaMenuItems = $('.nexus-has-mega-menu');

        $megaMenuItems.each(function() {
            var $item = $(this);
            var $link = $item.find('> a');
            var $megaMenu = $item.find('> .nexus-mega-menu');

            // Remove hover events
            $item.off('mouseenter mouseleave');

            // Add click toggle
            $link.on('click', function(e) {
                if ($megaMenu.length) {
                    e.preventDefault();
                    
                    // Close other mega menus
                    $('.nexus-mega-menu').not($megaMenu).slideUp(200);
                    $('.nexus-has-mega-menu').not($item).removeClass('mega-menu-active');

                    // Toggle this mega menu
                    $megaMenu.slideToggle(300);
                    $item.toggleClass('mega-menu-active');
                }
            });
        });
    }

    /**
     * Handle accessibility
     */
    function handleAccessibility() {
        var $megaMenuItems = $('.nexus-has-mega-menu');

        $megaMenuItems.each(function() {
            var $item = $(this);
            var $link = $item.find('> a');
            var $megaMenu = $item.find('> .nexus-mega-menu');

            // Add ARIA attributes
            $link.attr({
                'aria-haspopup': 'true',
                'aria-expanded': 'false'
            });

            $megaMenu.attr('role', 'menu');

            // Keyboard navigation
            $link.on('keydown', function(e) {
                // Enter or Space to toggle
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    
                    var isExpanded = $link.attr('aria-expanded') === 'true';
                    
                    if (isExpanded) {
                        $megaMenu.fadeOut(200);
                        $link.attr('aria-expanded', 'false');
                        $item.removeClass('mega-menu-active');
                    } else {
                        // Close other menus
                        $('.nexus-mega-menu').fadeOut(200);
                        $('.nexus-has-mega-menu > a').attr('aria-expanded', 'false');
                        $('.nexus-has-mega-menu').removeClass('mega-menu-active');

                        // Open this menu
                        $megaMenu.fadeIn(300);
                        $link.attr('aria-expanded', 'true');
                        $item.addClass('mega-menu-active');

                        // Focus first link
                        setTimeout(function() {
                            $megaMenu.find('a:first').focus();
                        }, 100);
                    }
                }

                // Escape to close
                if (e.key === 'Escape') {
                    $megaMenu.fadeOut(200);
                    $link.attr('aria-expanded', 'false');
                    $item.removeClass('mega-menu-active');
                    $link.focus();
                }
            });

            // Update ARIA on hover
            $item.on('mouseenter', function() {
                $link.attr('aria-expanded', 'true');
            });

            $item.on('mouseleave', function() {
                $link.attr('aria-expanded', 'false');
            });
        });

        // Close mega menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.nexus-has-mega-menu').length) {
                $('.nexus-mega-menu').fadeOut(200);
                $('.nexus-has-mega-menu > a').attr('aria-expanded', 'false');
                $('.nexus-has-mega-menu').removeClass('mega-menu-active');
            }
        });

        // Trap focus within mega menu
        $('.nexus-mega-menu').on('keydown', 'a', function(e) {
            if (e.key === 'Tab') {
                var $megaMenu = $(this).closest('.nexus-mega-menu');
                var $focusableElements = $megaMenu.find('a');
                var firstElement = $focusableElements.first()[0];
                var lastElement = $focusableElements.last()[0];

                // Shift + Tab on first element
                if (e.shiftKey && this === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                }
                // Tab on last element
                else if (!e.shiftKey && this === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }

            // Escape to close
            if (e.key === 'Escape') {
                var $item = $(this).closest('.nexus-has-mega-menu');
                var $link = $item.find('> a');
                
                $megaMenu.fadeOut(200);
                $link.attr('aria-expanded', 'false');
                $item.removeClass('mega-menu-active');
                $link.focus();
            }
        });
    }

    /**
     * Handle window resize
     */
    var resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        
        resizeTimer = setTimeout(function() {
            // Reinitialize based on screen size
            if ($(window).width() > 782) {
                initMegaMenu();
                
                // Remove mobile click handlers
                $('.nexus-has-mega-menu > a').off('click');
            } else {
                handleMobileMenu();
            }
        }, 250);
    });

})(jQuery);
