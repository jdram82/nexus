<?php
/**
 * Dashboard View
 * 
 * @package Nexus
 * @since 3.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$license_tier = isset($license['tier']) ? $license['tier'] : 'free';
$license_status = isset($license['status']) ? $license['status'] : 'inactive';
$is_pro = in_array($license_tier, array('pro', 'advanced', 'agency'));
?>

<div class="wrap nexus-admin-wrap">
    <div class="nexus-admin-header">
        <div class="nexus-logo">
            <h1><?php echo esc_html__('Nexus Theme', 'nexus'); ?></h1>
            <span class="nexus-version">v<?php echo NEXUS_VERSION; ?></span>
        </div>
        <div class="nexus-license-badge">
            <span class="license-tier tier-<?php echo esc_attr($license_tier); ?>">
                <?php echo esc_html(strtoupper($license_tier)); ?>
            </span>
            <?php if ($license_status === 'active'): ?>
                <span class="license-status active">● Active</span>
            <?php else: ?>
                <span class="license-status inactive">○ Inactive</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="nexus-admin-content">
        <!-- Quick Stats -->
        <div class="nexus-stats-grid">
            <div class="nexus-stat-card">
                <div class="stat-icon">
                    <span class="dashicons dashicons-admin-appearance"></span>
                </div>
                <div class="stat-content">
                    <h3><?php echo esc_html__('License Tier', 'nexus'); ?></h3>
                    <p class="stat-value"><?php echo esc_html(ucfirst($license_tier)); ?></p>
                </div>
            </div>

            <div class="nexus-stat-card">
                <div class="stat-icon">
                    <span class="dashicons dashicons-layout"></span>
                </div>
                <div class="stat-content">
                    <h3><?php echo esc_html__('Templates', 'nexus'); ?></h3>
                    <p class="stat-value"><?php echo $is_pro ? '50+' : '10'; ?></p>
                </div>
            </div>

            <div class="nexus-stat-card">
                <div class="stat-icon">
                    <span class="dashicons dashicons-admin-tools"></span>
                </div>
                <div class="stat-content">
                    <h3><?php echo esc_html__('Features', 'nexus'); ?></h3>
                    <p class="stat-value"><?php echo $is_pro ? 'Premium' : 'Basic'; ?></p>
                </div>
            </div>

            <div class="nexus-stat-card">
                <div class="stat-icon">
                    <span class="dashicons dashicons-update"></span>
                </div>
                <div class="stat-content">
                    <h3><?php echo esc_html__('Updates', 'nexus'); ?></h3>
                    <p class="stat-value"><?php echo esc_html__('Up to date', 'nexus'); ?></p>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="nexus-content-grid">
            <!-- Left Column -->
            <div class="nexus-main-column">
                <!-- Getting Started -->
                <div class="nexus-card">
                    <div class="card-header">
                        <h2><?php echo esc_html__('Getting Started', 'nexus'); ?></h2>
                    </div>
                    <div class="card-body">
                        <div class="nexus-checklist">
                            <div class="checklist-item <?php echo $license_status === 'active' ? 'completed' : ''; ?>">
                                <span class="dashicons dashicons-yes-alt"></span>
                                <div class="item-content">
                                    <h4><?php echo esc_html__('Activate License', 'nexus'); ?></h4>
                                    <p><?php echo esc_html__('Unlock premium features with your license key', 'nexus'); ?></p>
                                    <?php if ($license_status !== 'active'): ?>
                                        <a href="<?php echo admin_url('admin.php?page=nexus-license'); ?>" class="button button-primary">
                                            <?php echo esc_html__('Activate Now', 'nexus'); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="checklist-item">
                                <span class="dashicons dashicons-admin-customizer"></span>
                                <div class="item-content">
                                    <h4><?php echo esc_html__('Customize Your Site', 'nexus'); ?></h4>
                                    <p><?php echo esc_html__('Configure colors, fonts, and layout options', 'nexus'); ?></p>
                                    <a href="<?php echo admin_url('customize.php'); ?>" class="button">
                                        <?php echo esc_html__('Open Customizer', 'nexus'); ?>
                                    </a>
                                </div>
                            </div>

                            <div class="checklist-item">
                                <span class="dashicons dashicons-layout"></span>
                                <div class="item-content">
                                    <h4><?php echo esc_html__('Import a Template', 'nexus'); ?></h4>
                                    <p><?php echo esc_html__('Choose from pre-built templates to kickstart your site', 'nexus'); ?></p>
                                    <a href="<?php echo admin_url('admin.php?page=nexus-templates'); ?>" class="button">
                                        <?php echo esc_html__('Browse Templates', 'nexus'); ?>
                                    </a>
                                </div>
                            </div>

                            <div class="checklist-item">
                                <span class="dashicons dashicons-admin-settings"></span>
                                <div class="item-content">
                                    <h4><?php echo esc_html__('Configure Settings', 'nexus'); ?></h4>
                                    <p><?php echo esc_html__('Fine-tune performance and functionality', 'nexus'); ?></p>
                                    <a href="<?php echo admin_url('admin.php?page=nexus-settings'); ?>" class="button">
                                        <?php echo esc_html__('View Settings', 'nexus'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features -->
                <div class="nexus-card">
                    <div class="card-header">
                        <h2><?php echo esc_html__('Available Features', 'nexus'); ?></h2>
                    </div>
                    <div class="card-body">
                        <div class="nexus-features-grid">
                            <div class="feature-item">
                                <span class="dashicons dashicons-yes"></span>
                                <span><?php echo esc_html__('Advanced Typography', 'nexus'); ?></span>
                            </div>
                            <div class="feature-item">
                                <span class="dashicons dashicons-yes"></span>
                                <span><?php echo esc_html__('Color Schemes', 'nexus'); ?></span>
                            </div>
                            <div class="feature-item">
                                <span class="dashicons dashicons-yes"></span>
                                <span><?php echo esc_html__('Responsive Design', 'nexus'); ?></span>
                            </div>
                            <div class="feature-item">
                                <span class="dashicons dashicons-yes"></span>
                                <span><?php echo esc_html__('Custom Layouts', 'nexus'); ?></span>
                            </div>

                            <?php if ($is_pro): ?>
                                <div class="feature-item premium">
                                    <span class="dashicons dashicons-star-filled"></span>
                                    <span><?php echo esc_html__('Premium Templates', 'nexus'); ?></span>
                                </div>
                                <div class="feature-item premium">
                                    <span class="dashicons dashicons-star-filled"></span>
                                    <span><?php echo esc_html__('Advanced Customization', 'nexus'); ?></span>
                                </div>
                                <div class="feature-item premium">
                                    <span class="dashicons dashicons-star-filled"></span>
                                    <span><?php echo esc_html__('WooCommerce Integration', 'nexus'); ?></span>
                                </div>
                                <div class="feature-item premium">
                                    <span class="dashicons dashicons-star-filled"></span>
                                    <span><?php echo esc_html__('Priority Support', 'nexus'); ?></span>
                                </div>
                            <?php else: ?>
                                <div class="feature-item locked">
                                    <span class="dashicons dashicons-lock"></span>
                                    <span><?php echo esc_html__('Premium Templates (Pro)', 'nexus'); ?></span>
                                </div>
                                <div class="feature-item locked">
                                    <span class="dashicons dashicons-lock"></span>
                                    <span><?php echo esc_html__('Advanced Options (Pro)', 'nexus'); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="nexus-sidebar-column">
                <!-- Upgrade Notice -->
                <?php if (!$is_pro): ?>
                    <div class="nexus-card nexus-upgrade-card">
                        <div class="card-header">
                            <h2><?php echo esc_html__('Upgrade to Pro', 'nexus'); ?></h2>
                        </div>
                        <div class="card-body">
                            <p><?php echo esc_html__('Unlock premium features and templates', 'nexus'); ?></p>
                            <ul class="upgrade-features">
                                <li>✓ 50+ Premium Templates</li>
                                <li>✓ Advanced Customization</li>
                                <li>✓ WooCommerce Integration</li>
                                <li>✓ Priority Support</li>
                                <li>✓ Regular Updates</li>
                            </ul>
                            <a href="https://jdsandigitel.com/shop/" class="button button-primary button-hero" target="_blank">
                                <?php echo esc_html__('Upgrade Now', 'nexus'); ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Quick Links -->
                <div class="nexus-card">
                    <div class="card-header">
                        <h2><?php echo esc_html__('Quick Links', 'nexus'); ?></h2>
                    </div>
                    <div class="card-body">
                        <div class="quick-links">
                            <a href="<?php echo admin_url('admin.php?page=nexus-templates'); ?>">
                                <span class="dashicons dashicons-layout"></span>
                                <?php echo esc_html__('Browse Templates', 'nexus'); ?>
                            </a>
                            <a href="<?php echo admin_url('customize.php'); ?>">
                                <span class="dashicons dashicons-admin-customizer"></span>
                                <?php echo esc_html__('Customize Theme', 'nexus'); ?>
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=nexus-settings'); ?>">
                                <span class="dashicons dashicons-admin-settings"></span>
                                <?php echo esc_html__('Theme Settings', 'nexus'); ?>
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=nexus-license'); ?>">
                                <span class="dashicons dashicons-admin-network"></span>
                                <?php echo esc_html__('License Manager', 'nexus'); ?>
                            </a>
                            <a href="https://jdsandigitel.com/docs/" target="_blank">
                                <span class="dashicons dashicons-book"></span>
                                <?php echo esc_html__('Documentation', 'nexus'); ?>
                            </a>
                            <a href="https://jdsandigitel.com/support/" target="_blank">
                                <span class="dashicons dashicons-sos"></span>
                                <?php echo esc_html__('Support', 'nexus'); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="nexus-card">
                    <div class="card-header">
                        <h2><?php echo esc_html__('System Status', 'nexus'); ?></h2>
                    </div>
                    <div class="card-body">
                        <div class="system-status">
                            <div class="status-item">
                                <span><?php echo esc_html__('WordPress', 'nexus'); ?></span>
                                <span class="status-value"><?php echo get_bloginfo('version'); ?></span>
                            </div>
                            <div class="status-item">
                                <span><?php echo esc_html__('PHP', 'nexus'); ?></span>
                                <span class="status-value"><?php echo phpversion(); ?></span>
                            </div>
                            <div class="status-item">
                                <span><?php echo esc_html__('Theme', 'nexus'); ?></span>
                                <span class="status-value"><?php echo NEXUS_VERSION; ?></span>
                            </div>
                            <div class="status-item">
                                <span><?php echo esc_html__('License', 'nexus'); ?></span>
                                <span class="status-value tier-<?php echo esc_attr($license_tier); ?>">
                                    <?php echo esc_html(ucfirst($license_tier)); ?>
                                </span>
                            </div>
                        </div>
                        <a href="<?php echo admin_url('admin.php?page=nexus-system-info'); ?>" class="button button-small">
                            <?php echo esc_html__('View Full Report', 'nexus'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
