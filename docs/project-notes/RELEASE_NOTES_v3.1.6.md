# Nexus Theme v3.1.6 - Release Notes

**Release Date:** January 3, 2026  
**Version:** 3.1.6  
**Status:** Stable Release - Package Fixed

---

## 🔧 CRITICAL FIX (January 3, 2026)

### ✅ Package Installation Error Fixed
**Problem:** WordPress installation failed with error:  
`"The package could not be installed. The theme is missing the style.css stylesheet."`

**Root Cause:** The zip package contained a folder named `nexus-theme/` instead of `nexus/`  
WordPress requires the folder name to match the theme slug (Text Domain: nexus)

**Solution:** 
- Updated `package-theme.sh` script
- Changed `THEME_NAME` from `"nexus-theme"` to `"nexus"`
- Rebuilt package with correct folder structure
- Verified style.css is at `nexus/style.css`

**Status:** ✅ FIXED - New package uploaded to GitHub releases

---

## 🚀 Phase 4 Implementation Progress (48% Complete)

### ✅ Popup Builder - 100% Complete (3,000 lines)

**Core Files:**
1. `class-popup-builder.php` (680 lines) - Post type, admin menus, rendering
2. `class-popup-triggers.php` (420 lines) - 6 trigger types
3. `class-popup-targeting.php` (450 lines) - Targeting rules
4. `class-popup-editor.php` (450 lines) - Visual editor, meta boxes

**Assets:**
- Frontend: `popup-builder.css` (300 lines), `popup-builder.js` (350 lines)
- Admin: `popup-builder-admin.css` (200 lines), `popup-builder-admin.js` (150 lines)

**Features:**
- 6 Trigger Types: Page Load, Scroll, Exit Intent, Click, Time Delay, Inactivity
- Device Targeting: Desktop, Tablet, Mobile
- Page Targeting: Include/exclude rules
- User Targeting: Login status, user roles
- Frequency Controls: Show once, delay between displays
- 4 Animation Types: Fade, Slide Up, Slide Down, Zoom
- 5 Position Options: Center, Top, Bottom, Left, Right
- Responsive design with accessibility features

### ✅ Priority Widgets - 5/20 Complete (1,100 lines)

**Completed Widgets:**
1. **Star Rating Widget** (200 lines)
   - Customizable ratings (0-10)
   - Half-star support
   - Color customization
   - Size and alignment controls

2. **Gallery Widget** (230 lines)
   - Grid/Masonry/Justified layouts
   - Lightbox integration
   - 1-6 columns
   - Hover effects (zoom, fade, overlay)
   - Caption support

3. **Icon List Widget** (220 lines)
   - Dashicons integration
   - Repeater fields
   - Vertical/horizontal layouts
   - Per-item links
   - Custom styling

4. **Toggle Widget** (200 lines)
   - Collapsible content sections
   - Slide animations
   - Open by default option
   - Custom icons
   - Border and color controls

5. **Social Icons Widget** (250 lines)
   - 12 Social Networks: Facebook, Twitter, Instagram, LinkedIn, YouTube, Pinterest, GitHub, TikTok, WhatsApp, Telegram, Reddit, Discord
   - 3 Shape Options: None, Square, Circle
   - Horizontal/Vertical layouts
   - Custom colors and hover effects

**Files:** Located in `pro/theme-builder/widgets/`

---

## 📊 Overall Progress

- **Total Phase 4:** 8,500 lines estimated
- **Completed:** 4,100 lines (48%)
- **Popup Builder:** 3,000 lines (100% ✅)
- **Widgets:** 1,100/5,500 lines (25%)
- **Next:** 15 remaining priority widgets

---

## 🎉 Previous Features (v3.1.5)

### ✅ Native WordPress Menu Support
- Primary navigation menu in header
- Footer navigation menu
- Responsive mobile menu toggle

### 🎨 Starter Templates System
- 9 Beautiful template previews
- Visual browsing interface
- Block patterns integration
