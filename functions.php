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
define( 'NEXUS_VERSION', '3.1.6' );
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
 * Load Nexus Pro if available
 * Note: Pro features are disabled by default. Enable in WordPress Admin after theme activation.
 */
if ( file_exists( NEXUS_DIR . '/pro/class-nexus-pro.php' ) && get_option( 'nexus_enable_pro', false ) ) {
	require_once NEXUS_DIR . '/pro/class-nexus-pro.php';
}
