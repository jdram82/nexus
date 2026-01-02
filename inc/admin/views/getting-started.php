<?php
/**
 * Getting Started View
 * 
 * @package Nexus
 * @since 3.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap nexus-admin-wrap">
    <div class="nexus-admin-header">
        <h1><?php echo esc_html__('Getting Started with Nexus', 'nexus'); ?></h1>
        <p class="subtitle"><?php echo esc_html__('Welcome! Let\'s get your website up and running.', 'nexus'); ?></p>
    </div>

    <div class="nexus-admin-content">
        <div class="nexus-welcome-grid">
            <!-- Video Tutorial -->
            <div class="nexus-card welcome-video">
                <div class="card-header">
                    <h2><?php echo esc_html__('Watch the Tutorial', 'nexus'); ?></h2>
                </div>
                <div class="card-body">
                    <div class="video-placeholder">
                        <span class="dashicons dashicons-video-alt3"></span>
                        <p><?php echo esc_html__('5-minute quick start guide', 'nexus'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Setup Steps -->
            <div class="nexus-card">
                <div class="card-header">
                    <h2><?php echo esc_html__('Setup Steps', 'nexus'); ?></h2>
                </div>
                <div class="card-body">
                    <div class="setup-steps">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h3><?php echo esc_html__('Activate Your License', 'nexus'); ?></h3>
                                <p><?php echo esc_html__('Enter your license key to unlock premium features', 'nexus'); ?></p>
                                <a href="<?php echo admin_url('admin.php?page=nexus-license'); ?>" class="button">
                                    <?php echo esc_html__('Activate License', 'nexus'); ?>
                                </a>
                            </div>
                        </div>

                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h3><?php echo esc_html__('Choose a Template', 'nexus'); ?></h3>
                                <p><?php echo esc_html__('Import a pre-built template or start from scratch', 'nexus'); ?></p>
                                <a href="<?php echo admin_url('admin.php?page=nexus-templates'); ?>" class="button">
                                    <?php echo esc_html__('Browse Templates', 'nexus'); ?>
                                </a>
                            </div>
                        </div>

                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h3><?php echo esc_html__('Customize Your Design', 'nexus'); ?></h3>
                                <p><?php echo esc_html__('Adjust colors, fonts, and layouts to match your brand', 'nexus'); ?></p>
                                <a href="<?php echo admin_url('customize.php'); ?>" class="button">
                                    <?php echo esc_html__('Open Customizer', 'nexus'); ?>
                                </a>
                            </div>
                        </div>

                        <div class="step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h3><?php echo esc_html__('Configure Settings', 'nexus'); ?></h3>
                                <p><?php echo esc_html__('Fine-tune performance and functionality options', 'nexus'); ?></p>
                                <a href="<?php echo admin_url('admin.php?page=nexus-settings'); ?>" class="button">
                                    <?php echo esc_html__('View Settings', 'nexus'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resources -->
            <div class="nexus-card">
                <div class="card-header">
                    <h2><?php echo esc_html__('Helpful Resources', 'nexus'); ?></h2>
                </div>
                <div class="card-body">
                    <div class="resources-grid">
                        <a href="https://jdsandigitel.com/docs/" class="resource-item" target="_blank">
                            <span class="dashicons dashicons-book-alt"></span>
                            <h4><?php echo esc_html__('Documentation', 'nexus'); ?></h4>
                            <p><?php echo esc_html__('Comprehensive guides and tutorials', 'nexus'); ?></p>
                        </a>

                        <a href="https://jdsandigitel.com/support/" class="resource-item" target="_blank">
                            <span class="dashicons dashicons-sos"></span>
                            <h4><?php echo esc_html__('Support', 'nexus'); ?></h4>
                            <p><?php echo esc_html__('Get help from our support team', 'nexus'); ?></p>
                        </a>

                        <a href="https://jdsandigitel.com/community/" class="resource-item" target="_blank">
                            <span class="dashicons dashicons-groups"></span>
                            <h4><?php echo esc_html__('Community', 'nexus'); ?></h4>
                            <p><?php echo esc_html__('Connect with other Nexus users', 'nexus'); ?></p>
                        </a>

                        <a href="https://jdsandigitel.com/changelog/" class="resource-item" target="_blank">
                            <span class="dashicons dashicons-megaphone"></span>
                            <h4><?php echo esc_html__('Changelog', 'nexus'); ?></h4>
                            <p><?php echo esc_html__('See what\'s new in each update', 'nexus'); ?></p>
                        </a>
                    </div>
                </div>
            </div>

            <!-- FAQs -->
            <div class="nexus-card">
                <div class="card-header">
                    <h2><?php echo esc_html__('Frequently Asked Questions', 'nexus'); ?></h2>
                </div>
                <div class="card-body">
                    <div class="faq-list">
                        <details class="faq-item">
                            <summary><?php echo esc_html__('How do I activate my license?', 'nexus'); ?></summary>
                            <p><?php echo esc_html__('Go to Nexus → License and enter your license key. Click "Activate License" to unlock premium features.', 'nexus'); ?></p>
                        </details>

                        <details class="faq-item">
                            <summary><?php echo esc_html__('Can I use Nexus on multiple sites?', 'nexus'); ?></summary>
                            <p><?php echo esc_html__('It depends on your license tier. Pro (1 site), Advanced (3 sites), Agency (unlimited sites).', 'nexus'); ?></p>
                        </details>

                        <details class="faq-item">
                            <summary><?php echo esc_html__('How do I import a template?', 'nexus'); ?></summary>
                            <p><?php echo esc_html__('Visit Nexus → Templates, choose a template, and click "Import". The demo content will be imported to your site.', 'nexus'); ?></p>
                        </details>

                        <details class="faq-item">
                            <summary><?php echo esc_html__('Where can I get support?', 'nexus'); ?></summary>
                            <p><?php echo esc_html__('Pro users get priority support at https://jdsandigitel.com/support/. Free users can use the WordPress.org forums.', 'nexus'); ?></p>
                        </details>

                        <details class="faq-item">
                            <summary><?php echo esc_html__('Is WooCommerce supported?', 'nexus'); ?></summary>
                            <p><?php echo esc_html__('Yes! Nexus has full WooCommerce integration available in Pro, Advanced, and Agency tiers.', 'nexus'); ?></p>
                        </details>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
