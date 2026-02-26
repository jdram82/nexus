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
define( 'NEXUS_VERSION', '3.2.5' );
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
		wp_redirect( home_url( '/dashboard' ) );
		exit;
	}
}
add_action( 'template_redirect', 'ulnec_redirect_logged_in_users' );

/**
 * Handle post-login redirect
 */
function ulnec_login_redirect( $redirect_to, $request, $user ) {
	// Check if there's a redirect parameter
	if ( isset( $_GET['redirect'] ) && ! empty( $_GET['redirect'] ) ) {
		$redirect = esc_url_raw( $_GET['redirect'] );
		// Validate it's a local URL
		if ( strpos( $redirect, home_url() ) === 0 ) {
			return $redirect;
		}
	}
	
	// Default redirect to dashboard
	return home_url( '/dashboard' );
}
add_filter( 'login_redirect', 'ulnec_login_redirect', 10, 3 );

/**
 * Add body class for UL/NEC pages
 */
function ulnec_body_classes( $classes ) {
	$ulnec_pages = array( 'login', 'register', 'dashboard', 'billing', 'bug-report', 'feature-request', 'account-settings' );
	
	if ( is_page( $ulnec_pages ) ) {
		$classes[] = 'ulnec-page';
	}
	
	return $classes;
}
add_filter( 'body_class', 'ulnec_body_classes' );

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
