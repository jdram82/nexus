<?php
/**
 * Enqueue Scripts and Styles
 *
 * @package Nexus
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Enqueue Class
 */
class Nexus_Enqueue {

	/**
	 * Instance
	 *
	 * @var Nexus_Enqueue
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue Styles
	 */
	public function enqueue_styles() {
		// Main stylesheet
		wp_enqueue_style( 'nexus-style', get_stylesheet_uri(), array(), NEXUS_VERSION );

		// Compiled CSS
		if ( file_exists( NEXUS_DIR . '/assets/dist/css/main.css' ) ) {
			wp_enqueue_style(
				'nexus-main',
				NEXUS_URI . '/assets/dist/css/main.css',
				array(),
				NEXUS_VERSION
			);
		}

		// Add inline style for CSS custom properties
		$this->add_custom_properties();
	}

	/**
	 * Enqueue Scripts
	 */
	public function enqueue_scripts() {
		// Main JavaScript
		if ( file_exists( NEXUS_DIR . '/assets/dist/js/main.js' ) ) {
			wp_enqueue_script(
				'nexus-main',
				NEXUS_URI . '/assets/dist/js/main.js',
				array(),
				NEXUS_VERSION,
				true
			);
		}

		// Navigation script
		if ( file_exists( NEXUS_DIR . '/assets/dist/js/navigation.js' ) ) {
			wp_enqueue_script(
				'nexus-navigation',
				NEXUS_URI . '/assets/dist/js/navigation.js',
				array(),
				NEXUS_VERSION,
				true
			);
		}

		// Comment reply
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Localize script
		wp_localize_script(
			'nexus-main',
			'nexusVars',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'nexus-nonce' ),
			)
		);
	}

	/**
	 * Add CSS Custom Properties
	 */
	private function add_custom_properties() {
		// Get customizer settings
		$primary_color   = get_theme_mod( 'nexus_primary_color', '#0066cc' );
		$secondary_color = get_theme_mod( 'nexus_secondary_color', '#333333' );
		$accent_color    = get_theme_mod( 'nexus_accent_color', '#ff6600' );
		
		$heading_font = get_theme_mod( 'nexus_heading_font', 'Roboto' );
		$body_font    = get_theme_mod( 'nexus_body_font', 'Open Sans' );

		// Build CSS
		$css = ':root {';
		$css .= '--nexus-primary: ' . esc_attr( $primary_color ) . ';';
		$css .= '--nexus-secondary: ' . esc_attr( $secondary_color ) . ';';
		$css .= '--nexus-accent: ' . esc_attr( $accent_color ) . ';';
		$css .= '--nexus-heading-font: ' . esc_attr( $heading_font ) . ', sans-serif;';
		$css .= '--nexus-body-font: ' . esc_attr( $body_font ) . ', sans-serif;';
		$css .= '}';

		wp_add_inline_style( 'nexus-style', $css );
	}
}
