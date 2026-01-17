<?php
/**
 * Share Buttons Widget
 *
 * Display social media share buttons
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Share Buttons Widget Class
 */
class Nexus_Share_Buttons_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'share-buttons';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Share Buttons', 'nexus-pro' );
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
	 * Get available networks
	 */
	private function get_networks() {
		return array(
			'facebook'  => array(
				'title' => __( 'Facebook', 'nexus-pro' ),
				'icon'  => 'dashicons-facebook',
				'color' => '#1877F2',
			),
			'twitter'   => array(
				'title' => __( 'Twitter/X', 'nexus-pro' ),
				'icon'  => 'dashicons-twitter',
				'color' => '#1DA1F2',
			),
			'linkedin'  => array(
				'title' => __( 'LinkedIn', 'nexus-pro' ),
				'icon'  => 'dashicons-linkedin',
				'color' => '#0A66C2',
			),
			'pinterest' => array(
				'title' => __( 'Pinterest', 'nexus-pro' ),
				'icon'  => 'dashicons-pinterest',
				'color' => '#E60023',
			),
			'reddit'    => array(
				'title' => __( 'Reddit', 'nexus-pro' ),
				'icon'  => 'dashicons-reddit',
				'color' => '#FF4500',
			),
			'whatsapp'  => array(
				'title' => __( 'WhatsApp', 'nexus-pro' ),
				'icon'  => 'dashicons-whatsapp',
				'color' => '#25D366',
			),
			'telegram'  => array(
				'title' => __( 'Telegram', 'nexus-pro' ),
				'icon'  => 'dashicons-admin-comments',
				'color' => '#0088CC',
			),
			'email'     => array(
				'title' => __( 'Email', 'nexus-pro' ),
				'icon'  => 'dashicons-email',
				'color' => '#7F7F7F',
			),
			'print'     => array(
				'title' => __( 'Print', 'nexus-pro' ),
				'icon'  => 'dashicons-printer',
				'color' => '#666666',
			),
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
				'label' => __( 'Share Networks', 'nexus-pro' ),
			)
		);

		$networks = $this->get_networks();

		foreach ( $networks as $network_key => $network_data ) {
			$this->add_control(
				'show_' . $network_key,
				array(
					'label'   => $network_data['title'],
					'type'    => 'checkbox',
					'default' => in_array( $network_key, array( 'facebook', 'twitter', 'linkedin' ), true ),
				)
			);
		}

		$this->end_controls_section();

		// Settings section
		$this->start_controls_section(
			'settings_section',
			array(
				'label' => __( 'Settings', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'view',
			array(
				'label'   => __( 'View', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'icon-text' => __( 'Icon & Text', 'nexus-pro' ),
					'icon'      => __( 'Icon Only', 'nexus-pro' ),
					'text'      => __( 'Text Only', 'nexus-pro' ),
				),
				'default' => 'icon',
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

		$this->add_control(
			'share_url',
			array(
				'label'       => __( 'Share URL', 'nexus-pro' ),
				'type'        => 'select',
				'options'     => array(
					'current' => __( 'Current Page', 'nexus-pro' ),
					'custom'  => __( 'Custom URL', 'nexus-pro' ),
				),
				'default'     => 'current',
				'description' => __( 'URL to share', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'custom_url',
			array(
				'label'       => __( 'Custom URL', 'nexus-pro' ),
				'type'        => 'url',
				'placeholder' => __( 'https://your-link.com', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'show_counter',
			array(
				'label'   => __( 'Show Share Counter', 'nexus-pro' ),
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
			'button_style',
			array(
				'label'   => __( 'Button Style', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'flat'    => __( 'Flat', 'nexus-pro' ),
					'framed'  => __( 'Framed', 'nexus-pro' ),
					'minimal' => __( 'Minimal', 'nexus-pro' ),
					'rounded' => __( 'Rounded', 'nexus-pro' ),
				),
				'default' => 'flat',
			)
		);

		$this->add_control(
			'button_size',
			array(
				'label'   => __( 'Button Size', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'small'  => __( 'Small', 'nexus-pro' ),
					'medium' => __( 'Medium', 'nexus-pro' ),
					'large'  => __( 'Large', 'nexus-pro' ),
				),
				'default' => 'medium',
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'   => __( 'Icon Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 20,
				'min'     => 12,
				'max'     => 48,
			)
		);

		$this->add_control(
			'spacing',
			array(
				'label'   => __( 'Spacing (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 10,
				'min'     => 0,
				'max'     => 50,
			)
		);

		$this->add_control(
			'color_type',
			array(
				'label'   => __( 'Color Type', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'official' => __( 'Official Colors', 'nexus-pro' ),
					'custom'   => __( 'Custom Color', 'nexus-pro' ),
				),
				'default' => 'official',
			)
		);

		$this->add_control(
			'custom_color',
			array(
				'label'   => __( 'Custom Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#333333',
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'label'   => __( 'Hover Effect', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'none'   => __( 'None', 'nexus-pro' ),
					'fade'   => __( 'Fade', 'nexus-pro' ),
					'grow'   => __( 'Grow', 'nexus-pro' ),
					'shrink' => __( 'Shrink', 'nexus-pro' ),
				),
				'default' => 'grow',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get share URL
	 */
	private function get_share_url( $network, $url, $title = '' ) {
		$urls = array(
			'facebook'  => 'https://www.facebook.com/sharer.php?u=' . urlencode( $url ),
			'twitter'   => 'https://twitter.com/intent/tweet?url=' . urlencode( $url ) . '&text=' . urlencode( $title ),
			'linkedin'  => 'https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode( $url ) . '&title=' . urlencode( $title ),
			'pinterest' => 'https://pinterest.com/pin/create/button/?url=' . urlencode( $url ) . '&description=' . urlencode( $title ),
			'reddit'    => 'https://reddit.com/submit?url=' . urlencode( $url ) . '&title=' . urlencode( $title ),
			'whatsapp'  => 'https://api.whatsapp.com/send?text=' . urlencode( $title . ' ' . $url ),
			'telegram'  => 'https://t.me/share/url?url=' . urlencode( $url ) . '&text=' . urlencode( $title ),
			'email'     => 'mailto:?subject=' . urlencode( $title ) . '&body=' . urlencode( $url ),
			'print'     => 'javascript:window.print()',
		);

		return isset( $urls[ $network ] ) ? $urls[ $network ] : '#';
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();
		$networks = $this->get_networks();

		// Get share URL
		if ( 'custom' === $settings['share_url'] && ! empty( $settings['custom_url'] ) ) {
			$share_url = $settings['custom_url'];
		} else {
			global $wp;
			$share_url = home_url( add_query_arg( array(), $wp->request ) );
		}

		$share_title = get_the_title();

		// Get active networks
		$active_networks = array();
		foreach ( $networks as $network_key => $network_data ) {
			if ( ! empty( $settings[ 'show_' . $network_key ] ) ) {
				$active_networks[ $network_key ] = $network_data;
			}
		}

		if ( empty( $active_networks ) ) {
			echo '<div class="nexus-share-buttons-placeholder">' . esc_html__( 'Select networks to display', 'nexus-pro' ) . '</div>';
			return;
		}

		$button_classes = array(
			'style-' . $settings['button_style'],
			'size-' . $settings['button_size'],
			'hover-' . $settings['hover_effect'],
		);

		?>
		<div class="nexus-share-buttons layout-<?php echo esc_attr( $settings['layout'] ); ?> align-<?php echo esc_attr( $settings['alignment'] ); ?>" 
			 style="gap: <?php echo esc_attr( $settings['spacing'] ); ?>px;">
			
			<?php foreach ( $active_networks as $network_key => $network_data ) : ?>
				<?php
				$link_url = $this->get_share_url( $network_key, $share_url, $share_title );
				$button_color = 'official' === $settings['color_type'] ? $network_data['color'] : $settings['custom_color'];
				?>
				
				<a href="<?php echo esc_url( $link_url ); ?>" 
				   class="share-button <?php echo esc_attr( implode( ' ', $button_classes ) ); ?> network-<?php echo esc_attr( $network_key ); ?>"
				   target="_blank"
				   rel="noopener noreferrer"
				   data-network="<?php echo esc_attr( $network_key ); ?>"
				   style="--network-color: <?php echo esc_attr( $button_color ); ?>;">
					
					<?php if ( in_array( $settings['view'], array( 'icon', 'icon-text' ), true ) ) : ?>
						<span class="share-icon <?php echo esc_attr( $network_data['icon'] ); ?>" 
							  style="font-size: <?php echo esc_attr( $settings['icon_size'] ); ?>px;"></span>
					<?php endif; ?>

					<?php if ( in_array( $settings['view'], array( 'text', 'icon-text' ), true ) ) : ?>
						<span class="share-text"><?php echo esc_html( $network_data['title'] ); ?></span>
					<?php endif; ?>

					<?php if ( $settings['show_counter'] ) : ?>
						<span class="share-counter">0</span>
					<?php endif; ?>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
