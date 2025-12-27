<?php
/**
 * Nexus Pro Database Schema
 * 
 * Creates and manages database tables for Pro features
 * 
 * @package Nexus_Pro
 * @since 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Nexus_Database_Schema {
	
	/**
	 * Database version
	 */
	const DB_VERSION = '1.6.0';
	
	/**
	 * Initialize
	 */
	public static function init() {
		$installed_version = get_option( 'nexus_db_version', '0' );
		
		if ( version_compare( $installed_version, self::DB_VERSION, '<' ) ) {
			self::create_tables();
			update_option( 'nexus_db_version', self::DB_VERSION );
		}
	}
	
	/**
	 * Create all tables
	 */
	public static function create_tables() {
		global $wpdb;
		
		$charset_collate = $wpdb->get_charset_collate();
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		// Cloud templates table
		$sql_cloud_templates = "CREATE TABLE {$wpdb->prefix}nexus_cloud_templates (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			template_id varchar(100) NOT NULL,
			template_name varchar(255) NOT NULL,
			template_type varchar(50) NOT NULL,
			template_data longtext NOT NULL,
			cloud_url varchar(500) DEFAULT NULL,
			cloud_key varchar(500) DEFAULT NULL,
			last_synced datetime DEFAULT NULL,
			sync_status varchar(20) DEFAULT 'pending',
			file_size bigint(20) DEFAULT 0,
			checksum varchar(64) DEFAULT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY template_id (template_id),
			KEY template_type (template_type),
			KEY sync_status (sync_status),
			KEY last_synced (last_synced)
		) $charset_collate;";
		
		dbDelta( $sql_cloud_templates );
		
		// Payment orders table
		$sql_payment_orders = "CREATE TABLE {$wpdb->prefix}nexus_payment_orders (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			gateway varchar(50) NOT NULL,
			order_id varchar(100) NOT NULL,
			payment_id varchar(100) DEFAULT NULL,
			amount bigint(20) NOT NULL,
			currency varchar(3) NOT NULL,
			status varchar(20) NOT NULL,
			metadata longtext DEFAULT NULL,
			payment_data longtext DEFAULT NULL,
			created_at datetime NOT NULL,
			updated_at datetime DEFAULT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY order_id (order_id),
			KEY user_id (user_id),
			KEY gateway (gateway),
			KEY status (status),
			KEY created_at (created_at)
		) $charset_collate;";
		
		dbDelta( $sql_payment_orders );
		
		// Payment logs table
		$sql_payment_logs = "CREATE TABLE {$wpdb->prefix}nexus_payment_logs (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			payment_id varchar(100) NOT NULL,
			status varchar(20) NOT NULL,
			message text NOT NULL,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY payment_id (payment_id),
			KEY status (status),
			KEY created_at (created_at)
		) $charset_collate;";
		
		dbDelta( $sql_payment_logs );
		
		// Cloud sync logs table
		$sql_sync_logs = "CREATE TABLE {$wpdb->prefix}nexus_sync_logs (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			template_id varchar(100) NOT NULL,
			action varchar(50) NOT NULL,
			status varchar(20) NOT NULL,
			message text DEFAULT NULL,
			bytes_transferred bigint(20) DEFAULT 0,
			duration_ms int(11) DEFAULT 0,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY template_id (template_id),
			KEY action (action),
			KEY status (status),
			KEY created_at (created_at)
		) $charset_collate;";
		
		dbDelta( $sql_sync_logs );
		
		// Credits transactions table
		$sql_credits = "CREATE TABLE {$wpdb->prefix}nexus_credits (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			credits int(11) NOT NULL,
			transaction_type varchar(20) NOT NULL,
			reference_id varchar(100) DEFAULT NULL,
			description text DEFAULT NULL,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY transaction_type (transaction_type),
			KEY created_at (created_at)
		) $charset_collate;";
		
		dbDelta( $sql_credits );
		
		// Analytics events table (Advanced tier)
		$sql_analytics = "CREATE TABLE {$wpdb->prefix}nexus_analytics_events (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) DEFAULT NULL,
			session_id varchar(100) NOT NULL,
			event_type varchar(50) NOT NULL,
			event_name varchar(100) NOT NULL,
			event_data longtext DEFAULT NULL,
			page_url varchar(500) DEFAULT NULL,
			referrer varchar(500) DEFAULT NULL,
			user_agent text DEFAULT NULL,
			ip_address varchar(45) DEFAULT NULL,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY session_id (session_id),
			KEY event_type (event_type),
			KEY event_name (event_name),
			KEY created_at (created_at)
		) $charset_collate;";
		
		dbDelta( $sql_analytics );
		
		// A/B tests table (Agency tier)
		$sql_ab_tests = "CREATE TABLE {$wpdb->prefix}nexus_ab_tests (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			test_name varchar(255) NOT NULL,
			test_type varchar(50) NOT NULL,
			variant_a longtext NOT NULL,
			variant_b longtext NOT NULL,
			status varchar(20) NOT NULL DEFAULT 'active',
			impressions_a int(11) DEFAULT 0,
			impressions_b int(11) DEFAULT 0,
			conversions_a int(11) DEFAULT 0,
			conversions_b int(11) DEFAULT 0,
			winner varchar(1) DEFAULT NULL,
			created_at datetime NOT NULL,
			ended_at datetime DEFAULT NULL,
			PRIMARY KEY  (id),
			KEY test_type (test_type),
			KEY status (status),
			KEY created_at (created_at)
		) $charset_collate;";
		
		dbDelta( $sql_ab_tests );
	}
	
	/**
	 * Drop all tables (for uninstall)
	 */
	public static function drop_tables() {
		global $wpdb;
		
		$tables = array(
			'nexus_cloud_templates',
			'nexus_payment_orders',
			'nexus_payment_logs',
			'nexus_sync_logs',
			'nexus_credits',
			'nexus_analytics_events',
			'nexus_ab_tests',
		);
		
		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}{$table}" );
		}
		
		delete_option( 'nexus_db_version' );
	}
}

// Initialize on theme activation
add_action( 'after_switch_theme', array( 'Nexus_Database_Schema', 'init' ) );

// Also check on admin_init
add_action( 'admin_init', array( 'Nexus_Database_Schema', 'init' ) );
