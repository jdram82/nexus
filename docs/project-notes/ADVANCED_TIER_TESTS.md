# Nexus Advanced Tier Testing Guide

**Tier:** Advanced  
**Price:** $299/year  
**Target User:** Agencies, freelancers, content creators  
**Features:** All Pro features + 6 Advanced features (AI, marketplace, white-label, etc.)

---

## Pre-Test Setup

### 1. License Activation
- [ ] Navigate to **Nexus Options → License**
- [ ] Enter Advanced license key
- [ ] Verify activation shows "Advanced Tier" badge (pink gradient)
- [ ] Confirm Agency features still locked

### 2. Verify Tier Access
- [ ] `Nexus_License_Manager::get_tier()` returns `'advanced'`
- [ ] All Pro features accessible
- [ ] Advanced menu items visible
- [ ] Agency features show lock icons

---

## Inherited Pro Features (Quick Verification)

- [ ] Plugin Harmony working
- [ ] REST API functional
- [ ] Template Library: Unlimited cloud storage (not 5-limit)

---

## Advanced Feature Testing

### Feature 1: Plugin Orchestrator (Advanced)

**Location:** Nexus Options → Plugin Orchestrator  
**Test File:** `pro/plugin-orchestrator/class-plugin-orchestrator.php`

#### Test Cases:

1. **Deep Integration Dashboard**
   - [ ] Navigate to Plugin Orchestrator page
   - [ ] Verify dashboard shows all detected plugins
   - [ ] Check integration cards with status indicators

2. **Auto-Styling**
   - [ ] Install Gravity Forms
   - [ ] Create test form
   - [ ] Verify Nexus design tokens applied automatically
   - [ ] Check form matches theme colors/fonts

3. **Integration Toggles**
   - [ ] Test WooCommerce integration toggle
   - [ ] Disable → verify styles revert to default
   - [ ] Re-enable → styles re-apply

**Expected Results:**
- Dashboard loads without errors
- Design tokens inject into plugin stylesheets
- Integration status accurate

---

### Feature 2: Dynamic Loop Builder (Advanced)

**Location:** Nexus Options → Loop Builder  
**Test File:** `pro/loop-builder/class-loop-builder.php`

#### Test Cases:

1. **Visual Query Builder**
   - [ ] Navigate to Loop Builder page
   - [ ] Create new loop "Featured Products"
   - [ ] Select post type: Products
   - [ ] Add query conditions (e.g., category = electronics)
   - [ ] Click "Add Condition" → new row appears

2. **Live Preview**
   - [ ] Build query: 6 posts, category = blog
   - [ ] Preview updates in real-time (< 1s)
   - [ ] Change to grid layout → preview updates
   - [ ] Test pagination controls

3. **Template Customization**
   - [ ] Modify template HTML in code editor
   - [ ] Use tokens: `{post_title}`, `{post_thumbnail}`
   - [ ] Save → preview reflects changes
   - [ ] Test syntax highlighting

4. **Save & Use Loop**
   - [ ] Save loop as "Latest Posts"
   - [ ] Insert shortcode in page: `[nexus_loop id="123"]`
   - [ ] Verify loop renders on frontend
   - [ ] Check responsive grid (mobile/tablet/desktop)

**Expected Results:**
- Query builder intuitive (3-panel interface)
- Live preview accurate
- Shortcode works on frontend
- No AJAX errors in console

**Performance:**
- [ ] Loop preview renders < 500ms
- [ ] Complex queries (50+ posts) perform well

---

### Feature 3: AI Template Generator (Advanced)

**Location:** Nexus Options → AI Generator (Advanced)  
**Test File:** `pro/ai/class-template-generator.php`

#### Test Cases:

1. **Credit System**
   - [ ] Check credits: 100/month for Advanced
   - [ ] Generate 1 template → credits decrement (99 remaining)
   - [ ] Verify credit counter updates in UI

2. **Template Generation**
   - [ ] Use example prompt: "SaaS Landing Page"
   - [ ] Click "Generate Template with AI"
   - [ ] Wait for generation (should complete < 5s for mock)
   - [ ] Verify template preview appears
   - [ ] Check sections detected (hero, features, pricing, etc.)

3. **Advanced Options**
   - [ ] Set primary color: #FF6600
   - [ ] Choose typography: Modern
   - [ ] Select layout: Full Width
   - [ ] Generate → verify template uses these settings

4. **Template Refinement**
   - [ ] Click "Refine with AI"
   - [ ] Enter: "Make hero section taller"
   - [ ] Apply → template updates (1 credit used)
   - [ ] Verify change reflected in preview

5. **Device Preview**
   - [ ] Toggle desktop view → template full width
   - [ ] Toggle tablet view → responsive layout
   - [ ] Toggle mobile view → single column

6. **Save Template**
   - [ ] Click "Save Template"
   - [ ] Enter name: "My SaaS Page"
   - [ ] Verify template saved to Template Library
   - [ ] Check export code works (download HTML/CSS)

**Expected Results:**
- AI generates templates in < 5 seconds (mock data)
- Credit system enforces 100/month limit
- Preview responsive and accurate
- Templates save to library correctly

**Credit Limit Test:**
- [ ] Simulate 100 generations in test DB
- [ ] Try 101st generation → error "No credits remaining"
- [ ] Verify upgrade prompt to Agency tier (500 credits)

---

### Feature 4: AI Documentation Generator (Advanced)

**Location:** Nexus Options → AI Docs (Advanced)  
**Test File:** `pro/ai/class-docs-generator.php`

#### Test Cases:

1. **File Upload**
   - [ ] Upload README.md file
   - [ ] Verify file appears in uploaded list
   - [ ] Click "Analyze Content"
   - [ ] Check analysis shows: pages, sections, code examples

2. **GitHub Import**
   - [ ] Switch to "GitHub Import" tab
   - [ ] Enter repo: `https://github.com/facebook/react`
   - [ ] Click "Import from GitHub"
   - [ ] Verify README fetches successfully
   - [ ] Analyze → should detect multiple sections

3. **Paste Content**
   - [ ] Switch to "Paste Content" tab
   - [ ] Paste OpenAPI spec (JSON format)
   - [ ] Set type: OpenAPI/Swagger
   - [ ] Analyze → detect API endpoints

4. **Documentation Options**
   - [ ] Enable: Search functionality
   - [ ] Enable: Sidebar navigation
   - [ ] Enable: Table of contents
   - [ ] Enable: API reference pages
   - [ ] Select style: GitBook Style

5. **Generate Docs Site**
   - [ ] Click "Generate Docs Site"
   - [ ] Wait for generation (should complete < 10s)
   - [ ] Verify success message with URL
   - [ ] Click "View Site" → opens in new tab

6. **Verify Generated Site**
   - [ ] Homepage loads with search bar
   - [ ] Sidebar navigation present
   - [ ] Pages created from markdown sections
   - [ ] API reference pages generated
   - [ ] Syntax highlighting on code blocks

**Expected Results:**
- All 3 input methods work (upload, GitHub, paste)
- OpenAPI specs parsed correctly
- Documentation sites generated without errors
- Sites use AI credits from template generator pool

**Analysis Accuracy:**
- [ ] Markdown: Correctly detects H1/H2 headers
- [ ] OpenAPI: Parses paths and endpoints
- [ ] Code examples: Syntax highlighted

---

### Feature 5: Marketplace (Advanced - Integrated with Template Library)

**Location:** Nexus Options → Templates → Marketplace tab  
**Test File:** `pro/templates/class-template-library.php` (marketplace section)

#### Test Cases:

1. **Marketplace Access**
   - [ ] Navigate to Templates page
   - [ ] Click "Marketplace" tab
   - [ ] Verify tab accessible (no lock screen)
   - [ ] Dashboard shows creator stats

2. **Template Submission** (Mock)
   - [ ] Click "Submit Template to Marketplace"
   - [ ] Fill form: Title, description, price
   - [ ] Upload screenshot
   - [ ] Submit → verify success message

3. **Revenue Dashboard** (Mock Data)
   - [ ] Check "Total Sales": $0 initially
   - [ ] Check "Active Templates": 0
   - [ ] Check "Commission Rate": 30%

**Expected Results:**
- Marketplace tab unlocked for Advanced users
- Creator dashboard displays correctly
- 30% commission structure visible

---

### Feature 6: White-Label System (Advanced)

**Location:** Nexus Options → White Label (Advanced)  
**Test File:** `pro/agency/class-white-label.php`

#### Test Cases:

1. **Enable White Labeling**
   - [ ] Navigate to White Label page
   - [ ] Toggle "Enable White Labeling" ON
   - [ ] Save settings

2. **Theme Information**
   - [ ] Change theme name: "MyBrand Theme"
   - [ ] Change description: "Custom theme for clients"
   - [ ] Set author: "MyCompany"
   - [ ] Set author URL: "https://mycompany.com"
   - [ ] Save → verify settings persist

3. **Admin Branding**
   - [ ] Upload admin logo (PNG, 200x50px)
   - [ ] Set primary color: #FF5733
   - [ ] Set footer text: "Built by MyCompany"
   - [ ] Save → refresh admin page
   - [ ] Verify logo appears in admin bar
   - [ ] Check primary color applied to buttons

4. **Login Screen Branding**
   - [ ] Upload login logo (320x80px)
   - [ ] Set login background: #F0F0F0
   - [ ] Set login URL: "https://mycompany.com"
   - [ ] Logout → check login page
   - [ ] Verify custom logo displays
   - [ ] Click logo → redirects to set URL

5. **Hide Elements**
   - [ ] Enable "Hide WordPress logo"
   - [ ] Enable "Hide Nexus links"
   - [ ] Enable "Hide update notices for non-admins"
   - [ ] Save → verify WP logo removed from admin bar
   - [ ] Check theme links hidden

6. **Export White Label Package** (Agency Only)
   - [ ] Click "Export White Label Package"
   - [ ] Verify "Agency Only" lock message
   - [ ] Option disabled for Advanced tier

**Expected Results:**
- All branding applies correctly
- Login screen customized
- WordPress/Nexus branding hidden
- Export locked for Advanced (unlocks for Agency)

**Theme Info Verification:**
- [ ] Go to Appearance → Themes
- [ ] Check theme name shows as "MyBrand Theme"
- [ ] Verify author shows as "MyCompany"

---

## Cloud Storage Upgrade Test

**Pro vs Advanced:**
- [ ] Pro user: 5 template limit enforced
- [ ] Advanced user: Unlimited templates in cloud
- [ ] Sync 10+ templates → no errors
- [ ] Verify no "storage limit" warnings

---

## Performance Testing

### AI Features:
- [ ] Template generation: < 5 seconds
- [ ] Docs generation: < 10 seconds
- [ ] No memory leaks during AI operations

### Loop Builder:
- [ ] Complex queries (100+ posts): < 1 second
- [ ] Live preview renders: < 500ms

---

## Browser Compatibility

Test all Advanced features on:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

---

## Error Handling

### 1. AI Credit Exhaustion
- [ ] Use all 100 credits
- [ ] Try generating template → error message
- [ ] Verify upgrade prompt to Agency tier

### 2. Network Failures
- [ ] GitHub import with invalid repo → error
- [ ] Docs generation with bad content → fallback

### 3. White-Label Edge Cases
- [ ] Disable white-label → original branding restored
- [ ] Upload invalid logo format → error message

---

## Tier Restriction Verification

**Locked Features (Should be inaccessible):**
- [ ] Multi-Site Dashboard → 🔒 Agency Only
- [ ] White-Label Export → 🔒 Agency Only
- [ ] 500 AI credits/month → 🔒 Agency Only (Advanced gets 100)

---

## Final Checklist

- [ ] All 6 Advanced features tested
- [ ] All Pro features still working
- [ ] No PHP/JS errors
- [ ] Tier restrictions enforced
- [ ] Performance benchmarks met
- [ ] UI/UX polished

---

## Bug Report Template

```
**Feature:** [Feature Name]
**Tier:** Advanced
**Issue:** [Description]
**Steps to Reproduce:**
1. [Step 1]
2. [Step 2]
**Expected:** [What should happen]
**Actual:** [What actually happens]
**Browser/PHP Version:** [Details]
```

---

## Test Sign-Off

- **Tested By:** _______________
- **Date:** _______________
- **Result:** ⬜ Pass  ⬜ Fail
- **Notes:** _______________
