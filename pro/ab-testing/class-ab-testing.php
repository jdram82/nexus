<?php
/**
 * A/B Testing System - Main Class
 *
 * @package Nexus_Pro
 * @subpackage AB_Testing
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * A/B Testing Main Class
 */
class Nexus_AB_Testing {

    /**
     * Instance
     *
     * @var Nexus_AB_Testing
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Nexus_AB_Testing
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
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
        add_action( 'wp_ajax_nexus_create_ab_test', array( $this, 'ajax_create_test' ) );
        add_action( 'wp_ajax_nexus_end_ab_test', array( $this, 'ajax_end_test' ) );
        add_action( 'wp_ajax_nexus_ab_track_conversion', array( $this, 'ajax_track_conversion' ) );
        add_action( 'wp_footer', array( $this, 'render_active_tests' ) );
    }

    /**
     * Register post type for A/B tests
     */
    public function register_post_type() {
        register_post_type( 'nexus_ab_test', array(
            'labels' => array(
                'name'               => __( 'A/B Tests', 'nexus-pro' ),
                'singular_name'      => __( 'A/B Test', 'nexus-pro' ),
                'add_new'            => __( 'Add New Test', 'nexus-pro' ),
                'add_new_item'       => __( 'Add New A/B Test', 'nexus-pro' ),
                'edit_item'          => __( 'Edit A/B Test', 'nexus-pro' ),
                'new_item'           => __( 'New A/B Test', 'nexus-pro' ),
                'view_item'          => __( 'View A/B Test', 'nexus-pro' ),
                'search_items'       => __( 'Search A/B Tests', 'nexus-pro' ),
                'not_found'          => __( 'No A/B tests found', 'nexus-pro' ),
                'not_found_in_trash' => __( 'No A/B tests found in trash', 'nexus-pro' ),
            ),
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => false,
            'supports'     => array( 'title' ),
            'capabilities' => array(
                'edit_post'          => 'manage_options',
                'read_post'          => 'manage_options',
                'delete_post'        => 'manage_options',
                'edit_posts'         => 'manage_options',
                'edit_others_posts'  => 'manage_options',
                'delete_posts'       => 'manage_options',
                'publish_posts'      => 'manage_options',
                'read_private_posts' => 'manage_options',
            ),
        ) );
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'nexus-pro',
            __( 'A/B Testing', 'nexus-pro' ),
            __( 'A/B Testing', 'nexus-pro' ),
            'manage_options',
            'nexus-ab-testing',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_admin_assets( $hook ) {
        if ( $hook !== 'nexus-pro_page_nexus-ab-testing' ) {
            return;
        }

        wp_enqueue_style(
            'nexus-ab-testing-admin',
            NEXUS_PRO_URL . 'assets/css/ab-testing.css',
            array(),
            '3.0.0'
        );

        wp_enqueue_script(
            'chart-js',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
            array(),
            '4.4.0',
            true
        );

        wp_enqueue_script(
            'nexus-ab-testing-admin',
            NEXUS_PRO_URL . 'assets/js/ab-testing.js',
            array( 'jquery', 'chart-js' ),
            '3.0.0',
            true
        );

        wp_localize_script( 'nexus-ab-testing-admin', 'nexusABTesting', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'nexus_ab_testing' ),
            'i18n'    => array(
                'confirmEnd'      => __( 'Are you sure you want to end this test?', 'nexus-pro' ),
                'testCreated'     => __( 'Test created successfully!', 'nexus-pro' ),
                'testEnded'       => __( 'Test ended successfully!', 'nexus-pro' ),
                'error'           => __( 'An error occurred. Please try again.', 'nexus-pro' ),
                'noSignificance'  => __( 'No significant difference yet', 'nexus-pro' ),
                'variantAWinning' => __( 'Variant A is winning!', 'nexus-pro' ),
                'variantBWinning' => __( 'Variant B is winning!', 'nexus-pro' ),
            ),
        ) );
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        if ( ! $this->has_active_tests() ) {
            return;
        }

        wp_enqueue_script(
            'nexus-ab-testing-frontend',
            NEXUS_PRO_URL . 'assets/js/ab-testing-frontend.js',
            array( 'jquery' ),
            '3.0.0',
            true
        );

        wp_localize_script( 'nexus-ab-testing-frontend', 'nexusABTests', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'nexus_ab_conversion' ),
            'tests'   => $this->get_active_tests_data(),
        ) );
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        require_once NEXUS_PRO_PATH . 'ab-testing/views/admin-page.php';
    }

    /**
     * Create new A/B test (AJAX)
     */
    public function ajax_create_test() {
        check_ajax_referer( 'nexus_ab_testing', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied', 'nexus-pro' ) ) );
        }

        $test_name   = sanitize_text_field( $_POST['test_name'] ?? '' );
        $test_type   = sanitize_text_field( $_POST['test_type'] ?? 'content' );
        $variant_a   = wp_kses_post( $_POST['variant_a'] ?? '' );
        $variant_b   = wp_kses_post( $_POST['variant_b'] ?? '' );
        $target_page = absint( $_POST['target_page'] ?? 0 );
        $goal_type   = sanitize_text_field( $_POST['goal_type'] ?? 'clicks' );

        if ( empty( $test_name ) || empty( $variant_a ) || empty( $variant_b ) ) {
            wp_send_json_error( array( 'message' => __( 'Please fill all required fields', 'nexus-pro' ) ) );
        }

        // Create test post
        $test_id = wp_insert_post( array(
            'post_title'  => $test_name,
            'post_type'   => 'nexus_ab_test',
            'post_status' => 'publish',
        ) );

        if ( is_wp_error( $test_id ) ) {
            wp_send_json_error( array( 'message' => $test_id->get_error_message() ) );
        }

        // Save test meta
        update_post_meta( $test_id, '_nexus_test_type', $test_type );
        update_post_meta( $test_id, '_nexus_variant_a', $variant_a );
        update_post_meta( $test_id, '_nexus_variant_b', $variant_b );
        update_post_meta( $test_id, '_nexus_target_page', $target_page );
        update_post_meta( $test_id, '_nexus_goal_type', $goal_type );
        update_post_meta( $test_id, '_nexus_test_status', 'active' );
        update_post_meta( $test_id, '_nexus_started_at', current_time( 'mysql' ) );

        // Initialize results
        global $wpdb;
        $table = $wpdb->prefix . 'nexus_ab_results';

        $wpdb->insert( $table, array(
            'test_id'     => $test_id,
            'variant'     => 'A',
            'views'       => 0,
            'conversions' => 0,
            'recorded_at' => current_time( 'mysql' ),
        ) );

        $wpdb->insert( $table, array(
            'test_id'     => $test_id,
            'variant'     => 'B',
            'views'       => 0,
            'conversions' => 0,
            'recorded_at' => current_time( 'mysql' ),
        ) );

        wp_send_json_success( array(
            'message' => __( 'Test created successfully!', 'nexus-pro' ),
            'test_id' => $test_id,
        ) );
    }

    /**
     * End A/B test (AJAX)
     */
    public function ajax_end_test() {
        check_ajax_referer( 'nexus_ab_testing', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied', 'nexus-pro' ) ) );
        }

        $test_id = absint( $_POST['test_id'] ?? 0 );

        if ( ! $test_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid test ID', 'nexus-pro' ) ) );
        }

        update_post_meta( $test_id, '_nexus_test_status', 'ended' );
        update_post_meta( $test_id, '_nexus_ended_at', current_time( 'mysql' ) );

        wp_send_json_success( array(
            'message' => __( 'Test ended successfully!', 'nexus-pro' ),
        ) );
    }

    /**
     * Track conversion (AJAX)
     */
    public function ajax_track_conversion() {
        check_ajax_referer( 'nexus_ab_conversion', 'nonce' );

        $test_id = absint( $_POST['test_id'] ?? 0 );
        $variant = sanitize_text_field( $_POST['variant'] ?? '' );

        if ( ! $test_id || ! in_array( $variant, array( 'A', 'B' ), true ) ) {
            wp_send_json_error();
        }

        global $wpdb;
        $table = $wpdb->prefix . 'nexus_ab_results';

        $wpdb->query( $wpdb->prepare(
            "UPDATE {$table} SET conversions = conversions + 1 WHERE test_id = %d AND variant = %s",
            $test_id,
            $variant
        ) );

        wp_send_json_success();
    }

    /**
     * Render active tests on frontend
     */
    public function render_active_tests() {
        if ( is_admin() || ! $this->has_active_tests() ) {
            return;
        }

        $active_tests = $this->get_active_tests();

        foreach ( $active_tests as $test ) {
            $this->render_test_variant( $test );
        }
    }

    /**
     * Render test variant
     *
     * @param WP_Post $test Test post object.
     */
    private function render_test_variant( $test ) {
        // Assign variant (50/50 split)
        $variant = isset( $_COOKIE['nexus_ab_' . $test->ID] ) 
            ? $_COOKIE['nexus_ab_' . $test->ID] 
            : ( rand( 0, 1 ) ? 'A' : 'B' );

        // Set cookie for consistency
        if ( ! isset( $_COOKIE['nexus_ab_' . $test->ID] ) ) {
            setcookie( 'nexus_ab_' . $test->ID, $variant, time() + ( 30 * DAY_IN_SECONDS ), '/' );
        }

        // Track view
        $this->track_view( $test->ID, $variant );

        // Get variant content
        $content = get_post_meta( $test->ID, '_nexus_variant_' . strtolower( $variant ), true );
        $test_type = get_post_meta( $test->ID, '_nexus_test_type', true );

        // Output variant
        echo '<div class="nexus-ab-test" data-test-id="' . esc_attr( $test->ID ) . '" data-variant="' . esc_attr( $variant ) . '">';
        echo wp_kses_post( $content );
        echo '</div>';
    }

    /**
     * Track view
     *
     * @param int    $test_id Test ID.
     * @param string $variant Variant (A or B).
     */
    private function track_view( $test_id, $variant ) {
        global $wpdb;
        $table = $wpdb->prefix . 'nexus_ab_results';

        $wpdb->query( $wpdb->prepare(
            "UPDATE {$table} SET views = views + 1 WHERE test_id = %d AND variant = %s",
            $test_id,
            $variant
        ) );
    }

    /**
     * Check if there are active tests
     *
     * @return bool
     */
    private function has_active_tests() {
        $args = array(
            'post_type'      => 'nexus_ab_test',
            'posts_per_page' => 1,
            'meta_query'     => array(
                array(
                    'key'   => '_nexus_test_status',
                    'value' => 'active',
                ),
            ),
        );

        $query = new WP_Query( $args );
        return $query->have_posts();
    }

    /**
     * Get active tests
     *
     * @return array
     */
    private function get_active_tests() {
        $args = array(
            'post_type'      => 'nexus_ab_test',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'   => '_nexus_test_status',
                    'value' => 'active',
                ),
            ),
        );

        $query = new WP_Query( $args );
        return $query->posts;
    }

    /**
     * Get active tests data
     *
     * @return array
     */
    private function get_active_tests_data() {
        $tests = $this->get_active_tests();
        $data  = array();

        foreach ( $tests as $test ) {
            $data[] = array(
                'id'        => $test->ID,
                'name'      => $test->post_title,
                'goal_type' => get_post_meta( $test->ID, '_nexus_goal_type', true ),
            );
        }

        return $data;
    }

    /**
     * Get test results
     *
     * @param int $test_id Test ID.
     * @return array
     */
    public static function get_test_results( $test_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'nexus_ab_results';

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE test_id = %d ORDER BY variant ASC",
            $test_id
        ), ARRAY_A );

        return $results;
    }

    /**
     * Calculate statistical significance
     *
     * @param array $variant_a Variant A results.
     * @param array $variant_b Variant B results.
     * @return array
     */
    public static function calculate_significance( $variant_a, $variant_b ) {
        $views_a = (int) $variant_a['views'];
        $conv_a  = (int) $variant_a['conversions'];
        $views_b = (int) $variant_b['views'];
        $conv_b  = (int) $variant_b['conversions'];

        if ( $views_a === 0 || $views_b === 0 ) {
            return array(
                'significant' => false,
                'confidence'  => 0,
                'winner'      => null,
            );
        }

        $rate_a = $conv_a / $views_a;
        $rate_b = $conv_b / $views_b;

        // Z-score calculation (simplified)
        $pooled_rate = ( $conv_a + $conv_b ) / ( $views_a + $views_b );
        $se = sqrt( $pooled_rate * ( 1 - $pooled_rate ) * ( 1 / $views_a + 1 / $views_b ) );
        
        if ( $se === 0.0 ) {
            return array(
                'significant' => false,
                'confidence'  => 0,
                'winner'      => null,
            );
        }

        $z_score = abs( $rate_a - $rate_b ) / $se;
        
        // Confidence level (rough approximation)
        $confidence = min( 99, round( ( 1 - exp( -$z_score ) ) * 100 ) );

        return array(
            'significant' => $confidence >= 95,
            'confidence'  => $confidence,
            'winner'      => $rate_a > $rate_b ? 'A' : 'B',
            'rate_a'      => round( $rate_a * 100, 2 ),
            'rate_b'      => round( $rate_b * 100, 2 ),
        );
    }
}

// Initialize
Nexus_AB_Testing::get_instance();
