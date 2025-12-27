<?php
/**
 * Nexus License Manager
 * 
 * Validates licenses for Pro, Advanced, and Agency tiers
 * Code is visible (GPL) but won't function without valid license
 * 
 * @package Nexus
 * @since 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Nexus_License_Manager {
	
	/**
	 * License server URL
	 * IMPORTANT: Change this to YOUR domain
	 */
	private $license_server = 'https://yoursite.com/wp-json/nexus-licenses/v1/';
	
	/**
	 * License types
	 */
	const TIER_FREE = 'free';
	const TIER_PRO = 'pro';
	const TIER_ADVANCED = 'advanced';
	const TIER_AGENCY = 'agency';
	
	/**
	 * Current license data
	 */
	private $license_data = null;
	
	/**
	 * Singleton instance
	 */
	private static $instance = null;
	
	/**
	 * Get instance
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
		// Load license data from database
		$this->load_license();
		
		// Add hooks
		add_action( 'admin_menu', array( $this, 'add_license_page' ) );
		add_action( 'admin_notices', array( $this, 'show_license_notices' ) );
		add_action( 'admin_init', array( $this, 'handle_license_actions' ) );
		
		// Daily license validation
		add_action( 'nexus_daily_license_check', array( $this, 'validate_license' ) );
		if ( ! wp_next_scheduled( 'nexus_daily_license_check' ) ) {
			wp_schedule_event( time(), 'daily', 'nexus_daily_license_check' );
		}
	}
	
	/**
	 * Load license from database
	 */
	private function load_license() {
		$this->license_data = get_option( 'nexus_license_data', array(
			'key' => '',
			'tier' => self::TIER_FREE,
			'status' => 'inactive',
			'expires' => 0,
			'site_url' => get_site_url(),
			'last_check' => 0,
		) );
	}
	
	/**
	 * Save license to database
	 */
	private function save_license() {
		update_option( 'nexus_license_data', $this->license_data );
	}
	
	/**
	 * Check if feature is available for current license
	 * 
	 * @param string $feature Feature identifier
	 * @return bool True if feature available
	 */
	public function has_feature( $feature ) {
		// Feature to tier mapping
		$feature_tiers = array(
			// Pro Tier Features
			'cloud_storage' => array( self::TIER_PRO, self::TIER_ADVANCED, self::TIER_AGENCY ),
			'payment_gateway' => array( self::TIER_PRO, self::TIER_ADVANCED, self::TIER_AGENCY ),
			'template_sync' => array( self::TIER_PRO, self::TIER_ADVANCED, self::TIER_AGENCY ),
			'credits_system' => array( self::TIER_PRO, self::TIER_ADVANCED, self::TIER_AGENCY ),
			'database_schema' => array( self::TIER_PRO, self::TIER_ADVANCED, self::TIER_AGENCY ),
			
			// Advanced Tier Features
			'theme_builder' => array( self::TIER_ADVANCED, self::TIER_AGENCY ),
			'seo_manager' => array( self::TIER_ADVANCED, self::TIER_AGENCY ),
			'performance_monitor' => array( self::TIER_ADVANCED, self::TIER_AGENCY ),
			'ai_template_generator' => array( self::TIER_ADVANCED, self::TIER_AGENCY ),
			'advanced_filtering' => array( self::TIER_ADVANCED, self::TIER_AGENCY ),
			'form_builder' => array( self::TIER_ADVANCED, self::TIER_AGENCY ),
			'loop_builder' => array( self::TIER_ADVANCED, self::TIER_AGENCY ),
			
			// Agency Tier Features
			'white_label' => array( self::TIER_AGENCY ),
			'agency_dashboard' => array( self::TIER_AGENCY ),
			'ab_testing' => array( self::TIER_AGENCY ),
			'analytics' => array( self::TIER_AGENCY ),
			'client_portal' => array( self::TIER_AGENCY ),
		);
		
		// If feature not defined, assume free
		if ( ! isset( $feature_tiers[ $feature ] ) ) {
			return true;
		}
		
		// Check if current tier has access
		$current_tier = $this->get_tier();
		return in_array( $current_tier, $feature_tiers[ $feature ], true );
	}
	
	/**
	 * Get current license tier
	 * 
	 * @return string Tier identifier
	 */
	public function get_tier() {
		// If license not active, return free
		if ( ! $this->is_license_valid() ) {
			return self::TIER_FREE;
		}
		
		return $this->license_data['tier'];
	}
	
	/**
	 * Check if license is valid
	 * 
	 * @return bool True if valid
	 */
	public function is_license_valid() {
		// No license key = free tier
		if ( empty( $this->license_data['key'] ) ) {
			return false;
		}
		
		// Check status
		if ( 'active' !== $this->license_data['status'] ) {
			return false;
		}
		
		// Check expiration (0 = lifetime)
		if ( $this->license_data['expires'] > 0 && $this->license_data['expires'] < time() ) {
			return false;
		}
		
		// Check if validation is recent (within 7 days)
		$last_check = $this->license_data['last_check'];
		if ( $last_check < ( time() - ( 7 * DAY_IN_SECONDS ) ) ) {
			// Validate now
			$this->validate_license();
		}
		
		return true;
	}
	
	/**
	 * Activate license key
	 * 
	 * @param string $license_key License key
	 * @return bool|WP_Error True on success, WP_Error on failure
	 */
	public function activate_license( $license_key ) {
		$license_key = sanitize_text_field( trim( $license_key ) );
		
		if ( empty( $license_key ) ) {
			return new WP_Error( 'empty_key', 'License key is required' );
		}
		
		// Call license server
		$response = wp_remote_post( $this->license_server . 'activate', array(
			'timeout' => 15,
			'body' => array(
				'license_key' => $license_key,
				'site_url' => get_site_url(),
				'theme_version' => NEXUS_VERSION,
			),
		) );
		
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		
		$response_code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		
		if ( 200 !== $response_code || empty( $body['success'] ) ) {
			$message = ! empty( $body['message'] ) ? $body['message'] : 'License activation failed';
			return new WP_Error( 'activation_failed', $message );
		}
		
		// Save license data
		$this->license_data = array(
			'key' => $license_key,
			'tier' => $body['tier'],
			'status' => 'active',
			'expires' => $body['expires'],
			'site_url' => get_site_url(),
			'last_check' => time(),
		);
		
		$this->save_license();
		
		return true;
	}
	
	/**
	 * Deactivate license
	 * 
	 * @return bool|WP_Error True on success
	 */
	public function deactivate_license() {
		if ( empty( $this->license_data['key'] ) ) {
			return new WP_Error( 'no_license', 'No license to deactivate' );
		}
		
		// Call license server
		$response = wp_remote_post( $this->license_server . 'deactivate', array(
			'timeout' => 15,
			'body' => array(
				'license_key' => $this->license_data['key'],
				'site_url' => get_site_url(),
			),
		) );
		
		// Reset license data (even if API call fails)
		$this->license_data = array(
			'key' => '',
			'tier' => self::TIER_FREE,
			'status' => 'inactive',
			'expires' => 0,
			'site_url' => get_site_url(),
			'last_check' => 0,
		);
		
		$this->save_license();
		
		return true;
	}
	
	/**
	 * Validate existing license
	 * 
	 * @return bool True if still valid
	 */
	public function validate_license() {
		if ( empty( $this->license_data['key'] ) ) {
			return false;
		}
		
		// Call license server
		$response = wp_remote_post( $this->license_server . 'validate', array(
			'timeout' => 15,
			'body' => array(
				'license_key' => $this->license_data['key'],
				'site_url' => get_site_url(),
				'theme_version' => NEXUS_VERSION,
			),
		) );
		
		if ( is_wp_error( $response ) ) {
			// Don't deactivate on network error, just update last check
			$this->license_data['last_check'] = time();
			$this->save_license();
			return false;
		}
		
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		
		if ( ! empty( $body['success'] ) && 'active' === $body['status'] ) {
			// Update license data
			$this->license_data['status'] = 'active';
			$this->license_data['expires'] = $body['expires'];
			$this->license_data['tier'] = $body['tier'];
			$this->license_data['last_check'] = time();
			$this->save_license();
			return true;
		} else {
			// License no longer valid
			$this->license_data['status'] = 'inactive';
			$this->license_data['last_check'] = time();
			$this->save_license();
			return false;
		}
	}
	
	/**
	 * Add license page to admin menu
	 */
	public function add_license_page() {
		add_submenu_page(
			'themes.php',
			__( 'Nexus License', 'nexus' ),
			__( 'License', 'nexus' ),
			'manage_options',
			'nexus-license',
			array( $this, 'render_license_page' )
		);
	}
	
	/**
	 * Render license page
	 */
	public function render_license_page() {
		$tier = $this->get_tier();
		$is_valid = $this->is_license_valid();
		$license_key = $this->license_data['key'];
		$expires = $this->license_data['expires'];
		
		?>
		<div class="wrap">
			<h1><?php _e( 'Nexus Theme License', 'nexus' ); ?></h1>
			
			<div class="card">
				<h2><?php _e( 'License Status', 'nexus' ); ?></h2>
				
				<?php if ( $is_valid ) : ?>
					<div class="notice notice-success inline">
						<p>
							<strong><?php _e( 'License Active!', 'nexus' ); ?></strong><br>
							<?php printf( __( 'Tier: %s', 'nexus' ), '<strong>' . strtoupper( $tier ) . '</strong>' ); ?><br>
							<?php
							if ( $expires > 0 ) {
								printf(
									__( 'Expires: %s', 'nexus' ),
									date_i18n( get_option( 'date_format' ), $expires )
								);
							} else {
								_e( 'Lifetime License', 'nexus' );
							}
							?>
						</p>
					</div>
					
					<form method="post">
						<?php wp_nonce_field( 'nexus_license_deactivate', 'nexus_license_nonce' ); ?>
						<input type="hidden" name="action" value="deactivate_license">
						<p>
							<strong><?php _e( 'License Key:', 'nexus' ); ?></strong>
							<?php echo esc_html( substr( $license_key, 0, 8 ) . '...' . substr( $license_key, -8 ) ); ?>
						</p>
						<p>
							<button type="submit" class="button">
								<?php _e( 'Deactivate License', 'nexus' ); ?>
							</button>
							<button type="submit" name="action" value="validate_license" class="button">
								<?php _e( 'Re-validate License', 'nexus' ); ?>
							</button>
						</p>
					</form>
				<?php else : ?>
					<div class="notice notice-warning inline">
						<p>
							<strong><?php _e( 'No Active License', 'nexus' ); ?></strong><br>
							<?php _e( 'You are using the Free tier. Activate a license to unlock premium features.', 'nexus' ); ?>
						</p>
					</div>
					
					<form method="post">
						<?php wp_nonce_field( 'nexus_license_activate', 'nexus_license_nonce' ); ?>
						<input type="hidden" name="action" value="activate_license">
						
						<table class="form-table">
							<tr>
								<th scope="row">
									<label for="license_key"><?php _e( 'License Key', 'nexus' ); ?></label>
								</th>
								<td>
									<input type="text" id="license_key" name="license_key" class="regular-text" placeholder="XXXX-XXXX-XXXX-XXXX" required>
									<p class="description">
										<?php _e( 'Enter your license key from your purchase receipt.', 'nexus' ); ?>
										<a href="https://yoursite.com/my-account/" target="_blank">
											<?php _e( 'Get your license key', 'nexus' ); ?>
										</a>
									</p>
								</td>
							</tr>
						</table>
						
						<p class="submit">
							<button type="submit" class="button button-primary">
								<?php _e( 'Activate License', 'nexus' ); ?>
							</button>
						</p>
					</form>
				<?php endif; ?>
			</div>
			
			<div class="card">
				<h2><?php _e( 'Available Features', 'nexus' ); ?></h2>
				
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th><?php _e( 'Feature', 'nexus' ); ?></th>
							<th><?php _e( 'Free', 'nexus' ); ?></th>
							<th><?php _e( 'Pro', 'nexus' ); ?></th>
							<th><?php _e( 'Advanced', 'nexus' ); ?></th>
							<th><?php _e( 'Agency', 'nexus' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Cloud Storage (DigitalOcean)</td>
							<td>❌</td>
							<td>✅</td>
							<td>✅</td>
							<td>✅</td>
						</tr>
						<tr>
							<td>Payment Gateways (Razorpay/PayPal)</td>
							<td>❌</td>
							<td>✅</td>
							<td>✅</td>
							<td>✅</td>
						</tr>
						<tr>
							<td>Template Cloud Sync</td>
							<td>❌</td>
							<td>✅</td>
							<td>✅</td>
							<td>✅</td>
						</tr>
						<tr>
							<td>Theme Builder</td>
							<td>❌</td>
							<td>❌</td>
							<td>✅</td>
							<td>✅</td>
						</tr>
						<tr>
							<td>AI Template Generator</td>
							<td>❌</td>
							<td>❌</td>
							<td>✅</td>
							<td>✅</td>
						</tr>
						<tr>
							<td>SEO Manager</td>
							<td>❌</td>
							<td>❌</td>
							<td>✅</td>
							<td>✅</td>
						</tr>
						<tr>
							<td>White Label System</td>
							<td>❌</td>
							<td>❌</td>
							<td>❌</td>
							<td>✅</td>
						</tr>
						<tr>
							<td>A/B Testing</td>
							<td>❌</td>
							<td>❌</td>
							<td>❌</td>
							<td>✅</td>
						</tr>
						<tr>
							<td>Agency Dashboard</td>
							<td>❌</td>
							<td>❌</td>
							<td>❌</td>
							<td>✅</td>
						</tr>
					</tbody>
				</table>
				
				<p>
					<a href="https://yoursite.com/pricing/" class="button button-primary" target="_blank">
						<?php _e( 'Upgrade Now', 'nexus' ); ?>
					</a>
				</p>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Handle license actions
	 */
	public function handle_license_actions() {
		if ( ! isset( $_POST['action'] ) || ! isset( $_POST['nexus_license_nonce'] ) ) {
			return;
		}
		
		$action = sanitize_text_field( $_POST['action'] );
		
		if ( 'activate_license' === $action ) {
			if ( ! wp_verify_nonce( $_POST['nexus_license_nonce'], 'nexus_license_activate' ) ) {
				return;
			}
			
			$license_key = sanitize_text_field( $_POST['license_key'] );
			$result = $this->activate_license( $license_key );
			
			if ( is_wp_error( $result ) ) {
				add_settings_error(
					'nexus_license',
					'activation_failed',
					$result->get_error_message(),
					'error'
				);
			} else {
				add_settings_error(
					'nexus_license',
					'activation_success',
					__( 'License activated successfully!', 'nexus' ),
					'success'
				);
			}
		} elseif ( 'deactivate_license' === $action ) {
			if ( ! wp_verify_nonce( $_POST['nexus_license_nonce'], 'nexus_license_deactivate' ) ) {
				return;
			}
			
			$this->deactivate_license();
			
			add_settings_error(
				'nexus_license',
				'deactivation_success',
				__( 'License deactivated.', 'nexus' ),
				'success'
			);
		} elseif ( 'validate_license' === $action ) {
			if ( ! wp_verify_nonce( $_POST['nexus_license_nonce'], 'nexus_license_deactivate' ) ) {
				return;
			}
			
			$result = $this->validate_license();
			
			if ( $result ) {
				add_settings_error(
					'nexus_license',
					'validation_success',
					__( 'License is valid!', 'nexus' ),
					'success'
				);
			} else {
				add_settings_error(
					'nexus_license',
					'validation_failed',
					__( 'License validation failed. Please contact support.', 'nexus' ),
					'error'
				);
			}
		}
	}
	
	/**
	 * Show license notices
	 */
	public function show_license_notices() {
		settings_errors( 'nexus_license' );
		
		// Show warning if license expired
		if ( ! $this->is_license_valid() && ! empty( $this->license_data['key'] ) ) {
			?>
			<div class="notice notice-warning">
				<p>
					<strong><?php _e( 'Nexus License Expired or Invalid', 'nexus' ); ?></strong><br>
					<?php _e( 'Premium features are disabled. Please renew your license.', 'nexus' ); ?>
					<a href="<?php echo admin_url( 'themes.php?page=nexus-license' ); ?>">
						<?php _e( 'Manage License', 'nexus' ); ?>
					</a>
				</p>
			</div>
			<?php
		}
	}
	
	/**
	 * Get feature blocker message
	 * 
	 * @param string $feature Feature name
	 * @return string HTML message
	 */
	public function get_upgrade_message( $feature ) {
		$tier = $this->get_tier();
		
		$messages = array(
			self::TIER_FREE => sprintf(
				__( 'This feature requires a Pro license or higher. <a href="%s">Upgrade Now</a>', 'nexus' ),
				'https://yoursite.com/pricing/'
			),
			self::TIER_PRO => sprintf(
				__( 'This feature requires an Advanced license or higher. <a href="%s">Upgrade Now</a>', 'nexus' ),
				'https://yoursite.com/pricing/'
			),
			self::TIER_ADVANCED => sprintf(
				__( 'This feature requires an Agency license. <a href="%s">Upgrade Now</a>', 'nexus' ),
				'https://yoursite.com/pricing/'
			),
		);
		
		return isset( $messages[ $tier ] ) ? $messages[ $tier ] : '';
	}
}

// Initialize
Nexus_License_Manager::instance();
