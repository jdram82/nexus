# API Documentation Generator - Quick Start Guide

Get up and running with the API Documentation Generator in 5 minutes.

## Step 1: Access the API Docs

Navigate to **Appearance > API Docs** in your WordPress admin.

## Step 2: Generate Documentation

### Option A: Full Directory Scan

1. Select scan directory: `/wp-content/themes/nexus-theme/`
2. Choose languages: `PHP, JavaScript`
3. Click **"Generate Documentation"**
4. Wait 5-30 seconds (depending on codebase size)

### Option B: Single File Parse

1. Enter file path: `/wp-content/themes/nexus-theme/inc/api/class-nexus-rest-api.php`
2. Click **"Parse File"**
3. Review parsed endpoints

## Step 3: Explore Your API

Navigate to **Appearance > API Explorer**

1. **Select an endpoint** from the sidebar
2. **Fill in parameters** if required
3. **Click "Send Request"**
4. **View the response**

## Step 4: Copy Code Examples

Switch between code tabs:
- **cURL**: Command-line testing
- **PHP**: WordPress development
- **JavaScript**: Frontend/AJAX
- **Python**: External integrations

Click the **copy button** to copy code to clipboard.

## Example Workflow

### Testing a Product Endpoint

```
1. Go to API Explorer
2. Select: GET /nexus/v1/products
3. Enter parameters:
   - per_page: 10
   - orderby: date
4. Click "Send Request"
5. View response (should be 200 OK)
6. Copy PHP code example
7. Paste into your custom plugin/theme
```

## Common Use Cases

### 1. Document Your Custom Plugin

```php
// Your plugin: my-plugin.php
register_rest_route( 'myplugin/v1', '/items', array(
    'methods'  => 'GET',
    'callback' => 'get_items',
) );

// Generate docs:
// 1. Go to Appearance > API Docs
// 2. Scan Directory: /wp-content/plugins/my-plugin/
// 3. Click Generate
// 4. Export as Markdown for GitHub
```

### 2. Test WooCommerce Endpoints

```
1. Generate docs for: /wp-content/plugins/woocommerce/
2. Go to API Explorer
3. Test: GET /wc/v3/products
4. Add header: Authorization: Bearer YOUR_KEY
5. Send request and verify response
```

### 3. Export API Documentation

```
1. Go to Appearance > API Docs
2. Select export format: OpenAPI
3. Click "Export Documentation"
4. Import into Swagger UI or Postman
```

## Keyboard Shortcuts

- **Ctrl/Cmd + F**: Search endpoints
- **Ctrl/Cmd + Enter**: Send request (when in explorer)
- **Ctrl/Cmd + K**: Clear test inputs

## Quick Tips

✅ **Always document with PHPDoc** - Better parsing results  
✅ **Use descriptive names** - Makes searching easier  
✅ **Test before deploying** - Catch errors early  
✅ **Save common tests** - Reuse test configurations  
✅ **Export regularly** - Keep external docs updated  

## Video Tutorial

[Watch the 3-minute tutorial](https://nexustheme.com/docs/api-docs-tutorial) *(Coming Soon)*

## Next Steps

- Read the [full README](README.md) for advanced features
- Explore [hook and filter reference](../../docs/API-REFERENCE.md)
- Join the [community forum](https://nexustheme.com/community)

## Support

Need help? Contact us:
- Email: support@nexustheme.com  
- Docs: https://nexustheme.com/docs  
- Forum: https://nexustheme.com/community

---

**Ready to document your API?** Start scanning now! 🚀
