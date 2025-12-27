# Nexus License Protection System - Implementation Guide

## 🎯 Strategy: Public Code + License Validation

Your Nexus theme is **GPL licensed** (WordPress requirement) but premium features require **active subscription/license** to function.

### ✅ What This Achieves

- Public GitHub repository (builds trust, allows automatic updates)
- Anyone can see and study the code
- Premium features visible but won't work without license
- Revenue protected through license validation
- GPL compliant

---

## 🔐 How It Works

### License Tiers

| Tier | Price | Features | License Check |
|------|-------|----------|---------------|
| **Free** | $0 | Basic theme, no license needed | None |
| **Pro** | $199/year | Cloud storage, payments, template sync | Validated daily |
| **Advanced** | $299/year | Theme builder, AI, SEO, forms | Validated daily |
| **Agency** | $599/year | White label, A/B testing, analytics | Validated daily |

### License Validation Flow

```
User Activates License
    ↓
WordPress calls YOUR server API
    ↓
Your server checks:
  - Is license key valid?
  - Is it assigned to this domain?
  - Is subscription active?
  - Has it expired?
    ↓
Return: {success: true, tier: "pro", expires: timestamp}
    ↓
WordPress stores license data locally
    ↓
Premium features check license before loading
    ↓
If no valid license → Show upgrade message
```

---

## 📁 Files Created

### 1. License Manager (Already Created)
**File:** `inc/class-nexus-license-manager.php`

**Features:**
- Validates licenses against YOUR server
- Stores license data in WordPress database
- Daily re-validation (prevents sharing)
- Feature-based access control
- Admin UI for license activation

### 2. Protected Features

**Modified:**
- `pro/cloud/class-cloud-storage.php` - Added license check
- All Pro/Advanced/Agency features need same protection

---

## 🛠️ Step-by-Step Implementation

### Step 1: Load License Manager

Add to `functions.php`:

```php
/**
 * License Manager - Protects Premium Features
 */
require_once NEXUS_DIR . '/inc/class-nexus-license-manager.php';
```

### Step 2: Protect Each Premium Feature

**Example: Cloud Storage (Pro Tier)**

```php
// In pro/cloud/class-cloud-storage.php
private function __construct() {
    // LICENSE CHECK
    $license = Nexus_License_Manager::instance();
    if ( ! $license->has_feature( 'cloud_storage' ) ) {
        // Don't initialize - show upgrade notice
        add_action( 'admin_notices', array( $this, 'show_upgrade_notice' ) );
        return;
    }
    
    // Rest of initialization code...
}

public function show_upgrade_notice() {
    $license = Nexus_License_Manager::instance();
    ?>
    <div class="notice notice-warning">
        <p>
            <?php echo $license->get_upgrade_message( 'cloud_storage' ); ?>
        </p>
    </div>
    <?php
}
```

**Example: Theme Builder (Advanced Tier)**

```php
// In pro/builder/class-header-builder.php
private function __construct() {
    // LICENSE CHECK
    $license = Nexus_License_Manager::instance();
    if ( ! $license->has_feature( 'theme_builder' ) ) {
        add_action( 'admin_notices', array( $this, 'show_upgrade_notice' ) );
        return;
    }
    
    // Initialize builder...
}
```

**Example: White Label (Agency Tier)**

```php
// In pro/agency/class-white-label.php
private function __construct() {
    // LICENSE CHECK
    $license = Nexus_License_Manager::instance();
    if ( ! $license->has_feature( 'white_label' ) ) {
        add_action( 'admin_notices', array( $this, 'show_upgrade_notice' ) );
        return;
    }
    
    // Initialize white label system...
}
```

### Step 3: Apply to ALL Premium Features

Files to protect:

**Pro Tier (Require 'pro' license):**
- ✅ `pro/cloud/class-cloud-storage.php` - Already protected
- [ ] `pro/payment/class-payment-gateway-multi.php` - Add license check
- [ ] `pro/cloud/class-template-cloud-sync.php` - Add license check
- [ ] `pro/credits/class-credit-manager.php` - Add license check

**Advanced Tier (Require 'advanced' license):**
- [ ] `pro/builder/class-header-builder.php`
- [ ] `pro/builder/class-footer-builder.php`
- [ ] `pro/seo/class-seo-manager.php`
- [ ] `pro/performance/class-performance-monitor.php`
- [ ] `pro/ai/class-template-generator.php`
- [ ] `pro/forms/class-form-builder.php`
- [ ] `pro/loop-builder/class-loop-builder.php`
- [ ] `pro/filtering/class-advanced-filtering.php`

**Agency Tier (Require 'agency' license):**
- [ ] `pro/agency/class-white-label.php`
- [ ] `pro/agency/class-agency-dashboard.php`
- [ ] `pro/ab-testing/class-ab-testing.php`
- [ ] `pro/analytics/class-analytics.php`
- [ ] `pro/portal/class-client-portal.php`

---

## 🌐 License Server Setup

You need a server to validate licenses. Two options:

### Option A: WordPress Site with WooCommerce

1. **Set up e-commerce site** (yoursite.com)
2. **Install WooCommerce** + Software License Manager plugin
3. **Create products:**
   - Nexus Pro - $199/year
   - Nexus Advanced - $299/year
   - Nexus Agency - $599/year
4. **Generate license keys** on purchase
5. **API endpoints** for validation

### Option B: Custom License Server

Create REST API endpoints on YOUR domain:

```php
// On yoursite.com/wp-json/nexus-licenses/v1/activate
POST /activate
Body: {
    license_key: "XXXX-XXXX-XXXX-XXXX",
    site_url: "https://customer-site.com",
    theme_version: "1.6.0"
}

Response: {
    success: true,
    tier: "pro",
    expires: 1735689600,
    message: "License activated successfully"
}
```

```php
// On yoursite.com/wp-json/nexus-licenses/v1/validate
POST /validate
Body: {
    license_key: "XXXX-XXXX-XXXX-XXXX",
    site_url: "https://customer-site.com"
}

Response: {
    success: true,
    status: "active",
    tier: "pro",
    expires: 1735689600
}
```

```php
// On yoursite.com/wp-json/nexus-licenses/v1/deactivate
POST /deactivate
Body: {
    license_key: "XXXX-XXXX-XXXX-XXXX",
    site_url: "https://customer-site.com"
}

Response: {
    success: true,
    message: "License deactivated"
}
```

---

## 🔒 Security Best Practices

### 1. Server-Side Validation Only

```php
// ❌ BAD - Can be bypassed
if ( defined( 'NEXUS_LICENSE_KEY' ) ) {
    // Load feature
}

// ✅ GOOD - Must validate with server
$license = Nexus_License_Manager::instance();
if ( $license->has_feature( 'cloud_storage' ) ) {
    // Load feature
}
```

### 2. Daily Re-validation

```php
// Checks server every 24 hours
// Prevents license sharing across sites
// Detects expired/cancelled subscriptions
```

### 3. Domain Locking

```php
// License tied to specific domain
// Can't activate on multiple sites without additional licenses
// Deactivate from one site to move to another
```

### 4. Grace Period

```php
// If validation fails due to network error
// Don't immediately disable (7-day grace)
// Allows temporary connectivity issues
```

---

## 💰 Revenue Protection

### What Users CAN Do (GPL Compliant)

✅ Download and install theme  
✅ View all source code  
✅ Modify code for their own use  
✅ Share modified code (must remain GPL)  
✅ Learn from the code

### What Users CANNOT Do (License Protected)

❌ Use Pro features without license  
❌ Use license on multiple sites  
❌ Continue using after subscription expires  
❌ Remove license checks (but can see code)  
❌ Get updates without active license

### Why License Checks Work

Even though code is visible:
- Features check YOUR server before functioning
- You control who has active licenses
- Expired licenses = features disabled
- Can't fake server responses (signed/encrypted)
- Daily validation prevents sharing

---

## 📊 Real-World Examples

### Astra Pro Theme
- Free version: Public on WordPress.org
- Pro version: Public on GitHub
- Pro features: License-protected
- Revenue: $10M+/year despite public code

### WooCommerce
- Core: Free and public
- Extensions: License-protected
- Revenue: $200M+/year

### Easy Digital Downloads
- Core: Free
- Extensions: License-protected with server validation
- They literally sell a license system plugin!

---

## 🚀 Your Next Steps

### 1. Protect All Premium Features

Add license checks to all Pro/Advanced/Agency features:

```bash
# Example script to add license checks
for file in pro/*/class-*.php; do
    echo "Add license check to $file"
done
```

### 2. Set Up License Server

Choose Option A or B above and implement the API endpoints.

### 3. Update License Manager

Change this line in `inc/class-nexus-license-manager.php`:

```php
private $license_server = 'https://YOURSITE.com/wp-json/nexus-licenses/v1/';
```

### 4. Make Repository Public

```bash
# On GitHub, go to Settings → Change visibility to Public
```

### 5. Update README

Add license information to README.md:

```markdown
## License

Nexus is licensed under GPL v2 or later.

**Premium Features** (Pro, Advanced, Agency tiers) require an active license key from [yoursite.com](https://yoursite.com).

- Free Tier: Fully functional, no license required
- Pro Tier: $199/year - Cloud storage, payments, template sync
- Advanced Tier: $299/year - AI, SEO, theme builder, forms
- Agency Tier: $599/year - White label, A/B testing, client management

Purchase: [yoursite.com/pricing](https://yoursite.com/pricing)
```

### 6. Test License System

```php
// Test activation
1. Go to Appearance → License
2. Enter test license key
3. Verify features unlock
4. Check daily validation works
5. Test expiration handling
```

---

## 📝 Quick Implementation Checklist

- [ ] License Manager loaded in functions.php
- [ ] License server URL configured
- [ ] All Pro features have license checks
- [ ] All Advanced features have license checks
- [ ] All Agency features have license checks
- [ ] Upgrade notices display correctly
- [ ] License activation page works
- [ ] Daily validation cron job active
- [ ] License server API endpoints working
- [ ] Test license activation/deactivation
- [ ] Test feature access with/without license
- [ ] Test expiration handling
- [ ] Update README with license info
- [ ] Make GitHub repository public
- [ ] Create first public release

---

## 🎯 Expected Outcome

After implementation:

### For Users
- ✅ Can download and view code (builds trust)
- ✅ Free tier works perfectly (try before buy)
- ✅ Can study premium feature code (educational)
- ✅ Must purchase license to use premium features
- ✅ Automatic updates work seamlessly

### For You
- ✅ Code is public (GPL compliant)
- ✅ Revenue protected via license validation
- ✅ Can't share licenses across sites
- ✅ Expired subscriptions = features disabled
- ✅ Full control over who accesses what
- ✅ Automatic update system works
- ✅ Trust and credibility from open source

---

## 📞 Support

If you need help implementing:

1. Set up license server
2. Protect all premium features
3. Test license validation
4. Configure for production

**The code is visible, but the VALUE is in:**
- Your updates
- Your support
- Your servers validating licenses
- Your brand and trust
- Your documentation and community

This is how WordPress ecosystem works - and it's worth **billions**! 🚀
