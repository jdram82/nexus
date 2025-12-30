#!/bin/bash
# Test License API on jdsandigitel.com
# Created: December 29, 2025

echo "🧪 Testing jdsandigitel.com License Server API"
echo "=============================================="
echo ""

# Test 1: Check if API is enabled
echo "Test 1: Checking if API endpoint is accessible..."
curl -s "https://jdsandigitel.com/wp-json/slm/v1/" | jq '.' 2>/dev/null || echo "❌ API not accessible or jq not installed"
echo ""

# Test 2: Check alternative endpoint
echo "Test 2: Checking WordPress REST API..."
curl -s "https://jdsandigitel.com/wp-json/" | jq '.routes | keys | .[]' | grep -i "slm\|license" || echo "ℹ️  No license endpoints found"
echo ""

# Instructions
echo "📋 Next Steps:"
echo "1. If you see JSON response with 'slm' endpoints → API is enabled ✅"
echo "2. If you see 404 or no response → Need to enable API in settings"
echo "3. If you see 'rest_no_route' → Plugin installed but API disabled"
echo ""
echo "🔍 To enable API:"
echo "   - Go to Settings → Software License Manager"
echo "   - Look for 'API Settings' or 'Advanced' tab"
echo "   - Check 'Enable REST API' or similar option"
