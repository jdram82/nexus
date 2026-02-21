# 🚀 COMPLETE SETUP GUIDE - Email + Upload + Testing

**All 3 Critical Tasks in One Place**  
**Time Required:** 1-1.5 hours total  
**Date:** February 21, 2026

> 📧 **Can't access SendGrid?** See [EMAIL_SERVICE_COMPARISON.md](EMAIL_SERVICE_COMPARISON.md) for 5 alternative email services!  
> ⭐ **Quick recommendation:** Use Gmail SMTP (5-minute setup)

---

## 📋 OVERVIEW

This guide covers the final 3 tasks to reach beta launch:

1. ✅ **Configure SMTP** (20-30 minutes) - Choose from 5 email services
2. ✅ **Upload .msi file** (10-15 minutes)
3. ✅ **Test email system** (20-30 minutes)

**After completing this guide:** Plugin will be 98% complete, ready for payment testing!

---

# PART 1: SMTP CONFIGURATION (20-30 minutes)

## 🎯 Choose Your Email Service

Pick ONE option that works for you:
- **Option A: Gmail SMTP** (Easiest - 5 min setup) ⭐ RECOMMENDED
- **Option B: Mailgun** (Free tier - 5,000 emails/month)
- **Option C: Brevo** (300 emails/day free)
- **Option D: SMTP.com** (100 emails/month free)
- **Option E: cPanel/Hosting Email** (If you have hosting)

---

## ⭐ OPTION A: Gmail SMTP (RECOMMENDED - Easiest)

### Step 1.1: Create Gmail App Password (5 minutes)

**Requirements:** You need a Gmail account with 2-Factor Authentication enabled

1. **Enable 2FA (if not already enabled):**
   - Go to: https://myaccount.google.com/security
   - Find "2-Step Verification"
   - Click "Get Started" and follow steps
   - (If already enabled, skip to step 2)

2. **Create App Password:**
   - Go to: https://myaccount.google.com/apppasswords
   - OR Google Account → Security → 2-Step Verification → App passwords
   - Select app: **Mail**
   - Select device: **Other (Custom name)**
   - Enter name: `WordPress UL-NEC`
   - Click **Generate**

3. **Copy the 16-character password:**
   ```
   Example: abcd efgh ijkl mnop
   ```
   - **COPY & SAVE IT** (shows only once!)
   - Remove spaces when pasting: `abcdefghijklmnop`

✅ **Done:** Gmail App Password created

**Skip to Step 1.8** (Install WP Mail SMTP)

---

## OPTION B: Mailgun (5,000 emails/month free)

### Step 1.2: Create Mailgun Account (8 minutes)

1. Go to: https://signup.mailgun.com/
2. Click **"Start Free"**
3. Fill out signup form
4. Verify your email address
5. Complete profile
6. Login to Mailgun Dashboard
7. Left sidebar → **Sending** → **Domain settings**
8. Find your sandbox domain (starts with `sandbox`)
9. Click on the domain name

### Step 1.3: Get Mailgun SMTP Credentials

1. On domain details page, note:
   - **SMTP Hostname:** smtp.mailgun.org
   - **Port:** 587
   - **SMTP Username:** (shown on page, looks like: postmaster@sandbox...)
2. **SMTP Password:**
   - Click "Reset Password" button
   - Copy the new password
   - **SAVE IT** (won't show again)

✅ **Done:** Mailgun credentials ready

**Skip to Step 1.8** (Install WP Mail SMTP)

---

## OPTION C: Brevo (300 emails/day free)

### Step 1.4: Create Brevo Account (8 minutes)

1. Go to: https://www.brevo.com/
2. Click **"Sign up free"**
3. Fill out form and verify email
4. Complete onboarding

### Step 1.5: Get Brevo SMTP Credentials

1. **Login to Brevo Dashboard**
2. Click your name (top right) → **SMTP & API**
3. Scroll to **SMTP** section
4. Click **"Create a new SMTP key"**
5. Name: `WordPress UL-NEC`
6. Click **"Generate"**
7. **Copy these values:**
   - **SMTP Server:** smtp-relay.brevo.com
   - **Port:** 587
   - **Login:** (your email address)
   - **SMTP Key:** (the generated key - save it!)

✅ **Done:** Brevo credentials ready

**Skip to Step 1.8** (Install WP Mail SMTP)

---

## OPTION D: SMTP.com (100 emails/month free)

### Step 1.6: SMTP.com Setup (5 minutes)

1. Go to: https://www.smtp.com/
2. Click **"Free Trial"**
3. Sign up and verify email
4. Dashboard → **SMTP Credentials**
5. **Copy values:**
   - **Server:** smtp.smtp.com
   - **Port:** 587
   - **Username:** (shown in dashboard)
   - **Password:** (shown in dashboard)

✅ **Done:** SMTP.com ready

**Skip to Step 1.8** (Install WP Mail SMTP)

---

## OPTION E: cPanel/Hosting Email

### Step 1.7: Get cPanel SMTP Details (5 minutes)

**If you have standard hosting (cPanel/WHM):**

1. Login to your cPanel or ask your hosting provider
2. Look for **Email Accounts** section
3. Create or use existing email: `support@jdsancontrols.com`
4. Note your **SMTP details** (usually):
   - **Server:** mail.jdsancontrols.com
   - **Port:** 587 (TLS) or 465 (SSL)
   - **Username:** support@jdsancontrols.com
   - **Password:** (your email password)

✅ **Done:** cPanel SMTP ready

---

## Step 1.8: Install WP Mail SMTP Plugin (5 minutes)

1. **Login to WordPress:**
   - Go to: https://jdsancontrols.com/wp-admin/
   - Enter your credentials

2. **Install Plugin:**
   - Left sidebar → **Plugins** → **Add New**
   - Search box → type: `WP Mail SMTP`
   - Find: **"WP Mail SMTP by WPForms"** (should be first result)
   - Click **"Install Now"**
   - Wait 3-5 seconds
   - Click **"Activate"**

3. **Verify Installation:**
   - You should see "WP Mail SMTP" in left sidebar menu
   - A setup wizard may appear (we'll configure manually)

✅ **Done:** Plugin installed and activated

---

## Step 1.9: Configure WP Mail SMTP (10 minutes)

1. **Go to Settings:**
   - Left sidebar → **WP Mail SMTP** → **Settings**

2. **From Email Section:**
   ```
   From Email: noreply@jdsancontrols.com
   Force From Email: ✅ (check the box)
   From Name: UL/NEC Support
   Force From Name: ✅ (check the box)
   ```

3. **Select Your Mailer:**
   
   Choose based on which option you completed above:

### If using Gmail (Option A):
```
Mailer: Select "Gmail"
Client ID: (leave blank)
Client Secret: (leave blank)
```
- Then click "Allow plugin to send emails using your Google account"
- OR configure manually:
  - Mailer: Select "Other SMTP"
  - SMTP Host: smtp.gmail.com
  - SMTP Port: 587
  - Encryption: TLS
  - Authentication: ON
  - SMTP Username: your-email@gmail.com
  - SMTP Password: [Your 16-character app password]

### If using Mailgun (Option B):
```
Mailer: Select "Mailgun"
API Key: [Your Mailgun API key]
Domain: [Your sandbox domain]
Region: US or EU (check your account)
```

### If using Brevo (Option C):
```
Mailer: Select "Sendinblue" or "Other SMTP"
If Other SMTP:
  - SMTP Host: smtp-relay.brevo.com
  - SMTP Port: 587
  - Encryption: TLS
  - Authentication: ON
  - SMTP Username: [Your email]
  - SMTP Password: [Your SMTP key]
```

### If using SMTP.com (Option D):
```
Mailer: Select "Other SMTP"
SMTP Host: smtp.smtp.com
SMTP Port: 587
Encryption: TLS
Authentication: ON
SMTP Username: [From dashboard]
SMTP Password: [From dashboard]
```

### If using cPanel (Option E):
```
Mailer: Select "Other SMTP"
SMTP Host: mail.jdsancontrols.com
SMTP Port: 587 (or 465)
Encryption: TLS (or SSL if using 465)
Authentication: ON
SMTP Username: support@jdsancontrols.com
SMTP Password: [Your email password]
```

4. **Email Settings (scroll down):**
   ```
   Return Path: ✅ (check the box)
   ```

5. **Click "Save Settings"** button at bottom

6. **Wait for success message:** "Settings were successfully saved."

✅ **Done:** SMTP configured!

---

## Step 1.10: Test Email Delivery (2-3 minutes)

1. **Go to Email Test:**
   - Top tabs → Click **"Email Test"**

2. **Send Test Email:**
   ```
   Send To: your-personal-email@gmail.com (or whatever you use)
   Custom Subject: Test Email from UL/NEC
   ```

3. **Click "Send Email"** button

4. **Check Results:**
   - Should see green success message
   - Go check your email inbox (and spam folder)

5. **Verify Email:**
   - From: noreply@jdsancontrols.com
   - From Name: UL/NEC Support
   - Subject: Test Email from UL/NEC

✅ **Success!** If email received, SMTP is working!

❌ **Failed?** See troubleshooting section at bottom

---

# PART 2: UPLOAD .MSI FILE (10-15 minutes)

## Step 2.1: Access Supabase Storage (2 minutes)

1. **Login to Supabase:**
   - Go to: https://supabase.com/dashboard
   - Login with your credentials

2. **Select Project:**
   - Find and click your UL-NEC project

3. **Go to Storage:**
   - Left sidebar → Click **"Storage"** icon (looks like a folder)

✅ **Done:** In Supabase Storage

---

## Step 2.2: Verify/Create Storage Bucket (3 minutes)

1. **Check if bucket exists:**
   - Look for bucket named: `ulnec-downloads`

2. **If bucket exists:**
   - Skip to Step 2.3

3. **If bucket doesn't exist:**
   - Click **"New bucket"** button
   - Configure:
     ```
     Name: ulnec-downloads
     Public bucket: NO (leave unchecked)
     File size limit: 100
     Allowed MIME types: (leave empty)
     ```
   - Click **"Create bucket"**

✅ **Done:** Bucket ready

---

## Step 2.3: Upload .MSI File (5 minutes)

1. **Click on `ulnec-downloads` bucket**
   - Should show bucket contents (probably empty)

2. **Click "Upload file" button**
   - File browser opens

3. **Select your .msi file:**
   - Navigate to where you saved: `UL-NEC-Compliance-Plugin.msi`
   - Select it
   - Click "Open"

4. **IMPORTANT: Rename the file:**
   - In Supabase, before finalizing upload
   - Change filename to exactly: `UL-NEC-Compliance-Plugin-Latest.msi`
   - **Must use this exact name!** (plugin code expects it)

5. **Click "Upload" button**
   - Progress bar appears
   - Wait for completion (may take 30-60 seconds depending on file size)

6. **Verify upload:**
   - Should see file listed: `UL-NEC-Compliance-Plugin-Latest.msi`
   - Check file size matches your original

✅ **Done:** .msi file uploaded

---

## Step 2.4: Set File Permissions (3 minutes)

**Option A: RLS Policy (Recommended for Production)**

1. **Go to Policies:**
   - Top tabs → Click **"Policies"**

2. **Create New Policy:**
   - Click **"New Policy"** button
   - Configure:
     ```
     Name: Allow authenticated downloads
     Policy for: SELECT
     Target roles: All
     
     USING expression:
     auth.role() = 'authenticated' AND bucket_id = 'ulnec-downloads'
     ```

3. **Click "Review"** then **"Save policy"**

**Option B: Temporary Public URL (For Quick Testing)**

1. **Click on the file** in bucket
2. **Click "Get URL"** button
3. **Set expiration:** 24 hours
4. **Copy URL** and save it

⚠️ **Note:** Option B is only for testing. Use Option A for production.

✅ **Done:** Permissions set

---

## Step 2.5: Test Download (2 minutes)

1. **Go back to WordPress:**
   - Your WordPress admin panel

2. **Test download link:**
   - If you have a test account with active license:
     - Login as that user
     - Go to: `/billing/` page
     - Try clicking download button
   
   - OR check in code:
     - The download URL should be accessible

3. **Verify:**
   - File downloads successfully
   - Correct file size (should match original)
   - File is not corrupted

✅ **Done:** .msi upload complete!

---

# PART 3: TEST EMAIL SYSTEM (20-30 minutes)

## Step 3.1: Prepare Test Environment (5 minutes)

1. **Create Test User (if needed):**
   - WordPress Admin → Users → Add New
   - Username: `testuser`
   - Email: your-testing-email@gmail.com
   - Password: [create password]
   - Role: Subscriber
   - Click "Add New User"

2. **Setup Test Data:**
   - Note down test user credentials
   - Open an incognito/private browser window
   - Ready to test!

✅ **Done:** Test environment ready

---

## Step 3.2: Test Bug Report Email (8 minutes)

1. **Login as test user:**
   - Go to: https://jdsancontrols.com/wp-login.php
   - Login with test user credentials

2. **Go to Bug Report page:**
   - URL: https://jdsancontrols.com/bug-report/
   - (If page doesn't exist, you'll need to create it first - see note below)

3. **Fill out bug report form:**
   ```
   Title: Test Bug Report - Email System
   Description: This is a test to verify email confirmation works.
   Severity: Low
   Steps to Reproduce: N/A - Testing only
   AutoCAD Version: 2024
   Plugin Version: 1.0 Beta
   ```

4. **Submit form**

5. **Check for success message:**
   - Should see green confirmation
   - Bug ID displayed (e.g., BUG-2026-123)

6. **Check your email inbox:**
   - Wait 30-60 seconds
   - Check inbox for test email
   - Check spam folder if not in inbox

7. **Verify email content:**
   - ✅ From: UL/NEC Support
   - ✅ Subject: Bug Report Received - #[ID]
   - ✅ Contains bug details
   - ✅ Has nice HTML formatting
   - ✅ Includes tracking link

8. **Check WordPress admin:**
   - WP Admin → UL-NEC → Bugs & Features
   - Should see your bug report listed

✅ **Test 1 Passed:** Bug report email working!

---

## Step 3.3: Test Feature Request Email (8 minutes)

1. **Still logged in as test user**

2. **Go to Feature Request page:**
   - URL: https://jdsancontrols.com/feature-request/

3. **Fill out feature request form:**
   ```
   Title: Test Feature Request - Email System
   Description: This is a test to verify email confirmation works.
   Category: User Interface
   Priority: Medium
   Use Case: Testing email functionality
   ```

4. **Submit form**

5. **Check for success message:**
   - Should see green confirmation
   - Feature ID displayed (e.g., FEAT-2026-456)

6. **Check your email inbox:**
   - Wait 30-60 seconds
   - Look for new email

7. **Verify email content:**
   - ✅ From: UL/NEC Support
   - ✅ Subject: Feature Request Received - #[ID]
   - ✅ Contains feature details
   - ✅ Has nice HTML formatting
   - ✅ Includes tracking link

8. **Check WordPress admin:**
   - WP Admin → UL-NEC → Bugs & Features
   - Should see your feature request listed

✅ **Test 2 Passed:** Feature request email working!

---

## Step 3.4: Test License Delivery Email (Simulated) (5 minutes)

**Note:** Full testing requires payment processing, which comes later.

**For now, we'll verify the code is ready:**

1. **Check email handler exists:**
   - Go to: `ul-nec-compliance/includes/class-ulnec-emails.php`
   - Verify `send_license_delivery_email()` function exists

2. **Check payment integration:**
   - Go to: `ul-nec-compliance/includes/class-ulnec-payment.php`
   - Verify `send_license_email()` calls the email handler

3. **Manual test (optional):**
   - Use WordPress admin to manually create a test license
   - Check if email is triggered

✅ **Test 3 Ready:** License email code in place!

---

## Step 3.5: Verify Email Logs (5 minutes)

1. **Check WP Mail SMTP logs:**
   - WP Admin → WP Mail SMTP → Email Log
   - Should see 2 emails sent:
     - Bug report confirmation
     - Feature request confirmation

2. **Check Email Service Dashboard:**
   - **Gmail:** Check sent folder (youraddress@gmail.com)
   - **Mailgun:** Activity → Logs
   - **Brevo:** Statistics → Email Activity
   - **SMTP.com:** Dashboard → Reports
   - **cPanel:** May not have logs (check inbox directly)
   - Should see 2-3 emails:
     - Test email from Step 1.10
     - Bug report email
     - Feature request email

3. **Verify delivery status:**
   - All should show "Delivered" or "Sent"
   - None should show "Bounced" or "Dropped"

✅ **All Tests Passed:** Email system fully functional!

---

# 📊 POST-SETUP STATUS

## What's Complete Now:

- ✅ SMTP configured with SendGrid
- ✅ Email handler class integrated
- ✅ Bug report emails working
- ✅ Feature request emails working
- ✅ License delivery emails coded (ready for payment testing)
- ✅ .msi file uploaded to Supabase
- ✅ Download system configured
- ✅ File permissions set

## Plugin Completion Status:

**Before:** 95% Complete  
**After:** 98% Complete! 🎉

## Remaining Tasks:

1. ⏳ Create WordPress pages with shortcodes (30 minutes)
2. ⏳ Payment gateway testing (2-3 hours)
3. ⏳ End-to-end user journey testing (4-5 hours)
4. ⏳ Convert landing page HTML (2-3 hours)

**Time to Beta Launch:** 8-12 hours!

---

# 🚨 TROUBLESHOOTING

## Email Issues:

### "Test email not received"
**Solutions:**
1. Check spam folder first (most common issue)
2. Verify credentials copied correctly (no extra spaces)
3. Try different "Send To" email address
4. Check service-specific logs:
   - **Gmail:** Check Sent folder
   - **Mailgun:** Activity Feed
   - **Brevo:** Statistics
   - **SMTP.com:** Reports
   - **cPanel:** Check inbox directly
5. Verify WP Mail SMTP Email Log: WP Admin → WP Mail SMTP → Email Log

### "Authentication failed" or "Connection error"
**Solutions:**

**For Gmail:**
- Verify 2FA is enabled on Google account
- Regenerate app password (may expire)
- Try using "Other SMTP" instead of "Gmail" mailer
- Check firewall allows smtp.gmail.com port 587

**For Mailgun:**
- Verify API key has full permissions
- Check domain is correct (sandbox domain for testing)
- Add recipient to Authorized Recipients (sandbox mode)

**For Brevo:**
- Verify SMTP key (not API key)
- Check account verified
- Verify smtp-relay.brevo.com accessible

**For SMTP.com:**
- Check credentials from dashboard
- Verify account active (trial not expired)

**For cPanel:**
- Verify mail server address (usually mail.domain.com)
- Check email account exists and password correct
- Try port 465 (SSL) if 587 fails

### "500 Error when sending email"
**Solutions:**
1. Increase PHP memory: Add to wp-config.php:
   ```php
   define('WP_MEMORY_LIMIT', '256M');
   ```
2. Check error log: WP Admin → Tools → Site Health
3. Verify credentials have correct permissions
4. Check WP Mail SMTP debug log for details

### "Emails go to spam"
**Solutions:**
1. Use "noreply@yourdomain.com" sender address (not Gmail address)
2. Avoid spam trigger words in subject/body
3. For production (advanced):
   - Add SPF record to DNS
   - Verify domain with email service
   - Set up DKIM authentication
4. **Quick fix for beta:** Gmail has best deliverability

### "Can't install WP Mail SMTP plugin"
**Solutions:**
1. Check WordPress version (need 5.2+)
2. Try manual install:
   - Download from WordPress.org
   - Upload via Plugins → Add New → Upload
3. Check file permissions on wp-content/plugins/
4. Temporarily disable other plugins (conflict?)

## Upload Issues:

### "Upload fails"
**Solutions:**
1. Check file size (must be < 100 MB)
2. Check internet connection stability
3. Try different browser
4. Clear browser cache
5. Try upload from different network

### "Can't see uploaded file"
**Solutions:**
1. Refresh bucket view
2. Check correct bucket selected
3. Verify you're in correct Supabase project
4. Check for upload errors in browser console

### "Download gives 403 error"
**Solutions:**
1. Verify RLS policy created correctly
2. Check user is authenticated
3. Verify bucket name in code matches actual bucket
4. Check file path in download code

## WordPress Page Issues:

### "Bug report page doesn't exist"
**Quick Fix:**
1. WP Admin → Pages → Add New
2. Title: Bug Report
3. Content: Add shortcode: `[ulnec_bug_report]`
4. Permalink: bug-report
5. Publish

### "Form submission fails"
**Solutions:**
1. Check Supabase connection
2. Verify user logged in
3. Check browser console for errors
4. Review WordPress error log

---

# ✅ VERIFICATION CHECKLIST

After completing this guide, verify:

- [ ] Email service chosen and credentials obtained
- [ ] WP Mail SMTP plugin installed and configured
- [ ] Test email sent and received (check spam folder too)
- [ ] .msi file uploaded to ulnec-downloads bucket
- [ ] File renamed to: UL-NEC-Compliance-Plugin-Latest.msi
- [ ] File permissions/RLS policy set
- [ ] Bug report email received and looks good
- [ ] Feature request email received and looks good
- [ ] Both emails in WP Mail SMTP log
- [ ] Both emails in email service dashboard (if available)
- [ ] Bug report appears in WP Admin
- [ ] Feature request appears in WP Admin

**All checked?** 🎉 **You're 98% ready for beta launch!**

---

# 🎯 NEXT STEPS

**After completing this guide:**

1. **Create Missing WordPress Pages** (30 minutes)
   - Create pages with shortcodes
   - See: CREATE_PAGES_GUIDE.md

2. **Payment Testing** (2-3 hours)
   - Configure PayPal sandbox
   - Test payment flow
   - Verify license auto-generation
   - Test license delivery email

3. **End-to-End Testing** (4-5 hours)
   - Full user journey from registration to download
   - Test on different browsers
   - Mobile testing
   - Bug fixes

4. **Landing Page** (2-3 hours)
   - Convert HTML to WordPress
   - Add dynamic pricing
   - Connect to payment system

**Timeline:** 2-3 days to beta launch! 🚀

---

# 📞 SUPPORT

**Issues? Questions?**

1. **Check error logs:**
   - WordPress: WP Admin → Tools → Site Health
   - WP Mail SMTP: Email Log
   - Email Service: Dashboard activity/logs
   - Supabase: Logs section

2. **Documentation:**
   - WP Mail SMTP: https://wpmailsmtp.com/docs/
   - Gmail: https://support.google.com/mail/answer/7126229
   - Mailgun: https://documentation.mailgun.com/
   - Brevo: https://help.brevo.com/
   - Supabase: https://supabase.com/docs

3. **Quick Comparison:**
   - See: EMAIL_SERVICE_COMPARISON.md for choosing best service

3. **Contact:**
   - support@jdsancontrols.com

---

**Setup Guide v1.0 - February 21, 2026**  
**UL/NEC Compliance Plugin v1.3.0**

*All code is ready. You just need to configure external services!* ✨
