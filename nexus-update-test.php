<?php
/**
 * Nexus Update Test & Fix Script
 * 
 * Upload this file to your WordPress root directory
 * Access via: https://yoursite.com/nexus-update-test.php
 * 
 * This will diagnose and fix update detection issues
 */

// Load WordPress
require_once 'wp-load.php';

// Must be admin
if ( ! current_user_can( 'manage_options' ) ) {
    die( 'Access denied. Please login as administrator first.' );
}

echo '<h1>Nexus Update System Diagnostic</h1>';
echo '<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; padding: 20px; max-width: 1000px; margin: 0 auto; }
    .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 10px 0; border-radius: 4px; color: #155724; }
    .error { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 10px 0; border-radius: 4px; color: #721c24; }
    .warning { background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; margin: 10px 0; border-radius: 4px; color: #856404; }
    .info { background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; margin: 10px 0; border-radius: 4px; color: #0c5460; }
    code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    pre { background: #f4f4f4; padding: 15px; border-radius: 4px; overflow-x: auto; }
    button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; }
    button:hover { background: #005a87; }
</style>';

// Step 1: Check if theme exists
echo '<h2>1. Theme Installation</h2>';
$theme = wp_get_theme( 'nexus-theme' );
if ( $theme->exists() ) {
    echo '<div class="success">✅ Nexus theme found</div>';
    echo '<p><strong>Current Version:</strong> ' . $theme->get( 'Version' ) . '</p>';
} else {
    echo '<div class="error">❌ Nexus theme not found</div>';
    echo '<p>Theme folder should be named: <code>nexus-theme</code></p>';
    die();
}

// Step 2: Check GitHub token
echo '<h2>2. GitHub Token</h2>';
if ( defined( 'NEXUS_GITHUB_TOKEN' ) ) {
    echo '<div class="success">✅ GitHub token configured</div>';
    echo '<p>Token: <code>' . substr( NEXUS_GITHUB_TOKEN, 0, 8 ) . '...</code></p>';
} else {
    echo '<div class="error">❌ GitHub token NOT configured</div>';
    echo '<div class="warning">';
    echo '<p><strong>Action Required:</strong> Add this to <code>wp-config.php</code>:</p>';
    echo '<pre>define( \'NEXUS_GITHUB_TOKEN\', \'YOUR_GITHUB_TOKEN_HERE\' );</pre>';
    echo '<p>Add it BEFORE the line: <code>/* That\'s all, stop editing! */</code></p>';
    echo '</div>';
}

// Step 3: Test GitHub API
echo '<h2>3. GitHub API Connection</h2>';

$headers = array(
    'Accept' => 'application/vnd.github.v3+json',
);

if ( defined( 'NEXUS_GITHUB_TOKEN' ) ) {
    $headers['Authorization'] = 'token ' . NEXUS_GITHUB_TOKEN;
}

$response = wp_remote_get( 'https://api.github.com/repos/jdram82/nexus/releases/latest', array(
    'timeout' => 15,
    'headers' => $headers,
) );

if ( is_wp_error( $response ) ) {
    echo '<div class="error">❌ GitHub API Error: ' . $response->get_error_message() . '</div>';
} else {
    $code = wp_remote_retrieve_response_code( $response );
    $body = wp_remote_retrieve_body( $response );
    
    if ( 200 === $code ) {
        $release = json_decode( $body, true );
        echo '<div class="success">✅ GitHub API accessible</div>';
        echo '<p><strong>Latest Release:</strong> ' . $release['tag_name'] . '</p>';
        echo '<p><strong>Published:</strong> ' . date( 'Y-m-d H:i:s', strtotime( $release['published_at'] ) ) . '</p>';
        echo '<p><strong>Download URL:</strong> <code>' . $release['zipball_url'] . '</code></p>';
        
        // Compare versions
        $current_version = $theme->get( 'Version' );
        $latest_version = ltrim( $release['tag_name'], 'v' );
        
        echo '<h3>Version Comparison</h3>';
        echo '<p>Current: <code>' . $current_version . '</code></p>';
        echo '<p>Latest: <code>' . $latest_version . '</code></p>';
        
        if ( version_compare( $current_version, $latest_version, '<' ) ) {
            echo '<div class="warning">📦 Update available: ' . $current_version . ' → ' . $latest_version . '</div>';
        } else {
            echo '<div class="info">ℹ️ You have the latest version</div>';
        }
        
    } elseif ( 403 === $code ) {
        $data = json_decode( $body, true );
        if ( isset( $data['message'] ) && strpos( $data['message'], 'rate limit' ) !== false ) {
            echo '<div class="error">❌ GitHub rate limit exceeded</div>';
            echo '<div class="warning">';
            echo '<p><strong>Solution:</strong> Add GitHub token to wp-config.php (see step 2)</p>';
            echo '</div>';
        } else {
            echo '<div class="error">❌ Access forbidden (403)</div>';
            echo '<pre>' . htmlspecialchars( $body ) . '</pre>';
        }
    } elseif ( 404 === $code ) {
        echo '<div class="error">❌ Release not found (404)</div>';
        echo '<p>Repository or release might not be public</p>';
    } else {
        echo '<div class="error">❌ HTTP ' . $code . '</div>';
        echo '<pre>' . htmlspecialchars( substr( $body, 0, 500 ) ) . '</pre>';
    }
}

// Step 4: Check rate limit
echo '<h2>4. GitHub Rate Limit</h2>';
$rate_response = wp_remote_get( 'https://api.github.com/rate_limit', array(
    'headers' => $headers,
) );

if ( ! is_wp_error( $rate_response ) ) {
    $rate_body = json_decode( wp_remote_retrieve_body( $rate_response ), true );
    $remaining = $rate_body['resources']['core']['remaining'];
    $limit = $rate_body['resources']['core']['limit'];
    $reset = $rate_body['resources']['core']['reset'];
    
    if ( $remaining > 10 ) {
        echo '<div class="success">✅ Rate limit OK: ' . $remaining . ' / ' . $limit . ' remaining</div>';
    } else {
        echo '<div class="warning">⚠️ Rate limit low: ' . $remaining . ' / ' . $limit . ' remaining</div>';
        echo '<p>Resets at: ' . date( 'Y-m-d H:i:s', $reset ) . '</p>';
    }
}

// Step 5: Check update transient
echo '<h2>5. Update Cache</h2>';
$transient = get_transient( 'nexus_theme_update_check' );

if ( $transient ) {
    echo '<div class="info">ℹ️ Update cache exists</div>';
    echo '<pre>' . print_r( $transient, true ) . '</pre>';
} else {
    echo '<div class="warning">⚠️ No update cache found (this is OK, it will be created)</div>';
}

// Step 6: Clear cache and force check
echo '<h2>6. Force Update Check</h2>';

if ( isset( $_GET['clear_cache'] ) ) {
    delete_transient( 'nexus_theme_update_check' );
    delete_site_transient( 'update_themes' );
    wp_clean_themes_cache();
    
    echo '<div class="success">✅ All caches cleared!</div>';
    echo '<p>Checking for updates now...</p>';
    
    // Trigger update check
    wp_update_themes();
    
    echo '<div class="success">✅ Update check triggered</div>';
    echo '<p><a href="' . admin_url( 'themes.php' ) . '">Go to Themes page</a> to see if update appears.</p>';
} else {
    echo '<form method="get">';
    echo '<input type="hidden" name="clear_cache" value="1">';
    echo '<button type="submit">Clear Cache & Force Update Check</button>';
    echo '</form>';
}

// Step 7: Check WordPress update settings
echo '<h2>7. WordPress Update Settings</h2>';

$auto_update = get_site_option( 'auto_update_themes' );
echo '<p><strong>Auto-update themes:</strong> ' . ( $auto_update ? 'Enabled' : 'Disabled' ) . '</p>';

echo '<p><strong>WordPress Version:</strong> ' . get_bloginfo( 'version' ) . '</p>';
echo '<p><strong>PHP Version:</strong> ' . phpversion() . '</p>';

// Step 8: Manual update instructions
echo '<h2>8. Manual Update (If Automatic Fails)</h2>';
echo '<div class="info">';
echo '<p>If automatic updates still don\'t work, you can update manually:</p>';
echo '<ol>';
echo '<li>Download: <a href="https://github.com/jdram82/nexus/releases/latest" target="_blank">Latest Release</a></li>';
echo '<li>Go to <a href="' . admin_url( 'themes.php' ) . '">Appearance → Themes</a></li>';
echo '<li>Activate a different theme temporarily</li>';
echo '<li>Delete Nexus theme</li>';
echo '<li>Click "Add New" → "Upload Theme"</li>';
echo '<li>Upload downloaded ZIP file</li>';
echo '<li>Activate Nexus theme</li>';
echo '</ol>';
echo '</div>';

// Summary
echo '<h2>✅ Summary</h2>';
echo '<div class="info">';
echo '<p><strong>Next Steps:</strong></p>';
echo '<ol>';
if ( ! defined( 'NEXUS_GITHUB_TOKEN' ) ) {
    echo '<li>Add GitHub token to wp-config.php (see step 2)</li>';
}
echo '<li>Click "Clear Cache & Force Update Check" button above</li>';
echo '<li>Go to <a href="' . admin_url( 'themes.php' ) . '">Appearance → Themes</a></li>';
echo '<li>Look for "Update available" on Nexus theme</li>';
echo '</ol>';
echo '</div>';

echo '<hr>';
echo '<p><small>Script location: ' . __FILE__ . '</small></p>';
echo '<p><small>Delete this file after troubleshooting</small></p>';
