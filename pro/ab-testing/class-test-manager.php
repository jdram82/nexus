<?php
/**
 * Test Manager - Handles test creation and management
 *
 * @package Nexus_Pro
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Nexus_Test_Manager {
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
		$this->table_name = $wpdb->prefix . 'nexus_ab_tests';
		$this->maybe_create_table();
	}

	private function maybe_create_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			test_name varchar(255) NOT NULL,
			status varchar(20) DEFAULT 'draft',
			variants longtext NOT NULL,
			created_date datetime DEFAULT NULL,
			ended_date datetime DEFAULT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY status (status)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	public function create_test( $data ) {
		global $wpdb;

		$wpdb->insert(
			$this->table_name,
			array(
				'test_name' => $data['name'],
				'variants' => wp_json_encode( $data['variants'] ),
			),
			array( '%s', '%s' )
		);

		return $wpdb->insert_id;
	}

	public function get_all_tests() {
		global $wpdb;
		$tests = $wpdb->get_results( "SELECT * FROM {$this->table_name} ORDER BY created_at DESC", ARRAY_A );

		foreach ( $tests as &$test ) {
			$test['variants'] = json_decode( $test['variants'], true );
		}

		return $tests;
	}

	public function get_test( $test_id ) {
		global $wpdb;
		$test = $wpdb->get_row( 
			$wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE id = %d", $test_id ),
			ARRAY_A
		);

		if ( $test ) {
			$test['variants'] = json_decode( $test['variants'], true );
		}

		return $test;
	}

	public function update_test( $test_id, $data ) {
		global $wpdb;

		$update_data = array();
		if ( isset( $data['name'] ) ) {
			$update_data['test_name'] = $data['name'];
		}
		if ( isset( $data['status'] ) ) {
			$update_data['status'] = $data['status'];
		}
		if ( isset( $data['variants'] ) ) {
			$update_data['variants'] = wp_json_encode( $data['variants'] );
		}

		return $wpdb->update(
			$this->table_name,
			$update_data,
			array( 'id' => $test_id ),
			array( '%s' ),
			array( '%d' )
		);
	}

	public function delete_test( $test_id ) {
		global $wpdb;
		return $wpdb->delete(
			$this->table_name,
			array( 'id' => $test_id ),
			array( '%d' )
		);
	}
}
