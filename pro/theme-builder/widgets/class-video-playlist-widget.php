<?php
/**
 * Video Playlist Widget
 *
 * Display a playlist of videos with thumbnails
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Video Playlist Widget Class
 */
class Nexus_Video_Playlist_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'video-playlist';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Video Playlist', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-video-alt3';
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
				'label' => __( 'Videos', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'videos',
			array(
				'label'   => __( 'Video List', 'nexus-pro' ),
				'type'    => 'repeater',
				'default' => array(
					array(
						'title'       => __( 'Introduction Video', 'nexus-pro' ),
						'video_url'   => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
						'duration'    => '3:45',
						'description' => __( 'Learn the basics', 'nexus-pro' ),
						'thumbnail'   => '',
					),
					array(
						'title'       => __( 'Getting Started', 'nexus-pro' ),
						'video_url'   => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
						'duration'    => '5:20',
						'description' => __( 'Start your journey', 'nexus-pro' ),
						'thumbnail'   => '',
					),
					array(
						'title'       => __( 'Advanced Techniques', 'nexus-pro' ),
						'video_url'   => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
						'duration'    => '8:15',
						'description' => __( 'Master the skills', 'nexus-pro' ),
						'thumbnail'   => '',
					),
				),
				'fields'  => array(
					array(
						'name'        => 'title',
						'label'       => __( 'Title', 'nexus-pro' ),
						'type'        => 'text',
						'default'     => __( 'Video Title', 'nexus-pro' ),
						'placeholder' => __( 'Enter title', 'nexus-pro' ),
					),
					array(
						'name'        => 'video_url',
						'label'       => __( 'Video URL', 'nexus-pro' ),
						'type'        => 'url',
						'placeholder' => __( 'YouTube or Vimeo URL', 'nexus-pro' ),
					),
					array(
						'name'        => 'duration',
						'label'       => __( 'Duration', 'nexus-pro' ),
						'type'        => 'text',
						'default'     => '0:00',
						'placeholder' => __( 'e.g., 3:45', 'nexus-pro' ),
					),
					array(
						'name'        => 'description',
						'label'       => __( 'Description', 'nexus-pro' ),
						'type'        => 'textarea',
						'placeholder' => __( 'Enter description', 'nexus-pro' ),
					),
					array(
						'name'  => 'thumbnail',
						'label' => __( 'Custom Thumbnail', 'nexus-pro' ),
						'type'  => 'media',
					),
				),
			)
		);

		$this->end_controls_section();

		// Settings section
		$this->start_controls_section(
			'settings_section',
			array(
				'label' => __( 'Settings', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'playlist_layout',
			array(
				'label'   => __( 'Layout', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'horizontal' => __( 'Horizontal', 'nexus-pro' ),
					'vertical'   => __( 'Vertical', 'nexus-pro' ),
				),
				'default' => 'horizontal',
			)
		);

		$this->add_control(
			'playlist_position',
			array(
				'label'   => __( 'Playlist Position', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'right'  => __( 'Right', 'nexus-pro' ),
					'left'   => __( 'Left', 'nexus-pro' ),
					'bottom' => __( 'Bottom', 'nexus-pro' ),
				),
				'default' => 'right',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'   => __( 'Autoplay First Video', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => false,
			)
		);

		$this->add_control(
			'show_duration',
			array(
				'label'   => __( 'Show Duration', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'show_description',
			array(
				'label'   => __( 'Show Description', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'show_play_icon',
			array(
				'label'   => __( 'Show Play Icon', 'nexus-pro' ),
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
			'player_height',
			array(
				'label'   => __( 'Player Height (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 400,
				'min'     => 200,
				'max'     => 800,
			)
		);

		$this->add_control(
			'thumbnail_width',
			array(
				'label'   => __( 'Thumbnail Width (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 120,
				'min'     => 80,
				'max'     => 200,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'   => __( 'Title Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#1D2327',
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
			'description_color',
			array(
				'label'   => __( 'Description Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#646970',
			)
		);

		$this->add_control(
			'duration_color',
			array(
				'label'   => __( 'Duration Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#FFFFFF',
			)
		);

		$this->add_control(
			'duration_background',
			array(
				'label'   => __( 'Duration Background', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#000000',
			)
		);

		$this->add_control(
			'active_background',
			array(
				'label'   => __( 'Active Item Background', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#F0F6FC',
			)
		);

		$this->add_control(
			'hover_background',
			array(
				'label'   => __( 'Hover Background', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#F0F0F1',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get video embed HTML
	 */
	private function get_video_embed( $url, $autoplay = false ) {
		$autoplay_param = $autoplay ? '?autoplay=1' : '';

		// YouTube
		if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches ) ) {
			return '<iframe src="https://www.youtube.com/embed/' . esc_attr( $matches[1] ) . $autoplay_param . '" frameborder="0" allowfullscreen></iframe>';
		}

		// Vimeo
		if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $matches ) ) {
			return '<iframe src="https://player.vimeo.com/video/' . esc_attr( $matches[1] ) . $autoplay_param . '" frameborder="0" allowfullscreen></iframe>';
		}

		return '';
	}

	/**
	 * Get video thumbnail URL
	 */
	private function get_video_thumbnail( $url, $custom_thumbnail = '' ) {
		if ( ! empty( $custom_thumbnail ) ) {
			return $custom_thumbnail;
		}

		// YouTube thumbnail
		if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches ) ) {
			return 'https://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg';
		}

		// Vimeo thumbnail would require API call, return placeholder
		return '';
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings    = $this->get_settings();
		$playlist_id = 'video-playlist-' . uniqid();

		if ( empty( $settings['videos'] ) ) {
			echo '<div class="nexus-video-playlist-placeholder">' . esc_html__( 'Add videos to playlist', 'nexus-pro' ) . '</div>';
			return;
		}

		$first_video = $settings['videos'][0];

		?>
		<div class="nexus-video-playlist layout-<?php echo esc_attr( $settings['playlist_layout'] ); ?> position-<?php echo esc_attr( $settings['playlist_position'] ); ?>" 
			 id="<?php echo esc_attr( $playlist_id ); ?>">
			
			<!-- Video Player -->
			<div class="playlist-player" 
				 style="height: <?php echo esc_attr( $settings['player_height'] ); ?>px;">
				<div class="player-container" data-video-index="0">
					<?php echo wp_kses_post( $this->get_video_embed( $first_video['video_url'], $settings['autoplay'] ) ); ?>
				</div>

				<div class="player-info">
					<h3 class="current-video-title" 
						style="color: <?php echo esc_attr( $settings['title_color'] ); ?>;">
						<?php echo esc_html( $first_video['title'] ); ?>
					</h3>
					<?php if ( $settings['show_description'] && ! empty( $first_video['description'] ) ) : ?>
						<p class="current-video-description" 
						   style="color: <?php echo esc_attr( $settings['description_color'] ); ?>;">
							<?php echo esc_html( $first_video['description'] ); ?>
						</p>
					<?php endif; ?>
				</div>
			</div>

			<!-- Playlist -->
			<div class="playlist-items">
				<?php foreach ( $settings['videos'] as $index => $video ) : ?>
					<div class="playlist-item <?php echo 0 === $index ? 'active' : ''; ?>" 
						 data-video-index="<?php echo esc_attr( $index ); ?>"
						 data-video-url="<?php echo esc_attr( $video['video_url'] ); ?>"
						 data-video-title="<?php echo esc_attr( $video['title'] ); ?>"
						 data-video-description="<?php echo esc_attr( $video['description'] ); ?>"
						 style="--active-bg: <?php echo esc_attr( $settings['active_background'] ); ?>; 
								--hover-bg: <?php echo esc_attr( $settings['hover_background'] ); ?>;">
						
						<div class="item-thumbnail" 
							 style="width: <?php echo esc_attr( $settings['thumbnail_width'] ); ?>px;">
							<?php
							$thumbnail = $this->get_video_thumbnail( $video['video_url'], $video['thumbnail'] );
							if ( $thumbnail ) :
								?>
								<img src="<?php echo esc_url( $thumbnail ); ?>" 
									 alt="<?php echo esc_attr( $video['title'] ); ?>">
							<?php else : ?>
								<div class="thumbnail-placeholder">
									<span class="dashicons dashicons-format-video"></span>
								</div>
							<?php endif; ?>

							<?php if ( $settings['show_play_icon'] ) : ?>
								<div class="play-overlay">
									<span class="dashicons dashicons-controls-play"></span>
								</div>
							<?php endif; ?>

							<?php if ( $settings['show_duration'] && ! empty( $video['duration'] ) ) : ?>
								<span class="video-duration" 
									  style="color: <?php echo esc_attr( $settings['duration_color'] ); ?>;
											 background: <?php echo esc_attr( $settings['duration_background'] ); ?>;">
									<?php echo esc_html( $video['duration'] ); ?>
								</span>
							<?php endif; ?>
						</div>

						<div class="item-content">
							<h4 class="item-title" 
								style="color: <?php echo esc_attr( $settings['title_color'] ); ?>;
									   font-size: <?php echo esc_attr( $settings['title_size'] ); ?>px;">
								<?php echo esc_html( $video['title'] ); ?>
							</h4>

							<?php if ( $settings['show_description'] && ! empty( $video['description'] ) ) : ?>
								<p class="item-description" 
								   style="color: <?php echo esc_attr( $settings['description_color'] ); ?>;">
									<?php echo esc_html( wp_trim_words( $video['description'], 10 ) ); ?>
								</p>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
