<?php
/**
 * Template Name: UL/NEC Login Page
 * Description: Clean login page for UL/NEC Compliance Checker
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
    
    .ulnec-login-container {
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        max-width: 450px;
        width: 100%;
        padding: 0;
        overflow: hidden;
    }
    
    .ulnec-login-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: white;
        padding: 40px 40px 30px;
        text-align: center;
    }
    
    .ulnec-login-header h1 {
        margin: 0 0 10px 0;
        font-size: 28px;
        font-weight: 700;
    }
    
    .ulnec-login-header .logo {
        font-size: 48px;
        margin-bottom: 15px;
    }
    
    .ulnec-login-header p {
        margin: 0;
        opacity: 0.9;
        font-size: 14px;
    }
    
    .ulnec-login-body {
        padding: 40px;
    }
    
    .ulnec-login-body h2 {
        margin: 0 0 25px 0;
        font-size: 24px;
        color: #1e293b;
    }
    
    /* Style the shortcode output */
    .ulnec-login-body form {
        margin: 0;
    }
    
    .ulnec-login-body .form-group {
        margin-bottom: 20px;
    }
    
    .ulnec-login-body label {
        display: block;
        margin-bottom: 8px;
        color: #475569;
        font-weight: 500;
        font-size: 14px;
    }
    
    .ulnec-login-body input[type="text"],
    .ulnec-login-body input[type="email"],
    .ulnec-login-body input[type="password"] {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }
    
    .ulnec-login-body input[type="text"]:focus,
    .ulnec-login-body input[type="email"]:focus,
    .ulnec-login-body input[type="password"]:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .ulnec-login-body button[type="submit"],
    .ulnec-login-body input[type="submit"] {
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
    
    .ulnec-login-body button[type="submit"]:hover,
    .ulnec-login-body input[type="submit"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
    }
    
    .ulnec-login-footer {
        padding: 25px 40px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        text-align: center;
    }
    
    .ulnec-login-footer p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }
    
    .ulnec-login-footer a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 600;
    }
    
    .ulnec-login-footer a:hover {
        text-decoration: underline;
    }
    
    .ulnec-remember-forgot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 15px 0;
        font-size: 14px;
    }
    
    .ulnec-remember-forgot a {
        color: #3b82f6;
        text-decoration: none;
    }
    
    .ulnec-remember-forgot a:hover {
        text-decoration: underline;
    }
    
    .ulnec-social-login {
        margin: 25px 0;
        padding: 25px 0;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .ulnec-social-login p {
        text-align: center;
        color: #64748b;
        font-size: 14px;
        margin-bottom: 15px;
    }
    
    .ulnec-social-buttons {
        display: grid;
        gap: 10px;
    }
    
    .ulnec-social-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        background: white;
        color: #475569;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .ulnec-social-btn:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    
    @media (max-width: 480px) {
        .ulnec-login-header,
        .ulnec-login-body,
        .ulnec-login-footer {
            padding: 30px 25px;
        }
    }
</style>

<div class="ulnec-login-container">
    <div class="ulnec-login-header">
        <div class="logo">⚡</div>
        <h1>UL/NEC Compliance Checker</h1>
        <p>Automated UL508A & NEC Validation for AutoCAD</p>
    </div>
    
    <div class="ulnec-login-body">
        <h2>Welcome Back</h2>
        
        <?php
        // Display the login shortcode
        the_content();
        ?>
        
    </div>
    
    <div class="ulnec-login-footer">
        <p>Don't have an account? <a href="<?php echo home_url('/register'); ?>">Sign up for free trial</a></p>
        <p style="margin-top: 10px;"><a href="<?php echo home_url('/ul-nec-compliance-checker'); ?>">← Back to home</a></p>
    </div>
</div>

<?php get_footer(); ?>
