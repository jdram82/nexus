<?php
/**
 * API Documentation Generator - Main Class
 *
 * @package Nexus_Pro
 * @subpackage API_Docs
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main API Documentation Class
 *
 * Provides automatic API documentation generation from code
 */
class Nexus_API_Docs {

	/**
	 * Instance
	 *
	 * @var Nexus_API_Docs
	 */
	private static $instance = null;

	/**
	 * Code parser instance
	 *
	 * @var Nexus_Code_Parser
	 */
	private $parser;

	/**
	 * API explorer instance
	 *
	 * @var Nexus_API_Explorer
	 */
	private $explorer;

	/**
	 * Endpoint manager instance
	 *
	 * @var Nexus_Endpoint_Manager
	 */
	private $endpoint_manager;

	/**
	 * Get instance
	 *
	 * @return Nexus_API_Docs
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
		// Load dependencies
		$this->load_dependencies();

		// Initialize components
		$this->parser           = Nexus_Code_Parser::get_instance();
		$this->explorer         = Nexus_API_Explorer::get_instance();
		$this->endpoint_manager = Nexus_Endpoint_Manager::get_instance();

		// Admin hooks
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		// AJAX hooks
		add_action( 'wp_ajax_nexus_generate_docs', array( $this, 'ajax_generate_docs' ) );
		add_action( 'wp_ajax_nexus_parse_file', array( $this, 'ajax_parse_file' ) );
		add_action( 'wp_ajax_nexus_save_endpoint', array( $this, 'ajax_save_endpoint' ) );
		add_action( 'wp_ajax_nexus_export_docs', array( $this, 'ajax_export_docs' ) );

		// REST API hooks
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * Load dependencies
	 */
	private function load_dependencies() {
		require_once NEXUS_PRO_PATH . 'api-docs/class-code-parser.php';
		require_once NEXUS_PRO_PATH . 'api-docs/class-api-explorer.php';
		require_once NEXUS_PRO_PATH . 'api-docs/class-endpoint-manager.php';
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'API Documentation', 'nexus-pro' ),
			__( 'API Docs', 'nexus-pro' ),
			'manage_options',
			'nexus-api-docs',
			array( $this, 'render_admin_page' ),
			'dashicons-book',
			30
		);

		add_submenu_page(
			'nexus-api-docs',
			__( 'API Explorer', 'nexus-pro' ),
			__( 'Explorer', 'nexus-pro' ),
			'manage_options',
			'nexus-api-explorer',
			array( $this->explorer, 'render_page' )
		);

		add_submenu_page(
			'nexus-api-docs',
			__( 'Endpoints', 'nexus-pro' ),
			__( 'Endpoints', 'nexus-pro' ),
			'manage_options',
			'nexus-api-endpoints',
			array( $this->endpoint_manager, 'render_page' )
		);
	}

	/**
	 * Enqueue admin assets
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( ! str_contains( $hook, 'nexus-api' ) ) {
			return;
		}

		// Enqueue syntax highlighter
		wp_enqueue_style( 'prism', 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css', array(), '1.29.0' );
		wp_enqueue_script( 'prism', 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js', array(), '1.29.0', true );
		wp_enqueue_script( 'prism-php', 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js', array( 'prism' ), '1.29.0', true );
		wp_enqueue_script( 'prism-javascript', 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js', array( 'prism' ), '1.29.0', true );
		wp_enqueue_script( 'prism-python', 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-python.min.js', array( 'prism' ), '1.29.0', true );

		// API Docs styles
		wp_enqueue_style(
			'nexus-api-docs',
			NEXUS_PRO_URL . 'assets/css/api-docs.css',
			array(),
			'3.0.0'
		);

		// API Docs script
		wp_enqueue_script(
			'nexus-api-docs',
			NEXUS_PRO_URL . 'assets/js/api-docs.js',
			array( 'jquery', 'prism' ),
			'3.0.0',
			true
		);

		wp_localize_script(
			'nexus-api-docs',
			'nexusApiDocs',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'restUrl'  => rest_url( 'nexus/v1/' ),
				'nonce'    => wp_create_nonce( 'nexus-api-docs' ),
				'siteUrl'  => get_site_url(),
				'i18n'     => array(
					'generating'     => __( 'Generating documentation...', 'nexus-pro' ),
					'success'        => __( 'Documentation generated successfully!', 'nexus-pro' ),
					'error'          => __( 'Error generating documentation.', 'nexus-pro' ),
					'parsing'        => __( 'Parsing file...', 'nexus-pro' ),
					'exportSuccess'  => __( 'Documentation exported successfully!', 'nexus-pro' ),
					'confirmDelete'  => __( 'Are you sure you want to delete this endpoint?', 'nexus-pro' ),
				),
			)
		);
	}

	/**
	 * Render admin page
	 */
	public function render_admin_page() {
		$endpoints = $this->endpoint_manager->get_all_endpoints();
		$stats     = $this->get_documentation_stats();
		?>
		<div class="wrap nexus-api-docs-wrap">
			<h1><?php esc_html_e( 'API Documentation Generator', 'nexus-pro' ); ?></h1>

			<!-- Stats Dashboard -->
			<div class="nexus-api-stats">
				<div class="nexus-stat-card">
					<div class="nexus-stat-icon dashicons dashicons-rest-api"></div>
					<div class="nexus-stat-content">
						<div class="nexus-stat-value"><?php echo esc_html( $stats['total_endpoints'] ); ?></div>
						<div class="nexus-stat-label"><?php esc_html_e( 'API Endpoints', 'nexus-pro' ); ?></div>
					</div>
				</div>

				<div class="nexus-stat-card">
					<div class="nexus-stat-icon dashicons dashicons-editor-code"></div>
					<div class="nexus-stat-content">
						<div class="nexus-stat-value"><?php echo esc_html( $stats['parsed_files'] ); ?></div>
						<div class="nexus-stat-label"><?php esc_html_e( 'Parsed Files', 'nexus-pro' ); ?></div>
					</div>
				</div>

				<div class="nexus-stat-card">
					<div class="nexus-stat-icon dashicons dashicons-book-alt"></div>
					<div class="nexus-stat-content">
						<div class="nexus-stat-value"><?php echo esc_html( $stats['total_methods'] ); ?></div>
						<div class="nexus-stat-label"><?php esc_html_e( 'Documented Methods', 'nexus-pro' ); ?></div>
					</div>
				</div>

				<div class="nexus-stat-card">
					<div class="nexus-stat-icon dashicons dashicons-update"></div>
					<div class="nexus-stat-content">
						<div class="nexus-stat-value"><?php echo esc_html( $stats['last_generated'] ); ?></div>
						<div class="nexus-stat-label"><?php esc_html_e( 'Last Generated', 'nexus-pro' ); ?></div>
					</div>
				</div>
			</div>

			<!-- Generate Documentation -->
			<div class="nexus-api-generate">
				<h2><?php esc_html_e( 'Generate Documentation', 'nexus-pro' ); ?></h2>
				
				<div class="nexus-generate-form">
					<div class="nexus-form-field">
						<label for="nexus-scan-path"><?php esc_html_e( 'Scan Path:', 'nexus-pro' ); ?></label>
						<input type="text" id="nexus-scan-path" class="regular-text" value="<?php echo esc_attr( get_template_directory() ); ?>" />
						<p class="description"><?php esc_html_e( 'Enter the directory path to scan for code files', 'nexus-pro' ); ?></p>
					</div>

					<div class="nexus-form-field">
						<label><?php esc_html_e( 'File Types:', 'nexus-pro' ); ?></label>
						<label><input type="checkbox" name="file-types[]" value="php" checked /> PHP</label>
						<label><input type="checkbox" name="file-types[]" value="js" checked /> JavaScript</label>
						<label><input type="checkbox" name="file-types[]" value="py" /> Python</label>
					</div>

					<div class="nexus-form-field">
						<label><input type="checkbox" id="nexus-include-private" /> <?php esc_html_e( 'Include private methods', 'nexus-pro' ); ?></label>
						<label><input type="checkbox" id="nexus-generate-examples" checked /> <?php esc_html_e( 'Generate code examples', 'nexus-pro' ); ?></label>
					</div>

					<button type="button" class="button button-primary button-large" id="nexus-generate-docs-btn">
						<span class="dashicons dashicons-update"></span>
						<?php esc_html_e( 'Generate Documentation', 'nexus-pro' ); ?>
					</button>

					<button type="button" class="button button-secondary" id="nexus-export-docs-btn">
						<span class="dashicons dashicons-download"></span>
						<?php esc_html_e( 'Export Documentation', 'nexus-pro' ); ?>
					</button>
				</div>

				<div id="nexus-generation-progress" class="nexus-progress-bar" style="display: none;">
					<div class="nexus-progress-fill"></div>
					<div class="nexus-progress-text"></div>
				</div>
			</div>

			<!-- Recent Endpoints -->
			<div class="nexus-recent-endpoints">
				<h2><?php esc_html_e( 'Recent API Endpoints', 'nexus-pro' ); ?></h2>
				<?php if ( ! empty( $endpoints ) ) : ?>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Method', 'nexus-pro' ); ?></th>
								<th><?php esc_html_e( 'Endpoint', 'nexus-pro' ); ?></th>
								<th><?php esc_html_e( 'Description', 'nexus-pro' ); ?></th>
								<th><?php esc_html_e( 'Version', 'nexus-pro' ); ?></th>
								<th><?php esc_html_e( 'Actions', 'nexus-pro' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( array_slice( $endpoints, 0, 10 ) as $endpoint ) : ?>
								<tr>
									<td><span class="nexus-method-badge nexus-method-<?php echo esc_attr( strtolower( $endpoint['method'] ) ); ?>"><?php echo esc_html( $endpoint['method'] ); ?></span></td>
									<td><code><?php echo esc_html( $endpoint['path'] ); ?></code></td>
									<td><?php echo esc_html( $endpoint['description'] ); ?></td>
									<td><?php echo esc_html( $endpoint['version'] ?? 'v1' ); ?></td>
									<td>
										<a href="<?php echo esc_url( admin_url( 'admin.php?page=nexus-api-explorer&endpoint=' . urlencode( $endpoint['id'] ) ) ); ?>" class="button button-small">
											<?php esc_html_e( 'Test', 'nexus-pro' ); ?>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else : ?>
					<p class="nexus-empty-state">
						<?php esc_html_e( 'No endpoints found. Generate documentation to get started.', 'nexus-pro' ); ?>
					</p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Get documentation stats
	 *
	 * @return array
	 */
	private function get_documentation_stats() {
		$endpoints = $this->endpoint_manager->get_all_endpoints();
		$meta      = get_option( 'nexus_api_docs_meta', array() );

		return array(
			'total_endpoints' => count( $endpoints ),
			'parsed_files'    => $meta['parsed_files'] ?? 0,
			'total_methods'   => $meta['total_methods'] ?? 0,
			'last_generated'  => ! empty( $meta['last_generated'] ) ? human_time_diff( $meta['last_generated'], current_time( 'timestamp' ) ) . ' ago' : __( 'Never', 'nexus-pro' ),
		);
	}

	/**
	 * AJAX: Generate documentation
	 */
	public function ajax_generate_docs() {
		check_ajax_referer( 'nexus-api-docs', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
		}

		$scan_path        = isset( $_POST['scan_path'] ) ? sanitize_text_field( $_POST['scan_path'] ) : get_template_directory();
		$file_types       = isset( $_POST['file_types'] ) ? array_map( 'sanitize_text_field', (array) $_POST['file_types'] ) : array( 'php', 'js' );
		$include_private  = isset( $_POST['include_private'] ) && $_POST['include_private'] === 'true';
		$generate_examples = isset( $_POST['generate_examples'] ) && $_POST['generate_examples'] === 'true';

		try {
			$result = $this->parser->parse_directory(
				$scan_path,
				array(
					'file_types'        => $file_types,
					'include_private'   => $include_private,
					'generate_examples' => $generate_examples,
				)
			);

			// Update meta
			update_option(
				'nexus_api_docs_meta',
				array(
					'parsed_files'   => $result['files_parsed'],
					'total_methods'  => $result['methods_found'],
					'last_generated' => current_time( 'timestamp' ),
				)
			);

			wp_send_json_success(
				array(
					'message'       => __( 'Documentation generated successfully!', 'nexus-pro' ),
					'files_parsed'  => $result['files_parsed'],
					'methods_found' => $result['methods_found'],
				)
			);
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'message' => $e->getMessage() ) );
		}
	}

	/**
	 * AJAX: Parse file
	 */
	public function ajax_parse_file() {
		check_ajax_referer( 'nexus-api-docs', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
		}

		$file_path = isset( $_POST['file_path'] ) ? sanitize_text_field( $_POST['file_path'] ) : '';

		if ( empty( $file_path ) || ! file_exists( $file_path ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid file path.', 'nexus-pro' ) ) );
		}

		try {
			$result = $this->parser->parse_file( $file_path );
			wp_send_json_success( $result );
		} catch ( Exception $e ) {
			wp_send_json_error( array( 'message' => $e->getMessage() ) );
		}
	}

	/**
	 * AJAX: Save endpoint
	 */
	public function ajax_save_endpoint() {
		check_ajax_referer( 'nexus-api-docs', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
		}

		$endpoint_data = isset( $_POST['endpoint'] ) ? json_decode( stripslashes( $_POST['endpoint'] ), true ) : array();

		if ( empty( $endpoint_data ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid endpoint data.', 'nexus-pro' ) ) );
		}

		$result = $this->endpoint_manager->save_endpoint( $endpoint_data );

		if ( $result ) {
			wp_send_json_success( array( 'message' => __( 'Endpoint saved successfully!', 'nexus-pro' ) ) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Error saving endpoint.', 'nexus-pro' ) ) );
		}
	}

	/**
	 * AJAX: Export documentation
	 */
	public function ajax_export_docs() {
		check_ajax_referer( 'nexus-api-docs', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
		}

		$format = isset( $_POST['format'] ) ? sanitize_text_field( $_POST['format'] ) : 'json';

		$endpoints = $this->endpoint_manager->get_all_endpoints();

		$export_data = array(
			'version'   => '1.0.0',
			'generated' => current_time( 'mysql' ),
			'endpoints' => $endpoints,
		);

		if ( $format === 'json' ) {
			wp_send_json_success(
				array(
					'data'     => $export_data,
					'filename' => 'api-docs-' . date( 'Y-m-d' ) . '.json',
				)
			);
		}

		// Future: Add support for Markdown, HTML, OpenAPI formats
	}

	/**
	 * Register REST API routes
	 */
	public function register_rest_routes() {
		register_rest_route(
			'nexus/v1',
			'/docs',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_get_docs' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'nexus/v1',
			'/docs/endpoints',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_get_endpoints' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * REST: Get documentation
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function rest_get_docs( $request ) {
		$endpoints = $this->endpoint_manager->get_all_endpoints();

		return rest_ensure_response(
			array(
				'version'   => '1.0.0',
				'endpoints' => $endpoints,
			)
		);
	}

	/**
	 * REST: Get endpoints
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function rest_get_endpoints( $request ) {
		$endpoints = $this->endpoint_manager->get_all_endpoints();
		return rest_ensure_response( $endpoints );
	}
}

// Initialize
Nexus_API_Docs::get_instance();
