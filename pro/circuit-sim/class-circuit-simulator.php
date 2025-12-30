<?php
/**
 * Circuit Simulator - Main Class
 *
 * @package Nexus_Pro
 * @subpackage Circuit_Simulator
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Circuit Simulator Main Class
 *
 * Provides interactive circuit design and simulation
 */
class Nexus_Circuit_Simulator {

	/**
	 * Instance
	 *
	 * @var Nexus_Circuit_Simulator
	 */
	private static $instance = null;

	/**
	 * Component library instance
	 *
	 * @var Nexus_Component_Library
	 */
	private $component_library;

	/**
	 * Simulation engine instance
	 *
	 * @var Nexus_Simulation_Engine
	 */
	private $simulation_engine;

	/**
	 * Get instance
	 *
	 * @return Nexus_Circuit_Simulator
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
		$this->component_library = Nexus_Component_Library::get_instance();
		$this->simulation_engine = Nexus_Simulation_Engine::get_instance();

		// Hooks
		add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		
		// AJAX endpoints
		add_action( 'wp_ajax_nexus_save_circuit', array( $this, 'save_circuit' ) );
		add_action( 'wp_ajax_nexus_load_circuit', array( $this, 'load_circuit' ) );
		add_action( 'wp_ajax_nexus_simulate_circuit', array( $this, 'simulate_circuit' ) );
		add_action( 'wp_ajax_nexus_export_circuit', array( $this, 'export_circuit' ) );
		add_action( 'wp_ajax_nexus_delete_circuit', array( $this, 'delete_circuit' ) );

		// Register post type for saved circuits
		add_action( 'init', array( $this, 'register_circuit_post_type' ) );
	}

	/**
	 * Load dependencies
	 */
	private function load_dependencies() {
		require_once NEXUS_PRO_DIR . '/circuit-sim/class-component-library.php';
		require_once NEXUS_PRO_DIR . '/circuit-sim/class-simulation-engine.php';
	}

	/**
	 * Add admin page
	 */
	public function add_admin_page() {
		add_submenu_page(
			'themes.php',
			__( 'Circuit Simulator', 'nexus-pro' ),
			__( 'Circuit Simulator', 'nexus-pro' ),
			'edit_theme_options',
			'nexus-circuit-simulator',
			array( $this, 'render_admin_page' )
		);
	}

	/**
	 * Enqueue assets
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_assets( $hook ) {
		if ( $hook !== 'appearance_page_nexus-circuit-simulator' ) {
			return;
		}

		// Styles
		wp_enqueue_style(
			'nexus-circuit-sim',
			NEXUS_PRO_URL . 'assets/css/circuit-sim.css',
			array(),
			'3.0.0'
		);

		// Scripts
		wp_enqueue_script(
			'nexus-circuit-engine',
			NEXUS_PRO_URL . 'assets/js/libs/circuit-engine.js',
			array(),
			'3.0.0',
			true
		);

		wp_enqueue_script(
			'nexus-circuit-sim',
			NEXUS_PRO_URL . 'assets/js/circuit-sim.js',
			array( 'jquery', 'nexus-circuit-engine' ),
			'3.0.0',
			true
		);

		// Localize script
		wp_localize_script( 'nexus-circuit-sim', 'nexusCircuitSim', array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'nexus-circuit-sim' ),
			'components' => $this->component_library->get_all_components(),
			'i18n'       => array(
				'saveSuccess'      => __( 'Circuit saved successfully!', 'nexus-pro' ),
				'saveError'        => __( 'Error saving circuit.', 'nexus-pro' ),
				'simulateSuccess'  => __( 'Simulation complete!', 'nexus-pro' ),
				'simulateError'    => __( 'Simulation failed.', 'nexus-pro' ),
				'exportSuccess'    => __( 'Circuit exported!', 'nexus-pro' ),
				'deleteConfirm'    => __( 'Are you sure you want to delete this circuit?', 'nexus-pro' ),
			),
		) );
	}

	/**
	 * Render admin page
	 */
	public function render_admin_page() {
		?>
		<div class="wrap nexus-circuit-simulator-wrap">
			<h1><?php esc_html_e( 'Circuit Simulator', 'nexus-pro' ); ?></h1>

			<div class="nexus-circuit-toolbar">
				<div class="toolbar-left">
					<button type="button" class="button" id="new-circuit">
						<span class="dashicons dashicons-plus"></span>
						<?php esc_html_e( 'New Circuit', 'nexus-pro' ); ?>
					</button>
					<button type="button" class="button" id="load-circuit">
						<span class="dashicons dashicons-upload"></span>
						<?php esc_html_e( 'Load', 'nexus-pro' ); ?>
					</button>
					<button type="button" class="button button-primary" id="save-circuit">
						<span class="dashicons dashicons-saved"></span>
						<?php esc_html_e( 'Save', 'nexus-pro' ); ?>
					</button>
				</div>

				<div class="toolbar-center">
					<button type="button" class="button" id="undo-action">
						<span class="dashicons dashicons-undo"></span>
					</button>
					<button type="button" class="button" id="redo-action">
						<span class="dashicons dashicons-redo"></span>
					</button>
					<span class="toolbar-divider"></span>
					<button type="button" class="button" id="zoom-in">
						<span class="dashicons dashicons-plus-alt"></span>
					</button>
					<button type="button" class="button" id="zoom-out">
						<span class="dashicons dashicons-minus"></span>
					</button>
					<button type="button" class="button" id="zoom-reset">
						<span class="dashicons dashicons-image-crop"></span>
					</button>
				</div>

				<div class="toolbar-right">
					<button type="button" class="button button-secondary" id="simulate-circuit">
						<span class="dashicons dashicons-controls-play"></span>
						<?php esc_html_e( 'Simulate', 'nexus-pro' ); ?>
					</button>
					<button type="button" class="button" id="export-circuit">
						<span class="dashicons dashicons-download"></span>
						<?php esc_html_e( 'Export', 'nexus-pro' ); ?>
					</button>
				</div>
			</div>

			<div class="nexus-circuit-container">
				<!-- Component Library Sidebar -->
				<div class="circuit-sidebar">
					<h3><?php esc_html_e( 'Components', 'nexus-pro' ); ?></h3>
					
					<div class="component-search">
						<input type="text" id="component-search" placeholder="<?php esc_attr_e( 'Search components...', 'nexus-pro' ); ?>">
					</div>

					<div class="component-categories">
						<?php $this->render_component_library(); ?>
					</div>
				</div>

				<!-- Circuit Canvas -->
				<div class="circuit-canvas-wrapper">
					<canvas id="circuit-canvas" width="1200" height="800"></canvas>
					<div class="canvas-grid"></div>
				</div>

				<!-- Properties Panel -->
				<div class="circuit-properties">
					<h3><?php esc_html_e( 'Properties', 'nexus-pro' ); ?></h3>
					<div id="component-properties">
						<p class="no-selection"><?php esc_html_e( 'Select a component to edit properties', 'nexus-pro' ); ?></p>
					</div>

					<div class="simulation-results">
						<h3><?php esc_html_e( 'Simulation Results', 'nexus-pro' ); ?></h3>
						<div id="simulation-output">
							<p class="no-results"><?php esc_html_e( 'Run simulation to see results', 'nexus-pro' ); ?></p>
						</div>
					</div>
				</div>
			</div>

			<!-- Saved Circuits -->
			<div class="nexus-saved-circuits">
				<h2><?php esc_html_e( 'Saved Circuits', 'nexus-pro' ); ?></h2>
				<div class="circuits-grid">
					<?php $this->render_saved_circuits(); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render component library
	 */
	private function render_component_library() {
		$categories = $this->component_library->get_categories();

		foreach ( $categories as $category_id => $category ) {
			?>
			<div class="component-category" data-category="<?php echo esc_attr( $category_id ); ?>">
				<h4 class="category-title">
					<span class="dashicons <?php echo esc_attr( $category['icon'] ); ?>"></span>
					<?php echo esc_html( $category['name'] ); ?>
				</h4>
				<div class="category-components">
					<?php foreach ( $category['components'] as $component ) : ?>
						<div class="component-item" 
						     data-type="<?php echo esc_attr( $component['type'] ); ?>"
						     data-name="<?php echo esc_attr( $component['name'] ); ?>"
						     title="<?php echo esc_attr( $component['description'] ); ?>">
							<span class="component-icon"><?php echo $component['symbol']; ?></span>
							<span class="component-name"><?php echo esc_html( $component['name'] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Render saved circuits
	 */
	private function render_saved_circuits() {
		$circuits = get_posts( array(
			'post_type'      => 'nexus_circuit',
			'posts_per_page' => -1,
			'orderby'        => 'modified',
			'order'          => 'DESC',
		) );

		if ( empty( $circuits ) ) {
			echo '<p class="no-circuits">' . esc_html__( 'No saved circuits yet. Create your first circuit!', 'nexus-pro' ) . '</p>';
			return;
		}

		foreach ( $circuits as $circuit ) {
			$thumbnail = get_post_meta( $circuit->ID, '_circuit_thumbnail', true );
			?>
			<div class="circuit-card" data-circuit-id="<?php echo esc_attr( $circuit->ID ); ?>">
				<div class="circuit-thumbnail">
					<?php if ( $thumbnail ) : ?>
						<img src="<?php echo esc_url( $thumbnail ); ?>" alt="">
					<?php else : ?>
						<span class="dashicons dashicons-admin-settings"></span>
					<?php endif; ?>
				</div>
				<div class="circuit-info">
					<h4><?php echo esc_html( $circuit->post_title ); ?></h4>
					<p class="circuit-date"><?php echo esc_html( get_the_modified_date( '', $circuit ) ); ?></p>
				</div>
				<div class="circuit-actions">
					<button type="button" class="button button-small load-circuit-btn" data-id="<?php echo esc_attr( $circuit->ID ); ?>">
						<?php esc_html_e( 'Load', 'nexus-pro' ); ?>
					</button>
					<button type="button" class="button button-small delete-circuit-btn" data-id="<?php echo esc_attr( $circuit->ID ); ?>">
						<?php esc_html_e( 'Delete', 'nexus-pro' ); ?>
					</button>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Register circuit post type
	 */
	public function register_circuit_post_type() {
		register_post_type( 'nexus_circuit', array(
			'labels'              => array(
				'name'          => __( 'Circuits', 'nexus-pro' ),
				'singular_name' => __( 'Circuit', 'nexus-pro' ),
			),
			'public'              => false,
			'show_ui'             => false,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'exclude_from_search' => true,
		) );
	}

	/**
	 * Save circuit
	 */
	public function save_circuit() {
		check_ajax_referer( 'nexus-circuit-sim', 'nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
		}

		$circuit_id   = isset( $_POST['circuit_id'] ) ? absint( $_POST['circuit_id'] ) : 0;
		$circuit_name = isset( $_POST['circuit_name'] ) ? sanitize_text_field( $_POST['circuit_name'] ) : 'Untitled Circuit';
		$circuit_data = isset( $_POST['circuit_data'] ) ? json_decode( stripslashes( $_POST['circuit_data'] ), true ) : array();
		$thumbnail    = isset( $_POST['thumbnail'] ) ? sanitize_text_field( $_POST['thumbnail'] ) : '';

		if ( empty( $circuit_data ) ) {
			wp_send_json_error( array( 'message' => __( 'No circuit data provided.', 'nexus-pro' ) ) );
		}

		$post_data = array(
			'post_title'  => $circuit_name,
			'post_type'   => 'nexus_circuit',
			'post_status' => 'publish',
		);

		if ( $circuit_id ) {
			$post_data['ID'] = $circuit_id;
			$result = wp_update_post( $post_data );
		} else {
			$result = wp_insert_post( $post_data );
		}

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		// Save circuit data
		update_post_meta( $result, '_circuit_data', $circuit_data );
		update_post_meta( $result, '_circuit_thumbnail', $thumbnail );

		wp_send_json_success( array(
			'message'    => __( 'Circuit saved successfully!', 'nexus-pro' ),
			'circuit_id' => $result,
		) );
	}

	/**
	 * Load circuit
	 */
	public function load_circuit() {
		check_ajax_referer( 'nexus-circuit-sim', 'nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
		}

		$circuit_id = isset( $_POST['circuit_id'] ) ? absint( $_POST['circuit_id'] ) : 0;

		if ( ! $circuit_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid circuit ID.', 'nexus-pro' ) ) );
		}

		$circuit_data = get_post_meta( $circuit_id, '_circuit_data', true );
		$circuit_name = get_the_title( $circuit_id );

		wp_send_json_success( array(
			'circuit_data' => $circuit_data,
			'circuit_name' => $circuit_name,
		) );
	}

	/**
	 * Simulate circuit
	 */
	public function simulate_circuit() {
		check_ajax_referer( 'nexus-circuit-sim', 'nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
		}

		$circuit_data = isset( $_POST['circuit_data'] ) ? json_decode( stripslashes( $_POST['circuit_data'] ), true ) : array();

		if ( empty( $circuit_data ) ) {
			wp_send_json_error( array( 'message' => __( 'No circuit data provided.', 'nexus-pro' ) ) );
		}

		// Run simulation
		$results = $this->simulation_engine->simulate( $circuit_data );

		wp_send_json_success( array(
			'results' => $results,
		) );
	}

	/**
	 * Export circuit
	 */
	public function export_circuit() {
		check_ajax_referer( 'nexus-circuit-sim', 'nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
		}

		$circuit_data = isset( $_POST['circuit_data'] ) ? json_decode( stripslashes( $_POST['circuit_data'] ), true ) : array();
		$format       = isset( $_POST['format'] ) ? sanitize_text_field( $_POST['format'] ) : 'json';

		if ( empty( $circuit_data ) ) {
			wp_send_json_error( array( 'message' => __( 'No circuit data provided.', 'nexus-pro' ) ) );
		}

		$export_data = array(
			'format'  => $format,
			'data'    => $circuit_data,
			'message' => __( 'Circuit exported successfully!', 'nexus-pro' ),
		);

		wp_send_json_success( $export_data );
	}

	/**
	 * Delete circuit
	 */
	public function delete_circuit() {
		check_ajax_referer( 'nexus-circuit-sim', 'nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
		}

		$circuit_id = isset( $_POST['circuit_id'] ) ? absint( $_POST['circuit_id'] ) : 0;

		if ( ! $circuit_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid circuit ID.', 'nexus-pro' ) ) );
		}

		$result = wp_delete_post( $circuit_id, true );

		if ( ! $result ) {
			wp_send_json_error( array( 'message' => __( 'Error deleting circuit.', 'nexus-pro' ) ) );
		}

		wp_send_json_success( array( 'message' => __( 'Circuit deleted successfully!', 'nexus-pro' ) ) );
	}
}

// Initialize
Nexus_Circuit_Simulator::get_instance();
