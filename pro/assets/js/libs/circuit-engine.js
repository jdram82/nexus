/**
 * Circuit Engine - Physics and electrical calculations
 *
 * @package Nexus_Pro
 * @subpackage Circuit_Simulator
 * @since 3.0.0
 */

const CircuitEngine = (function() {
	'use strict';

	/**
	 * Constants
	 */
	const CONSTANTS = {
		ELECTRON_CHARGE: 1.602176634e-19,
		BOLTZMANN: 1.380649e-23,
		THERMAL_VOLTAGE: 0.026,
		MIN_RESISTANCE: 0.001,
		MAX_RESISTANCE: 1e12,
		MIN_CAPACITANCE: 1e-15,
		MAX_CAPACITANCE: 1,
		MIN_INDUCTANCE: 1e-12,
		MAX_INDUCTANCE: 100
	};

	/**
	 * Circuit Node
	 */
	class CircuitNode {
		constructor(id) {
			this.id = id;
			this.voltage = 0;
			this.connections = [];
			this.isGround = false;
		}

		addConnection(component) {
			if (!this.connections.includes(component)) {
				this.connections.push(component);
			}
		}
	}

	/**
	 * Circuit Component Base Class
	 */
	class Component {
		constructor(id, type, nodes) {
			this.id = id;
			this.type = type;
			this.nodes = nodes || [];
			this.current = 0;
			this.voltage = 0;
			this.power = 0;
		}

		getCurrent() {
			return this.current;
		}

		getVoltage() {
			return this.voltage;
		}

		getPower() {
			return this.power;
		}

		update(dt) {
			// Override in subclasses
		}
	}

	/**
	 * Resistor Component
	 */
	class Resistor extends Component {
		constructor(id, nodes, resistance) {
			super(id, 'resistor', nodes);
			this.resistance = Math.max(CONSTANTS.MIN_RESISTANCE, resistance || 1000);
		}

		getConductance() {
			return 1 / this.resistance;
		}

		calculateCurrent(v1, v2) {
			this.voltage = v1 - v2;
			this.current = this.voltage / this.resistance;
			this.power = this.voltage * this.current;
			return this.current;
		}
	}

	/**
	 * Capacitor Component
	 */
	class Capacitor extends Component {
		constructor(id, nodes, capacitance) {
			super(id, 'capacitor', nodes);
			this.capacitance = Math.max(CONSTANTS.MIN_CAPACITANCE, capacitance || 1e-6);
			this.charge = 0;
		}

		getImpedance(frequency) {
			if (frequency === 0) return Infinity;
			return 1 / (2 * Math.PI * frequency * this.capacitance);
		}

		update(dt, v1, v2) {
			const dv = v1 - v2;
			const targetCharge = this.capacitance * dv;
			const dq = (targetCharge - this.charge) * 0.1; // Simplified integration
			
			this.current = dq / dt;
			this.charge = this.charge + dq;
			this.voltage = dv;
			this.power = this.voltage * this.current;
		}
	}

	/**
	 * Inductor Component
	 */
	class Inductor extends Component {
		constructor(id, nodes, inductance) {
			super(id, 'inductor', nodes);
			this.inductance = Math.max(CONSTANTS.MIN_INDUCTANCE, inductance || 1e-3);
			this.flux = 0;
		}

		getImpedance(frequency) {
			return 2 * Math.PI * frequency * this.inductance;
		}

		update(dt, v1, v2) {
			this.voltage = v1 - v2;
			const dFlux = this.voltage * dt;
			this.flux += dFlux;
			this.current = this.flux / this.inductance;
			this.power = this.voltage * this.current;
		}
	}

	/**
	 * Voltage Source
	 */
	class VoltageSource extends Component {
		constructor(id, nodes, voltage) {
			super(id, 'battery', nodes);
			this.sourceVoltage = voltage || 9;
		}

		getVoltage() {
			return this.sourceVoltage;
		}
	}

	/**
	 * Diode Component
	 */
	class Diode extends Component {
		constructor(id, nodes, forwardVoltage) {
			super(id, 'diode', nodes);
			this.forwardVoltage = forwardVoltage || 0.7;
			this.saturationCurrent = 1e-12;
		}

		calculateCurrent(v1, v2) {
			this.voltage = v1 - v2;
			
			if (this.voltage >= this.forwardVoltage) {
				// Forward bias - exponential model
				const vt = CONSTANTS.THERMAL_VOLTAGE;
				this.current = this.saturationCurrent * (Math.exp(this.voltage / vt) - 1);
			} else {
				// Reverse bias - negligible current
				this.current = -this.saturationCurrent;
			}
			
			this.power = this.voltage * this.current;
			return this.current;
		}
	}

	/**
	 * LED Component
	 */
	class LED extends Diode {
		constructor(id, nodes, color, forwardVoltage) {
			super(id, nodes, forwardVoltage || 2.0);
			this.color = color || 'red';
			this.brightness = 0;
		}

		calculateCurrent(v1, v2) {
			const current = super.calculateCurrent(v1, v2);
			
			// Calculate brightness (0-100%)
			if (this.current > 0) {
				this.brightness = Math.min(100, (this.current / 0.02) * 100);
			} else {
				this.brightness = 0;
			}
			
			return current;
		}

		isOn() {
			return this.brightness > 0;
		}
	}

	/**
	 * Circuit Solver
	 */
	class CircuitSolver {
		constructor() {
			this.nodes = new Map();
			this.components = new Map();
			this.groundNode = null;
		}

		addNode(id) {
			if (!this.nodes.has(id)) {
				this.nodes.set(id, new CircuitNode(id));
			}
			return this.nodes.get(id);
		}

		addComponent(component) {
			this.components.set(component.id, component);
			
			// Register component with its nodes
			component.nodes.forEach(nodeId => {
				const node = this.addNode(nodeId);
				node.addConnection(component);
			});
		}

		setGround(nodeId) {
			const node = this.addNode(nodeId);
			node.isGround = true;
			node.voltage = 0;
			this.groundNode = node;
		}

		/**
		 * Solve DC circuit using nodal analysis
		 */
		solveDC() {
			if (!this.groundNode) {
				throw new Error('Circuit must have a ground node');
			}

			const maxIterations = 100;
			const tolerance = 1e-6;
			
			// Initialize voltages
			this.nodes.forEach(node => {
				if (!node.isGround) {
					node.voltage = 0;
				}
			});

			// Apply voltage sources
			this.components.forEach(comp => {
				if (comp.type === 'battery' && comp.nodes.length >= 2) {
					const positiveNode = this.nodes.get(comp.nodes[0]);
					if (positiveNode) {
						positiveNode.voltage = comp.sourceVoltage;
					}
				}
			});

			// Iterative solution using Gauss-Seidel method
			for (let iter = 0; iter < maxIterations; iter++) {
				let maxChange = 0;

				this.nodes.forEach(node => {
					if (node.isGround) return;

					let sumConductance = 0;
					let sumCurrent = 0;

					// Sum contributions from all connected components
					node.connections.forEach(comp => {
						if (comp instanceof Resistor) {
							const otherNodeId = comp.nodes.find(id => id !== node.id);
							if (otherNodeId) {
								const otherNode = this.nodes.get(otherNodeId);
								const conductance = comp.getConductance();
								sumConductance += conductance;
								sumCurrent += conductance * otherNode.voltage;
							}
						}
					});

					if (sumConductance > 0) {
						const newVoltage = sumCurrent / sumConductance;
						const change = Math.abs(newVoltage - node.voltage);
						maxChange = Math.max(maxChange, change);
						node.voltage = newVoltage;
					}
				});

				if (maxChange < tolerance) {
					break;
				}
			}

			// Calculate component currents
			this.components.forEach(comp => {
				if (comp.nodes.length >= 2) {
					const v1 = this.nodes.get(comp.nodes[0])?.voltage || 0;
					const v2 = this.nodes.get(comp.nodes[1])?.voltage || 0;

					if (comp instanceof Resistor || comp instanceof Diode) {
						comp.calculateCurrent(v1, v2);
					}
				}
			});

			return this.getResults();
		}

		/**
		 * Transient analysis
		 */
		solveTransient(duration, timeStep) {
			const steps = Math.floor(duration / timeStep);
			const results = [];

			for (let i = 0; i < steps; i++) {
				const time = i * timeStep;

				// Update reactive components
				this.components.forEach(comp => {
					if ((comp instanceof Capacitor || comp instanceof Inductor) && comp.nodes.length >= 2) {
						const v1 = this.nodes.get(comp.nodes[0])?.voltage || 0;
						const v2 = this.nodes.get(comp.nodes[1])?.voltage || 0;
						comp.update(timeStep, v1, v2);
					}
				});

				// Solve DC point
				this.solveDC();

				// Store results
				results.push({
					time: time,
					voltages: this.getNodeVoltages(),
					currents: this.getComponentCurrents()
				});
			}

			return results;
		}

		/**
		 * AC analysis
		 */
		solveAC(frequency) {
			const results = {
				frequency: frequency,
				impedances: {},
				phases: {}
			};

			this.components.forEach((comp, id) => {
				if (comp instanceof Capacitor) {
					results.impedances[id] = comp.getImpedance(frequency);
					results.phases[id] = -90; // Capacitor lags
				} else if (comp instanceof Inductor) {
					results.impedances[id] = comp.getImpedance(frequency);
					results.phases[id] = 90; // Inductor leads
				} else if (comp instanceof Resistor) {
					results.impedances[id] = comp.resistance;
					results.phases[id] = 0;
				}
			});

			return results;
		}

		/**
		 * Get results
		 */
		getResults() {
			return {
				nodeVoltages: this.getNodeVoltages(),
				componentCurrents: this.getComponentCurrents(),
				componentVoltages: this.getComponentVoltages(),
				power: this.getTotalPower()
			};
		}

		getNodeVoltages() {
			const voltages = {};
			this.nodes.forEach((node, id) => {
				voltages[id] = node.voltage;
			});
			return voltages;
		}

		getComponentCurrents() {
			const currents = {};
			this.components.forEach((comp, id) => {
				currents[id] = comp.getCurrent();
			});
			return currents;
		}

		getComponentVoltages() {
			const voltages = {};
			this.components.forEach((comp, id) => {
				voltages[id] = comp.getVoltage();
			});
			return voltages;
		}

		getTotalPower() {
			let total = 0;
			this.components.forEach(comp => {
				total += Math.abs(comp.getPower());
			});
			return total;
		}

		reset() {
			this.nodes.clear();
			this.components.clear();
			this.groundNode = null;
		}
	}

	/**
	 * Public API
	 */
	return {
		CONSTANTS,
		CircuitNode,
		Component,
		Resistor,
		Capacitor,
		Inductor,
		VoltageSource,
		Diode,
		LED,
		CircuitSolver
	};

})();

// Export for Node.js environments
if (typeof module !== 'undefined' && module.exports) {
	module.exports = CircuitEngine;
}
