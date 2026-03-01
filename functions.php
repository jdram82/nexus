<?php
/**
 * Nexus Theme Functions
 *
 * @package Nexus
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Define Constants
 */
define( 'NEXUS_VERSION', '3.2.26' );
define( 'NEXUS_DIR', get_template_directory() );
define( 'NEXUS_URI', get_template_directory_uri() );

/**
 * Theme Setup
 */
require_once NEXUS_DIR . '/inc/class-nexus-theme-setup.php';

/**
 * Customizer
 */
require_once NEXUS_DIR . '/inc/customizer/class-nexus-customizer.php';

/**
 * Custom Post Types
 */
require_once NEXUS_DIR . '/inc/post-types/class-nexus-products.php';
require_once NEXUS_DIR . '/inc/post-types/class-nexus-projects.php';
require_once NEXUS_DIR . '/inc/post-types/class-nexus-downloads.php';

/**
 * Template Functions
 */
require_once NEXUS_DIR . '/inc/template-functions.php';

/**
 * Template Tags
 */
require_once NEXUS_DIR . '/inc/template-tags.php';

/**
 * Block Patterns - Starter Templates
 */
require_once NEXUS_DIR . '/inc/block-patterns.php';

/**
 * Theme Updater - GitHub Integration
 */
require_once NEXUS_DIR . '/inc/class-nexus-theme-updater.php';

/**
 * License Manager - Protects Premium Features
 */
require_once NEXUS_DIR . '/inc/class-nexus-license-manager.php';

/**
 * Admin Dashboard
 */
require_once NEXUS_DIR . '/inc/admin/class-nexus-admin.php';

/**
 * Enqueue Scripts and Styles
 */
require_once NEXUS_DIR . '/inc/class-nexus-enqueue.php';

/**
 * Plugin Harmony - Auto-compatibility (Free Tier)
 */
require_once NEXUS_DIR . '/inc/class-nexus-plugin-harmony.php';

/**
 * REST API - Basic Endpoints (Free Tier)
 */
require_once NEXUS_DIR . '/inc/api/class-nexus-rest-api.php';

/**
 * WooCommerce Support
 */
if ( class_exists( 'WooCommerce' ) ) {
	require_once NEXUS_DIR . '/inc/woocommerce/class-nexus-woocommerce.php';
}

/**
 * Initialize Theme
 */
// Initialize Theme Setup first (handles after_setup_theme internally)
Nexus_Theme_Setup::instance();

function nexus_init() {
	Nexus_Customizer::instance();
	Nexus_Products::instance();
	Nexus_Projects::instance();
	Nexus_Downloads::instance();
	Nexus_Enqueue::instance();
	Nexus_Theme_Updater::instance(); // Initialize automatic updates
	Nexus_Admin::instance(); // Initialize admin dashboard
	
	if ( class_exists( 'WooCommerce' ) ) {
		Nexus_WooCommerce::instance();
	}
}
add_action( 'after_setup_theme', 'nexus_init' );

/**
 * UL/NEC Workflow Functions
 * Handles page protection, redirects, and user flow
 */

/**
 * Protect dashboard pages - redirect to login if not authenticated
 */
function ulnec_protect_dashboard_pages() {
	// Skip if in admin area
	if ( is_admin() ) {
		return;
	}
	
	// Get current page
	$current_page = get_queried_object();
	
	// Pages that require authentication
	$protected_pages = array(
		'dashboard',
		'download',
		'bug-report',
		'feature-request',
		'billing',
		'account-settings'
	);
	
	// Check if current page is protected and user is not logged in
	if ( $current_page && isset( $current_page->post_name ) && 
	     in_array( $current_page->post_name, $protected_pages ) && 
	     ! is_user_logged_in() ) {
		
		// Save the requested URL for redirect after login
		$redirect_url = add_query_arg( 'redirect', urlencode( $_SERVER['REQUEST_URI'] ), home_url( '/login' ) );
		wp_redirect( $redirect_url );
		exit;
	}
}
add_action( 'template_redirect', 'ulnec_protect_dashboard_pages' );

/**
 * Redirect logged-in users away from login/register pages
 */
function ulnec_redirect_logged_in_users() {
	// Skip if in admin area
	if ( is_admin() ) {
		return;
	}
	
	// If user is logged in and viewing login or register page
	if ( is_user_logged_in() && ( is_page( 'login' ) || is_page( 'register' ) ) ) {
		wp_redirect( ulnec_get_default_post_login_url() );
		exit;
	}
}
add_action( 'template_redirect', 'ulnec_redirect_logged_in_users' );

/**
 * Get the requested redirect URL from query parameter if present and valid.
 *
 * @return string
 */
function ulnec_get_requested_redirect_url() {
	if ( empty( $_GET['redirect'] ) ) {
		return '';
	}

	$raw_redirect = sanitize_text_field( wp_unslash( $_GET['redirect'] ) );
	$decoded = rawurldecode( $raw_redirect );
	$validated = wp_validate_redirect( $decoded, '' );

	if ( empty( $validated ) ) {
		return '';
	}

	if ( 0 === strpos( $validated, '/' ) ) {
		return home_url( $validated );
	}

	return $validated;
}

/**
 * Get default destination URL after successful login.
 *
 * @return string
 */
function ulnec_get_default_post_login_url() {
	$default_slug = get_option( 'ulnec_post_login_slug', 'download' );
	$default_slug = sanitize_title( (string) $default_slug );

	if ( empty( $default_slug ) ) {
		$default_slug = 'download';
	}

	return home_url( '/' . $default_slug . '/' );
}

/**
 * Handle post-login redirect
 */
function ulnec_login_redirect( $redirect_to, $request, $user ) {
	$requested_redirect = ulnec_get_requested_redirect_url();
	if ( ! empty( $requested_redirect ) ) {
		return $requested_redirect;
	}
	
	return ulnec_get_default_post_login_url();
}
add_filter( 'login_redirect', 'ulnec_login_redirect', 10, 3 );

/**
 * Register setting for post-login destination slug.
 */
function ulnec_register_login_destination_setting() {
	register_setting(
		'general',
		'ulnec_post_login_slug',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_title',
			'default'           => 'download',
		)
	);

	add_settings_field(
		'ulnec_post_login_slug',
		'UL/NEC Post Login Page Slug',
		'ulnec_render_login_destination_setting',
		'general'
	);
}
add_action( 'admin_init', 'ulnec_register_login_destination_setting' );

/**
 * Register UL/NEC billing URL settings.
 */
function ulnec_register_billing_url_settings() {
	register_setting(
		'general',
		'ulnec_checkout_url',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'default'           => home_url( '/billing/' ),
		)
	);

	register_setting(
		'general',
		'ulnec_add_payment_method_url',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'default'           => home_url( '/billing/' ),
		)
	);

	add_settings_field(
		'ulnec_checkout_url',
		'UL/NEC Checkout URL',
		'ulnec_render_checkout_url_setting',
		'general'
	);

	add_settings_field(
		'ulnec_add_payment_method_url',
		'UL/NEC Add Payment Method URL',
		'ulnec_render_payment_method_url_setting',
		'general'
	);
}
add_action( 'admin_init', 'ulnec_register_billing_url_settings' );

/**
 * Render checkout URL setting.
 */
function ulnec_render_checkout_url_setting() {
	$current = get_option( 'ulnec_checkout_url', home_url( '/billing/' ) );
	?>
	<input type="url" name="ulnec_checkout_url" id="ulnec_checkout_url" value="<?php echo esc_attr( $current ); ?>" class="regular-text code" placeholder="https://..." />
	<p class="description">Used by Billing page "Subscribe Now" button.</p>
	<?php
}

/**
 * Render add payment URL setting.
 */
function ulnec_render_payment_method_url_setting() {
	$current = get_option( 'ulnec_add_payment_method_url', home_url( '/billing/' ) );
	?>
	<input type="url" name="ulnec_add_payment_method_url" id="ulnec_add_payment_method_url" value="<?php echo esc_attr( $current ); ?>" class="regular-text code" placeholder="https://..." />
	<p class="description">Used by Billing page "+ Add Payment Method" button.</p>
	<?php
}

/**
 * Get checkout URL for billing actions.
 *
 * @return string
 */
function ulnec_get_checkout_url() {
	$raw_url = get_option( 'ulnec_checkout_url', home_url( '/billing/' ) );
	$url = ulnec_normalize_billing_url( $raw_url );
	$billing_url = ulnec_get_billing_url();

	if ( ! empty( $url ) && ! ulnec_urls_match( $url, $billing_url ) ) {
		return $url;
	}

	return $billing_url . '#payment-method-section';
}

/**
 * Get add-payment-method URL for billing actions.
 *
 * @return string
 */
function ulnec_get_add_payment_method_url() {
	$raw_url = get_option( 'ulnec_add_payment_method_url', '' );
	$url = ulnec_normalize_billing_url( $raw_url );
	$billing_url = ulnec_get_billing_url();

	if ( ! empty( $url ) && ! ulnec_urls_match( $url, $billing_url ) ) {
		return $url;
	}

	$checkout_url = ulnec_get_checkout_url();
	if ( ! empty( $checkout_url ) ) {
		return $checkout_url;
	}

	return $billing_url . '#payment-method-section';
}

/**
 * Get canonical billing page URL.
 *
 * @return string
 */
function ulnec_get_billing_url() {
	return home_url( '/billing/' );
}

/**
 * Compare two URLs while ignoring trailing slash and fragments.
 *
 * @param string $left  First URL.
 * @param string $right Second URL.
 * @return bool
 */
function ulnec_urls_match( $left, $right ) {
	$left  = trim( (string) $left );
	$right = trim( (string) $right );

	if ( '' === $left || '' === $right ) {
		return false;
	}

	$left_path  = wp_parse_url( $left );
	$right_path = wp_parse_url( $right );

	if ( ! is_array( $left_path ) || ! is_array( $right_path ) ) {
		return untrailingslashit( $left ) === untrailingslashit( $right );
	}

	$left_host  = strtolower( (string) ( $left_path['host'] ?? '' ) );
	$right_host = strtolower( (string) ( $right_path['host'] ?? '' ) );
	$left_path_only  = untrailingslashit( (string) ( $left_path['path'] ?? '' ) );
	$right_path_only = untrailingslashit( (string) ( $right_path['path'] ?? '' ) );

	return $left_host === $right_host && $left_path_only === $right_path_only;
}

/**
 * Normalize billing URL from settings.
 *
 * @param string $url Raw URL.
 * @return string
 */
function ulnec_normalize_billing_url( $url ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '';
	}

	if ( 0 === strpos( $url, '//' ) ) {
		$url = 'https:' . $url;
	} elseif ( ! preg_match( '#^https?://#i', $url ) ) {
		$url = 'https://' . ltrim( $url, '/' );
	}

	$validated = wp_http_validate_url( $url );
	if ( false === $validated ) {
		return '';
	}

	return $validated;
}

/**
 * Render post-login destination field.
 */
function ulnec_render_login_destination_setting() {
	$current_slug = get_option( 'ulnec_post_login_slug', 'download' );
	$current_slug = sanitize_title( (string) $current_slug );
	?>
	<input
		type="text"
		name="ulnec_post_login_slug"
		id="ulnec_post_login_slug"
		value="<?php echo esc_attr( $current_slug ); ?>"
		class="regular-text"
		placeholder="download"
	/>
	<p class="description">Enter a page slug (example: <code>dashboard</code>, <code>download</code>). Logged-in users on /login and /register will be redirected here.</p>
	<?php
}

/**
 * Force UL/NEC auth templates for core workflow slugs.
 */
function ulnec_force_auth_templates( $template ) {
	$template_map = array(
		'login'     => NEXUS_DIR . '/page-ulnec-login.php',
		'register'  => NEXUS_DIR . '/page-ulnec-register.php',
		'dashboard' => NEXUS_DIR . '/page-ulnec-dashboard.php',
		'download'  => NEXUS_DIR . '/page-ulnec-download.php',
		'billing'   => NEXUS_DIR . '/page-ulnec-billing.php',
	);

	foreach ( $template_map as $slug => $mapped_template ) {
		if ( is_page( $slug ) && file_exists( $mapped_template ) ) {
			return $mapped_template;
		}
	}

	if ( is_404() ) {
		$request_path = trim( (string) wp_parse_url( home_url( add_query_arg( array(), $_SERVER['REQUEST_URI'] ?? '' ) ), PHP_URL_PATH ), '/' );

		if ( isset( $template_map[ $request_path ] ) && file_exists( $template_map[ $request_path ] ) ) {
			status_header( 200 );
			nocache_headers();
			return $template_map[ $request_path ];
		}
	}

	return $template;
}
add_filter( 'template_include', 'ulnec_force_auth_templates', 99 );

/**
 * Add body class for UL/NEC pages
 */
function ulnec_body_classes( $classes ) {
	$ulnec_pages = array( 'login', 'register', 'dashboard', 'download', 'billing', 'bug-report', 'feature-request', 'account-settings' );
	
	if ( is_page( $ulnec_pages ) ) {
		$classes[] = 'ulnec-page';
	}
	
	return $classes;
}
add_filter( 'body_class', 'ulnec_body_classes' );

/**
 * Prepend quick navigation for logged-in UL/NEC workflow pages.
 */
function ulnec_prepend_logged_in_menu( $content ) {
	if ( is_admin() || ! is_user_logged_in() ) {
		return $content;
	}

	if ( ! is_page() ) {
		return $content;
	}

	static $has_rendered_quick_menu = false;
	if ( $has_rendered_quick_menu ) {
		return $content;
	}

	$ulnec_pages = array(
		'dashboard',
		'download',
		'bug-report',
		'feature-request',
		'billing',
		'account-settings',
		'founders-progress',
	);

	if ( ! is_page( $ulnec_pages ) ) {
		return $content;
	}

	$has_rendered_quick_menu = true;

	$menu = '<div class="ulnec-quick-menu" role="navigation" aria-label="UL/NEC Quick Menu">';
	$menu .= '<a href="' . esc_url( home_url( '/dashboard' ) ) . '">Dashboard</a>';
	$menu .= '<a href="' . esc_url( home_url( '/download' ) ) . '">Download</a>';
	$menu .= '<a href="' . esc_url( home_url( '/bug-report' ) ) . '">Bug Report</a>';
	$menu .= '<a href="' . esc_url( home_url( '/feature-request' ) ) . '">Feature Request</a>';
	$menu .= '<a href="' . esc_url( home_url( '/billing' ) ) . '">Billing</a>';
	$menu .= '<a href="' . esc_url( home_url( '/account-settings' ) ) . '">Settings</a>';
	$menu .= '<a href="' . esc_url( wp_logout_url( home_url( '/login' ) ) ) . '">Logout</a>';
	$menu .= '</div>';

	return $menu . $content;
}
add_filter( 'the_content', 'ulnec_prepend_logged_in_menu', 5 );

/**
 * Styles for logged-in UL/NEC quick menu.
 */
function ulnec_quick_menu_styles() {
	if ( is_admin() || ! is_user_logged_in() ) {
		return;
	}

	$ulnec_pages = array(
		'dashboard',
		'download',
		'bug-report',
		'feature-request',
		'billing',
		'account-settings',
		'founders-progress',
	);

	if ( ! is_page( $ulnec_pages ) ) {
		return;
	}

	$css = '
		.ulnec-quick-menu {
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
			padding: 14px 18px;
			margin: 16px 0;
			background: #ffffff;
			border: 1px solid #e2e8f0;
			border-radius: 10px;
			box-shadow: 0 2px 8px rgba(0,0,0,0.06);
		}

		.ulnec-quick-menu a {
			display: inline-block;
			padding: 8px 12px;
			border-radius: 8px;
			text-decoration: none;
			font-weight: 600;
			font-size: 14px;
			color: #1e3a8a;
			background: #eef2ff;
		}

		.ulnec-quick-menu a:hover {
			background: #dbeafe;
		}
	';

	wp_register_style( 'ulnec-quick-menu-inline', false, array(), NEXUS_VERSION );
	wp_enqueue_style( 'ulnec-quick-menu-inline' );
	wp_add_inline_style( 'ulnec-quick-menu-inline', $css );
}
add_action( 'wp_enqueue_scripts', 'ulnec_quick_menu_styles', 99 );

/**
 * Load Nexus Pro if available
 * PRO features load automatically when a valid license is detected
 */
if ( file_exists( NEXUS_DIR . '/pro/class-nexus-pro.php' ) ) {
	// Only load if no critical errors
	try {
		require_once NEXUS_DIR . '/pro/class-nexus-pro.php';
		// Initialize PRO features
		Nexus_Pro::instance();
	} catch ( Exception $e ) {
		// Log error but don't crash site
		error_log( 'Nexus PRO Load Error: ' . $e->getMessage() );
	}
}
