<?php
/**
 * Alert Widget
 *
 * Display notification/alert boxes
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Alert Widget Class
 */
class Nexus_Alert_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'alert';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Alert', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-warning';
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
				'label' => __( 'Alert', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'alert_type',
			array(
				'label'   => __( 'Type', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'info'    => __( 'Info', 'nexus-pro' ),
					'success' => __( 'Success', 'nexus-pro' ),
					'warning' => __( 'Warning', 'nexus-pro' ),
					'error'   => __( 'Error', 'nexus-pro' ),
				),
				'default' => 'info',
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'nexus-pro' ),
				'type'        => 'text',
				'default'     => __( 'Alert Title', 'nexus-pro' ),
				'placeholder' => __( 'Enter title', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'       => __( 'Description', 'nexus-pro' ),
				'type'        => 'textarea',
				'default'     => __( 'This is an important message for your users.', 'nexus-pro' ),
				'placeholder' => __( 'Enter description', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'show_icon',
			array(
				'label'   => __( 'Show Icon', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'custom_icon',
			array(
				'label'   => __( 'Custom Icon', 'nexus-pro' ),
				'type'    => 'text',
				'default' => '',
				'placeholder' => 'dashicons-info',
			)
		);

		$this->add_control(
			'dismissible',
			array(
				'label'   => __( 'Dismissible', 'nexus-pro' ),
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
			'padding',
			array(
				'label'   => __( 'Padding', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 20,
				'min'     => 10,
				'max'     => 50,
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'   => __( 'Icon Size', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 24,
				'min'     => 16,
				'max'     => 48,
			)
		);

		$this->add_control(
			'custom_bg_color',
			array(
				'label'   => __( 'Custom Background', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '',
			)
		);

		$this->add_control(
			'custom_text_color',
			array(
				'label'   => __( 'Custom Text Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '',
			)
		);

		$this->add_control(
			'custom_border_color',
			array(
				'label'   => __( 'Custom Border Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get default icon for alert type
	 *
	 * @param string $type Alert type.
	 * @return string
	 */
	private function get_default_icon( $type ) {
		$icons = array(
			'info'    => 'dashicons-info',
			'success' => 'dashicons-yes-alt',
			'warning' => 'dashicons-warning',
			'error'   => 'dashicons-dismiss',
		);

		return isset( $icons[ $type ] ) ? $icons[ $type ] : 'dashicons-info';
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();

		$alert_type = $settings['alert_type'];
		$icon = ! empty( $settings['custom_icon'] ) ? $settings['custom_icon'] : $this->get_default_icon( $alert_type );
		$alert_id = 'alert-' . uniqid();

		$inline_styles = array();
		if ( ! empty( $settings['custom_bg_color'] ) ) {
			$inline_styles[] = 'background-color: ' . esc_attr( $settings['custom_bg_color'] );
		}
		if ( ! empty( $settings['custom_text_color'] ) ) {
			$inline_styles[] = 'color: ' . esc_attr( $settings['custom_text_color'] );
		}
		if ( ! empty( $settings['custom_border_color'] ) ) {
			$inline_styles[] = 'border-color: ' . esc_attr( $settings['custom_border_color'] );
		}
		$inline_styles[] = 'border-radius: ' . esc_attr( $settings['border_radius'] ) . 'px';
		$inline_styles[] = 'padding: ' . esc_attr( $settings['padding'] ) . 'px';

		$style_attr = ! empty( $inline_styles ) ? 'style="' . implode( '; ', $inline_styles ) . '"' : '';

		?>
		<div class="nexus-alert alert-<?php echo esc_attr( $alert_type ); ?> <?php echo $settings['dismissible'] ? 'dismissible' : ''; ?>" 
			 id="<?php echo esc_attr( $alert_id ); ?>"
			 role="alert"
			 <?php echo $style_attr; ?>>
			
			<?php if ( $settings['show_icon'] ) : ?>
				<div class="alert-icon" style="font-size: <?php echo esc_attr( $settings['icon_size'] ); ?>px;">
					<span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
				</div>
			<?php endif; ?>

			<div class="alert-content">
				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<div class="alert-title"><?php echo esc_html( $settings['title'] ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $settings['description'] ) ) : ?>
					<div class="alert-description"><?php echo wp_kses_post( wpautop( $settings['description'] ) ); ?></div>
				<?php endif; ?>
			</div>

			<?php if ( $settings['dismissible'] ) : ?>
				<button class="alert-dismiss" 
						onclick="document.getElementById('<?php echo esc_js( $alert_id ); ?>').remove();"
						aria-label="<?php esc_attr_e( 'Dismiss alert', 'nexus-pro' ); ?>">
					<span class="dashicons dashicons-no-alt"></span>
				</button>
			<?php endif; ?>
		</div>
		<?php
	}
}
