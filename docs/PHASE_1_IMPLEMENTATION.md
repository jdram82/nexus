# Nexus Phase 1 Implementation Guide

## Overview

Phase 1 introduces foundational features that eliminate competitive gaps and establish Nexus as a developer-friendly, extensible WordPress theme. Features are split into **Pro** and **Advanced** tiers to create clear value differentiation.

---

## Feature Breakdown by Tier

### Pro Tier ($199/year)
Core improvements that benefit all professional users:

#### 1. Plugin Harmony Architecture
**File:** `inc/class-nexus-plugin-harmony.php`

**What it does:**
- Automatically detects 50+ popular WordPress plugins
- Gracefully deactivates conflicting Nexus features
- Applies basic styling compatibility
- Shows admin notices about detected integrations

**Example:**
```php
// If Gravity Forms is detected, Nexus Form Builder is automatically disabled
// Basic Nexus styling is applied to Gravity Forms
```

**Admin UI:**
- Harmony status shown in notices
- Link to Integration Dashboard (Advanced tier feature)

#### 2. REST API
**File:** `inc/api/class-nexus-rest-api.php`

**What it does:**
- Exposes theme settings via REST endpoints
- CRUD operations for templates
- Custom post types API
- Performance metrics endpoint
- API key authentication

**Example Endpoints:**
```
GET  /wp-json/nexus/v1/settings
POST /wp-json/nexus/v1/templates
GET  /wp-json/nexus/v1/projects
GET  /wp-json/nexus/v1/harmony
```

**Use Cases:**
- Headless WordPress setups
- Mobile app integrations
- Third-party tool connections
- Automated deployments

---

### Advanced Tier ($299/year)
Power user features for agencies and advanced developers:

#### 1. Plugin Orchestrator (Deep Integrations)
**File:** `pro/plugin-orchestrator/class-plugin-orchestrator.php`

**What it does:**
- Deep integration with specific plugins (Gravity Forms, Rank Math, WPForms, etc.)
- Auto-styling injection using Nexus design tokens
- Integration testing tools
- Visual integration dashboard

**Example:**
```php
// Gravity Forms gets automatic Nexus styling
// {{primary-color}} tokens replaced with actual theme colors
// All forms match Nexus design system perfectly
```

**Admin UI:**
- Beautiful integration dashboard showing all active integrations
- Test integration button for each plugin
- Statistics (active integrations, style injections, etc.)

#### 2. Dynamic Loop Builder
**File:** `pro/loop-builder/class-loop-builder.php`

**What it does:**
- Visual query builder (no code required)
- Template designer with drag-and-drop elements
- Live preview with real data
- Supports all post types
- Advanced filtering (taxonomy, meta queries)
- Multiple layout types (grid, masonry, list, carousel)

**Example:**
```
Query: "Show products where price > $100 AND category = Software"
Template: Grid layout, 3 columns, show image + title + price + button
Output: [nexus_loop id="123"]
```

**Admin UI:**
- Three-panel interface: Query Builder | Template Designer | Live Preview
- Saved loops library with shortcodes
- Responsive preview (desktop, tablet, mobile)

---

## License Tier Management

**File:** `pro/admin/class-license-manager.php`

### Tier Structure:
- **Free:** Basic theme features
- **Pro:** Plugin Harmony + REST API
- **Advanced:** Plugin Orchestrator + Loop Builder
- **Agency:** White-label + Multi-site management (Phase 3)

### Feature Access Control:
```php
// Check if feature is available
if ( Nexus_License_Manager::get_instance()->has_feature( 'loop_builder' ) ) {
    // Load Loop Builder
}
```

### Admin License Page:
- License activation/deactivation
- Tier comparison table
- Upgrade CTAs
- Expiration warnings

---

## Installation & Activation

### For Development/Testing:

1. **Activate Pro Tier License:**
   ```
   License Key: PRO-XXXX-XXXX-XXXX
   ```

2. **Activate Advanced Tier License:**
   ```
   License Key: ADV-XXXX-XXXX-XXXX
   ```

3. **Activate Agency Tier License:**
   ```
   License Key: AGE-XXXX-XXXX-XXXX
   ```

### Feature Availability After Activation:

**Pro Tier:**
- Plugin Harmony runs automatically
- REST API active at `/wp-json/nexus/v1/`
- Admin notice shows detected plugins

**Advanced Tier:**
- All Pro features +
- Integration Dashboard: `Appearance → Integrations`
- Loop Builder: `Appearance → Loop Builder`

---

## File Structure

```
nexus-theme/
├── inc/
│   ├── class-nexus-plugin-harmony.php (Pro)
│   └── api/
│       └── class-nexus-rest-api.php (Pro)
└── pro/
    ├── plugin-orchestrator/
    │   ├── class-plugin-orchestrator.php (Advanced)
    │   └── integrations/
    │       ├── class-gravity-forms-integration.php
    │       ├── class-rank-math-integration.php
    │       └── class-wpforms-integration.php
    ├── loop-builder/
    │   └── class-loop-builder.php (Advanced)
    ├── assets/
    │   ├── css/
    │   │   └── loop-builder.css
    │   └── js/
    │       └── loop-builder.js
    └── admin/
        └── class-license-manager.php
```

---

## Usage Examples

### 1. Plugin Harmony (Automatic)

When WPForms is installed:
```
✓ WPForms detected
✓ Nexus Form Builder deactivated
✓ WPForms styled with Nexus design system
✓ No conflicts, seamless integration
```

### 2. REST API Usage

Get theme settings:
```bash
curl https://yoursite.com/wp-json/nexus/v1/settings \
  -H "X-Nexus-API-Key: your-api-key"
```

Response:
```json
{
  "colors": {
    "primary": "#0066cc",
    "secondary": "#333333"
  },
  "typography": {
    "base_font": "Inter, sans-serif"
  }
}
```

### 3. Loop Builder Usage

Create a loop visually, then use shortcode:
```
[nexus_loop id="123"]
```

Or in theme template:
```php
<?php echo do_shortcode( '[nexus_loop id="123"]' ); ?>
```

---

## Admin Menu Structure

```
Appearance
├── Nexus Options
├── Integrations (Advanced tier)
├── Loop Builder (Advanced tier)
└── License
```

---

## Testing Phase 1 Features

### Test Plugin Harmony:
1. Install WPForms or Gravity Forms
2. Check admin notice confirming detection
3. Verify Nexus Form Builder is hidden
4. Check that forms match Nexus styling

### Test REST API:
1. Visit `/wp-json/nexus/v1/health`
2. Should see `{"status":"ok"}`
3. Generate API key in License page (future feature)
4. Test protected endpoints

### Test Plugin Orchestrator (Advanced):
1. Activate Advanced tier license
2. Go to `Appearance → Integrations`
3. See detected plugins with integration cards
4. Click "Test Integration" on any card
5. Verify styling applied to external plugin

### Test Loop Builder (Advanced):
1. Activate Advanced tier license
2. Go to `Appearance → Loop Builder`
3. Click "Create New Loop"
4. Build a query (e.g., show latest posts)
5. Design template (add image, title, excerpt)
6. Watch live preview update
7. Save and copy shortcode
8. Use shortcode on a page

---

## Next Steps (Phase 2)

Once Phase 1 is tested and validated:

1. **Template Marketplace** - Community-driven template library
2. **Pixel-Perfect Mode** - Advanced layout system
3. **AI Features** - Layout generation and content suggestions
4. **Performance Monitoring** - Real-time metrics dashboard

---

## Support & Documentation

- **API Documentation:** Auto-generated from PHPDoc comments
- **Developer Portal:** Coming in Phase 1.5
- **Community Forum:** For feature requests and support
- **Video Tutorials:** Planned for each major feature

---

## Changelog

**Version 1.4.0** (Phase 1 Release)
- Added Plugin Harmony Architecture (Pro)
- Added REST API with authentication (Pro)
- Added Plugin Orchestrator with deep integrations (Advanced)
- Added Dynamic Loop Builder (Advanced)
- Added tiered licensing system
- Updated admin UI with tier badges

---

*This implementation establishes Nexus as a truly extensible, developer-friendly theme while maintaining performance and user experience excellence.*
