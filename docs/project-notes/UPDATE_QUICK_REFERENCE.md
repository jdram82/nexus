# Nexus Theme Update System - Quick Reference

## 🚀 Automatic Updates (NEW in v1.6.0)

### What's New?
- ✅ **GitHub-based automatic updates** - No more manual reinstallation!
- ✅ **One-click updates** from WordPress admin
- ✅ **Update notifications** when new versions available
- ✅ **Settings preserved** during updates
- ✅ **Version checking** every 12 hours

---

## ⚡ Quick Update Methods

### Method 1: One-Click Update (Easiest)
```
1. Go to WordPress Dashboard
2. See "Nexus Theme Update Available!" notification
3. Click "Update Now"
4. Done! ✅
```

### Method 2: Appearance → Themes
```
1. Go to Appearance → Themes
2. Find Nexus theme card
3. Click "Update Now" button
4. Wait for completion ✅
```

### Method 3: Appearance → Updates
```
1. Go to Appearance → Updates (Nexus page)
2. View current vs latest version
3. Click "Update Now"
4. Or click "Check for Updates Now" to force check ✅
```

---

## 🔍 How It Works

```
GitHub Repository (jdram82/nexus)
         ↓
    New Release Created (e.g., v1.7.0)
         ↓
Nexus Checks GitHub API Every 12 Hours
         ↓
    New Version Detected?
         ↓
   Show Update Notification
         ↓
    User Clicks "Update Now"
         ↓
  Download from GitHub
         ↓
  Replace Theme Files
         ↓
  Preserve All Settings
         ↓
   Update Complete! ✅
```

---

## 📋 Before You Update

### Essential Checklist
- [ ] **Backup** everything (database + files)
- [ ] **Read** release notes on GitHub
- [ ] **Check** compatibility (WP 6.0+, PHP 7.4+)
- [ ] **Test** on staging site (recommended)
- [ ] **Disable** caching plugins temporarily

### One-Line Backup (WP-CLI)
```bash
wp db export backup-$(date +%Y%m%d).sql
```

---

## 🎯 What Gets Updated

### ✅ Updated (Theme Files)
- PHP files
- CSS/JS files
- Template files
- New features
- Bug fixes

### ❌ NOT Updated (Your Data - Preserved!)
- Theme settings
- Customizer options
- Widget configurations
- Menu assignments
- Pro license keys
- Payment settings
- Cloud credentials
- Your content

---

## 🛠️ Manual Update (Backup Method)

If automatic fails:

### Via WordPress Admin
```
1. Download: https://github.com/jdram82/nexus/releases/latest
2. Activate a different theme temporarily
3. Delete old Nexus theme
4. Upload new theme zip
5. Activate Nexus theme
```

### Via WP-CLI (Fastest)
```bash
wp theme install https://github.com/jdram82/nexus/archive/refs/tags/v1.6.0.zip --activate --force
```

### Via SSH
```bash
cd /var/www/html/wp-content/themes/
rm -rf nexus-theme
wget https://github.com/jdram82/nexus/archive/refs/tags/v1.6.0.zip
unzip v1.6.0.zip
mv nexus-1.6.0 nexus-theme
chown -R www-data:www-data nexus-theme
```

---

## 🔧 Troubleshooting

### Update Check Fails
```bash
# Clear update cache
wp transient delete nexus_theme_update_check
```

### Download Fails
**Check connectivity:**
```bash
curl -I https://api.github.com/repos/jdram82/nexus/releases/latest
```
Expected: `200 OK`

### Installation Fails
**Check disk space:**
```bash
df -h
```
Need: 50MB+ free

**Check permissions:**
```bash
ls -la /var/www/html/wp-content/themes/
```
Expected: `www-data` owner

### Site Breaks After Update
**Roll back immediately:**
```bash
cd /var/www/html/wp-content/themes/
rm -rf nexus-theme
mv nexus-theme-backup nexus-theme
```

---

## 📊 Version Information

### Current Version System
- **Major.Minor.Patch** (Semantic Versioning)
- Example: `1.6.0`
  - `1` = Major version
  - `6` = Minor version (new features)
  - `0` = Patch version (bug fixes)

### Where Version is Stored
1. `style.css` - Theme header (WordPress reads this)
2. `functions.php` - NEXUS_VERSION constant
3. `pro/class-nexus-pro.php` - NEXUS_PRO_VERSION constant

### Check Your Version
**WordPress Admin:**
```
Appearance → Updates
Shows: "Current Version: 1.6.0"
```

**WP-CLI:**
```bash
wp theme list
```

**Code:**
```php
echo NEXUS_VERSION; // 1.6.0
```

---

## 🔐 Security

### Update Security
- ✅ Downloads from official GitHub repository only
- ✅ Verifies GitHub API response
- ✅ Uses WordPress core update system
- ✅ File integrity maintained
- ✅ No external dependencies

### Best Practices
- Always update to latest version for security patches
- Security updates released immediately when needed
- Subscribe to GitHub releases for notifications

---

## ⏱️ Update Frequency

| Type | Frequency | Action |
|------|-----------|--------|
| **Security patches** | Immediate | Update ASAP |
| **Bug fixes** | Weekly | Update within 7 days |
| **Feature updates** | Monthly | Test on staging first |
| **Major versions** | Quarterly | Plan migration |

---

## 🎓 Advanced Features

### Force Update Check
```php
// Add to functions.php temporarily
delete_transient('nexus_theme_update_check');
// Reload admin page, then remove code
```

### Disable Update Checks (Not Recommended)
```php
// Add to child theme functions.php
add_filter('pre_set_site_transient_update_themes', function($transient) {
    if (isset($transient->response['nexus-theme'])) {
        unset($transient->response['nexus-theme']);
    }
    return $transient;
}, 99);
```

### Get Update Info Programmatically
```php
$updater = Nexus_Theme_Updater::instance();
$info = $updater->get_update_info();

echo $info['current_version'];  // 1.6.0
echo $info['latest_version'];   // 1.7.0
echo $info['update_available']; // true/false
```

---

## 📞 Support

### Need Help?
- **Docs**: [THEME_UPDATE_GUIDE.md](THEME_UPDATE_GUIDE.md) (comprehensive guide)
- **Changelog**: [CHANGELOG.md](CHANGELOG.md)
- **GitHub Issues**: https://github.com/jdram82/nexus/issues
- **Releases**: https://github.com/jdram82/nexus/releases

### Before Reporting Issues
Include:
- Current version: `wp theme list`
- WordPress version: `wp core version`
- PHP version: `php -v`
- Error messages from debug.log
- Steps to reproduce

---

## 📝 Notes

### Child Themes
Using a child theme? Perfect! Updates won't affect your customizations.

### Multisite
Update system works on WordPress multisite. Network admin can update theme network-wide.

### Update Cache
- Updates cached for 12 hours
- Manual check clears cache
- Cached in transient: `nexus_theme_update_check`

### GitHub Rate Limits
- 60 requests/hour for unauthenticated
- Rarely hit (only checks every 12 hours)
- If hit, wait 1 hour or use manual update

---

## ✅ After Update Checklist

- [ ] Verify version number updated
- [ ] Test homepage loads
- [ ] Check admin dashboard works
- [ ] Test navigation menus
- [ ] Verify widgets display
- [ ] Test forms (if any)
- [ ] Check WooCommerce (if using)
- [ ] Test Pro features (if applicable)
- [ ] Clear all caches
- [ ] Test mobile responsive
- [ ] Run PageSpeed Insights

---

**Update System Version**: 1.0  
**Release Date**: January 2024  
**Maintained by**: JDRAM  
**License**: GPL v2 or later
