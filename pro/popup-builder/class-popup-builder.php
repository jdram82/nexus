<?php
/**
 * Popup Builder - Create and Manage Popups
 *
 * Visual popup builder with triggers, targeting rules, and analytics.
 * Similar to Elementor PRO popup functionality.
 *
 * @package Nexus_Pro
 * @subpackage Popup_Builder
 * @since 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Popup Builder Class
 *
 * Handles popup post type registration, admin interface,
 * and popup display management.
 */
class Nexus_Popup_Builder {

	/**
	 * Builder version
	 *
	 * @var string
	 */
	const VERSION = '3.2.0';

	/**
	 * Post type for popups
	 *
	 * @var string
	 */
	const POST_TYPE = 'nexus_popup';

	/**
	 * Instance of this class
	 *
	 * @var Nexus_Popup_Builder
	 */
	private static $instance = null;

	/**
	 * Trigger manager instance
	 *
	 * @var Nexus_Popup_Triggers
	 */
	public $triggers;

	/**
	 * Targeting manager instance
	 *
	 * @var Nexus_Popup_Targeting
	 */
	public $targeting;

	/**
	 * Editor instance
	 *
	 * @var Nexus_Popup_Editor
	 */
	public $editor;

	/**
	 * Active popups cache
	 *
	 * @var array
	 */
	private $active_popups = array();

	/**
	 * Get instance
	 *
	 * @return Nexus_Popup_Builder
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
		$this->load_dependencies();
		$this->init_hooks();
	}

	/**
	 * Load required files
	 */
	private function load_dependencies() {
		// Files are loaded by class-nexus-pro.php based on license tier
		// Only instantiate if classes exist
		if ( class_exists( 'Nexus_Popup_Triggers' ) ) {
			$this->triggers = Nexus_Popup_Triggers::get_instance();
		}
		if ( class_exists( 'Nexus_Popup_Targeting' ) ) {
			$this->targeting = Nexus_Popup_Targeting::get_instance();
		}
		if ( class_exists( 'Nexus_Popup_Editor' ) ) {
			$this->editor = Nexus_Popup_Editor::get_instance();
		}
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'wp_footer', array( $this, 'render_popups' ) );
		add_action( 'wp_ajax_nexus_save_popup', array( $this, 'save_popup_ajax' ) );
		add_action( 'wp_ajax_nexus_get_popup_stats', array( $this, 'get_popup_stats_ajax' ) );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'add_custom_columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );
	}

	/**
	 * Register popup post type
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => __( 'Popups', 'nexus-pro' ),
			'singular_name'         => __( 'Popup', 'nexus-pro' ),
			'add_new'               => __( 'Add New', 'nexus-pro' ),
			'add_new_item'          => __( 'Add New Popup', 'nexus-pro' ),
			'edit_item'             => __( 'Edit Popup', 'nexus-pro' ),
			'new_item'              => __( 'New Popup', 'nexus-pro' ),
			'view_item'             => __( 'View Popup', 'nexus-pro' ),
			'search_items'          => __( 'Search Popups', 'nexus-pro' ),
			'not_found'             => __( 'No popups found', 'nexus-pro' ),
			'not_found_in_trash'    => __( 'No popups found in trash', 'nexus-pro' ),
			'all_items'             => __( 'All Popups', 'nexus-pro' ),
			'menu_name'             => __( 'Popups', 'nexus-pro' ),
			'name_admin_bar'        => __( 'Popup', 'nexus-pro' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => false, // We'll add custom menu
			'query_var'           => true,
			'rewrite'             => array( 'slug' => 'popup' ),
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-welcome-view-site',
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'show_in_rest'        => true,
		);

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		// Check if license manager exists and has popup_builder feature
		if ( ! class_exists( 'Nexus_License_Manager' ) ) {
			return;
		}

		$license_manager = Nexus_License_Manager::instance();
		if ( ! $license_manager || ! $license_manager->has_feature( 'popup_builder' ) ) {
			return;
		}

		add_menu_page(
			__( 'Popup Builder', 'nexus-pro' ),
			__( 'Popups', 'nexus-pro' ),
			'edit_posts',
			'nexus-popup-builder',
			array( $this, 'render_popup_list' ),
			'dashicons-welcome-view-site',
			30
		);

		add_submenu_page(
			'nexus-popup-builder',
			__( 'All Popups', 'nexus-pro' ),
			__( 'All Popups', 'nexus-pro' ),
			'edit_posts',
			'edit.php?post_type=' . self::POST_TYPE
		);

		add_submenu_page(
			'nexus-popup-builder',
			__( 'Add New Popup', 'nexus-pro' ),
			__( 'Add New', 'nexus-pro' ),
			'edit_posts',
			'post-new.php?post_type=' . self::POST_TYPE
		);

		add_submenu_page(
			'nexus-popup-builder',
			__( 'Popup Templates', 'nexus-pro' ),
			__( 'Templates', 'nexus-pro' ),
			'edit_posts',
			'nexus-popup-templates',
			array( $this, 'render_templates_page' )
		);

		add_submenu_page(
			'nexus-popup-builder',
			__( 'Popup Analytics', 'nexus-pro' ),
			__( 'Analytics', 'nexus-pro' ),
			'edit_posts',
			'nexus-popup-analytics',
			array( $this, 'render_analytics_page' )
		);
	}

	/**
	 * Enqueue admin assets
	 */
	public function enqueue_admin_assets( $hook ) {
		$screen = get_current_screen();
		
		if ( ! $screen || ( $screen->post_type !== self::POST_TYPE && strpos( $hook, 'nexus-popup' ) === false ) ) {
			return;
		}

		wp_enqueue_style(
			'nexus-popup-builder-admin',
			NEXUS_PRO_URL . 'assets/css/popup-builder-admin.css',
			array(),
			self::VERSION
		);

		wp_enqueue_script(
			'nexus-popup-builder-admin',
			NEXUS_PRO_URL . 'assets/js/popup-builder-admin.js',
			array( 'jquery', 'wp-color-picker', 'jquery-ui-sortable' ),
			self::VERSION,
			true
		);

		wp_localize_script(
			'nexus-popup-builder-admin',
			'nexusPopupBuilder',
			array(
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'nonce'       => wp_create_nonce( 'nexus_popup_builder' ),
				'post_id'     => get_the_ID(),
				'i18n'        => array(
					'save_success' => __( 'Popup saved successfully', 'nexus-pro' ),
					'save_error'   => __( 'Error saving popup', 'nexus-pro' ),
					'delete_confirm' => __( 'Are you sure you want to delete this popup?', 'nexus-pro' ),
				),
			)
		);

		wp_enqueue_style( 'wp-color-picker' );
	}

	/**
	 * Enqueue frontend assets
	 */
	public function enqueue_frontend_assets() {
		if ( ! $this->should_load_popups() ) {
			return;
		}

		wp_enqueue_style(
			'nexus-popup-builder',
			NEXUS_PRO_URL . 'assets/css/popup-builder.css',
			array(),
			self::VERSION
		);

		wp_enqueue_script(
			'nexus-popup-builder',
			NEXUS_PRO_URL . 'assets/js/popup-builder.js',
			array( 'jquery' ),
			self::VERSION,
			true
		);

		wp_localize_script(
			'nexus-popup-builder',
			'nexusPopup',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'nexus_popup_frontend' ),
				'popups'   => $this->get_active_popups_data(),
			)
		);
	}

	/**
	 * Check if popups should load on current page
	 *
	 * @return bool
	 */
	private function should_load_popups() {
		// Don't load in admin
		if ( is_admin() ) {
			return false;
		}

		// Don't load for logged-in admins if debug mode
		if ( current_user_can( 'manage_options' ) && defined( 'NEXUS_POPUP_DEBUG' ) && NEXUS_POPUP_DEBUG ) {
			return false;
		}

		return true;
	}

	/**
	 * Get active popups for current page
	 *
	 * @return array
	 */
	private function get_active_popups_data() {
		$popups = $this->get_active_popups();
		$popup_data = array();

		foreach ( $popups as $popup ) {
			$triggers = get_post_meta( $popup->ID, '_nexus_popup_triggers', true );
			$settings = get_post_meta( $popup->ID, '_nexus_popup_settings', true );
			
			$popup_data[] = array(
				'id'       => $popup->ID,
				'triggers' => $triggers,
				'settings' => $settings,
				'content'  => $this->get_popup_content( $popup->ID ),
			);
		}

		return $popup_data;
	}

	/**
	 * Get active popups
	 *
	 * @return array
	 */
	private function get_active_popups() {
		if ( ! empty( $this->active_popups ) ) {
			return $this->active_popups;
		}

		$args = array(
			'post_type'      => self::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'   => '_nexus_popup_status',
					'value' => 'active',
				),
			),
		);

		$popups = get_posts( $args );

		// Filter by targeting rules
		$active_popups = array();
		foreach ( $popups as $popup ) {
			if ( $this->targeting->should_display_popup( $popup->ID ) ) {
				$active_popups[] = $popup;
			}
		}

		$this->active_popups = $active_popups;

		return $this->active_popups;
	}

	/**
	 * Get popup content
	 *
	 * @param int $popup_id Popup ID
	 * @return string
	 */
	private function get_popup_content( $popup_id ) {
		$popup = get_post( $popup_id );
		if ( ! $popup ) {
			return '';
		}

		// Get popup layout data
		$layout = get_post_meta( $popup_id, '_nexus_popup_layout', true );
		
		if ( $layout ) {
			// Render using template builder
			return $this->editor->render_popup_layout( $layout );
		}

		// Fallback to post content
		return apply_filters( 'the_content', $popup->post_content );
	}

	/**
	 * Render popups in footer
	 */
	public function render_popups() {
		if ( ! $this->should_load_popups() ) {
			return;
		}

		$popups = $this->get_active_popups();

		foreach ( $popups as $popup ) {
			$this->render_single_popup( $popup );
		}
	}

	/**
	 * Render single popup
	 *
	 * @param WP_Post $popup Popup post object
	 */
	private function render_single_popup( $popup ) {
		$settings = get_post_meta( $popup->ID, '_nexus_popup_settings', true );
		$settings = wp_parse_args(
			$settings,
			array(
				'width'           => '600px',
				'height'          => 'auto',
				'overlay_color'   => 'rgba(0,0,0,0.8)',
				'close_button'    => true,
				'esc_close'       => true,
				'overlay_close'   => true,
				'animation'       => 'fadeIn',
				'position'        => 'center',
			)
		);

		?>
		<div id="nexus-popup-<?php echo esc_attr( $popup->ID ); ?>" 
			 class="nexus-popup-overlay" 
			 data-popup-id="<?php echo esc_attr( $popup->ID ); ?>"
			 data-animation="<?php echo esc_attr( $settings['animation'] ); ?>"
			 style="display: none; background-color: <?php echo esc_attr( $settings['overlay_color'] ); ?>;">
			
			<div class="nexus-popup-container" 
				 style="max-width: <?php echo esc_attr( $settings['width'] ); ?>; 
				        height: <?php echo esc_attr( $settings['height'] ); ?>;">
				
				<?php if ( $settings['close_button'] ) : ?>
					<button class="nexus-popup-close" aria-label="<?php esc_attr_e( 'Close', 'nexus-pro' ); ?>">
						<span class="dashicons dashicons-no-alt"></span>
					</button>
				<?php endif; ?>
				
				<div class="nexus-popup-content">
					<?php echo $this->get_popup_content( $popup->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render popup list page
	 */
	public function render_popup_list() {
		$popups = get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'posts_per_page' => -1,
				'post_status'    => array( 'publish', 'draft' ),
			)
		);

		include NEXUS_PRO_PATH . 'popup-builder/views/popup-list.php';
	}

	/**
	 * Render templates page
	 */
	public function render_templates_page() {
		include NEXUS_PRO_PATH . 'popup-builder/views/templates.php';
	}

	/**
	 * Render analytics page
	 */
	public function render_analytics_page() {
		include NEXUS_PRO_PATH . 'popup-builder/views/analytics.php';
	}

	/**
	 * Save popup via AJAX
	 */
	public function save_popup_ajax() {
		check_ajax_referer( 'nexus_popup_builder', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized', 'nexus-pro' ) ) );
		}

		$popup_id = isset( $_POST['popup_id'] ) ? intval( $_POST['popup_id'] ) : 0;
		$layout   = isset( $_POST['layout'] ) ? json_decode( stripslashes( $_POST['layout'] ), true ) : array();
		$triggers = isset( $_POST['triggers'] ) ? json_decode( stripslashes( $_POST['triggers'] ), true ) : array();
		$targeting = isset( $_POST['targeting'] ) ? json_decode( stripslashes( $_POST['targeting'] ), true ) : array();
		$settings = isset( $_POST['settings'] ) ? json_decode( stripslashes( $_POST['settings'] ), true ) : array();

		if ( ! $popup_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid popup ID', 'nexus-pro' ) ) );
		}

		// Save metadata
		update_post_meta( $popup_id, '_nexus_popup_layout', $layout );
		update_post_meta( $popup_id, '_nexus_popup_triggers', $triggers );
		update_post_meta( $popup_id, '_nexus_popup_targeting', $targeting );
		update_post_meta( $popup_id, '_nexus_popup_settings', $settings );

		wp_send_json_success( array( 'message' => __( 'Popup saved successfully', 'nexus-pro' ) ) );
	}

	/**
	 * Get popup statistics via AJAX
	 */
	public function get_popup_stats_ajax() {
		check_ajax_referer( 'nexus_popup_builder', 'nonce' );

		$popup_id = isset( $_POST['popup_id'] ) ? intval( $_POST['popup_id'] ) : 0;

		if ( ! $popup_id ) {
			wp_send_json_error();
		}

		$stats = array(
			'views'       => get_post_meta( $popup_id, '_nexus_popup_views', true ) ?: 0,
			'conversions' => get_post_meta( $popup_id, '_nexus_popup_conversions', true ) ?: 0,
			'close_rate'  => get_post_meta( $popup_id, '_nexus_popup_close_rate', true ) ?: 0,
		);

		wp_send_json_success( $stats );
	}

	/**
	 * Add custom columns to popup list
	 *
	 * @param array $columns Existing columns
	 * @return array
	 */
	public function add_custom_columns( $columns ) {
		$new_columns = array();
		
		foreach ( $columns as $key => $title ) {
			$new_columns[ $key ] = $title;
			
			if ( 'title' === $key ) {
				$new_columns['status']      = __( 'Status', 'nexus-pro' );
				$new_columns['triggers']    = __( 'Triggers', 'nexus-pro' );
				$new_columns['views']       = __( 'Views', 'nexus-pro' );
				$new_columns['conversions'] = __( 'Conversions', 'nexus-pro' );
			}
		}
		
		return $new_columns;
	}

	/**
	 * Render custom columns
	 *
	 * @param string $column  Column name
	 * @param int    $post_id Post ID
	 */
	public function render_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'status':
				$status = get_post_meta( $post_id, '_nexus_popup_status', true );
				$status = $status ?: 'inactive';
				echo '<span class="nexus-popup-status status-' . esc_attr( $status ) . '">';
				echo esc_html( ucfirst( $status ) );
				echo '</span>';
				break;

			case 'triggers':
				$triggers = get_post_meta( $post_id, '_nexus_popup_triggers', true );
				if ( $triggers && isset( $triggers['type'] ) ) {
					echo esc_html( ucfirst( str_replace( '_', ' ', $triggers['type'] ) ) );
				} else {
					echo '—';
				}
				break;

			case 'views':
				$views = get_post_meta( $post_id, '_nexus_popup_views', true );
				echo esc_html( number_format( $views ?: 0 ) );
				break;

			case 'conversions':
				$conversions = get_post_meta( $post_id, '_nexus_popup_conversions', true );
				$views = get_post_meta( $post_id, '_nexus_popup_views', true );
				$rate = $views > 0 ? ( $conversions / $views ) * 100 : 0;
				echo esc_html( number_format( $conversions ?: 0 ) );
				echo ' <small>(' . esc_html( number_format( $rate, 1 ) ) . '%)</small>';
				break;
		}
	}
}

// Initialize
Nexus_Popup_Builder::get_instance();
