#!/bin/bash

echo "======================================"
echo "Nexus Theme - Package Builder"
echo "======================================"
echo ""

# Set variables
THEME_NAME="nexus-theme"
VERSION="3.0.0"
OUTPUT_DIR="./dist"
OUTPUT_FILE="${THEME_NAME}-${VERSION}.zip"
TEMP_DIR="/tmp/${THEME_NAME}"

# Clean up previous builds
echo "🧹 Cleaning up previous builds..."
rm -rf "${OUTPUT_DIR}"
rm -rf "${TEMP_DIR}"
mkdir -p "${OUTPUT_DIR}"

# Create temporary directory
echo "📁 Creating temporary directory..."
mkdir -p "${TEMP_DIR}"

# Copy theme files (excluding dev files)
echo "📋 Copying theme files..."
rsync -av --progress . "${TEMP_DIR}/" \
  --exclude '.git' \
  --exclude 'node_modules' \
  --exclude 'dist' \
  --exclude '.gitignore' \
  --exclude '.gitattributes' \
  --exclude '*.log' \
  --exclude '.DS_Store' \
  --exclude 'package-theme.sh' \
  --exclude '.vscode' \
  --exclude 'assets/src' \
  --exclude 'webpack.config.js'

# Build assets if needed
if [ -d "node_modules" ]; then
    echo "🔨 Building production assets..."
    npm run build
    
    # Copy built assets to temp directory
    if [ -d "assets/dist" ]; then
        echo "📦 Copying built assets..."
        mkdir -p "${TEMP_DIR}/assets/dist"
        cp -r assets/dist/* "${TEMP_DIR}/assets/dist/"
    fi
fi

# Create zip file
echo "🗜️  Creating zip file..."
cd /tmp
zip -r "${THEME_NAME}-${VERSION}.zip" "${THEME_NAME}" -q

# Move to output directory
mv "${THEME_NAME}-${VERSION}.zip" "${OUTPUT_DIR}/" 2>/dev/null || \
    mv "${THEME_NAME}-${VERSION}.zip" "/workspaces/codespaces-blank/nexus-theme/${OUTPUT_DIR}/"

# Cleanup
echo "🧹 Cleaning up..."
rm -rf "${TEMP_DIR}"

# Success message
echo ""
echo "======================================"
echo "✅ Theme packaged successfully!"
echo "======================================"
echo ""
echo "📦 Package: ${OUTPUT_DIR}/${OUTPUT_FILE}"
echo "📊 Size: $(du -h "${OUTPUT_DIR}/${OUTPUT_FILE}" 2>/dev/null | cut -f1 || echo 'N/A')"
echo ""
echo "📥 To install:"
echo "   1. Go to WordPress Admin → Appearance → Themes"
echo "   2. Click 'Add New' → 'Upload Theme'"
echo "   3. Choose ${OUTPUT_FILE}"
echo "   4. Click 'Install Now'"
echo ""
