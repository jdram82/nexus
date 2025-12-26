<?php
/**
 * Gradient Control - Advanced gradient picker
 *
 * @package Nexus_Pro
 * @subpackage Controls
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Gradient Control Class
 */
class Nexus_Gradient_Control extends WP_Customize_Control {

    /**
     * Control type
     *
     * @var string
     */
    public $type = 'nexus-gradient';

    /**
     * Default values
     *
     * @var array
     */
    public $defaults = array(
        'type'       => 'linear',
        'angle'      => '135',
        'color1'     => '#667eea',
        'color1-pos' => '0',
        'color2'     => '#764ba2',
        'color2-pos' => '100',
    );

    /**
     * Render control content
     */
    protected function render_content() {
        $value = $this->value();
        $settings = wp_parse_args( json_decode( $value, true ), $this->defaults );
        ?>
        <label class="nexus-control-label">
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <?php if ( ! empty( $this->description ) ) : ?>
                <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
            <?php endif; ?>
        </label>

        <div class="nexus-gradient-control" data-control-id="<?php echo esc_attr( $this->id ); ?>">
            
            <!-- Preview -->
            <div class="gradient-preview" style="background: linear-gradient(<?php echo esc_attr( $settings['angle'] ); ?>deg, <?php echo esc_attr( $settings['color1'] ); ?> <?php echo esc_attr( $settings['color1-pos'] ); ?>%, <?php echo esc_attr( $settings['color2'] ); ?> <?php echo esc_attr( $settings['color2-pos'] ); ?>%);"></div>

            <!-- Gradient Type -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Type', 'nexus-pro' ); ?></label>
                <select class="gradient-type" data-setting="type">
                    <option value="linear" <?php selected( $settings['type'], 'linear' ); ?>><?php esc_html_e( 'Linear', 'nexus-pro' ); ?></option>
                    <option value="radial" <?php selected( $settings['type'], 'radial' ); ?>><?php esc_html_e( 'Radial', 'nexus-pro' ); ?></option>
                </select>
            </div>

            <!-- Angle (Linear only) -->
            <div class="control-field gradient-angle-field" style="<?php echo $settings['type'] === 'radial' ? 'display:none;' : ''; ?>">
                <label class="control-field-label"><?php esc_html_e( 'Angle', 'nexus-pro' ); ?></label>
                <div class="control-field-input-group">
                    <input type="range" class="gradient-angle" data-setting="angle" value="<?php echo esc_attr( $settings['angle'] ); ?>" min="0" max="360" step="1">
                    <input type="number" class="gradient-angle-number" value="<?php echo esc_attr( $settings['angle'] ); ?>" min="0" max="360" step="1">
                    <span class="input-suffix">°</span>
                </div>
            </div>

            <!-- Color 1 -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Color 1', 'nexus-pro' ); ?></label>
                <div class="control-field-color-group">
                    <input type="text" class="gradient-color1 color-picker" data-setting="color1" value="<?php echo esc_attr( $settings['color1'] ); ?>">
                    <div class="control-field-input-group">
                        <input type="range" class="gradient-color1-pos" data-setting="color1-pos" value="<?php echo esc_attr( $settings['color1-pos'] ); ?>" min="0" max="100" step="1">
                        <input type="number" class="gradient-color1-pos-number" value="<?php echo esc_attr( $settings['color1-pos'] ); ?>" min="0" max="100" step="1">
                        <span class="input-suffix">%</span>
                    </div>
                </div>
            </div>

            <!-- Color 2 -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Color 2', 'nexus-pro' ); ?></label>
                <div class="control-field-color-group">
                    <input type="text" class="gradient-color2 color-picker" data-setting="color2" value="<?php echo esc_attr( $settings['color2'] ); ?>">
                    <div class="control-field-input-group">
                        <input type="range" class="gradient-color2-pos" data-setting="color2-pos" value="<?php echo esc_attr( $settings['color2-pos'] ); ?>" min="0" max="100" step="1">
                        <input type="number" class="gradient-color2-pos-number" value="<?php echo esc_attr( $settings['color2-pos'] ); ?>" min="0" max="100" step="1">
                        <span class="input-suffix">%</span>
                    </div>
                </div>
            </div>

            <input type="hidden" class="gradient-value" <?php $this->link(); ?> value="<?php echo esc_attr( $value ); ?>">
        </div>
        <?php
    }
}
