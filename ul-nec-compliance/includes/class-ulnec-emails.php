<?php
/**
 * UL/NEC Compliance Plugin - Email Handler
 * 
 * Sends automated emails for user actions
 * 
 * @package UL_NEC_Compliance
 * @since 1.3.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class ULNEC_Emails {
    
    /**
     * Send email wrapper with error logging
     */
    private function send_email($to, $subject, $html_body) {
        if (!is_email($to)) {
            error_log('UL/NEC Email Error: Invalid email address: ' . $to);
            return false;
        }
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: UL/NEC Support <support@jdsancontrols.com>',
        );
        
        $sent = wp_mail($to, $subject, $html_body, $headers);
        
        if (!$sent) {
            error_log('UL/NEC Email Error: Failed to send email to ' . $to . ' - Subject: ' . $subject);
        } else {
            error_log('UL/NEC Email Success: Sent to ' . $to . ' - Subject: ' . $subject);
        }
        
        return $sent;
    }
    
    /**
     * Send Welcome Email on Registration
     */
    public function send_welcome_email($user_data) {
        $html = $this->get_welcome_email_html($user_data);
        return $this->send_email(
            $user_data['email'],
            'Welcome to UL/NEC Compliance Checker',
            $html
        );
    }
    
    /**
     * Send License Delivery Email
     */
    public function send_license_delivery_email($license_data) {
        $html = $this->get_license_delivery_html($license_data);
        return $this->send_email(
            $license_data['customer_email'],
            'Your UL/NEC License Key - ' . strtoupper($license_data['tier']),
            $html
        );
    }
    
    /**
     * Send Bug Report Confirmation
     */
    public function send_bug_confirmation_email($bug_data) {
        $html = $this->get_bug_confirmation_html($bug_data);
        return $this->send_email(
            $bug_data['user_email'],
            'Bug Report Received - #' . $bug_data['id'],
            $html
        );
    }
    
    /**
     * Send Feature Request Confirmation
     */
    public function send_feature_confirmation_email($feature_data) {
        $html = $this->get_feature_confirmation_html($feature_data);
        return $this->send_email(
            $feature_data['user_email'],
            'Feature Request Received - #' . $feature_data['id'],
            $html
        );
    }
    
    /**
     * Send 3-Day Follow-up Email
     */
    public function send_3day_followup_email($user_data) {
        $html = $this->get_3day_followup_html($user_data);
        return $this->send_email(
            $user_data['email'],
            'How\'s your UL/NEC experience so far?',
            $html
        );
    }
    
    /**
     * Send 7-Day Follow-up Email (Trial Reminder)
     */
    public function send_7day_followup_email($user_data) {
        $html = $this->get_7day_followup_html($user_data);
        return $this->send_email(
            $user_data['email'],
            '✨ Your UL/NEC trial is halfway through',
            $html
        );
    }
    
    /**
     * Send Download Reminder Email
     */
    public function send_download_reminder_email($user_data) {
        $html = $this->get_download_reminder_html($user_data);
        return $this->send_email(
            $user_data['email'],
            '🚀 Ready to download UL/NEC Plugin?',
            $html
        );
    }
    
    /**
     * Get email header HTML
     */
    private function get_email_header($title = '') {
        ob_start();
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($title); ?></title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #1a1f3a;
            color: #333333;
        }
        .email-wrapper {
            background-color: #1a1f3a;
            padding: 40px 20px;
            min-height: 100vh;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .email-header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body p {
            line-height: 1.6;
            margin: 0 0 15px;
            color: #1a1f3a;
        }
        .info-box {
            background-color: #f9fafb;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box strong {
            color: #667eea;
            display: block;
            margin-bottom: 10px;
        }
        .license-key-box {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            padding: 25px;
            margin: 25px 0;
            border-radius: 8px;
            text-align: center;
        }
        .license-key {
            font-family: 'Courier New', monospace;
            font-size: 24px;
            font-weight: bold;
            color: #000;
            letter-spacing: 2px;
            margin: 10px 0;
            padding: 15px;
            background-color: rgba(255,255,255,0.9);
            border-radius: 6px;
            word-break: break-all;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .email-footer a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="email-container">
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get email footer HTML
     */
    private function get_email_footer() {
        ob_start();
        ?>
    </div>
</div>
<div class="email-footer">
    <p><strong>UL/NEC Compliance Checker</strong></p>
    <p>Automated electrical code compliance for AutoCAD</p>
    <p style="margin-top: 20px;">
        <a href="https://jdsancontrols.com">Website</a> | 
        <a href="https://jdsancontrols.com/account/">Dashboard</a> | 
        <a href="mailto:support@jdsancontrols.com">Support</a>
    </p>
    <p style="margin-top: 20px; font-size: 12px; color: #9ca3af;">
        © <?php echo date('Y'); ?> JDS and Consols. All rights reserved.
    </p>
</div>
</body>
</html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Welcome Email HTML
     */
    private function get_welcome_email_html($user_data) {
        $header = $this->get_email_header('Welcome to UL/NEC');
        $footer = $this->get_email_footer();
        
        ob_start();
        echo $header;
        ?>
<div class="email-header">
    <h1>Welcome! 🎉</h1>
    <p>Your UL/NEC account is ready</p>
</div>

<div class="email-body">
    <p>Hi <?php echo esc_html($user_data['name']); ?>,</p>
    
    <p>Welcome to UL/NEC Compliance Checker! Your account has been successfully created.</p>
    
    <div class="info-box">
        <strong>🚀 Next Steps:</strong>
        <p style="margin: 10px 0 5px;">1. Purchase a license or apply for Beta access</p>
        <p style="margin: 5px 0;">2. Download the AutoCAD plugin</p>
        <p style="margin: 5px 0;">3. Start checking your electrical drawings!</p>
    </div>
    
    <a href="<?php echo esc_url($user_data['dashboard_url']); ?>" class="button">
        Go to Dashboard
    </a>
    
    <p style="margin-top: 30px; color: #6b7280; font-size: 14px;">
        Questions? Reply to this email or contact support@jdsancontrols.com
    </p>
</div>
        <?php
        echo $footer;
        return ob_get_clean();
    }
    
    /**
     * License Delivery Email HTML
     */
    private function get_license_delivery_html($license_data) {
        $header = $this->get_email_header('Your License Key');
        $footer = $this->get_email_footer();
        
        ob_start();
        echo $header;
        ?>
<div class="email-header">
    <h1>🎉 License Activated!</h1>
    <p>Your payment has been processed</p>
</div>

<div class="email-body">
    <p>Hi <?php echo esc_html($license_data['customer_name']); ?>,</p>
    
    <p>Thank you for your purchase! Here's your license key:</p>
    
    <div class="license-key-box">
        <p style="color: #000; font-size: 14px; margin: 0; font-weight: bold;">Your License Key:</p>
        <div class="license-key">
            <?php echo esc_html($license_data['license_key']); ?>
        </div>
    </div>
    
    <div class="info-box">
        <strong>📥 Download & Install:</strong>
        <p style="margin: 10px 0 5px;">1. Download the .msi installer from your dashboard</p>
        <p style="margin: 5px 0;">2. Run the installer on your computer</p>
        <p style="margin: 5px 0;">3. Enter your license key when prompted</p>
        <p style="margin: 5px 0;">4. Start using in AutoCAD!</p>
    </div>
    
    <a href="<?php echo esc_url($license_data['download_url']); ?>" class="button">
        Download Plugin
    </a>
    
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;">
        <p><strong>License Details:</strong></p>
        <p>Tier: <?php echo esc_html(ucfirst($license_data['tier'])); ?></p>
        <p>Status: Active</p>
        <p>Expires: <?php echo esc_html($license_data['expires_at']); ?></p>
        <p>Max Activations: <?php echo esc_html($license_data['max_activations']); ?></p>
    </div>
</div>
        <?php
        echo $footer;
        return ob_get_clean();
    }
    
    /**
     * Bug Report Confirmation Email HTML
     */
    private function get_bug_confirmation_html($bug_data) {
        $header = $this->get_email_header('Bug Report Received');
        $footer = $this->get_email_footer();
        
        ob_start();
        echo $header;
        ?>
<div class="email-header">
    <h1>🐛 Bug Report Received</h1>
    <p>We're on it!</p>
</div>

<div class="email-body">
    <p>Hi <?php echo esc_html($bug_data['user_name']); ?>,</p>
    
    <p>Thank you for reporting this bug. Our team will review it shortly.</p>
    
    <div class="info-box" style="border-left-color: #ef4444; background-color: #fef3c7;">
        <strong style="color: #ef4444;">Bug Report #<?php echo esc_html($bug_data['id']); ?></strong>
        <p style="margin: 10px 0 5px;"><strong>Title:</strong> <?php echo esc_html($bug_data['title']); ?></p>
        <p style="margin: 5px 0;"><strong>Priority:</strong> <?php echo esc_html(ucfirst($bug_data['priority'])); ?></p>
        <p style="margin: 5px 0;"><strong>Status:</strong> <?php echo esc_html(ucfirst($bug_data['status'])); ?></p>
    </div>
    
    <p>We'll email you when there's an update on your bug report.</p>
    
    <a href="<?php echo esc_url($bug_data['track_url']); ?>" class="button">
        Track Progress
    </a>
</div>
        <?php
        echo $footer;
        return ob_get_clean();
    }
    
    /**
     * Feature Request Confirmation Email HTML
     */
    private function get_feature_confirmation_html($feature_data) {
        $header = $this->get_email_header('Feature Request Received');
        $footer = $this->get_email_footer();
        
        ob_start();
        echo $header;
        ?>
<div class="email-header">
    <h1>💡 Feature Request Received</h1>
    <p>Thanks for the suggestion!</p>
</div>

<div class="email-body">
    <p>Hi <?php echo esc_html($feature_data['user_name']); ?>,</p>
    
    <p>Thank you for submitting your feature request. We appreciate your input!</p>
    
    <div class="info-box" style="border-left-color: #10b981;">
        <strong style="color: #10b981;">Feature Request #<?php echo esc_html($feature_data['id']); ?></strong>
        <p style="margin: 10px 0 5px;"><strong>Title:</strong> <?php echo esc_html($feature_data['title']); ?></p>
        <p style="margin: 5px 0;"><strong>Status:</strong> <?php echo esc_html(ucfirst($feature_data['status'])); ?></p>
    </div>
    
    <p>Your request will be reviewed by our product team. We'll notify you of any updates.</p>
    
    <a href="<?php echo esc_url($feature_data['track_url']); ?>" class="button">
        Track Progress
    </a>
</div>
        <?php
        echo $footer;
        return ob_get_clean();
    }
    
    /**
     * 3-Day Follow-up Email HTML
     */
    private function get_3day_followup_html($user_data) {
        $header = $this->get_email_header('How are you finding UL/NEC?');
        $footer = $this->get_email_footer();
        
        ob_start();
        echo $header;
        ?>
<div class="email-header">
    <h1>👋 Quick Check-in</h1>
    <p>How's your UL/NEC experience?</p>
</div>

<div class="email-body">
    <p>Hi <?php echo esc_html($user_data['name']); ?>,</p>
    
    <p>You registered for UL/NEC Compliance Checker 3 days ago. We wanted to check in and see how things are going!</p>
    
    <div class="info-box" style="border-left-color: #667eea;">
        <strong style="color: #667eea;">Need Help Getting Started?</strong>
        <p style="margin: 10px 0 5px;">📥 Download the plugin installer</p>
        <p style="margin: 5px 0;">📖 Check out our quick start guide</p>
        <p style="margin: 5px 0;">💬 Contact support if you're stuck</p>
    </div>
    
    <a href="<?php echo esc_url($user_data['dashboard_url'] ?? 'https://jdsancontrols.com/dashboard/'); ?>" class="button">
        Download Plugin
    </a>
    
    <div style="margin-top: 30px; padding: 20px; background: #f9fafb; border-radius: 8px;">
        <h3 style="color: #1a1f3a; margin-top: 0;">⏰ Your Trial: 27 Days Remaining</h3>
        <p style="color: #6b7280;">You have full access to all Professional features until <?php echo date('F j, Y', strtotime('+27 days')); ?>.</p>
        
        <p style="margin-top: 15px; color: #1a1f3a;"><strong>Haven't downloaded yet?</strong></p>
        <p style="color: #6b7280;">No worries! Your trial doesn't officially start until you activate the plugin.</p>
    </div>
    
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
        <h3 style="color: #1a1f3a;">Need Assistance?</h3>
        <p style="color: #6b7280;">Reply to this email or contact us at <a href="mailto:support@jdsancontrols.com" style="color: #667eea;">support@jdsancontrols.com</a></p>
        <p style="color: #6b7280; margin-top: 10px;">We typically respond within 24 hours!</p>
    </div>
</div>
        <?php
        echo $footer;
        return ob_get_clean();
    }
    
    /**
     * 7-Day Follow-up Email HTML (Trial Midpoint)
     */
    private function get_7day_followup_html($user_data) {
        $header = $this->get_email_header('Trial Midpoint Update');
        $footer = $this->get_email_footer();
        
        ob_start();
        echo $header;
        ?>
<div class="email-header">
    <h1>⏱️ Trial Update</h1>
    <p>You're halfway through your trial!</p>
</div>

<div class="email-body">
    <p>Hi <?php echo esc_html($user_data['name']); ?>,</p>
    
    <p>You've been with UL/NEC for a week now. Here's a quick update on your trial status:</p>
    
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 8px; color: #fff; text-align: center; margin: 25px 0;">
        <h2 style="color: #fff; margin: 0;">23 Days Remaining</h2>
        <p style="margin: 10px 0 0; opacity: 0.9;">Your trial expires on <?php echo date('F j, Y', strtotime('+23 days')); ?></p>
    </div>
    
    <h3 style="color: #1a1f3a;">🎁 Beta Launch Special - Ending Soon!</h3>
    <p>Lock in <strong>50% OFF for LIFE</strong> before April 30, 2026:</p>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0;">
        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; text-align: center;">
            <h4 style="color: #667eea; margin: 0;">Professional</h4>
            <p style="font-size: 24px; font-weight: 700; margin: 10px 0; color: #1a1f3a;">$37.50/mo</p>
            <p style="font-size: 12px; color: #6b7280; margin: 0;">First 6 months</p>
            <p style="font-size: 14px; color: #6b7280; margin-top: 5px;">Then $75/mo forever</p>
        </div>
        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; text-align: center;">
            <h4 style="color: #10b981; margin: 0;">Team (5 users)</h4>
            <p style="font-size: 24px; font-weight: 700; margin: 10px 0; color: #1a1f3a;">$200/mo</p>
            <p style="font-size: 12px; color: #6b7280; margin: 0;">Year 1</p>
            <p style="font-size: 14px; color: #6b7280; margin-top: 5px;">Then $280/mo forever</p>
        </div>
    </div>
    
    <div class="info-box" style="border-left-color: #f59e0b; background: #fef3c7;">
        <strong style="color: #d97706;">⚠️ Beta Pricing Ends April 30, 2026</strong>
        <p style="margin: 10px 0 0; color: #1a1f3a;">After this date, prices will increase to regular rates ($149/mo and $399/mo). Lock in your founding member discount today!</p>
    </div>
    
    <a href="https://jdsancontrols.com/ulnec-compliance-checker/#pricing" class="button">
        View Pricing Plans
    </a>
    
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
        <h3 style="color: #1a1f3a;">📊 Using the Plugin?</h3>
        <p style="color: #6b7280;">We'd love to hear your feedback! Reply to this email and let us know:</p>
        <ul style="color: #6b7280; line-height: 1.8;">
            <li>What features are you using most?</li>
            <li>Any bugs or issues?</li>
            <li>What would make UL/NEC even better?</li>
        </ul>
        <p style="color: #6b7280; margin-top: 15px;">As a thank you, active beta testers may qualify for our <strong>Founders Tier</strong> with extra perks! 🎁</p>
    </div>
</div>
        <?php
        echo $footer;
        return ob_get_clean();
    }
    
    /**
     * Download Reminder Email HTML (for users who haven't downloaded)
     */
    private function get_download_reminder_html($user_data) {
        $header = $this->get_email_header('Don\'t forget to download!');
        $footer = $this->get_email_footer();
        
        ob_start();
        echo $header;
        ?>
<div class="email-header">
    <h1>🚀 Ready to Get Started?</h1>
    <p>Your UL/NEC plugin is waiting!</p>
</div>

<div class="email-body">
    <p>Hi <?php echo esc_html($user_data['name']); ?>,</p>
    
    <p>We noticed you haven't downloaded the UL/NEC plugin yet. Your free 30-day trial is ready whenever you are!</p>
    
    <div class="info-box" style="border-left-color: #10b981;">
        <strong style="color: #10b981;">✅ What You Get with Your Trial:</strong>
        <p style="margin: 10px 0 5px;">🔍 Full UL508A + NEC compliance checking</p>
        <p style="margin: 5px 0;">📋 Unlimited panel validations</p>
        <p style="margin: 5px 0;">⚡ Wire sizing with auto-derating</p>
        <p style="margin: 5px 0;">📊 BOM generation (Excel, CSV, PDF)</p>
        <p style="margin: 5px 0;">🛡️ SCCR calculations</p>
        <p style="margin: 5px 0;">📈 Detailed compliance reports</p>
    </div>
    
    <a href="<?php echo esc_url($user_data['dashboard_url'] ?? 'https://jdsancontrols.com/dashboard/'); ?>" class="button">
        Download Now (Free)
    </a>
    
    <div style="margin-top: 30px; padding: 20px; background: #f9fafb; border-radius: 8px;">
        <h3 style="color: #1a1f3a; margin-top: 0;">⏱️ Installation Takes 2 Minutes:</h3>
        <ol style="color: #6b7280; line-height: 1.8; padding-left: 20px;">
            <li>Download the .msi installer from your dashboard</li>
            <li>Run the installer (works with AutoCAD 2020-2025)</li>
            <li>Start checking your drawings with ULCHECK command</li>
        </ol>
        <p style="color: #6b7280; margin-top: 15px;"><strong>No credit card required.</strong> Your 30-day trial starts when you first activate the plugin.</p>
    </div>
    
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
        <h3 style="color: #1a1f3a;">💰 Save 15-20 Hours Per Panel</h3>
        <p style="color: #6b7280;">Manual code compliance checking is tedious and error-prone. UL/NEC automates the entire process, saving you hours on every project.</p>
        <p style="color: #6b7280; margin-top: 10px;"><strong>ROI Example:</strong> If you bill at $75/hour and save 15 hours per panel, that's <strong>$1,125 saved per project.</strong></p>
    </div>
    
    <div style="margin-top: 30px; text-align: center; padding: 20px; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border-radius: 8px;">
        <h3 style="color: #000; margin: 0;">🏆 Beta Launch Pricing</h3>
        <p style="color: #000; margin: 10px 0;">Lock in <strong>50% OFF for LIFE</strong> before April 30, 2026</p>
        <p style="color: #000; font-size: 14px; margin: 0;">After trial, just $37.50/mo for 6 months, then $75/mo forever!</p>
    </div>
    
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
        <p style="color: #6b7280; text-align: center;">Questions? Reply to this email or contact <a href="mailto:support@jdsancontrols.com" style="color: #667eea;">support@jdsancontrols.com</a></p>
    </div>
</div>
        <?php
        echo $footer;
        return ob_get_clean();
    }
}
