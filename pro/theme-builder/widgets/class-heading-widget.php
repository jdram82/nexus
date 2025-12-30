<?php
/**
 * Heading Widget
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Heading Widget Class
 */
class Nexus_Heading_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'heading';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Heading', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-editor-textcolor';
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
			'heading_text',
			array(
				'label'   => __( 'Heading Text', 'nexus-pro' ),
				'type'    => 'text',
				'default' => __( 'Your Heading', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'heading_tag',
			array(
				'label'   => __( 'HTML Tag', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				),
				'default' => 'h2',
			)
		);

		$this->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'nexus-pro' ),
				'type'        => 'url',
				'placeholder' => __( 'https://your-link.com', 'nexus-pro' ),
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
			'text_align',
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
			'text_color',
			array(
				'label'   => __( 'Text Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#333333',
			)
		);

		$this->add_control(
			'font_size',
			array(
				'label'   => __( 'Font Size', 'nexus-pro' ),
				'type'    => 'slider',
				'min'     => 10,
				'max'     => 100,
				'default' => 32,
				'unit'    => 'px',
			)
		);

		$this->add_control(
			'font_weight',
			array(
				'label'   => __( 'Font Weight', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'300' => __( 'Light', 'nexus-pro' ),
					'400' => __( 'Normal', 'nexus-pro' ),
					'600' => __( 'Semi Bold', 'nexus-pro' ),
					'700' => __( 'Bold', 'nexus-pro' ),
					'900' => __( 'Black', 'nexus-pro' ),
				),
				'default' => '700',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();

		$tag   = $settings['heading_tag'];
		$text  = $settings['heading_text'];
		$link  = $settings['link'];
		$align = $settings['text_align'];
		$color = $settings['text_color'];
		$size  = $settings['font_size'];
		$weight = $settings['font_weight'];

		$style = sprintf(
			'text-align: %s; color: %s; font-size: %spx; font-weight: %s;',
			esc_attr( $align ),
			esc_attr( $color ),
			esc_attr( $size ),
			esc_attr( $weight )
		);

		$heading = sprintf( '<%1$s class="nexus-heading" style="%2$s">%3$s</%1$s>',
			tag_escape( $tag ),
			$style,
			esc_html( $text )
		);

		if ( ! empty( $link ) ) {
			printf( '<a href="%s">%s</a>', esc_url( $link ), $heading );
		} else {
			echo $heading;
		}
	}
}
