#!/bin/bash

echo "======================================"
echo "Nexus Theme - Setup Script"
echo "======================================"
echo ""

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js 14+ first."
    exit 1
fi

echo "✅ Node.js found: $(node --version)"

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "❌ npm is not installed. Please install npm first."
    exit 1
fi

echo "✅ npm found: $(npm --version)"
echo ""

# Install dependencies
echo "📦 Installing dependencies..."
npm install

if [ $? -ne 0 ]; then
    echo "❌ Failed to install dependencies."
    exit 1
fi

echo "✅ Dependencies installed successfully!"
echo ""

# Build assets
echo "🔨 Building assets..."
npm run build

if [ $? -ne 0 ]; then
    echo "❌ Failed to build assets."
    exit 1
fi

echo "✅ Assets built successfully!"
echo ""

# Create directories if they don't exist
mkdir -p assets/dist/css
mkdir -p assets/dist/js

echo "======================================"
echo "✅ Setup Complete!"
echo "======================================"
echo ""
echo "Next steps:"
echo "1. Upload this theme to your WordPress site"
echo "2. Activate the theme from Appearance > Themes"
echo "3. Customize colors and typography from Appearance > Customize"
echo ""
echo "Development commands:"
echo "  npm run watch  - Watch for changes and rebuild"
echo "  npm run build  - Production build"
echo "  npm run dev    - Development build"
echo ""
