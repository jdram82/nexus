<?php
/**
 * Download Management Class
 *
 * Handles secure file downloads
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

        // Fallback lookup by email when WordPress ID mapping is missing
        if (!$supabase_user || is_wp_error($supabase_user)) {
            $current_user = wp_get_current_user();
            if ($current_user instanceof WP_User && !empty($current_user->user_email)) {
                $user_response = $this->supabase->request('GET', 'ulnec_users?email=eq.' . urlencode($current_user->user_email));
                if (!is_wp_error($user_response) && is_array($user_response) && !empty($user_response[0])) {
                    $supabase_user = $user_response[0];
                }
            }
        }
        
        // Get download file
        $version = sanitize_text_field($_GET['version'] ?? 'latest');
        $file_name = $this->get_file_path($version);
        
        if (!$file_name) {
            wp_die('File not found', 'Download Error', ['response' => 404]);
        }
        
        // Build direct Supabase Storage URL (public access)
        $supabase_url = defined('ULNEC_SUPABASE_URL') ? ULNEC_SUPABASE_URL : get_option('ulnec_supabase_url');
        if (empty($supabase_url)) {
            wp_die('Download service is not configured. Please contact support.', 'Download Error', ['response' => 500]);
        }
        $download_url = trailingslashit($supabase_url) . 'storage/v1/object/public/ulnec-downloads/' . $file_name;
        
        // Record download (best effort)
        if (is_array($supabase_user) && !empty($supabase_user['id'])) {
            $this->supabase->request('POST', '/ulnec_downloads', [
                'user_id' => $supabase_user['id'],
                'license_id' => null,
                'version' => $version,
                'file_name' => $file_name,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        }
        
        // Redirect to file
        wp_redirect($download_url);
        exit;
    }
    
    /**
     * Get file path for version
     */
    private function get_file_path($version) {
        // Current release filename in Supabase Storage bucket 'ulnec-downloads'
        return 'UL_NEC_RuleEngine_v0.1.0_20260301.msi';
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
