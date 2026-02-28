<?php
/**
 * Template Name: UL/NEC Billing
 * Description: Billing and subscription management for UL/NEC Compliance Checker
 */

// Disable WordPress admin bar
show_admin_bar(false);

get_header();

$payment_method_url = function_exists( 'ulnec_get_add_payment_method_url' ) ? ulnec_get_add_payment_method_url() : home_url( '/billing/' );
?>

<style>
    /* Hide default WordPress elements */
    #site-header,
    .site-header,
    .page-header,
    .entry-header,
    #breadcrumbs {
        display: none !important;
    }
    
    body {
        background: #f8fafc;
        margin: 0;
        padding: 0;
    }
    
    .ulnec-billing-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    
    .ulnec-billing-header {
        text-align: center;
        margin-bottom: 50px;
    }
    
    .ulnec-billing-header h1 {
        font-size: 36px;
        color: #f8fafc !important;
        margin: 0 0 10px 0;
    }
    
    .ulnec-billing-header p {
        font-size: 18px;
        color: #cbd5e1 !important;
        margin: 0;
    }
    
    /* Pricing Grid */
    .pricing-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }
    
    .pricing-card {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 35px;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .pricing-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .pricing-card.featured {
        border-color: #3b82f6;
        box-shadow: 0 10px 40px rgba(59, 130, 246, 0.2);
    }
    
    .pricing-card .badge {
        position: absolute;
        top: -12px;
        right: 30px;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .pricing-card h3 {
        font-size: 24px;
        color: #1e293b;
        margin: 0 0 10px 0;
    }
    
    .pricing-card .price {
        font-size: 48px;
        font-weight: 700;
        color: #1e293b;
        margin: 20px 0;
    }
    
    .pricing-card .price span {
        font-size: 18px;
        color: #64748b;
        font-weight: 400;
    }
    
    .pricing-card .savings {
        background: #fef3c7;
        color: #92400e;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 20px;
    }
    
    .pricing-card .regular-note {
        color: #64748b;
        font-size: 14px;
        text-align: center;
        margin: -10px 0 20px 0;
    }
    
    .pricing-card ul {
        list-style: none;
        padding: 0;
        margin: 20px 0 30px 0;
    }
    
    .pricing-card ul li {
        padding: 10px 0;
        color: #475569;
        font-size: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .pricing-card ul li:last-child {
        border-bottom: none;
    }
    
    .btn-primary {
        display: block;
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
    }
    
    .btn-secondary {
        display: block;
        width: 100%;
        padding: 16px;
        background: white;
        color: #3b82f6;
        border: 2px solid #3b82f6;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
        background: #eff6ff;
    }
    
    /* Current Plan Section */
    .current-plan-section {
        background: white;
        border-radius: 16px;
        padding: 35px;
        margin-bottom: 30px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .current-plan-section h2 {
        margin: 0 0 20px 0;
        font-size: 24px;
        color: #1e293b;
    }
    
    .plan-status {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        background: #f0f9ff;
        border: 2px solid #bae6fd;
        border-radius: 12px;
        margin-bottom: 20px;
    }
    
    .plan-status-info h3 {
        margin: 0 0 5px 0;
        font-size: 20px;
        color: #0c4a6e;
    }
    
    .plan-status-info p {
        margin: 0;
        color: #075985;
        font-size: 14px;
    }
    
    .plan-status-badge {
        background: #0ea5e9;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }
    
    /* Payment Method Section */
    .payment-method-section {
        background: white;
        border-radius: 16px;
        padding: 35px;
        margin-bottom: 30px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .payment-method-section h2 {
        margin: 0 0 20px 0;
        font-size: 24px;
        color: #1e293b;
    }
    
    .payment-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
    }
    
    .payment-card-icon {
        width: 50px;
        height: 35px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
    }
    
    .payment-card-info {
        flex: 1;
    }
    
    .payment-card-info h4 {
        margin: 0 0 5px 0;
        font-size: 16px;
        color: #1e293b;
    }
    
    .payment-card-info p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }
    
    /* Billing History */
    .billing-history-section {
        background: white;
        border-radius: 16px;
        padding: 35px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .billing-history-section h2 {
        margin: 0 0 20px 0;
        font-size: 24px;
        color: #1e293b;
    }
    
    .billing-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .billing-table th {
        text-align: left;
        padding: 12px;
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .billing-table td {
        padding: 15px 12px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
    }
    
    .billing-table a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 600;
    }
    
    .billing-table a:hover {
        text-decoration: underline;
    }
    
    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-badge.paid {
        background: #dcfce7;
        color: #166534;
    }
    
    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }
    
    @media (max-width: 768px) {
        .pricing-grid {
            grid-template-columns: 1fr;
        }
        
        .plan-status {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }
    }
</style>

<div class="ulnec-billing-wrapper">
    <div class="ulnec-billing-header">
        <h1>⚡ Billing & Subscription</h1>
        <p>Manage your UL/NEC Compliance Checker subscription</p>
    </div>
    
    <!-- Current Plan -->
    <div class="current-plan-section">
        <h2>Current Plan</h2>
        <div class="plan-status">
            <div class="plan-status-info">
                <h3>Free Trial</h3>
                <p>29 days remaining • Ends March 26, 2026</p>
            </div>
            <span class="plan-status-badge">Active</span>
        </div>
        <p style="color: #64748b; margin: 0;">
            You're currently on the <strong>30-day free trial</strong>. No credit card required.
            Upgrade before your trial ends to lock in the beta pricing!
        </p>
    </div>
    
    <!-- Pricing Plans -->
    <div class="ulnec-billing-info">
        <h2 style="text-align: center; font-size: 28px; margin-bottom: 40px; color: #1e293b;">Choose Your Plan</h2>
        
        <div class="pricing-grid">
            <div class="pricing-card">
                <h3>Free Trial</h3>
                <p class="price">$0<span>/30 days</span></p>
                <ul>
                    <li>✓ Full feature access</li>
                    <li>✓ 10 panel checks</li>
                    <li>✓ No credit card required</li>
                    <li>✓ Instant activation</li>
                </ul>
                <button class="btn-secondary">Current Plan</button>
            </div>
            
            <div class="pricing-card featured">
                <span class="badge">Best Value</span>
                <h3>Beta Launch Special</h3>
                <p class="price">$75<span>/month forever</span></p>
                <p class="savings">🎉 Save 50% - Lock in this price for life!</p>
                <ul>
                    <li>✓ Unlimited panel checks</li>
                    <li>✓ Priority support</li>
                    <li>✓ Automated reports</li>
                    <li>✓ API access</li>
                    <li>✓ Early access to new features</li>
                    <li>✓ Lifetime price guarantee</li>
                </ul>
                <a class="btn-primary" href="<?php echo esc_url( $payment_method_url ); ?>">Subscribe Now</a>
            </div>
            
            <div class="pricing-card">
                <h3>Regular Monthly</h3>
                <p class="price">$150<span>/month</span></p>
                <p class="regular-note">Available after beta launch</p>
                <ul>
                    <li>✓ Unlimited panel checks</li>
                    <li>✓ Priority support</li>
                    <li>✓ Automated reports</li>
                    <li>✓ API access</li>
                    <li>✓ All future features</li>
                </ul>
                <a class="btn-secondary" href="<?php echo esc_url( home_url( '/ul-nec-compliance-checker/' ) ); ?>">Learn More</a>
            </div>
        </div>
    </div>
    
    <!-- Payment Method -->
    <div id="payment-method-section" class="payment-method-section">
        <h2>Payment Method</h2>
        <p style="color: #64748b; margin-bottom: 20px;">
            No payment method on file. Add a payment method to upgrade to a paid plan.
        </p>
        <a class="btn-primary" style="max-width: 300px;" href="<?php echo esc_url( $payment_method_url ); ?>">+ Add Payment Method</a>
    </div>
    
    <!-- Billing History -->
    <div class="billing-history-section">
        <h2>Billing History</h2>
        <p style="color: #64748b; margin-bottom: 20px;">
            Your billing history will appear here once you subscribe to a paid plan.
        </p>
        
        <table class="billing-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Invoice</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Feb 25, 2026</td>
                    <td>Free Trial Started</td>
                    <td>$0.00</td>
                    <td><span class="status-badge paid">Active</span></td>
                    <td>—</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div style="text-align: center; margin-top: 40px;">
        <p style="color: #64748b;">
            Need help? <a href="/contact" style="color: #3b82f6; text-decoration: none; font-weight: 600;">Contact Support</a> |
            <a href="<?php echo home_url('/dashboard'); ?>" style="color: #3b82f6; text-decoration: none; font-weight: 600;">Back to Dashboard</a>
        </p>
    </div>
</div>

<?php get_footer(); ?>
