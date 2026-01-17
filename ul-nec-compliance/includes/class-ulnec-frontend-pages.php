<?php
/**
 * Frontend Pages Class
 * 
 * Handles user-facing shortcodes for bug reports, feature requests, and support
 */

if (!defined('ABSPATH')) {
    exit;
}

class ULNEC_Frontend_Pages {
    
    private $supabase;
    
    public function __construct($supabase) {
        $this->supabase = $supabase;
        
        add_shortcode('ulnec_bug_report', [$this, 'bug_report_shortcode']);
        add_shortcode('ulnec_feature_request', [$this, 'feature_request_shortcode']);
        add_shortcode('ulnec_support', [$this, 'support_shortcode']);
    }
    
    /**
     * Bug Report Shortcode
     */
    public function bug_report_shortcode() {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . wp_login_url(get_permalink()) . '">login</a> to report bugs.</p>';
        }
        
        $current_user = wp_get_current_user();
        $error_message = '';
        $success_message = '';
        
        // Handle form submission
        if (isset($_POST['submit_bug_report']) && wp_verify_nonce($_POST['bug_nonce'], 'ulnec_bug_report')) {
            $title = sanitize_text_field($_POST['bug_title']);
            $description = sanitize_textarea_field($_POST['bug_description']);
            $priority = sanitize_text_field($_POST['bug_priority']);
            $autocad_version = sanitize_text_field($_POST['autocad_version']);
            
            if (empty($title) || empty($description)) {
                $error_message = 'Title and description are required.';
            } else {
                // Get Supabase user
                $supabase_user_response = $this->supabase->request('GET', 'ulnec_users?email=eq.' . urlencode($current_user->user_email));
                
                if (!is_wp_error($supabase_user_response) && !empty($supabase_user_response)) {
                    $supabase_user = $supabase_user_response[0];
                    
                    // Submit bug report
                    $bug_data = [
                        'user_id' => $supabase_user['id'],
                        'title' => $title,
                        'description' => $description,
                        'priority' => $priority,
                        'status' => 'open',
                        'autocad_version' => $autocad_version
                    ];
                    
                    $result = $this->supabase->request('POST', 'ulnec_bugs', $bug_data);
                    
                    if (!is_wp_error($result)) {
                        $success_message = 'Bug report submitted successfully! We\'ll review it soon.';
                        // Clear form
                        $_POST = [];
                    } else {
                        $error_message = 'Failed to submit bug report. Please try again.';
                    }
                } else {
                    $error_message = 'User account not found. Please contact support.';
                }
            }
        }
        
        ob_start();
        ?>
        <div class="ulnec-bug-report-container">
            <h2>🐛 Report a Bug</h2>
            <p>Help us improve by reporting bugs you encounter. We appreciate your feedback!</p>
            
            <?php if ($error_message): ?>
                <div class="ulnec-message ulnec-error"><?php echo esc_html($error_message); ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="ulnec-message ulnec-success"><?php echo esc_html($success_message); ?></div>
            <?php else: ?>
                <form method="post" class="ulnec-form">
                    <?php wp_nonce_field('ulnec_bug_report', 'bug_nonce'); ?>
                    
                    <div class="form-group">
                        <label for="bug_title">Bug Title *</label>
                        <input type="text" name="bug_title" id="bug_title" required 
                               placeholder="Brief description of the issue" 
                               value="<?php echo esc_attr($_POST['bug_title'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="bug_description">Detailed Description *</label>
                        <textarea name="bug_description" id="bug_description" rows="6" required 
                                  placeholder="Please provide steps to reproduce, expected vs actual behavior, etc."><?php echo esc_textarea($_POST['bug_description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bug_priority">Priority</label>
                            <select name="bug_priority" id="bug_priority">
                                <option value="low">Low - Minor inconvenience</option>
                                <option value="medium" selected>Medium - Affects workflow</option>
                                <option value="high">High - Blocks critical tasks</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="autocad_version">AutoCAD Version</label>
                            <input type="text" name="autocad_version" id="autocad_version" 
                                   placeholder="e.g., AutoCAD 2024"
                                   value="<?php echo esc_attr($_POST['autocad_version'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <button type="submit" name="submit_bug_report" class="ulnec-btn ulnec-btn-primary">
                        Submit Bug Report
                    </button>
                </form>
            <?php endif; ?>
        </div>
        
        <?php echo $this->get_form_styles(); ?>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Feature Request Shortcode
     */
    public function feature_request_shortcode() {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . wp_login_url(get_permalink()) . '">login</a> to request features.</p>';
        }
        
        $current_user = wp_get_current_user();
        $error_message = '';
        $success_message = '';
        
        // Handle form submission
        if (isset($_POST['submit_feature_request']) && wp_verify_nonce($_POST['feature_nonce'], 'ulnec_feature_request')) {
            $title = sanitize_text_field($_POST['feature_title']);
            $description = sanitize_textarea_field($_POST['feature_description']);
            $category = sanitize_text_field($_POST['feature_category']);
            
            if (empty($title) || empty($description)) {
                $error_message = 'Title and description are required.';
            } else {
                // Get Supabase user
                $supabase_user_response = $this->supabase->request('GET', 'ulnec_users?email=eq.' . urlencode($current_user->user_email));
                
                if (!is_wp_error($supabase_user_response) && !empty($supabase_user_response)) {
                    $supabase_user = $supabase_user_response[0];
                    
                    // Submit feature request
                    $feature_data = [
                        'user_id' => $supabase_user['id'],
                        'title' => $title,
                        'description' => $description,
                        'category' => $category,
                        'status' => 'under_review',
                        'vote_count' => 1 // Auto-vote from submitter
                    ];
                    
                    $result = $this->supabase->request('POST', 'ulnec_features', $feature_data);
                    
                    if (!is_wp_error($result)) {
                        $success_message = 'Feature request submitted successfully! Other users can now vote on it.';
                        $_POST = [];
                    } else {
                        $error_message = 'Failed to submit feature request. Please try again.';
                    }
                } else {
                    $error_message = 'User account not found. Please contact support.';
                }
            }
        }
        
        ob_start();
        ?>
        <div class="ulnec-feature-request-container">
            <h2>✨ Request a Feature</h2>
            <p>Share your ideas to help shape the future of UL-NEC Compliance Checker!</p>
            
            <?php if ($error_message): ?>
                <div class="ulnec-message ulnec-error"><?php echo esc_html($error_message); ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="ulnec-message ulnec-success"><?php echo esc_html($success_message); ?></div>
            <?php else: ?>
                <form method="post" class="ulnec-form">
                    <?php wp_nonce_field('ulnec_feature_request', 'feature_nonce'); ?>
                    
                    <div class="form-group">
                        <label for="feature_title">Feature Title *</label>
                        <input type="text" name="feature_title" id="feature_title" required 
                               placeholder="What feature would you like to see?" 
                               value="<?php echo esc_attr($_POST['feature_title'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="feature_description">Detailed Description *</label>
                        <textarea name="feature_description" id="feature_description" rows="6" required 
                                  placeholder="Explain how this feature would help you and how it should work..."><?php echo esc_textarea($_POST['feature_description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="feature_category">Category</label>
                        <select name="feature_category" id="feature_category">
                            <option value="compliance">Compliance Checks</option>
                            <option value="automation">Automation</option>
                            <option value="reporting">Reporting</option>
                            <option value="integration">Integration</option>
                            <option value="ui">User Interface</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="submit_feature_request" class="ulnec-btn ulnec-btn-primary">
                        Submit Feature Request
                    </button>
                </form>
            <?php endif; ?>
        </div>
        
        <?php echo $this->get_form_styles(); ?>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Support Shortcode
     */
    public function support_shortcode() {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . wp_login_url(get_permalink()) . '">login</a> to contact support.</p>';
        }
        
        $current_user = wp_get_current_user();
        $error_message = '';
        $success_message = '';
        
        // Handle form submission
        if (isset($_POST['submit_support_ticket']) && wp_verify_nonce($_POST['support_nonce'], 'ulnec_support')) {
            $subject = sanitize_text_field($_POST['support_subject']);
            $message = sanitize_textarea_field($_POST['support_message']);
            $priority = sanitize_text_field($_POST['support_priority']);
            
            if (empty($subject) || empty($message)) {
                $error_message = 'Subject and message are required.';
            } else {
                // Send email to support
                $admin_email = get_option('admin_email');
                $email_subject = '[UL-NEC Support] ' . $subject;
                $email_body = "Support Request from: {$current_user->display_name} ({$current_user->user_email})\n\n";
                $email_body .= "Priority: " . ucfirst($priority) . "\n\n";
                $email_body .= "Message:\n" . $message;
                
                $headers = ['From: ' . $current_user->user_email];
                
                $sent = wp_mail($admin_email, $email_subject, $email_body, $headers);
                
                if ($sent) {
                    $success_message = 'Support ticket submitted successfully! We\'ll get back to you within 24 hours.';
                    $_POST = [];
                } else {
                    $error_message = 'Failed to send support request. Please try emailing ' . $admin_email . ' directly.';
                }
            }
        }
        
        ob_start();
        ?>
        <div class="ulnec-support-container">
            <h2>💬 Contact Support</h2>
            <p>Need help? Our support team is here to assist you!</p>
            
            <div class="support-info">
                <div class="support-box">
                    <h3>📧 Email Support</h3>
                    <p>Average response time: <strong>24 hours</strong></p>
                </div>
                <div class="support-box">
                    <h3>📚 Documentation</h3>
                    <p><a href="<?php echo home_url('/documentation'); ?>">View Help Center</a></p>
                </div>
            </div>
            
            <?php if ($error_message): ?>
                <div class="ulnec-message ulnec-error"><?php echo esc_html($error_message); ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="ulnec-message ulnec-success"><?php echo esc_html($success_message); ?></div>
            <?php else: ?>
                <form method="post" class="ulnec-form">
                    <?php wp_nonce_field('ulnec_support', 'support_nonce'); ?>
                    
                    <div class="form-group">
                        <label for="support_subject">Subject *</label>
                        <input type="text" name="support_subject" id="support_subject" required 
                               placeholder="Brief description of your issue" 
                               value="<?php echo esc_attr($_POST['support_subject'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="support_priority">Priority</label>
                        <select name="support_priority" id="support_priority">
                            <option value="low">Low - General question</option>
                            <option value="medium" selected>Medium - Need assistance</option>
                            <option value="high">High - Urgent issue</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="support_message">Message *</label>
                        <textarea name="support_message" id="support_message" rows="8" required 
                                  placeholder="Please provide as much detail as possible..."><?php echo esc_textarea($_POST['support_message'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-info">
                        <p><strong>Your email:</strong> <?php echo esc_html($current_user->user_email); ?></p>
                        <p><small>We'll reply to this email address</small></p>
                    </div>
                    
                    <button type="submit" name="submit_support_ticket" class="ulnec-btn ulnec-btn-primary">
                        Send Support Request
                    </button>
                </form>
            <?php endif; ?>
        </div>
        
        <?php echo $this->get_form_styles(); ?>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get shared form styles
     */
    private function get_form_styles() {
        return '
        <style>
            /* Hide empty menu items */
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
            
            .ulnec-bug-report-container,
            .ulnec-feature-request-container,
            .ulnec-support-container {
                max-width: 800px;
                margin: 40px auto;
                padding: 40px;
                background: #ffffff;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            }
            
            .ulnec-bug-report-container h2,
            .ulnec-feature-request-container h2,
            .ulnec-support-container h2 {
                color: #2c3e50;
                margin-bottom: 10px;
                font-size: 28px;
            }
            
            .ulnec-bug-report-container > p,
            .ulnec-feature-request-container > p,
            .ulnec-support-container > p {
                color: #6b7280;
                margin-bottom: 30px;
            }
            
            .ulnec-message {
                padding: 15px 20px;
                border-radius: 8px;
                margin-bottom: 25px;
                font-weight: 500;
            }
            
            .ulnec-message.ulnec-error {
                background: #fee;
                border-left: 4px solid #e74c3c;
                color: #c0392b;
            }
            
            .ulnec-message.ulnec-success {
                background: #efd;
                border-left: 4px solid #27ae60;
                color: #27ae60;
            }
            
            .ulnec-form {
                margin-top: 30px;
            }
            
            .form-group {
                margin-bottom: 25px;
            }
            
            .form-group label {
                display: block;
                margin-bottom: 8px;
                font-weight: 600;
                color: #34495e;
                font-size: 14px;
            }
            
            .form-group input[type="text"],
            .form-group input[type="email"],
            .form-group textarea,
            .form-group select {
                width: 100%;
                padding: 12px 15px;
                border: 2px solid #e0e0e0;
                border-radius: 6px;
                font-size: 15px;
                font-family: inherit;
                transition: border-color 0.3s;
                box-sizing: border-box;
            }
            
            .form-group input:focus,
            .form-group textarea:focus,
            .form-group select:focus {
                outline: none;
                border-color: #667eea;
            }
            
            .form-group textarea {
                resize: vertical;
                min-height: 120px;
            }
            
            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }
            
            .ulnec-btn {
                padding: 14px 28px;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .ulnec-btn-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }
            
            .ulnec-btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            }
            
            .support-info {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-bottom: 30px;
            }
            
            .support-box {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 8px;
                border-left: 4px solid #667eea;
            }
            
            .support-box h3 {
                margin: 0 0 10px 0;
                color: #2c3e50;
                font-size: 18px;
            }
            
            .support-box p {
                margin: 0;
                color: #6b7280;
            }
            
            .support-box a {
                color: #667eea;
                text-decoration: none;
                font-weight: 600;
            }
            
            .support-box a:hover {
                text-decoration: underline;
            }
            
            .form-info {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 6px;
                margin-bottom: 25px;
            }
            
            .form-info p {
                margin: 5px 0;
                color: #6b7280;
            }
            
            @media (max-width: 768px) {
                .ulnec-bug-report-container,
                .ulnec-feature-request-container,
                .ulnec-support-container {
                    margin: 20px;
                    padding: 25px;
                }
                
                .form-row {
                    grid-template-columns: 1fr;
                }
                
                .support-info {
                    grid-template-columns: 1fr;
                }
            }
        </style>
        ';
    }
}
