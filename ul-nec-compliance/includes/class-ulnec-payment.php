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
    
    public function __construct($supabase) {
        $this->supabase = $supabase;
        
        // Handle payment webhooks
        add_action('init', [$this, 'handle_webhooks']);
    }
    
    /**
     * Handle payment webhooks
     */
    public function handle_webhooks() {
        if (isset($_GET['ulnec_webhook'])) {
            $gateway = sanitize_text_field($_GET['ulnec_webhook']);
            
            if ($gateway === 'paypal') {
                $this->handle_paypal_webhook();
            } elseif ($gateway === 'razorpay') {
                $this->handle_razorpay_webhook();
            }
        }
    }
    
    /**
     * Handle PayPal webhook
     */
    private function handle_paypal_webhook() {
        $raw_post = file_get_contents('php://input');
        $data = json_decode($raw_post, true);
        
        // Verify webhook (implement PayPal verification)
        // Process payment based on event type
        
        http_response_code(200);
        exit;
    }
    
    /**
     * Handle Razorpay webhook
     */
    private function handle_razorpay_webhook() {
        $raw_post = file_get_contents('php://input');
        $data = json_decode($raw_post, true);
        
        // Verify webhook signature
        // Process payment
        
        http_response_code(200);
        exit;
    }
    
    /**
     * Process successful payment
     */
    public function process_payment($user_id, $plan, $amount, $gateway, $transaction_id) {
        // Create subscription record
        $subscription = $this->supabase->create_subscription([
            'user_id' => $user_id,
            'gateway' => $gateway,
            'subscription_id' => $transaction_id,
            'plan' => $plan,
            'status' => 'active',
            'amount' => $amount,
            'currency' => 'USD',
            'billing_cycle' => 'yearly'
        ]);
        
        // Generate license
        $license_manager = new ULNEC_License($this->supabase);
        $duration = 365; // 1 year
        
        if ($plan === 'beta') {
            $duration = 0; // Lifetime for beta
        }
        
        $license = $license_manager->create_license($user_id, $plan, $duration);
        
        // Send email with license key
        $this->send_license_email($user_id, $license);
        
        return $license;
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
