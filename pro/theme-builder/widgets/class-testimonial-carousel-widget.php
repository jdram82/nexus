<?php
/**
 * Testimonial Carousel Widget
 *
 * Display customer testimonials in a carousel
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Testimonial Carousel Widget Class
 */
class Nexus_Testimonial_Carousel_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'testimonial-carousel';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Testimonial Carousel', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-format-quote';
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
				'label' => __( 'Testimonials', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'testimonials',
			array(
				'label'   => __( 'Testimonials', 'nexus-pro' ),
				'type'    => 'repeater',
				'default' => array(
					array(
						'content'  => __( 'This is an amazing product! It exceeded all my expectations.', 'nexus-pro' ),
						'name'     => __( 'John Doe', 'nexus-pro' ),
						'title'    => __( 'CEO, Company Inc.', 'nexus-pro' ),
						'image'    => '',
						'rating'   => 5,
					),
					array(
						'content'  => __( 'Excellent service and support. Highly recommended!', 'nexus-pro' ),
						'name'     => __( 'Jane Smith', 'nexus-pro' ),
						'title'    => __( 'Marketing Manager', 'nexus-pro' ),
						'image'    => '',
						'rating'   => 5,
					),
					array(
						'content'  => __( 'Great quality and value for money. Will definitely buy again.', 'nexus-pro' ),
						'name'     => __( 'Mike Johnson', 'nexus-pro' ),
						'title'    => __( 'Freelance Designer', 'nexus-pro' ),
						'image'    => '',
						'rating'   => 4,
					),
				),
				'fields'  => array(
					array(
						'name'        => 'content',
						'label'       => __( 'Content', 'nexus-pro' ),
						'type'        => 'textarea',
						'default'     => __( 'Testimonial content goes here', 'nexus-pro' ),
						'placeholder' => __( 'Enter testimonial', 'nexus-pro' ),
					),
					array(
						'name'        => 'name',
						'label'       => __( 'Name', 'nexus-pro' ),
						'type'        => 'text',
						'default'     => __( 'Customer Name', 'nexus-pro' ),
						'placeholder' => __( 'Enter name', 'nexus-pro' ),
					),
					array(
						'name'        => 'title',
						'label'       => __( 'Title/Position', 'nexus-pro' ),
						'type'        => 'text',
						'default'     => __( 'Position, Company', 'nexus-pro' ),
						'placeholder' => __( 'Enter title', 'nexus-pro' ),
					),
					array(
						'name'  => 'image',
						'label' => __( 'Image', 'nexus-pro' ),
						'type'  => 'media',
					),
					array(
						'name'    => 'rating',
						'label'   => __( 'Rating', 'nexus-pro' ),
						'type'    => 'select',
						'options' => array(
							'0' => __( 'No Rating', 'nexus-pro' ),
							'1' => '1 Star',
							'2' => '2 Stars',
							'3' => '3 Stars',
							'4' => '4 Stars',
							'5' => '5 Stars',
						),
						'default' => '5',
					),
				),
			)
		);

		$this->end_controls_section();

		// Carousel settings
		$this->start_controls_section(
			'carousel_section',
			array(
				'label' => __( 'Carousel Settings', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'slides_to_show',
			array(
				'label'   => __( 'Slides to Show', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
				'default' => '1',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'   => __( 'Auto Play', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'   => __( 'Autoplay Speed (ms)', 'nexus-pro' ),
				'type'    => 'number',
				'default' => 5000,
				'min'     => 1000,
				'max'     => 10000,
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
			'show_dots',
			array(
				'label'   => __( 'Show Dots', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
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

		// Style controls
		$this->start_controls_section(
			'style_section',
			array(
				'label' => __( 'Style', 'nexus-pro' ),
				'tab'   => 'style',
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'classic' => __( 'Classic', 'nexus-pro' ),
					'bubble'  => __( 'Bubble', 'nexus-pro' ),
					'card'    => __( 'Card', 'nexus-pro' ),
				),
				'default' => 'classic',
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
			'image_size',
			array(
				'label'   => __( 'Image Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 80,
				'min'     => 40,
				'max'     => 150,
			)
		);

		$this->add_control(
			'content_color',
			array(
				'label'   => __( 'Content Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#1D2327',
			)
		);

		$this->add_control(
			'content_size',
			array(
				'label'   => __( 'Content Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 16,
				'min'     => 12,
				'max'     => 24,
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'   => __( 'Name Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#1D2327',
			)
		);

		$this->add_control(
			'name_size',
			array(
				'label'   => __( 'Name Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 18,
				'min'     => 14,
				'max'     => 30,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'   => __( 'Title Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#646970',
			)
		);

		$this->add_control(
			'title_size',
			array(
				'label'   => __( 'Title Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 14,
				'min'     => 12,
				'max'     => 20,
			)
		);

		$this->add_control(
			'star_color',
			array(
				'label'   => __( 'Star Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#FFB900',
			)
		);

		$this->add_control(
			'background_color',
			array(
				'label'   => __( 'Background Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#F0F0F1',
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'   => __( 'Border Radius (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 8,
				'min'     => 0,
				'max'     => 50,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings    = $this->get_settings();
		$carousel_id = 'testimonial-carousel-' . uniqid();

		if ( empty( $settings['testimonials'] ) ) {
			echo '<div class="nexus-testimonial-carousel-placeholder">' . esc_html__( 'Add testimonials', 'nexus-pro' ) . '</div>';
			return;
		}

		$carousel_data = array(
			'slidesToShow'  => intval( $settings['slides_to_show'] ),
			'autoplay'      => (bool) $settings['autoplay'],
			'autoplaySpeed' => intval( $settings['autoplay_speed'] ),
			'showArrows'    => (bool) $settings['show_arrows'],
			'showDots'      => (bool) $settings['show_dots'],
			'infiniteLoop'  => (bool) $settings['infinite_loop'],
			'pauseOnHover'  => (bool) $settings['pause_on_hover'],
		);

		?>
		<div class="nexus-testimonial-carousel layout-<?php echo esc_attr( $settings['layout'] ); ?>" 
			 id="<?php echo esc_attr( $carousel_id ); ?>"
			 data-carousel='<?php echo esc_attr( wp_json_encode( $carousel_data ) ); ?>'>
			
			<div class="testimonial-slides">
				<?php foreach ( $settings['testimonials'] as $index => $testimonial ) : ?>
					<div class="testimonial-slide <?php echo 0 === $index ? 'active' : ''; ?>">
						<div class="testimonial-item" 
							 style="background: <?php echo esc_attr( $settings['background_color'] ); ?>;
									border-radius: <?php echo esc_attr( $settings['border_radius'] ); ?>px;
									text-align: <?php echo esc_attr( $settings['alignment'] ); ?>;">
							
							<?php if ( ! empty( $testimonial['rating'] ) && $testimonial['rating'] > 0 ) : ?>
								<div class="testimonial-rating">
									<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
										<span class="star <?php echo $i <= $testimonial['rating'] ? 'filled' : ''; ?>" 
											  style="color: <?php echo esc_attr( $settings['star_color'] ); ?>;">
											★
										</span>
									<?php endfor; ?>
								</div>
							<?php endif; ?>

							<div class="testimonial-content" 
								 style="color: <?php echo esc_attr( $settings['content_color'] ); ?>;
										font-size: <?php echo esc_attr( $settings['content_size'] ); ?>px;">
								<span class="quote-mark">"</span>
								<?php echo esc_html( $testimonial['content'] ); ?>
								<span class="quote-mark">"</span>
							</div>

							<div class="testimonial-author">
								<?php if ( ! empty( $testimonial['image'] ) ) : ?>
									<div class="author-image">
										<img src="<?php echo esc_url( $testimonial['image'] ); ?>" 
											 alt="<?php echo esc_attr( $testimonial['name'] ); ?>"
											 style="width: <?php echo esc_attr( $settings['image_size'] ); ?>px; 
													height: <?php echo esc_attr( $settings['image_size'] ); ?>px;">
									</div>
								<?php endif; ?>

								<div class="author-details">
									<?php if ( ! empty( $testimonial['name'] ) ) : ?>
										<div class="author-name" 
											 style="color: <?php echo esc_attr( $settings['name_color'] ); ?>;
													font-size: <?php echo esc_attr( $settings['name_size'] ); ?>px;">
											<?php echo esc_html( $testimonial['name'] ); ?>
										</div>
									<?php endif; ?>

									<?php if ( ! empty( $testimonial['title'] ) ) : ?>
										<div class="author-title" 
											 style="color: <?php echo esc_attr( $settings['title_color'] ); ?>;
													font-size: <?php echo esc_attr( $settings['title_size'] ); ?>px;">
											<?php echo esc_html( $testimonial['title'] ); ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( $settings['show_arrows'] ) : ?>
				<button class="carousel-arrow prev">
					<span class="dashicons dashicons-arrow-left-alt2"></span>
				</button>
				<button class="carousel-arrow next">
					<span class="dashicons dashicons-arrow-right-alt2"></span>
				</button>
			<?php endif; ?>

			<?php if ( $settings['show_dots'] ) : ?>
				<div class="carousel-dots">
					<?php foreach ( $settings['testimonials'] as $index => $testimonial ) : ?>
						<button class="dot <?php echo 0 === $index ? 'active' : ''; ?>" 
								data-slide="<?php echo esc_attr( $index ); ?>"></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
