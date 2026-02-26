# Nexus Automatic Update System - Complete Guide

**Version**: 1.8.1  
**Author**: Jdsan Digitel  
**System**: GitHub-based Automatic Updates

---

## 🎯 Overview

Nexus uses a custom GitHub-based automatic update system that provides:
- ✅ One-click updates from WordPress admin
- ✅ Automatic update checking every 12 hours
- ✅ No need for WordPress.org theme directory
- ✅ Complete control over releases
- ✅ Changelog integration
- ✅ Settings preservation during updates

---

## 🏗️ System Architecture

```
┌─────────────────┐
│  GitHub Release │ ← Developer creates release with version tag
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   GitHub API    │ ← Returns latest release info (JSON)
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Nexus Updater   │ ← Checks every 12 hours via cron
│ (WordPress)     │ ← Compares versions
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Update Transient│ ← Cached for 12 hours
│ (WordPress)     │ ← Shows "Update Available" notice
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ User Clicks     │ ← One-click update
│ "Update Now"    │ ← Downloads ZIP from GitHub
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ WordPress       │ ← Extracts & renames folder
│ Upgrader        │ ← Replaces old theme
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Cache Cleared   │ ← Auto-clears update cache
│ Theme Refreshed │ ← New version active!
└─────────────────┘
```

---

## 📁 Key Files

### **1. class-nexus-theme-updater.php** (518 lines)
**Location**: `inc/class-nexus-theme-updater.php`

**Purpose**: Main updater class that handles all update logic

**Key Methods**:
- `check_for_update()` - Checks GitHub API for new releases
- `get_latest_release()` - Fetches release data from GitHub
- `fix_theme_folder_name()` - Renames extracted ZIP folder
- `clear_cache_after_update()` - Clears caches post-update
- `show_update_notice()` - Displays admin notices
- `render_update_page()` - Update settings page

### **2. functions.php**
**Location**: Root directory

**Critical Line**:
```php
Nexus_Theme_Updater::instance(); // Must be in nexus_init()
```

**Version Constant**:
```php
define( 'NEXUS_VERSION', '1.8.1' );
```

### **3. style.css**
**Location**: Root directory

**Theme Header**:
```css
/*
Theme Name: Nexus
Version: 1.8.1
*/
```

---

## 🔧 How It Works

### **Step 1: Version Checking (Every 12 Hours)**

```php
// WordPress hook: pre_set_site_transient_update_themes
public function check_for_update( $transient ) {
    // 1. Check transient cache
    $update_cache = get_transient( 'nexus_theme_update_check' );
    
    // 2. If no cache, fetch from GitHub
    if ( false === $update_cache ) {
        $release = $this->get_latest_release();
        
        // 3. Cache for 12 hours
        set_transient( 'nexus_theme_update_check', $update_cache, 12 * HOUR_IN_SECONDS );
    }
    
    // 4. Compare versions
    if ( version_compare( $current, $latest, '<' ) ) {
        // Inject update info into WordPress
        $transient->response[ 'nexus-theme' ] = $update_data;
    }
    
    return $transient;
}
```

### **Step 2: GitHub API Request**

```php
// GET https://api.github.com/repos/jdram82/nexus/releases/latest
$headers = array(
    'Accept' => 'application/vnd.github.v3+json',
);

// Add token if available (bypasses rate limits)
if ( defined( 'NEXUS_GITHUB_TOKEN' ) ) {
    $headers['Authorization'] = 'token ' . NEXUS_GITHUB_TOKEN;
}

$response = wp_remote_get( $github_api_url, array(
    'timeout' => 15,
    'headers' => $headers,
) );
```

**API Response**:
```json
{
  "tag_name": "v1.8.1",
  "name": "Nexus v1.8.1",
  "body": "Release notes...",
  "zipball_url": "https://api.github.com/repos/jdram82/nexus/zipball/v1.8.1",
  "published_at": "2025-12-27T..."
}
```

### **Step 3: User Clicks "Update Now"**

WordPress core handles:
1. Downloads `zipball_url`
2. Extracts to temp directory
3. Calls `upgrader_source_selection` hook
4. Nexus renames folder from `nexus-abc1234` → `nexus-theme`
5. Moves to `/wp-content/themes/nexus-theme`
6. Deletes old theme
7. Calls `upgrader_process_complete` hook
8. Nexus clears all caches

### **Step 4: Folder Renaming**

**Problem**: GitHub zipballs create folders like:
- `nexus-1.8.1`
- `jdram82-nexus-abc1234`

**Solution**:
```php
public function fix_theme_folder_name( $source, $remote_source, $upgrader ) {
    // Get extracted folder name
    $source_name = basename( $source ); // "jdram82-nexus-abc1234"
    
    // Create correct path
    $new_source = dirname( $source ) . '/nexus-theme';
    
    // Rename folder
    $wp_filesystem->move( $source, $new_source, true );
    
    return $new_source;
}
```

### **Step 5: Cache Clearing**

```php
public function clear_cache_after_update( $upgrader_object, $options ) {
    // Check if our theme was updated
    if ( in_array( 'nexus-theme', $options['themes'] ) ) {
        // Clear update cache
        delete_transient( 'nexus_theme_update_check' );
        
        // Clear WordPress theme cache
        delete_site_transient( 'update_themes' );
        wp_clean_themes_cache();
        
        // Force refresh
        wp_update_themes();
    }
}
```

---

## 🔑 GitHub Token Configuration

### **Why Needed?**
GitHub API has rate limits:
- **Without token**: 60 requests/hour
- **With token**: 5,000 requests/hour

For production sites with multiple installations, token is required.

### **How to Generate**

1. Go to: https://github.com/settings/tokens/new
2. Token name: "Nexus Theme Updates"
3. Expiration: No expiration (or 1 year+)
4. Scopes: Select **public_repo** only
5. Click "Generate token"
6. Copy token (starts with `ghp_`)

### **Configuration Methods**

**Method 1: wp-config.php (Recommended)**
```php
// Add before /* That's all, stop editing! */
define( 'NEXUS_GITHUB_TOKEN', 'ghp_YOUR_TOKEN_HERE' );
```

**Method 2: Plugin**
Create `/wp-content/plugins/nexus-github-token.php`:
```php
<?php
/**
 * Plugin Name: Nexus GitHub Token
 */
if ( ! defined( 'NEXUS_GITHUB_TOKEN' ) ) {
    define( 'NEXUS_GITHUB_TOKEN', 'ghp_YOUR_TOKEN_HERE' );
}
```

**Method 3: Environment Variable**
```bash
# In .env file
NEXUS_GITHUB_TOKEN=ghp_YOUR_TOKEN_HERE
```

Then in wp-config.php:
```php
if ( getenv('NEXUS_GITHUB_TOKEN') ) {
    define( 'NEXUS_GITHUB_TOKEN', getenv('NEXUS_GITHUB_TOKEN') );
}
```

---

## 📊 Update Workflow for Developers

### **1. Make Code Changes**
```bash
# Make changes to theme files
git add -A
git commit -m "Add new feature"
```

### **2. Update Version Numbers**

**style.css**:
```css
Version: 1.9.0
```

**functions.php**:
```php
define( 'NEXUS_VERSION', '1.9.0' );
```

### **3. Create Git Tag**
```bash
git tag -a v1.9.0 -m "Release v1.9.0

- New feature added
- Bug fixes
- Performance improvements"

git push origin v1.9.0
```

### **4. Create GitHub Release**

**Via GitHub UI**:
1. Go to: https://github.com/jdram82/nexus/releases/new
2. Tag: `v1.9.0` (select existing or create new)
3. Release title: `Nexus v1.9.0 - Feature Name`
4. Description:
   ```markdown
   ## What's New
   - New feature description
   - Bug fix details
   
   ## Improvements
   - Performance enhancement
   - UI updates
   
   ## Full Changelog
   https://github.com/jdram82/nexus/compare/v1.8.1...v1.9.0
   ```
5. Click **"Publish release"**

**Via GitHub CLI**:
```bash
gh release create v1.9.0 \
  --title "Nexus v1.9.0 - Feature Name" \
  --notes "Release notes here"
```

### **5. Users See Update Automatically**
- Within 12 hours (or on next cache check)
- Or immediately if they click "Check for Updates Now"

---

## 🧪 Testing Updates Locally

### **1. Set Up Local Test Environment**
```bash
# Create test WordPress site
cd /var/www/
wp core download
wp config create --dbname=nexus_test --dbuser=root --dbpass=password
wp core install --url=nexus.test --title="Test" --admin_user=admin
```

### **2. Install Nexus**
```bash
cd wp-content/themes/
git clone https://github.com/jdram82/nexus.git nexus-theme
wp theme activate nexus-theme
```

### **3. Test Update Process**
```bash
# Create new version
cd nexus-theme
# Make changes
git commit -am "Test update"
git tag v1.9.0-test
git push origin v1.9.0-test

# Create release on GitHub
# Then in WordPress:
# - Go to Dashboard → Updates
# - Click "Check for Updates"
# - Verify update shows
# - Click "Update Now"
# - Verify successful completion
```

---

## 🐛 Troubleshooting

### **Problem: Updates Not Showing**

**Diagnostic Steps**:
1. Go to **Appearance → Update Diagnostic**
2. Check each section:
   - ✅ Theme Installation
   - ✅ GitHub Token
   - ✅ GitHub API Test
   - ✅ Update Available

**Common Fixes**:
```php
// 1. Clear cache manually
delete_transient( 'nexus_theme_update_check' );
delete_site_transient( 'update_themes' );
wp_clean_themes_cache();

// 2. Force update check
wp_update_themes();

// 3. Verify theme slug
$theme = wp_get_theme();
echo $theme->get_stylesheet(); // Should be "nexus-theme"
```

### **Problem: Update Fails/Loops**

**Symptoms**: "Update Now" keeps appearing after update

**Solution** (Fixed in v1.8.1):
- Automatic cache clearing added
- Improved folder renaming logic
- Better theme detection

**Manual Fix**:
```bash
# Via FTP, ensure folder is named correctly
/wp-content/themes/nexus-theme/  # ✅ Correct
/wp-content/themes/nexus-1.8.1/  # ❌ Wrong
```

### **Problem: GitHub Rate Limit**

**Error**: "API rate limit exceeded"

**Solution**:
1. Add GitHub token to wp-config.php
2. Or wait 1 hour for rate limit reset
3. Token increases limit from 60/hr to 5000/hr

### **Problem: Theme Broken After Update**

**Recovery Steps**:
1. Activate default theme (via database if needed):
   ```sql
   UPDATE wp_options 
   SET option_value = 'twentytwentyfive' 
   WHERE option_name = 'template' OR option_name = 'stylesheet';
   ```

2. Delete broken theme via FTP

3. Download fresh ZIP from GitHub:
   ```
   https://github.com/jdram82/nexus/archive/refs/tags/v1.8.1.zip
   ```

4. Extract and upload via FTP

5. Reactivate in WordPress admin

---

## 📈 Monitoring & Analytics

### **Update Metrics**

Track these in Google Analytics or Matomo:
- Update check frequency
- Version distribution across users
- Update completion rate
- Time to update after release

### **Error Logging**

Add to wp-config.php for debugging:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

Check `/wp-content/debug.log` for update errors.

### **User Notifications**

Send email to admin when update available:
```php
add_action( 'upgrader_process_complete', function( $upgrader, $options ) {
    if ( $options['type'] === 'theme' && in_array( 'nexus-theme', $options['themes'] ) ) {
        wp_mail(
            get_option( 'admin_email' ),
            'Nexus Theme Updated',
            'Theme successfully updated to version ' . NEXUS_VERSION
        );
    }
}, 10, 2 );
```

---

## 🔐 Security Considerations

### **1. Code Signing**
GitHub releases include SHA checksums for verification:
```bash
# Generate checksum
sha256sum nexus-theme-1.8.1.zip

# Verify on download
echo "checksum_here nexus-theme-1.8.1.zip" | sha256sum -c
```

### **2. HTTPS Only**
All API requests use HTTPS:
```php
$github_api_url = "https://api.github.com/..."; // Always HTTPS
```

### **3. Token Security**
- Never commit tokens to repository
- Use environment variables or wp-config.php
- Rotate tokens annually
- Use minimal scopes (public_repo only)

### **4. Update Integrity**
WordPress verifies:
- File permissions during extraction
- Folder structure matches expectations
- No malicious code injection

---

## 📚 Additional Resources

### **WordPress Hooks Used**
- `pre_set_site_transient_update_themes` - Inject update info
- `upgrader_source_selection` - Rename extracted folder
- `upgrader_process_complete` - Post-update actions
- `admin_notices` - Display update notifications
- `admin_menu` - Add settings pages

### **WordPress Functions Used**
- `wp_remote_get()` - HTTP requests to GitHub API
- `get_transient()` / `set_transient()` - Caching
- `wp_get_theme()` - Get theme object
- `version_compare()` - Compare version strings
- `delete_site_transient()` - Clear WordPress caches
- `wp_clean_themes_cache()` - Refresh theme data

### **GitHub API Documentation**
- Releases API: https://docs.github.com/en/rest/releases
- Rate Limiting: https://docs.github.com/en/rest/overview/resources-in-the-rest-api#rate-limiting
- Authentication: https://docs.github.com/en/rest/overview/other-authentication-methods

---

## 🎓 Best Practices

1. **Version Numbering**: Use Semantic Versioning (MAJOR.MINOR.PATCH)
2. **Release Notes**: Always include clear changelog
3. **Testing**: Test updates on staging before releasing
4. **Backup**: Users should backup before updating
5. **Compatibility**: Test with latest WordPress version
6. **Rollback**: Keep previous version available for rollback
7. **Communication**: Notify users of breaking changes

---

*Last updated: December 27, 2025*  
*Nexus Theme by Jdsan Digitel*
