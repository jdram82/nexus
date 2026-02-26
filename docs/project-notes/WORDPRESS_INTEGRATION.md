# WordPress Integration Guide - Nexus Theme

**Quick Start Guide for Manual Testing**  
**Version**: 1.6.1  
**Target Users**: Theme testers and administrators  

---

## 🚀 Quick Installation (5 Minutes)

### Prerequisites
- WordPress 6.0+ installed and running
- Admin access to WordPress dashboard
- FTP/SSH access OR ability to upload themes
- PHP 7.4+ with extensions: mysqli, gd, curl, mbstring

---

## Method 1: GitHub Clone (Recommended)

### Step 1: Clone Repository

```bash
# SSH into your server
ssh user@yourserver.com

# Navigate to WordPress themes directory
cd /var/www/html/wp-content/themes/

# Clone Nexus theme
git clone https://github.com/jdram82/nexus.git nexus-theme

# Set proper permissions
sudo chown -R www-data:www-data nexus-theme
sudo chmod -R 755 nexus-theme
```

### Step 2: Activate in WordPress

1. Log in to **WordPress Admin** (yoursite.com/wp-admin)
2. Go to **Appearance → Themes**
3. Find "Nexus" theme card
4. Click **"Activate"** button
5. You'll see success message: "New theme activated"

### Step 3: Initial Setup

1. **Notice will appear**: "Nexus Pro features require database setup"
2. Click **"Run Setup Now"** button
3. Wait 5-10 seconds
4. Success message: "Database tables created successfully"
5. Page will refresh automatically

---

## Method 2: Manual Upload (If No SSH)

### Step 1: Download Theme

1. Go to https://github.com/jdram82/nexus
2. Click green **"Code"** button
3. Select **"Download ZIP"**
4. Save `nexus-main.zip` to your computer

### Step 2: Extract and Rename

1. Extract ZIP file
2. You'll get folder: `nexus-main`
3. **Rename** to: `nexus-theme`
4. This is important for proper theme detection

### Step 3: Upload via FTP

**Using FileZilla or similar FTP client:**

1. Connect to your server via FTP
2. Navigate to: `/wp-content/themes/`
3. Upload entire `nexus-theme` folder
4. Wait for upload to complete (may take 2-5 minutes)
5. Verify all files uploaded successfully

**File structure should look like:**
```
wp-content/
  themes/
    nexus-theme/
      style.css
      functions.php
      header.php
      footer.php
      inc/
      pro/
      assets/
      (... more files)
```

### Step 4: Activate Theme

1. Go to WordPress Admin → **Appearance → Themes**
2. Find "Nexus" theme
3. Click **"Activate"**
4. Click **"Run Setup Now"** when prompted

---

## Method 3: WordPress Admin Upload (Limited)

**Note**: May fail if theme ZIP exceeds PHP upload limit

### Steps:

1. Go to **WordPress Admin → Appearance → Themes**
2. Click **"Add New"** at the top
3. Click **"Upload Theme"** button
4. Click **"Choose File"**
5. Select `nexus-main.zip` (downloaded from GitHub)
6. Click **"Install Now"**
7. Wait for upload and extraction
8. Click **"Activate"** when ready
9. Run database setup when prompted

**If upload fails** with "exceeds maximum upload size":
- Use Method 1 (SSH) or Method 2 (FTP) instead
- OR increase PHP upload limit in php.ini

---

## ⚙️ Post-Installation Configuration

### 1. Configure Permalinks (REQUIRED)

**Why**: Nexus uses custom post types and REST API that need pretty URLs

1. Go to **Settings → Permalinks**
2. Select **"Post name"** (recommended)
3. Or use **"Custom Structure"**: `/%postname%/`
4. Click **"Save Changes"**
5. Test by visiting: `yoursite.com/wp-json/nexus/v1/health`
6. Should see JSON response (not 404 error)

---

### 2. Verify Database Tables

**Check tables were created:**

```sql
-- Run in phpMyAdmin or MySQL client
SHOW TABLES LIKE 'wp_nexus%';
```

**Expected tables:**
```
wp_nexus_templates
wp_nexus_analytics
wp_nexus_ab_tests
wp_nexus_ab_results
wp_nexus_form_submissions
```

**If tables missing**:
1. Go to **Nexus Pro → Dashboard**
2. Click **"Run Database Setup"** again
3. Check PHP error log for issues
4. Verify MySQL user has CREATE TABLE permission

---

### 3. Theme Customization

**Basic theme settings:**

1. Go to **Appearance → Customize**
2. You'll see Nexus-specific sections:
   - **Theme Colors** - Set primary/secondary colors
   - **Typography** - Choose fonts, sizes
   - **Layout** - Container width, sidebar position
   - **Header Settings** - Logo, menu, top bar
   - **Footer Settings** - Widgets, copyright

3. Make changes and click **"Publish"**

**Recommended initial settings:**
- Primary Color: `#667eea` (Nexus purple)
- Secondary Color: `#4ecdc4` (Nexus teal)
- Container Width: `1200px`
- Font: System font stack (for performance)

---

### 4. Create Sample Content

**Test the custom post types:**

#### Create a Project:
1. Go to **Projects → Add New**
2. Title: "Sample Project"
3. Content: Project description
4. **Project Details** meta box:
   - Client Name: "Acme Corp"
   - Project Date: Today's date
   - Project URL: https://example.com
5. Set Featured Image
6. Click **"Publish"**

#### Create a Product:
1. Go to **Products → Add New**  
2. Title: "Sample Product"
3. Content: Product description
4. **Product Details** meta box:
   - Price: $99
   - Version: 1.0.0
5. Set Featured Image
6. Click **"Publish"**

#### Create a Download:
1. Go to **Downloads → Add New**
2. Title: "Sample Download"
3. **Download Details** meta box:
   - Version: 1.0.0
   - File Size: 5MB
   - Download Count: 0 (auto-increments)
5. Click **"Publish"**

---

## 🎨 Testing Pro Features

### Enable Pro Tier

**If testing Pro features:**

1. Go to **Nexus Pro → License**
2. Enter license key OR for testing purposes:
3. Manually update database:

```sql
UPDATE wp_options 
SET option_value = 'pro' 
WHERE option_name = 'nexus_license_tier';
```

4. Refresh WordPress admin
5. New menu items appear: AI Credits, Template Library, etc.

---

### Configure Payment Gateway (For Credit Purchases)

**Razorpay Test Setup:**

1. Go to **Nexus Options → Payment Gateways**
2. Select **"Razorpay"** radio button
3. Enter test credentials:
   ```
   Key ID: rzp_test_1DP5mmOlF5G5ag
   Key Secret: thisissecret
   ```
4. Click **"Save Settings"**

**Test purchase:**
1. Go to **Nexus Options → AI Credits**
2. Click **"Buy 100 Credits"** ($10)
3. Razorpay checkout modal opens
4. Use test card: `4111 1111 1111 1111`
5. CVV: `123`, Expiry: `12/28`
6. Complete payment
7. Credits should be added to your account

**Stripe Test Setup:**

1. Go to **Nexus Options → Payment Gateways**
2. Select **"Stripe"**
3. Get test keys from https://dashboard.stripe.com/test/apikeys
4. Enter:
   ```
   Publishable Key: pk_test_51...
   Secret Key: sk_test_51...
   ```
5. Save settings

**Test purchase:**
1. Go to AI Credits page
2. Purchase credits
3. Use test card: `4242 4242 4242 4242`
4. Any CVV, any future expiry
5. Payment should complete successfully

---

## 🔧 Testing Advanced Features

### Enable Advanced Tier

```sql
UPDATE wp_options 
SET option_value = 'advanced' 
WHERE option_name = 'nexus_license_tier';
```

Refresh admin - Advanced features now available.

---

### Test Loop Builder

1. Go to **Nexus Options → Loop Builder**
2. Click **"Create New Loop"**
3. Configure query:
   - Post Type: **Post**
   - Category: Select any
   - Posts per page: **6**
   - Order by: **Date**
4. Design template:
   - Layout: **Grid**
   - Columns: **3**
   - Elements: Title, Excerpt, Featured Image
5. Click **"Preview"** - see live preview
6. Click **"Save Loop"** - name it "Blog Grid"
7. Get shortcode: `[nexus_loop id="1"]`
8. Create a test page
9. Add shortcode to page content
10. Publish and view page
11. Verify loop displays correctly

---

### Test Plugin Orchestrator

1. Go to **Nexus Options → Plugin Orchestrator**
2. Dashboard shows detected plugins (initially empty)
3. Install **Yoast SEO** plugin
4. Activate Yoast SEO
5. Refresh Plugin Orchestrator
6. Yoast should appear under "SEO Tools" category
7. Status shows: "Active - Integrated"
8. Install **WPForms Lite**
9. Activate WPForms
10. Refresh Plugin Orchestrator
11. WPForms appears under "Forms" category
12. Check box: "Deactivate Nexus Form Builder"
13. Save settings
14. Native form builder now disabled (deferred to WPForms)

---

### Test White-Label System

1. Go to **Nexus Options → White-Label**
2. Enter custom theme name: **"MyBrand Theme"**
3. Description: **"Premium WordPress theme by MyBrand"**
4. Upload logo (200x50px PNG recommended)
5. Set colors:
   - Primary: `#ff6b6b`
   - Secondary: `#4ecdc4`
6. Check **"Hide WordPress Branding"**
7. Check **"Hide Theme Author"**
8. Click **"Save Settings"**
9. Log out
10. View login page - should show your logo
11. Log back in
12. Go to **Appearance → Themes**
13. Theme name changed to "MyBrand Theme"
14. Admin footer - WordPress branding hidden

---

## 🧪 Testing Agency Features

### Enable Agency Tier

```sql
UPDATE wp_options 
SET option_value = 'agency' 
WHERE option_name = 'nexus_license_tier';
```

---

### Test Multi-Site Dashboard

1. Go to **Nexus Options → Agency Dashboard**
2. Click **"Add New Site"**
3. Enter:
   - Site Name: **Client Site 1**
   - Site URL: https://client1.example.com
   - Admin URL: https://client1.example.com/wp-admin
   - Admin Email: admin@client1.com
4. Click **"Add Site"**
5. Site appears in dashboard list
6. Add 2-3 more test sites
7. Test filtering: Type "Client" in search
8. Test export: Click **"Export Report"**
9. Download CSV file
10. Open in Excel/Google Sheets
11. Verify site data present

**Note**: Auto-monitoring is not yet implemented - health status shows "Unknown"

---

## 🐛 Troubleshooting

### Theme Not Appearing

**Symptoms**: Nexus doesn't show in Appearance → Themes

**Fixes**:
1. Check folder name is `nexus-theme` (not `nexus-main`)
2. Verify `style.css` exists in theme root
3. Check file permissions: `chmod -R 755 nexus-theme`
4. Check PHP error log for syntax errors
5. Verify WordPress version is 6.0+

---

### Database Setup Failed

**Symptoms**: "Failed to create tables" error

**Fixes**:
1. Check MySQL user has CREATE TABLE permission
2. Verify database connection in wp-config.php
3. Check disk space on server
4. Try manual SQL creation (see INSTALLATION.md)
5. Check PHP error log for MySQL errors

**Manual table creation:**
```sql
-- Copy SQL from docs/INSTALLATION.md
-- Run in phpMyAdmin or MySQL client
```

---

### 404 Errors on REST API

**Symptoms**: `/wp-json/nexus/v1/health` returns 404

**Fixes**:
1. Go to **Settings → Permalinks**
2. Click **"Save Changes"** (flush rewrite rules)
3. Check `.htaccess` has rewrite rules
4. Verify `mod_rewrite` enabled (Apache)
5. Check nginx configuration (if using nginx)

**Test REST API:**
```bash
curl https://yoursite.com/wp-json/nexus/v1/health
```

Expected response:
```json
{
  "status": "ok",
  "version": "1.6.1",
  "php_version": "8.0.0",
  "wordpress_version": "6.4.0"
}
```

---

### Payment Gateway Not Working

**Symptoms**: Payment modal doesn't open or payments fail

**Fixes**:
1. Check browser console for JavaScript errors
2. Verify gateway credentials are correct
3. Check SSL certificate is valid (HTTPS required)
4. Test with different browser
5. Verify gateway script loading:
   - Razorpay: `https://checkout.razorpay.com/v1/checkout.js`
   - Stripe: `https://js.stripe.com/v3/`
6. Check PHP error log for AJAX errors

**Test gateway connection:**
```javascript
// Browser console
console.log(nexusCredits.gateway); // Should show 'razorpay' or 'stripe'
console.log(nexusCredits.gatewayKey); // Should show your key
```

---

### Credits Not Adding After Payment

**Symptoms**: Payment completes but credits don't increase

**Fixes**:
1. Check browser console for AJAX errors
2. Verify payment verification in PHP error log
3. Check credit history table:
   ```sql
   SELECT * FROM wp_nexus_credit_log ORDER BY id DESC LIMIT 10;
   ```
4. Check transient data:
   ```sql
   SELECT * FROM wp_options WHERE option_name LIKE '%nexus_pending_purchase%';
   ```
5. Verify nonce validation passing
6. Check user capabilities (`manage_options`)

---

### AI Features Return Errors

**Symptoms**: "Failed to generate template" error

**Expected Behavior**: AI features currently return **MOCK data**
- This is normal - real OpenAI integration not yet implemented
- Generator will return hardcoded template regardless of prompt
- This is documented limitation in TESTING_READINESS.md

**Not a bug** - planned for Phase 3B implementation

---

## 📊 Performance Verification

### Check PHP Memory

```php
// Add to wp-config.php
define('WP_MEMORY_LIMIT', '256M');
```

### Check Query Performance

```php
// Add to wp-config.php for debugging
define('SAVEQUERIES', true);

// View query log
global $wpdb;
echo '<pre>';
print_r($wpdb->queries);
echo '</pre>';
```

### Check Page Load Time

Use browser DevTools:
1. Open DevTools (F12)
2. Go to Network tab
3. Reload page
4. Check total load time
5. Look for slow resources
6. Should be < 3 seconds on decent hosting

---

## 🔐 Security Checklist

Before production use:

- [ ] Update WordPress to latest version
- [ ] Use strong database password
- [ ] Change database table prefix from `wp_`
- [ ] Disable file editing in wp-config.php:
  ```php
  define('DISALLOW_FILE_EDIT', true);
  ```
- [ ] Install SSL certificate (HTTPS)
- [ ] Use security plugin (Wordfence or similar)
- [ ] Enable automatic backups
- [ ] Use strong admin password
- [ ] Enable two-factor authentication
- [ ] Limit login attempts
- [ ] Keep plugins updated

---

## 📝 Production Deployment Checklist

When moving to live site:

- [ ] Replace test payment gateway keys with live keys
- [ ] Change Cashfree mode from 'sandbox' to 'production' in JavaScript
- [ ] Disable WP_DEBUG in wp-config.php
- [ ] Enable object caching (Redis/Memcached)
- [ ] Set up CDN for static assets
- [ ] Configure cron jobs (if not using WP-Cron)
- [ ] Test all payment flows with small amounts
- [ ] Set up monitoring (UptimeRobot or similar)
- [ ] Configure automated backups
- [ ] Test email notifications
- [ ] Verify GDPR compliance (if EU users)

---

## 🆘 Getting Help

### Check Logs

**PHP Error Log:**
```bash
tail -f /var/log/php/error.log
```

**WordPress Debug Log:**
```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Check: wp-content/debug.log
```

**Browser Console:**
- Press F12 → Console tab
- Look for JavaScript errors
- Check Network tab for failed AJAX requests

### Common Error Messages

| Error | Meaning | Fix |
|-------|---------|-----|
| "Call to undefined function" | Missing PHP extension | Install extension via `sudo apt install php-{extension}` |
| "Cannot modify header information" | Output before headers | Check for whitespace in PHP files |
| "Maximum execution time exceeded" | Script timeout | Increase `max_execution_time` in php.ini |
| "Allowed memory size exhausted" | Out of memory | Increase `memory_limit` in php.ini |
| "Database connection error" | MySQL down or wrong credentials | Check wp-config.php database settings |

---

## 📚 Next Steps After Integration

1. **Complete Manual Testing** - Use TESTING_READINESS.md checklist
2. **Provide Feedback** - Report bugs and issues found
3. **Request Features** - Identify missing functionality
4. **Performance Testing** - Test with realistic content volume
5. **Security Testing** - Run security scanner
6. **User Testing** - Have team members test workflows

---

## 🎯 Quick Reference

**Important Files:**
- Theme: `/wp-content/themes/nexus-theme/`
- Config: `/wp-config.php`
- Uploads: `/wp-content/uploads/`
- Logs: `/wp-content/debug.log`

**Important URLs:**
- Admin: `/wp-admin`
- Customizer: `/wp-admin/customize.php`
- REST API: `/wp-json/nexus/v1/`
- Pro Features: `/wp-admin/admin.php?page=nexus-options`

**Database Tables:**
- Options: `wp_options` (WHERE option_name LIKE 'nexus%')
- Templates: `wp_nexus_templates`
- Credits: `wp_nexus_credit_log`
- Sites: `wp_nexus_agency_sites`

**Support Channels:**
- GitHub Issues: https://github.com/jdram82/nexus/issues
- Documentation: `/docs/` folder in theme
- Error Logs: Check PHP and WordPress debug logs

---

**Last Updated**: December 27, 2025  
**Version**: 1.6.1  
**Status**: Ready for testing (Free, Pro, Advanced tiers)
