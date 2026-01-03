<?php
/**
 * Toggle Widget
 *
 * Collapsible content sections
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Toggle Widget Class
 */
class Nexus_Toggle_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'toggle';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Toggle', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-arrow-down-alt2';
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
				'label' => __( 'Toggle', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'nexus-pro' ),
				'type'        => 'text',
				'default'     => __( 'Toggle Title', 'nexus-pro' ),
				'placeholder' => __( 'Enter title', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'content',
			array(
				'label'       => __( 'Content', 'nexus-pro' ),
				'type'        => 'textarea',
				'default'     => __( 'Toggle content goes here.', 'nexus-pro' ),
				'placeholder' => __( 'Enter content', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'open_by_default',
			array(
				'label'   => __( 'Open by Default', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => false,
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'   => __( 'Icon', 'nexus-pro' ),
				'type'    => 'icon',
				'default' => 'dashicons-plus',
			)
		);

		$this->add_control(
			'icon_active',
			array(
				'label'   => __( 'Active Icon', 'nexus-pro' ),
				'type'    => 'icon',
				'default' => 'dashicons-minus',
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
			'title_bg_color',
			array(
				'label'   => __( 'Title Background', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#f5f5f5',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'   => __( 'Title Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#333333',
			)
		);

		$this->add_control(
			'content_bg_color',
			array(
				'label'   => __( 'Content Background', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#ffffff',
			)
		);

		$this->add_control(
			'content_color',
			array(
				'label'   => __( 'Content Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#666666',
			)
		);

		$this->add_control(
			'border_width',
			array(
				'label'   => __( 'Border Width', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 1,
				'min'     => 0,
				'max'     => 10,
			)
		);

		$this->add_control(
			'border_color',
			array(
				'label'   => __( 'Border Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#dddddd',
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'   => __( 'Border Radius', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 5,
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
		$settings = $this->get_settings();
		$unique_id = 'toggle-' . uniqid();
		$is_open = $settings['open_by_default'];

		$border_width = $settings['border_width'] . 'px';
		$border_radius = $settings['border_radius'] . 'px';

		?>
		<div class="nexus-toggle" 
			 style="border: <?php echo esc_attr( $border_width ); ?> solid <?php echo esc_attr( $settings['border_color'] ); ?>; 
					border-radius: <?php echo esc_attr( $border_radius ); ?>; 
					overflow: hidden;">
			
			<div class="nexus-toggle-title" 
				 data-toggle="<?php echo esc_attr( $unique_id ); ?>"
				 style="background-color: <?php echo esc_attr( $settings['title_bg_color'] ); ?>; 
						color: <?php echo esc_attr( $settings['title_color'] ); ?>; 
						padding: 15px 20px; 
						cursor: pointer; 
						display: flex; 
						justify-content: space-between; 
						align-items: center;">
				
				<span><?php echo esc_html( $settings['title'] ); ?></span>
				
				<span class="nexus-toggle-icon dashicons <?php echo esc_attr( $is_open ? $settings['icon_active'] : $settings['icon'] ); ?>"></span>
			</div>
			
			<div id="<?php echo esc_attr( $unique_id ); ?>" 
				 class="nexus-toggle-content" 
				 style="background-color: <?php echo esc_attr( $settings['content_bg_color'] ); ?>; 
						color: <?php echo esc_attr( $settings['content_color'] ); ?>; 
						padding: 20px; 
						display: <?php echo $is_open ? 'block' : 'none'; ?>;">
				<?php echo wp_kses_post( wpautop( $settings['content'] ) ); ?>
			</div>
		</div>

		<script>
		jQuery(document).ready(function($) {
			$('[data-toggle="<?php echo esc_js( $unique_id ); ?>"]').on('click', function() {
				var content = $('#<?php echo esc_js( $unique_id ); ?>');
				var icon = $(this).find('.nexus-toggle-icon');
				
				content.slideToggle(300);
				
				if (content.is(':visible')) {
					icon.removeClass('<?php echo esc_js( $settings['icon'] ); ?>').addClass('<?php echo esc_js( $settings['icon_active'] ); ?>');
				} else {
					icon.removeClass('<?php echo esc_js( $settings['icon_active'] ); ?>').addClass('<?php echo esc_js( $settings['icon'] ); ?>');
				}
			});
		});
		</script>
		<?php
	}
}
