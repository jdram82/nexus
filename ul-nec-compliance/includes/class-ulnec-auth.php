<?php
/**
 * Authentication Class
 * 
 * Handles user registration, login, and WordPress-Supabase sync
 */

if (!defined('ABSPATH')) {
    exit;
}

class ULNEC_Auth {
    
    private $supabase;
    
    public function __construct($supabase) {
        $this->supabase = $supabase;
        
        // Sync WordPress user to Supabase on registration
        add_action('user_register', [$this, 'sync_user_on_registration'], 10, 1);
        
        // Sync WordPress user on profile update
        add_action('profile_update', [$this, 'sync_user_on_update'], 10, 2);
    }
    
    /**
     * Sync WordPress user to Supabase on registration
     */
    public function sync_user_on_registration($user_id) {
        $user = get_userdata($user_id);
        
        if (!$user) {
            return;
        }
        
        // Check if user already exists in Supabase
        $existing = $this->supabase->get_user_by_wordpress_id($user_id);
        
        if (!$existing && !is_wp_error($existing)) {
            // Create new Supabase user
            $this->supabase->create_user([
                'wordpress_user_id' => $user_id,
                'email' => $user->user_email,
                'name' => $user->display_name,
                'tier' => 'free'
            ]);
        }
    }
    
    /**
     * Sync WordPress user to Supabase on update
     */
    public function sync_user_on_update($user_id, $old_user_data) {
        $user = get_userdata($user_id);
        
        if (!$user) {
            return;
        }
        
        // Get Supabase user
        $supabase_user = $this->supabase->get_user_by_wordpress_id($user_id);
        
        if ($supabase_user && !is_wp_error($supabase_user)) {
            // Update Supabase user
            $this->supabase->update_user($supabase_user['id'], [
                'email' => $user->user_email,
                'name' => $user->display_name
            ]);
        } else {
            // User doesn't exist in Supabase, create it
            $this->sync_user_on_registration($user_id);
        }
    }
    
    /**
     * Get or create Supabase user for current WordPress user
     */
    public function get_or_create_current_user() {
        if (!is_user_logged_in()) {
            return null;
        }
        
        $user_id = get_current_user_id();
        $supabase_user = $this->supabase->get_user_by_wordpress_id($user_id);
        
        if (!$supabase_user && !is_wp_error($supabase_user)) {
            // User doesn't exist, create it
            $this->sync_user_on_registration($user_id);
            $supabase_user = $this->supabase->get_user_by_wordpress_id($user_id);
        }
        
        return $supabase_user;
    }
    
    /**
     * Check if current user has active license
     */
    public function user_has_active_license() {
        $supabase_user = $this->get_or_create_current_user();
        
        if (!$supabase_user) {
            return false;
        }
        
        $licenses = $this->supabase->get_user_licenses($supabase_user['id']);
        
        if (is_wp_error($licenses)) {
            return false;
        }
        
        foreach ($licenses as $license) {
            if ($license['status'] === 'active') {
                // Check expiration
                if (empty($license['expires_at']) || strtotime($license['expires_at']) > time()) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Get current user's tier
     */
    public function get_user_tier() {
        $supabase_user = $this->get_or_create_current_user();
        
        if (!$supabase_user) {
            return 'free';
        }
        
        return $supabase_user['tier'] ?? 'free';
    }
}
