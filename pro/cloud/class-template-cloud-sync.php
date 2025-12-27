<?php
/**
 * Nexus Template Cloud Sync
 * 
 * Syncs templates to DigitalOcean Spaces cloud storage
 * 
 * @package Nexus_Pro
 * @since 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Nexus_Template_Cloud_Sync {
	
	/**
	 * Singleton instance
	 */
	private static $instance = null;
	
	/**
	 * Cloud storage instance
	 */
	private $cloud_storage;
	
	/**
	 * Tier limits
	 */
	private $tier_limits = array(
		'free' => 0,
		'pro' => 5,
		'advanced' => 999999,
		'agency' => 999999,
	);
	
	/**
	 * Get singleton instance
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * Constructor
	 */
	private function __construct() {
		// Load cloud storage
		if ( class_exists( 'Nexus_Cloud_Storage' ) ) {
			$this->cloud_storage = Nexus_Cloud_Storage::instance();
		}
		
		// Add hooks
		add_action( 'wp_ajax_nexus_sync_template', array( $this, 'ajax_sync_template' ) );
		add_action( 'wp_ajax_nexus_download_template', array( $this, 'ajax_download_template' ) );
		add_action( 'wp_ajax_nexus_delete_cloud_template', array( $this, 'ajax_delete_template' ) );
		add_action( 'wp_ajax_nexus_list_cloud_templates', array( $this, 'ajax_list_templates' ) );
		
		// Auto-sync hooks
		add_action( 'nexus_template_saved', array( $this, 'auto_sync_template' ), 10, 2 );
		add_action( 'nexus_template_deleted', array( $this, 'auto_delete_template' ), 10, 1 );
		
		// Cron hooks
		add_action( 'nexus_cloud_sync_cron', array( $this, 'cron_sync_all' ) );
		
		// Schedule cron if not already scheduled
		if ( ! wp_next_scheduled( 'nexus_cloud_sync_cron' ) ) {
			wp_schedule_event( time(), 'hourly', 'nexus_cloud_sync_cron' );
		}
	}
	
	/**
	 * Check if user can sync templates
	 */
	public function can_sync() {
		$tier = $this->get_user_tier();
		return $this->tier_limits[ $tier ] > 0;
	}
	
	/**
	 * Get user tier
	 */
	private function get_user_tier() {
		$license = get_option( 'nexus_license_tier', 'free' );
		return strtolower( $license );
	}
	
	/**
	 * Get user's cloud template limit
	 */
	public function get_user_limit() {
		$tier = $this->get_user_tier();
		return $this->tier_limits[ $tier ];
	}
	
	/**
	 * Get user's current template count
	 */
	public function get_user_template_count( $user_id = null ) {
		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		}
		
		global $wpdb;
		$table = $wpdb->prefix . 'nexus_cloud_templates';
		
		return (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM {$table} WHERE user_id = %d AND sync_status = 'synced'",
			$user_id
		) );
	}
	
	/**
	 * Check if user can upload more templates
	 */
	public function can_upload_more( $user_id = null ) {
		$limit = $this->get_user_limit();
		
		if ( $limit >= 999999 ) {
			return true; // Unlimited
		}
		
		$current = $this->get_user_template_count( $user_id );
		return $current < $limit;
	}
	
	/**
	 * Upload template to cloud
	 * 
	 * @param string $template_id Unique template ID
	 * @param string $template_name Human-readable name
	 * @param string $template_type Type (header, footer, page, etc.)
	 * @param array $template_data Template data
	 * @return array|WP_Error Result
	 */
	public function upload_template( $template_id, $template_name, $template_type, $template_data ) {
		// Check if cloud storage is available
		if ( ! $this->cloud_storage ) {
			return new WP_Error( 'no_cloud_storage', __( 'Cloud storage not configured', 'nexus' ) );
		}
		
		// Check tier limits
		if ( ! $this->can_upload_more() ) {
			$limit = $this->get_user_limit();
			return new WP_Error(
				'limit_exceeded',
				sprintf( __( 'You have reached your template limit (%d). Upgrade to upload more.', 'nexus' ), $limit )
			);
		}
		
		$start_time = microtime( true );
		
		// Prepare template JSON
		$json_data = wp_json_encode( $template_data, JSON_PRETTY_PRINT );
		$file_size = strlen( $json_data );
		$checksum = md5( $json_data );
		
		// Create filename
		$user_id = get_current_user_id();
		$filename = "templates/{$user_id}/{$template_id}.json";
		
		// Upload to cloud
		$result = $this->cloud_storage->upload( $filename, $json_data, 'application/json' );
		
		if ( is_wp_error( $result ) ) {
			$this->log_sync( $template_id, 'upload', 'failed', $result->get_error_message() );
			return $result;
		}
		
		$duration_ms = ( microtime( true ) - $start_time ) * 1000;
		
		// Save to database
		global $wpdb;
		$table = $wpdb->prefix . 'nexus_cloud_templates';
		
		$wpdb->replace(
			$table,
			array(
				'user_id' => $user_id,
				'template_id' => $template_id,
				'template_name' => $template_name,
				'template_type' => $template_type,
				'template_data' => $json_data,
				'cloud_url' => $result['url'],
				'cloud_key' => $filename,
				'last_synced' => current_time( 'mysql' ),
				'sync_status' => 'synced',
				'file_size' => $file_size,
				'checksum' => $checksum,
				'created_at' => current_time( 'mysql' ),
				'updated_at' => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s' )
		);
		
		$this->log_sync( $template_id, 'upload', 'success', 'Template uploaded successfully', $file_size, $duration_ms );
		
		return array(
			'success' => true,
			'template_id' => $template_id,
			'url' => $result['url'],
			'size' => $file_size,
			'duration_ms' => round( $duration_ms, 2 ),
		);
	}
	
	/**
	 * Download template from cloud
	 * 
	 * @param string $template_id Template ID
	 * @return array|WP_Error Template data
	 */
	public function download_template( $template_id ) {
		// Check database first
		global $wpdb;
		$table = $wpdb->prefix . 'nexus_cloud_templates';
		
		$record = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM {$table} WHERE template_id = %s AND user_id = %d",
			$template_id,
			get_current_user_id()
		), ARRAY_A );
		
		if ( ! $record ) {
			return new WP_Error( 'template_not_found', __( 'Template not found', 'nexus' ) );
		}
		
		$start_time = microtime( true );
		
		// Download from cloud
		if ( ! $this->cloud_storage ) {
			return new WP_Error( 'no_cloud_storage', __( 'Cloud storage not configured', 'nexus' ) );
		}
		
		$result = $this->cloud_storage->download( $record['cloud_key'] );
		
		if ( is_wp_error( $result ) ) {
			$this->log_sync( $template_id, 'download', 'failed', $result->get_error_message() );
			return $result;
		}
		
		$duration_ms = ( microtime( true ) - $start_time ) * 1000;
		
		// Decode JSON
		$template_data = json_decode( $result, true );
		
		if ( ! $template_data ) {
			return new WP_Error( 'invalid_json', __( 'Invalid template data', 'nexus' ) );
		}
		
		$this->log_sync( $template_id, 'download', 'success', 'Template downloaded successfully', strlen( $result ), $duration_ms );
		
		return array(
			'success' => true,
			'template_id' => $template_id,
			'template_name' => $record['template_name'],
			'template_type' => $record['template_type'],
			'template_data' => $template_data,
			'last_synced' => $record['last_synced'],
			'duration_ms' => round( $duration_ms, 2 ),
		);
	}
	
	/**
	 * Delete template from cloud
	 * 
	 * @param string $template_id Template ID
	 * @return bool|WP_Error True on success
	 */
	public function delete_template( $template_id ) {
		// Get record
		global $wpdb;
		$table = $wpdb->prefix . 'nexus_cloud_templates';
		
		$record = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM {$table} WHERE template_id = %s AND user_id = %d",
			$template_id,
			get_current_user_id()
		), ARRAY_A );
		
		if ( ! $record ) {
			return new WP_Error( 'template_not_found', __( 'Template not found', 'nexus' ) );
		}
		
		// Delete from cloud
		if ( $this->cloud_storage && ! empty( $record['cloud_key'] ) ) {
			$result = $this->cloud_storage->delete( $record['cloud_key'] );
			
			if ( is_wp_error( $result ) ) {
				$this->log_sync( $template_id, 'delete', 'failed', $result->get_error_message() );
				return $result;
			}
		}
		
		// Delete from database
		$wpdb->delete(
			$table,
			array(
				'template_id' => $template_id,
				'user_id' => get_current_user_id(),
			),
			array( '%s', '%d' )
		);
		
		$this->log_sync( $template_id, 'delete', 'success', 'Template deleted successfully' );
		
		return true;
	}
	
	/**
	 * List user's cloud templates
	 * 
	 * @param array $args Query arguments
	 * @return array Templates
	 */
	public function list_templates( $args = array() ) {
		$defaults = array(
			'user_id' => get_current_user_id(),
			'template_type' => null,
			'sync_status' => 'synced',
			'orderby' => 'updated_at',
			'order' => 'DESC',
			'limit' => 100,
			'offset' => 0,
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		global $wpdb;
		$table = $wpdb->prefix . 'nexus_cloud_templates';
		
		$where = array( 'user_id = %d' );
		$values = array( $args['user_id'] );
		
		if ( $args['template_type'] ) {
			$where[] = 'template_type = %s';
			$values[] = $args['template_type'];
		}
		
		if ( $args['sync_status'] ) {
			$where[] = 'sync_status = %s';
			$values[] = $args['sync_status'];
		}
		
		$where_clause = implode( ' AND ', $where );
		
		$orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );
		
		$query = $wpdb->prepare(
			"SELECT * FROM {$table} WHERE {$where_clause} ORDER BY {$orderby} LIMIT %d OFFSET %d",
			array_merge( $values, array( $args['limit'], $args['offset'] ) )
		);
		
		$templates = $wpdb->get_results( $query, ARRAY_A );
		
		// Get total count
		$count_query = $wpdb->prepare(
			"SELECT COUNT(*) FROM {$table} WHERE {$where_clause}",
			$values
		);
		$total = (int) $wpdb->get_var( $count_query );
		
		return array(
			'templates' => $templates,
			'total' => $total,
			'limit' => $this->get_user_limit(),
			'used' => $this->get_user_template_count( $args['user_id'] ),
		);
	}
	
	/**
	 * Auto-sync template on save
	 */
	public function auto_sync_template( $template_id, $template_data ) {
		if ( ! $this->can_sync() ) {
			return;
		}
		
		$this->upload_template(
			$template_id,
			$template_data['name'] ?? 'Untitled Template',
			$template_data['type'] ?? 'page',
			$template_data
		);
	}
	
	/**
	 * Auto-delete template from cloud
	 */
	public function auto_delete_template( $template_id ) {
		$this->delete_template( $template_id );
	}
	
	/**
	 * Cron: Sync all modified templates
	 */
	public function cron_sync_all() {
		global $wpdb;
		$table = $wpdb->prefix . 'nexus_cloud_templates';
		
		// Find templates that need syncing
		$templates = $wpdb->get_results(
			"SELECT * FROM {$table} 
			WHERE sync_status = 'pending' 
			OR (sync_status = 'synced' AND updated_at > last_synced)
			LIMIT 50",
			ARRAY_A
		);
		
		foreach ( $templates as $template ) {
			$template_data = maybe_unserialize( $template['template_data'] );
			
			$this->upload_template(
				$template['template_id'],
				$template['template_name'],
				$template['template_type'],
				$template_data
			);
		}
	}
	
	/**
	 * Log sync activity
	 */
	private function log_sync( $template_id, $action, $status, $message = '', $bytes = 0, $duration_ms = 0 ) {
		global $wpdb;
		$table = $wpdb->prefix . 'nexus_sync_logs';
		
		$wpdb->insert(
			$table,
			array(
				'template_id' => $template_id,
				'action' => $action,
				'status' => $status,
				'message' => $message,
				'bytes_transferred' => $bytes,
				'duration_ms' => $duration_ms,
				'created_at' => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%s', '%d', '%d', '%s' )
		);
		
		// Keep only last 1000 logs per template
		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$table} 
			WHERE template_id = %s 
			AND id NOT IN (
				SELECT id FROM (
					SELECT id FROM {$table} 
					WHERE template_id = %s 
					ORDER BY created_at DESC 
					LIMIT 1000
				) x
			)",
			$template_id,
			$template_id
		) );
	}
	
	/**
	 * AJAX: Sync template
	 */
	public function ajax_sync_template() {
		check_ajax_referer( 'nexus_cloud_sync', 'nonce' );
		
		$template_id = sanitize_text_field( $_POST['template_id'] );
		$template_name = sanitize_text_field( $_POST['template_name'] );
		$template_type = sanitize_text_field( $_POST['template_type'] );
		$template_data = json_decode( stripslashes( $_POST['template_data'] ), true );
		
		$result = $this->upload_template( $template_id, $template_name, $template_type, $template_data );
		
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array(
				'message' => $result->get_error_message(),
			) );
		}
		
		wp_send_json_success( $result );
	}
	
	/**
	 * AJAX: Download template
	 */
	public function ajax_download_template() {
		check_ajax_referer( 'nexus_cloud_sync', 'nonce' );
		
		$template_id = sanitize_text_field( $_POST['template_id'] );
		
		$result = $this->download_template( $template_id );
		
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array(
				'message' => $result->get_error_message(),
			) );
		}
		
		wp_send_json_success( $result );
	}
	
	/**
	 * AJAX: Delete template
	 */
	public function ajax_delete_template() {
		check_ajax_referer( 'nexus_cloud_sync', 'nonce' );
		
		$template_id = sanitize_text_field( $_POST['template_id'] );
		
		$result = $this->delete_template( $template_id );
		
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array(
				'message' => $result->get_error_message(),
			) );
		}
		
		wp_send_json_success( array(
			'message' => __( 'Template deleted successfully', 'nexus' ),
		) );
	}
	
	/**
	 * AJAX: List templates
	 */
	public function ajax_list_templates() {
		check_ajax_referer( 'nexus_cloud_sync', 'nonce' );
		
		$args = array(
			'template_type' => isset( $_POST['template_type'] ) ? sanitize_text_field( $_POST['template_type'] ) : null,
			'limit' => isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 100,
			'offset' => isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0,
		);
		
		$result = $this->list_templates( $args );
		
		wp_send_json_success( $result );
	}
}

// Initialize
Nexus_Template_Cloud_Sync::instance();
