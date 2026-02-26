# 🎨 Nexus Starter Templates - Complete Setup Guide

## Current Status

The Starter Templates feature is **partially implemented** but missing:
- ❌ Template preview images
- ❌ Template data files (JSON)
- ❌ Template import functionality

## ✅ What's Already Working

- Template listing UI in dashboard
- Filter system (All, Business, E-Commerce, Portfolio, Blog)
- Pro/Free tier detection
- Block patterns (available in WordPress editor)

---

## 🚀 How to Create Starter Templates

### Option 1: Use Block Patterns (EASIEST - ALREADY WORKING!)

Your theme already includes **5 working block patterns**. Users can access them:

1. **Edit any page** in WordPress
2. Click **"+"** button → **Patterns** tab
3. Select **"Nexus Starter Templates"** category
4. Insert pre-designed sections:
   - Hero Section
   - Services Grid
   - About Section
   - Portfolio Grid
   - CTA Section

**To add more block patterns:**
Edit [`inc/block-patterns.php`](inc/block-patterns.php) and add new patterns using this template:

```php
register_block_pattern(
    'nexus/pattern-name',
    array(
        'title'       => __( 'Pattern Title', 'nexus' ),
        'description' => __( 'Pattern description', 'nexus' ),
        'categories'  => array( 'nexus-starters' ),
        'content'     => '<!-- wp:heading -->
<h2>Your Block Content Here</h2>
<!-- /wp:heading -->'
    )
);
```

---

### Option 2: Create Template Preview Images

Create preview images for the templates listed in [`inc/admin/views/templates.php`](inc/admin/views/templates.php):

**Required Images** (recommended size: 1200x900px):

```
assets/admin/images/templates/
├── corporate.jpg      (Business template)
├── agency.jpg         (Agency template - PRO)
├── consulting.jpg     (Consulting template - PRO)
├── shop.jpg          (E-commerce template - PRO)
├── fashion.jpg       (Fashion store - PRO)
├── creative.jpg      (Portfolio template)
├── photographer.jpg  (Photography portfolio - PRO)
├── blog.jpg          (Personal blog)
└── magazine.jpg      (Magazine template - PRO)
```

**How to create template images:**

1. **Design a sample page** in WordPress/Figma/Canva
2. Take a screenshot or export as JPG
3. Resize to 1200x900px
4. Save to `assets/admin/images/templates/`

**Quick method:** Use placeholder image service:
```bash
# Download placeholder images
cd /workspaces/codespaces-blank/nexus-theme
mkdir -p assets/admin/images/templates
cd assets/admin/images/templates

# Create placeholder images (or use real screenshots)
wget https://picsum.photos/1200/900 -O corporate.jpg
wget https://picsum.photos/1200/900 -O agency.jpg
wget https://picsum.photos/1200/900 -O consulting.jpg
# ... repeat for all templates
```

---

### Option 3: Create Full Template Import System (ADVANCED)

For **one-click template import**, you need:

#### 3.1 Create Template Data Files

Create JSON files with page content:

```
assets/templates/data/
├── corporate.json
├── agency.json
├── consulting.json
└── ...
```

**Template JSON structure:**
```json
{
  "name": "Corporate Business",
  "version": "1.0.0",
  "author": "Nexus Theme",
  "pages": [
    {
      "title": "Home",
      "template": "page-templates/full-width.php",
      "content": "<!-- wp:heading --><h1>Welcome</h1><!-- /wp:heading -->",
      "meta": {
        "show_sidebar": false
      }
    },
    {
      "title": "About",
      "content": "<!-- wp:paragraph --><p>About us content</p><!-- /wp:paragraph -->"
    }
  ],
  "customizer": {
    "primary_color": "#2563eb",
    "heading_font": "Inter"
  },
  "widgets": {
    "sidebar-1": []
  }
}
```

#### 3.2 Add Import Handler

Add to [`inc/admin/class-admin-dashboard.php`](inc/admin/class-admin-dashboard.php):

```php
public function ajax_import_template() {
    check_ajax_referer('nexus_admin', 'nonce');
    
    $template_id = sanitize_text_field($_POST['template']);
    $file = get_template_directory() . '/assets/templates/data/' . $template_id . '.json';
    
    if (!file_exists($file)) {
        wp_send_json_error('Template not found');
    }
    
    $data = json_decode(file_get_contents($file), true);
    
    // Import pages
    foreach ($data['pages'] as $page) {
        $page_id = wp_insert_post([
            'post_title' => $page['title'],
            'post_content' => $page['content'],
            'post_status' => 'publish',
            'post_type' => 'page',
        ]);
        
        // Set template
        if (!empty($page['template'])) {
            update_post_meta($page_id, '_wp_page_template', $page['template']);
        }
    }
    
    wp_send_json_success('Template imported successfully');
}
```

#### 3.3 Add JavaScript Handler

Add to [`assets/admin/js/admin.js`](assets/admin/js/admin.js):

```javascript
// Template import
jQuery('.template-import').on('click', function(e) {
    e.preventDefault();
    const templateId = jQuery(this).data('template');
    
    if (!confirm('Import this template? This will create new pages.')) {
        return;
    }
    
    jQuery.post(ajaxurl, {
        action: 'nexus_import_template',
        template: templateId,
        nonce: nexusAdmin.nonce
    }, function(response) {
        if (response.success) {
            alert('Template imported! Check your pages.');
            window.location.href = 'edit.php?post_type=page';
        }
    });
});
```

---

### Option 4: Use Third-Party Template Plugins (RECOMMENDED FOR NOW)

Instead of building everything from scratch, integrate with existing template systems:

#### **Kadence Starter Templates** (FREE)
1. Install plugin: `Kadence Starter Templates`
2. Access via **Appearance → Starter Templates**
3. Import pre-built sites (100+ free templates)
4. Works with any block theme

#### **Astra Starter Templates** (FREE tier available)
1. Install plugin: `Starter Templates by Astra`
2. Browse 200+ templates
3. One-click import

#### **Envato Elements** (Premium - $16.50/month)
- 3,000+ WordPress templates
- Unlimited downloads
- Commercial license

---

## 🔧 Quick Fix: Enable Template Images Now

Run this to update the template view to show images:

```bash
cd /workspaces/codespaces-blank/nexus-theme
mkdir -p assets/admin/images/templates
```

Then update [`inc/admin/views/templates.php`](inc/admin/views/templates.php) line 102 to use theme images or placeholders:

```php
$image_url = get_template_directory_uri() . '/assets/admin/images/templates/' . $template['image'];
$fallback = get_template_directory_uri() . '/Nexus_images/Website mockup.png';
```

Replace the placeholder div with:

```php
<img src="<?php echo esc_url(file_exists(get_template_directory() . '/assets/admin/images/templates/' . $template['image']) ? $image_url : $fallback); ?>" 
     alt="<?php echo esc_attr($template['name']); ?>">
```

---

## 📋 Recommended Approach

For immediate results:

### ✅ **Phase 1: Use What's Already Working**
1. Promote the **Block Patterns** feature (already functional!)
2. Users can access via WordPress editor → Patterns → "Nexus Starter Templates"
3. Add more patterns to `inc/block-patterns.php`

### ✅ **Phase 2: Add Visual Appeal**
1. Create 9 template preview images (1200x900px)
2. Save to `assets/admin/images/templates/`
3. Update templates.php to load images

### ✅ **Phase 3: Full Template Import** (Future)
1. Create template JSON files
2. Add import AJAX handler
3. Test with demo content

---

## 🎯 What Users See Now

**Dashboard → Templates tab shows:**
- ✅ Template cards with names
- ✅ Pro/Free badges
- ✅ Category filters
- ❌ No preview images (shows placeholder icon)
- ❌ Import button exists but not functional

**Block Editor (Working!) shows:**
- ✅ 5 functional block patterns
- ✅ Ready to insert and customize
- ✅ Professional designs

---

## 💡 Recommended Next Steps

1. **Create template screenshots** (30 min)
   - Design sample pages in WordPress
   - Take screenshots
   - Save to `assets/admin/images/templates/`

2. **Update template display** (15 min)
   - Modify templates.php to show images
   - Test in dashboard

3. **Add 5 more block patterns** (1 hour)
   - Edit `inc/block-patterns.php`
   - Create: Pricing, Testimonials, FAQ, Contact, Team sections

4. **Document for users** (30 min)
   - Create user guide showing how to use patterns
   - Add to README or admin dashboard

---

## 📚 Resources

- **Block Patterns Handbook**: https://developer.wordpress.org/block-editor/reference-guides/block-api/block-patterns/
- **Template Kit Export**: https://wordpress.org/plugins/template-kit-export/
- **Kadence Blocks**: https://www.kadencewp.com/kadence-blocks/ (for inspiration)
- **Pattern Directory**: https://wordpress.org/patterns/

---

## 🐛 Troubleshooting

**Q: Templates tab shows placeholders?**
A: Images are missing. Create screenshots and save to `assets/admin/images/templates/`

**Q: Import button doesn't work?**
A: Import functionality not implemented yet. Use block patterns instead (they work!)

**Q: Where are the templates?**
A: Working templates are in WordPress Editor → Patterns → "Nexus Starter Templates"

**Q: Can I use Elementor templates?**
A: Nexus is a block theme. Use WordPress blocks or install Elementor separately.

---

## ✨ Example: Adding a New Block Pattern

Edit [`inc/block-patterns.php`](inc/block-patterns.php), add before the closing `}`:

```php
// Pricing Table Pattern
register_block_pattern(
    'nexus/pricing-table',
    array(
        'title'       => __( 'Pricing Table - 3 Columns', 'nexus' ),
        'description' => __( 'Professional pricing comparison table', 'nexus' ),
        'categories'  => array( 'nexus-starters' ),
        'content'     => '<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide">
    <!-- wp:column -->
    <div class="wp-block-column">
        <!-- wp:heading {"textAlign":"center"} -->
        <h2 class="has-text-align-center">Starter</h2>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center","fontSize":"large"} -->
        <p class="has-text-align-center has-large-font-size"><strong>$29</strong>/month</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:list -->
        <ul><li>10 Projects</li><li>5GB Storage</li><li>Email Support</li></ul>
        <!-- /wp:list -->
        
        <!-- wp:button {"align":"center"} -->
        <div class="wp-block-button"><a class="wp-block-button__link">Get Started</a></div>
        <!-- /wp:button -->
    </div>
    <!-- /wp:column -->
    
    <!-- wp:column -->
    <div class="wp-block-column">
        <!-- wp:heading {"textAlign":"center"} -->
        <h2 class="has-text-align-center">Pro</h2>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center","fontSize":"large"} -->
        <p class="has-text-align-center has-large-font-size"><strong>$79</strong>/month</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:list -->
        <ul><li>Unlimited Projects</li><li>50GB Storage</li><li>Priority Support</li></ul>
        <!-- /wp:list -->
        
        <!-- wp:button {"align":"center"} -->
        <div class="wp-block-button"><a class="wp-block-button__link">Get Started</a></div>
        <!-- /wp:button -->
    </div>
    <!-- /wp:column -->
    
    <!-- wp:column -->
    <div class="wp-block-column">
        <!-- wp:heading {"textAlign":"center"} -->
        <h2 class="has-text-align-center">Enterprise</h2>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center","fontSize":"large"} -->
        <p class="has-text-align-center has-large-font-size"><strong>$199</strong>/month</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:list -->
        <ul><li>Unlimited Everything</li><li>500GB Storage</li><li>24/7 Support</li></ul>
        <!-- /wp:list -->
        
        <!-- wp:button {"align":"center"} -->
        <div class="wp-block-button"><a class="wp-block-button__link">Contact Us</a></div>
        <!-- /wp:button -->
    </div>
    <!-- /wp:column -->
</div>
<!-- /wp:columns -->'
    )
);
```

Then users can insert it via WordPress Editor → Patterns!

---

**Need help?** Check the WordPress Block Patterns documentation or ask in the Nexus support forum.
