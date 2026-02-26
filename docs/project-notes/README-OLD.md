# Nexus WordPress Theme

A powerful, modern WordPress theme designed specifically for **Electrical & Control Systems**, **AI/ML**, and **Embedded Systems** businesses.

## Features

### ✨ Core Features (Essential)
- **Custom Product Catalog** - Showcase technical products with detailed specifications
- **Project Portfolio** - Display case studies and completed projects
- **Downloads Center** - Manage datasheets, manuals, firmware, and technical documents
- **Responsive Design** - Mobile-first, works on all devices
- **Customizer Integration** - Live preview of colors, typography, and layout
- **Performance Optimized** - Fast loading, minimal bloat
- **WooCommerce Ready** - Full e-commerce support
- **SEO Optimized** - Schema markup and best practices

### 🎨 Design Features
- **Global Color Palette** - 9 customizable colors
- **Typography Controls** - Choose from popular Google Fonts
- **Layout Options** - Left sidebar, right sidebar, or no sidebar
- **Custom Header/Footer** - Widget-ready areas
- **Sticky Navigation** - Smooth scrolling header

### 🔧 Technical Features
- **Specification Tables** - Display technical parameters elegantly
- **Product Categories & Tags** - Organize products effectively
- **Industry & Technology Taxonomies** - Categorize projects
- **Version Management** - Track file versions for downloads
- **Code Syntax Highlighting** - Ready for technical documentation

## Installation

### Requirements
- WordPress 5.8 or higher
- PHP 7.4 or higher
- Node.js 14+ (for development)

### Steps

1. **Download the theme**
   ```bash
   git clone https://github.com/yourusername/nexus-theme.git
   ```

2. **Install dependencies**
   ```bash
   cd nexus-theme
   npm install
   ```

3. **Build assets**
   ```bash
   npm run build
   ```

4. **Upload to WordPress**
   - Zip the theme folder
   - Go to WordPress Admin > Appearance > Themes > Add New > Upload Theme
   - Activate the theme

## Development

### Build Commands

- **Development build with watch:**
  ```bash
  npm run watch
  ```

- **Production build:**
  ```bash
  npm run build
  ```

- **Development build (one-time):**
  ```bash
  npm run dev
  ```

### File Structure

```
nexus-theme/
├── assets/
│   ├── src/
│   │   ├── scss/          # Source SASS files
│   │   └── js/            # Source JavaScript
│   └── dist/              # Compiled assets (auto-generated)
├── inc/
│   ├── customizer/        # Customizer settings
│   ├── post-types/        # Custom post types
│   ├── woocommerce/       # WooCommerce integration
│   └── *.php              # Core PHP classes
├── template-parts/
│   └── content/           # Content templates
├── templates/             # Page templates
├── functions.php          # Theme functions
├── style.css              # Main stylesheet (required)
├── header.php             # Header template
├── footer.php             # Footer template
├── index.php              # Main template
├── sidebar.php            # Sidebar template
└── package.json           # NPM dependencies
```

## Customization

### Colors

Navigate to **Appearance > Customize > Theme Colors** to customize:
- Primary Color
- Secondary Color
- Accent Color
- Success, Warning, Danger, Info colors
- Light and Dark colors

### Typography

Navigate to **Appearance > Customize > Typography** to customize:
- Heading Font
- Body Font
- Font Size

### Layout

Navigate to **Appearance > Customize > Layout Settings** to customize:
- Container Width
- Sidebar Position

## Custom Post Types

### Products (`nexus_product`)
- Product Categories (`product_category`)
- Product Tags (`product_tag`)
- Specifications (custom meta fields)

### Projects (`nexus_project`)
- Industries (`project_industry`)
- Technologies (`project_technology`)

### Downloads (`nexus_download`)
- Download Categories (`download_category`)
- File URL, Version, Size (custom meta fields)

## Template Functions

### Display Product Specifications
```php
<?php nexus_display_specifications(); ?>
```

### Get Download URL
```php
<?php
$url = nexus_get_download_url();
echo '<a href="' . esc_url( $url ) . '">Download</a>';
?>
```

### Check for Sidebar
```php
<?php if ( nexus_has_sidebar() ) : ?>
    <?php get_sidebar(); ?>
<?php endif; ?>
```

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Changelog

### Version 1.0.0 (Initial Release)
- Core theme structure
- Custom post types (Products, Projects, Downloads)
- Customizer integration
- Responsive design
- WooCommerce support
- Performance optimization

## Roadmap

### Phase 2 (Pro Version)
- Visual Header/Footer Builder
- Advanced Product Filtering
- Documentation System
- Client Portal
- Form Builder

### Phase 3 (Advanced Version)
- Theme Builder
- API Documentation
- Interactive Simulators
- 3D Model Viewer
- Advanced Analytics

## Support

For support, email support@nexustheme.com or visit our documentation at https://docs.nexustheme.com

## License

This theme is licensed under the GPL v2 or later.

## Credits

- Developed by [Your Name]
- Built with WordPress
- Icons from Font Awesome
- Fonts from Google Fonts

## Contributing

Contributions are welcome! Please read our contributing guidelines before submitting pull requests.

---

**Nexus** - Built for technical excellence.
