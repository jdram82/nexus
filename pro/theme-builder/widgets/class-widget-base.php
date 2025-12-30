<?php
/**
 * Base Widget Class
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base Widget Class
 */
abstract class Nexus_Widget_Base {

	/**
	 * Widget settings
	 *
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Controls registry
	 *
	 * @var array
	 */
	protected $controls = array();

	/**
	 * Current section
	 *
	 * @var string
	 */
	protected $current_section = '';

	/**
	 * Constructor
	 *
	 * @param array $settings Widget settings.
	 */
	public function __construct( $settings = array() ) {
		$this->settings = $settings;
		$this->register_controls();
	}

	/**
	 * Get widget name
	 *
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Get widget title
	 *
	 * @return string
	 */
	abstract public function get_title();

	/**
	 * Get widget icon
	 *
	 * @return string
	 */
	abstract public function get_icon();

	/**
	 * Get widget categories
	 *
	 * @return array
	 */
	abstract public function get_categories();

	/**
	 * Register widget controls
	 */
	abstract protected function register_controls();

	/**
	 * Render widget output
	 */
	abstract protected function render();

	/**
	 * Start controls section
	 *
	 * @param string $section_id Section ID.
	 * @param array  $args       Section arguments.
	 */
	protected function start_controls_section( $section_id, $args = array() ) {
		$this->current_section = $section_id;
		
		$this->controls[ $section_id ] = array(
			'type'     => 'section',
			'label'    => isset( $args['label'] ) ? $args['label'] : '',
			'tab'      => isset( $args['tab'] ) ? $args['tab'] : 'content',
			'controls' => array(),
		);
	}

	/**
	 * End controls section
	 */
	protected function end_controls_section() {
		$this->current_section = '';
	}

	/**
	 * Add control
	 *
	 * @param string $control_id Control ID.
	 * @param array  $args       Control arguments.
	 */
	protected function add_control( $control_id, $args = array() ) {
		if ( empty( $this->current_section ) ) {
			return;
		}

		$control = wp_parse_args( $args, array(
			'type'        => 'text',
			'label'       => '',
			'default'     => '',
			'placeholder' => '',
			'options'     => array(),
			'min'         => 0,
			'max'         => 100,
			'unit'        => '',
		) );

		$this->controls[ $this->current_section ]['controls'][ $control_id ] = $control;

		// Set default value if not already set
		if ( ! isset( $this->settings[ $control_id ] ) ) {
			$this->settings[ $control_id ] = $control['default'];
		}
	}

	/**
	 * Get settings
	 *
	 * @param string $key Optional. Setting key.
	 * @return mixed
	 */
	public function get_settings( $key = null ) {
		if ( null !== $key ) {
			return isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : null;
		}
		return $this->settings;
	}

	/**
	 * Get controls
	 *
	 * @return array
	 */
	public function get_controls() {
		return $this->controls;
	}

	/**
	 * Render widget
	 */
	public function render_widget() {
		$this->render();
	}

	/**
	 * Get widget data for export
	 *
	 * @return array
	 */
	public function get_widget_data() {
		return array(
			'name'     => $this->get_name(),
			'title'    => $this->get_title(),
			'icon'     => $this->get_icon(),
			'category' => $this->get_categories(),
			'settings' => $this->settings,
			'controls' => $this->controls,
		);
	}
}
