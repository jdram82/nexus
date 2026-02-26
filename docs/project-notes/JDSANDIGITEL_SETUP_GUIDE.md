# Complete Setup Guide: Nexus Theme License Server & Store on jdsandigitel.com

**Website:** jdsandigitel.com  
**Product:** Nexus WordPress Theme  
**Setup Time:** ~2-3 days  
**Date:** December 28, 2025

---

## 📋 Overview

This guide will help you set up jdsandigitel.com to:
1. **Sell Nexus Theme** with automated license key generation
2. **Validate Licenses** via REST API for customer sites
3. **Deliver Updates** automatically to theme customers
4. **Process Payments** via Stripe, PayPal, and Razorpay
5. **Manage Subscriptions** for annual renewals

---

## 🎯 Quick Summary

**What You Need:**
- WordPress site on jdsandigitel.com (✅ if already have)
- WooCommerce plugin (FREE)
- WooCommerce Subscriptions ($199/year or use free alternative)
- WooCommerce Software Add-on ($299 one-time OR use free Software License Manager plugin)
- 2-3 days setup time
- SSL certificate (Let's Encrypt - FREE)

**Monthly Costs After Setup:**
- Hosting: $10-30/month (recommended: Cloudways, WP Engine, or DigitalOcean)
- WooCommerce Subscriptions: ~$17/month OR use free alternatives
- Email (SendGrid/Mailgun): $10-15/month for transactional emails
- **Total: ~$27-45/month** (or ~$10/month with free alternatives)

**Revenue Model:**
- You keep 100% of sales (no commissions!)
- Pro Tier: $199/year per customer
- Advanced Tier: $299/year per customer  
- Agency Tier: $599/year per customer

---

## 🚀 Phase 1: WordPress Setup (Day 1 - 2-3 hours)

### Step 1.1: Install WordPress on jdsandigitel.com

**If you don't have WordPress yet:**

```bash
# Option A: cPanel Auto-Installer
1. Login to your hosting cPanel
2. Find "WordPress" or "Softaculous Apps Installer"
3. Click "Install"
4. Choose domain: jdsandigitel.com
5. Set admin username/password
6. Click "Install"

# Option B: Manual Installation
# Download WordPress
wget https://wordpress.org/latest.zip
unzip latest.zip
# Upload to your hosting via FTP
# Visit jdsandigitel.com/wp-admin/install.php
```

**Requirements Check:**
- ✅ PHP 8.0 or higher
- ✅ MySQL 5.7 or higher  
- ✅ 512MB+ memory limit
- ✅ HTTPS/SSL enabled

### Step 1.2: Secure Your Site

1. **Install SSL Certificate:**
   ```bash
   # Most hosts provide free Let's Encrypt SSL
   # In cPanel: Go to "SSL/TLS" → "Let's Encrypt SSL"
   # Click "Issue" for jdsandigitel.com
   ```

2. **Force HTTPS:**
   - Go to **Settings → General**
   - Change both URLs to `https://jdsandigitel.com`
   - Save

3. **Update Permalinks:**
   - Go to **Settings → Permalinks**
   - Select **"Post name"** (SEO-friendly URLs)
   - Save

### Step 1.3: Install Required Plugins

**Core Plugins (Install via Plugins → Add New):**

1. **WooCommerce** (FREE)
   - Search: "WooCommerce"
   - Install & Activate
   - Run setup wizard:
     - Industry: **"Software and Apps"**
     - Products: **"Downloads"**
     - Skip shipping options
     - Set up payments (Stripe, PayPal)

2. **Software License Manager** (FREE Alternative to WooCommerce Software Add-on)
   - Search: "Software License Manager"
   - Install & Activate
   - Go to **Settings → Software License Manager**
   - Configure:
     - License Key Prefix: `NEXUS-`
     - License Key Length: `32`
     - Enable API: ✅ Check
     - Generate and save API Secret Key

**Optional Plugins (Recommended):**

3. **WooCommerce Subscriptions** ($199/year)
   - For automatic annual renewals
   - Alternative FREE: **Subscriptions for WooCommerce** by WebToffee

4. **Payment Gateways:**
   - Stripe Payment Gateway (FREE)
   - PayPal Checkout (FREE)
   - Razorpay for WooCommerce (FREE) - for India

5. **Email Marketing:**
   - Mailchimp for WooCommerce (FREE)
   - Or: SendinBlue/Brevo (FREE tier available)

---

## 🛍️ Phase 2: Product Setup (Day 1 - 2 hours)

### Step 2.1: Create Nexus Theme Products

Create **4 products** (Free, Pro, Advanced, Agency):

#### Product 1: Nexus Theme FREE

1. Go to **Products → Add New**

2. **Basic Info:**
   - Product name: `Nexus Theme - FREE`
   - Regular price: `0`
   - Sale price: (leave empty)

3. **Product Data:**
   - Product type: **Simple product**
   - Virtual: ✅ Check
   - Downloadable: ✅ Check

4. **Downloads:**
   - File name: `Nexus Theme v3.0.0 (Free)`
   - File URL: `https://github.com/jdram82/nexus/archive/refs/tags/v3.0.0.zip`
   - OR upload the theme ZIP to Media Library and link it

5. **Description:**
   ```
   Modern WordPress theme with WooCommerce integration, custom post types,
   responsive design, and SEO optimization. Perfect for personal projects.
   
   FEATURES:
   ✅ Modern WordPress theme architecture
   ✅ WooCommerce integration
   ✅ Responsive design (mobile, tablet, desktop)
   ✅ SEO optimized structure
   ✅ Block patterns support
   ✅ 1 site license
   ✅ Community support
   ```

6. **Short Description:**
   ```
   Free WordPress theme with WooCommerce integration and responsive design.
   ```

7. **Product Image:**
   - Upload a screenshot of the theme

8. Click **Publish**

---

#### Product 2: Nexus Theme PRO

1. **Products → Add New**

2. **Basic Info:**
   - Product name: `Nexus Theme - PRO License`
   - Regular price: `199`
   - Sale price: (optional - use for launch discount)

3. **Product Data:**
   - Product type: **Simple subscription** (if using Subscriptions plugin)
     - OR: **Simple product** (if not using subscriptions)
   - Subscription details (if applicable):
     - Subscription period: **Year**
     - Expire after: **1 Year**
     - Sign-up fee: `0`
   - Virtual: ✅ Check
   - Downloadable: ✅ Check

4. **Downloads:**
   - File name: `Nexus Theme v3.0.0 (Pro)`
   - File URL: `https://github.com/jdram82/nexus/archive/refs/tags/v3.0.0.zip`

5. **Software License Manager Settings** (if visible):
   - Create License: ✅ Yes
   - License Type: `pro`
   - License Validity: `365` days
   - Max Domains: `1`

6. **Description:**
   ```
   Unlock premium features for professional WordPress sites.
   
   PRO FEATURES:
   ✅ Cloud Storage Integration (5GB DigitalOcean Spaces)
   ✅ Template Cloud Sync with Auto-Backup
   ✅ Payment Gateway Integration (Stripe, Razorpay, PayPal)
   ✅ Credits System with Transaction History
   ✅ Priority Email Support
   ✅ 1 Year of Updates
   ✅ 1 Site License
   
   PRICE: $199/year (renews annually)
   
   Get instant access to your license key after purchase. Activate
   on your WordPress site via the Nexus License Manager.
   ```

7. **Short Description:**
   ```
   Professional features: Cloud storage, payment gateways, template sync,
   and credits system. $199/year for 1 site.
   ```

8. **Product SKU:** `nexus-pro`

9. Click **Publish**

---

#### Product 3: Nexus Theme ADVANCED

1. **Products → Add New**

2. **Basic Info:**
   - Product name: `Nexus Theme - ADVANCED License`
   - Regular price: `299`

3. **Product Data:**
   - Same as Pro but:
     - License Type: `advanced`
     - Max Domains: `3`

4. **Description:**
   ```
   Everything in Pro, PLUS 14 advanced features for developers and agencies.
   
   ADVANCED FEATURES:
   ✅ Theme Builder (visual header/footer builder, 50+ templates)
   ✅ Advanced Controls (typography, spacing, animations)
   ✅ Mega Menu Builder (multi-column menus, 20+ templates)
   ✅ Template Manager (100+ professional templates)
   ✅ API Documentation Generator (auto REST API docs)
   ✅ Circuit Simulator (visual circuit designer with 20+ components)
   ✅ Performance Analytics (Core Web Vitals tracking)
   ✅ Plugin Orchestrator (deep plugin integrations)
   ✅ Dynamic Loop Builder (visual query builder)
   ✅ SEO Manager (meta optimization, schema markup)
   ✅ Advanced Filtering System (AJAX filters)
   ✅ Form Builder (visual designer, 15+ field types)
   
   PRICE: $299/year (renews annually)
   3 Site Licenses | Priority Email Support
   ```

5. **Short Description:**
   ```
   Advanced tools for developers: Theme builder, mega menus, circuit sim,
   API docs, performance analytics. $299/year for 3 sites.
   ```

6. **Product SKU:** `nexus-advanced`

7. Click **Publish**

---

#### Product 4: Nexus Theme AGENCY

1. **Products → Add New**

2. **Basic Info:**
   - Product name: `Nexus Theme - AGENCY License`
   - Regular price: `599`

3. **Product Data:**
   - License Type: `agency`
   - Max Domains: `999` (unlimited)

4. **Description:**
   ```
   Everything in Advanced, PLUS agency-exclusive features.
   
   AGENCY-EXCLUSIVE FEATURES:
   ✅ A/B Testing System (statistical analysis, conversion tracking)
   ✅ White Label System (complete rebranding + export)
   ✅ Agency Dashboard (manage unlimited WordPress sites)
   ✅ Client Portal (file sharing, support tickets)
   
   Plus ALL Advanced features:
   Theme Builder, Mega Menus, Circuit Sim, API Docs, Performance Analytics,
   Loop Builder, SEO Manager, Advanced Filtering, Form Builder, and more.
   
   PRICE: $599/year (renews annually)
   UNLIMITED Site Licenses | Priority Email + Phone Support
   
   Perfect for agencies managing multiple client sites.
   ```

5. **Short Description:**
   ```
   Agency toolkit: A/B testing, white label, agency dashboard, client portal.
   $599/year for unlimited sites.
   ```

6. **Product SKU:** `nexus-agency`

7. Click **Publish**

---

## 🔐 Phase 3: License Server Setup (Day 2 - 3 hours)

### Step 3.1: Create License API Wrapper Plugin

This plugin translates between Software License Manager and Nexus Theme's expected API format.

1. **Create plugin directory:**
   ```bash
   # Connect to your server via SSH or use File Manager
   cd /path/to/wordpress/wp-content/plugins
   mkdir nexus-license-api
   cd nexus-license-api
   ```

2. **Create plugin file:** `nexus-license-api.php`

```php
<?php
/**
 * Plugin Name: Nexus License API
 * Description: License validation server for Nexus Theme customers
 * Version: 1.0.0
 * Author: JDS & Digital
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Nexus_License_API {
    
    private $api_secret;
    
    public function __construct() {
        // Get API secret from Software License Manager settings
        $this->api_secret = get_option( 'slm_secret_key', '' );
        
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        // Activation endpoint
        register_rest_route( 'nexus-licenses/v1', '/activate', array(
            'methods' => 'POST',
            'callback' => array( $this, 'activate_license' ),
            'permission_callback' => '__return_true',
        ) );
        
        // Validation endpoint  
        register_rest_route( 'nexus-licenses/v1', '/validate', array(
            'methods' => 'POST',
            'callback' => array( $this, 'validate_license' ),
            'permission_callback' => '__return_true',
        ) );
        
        // Deactivation endpoint
        register_rest_route( 'nexus-licenses/v1', '/deactivate', array(
            'methods' => 'POST',
            'callback' => array( $this, 'deactivate_license' ),
            'permission_callback' => '__return_true',
        ) );
        
        // Info endpoint (for theme update checks)
        register_rest_route( 'nexus-licenses/v1', '/info', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_info' ),
            'permission_callback' => '__return_true',
        ) );
    }
    
    /**
     * Activate a license key for a site
     */
    public function activate_license( $request ) {
        $license_key = sanitize_text_field( $request->get_param( 'license_key' ) );
        $site_url = sanitize_text_field( $request->get_param( 'site_url' ) );
        
        // Validate input
        if ( empty( $license_key ) || empty( $site_url ) ) {
            return new WP_Error( 'missing_params', 'License key and site URL required', array( 'status' => 400 ) );
        }
        
        // Get license from database
        global $wpdb;
        $table = $wpdb->prefix . 'lic_key_tbl';
        
        $license = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table WHERE license_key = %s",
            $license_key
        ) );
        
        if ( ! $license ) {
            return new WP_Error( 'invalid_license', 'Invalid license key', array( 'status' => 400 ) );
        }
        
        // Check if active
        if ( 'active' !== $license->lic_status ) {
            return new WP_Error( 'inactive_license', 'License is not active', array( 'status' => 400 ) );
        }
        
        // Check expiration
        if ( ! empty( $license->date_expiry ) && '0000-00-00' !== $license->date_expiry ) {
            if ( strtotime( $license->date_expiry ) < time() ) {
                return new WP_Error( 'expired_license', 'License has expired', array( 'status' => 400 ) );
            }
        }
        
        // Check activation limit
        $activations_table = $wpdb->prefix . 'lic_key_activations';
        $current_activations = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $activations_table WHERE license_key = %s",
            $license_key
        ) );
        
        if ( $current_activations >= $license->max_allowed_domains ) {
            return new WP_Error( 'activation_limit', 'Activation limit reached', array( 'status' => 400 ) );
        }
        
        // Check if already activated for this domain
        $existing = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $activations_table WHERE license_key = %s AND registered_domain = %s",
            $license_key,
            $site_url
        ) );
        
        if ( $existing ) {
            // Already activated, return success
            return array(
                'success' => true,
                'tier' => $this->get_tier_from_license( $license ),
                'expires' => $license->date_expiry,
                'message' => 'Already activated for this site',
            );
        }
        
        // Add activation
        $wpdb->insert(
            $activations_table,
            array(
                'license_key' => $license_key,
                'registered_domain' => $site_url,
                'activation_date' => current_time( 'mysql' ),
            ),
            array( '%s', '%s', '%s' )
        );
        
        if ( $wpdb->insert_id ) {
            return array(
                'success' => true,
                'tier' => $this->get_tier_from_license( $license ),
                'expires' => $license->date_expiry,
                'activation_id' => $wpdb->insert_id,
                'message' => 'License activated successfully',
            );
        } else {
            return new WP_Error( 'activation_failed', 'Failed to activate license', array( 'status' => 500 ) );
        }
    }
    
    /**
     * Validate a license key
     */
    public function validate_license( $request ) {
        $license_key = sanitize_text_field( $request->get_param( 'license_key' ) );
        $site_url = sanitize_text_field( $request->get_param( 'site_url' ) );
        
        if ( empty( $license_key ) || empty( $site_url ) ) {
            return array(
                'valid' => false,
                'tier' => 'free',
            );
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'lic_key_tbl';
        
        $license = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table WHERE license_key = %s",
            $license_key
        ) );
        
        if ( ! $license ) {
            return array(
                'valid' => false,
                'tier' => 'free',
            );
        }
        
        // Check if activated for this site
        $activations_table = $wpdb->prefix . 'lic_key_activations';
        $activation = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $activations_table WHERE license_key = %s AND registered_domain = %s",
            $license_key,
            $site_url
        ) );
        
        if ( ! $activation ) {
            return array(
                'valid' => false,
                'tier' => 'free',
                'message' => 'License not activated for this site',
            );
        }
        
        // Check status
        if ( 'active' !== $license->lic_status ) {
            return array(
                'valid' => false,
                'tier' => 'free',
                'message' => 'License is not active',
            );
        }
        
        // Check expiration
        $expired = false;
        if ( ! empty( $license->date_expiry ) && '0000-00-00' !== $license->date_expiry ) {
            if ( strtotime( $license->date_expiry ) < time() ) {
                $expired = true;
            }
        }
        
        if ( $expired ) {
            return array(
                'valid' => false,
                'tier' => 'free',
                'expired' => true,
                'expires' => $license->date_expiry,
                'message' => 'License has expired',
            );
        }
        
        // Valid license!
        return array(
            'valid' => true,
            'tier' => $this->get_tier_from_license( $license ),
            'expires' => $license->date_expiry,
            'customer_email' => $license->email,
            'message' => 'License is valid',
        );
    }
    
    /**
     * Deactivate a license
     */
    public function deactivate_license( $request ) {
        $license_key = sanitize_text_field( $request->get_param( 'license_key' ) );
        $site_url = sanitize_text_field( $request->get_param( 'site_url' ) );
        
        if ( empty( $license_key ) || empty( $site_url ) ) {
            return new WP_Error( 'missing_params', 'License key and site URL required', array( 'status' => 400 ) );
        }
        
        global $wpdb;
        $activations_table = $wpdb->prefix . 'lic_key_activations';
        
        $deleted = $wpdb->delete(
            $activations_table,
            array(
                'license_key' => $license_key,
                'registered_domain' => $site_url,
            ),
            array( '%s', '%s' )
        );
        
        if ( $deleted ) {
            return array(
                'success' => true,
                'message' => 'License deactivated successfully',
            );
        } else {
            return array(
                'success' => false,
                'message' => 'License was not activated for this site',
            );
        }
    }
    
    /**
     * Get server info
     */
    public function get_info( $request ) {
        return array(
            'name' => 'Nexus License Server',
            'version' => '1.0.0',
            'endpoints' => array(
                'activate' => home_url( '/wp-json/nexus-licenses/v1/activate' ),
                'validate' => home_url( '/wp-json/nexus-licenses/v1/validate' ),
                'deactivate' => home_url( '/wp-json/nexus-licenses/v1/deactivate' ),
            ),
        );
    }
    
    /**
     * Determine tier from license data
     */
    private function get_tier_from_license( $license ) {
        // Check product name or license type
        if ( ! empty( $license->product_ref ) ) {
            $product_ref = strtolower( $license->product_ref );
            
            if ( strpos( $product_ref, 'agency' ) !== false ) {
                return 'agency';
            } elseif ( strpos( $product_ref, 'advanced' ) !== false ) {
                return 'advanced';
            } elseif ( strpos( $product_ref, 'pro' ) !== false ) {
                return 'pro';
            }
        }
        
        // Fallback: check by max domains
        if ( $license->max_allowed_domains >= 999 ) {
            return 'agency';
        } elseif ( $license->max_allowed_domains >= 3 ) {
            return 'advanced';
        } elseif ( $license->max_allowed_domains >= 1 ) {
            return 'pro';
        }
        
        return 'free';
    }
}

// Initialize
new Nexus_License_API();
```

3. **Activate the plugin:**
   - Go to **Plugins** in WordPress admin
   - Find "Nexus License API"
   - Click **Activate**

4. **Test the API:**
   - Open browser
   - Visit: `https://jdsandigitel.com/wp-json/nexus-licenses/v1/info`
   - Should see JSON response with endpoints

---

### Step 3.2: Configure Software License Manager

1. Go to **Settings → Software License Manager**

2. **General Settings:**
   - License Key Prefix: `NEXUS-`
   - License Key Length: `32`
   - License Key Separator: `-`
   - Separator Frequency: `8`
   - Enable API: ✅ Check

3. **Email Settings:**
   - Customize the license delivery email
   - Use these placeholders:
     - `{license_key}` - The license key
     - `{product_name}` - Product name
     - `{customer_name}` - Customer name
     - `{customer_email}` - Customer email

   Example email template:
   ```
   Hi {customer_name},

   Thank you for purchasing {product_name}!

   Your license key: {license_key}

   To activate your license:
   1. Install Nexus Theme on your WordPress site
   2. Go to Dashboard → Nexus → License
   3. Enter your license key above
   4. Click "Activate License"

   Need help? Reply to this email.

   Best regards,
   JDS & Digital Team
   ```

4. **Save Changes**

---

### Step 3.3: Link Products to License Manager

1. **Edit each product** (Pro, Advanced, Agency)

2. Scroll to **Software License Manager** meta box

3. Configure:
   - **Create License**: ✅ Yes
   - **License Type**: 
     - Pro: `pro`
     - Advanced: `advanced`
     - Agency: `agency`
   - **License Validity**: `365` days
   - **Max Domains**:
     - Pro: `1`
     - Advanced: `3`
     - Agency: `999`
   - **Product Reference**: 
     - Pro: `nexus-pro`
     - Advanced: `nexus-advanced`
     - Agency: `nexus-agency`

4. **Update** each product

---

## 🔄 Phase 4: Update Theme License Server URL (Day 2 - 10 minutes)

### Step 4.1: Update Nexus Theme Code

1. **Edit:** `inc/class-nexus-license-manager.php`

2. **Find line ~35:**
   ```php
   private $license_server = 'https://yoursite.com/wp-json/nexus-licenses/v1/';
   ```

3. **Replace with:**
   ```php
   private $license_server = 'https://jdsandigitel.com/wp-json/nexus-licenses/v1/';
   ```

4. **Save file**

### Step 4.2: Commit Changes to GitHub

```bash
cd /workspaces/codespaces-blank/nexus-theme

git add inc/class-nexus-license-manager.php

git commit -m "Update license server URL to jdsandigitel.com"

git push origin main

# Create new release
git tag -a v3.0.1 -m "Production release with jdsandigitel.com license server"
git push origin v3.0.1
```

### Step 4.3: Update Product Download URLs

1. Go to each product in WooCommerce
2. Update download URL to:
   ```
   https://github.com/jdram82/nexus/archive/refs/tags/v3.0.1.zip
   ```
3. Save each product

---

## 💳 Phase 5: Payment Gateway Setup (Day 2 - 1 hour)

### Step 5.1: Stripe Setup

1. **Create Stripe Account:**
   - Go to https://stripe.com
   - Sign up for account
   - Complete business verification

2. **Get API Keys:**
   - Go to **Developers → API keys**
   - Copy:
     - **Publishable key** (starts with `pk_test_` or `pk_live_`)
     - **Secret key** (starts with `sk_test_` or `sk_live_`)

3. **Configure in WooCommerce:**
   - Go to **WooCommerce → Settings → Payments**
   - Click **Stripe**
   - Enable: ✅ Yes
   - Paste API keys
   - Enable test mode initially
   - Save changes

### Step 5.2: PayPal Setup

1. **Get PayPal API Credentials:**
   - Go to https://developer.paypal.com
   - Login with PayPal account
   - Go to **My Apps & Credentials**
   - Create new app
   - Copy Client ID and Secret

2. **Configure in WooCommerce:**
   - Go to **WooCommerce → Settings → Payments**
   - Click **PayPal**
   - Enable: ✅ Yes
   - Paste credentials
   - Save changes

### Step 5.3: Razorpay Setup (for India)

1. **Install Plugin:**
   - **Plugins → Add New**
   - Search: "Razorpay for WooCommerce"
   - Install & Activate

2. **Get API Keys:**
   - Go to https://razorpay.com
   - Sign up / Login
   - Go to **Settings → API Keys**
   - Generate keys

3. **Configure:**
   - Go to **WooCommerce → Settings → Payments**
   - Click **Razorpay**
   - Enable and configure
   - Save changes

---

## 🎨 Phase 6: Store Design (Day 3 - 2-3 hours)

### Step 6.1: Create Shop Pages

**Create these pages:**

1. **Home Page** (Marketing landing page)
2. **Features Page** (tier comparison)
3. **Pricing Page** (product listing)
4. **Documentation Page**
5. **Support Page**

### Step 6.2: Build Marketing Pages

**Home Page Example Structure:**

```markdown
# Transform Your WordPress Site with Nexus Theme

Modern, feature-rich WordPress theme with 4 pricing tiers
to fit any project size.

[View Pricing] [Live Demo] [Documentation]

## Why Nexus?

✅ 18 Premium Features
✅ 100% GPL Licensed
✅ No Vendor Lock-in
✅ 1-Year Updates Included

## Trusted by Developers & Agencies

[Customer testimonials...]

## Choose Your Plan

[Feature comparison table - link to pricing]

## Ready to Get Started?

[Download Free Version] [Buy Pro License]
```

### Step 6.3: Create Pricing Page

Use WooCommerce shortcode:

```
[products ids="123,124,125,126" columns="4"]
```

Or create custom pricing table that links to products.

---

## 📧 Phase 7: Email & Customer Communication (Day 3 - 1 hour)

### Step 7.1: Configure Transactional Emails

**Option A: SendGrid (FREE up to 100 emails/day)**

1. Install plugin: "SendGrid Subscription Widget"
2. Create free account at https://sendgrid.com
3. Get API key
4. Configure plugin
5. Test emails

**Option B: Mailgun (FREE up to 5,000 emails/month)**

1. Install plugin: "Mailgun for WordPress"
2. Create account at https://mailgun.com
3. Configure plugin

### Step 7.2: Customize WooCommerce Emails

1. **WooCommerce → Settings → Emails**

2. **Customize these templates:**
   - New order (customer)
   - Processing order
   - Completed order
   - Customer invoice

3. **Add license key to emails:**
   - Go to each email template
   - Add instructions for activating license

---

## ✅ Phase 8: Testing (Day 3 - 2 hours)

### Step 8.1: Test Purchase Flow

1. **Enable test mode** in payment gateways

2. **Make test purchase:**
   - Buy Pro license
   - Use test credit card: `4242 4242 4242 4242`
   - Complete checkout

3. **Verify:**
   - ✅ Order received
   - ✅ License key generated
   - ✅ Email sent with license key
   - ✅ Download link works

### Step 8.2: Test License Activation

1. **Install Nexus Theme** on test WordPress site

2. **Activate license:**
   - Dashboard → Nexus → License
   - Enter test license key
   - Click "Activate"

3. **Verify:**
   - ✅ License activates successfully
   - ✅ Pro features unlock
   - ✅ Tier badge shows "PRO"

### Step 8.3: Test License Validation API

```bash
# Test activation
curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/activate \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "YOUR-TEST-LICENSE-KEY",
    "site_url": "https://example.com"
  }'

# Should return:
# {"success":true,"tier":"pro","expires":"2026-12-28","message":"License activated successfully"}

# Test validation
curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/validate \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "YOUR-TEST-LICENSE-KEY",
    "site_url": "https://example.com"
  }'

# Should return:
# {"valid":true,"tier":"pro","expires":"2026-12-28","message":"License is valid"}
```

---

## 🚀 Phase 9: Launch (Day 4)

### Step 9.1: Pre-Launch Checklist

- [ ] SSL certificate active and working
- [ ] All 4 products created and published
- [ ] License API responding correctly
- [ ] Payment gateways configured and tested
- [ ] Email templates customized
- [ ] Marketing pages created
- [ ] Test purchase completed successfully
- [ ] License activation tested
- [ ] Theme updated with jdsandigitel.com URL
- [ ] GitHub release created (v3.0.1 or higher)

### Step 9.2: Switch to Live Mode

1. **Stripe:**
   - WooCommerce → Settings → Payments → Stripe
   - Uncheck "Test mode"
   - Replace test keys with live keys
   - Save

2. **PayPal:**
   - Replace sandbox credentials with live
   - Save

### Step 9.3: Announce Launch

1. **Create blog post** announcing launch
2. **Email subscribers** (if you have list)
3. **Social media** announcement
4. **Add to WordPress.org** theme directory (free version)

---

## 📊 Post-Launch: Monitoring & Support

### Daily Tasks

- ✅ Check for new orders
- ✅ Respond to support emails
- ✅ Monitor license activations

### Weekly Tasks

- ✅ Review sales analytics
- ✅ Check license server logs
- ✅ Update documentation if needed

### Monthly Tasks

- ✅ Review and improve marketing pages
- ✅ Analyze customer feedback
- ✅ Plan feature updates

---

## 🛠️ Troubleshooting

### Problem: License activation fails

**Solution:**
1. Check API endpoint: `https://jdsandigitel.com/wp-json/nexus-licenses/v1/info`
2. Verify license key exists in database
3. Check site URL format (must include https://)
4. Review error logs: WP Debug or server logs

### Problem: License not generated after purchase

**Solution:**
1. Check Software License Manager is active
2. Verify product has "Create License" enabled
3. Check WooCommerce order status (must be "Processing" or "Completed")
4. Manually create license: **License Manager → Add New**

### Problem: Payment failing

**Solution:**
1. Check payment gateway logs
2. Verify API keys are correct (live vs test)
3. Check if SSL is working
4. Test with different payment method

---

## 💰 Revenue Projections

**Conservative Estimate (Year 1):**
- 10 Pro sales/month × $199 = $1,990/month
- 5 Advanced sales/month × $299 = $1,495/month
- 2 Agency sales/month × $599 = $1,198/month
- **Total: $4,683/month = $56,196/year**

**After Costs:**
- Hosting: -$360/year
- Email service: -$180/year
- WooCommerce plugins: -$398/year
- **Net Profit: ~$55,258/year**

---

## 📞 Support Resources

**Documentation:**
- [NEXUS_FEATURES_BY_TIER.md](NEXUS_FEATURES_BY_TIER.md) - Feature breakdown
- [LICENSE_PROTECTION_IMPLEMENTED.md](LICENSE_PROTECTION_IMPLEMENTED.md) - Security details
- [COMPREHENSIVE_TESTING_PLAN.md](COMPREHENSIVE_TESTING_PLAN.md) - Testing guide

**External Resources:**
- WooCommerce Docs: https://woocommerce.com/documentation/
- Software License Manager: https://www.tipsandtricks-hq.com/software-license-manager-plugin-for-wordpress
- Stripe Docs: https://stripe.com/docs

**Support:**
- WooCommerce Support: https://woocommerce.com/my-account/create-a-ticket/
- WordPress Forums: https://wordpress.org/support/

---

## 🎯 Next Steps

1. ✅ **Today:** Install WordPress on jdsandigitel.com (if not done)
2. ✅ **Day 1:** Install WooCommerce + Software License Manager
3. ✅ **Day 1:** Create 4 products (Free, Pro, Advanced, Agency)
4. ✅ **Day 2:** Set up license API plugin
5. ✅ **Day 2:** Configure payment gateways
6. ✅ **Day 3:** Design marketing pages
7. ✅ **Day 3:** Test complete purchase flow
8. ✅ **Day 4:** Go live!

---

**Questions or need help?**

This guide should get you 90% there. If you hit any roadblocks:
1. Check troubleshooting section above
2. Review the documentation files
3. Test the API endpoints manually
4. Verify WordPress/plugin versions

Good luck with your launch! 🚀

---

**Last Updated:** December 28, 2025  
**Version:** 1.0.0  
**Status:** Production Ready
