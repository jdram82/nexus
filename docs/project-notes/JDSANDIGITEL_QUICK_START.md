# Quick Start Checklist: jdsandigitel.com Setup

![Nexus Website Mockup](Nexus_images/Website%20mockup.png)

**Goal:** Get Nexus Theme selling on jdsandigitel.com in 2-3 days

![License Protection](Nexus_images/License%20Protection.png)

---

## 📋 Day 1: WordPress & Products (3-4 hours)

### Morning (2 hours)

- [ ] **Install WordPress** on jdsandigitel.com (if not already)
  - Verify PHP 8.0+, MySQL 5.7+
  - Install SSL certificate (Let's Encrypt - FREE)
  - Set permalinks to "Post name"

- [ ] **Install WooCommerce**
  - Plugins → Add New → "WooCommerce"
  - Run setup wizard (Industry: Software, Products: Downloads)
  - Skip shipping, add payment methods later

- [ ] **Install Software License Manager** (FREE)
  - Plugins → Add New → "Software License Manager"
  - Settings → Software License Manager:
    - Prefix: `NEXUS-`
    - Length: `32`
    - Enable API: ✅

### Afternoon (2 hours)

- [ ] **Create Product 1: FREE**
  - Price: $0
  - Download: Latest GitHub release ZIP
  - SKU: `nexus-free`

- [ ] **Create Product 2: PRO** 
  - Price: $199/year
  - License type: `pro`
  - Max domains: `1`
  - SKU: `nexus-pro`

- [ ] **Create Product 3: ADVANCED**
  - Price: $299/year
  - License type: `advanced`
  - Max domains: `3`
  - SKU: `nexus-advanced`

- [ ] **Create Product 4: AGENCY**
  - Price: $599/year
  - License type: `agency`
  - Max domains: `999`
  - SKU: `nexus-agency`

**✅ Day 1 Complete:** Products ready for sale!

---

## 📋 Day 2: License Server & Payments (3-4 hours)

### Morning (2 hours)

- [ ] **Create Nexus License API Plugin**
  - SSH/FTP to: `wp-content/plugins/nexus-license-api/`
  - Create `nexus-license-api.php` (copy from setup guide)
  - Activate plugin
  - Test: `https://jdsandigitel.com/wp-json/nexus-licenses/v1/info`

- [ ] **Link Products to Licenses**
  - Edit each product (Pro, Advanced, Agency)
  - Software License Manager settings:
    - Create License: ✅ Yes
    - Set license type and max domains
  - Save all products

### Afternoon (1-2 hours)

- [ ] **Setup Stripe** (Recommended)
  - Create account: https://stripe.com
  - Get API keys (test mode first)
  - WooCommerce → Settings → Payments → Stripe
  - Enable and add keys

- [ ] **Setup PayPal** (Optional)
  - Get API credentials
  - WooCommerce → Settings → Payments → PayPal
  - Enable and configure

- [ ] **Setup Razorpay** (For India - Optional)
  - Install "Razorpay for WooCommerce" plugin
  - Get API keys
  - Configure in WooCommerce

**✅ Day 2 Complete:** License server and payments working!

---

## 📋 Day 3: Testing & Launch (3-4 hours)

### Morning (2 hours)

- [ ] **Test Purchase Flow**
  - Enable test mode in Stripe
  - Buy Pro license (test card: 4242 4242 4242 4242)
  - Check: Order created, license generated, email sent

- [ ] **Test License Activation**
  - Install Nexus theme on test WordPress site
  - Go to: Dashboard → Nexus → License
  - Enter test license key
  - Click "Activate License"
  - Verify: Pro badge shows, features unlock

- [ ] **Test API Endpoints**
  ```bash
  curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/validate \
    -d "license_key=YOUR-KEY&site_url=https://example.com"
  ```

### Afternoon (1-2 hours)

- [ ] **Create Marketing Pages**
  - Home page (landing page)
  - Features page (tier comparison)
  - Pricing page (link to products)
  - Documentation page

- [ ] **Configure Email**
  - Install SendGrid or Mailgun plugin
  - Configure SMTP
  - Customize WooCommerce email templates
  - Test order confirmation email

- [ ] **Go Live!**
  - Switch payment gateways to live mode
  - Replace test API keys with live keys
  - Announce on social media
  - Submit free version to WordPress.org

**✅ Day 3 Complete:** Store is LIVE! 🚀

---

## 🧪 Quick Test Commands

### Test License Server
```bash
# Get server info
curl https://jdsandigitel.com/wp-json/nexus-licenses/v1/info

# Test activation
curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/activate \
  -H "Content-Type: application/json" \
  -d '{"license_key":"NEXUS-XXXX-XXXX-XXXX-XXXX","site_url":"https://testsite.com"}'

# Test validation
curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/validate \
  -H "Content-Type: application/json" \
  -d '{"license_key":"NEXUS-XXXX-XXXX-XXXX-XXXX","site_url":"https://testsite.com"}'
```

---

## 💰 Costs Summary

**One-time:**
- WooCommerce: FREE ✅
- Software License Manager: FREE ✅
- Nexus License API: FREE (you build it) ✅

**Optional (Recommended):**
- WooCommerce Subscriptions: $199/year (or use FREE alternative)
- WooCommerce Software Add-on: $299 one-time (or use FREE Software License Manager)

**Monthly:**
- Hosting: $10-30/month
- Email service: $0-15/month (SendGrid FREE tier = 100 emails/day)
- **Total: $10-45/month**

**You keep 100% of sales!** No commissions, no revenue sharing.

---

## 🆘 Quick Troubleshooting

**License activation fails:**
- Check: `https://jdsandigitel.com/wp-json/nexus-licenses/v1/info`
- Verify SSL is working (HTTPS)
- Check WordPress/plugin updates

**No license generated after purchase:**
- Verify Software License Manager is active
- Check product has "Create License" enabled
- Order status must be "Processing" or "Completed"

**Payment not working:**
- Verify you're using LIVE keys (not test keys)
- Check SSL certificate is valid
- Try different payment method

**Can't access features after activation:**
- Check license tier matches feature requirement
- Verify license not expired
- Reactivate license

---

## 📞 Need Help?

**Documentation:**
- [JDSANDIGITEL_SETUP_GUIDE.md](JDSANDIGITEL_SETUP_GUIDE.md) - Full detailed guide
- [NEXUS_FEATURES_BY_TIER.md](NEXUS_FEATURES_BY_TIER.md) - Feature breakdown
- [LICENSE_PROTECTION_IMPLEMENTED.md](LICENSE_PROTECTION_IMPLEMENTED.md) - Security

**Plugin Documentation:**
- WooCommerce: https://woocommerce.com/documentation/
- Software License Manager: https://www.tipsandtricks-hq.com/
- Stripe: https://stripe.com/docs

---

## 🎯 Success Metrics (Track These!)

**Week 1:**
- [ ] First test purchase completed
- [ ] License activation working
- [ ] Email delivery working

**Month 1:**
- [ ] First real customer purchase
- [ ] 5+ products sold
- [ ] Positive customer feedback

**Month 3:**
- [ ] 20+ products sold
- [ ] $2,000+ revenue
- [ ] Support process established

**Year 1 Goal:**
- [ ] 100+ customers
- [ ] $30,000+ revenue
- [ ] Feature roadmap based on feedback

---

**Ready to start?** Begin with Day 1 checklist above! 🚀

**Last Updated:** December 28, 2025
