<?php
/**
 * Nexus Cloud Storage - DigitalOcean Spaces Integration
 * 
 * Handles cloud template storage and synchronization
 * 
 * @package Nexus_Pro
 * @since 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Nexus_Cloud_Storage {
	
	/**
	 * Singleton instance
	 */
	private static $instance = null;
	
	/**
	 * DigitalOcean Spaces credentials
	 * REPLACE THESE WITH YOUR REAL CREDENTIALS
	 */
	private $spaces_key = 'YOUR_SPACES_KEY'; // Replace with actual key
	private $spaces_secret = 'YOUR_SPACES_SECRET'; // Replace with actual secret
	private $spaces_region = 'sgp1'; // Singapore region (change as needed: nyc3, sfo3, ams3, sgp1)
	private $spaces_bucket = 'nexus-templates'; // Your bucket name
	private $spaces_endpoint = 'https://sgp1.digitaloceanspaces.com'; // Auto-generated from region
	
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
		// Load credentials from options (if saved)
		$this->load_credentials();
		
		// Set endpoint based on region
		$this->spaces_endpoint = "https://{$this->spaces_region}.digitaloceanspaces.com";
		
		// Add admin settings page
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}
	
	/**
	 * Load credentials from database
	 */
	private function load_credentials() {
		$stored_key = get_option( 'nexus_spaces_key' );
		$stored_secret = get_option( 'nexus_spaces_secret' );
		$stored_region = get_option( 'nexus_spaces_region' );
		$stored_bucket = get_option( 'nexus_spaces_bucket' );
		
		if ( $stored_key ) {
			$this->spaces_key = $stored_key;
		}
		if ( $stored_secret ) {
			$this->spaces_secret = $stored_secret;
		}
		if ( $stored_region ) {
			$this->spaces_region = $stored_region;
			$this->spaces_endpoint = "https://{$stored_region}.digitaloceanspaces.com";
		}
		if ( $stored_bucket ) {
			$this->spaces_bucket = $stored_bucket;
		}
	}
	
	/**
	 * Check if credentials are configured
	 */
	public function has_credentials() {
		return ! empty( $this->spaces_key ) && 
		       ! empty( $this->spaces_secret ) && 
		       $this->spaces_key !== 'YOUR_SPACES_KEY';
	}
	
	/**
	 * Upload file to DigitalOcean Spaces
	 * 
	 * @param string $file_path Local file path
	 * @param string $remote_path Remote path in bucket
	 * @return array|WP_Error Upload result
	 */
	public function upload( $file_path, $remote_path ) {
		if ( ! $this->has_credentials() ) {
			return new WP_Error( 'no_credentials', __( 'DigitalOcean Spaces credentials not configured', 'nexus' ) );
		}
		
		if ( ! file_exists( $file_path ) ) {
			return new WP_Error( 'file_not_found', __( 'File not found', 'nexus' ) );
		}
		
		// Read file content
		$file_content = file_get_contents( $file_path );
		$file_size = filesize( $file_path );
		$content_type = $this->get_content_type( $file_path );
		
		// Prepare request
		$date = gmdate( 'D, d M Y H:i:s \G\M\T' );
		$string_to_sign = "PUT\n\n{$content_type}\n{$date}\n/{$this->spaces_bucket}/{$remote_path}";
		$signature = base64_encode( hash_hmac( 'sha1', $string_to_sign, $this->spaces_secret, true ) );
		
		$url = "{$this->spaces_endpoint}/{$this->spaces_bucket}/{$remote_path}";
		
		// Upload using wp_remote_request
		$response = wp_remote_request( $url, array(
			'method' => 'PUT',
			'headers' => array(
				'Host' => "{$this->spaces_region}.digitaloceanspaces.com",
				'Date' => $date,
				'Content-Type' => $content_type,
				'Content-Length' => $file_size,
				'Authorization' => "AWS {$this->spaces_key}:{$signature}",
				'x-amz-acl' => 'private', // Private by default
			),
			'body' => $file_content,
			'timeout' => 60,
		) );
		
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		
		$response_code = wp_remote_retrieve_response_code( $response );
		
		if ( $response_code !== 200 ) {
			return new WP_Error( 
				'upload_failed', 
				sprintf( __( 'Upload failed with status: %d', 'nexus' ), $response_code ),
				array( 'response' => wp_remote_retrieve_body( $response ) )
			);
		}
		
		// Log successful upload
		$this->log( 'upload', "Uploaded: {$remote_path} ({$file_size} bytes)" );
		
		return array(
			'success' => true,
			'url' => $url,
			'size' => $file_size,
			'remote_path' => $remote_path,
		);
	}
	
	/**
	 * Download file from DigitalOcean Spaces
	 * 
	 * @param string $remote_path Remote path in bucket
	 * @param string $local_path Local destination path
	 * @return bool|WP_Error True on success
	 */
	public function download( $remote_path, $local_path ) {
		if ( ! $this->has_credentials() ) {
			return new WP_Error( 'no_credentials', __( 'DigitalOcean Spaces credentials not configured', 'nexus' ) );
		}
		
		// Prepare request
		$date = gmdate( 'D, d M Y H:i:s \G\M\T' );
		$string_to_sign = "GET\n\n\n{$date}\n/{$this->spaces_bucket}/{$remote_path}";
		$signature = base64_encode( hash_hmac( 'sha1', $string_to_sign, $this->spaces_secret, true ) );
		
		$url = "{$this->spaces_endpoint}/{$this->spaces_bucket}/{$remote_path}";
		
		// Download using wp_remote_get
		$response = wp_remote_get( $url, array(
			'headers' => array(
				'Host' => "{$this->spaces_region}.digitaloceanspaces.com",
				'Date' => $date,
				'Authorization' => "AWS {$this->spaces_key}:{$signature}",
			),
			'timeout' => 60,
		) );
		
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		
		$response_code = wp_remote_retrieve_response_code( $response );
		
		if ( $response_code !== 200 ) {
			return new WP_Error( 
				'download_failed', 
				sprintf( __( 'Download failed with status: %d', 'nexus' ), $response_code )
			);
		}
		
		// Save file locally
		$file_content = wp_remote_retrieve_body( $response );
		
		// Create directory if needed
		$dir = dirname( $local_path );
		if ( ! file_exists( $dir ) ) {
			wp_mkdir_p( $dir );
		}
		
		$result = file_put_contents( $local_path, $file_content );
		
		if ( false === $result ) {
			return new WP_Error( 'save_failed', __( 'Failed to save file locally', 'nexus' ) );
		}
		
		$this->log( 'download', "Downloaded: {$remote_path} to {$local_path}" );
		
		return true;
	}
	
	/**
	 * Delete file from DigitalOcean Spaces
	 * 
	 * @param string $remote_path Remote path in bucket
	 * @return bool|WP_Error True on success
	 */
	public function delete( $remote_path ) {
		if ( ! $this->has_credentials() ) {
			return new WP_Error( 'no_credentials', __( 'DigitalOcean Spaces credentials not configured', 'nexus' ) );
		}
		
		// Prepare request
		$date = gmdate( 'D, d M Y H:i:s \G\M\T' );
		$string_to_sign = "DELETE\n\n\n{$date}\n/{$this->spaces_bucket}/{$remote_path}";
		$signature = base64_encode( hash_hmac( 'sha1', $string_to_sign, $this->spaces_secret, true ) );
		
		$url = "{$this->spaces_endpoint}/{$this->spaces_bucket}/{$remote_path}";
		
		// Delete using wp_remote_request
		$response = wp_remote_request( $url, array(
			'method' => 'DELETE',
			'headers' => array(
				'Host' => "{$this->spaces_region}.digitaloceanspaces.com",
				'Date' => $date,
				'Authorization' => "AWS {$this->spaces_key}:{$signature}",
			),
			'timeout' => 30,
		) );
		
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		
		$response_code = wp_remote_retrieve_response_code( $response );
		
		if ( $response_code !== 204 && $response_code !== 200 ) {
			return new WP_Error( 
				'delete_failed', 
				sprintf( __( 'Delete failed with status: %d', 'nexus' ), $response_code )
			);
		}
		
		$this->log( 'delete', "Deleted: {$remote_path}" );
		
		return true;
	}
	
	/**
	 * List files in bucket
	 * 
	 * @param string $prefix Prefix filter (folder path)
	 * @return array|WP_Error List of files
	 */
	public function list_files( $prefix = '' ) {
		if ( ! $this->has_credentials() ) {
			return new WP_Error( 'no_credentials', __( 'DigitalOcean Spaces credentials not configured', 'nexus' ) );
		}
		
		// Prepare request
		$date = gmdate( 'D, d M Y H:i:s \G\M\T' );
		$resource = "/{$this->spaces_bucket}/";
		if ( $prefix ) {
			$resource .= "?prefix={$prefix}";
		}
		$string_to_sign = "GET\n\n\n{$date}\n{$resource}";
		$signature = base64_encode( hash_hmac( 'sha1', $string_to_sign, $this->spaces_secret, true ) );
		
		$url = "{$this->spaces_endpoint}/{$this->spaces_bucket}/";
		if ( $prefix ) {
			$url .= "?prefix={$prefix}";
		}
		
		// List using wp_remote_get
		$response = wp_remote_get( $url, array(
			'headers' => array(
				'Host' => "{$this->spaces_region}.digitaloceanspaces.com",
				'Date' => $date,
				'Authorization' => "AWS {$this->spaces_key}:{$signature}",
			),
			'timeout' => 30,
		) );
		
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		
		$response_code = wp_remote_retrieve_response_code( $response );
		
		if ( $response_code !== 200 ) {
			return new WP_Error( 
				'list_failed', 
				sprintf( __( 'List failed with status: %d', 'nexus' ), $response_code )
			);
		}
		
		// Parse XML response
		$xml_body = wp_remote_retrieve_body( $response );
		$xml = simplexml_load_string( $xml_body );
		
		$files = array();
		
		if ( $xml && isset( $xml->Contents ) ) {
			foreach ( $xml->Contents as $content ) {
				$files[] = array(
					'key' => (string) $content->Key,
					'size' => (int) $content->Size,
					'last_modified' => (string) $content->LastModified,
				);
			}
		}
		
		return $files;
	}
	
	/**
	 * Test connection
	 * 
	 * @return bool|WP_Error True if connection successful
	 */
	public function test_connection() {
		if ( ! $this->has_credentials() ) {
			return new WP_Error( 'no_credentials', __( 'Credentials not configured', 'nexus' ) );
		}
		
		// Try to list bucket (should work if credentials are correct)
		$result = $this->list_files();
		
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		
		return true;
	}
	
	/**
	 * Get content type from file extension
	 */
	private function get_content_type( $file_path ) {
		$ext = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
		
		$types = array(
			'json' => 'application/json',
			'zip' => 'application/zip',
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png' => 'image/png',
			'gif' => 'image/gif',
			'pdf' => 'application/pdf',
			'txt' => 'text/plain',
		);
		
		return isset( $types[ $ext ] ) ? $types[ $ext ] : 'application/octet-stream';
	}
	
	/**
	 * Log activity
	 */
	private function log( $action, $message ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( "[Nexus Cloud Storage] {$action}: {$message}" );
		}
		
		// Store in option for admin log viewer
		$logs = get_option( 'nexus_cloud_logs', array() );
		$logs[] = array(
			'time' => current_time( 'mysql' ),
			'action' => $action,
			'message' => $message,
		);
		
		// Keep only last 100 logs
		if ( count( $logs ) > 100 ) {
			$logs = array_slice( $logs, -100 );
		}
		
		update_option( 'nexus_cloud_logs', $logs );
	}
	
	/**
	 * Add settings page
	 */
	public function add_settings_page() {
		add_submenu_page(
			'nexus-pro',
			__( 'Cloud Storage Settings', 'nexus' ),
			__( 'Cloud Storage', 'nexus' ),
			'manage_options',
			'nexus-cloud-storage',
			array( $this, 'render_settings_page' )
		);
	}
	
	/**
	 * Register settings
	 */
	public function register_settings() {
		register_setting( 'nexus_cloud_storage', 'nexus_spaces_key' );
		register_setting( 'nexus_cloud_storage', 'nexus_spaces_secret' );
		register_setting( 'nexus_cloud_storage', 'nexus_spaces_region' );
		register_setting( 'nexus_cloud_storage', 'nexus_spaces_bucket' );
	}
	
	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		// Test connection if requested
		$test_result = null;
		if ( isset( $_POST['test_connection'] ) && check_admin_referer( 'nexus_cloud_test' ) ) {
			$test_result = $this->test_connection();
		}
		
		?>
		<div class="wrap">
			<h1><?php _e( 'Cloud Storage Settings', 'nexus' ); ?></h1>
			
			<p><?php _e( 'Configure DigitalOcean Spaces for cloud template storage.', 'nexus' ); ?></p>
			
			<?php if ( $test_result !== null ) : ?>
				<?php if ( is_wp_error( $test_result ) ) : ?>
					<div class="notice notice-error">
						<p><strong><?php _e( 'Connection Failed:', 'nexus' ); ?></strong> <?php echo esc_html( $test_result->get_error_message() ); ?></p>
					</div>
				<?php else : ?>
					<div class="notice notice-success">
						<p><strong><?php _e( 'Connection Successful!', 'nexus' ); ?></strong> <?php _e( 'Cloud storage is properly configured.', 'nexus' ); ?></p>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			
			<form method="post" action="options.php">
				<?php settings_fields( 'nexus_cloud_storage' ); ?>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="nexus_spaces_key"><?php _e( 'Spaces Access Key', 'nexus' ); ?></label>
						</th>
						<td>
							<input type="text" 
							       id="nexus_spaces_key" 
							       name="nexus_spaces_key" 
							       value="<?php echo esc_attr( get_option( 'nexus_spaces_key', '' ) ); ?>" 
							       class="regular-text" />
							<p class="description">
								<?php _e( 'Your DigitalOcean Spaces access key ID', 'nexus' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="nexus_spaces_secret"><?php _e( 'Spaces Secret Key', 'nexus' ); ?></label>
						</th>
						<td>
							<input type="password" 
							       id="nexus_spaces_secret" 
							       name="nexus_spaces_secret" 
							       value="<?php echo esc_attr( get_option( 'nexus_spaces_secret', '' ) ); ?>" 
							       class="regular-text" />
							<p class="description">
								<?php _e( 'Your DigitalOcean Spaces secret access key', 'nexus' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="nexus_spaces_region"><?php _e( 'Spaces Region', 'nexus' ); ?></label>
						</th>
						<td>
							<select id="nexus_spaces_region" name="nexus_spaces_region">
								<option value="nyc3" <?php selected( get_option( 'nexus_spaces_region' ), 'nyc3' ); ?>>NYC3 (New York)</option>
								<option value="sfo3" <?php selected( get_option( 'nexus_spaces_region' ), 'sfo3' ); ?>>SFO3 (San Francisco)</option>
								<option value="ams3" <?php selected( get_option( 'nexus_spaces_region' ), 'ams3' ); ?>>AMS3 (Amsterdam)</option>
								<option value="sgp1" <?php selected( get_option( 'nexus_spaces_region', 'sgp1' ), 'sgp1' ); ?>>SGP1 (Singapore)</option>
								<option value="fra1" <?php selected( get_option( 'nexus_spaces_region' ), 'fra1' ); ?>>FRA1 (Frankfurt)</option>
							</select>
							<p class="description">
								<?php _e( 'Choose the region closest to your users for best performance', 'nexus' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="nexus_spaces_bucket"><?php _e( 'Bucket Name', 'nexus' ); ?></label>
						</th>
						<td>
							<input type="text" 
							       id="nexus_spaces_bucket" 
							       name="nexus_spaces_bucket" 
							       value="<?php echo esc_attr( get_option( 'nexus_spaces_bucket', 'nexus-templates' ) ); ?>" 
							       class="regular-text" />
							<p class="description">
								<?php _e( 'The name of your Spaces bucket (create one in DigitalOcean first)', 'nexus' ); ?>
							</p>
						</td>
					</tr>
				</table>
				
				<?php submit_button(); ?>
			</form>
			
			<hr />
			
			<h2><?php _e( 'Test Connection', 'nexus' ); ?></h2>
			<form method="post">
				<?php wp_nonce_field( 'nexus_cloud_test' ); ?>
				<p>
					<button type="submit" name="test_connection" class="button button-secondary">
						<?php _e( 'Test Connection', 'nexus' ); ?>
					</button>
				</p>
			</form>
			
			<hr />
			
			<h2><?php _e( 'Setup Instructions', 'nexus' ); ?></h2>
			<ol>
				<li><?php _e( 'Log in to DigitalOcean and go to Spaces', 'nexus' ); ?></li>
				<li><?php _e( 'Create a new Space (or use existing)', 'nexus' ); ?></li>
				<li><?php _e( 'Go to API → Spaces Keys', 'nexus' ); ?></li>
				<li><?php _e( 'Generate a new key pair', 'nexus' ); ?></li>
				<li><?php _e( 'Copy the Access Key and Secret Key to the form above', 'nexus' ); ?></li>
				<li><?php _e( 'Save settings and test connection', 'nexus' ); ?></li>
			</ol>
			
			<p>
				<a href="https://docs.digitalocean.com/products/spaces/how-to/manage-access/" target="_blank" class="button">
					<?php _e( 'View DigitalOcean Spaces Documentation', 'nexus' ); ?>
				</a>
			</p>
		</div>
		<?php
	}
}

// Initialize
Nexus_Cloud_Storage::instance();
