<?php
/**
 * Nexus Plugin Harmony Architecture (Free Tier)
 * 
 * Intelligent plugin detection and graceful feature degradation.
 * Ensures Nexus works harmoniously with popular WordPress plugins.
 * 
 * @package Nexus_Theme
 * @subpackage Plugin_Harmony
 * @since 1.4.0
 * @tier Free
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Nexus_Plugin_Harmony {
    
    /**
     * Singleton instance
     */
    private static $instance = null;
    
    /**
     * Plugin detection map
     * Maps Nexus features to popular third-party plugins
     */
    private $plugin_map = array(
        'forms' => array(
            'gravity-forms/gravityforms.php' => 'Gravity Forms',
            'wpforms-lite/wpforms.php' => 'WPForms Lite',
            'wpforms/wpforms.php' => 'WPForms',
            'contact-form-7/wp-contact-form-7.php' => 'Contact Form 7',
            'formidable/formidable.php' => 'Formidable Forms',
            'ninja-forms/ninja-forms.php' => 'Ninja Forms',
        ),
        'seo' => array(
            'wordpress-seo/wp-seo.php' => 'Yoast SEO',
            'seo-by-rank-math/rank-math.php' => 'Rank Math',
            'all-in-one-seo-pack/all_in_one_seo_pack.php' => 'All in One SEO',
            'autodescription/autodescription.php' => 'The SEO Framework',
        ),
        'analytics' => array(
            'google-analytics-for-wordpress/googleanalytics.php' => 'MonsterInsights',
            'google-analytics-dashboard-for-wp/gadwp.php' => 'ExactMetrics',
            'ga-google-analytics/ga-google-analytics.php' => 'GA Google Analytics',
        ),
        'performance' => array(
            'wp-rocket/wp-rocket.php' => 'WP Rocket',
            'w3-total-cache/w3-total-cache.php' => 'W3 Total Cache',
            'wp-super-cache/wp-cache.php' => 'WP Super Cache',
            'autoptimize/autoptimize.php' => 'Autoptimize',
            'wp-optimize/wp-optimize.php' => 'WP-Optimize',
        ),
        'security' => array(
            'wordfence/wordfence.php' => 'Wordfence',
            'better-wp-security/better-wp-security.php' => 'iThemes Security',
            'all-in-one-wp-security-and-firewall/wp-security.php' => 'All In One WP Security',
        ),
        'ecommerce' => array(
            'woocommerce/woocommerce.php' => 'WooCommerce',
            'easy-digital-downloads/easy-digital-downloads.php' => 'Easy Digital Downloads',
        ),
    );
    
    /**
     * Detected plugins storage
     */
    private $detected_plugins = array();
    
    /**
     * Deactivated features storage
     */
    private $deactivated_features = array();
    
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
        add_action( 'after_setup_theme', array( $this, 'detect_and_harmonize' ), 20 );
        add_action( 'admin_notices', array( $this, 'show_harmony_notices' ) );
        add_filter( 'nexus_available_features', array( $this, 'filter_available_features' ) );
    }
    
    /**
     * Main detection and harmonization routine
     */
    public function detect_and_harmonize() {
        // Detect all active third-party plugins
        $this->detect_active_plugins();
        
        // Gracefully deactivate conflicting Nexus features
        $this->deactivate_conflicting_features();
        
        // Apply basic styling compatibility
        $this->apply_styling_compatibility();
        
        // Store results in transient for performance
        set_transient( 'nexus_plugin_harmony_state', array(
            'detected' => $this->detected_plugins,
            'deactivated' => $this->deactivated_features,
        ), DAY_IN_SECONDS );
    }
    
    /**
     * Detect all active third-party plugins
     */
    private function detect_active_plugins() {
        foreach ( $this->plugin_map as $category => $plugins ) {
            foreach ( $plugins as $plugin_file => $plugin_name ) {
                if ( $this->is_plugin_active( $plugin_file ) ) {
                    $this->detected_plugins[ $category ] = array(
                        'file' => $plugin_file,
                        'name' => $plugin_name,
                        'category' => $category,
                    );
                    break; // Only detect one plugin per category
                }
            }
        }
    }
    
    /**
     * Check if a plugin is active
     */
    private function is_plugin_active( $plugin_file ) {
        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        return is_plugin_active( $plugin_file );
    }
    
    /**
     * Deactivate conflicting Nexus features
     */
    private function deactivate_conflicting_features() {
        foreach ( $this->detected_plugins as $category => $plugin_data ) {
            // Deactivate Nexus native feature for this category
            $this->deactivated_features[] = $category;
            
            // Apply specific deactivation logic
            switch ( $category ) {
                case 'forms':
                    remove_action( 'init', array( 'Nexus_Form_Builder', 'init' ) );
                    add_filter( 'nexus_show_form_builder', '__return_false' );
                    break;
                    
                case 'seo':
                    remove_action( 'init', array( 'Nexus_SEO', 'init' ) );
                    add_filter( 'nexus_show_seo_tools', '__return_false' );
                    break;
                    
                case 'analytics':
                    remove_action( 'wp_footer', array( 'Nexus_Analytics', 'render_tracking' ) );
                    add_filter( 'nexus_show_analytics', '__return_false' );
                    break;
                    
                case 'performance':
                    // Keep Nexus performance features but disable caching
                    add_filter( 'nexus_enable_caching', '__return_false' );
                    break;
                    
                case 'ecommerce':
                    // Nexus WooCommerce integration stays active
                    // This is enhancement, not replacement
                    break;
            }
        }
    }
    
    /**
     * Apply basic styling compatibility (Pro tier)
     */
    private function apply_styling_compatibility() {
        foreach ( $this->detected_plugins as $category => $plugin_data ) {
            $plugin_slug = sanitize_title( $plugin_data['name'] );
            
            // Enqueue compatibility CSS if it exists
            $compat_css = get_template_directory() . '/assets/dist/css/compat/' . $plugin_slug . '.css';
            if ( file_exists( $compat_css ) ) {
                add_action( 'wp_enqueue_scripts', function() use ( $plugin_slug ) {
                    wp_enqueue_style(
                        'nexus-compat-' . $plugin_slug,
                        get_template_directory_uri() . '/assets/dist/css/compat/' . $plugin_slug . '.css',
                        array( 'nexus-main' ),
                        NEXUS_VERSION
                    );
                }, 20 );
            }
            
            // Add body class for CSS targeting
            add_filter( 'body_class', function( $classes ) use ( $plugin_slug ) {
                $classes[] = 'nexus-compat-' . $plugin_slug;
                return $classes;
            } );
        }
    }
    
    /**
     * Show harmony notices in admin
     */
    public function show_harmony_notices() {
        if ( empty( $this->detected_plugins ) ) {
            return;
        }
        
        $screen = get_current_screen();
        if ( ! $screen || 'appearance_page_nexus-options' !== $screen->id ) {
            return;
        }
        
        ?>
        <div class="notice notice-success is-dismissible">
            <h3><?php esc_html_e( 'Nexus Plugin Harmony Active', 'nexus' ); ?></h3>
            <p><?php esc_html_e( 'Nexus has detected the following plugins and automatically adjusted to work harmoniously:', 'nexus' ); ?></p>
            <ul style="list-style: disc; margin-left: 20px;">
                <?php foreach ( $this->detected_plugins as $category => $plugin_data ) : ?>
                    <li>
                        <strong><?php echo esc_html( $plugin_data['name'] ); ?></strong> 
                        - <?php echo esc_html( ucfirst( $category ) ); ?>
                        <em>(Nexus native <?php echo esc_html( $category ); ?> feature deactivated)</em>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p>
                <a href="<?php echo esc_url( admin_url( 'appearance.php?page=nexus-plugin-harmony' ) ); ?>" class="button button-primary">
                    <?php esc_html_e( 'View Plugin Harmony Dashboard', 'nexus' ); ?>
                </a>
            </p>
        </div>
        <?php
    }
    
    /**
     * Filter available features based on detected plugins
     */
    public function filter_available_features( $features ) {
        foreach ( $this->deactivated_features as $feature ) {
            unset( $features[ $feature ] );
        }
        return $features;
    }
    
    /**
     * Get harmony status for API/external use
     */
    public function get_harmony_status() {
        return array(
            'detected_plugins' => $this->detected_plugins,
            'deactivated_features' => $this->deactivated_features,
            'harmony_active' => ! empty( $this->detected_plugins ),
        );
    }
    
    /**
     * Check if a specific feature is available
     */
    public function is_feature_available( $feature ) {
        return ! in_array( $feature, $this->deactivated_features, true );
    }
}

// Initialize
Nexus_Plugin_Harmony::get_instance();
