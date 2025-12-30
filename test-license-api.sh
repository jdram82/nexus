#!/bin/bash
# Quick Test Script for Nexus License API
# Run this AFTER installing the plugin on jdsandigitel.com

echo "🧪 Testing Nexus License API on jdsandigitel.com"
echo "=================================================="
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test 1: REST API Info
echo "Test 1: REST API Endpoint"
echo "-------------------------"
RESPONSE=$(curl -s "https://jdsandigitel.com/wp-json/nexus-licenses/v1/info")
if echo "$RESPONSE" | grep -q "Nexus License API"; then
    echo -e "${GREEN}✅ REST API is working!${NC}"
    echo "$RESPONSE" | jq '.' 2>/dev/null || echo "$RESPONSE"
else
    echo -e "${RED}❌ REST API not working${NC}"
    echo "Response: $RESPONSE"
fi
echo ""

# Test 2: Legacy API Info
echo "Test 2: Legacy API Endpoint (fallback)"
echo "---------------------------------------"
RESPONSE=$(curl -s "https://jdsandigitel.com/?nexus_api_action=info")
if echo "$RESPONSE" | grep -q "Nexus License API"; then
    echo -e "${GREEN}✅ Legacy API is working!${NC}"
    echo "$RESPONSE" | jq '.' 2>/dev/null || echo "$RESPONSE"
else
    echo -e "${YELLOW}⚠️  Legacy API response:${NC}"
    echo "$RESPONSE"
fi
echo ""

# Test 3: Test License Validation (requires a test license key)
echo "Test 3: License Validation Test"
echo "--------------------------------"
echo -e "${YELLOW}ℹ️  To test license validation, you need to:${NC}"
echo "1. Create a test license in WordPress Admin"
echo "2. Run this command with your test key:"
echo ""
echo "curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/activate \\"
echo "  -H 'Content-Type: application/json' \\"
echo "  -d '{\"license_key\":\"NEXUS-YOUR-KEY\",\"site_url\":\"https://testsite.com\"}'"
echo ""

# Summary
echo "=================================================="
echo "📋 Next Steps:"
echo "=================================================="
echo ""
echo "1. ✅ Install plugin on jdsandigitel.com"
echo "   - Upload nexus-license-api-plugin.zip to WordPress"
echo "   - Activate the plugin"
echo ""
echo "2. ✅ Create WooCommerce Products"
echo "   - Nexus Pro: \$199/year (SKU: nexus-pro)"
echo "   - Nexus Advanced: \$299/year (SKU: nexus-advanced)"
echo "   - Nexus Agency: \$599/year (SKU: nexus-agency)"
echo ""
echo "3. ✅ Test Purchase Flow"
echo "   - Buy a test product"
echo "   - Check license is generated"
echo "   - Test activation from customer site"
echo ""
echo "4. ✅ Update Nexus Theme"
echo "   - Change license_server URL to: https://jdsandigitel.com"
echo ""
