# Nexus Pro API Reference

## Overview

The Nexus Pro Tier exposes several REST API endpoints and PHP APIs for developers to integrate with.

---

## REST API Endpoints

Base URL: `https://yoursite.com/wp-json/nexus/v1/`

All endpoints require authentication unless specified otherwise.

### Authentication

Include WordPress nonce in requests:

```javascript
fetch('/wp-json/nexus/v1/templates', {
    headers: {
        'X-WP-Nonce': wpApiSettings.nonce
    }
})
```

---

## Cloud Storage API

### Upload Template to Cloud

**Endpoint:** `POST /cloud/upload`

**Parameters:**
```json
{
    "template_id": "unique-template-id",
    "template_name": "My Awesome Header",
    "template_type": "header|footer|page|section",
    "template_data": {
        "blocks": [...],
        "settings": {...}
    }
}
```

**Response:**
```json
{
    "success": true,
    "template_id": "unique-template-id",
    "url": "https://your-space.nyc3.digitaloceanspaces.com/templates/123/template.json",
    "size": 15840,
    "duration_ms": 245.67
}
```

**Errors:**
- `401` - Unauthorized
- `403` - Tier limit exceeded
- `500` - Upload failed

### Download Template from Cloud

**Endpoint:** `GET /cloud/download/{template_id}`

**Response:**
```json
{
    "success": true,
    "template_id": "unique-template-id",
    "template_name": "My Awesome Header",
    "template_type": "header",
    "template_data": {
        "blocks": [...],
        "settings": {...}
    },
    "last_synced": "2025-02-01 10:30:00",
    "duration_ms": 128.45
}
```

### List Cloud Templates

**Endpoint:** `GET /cloud/templates`

**Query Parameters:**
- `type` - Filter by template type
- `limit` - Results per page (default: 100)
- `offset` - Pagination offset

**Response:**
```json
{
    "templates": [
        {
            "template_id": "...",
            "template_name": "...",
            "template_type": "...",
            "file_size": 15840,
            "last_synced": "2025-02-01 10:30:00",
            "sync_status": "synced"
        }
    ],
    "total": 42,
    "limit": 5,
    "used": 3
}
```

### Delete Cloud Template

**Endpoint:** `DELETE /cloud/templates/{template_id}`

**Response:**
```json
{
    "success": true,
    "message": "Template deleted successfully"
}
```

---

## Payment API

### Create Payment Order

**Endpoint:** `POST /payments/create`

**Parameters:**
```json
{
    "amount": 19900,
    "currency": "INR",
    "credits": 100,
    "gateway": "razorpay|paypal"
}
```

**Response (Razorpay):**
```json
{
    "success": true,
    "gateway": "razorpay",
    "order_id": "order_xxxxx",
    "amount": 19900,
    "currency": "INR",
    "key_id": "rzp_live_xxxxx"
}
```

**Response (PayPal):**
```json
{
    "success": true,
    "gateway": "paypal",
    "order_id": "PAYPAL_ORDER_ID",
    "approval_url": "https://paypal.com/checkoutnow?token=xxx",
    "amount": "199.00",
    "currency": "USD"
}
```

### Verify Payment

**Endpoint:** `POST /payments/verify`

**Parameters (Razorpay):**
```json
{
    "payment_id": "pay_xxxxx",
    "order_id": "order_xxxxx",
    "signature": "generated_signature"
}
```

**Parameters (PayPal):**
```json
{
    "order_id": "PAYPAL_ORDER_ID"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Payment successful!",
    "credits_added": 100
}
```

### Get Payment History

**Endpoint:** `GET /payments/history`

**Query Parameters:**
- `limit` - Results per page (default: 50)
- `offset` - Pagination offset
- `status` - Filter by status (created|paid|failed)

**Response:**
```json
{
    "payments": [
        {
            "id": 123,
            "order_id": "order_xxxxx",
            "gateway": "razorpay",
            "amount": 19900,
            "currency": "INR",
            "status": "paid",
            "created_at": "2025-02-01 10:30:00"
        }
    ],
    "total": 25
}
```

---

## PHP API

### Cloud Storage Class

```php
// Get instance
$cloud = Nexus_Cloud_Storage::instance();

// Upload file
$result = $cloud->upload( 
    'path/to/file.json',  // Cloud path
    $file_content,         // Content
    'application/json'     // MIME type
);

if ( ! is_wp_error( $result ) ) {
    echo $result['url']; // Public URL
}

// Download file
$content = $cloud->download( 'path/to/file.json' );

// Delete file
$cloud->delete( 'path/to/file.json' );

// List files
$files = $cloud->list_files( 'templates/' );
foreach ( $files as $file ) {
    echo $file['name'] . ' - ' . size_format( $file['size'] );
}

// Test connection
$is_connected = $cloud->test_connection();
```

### Template Cloud Sync Class

```php
// Get instance
$sync = Nexus_Template_Cloud_Sync::instance();

// Upload template
$result = $sync->upload_template(
    'my-template-id',
    'My Template Name',
    'header',
    array(
        'blocks' => [...],
        'settings' => [...]
    )
);

// Download template
$template = $sync->download_template( 'my-template-id' );
$data = $template['template_data'];

// Delete template
$sync->delete_template( 'my-template-id' );

// List templates
$templates = $sync->list_templates( array(
    'template_type' => 'header',
    'limit' => 50
) );

// Check tier limits
if ( $sync->can_upload_more() ) {
    // User can upload more templates
}

$limit = $sync->get_user_limit(); // 5 for Pro, unlimited for Advanced
$used = $sync->get_user_template_count(); // Current usage
```

### Payment Gateway Class

```php
// Get instance
$gateway = Nexus_Payment_Gateway_Multi::instance();

// Create order
$order = $gateway->create_order(
    19900,     // Amount in smallest unit (paise/cents)
    'INR',     // Currency
    array(     // Metadata
        'credits' => 100,
        'user_id' => get_current_user_id()
    )
);

if ( ! is_wp_error( $order ) ) {
    // For Razorpay
    if ( $order['gateway'] === 'razorpay' ) {
        echo $order['order_id'];
        echo $order['key_id'];
    }
    
    // For PayPal
    if ( $order['gateway'] === 'paypal' ) {
        wp_redirect( $order['approval_url'] );
    }
}

// Verify payment
$verified = $gateway->verify_payment(
    'pay_xxxxx',
    array(
        'razorpay_order_id' => 'order_xxxxx',
        'razorpay_signature' => 'signature_xxxxx'
    )
);

// Check if gateway has credentials
if ( $gateway->has_credentials() ) {
    // Gateway configured
}
```

---

## WordPress Hooks

### Actions

#### Cloud Sync Actions

```php
// Triggered when template is saved
do_action( 'nexus_template_saved', $template_id, $template_data );

// Triggered when template is deleted
do_action( 'nexus_template_deleted', $template_id );

// Manually trigger sync
do_action( 'nexus_sync_template', $template_id );

// Sync all templates
do_action( 'nexus_sync_all_templates' );
```

#### Payment Actions

```php
// Before payment creation
do_action( 'nexus_before_payment_create', $amount, $currency );

// After successful payment
do_action( 'nexus_payment_success', $payment_id, $order_data );

// After failed payment
do_action( 'nexus_payment_failed', $payment_id, $error );
```

### Filters

#### Cloud Sync Filters

```php
// Modify upload path
add_filter( 'nexus_cloud_upload_path', function( $path, $template_id ) {
    return "custom/path/{$template_id}.json";
}, 10, 2 );

// Modify template data before upload
add_filter( 'nexus_cloud_template_data', function( $data, $template_id ) {
    $data['custom_field'] = 'value';
    return $data;
}, 10, 2 );

// Modify tier limits
add_filter( 'nexus_cloud_tier_limits', function( $limits ) {
    $limits['pro'] = 10; // Increase Pro limit to 10
    return $limits;
} );
```

#### Payment Filters

```php
// Modify payment amount
add_filter( 'nexus_payment_amount', function( $amount, $credits ) {
    return $amount * 0.9; // 10% discount
}, 10, 2 );

// Modify active gateway
add_filter( 'nexus_active_payment_gateway', function( $gateway ) {
    // Force PayPal for US users
    if ( is_us_user() ) {
        return 'paypal';
    }
    return $gateway;
} );

// Modify gateway credentials
add_filter( 'nexus_razorpay_credentials', function( $credentials ) {
    $credentials['key_id'] = get_option( 'custom_razorpay_key' );
    return $credentials;
} );
```

---

## JavaScript API

### Cloud Sync JS

```javascript
// Upload template
jQuery.post(ajaxurl, {
    action: 'nexus_sync_template',
    nonce: nexusData.nonce,
    template_id: 'my-template',
    template_name: 'My Template',
    template_type: 'header',
    template_data: JSON.stringify({
        blocks: [...],
        settings: {...}
    })
}, function(response) {
    if (response.success) {
        console.log('Synced!', response.data);
    }
});

// Download template
jQuery.post(ajaxurl, {
    action: 'nexus_download_template',
    nonce: nexusData.nonce,
    template_id: 'my-template'
}, function(response) {
    if (response.success) {
        const template = response.data.template_data;
        // Use template data
    }
});

// List templates
jQuery.post(ajaxurl, {
    action: 'nexus_list_cloud_templates',
    nonce: nexusData.nonce,
    template_type: 'header',
    limit: 50
}, function(response) {
    if (response.success) {
        response.data.templates.forEach(template => {
            console.log(template.template_name);
        });
    }
});
```

### Payment JS (Razorpay)

```javascript
// Create order
jQuery.post(ajaxurl, {
    action: 'nexus_create_payment',
    nonce: nexusData.nonce,
    amount: 19900,
    currency: 'INR',
    credits: 100
}, function(response) {
    if (response.success) {
        const options = {
            key: response.data.key_id,
            amount: response.data.amount,
            currency: response.data.currency,
            order_id: response.data.order_id,
            handler: function(response) {
                // Verify payment
                jQuery.post(ajaxurl, {
                    action: 'nexus_verify_payment',
                    nonce: nexusData.nonce,
                    payment_id: response.razorpay_payment_id,
                    data: {
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_signature: response.razorpay_signature
                    }
                }, function(verifyResponse) {
                    if (verifyResponse.success) {
                        alert('Payment successful!');
                    }
                });
            }
        };
        
        const rzp = new Razorpay(options);
        rzp.open();
    }
});
```

### Payment JS (PayPal)

```javascript
// Create order
jQuery.post(ajaxurl, {
    action: 'nexus_create_payment',
    nonce: nexusData.nonce,
    amount: 19900,
    currency: 'USD',
    credits: 100
}, function(response) {
    if (response.success && response.data.approval_url) {
        // Redirect to PayPal
        window.location.href = response.data.approval_url;
    }
});

// After redirect back from PayPal
const urlParams = new URLSearchParams(window.location.search);
const orderId = urlParams.get('token');

if (orderId) {
    jQuery.post(ajaxurl, {
        action: 'nexus_verify_payment',
        nonce: nexusData.nonce,
        payment_id: orderId,
        data: {}
    }, function(response) {
        if (response.success) {
            alert('Payment successful!');
        }
    });
}
```

---

## Database Schema

### `wp_nexus_cloud_templates`

Stores cloud template metadata.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `user_id` | bigint | WordPress user ID |
| `template_id` | varchar(100) | Unique template identifier |
| `template_name` | varchar(255) | Human-readable name |
| `template_type` | varchar(50) | Type (header, footer, page, section) |
| `template_data` | longtext | JSON template data |
| `cloud_url` | varchar(500) | Public cloud URL |
| `cloud_key` | varchar(500) | Cloud storage key/path |
| `last_synced` | datetime | Last sync timestamp |
| `sync_status` | varchar(20) | pending, synced, failed |
| `file_size` | bigint | File size in bytes |
| `checksum` | varchar(64) | MD5 checksum for integrity |
| `created_at` | datetime | Creation timestamp |
| `updated_at` | datetime | Last update timestamp |

### `wp_nexus_payment_orders`

Stores payment orders.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `user_id` | bigint | WordPress user ID |
| `gateway` | varchar(50) | razorpay, paypal |
| `order_id` | varchar(100) | Gateway order ID (unique) |
| `payment_id` | varchar(100) | Gateway payment ID |
| `amount` | bigint | Amount in smallest unit |
| `currency` | varchar(3) | Currency code (INR, USD, etc.) |
| `status` | varchar(20) | created, paid, failed |
| `metadata` | longtext | Serialized metadata |
| `payment_data` | longtext | Serialized payment response |
| `created_at` | datetime | Creation timestamp |
| `updated_at` | datetime | Last update timestamp |

### `wp_nexus_payment_logs`

Stores payment activity logs.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `payment_id` | varchar(100) | Payment/order ID |
| `status` | varchar(20) | success, failed, webhook_captured |
| `message` | text | Log message |
| `created_at` | datetime | Timestamp |

### `wp_nexus_sync_logs`

Stores cloud sync logs.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `template_id` | varchar(100) | Template ID |
| `action` | varchar(50) | upload, download, delete |
| `status` | varchar(20) | success, failed |
| `message` | text | Log message |
| `bytes_transferred` | bigint | Bytes transferred |
| `duration_ms` | int | Duration in milliseconds |
| `created_at` | datetime | Timestamp |

---

## Error Codes

### Cloud Storage Errors

| Code | Message | Solution |
|------|---------|----------|
| `no_cloud_storage` | Cloud storage not configured | Configure DigitalOcean Spaces credentials |
| `limit_exceeded` | Template limit exceeded | Upgrade tier or delete old templates |
| `upload_failed` | Upload to cloud failed | Check credentials and connection |
| `download_failed` | Download from cloud failed | Verify file exists in cloud |
| `template_not_found` | Template not found | Template doesn't exist in database |
| `invalid_json` | Invalid template data | Template JSON is corrupted |
| `connection_failed` | Connection to cloud failed | Check internet connection and credentials |

### Payment Errors

| Code | Message | Solution |
|------|---------|----------|
| `no_credentials` | Gateway credentials not configured | Add API keys in settings |
| `invalid_gateway` | Invalid payment gateway | Use 'razorpay' or 'paypal' |
| `razorpay_error` | Razorpay error message | Check Razorpay dashboard for details |
| `paypal_error` | PayPal error message | Check PayPal dashboard for details |
| `invalid_signature` | Payment verification failed | Signature mismatch, possible fraud |
| `payment_not_successful` | Payment not successful | Payment was cancelled or declined |
| `missing_data` | Missing verification data | Required payment data not provided |

---

## Rate Limits

To prevent abuse, the following rate limits apply:

- **Cloud Upload**: 60 requests/hour per user
- **Cloud Download**: 300 requests/hour per user
- **Payment Creation**: 10 requests/hour per user
- **API Calls**: 1000 requests/hour per IP

**Headers returned:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1643750400
```

---

## Webhooks

### Razorpay Webhook

**URL:** `https://yoursite.com/wp-admin/admin-ajax.php?action=nexus_razorpay_webhook`

**Events:**
- `payment.captured` - Payment successfully captured
- `payment.failed` - Payment failed
- `order.paid` - Order fully paid

**Signature Verification:**
```php
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'];
$expected = hash_hmac('sha256', $payload, $webhook_secret);

if ($signature === $expected) {
    // Webhook is valid
}
```

### PayPal Webhook

**URL:** `https://yoursite.com/wp-admin/admin-ajax.php?action=nexus_paypal_webhook`

**Events:**
- `CHECKOUT.ORDER.APPROVED` - Customer approved payment
- `PAYMENT.CAPTURE.COMPLETED` - Payment captured
- `PAYMENT.CAPTURE.DENIED` - Payment denied

---

## Examples

### Complete Template Sync Example

```php
<?php
// Create template data
$template_data = array(
    'blocks' => array(
        array(
            'type' => 'heading',
            'content' => 'Welcome to My Site'
        ),
        array(
            'type' => 'paragraph',
            'content' => 'This is a paragraph'
        )
    ),
    'settings' => array(
        'background' => '#ffffff',
        'text_color' => '#000000'
    )
);

// Upload to cloud
$sync = Nexus_Template_Cloud_Sync::instance();
$result = $sync->upload_template(
    'homepage-hero-' . time(),
    'Homepage Hero Section',
    'section',
    $template_data
);

if ( is_wp_error( $result ) ) {
    wp_die( $result->get_error_message() );
}

echo 'Template synced! URL: ' . $result['url'];
?>
```

### Complete Payment Flow Example

```php
<?php
// Create Razorpay order
$gateway = Nexus_Payment_Gateway_Multi::instance();
$order = $gateway->create_order(
    19900, // ₹199.00
    'INR',
    array(
        'credits' => 100,
        'user_id' => get_current_user_id(),
        'plan' => 'pro'
    )
);

if ( is_wp_error( $order ) ) {
    wp_die( $order->get_error_message() );
}
?>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<button id="pay-button">Pay ₹199</button>

<script>
document.getElementById('pay-button').onclick = function() {
    var options = {
        key: '<?php echo $order['key_id']; ?>',
        amount: <?php echo $order['amount']; ?>,
        currency: '<?php echo $order['currency']; ?>',
        order_id: '<?php echo $order['order_id']; ?>',
        handler: function(response) {
            // Verify payment on server
            jQuery.post(ajaxurl, {
                action: 'nexus_verify_payment',
                nonce: '<?php echo wp_create_nonce('nexus_payment'); ?>',
                payment_id: response.razorpay_payment_id,
                data: {
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_signature: response.razorpay_signature
                }
            }, function(data) {
                if (data.success) {
                    alert('Payment successful! Credits added.');
                    location.reload();
                }
            });
        }
    };
    
    var rzp = new Razorpay(options);
    rzp.open();
};
</script>
```

---

## Security Considerations

1. **Always validate nonces** for AJAX requests
2. **Sanitize all user input** using `sanitize_text_field()`, etc.
3. **Verify payment signatures** to prevent fraud
4. **Use HTTPS** for all API calls
5. **Rate limit** API endpoints
6. **Log all payment activities** for audit trail
7. **Encrypt sensitive data** in database
8. **Rotate API keys** regularly

---

## Support

For API support:
- **Documentation**: https://docs.nexustheme.com/api
- **Examples**: https://github.com/nexustheme/examples
- **Email**: api@nexustheme.com
