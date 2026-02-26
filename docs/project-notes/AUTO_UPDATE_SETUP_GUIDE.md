# Auto-Update Setup Guide for Nexus Theme & UL/NEC Plugin

**Date:** February 25, 2026  
**Author:** Auto-Update Implementation Guide

---

## 🎯 Overview

Your **Nexus Theme already has a complete GitHub-based auto-update system** built-in! You just need to:
1. Create GitHub releases with your packages
2. Configure a GitHub token
3. Enjoy automatic updates in WordPress

---

## 📦 Making Files Available for Download

### **Option 1: GitHub Releases** (Recommended - Auto-Updates Work)

#### Step 1: Create a New Release on GitHub

```bash
# 1. Go to your GitHub repository
https://github.com/jdram82/nexus

# 2. Click "Releases" (right sidebar)
# 3. Click "Create a new release"

# 4. Fill in the release form:
Tag version: v3.2.4
Release title: Nexus Theme v3.2.4 - UL/NEC Workflow
Description: [Copy from RELEASE_NOTES_v3.2.4.md]

# 5. Upload files:
- nexus-theme-v3.2.4-with-ulnec-workflow.zip
- ul-nec-compliance-v1.3.1.zip (if not already uploaded)

# 6. Check "Set as the latest release"
# 7. Click "Publish release"
```

**Result:** Files will be available at:
- Theme: `https://github.com/jdram82/nexus/releases/download/v3.2.4/nexus-theme-v3.2.4-with-ulnec-workflow.zip`
- Plugin: `https://github.com/jdram82/nexus/releases/download/v3.2.4/ul-nec-compliance-v1.3.1.zip`

---

### **Option 2: Direct Download from Codespace** (Quick Method)

#### Using VS Code:

```bash
# In terminal:
cd /workspaces/codespaces-blank/
ls -lh nexus-theme-v3.2.4*.zip

# Then in VS Code:
1. Right-click the file in Explorer
2. Click "Download..."
3. File saves to your local computer
```

#### Using Command Line:

```bash
# Download via SCP (if you have SSH access):
scp user@codespace:/workspaces/codespaces-blank/nexus-theme-v3.2.4-with-ulnec-workflow.zip ~/Downloads/

# Or upload to a file server:
# Install gh CLI tool first
gh release create v3.2.4 /workspaces/codespaces-blank/nexus-theme-v3.2.4-with-ulnec-workflow.zip
```

---

## 🔄 Auto-Update System Architecture

### **How It Works** (Already Implemented!)

```
┌─────────────────────────────────────────────────┐
│  WordPress Site (jdsancontrols.com)             │
│                                                  │
│  1. Every hour, checks GitHub API               │
│  2. Compares installed version vs latest        │
│  3. Shows "Update Available" notification       │
│  4. One-click update downloads from GitHub      │
│  5. Extracts and installs automatically         │
└─────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────┐
│  GitHub Repository (jdram82/nexus)              │
│                                                  │
│  - Releases contain packaged .zip files         │
│  - API provides version info                    │
│  - Theme auto-detected via GitHub API           │
└─────────────────────────────────────────────────┘
```

### **What's Already Implemented**

✅ **Theme Auto-Updater** (`inc/class-nexus-theme-updater.php` - 605 lines)
- Checks GitHub releases every hour
- Shows update notification in WordPress admin
- One-click update installation
- Folder name handling (fixes GitHub zipball issues)
- Rate limit protection
- Error handling

✅ **Features:**
- GitHub API integration
- Transient caching (1 hour)
- Manual update check button
- AJAX update checker
- Automatic folder renaming
- Compatible with any theme folder name

---

## 🔧 Setup Instructions

### **Step 1: Create GitHub Personal Access Token**

This allows unlimited API requests (avoids rate limits).

```bash
# 1. Go to GitHub Settings
https://github.com/settings/tokens

# 2. Click "Generate new token (classic)"

# 3. Set permissions:
☑ public_repo (read public repositories)

# 4. Click "Generate token"

# 5. Copy the token (starts with ghp_...)
```

---

### **Step 2: Add Token to WordPress**

Edit your `wp-config.php` file:

```php
/* Add this BEFORE "That's all, stop editing!" line */

/**
 * GitHub Token for Nexus Theme Updates
 * Allows unlimited API requests
 */
define( 'NEXUS_GITHUB_TOKEN', 'ghp_YourTokenHere123456789' );

/* That's all, stop editing! Happy publishing. */
```

**Location:** `/var/www/html/wp-config.php` (or your WordPress root)

---

### **Step 3: Upload Theme Files to GitHub Releases**

#### **Using GitHub Web Interface:**

```bash
1. Visit: https://github.com/jdram82/nexus/releases/new

2. Fill in:
   - Tag: v3.2.4
   - Title: Nexus Theme v3.2.4 - UL/NEC Workflow
   - Description: [Paste from RELEASE_NOTES_v3.2.4.md]

3. Drag and drop:
   ☑ nexus-theme-v3.2.4-with-ulnec-workflow.zip

4. Click "Publish release"
```

#### **Using GitHub CLI (gh):**

```bash
# Install gh CLI tool
brew install gh  # macOS
# or download from: https://cli.github.com/

# Login
gh auth login

# Create release and upload file
cd /workspaces/codespaces-blank/

gh release create v3.2.4 \
  --title "Nexus Theme v3.2.4 - UL/NEC Workflow" \
  --notes-file nexus-theme/RELEASE_NOTES_v3.2.4.md \
  nexus-theme-v3.2.4-with-ulnec-workflow.zip
```

---

### **Step 4: Test Auto-Update on Your Site**

```bash
1. Go to: WordPress Admin → Appearance → Themes

2. You should see:
   "Update Available: Version 3.2.4"

3. Click "Update Now"

4. Theme updates automatically!
```

---

## 🎛️ Manual Update Check

If update doesn't show automatically:

### **Option A: Admin Dashboard**
```bash
WordPress Admin → Dashboard → Nexus Updates
Click "Check for Updates"
```

### **Option B: Clear Transient**
```php
// In WordPress admin → Tools → Site Health → Info → Constants
// Or add to functions.php temporarily:
delete_transient('nexus_theme_update_check');
```

### **Option C: Command Line**
```bash
wp transient delete nexus_theme_update_check
wp theme update nexus-theme
```

---

## 🔌 Plugin Auto-Updates (Advanced)

The plugin doesn't have built-in auto-updates yet. Here's how to add it:

### **Option 1: Use Update Manager Plugin**

Install a plugin like:
- **GitHub Updater** (by Andy Fragen)
- **WP Pusher**
- **Easy Theme and Plugin Upgrades**

### **Option 2: Implement Custom Plugin Updater**

Create `ul-nec-compliance/includes/class-ulnec-updater.php`:

```php
<?php
class ULNEC_Plugin_Updater {
    private $github_user = 'jdram82';
    private $github_repo = 'nexus';
    private $plugin_slug = 'ul-nec-compliance/ul-nec-compliance.php';
    
    public function __construct() {
        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_for_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 20, 3);
    }
    
    public function check_for_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        $release = $this->get_latest_plugin_release();
        
        if (!is_wp_error($release)) {
            $remote_version = ltrim($release['tag_name'], 'plugin-v');
            $current_version = ULNEC_VERSION;
            
            if (version_compare($current_version, $remote_version, '<')) {
                $transient->response[$this->plugin_slug] = (object) [
                    'slug' => dirname($this->plugin_slug),
                    'new_version' => $remote_version,
                    'url' => $release['html_url'],
                    'package' => $release['zipball_url'], // or specific asset URL
                ];
            }
        }
        
        return $transient;
    }
    
    private function get_latest_plugin_release() {
        $api_url = "https://api.github.com/repos/{$this->github_user}/{$this->github_repo}/releases/tags/plugin-v" . ULNEC_VERSION;
        
        $response = wp_remote_get($api_url, [
            'timeout' => 15,
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
                'Authorization' => defined('NEXUS_GITHUB_TOKEN') ? 'token ' . NEXUS_GITHUB_TOKEN : '',
            ]
        ]);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        return json_decode(wp_remote_retrieve_body($response), true);
    }
    
    public function plugin_info($false, $action, $response) {
        if ($action !== 'plugin_information') {
            return $false;
        }
        
        if ($response->slug !== dirname($this->plugin_slug)) {
            return $false;
        }
        
        $release = $this->get_latest_plugin_release();
        
        if (is_wp_error($release)) {
            return $false;
        }
        
        return (object) [
            'name' => 'UL/NEC Compliance Plugin',
            'slug' => dirname($this->plugin_slug),
            'version' => ltrim($release['tag_name'], 'plugin-v'),
            'author' => '<a href="https://jdsancontrols.com">JDS & N Controls</a>',
            'homepage' => $release['html_url'],
            'download_link' => $release['zipball_url'],
            'sections' => [
                'description' => $release['body'],
            ],
        ];
    }
}

// Initialize
new ULNEC_Plugin_Updater();
```

Then add to `ul-nec-compliance.php`:
```php
// Auto-updater
require_once ULNEC_PLUGIN_DIR . 'includes/class-ulnec-updater.php';
```

---

## 📋 Release Checklist

When releasing a new version:

### **Theme Release:**
```bash
✅ 1. Update version in style.css (line 7)
✅ 2. Update NEXUS_VERSION in functions.php (line 15)
✅ 3. Create package: nexus-theme-vX.X.X.zip
✅ 4. Create RELEASE_NOTES_vX.X.X.md
✅ 5. Create GitHub release with tag vX.X.X
✅ 6. Upload .zip file to release
✅ 7. Mark as "Latest release"
```

### **Plugin Release:**
```bash
✅ 1. Update Version in ul-nec-compliance.php (line 6)
✅ 2. Update ULNEC_VERSION constant (line 23)
✅ 3. Create package: ul-nec-compliance-vX.X.X.zip
✅ 4. Create GitHub release with tag plugin-vX.X.X
✅ 5. Upload .zip file to release
```

---

## 🚨 Troubleshooting

### **"No update available" showing**

```bash
# Clear update cache
delete_transient('nexus_theme_update_check');

# Or in wp-config.php temporarily:
define('WP_AUTO_UPDATE_CORE', true);
```

### **"GitHub API rate limit exceeded"**

```bash
# Add token to wp-config.php:
define('NEXUS_GITHUB_TOKEN', 'ghp_YourToken');
```

### **"Update failed - folder name mismatch"**

The updater automatically handles this! But if issues persist:

```bash
# Ensure your .zip file has correct structure:
nexus-theme-v3.2.4.zip
└── nexus-theme/          ← Folder must match installed theme name
    ├── style.css
    ├── functions.php
    └── ...
```

### **"Download failed"**

```bash
# Check file permissions
# Ensure GitHub release is published (not draft)
# Check if file is actually uploaded to release
```

---

## 📊 Update Mechanism Comparison

| Method | Theme | Plugin | Difficulty | Auto-Update |
|--------|-------|--------|------------|-------------|
| **GitHub Releases** (Current) | ✅ Built-in | ❌ Manual | Easy | ✅ Yes (theme) |
| **WordPress.org** | ❌ Private | ❌ Private | Hard | ✅ Yes |
| **Update Manager Plugin** | ✅ Yes | ✅ Yes | Medium | ✅ Yes |
| **Custom Updater** | ✅ Done | ⏳ To-Do | Medium | ✅ Yes |
| **Manual Upload** | ✅ Yes | ✅ Yes | Easy | ❌ No |

---

## 🎯 Quick Start (5 Minutes)

```bash
# 1. Create GitHub token
https://github.com/settings/tokens → Generate new token

# 2. Add to wp-config.php
define('NEXUS_GITHUB_TOKEN', 'ghp_YOUR_TOKEN');

# 3. Download your file from codespace
# Right-click in VS Code → Download

# 4. Create GitHub release
https://github.com/jdram82/nexus/releases/new
- Tag: v3.2.4
- Upload: nexus-theme-v3.2.4-with-ulnec-workflow.zip
- Publish

# 5. Check for updates in WordPress
Appearance → Themes → "Update Available"

✅ Done! Auto-updates working!
```

---

## 📚 Additional Resources

### **Diagnostic Tools:**
- `nexus-update-diagnostic-plugin.php` - Tests update system
- `nexus-github-token-config.php` - Generates wp-config code

### **Documentation:**
- `inc/class-nexus-theme-updater.php` - Complete updater code
- `RELEASE_NOTES_v3.2.4.md` - Release information

### **GitHub:**
- Repository: https://github.com/jdram82/nexus
- Issues: https://github.com/jdram82/nexus/issues
- Releases: https://github.com/jdram82/nexus/releases

---

## ✅ Summary

**Your auto-update system is already built!** Just:

1. ✅ **Get files from codespace** (download or push to GitHub)
2. ✅ **Create GitHub release** (v3.2.4)
3. ✅ **Upload .zip files** to release
4. ✅ **Add GitHub token** to wp-config.php
5. ✅ **Check for updates** in WordPress admin

**Result:** One-click theme updates from your WordPress dashboard! 🚀

---

**Status:** 
- Theme auto-updates: ✅ **Fully implemented**
- Plugin auto-updates: ❌ **Not yet implemented** (optional)
- GitHub integration: ✅ **Ready to use**
