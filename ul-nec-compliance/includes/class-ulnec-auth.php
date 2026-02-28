<?php
/**
 * Authentication Class
 * 
 * Handles user registration, login, and WordPress-Supabase sync
 */

if (!defined('ABSPATH')) {
    exit;
}

class ULNEC_Auth {
    
    private $supabase;
    
    public function __construct($supabase) {
        $this->supabase = $supabase;
        
        // Sync WordPress user to Supabase on registration
        add_action('user_register', [$this, 'sync_user_on_registration'], 10, 1);
        
        // Sync WordPress user on profile update
        add_action('profile_update', [$this, 'sync_user_on_update'], 10, 2);

        // Handle WordPress email verification links
        add_action('init', [$this, 'handle_email_verification']);

        // Enforce email confirmation before login succeeds
        add_filter('authenticate', [$this, 'enforce_email_confirmation'], 40, 3);

        // Allow login fallback against Supabase Auth
        add_filter('authenticate', [$this, 'authenticate_via_supabase'], 30, 3);
    }
    
    /**
     * Sync WordPress user to Supabase on registration
     */
    public function sync_user_on_registration($user_id) {
        $user = get_userdata($user_id);
        
        if (!$user) {
            return;
        }
        
        // Check if user already exists in Supabase
        $existing = $this->supabase->get_user_by_wordpress_id($user_id);
        
        if (!$existing && !is_wp_error($existing)) {
            // Create new Supabase user
            $this->supabase->create_user([
                'wordpress_user_id' => $user_id,
                'email' => $user->user_email,
                'name' => $user->display_name,
                'tier' => 'free'
            ]);
        }
    }

    /**
     * Ensure Supabase Auth account exists for a WP user.
     */
    public function ensure_supabase_auth_user($user_id, $plain_password) {
        $user = get_userdata($user_id);
        if (!$user) {
            return new WP_Error('invalid_user', 'Invalid WordPress user');
        }

        $auth_result = $this->supabase->sign_up_user(
            $user->user_email,
            $plain_password,
            [
                'wordpress_user_id' => (int) $user_id,
                'username' => $user->user_login,
            ]
        );

        if (is_wp_error($auth_result)) {
            $message = strtolower($auth_result->get_error_message());
            if (strpos($message, 'already') !== false || strpos($message, 'exists') !== false || strpos($message, 'registered') !== false) {
                update_user_meta($user_id, 'ulnec_supabase_auth_status', 'exists');
                return true;
            }

            update_user_meta($user_id, 'ulnec_supabase_auth_status', 'failed');
            update_user_meta($user_id, 'ulnec_supabase_auth_last_error', $auth_result->get_error_message());
            return $auth_result;
        }

        update_user_meta($user_id, 'ulnec_supabase_auth_status', 'synced');
        update_user_meta($user_id, 'ulnec_email_verification_required', '1');
        update_user_meta($user_id, 'ulnec_email_verified', '0');

        if (is_array($auth_result) && !empty($auth_result['id'])) {
            update_user_meta($user_id, 'ulnec_supabase_auth_id', sanitize_text_field($auth_result['id']));
        } elseif (is_array($auth_result) && !empty($auth_result['user']['id'])) {
            update_user_meta($user_id, 'ulnec_supabase_auth_id', sanitize_text_field($auth_result['user']['id']));
        }

        return true;
    }

    /**
     * Force email verification before first successful login.
     */
    public function enforce_email_confirmation($user, $username, $password) {
        if (!$user instanceof WP_User) {
            return $user;
        }

        $account_status = get_user_meta($user->ID, 'ulnec_account_status', true);
        if ($account_status === 'suspended') {
            return new WP_Error(
                'ulnec_account_suspended',
                'Your account is suspended. Please contact support.'
            );
        }

        $auth_required = get_user_meta($user->ID, 'ulnec_email_verification_required', true) === '1';
        $is_verified = get_user_meta($user->ID, 'ulnec_email_verified', true) === '1';

        if (!$auth_required || $is_verified) {
            return $user;
        }

        return new WP_Error(
            'ulnec_email_not_confirmed',
            'Please verify your email first. Check your inbox for the confirmation link before logging in.'
        );
    }

    /**
     * Register user with redundancy (WordPress + Supabase Auth + Supabase profile table).
     */
    public function register_with_redundancy($username, $email, $password) {
        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            return $user_id;
        }

        $this->sync_user_on_registration($user_id);

        $token = wp_generate_password(32, false, false);
        $token_hash = wp_hash_password($token);
        $expires = time() + DAY_IN_SECONDS;

        update_user_meta($user_id, 'ulnec_email_verification_required', '1');
        update_user_meta($user_id, 'ulnec_email_verified', '0');
        update_user_meta($user_id, 'ulnec_account_status', 'active');
        update_user_meta($user_id, 'ulnec_email_verify_token_hash', $token_hash);
        update_user_meta($user_id, 'ulnec_email_verify_expires', (string) $expires);

        $mail_result = $this->send_verification_email($user_id, $token);

        if (is_wp_error($mail_result)) {
            if (!function_exists('wp_delete_user')) {
                require_once ABSPATH . 'wp-admin/includes/user.php';
            }

            wp_delete_user($user_id);
            return new WP_Error('verification_email_failed', 'Unable to send verification email. Please try again later.');
        }

        return [
            'user_id' => (int) $user_id,
            'supabase_auth_synced' => false,
            'supabase_auth_error' => '',
            'requires_email_verification' => true,
        ];
    }

    /**
     * Send verification email via WordPress mail (WP Mail SMTP transport if configured).
     */
    private function send_verification_email($user_id, $token) {
        $user = get_userdata($user_id);
        if (!$user instanceof WP_User) {
            return new WP_Error('invalid_user', 'Invalid user for verification email');
        }

        $verify_url = add_query_arg(
            [
                'ulnec_verify' => '1',
                'uid' => (int) $user_id,
                'token' => rawurlencode($token),
            ],
            home_url('/login')
        );

        $subject = sprintf('[%s] Verify your email address', wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES));

        $message = '<p>Hi ' . esc_html($user->display_name ?: $user->user_login) . ',</p>';
        $message .= '<p>Thanks for registering. Please confirm your email address to activate your account:</p>';
        $message .= '<p><a href="' . esc_url($verify_url) . '">Verify Email Address</a></p>';
        $message .= '<p>This verification link will expire in 24 hours.</p>';

        $headers = ['Content-Type: text/html; charset=UTF-8'];

        $sent = wp_mail($user->user_email, $subject, $message, $headers);

        if (!$sent) {
            return new WP_Error('mail_failed', 'Verification email could not be sent');
        }

        return true;
    }

    /**
     * Process email verification callback.
     */
    public function handle_email_verification() {
        if (!isset($_GET['ulnec_verify'])) {
            return;
        }

        $user_id = isset($_GET['uid']) ? absint($_GET['uid']) : 0;
        $token = isset($_GET['token']) ? sanitize_text_field(wp_unslash($_GET['token'])) : '';

        if ($user_id <= 0 || empty($token)) {
            wp_safe_redirect(add_query_arg('verified', 'invalid', home_url('/login')));
            exit;
        }

        $stored_hash = (string) get_user_meta($user_id, 'ulnec_email_verify_token_hash', true);
        $expires = (int) get_user_meta($user_id, 'ulnec_email_verify_expires', true);

        if (empty($stored_hash) || empty($expires)) {
            wp_safe_redirect(add_query_arg('verified', 'invalid', home_url('/login')));
            exit;
        }

        if ($expires < time()) {
            wp_safe_redirect(add_query_arg('verified', 'expired', home_url('/login')));
            exit;
        }

        if (!wp_check_password($token, $stored_hash)) {
            wp_safe_redirect(add_query_arg('verified', 'invalid', home_url('/login')));
            exit;
        }

        update_user_meta($user_id, 'ulnec_email_verified', '1');
        update_user_meta($user_id, 'ulnec_email_verification_required', '0');
        delete_user_meta($user_id, 'ulnec_email_verify_token_hash');
        delete_user_meta($user_id, 'ulnec_email_verify_expires');

        wp_safe_redirect(add_query_arg('verified', '1', home_url('/login')));
        exit;
    }

    /**
     * Authenticate user via Supabase Auth if WordPress authentication fails.
     */
    public function authenticate_via_supabase($user, $username, $password) {
        if ($user instanceof WP_User) {
            return $user;
        }

        if (empty($username) || empty($password)) {
            return $user;
        }

        if (is_wp_error($user)) {
            $codes = $user->get_error_codes();
            $allowed_codes = ['invalid_username', 'incorrect_password'];
            $has_allowed_code = false;

            foreach ($codes as $code) {
                if (in_array($code, $allowed_codes, true)) {
                    $has_allowed_code = true;
                    break;
                }
            }

            if (!$has_allowed_code) {
                return $user;
            }
        }

        $email = $username;
        if (!is_email($email)) {
            $wp_user = get_user_by('login', $username);
            if (!$wp_user || empty($wp_user->user_email)) {
                return $user;
            }
            $email = $wp_user->user_email;
        }

        $auth_result = $this->supabase->sign_in_with_password($email, $password);
        if (is_wp_error($auth_result)) {
            $message = strtolower($auth_result->get_error_message());
            if (strpos($message, 'confirm') !== false || strpos($message, 'not confirmed') !== false) {
                return new WP_Error(
                    'ulnec_email_not_confirmed',
                    'Please verify your email first. Check your inbox for the confirmation link before logging in.'
                );
            }

            return $user;
        }

        $wp_user = get_user_by('email', $email);
        if (!$wp_user) {
            $base_login = sanitize_user(current(explode('@', $email)), true);
            if (empty($base_login)) {
                $base_login = 'ulnec_user';
            }

            $login = $base_login;
            $suffix = 1;
            while (username_exists($login)) {
                $login = $base_login . $suffix;
                $suffix++;
            }

            $new_user_id = wp_create_user($login, wp_generate_password(32, true, true), $email);
            if (is_wp_error($new_user_id)) {
                return new WP_Error('supabase_wp_create_failed', 'Authenticated with Supabase but failed to create local WordPress user.');
            }

            $wp_user = get_user_by('id', $new_user_id);
            if ($wp_user) {
                wp_update_user([
                    'ID' => $wp_user->ID,
                    'display_name' => isset($auth_result['user']['user_metadata']['name']) ? sanitize_text_field($auth_result['user']['user_metadata']['name']) : $wp_user->display_name,
                ]);

                $this->sync_user_on_registration($wp_user->ID);
            }
        }

        if ($wp_user instanceof WP_User) {
            update_user_meta($wp_user->ID, 'ulnec_email_verification_required', '1');
            update_user_meta($wp_user->ID, 'ulnec_email_verified', '1');
            $this->sync_user_on_update($wp_user->ID, $wp_user);
            return $wp_user;
        }

        return $user;
    }
    
    /**
     * Sync WordPress user to Supabase on update
     */
    public function sync_user_on_update($user_id, $old_user_data) {
        $user = get_userdata($user_id);
        
        if (!$user) {
            return;
        }
        
        // Get Supabase user
        $supabase_user = $this->supabase->get_user_by_wordpress_id($user_id);
        
        if ($supabase_user && !is_wp_error($supabase_user)) {
            // Update Supabase user
            $this->supabase->update_user($supabase_user['id'], [
                'email' => $user->user_email,
                'name' => $user->display_name
            ]);
        } else {
            // User doesn't exist in Supabase, create it
            $this->sync_user_on_registration($user_id);
        }
    }
    
    /**
     * Get or create Supabase user for current WordPress user
     */
    public function get_or_create_current_user() {
        if (!is_user_logged_in()) {
            return null;
        }
        
        $user_id = get_current_user_id();
        $supabase_user = $this->supabase->get_user_by_wordpress_id($user_id);
        
        if (!$supabase_user && !is_wp_error($supabase_user)) {
            // User doesn't exist, create it
            $this->sync_user_on_registration($user_id);
            $supabase_user = $this->supabase->get_user_by_wordpress_id($user_id);
        }
        
        return $supabase_user;
    }
    
    /**
     * Check if current user has active license
     */
    public function user_has_active_license() {
        $supabase_user = $this->get_or_create_current_user();
        
        if (!$supabase_user) {
            return false;
        }
        
        $licenses = $this->supabase->get_user_licenses($supabase_user['id']);
        
        if (is_wp_error($licenses)) {
            return false;
        }
        
        foreach ($licenses as $license) {
            if ($license['status'] === 'active') {
                // Check expiration
                if (empty($license['expires_at']) || strtotime($license['expires_at']) > time()) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Get current user's tier
     */
    public function get_user_tier() {
        $supabase_user = $this->get_or_create_current_user();
        
        if (!$supabase_user) {
            return 'free';
        }
        
        return $supabase_user['tier'] ?? 'free';
    }
}
