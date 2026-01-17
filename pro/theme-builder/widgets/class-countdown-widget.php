<?php
/**
 * Countdown Timer Widget
 *
 * Display countdown timer to specific date/time
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Countdown Timer Widget Class
 */
class Nexus_Countdown_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'countdown';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Countdown Timer', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-clock';
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
				'label' => __( 'Countdown', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'countdown_type',
			array(
				'label'   => __( 'Countdown Type', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'fixed'     => __( 'Fixed', 'nexus-pro' ),
					'evergreen' => __( 'Evergreen', 'nexus-pro' ),
				),
				'default' => 'fixed',
			)
		);

		$this->add_control(
			'due_date',
			array(
				'label'       => __( 'Due Date', 'nexus-pro' ),
				'type'        => 'datetime',
				'default'     => gmdate( 'Y-m-d H:i', strtotime( '+7 days' ) ),
				'description' => __( 'Set the countdown end date and time', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'evergreen_days',
			array(
				'label'   => __( 'Days', 'nexus-pro' ),
				'type'    => 'number',
				'default' => 7,
				'min'     => 0,
				'max'     => 365,
			)
		);

		$this->add_control(
			'evergreen_hours',
			array(
				'label'   => __( 'Hours', 'nexus-pro' ),
				'type'    => 'number',
				'default' => 0,
				'min'     => 0,
				'max'     => 23,
			)
		);

		$this->add_control(
			'evergreen_minutes',
			array(
				'label'   => __( 'Minutes', 'nexus-pro' ),
				'type'    => 'number',
				'default' => 0,
				'min'     => 0,
				'max'     => 59,
			)
		);

		$this->add_control(
			'show_labels',
			array(
				'label'   => __( 'Show Labels', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'label_days',
			array(
				'label'   => __( 'Days Label', 'nexus-pro' ),
				'type'    => 'text',
				'default' => __( 'Days', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'label_hours',
			array(
				'label'   => __( 'Hours Label', 'nexus-pro' ),
				'type'    => 'text',
				'default' => __( 'Hours', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'label_minutes',
			array(
				'label'   => __( 'Minutes Label', 'nexus-pro' ),
				'type'    => 'text',
				'default' => __( 'Minutes', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'label_seconds',
			array(
				'label'   => __( 'Seconds Label', 'nexus-pro' ),
				'type'    => 'text',
				'default' => __( 'Seconds', 'nexus-pro' ),
			)
		);

		$this->end_controls_section();

		// Actions section
		$this->start_controls_section(
			'actions_section',
			array(
				'label' => __( 'Actions', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'expire_action',
			array(
				'label'   => __( 'Action After Expire', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'none'     => __( 'None', 'nexus-pro' ),
					'hide'     => __( 'Hide', 'nexus-pro' ),
					'message'  => __( 'Show Message', 'nexus-pro' ),
					'redirect' => __( 'Redirect', 'nexus-pro' ),
				),
				'default' => 'message',
			)
		);

		$this->add_control(
			'expire_message',
			array(
				'label'       => __( 'Expire Message', 'nexus-pro' ),
				'type'        => 'textarea',
				'default'     => __( 'The countdown has ended!', 'nexus-pro' ),
				'placeholder' => __( 'Enter message', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'redirect_url',
			array(
				'label'       => __( 'Redirect URL', 'nexus-pro' ),
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
			'layout',
			array(
				'label'   => __( 'Layout', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'inline' => __( 'Inline', 'nexus-pro' ),
					'block'  => __( 'Block', 'nexus-pro' ),
				),
				'default' => 'inline',
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
			'digit_size',
			array(
				'label'   => __( 'Digit Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 48,
				'min'     => 20,
				'max'     => 120,
			)
		);

		$this->add_control(
			'label_size',
			array(
				'label'   => __( 'Label Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 14,
				'min'     => 10,
				'max'     => 30,
			)
		);

		$this->add_control(
			'digit_color',
			array(
				'label'   => __( 'Digit Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#1D2327',
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'   => __( 'Label Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#646970',
			)
		);

		$this->add_control(
			'box_background',
			array(
				'label'   => __( 'Box Background', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#F0F0F1',
			)
		);

		$this->add_control(
			'box_padding',
			array(
				'label'   => __( 'Box Padding (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 20,
				'min'     => 0,
				'max'     => 50,
			)
		);

		$this->add_control(
			'box_border_radius',
			array(
				'label'   => __( 'Border Radius (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 8,
				'min'     => 0,
				'max'     => 50,
			)
		);

		$this->add_control(
			'spacing',
			array(
				'label'   => __( 'Spacing (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 15,
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
		$settings     = $this->get_settings();
		$countdown_id = 'countdown-' . uniqid();

		// Calculate target timestamp
		if ( 'evergreen' === $settings['countdown_type'] ) {
			$target_time = time() + ( $settings['evergreen_days'] * DAY_IN_SECONDS ) + 
						   ( $settings['evergreen_hours'] * HOUR_IN_SECONDS ) + 
						   ( $settings['evergreen_minutes'] * MINUTE_IN_SECONDS );
		} else {
			$target_time = strtotime( $settings['due_date'] );
		}

		$countdown_data = array(
			'targetTime'    => $target_time,
			'expireAction'  => $settings['expire_action'],
			'expireMessage' => $settings['expire_message'],
			'redirectUrl'   => $settings['redirect_url'],
		);

		?>
		<div class="nexus-countdown layout-<?php echo esc_attr( $settings['layout'] ); ?>" 
			 id="<?php echo esc_attr( $countdown_id ); ?>"
			 data-countdown='<?php echo esc_attr( wp_json_encode( $countdown_data ) ); ?>'
			 style="text-align: <?php echo esc_attr( $settings['alignment'] ); ?>;">
			
			<div class="countdown-container" style="gap: <?php echo esc_attr( $settings['spacing'] ); ?>px;">
				<!-- Days -->
				<div class="countdown-box" 
					 style="background: <?php echo esc_attr( $settings['box_background'] ); ?>;
							padding: <?php echo esc_attr( $settings['box_padding'] ); ?>px;
							border-radius: <?php echo esc_attr( $settings['box_border_radius'] ); ?>px;">
					<div class="countdown-digit" 
						 style="color: <?php echo esc_attr( $settings['digit_color'] ); ?>;
								font-size: <?php echo esc_attr( $settings['digit_size'] ); ?>px;">
						<span data-unit="days">00</span>
					</div>
					<?php if ( $settings['show_labels'] ) : ?>
						<div class="countdown-label" 
							 style="color: <?php echo esc_attr( $settings['label_color'] ); ?>;
									font-size: <?php echo esc_attr( $settings['label_size'] ); ?>px;">
							<?php echo esc_html( $settings['label_days'] ); ?>
						</div>
					<?php endif; ?>
				</div>

				<!-- Hours -->
				<div class="countdown-box" 
					 style="background: <?php echo esc_attr( $settings['box_background'] ); ?>;
							padding: <?php echo esc_attr( $settings['box_padding'] ); ?>px;
							border-radius: <?php echo esc_attr( $settings['box_border_radius'] ); ?>px;">
					<div class="countdown-digit" 
						 style="color: <?php echo esc_attr( $settings['digit_color'] ); ?>;
								font-size: <?php echo esc_attr( $settings['digit_size'] ); ?>px;">
						<span data-unit="hours">00</span>
					</div>
					<?php if ( $settings['show_labels'] ) : ?>
						<div class="countdown-label" 
							 style="color: <?php echo esc_attr( $settings['label_color'] ); ?>;
									font-size: <?php echo esc_attr( $settings['label_size'] ); ?>px;">
							<?php echo esc_html( $settings['label_hours'] ); ?>
						</div>
					<?php endif; ?>
				</div>

				<!-- Minutes -->
				<div class="countdown-box" 
					 style="background: <?php echo esc_attr( $settings['box_background'] ); ?>;
							padding: <?php echo esc_attr( $settings['box_padding'] ); ?>px;
							border-radius: <?php echo esc_attr( $settings['box_border_radius'] ); ?>px;">
					<div class="countdown-digit" 
						 style="color: <?php echo esc_attr( $settings['digit_color'] ); ?>;
								font-size: <?php echo esc_attr( $settings['digit_size'] ); ?>px;">
						<span data-unit="minutes">00</span>
					</div>
					<?php if ( $settings['show_labels'] ) : ?>
						<div class="countdown-label" 
							 style="color: <?php echo esc_attr( $settings['label_color'] ); ?>;
									font-size: <?php echo esc_attr( $settings['label_size'] ); ?>px;">
							<?php echo esc_html( $settings['label_minutes'] ); ?>
						</div>
					<?php endif; ?>
				</div>

				<!-- Seconds -->
				<div class="countdown-box" 
					 style="background: <?php echo esc_attr( $settings['box_background'] ); ?>;
							padding: <?php echo esc_attr( $settings['box_padding'] ); ?>px;
							border-radius: <?php echo esc_attr( $settings['box_border_radius'] ); ?>px;">
					<div class="countdown-digit" 
						 style="color: <?php echo esc_attr( $settings['digit_color'] ); ?>;
								font-size: <?php echo esc_attr( $settings['digit_size'] ); ?>px;">
						<span data-unit="seconds">00</span>
					</div>
					<?php if ( $settings['show_labels'] ) : ?>
						<div class="countdown-label" 
							 style="color: <?php echo esc_attr( $settings['label_color'] ); ?>;
									font-size: <?php echo esc_attr( $settings['label_size'] ); ?>px;">
							<?php echo esc_html( $settings['label_seconds'] ); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Expire Message Placeholder -->
			<div class="countdown-expire-message" style="display: none;"></div>
		</div>
		<?php
	}
}
