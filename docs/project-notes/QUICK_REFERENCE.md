# 🚀 Nexus Pro - Quick Reference Card

## Your AutoCAD Plugin Launch Setup (5-Minute Version)

### 1️⃣ Credentials You Need

#### DigitalOcean Spaces ($5/month)
```
Space Name: ________________
Region: NYC3 / SFO3 / AMS3 / SGP1 / FRA1 (choose one)
Access Key: ________________
Secret Key: ________________
```
**Get from:** https://www.digitalocean.com/products/spaces

#### Razorpay (India) - FREE to start, 2% per transaction
```
Key ID: rzp_live_________________
Secret: _________________________
Webhook Secret: _________________
```
**Get from:** https://dashboard.razorpay.com/

**OR**

#### PayPal (Global) - FREE to start, 2.9% + $0.30 per transaction
```
Client ID: _____________________
Secret: ________________________
Mode: Sandbox / Live (choose one)
```
**Get from:** https://developer.paypal.com/

---

### 2️⃣ WordPress Admin Setup

**Step 1:** Enable Pro Features
- Go to: **WP Admin → Appearance → Nexus Pro**
- Click: **"Enable Pro Features"**

**Step 2:** Configure Cloud Storage
- Go to: **Nexus Pro → Cloud Storage**
- Paste DigitalOcean credentials
- Click: **"Test Connection"** → Should show ✅
- Click: **"Save Settings"**

**Step 3:** Configure Payment Gateway
- Go to: **Nexus Pro → Payment Gateways**
- Choose: Razorpay (India) OR PayPal (Global)
- Paste credentials
- Set webhook URL (shown on page)
- Click: **"Save Settings"**

---

### 3️⃣ Test Before Going Live

#### Test Cloud Storage
```
1. Create a test template
2. Save it
3. Check DigitalOcean dashboard → Should see file in templates/ folder
4. Download template → Should match original
```

#### Test Payment (Razorpay)
```
Test Card: 4111 1111 1111 1111
CVV: 123
Expiry: Any future date
OTP: 123456
```

#### Test Payment (PayPal)
```
Mode: Sandbox
Use PayPal sandbox buyer account
Pay with sandbox balance
```

---

### 4️⃣ Go Live Checklist

- [ ] DigitalOcean Spaces configured and tested
- [ ] Payment gateway configured (Razorpay OR PayPal)
- [ ] Test payment completed successfully
- [ ] Test template upload/download worked
- [ ] Webhooks configured in gateway dashboard
- [ ] SSL certificate installed (HTTPS)
- [ ] Switch payment gateway to LIVE mode
- [ ] Create your AutoCAD plugin landing page
- [ ] Add pricing, features, download flow
- [ ] Test complete purchase flow once more
- [ ] LAUNCH! 🚀

---

### 5️⃣ Webhook URLs (Copy-Paste These)

**Razorpay Webhook:**
```
https://YOUR-SITE.com/wp-admin/admin-ajax.php?action=nexus_razorpay_webhook
```
Add in: Razorpay Dashboard → Settings → Webhooks

**PayPal Webhook:**
```
https://YOUR-SITE.com/wp-admin/admin-ajax.php?action=nexus_paypal_webhook
```
Add in: PayPal Developer → My Apps → Your App → Webhooks  
Subscribe to: `CHECKOUT.ORDER.APPROVED`

---

### 6️⃣ Important File Locations

**Configuration Files:**
- Cloud Storage: `pro/cloud/class-cloud-storage.php`
- Payments: `pro/payment/class-payment-gateway-multi.php`
- Template Sync: `pro/cloud/class-template-cloud-sync.php`
- Database: `pro/class-database-schema.php`

**Documentation:**
- Setup Guide: `PRO_TIER_SETUP_GUIDE.md`
- API Reference: `PRO_TIER_API_REFERENCE.md`
- Completion Summary: `PRO_TIER_COMPLETE.md`

---

### 7️⃣ Common Issues & Fixes

**"Cloud connection failed"**
→ Double-check Access Key, Secret Key, and Region

**"Payment verification failed"**
→ Ensure webhook URL is added in gateway dashboard  
→ Check webhook secret matches

**"Template sync pending"**
→ Verify cloud storage credentials  
→ Check you haven't hit 5-template limit (Pro tier)

**"Webhooks not working"**
→ Ensure HTTPS enabled  
→ Check PHP error logs  
→ Verify webhook URL is publicly accessible

---

### 8️⃣ Pricing Calculator

**DigitalOcean Spaces:**
- Base: $5/month (250GB storage + 1TB bandwidth)
- Extra storage: $0.02/GB
- Extra bandwidth: $0.01/GB

**Example:** 1000 templates (~100MB) + 10GB monthly bandwidth  
**Cost:** $5/month ✅

**Razorpay:**
- Transaction fee: 2%
- Plugin price: ₹1,999
- You receive: ₹1,959 (₹40 fee)

**PayPal:**
- Transaction fee: 2.9% + $0.30
- Plugin price: $29
- You receive: $28.16 ($0.84 fee)

---

### 9️⃣ Your AutoCAD Plugin Landing Page Structure

**Suggested Sections:**
1. **Hero** - "Professional AutoCAD Plugin for [Purpose]"
2. **Features** - List 5-7 key features with icons
3. **Pricing** - Clear pricing: ₹1,999 / $29 one-time
4. **Screenshots** - Show plugin in action
5. **Testimonials** - Beta user feedback
6. **FAQ** - Common questions
7. **Download/Purchase** - Big CTA button

**Use Nexus Block Patterns:**
- Hero Section pattern
- Features Grid pattern
- CTA Section pattern

---

### 🔟 Support Contacts

**Stuck? Need help?**

📧 Email: support@nexustheme.com  
📖 Docs: Check `PRO_TIER_SETUP_GUIDE.md`  
🐛 Bug Reports: GitHub Issues  
💬 Quick Questions: Theme support forum

---

## 🎯 TL;DR (Too Long; Didn't Read)

1. Get DigitalOcean Spaces account → $5/month
2. Get Razorpay (India) OR PayPal account → FREE
3. Paste credentials in WP Admin → Nexus Pro settings
4. Test with fake payment
5. Switch to live mode
6. Launch your AutoCAD plugin! 🚀

**Total Setup Time:** ~15 minutes  
**Monthly Cost:** $5 (cloud storage only)  
**Transaction Fees:** 2-3% per sale

---

## ✅ Pro Tier Features (100% Complete)

✓ 5 cloud templates  
✓ DigitalOcean Spaces integration  
✓ Razorpay + PayPal payments  
✓ Auto cloud sync (hourly)  
✓ Template library  
✓ Advanced controls  
✓ Priority email support  

**Ready for production. No mocks. No placeholders.** 🎉

---

**Print this page and keep it handy during setup!**

Good luck with your AutoCAD plugin launch! 🚀
