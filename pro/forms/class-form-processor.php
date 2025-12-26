<?php
/**
 * Form Processor
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Form Processor Class
 */
class Nexus_Form_Processor {

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
		add_action( 'wp_ajax_nexus_submit_form', array( $this, 'submit_form' ) );
		add_action( 'wp_ajax_nopriv_nexus_submit_form', array( $this, 'submit_form' ) );
	}

	/**
	 * Submit Form
	 */
	public function submit_form() {
		$form_id = isset( $_POST['form_id'] ) ? intval( $_POST['form_id'] ) : 0;

		if ( ! $form_id ) {
			wp_send_json_error( __( 'Invalid form', 'nexus' ) );
		}

		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'nexus_form_submit_' . $form_id ) ) {
			wp_send_json_error( __( 'Security check failed', 'nexus' ) );
		}

		// Get form fields
		$fields = get_post_meta( $form_id, '_nexus_form_fields', true );
		if ( ! is_array( $fields ) || empty( $fields ) ) {
			wp_send_json_error( __( 'Form configuration error', 'nexus' ) );
		}

		// Validate and sanitize
		$data   = isset( $_POST['data'] ) ? $_POST['data'] : array();
		$errors = array();

		foreach ( $fields as $field ) {
			$name     = $field['name'];
			$required = isset( $field['required'] ) && $field['required'];
			$value    = isset( $data[ $name ] ) ? $data[ $name ] : '';

			// Required validation
			if ( $required && empty( $value ) ) {
				$errors[ $name ] = sprintf( __( '%s is required', 'nexus' ), $field['label'] );
				continue;
			}

			// Type-specific validation
			switch ( $field['type'] ) {
				case 'email':
					if ( ! empty( $value ) && ! is_email( $value ) ) {
						$errors[ $name ] = __( 'Invalid email address', 'nexus' );
					}
					break;
				case 'tel':
					if ( ! empty( $value ) && ! preg_match( '/^[0-9\-\+\(\)\s]+$/', $value ) ) {
						$errors[ $name ] = __( 'Invalid phone number', 'nexus' );
					}
					break;
			}
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Please correct the errors', 'nexus' ),
					'errors'  => $errors,
				)
			);
		}

		// Save submission
		$submission_id = $this->save_submission( $form_id, $data );

		// Send email
		$this->send_email( $form_id, $data );

		// Get success message
		$success_message = get_post_meta( $form_id, '_nexus_form_success_message', true );
		if ( ! $success_message ) {
			$success_message = __( 'Thank you! Your submission has been received.', 'nexus' );
		}

		wp_send_json_success(
			array(
				'message'       => $success_message,
				'submission_id' => $submission_id,
			)
		);
	}

	/**
	 * Save Submission
	 */
	private function save_submission( $form_id, $data ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'nexus_form_submissions';

		// Create table if not exists
		$this->maybe_create_table();

		// Insert submission
		$wpdb->insert(
			$table_name,
			array(
				'form_id'    => $form_id,
				'data'       => wp_json_encode( $data ),
				'ip_address' => $this->get_ip_address(),
				'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '',
				'created_at' => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%s', '%s', '%s' )
		);

		return $wpdb->insert_id;
	}

	/**
	 * Send Email
	 */
	private function send_email( $form_id, $data ) {
		$email_to      = get_post_meta( $form_id, '_nexus_form_email_to', true );
		$email_subject = get_post_meta( $form_id, '_nexus_form_email_subject', true );

		if ( ! $email_to ) {
			$email_to = get_option( 'admin_email' );
		}

		if ( ! $email_subject ) {
			$email_subject = __( 'New Form Submission', 'nexus' );
		}

		// Build message
		$message  = sprintf( __( 'New form submission from %s', 'nexus' ), get_bloginfo( 'name' ) ) . "\n\n";
		$message .= "---\n\n";

		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}
			$message .= ucwords( str_replace( '_', ' ', $key ) ) . ": " . $value . "\n";
		}

		$message .= "\n---\n\n";
		$message .= __( 'Submitted from:', 'nexus' ) . ' ' . home_url();

		// Send email
		wp_mail( $email_to, $email_subject, $message );
	}

	/**
	 * Get IP Address
	 */
	private function get_ip_address() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			return sanitize_text_field( $_SERVER['HTTP_CLIENT_IP'] );
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			return sanitize_text_field( $_SERVER['HTTP_X_FORWARDED_FOR'] );
		} else {
			return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '';
		}
	}

	/**
	 * Maybe Create Table
	 */
	private function maybe_create_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'nexus_form_submissions';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			form_id bigint(20) NOT NULL,
			data longtext NOT NULL,
			ip_address varchar(100) DEFAULT '',
			user_agent varchar(255) DEFAULT '',
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY form_id (form_id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}
