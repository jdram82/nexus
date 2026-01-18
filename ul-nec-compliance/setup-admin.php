<?php
/**
 * One-time admin setup script
 * Run this once to make durgaram@jdsancontrols.com an admin
 */

// WordPress setup
require_once('../../../wp-load.php');

if (!current_user_can('administrator')) {
    die('You must be logged in as an administrator to run this script.');
}

// Admin email
$admin_email = 'durgaram@jdsancontrols.com';

echo "<h2>Setting up admin user: $admin_email</h2>";

// Check if user exists in WordPress
$wp_user = get_user_by('email', $admin_email);

if (!$wp_user) {
    // Create WordPress user
    echo "<p>Creating WordPress user...</p>";
    $user_id = wp_create_user('durgaram', wp_generate_password(12, true, true), $admin_email);
    
    if (is_wp_error($user_id)) {
        die('<p style="color:red;">Error creating WordPress user: ' . $user_id->get_error_message() . '</p>');
    }
    
    $wp_user = get_user_by('ID', $user_id);
    
    // Set as administrator
    $wp_user->set_role('administrator');
    
    // Update user meta
    wp_update_user([
        'ID' => $user_id,
        'display_name' => 'Durgaram',
        'first_name' => 'Durgaram',
        'nickname' => 'durgaram'
    ]);
    
    echo "<p style='color:green;'>✓ WordPress admin user created</p>";
} else {
    echo "<p>WordPress user already exists</p>";
    
    // Make sure they're an administrator
    if (!in_array('administrator', $wp_user->roles)) {
        $wp_user->set_role('administrator');
        echo "<p style='color:green;'>✓ User promoted to administrator</p>";
    } else {
        echo "<p style='color:green;'>✓ User is already an administrator</p>";
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
    // Create new user in Supabase
    echo "<p>Creating Supabase user...</p>";
    
    $user_data = [
        'email' => $admin_email,
        'name' => 'Durgaram',
        'tier' => 'pro',
        'status' => 'active',
        'company' => 'JDS & N Controls',
        'wordpress_user_id' => $wp_user->ID
    ];
    
    $result = $supabase->request('POST', 'ulnec_users', $user_data);
    
    if (is_wp_error($result)) {
        echo '<p style="color:red;">Error creating Supabase user: ' . $result->get_error_message() . '</p>';
    } else {
        echo "<p style='color:green;'>✓ Supabase admin user created</p>";
        echo "<pre>" . print_r($result, true) . "</pre>";
    }
} else {
    // Update existing user to admin
    echo "<p>Updating existing Supabase user to admin...</p>";
    
    $supabase_user = $response[0];
    
    $update_data = [
        'wordpress_user_id' => $wp_user->ID,
        'name' => 'Durgaram',
        'company' => 'JDS & N Controls',
        'tier' => 'pro'
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
echo "<p><strong>WordPress Role:</strong> Administrator</p>";
echo "<p><strong>Supabase Tier:</strong> Pro (unlimited access)</p>";
echo "<hr>";
echo "<p style='background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107;'><strong>IMPORTANT:</strong> Delete this file (setup-admin.php) after running for security reasons.</p>";
echo "<p><a href='/wp-admin/'>Go to WordPress Admin</a></p>";
?>
