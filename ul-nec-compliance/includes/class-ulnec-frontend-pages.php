<?php
/**
 * Frontend Pages Class - Claude_Beta Launch Design
 * 
 * Premium UI for bug reports, feature requests, and support
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
        add_shortcode('ulnec_founders_progress', [$this, 'founders_progress_shortcode']);
        add_shortcode('ulnec_account_settings', [$this, 'account_settings_shortcode']);
        add_shortcode('ulnec_billing', [$this, 'billing_shortcode']);
    }
    
    /**
     * Bug Report Shortcode - Claude_Beta Launch Design
     */
    public function bug_report_shortcode() {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . wp_login_url(get_permalink()) . '">login</a> to report bugs.</p>';
        }
        
        $current_user = wp_get_current_user();
        $error_message = '';
        $success_message = '';
        $bug_id = '';
        
        // Handle form submission
        if (isset($_POST['submit_bug_report']) && wp_verify_nonce($_POST['bug_nonce'], 'ulnec_bug_report')) {
            $title = sanitize_text_field($_POST['bug_title']);
            $description = sanitize_textarea_field($_POST['bug_description']);
            $steps = sanitize_textarea_field($_POST['bug_steps']);
            $expected = sanitize_textarea_field($_POST['bug_expected']);
            $actual = sanitize_textarea_field($_POST['bug_actual']);
            $severity = sanitize_text_field($_POST['bug_severity']);
            $cad_version = sanitize_text_field($_POST['cad_version']);
            $windows_version = sanitize_text_field($_POST['windows_version']);
            $plugin_version = sanitize_text_field($_POST['plugin_version']);
            
            if (empty($title) || empty($description) || empty($severity)) {
                $error_message = 'Title, description, and severity are required.';
            } else {
                // Get Supabase user
                $supabase_user_response = $this->supabase->request('GET', 'ulnec_users?email=eq.' . urlencode($current_user->user_email));
                
                if (is_wp_error($supabase_user_response)) {
                    $error_details = $supabase_user_response->get_error_data();
                    $full_error = $supabase_user_response->get_error_message();
                    if (is_array($error_details) && isset($error_details['body'])) {
                        $full_error .= ' | Body: ' . $error_details['body'];
                    }
                    error_log('Bug Report - User lookup error: ' . $full_error);
                    $error_message = 'Failed to verify user account.';
                } elseif (empty($supabase_user_response) || !is_array($supabase_user_response)) {
                    error_log('Bug Report - User not found for email: ' . $current_user->user_email);
                    $error_message = 'User account not found in system.';
                } else {
                    $supabase_user = $supabase_user_response[0];
                    
                    // Submit bug report with all fields
                    $bug_data = [
                        'user_id' => $supabase_user['id'],
                        'title' => $title,
                        'description' => $description,
                        'steps_to_reproduce' => !empty($steps) ? $steps : null,
                        'expected_behavior' => !empty($expected) ? $expected : null,
                        'actual_behavior' => !empty($actual) ? $actual : null,
                        'severity' => $severity,
                        'status' => 'open',
                        'autocad_version' => !empty($cad_version) ? $cad_version : null,
                        'plugin_version' => !empty($plugin_version) ? $plugin_version : '1.0 Beta',
                        'os_version' => !empty($windows_version) ? $windows_version : null
                    ];
                    
                    error_log('Bug Report - Submitting to Supabase: ' . json_encode($bug_data));
                    
                    $result = $this->supabase->request('POST', '/ulnec_bugs', $bug_data);
                    
                    if (is_wp_error($result)) {
                        $error_details = $result->get_error_data();
                        $full_error = $result->get_error_message();
                        if (is_array($error_details)) {
                            if (isset($error_details['body'])) {
                                $decoded_body = json_decode($error_details['body'], true);
                                if ($decoded_body) {
                                    $full_error .= ' | Details: ' . json_encode($decoded_body);
                                } else {
                                    $full_error .= ' | Body: ' . $error_details['body'];
                                }
                            }
                            if (isset($error_details['code'])) {
                                $full_error .= ' | HTTP Code: ' . $error_details['code'];
                            }
                        }
                        error_log('Bug Report - Submission failed: ' . $full_error);
                        $error_message = 'Failed to submit bug report. Please try again.';
                    } else {
                        error_log('Bug Report - Success! Result: ' . json_encode($result));
                        $success_message = 'Bug report submitted successfully! We\'ll review it soon.';
                        // Generate bug ID
                        if (isset($result[0]['id'])) {
                            $bug_id = 'BUG-' . date('Y') . '-' . substr($result[0]['id'], 0, 3);
                            $bug_record_id = $result[0]['id'];
                        } else {
                            $bug_id = 'BUG-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                            $bug_record_id = time();
                        }
                        
                        // Send confirmation email
                        $plugin = ULNEC_Plugin::instance();
                        if ($plugin && $plugin->emails) {
                            $email_data = [
                                'user_name' => $current_user->display_name,
                                'user_email' => $current_user->user_email,
                                'id' => $bug_id,
                                'title' => $title,
                                'priority' => $severity,
                                'status' => 'open',
                                'track_url' => home_url('/founders-progress/')
                            ];
                            $plugin->emails->send_bug_confirmation_email($email_data);
                        }
                        
                        // Clear form
                        $_POST = [];
                    }
                }
            }
        }
        
        ob_start();
        ?>
        <style>
            /* Hide empty list elements */
            ul:not(.ulnec-form-group ul):not(.ulnec-list):empty,
            ol:not(.ulnec-form-group ol):not(.ulnec-list):empty,
            li:empty {
                display: none !important;
            }
            
            /* Reset list styles for theme elements */
            body ul:not(.ulnec-list),
            body ol:not(.ulnec-list) {
                list-style: none !important;
            }
            
            body li:not(.ulnec-list li) {
                list-style: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Dark Background */
            body {
                background: #1a1f3a !important;
                min-height: 100vh;
            }
            
            .ulnec-bug-container {
                max-width: 900px;
                margin: 0 auto;
                padding: 2rem 1rem;
            }
            
            .ulnec-bug-header {
                text-align: center;
                margin-bottom: 3rem;
            }
            
            .ulnec-bug-header h1 {
                font-size: 2.5rem;
                margin-bottom: 1rem;
                color: #ffffff;
            }
            
            .ulnec-bug-header p {
                font-size: 1.1rem;
                color: #c7d2fe;
            }
            
            .ulnec-founders-note {
                background: linear-gradient(135deg, #fbbf24, #f59e0b);
                color: #000;
                padding: 1.5rem;
                border-radius: 20px;
                margin-bottom: 2rem;
                text-align: center;
            }
            
            .ulnec-founders-note strong {
                display: block;
                margin-bottom: 0.5rem;
                font-size: 1.1rem;
            }
            
            .ulnec-bug-form-container {
                background: #fff;
                padding: 3rem;
                border-radius: 20px;
                box-shadow: 0 10px 50px rgba(0, 0, 0, 0.1);
            }
            
            .ulnec-form-group {
                margin-bottom: 2rem;
                clear: both;
            }
            
            .ulnec-form-group label {
                display: block;
                margin-bottom: 0.75rem;
                color: #1a1f3a;
                font-weight: 600;
                font-size: 1rem;
                line-height: 1.5;
            }
            
            .ulnec-required {
                color: #ef4444;
            }
            
            .ulnec-form-group input[type="text"],
            .ulnec-form-group textarea,
            .ulnec-form-group select {
                width: 100%;
                max-width: 100%;
                padding: 1rem;
                background: #f9fafb;
                border: 2px solid #e5e7eb;
                border-radius: 10px;
                color: #1a1f3a;
                font-size: 1rem;
                font-family: inherit;
                transition: all 0.3s ease;
                box-sizing: border-box;
                display: block;
            }
            
            .ulnec-form-group input:focus,
            .ulnec-form-group textarea:focus,
            .ulnec-form-group select:focus {
                outline: none;
                border-color: #667eea;
                background: #fff;
            }
            
            .ulnec-form-group textarea {
                min-height: 120px;
                max-height: 300px;
                resize: vertical;
                line-height: 1.6;
            }
            
            .ulnec-form-group select {
                cursor: pointer;
                appearance: auto;
                -webkit-appearance: menulist;
                -moz-appearance: menulist;
            }
            
            .ulnec-help-text {
                font-size: 0.9rem;
                color: #9ca3af;
                margin-top: 0.5rem;
                margin-bottom: 0;
                line-height: 1.5;
            }
            
            .ulnec-severity-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 1rem;
            }
            
            .ulnec-severity-option {
                background: #f9fafb;
                padding: 1.5rem;
                border-radius: 15px;
                border: 2px solid #e5e7eb;
                cursor: pointer;
                transition: all 0.3s ease;
                text-align: center;
                position: relative;
            }
            
            .ulnec-severity-option:hover {
                border-color: #667eea;
                background: #fff;
                transform: translateY(-2px);
            }
            
            .ulnec-severity-option input[type="radio"] {
                position: absolute;
                opacity: 0;
            }
            
            .ulnec-severity-option input[type="radio"]:checked ~ .ulnec-severity-label {
                color: #667eea;
                font-weight: 700;
            }
            
            .ulnec-severity-option input[type="radio"]:checked ~ .ulnec-severity-icon {
                transform: scale(1.2);
            }
            
            .ulnec-severity-icon {
                font-size: 2rem;
                margin-bottom: 0.5rem;
                transition: transform 0.3s ease;
            }
            
            .ulnec-severity-label {
                display: block;
                font-weight: 600;
                color: #1a1f3a;
                transition: all 0.3s ease;
            }
            
            .ulnec-severity-desc {
                font-size: 0.85rem;
                margin-top: 0.5rem;
                color: #6b7280;
            }
            
            .ulnec-submit-btn {
                width: 100%;
                padding: 1.5rem;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #fff;
                font-size: 1.2rem;
                font-weight: 700;
                border: none;
                border-radius: 50px;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .ulnec-submit-btn:hover {
                transform: translateY(-3px);
                box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
            }
            
            .ulnec-error {
                background: #fee2e2;
                color: #991b1b;
                padding: 1.5rem;
                border-radius: 15px;
                margin-bottom: 2rem;
                border: 2px solid #fca5a5;
            }
            
            .ulnec-success-container {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                color: #fff;
                padding: 3rem;
                border-radius: 20px;
                text-align: center;
            }
            
            .ulnec-success-container h2 {
                font-size: 2rem;
                margin-bottom: 1rem;
            }
            
            .ulnec-bug-id {
                font-family: 'Courier New', monospace;
                font-size: 1.3rem;
                font-weight: 700;
                background: rgba(255, 255, 255, 0.2);
                padding: 0.75rem 1.5rem;
                border-radius: 10px;
                display: inline-block;
                margin-top: 1rem;
            }
            
            .ulnec-success-actions {
                margin-top: 2rem;
                display: flex;
                gap: 1rem;
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .ulnec-btn-link {
                display: inline-block;
                padding: 1rem 2rem;
                background: rgba(255,255,255,0.2);
                color: #fff;
                text-decoration: none;
                border-radius: 50px;
                font-weight: 600;
                transition: all 0.3s ease;
            }
            
            .ulnec-btn-link:hover {
                background: rgba(255,255,255,0.3);
                transform: translateY(-2px);
            }
            
            @media (max-width: 768px) {
                .ulnec-bug-form-container {
                    padding: 1.5rem;
                }
                
                .ulnec-bug-header h1 {
                    font-size: 2rem;
                }
                
                .ulnec-severity-grid {
                    grid-template-columns: 1fr 1fr;
                }
            }
        </style>
        
        <div class="ulnec-bug-container">
            <div class="ulnec-bug-header">
                <h1>🐛 Report a Bug</h1>
                <p>Help us improve the UL/NEC Compliance Checker</p>
            </div>
            
            <div class="ulnec-founders-note">
                <strong>🏆 Founders Tier Members:</strong>
                This submission counts toward your 3 required bug reports/feature suggestions!
            </div>
            
            <?php if ($error_message): ?>
                <div class="ulnec-error"><?php echo esc_html($error_message); ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="ulnec-success-container">
                    <h2>✓ Bug Report Submitted!</h2>
                    <p style="font-size: 1.1rem; margin-bottom: 1rem;">Thank you for helping us improve!</p>
                    <p>We've received your report and will investigate it soon.</p>
                    <?php if ($bug_id): ?>
                        <div class="ulnec-bug-id">Bug Report ID: <?php echo esc_html($bug_id); ?></div>
                    <?php endif; ?>
                    <p style="margin-top: 1rem; font-size: 0.95rem; opacity: 0.9;">
                        You'll receive email updates when we make progress on this issue.
                    </p>
                    <div class="ulnec-success-actions">
                        <a href="<?php echo home_url('/dashboard'); ?>" class="ulnec-btn-link">Back to Dashboard</a>
                        <a href="<?php echo get_permalink(); ?>" class="ulnec-btn-link">Report Another Bug</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="ulnec-bug-form-container">
                    <form method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field('ulnec_bug_report', 'bug_nonce'); ?>
                        
                        <!-- Bug Title -->
                        <div class="ulnec-form-group">
                            <label>Bug Title <span class="ulnec-required">*</span></label>
                            <input type="text" name="bug_title" required 
                                   placeholder="Brief description of the issue"
                                   value="<?php echo esc_attr($_POST['bug_title'] ?? ''); ?>">
                            <p class="ulnec-help-text">Example: "SCCR calculation incorrect for 3-phase systems"</p>
                        </div>
                        
                        <!-- Detailed Description -->
                        <div class="ulnec-form-group">
                            <label>Detailed Description <span class="ulnec-required">*</span></label>
                            <textarea name="bug_description" required 
                                      placeholder="What happened? What did you expect to happen?"><?php echo esc_textarea($_POST['bug_description'] ?? ''); ?></textarea>
                            <p class="ulnec-help-text">Provide as much detail as possible</p>
                        </div>
                        
                        <!-- Steps to Reproduce -->
                        <div class="ulnec-form-group">
                            <label>Steps to Reproduce <span class="ulnec-required">*</span></label>
                            <textarea name="bug_steps" required 
                                      placeholder="1. Open a drawing&#10;2. Click on...&#10;3. See error"><?php echo esc_textarea($_POST['bug_steps'] ?? ''); ?></textarea>
                            <p class="ulnec-help-text">List the exact steps to reproduce the bug</p>
                        </div>
                        
                        <!-- Expected Behavior -->
                        <div class="ulnec-form-group">
                            <label>Expected Behavior <span class="ulnec-required">*</span></label>
                            <textarea name="bug_expected" required 
                                      placeholder="What should have happened?"><?php echo esc_textarea($_POST['bug_expected'] ?? ''); ?></textarea>
                        </div>
                        
                        <!-- Actual Behavior -->
                        <div class="ulnec-form-group">
                            <label>Actual Behavior <span class="ulnec-required">*</span></label>
                            <textarea name="bug_actual" required 
                                      placeholder="What actually happened?"><?php echo esc_textarea($_POST['bug_actual'] ?? ''); ?></textarea>
                        </div>
                        
                        <!-- CAD Version -->
                        <div class="ulnec-form-group">
                            <label>AutoCAD/BricsCAD Version <span class="ulnec-required">*</span></label>
                            <select name="cad_version" required>
                                <option value="">Select...</option>
                                <option value="AutoCAD 2026">AutoCAD 2026</option>
                                <option value="AutoCAD 2025">AutoCAD 2025</option>
                                <option value="AutoCAD 2024">AutoCAD 2024</option>
                                <option value="BricsCAD V24">BricsCAD V24</option>
                                <option value="BricsCAD V25">BricsCAD V25</option>
                            </select>
                        </div>
                        
                        <!-- Windows Version -->
                        <div class="ulnec-form-group">
                            <label>Windows Version <span class="ulnec-required">*</span></label>
                            <select name="windows_version" required>
                                <option value="">Select...</option>
                                <option value="Windows 11">Windows 11</option>
                                <option value="Windows 10">Windows 10</option>
                            </select>
                        </div>
                        
                        <!-- Plugin Version -->
                        <div class="ulnec-form-group">
                            <label>Plugin Version</label>
                            <input type="text" name="plugin_version" value="1.0 Beta" readonly 
                                   style="background: #f3f4f6;">
                            <p class="ulnec-help-text">Automatically detected</p>
                        </div>
                        
                        <!-- Severity -->
                        <div class="ulnec-form-group">
                            <label>Severity <span class="ulnec-required">*</span></label>
                            <div class="ulnec-severity-grid">
                                <label class="ulnec-severity-option">
                                    <input type="radio" name="bug_severity" value="critical" required>
                                    <div class="ulnec-severity-icon">🔴</div>
                                    <span class="ulnec-severity-label">Critical</span>
                                    <p class="ulnec-severity-desc">Crashes, data loss</p>
                                </label>
                                
                                <label class="ulnec-severity-option">
                                    <input type="radio" name="bug_severity" value="high">
                                    <div class="ulnec-severity-icon">🟠</div>
                                    <span class="ulnec-severity-label">High</span>
                                    <p class="ulnec-severity-desc">Can't complete task</p>
                                </label>
                                
                                <label class="ulnec-severity-option">
                                    <input type="radio" name="bug_severity" value="medium" checked>
                                    <div class="ulnec-severity-icon">🟡</div>
                                    <span class="ulnec-severity-label">Medium</span>
                                    <p class="ulnec-severity-desc">Inconvenient</p>
                                </label>
                                
                                <label class="ulnec-severity-option">
                                    <input type="radio" name="bug_severity" value="low">
                                    <div class="ulnec-severity-icon">💡</div>
                                    <span class="ulnec-severity-label">Low</span>
                                    <p class="ulnec-severity-desc">Enhancement idea</p>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" name="submit_bug_report" class="ulnec-submit-btn">
                            Submit Bug Report
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        
        <?php
        return ob_get_clean();
    }
    
    /**
     * Feature Request Shortcode - Claude_Beta Launch Design
     */
    public function feature_request_shortcode() {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . wp_login_url(get_permalink()) . '">login</a> to request features.</p>';
        }
        
        $current_user = wp_get_current_user();
        $error_message = '';
        $success_message = '';
        $feature_id = '';
        
        // Handle form submission
        if (isset($_POST['submit_feature_request']) && wp_verify_nonce($_POST['feature_nonce'], 'ulnec_feature_request')) {
            $title = sanitize_text_field($_POST['feature_title']);
            $description = sanitize_textarea_field($_POST['feature_description']);
            $category = sanitize_text_field($_POST['feature_category']);
            $importance = sanitize_textarea_field($_POST['feature_importance']);
            $usecase = sanitize_textarea_field($_POST['feature_usecase']);
            $priority = sanitize_text_field($_POST['feature_priority']);
            $workaround = sanitize_textarea_field($_POST['feature_workaround']);
            
            if (empty($title) || empty($description) || empty($category)) {
                $error_message = 'Title, description, and category are required.';
            } else {
                // Get Supabase user
                $supabase_user_response = $this->supabase->request('GET', 'ulnec_users?email=eq.' . urlencode($current_user->user_email));
                
                if (is_wp_error($supabase_user_response)) {
                    error_log('Feature Request - User lookup error: ' . $supabase_user_response->get_error_message());
                    $error_message = 'Failed to verify user account.';
                } elseif (empty($supabase_user_response) || !is_array($supabase_user_response)) {
                    error_log('Feature Request - User not found for email: ' . $current_user->user_email);
                    $error_message = 'User account not found in system.';
                } else {
                    $supabase_user = $supabase_user_response[0];
                    
                    // Submit feature request with proper field mapping
                    // Note: Category is included in description since table doesn't have category field
                    $full_description = "**Category:** " . ucfirst($category) . "\n\n" . $description;
                    if (!empty($importance)) {
                        $full_description .= "\n\n**Why Important:** " . $importance;
                    }
                    if (!empty($workaround)) {
                        $full_description .= "\n\n**Current Workaround:** " . $workaround;
                    }
                    
                    $feature_data = [
                        'user_id' => $supabase_user['id'],
                        'title' => $title,
                        'description' => $full_description,
                        'use_case' => !empty($usecase) ? $usecase : null,
                        'status' => 'submitted',
                        'priority' => !empty($priority) ? $priority : 'medium',
                        'votes' => 1
                    ];
                    
                    error_log('Feature Request - Submitting to Supabase: ' . json_encode($feature_data));
                    
                    $result = $this->supabase->request('POST', '/ulnec_features', $feature_data);
                    
                    if (is_wp_error($result)) {
                        $error_details = $result->get_error_data();
                        $full_error = $result->get_error_message();
                        if (is_array($error_details)) {
                            if (isset($error_details['body'])) {
                                $decoded_body = json_decode($error_details['body'], true);
                                if ($decoded_body) {
                                    $full_error .= ' | Details: ' . json_encode($decoded_body);
                                } else {
                                    $full_error .= ' | Body: ' . $error_details['body'];
                                }
                            }
                            if (isset($error_details['code'])) {
                                $full_error .= ' | HTTP Code: ' . $error_details['code'];
                            }
                        }
                        error_log('Feature Request - Submission failed: ' . $full_error);
                        $error_message = 'Failed to submit feature request. Please try again. (Check error logs for details)';
                    } else {
                        error_log('Feature Request - Success! Result: ' . json_encode($result));
                        $success_message = 'Feature request submitted successfully!';
                        if (isset($result[0]['id'])) {
                            $feature_id = 'FEAT-' . date('Y') . '-' . substr($result[0]['id'], 0, 3);
                        } else {
                            $feature_id = 'FEAT-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                        }
                        
                        // Send confirmation email
                        $plugin = ULNEC_Plugin::instance();
                        if ($plugin && $plugin->emails) {
                            $email_data = [
                                'user_name' => $current_user->display_name,
                                'user_email' => $current_user->user_email,
                                'id' => $feature_id,
                                'title' => $title,
                                'status' => 'submitted',
                                'track_url' => home_url('/founders-progress/')
                            ];
                            $plugin->emails->send_feature_confirmation_email($email_data);
                        }
                        
                        $_POST = [];
                    }
                }
            }
        }
        
        ob_start();
        ?>
        <style>
            /* Hide empty list elements */
            ul:not(.ulnec-form-group ul):not(.ulnec-list):empty,
            ol:not(.ulnec-form-group ol):not(.ulnec-list):empty,
            li:empty {
                display: none !important;
            }
            
            /* Reset list styles for theme elements */
            body ul:not(.ulnec-list),
            body ol:not(.ulnec-list) {
                list-style: none !important;
            }
            
            body li:not(.ulnec-list li) {
                list-style: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Dark Background */
            body {
                background: #1a1f3a !important;
                min-height: 100vh;
            }
            
            .ulnec-feature-container {
                max-width: 900px;
                margin: 0 auto;
                padding: 2rem 1rem;
            }
            .ulnec-feature-header {
                text-align: center;
                margin-bottom: 3rem;
            }
            .ulnec-feature-header h1 {
                font-size: 2.5rem;
                margin-bottom: 1rem;
                color: #ffffff;
            }
            .ulnec-category-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                gap: 1rem;
                margin-top: 0.5rem;
                margin-bottom: 0;
            }
            .ulnec-category-option {
                background: #f9fafb;
                padding: 1.5rem;
                border-radius: 15px;
                border: 2px solid #e5e7eb;
                cursor: pointer;
                transition: all 0.3s ease;
                text-align: center;
                position: relative;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            .ulnec-category-option:hover {
                border-color: #667eea;
                transform: translateY(-2px);
            }
            .ulnec-category-option input[type="radio"] {
                position: absolute;
                opacity: 0;
                width: 0;
                height: 0;
            }
            .ulnec-category-option input[type="radio"]:checked ~ span {
                color: #667eea;
                font-weight: 600;
            }
            .ulnec-category-option input[type="radio"]:checked + .ulnec-category-icon + span {
                color: #667eea;
            }
            .ulnec-category-option input[type="radio"]:checked ~ .ulnec-category-icon {
                transform: scale(1.1);
            }
            .ulnec-category-icon {
                font-size: 2rem;
                margin-bottom: 0.5rem;
                line-height: 1;
                transition: transform 0.3s ease;
            }
            .ulnec-category-option span {
                display: block;
                font-size: 0.9rem;
                color: #1a1f3a;
                font-weight: 600;
                line-height: 1.4;
            }
            
            /* Form Container & Groups */
            .ulnec-bug-form-container {
                background: #fff;
                padding: 3rem;
                border-radius: 20px;
                box-shadow: 0 10px 50px rgba(0, 0, 0, 0.1);
            }
            
            .ulnec-form-group {
                margin-bottom: 2rem;
                clear: both;
            }
            
            .ulnec-form-group label {
                display: block;
                margin-bottom: 0.75rem;
                color: #1a1f3a;
                font-weight: 600;
                font-size: 1rem;
                line-height: 1.5;
            }
            
            .ulnec-required {
                color: #ef4444;
            }
            
            /* Form Inputs */
            .ulnec-form-group input[type="text"],
            .ulnec-form-group textarea,
            .ulnec-form-group select {
                width: 100%;
                max-width: 100%;
                padding: 1rem;
                background: #f9fafb;
                border: 2px solid #e5e7eb;
                border-radius: 10px;
                color: #1a1f3a;
                font-size: 1rem;
                font-family: inherit;
                transition: all 0.3s ease;
                box-sizing: border-box;
                display: block;
            }
            
            .ulnec-form-group input:focus,
            .ulnec-form-group textarea:focus,
            .ulnec-form-group select:focus {
                outline: none;
                border-color: #667eea;
                background: #fff;
            }
            
            .ulnec-form-group textarea {
                min-height: 120px;
                max-height: 300px;
                resize: vertical;
                line-height: 1.6;
            }
            
            .ulnec-form-group select {
                cursor: pointer;
                appearance: auto;
                -webkit-appearance: menulist;
                -moz-appearance: menulist;
            }
            
            .ulnec-help-text {
                font-size: 0.9rem;
                color: #9ca3af;
                margin-top: 0.5rem;
                margin-bottom: 0;
                line-height: 1.5;
            }
            
            /* Submit Button */
            .ulnec-submit-btn {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #fff;
                padding: 1rem 2.5rem;
                border: none;
                border-radius: 10px;
                font-size: 1.1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            }
            
            .ulnec-submit-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            }
            
            /* Success/Error Messages */
            .ulnec-error {
                background: #fee2e2;
                color: #991b1b;
                padding: 1.5rem;
                border-radius: 15px;
                margin-bottom: 2rem;
                border: 2px solid #fca5a5;
            }
            
            .ulnec-success-container {
                background: #d1fae5;
                color: #065f46;
                padding: 3rem;
                border-radius: 20px;
                text-align: center;
                border: 2px solid #6ee7b7;
            }
            
            /* Founders Note */
            .ulnec-founders-note {
                background: linear-gradient(135deg, #fbbf24, #f59e0b);
                color: #000;
                padding: 1.5rem;
                border-radius: 20px;
                margin-bottom: 2rem;
                text-align: center;
            }
            
            .ulnec-founders-note strong {
                display: block;
                margin-bottom: 0.5rem;
                font-size: 1.1rem;
            }
        </style>
        
        <div class="ulnec-feature-container">
            <div class="ulnec-feature-header">
                <h1>💡 Request a Feature</h1>
                <p style="font-size: 1.1rem; color: #c7d2fe;">Help shape the future of UL/NEC Compliance Checker</p>
            </div>
            
            <div class="ulnec-founders-note">
                <strong>🏆 Founders Tier Members:</strong>
                Your feature requests get priority consideration and count toward your requirements!
            </div>
            
            <?php if ($error_message): ?>
                <div class="ulnec-error"><?php echo esc_html($error_message); ?></div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="ulnec-success-container">
                    <h2>✓ Feature Request Submitted!</h2>
                    <p style="font-size: 1.1rem; margin-bottom: 1rem;">Thank you for your suggestion!</p>
                    <p>We've received your feature request and will review it with our development team.</p>
                    <?php if ($feature_id): ?>
                        <div class="ulnec-bug-id">Request ID: <?php echo esc_html($feature_id); ?></div>
                    <?php endif; ?>
                    <p style="margin-top: 1rem; font-size: 0.95rem; opacity: 0.9;">
                        You'll receive email updates on the status of your request.
                    </p>
                    <div class="ulnec-success-actions">
                        <a href="<?php echo home_url('/dashboard'); ?>" class="ulnec-btn-link">Back to Dashboard</a>
                        <a href="<?php echo get_permalink(); ?>" class="ulnec-btn-link">Submit Another</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="ulnec-bug-form-container">
                    <h2 style="color: #667eea; margin-bottom: 2rem; font-size: 1.8rem;">Submit Your Feature Request</h2>
                    
                    <form method="post">
                        <?php wp_nonce_field('ulnec_feature_request', 'feature_nonce'); ?>
                        
                        <div class="ulnec-form-group">
                            <label>Feature Title <span class="ulnec-required">*</span></label>
                            <input type="text" name="feature_title" required 
                                   placeholder="Brief description of the feature"
                                   value="<?php echo esc_attr($_POST['feature_title'] ?? ''); ?>">
                            <p class="ulnec-help-text">Example: "Add metric units support for international users"</p>
                        </div>
                        
                        <div class="ulnec-form-group">
                            <label>Category <span class="ulnec-required">*</span></label>
                            <div class="ulnec-category-grid">
                                <label class="ulnec-category-option">
                                    <input type="radio" name="feature_category" value="ui" required>
                                    <div class="ulnec-category-icon">🎨</div>
                                    <span>UI/UX</span>
                                </label>
                                <label class="ulnec-category-option">
                                    <input type="radio" name="feature_category" value="compliance">
                                    <div class="ulnec-category-icon">✅</div>
                                    <span>Compliance</span>
                                </label>
                                <label class="ulnec-category-option">
                                    <input type="radio" name="feature_category" value="export">
                                    <div class="ulnec-category-icon">📄</div>
                                    <span>Export</span>
                                </label>
                                <label class="ulnec-category-option">
                                    <input type="radio" name="feature_category" value="integration">
                                    <div class="ulnec-category-icon">🔗</div>
                                    <span>Integration</span>
                                </label>
                                <label class="ulnec-category-option">
                                    <input type="radio" name="feature_category" value="performance">
                                    <div class="ulnec-category-icon">⚡</div>
                                    <span>Performance</span>
                                </label>
                                <label class="ulnec-category-option">
                                    <input type="radio" name="feature_category" value="other">
                                    <div class="ulnec-category-icon">💡</div>
                                    <span>Other</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="ulnec-form-group">
                            <label>What feature would you like to see? <span class="ulnec-required">*</span></label>
                            <textarea name="feature_description" required 
                                      placeholder="Describe the feature in detail..."><?php echo esc_textarea($_POST['feature_description'] ?? ''); ?></textarea>
                            <p class="ulnec-help-text">Be specific about what you want and why</p>
                        </div>
                        
                        <div class="ulnec-form-group">
                            <label>Why is this important to you?</label>
                            <textarea name="feature_importance" 
                                      placeholder="Explain the problem this solves or the benefit it provides..."><?php echo esc_textarea($_POST['feature_importance'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="ulnec-form-group">
                            <label>How would you use this feature?</label>
                            <textarea name="feature_usecase" 
                                      placeholder="Describe a typical scenario where you'd use this..."><?php echo esc_textarea($_POST['feature_usecase'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="ulnec-form-group">
                            <label>Priority Level</label>
                            <select name="feature_priority">
                                <option value="medium">Medium - Would be helpful</option>
                                <option value="high">High - Very important</option>
                                <option value="low">Low - Nice to have</option>
                            </select>
                        </div>
                        
                        <div class="ulnec-form-group">
                            <label>Do you have a current workaround?</label>
                            <textarea name="feature_workaround" 
                                      placeholder="If you're currently handling this another way, describe how..."><?php echo esc_textarea($_POST['feature_workaround'] ?? ''); ?></textarea>
                            <p class="ulnec-help-text">Optional, but helps us understand urgency</p>
                        </div>
                        
                        <button type="submit" name="submit_feature_request" class="ulnec-submit-btn">
                            Submit Feature Request
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        
        <?php
        return ob_get_clean();
    }
    
    /**
     * Support Shortcode - Original (will upgrade next)
     */
    public function support_shortcode() {
        // Original implementation...
        return '<p>Support center coming soon...</p>';
    }
    
    /**
     * Founders Progress Shortcode
     */
    public function founders_progress_shortcode() {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . wp_login_url(get_permalink()) . '">login</a> to view your Founders progress.</p>';
        }
        
        $current_user = wp_get_current_user();
        
        // Get user from Supabase
        $supabase_user_response = $this->supabase->request('GET', 'ulnec_users?email=eq.' . urlencode($current_user->user_email));
        
        if (!is_wp_error($supabase_user_response) && !empty($supabase_user_response)) {
            $supabase_user = $supabase_user_response[0];
            
            // Get bugs count
            $bugs_response = $this->supabase->request('GET', 'ulnec_bugs?user_id=eq.' . $supabase_user['id']);
            $bugs_count = is_array($bugs_response) ? count($bugs_response) : 0;
            
            // Get features count
            $features_response = $this->supabase->request('GET', 'ulnec_features?user_id=eq.' . $supabase_user['id']);
            $features_count = is_array($features_response) ? count($features_response) : 0;
            
            $total_submissions = $bugs_count + $features_count;
            $progress_percent = min(($total_submissions / 3) * 100, 100);
            
            ob_start();
            ?>
            <style>
                body {
                    background: #1a1f3a !important;
                    min-height: 100vh;
                }
            </style>
            <div class="ulnec-bug-container">
                <div class="ulnec-bug-header">
                    <h1 style="color: #ffffff;">🏆 Founders Tier Progress</h1>
                    <p style="font-size: 1.1rem; color: #c7d2fe;">Track your progress toward Founders Tier benefits</p>
                </div>
                
                <div class="ulnec-bug-form-container">
                    <h2 style="color: #667eea; margin-bottom: 2rem;">Your Contributions</h2>
                    
                    <div style="background: #f9fafb; padding: 2rem; border-radius: 15px; margin-bottom: 2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <span style="font-weight: 600; color: #1a1f3a;">Bug Reports & Feature Requests</span>
                            <span style="font-size: 1.5rem; font-weight: 700; color: #667eea;"><?php echo $total_submissions; ?> / 3</span>
                        </div>
                        <div style="background: #e5e7eb; height: 20px; border-radius: 10px; overflow: hidden;">
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 100%; width: <?php echo $progress_percent; ?>%; transition: width 0.3s ease;"></div>
                        </div>
                        <p style="margin-top: 1rem; color: #6b7280;">
                            <?php if ($total_submissions >= 3): ?>
                                🎉 Congratulations! You've completed the requirement!
                            <?php else: ?>
                                Submit <?php echo 3 - $total_submissions; ?> more to qualify for Founders Tier
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                        <div style="background: #f9fafb; padding: 1.5rem; border-radius: 15px; text-align: center;">
                            <div style="font-size: 2.5rem; color: #667eea;">🐛</div>
                            <div style="font-size: 2rem; font-weight: 700; color: #1a1f3a; margin-top: 0.5rem;"><?php echo $bugs_count; ?></div>
                            <div style="color: #6b7280;">Bug Reports</div>
                        </div>
                        <div style="background: #f9fafb; padding: 1.5rem; border-radius: 15px; text-align: center;">
                            <div style="font-size: 2.5rem; color: #10b981;">💡</div>
                            <div style="font-size: 2rem; font-weight: 700; color: #1a1f3a; margin-top: 0.5rem;"><?php echo $features_count; ?></div>
                            <div style="color: #6b7280;">Feature Requests</div>
                        </div>
                    </div>
                    
                    <div style="background: linear-gradient(135deg, #fbbf24, #f59e0b); padding: 2rem; border-radius: 15px; color: #000; text-align: center;">
                        <h3 style="margin-bottom: 1rem;">🎁 Founders Tier Benefits</h3>
                        <ul style="list-style: none; padding: 0; text-align: left; max-width: 500px; margin: 0 auto;">
                            <li style="padding: 0.5rem 0;">✓ Lifetime license for $97 (80% off)</li>
                            <li style="padding: 0.5rem 0;">✓ Priority support & feature requests</li>
                            <li style="padding: 0.5rem 0;">✓ Free updates forever</li>
                            <li style="padding: 0.5rem 0;">✓ Beta access to new features</li>
                            <li style="padding: 0.5rem 0;">✓ Name in credits as founding member</li>
                        </ul>
                    </div>
                    
                    <div style="margin-top: 2rem; text-align: center;">
                        <a href="<?php echo home_url('/bug-report'); ?>" class="ulnec-submit-btn" style="display: inline-block; text-decoration: none; margin-right: 1rem;">Report a Bug</a>
                        <a href="<?php echo home_url('/feature-request'); ?>" class="ulnec-submit-btn" style="display: inline-block; text-decoration: none; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">Request Feature</a>
                    </div>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }
        
        return '<p>Unable to load progress information. Please try again.</p>';
    }
    
    /**
     * Account Settings Shortcode
     */
    public function account_settings_shortcode() {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . wp_login_url(get_permalink()) . '">login</a> to view account settings.</p>';
        }
        
        $current_user = wp_get_current_user();
        $success_message = '';
        $error_message = '';
        
        // Handle profile update
        if (isset($_POST['update_profile']) && wp_verify_nonce($_POST['profile_nonce'], 'ulnec_update_profile')) {
            $name = sanitize_text_field($_POST['user_name']);
            $company = sanitize_text_field($_POST['user_company']);
            $phone = sanitize_text_field($_POST['user_phone']);
            
            $supabase_user_response = $this->supabase->request('GET', 'ulnec_users?email=eq.' . urlencode($current_user->user_email));
            
            if (!is_wp_error($supabase_user_response) && !empty($supabase_user_response)) {
                $supabase_user = $supabase_user_response[0];
                
                $update_data = [
                    'name' => $name,
                    'company' => $company,
                    'phone' => $phone
                ];
                
                $result = $this->supabase->request('PATCH', 'ulnec_users?id=eq.' . $supabase_user['id'], $update_data);
                
                if (!is_wp_error($result)) {
                    $success_message = 'Profile updated successfully!';
                    // Refresh user data
                    $supabase_user_response = $this->supabase->request('GET', 'ulnec_users?email=eq.' . urlencode($current_user->user_email));
                    $user_data = !is_wp_error($supabase_user_response) && !empty($supabase_user_response) ? $supabase_user_response[0] : null;
                } else {
                    error_log('Account Settings - Update failed: ' . $result->get_error_message());
                    $error_message = 'Failed to update profile.';
                }
            } else {
                $error_message = 'User account not found in system.';
            }
        }
        
        // Get user data
        if (!isset($user_data)) {
            $supabase_user_response = $this->supabase->request('GET', 'ulnec_users?email=eq.' . urlencode($current_user->user_email));
            $user_data = !is_wp_error($supabase_user_response) && !empty($supabase_user_response) ? $supabase_user_response[0] : null;
            
            // Debug logging
            if ($user_data) {
                error_log('Account Settings - User data loaded: ' . json_encode($user_data));
            } else {
                error_log('Account Settings - No user data found for: ' . $current_user->user_email);
            }
        }
        
        ob_start();
        ?>
        <style>
            body {
                background: #1a1f3a !important;
                min-height: 100vh;
            }
        </style>
        <div class="ulnec-bug-container">
            <div class="ulnec-bug-header">
                <h1 style="color: #ffffff;">⚙️ Account Settings</h1>
                <p style="font-size: 1.1rem; color: #c7d2fe;">Manage your profile and preferences</p>
            </div>
            
            <?php if ($success_message): ?>
                <div style="background: #d1fae5; color: #065f46; padding: 1.5rem; border-radius: 15px; margin-bottom: 2rem; border: 2px solid #6ee7b7;">
                    <?php echo esc_html($success_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="ulnec-error"><?php echo esc_html($error_message); ?></div>
            <?php endif; ?>
            
            <div class="ulnec-bug-form-container">
                <h2 style="color: #667eea; margin-bottom: 2rem;">Profile Information</h2>
                
                <?php if (!$user_data): ?>
                    <div style="background: #fef3c7; color: #92400e; padding: 1.5rem; border-radius: 15px; margin-bottom: 2rem; border: 2px solid #fcd34d;">
                        <strong>⚠️ Account Not Synced</strong><br>
                        Your account hasn't been synced with our system yet. Please contact support.
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    <?php wp_nonce_field('ulnec_update_profile', 'profile_nonce'); ?>
                    
                    <div class="ulnec-form-group">
                        <label>Full Name</label>
                        <input type="text" name="user_name" value="<?php echo esc_attr($user_data['name'] ?? $current_user->display_name); ?>" required>
                    </div>
                    
                    <div class="ulnec-form-group">
                        <label>Email Address</label>
                        <input type="email" value="<?php echo esc_attr($current_user->user_email); ?>" disabled style="background: #e5e7eb; cursor: not-allowed;">
                        <p class="ulnec-help-text">Email cannot be changed. Contact support if needed.</p>
                    </div>
                    
                    <div class="ulnec-form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="user_phone" value="<?php echo esc_attr($user_data['phone'] ?? ''); ?>" placeholder="+1 (234) 567-8900">
                        <p class="ulnec-help-text">Optional - for important account notifications</p>
                    </div>
                    
                    <div class="ulnec-form-group">
                        <label>Company Name</label>
                        <input type="text" name="user_company" value="<?php echo esc_attr($user_data['company'] ?? ''); ?>" placeholder="Your company name">
                    </div>
                    
                    <div class="ulnec-form-group">
                        <label>Account Tier</label>
                        <input type="text" value="<?php echo esc_attr(ucfirst($user_data['tier'] ?? 'Free')); ?>" disabled style="background: #e5e7eb; cursor: not-allowed;">
                        <p class="ulnec-help-text">Current subscription level</p>
                    </div>
                    
                    <div class="ulnec-form-group">
                        <label>Member Since</label>
                        <input type="text" value="<?php echo esc_attr($user_data ? date('F j, Y', strtotime($user_data['created_at'])) : date('F j, Y')); ?>" disabled style="background: #e5e7eb; cursor: not-allowed;">
                    </div>
                    
                    <button type="submit" name="update_profile" class="ulnec-submit-btn">Update Profile</button>
                </form>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Billing Shortcode
     */
    public function billing_shortcode() {
        if (!is_user_logged_in()) {
            return '<p>Please <a href="' . wp_login_url(get_permalink()) . '">login</a> to view billing.</p>';
        }
        
        $current_user = wp_get_current_user();
        
        // Get user and licenses
        $supabase_user_response = $this->supabase->request('GET', 'ulnec_users?email=eq.' . urlencode($current_user->user_email));
        
        if (!is_wp_error($supabase_user_response) && !empty($supabase_user_response)) {
            $supabase_user = $supabase_user_response[0];
            $licenses_response = $this->supabase->request('GET', 'ulnec_licenses?user_id=eq.' . $supabase_user['id']);
            $licenses = is_array($licenses_response) ? $licenses_response : [];
            
            $transactions_response = $this->supabase->request('GET', 'ulnec_transactions?user_id=eq.' . $supabase_user['id'] . '&order=created_at.desc');
            $transactions = is_array($transactions_response) ? $transactions_response : [];
            
            ob_start();
            ?>
            <style>
                body {
                    background: #1a1f3a !important;
                    min-height: 100vh;
                }
                .ulnec-tabs {
                    display: flex;
                    gap: 0.5rem;
                    margin-bottom: 2rem;
                    border-bottom: 2px solid #e5e7eb;
                }
                .ulnec-tab {
                    padding: 1rem 2rem;
                    background: transparent;
                    border: none;
                    color: #c7d2fe;
                    cursor: pointer;
                    font-size: 1rem;
                    font-weight: 600;
                    border-bottom: 3px solid transparent;
                    transition: all 0.3s ease;
                }
                .ulnec-tab:hover {
                    color: #ffffff;
                    border-bottom-color: #667eea;
                }
                .ulnec-tab.active {
                    color: #ffffff;
                    border-bottom-color: #667eea;
                }
                .ulnec-tab-content {
                    display: none;
                }
                .ulnec-tab-content.active {
                    display: block;
                }
            </style>
            
            <script>
                function switchTab(tabName) {
                    // Hide all tabs
                    document.querySelectorAll('.ulnec-tab-content').forEach(tab => {
                        tab.classList.remove('active');
                    });
                    document.querySelectorAll('.ulnec-tab').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    
                    // Show selected tab
                    document.getElementById(tabName).classList.add('active');
                    document.querySelector('[onclick="switchTab(\'' + tabName + '\')"]').classList.add('active');
                }
            </script>
            
            <div class="ulnec-bug-container">
                <div class="ulnec-bug-header">
                    <h1 style="color: #ffffff;">💳 Billing & Subscriptions</h1>
                    <p style="font-size: 1.1rem; color: #c7d2fe;">Manage your licenses and payment history</p>
                </div>
                
                <div class="ulnec-bug-form-container">
                    <!-- Tabs Navigation -->
                    <div class="ulnec-tabs">
                        <button class="ulnec-tab active" onclick="switchTab('licenses')">Licenses</button>
                        <button class="ulnec-tab" onclick="switchTab('transactions')">Transactions</button>
                        <button class="ulnec-tab" onclick="switchTab('payment-settings')">Payment Settings</button>
                    </div>
                    
                    <!-- Licenses Tab -->
                    <div id="licenses" class="ulnec-tab-content active">
                        <h2 style="color: #ffffff; margin-bottom: 2rem; font-size: 1.5rem;">Active Licenses</h2>
                    
                    <?php if (empty($licenses)): ?>
                        <div style="background: #f9fafb; padding: 2rem; border-radius: 15px; text-align: center; margin-bottom: 2rem;">
                            <p style="color: #6b7280; margin-bottom: 1rem; font-size: 1.1rem;">No active licenses found.</p>
                            <?php
                            $pricing_page = (int) get_option('ulnec_page_pricing', 0);
                            $pricing_url  = $pricing_page ? get_permalink($pricing_page) : home_url('/billing');
                            ?>
                            <a href="<?php echo esc_url($pricing_url); ?>" class="ulnec-submit-btn" style="display: inline-block; text-decoration: none;">View Pricing</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($licenses as $license): ?>
                            <div style="background: #f9fafb; padding: 1.5rem; border-radius: 15px; margin-bottom: 1.5rem; border-left: 4px solid <?php echo $license['status'] === 'active' ? '#10b981' : '#6b7280'; ?>;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                    <div>
                                        <h3 style="color: #1a1f3a; margin-bottom: 0.5rem; font-size: 1.3rem;"><?php echo esc_html(ucfirst($license['tier'])); ?> License</h3>
                                        <p style="color: #6b7280; font-size: 0.9rem; margin: 0;">License Key: <code style="background: #fff; padding: 0.25rem 0.5rem; border-radius: 5px; font-size: 0.85rem;"><?php echo esc_html($license['license_key']); ?></code></p>
                                    </div>
                                    <span style="background: <?php echo $license['status'] === 'active' ? '#d1fae5' : '#f3f4f6'; ?>; color: <?php echo $license['status'] === 'active' ? '#065f46' : '#6b7280'; ?>; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.9rem;">
                                        <?php echo esc_html(ucfirst($license['status'])); ?>
                                    </span>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.9rem; color: #6b7280;">
                                    <div><strong style="color: #1a1f3a;">Activated:</strong> <?php echo date('M d, Y', strtotime($license['activated_at'])); ?></div>
                                    <div><strong style="color: #1a1f3a;">Expires:</strong> <?php echo $license['expires_at'] ? date('M d, Y', strtotime($license['expires_at'])) : 'Never'; ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </div>
                    
                    <!-- Transactions Tab -->
                    <div id="transactions" class="ulnec-tab-content">
                    <h2 style="color: #ffffff; margin-bottom: 2rem; font-size: 1.5rem;">Transaction History</h2>
                    
                    <?php if (empty($transactions)): ?>
                        <div style="background: #f9fafb; padding: 2rem; border-radius: 15px; text-align: center;">
                            <p style="color: #6b7280; font-size: 1.1rem;">No transactions found.</p>
                        </div>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f9fafb;">
                                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1a1f3a; font-size: 1rem;">Date</th>
                                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1a1f3a; font-size: 1rem;">Description</th>
                                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1a1f3a; font-size: 1rem;">Amount</th>
                                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1a1f3a; font-size: 1rem;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $transaction): ?>
                                        <tr style="border-bottom: 1px solid #e5e7eb;">
                                            <td style="padding: 1rem; color: #6b7280;"><?php echo date('M d, Y', strtotime($transaction['created_at'])); ?></td>
                                            <td style="padding: 1rem; color: #1a1f3a; font-weight: 500;"><?php echo esc_html($transaction['tier'] ?? 'License'); ?> License</td>
                                            <td style="padding: 1rem; color: #1a1f3a; font-weight: 600; font-size: 1.1rem;">$<?php echo number_format($transaction['amount'], 2); ?></td>
                                            <td style="padding: 1rem;">
                                                <span style="background: #d1fae5; color: #065f46; padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                                    <?php echo esc_html(ucfirst($transaction['status'] ?? 'completed')); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    </div>
                    
                    <!-- Payment Settings Tab -->
                    <div id="payment-settings" class="ulnec-tab-content">
                        <h2 style="color: #ffffff; margin-bottom: 2rem; font-size: 1.5rem;">Payment Methods</h2>
                        
                        <div style="background: #f9fafb; padding: 2rem; border-radius: 15px; margin-bottom: 2rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                                <div>
                                    <h3 style="color: #1a1f3a; margin-bottom: 0.5rem; font-size: 1.2rem;">💳 Credit/Debit Card</h3>
                                    <p style="color: #6b7280; margin: 0; font-size: 0.95rem;">Pay securely with your credit or debit card</p>
                                </div>
                                <span style="background: #d1fae5; color: #065f46; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.85rem;">Available</span>
                            </div>
                            <div style="display: flex; gap: 1rem;">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg" alt="Visa" style="height: 30px;">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" style="height: 30px;">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/American_Express_logo_%282018%29.svg" alt="Amex" style="height: 30px;">
                            </div>
                        </div>
                        
                        <div style="background: #f9fafb; padding: 2rem; border-radius: 15px; margin-bottom: 2rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                                <div>
                                    <h3 style="color: #1a1f3a; margin-bottom: 0.5rem; font-size: 1.2rem;">
                                        <img src="https://www.paypalobjects.com/webstatic/icon/pp258.png" alt="PayPal" style="height: 24px; vertical-align: middle; margin-right: 0.5rem;">
                                        PayPal
                                    </h3>
                                    <p style="color: #6b7280; margin: 0; font-size: 0.95rem;">Fast and secure PayPal checkout</p>
                                </div>
                                <span style="background: #d1fae5; color: #065f46; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.85rem;">Available</span>
                            </div>
                            <p style="color: #6b7280; margin: 0; font-size: 0.9rem;">✓ Buyer protection included</p>
                        </div>
                        
                        <div style="background: #f9fafb; padding: 2rem; border-radius: 15px; margin-bottom: 2rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                                <div>
                                    <h3 style="color: #1a1f3a; margin-bottom: 0.5rem; font-size: 1.2rem;">
                                        <img src="https://razorpay.com/assets/razorpay-glyph.svg" alt="Razorpay" style="height: 24px; vertical-align: middle; margin-right: 0.5rem;">
                                        Razorpay (India)
                                    </h3>
                                    <p style="color: #6b7280; margin: 0; font-size: 0.95rem;">UPI, Net Banking, Cards & Wallets</p>
                                </div>
                                <span style="background: #d1fae5; color: #065f46; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.85rem;">Available</span>
                            </div>
                            <p style="color: #6b7280; margin: 0; font-size: 0.9rem;">✓ Supports all major Indian payment methods</p>
                        </div>
                        
                        <div style="background: #fef3c7; border: 2px solid #fcd34d; padding: 1.5rem; border-radius: 15px; margin-top: 2rem;">
                            <h4 style="color: #92400e; margin: 0 0 0.5rem 0; font-size: 1.1rem;">🔒 Secure Payment Processing</h4>
                            <p style="color: #92400e; margin: 0; font-size: 0.95rem; line-height: 1.6;">
                                All payments are processed securely through encrypted connections. 
                                We never store your complete card details on our servers.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }
        
        return '<p>Unable to load billing information. Please try again.</p>';
    }
}
