<?php
/**
 * Footer Template
 *
 * @package Nexus
 * @since 1.0.0
 */
?>

	<footer id="colophon" class="site-footer">
		<div class="container">
			<?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) || is_active_sidebar( 'footer-4' ) ) : ?>
				<div class="footer-widgets">
					<div class="footer-widget-area">
						<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
							<div class="footer-widget">
								<?php dynamic_sidebar( 'footer-1' ); ?>
							</div>
						<?php endif; ?>
					</div>
					
					<div class="footer-widget-area">
						<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
							<div class="footer-widget">
								<?php dynamic_sidebar( 'footer-2' ); ?>
							</div>
						<?php endif; ?>
					</div>
					
					<div class="footer-widget-area">
						<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
							<div class="footer-widget">
								<?php dynamic_sidebar( 'footer-3' ); ?>
							</div>
						<?php endif; ?>
					</div>
					
					<div class="footer-widget-area">
						<?php if ( is_active_sidebar( 'footer-4' ) ) : ?>
							<div class="footer-widget">
								<?php dynamic_sidebar( 'footer-4' ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div><!-- .footer-widgets -->
			<?php endif; ?>

			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Footer Menu', 'nexus' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'menu_id'        => 'footer-menu',
							'menu_class'     => 'footer-menu',
							'depth'          => 1,
							'container'      => false,
						)
					);
					?>
				</nav><!-- .footer-navigation -->
			<?php endif; ?>

			<div class="site-info">
				<p class="copyright">
					<?php
					printf(
						/* translators: 1: Theme name, 2: Current year */
						esc_html__( 'Copyright &copy; %1$s %2$s. All rights reserved.', 'nexus' ),
						esc_html( date( 'Y' ) ),
						esc_html( get_bloginfo( 'name' ) )
					);
					?>
				</p>
				<p class="powered-by">
					<?php
					printf(
						/* translators: %s: WordPress */
						esc_html__( 'Powered by %s', 'nexus' ),
						'<a href="' . esc_url( __( 'https://wordpress.org/', 'nexus' ) ) . '">WordPress</a>'
					);
					?>
					<span class="sep"> | </span>
					<?php
					printf(
						/* translators: 1: Theme name, 2: Theme author */
						esc_html__( 'Theme: %1$s', 'nexus' ),
						'<a href="https://jdsandigitel.com">Nexus by Jdsan Digitel</a>'
					);
					?>
				</p>
			</div><!-- .site-info -->
		</div><!-- .container -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
