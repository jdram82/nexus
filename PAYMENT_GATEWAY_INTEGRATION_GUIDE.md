# UL/NEC Payment Gateway Integration Guide (PayPal + Razorpay)

This guide explains how to configure production payments for UL/NEC and how the webhook flow activates licenses in Supabase.

## 1) What is implemented

The current implementation includes:

- Verified PayPal webhook processing (`/?ulnec_webhook=paypal`)
- Verified Razorpay webhook processing (`/?ulnec_webhook=razorpay`)
- Supabase transaction write (`ulnec_transactions`)
- Supabase subscription write (`ulnec_subscriptions`)
- License activation/creation (`ulnec_licenses`) after successful payment
- Payment success/cancel return handling (`/?ulnec_payment_return=1...`)

Main files:

- `ul-nec-compliance/includes/class-ulnec-payment.php`
- `ul-nec-compliance/includes/class-ulnec-admin.php`
- `page-ulnec-billing.php`

## 2) Configure gateway credentials

In WordPress admin:

1. Open **UL-NEC → Settings**
2. Fill:
   - **PayPal Mode** (`sandbox` or `live`)
   - **PayPal Client ID**
   - **PayPal Client Secret**
   - **PayPal Webhook ID**
   - **Razorpay Key ID**
   - **Razorpay Key Secret**
   - **Razorpay Webhook Secret**
   - **Default Paid Tier** (`beta`, `pro`, `enterprise`, `agency`)
3. Save settings

## 3) Configure webhook endpoints

### PayPal

In PayPal Developer Dashboard, create a webhook:

- Webhook URL: `https://YOUR-DOMAIN/?ulnec_webhook=paypal`
- Subscribe to events:
  - `PAYMENT.CAPTURE.COMPLETED`
  - `PAYMENT.SALE.COMPLETED`
  - `CHECKOUT.ORDER.APPROVED`
  - `BILLING.SUBSCRIPTION.ACTIVATED`

Copy webhook ID from PayPal and set it in **UL-NEC Settings**.

### Razorpay

In Razorpay Dashboard, create a webhook:

- Webhook URL: `https://YOUR-DOMAIN/?ulnec_webhook=razorpay`
- Secret: set this value in **UL-NEC Settings** as Razorpay Webhook Secret
- Subscribe to events:
  - `payment.captured`
  - `order.paid`
  - `subscription.charged`

## 4) Configure return URLs

Use provider return URLs so user sees payment state on `/billing`.

- PayPal success: `https://YOUR-DOMAIN/?ulnec_payment_return=1&gateway=paypal&status=success`
- PayPal cancel: `https://YOUR-DOMAIN/?ulnec_payment_return=1&gateway=paypal&status=cancel`
- Razorpay success: `https://YOUR-DOMAIN/?ulnec_payment_return=1&gateway=razorpay&status=success`
- Razorpay cancel: `https://YOUR-DOMAIN/?ulnec_payment_return=1&gateway=razorpay&status=cancel`

## 5) Configure billing buttons

In **Settings → General** (existing Nexus settings), set:

- `UL/NEC Checkout URL` = your checkout URL
- `UL/NEC Add Payment Method URL` = your customer-portal / payment-method URL

If either points to `/billing`, Nexus falls back to in-page payment section anchors.

## 6) Pass user metadata for precise license mapping (recommended)

Webhook processing can map users by email, but best practice is to pass user metadata:

- `wp_user_id`
- `tier`

### PayPal

When creating orders/subscriptions via API, set a custom field:

- `custom_id` format: `wp_user_id:123|tier:beta`

### Razorpay

When creating order/checkout, set notes:

- `notes[wp_user_id] = 123`
- `notes[tier] = beta`

## 7) Production test checklist

1. Create a fresh test user in WordPress
2. Start payment from `/billing`
3. Complete payment in provider sandbox first
4. Confirm webhook delivery = HTTP 200
5. Check Supabase tables:
   - `ulnec_transactions`
   - `ulnec_subscriptions`
   - `ulnec_licenses`
6. Confirm user sees success message on billing page
7. Confirm download remains available

## 8) Troubleshooting

- **No activation after payment**: verify webhook secret/webhook ID and event type subscriptions.
- **Wrong user activated**: ensure `wp_user_id` metadata is passed in checkout payload.
- **Status shows pending only**: return URL works, but webhook has not delivered/verified yet.
- **PayPal verification failure**: check mode (`sandbox/live`) and credentials pair.

## 9) Security notes

- Keep PayPal/Razorpay secrets server-side only
- Rotate webhook secrets if exposed
- Do not disable webhook verification in production
