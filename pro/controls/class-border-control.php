<?php
/**
 * Border Control - Advanced border styling
 *
 * @package Nexus_Pro
 * @subpackage Controls
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Border Control Class
 */
class Nexus_Border_Control extends WP_Customize_Control {

    /**
     * Control type
     *
     * @var string
     */
    public $type = 'nexus-border';

    /**
     * Default values
     *
     * @var array
     */
    public $defaults = array(
        'width'  => '1',
        'style'  => 'solid',
        'color'  => '#dddddd',
        'radius' => '0',
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

        <div class="nexus-border-control" data-control-id="<?php echo esc_attr( $this->id ); ?>">
            
            <!-- Preview -->
            <div class="border-preview" style="border: <?php echo esc_attr( $settings['width'] ); ?>px <?php echo esc_attr( $settings['style'] ); ?> <?php echo esc_attr( $settings['color'] ); ?>; border-radius: <?php echo esc_attr( $settings['radius'] ); ?>px;"></div>

            <!-- Border Width -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Width', 'nexus-pro' ); ?></label>
                <div class="control-field-input-group">
                    <input type="range" class="border-width" data-setting="width" value="<?php echo esc_attr( $settings['width'] ); ?>" min="0" max="20" step="1">
                    <input type="number" class="border-width-number" value="<?php echo esc_attr( $settings['width'] ); ?>" min="0" max="20" step="1">
                    <span class="input-suffix">px</span>
                </div>
            </div>

            <!-- Border Style -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Style', 'nexus-pro' ); ?></label>
                <select class="border-style" data-setting="style">
                    <option value="solid" <?php selected( $settings['style'], 'solid' ); ?>><?php esc_html_e( 'Solid', 'nexus-pro' ); ?></option>
                    <option value="dashed" <?php selected( $settings['style'], 'dashed' ); ?>><?php esc_html_e( 'Dashed', 'nexus-pro' ); ?></option>
                    <option value="dotted" <?php selected( $settings['style'], 'dotted' ); ?>><?php esc_html_e( 'Dotted', 'nexus-pro' ); ?></option>
                    <option value="double" <?php selected( $settings['style'], 'double' ); ?>><?php esc_html_e( 'Double', 'nexus-pro' ); ?></option>
                    <option value="groove" <?php selected( $settings['style'], 'groove' ); ?>><?php esc_html_e( 'Groove', 'nexus-pro' ); ?></option>
                    <option value="ridge" <?php selected( $settings['style'], 'ridge' ); ?>><?php esc_html_e( 'Ridge', 'nexus-pro' ); ?></option>
                    <option value="inset" <?php selected( $settings['style'], 'inset' ); ?>><?php esc_html_e( 'Inset', 'nexus-pro' ); ?></option>
                    <option value="outset" <?php selected( $settings['style'], 'outset' ); ?>><?php esc_html_e( 'Outset', 'nexus-pro' ); ?></option>
                </select>
            </div>

            <!-- Border Color -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Color', 'nexus-pro' ); ?></label>
                <input type="text" class="border-color color-picker" data-setting="color" value="<?php echo esc_attr( $settings['color'] ); ?>">
            </div>

            <!-- Border Radius -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Radius', 'nexus-pro' ); ?></label>
                <div class="control-field-input-group">
                    <input type="range" class="border-radius" data-setting="radius" value="<?php echo esc_attr( $settings['radius'] ); ?>" min="0" max="100" step="1">
                    <input type="number" class="border-radius-number" value="<?php echo esc_attr( $settings['radius'] ); ?>" min="0" max="100" step="1">
                    <span class="input-suffix">px</span>
                </div>
            </div>

            <input type="hidden" class="border-value" <?php $this->link(); ?> value="<?php echo esc_attr( $value ); ?>">
        </div>
        <?php
    }
}
