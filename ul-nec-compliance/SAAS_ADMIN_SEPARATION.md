# SAAS ADMIN SEPARATION - SECURITY UPGRADE

**Date:** January 18, 2026  
**Version:** 1.3.0  
**Purpose:** Separate WordPress admin from SaaS product admin roles

---

## 🔐 SECURITY IMPROVEMENT

Previously, managing UL-NEC SaaS products required WordPress administrator access - a **security risk**. Now these roles are completely separate:

### Before (v1.2.0):
- ❌ SaaS admin = WordPress admin (full site control)
- ❌ Anyone managing products could modify themes, plugins, settings
- ❌ Single point of failure

### After (v1.3.0):
- ✅ SaaS admin = Supabase `is_admin` flag (product management only)
- ✅ WordPress admin = Site management (themes, plugins, settings)
- ✅ **Principle of least privilege**

---

## 📋 WHAT CHANGED

### 1. **Database Schema**
Added `is_admin` column to `ulnec_users` table:
```sql
ALTER TABLE ulnec_users ADD COLUMN is_admin BOOLEAN DEFAULT false;
```

### 2. **Plugin Core** (`ul-nec-compliance.php`)
Added `is_saas_admin()` method:
- Checks Supabase `is_admin` flag
- Independent of WordPress capabilities
- Returns true/false based on database

### 3. **Admin Class** (`class-ulnec-admin.php`)
- Changed capability from `manage_options` → `read`
- Added `is_saas_admin()` check in menu registration
- Added `check_access()` call in all admin page methods
- Blocks access with 403 error if unauthorized

### 4. **Setup Script** (`setup-admin.php`)
- Creates user with **subscriber** role (NOT administrator)
- Sets `is_admin = true` in Supabase
- No WordPress admin access granted

---

## 🚀 DEPLOYMENT STEPS

### Step 1: Run SQL Migration
In **Supabase SQL Editor**, run:
```sql
-- File: database-migrations/add-is-admin-column.sql
ALTER TABLE ulnec_users 
ADD COLUMN IF NOT EXISTS is_admin BOOLEAN DEFAULT false;

CREATE INDEX IF NOT EXISTS idx_ulnec_users_is_admin ON ulnec_users(is_admin);

UPDATE ulnec_users 
SET is_admin = true 
WHERE email = 'durgaram@jdsancontrols.com';
```

### Step 2: Upload Updated Plugin
Upload v1.3.0 files:
- `ul-nec-compliance.php`
- `includes/class-ulnec-admin.php`
- `setup-admin.php`

### Step 3: Run Setup Script (if needed)
If creating new SaaS admin or updating existing:
```
https://jdsancontrols.com/wp-content/plugins/ul-nec-compliance/setup-admin.php
```

**Result:** User gets SaaS admin access WITHOUT WordPress admin

### Step 4: Delete Setup Script
```bash
rm /wp-content/plugins/ul-nec-compliance/setup-admin.php
```

---

## 👥 ROLE COMPARISON

| Feature | WordPress Admin | SaaS Admin |
|---------|----------------|------------|
| **Manage WordPress themes** | ✅ Yes | ❌ No |
| **Install plugins** | ✅ Yes | ❌ No |
| **Edit WordPress settings** | ✅ Yes | ❌ No |
| **Manage UL-NEC users** | ❌ No* | ✅ Yes |
| **Manage licenses** | ❌ No* | ✅ Yes |
| **View bugs/features** | ❌ No* | ✅ Yes |
| **Manage downloads** | ❌ No* | ✅ Yes |
| **View analytics** | ❌ No* | ✅ Yes |

*Unless they also have SaaS admin flag

---

## 🔍 HOW IT WORKS

### Authentication Flow:
1. User logs into WordPress (any role: subscriber, editor, admin)
2. User visits UL-NEC admin page (e.g., `/wp-admin/?page=ulnec-dashboard`)
3. Plugin calls `is_saas_admin()`:
   - Gets current user's email
   - Queries Supabase: `SELECT is_admin FROM ulnec_users WHERE email = ?`
   - Returns `true` if `is_admin = true`, else `false`
4. If `false`, shows 403 Access Denied
5. If `true`, shows admin page

### Code Example:
```php
// Check if user is SaaS admin
private function is_saas_admin() {
    if (!is_user_logged_in()) {
        return false;
    }
    
    $current_user = wp_get_current_user();
    $email = $current_user->user_email;
    
    $response = $this->supabase->request('GET', 
        'ulnec_users?email=eq.' . urlencode($email) . '&select=is_admin');
    
    return isset($response[0]['is_admin']) && $response[0]['is_admin'] === true;
}
```

---

## ✅ TESTING CHECKLIST

### As SaaS Admin (`durgaram@jdsancontrols.com`):
- [ ] Can login to WordPress
- [ ] Sees "UL-NEC" menu in WordPress admin sidebar
- [ ] Can access Dashboard, Users, Licenses, Downloads
- [ ] Can access Bugs & Features, Founders, Analytics
- [ ] **Cannot** access WordPress Settings, Themes, Plugins

### As WordPress Admin (your current admin):
- [ ] Can login to WordPress
- [ ] Has full WordPress access (themes, plugins, settings)
- [ ] **Cannot** see UL-NEC menu (unless also SaaS admin)

### As Regular User:
- [ ] Can login to WordPress
- [ ] **Cannot** see UL-NEC menu
- [ ] Gets 403 error if manually accessing `?page=ulnec-dashboard`

---

## 🛡️ SECURITY BENEFITS

1. **Least Privilege:** Users only get permissions they need
2. **Separation of Concerns:** Product management ≠ Site management
3. **Audit Trail:** Supabase tracks who has admin access
4. **Revocable:** Change `is_admin = false` to revoke access instantly
5. **No WordPress Dependency:** Works even if WordPress roles change

---

## 🔄 REVERTING (if needed)

To revert to old behavior (SaaS admin = WordPress admin):

1. **class-ulnec-admin.php** - Change line 27:
   ```php
   // FROM:
   'read',
   // TO:
   'manage_options',
   ```

2. **Remove access checks** - Delete `$this->check_access();` calls

3. **setup-admin.php** - Change line 34:
   ```php
   // FROM:
   $wp_user->set_role('subscriber');
   // TO:
   $wp_user->set_role('administrator');
   ```

---

## 📊 VERSION INFO

- **Previous Version:** 1.2.0
- **Current Version:** 1.3.0
- **Files Modified:** 3
- **Database Changes:** 1 column added
- **Breaking Changes:** None (backward compatible)

---

## 🤝 RECOMMENDATIONS

1. **Use SaaS admin for:** Product managers, customer support, operations team
2. **Use WordPress admin for:** Web developers, site designers, IT administrators
3. **Both roles for:** Yourself (owner) - set `is_admin = true` AND keep WordPress admin role
4. **Rotate credentials:** Change SaaS admin password quarterly
5. **Audit regularly:** Check `SELECT * FROM ulnec_users WHERE is_admin = true`

---

## 📞 SUPPORT

- Email: support@jdsancontrols.com
- Docs: https://jdsancontrols.com/docs
- Supabase Dashboard: https://supabase.com/dashboard/project/_
