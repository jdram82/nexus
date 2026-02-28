<?php
/**
 * Template Name: UL/NEC Login Page
 * Description: Clean login page for UL/NEC Compliance Checker
 */

// Disable WordPress admin bar
show_admin_bar(false);

get_header();

$login_output = '';
if ( shortcode_exists( 'ulnec_login' ) ) {
    $login_output = do_shortcode( '[ulnec_login]' );
}

$use_fallback_login_form = empty( trim( wp_strip_all_tags( (string) $login_output ) ) );
$fallback_show_lost_password = isset( $_GET['action'] ) && sanitize_key( wp_unslash( $_GET['action'] ) ) === 'lostpassword';

$fallback_login_error_message = '';
$fallback_login_notice_message = '';

if ( isset( $_GET['verified'] ) ) {
    $verified_state = sanitize_text_field( wp_unslash( $_GET['verified'] ) );
    if ( $verified_state === '1' ) {
        $fallback_login_notice_message = 'Email verified successfully. You can now log in.';
    } elseif ( $verified_state === 'expired' ) {
        $fallback_login_error_message = 'Verification link has expired. Please register again or contact support.';
    } elseif ( $verified_state === 'invalid' ) {
        $fallback_login_error_message = 'Invalid verification link. Please use the latest email link.';
    }
}

if ( $use_fallback_login_form && $fallback_show_lost_password && isset( $_POST['fallback_lost_password_user'] ) ) {
    $lost_nonce = isset( $_POST['fallback_lost_password_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['fallback_lost_password_nonce'] ) ) : '';

    if ( ! wp_verify_nonce( $lost_nonce, 'fallback_lost_password_action' ) ) {
        $fallback_login_error_message = 'Security check failed. Please try again.';
    } else {
        $lost_login = isset( $_POST['fallback_lost_user_login'] ) ? sanitize_text_field( wp_unslash( $_POST['fallback_lost_user_login'] ) ) : '';

        if ( empty( $lost_login ) ) {
            $fallback_login_error_message = 'Please enter your username or email address.';
        } else {
            $lost_result = retrieve_password( $lost_login );

            if ( is_wp_error( $lost_result ) ) {
                $fallback_login_error_message = 'We could not process that request. Please verify your username/email and try again.';
            } else {
                $fallback_login_notice_message = 'Password reset instructions have been sent to your email address.';
            }
        }
    }
}

if ( $use_fallback_login_form && ! $fallback_show_lost_password && isset( $_POST['fallback_login_user'] ) ) {
    $nonce = isset( $_POST['fallback_login_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['fallback_login_nonce'] ) ) : '';

    if ( ! wp_verify_nonce( $nonce, 'fallback_login_action' ) ) {
        $fallback_login_error_message = 'Security check failed. Please try again.';
    } else {
        $login_input = isset( $_POST['fallback_login'] ) ? sanitize_text_field( wp_unslash( $_POST['fallback_login'] ) ) : '';
        $password = isset( $_POST['fallback_password'] ) ? (string) wp_unslash( $_POST['fallback_password'] ) : '';
        $remember = ! empty( $_POST['fallback_remember'] );

        if ( empty( $login_input ) || empty( $password ) ) {
            $fallback_login_error_message = 'Please enter username/email and password.';
        } else {
            $wp_login = $login_input;
            if ( is_email( $login_input ) ) {
                $user_by_email = get_user_by( 'email', $login_input );
                if ( $user_by_email instanceof WP_User ) {
                    $wp_login = $user_by_email->user_login;
                }
            }

            $auth_user = wp_signon(
                array(
                    'user_login'    => $wp_login,
                    'user_password' => $password,
                    'remember'      => $remember,
                ),
                is_ssl()
            );

            if ( is_wp_error( $auth_user ) ) {
                if ( in_array( $auth_user->get_error_code(), array( 'ulnec_email_not_confirmed', 'ulnec_account_suspended' ), true ) ) {
                    $fallback_login_error_message = $auth_user->get_error_message();
                } else {
                    $fallback_login_error_message = 'Invalid login credentials. Please try again.';
                }
            } else {
                wp_set_current_user( $auth_user->ID );
                wp_set_auth_cookie( $auth_user->ID, $remember );

                $post_login_url = function_exists( 'ulnec_get_requested_redirect_url' ) ? ulnec_get_requested_redirect_url() : '';
                if ( empty( $post_login_url ) && function_exists( 'ulnec_get_default_post_login_url' ) ) {
                    $post_login_url = ulnec_get_default_post_login_url();
                }
                if ( empty( $post_login_url ) ) {
                    $post_login_url = home_url( '/download/' );
                }

                wp_safe_redirect( $post_login_url );
                exit;
            }
        }
    }
}
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

    .ulnec-login-body .forgot-link,
    .ulnec-login-body .register-link,
    .ulnec-login-body .already-logged,
    .ulnec-login-body .dashboard-link {
        text-align: center;
        margin-top: 12px;
        display: block;
    }

    .ulnec-login-body .dashboard-link {
        color: #3b82f6;
        font-weight: 600;
        text-decoration: none;
    }

    .ulnec-login-body .dashboard-link:hover {
        text-decoration: underline;
    }

    .ulnec-login-body .error-message {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 16px;
        font-size: 14px;
    }

    .ulnec-login-body .success-message {
        background: #edfdf3;
        border: 1px solid #bbf7d0;
        color: #166534;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 16px;
        font-size: 14px;
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
        <h1>PanelcheckPRO</h1>
        <p>Automated UL508A & NEC Validation for AutoCAD</p>
    </div>
    
    <div class="ulnec-login-body">
        <h2>Welcome Back</h2>

        <?php if ( ! $use_fallback_login_form ) : ?>
            <?php echo $login_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php else : ?>
            <?php if ( ! is_user_logged_in() ) : ?>
                <?php if ( ! empty( $fallback_login_notice_message ) ) : ?>
                    <div class="success-message"><?php echo esc_html( $fallback_login_notice_message ); ?></div>
                <?php endif; ?>

                <?php if ( ! empty( $fallback_login_error_message ) ) : ?>
                    <div class="error-message"><?php echo esc_html( $fallback_login_error_message ); ?></div>
                <?php endif; ?>

                <?php if ( $fallback_show_lost_password ) : ?>
                <form method="post" action="">
                    <?php wp_nonce_field( 'fallback_lost_password_action', 'fallback_lost_password_nonce' ); ?>

                    <p class="form-group">
                        <label for="fallback_lost_user_login">Email or Username</label>
                        <input type="text" name="fallback_lost_user_login" id="fallback_lost_user_login" value="<?php echo esc_attr( isset( $_POST['fallback_lost_user_login'] ) ? sanitize_text_field( wp_unslash( $_POST['fallback_lost_user_login'] ) ) : '' ); ?>" autocomplete="username" required>
                    </p>

                    <button type="submit" name="fallback_lost_password_user">Send Reset Link</button>
                </form>

                <p class="register-link"><a href="<?php echo esc_url( home_url( '/login' ) ); ?>">Back to Login</a></p>
                <?php else : ?>
                <form method="post" action="">
                    <?php wp_nonce_field( 'fallback_login_action', 'fallback_login_nonce' ); ?>

                    <p class="form-group">
                        <label for="fallback_login">Email or Username</label>
                        <input type="text" name="fallback_login" id="fallback_login" value="<?php echo esc_attr( isset( $_POST['fallback_login'] ) ? sanitize_text_field( wp_unslash( $_POST['fallback_login'] ) ) : '' ); ?>" autocomplete="username" required>
                    </p>

                    <p class="form-group">
                        <label for="fallback_password">Password</label>
                        <input type="password" name="fallback_password" id="fallback_password" autocomplete="current-password" required>
                    </p>

                    <p class="form-group">
                        <label style="display:flex;align-items:center;gap:8px;">
                            <input type="checkbox" name="fallback_remember" value="1" style="width:auto;">
                            <span>Remember Me</span>
                        </label>
                    </p>

                    <button type="submit" name="fallback_login_user">Login</button>
                </form>

                <p class="register-link">Don't have an account? <a href="<?php echo esc_url( home_url( '/register' ) ); ?>">Register here</a></p>
                <p class="forgot-link"><a href="<?php echo esc_url( home_url( '/login/?action=lostpassword' ) ); ?>">Forgot Password?</a></p>
                <?php endif; ?>
            <?php else : ?>
                <p class="already-logged">You are already logged in.</p>
                <a class="dashboard-link" href="<?php echo esc_url( home_url( '/download' ) ); ?>">Go to Download</a>
            <?php endif; ?>
        <?php endif; ?>
        
    </div>
    
    <div class="ulnec-login-footer">
        <p>Don't have an account? <a href="<?php echo home_url('/register'); ?>">Sign up for free trial</a></p>
        <p style="margin-top: 10px;"><a href="<?php echo home_url('/ul-nec-compliance-checker'); ?>">← Back to home</a></p>
    </div>
</div>

<?php get_footer(); ?>
