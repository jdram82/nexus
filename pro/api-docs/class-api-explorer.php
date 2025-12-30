<?php
/**
 * API Explorer - Interactive API testing interface
 *
 * @package Nexus_Pro
 * @subpackage API_Docs
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * API Explorer Class
 *
 * Provides interactive interface for testing API endpoints
 */
class Nexus_API_Explorer {

	/**
	 * Instance
	 *
	 * @var Nexus_API_Explorer
	 */
	private static $instance = null;

	/**
	 * Get instance
	 *
	 * @return Nexus_API_Explorer
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'wp_ajax_nexus_test_endpoint', array( $this, 'ajax_test_endpoint' ) );
		add_action( 'wp_ajax_nexus_save_test', array( $this, 'ajax_save_test' ) );
	}

	/**
	 * Render explorer page
	 */
	public function render_page() {
		$endpoint_manager = Nexus_Endpoint_Manager::get_instance();
		$endpoints        = $endpoint_manager->get_all_endpoints();
		$selected_id      = isset( $_GET['endpoint'] ) ? sanitize_text_field( $_GET['endpoint'] ) : '';
		$selected         = null;

		if ( $selected_id ) {
			foreach ( $endpoints as $endpoint ) {
				if ( $endpoint['id'] === $selected_id ) {
					$selected = $endpoint;
					break;
				}
			}
		}
		?>
		<div class="wrap nexus-api-explorer-wrap">
			<h1><?php esc_html_e( 'API Explorer', 'nexus-pro' ); ?></h1>

			<div class="nexus-explorer-container">
				<!-- Sidebar - Endpoints List -->
				<div class="nexus-explorer-sidebar">
					<div class="nexus-endpoints-search">
						<input type="text" id="endpoint-search" placeholder="<?php esc_attr_e( 'Search endpoints...', 'nexus-pro' ); ?>" />
					</div>

					<div class="nexus-endpoints-list">
						<?php if ( ! empty( $endpoints ) ) : ?>
							<?php foreach ( $endpoints as $endpoint ) : ?>
								<div class="nexus-endpoint-item <?php echo $endpoint['id'] === $selected_id ? 'active' : ''; ?>" data-endpoint-id="<?php echo esc_attr( $endpoint['id'] ); ?>">
									<div class="nexus-endpoint-method nexus-method-<?php echo esc_attr( strtolower( $endpoint['method'] ) ); ?>">
										<?php echo esc_html( $endpoint['method'] ); ?>
									</div>
									<div class="nexus-endpoint-path">
										<div class="nexus-endpoint-title"><?php echo esc_html( $endpoint['path'] ); ?></div>
										<div class="nexus-endpoint-desc"><?php echo esc_html( wp_trim_words( $endpoint['description'], 8 ) ); ?></div>
									</div>
								</div>
							<?php endforeach; ?>
						<?php else : ?>
							<p class="nexus-empty-state"><?php esc_html_e( 'No endpoints found.', 'nexus-pro' ); ?></p>
						<?php endif; ?>
					</div>
				</div>

				<!-- Main - API Testing Interface -->
				<div class="nexus-explorer-main">
					<?php if ( $selected ) : ?>
						<div class="nexus-endpoint-details">
							<!-- Header -->
							<div class="nexus-endpoint-header">
								<span class="nexus-method-badge nexus-method-<?php echo esc_attr( strtolower( $selected['method'] ) ); ?>">
									<?php echo esc_html( $selected['method'] ); ?>
								</span>
								<code class="nexus-endpoint-url"><?php echo esc_html( rest_url( $selected['path'] ) ); ?></code>
							</div>

							<div class="nexus-endpoint-description">
								<p><?php echo esc_html( $selected['description'] ); ?></p>
							</div>

							<!-- Parameters -->
							<?php if ( ! empty( $selected['parameters'] ) ) : ?>
								<div class="nexus-section">
									<h3><?php esc_html_e( 'Parameters', 'nexus-pro' ); ?></h3>
									<table class="nexus-params-table">
										<thead>
											<tr>
												<th><?php esc_html_e( 'Name', 'nexus-pro' ); ?></th>
												<th><?php esc_html_e( 'Type', 'nexus-pro' ); ?></th>
												<th><?php esc_html_e( 'Required', 'nexus-pro' ); ?></th>
												<th><?php esc_html_e( 'Description', 'nexus-pro' ); ?></th>
												<th><?php esc_html_e( 'Value', 'nexus-pro' ); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ( $selected['parameters'] as $param ) : ?>
												<tr>
													<td><code><?php echo esc_html( $param['name'] ); ?></code></td>
													<td><span class="nexus-type-badge"><?php echo esc_html( $param['type'] ?? 'string' ); ?></span></td>
													<td><?php echo ! empty( $param['required'] ) ? '<span class="nexus-required">Yes</span>' : 'No'; ?></td>
													<td><?php echo esc_html( $param['description'] ?? '' ); ?></td>
													<td>
														<input type="text" 
															   class="nexus-param-input" 
															   name="param[<?php echo esc_attr( $param['name'] ); ?>]" 
															   placeholder="<?php echo esc_attr( $param['default'] ?? '' ); ?>" />
													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							<?php endif; ?>

							<!-- Headers -->
							<div class="nexus-section">
								<h3><?php esc_html_e( 'Headers', 'nexus-pro' ); ?></h3>
								<div class="nexus-headers-editor">
									<textarea id="request-headers" rows="4" placeholder="Content-Type: application/json&#10;Authorization: Bearer token"></textarea>
								</div>
							</div>

							<!-- Request Body -->
							<?php if ( in_array( $selected['method'], array( 'POST', 'PUT', 'PATCH' ), true ) ) : ?>
								<div class="nexus-section">
									<h3><?php esc_html_e( 'Request Body', 'nexus-pro' ); ?></h3>
									<div class="nexus-body-editor">
										<textarea id="request-body" rows="10" placeholder='{"key": "value"}'></textarea>
									</div>
								</div>
							<?php endif; ?>

							<!-- Test Button -->
							<div class="nexus-test-actions">
								<button type="button" class="button button-primary button-large" id="nexus-test-endpoint-btn" data-endpoint-id="<?php echo esc_attr( $selected['id'] ); ?>">
									<span class="dashicons dashicons-controls-play"></span>
									<?php esc_html_e( 'Send Request', 'nexus-pro' ); ?>
								</button>
								<button type="button" class="button button-secondary" id="nexus-save-test-btn">
									<span class="dashicons dashicons-saved"></span>
									<?php esc_html_e( 'Save Test', 'nexus-pro' ); ?>
								</button>
								<button type="button" class="button button-secondary" id="nexus-clear-test-btn">
									<?php esc_html_e( 'Clear', 'nexus-pro' ); ?>
								</button>
							</div>

							<!-- Response -->
							<div id="nexus-response-container" style="display: none;">
								<div class="nexus-section">
									<h3><?php esc_html_e( 'Response', 'nexus-pro' ); ?></h3>
									
									<div class="nexus-response-meta">
										<span class="nexus-response-status"></span>
										<span class="nexus-response-time"></span>
										<span class="nexus-response-size"></span>
									</div>

									<div class="nexus-response-tabs">
										<button type="button" class="nexus-tab-btn active" data-tab="body"><?php esc_html_e( 'Body', 'nexus-pro' ); ?></button>
										<button type="button" class="nexus-tab-btn" data-tab="headers"><?php esc_html_e( 'Headers', 'nexus-pro' ); ?></button>
									</div>

									<div class="nexus-tab-content active" data-tab="body">
										<pre><code id="response-body" class="language-json"></code></pre>
									</div>

									<div class="nexus-tab-content" data-tab="headers">
										<pre><code id="response-headers" class="language-http"></code></pre>
									</div>
								</div>
							</div>

							<!-- Code Examples -->
							<div class="nexus-section">
								<h3><?php esc_html_e( 'Code Examples', 'nexus-pro' ); ?></h3>
								
								<div class="nexus-code-tabs">
									<button type="button" class="nexus-code-tab-btn active" data-lang="curl">cURL</button>
									<button type="button" class="nexus-code-tab-btn" data-lang="php">PHP</button>
									<button type="button" class="nexus-code-tab-btn" data-lang="javascript">JavaScript</button>
									<button type="button" class="nexus-code-tab-btn" data-lang="python">Python</button>
								</div>

								<div class="nexus-code-example active" data-lang="curl">
									<button type="button" class="nexus-copy-code" data-clipboard-target="#curl-code">
										<span class="dashicons dashicons-clipboard"></span>
									</button>
									<pre><code id="curl-code" class="language-bash"><?php echo esc_html( $this->generate_curl_example( $selected ) ); ?></code></pre>
								</div>

								<div class="nexus-code-example" data-lang="php">
									<button type="button" class="nexus-copy-code" data-clipboard-target="#php-code">
										<span class="dashicons dashicons-clipboard"></span>
									</button>
									<pre><code id="php-code" class="language-php"><?php echo esc_html( $this->generate_php_example( $selected ) ); ?></code></pre>
								</div>

								<div class="nexus-code-example" data-lang="javascript">
									<button type="button" class="nexus-copy-code" data-clipboard-target="#js-code">
										<span class="dashicons dashicons-clipboard"></span>
									</button>
									<pre><code id="js-code" class="language-javascript"><?php echo esc_html( $this->generate_javascript_example( $selected ) ); ?></code></pre>
								</div>

								<div class="nexus-code-example" data-lang="python">
									<button type="button" class="nexus-copy-code" data-clipboard-target="#python-code">
										<span class="dashicons dashicons-clipboard"></span>
									</button>
									<pre><code id="python-code" class="language-python"><?php echo esc_html( $this->generate_python_example( $selected ) ); ?></code></pre>
								</div>
							</div>
						</div>
					<?php else : ?>
						<div class="nexus-empty-state-large">
							<span class="dashicons dashicons-rest-api"></span>
							<h2><?php esc_html_e( 'Select an endpoint to test', 'nexus-pro' ); ?></h2>
							<p><?php esc_html_e( 'Choose an endpoint from the sidebar to view details and test it.', 'nexus-pro' ); ?></p>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX: Test endpoint
	 */
	public function ajax_test_endpoint() {
		check_ajax_referer( 'nexus-api-docs', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
		}

		$endpoint_id = isset( $_POST['endpoint_id'] ) ? sanitize_text_field( $_POST['endpoint_id'] ) : '';
		$params      = isset( $_POST['params'] ) ? (array) $_POST['params'] : array();
		$headers     = isset( $_POST['headers'] ) ? sanitize_textarea_field( $_POST['headers'] ) : '';
		$body        = isset( $_POST['body'] ) ? sanitize_textarea_field( $_POST['body'] ) : '';

		$endpoint_manager = Nexus_Endpoint_Manager::get_instance();
		$endpoint         = $endpoint_manager->get_endpoint( $endpoint_id );

		if ( ! $endpoint ) {
			wp_send_json_error( array( 'message' => __( 'Endpoint not found.', 'nexus-pro' ) ) );
		}

		// Parse headers
		$request_headers = array();
		if ( ! empty( $headers ) ) {
			$header_lines = explode( "\n", $headers );
			foreach ( $header_lines as $line ) {
				if ( str_contains( $line, ':' ) ) {
					list( $key, $value ) = explode( ':', $line, 2 );
					$request_headers[ trim( $key ) ] = trim( $value );
				}
			}
		}

		// Build request
		$url = rest_url( $endpoint['path'] );
		
		if ( ! empty( $params ) && $endpoint['method'] === 'GET' ) {
			$url = add_query_arg( $params, $url );
		}

		$start_time = microtime( true );

		$args = array(
			'method'  => $endpoint['method'],
			'headers' => $request_headers,
			'timeout' => 30,
		);

		if ( ! empty( $body ) && in_array( $endpoint['method'], array( 'POST', 'PUT', 'PATCH' ), true ) ) {
			$args['body'] = $body;
		}

		$response = wp_remote_request( $url, $args );

		$end_time = microtime( true );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error(
				array(
					'message' => $response->get_error_message(),
				)
			);
		}

		$response_time = round( ( $end_time - $start_time ) * 1000, 2 );

		wp_send_json_success(
			array(
				'status'   => wp_remote_retrieve_response_code( $response ),
				'headers'  => wp_remote_retrieve_headers( $response )->getAll(),
				'body'     => wp_remote_retrieve_body( $response ),
				'time'     => $response_time,
				'size'     => strlen( wp_remote_retrieve_body( $response ) ),
			)
		);
	}

	/**
	 * AJAX: Save test
	 */
	public function ajax_save_test() {
		check_ajax_referer( 'nexus-api-docs', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
		}

		$test_data = isset( $_POST['test'] ) ? json_decode( stripslashes( $_POST['test'] ), true ) : array();

		if ( empty( $test_data ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid test data.', 'nexus-pro' ) ) );
		}

		$saved_tests = get_option( 'nexus_api_saved_tests', array() );
		$test_data['id'] = uniqid( 'test_' );
		$test_data['created'] = current_time( 'mysql' );

		$saved_tests[] = $test_data;

		update_option( 'nexus_api_saved_tests', $saved_tests );

		wp_send_json_success(
			array(
				'message' => __( 'Test saved successfully!', 'nexus-pro' ),
				'test_id' => $test_data['id'],
			)
		);
	}

	/**
	 * Generate cURL example
	 *
	 * @param array $endpoint Endpoint data.
	 * @return string
	 */
	private function generate_curl_example( $endpoint ) {
		$url = rest_url( $endpoint['path'] );
		
		$example = sprintf( "curl -X %s \\\n", $endpoint['method'] );
		$example .= sprintf( "  '%s' \\\n", $url );
		$example .= "  -H 'Content-Type: application/json'";

		if ( in_array( $endpoint['method'], array( 'POST', 'PUT', 'PATCH' ), true ) ) {
			$example .= " \\\n  -d '{}'";
		}

		return $example;
	}

	/**
	 * Generate PHP example
	 *
	 * @param array $endpoint Endpoint data.
	 * @return string
	 */
	private function generate_php_example( $endpoint ) {
		$example  = "<?php\n";
		$example .= sprintf( "\$response = wp_remote_%s(\n", strtolower( $endpoint['method'] ) );
		$example .= sprintf( "    '%s',\n", rest_url( $endpoint['path'] ) );
		$example .= "    array(\n";
		$example .= "        'headers' => array('Content-Type' => 'application/json'),\n";

		if ( in_array( $endpoint['method'], array( 'POST', 'PUT', 'PATCH' ), true ) ) {
			$example .= "        'body' => json_encode(array()),\n";
		}

		$example .= "    )\n";
		$example .= ");\n\n";
		$example .= "\$body = wp_remote_retrieve_body(\$response);";

		return $example;
	}

	/**
	 * Generate JavaScript example
	 *
	 * @param array $endpoint Endpoint data.
	 * @return string
	 */
	private function generate_javascript_example( $endpoint ) {
		$example  = sprintf( "fetch('%s', {\n", rest_url( $endpoint['path'] ) );
		$example .= sprintf( "  method: '%s',\n", $endpoint['method'] );
		$example .= "  headers: {\n";
		$example .= "    'Content-Type': 'application/json'\n";
		$example .= "  }";

		if ( in_array( $endpoint['method'], array( 'POST', 'PUT', 'PATCH' ), true ) ) {
			$example .= ",\n  body: JSON.stringify({})";
		}

		$example .= "\n})\n";
		$example .= ".then(response => response.json())\n";
		$example .= ".then(data => console.log(data));";

		return $example;
	}

	/**
	 * Generate Python example
	 *
	 * @param array $endpoint Endpoint data.
	 * @return string
	 */
	private function generate_python_example( $endpoint ) {
		$example  = "import requests\n\n";
		$example .= sprintf( "response = requests.%s(\n", strtolower( $endpoint['method'] ) );
		$example .= sprintf( "    '%s',\n", rest_url( $endpoint['path'] ) );
		$example .= "    headers={'Content-Type': 'application/json'}";

		if ( in_array( $endpoint['method'], array( 'POST', 'PUT', 'PATCH' ), true ) ) {
			$example .= ",\n    json={}";
		}

		$example .= "\n)\n\n";
		$example .= "print(response.json())";

		return $example;
	}
}
