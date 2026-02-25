<?php
/**
 * Popup Builder - Popup List Page
 *
 * Display all created popups with management options.
 *
 * @package Nexus_Pro
 * @subpackage Popup_Builder
 * @since 3.2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get popups from the calling function
if ( ! isset( $popups ) ) {
	$popups = array();
}
?>

<div class="wrap nexus-popup-list">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Popups', 'nexus-pro' ); ?>
	</h1>
	<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=nexus_popup' ) ); ?>" class="page-title-action">
		<?php esc_html_e( 'Add New', 'nexus-pro' ); ?>
	</a>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=nexus-popup-templates' ) ); ?>" class="page-title-action">
		<?php esc_html_e( 'Browse Templates', 'nexus-pro' ); ?>
	</a>
	
	<hr class="wp-header-end">

	<?php if ( empty( $popups ) ) : ?>
		<div class="nexus-empty-state">
			<div class="nexus-empty-state-icon">
				<span class="dashicons dashicons-admin-page"></span>
			</div>
			<h2><?php esc_html_e( 'No popups yet', 'nexus-pro' ); ?></h2>
			<p><?php esc_html_e( 'Create your first popup to engage your visitors.', 'nexus-pro' ); ?></p>
			<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=nexus_popup' ) ); ?>" class="button button-primary button-hero">
				<?php esc_html_e( 'Create Your First Popup', 'nexus-pro' ); ?>
			</a>
			<p>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=nexus-popup-templates' ) ); ?>">
					<?php esc_html_e( 'Or browse templates', 'nexus-pro' ); ?>
				</a>
			</p>
		</div>
	<?php else : ?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th scope="col" class="manage-column column-title column-primary">
						<?php esc_html_e( 'Title', 'nexus-pro' ); ?>
					</th>
					<th scope="col" class="manage-column">
						<?php esc_html_e( 'Type', 'nexus-pro' ); ?>
					</th>
					<th scope="col" class="manage-column">
						<?php esc_html_e( 'Status', 'nexus-pro' ); ?>
					</th>
					<th scope="col" class="manage-column">
						<?php esc_html_e( 'Views', 'nexus-pro' ); ?>
					</th>
					<th scope="col" class="manage-column">
						<?php esc_html_e( 'Conversions', 'nexus-pro' ); ?>
					</th>
					<th scope="col" class="manage-column">
						<?php esc_html_e( 'Date', 'nexus-pro' ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $popups as $popup ) :
					$post_id = $popup->ID;
					$edit_url = get_edit_post_link( $post_id );
					$popup_type = get_post_meta( $post_id, '_nexus_popup_type', true ) ?: 'modal';
					$views = get_post_meta( $post_id, '_nexus_popup_views', true ) ?: 0;
					$conversions = get_post_meta( $post_id, '_nexus_popup_conversions', true ) ?: 0;
					$status = $popup->post_status;
					$status_label = $status === 'publish' ? __( 'Active', 'nexus-pro' ) : __( 'Draft', 'nexus-pro' );
					$status_class = $status === 'publish' ? 'active' : 'draft';
					?>
					<tr>
						<td class="title column-title has-row-actions column-primary" data-colname="<?php esc_attr_e( 'Title', 'nexus-pro' ); ?>">
							<strong>
								<a href="<?php echo esc_url( $edit_url ); ?>" class="row-title">
									<?php echo esc_html( $popup->post_title ); ?>
								</a>
							</strong>
							<div class="row-actions">
								<span class="edit">
									<a href="<?php echo esc_url( $edit_url ); ?>">
										<?php esc_html_e( 'Edit', 'nexus-pro' ); ?>
									</a> |
								</span>
								<span class="trash">
									<a href="<?php echo esc_url( get_delete_post_link( $post_id ) ); ?>" class="submitdelete">
										<?php esc_html_e( 'Trash', 'nexus-pro' ); ?>
									</a> |
								</span>
								<span class="view">
									<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" target="_blank">
										<?php esc_html_e( 'Preview', 'nexus-pro' ); ?>
									</a>
								</span>
							</div>
						</td>
						<td class="type column-type" data-colname="<?php esc_attr_e( 'Type', 'nexus-pro' ); ?>">
							<?php echo esc_html( ucfirst( $popup_type ) ); ?>
						</td>
						<td class="status column-status" data-colname="<?php esc_attr_e( 'Status', 'nexus-pro' ); ?>">
							<span class="nexus-status-badge nexus-status-<?php echo esc_attr( $status_class ); ?>">
								<?php echo esc_html( $status_label ); ?>
							</span>
						</td>
						<td class="views column-views" data-colname="<?php esc_attr_e( 'Views', 'nexus-pro' ); ?>">
							<?php echo esc_html( number_format_i18n( $views ) ); ?>
						</td>
						<td class="conversions column-conversions" data-colname="<?php esc_attr_e( 'Conversions', 'nexus-pro' ); ?>">
							<?php
							$conversion_rate = $views > 0 ? ( $conversions / $views * 100 ) : 0;
							echo esc_html( number_format_i18n( $conversions ) );
							?>
							<span class="conversion-rate">
								(<?php echo esc_html( number_format_i18n( $conversion_rate, 1 ) ); ?>%)
							</span>
						</td>
						<td class="date column-date" data-colname="<?php esc_attr_e( 'Date', 'nexus-pro' ); ?>">
							<?php echo esc_html( get_the_date( '', $popup ) ); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>

<style>
.nexus-popup-list .nexus-empty-state {
	text-align: center;
	padding: 80px 20px;
}

.nexus-popup-list .nexus-empty-state-icon {
	font-size: 80px;
	color: #ddd;
	margin-bottom: 20px;
}

.nexus-popup-list .nexus-empty-state-icon .dashicons {
	width: 80px;
	height: 80px;
	font-size: 80px;
}

.nexus-popup-list .nexus-empty-state h2 {
	margin: 20px 0 10px;
	font-size: 24px;
}

.nexus-popup-list .nexus-status-badge {
	display: inline-block;
	padding: 3px 10px;
	border-radius: 3px;
	font-size: 12px;
	font-weight: 600;
}

.nexus-popup-list .nexus-status-active {
	background: #d4edda;
	color: #155724;
}

.nexus-popup-list .nexus-status-draft {
	background: #f8f9fa;
	color: #6c757d;
}

.nexus-popup-list .conversion-rate {
	color: #666;
	font-size: 0.9em;
}
</style>
