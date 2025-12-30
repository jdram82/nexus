# Nexus Theme Hosting & Sales Setup Guide

![Nexus Theme](Nexus_images/Social%20Media%20Post.png)

**Domain:** jdsandigitel.com  
**Product:** Nexus WordPress Theme  
**Pricing:** Free (base), Pro $199/year, Advanced $299/year, Agency $599/year  
**Updated:** December 28, 2025

![Performance](Nexus_images/Performance%20Concept.png)

---

## 📋 Table of Contents

1. [Standard WordPress Theme Sales Methods](#standard-methods)
2. [Recommended Setup for jdsandigitel.com](#recommended-setup)
3. [License Server Configuration](#license-server)
4. [Theme Distribution](#theme-distribution)
5. [Payment Processing](#payment-processing)
6. [Customer Portal](#customer-portal)
7. [Update Delivery System](#update-delivery)
8. [Security Checklist](#security)

---

## 🎯 Standard WordPress Theme Sales Methods

### Method 1: WooCommerce + Software Licensing (RECOMMENDED ✅)

**Best For:** Full control, recurring revenue, GPL compliance  
**Cost:** ~$299 one-time (WooCommerce Software Add-on)  
**Effort:** Medium (2-3 days setup)

**Pros:**
- ✅ Complete control over pricing and licensing
- ✅ Automatic updates via WordPress updater
- ✅ Customer dashboard with download history
- ✅ Subscription management for renewals
- ✅ Integration with Nexus License Manager (already built!)
- ✅ No revenue sharing

**Cons:**
- ❌ Requires WordPress hosting for store
- ❌ Initial setup complexity
- ❌ Need to handle support yourself

**Required Plugins:**
1. **WooCommerce** (Free) - Base e-commerce
2. **WooCommerce Software Licensing** ($299) - License key generation
3. **WooCommerce Subscriptions** ($199/year, optional) - Recurring billing

---

### Method 2: Easy Digital Downloads + Software Licensing

**Best For:** Digital products focus, lighter than WooCommerce  
**Cost:** $99/year (EDD Software Licensing)  
**Effort:** Medium (2-3 days)

**Pros:**
- ✅ Simpler than WooCommerce for digital-only products
- ✅ Built-in license management
- ✅ Recurring payments support
- ✅ Download tracking and limits

**Cons:**
- ❌ Less ecosystem than WooCommerce
- ❌ Annual renewal fee

---

### Method 3: Freemius (SaaS Platform)

**Best For:** Quick setup, no technical overhead  
**Cost:** FREE (but 30% revenue share)  
**Effort:** Low (1 day integration)

**Pros:**
- ✅ No server setup needed
- ✅ Automatic licensing and updates
- ✅ Analytics dashboard
- ✅ Handles payments via Stripe/PayPal

**Cons:**
- ❌ 30% commission on all sales
- ❌ Less control over customer experience
- ❌ Must integrate Freemius SDK into theme

**Not recommended** for premium themes like Nexus due to high commission.

---

### Method 4: ThemeForest/Envato

**Best For:** Maximum exposure, minimal setup  
**Cost:** FREE (but 37.5-50% revenue share!)  
**Effort:** Low (theme review process)

**Pros:**
- ✅ Massive built-in audience
- ✅ No hosting/payment setup needed
- ✅ Credibility boost

**Cons:**
- ❌ **50% commission** on regular licenses (you keep $100 from $199 sale)
- ❌ Strict code quality requirements
- ❌ No control over pricing/licensing
- ❌ Must follow their licensing terms

**Not recommended** for custom licensing model like Nexus.

---

## 🏗️ Recommended Setup for jdsandigitel.com

### Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    jdsandigitel.com                          │
│                  (WordPress Installation)                    │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Marketing  │  │  WooCommerce │  │   Customer   │      │
│  │   Website    │  │     Shop     │  │    Portal    │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
│                                                              │
│  Products:                                                   │
│  • Nexus Theme Free (FREE download)                          │
│  • Nexus Theme Pro ($199/year subscription)                  │
│  • Nexus Theme Advanced ($299/year subscription)             │
│  • Nexus Theme Agency ($599/year subscription)               │
│                                                              │
│  ┌───────────────────────────────────────────────────┐      │
│  │        License Server (REST API)                  │      │
│  │    /wp-json/nexus-licenses/v1/                   │      │
│  │    • /activate   • /deactivate   • /validate     │      │
│  └───────────────────────────────────────────────────┘      │
│                                                              │
│  ┌───────────────────────────────────────────────────┐      │
│  │        Update Server (Theme Updates)              │      │
│  │    Delivers latest theme versions to customers    │      │
│  └───────────────────────────────────────────────────┘      │
│                                                              │
└─────────────────────────────────────────────────────────────┘
         ↓                    ↓                    ↓
    Marketing           Purchase Flow        Customer Site
    Pages              & Downloads          (Theme Updates)
```

---

## 🔧 Step-by-Step Setup

### Phase 1: WordPress Installation (Day 1)

1. **Set up WordPress on jdsandigitel.com**
   ```bash
   # Recommended hosting: WP Engine, Kinsta, or Cloudways
   # Requirements:
   # - PHP 8.0+
   # - MySQL 5.7+
   # - SSL Certificate (Let's Encrypt)
   # - 512MB+ memory limit
   ```

2. **Install Required Plugins**
   ```
   # Core E-commerce
   ✅ WooCommerce (free)
   ✅ WooCommerce Subscriptions ($199/year)
   ✅ WooCommerce Software Add-on ($299 one-time)
   
   # Optional but Recommended
   ✅ Stripe Payment Gateway (free)
   ✅ PayPal Checkout (free)
   ✅ Mailchimp for WooCommerce (email marketing)
   ```

3. **Install SSL Certificate**
   ```bash
   # Most hosts provide free Let's Encrypt SSL
   # Or use Cloudflare for free SSL + CDN
   ```

---

### Phase 2: License Server Setup (Day 2)

1. **Create Custom Plugin: Nexus License Server**

   Create: `wp-content/plugins/nexus-license-server/nexus-license-server.php`

   ```php
   <?php
   /**
    * Plugin Name: Nexus License Server
    * Description: License validation server for Nexus Theme
    * Version: 1.0.0
    */

   class Nexus_License_Server {
       
       public function __construct() {
           add_action('rest_api_init', [$this, 'register_routes']);
       }
       
       public function register_routes() {
           register_rest_route('nexus-licenses/v1', '/activate', [
               'methods' => 'POST',
               'callback' => [$this, 'activate_license'],
               'permission_callback' => '__return_true'
           ]);
           
           register_rest_route('nexus-licenses/v1', '/validate', [
               'methods' => 'POST',
               'callback' => [$this, 'validate_license'],
               'permission_callback' => '__return_true'
           ]);
           
           register_rest_route('nexus-licenses/v1', '/deactivate', [
               'methods' => 'POST',
               'callback' => [$this, 'deactivate_license'],
               'permission_callback' => '__return_true'
           ]);
       }
       
       public function activate_license($request) {
           $license_key = sanitize_text_field($request->get_param('license_key'));
           $site_url = sanitize_text_field($request->get_param('site_url'));
           
           // Check if license exists in WooCommerce Software Licensing
           $license = wc_software_license($license_key);
           
           if (!$license) {
               return new WP_Error('invalid_license', 'Invalid license key', ['status' => 400]);
           }
           
           if ($license->get_status() !== 'active') {
               return new WP_Error('inactive_license', 'License is not active', ['status' => 400]);
           }
           
           // Check activation limit
           $activations = $license->get_activations();
           $max_activations = $license->get_activation_limit();
           
           if (count($activations) >= $max_activations) {
               return new WP_Error('activation_limit', 'Activation limit reached', ['status' => 400]);
           }
           
           // Activate for this site
           $activation_id = $license->add_activation($site_url);
           
           return [
               'success' => true,
               'tier' => $this->get_license_tier($license),
               'expires' => $license->get_expiration_date(),
               'activation_id' => $activation_id
           ];
       }
       
       public function validate_license($request) {
           $license_key = sanitize_text_field($request->get_param('license_key'));
           $site_url = sanitize_text_field($request->get_param('site_url'));
           
           $license = wc_software_license($license_key);
           
           if (!$license) {
               return ['valid' => false, 'tier' => 'free'];
           }
           
           // Check if activated for this site
           $activations = $license->get_activations();
           $is_activated = false;
           
           foreach ($activations as $activation) {
               if ($activation->get_activation_url() === $site_url) {
                   $is_activated = true;
                   break;
               }
           }
           
           if (!$is_activated) {
               return ['valid' => false, 'tier' => 'free'];
           }
           
           // Check expiration
           $expiration = $license->get_expiration_date();
           if ($expiration && strtotime($expiration) < time()) {
               return ['valid' => false, 'tier' => 'free', 'expired' => true];
           }
           
           return [
               'valid' => true,
               'tier' => $this->get_license_tier($license),
               'expires' => $expiration,
               'customer_email' => $license->get_customer_email()
           ];
       }
       
       public function deactivate_license($request) {
           $license_key = sanitize_text_field($request->get_param('license_key'));
           $site_url = sanitize_text_field($request->get_param('site_url'));
           
           $license = wc_software_license($license_key);
           
           if (!$license) {
               return new WP_Error('invalid_license', 'Invalid license key', ['status' => 400]);
           }
           
           // Find and remove activation
           $activations = $license->get_activations();
           foreach ($activations as $activation) {
               if ($activation->get_activation_url() === $site_url) {
                   $license->remove_activation($activation->get_id());
                   return ['success' => true];
               }
           }
           
           return new WP_Error('not_activated', 'License not activated for this site', ['status' => 400]);
       }
       
       private function get_license_tier($license) {
           $product_id = $license->get_product_id();
           $product = wc_get_product($product_id);
           
           // Map product SKU to tier
           $sku = $product->get_sku();
           
           if (stripos($sku, 'agency') !== false) return 'agency';
           if (stripos($sku, 'advanced') !== false) return 'advanced';
           if (stripos($sku, 'pro') !== false) return 'pro';
           
           return 'free';
       }
   }

   new Nexus_License_Server();
   ```

2. **Update Nexus Theme License Manager**

   Edit: `inc/class-nexus-license-manager.php`
   
   Replace line 35:
   ```php
   // OLD:
   private $license_server = 'https://yoursite.com/wp-json/nexus-licenses/v1/';
   
   // NEW:
   private $license_server = 'https://jdsandigitel.com/wp-json/nexus-licenses/v1/';
   ```

---

### Phase 3: WooCommerce Product Setup (Day 2-3)

1. **Create Products in WooCommerce**

   **Product 1: Nexus Theme Pro**
   - Product Type: Simple Subscription
   - Regular Price: $199
   - Billing Cycle: Every 1 year
   - SKU: `nexus-pro`
   - Enable Software Licensing: Yes
   - Activation Limit: 1 site
   - Product Visibility: Public

   **Product 2: Nexus Theme Advanced**
   - Product Type: Simple Subscription
   - Regular Price: $299
   - Billing Cycle: Every 1 year
   - SKU: `nexus-advanced`
   - Activation Limit: 3 sites
   
   **Product 3: Nexus Theme Agency**
   - Product Type: Simple Subscription
   - Regular Price: $599
   - Billing Cycle: Every 1 year
   - SKU: `nexus-agency`
   - Activation Limit: Unlimited

2. **Configure Software Licensing**
   
   For each product:
   - Enable "Software licensing"
   - Upload theme .zip file as downloadable file
   - Set license key pattern: `NEXUS-{tier}-{random}`
   - Enable automatic updates

3. **Set up Free Version**
   
   Create a separate free download page:
   - No WooCommerce product (bypass cart)
   - Direct download link to free version
   - Requires email signup (collect leads)

---

### Phase 4: Update Server Configuration (Day 3)

The WooCommerce Software Add-on automatically handles theme updates when configured properly.

**Theme Configuration:**

Edit your `style.css`:
```css
/*
Theme Name: Nexus
Theme URI: https://jdsandigitel.com/nexus-theme
Author: JD Sandi Digital
Author URI: https://jdsandigitel.com
Description: Professional WordPress theme for technical businesses
Version: 3.0.0
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: nexus
*/
```

**Update Checker Integration:**

The theme's `inc/class-nexus-theme-updater.php` will automatically check for updates from jdsandigitel.com when a license is activated.

---

### Phase 5: Payment Gateway Setup (Day 3)

1. **Stripe Setup** (Recommended for subscriptions)
   ```
   1. Create Stripe account at stripe.com
   2. Get Live API keys (publishable + secret)
   3. Install WooCommerce Stripe Gateway plugin
   4. Enter API keys in WooCommerce → Settings → Payments → Stripe
   5. Enable "Saved Cards" for easier renewals
   ```

2. **PayPal Setup** (Alternative/additional)
   ```
   1. Create PayPal Business account
   2. Enable recurring payments in PayPal settings
   3. Configure PayPal in WooCommerce
   4. Test subscription billing
   ```

3. **Razorpay** (If targeting India)
   ```
   1. Get Razorpay API keys
   2. Install Razorpay WooCommerce plugin
   3. Configure subscription support
   ```

**Test Mode First:**
- Use Stripe test keys initially
- Test card: 4242 4242 4242 4242
- Verify subscription renewals work
- Test failed payment scenarios

---

### Phase 6: Customer Portal (Day 4)

WooCommerce + Software Licensing provides a customer dashboard at:
`jdsandigitel.com/my-account/`

**Features:**
- View active subscriptions
- Download theme files
- View license keys
- Manage site activations
- Payment method updates
- Renewal management

**Customize the Portal:**

Create: `wp-content/themes/your-main-site-theme/woocommerce/myaccount/dashboard.php`

```php
<h2>Your Nexus Licenses</h2>

<?php
$customer_id = get_current_user_id();
$licenses = wc_get_customer_purchased_licenses($customer_id);

foreach ($licenses as $license) {
    $tier = get_tier_from_license($license);
    $activations = $license->get_activations();
    ?>
    <div class="license-card">
        <h3><?php echo ucfirst($tier); ?> License</h3>
        <p><strong>Key:</strong> <code><?php echo $license->get_key(); ?></code></p>
        <p><strong>Status:</strong> <?php echo $license->get_status(); ?></p>
        <p><strong>Expires:</strong> <?php echo $license->get_expiration_date(); ?></p>
        
        <h4>Active Sites (<?php echo count($activations); ?>)</h4>
        <ul>
            <?php foreach ($activations as $activation) : ?>
                <li><?php echo $activation->get_activation_url(); ?></li>
            <?php endforeach; ?>
        </ul>
        
        <a href="<?php echo $license->get_download_url(); ?>" class="button">
            Download Theme
        </a>
    </div>
    <?php
}
?>
```

---

### Phase 7: Marketing Website (Day 5-7)

**Homepage Structure:**

```
jdsandigitel.com/
├── Home (feature showcase, pricing)
├── Features (detailed feature breakdown by tier)
├── Pricing (comparison table)
├── Documentation (getting started guides)
├── Changelog (version history)
├── Support (contact form, FAQ)
└── My Account (customer portal)
```

**Pricing Page Template:**

```html
<div class="pricing-table">
    
    <!-- Free Tier -->
    <div class="pricing-card free">
        <h3>Free</h3>
        <p class="price">$0</p>
        <ul>
            <li>✅ Core theme features</li>
            <li>✅ WooCommerce integration</li>
            <li>✅ Responsive design</li>
            <li>✅ Basic customizer</li>
        </ul>
        <a href="/download-free" class="button">Download Free</a>
    </div>
    
    <!-- Pro Tier -->
    <div class="pricing-card pro">
        <h3>Pro</h3>
        <p class="price">$199<span>/year</span></p>
        <ul>
            <li>✅ Everything in Free</li>
            <li>✅ Cloud storage</li>
            <li>✅ Payment gateway</li>
            <li>✅ Template sync</li>
            <li>✅ Credits system</li>
            <li>✅ 1 site license</li>
        </ul>
        <a href="/checkout?add-to-cart=123" class="button primary">Buy Pro</a>
    </div>
    
    <!-- Advanced Tier -->
    <div class="pricing-card advanced popular">
        <div class="badge">Most Popular</div>
        <h3>Advanced</h3>
        <p class="price">$299<span>/year</span></p>
        <ul>
            <li>✅ Everything in Pro</li>
            <li>✅ Theme builder</li>
            <li>✅ Advanced controls</li>
            <li>✅ Mega menu</li>
            <li>✅ API docs generator</li>
            <li>✅ Circuit simulator</li>
            <li>✅ Performance analytics</li>
            <li>✅ 3 site licenses</li>
        </ul>
        <a href="/checkout?add-to-cart=124" class="button primary">Buy Advanced</a>
    </div>
    
    <!-- Agency Tier -->
    <div class="pricing-card agency">
        <h3>Agency</h3>
        <p class="price">$599<span>/year</span></p>
        <ul>
            <li>✅ Everything in Advanced</li>
            <li>✅ A/B testing</li>
            <li>✅ White label</li>
            <li>✅ Agency dashboard</li>
            <li>✅ Client portal</li>
            <li>✅ Unlimited sites</li>
            <li>✅ Priority support</li>
        </ul>
        <a href="/checkout?add-to-cart=125" class="button primary">Buy Agency</a>
    </div>
    
</div>
```

---

## 🔒 Security Checklist

Before launching:

### SSL/HTTPS
- [ ] SSL certificate installed
- [ ] Force HTTPS in WordPress settings
- [ ] Update all links to HTTPS
- [ ] Configure HSTS headers

### WordPress Hardening
- [ ] Strong admin password
- [ ] Two-factor authentication (Wordfence/iThemes Security)
- [ ] Disable file editing in wp-config.php:
  ```php
  define('DISALLOW_FILE_EDIT', true);
  ```
- [ ] Limit login attempts
- [ ] Hide WordPress version

### License Server Security
- [ ] Rate limit API endpoints (prevent brute force)
- [ ] Log all activation/validation requests
- [ ] Monitor for unusual patterns
- [ ] Implement nonce verification
- [ ] Validate all input data

### Payment Security (PCI Compliance)
- [ ] Never store credit card numbers
- [ ] Use Stripe/PayPal (they handle PCI)
- [ ] SSL on checkout pages
- [ ] Regular security audits

### Backups
- [ ] Daily automated backups
- [ ] Store backups off-site
- [ ] Test restore process
- [ ] Keep at least 30 days of backups

---

## 📊 Testing Your Setup

### Test Purchase Flow

1. **Test Free Download**
   - [ ] Download free version without account
   - [ ] Verify email delivery
   - [ ] Test theme installation
   - [ ] Confirm no Pro features load

2. **Test Pro Purchase**
   - [ ] Add Pro tier to cart
   - [ ] Complete checkout with test payment
   - [ ] Verify order confirmation email
   - [ ] Check license key generation
   - [ ] Download theme from account
   - [ ] Activate license on test site
   - [ ] Verify Pro features load
   - [ ] Confirm Advanced/Agency features blocked

3. **Test Subscription Renewal**
   - [ ] Wait for test renewal (or trigger manually)
   - [ ] Verify payment processing
   - [ ] Check license validity extends
   - [ ] Test failed payment scenario

4. **Test License Deactivation**
   - [ ] Deactivate from customer dashboard
   - [ ] Verify site returns to Free tier
   - [ ] Reactivate on different site
   - [ ] Test activation limits

---

## 📈 Post-Launch Monitoring

### Key Metrics to Track

1. **Sales Metrics**
   - Conversion rate by tier
   - Average order value
   - Renewal rate
   - Churn rate

2. **License Activity**
   - Daily activations
   - Failed validations
   - Activation limit violations
   - Expired licenses

3. **Support Requests**
   - Common issues
   - Feature requests
   - Bug reports

### Recommended Tools

- **Google Analytics** - Website traffic
- **WooCommerce Reports** - Sales data
- **Hotjar** - User behavior
- **Help Scout** - Support tickets
- **Mailchimp** - Email marketing

---

## 🚀 Go-Live Checklist

- [ ] Domain DNS pointed to hosting
- [ ] SSL certificate active
- [ ] WordPress installed and configured
- [ ] WooCommerce + plugins installed
- [ ] License server plugin activated
- [ ] Products created with correct SKUs
- [ ] Payment gateways in LIVE mode
- [ ] Theme updater URL configured
- [ ] Customer portal tested
- [ ] Test purchases completed successfully
- [ ] Email notifications working
- [ ] Legal pages created (Terms, Privacy, Refund)
- [ ] Marketing website content complete
- [ ] Documentation published
- [ ] Support system set up
- [ ] Analytics tracking installed
- [ ] Backup system verified
- [ ] Security audit completed

---

## 💡 Pro Tips

### 1. Offer a Free Trial
Instead of just a free version, offer a 14-day trial of Pro/Advanced tiers. Increases conversions by 30%+.

### 2. Implement Upgrade Paths
Allow Free → Pro, Pro → Advanced, Advanced → Agency upgrades with prorated billing.

### 3. Annual vs Monthly Pricing
- Annual: $199/year ($16.58/month) - Better revenue
- Monthly: $29/month ($348/year) - Lower barrier to entry

Consider offering both with annual discount.

### 4. Volume Licensing
For agencies buying multiple licenses:
- 5+ licenses: 20% discount
- 10+ licenses: 30% discount

### 5. Affiliate Program
Use AffiliateWP to let customers earn 20-30% commission on referrals.

---

## 📞 Next Steps

1. **Purchase Required Plugins** (~$500)
   - WooCommerce Software Add-on: $299
   - WooCommerce Subscriptions: $199/year

2. **Set Up Hosting** (~$30-100/month)
   - Recommended: WP Engine, Kinsta, or Cloudways

3. **Configure License Server** (2-3 days)
   - Install custom plugin above
   - Test all endpoints
   - Update theme with production URL

4. **Create WooCommerce Products** (1 day)
   - Pro, Advanced, Agency tiers
   - Configure SKUs and licensing

5. **Launch Marketing Site** (1 week)
   - Feature pages
   - Pricing page
   - Documentation
   - Support system

**Estimated Total Setup Time:** 2-3 weeks  
**Estimated Initial Investment:** $500-1,000 (plugins + hosting + SSL)

---

## ❓ FAQ

**Q: Can I sell on my site AND ThemeForest?**  
A: Technically yes, but you'd need different versions. ThemeForest requires GPL and doesn't allow external licensing. Not recommended.

**Q: How do I handle refunds?**  
A: WooCommerce has built-in refund handling. Recommended policy: 30-day money-back guarantee, no questions asked.

**Q: What about EU VAT/taxes?**  
A: Install WooCommerce EU VAT plugin to automatically calculate and collect VAT for EU customers.

**Q: How do I prevent theme piracy?**  
A: You can't completely prevent it (GPL requirement), but license validation prevents updates and premium features from working on unlicensed sites.

**Q: Should I offer lifetime licenses?**  
A: Not recommended. Annual subscriptions provide recurring revenue and ensure customers stay updated. Lifetime licenses are hard to support long-term.

---

**Ready to launch? Follow this guide step-by-step and you'll have a professional theme sales system running on jdsandigitel.com within 2-3 weeks!** 🚀
