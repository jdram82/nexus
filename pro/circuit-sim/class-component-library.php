<?php
/**
 * Component Library - Electronic components for circuit design
 *
 * @package Nexus_Pro
 * @subpackage Circuit_Simulator
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Component Library Class
 *
 * Manages all electronic components available for circuit design
 */
class Nexus_Component_Library {

	/**
	 * Instance
	 *
	 * @var Nexus_Component_Library
	 */
	private static $instance = null;

	/**
	 * Component categories
	 *
	 * @var array
	 */
	private $categories = array();

	/**
	 * All components
	 *
	 * @var array
	 */
	private $components = array();

	/**
	 * Get instance
	 *
	 * @return Nexus_Component_Library
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
		$this->init_categories();
		$this->init_components();
	}

	/**
	 * Initialize categories
	 */
	private function init_categories() {
		$this->categories = array(
			'passive' => array(
				'name'       => __( 'Passive Components', 'nexus-pro' ),
				'icon'       => 'dashicons-admin-generic',
				'components' => array(),
			),
			'active' => array(
				'name'       => __( 'Active Components', 'nexus-pro' ),
				'icon'       => 'dashicons-superhero',
				'components' => array(),
			),
			'sources' => array(
				'name'       => __( 'Power Sources', 'nexus-pro' ),
				'icon'       => 'dashicons-lightbulb',
				'components' => array(),
			),
			'meters' => array(
				'name'       => __( 'Measuring Instruments', 'nexus-pro' ),
				'icon'       => 'dashicons-chart-line',
				'components' => array(),
			),
			'switches' => array(
				'name'       => __( 'Switches & Controls', 'nexus-pro' ),
				'icon'       => 'dashicons-admin-settings',
				'components' => array(),
			),
			'logic' => array(
				'name'       => __( 'Logic Gates', 'nexus-pro' ),
				'icon'       => 'dashicons-networking',
				'components' => array(),
			),
		);
	}

	/**
	 * Initialize components
	 */
	private function init_components() {
		// Passive Components
		$this->add_component( 'passive', array(
			'type'        => 'resistor',
			'name'        => __( 'Resistor', 'nexus-pro' ),
			'symbol'      => 'R',
			'description' => __( 'Resists current flow', 'nexus-pro' ),
			'properties'  => array(
				'resistance' => array( 'name' => 'Resistance', 'unit' => 'Ω', 'default' => 1000 ),
				'tolerance'  => array( 'name' => 'Tolerance', 'unit' => '%', 'default' => 5 ),
				'power'      => array( 'name' => 'Power Rating', 'unit' => 'W', 'default' => 0.25 ),
			),
		) );

		$this->add_component( 'passive', array(
			'type'        => 'capacitor',
			'name'        => __( 'Capacitor', 'nexus-pro' ),
			'symbol'      => 'C',
			'description' => __( 'Stores electrical energy', 'nexus-pro' ),
			'properties'  => array(
				'capacitance' => array( 'name' => 'Capacitance', 'unit' => 'F', 'default' => 0.000001 ),
				'voltage'     => array( 'name' => 'Voltage Rating', 'unit' => 'V', 'default' => 50 ),
				'type'        => array( 'name' => 'Type', 'options' => array( 'ceramic', 'electrolytic', 'tantalum' ), 'default' => 'ceramic' ),
			),
		) );

		$this->add_component( 'passive', array(
			'type'        => 'inductor',
			'name'        => __( 'Inductor', 'nexus-pro' ),
			'symbol'      => 'L',
			'description' => __( 'Stores energy in magnetic field', 'nexus-pro' ),
			'properties'  => array(
				'inductance' => array( 'name' => 'Inductance', 'unit' => 'H', 'default' => 0.001 ),
				'current'    => array( 'name' => 'Current Rating', 'unit' => 'A', 'default' => 1 ),
			),
		) );

		// Active Components
		$this->add_component( 'active', array(
			'type'        => 'led',
			'name'        => __( 'LED', 'nexus-pro' ),
			'symbol'      => 'D',
			'description' => __( 'Light Emitting Diode', 'nexus-pro' ),
			'properties'  => array(
				'color'          => array( 'name' => 'Color', 'options' => array( 'red', 'green', 'blue', 'yellow', 'white' ), 'default' => 'red' ),
				'forward_voltage' => array( 'name' => 'Forward Voltage', 'unit' => 'V', 'default' => 2.0 ),
				'current'        => array( 'name' => 'Forward Current', 'unit' => 'mA', 'default' => 20 ),
			),
		) );

		$this->add_component( 'active', array(
			'type'        => 'diode',
			'name'        => __( 'Diode', 'nexus-pro' ),
			'symbol'      => 'D',
			'description' => __( 'Allows current in one direction', 'nexus-pro' ),
			'properties'  => array(
				'forward_voltage' => array( 'name' => 'Forward Voltage', 'unit' => 'V', 'default' => 0.7 ),
				'current'        => array( 'name' => 'Max Current', 'unit' => 'A', 'default' => 1 ),
			),
		) );

		$this->add_component( 'active', array(
			'type'        => 'transistor',
			'name'        => __( 'NPN Transistor', 'nexus-pro' ),
			'symbol'      => 'Q',
			'description' => __( 'Amplifies or switches signals', 'nexus-pro' ),
			'properties'  => array(
				'type'     => array( 'name' => 'Type', 'options' => array( 'NPN', 'PNP' ), 'default' => 'NPN' ),
				'beta'     => array( 'name' => 'Beta (hFE)', 'unit' => '', 'default' => 100 ),
				'ic_max'   => array( 'name' => 'Max Collector Current', 'unit' => 'A', 'default' => 0.1 ),
			),
		) );

		$this->add_component( 'active', array(
			'type'        => 'opamp',
			'name'        => __( 'Op-Amp', 'nexus-pro' ),
			'symbol'      => 'U',
			'description' => __( 'Operational Amplifier', 'nexus-pro' ),
			'properties'  => array(
				'gain'        => array( 'name' => 'Open Loop Gain', 'unit' => 'dB', 'default' => 100 ),
				'supply'      => array( 'name' => 'Supply Voltage', 'unit' => 'V', 'default' => 15 ),
			),
		) );

		// Power Sources
		$this->add_component( 'sources', array(
			'type'        => 'battery',
			'name'        => __( 'Battery', 'nexus-pro' ),
			'symbol'      => 'V',
			'description' => __( 'DC voltage source', 'nexus-pro' ),
			'properties'  => array(
				'voltage' => array( 'name' => 'Voltage', 'unit' => 'V', 'default' => 9 ),
			),
		) );

		$this->add_component( 'sources', array(
			'type'        => 'ac_source',
			'name'        => __( 'AC Source', 'nexus-pro' ),
			'symbol'      => '~',
			'description' => __( 'AC voltage source', 'nexus-pro' ),
			'properties'  => array(
				'voltage'   => array( 'name' => 'Peak Voltage', 'unit' => 'V', 'default' => 120 ),
				'frequency' => array( 'name' => 'Frequency', 'unit' => 'Hz', 'default' => 60 ),
			),
		) );

		$this->add_component( 'sources', array(
			'type'        => 'ground',
			'name'        => __( 'Ground', 'nexus-pro' ),
			'symbol'      => '⏚',
			'description' => __( 'Circuit ground reference', 'nexus-pro' ),
			'properties'  => array(),
		) );

		// Measuring Instruments
		$this->add_component( 'meters', array(
			'type'        => 'voltmeter',
			'name'        => __( 'Voltmeter', 'nexus-pro' ),
			'symbol'      => 'V',
			'description' => __( 'Measures voltage', 'nexus-pro' ),
			'properties'  => array(
				'range' => array( 'name' => 'Range', 'unit' => 'V', 'default' => 20 ),
			),
		) );

		$this->add_component( 'meters', array(
			'type'        => 'ammeter',
			'name'        => __( 'Ammeter', 'nexus-pro' ),
			'symbol'      => 'A',
			'description' => __( 'Measures current', 'nexus-pro' ),
			'properties'  => array(
				'range' => array( 'name' => 'Range', 'unit' => 'A', 'default' => 1 ),
			),
		) );

		$this->add_component( 'meters', array(
			'type'        => 'ohmmeter',
			'name'        => __( 'Ohmmeter', 'nexus-pro' ),
			'symbol'      => 'Ω',
			'description' => __( 'Measures resistance', 'nexus-pro' ),
			'properties'  => array(
				'range' => array( 'name' => 'Range', 'unit' => 'Ω', 'default' => 10000 ),
			),
		) );

		// Switches & Controls
		$this->add_component( 'switches', array(
			'type'        => 'switch',
			'name'        => __( 'Switch', 'nexus-pro' ),
			'symbol'      => 'S',
			'description' => __( 'Opens or closes circuit', 'nexus-pro' ),
			'properties'  => array(
				'type'  => array( 'name' => 'Type', 'options' => array( 'SPST', 'SPDT', 'DPST', 'DPDT' ), 'default' => 'SPST' ),
				'state' => array( 'name' => 'Initial State', 'options' => array( 'open', 'closed' ), 'default' => 'open' ),
			),
		) );

		$this->add_component( 'switches', array(
			'type'        => 'potentiometer',
			'name'        => __( 'Potentiometer', 'nexus-pro' ),
			'symbol'      => 'POT',
			'description' => __( 'Variable resistor', 'nexus-pro' ),
			'properties'  => array(
				'resistance' => array( 'name' => 'Max Resistance', 'unit' => 'Ω', 'default' => 10000 ),
				'position'   => array( 'name' => 'Position', 'unit' => '%', 'default' => 50 ),
			),
		) );

		// Logic Gates
		$this->add_component( 'logic', array(
			'type'        => 'and_gate',
			'name'        => __( 'AND Gate', 'nexus-pro' ),
			'symbol'      => '&',
			'description' => __( 'Logical AND operation', 'nexus-pro' ),
			'properties'  => array(
				'inputs' => array( 'name' => 'Number of Inputs', 'default' => 2 ),
			),
		) );

		$this->add_component( 'logic', array(
			'type'        => 'or_gate',
			'name'        => __( 'OR Gate', 'nexus-pro' ),
			'symbol'      => '≥1',
			'description' => __( 'Logical OR operation', 'nexus-pro' ),
			'properties'  => array(
				'inputs' => array( 'name' => 'Number of Inputs', 'default' => 2 ),
			),
		) );

		$this->add_component( 'logic', array(
			'type'        => 'not_gate',
			'name'        => __( 'NOT Gate', 'nexus-pro' ),
			'symbol'      => '1',
			'description' => __( 'Logical NOT operation', 'nexus-pro' ),
			'properties'  => array(),
		) );
	}

	/**
	 * Add component to category
	 *
	 * @param string $category Category ID.
	 * @param array  $component Component data.
	 */
	private function add_component( $category, $component ) {
		$this->categories[ $category ]['components'][] = $component;
		$this->components[ $component['type'] ] = $component;
	}

	/**
	 * Get all categories
	 *
	 * @return array
	 */
	public function get_categories() {
		return apply_filters( 'nexus_circuit_component_categories', $this->categories );
	}

	/**
	 * Get all components
	 *
	 * @return array
	 */
	public function get_all_components() {
		return apply_filters( 'nexus_circuit_components', $this->components );
	}

	/**
	 * Get component by type
	 *
	 * @param string $type Component type.
	 * @return array|null
	 */
	public function get_component( $type ) {
		return isset( $this->components[ $type ] ) ? $this->components[ $type ] : null;
	}

	/**
	 * Get component properties
	 *
	 * @param string $type Component type.
	 * @return array
	 */
	public function get_component_properties( $type ) {
		$component = $this->get_component( $type );
		return $component ? $component['properties'] : array();
	}
}
