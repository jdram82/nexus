<?php
/**
 * Analytics Tracker - Statistical analysis and conversion tracking
 *
 * @package Nexus_Pro
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Nexus_Analytics_Tracker {
	private static $instance = null;
	private $table_name;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'nexus_ab_analytics';
		$this->maybe_create_table();
	}

	private function maybe_create_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			test_id bigint(20) NOT NULL,
			variant_id bigint(20) NOT NULL,
			event_type varchar(50) DEFAULT 'view',
			conversion_value decimal(10,2) DEFAULT 0,
			user_id bigint(20) NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY test_id (test_id),
			KEY variant_id (variant_id),
			KEY event_type (event_type)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	public function track_event( $test_id, $variant_id, $event_type = 'view', $value = 0 ) {
		global $wpdb;

		$wpdb->insert(
			$this->table_name,
			array(
				'test_id' => $test_id,
				'variant_id' => $variant_id,
				'event_type' => $event_type,
				'conversion_value' => $value,
				'user_id' => get_current_user_id(),
			),
			array( '%d', '%d', '%s', '%f', '%d' )
		);

		return $wpdb->insert_id;
	}

	public function get_test_stats( $test_id ) {
		global $wpdb;

		$stats = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT 
					variant_id,
					event_type,
					COUNT(*) as count,
					SUM(conversion_value) as total_value
				FROM {$this->table_name}
				WHERE test_id = %d
				GROUP BY variant_id, event_type",
				$test_id
			),
			ARRAY_A
		);

		return $stats;
	}

	public function get_variant_stats( $test_id, $variant_id ) {
		global $wpdb;

		$stats = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT 
					event_type,
					COUNT(*) as count,
					SUM(conversion_value) as total_value
				FROM {$this->table_name}
				WHERE test_id = %d AND variant_id = %d
				GROUP BY event_type",
				$test_id,
				$variant_id
			),
			ARRAY_A
		);

		return $stats;
	}

	public function calculate_conversion_rate( $test_id, $variant_id ) {
		global $wpdb;

		$views = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->table_name} 
				WHERE test_id = %d AND variant_id = %d AND event_type = 'view'",
				$test_id,
				$variant_id
			)
		);

		$conversions = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->table_name} 
				WHERE test_id = %d AND variant_id = %d AND event_type = 'conversion'",
				$test_id,
				$variant_id
			)
		);

		if ( $views == 0 ) {
			return 0;
		}

		return ( $conversions / $views ) * 100;
	}

	public function get_winning_variant( $test_id ) {
		global $wpdb;

		$variants = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT 
					variant_id,
					SUM(CASE WHEN event_type = 'view' THEN 1 ELSE 0 END) as views,
					SUM(CASE WHEN event_type = 'conversion' THEN 1 ELSE 0 END) as conversions
				FROM {$this->table_name}
				WHERE test_id = %d
				GROUP BY variant_id",
				$test_id
			),
			ARRAY_A
		);

		$best_variant = null;
		$best_rate = 0;

		foreach ( $variants as $variant ) {
			if ( $variant['views'] == 0 ) {
				continue;
			}

			$rate = ( $variant['conversions'] / $variant['views'] ) * 100;

			if ( $rate > $best_rate ) {
				$best_rate = $rate;
				$best_variant = $variant['variant_id'];
			}
		}

		return array(
			'variant_id' => $best_variant,
			'conversion_rate' => $best_rate,
		);
	}
}
