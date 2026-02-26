# 💳 PayPal Setup & Testing Guide for jdsandigitel.com

**Status:** ✅ Nexus License API Plugin Activated  
**Next Step:** Setup PayPal for testing

---

## 🚀 PayPal Setup (15 minutes)

### Step 1: Create PayPal Sandbox Account (5 minutes)

1. **Go to PayPal Developer:**
   - Visit: https://developer.paypal.com
   - Login with your PayPal account (or create one)

2. **Access Sandbox:**
   - Click: **Dashboard** (top menu)
   - Click: **Sandbox** → **Accounts**

3. **You'll see 2 test accounts created automatically:**
   - **Business Account** (Merchant - this is YOU)
   - **Personal Account** (Buyer - this is TEST CUSTOMER)

4. **Get Business Account Credentials:**
   - Find the **Business** account
   - Click: **⋯ (three dots)** → **View/Edit Account**
   - Note down:
     - Email: `something-business@business.example.com`
     - Password: (shown in the modal)
   - Click: **API Credentials** tab
   - Copy: **Client ID** and **Secret**

### Step 2: Install PayPal in WooCommerce (5 minutes)

#### Option A: PayPal Standard (Easiest - FREE)

1. **Go to WordPress Admin:**
   - **WooCommerce → Settings → Payments**

2. **Enable PayPal Standard:**
   - Find: **PayPal Standard**
   - Toggle: **Enable** ✅
   - Click: **Manage**

3. **Configure Settings:**
   - **Enable PayPal Sandbox:** ✅ Yes (for testing)
   - **Sandbox Email:** Your test business account email
   - **PayPal Email:** Your real PayPal email (for live later)
   - **IPN Email Notifications:** ✅ Enable
   - **Receiver Email:** Same as PayPal email
   - **Invoice Prefix:** `NEXUS-`
   - **Payment Action:** Sale

4. **Save Changes**

#### Option B: PayPal Checkout (Recommended - Better UX)

1. **Install Plugin:**
   - **Plugins → Add New**
   - Search: **"WooCommerce PayPal Checkout Payment Gateway"**
   - Install & Activate (Official WooCommerce plugin)

2. **Configure:**
   - **WooCommerce → Settings → Payments**
   - Find: **PayPal Checkout**
   - Toggle: **Enable** ✅
   - Click: **Manage**

3. **Add Sandbox Credentials:**
   - **Enable Sandbox Mode:** ✅ Yes
   - **Sandbox Client ID:** (paste from step 1)
   - **Sandbox Secret:** (paste from step 1)
   - **Save Changes**

### Step 3: Create Test Products (10 minutes)

#### Product 1: Nexus Pro - Test Version

1. **Products → Add New**
2. **Product Name:** Nexus Pro License
3. **Regular Price:** 199
4. **Product Data:**
   - Type: Simple product
   - Virtual: ✅
   - Downloadable: ✅
5. **SKU:** `nexus-pro`
6. **Short Description:**
   ```
   Pro tier with cloud storage, payment gateways, and template sync.
   Valid for 1 site. Test purchase - will be refunded.
   ```
7. **Product Data → Downloadable Files:**
   - File name: Nexus Theme
   - File URL: `https://github.com/jdram82/nexus/archive/refs/heads/main.zip`

8. **Scroll down to Software License Manager:**
   - **Create License:** ✅ Yes
   - **License Type:** `pro`
   - **Max Allowed Domains:** `1`
   - **License Validity:** `365` days

9. **Publish**

---

## 🧪 Testing Purchase Flow with PayPal

### Test 1: Make a Test Purchase (5 minutes)

1. **Open Incognito/Private Browser Window**
   - This simulates a customer

2. **Go to Your Shop:**
   - Visit: https://jdsandigitel.com/shop
   - Or: https://jdsandigitel.com/product/nexus-pro-license

3. **Add to Cart:**
   - Click: **Add to Cart**
   - Click: **View Cart**
   - Click: **Proceed to Checkout**

4. **Fill Billing Details:**
   ```
   First Name: Test
   Last Name: Customer
   Email: your-email@example.com (use your real email to get license)
   Address: 123 Test Street
   City: Test City
   Postcode: 12345
   Phone: 1234567890
   ```

5. **Payment Method:**
   - Select: **PayPal**
   - Click: **Place Order**

6. **PayPal Sandbox Login:**
   - You'll be redirected to PayPal sandbox
   - Login with: **Personal** (buyer) test account
     - Email: `something-buyer@personal.example.com`
     - Password: (from developer dashboard)
   - Click: **Pay Now**

7. **Complete Purchase:**
   - You'll be redirected back to jdsandigitel.com
   - Order should be completed

### Test 2: Verify License Generated (2 minutes)

1. **Check WordPress Admin:**
   - **WooCommerce → Orders**
   - Open the order you just created
   - Status should be: **Processing** or **Completed**

2. **Check License Manager:**
   - **License Manager → Manage Licenses**
   - You should see a new license with prefix: `NEXUS-XXXX-XXXX`
   - Copy this license key

3. **Check Email:**
   - Check the email you entered at checkout
   - You should receive:
     - Order confirmation
     - License key

### Test 3: Test License Activation via API (2 minutes)

Open terminal and run:

```bash
# Replace NEXUS-XXXX with your actual license key
curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/activate \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "NEXUS-YOUR-KEY-HERE",
    "site_url": "https://mytestsite.com"
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

### Test 4: Test from Actual WordPress Site (10 minutes)

1. **Install Nexus Theme** on a test WordPress site
2. **Go to:** Dashboard → Nexus → License
3. **Enter License Key:** (from email or admin)
4. **Click:** Activate License
5. **Verify:**
   - ✅ Shows "Pro Active" badge
   - ✅ Pro features are unlocked
   - ✅ No error messages

---

## 📊 Verify Everything Works

### Checklist After First Test Purchase

- [ ] Order created in WooCommerce
- [ ] Order status is "Processing" or "Completed"
- [ ] License key generated automatically
- [ ] License visible in License Manager
- [ ] Email sent with license key
- [ ] API activation works
- [ ] License validation returns correct tier
- [ ] Features unlock in theme

---

## 🔧 Troubleshooting

### PayPal Returns Error

**Issue:** "We cannot process this transaction"
- **Fix:** Make sure you're using sandbox/test mode credentials
- **Fix:** Check sandbox is enabled in both PayPal and WooCommerce

### No License Generated

**Issue:** Order completed but no license
- **Fix:** Check Software License Manager is activated
- **Fix:** Verify product has "Create License" = Yes
- **Fix:** Order must be "Processing" or "Completed" status
- **Fix:** Check WordPress debug log: `wp-content/debug.log`

### IPN/Webhook Issues

**Issue:** Order stays in "Pending" status
- **Fix:** Enable IPN in PayPal settings
- **Fix:** WooCommerce → Settings → Payments → PayPal → Enable IPN
- **Fix:** Check webhook URL is accessible

### Payment Not Received

**Issue:** PayPal shows payment but WooCommerce doesn't
- **Fix:** Check email addresses match
- **Fix:** Verify receiver email is correct
- **Fix:** Check PayPal IPN history in PayPal dashboard

---

## 🎯 PayPal Test Accounts Reference

### From PayPal Developer Dashboard

Access: https://developer.paypal.com/dashboard/accounts

**Business Account (Merchant - YOU):**
- Purpose: Receives payments
- Email: `xxx-facilitator@business.example.com`
- Type: Business
- Use for: WooCommerce PayPal settings

**Personal Account (Buyer - TEST CUSTOMER):**
- Purpose: Makes test purchases
- Email: `xxx-buyer@personal.example.com`
- Type: Personal
- Use for: Testing checkout

---

## 💰 Going Live After Testing

Once testing is successful:

### Step 1: Get Live PayPal Credentials

1. **Go to:** https://developer.paypal.com
2. **Switch to:** Live (top right toggle)
3. **My Apps & Credentials**
4. **Create App** → Get Client ID & Secret

### Step 2: Update WooCommerce

1. **WooCommerce → Settings → Payments → PayPal**
2. **Disable Sandbox Mode:** ❌
3. **Replace with Live Credentials:**
   - Live Client ID
   - Live Secret
4. **Save Changes**

### Step 3: Test Small Real Purchase

- Make a small real purchase ($1 test product)
- Verify everything works
- Refund if needed

---

## 📝 Next Steps After PayPal Works

1. ✅ Create remaining products:
   - Nexus Advanced ($299)
   - Nexus Agency ($599)

2. ✅ Add Stripe as alternative payment method

3. ✅ Create marketing pages:
   - Features comparison
   - Pricing table
   - Documentation

4. ✅ Launch! 🚀

---

## 💡 Quick Commands

### Check License Server Status
```bash
curl https://jdsandigitel.com/wp-json/nexus-licenses/v1/info
```

### Test License Activation
```bash
curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/activate \
  -d "license_key=NEXUS-XXXX&site_url=https://testsite.com"
```

### Test License Validation
```bash
curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/validate \
  -d "license_key=NEXUS-XXXX&site_url=https://testsite.com"
```

---

**Your license server is ready!** Start with creating the Nexus Pro product and make your first test purchase with PayPal. 🎉
