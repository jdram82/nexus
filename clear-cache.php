<?php
/**
 * WordPress Cache Clearing Script
 * Upload this to your WordPress root and visit it to clear caches
 */

// Clear OPcache if available
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✓ OPcache cleared<br>";
} else {
    echo "× OPcache not available<br>";
}

// Clear APCu if available
if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    echo "✓ APCu cache cleared<br>";
} else {
    echo "× APCu not available<br>";
}

// Clear realpath cache
clearstatcache(true);
echo "✓ Stat cache cleared<br>";

echo "<br><strong>Cache clearing complete!</strong><br>";
echo "Please refresh your WordPress site now.";
