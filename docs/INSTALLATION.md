# Installation Guide - Nexus Theme

Complete step-by-step installation and configuration guide.

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Installation Methods](#installation-methods)
3. [Initial Configuration](#initial-configuration)
4. [Database Setup](#database-setup)
5. [First Steps](#first-steps)
6. [Troubleshooting](#troubleshooting)

---

## System Requirements

### Minimum Requirements

| Component | Requirement |
|-----------|-------------|
| WordPress | 6.0 or higher |
| PHP | 7.4 or higher |
| MySQL | 5.7 or higher |
| Apache/Nginx | Latest stable |
| PHP Memory | 128MB minimum (256MB recommended) |
| PHP Extensions | mysqli, gd, curl, mbstring, xml |

### Recommended Environment

- PHP 8.0+
- MySQL 8.0+ or MariaDB 10.5+
- 512MB PHP memory limit
- HTTPS/SSL enabled
- mod_rewrite enabled (Apache)

### Optional Requirements

- WooCommerce 5.0+ (for e-commerce features)
- Pretty permalinks enabled
- Cron jobs enabled
- GD or ImageMagick for image processing

---

## Installation Methods

### Method 1: Git Clone (Recommended for Developers)

```bash
# Navigate to WordPress themes directory
cd /path/to/wordpress/wp-content/themes/

# Clone the repository
git clone https://github.com/jdram82/nexus.git nexus-theme

# Set proper permissions
chmod -R 755 nexus-theme
```

### Method 2: Download ZIP

1. Visit [GitHub Repository](https://github.com/jdram82/nexus)
2. Click "Code" → "Download ZIP"
3. Extract `nexus-main.zip`
4. Rename folder to `nexus-theme`
5. Upload via FTP to `wp-content/themes/`

### Method 3: WordPress Admin Upload

1. Download ZIP from GitHub
2. Go to **WordPress Admin → Appearance → Themes**
3. Click **"Add New"** → **"Upload Theme"**
4. Choose ZIP file
5. Click **"Install Now"**
6. Click **"Activate"** after installation

---

## Initial Configuration

### Step 1: Activate Theme

1. Navigate to **Appearance → Themes**
2. Find "Nexus" theme
3. Click **"Activate"**
4. You'll see confirmation message

### Step 2: Run Database Setup (IMPORTANT)

**First-time activation requires database tables:**

1. Go to **Nexus Pro → Dashboard**
2. You'll see a notice: "Database tables need to be created"
3. Click **"Run Setup"** or **"Activate Pro Features"**
4. Wait for confirmation (5-10 seconds)

**Tables Created:**
```sql
wp_nexus_templates          # Template manager
wp_nexus_analytics          # Analytics data
wp_nexus_ab_tests           # A/B test definitions
wp_nexus_ab_results         # A/B test results
wp_nexus_form_submissions   # Form entries
```

### Step 3: Configure Permalinks

**Required for proper functionality:**

1. Go to **Settings → Permalinks**
2. Select **"Post name"** (recommended)
3. Or use **"Custom Structure"**: `/%postname%/`
4. Click **"Save Changes"**

### Step 4: Set Theme Options

**Go to Appearance → Customize:**

1. **Theme Colors**
   - Set Primary Color (#2196f3)
   - Set Secondary Color (#4caf50)
   - Configure accent colors

2. **Typography**
   - Choose heading font
   - Choose body font
   - Set base font size

3. **Layout Settings**
   - Container width (1200px default)
   - Sidebar position
   - Content layout

4. Click **"Publish"** to save

---

## Database Setup

### Manual Database Creation (If Needed)

If automatic setup fails, run this SQL manually:

```sql
-- Templates table
CREATE TABLE IF NOT EXISTS `wp_nexus_templates` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) NOT NULL,
  `template_data` longtext NOT NULL,
  `template_type` varchar(50) NOT NULL DEFAULT 'page',
  `is_premium` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `template_type` (`template_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Analytics table
CREATE TABLE IF NOT EXISTS `wp_nexus_analytics` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_id` bigint(20) UNSIGNED NOT NULL,
  `page_url` varchar(255) NOT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `metric_type` varchar(50) NOT NULL,
  `metric_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `recorded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `metric_type` (`metric_type`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- A/B Tests table
CREATE TABLE IF NOT EXISTS `wp_nexus_ab_tests` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `test_name` varchar(255) NOT NULL,
  `variant_a` text NOT NULL,
  `variant_b` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ended_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- A/B Results table
CREATE TABLE IF NOT EXISTS `wp_nexus_ab_results` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `test_id` bigint(20) UNSIGNED NOT NULL,
  `variant` varchar(1) NOT NULL,
  `views` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `conversions` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `recorded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `test_id` (`test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Form Submissions table
CREATE TABLE IF NOT EXISTS `wp_nexus_form_submissions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `form_id` bigint(20) UNSIGNED NOT NULL,
  `form_data` longtext NOT NULL,
  `user_ip` varchar(45) DEFAULT NULL,
  `submitted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Verify Tables

```sql
-- Check if tables exist
SHOW TABLES LIKE 'wp_nexus_%';

-- Should show 5 tables:
-- wp_nexus_ab_results
-- wp_nexus_ab_tests
-- wp_nexus_analytics
-- wp_nexus_form_submissions
-- wp_nexus_templates
```

---

## First Steps

### 1. Create Your First Page

**Using Theme Builder:**

1. Pages → Add New
2. Title: "Homepage"
3. Template: "Full Width"
4. Click **"Theme Builder"** button (top right)
5. Add widgets from left sidebar
6. Drag to canvas
7. Configure in right sidebar
8. Click "Save" (Ctrl+S)
9. Preview and publish

### 2. Set Up Navigation

**Create Menu:**

1. Appearance → Menus
2. Create new menu: "Primary Menu"
3. Add pages/links
4. Enable **"Mega Menu"** for top-level items (optional)
5. Configure icons, badges, columns
6. Assign to "Primary Navigation" location
7. Save

### 3. Configure Home Page

**Set Static Homepage:**

1. Settings → Reading
2. Select **"A static page"**
3. Homepage: Choose your homepage
4. Posts page: Choose blog page (optional)
5. Save Changes

### 4. Create Documentation

**If using docs feature:**

1. Documentation → Add New
2. Write technical content
3. Add code blocks with syntax highlighting
4. Assign to category
5. Publish

### 5. Set Up Analytics

**Enable tracking:**

1. Nexus Pro → Analytics
2. Analytics starts automatically
3. View dashboard after 24 hours
4. Data appears in real-time

### 6. Create Client Portal (Optional)

**For SaaS businesses:**

1. Nexus Pro → Client Portal
2. Enable portal
3. Create portal pages:
   - Dashboard
   - Projects
   - Downloads
4. Assign users
5. Configure permissions

---

## Post-Installation Checklist

- [ ] Theme activated successfully
- [ ] Database tables created (5 tables)
- [ ] Permalinks set to "Post name"
- [ ] Theme colors configured
- [ ] Typography set
- [ ] Primary menu created
- [ ] Homepage set
- [ ] Test page created with Theme Builder
- [ ] Analytics enabled
- [ ] Performance monitor checked
- [ ] SEO settings configured

---

## Security Configuration

### Recommended Security Steps

1. **Update .htaccess** (Apache):
```apache
# Protect theme files
<FilesMatch "^\.">
  Order allow,deny
  Deny from all
</FilesMatch>
```

2. **Disable File Editing**:
```php
// Add to wp-config.php
define('DISALLOW_FILE_EDIT', true);
```

3. **Enable SSL**:
- Install SSL certificate
- Force HTTPS in WordPress settings
- Update .htaccess for HTTPS redirect

4. **File Permissions**:
```bash
# Directories: 755
find /path/to/wordpress -type d -exec chmod 755 {} \;

# Files: 644
find /path/to/wordpress -type f -exec chmod 644 {} \;
```

---

## Performance Optimization

### After Installation

1. **Enable Caching**:
   - Install caching plugin (optional)
   - Or use server-level caching
   - Nexus is already optimized

2. **Optimize Images**:
   - Install image optimization plugin
   - Or use WebP format
   - Lazy loading enabled by default

3. **Enable GZIP**:
```apache
# Add to .htaccess
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/css text/javascript application/javascript
</IfModule>
```

4. **Check Performance**:
   - Go to Nexus Pro → Performance
   - Run performance test
   - Follow recommendations

---

## Troubleshooting

### Database Tables Not Created

**Solution:**
```php
// Add to wp-config.php temporarily
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

// Check /wp-content/debug.log for errors
```

### White Screen After Activation

**Solution:**
1. Increase PHP memory limit in wp-config.php:
```php
define('WP_MEMORY_LIMIT', '256M');
```
2. Check error logs
3. Deactivate plugins one by one

### 404 Errors on Pages

**Solution:**
1. Settings → Permalinks
2. Click "Save Changes" (flush rewrite rules)
3. If persists, check .htaccess permissions

### Visual Builder Not Loading

**Solution:**
1. Clear browser cache
2. Check browser console for JavaScript errors
3. Disable conflicting plugins
4. Ensure jQuery is loaded

### Analytics Not Tracking

**Solution:**
1. Check if logged in (logged-in users not tracked)
2. Verify database tables exist
3. Check cookie settings
4. Disable cache temporarily

---

## Upgrading From Previous Version

### Backup First

```bash
# Backup theme files
cp -r wp-content/themes/nexus-theme wp-content/themes/nexus-theme-backup

# Backup database
mysqldump -u username -p database_name > nexus-backup.sql
```

### Upgrade Process

1. Deactivate theme
2. Replace theme files (keep custom code separate)
3. Reactivate theme
4. Run database update if prompted
5. Clear all caches
6. Test functionality

---

## Getting Help

If installation fails:

1. Check [Troubleshooting Guide](TROUBLESHOOTING.md)
2. Review [GitHub Issues](https://github.com/jdram82/nexus/issues)
3. Post in [Discussions](https://github.com/jdram82/nexus/discussions)
4. Include:
   - WordPress version
   - PHP version
   - Error messages
   - Steps to reproduce

---

**Next Steps:** Read [Phase 1 Documentation](PHASE-1-CORE.md) to learn about core features.

[⬆ Back to Top](#installation-guide---nexus-theme)
