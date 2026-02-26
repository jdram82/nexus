# Nexus Agency Tier Testing Guide

**Tier:** Agency  
**Price:** $599/year  
**Target User:** Digital agencies managing multiple client sites  
**Features:** All Pro + Advanced features + 2 Agency-exclusive features

---

## Pre-Test Setup

### 1. License Activation
- [ ] Navigate to **Nexus Options → License**
- [ ] Enter Agency license key
- [ ] Verify activation shows "Agency Tier" badge (blue gradient)
- [ ] Confirm all features unlocked

### 2. Verify Tier Access
- [ ] `Nexus_License_Manager::get_tier()` returns `'agency'`
- [ ] All Pro features accessible
- [ ] All Advanced features accessible
- [ ] Agency menu items visible (no locks)

---

## Inherited Features (Quick Verification)

### Pro Features:
- [ ] Plugin Harmony working
- [ ] REST API functional
- [ ] Template Library operational

### Advanced Features:
- [ ] Plugin Orchestrator working
- [ ] Loop Builder functional
- [ ] AI Template Generator: **500 credits/month** (vs 100 for Advanced)
- [ ] AI Docs Generator operational
- [ ] Marketplace accessible
- [ ] White-Label: **Export package unlocked**

---

## Agency-Exclusive Feature Testing

### Feature 1: Multi-Site Dashboard (Agency Only)

**Location:** Top-level menu → Agency Dashboard  
**Test File:** `pro/agency/class-agency-dashboard.php`

#### Test Cases:

**Part 1: Dashboard Overview**

1. **Access Dashboard**
   - [ ] Click "Agency Dashboard" in WordPress admin (dashicons-networking icon)
   - [ ] Verify dashboard loads (should be top-level menu item)
   - [ ] Check 4 stat cards display:
     - Active Sites
     - Updates Available
     - Healthy Sites
     - Issues

2. **Initial State (No Sites)**
   - [ ] Verify "No Sites Added Yet" message
   - [ ] Check placeholder graphic displays
   - [ ] Confirm "Add New Site" button visible

**Part 2: Add Client Sites**

3. **Add First Site**
   - [ ] Click "Add New Site" button
   - [ ] Modal appears with form
   - [ ] Fill in:
     - Site Name: "Client Website 1"
     - Site URL: "https://client1.com"
     - API Key: [Generate from target site first]
     - Client Name: "ACME Corp"
     - Tags: "e-commerce, live, priority"
     - Auto-Monitor: Checked
   - [ ] Click "Add Site"
   - [ ] Verify connection test runs
   - [ ] Site card appears in grid

4. **API Key Generation** (On Target Site)
   - [ ] On client site: Nexus Options → REST API
   - [ ] Click "Generate API Key"
   - [ ] Copy key
   - [ ] Use in Agency Dashboard "Add Site" form
   - [ ] Verify authentication succeeds

5. **Add Multiple Sites**
   - [ ] Add 5 different client sites
   - [ ] Use varied tags (staging, production, e-commerce, blog)
   - [ ] Verify all appear in grid
   - [ ] Check stats update correctly

**Part 3: Site Health Monitoring**

6. **Health Status Indicators**
   - [ ] Check site cards show status icons:
     - ✓ Green = Healthy
     - ⚠️ Yellow = Warning
     - ✗ Red = Error
   - [ ] Verify color-coded status accurate

7. **Site Stats**
   - [ ] Each card shows:
     - Uptime percentage
     - Load time (e.g., 0.5s)
     - Nexus version
   - [ ] Verify stats accurate (compare with actual site)

8. **Refresh Single Site**
   - [ ] Click "Refresh" on any site card
   - [ ] Loading spinner appears
   - [ ] Stats update (< 2 seconds)
   - [ ] "Last checked" timestamp updates

9. **Refresh All Sites**
   - [ ] Click "Refresh All" button
   - [ ] All sites refresh in parallel
   - [ ] Stats update for all cards
   - [ ] No timeout errors

**Part 4: Filtering & Search**

10. **Status Filter**
    - [ ] Filter by: Healthy
    - [ ] Only healthy sites show
    - [ ] Filter by: Warnings
    - [ ] Only warning sites show
    - [ ] Filter by: Errors
    - [ ] Only error sites show

11. **Update Filter**
    - [ ] Filter by: Updates Available
    - [ ] Only sites with pending updates show
    - [ ] Badge shows update count

12. **Search Functionality**
    - [ ] Type "ACME" in search
    - [ ] Only ACME Corp sites show
    - [ ] Type "e-commerce" → sites with that tag show
    - [ ] Clear search → all sites return

**Part 5: Bulk Operations**

13. **Bulk Updates** (Mock in v1.5.0)
    - [ ] Click "Bulk Update Sites"
    - [ ] Modal shows sites with updates
    - [ ] Select 3 sites
    - [ ] Click "Update Selected"
    - [ ] Progress indicator shows
    - [ ] Verify success messages

14. **Export Report**
    - [ ] Click "Export Report"
    - [ ] CSV file downloads
    - [ ] Open CSV → verify columns:
      - Site Name, URL, Status, Uptime, Version, Last Checked
    - [ ] Data accurate

**Part 6: Auto-Monitoring (Cron)**

15. **Hourly Monitoring**
    - [ ] Verify cron scheduled: `wp cron event list | grep nexus_monitor_sites`
    - [ ] Simulate cron run: `wp cron event run nexus_monitor_sites`
    - [ ] Check all sites refreshed
    - [ ] "Last checked" timestamps updated

16. **Disable Auto-Monitor**
    - [ ] Edit site → uncheck "Auto-Monitor"
    - [ ] Wait for cron run
    - [ ] Verify that site NOT refreshed
    - [ ] Other sites still monitored

**Part 7: Site Details View**

17. **View Details**
    - [ ] Click "Details" on any site
    - [ ] Modal shows detailed stats:
      - Full health report
      - Plugin list
      - Theme info
      - Performance metrics
    - [ ] Verify data accurate

**Part 8: Remove Sites**

18. **Remove Site**
    - [ ] Click "Remove" on test site
    - [ ] Confirmation dialog appears
    - [ ] Confirm → site removed from grid
    - [ ] Stats recalculate
    - [ ] Database cleaned (no orphan data)

**Expected Results:**
- Dashboard loads without errors
- All CRUD operations work (Create, Read, Update, Delete sites)
- API connections stable
- Cron monitoring functions correctly
- No performance degradation with 10+ sites

**Performance Benchmarks:**
- [ ] Dashboard loads: < 1.5 seconds
- [ ] Site refresh (single): < 2 seconds
- [ ] Bulk refresh (10 sites): < 10 seconds
- [ ] Search/filter: < 100ms response

**Error Handling:**
- [ ] Invalid API key → error message
- [ ] Unreachable site → timeout handled gracefully
- [ ] Network failure → retry mechanism
- [ ] Duplicate site → warning message

---

### Feature 2: White-Label Export (Agency Only)

**Location:** Nexus Options → White Label → Export Package  
**Test File:** `pro/agency/class-white-label.php` (export section)

#### Test Cases:

1. **Export Package Button**
   - [ ] Navigate to White Label page
   - [ ] Scroll to "Export White Label Package"
   - [ ] Verify button enabled (no lock for Agency)
   - [ ] Advanced users see 🔒 lock

2. **Export Options**
   - [ ] Check "Include Pro features" → ON
   - [ ] Check "Remove license validation" → ON (Agency only)
   - [ ] Verify warning: "Exported theme will not require license"

3. **Generate Export**
   - [ ] Click "Export White Label Package"
   - [ ] Progress bar shows
   - [ ] ZIP file downloads (theme-mybrand.zip)
   - [ ] File size reasonable (< 10MB)

4. **Verify Exported Theme**
   - [ ] Extract ZIP file
   - [ ] Check `style.css`:
     - Theme name = custom name ("MyBrand Theme")
     - Author = custom author
     - Description = custom description
   - [ ] Check functions.php:
     - License checks removed (if option enabled)
     - Nexus branding replaced
   - [ ] Check pro/ folder:
     - Pro features included
     - License manager code adjusted

5. **Install Exported Theme**
   - [ ] Upload to test WordPress site
   - [ ] Activate theme
   - [ ] Verify:
     - Theme name shows as custom
     - No license prompts (if validation removed)
     - Admin branding applied
     - All Pro features work

6. **Client Delivery**
   - [ ] Share exported ZIP with client
   - [ ] Client installs on their site
   - [ ] Verify theme works independently
   - [ ] No Nexus branding visible

**Expected Results:**
- Export generates valid WordPress theme
- Custom branding throughout
- License validation optional
- Client can use theme without agency license

---

## Agency-Specific Benefits

### 1. AI Credit Boost
- [ ] Advanced tier: 100 credits/month
- [ ] Agency tier: **500 credits/month**
- [ ] Generate 150 templates in one month
- [ ] Verify no "out of credits" error until 501st

### 2. Unlimited Client Sites
- [ ] Add 20+ sites to dashboard
- [ ] Verify no site limits
- [ ] No performance degradation

### 3. Priority Support (Simulated)
- [ ] Check support ticket system
- [ ] Agency tier gets "Priority" badge
- [ ] Response time SLA documented

---

## Workflow Testing: Full Agency Scenario

**Scenario:** Agency onboards 3 new clients

1. **Client 1: E-Commerce Store**
   - [ ] Add site to dashboard
   - [ ] Monitor health
   - [ ] Generate product page template with AI (use 5 credits)
   - [ ] Apply white-label branding
   - [ ] Export custom theme
   - [ ] Deliver to client

2. **Client 2: Documentation Site**
   - [ ] Add site to dashboard
   - [ ] Import README from GitHub
   - [ ] Generate docs site with AI Docs Generator (use 5 credits)
   - [ ] White-label theme
   - [ ] Export and deliver

3. **Client 3: Startup Landing Page**
   - [ ] Add site to dashboard
   - [ ] Use AI Template Generator: "SaaS landing page" (use 5 credits)
   - [ ] Customize with Loop Builder
   - [ ] White-label + export
   - [ ] Deploy to client site

**Verify:**
- [ ] All 3 clients added to dashboard
- [ ] 15 AI credits used (485 remaining)
- [ ] Each client has unique white-label theme
- [ ] Agency dashboard shows 3 healthy sites

---

## Performance Testing (Agency Scale)

### Stress Tests:

1. **50+ Sites**
   - [ ] Add 50 mock sites to dashboard
   - [ ] Refresh all → completes within 60 seconds
   - [ ] Filter/search still responsive

2. **High AI Usage**
   - [ ] Generate 100 templates in 1 day
   - [ ] No performance degradation
   - [ ] Credit counter accurate

3. **Concurrent Operations**
   - [ ] Refresh 10 sites simultaneously
   - [ ] No race conditions
   - [ ] All stats update correctly

---

## Browser Compatibility

Test Agency Dashboard on:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

---

## Error Handling

### 1. Network Failures
- [ ] Client site down → dashboard shows error status
- [ ] Auto-retry mechanism kicks in
- [ ] Alert notification sent

### 2. API Key Expiration
- [ ] Revoke API key on client site
- [ ] Dashboard shows authentication error
- [ ] Option to update key

### 3. White-Label Export Failures
- [ ] Disk space full → error message
- [ ] Invalid branding settings → validation errors

---

## Security Testing

### 1. API Key Protection
- [ ] API keys stored encrypted in database
- [ ] Keys not visible in page source
- [ ] Transmission over HTTPS only

### 2. Multi-Site Access Control
- [ ] Non-admin users can't access dashboard
- [ ] Role-based permissions enforced

### 3. White-Label Export
- [ ] License removal only for Agency tier
- [ ] Pro/Advanced users can't bypass license

---

## Final Checklist

- [ ] Multi-Site Dashboard fully tested (20+ test cases)
- [ ] White-Label Export works correctly
- [ ] All Pro + Advanced features working
- [ ] 500 AI credits/month verified
- [ ] Unlimited sites supported
- [ ] No PHP/JS errors
- [ ] Performance benchmarks met
- [ ] Security measures validated

---

## Tier Comparison Verification

| Feature | Pro | Advanced | Agency |
|---------|-----|----------|--------|
| Plugin Harmony | ✓ | ✓ | ✓ |
| REST API | ✓ | ✓ | ✓ |
| Template Library | ✓ (5 cloud) | ✓ (unlimited) | ✓ (unlimited) |
| Plugin Orchestrator | ✗ | ✓ | ✓ |
| Loop Builder | ✗ | ✓ | ✓ |
| AI Template Generator | ✗ | ✓ (100 credits) | ✓ (500 credits) |
| AI Docs Generator | ✗ | ✓ | ✓ |
| Marketplace | ✗ | ✓ | ✓ |
| White-Label | ✗ | ✓ (no export) | ✓ (full export) |
| Multi-Site Dashboard | ✗ | ✗ | ✓ |

**Verify all ✓/✗ accurate in testing**

---

## Bug Report Template

```
**Feature:** [Feature Name]
**Tier:** Agency
**Issue:** [Description]
**Steps to Reproduce:**
1. [Step 1]
2. [Step 2]
**Expected:** [What should happen]
**Actual:** [What actually happens]
**Browser/PHP Version:** [Details]
**Sites Affected:** [Number/names]
```

---

## Test Sign-Off

- **Tested By:** _______________
- **Date:** _______________
- **Result:** ⬜ Pass  ⬜ Fail
- **Sites Tested:** _______________
- **Notes:** _______________

---

## Agency Dashboard Screenshot Checklist

For documentation, capture:
- [ ] Dashboard overview with 5+ sites
- [ ] Add Site modal
- [ ] Healthy site card
- [ ] Warning site card
- [ ] Error site card
- [ ] Bulk update interface
- [ ] Export report preview
- [ ] White-label export success
