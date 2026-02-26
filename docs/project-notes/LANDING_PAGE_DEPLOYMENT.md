# 🚀 UL/NEC Landing Page Deployment Guide

**File Updated**: `page-ulnec-landing.php`  
**Latest Commit**: `392f995`  
**Date**: February 24, 2026  
**Status**: ✅ Pushed to GitHub

---

## 📊 What Changed

### Major Updates:
- ✅ Changed from annual ($149/$224/year) to monthly SaaS pricing ($37.50-$75/month)
- ✅ Added 3 pricing tiers: Professional, Team, Enterprise
- ✅ Updated hero tagline: "Save 15-20 Hours Per Panel"
- ✅ Added beta pricing highlight with 50% lifetime discount
- ✅ Updated feature metrics (1,200+ rules, 10,000+ components)
- ✅ Complete FAQ rewrite for new pricing model
- ✅ Added money-back guarantee section
- ✅ Updated urgency countdown to April 30, 2026 deadline

**Statistics**: 269 lines changed (167 additions, 102 deletions)

---

## 🌐 Deployment Steps

### Step 1: Upload Template File

**Via FTP (FileZilla, WinSCP, etc.):**
```
1. Connect to jdsancontrols.com FTP
2. Navigate to: /public_html/wp-content/themes/[your-theme-name]/
3. Upload: page-ulnec-landing.php
4. Overwrite if file exists
```

**Via cPanel File Manager:**
```
1. Login to Namecheap cPanel
2. File Manager → public_html/wp-content/themes/[your-theme-name]/
3. Click "Upload"
4. Select: page-ulnec-landing.php
5. Click "Upload" button
```

**Via WordPress Theme Editor (Quick but not recommended):**
```
1. WordPress Admin → Appearance → Theme File Editor
2. Warning screen → "I understand"
3. Right panel → Theme Files
4. Add New File → page-ulnec-landing.php
5. Copy-paste content
6. Save
```

---

### Step 2: Create WordPress Page

**In WordPress Admin** (`jdsancontrols.com/wp-admin`):

1. **Click**: Pages → Add New

2. **Page Settings**:
   - **Title**: `UL/NEC Compliance Checker`
   - **Content**: Leave blank (template handles everything)
   - **Permalink**: `/ulnec-compliance-checker/` (click Edit next to title to customize)

3. **Select Template** (right sidebar):
   - Look for "Page Attributes" box
   - **Template dropdown**: Select "UL/NEC Landing Page"
   
4. **Publish**:
   - Click blue "Publish" button
   - Confirm by clicking "Publish" again

5. **View Page**:
   - Click "View Page" button
   - Or visit: `https://jdsancontrols.com/ulnec-compliance-checker/`

---

### Step 3: Set as Homepage (Optional)

If you want this as your main landing page:

```
WordPress Admin → Settings → Reading
→ "A static page" radio button
→ Homepage: Select "UL/NEC Compliance Checker"
→ Save Changes
```

**Homepage URL**: `https://jdsancontrols.com/`

---

## ✅ Verification Checklist

After deployment, check:

- [ ] **Hero section** displays new tagline "Save 15-20 Hours Per Panel"
- [ ] **Pricing highlight** shows "Lock in $75/mo Forever"
- [ ] **3 pricing cards** show: Professional ($37.50-$75), Team ($200-$280), Enterprise (Custom)
- [ ] **Trust badges** show "1,200+ compliance rules" and "10,000+ component database"
- [ ] **Counter section** shows beta deadline "April 30, 2026"
- [ ] **FAQ section** has 10 questions about new pricing
- [ ] **Money-back guarantee** section appears below pricing cards
- [ ] **Mobile responsive** - test on phone
- [ ] **All buttons work** - test CTA buttons
- [ ] **No JavaScript errors** - open browser console (F12)

---

## 🔗 Important Links

**Landing Page URLs**:
- Main: `https://jdsancontrols.com/ulnec-compliance-checker/`
- Alt: `https://jdsancontrols.com/` (if set as homepage)

**Admin URLs**:
- WordPress: `https://jdsancontrols.com/wp-admin/`
- Pages: `https://jdsancontrols.com/wp-admin/edit.php?post_type=page`
- Theme Editor: `https://jdsancontrols.com/wp-admin/theme-editor.php`

**GitHub**:
- Repository: `https://github.com/jdram82/nexus`
- File: `https://github.com/jdram82/nexus/blob/main/page-ulnec-landing.php`
- Commit: `https://github.com/jdram82/nexus/commit/392f995`

---

## 🎨 Customization

If you need to make changes after reviewing:

**1. Update locally**:
```bash
cd /workspaces/codespaces-blank/nexus-theme
# Edit page-ulnec-landing.php
git add page-ulnec-landing.php
git commit -m "Update landing page: [your changes]"
git push origin main
```

**2. Re-upload to WordPress** (repeat Step 1 above)

**3. Hard refresh page** (Ctrl+F5 or Cmd+Shift+R) to see changes

---

## 🐛 Troubleshooting

### "Template not showing in dropdown"
**Solution**:
1. Re-upload file
2. Check file is in theme root directory (not in a subfolder)
3. Verify first 5 lines contain:
```php
<?php
/**
 * Template Name: UL/NEC Landing Page
```

### "Page looks broken or unstyled"
**Solution**:
1. Check browser console (F12) for JavaScript errors
2. Hard refresh: Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)
3. Clear WordPress cache (if using cache plugin)
4. Verify entire file uploaded (not truncated)

### "404 Not Found error"
**Solution**:
1. WordPress Admin → Settings → Permalinks
2. Click "Save Changes" (flushes rewrite rules)
3. Try visiting page again

### "Changes not showing"
**Solution**:
1. Hard refresh: Ctrl+F5
2. Clear browser cache
3. Check correct file uploaded (verify line count: 951 lines)
4. Open Incognito/Private window to test

---

## 📱 Testing Checklist

### Desktop Testing:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

### Mobile Testing:
- [ ] iPhone Safari
- [ ] Android Chrome
- [ ] Tablet (iPad/Android)

### Functionality Testing:
- [ ] Scroll smooth (no lag)
- [ ] Buttons clickable
- [ ] FAQ accordion works
- [ ] Beta countdown displays correctly
- [ ] Forms work (if any)
- [ ] All links work

---

## 🎯 Next Steps After Deployment

Once landing page is live and reviewed:

### 1. **Upload .msi File** (15 minutes)
- Guide: [MSI_UPLOAD_GUIDE.md](MSI_UPLOAD_GUIDE.md)
- Supabase bucket: `ulnec-downloads`
- File name: `UL-NEC-Compliance-Plugin-Latest.msi`

### 2. **Create WordPress Pages** (30 minutes)
- Bug Report: `/bug-report/`
- Feature Request: `/feature-request/`
- Founders Progress: `/founders-progress/`
- Account Settings: `/account-settings/`
- Billing: `/billing/`

### 3. **Test Email System** (15 minutes)
- Send test bug report
- Verify email received
- Check email formatting

### 4. **Update Navigation Menu**
- Add landing page to main menu
- Add "Start Trial" button to header

### 5. **Setup Analytics** (optional)
- Add Google Analytics tracking code
- Setup conversion tracking for pricing clicks
- Track "Start Free Trial" button clicks

---

## 📞 Support

**Questions?**
- Email: support@jdsancontrols.com
- GitHub Issues: https://github.com/jdram82/nexus/issues

**Documentation**:
- Email Setup: [COMPLETE_SETUP_GUIDE.md](ul-nec-compliance/COMPLETE_SETUP_GUIDE.md)
- MSI Upload: [MSI_UPLOAD_GUIDE.md](ul-nec-compliance/MSI_UPLOAD_GUIDE.md)
- Create Pages: [CREATE_PAGES_GUIDE.md](ul-nec-compliance/CREATE_PAGES_GUIDE.md)

---

**Status**: Ready to deploy 🚀  
**Last Updated**: February 24, 2026  
**Version**: 1.0
