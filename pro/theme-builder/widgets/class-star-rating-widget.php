<?php
/**
 * Star Rating Widget
 *
 * Display customizable star ratings
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Star Rating Widget Class
 */
class Nexus_Star_Rating_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'star-rating';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Star Rating', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-star-filled';
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
				'label' => __( 'Rating', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'rating',
			array(
				'label'   => __( 'Rating', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 5,
				'min'     => 0,
				'max'     => 5,
				'step'    => 0.1,
			)
		);

		$this->add_control(
			'max_rating',
			array(
				'label'   => __( 'Max Rating', 'nexus-pro' ),
				'type'    => 'number',
				'default' => 5,
				'min'     => 1,
				'max'     => 10,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => __( 'Title', 'nexus-pro' ),
				'type'        => 'text',
				'default'     => '',
				'placeholder' => __( 'Rating Title', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'show_value',
			array(
				'label'   => __( 'Show Rating Value', 'nexus-pro' ),
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
			'star_size',
			array(
				'label'   => __( 'Star Size', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 20,
				'min'     => 10,
				'max'     => 100,
			)
		);

		$this->add_control(
			'star_color',
			array(
				'label'   => __( 'Star Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#FFD700',
			)
		);

		$this->add_control(
			'empty_color',
			array(
				'label'   => __( 'Empty Star Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#CCCCCC',
			)
		);

		$this->add_control(
			'star_spacing',
			array(
				'label'   => __( 'Star Spacing', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 2,
				'min'     => 0,
				'max'     => 20,
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
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();

		$rating = floatval( $settings['rating'] );
		$max_rating = intval( $settings['max_rating'] );
		$full_stars = floor( $rating );
		$half_star = ( $rating - $full_stars ) >= 0.5;
		$empty_stars = $max_rating - $full_stars - ( $half_star ? 1 : 0 );

		$star_size = $settings['star_size'] . 'px';
		$star_spacing = $settings['star_spacing'] . 'px';

		?>
		<div class="nexus-star-rating" style="text-align: <?php echo esc_attr( $settings['alignment'] ); ?>;">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<div class="rating-title"><?php echo esc_html( $settings['title'] ); ?></div>
			<?php endif; ?>

			<div class="stars-container" style="display: inline-block;">
				<?php
				// Full stars
				for ( $i = 0; $i < $full_stars; $i++ ) :
					?>
					<span class="star star-full" 
						  style="color: <?php echo esc_attr( $settings['star_color'] ); ?>; 
								 font-size: <?php echo esc_attr( $star_size ); ?>;
								 margin-right: <?php echo esc_attr( $star_spacing ); ?>;">
						★
					</span>
				<?php endfor; ?>

				<?php
				// Half star
				if ( $half_star ) :
					?>
					<span class="star star-half" 
						  style="position: relative; 
								 font-size: <?php echo esc_attr( $star_size ); ?>;
								 margin-right: <?php echo esc_attr( $star_spacing ); ?>;">
						<span style="color: <?php echo esc_attr( $settings['empty_color'] ); ?>;">★</span>
						<span style="position: absolute; 
									 left: 0; 
									 top: 0; 
									 width: 50%; 
									 overflow: hidden; 
									 color: <?php echo esc_attr( $settings['star_color'] ); ?>;">★</span>
					</span>
				<?php endif; ?>

				<?php
				// Empty stars
				for ( $i = 0; $i < $empty_stars; $i++ ) :
					?>
					<span class="star star-empty" 
						  style="color: <?php echo esc_attr( $settings['empty_color'] ); ?>; 
								 font-size: <?php echo esc_attr( $star_size ); ?>;
								 margin-right: <?php echo esc_attr( $star_spacing ); ?>;">
						★
					</span>
				<?php endfor; ?>
			</div>

			<?php if ( $settings['show_value'] ) : ?>
				<span class="rating-value" style="margin-left: 10px;">
					<?php echo esc_html( number_format( $rating, 1 ) ); ?> / <?php echo esc_html( $max_rating ); ?>
				</span>
			<?php endif; ?>
		</div>
		<?php
	}
}
