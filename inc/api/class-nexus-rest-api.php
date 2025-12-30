<?php
/**
 * Nexus REST API (Free Tier)
 * 
 * Basic API endpoints exposing core theme functionality.
 * Enables headless WordPress, third-party integrations, and developer extensions.
 * 
 * @package Nexus_Theme
 * @subpackage API
 * @since 1.4.0
 * @tier Free
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nexus_REST_API {
    
    /**
     * API namespace
     */
    const NAMESPACE = 'nexus/v1';
    
    /**
     * Singleton instance
     */
    private static $instance = null;
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
        add_filter( 'rest_authentication_errors', array( $this, 'custom_authentication' ) );
    }
    
    /**
     * Register all REST API routes
     */
    public function register_routes() {
        
        // Theme Settings Routes
        register_rest_route( self::NAMESPACE, '/settings', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_theme_settings' ),
            'permission_callback' => array( $this, 'check_read_permission' ),
        ) );
        
        register_rest_route( self::NAMESPACE, '/settings', array(
            'methods' => 'POST',
            'callback' => array( $this, 'update_theme_settings' ),
            'permission_callback' => array( $this, 'check_write_permission' ),
            'args' => $this->get_settings_schema(),
        ) );
        
        // Templates Routes
        register_rest_route( self::NAMESPACE, '/templates', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_templates' ),
            'permission_callback' => array( $this, 'check_read_permission' ),
        ) );
        
        register_rest_route( self::NAMESPACE, '/templates/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_template' ),
            'permission_callback' => array( $this, 'check_read_permission' ),
        ) );
        
        register_rest_route( self::NAMESPACE, '/templates', array(
            'methods' => 'POST',
            'callback' => array( $this, 'create_template' ),
            'permission_callback' => array( $this, 'check_write_permission' ),
        ) );
        
        register_rest_route( self::NAMESPACE, '/templates/(?P<id>\d+)', array(
            'methods' => 'PUT',
            'callback' => array( $this, 'update_template' ),
            'permission_callback' => array( $this, 'check_write_permission' ),
        ) );
        
        register_rest_route( self::NAMESPACE, '/templates/(?P<id>\d+)', array(
            'methods' => 'DELETE',
            'callback' => array( $this, 'delete_template' ),
            'permission_callback' => array( $this, 'check_write_permission' ),
        ) );
        
        // Custom Post Types Routes
        register_rest_route( self::NAMESPACE, '/projects', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_projects' ),
            'permission_callback' => '__return_true',
        ) );
        
        register_rest_route( self::NAMESPACE, '/products', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_products' ),
            'permission_callback' => '__return_true',
        ) );
        
        register_rest_route( self::NAMESPACE, '/downloads', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_downloads' ),
            'permission_callback' => '__return_true',
        ) );
        
        // Performance Metrics Route
        register_rest_route( self::NAMESPACE, '/performance', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_performance_metrics' ),
            'permission_callback' => array( $this, 'check_read_permission' ),
        ) );
        
        // Plugin Harmony Status
        register_rest_route( self::NAMESPACE, '/harmony', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_harmony_status' ),
            'permission_callback' => array( $this, 'check_read_permission' ),
        ) );
        
        // API Health Check
        register_rest_route( self::NAMESPACE, '/health', array(
            'methods' => 'GET',
            'callback' => array( $this, 'health_check' ),
            'permission_callback' => '__return_true',
        ) );
    }
    
    /**
     * Health check endpoint
     */
    public function health_check( $request ) {
        return new WP_REST_Response( array(
            'status' => 'ok',
            'version' => NEXUS_VERSION,
            'api_version' => '1.0',
            'timestamp' => current_time( 'mysql' ),
            'theme' => wp_get_theme()->get( 'Name' ),
        ), 200 );
    }
    
    /**
     * Get theme settings
     */
    public function get_theme_settings( $request ) {
        $settings = array(
            'colors' => array(
                'primary' => get_theme_mod( 'nexus_primary_color', '#0066cc' ),
                'secondary' => get_theme_mod( 'nexus_secondary_color', '#333333' ),
                'accent' => get_theme_mod( 'nexus_accent_color', '#ff6b6b' ),
            ),
            'typography' => array(
                'base_font' => get_theme_mod( 'nexus_font_base', 'Inter, sans-serif' ),
                'heading_font' => get_theme_mod( 'nexus_font_heading', 'Space Grotesk, sans-serif' ),
            ),
            'layout' => array(
                'container_width' => get_theme_mod( 'nexus_container_width', '1200px' ),
                'sidebar_position' => get_theme_mod( 'nexus_sidebar_position', 'right' ),
            ),
            'performance' => array(
                'lazy_load' => get_theme_mod( 'nexus_lazy_load', true ),
                'minify_css' => get_theme_mod( 'nexus_minify_css', true ),
                'minify_js' => get_theme_mod( 'nexus_minify_js', true ),
            ),
        );
        
        return new WP_REST_Response( $settings, 200 );
    }
    
    /**
     * Update theme settings
     */
    public function update_theme_settings( $request ) {
        $params = $request->get_json_params();
        
        if ( isset( $params['colors'] ) ) {
            foreach ( $params['colors'] as $key => $value ) {
                set_theme_mod( 'nexus_' . $key . '_color', sanitize_hex_color( $value ) );
            }
        }
        
        if ( isset( $params['typography'] ) ) {
            foreach ( $params['typography'] as $key => $value ) {
                set_theme_mod( 'nexus_font_' . $key, sanitize_text_field( $value ) );
            }
        }
        
        return new WP_REST_Response( array(
            'message' => 'Settings updated successfully',
            'settings' => $this->get_theme_settings( $request )->data,
        ), 200 );
    }
    
    /**
     * Get templates
     */
    public function get_templates( $request ) {
        $templates = get_posts( array(
            'post_type' => 'nexus_template',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ) );
        
        $formatted_templates = array_map( function( $template ) {
            return array(
                'id' => $template->ID,
                'title' => $template->post_title,
                'content' => json_decode( $template->post_content, true ),
                'type' => get_post_meta( $template->ID, '_nexus_template_type', true ),
                'created' => $template->post_date,
                'modified' => $template->post_modified,
            );
        }, $templates );
        
        return new WP_REST_Response( $formatted_templates, 200 );
    }
    
    /**
     * Get single template
     */
    public function get_template( $request ) {
        $id = $request->get_param( 'id' );
        $template = get_post( $id );
        
        if ( ! $template || 'nexus_template' !== $template->post_type ) {
            return new WP_Error( 'not_found', 'Template not found', array( 'status' => 404 ) );
        }
        
        return new WP_REST_Response( array(
            'id' => $template->ID,
            'title' => $template->post_title,
            'content' => json_decode( $template->post_content, true ),
            'type' => get_post_meta( $template->ID, '_nexus_template_type', true ),
            'created' => $template->post_date,
            'modified' => $template->post_modified,
        ), 200 );
    }
    
    /**
     * Create template
     */
    public function create_template( $request ) {
        $params = $request->get_json_params();
        
        $template_id = wp_insert_post( array(
            'post_type' => 'nexus_template',
            'post_title' => sanitize_text_field( $params['title'] ),
            'post_content' => wp_json_encode( $params['content'] ),
            'post_status' => 'publish',
        ) );
        
        if ( is_wp_error( $template_id ) ) {
            return new WP_Error( 'creation_failed', $template_id->get_error_message(), array( 'status' => 500 ) );
        }
        
        if ( isset( $params['type'] ) ) {
            update_post_meta( $template_id, '_nexus_template_type', sanitize_text_field( $params['type'] ) );
        }
        
        return new WP_REST_Response( array(
            'message' => 'Template created successfully',
            'id' => $template_id,
        ), 201 );
    }
    
    /**
     * Update template
     */
    public function update_template( $request ) {
        $id = $request->get_param( 'id' );
        $params = $request->get_json_params();
        
        $result = wp_update_post( array(
            'ID' => $id,
            'post_title' => sanitize_text_field( $params['title'] ),
            'post_content' => wp_json_encode( $params['content'] ),
        ) );
        
        if ( is_wp_error( $result ) ) {
            return new WP_Error( 'update_failed', $result->get_error_message(), array( 'status' => 500 ) );
        }
        
        return new WP_REST_Response( array(
            'message' => 'Template updated successfully',
            'id' => $id,
        ), 200 );
    }
    
    /**
     * Delete template
     */
    public function delete_template( $request ) {
        $id = $request->get_param( 'id' );
        
        $result = wp_delete_post( $id, true );
        
        if ( ! $result ) {
            return new WP_Error( 'deletion_failed', 'Failed to delete template', array( 'status' => 500 ) );
        }
        
        return new WP_REST_Response( array(
            'message' => 'Template deleted successfully',
            'id' => $id,
        ), 200 );
    }
    
    /**
     * Get projects
     */
    public function get_projects( $request ) {
        return $this->get_custom_post_type_data( 'nexus_project', $request );
    }
    
    /**
     * Get products
     */
    public function get_products( $request ) {
        return $this->get_custom_post_type_data( 'nexus_product', $request );
    }
    
    /**
     * Get downloads
     */
    public function get_downloads( $request ) {
        return $this->get_custom_post_type_data( 'nexus_download', $request );
    }
    
    /**
     * Generic custom post type data retrieval
     */
    private function get_custom_post_type_data( $post_type, $request ) {
        $per_page = $request->get_param( 'per_page' ) ?: 10;
        $page = $request->get_param( 'page' ) ?: 1;
        
        $posts = get_posts( array(
            'post_type' => $post_type,
            'posts_per_page' => $per_page,
            'paged' => $page,
            'post_status' => 'publish',
        ) );
        
        $formatted_posts = array_map( function( $post ) {
            return array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'excerpt' => $post->post_excerpt,
                'content' => apply_filters( 'the_content', $post->post_content ),
                'featured_image' => get_the_post_thumbnail_url( $post->ID, 'large' ),
                'link' => get_permalink( $post->ID ),
                'date' => $post->post_date,
                'meta' => get_post_meta( $post->ID ),
            );
        }, $posts );
        
        return new WP_REST_Response( array(
            'data' => $formatted_posts,
            'pagination' => array(
                'total' => wp_count_posts( $post_type )->publish,
                'per_page' => $per_page,
                'current_page' => $page,
            ),
        ), 200 );
    }
    
    /**
     * Get performance metrics
     */
    public function get_performance_metrics( $request ) {
        if ( class_exists( 'Nexus_Performance' ) ) {
            $performance = Nexus_Performance::get_instance();
            return new WP_REST_Response( $performance->get_metrics(), 200 );
        }
        
        return new WP_REST_Response( array(
            'message' => 'Performance metrics not available',
        ), 200 );
    }
    
    /**
     * Get plugin harmony status
     */
    public function get_harmony_status( $request ) {
        if ( class_exists( 'Nexus_Plugin_Harmony' ) ) {
            $harmony = Nexus_Plugin_Harmony::get_instance();
            return new WP_REST_Response( $harmony->get_harmony_status(), 200 );
        }
        
        return new WP_REST_Response( array(
            'harmony_active' => false,
        ), 200 );
    }
    
    /**
     * Check read permission
     */
    public function check_read_permission( $request ) {
        // Allow authenticated users or API key
        if ( is_user_logged_in() ) {
            return true;
        }
        
        // Check for API key
        $api_key = $request->get_header( 'X-Nexus-API-Key' );
        if ( $api_key ) {
            return $this->validate_api_key( $api_key );
        }
        
        return new WP_Error(
            'rest_forbidden',
            __( 'You do not have permission to access this resource.', 'nexus' ),
            array( 'status' => 401 )
        );
    }
    
    /**
     * Check write permission
     */
    public function check_write_permission( $request ) {
        if ( ! current_user_can( 'edit_theme_options' ) ) {
            return new WP_Error(
                'rest_forbidden',
                __( 'You do not have permission to modify this resource.', 'nexus' ),
                array( 'status' => 403 )
            );
        }
        
        return true;
    }
    
    /**
     * Custom authentication for API keys
     */
    public function custom_authentication( $result ) {
        // Don't override existing authentication
        if ( ! empty( $result ) ) {
            return $result;
        }
        
        // Check for API key in header
        $api_key = isset( $_SERVER['HTTP_X_NEXUS_API_KEY'] ) ? $_SERVER['HTTP_X_NEXUS_API_KEY'] : '';
        
        if ( empty( $api_key ) ) {
            return $result;
        }
        
        // Validate API key
        if ( $this->validate_api_key( $api_key ) ) {
            return true;
        }
        
        return new WP_Error(
            'rest_forbidden',
            __( 'Invalid API key.', 'nexus' ),
            array( 'status' => 401 )
        );
    }
    
    /**
     * Validate API key
     */
    private function validate_api_key( $api_key ) {
        $stored_keys = get_option( 'nexus_api_keys', array() );
        
        foreach ( $stored_keys as $key_data ) {
            if ( hash_equals( $key_data['key'], $api_key ) && $key_data['active'] ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get settings schema
     */
    private function get_settings_schema() {
        return array(
            'colors' => array(
                'type' => 'object',
                'properties' => array(
                    'primary' => array( 'type' => 'string' ),
                    'secondary' => array( 'type' => 'string' ),
                    'accent' => array( 'type' => 'string' ),
                ),
            ),
            'typography' => array(
                'type' => 'object',
                'properties' => array(
                    'base_font' => array( 'type' => 'string' ),
                    'heading_font' => array( 'type' => 'string' ),
                ),
            ),
        );
    }
}

// Initialize
Nexus_REST_API::get_instance();
