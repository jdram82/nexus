<?php
/**
 * Popup Triggers - Manage When Popups Display
 *
 * @package Nexus_Pro
 * @subpackage Popup_Builder
 * @since 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Popup Triggers Class
 *
 * Handles various popup trigger conditions:
 * - Page load
 * - Scroll depth
 * - Exit intent
 * - Click/hover
 * - Time delay
 * - Inactivity
 */
class Nexus_Popup_Triggers {

	/**
	 * Instance
	 *
	 * @var Nexus_Popup_Triggers
	 */
	private static $instance = null;

	/**
	 * Available trigger types
	 *
	 * @var array
	 */
	private $trigger_types = array();

	/**
	 * Get instance
	 *
	 * @return Nexus_Popup_Triggers
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->register_trigger_types();
		$this->init_hooks();
	}

	/**
	 * Register available trigger types
	 */
	private function register_trigger_types() {
		$this->trigger_types = array(
			'page_load'   => array(
				'label'       => __( 'On Page Load', 'nexus-pro' ),
				'description' => __( 'Show popup immediately when page loads', 'nexus-pro' ),
				'settings'    => array(
					'delay' => array(
						'type'    => 'number',
						'label'   => __( 'Delay (seconds)', 'nexus-pro' ),
						'default' => 0,
						'min'     => 0,
						'max'     => 60,
					),
				),
			),
			'scroll'      => array(
				'label'       => __( 'On Scroll', 'nexus-pro' ),
				'description' => __( 'Show popup when user scrolls down the page', 'nexus-pro' ),
				'settings'    => array(
					'percentage' => array(
						'type'    => 'number',
						'label'   => __( 'Scroll Depth (%)', 'nexus-pro' ),
						'default' => 50,
						'min'     => 0,
						'max'     => 100,
					),
					'direction' => array(
						'type'    => 'select',
						'label'   => __( 'Direction', 'nexus-pro' ),
						'options' => array(
							'down' => __( 'Scrolling Down', 'nexus-pro' ),
							'up'   => __( 'Scrolling Up', 'nexus-pro' ),
						),
						'default' => 'down',
					),
				),
			),
			'exit_intent' => array(
				'label'       => __( 'Exit Intent', 'nexus-pro' ),
				'description' => __( 'Show popup when user is about to leave the page', 'nexus-pro' ),
				'settings'    => array(
					'sensitivity' => array(
						'type'    => 'select',
						'label'   => __( 'Sensitivity', 'nexus-pro' ),
						'options' => array(
							'low'    => __( 'Low', 'nexus-pro' ),
							'medium' => __( 'Medium', 'nexus-pro' ),
							'high'   => __( 'High', 'nexus-pro' ),
						),
						'default' => 'medium',
					),
					'mobile' => array(
						'type'    => 'checkbox',
						'label'   => __( 'Enable on Mobile', 'nexus-pro' ),
						'default' => false,
					),
				),
			),
			'click'       => array(
				'label'       => __( 'On Click', 'nexus-pro' ),
				'description' => __( 'Show popup when element is clicked', 'nexus-pro' ),
				'settings'    => array(
					'selector' => array(
						'type'        => 'text',
						'label'       => __( 'CSS Selector', 'nexus-pro' ),
						'placeholder' => '.popup-trigger, #my-button',
						'default'     => '',
					),
				),
			),
			'time_delay'  => array(
				'label'       => __( 'After Time', 'nexus-pro' ),
				'description' => __( 'Show popup after specified time on page', 'nexus-pro' ),
				'settings'    => array(
					'seconds' => array(
						'type'    => 'number',
						'label'   => __( 'Seconds', 'nexus-pro' ),
						'default' => 30,
						'min'     => 1,
						'max'     => 300,
					),
				),
			),
			'inactivity'  => array(
				'label'       => __( 'On Inactivity', 'nexus-pro' ),
				'description' => __( 'Show popup when user is inactive', 'nexus-pro' ),
				'settings'    => array(
					'seconds' => array(
						'type'    => 'number',
						'label'   => __( 'Inactivity Duration (seconds)', 'nexus-pro' ),
						'default' => 30,
						'min'     => 5,
						'max'     => 300,
					),
				),
			),
		);

		$this->trigger_types = apply_filters( 'nexus_popup_trigger_types', $this->trigger_types );
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		add_action( 'wp_ajax_nexus_track_popup_trigger', array( $this, 'track_trigger' ) );
		add_action( 'wp_ajax_nopriv_nexus_track_popup_trigger', array( $this, 'track_trigger' ) );
	}

	/**
	 * Get available trigger types
	 *
	 * @return array
	 */
	public function get_trigger_types() {
		return $this->trigger_types;
	}

	/**
	 * Get trigger settings for popup
	 *
	 * @param int $popup_id Popup ID
	 * @return array
	 */
	public function get_popup_triggers( $popup_id ) {
		$triggers = get_post_meta( $popup_id, '_nexus_popup_triggers', true );

		return wp_parse_args(
			$triggers,
			array(
				'type'     => 'page_load',
				'settings' => array(),
			)
		);
	}

	/**
	 * Validate trigger settings
	 *
	 * @param array $triggers Trigger settings
	 * @return bool
	 */
	public function validate_triggers( $triggers ) {
		if ( ! isset( $triggers['type'] ) || ! isset( $this->trigger_types[ $triggers['type'] ] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Track popup trigger via AJAX
	 */
	public function track_trigger() {
		check_ajax_referer( 'nexus_popup_frontend', 'nonce' );

		$popup_id = isset( $_POST['popup_id'] ) ? intval( $_POST['popup_id'] ) : 0;
		$trigger_type = isset( $_POST['trigger'] ) ? sanitize_text_field( $_POST['trigger'] ) : '';

		if ( ! $popup_id || ! $trigger_type ) {
			wp_send_json_error();
		}

		// Track trigger in analytics
		$this->increment_trigger_count( $popup_id, $trigger_type );

		wp_send_json_success();
	}

	/**
	 * Increment trigger count
	 *
	 * @param int    $popup_id Popup ID
	 * @param string $trigger  Trigger type
	 */
	private function increment_trigger_count( $popup_id, $trigger ) {
		$key = '_nexus_popup_trigger_' . $trigger;
		$count = get_post_meta( $popup_id, $key, true );
		$count = $count ? intval( $count ) + 1 : 1;
		update_post_meta( $popup_id, $key, $count );
	}

	/**
	 * Render trigger settings HTML
	 *
	 * @param int $popup_id Popup ID
	 */
	public function render_trigger_settings( $popup_id ) {
		$triggers = $this->get_popup_triggers( $popup_id );
		?>
		<div class="nexus-popup-triggers">
			<h3><?php esc_html_e( 'Display Triggers', 'nexus-pro' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'Choose when and how your popup should be displayed.', 'nexus-pro' ); ?>
			</p>

			<div class="trigger-selector">
				<label for="trigger-type"><?php esc_html_e( 'Trigger Type', 'nexus-pro' ); ?></label>
				<select id="trigger-type" name="nexus_popup_triggers[type]">
					<?php foreach ( $this->trigger_types as $type => $data ) : ?>
						<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $triggers['type'], $type ); ?>>
							<?php echo esc_html( $data['label'] ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p class="trigger-description"></p>
			</div>

			<?php foreach ( $this->trigger_types as $type => $data ) : ?>
				<div class="trigger-settings trigger-settings-<?php echo esc_attr( $type ); ?>" 
					 style="display: <?php echo $triggers['type'] === $type ? 'block' : 'none'; ?>;">
					<?php $this->render_trigger_specific_settings( $type, $data, $triggers ); ?>
				</div>
			<?php endforeach; ?>

			<div class="trigger-frequency">
				<h4><?php esc_html_e( 'Display Frequency', 'nexus-pro' ); ?></h4>
				
				<label>
					<input type="checkbox" 
						   name="nexus_popup_triggers[show_once]" 
						   value="1" 
						   <?php checked( ! empty( $triggers['show_once'] ) ); ?>>
					<?php esc_html_e( 'Show only once per user', 'nexus-pro' ); ?>
				</label>

				<div class="show-again-after">
					<label for="show-again">
						<?php esc_html_e( 'Show again after (days)', 'nexus-pro' ); ?>
					</label>
					<input type="number" 
						   id="show-again" 
						   name="nexus_popup_triggers[show_again_days]" 
						   value="<?php echo esc_attr( $triggers['show_again_days'] ?? 7 ); ?>" 
						   min="0" 
						   max="365">
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render trigger-specific settings
	 *
	 * @param string $type     Trigger type
	 * @param array  $data     Trigger data
	 * @param array  $triggers Current trigger settings
	 */
	private function render_trigger_specific_settings( $type, $data, $triggers ) {
		if ( empty( $data['settings'] ) ) {
			return;
		}

		foreach ( $data['settings'] as $key => $setting ) {
			$value = $triggers['settings'][ $key ] ?? $setting['default'];
			$name = "nexus_popup_triggers[settings][{$key}]";

			?>
			<div class="trigger-setting">
				<label for="trigger-setting-<?php echo esc_attr( $key ); ?>">
					<?php echo esc_html( $setting['label'] ); ?>
				</label>

				<?php
				switch ( $setting['type'] ) {
					case 'number':
						?>
						<input type="number" 
							   id="trigger-setting-<?php echo esc_attr( $key ); ?>" 
							   name="<?php echo esc_attr( $name ); ?>" 
							   value="<?php echo esc_attr( $value ); ?>" 
							   min="<?php echo esc_attr( $setting['min'] ?? 0 ); ?>" 
							   max="<?php echo esc_attr( $setting['max'] ?? 100 ); ?>">
						<?php
						break;

					case 'text':
						?>
						<input type="text" 
							   id="trigger-setting-<?php echo esc_attr( $key ); ?>" 
							   name="<?php echo esc_attr( $name ); ?>" 
							   value="<?php echo esc_attr( $value ); ?>" 
							   placeholder="<?php echo esc_attr( $setting['placeholder'] ?? '' ); ?>">
						<?php
						break;

					case 'select':
						?>
						<select id="trigger-setting-<?php echo esc_attr( $key ); ?>" 
								name="<?php echo esc_attr( $name ); ?>">
							<?php foreach ( $setting['options'] as $opt_value => $opt_label ) : ?>
								<option value="<?php echo esc_attr( $opt_value ); ?>" 
										<?php selected( $value, $opt_value ); ?>>
									<?php echo esc_html( $opt_label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<?php
						break;

					case 'checkbox':
						?>
						<label>
							<input type="checkbox" 
								   id="trigger-setting-<?php echo esc_attr( $key ); ?>" 
								   name="<?php echo esc_attr( $name ); ?>" 
								   value="1" 
								   <?php checked( $value ); ?>>
							<?php echo esc_html( $setting['label'] ); ?>
						</label>
						<?php
						break;
				}
				?>
			</div>
			<?php
		}
	}
}

// Initialize
Nexus_Popup_Triggers::get_instance();
