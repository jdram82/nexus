# UL/NEC Workflow Setup Guide

## 📋 Overview

Complete guide for setting up the UL/NEC Compliance Checker page workflow with automatic navigation from landing page → register → login → dashboard → billing.

---

## 🚀 Quick Setup (5 Minutes)

### Step 1: Run the Page Setup Script

1. **Upload the setup script** to your WordPress root directory:
   - File: `setup-ulnec-pages.php`
   - Location: Same folder as `wp-config.php`

2. **Visit the setup URL** in your browser:
   ```
   https://jdsancontrols.com/setup-ulnec-pages.php
   ```

3. **Wait for completion** - You'll see:
   - ✓ Pages Created Successfully
   - List of all created pages with their URLs

4. **Delete the setup file** for security:
   ```bash
   rm /var/www/html/setup-ulnec-pages.php
   ```

### Step 2: Assign Page Templates

1. Go to **Pages → All Pages** in WordPress admin

2. For each page below, click **Edit** and assign the template:

   | Page | Template to Assign |
   |------|-------------------|
   | Login | UL/NEC Login Page |
   | Register | UL/NEC Register Page |
   | Dashboard | UL/NEC Dashboard |
   | Billing | UL/NEC Billing |
   | Bug Report | (default) |
   | Feature Request | (default) |
   | Account Settings | (default) |

3. **Update** each page after selecting the template

### Step 3: Configure UL/NEC Landing Page

1. Edit the page: **UL/NEC Compliance Checker**

2. In the editor, find the **"Start 30-Day Free Trial"** button

3. Update the button link to:
   ```
   /register
   ```

4. Find the **"Watch 3-Min Demo"** button (if exists)

5. Update or keep as-is for demo video

6. **Update** the page

---

## 📄 Pages Created

### 1. `/login` - Login Page
- **Template:** UL/NEC Login Page
- **Shortcode:** `[ulnec_login]`
- **Features:**
  - Clean, modern login form
  - Link to register page
  - Link back to landing page
  - Gradient background

### 2. `/register` - Registration Page
- **Template:** UL/NEC Register Page
- **Shortcode:** `[ulnec_register]`
- **Features:**
  - Beta launch pricing highlight
  - Benefits list (30-day trial, no credit card, etc.)
  - Terms & privacy links
  - Link to login page

### 3. `/dashboard` - User Dashboard
- **Template:** UL/NEC Dashboard
- **Shortcode:** `[ulnec_dashboard]`
- **Features:**
  - Full sidebar navigation
  - Stats grid
  - Recent projects
  - Quick actions
  - User profile info

### 4. `/bug-report` - Bug Reporting
- **Template:** (default or dashboard)
- **Shortcode:** `[ulnec_bug_report]`
- **Features:**
  - Bug submission form
  - Category selection
  - Priority levels
  - File attachments

### 5. `/feature-request` - Feature Requests
- **Template:** (default or dashboard)
- **Shortcode:** `[ulnec_feature_request]`
- **Features:**
  - Feature suggestion form
  - Voting system
  - Category selection
  - Status tracking

### 6. `/billing` - Billing & Subscription
- **Template:** UL/NEC Billing
- **Shortcode:** `[ulnec_billing]`
- **Features:**
  - Current plan display
  - Pricing grid (Free Trial, Beta $75, Regular $150)
  - Payment method management
  - Billing history table
  - Upgrade/downgrade options

### 7. `/account-settings` - Account Settings
- **Template:** (default or dashboard)
- **Shortcode:** `[ulnec_account_settings]`
- **Features:**
  - Profile information
  - Email preferences
  - Password change
  - Account deletion

---

## 🔄 Complete User Workflow

### New User Journey

```
1. Landing Page (/ul-nec-compliance-checker)
   └─> Clicks "Start 30-Day Free Trial"
   
2. Register Page (/register)
   └─> Fills out registration form
   └─> Creates account
   └─> Auto-login after registration
   
3. Dashboard (/dashboard) ← Redirect after registration
   └─> Sees welcome message
   └─> Views quick stats
   └─> Can start first compliance check
   
4. Navigation available:
   ├─> /bug-report (Report issues)
   ├─> /feature-request (Suggest features)
   ├─> /billing (Manage subscription)
   └─> /account-settings (Update profile)
```

### Existing User Journey

```
1. Landing Page (/ul-nec-compliance-checker)
   └─> Clicks "Login" or visits /login directly
   
2. Login Page (/login)
   └─> Enters credentials
   └─> Authenticates
   
3. Dashboard (/dashboard) ← Redirect after login
   └─> Continues work
```

---

## 🎨 Page Templates Overview

### Templates Included in Theme

1. **page-ulnec-landing.php** (952 lines)
   - Full landing page design
   - Hero section, features, pricing
   - Call-to-action buttons
   - No header/footer for clean look

2. **page-ulnec-login.php** (New)
   - Clean login interface
   - Gradient background
   - Social login options placeholder
   - Responsive design

3. **page-ulnec-register.php** (New)
   - Registration form
   - Beta pricing highlight
   - Benefits showcase
   - Terms acceptance

4. **page-ulnec-dashboard.php** (New)
   - Full app-like interface
   - Sidebar navigation
   - Stats grid
   - Content cards
   - User profile display

5. **page-ulnec-billing.php** (New)
   - Pricing comparison table
   - Current plan status
   - Payment method management
   - Billing history

---

## 🔧 Customization Guide

### Modify Button Links on Landing Page

**File:** Page editor for "UL/NEC Compliance Checker"

**Find:**
```html
<button>Start 30-Day Free Trial</button>
```

**Update button link to:**
```html
<a href="/register">
    <button>Start 30-Day Free Trial</button>
</a>
```

### Update Pricing

**File:** `/billing` page content or `page-ulnec-billing.php`

**Edit these values:**
```html
<p class="price">$75<span>/month forever</span></p>
```

### Change Redirect After Login/Register

**File:** UL/NEC Plugin `includes/class-auth.php` (or similar)

**Set redirect:**
```php
// After successful login/registration
wp_redirect( home_url( '/dashboard' ) );
exit;
```

### Add/Remove Sidebar Navigation Items

**File:** `page-ulnec-dashboard.php` lines 180-220

**Add new menu item:**
```html
<a href="<?php echo home_url('/new-page'); ?>">
    <span class="icon">🔥</span>
    <span>New Feature</span>
</a>
```

---

## 🔐 Security Considerations

### 1. Protect Dashboard Pages

Add to functions.php or plugin:

```php
function ulnec_protect_dashboard_pages() {
    // Get current page
    $current_page = get_queried_object();
    
    // Pages that require login
    $protected_pages = array('dashboard', 'bug-report', 'feature-request', 'billing', 'account-settings');
    
    // Check if current page is protected and user is not logged in
    if ( $current_page && in_array( $current_page->post_name, $protected_pages ) && !is_user_logged_in() ) {
        wp_redirect( home_url('/login?redirect=' . urlencode($_SERVER['REQUEST_URI'])) );
        exit;
    }
}
add_action('template_redirect', 'ulnec_protect_dashboard_pages');
```

### 2. Auto-Redirect Logged-in Users Away from Login/Register

```php
function ulnec_redirect_logged_in_users() {
    if ( is_user_logged_in() && ( is_page('login') || is_page('register') ) ) {
        wp_redirect( home_url('/dashboard') );
        exit;
    }
}
add_action('template_redirect', 'ulnec_redirect_logged_in_users');
```

### 3. Handle Post-Login Redirect

```php
function ulnec_login_redirect( $redirect_to, $request, $user ) {
    // If redirect parameter exists, use it
    if ( isset($_GET['redirect']) ) {
        return esc_url($_GET['redirect']);
    }
    
    // Otherwise, redirect to dashboard
    return home_url('/dashboard');
}
add_filter('login_redirect', 'ulnec_login_redirect', 10, 3);
```

---

## 📊 Shortcodes Reference

All shortcodes should be provided by the **UL/NEC Compliance Plugin**:

| Shortcode | Purpose | Expected Output |
|-----------|---------|-----------------|
| `[ulnec_login]` | Login form | Email, password, submit button |
| `[ulnec_register]` | Registration form | Name, email, password, company fields |
| `[ulnec_dashboard]` | Dashboard content | Stats, recent projects, quick actions |
| `[ulnec_bug_report]` | Bug report form | Title, description, priority, file upload |
| `[ulnec_feature_request]` | Feature request form | Title, description, category |
| `[ulnec_billing]` | Billing management | Current plan, payment method, invoice history |
| `[ulnec_account_settings]` | Account settings | Profile fields, password change, preferences |

---

## 🐛 Troubleshooting

### Template Not Showing in Editor

**Problem:** Can't find "UL/NEC Login Page" template in dropdown

**Solution:**
1. Go to Appearance → Themes
2. Switch to a different theme (Twenty Twenty-Three)
3. Switch back to Nexus Theme
4. Return to edit page - template should appear

### Shortcode Displays as Text

**Problem:** Page shows `[ulnec_login]` instead of login form

**Solution:**
1. Verify UL/NEC plugin is installed and activated
2. Check plugin version is v1.3.1 or later
3. View plugin code to confirm shortcodes are registered

### 404 Error on Pages

**Problem:** Pages return 404 Not Found

**Solution:**
1. Go to Settings → Permalinks
2. Click "Save Changes" (no need to change anything)
3. This flushes the permalink cache
4. Try visiting pages again

### Redirect Loop on Dashboard

**Problem:** Dashboard keeps redirecting back to login

**Solution:**
1. Check if user session is being maintained
2. Verify cookies are enabled
3. Check for conflicting security plugins
4. Review redirect code for logic errors

### Styling Issues (CSS Not Loading)

**Problem:** Pages look broken or unstyled

**Solution:**
1. Hard refresh browser (Ctrl+Shift+R or Cmd+Shift+R)
2. Clear WordPress cache (if caching plugin active)
3. Check browser console for CSS errors
4. Verify theme is v3.2.3 or later

---

## 📦 Files Included

### Theme Templates (in nexus-theme/)
- `page-ulnec-landing.php` (952 lines) - Landing page
- `page-ulnec-login.php` (new) - Login page
- `page-ulnec-register.php` (new) - Registration page
- `page-ulnec-dashboard.php` (new) - Dashboard
- `page-ulnec-billing.php` (new) - Billing page

### Setup Script (temporary)
- `setup-ulnec-pages.php` - One-time page creation script

### Documentation
- `ULNEC_WORKFLOW_SETUP.md` (this file)

---

## ✅ Testing Checklist

### Pre-Launch Testing

- [ ] Landing page loads correctly with template
- [ ] "Start Trial" button links to `/register`
- [ ] Registration page displays with template
- [ ] Registration form submits successfully
- [ ] User auto-redirects to `/dashboard` after registration
- [ ] Login page displays with template
- [ ] Login form authenticates correctly
- [ ] User redirects to `/dashboard` after login
- [ ] Dashboard sidebar navigation works
- [ ] All dashboard links are functional
- [ ] Bug report form submits correctly
- [ ] Feature request form submits correctly
- [ ] Billing page displays pricing correctly
- [ ] Billing upgrade/downgrade flows work
- [ ] Account settings save correctly
- [ ] Logout redirects to landing page
- [ ] Protected pages redirect to login when not authenticated
- [ ] Logged-in users can't access login/register pages
- [ ] Mobile responsive design works on all pages

### User Experience Testing

- [ ] New user can complete entire signup flow
- [ ] Existing user can login and access dashboard
- [ ] Navigation between pages is intuitive
- [ ] Forms have proper validation
- [ ] Error messages display correctly
- [ ] Success messages display correctly
- [ ] Loading states are appropriate
- [ ] No broken images or CSS
- [ ] Cross-browser testing (Chrome, Firefox, Safari, Edge)

---

## 🚀 Next Steps

### After Pages Are Set Up

1. **Configure UL/NEC Plugin:**
   - Install `ul-nec-compliance-v1.3.1.zip`
   - Configure Supabase credentials
   - Set up SMTP for emails
   - Configure payment gateway (if using)

2. **Test Complete Workflow:**
   - Create test user account
   - Perform compliance check
   - Generate report
   - Test billing process

3. **SEO & Marketing:**
   - Add meta descriptions to all pages
   - Set up Google Analytics
   - Configure conversion tracking
   - Create email sequences

4. **Launch Checklist:**
   - [ ] All pages tested and working
   - [ ] SSL certificate active (HTTPS)
   - [ ] Backup system in place
   - [ ] Support email configured
   - [ ] Terms of Service page created
   - [ ] Privacy Policy page created
   - [ ] Beta pricing locked in database
   - [ ] Email notifications working

---

## 📞 Support

If you encounter issues:

1. **Check this documentation** first
2. **Review error logs:** `wp-content/debug.log`
3. **Test with default theme** to isolate theme issues
4. **Disable plugins** one by one to find conflicts
5. **Check browser console** for JavaScript errors

---

## 📝 Version History

- **v1.0.0** (Feb 25, 2026) - Initial workflow setup
  - Created 7 pages with templates
  - Setup script for automated page creation
  - Complete navigation flow
  - Dashboard with sidebar
  - Billing page with pricing

---

**Status:** ✅ Ready to deploy  
**Theme Version:** Nexus v3.2.3  
**Plugin Version:** UL/NEC Compliance v1.3.1
