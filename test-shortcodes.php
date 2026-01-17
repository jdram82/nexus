<?php
/**
 * DIAGNOSTIC SCRIPT - Test if shortcodes are registered
 * Upload this file to your WordPress root directory
 * Then visit: yoursite.com/test-shortcodes.php
 */

// Load WordPress
require_once('wp-load.php');

echo '<h1>UL-NEC Shortcode Diagnostic</h1>';

// Check if plugin is active
$active_plugins = get_option('active_plugins');
$plugin_active = in_array('ul-nec-compliance/ul-nec-compliance.php', $active_plugins);

echo '<h2>1. Plugin Status</h2>';
echo '<p>Plugin Active: ' . ($plugin_active ? '✅ YES' : '❌ NO') . '</p>';

if (!$plugin_active) {
    echo '<p style="color: red;"><strong>The plugin is NOT activated! Please activate it in WordPress Admin → Plugins</strong></p>';
}

// Check if shortcodes are registered
global $shortcode_tags;

echo '<h2>2. Registered Shortcodes</h2>';

$ulnec_shortcodes = [
    'ulnec_login',
    'ulnec_register', 
    'ulnec_dashboard',
    'ulnec_download'
];

foreach ($ulnec_shortcodes as $shortcode) {
    $registered = isset($shortcode_tags[$shortcode]);
    echo '<p>[' . $shortcode . ']: ' . ($registered ? '✅ Registered' : '❌ NOT Registered') . '</p>';
}

// Check if classes exist
echo '<h2>3. Plugin Classes</h2>';

$classes = [
    'ULNEC_Plugin',
    'ULNEC_Supabase',
    'ULNEC_Shortcodes',
    'ULNEC_Auth'
];

foreach ($classes as $class) {
    $exists = class_exists($class);
    echo '<p>' . $class . ': ' . ($exists ? '✅ Loaded' : '❌ NOT Loaded') . '</p>';
}

// Test rendering a shortcode
echo '<h2>4. Shortcode Render Test</h2>';
if (isset($shortcode_tags['ulnec_login'])) {
    echo '<p>Attempting to render [ulnec_login] shortcode...</p>';
    echo '<div style="border: 2px solid #ccc; padding: 20px; background: #f9f9f9;">';
    echo do_shortcode('[ulnec_login]');
    echo '</div>';
} else {
    echo '<p style="color: red;">❌ Shortcode not registered, cannot test rendering</p>';
}

// Check plugin files
echo '<h2>5. Plugin Files</h2>';

$plugin_dir = WP_PLUGIN_DIR . '/ul-nec-compliance/';
$files_to_check = [
    'ul-nec-compliance.php',
    'includes/class-ulnec-shortcodes.php',
    'includes/class-ulnec-supabase.php',
    'includes/class-ulnec-auth.php'
];

foreach ($files_to_check as $file) {
    $exists = file_exists($plugin_dir . $file);
    echo '<p>' . $file . ': ' . ($exists ? '✅ Exists' : '❌ Missing') . '</p>';
}

// Show plugin version
if (defined('ULNEC_VERSION')) {
    echo '<h2>6. Plugin Version</h2>';
    echo '<p>Version: ' . ULNEC_VERSION . '</p>';
} else {
    echo '<p style="color: red;">ULNEC_VERSION constant not defined</p>';
}

echo '<hr>';
echo '<h2>Troubleshooting Steps</h2>';
echo '<ol>';
echo '<li>If plugin is NOT active: Activate it in WordPress Admin → Plugins</li>';
echo '<li>If shortcodes are NOT registered: Deactivate and reactivate the plugin</li>';
echo '<li>If classes are NOT loaded: Check for PHP errors in WordPress debug log</li>';
echo '<li>If files are missing: Re-upload the plugin zip file</li>';
echo '</ol>';

echo '<p><strong>After fixing, refresh this page to re-test.</strong></p>';
