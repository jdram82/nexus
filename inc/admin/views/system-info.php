<?php
/**
 * System Info View
 * 
 * @package Nexus
 * @since 3.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Get system info
$system_info = array(
    'WordPress' => get_bloginfo('version'),
    'PHP' => phpversion(),
    'MySQL' => $wpdb->db_version(),
    'Server' => $_SERVER['SERVER_SOFTWARE'],
    'User Agent' => $_SERVER['HTTP_USER_AGENT'],
    'Memory Limit' => ini_get('memory_limit'),
    'Max Upload Size' => wp_max_upload_size(),
    'Post Max Size' => ini_get('post_max_size'),
    'Time Limit' => ini_get('max_execution_time'),
    'Max Input Vars' => ini_get('max_input_vars'),
);

// Get active plugins
$active_plugins = get_option('active_plugins');
$all_plugins = get_plugins();
?>

<div class="wrap nexus-admin-wrap">
    <div class="nexus-admin-header">
        <h1><?php echo esc_html__('System Information', 'nexus'); ?></h1>
        <button class="button copy-system-info"><?php echo esc_html__('Copy to Clipboard', 'nexus'); ?></button>
    </div>

    <div class="nexus-admin-content">
        <div class="nexus-system-info">
            <!-- WordPress Environment -->
            <div class="nexus-card">
                <div class="card-header">
                    <h2><?php echo esc_html__('WordPress Environment', 'nexus'); ?></h2>
                </div>
                <div class="card-body">
                    <table class="system-info-table">
                        <tr>
                            <td><?php echo esc_html__('WordPress Version', 'nexus'); ?></td>
                            <td><?php echo esc_html(get_bloginfo('version')); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo esc_html__('Site URL', 'nexus'); ?></td>
                            <td><?php echo esc_url(get_site_url()); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo esc_html__('Home URL', 'nexus'); ?></td>
                            <td><?php echo esc_url(get_home_url()); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo esc_html__('WP Multisite', 'nexus'); ?></td>
                            <td><?php echo is_multisite() ? esc_html__('Yes', 'nexus') : esc_html__('No', 'nexus'); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo esc_html__('WP Debug Mode', 'nexus'); ?></td>
                            <td><?php echo defined('WP_DEBUG') && WP_DEBUG ? esc_html__('Yes', 'nexus') : esc_html__('No', 'nexus'); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo esc_html__('Language', 'nexus'); ?></td>
                            <td><?php echo esc_html(get_locale()); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Server Environment -->
            <div class="nexus-card">
                <div class="card-header">
                    <h2><?php echo esc_html__('Server Environment', 'nexus'); ?></h2>
                </div>
                <div class="card-body">
                    <table class="system-info-table">
                        <?php foreach ($system_info as $label => $value): ?>
                            <tr>
                                <td><?php echo esc_html($label); ?></td>
                                <td><?php echo esc_html($value); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <!-- Theme Information -->
            <div class="nexus-card">
                <div class="card-header">
                    <h2><?php echo esc_html__('Theme Information', 'nexus'); ?></h2>
                </div>
                <div class="card-body">
                    <table class="system-info-table">
                        <tr>
                            <td><?php echo esc_html__('Theme Name', 'nexus'); ?></td>
                            <td>Nexus</td>
                        </tr>
                        <tr>
                            <td><?php echo esc_html__('Theme Version', 'nexus'); ?></td>
                            <td><?php echo NEXUS_VERSION; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo esc_html__('Theme Directory', 'nexus'); ?></td>
                            <td><?php echo esc_html(get_template_directory()); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo esc_html__('Child Theme', 'nexus'); ?></td>
                            <td><?php echo is_child_theme() ? esc_html__('Yes', 'nexus') : esc_html__('No', 'nexus'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Active Plugins -->
            <div class="nexus-card">
                <div class="card-header">
                    <h2><?php echo esc_html__('Active Plugins', 'nexus'); ?> (<?php echo count($active_plugins); ?>)</h2>
                </div>
                <div class="card-body">
                    <table class="system-info-table">
                        <?php foreach ($active_plugins as $plugin): ?>
                            <?php if (isset($all_plugins[$plugin])): ?>
                                <tr>
                                    <td><?php echo esc_html($all_plugins[$plugin]['Name']); ?></td>
                                    <td><?php echo esc_html($all_plugins[$plugin]['Version']); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<textarea id="system-info-text" style="display: none;">
=== Nexus System Information ===

WordPress Environment:
- WordPress Version: <?php echo get_bloginfo('version'); ?>

- Site URL: <?php echo get_site_url(); ?>

- Home URL: <?php echo get_home_url(); ?>

- Multisite: <?php echo is_multisite() ? 'Yes' : 'No'; ?>

- Debug Mode: <?php echo defined('WP_DEBUG') && WP_DEBUG ? 'Yes' : 'No'; ?>


Server Environment:
<?php foreach ($system_info as $label => $value): ?>
- <?php echo $label; ?>: <?php echo $value; ?>

<?php endforeach; ?>

Theme Information:
- Theme: Nexus
- Version: <?php echo NEXUS_VERSION; ?>

- Directory: <?php echo get_template_directory(); ?>

- Child Theme: <?php echo is_child_theme() ? 'Yes' : 'No'; ?>


Active Plugins (<?php echo count($active_plugins); ?>):
<?php foreach ($active_plugins as $plugin): ?>
<?php if (isset($all_plugins[$plugin])): ?>
- <?php echo $all_plugins[$plugin]['Name']; ?> (<?php echo $all_plugins[$plugin]['Version']; ?>)
<?php endif; ?>
<?php endforeach; ?>
</textarea>

<script>
jQuery(document).ready(function($) {
    $('.copy-system-info').on('click', function() {
        var text = $('#system-info-text').val();
        navigator.clipboard.writeText(text).then(function() {
            alert('<?php echo esc_js(__('System information copied to clipboard!', 'nexus')); ?>');
        });
    });
});
</script>
