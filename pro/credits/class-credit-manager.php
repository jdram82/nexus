<?php
/**
 * Nexus Credit Manager
 * 
 * Manages AI credit allocation, usage tracking, and top-ups.
 * Prevents Agency tier bottleneck with flexible credit purchasing.
 * 
 * @package Nexus_Theme
 * @subpackage Credits
 * @since 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nexus_Credit_Manager {
    
    /**
     * Singleton instance
     */
    private static $instance = null;
    
    /**
     * Credit pricing (per credit)
     */
    const ADVANCED_CREDIT_PRICE = 0.10; // $0.10 per credit
    const AGENCY_CREDIT_PRICE = 0.08;   // $0.08 per credit (20% discount)
    
    /**
     * Monthly base allocations
     */
    const ADVANCED_BASE_CREDITS = 100;
    const AGENCY_BASE_CREDITS = 500;
    
    /**
     * Credit expiration (days)
     */
    const CREDIT_EXPIRATION_DAYS = 365; // Purchased credits expire after 1 year
    
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
        // Reset monthly credits on first of month
        add_action( 'wp', array( $this, 'check_monthly_reset' ) );
        
        // Low credit warnings
        add_action( 'admin_notices', array( $this, 'low_credit_notice' ) );
        
        // AJAX handlers
        add_action( 'wp_ajax_nexus_get_credit_status', array( $this, 'ajax_get_credit_status' ) );
    }
    
    /**
     * Get total available credits for user
     */
    public function get_available_credits( $user_id = null ) {
        $user_id = $user_id ?: get_current_user_id();
        
        $monthly_credits = $this->get_monthly_credits();
        $purchased_credits = $this->get_purchased_credits( $user_id );
        $rollover_credits = $this->get_rollover_credits( $user_id );
        
        return $monthly_credits + $purchased_credits + $rollover_credits;
    }
    
    /**
     * Get monthly base credits based on tier
     */
    public function get_monthly_credits() {
        if ( ! class_exists( 'Nexus_License_Manager' ) ) {
            return 0;
        }
        
        $license = Nexus_License_Manager::get_instance();
        $tier = $license->get_tier();
        
        if ( 'agency' === $tier ) {
            return self::AGENCY_BASE_CREDITS;
        } elseif ( 'advanced' === $tier ) {
            return self::ADVANCED_BASE_CREDITS;
        }
        
        return 0;
    }
    
    /**
     * Get credits used this month
     */
    public function get_credits_used( $user_id = null ) {
        $user_id = $user_id ?: get_current_user_id();
        $month_key = 'nexus_credits_used_' . date( 'Y_m' );
        
        return (int) get_user_meta( $user_id, $month_key, true );
    }
    
    /**
     * Get purchased (top-up) credits
     */
    public function get_purchased_credits( $user_id = null ) {
        $user_id = $user_id ?: get_current_user_id();
        
        $purchased = (int) get_user_meta( $user_id, 'nexus_purchased_credits', true );
        
        // Check expiration
        $purchased_credits = get_user_meta( $user_id, 'nexus_purchased_credits_detail', true );
        if ( is_array( $purchased_credits ) ) {
            $valid_credits = 0;
            $now = time();
            
            foreach ( $purchased_credits as $purchase ) {
                if ( $purchase['expires'] > $now ) {
                    $valid_credits += $purchase['remaining'];
                }
            }
            
            // Update if different
            if ( $valid_credits !== $purchased ) {
                update_user_meta( $user_id, 'nexus_purchased_credits', $valid_credits );
            }
            
            return $valid_credits;
        }
        
        return $purchased;
    }
    
    /**
     * Get rollover credits from previous month
     */
    public function get_rollover_credits( $user_id = null ) {
        $user_id = $user_id ?: get_current_user_id();
        
        return (int) get_user_meta( $user_id, 'nexus_rollover_credits', true );
    }
    
    /**
     * Use credits
     */
    public function use_credits( $amount = 1, $user_id = null ) {
        $user_id = $user_id ?: get_current_user_id();
        
        if ( ! $this->has_credits( $amount, $user_id ) ) {
            return new WP_Error( 'insufficient_credits', __( 'Insufficient AI credits.', 'nexus' ) );
        }
        
        // Deduct from monthly first, then purchased, then rollover
        $month_key = 'nexus_credits_used_' . date( 'Y_m' );
        $current_used = $this->get_credits_used( $user_id );
        $monthly_limit = $this->get_monthly_credits();
        
        $remaining_to_deduct = $amount;
        
        // Use monthly credits first
        if ( $current_used < $monthly_limit ) {
            $available_monthly = $monthly_limit - $current_used;
            $from_monthly = min( $available_monthly, $remaining_to_deduct );
            
            update_user_meta( $user_id, $month_key, $current_used + $from_monthly );
            $remaining_to_deduct -= $from_monthly;
        }
        
        // Use purchased credits next
        if ( $remaining_to_deduct > 0 ) {
            $purchased = $this->get_purchased_credits( $user_id );
            if ( $purchased >= $remaining_to_deduct ) {
                update_user_meta( $user_id, 'nexus_purchased_credits', $purchased - $remaining_to_deduct );
                
                // Update detail tracking
                $this->deduct_purchased_credits( $remaining_to_deduct, $user_id );
                $remaining_to_deduct = 0;
            }
        }
        
        // Use rollover credits last
        if ( $remaining_to_deduct > 0 ) {
            $rollover = $this->get_rollover_credits( $user_id );
            if ( $rollover >= $remaining_to_deduct ) {
                update_user_meta( $user_id, 'nexus_rollover_credits', $rollover - $remaining_to_deduct );
                $remaining_to_deduct = 0;
            }
        }
        
        // Log usage
        $this->log_credit_usage( $amount, 'ai_generation', $user_id );
        
        return true;
    }
    
    /**
     * Check if user has sufficient credits
     */
    public function has_credits( $amount = 1, $user_id = null ) {
        return $this->get_available_credits( $user_id ) >= $amount;
    }
    
    /**
     * Add purchased credits
     */
    public function add_purchased_credits( $amount, $user_id = null, $expires_days = null ) {
        $user_id = $user_id ?: get_current_user_id();
        $expires_days = $expires_days ?: self::CREDIT_EXPIRATION_DAYS;
        
        $current = $this->get_purchased_credits( $user_id );
        $new_total = $current + $amount;
        
        update_user_meta( $user_id, 'nexus_purchased_credits', $new_total );
        
        // Track detailed purchase
        $purchased_credits = get_user_meta( $user_id, 'nexus_purchased_credits_detail', true );
        if ( ! is_array( $purchased_credits ) ) {
            $purchased_credits = array();
        }
        
        $purchased_credits[] = array(
            'amount' => $amount,
            'remaining' => $amount,
            'purchased_at' => time(),
            'expires' => time() + ( $expires_days * DAY_IN_SECONDS ),
        );
        
        update_user_meta( $user_id, 'nexus_purchased_credits_detail', $purchased_credits );
        
        // Log purchase
        $this->log_credit_usage( $amount, 'purchase', $user_id );
        
        return $new_total;
    }
    
    /**
     * Deduct from purchased credits (oldest first)
     */
    private function deduct_purchased_credits( $amount, $user_id ) {
        $purchased_credits = get_user_meta( $user_id, 'nexus_purchased_credits_detail', true );
        
        if ( ! is_array( $purchased_credits ) ) {
            return;
        }
        
        $remaining_to_deduct = $amount;
        
        // Sort by purchase date (oldest first - FIFO)
        usort( $purchased_credits, function( $a, $b ) {
            return $a['purchased_at'] - $b['purchased_at'];
        });
        
        foreach ( $purchased_credits as &$purchase ) {
            if ( $remaining_to_deduct <= 0 ) {
                break;
            }
            
            if ( $purchase['remaining'] > 0 ) {
                $to_deduct = min( $purchase['remaining'], $remaining_to_deduct );
                $purchase['remaining'] -= $to_deduct;
                $remaining_to_deduct -= $to_deduct;
            }
        }
        
        update_user_meta( $user_id, 'nexus_purchased_credits_detail', $purchased_credits );
    }
    
    /**
     * Log credit usage
     */
    private function log_credit_usage( $amount, $type, $user_id ) {
        $log = get_user_meta( $user_id, 'nexus_credit_log', true );
        
        if ( ! is_array( $log ) ) {
            $log = array();
        }
        
        $log[] = array(
            'amount' => $amount,
            'type' => $type, // 'ai_generation', 'purchase', 'refund'
            'timestamp' => time(),
            'balance_after' => $this->get_available_credits( $user_id ),
        );
        
        // Keep last 100 entries
        $log = array_slice( $log, -100 );
        
        update_user_meta( $user_id, 'nexus_credit_log', $log );
    }
    
    /**
     * Get credit usage history
     */
    public function get_credit_history( $user_id = null, $limit = 50 ) {
        $user_id = $user_id ?: get_current_user_id();
        
        $log = get_user_meta( $user_id, 'nexus_credit_log', true );
        
        if ( ! is_array( $log ) ) {
            return array();
        }
        
        return array_slice( array_reverse( $log ), 0, $limit );
    }
    
    /**
     * Check and perform monthly reset
     */
    public function check_monthly_reset() {
        $last_reset = get_option( 'nexus_credits_last_reset' );
        $current_month = date( 'Y-m' );
        
        if ( $last_reset !== $current_month ) {
            $this->perform_monthly_reset();
            update_option( 'nexus_credits_last_reset', $current_month );
        }
    }
    
    /**
     * Perform monthly reset and rollover
     */
    private function perform_monthly_reset() {
        // Get all users with Nexus licenses
        $users = get_users( array(
            'meta_key' => 'nexus_license_tier',
            'meta_compare' => 'EXISTS',
        ) );
        
        foreach ( $users as $user ) {
            $used = $this->get_credits_used( $user->ID );
            $monthly_limit = $this->get_monthly_credits();
            
            // Calculate rollover (max 20% of monthly allocation)
            if ( $used < $monthly_limit ) {
                $unused = $monthly_limit - $used;
                $rollover = min( $unused, floor( $monthly_limit * 0.2 ) );
                
                if ( $rollover > 0 ) {
                    $current_rollover = $this->get_rollover_credits( $user->ID );
                    update_user_meta( $user->ID, 'nexus_rollover_credits', $current_rollover + $rollover );
                }
            }
        }
    }
    
    /**
     * Get credit price for user's tier
     */
    public function get_credit_price() {
        if ( ! class_exists( 'Nexus_License_Manager' ) ) {
            return self::ADVANCED_CREDIT_PRICE;
        }
        
        $license = Nexus_License_Manager::get_instance();
        
        if ( $license->get_tier() === 'agency' ) {
            return self::AGENCY_CREDIT_PRICE;
        }
        
        return self::ADVANCED_CREDIT_PRICE;
    }
    
    /**
     * Get bulk pricing options
     */
    public function get_bulk_pricing() {
        $base_price = $this->get_credit_price();
        
        return array(
            array(
                'credits' => 100,
                'price' => $base_price * 100 * 0.9, // 10% discount
                'discount' => '10%',
            ),
            array(
                'credits' => 500,
                'price' => $base_price * 500 * 0.8, // 20% discount
                'discount' => '20%',
            ),
            array(
                'credits' => 1000,
                'price' => $base_price * 1000 * 0.7, // 30% discount
                'discount' => '30%',
            ),
        );
    }
    
    /**
     * Admin notice for low credits
     */
    public function low_credit_notice() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        $available = $this->get_available_credits();
        $monthly_limit = $this->get_monthly_credits();
        
        if ( $monthly_limit > 0 && $available < ( $monthly_limit * 0.1 ) ) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <strong><?php esc_html_e( 'Nexus AI Credits Running Low', 'nexus' ); ?></strong><br>
                    <?php
                    printf(
                        esc_html__( 'You have %d AI credits remaining. Purchase more to continue using AI features.', 'nexus' ),
                        $available
                    );
                    ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=nexus-credits' ) ); ?>" class="button button-primary" style="margin-left: 10px;">
                        <?php esc_html_e( 'Buy Credits', 'nexus' ); ?>
                    </a>
                </p>
            </div>
            <?php
        }
    }
    
    /**
     * AJAX: Get credit status
     */
    public function ajax_get_credit_status() {
        check_ajax_referer( 'nexus_credits_nonce', 'nonce' );
        
        $status = array(
            'available' => $this->get_available_credits(),
            'monthly' => $this->get_monthly_credits(),
            'used' => $this->get_credits_used(),
            'purchased' => $this->get_purchased_credits(),
            'rollover' => $this->get_rollover_credits(),
            'price_per_credit' => $this->get_credit_price(),
        );
        
        wp_send_json_success( $status );
    }
}

// Initialize
Nexus_Credit_Manager::get_instance();
