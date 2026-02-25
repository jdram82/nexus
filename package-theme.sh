#!/bin/bash

echo "======================================"
echo "Nexus Theme v3.2.1 - Package Builder"
echo "======================================"
echo ""

# Set variables
THEME_NAME="nexus"
VERSION="3.2.1"
CURRENT_DIR="$(pwd)"
# Output to workspace releases folder for easy access
WORKSPACE_ROOT="$(dirname "${CURRENT_DIR}")"
OUTPUT_DIR="${WORKSPACE_ROOT}/releases"
OUTPUT_FILE="nexus-${VERSION}.zip"
TEMP_DIR="/tmp/${THEME_NAME}"

# Clean up previous builds
echo "🧹 Cleaning up previous builds..."
# Don't delete entire releases folder, just old temp files
rm -rf "${TEMP_DIR}"
# Create releases folder if it doesn't exist
mkdir -p "${OUTPUT_DIR}"
echo "📁 Releases folder: ${OUTPUT_DIR}"

# Create temporary directory
echo "📁 Creating temporary directory..."
mkdir -p "${TEMP_DIR}"

# Copy theme files (excluding dev/build files)
echo "📋 Copying theme files..."
rsync -av --progress "${CURRENT_DIR}/" "${TEMP_DIR}/" \
  --exclude '.git' \
  --exclude '.github' \
  --exclude 'node_modules' \
  --exclude 'dist' \
  --exclude '.gitignore' \
  --exclude '.gitattributes' \
  --exclude '*.log' \
  --exclude '.DS_Store' \
  --exclude 'package-theme.sh' \
  --exclude '.vscode' \
  --exclude 'assets/src' \
  --exclude 'webpack.config.js' \
  --exclude 'package.json' \
  --exclude 'package-lock.json' \
  --exclude '*.md' \
  --exclude 'PHASE_*.md' \
  --exclude 'SESSION_*.md' \
  --exclude '*_TESTS.md' \
  --exclude 'COMPETITIVE_*.md' \
  --exclude 'EASYWP_*.md' \
  --exclude 'UPDATE_*.md' \
  --exclude 'TESTING_*.md' \
  --exclude 'DEPLOYMENT_*.md' \
  --exclude 'DEVELOPMENT.md' \
  --exclude 'CUSTOMIZATION_*.md' \
  --exclude 'SCREENSHOT_*.md' \
  --exclude 'THEME_*.md' \
  --exclude 'WORDPRESS_*.md' \
  --exclude 'HOSTING_*.md' \
  --exclude 'COMPREHENSIVE_*.md' \
  --exclude 'LICENSE_*.md' \
  --exclude 'FIX_*.md' \
  --exclude 'QUICK_*.md' \
  --exclude 'PREMIUM_*.md' \
  --exclude 'PRO_*.md' \
  --exclude 'ADVANCED_*.md' \
  --exclude 'AGENCY_*.md' \
  --exclude 'CHANGELOG.md'

# Keep only essential documentation
echo "📝 Copying essential documentation..."
cp "${CURRENT_DIR}/README.md" "${TEMP_DIR}/" 2>/dev/null || true
cp "${CURRENT_DIR}/LICENSE" "${TEMP_DIR}/" 2>/dev/null || true

# Create a production-ready README
cat > "${TEMP_DIR}/README.md" << 'READMEEOF'
# Nexus Theme v3.2.0

Professional WordPress theme for technical businesses with multi-tier licensing.

## Features

### FREE Tier
- Core WordPress theme
- WooCommerce integration
- Responsive design
- Custom post types

### PRO Tier ($199/year)
- Cloud storage integration
- Template sync system
- Payment gateway (Stripe, Razorpay, PayPal)
- Credits system

### ADVANCED Tier ($299/year)
- Theme builder (headers/footers)
- Advanced controls
- Mega menu builder
- Template manager (100+ templates)
- API documentation generator
- Circuit simulator
- Performance analytics
- Plugin orchestrator
- Loop builder
- SEO manager
- Advanced filtering
- Form builder

### AGENCY Tier ($599/year)
- A/B testing system
- White label + export
- Agency dashboard
- Client portal
- Unlimited site licenses

## Installation

1. Upload `nexus-3.0.0.zip` via WordPress Admin → Appearance → Themes → Add New
2. Activate the theme
3. Go to Nexus Options → License to activate your license key
4. Enjoy premium features based on your tier!

## Requirements

- WordPress 6.0+
- PHP 8.0+
- MySQL 5.7+

## License

GPL v2 or later. Code is open source, premium features require valid license.

## Support

- Documentation: https://jdsandigitel.com/nexus/docs
- Support: https://jdsandigitel.com/support
- Purchase: https://jdsandigitel.com/nexus

---

**Nexus Theme** © 2025 Jdsan Digitel. All rights reserved.
READMEEOF

# Create changelog for users
cat > "${TEMP_DIR}/CHANGELOG.txt" << 'CHANGELOGEOF'
=== Nexus Theme Changelog ===

= 3.0.0 - December 28, 2025 =
* Major release: Phase 3 complete
* NEW: Theme Builder - Visual header/footer builder
* NEW: Advanced Controls - Pixel-perfect customization
* NEW: Mega Menu - Multi-column menus with widgets
* NEW: Template Manager - 100+ professional templates
* NEW: API Docs Generator - Automatic REST API documentation
* NEW: Circuit Simulator - Electronic circuit designer
* NEW: Performance Analytics - Page speed & optimization
* NEW: A/B Testing - Statistical split testing (Agency tier)
* IMPROVED: License protection with tier-based gating
* IMPROVED: Security hardening across all features
* FIXED: All premium features now properly license-gated

= 1.8.1 - Previous Version =
* Pro tier features
* Cloud storage
* Payment gateway integration
* Template sync
* Credits system

---

For complete changelog: https://jdsandigitel.com/nexus/changelog
CHANGELOGEOF

# Create .distignore for build files (if using GitHub Actions)
cat > "${TEMP_DIR}/.distignore" << 'DISTIGNORE'
.git
.github
node_modules
.DS_Store
.distignore
.gitignore
*.log
package-theme.sh
webpack.config.js
package.json
package-lock.json
DISTIGNORE

# Count files and size
echo ""
echo "📊 Package Statistics:"
FILE_COUNT=$(find "${TEMP_DIR}" -type f | wc -l)
echo "   Files: ${FILE_COUNT}"

# Create zip file
echo ""
echo "🗜️  Creating zip file..."
cd /tmp
zip -r "${OUTPUT_FILE}" "${THEME_NAME}" -q -9

# Move to output directory
echo "📦 Moving package to output directory..."
mv "${OUTPUT_FILE}" "${OUTPUT_DIR}/"

# Get final size
FINAL_SIZE=$(du -h "${OUTPUT_DIR}/${OUTPUT_FILE}" | cut -f1)

# Cleanup
echo "🧹 Cleaning up temporary files..."
rm -rf "${TEMP_DIR}"

# Calculate SHA256 checksum
echo "🔐 Calculating checksum..."
cd "${OUTPUT_DIR}"
sha256sum "${OUTPUT_FILE}" > "${OUTPUT_FILE}.sha256"

# Success message
echo ""
echo "======================================"
echo "✅ Theme packaged successfully!"
echo "======================================"
echo ""
echo "📦 Package: ${OUTPUT_DIR}/${OUTPUT_FILE}"
echo "📊 Size: ${FINAL_SIZE}"
echo "🔐 Checksum: ${OUTPUT_FILE}.sha256"
echo "📝 Files included: ${FILE_COUNT}"
echo ""
echo "📥 Installation Instructions:"
echo "   1. Go to WordPress Admin → Appearance → Themes"
echo "   2. Click 'Add New' → 'Upload Theme'"
echo "   3. Choose ${OUTPUT_FILE}"
echo "   4. Click 'Install Now'"
echo "   5. Activate and enter license key"
echo ""
echo "🚀 Ready for:"
echo "   ✅ jdsandigitel.com (fresh install)"
echo "   ✅ jdsancontrols.com (update from v1.8.1)"
echo ""
