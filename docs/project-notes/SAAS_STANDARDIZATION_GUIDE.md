# 🏢 SaaS Features Standardization Guide

**Purpose**: Standardize Dashboard, Bug Reports, Feature Requests, and more across ALL SaaS products on jdsancontrols.com

**Date**: February 24, 2026  
**Site**: jdsancontrols.com  
**Current Products**: UL/NEC Plugin, React Apps (2+)  
**Future**: Multiple SaaS products

---

## 🎯 Vision: Multi-Product SaaS Platform

Instead of building separate dashboards for each product, create a **unified SaaS management system** where:

- ✅ One dashboard shows ALL user's products
- ✅ Bug reports tagged by product
- ✅ Feature requests across all products
- ✅ Unified billing for all subscriptions
- ✅ Single account, multiple products

---

## 📋 Standardized Features (Site-Wide)

### Core Pages (Product-Agnostic):
1. `/dashboard` - Shows all user's products & licenses
2. `/bug-report` - Bug reports (tagged by product)
3. `/feature-request` - Feature requests (tagged by product)
4. `/founders-progress` - Founding member program (all products)
5. `/account-settings` - Profile, password, preferences
6. `/billing` - All subscriptions & invoices
7. `/login` - Single sign-on for all products
8. `/register` - Create account (choose products during onboarding)

### Additional Recommended Pages:
9. `/support` - Unified support center
10. `/downloads` - All product downloads in one place
11. `/licenses` - All license keys across products
12. `/notifications` - Activity feed (all products)

---

## 🏗️ Architecture Recommendation

### Option 1: Rename Plugin (RECOMMENDED)

**Current**: `ul-nec-compliance` plugin  
**New**: `jdsan-saas-manager` or `jdsan-platform`

**Why?**  
- Generic name supports all products
- Easier to scale
- Cleaner branding

**Migration Steps:**
1. Duplicate `ul-nec-compliance` folder
2. Rename to `jdsan-saas-manager`
3. Update plugin header:
```php
/**
 * Plugin Name: JDS&N SaaS Platform Manager
 * Description: Unified dashboard, billing, and support for all JDS&N SaaS products
 */
```
4. Keep product-specific settings in database (product_id field)

---

### Option 2: Keep Current Plugin, Add Product Support

Modify existing plugin to support multiple products without renaming.

**Database Changes:**
```sql
-- Add product_id to users table
ALTER TABLE ulnec_users ADD COLUMN product_ids TEXT[];

-- Tag bugs by product
ALTER TABLE ulnec_bugs ADD COLUMN product VARCHAR(50);

-- Tag features by product  
ALTER TABLE ulnec_feature_requests ADD COLUMN product VARCHAR(50);

-- Add products table
CREATE TABLE saas_products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100),
    slug VARCHAR(50) UNIQUE,
    status VARCHAR(20),
    created_at TIMESTAMP DEFAULT NOW()
);

INSERT INTO saas_products (name, slug) VALUES
('UL/NEC Compliance Checker', 'ulnec'),
('React App 1', 'reactapp1'),
('React App 2', 'reactapp2');
```

---

## 📂 Step 1: Landing Page Template Setup

### Upload Template File

**Via cPanel File Manager:**
```
1. Login to Namecheap cPanel
2. File Manager → /public_html/wp-content/themes/[your-theme]/
3. Upload: page-ulnec-landing.php
4. Confirm upload
```

**Via FTP:**
```
Host: ftp.jdsancontrols.com
Path: /public_html/wp-content/themes/[your-theme]/
Upload: page-ulnec-landing.php
```

### Create WordPress Page

```
1. WordPress Admin → Pages → Add New
2. Title: "UL/NEC Compliance Checker"
3. Content: Leave blank (template handles it)
4. Page Attributes → Template: "UL/NEC Landing Page"
5. Permalink: /ulnec-compliance-checker/
6. Publish
```

**View**: https://jdsancontrols.com/ulnec-compliance-checker/

---

## 📄 Step 2: Create 8 Core Pages with Shortcodes

### Quick Method (SQL - 2 minutes):

```sql
-- Run this in WordPress database (wp_posts table)
INSERT INTO wp_posts (post_title, post_name, post_content, post_status, post_type, post_author)
VALUES
('Login', 'login', '[ulnec_login]', 'publish', 'page', 1),
('Register', 'register', '[ulnec_register]', 'publish', 'page', 1),
('Dashboard', 'dashboard', '[ulnec_dashboard]', 'publish', 'page', 1),
('Bug Report', 'bug-report', '[ulnec_bug_report]', 'publish', 'page', 1),
('Feature Request', 'feature-request', '[ulnec_feature_request]', 'publish', 'page', 1),
('Founders Progress', 'founders-progress', '[ulnec_founders_progress]', 'publish', 'page', 1),
('Account Settings', 'account-settings', '[ulnec_account_settings]', 'publish', 'page', 1),
('Billing', 'billing', '[ulnec_billing]', 'publish', 'page', 1);
```

**After SQL**: WordPress Admin → Settings → Permalinks → Save Changes (flush permalinks)

---

### Manual Method (WordPress Admin - 30 minutes):

For each page:
```
1. Pages → Add New
2. Enter title
3. Add shortcode in content
4. Set permalink
5. Publish
```

| Page Title | Permalink | Shortcode | Description |
|------------|-----------|-----------|-------------|
| Login | `/login` | `[ulnec_login]` | User login form |
| Register | `/register` | `[ulnec_register]` | Account creation |
| Dashboard | `/dashboard` | `[ulnec_dashboard]` | User overview |
| Bug Report | `/bug-report` | `[ulnec_bug_report]` | Submit bugs |
| Feature Request | `/feature-request` | `[ulnec_feature_request]` | Request features |
| Founders Progress | `/founders-progress` | `[ulnec_founders_progress]` | Founding members |
| Account Settings | `/account-settings` | `[ulnec_account_settings]` | User settings |
| Billing | `/billing` | `[ulnec_billing]` | Subscriptions |

---

## 🔧 Step 3: Update Support References

### Remove USA References & Update Email

**Files to Update:**

#### 1. Support Email in Shortcodes

File: `ul-nec-compliance/includes/class-ulnec-shortcodes.php`

Search for: `support@`  
Replace with: `support@jdsancontrols.com`

#### 2. Support Page Content

File: `ul-nec-compliance/includes/class-ulnec-frontend-pages.php`

**In `support_shortcode()` method:**
```php
// Remove USA phone numbers, addresses
// Keep only:
- Email: support@jdsancontrols.com
- Response time: 24-48 hours
- Working hours: 24/7 online support
```

#### 3. Email Templates

File: `ul-nec-compliance/includes/class-ulnec-emails.php`

Update all email footers:
```php
'email_support' => 'support@jdsancontrols.com'
```

Remove:
- Physical addresses
- USA phone numbers
- Region-specific content

---

## 🌍 Making Features Product-Agnostic

### Current State: UL/NEC-Specific
- Shortcodes named `ulnec_*`
- Database tables prefixed `ulnec_*`
- Support references UL/NEC only

### Recommended Changes:

#### 1. Add Product Context to Shortcodes

**Option A: Add parameter**
```php
[jdsan_dashboard product="ulnec"]
[jdsan_bug_report product="reactapp1"]
```

**Option B: Auto-detect from URL**
```php
// In shortcode handler:
$product = $this->detect_product_from_context();
// Looks at: URL, user's active products, cookie, etc.
```

#### 2. Update Dashboard to Show All Products

Current Dashboard (Product-Specific):
```
My UL/NEC License
Download UL/NEC Plugin
```

Multi-Product Dashboard:
```
My Products:
┌─ UL/NEC Compliance Checker
│  ├─ License: ULNEC-XXXX
│  ├─ Status: Active
│  └─ Download
│
└─ React App 1
   ├─ License: REACT-YYYY  
   ├─ Status: Active
   └─ Launch App
```

#### 3. Bug Reports with Product Selection

```php
<select name="product" required>
    <option value="">Select Product</option>
    <option value="ulnec">UL/NEC Compliance Checker</option>
    <option value="reactapp1">React App 1</option>
    <option value="reactapp2">React App 2</option>
</select>
```

---

## 🔄 Migration Path for Existing UL/NEC Users

When expanding to multi-product:

**Phase 1: Keep Current Plugin, Add Product Support**
1. Add `product` field to database tables
2. Default existing records to `product='ulnec'`
3. Update shortcodes to accept product parameter
4. Dashboard shows all products (if user has multiple)

**Phase 2: Gradual Rollout**
1. Launch second product (React app)
2. Unified dashboard shows both
3. Bug reports tagged by product
4. Single billing page for both

**Phase 3: Full Platform**
1. Rename plugin to generic name
2. Each product has own landing page
3. Unified account system
4. Cross-product analytics

---

## 📱 Recommended Site Structure

```
jdsancontrols.com/
│
├── (Homepage - lists all products)
│
├── Products/
│   ├── /ulnec-compliance-checker/     (UL/NEC landing)
│   ├── /react-app-1/                  (React app landing)
│   └── /react-app-2/                  (React app landing)
│
├── Account/ (Unified across products)
│   ├── /login/                        ✅
│   ├── /register/                     ✅
│   ├── /dashboard/                    ✅ (shows ALL products)
│   ├── /account-settings/             ✅
│   ├── /billing/                      ✅
│   ├── /licenses/                     (all product licenses)
│   └── /downloads/                    (all product downloads)
│
├── Support/ (Unified)
│   ├── /support/                      ✅
│   ├── /bug-report/                   ✅ (with product selector)
│   ├── /feature-request/              ✅ (with product selector)
│   ├── /founders-progress/            ✅ (across all products)
│   └── /knowledge-base/               (all products)
│
└── Company/
    ├── /about/
    ├── /contact/
    └── /terms/
```

---

## 🎨 Standardized Design System

### UI Components (Site-Wide)

**Colors:**
```css
:root {
    --primary: #667eea;
    --secondary: #764ba2;
    --success: #10b981;
    --error: #ef4444;
    --warning: #f59e0b;
}
```

**Card Layout:**
```css
.saas-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
```

**Buttons:**
```css
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
}
```

---

## 📊 Database Schema for Multi-Product

### Products Table
```sql
CREATE TABLE saas_products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT NOW()
);
```

### User Products (Many-to-Many)
```sql
CREATE TABLE user_products (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES ulnec_users(id),
    product_id INTEGER REFERENCES saas_products(id),
    license_key VARCHAR(50),
    status VARCHAR(20) DEFAULT 'active',
    tier VARCHAR(20),
    expires_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT NOW()
);
```

### Tagged Bugs
```sql
ALTER TABLE ulnec_bugs 
ADD COLUMN product_id INTEGER REFERENCES saas_products(id);
```

### Tagged Feature Requests
```sql
ALTER TABLE ulnec_feature_requests 
ADD COLUMN product_id INTEGER REFERENCES saas_products(id);
```

---

## ✅ Implementation Checklist

### Phase 1: Setup Current Product (UL/NEC)
- [ ] Upload landing page template
- [ ] Create 8 pages with shortcodes
- [ ] Update support email to support@jdsancontrols.com
- [ ] Remove USA references from all pages
- [ ] Test all pages and shortcodes

### Phase 2: Prepare for Multi-Product
- [ ] Add `saas_products` table
- [ ] Add `product_id` to bugs and features
- [ ] Create product selector dropdown
- [ ] Update dashboard to support multiple products
- [ ] Test with UL/NEC as first product

### Phase 3: Launch Second Product
- [ ] Add second product to database
- [ ] Create second landing page
- [ ] Test cross-product functionality
- [ ] Unified billing across products
- [ ] Launch!

---

## 🎯 My Recommendation

**Start with Phase 1** (focus on UL/NEC only):
1. Get UL/NEC fully working
2. Use current plugin name (`ul-nec-compliance`)
3. Create the 8 pages with shortcodes
4. Launch beta

**Then Phase 2** (prepare for expansion):
1. Add database support for multiple products
2. Keep shortcode names (`ulnec_*` is fine)
3. Add product context internally
4. Refactor dashboard to show multiple products

**Finally Phase 3** (add new products):
1. Add React apps to the system
2. They share: login, dashboard, support, billing
3. Each has own landing page
4. Seamless multi-product experience

---

## 💡 Additional Recommendations

### 1. **Single Sign-On (SSO)**
Use WordPress cookies for all products (React apps use WordPress REST API for auth)

### 2. **Unified Billing**
One Stripe/PayPal account, products are line items in invoices

### 3. **Product-Specific Branding**
Customize colors/logos per product while keeping core UI consistent

### 4. **Analytics**
Track usage across all products in one dashboard

### 5. **Cross-Selling**
Show "You might also like..." on dashboard for other products

---

## 📞 Support Configuration

**Standard Support Response:**
```
Email: support@jdsancontrols.com
Response Time: 24-48 hours
Availability: 24/7 online support
```

**No Geographic References:**
- ❌ Don't mention: USA, specific states, phone numbers
- ✅ Keep generic: "Global support", "Online support"

---

## 🚀 Quick Start

**Today (2 hours):**
1. Upload `page-ulnec-landing.php` to theme
2. Create landing page in WordPress
3. Run SQL to create 8 pages
4. Flush permalinks
5. Test all pages

**This Week:**
1. Update support email references
2. Remove USA content
3. Test bug report & feature request
4. Launch UL/NEC beta

**Next Month:**
1. Add products table
2. Prepare for React app integration
3. Build multi-product dashboard
4. Launch second product

---

**Questions?** Reply with specific areas you need help with!

**Status:** Ready to implement 🚀  
**Estimated Time:** 2-3 hours for Phase 1
