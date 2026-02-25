<?php
/**
 * Template Name: UL/NEC Register Page
 * Description: Clean registration page for UL/NEC Compliance Checker
 */

// Disable WordPress admin bar
show_admin_bar(false);

get_header();
?>

<style>
    /* Hide default WordPress elements */
    #site-header,
    .site-header,
    .page-header,
    .entry-header,
    #breadcrumbs,
    .site-footer,
    #site-footer,
    footer {
        display: none !important;
    }
    
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        padding: 20px;
    }
    
    .ulnec-register-container {
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        max-width: 500px;
        width: 100%;
        padding: 0;
        overflow: hidden;
    }
    
    .ulnec-register-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: white;
        padding: 40px 40px 30px;
        text-align: center;
    }
    
    .ulnec-register-header h1 {
        margin: 0 0 10px 0;
        font-size: 28px;
        font-weight: 700;
    }
    
    .ulnec-register-header .logo {
        font-size: 48px;
        margin-bottom: 15px;
    }
    
    .ulnec-register-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 14px;
    }
    
    .ulnec-register-body {
        padding: 40px;
    }
    
    .ulnec-register-body h2 {
        margin: 0 0 10px 0;
        font-size: 24px;
        color: #1e293b;
    }
    
    .ulnec-beta-badge {
        display: inline-block;
        background: #fbbf24;
        color: #78350f;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .ulnec-benefits {
        background: #f0f9ff;
        border: 2px solid #bae6fd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
    }
    
    .ulnec-benefits h3 {
        margin: 0 0 15px 0;
        font-size: 16px;
        color: #0c4a6e;
    }
    
    .ulnec-benefits ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    
    .ulnec-benefits li {
        padding: 5px 0;
        color: #0c4a6e;
        font-size: 14px;
    }
    
    .ulnec-benefits li:before {
        content: "✓ ";
        color: #0ea5e9;
        font-weight: bold;
        margin-right: 8px;
    }
    
    /* Style the shortcode output */
    .ulnec-register-body form {
        margin: 0;
    }
    
    .ulnec-register-body .form-group {
        margin-bottom: 20px;
    }
    
    .ulnec-register-body label {
        display: block;
        margin-bottom: 8px;
        color: #475569;
        font-weight: 500;
        font-size: 14px;
    }
    
    .ulnec-register-body input[type="text"],
    .ulnec-register-body input[type="email"],
    .ulnec-register-body input[type="password"],
    .ulnec-register-body select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }
    
    .ulnec-register-body input:focus,
    .ulnec-register-body select:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .ulnec-register-body button[type="submit"],
    .ulnec-register-body input[type="submit"] {
        width: 100%;
        padding: 14px 24px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 10px;
    }
    
    .ulnec-register-body button[type="submit"]:hover,
    .ulnec-register-body input[type="submit"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
    }
    
    .ulnec-register-footer {
        padding: 25px 40px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        text-align: center;
    }
    
    .ulnec-register-footer p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }
    
    .ulnec-register-footer a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 600;
    }
    
    .ulnec-register-footer a:hover {
        text-decoration: underline;
    }
    
    .ulnec-terms {
        font-size: 12px;
        color: #64748b;
        margin-top: 15px;
        line-height: 1.5;
    }
    
    .ulnec-terms a {
        color: #3b82f6;
        text-decoration: none;
    }
    
    @media (max-width: 480px) {
        .ulnec-register-header,
        .ulnec-register-body,
        .ulnec-register-footer {
            padding: 30px 25px;
        }
    }
</style>

<div class="ulnec-register-container">
    <div class="ulnec-register-header">
        <div class="logo">⚡</div>
        <h1>UL/NEC Compliance Checker</h1>
        <p>Start Your 30-Day Free Trial Today</p>
    </div>
    
    <div class="ulnec-register-body">
        <h2>Create Your Account</h2>
        <span class="ulnec-beta-badge">🎉 BETA LAUNCH: Lock in $75/mo Forever!</span>
        
        <div class="ulnec-benefits">
            <h3>What You Get:</h3>
            <ul>
                <li>30-day free trial (no credit card)</li>
                <li>Save 15-20 hours per panel</li>
                <li>Automated UL508A & NEC validation</li>
                <li>1,200+ compliance rules</li>
                <li>10,000+ component database</li>
                <li>Priority email support</li>
            </ul>
        </div>
        
        <?php
        // Display the registration shortcode
        the_content();
        ?>
        
        <div class="ulnec-terms">
            By creating an account, you agree to our <a href="/terms">Terms of Service</a> and <a href="/privacy">Privacy Policy</a>.
        </div>
    </div>
    
    <div class="ulnec-register-footer">
        <p>Already have an account? <a href="<?php echo home_url('/login'); ?>">Log in</a></p>
        <p style="margin-top: 10px;"><a href="<?php echo home_url('/ul-nec-compliance-checker'); ?>">← Back to home</a></p>
    </div>
</div>

<?php get_footer(); ?>
