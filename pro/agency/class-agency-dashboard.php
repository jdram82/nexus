<?php
/**
 * Nexus Multi-Site Dashboard (Agency Tier)
 * 
 * Central dashboard for managing unlimited client sites:
 * - Site health monitoring
 * - Bulk updates management
 * - Performance analytics
 * - License deployment
 * - Remote site management via API
 * 
 * @package Nexus_Theme
 * @since 1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nexus_Agency_Dashboard {
    
    /**
     * Instance
     */
    private static $instance = null;
    
    /**
     * Get instance
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
        if ( ! Nexus_License_Manager::is_tier_or_higher( 'agency' ) ) {
            return;
        }
        
        add_action( 'admin_menu', array( $this, 'add_admin_page' ), 5 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        
        // AJAX handlers
        add_action( 'wp_ajax_nexus_add_site', array( $this, 'ajax_add_site' ) );
        add_action( 'wp_ajax_nexus_remove_site', array( $this, 'ajax_remove_site' ) );
        add_action( 'wp_ajax_nexus_refresh_site', array( $this, 'ajax_refresh_site' ) );
        add_action( 'wp_ajax_nexus_bulk_update', array( $this, 'ajax_bulk_update' ) );
        add_action( 'wp_ajax_nexus_deploy_license', array( $this, 'ajax_deploy_license' ) );
        
        // Cron for site monitoring
        add_action( 'nexus_monitor_sites', array( $this, 'monitor_sites' ) );
        
        if ( ! wp_next_scheduled( 'nexus_monitor_sites' ) ) {
            wp_schedule_event( time(), 'hourly', 'nexus_monitor_sites' );
        }
    }
    
    /**
     * Add admin page
     */
    public function add_admin_page() {
        add_menu_page(
            __( 'Agency Dashboard', 'nexus' ),
            __( 'Agency Dashboard', 'nexus' ),
            'manage_options',
            'nexus-agency',
            array( $this, 'render_admin_page' ),
            'dashicons-networking',
            3
        );
    }
    
    /**
     * Enqueue assets
     */
    public function enqueue_assets( $hook ) {
        if ( 'toplevel_page_nexus-agency' !== $hook ) {
            return;
        }
        
        wp_enqueue_style( 'nexus-agency', get_template_directory_uri() . '/pro/assets/css/agency-dashboard.css', array(), '1.5.0' );
        wp_enqueue_script( 'nexus-agency', get_template_directory_uri() . '/pro/assets/js/agency-dashboard.js', array( 'jquery', 'wp-util' ), '1.5.0', true );
        
        wp_localize_script( 'nexus-agency', 'nexusAgency', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'nexus_agency_nonce' ),
            'sites'    => $this->get_sites(),
        ) );
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        $sites = $this->get_sites();
        $stats = $this->calculate_stats( $sites );
        
        ?>
        <div class="wrap nexus-agency-wrap">
            <h1>
                <span class="dashicons dashicons-networking"></span>
                <?php esc_html_e( 'Agency Dashboard', 'nexus' ); ?>
            </h1>
            
            <!-- Agency Stats -->
            <div class="agency-stats">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <span class="dashicons dashicons-admin-multisite"></span>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo esc_html( $stats['total_sites'] ); ?></div>
                        <div class="stat-label"><?php esc_html_e( 'Active Sites', 'nexus' ); ?></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <span class="dashicons dashicons-update"></span>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo esc_html( $stats['updates_available'] ); ?></div>
                        <div class="stat-label"><?php esc_html_e( 'Updates Available', 'nexus' ); ?></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <span class="dashicons dashicons-yes-alt"></span>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo esc_html( $stats['healthy_sites'] ); ?></div>
                        <div class="stat-label"><?php esc_html_e( 'Healthy Sites', 'nexus' ); ?></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <span class="dashicons dashicons-warning"></span>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo esc_html( $stats['issues'] ); ?></div>
                        <div class="stat-label"><?php esc_html_e( 'Issues', 'nexus' ); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="quick-actions">
                <button id="add-site" class="button button-primary button-hero">
                    <span class="dashicons dashicons-plus-alt"></span>
                    <?php esc_html_e( 'Add New Site', 'nexus' ); ?>
                </button>
                
                <button id="bulk-update" class="button button-hero">
                    <span class="dashicons dashicons-update"></span>
                    <?php esc_html_e( 'Bulk Update Sites', 'nexus' ); ?>
                </button>
                
                <button id="export-report" class="button button-hero">
                    <span class="dashicons dashicons-media-spreadsheet"></span>
                    <?php esc_html_e( 'Export Report', 'nexus' ); ?>
                </button>
                
                <button id="refresh-all" class="button button-hero">
                    <span class="dashicons dashicons-update"></span>
                    <?php esc_html_e( 'Refresh All', 'nexus' ); ?>
                </button>
            </div>
            
            <!-- Filters -->
            <div class="sites-filters">
                <div class="filter-group">
                    <label><?php esc_html_e( 'Status:', 'nexus' ); ?></label>
                    <select id="filter-status">
                        <option value="all"><?php esc_html_e( 'All Sites', 'nexus' ); ?></option>
                        <option value="healthy"><?php esc_html_e( 'Healthy', 'nexus' ); ?></option>
                        <option value="warning"><?php esc_html_e( 'Warnings', 'nexus' ); ?></option>
                        <option value="error"><?php esc_html_e( 'Errors', 'nexus' ); ?></option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label><?php esc_html_e( 'Updates:', 'nexus' ); ?></label>
                    <select id="filter-updates">
                        <option value="all"><?php esc_html_e( 'All', 'nexus' ); ?></option>
                        <option value="available"><?php esc_html_e( 'Updates Available', 'nexus' ); ?></option>
                        <option value="uptodate"><?php esc_html_e( 'Up to Date', 'nexus' ); ?></option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label><?php esc_html_e( 'Search:', 'nexus' ); ?></label>
                    <input type="search" id="search-sites" placeholder="<?php esc_attr_e( 'Search sites...', 'nexus' ); ?>">
                </div>
            </div>
            
            <!-- Sites Grid -->
            <div class="sites-grid" id="sites-grid">
                <?php $this->render_sites( $sites ); ?>
            </div>
            
            <!-- Add Site Modal -->
            <div id="add-site-modal" class="nexus-modal" style="display:none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2><?php esc_html_e( 'Add New Site', 'nexus' ); ?></h2>
                        <button class="modal-close">&times;</button>
                    </div>
                    
                    <div class="modal-body">
                        <form id="add-site-form">
                            <table class="form-table">
                                <tr>
                                    <th><?php esc_html_e( 'Site Name:', 'nexus' ); ?></th>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="site_name" 
                                            class="regular-text" 
                                            required
                                            placeholder="Client Website"
                                        >
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th><?php esc_html_e( 'Site URL:', 'nexus' ); ?></th>
                                    <td>
                                        <input 
                                            type="url" 
                                            name="site_url" 
                                            class="regular-text" 
                                            required
                                            placeholder="https://example.com"
                                        >
                                        <p class="description"><?php esc_html_e( 'Full URL including https://', 'nexus' ); ?></p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th><?php esc_html_e( 'API Key:', 'nexus' ); ?></th>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="api_key" 
                                            class="regular-text" 
                                            required
                                        >
                                        <p class="description">
                                            <?php esc_html_e( 'Generate in target site: Nexus Options → REST API → Create API Key', 'nexus' ); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th><?php esc_html_e( 'Client Name:', 'nexus' ); ?></th>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="client_name" 
                                            class="regular-text"
                                            placeholder="Optional"
                                        >
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th><?php esc_html_e( 'Tags:', 'nexus' ); ?></th>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="tags" 
                                            class="regular-text"
                                            placeholder="e-commerce, live, priority"
                                        >
                                        <p class="description"><?php esc_html_e( 'Comma-separated', 'nexus' ); ?></p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th><?php esc_html_e( 'Auto-Monitor:', 'nexus' ); ?></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="auto_monitor" checked>
                                            <?php esc_html_e( 'Check site health hourly', 'nexus' ); ?>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                            
                            <div class="modal-actions">
                                <button type="submit" class="button button-primary">
                                    <?php esc_html_e( 'Add Site', 'nexus' ); ?>
                                </button>
                                <button type="button" class="button modal-close">
                                    <?php esc_html_e( 'Cancel', 'nexus' ); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
        <?php
    }
    
    /**
     * Render sites grid
     */
    private function render_sites( $sites ) {
        if ( empty( $sites ) ) {
            ?>
            <div class="no-sites">
                <span class="dashicons dashicons-admin-multisite"></span>
                <h2><?php esc_html_e( 'No Sites Added Yet', 'nexus' ); ?></h2>
                <p><?php esc_html_e( 'Add your first client site to start monitoring.', 'nexus' ); ?></p>
            </div>
            <?php
            return;
        }
        
        foreach ( $sites as $site ) {
            $this->render_site_card( $site );
        }
    }
    
    /**
     * Render single site card
     */
    private function render_site_card( $site ) {
        $health_class = $site['health']['status'] ?? 'unknown';
        $health_icon = $this->get_health_icon( $health_class );
        
        ?>
        <div class="site-card" data-status="<?php echo esc_attr( $health_class ); ?>" data-id="<?php echo esc_attr( $site['id'] ); ?>">
            <div class="site-header">
                <div class="site-status <?php echo esc_attr( $health_class ); ?>">
                    <?php echo wp_kses_post( $health_icon ); ?>
                </div>
                <div class="site-info">
                    <h3><?php echo esc_html( $site['name'] ); ?></h3>
                    <a href="<?php echo esc_url( $site['url'] ); ?>" target="_blank" class="site-url">
                        <?php echo esc_html( $site['url'] ); ?>
                    </a>
                    <?php if ( ! empty( $site['client'] ) ) : ?>
                        <span class="client-name"><?php echo esc_html( $site['client'] ); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="site-stats">
                <div class="site-stat">
                    <span class="stat-label"><?php esc_html_e( 'Uptime:', 'nexus' ); ?></span>
                    <span class="stat-value"><?php echo esc_html( $site['health']['uptime'] ?? '100%' ); ?></span>
                </div>
                <div class="site-stat">
                    <span class="stat-label"><?php esc_html_e( 'Speed:', 'nexus' ); ?></span>
                    <span class="stat-value"><?php echo esc_html( $site['health']['load_time'] ?? '0.5s' ); ?></span>
                </div>
                <div class="site-stat">
                    <span class="stat-label"><?php esc_html_e( 'Version:', 'nexus' ); ?></span>
                    <span class="stat-value"><?php echo esc_html( $site['nexus_version'] ?? '1.5.0' ); ?></span>
                </div>
            </div>
            
            <?php if ( ! empty( $site['updates'] ) ) : ?>
                <div class="site-updates">
                    <span class="updates-badge"><?php echo esc_html( count( $site['updates'] ) ); ?> <?php esc_html_e( 'updates', 'nexus' ); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ( ! empty( $site['tags'] ) ) : ?>
                <div class="site-tags">
                    <?php foreach ( $site['tags'] as $tag ) : ?>
                        <span class="tag"><?php echo esc_html( $tag ); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="site-actions">
                <button class="button button-small refresh-site" data-id="<?php echo esc_attr( $site['id'] ); ?>">
                    <span class="dashicons dashicons-update"></span>
                    <?php esc_html_e( 'Refresh', 'nexus' ); ?>
                </button>
                
                <button class="button button-small view-details" data-id="<?php echo esc_attr( $site['id'] ); ?>">
                    <span class="dashicons dashicons-visibility"></span>
                    <?php esc_html_e( 'Details', 'nexus' ); ?>
                </button>
                
                <button class="button button-small remove-site" data-id="<?php echo esc_attr( $site['id'] ); ?>">
                    <span class="dashicons dashicons-no"></span>
                    <?php esc_html_e( 'Remove', 'nexus' ); ?>
                </button>
            </div>
            
            <div class="site-last-check">
                <?php esc_html_e( 'Last checked:', 'nexus' ); ?>
                <?php echo esc_html( human_time_diff( $site['last_check'] ?? time() ) ); ?> ago
            </div>
        </div>
        <?php
    }
    
    /**
     * Get health icon
     */
    private function get_health_icon( $status ) {
        $icons = array(
            'healthy' => '<span class="dashicons dashicons-yes-alt"></span>',
            'warning' => '<span class="dashicons dashicons-warning"></span>',
            'error'   => '<span class="dashicons dashicons-dismiss"></span>',
            'unknown' => '<span class="dashicons dashicons-editor-help"></span>',
        );
        
        return $icons[ $status ] ?? $icons['unknown'];
    }
    
    /**
     * AJAX: Add site
     */
    public function ajax_add_site() {
        check_ajax_referer( 'nexus_agency_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Insufficient permissions.' ) );
        }
        
        $site_data = array(
            'id'           => uniqid( 'site_' ),
            'name'         => sanitize_text_field( $_POST['site_name'] ),
            'url'          => esc_url_raw( $_POST['site_url'] ),
            'api_key'      => sanitize_text_field( $_POST['api_key'] ),
            'client'       => sanitize_text_field( $_POST['client_name'] ?? '' ),
            'tags'         => array_map( 'trim', explode( ',', sanitize_text_field( $_POST['tags'] ?? '' ) ) ),
            'auto_monitor' => isset( $_POST['auto_monitor'] ),
            'added'        => time(),
            'last_check'   => 0,
        );
        
        // Test connection
        $connection = $this->test_site_connection( $site_data );
        if ( is_wp_error( $connection ) ) {
            wp_send_json_error( array( 'message' => $connection->get_error_message() ) );
        }
        
        $site_data['health'] = $connection['health'];
        $site_data['nexus_version'] = $connection['version'];
        
        // Save site
        $sites = $this->get_sites();
        $sites[] = $site_data;
        update_option( 'nexus_agency_sites', $sites );
        
        wp_send_json_success( array(
            'site'    => $site_data,
            'message' => 'Site added successfully',
        ) );
    }
    
    /**
     * AJAX: Remove site
     */
    public function ajax_remove_site() {
        check_ajax_referer( 'nexus_agency_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }
        
        $site_id = sanitize_text_field( $_POST['site_id'] );
        
        $sites = $this->get_sites();
        $sites = array_filter( $sites, function( $site ) use ( $site_id ) {
            return $site['id'] !== $site_id;
        } );
        
        update_option( 'nexus_agency_sites', array_values( $sites ) );
        
        wp_send_json_success();
    }
    
    /**
     * AJAX: Refresh site
     */
    public function ajax_refresh_site() {
        check_ajax_referer( 'nexus_agency_nonce', 'nonce' );
        
        $site_id = sanitize_text_field( $_POST['site_id'] );
        
        $sites = $this->get_sites();
        foreach ( $sites as &$site ) {
            if ( $site['id'] === $site_id ) {
                $health = $this->check_site_health( $site );
                $site['health'] = $health;
                $site['last_check'] = time();
                break;
            }
        }
        
        update_option( 'nexus_agency_sites', $sites );
        
        wp_send_json_success( array( 'site' => $site ) );
    }
    
    /**
     * Get sites
     */
    private function get_sites() {
        return get_option( 'nexus_agency_sites', array() );
    }
    
    /**
     * Calculate stats
     */
    private function calculate_stats( $sites ) {
        $stats = array(
            'total_sites'       => count( $sites ),
            'healthy_sites'     => 0,
            'updates_available' => 0,
            'issues'            => 0,
        );
        
        foreach ( $sites as $site ) {
            if ( 'healthy' === ( $site['health']['status'] ?? '' ) ) {
                $stats['healthy_sites']++;
            }
            
            if ( ! empty( $site['updates'] ) ) {
                $stats['updates_available'] += count( $site['updates'] );
            }
            
            if ( in_array( $site['health']['status'] ?? '', array( 'warning', 'error' ), true ) ) {
                $stats['issues']++;
            }
        }
        
        return $stats;
    }
    
    /**
     * Test site connection
     */
    private function test_site_connection( $site ) {
        $response = wp_remote_get( 
            trailingslashit( $site['url'] ) . 'wp-json/nexus/v1/health',
            array(
                'headers' => array(
                    'X-Nexus-API-Key' => $site['api_key'],
                ),
                'timeout' => 10,
            )
        );
        
        if ( is_wp_error( $response ) ) {
            return $response;
        }
        
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        
        return array(
            'health'  => $body['health'] ?? array( 'status' => 'unknown' ),
            'version' => $body['version'] ?? 'Unknown',
        );
    }
    
    /**
     * Check site health
     */
    private function check_site_health( $site ) {
        $connection = $this->test_site_connection( $site );
        
        if ( is_wp_error( $connection ) ) {
            return array(
                'status'   => 'error',
                'message'  => $connection->get_error_message(),
                'uptime'   => 'N/A',
                'load_time' => 'N/A',
            );
        }
        
        return $connection['health'];
    }
    
    /**
     * Monitor sites (cron)
     */
    public function monitor_sites() {
        $sites = $this->get_sites();
        
        foreach ( $sites as &$site ) {
            if ( ! empty( $site['auto_monitor'] ) ) {
                $site['health'] = $this->check_site_health( $site );
                $site['last_check'] = time();
            }
        }
        
        update_option( 'nexus_agency_sites', $sites );
    }
}

// Initialize
Nexus_Agency_Dashboard::get_instance();
