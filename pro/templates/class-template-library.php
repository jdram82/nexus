<?php
/**
 * Nexus Template Library (Pro Tier)
 * 
 * Browse, import, and manage templates.
 * Cloud sync for up to 5 templates.
 * 
 * @package Nexus_Theme
 * @subpackage Templates
 * @since 1.5.0
 * @tier Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nexus_Template_Library {
    
    /**
     * Singleton instance
     */
    private static $instance = null;
    
    /**
     * Template post type
     */
    const POST_TYPE = 'nexus_template';
    
    /**
     * Cloud sync limit for Pro tier
     */
    const PRO_CLOUD_LIMIT = 5;
    const ADVANCED_CLOUD_LIMIT = -1; // Unlimited
    
    /**
     * API endpoint for template repository
     */
    private $api_url = 'https://templates.nexustheme.com/wp-json/nexus-templates/v1';
    
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
        add_action( 'init', array( $this, 'register_template_post_type' ) );
        add_action( 'admin_menu', array( $this, 'register_admin_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        
        // AJAX handlers
        add_action( 'wp_ajax_nexus_browse_templates', array( $this, 'ajax_browse_templates' ) );
        add_action( 'wp_ajax_nexus_import_template', array( $this, 'ajax_import_template' ) );
        add_action( 'wp_ajax_nexus_export_template', array( $this, 'ajax_export_template' ) );
        add_action( 'wp_ajax_nexus_sync_to_cloud', array( $this, 'ajax_sync_to_cloud' ) );
        add_action( 'wp_ajax_nexus_delete_template', array( $this, 'ajax_delete_template' ) );
    }
    
    /**
     * Register template post type
     */
    public function register_template_post_type() {
        register_post_type( self::POST_TYPE, array(
            'labels' => array(
                'name' => __( 'Templates', 'nexus' ),
                'singular_name' => __( 'Template', 'nexus' ),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'supports' => array( 'title', 'thumbnail' ),
            'capability_type' => 'page',
        ) );
    }
    
    /**
     * Register admin page
     */
    public function register_admin_page() {
        add_submenu_page(
            'nexus-options',
            __( 'Template Library', 'nexus' ),
            __( 'Templates', 'nexus' ) . ' <span class="nexus-pro-badge">Pro</span>',
            'edit_theme_options',
            'nexus-templates',
            array( $this, 'render_admin_page' )
        );
    }
    
    /**
     * Enqueue assets
     */
    public function enqueue_assets( $hook ) {
        if ( 'nexus_page_nexus-templates' !== $hook ) {
            return;
        }
        
        wp_enqueue_style(
            'nexus-templates',
            get_template_directory_uri() . '/pro/assets/css/templates.css',
            array(),
            NEXUS_VERSION
        );
        
        wp_enqueue_script(
            'nexus-templates',
            get_template_directory_uri() . '/pro/assets/js/templates.js',
            array( 'jquery', 'wp-util' ),
            NEXUS_VERSION,
            true
        );
        
        $license = Nexus_License_Manager::get_instance();
        
        wp_localize_script( 'nexus-templates', 'nexusTemplates', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'nexus_templates' ),
            'tier' => $license->get_tier(),
            'cloud_limit' => $this->get_cloud_limit(),
            'cloud_used' => $this->get_cloud_count(),
            'is_advanced' => $license->is_tier_or_higher( 'advanced' ),
        ) );
    }
    
    /**
     * Get cloud sync limit based on tier
     */
    private function get_cloud_limit() {
        $license = Nexus_License_Manager::get_instance();
        
        if ( $license->is_tier_or_higher( 'advanced' ) ) {
            return self::ADVANCED_CLOUD_LIMIT;
        }
        
        return self::PRO_CLOUD_LIMIT;
    }
    
    /**
     * Get current cloud template count
     */
    private function get_cloud_count() {
        $cloud_templates = get_posts( array(
            'post_type' => self::POST_TYPE,
            'posts_per_page' => -1,
            'meta_key' => '_nexus_cloud_synced',
            'meta_value' => '1',
        ) );
        
        return count( $cloud_templates );
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        $license = Nexus_License_Manager::get_instance();
        $local_templates = $this->get_local_templates();
        ?>
        <div class="wrap nexus-templates-wrap">
            <h1><?php esc_html_e( 'Template Library', 'nexus' ); ?></h1>
            
            <!-- Tier Info -->
            <div class="nexus-tier-info">
                <div class="tier-badge <?php echo esc_attr( $license->get_tier() ); ?>">
                    <?php echo esc_html( ucfirst( $license->get_tier() ) ); ?> Tier
                </div>
                <div class="cloud-status">
                    <?php
                    $limit = $this->get_cloud_limit();
                    $used = $this->get_cloud_count();
                    
                    if ( $limit === -1 ) {
                        printf(
                            esc_html__( 'Cloud Templates: %d (Unlimited)', 'nexus' ),
                            $used
                        );
                    } else {
                        printf(
                            esc_html__( 'Cloud Templates: %d / %d', 'nexus' ),
                            $used,
                            $limit
                        );
                    }
                    ?>
                </div>
            </div>
            
            <!-- Tab Navigation -->
            <nav class="nav-tab-wrapper">
                <a href="#browse" class="nav-tab nav-tab-active" data-tab="browse">
                    <?php esc_html_e( 'Browse Templates', 'nexus' ); ?>
                </a>
                <a href="#my-templates" class="nav-tab" data-tab="my-templates">
                    <?php esc_html_e( 'My Templates', 'nexus' ); ?>
                </a>
                <a href="#cloud" class="nav-tab" data-tab="cloud">
                    <?php esc_html_e( 'Cloud Sync', 'nexus' ); ?>
                </a>
                <?php if ( $license->is_tier_or_higher( 'advanced' ) ) : ?>
                    <a href="#marketplace" class="nav-tab" data-tab="marketplace">
                        <?php esc_html_e( 'Sell Templates', 'nexus' ); ?>
                        <span class="badge">Advanced</span>
                    </a>
                <?php endif; ?>
            </nav>
            
            <!-- Browse Templates Tab -->
            <div id="tab-browse" class="tab-content active">
                <div class="template-filters">
                    <select id="template-category">
                        <option value=""><?php esc_html_e( 'All Categories', 'nexus' ); ?></option>
                        <option value="saas"><?php esc_html_e( 'SaaS & Tech', 'nexus' ); ?></option>
                        <option value="docs"><?php esc_html_e( 'Documentation', 'nexus' ); ?></option>
                        <option value="ecommerce"><?php esc_html_e( 'E-commerce', 'nexus' ); ?></option>
                        <option value="portfolio"><?php esc_html_e( 'Portfolio', 'nexus' ); ?></option>
                        <option value="blog"><?php esc_html_e( 'Blog', 'nexus' ); ?></option>
                    </select>
                    
                    <select id="template-type">
                        <option value=""><?php esc_html_e( 'All Types', 'nexus' ); ?></option>
                        <option value="full-site"><?php esc_html_e( 'Full Site', 'nexus' ); ?></option>
                        <option value="landing-page"><?php esc_html_e( 'Landing Page', 'nexus' ); ?></option>
                        <option value="section"><?php esc_html_e( 'Section', 'nexus' ); ?></option>
                    </select>
                    
                    <button class="button" id="refresh-templates">
                        <span class="dashicons dashicons-update"></span>
                        <?php esc_html_e( 'Refresh', 'nexus' ); ?>
                    </button>
                </div>
                
                <div id="templates-grid" class="templates-grid">
                    <div class="loading">
                        <span class="spinner is-active"></span>
                        <p><?php esc_html_e( 'Loading templates...', 'nexus' ); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- My Templates Tab -->
            <div id="tab-my-templates" class="tab-content">
                <div class="template-actions">
                    <button class="button button-primary" id="create-new-template">
                        <?php esc_html_e( 'Create New Template', 'nexus' ); ?>
                    </button>
                    <button class="button" id="import-template-file">
                        <?php esc_html_e( 'Import from File', 'nexus' ); ?>
                    </button>
                </div>
                
                <div class="local-templates-grid">
                    <?php if ( empty( $local_templates ) ) : ?>
                        <div class="no-templates">
                            <p><?php esc_html_e( 'No templates yet. Create your first template or import one from the library.', 'nexus' ); ?></p>
                        </div>
                    <?php else : ?>
                        <?php foreach ( $local_templates as $template ) : ?>
                            <div class="template-card local" data-id="<?php echo esc_attr( $template->ID ); ?>">
                                <?php if ( has_post_thumbnail( $template->ID ) ) : ?>
                                    <?php echo get_the_post_thumbnail( $template->ID, 'medium' ); ?>
                                <?php else : ?>
                                    <div class="template-placeholder">
                                        <span class="dashicons dashicons-admin-page"></span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="template-info">
                                    <h3><?php echo esc_html( $template->post_title ); ?></h3>
                                    <div class="template-meta">
                                        <?php
                                        $is_cloud = get_post_meta( $template->ID, '_nexus_cloud_synced', true );
                                        if ( $is_cloud ) {
                                            echo '<span class="cloud-badge">☁️ Cloud</span>';
                                        }
                                        ?>
                                        <span class="date"><?php echo esc_html( get_the_date( '', $template->ID ) ); ?></span>
                                    </div>
                                </div>
                                
                                <div class="template-actions-buttons">
                                    <button class="button use-template" data-id="<?php echo esc_attr( $template->ID ); ?>">
                                        <?php esc_html_e( 'Use Template', 'nexus' ); ?>
                                    </button>
                                    <button class="button export-template" data-id="<?php echo esc_attr( $template->ID ); ?>">
                                        <?php esc_html_e( 'Export', 'nexus' ); ?>
                                    </button>
                                    <button class="button delete-template" data-id="<?php echo esc_attr( $template->ID ); ?>">
                                        <?php esc_html_e( 'Delete', 'nexus' ); ?>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Cloud Sync Tab -->
            <div id="tab-cloud" class="tab-content">
                <div class="cloud-info-panel">
                    <h2><?php esc_html_e( 'Cloud Sync', 'nexus' ); ?></h2>
                    <p><?php esc_html_e( 'Sync your templates to the cloud and access them from any site.', 'nexus' ); ?></p>
                    
                    <div class="cloud-stats">
                        <div class="stat">
                            <div class="value"><?php echo esc_html( $this->get_cloud_count() ); ?></div>
                            <div class="label"><?php esc_html_e( 'Synced Templates', 'nexus' ); ?></div>
                        </div>
                        <div class="stat">
                            <div class="value">
                                <?php 
                                $limit = $this->get_cloud_limit();
                                echo $limit === -1 ? '∞' : esc_html( $limit );
                                ?>
                            </div>
                            <div class="label"><?php esc_html_e( 'Cloud Limit', 'nexus' ); ?></div>
                        </div>
                    </div>
                    
                    <?php if ( ! $license->is_tier_or_higher( 'advanced' ) ) : ?>
                        <div class="upgrade-notice">
                            <p>
                                <strong><?php esc_html_e( 'Upgrade to Advanced', 'nexus' ); ?></strong>
                                <?php esc_html_e( 'Get unlimited cloud templates and team collaboration.', 'nexus' ); ?>
                            </p>
                            <a href="https://nexustheme.com/pricing" class="button button-primary" target="_blank">
                                <?php esc_html_e( 'Upgrade Now', 'nexus' ); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="sync-templates-list">
                    <h3><?php esc_html_e( 'Select Templates to Sync', 'nexus' ); ?></h3>
                    <?php foreach ( $local_templates as $template ) : ?>
                        <?php
                        $is_synced = get_post_meta( $template->ID, '_nexus_cloud_synced', true );
                        ?>
                        <div class="sync-item">
                            <label>
                                <input 
                                    type="checkbox" 
                                    class="sync-checkbox" 
                                    data-id="<?php echo esc_attr( $template->ID ); ?>"
                                    <?php checked( $is_synced, '1' ); ?>
                                    <?php disabled( ! $is_synced && $this->is_cloud_limit_reached() ); ?>
                                >
                                <span><?php echo esc_html( $template->post_title ); ?></span>
                                <?php if ( $is_synced ) : ?>
                                    <span class="synced-badge">✓ Synced</span>
                                <?php endif; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Marketplace Tab (Advanced Only) -->
            <?php if ( $license->is_tier_or_higher( 'advanced' ) ) : ?>
                <div id="tab-marketplace" class="tab-content">
                    <div class="marketplace-creator">
                        <h2><?php esc_html_e( 'Sell Your Templates', 'nexus' ); ?></h2>
                        <p><?php esc_html_e( 'Upload your templates to the marketplace and earn 70% revenue share.', 'nexus' ); ?></p>
                        
                        <div class="creator-stats">
                            <div class="stat">
                                <div class="value">0</div>
                                <div class="label"><?php esc_html_e( 'Templates Listed', 'nexus' ); ?></div>
                            </div>
                            <div class="stat">
                                <div class="value">0</div>
                                <div class="label"><?php esc_html_e( 'Total Sales', 'nexus' ); ?></div>
                            </div>
                            <div class="stat">
                                <div class="value">$0</div>
                                <div class="label"><?php esc_html_e( 'Earnings', 'nexus' ); ?></div>
                            </div>
                        </div>
                        
                        <button class="button button-primary button-hero" id="submit-template-marketplace">
                            <?php esc_html_e( 'Submit Template to Marketplace', 'nexus' ); ?>
                        </button>
                        
                        <a href="https://marketplace.nexustheme.com/creator-guidelines" target="_blank">
                            <?php esc_html_e( 'View Creator Guidelines', 'nexus' ); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Get local templates
     */
    private function get_local_templates() {
        return get_posts( array(
            'post_type' => self::POST_TYPE,
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
        ) );
    }
    
    /**
     * Check if cloud limit is reached
     */
    private function is_cloud_limit_reached() {
        $limit = $this->get_cloud_limit();
        
        if ( $limit === -1 ) {
            return false; // Unlimited
        }
        
        return $this->get_cloud_count() >= $limit;
    }
    
    /**
     * AJAX: Browse templates from repository
     */
    public function ajax_browse_templates() {
        check_ajax_referer( 'nexus_templates', 'nonce' );
        
        $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
        $type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
        
        // Mock template data (replace with actual API call)
        $templates = $this->get_mock_templates( $category, $type );
        
        wp_send_json_success( array( 'templates' => $templates ) );
    }
    
    /**
     * Get mock templates (replace with actual API call)
     */
    private function get_mock_templates( $category = '', $type = '' ) {
        $templates = array(
            array(
                'id' => 1,
                'title' => 'SaaS Landing Page',
                'description' => 'Modern SaaS landing page with hero, features, pricing, and testimonials',
                'category' => 'saas',
                'type' => 'full-site',
                'thumbnail' => 'https://via.placeholder.com/400x300?text=SaaS+Landing',
                'author' => 'Nexus Team',
                'downloads' => 1250,
                'rating' => 4.8,
                'price' => 'free',
            ),
            array(
                'id' => 2,
                'title' => 'Documentation Site',
                'description' => 'Complete documentation template with search, navigation, and code highlighting',
                'category' => 'docs',
                'type' => 'full-site',
                'thumbnail' => 'https://via.placeholder.com/400x300?text=Docs+Site',
                'author' => 'Nexus Team',
                'downloads' => 892,
                'rating' => 4.9,
                'price' => 'free',
            ),
            array(
                'id' => 3,
                'title' => 'Product Page',
                'description' => 'E-commerce product page with gallery, reviews, and related products',
                'category' => 'ecommerce',
                'type' => 'landing-page',
                'thumbnail' => 'https://via.placeholder.com/400x300?text=Product+Page',
                'author' => 'Community Creator',
                'downloads' => 543,
                'rating' => 4.6,
                'price' => '$49',
            ),
        );
        
        // Filter by category
        if ( $category ) {
            $templates = array_filter( $templates, function( $template ) use ( $category ) {
                return $template['category'] === $category;
            } );
        }
        
        // Filter by type
        if ( $type ) {
            $templates = array_filter( $templates, function( $template ) use ( $type ) {
                return $template['type'] === $type;
            } );
        }
        
        return array_values( $templates );
    }
    
    /**
     * AJAX: Import template
     */
    public function ajax_import_template() {
        check_ajax_referer( 'nexus_templates', 'nonce' );
        
        if ( ! current_user_can( 'edit_theme_options' ) ) {
            wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
        }
        
        $template_id = isset( $_POST['template_id'] ) ? intval( $_POST['template_id'] ) : 0;
        
        // Mock import (replace with actual API call and import logic)
        $template_data = array(
            'title' => 'Imported Template ' . $template_id,
            'content' => '{"sections":[],"settings":{}}',
        );
        
        $post_id = wp_insert_post( array(
            'post_type' => self::POST_TYPE,
            'post_title' => $template_data['title'],
            'post_content' => $template_data['content'],
            'post_status' => 'publish',
        ) );
        
        if ( is_wp_error( $post_id ) ) {
            wp_send_json_error( array( 'message' => 'Failed to import template' ) );
        }
        
        wp_send_json_success( array(
            'message' => 'Template imported successfully',
            'template_id' => $post_id,
        ) );
    }
    
    /**
     * AJAX: Export template
     */
    public function ajax_export_template() {
        check_ajax_referer( 'nexus_templates', 'nonce' );
        
        $template_id = isset( $_POST['template_id'] ) ? intval( $_POST['template_id'] ) : 0;
        $template = get_post( $template_id );
        
        if ( ! $template || self::POST_TYPE !== $template->post_type ) {
            wp_send_json_error( array( 'message' => 'Template not found' ) );
        }
        
        $export_data = array(
            'version' => NEXUS_VERSION,
            'title' => $template->post_title,
            'content' => $template->post_content,
            'meta' => get_post_meta( $template_id ),
            'exported_at' => current_time( 'mysql' ),
        );
        
        wp_send_json_success( array(
            'data' => $export_data,
            'filename' => sanitize_file_name( $template->post_title ) . '.json',
        ) );
    }
    
    /**
     * AJAX: Sync to cloud
     */
    public function ajax_sync_to_cloud() {
        check_ajax_referer( 'nexus_templates', 'nonce' );
        
        $template_id = isset( $_POST['template_id'] ) ? intval( $_POST['template_id'] ) : 0;
        $sync = isset( $_POST['sync'] ) ? (bool) $_POST['sync'] : false;
        
        if ( $sync && $this->is_cloud_limit_reached() ) {
            wp_send_json_error( array( 'message' => 'Cloud storage limit reached' ) );
        }
        
        update_post_meta( $template_id, '_nexus_cloud_synced', $sync ? '1' : '0' );
        update_post_meta( $template_id, '_nexus_cloud_synced_at', current_time( 'mysql' ) );
        
        wp_send_json_success( array(
            'message' => $sync ? 'Synced to cloud' : 'Removed from cloud',
            'cloud_count' => $this->get_cloud_count(),
        ) );
    }
    
    /**
     * AJAX: Delete template
     */
    public function ajax_delete_template() {
        check_ajax_referer( 'nexus_templates', 'nonce' );
        
        $template_id = isset( $_POST['template_id'] ) ? intval( $_POST['template_id'] ) : 0;
        
        if ( ! current_user_can( 'delete_post', $template_id ) ) {
            wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
        }
        
        $result = wp_delete_post( $template_id, true );
        
        if ( ! $result ) {
            wp_send_json_error( array( 'message' => 'Failed to delete template' ) );
        }
        
        wp_send_json_success( array( 'message' => 'Template deleted successfully' ) );
    }
}

// Initialize
add_action( 'after_setup_theme', function() {
    if ( class_exists( 'Nexus_License_Manager' ) ) {
        $license = Nexus_License_Manager::get_instance();
        if ( $license->is_tier_or_higher( 'pro' ) ) {
            Nexus_Template_Library::get_instance();
        }
    }
}, 30 );
