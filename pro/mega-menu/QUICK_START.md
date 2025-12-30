# Nexus Mega Menu - Quick Start Guide

## 🚀 Get Started in 5 Minutes

This guide will help you create your first mega menu in under 5 minutes.

---

## Step 1: Create a Menu (1 minute)

1. Go to **WordPress Admin > Appearance > Menus**
2. Click **create a new menu**
3. Give it a name (e.g., "Main Navigation")
4. Click **Create Menu**

---

## Step 2: Add Menu Items (1 minute)

Add some pages to your menu:

1. Check the boxes next to pages you want to include
2. Click **Add to Menu**
3. Drag to arrange them
4. Create hierarchy by dragging items to the right (sub-items)

**Example structure:**
```
Products (parent)
  ├─ Product Category 1
  ├─ Product Category 2
  ├─ Product Category 3
  └─ Product Category 4

Services (parent)
  ├─ Service A
  ├─ Service B
  └─ Service C
```

---

## Step 3: Enable Mega Menu (1 minute)

1. Click on the **parent menu item** (e.g., "Products")
2. Scroll down to find **"Nexus Mega Menu Settings"**
3. Check the box **"Enable Mega Menu"**
4. Select number of columns (e.g., **4 Columns**)
5. See the column preview update

✅ Your mega menu is now enabled!

---

## Step 4: Add Icons (Optional - 1 minute)

Make your menu items stand out with icons:

1. Click on any menu item
2. Find the **"Icon"** field
3. Click **"Choose Icon"** button
4. Select an icon from the picker (200+ icons!)
5. Click to select - icon appears immediately

**Popular icons:**
- 🏠 `dashicons-admin-home` - Home
- 📦 `dashicons-products` - Products  
- 🛒 `dashicons-cart` - Cart
- 📧 `dashicons-email` - Contact
- 📱 `dashicons-smartphone` - Mobile

---

## Step 5: Add Badges (Optional - 1 minute)

Highlight special menu items:

1. Click on a menu item
2. Find **"Badge Text"** field
3. Enter text (e.g., "New", "Hot", "Sale")
4. Pick a color with the color picker
5. Badge appears next to the menu text

**Pro tips:**
- Use "New" for recently added items
- Use "Hot" for trending items
- Use "Sale" for promotions
- Keep text short (2-4 characters)

---

## Step 6: Save & View (30 seconds)

1. Click **"Save Menu"** button
2. Assign menu to a location (e.g., Primary Menu)
3. Click **"Save Menu"** again
4. Visit your website to see the mega menu in action!

---

## 🎨 Customization Examples

### Example 1: Simple Product Mega Menu

```
Products (parent) [4 columns]
├─ Electronics (with icon: dashicons-smartphone)
├─ Clothing (with icon: dashicons-admin-appearance)
├─ Home & Garden (with icon: dashicons-admin-home)
└─ Sports (with icon: dashicons-awards)
```

**Settings:**
- Enable Mega Menu: ✓
- Columns: 4
- Icons: Yes
- Badges: Optional

### Example 2: Services with New Badge

```
Services (parent) [3 columns]
├─ Web Design (badge: "Hot", color: #e74c3c)
├─ SEO Services (badge: "New", color: #2ecc71)
└─ Consulting
```

**Settings:**
- Enable Mega Menu: ✓
- Columns: 3
- Badges: On featured items

### Example 3: Icon-Only Menu

```
Quick Links (parent)
├─ Cart (icon: dashicons-cart, hide text: ✓)
├─ Wishlist (icon: dashicons-heart, hide text: ✓)
└─ Account (icon: dashicons-admin-users, hide text: ✓)
```

**Settings:**
- Icons: Required
- Hide Text: ✓ (icon-only display)

---

## 🔧 Visual Menu Builder (Advanced)

For a more visual experience, use the **Menu Builder**:

### Access the Builder

1. Go to **Appearance > Menu Builder**
2. Select your menu from the dropdown
3. Use the visual interface

### Builder Features

**Left Panel - Menu Items**
- See all your menu items
- Drag to reorder
- Click to edit

**Center Panel - Canvas**
- Visual representation of menu structure
- Expand/collapse sections
- Live preview

**Right Panel - Settings**
- Click item to show settings
- Configure mega menu options
- Add icons and badges

**Save Button**
- Click to save changes
- Auto-save indicator shows status

---

## 📱 Mobile Behavior

Your mega menu automatically adapts to mobile devices:

- **Desktop**: Hover to reveal
- **Tablet**: Click to toggle
- **Mobile**: Tap to expand (accordion style)

No configuration needed - it just works!

---

## 🎯 Common Use Cases

### E-commerce Store

```
Shop [5 columns]
├─ New Arrivals (badge: "New")
├─ Best Sellers (badge: "Hot")
├─ On Sale (badge: "Sale")
├─ Men's
└─ Women's
```

### SaaS Website

```
Features [4 columns]
├─ Analytics (icon: dashicons-chart-line)
├─ Integrations (icon: dashicons-networking)
├─ Security (icon: dashicons-shield)
└─ Support (icon: dashicons-sos)
```

### Portfolio Site

```
Work [3 columns]
├─ Web Design
├─ Branding
└─ Photography
```

### Blog/Magazine

```
Categories [4 columns]
├─ Technology (icon: dashicons-laptop)
├─ Business (icon: dashicons-businessman)
├─ Lifestyle (icon: dashicons-palmtree)
└─ Travel (icon: dashicons-location)
```

---

## ⚡ Quick Tips

### Performance

✅ **DO:**
- Use 2-6 columns maximum
- Keep menu items under 20 per column
- Use icons sparingly (not on every item)

❌ **DON'T:**
- Create menus with 50+ items
- Enable mega menu on every parent item
- Use very large widget areas

### Design

✅ **DO:**
- Use consistent icon style
- Limit badge colors to 2-3
- Test on mobile devices
- Keep menu hierarchy simple

❌ **DON'T:**
- Mix too many icon styles
- Use more than 6 columns
- Create more than 3 levels deep
- Use bright/clashing badge colors

### Accessibility

✅ **DO:**
- Test keyboard navigation
- Ensure sufficient color contrast
- Provide text alternatives for icons
- Test with screen readers

❌ **DON'T:**
- Rely only on color for information
- Remove focus indicators
- Use icon-only without aria-labels

---

## 🔍 Troubleshooting

### "I don't see Mega Menu Settings"

**Solution:**
- Check that Nexus Pro (Advanced tier) is active
- Clear your browser cache
- Try a different menu item (must be top-level)

### "Mega menu doesn't appear on my site"

**Solution:**
1. Check menu is assigned to a location
2. Verify "Enable Mega Menu" is checked
3. Ensure menu has child items
4. Check theme supports menus
5. Look for JavaScript errors in console

### "Icons don't show"

**Solution:**
- Use correct format: `dashicons-icon-name`
- Check Dashicons are loaded
- Clear cache
- Try different browser

### "Mobile menu doesn't work"

**Solution:**
- Check JavaScript is enabled
- Verify no conflicting scripts
- Test in different mobile browser
- Check console for errors

---

## 📚 Next Steps

Now that you've created your first mega menu, explore advanced features:

1. **Add Widget Areas** - Include widgets in mega menus
2. **Customize CSS** - Match your brand colors
3. **Advanced Layouts** - Custom column widths
4. **Animations** - Add hover effects
5. **Background Images** - Visual mega menus

→ See full documentation: [README.md](README.md)

---

## 🆘 Need Help?

- **Video Tutorial**: [Watch on YouTube](#)
- **Documentation**: [Read Full Docs](README.md)
- **Support**: [Get Help](https://support.nexustheme.com)
- **Community**: [Join Forum](https://community.nexustheme.com)

---

## ⭐ Pro Tips from Experts

> **"Keep it simple"** - Don't overcomplicate your mega menu. Users should find what they need in 2 clicks max.

> **"Mobile first"** - Always test on mobile devices. 60% of users are on mobile.

> **"Use badges wisely"** - Badges lose impact if overused. Reserve for truly special items.

> **"Test navigation"** - Have someone unfamiliar with your site try to navigate. Watch where they struggle.

> **"Performance matters"** - Large mega menus can slow down your site. Keep them lean.

---

**Happy Menu Building! 🎉**

Got questions? [Contact Support](https://support.nexustheme.com)
