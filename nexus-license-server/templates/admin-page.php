<?php
/**
 * Admin Page Template for Nexus License Server
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'nexus_licenses';
$licenses = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY created_at DESC LIMIT 100");
?>

<div class="wrap nexus-license-admin">
    <h1><?php _e('Nexus License Server', 'nexus-license-server'); ?></h1>
    
    <div class="nls-container">
        <!-- Generate License Form -->
        <div class="nls-card">
            <h2><?php _e('Generate New License', 'nexus-license-server'); ?></h2>
            
            <form id="nls-generate-form" class="nls-form">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="tier"><?php _e('License Tier', 'nexus-license-server'); ?></label>
                        </th>
                        <td>
                            <select name="tier" id="tier" required>
                                <option value="pro"><?php _e('Pro ($199/year)', 'nexus-license-server'); ?></option>
                                <option value="advanced" selected><?php _e('Advanced ($299/year)', 'nexus-license-server'); ?></option>
                                <option value="agency"><?php _e('Agency ($599/year)', 'nexus-license-server'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="customer_name"><?php _e('Customer Name', 'nexus-license-server'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="customer_name" id="customer_name" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="customer_email"><?php _e('Customer Email', 'nexus-license-server'); ?></label>
                        </th>
                        <td>
                            <input type="email" name="customer_email" id="customer_email" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="max_activations"><?php _e('Max Activations', 'nexus-license-server'); ?></label>
                        </th>
                        <td>
                            <input type="number" name="max_activations" id="max_activations" value="1" min="1" max="999" required>
                            <p class="description"><?php _e('Number of sites that can use this license (999 = unlimited)', 'nexus-license-server'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="expires_at"><?php _e('Expiration Date', 'nexus-license-server'); ?></label>
                        </th>
                        <td>
                            <input type="date" name="expires_at" id="expires_at" value="<?php echo date('Y-m-d', strtotime('+1 year')); ?>">
                            <p class="description"><?php _e('Leave blank for lifetime license', 'nexus-license-server'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php _e('Generate License', 'nexus-license-server'); ?>
                    </button>
                </p>
            </form>
            
            <div id="nls-generated-license" style="display:none;" class="notice notice-success inline">
                <p>
                    <strong><?php _e('License Generated Successfully!', 'nexus-license-server'); ?></strong><br>
                    <code id="nls-new-license-key" style="font-size: 16px; padding: 10px; background: #f0f0f0; display: inline-block; margin-top: 10px;"></code>
                    <button type="button" class="button" id="nls-copy-license"><?php _e('Copy to Clipboard', 'nexus-license-server'); ?></button>
                </p>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="nls-stats">
            <div class="nls-stat-card">
                <div class="stat-icon">
                    <span class="dashicons dashicons-admin-network"></span>
                </div>
                <div class="stat-content">
                    <h3><?php echo $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}"); ?></h3>
                    <p><?php _e('Total Licenses', 'nexus-license-server'); ?></p>
                </div>
            </div>
            
            <div class="nls-stat-card">
                <div class="stat-icon stat-success">
                    <span class="dashicons dashicons-yes-alt"></span>
                </div>
                <div class="stat-content">
                    <h3><?php echo $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE status = 'active'"); ?></h3>
                    <p><?php _e('Active Licenses', 'nexus-license-server'); ?></p>
                </div>
            </div>
            
            <div class="nls-stat-card">
                <div class="stat-icon stat-warning">
                    <span class="dashicons dashicons-warning"></span>
                </div>
                <div class="stat-content">
                    <h3><?php echo $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE expires_at IS NOT NULL AND expires_at < NOW()"); ?></h3>
                    <p><?php _e('Expired Licenses', 'nexus-license-server'); ?></p>
                </div>
            </div>
            
            <div class="nls-stat-card">
                <div class="stat-icon stat-info">
                    <span class="dashicons dashicons-chart-line"></span>
                </div>
                <div class="stat-content">
                    <h3><?php echo $wpdb->get_var("SELECT SUM(activations) FROM {$table_name}"); ?></h3>
                    <p><?php _e('Total Activations', 'nexus-license-server'); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Licenses Table -->
        <div class="nls-card">
            <h2><?php _e('Existing Licenses', 'nexus-license-server'); ?></h2>
            
            <div class="tablenav top">
                <div class="alignleft actions">
                    <input type="text" id="nls-search" class="regular-text" placeholder="<?php _e('Search licenses...', 'nexus-license-server'); ?>">
                </div>
                <div class="alignright">
                    <button type="button" class="button" id="nls-refresh">
                        <span class="dashicons dashicons-update"></span>
                        <?php _e('Refresh', 'nexus-license-server'); ?>
                    </button>
                </div>
            </div>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('License Key', 'nexus-license-server'); ?></th>
                        <th><?php _e('Customer', 'nexus-license-server'); ?></th>
                        <th><?php _e('Tier', 'nexus-license-server'); ?></th>
                        <th><?php _e('Status', 'nexus-license-server'); ?></th>
                        <th><?php _e('Site URL', 'nexus-license-server'); ?></th>
                        <th><?php _e('Activations', 'nexus-license-server'); ?></th>
                        <th><?php _e('Expires', 'nexus-license-server'); ?></th>
                        <th><?php _e('Actions', 'nexus-license-server'); ?></th>
                    </tr>
                </thead>
                <tbody id="nls-licenses-tbody">
                    <?php if (empty($licenses)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px;">
                                <?php _e('No licenses found. Generate your first license above.', 'nexus-license-server'); ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($licenses as $license): ?>
                            <tr data-id="<?php echo $license->id; ?>">
                                <td>
                                    <code class="license-key"><?php echo esc_html($license->license_key); ?></code>
                                    <button type="button" class="button-link nls-copy-btn" data-key="<?php echo esc_attr($license->license_key); ?>">
                                        <span class="dashicons dashicons-clipboard"></span>
                                    </button>
                                </td>
                                <td>
                                    <strong><?php echo esc_html($license->customer_name); ?></strong><br>
                                    <small><?php echo esc_html($license->customer_email); ?></small>
                                </td>
                                <td>
                                    <span class="tier-badge tier-<?php echo esc_attr($license->tier); ?>">
                                        <?php echo esc_html(strtoupper($license->tier)); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo esc_attr($license->status); ?>">
                                        <?php echo esc_html($license->status); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($license->site_url): ?>
                                        <a href="<?php echo esc_url($license->site_url); ?>" target="_blank">
                                            <?php echo esc_html(parse_url($license->site_url, PHP_URL_HOST)); ?>
                                        </a>
                                    <?php else: ?>
                                        <em><?php _e('Not activated', 'nexus-license-server'); ?></em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo intval($license->activations); ?> / <?php echo intval($license->max_activations); ?>
                                </td>
                                <td>
                                    <?php
                                    if ($license->expires_at) {
                                        $is_expired = strtotime($license->expires_at) < time();
                                        echo '<span style="color: ' . ($is_expired ? '#dc3545' : '#28a745') . '">';
                                        echo esc_html(date_i18n(get_option('date_format'), strtotime($license->expires_at)));
                                        echo '</span>';
                                    } else {
                                        echo '<strong>' . __('Lifetime', 'nexus-license-server') . '</strong>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button type="button" class="button button-small nls-toggle-status" data-id="<?php echo $license->id; ?>" data-status="<?php echo $license->status; ?>">
                                        <?php echo $license->status === 'active' ? __('Suspend', 'nexus-license-server') : __('Activate', 'nexus-license-server'); ?>
                                    </button>
                                    <button type="button" class="button button-small button-link-delete nls-delete" data-id="<?php echo $license->id; ?>">
                                        <?php _e('Delete', 'nexus-license-server'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- API Documentation -->
        <div class="nls-card">
            <h2><?php _e('API Documentation', 'nexus-license-server'); ?></h2>
            
            <h3><?php _e('Endpoint URLs', 'nexus-license-server'); ?></h3>
            <table class="widefat">
                <tr>
                    <th><?php _e('Legacy API (Query Params)', 'nexus-license-server'); ?></th>
                    <td>
                        <code><?php echo esc_html(home_url('/?nexus_api_action=activate')); ?></code><br>
                        <code><?php echo esc_html(home_url('/?nexus_api_action=validate')); ?></code><br>
                        <code><?php echo esc_html(home_url('/?nexus_api_action=deactivate')); ?></code>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('REST API', 'nexus-license-server'); ?></th>
                    <td>
                        <code><?php echo esc_html(rest_url('nexus-licenses/v1/activate')); ?></code><br>
                        <code><?php echo esc_html(rest_url('nexus-licenses/v1/validate')); ?></code><br>
                        <code><?php echo esc_html(rest_url('nexus-licenses/v1/deactivate')); ?></code>
                    </td>
                </tr>
            </table>
            
            <h3><?php _e('Theme Configuration', 'nexus-license-server'); ?></h3>
            <p><?php _e('Update this URL in the Nexus theme file:', 'nexus-license-server'); ?></p>
            <p><code>/inc/class-nexus-license-manager.php</code> Line 24:</p>
            <pre style="background: #f5f5f5; padding: 15px; border-left: 4px solid #667eea;">private $license_server = '<?php echo esc_html(home_url('/')); ?>';</pre>
            
            <p><?php _e('Also update Line 29:', 'nexus-license-server'); ?></p>
            <pre style="background: #f5f5f5; padding: 15px; border-left: 4px solid #667eea;">private $use_legacy_api = true;</pre>
        </div>
    </div>
</div>
