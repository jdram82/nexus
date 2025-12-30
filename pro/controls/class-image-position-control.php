<?php
/**
 * Image Position Control - Focal point and positioning
 *
 * @package Nexus_Pro
 * @subpackage Controls
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Image Position Control Class
 */
class Nexus_Image_Position_Control extends WP_Customize_Control {

	/**
	 * Control type
	 *
	 * @var string
	 */
	public $type = 'nexus-image-position';

	/**
	 * Default values
	 *
	 * @var array
	 */
	public $defaults = array(
		'position-x' => '50',
		'position-y' => '50',
		'size'       => 'cover',
		'repeat'     => 'no-repeat',
		'attachment' => 'scroll',
	);

	/**
	 * Render control content
	 */
	protected function render_content() {
		$value    = $this->value();
		$settings = wp_parse_args( json_decode( $value, true ), $this->defaults );
		?>
		<label class="nexus-control-label">
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>
		</label>

		<div class="nexus-image-position-control" data-control-id="<?php echo esc_attr( $this->id ); ?>">
			
			<!-- Position Preview Box -->
			<div class="position-preview-box">
				<div class="position-grid">
					<?php for ( $i = 0; $i < 9; $i++ ) : ?>
						<div class="position-point" data-point="<?php echo esc_attr( $i ); ?>"></div>
					<?php endfor; ?>
					<div class="position-focal-point" style="left: <?php echo esc_attr( $settings['position-x'] ); ?>%; top: <?php echo esc_attr( $settings['position-y'] ); ?>%;"></div>
				</div>
				<p class="position-coordinates">
					<?php esc_html_e( 'Position:', 'nexus-pro' ); ?> 
					<span class="position-x-display"><?php echo esc_html( $settings['position-x'] ); ?>%</span>, 
					<span class="position-y-display"><?php echo esc_html( $settings['position-y'] ); ?>%</span>
				</p>
			</div>

			<!-- Position X -->
			<div class="control-field">
				<label class="control-field-label"><?php esc_html_e( 'Horizontal Position', 'nexus-pro' ); ?></label>
				<div class="control-field-input-group">
					<input type="range" class="position-x-slider" data-setting="position-x" value="<?php echo esc_attr( $settings['position-x'] ); ?>" min="0" max="100" step="1">
					<input type="number" class="position-x-number" value="<?php echo esc_attr( $settings['position-x'] ); ?>" min="0" max="100" step="1">
					<span class="input-suffix">%</span>
				</div>
			</div>

			<!-- Position Y -->
			<div class="control-field">
				<label class="control-field-label"><?php esc_html_e( 'Vertical Position', 'nexus-pro' ); ?></label>
				<div class="control-field-input-group">
					<input type="range" class="position-y-slider" data-setting="position-y" value="<?php echo esc_attr( $settings['position-y'] ); ?>" min="0" max="100" step="1">
					<input type="number" class="position-y-number" value="<?php echo esc_attr( $settings['position-y'] ); ?>" min="0" max="100" step="1">
					<span class="input-suffix">%</span>
				</div>
			</div>

			<!-- Quick Presets -->
			<div class="control-field">
				<label class="control-field-label"><?php esc_html_e( 'Quick Presets', 'nexus-pro' ); ?></label>
				<div class="position-presets">
					<button type="button" class="position-preset-btn" data-x="0" data-y="0" title="<?php esc_attr_e( 'Top Left', 'nexus-pro' ); ?>">
						<span class="dashicons dashicons-arrow-up-alt"></span>
					</button>
					<button type="button" class="position-preset-btn" data-x="50" data-y="0" title="<?php esc_attr_e( 'Top Center', 'nexus-pro' ); ?>">
						<span class="dashicons dashicons-arrow-up-alt"></span>
					</button>
					<button type="button" class="position-preset-btn" data-x="100" data-y="0" title="<?php esc_attr_e( 'Top Right', 'nexus-pro' ); ?>">
						<span class="dashicons dashicons-arrow-up-alt"></span>
					</button>
					<button type="button" class="position-preset-btn" data-x="0" data-y="50" title="<?php esc_attr_e( 'Center Left', 'nexus-pro' ); ?>">
						<span class="dashicons dashicons-arrow-left-alt"></span>
					</button>
					<button type="button" class="position-preset-btn" data-x="50" data-y="50" title="<?php esc_attr_e( 'Center', 'nexus-pro' ); ?>">
						<span class="dashicons dashicons-marker"></span>
					</button>
					<button type="button" class="position-preset-btn" data-x="100" data-y="50" title="<?php esc_attr_e( 'Center Right', 'nexus-pro' ); ?>">
						<span class="dashicons dashicons-arrow-right-alt"></span>
					</button>
					<button type="button" class="position-preset-btn" data-x="0" data-y="100" title="<?php esc_attr_e( 'Bottom Left', 'nexus-pro' ); ?>">
						<span class="dashicons dashicons-arrow-down-alt"></span>
					</button>
					<button type="button" class="position-preset-btn" data-x="50" data-y="100" title="<?php esc_attr_e( 'Bottom Center', 'nexus-pro' ); ?>">
						<span class="dashicons dashicons-arrow-down-alt"></span>
					</button>
					<button type="button" class="position-preset-btn" data-x="100" data-y="100" title="<?php esc_attr_e( 'Bottom Right', 'nexus-pro' ); ?>">
						<span class="dashicons dashicons-arrow-down-alt"></span>
					</button>
				</div>
			</div>

			<!-- Background Size -->
			<div class="control-field">
				<label class="control-field-label"><?php esc_html_e( 'Size', 'nexus-pro' ); ?></label>
				<select class="position-size" data-setting="size">
					<option value="auto" <?php selected( $settings['size'], 'auto' ); ?>><?php esc_html_e( 'Auto', 'nexus-pro' ); ?></option>
					<option value="cover" <?php selected( $settings['size'], 'cover' ); ?>><?php esc_html_e( 'Cover', 'nexus-pro' ); ?></option>
					<option value="contain" <?php selected( $settings['size'], 'contain' ); ?>><?php esc_html_e( 'Contain', 'nexus-pro' ); ?></option>
					<option value="100% 100%" <?php selected( $settings['size'], '100% 100%' ); ?>><?php esc_html_e( 'Fill', 'nexus-pro' ); ?></option>
				</select>
			</div>

			<!-- Background Repeat -->
			<div class="control-field">
				<label class="control-field-label"><?php esc_html_e( 'Repeat', 'nexus-pro' ); ?></label>
				<select class="position-repeat" data-setting="repeat">
					<option value="no-repeat" <?php selected( $settings['repeat'], 'no-repeat' ); ?>><?php esc_html_e( 'No Repeat', 'nexus-pro' ); ?></option>
					<option value="repeat" <?php selected( $settings['repeat'], 'repeat' ); ?>><?php esc_html_e( 'Repeat', 'nexus-pro' ); ?></option>
					<option value="repeat-x" <?php selected( $settings['repeat'], 'repeat-x' ); ?>><?php esc_html_e( 'Repeat X', 'nexus-pro' ); ?></option>
					<option value="repeat-y" <?php selected( $settings['repeat'], 'repeat-y' ); ?>><?php esc_html_e( 'Repeat Y', 'nexus-pro' ); ?></option>
					<option value="space" <?php selected( $settings['repeat'], 'space' ); ?>><?php esc_html_e( 'Space', 'nexus-pro' ); ?></option>
					<option value="round" <?php selected( $settings['repeat'], 'round' ); ?>><?php esc_html_e( 'Round', 'nexus-pro' ); ?></option>
				</select>
			</div>

			<!-- Background Attachment -->
			<div class="control-field">
				<label class="control-field-label"><?php esc_html_e( 'Attachment', 'nexus-pro' ); ?></label>
				<select class="position-attachment" data-setting="attachment">
					<option value="scroll" <?php selected( $settings['attachment'], 'scroll' ); ?>><?php esc_html_e( 'Scroll', 'nexus-pro' ); ?></option>
					<option value="fixed" <?php selected( $settings['attachment'], 'fixed' ); ?>><?php esc_html_e( 'Fixed', 'nexus-pro' ); ?></option>
					<option value="local" <?php selected( $settings['attachment'], 'local' ); ?>><?php esc_html_e( 'Local', 'nexus-pro' ); ?></option>
				</select>
			</div>

			<input type="hidden" class="position-value" <?php $this->link(); ?> value="<?php echo esc_attr( $value ); ?>">
		</div>
		<?php
	}

	/**
	 * Enqueue control scripts
	 */
	public function enqueue() {
		wp_enqueue_script( 'nexus-controls' );
		wp_enqueue_style( 'nexus-controls' );
	}
}
