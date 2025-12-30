<?php
/**
 * Simulation Engine - Circuit simulation and analysis
 *
 * @package Nexus_Pro
 * @subpackage Circuit_Simulator
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Simulation Engine Class
 *
 * Performs electrical circuit simulation and analysis
 */
class Nexus_Simulation_Engine {

	/**
	 * Instance
	 *
	 * @var Nexus_Simulation_Engine
	 */
	private static $instance = null;

	/**
	 * Component library
	 *
	 * @var Nexus_Component_Library
	 */
	private $component_library;

	/**
	 * Get instance
	 *
	 * @return Nexus_Simulation_Engine
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
		$this->component_library = Nexus_Component_Library::get_instance();
	}

	/**
	 * Simulate circuit
	 *
	 * @param array $circuit_data Circuit data.
	 * @return array Simulation results.
	 */
	public function simulate( $circuit_data ) {
		if ( empty( $circuit_data['components'] ) ) {
			return array(
				'success' => false,
				'error'   => __( 'No components in circuit', 'nexus-pro' ),
			);
		}

		// Build circuit netlist
		$netlist = $this->build_netlist( $circuit_data );

		// Validate circuit
		$validation = $this->validate_circuit( $netlist );
		if ( ! $validation['valid'] ) {
			return array(
				'success' => false,
				'error'   => $validation['error'],
			);
		}

		// Perform DC analysis
		$dc_results = $this->dc_analysis( $netlist );

		// Calculate component values
		$component_results = $this->calculate_component_values( $netlist, $dc_results );

		// Generate reports
		$reports = $this->generate_reports( $dc_results, $component_results );

		return array(
			'success'    => true,
			'dc'         => $dc_results,
			'components' => $component_results,
			'reports'    => $reports,
			'netlist'    => $netlist,
		);
	}

	/**
	 * Build netlist from circuit data
	 *
	 * @param array $circuit_data Circuit data.
	 * @return array Netlist.
	 */
	private function build_netlist( $circuit_data ) {
		$netlist = array(
			'components' => array(),
			'nodes'      => array(),
			'connections' => array(),
		);

		// Process components
		foreach ( $circuit_data['components'] as $component ) {
			$netlist['components'][ $component['id'] ] = array(
				'type'       => $component['type'],
				'properties' => $component['properties'],
				'nodes'      => $component['nodes'],
			);
		}

		// Process connections
		if ( isset( $circuit_data['connections'] ) ) {
			foreach ( $circuit_data['connections'] as $connection ) {
				$netlist['connections'][] = array(
					'from' => $connection['from'],
					'to'   => $connection['to'],
					'node' => $connection['node'],
				);
			}
		}

		// Build node list
		$netlist['nodes'] = $this->extract_nodes( $netlist );

		return $netlist;
	}

	/**
	 * Extract unique nodes from circuit
	 *
	 * @param array $netlist Netlist.
	 * @return array Nodes.
	 */
	private function extract_nodes( $netlist ) {
		$nodes = array();

		foreach ( $netlist['components'] as $component_id => $component ) {
			if ( isset( $component['nodes'] ) ) {
				foreach ( $component['nodes'] as $node ) {
					if ( ! in_array( $node, $nodes, true ) ) {
						$nodes[] = $node;
					}
				}
			}
		}

		return $nodes;
	}

	/**
	 * Validate circuit
	 *
	 * @param array $netlist Netlist.
	 * @return array Validation result.
	 */
	private function validate_circuit( $netlist ) {
		// Check for ground
		$has_ground = false;
		foreach ( $netlist['components'] as $component ) {
			if ( $component['type'] === 'ground' ) {
				$has_ground = true;
				break;
			}
		}

		if ( ! $has_ground ) {
			return array(
				'valid' => false,
				'error' => __( 'Circuit must have at least one ground connection', 'nexus-pro' ),
			);
		}

		// Check for power source
		$has_source = false;
		foreach ( $netlist['components'] as $component ) {
			if ( in_array( $component['type'], array( 'battery', 'ac_source' ), true ) ) {
				$has_source = true;
				break;
			}
		}

		if ( ! $has_source ) {
			return array(
				'valid' => false,
				'error' => __( 'Circuit must have at least one power source', 'nexus-pro' ),
			);
		}

		// Check for floating nodes
		if ( count( $netlist['nodes'] ) < 2 ) {
			return array(
				'valid' => false,
				'error' => __( 'Circuit must have at least 2 connected nodes', 'nexus-pro' ),
			);
		}

		return array( 'valid' => true );
	}

	/**
	 * DC analysis
	 *
	 * @param array $netlist Netlist.
	 * @return array DC analysis results.
	 */
	private function dc_analysis( $netlist ) {
		$results = array(
			'node_voltages' => array(),
			'branch_currents' => array(),
			'total_power' => 0,
		);

		// Initialize node voltages (ground = 0V)
		foreach ( $netlist['nodes'] as $node ) {
			$results['node_voltages'][ $node ] = 0;
		}

		// Find voltage sources and set node voltages
		foreach ( $netlist['components'] as $component_id => $component ) {
			if ( $component['type'] === 'battery' ) {
				$voltage = isset( $component['properties']['voltage'] ) ? floatval( $component['properties']['voltage'] ) : 9;
				if ( isset( $component['nodes'][0] ) ) {
					$results['node_voltages'][ $component['nodes'][0] ] = $voltage;
				}
			}
		}

		// Calculate branch currents (simplified Ohm's law)
		foreach ( $netlist['components'] as $component_id => $component ) {
			if ( $component['type'] === 'resistor' && isset( $component['nodes'][0], $component['nodes'][1] ) ) {
				$resistance = isset( $component['properties']['resistance'] ) ? floatval( $component['properties']['resistance'] ) : 1000;
				$v1 = $results['node_voltages'][ $component['nodes'][0] ];
				$v2 = $results['node_voltages'][ $component['nodes'][1] ];
				$current = abs( $v1 - $v2 ) / $resistance;
				
				$results['branch_currents'][ $component_id ] = $current;
				$results['total_power'] += $current * $current * $resistance;
			}
		}

		return $results;
	}

	/**
	 * Calculate component values
	 *
	 * @param array $netlist Netlist.
	 * @param array $dc_results DC analysis results.
	 * @return array Component calculations.
	 */
	private function calculate_component_values( $netlist, $dc_results ) {
		$results = array();

		foreach ( $netlist['components'] as $component_id => $component ) {
			$component_info = $this->component_library->get_component( $component['type'] );
			
			if ( ! $component_info ) {
				continue;
			}

			$calc = array(
				'type'    => $component['type'],
				'name'    => $component_info['name'],
				'voltage' => 0,
				'current' => 0,
				'power'   => 0,
			);

			// Calculate voltage across component
			if ( isset( $component['nodes'][0], $component['nodes'][1] ) ) {
				$v1 = isset( $dc_results['node_voltages'][ $component['nodes'][0] ] ) ? $dc_results['node_voltages'][ $component['nodes'][0] ] : 0;
				$v2 = isset( $dc_results['node_voltages'][ $component['nodes'][1] ] ) ? $dc_results['node_voltages'][ $component['nodes'][1] ] : 0;
				$calc['voltage'] = abs( $v1 - $v2 );
			}

			// Get current through component
			if ( isset( $dc_results['branch_currents'][ $component_id ] ) ) {
				$calc['current'] = $dc_results['branch_currents'][ $component_id ];
			}

			// Calculate power
			$calc['power'] = $calc['voltage'] * $calc['current'];

			// Component-specific calculations
			switch ( $component['type'] ) {
				case 'resistor':
					$calc['resistance'] = isset( $component['properties']['resistance'] ) ? $component['properties']['resistance'] : 1000;
					break;
				
				case 'led':
					$calc['color'] = isset( $component['properties']['color'] ) ? $component['properties']['color'] : 'red';
					$calc['state'] = ( $calc['voltage'] >= 1.8 ) ? 'on' : 'off';
					break;
				
				case 'capacitor':
					$calc['capacitance'] = isset( $component['properties']['capacitance'] ) ? $component['properties']['capacitance'] : 0.000001;
					break;
			}

			$results[ $component_id ] = $calc;
		}

		return $results;
	}

	/**
	 * Generate reports
	 *
	 * @param array $dc_results DC analysis results.
	 * @param array $component_results Component calculations.
	 * @return array Reports.
	 */
	private function generate_reports( $dc_results, $component_results ) {
		$reports = array(
			'summary' => array(),
			'warnings' => array(),
			'recommendations' => array(),
		);

		// Summary
		$reports['summary'] = array(
			'total_components' => count( $component_results ),
			'total_power'      => round( $dc_results['total_power'], 4 ),
			'max_voltage'      => max( $dc_results['node_voltages'] ),
			'max_current'      => max( $dc_results['branch_currents'] ),
		);

		// Check for warnings
		foreach ( $component_results as $component_id => $calc ) {
			// Check for excessive power dissipation in resistors
			if ( $calc['type'] === 'resistor' && $calc['power'] > 0.25 ) {
				$reports['warnings'][] = sprintf(
					__( 'Resistor %s exceeds power rating (%.2fW)', 'nexus-pro' ),
					$component_id,
					$calc['power']
				);
			}

			// Check for reverse bias on LEDs
			if ( $calc['type'] === 'led' && $calc['voltage'] < 0 ) {
				$reports['warnings'][] = sprintf(
					__( 'LED %s may be reverse biased', 'nexus-pro' ),
					$component_id
				);
			}
		}

		// Recommendations
		if ( $dc_results['total_power'] > 1 ) {
			$reports['recommendations'][] = __( 'Consider adding heat sinks for high power components', 'nexus-pro' );
		}

		return $reports;
	}

	/**
	 * Perform AC analysis
	 *
	 * @param array $netlist Netlist.
	 * @param float $frequency Frequency in Hz.
	 * @return array AC analysis results.
	 */
	public function ac_analysis( $netlist, $frequency = 1000 ) {
		// Simplified AC analysis
		$results = array(
			'frequency' => $frequency,
			'impedances' => array(),
			'phase_angles' => array(),
		);

		foreach ( $netlist['components'] as $component_id => $component ) {
			switch ( $component['type'] ) {
				case 'capacitor':
					$capacitance = isset( $component['properties']['capacitance'] ) ? floatval( $component['properties']['capacitance'] ) : 0.000001;
					$impedance = 1 / ( 2 * pi() * $frequency * $capacitance );
					$results['impedances'][ $component_id ] = $impedance;
					$results['phase_angles'][ $component_id ] = -90;
					break;

				case 'inductor':
					$inductance = isset( $component['properties']['inductance'] ) ? floatval( $component['properties']['inductance'] ) : 0.001;
					$impedance = 2 * pi() * $frequency * $inductance;
					$results['impedances'][ $component_id ] = $impedance;
					$results['phase_angles'][ $component_id ] = 90;
					break;

				case 'resistor':
					$resistance = isset( $component['properties']['resistance'] ) ? floatval( $component['properties']['resistance'] ) : 1000;
					$results['impedances'][ $component_id ] = $resistance;
					$results['phase_angles'][ $component_id ] = 0;
					break;
			}
		}

		return $results;
	}
}
