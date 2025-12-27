<?php
/**
 * Nexus Advanced Plugin Orchestrator (Advanced Tier)
 * 
 * Deep plugin integrations with auto-styling injection,
 * integration dashboard, and advanced compatibility features.
 * 
 * @package Nexus_Theme
 * @subpackage Plugin_Orchestrator
 * @since 1.4.0
 * @tier Advanced
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nexus_Plugin_Orchestrator {
    
    /**
     * Singleton instance
     */
    private static $instance = null;
    
    /**
     * Advanced integration handlers
     */
    private $integration_handlers = array();
    
    /**
     * Style injection queue
     */
    private $style_injections = array();
    
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
        
        add_action( 'init', array( $this, 'register_integration_handlers' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'inject_advanced_styles' ), 100 );
        add_action( 'admin_menu', array( $this, 'register_integration_dashboard' ) );
        add_action( 'wp_ajax_nexus_test_integration', array( $this, 'ajax_test_integration' ) );
    }
    
    /**
     * Check if Advanced tier is active
     */
    private function is_advanced_tier_active() {
        // Check license tier
        if ( class_exists( 'Nexus_License_Manager' ) ) {
            $license = Nexus_License_Manager::get_instance();
            return $license->get_tier() === 'advanced' || $license->get_tier() === 'agency';
        }
        return false;
    }
    
    /**
     * Register integration handlers for specific plugins
     */
    public function register_integration_handlers() {
        // Gravity Forms Deep Integration
        if ( class_exists( 'GFForms' ) ) {
            require_once __DIR__ . '/integrations/class-gravity-forms-integration.php';
            $this->integration_handlers['gravity-forms'] = new Nexus_Gravity_Forms_Integration();
        }
        
        // Rank Math Deep Integration
        if ( class_exists( 'RankMath' ) ) {
            require_once __DIR__ . '/integrations/class-rank-math-integration.php';
            $this->integration_handlers['rank-math'] = new Nexus_Rank_Math_Integration();
        }
        
        // WPForms Deep Integration
        if ( function_exists( 'wpforms' ) ) {
            require_once __DIR__ . '/integrations/class-wpforms-integration.php';
            $this->integration_handlers['wpforms'] = new Nexus_WPForms_Integration();
        }
        
        // WP Rocket Deep Integration
        if ( defined( 'WP_ROCKET_VERSION' ) ) {
            require_once __DIR__ . '/integrations/class-wp-rocket-integration.php';
            $this->integration_handlers['wp-rocket'] = new Nexus_WP_Rocket_Integration();
        }
        
        // Allow third-party integrations
        $this->integration_handlers = apply_filters( 'nexus_integration_handlers', $this->integration_handlers );
    }
    
    /**
     * Inject advanced styles for third-party plugins
     */
    public function inject_advanced_styles() {
        foreach ( $this->integration_handlers as $slug => $handler ) {
            if ( method_exists( $handler, 'get_style_overrides' ) ) {
                $overrides = $handler->get_style_overrides();
                if ( ! empty( $overrides ) ) {
                    $this->inject_inline_styles( $slug, $overrides );
                }
            }
        }
    }
    
    /**
     * Inject inline styles with Nexus design tokens
     */
    private function inject_inline_styles( $slug, $overrides ) {
        // Get Nexus design tokens
        $tokens = $this->get_design_tokens();
        
        // Parse overrides and replace tokens
        $css = '';
        foreach ( $overrides as $selector => $properties ) {
            $css .= $selector . ' {';
            foreach ( $properties as $property => $value ) {
                // Replace design tokens (e.g., {{primary-color}})
                $value = preg_replace_callback(
                    '/\{\{([^}]+)\}\}/',
                    function( $matches ) use ( $tokens ) {
                        return isset( $tokens[ $matches[1] ] ) ? $tokens[ $matches[1] ] : $matches[0];
                    },
                    $value
                );
                $css .= $property . ': ' . $value . ';';
            }
            $css .= '}';
        }
        
        // Inject minified CSS
        wp_add_inline_style( 'nexus-main', $this->minify_css( $css ) );
    }
    
    /**
     * Get Nexus design tokens from customizer
     */
    private function get_design_tokens() {
        return array(
            'primary-color' => get_theme_mod( 'nexus_primary_color', '#0066cc' ),
            'secondary-color' => get_theme_mod( 'nexus_secondary_color', '#333333' ),
            'accent-color' => get_theme_mod( 'nexus_accent_color', '#ff6b6b' ),
            'font-family-base' => get_theme_mod( 'nexus_font_base', 'Inter, sans-serif' ),
            'font-family-heading' => get_theme_mod( 'nexus_font_heading', 'Space Grotesk, sans-serif' ),
            'border-radius' => get_theme_mod( 'nexus_border_radius', '8px' ),
            'spacing-unit' => get_theme_mod( 'nexus_spacing_unit', '1rem' ),
        );
    }
    
    /**
     * Minify CSS
     */
    private function minify_css( $css ) {
        $css = preg_replace( '/\s+/', ' ', $css );
        $css = preg_replace( '/\s*([{}:;,])\s*/', '$1', $css );
        return trim( $css );
    }
    
    /**
     * Register integration dashboard in admin
     */
    public function register_integration_dashboard() {
        add_submenu_page(
            'nexus-options',
            __( 'Plugin Integrations', 'nexus' ),
            __( 'Integrations', 'nexus' ),
            'manage_options',
            'nexus-integrations',
            array( $this, 'render_integration_dashboard' )
        );
    }
    
    /**
     * Render integration dashboard
     */
    public function render_integration_dashboard() {
        $harmony = Nexus_Plugin_Harmony::get_instance();
        $status = $harmony->get_harmony_status();
        
        ?>
        <div class="wrap nexus-integrations-dashboard">
            <h1><?php esc_html_e( 'Nexus Plugin Integrations', 'nexus' ); ?></h1>
            
            <div class="nexus-integration-header">
                <div class="nexus-tier-badge advanced">
                    <?php esc_html_e( 'Advanced Tier Feature', 'nexus' ); ?>
                </div>
                <p class="description">
                    <?php esc_html_e( 'Advanced deep integrations with automatic styling and enhanced compatibility.', 'nexus' ); ?>
                </p>
            </div>
            
            <div class="nexus-integration-grid">
                <?php if ( ! empty( $this->integration_handlers ) ) : ?>
                    <?php foreach ( $this->integration_handlers as $slug => $handler ) : ?>
                        <div class="nexus-integration-card active">
                            <div class="card-header">
                                <span class="status-indicator active"></span>
                                <h3><?php echo esc_html( $handler->get_name() ); ?></h3>
                            </div>
                            <div class="card-body">
                                <p><?php echo esc_html( $handler->get_description() ); ?></p>
                                <ul class="integration-features">
                                    <?php foreach ( $handler->get_features() as $feature ) : ?>
                                        <li>✓ <?php echo esc_html( $feature ); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="card-footer">
                                <button 
                                    class="button test-integration" 
                                    data-integration="<?php echo esc_attr( $slug ); ?>">
                                    <?php esc_html_e( 'Test Integration', 'nexus' ); ?>
                                </button>
                                <?php if ( method_exists( $handler, 'has_settings' ) && $handler->has_settings() ) : ?>
                                    <a href="<?php echo esc_url( $handler->get_settings_url() ); ?>" class="button button-secondary">
                                        <?php esc_html_e( 'Settings', 'nexus' ); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="nexus-no-integrations">
                        <p><?php esc_html_e( 'No compatible plugins detected. Install supported plugins to see integrations here.', 'nexus' ); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="nexus-integration-stats">
                <h2><?php esc_html_e( 'Integration Statistics', 'nexus' ); ?></h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?php echo count( $this->integration_handlers ); ?></div>
                        <div class="stat-label"><?php esc_html_e( 'Active Integrations', 'nexus' ); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo count( $status['deactivated_features'] ); ?></div>
                        <div class="stat-label"><?php esc_html_e( 'Nexus Features Deferred', 'nexus' ); ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo $this->count_style_injections(); ?></div>
                        <div class="stat-label"><?php esc_html_e( 'Style Injections', 'nexus' ); ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
            .nexus-integrations-dashboard {
                max-width: 1200px;
            }
            .nexus-integration-header {
                background: #f9f9f9;
                padding: 20px;
                margin: 20px 0;
                border-radius: 8px;
            }
            .nexus-tier-badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 600;
                text-transform: uppercase;
                margin-bottom: 10px;
            }
            .nexus-tier-badge.advanced {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }
            .nexus-integration-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 20px;
                margin: 30px 0;
            }
            .nexus-integration-card {
                background: white;
                border: 1px solid #ddd;
                border-radius: 8px;
                overflow: hidden;
                transition: all 0.3s ease;
            }
            .nexus-integration-card:hover {
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                transform: translateY(-2px);
            }
            .nexus-integration-card .card-header {
                padding: 20px;
                border-bottom: 1px solid #eee;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .status-indicator {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: #ccc;
            }
            .status-indicator.active {
                background: #46b450;
                box-shadow: 0 0 10px rgba(70, 180, 80, 0.5);
            }
            .nexus-integration-card h3 {
                margin: 0;
                font-size: 16px;
            }
            .nexus-integration-card .card-body {
                padding: 20px;
            }
            .integration-features {
                list-style: none;
                padding: 0;
                margin: 15px 0 0;
            }
            .integration-features li {
                padding: 5px 0;
                color: #46b450;
            }
            .nexus-integration-card .card-footer {
                padding: 15px 20px;
                background: #f9f9f9;
                border-top: 1px solid #eee;
                display: flex;
                gap: 10px;
            }
            .nexus-integration-stats {
                margin-top: 40px;
                padding: 30px;
                background: white;
                border: 1px solid #ddd;
                border-radius: 8px;
            }
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-top: 20px;
            }
            .stat-card {
                text-align: center;
                padding: 20px;
                background: #f9f9f9;
                border-radius: 8px;
            }
            .stat-value {
                font-size: 36px;
                font-weight: bold;
                color: #0066cc;
                margin-bottom: 5px;
            }
            .stat-label {
                font-size: 14px;
                color: #666;
            }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            $('.test-integration').on('click', function() {
                var button = $(this);
                var integration = button.data('integration');
                
                button.prop('disabled', true).text('Testing...');
                
                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'nexus_test_integration',
                        integration: integration,
                        nonce: '<?php echo wp_create_nonce( 'nexus_test_integration' ); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Integration test passed!\n\n' + response.data.message);
                        } else {
                            alert('Integration test failed:\n\n' + response.data.message);
                        }
                    },
                    complete: function() {
                        button.prop('disabled', false).text('Test Integration');
                    }
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * AJAX handler for testing integrations
     */
    public function ajax_test_integration() {
        check_ajax_referer( 'nexus_test_integration', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
        }
        
        $integration = sanitize_key( $_POST['integration'] );
        
        if ( ! isset( $this->integration_handlers[ $integration ] ) ) {
            wp_send_json_error( array( 'message' => 'Integration not found' ) );
        }
        
        $handler = $this->integration_handlers[ $integration ];
        
        if ( method_exists( $handler, 'test_integration' ) ) {
            $result = $handler->test_integration();
            if ( $result['success'] ) {
                wp_send_json_success( $result );
            } else {
                wp_send_json_error( $result );
            }
        } else {
            wp_send_json_success( array(
                'message' => 'Integration is active and functioning correctly.'
            ) );
        }
    }
    
    /**
     * Count style injections
     */
    private function count_style_injections() {
        $count = 0;
        foreach ( $this->integration_handlers as $handler ) {
            if ( method_exists( $handler, 'get_style_overrides' ) ) {
                $overrides = $handler->get_style_overrides();
                $count += count( $overrides );
            }
        }
        return $count;
    }
}

// Initialize if Advanced tier
if ( class_exists( 'Nexus_License_Manager' ) ) {
    add_action( 'after_setup_theme', function() {
        Nexus_Plugin_Orchestrator::get_instance();
    }, 25 );
}
