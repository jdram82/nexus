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
            
            if (empty($title) || empty($description)) {
                $error_message = 'Title and description are required.';
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
                    $error_message = 'Failed to verify user account. Error: ' . $supabase_user_response->get_error_message();
                } elseif (empty($supabase_user_response) || !is_array($supabase_user_response)) {
                    error_log('Bug Report - User not found for email: ' . $current_user->user_email);
                    $error_message = 'User account not found in system. Please contact support.';
                } else {
                    $supabase_user = $supabase_user_response[0];
                    
                    // Submit bug report
                    $bug_data = [
                        'user_id' => $supabase_user['id'],
                        'title' => $title,
                        'description' => $description,
                        'severity' => $severity, // Changed from 'priority' to 'severity' to match DB schema
                        'status' => 'open',
                        'autocad_version' => !empty($autocad_version) ? $autocad_version : null
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
                        $error_message = 'Failed to submit bug report. ' . $result->get_error_message();
                    } else {
                        error_log('Bug Report - Success! Result: ' . json_encode($result));
                        $success_message = 'Bug report submitted successfully! We\'ll review it soon.';
                        // Clear form
                        $_POST = [];
                    }
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
                
                if (is_wp_error($supabase_user_response)) {
                    $error_details = $supabase_user_response->get_error_data();
                    $full_error = $supabase_user_response->get_error_message();
                    if (is_array($error_details) && isset($error_details['body'])) {
                        $full_error .= ' | Body: ' . $error_details['body'];
                    }
                    error_log('Feature Request - User lookup error: ' . $full_error);
                    $error_message = 'Failed to verify user account. Error: ' . $supabase_user_response->get_error_message();
                } elseif (empty($supabase_user_response) || !is_array($supabase_user_response)) {
                    error_log('Feature Request - User not found for email: ' . $current_user->user_email);
                    $error_message = 'User account not found in system. Please contact support.';
                } else {
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
                        $error_message = 'Failed to submit feature request. ' . $result->get_error_message();
                    } else {
                        error_log('Feature Request - Success! Result: ' . json_encode($result));
                        $success_message = 'Feature request submitted successfully! Other users can now vote on it.';
                        $_POST = [];
                    }
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
     * Support Shortcode - Full Support Center
     */
    public function support_shortcode() {
        ob_start();
        ?>
        <style>
            .ulnec-support-center {
                max-width: 1200px;
                margin: 0 auto;
                background: linear-gradient(135deg, #0a0e27 0%, #1a1f3a 100%);
                padding: 3rem 2rem;
                border-radius: 20px;
                color: #fff;
            }
            
            .support-header {
                text-align: center;
                margin-bottom: 3rem;
            }
            
            .support-header h1 {
                font-size: 3rem;
                margin-bottom: 1rem;
                background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            
            .support-header p {
                font-size: 1.3rem;
                opacity: 0.9;
                color: #d1d5db;
            }
            
            .search-box {
                max-width: 700px;
                margin: 0 auto 4rem;
            }
            
            .search-input {
                width: 100%;
                padding: 1.5rem;
                background: rgba(255,255,255,0.1);
                border: 2px solid rgba(255,255,255,0.2);
                border-radius: 50px;
                color: #fff;
                font-size: 1.1rem;
                transition: all 0.3s ease;
            }
            
            .search-input:focus {
                outline: none;
                border-color: #667eea;
                background: rgba(255,255,255,0.15);
            }
            
            .search-input::placeholder {
                color: #9ca3af;
            }
            
            .quick-links {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 2rem;
                margin-bottom: 4rem;
            }
            
            .link-card {
                background: rgba(255,255,255,0.05);
                padding: 2.5rem;
                border-radius: 30px;
                text-align: center;
                border: 2px solid rgba(102, 126, 234, 0.3);
                transition: all 0.3s ease;
                cursor: pointer;
                text-decoration: none;
                color: inherit;
                display: block;
            }
            
            .link-card:hover {
                border-color: #667eea;
                transform: translateY(-10px);
                box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
            }
            
            .link-icon {
                font-size: 4rem;
                margin-bottom: 1.5rem;
            }
            
            .link-card h3 {
                color: #fbbf24;
                margin-bottom: 1rem;
                font-size: 1.5rem;
            }
            
            .link-card p {
                color: #d1d5db;
                line-height: 1.6;
            }
            
            .categories-section {
                margin-bottom: 4rem;
            }
            
            .section-title {
                font-size: 2rem;
                margin-bottom: 2rem;
                color: #fbbf24;
                border-bottom: 2px solid rgba(251, 191, 36, 0.3);
                padding-bottom: 1rem;
            }
            
            .category-grid {
                display: grid;
                gap: 1.5rem;
            }
            
            .category-item {
                background: rgba(255,255,255,0.05);
                padding: 2rem;
                border-radius: 20px;
                border-left: 4px solid #667eea;
                transition: all 0.3s ease;
                cursor: pointer;
            }
            
            .category-item:hover {
                background: rgba(255,255,255,0.08);
                transform: translateX(10px);
            }
            
            .category-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1rem;
            }
            
            .category-header h3 {
                color: #fff;
                font-size: 1.3rem;
            }
            
            .article-count {
                background: rgba(102, 126, 234, 0.3);
                padding: 0.3rem 1rem;
                border-radius: 20px;
                font-size: 0.9rem;
                color: #667eea;
            }
            
            .articles-list {
                list-style: none;
                margin-top: 1rem;
                padding-left: 0;
            }
            
            .articles-list li {
                padding: 0.75rem 0;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }
            
            .articles-list li:last-child {
                border-bottom: none;
            }
            
            .articles-list a {
                color: #d1d5db;
                text-decoration: none;
                transition: color 0.3s ease;
            }
            
            .articles-list a:hover {
                color: #667eea;
            }
            
            .contact-section {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 3rem;
                border-radius: 30px;
                text-align: center;
                margin-bottom: 2rem;
            }
            
            .contact-section h2 {
                font-size: 2rem;
                margin-bottom: 1rem;
            }
            
            .contact-section > p {
                font-size: 1.1rem;
                margin-bottom: 2rem;
                opacity: 0.95;
            }
            
            .contact-methods {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 1.5rem;
                margin-top: 2rem;
            }
            
            .contact-card {
                background: rgba(255,255,255,0.15);
                padding: 2rem;
                border-radius: 20px;
                backdrop-filter: blur(10px);
            }
            
            .contact-card h4 {
                color: #fef3c7;
                margin-bottom: 1rem;
            }
            
            .contact-card a {
                color: #fff;
                font-weight: 600;
                text-decoration: none;
            }
            
            .contact-card a:hover {
                text-decoration: underline;
            }
            
            .status-badge {
                display: inline-block;
                background: #10b981;
                color: #fff;
                padding: 0.5rem 1rem;
                border-radius: 20px;
                font-size: 0.9rem;
                margin-top: 1rem;
            }
            
            @media (max-width: 768px) {
                .support-header h1 {
                    font-size: 2rem;
                }
                .quick-links {
                    grid-template-columns: 1fr;
                }
            }
        </style>
        
        <div class="ulnec-support-center">
            <div class="support-header">
                <h1>How Can We Help?</h1>
                <p>Find answers, guides, and support resources</p>
            </div>

            <!-- Search Box -->
            <div class="search-box">
                <input 
                    type="text" 
                    class="search-input" 
                    placeholder="🔍 Search for help articles, tutorials, or topics..."
                    id="support-search"
                >
            </div>

            <!-- Quick Links -->
            <div class="quick-links">
                <a href="#getting-started" class="link-card">
                    <div class="link-icon">🚀</div>
                    <h3>Getting Started</h3>
                    <p>Installation guides, first steps, and basic tutorials</p>
                </a>

                <a href="#video-tutorials" class="link-card">
                    <div class="link-icon">🎥</div>
                    <h3>Video Tutorials</h3>
                    <p>Watch step-by-step video guides for common tasks</p>
                </a>

                <a href="<?php echo home_url('/bug-report'); ?>" class="link-card">
                    <div class="link-icon">🐛</div>
                    <h3>Report a Bug</h3>
                    <p>Found an issue? Help us improve by reporting it</p>
                </a>

                <a href="<?php echo home_url('/request-feature'); ?>" class="link-card">
                    <div class="link-icon">💡</div>
                    <h3>Request Feature</h3>
                    <p>Share your ideas and vote on upcoming features</p>
                </a>
            </div>

            <!-- Knowledge Base Categories -->
            <div class="categories-section">
                <h2 class="section-title">📚 Knowledge Base</h2>
                
                <div class="category-grid">
                    <div class="category-item" onclick="toggleArticles('installation')">
                        <div class="category-header">
                            <h3>Installation & Setup</h3>
                            <span class="article-count">8 articles</span>
                        </div>
                        <p style="color: #d1d5db; margin-bottom: 1rem;">Everything you need to install and configure the plugin</p>
                        <ul class="articles-list" id="installation" style="display: none;">
                            <li><a href="#">How to install on AutoCAD 2024-2026</a></li>
                            <li><a href="#">Installing on BricsCAD V24+</a></li>
                            <li><a href="#">License activation troubleshooting</a></li>
                            <li><a href="#">Finding the toolbar after installation</a></li>
                            <li><a href="#">Updating to the latest version</a></li>
                            <li><a href="#">Uninstalling the plugin</a></li>
                            <li><a href="#">System requirements explained</a></li>
                            <li><a href="#">Network license configuration</a></li>
                        </ul>
                    </div>

                    <div class="category-item" onclick="toggleArticles('compliance')">
                        <div class="category-header">
                            <h3>Compliance Checking</h3>
                            <span class="article-count">12 articles</span>
                        </div>
                        <p style="color: #d1d5db; margin-bottom: 1rem;">Understanding UL508A and NEC compliance rules</p>
                        <ul class="articles-list" id="compliance" style="display: none;">
                            <li><a href="#">Running your first compliance check</a></li>
                            <li><a href="#">Understanding the 80 UL508A rules</a></li>
                            <li><a href="#">What is SCCR and how is it calculated?</a></li>
                            <li><a href="#">Wire gauge validation explained</a></li>
                            <li><a href="#">Understanding error vs warning messages</a></li>
                            <li><a href="#">How to fix common compliance errors</a></li>
                            <li><a href="#">Customizing rule severity levels</a></li>
                            <li><a href="#">NEC 2023 vs 2020 differences</a></li>
                            <li><a href="#">Component library management</a></li>
                            <li><a href="#">Adding custom components</a></li>
                            <li><a href="#">Exporting compliance reports</a></li>
                            <li><a href="#">Understanding the PDF report format</a></li>
                        </ul>
                    </div>

                    <div class="category-item" onclick="toggleArticles('features')">
                        <div class="category-header">
                            <h3>Features & Tools</h3>
                            <span class="article-count">10 articles</span>
                        </div>
                        <p style="color: #d1d5db; margin-bottom: 1rem;">Make the most of advanced features</p>
                        <ul class="articles-list" id="features" style="display: none;">
                            <li><a href="#">Real-time validation as you draw</a></li>
                            <li><a href="#">Using the SCCR calculator</a></li>
                            <li><a href="#">Auto-generating bill of materials</a></li>
                            <li><a href="#">Batch checking multiple drawings</a></li>
                            <li><a href="#">Keyboard shortcuts reference</a></li>
                            <li><a href="#">Customizing the toolbar</a></li>
                            <li><a href="#">Integration with other CAD plugins</a></li>
                            <li><a href="#">Template drawing setup</a></li>
                            <li><a href="#">Import/export settings</a></li>
                            <li><a href="#">Performance optimization tips</a></li>
                        </ul>
                    </div>

                    <div class="category-item" onclick="toggleArticles('billing')">
                        <div class="category-header">
                            <h3>Billing & Account</h3>
                            <span class="article-count">6 articles</span>
                        </div>
                        <p style="color: #d1d5db; margin-bottom: 1rem;">Manage your subscription and account settings</p>
                        <ul class="articles-list" id="billing" style="display: none;">
                            <li><a href="#">Understanding beta tier pricing</a></li>
                            <li><a href="#">How to update payment method</a></li>
                            <li><a href="#">Viewing billing history and invoices</a></li>
                            <li><a href="#">Upgrading or downgrading tiers</a></li>
                            <li><a href="#">Cancellation and refund policy</a></li>
                            <li><a href="#">Managing multiple licenses</a></li>
                        </ul>
                    </div>

                    <div class="category-item" onclick="toggleArticles('troubleshooting')">
                        <div class="category-header">
                            <h3>Troubleshooting</h3>
                            <span class="article-count">15 articles</span>
                        </div>
                        <p style="color: #d1d5db; margin-bottom: 1rem;">Common issues and solutions</p>
                        <ul class="articles-list" id="troubleshooting" style="display: none;">
                            <li><a href="#">Plugin not loading in AutoCAD</a></li>
                            <li><a href="#">License activation failed</a></li>
                            <li><a href="#">Toolbar disappeared after update</a></li>
                            <li><a href="#">Compliance check runs very slowly</a></li>
                            <li><a href="#">PDF export not working</a></li>
                            <li><a href="#">Error: "Component not found in library"</a></li>
                            <li><a href="#">SCCR calculation shows incorrect value</a></li>
                            <li><a href="#">Plugin crashes when opening large drawings</a></li>
                            <li><a href="#">Cannot connect to license server</a></li>
                            <li><a href="#">Compatibility issues with other plugins</a></li>
                            <li><a href="#">Installation error codes explained</a></li>
                            <li><a href="#">Deactivating license on old computer</a></li>
                            <li><a href="#">Resetting plugin settings to default</a></li>
                            <li><a href="#">Contact support about a persistent issue</a></li>
                            <li><a href="#">Reporting false positive compliance errors</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Contact Section -->
            <div class="contact-section">
                <h2>Still Need Help?</h2>
                <p>Our support team is here to assist you</p>
                
                <div class="contact-methods">
                    <div class="contact-card">
                        <h4>📧 Email Support</h4>
                        <a href="mailto:<?php echo get_option('admin_email'); ?>"><?php echo get_option('admin_email'); ?></a>
                        <p style="margin-top: 1rem; font-size: 0.9rem;">Response within 24-48 hours</p>
                    </div>

                    <div class="contact-card">
                        <h4>💬 Submit Ticket</h4>
                        <a href="<?php echo home_url('/contact'); ?>">Open Support Ticket</a>
                        <div class="status-badge">● Available</div>
                    </div>

                    <div class="contact-card">
                        <h4>📚 Documentation</h4>
                        <a href="#getting-started">Browse Help Center</a>
                        <p style="margin-top: 1rem; font-size: 0.9rem;">Self-service guides</p>
                    </div>

                    <div class="contact-card">
                        <h4>🎥 Video Tutorials</h4>
                        <a href="#video-tutorials">Watch Videos</a>
                        <p style="margin-top: 1rem; font-size: 0.9rem;">Step-by-step guides</p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function toggleArticles(categoryId) {
                const articlesList = document.getElementById(categoryId);
                if (articlesList.style.display === 'none') {
                    articlesList.style.display = 'block';
                } else {
                    articlesList.style.display = 'none';
                }
            }
        </script>
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
