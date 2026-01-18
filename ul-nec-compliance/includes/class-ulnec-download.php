<?php
/**
 * Download Management Class
 * 
 * Handles secure file downloads with license validation
 */

if (!defined('ABSPATH')) {
    exit;
}

class ULNEC_Download {
    
    private $supabase;
    
    public function __construct($supabase) {
        $this->supabase = $supabase;
        
        // Handle download requests
        add_action('template_redirect', [$this, 'handle_download_request']);
    }
    
    /**
     * Handle download request
     */
    public function handle_download_request() {
        if (!isset($_GET['ulnec_download']) || !isset($_GET['token'])) {
            return;
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_GET['token'], 'ulnec_download')) {
            wp_die('Invalid download token', 'Download Error', ['response' => 403]);
        }
        
        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_redirect(wp_login_url(add_query_arg($_GET, home_url())));
            exit;
        }
        
        // Get current user's Supabase record
        $user_id = get_current_user_id();
        $supabase_user = $this->supabase->get_user_by_wordpress_id($user_id);
        
        if (!$supabase_user || is_wp_error($supabase_user)) {
            wp_die('User not found', 'Download Error', ['response' => 404]);
        }
        
        // Check if user has active license
        $licenses = $this->supabase->get_user_licenses($supabase_user['id']);
        
        if (is_wp_error($licenses) || empty($licenses)) {
            wp_die('No active license found. Please purchase a license first.', 'Download Error', ['response' => 403]);
        }
        
        $has_active_license = false;
        foreach ($licenses as $license) {
            if ($license['status'] === 'active' && (empty($license['expires_at']) || strtotime($license['expires_at']) > time())) {
                $has_active_license = true;
                break;
            }
        }
        
        if (!$has_active_license) {
            wp_die('No active license found. Please purchase or renew your license.', 'Download Error', ['response' => 403]);
        }
        
        // Get download file
        $version = sanitize_text_field($_GET['version'] ?? 'latest');
        $file_name = 'UL-NEC-Compliance-Plugin-Latest.msi';
        
        if (!$file_name) {
            wp_die('File not found', 'Download Error', ['response' => 404]);
        }
        
        // Build direct Supabase Storage URL (public access)
        $supabase_url = defined('ULNEC_SUPABASE_URL') ? ULNEC_SUPABASE_URL : get_option('ulnec_supabase_url');
        $download_url = trailingslashit($supabase_url) . 'storage/v1/object/public/ulnec-downloads/' . $file_name;
        
        // Record download
        $this->supabase->request('POST', '/ulnec_downloads', [
            'user_id' => $supabase_user['id'],
            'license_id' => $license['id'] ?? null,
            'version' => $version,
            'file_name' => $file_name,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
        
        // Redirect to file
        wp_redirect($download_url);
        exit;
    }
    
    /**
     * Get file path for version
     */
    private function get_file_path($version) {
        // You can customize this based on your file structure
        if ($version === 'latest') {
            return 'UL-NEC-Compliance-Plugin-Latest.msi';
        }
        
        return 'UL-NEC-Compliance-Plugin-' . $version . '.msi';
    }
    
    /**
     * Generate download link
     */
    public function get_download_link($version = 'latest') {
        return add_query_arg([
            'ulnec_download' => '1',
            'version' => $version,
            'token' => wp_create_nonce('ulnec_download')
        ], home_url());
    }
    
    /**
     * Get user download history
     */
    public function get_user_download_history($user_id = null) {
        if (!$user_id) {
            if (!is_user_logged_in()) {
                return [];
            }
            $user_id = get_current_user_id();
        }
        
        $supabase_user = $this->supabase->get_user_by_wordpress_id($user_id);
        
        if (!$supabase_user || is_wp_error($supabase_user)) {
            return [];
        }
        
        $downloads = $this->supabase->get_user_downloads($supabase_user['id'], 10);
        
        if (is_wp_error($downloads)) {
            return [];
        }
        
        return $downloads;
    }
}
