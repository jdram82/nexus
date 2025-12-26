<?php
/**
 * Template part for displaying project content
 *
 * @package Nexus
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'nexus-project' ); ?>>
	<header class="entry-header">
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;
		?>
		
		<?php
		$industries = get_the_terms( get_the_ID(), 'project_industry' );
		if ( $industries && ! is_wp_error( $industries ) ) :
			?>
			<div class="project-industries">
				<strong><?php esc_html_e( 'Industry:', 'nexus' ); ?></strong>
				<?php
				foreach ( $industries as $industry ) {
					echo '<a href="' . esc_url( get_term_link( $industry ) ) . '" class="project-industry">' . esc_html( $industry->name ) . '</a>';
				}
				?>
			</div>
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="project-thumbnail">
			<?php
			if ( is_singular() ) {
				the_post_thumbnail( 'nexus-featured' );
			} else {
				?>
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( 'nexus-featured' ); ?>
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
		} else {
			the_excerpt();
		}
		?>
	</div><!-- .entry-content -->

	<?php if ( is_singular() ) : ?>
		<footer class="entry-footer">
			<?php
			$technologies = get_the_terms( get_the_ID(), 'project_technology' );
			if ( $technologies && ! is_wp_error( $technologies ) ) :
				?>
				<div class="project-technologies">
					<strong><?php esc_html_e( 'Technologies:', 'nexus' ); ?></strong>
					<?php
					foreach ( $technologies as $tech ) {
						echo '<span class="technology-tag">' . esc_html( $tech->name ) . '</span> ';
					}
					?>
				</div>
			<?php endif; ?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
