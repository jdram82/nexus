<?php
/**
 * Portal Dashboard
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Portal Dashboard Class
 */
class Nexus_Portal_Dashboard {

	/**
	 * Instance
	 */
	private static $instance;

	/**
	 * Get Instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'wp_ajax_portal_get_stats', array( $this, 'get_stats' ) );
		add_action( 'wp_ajax_portal_get_activity', array( $this, 'get_activity' ) );
	}

	/**
	 * Get Stats
	 */
	public function get_stats() {
		check_ajax_referer( 'nexus-portal', 'nonce' );

		$current_user = wp_get_current_user();

		$stats = array(
			'projects'  => $this->count_user_projects( $current_user->ID ),
			'downloads' => $this->count_user_downloads( $current_user->ID ),
			'tickets'   => $this->count_user_tickets( $current_user->ID ),
		);

		wp_send_json_success( $stats );
	}

	/**
	 * Get Activity
	 */
	public function get_activity() {
		check_ajax_referer( 'nexus-portal', 'nonce' );

		$current_user = wp_get_current_user();
		$activity = $this->get_user_activity( $current_user->ID );

		wp_send_json_success( $activity );
	}

	/**
	 * Count User Projects
	 */
	private function count_user_projects( $user_id ) {
		$count = get_posts(
			array(
				'post_type'      => 'nexus_project',
				'meta_key'       => '_client_id',
				'meta_value'     => $user_id,
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);
		return count( $count );
	}

	/**
	 * Count User Downloads
	 */
	private function count_user_downloads( $user_id ) {
		return get_user_meta( $user_id, '_download_count', true ) ?: 0;
	}

	/**
	 * Count User Tickets
	 */
	private function count_user_tickets( $user_id ) {
		return get_user_meta( $user_id, '_ticket_count', true ) ?: 0;
	}

	/**
	 * Get User Activity
	 */
	private function get_user_activity( $user_id ) {
		return array();
	}
}
