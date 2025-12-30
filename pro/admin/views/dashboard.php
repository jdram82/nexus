<?php
/**
 * Admin Dashboard View
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$license_status = Nexus_License_Manager::instance()->get_license_status();
?>
<div class="wrap nexus-pro-dashboard">
	<h1><?php esc_html_e( 'Nexus Pro Dashboard', 'nexus' ); ?></h1>

	<!-- Dashboard Hero -->
	<div class="nexus-dashboard-hero">
		<img src="<?php echo esc_url( get_template_directory_uri() . '/Nexus_images/Analytics dashboard.png' ); ?>" alt="Nexus Dashboard" style="max-width: 100%; height: auto; border-radius: 8px; margin-bottom: 20px;">
	</div>

	<div class="nexus-dashboard-grid">
		
		<!-- License Status -->
		<div class="nexus-card">
			<h2><?php esc_html_e( 'License Status', 'nexus' ); ?></h2>
			<?php if ( $license_status['valid'] ) : ?>
				<p class="license-status active">
					<span class="dashicons dashicons-yes-alt"></span>
					<?php esc_html_e( 'Active', 'nexus' ); ?>
				</p>
				<?php if ( $license_status['expires'] ) : ?>
					<p><?php printf( __( 'Expires: %s', 'nexus' ), esc_html( $license_status['expires'] ) ); ?></p>
				<?php endif; ?>
			<?php else : ?>
				<p class="license-status inactive">
					<span class="dashicons dashicons-warning"></span>
					<?php esc_html_e( 'Inactive', 'nexus' ); ?>
				</p>
				<p><?php esc_html_e( 'Please activate your license to use Nexus Pro features.', 'nexus' ); ?></p>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=nexus-pro-license' ) ); ?>" class="button button-primary">
					<?php esc_html_e( 'Activate License', 'nexus' ); ?>
				</a>
			<?php endif; ?>
		</div>

		<!-- Features Overview -->
		<div class="nexus-card">
			<h2><?php esc_html_e( 'Pro Features', 'nexus' ); ?></h2>
			<ul class="features-list">
				<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Visual Header/Footer Builder', 'nexus' ); ?></li>
				<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Advanced Product Filtering', 'nexus' ); ?></li>
				<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Documentation System', 'nexus' ); ?></li>
				<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Client Portal', 'nexus' ); ?></li>
				<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Form Builder', 'nexus' ); ?></li>
			</ul>
		</div>

		<!-- Quick Stats -->
		<div class="nexus-card">
			<h2><?php esc_html_e( 'Statistics', 'nexus' ); ?></h2>
			<?php
			$forms_count  = wp_count_posts( 'nexus_form' )->publish;
			$docs_count   = wp_count_posts( 'nexus_doc' )->publish;
			?>
			<div class="stats-grid">
				<div class="stat-item">
					<strong><?php echo esc_html( $forms_count ); ?></strong>
					<span><?php esc_html_e( 'Forms', 'nexus' ); ?></span>
				</div>
				<div class="stat-item">
					<strong><?php echo esc_html( $docs_count ); ?></strong>
					<span><?php esc_html_e( 'Docs', 'nexus' ); ?></span>
				</div>
			</div>
		</div>

		<!-- Quick Links -->
		<div class="nexus-card">
			<h2><?php esc_html_e( 'Quick Links', 'nexus' ); ?></h2>
			<ul class="quick-links">
				<li><a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=nexus_header_builder' ) ); ?>"><?php esc_html_e( 'Header Builder', 'nexus' ); ?></a></li>
				<li><a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=nexus_footer_builder' ) ); ?>"><?php esc_html_e( 'Footer Builder', 'nexus' ); ?></a></li>
				<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=nexus_form' ) ); ?>"><?php esc_html_e( 'Manage Forms', 'nexus' ); ?></a></li>
				<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=nexus_doc' ) ); ?>"><?php esc_html_e( 'Manage Documentation', 'nexus' ); ?></a></li>
				<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=nexus-pro-submissions' ) ); ?>"><?php esc_html_e( 'Form Submissions', 'nexus' ); ?></a></li>
			</ul>
		</div>

	</div>
</div>

<style>
.nexus-dashboard-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
	gap: 20px;
	margin-top: 20px;
}

.nexus-card {
	background: #fff;
	border: 1px solid #ccd0d4;
	box-shadow: 0 1px 1px rgba(0,0,0,.04);
	padding: 20px;
}

.nexus-card h2 {
	margin-top: 0;
	padding-bottom: 10px;
	border-bottom: 1px solid #eee;
}

.license-status {
	font-size: 18px;
	font-weight: 600;
}

.license-status.active {
	color: #46b450;
}

.license-status.inactive {
	color: #dc3232;
}

.license-status .dashicons {
	vertical-align: middle;
}

.features-list {
	list-style: none;
	padding: 0;
	margin: 0;
}

.features-list li {
	padding: 8px 0;
}

.features-list .dashicons {
	color: #46b450;
	vertical-align: middle;
}

.stats-grid {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 15px;
	margin-top: 15px;
}

.stat-item {
	text-align: center;
	padding: 15px;
	background: #f9f9f9;
	border-radius: 4px;
}

.stat-item strong {
	display: block;
	font-size: 32px;
	color: #2271b1;
}

.quick-links {
	list-style: none;
	padding: 0;
	margin: 0;
}

.quick-links li {
	padding: 8px 0;
	border-bottom: 1px solid #f0f0f0;
}

.quick-links li:last-child {
	border-bottom: none;
}
</style>
