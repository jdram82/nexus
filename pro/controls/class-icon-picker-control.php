<?php
/**
 * Icon Picker Control - Dashicons and custom icons
 *
 * @package Nexus_Pro
 * @subpackage Controls
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Icon Picker Control Class
 */
class Nexus_Icon_Picker_Control extends WP_Customize_Control {

    /**
     * Control type
     *
     * @var string
     */
    public $type = 'nexus-icon-picker';

    /**
     * Render control content
     */
    protected function render_content() {
        $value = $this->value();
        ?>
        <label class="nexus-control-label">
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <?php if ( ! empty( $this->description ) ) : ?>
                <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
            <?php endif; ?>
        </label>

        <div class="nexus-icon-picker-control" data-control-id="<?php echo esc_attr( $this->id ); ?>">
            
            <!-- Selected Icon Display -->
            <div class="selected-icon-display">
                <button type="button" class="select-icon-btn">
                    <?php if ( $value ) : ?>
                        <span class="dashicons <?php echo esc_attr( $value ); ?>"></span>
                    <?php else : ?>
                        <span class="placeholder"><?php esc_html_e( 'Select Icon', 'nexus-pro' ); ?></span>
                    <?php endif; ?>
                </button>
                <?php if ( $value ) : ?>
                    <button type="button" class="remove-icon-btn" title="<?php esc_attr_e( 'Remove Icon', 'nexus-pro' ); ?>">
                        <span class="dashicons dashicons-no-alt"></span>
                    </button>
                <?php endif; ?>
            </div>

            <!-- Icon Picker Modal -->
            <div class="icon-picker-modal" style="display: none;">
                <div class="icon-picker-header">
                    <input type="search" class="icon-search" placeholder="<?php esc_attr_e( 'Search icons...', 'nexus-pro' ); ?>">
                    <button type="button" class="close-modal-btn">
                        <span class="dashicons dashicons-no"></span>
                    </button>
                </div>
                <div class="icon-picker-content">
                    <div class="icon-grid">
                        <?php $this->render_dashicons(); ?>
                    </div>
                </div>
            </div>

            <input type="hidden" class="icon-value" <?php $this->link(); ?> value="<?php echo esc_attr( $value ); ?>">
        </div>
        <?php
    }

    /**
     * Render Dashicons
     */
    private function render_dashicons() {
        $dashicons = array(
            'admin-appearance', 'admin-generic', 'admin-tools', 'admin-settings',
            'admin-page', 'admin-post', 'admin-media', 'admin-links',
            'admin-users', 'admin-comments', 'star-filled', 'star-empty',
            'heart', 'location', 'location-alt', 'chart-line',
            'chart-bar', 'chart-pie', 'chart-area', 'analytics',
            'email', 'email-alt', 'phone', 'smartphone',
            'products', 'cart', 'store', 'money',
            'tickets', 'awards', 'businessman', 'groups',
            'calendar', 'calendar-alt', 'clock', 'backup',
            'lightbulb', 'info', 'warning', 'yes',
            'no', 'plus', 'minus', 'search',
            'download', 'upload', 'share', 'share-alt',
            'twitter', 'facebook', 'instagram', 'linkedin',
            'video-alt3', 'format-image', 'format-audio', 'format-video',
            'controls-play', 'controls-pause', 'controls-forward', 'controls-back',
            'shield', 'lock', 'unlock', 'visibility',
            'hidden', 'cloud', 'cloud-upload', 'cloud-saved',
            'database', 'code', 'editor-code', 'layout',
            'editor-alignleft', 'editor-aligncenter', 'editor-alignright', 'editor-bold',
            'editor-italic', 'editor-ul', 'editor-ol', 'editor-quote',
            'dashboard', 'portfolio', 'archive', 'category',
            'tag', 'networking', 'rss', 'menu',
            'menu-alt', 'menu-alt2', 'menu-alt3', 'grid-view',
            'list-view', 'table-col-after', 'table-row-after', 'arrow-left',
            'arrow-right', 'arrow-up', 'arrow-down', 'arrow-left-alt',
            'arrow-right-alt', 'arrow-up-alt', 'arrow-down-alt', 'sort',
            'move', 'dismiss', 'yes-alt', 'no-alt',
            'marker', 'flag', 'building', 'palmtree',
            'paperclip', 'portfolio', 'media-spreadsheet', 'media-document',
        );

        foreach ( $dashicons as $icon ) {
            echo '<button type="button" class="icon-option" data-icon="dashicons-' . esc_attr( $icon ) . '">';
            echo '<span class="dashicons dashicons-' . esc_attr( $icon ) . '"></span>';
            echo '</button>';
        }
    }
}
