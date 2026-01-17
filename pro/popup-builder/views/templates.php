<?php
/**
 * Popup Builder - Templates Page
 *
 * Display available popup templates for quick creation.
 *
 * @package Nexus_Pro
 * @subpackage Popup_Builder
 * @since 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap nexus-popup-templates">
	<h1><?php esc_html_e( 'Popup Templates', 'nexus-pro' ); ?></h1>
	
	<div class="nexus-templates-header">
		<p class="description">
			<?php esc_html_e( 'Choose from pre-designed popup templates to get started quickly.', 'nexus-pro' ); ?>
		</p>
	</div>

	<div class="nexus-templates-grid">
		<?php
		// Template categories
		$template_categories = array(
			'marketing' => __( 'Marketing', 'nexus-pro' ),
			'newsletter' => __( 'Newsletter', 'nexus-pro' ),
			'exit-intent' => __( 'Exit Intent', 'nexus-pro' ),
			'announcement' => __( 'Announcement', 'nexus-pro' ),
			'video' => __( 'Video', 'nexus-pro' ),
			'coupon' => __( 'Coupon', 'nexus-pro' ),
		);

		// Sample templates
		$templates = array(
			array(
				'id' => 'newsletter-basic',
				'name' => __( 'Newsletter Signup', 'nexus-pro' ),
				'category' => 'newsletter',
				'description' => __( 'Simple newsletter subscription form', 'nexus-pro' ),
				'preview_image' => '',
			),
			array(
				'id' => 'exit-discount',
				'name' => __( 'Exit Discount Offer', 'nexus-pro' ),
				'category' => 'exit-intent',
				'description' => __( 'Special discount for users about to leave', 'nexus-pro' ),
				'preview_image' => '',
			),
			array(
				'id' => 'video-popup',
				'name' => __( 'Video Popup', 'nexus-pro' ),
				'category' => 'video',
				'description' => __( 'Embed video in a popup', 'nexus-pro' ),
				'preview_image' => '',
			),
			array(
				'id' => 'announcement-bar',
				'name' => __( 'Announcement Bar', 'nexus-pro' ),
				'category' => 'announcement',
				'description' => __( 'Top/bottom announcement bar', 'nexus-pro' ),
				'preview_image' => '',
			),
			array(
				'id' => 'coupon-code',
				'name' => __( 'Coupon Code Popup', 'nexus-pro' ),
				'category' => 'coupon',
				'description' => __( 'Display coupon codes to visitors', 'nexus-pro' ),
				'preview_image' => '',
			),
		);

		foreach ( $templates as $template ) :
			?>
			<div class="nexus-template-card" data-category="<?php echo esc_attr( $template['category'] ); ?>">
				<div class="template-preview">
					<?php if ( ! empty( $template['preview_image'] ) ) : ?>
						<img src="<?php echo esc_url( $template['preview_image'] ); ?>" alt="<?php echo esc_attr( $template['name'] ); ?>">
					<?php else : ?>
						<div class="template-placeholder">
							<span class="dashicons dashicons-welcome-view-site"></span>
						</div>
					<?php endif; ?>
				</div>
				<div class="template-info">
					<h3><?php echo esc_html( $template['name'] ); ?></h3>
					<p class="template-description"><?php echo esc_html( $template['description'] ); ?></p>
					<div class="template-actions">
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=nexus_popup&template=' . $template['id'] ) ); ?>" class="button button-primary">
							<?php esc_html_e( 'Use Template', 'nexus-pro' ); ?>
						</a>
						<button type="button" class="button template-preview-btn" data-template="<?php echo esc_attr( $template['id'] ); ?>">
							<?php esc_html_e( 'Preview', 'nexus-pro' ); ?>
						</button>
					</div>
				</div>
			</div>
			<?php
		endforeach;
		?>
	</div>

	<div class="nexus-templates-empty" style="display: none;">
		<p><?php esc_html_e( 'No templates found matching your criteria.', 'nexus-pro' ); ?></p>
	</div>
</div>

<style>
.nexus-popup-templates {
	max-width: 1400px;
}

.nexus-templates-header {
	margin: 20px 0;
}

.nexus-templates-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
	gap: 20px;
	margin-top: 30px;
}

.nexus-template-card {
	background: #fff;
	border: 1px solid #ddd;
	border-radius: 4px;
	overflow: hidden;
	transition: box-shadow 0.2s;
}

.nexus-template-card:hover {
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.template-preview {
	position: relative;
	padding-bottom: 60%;
	background: #f5f5f5;
}

.template-preview img {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	object-fit: cover;
}

.template-placeholder {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	display: flex;
	align-items: center;
	justify-content: center;
}

.template-placeholder .dashicons {
	font-size: 64px;
	width: 64px;
	height: 64px;
	color: #ccc;
}

.template-info {
	padding: 15px;
}

.template-info h3 {
	margin: 0 0 10px;
	font-size: 16px;
}

.template-description {
	margin: 0 0 15px;
	color: #666;
	font-size: 13px;
}

.template-actions {
	display: flex;
	gap: 10px;
}

.template-actions .button {
	flex: 1;
}

.nexus-templates-empty {
	text-align: center;
	padding: 60px 20px;
	color: #666;
}
</style>

<script>
jQuery(document).ready(function($) {
	// Template preview functionality
	$('.template-preview-btn').on('click', function() {
		const templateId = $(this).data('template');
		alert('Template preview for: ' + templateId + '\n(Preview functionality coming soon)');
	});
});
</script>
