<?php
/**
 * Template Functions
 *
 * @package Nexus
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Product Specifications
 *
 * @param int $post_id Post ID.
 * @return array
 */
function nexus_get_product_specifications( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	
	$specifications = get_post_meta( $post_id, '_nexus_specifications', true );
	return is_array( $specifications ) ? $specifications : array();
}

/**
 * Display Product Specifications Table
 *
 * @param int $post_id Post ID.
 */
function nexus_display_specifications( $post_id = null ) {
	$specifications = nexus_get_product_specifications( $post_id );
	
	if ( empty( $specifications ) ) {
		return;
	}
	?>
	<div class="nexus-specifications">
		<h3><?php esc_html_e( 'Technical Specifications', 'nexus' ); ?></h3>
		<table class="specifications-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Parameter', 'nexus' ); ?></th>
					<th><?php esc_html_e( 'Value', 'nexus' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $specifications as $spec ) : ?>
					<tr>
						<td><?php echo esc_html( $spec['parameter'] ); ?></td>
						<td>
							<?php echo esc_html( $spec['value'] ); ?>
							<?php if ( ! empty( $spec['unit'] ) ) : ?>
								<span class="unit"><?php echo esc_html( $spec['unit'] ); ?></span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}

/**
 * Get Download File URL
 *
 * @param int $post_id Post ID.
 * @return string
 */
function nexus_get_download_url( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	
	return get_post_meta( $post_id, '_nexus_file_url', true );
}

/**
 * Get Download File Version
 *
 * @param int $post_id Post ID.
 * @return string
 */
function nexus_get_download_version( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	
	return get_post_meta( $post_id, '_nexus_file_version', true );
}

/**
 * Get Container Class
 *
 * @return string
 */
function nexus_get_container_class() {
	$layout = get_theme_mod( 'nexus_sidebar_position', 'right' );
	
	$class = 'nexus-container';
	
	if ( 'none' === $layout ) {
		$class .= ' no-sidebar';
	} elseif ( 'left' === $layout ) {
		$class .= ' sidebar-left';
	} else {
		$class .= ' sidebar-right';
	}
	
	return $class;
}

/**
 * Check if sidebar should be displayed
 *
 * @return bool
 */
function nexus_has_sidebar() {
	$layout = get_theme_mod( 'nexus_sidebar_position', 'right' );
	
	if ( 'none' === $layout ) {
		return false;
	}
	
	if ( is_page_template( 'templates/full-width.php' ) ) {
		return false;
	}
	
	return is_active_sidebar( 'sidebar-1' );
}
