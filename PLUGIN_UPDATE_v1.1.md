# 🎉 Nexus License API Plugin Update v1.1

**Updated:** December 30, 2025  
**New Feature:** Auto-create licenses on WooCommerce order completion!

---

## ✨ What's New

### Automatic License Generation

The plugin now **automatically creates licenses** when customers complete purchases! No more manual work!

**How it works:**
1. Customer buys Nexus Pro/Advanced/Agency
2. PayPal payment completes
3. WooCommerce order status → "Processing" or "Completed"
4. **License auto-created!** 🎉
5. Email sent to customer with license key
6. License stored in License Manager

---

## 🔄 How to Update

### Step 1: Deactivate Old Version

1. **Go to**: Plugins → Installed Plugins
2. **Find**: Nexus License API
3. **Click**: Deactivate

### Step 2: Delete Old Version

1. **Click**: Delete (after deactivating)
2. Confirm deletion

### Step 3: Install New Version

1. **Download**: `nexus-license-api-plugin.zip` (v1.1 - 7.9 KB)
2. **Go to**: Plugins → Add New → Upload Plugin
3. **Choose file**: nexus-license-api-plugin.zip
4. **Click**: Install Now
5. **Click**: Activate

---

## ✅ Verify It's Working

### Test 1: Check Plugin is Active

**Go to**: Plugins → Installed Plugins  
**Verify**: "Nexus License API" shows as Active

### Test 2: Make a Test Purchase

1. **Add product to cart** (use incognito browser)
2. **Complete purchase** with PayPal sandbox
3. **Check**: License Manager → Manage Licenses
4. **You should see**: New license auto-created! ✅

### Test 3: Check Order Notes

1. **Go to**: WooCommerce → Orders
2. **Open**: Your test order
3. **Check order notes**: Should say "License auto-created: NEXUS-XXXX..."

### Test 4: Check Customer Email

Customer should receive **2 emails**:
1. Order confirmation from WooCommerce
2. **License key email** from Nexus License API ✅

---

## 📋 Supported Products (Auto-Detection)

The plugin auto-detects license tier by product SKU:

| Product SKU | License Tier | Max Domains | Validity |
|-------------|--------------|-------------|----------|
| `nexus-pro` | pro | 1 | 365 days |
| `nexus-advanced` | advanced | 3 | 365 days |
| `nexus-agency` | agency | 999 | 365 days |
| `nexus-free` | (skipped) | - | - |

**Important**: Make sure your product SKUs match exactly!

---

## 🎯 For Existing Orders

The auto-creation only works for **new orders** after the plugin update.

**For orders already completed**, you can:

### Option A: Manually Create Licenses

1. **Go to**: License Manager → Add New License
2. **Fill in**:
   - License Key: (auto-generated)
   - Email: Customer email
   - License Type: `pro`, `advanced`, or `agency`
   - Max Domains: 1, 3, or 999
   - Expiry: 365 days from now
3. **Click**: Add License
4. **Send email** to customer with the license key

### Option B: Re-process Old Orders

1. **Go to**: WooCommerce → Orders
2. **Find old order**
3. **Change status** to "On Hold"
4. **Change status** back to "Completed"
5. License will auto-create! ✅

---

## 🔧 Troubleshooting

### License Not Auto-Created

**Check:**
1. Product SKU is correct (`nexus-pro`, `nexus-advanced`, `nexus-agency`)
2. Order status is "Processing" or "Completed"
3. Plugin is activated
4. Check WordPress debug log for errors

### Multiple Licenses Created

**Fix:**
- Plugin prevents duplicate licenses for same order
- If you see duplicates, manually delete extras in License Manager

### Email Not Sent

**Check:**
1. WordPress email is configured (install WP Mail SMTP plugin)
2. Customer email is valid
3. Check spam folder
4. Manually resend from License Manager

---

## 📧 License Email Content

Customers receive this email automatically:

```
Subject: Your Nexus Theme License Key

Hi [Customer Name],

Thank you for purchasing Nexus [Tier]!

Your License Key: NEXUS-XXXX-XXXX-XXXX-XXXX

License Details:
- Tier: Pro/Advanced/Agency
- Max Domains: 1/3/999
- Valid Until: [Date]

To activate your license:
1. Install Nexus theme on your WordPress site
2. Go to Dashboard → Nexus → License
3. Enter your license key
4. Click 'Activate License'

Need help? Visit: https://jdsandigitel.com/support

Thank you!
jdsan Digitel Team
```

---

## 🎉 Success!

You now have **fully automated license generation**!

Every purchase = instant license = happy customers! 🚀

---

**Questions?** Check the main setup guide: [JDSANDIGITEL_LICENSE_SERVER_SETUP.md](JDSANDIGITEL_LICENSE_SERVER_SETUP.md)
