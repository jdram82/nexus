# ⚡ QUICK ACTION CHECKLIST

**Print this or keep it open while working!**  
**Total Time: 1-1.5 hours**

---

## ☑️ PART 1: SMTP (20-30 min)

### SendGrid Setup
- [ ] Go to https://signup.sendgrid.com/
- [ ] Create account & verify email
- [ ] Dashboard → Settings → API Keys
- [ ] Create API Key: "WordPress UL-NEC"
- [ ] Permission: Full Access
- [ ] **COPY & SAVE API KEY**

### WordPress Plugin
- [ ] Login to WordPress admin
- [ ] Plugins → Add New
- [ ] Install "WP Mail SMTP by WPForms"
- [ ] Activate plugin

### Configure WP Mail SMTP
- [ ] WP Mail SMTP → Settings
- [ ] From Email: `noreply@jdsancontrols.com`
- [ ] Force From Email: ✅
- [ ] From Name: `UL/NEC Support`
- [ ] Force From Name: ✅
- [ ] Mailer: Select SendGrid
- [ ] API Key: [Paste your key]
- [ ] Return Path: ✅
- [ ] Save Settings

### Test
- [ ] WP Mail SMTP → Email Test
- [ ] Send to: your-email@gmail.com
- [ ] Check inbox (& spam)
- [ ] ✅ Email received!

---

## ☑️ PART 2: UPLOAD (10-15 min)

### Supabase Access
- [ ] Go to https://supabase.com/dashboard
- [ ] Login & select UL-NEC project
- [ ] Sidebar → Storage

### Bucket Setup
- [ ] Check for `ulnec-downloads` bucket
- [ ] If missing: New Bucket → Name: `ulnec-downloads` → Public: NO
- [ ] Click on bucket

### Upload File
- [ ] Click "Upload file"
- [ ] Select your .msi file
- [ ] **Rename to: `UL-NEC-Compliance-Plugin-Latest.msi`**
- [ ] Upload
- [ ] Verify file appears

### Permissions
- [ ] Policies tab → New Policy
- [ ] Name: Allow authenticated downloads
- [ ] Operation: SELECT
- [ ] USING: `auth.role() = 'authenticated' AND bucket_id = 'ulnec-downloads'`
- [ ] Save Policy

---

## ☑️ PART 3: TEST EMAILS (20-30 min)

### Setup Test User
- [ ] WP Admin → Users → Add New
- [ ] Username: testuser
- [ ] Email: your-test-email@gmail.com
- [ ] Role: Subscriber
- [ ] Create

### Test Bug Report Email
- [ ] Open incognito browser
- [ ] Login as testuser
- [ ] Go to: /bug-report/
- [ ] Fill form with test data
- [ ] Submit
- [ ] Check email inbox
- [ ] ✅ Email received with Bug ID!
- [ ] Check WP Admin → UL-NEC → Bugs

### Test Feature Request Email
- [ ] Still logged in as testuser
- [ ] Go to: /feature-request/
- [ ] Fill form with test data
- [ ] Submit
- [ ] Check email inbox
- [ ] ✅ Email received with Feature ID!
- [ ] Check WP Admin → UL-NEC → Features

### Verify Logs
- [ ] WP Mail SMTP → Email Log (2 emails)
- [ ] SendGrid → Activity Feed (3 emails total)
- [ ] All show "Delivered" status

---

## 🎯 QUICK LINKS

**Services:**
- SendGrid: https://app.sendgrid.com/
- Supabase: https://supabase.com/dashboard
- WordPress: https://jdsancontrols.com/wp-admin/

**Credentials:** (keep safe!)
```
SendGrid API Key: SG._____________
Supabase Project URL: https://_______.supabase.co
WordPress Admin: [your credentials]
```

---

## ✅ COMPLETION STATUS

Track your progress:

- [ ] SendGrid account created
- [ ] API key created & saved
- [ ] WP Mail SMTP installed
- [ ] SMTP configured
- [ ] Test email sent ✓
- [ ] Supabase storage accessed
- [ ] .msi file uploaded
- [ ] File renamed correctly
- [ ] Permissions set
- [ ] Bug report email tested ✓
- [ ] Feature email tested ✓
- [ ] All logs verified ✓

**All checked?** 🚀 **You're 98% done!**

---

## 🚨 QUICK TROUBLESHOOTING

**Email not received?**
→ Check spam, verify API key, check SendGrid Activity

**Upload fails?**
→ Check file size < 100MB, try different browser

**Form submission fails?**
→ Check if logged in, verify pages exist

**Still stuck?**
→ See COMPLETE_SETUP_GUIDE.md for detailed troubleshooting

---

## 📊 WHAT'S LEFT

After this checklist:
- Payment testing (2-3 hours)
- End-to-end testing (4-5 hours)
- Landing page (2-3 hours)

**Beta launch:** 2-3 days away! 🎉

---

*Quick Checklist v1.0 - Feb 21, 2026*
