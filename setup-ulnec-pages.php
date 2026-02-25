<?php
/**
 * UL/NEC Pages Setup Script
 * 
 * Run this once to create all necessary pages for the UL/NEC Compliance Checker workflow
 * 
 * Usage: Place in WordPress root and visit: yoursite.com/setup-ulnec-pages.php
 * 
 * @package UL_NEC_Compliance
 * @version 1.0.0
 */

// Load WordPress
require_once('wp-load.php');

// Security check - only allow administrators
if (!current_user_can('administrator')) {
    wp_die('Unauthorized access. Only administrators can run this setup.');
}

/**
 * Create UL/NEC pages
 */
function create_ulnec_pages() {
    $pages_created = array();
    $pages_skipped = array();
    
    // Define pages structure
    $pages = array(
        array(
            'title' => 'Login',
            'slug' => 'login',
            'content' => '[ulnec_login]',
            'template' => '',
            'meta_title' => 'Login - UL/NEC Compliance Checker',
            'meta_desc' => 'Login to your UL/NEC Compliance Checker account'
        ),
        array(
            'title' => 'Register',
            'slug' => 'register',
            'content' => '[ulnec_register]',
            'template' => '',
            'meta_title' => 'Register - UL/NEC Compliance Checker',
            'meta_desc' => 'Create your UL/NEC Compliance Checker account and start your 30-day free trial'
        ),
        array(
            'title' => 'Dashboard',
            'slug' => 'dashboard',
            'content' => '[ulnec_dashboard]',
            'template' => '',
            'meta_title' => 'Dashboard - UL/NEC Compliance Checker',
            'meta_desc' => 'Your UL/NEC Compliance Checker dashboard'
        ),
        array(
            'title' => 'Bug Report',
            'slug' => 'bug-report',
            'content' => '[ulnec_bug_report]',
            'template' => '',
            'meta_title' => 'Report a Bug - UL/NEC Compliance Checker',
            'meta_desc' => 'Report issues or bugs with the UL/NEC Compliance Checker'
        ),
        array(
            'title' => 'Feature Request',
            'slug' => 'feature-request',
            'content' => '[ulnec_feature_request]',
            'template' => '',
            'meta_title' => 'Feature Request - UL/NEC Compliance Checker',
            'meta_desc' => 'Suggest new features for the UL/NEC Compliance Checker'
        ),
        array(
            'title' => 'Billing',
            'slug' => 'billing',
            'content' => '[ulnec_billing]

<div class="ulnec-billing-info">
    <h2>Choose Your Plan</h2>
    
    <div class="pricing-grid">
        <div class="pricing-card">
            <h3>Free Trial</h3>
            <p class="price">$0<span>/30 days</span></p>
            <ul>
                <li>✓ Full feature access</li>
                <li>✓ 10 panel checks</li>
                <li>✓ No credit card required</li>
                <li>✓ Instant activation</li>
            </ul>
            <button class="btn-primary">Start Free Trial</button>
        </div>
        
        <div class="pricing-card featured">
            <span class="badge">Best Value</span>
            <h3>Beta Launch Special</h3>
            <p class="price">$75<span>/month forever</span></p>
            <p class="savings">Save 50% - Lock in this price for life!</p>
            <ul>
                <li>✓ Unlimited panel checks</li>
                <li>✓ Priority support</li>
                <li>✓ Automated reports</li>
                <li>✓ API access</li>
                <li>✓ Early access to new features</li>
            </ul>
            <button class="btn-primary">Subscribe Now</button>
        </div>
        
        <div class="pricing-card">
            <h3>Regular Monthly</h3>
            <p class="price">$150<span>/month</span></p>
            <p class="regular-note">After beta launch</p>
            <ul>
                <li>✓ Unlimited panel checks</li>
                <li>✓ Priority support</li>
                <li>✓ Automated reports</li>
                <li>✓ API access</li>
            </ul>
            <button class="btn-secondary">Learn More</button>
        </div>
    </div>
</div>',
            'template' => '',
            'meta_title' => 'Billing & Plans - UL/NEC Compliance Checker',
            'meta_desc' => 'Choose your UL/NEC Compliance Checker subscription plan'
        ),
        array(
            'title' => 'Account Settings',
            'slug' => 'account-settings',
            'content' => '[ulnec_account_settings]',
            'template' => '',
            'meta_title' => 'Account Settings - UL/NEC Compliance Checker',
            'meta_desc' => 'Manage your UL/NEC Compliance Checker account settings'
        ),
    );
    
    // Create each page
    foreach ($pages as $page_data) {
        // Check if page already exists
        $existing_page = get_page_by_path($page_data['slug']);
        
        if ($existing_page) {
            $pages_skipped[] = $page_data['title'] . ' (/' . $page_data['slug'] . ')';
            continue;
        }
        
        // Create page
        $page_id = wp_insert_post(array(
            'post_title'    => $page_data['title'],
            'post_name'     => $page_data['slug'],
            'post_content'  => $page_data['content'],
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_author'   => get_current_user_id(),
            'comment_status' => 'closed',
            'ping_status'   => 'closed',
        ));
        
        if ($page_id && !is_wp_error($page_id)) {
            // Set template if specified
            if (!empty($page_data['template'])) {
                update_post_meta($page_id, '_wp_page_template', $page_data['template']);
            }
            
            // Set SEO meta
            update_post_meta($page_id, '_yoast_wpseo_title', $page_data['meta_title']);
            update_post_meta($page_id, '_yoast_wpseo_metadesc', $page_data['meta_desc']);
            
            $pages_created[] = $page_data['title'] . ' (/' . $page_data['slug'] . ')';
        }
    }
    
    return array(
        'created' => $pages_created,
        'skipped' => $pages_skipped
    );
}

// Run the setup
$result = create_ulnec_pages();

?>
<!DOCTYPE html>
<html>
<head>
    <title>UL/NEC Pages Setup</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f0f0f1;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1e3a8a;
            margin-top: 0;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        ul li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        ul li:before {
            content: "✓ ";
            color: #28a745;
            font-weight: bold;
            margin-right: 10px;
        }
        .skipped li:before {
            content: "→ ";
            color: #ffc107;
        }
        .actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #1e3a8a;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }
        .btn:hover {
            background: #1e40af;
        }
        .workflow {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .workflow h3 {
            margin-top: 0;
        }
        .workflow ol {
            list-style: decimal;
            padding-left: 20px;
        }
        .workflow ol li {
            border: none;
            padding: 5px 0;
        }
        .workflow ol li:before {
            content: "";
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚡ UL/NEC Pages Setup Complete!</h1>
        
        <?php if (!empty($result['created'])): ?>
        <div class="success">
            <h3>✓ Pages Created Successfully</h3>
            <ul>
                <?php foreach ($result['created'] as $page): ?>
                    <li><?php echo esc_html($page); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($result['skipped'])): ?>
        <div class="info">
            <h3>→ Pages Already Exist (Skipped)</h3>
            <ul class="skipped">
                <?php foreach ($result['skipped'] as $page): ?>
                    <li><?php echo esc_html($page); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <div class="workflow">
            <h3>📋 User Workflow</h3>
            <ol>
                <li><strong>Landing Page:</strong> /ul-nec-compliance-checker (with UL/NEC Landing Page template)</li>
                <li><strong>Start Trial:</strong> User clicks button → redirects to /register</li>
                <li><strong>Registration:</strong> /register → User creates account</li>
                <li><strong>Login:</strong> /login → Existing users sign in</li>
                <li><strong>Dashboard:</strong> /dashboard → Main user workspace</li>
                <li><strong>Bug Report:</strong> /bug-report → Submit issues</li>
                <li><strong>Feature Request:</strong> /feature-request → Suggest features</li>
                <li><strong>Billing:</strong> /billing → Manage subscription</li>
                <li><strong>Account:</strong> /account-settings → User preferences</li>
            </ol>
        </div>
        
        <div class="actions">
            <h3>Next Steps:</h3>
            <p>
                <a href="<?php echo admin_url('edit.php?post_type=page'); ?>" class="btn">View All Pages</a>
                <a href="<?php echo home_url('/register'); ?>" class="btn">Test Register Page</a>
                <a href="<?php echo home_url('/login'); ?>" class="btn">Test Login Page</a>
            </p>
            
            <p style="margin-top: 20px; color: #666;">
                <strong>Important:</strong> You can now delete this setup file (setup-ulnec-pages.php) from your WordPress root directory for security.
            </p>
        </div>
    </div>
</body>
</html>
