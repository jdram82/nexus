/**
 * Popup Builder Admin JavaScript
 *
 * @package Nexus_Pro
 * @since 3.2.0
 */

(function($) {
	'use strict';

	/**
	 * Admin Manager
	 */
	const NexusPopupAdmin = {
		
		/**
		 * Initialize
		 */
		init: function() {
			this.initColorPickers();
			this.bindEvents();
		},

		/**
		 * Initialize color pickers
		 */
		initColorPickers: function() {
			if (typeof $.fn.wpColorPicker !== 'undefined') {
				$('.color-picker').wpColorPicker();
			}
		},

		/**
		 * Bind events
		 */
		bindEvents: function() {
			const self = this;

			// Template selection
			$(document).on('click', '.nexus-popup-template-card', function() {
				const templateId = $(this).data('template-id');
				self.useTemplate(templateId);
			});

			// Duplicate popup
			$(document).on('click', '.duplicate-popup', function(e) {
				e.preventDefault();
				const popupId = $(this).data('popup-id');
				self.duplicatePopup(popupId);
			});

			// Preview popup
			$(document).on('click', '.preview-popup', function(e) {
				e.preventDefault();
				const popupId = $(this).data('popup-id');
				self.previewPopup(popupId);
			});
		},

		/**
		 * Use template
		 */
		useTemplate: function(templateId) {
			if (!confirm('This will replace the current popup content. Continue?')) {
				return;
			}

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'nexus_use_popup_template',
					nonce: nexusPopupAdminVars.nonce,
					template_id: templateId,
					popup_id: $('#post_ID').val()
				},
				beforeSend: function() {
					$('.nexus-popup-editor').addClass('loading');
				},
				success: function(response) {
					if (response.success) {
						// Update editor content
						if (typeof tinyMCE !== 'undefined') {
							tinyMCE.get('nexus_popup_content').setContent(response.data.content);
						} else {
							$('#nexus_popup_content').val(response.data.content);
						}

						// Update settings
						if (response.data.settings) {
							$.each(response.data.settings, function(key, value) {
								$('[name="' + key + '"]').val(value);
							});
						}

						alert('Template applied successfully!');
					} else {
						alert('Error: ' + response.data.message);
					}
				},
				error: function() {
					alert('An error occurred while applying the template.');
				},
				complete: function() {
					$('.nexus-popup-editor').removeClass('loading');
				}
			});
		},

		/**
		 * Duplicate popup
		 */
		duplicatePopup: function(popupId) {
			if (!confirm('Duplicate this popup?')) {
				return;
			}

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'nexus_duplicate_popup',
					nonce: nexusPopupAdminVars.nonce,
					popup_id: popupId
				},
				beforeSend: function() {
					$('.nexus-popup-loading').show();
				},
				success: function(response) {
					if (response.success) {
						window.location.href = response.data.edit_url;
					} else {
						alert('Error: ' + response.data.message);
					}
				},
				error: function() {
					alert('An error occurred while duplicating the popup.');
				},
				complete: function() {
					$('.nexus-popup-loading').hide();
				}
			});
		},

		/**
		 * Preview popup
		 */
		previewPopup: function(popupId) {
			const previewUrl = nexusPopupAdminVars.siteUrl + '?nexus_popup_preview=' + popupId;
			window.open(previewUrl, 'popup_preview', 'width=1200,height=800');
		}
	};

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function() {
		NexusPopupAdmin.init();
	});

	/**
	 * Expose to global scope
	 */
	window.NexusPopupAdmin = NexusPopupAdmin;

})(jQuery);
