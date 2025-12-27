<?php
/**
 * Nexus Credit Top-Up System
 * 
 * Purchase additional AI credits with Stripe integration.
 * Bulk discounts and auto-refill options.
 * 
 * @package Nexus_Theme
 * @subpackage Credits
 * @since 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nexus_Credit_Topup {
    
    /**
     * Singleton instance
     */
    private static $instance = null;
    
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
        add_action( 'admin_menu', array( $this, 'register_credit_page' ), 100 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        
        // AJAX handlers
        add_action( 'wp_ajax_nexus_create_payment_intent', array( $this, 'ajax_create_payment_intent' ) );
        add_action( 'wp_ajax_nexus_confirm_credit_purchase', array( $this, 'ajax_confirm_credit_purchase' ) );
        add_action( 'wp_ajax_nexus_setup_auto_refill', array( $this, 'ajax_setup_auto_refill' ) );
    }
    
    /**
     * Register credit management page
     */
    public function register_credit_page() {
        add_submenu_page(
            'nexus-theme-options',
            __( 'AI Credits', 'nexus' ),
            __( 'AI Credits', 'nexus' ),
            'manage_options',
            'nexus-credits',
            array( $this, 'render_credit_page' )
        );
    }
    
    /**
     * Enqueue assets
     */
    public function enqueue_assets( $hook ) {
        if ( 'nexus-options_page_nexus-credits' !== $hook ) {
            return;
        }
        
        wp_enqueue_style(
            'nexus-credits',
            get_template_directory_uri() . '/pro/assets/css/credits.css',
            array(),
            '1.6.0'
        );
        
        wp_enqueue_script(
            'nexus-credits',
            get_template_directory_uri() . '/pro/assets/js/credits.js',
            array( 'jquery' ),
            '1.6.0',
            true
        );
        
        // Stripe.js (Production: use real Stripe key)
        wp_enqueue_script( 'stripe-js', 'https://js.stripe.com/v3/', array(), null, true );
        
        $credit_manager = Nexus_Credit_Manager::get_instance();
        
        wp_localize_script( 'nexus-credits', 'nexusCredits', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'nexus_credits_nonce' ),
            'stripe_key' => $this->get_stripe_publishable_key(),
            'available_credits' => $credit_manager->get_available_credits(),
            'price_per_credit' => $credit_manager->get_credit_price(),
            'bulk_pricing' => $credit_manager->get_bulk_pricing(),
        ) );
    }
    
    /**
     * Render credit management page
     */
    public function render_credit_page() {
        $credit_manager = Nexus_Credit_Manager::get_instance();
        $license = Nexus_License_Manager::get_instance();
        
        $available = $credit_manager->get_available_credits();
        $monthly = $credit_manager->get_monthly_credits();
        $used = $credit_manager->get_credits_used();
        $purchased = $credit_manager->get_purchased_credits();
        $rollover = $credit_manager->get_rollover_credits();
        $price_per_credit = $credit_manager->get_credit_price();
        $bulk_pricing = $credit_manager->get_bulk_pricing();
        $history = $credit_manager->get_credit_history();
        
        ?>
        <div class="wrap nexus-credits-wrap">
            <h1><?php esc_html_e( 'AI Credits Management', 'nexus' ); ?></h1>
            
            <!-- Credit Overview -->
            <div class="credit-overview">
                <div class="credit-stat-card total">
                    <div class="stat-icon">💳</div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format( $available ); ?></div>
                        <div class="stat-label"><?php esc_html_e( 'Total Available Credits', 'nexus' ); ?></div>
                    </div>
                </div>
                
                <div class="credit-stat-card monthly">
                    <div class="stat-icon">📅</div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format( $monthly - $used ); ?> / <?php echo number_format( $monthly ); ?></div>
                        <div class="stat-label"><?php esc_html_e( 'Monthly Credits Remaining', 'nexus' ); ?></div>
                        <div class="stat-progress">
                            <div class="progress-bar" style="width: <?php echo $monthly > 0 ? ( ( $monthly - $used ) / $monthly * 100 ) : 0; ?>%;"></div>
                        </div>
                    </div>
                </div>
                
                <div class="credit-stat-card purchased">
                    <div class="stat-icon">🛒</div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format( $purchased ); ?></div>
                        <div class="stat-label"><?php esc_html_e( 'Purchased Credits', 'nexus' ); ?></div>
                    </div>
                </div>
                
                <div class="credit-stat-card rollover">
                    <div class="stat-icon">🔄</div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format( $rollover ); ?></div>
                        <div class="stat-label"><?php esc_html_e( 'Rollover Credits', 'nexus' ); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Buy Credits Section -->
            <div class="buy-credits-section">
                <h2><?php esc_html_e( 'Buy Additional Credits', 'nexus' ); ?></h2>
                <p class="description">
                    <?php
                    printf(
                        esc_html__( 'Current price: $%s per credit for %s tier. Bulk discounts available.', 'nexus' ),
                        number_format( $price_per_credit, 2 ),
                        ucfirst( $license->get_tier() )
                    );
                    ?>
                </p>
                
                <div class="credit-packages">
                    <?php foreach ( $bulk_pricing as $package ) : ?>
                        <div class="credit-package" data-credits="<?php echo esc_attr( $package['credits'] ); ?>" data-price="<?php echo esc_attr( $package['price'] ); ?>">
                            <div class="package-header">
                                <div class="package-credits"><?php echo number_format( $package['credits'] ); ?></div>
                                <div class="package-label"><?php esc_html_e( 'Credits', 'nexus' ); ?></div>
                                <?php if ( ! empty( $package['discount'] ) ) : ?>
                                    <div class="package-discount"><?php echo esc_html( $package['discount'] ); ?> OFF</div>
                                <?php endif; ?>
                            </div>
                            <div class="package-price">
                                <span class="price">$<?php echo number_format( $package['price'], 2 ); ?></span>
                                <span class="per-credit">($<?php echo number_format( $package['price'] / $package['credits'], 3 ); ?>/credit)</span>
                            </div>
                            <button class="button button-primary button-large buy-package">
                                <?php esc_html_e( 'Buy Now', 'nexus' ); ?>
                            </button>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Custom Amount -->
                    <div class="credit-package custom">
                        <div class="package-header">
                            <div class="package-label"><?php esc_html_e( 'Custom Amount', 'nexus' ); ?></div>
                        </div>
                        <div class="custom-input-group">
                            <input type="number" id="custom-credits" min="1" max="10000" value="100" class="custom-credits-input">
                            <span class="input-label"><?php esc_html_e( 'credits', 'nexus' ); ?></span>
                        </div>
                        <div class="package-price">
                            <span class="price" id="custom-price">$<?php echo number_format( $price_per_credit * 100, 2 ); ?></span>
                        </div>
                        <button class="button button-primary button-large" id="buy-custom">
                            <?php esc_html_e( 'Buy Custom', 'nexus' ); ?>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Auto-Refill Section -->
            <div class="auto-refill-section">
                <h2><?php esc_html_e( 'Auto-Refill Settings', 'nexus' ); ?></h2>
                <p class="description"><?php esc_html_e( 'Automatically purchase credits when your balance runs low.', 'nexus' ); ?></p>
                
                <div class="auto-refill-form">
                    <label>
                        <input type="checkbox" id="enable-auto-refill" <?php checked( get_option( 'nexus_auto_refill_enabled' ), '1' ); ?>>
                        <?php esc_html_e( 'Enable Auto-Refill', 'nexus' ); ?>
                    </label>
                    
                    <div class="refill-settings" style="<?php echo get_option( 'nexus_auto_refill_enabled' ) !== '1' ? 'display:none;' : ''; ?>">
                        <label>
                            <?php esc_html_e( 'Trigger when credits drop below:', 'nexus' ); ?>
                            <input type="number" id="refill-threshold" value="<?php echo esc_attr( get_option( 'nexus_auto_refill_threshold', 50 ) ); ?>" min="10" max="500">
                        </label>
                        
                        <label>
                            <?php esc_html_e( 'Purchase amount:', 'nexus' ); ?>
                            <select id="refill-amount">
                                <option value="100" <?php selected( get_option( 'nexus_auto_refill_amount' ), '100' ); ?>>100 credits</option>
                                <option value="500" <?php selected( get_option( 'nexus_auto_refill_amount' ), '500' ); ?>>500 credits</option>
                                <option value="1000" <?php selected( get_option( 'nexus_auto_refill_amount' ), '1000' ); ?>>1000 credits</option>
                            </select>
                        </label>
                        
                        <button class="button" id="save-auto-refill">
                            <?php esc_html_e( 'Save Auto-Refill Settings', 'nexus' ); ?>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Credit History -->
            <div class="credit-history-section">
                <h2><?php esc_html_e( 'Credit History', 'nexus' ); ?></h2>
                
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Date', 'nexus' ); ?></th>
                            <th><?php esc_html_e( 'Type', 'nexus' ); ?></th>
                            <th><?php esc_html_e( 'Amount', 'nexus' ); ?></th>
                            <th><?php esc_html_e( 'Balance After', 'nexus' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( empty( $history ) ) : ?>
                            <tr>
                                <td colspan="4"><?php esc_html_e( 'No credit activity yet.', 'nexus' ); ?></td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ( $history as $entry ) : ?>
                                <tr>
                                    <td><?php echo esc_html( date( 'M j, Y g:i a', $entry['timestamp'] ) ); ?></td>
                                    <td>
                                        <?php
                                        $type_labels = array(
                                            'ai_generation' => __( 'AI Generation', 'nexus' ),
                                            'purchase' => __( 'Purchase', 'nexus' ),
                                            'refund' => __( 'Refund', 'nexus' ),
                                        );
                                        echo esc_html( $type_labels[ $entry['type'] ] ?? $entry['type'] );
                                        ?>
                                    </td>
                                    <td>
                                        <span class="<?php echo $entry['type'] === 'ai_generation' ? 'credit-debit' : 'credit-credit'; ?>">
                                            <?php echo $entry['type'] === 'ai_generation' ? '-' : '+'; ?>
                                            <?php echo number_format( $entry['amount'] ); ?>
                                        </span>
                                    </td>
                                    <td><?php echo number_format( $entry['balance_after'] ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Payment Modal (Hidden) -->
            <div id="payment-modal" class="nexus-modal" style="display:none;">
                <div class="modal-content">
                    <span class="modal-close">&times;</span>
                    <h2><?php esc_html_e( 'Complete Payment', 'nexus' ); ?></h2>
                    
                    <div class="payment-summary">
                        <div class="summary-row">
                            <span><?php esc_html_e( 'Credits:', 'nexus' ); ?></span>
                            <span id="payment-credits">100</span>
                        </div>
                        <div class="summary-row total">
                            <span><?php esc_html_e( 'Total:', 'nexus' ); ?></span>
                            <span id="payment-total">$10.00</span>
                        </div>
                    </div>
                    
                    <div id="card-element" class="card-element">
                        <!-- Stripe Card Element will be inserted here -->
                    </div>
                    
                    <div id="card-errors" class="card-errors"></div>
                    
                    <button id="submit-payment" class="button button-primary button-large">
                        <?php esc_html_e( 'Pay Now', 'nexus' ); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Get Stripe publishable key
     */
    private function get_stripe_publishable_key() {
        // In production, store in wp-config.php: define( 'NEXUS_STRIPE_PK', 'pk_live_...' );
        if ( defined( 'NEXUS_STRIPE_PK' ) ) {
            return NEXUS_STRIPE_PK;
        }
        
        // Test mode
        return get_option( 'nexus_stripe_test_pk', 'pk_test_mock_key' );
    }
    
    /**
     * Get Stripe secret key
     */
    private function get_stripe_secret_key() {
        // In production, store in wp-config.php: define( 'NEXUS_STRIPE_SK', 'sk_live_...' );
        if ( defined( 'NEXUS_STRIPE_SK' ) ) {
            return NEXUS_STRIPE_SK;
        }
        
        // Test mode
        return get_option( 'nexus_stripe_test_sk', 'sk_test_mock_key' );
    }
    
    /**
     * AJAX: Create payment intent
     */
    public function ajax_create_payment_intent() {
        check_ajax_referer( 'nexus_credits_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
        }
        
        $credits = isset( $_POST['credits'] ) ? intval( $_POST['credits'] ) : 0;
        $amount = isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : 0;
        
        if ( $credits <= 0 || $amount <= 0 ) {
            wp_send_json_error( array( 'message' => 'Invalid credit amount' ) );
        }
        
        // In production, create real Stripe Payment Intent
        // For now, mock response
        $intent_id = 'pi_mock_' . uniqid();
        $client_secret = 'pi_mock_secret_' . uniqid();
        
        // Store pending purchase
        set_transient( 'nexus_pending_purchase_' . $intent_id, array(
            'credits' => $credits,
            'amount' => $amount,
            'user_id' => get_current_user_id(),
        ), HOUR_IN_SECONDS );
        
        wp_send_json_success( array(
            'client_secret' => $client_secret,
            'intent_id' => $intent_id,
        ) );
    }
    
    /**
     * AJAX: Confirm credit purchase
     */
    public function ajax_confirm_credit_purchase() {
        check_ajax_referer( 'nexus_credits_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
        }
        
        $intent_id = isset( $_POST['intent_id'] ) ? sanitize_text_field( $_POST['intent_id'] ) : '';
        
        $purchase = get_transient( 'nexus_pending_purchase_' . $intent_id );
        
        if ( ! $purchase ) {
            wp_send_json_error( array( 'message' => 'Purchase not found' ) );
        }
        
        // Add credits
        $credit_manager = Nexus_Credit_Manager::get_instance();
        $new_balance = $credit_manager->add_purchased_credits( $purchase['credits'], $purchase['user_id'] );
        
        // Clean up
        delete_transient( 'nexus_pending_purchase_' . $intent_id );
        
        wp_send_json_success( array(
            'message' => sprintf( __( '%d credits added successfully!', 'nexus' ), $purchase['credits'] ),
            'new_balance' => $new_balance,
        ) );
    }
    
    /**
     * AJAX: Setup auto-refill
     */
    public function ajax_setup_auto_refill() {
        check_ajax_referer( 'nexus_credits_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
        }
        
        $enabled = isset( $_POST['enabled'] ) ? (bool) $_POST['enabled'] : false;
        $threshold = isset( $_POST['threshold'] ) ? intval( $_POST['threshold'] ) : 50;
        $amount = isset( $_POST['amount'] ) ? intval( $_POST['amount'] ) : 100;
        
        update_option( 'nexus_auto_refill_enabled', $enabled ? '1' : '0' );
        update_option( 'nexus_auto_refill_threshold', $threshold );
        update_option( 'nexus_auto_refill_amount', $amount );
        
        wp_send_json_success( array(
            'message' => __( 'Auto-refill settings saved', 'nexus' ),
        ) );
    }
}

// Initialize
Nexus_Credit_Topup::get_instance();
