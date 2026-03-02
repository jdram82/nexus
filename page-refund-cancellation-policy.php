<?php
/**
 * Template Name: Refund & Cancellation Policy
 * Description: Refund and cancellation policy page template.
 */

show_admin_bar( false );
get_header();
?>

<style>
    .legal-page-wrap { max-width: 980px; margin: 40px auto; padding: 0 20px; }
    .legal-card { background: #fff; border-radius: 14px; padding: 32px; box-shadow: 0 8px 24px rgba(0,0,0,.06); }
    .legal-card h1 { margin-top: 0; }
    .legal-card h2 { margin-top: 28px; }
    .legal-card p, .legal-card li { line-height: 1.7; }
    @media (max-width: 768px) { .legal-card { padding: 24px; } }
</style>

<div class="legal-page-wrap">
    <article class="legal-card">
        <h1>Refund &amp; Cancellation Policy</h1>
        <p><strong>Last updated:</strong> <?php echo esc_html( date_i18n( 'F j, Y' ) ); ?></p>

        <h2>1. Scope</h2>
        <p>This policy applies to purchases/subscriptions for software licenses and related digital services sold through this website.</p>

        <h2>2. Cancellation</h2>
        <ul>
            <li>You may cancel auto-renewal any time before the next billing cycle.</li>
            <li>Cancellation stops future billing and does not retroactively cancel already consumed periods.</li>
        </ul>

        <h2>3. Refund Eligibility</h2>
        <ul>
            <li>Refund requests must be submitted within <strong>[Add Refund Window, e.g., 7 days]</strong> of purchase.</li>
            <li>Refunds may be approved for duplicate payments, technical non-delivery, or accidental overcharge.</li>
            <li>Refunds may be denied where service was substantially consumed or where abuse/fraud is detected.</li>
        </ul>

        <h2>4. Non-Refundable Cases</h2>
        <ul>
            <li>Change of mind after successful activation and substantial use.</li>
            <li>Failure to meet unsupported system requirements disclosed in documentation.</li>
            <li>Downtime/outages caused by third-party infrastructure beyond reasonable control.</li>
        </ul>

        <h2>5. Refund Process</h2>
        <p>To request a refund, email <strong>[Add Billing Email]</strong> with your registered email, order ID, and reason. Approved refunds are processed to the original payment method within <strong>[Add Timeline, e.g., 7-14 business days]</strong>.</p>

        <h2>6. Chargebacks</h2>
        <p>If you raise a chargeback without contacting support first, we reserve the right to suspend related accounts/licenses during investigation.</p>

        <h2>7. Contact</h2>
        <p>Billing and refund support: <strong>[Add Billing Email]</strong></p>
    </article>
</div>

<?php get_footer(); ?>
