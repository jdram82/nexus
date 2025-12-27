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
		// Core: Database Schema (ALL Tiers)
		if ( file_exists( NEXUS_PRO_DIR . '/class-database-schema.php' ) ) {
			require_once NEXUS_PRO_DIR . '/class-database-schema.php';
		}
		
		// Activation handler
		if ( file_exists( NEXUS_PRO_DIR . '/class-pro-activation.php' ) ) {
			require_once NEXUS_PRO_DIR . '/class-pro-activation.php';
		}
		
		// Core: Cloud Storage (Pro+ Tiers)
		if ( file_exists( NEXUS_PRO_DIR . '/cloud/class-cloud-storage.php' ) ) {
			require_once NEXUS_PRO_DIR . '/cloud/class-cloud-storage.php';
		}
		
		// Core: Template Cloud Sync (Pro+ Tiers)
		if ( file_exists( NEXUS_PRO_DIR . '/cloud/class-template-cloud-sync.php' ) ) {
			require_once NEXUS_PRO_DIR . '/cloud/class-template-cloud-sync.php';
		}
		
		// Core: Multi-Gateway Payment System (Pro+ Tiers)
		if ( file_exists( NEXUS_PRO_DIR . '/payment/class-payment-gateway-multi.php' ) ) {
			require_once NEXUS_PRO_DIR . '/payment/class-payment-gateway-multi.php';
		}
		
		// Phase 1: Plugin Orchestrator (Advanced Tier)
		if ( file_exists( NEXUS_PRO_DIR . '/plugin-orchestrator/class-plugin-orchestrator.php' ) ) {
			require_once NEXUS_PRO_DIR . '/plugin-orchestrator/class-plugin-orchestrator.php';
		}
		
		// Phase 1: Loop Builder (Advanced Tier)
		if ( file_exists( NEXUS_PRO_DIR . '/loop-builder/class-loop-builder.php' ) ) {
			require_once NEXUS_PRO_DIR . '/loop-builder/class-loop-builder.php';
		}
		
		// Phase 2: Template Library (Pro Tier)
		if ( file_exists( NEXUS_PRO_DIR . '/templates/class-template-library.php' ) ) {
			require_once NEXUS_PRO_DIR . '/templates/class-template-library.php';
		}
		
		// Phase 2: AI Template Generator (Advanced Tier)
		if ( file_exists( NEXUS_PRO_DIR . '/ai/class-template-generator.php' ) ) {
			require_once NEXUS_PRO_DIR . '/ai/class-template-generator.php';
		}
		
		// Phase 2: AI Documentation Generator (Advanced Tier)
		if ( file_exists( NEXUS_PRO_DIR . '/ai/class-docs-generator.php' ) ) {
			require_once NEXUS_PRO_DIR . '/ai/class-docs-generator.php';
		}
		
		// Phase 2: White-Label System (Advanced Tier)
		if ( file_exists( NEXUS_PRO_DIR . '/agency/class-white-label.php' ) ) {
			require_once NEXUS_PRO_DIR . '/agency/class-white-label.php';
		}
		
		// Phase 2: Multi-Site Dashboard (Agency Tier)
		if ( file_exists( NEXUS_PRO_DIR . '/agency/class-agency-dashboard.php' ) ) {
			require_once NEXUS_PRO_DIR . '/agency/class-agency-dashboard.php';
		}
		
		// Phase 3A: Credit System (Pro+ Tiers)
		if ( file_exists( NEXUS_PRO_DIR . '/credits/class-payment-gateway.php' ) ) {
			require_once NEXUS_PRO_DIR . '/credits/class-payment-gateway.php';
		}
		if ( file_exists( NEXUS_PRO_DIR . '/credits/class-credit-manager.php' ) ) {
			require_once NEXUS_PRO_DIR . '/credits/class-credit-manager.php';
		}
		if ( file_exists( NEXUS_PRO_DIR . '/credits/class-credit-topup.php' ) ) {
			require_once NEXUS_PRO_DIR . '/credits/class-credit-topup.php';
		}

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

		// Phase 3: A/B Testing System
		if ( file_exists( NEXUS_PRO_DIR . '/ab-testing/class-ab-testing.php' ) ) {
			require_once NEXUS_PRO_DIR . '/ab-testing/class-ab-testing.php';
		}

		// Phase 3: Analytics Dashboard
		if ( file_exists( NEXUS_PRO_DIR . '/analytics/class-analytics.php' ) ) {
			require_once NEXUS_PRO_DIR . '/analytics/class-analytics.php';
		}

		// Phase 3: Performance Monitor
		if ( file_exists( NEXUS_PRO_DIR . '/performance/class-performance.php' ) ) {
			require_once NEXUS_PRO_DIR . '/performance/class-performance.php';
		}

		// Phase 3: SEO Tools
		if ( file_exists( NEXUS_PRO_DIR . '/seo/class-seo.php' ) ) {
			require_once NEXUS_PRO_DIR . '/seo/class-seo.php';
		}

		// Header/Footer Builder
		if ( file_exists( NEXUS_PRO_DIR . '/builder/class-header-builder.php' ) ) {
			require_once NEXUS_PRO_DIR . '/builder/class-header-builder.php';
		}
		if ( file_exists( NEXUS_PRO_DIR . '/builder/class-footer-builder.php' ) ) {
			require_once NEXUS_PRO_DIR . '/builder/class-footer-builder.php';
		}
		if ( file_exists( NEXUS_PRO_DIR . '/builder/class-builder-elements.php' ) ) {
			require_once NEXUS_PRO_DIR . '/builder/class-builder-elements.php';
		}

		// Advanced Filtering
		if ( file_exists( NEXUS_PRO_DIR . '/filtering/class-product-filter.php' ) ) {
			require_once NEXUS_PRO_DIR . '/filtering/class-product-filter.php';
		}
		if ( file_exists( NEXUS_PRO_DIR . '/filtering/class-ajax-filter.php' ) ) {
			require_once NEXUS_PRO_DIR . '/filtering/class-ajax-filter.php';
		}

		// Documentation System
		if ( file_exists( NEXUS_PRO_DIR . '/documentation/class-docs-manager.php' ) ) {
			require_once NEXUS_PRO_DIR . '/documentation/class-docs-manager.php';
		}
		if ( file_exists( NEXUS_PRO_DIR . '/documentation/class-docs-search.php' ) ) {
			require_once NEXUS_PRO_DIR . '/documentation/class-docs-search.php';
		}
		if ( file_exists( NEXUS_PRO_DIR . '/documentation/class-code-highlighter.php' ) ) {
			require_once NEXUS_PRO_DIR . '/documentation/class-code-highlighter.php';
		}

		// Client Portal
		if ( file_exists( NEXUS_PRO_DIR . '/portal/class-portal-manager.php' ) ) {
			require_once NEXUS_PRO_DIR . '/portal/class-portal-manager.php';
		}
		if ( file_exists( NEXUS_PRO_DIR . '/portal/class-portal-dashboard.php' ) ) {
			require_once NEXUS_PRO_DIR . '/portal/class-portal-dashboard.php';
		}
		if ( file_exists( NEXUS_PRO_DIR . '/portal/class-portal-projects.php' ) ) {
			require_once NEXUS_PRO_DIR . '/portal/class-portal-projects.php';
		}

		// Form Builder
		if ( file_exists( NEXUS_PRO_DIR . '/forms/class-form-builder.php' ) ) {
			require_once NEXUS_PRO_DIR . '/forms/class-form-builder.php';
		}
		if ( file_exists( NEXUS_PRO_DIR . '/forms/class-form-processor.php' ) ) {
			require_once NEXUS_PRO_DIR . '/forms/class-form-processor.php';
		}
		if ( file_exists( NEXUS_PRO_DIR . '/forms/class-form-fields.php' ) ) {
			require_once NEXUS_PRO_DIR . '/forms/class-form-fields.php';
		}

		// Admin
		if ( file_exists( NEXUS_PRO_DIR . '/admin/class-pro-admin.php' ) ) {
			require_once NEXUS_PRO_DIR . '/admin/class-pro-admin.php';
		}
		if ( file_exists( NEXUS_PRO_DIR . '/admin/class-license-manager.php' ) ) {
			require_once NEXUS_PRO_DIR . '/admin/class-license-manager.php';
		}
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
		if ( class_exists( 'Nexus_Header_Builder' ) ) {
			Nexus_Header_Builder::instance();
		}
		if ( class_exists( 'Nexus_Footer_Builder' ) ) {
			Nexus_Footer_Builder::instance();
		}
		if ( class_exists( 'Nexus_Builder_Elements' ) ) {
			Nexus_Builder_Elements::instance();
		}

		// Advanced Filtering
		if ( class_exists( 'Nexus_Product_Filter' ) ) {
			Nexus_Product_Filter::instance();
		}
		if ( class_exists( 'Nexus_Ajax_Filter' ) ) {
			Nexus_Ajax_Filter::instance();
		}

		// Documentation
		if ( class_exists( 'Nexus_Docs_Manager' ) ) {
			Nexus_Docs_Manager::instance();
		}
		if ( class_exists( 'Nexus_Docs_Search' ) ) {
			Nexus_Docs_Search::instance();
		}
		if ( class_exists( 'Nexus_Code_Highlighter' ) ) {
			Nexus_Code_Highlighter::instance();
		}

		// Client Portal
		if ( class_exists( 'Nexus_Portal_Manager' ) ) {
			Nexus_Portal_Manager::instance();
		}
		if ( class_exists( 'Nexus_Portal_Dashboard' ) ) {
			Nexus_Portal_Dashboard::instance();
		}
		if ( class_exists( 'Nexus_Portal_Projects' ) ) {
			Nexus_Portal_Projects::instance();
		}

		// Form Builder
		if ( class_exists( 'Nexus_Form_Builder' ) ) {
			Nexus_Form_Builder::instance();
		}
		if ( class_exists( 'Nexus_Form_Processor' ) ) {
			Nexus_Form_Processor::instance();
		}

		// Admin
		if ( class_exists( 'Nexus_Pro_Admin' ) ) {
			Nexus_Pro_Admin::instance();
		}
		if ( class_exists( 'Nexus_License_Manager' ) ) {
			Nexus_License_Manager::instance();
		}
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
