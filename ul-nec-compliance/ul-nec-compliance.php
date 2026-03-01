<?php
/**
 * Plugin Name: PanelcheckPRO - UL/NEC Compliance Manager
 * Plugin URI: https://jdsancontrols.com
 * Description: Complete management system for UL-NEC Compliance AutoCAD Plugin - handles users, licensing, downloads, payments, and support.
 * Version: 1.4.7
 * Author: JDS & N Controls
 * Author URI: https://jdsancontrols.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ulnec
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('ULNEC_VERSION', '1.4.7');
define('ULNEC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ULNEC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ULNEC_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main UL-NEC Compliance Plugin Class
 */
final class ULNEC_Plugin {
    
    /**
     * Plugin instance
     */
    private static $instance = null;
    
    /**
     * Supabase instance
     */
    public $supabase = null;
    
    /**
     * Auth instance
     */
    public $auth = null;
    
    /**
     * License instance
     */
    public $license = null;
    
    /**
     * Download instance
     */
    public $download = null;
    
    /**
     * Payment instance
     */
    public $payment = null;
    
    /**
     * Emails instance
     */
    public $emails = null;
    
    /**
     * Get plugin instance
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    /**
     * Load required files
     */
    private function load_dependencies() {
        // Core classes
        require_once ULNEC_PLUGIN_DIR . 'includes/class-ulnec-supabase.php';
        require_once ULNEC_PLUGIN_DIR . 'includes/class-ulnec-auth.php';
        require_once ULNEC_PLUGIN_DIR . 'includes/class-ulnec-license.php';
        require_once ULNEC_PLUGIN_DIR . 'includes/class-ulnec-download.php';
        require_once ULNEC_PLUGIN_DIR . 'includes/class-ulnec-payment.php';
        require_once ULNEC_PLUGIN_DIR . 'includes/class-ulnec-emails.php';
        require_once ULNEC_PLUGIN_DIR . 'includes/class-ulnec-admin.php';
        require_once ULNEC_PLUGIN_DIR . 'includes/class-ulnec-frontend.php';
        require_once ULNEC_PLUGIN_DIR . 'includes/class-ulnec-frontend-pages.php';
        require_once ULNEC_PLUGIN_DIR . 'includes/class-ulnec-shortcodes.php';
        require_once ULNEC_PLUGIN_DIR . 'includes/class-ulnec-ajax.php';
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Initialize plugin
        add_action('plugins_loaded', [$this, 'init'], 10);
        
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        
        // Enqueue assets
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }
    
    /**
     * Initialize plugin components
     */
    public function init() {
        if ( get_option( 'ulnec_version' ) !== ULNEC_VERSION ) {
            update_option( 'ulnec_version', ULNEC_VERSION, false );
            update_option( 'ulnec_required_pages_ready', '0', false );
        }

        // Initialize Supabase connection
        $this->supabase = new ULNEC_Supabase();
        
        // Initialize other components
        $this->auth = new ULNEC_Auth($this->supabase);
        $this->license = new ULNEC_License($this->supabase);
        $this->download = new ULNEC_Download($this->supabase);
        $this->payment = new ULNEC_Payment($this->supabase);
        $this->emails = new ULNEC_Emails();
        
        // Initialize admin interface
        if (is_admin()) {
            new ULNEC_Admin($this->supabase);
        }
        
        // Initialize frontend
        new ULNEC_Frontend($this->supabase);
        
        // Initialize frontend pages (bug report, feature request, support)
        new ULNEC_Frontend_Pages($this->supabase);
        
        // Initialize shortcodes
        new ULNEC_Shortcodes($this->supabase);
        
        // Initialize AJAX handlers
        new ULNEC_Ajax($this->supabase);

        // Ensure required frontend pages exist.
        $this->ensure_required_pages();
        
        // Load text domain
        load_plugin_textdomain('ulnec', false, dirname(ULNEC_PLUGIN_BASENAME) . '/languages');
    }

    /**
     * Create required UL/NEC pages when missing.
     *
     * @return void
     */
    private function ensure_required_pages() {
        if ( get_option( 'ulnec_required_pages_ready', '0' ) === '1' ) {
            return;
        }

        $page_definitions = [
            'login' => [
                'title' => 'Login',
                'content' => '[ulnec_login]',
            ],
            'register' => [
                'title' => 'Register',
                'content' => '[ulnec_register]',
            ],
            'dashboard' => [
                'title' => 'Dashboard',
                'content' => '[ulnec_dashboard]',
            ],
            'download' => [
                'title' => 'Download',
                'content' => '[ulnec_download]',
            ],
            'bug-report' => [
                'title' => 'Bug Report',
                'content' => '[ulnec_bug_report]',
            ],
            'feature-request' => [
                'title' => 'Feature Request',
                'content' => '[ulnec_feature_request]',
            ],
            'billing' => [
                'title' => 'Billing',
                'content' => '[ulnec_billing]',
            ],
            'account-settings' => [
                'title' => 'Account Settings',
                'content' => '[ulnec_account_settings]',
            ],
            'founders-progress' => [
                'title' => 'Founders Progress',
                'content' => '[ulnec_founders_progress]',
            ],
        ];

        foreach ( $page_definitions as $slug => $definition ) {
            $existing = get_page_by_path( $slug, OBJECT, 'page' );

            if ( $existing instanceof WP_Post ) {
                continue;
            }

            wp_insert_post(
                [
                    'post_type' => 'page',
                    'post_status' => 'publish',
                    'post_title' => $definition['title'],
                    'post_name' => $slug,
                    'post_content' => $definition['content'],
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                ]
            );
        }

        update_option( 'ulnec_required_pages_ready', '1', false );
    }
    
    /**
     * Check if current user is a SaaS admin (Supabase-based)
     * This is separate from WordPress admin capabilities
     */
    public function is_saas_admin() {
        if (!is_user_logged_in()) {
            return false;
        }
        
        $current_user = wp_get_current_user();
        $email = $current_user->user_email;
        
        // Check Supabase for is_admin flag
        $response = $this->supabase->request('GET', 'ulnec_users?email=eq.' . urlencode($email) . '&select=is_admin');
        
        if (is_wp_error($response) || empty($response)) {
            return false;
        }
        
        return isset($response[0]['is_admin']) && $response[0]['is_admin'] === true;
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create necessary options
        add_option('ulnec_version', ULNEC_VERSION);
        add_option('ulnec_activated', current_time('mysql'));
        update_option('ulnec_required_pages_ready', '0', false);
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        if ( ! $this->should_load_frontend_assets() ) {
            return;
        }

        // CSS
        wp_enqueue_style(
            'ulnec-frontend',
            ULNEC_PLUGIN_URL . 'assets/css/frontend.css',
            [],
            ULNEC_VERSION
        );
        
        // JavaScript
        wp_enqueue_script(
            'ulnec-frontend',
            ULNEC_PLUGIN_URL . 'assets/js/frontend.js',
            ['jquery'],
            ULNEC_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('ulnec-frontend', 'ulnecData', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ulnec_nonce'),
            'strings' => [
                'error' => __('An error occurred. Please try again.', 'ulnec'),
                'success' => __('Success!', 'ulnec'),
            ]
        ]);
    }

    /**
     * Determine whether UL/NEC frontend assets should load on current request.
     *
     * @return bool
     */
    private function should_load_frontend_assets() {
        if ( is_admin() ) {
            return false;
        }

        $configured_page_ids = array_filter(
            array_map(
                'absint',
                array(
                    get_option( 'ulnec_page_pricing', 0 ),
                    get_option( 'ulnec_page_login', 0 ),
                    get_option( 'ulnec_page_register', 0 ),
                    get_option( 'ulnec_page_dashboard', 0 ),
                )
            )
        );

        $current_page_id = get_queried_object_id();
        if ( $current_page_id && in_array( (int) $current_page_id, $configured_page_ids, true ) ) {
            return true;
        }

        $ulnec_slugs = array(
            'login',
            'register',
            'dashboard',
            'download',
            'billing',
            'bug-report',
            'feature-request',
            'account-settings',
            'support',
            'founders-progress',
            'ul-nec-compliance-checker',
        );

        if ( is_page( $ulnec_slugs ) ) {
            return true;
        }

        if ( is_singular() ) {
            $post = get_post();

            if ( $post && ! empty( $post->post_content ) ) {
                $ulnec_shortcodes = array(
                    'ulnec_login',
                    'ulnec_register',
                    'ulnec_dashboard',
                    'ulnec_download',
                );

                foreach ( $ulnec_shortcodes as $shortcode ) {
                    if ( has_shortcode( $post->post_content, $shortcode ) ) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on plugin pages
        if (strpos($hook, 'ulnec') === false) {
            return;
        }
        
        // CSS
        wp_enqueue_style(
            'ulnec-admin',
            ULNEC_PLUGIN_URL . 'assets/css/admin.css',
            [],
            ULNEC_VERSION
        );
        
        // JavaScript
        wp_enqueue_script(
            'ulnec-admin',
            ULNEC_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            ULNEC_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('ulnec-admin', 'ulnecAdmin', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ulnec_admin_nonce'),
        ]);
    }
}

/**
 * Initialize the plugin
 */
function ulnec() {
    return ULNEC_Plugin::instance();
}

// Start the plugin
ulnec();
