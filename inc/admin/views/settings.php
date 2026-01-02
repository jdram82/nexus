<?php
/**
 * Settings View
 * 
 * @package Nexus
 * @since 3.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$license_tier = isset($license['tier']) ? $license['tier'] : 'free';
$is_pro = in_array($license_tier, array('pro', 'advanced', 'agency'));
$settings = get_option('nexus_theme_settings', array());
?>

<div class="wrap nexus-admin-wrap">
    <div class="nexus-admin-header">
        <h1><?php echo esc_html__('Theme Settings', 'nexus'); ?></h1>
    </div>

    <div class="nexus-admin-content">
        <form method="post" action="">
            <?php wp_nonce_field('nexus-admin', 'nexus_nonce'); ?>
            <input type="hidden" name="nexus_action" value="save_settings">

            <div class="nexus-settings-grid">
                <!-- Performance Settings -->
                <div class="nexus-card">
                    <div class="card-header">
                        <h2><?php echo esc_html__('Performance', 'nexus'); ?></h2>
                    </div>
                    <div class="card-body">
                        <table class="form-table">
                            <tr>
                                <th><?php echo esc_html__('Minify CSS', 'nexus'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="nexus_settings[minify_css]" value="1" <?php checked(isset($settings['minify_css'])); ?>>
                                        <?php echo esc_html__('Enable CSS minification', 'nexus'); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo esc_html__('Minify JavaScript', 'nexus'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="nexus_settings[minify_js]" value="1" <?php checked(isset($settings['minify_js'])); ?>>
                                        <?php echo esc_html__('Enable JavaScript minification', 'nexus'); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo esc_html__('Lazy Load Images', 'nexus'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="nexus_settings[lazy_load]" value="1" <?php checked(isset($settings['lazy_load'])); ?>>
                                        <?php echo esc_html__('Enable lazy loading for images', 'nexus'); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Header Settings -->
                <div class="nexus-card">
                    <div class="card-header">
                        <h2><?php echo esc_html__('Header', 'nexus'); ?></h2>
                    </div>
                    <div class="card-body">
                        <table class="form-table">
                            <tr>
                                <th><?php echo esc_html__('Header Style', 'nexus'); ?></th>
                                <td>
                                    <select name="nexus_settings[header_style]">
                                        <option value="default" <?php selected(isset($settings['header_style']) ? $settings['header_style'] : 'default', 'default'); ?>>
                                            <?php echo esc_html__('Default', 'nexus'); ?>
                                        </option>
                                        <option value="centered" <?php selected(isset($settings['header_style']) ? $settings['header_style'] : '', 'centered'); ?>>
                                            <?php echo esc_html__('Centered', 'nexus'); ?>
                                        </option>
                                        <option value="minimal" <?php selected(isset($settings['header_style']) ? $settings['header_style'] : '', 'minimal'); ?>>
                                            <?php echo esc_html__('Minimal', 'nexus'); ?>
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo esc_html__('Sticky Header', 'nexus'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="nexus_settings[sticky_header]" value="1" <?php checked(isset($settings['sticky_header'])); ?>>
                                        <?php echo esc_html__('Enable sticky header on scroll', 'nexus'); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Footer Settings -->
                <div class="nexus-card">
                    <div class="card-header">
                        <h2><?php echo esc_html__('Footer', 'nexus'); ?></h2>
                    </div>
                    <div class="card-body">
                        <table class="form-table">
                            <tr>
                                <th><?php echo esc_html__('Footer Columns', 'nexus'); ?></th>
                                <td>
                                    <select name="nexus_settings[footer_columns]">
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                            <option value="<?php echo $i; ?>" <?php selected(isset($settings['footer_columns']) ? $settings['footer_columns'] : 3, $i); ?>>
                                                <?php echo sprintf(esc_html__('%d Columns', 'nexus'), $i); ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo esc_html__('Copyright Text', 'nexus'); ?></th>
                                <td>
                                    <input type="text" name="nexus_settings[copyright_text]" value="<?php echo esc_attr(isset($settings['copyright_text']) ? $settings['copyright_text'] : ''); ?>" class="regular-text" placeholder="© 2025 Your Site Name">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Advanced Settings (Pro) -->
                <div class="nexus-card <?php echo !$is_pro ? 'locked-card' : ''; ?>">
                    <div class="card-header">
                        <h2>
                            <?php echo esc_html__('Advanced Settings', 'nexus'); ?>
                            <?php if (!$is_pro): ?>
                                <span class="pro-badge">PRO</span>
                            <?php endif; ?>
                        </h2>
                    </div>
                    <div class="card-body">
                        <?php if (!$is_pro): ?>
                            <div class="upgrade-overlay">
                                <span class="dashicons dashicons-lock"></span>
                                <p><?php echo esc_html__('Upgrade to Pro to access advanced settings', 'nexus'); ?></p>
                                <a href="https://jdsandigitel.com/shop/" class="button button-primary" target="_blank">
                                    <?php echo esc_html__('Upgrade Now', 'nexus'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <table class="form-table" <?php echo !$is_pro ? 'style="opacity: 0.5; pointer-events: none;"' : ''; ?>>
                            <tr>
                                <th><?php echo esc_html__('Custom CSS', 'nexus'); ?></th>
                                <td>
                                    <textarea name="nexus_settings[custom_css]" rows="10" class="large-text code" <?php disabled(!$is_pro); ?>><?php echo esc_textarea(isset($settings['custom_css']) ? $settings['custom_css'] : ''); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo esc_html__('Custom JavaScript', 'nexus'); ?></th>
                                <td>
                                    <textarea name="nexus_settings[custom_js]" rows="10" class="large-text code" <?php disabled(!$is_pro); ?>><?php echo esc_textarea(isset($settings['custom_js']) ? $settings['custom_js'] : ''); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo esc_html__('Google Analytics', 'nexus'); ?></th>
                                <td>
                                    <input type="text" name="nexus_settings[google_analytics]" value="<?php echo esc_attr(isset($settings['google_analytics']) ? $settings['google_analytics'] : ''); ?>" class="regular-text" placeholder="UA-XXXXXXXXX-X" <?php disabled(!$is_pro); ?>>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="nexus-settings-footer">
                <?php submit_button(__('Save Settings', 'nexus'), 'primary', 'submit', false); ?>
                <button type="submit" name="nexus_action" value="reset_settings" class="button" onclick="return confirm('<?php echo esc_js(__('Are you sure you want to reset all settings?', 'nexus')); ?>');">
                    <?php echo esc_html__('Reset to Defaults', 'nexus'); ?>
                </button>
            </div>
        </form>
    </div>
</div>
