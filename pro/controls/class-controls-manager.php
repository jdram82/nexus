<?php
/**
 * Advanced Controls Manager - Custom WordPress Customizer controls
 *
 * @package Nexus_Pro
 * @subpackage Controls
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Controls Manager Class
 */
class Nexus_Controls_Manager {

    /**
     * Instance
     *
     * @var Nexus_Controls_Manager
     */
    private static $instance = null;

    /**
     * Registered controls
     *
     * @var array
     */
    private $controls = array();

    /**
     * Get instance
     *
     * @return Nexus_Controls_Manager
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'customize_register', array( $this, 'register_controls' ), 1 );
        add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ) );
    }

    /**
     * Register custom controls
     *
     * @param WP_Customize_Manager $wp_customize Customizer instance.
     */
    public function register_controls( $wp_customize ) {
        // Load control classes
        require_once NEXUS_PRO_PATH . 'controls/class-typography-control.php';
        require_once NEXUS_PRO_PATH . 'controls/class-gradient-control.php';
        require_once NEXUS_PRO_PATH . 'controls/class-shadow-control.php';
        require_once NEXUS_PRO_PATH . 'controls/class-border-control.php';
        require_once NEXUS_PRO_PATH . 'controls/class-spacing-control.php';
        require_once NEXUS_PRO_PATH . 'controls/class-icon-picker-control.php';

        // Register controls
        $wp_customize->register_control_type( 'Nexus_Typography_Control' );
        $wp_customize->register_control_type( 'Nexus_Gradient_Control' );
        $wp_customize->register_control_type( 'Nexus_Shadow_Control' );
        $wp_customize->register_control_type( 'Nexus_Border_Control' );
        $wp_customize->register_control_type( 'Nexus_Spacing_Control' );
        $wp_customize->register_control_type( 'Nexus_Icon_Picker_Control' );
    }

    /**
     * Enqueue control scripts and styles
     */
    public function enqueue_control_scripts() {
        wp_enqueue_style(
            'nexus-controls',
            NEXUS_PRO_URL . 'assets/css/controls.css',
            array( 'customize-controls' ),
            '3.0.0'
        );

        wp_enqueue_script(
            'nexus-controls',
            NEXUS_PRO_URL . 'assets/js/controls.js',
            array( 'customize-controls', 'jquery', 'wp-color-picker' ),
            '3.0.0',
            true
        );

        wp_localize_script( 'nexus-controls', 'nexusControls', array(
            'fonts' => $this->get_google_fonts(),
            'i18n'  => array(
                'selectFont' => __( 'Select Font', 'nexus-pro' ),
                'selectIcon' => __( 'Select Icon', 'nexus-pro' ),
            ),
        ) );
    }

    /**
     * Get Google Fonts list
     *
     * @return array
     */
    private function get_google_fonts() {
        return array(
            'Roboto'          => 'Roboto',
            'Open Sans'       => 'Open Sans',
            'Lato'            => 'Lato',
            'Montserrat'      => 'Montserrat',
            'Poppins'         => 'Poppins',
            'Raleway'         => 'Raleway',
            'Inter'           => 'Inter',
            'Playfair Display' => 'Playfair Display',
            'Merriweather'    => 'Merriweather',
            'PT Sans'         => 'PT Sans',
        );
    }

    /**
     * Register a custom control
     *
     * @param string $type Control type.
     * @param array  $args Control arguments.
     */
    public function register_control( $type, $args = array() ) {
        $this->controls[ $type ] = $args;
    }

    /**
     * Get registered controls
     *
     * @return array
     */
    public function get_controls() {
        return $this->controls;
    }
}

// Initialize
Nexus_Controls_Manager::get_instance();
