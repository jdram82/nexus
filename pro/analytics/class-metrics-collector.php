<?php
/**
 * Metrics Collector - Collect and store performance metrics
 *
 * @package Nexus_Pro
 * @subpackage Performance_Analytics
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Metrics Collector Class
 *
 * Collects Core Web Vitals and other performance metrics
 */
class Nexus_Metrics_Collector {

	/**
	 * Instance
	 *
	 * @var Nexus_Metrics_Collector
	 */
	private static $instance = null;

	/**
	 * Metrics table name
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * Get instance
	 *
	 * @return Nexus_Metrics_Collector
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
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'nexus_performance_metrics';
		
		$this->maybe_create_table();
	}

	/**
	 * Create metrics table
	 */
	private function maybe_create_table() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			metric_name varchar(50) NOT NULL,
			metric_value decimal(10,3) NOT NULL,
			page_url varchar(255) NOT NULL,
			page_title varchar(255) DEFAULT '',
			device_type varchar(20) DEFAULT 'desktop',
			user_agent text,
			timestamp datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY metric_name (metric_name),
			KEY timestamp (timestamp)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Record metric
	 *
	 * @param string $metric Metric name.
	 * @param float  $value Metric value.
	 * @param string $url Page URL.
	 */
	public function record_metric( $metric, $value, $url ) {
		global $wpdb;

		$page_title  = $this->get_page_title( $url );
		$device_type = $this->detect_device_type();
		$user_agent  = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '';

		$wpdb->insert(
			$this->table_name,
			array(
				'metric_name'  => $metric,
				'metric_value' => $value,
				'page_url'     => $url,
				'page_title'   => $page_title,
				'device_type'  => $device_type,
				'user_agent'   => $user_agent,
			),
			array( '%s', '%f', '%s', '%s', '%s', '%s' )
		);
	}

	/**
	 * Get page title from URL
	 *
	 * @param string $url Page URL.
	 * @return string
	 */
	private function get_page_title( $url ) {
		$post_id = url_to_postid( $url );
		if ( $post_id ) {
			return get_the_title( $post_id );
		}
		return parse_url( $url, PHP_URL_PATH );
	}

	/**
	 * Detect device type
	 *
	 * @return string
	 */
	private function detect_device_type() {
		if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return 'desktop';
		}

		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		if ( preg_match( '/mobile|android|iphone|ipad|phone/i', $user_agent ) ) {
			return 'mobile';
		} elseif ( preg_match( '/tablet|ipad/i', $user_agent ) ) {
			return 'tablet';
		}

		return 'desktop';
	}

	/**
	 * Get current metrics
	 *
	 * @return array
	 */
	public function get_current_metrics() {
		$metrics = array(
			'lcp'                 => $this->get_metric_summary( 'lcp', 24 ),
			'fid'                 => $this->get_metric_summary( 'fid', 24 ),
			'cls'                 => $this->get_metric_summary( 'cls', 24 ),
			'ttfb'                => $this->get_metric_summary( 'ttfb', 24 ),
			'score'               => $this->calculate_performance_score(),
			'loading_score'       => $this->calculate_loading_score(),
			'interactivity_score' => $this->calculate_interactivity_score(),
			'stability_score'     => $this->calculate_stability_score(),
		);

		return $metrics;
	}

	/**
	 * Get metric summary
	 *
	 * @param string $metric Metric name.
	 * @param int    $hours Hours to look back.
	 * @return array
	 */
	private function get_metric_summary( $metric, $hours = 24 ) {
		global $wpdb;

		$since = gmdate( 'Y-m-d H:i:s', strtotime( "-{$hours} hours" ) );

		$results = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT 
					AVG(metric_value) as avg,
					MIN(metric_value) as min,
					MAX(metric_value) as max,
					COUNT(*) as count
				FROM {$this->table_name}
				WHERE metric_name = %s
				AND timestamp >= %s",
				$metric,
				$since
			),
			ARRAY_A
		);

		if ( ! $results || $results['count'] == 0 ) {
			return array(
				'avg'    => 0,
				'min'    => 0,
				'max'    => 0,
				'count'  => 0,
				'status' => 'unknown',
			);
		}

		$avg    = floatval( $results['avg'] );
		$status = $this->get_metric_status( $metric, $avg );

		return array(
			'avg'    => round( $avg, 3 ),
			'min'    => round( floatval( $results['min'] ), 3 ),
			'max'    => round( floatval( $results['max'] ), 3 ),
			'count'  => intval( $results['count'] ),
			'status' => $status,
		);
	}

	/**
	 * Get metric status
	 *
	 * @param string $metric Metric name.
	 * @param float  $value Metric value.
	 * @return string
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
	 * Get recent data
	 *
	 * @param int $hours Hours to look back.
	 * @param int $limit Number of results.
	 * @return array
	 */
	public function get_recent_data( $hours = 24, $limit = 20 ) {
		global $wpdb;

		$since = gmdate( 'Y-m-d H:i:s', strtotime( "-{$hours} hours" ) );

		// Get unique page loads with their metrics
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT 
					page_url,
					page_title,
					device_type,
					MAX(CASE WHEN metric_name = 'lcp' THEN metric_value END) as lcp,
					MAX(CASE WHEN metric_name = 'fid' THEN metric_value END) as fid,
					MAX(CASE WHEN metric_name = 'cls' THEN metric_value END) as cls,
					MAX(CASE WHEN metric_name = 'ttfb' THEN metric_value END) as ttfb,
					MAX(timestamp) as timestamp
				FROM {$this->table_name}
				WHERE timestamp >= %s
				GROUP BY page_url, device_type, DATE_FORMAT(timestamp, '%%Y-%%m-%%d %%H:%%i')
				ORDER BY timestamp DESC
				LIMIT %d",
				$since,
				$limit
			),
			ARRAY_A
		);

		// Calculate load time for each
		foreach ( $results as &$result ) {
			$result['load_time'] = max(
				floatval( $result['lcp'] ?? 0 ),
				floatval( $result['ttfb'] ?? 0 ) / 1000
			);
			$result['timestamp'] = strtotime( $result['timestamp'] );
		}

		return $results;
	}

	/**
	 * Get metrics summary for time range
	 *
	 * @param int $hours Hours to look back.
	 * @return array
	 */
	public function get_metrics_summary( $hours = 24 ) {
		return array(
			'lcp'       => $this->get_metric_data( 'lcp', $hours ),
			'fid'       => $this->get_metric_data( 'fid', $hours ),
			'cls'       => $this->get_metric_data( 'cls', $hours ),
			'ttfb'      => $this->get_metric_data( 'ttfb', $hours ),
			'by_device' => $this->get_metrics_by_device( $hours ),
			'by_page'   => $this->get_metrics_by_page( $hours ),
			'timeline'  => $this->get_metrics_timeline( $hours ),
		);
	}

	/**
	 * Get metric data
	 *
	 * @param string $metric Metric name.
	 * @param int    $hours Hours to look back.
	 * @return array
	 */
	private function get_metric_data( $metric, $hours ) {
		global $wpdb;

		$since = gmdate( 'Y-m-d H:i:s', strtotime( "-{$hours} hours" ) );

		$data = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT metric_value, timestamp
				FROM {$this->table_name}
				WHERE metric_name = %s
				AND timestamp >= %s
				ORDER BY timestamp ASC",
				$metric,
				$since
			),
			ARRAY_A
		);

		return $data;
	}

	/**
	 * Get metrics by device
	 *
	 * @param int $hours Hours to look back.
	 * @return array
	 */
	private function get_metrics_by_device( $hours ) {
		global $wpdb;

		$since = gmdate( 'Y-m-d H:i:s', strtotime( "-{$hours} hours" ) );

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT 
					device_type,
					metric_name,
					AVG(metric_value) as avg_value,
					COUNT(*) as count
				FROM {$this->table_name}
				WHERE timestamp >= %s
				GROUP BY device_type, metric_name",
				$since
			),
			ARRAY_A
		);

		$data = array();
		foreach ( $results as $row ) {
			$device = $row['device_type'];
			$metric = $row['metric_name'];
			
			if ( ! isset( $data[ $device ] ) ) {
				$data[ $device ] = array();
			}
			
			$data[ $device ][ $metric ] = round( floatval( $row['avg_value'] ), 3 );
		}

		return $data;
	}

	/**
	 * Get metrics by page
	 *
	 * @param int $hours Hours to look back.
	 * @return array
	 */
	private function get_metrics_by_page( $hours ) {
		global $wpdb;

		$since = gmdate( 'Y-m-d H:i:s', strtotime( "-{$hours} hours" ) );

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT 
					page_url,
					page_title,
					metric_name,
					AVG(metric_value) as avg_value,
					COUNT(*) as count
				FROM {$this->table_name}
				WHERE timestamp >= %s
				GROUP BY page_url, metric_name
				ORDER BY count DESC
				LIMIT 10",
				$since
			),
			ARRAY_A
		);

		$data = array();
		foreach ( $results as $row ) {
			$url    = $row['page_url'];
			$metric = $row['metric_name'];
			
			if ( ! isset( $data[ $url ] ) ) {
				$data[ $url ] = array(
					'title' => $row['page_title'],
					'count' => 0,
				);
			}
			
			$data[ $url ][ $metric ] = round( floatval( $row['avg_value'] ), 3 );
			$data[ $url ]['count']  += intval( $row['count'] );
		}

		return $data;
	}

	/**
	 * Get metrics timeline
	 *
	 * @param int $hours Hours to look back.
	 * @return array
	 */
	private function get_metrics_timeline( $hours ) {
		global $wpdb;

		$since    = gmdate( 'Y-m-d H:i:s', strtotime( "-{$hours} hours" ) );
		$interval = $hours > 48 ? 'HOUR' : '15 MINUTE';

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT 
					DATE_FORMAT(timestamp, '%%Y-%%m-%%d %%H:%%i') as time_bucket,
					metric_name,
					AVG(metric_value) as avg_value
				FROM {$this->table_name}
				WHERE timestamp >= %s
				GROUP BY time_bucket, metric_name
				ORDER BY time_bucket ASC",
				$since
			),
			ARRAY_A
		);

		$timeline = array();
		foreach ( $results as $row ) {
			$time   = $row['time_bucket'];
			$metric = $row['metric_name'];
			
			if ( ! isset( $timeline[ $time ] ) ) {
				$timeline[ $time ] = array( 'time' => $time );
			}
			
			$timeline[ $time ][ $metric ] = round( floatval( $row['avg_value'] ), 3 );
		}

		return array_values( $timeline );
	}

	/**
	 * Calculate performance score
	 *
	 * @return int
	 */
	private function calculate_performance_score() {
		$loading       = $this->calculate_loading_score();
		$interactivity = $this->calculate_interactivity_score();
		$stability     = $this->calculate_stability_score();

		return round( ( $loading * 0.4 ) + ( $interactivity * 0.3 ) + ( $stability * 0.3 ) );
	}

	/**
	 * Calculate loading score
	 *
	 * @return int
	 */
	private function calculate_loading_score() {
		$lcp = $this->get_metric_summary( 'lcp', 24 );
		
		if ( $lcp['count'] == 0 ) {
			return 0;
		}

		$value = $lcp['avg'];
		
		if ( $value <= 2.5 ) {
			return 100;
		} elseif ( $value <= 4.0 ) {
			return 100 - ( ( $value - 2.5 ) / 1.5 ) * 50;
		} else {
			return max( 0, 50 - ( ( $value - 4.0 ) / 2.0 ) * 50 );
		}
	}

	/**
	 * Calculate interactivity score
	 *
	 * @return int
	 */
	private function calculate_interactivity_score() {
		$fid = $this->get_metric_summary( 'fid', 24 );
		
		if ( $fid['count'] == 0 ) {
			return 0;
		}

		$value = $fid['avg'];
		
		if ( $value <= 100 ) {
			return 100;
		} elseif ( $value <= 300 ) {
			return 100 - ( ( $value - 100 ) / 200 ) * 50;
		} else {
			return max( 0, 50 - ( ( $value - 300 ) / 200 ) * 50 );
		}
	}

	/**
	 * Calculate stability score
	 *
	 * @return int
	 */
	private function calculate_stability_score() {
		$cls = $this->get_metric_summary( 'cls', 24 );
		
		if ( $cls['count'] == 0 ) {
			return 0;
		}

		$value = $cls['avg'];
		
		if ( $value <= 0.1 ) {
			return 100;
		} elseif ( $value <= 0.25 ) {
			return 100 - ( ( $value - 0.1 ) / 0.15 ) * 50;
		} else {
			return max( 0, 50 - ( ( $value - 0.25 ) / 0.15 ) * 50 );
		}
	}

	/**
	 * Clear all metrics
	 */
	public function clear_all_metrics() {
		global $wpdb;
		$wpdb->query( "TRUNCATE TABLE {$this->table_name}" );
	}

	/**
	 * Delete old metrics
	 *
	 * @param int $days Days to keep.
	 */
	public function delete_old_metrics( $days = 30 ) {
		global $wpdb;

		$since = gmdate( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$this->table_name} WHERE timestamp < %s",
				$since
			)
		);
	}
}
