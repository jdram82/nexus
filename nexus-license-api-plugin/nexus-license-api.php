<?php
/**
 * Plugin Name: Nexus License API
 * Plugin URI: https://jdsandigitel.com
 * Description: Custom license API for Nexus Theme that works with Software License Manager
 * Version: 1.0.0
 * Author: jdsan Digitel
 * Author URI: https://jdsandigitel.com
 * License: GPL v2 or later
 * Text Domain: nexus-license-api
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Nexus_License_API {
    
    private $secret_creation = '6951ee21aaed19.50210993';
    private $secret_verification = '6951ee21aaed88.33650597';
    
    public function __construct() {
        // Register REST API routes
        add_action('rest_api_init', array($this, 'register_routes'));
        
        // Also register legacy endpoints (in case REST API is blocked)
        add_action('init', array($this, 'register_legacy_endpoints'));
        
        // Auto-create licenses when WooCommerce order is completed
        add_action('woocommerce_order_status_completed', array($this, 'auto_create_license'), 10, 1);
        add_action('woocommerce_order_status_processing', array($this, 'auto_create_license'), 10, 1);
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        // API info endpoint
        register_rest_route('nexus-licenses/v1', '/info', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_api_info'),
            'permission_callback' => '__return_true',
        ));
        
        // Activate license
        register_rest_route('nexus-licenses/v1', '/activate', array(
            'methods' => 'POST',
            'callback' => array($this, 'activate_license'),
            'permission_callback' => '__return_true',
        ));
        
        // Validate license
        register_rest_route('nexus-licenses/v1', '/validate', array(
            'methods' => 'POST',
            'callback' => array($this, 'validate_license'),
            'permission_callback' => '__return_true',
        ));
        
        // Deactivate license
        register_rest_route('nexus-licenses/v1', '/deactivate', array(
            'methods' => 'POST',
            'callback' => array($this, 'deactivate_license'),
            'permission_callback' => '__return_true',
        ));
        
        // Check for updates
        register_rest_route('nexus-licenses/v1', '/check-update', array(
            'methods' => 'POST',
            'callback' => array($this, 'check_update'),
            'permission_callback' => '__return_true',
        ));
    }
    
    /**
     * Register legacy endpoints (if REST API is blocked)
     */
    public function register_legacy_endpoints() {
        if (isset($_GET['nexus_api_action'])) {
            $action = sanitize_text_field($_GET['nexus_api_action']);
            
            switch ($action) {
                case 'info':
                    $this->output_json($this->get_api_info(null));
                    break;
                case 'activate':
                    $this->output_json($this->activate_license(null));
                    break;
                case 'validate':
                    $this->output_json($this->validate_license(null));
                    break;
                case 'deactivate':
                    $this->output_json($this->deactivate_license(null));
                    break;
                case 'check_update':
                    $this->output_json($this->check_update(null));
                    break;
            }
        }
    }
    
    /**
     * Output JSON and exit
     */
    private function output_json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Get API info
     */
    public function get_api_info($request) {
        return array(
            'name' => 'Nexus License API',
            'version' => '1.0.0',
            'server' => get_site_url(),
            'timestamp' => current_time('timestamp'),
            'endpoints' => array(
                'info' => get_rest_url(null, 'nexus-licenses/v1/info'),
                'activate' => get_rest_url(null, 'nexus-licenses/v1/activate'),
                'validate' => get_rest_url(null, 'nexus-licenses/v1/validate'),
                'deactivate' => get_rest_url(null, 'nexus-licenses/v1/deactivate'),
                'check_update' => get_rest_url(null, 'nexus-licenses/v1/check-update'),
            ),
            'legacy_endpoints' => array(
                'info' => get_site_url() . '/?nexus_api_action=info',
                'activate' => get_site_url() . '/?nexus_api_action=activate',
                'validate' => get_site_url() . '/?nexus_api_action=validate',
                'deactivate' => get_site_url() . '/?nexus_api_action=deactivate',
                'check_update' => get_site_url() . '/?nexus_api_action=check_update',
            ),
            'status' => 'active',
        );
    }
    
    /**
     * Get request data (works for both REST and legacy)
     */
    private function get_request_data($request) {
        if ($request) {
            // REST API request
            return array(
                'license_key' => sanitize_text_field($request->get_param('license_key')),
                'site_url' => sanitize_text_field($request->get_param('site_url')),
                'item_reference' => sanitize_text_field($request->get_param('item_reference')),
            );
        } else {
            // Legacy request
            return array(
                'license_key' => sanitize_text_field($_REQUEST['license_key'] ?? ''),
                'site_url' => sanitize_text_field($_REQUEST['site_url'] ?? ''),
                'item_reference' => sanitize_text_field($_REQUEST['item_reference'] ?? 'nexus-theme'),
            );
        }
    }
    
    /**
     * Activate license
     */
    public function activate_license($request) {
        global $wpdb;
        
        $data = $this->get_request_data($request);
        $license_key = $data['license_key'];
        $site_url = $data['site_url'];
        
        if (empty($license_key) || empty($site_url)) {
            return new WP_Error('missing_params', 'License key and site URL are required', array('status' => 400));
        }
        
        // Get license from database
        $table_name = $wpdb->prefix . 'lic_key_tbl';
        $license = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE license_key = %s",
            $license_key
        ));
        
        if (!$license) {
            return array(
                'success' => false,
                'error' => 'invalid_license',
                'message' => 'Invalid license key',
            );
        }
        
        // Check if license is expired
        if ($license->date_expiry && strtotime($license->date_expiry) < time()) {
            return array(
                'success' => false,
                'error' => 'expired_license',
                'message' => 'License has expired',
                'expires' => $license->date_expiry,
            );
        }
        
        // Check domain limit
        $current_domains = $this->get_registered_domains($license_key);
        $max_domains = $license->max_allowed_domains ?? 1;
        
        if (!in_array($site_url, $current_domains) && count($current_domains) >= $max_domains) {
            return array(
                'success' => false,
                'error' => 'domain_limit_reached',
                'message' => 'Maximum number of domains reached',
                'max_domains' => $max_domains,
            );
        }
        
        // Add domain if not already registered
        if (!in_array($site_url, $current_domains)) {
            $reg_table = $wpdb->prefix . 'lic_reg_domain_tbl';
            $wpdb->insert($reg_table, array(
                'lic_key_id' => $license->id,
                'lic_key' => $license_key,
                'registered_domain' => $site_url,
                'registered_date' => current_time('mysql'),
            ));
        }
        
        // Get license tier
        $tier = $this->get_license_tier($license);
        
        return array(
            'success' => true,
            'message' => 'License activated successfully',
            'tier' => $tier,
            'expires' => $license->date_expiry,
            'max_domains' => $max_domains,
            'active_domains' => count($current_domains) + (in_array($site_url, $current_domains) ? 0 : 1),
        );
    }
    
    /**
     * Validate license
     */
    public function validate_license($request) {
        global $wpdb;
        
        $data = $this->get_request_data($request);
        $license_key = $data['license_key'];
        $site_url = $data['site_url'];
        
        if (empty($license_key)) {
            return array(
                'success' => false,
                'status' => 'inactive',
                'tier' => 'free',
            );
        }
        
        // Get license from database
        $table_name = $wpdb->prefix . 'lic_key_tbl';
        $license = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE license_key = %s",
            $license_key
        ));
        
        if (!$license) {
            return array(
                'success' => false,
                'status' => 'invalid',
                'tier' => 'free',
            );
        }
        
        // Check if expired
        if ($license->date_expiry && strtotime($license->date_expiry) < time()) {
            return array(
                'success' => false,
                'status' => 'expired',
                'tier' => 'free',
                'expires' => $license->date_expiry,
            );
        }
        
        // Check if domain is registered (if site_url provided)
        if (!empty($site_url)) {
            $current_domains = $this->get_registered_domains($license_key);
            if (!in_array($site_url, $current_domains)) {
                return array(
                    'success' => false,
                    'status' => 'not_activated',
                    'tier' => 'free',
                    'message' => 'License not activated for this domain',
                );
            }
        }
        
        // Get license tier
        $tier = $this->get_license_tier($license);
        
        return array(
            'success' => true,
            'status' => 'active',
            'tier' => $tier,
            'expires' => $license->date_expiry,
            'max_domains' => $license->max_allowed_domains ?? 1,
        );
    }
    
    /**
     * Deactivate license
     */
    public function deactivate_license($request) {
        global $wpdb;
        
        $data = $this->get_request_data($request);
        $license_key = $data['license_key'];
        $site_url = $data['site_url'];
        
        if (empty($license_key) || empty($site_url)) {
            return new WP_Error('missing_params', 'License key and site URL are required', array('status' => 400));
        }
        
        // Remove domain registration
        $reg_table = $wpdb->prefix . 'lic_reg_domain_tbl';
        $deleted = $wpdb->delete($reg_table, array(
            'lic_key' => $license_key,
            'registered_domain' => $site_url,
        ));
        
        if ($deleted) {
            return array(
                'success' => true,
                'message' => 'License deactivated successfully',
            );
        } else {
            return array(
                'success' => false,
                'error' => 'not_found',
                'message' => 'License not found for this domain',
            );
        }
    }
    
    /**
     * Check for theme updates
     */
    public function check_update($request) {
        $data = $this->get_request_data($request);
        $license_key = $data['license_key'];
        
        // Validate license first
        $validation = $this->validate_license($request);
        
        if (!$validation['success']) {
            return $validation;
        }
        
        // Get latest version from GitHub
        $github_response = wp_remote_get('https://api.github.com/repos/jdram82/nexus/releases/latest');
        
        if (is_wp_error($github_response)) {
            return array(
                'success' => false,
                'error' => 'github_error',
                'message' => 'Could not check for updates',
            );
        }
        
        $release = json_decode(wp_remote_retrieve_body($github_response), true);
        
        return array(
            'success' => true,
            'version' => ltrim($release['tag_name'] ?? '1.0.0', 'v'),
            'download_url' => $release['zipball_url'] ?? '',
            'changelog' => $release['body'] ?? '',
            'release_date' => $release['published_at'] ?? '',
        );
    }
    
    /**
     * Get registered domains for a license
     */
    private function get_registered_domains($license_key) {
        global $wpdb;
        
        $reg_table = $wpdb->prefix . 'lic_reg_domain_tbl';
        $domains = $wpdb->get_col($wpdb->prepare(
            "SELECT registered_domain FROM {$reg_table} WHERE lic_key = %s",
            $license_key
        ));
        
        return $domains;
    }
    
    /**
     * Get license tier from product SKU or license type
     */
    private function get_license_tier($license) {
        // Try to get from product first
        if (!empty($license->product_ref)) {
            $product_id = $license->product_ref;
            $product = wc_get_product($product_id);
            
            if ($product) {
                $sku = strtolower($product->get_sku());
                
                if (strpos($sku, 'agency') !== false) {
                    return 'agency';
                } elseif (strpos($sku, 'advanced') !== false) {
                    return 'advanced';
                } elseif (strpos($sku, 'pro') !== false) {
                    return 'pro';
                }
            }
        }
        
        // Fallback to lic_type field
        if (!empty($license->lic_type)) {
            $type = strtolower($license->lic_type);
            if (in_array($type, array('agency', 'advanced', 'pro'))) {
                return $type;
            }
     
    
    /**
     * Auto-create license when WooCommerce order is completed
     */
    public function auto_create_license($order_id) {
        global $wpdb;
        
        // Get the order
        $order = wc_get_order($order_id);
        
        if (!$order) {
            return;
        }
        
        // Check if license already created for this order
        $table_name = $wpdb->prefix . 'lic_key_tbl';
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} WHERE product_ref = %s",
            'order_' . $order_id
        ));
        
        if ($existing > 0) {
            // License already created, skip
            return;
        }
        
        // Get order items
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            
            if (!$product) {
                continue;
            }
            
            $sku = strtolower($product->get_sku());
            
            // Skip free tier
            if (strpos($sku, 'free') !== false) {
                continue;
            }
            
            // Determine license settings based on SKU
            $license_settings = $this->get_license_settings_from_sku($sku);
            
            if (!$license_settings) {
                continue; // Not a Nexus product
            }
            
            // Generate license key
            $license_key = $this->generate_license_key();
            
            // Calculate expiry date
            $expiry_date = date('Y-m-d H:i:s', strtotime('+' . $license_settings['validity'] . ' days'));
            
            // Insert license into database
            $wpdb->insert($table_name, array(
                'license_key' => $license_key,
                'lic_type' => $license_settings['type'],
                'first_name' => $order->get_billing_first_name(),
                'last_name' => $order->get_billing_last_name(),
                'email' => $order->get_billing_email(),
                'company_name' => $order->get_billing_company(),
                'product_ref' => 'order_' . $order_id,
                'max_allowed_domains' => $license_settings['max_domains'],
                'date_created' => current_time('mysql'),
                'date_expiry' => $expiry_date,
            ));
            
            // Add order note
            $order->add_order_note(
                sprintf(
                    'License auto-created: %s (Type: %s, Max Domains: %d)',
                    $license_key,
                    $license_settings['type'],
                    $license_settings['max_domains']
                )
            );
            
            // Add license key to order meta for easy access
            $order->update_meta_data('_nexus_license_key', $license_key);
            $order->update_meta_data('_nexus_license_tier', $license_settings['type']);
            $order->save();
            
            // Send email to customer (optional - you can customize this)
            $this->send_license_email($order, $license_key, $license_settings);
        }
    }
    
    /**
     * Get license settings based on product SKU
     */
    private function get_license_settings_from_sku($sku) {
        $settings_map = array(
            'nexus-pro' => array(
                'type' => 'pro',
                'max_domains' => 1,
                'validity' => 365,
            ),
            'nexus-advanced' => array(
                'type' => 'advanced',
                'max_domains' => 3,
                'validity' => 365,
            ),
            'nexus-agency' => array(
                'type' => 'agency',
                'max_domains' => 999,
                'validity' => 365,
            ),
        );
        
        // Check exact match first
        if (isset($settings_map[$sku])) {
            return $settings_map[$sku];
        }
        
        // Check partial match
        foreach ($settings_map as $key => $settings) {
            if (strpos($sku, $key) !== false) {
                return $settings;
            }
        }
        
        return false;
    }
    
    /**
     * Generate a random license key with NEXUS- prefix
     */
    private function generate_license_key() {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Removed confusing chars
        $segments = 4;
        $segment_length = 4;
        
        $key_parts = array('NEXUS');
        
        for ($i = 0; $i < $segments; $i++) {
            $segment = '';
            for ($j = 0; $j < $segment_length; $j++) {
                $segment .= $characters[rand(0, strlen($characters) - 1)];
            }
            $key_parts[] = $segment;
        }
        
        return implode('-', $key_parts);
    }
    
    /**
     * Send license email to customer
     */
    private function send_license_email($order, $license_key, $settings) {
        $to = $order->get_billing_email();
        $subject = 'Your Nexus Theme License Key';
        
        $message = sprintf(
            "Hi %s,\n\n" .
            "Thank you for purchasing Nexus %s!\n\n" .
            "Your License Key: %s\n\n" .
            "License Details:\n" .
            "- Tier: %s\n" .
            "- Max Domains: %d\n" .
            "- Valid Until: %s\n\n" .
            "To activate your license:\n" .
            "1. Install Nexus theme on your WordPress site\n" .
            "2. Go to Dashboard → Nexus → License\n" .
            "3. Enter your license key\n" .
            "4. Click 'Activate License'\n\n" .
            "Need help? Visit: https://jdsandigitel.com/support\n\n" .
            "Thank you!\n" .
            "jdsan Digitel Team",
            $order->get_billing_first_name(),
            ucfirst($settings['type']),
            $license_key,
            ucfirst($settings['type']),
            $settings['max_domains'],
            date('F j, Y', strtotime('+' . $settings['validity'] . ' days'))
        );
        
        $headers = array('Content-Type: text/plain; charset=UTF-8');
        
        wp_mail($to, $subject, $message, $headers);
    }   }
        
        // Default to pro if we can't determine
        return 'pro';
    }
}

// Initialize the plugin
new Nexus_License_API();
