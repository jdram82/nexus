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
define( 'NEXUS_VERSION', '1.5.0' );
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
 * Enqueue Scripts and Styles
 */
require_once NEXUS_DIR . '/inc/class-nexus-enqueue.php';

/**
 * Phase 1 Features - Plugin Harmony (Pro Tier)
 */
require_once NEXUS_DIR . '/inc/class-nexus-plugin-harmony.php';

/**
 * Phase 1 Features - REST API (Pro Tier)
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
function nexus_init() {
	Nexus_Theme_Setup::instance();
	Nexus_Customizer::instance();
	Nexus_Products::instance();
	Nexus_Projects::instance();
	Nexus_Downloads::instance();
	Nexus_Enqueue::instance();
	
	if ( class_exists( 'WooCommerce' ) ) {
		Nexus_WooCommerce::instance();
	}
}
add_action( 'after_setup_theme', 'nexus_init' );

/**
 * Load Nexus Pro if available
 */
if ( file_exists( NEXUS_DIR . '/pro/class-nexus-pro.php' ) ) {
require_once NEXUS_DIR . '/pro/class-nexus-pro.php';
}
