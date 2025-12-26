<?php
/**
 * Settings View
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Nexus Pro Settings', 'nexus' ); ?></h1>

	<h2 class="nav-tab-wrapper">
		<a href="#general" class="nav-tab nav-tab-active"><?php esc_html_e( 'General', 'nexus' ); ?></a>
		<a href="#portal" class="nav-tab"><?php esc_html_e( 'Client Portal', 'nexus' ); ?></a>
		<a href="#forms" class="nav-tab"><?php esc_html_e( 'Forms', 'nexus' ); ?></a>
		<a href="#docs" class="nav-tab"><?php esc_html_e( 'Documentation', 'nexus' ); ?></a>
	</h2>

	<form method="post" action="options.php">
		<?php settings_fields( 'nexus_pro_settings' ); ?>
		
		<div id="general" class="tab-content active">
			<h2><?php esc_html_e( 'General Settings', 'nexus' ); ?></h2>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Enable Features', 'nexus' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="nexus_pro_enable_header_builder" value="1" checked>
							<?php esc_html_e( 'Header Builder', 'nexus' ); ?>
						</label><br>
						<label>
							<input type="checkbox" name="nexus_pro_enable_footer_builder" value="1" checked>
							<?php esc_html_e( 'Footer Builder', 'nexus' ); ?>
						</label><br>
						<label>
							<input type="checkbox" name="nexus_pro_enable_filtering" value="1" checked>
							<?php esc_html_e( 'Advanced Filtering', 'nexus' ); ?>
						</label><br>
						<label>
							<input type="checkbox" name="nexus_pro_enable_docs" value="1" checked>
							<?php esc_html_e( 'Documentation System', 'nexus' ); ?>
						</label><br>
						<label>
							<input type="checkbox" name="nexus_pro_enable_portal" value="1" checked>
							<?php esc_html_e( 'Client Portal', 'nexus' ); ?>
						</label><br>
						<label>
							<input type="checkbox" name="nexus_pro_enable_forms" value="1" checked>
							<?php esc_html_e( 'Form Builder', 'nexus' ); ?>
						</label>
					</td>
				</tr>
			</table>
		</div>

		<div id="portal" class="tab-content">
			<h2><?php esc_html_e( 'Client Portal Settings', 'nexus' ); ?></h2>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Portal Page', 'nexus' ); ?></th>
					<td>
						<p class="description">
							<?php esc_html_e( 'The client portal is accessible at:', 'nexus' ); ?>
							<code><?php echo esc_url( home_url( '/client-portal/' ) ); ?></code>
						</p>
					</td>
				</tr>
			</table>
		</div>

		<div id="forms" class="tab-content">
			<h2><?php esc_html_e( 'Form Settings', 'nexus' ); ?></h2>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Save Submissions', 'nexus' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="nexus_pro_save_submissions" value="1" checked>
							<?php esc_html_e( 'Save form submissions to database', 'nexus' ); ?>
						</label>
					</td>
				</tr>
			</table>
		</div>

		<div id="docs" class="tab-content">
			<h2><?php esc_html_e( 'Documentation Settings', 'nexus' ); ?></h2>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Syntax Highlighting', 'nexus' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="nexus_pro_syntax_highlighting" value="1" checked>
							<?php esc_html_e( 'Enable syntax highlighting for code blocks', 'nexus' ); ?>
						</label>
					</td>
				</tr>
			</table>
		</div>

		<?php submit_button(); ?>
	</form>
</div>

<style>
.tab-content {
	display: none;
	background: #fff;
	border: 1px solid #ccd0d4;
	border-top: none;
	padding: 20px;
}

.tab-content.active {
	display: block;
}
</style>

<script>
jQuery(document).ready(function($) {
	$('.nav-tab').on('click', function(e) {
		e.preventDefault();
		var target = $(this).attr('href');
		
		$('.nav-tab').removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active');
		
		$('.tab-content').removeClass('active');
		$(target).addClass('active');
	});
});
</script>
