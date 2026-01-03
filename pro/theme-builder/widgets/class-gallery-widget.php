<?php
/**
 * Gallery Widget
 *
 * Display image gallery with lightbox
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gallery Widget Class
 */
class Nexus_Gallery_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'gallery';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Gallery', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-format-gallery';
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
				'label' => __( 'Gallery', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'images',
			array(
				'label'   => __( 'Add Images', 'nexus-pro' ),
				'type'    => 'media',
				'multiple' => true,
				'default' => array(),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'grid'    => __( 'Grid', 'nexus-pro' ),
					'masonry' => __( 'Masonry', 'nexus-pro' ),
					'justified' => __( 'Justified', 'nexus-pro' ),
				),
				'default' => 'grid',
			)
		);

		$this->add_control(
			'columns',
			array(
				'label'   => __( 'Columns', 'nexus-pro' ),
				'type'    => 'number',
				'default' => 3,
				'min'     => 1,
				'max'     => 6,
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
					'full'      => __( 'Full', 'nexus-pro' ),
				),
				'default' => 'medium',
			)
		);

		$this->add_control(
			'lightbox',
			array(
				'label'   => __( 'Enable Lightbox', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'show_caption',
			array(
				'label'   => __( 'Show Caption', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => false,
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
			'gap',
			array(
				'label'   => __( 'Gap', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 10,
				'min'     => 0,
				'max'     => 50,
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'   => __( 'Border Radius', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 0,
				'min'     => 0,
				'max'     => 50,
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'label'   => __( 'Hover Effect', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'none'   => __( 'None', 'nexus-pro' ),
					'zoom'   => __( 'Zoom', 'nexus-pro' ),
					'fade'   => __( 'Fade', 'nexus-pro' ),
					'overlay' => __( 'Overlay', 'nexus-pro' ),
				),
				'default' => 'zoom',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();
		$images = ! empty( $settings['images'] ) ? $settings['images'] : array();

		if ( empty( $images ) ) {
			return;
		}

		$gap = $settings['gap'] . 'px';
		$columns = intval( $settings['columns'] );
		$border_radius = $settings['border_radius'] . 'px';

		?>
		<div class="nexus-gallery nexus-gallery-<?php echo esc_attr( $settings['layout'] ); ?> 
					nexus-gallery-hover-<?php echo esc_attr( $settings['hover_effect'] ); ?>"
			 data-lightbox="<?php echo esc_attr( $settings['lightbox'] ? '1' : '0' ); ?>"
			 style="display: grid; 
					grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr); 
					gap: <?php echo esc_attr( $gap ); ?>;">
			
			<?php foreach ( $images as $image ) : ?>
				<?php
				$image_id = is_array( $image ) ? $image['id'] : $image;
				$image_url = wp_get_attachment_image_url( $image_id, $settings['image_size'] );
				$full_url = wp_get_attachment_image_url( $image_id, 'full' );
				$caption = wp_get_attachment_caption( $image_id );
				$alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
				?>
				
				<div class="nexus-gallery-item" 
					 style="border-radius: <?php echo esc_attr( $border_radius ); ?>; overflow: hidden;">
					
					<?php if ( $settings['lightbox'] ) : ?>
						<a href="<?php echo esc_url( $full_url ); ?>" 
						   class="nexus-lightbox-link"
						   data-caption="<?php echo esc_attr( $caption ); ?>">
					<?php endif; ?>
					
					<img src="<?php echo esc_url( $image_url ); ?>" 
						 alt="<?php echo esc_attr( $alt ); ?>"
						 style="width: 100%; height: auto; display: block;">
					
					<?php if ( $settings['lightbox'] ) : ?>
						</a>
					<?php endif; ?>
					
					<?php if ( $settings['show_caption'] && $caption ) : ?>
						<div class="nexus-gallery-caption"><?php echo esc_html( $caption ); ?></div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( $settings['lightbox'] ) : ?>
			<style>
				.nexus-gallery-item { position: relative; }
				.nexus-gallery-hover-zoom .nexus-gallery-item img { transition: transform 0.3s; }
				.nexus-gallery-hover-zoom .nexus-gallery-item:hover img { transform: scale(1.1); }
				.nexus-gallery-hover-fade .nexus-gallery-item img { transition: opacity 0.3s; }
				.nexus-gallery-hover-fade .nexus-gallery-item:hover img { opacity: 0.7; }
				.nexus-gallery-caption { 
					padding: 10px; 
					background: rgba(0,0,0,0.7); 
					color: #fff; 
					font-size: 14px; 
				}
			</style>
		<?php endif; ?>
		<?php
	}
}
