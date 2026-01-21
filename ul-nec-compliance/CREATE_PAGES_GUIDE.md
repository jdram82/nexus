# 📄 Create WordPress Pages - Quick Guide

## Option 1: Automatic (FASTEST - 5 minutes) ⚡

### Using SQL Import:

1. **Download** `create-wordpress-pages.sql` from the plugin folder
2. **Login to phpMyAdmin** or database manager
3. **Select your WordPress database**
4. **Click "Import" tab**
5. **Choose file** → select `create-wordpress-pages.sql`
6. **Click "Go"**
7. **Done!** All 5 pages created instantly

**Verify:**
- Go to WordPress Admin → Pages
- You should see 5 new pages published

---

## Option 2: Manual Creation (10 minutes per page)

### Step-by-Step for Each Page:

**1. Bug Report Page**
```
WordPress Admin → Pages → Add New

Title: Bug Report
Content: [ulnec_bug_report]
URL Slug: bug-report
Status: Publish
```

**2. Feature Request Page**
```
WordPress Admin → Pages → Add New

Title: Feature Request
Content: [ulnec_feature_request]
URL Slug: feature-request
Status: Publish
```

**3. Founders Progress Page**
```
WordPress Admin → Pages → Add New

Title: Founders Progress
Content: [ulnec_founders_progress]
URL Slug: founders-progress
Status: Publish
```

**4. Account Settings Page**
```
WordPress Admin → Pages → Add New

Title: Account Settings
Content: [ulnec_account_settings]
URL Slug: account-settings
Status: Publish
```

**5. Billing Page**
```
WordPress Admin → Pages → Add New

Title: Billing
Content: [ulnec_billing]
URL Slug: billing
Status: Publish
```

---

## Option 3: Using WP-CLI (For Advanced Users)

If you have SSH access:

```bash
# Navigate to WordPress directory
cd /wptbox

# Create all pages at once
wp post create --post_type=page --post_title='Bug Report' --post_content='[ulnec_bug_report]' --post_status=publish --post_name=bug-report

wp post create --post_type=page --post_title='Feature Request' --post_content='[ulnec_feature_request]' --post_status=publish --post_name=feature-request

wp post create --post_type=page --post_title='Founders Progress' --post_content='[ulnec_founders_progress]' --post_status=publish --post_name=founders-progress

wp post create --post_type=page --post_title='Account Settings' --post_content='[ulnec_account_settings]' --post_status=publish --post_name=account-settings

wp post create --post_type=page --post_title='Billing' --post_content='[ulnec_billing]' --post_status=publish --post_name=billing
```

---

## ✅ Verification Checklist

After creating pages, test each one:

- [ ] Visit https://jdsancontrols.com/bug-report/
  - Should show bug report form with dark background
  
- [ ] Visit https://jdsancontrols.com/feature-request/
  - Should show feature request form
  
- [ ] Visit https://jdsancontrols.com/founders-progress/
  - Should show progress tracking (if logged in)
  
- [ ] Visit https://jdsancontrols.com/account-settings/
  - Should show profile settings (if logged in)
  
- [ ] Visit https://jdsancontrols.com/billing/
  - Should show licenses and billing (if logged in)

---

## 🎨 Optional: Add to Menu

**Create User Dashboard Menu:**

1. Go to **Appearance → Menus**
2. Create new menu: "User Dashboard"
3. Add these pages:
   - Bug Report
   - Feature Request
   - Founders Progress
   - Account Settings
   - Billing
4. Assign to location (if your theme supports it)

---

## 🔐 Optional: Restrict Access

If you want pages to be login-only:

**Install Plugin:** [Content Control](https://wordpress.org/plugins/content-control/)

**Or add to each page's template:**
```php
<?php
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}
?>
```

---

## 📝 Page URLs Reference

After creation, your pages will be available at:

| Page | URL |
|------|-----|
| Bug Report | `/bug-report/` |
| Feature Request | `/feature-request/` |
| Founders Progress | `/founders-progress/` |
| Account Settings | `/account-settings/` |
| Billing | `/billing/` |

---

## 🚨 Troubleshooting

**404 Error?**
- Go to **Settings → Permalinks**
- Click "Save Changes" (flush permalinks)

**Shortcode Not Working?**
- Verify plugin is activated
- Check for typos in shortcode name
- Ensure square brackets are used `[ ]`

**Page Shows Code?**
- Using Classic Editor? Switch to Shortcode block
- Using Gutenberg? Add Shortcode block, not Code block

---

**Total Time:** 5 minutes (SQL) or 30 minutes (Manual)
**Difficulty:** Easy
**Status:** Ready to implement
