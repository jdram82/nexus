<?php
/**
 * Social Icons Widget
 *
 * Display social media icons
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Social Icons Widget Class
 */
class Nexus_Social_Icons_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'social-icons';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Social Icons', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-share';
	}

	/**
	 * Get widget categories
	 */
	public function get_categories() {
		return array( 'content' );
	}

	/**
	 * Get available social networks
	 *
	 * @return array
	 */
	private function get_social_networks() {
		return array(
			'facebook'   => __( 'Facebook', 'nexus-pro' ),
			'twitter'    => __( 'Twitter', 'nexus-pro' ),
			'instagram'  => __( 'Instagram', 'nexus-pro' ),
			'linkedin'   => __( 'LinkedIn', 'nexus-pro' ),
			'youtube'    => __( 'YouTube', 'nexus-pro' ),
			'pinterest'  => __( 'Pinterest', 'nexus-pro' ),
			'github'     => __( 'GitHub', 'nexus-pro' ),
			'tiktok'     => __( 'TikTok', 'nexus-pro' ),
			'whatsapp'   => __( 'WhatsApp', 'nexus-pro' ),
			'telegram'   => __( 'Telegram', 'nexus-pro' ),
			'reddit'     => __( 'Reddit', 'nexus-pro' ),
			'discord'    => __( 'Discord', 'nexus-pro' ),
		);
	}

	/**
	 * Register widget controls
	 */
	protected function register_controls() {
		// Content controls
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Social Icons', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'icons',
			array(
				'label'   => __( 'Social Icons', 'nexus-pro' ),
				'type'    => 'repeater',
				'default' => array(
					array(
						'network' => 'facebook',
						'url'     => 'https://facebook.com',
					),
					array(
						'network' => 'twitter',
						'url'     => 'https://twitter.com',
					),
					array(
						'network' => 'instagram',
						'url'     => 'https://instagram.com',
					),
				),
				'fields'  => array(
					array(
						'name'    => 'network',
						'label'   => __( 'Network', 'nexus-pro' ),
						'type'    => 'select',
						'options' => $this->get_social_networks(),
						'default' => 'facebook',
					),
					array(
						'name'        => 'url',
						'label'       => __( 'URL', 'nexus-pro' ),
						'type'        => 'url',
						'placeholder' => 'https://...',
						'default'     => '',
					),
				),
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
					'horizontal' => __( 'Horizontal', 'nexus-pro' ),
					'vertical'   => __( 'Vertical', 'nexus-pro' ),
				),
				'default' => 'horizontal',
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'   => __( 'Icon Size', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 20,
				'min'     => 10,
				'max'     => 60,
			)
		);

		$this->add_control(
			'icon_spacing',
			array(
				'label'   => __( 'Icon Spacing', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 10,
				'min'     => 0,
				'max'     => 50,
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'   => __( 'Icon Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#333333',
			)
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label'   => __( 'Icon Hover Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#0066cc',
			)
		);

		$this->add_control(
			'shape',
			array(
				'label'   => __( 'Shape', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'none'   => __( 'None', 'nexus-pro' ),
					'square' => __( 'Square', 'nexus-pro' ),
					'circle' => __( 'Circle', 'nexus-pro' ),
				),
				'default' => 'none',
			)
		);

		$this->add_control(
			'bg_color',
			array(
				'label'   => __( 'Background Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#f5f5f5',
			)
		);

		$this->add_control(
			'bg_hover_color',
			array(
				'label'   => __( 'Background Hover Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#0066cc',
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
				'default' => 'left',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get icon class for network
	 *
	 * @param string $network Network name
	 * @return string
	 */
	private function get_icon_class( $network ) {
		$icons = array(
			'facebook'   => 'dashicons-facebook',
			'twitter'    => 'dashicons-twitter',
			'instagram'  => 'dashicons-instagram',
			'linkedin'   => 'dashicons-linkedin',
			'youtube'    => 'dashicons-youtube',
			'pinterest'  => 'dashicons-pinterest',
			'github'     => 'dashicons-github',
			'tiktok'     => 'dashicons-video-alt3',
			'whatsapp'   => 'dashicons-whatsapp',
			'telegram'   => 'dashicons-email',
			'reddit'     => 'dashicons-reddit',
			'discord'    => 'dashicons-format-chat',
		);

		return $icons[ $network ] ?? 'dashicons-share';
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();
		$icons = ! empty( $settings['icons'] ) ? $settings['icons'] : array();

		if ( empty( $icons ) ) {
			return;
		}

		$icon_size = $settings['icon_size'] . 'px';
		$spacing = $settings['icon_spacing'] . 'px';
		$is_vertical = $settings['layout'] === 'vertical';
		$has_shape = $settings['shape'] !== 'none';
		$border_radius = $settings['shape'] === 'circle' ? '50%' : '0';
		
		$padding = $has_shape ? '10px' : '0';

		?>
		<div class="nexus-social-icons nexus-social-icons-<?php echo esc_attr( $settings['layout'] ); ?>"
			 style="display: flex; 
					flex-direction: <?php echo $is_vertical ? 'column' : 'row'; ?>; 
					gap: <?php echo esc_attr( $spacing ); ?>; 
					justify-content: <?php echo esc_attr( $settings['alignment'] ); ?>; 
					align-items: <?php echo esc_attr( $settings['alignment'] ); ?>;">
			
			<?php foreach ( $icons as $icon ) : ?>
				<?php if ( ! empty( $icon['url'] ) ) : ?>
					<a href="<?php echo esc_url( $icon['url'] ); ?>" 
					   class="nexus-social-icon nexus-social-<?php echo esc_attr( $icon['network'] ); ?>"
					   target="_blank"
					   rel="noopener noreferrer"
					   style="color: <?php echo esc_attr( $settings['icon_color'] ); ?>; 
							  background-color: <?php echo $has_shape ? esc_attr( $settings['bg_color'] ) : 'transparent'; ?>; 
							  padding: <?php echo esc_attr( $padding ); ?>; 
							  border-radius: <?php echo esc_attr( $border_radius ); ?>; 
							  display: inline-flex; 
							  align-items: center; 
							  justify-content: center; 
							  transition: all 0.3s;">
						
						<span class="dashicons <?php echo esc_attr( $this->get_icon_class( $icon['network'] ) ); ?>"
							  style="font-size: <?php echo esc_attr( $icon_size ); ?>; 
									 width: <?php echo esc_attr( $icon_size ); ?>; 
									 height: <?php echo esc_attr( $icon_size ); ?>;"></span>
					</a>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

		<style>
			.nexus-social-icon:hover {
				color: <?php echo esc_attr( $settings['icon_hover_color'] ); ?> !important;
				<?php if ( $has_shape ) : ?>
					background-color: <?php echo esc_attr( $settings['bg_hover_color'] ); ?> !important;
				<?php endif; ?>
			}
		</style>
		<?php
	}
}
