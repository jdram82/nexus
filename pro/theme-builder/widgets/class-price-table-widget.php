<?php
/**
 * Price Table Widget
 *
 * Display pricing tables with multiple columns
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Price Table Widget Class
 */
class Nexus_Price_Table_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'price-table';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Price Table', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-table-col-before';
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
				'label' => __( 'Pricing Tables', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'tables',
			array(
				'label'   => __( 'Pricing Plans', 'nexus-pro' ),
				'type'    => 'repeater',
				'default' => array(
					array(
						'title'        => __( 'Basic', 'nexus-pro' ),
						'price'        => '$29',
						'period'       => '/month',
						'features'     => "5 Projects\n10GB Storage\nBasic Support\n1 User",
						'button_text'  => __( 'Get Started', 'nexus-pro' ),
						'button_link'  => '#',
						'featured'     => false,
						'ribbon_text'  => '',
					),
					array(
						'title'        => __( 'Pro', 'nexus-pro' ),
						'price'        => '$59',
						'period'       => '/month',
						'features'     => "Unlimited Projects\n100GB Storage\nPriority Support\n5 Users\nAPI Access",
						'button_text'  => __( 'Get Started', 'nexus-pro' ),
						'button_link'  => '#',
						'featured'     => true,
						'ribbon_text'  => __( 'Popular', 'nexus-pro' ),
					),
					array(
						'title'        => __( 'Enterprise', 'nexus-pro' ),
						'price'        => '$99',
						'period'       => '/month',
						'features'     => "Unlimited Everything\n500GB Storage\n24/7 Support\nUnlimited Users\nAPI Access\nWhite Label",
						'button_text'  => __( 'Contact Sales', 'nexus-pro' ),
						'button_link'  => '#',
						'featured'     => false,
						'ribbon_text'  => '',
					),
				),
				'fields'  => array(
					array(
						'name'        => 'title',
						'label'       => __( 'Title', 'nexus-pro' ),
						'type'        => 'text',
						'default'     => __( 'Plan Name', 'nexus-pro' ),
						'placeholder' => __( 'Enter plan title', 'nexus-pro' ),
					),
					array(
						'name'        => 'price',
						'label'       => __( 'Price', 'nexus-pro' ),
						'type'        => 'text',
						'default'     => '$99',
						'placeholder' => __( 'Enter price', 'nexus-pro' ),
					),
					array(
						'name'        => 'period',
						'label'       => __( 'Period', 'nexus-pro' ),
						'type'        => 'text',
						'default'     => '/month',
						'placeholder' => __( '/month, /year, etc.', 'nexus-pro' ),
					),
					array(
						'name'        => 'features',
						'label'       => __( 'Features', 'nexus-pro' ),
						'type'        => 'textarea',
						'default'     => "Feature 1\nFeature 2\nFeature 3",
						'placeholder' => __( 'One feature per line', 'nexus-pro' ),
						'description' => __( 'Enter each feature on a new line', 'nexus-pro' ),
					),
					array(
						'name'        => 'button_text',
						'label'       => __( 'Button Text', 'nexus-pro' ),
						'type'        => 'text',
						'default'     => __( 'Get Started', 'nexus-pro' ),
					),
					array(
						'name'        => 'button_link',
						'label'       => __( 'Button Link', 'nexus-pro' ),
						'type'        => 'url',
						'placeholder' => __( 'https://your-link.com', 'nexus-pro' ),
					),
					array(
						'name'    => 'featured',
						'label'   => __( 'Featured', 'nexus-pro' ),
						'type'    => 'checkbox',
						'default' => false,
					),
					array(
						'name'        => 'ribbon_text',
						'label'       => __( 'Ribbon Text', 'nexus-pro' ),
						'type'        => 'text',
						'placeholder' => __( 'Popular, Best Value, etc.', 'nexus-pro' ),
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
			'columns',
			array(
				'label'   => __( 'Columns', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'default' => '3',
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
			'gap',
			array(
				'label'   => __( 'Column Gap (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 30,
				'min'     => 0,
				'max'     => 100,
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
				'label'   => __( 'Border Radius (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 8,
				'min'     => 0,
				'max'     => 50,
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
			'price_color',
			array(
				'label'   => __( 'Price Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#2271B1',
			)
		);

		$this->add_control(
			'feature_color',
			array(
				'label'   => __( 'Feature Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#646970',
			)
		);

		$this->add_control(
			'background_color',
			array(
				'label'   => __( 'Background Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#FFFFFF',
			)
		);

		$this->add_control(
			'border_color',
			array(
				'label'   => __( 'Border Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#DCDCDE',
			)
		);

		$this->add_control(
			'featured_background',
			array(
				'label'   => __( 'Featured Background', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#F0F6FC',
			)
		);

		$this->add_control(
			'ribbon_background',
			array(
				'label'   => __( 'Ribbon Background', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#2271B1',
			)
		);

		$this->add_control(
			'ribbon_color',
			array(
				'label'   => __( 'Ribbon Text Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#FFFFFF',
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
			'button_color',
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

		if ( empty( $settings['tables'] ) ) {
			echo '<div class="nexus-price-table-placeholder">' . esc_html__( 'Add pricing plans', 'nexus-pro' ) . '</div>';
			return;
		}

		?>
		<div class="nexus-price-table columns-<?php echo esc_attr( $settings['columns'] ); ?>" 
			 style="gap: <?php echo esc_attr( $settings['gap'] ); ?>px;">
			
			<?php foreach ( $settings['tables'] as $index => $table ) : ?>
				<div class="price-table-column <?php echo $table['featured'] ? 'featured' : ''; ?>"
					 style="background: <?php echo esc_attr( $table['featured'] ? $settings['featured_background'] : $settings['background_color'] ); ?>;
							border: 1px solid <?php echo esc_attr( $settings['border_color'] ); ?>;
							border-radius: <?php echo esc_attr( $settings['border_radius'] ); ?>px;
							text-align: <?php echo esc_attr( $settings['alignment'] ); ?>;">
					
					<?php if ( $table['featured'] && ! empty( $table['ribbon_text'] ) ) : ?>
						<div class="price-ribbon" 
							 style="background: <?php echo esc_attr( $settings['ribbon_background'] ); ?>;
									color: <?php echo esc_attr( $settings['ribbon_color'] ); ?>;">
							<?php echo esc_html( $table['ribbon_text'] ); ?>
						</div>
					<?php endif; ?>

					<div class="table-header">
						<h3 class="table-title" 
							style="color: <?php echo esc_attr( $settings['title_color'] ); ?>;">
							<?php echo esc_html( $table['title'] ); ?>
						</h3>
						
						<div class="table-price" 
							 style="color: <?php echo esc_attr( $settings['price_color'] ); ?>;">
							<span class="price-amount"><?php echo esc_html( $table['price'] ); ?></span>
							<?php if ( ! empty( $table['period'] ) ) : ?>
								<span class="price-period"><?php echo esc_html( $table['period'] ); ?></span>
							<?php endif; ?>
						</div>
					</div>

					<div class="table-features">
						<ul style="color: <?php echo esc_attr( $settings['feature_color'] ); ?>;">
							<?php
							$features = array_filter( array_map( 'trim', explode( "\n", $table['features'] ) ) );
							foreach ( $features as $feature ) :
								?>
								<li>
									<span class="dashicons dashicons-yes-alt"></span>
									<?php echo esc_html( $feature ); ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

					<?php if ( ! empty( $table['button_text'] ) ) : ?>
						<div class="table-footer">
							<a href="<?php echo esc_url( $table['button_link'] ); ?>" 
							   class="price-button"
							   style="background: <?php echo esc_attr( $settings['button_background'] ); ?>;
									  color: <?php echo esc_attr( $settings['button_color'] ); ?>;">
								<?php echo esc_html( $table['button_text'] ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
