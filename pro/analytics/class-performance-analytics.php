<?php
/**
 * Performance Analytics - Track and analyze site performance
 *
 * @package Nexus_Pro
 * @subpackage Performance_Analytics
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Performance Analytics Class
 *
 * Monitors site performance, collects metrics, and generates reports
 */
class Nexus_Performance_Analytics {

	/**
	 * Instance
	 *
	 * @var Nexus_Performance_Analytics
	 */
	private static $instance = null;

	/**
	 * Metrics collector
	 *
	 * @var Nexus_Metrics_Collector
	 */
	private $metrics_collector;

	/**
	 * Report generator
	 *
	 * @var Nexus_Report_Generator
	 */
	private $report_generator;

	/**
	 * Get instance
	 *
	 * @return Nexus_Performance_Analytics
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
		$this->metrics_collector = Nexus_Metrics_Collector::get_instance();
		$this->report_generator  = Nexus_Report_Generator::get_instance();

		add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_ajax_nexus_get_metrics', array( $this, 'get_metrics_ajax' ) );
		add_action( 'wp_ajax_nexus_get_report', array( $this, 'get_report_ajax' ) );
		add_action( 'wp_ajax_nexus_clear_metrics', array( $this, 'clear_metrics_ajax' ) );
		add_action( 'wp_ajax_nexus_export_report', array( $this, 'export_report_ajax' ) );
		
		// Collect metrics on frontend
		add_action( 'wp_footer', array( $this, 'inject_metrics_script' ), 999 );
		add_action( 'wp_ajax_nexus_record_metric', array( $this, 'record_metric_ajax' ) );
		add_action( 'wp_ajax_nopriv_nexus_record_metric', array( $this, 'record_metric_ajax' ) );
	}

	/**
	 * Add admin page
	 */
	public function add_admin_page() {
		add_menu_page(
			__( 'Performance Analytics', 'nexus-pro' ),
			__( 'Performance', 'nexus-pro' ),
			'manage_options',
			'nexus-performance',
			array( $this, 'render_admin_page' ),
			'dashicons-performance',
			30
		);

		add_submenu_page(
			'nexus-performance',
			__( 'Dashboard', 'nexus-pro' ),
			__( 'Dashboard', 'nexus-pro' ),
			'manage_options',
			'nexus-performance',
			array( $this, 'render_admin_page' )
		);

		add_submenu_page(
			'nexus-performance',
			__( 'Reports', 'nexus-pro' ),
			__( 'Reports', 'nexus-pro' ),
			'manage_options',
			'nexus-performance-reports',
			array( $this, 'render_reports_page' )
		);

		add_submenu_page(
			'nexus-performance',
			__( 'Settings', 'nexus-pro' ),
			__( 'Settings', 'nexus-pro' ),
			'manage_options',
			'nexus-performance-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Enqueue assets
	 */
	public function enqueue_assets( $hook ) {
		if ( strpos( $hook, 'nexus-performance' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'nexus-analytics',
			get_template_directory_uri() . '/pro/assets/css/analytics.css',
			array(),
			'3.0.0'
		);

		wp_enqueue_script(
			'chart-js',
			'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js',
			array(),
			'3.9.1',
			true
		);

		wp_enqueue_script(
			'nexus-analytics',
			get_template_directory_uri() . '/pro/assets/js/analytics.js',
			array( 'jquery', 'chart-js' ),
			'3.0.0',
			true
		);

		wp_localize_script(
			'nexus-analytics',
			'nexusAnalyticsData',
			array(
				'nonce'        => wp_create_nonce( 'nexus_analytics_nonce' ),
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'currentPage'  => $hook,
				'i18n'         => array(
					'loading'          => __( 'Loading metrics...', 'nexus-pro' ),
					'error'            => __( 'Error loading data', 'nexus-pro' ),
					'noData'           => __( 'No data available', 'nexus-pro' ),
					'cleared'          => __( 'Metrics cleared successfully', 'nexus-pro' ),
					'confirmClear'     => __( 'Are you sure you want to clear all metrics?', 'nexus-pro' ),
					'exportSuccess'    => __( 'Report exported successfully', 'nexus-pro' ),
					'exportError'      => __( 'Error exporting report', 'nexus-pro' ),
				),
			)
		);
	}

	/**
	 * Render admin page
	 */
	public function render_admin_page() {
		$current_metrics = $this->metrics_collector->get_current_metrics();
		$recent_data     = $this->metrics_collector->get_recent_data( 24 );
		?>
		<div class="wrap nexus-analytics-dashboard">
			<h1>
				<?php esc_html_e( 'Performance Analytics Dashboard', 'nexus-pro' ); ?>
				<button class="button button-primary" id="refresh-metrics">
					<span class="dashicons dashicons-update"></span>
					<?php esc_html_e( 'Refresh', 'nexus-pro' ); ?>
				</button>
			</h1>

			<!-- Time Range Filter -->
			<div class="analytics-toolbar">
				<div class="time-range-selector">
					<label><?php esc_html_e( 'Time Range:', 'nexus-pro' ); ?></label>
					<select id="time-range">
						<option value="1"><?php esc_html_e( 'Last Hour', 'nexus-pro' ); ?></option>
						<option value="24" selected><?php esc_html_e( 'Last 24 Hours', 'nexus-pro' ); ?></option>
						<option value="168"><?php esc_html_e( 'Last 7 Days', 'nexus-pro' ); ?></option>
						<option value="720"><?php esc_html_e( 'Last 30 Days', 'nexus-pro' ); ?></option>
					</select>
				</div>

				<div class="toolbar-actions">
					<button class="button" id="export-report">
						<span class="dashicons dashicons-download"></span>
						<?php esc_html_e( 'Export Report', 'nexus-pro' ); ?>
					</button>
					<button class="button button-secondary" id="clear-metrics">
						<span class="dashicons dashicons-trash"></span>
						<?php esc_html_e( 'Clear Data', 'nexus-pro' ); ?>
					</button>
				</div>
			</div>

			<!-- Core Web Vitals -->
			<div class="metrics-grid">
				<div class="metric-card metric-lcp">
					<div class="metric-header">
						<h3><?php esc_html_e( 'Largest Contentful Paint', 'nexus-pro' ); ?></h3>
						<span class="metric-info" title="<?php esc_attr_e( 'LCP measures loading performance. Good: < 2.5s', 'nexus-pro' ); ?>">
							<span class="dashicons dashicons-info"></span>
						</span>
					</div>
					<div class="metric-value" data-metric="lcp">
						<?php echo esc_html( $current_metrics['lcp']['avg'] ?? '--' ); ?>
						<span class="metric-unit">s</span>
					</div>
					<div class="metric-status status-<?php echo esc_attr( $current_metrics['lcp']['status'] ?? 'unknown' ); ?>">
						<?php echo esc_html( ucfirst( $current_metrics['lcp']['status'] ?? 'Unknown' ) ); ?>
					</div>
					<div class="metric-trend" data-trend="lcp"></div>
				</div>

				<div class="metric-card metric-fid">
					<div class="metric-header">
						<h3><?php esc_html_e( 'First Input Delay', 'nexus-pro' ); ?></h3>
						<span class="metric-info" title="<?php esc_attr_e( 'FID measures interactivity. Good: < 100ms', 'nexus-pro' ); ?>">
							<span class="dashicons dashicons-info"></span>
						</span>
					</div>
					<div class="metric-value" data-metric="fid">
						<?php echo esc_html( $current_metrics['fid']['avg'] ?? '--' ); ?>
						<span class="metric-unit">ms</span>
					</div>
					<div class="metric-status status-<?php echo esc_attr( $current_metrics['fid']['status'] ?? 'unknown' ); ?>">
						<?php echo esc_html( ucfirst( $current_metrics['fid']['status'] ?? 'Unknown' ) ); ?>
					</div>
					<div class="metric-trend" data-trend="fid"></div>
				</div>

				<div class="metric-card metric-cls">
					<div class="metric-header">
						<h3><?php esc_html_e( 'Cumulative Layout Shift', 'nexus-pro' ); ?></h3>
						<span class="metric-info" title="<?php esc_attr_e( 'CLS measures visual stability. Good: < 0.1', 'nexus-pro' ); ?>">
							<span class="dashicons dashicons-info"></span>
						</span>
					</div>
					<div class="metric-value" data-metric="cls">
						<?php echo esc_html( $current_metrics['cls']['avg'] ?? '--' ); ?>
					</div>
					<div class="metric-status status-<?php echo esc_attr( $current_metrics['cls']['status'] ?? 'unknown' ); ?>">
						<?php echo esc_html( ucfirst( $current_metrics['cls']['status'] ?? 'Unknown' ) ); ?>
					</div>
					<div class="metric-trend" data-trend="cls"></div>
				</div>

				<div class="metric-card metric-ttfb">
					<div class="metric-header">
						<h3><?php esc_html_e( 'Time to First Byte', 'nexus-pro' ); ?></h3>
						<span class="metric-info" title="<?php esc_attr_e( 'TTFB measures server response time. Good: < 600ms', 'nexus-pro' ); ?>">
							<span class="dashicons dashicons-info"></span>
						</span>
					</div>
					<div class="metric-value" data-metric="ttfb">
						<?php echo esc_html( $current_metrics['ttfb']['avg'] ?? '--' ); ?>
						<span class="metric-unit">ms</span>
					</div>
					<div class="metric-status status-<?php echo esc_attr( $current_metrics['ttfb']['status'] ?? 'unknown' ); ?>">
						<?php echo esc_html( ucfirst( $current_metrics['ttfb']['status'] ?? 'Unknown' ) ); ?>
					</div>
					<div class="metric-trend" data-trend="ttfb"></div>
				</div>
			</div>

			<!-- Charts -->
			<div class="charts-section">
				<div class="chart-container">
					<h3><?php esc_html_e( 'Core Web Vitals Trends', 'nexus-pro' ); ?></h3>
					<canvas id="vitals-chart"></canvas>
				</div>

				<div class="chart-container">
					<h3><?php esc_html_e( 'Resource Usage', 'nexus-pro' ); ?></h3>
					<canvas id="resources-chart"></canvas>
				</div>
			</div>

			<!-- Performance Score -->
			<div class="score-section">
				<div class="performance-score">
					<h3><?php esc_html_e( 'Overall Performance Score', 'nexus-pro' ); ?></h3>
					<div class="score-circle" data-score="<?php echo esc_attr( $current_metrics['score'] ?? 0 ); ?>">
						<span class="score-value"><?php echo esc_html( $current_metrics['score'] ?? 0 ); ?></span>
						<span class="score-max">/100</span>
					</div>
					<div class="score-breakdown">
						<div class="breakdown-item">
							<span class="breakdown-label"><?php esc_html_e( 'Loading', 'nexus-pro' ); ?></span>
							<div class="breakdown-bar">
								<div class="breakdown-fill" style="width: <?php echo esc_attr( $current_metrics['loading_score'] ?? 0 ); ?>%;"></div>
							</div>
							<span class="breakdown-value"><?php echo esc_html( $current_metrics['loading_score'] ?? 0 ); ?>%</span>
						</div>
						<div class="breakdown-item">
							<span class="breakdown-label"><?php esc_html_e( 'Interactivity', 'nexus-pro' ); ?></span>
							<div class="breakdown-bar">
								<div class="breakdown-fill" style="width: <?php echo esc_attr( $current_metrics['interactivity_score'] ?? 0 ); ?>%;"></div>
							</div>
							<span class="breakdown-value"><?php echo esc_html( $current_metrics['interactivity_score'] ?? 0 ); ?>%</span>
						</div>
						<div class="breakdown-item">
							<span class="breakdown-label"><?php esc_html_e( 'Visual Stability', 'nexus-pro' ); ?></span>
							<div class="breakdown-bar">
								<div class="breakdown-fill" style="width: <?php echo esc_attr( $current_metrics['stability_score'] ?? 0 ); ?>%;"></div>
							</div>
							<span class="breakdown-value"><?php echo esc_html( $current_metrics['stability_score'] ?? 0 ); ?>%</span>
						</div>
					</div>
				</div>

				<!-- Optimization Suggestions -->
				<div class="optimization-suggestions">
					<h3><?php esc_html_e( 'Optimization Suggestions', 'nexus-pro' ); ?></h3>
					<div id="suggestions-list">
						<?php $this->render_optimization_suggestions(); ?>
					</div>
				</div>
			</div>

			<!-- Recent Page Loads -->
			<div class="recent-loads">
				<h3><?php esc_html_e( 'Recent Page Loads', 'nexus-pro' ); ?></h3>
				<table class="wp-list-table widefat striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Page', 'nexus-pro' ); ?></th>
							<th><?php esc_html_e( 'Load Time', 'nexus-pro' ); ?></th>
							<th><?php esc_html_e( 'LCP', 'nexus-pro' ); ?></th>
							<th><?php esc_html_e( 'FID', 'nexus-pro' ); ?></th>
							<th><?php esc_html_e( 'CLS', 'nexus-pro' ); ?></th>
							<th><?php esc_html_e( 'Device', 'nexus-pro' ); ?></th>
							<th><?php esc_html_e( 'Time', 'nexus-pro' ); ?></th>
						</tr>
					</thead>
					<tbody id="recent-loads-table">
						<?php $this->render_recent_loads( $recent_data ); ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}

	/**
	 * Render optimization suggestions
	 */
	private function render_optimization_suggestions() {
		$suggestions = $this->report_generator->get_optimization_suggestions();
		
		if ( empty( $suggestions ) ) {
			echo '<p class="no-suggestions">' . esc_html__( 'No optimization suggestions at this time. Great job!', 'nexus-pro' ) . '</p>';
			return;
		}

		foreach ( $suggestions as $suggestion ) {
			$priority_class = 'priority-' . $suggestion['priority'];
			?>
			<div class="suggestion-item <?php echo esc_attr( $priority_class ); ?>">
				<div class="suggestion-icon">
					<span class="dashicons dashicons-<?php echo esc_attr( $suggestion['icon'] ); ?>"></span>
				</div>
				<div class="suggestion-content">
					<h4><?php echo esc_html( $suggestion['title'] ); ?></h4>
					<p><?php echo esc_html( $suggestion['description'] ); ?></p>
					<?php if ( ! empty( $suggestion['action'] ) ) : ?>
						<a href="<?php echo esc_url( $suggestion['action']['url'] ); ?>" class="button button-small">
							<?php echo esc_html( $suggestion['action']['label'] ); ?>
						</a>
					<?php endif; ?>
				</div>
				<div class="suggestion-impact">
					<span class="impact-label"><?php esc_html_e( 'Potential Impact:', 'nexus-pro' ); ?></span>
					<span class="impact-value"><?php echo esc_html( $suggestion['impact'] ); ?></span>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Render recent loads
	 */
	private function render_recent_loads( $data ) {
		if ( empty( $data ) ) {
			?>
			<tr>
				<td colspan="7"><?php esc_html_e( 'No recent data available', 'nexus-pro' ); ?></td>
			</tr>
			<?php
			return;
		}

		foreach ( $data as $load ) {
			?>
			<tr>
				<td><a href="<?php echo esc_url( $load['url'] ); ?>" target="_blank"><?php echo esc_html( $load['page_title'] ); ?></a></td>
				<td><?php echo esc_html( number_format( $load['load_time'], 2 ) ); ?>s</td>
				<td class="metric-<?php echo esc_attr( $this->get_metric_status( 'lcp', $load['lcp'] ) ); ?>"><?php echo esc_html( number_format( $load['lcp'], 2 ) ); ?>s</td>
				<td class="metric-<?php echo esc_attr( $this->get_metric_status( 'fid', $load['fid'] ) ); ?>"><?php echo esc_html( number_format( $load['fid'], 0 ) ); ?>ms</td>
				<td class="metric-<?php echo esc_attr( $this->get_metric_status( 'cls', $load['cls'] ) ); ?>"><?php echo esc_html( number_format( $load['cls'], 3 ) ); ?></td>
				<td><?php echo esc_html( $load['device_type'] ); ?></td>
				<td><?php echo esc_html( human_time_diff( $load['timestamp'], current_time( 'timestamp' ) ) ); ?> ago</td>
			</tr>
			<?php
		}
	}

	/**
	 * Get metric status
	 */
	private function get_metric_status( $metric, $value ) {
		$thresholds = array(
			'lcp'  => array( 'good' => 2.5, 'needs_improvement' => 4.0 ),
			'fid'  => array( 'good' => 100, 'needs_improvement' => 300 ),
			'cls'  => array( 'good' => 0.1, 'needs_improvement' => 0.25 ),
			'ttfb' => array( 'good' => 600, 'needs_improvement' => 1000 ),
		);

		if ( ! isset( $thresholds[ $metric ] ) ) {
			return 'unknown';
		}

		if ( $value <= $thresholds[ $metric ]['good'] ) {
			return 'good';
		} elseif ( $value <= $thresholds[ $metric ]['needs_improvement'] ) {
			return 'needs-improvement';
		} else {
			return 'poor';
		}
	}

	/**
	 * Render reports page
	 */
	public function render_reports_page() {
		?>
		<div class="wrap nexus-analytics-reports">
			<h1><?php esc_html_e( 'Performance Reports', 'nexus-pro' ); ?></h1>
			<div id="reports-container"></div>
		</div>
		<?php
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		?>
		<div class="wrap nexus-analytics-settings">
			<h1><?php esc_html_e( 'Performance Analytics Settings', 'nexus-pro' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'nexus_performance_settings' );
				do_settings_sections( 'nexus-performance-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Inject metrics collection script
	 */
	public function inject_metrics_script() {
		if ( is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<script>
		(function() {
			if (!('PerformanceObserver' in window)) return;
			
			const metrics = {};
			const recordMetric = (name, value) => {
				metrics[name] = value;
				
				// Send to server
				const data = new FormData();
				data.append('action', 'nexus_record_metric');
				data.append('metric', name);
				data.append('value', value);
				data.append('url', window.location.href);
				
				navigator.sendBeacon('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', data);
			};

			// LCP
			new PerformanceObserver((list) => {
				const entries = list.getEntries();
				const lastEntry = entries[entries.length - 1];
				recordMetric('lcp', lastEntry.renderTime || lastEntry.loadTime);
			}).observe({entryTypes: ['largest-contentful-paint']});

			// FID
			new PerformanceObserver((list) => {
				list.getEntries().forEach((entry) => {
					recordMetric('fid', entry.processingStart - entry.startTime);
				});
			}).observe({entryTypes: ['first-input']});

			// CLS
			let clsValue = 0;
			new PerformanceObserver((list) => {
				list.getEntries().forEach((entry) => {
					if (!entry.hadRecentInput) {
						clsValue += entry.value;
						recordMetric('cls', clsValue);
					}
				});
			}).observe({entryTypes: ['layout-shift']});

			// TTFB
			window.addEventListener('load', () => {
				const navTiming = performance.getEntriesByType('navigation')[0];
				if (navTiming) {
					recordMetric('ttfb', navTiming.responseStart - navTiming.requestStart);
				}
			});
		})();
		</script>
		<?php
	}

	/**
	 * AJAX: Get metrics
	 */
	public function get_metrics_ajax() {
		check_ajax_referer( 'nexus_analytics_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Permission denied', 'nexus-pro' ) );
		}

		$hours   = isset( $_POST['hours'] ) ? intval( $_POST['hours'] ) : 24;
		$metrics = $this->metrics_collector->get_metrics_summary( $hours );

		wp_send_json_success( $metrics );
	}

	/**
	 * AJAX: Get report
	 */
	public function get_report_ajax() {
		check_ajax_referer( 'nexus_analytics_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Permission denied', 'nexus-pro' ) );
		}

		$report_type = isset( $_POST['report_type'] ) ? sanitize_text_field( $_POST['report_type'] ) : 'summary';
		$report      = $this->report_generator->generate_report( $report_type );

		wp_send_json_success( $report );
	}

	/**
	 * AJAX: Clear metrics
	 */
	public function clear_metrics_ajax() {
		check_ajax_referer( 'nexus_analytics_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Permission denied', 'nexus-pro' ) );
		}

		$this->metrics_collector->clear_all_metrics();
		wp_send_json_success( __( 'All metrics cleared', 'nexus-pro' ) );
	}

	/**
	 * AJAX: Export report
	 */
	public function export_report_ajax() {
		check_ajax_referer( 'nexus_analytics_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Permission denied', 'nexus-pro' ) );
		}

		$format = isset( $_POST['format'] ) ? sanitize_text_field( $_POST['format'] ) : 'csv';
		$report = $this->report_generator->export_report( $format );

		wp_send_json_success( $report );
	}

	/**
	 * AJAX: Record metric (frontend)
	 */
	public function record_metric_ajax() {
		$metric = isset( $_POST['metric'] ) ? sanitize_text_field( $_POST['metric'] ) : '';
		$value  = isset( $_POST['value'] ) ? floatval( $_POST['value'] ) : 0;
		$url    = isset( $_POST['url'] ) ? esc_url_raw( $_POST['url'] ) : '';

		if ( empty( $metric ) || empty( $url ) ) {
			wp_send_json_error();
		}

		$this->metrics_collector->record_metric( $metric, $value, $url );
		wp_send_json_success();
	}
}
