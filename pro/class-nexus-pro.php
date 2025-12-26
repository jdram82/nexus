<?php
/**
 * Nexus Pro Main Class
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Nexus Pro Class
 */
class Nexus_Pro {

	/**
	 * Instance
	 *
	 * @var Nexus_Pro
	 */
	private static $instance;

	/**
	 * Version
	 *
	 * @var string
	 */
	public $version = '2.0.0';

	/**
	 * Get Instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define Constants
	 */
	private function define_constants() {
		define( 'NEXUS_PRO_VERSION', $this->version );
		define( 'NEXUS_PRO_DIR', NEXUS_DIR . '/pro' );
		define( 'NEXUS_PRO_URI', NEXUS_URI . '/pro' );
		define( 'NEXUS_PRO_PATH', NEXUS_PRO_DIR . '/' );
		define( 'NEXUS_PRO_URL', NEXUS_PRO_URI . '/' );
	}

	/**
	 * Include Files
	 */
	private function includes() {
		// Activation handler
		require_once NEXUS_PRO_DIR . '/class-pro-activation.php';

		// Phase 3: Theme Builder (Advanced)
		if ( file_exists( NEXUS_PRO_DIR . '/theme-builder/class-theme-builder.php' ) ) {
			require_once NEXUS_PRO_DIR . '/theme-builder/class-theme-builder.php';
		}

		// Phase 3: Advanced Controls
		if ( file_exists( NEXUS_PRO_DIR . '/controls/class-controls-manager.php' ) ) {
			require_once NEXUS_PRO_DIR . '/controls/class-controls-manager.php';
		}

		// Phase 3: Mega Menu Builder
		if ( file_exists( NEXUS_PRO_DIR . '/mega-menu/class-mega-menu.php' ) ) {
			require_once NEXUS_PRO_DIR . '/mega-menu/class-mega-menu.php';
		}

		// Header/Footer Builder
		require_once NEXUS_PRO_DIR . '/builder/class-header-builder.php';
		require_once NEXUS_PRO_DIR . '/builder/class-footer-builder.php';
		require_once NEXUS_PRO_DIR . '/builder/class-builder-elements.php';

		// Advanced Filtering
		require_once NEXUS_PRO_DIR . '/filtering/class-product-filter.php';
		require_once NEXUS_PRO_DIR . '/filtering/class-ajax-filter.php';

		// Documentation System
		require_once NEXUS_PRO_DIR . '/documentation/class-docs-manager.php';
		require_once NEXUS_PRO_DIR . '/documentation/class-docs-search.php';
		require_once NEXUS_PRO_DIR . '/documentation/class-code-highlighter.php';

		// Client Portal
		require_once NEXUS_PRO_DIR . '/portal/class-portal-manager.php';
		require_once NEXUS_PRO_DIR . '/portal/class-portal-dashboard.php';
		require_once NEXUS_PRO_DIR . '/portal/class-portal-projects.php';

		// Form Builder
		require_once NEXUS_PRO_DIR . '/forms/class-form-builder.php';
		require_once NEXUS_PRO_DIR . '/forms/class-form-processor.php';
		require_once NEXUS_PRO_DIR . '/forms/class-form-fields.php';

		// Admin
		require_once NEXUS_PRO_DIR . '/admin/class-pro-admin.php';
		require_once NEXUS_PRO_DIR . '/admin/class-license-manager.php';
	}

	/**
	 * Initialize Hooks
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	/**
	 * Initialize
	 */
	public function init() {
		// Header/Footer Builder
		Nexus_Header_Builder::instance();
		Nexus_Footer_Builder::instance();
		Nexus_Builder_Elements::instance();

		// Advanced Filtering
		Nexus_Product_Filter::instance();
		Nexus_Ajax_Filter::instance();

		// Documentation
		Nexus_Docs_Manager::instance();
		Nexus_Docs_Search::instance();
		Nexus_Code_Highlighter::instance();

		// Client Portal
		Nexus_Portal_Manager::instance();
		Nexus_Portal_Dashboard::instance();
		Nexus_Portal_Projects::instance();

		// Form Builder
		Nexus_Form_Builder::instance();
		Nexus_Form_Processor::instance();

		// Admin
		Nexus_Pro_Admin::instance();
		Nexus_License_Manager::instance();
	}

	/**
	 * Add Admin Menu
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'Nexus Pro', 'nexus' ),
			__( 'Nexus Pro', 'nexus' ),
			'manage_options',
			'nexus-pro',
			array( $this, 'admin_page' ),
			'dashicons-admin-customizer',
			59
		);
	}

	/**
	 * Admin Page
	 */
	public function admin_page() {
		require_once NEXUS_PRO_DIR . '/admin/views/dashboard.php';
	}
}

/**
 * Initialize Nexus Pro
 */
function nexus_pro() {
	return Nexus_Pro::instance();
}

// Initialize if license is valid
if ( get_option( 'nexus_pro_license_valid' ) ) {
	nexus_pro();
}
