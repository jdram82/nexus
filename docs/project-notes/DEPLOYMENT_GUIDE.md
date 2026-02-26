# Nexus Theme Deployment Guide

**Version**: 1.8.1  
**Author**: Jdsan Digitel  
**Last Updated**: December 27, 2025

---

## 🎯 Quick Start

This guide covers deploying Nexus theme from scratch to a live WordPress site with automatic updates enabled.

---

## 📋 Prerequisites

### **Requirements**
- ✅ WordPress 6.0 or higher
- ✅ PHP 7.4 or higher
- ✅ MySQL 5.7+ or MariaDB 10.3+
- ✅ HTTPS/SSL certificate (recommended)
- ✅ FTP/SFTP access to server
- ✅ GitHub account (for automatic updates)

### **Recommended Hosting**
- EasyWP (Namecheap) ✅ Currently using
- DigitalOcean
- Kinsta
- WP Engine
- SiteGround

---

## 🚀 Installation Methods

### **Method 1: WordPress Admin Upload (Recommended)**

1. **Download Theme ZIP**
   - Go to: https://github.com/jdram82/nexus/releases/latest
   - Download `nexus-theme-v1.8.1.zip` (or latest version)
   - Save to your computer

2. **Upload to WordPress**
   - Login to WordPress Admin
   - Go to **Appearance → Themes**
   - Click **"Add New"**
   - Click **"Upload Theme"**
   - Choose the ZIP file
   - Click **"Install Now"**

3. **Activate Theme**
   - Click **"Activate"** when installation completes
   - Your site now uses Nexus theme!

### **Method 2: FTP/SFTP Upload**

1. **Extract ZIP File**
   - Extract `nexus-theme-v1.8.1.zip`
   - You'll get a folder named `nexus-theme`

2. **Upload via FTP**
   - Connect to your server via FileZilla/Cyberduck
   - Navigate to `/wp-content/themes/`
   - Upload the entire `nexus-theme` folder
   - Wait for upload to complete

3. **Activate in WordPress**
   - Go to **Appearance → Themes**
   - Find **Nexus by Jdsan Digitel**
   - Click **"Activate"**

### **Method 3: Git Clone (Developers)**

```bash
cd /path/to/wordpress/wp-content/themes/
git clone https://github.com/jdram82/nexus.git nexus-theme
cd nexus-theme
git checkout v1.8.1
```

Then activate in WordPress admin.

---

## ⚙️ Initial Configuration

### **Step 1: Enable Automatic Updates**

1. **Get GitHub Token**
   - Go to: https://github.com/settings/tokens/new
   - Token name: "Nexus Theme Updates"
   - Expiration: No expiration (or 1 year)
   - Scopes: **public_repo** only
   - Click **"Generate token"**
   - Copy the token (starts with `ghp_`)

2. **Add Token to WordPress**
   
   **Option A: Via wp-config.php (Recommended)**
   - Connect via FTP/SFTP
   - Open `wp-config.php` for editing
   - Add this line BEFORE `/* That's all, stop editing! */`:
   
   ```php
   define( 'NEXUS_GITHUB_TOKEN', 'ghp_YOUR_TOKEN_HERE' );
   ```
   
   - Save and upload
   
   **Option B: Via Plugin**
   - Download `nexus-github-token-config.php` from repo
   - Edit line 13 with your token
   - Upload to `/wp-content/plugins/`
   - Activate in **Plugins** menu

3. **Verify Updates Working**
   - Go to **Appearance → Updates** (new menu item)
   - Should show current version and update status
   - Click **"Check for Updates Now"**
   - Should show "You have the latest version" or available update

---

## 🎨 Theme Customization

### **Basic Setup**

1. **Site Identity**
   - Go to **Appearance → Customize → Site Identity**
   - Upload site logo
   - Set site title and tagline
   - Upload favicon

2. **Menus**
   - Go to **Appearance → Menus**
   - Create primary menu
   - Add pages/links
   - Assign to "Primary Menu" location

3. **Widgets**
   - Go to **Appearance → Widgets**
   - Sidebar and 4 footer widget areas available
   - Add widgets as needed

4. **Homepage Settings**
   - Go to **Settings → Reading**
   - Choose homepage (static page or latest posts)
   - Set posts per page

### **Advanced Customization**

1. **Custom CSS**
   - Go to **Appearance → Customize → Additional CSS**
   - Add your custom styles

2. **Child Theme** (for major customizations)
   ```
   /wp-content/themes/nexus-child/
   ├── style.css
   └── functions.php
   ```
   
   **style.css:**
   ```css
   /*
   Theme Name: Nexus Child
   Template: nexus-theme
   */
   ```
   
   **functions.php:**
   ```php
   <?php
   function nexus_child_enqueue() {
       wp_enqueue_style('nexus-parent', get_template_directory_uri() . '/style.css');
   }
   add_action('wp_enqueue_scripts', 'nexus_child_enqueue');
   ```

---

## 🔐 License Activation (Pro/Advanced/Agency)

### **For Premium Features**

1. **Purchase License**
   - Visit: https://jdsandigitel.com (when available)
   - Choose tier: Pro ($199), Advanced ($299), or Agency ($599)
   - Complete purchase
   - Receive license key via email

2. **Activate License**
   - Go to **Appearance → License**
   - Enter your license key
   - Enter your email (used for purchase)
   - Click **"Activate License"**
   - Should show: "License activated successfully"

3. **Verify Features**
   - Premium features now unlocked based on tier
   - Check available features in theme settings

### **License Tiers**

**Free** ($0):
- ✅ Basic WordPress theme features
- ✅ Custom post types (Products, Projects, Downloads)
- ✅ Gutenberg block patterns
- ✅ Responsive design
- ✅ Automatic updates

**Pro** ($199/year):
- ✅ Everything in Free
- ✅ DigitalOcean Spaces cloud storage
- ✅ Razorpay + PayPal payment gateways
- ✅ Template cloud sync with auto-backup
- ✅ Credit system with topup tiers
- ✅ Priority email support

**Advanced** ($299/year):
- ✅ Everything in Pro
- ✅ Visual theme builder
- ✅ AI template generator
- ✅ Advanced SEO tools
- ✅ Form builder
- ✅ Loop query builder
- ✅ Product filtering

**Agency** ($599/year):
- ✅ Everything in Advanced
- ✅ White label branding
- ✅ A/B testing
- ✅ Advanced analytics
- ✅ Client portal
- ✅ Unlimited sites

---

## 🔄 Updating the Theme

### **Automatic Updates (Recommended)**

1. **Check for Updates**
   - Go to **Dashboard → Updates**
   - Or **Appearance → Themes**
   - Update notification appears when available

2. **One-Click Update**
   - Click **"Update Now"** button
   - Wait for update to complete (30-60 seconds)
   - Refresh page to verify new version

3. **Verify Update**
   - Go to **Appearance → Updates**
   - Should show latest version number
   - Check **Changelog** for what's new

### **Manual Updates**

1. **Backup First**
   - Backup database (via phpMyAdmin or plugin)
   - Backup files (via FTP or cPanel)
   - Backup theme settings (export from Customizer)

2. **Download Latest Version**
   - Go to: https://github.com/jdram82/nexus/releases/latest
   - Download ZIP file

3. **Delete Old Theme**
   - Switch to another theme first (e.g., Twenty Twenty-Five)
   - Go to **Appearance → Themes**
   - Find Nexus, click **"Theme Details"**
   - Click **"Delete"**

4. **Install New Version**
   - **Appearance → Themes → Add New → Upload**
   - Choose downloaded ZIP
   - Install and activate

5. **Restore Settings**
   - Reactivate license if using Pro/Advanced/Agency
   - Reconfigure any custom settings

---

## 🛠️ Troubleshooting

### **Common Issues**

**❌ Updates Not Showing**
- Check GitHub token is configured
- Go to **Appearance → Update Diagnostic**
- Click **"Clear Cache & Force Update Check"**
- Verify GitHub API accessible

**❌ Theme Broken After Update**
- Activate default theme (Twenty Twenty-Five)
- Re-download theme ZIP from GitHub
- Delete broken theme folder via FTP
- Reinstall fresh copy

**❌ License Activation Fails**
- Verify license key is correct
- Check email matches purchase email
- Ensure site has internet connection
- Contact support if issue persists

**❌ Missing Features**
- Verify license is active (**Appearance → License**)
- Check tier includes feature you're looking for
- Try deactivating and reactivating license

---

## 🔧 Server Configuration

### **Recommended PHP Settings**

```ini
upload_max_filesize = 64M
post_max_size = 64M
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
```

### **Required PHP Extensions**
- ✅ mysqli or pdo_mysql
- ✅ curl
- ✅ json
- ✅ mbstring
- ✅ zip
- ✅ gd or imagick

### **.htaccess Optimization**

```apache
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress

# Enable GZIP compression
<IfModule mod_deflate.c>
AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# Browser caching
<IfModule mod_expires.c>
ExpiresActive On
ExpiresByType image/jpg "access plus 1 year"
ExpiresByType image/jpeg "access plus 1 year"
ExpiresByType image/gif "access plus 1 year"
ExpiresByType image/png "access plus 1 year"
ExpiresByType text/css "access plus 1 month"
ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

---

## 📊 Performance Optimization

### **1. Caching**
- Install caching plugin (WP Super Cache or W3 Total Cache)
- Enable object caching if using Pro tier
- Configure CDN (Cloudflare recommended)

### **2. Image Optimization**
- Install image optimization plugin (Smush or EWWW)
- Use WebP format when possible
- Lazy load images

### **3. Database Optimization**
- Use WP-Optimize plugin
- Schedule weekly database cleanup
- Remove post revisions regularly

### **4. Code Optimization**
- Minimize CSS/JS (built-in with Nexus Pro)
- Remove unused plugins
- Keep WordPress and plugins updated

---

## 🔒 Security Best Practices

1. **Keep Everything Updated**
   - WordPress core
   - Nexus theme (automatic)
   - All plugins
   - PHP version

2. **Use Strong Passwords**
   - 16+ characters
   - Mix of letters, numbers, symbols
   - Unique password for WP admin

3. **Install Security Plugin**
   - Wordfence Security (recommended)
   - iThemes Security
   - Sucuri Security

4. **Enable 2FA**
   - Two-Factor plugin
   - Google Authenticator integration

5. **Regular Backups**
   - UpdraftPlus (recommended)
   - Daily automatic backups
   - Store off-site (Google Drive, Dropbox)

6. **Limit Login Attempts**
   - Limit Login Attempts Reloaded
   - Blocks brute force attacks

---

## 🌍 Going Live Checklist

### **Pre-Launch**
- [ ] Test all pages and links
- [ ] Verify mobile responsiveness
- [ ] Test contact forms
- [ ] Check page speed (GTmetrix/PageSpeed Insights)
- [ ] Verify SSL certificate working
- [ ] Test all menu items
- [ ] Proofread all content
- [ ] Set up Google Analytics
- [ ] Configure SEO plugin (Yoast/Rank Math)
- [ ] Create XML sitemap
- [ ] Submit to Google Search Console
- [ ] Test checkout process (if eCommerce)

### **Launch Day**
- [ ] Final backup
- [ ] Switch DNS to production domain
- [ ] Verify site loads correctly
- [ ] Test email functionality
- [ ] Monitor error logs
- [ ] Check broken links (Broken Link Checker plugin)

### **Post-Launch**
- [ ] Monitor uptime (UptimeRobot)
- [ ] Set up automatic backups
- [ ] Enable security monitoring
- [ ] Configure CDN
- [ ] Submit to search engines

---

## 📞 Support & Resources

### **Documentation**
- Theme Updates: [docs/THEME_UPDATE_GUIDE.md](docs/THEME_UPDATE_GUIDE.md)
- License Setup: [docs/LICENSE_SERVER_SETUP.md](docs/LICENSE_SERVER_SETUP.md)
- License Protection: [docs/LICENSE_PROTECTION_GUIDE.md](docs/LICENSE_PROTECTION_GUIDE.md)

### **Diagnostic Tools**
- **Appearance → Update Diagnostic** - Check update system
- **Appearance → Theme Debug** - View theme information
- Standalone diagnostic: Upload `nexus-update-test.php` to root

### **Quick Fixes**
- [FIX_UPDATES_NOT_SHOWING.md](FIX_UPDATES_NOT_SHOWING.md)
- [UPDATE_QUICK_REFERENCE.md](UPDATE_QUICK_REFERENCE.md)

### **Community**
- GitHub Issues: https://github.com/jdram82/nexus/issues
- Website: https://jdsandigitel.com (coming soon)

### **Professional Support**
- Email: support@jdsandigitel.com
- Priority support for Pro/Advanced/Agency license holders

---

## 🎓 Video Tutorials (Coming Soon)

- Installation walkthrough
- License activation
- Theme customization basics
- Setting up Pro features
- Building with theme builder (Advanced tier)
- White labeling for clients (Agency tier)

---

*Last updated: December 27, 2025*  
*Nexus Theme by Jdsan Digitel*
