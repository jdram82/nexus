<?php
/**
 * Template part for displaying download content
 *
 * @package Nexus
 * @since 1.0.0
 */

$file_url     = nexus_get_download_url();
$file_version = nexus_get_download_version();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'nexus-download' ); ?>>
	<header class="entry-header">
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;
		?>
		
		<?php if ( $file_version ) : ?>
			<div class="download-version">
				<strong><?php esc_html_e( 'Version:', 'nexus' ); ?></strong> <?php echo esc_html( $file_version ); ?>
			</div>
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="download-thumbnail">
			<?php the_post_thumbnail( 'nexus-thumbnail' ); ?>
		</div>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		if ( is_singular() ) {
			the_content();
		} else {
			the_excerpt();
		}
		?>
	</div><!-- .entry-content -->

	<?php if ( $file_url ) : ?>
		<footer class="entry-footer">
			<div class="download-button-wrapper">
				<a href="<?php echo esc_url( $file_url ); ?>" class="download-button" download>
					<span class="download-icon">⬇</span>
					<?php esc_html_e( 'Download', 'nexus' ); ?>
					<?php
					$file_size = get_post_meta( get_the_ID(), '_nexus_file_size', true );
					if ( $file_size ) :
						?>
						<span class="file-size">(<?php echo esc_html( $file_size ); ?>)</span>
					<?php endif; ?>
				</a>
			</div>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
