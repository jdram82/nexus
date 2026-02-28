<?php
/**
 * Supabase Integration Class
 * 
 * Handles all communication with Supabase backend
 */

if (!defined('ABSPATH')) {
    exit;
}

class ULNEC_Supabase {
    
    /**
     * Supabase project URL
     */
    private $url;
    
    /**
     * Supabase anon key (for client-side requests)
     */
    private $anon_key;
    
    /**
     * Supabase service role key (for server-side requests, bypasses RLS)
     */
    private $service_key;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->url = defined('ULNEC_SUPABASE_URL') ? ULNEC_SUPABASE_URL : get_option('ulnec_supabase_url');
        $this->anon_key = defined('ULNEC_SUPABASE_ANON_KEY') ? ULNEC_SUPABASE_ANON_KEY : get_option('ulnec_supabase_anon_key');
        $this->service_key = defined('ULNEC_SUPABASE_SERVICE_KEY') ? ULNEC_SUPABASE_SERVICE_KEY : get_option('ulnec_supabase_service_key');
    }
    
    /**
     * Test connection to Supabase
     */
    public function test_connection() {
        $response = $this->request('GET', '/ulnec_users?limit=1');
        return !is_wp_error($response);
    }
    
    /**
     * Make HTTP request to Supabase
     */
    public function request($method, $endpoint, $data = [], $use_service_key = true) {
        if (empty($this->url) || empty($this->anon_key)) {
            return new WP_Error('no_credentials', 'Supabase credentials not configured');
        }
        
        // Ensure endpoint starts with /
        if (substr($endpoint, 0, 1) !== '/') {
            $endpoint = '/' . $endpoint;
        }
        
        $url = trailingslashit($this->url) . 'rest/v1' . $endpoint;
        $key = $use_service_key && !empty($this->service_key) ? $this->service_key : $this->anon_key;
        
        $args = [
            'method' => $method,
            'headers' => [
                'apikey' => $key,
                'Authorization' => 'Bearer ' . $key,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation'
            ],
            'timeout' => 30
        ];
        
        if (!empty($data) && in_array($method, ['POST', 'PATCH', 'PUT'])) {
            $args['body'] = json_encode($data);
        }
        
        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($code >= 400) {
            $error_data = json_decode($body, true);
            $error_message = isset($error_data['message']) ? $error_data['message'] : $body;
            
            return new WP_Error(
                'supabase_error',
                sprintf('Supabase API Error (Code %d): %s', $code, $error_message),
                ['code' => $code, 'body' => $body]
            );
        }
        
        return json_decode($body, true);
    }

    /**
     * Make HTTP request to Supabase Auth API.
     */
    public function auth_request($method, $endpoint, $data = [], $use_service_key = false) {
        if (empty($this->url) || empty($this->anon_key)) {
            return new WP_Error('no_credentials', 'Supabase credentials not configured');
        }

        if (substr($endpoint, 0, 1) !== '/') {
            $endpoint = '/' . $endpoint;
        }

        $url = trailingslashit($this->url) . 'auth/v1' . $endpoint;
        $key = $use_service_key && !empty($this->service_key) ? $this->service_key : $this->anon_key;

        $args = [
            'method' => $method,
            'headers' => [
                'apikey' => $key,
                'Authorization' => 'Bearer ' . $key,
                'Content-Type' => 'application/json'
            ],
            'timeout' => 30
        ];

        if (!empty($data) && in_array($method, ['POST', 'PATCH', 'PUT'])) {
            $args['body'] = json_encode($data);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $decoded = json_decode($body, true);

        if ($code >= 400) {
            $error_message = '';

            if (is_array($decoded)) {
                if (!empty($decoded['msg'])) {
                    $error_message = $decoded['msg'];
                } elseif (!empty($decoded['error_description'])) {
                    $error_message = $decoded['error_description'];
                } elseif (!empty($decoded['message'])) {
                    $error_message = $decoded['message'];
                }
            }

            if (empty($error_message)) {
                $error_message = $body;
            }

            return new WP_Error(
                'supabase_auth_error',
                sprintf('Authentication service error (Code %d): %s', $code, $error_message),
                ['code' => $code, 'body' => $decoded]
            );
        }

        return $decoded;
    }

    /**
     * Create a user in Supabase Auth (admin API).
     */
    public function create_auth_user($email, $password, $metadata = []) {
        if (empty($this->service_key)) {
            return new WP_Error('no_service_key', 'Supabase service role key not configured');
        }

        return $this->auth_request('POST', '/admin/users', [
            'email' => $email,
            'password' => $password,
            'email_confirm' => true,
            'user_metadata' => (array) $metadata,
        ], true);
    }

    /**
     * Sign up a user via Supabase Auth and trigger email confirmation flow.
     */
    public function sign_up_user($email, $password, $metadata = []) {
        return $this->auth_request('POST', '/signup', [
            'email' => $email,
            'password' => $password,
            'data' => (array) $metadata,
        ], false);
    }

    /**
     * Authenticate via Supabase Auth using password grant.
     */
    public function sign_in_with_password($email, $password) {
        $query = '/token?grant_type=password';

        return $this->auth_request('POST', $query, [
            'email' => $email,
            'password' => $password,
        ], false);
    }
    
    /**
     * Get user by WordPress ID
     */
    public function get_user_by_wordpress_id($wordpress_id) {
        $result = $this->request('GET', '/ulnec_users?wordpress_user_id=eq.' . intval($wordpress_id));
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Get user by email
     */
    public function get_user_by_email($email) {
        $result = $this->request('GET', '/ulnec_users?email=eq.' . urlencode($email));
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Create user
     */
    public function create_user($data) {
        return $this->request('POST', '/ulnec_users', $data);
    }
    
    /**
     * Update user
     */
    public function update_user($id, $data) {
        return $this->request('PATCH', '/ulnec_users?id=eq.' . $id, $data);
    }
    
    /**
     * Get license by key
     */
    public function get_license_by_key($license_key) {
        $result = $this->request('GET', '/ulnec_licenses?license_key=eq.' . urlencode($license_key));
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Get user licenses
     */
    public function get_user_licenses($user_id) {
        return $this->request('GET', '/ulnec_licenses?user_id=eq.' . $user_id);
    }
    
    /**
     * Create license
     */
    public function create_license($data) {
        return $this->request('POST', '/ulnec_licenses', $data);
    }
    
    /**
     * Update license
     */
    public function update_license($id, $data) {
        return $this->request('PATCH', '/ulnec_licenses?id=eq.' . $id, $data);
    }
    
    /**
     * Record download
     */
    public function record_download($data) {
        return $this->request('POST', '/ulnec_downloads', $data);
    }
    
    /**
     * Get user downloads
     */
    public function get_user_downloads($user_id, $limit = 10) {
        return $this->request('GET', '/ulnec_downloads?user_id=eq.' . $user_id . '&order=downloaded_at.desc&limit=' . $limit);
    }
    
    /**
     * Create bug report
     */
    public function create_bug($data) {
        return $this->request('POST', '/ulnec_bugs', $data);
    }
    
    /**
     * Get bugs
     */
    public function get_bugs($filters = [], $limit = 50) {
        $query = '/ulnec_bugs?order=created_at.desc&limit=' . $limit;
        
        if (!empty($filters['status'])) {
            $query .= '&status=eq.' . $filters['status'];
        }
        
        if (!empty($filters['user_id'])) {
            $query .= '&user_id=eq.' . $filters['user_id'];
        }
        
        return $this->request('GET', $query);
    }
    
    /**
     * Create feature request
     */
    public function create_feature($data) {
        return $this->request('POST', '/ulnec_features', $data);
    }
    
    /**
     * Get features
     */
    public function get_features($order_by = 'votes', $limit = 50) {
        $query = '/ulnec_features?order=' . $order_by . '.desc&limit=' . $limit;
        return $this->request('GET', $query);
    }
    
    /**
     * Vote for feature
     */
    public function vote_feature($feature_id, $user_id) {
        return $this->request('POST', '/ulnec_feature_votes', [
            'feature_id' => $feature_id,
            'user_id' => $user_id
        ]);
    }
    
    /**
     * Create subscription
     */
    public function create_subscription($data) {
        return $this->request('POST', '/ulnec_subscriptions', $data);
    }
    
    /**
     * Update subscription
     */
    public function update_subscription($id, $data) {
        return $this->request('PATCH', '/ulnec_subscriptions?id=eq.' . $id, $data);
    }
    
    /**
     * Get user subscription
     */
    public function get_user_subscription($user_id) {
        $result = $this->request('GET', '/ulnec_subscriptions?user_id=eq.' . $user_id . '&status=eq.active');
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Record transaction
     */
    public function record_transaction($data) {
        return $this->request('POST', '/ulnec_transactions', $data);
    }
    
    /**
     * Record analytics event
     */
    public function record_analytics($data) {
        return $this->request('POST', '/ulnec_analytics', $data);
    }
    
    /**
     * Get download URL from Supabase Storage
     */
    public function get_download_url($file_path, $expires_in = 300) {
        if (empty($this->url) || empty($this->service_key)) {
            return new WP_Error('no_credentials', 'Supabase credentials not configured');
        }
        
        $url = trailingslashit($this->url) . 'storage/v1/object/sign/ulnec-downloads/' . $file_path;
        
        $response = wp_remote_post($url, [
            'headers' => [
                'apikey' => $this->service_key,
                'Authorization' => 'Bearer ' . $this->service_key,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode(['expiresIn' => $expires_in])
        ]);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['signedURL'])) {
            return $this->url . '/storage/v1' . $body['signedURL'];
        }
        
        return new WP_Error('no_url', 'Failed to generate download URL');
    }
}
