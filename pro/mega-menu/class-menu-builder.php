<?php
/**
 * Menu Builder - Visual menu builder with drag-drop
 *
 * @package Nexus_Pro
 * @subpackage Mega_Menu
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Menu Builder Class
 *
 * Provides visual drag-drop interface for building menus
 * with widget areas and live preview
 */
class Nexus_Menu_Builder {

    /**
     * Instance
     *
     * @var Nexus_Menu_Builder
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Nexus_Menu_Builder
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
        add_action( 'admin_menu', array( $this, 'add_builder_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_builder_assets' ) );
        add_action( 'wp_ajax_nexus_save_menu_builder', array( $this, 'save_menu_builder' ) );
        add_action( 'wp_ajax_nexus_load_menu_builder', array( $this, 'load_menu_builder' ) );
        add_action( 'wp_ajax_nexus_add_widget_area', array( $this, 'add_widget_area' ) );
        add_action( 'wp_ajax_nexus_remove_widget_area', array( $this, 'remove_widget_area' ) );

        // Register widget areas
        add_action( 'widgets_init', array( $this, 'register_widget_areas' ) );
    }

    /**
     * Add menu builder page
     */
    public function add_builder_page() {
        add_submenu_page(
            'themes.php',
            __( 'Menu Builder', 'nexus-pro' ),
            __( 'Menu Builder', 'nexus-pro' ),
            'edit_theme_options',
            'nexus-menu-builder',
            array( $this, 'render_builder_page' )
        );
    }

    /**
     * Enqueue builder assets
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_builder_assets( $hook ) {
        if ( $hook !== 'appearance_page_nexus-menu-builder' ) {
            return;
        }

        // Enqueue WordPress color picker
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_media();

        // jQuery UI
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'jquery-ui-draggable' );
        wp_enqueue_script( 'jquery-ui-droppable' );

        // Builder styles
        wp_enqueue_style(
            'nexus-menu-builder',
            NEXUS_PRO_URL . 'assets/css/menu-builder.css',
            array( 'wp-color-picker' ),
            '3.0.0'
        );

        // Builder script
        wp_enqueue_script(
            'nexus-menu-builder',
            NEXUS_PRO_URL . 'assets/js/menu-builder.js',
            array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'wp-color-picker' ),
            '3.0.0',
            true
        );

        wp_localize_script( 'nexus-menu-builder', 'nexusMenuBuilder', array(
            'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
            'nonce'       => wp_create_nonce( 'nexus-menu-builder' ),
            'menus'       => $this->get_menus(),
            'i18n'        => array(
                'saveSuccess'       => __( 'Menu saved successfully!', 'nexus-pro' ),
                'saveError'         => __( 'Error saving menu.', 'nexus-pro' ),
                'confirmDelete'     => __( 'Are you sure you want to delete this widget area?', 'nexus-pro' ),
                'widgetAreaAdded'   => __( 'Widget area added successfully!', 'nexus-pro' ),
                'widgetAreaRemoved' => __( 'Widget area removed successfully!', 'nexus-pro' ),
            ),
        ) );
    }

    /**
     * Render builder page
     */
    public function render_builder_page() {
        ?>
        <div class="wrap nexus-menu-builder-wrap">
            <h1><?php esc_html_e( 'Nexus Menu Builder', 'nexus-pro' ); ?></h1>
            
            <div class="nexus-builder-header">
                <div class="nexus-builder-menu-select">
                    <label for="nexus-menu-select"><?php esc_html_e( 'Select Menu:', 'nexus-pro' ); ?></label>
                    <select id="nexus-menu-select" class="nexus-menu-select">
                        <option value=""><?php esc_html_e( '-- Select a Menu --', 'nexus-pro' ); ?></option>
                        <?php foreach ( $this->get_menus() as $menu ) : ?>
                            <option value="<?php echo esc_attr( $menu->term_id ); ?>">
                                <?php echo esc_html( $menu->name ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="nexus-builder-actions">
                    <button type="button" class="button button-secondary" id="nexus-preview-menu">
                        <span class="dashicons dashicons-visibility"></span>
                        <?php esc_html_e( 'Preview', 'nexus-pro' ); ?>
                    </button>
                    <button type="button" class="button button-primary" id="nexus-save-menu">
                        <span class="dashicons dashicons-saved"></span>
                        <?php esc_html_e( 'Save Menu', 'nexus-pro' ); ?>
                    </button>
                </div>
            </div>

            <div class="nexus-builder-container">
                <!-- Sidebar - Menu Items & Widgets -->
                <div class="nexus-builder-sidebar">
                    <div class="nexus-builder-panel">
                        <h3><?php esc_html_e( 'Menu Items', 'nexus-pro' ); ?></h3>
                        <div id="nexus-menu-items-list" class="nexus-items-list">
                            <p class="nexus-empty-state"><?php esc_html_e( 'Select a menu to get started.', 'nexus-pro' ); ?></p>
                        </div>
                    </div>

                    <div class="nexus-builder-panel">
                        <h3><?php esc_html_e( 'Widget Areas', 'nexus-pro' ); ?></h3>
                        <button type="button" class="button button-secondary" id="nexus-add-widget-area">
                            <span class="dashicons dashicons-plus"></span>
                            <?php esc_html_e( 'Add Widget Area', 'nexus-pro' ); ?>
                        </button>
                        <div id="nexus-widget-areas-list" class="nexus-items-list">
                            <?php $this->render_widget_areas(); ?>
                        </div>
                    </div>
                </div>

                <!-- Canvas - Visual Builder -->
                <div class="nexus-builder-canvas">
                    <div class="nexus-canvas-header">
                        <h3><?php esc_html_e( 'Menu Structure', 'nexus-pro' ); ?></h3>
                        <div class="nexus-canvas-controls">
                            <button type="button" class="button button-small" id="nexus-expand-all">
                                <?php esc_html_e( 'Expand All', 'nexus-pro' ); ?>
                            </button>
                            <button type="button" class="button button-small" id="nexus-collapse-all">
                                <?php esc_html_e( 'Collapse All', 'nexus-pro' ); ?>
                            </button>
                        </div>
                    </div>

                    <div id="nexus-menu-canvas" class="nexus-canvas-area">
                        <div class="nexus-canvas-empty">
                            <span class="dashicons dashicons-menu"></span>
                            <p><?php esc_html_e( 'Select a menu to start building', 'nexus-pro' ); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Settings Panel -->
                <div class="nexus-builder-settings">
                    <div class="nexus-settings-header">
                        <h3><?php esc_html_e( 'Item Settings', 'nexus-pro' ); ?></h3>
                        <button type="button" class="nexus-settings-close">
                            <span class="dashicons dashicons-no-alt"></span>
                        </button>
                    </div>

                    <div id="nexus-settings-content" class="nexus-settings-content">
                        <p class="nexus-empty-state"><?php esc_html_e( 'Select a menu item to edit settings.', 'nexus-pro' ); ?></p>
                    </div>
                </div>
            </div>

            <!-- Preview Modal -->
            <div id="nexus-preview-modal" class="nexus-preview-modal">
                <div class="nexus-preview-overlay"></div>
                <div class="nexus-preview-container">
                    <div class="nexus-preview-header">
                        <h3><?php esc_html_e( 'Menu Preview', 'nexus-pro' ); ?></h3>
                        <button type="button" class="nexus-preview-close">
                            <span class="dashicons dashicons-no-alt"></span>
                        </button>
                    </div>
                    <div class="nexus-preview-body">
                        <iframe id="nexus-preview-frame"></iframe>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Get all menus
     *
     * @return array
     */
    private function get_menus() {
        return wp_get_nav_menus();
    }

    /**
     * Render widget areas
     */
    private function render_widget_areas() {
        $widget_areas = get_option( 'nexus_mega_menu_widget_areas', array() );

        if ( empty( $widget_areas ) ) {
            echo '<p class="nexus-empty-state">' . esc_html__( 'No widget areas created yet.', 'nexus-pro' ) . '</p>';
            return;
        }

        foreach ( $widget_areas as $area ) {
            echo '<div class="nexus-widget-area-item" data-area-id="' . esc_attr( $area['id'] ) . '">';
            echo '  <span class="nexus-widget-area-name">' . esc_html( $area['name'] ) . '</span>';
            echo '  <button type="button" class="button button-small nexus-remove-widget-area" data-area-id="' . esc_attr( $area['id'] ) . '">';
            echo '    <span class="dashicons dashicons-trash"></span>';
            echo '  </button>';
            echo '</div>';
        }
    }

    /**
     * Register widget areas
     */
    public function register_widget_areas() {
        $widget_areas = get_option( 'nexus_mega_menu_widget_areas', array() );

        foreach ( $widget_areas as $area ) {
            register_sidebar( array(
                'id'            => 'nexus-mega-menu-' . $area['id'],
                'name'          => $area['name'],
                'description'   => __( 'Widget area for mega menu', 'nexus-pro' ),
                'before_widget' => '<div id="%1$s" class="nexus-mega-widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h4 class="nexus-mega-widget-title">',
                'after_title'   => '</h4>',
            ) );
        }
    }

    /**
     * Save menu builder data
     */
    public function save_menu_builder() {
        check_ajax_referer( 'nexus-menu-builder', 'nonce' );

        if ( ! current_user_can( 'edit_theme_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
        }

        $menu_id = isset( $_POST['menu_id'] ) ? absint( $_POST['menu_id'] ) : 0;
        $structure = isset( $_POST['structure'] ) ? json_decode( stripslashes( $_POST['structure'] ), true ) : array();

        if ( ! $menu_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid menu ID.', 'nexus-pro' ) ) );
        }

        // Save menu structure
        update_option( 'nexus_menu_builder_' . $menu_id, $structure );

        wp_send_json_success( array( 'message' => __( 'Menu saved successfully!', 'nexus-pro' ) ) );
    }

    /**
     * Load menu builder data
     */
    public function load_menu_builder() {
        check_ajax_referer( 'nexus-menu-builder', 'nonce' );

        if ( ! current_user_can( 'edit_theme_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
        }

        $menu_id = isset( $_POST['menu_id'] ) ? absint( $_POST['menu_id'] ) : 0;

        if ( ! $menu_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid menu ID.', 'nexus-pro' ) ) );
        }

        // Get menu items
        $menu_items = wp_get_nav_menu_items( $menu_id );
        $structure = get_option( 'nexus_menu_builder_' . $menu_id, array() );

        wp_send_json_success( array(
            'items'     => $menu_items,
            'structure' => $structure,
        ) );
    }

    /**
     * Add widget area
     */
    public function add_widget_area() {
        check_ajax_referer( 'nexus-menu-builder', 'nonce' );

        if ( ! current_user_can( 'edit_theme_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
        }

        $name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';

        if ( empty( $name ) ) {
            wp_send_json_error( array( 'message' => __( 'Widget area name is required.', 'nexus-pro' ) ) );
        }

        $widget_areas = get_option( 'nexus_mega_menu_widget_areas', array() );

        $new_area = array(
            'id'   => 'widget-area-' . time(),
            'name' => $name,
        );

        $widget_areas[] = $new_area;

        update_option( 'nexus_mega_menu_widget_areas', $widget_areas );

        wp_send_json_success( array(
            'message' => __( 'Widget area added successfully!', 'nexus-pro' ),
            'area'    => $new_area,
        ) );
    }

    /**
     * Remove widget area
     */
    public function remove_widget_area() {
        check_ajax_referer( 'nexus-menu-builder', 'nonce' );

        if ( ! current_user_can( 'edit_theme_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
        }

        $area_id = isset( $_POST['area_id'] ) ? sanitize_text_field( $_POST['area_id'] ) : '';

        if ( empty( $area_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Widget area ID is required.', 'nexus-pro' ) ) );
        }

        $widget_areas = get_option( 'nexus_mega_menu_widget_areas', array() );

        foreach ( $widget_areas as $key => $area ) {
            if ( $area['id'] === $area_id ) {
                unset( $widget_areas[ $key ] );
                break;
            }
        }

        $widget_areas = array_values( $widget_areas );

        update_option( 'nexus_mega_menu_widget_areas', $widget_areas );

        wp_send_json_success( array( 'message' => __( 'Widget area removed successfully!', 'nexus-pro' ) ) );
    }
}

// Initialize
Nexus_Menu_Builder::get_instance();
