#!/bin/bash
# Test specific license key activation

LICENSE_KEY="NEXUS-69541540c5b25"
SITE_URL="https://testsite.com"

echo "🔑 Testing License Key: $LICENSE_KEY"
echo "=================================================="
echo ""

# Test 1: Check API Info
echo "Test 1: Checking API status..."
curl -s "https://jdsandigitel.com/wp-json/nexus-licenses/v1/info"
echo ""
echo ""

# Test 2: Activate License
echo "Test 2: Activating license..."
curl -X POST "https://jdsandigitel.com/wp-json/nexus-licenses/v1/activate" \
  -H "Content-Type: application/json" \
  -d "{\"license_key\":\"$LICENSE_KEY\",\"site_url\":\"$SITE_URL\"}"
echo ""
echo ""

# Test 3: Validate License
echo "Test 3: Validating license..."
curl -X POST "https://jdsandigitel.com/wp-json/nexus-licenses/v1/validate" \
  -H "Content-Type: application/json" \
  -d "{\"license_key\":\"$LICENSE_KEY\",\"site_url\":\"$SITE_URL\"}"
echo ""
echo ""

echo "✅ Test complete!"
