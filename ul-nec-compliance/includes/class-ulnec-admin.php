<?php
/**
 * Admin Interface Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class ULNEC_Admin {
    
    private $supabase;
    
    public function __construct($supabase) {
        $this->supabase = $supabase;
        
        add_action('admin_menu', [$this, 'add_admin_menu']);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Check if user is SaaS admin (Supabase-based check)
        $is_saas_admin = $this->is_saas_admin();
        
        // Only add menu if user is SaaS admin OR WordPress admin (for initial setup)
        if (!$is_saas_admin && !current_user_can('manage_options')) {
            return;
        }
        
        add_menu_page(
            'UL-NEC Manager',
            'UL-NEC',
            'read', // Minimal capability - actual check is in is_saas_admin()
            'ulnec-dashboard',
            [$this, 'dashboard_page'],
            'dashicons-shield',
            30
        );
        
        // Main sections
        add_submenu_page('ulnec-dashboard', 'Dashboard', 'Dashboard', 'read', 'ulnec-dashboard', [$this, 'dashboard_page']);
        add_submenu_page('ulnec-dashboard', 'Users', 'Users', 'read', 'ulnec-users', [$this, 'users_page']);
        add_submenu_page('ulnec-dashboard', 'Licenses', 'Licenses', 'read', 'ulnec-licenses', [$this, 'licenses_page']);
        add_submenu_page('ulnec-dashboard', 'Downloads', 'Downloads', 'read', 'ulnec-downloads', [$this, 'downloads_page']);
        
        // New Beta Management Pages
        add_submenu_page('ulnec-dashboard', 'Bugs & Features', 'Bugs & Features', 'read', 'ulnec-bugs-features', [$this, 'bugs_features_page']);
        add_submenu_page('ulnec-dashboard', 'Founders Program', 'Founders Program', 'read', 'ulnec-founders', [$this, 'founders_page']);
        add_submenu_page('ulnec-dashboard', 'Beta Analytics', 'Beta Analytics', 'read', 'ulnec-analytics', [$this, 'analytics_page']);
        
        add_submenu_page('ulnec-dashboard', 'Settings', 'Settings', 'read', 'ulnec-settings', [$this, 'settings_page']);
    }
    
    /**
     * Check if current user is a SaaS admin
     */
    private function is_saas_admin() {
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
     * Check access and redirect if not authorized
     */
    private function check_access() {
        if (!$this->is_saas_admin() && !current_user_can('manage_options')) {
            wp_die(
                '<h1>Access Denied</h1><p>You do not have permission to access this page.</p>',
                'Access Denied',
                ['response' => 403]
            );
        }
    }
    
    /**
     * Dashboard page
     */
    public function dashboard_page() {
        $this->check_access();
        
        $connected = $this->supabase->test_connection();
        ?>
        <div class="wrap">
            <h1>UL-NEC Dashboard</h1>
            
            <div class="ulnec-status">
                <h2>Connection Status</h2>
                <?php if ($connected): ?>
                    <p style="color: green;">✅ Supabase Connected</p>
                <?php else: ?>
                    <p style="color: red;">❌ Supabase Connection Failed</p>
                    <p>Please check your credentials in Settings.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Users page
     */
    public function users_page() {
        $this->check_access();

        $action_notice = '';
        $action_error = '';
        $can_manage_status = $this->is_saas_admin() || current_user_can('manage_options');

        if (isset($_GET['ulnec_action'], $_GET['_wpnonce'])) {
            $ulnec_action = sanitize_key(wp_unslash($_GET['ulnec_action']));

            if (in_array($ulnec_action, ['suspend_user', 'activate_user'], true)) {
                if (!$can_manage_status) {
                    $action_error = 'You do not have permission to update user status.';
                } else {
                    $supabase_user_id = isset($_GET['supabase_user_id']) ? sanitize_text_field(wp_unslash($_GET['supabase_user_id'])) : '';
                    $wp_user_id = isset($_GET['wp_user_id']) ? absint($_GET['wp_user_id']) : 0;
                    $nonce = sanitize_text_field(wp_unslash($_GET['_wpnonce']));

                    if (empty($supabase_user_id)) {
                        $action_error = 'Invalid user selected.';
                    } elseif (!wp_verify_nonce($nonce, 'ulnec_' . $ulnec_action . '_' . $supabase_user_id)) {
                        $action_error = 'Security check failed. Please try again.';
                    } else {
                        $target_status = $ulnec_action === 'suspend_user' ? 'suspended' : 'active';
                        $status_result = $this->supabase->request(
                            'PATCH',
                            'ulnec_users?id=eq.' . urlencode($supabase_user_id),
                            ['status' => $target_status]
                        );

                        if (is_wp_error($status_result)) {
                            $action_error = 'Failed to update user status: ' . $status_result->get_error_message();
                        } else {
                            if ($wp_user_id > 0) {
                                update_user_meta($wp_user_id, 'ulnec_account_status', $target_status);
                            }

                            $action_notice = $target_status === 'suspended'
                                ? 'User suspended successfully.'
                                : 'User reactivated successfully.';
                        }
                    }
                }
            }
        }

        if (
            isset($_GET['ulnec_action'], $_GET['wp_user_id'], $_GET['_wpnonce']) &&
            $_GET['ulnec_action'] === 'delete_user'
        ) {
            if (!current_user_can('delete_users')) {
                $action_error = 'You do not have permission to delete users.';
            } else {
                $wp_user_id = absint($_GET['wp_user_id']);
                $nonce = sanitize_text_field(wp_unslash($_GET['_wpnonce']));

                if (!wp_verify_nonce($nonce, 'ulnec_delete_user_' . $wp_user_id)) {
                    $action_error = 'Security check failed. Please try again.';
                } elseif ($wp_user_id <= 0) {
                    $action_error = 'Invalid user selected.';
                } elseif (get_current_user_id() === $wp_user_id) {
                    $action_error = 'You cannot delete your own account.';
                } else {
                    $wp_user = get_user_by('id', $wp_user_id);

                    if (!$wp_user) {
                        $action_error = 'WordPress user not found.';
                    } else {
                        $supabase_delete_error = '';
                        $supabase_user = $this->supabase->get_user_by_wordpress_id($wp_user_id);

                        if (!is_wp_error($supabase_user) && !empty($supabase_user['id'])) {
                            $supabase_result = $this->supabase->request('DELETE', 'ulnec_users?id=eq.' . urlencode($supabase_user['id']));
                            if (is_wp_error($supabase_result)) {
                                $supabase_delete_error = $supabase_result->get_error_message();
                            }
                        }

                        if (!function_exists('wp_delete_user')) {
                            require_once ABSPATH . 'wp-admin/includes/user.php';
                        }

                        $wp_deleted = wp_delete_user($wp_user_id);

                        if (!$wp_deleted) {
                            $action_error = 'Failed to delete WordPress user. Please try again.';
                        } else {
                            if (!empty($supabase_delete_error)) {
                                $action_notice = 'User deleted in WordPress, but failed to remove Supabase record: ' . $supabase_delete_error;
                            } else {
                                $action_notice = 'User deleted successfully.';
                            }
                        }
                    }
                }
            }
        }
        
        // Get users from Supabase
        $users = $this->supabase->request('GET', 'ulnec_users?order=created_at.desc&limit=100');
        
        ?>
        <div class="wrap">
            <h1>UL-NEC Users</h1>

            <?php if (!empty($action_notice)): ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php echo esc_html($action_notice); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($action_error)): ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php echo esc_html($action_error); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (is_wp_error($users)): ?>
                <div class="error">
                    <p><strong>Error loading users:</strong> <?php echo esc_html($users->get_error_message()); ?></p>
                    <p><small>Check your Supabase connection in Settings.</small></p>
                </div>
            <?php elseif (empty($users)): ?>
                <div class="notice notice-info">
                    <p>No users found. Users will appear here after registration.</p>
                </div>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Tier</th>
                            <th>Status</th>
                            <th>WordPress ID</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <?php
                            $supabase_user_id = isset($user['id']) ? sanitize_text_field($user['id']) : '';
                            $current_status = isset($user['status']) ? sanitize_key($user['status']) : 'active';
                            $wp_user_id = isset($user['wordpress_user_id']) ? absint($user['wordpress_user_id']) : 0;
                            $can_delete_user = $wp_user_id > 0 && current_user_can('delete_users') && get_current_user_id() !== $wp_user_id;
                            $delete_url = '';
                            $status_url = '';
                            $status_action = $current_status === 'suspended' ? 'activate_user' : 'suspend_user';
                            $status_label = $current_status === 'suspended' ? 'Reactivate' : 'Suspend';

                            if ($can_delete_user) {
                                $delete_url = wp_nonce_url(
                                    add_query_arg(
                                        [
                                            'page' => 'ulnec-users',
                                            'ulnec_action' => 'delete_user',
                                            'wp_user_id' => $wp_user_id,
                                        ],
                                        admin_url('admin.php')
                                    ),
                                    'ulnec_delete_user_' . $wp_user_id
                                );
                            }

                            if ($can_manage_status && !empty($supabase_user_id)) {
                                $status_url = wp_nonce_url(
                                    add_query_arg(
                                        [
                                            'page' => 'ulnec-users',
                                            'ulnec_action' => $status_action,
                                            'supabase_user_id' => $supabase_user_id,
                                            'wp_user_id' => $wp_user_id,
                                        ],
                                        admin_url('admin.php')
                                    ),
                                    'ulnec_' . $status_action . '_' . $supabase_user_id
                                );
                            }
                            ?>
                            <tr>
                                <td><strong><?php echo esc_html($user['name'] ?? 'N/A'); ?></strong></td>
                                <td><?php echo esc_html($user['email']); ?></td>
                                <td>
                                    <span class="ulnec-tier-badge <?php echo esc_attr($user['tier']); ?>">
                                        <?php echo esc_html(ucfirst($user['tier'] ?? 'free')); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="ulnec-status-<?php echo esc_attr($user['status'] ?? 'active'); ?>">
                                        <?php echo esc_html(ucfirst($user['status'] ?? 'active')); ?>
                                    </span>
                                </td>
                                <td><?php echo esc_html($user['wordpress_user_id'] ?? 'Not synced'); ?></td>
                                <td><?php echo esc_html(date('M d, Y', strtotime($user['created_at']))); ?></td>
                                <td>
                                    <?php if (!empty($status_url)): ?>
                                        <a
                                            href="<?php echo esc_url($status_url); ?>"
                                            class="button"
                                            onclick="return confirm('<?php echo $status_action === 'suspend_user' ? 'Suspend this user account?' : 'Reactivate this user account?'; ?>');"
                                        ><?php echo esc_html($status_label); ?></a>
                                    <?php endif; ?>

                                    <?php if ($can_delete_user): ?>
                                        <?php if (!empty($status_url)): ?>&nbsp;<?php endif; ?>
                                        <a
                                            href="<?php echo esc_url($delete_url); ?>"
                                            class="button button-link-delete"
                                            onclick="return confirm('Delete this user from WordPress? This cannot be undone.');"
                                        >Delete User</a>
                                    <?php elseif ($wp_user_id <= 0): ?>
                                        <span style="color:#666;">No WP account</span>
                                    <?php elseif (get_current_user_id() === $wp_user_id): ?>
                                        <span style="color:#666;">Current user</span>
                                    <?php else: ?>
                                        <span style="color:#666;">No permission</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            
            <style>
                .ulnec-tier-badge {
                    padding: 4px 10px;
                    border-radius: 3px;
                    font-size: 12px;
                    font-weight: 600;
                    text-transform: uppercase;
                }
                .ulnec-tier-badge.free { background: #e0e0e0; color: #666; }
                .ulnec-tier-badge.beta { background: #667eea; color: white; }
                .ulnec-tier-badge.pro { background: #f6ad55; color: white; }
                .ulnec-tier-badge.enterprise { background: #9f7aea; color: white; }
                
                .ulnec-status-active { color: #27ae60; }
                .ulnec-status-suspended { color: #e74c3c; }
                .ulnec-status-cancelled { color: #95a5a6; }
            </style>
        </div>
        <?php
    }
    
    /**
     * Licenses page
     */
    public function licenses_page() {
        $this->check_access();
        
        // Get licenses with user info from Supabase
        $licenses_query = 'ulnec_licenses?select=*,ulnec_users(name,email)&order=created_at.desc&limit=100';
        $licenses = $this->supabase->request('GET', $licenses_query);
        
        if (is_wp_error($licenses)) {
            echo '<div class="wrap"><h1>Licenses</h1>';
            echo '<div class="error"><p>Error loading licenses: ' . $licenses->get_error_message() . '</p></div>';
            echo '</div>';
            return;
        }
        
        ?>
        <div class="wrap">
            <h1>UL-NEC Licenses</h1>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>License Key</th>
                        <th>User</th>
                        <th>Tier</th>
                        <th>Status</th>
                        <th>Activations</th>
                        <th>Expires</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($licenses)): ?>
                        <tr>
                            <td colspan="7">No licenses found. Licenses will appear here after creation.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($licenses as $license): ?>
                            <?php
                            $user_info = $license['ulnec_users'] ?? null;
                            $is_expired = strtotime($license['expires_at']) < time();
                            ?>
                            <tr>
                                <td><code><?php echo esc_html($license['license_key']); ?></code></td>
                                <td>
                                    <?php if ($user_info): ?>
                                        <?php echo esc_html($user_info['name'] ?? $user_info['email']); ?><br>
                                        <small><?php echo esc_html($user_info['email']); ?></small>
                                    <?php else: ?>
                                        <em>Unknown</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="ulnec-tier-badge <?php echo esc_attr($license['tier']); ?>">
                                        <?php echo esc_html(ucfirst($license['tier'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="ulnec-license-status <?php echo esc_attr($license['status']); ?> <?php echo $is_expired ? 'expired' : ''; ?>">
                                        <?php echo esc_html(ucfirst($license['status'])); ?>
                                        <?php if ($is_expired && $license['status'] === 'active'): ?>
                                            (Expired)
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo esc_html($license['activation_count']); ?> / <?php echo esc_html($license['max_activations']); ?>
                                </td>
                                <td><?php echo esc_html(date('M d, Y', strtotime($license['expires_at']))); ?></td>
                                <td><?php echo esc_html(date('M d, Y', strtotime($license['created_at']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <style>
                .ulnec-license-status.active { color: #27ae60; font-weight: 600; }
                .ulnec-license-status.expired { color: #e74c3c; font-weight: 600; }
                .ulnec-license-status.revoked { color: #e74c3c; }
                .ulnec-license-status.suspended { color: #f39c12; }
            </style>
        </div>
        <?php
    }
    
    /**
     * Downloads page
     */
    public function downloads_page() {
        $this->check_access();
        
        $downloads = $this->supabase->request('GET', 'ulnec_downloads?select=*,ulnec_users(name,email)&order=downloaded_at.desc&limit=100');
        
        ?>
        <div class="wrap">
            <h1>Download Logs</h1>
            
            <?php if (is_wp_error($downloads)): ?>
                <div class="error">
                    <p><strong>Error loading downloads:</strong> <?php echo esc_html($downloads->get_error_message()); ?></p>
                </div>
            <?php elseif (empty($downloads)): ?>
                <div class="notice notice-info">
                    <p>No downloads yet.</p>
                </div>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Version</th>
                            <th>File</th>
                            <th>IP Address</th>
                            <th>Downloaded At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($downloads as $download): ?>
                            <?php $user_info = $download['ulnec_users'] ?? null; ?>
                            <tr>
                                <td>
                                    <?php if ($user_info): ?>
                                        <?php echo esc_html($user_info['name'] ?? $user_info['email']); ?><br>
                                        <small><?php echo esc_html($user_info['email']); ?></small>
                                    <?php else: ?>
                                        <em>Unknown</em>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html($download['version']); ?></td>
                                <td><code><?php echo esc_html($download['file_name']); ?></code></td>
                                <td><?php echo esc_html($download['ip_address'] ?? 'N/A'); ?></td>
                                <td><?php echo esc_html(date('M d, Y H:i', strtotime($download['downloaded_at']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Bugs & Features page
     */
    public function bugs_features_page() {
        $this->check_access();
        
        $bugs = $this->supabase->request('GET', 'ulnec_bugs?order=created_at.desc&limit=50');
        $features = $this->supabase->request('GET', 'ulnec_features?order=created_at.desc&limit=50');
        
        // Debug logging
        error_log('Admin - Bugs query result: ' . json_encode([
            'is_error' => is_wp_error($bugs),
            'is_empty' => empty($bugs),
            'is_array' => is_array($bugs),
            'count' => is_array($bugs) ? count($bugs) : 0,
            'data' => $bugs
        ]));
        
        if (is_wp_error($bugs)) {
            error_log('Admin - Bugs ERROR: ' . $bugs->get_error_message() . ' | Data: ' . json_encode($bugs->get_error_data()));
        }
        
        ?>
        <div class="wrap">
            <h1>Bugs & Feature Requests</h1>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px;">
                <!-- Bug Reports -->
                <div>
                    <h2>🐛 Bug Reports</h2>
                    <?php if (is_wp_error($bugs) || empty($bugs)): ?>
                        <p>No bugs reported yet.</p>
                        <?php if (is_wp_error($bugs)): ?>
                            <p style="color: red;"><strong>Error:</strong> <?php echo esc_html($bugs->get_error_message()); ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Severity</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bugs as $bug): ?>
                                    <tr>
                                        <td><strong><?php echo esc_html($bug['title']); ?></strong></td>
                                        <td>
                                            <span class="severity-<?php echo esc_attr($bug['severity'] ?? 'medium'); ?>">
                                                <?php echo esc_html(ucfirst($bug['severity'] ?? 'medium')); ?>
                                            </span>
                                        </td>
                                        <td><?php echo esc_html(ucfirst($bug['status'])); ?></td>
                                        <td><?php echo esc_html(date('M d, Y', strtotime($bug['created_at']))); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                
                <!-- Feature Requests -->
                <div>
                    <h2>✨ Feature Requests</h2>
                    <?php if (is_wp_error($features) || empty($features)): ?>
                        <p>No feature requests yet.</p>
                    <?php else: ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Votes</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($features as $feature): ?>
                                    <tr>
                                        <td><strong><?php echo esc_html($feature['title']); ?></strong></td>
                                        <td><?php echo esc_html($feature['vote_count'] ?? 0); ?> 👍</td>
                                        <td><?php echo esc_html(ucfirst($feature['status'])); ?></td>
                                        <td><?php echo esc_html(date('M d, Y', strtotime($feature['created_at']))); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Founders Program page
     */
    public function founders_page() {
        $this->check_access();
        
        $founders = $this->supabase->request('GET', 'ulnec_founders?select=*,ulnec_users(name,email)&order=created_at.desc');
        
        ?>
        <div class="wrap">
            <h1>🚀 Founders Program</h1>
            
            <?php if (is_wp_error($founders)): ?>
                <div class="error">
                    <p><strong>Error:</strong> <?php echo esc_html($founders->get_error_message()); ?></p>
                </div>
            <?php elseif (empty($founders)): ?>
                <p>No founders yet.</p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Status</th>
                            <th>Benefits Claimed</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($founders as $founder): ?>
                            <?php $user_info = $founder['ulnec_users'] ?? null; ?>
                            <tr>
                                <td>
                                    <?php if ($user_info): ?>
                                        <?php echo esc_html($user_info['name'] ?? $user_info['email']); ?><br>
                                        <small><?php echo esc_html($user_info['email']); ?></small>
                                    <?php else: ?>
                                        <em>Unknown</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="founder-status-<?php echo esc_attr($founder['status']); ?>">
                                        <?php echo esc_html(ucfirst($founder['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $benefits = json_decode($founder['benefits_claimed'] ?? '[]', true);
                                    echo esc_html(count($benefits)) . ' benefits';
                                    ?>
                                </td>
                                <td><?php echo esc_html(date('M d, Y', strtotime($founder['created_at']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Analytics page - Enhanced with detailed user tracking
     */
    public function analytics_page() {
        $this->check_access();
        
        // Get summary stats
        $total_users = $this->supabase->request('GET', 'ulnec_users?select=count');
        $total_licenses = $this->supabase->request('GET', 'ulnec_licenses?select=count');
        $total_downloads = $this->supabase->request('GET', 'ulnec_downloads?select=count');
        $total_bugs = $this->supabase->request('GET', 'ulnec_bugs?select=count');
        $total_features = $this->supabase->request('GET', 'ulnec_features?select=count');
        
        // Get recent users (last 30 days)
        $date_30_days_ago = date('Y-m-d', strtotime('-30 days'));
        $recent_users = $this->supabase->request('GET', 'ulnec_users?created_at=gte.' . $date_30_days_ago . '&order=created_at.desc');
        
        // Get all users with detailed info
        $all_users = $this->supabase->request('GET', 'ulnec_users?order=created_at.desc&limit=100');
        
        // Calculate conversion rate
        $total_users_count = is_array($total_users) && isset($total_users[0]['count']) ? $total_users[0]['count'] : 0;
        $total_licenses_count = is_array($total_licenses) && isset($total_licenses[0]['count']) ? $total_licenses[0]['count'] : 0;
        $conversion_rate = $total_users_count > 0 ? round(($total_licenses_count / $total_users_count) * 100, 1) : 0;
        
        // Get recent signups (last 7 days for chart)
        $signups_by_day = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $count = 0;
            if (is_array($all_users)) {
                foreach ($all_users as $user) {
                    if (isset($user['created_at']) && strpos($user['created_at'], $date) === 0) {
                        $count++;
                    }
                }
            }
            $signups_by_day[] = ['date' => date('M d', strtotime($date)), 'count' => $count];
        }
        
        ?>
        <div class="wrap">
            <h1>📊 UL/NEC Analytics Dashboard</h1>
            <p style="color: #6b7280; margin-bottom: 30px;">Real-time insights into user signups, engagement, and usage</p>
            
            <!-- Summary Stats -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0;">
                <div class="ulnec-stat-card">
                    <h3>👥 Total Users</h3>
                    <p class="stat-number"><?php echo $total_users_count; ?></p>
                    <p class="stat-label">All registered users</p>
                </div>
                <div class="ulnec-stat-card" style="border-left-color: #10b981;">
                    <h3>🔑 Active Licenses</h3>
                    <p class="stat-number"><?php echo $total_licenses_count; ?></p>
                    <p class="stat-label">Paid customers</p>
                </div>
                <div class="ulnec-stat-card" style="border-left-color: #f59e0b;">
                    <h3>📥 Downloads</h3>
                    <p class="stat-number"><?php echo is_array($total_downloads) && isset($total_downloads[0]['count']) ? $total_downloads[0]['count'] : 0; ?></p>
                    <p class="stat-label">Total .msi downloads</p>
                </div>
                <div class="ulnec-stat-card" style="border-left-color: #ef4444;">
                    <h3>🐛 Bug Reports</h3>
                    <p class="stat-number"><?php echo is_array($total_bugs) && isset($total_bugs[0]['count']) ? $total_bugs[0]['count'] : 0; ?></p>
                    <p class="stat-label">Reported issues</p>
                </div>
                <div class="ulnec-stat-card" style="border-left-color: #8b5cf6;">
                    <h3>💡 Features</h3>
                    <p class="stat-number"><?php echo is_array($total_features) && isset($total_features[0]['count']) ? $total_features[0]['count'] : 0; ?></p>
                    <p class="stat-label">Feature requests</p>
                </div>
                <div class="ulnec-stat-card" style="border-left-color: #06b6d4;">
                    <h3>📈 Conversion</h3>
                    <p class="stat-number"><?php echo $conversion_rate; ?>%</p>
                    <p class="stat-label">Sign-up to paid</p>
                </div>
            </div>
            
            <!-- Signups Chart -->
            <div style="background: #fff; padding: 25px; border-radius: 8px; margin: 30px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;">📅 Sign-ups Last 7 Days</h2>
                <div style="display: flex; align-items: flex-end; gap: 15px; height: 200px; margin-top: 20px;">
                    <?php 
                    $max_count = max(array_column($signups_by_day, 'count'));
                    $max_count = $max_count > 0 ? $max_count : 1;
                    foreach ($signups_by_day as $day): 
                        $height = $max_count > 0 ? ($day['count'] / $max_count) * 160 : 0;
                    ?>
                        <div style="flex: 1; text-align: center;">
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: <?php echo $height; ?>px; border-radius: 8px; min-height: 20px; position: relative;">
                                <span style="position: absolute; top: -25px; left: 0; right: 0; font-weight: 600; color: #1f2937;"><?php echo $day['count']; ?></span>
                            </div>
                            <p style="margin-top: 10px; font-size: 12px; color: #6b7280;"><?php echo $day['date']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Recent Users Table -->
            <div style="background: #fff; padding: 25px; border-radius: 8px; margin: 30px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;">🆕 Recent User Sign-ups (Last 30 Days)</h2>
                <?php if (is_array($recent_users) && !empty($recent_users)): ?>
                    <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Signed Up</th>
                                <th>Status</th>
                                <th>License</th>
                                <th>Downloads</th>
                                <th>Activity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($recent_users, 0, 20) as $user): ?>
                                <?php
                                // Get user's license info
                                $user_licenses = $this->supabase->request('GET', 'ulnec_licenses?user_id=eq.' . $user['id']);
                                $has_license = is_array($user_licenses) && !empty($user_licenses);
                                
                                // Get user's download count
                                $user_downloads = $this->supabase->request('GET', 'ulnec_downloads?user_id=eq.' . $user['id'] . '&select=count');
                                $download_count = is_array($user_downloads) && isset($user_downloads[0]['count']) ? $user_downloads[0]['count'] : 0;
                                
                                // Get user's activity (bugs + features)
                                $user_bugs = $this->supabase->request('GET', 'ulnec_bugs?user_id=eq.' . $user['id'] . '&select=count');
                                $user_features = $this->supabase->request('GET', 'ulnec_features?user_id=eq.' . $user['id'] . '&select=count');
                                $bug_count = is_array($user_bugs) && isset($user_bugs[0]['count']) ? $user_bugs[0]['count'] : 0;
                                $feature_count = is_array($user_features) && isset($user_features[0]['count']) ? $user_features[0]['count'] : 0;
                                $total_activity = $bug_count + $feature_count;
                                ?>
                                <tr>
                                    <td><strong><?php echo esc_html($user['name'] ?? 'N/A'); ?></strong></td>
                                    <td><?php echo esc_html($user['email']); ?></td>
                                    <td><?php echo esc_html(date('M d, Y', strtotime($user['created_at']))); ?></td>
                                    <td>
                                        <?php if ($has_license): ?>
                                            <span style="background: #10b981; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px;">✓ Paid</span>
                                        <?php else: ?>
                                            <span style="background: #f59e0b; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Trial</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($has_license): ?>
                                            <?php echo esc_html(ucfirst($user_licenses[0]['tier'] ?? 'N/A')); ?>
                                        <?php else: ?>
                                            --
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $download_count; ?> times</td>
                                    <td>
                                        <?php if ($total_activity > 0): ?>
                                            <?php echo $bug_count; ?> bugs, <?php echo $feature_count; ?> features
                                        <?php else: ?>
                                            <span style="color: #9ca3af;">No activity</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: #9ca3af; margin-top: 20px;">No users signed up in the last 30 days.</p>
                <?php endif; ?>
            </div>
            
            <!-- Usage Insights -->
            <div style="background: #fff; padding: 25px; border-radius: 8px; margin: 30px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h2 style="margin-top: 0;">💎 Key Insights</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
                    <div style="padding: 20px; background: #f9fafb; border-radius: 8px;">
                        <h3 style="color: #667eea; margin-top: 0;">Average Downloads per User</h3>
                        <p style="font-size: 28px; font-weight: 700; margin: 10px 0;">
                            <?php 
                            $total_downloads_count = is_array($total_downloads) && isset($total_downloads[0]['count']) ? $total_downloads[0]['count'] : 0;
                            echo $total_users_count > 0 ? round($total_downloads_count / $total_users_count, 1) : 0; 
                            ?>
                        </p>
                        <p style="color: #6b7280; font-size: 14px;">Times per registered user</p>
                    </div>
                    <div style="padding: 20px; background: #f9fafb; border-radius: 8px;">
                        <h3 style="color: #10b981; margin-top: 0;">Engagement Rate</h3>
                        <p style="font-size: 28px; font-weight: 700; margin: 10px 0;">
                            <?php 
                            $total_bugs_count = is_array($total_bugs) && isset($total_bugs[0]['count']) ? $total_bugs[0]['count'] : 0;
                            $total_features_count = is_array($total_features) && isset($total_features[0]['count']) ? $total_features[0]['count'] : 0;
                            $total_engaged = $total_bugs_count + $total_features_count;
                            echo $total_users_count > 0 ? round(($total_engaged / $total_users_count) * 100, 1) : 0; 
                            ?>%
                        </p>
                        <p style="color: #6b7280; font-size: 14px;">Users submitting bugs/features</p>
                    </div>
                    <div style="padding: 20px; background: #f9fafb; border-radius: 8px;">
                        <h3 style="color: #f59e0b; margin-top: 0;">Trial to Paid</h3>
                        <p style="font-size: 28px; font-weight: 700; margin: 10px 0;"><?php echo $conversion_rate; ?>%</p>
                        <p style="color: #6b7280; font-size: 14px;">Conversion rate</p>
                    </div>
                </div>
            </div>
            
            <style>
                .ulnec-stat-card {
                    background: #fff;
                    padding: 25px;
                    border-radius: 8px;
                    border-left: 4px solid #667eea;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
                .ulnec-stat-card h3 {
                    font-size: 14px;
                    color: #6b7280;
                    margin-bottom: 10px;
                    font-weight: 600;
                }
                .ulnec-stat-card .stat-number {
                    font-size: 36px;
                    font-weight: 700;
                    color: #1f2937;
                    margin: 5px 0;
                }
                .ulnec-stat-card .stat-label {
                    font-size: 12px;
                    color: #9ca3af;
                    margin: 0;
                }
            </style>
        </div>
        <?php
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        $this->check_access();
        
        if (isset($_POST['ulnec_save_settings'])) {
            check_admin_referer('ulnec_settings');
            
            update_option('ulnec_supabase_url', sanitize_text_field($_POST['supabase_url']));
            update_option('ulnec_supabase_anon_key', sanitize_text_field($_POST['supabase_anon_key']));
            update_option('ulnec_supabase_service_key', sanitize_text_field($_POST['supabase_service_key']));
            update_option('ulnec_page_pricing',  absint($_POST['ulnec_page_pricing']));
            update_option('ulnec_page_login',    absint($_POST['ulnec_page_login']));
            update_option('ulnec_page_register', absint($_POST['ulnec_page_register']));
            update_option('ulnec_page_dashboard',absint($_POST['ulnec_page_dashboard']));
            
            echo '<div class="updated"><p>Settings saved!</p></div>';
        }
        
        $url     = get_option('ulnec_supabase_url', '');
        $anon    = get_option('ulnec_supabase_anon_key', '');
        $service = get_option('ulnec_supabase_service_key', '');
        $pg_pricing   = (int) get_option('ulnec_page_pricing', 0);
        $pg_login     = (int) get_option('ulnec_page_login', 0);
        $pg_register  = (int) get_option('ulnec_page_register', 0);
        $pg_dashboard = (int) get_option('ulnec_page_dashboard', 0);
        ?>
        <div class="wrap">
            <h1>UL-NEC Settings</h1>
            
            <form method="post">
                <?php wp_nonce_field('ulnec_settings'); ?>
                
                <table class="form-table">
                    <tr>
                        <th>Supabase URL</th>
                        <td>
                            <input type="text" name="supabase_url" value="<?php echo esc_attr($url); ?>" class="regular-text" placeholder="https://xxx.supabase.co">
                        </td>
                    </tr>
                    <tr>
                        <th>Supabase Anon Key</th>
                        <td>
                            <input type="text" name="supabase_anon_key" value="<?php echo esc_attr($anon); ?>" class="large-text">
                        </td>
                    </tr>
                    <tr>
                        <th>Supabase Service Key</th>
                        <td>
                            <input type="password" name="supabase_service_key" value="<?php echo esc_attr($service); ?>" class="large-text">
                        </td>
                    </tr>
                </table>

                <h2 style="margin-top:2rem;">Page Links</h2>
                <p style="color:#666;">Choose which WordPress page each link points to.</p>
                <table class="form-table">
                    <tr>
                        <th><label>Pricing / Billing Page</label></th>
                        <td>
                            <?php wp_dropdown_pages(['name' => 'ulnec_page_pricing', 'selected' => $pg_pricing, 'show_option_none' => '— Select a page —', 'option_none_value' => 0]); ?>
                            <?php if ($pg_pricing) echo '<p class="description">Current URL: <code>' . get_permalink($pg_pricing) . '</code></p>'; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Login Page</label></th>
                        <td>
                            <?php wp_dropdown_pages(['name' => 'ulnec_page_login', 'selected' => $pg_login, 'show_option_none' => '— Select a page —', 'option_none_value' => 0]); ?>
                            <?php if ($pg_login) echo '<p class="description">Current URL: <code>' . get_permalink($pg_login) . '</code></p>'; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Register Page</label></th>
                        <td>
                            <?php wp_dropdown_pages(['name' => 'ulnec_page_register', 'selected' => $pg_register, 'show_option_none' => '— Select a page —', 'option_none_value' => 0]); ?>
                            <?php if ($pg_register) echo '<p class="description">Current URL: <code>' . get_permalink($pg_register) . '</code></p>'; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Dashboard Page</label></th>
                        <td>
                            <?php wp_dropdown_pages(['name' => 'ulnec_page_dashboard', 'selected' => $pg_dashboard, 'show_option_none' => '— Select a page —', 'option_none_value' => 0]); ?>
                            <?php if ($pg_dashboard) echo '<p class="description">Current URL: <code>' . get_permalink($pg_dashboard) . '</code></p>'; ?>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" name="ulnec_save_settings" class="button button-primary">Save Settings</button>
                </p>
            </form>
        </div>
        <?php
    }
}
