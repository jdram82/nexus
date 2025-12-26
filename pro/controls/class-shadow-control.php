<?php
/**
 * Shadow Control - Box shadow builder
 *
 * @package Nexus_Pro
 * @subpackage Controls
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shadow Control Class
 */
class Nexus_Shadow_Control extends WP_Customize_Control {

    /**
     * Control type
     *
     * @var string
     */
    public $type = 'nexus-shadow';

    /**
     * Default values
     *
     * @var array
     */
    public $defaults = array(
        'horizontal' => '0',
        'vertical'   => '2',
        'blur'       => '8',
        'spread'     => '0',
        'color'      => 'rgba(0,0,0,0.1)',
        'inset'      => false,
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

        <div class="nexus-shadow-control" data-control-id="<?php echo esc_attr( $this->id ); ?>">
            
            <!-- Preview -->
            <div class="shadow-preview-box">
                <div class="shadow-preview" style="box-shadow: <?php echo esc_attr( $settings['inset'] ? 'inset ' : '' ); ?><?php echo esc_attr( $settings['horizontal'] ); ?>px <?php echo esc_attr( $settings['vertical'] ); ?>px <?php echo esc_attr( $settings['blur'] ); ?>px <?php echo esc_attr( $settings['spread'] ); ?>px <?php echo esc_attr( $settings['color'] ); ?>;"></div>
            </div>

            <!-- Horizontal Offset -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Horizontal', 'nexus-pro' ); ?></label>
                <div class="control-field-input-group">
                    <input type="range" class="shadow-horizontal" data-setting="horizontal" value="<?php echo esc_attr( $settings['horizontal'] ); ?>" min="-100" max="100" step="1">
                    <input type="number" class="shadow-horizontal-number" value="<?php echo esc_attr( $settings['horizontal'] ); ?>" min="-100" max="100" step="1">
                    <span class="input-suffix">px</span>
                </div>
            </div>

            <!-- Vertical Offset -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Vertical', 'nexus-pro' ); ?></label>
                <div class="control-field-input-group">
                    <input type="range" class="shadow-vertical" data-setting="vertical" value="<?php echo esc_attr( $settings['vertical'] ); ?>" min="-100" max="100" step="1">
                    <input type="number" class="shadow-vertical-number" value="<?php echo esc_attr( $settings['vertical'] ); ?>" min="-100" max="100" step="1">
                    <span class="input-suffix">px</span>
                </div>
            </div>

            <!-- Blur -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Blur', 'nexus-pro' ); ?></label>
                <div class="control-field-input-group">
                    <input type="range" class="shadow-blur" data-setting="blur" value="<?php echo esc_attr( $settings['blur'] ); ?>" min="0" max="100" step="1">
                    <input type="number" class="shadow-blur-number" value="<?php echo esc_attr( $settings['blur'] ); ?>" min="0" max="100" step="1">
                    <span class="input-suffix">px</span>
                </div>
            </div>

            <!-- Spread -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Spread', 'nexus-pro' ); ?></label>
                <div class="control-field-input-group">
                    <input type="range" class="shadow-spread" data-setting="spread" value="<?php echo esc_attr( $settings['spread'] ); ?>" min="-100" max="100" step="1">
                    <input type="number" class="shadow-spread-number" value="<?php echo esc_attr( $settings['spread'] ); ?>" min="-100" max="100" step="1">
                    <span class="input-suffix">px</span>
                </div>
            </div>

            <!-- Color -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Color', 'nexus-pro' ); ?></label>
                <input type="text" class="shadow-color color-picker" data-setting="color" value="<?php echo esc_attr( $settings['color'] ); ?>">
            </div>

            <!-- Inset -->
            <div class="control-field">
                <label class="control-field-checkbox">
                    <input type="checkbox" class="shadow-inset" data-setting="inset" <?php checked( $settings['inset'], true ); ?>>
                    <?php esc_html_e( 'Inset Shadow', 'nexus-pro' ); ?>
                </label>
            </div>

            <input type="hidden" class="shadow-value" <?php $this->link(); ?> value="<?php echo esc_attr( $value ); ?>">
        </div>
        <?php
    }
}
