# API Documentation Generator

> **Advanced Tier Feature** - Automatically generate comprehensive API documentation from your codebase with interactive testing capabilities.

## Overview

The API Documentation Generator is a powerful tool that scans your WordPress theme/plugin code and automatically generates professional API documentation. It includes an interactive API explorer for testing endpoints in real-time.

## Features

### 🔍 **Automatic Code Parsing**
- **Multi-Language Support**: Parses PHP, JavaScript, and Python files
- **Smart Detection**: Extracts classes, methods, functions, and docblocks
- **Documentation Extraction**: Reads PHPDoc, JSDoc, and Python docstrings
- **Code Examples**: Auto-generates usage examples for each endpoint

### 📡 **REST API Discovery**
- **Automatic Detection**: Discovers all WordPress REST API endpoints
- **Endpoint Cataloging**: Stores endpoint metadata, parameters, and responses
- **Version Tracking**: Maintains API version history
- **Namespace Organization**: Groups endpoints by namespace

### 🧪 **Interactive API Explorer**
- **Live Testing**: Test any endpoint directly from the admin panel
- **Request Builder**: Visual interface for building API requests
- **Response Viewer**: Formatted JSON/XML response display
- **Code Generation**: Generate cURL, PHP, JavaScript, and Python examples

### 📊 **Documentation Management**
- **Export Options**: Export as JSON, Markdown, or OpenAPI/Swagger
- **Search & Filter**: Quickly find endpoints by path, method, or tag
- **Custom Documentation**: Add custom notes and examples
- **Saved Tests**: Save and replay test requests

## Installation

The API Documentation Generator is automatically available in the **Advanced Tier** and **Agency Tier**.

### Requirements

- WordPress 5.8+
- Nexus Theme v3.0.0+
- PHP 7.4+
- Advanced or Agency Tier license

### Activation

The feature is automatically loaded when you activate an Advanced or Agency tier license. No additional setup required.

## Usage

### Accessing the API Docs

Navigate to **Appearance > API Docs** in your WordPress admin panel.

### Generating Documentation

1. **Select Directory**: Choose which directory to scan (themes, plugins, etc.)
2. **Choose Languages**: Select file types to parse (PHP, JS, Python)
3. **Click Generate**: The system will scan and parse your code
4. **Review Results**: View all discovered endpoints in the table

```php
// Example: Scanning the entire theme
Directory: /wp-content/themes/nexus-theme/
Languages: PHP, JavaScript
```

### Parsing Individual Files

You can also parse a single file:

```php
// Parse a specific file
File Path: /wp-content/themes/nexus-theme/inc/api/class-nexus-rest-api.php
```

### Using the API Explorer

1. **Navigate to Explorer**: Go to **Appearance > API Explorer**
2. **Select Endpoint**: Choose an endpoint from the sidebar
3. **Fill Parameters**: Enter required and optional parameters
4. **Add Headers**: Customize request headers (authentication, content-type)
5. **Send Request**: Click "Send Request" to test the endpoint
6. **View Response**: Inspect status code, headers, and body

#### Example Test

```javascript
// Testing GET /wp-json/nexus/v1/products
Endpoint: GET /nexus/v1/products
Parameters:
  - per_page: 10
  - orderby: date
Headers:
  Content-Type: application/json
  Authorization: Bearer YOUR_TOKEN

Response:
  Status: 200 OK
  Time: 245ms
  Size: 3.2 KB
  Body: [...]
```

### Managing Endpoints

#### View All Endpoints

The main API Docs page shows all documented endpoints:

- **Method Badge**: Visual indicator for GET/POST/PUT/DELETE
- **Endpoint Path**: Full REST API path
- **Description**: Brief description from docblocks
- **Actions**: Test, Edit, or Delete endpoints

#### Edit Endpoint

Click "Edit" to modify endpoint documentation:

```php
// Endpoint details
Path: /nexus/v1/products/{id}
Method: GET
Description: Retrieve a single product by ID
Parameters:
  - id (required): Product ID
  - fields: Comma-separated list of fields to return
Response:
  - 200: Success
  - 404: Product not found
Tags: Products, E-commerce
Version: 1.0.0
```

### Exporting Documentation

Export your API docs in multiple formats:

#### JSON Export

```json
{
  "endpoints": [
    {
      "id": "abc123",
      "path": "/nexus/v1/products",
      "method": "GET",
      "description": "Get all products",
      "parameters": [...],
      "response": {...}
    }
  ]
}
```

#### Markdown Export

```markdown
# API Documentation

## GET /nexus/v1/products

Get all products with optional filtering.

### Parameters

| Name | Type | Required | Description |
|------|------|----------|-------------|
| per_page | integer | No | Items per page (default: 10) |
| orderby | string | No | Sort field (date, title, etc.) |
```

#### OpenAPI/Swagger Export

```yaml
openapi: 3.0.0
info:
  title: Nexus Theme API
  version: 1.0.0
paths:
  /nexus/v1/products:
    get:
      summary: Get all products
      parameters:
        - name: per_page
          in: query
          schema:
            type: integer
```

## Code Examples

### Registering a Custom Endpoint

The documentation generator will automatically discover and document your custom endpoints:

```php
<?php
/**
 * Get products
 *
 * Retrieves a list of products with optional filtering.
 *
 * @since 1.0.0
 * @param WP_REST_Request $request Full request object.
 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
 */
public function get_products( $request ) {
    $per_page = $request->get_param( 'per_page' ) ?: 10;
    $orderby  = $request->get_param( 'orderby' ) ?: 'date';
    
    $products = get_posts( array(
        'post_type'      => 'product',
        'posts_per_page' => $per_page,
        'orderby'        => $orderby,
    ) );
    
    return rest_ensure_response( $products );
}

// Register route
register_rest_route( 'nexus/v1', '/products', array(
    'methods'  => 'GET',
    'callback' => array( $this, 'get_products' ),
    'args'     => array(
        'per_page' => array(
            'type'    => 'integer',
            'default' => 10,
        ),
        'orderby'  => array(
            'type'    => 'string',
            'default' => 'date',
        ),
    ),
) );
```

### Testing with Code Examples

The API Explorer generates code examples in multiple languages:

#### cURL

```bash
curl -X GET \
  'https://yoursite.com/wp-json/nexus/v1/products?per_page=10' \
  -H 'Content-Type: application/json'
```

#### PHP

```php
<?php
$response = wp_remote_get(
    'https://yoursite.com/wp-json/nexus/v1/products',
    array(
        'headers' => array('Content-Type' => 'application/json'),
    )
);

$body = wp_remote_retrieve_body($response);
$data = json_decode($body);
```

#### JavaScript

```javascript
fetch('https://yoursite.com/wp-json/nexus/v1/products?per_page=10', {
  method: 'GET',
  headers: {
    'Content-Type': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data));
```

#### Python

```python
import requests

response = requests.get(
    'https://yoursite.com/wp-json/nexus/v1/products',
    headers={'Content-Type': 'application/json'},
    params={'per_page': 10}
)

print(response.json())
```

## Advanced Features

### Custom Endpoint Documentation

Add custom metadata to endpoints:

```php
// Save custom documentation
$endpoint_manager = Nexus_Endpoint_Manager::get_instance();
$endpoint_manager->save_endpoint( array(
    'path'        => '/nexus/v1/custom',
    'method'      => 'POST',
    'description' => 'Custom endpoint for special operations',
    'parameters'  => array(
        array(
            'name'        => 'action',
            'type'        => 'string',
            'required'    => true,
            'description' => 'The action to perform',
        ),
    ),
    'tags'        => array( 'Custom', 'Advanced' ),
    'version'     => '2.0.0',
) );
```

### Discovering WordPress Endpoints

Automatically discover all WordPress core and plugin endpoints:

```php
$endpoint_manager = Nexus_Endpoint_Manager::get_instance();
$wp_endpoints = $endpoint_manager->discover_wp_endpoints();

// Returns array of all registered REST API routes
// Including core WP, plugins, and custom endpoints
```

### Saved Tests

Save frequently used test requests:

```javascript
// JavaScript: Save current test configuration
jQuery('#nexus-save-test-btn').click();

// Saved tests include:
// - Endpoint ID
// - Parameters
// - Headers
// - Request body
// - Creation timestamp
```

### Filtering Endpoints

Filter endpoints by various criteria:

```php
$endpoint_manager = Nexus_Endpoint_Manager::get_instance();

// Get only GET endpoints
$get_endpoints = $endpoint_manager->get_all_endpoints( array(
    'method' => 'GET',
) );

// Get endpoints by tag
$product_endpoints = $endpoint_manager->get_all_endpoints( array(
    'tag' => 'Products',
) );

// Search endpoints
$search_results = $endpoint_manager->get_all_endpoints( array(
    'search' => 'product',
) );
```

## REST API Routes

The API Documentation Generator exposes its own REST API:

### GET /wp-json/nexus/v1/docs

Get all documented endpoints.

**Response:**
```json
{
  "endpoints": [...],
  "total": 42
}
```

### GET /wp-json/nexus/v1/docs/endpoints/{id}

Get a specific endpoint by ID.

**Response:**
```json
{
  "id": "abc123",
  "path": "/nexus/v1/products",
  "method": "GET",
  "description": "Get all products",
  "parameters": [...],
  "created_at": "2024-01-01 12:00:00",
  "updated_at": "2024-01-15 14:30:00"
}
```

## Hooks & Filters

### Actions

```php
// Before documentation generation
do_action( 'nexus_before_generate_docs', $directory, $languages );

// After documentation generation
do_action( 'nexus_after_generate_docs', $results );

// Before endpoint save
do_action( 'nexus_before_save_endpoint', $endpoint_data );

// After endpoint save
do_action( 'nexus_after_save_endpoint', $endpoint_id, $endpoint_data );
```

### Filters

```php
// Filter parsed endpoints
$endpoints = apply_filters( 'nexus_parsed_endpoints', $endpoints, $file_path );

// Filter code examples
$example = apply_filters( 'nexus_code_example', $example, $language, $endpoint );

// Filter export format
$export = apply_filters( 'nexus_export_docs', $export, $format, $endpoints );
```

## Troubleshooting

### Documentation Not Generating

**Issue**: No endpoints found after scanning.

**Solutions**:
- Ensure the directory path is correct
- Check file permissions (files must be readable)
- Verify docblocks are properly formatted
- Check error logs for parsing errors

### API Explorer Not Loading Endpoints

**Issue**: Sidebar shows "No endpoints found."

**Solutions**:
- Generate documentation first (Appearance > API Docs)
- Check that endpoints were saved successfully
- Clear WordPress transients/cache
- Verify Advanced tier license is active

### Code Parser Errors

**Issue**: Parse errors in specific files.

**Solutions**:
- Ensure PHP files have valid syntax
- Check for unescaped characters in docblocks
- Verify file encoding (UTF-8 recommended)
- Review error messages in browser console

### Response Not Displaying

**Issue**: Test request succeeds but response not shown.

**Solutions**:
- Check browser console for JavaScript errors
- Ensure Prism.js is loaded (view page source)
- Disable conflicting plugins temporarily
- Clear browser cache

## Performance

### Optimization Tips

1. **Selective Scanning**: Only scan directories that contain API code
2. **Incremental Updates**: Parse individual files after changes
3. **Cache Results**: Documentation is cached in WordPress options
4. **Limit Languages**: Only select necessary file types

### Benchmarks

Typical performance metrics:

- **Small Theme** (50 files): ~2-3 seconds
- **Medium Theme** (200 files): ~8-12 seconds
- **Large Theme** (500+ files): ~20-30 seconds
- **Plugin Scan** (100 files): ~5-7 seconds

## Best Practices

### Documentation Standards

1. **Use PHPDoc**: Always document functions with PHPDoc blocks
2. **Describe Parameters**: Include @param tags with types and descriptions
3. **Document Returns**: Use @return tags to specify return types
4. **Version Tags**: Add @since tags for version tracking
5. **Examples**: Include @example tags for complex functions

### Endpoint Design

1. **RESTful Naming**: Use plural nouns (e.g., `/products` not `/getProducts`)
2. **Consistent Methods**: GET for retrieval, POST for creation, etc.
3. **Versioning**: Include version in namespace (e.g., `/nexus/v1/`)
4. **Error Responses**: Return proper HTTP status codes
5. **Pagination**: Support `per_page` and `page` parameters

### Security

1. **Authentication**: Require auth for sensitive endpoints
2. **Validation**: Validate all input parameters
3. **Sanitization**: Sanitize user input before processing
4. **Permissions**: Check user capabilities
5. **Rate Limiting**: Implement rate limiting for public endpoints

## Support

For questions or issues:

- **Documentation**: [Full API Reference](../docs/API-REFERENCE.md)
- **Support**: support@nexustheme.com
- **Community**: [Community Forum](https://nexustheme.com/community)
- **GitHub**: [Report Issues](https://github.com/jdram82/nexus/issues)

## Changelog

### Version 1.0.0 (Initial Release)

- Multi-language code parser (PHP, JavaScript, Python)
- Interactive API Explorer with live testing
- Automatic WordPress REST API discovery
- Export to JSON, Markdown, and OpenAPI formats
- Code example generation (cURL, PHP, JS, Python)
- Saved test requests
- Endpoint management (CRUD operations)
- Search and filter functionality
- Syntax highlighting with Prism.js
- Responsive admin interface

## License

This feature is part of the Nexus Theme Advanced Tier package. See [LICENSE](../../LICENSE) for details.
