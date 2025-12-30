# Nexus Template Manager Documentation

## Overview

The Nexus Template Manager is an **Advanced Tier** feature that provides a comprehensive template system with 50+ pre-built templates across 10 categories. Import professional templates with one click, export your own designs, and sync with the cloud.

## Features

### 🎨 50+ Premium Templates
- **10 Categories**: Business, SaaS, E-commerce, Portfolio, Blog, Documentation, Landing Pages, Marketing, Education, Events
- **Multiple Layouts**: Homepage, product pages, landing pages, documentation layouts, and more
- **One-Click Import**: Import entire templates including content, media, and settings
- **Professional Design**: Each template is professionally designed and optimized

### 📦 Template Components
- **Flexible Sections**: Header, hero, features, testimonials, pricing, contact, and more
- **Widget Support**: Heading, text, button, and image widgets
- **Responsive Design**: All templates are mobile-friendly
- **Customizable**: Full Customizer integration for easy modifications

### ☁️ Cloud Sync (Pro+)
- **Backup Templates**: Sync your templates to the cloud
- **Multi-Site Sync**: Access your templates across multiple sites
- **Tier Limits**: Pro (5 templates), Advanced (unlimited)
- **Automatic Updates**: Keep templates up to date

### 🔧 Import/Export
- **Page Export**: Export any page as a reusable template
- **Media Import**: Automatically download and import images
- **Settings Import**: Import theme customizer settings
- **Widget Import**: Import widget configurations

## Quick Start

### Browsing Templates

1. Navigate to **Pro Features > Template Library** in WordPress admin
2. Use the category filter to browse templates by type
3. Use the search bar to find specific templates
4. Filter by template type (page, landing, etc.)

### Importing a Template

**Method 1: Direct Import**
1. Click the **Import** button on any template card
2. Confirm the import action
3. Wait for the import to complete
4. You'll be redirected to edit the new page

**Method 2: Preview First**
1. Click the **Preview** button on any template card
2. Review the template design and content
3. Configure import options:
   - ✅ Import Settings
   - ✅ Import Content
   - ✅ Import Media
4. Click **Import Template**
5. The page will be created and opened for editing

### Exporting a Template

1. Go to the **Export** tab
2. Select the page you want to export
3. Click **Export Template**
4. A JSON file will be downloaded to your computer
5. Share or reuse the template file

### Cloud Sync

**Uploading to Cloud:**
1. Create or import a template
2. It will automatically sync to the cloud (tier limits apply)
3. Pro tier: Up to 5 templates
4. Advanced tier: Unlimited templates

**Downloading from Cloud:**
1. Go to **Pro Features > Template Library**
2. Templates marked with "Cloud Synced" badge are from the cloud
3. Click **Import** to add them to your site

## Template Structure

### JSON Format

Templates are stored as JSON files with the following structure:

```json
{
  "name": "Template Name",
  "description": "Template description",
  "category": "business",
  "type": "page",
  "tags": ["homepage", "corporate", "professional"],
  "thumbnail": "https://example.com/thumbnail.jpg",
  "sections": [
    {
      "type": "hero",
      "settings": {
        "background": "#f5f5f5",
        "padding": "80px 0"
      },
      "columns": [
        {
          "width": "100%",
          "widgets": [
            {
              "type": "heading",
              "content": "Welcome",
              "tag": "h1",
              "style": "font-size: 48px;"
            },
            {
              "type": "text",
              "content": "Description text"
            },
            {
              "type": "button",
              "text": "Get Started",
              "url": "#contact",
              "style": "primary"
            }
          ]
        }
      ]
    }
  ],
  "settings": {
    "theme_mods": {
      "primary_color": "#2196f3"
    }
  }
}
```

### Section Types

- **hero**: Large hero section with heading, text, and CTA
- **features**: Grid of feature items with icons
- **about**: About section with image and text
- **services**: Service offerings grid
- **portfolio**: Project/portfolio showcase
- **testimonials**: Customer testimonials
- **pricing**: Pricing tables
- **team**: Team member cards
- **contact**: Contact form and information
- **footer**: Footer with multiple columns
- **custom**: Custom HTML content

### Widget Types

**Heading Widget:**
```json
{
  "type": "heading",
  "content": "Heading Text",
  "tag": "h1",
  "style": "font-size: 36px; color: #333;"
}
```

**Text Widget:**
```json
{
  "type": "text",
  "content": "Paragraph text with <strong>HTML</strong> support."
}
```

**Button Widget:**
```json
{
  "type": "button",
  "text": "Click Me",
  "url": "https://example.com",
  "style": "primary",
  "target": "_blank"
}
```

**Image Widget:**
```json
{
  "type": "image",
  "url": "https://example.com/image.jpg",
  "alt": "Image description",
  "caption": "Image caption"
}
```

## Creating Custom Templates

### Step 1: Design Your Page

1. Create a new page in WordPress
2. Design your page using:
   - Gutenberg blocks
   - Page builder
   - Custom HTML
   - Widgets

### Step 2: Configure Settings

1. Go to **Appearance > Customize**
2. Set your desired theme options
3. Configure colors, fonts, and layouts
4. Save your changes

### Step 3: Export Template

1. Navigate to **Pro Features > Template Library**
2. Go to the **Export** tab
3. Select your page from the dropdown
4. Click **Export Template**
5. Save the JSON file

### Step 4: Customize Template File

1. Open the downloaded JSON file in a text editor
2. Update metadata:
   ```json
   {
     "name": "My Custom Template",
     "description": "A brief description",
     "category": "business",
     "tags": ["custom", "professional"]
   }
   ```
3. Save the file

### Step 5: Share or Reuse

- **Upload to Cloud**: Import on your site and it syncs automatically
- **Share with Team**: Send the JSON file to teammates
- **Reuse on Other Sites**: Import on multiple WordPress sites
- **Create Template Library**: Build a collection of templates

## Categories

### 1. Business Templates
**Purpose**: Corporate websites, professional services, consulting firms

**Templates:**
- Business Homepage
- Corporate Layout (5 variants)
- Service Pages
- About Us Pages

**Use Cases:**
- Professional service firms
- Consulting companies
- B2B businesses
- Corporate websites

### 2. SaaS Templates
**Purpose**: Software products, cloud services, tech startups

**Templates:**
- SaaS Landing Page
- Product Pages (5 variants)
- Feature Showcases
- Pricing Pages

**Use Cases:**
- Software companies
- Cloud service providers
- Tech startups
- Digital products

### 3. E-commerce Templates
**Purpose**: Online stores, product catalogs, retail sites

**Templates:**
- E-commerce Shop Homepage
- Product Detail Pages (4 variants)
- Category Pages
- Shopping Cart Layouts

**Use Cases:**
- Online stores
- Product catalogs
- Retail websites
- Digital marketplaces

### 4. Portfolio Templates
**Purpose**: Creative professionals, agencies, freelancers

**Templates:**
- Portfolio Minimal
- Photography Portfolio
- Creative Portfolios (4 variants)
- Agency Showcases

**Use Cases:**
- Photographers
- Designers
- Creative agencies
- Freelancers

### 5. Blog Templates
**Purpose**: Content websites, magazines, news sites

**Templates:**
- Blog Magazine Layout
- Personal Blog
- Tech Blog Variants (3)
- Article Layouts

**Use Cases:**
- Bloggers
- Content marketers
- Online magazines
- News sites

### 6. Documentation Templates
**Purpose**: API docs, knowledge bases, user guides

**Templates:**
- API Documentation
- Knowledge Base
- User Guides (3 variants)
- Technical Documentation

**Use Cases:**
- Software documentation
- API references
- Help centers
- Knowledge bases

### 7. Landing Page Templates
**Purpose**: Product launches, lead generation, conversions

**Templates:**
- Product Launch Page
- Webinar Landing Page
- Conversion-Optimized Layouts (5 variants)
- Lead Capture Pages

**Use Cases:**
- Product launches
- Lead generation
- Webinar registration
- Campaign pages

### 8. Marketing Templates
**Purpose**: Marketing agencies, SEO firms, digital marketing

**Templates:**
- Marketing Agency Homepage
- SEO Service Page
- Marketing Service Pages (3 variants)
- Case Study Layouts

**Use Cases:**
- Marketing agencies
- SEO companies
- Digital marketers
- Growth agencies

### 9. Education Templates
**Purpose**: Online courses, universities, learning platforms

**Templates:**
- Online Course Page
- University Homepage
- Learning Platform Layouts (3 variants)
- Course Catalogs

**Use Cases:**
- Online courses
- Educational institutions
- E-learning platforms
- Training programs

### 10. Event Templates
**Purpose**: Conferences, meetups, workshops, events

**Templates:**
- Conference Homepage
- Meetup Page
- Workshop Layouts (2 variants)
- Event Registration

**Use Cases:**
- Conferences
- Meetups
- Workshops
- Virtual events

## Advanced Features

### Search and Filtering

**Search by Name:**
```javascript
// Templates are automatically filtered as you type
$('#template-search').val('business');
```

**Filter by Category:**
```javascript
// Click category filter items
$('.category-filter-item[data-category="business"]').click();
```

**Filter by Type:**
```javascript
// Use type dropdown
$('#template-type').val('landing');
```

### Custom Import Options

When importing templates, you can customize what gets imported:

```javascript
{
  import_settings: true,   // Import theme customizer settings
  import_content: true,    // Import page content
  import_media: true       // Download and import images
}
```

**Settings Only:**
- Imports theme_mods
- Imports theme options
- Imports widget configurations
- **Does NOT** create a new page

**Content Only:**
- Creates a new page
- Imports all content
- **Does NOT** modify theme settings
- **Does NOT** import media

**Media Only:**
- Downloads remote images
- Adds to media library
- Updates image URLs in content
- **Does NOT** modify theme settings

### Programmatic Usage

**Get Templates:**
```php
$manager = Nexus_Template_Manager::get_instance();
$templates = $manager->get_templates([
    'category' => 'business',
    'search' => 'homepage',
    'type' => 'page'
]);
```

**Import Template:**
```php
$importer = Nexus_Template_Importer::get_instance();
$result = $importer->import_template($template_data, [
    'import_settings' => true,
    'import_content' => true,
    'import_media' => true
]);
```

**Export Template:**
```php
$manager = Nexus_Template_Manager::get_instance();
$template = $manager->export_template($page_id);
```

## API Reference

### Nexus_Template_Manager

**Methods:**

- `get_instance()` - Get singleton instance
- `init()` - Initialize the manager
- `get_templates($args)` - Get filtered templates
- `load_all_templates()` - Load all template files
- `get_categories()` - Get category definitions
- `browse_templates()` - AJAX handler for browsing
- `export_template()` - AJAX handler for export

**Hooks:**

```php
// Filter templates before rendering
add_filter('nexus_templates_list', function($templates) {
    // Modify $templates array
    return $templates;
});

// Filter template data before import
add_filter('nexus_template_data', function($data, $template_id) {
    // Modify $data array
    return $data;
}, 10, 2);
```

### Nexus_Template_Importer

**Methods:**

- `get_instance()` - Get singleton instance
- `import_template($data, $options)` - Import a template
- `import_media($media_items)` - Import media files
- `build_content_from_sections($sections)` - Build HTML content
- `render_widget($widget)` - Render a widget
- `import_settings($settings)` - Import theme settings

**Hooks:**

```php
// After template import
add_action('nexus_template_imported', function($page_id, $template_data) {
    // Do something with $page_id
}, 10, 2);

// Before media import
add_filter('nexus_before_media_import', function($media_items) {
    // Modify $media_items array
    return $media_items;
});
```

## Troubleshooting

### Templates Not Loading

**Issue**: Template grid shows "Loading..." indefinitely

**Solutions:**
1. Check browser console for JavaScript errors
2. Verify AJAX URL is correct
3. Check nonce is being passed
4. Ensure template JSON files exist in `/pro/templates/data/`

### Import Fails

**Issue**: Template import fails with error message

**Solutions:**
1. Check tier permissions (Advanced tier required)
2. Verify template JSON is valid
3. Check media URLs are accessible
4. Increase PHP memory limit if needed
5. Check file upload permissions

### Media Not Importing

**Issue**: Template imports but images are missing

**Solutions:**
1. Enable "Import Media" option
2. Check remote image URLs are valid
3. Verify `allow_url_fopen` is enabled in PHP
4. Check WordPress media upload permissions
5. Increase `upload_max_filesize` in PHP

### Cloud Sync Not Working

**Issue**: Templates not syncing to cloud

**Solutions:**
1. Verify license tier (Pro or higher required)
2. Check cloud sync limits (Pro: 5, Advanced: unlimited)
3. Ensure license is active and valid
4. Check internet connectivity
5. Review cloud sync settings

## Best Practices

### Template Design

1. **Keep It Simple**: Don't overcomplicate template structure
2. **Use Semantic HTML**: Proper heading hierarchy (h1, h2, h3)
3. **Mobile First**: Design for mobile, enhance for desktop
4. **Performance**: Optimize images, minimize CSS/JS
5. **Accessibility**: Include alt text, ARIA labels, proper contrast

### Template Creation

1. **Descriptive Names**: Use clear, descriptive template names
2. **Accurate Tags**: Add relevant tags for searchability
3. **Good Descriptions**: Write helpful descriptions
4. **Preview Images**: Include high-quality thumbnail images
5. **Test Imports**: Test your templates before sharing

### Template Management

1. **Regular Backups**: Export important templates regularly
2. **Version Control**: Keep multiple versions of templates
3. **Documentation**: Document custom template structures
4. **Cloud Sync**: Use cloud sync for important templates
5. **Organize**: Use categories and tags effectively

## Support

For additional help:

- **Documentation**: `/docs/THEME_UPDATE_GUIDE.md`
- **API Reference**: `/docs/API-REFERENCE.md`
- **Support Forum**: https://support.nexustheme.com
- **Email**: support@nexustheme.com

## License

Template Manager is available in:
- ✅ **Advanced Tier** ($299/year)
- ✅ **Agency Tier** ($599/year)
- ❌ Pro Tier (Template Library only)
- ❌ Free Tier

## Version History

### Version 3.0.0
- Initial release
- 50+ templates across 10 categories
- One-click import/export
- Cloud sync integration
- Advanced filtering and search
- Media import functionality
- Settings import support
