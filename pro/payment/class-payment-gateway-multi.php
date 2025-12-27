<?php
/**
 * Nexus Multi-Gateway Payment System
 * 
 * Supports Razorpay (India) + PayPal (Global)
 * 
 * @package Nexus_Pro
 * @since 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Nexus_Payment_Gateway_Multi {
	
	/**
	 * Singleton instance
	 */
	private static $instance = null;
	
	/**
	 * Razorpay credentials
	 * REPLACE WITH YOUR REAL CREDENTIALS
	 */
	private $razorpay_key_id = 'rzp_test_YOUR_KEY_ID'; // Replace with live: rzp_live_xxxxx
	private $razorpay_key_secret = 'YOUR_RAZORPAY_SECRET'; // Your secret key
	private $razorpay_webhook_secret = 'YOUR_WEBHOOK_SECRET'; // For webhook verification
	
	/**
	 * PayPal credentials  
	 * REPLACE WITH YOUR REAL CREDENTIALS
	 */
	private $paypal_client_id = 'YOUR_PAYPAL_CLIENT_ID'; // Get from PayPal Developer
	private $paypal_secret = 'YOUR_PAYPAL_SECRET';
	private $paypal_mode = 'sandbox'; // 'sandbox' or 'live'
	
	/**
	 * Active gateway
	 */
	private $active_gateway = 'razorpay';
	
	/**
	 * Get singleton instance
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * Constructor
	 */
	private function __construct() {
		// Load credentials from database
		$this->load_credentials();
		
		// Get active gateway
		$this->active_gateway = get_option( 'nexus_active_payment_gateway', 'razorpay' );
		
		// Add hooks
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		
		// AJAX handlers
		add_action( 'wp_ajax_nexus_create_payment', array( $this, 'ajax_create_payment' ) );
		add_action( 'wp_ajax_nexus_verify_payment', array( $this, 'ajax_verify_payment' ) );
		
		// Webhook handlers
		add_action( 'wp_ajax_nopriv_nexus_razorpay_webhook', array( $this, 'razorpay_webhook' ) );
		add_action( 'wp_ajax_nopriv_nexus_paypal_webhook', array( $this, 'paypal_webhook' ) );
	}
	
	/**
	 * Load credentials from database
	 */
	private function load_credentials() {
		// Razorpay
		$rzp_key = get_option( 'nexus_razorpay_key_id' );
		$rzp_secret = get_option( 'nexus_razorpay_secret' );
		$rzp_webhook = get_option( 'nexus_razorpay_webhook_secret' );
		
		if ( $rzp_key ) $this->razorpay_key_id = $rzp_key;
		if ( $rzp_secret ) $this->razorpay_key_secret = $rzp_secret;
		if ( $rzp_webhook ) $this->razorpay_webhook_secret = $rzp_webhook;
		
		// PayPal
		$pp_client = get_option( 'nexus_paypal_client_id' );
		$pp_secret = get_option( 'nexus_paypal_secret' );
		$pp_mode = get_option( 'nexus_paypal_mode', 'sandbox' );
		
		if ( $pp_client ) $this->paypal_client_id = $pp_client;
		if ( $pp_secret ) $this->paypal_secret = $pp_secret;
		$this->paypal_mode = $pp_mode;
	}
	
	/**
	 * Check if gateway has credentials
	 */
	public function has_credentials( $gateway = null ) {
		if ( null === $gateway ) {
			$gateway = $this->active_gateway;
		}
		
		switch ( $gateway ) {
			case 'razorpay':
				return ! empty( $this->razorpay_key_id ) && 
				       $this->razorpay_key_id !== 'rzp_test_YOUR_KEY_ID';
			
			case 'paypal':
				return ! empty( $this->paypal_client_id ) && 
				       $this->paypal_client_id !== 'YOUR_PAYPAL_CLIENT_ID';
			
			default:
				return false;
		}
	}
	
	/**
	 * Create payment order
	 * 
	 * @param int $amount Amount in smallest currency unit (paise for INR, cents for USD)
	 * @param string $currency Currency code (INR, USD, etc.)
	 * @param array $metadata Additional data
	 * @return array|WP_Error Order details
	 */
	public function create_order( $amount, $currency = 'INR', $metadata = array() ) {
		switch ( $this->active_gateway ) {
			case 'razorpay':
				return $this->create_razorpay_order( $amount, $currency, $metadata );
			
			case 'paypal':
				return $this->create_paypal_order( $amount, $currency, $metadata );
			
			default:
				return new WP_Error( 'invalid_gateway', __( 'Invalid payment gateway', 'nexus' ) );
		}
	}
	
	/**
	 * Create Razorpay order
	 */
	private function create_razorpay_order( $amount, $currency, $metadata ) {
		if ( ! $this->has_credentials( 'razorpay' ) ) {
			return new WP_Error( 'no_credentials', __( 'Razorpay credentials not configured', 'nexus' ) );
		}
		
		$url = 'https://api.razorpay.com/v1/orders';
		
		$body = array(
			'amount' => $amount,
			'currency' => $currency,
			'receipt' => 'nexus_' . time(),
			'notes' => $metadata,
		);
		
		$response = wp_remote_post( $url, array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $this->razorpay_key_id . ':' . $this->razorpay_key_secret ),
				'Content-Type' => 'application/json',
			),
			'body' => wp_json_encode( $body ),
			'timeout' => 30,
		) );
		
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
		
		if ( $response_code !== 200 ) {
			return new WP_Error(
				'razorpay_error',
				isset( $response_body['error']['description'] ) ? $response_body['error']['description'] : __( 'Failed to create order', 'nexus' )
			);
		}
		
		// Save order to database
		$this->save_order( array(
			'gateway' => 'razorpay',
			'order_id' => $response_body['id'],
			'amount' => $amount,
			'currency' => $currency,
			'status' => 'created',
			'metadata' => $metadata,
		) );
		
		return array(
			'success' => true,
			'gateway' => 'razorpay',
			'order_id' => $response_body['id'],
			'amount' => $response_body['amount'],
			'currency' => $response_body['currency'],
			'key_id' => $this->razorpay_key_id,
		);
	}
	
	/**
	 * Create PayPal order
	 */
	private function create_paypal_order( $amount, $currency, $metadata ) {
		if ( ! $this->has_credentials( 'paypal' ) ) {
			return new WP_Error( 'no_credentials', __( 'PayPal credentials not configured', 'nexus' ) );
		}
		
		// Get PayPal access token first
		$access_token = $this->get_paypal_access_token();
		if ( is_wp_error( $access_token ) ) {
			return $access_token;
		}
		
		$base_url = $this->paypal_mode === 'live' 
			? 'https://api-m.paypal.com' 
			: 'https://api-m.sandbox.paypal.com';
		
		$url = $base_url . '/v2/checkout/orders';
		
		// Convert amount to decimal (PayPal uses decimal, not smallest unit)
		$decimal_amount = number_format( $amount / 100, 2, '.', '' );
		
		$body = array(
			'intent' => 'CAPTURE',
			'purchase_units' => array(
				array(
					'amount' => array(
						'currency_code' => $currency,
						'value' => $decimal_amount,
					),
					'description' => 'Nexus Credits Purchase',
				),
			),
			'application_context' => array(
				'return_url' => admin_url( 'admin.php?page=nexus-credits&payment=success' ),
				'cancel_url' => admin_url( 'admin.php?page=nexus-credits&payment=cancelled' ),
			),
		);
		
		$response = wp_remote_post( $url, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $access_token,
				'Content-Type' => 'application/json',
			),
			'body' => wp_json_encode( $body ),
			'timeout' => 30,
		) );
		
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
		
		if ( $response_code !== 201 ) {
			return new WP_Error(
				'paypal_error',
				isset( $response_body['message'] ) ? $response_body['message'] : __( 'Failed to create PayPal order', 'nexus' )
			);
		}
		
		// Get approval URL
		$approval_url = '';
		if ( isset( $response_body['links'] ) ) {
			foreach ( $response_body['links'] as $link ) {
				if ( $link['rel'] === 'approve' ) {
					$approval_url = $link['href'];
					break;
				}
			}
		}
		
		// Save order to database
		$this->save_order( array(
			'gateway' => 'paypal',
			'order_id' => $response_body['id'],
			'amount' => $amount,
			'currency' => $currency,
			'status' => 'created',
			'metadata' => $metadata,
		) );
		
		return array(
			'success' => true,
			'gateway' => 'paypal',
			'order_id' => $response_body['id'],
			'approval_url' => $approval_url,
			'amount' => $decimal_amount,
			'currency' => $currency,
		);
	}
	
	/**
	 * Get PayPal access token
	 */
	private function get_paypal_access_token() {
		$base_url = $this->paypal_mode === 'live' 
			? 'https://api-m.paypal.com' 
			: 'https://api-m.sandbox.paypal.com';
		
		$url = $base_url . '/v1/oauth2/token';
		
		$response = wp_remote_post( $url, array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $this->paypal_client_id . ':' . $this->paypal_secret ),
				'Content-Type' => 'application/x-www-form-urlencoded',
			),
			'body' => 'grant_type=client_credentials',
			'timeout' => 30,
		) );
		
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		
		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
		
		if ( ! isset( $response_body['access_token'] ) ) {
			return new WP_Error( 'paypal_auth_failed', __( 'Failed to get PayPal access token', 'nexus' ) );
		}
		
		return $response_body['access_token'];
	}
	
	/**
	 * Verify payment
	 * 
	 * @param string $payment_id Payment ID
	 * @param array $data Additional verification data
	 * @return bool|WP_Error True if verified
	 */
	public function verify_payment( $payment_id, $data = array() ) {
		switch ( $this->active_gateway ) {
			case 'razorpay':
				return $this->verify_razorpay_payment( $payment_id, $data );
			
			case 'paypal':
				return $this->verify_paypal_payment( $payment_id, $data );
			
			default:
				return new WP_Error( 'invalid_gateway', __( 'Invalid payment gateway', 'nexus' ) );
		}
	}
	
	/**
	 * Verify Razorpay payment
	 */
	private function verify_razorpay_payment( $payment_id, $data ) {
		if ( ! isset( $data['razorpay_order_id'], $data['razorpay_signature'] ) ) {
			return new WP_Error( 'missing_data', __( 'Missing verification data', 'nexus' ) );
		}
		
		// Verify signature
		$generated_signature = hash_hmac(
			'sha256',
			$data['razorpay_order_id'] . '|' . $payment_id,
			$this->razorpay_key_secret
		);
		
		if ( $generated_signature !== $data['razorpay_signature'] ) {
			$this->log_payment( $payment_id, 'failed', 'Signature verification failed' );
			return new WP_Error( 'invalid_signature', __( 'Payment verification failed', 'nexus' ) );
		}
		
		// Fetch payment details from Razorpay
		$url = 'https://api.razorpay.com/v1/payments/' . $payment_id;
		
		$response = wp_remote_get( $url, array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( $this->razorpay_key_id . ':' . $this->razorpay_key_secret ),
			),
			'timeout' => 30,
		) );
		
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		
		$payment_data = json_decode( wp_remote_retrieve_body( $response ), true );
		
		if ( $payment_data['status'] !== 'captured' && $payment_data['status'] !== 'authorized' ) {
			return new WP_Error( 'payment_not_successful', __( 'Payment not successful', 'nexus' ) );
		}
		
		// Update order status
		$this->update_order_status( $data['razorpay_order_id'], 'paid', $payment_data );
		
		$this->log_payment( $payment_id, 'success', 'Payment verified and captured' );
		
		return true;
	}
	
	/**
	 * Verify PayPal payment
	 */
	private function verify_paypal_payment( $order_id, $data ) {
		$access_token = $this->get_paypal_access_token();
		if ( is_wp_error( $access_token ) ) {
			return $access_token;
		}
		
		$base_url = $this->paypal_mode === 'live' 
			? 'https://api-m.paypal.com' 
			: 'https://api-m.sandbox.paypal.com';
		
		// Capture the order
		$url = $base_url . '/v2/checkout/orders/' . $order_id . '/capture';
		
		$response = wp_remote_post( $url, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $access_token,
				'Content-Type' => 'application/json',
			),
			'timeout' => 30,
		) );
		
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		
		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
		
		if ( $response_body['status'] !== 'COMPLETED' ) {
			return new WP_Error( 'payment_not_completed', __( 'PayPal payment not completed', 'nexus' ) );
		}
		
		// Update order status
		$this->update_order_status( $order_id, 'paid', $response_body );
		
		$this->log_payment( $order_id, 'success', 'PayPal payment verified' );
		
		return true;
	}
	
	/**
	 * Save order to database
	 */
	private function save_order( $order_data ) {
		global $wpdb;
		
		$table = $wpdb->prefix . 'nexus_payment_orders';
		
		$wpdb->insert(
			$table,
			array(
				'user_id' => get_current_user_id(),
				'gateway' => $order_data['gateway'],
				'order_id' => $order_data['order_id'],
				'amount' => $order_data['amount'],
				'currency' => $order_data['currency'],
				'status' => $order_data['status'],
				'metadata' => maybe_serialize( $order_data['metadata'] ),
				'created_at' => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s' )
		);
	}
	
	/**
	 * Update order status
	 */
	private function update_order_status( $order_id, $status, $payment_data = array() ) {
		global $wpdb;
		
		$table = $wpdb->prefix . 'nexus_payment_orders';
		
		$wpdb->update(
			$table,
			array(
				'status' => $status,
				'payment_data' => maybe_serialize( $payment_data ),
				'updated_at' => current_time( 'mysql' ),
			),
			array( 'order_id' => $order_id ),
			array( '%s', '%s', '%s' ),
			array( '%s' )
		);
	}
	
	/**
	 * Log payment activity
	 */
	private function log_payment( $payment_id, $status, $message ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( "[Nexus Payment] {$payment_id}: {$status} - {$message}" );
		}
		
		// Store in database
		global $wpdb;
		$table = $wpdb->prefix . 'nexus_payment_logs';
		
		$wpdb->insert(
			$table,
			array(
				'payment_id' => $payment_id,
				'status' => $status,
				'message' => $message,
				'created_at' => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%s' )
		);
	}
	
	/**
	 * AJAX: Create payment
	 */
	public function ajax_create_payment() {
		check_ajax_referer( 'nexus_payment', 'nonce' );
		
		$amount = intval( $_POST['amount'] );
		$currency = sanitize_text_field( $_POST['currency'] );
		$credits = intval( $_POST['credits'] );
		
		$metadata = array(
			'user_id' => get_current_user_id(),
			'credits' => $credits,
		);
		
		$result = $this->create_order( $amount, $currency, $metadata );
		
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array(
				'message' => $result->get_error_message(),
			) );
		}
		
		wp_send_json_success( $result );
	}
	
	/**
	 * AJAX: Verify payment
	 */
	public function ajax_verify_payment() {
		check_ajax_referer( 'nexus_payment', 'nonce' );
		
		$payment_id = sanitize_text_field( $_POST['payment_id'] );
		$data = array_map( 'sanitize_text_field', $_POST['data'] );
		
		$result = $this->verify_payment( $payment_id, $data );
		
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array(
				'message' => $result->get_error_message(),
			) );
		}
		
		wp_send_json_success( array(
			'message' => __( 'Payment successful!', 'nexus' ),
		) );
	}
	
	/**
	 * Razorpay webhook handler
	 */
	public function razorpay_webhook() {
		$payload = file_get_contents( 'php://input' );
		$signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';
		
		// Verify signature
		$expected_signature = hash_hmac( 'sha256', $payload, $this->razorpay_webhook_secret );
		
		if ( $signature !== $expected_signature ) {
			status_header( 400 );
			die( 'Invalid signature' );
		}
		
		$event = json_decode( $payload, true );
		
		// Handle event
		if ( $event['event'] === 'payment.captured' ) {
			$payment_id = $event['payload']['payment']['entity']['id'];
			$order_id = $event['payload']['payment']['entity']['order_id'];
			
			$this->update_order_status( $order_id, 'paid', $event['payload']['payment']['entity'] );
			$this->log_payment( $payment_id, 'webhook_captured', 'Payment captured via webhook' );
		}
		
		status_header( 200 );
		die( 'OK' );
	}
	
	/**
	 * PayPal webhook handler
	 */
	public function paypal_webhook() {
		$payload = file_get_contents( 'php://input' );
		$event = json_decode( $payload, true );
		
		// Handle event
		if ( $event['event_type'] === 'CHECKOUT.ORDER.APPROVED' ) {
			$order_id = $event['resource']['id'];
			
			// Auto-capture the order
			$this->verify_paypal_payment( $order_id, array() );
		}
		
		status_header( 200 );
		die( 'OK' );
	}
	
	/**
	 * Add settings page
	 */
	public function add_settings_page() {
		add_submenu_page(
			'nexus-pro',
			__( 'Payment Gateways', 'nexus' ),
			__( 'Payment Gateways', 'nexus' ),
			'manage_options',
			'nexus-payment-gateways',
			array( $this, 'render_settings_page' )
		);
	}
	
	/**
	 * Register settings
	 */
	public function register_settings() {
		// Razorpay
		register_setting( 'nexus_payment_gateways', 'nexus_razorpay_key_id' );
		register_setting( 'nexus_payment_gateways', 'nexus_razorpay_secret' );
		register_setting( 'nexus_payment_gateways', 'nexus_razorpay_webhook_secret' );
		
		// PayPal
		register_setting( 'nexus_payment_gateways', 'nexus_paypal_client_id' );
		register_setting( 'nexus_payment_gateways', 'nexus_paypal_secret' );
		register_setting( 'nexus_payment_gateways', 'nexus_paypal_mode' );
		
		// Active gateway
		register_setting( 'nexus_payment_gateways', 'nexus_active_payment_gateway' );
	}
	
	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php _e( 'Payment Gateway Settings', 'nexus' ); ?></h1>
			
			<p><?php _e( 'Configure Razorpay (India) and PayPal (Global) for accepting payments.', 'nexus' ); ?></p>
			
			<form method="post" action="options.php">
				<?php settings_fields( 'nexus_payment_gateways' ); ?>
				
				<h2><?php _e( 'Active Gateway', 'nexus' ); ?></h2>
				
				<table class="form-table">
					<tr>
						<th scope="row"><?php _e( 'Primary Gateway', 'nexus' ); ?></th>
						<td>
							<select name="nexus_active_payment_gateway">
								<option value="razorpay" <?php selected( get_option( 'nexus_active_payment_gateway', 'razorpay' ), 'razorpay' ); ?>>
									Razorpay (Recommended for India)
								</option>
								<option value="paypal" <?php selected( get_option( 'nexus_active_payment_gateway' ), 'paypal' ); ?>>
									PayPal (Global)
								</option>
							</select>
							<p class="description">
								<?php _e( 'Choose your primary payment gateway. Recommended: Razorpay for India, PayPal for global.', 'nexus' ); ?>
							</p>
						</td>
					</tr>
				</table>
				
				<hr />
				
				<h2><?php _e( 'Razorpay Settings', 'nexus' ); ?></h2>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="nexus_razorpay_key_id"><?php _e( 'Key ID', 'nexus' ); ?></label>
						</th>
						<td>
							<input type="text" 
							       id="nexus_razorpay_key_id" 
							       name="nexus_razorpay_key_id" 
							       value="<?php echo esc_attr( get_option( 'nexus_razorpay_key_id', '' ) ); ?>" 
							       class="regular-text" 
							       placeholder="rzp_live_xxxxx" />
							<p class="description">
								<?php _e( 'Your Razorpay Key ID (starts with rzp_test_ or rzp_live_)', 'nexus' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="nexus_razorpay_secret"><?php _e( 'Key Secret', 'nexus' ); ?></label>
						</th>
						<td>
							<input type="password" 
							       id="nexus_razorpay_secret" 
							       name="nexus_razorpay_secret" 
							       value="<?php echo esc_attr( get_option( 'nexus_razorpay_secret', '' ) ); ?>" 
							       class="regular-text" />
							<p class="description">
								<?php _e( 'Your Razorpay Key Secret', 'nexus' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="nexus_razorpay_webhook_secret"><?php _e( 'Webhook Secret', 'nexus' ); ?></label>
						</th>
						<td>
							<input type="password" 
							       id="nexus_razorpay_webhook_secret" 
							       name="nexus_razorpay_webhook_secret" 
							       value="<?php echo esc_attr( get_option( 'nexus_razorpay_webhook_secret', '' ) ); ?>" 
							       class="regular-text" />
							<p class="description">
								<?php _e( 'Webhook secret for verifying webhooks', 'nexus' ); ?><br>
								<strong>Webhook URL:</strong> <code><?php echo admin_url( 'admin-ajax.php?action=nexus_razorpay_webhook' ); ?></code>
							</p>
						</td>
					</tr>
				</table>
				
				<hr />
				
				<h2><?php _e( 'PayPal Settings', 'nexus' ); ?></h2>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="nexus_paypal_mode"><?php _e( 'Mode', 'nexus' ); ?></label>
						</th>
						<td>
							<select id="nexus_paypal_mode" name="nexus_paypal_mode">
								<option value="sandbox" <?php selected( get_option( 'nexus_paypal_mode', 'sandbox' ), 'sandbox' ); ?>>Sandbox (Test)</option>
								<option value="live" <?php selected( get_option( 'nexus_paypal_mode' ), 'live' ); ?>>Live (Production)</option>
							</select>
							<p class="description">
								<?php _e( 'Use Sandbox for testing, Live for production', 'nexus' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="nexus_paypal_client_id"><?php _e( 'Client ID', 'nexus' ); ?></label>
						</th>
						<td>
							<input type="text" 
							       id="nexus_paypal_client_id" 
							       name="nexus_paypal_client_id" 
							       value="<?php echo esc_attr( get_option( 'nexus_paypal_client_id', '' ) ); ?>" 
							       class="regular-text" />
							<p class="description">
								<?php _e( 'Your PayPal REST API Client ID', 'nexus' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="nexus_paypal_secret"><?php _e( 'Secret', 'nexus' ); ?></label>
						</th>
						<td>
							<input type="password" 
							       id="nexus_paypal_secret" 
							       name="nexus_paypal_secret" 
							       value="<?php echo esc_attr( get_option( 'nexus_paypal_secret', '' ) ); ?>" 
							       class="regular-text" />
							<p class="description">
								<?php _e( 'Your PayPal REST API Secret', 'nexus' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><?php _e( 'Webhook URL', 'nexus' ); ?></th>
						<td>
							<code><?php echo admin_url( 'admin-ajax.php?action=nexus_paypal_webhook' ); ?></code>
							<p class="description">
								<?php _e( 'Add this URL in PayPal Dashboard → Webhooks', 'nexus' ); ?>
							</p>
						</td>
					</tr>
				</table>
				
				<?php submit_button(); ?>
			</form>
			
			<hr />
			
			<h2><?php _e( 'Setup Instructions', 'nexus' ); ?></h2>
			
			<h3>Razorpay Setup:</h3>
			<ol>
				<li>Go to <a href="https://dashboard.razorpay.com/" target="_blank">Razorpay Dashboard</a></li>
				<li>Navigate to Settings → API Keys</li>
				<li>Generate live keys (or use test keys for testing)</li>
				<li>Copy Key ID and Secret to form above</li>
				<li>Set up webhook with URL shown above</li>
			</ol>
			
			<h3>PayPal Setup:</h3>
			<ol>
				<li>Go to <a href="https://developer.paypal.com/" target="_blank">PayPal Developer</a></li>
				<li>Create a REST API app</li>
				<li>Get Client ID and Secret</li>
				<li>Add webhook URL shown above</li>
				<li>Subscribe to CHECKOUT.ORDER.APPROVED event</li>
			</ol>
		</div>
		<?php
	}
}

// Initialize
Nexus_Payment_Gateway_Multi::instance();
