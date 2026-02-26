# 🚀 COMPLETE SETUP CHECKLIST - jdsandigitel.com License Server

**Date:** December 29, 2025  
**Website:** jdsandigitel.com  
**Plugin Created:** ✅ nexus-license-api-plugin.zip

---

## ✅ STEP 1: Install the License API Plugin (5 minutes)

### Upload to WordPress

1. **Download the ZIP file:**
   - Location: `/workspaces/codespaces-blank/nexus-theme/nexus-license-api-plugin.zip`
   - Download this file to your computer

2. **Install on WordPress:**
   - Login to: https://jdsandigitel.com/wp-admin
   - Go to: **Plugins → Add New**
   - Click: **Upload Plugin**
   - Choose: `nexus-license-api-plugin.zip`
   - Click: **Install Now**
   - Click: **Activate Plugin**

3. **Verify Installation:**
   - Open browser: https://jdsandigitel.com/wp-json/nexus-licenses/v1/info
   - **If REST API is blocked**, try: https://jdsandigitel.com/?nexus_api_action=info
   - You should see JSON response with "Nexus License API"

---

## ✅ STEP 2: Create WooCommerce Products (30 minutes)

### Product 1: Nexus Pro

1. **Go to:** Products → Add New
2. **Fill in:**
   - Product Name: `Nexus Pro Theme License`
   - Regular Price: `199`
   - SKU: `nexus-pro`
   - Product Type: `Simple product`
   - Virtual: ✅ Check
   - Downloadable: ✅ Check

3. **Product Description:**
   ```
   Unlock premium features for Nexus theme:
   - DigitalOcean Spaces cloud storage
   - Razorpay & PayPal payment gateways
   - Template cloud sync with auto-backup
   - Credit system with topup tiers
   - Priority email support
   - 1 year of updates
   
   License valid for 1 site. Renews annually.
   ```

4. **Downloadable Files:**
   - File Name: `Nexus Theme Latest`
   - File URL: `https://github.com/jdram82/nexus/archive/refs/heads/main.zip`

5. **Software License Manager Settings:**
   - Create License: ✅ Yes
   - License Type: `pro`
   - Max Domains: `1`
   - Validity: `365` days

6. **Click:** Publish

### Product 2: Nexus Advanced

Repeat above with:
- Price: `299`
- SKU: `nexus-advanced`
- License Type: `advanced`
- Max Domains: `3`
- Description: Add AI, SEO, Theme Builder, Forms

### Product 3: Nexus Agency

Repeat above with:
- Price: `599`
- SKU: `nexus-agency`
- License Type: `agency`
- Max Domains: `999`
- Description: Add White Label, A/B Testing, Analytics

### Product 4: Nexus Free (Optional)

- Price: `0`
- SKU: `nexus-free`
- License Type: `free`
- Max Domains: `1`
- No license creation needed

---

## ✅ STEP 3: Setup Payment Gateways (30 minutes)

### Stripe (Recommended)

1. **Create Stripe Account:**
   - Go to: https://stripe.com
   - Sign up for free account
   - Complete verification

2. **Get API Keys:**
   - Dashboard → Developers → API Keys
   - Copy: **Publishable key** and **Secret key**
   - Use **Test mode** keys first!

3. **Install in WooCommerce:**
   - Go to: **WooCommerce → Settings → Payments**
   - Enable: **Stripe**
   - Configure:
     - Test Publishable Key: `pk_test_...`
     - Test Secret Key: `sk_test_...`
   - Save changes

### PayPal (Optional)

1. **Create PayPal Business Account:**
   - Go to: https://www.paypal.com/business
   - Sign up or login

2. **Get API Credentials:**
   - Dashboard → Account Settings → API Access
   - Generate API credentials

3. **Configure in WooCommerce:**
   - **WooCommerce → Settings → Payments → PayPal**
   - Add API credentials
   - Enable

### Razorpay (For India - Optional)

1. **Install Plugin:**
   - **Plugins → Add New**
   - Search: "Razorpay for WooCommerce"
   - Install & Activate

2. **Configure:**
   - Get keys from: https://dashboard.razorpay.com
   - **WooCommerce → Settings → Payments → Razorpay**
   - Add keys

---

## ✅ STEP 4: Test Everything (30 minutes)

### Test 1: API Endpoints

Open terminal and run:
```bash
# Test info endpoint
curl https://jdsandigitel.com/wp-json/nexus-licenses/v1/info

# OR if REST API is blocked:
curl https://jdsandigitel.com/?nexus_api_action=info
```

Expected: JSON response with endpoints

### Test 2: Create Manual Test License

1. **Go to:** License Manager → Add License
2. **Fill in:**
   - Product: Nexus Pro
   - Max Domains: 1
   - Expiry: 365 days
3. **Click:** Create
4. **Copy:** The generated license key (NEXUS-XXXX-XXXX)

### Test 3: Test License Activation

```bash
curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/activate \
  -H "Content-Type: application/json" \
  -d '{"license_key":"NEXUS-YOUR-KEY-HERE","site_url":"https://testsite.com"}'
```

Expected response:
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

### Test 4: Test Purchase Flow

1. **Enable Stripe Test Mode**
2. **Go to your shop:** https://jdsandigitel.com/shop
3. **Add Nexus Pro to cart**
4. **Checkout with test card:**
   - Card: `4242 4242 4242 4242`
   - Expiry: Any future date
   - CVC: Any 3 digits
5. **Complete purchase**
6. **Check:**
   - Order created
   - License generated automatically
   - Email sent with license key

### Test 5: Test from Customer Site

1. **Install Nexus theme on test WordPress site**
2. **Go to:** Dashboard → Nexus → License
3. **Enter license key** from test purchase
4. **Click:** Activate License
5. **Verify:**
   - Green "Pro Active" badge shows
   - Pro features unlock
   - No errors

---

## ✅ STEP 5: Update Nexus Theme (10 minutes)

Update the theme to point to your license server:

1. **Edit:** `inc/class-nexus-license-manager.php`
2. **Find line ~24:**
   ```php
   private $license_server = 'https://example.com/wp-json/nexus-licenses/v1/';
   ```
3. **Change to:**
   ```php
   private $license_server = 'https://jdsandigitel.com/wp-json/nexus-licenses/v1/';
   ```
4. **Commit and push to GitHub**

---

## ✅ STEP 6: Go Live! (15 minutes)

### Switch to Live Mode

1. **Stripe:**
   - Get **Live** API keys from Stripe
   - WooCommerce → Settings → Payments → Stripe
   - Replace with live keys
   - Disable test mode

2. **PayPal:**
   - Switch to live credentials

3. **Test one real purchase** (you can refund it after)

### Create Marketing Pages

1. **Home Page:** Landing page for Nexus
2. **Features Page:** Tier comparison table
3. **Pricing Page:** Link to products
4. **Documentation:** Setup guides
5. **Support Page:** Contact form

### Announce!

- Tweet about launch
- Post on WordPress forums
- Email newsletter
- Submit free version to WordPress.org

---

## 📊 Success Metrics

**Week 1:**
- [ ] 5+ test purchases completed
- [ ] All license activations working
- [ ] No customer support issues

**Month 1:**
- [ ] 10+ real customers
- [ ] $1,000+ revenue
- [ ] Positive reviews

**Year 1 Goal:**
- [ ] 100+ customers
- [ ] $30,000+ revenue
- [ ] 4.5+ star rating

---

## 🆘 Troubleshooting

### API Returns 404
- Check plugin is activated
- Try legacy endpoint: `?nexus_api_action=info`
- Check WordPress debug log

### License Not Created After Purchase
- Verify Software License Manager is active
- Check product has "Create License" enabled
- Order must be "Processing" or "Completed" status
- Check Email notifications are working

### Payment Fails
- Verify using LIVE keys (not test)
- Check SSL certificate is valid
- Test different payment method
- Check WooCommerce error logs

### Features Don't Unlock
- Verify license tier matches feature requirement
- Check license not expired
- Try reactivating license
- Clear WordPress cache

---

## 📞 Files Created

1. ✅ `nexus-license-api-plugin.zip` - Main plugin (upload to WordPress)
2. ✅ `nexus-license-api-plugin/README.md` - Documentation
3. ✅ `nexus-license-api-plugin/INSTALLATION.md` - Install guide
4. ✅ `test-license-api.sh` - Test script

---

## 🎯 Your Next Actions

**Right Now:**
1. Download `nexus-license-api-plugin.zip`
2. Upload to jdsandigitel.com WordPress
3. Activate plugin
4. Test API endpoint

**Today:**
1. Create 3 WooCommerce products (Pro, Advanced, Agency)
2. Setup Stripe test mode
3. Test purchase flow

**This Week:**
1. Switch to live payment keys
2. Create marketing pages
3. Make first real sale!

---

**Ready to launch!** 🚀

Let me know if you need help with any step!
