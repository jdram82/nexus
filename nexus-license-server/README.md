# Nexus License Server Plugin

Complete license management system for the Nexus WordPress theme. Handles license generation, activation, validation, and provides an admin interface for managing all licenses.

## Features

✅ **License Generation** - Create licenses for Pro, Advanced, and Agency tiers  
✅ **Activation Management** - Track which sites are using which licenses  
✅ **Expiration Handling** - Set expiration dates or create lifetime licenses  
✅ **Multi-Site Support** - Control how many sites can use each license  
✅ **REST API** - Modern REST endpoints for license validation  
✅ **Legacy API** - Query parameter support for backward compatibility  
✅ **Admin Dashboard** - Beautiful interface for managing all licenses  
✅ **Statistics** - Real-time overview of active, expired, and total licenses  

## Installation

### Option 1: Upload via WordPress Admin

1. Download or create a ZIP file of this plugin directory
2. Go to WordPress Admin → Plugins → Add New
3. Click "Upload Plugin"
4. Select the ZIP file
5. Click "Install Now" then "Activate"

### Option 2: Manual Upload

1. Upload the `nexus-license-server` folder to `/wp-content/plugins/`
2. Go to WordPress Admin → Plugins
3. Activate "Nexus License Server"

### Option 3: From Repository (Development)

```bash
cd /path/to/wordpress/wp-content/plugins/
cp -r /path/to/nexus-theme/nexus-license-server ./
```

Then activate via WordPress admin.

## Configuration

### 1. Plugin Activation

When you activate the plugin, it will automatically:
- Create the `wp_nexus_licenses` database table
- Generate 3 sample licenses (Pro, Advanced, Agency)
- Set up all API endpoints

### 2. Configure Nexus Theme

Update the Nexus theme to use your license server:

**File:** `/wp-content/themes/nexus-theme/inc/class-nexus-license-manager.php`

**Line 24:** Change license server URL
```php
private $license_server = 'https://yourdomain.com/';
```

**Line 29:** Keep legacy API enabled
```php
private $use_legacy_api = true;
```

### 3. Test Connection

1. Install Nexus theme on a different WordPress site (customer site)
2. Go to Appearance → License
3. Enter one of the sample license keys
4. Click "Activate License"
5. Should show "License activated successfully!"

## Usage

### Admin Interface

Access the license management dashboard:

**WordPress Admin → Nexus Licenses**

### Generate New License

1. Go to Nexus Licenses admin page
2. Fill out the form:
   - **Tier:** Pro ($199), Advanced ($299), or Agency ($599)
   - **Customer Name:** Full name of customer
   - **Customer Email:** Customer's email address
   - **Max Activations:** Number of sites (1-999, or 999 for unlimited)
   - **Expiration Date:** When license expires (blank = lifetime)
3. Click "Generate License"
4. Copy the license key and send to customer

### Manage Existing Licenses

#### View All Licenses
- See complete list with status, tier, activations, and expiration
- Search by license key, customer name, or email
- Filter by tier or status

#### Suspend/Activate License
- Click "Suspend" to disable a license temporarily
- Click "Activate" to re-enable a suspended license
- Useful for non-payment or abuse

#### Delete License
- Click "Delete" to permanently remove a license
- Cannot be undone
- Customer will lose access immediately

### Statistics Dashboard

View real-time stats:
- **Total Licenses:** All licenses in system
- **Active Licenses:** Currently active and valid
- **Expired Licenses:** Past expiration date
- **Total Activations:** Sum of all site activations

## API Endpoints

### Legacy API (Query Parameters)

**Activate:**
```
POST https://yourdomain.com/?nexus_api_action=activate
Body: license_key, site_url, theme_version
```

**Validate:**
```
POST https://yourdomain.com/?nexus_api_action=validate
Body: license_key, site_url, theme_version
```

**Deactivate:**
```
POST https://yourdomain.com/?nexus_api_action=deactivate
Body: license_key, site_url
```

### REST API

**Activate:**
```
POST https://yourdomain.com/wp-json/nexus-licenses/v1/activate
Body: license_key, site_url, theme_version
```

**Validate:**
```
POST https://yourdomain.com/wp-json/nexus-licenses/v1/validate
Body: license_key, site_url, theme_version
```

**Deactivate:**
```
POST https://yourdomain.com/wp-json/nexus-licenses/v1/deactivate
Body: license_key, site_url
```

## Database Schema

### Table: `wp_nexus_licenses`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `license_key` | varchar(100) | Unique license key (XXXX-XXXX-XXXX-XXXX) |
| `tier` | varchar(20) | pro, advanced, or agency |
| `status` | varchar(20) | active, suspended, or inactive |
| `customer_email` | varchar(100) | Customer's email address |
| `customer_name` | varchar(100) | Customer's full name |
| `site_url` | varchar(255) | Currently activated site URL |
| `activations` | int | Number of times activated |
| `max_activations` | int | Maximum allowed activations |
| `created_at` | datetime | When license was created |
| `expires_at` | datetime | When license expires (NULL = lifetime) |
| `last_validated` | datetime | Last validation check |
| `notes` | text | Internal notes |

## WooCommerce Integration

To automatically generate licenses on purchase:

### 1. Install WooCommerce

```bash
WordPress Admin → Plugins → Add New → Search "WooCommerce" → Install & Activate
```

### 2. Create Products

Create 3 products:
- **Nexus Pro** - $199/year (Subscription)
- **Nexus Advanced** - $299/year (Subscription)
- **Nexus Agency** - $599/year (Subscription)

### 3. Add Automation Hook

Add this code to your theme's `functions.php` or a custom plugin:

```php
add_action('woocommerce_order_status_completed', 'nexus_generate_license_on_purchase');

function nexus_generate_license_on_purchase($order_id) {
    $order = wc_get_order($order_id);
    
    // Check if license already generated
    if ($order->get_meta('_nexus_license_key')) {
        return;
    }
    
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        $product_name = $product->get_name();
        
        // Determine tier from product name
        $tier = 'pro';
        if (stripos($product_name, 'agency') !== false) {
            $tier = 'agency';
        } elseif (stripos($product_name, 'advanced') !== false) {
            $tier = 'advanced';
        }
        
        // Determine activations
        $max_activations = ($tier === 'agency') ? 999 : 1;
        
        // Get customer info
        $customer_email = $order->get_billing_email();
        $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
        
        // Generate license
        $license_server = Nexus_License_Server::instance();
        $license_key = $license_server->generate_license(
            $tier,
            $customer_email,
            $customer_name,
            $max_activations,
            date('Y-m-d H:i:s', strtotime('+1 year'))
        );
        
        // Save to order
        $order->add_meta_data('_nexus_license_key', $license_key);
        $order->add_meta_data('_nexus_license_tier', $tier);
        $order->save();
        
        // Send email to customer
        wp_mail(
            $customer_email,
            'Your Nexus Theme License Key',
            "Thank you for your purchase!\n\nYour license key: {$license_key}\n\nTier: " . strtoupper($tier) . "\n\nActivate at: Appearance → License"
        );
    }
}
```

## Security

### Best Practices

1. **HTTPS Only** - Always use HTTPS for your license server
2. **API Rate Limiting** - Consider adding rate limiting to prevent abuse
3. **Backup Database** - Regularly backup the licenses table
4. **Secure API Keys** - Never expose license keys in public repositories

### Firewall Rules (Optional)

If using a firewall, whitelist these endpoints:
- `/?nexus_api_action=*`
- `/wp-json/nexus-licenses/v1/*`

## Troubleshooting

### License Activation Fails

**Problem:** "License activation failed" error

**Solutions:**
1. Check that license server URL is correct in Nexus theme
2. Verify legacy API is enabled (`$use_legacy_api = true`)
3. Check that customer site can reach your server (test with ping/curl)
4. Verify license key exists and status is "active"
5. Check license hasn't expired

### "Invalid license key"

**Problem:** License key not found

**Solutions:**
1. Verify license key was typed correctly (copy/paste recommended)
2. Check database to ensure license exists
3. Look for typos or extra spaces

### "License activation limit reached"

**Problem:** Customer trying to activate on too many sites

**Solutions:**
1. Deactivate from old site first (Appearance → License → Deactivate)
2. Or increase `max_activations` in database
3. Or upgrade to higher tier (Agency = unlimited)

### Database Table Not Created

**Problem:** Plugin activated but no licenses table

**Solutions:**
1. Deactivate and reactivate plugin
2. Check database user has CREATE TABLE permissions
3. Manually run SQL from plugin activation function

## Support

- **Documentation:** [https://jdsandigitel.com/docs/nexus-theme](https://jdsandigitel.com/docs/nexus-theme)
- **Support:** [support@jdsandigitel.com](mailto:support@jdsandigitel.com)
- **GitHub:** [https://github.com/jdram82/nexus](https://github.com/jdram82/nexus)

## Changelog

### Version 1.0.0 - 2026-02-13
- Initial release
- License generation system
- Activation/validation/deactivation API
- Admin dashboard with statistics
- REST API and legacy API support
- WooCommerce integration ready
- Sample licenses on activation

## License

GPL v2 or later - Same as WordPress

## Credits

Developed by Jdsan Digitel for the Nexus WordPress Theme ecosystem.
