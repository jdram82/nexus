# Nexus Pro Tier - Complete Setup Guide

## 🎯 What You Get with Pro Tier

The **Nexus Pro Tier** ($199/year) is now 100% complete with production-ready features:

### ✅ Core Pro Features

1. **Cloud Template Storage** - DigitalOcean Spaces integration
2. **Multi-Gateway Payments** - Razorpay (India) + PayPal (Global)
3. **Template Cloud Sync** - Auto-backup templates to cloud
4. **Credit System** - Purchase and manage credits
5. **Template Library** - 50+ professional templates
6. **Advanced Customizer Controls** - Typography, spacing, shadows, etc.

### 📦 What's Included

- **5 Cloud Templates** - Store up to 5 templates in cloud
- **Unlimited Local Templates** - Create unlimited templates locally
- **Payment Gateway Integration** - Accept payments via Razorpay or PayPal
- **Auto Cloud Sync** - Templates automatically backed up hourly
- **Template Import/Export** - Share templates between sites
- **Priority Support** - Email support within 24 hours

---

## 🚀 Quick Start (5 Minutes)

### Step 1: Enable Pro Features

1. Go to **WordPress Admin → Appearance → Nexus Pro**
2. Click **"Enable Pro Features"**
3. Enter your license key (or use trial mode)
4. Click **Save Changes**

### Step 2: Configure Cloud Storage

1. Go to **Nexus Pro → Cloud Storage**
2. Sign up for [DigitalOcean Spaces](https://www.digitalocean.com/products/spaces) ($5/month)
3. Get your credentials:
   - **Space Name**: e.g., `my-templates`
   - **Region**: Choose nearest (NYC3, SFO3, AMS3, SGP1, FRA1)
   - **Access Key**: From Spaces → API section
   - **Secret Key**: From Spaces → API section
4. Paste credentials in form and click **Test Connection**
5. Click **Save Settings**

### Step 3: Configure Payment Gateway

**Choose ONE based on your location:**

#### Option A: Razorpay (Recommended for India)

1. Go to **Nexus Pro → Payment Gateways**
2. Sign up at [Razorpay Dashboard](https://dashboard.razorpay.com/)
3. Navigate to **Settings → API Keys**
4. Generate **Live** keys (or test keys for testing)
5. Copy:
   - **Key ID**: `rzp_live_xxxxx`
   - **Key Secret**: Your secret key
6. Set up webhook:
   - URL: `https://yoursite.com/wp-admin/admin-ajax.php?action=nexus_razorpay_webhook`
   - Events: Select all payment events
   - Copy **Webhook Secret**
7. Paste all credentials in Nexus Pro settings
8. Select **Razorpay** as Primary Gateway
9. Click **Save Settings**

#### Option B: PayPal (Recommended for Global)

1. Go to **Nexus Pro → Payment Gateways**
2. Sign up at [PayPal Developer](https://developer.paypal.com/)
3. Create a **REST API App**
4. Get **Client ID** and **Secret**
5. Set **Mode** to:
   - `Sandbox` for testing
   - `Live` for production
6. Add webhook URL:
   - `https://yoursite.com/wp-admin/admin-ajax.php?action=nexus_paypal_webhook`
   - Subscribe to: `CHECKOUT.ORDER.APPROVED`
7. Paste credentials in Nexus Pro settings
8. Select **PayPal** as Primary Gateway
9. Click **Save Settings**

### Step 4: Start Creating!

1. Go to **Nexus Pro → Templates**
2. Click **Create New Template**
3. Design your template
4. Click **Save & Sync to Cloud**
5. Your template is now backed up to DigitalOcean Spaces!

---

## 💳 Payment Gateway Comparison

| Feature | Razorpay | PayPal |
|---------|----------|--------|
| **Best For** | India, South Asia | Global, US, Europe |
| **Transaction Fee** | 2% + ₹0 | 2.9% + $0.30 |
| **Settlement** | T+3 days | Instant |
| **Currency** | INR, USD, EUR, etc. | 25+ currencies |
| **Local Payment Methods** | UPI, NetBanking, Cards | Cards, PayPal balance |
| **International** | Yes, but higher fees | Optimized for international |
| **Subscription Support** | Yes | Yes |

**Recommendation:**
- **India-based business?** → Use Razorpay
- **Global audience?** → Use PayPal
- **Both?** → Set Razorpay as primary, PayPal as fallback

---

## ☁️ Cloud Storage Providers

### DigitalOcean Spaces (Recommended)

**Pros:**
- ✅ Predictable pricing: $5/month for 250GB
- ✅ S3-compatible API (easy integration)
- ✅ Built-in CDN included
- ✅ Simple dashboard
- ✅ Great for small-medium businesses

**Cons:**
- ❌ Limited to specific regions
- ❌ Less features than AWS S3

**Pricing:**
- $5/month → 250GB storage + 1TB bandwidth
- $0.02/GB for additional storage
- $0.01/GB for additional bandwidth

### Alternative: AWS S3

**Pros:**
- ✅ Global infrastructure
- ✅ Advanced features (versioning, encryption, etc.)
- ✅ Pay-as-you-go

**Cons:**
- ❌ Complex pricing
- ❌ Steeper learning curve
- ❌ Can be expensive for small usage

**When to choose:**
- Already using AWS
- Need advanced features
- Have DevOps expertise

---

## 🔧 Advanced Configuration

### Custom Domain for Cloud Templates

1. In DigitalOcean Spaces, enable CDN
2. Add custom subdomain: `templates.yourdomain.com`
3. Update CNAME in your DNS:
   ```
   templates.yourdomain.com → your-space.nyc3.digitaloceanspaces.com
   ```
4. In Nexus Pro → Cloud Storage, enable **Custom Domain**
5. Enter: `https://templates.yourdomain.com`

### Automatic Hourly Sync

Cloud templates sync automatically every hour via WordPress Cron.

To change frequency:
```php
// Add to functions.php
add_filter( 'cron_schedules', 'nexus_custom_sync_interval' );
function nexus_custom_sync_interval( $schedules ) {
    $schedules['every_30min'] = array(
        'interval' => 1800, // 30 minutes in seconds
        'display'  => 'Every 30 Minutes'
    );
    return $schedules;
}
```

### Manual Sync Trigger

```php
// Manually trigger sync for specific template
do_action( 'nexus_sync_template', $template_id );

// Sync all templates
do_action( 'nexus_sync_all_templates' );
```

---

## 🧪 Testing Mode

Before going live, test everything:

### Cloud Storage Test

1. Upload a test template
2. Check DigitalOcean Spaces dashboard
3. Verify file appears in `templates/{user_id}/` folder
4. Download template back to verify integrity

### Payment Gateway Test

**Razorpay Test Mode:**
- Use `rzp_test_xxxxx` keys
- Test card: `4111 1111 1111 1111`
- Any CVV, any future date
- OTP: `123456`

**PayPal Sandbox:**
- Set Mode to `Sandbox`
- Use sandbox buyer account
- Test with sandbox PayPal balance

---

## 📊 Monitoring & Logs

### Cloud Sync Logs

View sync activity:
1. Go to **Nexus Pro → Cloud Storage**
2. Click **View Sync Logs**
3. See upload/download history with:
   - Timestamp
   - Template name
   - Action (upload/download/delete)
   - Status (success/failed)
   - File size
   - Duration

### Payment Logs

View payment activity:
1. Go to **Nexus Pro → Payment Gateways**
2. Click **View Payment Logs**
3. See transaction history with:
   - Order ID
   - Amount & Currency
   - Gateway (Razorpay/PayPal)
   - Status (pending/paid/failed)
   - Timestamp

---

## 🆘 Troubleshooting

### "Cloud connection failed"

**Possible causes:**
1. Wrong credentials → Double-check Access Key & Secret
2. Wrong region → Ensure region matches Space location
3. Firewall blocking → Check server firewall rules
4. CORS issue → Add your domain to Space CORS settings

**Fix:**
```bash
# Test connection via curl
curl -X GET \
  "https://YOUR_SPACE.nyc3.digitaloceanspaces.com" \
  -H "Authorization: AWS YOUR_ACCESS_KEY:SIGNATURE"
```

### "Payment verification failed"

**Razorpay:**
1. Check webhook is active
2. Verify webhook secret matches
3. Check signature in logs
4. Ensure HTTPS enabled

**PayPal:**
1. Verify Client ID matches mode (sandbox/live)
2. Check webhook subscribed to `CHECKOUT.ORDER.APPROVED`
3. Ensure return URLs are correct

### "Template sync pending"

**Causes:**
1. Cloud storage not configured
2. Tier limit reached (Pro = 5 templates)
3. WP Cron disabled
4. Large template size (>10MB)

**Fix:**
```php
// Check WP Cron status
define( 'DISABLE_WP_CRON', false );

// Manually trigger sync
wp_schedule_single_event( time(), 'nexus_cloud_sync_cron' );
```

---

## 🔐 Security Best Practices

### API Key Management

1. **Never commit keys to Git**
   ```bash
   # Add to .gitignore
   wp-config.php
   .env
   ```

2. **Use environment variables**
   ```php
   // In wp-config.php
   define( 'NEXUS_DO_ACCESS_KEY', getenv('DO_ACCESS_KEY') );
   define( 'NEXUS_DO_SECRET_KEY', getenv('DO_SECRET_KEY') );
   ```

3. **Rotate keys quarterly**
   - Generate new DigitalOcean keys every 3 months
   - Update in Nexus Pro settings
   - Delete old keys

### Payment Security

1. **Use HTTPS only** - Required for PCI compliance
2. **Enable webhook signature verification** - Prevents fraud
3. **Log all transactions** - For audit trail
4. **Set rate limits** - Prevent abuse

---

## 📈 Upgrading to Advanced/Agency Tier

### Advanced Tier ($299/year)

**Additional Features:**
- **Unlimited Cloud Templates** (no 5-template limit)
- **AI Template Generation** (OpenAI/Anthropic integration)
- **AI Documentation Generator**
- **White-Label Options**
- **Advanced Analytics**

### Agency Tier ($599/year)

**Additional Features:**
- **Everything in Advanced +**
- **Multi-Site Management Dashboard**
- **A/B Testing System**
- **Client Portal**
- **Priority Phone Support**

---

## 🎓 Next Steps

1. ✅ **Complete Setup** - Follow Quick Start above
2. 📚 **Read Template Guide** - Learn template best practices
3. 🎨 **Create Templates** - Design your first template
4. ☁️ **Test Cloud Sync** - Verify backup working
5. 💳 **Process Test Payment** - Ensure gateway working
6. 🚀 **Go Live** - Switch to production credentials

---

## 📞 Support

**Need help?**
- 📧 Email: support@nexustheme.com
- 💬 Live Chat: Available on website
- 📖 Docs: https://docs.nexustheme.com
- 🎥 Video Tutorials: https://youtube.com/nexustheme

**Pro Tier Support:**
- ⏱️ Response time: Within 24 hours
- 📧 Email only
- 🎯 Technical issues covered

---

## 🔄 Changelog

### v1.6.0 (Current)
- ✅ Complete DigitalOcean Spaces integration
- ✅ Multi-gateway payment (Razorpay + PayPal)
- ✅ Auto cloud sync system
- ✅ Database schema for all features
- ✅ Comprehensive logging

### v1.5.0
- Template library
- Basic customizer controls
- License management

---

**🎉 Congratulations!** You now have a fully functional Pro tier installation.

Happy building! 🚀
