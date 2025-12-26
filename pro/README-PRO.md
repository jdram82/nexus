# Nexus Pro - Phase 2 Documentation

## Overview

Nexus Pro extends the Nexus Essential theme with advanced features for professional WordPress websites. This premium version includes powerful tools for customization, content management, and client interaction.

## Version Information

- **Version**: 2.0.0
- **Release Date**: December 2025
- **Requires**: Nexus Essential 1.0.0+
- **WordPress**: 5.8+
- **PHP**: 7.4+

## Features

### 1. Visual Header/Footer Builder

Build custom headers and footers using an intuitive drag-and-drop interface in the WordPress Customizer.

#### Available Elements:
- Logo
- Navigation Menu
- Search Box
- Shopping Cart (WooCommerce)
- Account Links
- Social Icons
- Custom Button
- Text/HTML
- Widget Areas

#### Header Structure:
- **3 Rows**: Top, Main, Bottom
- **Flexible Columns**: Left, Center, Right positioning
- **Multiple Styles**: Default, Transparent, Sticky

#### Usage:
```php
// Access Header Builder
Dashboard → Appearance → Customize → Header Builder

// Programmatic Access
$header_layout = get_theme_mod('nexus_header_main_left');
```

### 2. Advanced Product Filtering

Powerful AJAX-based filtering system for products with real-time results.

#### Features:
- **Search**: Real-time product search
- **Categories**: Hierarchical category filtering
- **Tags**: Tag-based filtering
- **Specifications**: Custom specification filters
- **Price Range**: Min/max price filtering (WooCommerce)
- **Sorting**: Multiple sort options

#### Shortcode:
```php
[nexus_product_filter layout="sidebar" show_search="yes" show_sort="yes"]
```

#### Widget:
Add "Nexus Product Filter" widget to any sidebar.

#### Customization:
```php
// Filter query args
add_filter('nexus_product_filter_args', function($args) {
    $args['posts_per_page'] = 20;
    return $args;
});
```

### 3. Documentation System

Complete documentation management system with code highlighting and search.

#### Post Type: `nexus_doc`

#### Features:
- **Hierarchical Structure**: Parent/child documentation
- **Auto TOC**: Automatic table of contents generation
- **Code Highlighting**: Prism.js integration with 8+ languages
- **Versioning**: Documentation version taxonomy
- **Categories**: Organized documentation categories
- **Search**: AJAX-powered documentation search

#### Metadata:
- Difficulty Level (Beginner/Intermediate/Advanced)
- Estimated Read Time
- External URL linking

#### Shortcodes:
```php
// Search Box
[nexus_docs_search placeholder="Search docs..." show_popular="yes"]

// Code Block
[code lang="php" title="Example Function" highlight="3,5-7"]
function example() {
    return true;
}
[/code]
```

#### Customization:
```php
// Disable auto TOC globally
add_filter('nexus_docs_auto_toc', '__return_false');

// Custom code languages
add_filter('nexus_docs_languages', function($langs) {
    $langs[] = 'rust';
    return $langs;
});
```

### 4. Client Portal

Secure client area with project management and downloads.

#### URL Structure:
```
/client-portal/          - Dashboard
/client-portal/projects/ - Client Projects
/client-portal/downloads/ - Available Downloads
/client-portal/support/  - Support Tickets
/client-portal/profile/  - Profile Settings
```

#### Features:
- **Dashboard**: Overview of projects, downloads, tickets
- **Projects**: Assigned project viewing
- **Downloads**: Access to client files
- **Support**: Ticket management (placeholder)
- **Profile**: User profile editing

#### Project Assignment:
```php
// Assign project to client (in admin)
Dashboard → Projects → Edit Project → Client Access

// Get client projects programmatically
$projects = get_posts(array(
    'post_type' => 'nexus_project',
    'meta_key' => '_client_id',
    'meta_value' => get_current_user_id()
));
```

#### Customization:
```php
// Add custom portal page
add_filter('nexus_portal_pages', function($pages) {
    $pages['invoices'] = 'Invoices';
    return $pages;
});

// Custom dashboard widgets
add_action('nexus_portal_dashboard_before', function() {
    echo '<div class="custom-widget">Content</div>';
});
```

### 5. Form Builder

Drag-and-drop form builder with submission management.

#### Post Type: `nexus_form`

#### Supported Fields:
- Text
- Email
- Phone
- Textarea
- Dropdown (Select)
- Radio Buttons
- Checkboxes
- File Upload

#### Field Options:
- Label
- Name (field identifier)
- Placeholder
- Required validation
- Options (for select/radio/checkbox)

#### Form Settings:
- Submit button text
- Success message
- Email notification recipient
- Email subject

#### Shortcode:
```php
[nexus_form id="123"]
```

#### Submissions:
All form submissions are stored in `wp_nexus_form_submissions` table.

View submissions: **Dashboard → Nexus Pro → Submissions**

#### Customization:
```php
// Validation hook
add_filter('nexus_form_validate', function($errors, $data, $form_id) {
    if (empty($data['company'])) {
        $errors['company'] = 'Company is required';
    }
    return $errors;
}, 10, 3);

// Before email send
add_action('nexus_form_before_email', function($form_id, $data) {
    // Custom processing
}, 10, 2);

// Custom email headers
add_filter('nexus_form_email_headers', function($headers) {
    $headers[] = 'Cc: sales@example.com';
    return $headers;
});
```

## Installation

### Method 1: Upload Pro Files

1. Extract the Nexus Pro package
2. Upload the `/pro/` folder to `/wp-content/themes/nexus/`
3. Activate your license key

### Method 2: Using FTP

1. Connect to your server via FTP
2. Navigate to `/wp-content/themes/nexus/`
3. Upload the `pro` folder
4. Verify all files are uploaded

### Activation

1. Go to **Dashboard → Nexus Pro → License**
2. Enter your license key
3. Click "Activate License"
4. Features will be enabled immediately

## Upgrading from Essential

1. Ensure Nexus Essential 1.0.0+ is installed
2. Install Pro version (files will merge)
3. Activate license
4. All Essential features remain functional
5. Pro features are added on top

## File Structure

```
nexus-theme/
├── pro/
│   ├── class-nexus-pro.php          # Main Pro class
│   ├── admin/
│   │   ├── class-pro-admin.php      # Admin interface
│   │   ├── class-license-manager.php # License validation
│   │   └── views/                    # Admin view templates
│   ├── builder/
│   │   ├── class-header-builder.php  # Header builder
│   │   ├── class-footer-builder.php  # Footer builder
│   │   └── class-builder-elements.php # Element definitions
│   ├── filtering/
│   │   ├── class-product-filter.php  # Filter UI
│   │   └── class-ajax-filter.php     # AJAX handler
│   ├── documentation/
│   │   ├── class-docs-manager.php    # Docs post type
│   │   ├── class-docs-search.php     # Search functionality
│   │   └── class-code-highlighter.php # Syntax highlighting
│   ├── portal/
│   │   ├── class-portal-manager.php  # Portal core
│   │   ├── class-portal-dashboard.php # Dashboard
│   │   └── class-portal-projects.php  # Project assignment
│   ├── forms/
│   │   ├── class-form-builder.php    # Form builder UI
│   │   ├── class-form-processor.php  # Submission handler
│   │   └── class-form-fields.php     # Field rendering
│   └── assets/
│       ├── css/                       # Pro stylesheets
│       └── js/                        # Pro JavaScript
```

## Developer Hooks

### Actions

```php
// Header Builder
do_action('nexus_header_element_{$element}');
do_action('nexus_before_header_row', $row);
do_action('nexus_after_header_row', $row);

// Footer Builder
do_action('nexus_footer_element_{$element}');

// Forms
do_action('nexus_form_before_submit', $form_id, $data);
do_action('nexus_form_after_submit', $form_id, $submission_id);
do_action('nexus_form_before_email', $form_id, $data);

// Portal
do_action('nexus_portal_dashboard_before');
do_action('nexus_portal_dashboard_after');
do_action('nexus_portal_sidebar_before');
do_action('nexus_portal_sidebar_after');
```

### Filters

```php
// Filtering
apply_filters('nexus_product_filter_args', $args);
apply_filters('nexus_filter_results', $results);

// Documentation
apply_filters('nexus_docs_auto_toc', true);
apply_filters('nexus_docs_languages', $languages);

// Forms
apply_filters('nexus_form_validate', $errors, $data, $form_id);
apply_filters('nexus_form_email_to', $email, $form_id);
apply_filters('nexus_form_email_subject', $subject, $form_id);

// Portal
apply_filters('nexus_portal_pages', $pages);
apply_filters('nexus_portal_stats', $stats);
```

## Performance

### Optimization Tips

1. **Header/Footer Builder**: Cached on frontend, regenerated only on customizer save
2. **Product Filtering**: Uses transients for taxonomy queries (1 hour cache)
3. **Documentation Search**: Indexes on post save for faster queries
4. **Forms**: Submissions table uses indexed columns for performance

### Recommended Settings

- **Object Cache**: Redis or Memcached for large sites
- **CDN**: For Prism.js and other external assets
- **Page Caching**: Compatible with WP Rocket, W3 Total Cache

## Security

### Features

- **Nonce Verification**: All AJAX requests verified
- **Capability Checks**: Admin actions require proper permissions
- **Data Sanitization**: All inputs sanitized before storage
- **Prepared Statements**: SQL queries use $wpdb->prepare()
- **License Validation**: Server-side license checking

### Best Practices

```php
// Always verify nonces in custom forms
wp_verify_nonce($_POST['nonce'], 'action_name');

// Check capabilities
if (!current_user_can('manage_options')) {
    wp_die('Unauthorized');
}

// Sanitize inputs
$value = sanitize_text_field($_POST['value']);
```

## Troubleshooting

### Common Issues

**1. License won't activate**
- Check internet connection
- Verify license key is correct
- Ensure site URL matches license
- Check for firewall blocking outbound requests

**2. Forms not submitting**
- Check AJAX URL in browser console
- Verify nonce is present
- Check PHP error logs
- Ensure database table exists

**3. Portal shows 404**
- Flush rewrite rules: Settings → Permalinks → Save
- Check if user is logged in
- Verify portal files exist

**4. Builder not loading**
- Clear browser cache
- Check JavaScript errors in console
- Verify jQuery is loaded
- Check file permissions

### Debug Mode

```php
// Enable debug mode in wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Nexus Pro specific debugging
define('NEXUS_PRO_DEBUG', true);
```

## Support

### Getting Help

1. **Documentation**: Read this guide thoroughly
2. **Knowledge Base**: Visit support.example.com
3. **Support Ticket**: Submit ticket with:
   - WordPress version
   - PHP version
   - Active plugins list
   - Error messages
   - Steps to reproduce

### System Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher
- mod_rewrite enabled
- 64MB PHP memory limit (128MB recommended)

## Changelog

### Version 2.0.0 (December 2025)
- Initial Pro release
- Header/Footer Builder
- Advanced Product Filtering
- Documentation System
- Client Portal
- Form Builder
- License management

## Credits

- **Prism.js**: Syntax highlighting (MIT License)
- **Dashicons**: WordPress icon font
- **jQuery UI**: Sortable functionality

## License

Nexus Pro is licensed under the GPLv2 or later license with the following exceptions:

- One license = One website installation
- License key required for updates
- Support included for 1 year from purchase
- Renewal required for continued updates/support

---

**© 2025 Nexus Theme. All rights reserved.**
