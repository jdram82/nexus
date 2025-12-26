<?php
/**
 * Template part for displaying product content
 *
 * @package Nexus
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'nexus-product' ); ?>>
	<header class="entry-header">
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;
		?>
		
		<?php
		$categories = get_the_terms( get_the_ID(), 'product_category' );
		if ( $categories && ! is_wp_error( $categories ) ) :
			?>
			<div class="product-categories">
				<?php
				foreach ( $categories as $category ) {
					echo '<a href="' . esc_url( get_term_link( $category ) ) . '" class="product-category">' . esc_html( $category->name ) . '</a>';
				}
				?>
			</div>
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="product-thumbnail">
			<?php
			if ( is_singular() ) {
				the_post_thumbnail( 'nexus-product' );
			} else {
				?>
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( 'nexus-product' ); ?>
				</a>
				<?php
			}
			?>
		</div>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		if ( is_singular() ) {
			the_content();
			
			// Display specifications
			nexus_display_specifications();
		} else {
			the_excerpt();
		}
		?>
	</div><!-- .entry-content -->

	<?php if ( is_singular() ) : ?>
		<footer class="entry-footer">
			<?php
			$tags = get_the_terms( get_the_ID(), 'product_tag' );
			if ( $tags && ! is_wp_error( $tags ) ) :
				?>
				<div class="product-tags">
					<strong><?php esc_html_e( 'Tags:', 'nexus' ); ?></strong>
					<?php
					foreach ( $tags as $tag ) {
						echo '<a href="' . esc_url( get_term_link( $tag ) ) . '" class="product-tag">' . esc_html( $tag->name ) . '</a> ';
					}
					?>
				</div>
			<?php endif; ?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
