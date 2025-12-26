<?php
/**
 * Builder Elements
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Builder Elements Class
 */
class Nexus_Builder_Elements {

	/**
	 * Instance
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
		add_action( 'wp_ajax_nexus_save_builder_layout', array( $this, 'save_layout' ) );
		add_action( 'wp_ajax_nexus_get_builder_elements', array( $this, 'get_elements' ) );
	}

	/**
	 * Save Layout
	 */
	public function save_layout() {
		check_ajax_referer( 'nexus-builder-nonce', 'nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( __( 'Permission denied', 'nexus' ) );
		}

		$layout = isset( $_POST['layout'] ) ? json_decode( stripslashes( $_POST['layout'] ), true ) : array();
		$type   = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : 'header';

		update_option( 'nexus_' . $type . '_builder_layout', $layout );

		wp_send_json_success( __( 'Layout saved', 'nexus' ) );
	}

	/**
	 * Get Elements
	 */
	public function get_elements() {
		check_ajax_referer( 'nexus-builder-nonce', 'nonce' );

		$elements = $this->get_available_elements();

		wp_send_json_success( $elements );
	}

	/**
	 * Get Available Elements
	 */
	public function get_available_elements() {
		return array(
			'logo'       => array(
				'label'    => __( 'Logo', 'nexus' ),
				'icon'     => 'dashicons-format-image',
				'settings' => array(
					'width'  => array(
						'type'    => 'number',
						'label'   => __( 'Width', 'nexus' ),
						'default' => 150,
					),
					'height' => array(
						'type'    => 'number',
						'label'   => __( 'Height', 'nexus' ),
						'default' => 50,
					),
				),
			),
			'menu'       => array(
				'label'    => __( 'Menu', 'nexus' ),
				'icon'     => 'dashicons-menu',
				'settings' => array(
					'menu_id' => array(
						'type'    => 'select',
						'label'   => __( 'Select Menu', 'nexus' ),
						'choices' => $this->get_menus(),
					),
					'layout'  => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'nexus' ),
						'choices' => array(
							'horizontal' => __( 'Horizontal', 'nexus' ),
							'vertical'   => __( 'Vertical', 'nexus' ),
						),
					),
				),
			),
			'search'     => array(
				'label'    => __( 'Search', 'nexus' ),
				'icon'     => 'dashicons-search',
				'settings' => array(
					'style' => array(
						'type'    => 'select',
						'label'   => __( 'Style', 'nexus' ),
						'choices' => array(
							'icon'   => __( 'Icon Only', 'nexus' ),
							'form'   => __( 'Search Form', 'nexus' ),
							'popup'  => __( 'Popup', 'nexus' ),
						),
					),
				),
			),
			'cart'       => array(
				'label'    => __( 'Cart', 'nexus' ),
				'icon'     => 'dashicons-cart',
				'settings' => array(
					'show_count' => array(
						'type'    => 'checkbox',
						'label'   => __( 'Show Count', 'nexus' ),
						'default' => true,
					),
				),
			),
			'button'     => array(
				'label'    => __( 'Button', 'nexus' ),
				'icon'     => 'dashicons-button',
				'settings' => array(
					'text' => array(
						'type'    => 'text',
						'label'   => __( 'Button Text', 'nexus' ),
						'default' => __( 'Get Started', 'nexus' ),
					),
					'url'  => array(
						'type'    => 'url',
						'label'   => __( 'Button URL', 'nexus' ),
						'default' => '#',
					),
					'style' => array(
						'type'    => 'select',
						'label'   => __( 'Style', 'nexus' ),
						'choices' => array(
							'primary'   => __( 'Primary', 'nexus' ),
							'secondary' => __( 'Secondary', 'nexus' ),
							'outline'   => __( 'Outline', 'nexus' ),
						),
					),
				),
			),
			'social'     => array(
				'label'    => __( 'Social Icons', 'nexus' ),
				'icon'     => 'dashicons-share',
				'settings' => array(
					'links' => array(
						'type'  => 'repeater',
						'label' => __( 'Social Links', 'nexus' ),
						'fields' => array(
							'network' => array(
								'type'    => 'select',
								'label'   => __( 'Network', 'nexus' ),
								'choices' => $this->get_social_networks(),
							),
							'url'     => array(
								'type'  => 'url',
								'label' => __( 'URL', 'nexus' ),
							),
						),
					),
				),
			),
			'text'       => array(
				'label'    => __( 'Text/HTML', 'nexus' ),
				'icon'     => 'dashicons-text',
				'settings' => array(
					'content' => array(
						'type'    => 'textarea',
						'label'   => __( 'Content', 'nexus' ),
						'default' => '',
					),
				),
			),
			'widget'     => array(
				'label'    => __( 'Widget Area', 'nexus' ),
				'icon'     => 'dashicons-admin-generic',
				'settings' => array(
					'widget_area' => array(
						'type'    => 'select',
						'label'   => __( 'Widget Area', 'nexus' ),
						'choices' => $this->get_widget_areas(),
					),
				),
			),
		);
	}

	/**
	 * Get Menus
	 */
	private function get_menus() {
		$menus   = wp_get_nav_menus();
		$choices = array();

		foreach ( $menus as $menu ) {
			$choices[ $menu->term_id ] = $menu->name;
		}

		return $choices;
	}

	/**
	 * Get Social Networks
	 */
	private function get_social_networks() {
		return array(
			'facebook'  => __( 'Facebook', 'nexus' ),
			'twitter'   => __( 'Twitter', 'nexus' ),
			'linkedin'  => __( 'LinkedIn', 'nexus' ),
			'instagram' => __( 'Instagram', 'nexus' ),
			'youtube'   => __( 'YouTube', 'nexus' ),
			'github'    => __( 'GitHub', 'nexus' ),
		);
	}

	/**
	 * Get Widget Areas
	 */
	private function get_widget_areas() {
		global $wp_registered_sidebars;
		$choices = array();

		foreach ( $wp_registered_sidebars as $sidebar ) {
			$choices[ $sidebar['id'] ] = $sidebar['name'];
		}

		return $choices;
	}
}
