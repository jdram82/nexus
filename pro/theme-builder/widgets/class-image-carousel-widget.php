<?php
/**
 * Image Carousel Widget
 *
 * Display image slider/carousel with navigation
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Image Carousel Widget Class
 */
class Nexus_Image_Carousel_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'image-carousel';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Image Carousel', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-images-alt2';
	}

	/**
	 * Get widget categories
	 */
	public function get_categories() {
		return array( 'content' );
	}

	/**
	 * Register widget controls
	 */
	protected function register_controls() {
		// Content controls
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Carousel', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'images',
			array(
				'label'    => __( 'Add Images', 'nexus-pro' ),
				'type'     => 'media',
				'multiple' => true,
				'default'  => array(),
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
				'default' => 'large',
			)
		);

		$this->add_control(
			'show_captions',
			array(
				'label'   => __( 'Show Captions', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => false,
			)
		);

		$this->add_control(
			'link_to',
			array(
				'label'   => __( 'Link To', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'none'      => __( 'None', 'nexus-pro' ),
					'file'      => __( 'Media File', 'nexus-pro' ),
					'lightbox'  => __( 'Lightbox', 'nexus-pro' ),
				),
				'default' => 'lightbox',
			)
		);

		$this->end_controls_section();

		// Carousel settings
		$this->start_controls_section(
			'carousel_settings',
			array(
				'label' => __( 'Carousel Settings', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'slides_to_show',
			array(
				'label'   => __( 'Slides to Show', 'nexus-pro' ),
				'type'    => 'number',
				'default' => 1,
				'min'     => 1,
				'max'     => 6,
			)
		);

		$this->add_control(
			'slides_to_scroll',
			array(
				'label'   => __( 'Slides to Scroll', 'nexus-pro' ),
				'type'    => 'number',
				'default' => 1,
				'min'     => 1,
				'max'     => 6,
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'   => __( 'Autoplay', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'   => __( 'Autoplay Speed (ms)', 'nexus-pro' ),
				'type'    => 'number',
				'default' => 3000,
				'min'     => 1000,
				'max'     => 10000,
				'step'    => 500,
			)
		);

		$this->add_control(
			'animation_speed',
			array(
				'label'   => __( 'Animation Speed (ms)', 'nexus-pro' ),
				'type'    => 'number',
				'default' => 600,
				'min'     => 200,
				'max'     => 2000,
				'step'    => 100,
			)
		);

		$this->add_control(
			'infinite_loop',
			array(
				'label'   => __( 'Infinite Loop', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'   => __( 'Pause on Hover', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->end_controls_section();

		// Navigation settings
		$this->start_controls_section(
			'navigation_section',
			array(
				'label' => __( 'Navigation', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'show_arrows',
			array(
				'label'   => __( 'Show Arrows', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'arrow_position',
			array(
				'label'   => __( 'Arrow Position', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'inside'  => __( 'Inside', 'nexus-pro' ),
					'outside' => __( 'Outside', 'nexus-pro' ),
				),
				'default' => 'inside',
			)
		);

		$this->add_control(
			'show_dots',
			array(
				'label'   => __( 'Show Dots', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'dots_position',
			array(
				'label'   => __( 'Dots Position', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'inside'  => __( 'Inside', 'nexus-pro' ),
					'outside' => __( 'Outside', 'nexus-pro' ),
				),
				'default' => 'outside',
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
			'image_fit',
			array(
				'label'   => __( 'Image Fit', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'cover'   => __( 'Cover', 'nexus-pro' ),
					'contain' => __( 'Contain', 'nexus-pro' ),
					'fill'    => __( 'Fill', 'nexus-pro' ),
				),
				'default' => 'cover',
			)
		);

		$this->add_control(
			'image_height',
			array(
				'label'   => __( 'Image Height (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 400,
				'min'     => 200,
				'max'     => 800,
			)
		);

		$this->add_control(
			'gap',
			array(
				'label'   => __( 'Gap Between Slides', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 15,
				'min'     => 0,
				'max'     => 50,
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'   => __( 'Border Radius', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 4,
				'min'     => 0,
				'max'     => 50,
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'   => __( 'Arrow Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#FFFFFF',
			)
		);

		$this->add_control(
			'arrow_background',
			array(
				'label'   => __( 'Arrow Background', 'nexus-pro' ),
				'type'    => 'color',
				'default' => 'rgba(0, 0, 0, 0.5)',
			)
		);

		$this->add_control(
			'dot_color',
			array(
				'label'   => __( 'Dot Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#CCCCCC',
			)
		);

		$this->add_control(
			'dot_active_color',
			array(
				'label'   => __( 'Active Dot Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#2271B1',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();

		if ( empty( $settings['images'] ) ) {
			echo '<div class="nexus-carousel-placeholder">' . esc_html__( 'Please add images to the carousel', 'nexus-pro' ) . '</div>';
			return;
		}

		$carousel_id = 'carousel-' . uniqid();
		$carousel_data = array(
			'slidesToShow'   => intval( $settings['slides_to_show'] ),
			'slidesToScroll' => intval( $settings['slides_to_scroll'] ),
			'autoplay'       => (bool) $settings['autoplay'],
			'autoplaySpeed'  => intval( $settings['autoplay_speed'] ),
			'speed'          => intval( $settings['animation_speed'] ),
			'infinite'       => (bool) $settings['infinite_loop'],
			'pauseOnHover'   => (bool) $settings['pause_on_hover'],
			'arrows'         => (bool) $settings['show_arrows'],
			'dots'           => (bool) $settings['show_dots'],
		);

		?>
		<div class="nexus-image-carousel" 
			 id="<?php echo esc_attr( $carousel_id ); ?>"
			 data-carousel='<?php echo esc_attr( wp_json_encode( $carousel_data ) ); ?>'>
			
			<div class="carousel-container arrows-<?php echo esc_attr( $settings['arrow_position'] ); ?> dots-<?php echo esc_attr( $settings['dots_position'] ); ?>">
				
				<div class="carousel-track" style="--gap: <?php echo esc_attr( $settings['gap'] ); ?>px;">
					<?php foreach ( $settings['images'] as $image_id ) : ?>
						<?php
						$image_url = wp_get_attachment_image_url( $image_id, $settings['image_size'] );
						$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
						$caption = wp_get_attachment_caption( $image_id );
						?>
						
						<div class="carousel-slide">
							<div class="slide-inner" style="border-radius: <?php echo esc_attr( $settings['border_radius'] ); ?>px;">
								<?php if ( 'lightbox' === $settings['link_to'] || 'file' === $settings['link_to'] ) : ?>
									<a href="<?php echo esc_url( wp_get_attachment_url( $image_id ) ); ?>" 
									   <?php echo 'lightbox' === $settings['link_to'] ? 'data-lightbox="carousel"' : ''; ?>
									   target="_blank">
								<?php endif; ?>
								
								<img src="<?php echo esc_url( $image_url ); ?>" 
									 alt="<?php echo esc_attr( $image_alt ); ?>"
									 style="height: <?php echo esc_attr( $settings['image_height'] ); ?>px; 
											object-fit: <?php echo esc_attr( $settings['image_fit'] ); ?>;" />
								
								<?php if ( 'lightbox' === $settings['link_to'] || 'file' === $settings['link_to'] ) : ?>
									</a>
								<?php endif; ?>
								
								<?php if ( $settings['show_captions'] && $caption ) : ?>
									<div class="slide-caption">
										<?php echo esc_html( $caption ); ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<?php if ( $settings['show_arrows'] ) : ?>
					<button class="carousel-arrow carousel-prev" 
							style="color: <?php echo esc_attr( $settings['arrow_color'] ); ?>; 
								   background: <?php echo esc_attr( $settings['arrow_background'] ); ?>;"
							aria-label="<?php esc_attr_e( 'Previous', 'nexus-pro' ); ?>">
						<span class="dashicons dashicons-arrow-left-alt2"></span>
					</button>
					<button class="carousel-arrow carousel-next" 
							style="color: <?php echo esc_attr( $settings['arrow_color'] ); ?>; 
								   background: <?php echo esc_attr( $settings['arrow_background'] ); ?>;"
							aria-label="<?php esc_attr_e( 'Next', 'nexus-pro' ); ?>">
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</button>
				<?php endif; ?>

			</div>

			<?php if ( $settings['show_dots'] ) : ?>
				<div class="carousel-dots" 
					 style="--dot-color: <?php echo esc_attr( $settings['dot_color'] ); ?>; 
							--dot-active-color: <?php echo esc_attr( $settings['dot_active_color'] ); ?>;">
					<?php foreach ( $settings['images'] as $index => $image_id ) : ?>
						<button class="carousel-dot <?php echo 0 === $index ? 'active' : ''; ?>" 
								data-slide="<?php echo esc_attr( $index ); ?>"
								aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'nexus-pro' ), $index + 1 ) ); ?>">
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
