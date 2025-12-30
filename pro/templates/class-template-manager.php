<?php
/**
 * Template Manager - Enhanced template management
 *
 * @package Nexus_Pro
 * @subpackage Templates
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Template Manager Class
 *
 * Manages templates with categories, filters, search, and organization
 */
class Nexus_Template_Manager {

    /**
     * Instance
     *
     * @var Nexus_Template_Manager
     */
    private static $instance = null;

    /**
     * Template categories
     *
     * @var array
     */
    private $categories = array();

    /**
     * Template data directory
     *
     * @var string
     */
    private $data_dir;

    /**
     * Get instance
     *
     * @return Nexus_Template_Manager
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
        $this->data_dir = NEXUS_PRO_PATH . 'templates/data/';
        $this->init_categories();

        add_action( 'wp_ajax_nexus_get_templates', array( $this, 'ajax_get_templates' ) );
        add_action( 'wp_ajax_nexus_get_template_preview', array( $this, 'ajax_get_template_preview' ) );
        add_action( 'wp_ajax_nexus_search_templates', array( $this, 'ajax_search_templates' ) );
        add_action( 'wp_ajax_nexus_get_template_categories', array( $this, 'ajax_get_categories' ) );
    }

    /**
     * Initialize template categories
     */
    private function init_categories() {
        $this->categories = array(
            'business' => array(
                'name'        => __( 'Business & Corporate', 'nexus-pro' ),
                'description' => __( 'Professional templates for businesses and corporations', 'nexus-pro' ),
                'icon'        => 'dashicons-building',
            ),
            'saas' => array(
                'name'        => __( 'SaaS & Technology', 'nexus-pro' ),
                'description' => __( 'Modern templates for software and tech companies', 'nexus-pro' ),
                'icon'        => 'dashicons-laptop',
            ),
            'ecommerce' => array(
                'name'        => __( 'E-commerce & Shop', 'nexus-pro' ),
                'description' => __( 'Online store and product showcase templates', 'nexus-pro' ),
                'icon'        => 'dashicons-cart',
            ),
            'portfolio' => array(
                'name'        => __( 'Portfolio & Creative', 'nexus-pro' ),
                'description' => __( 'Showcase your work and creative projects', 'nexus-pro' ),
                'icon'        => 'dashicons-portfolio',
            ),
            'blog' => array(
                'name'        => __( 'Blog & Magazine', 'nexus-pro' ),
                'description' => __( 'Content-focused blog and magazine layouts', 'nexus-pro' ),
                'icon'        => 'dashicons-admin-post',
            ),
            'docs' => array(
                'name'        => __( 'Documentation', 'nexus-pro' ),
                'description' => __( 'Technical documentation and knowledge bases', 'nexus-pro' ),
                'icon'        => 'dashicons-book',
            ),
            'landing' => array(
                'name'        => __( 'Landing Pages', 'nexus-pro' ),
                'description' => __( 'High-converting landing pages', 'nexus-pro' ),
                'icon'        => 'dashicons-welcome-view-site',
            ),
            'marketing' => array(
                'name'        => __( 'Marketing & Agency', 'nexus-pro' ),
                'description' => __( 'Marketing agency and service templates', 'nexus-pro' ),
                'icon'        => 'dashicons-megaphone',
            ),
            'education' => array(
                'name'        => __( 'Education & Learning', 'nexus-pro' ),
                'description' => __( 'Online courses and educational platforms', 'nexus-pro' ),
                'icon'        => 'dashicons-welcome-learn-more',
            ),
            'events' => array(
                'name'        => __( 'Events & Conference', 'nexus-pro' ),
                'description' => __( 'Event websites and conference pages', 'nexus-pro' ),
                'icon'        => 'dashicons-tickets',
            ),
        );

        $this->categories = apply_filters( 'nexus_template_categories', $this->categories );
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
            'type'     => '',
            'search'   => '',
            'limit'    => -1,
            'orderby'  => 'name',
            'order'    => 'ASC',
        );

        $args = wp_parse_args( $args, $defaults );

        $templates = $this->load_all_templates();

        // Filter by category
        if ( ! empty( $args['category'] ) ) {
            $templates = array_filter( $templates, function( $template ) use ( $args ) {
                return isset( $template['category'] ) && $template['category'] === $args['category'];
            } );
        }

        // Filter by type
        if ( ! empty( $args['type'] ) ) {
            $templates = array_filter( $templates, function( $template ) use ( $args ) {
                return isset( $template['type'] ) && $template['type'] === $args['type'];
            } );
        }

        // Search
        if ( ! empty( $args['search'] ) ) {
            $search = strtolower( $args['search'] );
            $templates = array_filter( $templates, function( $template ) use ( $search ) {
                $searchable = strtolower( $template['name'] . ' ' . $template['description'] );
                return strpos( $searchable, $search ) !== false;
            } );
        }

        // Sort
        usort( $templates, function( $a, $b ) use ( $args ) {
            if ( $args['orderby'] === 'name' ) {
                return strcmp( $a['name'], $b['name'] ) * ( $args['order'] === 'ASC' ? 1 : -1 );
            }
            return 0;
        } );

        // Limit
        if ( $args['limit'] > 0 ) {
            $templates = array_slice( $templates, 0, $args['limit'] );
        }

        return $templates;
    }

    /**
     * Load all template files
     *
     * @return array
     */
    private function load_all_templates() {
        $templates = array();

        if ( ! is_dir( $this->data_dir ) ) {
            return $templates;
        }

        $files = glob( $this->data_dir . '*.json' );

        foreach ( $files as $file ) {
            $template_data = $this->load_template_file( $file );
            
            if ( $template_data ) {
                $template_id = basename( $file, '.json' );
                $template_data['id'] = $template_id;
                $template_data['file'] = $file;
                $templates[] = $template_data;
            }
        }

        return $templates;
    }

    /**
     * Load single template file
     *
     * @param string $file Template file path.
     * @return array|false
     */
    private function load_template_file( $file ) {
        if ( ! file_exists( $file ) ) {
            return false;
        }

        $content = file_get_contents( $file );
        $data = json_decode( $content, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return false;
        }

        // Ensure required fields
        $defaults = array(
            'name'        => basename( $file, '.json' ),
            'description' => '',
            'category'    => 'business',
            'type'        => 'page',
            'tags'        => array(),
            'preview_url' => '',
            'thumbnail'   => '',
            'sections'    => array(),
        );

        return wp_parse_args( $data, $defaults );
    }

    /**
     * Get template by ID
     *
     * @param string $template_id Template ID.
     * @return array|false
     */
    public function get_template( $template_id ) {
        $file = $this->data_dir . $template_id . '.json';
        return $this->load_template_file( $file );
    }

    /**
     * Get template categories
     *
     * @return array
     */
    public function get_categories() {
        return $this->categories;
    }

    /**
     * Get templates by category
     *
     * @param string $category Category slug.
     * @return array
     */
    public function get_templates_by_category( $category ) {
        return $this->get_templates( array( 'category' => $category ) );
    }

    /**
     * Search templates
     *
     * @param string $query Search query.
     * @return array
     */
    public function search_templates( $query ) {
        return $this->get_templates( array( 'search' => $query ) );
    }

    /**
     * Get template stats
     *
     * @return array
     */
    public function get_stats() {
        $templates = $this->load_all_templates();

        $stats = array(
            'total'      => count( $templates ),
            'categories' => array(),
            'types'      => array(),
        );

        foreach ( $templates as $template ) {
            // Count by category
            $category = $template['category'];
            if ( ! isset( $stats['categories'][ $category ] ) ) {
                $stats['categories'][ $category ] = 0;
            }
            $stats['categories'][ $category ]++;

            // Count by type
            $type = $template['type'];
            if ( ! isset( $stats['types'][ $type ] ) ) {
                $stats['types'][ $type ] = 0;
            }
            $stats['types'][ $type ]++;
        }

        return $stats;
    }

    /**
     * AJAX: Get templates
     */
    public function ajax_get_templates() {
        check_ajax_referer( 'nexus_templates', 'nonce' );

        $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
        $type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';

        $templates = $this->get_templates( array(
            'category' => $category,
            'type'     => $type,
        ) );

        wp_send_json_success( array(
            'templates' => $templates,
            'total'     => count( $templates ),
        ) );
    }

    /**
     * AJAX: Get template preview
     */
    public function ajax_get_template_preview() {
        check_ajax_referer( 'nexus_templates', 'nonce' );

        $template_id = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : '';

        if ( empty( $template_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Template ID required.', 'nexus-pro' ) ) );
        }

        $template = $this->get_template( $template_id );

        if ( ! $template ) {
            wp_send_json_error( array( 'message' => __( 'Template not found.', 'nexus-pro' ) ) );
        }

        wp_send_json_success( array( 'template' => $template ) );
    }

    /**
     * AJAX: Search templates
     */
    public function ajax_search_templates() {
        check_ajax_referer( 'nexus_templates', 'nonce' );

        $query = isset( $_POST['query'] ) ? sanitize_text_field( $_POST['query'] ) : '';

        if ( empty( $query ) ) {
            wp_send_json_error( array( 'message' => __( 'Search query required.', 'nexus-pro' ) ) );
        }

        $templates = $this->search_templates( $query );

        wp_send_json_success( array(
            'templates' => $templates,
            'total'     => count( $templates ),
        ) );
    }

    /**
     * AJAX: Get categories
     */
    public function ajax_get_categories() {
        check_ajax_referer( 'nexus_templates', 'nonce' );

        $stats = $this->get_stats();

        $categories = array();
        foreach ( $this->categories as $slug => $data ) {
            $categories[] = array(
                'slug'        => $slug,
                'name'        => $data['name'],
                'description' => $data['description'],
                'icon'        => $data['icon'],
                'count'       => isset( $stats['categories'][ $slug ] ) ? $stats['categories'][ $slug ] : 0,
            );
        }

        wp_send_json_success( array(
            'categories' => $categories,
            'stats'      => $stats,
        ) );
    }
}

// Initialize
Nexus_Template_Manager::get_instance();
