<?php
/**
 * Icon List Widget
 *
 * Display list with icons
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Icon List Widget Class
 */
class Nexus_Icon_List_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'icon-list';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Icon List', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-editor-ul';
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
				'label' => __( 'List Items', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'   => __( 'Items', 'nexus-pro' ),
				'type'    => 'repeater',
				'default' => array(
					array(
						'text' => __( 'List Item 1', 'nexus-pro' ),
						'icon' => 'dashicons-yes',
						'link' => '',
					),
					array(
						'text' => __( 'List Item 2', 'nexus-pro' ),
						'icon' => 'dashicons-yes',
						'link' => '',
					),
					array(
						'text' => __( 'List Item 3', 'nexus-pro' ),
						'icon' => 'dashicons-yes',
						'link' => '',
					),
				),
				'fields'  => array(
					array(
						'name'  => 'text',
						'label' => __( 'Text', 'nexus-pro' ),
						'type'  => 'text',
						'default' => __( 'List Item', 'nexus-pro' ),
					),
					array(
						'name'  => 'icon',
						'label' => __( 'Icon', 'nexus-pro' ),
						'type'  => 'icon',
						'default' => 'dashicons-yes',
					),
					array(
						'name'  => 'link',
						'label' => __( 'Link', 'nexus-pro' ),
						'type'  => 'url',
						'default' => '',
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
					'vertical'   => __( 'Vertical', 'nexus-pro' ),
					'horizontal' => __( 'Horizontal', 'nexus-pro' ),
				),
				'default' => 'vertical',
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'   => __( 'Icon Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#0066cc',
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'   => __( 'Icon Size', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 16,
				'min'     => 10,
				'max'     => 50,
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'   => __( 'Text Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#333333',
			)
		);

		$this->add_control(
			'text_size',
			array(
				'label'   => __( 'Text Size', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 16,
				'min'     => 12,
				'max'     => 30,
			)
		);

		$this->add_control(
			'spacing',
			array(
				'label'   => __( 'Spacing', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 10,
				'min'     => 0,
				'max'     => 50,
			)
		);

		$this->add_control(
			'icon_spacing',
			array(
				'label'   => __( 'Icon Spacing', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 10,
				'min'     => 0,
				'max'     => 30,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();
		$items = ! empty( $settings['items'] ) ? $settings['items'] : array();

		if ( empty( $items ) ) {
			return;
		}

		$is_horizontal = $settings['layout'] === 'horizontal';
		$spacing = $settings['spacing'] . 'px';
		$icon_spacing = $settings['icon_spacing'] . 'px';
		$icon_size = $settings['icon_size'] . 'px';
		$text_size = $settings['text_size'] . 'px';

		?>
		<ul class="nexus-icon-list nexus-icon-list-<?php echo esc_attr( $settings['layout'] ); ?>"
			style="list-style: none; 
				   padding: 0; 
				   margin: 0; 
				   display: flex; 
				   flex-direction: <?php echo $is_horizontal ? 'row' : 'column'; ?>;
				   gap: <?php echo esc_attr( $spacing ); ?>;">
			
			<?php foreach ( $items as $item ) : ?>
				<li class="nexus-icon-list-item" 
					style="display: flex; 
						   align-items: center; 
						   gap: <?php echo esc_attr( $icon_spacing ); ?>;">
					
					<span class="nexus-icon-list-icon dashicons <?php echo esc_attr( $item['icon'] ); ?>" 
						  style="color: <?php echo esc_attr( $settings['icon_color'] ); ?>; 
								 font-size: <?php echo esc_attr( $icon_size ); ?>; 
								 width: <?php echo esc_attr( $icon_size ); ?>; 
								 height: <?php echo esc_attr( $icon_size ); ?>;">
					</span>
					
					<?php if ( ! empty( $item['link'] ) ) : ?>
						<a href="<?php echo esc_url( $item['link'] ); ?>" 
						   class="nexus-icon-list-text"
						   style="color: <?php echo esc_attr( $settings['text_color'] ); ?>; 
								  font-size: <?php echo esc_attr( $text_size ); ?>; 
								  text-decoration: none;">
							<?php echo esc_html( $item['text'] ); ?>
						</a>
					<?php else : ?>
						<span class="nexus-icon-list-text" 
							  style="color: <?php echo esc_attr( $settings['text_color'] ); ?>; 
									 font-size: <?php echo esc_attr( $text_size ); ?>;">
							<?php echo esc_html( $item['text'] ); ?>
						</span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}
