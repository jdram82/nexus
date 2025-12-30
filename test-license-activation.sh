#!/bin/bash
# Test license activation from customer perspective

echo "🧪 Testing License Activation on jdsandigitel.com"
echo "=================================================="
echo ""

# You'll need to replace this with your actual license key
LICENSE_KEY="YOUR-LICENSE-KEY-HERE"
SITE_URL="https://testsite.com"

echo "Testing with:"
echo "License Key: $LICENSE_KEY"
echo "Site URL: $SITE_URL"
echo ""

# Test 1: Check if API is accessible
echo "Test 1: Checking API endpoint..."
curl -s "https://jdsandigitel.com/wp-json/nexus-licenses/v1/info" | jq '.' || echo "API not responding"
echo ""

# Test 2: Try activation (you need to provide license key)
echo "Test 2: Testing license activation..."
echo "Run this command with your actual license key:"
echo ""
echo "curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/activate \\"
echo "  -H 'Content-Type: application/json' \\"
echo "  -d '{\"license_key\":\"NEXUS-YOUR-KEY\",\"site_url\":\"https://testsite.com\"}'"
echo ""
