<?php
/**
 * Animated Headline Widget
 *
 * Display animated text headlines with rotation effects
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Animated Headline Widget Class
 */
class Nexus_Animated_Headline_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'animated-headline';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Animated Headline', 'nexus-pro' );
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
				'label' => __( 'Headline', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'headline_style',
			array(
				'label'   => __( 'Style', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'rotating'   => __( 'Rotating', 'nexus-pro' ),
					'highlighted' => __( 'Highlighted', 'nexus-pro' ),
					'typing'     => __( 'Typing', 'nexus-pro' ),
				),
				'default' => 'rotating',
			)
		);

		$this->add_control(
			'before_text',
			array(
				'label'       => __( 'Before Text', 'nexus-pro' ),
				'type'        => 'text',
				'default'     => __( 'We are', 'nexus-pro' ),
				'placeholder' => __( 'Enter text before', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'rotating_text',
			array(
				'label'       => __( 'Rotating Text', 'nexus-pro' ),
				'type'        => 'textarea',
				'default'     => "Creative\nInnovative\nProfessional",
				'placeholder' => __( 'Enter words (one per line)', 'nexus-pro' ),
				'description' => __( 'Enter each word on a new line', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'after_text',
			array(
				'label'       => __( 'After Text', 'nexus-pro' ),
				'type'        => 'text',
				'default'     => __( 'Developers', 'nexus-pro' ),
				'placeholder' => __( 'Enter text after', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'animation_type',
			array(
				'label'   => __( 'Animation Type', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'fade'        => __( 'Fade', 'nexus-pro' ),
					'slide-down'  => __( 'Slide Down', 'nexus-pro' ),
					'slide-up'    => __( 'Slide Up', 'nexus-pro' ),
					'clip'        => __( 'Clip', 'nexus-pro' ),
					'zoom'        => __( 'Zoom', 'nexus-pro' ),
					'rotate'      => __( 'Rotate', 'nexus-pro' ),
					'flip'        => __( 'Flip', 'nexus-pro' ),
				),
				'default' => 'fade',
			)
		);

		$this->add_control(
			'tag',
			array(
				'label'   => __( 'HTML Tag', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'default' => 'h2',
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

		$this->end_controls_section();

		// Animation settings
		$this->start_controls_section(
			'animation_section',
			array(
				'label' => __( 'Animation Settings', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'animation_speed',
			array(
				'label'   => __( 'Animation Speed (ms)', 'nexus-pro' ),
				'type'    => 'number',
				'default' => 500,
				'min'     => 100,
				'max'     => 2000,
				'step'    => 50,
			)
		);

		$this->add_control(
			'pause_duration',
			array(
				'label'   => __( 'Pause Duration (ms)', 'nexus-pro' ),
				'type'    => 'number',
				'default' => 2000,
				'min'     => 500,
				'max'     => 10000,
				'step'    => 100,
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'   => __( 'Infinite Loop', 'nexus-pro' ),
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
			'font_size',
			array(
				'label'   => __( 'Font Size', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 48,
				'min'     => 16,
				'max'     => 120,
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
					'500' => __( 'Medium', 'nexus-pro' ),
					'600' => __( 'Semi Bold', 'nexus-pro' ),
					'700' => __( 'Bold', 'nexus-pro' ),
					'800' => __( 'Extra Bold', 'nexus-pro' ),
				),
				'default' => '700',
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'   => __( 'Text Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#1D2327',
			)
		);

		$this->add_control(
			'animated_color',
			array(
				'label'   => __( 'Animated Text Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#2271B1',
			)
		);

		$this->add_control(
			'animated_background',
			array(
				'label'   => __( 'Animated Background', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '',
			)
		);

		$this->add_control(
			'line_height',
			array(
				'label'   => __( 'Line Height', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 1.2,
				'min'     => 1,
				'max'     => 3,
				'step'    => 0.1,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();

		$rotating_words = array_filter( array_map( 'trim', explode( "\n", $settings['rotating_text'] ) ) );
		
		if ( empty( $rotating_words ) ) {
			echo '<div class="nexus-animated-headline-placeholder">' . esc_html__( 'Please add rotating text (one per line)', 'nexus-pro' ) . '</div>';
			return;
		}

		$headline_id = 'animated-headline-' . uniqid();
		$animation_data = array(
			'animationType'  => $settings['animation_type'],
			'animationSpeed' => intval( $settings['animation_speed'] ),
			'pauseDuration'  => intval( $settings['pause_duration'] ),
			'loop'           => (bool) $settings['loop'],
		);

		$tag = tag_escape( $settings['tag'] );

		?>
		<div class="nexus-animated-headline" 
			 id="<?php echo esc_attr( $headline_id ); ?>"
			 data-animation='<?php echo esc_attr( wp_json_encode( $animation_data ) ); ?>'
			 style="text-align: <?php echo esc_attr( $settings['alignment'] ); ?>;">
			
			<<?php echo $tag; ?> class="animated-headline-text"
				style="font-size: <?php echo esc_attr( $settings['font_size'] ); ?>px;
					   font-weight: <?php echo esc_attr( $settings['font_weight'] ); ?>;
					   color: <?php echo esc_attr( $settings['text_color'] ); ?>;
					   line-height: <?php echo esc_attr( $settings['line_height'] ); ?>;">
				
				<?php if ( ! empty( $settings['before_text'] ) ) : ?>
					<span class="headline-before"><?php echo esc_html( $settings['before_text'] ); ?></span>
				<?php endif; ?>

				<span class="headline-animated-wrapper" 
					  style="color: <?php echo esc_attr( $settings['animated_color'] ); ?>;
							 <?php echo ! empty( $settings['animated_background'] ) ? 'background: ' . esc_attr( $settings['animated_background'] ) . '; padding: 0 10px;' : ''; ?>">
					
					<?php foreach ( $rotating_words as $index => $word ) : ?>
						<span class="headline-word <?php echo 0 === $index ? 'is-visible' : ''; ?>" 
							  data-word="<?php echo esc_attr( $word ); ?>">
							<?php echo esc_html( $word ); ?>
						</span>
					<?php endforeach; ?>
				</span>

				<?php if ( ! empty( $settings['after_text'] ) ) : ?>
					<span class="headline-after"><?php echo esc_html( $settings['after_text'] ); ?></span>
				<?php endif; ?>
			</<?php echo $tag; ?>>
		</div>
		<?php
	}
}
