<?php
/**
 * Typography Control - Advanced font styling
 *
 * @package Nexus_Pro
 * @subpackage Controls
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Typography Control Class
 */
class Nexus_Typography_Control extends WP_Customize_Control {

    /**
     * Control type
     *
     * @var string
     */
    public $type = 'nexus-typography';

    /**
     * Default values
     *
     * @var array
     */
    public $defaults = array(
        'font-family'    => '',
        'font-weight'    => '400',
        'font-style'     => 'normal',
        'font-size'      => '',
        'line-height'    => '',
        'letter-spacing' => '',
        'text-transform' => 'none',
    );

    /**
     * Enqueue control scripts
     */
    public function enqueue() {
        wp_enqueue_script( 'nexus-controls' );
        wp_enqueue_style( 'nexus-controls' );
    }

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

        <div class="nexus-typography-control" data-control-id="<?php echo esc_attr( $this->id ); ?>">
            
            <!-- Font Family -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Font Family', 'nexus-pro' ); ?></label>
                <select class="typography-font-family" data-setting="font-family">
                    <option value=""><?php esc_html_e( 'Default', 'nexus-pro' ); ?></option>
                    <optgroup label="<?php esc_attr_e( 'System Fonts', 'nexus-pro' ); ?>">
                        <option value="Arial" <?php selected( $settings['font-family'], 'Arial' ); ?>>Arial</option>
                        <option value="Helvetica" <?php selected( $settings['font-family'], 'Helvetica' ); ?>>Helvetica</option>
                        <option value="Georgia" <?php selected( $settings['font-family'], 'Georgia' ); ?>>Georgia</option>
                        <option value="Times New Roman" <?php selected( $settings['font-family'], 'Times New Roman' ); ?>>Times New Roman</option>
                        <option value="Courier New" <?php selected( $settings['font-family'], 'Courier New' ); ?>>Courier New</option>
                    </optgroup>
                    <optgroup label="<?php esc_attr_e( 'Google Fonts', 'nexus-pro' ); ?>">
                        <option value="Roboto" <?php selected( $settings['font-family'], 'Roboto' ); ?>>Roboto</option>
                        <option value="Open Sans" <?php selected( $settings['font-family'], 'Open Sans' ); ?>>Open Sans</option>
                        <option value="Lato" <?php selected( $settings['font-family'], 'Lato' ); ?>>Lato</option>
                        <option value="Montserrat" <?php selected( $settings['font-family'], 'Montserrat' ); ?>>Montserrat</option>
                        <option value="Poppins" <?php selected( $settings['font-family'], 'Poppins' ); ?>>Poppins</option>
                        <option value="Inter" <?php selected( $settings['font-family'], 'Inter' ); ?>>Inter</option>
                    </optgroup>
                </select>
            </div>

            <!-- Font Weight -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Font Weight', 'nexus-pro' ); ?></label>
                <select class="typography-font-weight" data-setting="font-weight">
                    <option value="100" <?php selected( $settings['font-weight'], '100' ); ?>>100 - Thin</option>
                    <option value="200" <?php selected( $settings['font-weight'], '200' ); ?>>200 - Extra Light</option>
                    <option value="300" <?php selected( $settings['font-weight'], '300' ); ?>>300 - Light</option>
                    <option value="400" <?php selected( $settings['font-weight'], '400' ); ?>>400 - Normal</option>
                    <option value="500" <?php selected( $settings['font-weight'], '500' ); ?>>500 - Medium</option>
                    <option value="600" <?php selected( $settings['font-weight'], '600' ); ?>>600 - Semi Bold</option>
                    <option value="700" <?php selected( $settings['font-weight'], '700' ); ?>>700 - Bold</option>
                    <option value="800" <?php selected( $settings['font-weight'], '800' ); ?>>800 - Extra Bold</option>
                    <option value="900" <?php selected( $settings['font-weight'], '900' ); ?>>900 - Black</option>
                </select>
            </div>

            <!-- Font Style -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Font Style', 'nexus-pro' ); ?></label>
                <select class="typography-font-style" data-setting="font-style">
                    <option value="normal" <?php selected( $settings['font-style'], 'normal' ); ?>><?php esc_html_e( 'Normal', 'nexus-pro' ); ?></option>
                    <option value="italic" <?php selected( $settings['font-style'], 'italic' ); ?>><?php esc_html_e( 'Italic', 'nexus-pro' ); ?></option>
                    <option value="oblique" <?php selected( $settings['font-style'], 'oblique' ); ?>><?php esc_html_e( 'Oblique', 'nexus-pro' ); ?></option>
                </select>
            </div>

            <!-- Font Size -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Font Size', 'nexus-pro' ); ?></label>
                <div class="control-field-input-group">
                    <input type="number" class="typography-font-size" data-setting="font-size" value="<?php echo esc_attr( $settings['font-size'] ); ?>" min="0" step="1">
                    <span class="input-suffix">px</span>
                </div>
            </div>

            <!-- Line Height -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Line Height', 'nexus-pro' ); ?></label>
                <input type="number" class="typography-line-height" data-setting="line-height" value="<?php echo esc_attr( $settings['line-height'] ); ?>" min="0" step="0.1">
            </div>

            <!-- Letter Spacing -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Letter Spacing', 'nexus-pro' ); ?></label>
                <div class="control-field-input-group">
                    <input type="number" class="typography-letter-spacing" data-setting="letter-spacing" value="<?php echo esc_attr( $settings['letter-spacing'] ); ?>" step="0.1">
                    <span class="input-suffix">px</span>
                </div>
            </div>

            <!-- Text Transform -->
            <div class="control-field">
                <label class="control-field-label"><?php esc_html_e( 'Text Transform', 'nexus-pro' ); ?></label>
                <select class="typography-text-transform" data-setting="text-transform">
                    <option value="none" <?php selected( $settings['text-transform'], 'none' ); ?>><?php esc_html_e( 'None', 'nexus-pro' ); ?></option>
                    <option value="uppercase" <?php selected( $settings['text-transform'], 'uppercase' ); ?>><?php esc_html_e( 'Uppercase', 'nexus-pro' ); ?></option>
                    <option value="lowercase" <?php selected( $settings['text-transform'], 'lowercase' ); ?>><?php esc_html_e( 'Lowercase', 'nexus-pro' ); ?></option>
                    <option value="capitalize" <?php selected( $settings['text-transform'], 'capitalize' ); ?>><?php esc_html_e( 'Capitalize', 'nexus-pro' ); ?></option>
                </select>
            </div>

            <input type="hidden" class="typography-value" <?php $this->link(); ?> value="<?php echo esc_attr( $value ); ?>">
        </div>
        <?php
    }
}
