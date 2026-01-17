<?php
/**
 * Price List Widget
 *
 * Display a list of products/services with prices
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Price List Widget Class
 */
class Nexus_Price_List_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'price-list';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Price List', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-list-view';
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
				'label' => __( 'Price Items', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'   => __( 'Items', 'nexus-pro' ),
				'type'    => 'repeater',
				'default' => array(
					array(
						'title'       => __( 'Basic Package', 'nexus-pro' ),
						'price'       => '$29',
						'description' => __( 'Perfect for small businesses', 'nexus-pro' ),
						'image'       => '',
					),
					array(
						'title'       => __( 'Premium Package', 'nexus-pro' ),
						'price'       => '$59',
						'description' => __( 'Best for growing companies', 'nexus-pro' ),
						'image'       => '',
					),
					array(
						'title'       => __( 'Enterprise Package', 'nexus-pro' ),
						'price'       => '$99',
						'description' => __( 'Complete solution for enterprises', 'nexus-pro' ),
						'image'       => '',
					),
				),
				'fields'  => array(
					array(
						'name'        => 'title',
						'label'       => __( 'Title', 'nexus-pro' ),
						'type'        => 'text',
						'default'     => __( 'Item Title', 'nexus-pro' ),
						'placeholder' => __( 'Enter title', 'nexus-pro' ),
					),
					array(
						'name'        => 'price',
						'label'       => __( 'Price', 'nexus-pro' ),
						'type'        => 'text',
						'default'     => '$99',
						'placeholder' => __( 'Enter price', 'nexus-pro' ),
					),
					array(
						'name'        => 'description',
						'label'       => __( 'Description', 'nexus-pro' ),
						'type'        => 'textarea',
						'default'     => __( 'Item description goes here', 'nexus-pro' ),
						'placeholder' => __( 'Enter description', 'nexus-pro' ),
					),
					array(
						'name'  => 'image',
						'label' => __( 'Image', 'nexus-pro' ),
						'type'  => 'media',
					),
					array(
						'name'        => 'link',
						'label'       => __( 'Link', 'nexus-pro' ),
						'type'        => 'url',
						'placeholder' => __( 'https://your-link.com', 'nexus-pro' ),
					),
				),
			)
		);

		$this->end_controls_section();

		// Layout section
		$this->start_controls_section(
			'layout_section',
			array(
				'label' => __( 'Layout', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'inline'  => __( 'Inline', 'nexus-pro' ),
					'stacked' => __( 'Stacked', 'nexus-pro' ),
				),
				'default' => 'inline',
			)
		);

		$this->add_control(
			'image_position',
			array(
				'label'   => __( 'Image Position', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'left'  => __( 'Left', 'nexus-pro' ),
					'right' => __( 'Right', 'nexus-pro' ),
					'top'   => __( 'Top', 'nexus-pro' ),
				),
				'default' => 'left',
			)
		);

		$this->add_control(
			'separator_style',
			array(
				'label'   => __( 'Separator Style', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'dotted' => __( 'Dotted', 'nexus-pro' ),
					'dashed' => __( 'Dashed', 'nexus-pro' ),
					'solid'  => __( 'Solid', 'nexus-pro' ),
					'none'   => __( 'None', 'nexus-pro' ),
				),
				'default' => 'dotted',
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
			'image_size',
			array(
				'label'   => __( 'Image Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 80,
				'min'     => 40,
				'max'     => 200,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'   => __( 'Title Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#1D2327',
			)
		);

		$this->add_control(
			'title_size',
			array(
				'label'   => __( 'Title Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 20,
				'min'     => 14,
				'max'     => 40,
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'   => __( 'Price Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#2271B1',
			)
		);

		$this->add_control(
			'price_size',
			array(
				'label'   => __( 'Price Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 22,
				'min'     => 14,
				'max'     => 40,
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
			'description_size',
			array(
				'label'   => __( 'Description Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 14,
				'min'     => 12,
				'max'     => 24,
			)
		);

		$this->add_control(
			'separator_color',
			array(
				'label'   => __( 'Separator Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#DCDCDE',
			)
		);

		$this->add_control(
			'item_spacing',
			array(
				'label'   => __( 'Item Spacing (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 20,
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

		if ( empty( $settings['items'] ) ) {
			echo '<div class="nexus-price-list-placeholder">' . esc_html__( 'Add price list items', 'nexus-pro' ) . '</div>';
			return;
		}

		?>
		<div class="nexus-price-list layout-<?php echo esc_attr( $settings['layout'] ); ?> image-<?php echo esc_attr( $settings['image_position'] ); ?>">
			<?php foreach ( $settings['items'] as $index => $item ) : ?>
				<div class="price-list-item" 
					 style="margin-bottom: <?php echo esc_attr( $settings['item_spacing'] ); ?>px;">
					
					<?php if ( ! empty( $item['image'] ) ) : ?>
						<div class="item-image">
							<img src="<?php echo esc_url( $item['image'] ); ?>" 
								 alt="<?php echo esc_attr( $item['title'] ); ?>"
								 style="width: <?php echo esc_attr( $settings['image_size'] ); ?>px; 
										height: <?php echo esc_attr( $settings['image_size'] ); ?>px;">
						</div>
					<?php endif; ?>

					<div class="item-content">
						<div class="item-header">
							<div class="item-title-wrapper">
								<?php if ( ! empty( $item['link'] ) ) : ?>
									<a href="<?php echo esc_url( $item['link'] ); ?>" class="item-title" 
									   style="color: <?php echo esc_attr( $settings['title_color'] ); ?>; 
											  font-size: <?php echo esc_attr( $settings['title_size'] ); ?>px;">
										<?php echo esc_html( $item['title'] ); ?>
									</a>
								<?php else : ?>
									<h3 class="item-title" 
										style="color: <?php echo esc_attr( $settings['title_color'] ); ?>; 
											   font-size: <?php echo esc_attr( $settings['title_size'] ); ?>px;">
										<?php echo esc_html( $item['title'] ); ?>
									</h3>
								<?php endif; ?>
							</div>

							<?php if ( 'none' !== $settings['separator_style'] ) : ?>
								<span class="item-separator" 
									  style="border-bottom-style: <?php echo esc_attr( $settings['separator_style'] ); ?>; 
											 border-bottom-color: <?php echo esc_attr( $settings['separator_color'] ); ?>;"></span>
							<?php endif; ?>

							<div class="item-price" 
								 style="color: <?php echo esc_attr( $settings['price_color'] ); ?>; 
										font-size: <?php echo esc_attr( $settings['price_size'] ); ?>px;">
								<?php echo esc_html( $item['price'] ); ?>
							</div>
						</div>

						<?php if ( ! empty( $item['description'] ) ) : ?>
							<div class="item-description" 
								 style="color: <?php echo esc_attr( $settings['description_color'] ); ?>; 
										font-size: <?php echo esc_attr( $settings['description_size'] ); ?>px;">
								<?php echo esc_html( $item['description'] ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
