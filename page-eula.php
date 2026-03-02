<?php
/**
 * Template Name: EULA
 * Description: End User License Agreement page template.
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
        <h1>End User License Agreement (EULA)</h1>
        <p><strong>Last updated:</strong> <?php echo esc_html( date_i18n( 'F j, Y' ) ); ?></p>

        <p>This EULA is a legal agreement between you ("End User") and <strong>Jdsan Controls</strong> for use of the UL/NEC software and related installers.</p>

        <h2>1. License Grant</h2>
        <p>Subject to compliance with this EULA and valid subscription/license terms, we grant you a limited, non-exclusive, non-transferable, revocable license to install and use the software.</p>

        <h2>2. Restrictions</h2>
        <ul>
            <li>No sublicensing, resale, or unauthorized distribution.</li>
            <li>No reverse engineering, decompilation, or disassembly except as permitted by law.</li>
            <li>No use for unlawful, malicious, or abusive purposes.</li>
        </ul>

        <h2>3. Ownership</h2>
        <p>All rights, title, and interest in the software remain with us and/or our licensors. This EULA grants use rights only and does not transfer ownership.</p>

        <h2>4. Updates and Support</h2>
        <p>Updates may be released from time to time. Access to updates and support may depend on your active plan/license.</p>

        <h2>5. Term and Termination</h2>
        <p>This license remains effective until terminated. We may suspend or terminate your license for breach of terms, misuse, or legal non-compliance.</p>

        <h2>6. Disclaimer and Liability</h2>
        <p>The software is provided "as is" without warranties of any kind to the extent permitted by law. Our liability is limited to the maximum extent permitted under applicable law.</p>

        <h2>7. Governing Law</h2>
        <p>This EULA is governed by the laws of India. Courts at <strong>[Add City, India]</strong> shall have jurisdiction, subject to applicable law.</p>

        <h2>8. Contact</h2>
        <p>For licensing support: <strong>[Add Licensing Email]</strong></p>
    </article>
</div>

<?php get_footer(); ?>
