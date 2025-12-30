<?php
/**
 * Image Widget
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Image Widget Class
 */
class Nexus_Image_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'image';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Image', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-format-image';
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
			'image',
			array(
				'label'   => __( 'Choose Image', 'nexus-pro' ),
				'type'    => 'media',
				'default' => array(
					'id'  => '',
					'url' => '',
				),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'   => __( 'Image Size', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'thumbnail' => __( 'Thumbnail', 'nexus-pro' ),
					'medium'    => __( 'Medium', 'nexus-pro' ),
					'large'     => __( 'Large', 'nexus-pro' ),
					'full'      => __( 'Full Size', 'nexus-pro' ),
				),
				'default' => 'full',
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

		$this->add_control(
			'link_target',
			array(
				'label'   => __( 'Open in new tab', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => false,
			)
		);

		$this->add_control(
			'caption',
			array(
				'label'       => __( 'Caption', 'nexus-pro' ),
				'type'        => 'textarea',
				'placeholder' => __( 'Enter image caption', 'nexus-pro' ),
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
			'alignment',
			array(
				'label'   => __( 'Alignment', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'left'   => __( 'Left', 'nexus-pro' ),
					'center' => __( 'Center', 'nexus-pro' ),
					'right'  => __( 'Right', 'nexus-pro' ),
				),
				'default' => 'center',
			)
		);

		$this->add_control(
			'width',
			array(
				'label'   => __( 'Width', 'nexus-pro' ),
				'type'    => 'slider',
				'min'     => 0,
				'max'     => 100,
				'default' => 100,
				'unit'    => '%',
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'   => __( 'Border Radius', 'nexus-pro' ),
				'type'    => 'slider',
				'min'     => 0,
				'max'     => 50,
				'default' => 0,
				'unit'    => 'px',
			)
		);

		$this->add_control(
			'opacity',
			array(
				'label'   => __( 'Opacity', 'nexus-pro' ),
				'type'    => 'slider',
				'min'     => 0,
				'max'     => 100,
				'default' => 100,
				'unit'    => '%',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();

		$image_id      = $settings['image']['id'];
		$image_size    = $settings['image_size'];
		$link          = $settings['link'];
		$target        = $settings['link_target'] ? '_blank' : '_self';
		$caption       = $settings['caption'];
		$alignment     = $settings['alignment'];
		$width         = $settings['width'];
		$border_radius = $settings['border_radius'];
		$opacity       = $settings['opacity'] / 100;

		$wrapper_style = 'text-align: ' . esc_attr( $alignment ) . ';';
		$image_style   = sprintf(
			'width: %s%%; border-radius: %spx; opacity: %s;',
			esc_attr( $width ),
			esc_attr( $border_radius ),
			esc_attr( $opacity )
		);

		echo '<div class="nexus-image-wrapper" style="' . $wrapper_style . '">';

		if ( $image_id ) {
			$image_html = wp_get_attachment_image( $image_id, $image_size, false, array(
				'class' => 'nexus-image',
				'style' => $image_style,
			) );

			if ( ! empty( $link ) ) {
				printf(
					'<a href="%s" target="%s">%s</a>',
					esc_url( $link ),
					esc_attr( $target ),
					$image_html
				);
			} else {
				echo $image_html;
			}

			if ( ! empty( $caption ) ) {
				printf( '<p class="nexus-image-caption">%s</p>', esc_html( $caption ) );
			}
		} else {
			echo '<div class="nexus-image-placeholder">';
			echo '<span class="dashicons dashicons-format-image"></span>';
			echo '<p>' . esc_html__( 'Click to select an image', 'nexus-pro' ) . '</p>';
			echo '</div>';
		}

		echo '</div>';
	}
}
