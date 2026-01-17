<?php
/**
 * Progress Tracker Widget
 *
 * Display step-by-step progress indicator
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Progress Tracker Widget Class
 */
class Nexus_Progress_Tracker_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'progress-tracker';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Progress Tracker', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-arrow-right-alt';
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
				'label' => __( 'Steps', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'steps',
			array(
				'label'   => __( 'Progress Steps', 'nexus-pro' ),
				'type'    => 'repeater',
				'default' => array(
					array(
						'title'       => __( 'Step 1', 'nexus-pro' ),
						'description' => __( 'Complete registration', 'nexus-pro' ),
						'icon'        => 'dashicons-admin-users',
						'status'      => 'completed',
					),
					array(
						'title'       => __( 'Step 2', 'nexus-pro' ),
						'description' => __( 'Verify email address', 'nexus-pro' ),
						'icon'        => 'dashicons-email',
						'status'      => 'active',
					),
					array(
						'title'       => __( 'Step 3', 'nexus-pro' ),
						'description' => __( 'Setup profile', 'nexus-pro' ),
						'icon'        => 'dashicons-admin-settings',
						'status'      => 'pending',
					),
					array(
						'title'       => __( 'Step 4', 'nexus-pro' ),
						'description' => __( 'Start using', 'nexus-pro' ),
						'icon'        => 'dashicons-yes',
						'status'      => 'pending',
					),
				),
				'fields'  => array(
					array(
						'name'        => 'title',
						'label'       => __( 'Title', 'nexus-pro' ),
						'type'        => 'text',
						'default'     => __( 'Step Title', 'nexus-pro' ),
						'placeholder' => __( 'Enter title', 'nexus-pro' ),
					),
					array(
						'name'        => 'description',
						'label'       => __( 'Description', 'nexus-pro' ),
						'type'        => 'textarea',
						'placeholder' => __( 'Enter description', 'nexus-pro' ),
					),
					array(
						'name'    => 'icon',
						'label'   => __( 'Icon', 'nexus-pro' ),
						'type'    => 'icon',
						'default' => 'dashicons-marker',
					),
					array(
						'name'    => 'status',
						'label'   => __( 'Status', 'nexus-pro' ),
						'type'    => 'select',
						'options' => array(
							'completed' => __( 'Completed', 'nexus-pro' ),
							'active'    => __( 'Active', 'nexus-pro' ),
							'pending'   => __( 'Pending', 'nexus-pro' ),
						),
						'default' => 'pending',
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
			'layout',
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
			'style',
			array(
				'label'   => __( 'Style', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'default' => __( 'Default', 'nexus-pro' ),
					'minimal' => __( 'Minimal', 'nexus-pro' ),
					'modern'  => __( 'Modern', 'nexus-pro' ),
					'circle'  => __( 'Circle', 'nexus-pro' ),
				),
				'default' => 'default',
			)
		);

		$this->add_control(
			'show_numbers',
			array(
				'label'   => __( 'Show Numbers', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'show_icons',
			array(
				'label'   => __( 'Show Icons', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'show_connector',
			array(
				'label'   => __( 'Show Connector Line', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
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

		// Style controls
		$this->start_controls_section(
			'style_section',
			array(
				'label' => __( 'Style', 'nexus-pro' ),
				'tab'   => 'style',
			)
		);

		$this->add_control(
			'marker_size',
			array(
				'label'   => __( 'Marker Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 40,
				'min'     => 30,
				'max'     => 80,
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'   => __( 'Icon Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 20,
				'min'     => 14,
				'max'     => 40,
			)
		);

		$this->add_control(
			'title_size',
			array(
				'label'   => __( 'Title Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 16,
				'min'     => 12,
				'max'     => 24,
			)
		);

		$this->add_control(
			'description_size',
			array(
				'label'   => __( 'Description Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 14,
				'min'     => 12,
				'max'     => 18,
			)
		);

		$this->add_control(
			'completed_color',
			array(
				'label'   => __( 'Completed Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#00A32A',
			)
		);

		$this->add_control(
			'active_color',
			array(
				'label'   => __( 'Active Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#2271B1',
			)
		);

		$this->add_control(
			'pending_color',
			array(
				'label'   => __( 'Pending Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#DCDCDE',
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
			'description_color',
			array(
				'label'   => __( 'Description Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#646970',
			)
		);

		$this->add_control(
			'connector_width',
			array(
				'label'   => __( 'Connector Width (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 2,
				'min'     => 1,
				'max'     => 10,
			)
		);

		$this->add_control(
			'spacing',
			array(
				'label'   => __( 'Spacing (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 30,
				'min'     => 10,
				'max'     => 100,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get status color
	 */
	private function get_status_color( $status, $settings ) {
		$colors = array(
			'completed' => $settings['completed_color'],
			'active'    => $settings['active_color'],
			'pending'   => $settings['pending_color'],
		);

		return isset( $colors[ $status ] ) ? $colors[ $status ] : $colors['pending'];
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();

		if ( empty( $settings['steps'] ) ) {
			echo '<div class="nexus-progress-tracker-placeholder">' . esc_html__( 'Add progress steps', 'nexus-pro' ) . '</div>';
			return;
		}

		$tracker_id = 'progress-tracker-' . uniqid();

		?>
		<div class="nexus-progress-tracker layout-<?php echo esc_attr( $settings['layout'] ); ?> style-<?php echo esc_attr( $settings['style'] ); ?> align-<?php echo esc_attr( $settings['alignment'] ); ?>" 
			 id="<?php echo esc_attr( $tracker_id ); ?>"
			 style="gap: <?php echo esc_attr( $settings['spacing'] ); ?>px;">
			
			<?php foreach ( $settings['steps'] as $index => $step ) : ?>
				<?php
				$status_color = $this->get_status_color( $step['status'], $settings );
				$step_number  = $index + 1;
				?>

				<div class="progress-step status-<?php echo esc_attr( $step['status'] ); ?>" 
					 data-step="<?php echo esc_attr( $step_number ); ?>"
					 style="--status-color: <?php echo esc_attr( $status_color ); ?>;">
					
					<!-- Marker -->
					<div class="step-marker" 
						 style="width: <?php echo esc_attr( $settings['marker_size'] ); ?>px; 
								height: <?php echo esc_attr( $settings['marker_size'] ); ?>px;
								background: <?php echo esc_attr( $status_color ); ?>;">
						
						<?php if ( 'completed' === $step['status'] && $settings['show_icons'] ) : ?>
							<span class="dashicons dashicons-yes" 
								  style="font-size: <?php echo esc_attr( $settings['icon_size'] ); ?>px;"></span>
						
						<?php elseif ( $settings['show_icons'] && ! empty( $step['icon'] ) ) : ?>
							<span class="<?php echo esc_attr( $step['icon'] ); ?>" 
								  style="font-size: <?php echo esc_attr( $settings['icon_size'] ); ?>px;"></span>
						
						<?php elseif ( $settings['show_numbers'] ) : ?>
							<span class="step-number"><?php echo esc_html( $step_number ); ?></span>
						<?php endif; ?>
					</div>

					<!-- Connector Line -->
					<?php if ( $settings['show_connector'] && $index < count( $settings['steps'] ) - 1 ) : ?>
						<div class="step-connector" 
							 style="<?php echo 'horizontal' === $settings['layout'] ? 'width' : 'height'; ?>: 100%; 
									border-<?php echo 'horizontal' === $settings['layout'] ? 'bottom' : 'left'; ?>: <?php echo esc_attr( $settings['connector_width'] ); ?>px solid <?php echo esc_attr( $status_color ); ?>;"></div>
					<?php endif; ?>

					<!-- Content -->
					<div class="step-content">
						<?php if ( ! empty( $step['title'] ) ) : ?>
							<h4 class="step-title" 
								style="color: <?php echo esc_attr( $settings['text_color'] ); ?>;
									   font-size: <?php echo esc_attr( $settings['title_size'] ); ?>px;">
								<?php echo esc_html( $step['title'] ); ?>
							</h4>
						<?php endif; ?>

						<?php if ( ! empty( $step['description'] ) ) : ?>
							<p class="step-description" 
							   style="color: <?php echo esc_attr( $settings['description_color'] ); ?>;
									  font-size: <?php echo esc_attr( $settings['description_size'] ); ?>px;">
								<?php echo esc_html( $step['description'] ); ?>
							</p>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
