<?php
/**
 * Nexus Theme Updater
 * 
 * Enables automatic theme updates from GitHub repository
 * 
 * @package Nexus
 * @since 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Nexus_Theme_Updater {
	
	/**
	 * GitHub repository owner
	 */
	private $github_user = 'jdram82';
	
	/**
	 * GitHub repository name
	 */
	private $github_repo = 'nexus';
	
	/**
	 * Theme slug
	 */
	private $theme_slug = 'nexus-theme';
	
	/**
	 * Current theme version
	 */
	private $version;
	
	/**
	 * GitHub API URL
	 */
	private $github_api_url;
	
	/**
	 * Update transient name
	 */
	private $transient_name = 'nexus_theme_update_check';
	
	/**
	 * Singleton instance
	 */
	private static $instance = null;
	
	/**
	 * Get instance
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
		$theme = wp_get_theme( $this->theme_slug );
		$this->version = $theme->get( 'Version' );
		$this->github_api_url = "https://api.github.com/repos/{$this->github_user}/{$this->github_repo}/releases/latest";
		
		// Add hooks
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update' ) );
		add_filter( 'upgrader_source_selection', array( $this, 'fix_theme_folder_name' ), 10, 3 );
		add_action( 'admin_notices', array( $this, 'show_update_notice' ) );
		add_action( 'admin_init', array( $this, 'manual_update_check' ) );
		add_action( 'wp_ajax_nexus_check_update', array( $this, 'ajax_check_update' ) );
		add_action( 'upgrader_process_complete', array( $this, 'clear_cache_after_update' ), 10, 2 );
		
		// Add update button in Appearance → Themes
		add_action( 'admin_menu', array( $this, 'add_update_page' ) );
	}
	
	/**
	 * Check for updates
	 */
	public function check_for_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}
		
		// Check if we have cached update info
		$update_cache = get_transient( $this->transient_name );
		
		if ( false === $update_cache ) {
			// Fetch latest release from GitHub
			$release = $this->get_latest_release();
			
			if ( is_wp_error( $release ) ) {
				// Cache error for 1 hour to avoid repeated failed requests
				set_transient( $this->transient_name, array( 'error' => true ), HOUR_IN_SECONDS );
				return $transient;
			}
			
			$update_cache = array(
				'version' => $release['tag_name'],
				'url' => $release['html_url'],
				'package' => $release['zipball_url'],
				'requires' => '6.0',
				'requires_php' => '7.4',
			);
			
			// Cache for 12 hours
			set_transient( $this->transient_name, $update_cache, 12 * HOUR_IN_SECONDS );
		}
		
		// Skip if cached error
		if ( isset( $update_cache['error'] ) ) {
			return $transient;
		}
		
		// Compare versions
		$remote_version = ltrim( $update_cache['version'], 'v' );
		
		if ( version_compare( $this->version, $remote_version, '<' ) ) {
			// Update available
			$transient->response[ $this->theme_slug ] = array(
				'theme' => $this->theme_slug,
				'new_version' => $remote_version,
				'url' => $update_cache['url'],
				'package' => $update_cache['package'],
				'requires' => $update_cache['requires'],
				'requires_php' => $update_cache['requires_php'],
			);
		}
		
		return $transient;
	}
	
	/**
	 * Get latest release from GitHub
	 */
	private function get_latest_release() {
		// Prepare headers
		$headers = array(
			'Accept' => 'application/vnd.github.v3+json',
		);
		
		// Add GitHub token if available (to avoid rate limits)
		$github_token = defined( 'NEXUS_GITHUB_TOKEN' ) ? NEXUS_GITHUB_TOKEN : false;
		if ( $github_token ) {
			$headers['Authorization'] = 'token ' . $github_token;
		}
		
		$response = wp_remote_get( $this->github_api_url, array(
			'timeout' => 15,
			'headers' => $headers,
		) );
		
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		
		$response_code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		
		// Check for rate limit
		if ( 403 === $response_code ) {
			$data = json_decode( $body, true );
			if ( isset( $data['message'] ) && strpos( $data['message'], 'rate limit' ) !== false ) {
				return new WP_Error( 'rate_limit', 'GitHub API rate limit exceeded. Please wait or add NEXUS_GITHUB_TOKEN to wp-config.php.' );
			}
		}
		
		if ( 200 !== $response_code ) {
			return new WP_Error( 'github_error', sprintf( 'GitHub API returned %d', $response_code ) );
		}
		
		$release = json_decode( $body, true );
		
		if ( empty( $release['tag_name'] ) ) {
			return new WP_Error( 'invalid_response', 'Invalid GitHub response: ' . substr( $body, 0, 200 ) );
		}
		
		return $release;
	}
	
	/**
	 * Fix theme folder name after update
	 * 
	 * GitHub downloads create folder like "jdram82-nexus-abc123"
	 * We need to rename it to "nexus-theme"
	 */
	public function fix_theme_folder_name( $source, $remote_source, $upgrader ) {
		global $wp_filesystem;
		
		// Only run for theme updates
		if ( ! isset( $upgrader->skin->theme_info ) && ! isset( $upgrader->skin->theme ) ) {
			return $source;
		}
		
		// Check if this is updating our theme
		$updating_theme = isset( $upgrader->skin->theme ) ? $upgrader->skin->theme : '';
		if ( ! empty( $updating_theme ) && $this->theme_slug !== $updating_theme ) {
			return $source;
		}
		
		// Get the actual folder name from the extracted ZIP
		$source_name = basename( $source );
		
		// If it's already named correctly, return
		if ( $this->theme_slug === $source_name ) {
			return $source;
		}
		
		// GitHub creates folders like "nexus-abc1234" or "jdram82-nexus-abc1234"
		// We need to rename it to "nexus-theme"
		$new_source = dirname( $source ) . '/' . $this->theme_slug;
		
		// Move the folder to correct name
		if ( $wp_filesystem->move( $source, $new_source, true ) ) {
			return $new_source;
		}
		
		return new WP_Error( 'rename_failed', __( 'Could not rename theme folder. Please reinstall manually.', 'nexus' ) );
	}
	
	/**
	 * Show update notice in admin
	 */
	public function show_update_notice() {
		$screen = get_current_screen();
		
		// Only show on dashboard and themes page
		if ( ! in_array( $screen->id, array( 'dashboard', 'themes' ) ) ) {
			return;
		}
		
		// Check for updates
		$update_cache = get_transient( $this->transient_name );
		
		if ( empty( $update_cache ) || isset( $update_cache['error'] ) ) {
			return;
		}
		
		$remote_version = ltrim( $update_cache['version'], 'v' );
		
		if ( version_compare( $this->version, $remote_version, '<' ) ) {
			?>
			<div class="notice notice-info is-dismissible">
				<p>
					<strong><?php _e( 'Nexus Theme Update Available!', 'nexus' ); ?></strong>
				</p>
				<p>
					<?php
					printf(
						__( 'Version %s is available. You have version %s. Update now to get the latest features and security fixes.', 'nexus' ),
						'<strong>' . esc_html( $remote_version ) . '</strong>',
						esc_html( $this->version )
					);
					?>
				</p>
				<p>
					<a href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>" class="button button-primary">
						<?php _e( 'Update Now', 'nexus' ); ?>
					</a>
					<a href="<?php echo esc_url( $update_cache['url'] ); ?>" class="button" target="_blank">
						<?php _e( 'View Release Notes', 'nexus' ); ?>
					</a>
				</p>
			</div>
			<?php
		}
	}
	
	/**
	 * Manual update check
	 */
	public function manual_update_check() {
		if ( ! isset( $_GET['nexus_check_update'] ) || ! isset( $_GET['_wpnonce'] ) ) {
			return;
		}
		
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'nexus_check_update' ) ) {
			return;
		}
		
		// Clear transient to force update check
		delete_transient( $this->transient_name );
		
		// Redirect back
		wp_safe_redirect( admin_url( 'themes.php?nexus_update_checked=1' ) );
		exit;
	}
	
	/**
	 * AJAX check for update
	 */
	public function ajax_check_update() {
		check_ajax_referer( 'nexus_update', 'nonce' );
		
		if ( ! current_user_can( 'update_themes' ) ) {
			wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
		}
		
		// Clear cache
		delete_transient( $this->transient_name );
		
		// Force check
		$release = $this->get_latest_release();
		
		if ( is_wp_error( $release ) ) {
			wp_send_json_error( array(
				'message' => $release->get_error_message(),
			) );
		}
		
		$remote_version = ltrim( $release['tag_name'], 'v' );
		$update_available = version_compare( $this->version, $remote_version, '<' );
		
		wp_send_json_success( array(
			'update_available' => $update_available,
			'current_version' => $this->version,
			'latest_version' => $remote_version,
			'release_url' => $release['html_url'],
			'download_url' => $release['zipball_url'],
			'message' => $update_available
				? sprintf( 'Update available: %s → %s', $this->version, $remote_version )
				: 'You have the latest version',
		) );
	}
	
	/**
	 * Add update page
	 */
	public function add_update_page() {
		add_theme_page(
			__( 'Nexus by Jdsan Digitel - Updates', 'nexus' ),
			__( 'Updates', 'nexus' ),
			'update_themes',
			'nexus-updates',
			array( $this, 'render_update_page' )
		);
	}
	
	/**
	 * Render update page
	 */
	public function render_update_page() {
		$update_cache = get_transient( $this->transient_name );
		$update_available = false;
		$remote_version = null;
		
		if ( ! empty( $update_cache ) && ! isset( $update_cache['error'] ) ) {
			$remote_version = ltrim( $update_cache['version'], 'v' );
			$update_available = version_compare( $this->version, $remote_version, '<' );
		}
		
		?>
		<div class="wrap">
			<h1><?php _e( 'Nexus Theme Updates', 'nexus' ); ?></h1>
			
			<div class="card">
				<h2><?php _e( 'Current Version', 'nexus' ); ?></h2>
				<p style="font-size: 24px; margin: 10px 0;">
					<strong><?php echo esc_html( $this->version ); ?></strong>
				</p>
				
				<?php if ( $update_available ) : ?>
					<div class="notice notice-warning inline">
						<p>
							<strong><?php _e( 'Update Available!', 'nexus' ); ?></strong><br>
							<?php
							printf(
								__( 'Version %s is now available.', 'nexus' ),
								'<strong>' . esc_html( $remote_version ) . '</strong>'
							);
							?>
						</p>
					</div>
					
					<p>
						<a href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>" class="button button-primary button-hero">
							<?php _e( 'Update Now', 'nexus' ); ?>
						</a>
						<a href="<?php echo esc_url( $update_cache['url'] ); ?>" class="button button-hero" target="_blank">
							<?php _e( 'View Release Notes', 'nexus' ); ?>
						</a>
					</p>
				<?php else : ?>
					<div class="notice notice-success inline">
						<p>
							<strong><?php _e( 'You have the latest version!', 'nexus' ); ?></strong>
						</p>
					</div>
				<?php endif; ?>
				
				<hr>
				
				<h3><?php _e( 'Update Settings', 'nexus' ); ?></h3>
				
				<p>
					<strong><?php _e( 'Automatic Updates:', 'nexus' ); ?></strong>
					<?php _e( 'Enabled (checks every 12 hours)', 'nexus' ); ?>
				</p>
				
				<p>
					<strong><?php _e( 'Update Source:', 'nexus' ); ?></strong>
					GitHub (<?php echo esc_html( $this->github_user . '/' . $this->github_repo ); ?>)
				</p>
				
				<p>
					<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'themes.php?nexus_check_update=1' ), 'nexus_check_update' ) ); ?>" class="button">
						<?php _e( 'Check for Updates Now', 'nexus' ); ?>
					</a>
					<button id="nexus-clear-cache" class="button">
						<?php _e( 'Clear Update Cache', 'nexus' ); ?>
					</button>
				</p>
			</div>
			
			<div class="card">
				<h2><?php _e( 'Update Information', 'nexus' ); ?></h2>
				
				<h3><?php _e( 'How Updates Work', 'nexus' ); ?></h3>
				<ol>
					<li><?php _e( 'Nexus checks GitHub for new releases every 12 hours', 'nexus' ); ?></li>
					<li><?php _e( 'When an update is available, you\'ll see a notification', 'nexus' ); ?></li>
					<li><?php _e( 'Click "Update Now" to download and install automatically', 'nexus' ); ?></li>
					<li><?php _e( 'Your settings and content are preserved during updates', 'nexus' ); ?></li>
				</ol>
				
				<h3><?php _e( 'What Gets Updated', 'nexus' ); ?></h3>
				<ul>
					<li>✅ <?php _e( 'Theme files (PHP, CSS, JS)', 'nexus' ); ?></li>
					<li>✅ <?php _e( 'New features and improvements', 'nexus' ); ?></li>
					<li>✅ <?php _e( 'Bug fixes and security patches', 'nexus' ); ?></li>
					<li>❌ <?php _e( 'Your content and settings (preserved)', 'nexus' ); ?></li>
					<li>❌ <?php _e( 'Your customizations (if in child theme)', 'nexus' ); ?></li>
				</ul>
				
				<h3><?php _e( 'Best Practices', 'nexus' ); ?></h3>
				<ul>
					<li>📋 <?php _e( 'Always backup before updating', 'nexus' ); ?></li>
					<li>🧪 <?php _e( 'Test updates on staging site first', 'nexus' ); ?></li>
					<li>👶 <?php _e( 'Use a child theme for customizations', 'nexus' ); ?></li>
					<li>📖 <?php _e( 'Read release notes before updating', 'nexus' ); ?></li>
				</ul>
			</div>
			
			<div class="card">
				<h2><?php _e( 'Manual Update (Advanced)', 'nexus' ); ?></h2>
				
				<p><?php _e( 'If automatic updates fail, you can update manually:', 'nexus' ); ?></p>
				
				<ol>
					<li>
						<?php _e( 'Download latest release:', 'nexus' ); ?>
						<a href="https://github.com/<?php echo esc_attr( $this->github_user . '/' . $this->github_repo ); ?>/releases/latest" target="_blank">
							<?php _e( 'GitHub Releases', 'nexus' ); ?>
						</a>
					</li>
					<li><?php _e( 'Delete old theme folder via FTP/SSH', 'nexus' ); ?></li>
					<li><?php _e( 'Upload new theme folder', 'nexus' ); ?></li>
					<li><?php _e( 'Activate theme in WordPress admin', 'nexus' ); ?></li>
				</ol>
			</div>
		</div>
		
		<script>
		jQuery(document).ready(function($) {
			$('#nexus-clear-cache').on('click', function(e) {
				e.preventDefault();
				
				$(this).prop('disabled', true).text('Clearing...');
				
				$.post(ajaxurl, {
					action: 'nexus_check_update',
					nonce: '<?php echo wp_create_nonce( 'nexus_update' ); ?>'
				}, function(response) {
					if (response.success) {
						location.reload();
					} else {
						alert(response.data.message);
					}
				});
			});
		});
		</script>
		<?php
	}
	
	/**
	 * Clear cache after successful update
	 */
	public function clear_cache_after_update( $upgrader_object, $options ) {
		// Check if this is a theme update
		if ( $options['action'] !== 'update' || $options['type'] !== 'theme' ) {
			return;
		}
		
		// Check if our theme was updated
		if ( isset( $options['themes'] ) && in_array( $this->theme_slug, $options['themes'] ) ) {
			// Clear our update cache
			delete_transient( $this->transient_name );
			
			// Clear WordPress theme cache
			delete_site_transient( 'update_themes' );
			wp_clean_themes_cache();
			
			// Force refresh theme data
			wp_update_themes();
		}
	}
	
	/**
	 * Get update info (for display purposes)
	 */
	public function get_update_info() {
		$update_cache = get_transient( $this->transient_name );
		
		if ( empty( $update_cache ) || isset( $update_cache['error'] ) ) {
			return array(
				'current_version' => $this->version,
				'latest_version' => $this->version,
				'update_available' => false,
			);
		}
		
		$remote_version = ltrim( $update_cache['version'], 'v' );
		
		return array(
			'current_version' => $this->version,
			'latest_version' => $remote_version,
			'update_available' => version_compare( $this->version, $remote_version, '<' ),
			'release_url' => $update_cache['url'],
		);
	}
}

// Initialize
Nexus_Theme_Updater::instance();
