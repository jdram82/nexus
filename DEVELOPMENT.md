# Nexus Theme Development Guide

## Quick Start

### 1. Initial Setup
```bash
chmod +x setup.sh
./setup.sh
```

This will:
- Install all npm dependencies
- Build production assets
- Create necessary directories

### 2. Development Workflow

**Start development mode:**
```bash
npm run watch
```

This watches for changes in `assets/src/` and automatically rebuilds.

**Build for production:**
```bash
npm run build
```

This creates optimized, minified files in `assets/dist/`.

## Project Structure

### PHP Files

**Core Files:**
- `functions.php` - Main theme functions, loads all includes
- `style.css` - Required theme header and base styles
- `index.php` - Main template fallback
- `header.php` - Site header
- `footer.php` - Site footer
- `sidebar.php` - Sidebar widget area

**Inc Directory (`inc/`):**
- `class-nexus-theme-setup.php` - Theme setup, menus, widgets
- `class-nexus-enqueue.php` - Scripts and styles enqueuing
- `template-functions.php` - Template helper functions
- `template-tags.php` - Template display functions

**Customizer (`inc/customizer/`):**
- `class-nexus-customizer.php` - Customizer settings (colors, fonts, layout)

**Custom Post Types (`inc/post-types/`):**
- `class-nexus-products.php` - Products with specifications
- `class-nexus-projects.php` - Project portfolio
- `class-nexus-downloads.php` - Download center

**Template Parts (`template-parts/content/`):**
- `content.php` - Default post content
- `content-nexus_product.php` - Product display
- `content-nexus_project.php` - Project display
- `content-nexus_download.php` - Download display
- `content-none.php` - No results found

### SASS Files

**Main Entry (`assets/src/scss/main.scss`):**
Imports all partial files in order

**Variables (`_variables.scss`):**
- Colors (use CSS custom properties for dynamic values)
- Typography
- Spacing
- Breakpoints
- Shadows, borders, transitions

**Mixins (`_mixins.scss`):**
- Responsive breakpoints
- Flex utilities
- Common patterns (clearfix, transitions, etc.)

**Base Styles:**
- `base/_normalize.scss` - CSS normalize
- `base/_reset.scss` - Custom resets
- `base/_base.scss` - Base elements
- `base/_typography.scss` - Typography styles

**Layout:**
- `layout/_grid.scss` - Grid system
- `layout/_header.scss` - Header styles
- `layout/_footer.scss` - Footer styles
- `layout/_sidebar.scss` - Sidebar styles

**Components:**
- `components/_products.scss` - Product styles
- `components/_projects.scss` - Project styles
- `components/_downloads.scss` - Download styles
- `components/_posts.scss` - Blog post styles
- `components/_widgets.scss` - Widget styles
- `components/_comments.scss` - Comment styles

**Elements:**
- `elements/_buttons.scss` - Button styles
- `elements/_tables.scss` - Table styles
- `elements/_lists.scss` - List styles
- `elements/_elements.scss` - Misc elements

**Forms:**
- `forms/_forms.scss` - Form element styles

**Navigation:**
- `navigation/_navigation.scss` - Main navigation
- `navigation/_menus.scss` - Menu styles

**Utilities:**
- `utilities/_utilities.scss` - Helper classes

### JavaScript Files

**Main JS (`assets/src/js/main.js`):**
- Imports main SCSS
- Mobile menu toggle
- Smooth scroll
- Sticky header
- Back to top button

**Navigation JS (`assets/src/js/navigation.js`):**
- Dropdown menu functionality
- Close menu on outside click
- Keyboard navigation

## Adding New Features

### Adding a New Custom Post Type

1. Create file in `inc/post-types/class-nexus-yourtype.php`
2. Follow the pattern from existing post types
3. Register in `functions.php`:
```php
require_once NEXUS_DIR . '/inc/post-types/class-nexus-yourtype.php';
// In nexus_init()
Nexus_YourType::instance();
```

### Adding New Customizer Options

1. Edit `inc/customizer/class-nexus-customizer.php`
2. Add new section, settings, and controls
3. Update `class-nexus-enqueue.php` to use new settings

### Adding New SASS Components

1. Create `assets/src/scss/components/_newcomponent.scss`
2. Import in `main.scss`:
```scss
@import 'components/newcomponent';
```

### Adding New JavaScript

1. Create file in `assets/src/js/`
2. Add entry point in `webpack.config.js`:
```js
entry: {
  main: './assets/src/js/main.js',
  navigation: './assets/src/js/navigation.js',
  newscript: './assets/src/js/newscript.js', // Add this
},
```
3. Enqueue in `class-nexus-enqueue.php`

## Customization Tips

### Changing Colors Dynamically

Colors use CSS custom properties that are set from customizer:
```css
.my-element {
  color: var(--nexus-primary);
  background: var(--nexus-secondary);
}
```

### Using Mixins

```scss
.my-component {
  @include container; // Max-width container
  @include flex-between; // Flexbox space-between
  @include border-radius($border-radius-lg);
  @include box-shadow($shadow-md);
  
  @include respond-to(md) {
    // Tablet and up
  }
  
  @include respond-to(lg) {
    // Desktop and up
  }
}
```

### Using Template Functions

```php
// In your template
<?php
// Display product specifications
nexus_display_specifications();

// Get download URL
$url = nexus_get_download_url();

// Check if sidebar exists
if ( nexus_has_sidebar() ) {
    get_sidebar();
}
?>
```

## Testing

### Browser Testing
Test in:
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile devices (iOS Safari, Chrome Mobile)

### WordPress Compatibility
Test with:
- Latest WordPress version
- WordPress 5.8+ (minimum requirement)
- Classic Editor plugin
- Gutenberg block editor

### Plugin Testing
Test with:
- WooCommerce
- Contact Form 7
- Yoast SEO
- Elementor (basic compatibility)

## Deployment

### Production Build
```bash
npm run build
```

### Create Distribution Zip
```bash
# From parent directory
zip -r nexus-theme.zip nexus-theme/ \
  -x "*/node_modules/*" \
  -x "*/assets/src/*" \
  -x "*/.git/*" \
  -x "*/.*" \
  -x "*/setup.sh"
```

### Upload to WordPress
1. Go to Appearance > Themes > Add New > Upload Theme
2. Upload the zip file
3. Activate the theme

## Troubleshooting

### Assets not loading
```bash
# Rebuild assets
npm run build

# Check file permissions
chmod -R 755 assets/dist/
```

### Styles not applying
1. Check browser console for 404 errors
2. Clear browser cache
3. Regenerate assets: `npm run build`
4. Check file paths in `class-nexus-enqueue.php`

### Customizer changes not showing
1. Clear browser cache
2. Check if CSS custom properties are being output
3. Inspect element to see if inline styles are added

## Performance Optimization

### Images
- Use WebP format when possible
- Lazy load images
- Optimize image sizes before upload

### CSS/JS
- Production build minifies automatically
- Consider using a caching plugin
- Use CDN for assets

### Database
- Limit revisions
- Clean up unused data
- Optimize database tables

## Next Steps

### Essential Features to Add
1. Archive templates for custom post types
2. Single templates for custom post types
3. Search functionality for products
4. Product filtering
5. Related products widget

### Pro Features (Future)
1. Visual header builder
2. Advanced customizer controls
3. More layout options
4. Template library
5. Import/export settings

---

For support: support@nexustheme.com
Documentation: https://docs.nexustheme.com
