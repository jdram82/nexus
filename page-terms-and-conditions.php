<?php
/**
 * Template Name: Terms and Conditions
 * Description: Terms and Conditions page template.
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
        <h1>Terms and Conditions</h1>
        <p><strong>Last updated:</strong> <?php echo esc_html( date_i18n( 'F j, Y' ) ); ?></p>

        <p>These Terms and Conditions govern use of <strong>Jdsan Controls</strong> website and related products/services. By using the site, you agree to these terms.</p>

        <h2>1. Eligibility</h2>
        <p>You must be legally capable of entering into a binding agreement under applicable law.</p>

        <h2>2. Accounts</h2>
        <ul>
            <li>You are responsible for maintaining account confidentiality.</li>
            <li>You are responsible for all activities under your account.</li>
            <li>We may suspend accounts for policy violations or suspicious activity.</li>
        </ul>

        <h2>3. Licenses and Downloads</h2>
        <ul>
            <li>Software downloads are provided only to eligible users/licenses.</li>
            <li>License misuse, unauthorized sharing, or reverse engineering is prohibited unless permitted by law.</li>
            <li>We may rotate build versions and update download links for security and support reasons.</li>
        </ul>

        <h2>4. Payments and Subscriptions</h2>
        <p>Pricing, billing cycles, and inclusions are displayed at checkout. Taxes, where applicable, may be added based on law.</p>

        <h2>5. Acceptable Use</h2>
        <p>You agree not to misuse the website, attempt unauthorized access, disrupt services, upload malware, or violate applicable law.</p>

        <h2>6. Intellectual Property</h2>
        <p>All site content, trademarks, and proprietary materials are owned by us or licensed to us, except open-source components used under their respective licenses.</p>

        <h2>7. Disclaimer of Warranties</h2>
        <p>Services are provided on an "as is" and "as available" basis. We do not warrant uninterrupted or error-free operation.</p>

        <h2>8. Limitation of Liability</h2>
        <p>To the maximum extent permitted by law, we are not liable for indirect, incidental, special, or consequential damages arising from use of our services.</p>

        <h2>9. Termination</h2>
        <p>We may suspend or terminate access for breach of these terms or legal non-compliance.</p>

        <h2>10. Governing Law and Jurisdiction</h2>
        <p>These terms are governed by the laws of India. Courts at <strong>Chennai, India</strong> shall have jurisdiction, subject to applicable consumer law.</p>

        <h2>11. Contact</h2>
        <p>For legal queries: <strong>support@jdsancontrols.com</strong></p>
    </article>
</div>

<?php get_footer(); ?>
