
<?php
/**
 * UL/NEC Compliance Plugin - Email Templates
 * 
 * Copy these functions to your UL/NEC plugin email handler
 * Configure with support@jdsandigitel.com as sender
 * 
 * @package UL_NEC_Compliance
 * @since 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get email header HTML
 */
function ulnec_get_email_header( $title = '' ) {
	ob_start();
	?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo esc_html( $title ); ?></title>
		<style>
			body {
				margin: 0;
				padding: 0;
				font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
				background-color: #f5f5f5;
				color: #333333;
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
			}
			.info-box {
				background-color: #f8f9fa;
				border-left: 4px solid #667eea;
				padding: 20px;
				margin: 25px 0;
				border-radius: 4px;
			}
			.info-box strong {
				color: #667eea;
				display: block;
				margin-bottom: 8px;
				font-size: 14px;
				text-transform: uppercase;
				letter-spacing: 0.5px;
			}
			.info-box p {
				margin: 0;
				font-size: 18px;
				font-weight: 600;
				color: #333;
			}
			.button {
				display: inline-block;
				padding: 14px 32px;
				background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
				color: #ffffff !important;
				text-decoration: none;
				border-radius: 6px;
				font-weight: 600;
				margin: 20px 0;
				text-align: center;
			}
			.button:hover {
				opacity: 0.9;
			}
			.email-footer {
				background-color: #f8f9fa;
				padding: 30px;
				text-align: center;
				color: #666666;
				font-size: 14px;
			}
			.email-footer a {
				color: #667eea;
				text-decoration: none;
			}
			.divider {
				border: 0;
				height: 1px;
				background-color: #e9ecef;
				margin: 30px 0;
			}
			.feature-list {
				background-color: #f8f9fa;
				padding: 20px;
				border-radius: 6px;
				margin: 20px 0;
			}
			.feature-list li {
				margin: 10px 0;
				line-height: 1.6;
			}
		</style>
	</head>
	<body>
		<div class="email-container">
	<?php
	return ob_get_clean();
}

/**
 * Get email footer HTML
 */
function ulnec_get_email_footer() {
	ob_start();
	?>
			<div class="email-footer">
				<p><strong>UL/NEC Compliance Checker</strong></p>
				<p>Automate electrical code compliance for AutoCAD drawings</p>
				<hr style="border: 0; height: 1px; background-color: #e9ecef; margin: 20px 0;">
				<p>
					<a href="https://jdsandigitel.com/support">Support</a> •
					<a href="https://jdsandigitel.com/documentation">Documentation</a> •
					<a href="https://jdsandigitel.com/account">My Account</a>
				</p>
				<p style="font-size: 12px; color: #999;">
					© <?php echo date( 'Y' ); ?> JDS & Digitel. All rights reserved.<br>
					You're receiving this email because you registered for UL/NEC Compliance Checker.
				</p>
			</div>
		</div>
	</body>
	</html>
	<?php
	return ob_get_clean();
}

/**
 * 1. Welcome Email - Sent on successful registration
 * 
 * @param array $user_data User information
 * @return string Email HTML
 */
function ulnec_email_welcome( $user_data ) {
	$header = ulnec_get_email_header( 'Welcome to UL/NEC Compliance Checker' );
	$footer = ulnec_get_email_footer();
	
	ob_start();
	?>
	<?php echo $header; ?>
	
	<div class="email-header">
		<h1>Welcome! 🎉</h1>
		<p>Your UL/NEC account is ready</p>
	</div>
	
	<div class="email-body">
		<p>Hi <?php echo esc_html( $user_data['name'] ); ?>,</p>
		
		<p>Welcome to UL/NEC Compliance Checker! Your account has been successfully created and you're ready to start automating electrical code compliance for your AutoCAD drawings.</p>
		
		<div class="info-box">
			<strong>Your Account Details</strong>
			<p>Email: <?php echo esc_html( $user_data['email'] ); ?></p>
		</div>
		
		<h3>What's Next?</h3>
		<ol>
			<li><strong>Download the AutoCAD Plugin</strong> - Get the .msi installer from your account</li>
			<li><strong>Install on AutoCAD</strong> - Follow the installation wizard</li>
			<li><strong>Activate Your License</strong> - Use your license key to unlock features</li>
			<li><strong>Start Checking Compliance</strong> - Run your first UL/NEC analysis</li>
		</ol>
		
		<center>
			<a href="<?php echo esc_url( $user_data['dashboard_url'] ); ?>" class="button">
				Go to Dashboard →
			</a>
		</center>
		
		<hr class="divider">
		
		<h3>Quick Start Guide</h3>
		<div class="feature-list">
			<ul>
				<li>📥 <strong>Download Plugin:</strong> Account → Downloads → AutoCAD Plugin</li>
				<li>🔑 <strong>Find License Key:</strong> Account → License Information</li>
				<li>📚 <strong>Documentation:</strong> Complete setup guides and video tutorials</li>
				<li>💬 <strong>Support:</strong> Questions? Email support@jdsandigitel.com</li>
			</ul>
		</div>
		
		<p>Need help getting started? Check out our <a href="https://jdsandigitel.com/docs/quick-start">Quick Start Guide</a> or reach out to our support team.</p>
		
		<p>Best regards,<br>
		<strong>The UL/NEC Team</strong></p>
	</div>
	
	<?php echo $footer; ?>
	<?php
	return ob_get_clean();
}

/**
 * 2. License Delivery Email - Sent when license is purchased/generated
 * 
 * @param array $license_data License information
 * @return string Email HTML
 */
function ulnec_email_license_delivery( $license_data ) {
	$header = ulnec_get_email_header( 'Your UL/NEC License Key' );
	$footer = ulnec_get_email_footer();
	
	ob_start();
	?>
	<?php echo $header; ?>
	
	<div class="email-header">
		<h1>Your License Key 🔑</h1>
		<p><?php echo esc_html( ucfirst( $license_data['tier'] ) ); ?> Tier Activated</p>
	</div>
	
	<div class="email-body">
		<p>Hi <?php echo esc_html( $license_data['customer_name'] ); ?>,</p>
		
		<p>Thank you for your purchase! Your <?php echo esc_html( ucfirst( $license_data['tier'] ) ); ?> Tier license for UL/NEC Compliance Checker is ready.</p>
		
		<div class="info-box">
			<strong>Your License Key</strong>
			<p style="font-family: monospace; font-size: 20px; letter-spacing: 2px;">
				<?php echo esc_html( $license_data['license_key'] ); ?>
			</p>
		</div>
		
		<div class="info-box">
			<strong>License Details</strong>
			<p>
				<strong>Tier:</strong> <?php echo esc_html( ucfirst( $license_data['tier'] ) ); ?><br>
				<strong>Status:</strong> Active<br>
				<strong>Expires:</strong> <?php echo esc_html( $license_data['expires_at'] ); ?><br>
				<strong>Max Activations:</strong> <?php echo esc_html( $license_data['max_activations'] ); ?> computer(s)
			</p>
		</div>
		
		<?php if ( 'founders' === $license_data['tier'] ) : ?>
		<div style="background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%); padding: 20px; border-radius: 6px; margin: 20px 0; text-align: center;">
			<h3 style="margin: 0 0 10px; color: #2d3436;">🌟 Founders Tier Member</h3>
			<p style="margin: 0; color: #2d3436;">You're one of the first 50! Enjoy lifetime access and all future updates free forever.</p>
		</div>
		<?php endif; ?>
		
		<h3>Installation Steps</h3>
		<ol>
			<li>Download the AutoCAD plugin from your account dashboard</li>
			<li>Run the .msi installer and follow the wizard</li>
			<li>Open AutoCAD and find the UL/NEC ribbon tab</li>
			<li>Click "Activate License" and enter your license key above</li>
			<li>Start checking compliance!</li>
		</ol>
		
		<center>
			<a href="<?php echo esc_url( $license_data['download_url'] ); ?>" class="button">
				Download AutoCAD Plugin
			</a>
		</center>
		
		<hr class="divider">
		
		<h3>What You Get with <?php echo esc_html( ucfirst( $license_data['tier'] ) ); ?> Tier</h3>
		
		<?php if ( 'standard' === $license_data['tier'] ) : ?>
		<div class="feature-list">
			<ul>
				<li>✅ Automated UL/NEC code compliance checking</li>
				<li>✅ Wire sizing validation (AWG 14-500 MCM)</li>
				<li>✅ Conduit fill calculations</li>
				<li>✅ Voltage drop analysis</li>
				<li>✅ PDF compliance reports</li>
				<li>✅ 1 computer activation</li>
				<li>✅ Email support</li>
			</ul>
		</div>
		<?php elseif ( 'enterprise' === $license_data['tier'] ) : ?>
		<div class="feature-list">
			<ul>
				<li>✅ All Standard features</li>
				<li>✅ Multi-user license (5 seats)</li>
				<li>✅ Batch drawing processing</li>
				<li>✅ Custom compliance templates</li>
				<li>✅ Priority support</li>
				<li>✅ Team collaboration tools</li>
				<li>✅ API access</li>
			</ul>
		</div>
		<?php elseif ( 'founders' === $license_data['tier'] ) : ?>
		<div class="feature-list">
			<ul>
				<li>✅ All features included</li>
				<li>✅ Lifetime access (never expires)</li>
				<li>✅ All future updates free</li>
				<li>✅ Priority support</li>
				<li>✅ Exclusive founders community</li>
				<li>✅ Direct influence on roadmap</li>
			</ul>
		</div>
		<?php endif; ?>
		
		<p><strong>Important:</strong> Keep this email safe! You'll need your license key to activate the plugin. You can also view your license anytime in your account dashboard.</p>
		
		<p>Questions? Our support team is here to help at <a href="mailto:support@jdsandigitel.com">support@jdsandigitel.com</a></p>
		
		<p>Best regards,<br>
		<strong>The UL/NEC Team</strong></p>
	</div>
	
	<?php echo $footer; ?>
	<?php
	return ob_get_clean();
}

/**
 * 3. Bug Report Confirmation Email
 * 
 * @param array $bug_data Bug report information
 * @return string Email HTML
 */
function ulnec_email_bug_confirmation( $bug_data ) {
	$header = ulnec_get_email_header( 'Bug Report Received' );
	$footer = ulnec_get_email_footer();
	
	ob_start();
	?>
	<?php echo $header; ?>
	
	<div class="email-header">
		<h1>Report Received ✓</h1>
		<p>We're on it!</p>
	</div>
	
	<div class="email-body">
		<p>Hi <?php echo esc_html( $bug_data['user_name'] ); ?>,</p>
		
		<p>Thank you for reporting this issue. We've received your bug report and our team is investigating.</p>
		
		<div class="info-box">
			<strong>Report ID</strong>
			<p>#<?php echo esc_html( $bug_data['id'] ); ?></p>
		</div>
		
		<h3>Report Details</h3>
		<div class="feature-list">
			<p><strong>Title:</strong> <?php echo esc_html( $bug_data['title'] ); ?></p>
			<p><strong>Priority:</strong> <?php echo esc_html( ucfirst( $bug_data['priority'] ) ); ?></p>
			<p><strong>Status:</strong> <?php echo esc_html( ucfirst( $bug_data['status'] ) ); ?></p>
			<p><strong>Submitted:</strong> <?php echo esc_html( date( 'F j, Y g:i A', strtotime( $bug_data['created_at'] ) ) ); ?></p>
		</div>
		
		<h3>Your Description</h3>
		<div class="feature-list">
			<p><?php echo nl2br( esc_html( $bug_data['description'] ) ); ?></p>
		</div>
		
		<?php if ( ! empty( $bug_data['screenshot_url'] ) ) : ?>
		<p>📎 <strong>Screenshot attached:</strong> We'll review your screenshot to better understand the issue.</p>
		<?php endif; ?>
		
		<h3>What Happens Next?</h3>
		<ol>
			<li><strong>Investigation (1-2 days):</strong> Our team will reproduce and analyze the issue</li>
			<li><strong>Fix Development:</strong> Based on priority, we'll develop a solution</li>
			<li><strong>Testing:</strong> Thorough QA to ensure the fix works</li>
			<li><strong>Release:</strong> Deploy in the next update</li>
			<li><strong>Notification:</strong> You'll receive an email when it's fixed</li>
		</ol>
		
		<center>
			<a href="<?php echo esc_url( $bug_data['track_url'] ); ?>" class="button">
				Track Report Status
			</a>
		</center>
		
		<hr class="divider">
		
		<p><strong>Response Time:</strong></p>
		<ul>
			<li>🔴 <strong>Critical:</strong> Same day response</li>
			<li>🟡 <strong>High:</strong> Within 24 hours</li>
			<li>🟢 <strong>Medium:</strong> Within 2-3 days</li>
			<li>🔵 <strong>Low:</strong> Within 1 week</li>
		</ul>
		
		<p>You can always check your report status in your account dashboard under "Bug Reports".</p>
		
		<p>Thank you for helping us improve UL/NEC Compliance Checker!</p>
		
		<p>Best regards,<br>
		<strong>The UL/NEC Support Team</strong></p>
	</div>
	
	<?php echo $footer; ?>
	<?php
	return ob_get_clean();
}

/**
 * 4. Feature Request Confirmation Email
 * 
 * @param array $feature_data Feature request information
 * @return string Email HTML
 */
function ulnec_email_feature_confirmation( $feature_data ) {
	$header = ulnec_get_email_header( 'Feature Request Received' );
	$footer = ulnec_get_email_footer();
	
	ob_start();
	?>
	<?php echo $header; ?>
	
	<div class="email-header">
		<h1>Great Idea! 💡</h1>
		<p>Feature request received</p>
	</div>
	
	<div class="email-body">
		<p>Hi <?php echo esc_html( $feature_data['user_name'] ); ?>,</p>
		
		<p>Thank you for your feature suggestion! We love hearing ideas from our users. Your request has been added to our product roadmap.</p>
		
		<div class="info-box">
			<strong>Request ID</strong>
			<p>#<?php echo esc_html( $feature_data['id'] ); ?></p>
		</div>
		
		<h3>Your Feature Request</h3>
		<div class="feature-list">
			<p><strong>Title:</strong> <?php echo esc_html( $feature_data['title'] ); ?></p>
			<p><strong>Category:</strong> <?php echo esc_html( ucfirst( $feature_data['category'] ) ); ?></p>
			<p><strong>Status:</strong> Under Review</p>
			<p><strong>Votes:</strong> 1 (yours!)</p>
		</div>
		
		<h3>Your Description</h3>
		<div class="feature-list">
			<p><?php echo nl2br( esc_html( $feature_data['description'] ) ); ?></p>
		</div>
		
		<h3>Evaluation Process</h3>
		<ol>
			<li><strong>Review:</strong> Product team evaluates feasibility and value</li>
			<li><strong>Voting:</strong> Community upvotes most-wanted features</li>
			<li><strong>Prioritization:</strong> Features ranked by demand and impact</li>
			<li><strong>Development:</strong> Top requests added to sprint planning</li>
			<li><strong>Release:</strong> Announcement when feature ships</li>
		</ol>
		
		<center>
			<a href="<?php echo esc_url( $feature_data['vote_url'] ); ?>" class="button">
				View & Vote on Features
			</a>
		</center>
		
		<hr class="divider">
		
		<div style="background-color: #e7f3ff; padding: 20px; border-radius: 6px; border-left: 4px solid #2196F3;">
			<p style="margin: 0;"><strong>💡 Pro Tip:</strong> Share your feature request with colleagues and ask them to upvote! Features with more votes get prioritized faster.</p>
		</div>
		
		<p>We'll notify you via email when there are updates on your request, including when it moves to development or gets released.</p>
		
		<p>Thank you for helping shape the future of UL/NEC Compliance Checker!</p>
		
		<p>Best regards,<br>
		<strong>The UL/NEC Product Team</strong></p>
	</div>
	
	<?php echo $footer; ?>
	<?php
	return ob_get_clean();
}

/**
 * 5. License Expiration Warning (7 days before)
 * 
 * @param array $license_data License information
 * @return string Email HTML
 */
function ulnec_email_license_expiring( $license_data ) {
	$header = ulnec_get_email_header( 'License Expiring Soon' );
	$footer = ulnec_get_email_footer();
	
	$days_remaining = ceil( ( strtotime( $license_data['expires_at'] ) - time() ) / DAY_IN_SECONDS );
	
	ob_start();
	?>
	<?php echo $header; ?>
	
	<div class="email-header">
		<h1>Renewal Reminder ⏰</h1>
		<p><?php echo $days_remaining; ?> days remaining</p>
	</div>
	
	<div class="email-body">
		<p>Hi <?php echo esc_html( $license_data['customer_name'] ); ?>,</p>
		
		<p>Your UL/NEC Compliance Checker license is expiring soon. Renew now to continue enjoying uninterrupted access to all features.</p>
		
		<div class="info-box">
			<strong>License Expiration</strong>
			<p><?php echo esc_html( date( 'F j, Y', strtotime( $license_data['expires_at'] ) ) ); ?></p>
			<p style="font-size: 14px; color: #666; margin-top: 5px;">
				(<?php echo $days_remaining; ?> days from now)
			</p>
		</div>
		
		<div class="info-box">
			<strong>Current License</strong>
			<p>
				<strong>Key:</strong> <?php echo esc_html( $license_data['license_key'] ); ?><br>
				<strong>Tier:</strong> <?php echo esc_html( ucfirst( $license_data['tier'] ) ); ?><br>
				<strong>Status:</strong> Active
			</p>
		</div>
		
		<h3>What Happens When It Expires?</h3>
		<ul>
			<li>❌ UL/NEC plugin will stop working in AutoCAD</li>
			<li>❌ No compliance checking or reports</li>
			<li>❌ No access to downloads and updates</li>
			<li>❌ Support requests will be limited</li>
		</ul>
		
		<h3>Renew Now & Save Your Workflow</h3>
		<p>Keep your projects on track without interruption. Renewing extends your license for another year with all the same features.</p>
		
		<center>
			<a href="<?php echo esc_url( $license_data['renewal_url'] ); ?>" class="button">
				Renew License Now
			</a>
		</center>
		
		<hr class="divider">
		
		<div style="background-color: #fff3cd; padding: 20px; border-radius: 6px; border-left: 4px solid #ffc107;">
			<p style="margin: 0;"><strong>🎁 Renewal Discount:</strong> Use code <strong>RENEW15</strong> for 15% off your renewal!</p>
		</div>
		
		<p>Questions about renewal? Contact us at <a href="mailto:support@jdsandigitel.com">support@jdsandigitel.com</a></p>
		
		<p>Best regards,<br>
		<strong>The UL/NEC Team</strong></p>
	</div>
	
	<?php echo $footer; ?>
	<?php
	return ob_get_clean();
}

/**
 * 6. Password Reset Email
 * 
 * @param array $user_data User and reset information
 * @return string Email HTML
 */
function ulnec_email_password_reset( $user_data ) {
	$header = ulnec_get_email_header( 'Reset Your Password' );
	$footer = ulnec_get_email_footer();
	
	ob_start();
	?>
	<?php echo $header; ?>
	
	<div class="email-header">
		<h1>Password Reset 🔐</h1>
		<p>Reset your account password</p>
	</div>
	
	<div class="email-body">
		<p>Hi <?php echo esc_html( $user_data['name'] ); ?>,</p>
		
		<p>We received a request to reset your password for your UL/NEC account associated with <strong><?php echo esc_html( $user_data['email'] ); ?></strong>.</p>
		
		<p>Click the button below to create a new password:</p>
		
		<center>
			<a href="<?php echo esc_url( $user_data['reset_url'] ); ?>" class="button">
				Reset Password
			</a>
		</center>
		
		<p style="font-size: 14px; color: #666; margin-top: 20px;">
			<strong>Link expires in:</strong> 60 minutes<br>
			<strong>Requested from IP:</strong> <?php echo esc_html( $user_data['ip_address'] ); ?>
		</p>
		
		<hr class="divider">
		
		<div style="background-color: #f8d7da; padding: 20px; border-radius: 6px; border-left: 4px solid #dc3545;">
			<p style="margin: 0;"><strong>⚠️ Didn't request this?</strong> If you didn't ask to reset your password, ignore this email. Your password will remain unchanged and secure.</p>
		</div>
		
		<p>For security reasons, this link will only work once and expires in 1 hour.</p>
		
		<p>If you're having trouble clicking the button, copy and paste this URL into your browser:</p>
		<p style="font-size: 12px; word-break: break-all; background-color: #f8f9fa; padding: 10px; border-radius: 4px;">
			<?php echo esc_url( $user_data['reset_url'] ); ?>
		</p>
		
		<p>Need help? Contact us at <a href="mailto:support@jdsandigitel.com">support@jdsandigitel.com</a></p>
		
		<p>Best regards,<br>
		<strong>The UL/NEC Security Team</strong></p>
	</div>
	
	<?php echo $footer; ?>
	<?php
	return ob_get_clean();
}

/**
 * Send email using WordPress wp_mail()
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $message Email HTML body
 * @return bool True on success, false on failure
 */
function ulnec_send_email( $to, $subject, $message ) {
	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
		'From: UL/NEC Support <support@jdsandigitel.com>',
	);
	
	return wp_mail( $to, $subject, $message, $headers );
}

/**
 * Example usage in your plugin:
 * 
 * // Send welcome email
 * $user_data = array(
 *     'name' => 'John Doe',
 *     'email' => 'john@example.com',
 *     'dashboard_url' => 'https://your-site.com/account'
 * );
 * $html = ulnec_email_welcome( $user_data );
 * ulnec_send_email( $user_data['email'], 'Welcome to UL/NEC Compliance Checker', $html );
 * 
 * // Send license delivery
 * $license_data = array(
 *     'customer_name' => 'John Doe',
 *     'license_key' => 'ABCD-1234-EFGH-5678',
 *     'tier' => 'standard',
 *     'expires_at' => '2027-02-13',
 *     'max_activations' => 1,
 *     'download_url' => 'https://your-site.com/download'
 * );
 * $html = ulnec_email_license_delivery( $license_data );
 * ulnec_send_email( $license_data['customer_email'], 'Your UL/NEC License Key', $html );
 */
