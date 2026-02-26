# USA References & Contact Info Cleanup

**Date**: February 24, 2026  
**Goal**: Remove USA-specific references and standardize on support@jdsancontrols.com

---

## ✅ Already Correct

### Email System
**File**: `ul-nec-compliance/includes/class-ulnec-emails.php`
- ✅ Line 28: Uses `support@jdsancontrols.com`
- ✅ Line 222: Support link correct
- ✅ Line 266: Support email correct

**No changes needed** - Email system is already configured correctly!

---

## ⚠️ Needs Update

### 1. Phone Number Placeholder

**File**: `ul-nec-compliance/includes/class-ulnec-frontend-pages.php`  
**Line**: 1260

**Current:**
```php
<input type="tel" name="user_phone" value="<?php echo esc_attr($user_data['phone'] ?? ''); ?>" placeholder="+1 (234) 567-8900">
```

**Change To:**
```php
<input type="tel" name="user_phone" value="<?php echo esc_attr($user_data['phone'] ?? ''); ?>" placeholder="+XX XXX-XXX-XXXX">
```

**Reason**: Remove USA country code (+1)

---

### 2. Support Shortcode Content

**File**: `ul-nec-compliance/includes/class-ulnec-frontend-pages.php`  
**Lines**: ~250-400 (support_shortcode method)

**Need to verify and update:**
- Remove any USA phone numbers
- Remove physical addresses
- Keep only: support@jdsancontrols.com
- Response time: 24-48 hours
- Availability: 24/7 online support

---

### 3. Landing Page

**File**: `page-ulnec-landing.php`  
**Line**: 863

**Current** (about privacy/security):
```
"We never upload your drawings. All validation happens locally on your machine."
```

**This is fine** - No USA reference, just mentions "machine" which is generic.

**Check for other sections:**
- Contact forms
- FAQ answers
- Footer content
- Testimonials (if any mention USA-specific locations)

---

## 🔍 Search Results Summary

**Files Checked:**
1. ✅ `class-ulnec-emails.php` - All correct
2. ⚠️ `class-ulnec-frontend-pages.php` - Phone placeholder needs update
3. ✅ `page-ulnec-landing.php` - No USA references found

**Other mentions found (NOT USA-related):**
- "Email Address" (field label) - ✅ OK
- "IP Address" (logging) - ✅ OK
- "phone" (field name) - ✅ OK

---

## 📝 Action Items

### Priority 1: Update Phone Placeholder
```bash
# Fix USA phone number format
1. Open: ul-nec-compliance/includes/class-ulnec-frontend-pages.php
2. Find line 1260
3. Change placeholder from "+1 (234) 567-8900" to "+XX XXX-XXX-XXXX"
4. Save
```

### Priority 2: Review Support Page
```bash
# Verify support shortcode content
1. Open: ul-nec-compliance/includes/class-ulnec-frontend-pages.php
2. Find: support_shortcode() method
3. Verify contact info shows only:
   - Email: support@jdsancontrols.com
   - Response time: 24-48 hours
   - No phone numbers or addresses
```

### Priority 3: Scan Documentation
```bash
# Check MD files for USA references
grep -r "USA\|United States\|U\.S\." *.md
```

---

## ✨ Recommendations

### Keep Generic & International

**Good Examples:**
- ✅ "Contact us at support@jdsancontrols.com"
- ✅ "24/7 online support available"
- ✅ "Response time: 24-48 hours"
- ✅ "Global coverage"

**Avoid:**
- ❌ Phone numbers with country codes
- ❌ Physical addresses
- ❌ "USA-based", "American company"
- ❌ State-specific references (CA, NY, etc.)

### Time Zones
If mentioning hours:
- ✅ "Support available 24/7"
- ❌ "9am-5pm EST"

### Currency
Current landing page uses USD ($):
- ✅ OK - Stripe/PayPal handle international payments
- ✅ Buyers see their local currency automatically

---

## 🎯 Implementation

**Quick Fix (2 minutes):**

```php
// File: class-ulnec-frontend-pages.php
// Line: 1260

// BEFORE:
placeholder="+1 (234) 567-8900"

// AFTER:
placeholder="Your phone number"
```

Or for international format hint:
```php
placeholder="+[Country Code] [Number]"
```

---

## ✅ Final Checklist

- [ ] Update phone placeholder in class-ulnec-frontend-pages.php
- [ ] Review support_shortcode() content
- [ ] Verify no USA references in landing page FAQ
- [ ] Check footer content
- [ ] Test all forms with international phone format
- [ ] Update any remaining documentation

---

**Status**: Minor updates needed (5 minutes)  
**Impact**: Low - Email system already correct, just placeholder text to update
