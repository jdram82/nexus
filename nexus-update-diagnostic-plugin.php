<?php
/**
 * Plugin Name: Nexus Update Diagnostic
 * Description: Diagnose and fix Nexus theme update detection issues
 * Version: 1.0.0
 * Author: JDRAM
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Add admin menu
add_action( 'admin_menu', 'nexus_diagnostic_menu' );
function nexus_diagnostic_menu() {
    add_submenu_page(
        'themes.php',
        'Nexus Update Diagnostic',
        'Update Diagnostic',
        'manage_options',
        'nexus-diagnostic',
        'nexus_diagnostic_page'
    );
}

// Diagnostic page
function nexus_diagnostic_page() {
    ?>
    <div class="wrap">
        <h1>Nexus Update System Diagnostic</h1>
        <style>
            .nexus-success { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 10px 0; border-radius: 4px; }
            .nexus-error { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 10px 0; border-radius: 4px; }
            .nexus-warning { background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; margin: 10px 0; border-radius: 4px; }
            .nexus-info { background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; margin: 10px 0; border-radius: 4px; }
        </style>
        
        <?php
        // Check theme
        echo '<h2>1. Theme Installation</h2>';
        $theme = wp_get_theme( 'nexus-theme' );
        if ( $theme->exists() ) {
            echo '<div class="nexus-success">✅ Nexus theme found - Version: ' . $theme->get( 'Version' ) . '</div>';
        } else {
            echo '<div class="nexus-error">❌ Nexus theme not found. Folder should be named: nexus-theme</div>';
        }
        
        // Check GitHub token
        echo '<h2>2. GitHub Token</h2>';
        if ( defined( 'NEXUS_GITHUB_TOKEN' ) ) {
            echo '<div class="nexus-success">✅ GitHub token configured</div>';
        } else {
            echo '<div class="nexus-error">❌ GitHub token NOT configured</div>';
            echo '<div class="nexus-warning"><p><strong>Add to wp-config.php:</strong></p>';
            echo '<code>define( \'NEXUS_GITHUB_TOKEN\', \'YOUR_TOKEN_HERE\' );</code></div>';
        }
        
        // Test GitHub API
        echo '<h2>3. GitHub API Test</h2>';
        $headers = array( 'Accept' => 'application/vnd.github.v3+json' );
        if ( defined( 'NEXUS_GITHUB_TOKEN' ) ) {
            $headers['Authorization'] = 'token ' . NEXUS_GITHUB_TOKEN;
        }
        
        $response = wp_remote_get( 'https://api.github.com/repos/jdram82/nexus/releases/latest', array(
            'timeout' => 15,
            'headers' => $headers,
        ) );
        
        if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
            $release = json_decode( wp_remote_retrieve_body( $response ), true );
            echo '<div class="nexus-success">✅ GitHub API accessible - Latest: ' . $release['tag_name'] . '</div>';
            
            if ( $theme->exists() ) {
                $current = $theme->get( 'Version' );
                $latest = ltrim( $release['tag_name'], 'v' );
                
                if ( version_compare( $current, $latest, '<' ) ) {
                    echo '<div class="nexus-warning">📦 Update available: ' . $current . ' → ' . $latest . '</div>';
                } else {
                    echo '<div class="nexus-info">ℹ️ You have the latest version</div>';
                }
            }
        } else {
            echo '<div class="nexus-error">❌ GitHub API error</div>';
            if ( is_wp_error( $response ) ) {
                echo '<p>' . $response->get_error_message() . '</p>';
            }
        }
        
        // Clear cache button
        echo '<h2>4. Clear Update Cache</h2>';
        if ( isset( $_POST['clear_cache'] ) && check_admin_referer( 'nexus_clear_cache' ) ) {
            delete_transient( 'nexus_theme_update_check' );
            delete_site_transient( 'update_themes' );
            wp_clean_themes_cache();
            wp_update_themes();
            echo '<div class="nexus-success">✅ Cache cleared and update check triggered!</div>';
            echo '<p><a href="' . admin_url( 'themes.php' ) . '" class="button button-primary">Go to Themes Page</a></p>';
        } else {
            echo '<form method="post">';
            wp_nonce_field( 'nexus_clear_cache' );
            echo '<input type="hidden" name="clear_cache" value="1">';
            echo '<button type="submit" class="button button-primary">Clear Cache & Force Update Check</button>';
            echo '</form>';
        }
        
        // Instructions
        echo '<h2>5. Next Steps</h2>';
        echo '<div class="nexus-info">';
        echo '<ol>';
        if ( ! defined( 'NEXUS_GITHUB_TOKEN' ) ) {
            echo '<li>Add GitHub token to wp-config.php</li>';
        }
        echo '<li>Click "Clear Cache & Force Update Check" button</li>';
        echo '<li>Go to Appearance → Themes</li>';
        echo '<li>Look for "Update available" notice</li>';
        echo '</ol>';
        echo '</div>';
        ?>
    </div>
    <?php
}
