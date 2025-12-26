<?php
/**
 * Theme Setup Class
 *
 * @package Nexus
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Theme Setup
 */
class Nexus_Theme_Setup {

	/**
	 * Instance
	 *
	 * @var Nexus_Theme_Setup
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
		add_action( 'after_setup_theme', array( $this, 'setup' ) );
		add_action( 'after_setup_theme', array( $this, 'content_width' ), 0 );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}

	/**
	 * Theme Setup
	 */
	public function setup() {
		// Make theme available for translation
		load_theme_textdomain( 'nexus', NEXUS_DIR . '/languages' );

		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links' );

		// Let WordPress manage the document title
		add_theme_support( 'title-tag' );

		// Enable support for Post Thumbnails
		add_theme_support( 'post-thumbnails' );

		// Add custom image sizes
		add_image_size( 'nexus-featured', 1200, 600, true );
		add_image_size( 'nexus-thumbnail', 400, 300, true );
		add_image_size( 'nexus-product', 800, 800, true );

		// Register navigation menus
		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary Menu', 'nexus' ),
				'footer'  => esc_html__( 'Footer Menu', 'nexus' ),
			)
		);

		// Switch default core markup to output valid HTML5
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// Add theme support for selective refresh for widgets
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for custom logo
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 100,
				'width'       => 300,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);

		// Add support for custom background
		add_theme_support(
			'custom-background',
			array(
				'default-color' => 'ffffff',
			)
		);

		// Add theme support for custom header
		add_theme_support(
			'custom-header',
			array(
				'default-image'      => '',
				'width'              => 1920,
				'height'             => 200,
				'flex-height'        => true,
				'flex-width'         => true,
				'header-text'        => true,
				'default-text-color' => '000000',
			)
		);

		// Add support for editor styles
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/dist/css/editor-style.css' );

		// Add support for responsive embeds
		add_theme_support( 'responsive-embeds' );

		// Add support for align wide
		add_theme_support( 'align-wide' );
	}

	/**
	 * Set content width
	 */
	public function content_width() {
		$GLOBALS['content_width'] = apply_filters( 'nexus_content_width', 1200 );
	}

	/**
	 * Register widget areas
	 */
	public function widgets_init() {
		// Main Sidebar
		register_sidebar(
			array(
				'name'          => esc_html__( 'Sidebar', 'nexus' ),
				'id'            => 'sidebar-1',
				'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'nexus' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);

		// Footer Widgets
		for ( $i = 1; $i <= 4; $i++ ) {
			register_sidebar(
				array(
					'name'          => sprintf( esc_html__( 'Footer %d', 'nexus' ), $i ),
					'id'            => 'footer-' . $i,
					'description'   => sprintf( esc_html__( 'Add widgets here to appear in footer column %d.', 'nexus' ), $i ),
					'before_widget' => '<section id="%1$s" class="widget %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h3 class="widget-title">',
					'after_title'   => '</h3>',
				)
			);
		}
	}
}
