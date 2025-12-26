<?php
/**
 * Portal Manager
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Portal Manager Class
 */
class Nexus_Portal_Manager {

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
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_action( 'template_redirect', array( $this, 'template_redirect' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_shortcode( 'nexus_client_portal', array( $this, 'portal_shortcode' ) );
	}

	/**
	 * Add Rewrite Rules
	 */
	public function add_rewrite_rules() {
		add_rewrite_rule( '^client-portal/?$', 'index.php?nexus_portal=dashboard', 'top' );
		add_rewrite_rule( '^client-portal/([^/]+)/?$', 'index.php?nexus_portal=$matches[1]', 'top' );
	}

	/**
	 * Add Query Vars
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'nexus_portal';
		return $vars;
	}

	/**
	 * Template Redirect
	 */
	public function template_redirect() {
		$portal_page = get_query_var( 'nexus_portal' );

		if ( ! empty( $portal_page ) ) {
			// Require login
			if ( ! is_user_logged_in() ) {
				auth_redirect();
				exit;
			}

			// Load portal template
			$this->load_portal_template( $portal_page );
			exit;
		}
	}

	/**
	 * Load Portal Template
	 */
	private function load_portal_template( $page ) {
		get_header();

		echo '<div class="nexus-client-portal">';
		echo '<div class="container">';

		// Sidebar
		$this->render_sidebar();

		// Content
		echo '<div class="portal-content">';
		
		switch ( $page ) {
			case 'dashboard':
				$this->render_dashboard();
				break;
			case 'projects':
				$this->render_projects();
				break;
			case 'downloads':
				$this->render_downloads();
				break;
			case 'support':
				$this->render_support();
				break;
			case 'profile':
				$this->render_profile();
				break;
			default:
				$this->render_dashboard();
		}

		echo '</div>'; // .portal-content
		echo '</div>'; // .container
		echo '</div>'; // .nexus-client-portal

		get_footer();
	}

	/**
	 * Render Sidebar
	 */
	private function render_sidebar() {
		$current_user = wp_get_current_user();
		$current_page = get_query_var( 'nexus_portal', 'dashboard' );
		?>
		<div class="portal-sidebar">
			<div class="portal-user-info">
				<?php echo get_avatar( $current_user->ID, 80 ); ?>
				<h3><?php echo esc_html( $current_user->display_name ); ?></h3>
				<p><?php echo esc_html( $current_user->user_email ); ?></p>
			</div>

			<nav class="portal-nav">
				<ul>
					<li class="<?php echo 'dashboard' === $current_page ? 'active' : ''; ?>">
						<a href="<?php echo esc_url( home_url( '/client-portal/' ) ); ?>">
							<span class="dashicons dashicons-dashboard"></span>
							<?php esc_html_e( 'Dashboard', 'nexus' ); ?>
						</a>
					</li>
					<li class="<?php echo 'projects' === $current_page ? 'active' : ''; ?>">
						<a href="<?php echo esc_url( home_url( '/client-portal/projects/' ) ); ?>">
							<span class="dashicons dashicons-portfolio"></span>
							<?php esc_html_e( 'Projects', 'nexus' ); ?>
						</a>
					</li>
					<li class="<?php echo 'downloads' === $current_page ? 'active' : ''; ?>">
						<a href="<?php echo esc_url( home_url( '/client-portal/downloads/' ) ); ?>">
							<span class="dashicons dashicons-download"></span>
							<?php esc_html_e( 'Downloads', 'nexus' ); ?>
						</a>
					</li>
					<li class="<?php echo 'support' === $current_page ? 'active' : ''; ?>">
						<a href="<?php echo esc_url( home_url( '/client-portal/support/' ) ); ?>">
							<span class="dashicons dashicons-sos"></span>
							<?php esc_html_e( 'Support', 'nexus' ); ?>
						</a>
					</li>
					<li class="<?php echo 'profile' === $current_page ? 'active' : ''; ?>">
						<a href="<?php echo esc_url( home_url( '/client-portal/profile/' ) ); ?>">
							<span class="dashicons dashicons-admin-users"></span>
							<?php esc_html_e( 'Profile', 'nexus' ); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>">
							<span class="dashicons dashicons-exit"></span>
							<?php esc_html_e( 'Logout', 'nexus' ); ?>
						</a>
					</li>
				</ul>
			</nav>
		</div>
		<?php
	}

	/**
	 * Render Dashboard
	 */
	private function render_dashboard() {
		$current_user = wp_get_current_user();
		?>
		<div class="portal-page portal-dashboard">
			<h1><?php esc_html_e( 'Dashboard', 'nexus' ); ?></h1>
			
			<div class="portal-stats">
				<div class="stat-box">
					<span class="stat-icon dashicons dashicons-portfolio"></span>
					<div class="stat-content">
						<h3><?php echo esc_html( $this->get_user_projects_count() ); ?></h3>
						<p><?php esc_html_e( 'Active Projects', 'nexus' ); ?></p>
					</div>
				</div>

				<div class="stat-box">
					<span class="stat-icon dashicons dashicons-download"></span>
					<div class="stat-content">
						<h3><?php echo esc_html( $this->get_user_downloads_count() ); ?></h3>
						<p><?php esc_html_e( 'Downloads', 'nexus' ); ?></p>
					</div>
				</div>

				<div class="stat-box">
					<span class="stat-icon dashicons dashicons-admin-comments"></span>
					<div class="stat-content">
						<h3><?php echo esc_html( $this->get_user_tickets_count() ); ?></h3>
						<p><?php esc_html_e( 'Support Tickets', 'nexus' ); ?></p>
					</div>
				</div>
			</div>

			<div class="portal-recent">
				<h2><?php esc_html_e( 'Recent Activity', 'nexus' ); ?></h2>
				<?php $this->render_recent_activity(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Projects
	 */
	private function render_projects() {
		?>
		<div class="portal-page portal-projects">
			<h1><?php esc_html_e( 'My Projects', 'nexus' ); ?></h1>
			<?php
			$projects = $this->get_user_projects();
			if ( ! empty( $projects ) ) {
				echo '<div class="projects-grid">';
				foreach ( $projects as $project ) {
					$this->render_project_card( $project );
				}
				echo '</div>';
			} else {
				echo '<p>' . esc_html__( 'No projects found.', 'nexus' ) . '</p>';
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render Downloads
	 */
	private function render_downloads() {
		?>
		<div class="portal-page portal-downloads">
			<h1><?php esc_html_e( 'Downloads', 'nexus' ); ?></h1>
			<?php
			$downloads = $this->get_user_downloads();
			if ( ! empty( $downloads ) ) {
				echo '<table class="downloads-table">';
				echo '<thead><tr><th>' . esc_html__( 'File', 'nexus' ) . '</th><th>' . esc_html__( 'Version', 'nexus' ) . '</th><th>' . esc_html__( 'Date', 'nexus' ) . '</th><th>' . esc_html__( 'Actions', 'nexus' ) . '</th></tr></thead>';
				echo '<tbody>';
				foreach ( $downloads as $download ) {
					$this->render_download_row( $download );
				}
				echo '</tbody>';
				echo '</table>';
			} else {
				echo '<p>' . esc_html__( 'No downloads available.', 'nexus' ) . '</p>';
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render Support
	 */
	private function render_support() {
		?>
		<div class="portal-page portal-support">
			<h1><?php esc_html_e( 'Support', 'nexus' ); ?></h1>
			
			<div class="support-actions">
				<button class="button button-primary" id="new-ticket-btn">
					<?php esc_html_e( 'New Support Ticket', 'nexus' ); ?>
				</button>
			</div>

			<h2><?php esc_html_e( 'My Tickets', 'nexus' ); ?></h2>
			<?php $this->render_support_tickets(); ?>
		</div>
		<?php
	}

	/**
	 * Render Profile
	 */
	private function render_profile() {
		$current_user = wp_get_current_user();
		?>
		<div class="portal-page portal-profile">
			<h1><?php esc_html_e( 'Profile Settings', 'nexus' ); ?></h1>

			<form method="post" class="portal-profile-form">
				<?php wp_nonce_field( 'update_profile', 'profile_nonce' ); ?>
				
				<div class="form-group">
					<label><?php esc_html_e( 'Display Name', 'nexus' ); ?></label>
					<input type="text" name="display_name" value="<?php echo esc_attr( $current_user->display_name ); ?>" required>
				</div>

				<div class="form-group">
					<label><?php esc_html_e( 'Email', 'nexus' ); ?></label>
					<input type="email" name="user_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" required>
				</div>

				<div class="form-group">
					<label><?php esc_html_e( 'Company', 'nexus' ); ?></label>
					<input type="text" name="company" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'company', true ) ); ?>">
				</div>

				<div class="form-group">
					<label><?php esc_html_e( 'Phone', 'nexus' ); ?></label>
					<input type="tel" name="phone" value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'phone', true ) ); ?>">
				</div>

				<button type="submit" class="button button-primary">
					<?php esc_html_e( 'Update Profile', 'nexus' ); ?>
				</button>
			</form>
		</div>
		<?php
	}

	/**
	 * Get User Projects Count
	 */
	private function get_user_projects_count() {
		$current_user = wp_get_current_user();
		$projects = get_posts(
			array(
				'post_type'   => 'nexus_project',
				'meta_key'    => '_client_id',
				'meta_value'  => $current_user->ID,
				'numberposts' => -1,
			)
		);
		return count( $projects );
	}

	/**
	 * Get User Downloads Count
	 */
	private function get_user_downloads_count() {
		return count( $this->get_user_downloads() );
	}

	/**
	 * Get User Tickets Count
	 */
	private function get_user_tickets_count() {
		return 0; // Placeholder
	}

	/**
	 * Get User Projects
	 */
	private function get_user_projects() {
		$current_user = wp_get_current_user();
		return get_posts(
			array(
				'post_type'   => 'nexus_project',
				'meta_key'    => '_client_id',
				'meta_value'  => $current_user->ID,
				'numberposts' => -1,
			)
		);
	}

	/**
	 * Get User Downloads
	 */
	private function get_user_downloads() {
		return get_posts(
			array(
				'post_type'   => 'nexus_download',
				'numberposts' => -1,
			)
		);
	}

	/**
	 * Render Recent Activity
	 */
	private function render_recent_activity() {
		echo '<p>' . esc_html__( 'No recent activity.', 'nexus' ) . '</p>';
	}

	/**
	 * Render Project Card
	 */
	private function render_project_card( $project ) {
		?>
		<div class="project-card">
			<?php if ( has_post_thumbnail( $project->ID ) ) : ?>
				<?php echo get_the_post_thumbnail( $project->ID, 'medium' ); ?>
			<?php endif; ?>
			<h3><?php echo esc_html( $project->post_title ); ?></h3>
			<p><?php echo wp_trim_words( $project->post_content, 20 ); ?></p>
			<a href="<?php echo esc_url( get_permalink( $project->ID ) ); ?>" class="button">
				<?php esc_html_e( 'View Project', 'nexus' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Render Download Row
	 */
	private function render_download_row( $download ) {
		$file_url     = get_post_meta( $download->ID, '_nexus_file_url', true );
		$file_version = get_post_meta( $download->ID, '_nexus_file_version', true );
		?>
		<tr>
			<td><?php echo esc_html( $download->post_title ); ?></td>
			<td><?php echo esc_html( $file_version ); ?></td>
			<td><?php echo esc_html( get_the_date( '', $download->ID ) ); ?></td>
			<td>
				<a href="<?php echo esc_url( $file_url ); ?>" class="button button-small" download>
					<?php esc_html_e( 'Download', 'nexus' ); ?>
				</a>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render Support Tickets
	 */
	private function render_support_tickets() {
		echo '<p>' . esc_html__( 'No support tickets found.', 'nexus' ) . '</p>';
	}

	/**
	 * Portal Shortcode
	 */
	public function portal_shortcode( $atts ) {
		if ( ! is_user_logged_in() ) {
			return '<p>' . __( 'Please login to access the client portal.', 'nexus' ) . '</p>';
		}

		ob_start();
		$this->render_dashboard();
		return ob_get_clean();
	}

	/**
	 * Enqueue Scripts
	 */
	public function enqueue_scripts() {
		if ( get_query_var( 'nexus_portal' ) ) {
			wp_enqueue_style(
				'nexus-portal',
				NEXUS_PRO_URI . '/assets/css/portal.css',
				array(),
				NEXUS_PRO_VERSION
			);

			wp_enqueue_script(
				'nexus-portal',
				NEXUS_PRO_URI . '/assets/js/portal.js',
				array( 'jquery' ),
				NEXUS_PRO_VERSION,
				true
			);
		}
	}
}
