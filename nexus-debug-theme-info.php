<?php
/**
 * Plugin Name: Nexus Debug - Theme Info
 * Description: Shows what WordPress sees for Nexus theme
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', 'nexus_debug_menu' );
function nexus_debug_menu() {
    add_submenu_page(
        'themes.php',
        'Theme Debug Info',
        'Theme Debug',
        'manage_options',
        'nexus-theme-debug',
        'nexus_debug_page'
    );
}

function nexus_debug_page() {
    ?>
    <div class="wrap">
        <h1>Nexus Theme Debug Info</h1>
        
        <h2>1. All Installed Themes</h2>
        <pre><?php
        $themes = wp_get_themes();
        foreach ( $themes as $slug => $theme ) {
            echo "Slug: " . $slug . "\n";
            echo "Name: " . $theme->get( 'Name' ) . "\n";
            echo "Version: " . $theme->get( 'Version' ) . "\n";
            echo "Directory: " . $theme->get_stylesheet_directory() . "\n";
            echo "---\n\n";
        }
        ?></pre>
        
        <h2>2. Active Theme</h2>
        <pre><?php
        $theme = wp_get_theme();
        echo "Slug: " . $theme->get_stylesheet() . "\n";
        echo "Name: " . $theme->get( 'Name' ) . "\n";
        echo "Version: " . $theme->get( 'Version' ) . "\n";
        echo "Directory: " . $theme->get_stylesheet_directory() . "\n";
        ?></pre>
        
        <h2>3. Update Transient</h2>
        <pre><?php
        $transient = get_site_transient( 'update_themes' );
        print_r( $transient );
        ?></pre>
        
        <h2>4. Nexus Update Check Transient</h2>
        <pre><?php
        $nexus_cache = get_transient( 'nexus_theme_update_check' );
        print_r( $nexus_cache );
        ?></pre>
    </div>
    <?php
}
