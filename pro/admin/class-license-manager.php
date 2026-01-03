<?php
/**
 * License Manager
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus License Manager Class with Tier Support
 */
class Nexus_License_Manager {

	/**
	 * Instance
	 */
	private static $instance;

	/**
	 * License Server URL
	 */
	private $server_url = 'https://yourdomain.com'; // Change this
	
	/**
	 * License tiers
	 */
	const TIER_FREE = 'free';
	const TIER_PRO = 'pro';
	const TIER_ADVANCED = 'advanced';
	const TIER_AGENCY = 'agency';
	
	/**
	 * License data
	 */
	private $license_data = null;

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
	 * Get Instance (alias for consistency)
	 */
	public static function get_instance() {
		return self::instance();
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'admin_init', array( $this, 'process_license_form' ) );
		add_action( 'admin_notices', array( $this, 'license_notices' ) );
		
		// Load license data
		$this->load_license_data();
	}
	
	/**
	 * Load license data from options
	 */
	private function load_license_data() {
		$this->license_data = get_option( 'nexus_license_data', array(
			'key' => '',
			'tier' => self::TIER_FREE,
			'status' => 'inactive',
			'expires' => '',
			'activated_at' => '',
		) );
	}
	
	/**
	 * Get current license tier
	 */
	public function get_tier() {
		return $this->license_data['tier'] ?? self::TIER_FREE;
	}
	
	/**
	 * Get license status
	 */
	public function get_status() {
		return $this->license_data['status'] ?? 'inactive';
	}
	
	/**
	 * Check if license is active
	 */
	public function is_active() {
		return $this->get_status() === 'active';
	}
	
	/**
	 * Check if feature is available for current tier
	 */
	public function has_feature( $feature ) {
		$tier = $this->get_tier();
		
		// Feature tier requirements
		$feature_tiers = array(
			// Pro Features
			'cloud_storage' => self::TIER_PRO,
			'template_sync' => self::TIER_PRO,
			'payment_gateway' => self::TIER_PRO,
			'credits_system' => self::TIER_PRO,
			'plugin_harmony' => self::TIER_PRO,
			'rest_api' => self::TIER_PRO,
			// Advanced Features
			'plugin_orchestrator' => self::TIER_ADVANCED,
			'loop_builder' => self::TIER_ADVANCED,
			'template_manager' => self::TIER_ADVANCED,
			'ai_template_generator' => self::TIER_ADVANCED,
			'theme_builder' => self::TIER_ADVANCED,
			'popup_builder' => self::TIER_ADVANCED,
			'advanced_controls' => self::TIER_ADVANCED,
			'mega_menu' => self::TIER_ADVANCED,
			'api_docs' => self::TIER_ADVANCED,
			'circuit_simulator' => self::TIER_ADVANCED,
			'performance_analytics' => self::TIER_ADVANCED,
			'advanced_theme_builder' => self::TIER_ADVANCED,
			// Agency Features
			'ab_testing' => self::TIER_AGENCY,
			'white_label' => self::TIER_AGENCY,
		);
		
		if ( ! isset( $feature_tiers[ $feature ] ) ) {
			return true; // Feature available to all
		}
		
		$required_tier = $feature_tiers[ $feature ];
		$tier_order = array( self::TIER_FREE, self::TIER_PRO, self::TIER_ADVANCED, self::TIER_AGENCY );
		
		$current_index = array_search( $tier, $tier_order, true );
		$required_index = array_search( $required_tier, $tier_order, true );
		
		return $current_index >= $required_index;
	}

	/**
	 * Process License Form
	 */
	public function process_license_form() {
		if ( ! isset( $_POST['nexus_license_action'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		check_admin_referer( 'nexus_license_action', 'nexus_license_nonce' );

		$action = sanitize_key( $_POST['nexus_license_action'] );

		switch ( $action ) {
			case 'activate':
				$this->activate_license();
				break;
			case 'deactivate':
				$this->deactivate_license();
				break;
		}
	}

	/**
	 * Activate License
	 */
	private function activate_license() {
		$license_key = isset( $_POST['license_key'] ) ? sanitize_text_field( $_POST['license_key'] ) : '';

		if ( empty( $license_key ) ) {
			add_settings_error( 'nexus_license', 'empty_license', __( 'Please enter a license key', 'nexus' ) );
			return;
		}

		// Call license server
		$response = wp_remote_post(
			$this->server_url . '/wp-json/license/v1/activate',
			array(
				'body' => array(
					'license_key' => $license_key,
					'site_url'    => home_url(),
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			add_settings_error( 'nexus_license', 'server_error', __( 'Could not connect to license server', 'nexus' ) );
			return;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['success'] ) && $body['success'] ) {
			update_option( 'nexus_pro_license_key', $license_key );
			update_option( 'nexus_pro_license_valid', true );
			update_option( 'nexus_pro_license_expires', $body['expires'] );

			add_settings_error( 'nexus_license', 'license_activated', __( 'License activated successfully', 'nexus' ), 'updated' );
		} else {
			$message = isset( $body['message'] ) ? $body['message'] : __( 'License activation failed', 'nexus' );
			add_settings_error( 'nexus_license', 'activation_failed', $message );
		}
	}

	/**
	 * Deactivate License
	 */
	private function deactivate_license() {
		$license_key = get_option( 'nexus_pro_license_key' );

		// Call license server
		wp_remote_post(
			$this->server_url . '/wp-json/license/v1/deactivate',
			array(
				'body' => array(
					'license_key' => $license_key,
					'site_url'    => home_url(),
				),
			)
		);

		delete_option( 'nexus_pro_license_key' );
		delete_option( 'nexus_pro_license_valid' );
		delete_option( 'nexus_pro_license_expires' );

		add_settings_error( 'nexus_license', 'license_deactivated', __( 'License deactivated', 'nexus' ), 'updated' );
	}

	/**
	 * License Notices
	 */
	public function license_notices() {
		$screen = get_current_screen();
		if ( 'toplevel_page_nexus-pro' !== $screen->id && strpos( $screen->id, 'nexus-pro' ) === false ) {
			return;
		}

		settings_errors( 'nexus_license' );
	}

	/**
	 * Get License Status
	 */
	public function get_license_status() {
		$is_valid = get_option( 'nexus_pro_license_valid', false );
		$expires  = get_option( 'nexus_pro_license_expires', '' );

		return array(
			'valid'   => $is_valid,
			'expires' => $expires,
		);
	}
}
