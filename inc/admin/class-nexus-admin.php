<?php
/**
 * Nexus Admin Dashboard
 * 
 * Main admin interface for Nexus theme
 * 
 * @package Nexus
 * @since 3.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Nexus_Admin {
    
    /**
     * Instance
     */
    private static $instance = null;
    
    /**
     * Menu slug
     */
    const MENU_SLUG = 'nexus-dashboard';
    
    /**
     * Get instance
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_init', array($this, 'handle_actions'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('Nexus Dashboard', 'nexus'),
            __('Nexus', 'nexus'),
            'manage_options',
            self::MENU_SLUG,
            array($this, 'render_dashboard'),
            'dashicons-layout',
            59
        );
        
        // Dashboard submenu
        add_submenu_page(
            self::MENU_SLUG,
            __('Dashboard', 'nexus'),
            __('Dashboard', 'nexus'),
            'manage_options',
            self::MENU_SLUG,
            array($this, 'render_dashboard')
        );
        
        // Templates
        add_submenu_page(
            self::MENU_SLUG,
            __('Templates', 'nexus'),
            __('Templates', 'nexus'),
            'manage_options',
            'nexus-templates',
            array($this, 'render_templates')
        );
        
        // Settings
        add_submenu_page(
            self::MENU_SLUG,
            __('Settings', 'nexus'),
            __('Settings', 'nexus'),
            'manage_options',
            'nexus-settings',
            array($this, 'render_settings')
        );
        
        // License
        add_submenu_page(
            self::MENU_SLUG,
            __('License', 'nexus'),
            __('License', 'nexus'),
            'manage_options',
            'nexus-license',
            array($this, 'render_license')
        );
        
        // Getting Started
        add_submenu_page(
            self::MENU_SLUG,
            __('Getting Started', 'nexus'),
            __('Getting Started', 'nexus'),
            'manage_options',
            'nexus-getting-started',
            array($this, 'render_getting_started')
        );
        
        // System Info
        add_submenu_page(
            self::MENU_SLUG,
            __('System Info', 'nexus'),
            __('System Info', 'nexus'),
            'manage_options',
            'nexus-system-info',
            array($this, 'render_system_info')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'nexus') === false) {
            return;
        }
        
        wp_enqueue_style(
            'nexus-admin',
            get_template_directory_uri() . '/assets/admin/css/admin.css',
            array(),
            NEXUS_VERSION
        );
        
        wp_enqueue_script(
            'nexus-admin',
            get_template_directory_uri() . '/assets/admin/js/admin.js',
            array('jquery'),
            NEXUS_VERSION,
            true
        );
        
        wp_localize_script('nexus-admin', 'nexusAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nexus-admin'),
            'license' => $this->get_license_info(),
        ));
    }
    
    /**
     * Handle admin actions
     */
    public function handle_actions() {
        if (!isset($_POST['nexus_action']) || !wp_verify_nonce($_POST['nexus_nonce'], 'nexus-admin')) {
            return;
        }
        
        $action = sanitize_text_field($_POST['nexus_action']);
        
        switch ($action) {
            case 'save_settings':
                $this->save_settings();
                break;
            case 'import_template':
                $this->import_template();
                break;
            case 'reset_settings':
                $this->reset_settings();
                break;
        }
    }
    
    /**
     * Get license info
     */
    private function get_license_info() {
        if (class_exists('Nexus_License_Manager')) {
            $license_manager = Nexus_License_Manager::instance();
            $license_data = $license_manager->get_license_info();
            return array(
                'tier' => $license_data['tier'],
                'status' => $license_data['status'],
                'key' => $license_data['key'],
            );
        }
        
        return array(
            'tier' => 'free',
            'status' => 'inactive',
            'key' => '',
        );
    }
    
    /**
     * Render dashboard
     */
    public function render_dashboard() {
        $license = $this->get_license_info();
        include get_template_directory() . '/inc/admin/views/dashboard.php';
    }
    
    /**
     * Render templates page
     */
    public function render_templates() {
        $license = $this->get_license_info();
        include get_template_directory() . '/inc/admin/views/templates.php';
    }
    
    /**
     * Render settings page
     */
    public function render_settings() {
        $license = $this->get_license_info();
        include get_template_directory() . '/inc/admin/views/settings.php';
    }
    
    /**
     * Render license page
     */
    public function render_license() {
        if (class_exists('Nexus_License_Manager')) {
            $license_manager = Nexus_License_Manager::instance();
            $license_manager->render_license_page();
        }
    }
    
    /**
     * Render getting started
     */
    public function render_getting_started() {
        include get_template_directory() . '/inc/admin/views/getting-started.php';
    }
    
    /**
     * Render system info
     */
    public function render_system_info() {
        include get_template_directory() . '/inc/admin/views/system-info.php';
    }
    
    /**
     * Save settings
     */
    private function save_settings() {
        // Save theme settings
        $settings = isset($_POST['nexus_settings']) ? $_POST['nexus_settings'] : array();
        update_option('nexus_theme_settings', $settings);
        
        add_settings_error(
            'nexus_messages',
            'nexus_message',
            __('Settings saved successfully!', 'nexus'),
            'success'
        );
    }
    
    /**
     * Import template
     */
    private function import_template() {
        $template_id = isset($_POST['template_id']) ? sanitize_text_field($_POST['template_id']) : '';
        
        if (empty($template_id)) {
            return;
        }
        
        // Import template logic here
        add_settings_error(
            'nexus_messages',
            'nexus_message',
            __('Template imported successfully!', 'nexus'),
            'success'
        );
    }
    
    /**
     * Reset settings
     */
    private function reset_settings() {
        delete_option('nexus_theme_settings');
        
        add_settings_error(
            'nexus_messages',
            'nexus_message',
            __('Settings reset successfully!', 'nexus'),
            'success'
        );
    }
}

// Initialize
Nexus_Admin::instance();
