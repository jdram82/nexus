<?php
/**
 * Lottie Animation Widget
 *
 * Display Lottie JSON animations
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lottie Animation Widget Class
 */
class Nexus_Lottie_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'lottie';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Lottie Animation', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-format-video';
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
				'label' => __( 'Animation', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'source_type',
			array(
				'label'   => __( 'Source', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'url'  => __( 'External URL', 'nexus-pro' ),
					'file' => __( 'Upload File', 'nexus-pro' ),
					'code' => __( 'Custom Code', 'nexus-pro' ),
				),
				'default' => 'url',
			)
		);

		$this->add_control(
			'animation_url',
			array(
				'label'       => __( 'Animation URL', 'nexus-pro' ),
				'type'        => 'url',
				'placeholder' => __( 'https://assets.example.com/animation.json', 'nexus-pro' ),
				'description' => __( 'URL to Lottie JSON file', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'animation_file',
			array(
				'label'       => __( 'Upload JSON', 'nexus-pro' ),
				'type'        => 'media',
				'description' => __( 'Upload Lottie JSON file', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'animation_code',
			array(
				'label'       => __( 'JSON Code', 'nexus-pro' ),
				'type'        => 'textarea',
				'placeholder' => __( '{"v":"5.5.7","fr":60,"ip":0...}', 'nexus-pro' ),
				'description' => __( 'Paste Lottie JSON code', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'link_to',
			array(
				'label'   => __( 'Link', 'nexus-pro' ),
				'type'    => 'url',
				'placeholder' => __( 'https://your-link.com', 'nexus-pro' ),
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
			'trigger',
			array(
				'label'   => __( 'Trigger', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'autoplay'  => __( 'Autoplay', 'nexus-pro' ),
					'viewport'  => __( 'On Scroll (Viewport)', 'nexus-pro' ),
					'hover'     => __( 'On Hover', 'nexus-pro' ),
					'click'     => __( 'On Click', 'nexus-pro' ),
				),
				'default' => 'autoplay',
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'   => __( 'Loop', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'reverse',
			array(
				'label'   => __( 'Reverse', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => false,
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'   => __( 'Speed', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 1,
				'min'     => 0.1,
				'max'     => 3,
				'step'    => 0.1,
			)
		);

		$this->add_control(
			'start_point',
			array(
				'label'       => __( 'Start Point (%)', 'nexus-pro' ),
				'type'        => 'slider',
				'default'     => 0,
				'min'         => 0,
				'max'         => 100,
				'description' => __( 'Start animation from this point', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'end_point',
			array(
				'label'       => __( 'End Point (%)', 'nexus-pro' ),
				'type'        => 'slider',
				'default'     => 100,
				'min'         => 0,
				'max'         => 100,
				'description' => __( 'End animation at this point', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'renderer',
			array(
				'label'   => __( 'Renderer', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'svg'    => __( 'SVG (Recommended)', 'nexus-pro' ),
					'canvas' => __( 'Canvas', 'nexus-pro' ),
					'html'   => __( 'HTML', 'nexus-pro' ),
				),
				'default' => 'svg',
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
			'width_type',
			array(
				'label'   => __( 'Width', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'auto'   => __( 'Auto', 'nexus-pro' ),
					'custom' => __( 'Custom', 'nexus-pro' ),
				),
				'default' => 'auto',
			)
		);

		$this->add_control(
			'custom_width',
			array(
				'label'   => __( 'Custom Width (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 400,
				'min'     => 100,
				'max'     => 1200,
			)
		);

		$this->add_control(
			'max_width',
			array(
				'label'   => __( 'Max Width (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 600,
				'min'     => 100,
				'max'     => 1200,
			)
		);

		$this->add_control(
			'opacity',
			array(
				'label'   => __( 'Opacity', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 1,
				'min'     => 0.1,
				'max'     => 1,
				'step'    => 0.1,
			)
		);

		$this->add_control(
			'css_filter_enabled',
			array(
				'label'   => __( 'CSS Filters', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => false,
			)
		);

		$this->add_control(
			'css_filter_blur',
			array(
				'label'   => __( 'Blur (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 0,
				'min'     => 0,
				'max'     => 10,
			)
		);

		$this->add_control(
			'css_filter_brightness',
			array(
				'label'   => __( 'Brightness (%)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 100,
				'min'     => 0,
				'max'     => 200,
			)
		);

		$this->add_control(
			'css_filter_contrast',
			array(
				'label'   => __( 'Contrast (%)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 100,
				'min'     => 0,
				'max'     => 200,
			)
		);

		$this->add_control(
			'css_filter_saturate',
			array(
				'label'   => __( 'Saturation (%)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 100,
				'min'     => 0,
				'max'     => 200,
			)
		);

		$this->add_control(
			'css_filter_hue',
			array(
				'label'   => __( 'Hue Rotate (deg)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 0,
				'min'     => 0,
				'max'     => 360,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get animation source
	 */
	private function get_animation_source( $settings ) {
		if ( 'url' === $settings['source_type'] && ! empty( $settings['animation_url'] ) ) {
			return $settings['animation_url'];
		}

		if ( 'file' === $settings['source_type'] && ! empty( $settings['animation_file'] ) ) {
			return $settings['animation_file'];
		}

		if ( 'code' === $settings['source_type'] && ! empty( $settings['animation_code'] ) ) {
			// Validate JSON
			$json = json_decode( $settings['animation_code'] );
			if ( json_last_error() === JSON_ERROR_NONE ) {
				return 'data:application/json;base64,' . base64_encode( $settings['animation_code'] );
			}
		}

		return '';
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings   = $this->get_settings();
		$lottie_id  = 'lottie-' . uniqid();
		$anim_source = $this->get_animation_source( $settings );

		if ( empty( $anim_source ) ) {
			echo '<div class="nexus-lottie-placeholder">' . esc_html__( 'Please configure animation source', 'nexus-pro' ) . '</div>';
			return;
		}

		// Build CSS filters
		$css_filters = '';
		if ( $settings['css_filter_enabled'] ) {
			$filters = array();
			
			if ( $settings['css_filter_blur'] > 0 ) {
				$filters[] = 'blur(' . $settings['css_filter_blur'] . 'px)';
			}
			
			if ( $settings['css_filter_brightness'] != 100 ) {
				$filters[] = 'brightness(' . ( $settings['css_filter_brightness'] / 100 ) . ')';
			}
			
			if ( $settings['css_filter_contrast'] != 100 ) {
				$filters[] = 'contrast(' . ( $settings['css_filter_contrast'] / 100 ) . ')';
			}
			
			if ( $settings['css_filter_saturate'] != 100 ) {
				$filters[] = 'saturate(' . ( $settings['css_filter_saturate'] / 100 ) . ')';
			}
			
			if ( $settings['css_filter_hue'] > 0 ) {
				$filters[] = 'hue-rotate(' . $settings['css_filter_hue'] . 'deg)';
			}
			
			if ( ! empty( $filters ) ) {
				$css_filters = 'filter: ' . implode( ' ', $filters ) . ';';
			}
		}

		$lottie_config = array(
			'trigger'   => $settings['trigger'],
			'loop'      => (bool) $settings['loop'],
			'reverse'   => (bool) $settings['reverse'],
			'speed'     => floatval( $settings['speed'] ),
			'start'     => intval( $settings['start_point'] ),
			'end'       => intval( $settings['end_point'] ),
			'renderer'  => $settings['renderer'],
		);

		$container_style = array(
			'text-align: ' . $settings['alignment'],
			'opacity: ' . $settings['opacity'],
		);

		if ( 'custom' === $settings['width_type'] ) {
			$container_style[] = 'width: ' . $settings['custom_width'] . 'px';
		} else {
			$container_style[] = 'max-width: ' . $settings['max_width'] . 'px';
		}

		$wrapper_start = '';
		$wrapper_end = '';

		if ( ! empty( $settings['link_to'] ) ) {
			$wrapper_start = '<a href="' . esc_url( $settings['link_to'] ) . '" target="_blank" rel="noopener noreferrer">';
			$wrapper_end = '</a>';
		}

		?>
		<div class="nexus-lottie-container" style="<?php echo esc_attr( implode( '; ', $container_style ) ); ?>">
			<?php echo wp_kses_post( $wrapper_start ); ?>
			
			<div class="nexus-lottie-animation" 
				 id="<?php echo esc_attr( $lottie_id ); ?>"
				 data-lottie-config='<?php echo esc_attr( wp_json_encode( $lottie_config ) ); ?>'
				 data-animation-source="<?php echo esc_attr( $anim_source ); ?>"
				 style="<?php echo esc_attr( $css_filters ); ?>">
				<!-- Lottie player will be injected here via JavaScript -->
				<noscript>
					<div class="lottie-fallback">
						<p><?php esc_html_e( 'Please enable JavaScript to view this animation.', 'nexus-pro' ); ?></p>
					</div>
				</noscript>
			</div>

			<?php echo wp_kses_post( $wrapper_end ); ?>
		</div>

		<?php
		// Enqueue Lottie library (will be added to theme-builder.js)
		?>
		<script>
		// Note: Lottie Web library should be loaded via theme-builder.js
		// CDN: https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js
		</script>
		<?php
	}
}
