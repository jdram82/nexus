<?php
/**
 * Header Builder
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Header Builder Class
 */
class Nexus_Header_Builder {

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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'nexus_header_output', array( $this, 'render_header' ) );
	}

	/**
	 * Register Customizer
	 */
	public function register_customizer( $wp_customize ) {
		// Header Builder Panel
		$wp_customize->add_panel(
			'nexus_header_builder',
			array(
				'title'       => __( 'Header Builder', 'nexus' ),
				'description' => __( 'Build your custom header layout', 'nexus' ),
				'priority'    => 30,
			)
		);

		// Layout Section
		$wp_customize->add_section(
			'nexus_header_layout',
			array(
				'title' => __( 'Header Layout', 'nexus' ),
				'panel' => 'nexus_header_builder',
			)
		);

		// Header Style
		$wp_customize->add_setting(
			'nexus_header_style',
			array(
				'default'   => 'default',
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			'nexus_header_style',
			array(
				'label'   => __( 'Header Style', 'nexus' ),
				'section' => 'nexus_header_layout',
				'type'    => 'select',
				'choices' => array(
					'default'     => __( 'Default', 'nexus' ),
					'transparent' => __( 'Transparent', 'nexus' ),
					'sticky'      => __( 'Sticky', 'nexus' ),
					'custom'      => __( 'Custom Builder', 'nexus' ),
				),
			)
		);

		// Header Rows
		$wp_customize->add_setting(
			'nexus_header_rows',
			array(
				'default'   => array( 'top', 'main', 'bottom' ),
				'transport' => 'refresh',
			)
		);

		// Top Row Section
		$this->add_row_section( $wp_customize, 'top', __( 'Top Row', 'nexus' ) );
		$this->add_row_section( $wp_customize, 'main', __( 'Main Row', 'nexus' ) );
		$this->add_row_section( $wp_customize, 'bottom', __( 'Bottom Row', 'nexus' ) );
	}

	/**
	 * Add Row Section
	 */
	private function add_row_section( $wp_customize, $row, $title ) {
		$wp_customize->add_section(
			'nexus_header_' . $row . '_row',
			array(
				'title' => $title,
				'panel' => 'nexus_header_builder',
			)
		);

		// Enable Row
		$wp_customize->add_setting(
			'nexus_header_' . $row . '_enabled',
			array(
				'default'   => 'main' === $row ? true : false,
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			'nexus_header_' . $row . '_enabled',
			array(
				'label'   => __( 'Enable Row', 'nexus' ),
				'section' => 'nexus_header_' . $row . '_row',
				'type'    => 'checkbox',
			)
		);

		// Left Elements
		$wp_customize->add_setting(
			'nexus_header_' . $row . '_left',
			array(
				'default'   => 'main' === $row ? array( 'logo' ) : array(),
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			'nexus_header_' . $row . '_left',
			array(
				'label'       => __( 'Left Elements', 'nexus' ),
				'section'     => 'nexus_header_' . $row . '_row',
				'type'        => 'select',
				'multiple'    => true,
				'choices'     => $this->get_element_choices(),
			)
		);

		// Center Elements
		$wp_customize->add_setting(
			'nexus_header_' . $row . '_center',
			array(
				'default'   => array(),
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			'nexus_header_' . $row . '_center',
			array(
				'label'       => __( 'Center Elements', 'nexus' ),
				'section'     => 'nexus_header_' . $row . '_row',
				'type'        => 'select',
				'multiple'    => true,
				'choices'     => $this->get_element_choices(),
			)
		);

		// Right Elements
		$wp_customize->add_setting(
			'nexus_header_' . $row . '_right',
			array(
				'default'   => 'main' === $row ? array( 'menu' ) : array(),
				'transport' => 'refresh',
			)
		);

		$wp_customize->add_control(
			'nexus_header_' . $row . '_right',
			array(
				'label'       => __( 'Right Elements', 'nexus' ),
				'section'     => 'nexus_header_' . $row . '_row',
				'type'        => 'select',
				'multiple'    => true,
				'choices'     => $this->get_element_choices(),
			)
		);
	}

	/**
	 * Get Element Choices
	 */
	private function get_element_choices() {
		return array(
			'logo'         => __( 'Logo', 'nexus' ),
			'menu'         => __( 'Menu', 'nexus' ),
			'search'       => __( 'Search', 'nexus' ),
			'cart'         => __( 'Cart', 'nexus' ),
			'account'      => __( 'Account', 'nexus' ),
			'social'       => __( 'Social Icons', 'nexus' ),
			'button'       => __( 'Button', 'nexus' ),
			'text'         => __( 'Text/HTML', 'nexus' ),
			'widget'       => __( 'Widget Area', 'nexus' ),
		);
	}

	/**
	 * Enqueue Scripts
	 */
	public function enqueue_scripts() {
		if ( is_customize_preview() ) {
			wp_enqueue_script(
				'nexus-header-builder',
				NEXUS_PRO_URI . '/assets/js/header-builder.js',
				array( 'jquery', 'customize-preview' ),
				NEXUS_PRO_VERSION,
				true
			);
		}

		wp_enqueue_style(
			'nexus-header-builder',
			NEXUS_PRO_URI . '/assets/css/header-builder.css',
			array(),
			NEXUS_PRO_VERSION
		);
	}

	/**
	 * Render Header
	 */
	public function render_header( $content ) {
		$style = get_theme_mod( 'nexus_header_style', 'default' );

		if ( 'custom' !== $style ) {
			return $content;
		}

		ob_start();
		?>
		<header id="masthead" class="nexus-header-builder site-header">
			<?php
			$rows = array( 'top', 'main', 'bottom' );
			foreach ( $rows as $row ) {
				if ( get_theme_mod( 'nexus_header_' . $row . '_enabled', 'main' === $row ) ) {
					$this->render_row( $row );
				}
			}
			?>
		</header>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render Row
	 */
	private function render_row( $row ) {
		$left   = get_theme_mod( 'nexus_header_' . $row . '_left', array() );
		$center = get_theme_mod( 'nexus_header_' . $row . '_center', array() );
		$right  = get_theme_mod( 'nexus_header_' . $row . '_right', array() );

		if ( empty( $left ) && empty( $center ) && empty( $right ) ) {
			return;
		}
		?>
		<div class="header-row header-<?php echo esc_attr( $row ); ?>-row">
			<div class="container">
				<div class="header-columns">
					<?php if ( ! empty( $left ) ) : ?>
						<div class="header-column header-left">
							<?php $this->render_elements( $left ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $center ) ) : ?>
						<div class="header-column header-center">
							<?php $this->render_elements( $center ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $right ) ) : ?>
						<div class="header-column header-right">
							<?php $this->render_elements( $right ); ?>
						</div>
					<?php endif; ?>
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
		do_action( 'nexus_header_element_' . $element );

		switch ( $element ) {
			case 'logo':
				$this->render_logo();
				break;
			case 'menu':
				$this->render_menu();
				break;
			case 'search':
				$this->render_search();
				break;
			case 'cart':
				$this->render_cart();
				break;
			case 'account':
				$this->render_account();
				break;
			case 'social':
				$this->render_social();
				break;
			case 'button':
				$this->render_button();
				break;
		}
	}

	/**
	 * Render Logo
	 */
	private function render_logo() {
		?>
		<div class="header-element header-logo">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-title">
					<?php bloginfo( 'name' ); ?>
				</a>
				<?php
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render Menu
	 */
	private function render_menu() {
		?>
		<nav class="header-element header-menu">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_class'     => 'primary-menu',
					'container'      => false,
					'fallback_cb'    => 'nexus_primary_menu_fallback',
				)
			);
			?>
		</nav>
		<?php
	}

	/**
	 * Render Search
	 */
	private function render_search() {
		?>
		<div class="header-element header-search">
			<button class="search-toggle" aria-label="<?php esc_attr_e( 'Toggle search', 'nexus' ); ?>">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
					<path d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"/>
				</svg>
			</button>
			<div class="search-popup">
				<?php get_search_form(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Cart
	 */
	private function render_cart() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		?>
		<div class="header-element header-cart">
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-link">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
					<path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
				</svg>
				<span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
			</a>
		</div>
		<?php
	}

	/**
	 * Render Account
	 */
	private function render_account() {
		?>
		<div class="header-element header-account">
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="account-link">
					<?php esc_html_e( 'Logout', 'nexus' ); ?>
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="account-link">
					<?php esc_html_e( 'Login', 'nexus' ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
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
		<div class="header-element header-social">
			<?php foreach ( $social_links as $network => $url ) : ?>
				<a href="<?php echo esc_url( $url ); ?>" class="social-link social-<?php echo esc_attr( $network ); ?>" target="_blank" rel="noopener">
					<span class="screen-reader-text"><?php echo esc_html( ucfirst( $network ) ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render Button
	 */
	private function render_button() {
		$button_text = get_theme_mod( 'nexus_header_button_text', __( 'Get Started', 'nexus' ) );
		$button_url  = get_theme_mod( 'nexus_header_button_url', '#' );
		?>
		<div class="header-element header-button">
			<a href="<?php echo esc_url( $button_url ); ?>" class="button">
				<?php echo esc_html( $button_text ); ?>
			</a>
		</div>
		<?php
	}
}
