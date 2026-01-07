<?php
/**
 * Pro Admin
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Pro Admin Class
 */
class Nexus_Pro_Admin {

	/**
	 * Instance
	 */
	private static $instance;

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
		add_action( 'admin_menu', array( $this, 'add_submenu_pages' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add Submenu Pages
	 */
	public function add_submenu_pages() {
		add_submenu_page(
			'nexus-pro',
			__( 'Settings', 'nexus' ),
			__( 'Settings', 'nexus' ),
			'manage_options',
			'nexus-pro-settings',
			array( $this, 'settings_page' )
		);

		add_submenu_page(
			'nexus-pro',
			__( 'Form Submissions', 'nexus' ),
			__( 'Submissions', 'nexus' ),
			'manage_options',
			'nexus-pro-submissions',
			array( $this, 'submissions_page' )
		);

		// License page removed - use unified license system in main Nexus menu
		// Users should go to: Nexus > License instead
	}

	/**
	 * Settings Page
	 */
	public function settings_page() {
		require_once NEXUS_PRO_DIR . '/admin/views/settings.php';
	}

	/**
	 * Submissions Page
	 */
	public function submissions_page() {
		require_once NEXUS_PRO_DIR . '/admin/views/submissions.php';
	}

	/**
	 * License Page
	 */
	public function license_page() {
		require_once NEXUS_PRO_DIR . '/admin/views/license.php';
	}

	/**
	 * Enqueue Scripts
	 */
	public function enqueue_scripts( $hook ) {
		if ( strpos( $hook, 'nexus-pro' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'nexus-pro-admin',
			NEXUS_PRO_URI . '/assets/css/admin.css',
			array(),
			NEXUS_PRO_VERSION
		);

		wp_enqueue_script(
			'nexus-pro-admin',
			NEXUS_PRO_URI . '/assets/js/admin.js',
			array( 'jquery' ),
			NEXUS_PRO_VERSION,
			true
		);
	}
}
