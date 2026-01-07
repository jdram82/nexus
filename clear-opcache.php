<?php
/**
 * Clear OpCache and restart PHP
 * Access this file directly: http://yoursite.com/wp-content/themes/nexus/clear-opcache.php
 */

// Security check
if ( ! isset( $_GET['clear'] ) || $_GET['clear'] !== 'now' ) {
    die( 'Add ?clear=now to the URL to execute' );
}

echo '<h1>Clearing PHP OpCache...</h1>';

// Clear OpCache
if ( function_exists( 'opcache_reset' ) ) {
    opcache_reset();
    echo '<p style="color: green;">✓ OpCache cleared successfully!</p>';
} else {
    echo '<p style="color: orange;">⚠ OpCache not available</p>';
}

// Clear APCu if available
if ( function_exists( 'apcu_clear_cache' ) ) {
    apcu_clear_cache();
    echo '<p style="color: green;">✓ APCu cache cleared!</p>';
}

// Clear WordPress object cache
if ( file_exists( '../../../wp-load.php' ) ) {
    require_once '../../../wp-load.php';
    if ( function_exists( 'wp_cache_flush' ) ) {
        wp_cache_flush();
        echo '<p style="color: green;">✓ WordPress object cache flushed!</p>';
    }
}

echo '<hr>';
echo '<p><strong>The fix has been applied. Please refresh your WordPress admin page.</strong></p>';
echo '<p><a href="/wp-admin/">← Back to WordPress Admin</a></p>';
