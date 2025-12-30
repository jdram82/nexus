# Nexus Theme Changelog

All notable changes to Nexus Theme will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.8.1] - 2025-12-27

### Fixed
- Fixed update notification loop - "Update Now" button no longer reappears after successful update
- Improved GitHub zipball folder renaming for better WordPress compatibility
- Added automatic cache clearing after successful updates
- Enhanced theme detection in WordPress upgrader hook
- Update system now properly recognizes installed version

### Technical
- Improved `fix_theme_folder_name()` with better theme detection logic
- Added `clear_cache_after_update()` method for post-update cleanup
- Proper transient cache clearing on `upgrader_process_complete` hook
- Better handling of folder structure edge cases

## [1.8.0] - 2025-12-27

### Changed
- **Major Rebranding: Nexus by Jdsan Digitel**
- Theme Author: JDRAM → Jdsan Digitel
- Author URI: https://jdsandigitel.com
- Theme URI: https://jdsandigitel.com/nexus
- Copyright updated to "Copyright (C) 2025 Jdsan Digitel"
- Footer displays "Nexus by Jdsan Digitel"
- All admin page titles updated with company branding

## [1.7.0] - 2025-12-27

### Added
- **Automatic GitHub-based update system**
- GitHub Personal Access Token support (5000 req/hr vs 60 req/hr)
- Automatic update checking every 12 hours
- One-click updates from WordPress admin
- Update notifications in Appearance → Themes
- New "Updates" admin page under Appearance
- Manual "Check for Updates Now" functionality
- GitHub zipball automatic folder renaming
- Settings preservation during updates
- Comprehensive diagnostic tools

### Technical
- Created `inc/class-nexus-theme-updater.php` (518 lines)
- GitHub Releases API integration
- 12-hour transient caching for API efficiency
- NEXUS_GITHUB_TOKEN constant support
- WordPress hooks: `pre_set_site_transient_update_themes`, `upgrader_source_selection`

### Documentation
- Created UPDATE_SYSTEM_GUIDE.md
- Created DEPLOYMENT_GUIDE.md
- Created diagnostic plugin tools
- Update configuration guides
- Quick reference documentation

## [Unreleased]

### Added
- Automatic theme updates via GitHub releases
- Update notification system in WordPress admin
- One-click theme updates from admin dashboard
- Manual update instructions for advanced users
- Update settings page (Appearance → Updates)

## [1.6.0] - 2024-01-XX

### Added
- **GitHub-based automatic updates** - No more manual reinstallation required
- Update checker that runs every 12 hours
- Admin notifications when updates are available
- Dedicated Updates page in Appearance menu
- Release notes integration from GitHub
- Version comparison system
- Update cache management
- AJAX update checking
- Theme folder name fixer for GitHub downloads

### Changed
- Improved version tracking across all files
- Enhanced admin UI for update management
- Better error handling in update process

### Developer Notes
- New class: `Nexus_Theme_Updater` in `inc/class-nexus-theme-updater.php`
- Hooks into WordPress `pre_set_site_transient_update_themes` filter
- Uses GitHub API: `https://api.github.com/repos/jdram82/nexus/releases/latest`
- Update cache stored in transient: `nexus_theme_update_check` (12-hour TTL)

## [1.5.0] - 2024-01-XX

### Added
- **Pro Tier Features (100% Complete)**
  - DigitalOcean Spaces cloud storage integration
  - Multi-gateway payment system (Razorpay + PayPal)
  - Template cloud sync with auto-backup
  - Credit system with multiple topup tiers
  - Database schema for all tiers
  
- **Block Patterns & Starter Templates**
  - 5 professional WordPress block patterns
  - Hero sections with CTA
  - Feature showcases
  - Testimonials layout
  - Pricing tables
  - Contact forms with map integration

### Changed
- Refactored Pro features into modular components
- Improved cloud storage performance
- Enhanced payment gateway error handling
- Better database schema management

### Fixed
- PHP syntax error in `pro/class-nexus-pro.php`
- Missing file checks with `file_exists()`
- WooCommerce integration compatibility

## [1.4.0] - 2024-01-XX

### Added
- Advanced Tier Features (55% Complete)
  - Theme Builder with drag-drop interface
  - SEO Manager with meta optimization
  - Performance Monitor dashboard
  - Schema Markup generator
  - Advanced filtering system
  - Form builder with conditional logic

### In Progress
- AI features (currently mock implementations)
  - AI Template Generator
  - AI Documentation Generator
  - Needs OpenAI API integration

## [1.3.0] - 2024-01-XX

### Added
- Agency Tier Features (25% Complete)
  - White Label system
  - Agency Dashboard
  - Client Portal (basic)
  
### Planned
- A/B Testing Engine
- Advanced Analytics
- Multi-site management
- Client reporting

## [1.2.0] - 2024-01-XX

### Added
- REST API endpoints (`/wp-json/nexus/v1/*`)
- Plugin Harmony system
- Custom post types (Products, Projects, Downloads)
- WooCommerce integration
- Customizer controls

## [1.1.0] - 2024-01-XX

### Added
- Theme customizer
- Basic styling options
- Layout controls
- Color schemes

## [1.0.0] - 2024-01-XX

### Added
- Initial theme release
- Basic WordPress theme structure
- Free tier features
- Responsive design
- Accessibility features

---

## Update Instructions

### Automatic Updates (Recommended)

1. Navigate to **Appearance → Updates** in WordPress admin
2. Click **"Check for Updates Now"**
3. If update available, click **"Update Now"**
4. Wait for update to complete
5. Review release notes

### Manual Updates (If Automatic Fails)

1. **Backup First**
   ```bash
   # Backup theme folder
   cp -r wp-content/themes/nexus-theme wp-content/themes/nexus-theme-backup
   
   # Export database
   wp db export backup.sql
   ```

2. **Download Latest Release**
   - Visit: https://github.com/jdram82/nexus/releases/latest
   - Download: `nexus-theme-x.x.x.zip`

3. **Upload via WordPress Admin**
   - Go to Appearance → Themes
   - Click "Add New" → "Upload Theme"
   - Choose downloaded zip file
   - Click "Install Now"
   - Activate theme

4. **Or Upload via FTP/SSH**
   ```bash
   # Delete old theme
   rm -rf wp-content/themes/nexus-theme
   
   # Upload new theme
   unzip nexus-theme-1.6.0.zip -d wp-content/themes/
   
   # Set permissions
   chown -R www-data:www-data wp-content/themes/nexus-theme
   chmod -R 755 wp-content/themes/nexus-theme
   ```

5. **Clear Caches**
   ```bash
   # Clear WordPress cache
   wp cache flush
   
   # Clear object cache (if using Redis/Memcached)
   wp cache flush --redis
   ```

### Settings Preservation

Your settings are **automatically preserved** during updates:
- ✅ Theme options (stored in database)
- ✅ Customizer settings
- ✅ Widget configurations
- ✅ Menu assignments
- ✅ Uploaded content
- ✅ Pro tier license keys
- ✅ Payment gateway settings
- ✅ Cloud storage credentials

Only theme **files** are updated, not your **data**.

### Child Theme Users

If you use a child theme for customizations:
- ✅ Your customizations are safe
- ✅ Child theme files are untouched
- ✅ Only parent theme is updated

### Troubleshooting Updates

**Update Check Fails**
```bash
# Clear update cache
wp transient delete nexus_theme_update_check

# Check GitHub connectivity
curl -I https://api.github.com/repos/jdram82/nexus/releases/latest
```

**Update Downloads But Won't Install**
- Check file permissions: `755` for folders, `644` for files
- Check available disk space: `df -h`
- Check PHP memory limit: `php -i | grep memory_limit`
- Increase in `wp-config.php`: `define('WP_MEMORY_LIMIT', '256M');`

**Theme Breaks After Update**
```bash
# Roll back to previous version
cd wp-content/themes/
rm -rf nexus-theme
mv nexus-theme-backup nexus-theme

# Or restore from database backup
wp db import backup.sql
```

**Still Having Issues?**
- GitHub Issues: https://github.com/jdram82/nexus/issues
- Documentation: https://github.com/jdram82/nexus/wiki
- Check compatibility: WordPress 6.0+, PHP 7.4+

---

## Version Numbering

Nexus uses [Semantic Versioning](https://semver.org/):

- **Major version** (X.0.0): Breaking changes, major rewrites
- **Minor version** (1.X.0): New features, backward compatible
- **Patch version** (1.5.X): Bug fixes, security patches

Examples:
- `1.5.0 → 1.6.0`: New features added (automatic updates)
- `1.6.0 → 1.6.1`: Bug fixes only
- `1.6.0 → 2.0.0`: Major changes (may require migration)

## Release Schedule

- **Security patches**: Released immediately when needed
- **Bug fixes**: Released as needed (usually weekly)
- **Feature updates**: Released monthly
- **Major versions**: Released quarterly

## Beta Testing

Want early access to new features?

```bash
# Switch to beta channel (coming soon)
wp theme update nexus-theme --version=beta
```

---

## Credits

**Developed by:** JDRAM  
**Repository:** https://github.com/jdram82/nexus  
**License:** GPL v2 or later  
**WordPress:** Requires 6.0+  
**PHP:** Requires 7.4+
