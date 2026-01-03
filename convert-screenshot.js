#!/usr/bin/env node

/**
 * Convert screenshot.svg to screenshot.png
 * Uses canvas to render SVG
 */

const fs = require('fs');
const { createCanvas, loadImage } = require('canvas');

async function convertSvgToPng() {
  try {
    // Read SVG file
    const svgBuffer = fs.readFileSync('screenshot.svg');
    const svgString = svgBuffer.toString('utf-8');
    
    // Create data URL
    const svgDataUrl = 'data:image/svg+xml;base64,' + Buffer.from(svgString).toString('base64');
    
    // Create canvas
    const canvas = createCanvas(1200, 900);
    const ctx = canvas.getContext('2d');
    
    // Load and draw image
    const img = await loadImage(svgDataUrl);
    ctx.drawImage(img, 0, 0, 1200, 900);
    
    // Save as PNG
    const buffer = canvas.toBuffer('image/png');
    fs.writeFileSync('screenshot.png', buffer);
    
    console.log('✅ Created screenshot.png (1200x900)');
    console.log('📊 File size:', Math.round(buffer.length / 1024) + 'KB');
  } catch (error) {
    console.error('❌ Error:', error.message);
    console.log('\n💡 Alternative: Use an online converter or install canvas:');
    console.log('   npm install canvas');
  }
}

convertSvgToPng();
