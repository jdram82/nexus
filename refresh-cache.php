<?php
/**
 * Simple cache refresh - just access this file
 * URL: http://yoursite.com/wp-content/themes/nexus/refresh-cache.php
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cache Refresh</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .success { color: green; font-size: 18px; }
        .info { background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Nexus Theme - Cache Refresh</h1>
    
    <?php
    $cleared = array();
    
    // Clear OpCache
    if (function_exists('opcache_reset')) {
        opcache_reset();
        $cleared[] = 'OpCache';
    }
    
    // Clear APCu
    if (function_exists('apcu_clear_cache')) {
        apcu_clear_cache();
        $cleared[] = 'APCu';
    }
    
    // Clear Realpath Cache
    clearstatcache(true);
    $cleared[] = 'Stat Cache';
    
    if (!empty($cleared)) {
        echo '<p class="success">✓ Cleared: ' . implode(', ', $cleared) . '</p>';
    }
    ?>
    
    <div class="info">
        <strong>Fix Applied!</strong><br>
        The updated theme file has been loaded.<br><br>
        <a href="/wp-admin/" style="color: #0073aa; text-decoration: none;">→ Go to WordPress Admin</a>
    </div>
    
    <p><small>If you still see errors, your hosting may need a PHP restart. Contact EasyWP support.</small></p>
</body>
</html>
