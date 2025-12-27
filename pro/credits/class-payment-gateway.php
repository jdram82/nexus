<?php
/**
 * Nexus Payment Gateway Manager
 * 
 * Supports multiple payment gateways:
 * - Razorpay (India) - Primary
 * - Stripe (Global) - Fallback
 * - Cashfree (India) - Alternative
 * 
 * @package Nexus_Theme
 * @subpackage Credits
 * @since 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nexus_Payment_Gateway {
    
    /**
     * Singleton instance
     */
    private static $instance = null;
    
    /**
     * Active gateway
     */
    private $active_gateway = 'razorpay';
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->active_gateway = get_option( 'nexus_payment_gateway', 'razorpay' );
        
        add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }
    
    /**
     * Get active gateway
     */
    public function get_active_gateway() {
        return $this->active_gateway;
    }
    
    /**
     * Get available gateways
     */
    public function get_available_gateways() {
        return array(
            'razorpay' => array(
                'name' => 'Razorpay',
                'description' => 'Popular in India. Supports UPI, Cards, Net Banking, Wallets.',
                'countries' => array( 'IN', 'MY', 'SG' ),
                'currencies' => array( 'INR', 'USD', 'EUR', 'GBP', 'MYR', 'SGD' ),
                'fees' => '2% + ₹0',
                'logo' => 'https://razorpay.com/favicon.png',
            ),
            'stripe' => array(
                'name' => 'Stripe',
                'description' => 'Global payment gateway. Best for international payments.',
                'countries' => array( 'US', 'GB', 'CA', 'AU', 'EU', 'IN' ),
                'currencies' => array( 'USD', 'EUR', 'GBP', 'CAD', 'AUD', 'INR' ),
                'fees' => '2.9% + $0.30',
                'logo' => 'https://stripe.com/favicon.ico',
            ),
            'cashfree' => array(
                'name' => 'Cashfree',
                'description' => 'Indian gateway with instant settlements.',
                'countries' => array( 'IN' ),
                'currencies' => array( 'INR' ),
                'fees' => '1.99% (no setup fee)',
                'logo' => 'https://www.cashfree.com/favicon.ico',
            ),
            'paytm' => array(
                'name' => 'Paytm',
                'description' => 'Largest wallet in India with instant refunds.',
                'countries' => array( 'IN' ),
                'currencies' => array( 'INR' ),
                'fees' => '2% + ₹0',
                'logo' => 'https://paytm.com/favicon.ico',
            ),
        );
    }
    
    /**
     * Create payment order
     */
    public function create_payment( $amount, $currency, $metadata = array() ) {
        switch ( $this->active_gateway ) {
            case 'razorpay':
                return $this->create_razorpay_order( $amount, $currency, $metadata );
            
            case 'stripe':
                return $this->create_stripe_payment( $amount, $currency, $metadata );
            
            case 'cashfree':
                return $this->create_cashfree_order( $amount, $currency, $metadata );
            
            case 'paytm':
                return $this->create_paytm_order( $amount, $currency, $metadata );
            
            default:
                return new WP_Error( 'invalid_gateway', 'Invalid payment gateway configured' );
        }
    }
    
    /**
     * Razorpay: Create order
     */
    private function create_razorpay_order( $amount, $currency, $metadata ) {
        $key_id = $this->get_gateway_config( 'razorpay', 'key_id' );
        $key_secret = $this->get_gateway_config( 'razorpay', 'key_secret' );
        
        if ( ! $key_id || ! $key_secret ) {
            return new WP_Error( 'missing_credentials', 'Razorpay credentials not configured' );
        }
        
        // Convert to smallest currency unit (paise for INR)
        $amount_paise = $amount * 100;
        
        $url = 'https://api.razorpay.com/v1/orders';
        
        $body = array(
            'amount' => $amount_paise,
            'currency' => $currency,
            'receipt' => 'rcpt_' . uniqid(),
            'notes' => $metadata,
        );
        
        $response = wp_remote_post( $url, array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( $key_id . ':' . $key_secret ),
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode( $body ),
        ) );
        
        if ( is_wp_error( $response ) ) {
            return $response;
        }
        
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        
        if ( isset( $body['error'] ) ) {
            return new WP_Error( 'razorpay_error', $body['error']['description'] );
        }
        
        return array(
            'gateway' => 'razorpay',
            'order_id' => $body['id'],
            'amount' => $amount,
            'currency' => $currency,
            'key_id' => $key_id,
        );
    }
    
    /**
     * Stripe: Create payment intent
     */
    private function create_stripe_payment( $amount, $currency, $metadata ) {
        $secret_key = $this->get_gateway_config( 'stripe', 'secret_key' );
        
        if ( ! $secret_key ) {
            return new WP_Error( 'missing_credentials', 'Stripe credentials not configured' );
        }
        
        // Convert to smallest currency unit (cents for USD)
        $amount_cents = $amount * 100;
        
        $url = 'https://api.stripe.com/v1/payment_intents';
        
        $body = array(
            'amount' => $amount_cents,
            'currency' => strtolower( $currency ),
            'metadata' => $metadata,
        );
        
        $response = wp_remote_post( $url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $secret_key,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'body' => $body,
        ) );
        
        if ( is_wp_error( $response ) ) {
            return $response;
        }
        
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        
        if ( isset( $body['error'] ) ) {
            return new WP_Error( 'stripe_error', $body['error']['message'] );
        }
        
        return array(
            'gateway' => 'stripe',
            'intent_id' => $body['id'],
            'client_secret' => $body['client_secret'],
            'amount' => $amount,
            'currency' => $currency,
        );
    }
    
    /**
     * Cashfree: Create order
     */
    private function create_cashfree_order( $amount, $currency, $metadata ) {
        $app_id = $this->get_gateway_config( 'cashfree', 'app_id' );
        $secret_key = $this->get_gateway_config( 'cashfree', 'secret_key' );
        
        if ( ! $app_id || ! $secret_key ) {
            return new WP_Error( 'missing_credentials', 'Cashfree credentials not configured' );
        }
        
        $url = 'https://api.cashfree.com/pg/orders';
        
        $body = array(
            'order_id' => 'order_' . uniqid(),
            'order_amount' => $amount,
            'order_currency' => $currency,
            'customer_details' => array(
                'customer_id' => 'customer_' . get_current_user_id(),
                'customer_email' => wp_get_current_user()->user_email,
            ),
            'order_meta' => $metadata,
        );
        
        $response = wp_remote_post( $url, array(
            'headers' => array(
                'x-client-id' => $app_id,
                'x-client-secret' => $secret_key,
                'Content-Type' => 'application/json',
                'x-api-version' => '2022-09-01',
            ),
            'body' => json_encode( $body ),
        ) );
        
        if ( is_wp_error( $response ) ) {
            return $response;
        }
        
        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        
        return array(
            'gateway' => 'cashfree',
            'order_id' => $body['order_id'],
            'payment_session_id' => $body['payment_session_id'],
            'amount' => $amount,
            'currency' => $currency,
        );
    }
    
    /**
     * Paytm: Create order
     */
    private function create_paytm_order( $amount, $currency, $metadata ) {
        // Paytm integration
        return new WP_Error( 'not_implemented', 'Paytm integration coming soon' );
    }
    
    /**
     * Get gateway configuration
     */
    private function get_gateway_config( $gateway, $key ) {
        // Check wp-config.php constants first (most secure)
        $constant_name = 'NEXUS_' . strtoupper( $gateway ) . '_' . strtoupper( $key );
        if ( defined( $constant_name ) ) {
            return constant( $constant_name );
        }
        
        // Fall back to options (less secure, but easier for testing)
        return get_option( 'nexus_' . $gateway . '_' . $key, '' );
    }
    
    /**
     * Verify payment
     */
    public function verify_payment( $payment_id, $gateway = null ) {
        $gateway = $gateway ?: $this->active_gateway;
        
        switch ( $gateway ) {
            case 'razorpay':
                return $this->verify_razorpay_payment( $payment_id );
            
            case 'stripe':
                return $this->verify_stripe_payment( $payment_id );
            
            case 'cashfree':
                return $this->verify_cashfree_payment( $payment_id );
            
            default:
                return new WP_Error( 'invalid_gateway', 'Invalid gateway for verification' );
        }
    }
    
    /**
     * Razorpay: Verify signature
     */
    private function verify_razorpay_payment( $payment_data ) {
        $key_secret = $this->get_gateway_config( 'razorpay', 'key_secret' );
        
        $order_id = $payment_data['razorpay_order_id'];
        $payment_id = $payment_data['razorpay_payment_id'];
        $signature = $payment_data['razorpay_signature'];
        
        $generated_signature = hash_hmac( 'sha256', $order_id . '|' . $payment_id, $key_secret );
        
        return hash_equals( $generated_signature, $signature );
    }
    
    /**
     * Register settings page
     */
    public function register_settings_page() {
        add_submenu_page(
            'nexus-theme-options',
            __( 'Payment Settings', 'nexus' ),
            __( 'Payment Gateway', 'nexus' ),
            'manage_options',
            'nexus-payment-gateway',
            array( $this, 'render_settings_page' )
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting( 'nexus_payment_gateway', 'nexus_payment_gateway' );
        
        // Razorpay
        register_setting( 'nexus_payment_gateway', 'nexus_razorpay_key_id' );
        register_setting( 'nexus_payment_gateway', 'nexus_razorpay_key_secret' );
        
        // Stripe
        register_setting( 'nexus_payment_gateway', 'nexus_stripe_publishable_key' );
        register_setting( 'nexus_payment_gateway', 'nexus_stripe_secret_key' );
        
        // Cashfree
        register_setting( 'nexus_payment_gateway', 'nexus_cashfree_app_id' );
        register_setting( 'nexus_payment_gateway', 'nexus_cashfree_secret_key' );
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        $gateways = $this->get_available_gateways();
        $active = $this->active_gateway;
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Payment Gateway Settings', 'nexus' ); ?></h1>
            
            <form method="post" action="options.php">
                <?php settings_fields( 'nexus_payment_gateway' ); ?>
                
                <h2><?php esc_html_e( 'Select Payment Gateway', 'nexus' ); ?></h2>
                
                <table class="form-table">
                    <?php foreach ( $gateways as $key => $gateway ) : ?>
                        <tr>
                            <td>
                                <label>
                                    <input 
                                        type="radio" 
                                        name="nexus_payment_gateway" 
                                        value="<?php echo esc_attr( $key ); ?>"
                                        <?php checked( $active, $key ); ?>
                                    >
                                    <strong><?php echo esc_html( $gateway['name'] ); ?></strong>
                                </label>
                                <p class="description"><?php echo esc_html( $gateway['description'] ); ?></p>
                                <p class="description">
                                    <strong><?php esc_html_e( 'Fees:', 'nexus' ); ?></strong> <?php echo esc_html( $gateway['fees'] ); ?><br>
                                    <strong><?php esc_html_e( 'Currencies:', 'nexus' ); ?></strong> <?php echo esc_html( implode( ', ', $gateway['currencies'] ) ); ?>
                                </p>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                
                <hr>
                
                <h2><?php esc_html_e( 'API Credentials', 'nexus' ); ?></h2>
                <p class="description">
                    <?php esc_html_e( 'For security, store credentials in wp-config.php using constants like:', 'nexus' ); ?>
                    <code>define('NEXUS_RAZORPAY_KEY_ID', 'rzp_live_...');</code>
                </p>
                
                <!-- Razorpay -->
                <h3>Razorpay</h3>
                <table class="form-table">
                    <tr>
                        <th><?php esc_html_e( 'Key ID', 'nexus' ); ?></th>
                        <td>
                            <input 
                                type="text" 
                                name="nexus_razorpay_key_id" 
                                value="<?php echo esc_attr( get_option( 'nexus_razorpay_key_id' ) ); ?>"
                                class="regular-text"
                                placeholder="rzp_test_... or rzp_live_..."
                            >
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Key Secret', 'nexus' ); ?></th>
                        <td>
                            <input 
                                type="password" 
                                name="nexus_razorpay_key_secret" 
                                value="<?php echo esc_attr( get_option( 'nexus_razorpay_key_secret' ) ); ?>"
                                class="regular-text"
                            >
                        </td>
                    </tr>
                </table>
                
                <!-- Stripe -->
                <h3>Stripe</h3>
                <table class="form-table">
                    <tr>
                        <th><?php esc_html_e( 'Publishable Key', 'nexus' ); ?></th>
                        <td>
                            <input 
                                type="text" 
                                name="nexus_stripe_publishable_key" 
                                value="<?php echo esc_attr( get_option( 'nexus_stripe_publishable_key' ) ); ?>"
                                class="regular-text"
                                placeholder="pk_test_... or pk_live_..."
                            >
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Secret Key', 'nexus' ); ?></th>
                        <td>
                            <input 
                                type="password" 
                                name="nexus_stripe_secret_key" 
                                value="<?php echo esc_attr( get_option( 'nexus_stripe_secret_key' ) ); ?>"
                                class="regular-text"
                            >
                        </td>
                    </tr>
                </table>
                
                <!-- Cashfree -->
                <h3>Cashfree</h3>
                <table class="form-table">
                    <tr>
                        <th><?php esc_html_e( 'App ID', 'nexus' ); ?></th>
                        <td>
                            <input 
                                type="text" 
                                name="nexus_cashfree_app_id" 
                                value="<?php echo esc_attr( get_option( 'nexus_cashfree_app_id' ) ); ?>"
                                class="regular-text"
                            >
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Secret Key', 'nexus' ); ?></th>
                        <td>
                            <input 
                                type="password" 
                                name="nexus_cashfree_secret_key" 
                                value="<?php echo esc_attr( get_option( 'nexus_cashfree_secret_key' ) ); ?>"
                                class="regular-text"
                            >
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Get currency for user's location
     */
    public function get_default_currency() {
        // You could use geo-location or allow user to select
        $country = get_option( 'nexus_default_country', 'IN' );
        
        $currency_map = array(
            'IN' => 'INR',
            'US' => 'USD',
            'GB' => 'GBP',
            'EU' => 'EUR',
            'AU' => 'AUD',
            'CA' => 'CAD',
        );
        
        return $currency_map[ $country ] ?? 'USD';
    }
}

// Initialize
Nexus_Payment_Gateway::get_instance();
