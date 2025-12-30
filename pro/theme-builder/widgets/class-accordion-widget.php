<?php
/**
 * Accordion Widget
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Accordion Widget Class
 */
class Nexus_Accordion_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'accordion';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Accordion', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-editor-justify';
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
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'   => __( 'Accordion Items', 'nexus-pro' ),
				'type'    => 'repeater',
				'default' => array(
					array(
						'title'   => __( 'Accordion Item #1', 'nexus-pro' ),
						'content' => __( 'Content for accordion item #1', 'nexus-pro' ),
					),
					array(
						'title'   => __( 'Accordion Item #2', 'nexus-pro' ),
						'content' => __( 'Content for accordion item #2', 'nexus-pro' ),
					),
				),
				'fields'  => array(
					array(
						'name'  => 'title',
						'label' => __( 'Title', 'nexus-pro' ),
						'type'  => 'text',
					),
					array(
						'name'  => 'content',
						'label' => __( 'Content', 'nexus-pro' ),
						'type'  => 'textarea',
					),
				),
			)
		);

		$this->add_control(
			'allow_multiple_open',
			array(
				'label'   => __( 'Allow Multiple Items Open', 'nexus-pro' ),
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
			'title_color',
			array(
				'label'   => __( 'Title Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#333333',
			)
		);

		$this->add_control(
			'title_background',
			array(
				'label'   => __( 'Title Background', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#f5f5f5',
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
			'border_color',
			array(
				'label'   => __( 'Border Color', 'nexus-pro' ),
				'type'    => 'color',
				'default' => '#dddddd',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();
		$items    = $settings['items'];
		$multiple = $settings['allow_multiple_open'];

		if ( empty( $items ) ) {
			return;
		}

		?>
		<div class="nexus-accordion" data-allow-multiple="<?php echo esc_attr( $multiple ? '1' : '0' ); ?>">
			<?php foreach ( $items as $index => $item ) : ?>
				<div class="accordion-item">
					<div class="accordion-title" data-index="<?php echo esc_attr( $index ); ?>">
						<span><?php echo esc_html( $item['title'] ); ?></span>
						<span class="accordion-icon dashicons dashicons-arrow-down-alt2"></span>
					</div>
					<div class="accordion-content">
						<div class="accordion-content-inner">
							<?php echo wp_kses_post( $item['content'] ); ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<script>
		jQuery(document).ready(function($) {
			$('.nexus-accordion .accordion-title').on('click', function() {
				var $accordion = $(this).closest('.nexus-accordion');
				var $item = $(this).closest('.accordion-item');
				var allowMultiple = $accordion.data('allow-multiple');

				if (!allowMultiple) {
					$accordion.find('.accordion-item').not($item).removeClass('active');
					$accordion.find('.accordion-content').not($item.find('.accordion-content')).slideUp();
				}

				$item.toggleClass('active');
				$item.find('.accordion-content').slideToggle();
			});
		});
		</script>

		<style>
		.nexus-accordion .accordion-item {
			border: 1px solid <?php echo esc_attr( $settings['border_color'] ); ?>;
			margin-bottom: 10px;
		}
		.nexus-accordion .accordion-title {
			padding: 15px 20px;
			background: <?php echo esc_attr( $settings['title_background'] ); ?>;
			color: <?php echo esc_attr( $settings['title_color'] ); ?>;
			cursor: pointer;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
		.nexus-accordion .accordion-content {
			display: none;
			padding: 20px;
			color: <?php echo esc_attr( $settings['content_color'] ); ?>;
		}
		.nexus-accordion .accordion-item.active .accordion-icon {
			transform: rotate(180deg);
		}
		</style>
		<?php
	}
}
