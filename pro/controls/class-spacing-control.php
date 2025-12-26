<?php
/**
 * Spacing Control - Padding and margin control
 *
 * @package Nexus_Pro
 * @subpackage Controls
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Spacing Control Class
 */
class Nexus_Spacing_Control extends WP_Customize_Control {

    /**
     * Control type
     *
     * @var string
     */
    public $type = 'nexus-spacing';

    /**
     * Default values
     *
     * @var array
     */
    public $defaults = array(
        'top'    => '0',
        'right'  => '0',
        'bottom' => '0',
        'left'   => '0',
        'linked' => true,
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

        <div class="nexus-spacing-control" data-control-id="<?php echo esc_attr( $this->id ); ?>">
            
            <!-- Link/Unlink Toggle -->
            <div class="spacing-link-toggle">
                <button type="button" class="spacing-link-btn <?php echo $settings['linked'] ? 'is-linked' : ''; ?>" data-setting="linked">
                    <span class="dashicons dashicons-admin-links"></span>
                    <span class="dashicons dashicons-editor-unlink"></span>
                </button>
            </div>

            <!-- Spacing Values -->
            <div class="spacing-values">
                
                <!-- Top -->
                <div class="spacing-field spacing-top">
                    <label class="spacing-label"><?php esc_html_e( 'Top', 'nexus-pro' ); ?></label>
                    <div class="control-field-input-group">
                        <input type="number" class="spacing-input" data-setting="top" value="<?php echo esc_attr( $settings['top'] ); ?>" min="0" step="1">
                        <span class="input-suffix">px</span>
                    </div>
                </div>

                <!-- Right -->
                <div class="spacing-field spacing-right">
                    <label class="spacing-label"><?php esc_html_e( 'Right', 'nexus-pro' ); ?></label>
                    <div class="control-field-input-group">
                        <input type="number" class="spacing-input" data-setting="right" value="<?php echo esc_attr( $settings['right'] ); ?>" min="0" step="1">
                        <span class="input-suffix">px</span>
                    </div>
                </div>

                <!-- Bottom -->
                <div class="spacing-field spacing-bottom">
                    <label class="spacing-label"><?php esc_html_e( 'Bottom', 'nexus-pro' ); ?></label>
                    <div class="control-field-input-group">
                        <input type="number" class="spacing-input" data-setting="bottom" value="<?php echo esc_attr( $settings['bottom'] ); ?>" min="0" step="1">
                        <span class="input-suffix">px</span>
                    </div>
                </div>

                <!-- Left -->
                <div class="spacing-field spacing-left">
                    <label class="spacing-label"><?php esc_html_e( 'Left', 'nexus-pro' ); ?></label>
                    <div class="control-field-input-group">
                        <input type="number" class="spacing-input" data-setting="left" value="<?php echo esc_attr( $settings['left'] ); ?>" min="0" step="1">
                        <span class="input-suffix">px</span>
                    </div>
                </div>

            </div>

            <!-- Visual Preview -->
            <div class="spacing-preview">
                <div class="spacing-preview-box">
                    <span class="spacing-preview-value spacing-preview-top"><?php echo esc_html( $settings['top'] ); ?></span>
                    <span class="spacing-preview-value spacing-preview-right"><?php echo esc_html( $settings['right'] ); ?></span>
                    <span class="spacing-preview-value spacing-preview-bottom"><?php echo esc_html( $settings['bottom'] ); ?></span>
                    <span class="spacing-preview-value spacing-preview-left"><?php echo esc_html( $settings['left'] ); ?></span>
                </div>
            </div>

            <input type="hidden" class="spacing-value" <?php $this->link(); ?> value="<?php echo esc_attr( $value ); ?>">
        </div>
        <?php
    }
}
