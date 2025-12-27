# WooCommerce + Software License Manager Setup Guide

## 🎯 Complete License Server Setup (30 minutes)

This guide shows you how to set up a license validation server using WooCommerce + Software License Manager plugin.

---

## 📋 Prerequisites

- [ ] WordPress site for selling (e.g., yoursite.com)
- [ ] Domain name and hosting
- [ ] SSL certificate (HTTPS required)
- [ ] Email configured for receipts

---

## Step 1: Install WordPress & WooCommerce (10 minutes)

### Install WooCommerce

1. Go to **Plugins → Add New**
2. Search for **"WooCommerce"**
3. Click **"Install Now"** on WooCommerce by Automattic
4. Click **"Activate"**
5. Follow WooCommerce setup wizard:
   - **Store Details**: Your business information
   - **Industry**: Software/Digital Products
   - **Product Types**: Downloads
   - **Business Details**: Complete as needed
   - **Theme**: Choose any (or skip)
   - **Payment**: Set up PayPal and/or Stripe
   - **Shipping**: Not needed (skip)
   - **Tax**: Configure based on your location

---

## Step 2: Install Software License Manager (5 minutes)

### Install the Plugin

1. Go to **Plugins → Add New**
2. Search for **"Software License Manager"**
3. Click **"Install Now"** on "Software License Manager" by Tips and Tricks HQ
4. Click **"Activate"**

### Configure License Manager

1. Go to **Settings → Software License Manager**
2. **General Settings** tab:
   - **License Key Prefix**: `NEXUS-`
   - **License Key Length**: `32`
   - **License Key Separator**: `-` (every 8 characters)
   - **Enable API**: ✅ Check this
   - **API Secret**: Generate a secure random string (save this!)
3. **Email Settings** tab:
   - Configure license delivery email template
   - Use `{license_key}` placeholder
4. Click **"Save Changes"**

---

## Step 3: Create Products (10 minutes)

### Create Nexus Pro Product

1. Go to **Products → Add New**

2. **Product Details:**
   - **Product name**: Nexus Pro Theme License
   - **Regular price**: $199
   - **Product type**: Simple product
   - **Virtual**: ✅ Check (no shipping)
   - **Downloadable**: ✅ Check

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

4. **Short Description:**
   ```
   Premium features for professional WordPress sites.
   $199/year - Cloud storage, payments, template sync.
   ```

5. **Product Data → Software License Manager:**
   - **Create License**: ✅ Yes
   - **License Type**: `pro`
   - **License Validity**: `365` days
   - **Max Domains**: `1`
   - **Renewal**: ✅ Enable (optional)

6. **Downloadable Files:**
   - **File name**: Nexus Theme v1.6.0
   - **File URL**: `https://github.com/jdram82/nexus/archive/refs/tags/v1.6.0.zip`

7. **Attributes** (for filtering):
   - **Tier**: Pro
   - **Sites**: 1 site

8. Click **"Publish"**

### Create Nexus Advanced Product

Repeat above with:
- **Price**: $299/year
- **License Type**: `advanced`
- **Description**: Include AI, SEO, Theme Builder, Forms
- **Max Domains**: `3`

### Create Nexus Agency Product

Repeat above with:
- **Price**: $599/year  
- **License Type**: `agency`
- **Description**: Include White Label, A/B Testing, Analytics
- **Max Domains**: `10`

---

## Step 4: Configure API Endpoints (5 minutes)

### Get API Credentials

1. Go to **Settings → Software License Manager → API**
2. Copy your **API Secret Key**
3. Note the API endpoint: `https://yoursite.com/wp-json/slm/v1/`

### Test API Endpoint

Open browser and visit:
```
https://yoursite.com/wp-json/slm/v1/
```

Should return:
```json
{
  "name": "Software License Manager API",
  "version": "1.0",
  "endpoints": {...}
}
```

---

## Step 5: Update Nexus Theme (5 minutes)

### Update License Server URL

Edit the file: `/inc/class-nexus-license-manager.php`

Find line 24:
```php
private $license_server = 'https://yoursite.com/wp-json/nexus-licenses/v1/';
```

Change to:
```php
private $license_server = 'https://YOURSITE.com/wp-json/slm/v1/';
```

**Replace `YOURSITE.com` with your actual domain!**

### Commit and Push

```bash
cd /workspaces/codespaces-blank/nexus-theme
git add inc/class-nexus-license-manager.php
git commit -m "Update license server URL to production"
git push origin main
```

---

## Step 6: Create Custom API Wrapper (Optional but Recommended)

The Software License Manager plugin has different endpoints than our theme expects. Create a wrapper:

### Create API Wrapper Plugin

Create: `wp-content/plugins/nexus-license-api/nexus-license-api.php`

```php
<?php
/**
 * Plugin Name: Nexus License API Wrapper
 * Description: Wrapper for Software License Manager to work with Nexus theme
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Nexus_License_API_Wrapper {
    
    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }
    
    public function register_routes() {
        register_rest_route( 'nexus-licenses/v1', '/activate', array(
            'methods' => 'POST',
            'callback' => array( $this, 'activate_license' ),
            'permission_callback' => '__return_true',
        ) );
        
        register_rest_route( 'nexus-licenses/v1', '/validate', array(
            'methods' => 'POST',
            'callback' => array( $this, 'validate_license' ),
            'permission_callback' => '__return_true',
        ) );
        
        register_rest_route( 'nexus-licenses/v1', '/deactivate', array(
            'methods' => 'POST',
            'callback' => array( $this, 'deactivate_license' ),
            'permission_callback' => '__return_true',
        ) );
    }
    
    public function activate_license( $request ) {
        $license_key = sanitize_text_field( $request->get_param( 'license_key' ) );
        $site_url = sanitize_text_field( $request->get_param( 'site_url' ) );
        
        // Call Software License Manager API
        $slm_response = wp_remote_post( home_url( '/wp-json/slm/v1/activate' ), array(
            'body' => array(
                'license_key' => $license_key,
                'registered_domain' => $site_url,
                'item_reference' => 'nexus-theme',
            ),
        ) );
        
        if ( is_wp_error( $slm_response ) ) {
            return new WP_Error( 'api_error', $slm_response->get_error_message(), array( 'status' => 500 ) );
        }
        
        $slm_data = json_decode( wp_remote_retrieve_body( $slm_response ), true );
        
        if ( ! empty( $slm_data['result'] ) && 'success' === $slm_data['result'] ) {
            // Get license details to determine tier
            $license_data = $this->get_license_data( $license_key );
            
            return array(
                'success' => true,
                'tier' => $license_data['tier'],
                'expires' => $license_data['expires'],
                'message' => 'License activated successfully',
            );
        } else {
            return new WP_Error(
                'activation_failed',
                ! empty( $slm_data['message'] ) ? $slm_data['message'] : 'License activation failed',
                array( 'status' => 400 )
            );
        }
    }
    
    public function validate_license( $request ) {
        $license_key = sanitize_text_field( $request->get_param( 'license_key' ) );
        $site_url = sanitize_text_field( $request->get_param( 'site_url' ) );
        
        // Call Software License Manager API
        $slm_response = wp_remote_get( home_url( '/wp-json/slm/v1/check' ), array(
            'body' => array(
                'license_key' => $license_key,
                'registered_domain' => $site_url,
            ),
        ) );
        
        if ( is_wp_error( $slm_response ) ) {
            return new WP_Error( 'api_error', $slm_response->get_error_message(), array( 'status' => 500 ) );
        }
        
        $slm_data = json_decode( wp_remote_retrieve_body( $slm_response ), true );
        
        if ( ! empty( $slm_data['result'] ) && 'success' === $slm_data['result'] ) {
            $license_data = $this->get_license_data( $license_key );
            
            return array(
                'success' => true,
                'status' => 'active',
                'tier' => $license_data['tier'],
                'expires' => $license_data['expires'],
            );
        } else {
            return array(
                'success' => false,
                'status' => 'inactive',
                'tier' => 'free',
                'expires' => 0,
            );
        }
    }
    
    public function deactivate_license( $request ) {
        $license_key = sanitize_text_field( $request->get_param( 'license_key' ) );
        $site_url = sanitize_text_field( $request->get_param( 'site_url' ) );
        
        // Call Software License Manager API
        $slm_response = wp_remote_post( home_url( '/wp-json/slm/v1/deactivate' ), array(
            'body' => array(
                'license_key' => $license_key,
                'registered_domain' => $site_url,
            ),
        ) );
        
        return array(
            'success' => true,
            'message' => 'License deactivated',
        );
    }
    
    private function get_license_data( $license_key ) {
        global $wpdb;
        
        // Query SLM database
        $license = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}lic_key_tbl WHERE license_key = %s",
            $license_key
        ) );
        
        if ( ! $license ) {
            return array(
                'tier' => 'free',
                'expires' => 0,
            );
        }
        
        // Determine tier from license type or product
        $tier = 'pro'; // Default
        if ( ! empty( $license->lic_type ) ) {
            $tier = strtolower( $license->lic_type );
        }
        
        // Calculate expiration
        $expires = 0;
        if ( ! empty( $license->date_expiry ) && '0000-00-00' !== $license->date_expiry ) {
            $expires = strtotime( $license->date_expiry );
        }
        
        return array(
            'tier' => $tier,
            'expires' => $expires,
        );
    }
}

new Nexus_License_API_Wrapper();
```

**Activate this plugin** on your sales site.

---

## Step 7: Test the Complete Flow (15 minutes)

### Test Purchase Flow

1. Go to your shop page
2. Add "Nexus Pro" to cart
3. Complete checkout (use test payment)
4. Check email for license key
5. Verify license appears in **WP Admin → Software Licenses**

### Test License Activation

1. On a **different WordPress site** (test/staging):
2. Install Nexus theme
3. Go to **Appearance → License**
4. Enter license key from purchase
5. Click **"Activate License"**
6. Should see: ✅ "License Active!"
7. Verify tier shows as "PRO"

### Test Features Unlock

1. Go to **Nexus Pro → Cloud Storage**
2. Should be accessible (not showing upgrade notice)
3. Try other Pro features
4. Verify they load without license warnings

### Test Validation

1. Wait 1 minute
2. License should auto-validate
3. Check **Appearance → License**
4. Should still show "Active"

### Test Deactivation

1. Click **"Deactivate License"**
2. Features should be disabled
3. Upgrade notices should appear
4. Re-activate to restore features

---

## Step 8: Configure Recurring Payments (Optional)

### Enable Subscriptions

1. Install **WooCommerce Subscriptions** extension ($199)
2. Or use **WooCommerce Payments** (includes subscriptions)
3. Convert products to subscription products:
   - **Billing Period**: Every 1 year
   - **Sign-up Fee**: $0
   - **Free Trial**: 0 days (or 14-day trial)

### License Renewal

Software License Manager will:
- Auto-renew licenses when subscription renews
- Deactivate licenses when subscription expires
- Send renewal reminders

---

## ✅ Final Checklist

- [ ] WooCommerce installed and configured
- [ ] Software License Manager installed
- [ ] API enabled and tested
- [ ] 3 products created (Pro, Advanced, Agency)
- [ ] License types set correctly
- [ ] Download files linked to GitHub
- [ ] API wrapper plugin created and activated
- [ ] Nexus theme updated with license server URL
- [ ] Test purchase completed
- [ ] Test license activation works
- [ ] Test features unlock correctly
- [ ] Test deactivation works
- [ ] Payment gateway configured
- [ ] Email templates customized
- [ ] SSL certificate active

---

## 🚀 You're Live!

Your license server is now operational. Users can:

1. **Purchase** licenses from your site
2. **Download** theme from GitHub or your site
3. **Activate** license in WordPress
4. **Use** premium features
5. **Get updates** automatically

---

## 📞 Support Resources

**Software License Manager:**
- [Documentation](https://www.tipsandtricks-hq.com/software-license-manager-plugin-for-wordpress)
- [API Reference](https://www.tipsandtricks-hq.com/ecommerce/software-license-manager/api-documentation)

**WooCommerce:**
- [Documentation](https://woocommerce.com/documentation/)
- [Subscriptions Guide](https://woocommerce.com/document/subscriptions/)

**Nexus Theme:**
- [License Protection Guide](docs/LICENSE_PROTECTION_GUIDE.md)

---

## 🔧 Troubleshooting

**License activation fails:**
- Check API endpoint is accessible
- Verify license exists in database
- Check domain is correctly registered
- Enable WordPress debug logging

**Features not unlocking:**
- Verify license tier matches feature requirements
- Check license status is "active"
- Clear WordPress cache
- Check license_data in wp_options table

**API not responding:**
- Check permalink settings (flush)
- Verify REST API is enabled
- Check .htaccess file
- Test with Postman/curl

---

**Setup Time:** ~30-45 minutes  
**Monthly Cost:** $0 (using free plugins) + hosting  
**Revenue Potential:** $10,000+/year

Your license server is production-ready! 🎉
