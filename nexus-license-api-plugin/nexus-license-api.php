<?php
/**
 * Plugin Name: Nexus License API Plugin
 * Plugin URI: https://jdsandigitel.com
 * Description: Custom license API for Nexus Theme that works with Software License Manager
 * Version: 1.3.0
 * Author: jdsan Digitel
 * Author URI: https://jdsandigitel.com
 * License: GPL v2 or later
 * Text Domain: nexus-license-api-plugin
 * 
 * Changelog:
 * 1.3.0 - Added custom admin dashboard
 *       - Shows activated domains count and list
 *       - Better date picker with extended range (10 years)
 *       - Domain activation/deactivation management
 *       - Visual stats and license overview
 * 1.2.0 - Fixed database compatibility with Software License Manager schema
 *       - Removed non-existent lic_type field
 *       - Store tier in product_ref field
 *       - Added all required fields (txn_id, subscr_id, user_ref, etc)
 *       - Fixed date format from datetime to date
 *       - Enhanced debugging for manual trigger
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if WooCommerce is active
function nexus_license_api_check_woocommerce() {
    if (!function_exists('WC')) {
        add_action('admin_notices', 'nexus_license_api_woocommerce_notice');
    }
}
add_action('plugins_loaded', 'nexus_license_api_check_woocommerce');

function nexus_license_api_woocommerce_notice() {
    ?>
    <div class="notice notice-warning">
        <p><strong>Nexus License API Plugin:</strong> WooCommerce is not installed. Auto-license creation will not work until WooCommerce is activated. The API endpoints will still function normally.</p>
    </div>
    <?php
}

class Nexus_License_API {
    
    private $secret_creation = '6951ee21aaed19.50210993';
    private $secret_verification = '6951ee21aaed88.33650597';
    
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
        add_action('init', array($this, 'register_legacy_endpoints'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        if (function_exists('WC')) {
            add_action('woocommerce_order_status_completed', array($this, 'auto_create_license'), 10, 1);
            add_action('woocommerce_order_status_processing', array($this, 'auto_create_license'), 10, 1);
        }
    }
    
    public function register_routes() {
        register_rest_route('nexus-licenses/v1', '/info', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_api_info'),
            'permission_callback' => '__return_true',
        ));
        
        register_rest_route('nexus-licenses/v1', '/activate', array(
            'methods' => 'POST',
            'callback' => array($this, 'activate_license'),
            'permission_callback' => '__return_true',
        ));
        
        register_rest_route('nexus-licenses/v1', '/validate', array(
            'methods' => 'POST',
            'callback' => array($this, 'validate_license'),
            'permission_callback' => '__return_true',
        ));
        
        register_rest_route('nexus-licenses/v1', '/deactivate', array(
            'methods' => 'POST',
            'callback' => array($this, 'deactivate_license'),
            'permission_callback' => '__return_true',
        ));
        
        register_rest_route('nexus-licenses/v1', '/domains', array(
            'methods' => 'POST',
            'callback' => array($this, 'get_registered_domains'),
            'permission_callback' => '__return_true',
        ));
    }
    
    public function register_legacy_endpoints() {
        if (isset($_GET['nexus_manual_create']) && current_user_can('manage_options')) {
            global $wpdb;
            $order_id = intval($_GET['nexus_manual_create']);
            echo '<h2>Nexus License API - Manual Trigger</h2>';
            echo '<p>Attempting to create license for Order #' . $order_id . '</p>';
            echo '<hr><h3>Debug Output:</h3>';
            
            // Check if order exists
            if (!function_exists('wc_get_order')) {
                echo '<p style="color:red;">❌ WooCommerce not available</p>';
            } else {
                echo '<p style="color:green;">✅ WooCommerce available</p>';
                $order = wc_get_order($order_id);
                if (!$order) {
                    echo '<p style="color:red;">❌ Order not found</p>';
                } else {
                    echo '<p style="color:green;">✅ Order found: #' . $order_id . '</p>';
                    echo '<p>Customer: ' . $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() . '</p>';
                    
                    // Check for existing license
                    $table_name = $wpdb->prefix . 'lic_key_tbl';
                    $existing = $wpdb->get_var($wpdb->prepare(
                        "SELECT license_key FROM {$table_name} WHERE product_ref = %s",
                        'order_' . $order_id
                    ));
                    
                    if ($existing) {
                        echo '<p style="color:orange;">⚠️ License already exists: ' . $existing . '</p>';
                    } else {
                        echo '<p style="color:green;">✅ No existing license</p>';
                        
                        // Check table structure
                        echo '<h4>Database Table Structure:</h4>';
                        $columns = $wpdb->get_results("DESCRIBE {$table_name}");
                        echo '<ul style="font-size:12px;">';
                        foreach ($columns as $col) {
                            echo '<li>' . $col->Field . ' (' . $col->Type . ')';
                            if ($col->Null == 'NO') echo ' <strong>REQUIRED</strong>';
                            if ($col->Default !== null) echo ' [Default: ' . $col->Default . ']';
                            echo '</li>';
                        }
                        echo '</ul>';
                        
                        // Check items
                        echo '<h4>Order Items:</h4>';
                        foreach ($order->get_items() as $item_id => $item) {
                            $product = $item->get_product();
                            if ($product) {
                                $sku = $product->get_sku();
                                echo '<p>- Product: ' . $product->get_name() . ' (SKU: ' . $sku . ')</p>';
                                
                                $settings = $this->get_license_settings_from_sku(strtolower($sku));
                                if ($settings) {
                                    echo '<p style="color:green;">  ✅ License settings found: Type=' . $settings['type'] . ', Max Domains=' . $settings['max_domains'] . '</p>';
                                } else {
                                    echo '<p style="color:red;">  ❌ No license settings for this SKU</p>';
                                }
                            }
                        }
                    }
                }
            }
            
            echo '<hr><h3>Creating License:</h3>';
            echo '<p>Calling auto_create_license(' . $order_id . ')...</p>';
            ob_start();
            $this->auto_create_license($order_id);
            $output = ob_get_clean();
            if ($output) {
                echo '<pre>' . htmlspecialchars($output) . '</pre>';
            }
            
            // Check if created
            $new_license = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE product_ref = %s OR txn_id = %s",
                'advanced',
                'order_' . $order_id
            ));
            
            if ($new_license) {
                echo '<p style="color:green;font-weight:bold;">✅ SUCCESS! License created:</p>';
                echo '<ul>';
                echo '<li><strong>License Key:</strong> ' . $new_license->license_key . '</li>';
                echo '<li><strong>Type:</strong> ' . strtoupper($new_license->product_ref) . '</li>';
                echo '<li><strong>Max Domains:</strong> ' . $new_license->max_allowed_domains . '</li>';
                echo '<li><strong>Expires:</strong> ' . $new_license->date_expiry . '</li>';
                echo '<li><strong>Status:</strong> ' . $new_license->lic_status . '</li>';
                echo '<li><strong>Transaction ID:</strong> ' . $new_license->txn_id . '</li>';
                echo '</ul>';
            } else {
                echo '<p style="color:red;font-weight:bold;">❌ FAILED - License not created</p>';
                echo '<p>Last DB Error: ' . $wpdb->last_error . '</p>';
            }
            
            echo '<hr><p><a href="/wp-admin/admin.php?page=slm_manage_license">View All Licenses</a> | ';
            echo '<a href="/wp-admin/post.php?post=' . $order_id . '&action=edit">View Order</a></p>';
            exit;
        }
        
        if (isset($_GET['nexus_api_action'])) {
            $action = sanitize_text_field($_GET['nexus_api_action']);
            
            switch ($action) {
                case 'info':
                    $this->legacy_api_info();
                    break;
                case 'activate':
                    $this->legacy_api_activate();
                    break;
                case 'validate':
                    $this->legacy_api_validate();
                    break;
                case 'deactivate':
                    $this->legacy_api_deactivate();
                    break;
            }
        }
    }
    
    public function get_api_info() {
        return array(
            'status' => 'active',
            'version' => '1.1.0',
            'server' => 'jdsandigitel.com',
        );
    }
    
    private function legacy_api_info() {
        header('Content-Type: application/json');
        echo json_encode($this->get_api_info());
        exit;
    }
    
    public function activate_license($request) {
        global $wpdb;
        
        $license_key = isset($request['license_key']) ? sanitize_text_field($request['license_key']) : '';
        $domain = isset($request['domain']) ? sanitize_text_field($request['domain']) : '';
        
        // Also accept 'site_url' parameter (theme sends this)
        if (empty($domain) && isset($request['site_url'])) {
            $domain = sanitize_text_field($request['site_url']);
        }
        
        // Clean domain (remove http/https)
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = rtrim($domain, '/');
        
        if (empty($license_key) || empty($domain)) {
            return array('success' => false, 'message' => 'Missing parameters (key or domain)');
        }
        
        $table_name = $wpdb->prefix . 'lic_key_tbl';
        $license = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE license_key = %s",
            $license_key
        ));
        
        if (!$license) {
            return array('success' => false, 'message' => 'Invalid license key');
        }
        
        if (strtotime($license->date_expiry) < time()) {
            return array('success' => false, 'message' => 'License expired');
        }
        
        $reg_table = $wpdb->prefix . 'lic_reg_domain_tbl';
        $registered_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$reg_table} WHERE lic_key = %s",
            $license_key
        ));
        
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$reg_table} WHERE lic_key = %s AND lic_key_domain = %s",
            $license_key,
            $domain
        ));
        
        if ($existing > 0) {
            return array(
                'success' => true,
                'message' => 'License already activated on this domain',
                'tier' => $this->get_license_tier($license),
                'expires' => $license->date_expiry,
                'max_domains' => (int)$license->max_allowed_domains,
                'active_domains' => (int)$registered_count,
            );
        }
        
        if ($registered_count >= $license->max_allowed_domains) {
            return array('success' => false, 'message' => 'Maximum activations reached');
        }
        
        $wpdb->insert($reg_table, array(
            'lic_key_id' => $license->id,
            'lic_key' => $license_key,
            'lic_key_domain' => $domain,
            'product_ref' => $license->product_ref,
        ));
        
        // Get updated count after insertion
        $registered_count++;
        
        return array(
            'success' => true,
            'message' => 'License activated successfully',
            'tier' => $this->get_license_tier($license),
            'expires' => $license->date_expiry,
            'max_domains' => (int)$license->max_allowed_domains,
            'active_domains' => (int)$registered_count,
        );
    }
    
    private function legacy_api_activate() {
        $request = array(
            'license_key' => isset($_GET['license_key']) ? $_GET['license_key'] : (isset($_POST['license_key']) ? $_POST['license_key'] : ''),
            'domain' => isset($_GET['domain']) ? $_GET['domain'] : (isset($_POST['domain']) ? $_POST['domain'] : ''),
            'site_url' => isset($_GET['site_url']) ? $_GET['site_url'] : (isset($_POST['site_url']) ? $_POST['site_url'] : ''),
        );
        
        header('Content-Type: application/json');
        echo json_encode($this->activate_license($request));
        exit;
    }
    
    public function validate_license($request) {
        global $wpdb;
        
        $license_key = isset($request['license_key']) ? sanitize_text_field($request['license_key']) : '';
        $domain = isset($request['domain']) ? sanitize_text_field($request['domain']) : '';
        
        if (empty($domain) && isset($request['site_url'])) {
            $domain = sanitize_text_field($request['site_url']);
        }
        
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = rtrim($domain, '/');
        
        if (empty($license_key)) {
            return array('valid' => false, 'message' => 'Missing license key');
        }
        
        $table_name = $wpdb->prefix . 'lic_key_tbl';
        $license = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE license_key = %s",
            $license_key
        ));
        
        if (!$license) {
            return array('valid' => false, 'message' => 'Invalid license');
        }
        
        if (strtotime($license->date_expiry) < time()) {
            return array('valid' => false, 'message' => 'License expired');
        }
        
        if (!empty($domain)) {
            $reg_table = $wpdb->prefix . 'lic_reg_domain_tbl';
            $registered = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$reg_table} WHERE lic_key = %s AND lic_key_domain = %s",
                $license_key,
                $domain
            ));
            
            if ($registered == 0) {
                return array('valid' => false, 'message' => 'License not activated for this domain');
            }
        }
        
        return array(
            'success' => true,
            'valid' => true,
            'status' => 'active',
            'tier' => $this->get_license_tier($license),
            'expires' => $license->date_expiry,
        );
    }
    
    private function legacy_api_validate() {
        $request = array(
            'license_key' => isset($_GET['license_key']) ? $_GET['license_key'] : (isset($_POST['license_key']) ? $_POST['license_key'] : ''),
            'domain' => isset($_GET['domain']) ? $_GET['domain'] : (isset($_POST['domain']) ? $_POST['domain'] : ''),
        );
        
        header('Content-Type: application/json');
        echo json_encode($this->validate_license($request));
        exit;
    }
    
    public function deactivate_license($request) {
        global $wpdb;
        
        $license_key = isset($request['license_key']) ? sanitize_text_field($request['license_key']) : '';
        $domain = isset($request['domain']) ? sanitize_text_field($request['domain']) : '';
        
        if (empty($domain) && isset($request['site_url'])) {
            $domain = sanitize_text_field($request['site_url']);
        }
        
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = rtrim($domain, '/');
        
        if (empty($license_key) || empty($domain)) {
            return array('success' => false, 'message' => 'Missing parameters');
        }
        
        $reg_table = $wpdb->prefix . 'lic_reg_domain_tbl';
        $wpdb->delete($reg_table, array(
            'lic_key' => $license_key,
            'lic_key_domain' => $domain,
        ));
        
        return array('success' => true, 'message' => 'License deactivated');
    }
    
    private function legacy_api_deactivate() {
        $request = array(
            'license_key' => isset($_GET['license_key']) ? $_GET['license_key'] : (isset($_POST['license_key']) ? $_POST['license_key'] : ''),
            'domain' => isset($_GET['domain']) ? $_GET['domain'] : (isset($_POST['domain']) ? $_POST['domain'] : ''),
        );
        
        header('Content-Type: application/json');
        echo json_encode($this->deactivate_license($request));
        exit;
    }
    
    public function get_registered_domains($request) {
        global $wpdb;
        
        $license_key = isset($request['license_key']) ? sanitize_text_field($request['license_key']) : '';
        
        if (empty($license_key)) {
            return array('success' => false, 'message' => 'Missing license key');
        }
        
        $table_name = $wpdb->prefix . 'lic_key_tbl';
        $license = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE license_key = %s",
            $license_key
        ));
        
        if (!$license) {
            return array('success' => false, 'message' => 'Invalid license key');
        }
        
        $reg_table = $wpdb->prefix . 'lic_reg_domain_tbl';
        $domains = $wpdb->get_results($wpdb->prepare(
            "SELECT lic_key_domain as domain, lic_key_id FROM {$reg_table} WHERE lic_key = %s",
            $license_key
        ), ARRAY_A);
        
        return array(
            'success' => true,
            'domains' => $domains,
            'active_count' => count($domains),
            'max_allowed' => (int)$license->max_allowed_domains,
            'tier' => $this->get_license_tier($license),
        );
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'slm_manage_license',
            'Nexus License Dashboard',
            'Nexus Dashboard',
            'manage_options',
            'nexus-license-dashboard',
            array($this, 'render_dashboard')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'slm-admin_page_nexus-license-dashboard') {
            return;
        }
        
        wp_enqueue_style('nexus-license-admin', plugin_dir_url(__FILE__) . 'assets/admin.css', array(), '1.2.0');
    }
    
    /**
     * Render dashboard
     */
    public function render_dashboard() {
        global $wpdb;
        
        // Handle domain deactivation
        if (isset($_POST['deactivate_domain']) && check_admin_referer('nexus_deactivate_domain')) {
            $domain_id = intval($_POST['domain_id']);
            $reg_table = $wpdb->prefix . 'lic_reg_domain_tbl';
            $wpdb->delete($reg_table, array('lic_key_id' => $domain_id));
            echo '<div class="notice notice-success"><p>Domain deactivated successfully!</p></div>';
        }
        
        // Get all licenses with activation count
        $table_name = $wpdb->prefix . 'lic_key_tbl';
        $reg_table = $wpdb->prefix . 'lic_reg_domain_tbl';
        
        $licenses = $wpdb->get_results("
            SELECT l.*, 
                   COUNT(r.lic_key_id) as active_domains
            FROM {$table_name} l
            LEFT JOIN {$reg_table} r ON l.id = r.lic_key_id
            WHERE l.product_ref IN ('pro', 'advanced', 'agency')
            GROUP BY l.id
            ORDER BY l.date_created DESC
        ");
        
        ?>
        <div class="wrap">
            <h1>Nexus License Dashboard</h1>
            
            <div class="nexus-license-stats">
                <div class="stat-card">
                    <h3><?php echo count($licenses); ?></h3>
                    <p>Total Licenses</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count(array_filter($licenses, function($l) { return $l->lic_status === 'active'; })); ?></h3>
                    <p>Active Licenses</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo array_sum(array_map(function($l) { return $l->active_domains; }, $licenses)); ?></h3>
                    <p>Total Activations</p>
                </div>
            </div>
            
            <div class="nexus-licenses-table">
                <h2>License Details</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>License Key</th>
                            <th>Tier</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Domains</th>
                            <th>Expiry Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($licenses as $license): ?>
                            <?php
                            $domains = $wpdb->get_results($wpdb->prepare(
                                "SELECT * FROM {$reg_table} WHERE lic_key_id = %d",
                                $license->id
                            ));
                            $expiry_days = floor((strtotime($license->date_expiry) - time()) / DAY_IN_SECONDS);
                            $status_class = $license->lic_status === 'active' ? 'active' : 'inactive';
                            if ($expiry_days < 30 && $expiry_days > 0) {
                                $status_class = 'expiring';
                            } elseif ($expiry_days <= 0) {
                                $status_class = 'expired';
                            }
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($license->license_key); ?></strong>
                                    <button class="button-link" onclick="copyToClipboard('<?php echo esc_js($license->license_key); ?>')">Copy</button>
                                </td>
                                <td>
                                    <span class="tier-badge tier-<?php echo esc_attr($license->product_ref); ?>">
                                        <?php echo esc_html(strtoupper($license->product_ref)); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo esc_html($license->first_name . ' ' . $license->last_name); ?><br>
                                    <small><?php echo esc_html($license->email); ?></small>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $status_class; ?>">
                                        <?php echo esc_html(ucfirst($license->lic_status)); ?>
                                    </span>
                                    <?php if ($expiry_days > 0 && $expiry_days < 30): ?>
                                        <br><small style="color: #f56e28;">Expires in <?php echo $expiry_days; ?> days</small>
                                    <?php elseif ($expiry_days <= 0): ?>
                                        <br><small style="color: #d63638;">Expired</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo $license->active_domains; ?> / <?php echo $license->max_allowed_domains; ?></strong>
                                    <?php if ($license->active_domains > 0): ?>
                                        <button class="button-link" onclick="toggleDomains(<?php echo $license->id; ?>)">View</button>
                                        <div id="domains-<?php echo $license->id; ?>" style="display: none; margin-top: 10px;">
                                            <ul style="margin: 0; padding-left: 20px;">
                                                <?php foreach ($domains as $domain): ?>
                                                    <li>
                                                        <?php echo esc_html($domain->lic_key_domain); ?>
                                                        <form method="post" style="display: inline;">
                                                            <?php wp_nonce_field('nexus_deactivate_domain'); ?>
                                                            <input type="hidden" name="domain_id" value="<?php echo $domain->lic_key_id; ?>">
                                                            <input type="hidden" name="deactivate_domain" value="1">
                                                            <button type="submit" class="button-link" style="color: #d63638;" onclick="return confirm('Deactivate this domain?')">Remove</button>
                                                        </form>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php else: ?>
                                        <small style="color: #999;">No activations</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo esc_html(date('M j, Y', strtotime($license->date_expiry))); ?>
                                    <form method="post" style="margin-top: 5px;">
                                        <input type="date" name="new_expiry" value="<?php echo esc_attr($license->date_expiry); ?>" 
                                               min="<?php echo date('Y-m-d'); ?>" 
                                               max="<?php echo date('Y-m-d', strtotime('+10 years')); ?>"
                                               style="font-size: 11px;">
                                        <button type="submit" class="button button-small">Update</button>
                                    </form>
                                </td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=slm_manage_license&edit_record=' . $license->id); ?>" 
                                       class="button button-small">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <style>
            .nexus-license-stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin: 20px 0;
            }
            .stat-card {
                background: white;
                padding: 20px;
                border-left: 4px solid #2271b1;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            .stat-card h3 {
                margin: 0 0 5px 0;
                font-size: 32px;
                color: #2271b1;
            }
            .stat-card p {
                margin: 0;
                color: #666;
            }
            .nexus-licenses-table {
                background: white;
                padding: 20px;
                margin-top: 20px;
            }
            .tier-badge {
                display: inline-block;
                padding: 4px 10px;
                border-radius: 3px;
                font-size: 11px;
                font-weight: 600;
            }
            .tier-pro { background: #4CAF50; color: white; }
            .tier-advanced { background: #2196F3; color: white; }
            .tier-agency { background: #9C27B0; color: white; }
            .status-badge {
                display: inline-block;
                padding: 4px 10px;
                border-radius: 3px;
                font-size: 11px;
                font-weight: 600;
            }
            .status-active { background: #d4edda; color: #155724; }
            .status-inactive { background: #f8d7da; color: #721c24; }
            .status-expiring { background: #fff3cd; color: #856404; }
            .status-expired { background: #f8d7da; color: #721c24; }
            .button-link {
                background: none;
                border: none;
                color: #2271b1;
                text-decoration: underline;
                cursor: pointer;
                padding: 0;
                font-size: 12px;
            }
            .button-link:hover {
                color: #135e96;
            }
        </style>
        
        <script>
        function toggleDomains(id) {
            var el = document.getElementById('domains-' + id);
            el.style.display = el.style.display === 'none' ? 'block' : 'none';
        }
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('License key copied to clipboard!');
            });
        }
        </script>
        <?php
    }
    
    private function get_license_tier($license) {
        // Check product_ref field (we store tier there)
        if (!empty($license->product_ref)) {
            $ref = strtolower($license->product_ref);
            if (in_array($ref, array('agency', 'advanced', 'pro'))) {
                return $ref;
            }
        }
        
        // Fallback: try to get from WooCommerce via txn_id (order_XX)
        if (function_exists('wc_get_order') && !empty($license->txn_id)) {
            $order_id = str_replace('order_', '', $license->txn_id);
            if (is_numeric($order_id)) {
                $order = wc_get_order($order_id);
                if ($order) {
                    foreach ($order->get_items() as $item) {
                        $product = $item->get_product();
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
                }
            }
        }
        
        return 'pro';
    }
    
    public function auto_create_license($order_id) {
        global $wpdb;
        
        if (!function_exists('wc_get_order')) {
            error_log('Nexus: WooCommerce not available');
            return;
        }
        
        $order = wc_get_order($order_id);
        if (!$order) {
            error_log('Nexus: Order not found - ' . $order_id);
            return;
        }
        
        error_log('Nexus: Processing order #' . $order_id);
        
        $table_name = $wpdb->prefix . 'lic_key_tbl';
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} WHERE product_ref = %s",
            'order_' . $order_id
        ));
        
        if ($existing > 0) {
            error_log('Nexus: License already exists for order #' . $order_id);
            return;
        }
        
        error_log('Nexus: Creating new license...');
        
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            
            if (!$product) {
                continue;
            }
            
            $sku = strtolower($product->get_sku());
            error_log('Nexus: Product SKU: ' . $sku);
            
            if (strpos($sku, 'free') !== false) {
                continue;
            }
            
            $license_settings = $this->get_license_settings_from_sku($sku);
            error_log('Nexus: Settings: ' . print_r($license_settings, true));
            
            if (!$license_settings) {
                continue;
            }
            
            $license_key = $this->generate_license_key();
            error_log('Nexus: Generated: ' . $license_key);
            
            $expiry_date = date('Y-m-d', strtotime('+' . $license_settings['validity'] . ' days'));
            
            $insert_data = array(
                'license_key' => $license_key,
                'first_name' => $order->get_billing_first_name() ?: 'Customer',
                'last_name' => $order->get_billing_last_name() ?: 'Name',
                'email' => $order->get_billing_email(),
                'company_name' => $order->get_billing_company() ?: 'N/A',
                'product_ref' => $license_settings['type'], // Store tier in product_ref
                'max_allowed_domains' => $license_settings['max_domains'],
                'date_created' => date('Y-m-d'),
                'date_renewed' => date('Y-m-d'),
                'date_expiry' => $expiry_date,
                'lic_status' => 'active',
                'txn_id' => 'order_' . $order_id,
                'subscr_id' => '',
                'manual_reset_count' => '0',
                'user_ref' => (string)$order->get_customer_id(),
            );
            
            error_log('Nexus: Attempting insert with data: ' . print_r($insert_data, true));
            error_log('Nexus: Table name: ' . $table_name);
            
            $result = $wpdb->insert($table_name, $insert_data);
            
            error_log('Nexus: Insert result: ' . var_export($result, true));
            error_log('Nexus: wpdb->last_error: ' . $wpdb->last_error);
            error_log('Nexus: wpdb->last_query: ' . $wpdb->last_query);
            error_log('Nexus: wpdb->insert_id: ' . $wpdb->insert_id);
            
            if ($result === false) {
                error_log('Nexus: DB insert failed: ' . $wpdb->last_error);
            } else {
                error_log('Nexus: License created successfully!');
            }
            
            $order->add_order_note(
                sprintf(
                    'License auto-created: %s (Type: %s, Max Domains: %d)',
                    $license_key,
                    $license_settings['type'],
                    $license_settings['max_domains']
                )
            );
            
            $order->update_meta_data('_nexus_license_key', $license_key);
            $order->update_meta_data('_nexus_license_tier', $license_settings['type']);
            $order->save();
            
            $this->send_license_email($order, $license_key, $license_settings);
        }
    }
    
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
        
        if (isset($settings_map[$sku])) {
            return $settings_map[$sku];
        }
        
        foreach ($settings_map as $key => $settings) {
            if (strpos($sku, $key) !== false) {
                return $settings;
            }
        }
        
        return false;
    }
    
    private function generate_license_key() {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
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
    }
}

new Nexus_License_API();
