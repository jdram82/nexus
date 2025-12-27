# Nexus Theme Update System - Configuration & Troubleshooting

## 🚨 GitHub API Rate Limit Issue

If WordPress doesn't show Nexus theme updates, it's likely due to GitHub API rate limits.

### The Problem

GitHub limits unauthenticated API requests to:
- **60 requests per hour** per IP address

When WordPress checks for updates, it uses your server's IP. If multiple sites or plugins check GitHub, you hit the limit quickly.

### The Solution: Add GitHub Token

Adding a GitHub personal access token increases your rate limit to **5,000 requests per hour**.

---

## 🔧 Setup GitHub Token (5 Minutes)

### Step 1: Create GitHub Token

1. Go to: https://github.com/settings/tokens/new
2. **Description**: `Nexus Theme Updates`
3. **Expiration**: `No expiration` (or 1 year)
4. **Scopes**: Check **ONLY**:
   - ✅ `public_repo` (Access public repositories)
5. Click **"Generate token"**
6. **Copy the token** (you won't see it again!)
   - Example: `ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`

### Step 2: Add Token to WordPress

**Option A: Via wp-config.php (Recommended)**

1. Open `wp-config.php` in your WordPress root
2. Add this line **before** `/* That's all, stop editing! */`:

```php
define( 'NEXUS_GITHUB_TOKEN', 'ghp_your_token_here' );
```

3. Save the file

**Option B: Via .env file (if your host supports it)**

```env
NEXUS_GITHUB_TOKEN=ghp_your_token_here
```

**Option C: Via Plugin (Most Secure)**

Create a small plugin to store the token:

```php
<?php
/**
 * Plugin Name: Nexus GitHub Token
 * Description: Stores GitHub token for Nexus theme updates
 */

define( 'NEXUS_GITHUB_TOKEN', 'ghp_your_token_here' );
```

Save as `wp-content/plugins/nexus-github-token.php` and activate.

### Step 3: Verify It Works

1. Go to **WordPress Admin → Appearance → Updates**
2. Click **"Check for Updates Now"**
3. Should now show update available! ✅

---

## 🔍 Troubleshooting

### Updates Still Not Showing?

**1. Clear Update Cache**

```bash
# Via WP-CLI
wp transient delete nexus_theme_update_check

# Or via WordPress admin
# Go to Appearance → Updates → Click "Clear Update Cache"
```

**2. Check GitHub Token**

Add this to a temporary PHP file and access via browser:

```php
<?php
// test-github-token.php
require_once 'wp-load.php';

if ( defined( 'NEXUS_GITHUB_TOKEN' ) ) {
    echo "✅ Token is defined\n";
    
    // Test GitHub API
    $response = wp_remote_get( 'https://api.github.com/rate_limit', array(
        'headers' => array(
            'Authorization' => 'token ' . NEXUS_GITHUB_TOKEN,
        ),
    ) );
    
    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    echo "Rate limit: " . $body['resources']['core']['remaining'] . " / " . $body['resources']['core']['limit'] . "\n";
} else {
    echo "❌ NEXUS_GITHUB_TOKEN not defined\n";
}
```

**3. Check GitHub Release Exists**

Visit: https://github.com/jdram82/nexus/releases/latest

Should show v1.6.0 release. If not, create it:

```bash
# Create release on GitHub
gh release create v1.6.0 \
  --title "v1.6.0 - Automatic GitHub Updates" \
  --notes "See CHANGELOG.md for details"
```

**4. Check Theme Folder Name**

```bash
# Check actual folder name
ls -la wp-content/themes/

# Should be "nexus-theme"
```

If different, update in `inc/class-nexus-theme-updater.php`:

```php
private $theme_slug = 'your-actual-folder-name';
```

**5. Enable Debug Mode**

Add to `wp-config.php`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

Check `wp-content/debug.log` for errors.

---

## 🎯 Manual Update (If Automatic Fails)

### Method 1: WordPress Admin

1. Download: https://github.com/jdram82/nexus/releases/latest
2. Extract ZIP file
3. Go to **Appearance → Themes**
4. Activate a different theme temporarily
5. Delete old Nexus theme
6. Click **Add New → Upload Theme**
7. Upload the extracted ZIP
8. Activate Nexus theme

### Method 2: WP-CLI (Fastest)

```bash
# Download and install from GitHub
wp theme install https://github.com/jdram82/nexus/archive/refs/tags/v1.6.0.zip --activate --force

# Verify version
wp theme list | grep nexus
```

### Method 3: FTP/SSH

```bash
# SSH into your server
cd /var/www/html/wp-content/themes/

# Backup old theme
mv nexus-theme nexus-theme-backup

# Download latest
wget https://github.com/jdram82/nexus/archive/refs/tags/v1.6.0.zip
unzip v1.6.0.zip
mv nexus-1.6.0 nexus-theme

# Set permissions
chown -R www-data:www-data nexus-theme
chmod -R 755 nexus-theme
```

---

## 📊 Verify Update System Status

### Check Current Configuration

Add this code to your theme's functions.php temporarily:

```php
add_action( 'admin_notices', function() {
    if ( ! current_user_can( 'update_themes' ) ) return;
    
    $updater = Nexus_Theme_Updater::instance();
    $info = $updater->get_update_info();
    
    echo '<div class="notice notice-info">';
    echo '<h3>Nexus Update System Status</h3>';
    echo '<p><strong>Current Version:</strong> ' . $info['current_version'] . '</p>';
    echo '<p><strong>Latest Version:</strong> ' . $info['latest_version'] . '</p>';
    echo '<p><strong>Update Available:</strong> ' . ( $info['update_available'] ? 'Yes ✅' : 'No' ) . '</p>';
    echo '<p><strong>GitHub Token:</strong> ' . ( defined('NEXUS_GITHUB_TOKEN') ? 'Configured ✅' : 'Not configured ❌' ) . '</p>';
    echo '</div>';
} );
```

Visit WordPress admin to see status.

---

## 🔐 Security Best Practices

### Storing GitHub Token Securely

**✅ DO:**
- Store in `wp-config.php` above `ABSPATH`
- Use environment variables (`.env` file)
- Use a secrets management plugin
- Set token expiration (e.g., 1 year) and renew regularly

**❌ DON'T:**
- Hardcode in theme files
- Commit to Git repositories
- Share in screenshots or documentation
- Use root-level tokens (use minimal scope)

### Token Scope

The token only needs **public_repo** access:
- ✅ Can read public repositories
- ✅ Can check for releases
- ❌ Cannot modify your code
- ❌ Cannot access private repos
- ❌ Cannot perform other GitHub actions

### Revoking Token

If compromised:
1. Go to: https://github.com/settings/tokens
2. Find "Nexus Theme Updates" token
3. Click **"Revoke"**
4. Generate a new token
5. Update `wp-config.php`

---

## 📈 Rate Limit Status

### Check Your Current Rate Limit

```bash
# Without token (60/hour)
curl https://api.github.com/rate_limit

# With token (5000/hour)
curl -H "Authorization: token YOUR_TOKEN" https://api.github.com/rate_limit
```

### Why You Might Hit Limits

**Without Token:**
- Shared hosting (multiple sites on same IP)
- Multiple WordPress sites
- Many plugins checking GitHub
- CI/CD systems
- Development environments

**With Token:**
- Should never hit 5,000/hour limit
- Each update check = 1 request
- Check every 12 hours = 2 requests/day

---

## 🎓 Advanced Configuration

### Change Update Check Frequency

Default is 12 hours. To change:

```php
// In your child theme's functions.php
add_filter( 'nexus_update_check_frequency', function() {
    return 24 * HOUR_IN_SECONDS; // 24 hours
} );
```

### Disable Automatic Updates

```php
// In child theme's functions.php
remove_action( 'pre_set_site_transient_update_themes', array( Nexus_Theme_Updater::instance(), 'check_for_update' ) );
```

### Custom GitHub Repository

If you fork Nexus:

```php
// In child theme's functions.php
add_filter( 'nexus_github_repo', function( $repo ) {
    return 'your-username/your-repo';
} );
```

---

## 📞 Still Having Issues?

### Before Asking for Help

Please provide:

1. **WordPress version**: `wp core version`
2. **PHP version**: `php -v`
3. **Theme version**: Check Appearance → Themes
4. **GitHub token status**: Configured or not?
5. **Error messages**: Check `wp-content/debug.log`
6. **Rate limit status**: 
   ```bash
   curl https://api.github.com/rate_limit
   ```

### Get Support

- **GitHub Issues**: https://github.com/jdram82/nexus/issues
- **Documentation**: [THEME_UPDATE_GUIDE.md](THEME_UPDATE_GUIDE.md)
- **Changelog**: [CHANGELOG.md](../CHANGELOG.md)

---

## ✅ Checklist for Working Updates

- [ ] GitHub release v1.6.0 published
- [ ] GitHub token created and configured
- [ ] Token has `public_repo` scope
- [ ] Token added to `wp-config.php`
- [ ] Update cache cleared
- [ ] WordPress can access GitHub API
- [ ] Theme folder named `nexus-theme`
- [ ] WordPress 6.0+ and PHP 7.4+
- [ ] Update notification appears in admin

---

**Last Updated**: January 2024  
**Applies to**: Nexus v1.6.0+  
**GitHub**: https://github.com/jdram82/nexus
