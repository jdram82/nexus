<?php
/**
 * Payment Processing Class
 * Handles PayPal and Razorpay integration
 */

if (!defined('ABSPATH')) {
    exit;
}

class ULNEC_Payment {
    
    private $supabase;
    private $license_manager;
    
    public function __construct($supabase) {
        $this->supabase = $supabase;
        $this->license_manager = new ULNEC_License($supabase);
        
        // Handle payment webhooks
        add_action('init', [$this, 'handle_webhooks']);
        add_action('template_redirect', [$this, 'handle_payment_return']);
    }
    
    /**
     * Handle payment webhooks
     */
    public function handle_webhooks() {
        if (empty($_GET['ulnec_webhook'])) {
            return;
        }

        $gateway = sanitize_text_field(wp_unslash($_GET['ulnec_webhook']));

        if (!in_array($gateway, ['paypal', 'razorpay'], true)) {
            return;
        }
            
        if ($gateway === 'paypal') {
            $this->handle_paypal_webhook();
        } elseif ($gateway === 'razorpay') {
            $this->handle_razorpay_webhook();
        }
    }

    /**
     * Handle return/cancel redirects from payment providers.
     */
    public function handle_payment_return() {
        if (empty($_GET['ulnec_payment_return'])) {
            return;
        }

        $status = isset($_GET['status']) ? sanitize_key(wp_unslash($_GET['status'])) : 'pending';
        $gateway = isset($_GET['gateway']) ? sanitize_key(wp_unslash($_GET['gateway'])) : 'payment';

        if (!in_array($status, ['success', 'cancel', 'failed', 'pending'], true)) {
            $status = 'pending';
        }

        $billing_url = function_exists('ulnec_get_billing_url') ? ulnec_get_billing_url() : home_url('/billing/');
        $redirect_url = add_query_arg(
            [
                'ulnec_payment_notice' => $status,
                'ulnec_gateway' => $gateway,
            ],
            $billing_url
        );

        wp_safe_redirect($redirect_url);
        exit;
    }
    
    /**
     * Handle PayPal webhook
     */
    private function handle_paypal_webhook() {
        $raw_post = file_get_contents('php://input');
        $data = json_decode($raw_post, true);

        if (!is_array($data)) {
            $this->send_webhook_response(400, ['success' => false, 'message' => 'Invalid payload']);
        }

        $verification = $this->verify_paypal_webhook($raw_post, $data);
        if (is_wp_error($verification)) {
            $this->send_webhook_response(403, ['success' => false, 'message' => $verification->get_error_message()]);
        }

        $event_type = isset($data['event_type']) ? sanitize_text_field($data['event_type']) : '';
        $resource = isset($data['resource']) && is_array($data['resource']) ? $data['resource'] : [];

        if (!$this->is_paypal_success_event($event_type, $resource)) {
            $this->send_webhook_response(200, ['success' => true, 'message' => 'Event ignored']);
        }

        $transaction_id = $this->extract_paypal_transaction_id($resource, $data);
        if (empty($transaction_id)) {
            $this->send_webhook_response(422, ['success' => false, 'message' => 'Transaction ID missing']);
        }

        if ($this->is_event_processed('paypal', $transaction_id)) {
            $this->send_webhook_response(200, ['success' => true, 'message' => 'Already processed']);
        }

        $amount_data = $this->extract_paypal_amount($resource);
        $identity = $this->resolve_paypal_identity($resource, $data);

        $result = $this->fulfill_successful_payment([
            'gateway' => 'paypal',
            'transaction_id' => $transaction_id,
            'status' => 'completed',
            'amount' => $amount_data['amount'],
            'currency' => $amount_data['currency'],
            'tier' => $identity['tier'],
            'subscription_id' => $this->extract_paypal_subscription_id($resource),
            'user_id' => $identity['user_id'],
            'email' => $identity['email'],
            'raw_payload' => $data,
        ]);

        if (is_wp_error($result)) {
            $this->send_webhook_response(500, ['success' => false, 'message' => $result->get_error_message()]);
        }

        $this->mark_event_processed('paypal', $transaction_id);
        $this->send_webhook_response(200, ['success' => true, 'message' => 'Processed']);
    }
    
    /**
     * Handle Razorpay webhook
     */
    private function handle_razorpay_webhook() {
        $raw_post = file_get_contents('php://input');
        $data = json_decode($raw_post, true);

        if (!is_array($data)) {
            $this->send_webhook_response(400, ['success' => false, 'message' => 'Invalid payload']);
        }

        $verification = $this->verify_razorpay_webhook($raw_post);
        if (is_wp_error($verification)) {
            $this->send_webhook_response(403, ['success' => false, 'message' => $verification->get_error_message()]);
        }

        $event_type = isset($data['event']) ? sanitize_text_field($data['event']) : '';
        if (!in_array($event_type, ['payment.captured', 'order.paid', 'subscription.charged'], true)) {
            $this->send_webhook_response(200, ['success' => true, 'message' => 'Event ignored']);
        }

        $payment_entity = $this->extract_razorpay_payment_entity($data);
        if (empty($payment_entity) || !is_array($payment_entity)) {
            $this->send_webhook_response(422, ['success' => false, 'message' => 'Payment entity missing']);
        }

        $transaction_id = isset($payment_entity['id']) ? sanitize_text_field($payment_entity['id']) : '';
        if (empty($transaction_id)) {
            $this->send_webhook_response(422, ['success' => false, 'message' => 'Transaction ID missing']);
        }

        if ($this->is_event_processed('razorpay', $transaction_id)) {
            $this->send_webhook_response(200, ['success' => true, 'message' => 'Already processed']);
        }

        $amount = isset($payment_entity['amount']) ? ((float) $payment_entity['amount']) / 100 : 0.0;
        $currency = isset($payment_entity['currency']) ? strtoupper((string) $payment_entity['currency']) : 'INR';
        $notes = isset($payment_entity['notes']) && is_array($payment_entity['notes']) ? $payment_entity['notes'] : [];

        $result = $this->fulfill_successful_payment([
            'gateway' => 'razorpay',
            'transaction_id' => $transaction_id,
            'status' => 'completed',
            'amount' => $amount,
            'currency' => $currency,
            'tier' => $this->extract_tier_from_meta($notes),
            'subscription_id' => isset($payment_entity['order_id']) ? sanitize_text_field($payment_entity['order_id']) : '',
            'user_id' => isset($notes['wp_user_id']) ? absint($notes['wp_user_id']) : 0,
            'email' => isset($payment_entity['email']) ? sanitize_email($payment_entity['email']) : '',
            'raw_payload' => $data,
        ]);

        if (is_wp_error($result)) {
            $this->send_webhook_response(500, ['success' => false, 'message' => $result->get_error_message()]);
        }

        $this->mark_event_processed('razorpay', $transaction_id);
        $this->send_webhook_response(200, ['success' => true, 'message' => 'Processed']);
    }
    
    /**
     * Process successful payment
     */
    public function process_payment($user_id, $plan, $amount, $gateway, $transaction_id) {
        return $this->fulfill_successful_payment([
            'gateway' => $gateway,
            'transaction_id' => $transaction_id,
            'status' => 'completed',
            'amount' => (float) $amount,
            'currency' => 'USD',
            'tier' => $plan,
            'subscription_id' => $transaction_id,
            'user_id' => (int) $user_id,
            'email' => '',
            'raw_payload' => [],
        ]);
    }

    /**
     * Fulfill a successful payment end-to-end.
     */
    private function fulfill_successful_payment($payload) {
        $gateway = sanitize_key($payload['gateway'] ?? 'unknown');
        $transaction_id = sanitize_text_field($payload['transaction_id'] ?? '');
        $tier = $this->sanitize_tier($payload['tier'] ?? 'beta');
        $status = sanitize_key($payload['status'] ?? 'completed');
        $currency = strtoupper((string) ($payload['currency'] ?? 'USD'));
        $amount = (float) ($payload['amount'] ?? 0);
        $subscription_id = sanitize_text_field($payload['subscription_id'] ?? $transaction_id);
        $email = sanitize_email($payload['email'] ?? '');
        $user_id = absint($payload['user_id'] ?? 0);

        $supabase_user = $this->resolve_supabase_user($user_id, $email);
        if (is_wp_error($supabase_user)) {
            return $supabase_user;
        }

        if (empty($supabase_user) || empty($supabase_user['id'])) {
            return new WP_Error('payment_user_not_found', 'Could not resolve user for payment.');
        }

        $supabase_user_id = $supabase_user['id'];
        $wp_user_id = !empty($supabase_user['wordpress_user_id']) ? absint($supabase_user['wordpress_user_id']) : $user_id;

        $transaction_result = $this->supabase->record_transaction([
            'user_id' => $supabase_user_id,
            'tier' => $tier,
            'amount' => $amount,
            'status' => $status,
            'gateway' => $gateway,
            'transaction_id' => $transaction_id,
            'currency' => $currency,
            'metadata' => wp_json_encode($payload['raw_payload'] ?? []),
        ]);

        if (is_wp_error($transaction_result)) {
            return $transaction_result;
        }

        $subscription_result = $this->supabase->create_subscription([
            'user_id' => $supabase_user_id,
            'gateway' => $gateway,
            'subscription_id' => $subscription_id,
            'plan' => $tier,
            'status' => 'active',
            'amount' => $amount,
            'currency' => $currency,
            'billing_cycle' => 'monthly',
        ]);

        if (is_wp_error($subscription_result)) {
            return $subscription_result;
        }

        $license = $this->create_or_reuse_active_license($supabase_user_id, $tier);
        if (is_wp_error($license)) {
            return $license;
        }

        if ($wp_user_id > 0) {
            update_user_meta($wp_user_id, 'ulnec_last_payment_gateway', $gateway);
            update_user_meta($wp_user_id, 'ulnec_last_payment_txn', $transaction_id);
            update_user_meta($wp_user_id, 'ulnec_last_payment_status', $status);
        }

        if ($wp_user_id > 0) {
            $this->send_license_email($wp_user_id, $license);
        }

        return [
            'success' => true,
            'user_id' => $supabase_user_id,
            'transaction_id' => $transaction_id,
            'license' => $license,
        ];
    }

    /**
     * Verify PayPal webhook payload using PayPal verify-webhook-signature API.
     */
    private function verify_paypal_webhook($raw_body, $payload) {
        $client_id = trim((string) get_option('ulnec_paypal_client_id', ''));
        $client_secret = trim((string) get_option('ulnec_paypal_client_secret', ''));
        $webhook_id = trim((string) get_option('ulnec_paypal_webhook_id', ''));
        $mode = get_option('ulnec_paypal_mode', 'sandbox') === 'live' ? 'live' : 'sandbox';

        if ($client_id === '' || $client_secret === '' || $webhook_id === '') {
            return new WP_Error('paypal_config_missing', 'PayPal credentials or webhook ID are missing.');
        }

        $transmission_id = isset($_SERVER['HTTP_PAYPAL_TRANSMISSION_ID']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_PAYPAL_TRANSMISSION_ID'])) : '';
        $transmission_time = isset($_SERVER['HTTP_PAYPAL_TRANSMISSION_TIME']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_PAYPAL_TRANSMISSION_TIME'])) : '';
        $transmission_sig = isset($_SERVER['HTTP_PAYPAL_TRANSMISSION_SIG']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_PAYPAL_TRANSMISSION_SIG'])) : '';
        $cert_url = isset($_SERVER['HTTP_PAYPAL_CERT_URL']) ? esc_url_raw(wp_unslash($_SERVER['HTTP_PAYPAL_CERT_URL'])) : '';
        $auth_algo = isset($_SERVER['HTTP_PAYPAL_AUTH_ALGO']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_PAYPAL_AUTH_ALGO'])) : '';

        if ($transmission_id === '' || $transmission_time === '' || $transmission_sig === '' || $cert_url === '' || $auth_algo === '') {
            return new WP_Error('paypal_headers_missing', 'PayPal webhook headers are missing.');
        }

        $access_token = $this->get_paypal_access_token($client_id, $client_secret, $mode);
        if (is_wp_error($access_token)) {
            return $access_token;
        }

        $api_base = $this->get_paypal_api_base($mode);
        $verify_response = wp_remote_post($api_base . '/v1/notifications/verify-webhook-signature', [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
            ],
            'timeout' => 30,
            'body' => wp_json_encode([
                'transmission_id' => $transmission_id,
                'transmission_time' => $transmission_time,
                'cert_url' => $cert_url,
                'auth_algo' => $auth_algo,
                'transmission_sig' => $transmission_sig,
                'webhook_id' => $webhook_id,
                'webhook_event' => $payload,
            ]),
        ]);

        if (is_wp_error($verify_response)) {
            return $verify_response;
        }

        $code = wp_remote_retrieve_response_code($verify_response);
        $body = json_decode((string) wp_remote_retrieve_body($verify_response), true);
        if ($code >= 400 || !is_array($body)) {
            return new WP_Error('paypal_verify_failed', 'PayPal webhook verification failed.');
        }

        $status = strtoupper((string) ($body['verification_status'] ?? ''));
        if ($status !== 'SUCCESS') {
            return new WP_Error('paypal_verify_status_invalid', 'PayPal webhook is not verified.');
        }

        return true;
    }

    /**
     * Verify Razorpay webhook using HMAC SHA256 signature.
     */
    private function verify_razorpay_webhook($raw_body) {
        $secret = trim((string) get_option('ulnec_razorpay_webhook_secret', ''));
        if ($secret === '') {
            return new WP_Error('razorpay_secret_missing', 'Razorpay webhook secret is missing.');
        }

        $signature = isset($_SERVER['HTTP_X_RAZORPAY_SIGNATURE']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_X_RAZORPAY_SIGNATURE'])) : '';
        if ($signature === '') {
            return new WP_Error('razorpay_signature_missing', 'Razorpay signature header is missing.');
        }

        $expected = hash_hmac('sha256', $raw_body, $secret);
        if (!hash_equals($expected, $signature)) {
            return new WP_Error('razorpay_signature_invalid', 'Invalid Razorpay webhook signature.');
        }

        return true;
    }

    private function get_paypal_access_token($client_id, $client_secret, $mode) {
        $api_base = $this->get_paypal_api_base($mode);
        $response = wp_remote_post($api_base . '/v1/oauth2/token', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $client_secret),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'timeout' => 30,
            'body' => 'grant_type=client_credentials',
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = json_decode((string) wp_remote_retrieve_body($response), true);
        if ($code >= 400 || !is_array($body) || empty($body['access_token'])) {
            return new WP_Error('paypal_oauth_failed', 'Could not fetch PayPal access token.');
        }

        return sanitize_text_field($body['access_token']);
    }

    private function get_paypal_api_base($mode) {
        return $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
    }

    private function extract_paypal_transaction_id($resource, $payload) {
        if (!empty($resource['id'])) {
            return sanitize_text_field($resource['id']);
        }

        if (!empty($payload['id'])) {
            return sanitize_text_field($payload['id']);
        }

        return '';
    }

    private function extract_paypal_subscription_id($resource) {
        if (!empty($resource['billing_agreement_id'])) {
            return sanitize_text_field($resource['billing_agreement_id']);
        }

        if (!empty($resource['supplementary_data']['related_ids']['order_id'])) {
            return sanitize_text_field($resource['supplementary_data']['related_ids']['order_id']);
        }

        return '';
    }

    private function extract_paypal_amount($resource) {
        $amount = 0.0;
        $currency = 'USD';

        if (!empty($resource['amount']) && is_array($resource['amount'])) {
            $amount = isset($resource['amount']['value']) ? (float) $resource['amount']['value'] : 0.0;
            $currency = isset($resource['amount']['currency_code']) ? strtoupper((string) $resource['amount']['currency_code']) : 'USD';
        } elseif (!empty($resource['seller_receivable_breakdown']['gross_amount']['value'])) {
            $amount = (float) $resource['seller_receivable_breakdown']['gross_amount']['value'];
            $currency = strtoupper((string) ($resource['seller_receivable_breakdown']['gross_amount']['currency_code'] ?? 'USD'));
        }

        return [
            'amount' => $amount,
            'currency' => $currency,
        ];
    }

    private function resolve_paypal_identity($resource, $payload) {
        $email = '';
        $user_id = 0;
        $tier = $this->get_default_paid_tier();

        $custom_data = '';
        if (!empty($resource['custom_id'])) {
            $custom_data = (string) $resource['custom_id'];
        } elseif (!empty($resource['invoice_id'])) {
            $custom_data = (string) $resource['invoice_id'];
        } elseif (!empty($resource['purchase_units'][0]['custom_id'])) {
            $custom_data = (string) $resource['purchase_units'][0]['custom_id'];
        }

        $meta = $this->parse_identity_meta($custom_data);
        if (!empty($meta['wp_user_id'])) {
            $user_id = absint($meta['wp_user_id']);
        }
        if (!empty($meta['tier'])) {
            $tier = $this->sanitize_tier($meta['tier']);
        }

        if (!empty($resource['payer']['email_address'])) {
            $email = sanitize_email($resource['payer']['email_address']);
        } elseif (!empty($payload['resource']['payer']['email_address'])) {
            $email = sanitize_email($payload['resource']['payer']['email_address']);
        }

        return [
            'user_id' => $user_id,
            'email' => $email,
            'tier' => $tier,
        ];
    }

    private function extract_razorpay_payment_entity($payload) {
        if (!empty($payload['payload']['payment']['entity']) && is_array($payload['payload']['payment']['entity'])) {
            return $payload['payload']['payment']['entity'];
        }

        if (!empty($payload['payload']['subscription']['entity']) && is_array($payload['payload']['subscription']['entity'])) {
            return $payload['payload']['subscription']['entity'];
        }

        return [];
    }

    private function parse_identity_meta($custom_data) {
        $custom_data = trim((string) $custom_data);
        if ($custom_data === '') {
            return [];
        }

        $parts = preg_split('/[|,]/', $custom_data);
        if (!is_array($parts)) {
            return [];
        }

        $meta = [];
        foreach ($parts as $part) {
            $segment = trim((string) $part);
            if ($segment === '' || strpos($segment, ':') === false) {
                continue;
            }

            [$key, $value] = array_map('trim', explode(':', $segment, 2));
            if ($key !== '') {
                $meta[$key] = $value;
            }
        }

        return $meta;
    }

    private function sanitize_tier($tier) {
        $tier = sanitize_key((string) $tier);
        if (!in_array($tier, ['beta', 'pro', 'enterprise', 'agency'], true)) {
            return 'beta';
        }

        return $tier;
    }

    private function extract_tier_from_meta($meta) {
        if (is_array($meta)) {
            if (!empty($meta['tier'])) {
                return $this->sanitize_tier($meta['tier']);
            }

            if (!empty($meta['plan'])) {
                return $this->sanitize_tier($meta['plan']);
            }
        }

        return $this->get_default_paid_tier();
    }

    private function get_default_paid_tier() {
        $tier = get_option('ulnec_default_paid_tier', 'beta');
        return $this->sanitize_tier($tier);
    }

    private function resolve_supabase_user($wp_user_id, $email) {
        $wp_user_id = absint($wp_user_id);
        if ($wp_user_id > 0) {
            $user = $this->supabase->get_user_by_wordpress_id($wp_user_id);
            if (!is_wp_error($user) && !empty($user)) {
                return $user;
            }
        }

        if (!empty($email)) {
            $user = $this->supabase->get_user_by_email($email);
            if (!is_wp_error($user) && !empty($user)) {
                return $user;
            }
        }

        return new WP_Error('supabase_user_missing', 'Supabase user not found for payment payload.');
    }

    private function create_or_reuse_active_license($supabase_user_id, $tier) {
        $licenses = $this->supabase->get_user_licenses($supabase_user_id);
        if (!is_wp_error($licenses) && is_array($licenses)) {
            foreach ($licenses as $license) {
                $is_active = ($license['status'] ?? '') === 'active';
                $not_expired = !isset($license['expires_at']) || empty($license['expires_at']) || strtotime($license['expires_at']) > time();
                if ($is_active && $not_expired) {
                    return $license;
                }
            }
        }

        $duration = 365;
        if ($tier === 'beta') {
            $duration = 0;
        }

        return $this->license_manager->create_license($supabase_user_id, $tier, $duration);
    }

    private function is_event_processed($gateway, $transaction_id) {
        $key = $this->get_event_cache_key($gateway, $transaction_id);
        return get_transient($key) !== false;
    }

    private function mark_event_processed($gateway, $transaction_id) {
        $key = $this->get_event_cache_key($gateway, $transaction_id);
        set_transient($key, 1, WEEK_IN_SECONDS);
    }

    private function get_event_cache_key($gateway, $transaction_id) {
        return 'ulnec_pay_evt_' . md5($gateway . ':' . $transaction_id);
    }

    private function is_paypal_success_event($event_type, $resource) {
        $allowed = [
            'PAYMENT.CAPTURE.COMPLETED',
            'PAYMENT.SALE.COMPLETED',
            'CHECKOUT.ORDER.APPROVED',
            'BILLING.SUBSCRIPTION.ACTIVATED',
        ];

        if (in_array($event_type, $allowed, true)) {
            return true;
        }

        if (!empty($resource['status'])) {
            $status = strtoupper((string) $resource['status']);
            return in_array($status, ['COMPLETED', 'APPROVED', 'ACTIVE'], true);
        }

        return false;
    }

    private function send_webhook_response($status_code, $data) {
        status_header((int) $status_code);
        wp_send_json($data, (int) $status_code);
        exit;
    }
    
    /**
     * Send license email with professional HTML template
     */
    private function send_license_email($user_id, $license) {
        $wp_user = get_userdata($user_id);
        
        if (!$wp_user) {
            error_log('UL/NEC Payment: User not found for license email - User ID: ' . $user_id);
            return false;
        }
        
        // Get plugin instance and email handler
        $plugin = ULNEC_Plugin::instance();
        if (!$plugin || !$plugin->emails) {
            error_log('UL/NEC Payment: Email handler not available');
            return false;
        }
        
        // Calculate expiration date
        $expires_at = 'Lifetime';
        if (!empty($license['expires_at'])) {
            $expires_at = date('F j, Y', strtotime($license['expires_at']));
        }
        
        // Prepare email data
        $email_data = [
            'customer_name' => $wp_user->display_name ?: $wp_user->user_login,
            'customer_email' => $wp_user->user_email,
            'license_key' => $license['license_key'],
            'tier' => $license['tier'],
            'expires_at' => $expires_at,
            'max_activations' => isset($license['max_activations']) ? $license['max_activations'] : 1,
            'download_url' => home_url('/billing/')
        ];
        
        // Send professional HTML email
        $sent = $plugin->emails->send_license_delivery_email($email_data);
        
        if ($sent) {
            error_log('UL/NEC Payment: License email sent successfully to ' . $wp_user->user_email);
        } else {
            error_log('UL/NEC Payment: Failed to send license email to ' . $wp_user->user_email);
        }
        
        return $sent;
    }
}
