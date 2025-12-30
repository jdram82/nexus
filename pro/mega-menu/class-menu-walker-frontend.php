<?php
/**
 * Menu Walker Frontend - Custom frontend menu walker
 *
 * @package Nexus_Pro
 * @subpackage Mega_Menu
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom Menu Walker for Frontend
 */
class Nexus_Menu_Walker_Frontend extends Walker_Nav_Menu {

    /**
     * Start level - output UL wrapper
     *
     * @param string $output Output HTML.
     * @param int    $depth  Depth of menu item.
     * @param array  $args   Menu item args.
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );

        // Check if parent has mega menu enabled
        $is_mega = false;
        $mega_columns = 4;

        if ( isset( $args->mega_menu_item_id ) ) {
            $is_mega = get_post_meta( $args->mega_menu_item_id, '_nexus_mega_enabled', true );
            $mega_columns = get_post_meta( $args->mega_menu_item_id, '_nexus_mega_columns', true );
            $mega_columns = $mega_columns ? $mega_columns : 4;
        }

        $classes = array( 'sub-menu' );

        if ( $depth === 0 && $is_mega ) {
            $classes[] = 'nexus-mega-menu';
            $classes[] = 'nexus-mega-columns-' . $mega_columns;
            
            // Check if widget area is enabled
            if ( isset( $args->mega_menu_item_id ) ) {
                $widget_area_id = get_post_meta( $args->mega_menu_item_id, '_nexus_mega_widget_area', true );
                
                if ( $widget_area_id && is_active_sidebar( 'nexus-mega-menu-' . $widget_area_id ) ) {
                    $args->mega_has_widget = true;
                    $args->mega_widget_id = $widget_area_id;
                }
            }
        }

        $class_names = implode( ' ', $classes );

        $output .= "\n{$indent}<ul class=\"{$class_names}\">\n";
    }

    /**
     * Start element - output menu item
     *
     * @param string $output Output HTML.
     * @param object $item   Menu item data object.
     * @param int    $depth  Depth of menu item.
     * @param array  $args   Menu item args.
     * @param int    $id     Current item ID.
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        // Get custom meta
        $mega_enabled   = get_post_meta( $item->ID, '_nexus_mega_enabled', true );
        $menu_icon      = get_post_meta( $item->ID, '_nexus_menu_icon', true );
        $menu_badge     = get_post_meta( $item->ID, '_nexus_menu_badge', true );
        $badge_color    = get_post_meta( $item->ID, '_nexus_menu_badge_color', true );
        $hide_text      = get_post_meta( $item->ID, '_nexus_menu_hide_text', true );
        $disable_link   = get_post_meta( $item->ID, '_nexus_menu_disable_link', true );

        // Classes
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        if ( $mega_enabled && $depth === 0 ) {
            $classes[] = 'nexus-has-mega-menu';
            // Store for child items
            $args->mega_menu_item_id = $item->ID;
        }

        if ( $menu_icon ) {
            $classes[] = 'nexus-has-icon';
        }

        if ( $menu_badge ) {
            $classes[] = 'nexus-has-badge';
        }

        if ( $hide_text ) {
            $classes[] = 'nexus-icon-only';
        }

        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        // ID
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        // Output <li>
        $output .= $indent . '<li' . $id . $class_names . '>';

        // Attributes
        $atts           = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target ) ? $item->target : '';
        $atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
        $atts['href']   = ! empty( $item->url ) ? $item->url : '';

        if ( $disable_link ) {
            $atts['href'] = '#';
            $atts['class'] = 'nexus-disabled-link';
        }

        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        // Build link content
        $item_output = isset( $args->before ) ? $args->before : '';

        $item_output .= '<a' . $attributes . '>';

        // Icon
        if ( $menu_icon ) {
            $item_output .= '<span class="nexus-menu-icon dashicons ' . esc_attr( $menu_icon ) . '"></span>';
        }

        // Text
        if ( ! $hide_text ) {
            $title = apply_filters( 'the_title', $item->title, $item->ID );
            $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

            $item_output .= '<span class="nexus-menu-text">' . $title . '</span>';
        }

        // Badge
        if ( $menu_badge ) {
            $badge_style = $badge_color ? ' style="background-color: ' . esc_attr( $badge_color ) . ';"' : '';
            $item_output .= '<span class="nexus-menu-badge"' . $badge_style . '>' . esc_html( $menu_badge ) . '</span>';
        }

        $item_output .= '</a>';
        $item_output .= isset( $args->after ) ? $args->after : '';

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    /**
     * End level - close UL wrapper and optionally render widget area
     *
     * @param string $output Output HTML.
     * @param int    $depth  Depth of menu item.
     * @param array  $args   Menu item args.
     */
    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );

        // Render widget area if enabled for mega menu
        if ( $depth === 0 && isset( $args->mega_has_widget ) && $args->mega_has_widget ) {
            $output .= "\n{$indent}\t<li class='nexus-mega-widget-area'>";
            
            ob_start();
            dynamic_sidebar( 'nexus-mega-menu-' . $args->mega_widget_id );
            $widget_output = ob_get_clean();
            
            $output .= $widget_output;
            $output .= "</li>\n";
            
            // Reset widget flag
            unset( $args->mega_has_widget );
            unset( $args->mega_widget_id );
        }

        $output .= "{$indent}</ul>\n";
    }
}
