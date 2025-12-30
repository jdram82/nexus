<?php
/**
 * Button Widget
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Button Widget Class
 */
class Nexus_Button_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'button';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Button', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-button';
	}

	/**
	 * Get widget categories
	 */
	public function get_categories() {
		return array( 'basic' );
	}

	/**
	 * Register widget controls
	 */
	protected function register_controls() {
		// Content controls
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Text', 'nexus-pro' ),
				'type'    => 'text',
				'default' => __( 'Click Here', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'link',
			array(
				'label'   => __( 'Link', 'nexus-pro' ),
				'type'    => 'url',
				'default' => '#',
			)
		);

		$this->add_control(
			'link_target',
			array(
				'label'   => __( 'Open in new tab', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => false,
			)
		);

		$this->add_control(
			'icon',
			array(
				'label' => __( 'Icon', 'nexus-pro' ),
				'type'  => 'icon',
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'   => __( 'Icon Position', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'left'  => __( 'Left', 'nexus-pro' ),
					'right' => __( 'Right', 'nexus-pro' ),
				),
				'default' => 'left',
			)
		);

		$this->end_controls_section();

		// Style controls
		$this->start_controls_section(
			'style_section',
			array(
				'label' => __( 'Style', 'nexus-pro' ),
				'tab'   => 'style',
			)
		);

		$this->add_control(
			'button_style',
			array(
				'label'   => __( 'Style', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'primary'   => __( 'Primary', 'nexus-pro' ),
					'secondary' => __( 'Secondary', 'nexus-pro' ),
					'outline'   => __( 'Outline', 'nexus-pro' ),
					'ghost'     => __( 'Ghost', 'nexus-pro' ),
				),
				'default' => 'primary',
			)
		);

		$this->add_control(
			'button_size',
			array(
				'label'   => __( 'Size', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'small'  => __( 'Small', 'nexus-pro' ),
					'medium' => __( 'Medium', 'nexus-pro' ),
					'large'  => __( 'Large', 'nexus-pro' ),
				),
				'default' => 'medium',
			)
		);

		$this->add_control(
			'alignment',
			array(
				'label'   => __( 'Alignment', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'left'   => __( 'Left', 'nexus-pro' ),
					'center' => __( 'Center', 'nexus-pro' ),
					'right'  => __( 'Right', 'nexus-pro' ),
				),
				'default' => 'left',
			)
		);

		$this->add_control(
			'background_color',
			array(
				'label'   => __( 'Background Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#2271b1',
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'   => __( 'Text Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#ffffff',
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'   => __( 'Border Radius', 'nexus-pro' ),
				'type'    => 'slider',
				'min'     => 0,
				'max'     => 50,
				'default' => 4,
				'unit'    => 'px',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();

		$text          = $settings['button_text'];
		$link          = $settings['link'];
		$target        = $settings['link_target'] ? '_blank' : '_self';
		$style_class   = 'button-' . $settings['button_style'];
		$size_class    = 'button-' . $settings['button_size'];
		$bg_color      = $settings['background_color'];
		$text_color    = $settings['text_color'];
		$border_radius = $settings['border_radius'];

		$wrapper_style = 'text-align: ' . esc_attr( $settings['alignment'] ) . ';';
		$button_style  = sprintf(
			'background-color: %s; color: %s; border-radius: %spx;',
			esc_attr( $bg_color ),
			esc_attr( $text_color ),
			esc_attr( $border_radius )
		);

		printf(
			'<div class="nexus-button-wrapper" style="%s"><a href="%s" target="%s" class="nexus-button %s %s" style="%s">%s</a></div>',
			$wrapper_style,
			esc_url( $link ),
			esc_attr( $target ),
			esc_attr( $style_class ),
			esc_attr( $size_class ),
			$button_style,
			esc_html( $text )
		);
	}
}
