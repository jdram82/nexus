# Nexus Theme v3.2.0 - Release Notes

**Release Date:** January 8, 2026  
**Version:** 3.2.0  
**Type:** Bug Fix & Enhancement Release

---

## 🐛 Bug Fixes

### Popup Builder View Files

Fixed critical issue where Popup Builder admin pages were generating PHP warnings due to missing view files.

#### Issues Resolved

1. **Missing Templates Page**
   - Created `/pro/popup-builder/views/templates.php`
   - Displays pre-designed popup templates for quick creation
   - Includes 6 template categories: Marketing, Newsletter, Exit Intent, Announcement, Video, and Coupon
   - Full grid layout with preview functionality

2. **Missing Analytics Page**
   - Created `/pro/popup-builder/views/analytics.php`
   - Comprehensive analytics dashboard for popup performance
   - Real-time statistics: Total Impressions, Conversions, Conversion Rate
   - Per-popup performance table with color-coded conversion rates
   - Reset statistics functionality

#### Technical Details

**Fixed Warnings:**
```
Warning: include(/var/www/wptbox/wp-content/themes/nexus/pro/popup-builder/views/analytics.php): 
Failed to open stream: No such file or directory
```

**Files Added:**
- `pro/popup-builder/views/templates.php` - Template selection interface
- `pro/popup-builder/views/analytics.php` - Analytics dashboard

**Features in Templates Page:**
- Responsive grid layout
- Template categories and filtering
- One-click template deployment
- Preview functionality (coming soon)
- Professional UI matching WordPress admin style

**Features in Analytics Page:**
- Summary cards with key metrics
- Sortable performance table
- Color-coded conversion rates (High >5%, Medium 2-5%, Low <2%)
- Individual popup statistics
- Reset stats capability with AJAX
- Empty state with call-to-action

---

## 📊 Popup Builder Templates

### Available Templates

1. **Newsletter Signup** - Simple newsletter subscription form
2. **Exit Discount Offer** - Special discount for users about to leave
3. **Video Popup** - Embed video in a popup
4. **Announcement Bar** - Top/bottom announcement bar
5. **Coupon Code Popup** - Display coupon codes to visitors

### Template Categories

- Marketing
- Newsletter
- Exit Intent
- Announcement
- Video
- Coupon

---

## 🎨 UI/UX Improvements

### Analytics Dashboard

- **Summary Cards**: Visual representation of key metrics with icons
- **Performance Table**: Detailed breakdown of each popup's performance
- **Status Badges**: Quick visual indicators for popup status
- **Action Buttons**: Easy access to edit and reset functions

### Templates Gallery

- **Grid Layout**: Clean, modern card-based layout
- **Template Cards**: Preview images with descriptions
- **Quick Actions**: Use Template and Preview buttons
- **Responsive Design**: Adapts to different screen sizes

---

## 🔧 Technical Improvements

### Code Quality

- Added proper WordPress security checks (ABSPATH)
- Implemented nonce verification for AJAX actions
- Proper internationalization with text domain 'nexus-pro'
- Escaped output for security

### Performance

- Efficient database queries for analytics
- Cached popup statistics
- Optimized table rendering

### Accessibility

- Semantic HTML structure
- Proper ARIA labels
- Keyboard navigation support
- Screen reader friendly

---

## 📦 Installation & Update

### Automatic Update (Recommended)

1. Navigate to **Dashboard** → **Updates**
2. Find **Nexus Theme v3.2.0**
3. Click **Update Now**
4. Wait for the update to complete

### Manual Update

1. Download `nexus-v3.2.0.zip`
2. Go to **Appearance** → **Themes**
3. Click **Add New** → **Upload Theme**
4. Select the downloaded zip file
5. Click **Install Now** → **Replace current with uploaded**

---

## ⚙️ System Requirements

- **WordPress:** 6.0 or higher
- **PHP:** 8.0 or higher
- **MySQL:** 5.7 or higher
- **License Tier:** Advanced or Agency (for Popup Builder)

---

## 🎯 License Tier Compatibility

### Free Tier
- All core theme features
- Basic templates
- Standard support

### Pro Tier ($199/year)
- Advanced controls
- API documentation
- Circuit simulator
- Performance analytics

### Advanced Tier ($299/year)
- **Popup Builder** ✓
- A/B testing
- Cloud storage integration
- Priority support

### Agency Tier ($599/year)
- All Advanced features
- White-label capabilities
- Unlimited sites
- Premium support

---

## 🔄 Upgrade Path

### From v3.1.9 or Earlier

This is a **patch release** that adds missing functionality. All existing features and data are preserved.

**What's Changed:**
- Added missing admin view files
- No database changes required
- No settings migration needed
- Fully backward compatible

---

## 🐞 Known Issues

None reported at this time.

---

## 📝 Changelog Summary

### Added
- Popup Builder Templates page with 5 pre-designed templates
- Popup Builder Analytics page with comprehensive statistics
- Template preview functionality (placeholder)
- Reset statistics AJAX handler

### Fixed
- PHP warnings for missing view files in Popup Builder
- Template and Analytics menu pages now load correctly
- Proper error handling in admin interface

### Changed
- Improved Popup Builder admin UI consistency
- Enhanced analytics visualization
- Better empty states for new installations

---

## 🙏 Acknowledgments

Thank you to all users who reported the missing view files issue. Your feedback helps us maintain a high-quality product.

---

## 📞 Support

- **Documentation:** [https://jdsandigitel.com/nexus/docs](https://jdsandigitel.com/nexus/docs)
- **Support Forum:** [https://jdsandigitel.com/support](https://jdsandigitel.com/support)
- **Email:** support@jdsandigitel.com
- **License Server:** [https://jdsandigitel.com/license-server](https://jdsandigitel.com/license-server)

---

## 🔐 License Information

This theme is licensed under the GNU General Public License v2 or later.

**Premium Features Licensing:**
- Advanced features require active license key
- Automatic updates require license validation
- Annual renewal for continued access
- Multi-site licensing available (Agency tier)

---

**Full Changelog:** [View on GitHub](https://github.com/jdram82/nexus/releases/tag/v3.2.0)

---

*Nexus Theme - Professional WordPress Development Platform*  
*© 2026 Jdsan Digitel. All rights reserved.*
