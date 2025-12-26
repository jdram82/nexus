<?php
/**
 * License View
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$license_key    = get_option( 'nexus_pro_license_key', '' );
$license_valid  = get_option( 'nexus_pro_license_valid', false );
$license_expires = get_option( 'nexus_pro_license_expires', '' );
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Nexus Pro License', 'nexus' ); ?></h1>

	<div class="nexus-license-card">
		<?php if ( $license_valid ) : ?>
			<div class="license-active">
				<span class="dashicons dashicons-yes-alt"></span>
				<h2><?php esc_html_e( 'License Active', 'nexus' ); ?></h2>
				<p><?php esc_html_e( 'Your Nexus Pro license is active and all features are enabled.', 'nexus' ); ?></p>
				
				<table class="license-info">
					<tr>
						<th><?php esc_html_e( 'License Key:', 'nexus' ); ?></th>
						<td><code><?php echo esc_html( substr( $license_key, 0, 8 ) . str_repeat( '*', 16 ) . substr( $license_key, -4 ) ); ?></code></td>
					</tr>
					<?php if ( $license_expires ) : ?>
					<tr>
						<th><?php esc_html_e( 'Expires:', 'nexus' ); ?></th>
						<td><?php echo esc_html( $license_expires ); ?></td>
					</tr>
					<?php endif; ?>
					<tr>
						<th><?php esc_html_e( 'Site URL:', 'nexus' ); ?></th>
						<td><?php echo esc_html( home_url() ); ?></td>
					</tr>
				</table>

				<form method="post" action="">
					<?php wp_nonce_field( 'nexus_license_action', 'nexus_license_nonce' ); ?>
					<input type="hidden" name="nexus_license_action" value="deactivate">
					<button type="submit" class="button">
						<?php esc_html_e( 'Deactivate License', 'nexus' ); ?>
					</button>
				</form>
			</div>
		<?php else : ?>
			<div class="license-inactive">
				<span class="dashicons dashicons-warning"></span>
				<h2><?php esc_html_e( 'Activate Your License', 'nexus' ); ?></h2>
				<p><?php esc_html_e( 'Enter your license key to activate Nexus Pro features.', 'nexus' ); ?></p>

				<form method="post" action="">
					<?php wp_nonce_field( 'nexus_license_action', 'nexus_license_nonce' ); ?>
					<input type="hidden" name="nexus_license_action" value="activate">
					
					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="license_key"><?php esc_html_e( 'License Key', 'nexus' ); ?></label>
							</th>
							<td>
								<input type="text" 
									id="license_key" 
									name="license_key" 
									value="<?php echo esc_attr( $license_key ); ?>" 
									class="regular-text" 
									placeholder="XXXX-XXXX-XXXX-XXXX">
								<p class="description">
									<?php esc_html_e( 'Enter your Nexus Pro license key. You can find this in your purchase confirmation email.', 'nexus' ); ?>
								</p>
							</td>
						</tr>
					</table>

					<?php submit_button( __( 'Activate License', 'nexus' ) ); ?>
				</form>

				<div class="license-help">
					<h3><?php esc_html_e( 'Need Help?', 'nexus' ); ?></h3>
					<ul>
						<li><?php esc_html_e( 'Lost your license key?', 'nexus' ); ?> <a href="#"><?php esc_html_e( 'Retrieve it here', 'nexus' ); ?></a></li>
						<li><?php esc_html_e( 'Don\'t have a license?', 'nexus' ); ?> <a href="#"><?php esc_html_e( 'Purchase Nexus Pro', 'nexus' ); ?></a></li>
						<li><?php esc_html_e( 'Having trouble?', 'nexus' ); ?> <a href="#"><?php esc_html_e( 'Contact Support', 'nexus' ); ?></a></li>
					</ul>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<style>
.nexus-license-card {
	max-width: 800px;
	background: #fff;
	border: 1px solid #ccd0d4;
	box-shadow: 0 1px 1px rgba(0,0,0,.04);
	padding: 30px;
	margin-top: 20px;
}

.license-active .dashicons,
.license-inactive .dashicons {
	font-size: 48px;
	width: 48px;
	height: 48px;
}

.license-active .dashicons {
	color: #46b450;
}

.license-inactive .dashicons {
	color: #dc3232;
}

.license-active h2,
.license-inactive h2 {
	margin-top: 10px;
}

.license-info {
	margin: 20px 0;
	border-collapse: collapse;
}

.license-info th,
.license-info td {
	padding: 10px;
	text-align: left;
	border-bottom: 1px solid #f0f0f0;
}

.license-info th {
	font-weight: 600;
	width: 150px;
}

.license-help {
	margin-top: 30px;
	padding-top: 30px;
	border-top: 1px solid #eee;
}

.license-help ul {
	list-style: disc;
	margin-left: 20px;
}

.license-help li {
	padding: 5px 0;
}
</style>
