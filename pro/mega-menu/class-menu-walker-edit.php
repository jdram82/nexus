<?php
/**
 * Menu Walker Edit - Custom admin menu walker
 *
 * @package Nexus_Pro
 * @subpackage Mega_Menu
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom Menu Walker for Admin Editor
 */
class Nexus_Menu_Walker_Edit extends Walker_Nav_Menu_Edit {

    /**
     * Start the element output
     *
     * @param string $output Output HTML.
     * @param object $item   Menu item data object.
     * @param int    $depth  Depth of menu item.
     * @param array  $args   Menu item args.
     * @param int    $id     Current item ID.
     */
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        parent::start_el( $output, $item, $depth, $args, $id );

        // Get custom meta
        $mega_enabled   = get_post_meta( $item->ID, '_nexus_mega_enabled', true );
        $mega_columns   = get_post_meta( $item->ID, '_nexus_mega_columns', true );
        $menu_icon      = get_post_meta( $item->ID, '_nexus_menu_icon', true );
        $menu_badge     = get_post_meta( $item->ID, '_nexus_menu_badge', true );
        $badge_color    = get_post_meta( $item->ID, '_nexus_menu_badge_color', true );
        $hide_text      = get_post_meta( $item->ID, '_nexus_menu_hide_text', true );
        $disable_link   = get_post_meta( $item->ID, '_nexus_menu_disable_link', true );

        // Default values
        $mega_columns  = $mega_columns ? $mega_columns : 4;
        $badge_color   = $badge_color ? $badge_color : '#e74c3c';

        // Custom fields HTML
        ob_start();
        ?>
        <div class="nexus-mega-menu-settings">
            <h4><?php esc_html_e( 'Nexus Mega Menu Settings', 'nexus-pro' ); ?></h4>

            <?php if ( $depth === 0 ) : ?>
                <p class="description description-wide nexus-mega-toggle">
                    <label>
                        <input type="checkbox" 
                               name="menu-item-nexus-mega[<?php echo esc_attr( $item->ID ); ?>]" 
                               value="1" 
                               <?php checked( $mega_enabled, 1 ); ?> />
                        <?php esc_html_e( 'Enable Mega Menu', 'nexus-pro' ); ?>
                    </label>
                </p>

                <p class="description description-wide nexus-mega-columns" style="<?php echo $mega_enabled ? '' : 'display:none;'; ?>">
                    <label for="edit-menu-item-nexus-columns-<?php echo esc_attr( $item->ID ); ?>">
                        <?php esc_html_e( 'Mega Menu Columns', 'nexus-pro' ); ?><br>
                        <select name="menu-item-nexus-columns[<?php echo esc_attr( $item->ID ); ?>]" 
                                id="edit-menu-item-nexus-columns-<?php echo esc_attr( $item->ID ); ?>">
                            <option value="2" <?php selected( $mega_columns, 2 ); ?>>2 <?php esc_html_e( 'Columns', 'nexus-pro' ); ?></option>
                            <option value="3" <?php selected( $mega_columns, 3 ); ?>>3 <?php esc_html_e( 'Columns', 'nexus-pro' ); ?></option>
                            <option value="4" <?php selected( $mega_columns, 4 ); ?>>4 <?php esc_html_e( 'Columns', 'nexus-pro' ); ?></option>
                            <option value="5" <?php selected( $mega_columns, 5 ); ?>>5 <?php esc_html_e( 'Columns', 'nexus-pro' ); ?></option>
                            <option value="6" <?php selected( $mega_columns, 6 ); ?>>6 <?php esc_html_e( 'Columns', 'nexus-pro' ); ?></option>
                        </select>
                    </label>
                </p>
            <?php endif; ?>

            <p class="description description-wide nexus-menu-icon-field">
                <label for="edit-menu-item-nexus-icon-<?php echo esc_attr( $item->ID ); ?>">
                    <?php esc_html_e( 'Icon (Dashicon class)', 'nexus-pro' ); ?><br>
                    <input type="text" 
                           name="menu-item-nexus-icon[<?php echo esc_attr( $item->ID ); ?>]" 
                           id="edit-menu-item-nexus-icon-<?php echo esc_attr( $item->ID ); ?>" 
                           value="<?php echo esc_attr( $menu_icon ); ?>" 
                           class="widefat" 
                           placeholder="dashicons-admin-home" />
                    <span class="description"><?php esc_html_e( 'Example: dashicons-admin-home', 'nexus-pro' ); ?></span>
                </label>
            </p>

            <p class="description description-thin nexus-menu-badge-field">
                <label for="edit-menu-item-nexus-badge-<?php echo esc_attr( $item->ID ); ?>">
                    <?php esc_html_e( 'Badge Text', 'nexus-pro' ); ?><br>
                    <input type="text" 
                           name="menu-item-nexus-badge[<?php echo esc_attr( $item->ID ); ?>]" 
                           id="edit-menu-item-nexus-badge-<?php echo esc_attr( $item->ID ); ?>" 
                           value="<?php echo esc_attr( $menu_badge ); ?>" 
                           class="widefat" 
                           placeholder="<?php esc_attr_e( 'New', 'nexus-pro' ); ?>" />
                </label>
            </p>

            <p class="description description-thin nexus-menu-badge-color-field">
                <label for="edit-menu-item-nexus-badge-color-<?php echo esc_attr( $item->ID ); ?>">
                    <?php esc_html_e( 'Badge Color', 'nexus-pro' ); ?><br>
                    <input type="text" 
                           name="menu-item-nexus-badge-color[<?php echo esc_attr( $item->ID ); ?>]" 
                           id="edit-menu-item-nexus-badge-color-<?php echo esc_attr( $item->ID ); ?>" 
                           value="<?php echo esc_attr( $badge_color ); ?>" 
                           class="nexus-color-picker" />
                </label>
            </p>

            <p class="description description-wide nexus-menu-options">
                <label>
                    <input type="checkbox" 
                           name="menu-item-nexus-hide-text[<?php echo esc_attr( $item->ID ); ?>]" 
                           value="1" 
                           <?php checked( $hide_text, 1 ); ?> />
                    <?php esc_html_e( 'Hide Text (Icon Only)', 'nexus-pro' ); ?>
                </label>
            </p>

            <p class="description description-wide nexus-menu-options">
                <label>
                    <input type="checkbox" 
                           name="menu-item-nexus-disable-link[<?php echo esc_attr( $item->ID ); ?>]" 
                           value="1" 
                           <?php checked( $disable_link, 1 ); ?> />
                    <?php esc_html_e( 'Disable Link (Heading Only)', 'nexus-pro' ); ?>
                </label>
            </p>
        </div>
        <?php
        $custom_fields = ob_get_clean();

        // Insert before description field
        $output = preg_replace(
            '/(<p[^>]+class="[^"]*field-move[^"]*")/i',
            $custom_fields . '$1',
            $output,
            1
        );
    }
}
