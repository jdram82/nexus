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
        add_menu_page(
            'UL-NEC Manager',
            'UL-NEC',
            'manage_options',
            'ulnec-dashboard',
            [$this, 'dashboard_page'],
            'dashicons-shield',
            30
        );
        
        // Main sections
        add_submenu_page('ulnec-dashboard', 'Dashboard', 'Dashboard', 'manage_options', 'ulnec-dashboard', [$this, 'dashboard_page']);
        add_submenu_page('ulnec-dashboard', 'Users', 'Users', 'manage_options', 'ulnec-users', [$this, 'users_page']);
        add_submenu_page('ulnec-dashboard', 'Licenses', 'Licenses', 'manage_options', 'ulnec-licenses', [$this, 'licenses_page']);
        add_submenu_page('ulnec-dashboard', 'Downloads', 'Downloads', 'manage_options', 'ulnec-downloads', [$this, 'downloads_page']);
        
        // New Beta Management Pages
        add_submenu_page('ulnec-dashboard', 'Bugs & Features', 'Bugs & Features', 'manage_options', 'ulnec-bugs-features', [$this, 'bugs_features_page']);
        add_submenu_page('ulnec-dashboard', 'Founders Program', 'Founders Program', 'manage_options', 'ulnec-founders', [$this, 'founders_page']);
        add_submenu_page('ulnec-dashboard', 'Beta Analytics', 'Beta Analytics', 'manage_options', 'ulnec-analytics', [$this, 'analytics_page']);
        
        add_submenu_page('ulnec-dashboard', 'Settings', 'Settings', 'manage_options', 'ulnec-settings', [$this, 'settings_page']);
    }
    
    /**
     * Dashboard page
     */
    public function dashboard_page() {
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
        // Get users from Supabase
        $users = $this->supabase->request('GET', 'ulnec_users?order=created_at.desc&limit=100');
        
        ?>
        <div class="wrap">
            <h1>UL-NEC Users</h1>
            
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
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
        $bugs = $this->supabase->request('GET', 'ulnec_bugs?order=created_at.desc&limit=50');
        $features = $this->supabase->request('GET', 'ulnec_features?order=created_at.desc&limit=50');
        
        ?>
        <div class="wrap">
            <h1>Bugs & Feature Requests</h1>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 20px;">
                <!-- Bug Reports -->
                <div>
                    <h2>🐛 Bug Reports</h2>
                    <?php if (is_wp_error($bugs) || empty($bugs)): ?>
                        <p>No bugs reported yet.</p>
                    <?php else: ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bugs as $bug): ?>
                                    <tr>
                                        <td><strong><?php echo esc_html($bug['title']); ?></strong></td>
                                        <td>
                                            <span class="priority-<?php echo esc_attr($bug['priority']); ?>">
                                                <?php echo esc_html(ucfirst($bug['priority'])); ?>
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
     * Analytics page
     */
    public function analytics_page() {
        // Get summary stats
        $total_users = $this->supabase->request('GET', 'ulnec_users?select=count');
        $total_licenses = $this->supabase->request('GET', 'ulnec_licenses?select=count');
        $total_downloads = $this->supabase->request('GET', 'ulnec_downloads?select=count');
        
        ?>
        <div class="wrap">
            <h1>📊 Beta Analytics</h1>
            
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 30px 0;">
                <div class="ulnec-stat-card">
                    <h3>Total Users</h3>
                    <p class="stat-number"><?php echo is_array($total_users) && isset($total_users[0]['count']) ? $total_users[0]['count'] : 0; ?></p>
                </div>
                <div class="ulnec-stat-card">
                    <h3>Active Licenses</h3>
                    <p class="stat-number"><?php echo is_array($total_licenses) && isset($total_licenses[0]['count']) ? $total_licenses[0]['count'] : 0; ?></p>
                </div>
                <div class="ulnec-stat-card">
                    <h3>Total Downloads</h3>
                    <p class="stat-number"><?php echo is_array($total_downloads) && isset($total_downloads[0]['count']) ? $total_downloads[0]['count'] : 0; ?></p>
                </div>
                <div class="ulnec-stat-card">
                    <h3>Conversion Rate</h3>
                    <p class="stat-number">--</p>
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
                    margin: 0;
                }
            </style>
            
            <p><em>More detailed analytics coming soon...</em></p>
        </div>
        <?php
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        if (isset($_POST['ulnec_save_settings'])) {
            check_admin_referer('ulnec_settings');
            
            update_option('ulnec_supabase_url', sanitize_text_field($_POST['supabase_url']));
            update_option('ulnec_supabase_anon_key', sanitize_text_field($_POST['supabase_anon_key']));
            update_option('ulnec_supabase_service_key', sanitize_text_field($_POST['supabase_service_key']));
            
            echo '<div class="updated"><p>Settings saved!</p></div>';
        }
        
        $url = get_option('ulnec_supabase_url', '');
        $anon = get_option('ulnec_supabase_anon_key', '');
        $service = get_option('ulnec_supabase_service_key', '');
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
                
                <p class="submit">
                    <button type="submit" name="ulnec_save_settings" class="button button-primary">Save Settings</button>
                </p>
            </form>
        </div>
        <?php
    }
}
