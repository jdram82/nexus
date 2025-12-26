<?php
/**
 * Theme Builder - Visual Page Builder
 *
 * Provides Elementor-style drag-and-drop page building capabilities
 * with custom widgets, templates, and responsive editing.
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main Theme Builder Class
 *
 * Handles builder initialization, post type registration,
 * and builder mode activation.
 */
class Nexus_Theme_Builder {

    /**
     * Builder version
     *
     * @var string
     */
    const VERSION = '3.0.0';

    /**
     * Post type for builder pages
     *
     * @var string
     */
    const POST_TYPE = 'nexus_builder';

    /**
     * Instance of this class
     *
     * @var Nexus_Theme_Builder
     */
    private static $instance = null;

    /**
     * Widget manager instance
     *
     * @var Nexus_Builder_Widgets
     */
    public $widgets;

    /**
     * Canvas renderer instance
     *
     * @var Nexus_Builder_Canvas
     */
    public $canvas;

    /**
     * Get singleton instance
     *
     * @return Nexus_Theme_Builder
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
        $this->init_hooks();
        $this->load_dependencies();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
        add_action( 'wp_ajax_nexus_save_builder_content', array( $this, 'ajax_save_content' ) );
        add_action( 'wp_ajax_nexus_load_builder_content', array( $this, 'ajax_load_content' ) );
        add_action( 'template_redirect', array( $this, 'builder_mode' ) );
        add_filter( 'the_content', array( $this, 'render_builder_content' ), 999 );
    }

    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        require_once NEXUS_PRO_PATH . 'theme-builder/class-builder-canvas.php';
        require_once NEXUS_PRO_PATH . 'theme-builder/class-builder-widgets.php';
        require_once NEXUS_PRO_PATH . 'theme-builder/class-builder-templates.php';

        $this->canvas = new Nexus_Builder_Canvas();
        $this->widgets = new Nexus_Builder_Widgets();
    }

    /**
     * Register builder post type
     */
    public function register_post_type() {
        $labels = array(
            'name'               => __( 'Builder Pages', 'nexus-pro' ),
            'singular_name'      => __( 'Builder Page', 'nexus-pro' ),
            'add_new'            => __( 'Add New', 'nexus-pro' ),
            'add_new_item'       => __( 'Add New Builder Page', 'nexus-pro' ),
            'edit_item'          => __( 'Edit Builder Page', 'nexus-pro' ),
            'new_item'           => __( 'New Builder Page', 'nexus-pro' ),
            'view_item'          => __( 'View Builder Page', 'nexus-pro' ),
            'search_items'       => __( 'Search Builder Pages', 'nexus-pro' ),
            'not_found'          => __( 'No builder pages found', 'nexus-pro' ),
            'not_found_in_trash' => __( 'No builder pages found in trash', 'nexus-pro' ),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'has_archive'         => false,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => false, // We'll add custom menu
            'show_in_rest'        => true,
            'rest_base'           => 'builder-pages',
            'capability_type'     => 'page',
            'hierarchical'        => true,
            'supports'            => array( 'title', 'author', 'revisions', 'page-attributes' ),
            'menu_icon'           => 'dashicons-layout',
            'rewrite'             => array( 'slug' => 'builder-page' ),
        );

        register_post_type( self::POST_TYPE, $args );

        // Register metadata for builder content
        register_post_meta( self::POST_TYPE, '_nexus_builder_data', array(
            'type'              => 'string',
            'description'       => __( 'Builder content data in JSON format', 'nexus-pro' ),
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback'     => function() {
                return current_user_can( 'edit_posts' );
            },
        ) );

        register_post_meta( self::POST_TYPE, '_nexus_builder_version', array(
            'type'              => 'string',
            'description'       => __( 'Builder version used', 'nexus-pro' ),
            'single'            => true,
            'show_in_rest'      => true,
            'default'           => self::VERSION,
        ) );
    }

    /**
     * Add admin menu page
     */
    public function add_menu_page() {
        add_menu_page(
            __( 'Theme Builder', 'nexus-pro' ),
            __( 'Theme Builder', 'nexus-pro' ),
            'edit_pages',
            'nexus-theme-builder',
            array( $this, 'render_admin_page' ),
            'dashicons-layout',
            25
        );

        add_submenu_page(
            'nexus-theme-builder',
            __( 'All Pages', 'nexus-pro' ),
            __( 'All Pages', 'nexus-pro' ),
            'edit_pages',
            'edit.php?post_type=' . self::POST_TYPE
        );

        add_submenu_page(
            'nexus-theme-builder',
            __( 'Add New', 'nexus-pro' ),
            __( 'Add New', 'nexus-pro' ),
            'edit_pages',
            'post-new.php?post_type=' . self::POST_TYPE
        );
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        ?>
        <div class="wrap nexus-builder-welcome">
            <h1><?php esc_html_e( 'Nexus Theme Builder', 'nexus-pro' ); ?></h1>
            <p class="about-text">
                <?php esc_html_e( 'Create stunning pages with our drag-and-drop visual builder. No coding required.', 'nexus-pro' ); ?>
            </p>

            <div class="nexus-builder-stats">
                <div class="stat-box">
                    <span class="stat-number"><?php echo esc_html( wp_count_posts( self::POST_TYPE )->publish ); ?></span>
                    <span class="stat-label"><?php esc_html_e( 'Published Pages', 'nexus-pro' ); ?></span>
                </div>
                <div class="stat-box">
                    <span class="stat-number"><?php echo esc_html( wp_count_posts( self::POST_TYPE )->draft ); ?></span>
                    <span class="stat-label"><?php esc_html_e( 'Drafts', 'nexus-pro' ); ?></span>
                </div>
                <div class="stat-box">
                    <span class="stat-number"><?php echo count( $this->widgets->get_registered_widgets() ); ?></span>
                    <span class="stat-label"><?php esc_html_e( 'Available Widgets', 'nexus-pro' ); ?></span>
                </div>
            </div>

            <div class="nexus-builder-actions">
                <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . self::POST_TYPE ) ); ?>" class="button button-primary button-hero">
                    <?php esc_html_e( 'Create New Page', 'nexus-pro' ); ?>
                </a>
                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . self::POST_TYPE ) ); ?>" class="button button-hero">
                    <?php esc_html_e( 'View All Pages', 'nexus-pro' ); ?>
                </a>
            </div>

            <div class="nexus-builder-features">
                <h2><?php esc_html_e( 'Builder Features', 'nexus-pro' ); ?></h2>
                <div class="feature-grid">
                    <div class="feature-item">
                        <span class="dashicons dashicons-move"></span>
                        <h3><?php esc_html_e( 'Drag & Drop', 'nexus-pro' ); ?></h3>
                        <p><?php esc_html_e( 'Intuitive interface for building pages visually', 'nexus-pro' ); ?></p>
                    </div>
                    <div class="feature-item">
                        <span class="dashicons dashicons-admin-customizer"></span>
                        <h3><?php esc_html_e( 'Live Preview', 'nexus-pro' ); ?></h3>
                        <p><?php esc_html_e( 'See changes in real-time as you build', 'nexus-pro' ); ?></p>
                    </div>
                    <div class="feature-item">
                        <span class="dashicons dashicons-smartphone"></span>
                        <h3><?php esc_html_e( 'Responsive', 'nexus-pro' ); ?></h3>
                        <p><?php esc_html_e( 'Edit for desktop, tablet, and mobile views', 'nexus-pro' ); ?></p>
                    </div>
                    <div class="feature-item">
                        <span class="dashicons dashicons-admin-appearance"></span>
                        <h3><?php esc_html_e( '20+ Widgets', 'nexus-pro' ); ?></h3>
                        <p><?php esc_html_e( 'Rich library of pre-built components', 'nexus-pro' ); ?></p>
                    </div>
                    <div class="feature-item">
                        <span class="dashicons dashicons-admin-page"></span>
                        <h3><?php esc_html_e( 'Templates', 'nexus-pro' ); ?></h3>
                        <p><?php esc_html_e( 'Save and reuse your favorite designs', 'nexus-pro' ); ?></p>
                    </div>
                    <div class="feature-item">
                        <span class="dashicons dashicons-backup"></span>
                        <h3><?php esc_html_e( 'Revision History', 'nexus-pro' ); ?></h3>
                        <p><?php esc_html_e( 'Undo changes and restore previous versions', 'nexus-pro' ); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_admin_assets( $hook ) {
        // Only load on builder pages
        if ( ! $this->is_builder_screen( $hook ) ) {
            return;
        }

        // Styles
        wp_enqueue_style(
            'nexus-theme-builder',
            NEXUS_PRO_URL . 'assets/css/theme-builder.css',
            array( 'wp-components' ),
            self::VERSION
        );

        // Scripts
        wp_enqueue_script(
            'nexus-theme-builder',
            NEXUS_PRO_URL . 'assets/js/theme-builder.js',
            array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'wp-util' ),
            self::VERSION,
            true
        );

        // Localize script
        wp_localize_script( 'nexus-theme-builder', 'nexusBuilder', array(
            'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
            'nonce'      => wp_create_nonce( 'nexus_builder_nonce' ),
            'postId'     => get_the_ID(),
            'version'    => self::VERSION,
            'widgets'    => $this->widgets->get_registered_widgets(),
            'i18n'       => array(
                'save'             => __( 'Save', 'nexus-pro' ),
                'saving'           => __( 'Saving...', 'nexus-pro' ),
                'saved'            => __( 'Saved!', 'nexus-pro' ),
                'error'            => __( 'Error saving content', 'nexus-pro' ),
                'confirmDelete'    => __( 'Are you sure you want to delete this element?', 'nexus-pro' ),
                'addWidget'        => __( 'Add Widget', 'nexus-pro' ),
                'editWidget'       => __( 'Edit Widget', 'nexus-pro' ),
                'duplicateWidget'  => __( 'Duplicate', 'nexus-pro' ),
                'deleteWidget'     => __( 'Delete', 'nexus-pro' ),
            ),
        ) );
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        if ( ! is_singular( self::POST_TYPE ) && ! $this->has_builder_content() ) {
            return;
        }

        wp_enqueue_style(
            'nexus-builder-frontend',
            NEXUS_PRO_URL . 'assets/css/theme-builder.css',
            array(),
            self::VERSION
        );

        wp_enqueue_script(
            'nexus-builder-frontend',
            NEXUS_PRO_URL . 'assets/js/theme-builder-frontend.js',
            array( 'jquery' ),
            self::VERSION,
            true
        );
    }

    /**
     * Check if current screen is builder screen
     *
     * @param string $hook Current hook.
     * @return bool
     */
    private function is_builder_screen( $hook ) {
        global $post;

        $builder_screens = array(
            'post.php',
            'post-new.php',
            'toplevel_page_nexus-theme-builder',
        );

        if ( ! in_array( $hook, $builder_screens, true ) ) {
            return false;
        }

        if ( isset( $post ) && self::POST_TYPE === get_post_type( $post ) ) {
            return true;
        }

        if ( isset( $_GET['post_type'] ) && self::POST_TYPE === $_GET['post_type'] ) {
            return true;
        }

        return false;
    }

    /**
     * Builder mode - Full screen editing
     */
    public function builder_mode() {
        if ( ! isset( $_GET['nexus-builder'] ) || ! isset( $_GET['post'] ) ) {
            return;
        }

        $post_id = absint( $_GET['post'] );
        
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            wp_die( esc_html__( 'You do not have permission to edit this page.', 'nexus-pro' ) );
        }

        // Load builder template
        $this->canvas->render_builder_interface( $post_id );
        exit;
    }

    /**
     * AJAX: Save builder content
     */
    public function ajax_save_content() {
        check_ajax_referer( 'nexus_builder_nonce', 'nonce' );

        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
        $content = isset( $_POST['content'] ) ? wp_kses_post( $_POST['content'] ) : '';
        $data    = isset( $_POST['data'] ) ? sanitize_text_field( $_POST['data'] ) : '';

        if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied', 'nexus-pro' ) ) );
        }

        // Save builder data
        update_post_meta( $post_id, '_nexus_builder_data', $data );
        update_post_meta( $post_id, '_nexus_builder_version', self::VERSION );

        // Update post content (for non-builder rendering)
        wp_update_post( array(
            'ID'           => $post_id,
            'post_content' => $content,
        ) );

        wp_send_json_success( array(
            'message' => __( 'Content saved successfully', 'nexus-pro' ),
            'time'    => current_time( 'mysql' ),
        ) );
    }

    /**
     * AJAX: Load builder content
     */
    public function ajax_load_content() {
        check_ajax_referer( 'nexus_builder_nonce', 'nonce' );

        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

        if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied', 'nexus-pro' ) ) );
        }

        $data = get_post_meta( $post_id, '_nexus_builder_data', true );

        wp_send_json_success( array(
            'data'    => $data ? json_decode( $data, true ) : array(),
            'version' => get_post_meta( $post_id, '_nexus_builder_version', true ),
        ) );
    }

    /**
     * Render builder content on frontend
     *
     * @param string $content Post content.
     * @return string
     */
    public function render_builder_content( $content ) {
        global $post;

        if ( ! is_singular( self::POST_TYPE ) || ! is_main_query() ) {
            return $content;
        }

        $builder_data = get_post_meta( $post->ID, '_nexus_builder_data', true );

        if ( ! $builder_data ) {
            return $content;
        }

        return $this->canvas->render_content( json_decode( $builder_data, true ) );
    }

    /**
     * Check if current post has builder content
     *
     * @return bool
     */
    private function has_builder_content() {
        global $post;

        if ( ! $post ) {
            return false;
        }

        $builder_data = get_post_meta( $post->ID, '_nexus_builder_data', true );
        return ! empty( $builder_data );
    }

    /**
     * Get builder edit URL
     *
     * @param int $post_id Post ID.
     * @return string
     */
    public static function get_edit_url( $post_id ) {
        return add_query_arg(
            array(
                'nexus-builder' => '1',
                'post'          => $post_id,
            ),
            home_url( '/' )
        );
    }
}

// Initialize
Nexus_Theme_Builder::get_instance();
