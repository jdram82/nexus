# 🎯 QUICK REFERENCE - License Server Setup

## Files You Need

📦 **nexus-license-api-plugin.zip** (6.4 KB)
- Location: `/workspaces/codespaces-blank/nexus-theme/`
- Action: Upload to WordPress on jdsandigitel.com

## Installation Steps

### 1️⃣ Install Plugin (2 minutes)
```
WordPress Admin → Plugins → Add New → Upload Plugin
→ Choose nexus-license-api-plugin.zip → Install → Activate
```

### 2️⃣ Verify It Works (30 seconds)
```
Visit: https://jdsandigitel.com/wp-json/nexus-licenses/v1/info
OR: https://jdsandigitel.com/?nexus_api_action=info
```

### 3️⃣ Create Products (10 minutes each)
| Product | Price | SKU | License Type | Max Domains |
|---------|-------|-----|--------------|-------------|
| Nexus Pro | $199 | nexus-pro | pro | 1 |
| Nexus Advanced | $299 | nexus-advanced | advanced | 3 |
| Nexus Agency | $599 | nexus-agency | agency | 999 |

**For each product:**
- ✅ Virtual
- ✅ Downloadable
- ✅ Software License Manager: "Create License" = Yes

### 4️⃣ Setup Payments (15 minutes)
**Stripe (recommended):**
- Get test keys from stripe.com
- WooCommerce → Settings → Payments → Stripe
- Add test keys first!

### 5️⃣ Test Purchase (5 minutes)
- Buy with test card: 4242 4242 4242 4242
- Check license auto-created
- Copy license key from email

### 6️⃣ Test Activation
```bash
curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/activate \
  -H "Content-Type: application/json" \
  -d '{"license_key":"NEXUS-YOUR-KEY","site_url":"https://testsite.com"}'
```

## API Endpoints

### Main Endpoint
```
https://jdsandigitel.com/wp-json/nexus-licenses/v1/
```

### All Endpoints
- `/info` - Server info
- `/activate` - Activate license
- `/validate` - Check license status
- `/deactivate` - Deactivate license
- `/check-update` - Check for theme updates

### Fallback (if REST API blocked)
```
https://jdsandigitel.com/?nexus_api_action=info
https://jdsandigitel.com/?nexus_api_action=activate
```

## Secret Keys (Already Configured)

✅ Creation Key: `6951ee21aaed19.50210993`  
✅ Verification Key: `6951ee21aaed88.33650597`  
✅ Prefix: `NEXUS-`

## Test Commands

### Check Server
```bash
curl https://jdsandigitel.com/wp-json/nexus-licenses/v1/info
```

### Activate License
```bash
curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/activate \
  -d "license_key=NEXUS-XXXX&site_url=https://testsite.com"
```

### Validate License
```bash
curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/validate \
  -d "license_key=NEXUS-XXXX&site_url=https://testsite.com"
```

## Expected Responses

### Successful Activation
```json
{
  "success": true,
  "message": "License activated successfully",
  "tier": "pro",
  "expires": "2026-12-29",
  "max_domains": 1,
  "active_domains": 1
}
```

### Successful Validation
```json
{
  "success": true,
  "status": "active",
  "tier": "pro",
  "expires": "2026-12-29",
  "max_domains": 1
}
```

## Troubleshooting

| Issue | Solution |
|-------|----------|
| 404 Error | Try legacy endpoint with `?nexus_api_action=info` |
| Blank page | Check plugin is activated |
| No license created | Verify "Create License" enabled in product |
| Payment fails | Check using live keys, verify SSL |
| Features don't unlock | Reactivate license, check tier |

## Revenue Model

- **Pro:** $199/year × customers = Revenue
- **Advanced:** $299/year × customers = Revenue  
- **Agency:** $599/year × customers = Revenue
- **You keep 100%** - No commissions!

## Support

📖 Full Guide: `JDSANDIGITEL_LICENSE_SERVER_SETUP.md`  
📖 Plugin Docs: `nexus-license-api-plugin/README.md`  
📖 Install Guide: `nexus-license-api-plugin/INSTALLATION.md`

---

**Time to Complete:** ~2 hours  
**Monthly Cost:** $10-30 (hosting only)  
**You're Ready!** 🚀
