<?php
/**
 * Performance Monitor - Main Class
 *
 * @package Nexus_Pro
 * @subpackage Performance
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Performance Monitor Class
 */
class Nexus_Performance {

    /**
     * Instance
     */
    private static $instance = null;

    /**
     * Get instance
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
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'shutdown', array( $this, 'track_performance' ) );
        add_action( 'wp_ajax_nexus_run_performance_test', array( $this, 'ajax_run_test' ) );
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'nexus-pro',
            __( 'Performance', 'nexus-pro' ),
            __( 'Performance', 'nexus-pro' ),
            'manage_options',
            'nexus-performance',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets( $hook ) {
        if ( $hook !== 'nexus-pro_page_nexus-performance' ) {
            return;
        }

        wp_enqueue_style( 'nexus-performance', NEXUS_PRO_URL . 'assets/css/performance.css', array(), '3.0.0' );
        wp_enqueue_script( 'nexus-performance', NEXUS_PRO_URL . 'assets/js/performance.js', array( 'jquery' ), '3.0.0', true );

        wp_localize_script( 'nexus-performance', 'nexusPerformance', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'nexus_performance' ),
        ) );
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        $metrics = $this->get_current_metrics();
        $recommendations = $this->get_recommendations();
        ?>
        <div class="wrap nexus-performance-wrap">
            <h1><?php esc_html_e( 'Performance Monitor', 'nexus-pro' ); ?></h1>

            <div class="nexus-performance-header">
                <button class="button button-primary" id="nexus-run-performance-test">
                    <span class="dashicons dashicons-update"></span>
                    <?php esc_html_e( 'Run Performance Test', 'nexus-pro' ); ?>
                </button>
            </div>

            <!-- Performance Score -->
            <div class="nexus-performance-score">
                <div class="nexus-score-circle" data-score="<?php echo esc_attr( $metrics['score'] ); ?>">
                    <svg width="200" height="200">
                        <circle cx="100" cy="100" r="90" fill="none" stroke="#e0e0e0" stroke-width="10"/>
                        <circle cx="100" cy="100" r="90" fill="none" stroke="<?php echo esc_attr( $this->get_score_color( $metrics['score'] ) ); ?>" stroke-width="10" stroke-dasharray="565" stroke-dashoffset="<?php echo esc_attr( 565 - ( 565 * $metrics['score'] / 100 ) ); ?>" transform="rotate(-90 100 100)"/>
                        <text x="100" y="110" text-anchor="middle" font-size="48" font-weight="bold" fill="#333"><?php echo esc_html( $metrics['score'] ); ?></text>
                    </svg>
                </div>
                <h2><?php esc_html_e( 'Performance Score', 'nexus-pro' ); ?></h2>
            </div>

            <!-- Metrics Grid -->
            <div class="nexus-metrics-grid">
                <div class="nexus-metric-card">
                    <h3><?php esc_html_e( 'Page Load Time', 'nexus-pro' ); ?></h3>
                    <div class="nexus-metric-value"><?php echo esc_html( round( $metrics['load_time'], 2 ) ); ?>s</div>
                    <div class="nexus-metric-status <?php echo esc_attr( $metrics['load_time'] < 2 ? 'good' : 'warning' ); ?>">
                        <?php echo $metrics['load_time'] < 2 ? esc_html__( 'Good', 'nexus-pro' ) : esc_html__( 'Needs Improvement', 'nexus-pro' ); ?>
                    </div>
                </div>

                <div class="nexus-metric-card">
                    <h3><?php esc_html_e( 'Database Queries', 'nexus-pro' ); ?></h3>
                    <div class="nexus-metric-value"><?php echo esc_html( $metrics['db_queries'] ); ?></div>
                    <div class="nexus-metric-status <?php echo esc_attr( $metrics['db_queries'] < 50 ? 'good' : 'warning' ); ?>">
                        <?php echo $metrics['db_queries'] < 50 ? esc_html__( 'Good', 'nexus-pro' ) : esc_html__( 'Too Many', 'nexus-pro' ); ?>
                    </div>
                </div>

                <div class="nexus-metric-card">
                    <h3><?php esc_html_e( 'Memory Usage', 'nexus-pro' ); ?></h3>
                    <div class="nexus-metric-value"><?php echo esc_html( size_format( $metrics['memory'] ) ); ?></div>
                    <div class="nexus-metric-status <?php echo esc_attr( $metrics['memory'] < 50000000 ? 'good' : 'warning' ); ?>">
                        <?php echo $metrics['memory'] < 50000000 ? esc_html__( 'Good', 'nexus-pro' ) : esc_html__( 'High', 'nexus-pro' ); ?>
                    </div>
                </div>

                <div class="nexus-metric-card">
                    <h3><?php esc_html_e( 'Page Size', 'nexus-pro' ); ?></h3>
                    <div class="nexus-metric-value"><?php echo esc_html( size_format( $metrics['page_size'] ) ); ?></div>
                    <div class="nexus-metric-status <?php echo esc_attr( $metrics['page_size'] < 1000000 ? 'good' : 'warning' ); ?>">
                        <?php echo $metrics['page_size'] < 1000000 ? esc_html__( 'Good', 'nexus-pro' ) : esc_html__( 'Large', 'nexus-pro' ); ?>
                    </div>
                </div>
            </div>

            <!-- Recommendations -->
            <div class="nexus-recommendations">
                <h2><?php esc_html_e( 'Optimization Recommendations', 'nexus-pro' ); ?></h2>
                <?php if ( ! empty( $recommendations ) ) : ?>
                    <ul class="nexus-recommendations-list">
                        <?php foreach ( $recommendations as $rec ) : ?>
                            <li class="nexus-recommendation-item priority-<?php echo esc_attr( $rec['priority'] ); ?>">
                                <span class="dashicons dashicons-<?php echo esc_attr( $rec['icon'] ); ?>"></span>
                                <div class="nexus-recommendation-content">
                                    <h4><?php echo esc_html( $rec['title'] ); ?></h4>
                                    <p><?php echo esc_html( $rec['description'] ); ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p><?php esc_html_e( 'Great! No optimization recommendations at this time.', 'nexus-pro' ); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Get current metrics
     */
    private function get_current_metrics() {
        global $wpdb;

        return array(
            'score'      => $this->calculate_performance_score(),
            'load_time'  => $this->get_avg_load_time(),
            'db_queries' => $wpdb->num_queries,
            'memory'     => memory_get_peak_usage(),
            'page_size'  => $this->estimate_page_size(),
        );
    }

    /**
     * Calculate performance score
     */
    private function calculate_performance_score() {
        $load_time = $this->get_avg_load_time();
        $score = 100;

        // Deduct points based on load time
        if ( $load_time > 1 ) $score -= 10;
        if ( $load_time > 2 ) $score -= 20;
        if ( $load_time > 3 ) $score -= 30;

        global $wpdb;
        if ( $wpdb->num_queries > 50 ) $score -= 15;
        if ( $wpdb->num_queries > 100 ) $score -= 25;

        if ( memory_get_peak_usage() > 50000000 ) $score -= 10;
        if ( $this->estimate_page_size() > 1000000 ) $score -= 10;

        return max( 0, $score );
    }

    /**
     * Get average load time
     */
    private function get_avg_load_time() {
        return get_transient( 'nexus_avg_load_time' ) ?: 1.5;
    }

    /**
     * Estimate page size
     */
    private function estimate_page_size() {
        return get_transient( 'nexus_page_size' ) ?: 500000;
    }

    /**
     * Get score color
     */
    private function get_score_color( $score ) {
        if ( $score >= 80 ) return '#4caf50';
        if ( $score >= 50 ) return '#ff9800';
        return '#f44336';
    }

    /**
     * Get recommendations
     */
    private function get_recommendations() {
        $recommendations = array();

        if ( $this->get_avg_load_time() > 2 ) {
            $recommendations[] = array(
                'priority'    => 'high',
                'icon'        => 'performance',
                'title'       => __( 'Reduce Page Load Time', 'nexus-pro' ),
                'description' => __( 'Enable caching and optimize images to improve load times.', 'nexus-pro' ),
            );
        }

        global $wpdb;
        if ( $wpdb->num_queries > 50 ) {
            $recommendations[] = array(
                'priority'    => 'high',
                'icon'        => 'database',
                'title'       => __( 'Optimize Database Queries', 'nexus-pro' ),
                'description' => __( 'Reduce the number of database queries or enable query caching.', 'nexus-pro' ),
            );
        }

        if ( ! function_exists( 'gzencode' ) ) {
            $recommendations[] = array(
                'priority'    => 'medium',
                'icon'        => 'archive',
                'title'       => __( 'Enable GZIP Compression', 'nexus-pro' ),
                'description' => __( 'GZIP compression is not enabled. Contact your host to enable it.', 'nexus-pro' ),
            );
        }

        return $recommendations;
    }

    /**
     * Track performance
     */
    public function track_performance() {
        if ( is_admin() || defined( 'DOING_AJAX' ) ) {
            return;
        }

        $load_time = microtime( true ) - $_SERVER['REQUEST_TIME_FLOAT'];
        set_transient( 'nexus_avg_load_time', $load_time, HOUR_IN_SECONDS );
    }

    /**
     * AJAX: Run performance test
     */
    public function ajax_run_test() {
        check_ajax_referer( 'nexus_performance', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }

        $metrics = $this->get_current_metrics();
        wp_send_json_success( $metrics );
    }
}

// Initialize
Nexus_Performance::get_instance();
