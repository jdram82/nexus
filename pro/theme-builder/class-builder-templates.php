<?php
/**
 * Builder Templates - Template library and management
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Builder Templates Class
 */
class Nexus_Builder_Templates {

    /**
     * Instance
     *
     * @var Nexus_Builder_Templates
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Nexus_Builder_Templates
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
        add_action( 'wp_ajax_nexus_get_templates', array( $this, 'ajax_get_templates' ) );
        add_action( 'wp_ajax_nexus_import_template', array( $this, 'ajax_import_template' ) );
        add_action( 'wp_ajax_nexus_save_template', array( $this, 'ajax_save_template' ) );
        add_action( 'wp_ajax_nexus_delete_template', array( $this, 'ajax_delete_template' ) );
    }

    /**
     * Get all templates
     *
     * @param array $args Query arguments.
     * @return array
     */
    public function get_templates( $args = array() ) {
        $defaults = array(
            'category' => '',
            'search'   => '',
            'per_page' => 20,
            'page'     => 1,
        );

        $args = wp_parse_args( $args, $defaults );

        // Get built-in templates
        $builtin_templates = $this->get_builtin_templates();

        // Get user templates from database
        $user_templates = $this->get_user_templates();

        // Merge templates
        $templates = array_merge( $builtin_templates, $user_templates );

        // Filter by category
        if ( ! empty( $args['category'] ) ) {
            $templates = array_filter( $templates, function( $template ) use ( $args ) {
                return $template['category'] === $args['category'];
            } );
        }

        // Filter by search
        if ( ! empty( $args['search'] ) ) {
            $search = strtolower( $args['search'] );
            $templates = array_filter( $templates, function( $template ) use ( $search ) {
                return strpos( strtolower( $template['title'] ), $search ) !== false;
            } );
        }

        return array_values( $templates );
    }

    /**
     * Get built-in templates
     *
     * @return array
     */
    private function get_builtin_templates() {
        $templates = array();

        // Homepage templates
        $templates[] = array(
            'id'       => 'tech-homepage',
            'title'    => __( 'Technical Homepage', 'nexus-pro' ),
            'category' => 'homepage',
            'preview'  => NEXUS_PRO_URL . 'templates/previews/tech-homepage.jpg',
            'file'     => NEXUS_PRO_PATH . 'templates/data/tech-homepage.json',
            'pro'      => false,
        );

        $templates[] = array(
            'id'       => 'product-homepage',
            'title'    => __( 'Product Showcase Homepage', 'nexus-pro' ),
            'category' => 'homepage',
            'preview'  => NEXUS_PRO_URL . 'templates/previews/product-homepage.jpg',
            'file'     => NEXUS_PRO_PATH . 'templates/data/product-homepage.json',
            'pro'      => false,
        );

        $templates[] = array(
            'id'       => 'corporate-homepage',
            'title'    => __( 'Corporate Homepage', 'nexus-pro' ),
            'category' => 'homepage',
            'preview'  => NEXUS_PRO_URL . 'templates/previews/corporate-homepage.jpg',
            'file'     => NEXUS_PRO_PATH . 'templates/data/corporate-homepage.json',
            'pro'      => false,
        );

        // Product pages
        $templates[] = array(
            'id'       => 'product-detail',
            'title'    => __( 'Product Detail Page', 'nexus-pro' ),
            'category' => 'product',
            'preview'  => NEXUS_PRO_URL . 'templates/previews/product-detail.jpg',
            'file'     => NEXUS_PRO_PATH . 'templates/data/product-detail.json',
            'pro'      => false,
        );

        $templates[] = array(
            'id'       => 'product-catalog',
            'title'    => __( 'Product Catalog', 'nexus-pro' ),
            'category' => 'product',
            'preview'  => NEXUS_PRO_URL . 'templates/previews/product-catalog.jpg',
            'file'     => NEXUS_PRO_PATH . 'templates/data/product-catalog.json',
            'pro'      => false,
        );

        // Service pages
        $templates[] = array(
            'id'       => 'services-grid',
            'title'    => __( 'Services Grid', 'nexus-pro' ),
            'category' => 'services',
            'preview'  => NEXUS_PRO_URL . 'templates/previews/services-grid.jpg',
            'file'     => NEXUS_PRO_PATH . 'templates/data/services-grid.json',
            'pro'      => false,
        );

        // About pages
        $templates[] = array(
            'id'       => 'about-company',
            'title'    => __( 'About Company', 'nexus-pro' ),
            'category' => 'about',
            'preview'  => NEXUS_PRO_URL . 'templates/previews/about-company.jpg',
            'file'     => NEXUS_PRO_PATH . 'templates/data/about-company.json',
            'pro'      => false,
        );

        $templates[] = array(
            'id'       => 'team-page',
            'title'    => __( 'Team Page', 'nexus-pro' ),
            'category' => 'about',
            'preview'  => NEXUS_PRO_URL . 'templates/previews/team-page.jpg',
            'file'     => NEXUS_PRO_PATH . 'templates/data/team-page.json',
            'pro'      => false,
        );

        // Contact pages
        $templates[] = array(
            'id'       => 'contact-simple',
            'title'    => __( 'Contact Simple', 'nexus-pro' ),
            'category' => 'contact',
            'preview'  => NEXUS_PRO_URL . 'templates/previews/contact-simple.jpg',
            'file'     => NEXUS_PRO_PATH . 'templates/data/contact-simple.json',
            'pro'      => false,
        );

        $templates[] = array(
            'id'       => 'contact-with-map',
            'title'    => __( 'Contact with Map', 'nexus-pro' ),
            'category' => 'contact',
            'preview'  => NEXUS_PRO_URL . 'templates/previews/contact-map.jpg',
            'file'     => NEXUS_PRO_PATH . 'templates/data/contact-map.json',
            'pro'      => false,
        );

        // Landing pages
        $templates[] = array(
            'id'       => 'webinar-landing',
            'title'    => __( 'Webinar Landing Page', 'nexus-pro' ),
            'category' => 'landing',
            'preview'  => NEXUS_PRO_URL . 'templates/previews/webinar-landing.jpg',
            'file'     => NEXUS_PRO_PATH . 'templates/data/webinar-landing.json',
            'pro'      => true,
        );

        $templates[] = array(
            'id'       => 'download-landing',
            'title'    => __( 'Download Landing Page', 'nexus-pro' ),
            'category' => 'landing',
            'preview'  => NEXUS_PRO_URL . 'templates/previews/download-landing.jpg',
            'file'     => NEXUS_PRO_PATH . 'templates/data/download-landing.json',
            'pro'      => true,
        );

        return apply_filters( 'nexus_builtin_templates', $templates );
    }

    /**
     * Get user templates
     *
     * @return array
     */
    private function get_user_templates() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'nexus_templates';
        
        // Check if table exists
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) {
            return array();
        }

        $results = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY created_at DESC",
            ARRAY_A
        );

        $templates = array();
        
        foreach ( $results as $row ) {
            $templates[] = array(
                'id'       => 'user-' . $row['id'],
                'title'    => $row['template_name'],
                'category' => $row['template_type'],
                'preview'  => '',
                'data'     => json_decode( $row['template_data'], true ),
                'user'     => true,
                'created'  => $row['created_at'],
            );
        }

        return $templates;
    }

    /**
     * Import template
     *
     * @param string $template_id Template ID.
     * @return array|WP_Error Template data or error.
     */
    public function import_template( $template_id ) {
        $templates = $this->get_templates();
        
        $template = null;
        foreach ( $templates as $t ) {
            if ( $t['id'] === $template_id ) {
                $template = $t;
                break;
            }
        }

        if ( ! $template ) {
            return new WP_Error( 'template_not_found', __( 'Template not found', 'nexus-pro' ) );
        }

        // Get template data
        if ( isset( $template['data'] ) ) {
            return $template['data'];
        }

        if ( isset( $template['file'] ) && file_exists( $template['file'] ) ) {
            $json = file_get_contents( $template['file'] );
            return json_decode( $json, true );
        }

        return new WP_Error( 'template_data_missing', __( 'Template data is missing', 'nexus-pro' ) );
    }

    /**
     * Save template
     *
     * @param string $name Template name.
     * @param array  $data Template data.
     * @param string $type Template type.
     * @return int|WP_Error Template ID or error.
     */
    public function save_template( $name, $data, $type = 'page' ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'nexus_templates';

        $result = $wpdb->insert(
            $table_name,
            array(
                'template_name' => sanitize_text_field( $name ),
                'template_data' => wp_json_encode( $data ),
                'template_type' => sanitize_text_field( $type ),
                'created_at'    => current_time( 'mysql' ),
            ),
            array( '%s', '%s', '%s', '%s' )
        );

        if ( false === $result ) {
            return new WP_Error( 'save_failed', __( 'Failed to save template', 'nexus-pro' ) );
        }

        return $wpdb->insert_id;
    }

    /**
     * Delete template
     *
     * @param int $template_id Template ID.
     * @return bool
     */
    public function delete_template( $template_id ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'nexus_templates';

        $result = $wpdb->delete(
            $table_name,
            array( 'id' => absint( $template_id ) ),
            array( '%d' )
        );

        return false !== $result;
    }

    /**
     * AJAX: Get templates
     */
    public function ajax_get_templates() {
        check_ajax_referer( 'nexus_builder_nonce', 'nonce' );

        $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
        $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';

        $templates = $this->get_templates( array(
            'category' => $category,
            'search'   => $search,
        ) );

        wp_send_json_success( $templates );
    }

    /**
     * AJAX: Import template
     */
    public function ajax_import_template() {
        check_ajax_referer( 'nexus_builder_nonce', 'nonce' );

        $template_id = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : '';

        if ( ! $template_id ) {
            wp_send_json_error( array( 'message' => __( 'Template ID is required', 'nexus-pro' ) ) );
        }

        $data = $this->import_template( $template_id );

        if ( is_wp_error( $data ) ) {
            wp_send_json_error( array( 'message' => $data->get_error_message() ) );
        }

        wp_send_json_success( array( 'data' => $data ) );
    }

    /**
     * AJAX: Save template
     */
    public function ajax_save_template() {
        check_ajax_referer( 'nexus_builder_nonce', 'nonce' );

        $name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
        $data = isset( $_POST['data'] ) ? $_POST['data'] : '';
        $type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'page';

        if ( ! $name || ! $data ) {
            wp_send_json_error( array( 'message' => __( 'Name and data are required', 'nexus-pro' ) ) );
        }

        $template_id = $this->save_template( $name, $data, $type );

        if ( is_wp_error( $template_id ) ) {
            wp_send_json_error( array( 'message' => $template_id->get_error_message() ) );
        }

        wp_send_json_success( array(
            'message'     => __( 'Template saved successfully', 'nexus-pro' ),
            'template_id' => $template_id,
        ) );
    }

    /**
     * AJAX: Delete template
     */
    public function ajax_delete_template() {
        check_ajax_referer( 'nexus_builder_nonce', 'nonce' );

        $template_id = isset( $_POST['template_id'] ) ? absint( $_POST['template_id'] ) : 0;

        if ( ! $template_id ) {
            wp_send_json_error( array( 'message' => __( 'Template ID is required', 'nexus-pro' ) ) );
        }

        $deleted = $this->delete_template( $template_id );

        if ( ! $deleted ) {
            wp_send_json_error( array( 'message' => __( 'Failed to delete template', 'nexus-pro' ) ) );
        }

        wp_send_json_success( array( 'message' => __( 'Template deleted successfully', 'nexus-pro' ) ) );
    }
}

// Initialize
Nexus_Builder_Templates::get_instance();
