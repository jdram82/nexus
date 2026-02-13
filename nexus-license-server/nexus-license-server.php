<?php
/**
 * Plugin Name: Nexus License Server
 * Plugin URI: https://jdsandigitel.com
 * Description: License server for Nexus Theme - manages license activation, validation, and tier management
 * Version: 1.0.0
 * Author: Jdsan Digitel
 * Author URI: https://jdsandigitel.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: nexus-license-server
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('NEXUS_LICENSE_SERVER_VERSION', '1.0.0');
define('NEXUS_LICENSE_SERVER_DIR', plugin_dir_path(__FILE__));
define('NEXUS_LICENSE_SERVER_URL', plugin_dir_url(__FILE__));

/**
 * Main License Server Class
 */
class Nexus_License_Server {
    
    /**
     * Singleton instance
     */
    private static $instance = null;
    
    /**
     * Database table name
     */
    private $table_name;
    
    /**
     * Tier constants
     */
    const TIER_PRO = 'pro';
    const TIER_ADVANCED = 'advanced';
    const TIER_AGENCY = 'agency';
    
    /**
     * Get instance
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
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'nexus_licenses';
        
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Init hooks
        add_action('plugins_loaded', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // Legacy API endpoints (query parameters)
        add_action('init', array($this, 'handle_legacy_api'));
        
        // REST API endpoints
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        
        // AJAX handlers
        add_action('wp_ajax_nls_generate_license', array($this, 'ajax_generate_license'));
        add_action('wp_ajax_nls_delete_license', array($this, 'ajax_delete_license'));
        add_action('wp_ajax_nls_update_license', array($this, 'ajax_update_license'));
        add_action('wp_ajax_nls_get_licenses', array($this, 'ajax_get_licenses'));
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        $this->create_tables();
        $this->insert_sample_data();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Intentionally empty - keep data on deactivation
    }
    
    /**
     * Create database tables
     */
    private function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            license_key varchar(100) NOT NULL,
            tier varchar(20) NOT NULL DEFAULT 'pro',
            status varchar(20) NOT NULL DEFAULT 'active',
            customer_email varchar(100) DEFAULT NULL,
            customer_name varchar(100) DEFAULT NULL,
            site_url varchar(255) DEFAULT NULL,
            activations int(11) NOT NULL DEFAULT 0,
            max_activations int(11) NOT NULL DEFAULT 1,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expires_at datetime DEFAULT NULL,
            last_validated datetime DEFAULT NULL,
            notes text DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY license_key (license_key),
            KEY status (status),
            KEY tier (tier),
            KEY customer_email (customer_email)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Insert sample license for testing
     */
    private function insert_sample_data() {
        global $wpdb;
        
        // Check if any licenses exist
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");
        
        if ($count > 0) {
            return; // Already have licenses
        }
        
        // Generate sample licenses
        $sample_licenses = array(
            array(
                'tier' => self::TIER_PRO,
                'customer_email' => 'sample@example.com',
                'customer_name' => 'Sample Pro User',
                'max_activations' => 1,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+1 year')),
            ),
            array(
                'tier' => self::TIER_ADVANCED,
                'customer_email' => 'advanced@example.com',
                'customer_name' => 'Sample Advanced User',
                'max_activations' => 3,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+1 year')),
            ),
            array(
                'tier' => self::TIER_AGENCY,
                'customer_email' => 'agency@example.com',
                'customer_name' => 'Sample Agency User',
                'max_activations' => 999, // Unlimited
                'expires_at' => null, // Lifetime
            ),
        );
        
        foreach ($sample_licenses as $license) {
            $this->generate_license(
                $license['tier'],
                $license['customer_email'],
                $license['customer_name'],
                $license['max_activations'],
                $license['expires_at']
            );
        }
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        load_plugin_textdomain('nexus-license-server', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Nexus Licenses', 'nexus-license-server'),
            __('Nexus Licenses', 'nexus-license-server'),
            'manage_options',
            'nexus-licenses',
            array($this, 'render_admin_page'),
            'dashicons-admin-network',
            30
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_nexus-licenses' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'nexus-license-server-admin',
            NEXUS_LICENSE_SERVER_URL . 'assets/admin.css',
            array(),
            NEXUS_LICENSE_SERVER_VERSION
        );
        
        wp_enqueue_script(
            'nexus-license-server-admin',
            NEXUS_LICENSE_SERVER_URL . 'assets/admin.js',
            array('jquery'),
            NEXUS_LICENSE_SERVER_VERSION,
            true
        );
        
        wp_localize_script('nexus-license-server-admin', 'nlsData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nls_admin'),
        ));
    }
    
    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('nexus-licenses/v1', '/activate', array(
            'methods' => 'POST',
            'callback' => array($this, 'api_activate_license'),
            'permission_callback' => '__return_true',
        ));
        
        register_rest_route('nexus-licenses/v1', '/validate', array(
            'methods' => 'POST',
            'callback' => array($this, 'api_validate_license'),
            'permission_callback' => '__return_true',
        ));
        
        register_rest_route('nexus-licenses/v1', '/deactivate', array(
            'methods' => 'POST',
            'callback' => array($this, 'api_deactivate_license'),
            'permission_callback' => '__return_true',
        ));
    }
    
    /**
     * Handle legacy API (query parameters)
     */
    public function handle_legacy_api() {
        if (!isset($_GET['nexus_api_action'])) {
            return;
        }
        
        $action = sanitize_text_field($_GET['nexus_api_action']);
        
        switch ($action) {
            case 'activate':
                $result = $this->api_activate_license(new WP_REST_Request('POST', ''));
                break;
            case 'validate':
                $result = $this->api_validate_license(new WP_REST_Request('POST', ''));
                break;
            case 'deactivate':
                $result = $this->api_deactivate_license(new WP_REST_Request('POST', ''));
                break;
            default:
                $result = array('success' => false, 'message' => 'Invalid action');
        }
        
        wp_send_json($result);
    }
    
    /**
     * API: Activate license
     */
    public function api_activate_license($request) {
        global $wpdb;
        
        $license_key = isset($_POST['license_key']) ? sanitize_text_field($_POST['license_key']) : '';
        $site_url = isset($_POST['site_url']) ? esc_url_raw($_POST['site_url']) : '';
        
        if (empty($license_key) || empty($site_url)) {
            return array(
                'success' => false,
                'message' => 'License key and site URL are required',
            );
        }
        
        // Get license from database
        $license = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE license_key = %s",
            $license_key
        ));
        
        if (!$license) {
            return array(
                'success' => false,
                'message' => 'Invalid license key',
            );
        }
        
        // Check if license is active
        if ($license->status !== 'active') {
            return array(
                'success' => false,
                'message' => 'License is ' . $license->status,
            );
        }
        
        // Check expiration
        if ($license->expires_at && strtotime($license->expires_at) < time()) {
            return array(
                'success' => false,
                'message' => 'License has expired',
            );
        }
        
        // Check activation limit
        if ($license->activations >= $license->max_activations && $license->max_activations < 999) {
            // Check if this site is already activated
            if ($license->site_url !== $site_url) {
                return array(
                    'success' => false,
                    'message' => 'License activation limit reached. Deactivate from another site first.',
                );
            }
        }
        
        // Update license
        $wpdb->update(
            $this->table_name,
            array(
                'site_url' => $site_url,
                'activations' => $license->activations + 1,
                'last_validated' => current_time('mysql'),
            ),
            array('id' => $license->id),
            array('%s', '%d', '%s'),
            array('%d')
        );
        
        return array(
            'success' => true,
            'tier' => $license->tier,
            'expires' => $license->expires_at ? strtotime($license->expires_at) : 0,
            'message' => 'License activated successfully',
        );
    }
    
    /**
     * API: Validate license
     */
    public function api_validate_license($request) {
        global $wpdb;
        
        $license_key = isset($_POST['license_key']) ? sanitize_text_field($_POST['license_key']) : '';
        $site_url = isset($_POST['site_url']) ? esc_url_raw($_POST['site_url']) : '';
        
        if (empty($license_key)) {
            return array(
                'valid' => false,
                'message' => 'License key is required',
            );
        }
        
        // Get license from database
        $license = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE license_key = %s",
            $license_key
        ));
        
        if (!$license) {
            return array(
                'valid' => false,
                'message' => 'Invalid license key',
            );
        }
        
        // Check status
        if ($license->status !== 'active') {
            return array(
                'valid' => false,
                'message' => 'License is ' . $license->status,
            );
        }
        
        // Check expiration
        if ($license->expires_at && strtotime($license->expires_at) < time()) {
            return array(
                'valid' => false,
                'message' => 'License has expired',
            );
        }
        
        // Check if site matches (optional - for single site licenses)
        if ($site_url && $license->site_url && $license->site_url !== $site_url && $license->max_activations < 999) {
            return array(
                'valid' => false,
                'message' => 'License is activated on a different site',
            );
        }
        
        // Update last validated timestamp
        $wpdb->update(
            $this->table_name,
            array('last_validated' => current_time('mysql')),
            array('id' => $license->id),
            array('%s'),
            array('%d')
        );
        
        return array(
            'valid' => true,
            'tier' => $license->tier,
            'expires' => $license->expires_at ? strtotime($license->expires_at) : 0,
            'message' => 'License is valid',
        );
    }
    
    /**
     * API: Deactivate license
     */
    public function api_deactivate_license($request) {
        global $wpdb;
        
        $license_key = isset($_POST['license_key']) ? sanitize_text_field($_POST['license_key']) : '';
        $site_url = isset($_POST['site_url']) ? esc_url_raw($_POST['site_url']) : '';
        
        if (empty($license_key)) {
            return array(
                'success' => false,
                'message' => 'License key is required',
            );
        }
        
        // Get license from database
        $license = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE license_key = %s",
            $license_key
        ));
        
        if (!$license) {
            return array(
                'success' => false,
                'message' => 'Invalid license key',
            );
        }
        
        // Clear site URL and decrease activation count
        $wpdb->update(
            $this->table_name,
            array(
                'site_url' => null,
                'activations' => max(0, $license->activations - 1),
            ),
            array('id' => $license->id),
            array('%s', '%d'),
            array('%d')
        );
        
        return array(
            'success' => true,
            'message' => 'License deactivated successfully',
        );
    }
    
    /**
     * Generate new license key
     */
    public function generate_license($tier, $customer_email, $customer_name, $max_activations = 1, $expires_at = null) {
        global $wpdb;
        
        // Generate unique license key
        $license_key = $this->create_license_key();
        
        // Insert into database
        $result = $wpdb->insert(
            $this->table_name,
            array(
                'license_key' => $license_key,
                'tier' => $tier,
                'status' => 'active',
                'customer_email' => $customer_email,
                'customer_name' => $customer_name,
                'max_activations' => $max_activations,
                'expires_at' => $expires_at,
                'created_at' => current_time('mysql'),
            ),
            array('%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s')
        );
        
        if ($result) {
            return $license_key;
        }
        
        return false;
    }
    
    /**
     * Create unique license key
     */
    private function create_license_key() {
        global $wpdb;
        
        do {
            // Generate format: XXXX-XXXX-XXXX-XXXX
            $key = sprintf(
                '%s-%s-%s-%s',
                $this->random_string(4),
                $this->random_string(4),
                $this->random_string(4),
                $this->random_string(4)
            );
            
            // Check if exists
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_name} WHERE license_key = %s",
                $key
            ));
        } while ($exists > 0);
        
        return $key;
    }
    
    /**
     * Generate random string
     */
    private function random_string($length = 8) {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Excluded: I, O, 0, 1
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $string;
    }
    
    /**
     * AJAX: Generate license
     */
    public function ajax_generate_license() {
        check_ajax_referer('nls_admin', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        $tier = isset($_POST['tier']) ? sanitize_text_field($_POST['tier']) : 'pro';
        $customer_email = isset($_POST['customer_email']) ? sanitize_email($_POST['customer_email']) : '';
        $customer_name = isset($_POST['customer_name']) ? sanitize_text_field($_POST['customer_name']) : '';
        $max_activations = isset($_POST['max_activations']) ? intval($_POST['max_activations']) : 1;
        $expires_at = isset($_POST['expires_at']) ? sanitize_text_field($_POST['expires_at']) : null;
        
        $license_key = $this->generate_license($tier, $customer_email, $customer_name, $max_activations, $expires_at);
        
        if ($license_key) {
            wp_send_json_success(array(
                'message' => 'License generated successfully',
                'license_key' => $license_key,
            ));
        } else {
            wp_send_json_error(array('message' => 'Failed to generate license'));
        }
    }
    
    /**
     * AJAX: Get licenses
     */
    public function ajax_get_licenses() {
        check_ajax_referer('nls_admin', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        global $wpdb;
        
        $licenses = $wpdb->get_results(
            "SELECT * FROM {$this->table_name} ORDER BY created_at DESC"
        );
        
        wp_send_json_success(array('licenses' => $licenses));
    }
    
    /**
     * AJAX: Delete license
     */
    public function ajax_delete_license() {
        check_ajax_referer('nls_admin', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        $license_id = isset($_POST['license_id']) ? intval($_POST['license_id']) : 0;
        
        if (!$license_id) {
            wp_send_json_error(array('message' => 'Invalid license ID'));
        }
        
        global $wpdb;
        $result = $wpdb->delete(
            $this->table_name,
            array('id' => $license_id),
            array('%d')
        );
        
        if ($result) {
            wp_send_json_success(array('message' => 'License deleted'));
        } else {
            wp_send_json_error(array('message' => 'Failed to delete license'));
        }
    }
    
    /**
     * AJAX: Update license
     */
    public function ajax_update_license() {
        check_ajax_referer('nls_admin', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        $license_id = isset($_POST['license_id']) ? intval($_POST['license_id']) : 0;
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        
        if (!$license_id || !$status) {
            wp_send_json_error(array('message' => 'Invalid data'));
        }
        
        global $wpdb;
        $result = $wpdb->update(
            $this->table_name,
            array('status' => $status),
            array('id' => $license_id),
            array('%s'),
            array('%d')
        );
        
        if ($result !== false) {
            wp_send_json_success(array('message' => 'License updated'));
        } else {
            wp_send_json_error(array('message' => 'Failed to update license'));
        }
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        include NEXUS_LICENSE_SERVER_DIR . 'templates/admin-page.php';
    }
}

// Initialize plugin
function nexus_license_server() {
    return Nexus_License_Server::instance();
}

// Start
nexus_license_server();
