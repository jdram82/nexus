# UL-NEC Compliance Plugin v1.2.0 - Complete Claude_Beta Launch Integration

## 🎉 What's New in v1.2.0

### New Premium Shortcodes (Claude_Beta Launch Design)

#### 1. **Bug Report** - `[ulnec_bug_report]`
✅ **Already Working!**
- Premium gradient UI
- Comprehensive bug reporting fields
- Severity selector with emoji icons
- CAD version & Windows version dropdowns
- Success screen with Bug ID
- Saves to Supabase `ulnec_bugs` table

#### 2. **Feature Request** - `[ulnec_feature_request]` 
✨ **NEW**
- Premium form matching bug report style
- Category selector (UI/UX, Compliance, Export, Integration, Performance, Other)
- Detailed fields for use case and importance
- Priority levels
- Saves to Supabase `ulnec_features` table

#### 3. **Founders Progress** - `[ulnec_founders_progress]`
🏆 **NEW**
- Visual progress tracker (X / 3 submissions)
- Progress bar showing completion percentage
- Breakdown: Bug reports vs Feature requests
- Founders Tier benefits callout
- Direct links to submit bugs/features

#### 4. **Account Settings** - `[ulnec_account_settings]`
⚙️ **NEW**
- Edit profile information (name, company)
- View account type and member since date
- Updates Supabase user records
- Success/error messaging

#### 5. **Billing & Subscriptions** - `[ulnec_billing]`
💳 **NEW**
- View all active licenses
- License key display
- Activation and expiration dates
- Transaction history table
- Payment status indicators

---

## 📦 Installation Instructions

### 1. Upload Plugin
```
WordPress Admin → Plugins → Deactivate old version → Delete
→ Upload ul-nec-compliance.zip → Activate
```

### 2. Create WordPress Pages

Create these pages in WordPress with the corresponding shortcodes:

| Page Name | Slug | Shortcode |
|-----------|------|-----------|
| Bug Report | `/bug-report` | `[ulnec_bug_report]` |
| Feature Request | `/feature-request` | `[ulnec_feature_request]` |
| Founders Progress | `/founders-progress` | `[ulnec_founders_progress]` |
| Account Settings | `/account-settings` | `[ulnec_account_settings]` |
| Billing | `/billing` | `[ulnec_billing]` |

### 3. Setup Admin User (durgaram@jdsancontrols.com)

**⚠️ IMPORTANT: Run this ONE TIME only**

1. Navigate to: `https://yoursite.com/wp-content/plugins/ul-nec-compliance/setup-admin.php`
2. The script will:
   - Create or update WordPress user
   - Set as Administrator role
   - Create/update Supabase user with `role: admin`
3. **DELETE `setup-admin.php` after running** for security

**Alternative Manual Method:**
```sql
-- In Supabase SQL Editor
UPDATE ulnec_users 
SET role = 'admin' 
WHERE email = 'durgaram@jdsancontrols.com';
```

---

## 🎨 Design Features

### Common UI Elements (All Pages)
- **Founders Tier Banner**: Yellow gradient callout at top
- **Premium Form Container**: White card with shadow on light background
- **Gradient Buttons**: Purple/violet gradient with hover effects
- **Success Screens**: Green gradient with animated checkmark
- **Error Messages**: Red background with border
- **Mobile Responsive**: Clean stacking on small screens

### Color Palette
- Primary: `#667eea` (purple/blue)
- Secondary: `#764ba2` (violet)
- Success: `#10b981` (green)
- Warning: `#fbbf24` (yellow/gold)
- Error: `#ef4444` (red)
- Background: `#f9fafb` (light gray)
- Text: `#1a1f3a` (dark blue)

---

## 🔧 Technical Details

### Database Schema Used

**ulnec_bugs**
- `user_id`, `title`, `description`
- `steps_to_reproduce`, `expected_behavior`, `actual_behavior`
- `severity` (critical, high, medium, low)
- `autocad_version`, `plugin_version`, `os_version`
- `status` (open, in_progress, resolved, closed)

**ulnec_features**
- `user_id`, `title`, `description`
- `category` (ui, compliance, export, integration, performance, other)
- `status` (submitted, under_review, planned, in_progress, completed, rejected)
- `vote_count` (integer)

**ulnec_users**
- `email`, `name`, `company`, `role`
- `wordpress_user_id`
- `email_verified`, `created_at`

**ulnec_licenses**
- `user_id`, `license_key`, `tier`, `status`
- `activated_at`, `expires_at`

**ulnec_transactions**
- `user_id`, `amount`, `tier`, `status`
- `payment_method`, `created_at`

---

## 🚀 Usage Examples

### Bug Report Page
```
Users submit bugs with:
- Steps to reproduce
- Expected vs actual behavior
- CAD version, Windows version
- Severity level (visual selector)

Success message shows:
"Bug Report ID: BUG-2026-355"
```

### Feature Request Page
```
Users submit features with:
- Category selection (visual icons)
- Detailed description
- Use case explanation
- Priority level

Success message shows:
"Request ID: FEAT-2026-123"
```

### Founders Progress Page
```
Shows:
- 2 / 3 submissions (progress bar)
- 1 Bug Report, 1 Feature Request
- Founders Tier benefits list
- Quick links to submit more
```

### Account Settings
```
Editable fields:
- Full Name
- Company

Read-only:
- Email
- Account Type (user/admin)
- Member Since date
```

### Billing Page
```
Shows:
- Active licenses with keys
- License status badges
- Transaction history table
- Payment amounts and dates
```

---

## 🎯 Next Steps

1. **Upload v1.2.0** to WordPress
2. **Run setup-admin.php** (then delete it)
3. **Create the 5 new pages** with shortcodes
4. **Test each page** while logged in
5. **Test bug/feature submission** → Check Supabase
6. **Verify Founders Progress** counts correctly
7. **Update navigation menu** to link to new pages

---

## 📝 Admin Access

After running `setup-admin.php`:

**WordPress Admin**: `durgaram@jdsancontrols.com`
- Role: Administrator
- Can access all WordPress admin features
- Can manage plugins, users, pages

**Supabase Admin**: `durgaram@jdsancontrols.com`
- Role: admin
- Can view/edit all user data
- Can manage bugs, features, licenses

**Admin Dashboard**: `/wp-admin/admin.php?page=ulnec-dashboard`
- View all users
- View bugs & features
- View downloads
- View analytics

---

## ✅ Testing Checklist

- [ ] Upload and activate v1.2.0
- [ ] Run setup-admin.php
- [ ] Delete setup-admin.php
- [ ] Create Bug Report page
- [ ] Create Feature Request page
- [ ] Create Founders Progress page
- [ ] Create Account Settings page
- [ ] Create Billing page
- [ ] Test bug submission → Check Supabase
- [ ] Test feature submission → Check Supabase
- [ ] Verify Founders Progress counts
- [ ] Update profile in Account Settings
- [ ] View licenses and transactions in Billing
- [ ] Confirm admin can see all data

---

## 🐛 Troubleshooting

**Issue**: Pages show "coming soon"
- **Fix**: Make sure you uploaded v1.2.0 (not 1.1.0 or earlier)

**Issue**: Forms don't submit
- **Fix**: Check Supabase RLS policies allow INSERT/UPDATE
- **Fix**: Check WordPress debug.log for errors

**Issue**: Founders Progress shows 0/3
- **Fix**: Submit a bug or feature while logged in
- **Fix**: Verify Supabase user_id matches between tables

**Issue**: Setup-admin.php shows errors
- **Fix**: Make sure you're logged in as WordPress admin first
- **Fix**: Check Supabase connection settings

**Issue**: Styling looks broken
- **Fix**: All CSS is inline in the shortcodes
- **Fix**: Clear browser cache
- **Fix**: Check theme doesn't override .ulnec- classes

---

## 📞 Support

For issues or questions:
- Email: support@jdsancontrols.com
- Check WordPress debug.log: `/wp-content/debug.log`
- Check Supabase logs in dashboard

---

**Version**: 1.2.0  
**Release Date**: January 18, 2026  
**Compatibility**: WordPress 5.8+, PHP 7.4+
