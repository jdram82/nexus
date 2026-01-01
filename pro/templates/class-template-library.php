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
     * Get templates from local data files
     */
    private function get_mock_templates( $category = '', $type = '' ) {
        $templates = array();
        $data_dir = get_template_directory() . '/pro/templates/data/';
        
        if ( ! is_dir( $data_dir ) ) {
            return $templates;
        }
        
        $files = glob( $data_dir . '*.json' );
        $id = 1;
        
        foreach ( $files as $file ) {
            $template_data = json_decode( file_get_contents( $file ), true );
            
            if ( ! $template_data || ! isset( $template_data['name'] ) ) {
                continue;
            }
            
            $filename = basename( $file, '.json' );
            $template_category = isset( $template_data['category'] ) ? $template_data['category'] : 'general';
            $template_type = isset( $template_data['type'] ) ? $template_data['type'] : 'page';
            
            // Filter by category if specified
            if ( $category && $template_category !== $category ) {
                continue;
            }
            
            // Filter by type if specified
            if ( $type && $template_type !== $type ) {
                continue;
            }
            
            $templates[] = array(
                'id' => $filename,
                'title' => $template_data['name'],
                'description' => isset( $template_data['description'] ) ? $template_data['description'] : '',
                'category' => $template_category,
                'type' => $template_type,
                'thumbnail' => get_template_directory_uri() . '/pro/templates/previews/' . $filename . '.jpg',
                'thumbnail_fallback' => get_template_directory_uri() . '/assets/images/template-placeholder.png',
                'author' => 'Nexus Team',
                'downloads' => rand( 100, 2000 ),
                'rating' => number_format( rand( 40, 50 ) / 10, 1 ),
                'price' => 'free',
                'file' => $filename,
            );
            
            $id++;
        }
        
        return $templates;
    }
    
    /**
     * AJAX: Import template
     */
    public function ajax_import_template() {
        check_ajax_referer( 'nexus_templates', 'nonce' );
        
        if ( ! current_user_can( 'edit_theme_options' ) ) {
            wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
        }
        
        $template_id = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : '';
        
        if ( empty( $template_id ) ) {
            wp_send_json_error( array( 'message' => 'No template ID provided' ) );
        }
        
        // Load template data from JSON file
        $template_file = get_template_directory() . '/pro/templates/data/' . $template_id . '.json';
        
        if ( ! file_exists( $template_file ) ) {
            wp_send_json_error( array( 'message' => 'Template file not found: ' . $template_id ) );
        }
        
        $template_data = json_decode( file_get_contents( $template_file ), true );
        
        if ( ! $template_data ) {
            wp_send_json_error( array( 'message' => 'Invalid template data' ) );
        }
        
        // Create new page with template content
        $page_title = isset( $template_data['name'] ) ? $template_data['name'] : 'Imported Template';
        $page_content = '';
        
        // Build page content from sections
        if ( isset( $template_data['sections'] ) && is_array( $template_data['sections'] ) ) {
            foreach ( $template_data['sections'] as $section ) {
                $page_content .= '<!-- wp:group -->';
                $page_content .= '<div class="wp-block-group">';
                
                if ( isset( $section['columns'] ) && is_array( $section['columns'] ) ) {
                    foreach ( $section['columns'] as $column ) {
                        if ( isset( $column['widgets'] ) && is_array( $column['widgets'] ) ) {
                            foreach ( $column['widgets'] as $widget ) {
                                if ( isset( $widget['type'] ) && $widget['type'] === 'heading' && isset( $widget['settings']['text'] ) ) {
                                    $heading_tag = isset( $widget['settings']['tag'] ) ? $widget['settings']['tag'] : 'h2';
                                    $page_content .= "<!-- wp:heading -->";
                                    $page_content .= "<{$heading_tag}>" . esc_html( $widget['settings']['text'] ) . "</{$heading_tag}>";
                                    $page_content .= "<!-- /wp:heading -->";
                                }
                            }
                        }
                    }
                }
                
                $page_content .= '</div>';
                $page_content .= '<!-- /wp:group -->';
            }
        }
        
        $post_id = wp_insert_post( array(
            'post_type' => 'page',
            'post_title' => $page_title,
            'post_content' => $page_content,
            'post_status' => 'draft',
        ) );
        
        if ( is_wp_error( $post_id ) ) {
            wp_send_json_error( array( 'message' => 'Failed to create page' ) );
        }
        
        // Store template metadata
        update_post_meta( $post_id, '_nexus_template_source', $template_id );
        update_post_meta( $post_id, '_nexus_template_data', $template_data );
        
        wp_send_json_success( array(
            'message' => 'Template imported successfully!',
            'page_id' => $post_id,
            'edit_url' => admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
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
