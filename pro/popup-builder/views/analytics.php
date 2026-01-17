<?php
/**
 * Popup Builder - Analytics Page
 *
 * Display popup performance analytics and statistics.
 *
 * @package Nexus_Pro
 * @subpackage Popup_Builder
 * @since 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get all popups
$args = array(
	'post_type' => 'nexus_popup',
	'posts_per_page' => -1,
	'post_status' => 'publish',
);

$popups = get_posts( $args );

// Calculate totals
$total_impressions = 0;
$total_conversions = 0;
$popup_stats = array();

foreach ( $popups as $popup ) {
	$impressions = (int) get_post_meta( $popup->ID, '_nexus_popup_impressions', true );
	$conversions = (int) get_post_meta( $popup->ID, '_nexus_popup_conversions', true );
	
	$total_impressions += $impressions;
	$total_conversions += $conversions;
	
	$conversion_rate = $impressions > 0 ? ( $conversions / $impressions ) * 100 : 0;
	
	$popup_stats[] = array(
		'id' => $popup->ID,
		'title' => $popup->post_title,
		'impressions' => $impressions,
		'conversions' => $conversions,
		'conversion_rate' => $conversion_rate,
		'status' => $popup->post_status,
	);
}

// Sort by impressions
usort( $popup_stats, function( $a, $b ) {
	return $b['impressions'] - $a['impressions'];
} );

$overall_conversion_rate = $total_impressions > 0 ? ( $total_conversions / $total_impressions ) * 100 : 0;
?>

<div class="wrap nexus-popup-analytics">
	<h1><?php esc_html_e( 'Popup Analytics', 'nexus-pro' ); ?></h1>
	
	<div class="nexus-analytics-summary">
		<div class="analytics-card">
			<div class="card-icon">
				<span class="dashicons dashicons-visibility"></span>
			</div>
			<div class="card-content">
				<h3><?php echo number_format_i18n( $total_impressions ); ?></h3>
				<p><?php esc_html_e( 'Total Impressions', 'nexus-pro' ); ?></p>
			</div>
		</div>
		
		<div class="analytics-card">
			<div class="card-icon">
				<span class="dashicons dashicons-yes-alt"></span>
			</div>
			<div class="card-content">
				<h3><?php echo number_format_i18n( $total_conversions ); ?></h3>
				<p><?php esc_html_e( 'Total Conversions', 'nexus-pro' ); ?></p>
			</div>
		</div>
		
		<div class="analytics-card">
			<div class="card-icon">
				<span class="dashicons dashicons-chart-line"></span>
			</div>
			<div class="card-content">
				<h3><?php echo number_format_i18n( $overall_conversion_rate, 2 ); ?>%</h3>
				<p><?php esc_html_e( 'Conversion Rate', 'nexus-pro' ); ?></p>
			</div>
		</div>
		
		<div class="analytics-card">
			<div class="card-icon">
				<span class="dashicons dashicons-media-document"></span>
			</div>
			<div class="card-content">
				<h3><?php echo number_format_i18n( count( $popups ) ); ?></h3>
				<p><?php esc_html_e( 'Active Popups', 'nexus-pro' ); ?></p>
			</div>
		</div>
	</div>

	<?php if ( ! empty( $popup_stats ) ) : ?>
		<div class="nexus-analytics-table">
			<h2><?php esc_html_e( 'Popup Performance', 'nexus-pro' ); ?></h2>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Popup Name', 'nexus-pro' ); ?></th>
						<th><?php esc_html_e( 'Impressions', 'nexus-pro' ); ?></th>
						<th><?php esc_html_e( 'Conversions', 'nexus-pro' ); ?></th>
						<th><?php esc_html_e( 'Conversion Rate', 'nexus-pro' ); ?></th>
						<th><?php esc_html_e( 'Status', 'nexus-pro' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'nexus-pro' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $popup_stats as $stat ) : ?>
						<tr>
							<td>
								<strong>
									<a href="<?php echo esc_url( get_edit_post_link( $stat['id'] ) ); ?>">
										<?php echo esc_html( $stat['title'] ); ?>
									</a>
								</strong>
							</td>
							<td><?php echo number_format_i18n( $stat['impressions'] ); ?></td>
							<td><?php echo number_format_i18n( $stat['conversions'] ); ?></td>
							<td>
								<span class="conversion-rate <?php echo $stat['conversion_rate'] > 5 ? 'high' : ( $stat['conversion_rate'] > 2 ? 'medium' : 'low' ); ?>">
									<?php echo number_format_i18n( $stat['conversion_rate'], 2 ); ?>%
								</span>
							</td>
							<td>
								<span class="status-badge status-<?php echo esc_attr( $stat['status'] ); ?>">
									<?php echo esc_html( ucfirst( $stat['status'] ) ); ?>
								</span>
							</td>
							<td>
								<a href="<?php echo esc_url( get_edit_post_link( $stat['id'] ) ); ?>" class="button button-small">
									<?php esc_html_e( 'Edit', 'nexus-pro' ); ?>
								</a>
								<button type="button" class="button button-small reset-stats" data-popup-id="<?php echo esc_attr( $stat['id'] ); ?>">
									<?php esc_html_e( 'Reset Stats', 'nexus-pro' ); ?>
								</button>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php else : ?>
		<div class="nexus-analytics-empty">
			<p><?php esc_html_e( 'No popup data available yet. Create your first popup to start tracking analytics.', 'nexus-pro' ); ?></p>
			<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=nexus_popup' ) ); ?>" class="button button-primary">
				<?php esc_html_e( 'Create Your First Popup', 'nexus-pro' ); ?>
			</a>
		</div>
	<?php endif; ?>
</div>

<style>
.nexus-popup-analytics {
	max-width: 1400px;
}

.nexus-analytics-summary {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
	gap: 20px;
	margin: 30px 0;
}

.analytics-card {
	background: #fff;
	border: 1px solid #ddd;
	border-radius: 4px;
	padding: 20px;
	display: flex;
	align-items: center;
	gap: 15px;
}

.card-icon {
	flex-shrink: 0;
}

.card-icon .dashicons {
	font-size: 48px;
	width: 48px;
	height: 48px;
	color: #2271b1;
}

.card-content h3 {
	margin: 0 0 5px;
	font-size: 32px;
	font-weight: 600;
	color: #1d2327;
}

.card-content p {
	margin: 0;
	color: #666;
	font-size: 14px;
}

.nexus-analytics-table {
	margin-top: 40px;
}

.nexus-analytics-table h2 {
	margin-bottom: 15px;
}

.conversion-rate {
	padding: 3px 8px;
	border-radius: 3px;
	font-weight: 600;
}

.conversion-rate.high {
	background: #d4edda;
	color: #155724;
}

.conversion-rate.medium {
	background: #fff3cd;
	color: #856404;
}

.conversion-rate.low {
	background: #f8d7da;
	color: #721c24;
}

.status-badge {
	padding: 3px 8px;
	border-radius: 3px;
	font-size: 12px;
	font-weight: 600;
}

.status-badge.status-publish {
	background: #d4edda;
	color: #155724;
}

.status-badge.status-draft {
	background: #e2e3e5;
	color: #383d41;
}

.nexus-analytics-empty {
	text-align: center;
	padding: 60px 20px;
	background: #fff;
	border: 1px solid #ddd;
	border-radius: 4px;
	margin-top: 30px;
}

.nexus-analytics-empty p {
	color: #666;
	margin-bottom: 20px;
}

.reset-stats {
	color: #b32d2e;
}

.reset-stats:hover {
	color: #b32d2e;
	border-color: #b32d2e;
}
</style>

<script>
jQuery(document).ready(function($) {
	// Reset stats functionality
	$('.reset-stats').on('click', function() {
		const popupId = $(this).data('popup-id');
		const $button = $(this);
		
		if (!confirm('<?php echo esc_js( __( 'Are you sure you want to reset the statistics for this popup?', 'nexus-pro' ) ); ?>')) {
			return;
		}
		
		$button.prop('disabled', true).text('<?php echo esc_js( __( 'Resetting...', 'nexus-pro' ) ); ?>');
		
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'nexus_reset_popup_stats',
				popup_id: popupId,
				nonce: '<?php echo wp_create_nonce( 'nexus_reset_stats' ); ?>'
			},
			success: function(response) {
				if (response.success) {
					location.reload();
				} else {
					alert('<?php echo esc_js( __( 'Error resetting stats. Please try again.', 'nexus-pro' ) ); ?>');
					$button.prop('disabled', false).text('<?php echo esc_js( __( 'Reset Stats', 'nexus-pro' ) ); ?>');
				}
			},
			error: function() {
				alert('<?php echo esc_js( __( 'Error resetting stats. Please try again.', 'nexus-pro' ) ); ?>');
				$button.prop('disabled', false).text('<?php echo esc_js( __( 'Reset Stats', 'nexus-pro' ) ); ?>');
			}
		});
	});
});
</script>
