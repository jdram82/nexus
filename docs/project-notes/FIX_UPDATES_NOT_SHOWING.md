# 🚨 URGENT: Fix WordPress Not Detecting Updates

## The Problem

WordPress doesn't show Nexus theme updates because of **GitHub API rate limits**.

GitHub limits unauthenticated requests to **60 per hour**. Your server hit this limit.

## ✅ Quick Fix (5 Minutes)

### Step 1: Create GitHub Token

1. Go to: https://github.com/settings/tokens/new

2. Fill in:
   - **Description**: `Nexus Theme Updates`
   - **Expiration**: `No expiration`
   - **Scopes**: Check ONLY `public_repo`

3. Click **"Generate token"**

4. **COPY THE TOKEN** (looks like: `ghp_xxxxxxxxxxxxxxxxxxxx`)

### Step 2: Add to WordPress

Open your **wp-config.php** file and add this line **BEFORE** the line that says `/* That's all, stop editing! */`:

```php
define( 'NEXUS_GITHUB_TOKEN', 'ghp_paste_your_token_here' );
```

**Example:**
```php
define( 'NEXUS_GITHUB_TOKEN', 'ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' );

/* That's all, stop editing! Happy publishing. */
```

Save the file.

### Step 3: Test It

1. Go to **WordPress Admin → Appearance → Themes**
2. Click **"Check for updates now"** (or wait a minute)
3. You should now see **"Update available for Nexus"** ✅

---

## 🔥 If Still Not Working

### Option 1: Clear Cache

```bash
# Via WP-CLI
wp transient delete nexus_theme_update_check
```

Or in WordPress:
- Go to **Appearance → Updates**
- Click **"Clear Update Cache"**

### Option 2: Check GitHub Release

Make sure the release exists: https://github.com/jdram82/nexus/releases

If no v1.6.0 release, create it:

1. Go to: https://github.com/jdram82/nexus/releases/new
2. **Tag**: `v1.6.0`
3. **Title**: `v1.6.0 - Automatic GitHub Updates`
4. **Description**:
   ```
   ## Features
   - Automatic GitHub-based updates
   - One-click updates from WordPress
   - Settings preservation
   
   See CHANGELOG.md for details.
   ```
5. Click **"Publish release"**

### Option 3: Manual Update (Temporary Workaround)

If you need to update **right now** while fixing the automatic system:

**Via WordPress Admin:**
1. Download: https://github.com/jdram82/nexus/archive/refs/heads/main.zip
2. Go to **Appearance → Themes**
3. Activate a different theme (Twenty Twenty-Four)
4. Delete Nexus theme
5. Click **Add New → Upload Theme**
6. Upload the downloaded ZIP
7. Activate Nexus

**Via WP-CLI (Fastest):**
```bash
wp theme install https://github.com/jdram82/nexus/archive/refs/heads/main.zip --activate --force
```

---

## 📊 Verify Token Works

Add this code temporarily to test:

```php
<?php
// test-github.php - Upload to WordPress root, access via browser

require_once 'wp-load.php';

echo "<h1>Nexus GitHub Update Test</h1>";

// Check token
if ( defined( 'NEXUS_GITHUB_TOKEN' ) ) {
    echo "<p>✅ Token is defined</p>";
    
    // Test API
    $response = wp_remote_get( 'https://api.github.com/repos/jdram82/nexus/releases/latest', array(
        'headers' => array(
            'Authorization' => 'token ' . NEXUS_GITHUB_TOKEN,
        ),
    ) );
    
    if ( is_wp_error( $response ) ) {
        echo "<p>❌ Error: " . $response->get_error_message() . "</p>";
    } else {
        $code = wp_remote_retrieve_response_code( $response );
        echo "<p>Response Code: $code</p>";
        
        if ( $code === 200 ) {
            $body = json_decode( wp_remote_retrieve_body( $response ), true );
            echo "<p>✅ Latest Release: " . $body['tag_name'] . "</p>";
            echo "<p>✅ Update system should work!</p>";
        } else {
            echo "<p>❌ GitHub returned: $code</p>";
            echo "<pre>" . wp_remote_retrieve_body( $response ) . "</pre>";
        }
    }
    
    // Check rate limit
    $rate = wp_remote_get( 'https://api.github.com/rate_limit', array(
        'headers' => array(
            'Authorization' => 'token ' . NEXUS_GITHUB_TOKEN,
        ),
    ) );
    
    $rate_body = json_decode( wp_remote_retrieve_body( $rate ), true );
    $remaining = $rate_body['resources']['core']['remaining'];
    $limit = $rate_body['resources']['core']['limit'];
    
    echo "<p>Rate Limit: $remaining / $limit remaining</p>";
    
} else {
    echo "<p>❌ NEXUS_GITHUB_TOKEN not defined in wp-config.php</p>";
    echo "<p>Add this to wp-config.php:</p>";
    echo "<pre>define( 'NEXUS_GITHUB_TOKEN', 'your_token_here' );</pre>";
}
```

Access: `https://yoursite.com/test-github.php`

---

## 📝 Summary

**The issue:** GitHub API rate limit (60 requests/hour without token)

**The fix:** Add GitHub token to wp-config.php (increases to 5,000 requests/hour)

**Time to fix:** 5 minutes

**After fixing:**
- ✅ Updates will appear in WordPress admin automatically
- ✅ One-click updates work
- ✅ No more rate limit issues

---

## 📞 Need Help?

See comprehensive guide: [docs/UPDATE_CONFIGURATION.md](docs/UPDATE_CONFIGURATION.md)

**Token is safe because:**
- Only has `public_repo` scope (read-only)
- Can't modify your code
- Can be revoked anytime
- Only checks for updates
