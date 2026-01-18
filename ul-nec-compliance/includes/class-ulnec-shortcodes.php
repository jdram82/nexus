<?php
/**
 * Shortcodes Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class ULNEC_Shortcodes {
    
    private $supabase;
    
    public function __construct($supabase) {
        $this->supabase = $supabase;
        
        add_shortcode('ulnec_login', [$this, 'login_shortcode']);
        add_shortcode('ulnec_register', [$this, 'register_shortcode']);
        add_shortcode('ulnec_dashboard', [$this, 'dashboard_shortcode']);
        add_shortcode('ulnec_download', [$this, 'download_shortcode']);
    }
    
    /**
     * Login shortcode
     */
    public function login_shortcode() {
        ob_start();
        ?>
        <div class="login-container">
            <h2>Login to Your Account</h2>
            
            <?php
            if (!is_user_logged_in()) {
                wp_login_form(array(
                    'redirect' => home_url('/dashboard'),
                    'label_username' => 'Email or Username',
                    'label_password' => 'Password',
                    'label_remember' => 'Remember Me',
                    'label_log_in' => 'Login',
                    'remember' => true
                ));
                
                echo '<p class="register-link">Don\'t have an account? <a href="' . home_url('/register') . '">Register here</a></p>';
                echo '<p class="forgot-link"><a href="' . wp_lostpassword_url() . '">Forgot Password?</a></p>';
            } else {
                echo '<p class="already-logged">You are already logged in.</p>';
                echo '<p><a href="' . home_url('/dashboard') . '" class="dashboard-link">Go to Dashboard</a></p>';
            }
            ?>
        </div>
        
        <style>
        /* Hide all empty menu items and navigation elements */
        nav ul li:empty,
        nav ol li:empty,
        .menu li:empty,
        ul li:empty,
        ol li:empty,
        header ul li:empty,
        header ol li:empty,
        nav li,
        .menu li {
            display: none !important;
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        nav ul, nav ol, .menu, header ul, header ol {
            list-style: none !important;
        }
        
        .login-container {
            max-width: 450px;
            margin: 50px auto;
            padding: 40px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
            font-size: 28px;
            font-weight: 600;
        }
        .login-container form {
            margin-bottom: 20px;
        }
        .login-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #34495e;
            font-size: 14px;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        .login-container input[type="text"]:focus,
        .login-container input[type="password"]:focus {
            outline: none;
            border-color: #FF6B35;
        }
        .login-container input[type="checkbox"] {
            margin-right: 8px;
        }
        .login-container input[type="submit"] {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }
        .login-container input[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        }
        .login-container p {
            text-align: center;
            margin: 15px 0;
            font-size: 14px;
        }
        .login-container .register-link,
        .login-container .forgot-link {
            color: #7f8c8d;
        }
        .login-container a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .login-container a:hover {
            text-decoration: underline;
        }
        .login-container .already-logged {
            color: #27ae60;
            font-weight: 500;
        }
        .login-container .dashboard-link {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }
        .login-container .dashboard-link:hover {
            background: #5568d3;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .login-container {
                margin: 20px;
                padding: 25px;
            }
            .login-container h2 {
                font-size: 24px;
            }
        }
        </style>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Register shortcode
     */
    public function register_shortcode() {
        ob_start();
        
        // Handle registration
        $error_message = '';
        $success_message = '';
        
        if (isset($_POST['register_user'])) {
            $username = sanitize_user($_POST['username']);
            $email = sanitize_email($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            
            if (empty($username) || empty($email) || empty($password)) {
                $error_message = 'All fields are required.';
            } elseif ($password !== $confirm_password) {
                $error_message = 'Passwords do not match.';
            } elseif (strlen($password) < 8) {
                $error_message = 'Password must be at least 8 characters long.';
            } elseif (username_exists($username)) {
                $error_message = 'Username already exists.';
            } elseif (email_exists($email)) {
                $error_message = 'Email already registered.';
            } else {
                $user_id = wp_create_user($username, $password, $email);
                
                if (is_wp_error($user_id)) {
                    $error_message = $user_id->get_error_message();
                } else {
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id);
                    $success_message = 'Registration successful! Redirecting to dashboard...';
                    echo '<script>setTimeout(function(){ window.location.href = "' . home_url('/dashboard') . '"; }, 2000);</script>';
                }
            }
        }
        ?>
        
        <div class="register-container">
            <h2>Create Your Account</h2>
            
            <?php if ($error_message): ?>
                <div class="error-message"><?php echo esc_html($error_message); ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="success-message"><?php echo esc_html($success_message); ?></div>
            <?php else: ?>
                <form method="post" id="register-form">
                    <label for="username">Username *</label>
                    <input type="text" name="username" id="username" required minlength="3">
                    
                    <label for="email">Email Address *</label>
                    <input type="email" name="email" id="email" required>
                    
                    <label for="password">Password *</label>
                    <input type="password" name="password" id="password" required minlength="8">
                    
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                    
                    <div class="terms-container">
                        <input type="checkbox" name="terms" id="terms" required>
                        <label for="terms">I agree to the Terms & Conditions</label>
                    </div>
                    
                    <button type="submit" name="register_user">Create Account</button>
                </form>
                
                <p class="login-link">Already have an account? <a href="<?php echo home_url('/login'); ?>">Login here</a></p>
            <?php endif; ?>
        </div>
        
        <style>
        /* Hide all empty menu items and navigation elements */
        nav ul li:empty,
        nav ol li:empty,
        .menu li:empty,
        ul li:empty,
        ol li:empty,
        header ul li:empty,
        header ol li:empty,
        nav li,
        .menu li {
            display: none !important;
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        nav ul, nav ol, .menu, header ul, header ol {
            list-style: none !important;
        }
        
        .register-container {
            max-width: 450px;
            margin: 50px auto;
            padding: 40px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
            font-size: 28px;
            font-weight: 600;
        }
        .register-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #34495e;
            font-size: 14px;
        }
        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        .register-container input:focus {
            outline: none;
            border-color: #FF6B35;
        }
        .register-container .terms-container {
            margin: 20px 0;
        }
        .register-container .terms-container input[type="checkbox"] {
            margin-right: 8px;
        }
        .register-container .terms-container label {
            display: inline;
            font-size: 14px;
            font-weight: normal;
        }
        .register-container button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .register-container button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        }
        .register-container .login-link {
            text-align: center;
            margin-top: 20px;
            color: #7f8c8d;
        }
        .register-container a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .register-container a:hover {
            text-decoration: underline;
        }
        .error-message {
            background: #fee;
            border-left: 4px solid #e74c3c;
            padding: 12px;
            margin-bottom: 20px;
            color: #c0392b;
        }
        .success-message {
            background: #efd;
            border-left: 4px solid #27ae60;
            padding: 12px;
            margin-bottom: 20px;
            color: #27ae60;
        }
        @media (max-width: 600px) {
            .register-container {
                margin: 20px;
                padding: 25px;
            }
        }
        </style>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('register-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const password = document.getElementById('password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    
                    if (password !== confirmPassword) {
                        e.preventDefault();
                        alert('Passwords do not match!');
                        return false;
                    }
                });
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Dashboard shortcode
     */
    public function dashboard_shortcode() {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . home_url('/login') . '">login</a> to view your dashboard.</p>';
        }
        
        try {
            $current_user = wp_get_current_user();
            
            // Safely get user from Supabase
            $supabase_user = null;
            $licenses = [];
            $has_active_license = false;
            
            try {
                // Try to get user from Supabase by WordPress ID
                $supabase_user_response = $this->supabase->request('GET', 'ulnec_users?wordpress_user_id=eq.' . $current_user->ID);
                
                // Check if response is valid and not an error
                if (is_wp_error($supabase_user_response)) {
                    error_log('Dashboard: User lookup by WP ID failed: ' . $supabase_user_response->get_error_message());
                } elseif (is_array($supabase_user_response) && !empty($supabase_user_response)) {
                    $supabase_user = $supabase_user_response[0];
                    
                    // Get licenses
                    $licenses_response = $this->supabase->request('GET', 'ulnec_licenses?user_id=eq.' . $supabase_user['id'] . '&order=created_at.desc');
                    if (!is_wp_error($licenses_response) && is_array($licenses_response)) {
                        $licenses = $licenses_response;
                        
                        // Check for active license
                        foreach ($licenses as $license) {
                            if ($license['status'] === 'active' && strtotime($license['expires_at']) > time()) {
                                $has_active_license = true;
                                break;
                            }
                        }
                    }
                }
                
                // If user not found by WP ID, try to find by email
                if (!$supabase_user) {
                    $supabase_user_response = $this->supabase->request('GET', 'ulnec_users?email=eq.' . urlencode($current_user->user_email));
                    
                    if (is_wp_error($supabase_user_response)) {
                        error_log('Dashboard: User lookup by email failed: ' . $supabase_user_response->get_error_message());
                    } elseif (is_array($supabase_user_response) && !empty($supabase_user_response)) {
                        $supabase_user = $supabase_user_response[0];
                        
                        // Get licenses
                        $licenses_response = $this->supabase->request('GET', 'ulnec_licenses?user_id=eq.' . $supabase_user['id'] . '&order=created_at.desc');
                        if (!is_wp_error($licenses_response) && is_array($licenses_response)) {
                            $licenses = $licenses_response;
                            
                            // Check for active license
                            foreach ($licenses as $license) {
                                if ($license['status'] === 'active' && strtotime($license['expires_at']) > time()) {
                                    $has_active_license = true;
                                    break;
                                }
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                // Supabase error - continue with limited dashboard
                error_log('Dashboard shortcode Supabase error: ' . $e->getMessage());
            }
            
        ob_start();
        ?>
        <div class="dashboard-container">
            <h1>Welcome, <?php echo esc_html($current_user->display_name); ?>!</h1>
            
            <div class="dashboard-grid">
                <!-- Account Information -->
                <div class="dashboard-card">
                    <h3>Account Information</h3>
                    <div class="info-row">
                        <span class="label">Email:</span>
                        <span class="value"><?php echo esc_html($current_user->user_email); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Username:</span>
                        <span class="value"><?php echo esc_html($current_user->user_login); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Account Tier:</span>
                        <span class="value tier-badge"><?php echo esc_html($supabase_user['tier'] ?? 'Free'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Member Since:</span>
                        <span class="value"><?php echo date('M d, Y', strtotime($current_user->user_registered)); ?></span>
                    </div>
                </div>
                
                <!-- License Information -->
                <div class="dashboard-card">
                    <h3>Your Licenses</h3>
                    <?php
                    if (!empty($licenses)) {
                        foreach ($licenses as $license) {
                            $is_active = $license['status'] === 'active' && strtotime($license['expires_at']) > time();
                            ?>
                            <div class="license-item">
                                <div class="license-key">
                                    <strong><?php echo esc_html($license['license_key']); ?></strong>
                                    <button onclick="copyToClipboard('<?php echo esc_js($license['license_key']); ?>')" class="copy-btn">Copy</button>
                                </div>
                                <div class="license-details">
                                    <span class="status-badge <?php echo $is_active ? 'active' : 'expired'; ?>">
                                        <?php echo $is_active ? 'Active' : 'Expired'; ?>
                                    </span>
                                    <span>Tier: <?php echo esc_html(ucfirst($license['tier'])); ?></span>
                                    <span>Expires: <?php echo date('M d, Y', strtotime($license['expires_at'])); ?></span>
                                    <span>Activations: <?php echo esc_html($license['activation_count'] ?? 0); ?>/<?php echo esc_html($license['max_activations'] ?? 0); ?></span>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p>No licenses yet. <a href="' . home_url('/pricing') . '">Purchase a license</a></p>';
                    }
                    ?>
                </div>
                
                <!-- Download Section -->
                <div class="dashboard-card download-section">
                    <h3>Download Plugin</h3>
                    <?php if ($has_active_license): ?>
                        <p>You have an active license. Download the latest version below:</p>
                        <?php 
                        $download_manager = new ULNEC_Download($this->supabase);
                        $download_url = $download_manager->get_download_link();
                        ?>
                        <a href="<?php echo esc_url($download_url); ?>" class="download-button">
                            Download UL-NEC Plugin
                        </a>
                    <?php else: ?>
                        <p>Purchase a license to download the plugin.</p>
                        <a href="<?php echo home_url('/pricing'); ?>" class="purchase-button">View Pricing</a>
                    <?php endif; ?>
                </div>
                
                <!-- Quick Actions -->
                <div class="dashboard-card">
                    <h3>Quick Actions</h3>
                    <ul class="quick-actions">
                        <li><a href="<?php echo home_url('/support'); ?>">Contact Support</a></li>
                        <li><a href="<?php echo home_url('/documentation'); ?>">View Documentation</a></li>
                        <li><a href="<?php echo home_url('/bug-report'); ?>">Report a Bug</a></li>
                        <li><a href="<?php echo wp_logout_url(home_url()); ?>">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <style>
        /* Hide all empty menu items and navigation elements */
        nav ul li:empty,
        nav ol li:empty,
        .menu li:empty,
        ul li:empty,
        ol li:empty,
        header ul li:empty,
        header ol li:empty,
        nav li,
        .menu li {
            display: none !important;
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        nav ul, nav ol, .menu, header ul, header ol {
            list-style: none !important;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }
        .dashboard-container h1 {
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .dashboard-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .dashboard-card h3 {
            color: #34495e;
            margin-bottom: 20px;
            font-size: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-row .label {
            font-weight: 600;
            color: #7f8c8d;
        }
        .tier-badge {
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .license-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .license-key {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .copy-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .copy-btn:hover {
            background: #5568d3;
        }
        .license-details {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            font-size: 13px;
            color: #7f8c8d;
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
        }
        .status-badge.active {
            background: #d4edda;
            color: #27ae60;
        }
        .status-badge.expired {
            background: #f8d7da;
            color: #e74c3c;
        }
        .download-button {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .download-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .purchase-button {
            display: inline-block;
            padding: 12px 24px;
            background: #FF6B35;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
        }
        .purchase-button:hover {
            background: #e55a2b;
        }
        .quick-actions {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .quick-actions li {
            padding: 10px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        .quick-actions li:last-child {
            border-bottom: none;
        }
        .quick-actions a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .quick-actions a:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
        </style>
        
        <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('License key copied to clipboard!');
            }, function() {
                alert('Failed to copy license key');
            });
        }
        </script>
        <?php
        return ob_get_clean();
        
        } catch (Exception $e) {
            error_log('Dashboard shortcode error: ' . $e->getMessage());
            return '<div class="dashboard-error"><p>Unable to load dashboard. Please try again later or contact support.</p></div>';
        }
    }
    
    /**
     * Download shortcode
     */
    public function download_shortcode() {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . wp_login_url() . '">login</a> to download.</p>';
        }
        
        $download_manager = new ULNEC_Download($this->supabase);
        $download_url = $download_manager->get_download_link();
        
        return '<a href="' . esc_url($download_url) . '" class="button">Download Plugin</a>';
    }
}
