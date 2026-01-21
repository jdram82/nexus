# 📧 EMAIL SYSTEM SETUP GUIDE
**Time Required:** 30-60 minutes  
**Difficulty:** Easy

---

## 🎯 What You'll Set Up

Automated emails for:
1. ✅ **Welcome Email** - New user registration
2. ✅ **License Delivery** - After purchase (with license key)
3. ✅ **Bug Report Confirmation** - When user submits bug
4. ✅ **Feature Request Confirmation** - When user submits feature
5. ✅ **Payment Receipt** - Transaction confirmation

---

## 📋 STEP 1: Install WP Mail SMTP Plugin (10 minutes)

### Option A: Via WordPress Dashboard (Easiest)

1. **Go to:** WordPress Admin → Plugins → Add New
2. **Search for:** "WP Mail SMTP"
3. **Install:** WP Mail SMTP by WPForms
4. **Activate** the plugin

### Option B: Manual Installation

1. Download from: https://wordpress.org/plugins/wp-mail-smtp/
2. Upload to `/wp-content/plugins/`
3. Activate in WordPress

---

## 📋 STEP 2: Choose Email Provider

Pick ONE of these options based on your needs:

### ✅ **Option 1: Gmail (FREE - Best for Testing)**

**Pros:**
- Free
- Easy setup
- Instant activation

**Cons:**
- 500 emails/day limit
- May go to spam
- Requires app password

**Setup Steps:**

1. **Create Gmail App Password:**
   - Go to: https://myaccount.google.com/apppasswords
   - Select app: Mail
   - Select device: Other (Custom name)
   - Name it: "WordPress UL-NEC"
   - Copy the 16-character password

2. **Configure WP Mail SMTP:**
   - Go to: WP Mail SMTP → Settings
   - From Email: your-email@gmail.com
   - From Name: UL/NEC Compliance
   - Mailer: Gmail
   - Client ID: (Leave for now)
   - Return Path: Check box

---

### ✅ **Option 2: SendGrid (FREE Tier - RECOMMENDED)**

**Pros:**
- 100 emails/day FREE
- Professional delivery
- Better deliverability
- Email analytics

**Cons:**
- Requires signup
- API key needed

**Setup Steps:**

1. **Create SendGrid Account:**
   - Go to: https://signup.sendgrid.com/
   - Sign up (FREE account)
   - Verify email

2. **Create API Key:**
   - Login to SendGrid
   - Go to: Settings → API Keys
   - Click: "Create API Key"
   - Name: "WordPress UL-NEC"
   - Permission: Full Access
   - **Copy the API key** (you'll only see it once!)

3. **Configure WP Mail SMTP:**
   - Go to: WP Mail SMTP → Settings
   - From Email: noreply@jdsancontrols.com
   - From Name: UL/NEC Compliance Support
   - Mailer: SendGrid
   - API Key: [Paste your API key]
   - Return Path: Check box

4. **Verify Domain (Optional but Recommended):**
   - SendGrid → Settings → Sender Authentication
   - Follow domain verification steps
   - Add DNS records to Namecheap

---

### ✅ **Option 3: Mailgun (FREE Tier)**

**Pros:**
- 5,000 emails/month FREE
- Great deliverability
- Detailed analytics

**Setup Steps:**

1. **Create Mailgun Account:**
   - Go to: https://signup.mailgun.com/
   - Sign up (FREE trial)

2. **Get API Key:**
   - Login → Settings → API Keys
   - Copy "Private API key"

3. **Get Domain:**
   - Mailgun → Sending → Domains
   - Use sandbox domain or add your own

4. **Configure WP Mail SMTP:**
   - From Email: postmaster@your-domain.com
   - From Name: UL/NEC Compliance
   - Mailer: Mailgun
   - API Key: [Your private key]
   - Domain: [Your Mailgun domain]
   - Region: US or EU

---

## 📋 STEP 3: Test Email Delivery (5 minutes)

1. **Go to:** WP Mail SMTP → Email Test
2. **Send to:** Your email address
3. **Click:** Send Email
4. **Check:** Your inbox (and spam folder)

**✅ If email received:** You're ready!  
**❌ If not received:** 
- Check spam folder
- Verify API key/password
- Check email provider settings

---

## 📋 STEP 4: Create Email Templates (20-30 minutes)

### Create Custom Email Templates

**Location:** WP Mail SMTP → Email Log → Settings

Or use plugin: [Email Templates](https://wordpress.org/plugins/kadence-emails/)

---

### Template 1: Welcome Email

**Subject:** Welcome to UL/NEC Compliance Checker - Beta Access

**Body:**
```html
<div style="background: #1a1f3a; padding: 40px; font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px;">
        <h1 style="color: #667eea; margin-bottom: 20px;">Welcome to UL/NEC Compliance Checker!</h1>
        
        <p style="color: #1a1f3a; font-size: 16px; line-height: 1.6;">
            Hi {{user_name}},
        </p>
        
        <p style="color: #1a1f3a; font-size: 16px; line-height: 1.6;">
            Thank you for joining our Beta program! Your account has been successfully created.
        </p>
        
        <div style="background: #f9fafb; padding: 20px; border-radius: 10px; margin: 20px 0;">
            <h3 style="color: #1a1f3a; margin-top: 0;">🚀 Next Steps:</h3>
            <ol style="color: #6b7280; line-height: 1.8;">
                <li>Complete your profile in Account Settings</li>
                <li>Purchase a license or apply for Beta access</li>
                <li>Download the AutoCAD plugin</li>
                <li>Start checking your electrical drawings!</li>
            </ol>
        </div>
        
        <a href="https://jdsancontrols.com/account-settings/" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0;">
            Go to Dashboard
        </a>
        
        <p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
            Questions? Reply to this email or visit our support page.
        </p>
    </div>
</div>
```

---

### Template 2: License Delivery Email

**Subject:** Your UL/NEC Compliance License Key

**Body:**
```html
<div style="background: #1a1f3a; padding: 40px; font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px;">
        <h1 style="color: #667eea; margin-bottom: 20px;">🎉 License Activated!</h1>
        
        <p style="color: #1a1f3a; font-size: 16px; line-height: 1.6;">
            Hi {{user_name}},
        </p>
        
        <p style="color: #1a1f3a; font-size: 16px; line-height: 1.6;">
            Your payment has been processed successfully. Here's your license key:
        </p>
        
        <div style="background: #fbbf24; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;">
            <p style="color: #000; font-size: 14px; margin: 0; font-weight: bold;">Your License Key:</p>
            <h2 style="color: #000; font-family: 'Courier New', monospace; margin: 10px 0; font-size: 24px; letter-spacing: 2px;">
                {{license_key}}
            </h2>
        </div>
        
        <div style="background: #f9fafb; padding: 20px; border-radius: 10px; margin: 20px 0;">
            <h3 style="color: #1a1f3a; margin-top: 0;">📥 Download & Install:</h3>
            <ol style="color: #6b7280; line-height: 1.8;">
                <li>Download the .msi installer from your dashboard</li>
                <li>Run the installer on your computer</li>
                <li>Enter your license key when prompted</li>
                <li>Start using in AutoCAD!</li>
            </ol>
        </div>
        
        <a href="https://jdsancontrols.com/billing/" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0;">
            Download Plugin
        </a>
        
        <p style="color: #6b7280; font-size: 14px; margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 20px;">
            <strong>License Details:</strong><br>
            Tier: {{license_tier}}<br>
            Status: Active<br>
            Expires: {{expiry_date}}
        </p>
    </div>
</div>
```

---

### Template 3: Bug Report Confirmation

**Subject:** Bug Report Received - {{bug_id}}

**Body:**
```html
<div style="background: #1a1f3a; padding: 40px; font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px;">
        <h1 style="color: #ef4444; margin-bottom: 20px;">🐛 Bug Report Received</h1>
        
        <p style="color: #1a1f3a; font-size: 16px; line-height: 1.6;">
            Hi {{user_name}},
        </p>
        
        <p style="color: #1a1f3a; font-size: 16px; line-height: 1.6;">
            Thank you for reporting this bug. We've received your report and our team will review it shortly.
        </p>
        
        <div style="background: #fef3c7; padding: 20px; border-radius: 10px; margin: 20px 0;">
            <p style="color: #92400e; margin: 0; font-weight: bold;">Bug ID: {{bug_id}}</p>
            <p style="color: #92400e; margin: 10px 0 0 0;">Title: {{bug_title}}</p>
        </div>
        
        <p style="color: #6b7280; font-size: 14px; line-height: 1.6;">
            We'll email you when there's an update on your bug report.
        </p>
        
        <a href="https://jdsancontrols.com/founders-progress/" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0;">
            Track Progress
        </a>
    </div>
</div>
```

---

## 📋 STEP 5: Configure WordPress Emails

Add this to your theme's `functions.php` or create a custom plugin:

```php
// Customize email headers
add_filter('wp_mail_from', function($email) {
    return 'noreply@jdsancontrols.com';
});

add_filter('wp_mail_from_name', function($name) {
    return 'UL/NEC Compliance Support';
});

// HTML email content type
add_filter('wp_mail_content_type', function() {
    return 'text/html';
});
```

---

## ✅ VERIFICATION CHECKLIST

After setup, test each email type:

- [ ] Welcome email sent on registration
- [ ] License email sent after payment
- [ ] Bug report confirmation sent
- [ ] Feature request confirmation sent
- [ ] All emails land in inbox (not spam)
- [ ] Emails display correctly on mobile
- [ ] All links work
- [ ] Unsubscribe link included (if required)

---

## 🚨 TROUBLESHOOTING

**Emails go to spam?**
- Set up SPF/DKIM records in Namecheap
- Use a verified sender domain
- Avoid spam trigger words

**Emails not sending?**
- Check WP Mail SMTP → Email Log
- Verify API key/credentials
- Test with different email provider

**502/504 Errors?**
- Increase PHP max_execution_time
- Use background processing for emails

---

## 📈 MONITORING

**Track Email Performance:**
- WP Mail SMTP → Email Log
- SendGrid Dashboard (if using SendGrid)
- Mailgun Analytics (if using Mailgun)

**Key Metrics:**
- Delivery rate (should be >95%)
- Open rate (should be >20%)
- Bounce rate (should be <5%)

---

## 🔒 SECURITY BEST PRACTICES

1. ✅ Never expose API keys in code
2. ✅ Use environment variables
3. ✅ Enable 2FA on email provider
4. ✅ Regularly rotate API keys
5. ✅ Monitor for suspicious activity

---

## ⏱️ TIME ESTIMATE

| Task | Time |
|------|------|
| Install plugin | 5 min |
| Configure provider | 10 min |
| Test delivery | 5 min |
| Create templates | 20-30 min |
| Testing | 10 min |
| **Total** | **50-60 min** |

---

## 🎯 RECOMMENDED: SendGrid Setup

**Why SendGrid for Beta:**
- Professional appearance
- Better deliverability
- Email analytics
- Scales easily
- Free tier sufficient for beta

**Quick Setup:**
```
1. Sign up at SendGrid.com
2. Create API key with Full Access
3. Add to WP Mail SMTP
4. Send test email
5. Done!
```

---

**Next Step After Email Setup:** Upload .msi file to Supabase Storage

**Need Help?** Check WP Mail SMTP documentation or contact support@wpforms.com
