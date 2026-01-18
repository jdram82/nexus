<?php
/**
 * One-time SaaS admin setup script
 * Run this once to make durgaram@jdsancontrols.com a SaaS admin
 * NOTE: This creates a SAAS ADMIN (UL-NEC product manager) NOT a WordPress admin
 */

// WordPress setup
require_once('../../../wp-load.php');

// Admin email
$admin_email = 'durgaram@jdsancontrols.com';

echo "<h2>Setting up SaaS admin user: $admin_email</h2>";
echo "<p style='background: #e7f3ff; padding: 10px; border-left: 4px solid #0073aa;'><strong>Note:</strong> This creates a SaaS admin for managing UL-NEC products, NOT a WordPress admin.</p>";

// Check if user exists in WordPress
$wp_user = get_user_by('email', $admin_email);

if (!$wp_user) {
    // Create WordPress user with SUBSCRIBER role (not admin)
    echo "<p>Creating WordPress user...</p>";
    $user_id = wp_create_user('durgaram', wp_generate_password(12, true, true), $admin_email);
    
    if (is_wp_error($user_id)) {
        die('<p style="color:red;">Error creating WordPress user: ' . $user_id->get_error_message() . '</p>');
    }
    
    $wp_user = get_user_by('ID', $user_id);
    
    // Set as SUBSCRIBER (minimal WordPress permissions)
    $wp_user->set_role('subscriber');
    
    // Update user meta
    wp_update_user([
        'ID' => $user_id,
        'display_name' => 'Durgaram',
        'first_name' => 'Durgaram',
        'nickname' => 'durgaram'
    ]);
    
    echo "<p style='color:green;'>✓ WordPress user created (role: subscriber)</p>";
} else {
    echo "<p>WordPress user already exists</p>";
    
    // Make sure they're NOT an administrator (keep as subscriber or current role)
    if (in_array('administrator', $wp_user->roles)) {
        echo "<p style='color:orange;'>⚠ User is currently a WordPress administrator. Leave as-is or demote to subscriber?</p>";
        // Uncomment to demote: $wp_user->set_role('subscriber');
    } else {
        echo "<p style='color:green;'>✓ User role: " . implode(', ', $wp_user->roles) . "</p>";
    }
}

// Now update/create Supabase user
echo "<h3>Updating Supabase...</h3>";

// Load Supabase class
require_once(__DIR__ . '/includes/class-ulnec-supabase.php');

$supabase = new ULNEC_Supabase();

// Check if user exists in Supabase
$response = $supabase->request('GET', 'ulnec_users?email=eq.' . urlencode($admin_email));

if (is_wp_error($response)) {
    echo '<p style="color:red;">Error checking Supabase: ' . $response->get_error_message() . '</p>';
} elseif (empty($response)) {
    // Create new user in Supabase with is_admin flag
    echo "<p>Creating Supabase user...</p>";
    
    $user_data = [
        'email' => $admin_email,
        'name' => 'Durgaram',
        'tier' => 'pro',
        'status' => 'active',
        'company' => 'JDS & N Controls',
        'wordpress_user_id' => $wp_user->ID,
        'is_admin' => true  // SaaS admin flag
    ];
    
    $result = $supabase->request('POST', 'ulnec_users', $user_data);
    
    if (is_wp_error($result)) {
        echo '<p style="color:red;">Error creating Supabase user: ' . $result->get_error_message() . '</p>';
    } else {
        echo "<p style='color:green;'>✓ Supabase SaaS admin user created</p>";
        echo "<pre>" . print_r($result, true) . "</pre>";
    }
} else {
    // Update existing user to SaaS admin
    echo "<p>Updating existing Supabase user to SaaS admin...</p>";
    
    $supabase_user = $response[0];
    
    $update_data = [
        'wordpress_user_id' => $wp_user->ID,
        'name' => 'Durgaram',
        'company' => 'JDS & N Controls',
        'tier' => 'pro',
        'is_admin' => true  // SaaS admin flag
    ];
    
    $result = $supabase->request('PATCH', 'ulnec_users?id=eq.' . $supabase_user['id'], $update_data);
    
    if (is_wp_error($result)) {
        echo '<p style="color:red;">Error updating Supabase user: ' . $result->get_error_message() . '</p>';
    } else {
        echo "<p style='color:green;'>✓ Supabase user synced with WordPress</p>";
        echo "<pre>" . print_r($result, true) . "</pre>";
    }
}

echo "<hr>";
echo "<h2 style='color:green;'>✓ Setup Complete!</h2>";
echo "<p><strong>Admin Email:</strong> $admin_email</p>";
echo "<p><strong>WordPress Role:</strong> " . implode(', ', $wp_user->roles) . " (NOT WordPress admin)</p>";
echo "<p><strong>SaaS Admin:</strong> Yes (can manage UL-NEC products)</p>";
echo "<p><strong>Supabase is_admin:</strong> true</p>";
echo "<hr>";
echo "<p style='background: #d4edda; padding: 10px; border-left: 4px solid #28a745;'><strong>✓ Security Improved:</strong> SaaS admin and WordPress admin are now separate roles!</p>";
echo "<p style='background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107;'><strong>IMPORTANT:</strong> Delete this file (setup-admin.php) after running for security reasons.</p>";
echo "<p><a href='/wp-admin/'>Go to WordPress Admin</a></p>";
?>
