# Payment Gateway Integration - India Market Support

## Overview
Extended Nexus credit system with multi-gateway payment support, prioritizing India-specific payment methods while maintaining global compatibility.

**Version**: 1.6.0 → 1.6.1  
**Feature**: Phase 3A - Multi-Gateway Payment System  
**Date**: 2025

---

## Supported Payment Gateways

### 1. Razorpay (Primary for India)
**Status**: ✅ Fully Integrated  
**Countries**: India, Malaysia, Singapore  
**Currencies**: INR, USD, EUR, GBP, MYR, SGD  
**Fees**: 2% + ₹0  

**Payment Methods Supported**:
- UPI (Google Pay, PhonePe, Paytm)
- Credit/Debit Cards
- Net Banking (100+ banks)
- Wallets (Paytm, Mobikwik, Ola Money, etc.)
- EMI options

**Implementation**:
```php
// Backend: class-payment-gateway.php
$order = $gateway->create_razorpay_order($amount, 'INR', $metadata);

// Frontend: credits-multi-gateway.js
const rzp = new Razorpay({
    key: key_id,
    amount: amount_in_paise,
    currency: 'INR',
    order_id: order_id
});
rzp.open();
```

**Signature Verification**:
```php
$expected = hash_hmac('sha256', $order_id . '|' . $payment_id, $key_secret);
if ($expected === $signature) {
    // Payment verified
}
```

---

### 2. Stripe (Global Fallback)
**Status**: ✅ Maintained from Original  
**Countries**: 40+ countries  
**Currencies**: 130+ currencies  
**Fees**: 2.9% + $0.30  

**Payment Methods Supported**:
- Credit/Debit Cards
- Apple Pay
- Google Pay
- Bank transfers (in some regions)

**Implementation**:
```javascript
const {paymentIntent} = await stripe.confirmCardPayment(clientSecret, {
    payment_method: { card: cardElement }
});
```

---

### 3. Cashfree (India Alternative)
**Status**: ✅ Fully Integrated  
**Countries**: India  
**Currencies**: INR  
**Fees**: 1.99% (lowest cost option)  

**Payment Methods Supported**:
- UPI
- Cards
- Net Banking
- Wallets
- EMI

**Implementation**:
```php
// Backend
$order = $gateway->create_cashfree_order($amount, 'INR', $metadata);

// Frontend
const cashfree = Cashfree({mode: 'sandbox'});
cashfree.checkout({paymentSessionId: session_id});
```

---

### 4. Paytm
**Status**: 🔨 Stub Created (Coming Soon)  
**Note**: Returns WP_Error currently - planned for future release

---

## Architecture

### Gateway Manager Class
**File**: `pro/credits/class-payment-gateway.php` (600+ lines)

**Key Methods**:
```php
// Gateway-agnostic payment creation
$payment = $gateway->create_payment($amount, $currency, $metadata);

// Returns:
array(
    'gateway' => 'razorpay',
    'order_id' => 'order_xyz123',
    'amount' => 10000, // Amount in smallest unit (paise)
    'currency' => 'INR',
    'key' => 'rzp_test_key' // For frontend initialization
)

// Payment verification
$verified = $gateway->verify_payment($order_id, $payment_id, $signature);
```

**Gateway Configuration**:
```php
$gateways = array(
    'razorpay' => array(
        'name' => 'Razorpay',
        'countries' => array('IN', 'MY', 'SG'),
        'currencies' => array('INR', 'USD', 'EUR', 'GBP'),
        'fees' => '2% + ₹0',
        'test_mode_supported' => true
    ),
    // ... other gateways
);
```

### Admin Settings Page
**Location**: Nexus Options → Payment Gateways

**Features**:
- Gateway selection (radio buttons)
- Fee comparison table
- Currency support display
- API credential management
- Test/Live mode toggle

**Configuration Options**:
1. **Razorpay**:
   - Key ID: `rzp_test_...` or `rzp_live_...`
   - Key Secret

2. **Stripe**:
   - Publishable Key: `pk_test_...` or `pk_live_...`
   - Secret Key

3. **Cashfree**:
   - App ID
   - Secret Key
   - API Version: 2022-09-01

**Security Best Practice**:
```php
// wp-config.php (recommended)
define('NEXUS_RAZORPAY_KEY_ID', 'rzp_live_...');
define('NEXUS_RAZORPAY_KEY_SECRET', '...');
define('NEXUS_STRIPE_PK', 'pk_live_...');
define('NEXUS_STRIPE_SK', 'sk_live_...');
```

---

## Frontend Integration

### JavaScript File
**File**: `pro/assets/js/credits-multi-gateway.js` (350 lines)

**Gateway Detection**:
```javascript
const gateway = nexusCredits.gateway; // 'razorpay', 'stripe', or 'cashfree'

if (gateway === 'razorpay') {
    processRazorpay(paymentData);
} else if (gateway === 'stripe') {
    processStripe(paymentData);
} else if (gateway === 'cashfree') {
    processCashfree(paymentData);
}
```

**Razorpay Checkout Flow**:
```javascript
processRazorpay(paymentData, credits, amount) {
    const options = {
        key: nexusCredits.gatewayKey,
        amount: paymentData.amount, // In paise
        currency: 'INR',
        order_id: paymentData.order_id,
        handler: (response) => {
            confirmPurchase(
                response.razorpay_order_id,
                response.razorpay_payment_id,
                response.razorpay_signature
            );
        },
        theme: { color: '#667eea' }
    };
    
    const rzp = new Razorpay(options);
    rzp.open();
}
```

### Script Enqueuing
**File**: `pro/credits/class-credit-topup.php`

```php
// Load gateway-specific scripts
switch ($gateway) {
    case 'razorpay':
        wp_enqueue_script('razorpay-checkout', 
            'https://checkout.razorpay.com/v1/checkout.js');
        break;
    
    case 'stripe':
        wp_enqueue_script('stripe-js', 
            'https://js.stripe.com/v3/');
        break;
    
    case 'cashfree':
        wp_enqueue_script('cashfree-sdk', 
            'https://sdk.cashfree.com/js/v3/cashfree.js');
        break;
}

// Localize payment data
wp_localize_script('nexus-credits', 'nexusCredits', array(
    'gateway' => $gateway,
    'gatewayKey' => $gateway_key,
    'currency' => $default_currency,
    'nonce' => wp_create_nonce('nexus_credit_nonce')
));
```

---

## AJAX Workflow

### 1. Create Payment Intent
**Endpoint**: `nexus_create_payment_intent`

**Request**:
```javascript
{
    action: 'nexus_create_payment_intent',
    nonce: '...',
    credits: 100,
    amount: 10.00
}
```

**Response** (Razorpay):
```json
{
    "success": true,
    "data": {
        "gateway": "razorpay",
        "order_id": "order_Lmao1iNWZ4Pq6p",
        "payment_data": {
            "order_id": "order_Lmao1iNWZ4Pq6p",
            "amount": 1000,
            "currency": "INR"
        }
    }
}
```

**Response** (Stripe):
```json
{
    "success": true,
    "data": {
        "gateway": "stripe",
        "order_id": "pi_abc123",
        "payment_data": {
            "client_secret": "pi_abc123_secret_def456"
        }
    }
}
```

### 2. Confirm Purchase
**Endpoint**: `nexus_confirm_credit_purchase`

**Request** (Razorpay):
```javascript
{
    action: 'nexus_confirm_credit_purchase',
    nonce: '...',
    order_id: 'order_Lmao1iNWZ4Pq6p',
    payment_id: 'pay_Lmao1iNWZ4Pq6q',
    signature: 'sha256_hash_here'
}
```

**Backend Verification** (Razorpay):
```php
$expected_signature = hash_hmac('sha256', 
    $order_id . '|' . $payment_id, 
    $key_secret
);

if ($expected_signature === $signature) {
    // Add credits to user account
    $credit_manager->add_purchased_credits($credits, $user_id);
}
```

---

## Currency Handling

### Geo-Based Currency Detection
```php
public function get_default_currency() {
    $timezone = get_option('timezone_string');
    $gmt_offset = get_option('gmt_offset');
    
    // India timezone detection
    if (strpos($timezone, 'India') !== false || 
        strpos($timezone, 'Kolkata') !== false || 
        $gmt_offset == 5.5) {
        return 'INR';
    }
    
    return 'USD'; // Default
}
```

### Amount Conversion
```php
// Razorpay expects amount in paise (1 INR = 100 paise)
'amount' => intval($amount * 100)

// Cashfree expects amount as float
'order_amount' => floatval($amount)

// Stripe expects amount in cents (1 USD = 100 cents)
'amount' => intval($amount * 100)
```

---

## Testing

### Test Credentials

**Razorpay**:
```
Key ID: rzp_test_1DP5mmOlF5G5ag
Key Secret: thisissecret
```

**Test Cards** (Razorpay):
- Successful: 4111 1111 1111 1111
- Failed: 4000 0000 0000 0002
- CVV: Any 3 digits
- Expiry: Any future date

**Test UPI** (Razorpay):
- UPI ID: success@razorpay
- For failure: failure@razorpay

**Stripe**:
```
Publishable Key: pk_test_...
Secret Key: sk_test_...
```

**Test Cards** (Stripe):
- Successful: 4242 4242 4242 4242
- Requires Auth: 4000 0027 6000 3184
- Declined: 4000 0000 0000 0002

**Cashfree**:
```
App ID: TEST...
Secret Key: TEST...
Mode: sandbox
```

### Test Scenarios

1. **Purchase 100 Credits via Razorpay (INR)**
   - Amount: ₹10.00 (1000 paise)
   - Gateway: Razorpay
   - Payment Method: UPI (success@razorpay)
   - Expected: Credits added, transaction logged

2. **Purchase 500 Credits via Stripe (USD)**
   - Amount: $50.00 (5000 cents)
   - Gateway: Stripe
   - Payment Method: Test card 4242...
   - Expected: Credits added with USD conversion logged

3. **Failed Payment Handling**
   - Use failure test credentials
   - Expected: Error message, no credits added, transaction marked failed

4. **Signature Verification** (Razorpay)
   - Tamper with signature parameter
   - Expected: Verification fails, payment rejected

---

## Migration Notes

### From Old System (Stripe-only)
All existing Stripe configurations remain functional. No breaking changes.

**Changes**:
- Old: `nexus_stripe_test_pk` → New: `nexus_stripe_publishable_key`
- Old: `nexus_stripe_test_sk` → New: Uses `NEXUS_STRIPE_SK` constant
- Old: `client_secret` only → New: Gateway-agnostic `payment_data`

### Adding New Gateway
1. Add gateway configuration to `$this->gateways` array
2. Implement `create_{gateway}_order()` method
3. Implement `verify_{gateway}_payment()` method
4. Add frontend handler in `credits-multi-gateway.js`
5. Enqueue gateway-specific JavaScript library

---

## Production Checklist

- [ ] Replace test API keys with live keys in wp-config.php
- [ ] Set Cashfree mode to 'production' in JavaScript
- [ ] Update Razorpay webhook URL for payment notifications
- [ ] Test all payment methods in live mode with small amounts
- [ ] Enable logging for payment transactions
- [ ] Set up Razorpay payment reminders (optional)
- [ ] Configure automatic refund policies
- [ ] Add SSL certificate verification
- [ ] Implement PCI DSS compliance measures
- [ ] Set up payment failure alerts

---

## Security Considerations

1. **Credential Storage**:
   - Use wp-config.php constants for production keys
   - Never commit API keys to git
   - Rotate keys periodically

2. **Signature Verification**:
   - Always verify payment signatures server-side
   - Reject payments without valid signatures
   - Log verification failures

3. **HTTPS Required**:
   - All payment gateways require SSL/TLS
   - Enforce HTTPS on credit purchase pages

4. **AJAX Security**:
   - Nonce verification on all AJAX endpoints
   - Capability checks (`manage_options`)
   - Sanitize all input data

5. **Transaction Logging**:
   - Log all payment attempts
   - Store transaction IDs for reconciliation
   - Retain logs for minimum 7 years (compliance)

---

## Fee Comparison

| Gateway   | Fee Structure | Best For | Settlement Time |
|-----------|---------------|----------|-----------------|
| **Cashfree** | 1.99% | High volume India | T+1 days |
| **Razorpay** | 2% + ₹0 | Indian market, UPI | T+2 days |
| **Stripe** | 2.9% + $0.30 | Global, cards | 7 days |

**Savings Example** (1000 transactions @ ₹100 each):
- Cashfree: ₹1,990 in fees
- Razorpay: ₹2,000 in fees  
- Stripe: ₹2,900 + ₹300 = ₹3,200 in fees

**Annual Savings** (Cashfree vs Stripe): ₹1,210 per ₹100,000 processed

---

## Support Resources

### Razorpay
- Docs: https://razorpay.com/docs/
- Dashboard: https://dashboard.razorpay.com/
- Support: support@razorpay.com

### Stripe
- Docs: https://stripe.com/docs
- Dashboard: https://dashboard.stripe.com/
- Support: https://support.stripe.com/

### Cashfree
- Docs: https://docs.cashfree.com/
- Dashboard: https://merchant.cashfree.com/
- Support: support@cashfree.com

---

## Known Limitations

1. **Paytm**: Not yet implemented (stub returns error)
2. **Offline Payments**: Not supported (all gateways are online)
3. **Cryptocurrency**: Not supported
4. **Bank Transfers**: Limited support (Stripe in some regions)
5. **Recurring Payments**: Auto-refill uses one-time payments (not subscriptions)

---

## Future Enhancements

- [ ] Implement Paytm gateway
- [ ] Add PhonePe payment gateway
- [ ] Support for PayPal
- [ ] Subscription-based auto-refill (recurring payments)
- [ ] Multi-currency pricing with auto-conversion
- [ ] Payment analytics dashboard
- [ ] Automatic tax calculation (GST for India)
- [ ] Invoice generation with gateway receipts
- [ ] Refund management interface
- [ ] Payment method recommendations based on user location

---

## Files Modified/Created

### Created (Phase 3A Multi-Gateway):
1. `pro/credits/class-payment-gateway.php` (600 lines)
2. `pro/assets/js/credits-multi-gateway.js` (350 lines)
3. `docs/PAYMENT_GATEWAY_INTEGRATION.md` (this file)

### Modified:
1. `pro/credits/class-credit-topup.php`:
   - Updated `enqueue_assets()` to load gateway-specific scripts
   - Updated `get_gateway_keys()` to support multi-gateway
   - Updated `ajax_create_payment_intent()` to use Payment Gateway Manager
   - Updated `ajax_confirm_credit_purchase()` to verify signatures

2. `pro/class-nexus-pro.php`:
   - Added include for `class-payment-gateway.php`

---

## Git Commit Details

**Branch**: main  
**Commit Message**: "Phase 3A: Multi-Gateway Payment System for India Market"  

**Files Changed**: 6 files  
**Insertions**: ~1,200 lines  
**Deletions**: ~100 lines  

**Version Bump**: 1.6.0 → 1.6.1

---

## Conclusion

The multi-gateway payment system extends Nexus beyond the global Stripe-only approach to support India-specific payment methods through Razorpay and Cashfree. This localization improves conversion rates in the Indian market (where UPI dominates) and reduces transaction fees by up to 31% using Cashfree.

The architecture maintains backward compatibility with existing Stripe integrations while providing a clean abstraction for adding future payment gateways.
