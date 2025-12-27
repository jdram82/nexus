<?php
/**
 * Nexus Dynamic Loop Builder (Advanced Tier)
 * 
 * Visual query builder with template designer and live preview.
 * Create custom post grids without touching code.
 * 
 * @package Nexus_Theme
 * @subpackage Loop_Builder
 * @since 1.4.0
 * @tier Advanced
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nexus_Loop_Builder {
    
    /**
     * Singleton instance
     */
    private static $instance = null;
    
    /**
     * Post type for saved loops
     */
    const POST_TYPE = 'nexus_loop';
    
    /**
     * Get singleton instance
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
        // Check for Advanced tier license
        if ( ! $this->is_advanced_tier_active() ) {
            return;
        }
        
        add_action( 'init', array( $this, 'register_loop_post_type' ) );
        add_action( 'admin_menu', array( $this, 'register_builder_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_builder_assets' ) );
        add_action( 'wp_ajax_nexus_loop_preview', array( $this, 'ajax_loop_preview' ) );
        add_action( 'wp_ajax_nexus_save_loop', array( $this, 'ajax_save_loop' ) );
        add_shortcode( 'nexus_loop', array( $this, 'render_loop_shortcode' ) );
    }
    
    /**
     * Check if Advanced tier is active
     */
    private function is_advanced_tier_active() {
        if ( class_exists( 'Nexus_License_Manager' ) ) {
            $license = Nexus_License_Manager::get_instance();
            return in_array( $license->get_tier(), array( 'advanced', 'agency' ), true );
        }
        return false;
    }
    
    /**
     * Register loop post type
     */
    public function register_loop_post_type() {
        register_post_type( self::POST_TYPE, array(
            'labels' => array(
                'name' => __( 'Loops', 'nexus' ),
                'singular_name' => __( 'Loop', 'nexus' ),
                'add_new' => __( 'Add New Loop', 'nexus' ),
                'add_new_item' => __( 'Add New Loop', 'nexus' ),
                'edit_item' => __( 'Edit Loop', 'nexus' ),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'supports' => array( 'title' ),
            'capability_type' => 'page',
        ) );
    }
    
    /**
     * Register builder admin page
     */
    public function register_builder_page() {
        add_submenu_page(
            'nexus-options',
            __( 'Loop Builder', 'nexus' ),
            __( 'Loop Builder', 'nexus' ) . ' <span class="nexus-pro-badge">Advanced</span>',
            'edit_theme_options',
            'nexus-loop-builder',
            array( $this, 'render_builder_page' )
        );
    }
    
    /**
     * Enqueue builder assets
     */
    public function enqueue_builder_assets( $hook ) {
        if ( 'nexus_page_nexus-loop-builder' !== $hook ) {
            return;
        }
        
        wp_enqueue_style(
            'nexus-loop-builder',
            get_template_directory_uri() . '/pro/assets/css/loop-builder.css',
            array(),
            NEXUS_VERSION
        );
        
        wp_enqueue_script(
            'nexus-loop-builder',
            get_template_directory_uri() . '/pro/assets/js/loop-builder.js',
            array( 'jquery', 'wp-api' ),
            NEXUS_VERSION,
            true
        );
        
        wp_localize_script( 'nexus-loop-builder', 'nexusLoopBuilder', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'nexus_loop_builder' ),
            'post_types' => $this->get_available_post_types(),
            'taxonomies' => $this->get_available_taxonomies(),
            'meta_keys' => $this->get_available_meta_keys(),
            'available_elements' => $this->get_available_elements(),
        ) );
    }
    
    /**
     * Render builder page
     */
    public function render_builder_page() {
        ?>
        <div class="wrap nexus-loop-builder-wrap">
            <h1>
                <?php esc_html_e( 'Loop Builder', 'nexus' ); ?>
                <button class="button button-primary" id="nexus-create-new-loop">
                    <?php esc_html_e( 'Create New Loop', 'nexus' ); ?>
                </button>
            </h1>
            
            <div class="nexus-loop-builder-container" style="display: none;">
                <!-- Three-Panel Layout -->
                <div class="nexus-builder-layout">
                    
                    <!-- Left Panel: Query Builder -->
                    <div class="nexus-panel nexus-query-panel">
                        <div class="panel-header">
                            <h2><?php esc_html_e( 'Query Builder', 'nexus' ); ?></h2>
                            <p class="description"><?php esc_html_e( 'Define what content to display', 'nexus' ); ?></p>
                        </div>
                        
                        <div class="panel-body">
                            <!-- Post Type Selection -->
                            <div class="control-group">
                                <label><?php esc_html_e( 'Post Type', 'nexus' ); ?></label>
                                <select id="loop-post-type" class="widefat">
                                    <option value="post"><?php esc_html_e( 'Posts', 'nexus' ); ?></option>
                                    <option value="page"><?php esc_html_e( 'Pages', 'nexus' ); ?></option>
                                    <option value="nexus_project"><?php esc_html_e( 'Projects', 'nexus' ); ?></option>
                                    <option value="nexus_product"><?php esc_html_e( 'Products', 'nexus' ); ?></option>
                                    <option value="nexus_download"><?php esc_html_e( 'Downloads', 'nexus' ); ?></option>
                                </select>
                            </div>
                            
                            <!-- Posts Per Page -->
                            <div class="control-group">
                                <label><?php esc_html_e( 'Posts Per Page', 'nexus' ); ?></label>
                                <input type="number" id="loop-posts-per-page" class="widefat" value="9" min="1" max="100">
                            </div>
                            
                            <!-- Order By -->
                            <div class="control-group">
                                <label><?php esc_html_e( 'Order By', 'nexus' ); ?></label>
                                <select id="loop-orderby" class="widefat">
                                    <option value="date"><?php esc_html_e( 'Date', 'nexus' ); ?></option>
                                    <option value="title"><?php esc_html_e( 'Title', 'nexus' ); ?></option>
                                    <option value="menu_order"><?php esc_html_e( 'Menu Order', 'nexus' ); ?></option>
                                    <option value="rand"><?php esc_html_e( 'Random', 'nexus' ); ?></option>
                                </select>
                            </div>
                            
                            <!-- Order -->
                            <div class="control-group">
                                <label><?php esc_html_e( 'Order', 'nexus' ); ?></label>
                                <select id="loop-order" class="widefat">
                                    <option value="DESC"><?php esc_html_e( 'Descending', 'nexus' ); ?></option>
                                    <option value="ASC"><?php esc_html_e( 'Ascending', 'nexus' ); ?></option>
                                </select>
                            </div>
                            
                            <!-- Taxonomy Filter -->
                            <div class="control-group">
                                <label><?php esc_html_e( 'Filter by Taxonomy', 'nexus' ); ?></label>
                                <button class="button" id="add-taxonomy-filter">
                                    <?php esc_html_e( '+ Add Taxonomy Filter', 'nexus' ); ?>
                                </button>
                                <div id="taxonomy-filters"></div>
                            </div>
                            
                            <!-- Meta Query -->
                            <div class="control-group">
                                <label><?php esc_html_e( 'Filter by Meta Field', 'nexus' ); ?></label>
                                <button class="button" id="add-meta-filter">
                                    <?php esc_html_e( '+ Add Meta Filter', 'nexus' ); ?>
                                </button>
                                <div id="meta-filters"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Center Panel: Template Designer -->
                    <div class="nexus-panel nexus-template-panel">
                        <div class="panel-header">
                            <h2><?php esc_html_e( 'Template Designer', 'nexus' ); ?></h2>
                            <p class="description"><?php esc_html_e( 'Design how each item looks', 'nexus' ); ?></p>
                        </div>
                        
                        <div class="panel-body">
                            <!-- Layout Type -->
                            <div class="control-group">
                                <label><?php esc_html_e( 'Layout Type', 'nexus' ); ?></label>
                                <div class="layout-selector">
                                    <button class="layout-option active" data-layout="grid">
                                        <span class="dashicons dashicons-grid-view"></span>
                                        <?php esc_html_e( 'Grid', 'nexus' ); ?>
                                    </button>
                                    <button class="layout-option" data-layout="masonry">
                                        <span class="dashicons dashicons-layout"></span>
                                        <?php esc_html_e( 'Masonry', 'nexus' ); ?>
                                    </button>
                                    <button class="layout-option" data-layout="list">
                                        <span class="dashicons dashicons-list-view"></span>
                                        <?php esc_html_e( 'List', 'nexus' ); ?>
                                    </button>
                                    <button class="layout-option" data-layout="carousel">
                                        <span class="dashicons dashicons-images-alt2"></span>
                                        <?php esc_html_e( 'Carousel', 'nexus' ); ?>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Columns -->
                            <div class="control-group" id="columns-control">
                                <label><?php esc_html_e( 'Columns', 'nexus' ); ?></label>
                                <input type="range" id="loop-columns" min="1" max="6" value="3" step="1">
                                <span class="value-display">3</span>
                            </div>
                            
                            <!-- Gap -->
                            <div class="control-group">
                                <label><?php esc_html_e( 'Gap Between Items', 'nexus' ); ?></label>
                                <input type="range" id="loop-gap" min="0" max="60" value="30" step="5">
                                <span class="value-display">30px</span>
                            </div>
                            
                            <!-- Card Elements -->
                            <div class="control-group">
                                <label><?php esc_html_e( 'Card Elements', 'nexus' ); ?></label>
                                <p class="description"><?php esc_html_e( 'Drag to reorder', 'nexus' ); ?></p>
                                <div id="card-elements" class="sortable-list">
                                    <!-- Elements will be added via JS -->
                                </div>
                                <button class="button" id="add-card-element">
                                    <?php esc_html_e( '+ Add Element', 'nexus' ); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Panel: Live Preview -->
                    <div class="nexus-panel nexus-preview-panel">
                        <div class="panel-header">
                            <h2><?php esc_html_e( 'Live Preview', 'nexus' ); ?></h2>
                            <div class="preview-controls">
                                <button class="button" id="refresh-preview">
                                    <span class="dashicons dashicons-update"></span>
                                </button>
                                <select id="preview-device">
                                    <option value="desktop"><?php esc_html_e( 'Desktop', 'nexus' ); ?></option>
                                    <option value="tablet"><?php esc_html_e( 'Tablet', 'nexus' ); ?></option>
                                    <option value="mobile"><?php esc_html_e( 'Mobile', 'nexus' ); ?></option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="panel-body">
                            <div id="loop-preview-container" class="preview-frame">
                                <div class="preview-loading">
                                    <span class="spinner is-active"></span>
                                    <p><?php esc_html_e( 'Loading preview...', 'nexus' ); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Bottom Action Bar -->
                <div class="nexus-builder-actions">
                    <div class="left-actions">
                        <input type="text" id="loop-title" placeholder="<?php esc_attr_e( 'Loop name...', 'nexus' ); ?>" class="regular-text">
                    </div>
                    <div class="right-actions">
                        <button class="button" id="cancel-loop"><?php esc_html_e( 'Cancel', 'nexus' ); ?></button>
                        <button class="button button-primary" id="save-loop"><?php esc_html_e( 'Save Loop', 'nexus' ); ?></button>
                    </div>
                </div>
            </div>
            
            <!-- Saved Loops List -->
            <div class="nexus-saved-loops">
                <h2><?php esc_html_e( 'Saved Loops', 'nexus' ); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Name', 'nexus' ); ?></th>
                            <th><?php esc_html_e( 'Post Type', 'nexus' ); ?></th>
                            <th><?php esc_html_e( 'Layout', 'nexus' ); ?></th>
                            <th><?php esc_html_e( 'Shortcode', 'nexus' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'nexus' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $loops = get_posts( array(
                            'post_type' => self::POST_TYPE,
                            'posts_per_page' => -1,
                            'post_status' => 'publish',
                        ) );
                        
                        if ( empty( $loops ) ) {
                            echo '<tr><td colspan="5">' . esc_html__( 'No loops created yet.', 'nexus' ) . '</td></tr>';
                        } else {
                            foreach ( $loops as $loop ) {
                                $config = json_decode( $loop->post_content, true );
                                ?>
                                <tr>
                                    <td><strong><?php echo esc_html( $loop->post_title ); ?></strong></td>
                                    <td><?php echo esc_html( $config['query']['post_type'] ?? 'post' ); ?></td>
                                    <td><?php echo esc_html( $config['template']['layout'] ?? 'grid' ); ?></td>
                                    <td>
                                        <code>[nexus_loop id="<?php echo esc_attr( $loop->ID ); ?>"]</code>
                                        <button class="button-small copy-shortcode" data-shortcode='[nexus_loop id="<?php echo esc_attr( $loop->ID ); ?>"]'>
                                            <?php esc_html_e( 'Copy', 'nexus' ); ?>
                                        </button>
                                    </td>
                                    <td>
                                        <a href="#" class="edit-loop" data-id="<?php echo esc_attr( $loop->ID ); ?>">
                                            <?php esc_html_e( 'Edit', 'nexus' ); ?>
                                        </a> |
                                        <a href="#" class="delete-loop" data-id="<?php echo esc_attr( $loop->ID ); ?>" style="color: #a00;">
                                            <?php esc_html_e( 'Delete', 'nexus' ); ?>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX: Generate loop preview
     */
    public function ajax_loop_preview() {
        check_ajax_referer( 'nexus_loop_builder', 'nonce' );
        
        $config = json_decode( stripslashes( $_POST['config'] ), true );
        
        $html = $this->render_loop( $config );
        
        wp_send_json_success( array( 'html' => $html ) );
    }
    
    /**
     * AJAX: Save loop
     */
    public function ajax_save_loop() {
        check_ajax_referer( 'nexus_loop_builder', 'nonce' );
        
        if ( ! current_user_can( 'edit_theme_options' ) ) {
            wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
        }
        
        $title = sanitize_text_field( $_POST['title'] );
        $config = stripslashes( $_POST['config'] );
        $loop_id = isset( $_POST['loop_id'] ) ? intval( $_POST['loop_id'] ) : 0;
        
        if ( $loop_id ) {
            // Update existing loop
            wp_update_post( array(
                'ID' => $loop_id,
                'post_title' => $title,
                'post_content' => $config,
            ) );
        } else {
            // Create new loop
            $loop_id = wp_insert_post( array(
                'post_type' => self::POST_TYPE,
                'post_title' => $title,
                'post_content' => $config,
                'post_status' => 'publish',
            ) );
        }
        
        if ( is_wp_error( $loop_id ) ) {
            wp_send_json_error( array( 'message' => $loop_id->get_error_message() ) );
        }
        
        wp_send_json_success( array(
            'message' => 'Loop saved successfully',
            'loop_id' => $loop_id,
            'shortcode' => '[nexus_loop id="' . $loop_id . '"]',
        ) );
    }
    
    /**
     * Render loop shortcode
     */
    public function render_loop_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'id' => 0,
        ), $atts );
        
        $loop = get_post( $atts['id'] );
        
        if ( ! $loop || self::POST_TYPE !== $loop->post_type ) {
            return '<p>' . esc_html__( 'Loop not found.', 'nexus' ) . '</p>';
        }
        
        $config = json_decode( $loop->post_content, true );
        
        return $this->render_loop( $config );
    }
    
    /**
     * Render loop based on configuration
     */
    private function render_loop( $config ) {
        // Build WP_Query args
        $args = array(
            'post_type' => $config['query']['post_type'] ?? 'post',
            'posts_per_page' => $config['query']['posts_per_page'] ?? 9,
            'orderby' => $config['query']['orderby'] ?? 'date',
            'order' => $config['query']['order'] ?? 'DESC',
        );
        
        // Add taxonomy filters if present
        if ( ! empty( $config['query']['tax_query'] ) ) {
            $args['tax_query'] = $config['query']['tax_query'];
        }
        
        // Add meta query if present
        if ( ! empty( $config['query']['meta_query'] ) ) {
            $args['meta_query'] = $config['query']['meta_query'];
        }
        
        $query = new WP_Query( $args );
        
        if ( ! $query->have_posts() ) {
            return '<p>' . esc_html__( 'No posts found.', 'nexus' ) . '</p>';
        }
        
        $layout = $config['template']['layout'] ?? 'grid';
        $columns = $config['template']['columns'] ?? 3;
        $gap = $config['template']['gap'] ?? 30;
        
        ob_start();
        ?>
        <div class="nexus-loop nexus-loop-<?php echo esc_attr( $layout ); ?>" 
             style="--columns: <?php echo esc_attr( $columns ); ?>; --gap: <?php echo esc_attr( $gap ); ?>px;">
            <?php
            while ( $query->have_posts() ) {
                $query->the_post();
                $this->render_loop_item( $config['template'] );
            }
            wp_reset_postdata();
            ?>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Render single loop item
     */
    private function render_loop_item( $template ) {
        $elements = $template['elements'] ?? array();
        ?>
        <div class="nexus-loop-item">
            <?php foreach ( $elements as $element ) : ?>
                <?php $this->render_element( $element ); ?>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    /**
     * Render individual element
     */
    private function render_element( $element ) {
        $type = $element['type'] ?? 'title';
        
        switch ( $type ) {
            case 'featured_image':
                if ( has_post_thumbnail() ) {
                    echo '<div class="element-image">';
                    the_post_thumbnail( $element['size'] ?? 'large' );
                    echo '</div>';
                }
                break;
                
            case 'title':
                echo '<h3 class="element-title">';
                echo '<a href="' . esc_url( get_permalink() ) . '">';
                the_title();
                echo '</a></h3>';
                break;
                
            case 'excerpt':
                echo '<div class="element-excerpt">';
                the_excerpt();
                echo '</div>';
                break;
                
            case 'meta':
                echo '<div class="element-meta">';
                echo esc_html( get_the_date() );
                echo ' by ' . esc_html( get_the_author() );
                echo '</div>';
                break;
                
            case 'button':
                echo '<div class="element-button">';
                echo '<a href="' . esc_url( get_permalink() ) . '" class="nexus-button">';
                echo esc_html( $element['text'] ?? __( 'Read More', 'nexus' ) );
                echo '</a></div>';
                break;
        }
    }
    
    /**
     * Get available post types
     */
    private function get_available_post_types() {
        return get_post_types( array( 'public' => true ), 'objects' );
    }
    
    /**
     * Get available taxonomies
     */
    private function get_available_taxonomies() {
        return get_taxonomies( array( 'public' => true ), 'objects' );
    }
    
    /**
     * Get available meta keys
     */
    private function get_available_meta_keys() {
        global $wpdb;
        return $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} ORDER BY meta_key LIMIT 100" );
    }
    
    /**
     * Get available elements
     */
    private function get_available_elements() {
        return array(
            'featured_image' => __( 'Featured Image', 'nexus' ),
            'title' => __( 'Title', 'nexus' ),
            'excerpt' => __( 'Excerpt', 'nexus' ),
            'content' => __( 'Full Content', 'nexus' ),
            'meta' => __( 'Post Meta', 'nexus' ),
            'taxonomies' => __( 'Categories/Tags', 'nexus' ),
            'button' => __( 'CTA Button', 'nexus' ),
            'custom_field' => __( 'Custom Field', 'nexus' ),
        );
    }
}

// Initialize
add_action( 'after_setup_theme', function() {
    Nexus_Loop_Builder::get_instance();
}, 30 );
