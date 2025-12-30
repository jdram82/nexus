# Installation Instructions for jdsandigitel.com

## Quick Install (5 minutes)

### Step 1: Create ZIP File

On your local computer:

```bash
# Navigate to the plugin folder
cd /workspaces/codespaces-blank/nexus-theme

# Create a ZIP file
zip -r nexus-license-api-plugin.zip nexus-license-api-plugin/
```

### Step 2: Upload to WordPress

1. **Login to jdsandigitel.com WordPress Admin**
   - Go to: https://jdsandigitel.com/wp-admin

2. **Install Plugin**
   - Go to: **Plugins → Add New**
   - Click: **Upload Plugin** button
   - Click: **Choose File**
   - Select: `nexus-license-api-plugin.zip`
   - Click: **Install Now**
   - Click: **Activate Plugin**

### Step 3: Verify Installation

Open in browser:
```
https://jdsandigitel.com/wp-json/nexus-licenses/v1/info
```

**Expected Response (JSON):**
```json
{
  "name": "Nexus License API",
  "version": "1.0.0",
  "server": "https://jdsandigitel.com",
  "status": "active",
  "endpoints": {...}
}
```

**If REST API is blocked**, try legacy endpoint:
```
https://jdsandigitel.com/?nexus_api_action=info
```

✅ **Success!** Your license server is now active!

---

## Alternative: FTP/SSH Installation

If you have FTP or SSH access:

### Via FTP:
1. Upload the entire `nexus-license-api-plugin` folder to:
   ```
   /wp-content/plugins/nexus-license-api-plugin/
   ```

2. Go to WordPress Admin → Plugins
3. Find "Nexus License API"
4. Click "Activate"

### Via SSH:
```bash
# SSH into your server
ssh user@jdsandigitel.com

# Navigate to plugins directory
cd /path/to/wordpress/wp-content/plugins/

# Download or upload the plugin folder
# Then set permissions
chmod -R 755 nexus-license-api-plugin/

# Activate via WP-CLI (if available)
wp plugin activate nexus-license-api
```

---

## Testing Your Installation

### Test 1: API Info
```bash
curl https://jdsandigitel.com/wp-json/nexus-licenses/v1/info
```

### Test 2: Create Test License

1. Go to **License Manager → Add License**
2. Fill in:
   - License Key: (auto-generated)
   - Product: Nexus Pro
   - Max Domains: 1
   - Expiry: 1 year from now
3. Click **Create**
4. Copy the license key

### Test 3: Activate Test License
```bash
curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/activate \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "NEXUS-YOUR-KEY-HERE",
    "site_url": "https://testsite.com"
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "License activated successfully",
  "tier": "pro",
  "expires": "2026-12-29",
  "max_domains": 1,
  "active_domains": 1
}
```

✅ **Perfect!** Your license server is working!

---

## Next Steps

1. ✅ **Create WooCommerce Products**
   - Pro: $199/year
   - Advanced: $299/year
   - Agency: $599/year

2. ✅ **Link Products to License Manager**
   - Enable "Create License" for each product
   - Set SKU: `nexus-pro`, `nexus-advanced`, `nexus-agency`
   - Set tier in product metadata

3. ✅ **Setup Payment Gateways**
   - Stripe (recommended)
   - PayPal
   - Razorpay (for India)

4. ✅ **Test Purchase Flow**
   - Buy a product in test mode
   - Check license is auto-created
   - Test activation from customer site

---

## Troubleshooting

**Plugin doesn't appear in Plugins list:**
- Check folder name is exactly: `nexus-license-api-plugin`
- Check file exists: `nexus-license-api-plugin/nexus-license-api.php`
- Check file permissions: `chmod 755`

**API returns blank page:**
- Try legacy endpoint: `?nexus_api_action=info`
- Check WordPress debug log
- Contact hosting support about REST API

**License activation fails:**
- Verify Software License Manager is active
- Check database tables exist: `wp_lic_key_tbl`
- Test license exists in License Manager

---

## Support

Need help? Check the README.md or contact support.
