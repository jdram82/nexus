<?php
/**
 * Popup Targeting - Control Where Popups Display
 *
 * @package Nexus_Pro
 * @subpackage Popup_Builder
 * @since 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Popup Targeting Class
 *
 * Handles popup display targeting based on:
 * - Page/post types
 * - User roles
 * - Device types
 * - Geolocation
 * - Referrer sources
 */
class Nexus_Popup_Targeting {

	/**
	 * Instance
	 *
	 * @var Nexus_Popup_Targeting
	 */
	private static $instance = null;

	/**
	 * Get instance
	 *
	 * @return Nexus_Popup_Targeting
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
		// Constructor
	}

	/**
	 * Check if popup should display on current page
	 *
	 * @param int $popup_id Popup ID
	 * @return bool
	 */
	public function should_display_popup( $popup_id ) {
		$targeting = get_post_meta( $popup_id, '_nexus_popup_targeting', true );

		if ( empty( $targeting ) ) {
			return true; // No targeting rules = show everywhere
		}

		// Check all targeting rules
		if ( ! $this->check_page_targeting( $targeting ) ) {
			return false;
		}

		if ( ! $this->check_user_targeting( $targeting ) ) {
			return false;
		}

		if ( ! $this->check_device_targeting( $targeting ) ) {
			return false;
		}

		if ( ! $this->check_referrer_targeting( $targeting ) ) {
			return false;
		}

		// Check frequency (cookies)
		if ( ! $this->check_frequency( $popup_id, $targeting ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check page targeting rules
	 *
	 * @param array $targeting Targeting settings
	 * @return bool
	 */
	private function check_page_targeting( $targeting ) {
		if ( empty( $targeting['pages'] ) ) {
			return true;
		}

		$include = $targeting['pages']['include'] ?? array();
		$exclude = $targeting['pages']['exclude'] ?? array();

		// Check exclusions first
		if ( ! empty( $exclude ) ) {
			if ( $this->match_page_rules( $exclude ) ) {
				return false;
			}
		}

		// Check inclusions
		if ( ! empty( $include ) ) {
			return $this->match_page_rules( $include );
		}

		return true;
	}

	/**
	 * Match current page against rules
	 *
	 * @param array $rules Page rules
	 * @return bool
	 */
	private function match_page_rules( $rules ) {
		foreach ( $rules as $rule ) {
			switch ( $rule['type'] ) {
				case 'all':
					return true;

				case 'homepage':
					if ( is_front_page() ) {
						return true;
					}
					break;

				case 'posts':
					if ( is_singular( 'post' ) ) {
						return true;
					}
					break;

				case 'pages':
					if ( is_page() ) {
						return true;
					}
					break;

				case 'archives':
					if ( is_archive() ) {
						return true;
					}
					break;

				case 'search':
					if ( is_search() ) {
						return true;
					}
					break;

				case 'specific_page':
					if ( is_page( $rule['value'] ) ) {
						return true;
					}
					break;

				case 'specific_post':
					if ( is_single( $rule['value'] ) ) {
						return true;
					}
					break;

				case 'post_type':
					if ( is_singular( $rule['value'] ) ) {
						return true;
					}
					break;

				case 'taxonomy':
					if ( is_tax( $rule['value'] ) || has_term( '', $rule['value'] ) ) {
						return true;
					}
					break;
			}
		}

		return false;
	}

	/**
	 * Check user targeting rules
	 *
	 * @param array $targeting Targeting settings
	 * @return bool
	 */
	private function check_user_targeting( $targeting ) {
		if ( empty( $targeting['users'] ) ) {
			return true;
		}

		$user_rules = $targeting['users'];

		// Check logged in/out status
		if ( isset( $user_rules['login_status'] ) ) {
			switch ( $user_rules['login_status'] ) {
				case 'logged_in':
					if ( ! is_user_logged_in() ) {
						return false;
					}
					break;

				case 'logged_out':
					if ( is_user_logged_in() ) {
						return false;
					}
					break;
			}
		}

		// Check user roles
		if ( ! empty( $user_rules['roles'] ) && is_user_logged_in() ) {
			$user = wp_get_current_user();
			$has_role = false;

			foreach ( $user_rules['roles'] as $role ) {
				if ( in_array( $role, $user->roles, true ) ) {
					$has_role = true;
					break;
				}
			}

			if ( ! $has_role ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check device targeting rules
	 *
	 * @param array $targeting Targeting settings
	 * @return bool
	 */
	private function check_device_targeting( $targeting ) {
		if ( empty( $targeting['devices'] ) ) {
			return true;
		}

		$allowed_devices = $targeting['devices'];
		$current_device = $this->detect_device();

		return in_array( $current_device, $allowed_devices, true );
	}

	/**
	 * Detect current device type
	 *
	 * @return string
	 */
	private function detect_device() {
		if ( wp_is_mobile() ) {
			// Simple mobile detection - can be enhanced
			$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
			
			if ( preg_match( '/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $user_agent ) ) {
				return 'tablet';
			}
			
			return 'mobile';
		}

		return 'desktop';
	}

	/**
	 * Check referrer targeting rules
	 *
	 * @param array $targeting Targeting settings
	 * @return bool
	 */
	private function check_referrer_targeting( $targeting ) {
		if ( empty( $targeting['referrer'] ) ) {
			return true;
		}

		$referrer_rules = $targeting['referrer'];
		$referrer = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';

		if ( empty( $referrer ) && ! empty( $referrer_rules['require_referrer'] ) ) {
			return false;
		}

		// Check specific referrers
		if ( ! empty( $referrer_rules['sources'] ) ) {
			$matched = false;

			foreach ( $referrer_rules['sources'] as $source ) {
				if ( strpos( $referrer, $source ) !== false ) {
					$matched = true;
					break;
				}
			}

			if ( ! $matched ) {
				return false;
			}
		}

		// Check search engines
		if ( ! empty( $referrer_rules['from_search_engines'] ) ) {
			$search_engines = array( 'google', 'bing', 'yahoo', 'duckduckgo', 'baidu' );
			$from_search = false;

			foreach ( $search_engines as $engine ) {
				if ( strpos( $referrer, $engine ) !== false ) {
					$from_search = true;
					break;
				}
			}

			if ( ! $from_search ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check display frequency rules
	 *
	 * @param int   $popup_id  Popup ID
	 * @param array $targeting Targeting settings
	 * @return bool
	 */
	private function check_frequency( $popup_id, $targeting ) {
		$cookie_name = 'nexus_popup_' . $popup_id;

		// Check if popup was already shown
		if ( isset( $_COOKIE[ $cookie_name ] ) ) {
			$shown_at = intval( $_COOKIE[ $cookie_name ] );
			$show_again_days = $targeting['show_again_days'] ?? 7;

			if ( time() < ( $shown_at + ( $show_again_days * DAY_IN_SECONDS ) ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Render targeting settings HTML
	 *
	 * @param int $popup_id Popup ID
	 */
	public function render_targeting_settings( $popup_id ) {
		$targeting = get_post_meta( $popup_id, '_nexus_popup_targeting', true );
		$targeting = wp_parse_args(
			$targeting,
			array(
				'pages'   => array(),
				'users'   => array(),
				'devices' => array( 'desktop', 'tablet', 'mobile' ),
				'referrer' => array(),
			)
		);

		?>
		<div class="nexus-popup-targeting">
			<h3><?php esc_html_e( 'Display Rules', 'nexus-pro' ); ?></h3>
			
			<!-- Page Targeting -->
			<div class="targeting-section">
				<h4><?php esc_html_e( 'Page Targeting', 'nexus-pro' ); ?></h4>
				
				<div class="targeting-rules">
					<label><?php esc_html_e( 'Show on these pages:', 'nexus-pro' ); ?></label>
					<select name="nexus_popup_targeting[pages][include][]" multiple class="nexus-select2">
						<option value="all"><?php esc_html_e( 'All Pages', 'nexus-pro' ); ?></option>
						<option value="homepage"><?php esc_html_e( 'Homepage', 'nexus-pro' ); ?></option>
						<option value="posts"><?php esc_html_e( 'All Posts', 'nexus-pro' ); ?></option>
						<option value="pages"><?php esc_html_e( 'All Pages', 'nexus-pro' ); ?></option>
						<option value="archives"><?php esc_html_e( 'Archives', 'nexus-pro' ); ?></option>
						<option value="search"><?php esc_html_e( 'Search Results', 'nexus-pro' ); ?></option>
					</select>

					<label><?php esc_html_e( 'Hide on these pages:', 'nexus-pro' ); ?></label>
					<select name="nexus_popup_targeting[pages][exclude][]" multiple class="nexus-select2">
						<option value="homepage"><?php esc_html_e( 'Homepage', 'nexus-pro' ); ?></option>
						<option value="posts"><?php esc_html_e( 'All Posts', 'nexus-pro' ); ?></option>
						<option value="pages"><?php esc_html_e( 'All Pages', 'nexus-pro' ); ?></option>
					</select>
				</div>
			</div>

			<!-- User Targeting -->
			<div class="targeting-section">
				<h4><?php esc_html_e( 'User Targeting', 'nexus-pro' ); ?></h4>
				
				<label><?php esc_html_e( 'Login Status', 'nexus-pro' ); ?></label>
				<select name="nexus_popup_targeting[users][login_status]">
					<option value="all"><?php esc_html_e( 'All Users', 'nexus-pro' ); ?></option>
					<option value="logged_in"><?php esc_html_e( 'Logged In Only', 'nexus-pro' ); ?></option>
					<option value="logged_out"><?php esc_html_e( 'Logged Out Only', 'nexus-pro' ); ?></option>
				</select>

				<label><?php esc_html_e( 'User Roles', 'nexus-pro' ); ?></label>
				<select name="nexus_popup_targeting[users][roles][]" multiple>
					<?php
					$roles = wp_roles()->get_names();
					foreach ( $roles as $role => $name ) :
						?>
						<option value="<?php echo esc_attr( $role ); ?>">
							<?php echo esc_html( $name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<!-- Device Targeting -->
			<div class="targeting-section">
				<h4><?php esc_html_e( 'Device Targeting', 'nexus-pro' ); ?></h4>
				
				<label>
					<input type="checkbox" 
						   name="nexus_popup_targeting[devices][]" 
						   value="desktop" 
						   <?php checked( in_array( 'desktop', $targeting['devices'], true ) ); ?>>
					<?php esc_html_e( 'Desktop', 'nexus-pro' ); ?>
				</label>

				<label>
					<input type="checkbox" 
						   name="nexus_popup_targeting[devices][]" 
						   value="tablet" 
						   <?php checked( in_array( 'tablet', $targeting['devices'], true ) ); ?>>
					<?php esc_html_e( 'Tablet', 'nexus-pro' ); ?>
				</label>

				<label>
					<input type="checkbox" 
						   name="nexus_popup_targeting[devices][]" 
						   value="mobile" 
						   <?php checked( in_array( 'mobile', $targeting['devices'], true ) ); ?>>
					<?php esc_html_e( 'Mobile', 'nexus-pro' ); ?>
				</label>
			</div>

			<!-- Referrer Targeting -->
			<div class="targeting-section">
				<h4><?php esc_html_e( 'Traffic Source', 'nexus-pro' ); ?></h4>
				
				<label>
					<input type="checkbox" 
						   name="nexus_popup_targeting[referrer][from_search_engines]" 
						   value="1">
					<?php esc_html_e( 'From Search Engines Only', 'nexus-pro' ); ?>
				</label>

				<label>
					<?php esc_html_e( 'Specific Referrers (one per line)', 'nexus-pro' ); ?>
				</label>
				<textarea name="nexus_popup_targeting[referrer][sources]" 
						  rows="4" 
						  placeholder="google.com&#10;facebook.com&#10;twitter.com"></textarea>
			</div>
		</div>
		<?php
	}
}

// Initialize
Nexus_Popup_Targeting::get_instance();
