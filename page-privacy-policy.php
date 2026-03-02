<?php
/**
 * Template Name: Privacy Policy
 * Description: Privacy Policy page for legal compliance and trust disclosures.
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
    .legal-note { background: #f8fafc; border-left: 4px solid #2563eb; padding: 12px 14px; border-radius: 8px; }
    @media (max-width: 768px) { .legal-card { padding: 24px; } }
</style>

<div class="legal-page-wrap">
    <article class="legal-card">
        <h1>Privacy Policy</h1>
        <p><strong>Last updated:</strong> <?php echo esc_html( date_i18n( 'F j, Y' ) ); ?></p>

        <p>This Privacy Policy explains how <strong>Jdsan Controls</strong> ("we", "us", "our") collects, uses, stores, and protects personal data when you use our website and software download services.</p>

        <div class="legal-note">
            <p><strong>Jurisdiction:</strong> This policy is intended for an India-based business and aligned with applicable Indian law, including the Digital Personal Data Protection Act, 2023 (DPDP Act), Information Technology Act, 2000, and related rules.</p>
        </div>

        <h2>1. Personal Information We Collect</h2>
        <ul>
            <li>Identity and account data: name, email address, username, password hash.</li>
            <li>Transaction and billing data: payment status, license tier, subscription records (payment card data is processed by third-party payment providers).</li>
            <li>Technical data: IP address, browser type, user-agent, device/session metadata.</li>
            <li>Usage data: login activity, download activity, support requests, bug reports, and feature requests.</li>
            <li>Communication data: messages submitted through contact/support forms.</li>
        </ul>

        <h2>2. Why We Collect Personal Information</h2>
        <ul>
            <li>To create and manage user accounts.</li>
            <li>To issue and validate licenses.</li>
            <li>To deliver secure software downloads.</li>
            <li>To provide customer support and respond to requests.</li>
            <li>To prevent fraud, abuse, and unauthorized access.</li>
            <li>To comply with legal, regulatory, and contractual obligations.</li>
        </ul>

        <h2>3. Legal Basis and Consent</h2>
        <p>We process personal data for legitimate business purposes, performance of contract, legal compliance, and where required, based on user consent.</p>

        <h2>4. Sharing of Data</h2>
        <p>We may share limited data with trusted service providers (for example: hosting, storage, analytics, email, and payment processing) only as necessary to operate services. We do not sell personal data.</p>

        <h2>5. Data Retention</h2>
        <p>We retain personal data only as long as necessary for account management, support, compliance, dispute resolution, and security auditing.</p>

        <h2>6. Security Measures</h2>
        <p>We use reasonable technical and organizational safeguards, including access controls, authentication controls, and transport security, to protect personal data.</p>

        <h2>7. Your Rights</h2>
        <p>Subject to applicable law, you may request access, correction, update, deletion, or withdrawal of consent regarding your personal data.</p>

        <h2>8. Cookies and Tracking</h2>
        <p>We may use essential cookies/session storage for login, security, and service functionality. Optional analytics cookies may be used where configured.</p>

        <h2>9. Cross-Border Processing</h2>
        <p>Your data may be processed on servers located outside India by our service providers, subject to applicable safeguards and lawful transfer mechanisms.</p>

        <h2>10. Grievance and Contact</h2>
        <p>For privacy concerns or rights requests, contact:</p>
        <p>
            <strong>Grievance Officer:</strong> [Add Name]<br>
            <strong>Email:</strong> [Add Privacy Email]<br>
            <strong>Address:</strong> [Add Business Address]
        </p>

        <h2>11. Policy Updates</h2>
        <p>We may update this policy periodically. Continued use of the website after updates indicates acceptance of the revised policy.</p>
    </article>
</div>

<?php get_footer(); ?>
