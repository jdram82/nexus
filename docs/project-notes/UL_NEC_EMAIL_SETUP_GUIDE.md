# UL/NEC Email Setup Guide

**Configure Email Notifications with support@jdsandigitel.com**

## Overview

This guide shows you how to configure email notifications for the UL/NEC Compliance Plugin using your existing **support@jdsandigitel.com** email address.

**Time Required:** 30-45 minutes  
**Difficulty:** Easy  
**Prerequisites:** WordPress admin access, SMTP server details

---

## Step 1: Install WP Mail SMTP Plugin (5 minutes)

### 1.1 Install Plugin

1. WordPress Dashboard → **Plugins** → **Add New**
2. Search for **"WP Mail SMTP"**
3. Find "WP Mail SMTP – The Most Popular SMTP and Email Log Plugin" by WPForms
4. Click **Install Now**
5. Click **Activate**

### 1.2 Verify Installation

- You should see "WP Mail SMTP" in left sidebar menu
- A setup wizard may appear (recommended to complete)

---

## Step 2: Configure SMTP Settings (10-15 minutes)

### 2.1 Access Settings

1. WordPress Dashboard → **WP Mail SMTP** → **Settings**
2. Navigate to **General** tab if not already there

### 2.2 Configure From Email

```
From Email: support@jdsandigitel.com
From Name: UL/NEC Support
Force From Email: ✅ Enabled
Force From Name: ✅ Enabled
```

**Why Force From Email?**  
This ensures ALL emails from WordPress use your support address, even if other plugins try to use different sender addresses.

### 2.3 Choose Your SMTP Provider

You have 3 options depending on your hosting/email provider:

---

## Option A: Using cPanel/WHM Email (Recommended if you use standard hosting)

**Best for:** Most shared hosting providers (HostGator, Bluehost, SiteGround, etc.)

### Configuration:

```
Mailer: Other SMTP
SMTP Host: mail.jdsandigitel.com (or your hosting's mail server)
SMTP Port: 587 (recommended) or 465 (SSL)
Encryption: TLS (for port 587) or SSL (for port 465)
Authentication: ON
SMTP Username: support@jdsandigitel.com
SMTP Password: [Your email password]
```

### How to Find SMTP Host:

1. Go to your **cPanel** (usually yourdomain.com/cpanel)
2. Search for **"Email Accounts"**
3. Find support@jdsandigitel.com
4. Click **"Configure Mail Client"**
5. Look for **"Mail Server"** or **"Incoming Server"**
6. Use that value for SMTP Host (usually `mail.yourdomain.com`)

---

## Option B: Using Gmail SMTP (Good for testing/small volume)

**Best for:** Low email volume (< 500/day), testing environments

**⚠️ Limitations:** Gmail limits you to 500 emails per day

### Prerequisites:

1. Create a Google App Password:
   - Go to https://myaccount.google.com/security
   - Enable **2-Step Verification** (required)
   - Go to **App Passwords**
   - Select **Mail** and **Other (Custom name)**
   - Enter "UL/NEC WordPress"
   - Click **Generate**
   - Copy the 16-character password

### Configuration:

```
Mailer: Gmail
SMTP Username: support@jdsandigitel.com
SMTP Password: [16-character App Password from above]
```

**Note:** If support@jdsandigitel.com is not a Gmail account, you'll need to forward it to Gmail or use Option A/C instead.

---

## Option C: Using SendGrid (Professional, high volume)

**Best for:** High email volume, reliability, deliverability tracking

**✅ Recommended for production**

### 3.1 Create SendGrid Account

1. Go to https://sendgrid.com/
2. Click **"Sign Up"** → Choose **Free Plan** (100 emails/day)
3. Verify your email address
4. Complete sender verification

### 3.2 Verify Domain

1. SendGrid Dashboard → **Settings** → **Sender Authentication**
2. Click **Authenticate Your Domain**
3. Choose **jdsandigitel.com**
4. SendGrid will provide DNS records
5. Add these DNS records to your domain:
   ```
   Type: CNAME
   Host: em1234.jdsandigitel.com
   Value: u1234567.wl001.sendgrid.net
   
   Type: CNAME
   Host: s1._domainkey.jdsandigitel.com
   Value: s1.domainkey.u1234567.wl001.sendgrid.net
   
   Type: CNAME
   Host: s2._domainkey.jdsandigitel.com
   Value: s2.domainkey.u1234567.wl001.sendgrid.net
   ```
6. Wait 24-48 hours for DNS propagation
7. Click **Verify** in SendGrid

### 3.3 Create API Key

1. SendGrid Dashboard → **Settings** → **API Keys**
2. Click **Create API Key**
3. Name: "UL/NEC WordPress"
4. Permissions: **Full Access** or **Restricted Access** (Mail Send only)
5. Click **Create & View**
6. **Copy the API key** (you can't view it again!)

### 3.4 Configure WP Mail SMTP

```
Mailer: SendGrid
API Key: [Paste API key from step 3.3]
```

---

## Step 3: Test Email Configuration (5 minutes)

### 3.1 Send Test Email

1. WP Mail SMTP → **Email Test**
2. Enter your email address
3. Click **Send Email**
4. Check your inbox (and spam folder)

### 3.2 Verify Results

**✅ Success:** You should see:
- "Test email was sent successfully!"
- Email in your inbox within 1-2 minutes

**❌ Failed:** Common errors:
- **"Could not authenticate"** → Check username/password
- **"Connection refused"** → Check SMTP host and port
- **"Timed out"** → Your host may block port 587/465, contact support

### 3.3 Check Email Log

1. WP Mail SMTP → **Email Log**
2. Verify test email appears
3. Status should be "Sent"

---

## Step 4: Integrate Email Templates (10-15 minutes)

### 4.1 Copy Email Templates

1. Open **UL_NEC_EMAIL_TEMPLATES.php** (provided separately)
2. Copy all functions to your UL/NEC plugin
3. Recommended location: `/includes/class-ulnec-emails.php`

### 4.2 Send Welcome Email on Registration

Add this code where users register:

```php
// After successful registration
$user_data = array(
    'name' => $user->display_name,
    'email' => $user->user_email,
    'dashboard_url' => home_url( '/account/dashboard' ),
);

$html = ulnec_email_welcome( $user_data );
ulnec_send_email( 
    $user_data['email'], 
    'Welcome to UL/NEC Compliance Checker', 
    $html 
);
```

### 4.3 Send License Delivery on Purchase

Add this code after license generation:

```php
// After license is generated/purchased
$license_data = array(
    'customer_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
    'customer_email' => $order->billing_email,
    'license_key' => $license->key,
    'tier' => $license->tier,
    'expires_at' => date( 'F j, Y', strtotime( $license->expires_at ) ),
    'max_activations' => $license->max_activations,
    'download_url' => home_url( '/account/downloads' ),
);

$html = ulnec_email_license_delivery( $license_data );
ulnec_send_email(
    $license_data['customer_email'],
    'Your UL/NEC License Key',
    $html
);
```

### 4.4 Send Bug Report Confirmation

Add this code after bug submission:

```php
// After bug report is submitted
$bug_data = array(
    'user_name' => $current_user->display_name,
    'user_email' => $current_user->user_email,
    'id' => $bug->id,
    'title' => $bug->title,
    'description' => $bug->description,
    'priority' => $bug->priority,
    'status' => $bug->status,
    'created_at' => $bug->created_at,
    'screenshot_url' => $bug->screenshot_url,
    'track_url' => home_url( '/account/bug-reports/' . $bug->id ),
);

$html = ulnec_email_bug_confirmation( $bug_data );
ulnec_send_email(
    $bug_data['user_email'],
    'Bug Report Received (#' . $bug_data['id'] . ')',
    $html
);
```

### 4.5 Send Feature Request Confirmation

Add this code after feature request:

```php
// After feature request is submitted
$feature_data = array(
    'user_name' => $current_user->display_name,
    'user_email' => $current_user->user_email,
    'id' => $feature->id,
    'title' => $feature->title,
    'description' => $feature->description,
    'category' => $feature->category,
    'vote_url' => home_url( '/account/feature-requests/' . $feature->id ),
);

$html = ulnec_email_feature_confirmation( $feature_data );
ulnec_send_email(
    $feature_data['user_email'],
    'Feature Request Received (#' . $feature_data['id'] . ')',
    $html
);
```

---

## Step 5: Set Up Automated License Expiration Reminders (Optional)

### 5.1 Create Cron Job

Add to your plugin activation hook:

```php
// Schedule daily license expiration check
if ( ! wp_next_scheduled( 'ulnec_check_license_expiration' ) ) {
    wp_schedule_event( time(), 'daily', 'ulnec_check_license_expiration' );
}
```

### 5.2 Create Check Function

```php
add_action( 'ulnec_check_license_expiration', 'ulnec_send_expiration_reminders' );

function ulnec_send_expiration_reminders() {
    global $wpdb;
    
    // Find licenses expiring in 7 days
    $seven_days = date( 'Y-m-d', strtotime( '+7 days' ) );
    
    $expiring_licenses = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}ulnec_licenses 
        WHERE DATE(expires_at) = %s 
        AND status = 'active'
        AND reminder_sent = 0",
        $seven_days
    ) );
    
    foreach ( $expiring_licenses as $license ) {
        $license_data = array(
            'customer_name' => $license->customer_name,
            'customer_email' => $license->customer_email,
            'license_key' => $license->license_key,
            'tier' => $license->tier,
            'expires_at' => $license->expires_at,
            'renewal_url' => home_url( '/renew-license/' . $license->license_key ),
        );
        
        $html = ulnec_email_license_expiring( $license_data );
        $sent = ulnec_send_email(
            $license->customer_email,
            'Your UL/NEC License Expires in 7 Days',
            $html
        );
        
        if ( $sent ) {
            // Mark reminder as sent
            $wpdb->update(
                $wpdb->prefix . 'ulnec_licenses',
                array( 'reminder_sent' => 1 ),
                array( 'id' => $license->id )
            );
        }
    }
}
```

---

## Email Templates Reference

All 6 email templates are included in **UL_NEC_EMAIL_TEMPLATES.php**:

| Template | Function | Trigger |
|----------|----------|---------|
| **Welcome Email** | `ulnec_email_welcome()` | User registration |
| **License Delivery** | `ulnec_email_license_delivery()` | License purchase/generation |
| **Bug Confirmation** | `ulnec_email_bug_confirmation()` | Bug report submission |
| **Feature Confirmation** | `ulnec_email_feature_confirmation()` | Feature request submission |
| **License Expiring** | `ulnec_email_license_expiring()` | 7 days before expiration |
| **Password Reset** | `ulnec_email_password_reset()` | User requests password reset |

---

## Troubleshooting

### Problem: Emails Not Sending

**Check:**
1. WP Mail SMTP → Email Log (any errors?)
2. Verify SMTP credentials are correct
3. Test with Email Test tool
4. Check if port 587/465 is blocked by hosting

**Solution:**
```bash
# Test if port is open (run in terminal)
telnet mail.jdsandigitel.com 587
# If "Connection refused", port is blocked - contact hosting
```

### Problem: Emails Go to Spam

**Solutions:**
1. **Verify domain with SendGrid** (most effective)
2. **Set up SPF record:**
   ```
   Type: TXT
   Host: @
   Value: v=spf1 include:_spf.google.com include:sendgrid.net ~all
   ```
3. **Set up DKIM** (done automatically with SendGrid domain verification)
4. **Set up DMARC record:**
   ```
   Type: TXT
   Host: _dmarc
   Value: v=DMARC1; p=none; rua=mailto:support@jdsandigitel.com
   ```

### Problem: Gmail Blocks Login

**Solutions:**
1. Use **App Passwords** instead of regular password
2. Enable "Less Secure Apps" (not recommended)
3. Switch to SendGrid (better option)

### Problem: SendGrid API Key Invalid

**Solutions:**
1. Regenerate API key in SendGrid
2. Ensure you copied entire key (starts with `SG.`)
3. Check permissions (must include "Mail Send")
4. Try creating new key with Full Access

---

## DNS Records Summary (for SendGrid)

Add these to your domain DNS settings at your domain registrar:

### SPF Record
```
Type: TXT
Host: @
Value: v=spf1 include:sendgrid.net ~all
TTL: 3600
```

### DMARC Record
```
Type: TXT
Host: _dmarc
Value: v=DMARC1; p=none; rua=mailto:support@jdsandigitel.com
TTL: 3600
```

### SendGrid CNAME Records (provided during domain authentication)
```
Type: CNAME
Host: em[####].jdsandigitel.com
Value: u[#######].wl[###].sendgrid.net
TTL: 3600

Type: CNAME
Host: s1._domainkey.jdsandigitel.com
Value: s1.domainkey.u[#######].wl[###].sendgrid.net
TTL: 3600

Type: CNAME
Host: s2._domainkey.jdsandigitel.com
Value: s2.domainkey.u[#######].wl[###].sendgrid.net
TTL: 3600
```

**Note:** Values in brackets `[####]` will be unique to your account and provided by SendGrid.

---

## Testing Checklist

Once configured, test each email type:

- [ ] Welcome email on new registration
- [ ] License delivery email after purchase
- [ ] Bug report confirmation after submission
- [ ] Feature request confirmation after submission
- [ ] License expiration reminder (manually trigger or wait 7 days)
- [ ] Password reset email

**How to Test Without Real Users:**

1. Create test user with your email: `test@jdsandigitel.com`
2. Trigger each function manually in code:
   ```php
   // Test welcome email
   $html = ulnec_email_welcome( array(
       'name' => 'Test User',
       'email' => 'your-email@example.com',
       'dashboard_url' => home_url('/account')
   ));
   ulnec_send_email( 'your-email@example.com', 'Test Welcome', $html );
   ```
3. Check inbox and verify:
   - Email delivered successfully
   - Formatting looks correct
   - Links work
   - Images load (if any)

---

## Best Practices

### Email Deliverability

1. **Always use verified domain** (SendGrid domain authentication)
2. **Include unsubscribe link** (for marketing emails, not required for transactional)
3. **Monitor bounce rate** (check Email Log regularly)
4. **Keep emails under 100KB** (templates are optimized)

### Performance

1. **Don't send emails during page load** - Use background processing:
   ```php
   wp_schedule_single_event( time(), 'send_ulnec_email', array( $email_data ) );
   ```
2. **Batch emails for multiple recipients** (if sending newsletters)
3. **Monitor SendGrid quota** (100/day free, upgrade if needed)

### Security

1. **Never expose SMTP password** in code
2. **Use environment variables** for sensitive data:
   ```php
   define( 'ULNEC_SMTP_PASS', getenv('SMTP_PASSWORD') );
   ```
3. **Validate email addresses** before sending:
   ```php
   if ( ! is_email( $to ) ) {
       return false;
   }
   ```

---

## Support & Resources

### WP Mail SMTP Documentation
- https://wpmailsmtp.com/docs/

### SendGrid Documentation
- Quick Start: https://docs.sendgrid.com/for-developers/sending-email/quickstart-php
- API Reference: https://docs.sendgrid.com/api-reference

### Troubleshooting
- Check WP Mail SMTP → Email Log for errors
- SendGrid Activity Feed shows delivery status
- Use Email Test tool before going live

### Get Help
- **UL/NEC Support:** support@jdsandigitel.com
- **WP Mail SMTP Support:** https://wpmailsmtp.com/support/
- **SendGrid Support:** https://support.sendgrid.com/

---

## Quick Start Summary

**For fastest setup (if using standard hosting):**

1. Install WP Mail SMTP plugin
2. Configure:
   - From: support@jdsandigitel.com
   - SMTP Host: mail.jdsandigitel.com
   - Port: 587
   - Username: support@jdsandigitel.com
   - Password: [your email password]
3. Send test email
4. Copy templates from UL_NEC_EMAIL_TEMPLATES.php
5. Integrate with your plugin triggers

**Total time:** 30-45 minutes

**For production/high volume (recommended):**

1. Sign up for SendGrid (free tier)
2. Verify jdsandigitel.com domain
3. Create API key
4. Configure WP Mail SMTP with SendGrid
5. Rest of steps same as above

**Total time:** 1-2 hours (mostly waiting for DNS)

---

*Setup Guide v1.0 - February 13, 2026*  
*For UL/NEC Compliance Plugin v1.3.0+*
