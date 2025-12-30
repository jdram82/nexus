# Nexus License API Plugin

Custom license validation API for Nexus Theme on jdsandigitel.com

## Installation

1. **Upload to WordPress:**
   - Zip this folder: `nexus-license-api-plugin`
   - Go to WordPress Admin → Plugins → Add New
   - Click "Upload Plugin"
   - Choose the ZIP file
   - Click "Install Now"
   - Click "Activate"

2. **Or via FTP/SSH:**
   ```bash
   # Upload the entire folder to:
   /wp-content/plugins/nexus-license-api-plugin/
   
   # Then activate in WordPress admin
   ```

## Features

✅ Works with Software License Manager database  
✅ Provides REST API endpoints (if enabled)  
✅ Provides legacy endpoints (if REST API is blocked)  
✅ Handles license activation, validation, deactivation  
✅ Automatic tier detection (Free, Pro, Advanced, Agency)  
✅ Domain limit enforcement  
✅ Expiration checking  
✅ GitHub update integration  

## API Endpoints

### REST API (if WordPress REST API is enabled)

```
GET  https://jdsandigitel.com/wp-json/nexus-licenses/v1/info
POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/activate
POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/validate
POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/deactivate
POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/check-update
```

### Legacy Endpoints (if REST API is blocked)

```
GET  https://jdsandigitel.com/?nexus_api_action=info
POST https://jdsandigitel.com/?nexus_api_action=activate
POST https://jdsandigitel.com/?nexus_api_action=validate
POST https://jdsandigitel.com/?nexus_api_action=deactivate
POST https://jdsandigitel.com/?nexus_api_action=check_update
```

## Testing

### Test 1: Check API Info
```bash
curl https://jdsandigitel.com/wp-json/nexus-licenses/v1/info

# OR if REST API is blocked:
curl https://jdsandigitel.com/?nexus_api_action=info
```

### Test 2: Activate License
```bash
curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/activate \
  -H "Content-Type: application/json" \
  -d '{"license_key":"NEXUS-XXXX-XXXX","site_url":"https://testsite.com"}'
```

### Test 3: Validate License
```bash
curl -X POST https://jdsandigitel.com/wp-json/nexus-licenses/v1/validate \
  -H "Content-Type: application/json" \
  -d '{"license_key":"NEXUS-XXXX-XXXX","site_url":"https://testsite.com"}'
```

## Configuration

The plugin uses your existing Software License Manager settings:
- Secret Key for Creation: `6951ee21aaed19.50210993`
- Secret Key for Verification: `6951ee21aaed88.33650597`
- License Prefix: `NEXUS-`

## How It Works

1. **License Activation:**
   - Checks if license exists in database
   - Verifies not expired
   - Checks domain limit
   - Registers domain
   - Returns tier and expiration

2. **License Validation:**
   - Checks license status
   - Verifies domain is registered
   - Returns active tier

3. **License Deactivation:**
   - Removes domain registration
   - Frees up domain slot

4. **Tier Detection:**
   - Reads product SKU (nexus-pro, nexus-advanced, nexus-agency)
   - Falls back to license type field
   - Returns appropriate tier level

## Troubleshooting

**API returns 404:**
- Make sure plugin is activated
- Try the legacy endpoint with `?nexus_api_action=info`

**License not found:**
- Check that Software License Manager has created the license
- Verify the license key is correct

**Domain limit reached:**
- Deactivate from one domain first
- Or upgrade to higher tier

## Support

For issues, contact: jdsandigitel.com
