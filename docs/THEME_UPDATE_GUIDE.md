# Nexus Theme Update Guide

A complete guide to updating your Nexus theme safely and efficiently.

## Table of Contents

1. [Automatic Updates](#automatic-updates)
2. [Manual Updates](#manual-updates)
3. [Before You Update](#before-you-update)
4. [After Update Checklist](#after-update-checklist)
5. [Troubleshooting](#troubleshooting)
6. [FAQ](#faq)

---

## Automatic Updates

Nexus includes a **GitHub-based automatic update system**. You no longer need to manually download and reinstall the theme!

### How It Works

1. **Automatic Checks**: Nexus checks GitHub for new releases every 12 hours
2. **Notifications**: When an update is available, you'll see a notification in WordPress admin
3. **One-Click Update**: Click "Update Now" to download and install automatically
4. **Settings Preserved**: All your customizations and settings are kept safe

### Update from WordPress Admin

**Method 1: Dashboard Notification**

When an update is available, you'll see a notification on your Dashboard:

```
Nexus Theme Update Available!
Version 1.6.0 is available. You have version 1.5.0.
[Update Now] [View Release Notes]
```

Click **"Update Now"** to start the automatic update.

**Method 2: Appearance → Themes**

1. Go to **Appearance → Themes**
2. Find Nexus theme card
3. If update available, you'll see **"Update Available"** badge
4. Click **"Update Now"**

**Method 3: Appearance → Updates**

1. Go to **Appearance → Updates** (Nexus custom page)
2. View current version and latest version
3. Click **"Update Now"** if update available
4. Or click **"Check for Updates Now"** to force check

### Update Process

When you click "Update Now", WordPress will:

1. ✅ Download latest version from GitHub
2. ✅ Backup current theme files
3. ✅ Extract new theme files
4. ✅ Replace old files with new files
5. ✅ Preserve all your settings
6. ✅ Clear theme cache
7. ✅ Show success message

**Total time:** Usually 10-30 seconds depending on your server.

---

## Manual Updates

If automatic updates fail or you prefer manual control, you can update manually.

### Prerequisites

Before manual update:
- [ ] WordPress admin access
- [ ] FTP/SSH access (for method 2)
- [ ] Downloaded latest theme zip from GitHub
- [ ] Backup completed

### Method 1: WordPress Admin Upload

1. **Download Latest Release**
   - Visit: https://github.com/jdram82/nexus/releases/latest
   - Click on **"Source code (zip)"**
   - Save as `nexus-theme-1.6.0.zip`

2. **Prepare WordPress**
   - Go to **Appearance → Themes**
   - **Important**: Activate a different theme temporarily (e.g., Twenty Twenty-Four)
   - This allows you to delete the old Nexus theme

3. **Delete Old Theme**
   - Find Nexus theme card
   - Click **"Theme Details"**
   - Click **"Delete"** in bottom-right
   - Confirm deletion

4. **Upload New Theme**
   - Click **"Add New"** button
   - Click **"Upload Theme"**
   - Click **"Choose File"**
   - Select downloaded `nexus-theme-1.6.0.zip`
   - Click **"Install Now"**
   - Wait for upload to complete

5. **Activate Theme**
   - Click **"Activate"** when upload finishes
   - Or go to **Appearance → Themes** and activate Nexus

6. **Verify Update**
   - Go to **Appearance → Updates**
   - Confirm version shows `1.6.0` (or latest)

### Method 2: FTP/SSH Upload

**Via FTP (FileZilla, Cyberduck, etc.)**

1. **Connect to your server**
   ```
   Host: ftp.yoursite.com
   Username: your-ftp-username
   Password: your-ftp-password
   Port: 21 (or 22 for SFTP)
   ```

2. **Navigate to theme directory**
   ```
   /public_html/wp-content/themes/
   ```

3. **Backup old theme**
   - Right-click `nexus-theme` folder
   - Select "Download" (saves backup to your computer)
   - Or rename to `nexus-theme-backup`

4. **Delete old theme**
   - Delete `nexus-theme` folder
   - Confirm deletion

5. **Upload new theme**
   - Unzip `nexus-theme-1.6.0.zip` on your computer
   - Upload extracted `nexus-theme` folder to `/wp-content/themes/`
   - Wait for all files to upload (500+ files)

6. **Set permissions**
   - Right-click `nexus-theme` folder
   - Select "File Permissions"
   - Set to `755` for folders
   - Set to `644` for files
   - Check "Recurse into subdirectories"
   - Click OK

**Via SSH (Terminal/PuTTY)**

```bash
# Connect to your server
ssh username@yoursite.com

# Navigate to themes directory
cd /var/www/html/wp-content/themes/

# Backup old theme
cp -r nexus-theme nexus-theme-backup

# Delete old theme
rm -rf nexus-theme

# Upload new theme (if you have it locally)
# Method A: SCP from your computer
# Run this from your local terminal:
scp -r /path/to/nexus-theme username@yoursite.com:/var/www/html/wp-content/themes/

# Method B: Download from GitHub directly on server
cd /var/www/html/wp-content/themes/
wget https://github.com/jdram82/nexus/archive/refs/tags/v1.6.0.zip
unzip v1.6.0.zip
mv nexus-1.6.0 nexus-theme

# Set correct permissions
chown -R www-data:www-data nexus-theme
chmod -R 755 nexus-theme
find nexus-theme -type f -exec chmod 644 {} \;

# Clear WordPress cache
cd /var/www/html/
wp cache flush
```

### Method 3: WP-CLI (Advanced)

```bash
# SSH into your server
ssh username@yoursite.com

# Navigate to WordPress directory
cd /var/www/html/

# Backup database
wp db export backup-$(date +%Y%m%d).sql

# Download and install latest theme from GitHub
wp theme install https://github.com/jdram82/nexus/archive/refs/tags/v1.6.0.zip --activate --force

# Verify installation
wp theme status nexus-theme

# Clear cache
wp cache flush
```

---

## Before You Update

### Pre-Update Checklist

Before updating Nexus, complete this checklist:

- [ ] **Backup Everything**
  - Database backup
  - Files backup (especially `wp-content/` folder)
  - Use backup plugin like UpdraftPlus or VaultPress

- [ ] **Read Release Notes**
  - Visit: https://github.com/jdram82/nexus/releases/latest
  - Check for breaking changes
  - Note new features
  - Review compatibility requirements

- [ ] **Check Compatibility**
  - WordPress version: 6.0+ required
  - PHP version: 7.4+ required
  - Server resources: 256MB+ memory recommended
  - Disk space: 50MB+ free

- [ ] **Test on Staging Site** (Recommended)
  - Clone production site to staging
  - Update theme on staging first
  - Test all pages and features
  - Check custom code compatibility
  - Only then update production

- [ ] **Notify Users** (For live sites)
  - Enable maintenance mode
  - Notify users of brief downtime
  - Schedule update during low-traffic period

- [ ] **Disable Caching**
  - Temporarily disable caching plugins
  - This prevents cache issues during update
  - Re-enable after update completes

### Backup Instructions

**Option 1: Via Backup Plugin (Easiest)**

1. Install UpdraftPlus (free)
2. Go to **Settings → UpdraftPlus Backups**
3. Click **"Backup Now"**
4. Select "Include database" and "Include files"
5. Wait for backup to complete
6. Download backup to your computer

**Option 2: Via Hosting Control Panel**

Most hosts (SiteGround, Bluehost, etc.) offer one-click backups:
1. Log into your hosting control panel
2. Find "Backup" or "Site Backup" section
3. Click "Create Backup Now"
4. Wait for confirmation email

**Option 3: Via WP-CLI**

```bash
# Backup database
wp db export nexus-backup-$(date +%Y%m%d).sql

# Backup theme files
cd wp-content/themes/
tar -czf nexus-theme-backup-$(date +%Y%m%d).tar.gz nexus-theme/

# Backup uploads
cd ../uploads/
tar -czf uploads-backup-$(date +%Y%m%d).tar.gz .
```

**Option 4: Manual Backup**

1. **Database**: Use phpMyAdmin → Export
2. **Files**: Download via FTP entire `wp-content/themes/nexus-theme/` folder

---

## After Update Checklist

After updating, verify everything works:

### Immediate Checks

- [ ] **Theme Activated**: Go to Appearance → Themes, confirm Nexus is active
- [ ] **Version Correct**: Go to Appearance → Updates, confirm version number
- [ ] **Homepage Loads**: Visit your homepage, check for errors
- [ ] **Admin Access**: Confirm WordPress admin still works

### Functionality Checks

- [ ] **Navigation Menus**: Check all menu items work
- [ ] **Widgets**: Verify sidebar/footer widgets display correctly
- [ ] **Customizer Settings**: Check Appearance → Customize for all settings
- [ ] **Custom Post Types**: Verify Products, Projects, Downloads still work
- [ ] **Forms**: Test contact forms and other forms
- [ ] **E-commerce**: If using WooCommerce, test checkout process

### Pro Tier Checks (If Applicable)

- [ ] **License Status**: Verify Pro license still active
- [ ] **Cloud Storage**: Test DigitalOcean Spaces connection
- [ ] **Payment Gateways**: Test Razorpay/PayPal if configured
- [ ] **Template Sync**: Verify cloud sync still works

### Performance Checks

- [ ] **Page Speed**: Run Google PageSpeed Insights
- [ ] **Mobile Responsive**: Test on mobile devices
- [ ] **Browser Compatibility**: Test in Chrome, Firefox, Safari
- [ ] **Console Errors**: Open browser DevTools, check for JavaScript errors

### Clean Up

- [ ] **Clear Caches**
  ```bash
  # WordPress cache
  wp cache flush
  
  # Caching plugins (if using)
  wp w3tc flush all  # W3 Total Cache
  wp super-cache flush  # WP Super Cache
  ```

- [ ] **Re-enable Caching**: Turn caching plugins back on

- [ ] **Remove Backup** (After 7 days): If everything works, you can delete backup

---

## Troubleshooting

### Update Check Fails

**Symptom**: "No updates available" when you know there's a new version

**Solution**:
```bash
# Method 1: Clear update cache via WP-CLI
wp transient delete nexus_theme_update_check

# Method 2: Clear update cache manually
# Add this to functions.php temporarily:
delete_transient('nexus_theme_update_check');
# Then reload any admin page, remove the code

# Method 3: Force check from admin
# Go to Appearance → Updates
# Click "Clear Update Cache" button
```

### GitHub API Rate Limit

**Symptom**: Error message "GitHub API rate limit exceeded"

**Solution**:
- Wait 1 hour (GitHub resets rate limits hourly)
- Or use manual update method
- Or add GitHub token to updater (advanced)

### Download Fails

**Symptom**: "Download failed" or "Could not download theme"

**Solution**:
1. **Check server connectivity**:
   ```bash
   curl -I https://api.github.com/repos/jdram82/nexus/releases/latest
   ```
   Should return `200 OK`

2. **Check disk space**:
   ```bash
   df -h
   ```
   Need at least 50MB free

3. **Check write permissions**:
   ```bash
   ls -la /var/www/html/wp-content/themes/
   ```
   Should show `www-data` as owner

4. **Try manual update** if automatic continues to fail

### Installation Fails

**Symptom**: "Could not install theme" or "Installation failed"

**Solutions**:

1. **Check PHP memory limit**:
   ```bash
   php -i | grep memory_limit
   ```
   Should be at least `256M`

   Increase in `wp-config.php`:
   ```php
   define('WP_MEMORY_LIMIT', '256M');
   ```

2. **Check file permissions**:
   ```bash
   # Fix ownership
   chown -R www-data:www-data /var/www/html/wp-content/themes/
   
   # Fix permissions
   chmod -R 755 /var/www/html/wp-content/themes/
   ```

3. **Check PHP errors**:
   - Enable debug mode in `wp-config.php`:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```
   - Check `/wp-content/debug.log`

### Theme Breaks After Update

**Symptom**: White screen, errors, or site doesn't load

**Immediate Solution**:
```bash
# Roll back to previous version
cd /var/www/html/wp-content/themes/
rm -rf nexus-theme
mv nexus-theme-backup nexus-theme

# Or restore from backup
# If you have a database backup:
wp db import backup.sql
```

**Permanent Solution**:
1. Restore from backup (see above)
2. Report issue: https://github.com/jdram82/nexus/issues
3. Include error messages from debug.log
4. Wait for patch release
5. Try update again when patch is available

### Settings Lost After Update

**Symptom**: Customizer settings, widgets, or theme options reset

**Prevention**: This shouldn't happen! Settings are stored in database, not theme files.

**Solution**:
1. Check if settings exist in database:
   ```bash
   wp option get nexus_options
   wp option get theme_mods_nexus-theme
   ```

2. If settings exist but not showing:
   - Go to Appearance → Customize
   - Make any small change
   - Click "Publish"
   - This may re-trigger settings

3. If settings truly lost:
   - Restore from database backup
   - Or reconfigure manually

4. Report issue: This is a bug that needs fixing

### Child Theme Compatibility

**Symptom**: Child theme doesn't work after parent theme update

**Solution**:
1. **Check child theme's `functions.php`**:
   ```php
   // Ensure you're using get_template_directory()
   // NOT get_stylesheet_directory() for parent theme files
   ```

2. **Check template overrides**:
   - If child theme overrides parent templates
   - Update child theme templates to match new parent structure

3. **Check version compatibility**:
   - Some child themes require specific parent versions
   - Check child theme documentation

---

## FAQ

### How often should I update Nexus?

**Security updates**: Immediately when released  
**Bug fixes**: Within 1 week  
**Feature updates**: At your convenience (test on staging first)

### Will updates break my site?

Not if you follow best practices:
- ✅ Always backup before updating
- ✅ Test on staging site first
- ✅ Read release notes for breaking changes
- ✅ Use child theme for customizations

### Do I lose my settings when updating?

**No!** Settings are stored in the database, not theme files. Updates only replace theme files.

Settings preserved:
- Customizer options
- Widget configurations
- Menu assignments
- Theme options
- Pro tier license keys
- Payment gateway settings

### Can I skip versions?

Yes! You can go from 1.5.0 directly to 1.8.0. No need to install intermediate versions.

### How do I know if an update is available?

You'll see notifications in:
1. WordPress Dashboard
2. Appearance → Themes page
3. Appearance → Updates page

Or manually check: https://github.com/jdram82/nexus/releases

### Can I roll back to a previous version?

Yes, several ways:

**Option 1**: Restore from backup (recommended)

**Option 2**: Download previous version from GitHub
- Visit: https://github.com/jdram82/nexus/releases
- Find previous version
- Download and install manually

**Option 3**: Git checkout (if theme installed via Git)
```bash
cd wp-content/themes/nexus-theme/
git checkout v1.5.0
```

### Should I use automatic or manual updates?

**Automatic**: For most users, easiest and safest

**Manual**: Use if:
- You have custom modifications in theme files (use child theme instead!)
- Your server has limited resources
- You need to test extensively before updating
- You prefer full control

### What if automatic updates are disabled on my server?

Some hosts disable automatic updates. Solutions:

1. **Ask your host** to enable theme updates
2. **Use manual updates** instead
3. **Use WP-CLI** to update
4. **Change hosting** to one that allows updates

### How do I disable automatic update checks?

Not recommended, but possible:

```php
// Add to child theme's functions.php or custom plugin
add_filter( 'pre_set_site_transient_update_themes', function( $transient ) {
    if ( isset( $transient->response['nexus-theme'] ) ) {
        unset( $transient->response['nexus-theme'] );
    }
    return $transient;
}, 99 );
```

### Where are update logs stored?

Updates are logged in:
- WordPress debug log: `/wp-content/debug.log` (if WP_DEBUG enabled)
- Hosting panel logs
- WP-CLI output (if updating via WP-CLI)

### Can I update via WP-CLI?

Yes! The automatic updater works with WP-CLI:

```bash
# Check for updates
wp theme list

# Update Nexus theme
wp theme update nexus-theme

# Update all themes
wp theme update --all
```

---

## Support

### Need Help?

- **Documentation**: https://github.com/jdram82/nexus/wiki
- **GitHub Issues**: https://github.com/jdram82/nexus/issues
- **Changelog**: See [CHANGELOG.md](CHANGELOG.md)
- **Release Notes**: https://github.com/jdram82/nexus/releases

### Before Asking for Help

Please provide:
1. Current theme version
2. WordPress version
3. PHP version
4. Hosting provider
5. Error messages (from debug.log)
6. Steps to reproduce issue
7. What you've already tried

### Report a Bug

Create an issue at: https://github.com/jdram82/nexus/issues

Include:
- Clear description of problem
- Steps to reproduce
- Expected behavior vs actual behavior
- Screenshots if relevant
- Error logs
- Environment details (WP version, PHP version, etc.)

---

## Best Practices

### General Update Best Practices

1. ✅ **Always backup** before updating
2. ✅ **Test on staging** before production
3. ✅ **Read release notes** before updating
4. ✅ **Update during low traffic** periods
5. ✅ **Enable maintenance mode** during update
6. ✅ **Clear all caches** after update
7. ✅ **Test thoroughly** after update
8. ✅ **Keep backups** for 30 days

### Child Theme Best Practices

If you need to customize Nexus:

1. **Always use a child theme** for customizations
2. **Never edit parent theme files** directly
3. **Override templates** in child theme
4. **Use hooks and filters** when possible
5. **Document your customizations**

Example child theme structure:
```
nexus-child/
├── style.css (required)
├── functions.php (recommended)
├── screenshot.png (optional)
└── template-parts/ (override parent templates)
```

### Security Best Practices

1. **Keep WordPress updated** (6.0+)
2. **Keep PHP updated** (7.4+)
3. **Use strong passwords**
4. **Enable two-factor authentication**
5. **Regular security scans**
6. **Keep backups off-site**

---

**Last Updated**: January 2024  
**Nexus Version**: 1.6.0  
**Maintained by**: JDRAM
