<?php
/**
 * Mega Menu - Advanced navigation menu system
 *
 * @package Nexus_Pro
 * @subpackage Mega_Menu
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Mega Menu Main Class
 */
class Nexus_Mega_Menu {

    /**
     * Instance
     *
     * @var Nexus_Mega_Menu
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Nexus_Mega_Menu
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
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
        add_action( 'wp_update_nav_menu_item', array( $this, 'save_menu_item_settings' ), 10, 2 );
        add_filter( 'wp_edit_nav_menu_walker', array( $this, 'custom_menu_walker' ) );
        add_filter( 'wp_nav_menu_args', array( $this, 'custom_frontend_walker' ) );
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_admin_assets( $hook ) {
        if ( $hook !== 'nav-menus.php' ) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );

        wp_enqueue_style(
            'nexus-mega-menu-admin',
            NEXUS_PRO_URL . 'assets/css/mega-menu.css',
            array( 'wp-color-picker' ),
            '3.0.0'
        );

        wp_enqueue_script(
            'nexus-mega-menu-admin',
            NEXUS_PRO_URL . 'assets/js/mega-menu.js',
            array( 'jquery', 'wp-color-picker', 'jquery-ui-sortable' ),
            '3.0.0',
            true
        );

        wp_localize_script( 'nexus-mega-menu-admin', 'nexusMegaMenu', array(
            'i18n' => array(
                'enableMegaMenu'  => __( 'Enable Mega Menu', 'nexus-pro' ),
                'menuSettings'    => __( 'Mega Menu Settings', 'nexus-pro' ),
                'columns'         => __( 'Columns', 'nexus-pro' ),
                'icon'            => __( 'Icon', 'nexus-pro' ),
                'badge'           => __( 'Badge', 'nexus-pro' ),
                'badgeText'       => __( 'Badge Text', 'nexus-pro' ),
                'badgeColor'      => __( 'Badge Color', 'nexus-pro' ),
            ),
        ) );
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'nexus-mega-menu',
            NEXUS_PRO_URL . 'assets/css/mega-menu-frontend.css',
            array(),
            '3.0.0'
        );

        wp_enqueue_script(
            'nexus-mega-menu',
            NEXUS_PRO_URL . 'assets/js/mega-menu-frontend.js',
            array( 'jquery' ),
            '3.0.0',
            true
        );
    }

    /**
     * Save menu item custom settings
     *
     * @param int $menu_id Menu ID.
     * @param int $menu_item_db_id Menu item ID.
     */
    public function save_menu_item_settings( $menu_id, $menu_item_db_id ) {
        // Save mega menu enabled
        $mega_enabled = isset( $_POST['menu-item-nexus-mega'][ $menu_item_db_id ] ) ? 1 : 0;
        update_post_meta( $menu_item_db_id, '_nexus_mega_enabled', $mega_enabled );

        // Save mega menu columns
        if ( isset( $_POST['menu-item-nexus-columns'][ $menu_item_db_id ] ) ) {
            $columns = absint( $_POST['menu-item-nexus-columns'][ $menu_item_db_id ] );
            update_post_meta( $menu_item_db_id, '_nexus_mega_columns', $columns );
        }

        // Save widget area
        if ( isset( $_POST['menu-item-nexus-widget-area'][ $menu_item_db_id ] ) ) {
            $widget_area = sanitize_text_field( $_POST['menu-item-nexus-widget-area'][ $menu_item_db_id ] );
            update_post_meta( $menu_item_db_id, '_nexus_mega_widget_area', $widget_area );
        }

        // Save icon
        if ( isset( $_POST['menu-item-nexus-icon'][ $menu_item_db_id ] ) ) {
            $icon = sanitize_text_field( $_POST['menu-item-nexus-icon'][ $menu_item_db_id ] );
            update_post_meta( $menu_item_db_id, '_nexus_menu_icon', $icon );
        }

        // Save badge
        if ( isset( $_POST['menu-item-nexus-badge'][ $menu_item_db_id ] ) ) {
            $badge = sanitize_text_field( $_POST['menu-item-nexus-badge'][ $menu_item_db_id ] );
            update_post_meta( $menu_item_db_id, '_nexus_menu_badge', $badge );
        }

        // Save badge color
        if ( isset( $_POST['menu-item-nexus-badge-color'][ $menu_item_db_id ] ) ) {
            $badge_color = sanitize_hex_color( $_POST['menu-item-nexus-badge-color'][ $menu_item_db_id ] );
            update_post_meta( $menu_item_db_id, '_nexus_menu_badge_color', $badge_color );
        }

        // Save hide text
        $hide_text = isset( $_POST['menu-item-nexus-hide-text'][ $menu_item_db_id ] ) ? 1 : 0;
        update_post_meta( $menu_item_db_id, '_nexus_menu_hide_text', $hide_text );

        // Save disable link
        $disable_link = isset( $_POST['menu-item-nexus-disable-link'][ $menu_item_db_id ] ) ? 1 : 0;
        update_post_meta( $menu_item_db_id, '_nexus_menu_disable_link', $disable_link );
    }

    /**
     * Use custom walker for admin menu editor
     *
     * @param string $walker Walker class name.
     * @return string
     */
    public function custom_menu_walker( $walker ) {
        require_once NEXUS_PRO_PATH . 'mega-menu/class-menu-walker-edit.php';
        return 'Nexus_Menu_Walker_Edit';
    }

    /**
     * Use custom walker for frontend menu
     *
     * @param array $args Menu arguments.
     * @return array
     */
    public function custom_frontend_walker( $args ) {
        if ( ! isset( $args['walker'] ) || empty( $args['walker'] ) ) {
            require_once NEXUS_PRO_PATH . 'mega-menu/class-menu-walker-frontend.php';
            $args['walker'] = new Nexus_Menu_Walker_Frontend();
        }
        return $args;
    }

    /**
     * Check if menu item has mega menu enabled
     *
     * @param int $menu_item_id Menu item ID.
     * @return bool
     */
    public static function is_mega_menu( $menu_item_id ) {
        return (bool) get_post_meta( $menu_item_id, '_nexus_mega_enabled', true );
    }

    /**
     * Get mega menu columns
     *
     * @param int $menu_item_id Menu item ID.
     * @return int
     */
    public static function get_mega_columns( $menu_item_id ) {
        $columns = get_post_meta( $menu_item_id, '_nexus_mega_columns', true );
        return $columns ? absint( $columns ) : 4;
    }
}

// Initialize
Nexus_Mega_Menu::get_instance();
