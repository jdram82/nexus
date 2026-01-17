<?php
/**
 * Frontend Pages Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class ULNEC_Frontend {
    
    private $supabase;
    
    public function __construct($supabase) {
        $this->supabase = $supabase;
        
        add_filter('template_include', [$this, 'load_templates']);
    }
    
    /**
     * Load custom templates
     */
    public function load_templates($template) {
        // Add custom template loading logic here
        return $template;
    }
}
