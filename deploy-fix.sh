#!/bin/bash
# Deploy the fixed theme to WordPress installation

echo "Deploying Nexus Theme Fix..."

# Define paths
THEME_SOURCE="/workspaces/codespaces-blank/nexus-theme"
WP_THEME_DIR="/var/www/wptbox/wp-content/themes/nexus"

# Check if WordPress directory exists
if [ ! -d "/var/www/wptbox" ]; then
    echo "ERROR: WordPress directory /var/www/wptbox not accessible from this container"
    echo ""
    echo "Please manually:"
    echo "1. Copy the updated file to your WordPress installation:"
    echo "   cp ${THEME_SOURCE}/inc/class-nexus-license-manager.php ${WP_THEME_DIR}/inc/"
    echo ""
    echo "2. Clear WordPress cache (if using object cache):"
    echo "   wp cache flush"
    echo ""
    echo "3. Restart PHP-FPM or Apache:"
    echo "   sudo systemctl restart php-fpm"
    echo "   # or"
    echo "   sudo systemctl restart apache2"
    exit 1
fi

# Copy the fixed file
echo "Copying updated license manager..."
cp -f "${THEME_SOURCE}/inc/class-nexus-license-manager.php" "${WP_THEME_DIR}/inc/"

# Clear WordPress cache
echo "Clearing WordPress cache..."
if command -v wp &> /dev/null; then
    wp cache flush --path=/var/www/wptbox
fi

# Try to restart PHP-FPM
echo "Attempting to restart PHP..."
if command -v systemctl &> /dev/null; then
    sudo systemctl restart php-fpm 2>/dev/null || sudo systemctl restart php7.4-fpm 2>/dev/null || sudo systemctl restart php8.1-fpm 2>/dev/null || true
fi

echo ""
echo "Fix deployed! Please refresh your WordPress admin page."
