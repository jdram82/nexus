<?php
/**
 * Media Carousel Widget
 *
 * Display a carousel of images and videos with thumbnails
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Media Carousel Widget Class
 */
class Nexus_Media_Carousel_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'media-carousel';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Media Carousel', 'nexus-pro' );
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
		return array( 'media' );
	}

	/**
	 * Register widget controls
	 */
	protected function register_controls() {
		// Content controls
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Media Items', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'   => __( 'Media', 'nexus-pro' ),
				'type'    => 'repeater',
				'default' => array(
					array(
						'type'        => 'image',
						'image'       => '',
						'video_url'   => '',
						'title'       => __( 'Image 1', 'nexus-pro' ),
						'description' => __( 'Image description', 'nexus-pro' ),
					),
					array(
						'type'        => 'image',
						'image'       => '',
						'video_url'   => '',
						'title'       => __( 'Image 2', 'nexus-pro' ),
						'description' => __( 'Image description', 'nexus-pro' ),
					),
					array(
						'type'        => 'video',
						'image'       => '',
						'video_url'   => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
						'title'       => __( 'Video 1', 'nexus-pro' ),
						'description' => __( 'Video description', 'nexus-pro' ),
					),
				),
				'fields'  => array(
					array(
						'name'    => 'type',
						'label'   => __( 'Type', 'nexus-pro' ),
						'type'    => 'select',
						'options' => array(
							'image' => __( 'Image', 'nexus-pro' ),
							'video' => __( 'Video', 'nexus-pro' ),
						),
						'default' => 'image',
					),
					array(
						'name'  => 'image',
						'label' => __( 'Image', 'nexus-pro' ),
						'type'  => 'media',
					),
					array(
						'name'        => 'video_url',
						'label'       => __( 'Video URL', 'nexus-pro' ),
						'type'        => 'url',
						'placeholder' => __( 'YouTube or Vimeo URL', 'nexus-pro' ),
					),
					array(
						'name'        => 'title',
						'label'       => __( 'Title', 'nexus-pro' ),
						'type'        => 'text',
						'placeholder' => __( 'Enter title', 'nexus-pro' ),
					),
					array(
						'name'        => 'description',
						'label'       => __( 'Description', 'nexus-pro' ),
						'type'        => 'textarea',
						'placeholder' => __( 'Enter description', 'nexus-pro' ),
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
			'show_thumbnails',
			array(
				'label'   => __( 'Show Thumbnails', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'thumbnail_position',
			array(
				'label'   => __( 'Thumbnail Position', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'bottom' => __( 'Bottom', 'nexus-pro' ),
					'left'   => __( 'Left', 'nexus-pro' ),
					'right'  => __( 'Right', 'nexus-pro' ),
				),
				'default' => 'bottom',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'   => __( 'Auto Play', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => false,
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
			'infinite_loop',
			array(
				'label'   => __( 'Infinite Loop', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
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
			'image_height',
			array(
				'label'   => __( 'Image Height (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 500,
				'min'     => 200,
				'max'     => 800,
			)
		);

		$this->add_control(
			'thumbnail_size',
			array(
				'label'   => __( 'Thumbnail Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 100,
				'min'     => 60,
				'max'     => 200,
			)
		);

		$this->add_control(
			'show_caption',
			array(
				'label'   => __( 'Show Caption', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'caption_background',
			array(
				'label'   => __( 'Caption Background', 'nexus-pro' ),
				'type'    => 'color',
				'default' => 'rgba(0, 0, 0, 0.7)',
			)
		);

		$this->add_control(
			'caption_color',
			array(
				'label'   => __( 'Caption Text Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#FFFFFF',
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

		$this->end_controls_section();
	}

	/**
	 * Get video embed HTML
	 */
	private function get_video_embed( $url ) {
		// YouTube
		if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches ) ) {
			return '<iframe src="https://www.youtube.com/embed/' . esc_attr( $matches[1] ) . '" frameborder="0" allowfullscreen></iframe>';
		}

		// Vimeo
		if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $matches ) ) {
			return '<iframe src="https://player.vimeo.com/video/' . esc_attr( $matches[1] ) . '" frameborder="0" allowfullscreen></iframe>';
		}

		return '';
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings    = $this->get_settings();
		$carousel_id = 'media-carousel-' . uniqid();

		if ( empty( $settings['items'] ) ) {
			echo '<div class="nexus-media-carousel-placeholder">' . esc_html__( 'Add media items', 'nexus-pro' ) . '</div>';
			return;
		}

		$carousel_data = array(
			'autoplay'        => (bool) $settings['autoplay'],
			'autoplaySpeed'   => intval( $settings['autoplay_speed'] ),
			'showArrows'      => (bool) $settings['show_arrows'],
			'infiniteLoop'    => (bool) $settings['infinite_loop'],
			'animationSpeed'  => intval( $settings['animation_speed'] ),
			'showThumbnails'  => (bool) $settings['show_thumbnails'],
		);

		?>
		<div class="nexus-media-carousel thumbnail-<?php echo esc_attr( $settings['thumbnail_position'] ); ?>" 
			 id="<?php echo esc_attr( $carousel_id ); ?>"
			 data-carousel='<?php echo esc_attr( wp_json_encode( $carousel_data ) ); ?>'>
			
			<!-- Main Carousel -->
			<div class="media-carousel-main">
				<div class="carousel-slides">
					<?php foreach ( $settings['items'] as $index => $item ) : ?>
						<div class="carousel-slide <?php echo 0 === $index ? 'active' : ''; ?>">
							<div class="slide-content" 
								 style="height: <?php echo esc_attr( $settings['image_height'] ); ?>px;">
								
								<?php if ( 'video' === $item['type'] && ! empty( $item['video_url'] ) ) : ?>
									<div class="slide-video">
										<?php echo wp_kses_post( $this->get_video_embed( $item['video_url'] ) ); ?>
									</div>
								<?php elseif ( ! empty( $item['image'] ) ) : ?>
									<img src="<?php echo esc_url( $item['image'] ); ?>" 
										 alt="<?php echo esc_attr( $item['title'] ); ?>">
								<?php else : ?>
									<div class="slide-placeholder" 
										 style="height: <?php echo esc_attr( $settings['image_height'] ); ?>px;">
										<span class="dashicons dashicons-format-image"></span>
									</div>
								<?php endif; ?>

								<?php if ( $settings['show_caption'] && ( ! empty( $item['title'] ) || ! empty( $item['description'] ) ) ) : ?>
									<div class="slide-caption" 
										 style="background: <?php echo esc_attr( $settings['caption_background'] ); ?>;
												color: <?php echo esc_attr( $settings['caption_color'] ); ?>;">
										<?php if ( ! empty( $item['title'] ) ) : ?>
											<h3 class="caption-title"><?php echo esc_html( $item['title'] ); ?></h3>
										<?php endif; ?>
										<?php if ( ! empty( $item['description'] ) ) : ?>
											<p class="caption-description"><?php echo esc_html( $item['description'] ); ?></p>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<?php if ( $settings['show_arrows'] ) : ?>
					<button class="carousel-arrow prev" 
							style="color: <?php echo esc_attr( $settings['arrow_color'] ); ?>;
								   background: <?php echo esc_attr( $settings['arrow_background'] ); ?>;">
						<span class="dashicons dashicons-arrow-left-alt2"></span>
					</button>
					<button class="carousel-arrow next" 
							style="color: <?php echo esc_attr( $settings['arrow_color'] ); ?>;
								   background: <?php echo esc_attr( $settings['arrow_background'] ); ?>;">
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</button>
				<?php endif; ?>
			</div>

			<!-- Thumbnails -->
			<?php if ( $settings['show_thumbnails'] ) : ?>
				<div class="media-carousel-thumbnails">
					<?php foreach ( $settings['items'] as $index => $item ) : ?>
						<div class="thumbnail <?php echo 0 === $index ? 'active' : ''; ?>" 
							 data-slide="<?php echo esc_attr( $index ); ?>"
							 style="width: <?php echo esc_attr( $settings['thumbnail_size'] ); ?>px; 
									height: <?php echo esc_attr( $settings['thumbnail_size'] ); ?>px;">
							
							<?php if ( 'video' === $item['type'] ) : ?>
								<div class="thumbnail-video-indicator">
									<span class="dashicons dashicons-controls-play"></span>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $item['image'] ) ) : ?>
								<img src="<?php echo esc_url( $item['image'] ); ?>" 
									 alt="<?php echo esc_attr( $item['title'] ); ?>">
							<?php else : ?>
								<div class="thumbnail-placeholder">
									<span class="dashicons dashicons-format-image"></span>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
