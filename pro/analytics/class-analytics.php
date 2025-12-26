<?php
/**
 * Analytics Dashboard - Main Class
 *
 * @package Nexus_Pro
 * @subpackage Analytics
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Analytics Dashboard Class
 */
class Nexus_Analytics {

    /**
     * Instance
     *
     * @var Nexus_Analytics
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Nexus_Analytics
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
        add_action( 'wp_footer', array( $this, 'track_page_view' ) );
        add_action( 'wp_ajax_nexus_analytics_data', array( $this, 'ajax_get_analytics_data' ) );
        add_action( 'wp_ajax_nexus_analytics_export', array( $this, 'ajax_export_data' ) );
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'nexus-pro',
            __( 'Analytics', 'nexus-pro' ),
            __( 'Analytics', 'nexus-pro' ),
            'manage_options',
            'nexus-analytics',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_admin_assets( $hook ) {
        if ( $hook !== 'nexus-pro_page_nexus-analytics' ) {
            return;
        }

        wp_enqueue_style(
            'nexus-analytics-admin',
            NEXUS_PRO_URL . 'assets/css/analytics.css',
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
            'nexus-analytics-admin',
            NEXUS_PRO_URL . 'assets/js/analytics.js',
            array( 'jquery', 'chart-js' ),
            '3.0.0',
            true
        );

        wp_localize_script( 'nexus-analytics-admin', 'nexusAnalytics', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'nexus_analytics' ),
            'i18n'    => array(
                'loading'      => __( 'Loading...', 'nexus-pro' ),
                'error'        => __( 'Error loading data', 'nexus-pro' ),
                'noData'       => __( 'No data available', 'nexus-pro' ),
                'pageViews'    => __( 'Page Views', 'nexus-pro' ),
                'uniqueVisits' => __( 'Unique Visits', 'nexus-pro' ),
                'avgDuration'  => __( 'Avg. Duration', 'nexus-pro' ),
                'bounceRate'   => __( 'Bounce Rate', 'nexus-pro' ),
            ),
        ) );
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        $stats = $this->get_overview_stats();
        $top_pages = $this->get_top_pages( 10 );
        $referrers = $this->get_top_referrers( 10 );
        ?>
        <div class="wrap nexus-analytics-wrap">
            <h1><?php esc_html_e( 'Analytics Dashboard', 'nexus-pro' ); ?></h1>

            <div class="nexus-analytics-header">
                <select id="nexus-analytics-period">
                    <option value="7"><?php esc_html_e( 'Last 7 Days', 'nexus-pro' ); ?></option>
                    <option value="30"><?php esc_html_e( 'Last 30 Days', 'nexus-pro' ); ?></option>
                    <option value="90"><?php esc_html_e( 'Last 90 Days', 'nexus-pro' ); ?></option>
                </select>
                <button class="button" id="nexus-analytics-export">
                    <span class="dashicons dashicons-download"></span>
                    <?php esc_html_e( 'Export Data', 'nexus-pro' ); ?>
                </button>
            </div>

            <!-- Overview Stats -->
            <div class="nexus-analytics-stats">
                <div class="nexus-stat-card">
                    <div class="nexus-stat-icon" style="background: #2196f3;">
                        <span class="dashicons dashicons-visibility"></span>
                    </div>
                    <div class="nexus-stat-content">
                        <h3><?php echo number_format( $stats['total_views'] ?? 0 ); ?></h3>
                        <p><?php esc_html_e( 'Total Page Views', 'nexus-pro' ); ?></p>
                    </div>
                </div>

                <div class="nexus-stat-card">
                    <div class="nexus-stat-icon" style="background: #4caf50;">
                        <span class="dashicons dashicons-groups"></span>
                    </div>
                    <div class="nexus-stat-content">
                        <h3><?php echo number_format( $stats['unique_visitors'] ?? 0 ); ?></h3>
                        <p><?php esc_html_e( 'Unique Visitors', 'nexus-pro' ); ?></p>
                    </div>
                </div>

                <div class="nexus-stat-card">
                    <div class="nexus-stat-icon" style="background: #ff9800;">
                        <span class="dashicons dashicons-clock"></span>
                    </div>
                    <div class="nexus-stat-content">
                        <h3><?php echo esc_html( $this->format_duration( $stats['avg_duration'] ?? 0 ) ); ?></h3>
                        <p><?php esc_html_e( 'Avg. Session Duration', 'nexus-pro' ); ?></p>
                    </div>
                </div>

                <div class="nexus-stat-card">
                    <div class="nexus-stat-icon" style="background: #e91e63;">
                        <span class="dashicons dashicons-chart-line"></span>
                    </div>
                    <div class="nexus-stat-content">
                        <h3><?php echo number_format( $stats['bounce_rate'] ?? 0, 1 ); ?>%</h3>
                        <p><?php esc_html_e( 'Bounce Rate', 'nexus-pro' ); ?></p>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="nexus-analytics-charts">
                <div class="nexus-chart-card">
                    <h3><?php esc_html_e( 'Page Views Over Time', 'nexus-pro' ); ?></h3>
                    <canvas id="nexus-views-chart"></canvas>
                </div>

                <div class="nexus-chart-card">
                    <h3><?php esc_html_e( 'Traffic Sources', 'nexus-pro' ); ?></h3>
                    <canvas id="nexus-sources-chart"></canvas>
                </div>
            </div>

            <!-- Tables -->
            <div class="nexus-analytics-tables">
                <div class="nexus-table-card">
                    <h3><?php esc_html_e( 'Top Pages', 'nexus-pro' ); ?></h3>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Page', 'nexus-pro' ); ?></th>
                                <th><?php esc_html_e( 'Views', 'nexus-pro' ); ?></th>
                                <th><?php esc_html_e( 'Unique', 'nexus-pro' ); ?></th>
                                <th><?php esc_html_e( 'Avg. Time', 'nexus-pro' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ( ! empty( $top_pages ) ) : ?>
                                <?php foreach ( $top_pages as $page ) : ?>
                                    <tr>
                                        <td><?php echo esc_html( $page['page_url'] ); ?></td>
                                        <td><?php echo number_format( $page['views'] ); ?></td>
                                        <td><?php echo number_format( $page['unique_visits'] ); ?></td>
                                        <td><?php echo esc_html( $this->format_duration( $page['avg_duration'] ) ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="4"><?php esc_html_e( 'No data available', 'nexus-pro' ); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="nexus-table-card">
                    <h3><?php esc_html_e( 'Top Referrers', 'nexus-pro' ); ?></h3>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Source', 'nexus-pro' ); ?></th>
                                <th><?php esc_html_e( 'Visits', 'nexus-pro' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ( ! empty( $referrers ) ) : ?>
                                <?php foreach ( $referrers as $referrer ) : ?>
                                    <tr>
                                        <td><?php echo esc_html( $referrer['referrer'] ); ?></td>
                                        <td><?php echo number_format( $referrer['visits'] ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="2"><?php esc_html_e( 'No data available', 'nexus-pro' ); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Track page view
     */
    public function track_page_view() {
        if ( is_admin() || is_user_logged_in() ) {
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'nexus_analytics';

        $wpdb->insert( $table, array(
            'page_id'      => get_queried_object_id(),
            'page_url'     => esc_url_raw( $_SERVER['REQUEST_URI'] ?? '' ),
            'referrer'     => esc_url_raw( $_SERVER['HTTP_REFERER'] ?? 'Direct' ),
            'user_agent'   => sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ?? '' ),
            'ip_address'   => $this->get_client_ip(),
            'session_id'   => $this->get_session_id(),
            'metric_type'  => 'pageview',
            'metric_value' => 1,
            'recorded_at'  => current_time( 'mysql' ),
        ) );
    }

    /**
     * Get overview stats
     *
     * @param int $days Number of days.
     * @return array
     */
    private function get_overview_stats( $days = 30 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'nexus_analytics';
        $date = date( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );

        $total_views = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE recorded_at >= %s",
            $date
        ) );

        $unique_visitors = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM {$table} WHERE recorded_at >= %s",
            $date
        ) );

        $avg_duration = $wpdb->get_var( $wpdb->prepare(
            "SELECT AVG(metric_value) FROM {$table} WHERE metric_type = 'duration' AND recorded_at >= %s",
            $date
        ) );

        $bounce_rate = $this->calculate_bounce_rate( $days );

        return array(
            'total_views'      => (int) $total_views,
            'unique_visitors'  => (int) $unique_visitors,
            'avg_duration'     => (float) $avg_duration,
            'bounce_rate'      => (float) $bounce_rate,
        );
    }

    /**
     * Get top pages
     *
     * @param int $limit Number of pages.
     * @return array
     */
    private function get_top_pages( $limit = 10 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'nexus_analytics';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT 
                page_url,
                COUNT(*) as views,
                COUNT(DISTINCT session_id) as unique_visits,
                AVG(CASE WHEN metric_type = 'duration' THEN metric_value END) as avg_duration
            FROM {$table}
            WHERE recorded_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY page_url
            ORDER BY views DESC
            LIMIT %d",
            $limit
        ), ARRAY_A );
    }

    /**
     * Get top referrers
     *
     * @param int $limit Number of referrers.
     * @return array
     */
    private function get_top_referrers( $limit = 10 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'nexus_analytics';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT 
                referrer,
                COUNT(*) as visits
            FROM {$table}
            WHERE recorded_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            AND referrer != 'Direct'
            GROUP BY referrer
            ORDER BY visits DESC
            LIMIT %d",
            $limit
        ), ARRAY_A );
    }

    /**
     * Calculate bounce rate
     *
     * @param int $days Number of days.
     * @return float
     */
    private function calculate_bounce_rate( $days = 30 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'nexus_analytics';
        $date = date( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );

        $total_sessions = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM {$table} WHERE recorded_at >= %s",
            $date
        ) );

        $bounced_sessions = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT session_id) 
            FROM {$table} a
            WHERE recorded_at >= %s
            AND (SELECT COUNT(*) FROM {$table} b WHERE a.session_id = b.session_id) = 1",
            $date
        ) );

        return $total_sessions > 0 ? ( $bounced_sessions / $total_sessions ) * 100 : 0;
    }

    /**
     * Format duration
     *
     * @param float $seconds Duration in seconds.
     * @return string
     */
    private function format_duration( $seconds ) {
        if ( $seconds < 60 ) {
            return round( $seconds ) . 's';
        } elseif ( $seconds < 3600 ) {
            return round( $seconds / 60 ) . 'm';
        } else {
            return round( $seconds / 3600, 1 ) . 'h';
        }
    }

    /**
     * Get client IP
     *
     * @return string
     */
    private function get_client_ip() {
        $ip = '';
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        }
        return sanitize_text_field( $ip );
    }

    /**
     * Get session ID
     *
     * @return string
     */
    private function get_session_id() {
        if ( isset( $_COOKIE['nexus_session'] ) ) {
            return sanitize_text_field( $_COOKIE['nexus_session'] );
        }

        $session_id = wp_generate_password( 32, false );
        setcookie( 'nexus_session', $session_id, time() + ( 30 * MINUTE_IN_SECONDS ), '/' );
        return $session_id;
    }

    /**
     * AJAX: Get analytics data
     */
    public function ajax_get_analytics_data() {
        check_ajax_referer( 'nexus_analytics', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }

        $period = absint( $_POST['period'] ?? 30 );
        $stats = $this->get_overview_stats( $period );

        wp_send_json_success( $stats );
    }

    /**
     * AJAX: Export data
     */
    public function ajax_export_data() {
        check_ajax_referer( 'nexus_analytics', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error();
        }

        global $wpdb;
        $table = $wpdb->prefix . 'nexus_analytics';

        $data = $wpdb->get_results(
            "SELECT * FROM {$table} ORDER BY recorded_at DESC LIMIT 10000",
            ARRAY_A
        );

        wp_send_json_success( array( 'data' => $data ) );
    }
}

// Initialize
Nexus_Analytics::get_instance();
