<?php
/**
 * Template Name: Disclaimer
 * Description: Disclaimer page template.
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
        <h1>Disclaimer</h1>
        <p><strong>Last updated:</strong> <?php echo esc_html( date_i18n( 'F j, Y' ) ); ?></p>

        <h2>1. General Information</h2>
        <p>Information on this website is provided for general informational purposes only and does not constitute legal, tax, engineering, or professional advice unless explicitly stated.</p>

        <h2>2. No Warranty</h2>
        <p>We make reasonable efforts to keep content accurate and up to date, but we do not guarantee completeness, reliability, or suitability for a specific purpose.</p>

        <h2>3. Third-Party Services</h2>
        <p>Our website and software may integrate with third-party services. We are not responsible for third-party content, uptime, or policies.</p>

        <h2>4. Download and Security Notice</h2>
        <p>Software installers may trigger platform security checks (such as reputation/SmartScreen prompts). Users should download only from official links published on this site.</p>

        <h2>5. Limitation of Liability</h2>
        <p>To the extent permitted by law, we are not liable for direct or indirect loss resulting from reliance on website content, service interruption, or use of downloaded files.</p>

        <h2>6. Contact</h2>
        <p>For concerns, contact: <strong>support@jdsancontrols.com</strong></p>
    </article>
</div>

<?php get_footer(); ?>
