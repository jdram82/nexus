<?php
/**
 * Customizer Class
 *
 * @package Nexus
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Customizer
 */
class Nexus_Customizer {

	/**
	 * Instance
	 *
	 * @var Nexus_Customizer
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
		add_action( 'customize_register', array( $this, 'register' ) );
	}

	/**
	 * Register Customizer Settings
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer object.
	 */
	public function register( $wp_customize ) {
		// Add Color Settings Panel
		$this->add_color_settings( $wp_customize );

		// Add Typography Settings Panel
		$this->add_typography_settings( $wp_customize );

		// Add Layout Settings Panel
		$this->add_layout_settings( $wp_customize );
	}

	/**
	 * Add Color Settings
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer object.
	 */
	private function add_color_settings( $wp_customize ) {
		// Colors Section
		$wp_customize->add_section(
			'nexus_colors',
			array(
				'title'    => esc_html__( 'Theme Colors', 'nexus' ),
				'priority' => 40,
			)
		);

		// Primary Color
		$wp_customize->add_setting(
			'nexus_primary_color',
			array(
				'default'           => '#0066cc',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'nexus_primary_color',
				array(
					'label'   => esc_html__( 'Primary Color', 'nexus' ),
					'section' => 'nexus_colors',
				)
			)
		);

		// Secondary Color
		$wp_customize->add_setting(
			'nexus_secondary_color',
			array(
				'default'           => '#333333',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'nexus_secondary_color',
				array(
					'label'   => esc_html__( 'Secondary Color', 'nexus' ),
					'section' => 'nexus_colors',
				)
			)
		);

		// Accent Color
		$wp_customize->add_setting(
			'nexus_accent_color',
			array(
				'default'           => '#ff6600',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'nexus_accent_color',
				array(
					'label'   => esc_html__( 'Accent Color', 'nexus' ),
					'section' => 'nexus_colors',
				)
			)
		);

		// Additional color palette (9 colors total)
		$additional_colors = array(
			'success' => array( 'label' => 'Success Color', 'default' => '#28a745' ),
			'warning' => array( 'label' => 'Warning Color', 'default' => '#ffc107' ),
			'danger'  => array( 'label' => 'Danger Color', 'default' => '#dc3545' ),
			'info'    => array( 'label' => 'Info Color', 'default' => '#17a2b8' ),
			'light'   => array( 'label' => 'Light Color', 'default' => '#f8f9fa' ),
			'dark'    => array( 'label' => 'Dark Color', 'default' => '#343a40' ),
		);

		foreach ( $additional_colors as $key => $color ) {
			$wp_customize->add_setting(
				'nexus_' . $key . '_color',
				array(
					'default'           => $color['default'],
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'nexus_' . $key . '_color',
					array(
						'label'   => esc_html__( $color['label'], 'nexus' ),
						'section' => 'nexus_colors',
					)
				)
			);
		}
	}

	/**
	 * Add Typography Settings
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer object.
	 */
	private function add_typography_settings( $wp_customize ) {
		// Typography Section
		$wp_customize->add_section(
			'nexus_typography',
			array(
				'title'    => esc_html__( 'Typography', 'nexus' ),
				'priority' => 50,
			)
		);

		// Heading Font
		$wp_customize->add_setting(
			'nexus_heading_font',
			array(
				'default'           => 'Roboto',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			'nexus_heading_font',
			array(
				'label'   => esc_html__( 'Heading Font', 'nexus' ),
				'section' => 'nexus_typography',
				'type'    => 'select',
				'choices' => $this->get_font_choices(),
			)
		);

		// Body Font
		$wp_customize->add_setting(
			'nexus_body_font',
			array(
				'default'           => 'Open Sans',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			'nexus_body_font',
			array(
				'label'   => esc_html__( 'Body Font', 'nexus' ),
				'section' => 'nexus_typography',
				'type'    => 'select',
				'choices' => $this->get_font_choices(),
			)
		);

		// Font Size
		$wp_customize->add_setting(
			'nexus_body_font_size',
			array(
				'default'           => '16',
				'sanitize_callback' => 'absint',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			'nexus_body_font_size',
			array(
				'label'       => esc_html__( 'Body Font Size (px)', 'nexus' ),
				'section'     => 'nexus_typography',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 12,
					'max'  => 24,
					'step' => 1,
				),
			)
		);
	}

	/**
	 * Add Layout Settings
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer object.
	 */
	private function add_layout_settings( $wp_customize ) {
		// Layout Section
		$wp_customize->add_section(
			'nexus_layout',
			array(
				'title'    => esc_html__( 'Layout Settings', 'nexus' ),
				'priority' => 60,
			)
		);

		// Container Width
		$wp_customize->add_setting(
			'nexus_container_width',
			array(
				'default'           => '1200',
				'sanitize_callback' => 'absint',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			'nexus_container_width',
			array(
				'label'       => esc_html__( 'Container Width (px)', 'nexus' ),
				'section'     => 'nexus_layout',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 960,
					'max'  => 1920,
					'step' => 10,
				),
			)
		);

		// Sidebar Position
		$wp_customize->add_setting(
			'nexus_sidebar_position',
			array(
				'default'           => 'right',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'nexus_sidebar_position',
			array(
				'label'   => esc_html__( 'Sidebar Position', 'nexus' ),
				'section' => 'nexus_layout',
				'type'    => 'select',
				'choices' => array(
					'left'  => esc_html__( 'Left', 'nexus' ),
					'right' => esc_html__( 'Right', 'nexus' ),
					'none'  => esc_html__( 'No Sidebar', 'nexus' ),
				),
			)
		);
	}

	/**
	 * Get Font Choices
	 *
	 * @return array
	 */
	private function get_font_choices() {
		return array(
			'Roboto'        => 'Roboto',
			'Open Sans'     => 'Open Sans',
			'Lato'          => 'Lato',
			'Montserrat'    => 'Montserrat',
			'Poppins'       => 'Poppins',
			'Inter'         => 'Inter',
			'Raleway'       => 'Raleway',
			'Ubuntu'        => 'Ubuntu',
			'Nunito'        => 'Nunito',
			'Source Sans Pro' => 'Source Sans Pro',
		);
	}
}
