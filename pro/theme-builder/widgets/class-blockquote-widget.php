<?php
/**
 * Blockquote Widget
 *
 * Display styled blockquotes with author attribution
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Blockquote Widget Class
 */
class Nexus_Blockquote_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'blockquote';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Blockquote', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-editor-quote';
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
				'label' => __( 'Blockquote', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'content',
			array(
				'label'       => __( 'Content', 'nexus-pro' ),
				'type'        => 'textarea',
				'default'     => __( 'The best way to predict the future is to invent it.', 'nexus-pro' ),
				'placeholder' => __( 'Enter quote', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'author',
			array(
				'label'       => __( 'Author', 'nexus-pro' ),
				'type'        => 'text',
				'default'     => __( 'Alan Kay', 'nexus-pro' ),
				'placeholder' => __( 'Enter author name', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'author_title',
			array(
				'label'       => __( 'Author Title', 'nexus-pro' ),
				'type'        => 'text',
				'default'     => __( 'Computer Scientist', 'nexus-pro' ),
				'placeholder' => __( 'Enter author title', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'author_image',
			array(
				'label' => __( 'Author Image', 'nexus-pro' ),
				'type'  => 'media',
			)
		);

		$this->add_control(
			'show_quote_icon',
			array(
				'label'   => __( 'Show Quote Icon', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
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
			'style',
			array(
				'label'   => __( 'Style', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'classic'  => __( 'Classic', 'nexus-pro' ),
					'modern'   => __( 'Modern', 'nexus-pro' ),
					'minimal'  => __( 'Minimal', 'nexus-pro' ),
					'bordered' => __( 'Bordered', 'nexus-pro' ),
				),
				'default' => 'classic',
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
			'quote_position',
			array(
				'label'   => __( 'Quote Icon Position', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'before'  => __( 'Before Text', 'nexus-pro' ),
					'after'   => __( 'After Text', 'nexus-pro' ),
					'corner'  => __( 'Corner', 'nexus-pro' ),
				),
				'default' => 'before',
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
			'content_color',
			array(
				'label'   => __( 'Content Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#1D2327',
			)
		);

		$this->add_control(
			'content_size',
			array(
				'label'   => __( 'Content Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 20,
				'min'     => 14,
				'max'     => 36,
			)
		);

		$this->add_control(
			'content_weight',
			array(
				'label'   => __( 'Content Weight', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'300' => __( 'Light', 'nexus-pro' ),
					'400' => __( 'Normal', 'nexus-pro' ),
					'500' => __( 'Medium', 'nexus-pro' ),
					'600' => __( 'Semi Bold', 'nexus-pro' ),
					'700' => __( 'Bold', 'nexus-pro' ),
				),
				'default' => '400',
			)
		);

		$this->add_control(
			'content_style',
			array(
				'label'   => __( 'Content Style', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'normal' => __( 'Normal', 'nexus-pro' ),
					'italic' => __( 'Italic', 'nexus-pro' ),
				),
				'default' => 'italic',
			)
		);

		$this->add_control(
			'author_color',
			array(
				'label'   => __( 'Author Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#2271B1',
			)
		);

		$this->add_control(
			'author_size',
			array(
				'label'   => __( 'Author Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 16,
				'min'     => 12,
				'max'     => 24,
			)
		);

		$this->add_control(
			'quote_icon_color',
			array(
				'label'   => __( 'Quote Icon Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#DCDCDE',
			)
		);

		$this->add_control(
			'quote_icon_size',
			array(
				'label'   => __( 'Quote Icon Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 60,
				'min'     => 30,
				'max'     => 150,
			)
		);

		$this->add_control(
			'background_color',
			array(
				'label'   => __( 'Background Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#F0F0F1',
			)
		);

		$this->add_control(
			'border_color',
			array(
				'label'   => __( 'Border Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#2271B1',
			)
		);

		$this->add_control(
			'border_width',
			array(
				'label'   => __( 'Border Width (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 4,
				'min'     => 0,
				'max'     => 20,
			)
		);

		$this->add_control(
			'padding',
			array(
				'label'   => __( 'Padding (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 30,
				'min'     => 0,
				'max'     => 80,
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'   => __( 'Author Image Size (px)', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 60,
				'min'     => 40,
				'max'     => 120,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();

		if ( empty( $settings['content'] ) ) {
			echo '<div class="nexus-blockquote-placeholder">' . esc_html__( 'Enter quote content', 'nexus-pro' ) . '</div>';
			return;
		}

		?>
		<div class="nexus-blockquote style-<?php echo esc_attr( $settings['style'] ); ?> quote-<?php echo esc_attr( $settings['quote_position'] ); ?>" 
			 style="text-align: <?php echo esc_attr( $settings['alignment'] ); ?>;
					background: <?php echo esc_attr( $settings['background_color'] ); ?>;
					padding: <?php echo esc_attr( $settings['padding'] ); ?>px;
					border-left: <?php echo esc_attr( $settings['border_width'] ); ?>px solid <?php echo esc_attr( $settings['border_color'] ); ?>;">
			
			<?php if ( $settings['show_quote_icon'] ) : ?>
				<div class="quote-icon" 
					 style="color: <?php echo esc_attr( $settings['quote_icon_color'] ); ?>;
							font-size: <?php echo esc_attr( $settings['quote_icon_size'] ); ?>px;">
					<span class="dashicons dashicons-editor-quote"></span>
				</div>
			<?php endif; ?>

			<blockquote>
				<p class="quote-content" 
				   style="color: <?php echo esc_attr( $settings['content_color'] ); ?>;
						  font-size: <?php echo esc_attr( $settings['content_size'] ); ?>px;
						  font-weight: <?php echo esc_attr( $settings['content_weight'] ); ?>;
						  font-style: <?php echo esc_attr( $settings['content_style'] ); ?>;">
					<?php echo esc_html( $settings['content'] ); ?>
				</p>

				<?php if ( ! empty( $settings['author'] ) || ! empty( $settings['author_image'] ) ) : ?>
					<footer class="quote-footer">
						<div class="author-info">
							<?php if ( ! empty( $settings['author_image'] ) ) : ?>
								<div class="author-image">
									<img src="<?php echo esc_url( $settings['author_image'] ); ?>" 
										 alt="<?php echo esc_attr( $settings['author'] ); ?>"
										 style="width: <?php echo esc_attr( $settings['image_size'] ); ?>px; 
												height: <?php echo esc_attr( $settings['image_size'] ); ?>px;">
								</div>
							<?php endif; ?>

							<div class="author-details">
								<?php if ( ! empty( $settings['author'] ) ) : ?>
									<cite class="author-name" 
										  style="color: <?php echo esc_attr( $settings['author_color'] ); ?>;
												 font-size: <?php echo esc_attr( $settings['author_size'] ); ?>px;">
										<?php echo esc_html( $settings['author'] ); ?>
									</cite>
								<?php endif; ?>

								<?php if ( ! empty( $settings['author_title'] ) ) : ?>
									<span class="author-title"><?php echo esc_html( $settings['author_title'] ); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</footer>
				<?php endif; ?>
			</blockquote>
		</div>
		<?php
	}
}
