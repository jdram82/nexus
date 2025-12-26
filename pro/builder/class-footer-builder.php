<?php
/**
 * Footer Builder
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Footer Builder Class
 */
class Nexus_Footer_Builder {

	/**
	 * Instance
	 */
	private static $instance;

	/**
	 * Get Instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'customize_register', array( $this, 'register_customizer' ) );
		add_filter( 'nexus_footer_output', array( $this, 'render_footer' ) );
	}

	/**
	 * Register Customizer
	 */
	public function register_customizer( $wp_customize ) {
		// Footer Builder Panel
		$wp_customize->add_panel(
			'nexus_footer_builder',
			array(
				'title'       => __( 'Footer Builder', 'nexus' ),
				'description' => __( 'Build your custom footer layout', 'nexus' ),
				'priority'    => 31,
			)
		);

		// Layout Section
		$wp_customize->add_section(
			'nexus_footer_layout',
			array(
				'title' => __( 'Footer Layout', 'nexus' ),
				'panel' => 'nexus_footer_builder',
			)
		);

		// Footer Rows
		$this->add_row_section( $wp_customize, 'top', __( 'Top Row', 'nexus' ) );
		$this->add_row_section( $wp_customize, 'main', __( 'Main Row', 'nexus' ) );
		$this->add_row_section( $wp_customize, 'bottom', __( 'Bottom Row', 'nexus' ) );
	}

	/**
	 * Add Row Section
	 */
	private function add_row_section( $wp_customize, $row, $title ) {
		$wp_customize->add_section(
			'nexus_footer_' . $row . '_row',
			array(
				'title' => $title,
				'panel' => 'nexus_footer_builder',
			)
		);

		// Enable Row
		$wp_customize->add_setting(
			'nexus_footer_' . $row . '_enabled',
			array(
				'default'   => 'main' === $row ? true : false,
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			'nexus_footer_' . $row . '_enabled',
			array(
				'label'   => __( 'Enable Row', 'nexus' ),
				'section' => 'nexus_footer_' . $row . '_row',
				'type'    => 'checkbox',
			)
		);

		// Columns
		$wp_customize->add_setting(
			'nexus_footer_' . $row . '_columns',
			array(
				'default'   => 'main' === $row ? 4 : 1,
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			'nexus_footer_' . $row . '_columns',
			array(
				'label'   => __( 'Columns', 'nexus' ),
				'section' => 'nexus_footer_' . $row . '_row',
				'type'    => 'select',
				'choices' => array(
					'1' => __( '1 Column', 'nexus' ),
					'2' => __( '2 Columns', 'nexus' ),
					'3' => __( '3 Columns', 'nexus' ),
					'4' => __( '4 Columns', 'nexus' ),
					'5' => __( '5 Columns', 'nexus' ),
					'6' => __( '6 Columns', 'nexus' ),
				),
			)
		);

		// Column Elements
		for ( $i = 1; $i <= 6; $i++ ) {
			$wp_customize->add_setting(
				'nexus_footer_' . $row . '_col' . $i,
				array(
					'default'   => array(),
					'transport' => 'refresh',
				)
			);

			$wp_customize->add_control(
				'nexus_footer_' . $row . '_col' . $i,
				array(
					'label'       => sprintf( __( 'Column %d Elements', 'nexus' ), $i ),
					'section'     => 'nexus_footer_' . $row . '_row',
					'type'        => 'select',
					'multiple'    => true,
					'choices'     => $this->get_element_choices(),
				)
			);
		}
	}

	/**
	 * Get Element Choices
	 */
	private function get_element_choices() {
		return array(
			'widget'       => __( 'Widget Area', 'nexus' ),
			'menu'         => __( 'Menu', 'nexus' ),
			'logo'         => __( 'Logo', 'nexus' ),
			'social'       => __( 'Social Icons', 'nexus' ),
			'copyright'    => __( 'Copyright', 'nexus' ),
			'text'         => __( 'Text/HTML', 'nexus' ),
			'contact'      => __( 'Contact Info', 'nexus' ),
			'newsletter'   => __( 'Newsletter', 'nexus' ),
		);
	}

	/**
	 * Render Footer
	 */
	public function render_footer( $content ) {
		$custom_footer = get_theme_mod( 'nexus_footer_custom', false );

		if ( ! $custom_footer ) {
			return $content;
		}

		ob_start();
		?>
		<footer id="colophon" class="nexus-footer-builder site-footer">
			<?php
			$rows = array( 'top', 'main', 'bottom' );
			foreach ( $rows as $row ) {
				if ( get_theme_mod( 'nexus_footer_' . $row . '_enabled', 'main' === $row ) ) {
					$this->render_row( $row );
				}
			}
			?>
		</footer>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render Row
	 */
	private function render_row( $row ) {
		$columns = get_theme_mod( 'nexus_footer_' . $row . '_columns', 1 );
		?>
		<div class="footer-row footer-<?php echo esc_attr( $row ); ?>-row">
			<div class="container">
				<div class="footer-columns footer-columns-<?php echo esc_attr( $columns ); ?>">
					<?php
					for ( $i = 1; $i <= $columns; $i++ ) {
						$elements = get_theme_mod( 'nexus_footer_' . $row . '_col' . $i, array() );
						if ( ! empty( $elements ) ) {
							?>
							<div class="footer-column footer-column-<?php echo esc_attr( $i ); ?>">
								<?php $this->render_elements( $elements ); ?>
							</div>
							<?php
						}
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Elements
	 */
	private function render_elements( $elements ) {
		if ( ! is_array( $elements ) ) {
			$elements = array( $elements );
		}

		foreach ( $elements as $element ) {
			$this->render_element( $element );
		}
	}

	/**
	 * Render Element
	 */
	private function render_element( $element ) {
		do_action( 'nexus_footer_element_' . $element );

		switch ( $element ) {
			case 'widget':
				$this->render_widget();
				break;
			case 'menu':
				$this->render_menu();
				break;
			case 'logo':
				$this->render_logo();
				break;
			case 'social':
				$this->render_social();
				break;
			case 'copyright':
				$this->render_copyright();
				break;
			case 'contact':
				$this->render_contact();
				break;
			case 'newsletter':
				$this->render_newsletter();
				break;
		}
	}

	/**
	 * Render Widget
	 */
	private function render_widget() {
		if ( is_active_sidebar( 'footer-1' ) ) {
			dynamic_sidebar( 'footer-1' );
		}
	}

	/**
	 * Render Menu
	 */
	private function render_menu() {
		wp_nav_menu(
			array(
				'theme_location' => 'footer',
				'menu_class'     => 'footer-menu',
				'container'      => 'nav',
			)
		);
	}

	/**
	 * Render Logo
	 */
	private function render_logo() {
		if ( has_custom_logo() ) {
			the_custom_logo();
		}
	}

	/**
	 * Render Social
	 */
	private function render_social() {
		$social_links = get_theme_mod( 'nexus_social_links', array() );
		
		if ( empty( $social_links ) ) {
			return;
		}
		?>
		<div class="footer-social">
			<?php foreach ( $social_links as $network => $url ) : ?>
				<a href="<?php echo esc_url( $url ); ?>" class="social-link social-<?php echo esc_attr( $network ); ?>" target="_blank" rel="noopener">
					<span class="screen-reader-text"><?php echo esc_html( ucfirst( $network ) ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render Copyright
	 */
	private function render_copyright() {
		$copyright = get_theme_mod( 'nexus_copyright_text', sprintf( '&copy; %s %s', date( 'Y' ), get_bloginfo( 'name' ) ) );
		?>
		<div class="footer-copyright">
			<?php echo wp_kses_post( $copyright ); ?>
		</div>
		<?php
	}

	/**
	 * Render Contact
	 */
	private function render_contact() {
		$phone   = get_theme_mod( 'nexus_contact_phone' );
		$email   = get_theme_mod( 'nexus_contact_email' );
		$address = get_theme_mod( 'nexus_contact_address' );
		?>
		<div class="footer-contact">
			<?php if ( $phone ) : ?>
				<p class="contact-phone">
					<strong><?php esc_html_e( 'Phone:', 'nexus' ); ?></strong>
					<a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a>
				</p>
			<?php endif; ?>
			<?php if ( $email ) : ?>
				<p class="contact-email">
					<strong><?php esc_html_e( 'Email:', 'nexus' ); ?></strong>
					<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
				</p>
			<?php endif; ?>
			<?php if ( $address ) : ?>
				<p class="contact-address">
					<strong><?php esc_html_e( 'Address:', 'nexus' ); ?></strong>
					<?php echo esc_html( $address ); ?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render Newsletter
	 */
	private function render_newsletter() {
		?>
		<div class="footer-newsletter">
			<h4><?php esc_html_e( 'Newsletter', 'nexus' ); ?></h4>
			<form class="newsletter-form" method="post">
				<input type="email" name="email" placeholder="<?php esc_attr_e( 'Your email', 'nexus' ); ?>" required>
				<button type="submit"><?php esc_html_e( 'Subscribe', 'nexus' ); ?></button>
			</form>
		</div>
		<?php
	}
}
