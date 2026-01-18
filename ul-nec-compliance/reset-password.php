<?php
/**
 * One-time password reset script
 * Sets password for durgaram@jdsancontrols.com
 * DELETE THIS FILE AFTER RUNNING
 */

// WordPress setup
require_once('../../../wp-load.php');

// Admin email
$admin_email = 'durgaram@jdsancontrols.com';

// SET YOUR PASSWORD HERE
$new_password = 'Admin@123'; // CHANGE THIS TO YOUR DESIRED PASSWORD

echo "<h2>Resetting password for: $admin_email</h2>";

// Get user
$user = get_user_by('email', $admin_email);

if (!$user) {
    die('<p style="color:red;">Error: User not found!</p>');
}

// Set new password
wp_set_password($new_password, $user->ID);

echo "<p style='color:green;'>✓ Password updated successfully!</p>";
echo "<hr>";
echo "<p><strong>Email:</strong> $admin_email</p>";
echo "<p><strong>Password:</strong> $new_password</p>";
echo "<hr>";
echo "<p style='background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107;'><strong>IMPORTANT:</strong> Delete this file (reset-password.php) immediately for security!</p>";
echo "<p><a href='/wp-admin/'>Go to WordPress Admin</a></p>";
?>
