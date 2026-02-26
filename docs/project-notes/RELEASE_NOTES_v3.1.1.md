# Nexus Theme v3.1.1 - Release Notes

**Release Date:** January 1, 2026

## 🎯 Template Library Fixes

This maintenance release fixes critical issues with the Template Library system.

### ✅ Fixed Issues

1. **Template Loading**
   - Templates now load from actual JSON data files (50+ templates available)
   - Removed placeholder/mock data
   - All templates display with proper metadata

2. **Import Functionality**
   - Template import now works correctly
   - Creates draft WordPress pages from template data
   - Redirects to page editor after successful import
   - Proper error handling and user feedback

3. **Visual Improvements**
   - Added gradient placeholder backgrounds for templates
   - Template titles display in placeholders when images unavailable
   - Better loading states and error messages
   - Improved template card styling

### 📦 Files Changed

- `pro/templates/class-template-library.php` - Backend template loading and import logic
- `pro/assets/js/templates.js` - Frontend template display and import handling
- `pro/assets/css/templates.css` - Template styling and placeholders

### 🔧 Technical Details

- Templates now loaded from `/pro/templates/data/*.json`
- Import creates WordPress pages with Block Editor content
- Template metadata stored in post meta for reference
- Improved AJAX error handling

### 📋 Upgrade Instructions

1. Upload updated theme files via FTP/FileZilla:
   - `/wp-content/themes/nexus-theme/pro/templates/class-template-library.php`
   - `/wp-content/themes/nexus-theme/pro/assets/js/templates.js`
   - `/wp-content/themes/nexus-theme/pro/assets/css/templates.css`
   
   OR

2. Via Git:
   ```bash
   git pull origin main
   ```

3. Clear WordPress cache and browser cache

4. Visit Dashboard → Nexus → Templates to see the fixes

### 🎉 What's Working Now

- ✅ All 50+ templates display properly
- ✅ Template import creates WordPress pages
- ✅ Templates show with gradient placeholders
- ✅ Import redirects to page editor
- ✅ Better error messages

### 🔮 Coming in v3.2.0

- Template preview functionality
- Template categories and filtering
- Custom template creation interface
- Template marketplace integration (Advanced/Agency tiers)

---

**Full Changelog:** [CHANGELOG.md](CHANGELOG.md)
