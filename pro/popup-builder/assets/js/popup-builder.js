/**
 * Popup Builder Frontend JavaScript
 *
 * @package Nexus_Pro
 * @since 3.2.0
 */

(function($) {
	'use strict';

	/**
	 * Popup Manager
	 */
	const NexusPopupBuilder = {
		
		/**
		 * Active popups
		 */
		activePopups: [],

		/**
		 * Initialize
		 */
		init: function() {
			this.bindEvents();
			this.loadPopups();
		},

		/**
		 * Bind events
		 */
		bindEvents: function() {
			const self = this;

			// Close on overlay click
			$(document).on('click', '.nexus-popup-overlay', function(e) {
				if (e.target === this) {
					const popupId = $(this).data('popup-id');
					const closeOnOverlay = $(this).data('close-overlay');
					
					if (closeOnOverlay) {
						self.closePopup(popupId);
					}
				}
			});

			// Close button
			$(document).on('click', '.nexus-popup-close', function() {
				const popupId = $(this).closest('.nexus-popup-container').data('popup-id');
				self.closePopup(popupId);
			});

			// ESC key
			$(document).on('keydown', function(e) {
				if (e.key === 'Escape') {
					$('.nexus-popup-container').each(function() {
						const closeOnEsc = $(this).data('close-esc');
						if (closeOnEsc) {
							const popupId = $(this).data('popup-id');
							self.closePopup(popupId);
						}
					});
				}
			});

			// Prevent body scroll when popup is open
			$(document).on('nexus-popup-opened', function() {
				$('body').css('overflow', 'hidden');
			});

			$(document).on('nexus-popup-closed', function() {
				if ($('.nexus-popup-container.active').length === 0) {
					$('body').css('overflow', '');
				}
			});
		},

		/**
		 * Load popups
		 */
		loadPopups: function() {
			const self = this;

			if (typeof nexusPopups === 'undefined') {
				return;
			}

			nexusPopups.forEach(function(popup) {
				self.initPopup(popup);
			});
		},

		/**
		 * Initialize popup
		 */
		initPopup: function(popup) {
			const self = this;

			// Check frequency
			if (!this.checkFrequency(popup)) {
				return;
			}

			// Set up triggers
			const triggers = popup.triggers || {};

			// Page load trigger
			if (triggers.page_load && triggers.page_load.enabled) {
				const delay = parseInt(triggers.page_load.delay) || 0;
				setTimeout(function() {
					self.showPopup(popup.id);
				}, delay * 1000);
			}

			// Scroll trigger
			if (triggers.scroll && triggers.scroll.enabled) {
				this.initScrollTrigger(popup);
			}

			// Exit intent trigger
			if (triggers.exit_intent && triggers.exit_intent.enabled) {
				this.initExitIntentTrigger(popup);
			}

			// Click trigger
			if (triggers.click && triggers.click.enabled) {
				this.initClickTrigger(popup);
			}

			// Time delay trigger
			if (triggers.time_delay && triggers.time_delay.enabled) {
				const delay = parseInt(triggers.time_delay.delay) || 5;
				setTimeout(function() {
					self.showPopup(popup.id);
				}, delay * 1000);
			}

			// Inactivity trigger
			if (triggers.inactivity && triggers.inactivity.enabled) {
				this.initInactivityTrigger(popup);
			}
		},

		/**
		 * Check frequency
		 */
		checkFrequency: function(popup) {
			const triggers = popup.triggers || {};
			const frequency = triggers.frequency || {};

			if (frequency.show_once) {
				const cookieName = 'nexus_popup_shown_' + popup.id;
				if (this.getCookie(cookieName)) {
					return false;
				}
			}

			if (frequency.show_again_days) {
				const cookieName = 'nexus_popup_last_shown_' + popup.id;
				const lastShown = this.getCookie(cookieName);
				
				if (lastShown) {
					const daysSince = (Date.now() - parseInt(lastShown)) / (1000 * 60 * 60 * 24);
					if (daysSince < frequency.show_again_days) {
						return false;
					}
				}
			}

			return true;
		},

		/**
		 * Initialize scroll trigger
		 */
		initScrollTrigger: function(popup) {
			const self = this;
			const depth = parseInt(popup.triggers.scroll.depth) || 50;
			const direction = popup.triggers.scroll.direction || 'down';
			let triggered = false;

			$(window).on('scroll', function() {
				if (triggered) {
					return;
				}

				const scrolled = ($(window).scrollTop() / ($(document).height() - $(window).height())) * 100;

				if (direction === 'down' && scrolled >= depth) {
					triggered = true;
					self.showPopup(popup.id);
				} else if (direction === 'up' && scrolled <= depth) {
					triggered = true;
					self.showPopup(popup.id);
				}
			});
		},

		/**
		 * Initialize exit intent trigger
		 */
		initExitIntentTrigger: function(popup) {
			const self = this;
			const sensitivity = parseInt(popup.triggers.exit_intent.sensitivity) || 20;
			let triggered = false;

			$(document).on('mouseleave', function(e) {
				if (triggered) {
					return;
				}

				if (e.clientY <= sensitivity) {
					triggered = true;
					self.showPopup(popup.id);
				}
			});
		},

		/**
		 * Initialize click trigger
		 */
		initClickTrigger: function(popup) {
			const self = this;
			const selector = popup.triggers.click.selector || '';

			if (selector) {
				$(document).on('click', selector, function(e) {
					e.preventDefault();
					self.showPopup(popup.id);
				});
			}
		},

		/**
		 * Initialize inactivity trigger
		 */
		initInactivityTrigger: function(popup) {
			const self = this;
			const timeout = parseInt(popup.triggers.inactivity.timeout) || 30;
			let inactivityTimer;

			function resetTimer() {
				clearTimeout(inactivityTimer);
				inactivityTimer = setTimeout(function() {
					self.showPopup(popup.id);
				}, timeout * 1000);
			}

			$(document).on('mousemove keypress scroll', resetTimer);
			resetTimer();
		},

		/**
		 * Show popup
		 */
		showPopup: function(popupId) {
			const $overlay = $('.nexus-popup-overlay[data-popup-id="' + popupId + '"]');
			const $container = $('.nexus-popup-container[data-popup-id="' + popupId + '"]');

			if ($container.hasClass('active')) {
				return;
			}

			// Show overlay
			$overlay.addClass('active');

			// Show popup
			$container.addClass('active');

			// Track view
			this.trackView(popupId);

			// Set frequency cookie
			this.setFrequencyCookie(popupId);

			// Trigger event
			$(document).trigger('nexus-popup-opened', [popupId]);

			// Track in active popups
			this.activePopups.push(popupId);
		},

		/**
		 * Close popup
		 */
		closePopup: function(popupId) {
			const $overlay = $('.nexus-popup-overlay[data-popup-id="' + popupId + '"]');
			const $container = $('.nexus-popup-container[data-popup-id="' + popupId + '"]');

			// Add closing animation
			$overlay.addClass('closing');
			$container.addClass('closing');

			// Remove after animation
			setTimeout(function() {
				$overlay.removeClass('active closing');
				$container.removeClass('active closing');
			}, 300);

			// Trigger event
			$(document).trigger('nexus-popup-closed', [popupId]);

			// Remove from active popups
			const index = this.activePopups.indexOf(popupId);
			if (index > -1) {
				this.activePopups.splice(index, 1);
			}
		},

		/**
		 * Track view
		 */
		trackView: function(popupId) {
			$.ajax({
				url: nexusPopupVars.ajaxUrl,
				type: 'POST',
				data: {
					action: 'nexus_track_popup_view',
					nonce: nexusPopupVars.nonce,
					popup_id: popupId
				}
			});
		},

		/**
		 * Set frequency cookie
		 */
		setFrequencyCookie: function(popupId) {
			this.setCookie('nexus_popup_shown_' + popupId, '1', 365);
			this.setCookie('nexus_popup_last_shown_' + popupId, Date.now().toString(), 365);
		},

		/**
		 * Get cookie
		 */
		getCookie: function(name) {
			const value = '; ' + document.cookie;
			const parts = value.split('; ' + name + '=');
			
			if (parts.length === 2) {
				return parts.pop().split(';').shift();
			}
			
			return null;
		},

		/**
		 * Set cookie
		 */
		setCookie: function(name, value, days) {
			const expires = new Date();
			expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
			document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
		}
	};

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function() {
		NexusPopupBuilder.init();
	});

	/**
	 * Expose to global scope
	 */
	window.NexusPopupBuilder = NexusPopupBuilder;

})(jQuery);
