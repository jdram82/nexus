/**
 * API Documentation Generator JavaScript
 *
 * @package Nexus_Pro
 * @since 3.0.0
 */

(function($) {
	'use strict';

	const NexusAPIDocs = {

		/**
		 * Initialize
		 */
		init: function() {
			this.bindEvents();
			this.initExplorer();
			this.initPrism();
		},

		/**
		 * Bind events
		 */
		bindEvents: function() {
			// Generate documentation
			$(document).on('click', '#generate-docs-btn', this.generateDocs.bind(this));

			// Parse single file
			$(document).on('click', '#parse-file-btn', this.parseFile.bind(this));

			// Export documentation
			$(document).on('click', '#export-docs-btn', this.exportDocs.bind(this));

			// Filter endpoints table
			$(document).on('keyup', '#endpoints-search', this.filterEndpoints.bind(this));

			// Delete endpoint
			$(document).on('click', '.delete-endpoint', this.deleteEndpoint.bind(this));
		},

		/**
		 * Generate documentation
		 */
		generateDocs: function(e) {
			e.preventDefault();

			const $btn = $(e.currentTarget);
			const $progress = $('.nexus-progress-wrap');
			const $progressBar = $('.nexus-progress-fill');
			const $progressStatus = $('.nexus-progress-status');
			
			const directory = $('#scan-directory').val();
			const languages = $('#scan-languages').val();

			if (!directory) {
				alert('Please select a directory to scan.');
				return;
			}

			$btn.prop('disabled', true).text('Generating...');
			$progress.addClass('active');
			$progressBar.css('width', '0%').text('0%');
			$progressStatus.text('Starting scan...');

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'nexus_generate_docs',
					nonce: nexusApiDocs.nonce,
					directory: directory,
					languages: languages
				},
				xhr: function() {
					const xhr = new window.XMLHttpRequest();
					// Monitor progress if supported
					xhr.addEventListener('progress', function(e) {
						if (e.lengthComputable) {
							const percent = Math.round((e.loaded / e.total) * 100);
							$progressBar.css('width', percent + '%').text(percent + '%');
						}
					});
					return xhr;
				},
				success: function(response) {
					if (response.success) {
						const data = response.data;
						$progressBar.css('width', '100%').text('100%');
						$progressStatus.html(
							'<strong>Complete!</strong> ' +
							'Parsed ' + data.files_parsed + ' files, ' +
							'found ' + data.endpoints_found + ' endpoints.'
						);

						// Reload page after delay
						setTimeout(function() {
							location.reload();
						}, 2000);
					} else {
						alert('Error: ' + response.data.message);
						$progress.removeClass('active');
					}
				},
				error: function() {
					alert('An error occurred while generating documentation.');
					$progress.removeClass('active');
				},
				complete: function() {
					$btn.prop('disabled', false).text('Generate Documentation');
				}
			});
		},

		/**
		 * Parse single file
		 */
		parseFile: function(e) {
			e.preventDefault();

			const $btn = $(e.currentTarget);
			const file = $('#parse-file-path').val();

			if (!file) {
				alert('Please enter a file path.');
				return;
			}

			$btn.prop('disabled', true).text('Parsing...');

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'nexus_parse_file',
					nonce: nexusApiDocs.nonce,
					file: file
				},
				success: function(response) {
					if (response.success) {
						alert('File parsed successfully! Found ' + response.data.methods_found + ' methods.');
						location.reload();
					} else {
						alert('Error: ' + response.data.message);
					}
				},
				error: function() {
					alert('An error occurred while parsing the file.');
				},
				complete: function() {
					$btn.prop('disabled', false).text('Parse File');
				}
			});
		},

		/**
		 * Export documentation
		 */
		exportDocs: function(e) {
			e.preventDefault();

			const format = $('#export-format').val();
			
			window.location.href = ajaxurl + 
				'?action=nexus_export_docs' +
				'&format=' + format +
				'&nonce=' + nexusApiDocs.nonce;
		},

		/**
		 * Filter endpoints table
		 */
		filterEndpoints: function(e) {
			const search = $(e.currentTarget).val().toLowerCase();
			
			$('.nexus-endpoints-table tbody tr').each(function() {
				const text = $(this).text().toLowerCase();
				$(this).toggle(text.indexOf(search) > -1);
			});
		},

		/**
		 * Delete endpoint
		 */
		deleteEndpoint: function(e) {
			e.preventDefault();

			if (!confirm('Are you sure you want to delete this endpoint?')) {
				return;
			}

			const $btn = $(e.currentTarget);
			const endpointId = $btn.data('endpoint-id');

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'nexus_delete_endpoint',
					nonce: nexusApiDocs.nonce,
					endpoint_id: endpointId
				},
				success: function(response) {
					if (response.success) {
						$btn.closest('tr').fadeOut(300, function() {
							$(this).remove();
						});
					} else {
						alert('Error: ' + response.data.message);
					}
				},
				error: function() {
					alert('An error occurred while deleting the endpoint.');
				}
			});
		},

		/**
		 * Initialize Explorer
		 */
		initExplorer: function() {
			// Endpoint selection
			$(document).on('click', '.nexus-endpoint-item', function() {
				const endpointId = $(this).data('endpoint-id');
				window.location.href = '?page=nexus-api-explorer&endpoint=' + endpointId;
			});

			// Search endpoints
			$(document).on('keyup', '#endpoint-search', function() {
				const search = $(this).val().toLowerCase();
				
				$('.nexus-endpoint-item').each(function() {
					const text = $(this).text().toLowerCase();
					$(this).toggle(text.indexOf(search) > -1);
				});
			});

			// Test endpoint
			$(document).on('click', '#nexus-test-endpoint-btn', this.testEndpoint.bind(this));

			// Save test
			$(document).on('click', '#nexus-save-test-btn', this.saveTest.bind(this));

			// Clear test
			$(document).on('click', '#nexus-clear-test-btn', this.clearTest.bind(this));

			// Response tabs
			$(document).on('click', '.nexus-tab-btn', function() {
				const tab = $(this).data('tab');
				$('.nexus-tab-btn').removeClass('active');
				$(this).addClass('active');
				$('.nexus-tab-content').removeClass('active');
				$('.nexus-tab-content[data-tab="' + tab + '"]').addClass('active');
			});

			// Code tabs
			$(document).on('click', '.nexus-code-tab-btn', function() {
				const lang = $(this).data('lang');
				$('.nexus-code-tab-btn').removeClass('active');
				$(this).addClass('active');
				$('.nexus-code-example').removeClass('active');
				$('.nexus-code-example[data-lang="' + lang + '"]').addClass('active');
			});

			// Copy code
			$(document).on('click', '.nexus-copy-code', this.copyCode.bind(this));
		},

		/**
		 * Test endpoint
		 */
		testEndpoint: function(e) {
			e.preventDefault();

			const $btn = $(e.currentTarget);
			const endpointId = $btn.data('endpoint-id');
			
			// Collect parameters
			const params = {};
			$('.nexus-param-input').each(function() {
				const name = $(this).attr('name').replace('param[', '').replace(']', '');
				const value = $(this).val();
				if (value) {
					params[name] = value;
				}
			});

			const headers = $('#request-headers').val();
			const body = $('#request-body').val();

			$btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Sending...');

			const startTime = Date.now();

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'nexus_test_endpoint',
					nonce: nexusApiDocs.nonce,
					endpoint_id: endpointId,
					params: params,
					headers: headers,
					body: body
				},
				success: function(response) {
					const endTime = Date.now();
					const duration = endTime - startTime;

					if (response.success) {
						NexusAPIDocs.displayResponse(response.data, duration);
					} else {
						NexusAPIDocs.displayError(response.data.message);
					}
				},
				error: function(xhr) {
					NexusAPIDocs.displayError('Request failed: ' + xhr.statusText);
				},
				complete: function() {
					$btn.prop('disabled', false).html('<span class="dashicons dashicons-controls-play"></span> Send Request');
				}
			});
		},

		/**
		 * Display response
		 */
		displayResponse: function(data, duration) {
			const $container = $('#nexus-response-container');
			
			// Status
			const statusClass = data.status >= 200 && data.status < 300 ? '' : 'error';
			$('.nexus-response-status')
				.text('Status: ' + data.status)
				.removeClass('error')
				.addClass(statusClass);

			// Time
			$('.nexus-response-time').text('Time: ' + duration + 'ms');

			// Size
			const size = data.size < 1024 ? data.size + ' B' : 
						 Math.round(data.size / 1024) + ' KB';
			$('.nexus-response-size').text('Size: ' + size);

			// Body
			try {
				const bodyJson = JSON.parse(data.body);
				$('#response-body').text(JSON.stringify(bodyJson, null, 2));
			} catch (e) {
				$('#response-body').text(data.body);
			}

			// Headers
			let headersText = '';
			if (data.headers) {
				for (const [key, value] of Object.entries(data.headers)) {
					headersText += key + ': ' + value + '\n';
				}
			}
			$('#response-headers').text(headersText);

			// Show container
			$container.show();

			// Highlight syntax
			if (typeof Prism !== 'undefined') {
				Prism.highlightAll();
			}

			// Scroll to response
			$('html, body').animate({
				scrollTop: $container.offset().top - 100
			}, 500);
		},

		/**
		 * Display error
		 */
		displayError: function(message) {
			const $container = $('#nexus-response-container');
			
			$('.nexus-response-status')
				.text('Error')
				.addClass('error');
			
			$('.nexus-response-time').text('');
			$('.nexus-response-size').text('');
			
			$('#response-body').text(message);
			$('#response-headers').text('');
			
			$container.show();

			$('html, body').animate({
				scrollTop: $container.offset().top - 100
			}, 500);
		},

		/**
		 * Save test
		 */
		saveTest: function(e) {
			e.preventDefault();

			const testData = {
				endpoint_id: $('#nexus-test-endpoint-btn').data('endpoint-id'),
				params: {},
				headers: $('#request-headers').val(),
				body: $('#request-body').val()
			};

			// Collect parameters
			$('.nexus-param-input').each(function() {
				const name = $(this).attr('name').replace('param[', '').replace(']', '');
				const value = $(this).val();
				if (value) {
					testData.params[name] = value;
				}
			});

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'nexus_save_test',
					nonce: nexusApiDocs.nonce,
					test: JSON.stringify(testData)
				},
				success: function(response) {
					if (response.success) {
						alert('Test saved successfully!');
					} else {
						alert('Error: ' + response.data.message);
					}
				},
				error: function() {
					alert('An error occurred while saving the test.');
				}
			});
		},

		/**
		 * Clear test
		 */
		clearTest: function(e) {
			e.preventDefault();

			$('.nexus-param-input').val('');
			$('#request-headers').val('');
			$('#request-body').val('');
			$('#nexus-response-container').hide();
		},

		/**
		 * Copy code
		 */
		copyCode: function(e) {
			e.preventDefault();

			const $btn = $(e.currentTarget);
			const target = $btn.data('clipboard-target');
			const $code = $(target);
			
			// Create temporary textarea
			const $temp = $('<textarea>');
			$('body').append($temp);
			$temp.val($code.text()).select();
			document.execCommand('copy');
			$temp.remove();

			// Visual feedback
			const originalHtml = $btn.html();
			$btn.html('<span class="dashicons dashicons-yes"></span> Copied!');
			
			setTimeout(function() {
				$btn.html(originalHtml);
			}, 2000);
		},

		/**
		 * Initialize Prism syntax highlighting
		 */
		initPrism: function() {
			if (typeof Prism !== 'undefined') {
				Prism.highlightAll();
			}
		}
	};

	// Initialize on document ready
	$(document).ready(function() {
		NexusAPIDocs.init();
	});

})(jQuery);
