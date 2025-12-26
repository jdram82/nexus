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
 * Nexus License Manager Class
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
		add_action( 'admin_init', array( $this, 'process_license_form' ) );
		add_action( 'admin_notices', array( $this, 'license_notices' ) );
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
