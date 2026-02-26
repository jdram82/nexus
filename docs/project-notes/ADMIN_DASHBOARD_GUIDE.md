# Nexus Admin Dashboard Guide

## Overview

Version 3.1.0 introduces a comprehensive admin dashboard for the Nexus theme, inspired by premium themes like Astra and Hello Elementor. The dashboard provides a centralized hub for managing templates, settings, license, and onboarding.

## Access the Dashboard

Navigate to **WordPress Admin > Nexus** in the left sidebar.

## Dashboard Pages

### 1. Dashboard (Main)

**Location:** Nexus > Dashboard

**Features:**
- **Stats Overview:** Quick view of license tier, templates count, available features, and update status
- **Getting Started Checklist:** 4-step onboarding guide with completion tracking
- **Features Grid:** Visual display of all available features with premium/locked indicators
- **Upgrade Card:** Call-to-action for free tier users to upgrade
- **Quick Links:** Fast access to documentation, support, and community
- **System Status Widget:** WordPress and server environment health check

**License-Based Display:**
- **Free Tier:** Shows locked features with upgrade prompts
- **Advanced/Pro/Agency:** All features unlocked with no upgrade cards

---

### 2. Templates

**Location:** Nexus > Templates

**Features:**
- **Template Library:** Pre-designed templates for quick site setup
- **Category Filters:** All, Business, E-Commerce, Portfolio, Blog, Premium
- **Template Preview:** Visual cards showing template name, category, and features
- **Import System:** One-click template import with progress indicator
- **Premium Badges:** Visual indicators for pro-only templates

**Available Templates:**
1. **Business Hub** (Business, Free)
2. **Startup Pro** (Business, Premium)
3. **Fashion Store** (E-Commerce, Free)
4. **Tech Shop** (E-Commerce, Premium)
5. **Creative Portfolio** (Portfolio, Free)
6. **Photography Pro** (Portfolio, Premium)
7. **Modern Blog** (Blog, Free)
8. **Magazine Pro** (Blog, Premium)

**Import Process:**
1. Browse templates by category
2. Click "Import Template"
3. View import progress modal
4. Template applied to site (homepage, settings, widgets)

**Free Tier Limitations:**
- Can only import free templates
- Premium templates show locked state with upgrade prompt

---

### 3. Settings

**Location:** Nexus > Settings

**Features:**

#### Performance Settings (All Tiers)
- **Minify CSS:** Reduce CSS file sizes for faster loading
- **Minify JavaScript:** Reduce JS file sizes for faster loading
- **Lazy Load Images:** Defer image loading for improved performance

#### Header Settings (All Tiers)
- **Header Style:** Default, Transparent, Sticky, Custom
- **Sticky Header:** Enable/disable sticky header on scroll

#### Footer Settings (All Tiers)
- **Footer Columns:** 1, 2, 3, or 4 column layout
- **Copyright Text:** Customize footer copyright message

#### Advanced Settings (Premium Only)
- **Custom CSS:** Add custom CSS code
- **Custom JavaScript:** Add custom JS code (header or footer)
- **Google Analytics:** Add GA tracking ID
- **🔒 Locked for Free Tier** - Upgrade to access

**Actions:**
- **Save Settings:** Persist changes to database
- **Reset to Defaults:** Restore default theme settings

**Free Tier Limitations:**
- Advanced settings section is locked
- Shows upgrade prompt for custom CSS/JS and analytics

---

### 4. License

**Location:** Nexus > License

**Features:**
- **Redirects to existing license management page**
- Managed by `Nexus_License_Manager` class
- Shows license activation form, status, and tier information

**What to Expect:**
- License key input field
- Activate/Deactivate buttons
- License status: Active/Inactive
- Tier display: Free/Pro/Advanced/Agency
- Expiration date
- Remaining activations

---

### 5. Getting Started

**Location:** Nexus > Getting Started

**Features:**

#### Video Tutorial
- Embedded video guide for Nexus setup (placeholder for actual video)

#### Setup Steps
1. **Activate Your License** - Enter license key to unlock premium features
2. **Import a Template** - Choose from pre-designed templates
3. **Customize Your Site** - Use WordPress Customizer or Theme Builder
4. **Go Live** - Launch your website

#### Resources
- **📚 Documentation:** Complete theme documentation
- **💬 Support Forum:** Community help and discussions
- **🎓 Video Tutorials:** Step-by-step guides
- **💡 Feature Requests:** Suggest new features

#### FAQ
1. **How do I activate my license?**
   - Go to Nexus > License, enter your key, click Activate

2. **Can I use templates on multiple sites?**
   - Depends on license tier (Free: 1, Pro: 3, Advanced: 5, Agency: Unlimited)

3. **How do I update the theme?**
   - Updates are automatic via WordPress admin

4. **What if I need help?**
   - Visit support forum or contact support team

5. **Can I customize the templates?**
   - Yes, all templates are fully customizable

---

### 6. System Info

**Location:** Nexus > System Info

**Features:**

#### WordPress Environment
- WordPress Version
- Site URL
- Home URL
- Language
- Timezone
- User Role

#### Server Environment
- PHP Version
- MySQL Version
- Server Software
- PHP Memory Limit
- PHP Max Upload Size
- PHP Max Execution Time
- WP Memory Limit
- WP Debug Mode

#### Theme Information
- Theme Name
- Theme Version
- Theme Directory
- Parent Theme (if child theme)
- Active Child Theme

#### Active Plugins
- List of all active plugins with version numbers

#### Export System Info
- **Copy to Clipboard:** One-click copy for support tickets
- Useful for debugging and support requests

---

## Technical Details

### File Structure

```
/inc/admin/
├── class-nexus-admin.php          # Main admin controller
└── views/
    ├── dashboard.php              # Main dashboard page
    ├── templates.php              # Template library
    ├── settings.php               # Theme settings
    ├── getting-started.php        # Onboarding guide
    └── system-info.php            # System diagnostics

/assets/admin/
├── css/
│   └── admin.css                  # Admin dashboard styles (800+ lines)
└── js/
    └── admin.js                   # Admin dashboard scripts
```

### Main Class: `Nexus_Admin`

**Location:** `/inc/admin/class-nexus-admin.php`

**Key Methods:**
- `instance()` - Singleton pattern
- `add_admin_menu()` - Register admin menu and pages
- `enqueue_admin_assets()` - Load CSS/JS for admin pages
- `handle_actions()` - Process form submissions
- `get_license_info()` - Fetch license data from License Manager
- `render_dashboard()` - Render main dashboard page
- `render_templates()` - Render template library
- `render_settings()` - Render settings page
- `render_getting_started()` - Render onboarding page
- `render_system_info()` - Render system information

**Integration:**
- Loaded in `functions.php` via `require_once NEXUS_DIR . '/inc/admin/class-nexus-admin.php'`
- Automatically initializes on admin_menu hook
- Integrates with existing `Nexus_License_Manager` class

---

## License Tier Features

### Free Tier
- ✅ Dashboard access
- ✅ Free templates (4 templates)
- ✅ Basic settings (performance, header, footer)
- ✅ Getting Started guide
- ✅ System Info
- ❌ Premium templates (4 templates locked)
- ❌ Advanced settings (custom CSS/JS, analytics)

### Pro Tier ($99/year)
- ✅ All Free features
- ✅ Premium templates (all 8 templates)
- ✅ Advanced settings
- ✅ Custom CSS/JS
- ✅ Google Analytics integration
- ✅ Priority support
- ✅ 3 site licenses

### Advanced Tier ($149/year)
- ✅ All Pro features
- ✅ Theme Builder access
- ✅ Advanced Controls
- ✅ A/B Testing
- ✅ Performance Analytics
- ✅ 5 site licenses

### Agency Tier ($399/year)
- ✅ All Advanced features
- ✅ White-Label options
- ✅ Cloud Storage
- ✅ Payment Gateway
- ✅ Credits System
- ✅ Unlimited site licenses

---

## Customization

### Adding New Templates

Edit `/inc/admin/views/templates.php`:

```php
$templates = array(
    array(
        'name'     => 'My New Template',
        'category' => 'business',
        'image'    => NEXUS_URI . '/assets/admin/images/template-new.jpg',
        'features' => array( 'Homepage', 'Contact Page', 'About Page' ),
        'premium'  => false, // Set to true for pro-only
    ),
    // ... more templates
);
```

### Customizing Dashboard Stats

Edit `/inc/admin/views/dashboard.php`:

```php
<div class="nexus-stat-card">
    <div class="stat-icon">🎨</div>
    <div class="stat-content">
        <h3>Custom Stat</h3>
        <p class="stat-number"><?php echo get_custom_stat(); ?></p>
    </div>
</div>
```

### Adding Settings Fields

Edit `/inc/admin/views/settings.php`:

```php
<tr>
    <th scope="row">
        <label for="my_new_setting"><?php esc_html_e( 'My New Setting', 'nexus' ); ?></label>
    </th>
    <td>
        <input type="text" 
               id="my_new_setting" 
               name="nexus_theme_settings[my_new_setting]" 
               value="<?php echo esc_attr( $settings['my_new_setting'] ?? '' ); ?>" />
    </td>
</tr>
```

### Styling Customization

Edit `/assets/admin/css/admin.css`:

```css
/* Custom stat card colors */
.nexus-stat-card.custom {
    background: linear-gradient(135deg, #your-color-1 0%, #your-color-2 100%);
}

/* Custom button styles */
.nexus-btn.custom {
    background: #your-button-color;
}
```

---

## JavaScript Events

### Template Import

```javascript
// Custom import handler
jQuery('.nexus-import-btn').on('click', function() {
    var templateId = jQuery(this).data('template-id');
    // Your custom import logic
});
```

### Settings Save

```javascript
// Custom validation before save
jQuery('#nexus-settings-form').on('submit', function(e) {
    // Your custom validation
    if (!valid) {
        e.preventDefault();
    }
});
```

---

## AJAX Endpoints

### Template Import (To Be Implemented)

```php
// Add to class-nexus-admin.php
add_action( 'wp_ajax_nexus_import_template', array( $this, 'ajax_import_template' ) );

public function ajax_import_template() {
    check_ajax_referer( 'nexus-admin-nonce', 'nonce' );
    
    $template_id = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : '';
    
    // Import logic here
    // 1. Download template files
    // 2. Import content
    // 3. Set theme_mods
    // 4. Import widgets
    
    wp_send_json_success( array(
        'message' => 'Template imported successfully!',
    ) );
}
```

---

## Migration from v3.0.1 to v3.1.0

### What's New
- ✨ Complete admin dashboard
- ✨ Template library with 8 starter templates
- ✨ Centralized settings panel
- ✨ Getting started onboarding
- ✨ System diagnostics tool
- ✨ Modern gradient UI design
- ✨ Mobile-responsive admin interface

### Automatic Updates
1. WordPress will detect new version
2. Admin sees update notification
3. Click "Update Now"
4. Dashboard automatically available at **Nexus** menu

### Manual Installation
1. Download nexus-theme-v3.1.0.zip
2. Go to Appearance > Themes
3. Click "Add New" > "Upload Theme"
4. Select ZIP file and click "Install Now"
5. Activate theme
6. Access **Nexus** menu in admin sidebar

---

## Troubleshooting

### Dashboard Not Showing

**Issue:** "Nexus" menu not appearing in admin sidebar

**Solutions:**
1. Verify theme version is 3.1.0 in Appearance > Themes
2. Check `functions.php` includes admin class: `require_once NEXUS_DIR . '/inc/admin/class-nexus-admin.php'`
3. Verify file exists: `/inc/admin/class-nexus-admin.php`
4. Clear browser cache and WordPress object cache
5. Check for PHP errors in WP Debug log

### Templates Not Importing

**Issue:** Template import fails or shows error

**Solutions:**
1. Verify license tier (premium templates require Pro or higher)
2. Check file permissions on uploads directory
3. Increase PHP memory limit (recommended: 256M)
4. Check PHP max execution time (recommended: 300s)
5. View browser console for JavaScript errors

### Settings Not Saving

**Issue:** Changes don't persist after clicking "Save Settings"

**Solutions:**
1. Verify nonce is present in form
2. Check database connection
3. Verify user has `manage_options` capability
4. Check for conflicts with security plugins
5. Review PHP error log

### Styles Not Loading

**Issue:** Dashboard appears unstyled

**Solutions:**
1. Verify `/assets/admin/css/admin.css` exists
2. Check `enqueue_admin_assets()` method in class
3. Clear browser cache
4. Verify correct admin page hook
5. Check for CSS conflicts with plugins

---

## Performance

### Optimizations
- Admin assets only loaded on Nexus admin pages (not globally)
- CSS minification ready
- Lazy loading for template preview images
- AJAX-based template import (non-blocking)
- Efficient database queries with caching

### Best Practices
- Only activate needed features
- Use template import for quick setup
- Regular theme updates
- Monitor system info for server health
- Keep WordPress and plugins updated

---

## Support

### Documentation
- Full documentation: https://jdsandigitel.com/docs
- API Reference: https://jdsandigitel.com/api-docs

### Community
- Support Forum: https://jdsandigitel.com/forum
- Video Tutorials: https://jdsandigitel.com/tutorials

### Contact
- Priority Support: support@jdsandigitel.com (Pro/Advanced/Agency)
- General Support: https://jdsandigitel.com/support

---

## Changelog

### Version 3.1.0 (Current)
- ✨ Added complete admin dashboard
- ✨ Added template library with 8 starter templates
- ✨ Added centralized settings panel
- ✨ Added getting started onboarding guide
- ✨ Added system information diagnostics
- ✨ Modern gradient UI design
- ✨ Mobile-responsive admin interface
- 🔧 Improved license tier detection
- 🔧 Enhanced user experience
- 📚 Comprehensive admin documentation

### Version 3.0.1
- 🐛 Fixed license activation issues
- 🔧 Added legacy API support
- 🔧 Improved update system

### Version 3.0.0
- Initial release with multi-tier licensing

---

## Credits

**Inspired By:**
- Astra Theme - Dashboard UI patterns
- Hello Elementor - Onboarding flow
- GeneratePress - Settings organization

**Built With:**
- WordPress Admin API
- jQuery
- CSS Grid & Flexbox
- Modern gradient designs

---

**Last Updated:** December 30, 2024  
**Version:** 3.1.0  
**Author:** Jdsan Digitel
