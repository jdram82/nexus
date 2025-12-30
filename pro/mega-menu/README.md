# Nexus Mega Menu - Complete Documentation

## Overview

The Nexus Mega Menu Builder is a powerful Advanced tier feature that enables you to create sophisticated, multi-column navigation menus with icons, badges, widget areas, and advanced styling options.

**Tier Access**: Advanced ($299/year) and Agency ($599/year)

---

## Features

### ✅ Core Features

- **Multi-column layouts** - 2, 3, 4, 5, or 6 columns
- **Visual menu builder** - Drag-and-drop interface
- **Widget areas** - Add any WordPress widget to mega menus
- **Icon support** - 200+ Dashicons with visual picker
- **Badge system** - Add "New", "Hot", "Sale" badges with custom colors
- **Mobile-responsive** - Automatic mobile optimization
- **Keyboard navigation** - Full accessibility support
- **Live preview** - See changes before publishing

### 🎨 Styling Options

- Custom badge colors
- Icon-only menu items
- Disabled links (headings)
- Hide text option
- Column preview
- Depth indicators

---

## Installation & Activation

The Mega Menu is automatically loaded when you activate Nexus Pro (Advanced or Agency tier).

### Verification

1. Navigate to **Appearance > Menus**
2. You should see "Nexus Mega Menu Settings" panel on each menu item
3. Navigate to **Appearance > Menu Builder** for visual builder

---

## Quick Start Guide

### Creating Your First Mega Menu

#### Step 1: Create a Menu
```
WordPress Admin > Appearance > Menus > Create a new menu
```

#### Step 2: Add Menu Items
```
Add pages, custom links, or categories as usual
```

#### Step 3: Enable Mega Menu
```
1. Click on a top-level menu item
2. Find "Nexus Mega Menu Settings" panel
3. Check "Enable Mega Menu"
4. Select column count (2-6 columns)
```

#### Step 4: Configure Child Items
```
Add sub-menu items under the mega menu parent
They will automatically arrange in the selected column layout
```

#### Step 5: Add Icons (Optional)
```
1. In menu item settings, find "Icon" field
2. Click "Choose Icon" button
3. Select from 200+ Dashicons
4. Icon appears next to menu text
```

#### Step 6: Add Badges (Optional)
```
1. In "Badge Text" field, enter text (e.g., "New", "Hot")
2. Pick badge color with color picker
3. Badge appears as colored label next to item
```

---

## Menu Builder (Visual Interface)

### Accessing the Builder

```
WordPress Admin > Appearance > Menu Builder
```

### Builder Interface

#### **Sidebar** (Left Panel)
- **Menu Items List**: All items from selected menu
- **Widget Areas**: Create and manage widget areas

#### **Canvas** (Center Panel)
- **Visual Menu Structure**: Drag-drop menu items
- **Expand/Collapse Controls**: Manage view
- **Live Preview**: See current structure

#### **Settings** (Right Panel)
- **Item Settings**: Configure selected item
- **Mega Menu Options**: Enable/disable mega menu
- **Column Settings**: Choose layout
- **Icon & Badge**: Visual customization

### Creating Widget Areas

```php
// Step 1: Click "Add Widget Area" in builder sidebar
// Step 2: Enter widget area name (e.g., "Featured Products")
// Step 3: Select widget area in mega menu item settings
// Step 4: Add widgets via Appearance > Widgets
```

The widget area will render as an additional column in the mega menu.

---

## Code Examples

### Programmatically Enable Mega Menu

```php
// Enable mega menu for a menu item
$menu_item_id = 123; // Your menu item ID

update_post_meta( $menu_item_id, '_nexus_mega_enabled', 1 );
update_post_meta( $menu_item_id, '_nexus_mega_columns', 4 );
update_post_meta( $menu_item_id, '_nexus_menu_icon', 'dashicons-admin-home' );
update_post_meta( $menu_item_id, '_nexus_menu_badge', 'New' );
update_post_meta( $menu_item_id, '_nexus_menu_badge_color', '#e74c3c' );
```

### Check if Mega Menu is Enabled

```php
if ( Nexus_Mega_Menu::is_mega_menu( $menu_item_id ) ) {
    $columns = Nexus_Mega_Menu::get_mega_columns( $menu_item_id );
    echo "This is a mega menu with {$columns} columns";
}
```

### Register Custom Widget Area

```php
// Widget areas are automatically registered by Menu Builder
// Access them programmatically:
$widget_areas = get_option( 'nexus_mega_menu_widget_areas', array() );

foreach ( $widget_areas as $area ) {
    // Sidebar ID: 'nexus-mega-menu-' . $area['id']
    if ( is_active_sidebar( 'nexus-mega-menu-' . $area['id'] ) ) {
        dynamic_sidebar( 'nexus-mega-menu-' . $area['id'] );
    }
}
```

### Custom Walker Usage

```php
// The walker is automatically applied to all menus
// To manually apply:
wp_nav_menu( array(
    'theme_location' => 'primary',
    'walker'         => new Nexus_Menu_Walker_Frontend(),
) );
```

---

## CSS Customization

### Styling the Mega Menu Container

```css
/* Mega menu container */
.nexus-mega-menu {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 30px;
}

/* Change column width */
.nexus-mega-columns-4 > li {
    width: 25%; /* 4 columns */
}

/* Menu item headings */
.nexus-mega-menu > li > a {
    font-weight: 700;
    text-transform: uppercase;
    border-bottom: 2px solid #e0e0e0;
}
```

### Styling Icons and Badges

```css
/* Icon styling */
.nexus-menu-icon {
    margin-right: 8px;
    font-size: 18px;
    color: #2196f3;
}

/* Badge styling */
.nexus-menu-badge {
    background: #e74c3c;
    color: #fff;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 10px;
}
```

### Mobile Responsive Customization

```css
@media screen and (max-width: 782px) {
    .nexus-mega-menu {
        position: static;
        width: 100%;
        background: #f9f9f9;
    }
    
    .nexus-mega-menu > li {
        width: 100% !important; /* Single column on mobile */
    }
}
```

---

## JavaScript API

### Events

```javascript
// Mega menu opened
jQuery(document).on('nexus-mega-menu-opened', function(event, menuItem) {
    console.log('Mega menu opened:', menuItem);
});

// Mega menu closed
jQuery(document).on('nexus-mega-menu-closed', function(event, menuItem) {
    console.log('Mega menu closed:', menuItem);
});

// Menu item clicked
jQuery(document).on('nexus-menu-item-clicked', function(event, menuItem, url) {
    console.log('Menu item clicked:', menuItem, url);
});
```

### Custom Behavior

```javascript
(function($) {
    // Add custom class when mega menu is active
    $('.nexus-has-mega-menu').on('mouseenter', function() {
        $(this).addClass('my-custom-class');
    }).on('mouseleave', function() {
        $(this).removeClass('my-custom-class');
    });
    
    // Custom animation
    $('.nexus-mega-menu').each(function() {
        $(this).css({
            'transition': 'all 0.5s ease',
            'transform': 'translateY(10px)',
            'opacity': '0'
        });
    });
    
    $('.nexus-has-mega-menu').on('mouseenter', function() {
        $(this).find('.nexus-mega-menu').css({
            'transform': 'translateY(0)',
            'opacity': '1'
        });
    });
})(jQuery);
```

---

## Advanced Usage

### Custom Column Widths

```php
// Use custom CSS to override column widths
add_action( 'wp_head', function() {
    ?>
    <style>
        /* Custom 60/40 split for 2 columns */
        .nexus-mega-columns-2 > li:first-child {
            width: 60%;
        }
        .nexus-mega-columns-2 > li:last-child {
            width: 40%;
        }
    </style>
    <?php
});
```

### Background Images in Mega Menu

```css
/* Add background image to specific mega menu */
#menu-item-123 .nexus-mega-menu {
    background-image: url('/path/to/image.jpg');
    background-size: cover;
    background-position: center;
}

/* Add overlay for readability */
#menu-item-123 .nexus-mega-menu::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1;
}

#menu-item-123 .nexus-mega-menu > * {
    position: relative;
    z-index: 2;
    color: #fff;
}
```

### AJAX Menu Loading

```javascript
// Load menu items via AJAX (for very large menus)
jQuery('.nexus-has-mega-menu').on('mouseenter', function() {
    var $menu = jQuery(this);
    var menuId = $menu.data('menu-id');
    
    if (!$menu.hasClass('loaded')) {
        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: 'load_mega_menu_items',
                menu_id: menuId
            },
            success: function(response) {
                $menu.find('.nexus-mega-menu').html(response);
                $menu.addClass('loaded');
            }
        });
    }
});
```

---

## Accessibility

### ARIA Attributes

The Mega Menu automatically includes proper ARIA attributes:

```html
<a href="#" aria-haspopup="true" aria-expanded="false">
    Products
</a>
<ul role="menu" class="nexus-mega-menu">
    <!-- Menu items -->
</ul>
```

### Keyboard Navigation

- **Tab/Shift+Tab**: Navigate through menu items
- **Enter/Space**: Open/close mega menu
- **Escape**: Close mega menu and return focus to trigger
- **Arrow Keys**: Navigate within mega menu (when enabled)

### Screen Readers

```php
// Add screen reader text
add_filter( 'walker_nav_menu_start_el', function( $item_output, $item ) {
    if ( Nexus_Mega_Menu::is_mega_menu( $item->ID ) ) {
        $item_output .= '<span class="nexus-sr-only">Has submenu</span>';
    }
    return $item_output;
}, 10, 2 );
```

---

## Troubleshooting

### Mega Menu Not Showing

**Check:**
1. ✅ Nexus Pro (Advanced/Agency) is active
2. ✅ "Enable Mega Menu" is checked on parent item
3. ✅ Menu has child items
4. ✅ No CSS `display: none` override
5. ✅ JavaScript is not disabled

**Solution:**
```javascript
// Debug in browser console
jQuery('.nexus-has-mega-menu').length; // Should return > 0
jQuery('.nexus-mega-menu').length; // Should return > 0
```

### Icons Not Displaying

**Check:**
1. ✅ Dashicons are loaded (`wp_enqueue_style('dashicons')`)
2. ✅ Icon class format: `dashicons-icon-name`
3. ✅ No font override in theme

**Solution:**
```php
// Force load Dashicons on frontend
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'dashicons' );
});
```

### Widget Area Not Rendering

**Check:**
1. ✅ Widget area is created in Menu Builder
2. ✅ Widget area is assigned to mega menu item
3. ✅ Widgets are added to the widget area
4. ✅ `is_active_sidebar()` returns true

**Debug:**
```php
// Check if widget area exists
$areas = get_option( 'nexus_mega_menu_widget_areas', array() );
print_r( $areas );

// Check if sidebar is active
is_active_sidebar( 'nexus-mega-menu-widget-area-123' );
```

### Mobile Menu Not Working

**Check:**
1. ✅ JavaScript is loaded on mobile
2. ✅ Click events not prevented by other scripts
3. ✅ CSS breakpoint is correct (782px)

**Solution:**
```javascript
// Debug mobile events
jQuery('.nexus-has-mega-menu > a').on('click', function(e) {
    console.log('Menu item clicked', this, window.innerWidth);
});
```

---

## Performance Optimization

### Lazy Load Mega Menus

```php
// Only load mega menu content on hover
add_filter( 'nexus_mega_menu_lazy_load', '__return_true' );
```

### Limit Menu Items

```php
// For very large menus, limit items per column
add_filter( 'nexus_mega_menu_max_items_per_column', function() {
    return 10; // Max 10 items per column
});
```

### Cache Menu HTML

```php
// Cache mega menu HTML for 1 hour
function get_cached_mega_menu( $menu_id ) {
    $cache_key = 'nexus_mega_menu_' . $menu_id;
    $cached = get_transient( $cache_key );
    
    if ( false === $cached ) {
        ob_start();
        wp_nav_menu( array( 'menu' => $menu_id ) );
        $cached = ob_get_clean();
        
        set_transient( $cache_key, $cached, HOUR_IN_SECONDS );
    }
    
    return $cached;
}
```

---

## Hooks & Filters

### Filters

```php
// Modify mega menu classes
add_filter( 'nexus_mega_menu_classes', function( $classes, $item ) {
    if ( $item->object_id === 123 ) {
        $classes[] = 'custom-mega-menu';
    }
    return $classes;
}, 10, 2 );

// Modify column count
add_filter( 'nexus_mega_menu_columns', function( $columns, $item_id ) {
    // Force 3 columns for specific menu item
    if ( $item_id === 456 ) {
        return 3;
    }
    return $columns;
}, 10, 2 );

// Customize badge output
add_filter( 'nexus_menu_badge_html', function( $html, $badge_text, $badge_color ) {
    return sprintf(
        '<span class="my-badge" style="background: %s">%s</span>',
        esc_attr( $badge_color ),
        esc_html( $badge_text )
    );
}, 10, 3 );
```

### Actions

```php
// Before mega menu renders
add_action( 'nexus_before_mega_menu', function( $item_id ) {
    echo '<div class="mega-menu-wrapper">';
});

// After mega menu renders
add_action( 'nexus_after_mega_menu', function( $item_id ) {
    echo '</div>';
});

// When menu is saved
add_action( 'nexus_menu_builder_saved', function( $menu_id ) {
    // Clear menu cache
    delete_transient( 'nexus_mega_menu_' . $menu_id );
});
```

---

## FAQs

**Q: Can I have multiple mega menus on one page?**  
A: Yes! Each menu can have multiple top-level items with mega menus enabled.

**Q: Is the mega menu SEO-friendly?**  
A: Yes! All menu items are standard HTML links, fully crawlable by search engines.

**Q: Can I use custom widgets in mega menus?**  
A: Absolutely! Any WordPress widget can be added to mega menu widget areas.

**Q: Does it work with WooCommerce?**  
A: Yes! You can add WooCommerce product widgets, categories, etc.

**Q: Can I disable mega menu on mobile?**  
A: Yes, use CSS media queries to hide or style differently:
```css
@media (max-width: 782px) {
    .nexus-mega-menu { display: none !important; }
}
```

**Q: How do I add mega menu to a specific theme location?**  
A: Assign your menu to the theme location in **Appearance > Menus > Menu Settings**.

---

## Support & Resources

- **Documentation**: [Nexus Mega Menu Docs](#)
- **Video Tutorials**: [YouTube Playlist](#)
- **Support Forum**: [Nexus Support](https://support.nexustheme.com)
- **GitHub Issues**: [Report Bugs](https://github.com/jdram82/nexus/issues)

---

## Changelog

### Version 3.0.0
- Initial release with Nexus Advanced tier
- Multi-column layouts (2-6 columns)
- Icon picker with 200+ Dashicons
- Badge system with custom colors
- Widget area support
- Visual menu builder
- Mobile-responsive design
- Full accessibility support
- Keyboard navigation
- AJAX save/load

---

## Credits

Developed by the Nexus Theme Team  
© 2025 Nexus Pro - Advanced WordPress Theme

**License**: GPL-2.0+  
**Tier**: Advanced ($299/year) | Agency ($599/year)
