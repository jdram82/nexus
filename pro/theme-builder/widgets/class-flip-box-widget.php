<?php
/**
 * Flip Box Widget
 *
 * Display content that flips to reveal back side on hover
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Flip Box Widget Class
 */
class Nexus_Flip_Box_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'flip-box';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Flip Box', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-image-flip-horizontal';
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
		// Front side controls
		$this->start_controls_section(
			'front_section',
			array(
				'label' => __( 'Front Side', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'front_icon',
			array(
				'label'   => __( 'Icon', 'nexus-pro' ),
				'type'    => 'icon',
				'default' => 'dashicons-star-filled',
			)
		);

		$this->add_control(
			'front_title',
			array(
				'label'       => __( 'Title', 'nexus-pro' ),
				'type'        => 'text',
				'default'     => __( 'Front Title', 'nexus-pro' ),
				'placeholder' => __( 'Enter title', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'front_description',
			array(
				'label'       => __( 'Description', 'nexus-pro' ),
				'type'        => 'textarea',
				'default'     => __( 'This is the front side description.', 'nexus-pro' ),
				'placeholder' => __( 'Enter description', 'nexus-pro' ),
			)
		);

		$this->end_controls_section();

		// Back side controls
		$this->start_controls_section(
			'back_section',
			array(
				'label' => __( 'Back Side', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'back_icon',
			array(
				'label'   => __( 'Icon', 'nexus-pro' ),
				'type'    => 'icon',
				'default' => 'dashicons-admin-generic',
			)
		);

		$this->add_control(
			'back_title',
			array(
				'label'       => __( 'Title', 'nexus-pro' ),
				'type'        => 'text',
				'default'     => __( 'Back Title', 'nexus-pro' ),
				'placeholder' => __( 'Enter title', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'back_description',
			array(
				'label'       => __( 'Description', 'nexus-pro' ),
				'type'        => 'textarea',
				'default'     => __( 'This is the back side description with more details.', 'nexus-pro' ),
				'placeholder' => __( 'Enter description', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => __( 'Button Text', 'nexus-pro' ),
				'type'        => 'text',
				'default'     => __( 'Learn More', 'nexus-pro' ),
				'placeholder' => __( 'Enter button text', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'       => __( 'Button Link', 'nexus-pro' ),
				'type'        => 'url',
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
			'flip_effect',
			array(
				'label'   => __( 'Flip Effect', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'flip-horizontal' => __( 'Flip Horizontal', 'nexus-pro' ),
					'flip-vertical'   => __( 'Flip Vertical', 'nexus-pro' ),
					'fade'            => __( 'Fade', 'nexus-pro' ),
					'zoom-in'         => __( 'Zoom In', 'nexus-pro' ),
					'zoom-out'        => __( 'Zoom Out', 'nexus-pro' ),
				),
				'default' => 'flip-horizontal',
			)
		);

		$this->add_control(
			'height',
			array(
				'label'   => __( 'Height (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 300,
				'min'     => 200,
				'max'     => 600,
			)
		);

		$this->end_controls_section();

		// Style controls - Front
		$this->start_controls_section(
			'front_style_section',
			array(
				'label' => __( 'Front Style', 'nexus-pro' ),
				'tab'   => 'style',
			)
		);

		$this->add_control(
			'front_background',
			array(
				'label'   => __( 'Background Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#2271B1',
			)
		);

		$this->add_control(
			'front_text_color',
			array(
				'label'   => __( 'Text Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#FFFFFF',
			)
		);

		$this->add_control(
			'front_icon_size',
			array(
				'label'   => __( 'Icon Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 60,
				'min'     => 20,
				'max'     => 120,
			)
		);

		$this->add_control(
			'front_title_size',
			array(
				'label'   => __( 'Title Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 24,
				'min'     => 16,
				'max'     => 48,
			)
		);

		$this->end_controls_section();

		// Style controls - Back
		$this->start_controls_section(
			'back_style_section',
			array(
				'label' => __( 'Back Style', 'nexus-pro' ),
				'tab'   => 'style',
			)
		);

		$this->add_control(
			'back_background',
			array(
				'label'   => __( 'Background Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#1D2327',
			)
		);

		$this->add_control(
			'back_text_color',
			array(
				'label'   => __( 'Text Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#FFFFFF',
			)
		);

		$this->add_control(
			'back_icon_size',
			array(
				'label'   => __( 'Icon Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 40,
				'min'     => 20,
				'max'     => 120,
			)
		);

		$this->add_control(
			'back_title_size',
			array(
				'label'   => __( 'Title Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 24,
				'min'     => 16,
				'max'     => 48,
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'   => __( 'Button Background', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#2271B1',
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'   => __( 'Button Text Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#FFFFFF',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();
		$flip_id  = 'flip-box-' . uniqid();

		?>
		<div class="nexus-flip-box effect-<?php echo esc_attr( $settings['flip_effect'] ); ?>" 
			 id="<?php echo esc_attr( $flip_id ); ?>"
			 style="height: <?php echo esc_attr( $settings['height'] ); ?>px;">
			
			<div class="flip-box-inner">
				<!-- Front Side -->
				<div class="flip-box-front" 
					 style="background: <?php echo esc_attr( $settings['front_background'] ); ?>;
							color: <?php echo esc_attr( $settings['front_text_color'] ); ?>;">
					
					<?php if ( ! empty( $settings['front_icon'] ) ) : ?>
						<div class="flip-icon" 
							 style="font-size: <?php echo esc_attr( $settings['front_icon_size'] ); ?>px;">
							<span class="<?php echo esc_attr( $settings['front_icon'] ); ?>"></span>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $settings['front_title'] ) ) : ?>
						<h3 class="flip-title" 
							style="font-size: <?php echo esc_attr( $settings['front_title_size'] ); ?>px;">
							<?php echo esc_html( $settings['front_title'] ); ?>
						</h3>
					<?php endif; ?>

					<?php if ( ! empty( $settings['front_description'] ) ) : ?>
						<p class="flip-description">
							<?php echo esc_html( $settings['front_description'] ); ?>
						</p>
					<?php endif; ?>
				</div>

				<!-- Back Side -->
				<div class="flip-box-back" 
					 style="background: <?php echo esc_attr( $settings['back_background'] ); ?>;
							color: <?php echo esc_attr( $settings['back_text_color'] ); ?>;">
					
					<?php if ( ! empty( $settings['back_icon'] ) ) : ?>
						<div class="flip-icon" 
							 style="font-size: <?php echo esc_attr( $settings['back_icon_size'] ); ?>px;">
							<span class="<?php echo esc_attr( $settings['back_icon'] ); ?>"></span>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $settings['back_title'] ) ) : ?>
						<h3 class="flip-title" 
							style="font-size: <?php echo esc_attr( $settings['back_title_size'] ); ?>px;">
							<?php echo esc_html( $settings['back_title'] ); ?>
						</h3>
					<?php endif; ?>

					<?php if ( ! empty( $settings['back_description'] ) ) : ?>
						<p class="flip-description">
							<?php echo esc_html( $settings['back_description'] ); ?>
						</p>
					<?php endif; ?>

					<?php if ( ! empty( $settings['button_text'] ) ) : ?>
						<a href="<?php echo esc_url( $settings['button_link'] ); ?>" 
						   class="flip-button"
						   style="background: <?php echo esc_attr( $settings['button_background'] ); ?>;
								  color: <?php echo esc_attr( $settings['button_text_color'] ); ?>;">
							<?php echo esc_html( $settings['button_text'] ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
