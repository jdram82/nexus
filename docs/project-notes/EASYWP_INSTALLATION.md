# EasyWP Installation Guide - Nexus Theme

**Step-by-step guide to install Nexus on EasyWP (Namecheap's managed WordPress hosting)**

---

## 📦 Download the Theme ZIP

### Option 1: Download from Your Codespace (Recommended)

The theme is already packaged as a ZIP file. Download it from your current location:

**File Location**: `/workspaces/codespaces-blank/nexus-theme.zip`  
**File Size**: ~260 KB  

**To Download:**
1. In VS Code, locate `nexus-theme.zip` in the file explorer
2. Right-click the file
3. Select **"Download"**
4. Save to your computer (remember the location!)

### Option 2: Download from GitHub

1. Go to https://github.com/jdram82/nexus
2. Click the green **"Code"** button
3. Select **"Download ZIP"**
4. Extract the ZIP file
5. Rename the folder from `nexus-main` to `nexus-theme`
6. Re-zip the folder as `nexus-theme.zip`

---

## 🚀 Install on EasyWP

### Step 1: Log in to EasyWP Dashboard

1. Go to https://easywp.com or log in via Namecheap account
2. Navigate to **"My Websites"**
3. Click on your website to open the site dashboard
4. You'll see the EasyWP control panel

### Step 2: Access WordPress Admin

**Option A: Via EasyWP Dashboard**
1. Click **"Manage WordPress"** button
2. This automatically logs you into WordPress admin
3. Skip username/password entry

**Option B: Direct Login**
1. Go to `yoursite.easywp.com/wp-admin`
2. Enter your WordPress username and password
3. Click **"Log In"**

### Step 3: Upload Nexus Theme

1. In WordPress Admin, go to **Appearance → Themes**
2. Click **"Add New"** button at the top
3. Click **"Upload Theme"** button
4. Click **"Choose File"**
5. Select `nexus-theme.zip` from your computer
6. Click **"Install Now"**

**Wait for upload** (30-60 seconds depending on connection)

### Step 4: Activate Theme

1. After installation completes, you'll see success message
2. Click **"Activate"** button
3. WordPress will switch to Nexus theme
4. You'll see confirmation: **"New theme activated. View site"**

---

## ⚙️ Initial Setup (CRITICAL)

### Step 5: Run Database Setup

**Important**: Nexus requires database tables for Pro features

1. After activation, you'll see a notice at the top:
   ```
   ⚠️ Nexus Pro features require database setup
   ```

2. Click **"Run Setup Now"** button in the notice

3. Wait 5-10 seconds - page will show:
   ```
   ✅ Database tables created successfully
   ```

4. Page will auto-refresh

**If you don't see the notice:**
1. Go to **Nexus Pro → Dashboard** in the admin menu
2. Look for **"Activate Pro Features"** button
3. Click it to run database setup

---

## 🔧 Configure Permalinks (REQUIRED)

**Why**: Nexus uses REST API and custom post types that need pretty URLs

### Steps:

1. Go to **Settings → Permalinks**
2. Select **"Post name"** option (recommended)
   - Or use **"Custom Structure"**: `/%postname%/`
3. Click **"Save Changes"** button
4. You'll see: "Permalink structure updated"

### Verify Permalinks Work:

1. Open new browser tab
2. Go to: `yoursite.easywp.com/wp-json/nexus/v1/health`
3. You should see JSON response like:
   ```json
   {
     "status": "ok",
     "version": "1.6.1",
     "php_version": "8.0",
     "wordpress_version": "6.4"
   }
   ```

**If you see 404 error** → Permalinks not working, try:
- Save permalinks again
- Contact EasyWP support (they may need to enable mod_rewrite)

---

## 🎨 Customize Your Theme

### Step 6: Basic Customization

1. Go to **Appearance → Customize**
2. You'll see Nexus customizer sections:

**Theme Colors:**
- Primary Color: `#667eea` (purple) - change to your brand color
- Secondary Color: `#4ecdc4` (teal)
- Click color picker to choose new colors

**Typography:**
- Heading Font: Choose from dropdown
- Body Font: Choose from dropdown
- Base Font Size: 16px (adjust if needed)

**Layout:**
- Container Width: 1200px (standard)
- Sidebar Position: Right/Left/None

**Header Settings:**
- Upload logo (200x50px PNG recommended)
- Set menu position
- Configure top bar

3. Click **"Publish"** to save all changes

---

## 📝 Create Sample Content

### Step 7: Test Custom Post Types

**Create a Project:**
1. Go to **Projects → Add New**
2. Title: "Sample Project"
3. Content: Add project description
4. In **Project Details** meta box:
   - Client Name: "Acme Corp"
   - Project Date: Today
   - Project URL: https://example.com
5. Set Featured Image (click "Set featured image")
6. Upload an image or select from media library
7. Click **"Publish"**

**Create a Product:**
1. Go to **Products → Add New**
2. Title: "Premium WordPress Plugin"
3. Content: Product description
4. In **Product Details** meta box:
   - Price: $99
   - Version: 1.0.0
   - Product URL: https://example.com
5. Set Featured Image
6. Click **"Publish"**

**View Your Content:**
- Visit `yoursite.easywp.com/projects/` to see projects archive
- Visit `yoursite.easywp.com/products/` to see products archive

---

## 🔐 Enable Pro Features (Optional Testing)

### Step 8: Activate Pro Tier

**For testing Pro features**, you can manually activate the tier:

1. Log in to your EasyWP **phpMyAdmin**:
   - EasyWP Dashboard → **"Database"** section
   - Click **"Open phpMyAdmin"**
   - Select your database (usually named after your site)

2. Click **"SQL"** tab at the top

3. Run this query:
   ```sql
   INSERT INTO wp_options (option_name, option_value, autoload) 
   VALUES ('nexus_license_tier', 'pro', 'yes')
   ON DUPLICATE KEY UPDATE option_value = 'pro';
   ```

4. Click **"Go"** button

5. Return to WordPress Admin and refresh

6. New menu items appear:
   - **Nexus Options → Template Library**
   - **Nexus Options → AI Credits**
   - **Nexus Options → Payment Gateways**

**To activate Advanced tier** (for testing):
```sql
UPDATE wp_options SET option_value = 'advanced' WHERE option_name = 'nexus_license_tier';
```

**To activate Agency tier** (for testing):
```sql
UPDATE wp_options SET option_value = 'agency' WHERE option_name = 'nexus_license_tier';
```

---

## 💳 Configure Payment Gateway (If Using Credits)

### Step 9: Set Up Test Payments

**Only needed if you want to test credit purchases**

1. Go to **Nexus Options → Payment Gateways**

2. Select **Razorpay** (recommended for India):
   - Key ID: `rzp_test_1DP5mmOlF5G5ag`
   - Key Secret: `thisissecret`

3. Or select **Stripe** (for international):
   - Publishable Key: `pk_test_...` (get from Stripe dashboard)
   - Secret Key: `sk_test_...` (get from Stripe dashboard)

4. Click **"Save Settings"**

**Test Credit Purchase:**
1. Go to **Nexus Options → AI Credits**
2. Click **"Buy 100 Credits"** ($10)
3. Use test card:
   - Razorpay: `4111 1111 1111 1111`
   - Stripe: `4242 4242 4242 4242`
   - CVV: `123`
   - Expiry: Any future date
4. Complete payment
5. Credits should be added

---

## 🛠️ EasyWP-Specific Settings

### Performance Optimization

**EasyWP has built-in caching**, but you can optimize:

1. **Enable Lazy Loading** (if not already on):
   - EasyWP Dashboard → **Performance**
   - Enable "Lazy Load Images"

2. **CDN** (already included with EasyWP):
   - Automatic CDN for static files
   - No configuration needed

3. **PHP Version** (check you're on PHP 8.0+):
   - EasyWP Dashboard → **Settings**
   - Under "PHP Version", select **8.0 or 8.1**
   - Click **"Save"**

### Security Settings

1. **SSL Certificate** (should be auto-enabled):
   - EasyWP automatically provides free SSL
   - Verify your site uses `https://`

2. **Automatic Backups**:
   - EasyWP creates automatic daily backups
   - No configuration needed
   - Access via: EasyWP Dashboard → **Backups**

---

## ✅ Verify Installation

### Checklist:

- [ ] Theme activated successfully
- [ ] Database tables created (no errors)
- [ ] Permalinks set to "Post name"
- [ ] REST API working (`/wp-json/nexus/v1/health`)
- [ ] Customizer opens without errors
- [ ] Sample project created and viewable
- [ ] Sample product created and viewable
- [ ] Menu appears on frontend
- [ ] Logo uploaded (if desired)
- [ ] Mobile view works (test on phone)

**Test Your Site:**
1. Open `yoursite.easywp.com` in browser
2. Check homepage loads
3. Navigate to Projects page
4. Navigate to Products page
5. View on mobile device
6. Test contact form (if added)

---

## 🐛 Troubleshooting

### Theme Not Appearing After Upload

**Problem**: Can't find Nexus in themes list

**Solution**:
1. Check ZIP file is named `nexus-theme.zip`
2. Verify ZIP contains `style.css` in root
3. Try re-uploading
4. Check EasyWP file size limits (usually 10MB max)

### "Upload Exceeds Maximum Size" Error

**Problem**: ZIP file too large for upload

**Solution**:
```
EasyWP default limit: 10MB
Nexus ZIP size: ~260KB ✅ Should work!
```

If still getting error:
1. Contact EasyWP support to increase limit
2. Or use FTP method (see below)

### Database Tables Not Created

**Problem**: "Failed to create tables" error

**Solution**:
1. Check MySQL user has CREATE TABLE permission
2. EasyWP should allow this by default
3. Try running setup again from Nexus Pro → Dashboard
4. Contact EasyWP support if persists

### Permalinks Not Working (404 Errors)

**Problem**: Custom post types show 404

**Solution**:
1. Go to **Settings → Permalinks**
2. Click **"Save Changes"** (this flushes rewrite rules)
3. Try accessing pages again
4. If still 404, contact EasyWP support about mod_rewrite

### Payment Gateway Not Loading

**Problem**: Payment modal doesn't open

**Solution**:
1. Check browser console for errors (F12)
2. Verify SSL is enabled (`https://`)
3. Test with different browser
4. Clear browser cache
5. Verify gateway credentials are correct

---

## 🔄 Alternative: FTP Installation

If ZIP upload fails, use FTP:

### Step 1: Get FTP Credentials

1. EasyWP Dashboard → **"FTP/SFTP"** section
2. Note down:
   - Host: `ftp.yourdomain.easywp.com`
   - Username: `your-username`
   - Password: `your-password`
   - Port: `21` (FTP) or `22` (SFTP)

### Step 2: Connect via FTP

Use FileZilla or similar:
1. Open FileZilla
2. Host: Enter your FTP host
3. Username: Enter FTP username
4. Password: Enter FTP password
5. Port: 21
6. Click **"Quickconnect"**

### Step 3: Upload Theme

1. Navigate to: `/wp-content/themes/`
2. Extract `nexus-theme.zip` on your computer
3. Upload entire `nexus-theme` folder to themes directory
4. Wait for upload to complete
5. Go to WordPress Admin → Appearance → Themes
6. Activate Nexus

---

## 📞 Getting Help

### EasyWP Support
- **Live Chat**: Available in EasyWP dashboard
- **Support Tickets**: Submit via Namecheap account
- **Knowledge Base**: https://www.namecheap.com/support/knowledgebase/category/10245/easywp/

### Theme Support
- **Documentation**: Check `/docs/` folder in theme
- **Testing Guide**: `TESTING_READINESS.md`
- **Integration Guide**: `WORDPRESS_INTEGRATION.md`
- **GitHub Issues**: https://github.com/jdram82/nexus/issues

### Common EasyWP Contacts
- **Sales**: For plan upgrades
- **Technical**: For server issues
- **Billing**: For payment questions

---

## 🎯 Next Steps

After successful installation:

1. **Customize Theme**:
   - Upload logo
   - Set brand colors
   - Configure typography

2. **Add Content**:
   - Create pages (About, Contact, etc.)
   - Add blog posts
   - Create projects/products

3. **Configure Menus**:
   - Appearance → Menus
   - Create navigation menu
   - Assign to Primary location

4. **Install Essential Plugins** (optional):
   - Contact Form 7 (forms)
   - Yoast SEO (SEO)
   - WooCommerce (if selling products)

5. **Test All Features**:
   - Follow `TESTING_READINESS.md`
   - Report any issues found

---

## 💡 EasyWP-Specific Tips

### Advantages of EasyWP:
✅ Automatic daily backups
✅ Free SSL certificates
✅ Built-in CDN
✅ WordPress auto-updates
✅ Malware scanning
✅ 24/7 support

### Known Limitations:
⚠️ No SSH access (can't run command line)
⚠️ Limited plugin installation (some may be blocked)
⚠️ Can't modify server config (php.ini, .htaccess limited)
⚠️ Staging environment only on higher plans

### Performance on EasyWP:
- **Starter Plan**: Good for 25K visitors/month
- **Turbo Plan**: Good for 100K visitors/month
- **Supersonic Plan**: Good for 200K+ visitors/month

Nexus is optimized and should run smoothly on all EasyWP plans.

---

## 🔒 Security Best Practices

1. **Strong Admin Password**:
   - Use 16+ characters
   - Mix letters, numbers, symbols

2. **Two-Factor Authentication**:
   - Install "Two Factor Authentication" plugin
   - Enable for admin account

3. **Regular Updates**:
   - Keep WordPress updated (auto-updates enabled on EasyWP)
   - Update plugins regularly
   - Theme updates via GitHub

4. **Limit Login Attempts**:
   - Install "Limit Login Attempts Reloaded"
   - Protects against brute force attacks

---

## 📊 Monitoring Your Site

**Via EasyWP Dashboard:**
- **Visitor Stats**: View traffic analytics
- **Resource Usage**: CPU/RAM consumption
- **Uptime**: Automatic monitoring
- **Backups**: Daily automatic backups

**Via WordPress:**
- **Nexus Analytics** (Agency tier): Custom event tracking
- **Google Analytics**: Install GA plugin for detailed analytics

---

## ✨ Summary

**Total Installation Time**: 10-15 minutes

**Steps Completed**:
1. ✅ Downloaded theme ZIP
2. ✅ Uploaded to EasyWP WordPress
3. ✅ Activated theme
4. ✅ Ran database setup
5. ✅ Configured permalinks
6. ✅ Customized appearance
7. ✅ Created sample content

**You're now ready to build with Nexus!** 🎉

For detailed testing procedures, see `TESTING_READINESS.md`

---

**Last Updated**: December 27, 2025  
**Version**: 1.6.1  
**Platform**: EasyWP (Namecheap)
