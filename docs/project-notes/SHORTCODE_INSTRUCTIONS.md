# How to Create Login/Register/Dashboard Pages with Shortcodes

## The Problem
WordPress pages don't execute PHP code for security reasons. When you paste PHP code, it shows as text.

## The Solution
Use **shortcodes** instead! The plugin now has shortcodes that do all the work for you.

---

## Step-by-Step Instructions

### 1. Upload Updated Plugin

First, you need to re-upload the plugin with the new shortcodes:

1. Go to WordPress Admin → Plugins
2. Deactivate "UL-NEC Compliance"
3. Delete the plugin
4. Upload the updated plugin (create new zip from `ul-nec-compliance` folder)
5. Activate it again

### 2. Create Login Page

1. Go to **Pages → Add New**
2. Title: `Login`
3. In the content area (use Classic Editor or Code Editor mode), simply paste:
   ```
   [ulnec_login]
   ```
4. Set permalink to: `/login`
5. Click **Publish**

**That's it!** The shortcode will display the full login form with all styling.

### 3. Create Register Page

1. Go to **Pages → Add New**
2. Title: `Register`
3. In the content area, paste:
   ```
   [ulnec_register]
   ```
4. Set permalink to: `/register`
5. Click **Publish**

### 4. Create Dashboard Page

1. Go to **Pages → Add New**
2. Title: `Dashboard`
3. In the content area, paste:
   ```
   [ulnec_dashboard]
   ```
4. Set permalink to: `/dashboard`
5. Click **Publish**

---

## Available Shortcodes

| Shortcode | Description |
|-----------|-------------|
| `[ulnec_login]` | Complete login form with styling |
| `[ulnec_register]` | Registration form with auto-login |
| `[ulnec_dashboard]` | Full user dashboard with licenses & downloads |
| `[ulnec_download]` | Simple download button (for use anywhere) |

---

## What Each Page Does

### Login Page (`[ulnec_login]`)
- WordPress login form
- Redirects to /dashboard after login
- Links to register and forgot password
- Shows "already logged in" message if user is logged in

### Register Page (`[ulnec_register]`)
- Registration form (username, email, password)
- Password confirmation validation
- Auto-login after registration
- Auto-redirects to dashboard
- **Automatically syncs user to Supabase** (via plugin hook)

### Dashboard Page (`[ulnec_dashboard]`)
- Account information (email, username, tier, member since)
- License keys with copy-to-clipboard button
- License status (active/expired)
- Download button (only shows if user has active license)
- Quick action links (support, docs, logout)

---

## Testing

After creating the pages:

1. **Logout** from WordPress admin
2. Go to `jdsancontrols.com/register`
3. Create a test account
4. Should auto-redirect to `/dashboard`
5. Check Supabase → `ulnec_users` table → New user should appear
6. To test download: Manually add a license in Supabase, refresh dashboard

---

## Next Steps

1. Re-upload plugin with shortcodes ✅
2. Create 3 pages with shortcodes (5 minutes)
3. Test registration flow
4. Add test license in Supabase
5. Test download button

No more PHP code pasting needed! Just use the shortcodes. 🎉
