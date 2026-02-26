# Complete UL/NEC Plugin + Nexus Theme Workflow

**Date:** February 25, 2026  
**Site:** jdsancontrols.com  
**Plugin Version:** 1.3.1  

---

## Architecture Overview

```
Nexus Theme (WordPress)
   └── UL/NEC Compliance Plugin (WordPress Plugin)
         └── Supabase (External Database)
```

The **Nexus theme** provides the visual framework — templates, styles, navigation.  
The **UL/NEC plugin** is a fully independent WordPress plugin that runs on top of it.  
It does NOT depend on Nexus to function, but it uses Nexus page templates for consistent styling.

---

## Plugin File Structure & What Each Class Does

| File | Role |
|---|---|
| `ul-nec-compliance.php` | Main entry point — loads all classes, registers hooks |
| `class-ulnec-supabase.php` | API bridge to Supabase (all DB reads/writes go here) |
| `class-ulnec-auth.php` | Handles WordPress login/register + session management |
| `class-ulnec-license.php` | Creates, validates, activates license keys |
| `class-ulnec-download.php` | Secure `.msi` file download (checks valid license first) |
| `class-ulnec-payment.php` | PayPal payment processing + license auto-generation |
| `class-ulnec-emails.php` | Sends welcome, license delivery, bug confirmation emails |
| `class-ulnec-admin.php` | All WP Admin pages (dashboard, users, analytics, settings) |
| `class-ulnec-shortcodes.php` | `[ulnec_login]`, `[ulnec_dashboard]` etc. rendered on frontend |
| `class-ulnec-frontend-pages.php` | Extended shortcodes for billing, bug reports, feature requests |
| `class-ulnec-ajax.php` | Handles all AJAX form submissions (no page reload) |
| `class-ulnec-frontend.php` | Enqueues CSS/JS for frontend pages |

---

## Complete User Journey

```
jdsancontrols.com/ul-nec-compliance-checker   ← Nexus template: UL/NEC Landing Page
         ↓ clicks "Start 30-Day Free Trial"
/register                                      ← [ulnec_register] shortcode
         ↓ creates account → WordPress user created + Supabase record
/login                                         ← [ulnec_login] shortcode (wp_login_form)
         ↓ logs in → redirects to
/dashboard                                     ← [ulnec_dashboard] shortcode
         ↓ no license → clicks "View Pricing"
/billing                                       ← [ulnec_billing] shortcode
         ↓ selects plan → PayPal checkout
         ↓ PayPal IPN callback → class-ulnec-payment.php
         ↓ license auto-generated (XXXX-XXXX-XXXX-XXXX format)
         ↓ license delivery email sent (class-ulnec-emails.php)
/dashboard                                     ← now shows license key + Download Plugin button
         ↓ clicks Download
         ↓ class-ulnec-download.php validates license → serves .msi from Supabase storage
         ↓ user installs .msi → uses AutoCAD plugin
         ↓ plugin checks license on launch → Supabase validates
```

---

## WordPress Pages & Shortcodes

| Page Title       | Slug               | Shortcode                  | Nexus Template           |
|------------------|--------------------|----------------------------|--------------------------|
| Landing Page     | /ul-nec-compliance-checker | (full template)    | UL/NEC Landing Page      |
| Register         | /register          | `[ulnec_register]`         | UL/NEC Register Page     |
| Login            | /login             | `[ulnec_login]`            | UL/NEC Login Page        |
| Dashboard        | /dashboard         | `[ulnec_dashboard]`        | UL/NEC Dashboard         |
| Billing          | /billing           | `[ulnec_billing]`          | UL/NEC Billing           |
| Bug Report       | /bug-report        | `[ulnec_bug_report]`       | Default                  |
| Feature Request  | /feature-request   | `[ulnec_feature_request]`  | Default                  |
| Account Settings | /account-settings  | `[ulnec_account_settings]` | Default                  |

---

## How Nexus Theme Connects

| Nexus File | What it does for UL/NEC |
|---|---|
| `page-ulnec-landing.php` | Full sales page — hero, pricing cards, FAQ, CTAs |
| `page-ulnec-login.php` | Styled login page template |
| `page-ulnec-register.php` | Styled register page with beta pricing highlight |
| `page-ulnec-dashboard.php` | Dashboard layout with sidebar navigation |
| `page-ulnec-billing.php` | Billing/pricing page template |
| Theme CSS | Base fonts, colors, layout variables used by plugin CSS |

WordPress pages use these templates via **WP Admin → Pages → Edit → Page Attributes → Template dropdown**.  
The shortcodes render dynamic content inside the template's content area.

---

## Admin Side (WP Admin → UL/NEC menu)

| Admin Page | What you manage |
|---|---|
| Dashboard | User count, license count, download stats at a glance |
| Users | All registered accounts, tiers, license status |
| Licenses | Generate, view, revoke license keys manually |
| Bugs & Features | Review submitted bugs and feature requests from beta users |
| Beta Analytics | 7-day signup bar chart, conversion rate, engagement metrics |
| Settings → Supabase | Supabase URL, anon key, service key |
| Settings → Page Links | Set which WP page = Pricing, Login, Register, Dashboard |

---

## Data Flow (Supabase Tables)

```
WordPress User Register → wp_users (WordPress DB)
                        + ulnec_users (Supabase) ← synced via plugin

PayPal Payment Success  → ulnec_licenses (Supabase) ← auto-generated key

Download event          → ulnec_downloads (Supabase) ← tracks per user

Bug submission          → ulnec_bugs (Supabase)

Feature request         → ulnec_features (Supabase)

Founders application    → ulnec_founders (Supabase)
```

---

## Email Automation

| Trigger | Email Sent | Class |
|---|---|---|
| User registers | Welcome email + next steps | `class-ulnec-emails.php` |
| PayPal payment confirmed | License key delivery email | `class-ulnec-emails.php` |
| Bug report submitted | Bug confirmation email (#ID) | `class-ulnec-emails.php` |
| Feature request submitted | Feature confirmation email | `class-ulnec-emails.php` |
| License expiring soon | Renewal reminder | `class-ulnec-emails.php` |

---

## Key Configuration Checklist

### Supabase Setup
- [ ] Run `BETA_DATABASE_SCHEMA.sql` in Supabase SQL Editor (creates 11 tables)
- [ ] Create Storage bucket `ulnec-downloads` (Private)
- [ ] Create Storage bucket `ulnec-screenshots` (Public)
- [ ] Upload `.msi` file → `ulnec-downloads/UL-NEC-Compliance-Plugin-Latest.msi`
- [ ] Copy: Project URL, anon key, service_role key

### WordPress Plugin Settings (WP Admin → UL/NEC → Settings)
- [ ] Supabase URL entered
- [ ] Supabase Anon Key entered
- [ ] Supabase Service Key entered
- [ ] **Page Links → Pricing/Billing Page** = Billing page
- [ ] **Page Links → Login Page** = Login page
- [ ] **Page Links → Register Page** = Register page
- [ ] **Page Links → Dashboard Page** = Dashboard page

### PayPal
- [ ] PayPal Client ID entered in Settings
- [ ] PayPal Secret entered in Settings
- [ ] IPN/Webhook URL configured: `jdsancontrols.com/?ulnec_paypal_ipn=1`

### Email
- [ ] SMTP configured (WP Mail SMTP plugin recommended)
- [ ] From address: support@jdsancontrols.com
- [ ] Test email sent and received

### Nexus Theme
- [ ] Nexus theme activated (not just installed)
- [ ] All 8 pages created with correct slugs
- [ ] All pages assigned correct Nexus templates
- [ ] Landing page "Start 30-Day Free Trial" button → `/register`
- [ ] `page-ulnec-landing.php` uploaded to `wp-content/themes/nexus/`

---

## Pricing Tiers (Beta Launch)

| Tier | Price | Features |
|---|---|---|
| Free Trial | $0 / 30 days | Full access, 10 panel checks, no credit card |
| Beta Launch Special | $75/month forever | Unlimited checks, priority support, API access |
| Regular Monthly | $150/month | Same as beta (post-launch price) |

---

## Files to Upload After Code Changes (Feb 25, 2026)

Upload these 4 files via **EasyWP File Manager**:

| Local File | Upload To |
|---|---|
| `nexus-theme/page-ulnec-landing.php` | `wp-content/themes/nexus/` |
| `ul-nec-compliance/includes/class-ulnec-admin.php` | `wp-content/plugins/ul-nec-compliance/includes/` |
| `ul-nec-compliance/includes/class-ulnec-shortcodes.php` | `wp-content/plugins/ul-nec-compliance/includes/` |
| `ul-nec-compliance/includes/class-ulnec-frontend-pages.php` | `wp-content/plugins/ul-nec-compliance/includes/` |

---

## Testing the Full Flow

1. Visit `jdsancontrols.com/ul-nec-compliance-checker` → landing page renders
2. Click **Start 30-Day Free Trial** → goes to `/register`
3. Register a new test account → welcome email received
4. Auto-redirected to `/dashboard` → shows account info, FREE tier badge
5. Click **View Pricing** → goes to `/billing`
6. Complete PayPal test payment → license key generated
7. License delivery email received with key
8. Dashboard → **Your Licenses** shows the key
9. Click **Download Plugin** → `.msi` file downloads from Supabase
10. Submit a bug report at `/bug-report` → confirmation email received
11. WP Admin → UL/NEC → Beta Analytics → all stats visible
