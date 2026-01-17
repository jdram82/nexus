<?php
/**
 * AJAX Handlers Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class ULNEC_Ajax {
    
    private $supabase;
    
    public function __construct($supabase) {
        $this->supabase = $supabase;
        
        add_action('wp_ajax_ulnec_test_connection', [$this, 'test_connection']);
    }
    
    /**
     * Test Supabase connection
     */
    public function test_connection() {
        check_ajax_referer('ulnec_admin_nonce', 'nonce');
        
        $connected = $this->supabase->test_connection();
        
        wp_send_json_success([
            'connected' => $connected,
            'message' => $connected ? 'Connected!' : 'Connection failed'
        ]);
    }
}
