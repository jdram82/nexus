# Nexus Pro Tier Testing Guide

**Tier:** Pro  
**Price:** $199/year  
**Target User:** Individual consumers and small businesses  
**Features:** 10 Pro features unlocked

---

## Pre-Test Setup

### 1. License Activation
- [ ] Navigate to **Nexus Options → License**
- [ ] Enter Pro license key
- [ ] Verify activation message appears
- [ ] Confirm tier badge shows "Pro Tier" in purple gradient

### 2. Verify Tier Access
- [ ] Check `Nexus_License_Manager::get_tier()` returns `'pro'`
- [ ] Verify Advanced/Agency features show lock icons
- [ ] Confirm Pro menu items are visible

---

## Feature Testing

### Feature 1: Plugin Harmony (Pro)

**Location:** Automatic (background system)  
**Test File:** `inc/class-nexus-plugin-harmony.php`

#### Test Cases:

1. **Auto-Detection**
   - [ ] Install Yoast SEO
   - [ ] Check Nexus auto-detects it without errors
   - [ ] Verify dashboard shows "Yoast SEO detected"

2. **Conflict Resolution**
   - [ ] Install conflicting plugin (e.g., another theme options plugin)
   - [ ] Verify Nexus gracefully disables overlapping features
   - [ ] Check no fatal errors occur

3. **50+ Plugin Support**
   - [ ] Test with: Elementor, WPForms, Contact Form 7, Gravity Forms
   - [ ] Verify each shows in harmony dashboard
   - [ ] Confirm styling applied automatically

**Expected Results:**
- Dashboard shows all detected plugins
- No conflicts or fatal errors
- Nexus features disabled when conflicts exist

---

### Feature 2: REST API (Pro)

**Location:** Nexus Options → REST API  
**Test File:** `inc/api/class-nexus-rest-api.php`

#### Test Cases:

1. **API Key Generation**
   - [ ] Navigate to REST API page
   - [ ] Click "Generate API Key"
   - [ ] Verify key appears (32-char string)
   - [ ] Copy key to clipboard

2. **Health Endpoint**
   - [ ] Test: `GET /wp-json/nexus/v1/health`
   - [ ] Expected: `{"status":"healthy","version":"1.5.0"}`
   - [ ] Verify response time < 200ms

3. **Theme Settings Endpoint**
   - [ ] Test: `GET /wp-json/nexus/v1/settings`
   - [ ] Expected: JSON with theme customizer settings
   - [ ] Verify all settings included

4. **Authentication**
   - [ ] Test endpoint without API key → 401 error
   - [ ] Test with invalid key → 403 error
   - [ ] Test with valid key → 200 success

**Expected Results:**
- API key generates successfully
- 15+ endpoints respond correctly
- Authentication works as expected

**Test Script:**
```bash
# Replace YOUR_API_KEY and example.com
curl -H "X-Nexus-API-Key: YOUR_API_KEY" https://example.com/wp-json/nexus/v1/health
```

---

### Feature 3: Template Library (Pro)

**Location:** Nexus Options → Templates (Pro)  
**Test File:** `pro/templates/class-template-library.php`

#### Test Cases:

1. **Browse Templates**
   - [ ] Navigate to Templates page
   - [ ] Verify 3 sample templates appear
   - [ ] Test category filter (Landing Pages, Portfolio, Docs)
   - [ ] Test type filter (Free, Premium)

2. **Import Template**
   - [ ] Click "Import Template" on any template
   - [ ] Wait for import to complete
   - [ ] Verify template appears in "My Templates" tab
   - [ ] Check template data saved correctly

3. **Export Template**
   - [ ] Go to "My Templates" tab
   - [ ] Click "Export" on any template
   - [ ] Verify JSON file downloads
   - [ ] Open JSON → confirm valid structure

4. **Cloud Sync (Pro Limit: 5 templates)**
   - [ ] Go to "Cloud Sync" tab
   - [ ] Check available slots (should show 5)
   - [ ] Enable sync for 1 template
   - [ ] Verify counter updates (4 remaining)
   - [ ] Try syncing 6th template → error message
   - [ ] Confirm upgrade prompt to Advanced tier

5. **Marketplace Tab (Locked)**
   - [ ] Click "Marketplace" tab
   - [ ] Verify lock message appears
   - [ ] Confirm "Upgrade to Advanced" button visible

**Expected Results:**
- Template browser loads within 2 seconds
- Import/export works without errors
- Cloud sync enforces 5-template limit
- Marketplace tab locked for Pro users

**CSS/JS Testing:**
- [ ] Grid layout responsive on mobile
- [ ] Template cards hover effects work
- [ ] Tab switching smooth (no flicker)
- [ ] AJAX operations show loading spinner

---

## Performance Testing

### 1. Page Load Times
- [ ] Template Library page loads < 1.5s
- [ ] REST API responses < 200ms average
- [ ] Plugin Harmony detection < 500ms

### 2. Database Queries
- [ ] Template Library: < 10 queries
- [ ] REST API health check: < 3 queries
- [ ] Plugin Harmony: < 5 queries on admin pages

### 3. Memory Usage
- [ ] All Pro features combined: < 10MB memory overhead
- [ ] No memory leaks after extended use

---

## Browser Compatibility

Test Template Library admin interface on:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

---

## Error Handling

### 1. Network Failures
- [ ] Disable internet → cloud sync shows error
- [ ] Re-enable internet → sync works again

### 2. Invalid Input
- [ ] Try uploading invalid template JSON → error message
- [ ] Test malformed API requests → proper error codes

### 3. Permission Issues
- [ ] Test as non-admin user → features hidden
- [ ] Test as admin → all features accessible

---

## Upgrade Path Testing

### Test Tier Upgrades:
1. **Pro → Advanced**
   - [ ] Deactivate Pro license
   - [ ] Activate Advanced license
   - [ ] Verify cloud sync limit increases (5 → unlimited)
   - [ ] Marketplace tab unlocks
   - [ ] Advanced features appear

2. **License Expiration**
   - [ ] Simulate expired license (change DB option)
   - [ ] Verify Pro features locked
   - [ ] Confirm renewal prompt appears

---

## Final Checklist

- [ ] All 3 Pro features tested and working
- [ ] No PHP errors in debug.log
- [ ] No JavaScript console errors
- [ ] Tier restrictions enforced correctly
- [ ] Performance benchmarks met
- [ ] UI/UX polished and responsive

---

## Bug Report Template

If issues found:

```
**Feature:** [Feature Name]
**Tier:** Pro
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
