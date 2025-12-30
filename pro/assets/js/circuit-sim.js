/**
 * Circuit Simulator JavaScript
 *
 * @package Nexus_Pro
 * @subpackage Circuit_Simulator
 * @since 3.0.0
 */

(function($) {
	'use strict';

	/**
	 * Circuit Simulator
	 */
	const CircuitSimulator = {
		// Canvas
		canvas: null,
		ctx: null,
		
		// State
		components: [],
		connections: [],
		selectedComponent: null,
		selectedConnection: null,
		
		// Drawing
		isDragging: false,
		isDrawingWire: false,
		draggedComponent: null,
		wireStart: null,
		
		// History
		history: [],
		historyIndex: -1,
		maxHistory: 50,
		
		// Zoom
		zoom: 1,
		panX: 0,
		panY: 0,

		/**
		 * Initialize
		 */
		init: function() {
			this.canvas = document.getElementById('circuit-canvas');
			if (!this.canvas) return;
			
			this.ctx = this.canvas.getContext('2d');
			this.setupCanvas();
			this.bindEvents();
			this.initComponentLibrary();
			this.render();
		},

		/**
		 * Setup canvas
		 */
		setupCanvas: function() {
			this.canvas.width = 1200;
			this.canvas.height = 800;
		},

		/**
		 * Bind events
		 */
		bindEvents: function() {
			// Toolbar buttons
			$('#new-circuit').on('click', () => this.newCircuit());
			$('#save-circuit').on('click', () => this.saveCircuit());
			$('#load-circuit').on('click', () => this.showLoadDialog());
			$('#undo').on('click', () => this.undo());
			$('#redo').on('click', () => this.redo());
			$('#zoom-in').on('click', () => this.zoomIn());
			$('#zoom-out').on('click', () => this.zoomOut());
			$('#simulate').on('click', () => this.simulate());
			$('#export-circuit').on('click', () => this.exportCircuit());

			// Canvas events
			$(this.canvas).on('mousedown', (e) => this.onMouseDown(e));
			$(this.canvas).on('mousemove', (e) => this.onMouseMove(e));
			$(this.canvas).on('mouseup', (e) => this.onMouseUp(e));
			$(this.canvas).on('click', (e) => this.onCanvasClick(e));

			// Keyboard shortcuts
			$(document).on('keydown', (e) => this.onKeyDown(e));

			// Component search
			$('#component-search').on('input', (e) => this.filterComponents(e.target.value));

			// Category toggles
			$('.category-header').on('click', function() {
				$(this).parent('.category').toggleClass('expanded collapsed');
			});
		},

		/**
		 * Initialize component library
		 */
		initComponentLibrary: function() {
			const self = this;
			
			$('.component-item').each(function() {
				const $item = $(this);
				const componentType = $item.data('type');
				
				$item.on('dragstart', function(e) {
					e.originalEvent.dataTransfer.setData('componentType', componentType);
					$item.addClass('dragging');
				});
				
				$item.on('dragend', function() {
					$item.removeClass('dragging');
				});
			});

			// Canvas drop zone
			$(this.canvas).on('dragover', function(e) {
				e.preventDefault();
			});

			$(this.canvas).on('drop', (e) => {
				e.preventDefault();
				const componentType = e.originalEvent.dataTransfer.getData('componentType');
				if (componentType) {
					const rect = this.canvas.getBoundingClientRect();
					const x = e.clientX - rect.left;
					const y = e.clientY - rect.top;
					this.addComponent(componentType, x, y);
				}
			});
		},

		/**
		 * Add component
		 */
		addComponent: function(type, x, y) {
			const componentDef = nexusCircuitData.components[type];
			if (!componentDef) return;

			const component = {
				id: 'comp_' + Date.now(),
				type: type,
				x: x,
				y: y,
				width: 60,
				height: 40,
				properties: {},
				nodes: ['node_' + Date.now() + '_1', 'node_' + Date.now() + '_2']
			};

			// Copy default properties
			if (componentDef.properties) {
				for (const key in componentDef.properties) {
					component.properties[key] = componentDef.properties[key].default;
				}
			}

			this.components.push(component);
			this.saveState();
			this.render();
		},

		/**
		 * Remove component
		 */
		removeComponent: function(component) {
			const index = this.components.indexOf(component);
			if (index > -1) {
				// Remove connected wires
				this.connections = this.connections.filter(conn => 
					conn.from !== component.id && conn.to !== component.id
				);
				
				this.components.splice(index, 1);
				this.selectedComponent = null;
				this.updatePropertiesPanel();
				this.saveState();
				this.render();
			}
		},

		/**
		 * Mouse down handler
		 */
		onMouseDown: function(e) {
			const rect = this.canvas.getBoundingClientRect();
			const x = e.clientX - rect.left;
			const y = e.clientY - rect.top;

			// Check if clicking on component
			const component = this.getComponentAt(x, y);
			if (component) {
				this.selectedComponent = component;
				this.isDragging = true;
				this.draggedComponent = component;
				this.updatePropertiesPanel();
				this.render();
				return;
			}

			// Start drawing wire
			this.isDrawingWire = true;
			this.wireStart = { x, y };
		},

		/**
		 * Mouse move handler
		 */
		onMouseMove: function(e) {
			const rect = this.canvas.getBoundingClientRect();
			const x = e.clientX - rect.left;
			const y = e.clientY - rect.top;

			if (this.isDragging && this.draggedComponent) {
				this.draggedComponent.x = x - this.draggedComponent.width / 2;
				this.draggedComponent.y = y - this.draggedComponent.height / 2;
				this.render();
			}

			if (this.isDrawingWire && this.wireStart) {
				this.render();
				this.ctx.beginPath();
				this.ctx.moveTo(this.wireStart.x, this.wireStart.y);
				this.ctx.lineTo(x, y);
				this.ctx.strokeStyle = '#0073aa';
				this.ctx.lineWidth = 2;
				this.ctx.stroke();
			}
		},

		/**
		 * Mouse up handler
		 */
		onMouseUp: function(e) {
			if (this.isDragging) {
				this.isDragging = false;
				this.draggedComponent = null;
				this.saveState();
			}

			if (this.isDrawingWire) {
				const rect = this.canvas.getBoundingClientRect();
				const x = e.clientX - rect.left;
				const y = e.clientY - rect.top;
				
				const endComponent = this.getComponentAt(x, y);
				if (endComponent && this.wireStart) {
					const startComponent = this.getComponentAt(this.wireStart.x, this.wireStart.y);
					if (startComponent && startComponent !== endComponent) {
						this.addConnection(startComponent, endComponent);
					}
				}
				
				this.isDrawingWire = false;
				this.wireStart = null;
				this.render();
			}
		},

		/**
		 * Canvas click handler
		 */
		onCanvasClick: function(e) {
			const rect = this.canvas.getBoundingClientRect();
			const x = e.clientX - rect.left;
			const y = e.clientY - rect.top;

			const component = this.getComponentAt(x, y);
			if (component) {
				this.selectedComponent = component;
				this.updatePropertiesPanel();
				this.render();
			} else {
				this.selectedComponent = null;
				this.updatePropertiesPanel();
				this.render();
			}
		},

		/**
		 * Get component at position
		 */
		getComponentAt: function(x, y) {
			for (let i = this.components.length - 1; i >= 0; i--) {
				const comp = this.components[i];
				if (x >= comp.x && x <= comp.x + comp.width &&
					y >= comp.y && y <= comp.y + comp.height) {
					return comp;
				}
			}
			return null;
		},

		/**
		 * Add connection
		 */
		addConnection: function(from, to) {
			this.connections.push({
				id: 'conn_' + Date.now(),
				from: from.id,
				to: to.id,
				node: 'node_' + Date.now()
			});
			this.saveState();
			this.render();
		},

		/**
		 * Render circuit
		 */
		render: function() {
			// Clear canvas
			this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

			// Draw connections
			this.renderConnections();

			// Draw components
			this.renderComponents();
		},

		/**
		 * Render components
		 */
		renderComponents: function() {
			this.components.forEach(comp => {
				const isSelected = this.selectedComponent === comp;
				
				// Component box
				this.ctx.fillStyle = '#fff';
				this.ctx.strokeStyle = isSelected ? '#d63638' : '#0073aa';
				this.ctx.lineWidth = isSelected ? 3 : 2;
				this.ctx.fillRect(comp.x, comp.y, comp.width, comp.height);
				this.ctx.strokeRect(comp.x, comp.y, comp.width, comp.height);

				// Component symbol
				const componentDef = nexusCircuitData.components[comp.type];
				if (componentDef) {
					this.ctx.fillStyle = '#1d2327';
					this.ctx.font = 'bold 14px Arial';
					this.ctx.textAlign = 'center';
					this.ctx.textBaseline = 'middle';
					this.ctx.fillText(
						componentDef.symbol,
						comp.x + comp.width / 2,
						comp.y + comp.height / 2
					);
				}

				// Connection points
				this.ctx.fillStyle = '#0073aa';
				this.ctx.beginPath();
				this.ctx.arc(comp.x, comp.y + comp.height / 2, 4, 0, Math.PI * 2);
				this.ctx.fill();
				this.ctx.beginPath();
				this.ctx.arc(comp.x + comp.width, comp.y + comp.height / 2, 4, 0, Math.PI * 2);
				this.ctx.fill();
			});
		},

		/**
		 * Render connections
		 */
		renderConnections: function() {
			this.connections.forEach(conn => {
				const fromComp = this.components.find(c => c.id === conn.from);
				const toComp = this.components.find(c => c.id === conn.to);

				if (fromComp && toComp) {
					this.ctx.beginPath();
					this.ctx.moveTo(
						fromComp.x + fromComp.width,
						fromComp.y + fromComp.height / 2
					);
					this.ctx.lineTo(
						toComp.x,
						toComp.y + toComp.height / 2
					);
					this.ctx.strokeStyle = '#2c3338';
					this.ctx.lineWidth = 2;
					this.ctx.stroke();
				}
			});
		},

		/**
		 * Update properties panel
		 */
		updatePropertiesPanel: function() {
			const $panel = $('.panel-content');
			
			if (!this.selectedComponent) {
				$panel.html(`
					<div class="empty-state">
						<span class="dashicons dashicons-admin-generic"></span>
						<p>${nexusCircuitData.i18n.selectComponent}</p>
					</div>
				`);
				return;
			}

			const componentDef = nexusCircuitData.components[this.selectedComponent.type];
			if (!componentDef) return;

			let html = `<h4>${componentDef.name}</h4>`;
			
			if (componentDef.properties) {
				Object.keys(componentDef.properties).forEach(key => {
					const prop = componentDef.properties[key];
					const value = this.selectedComponent.properties[key] || prop.default;

					html += `
						<div class="property-field">
							<label>${prop.name}</label>
							<input 
								type="number" 
								class="property-input" 
								data-property="${key}"
								value="${value}"
								step="any"
							/>
							${prop.unit ? `<span class="unit">${prop.unit}</span>` : ''}
						</div>
					`;
				});
			}

			html += `
				<button class="toolbar-btn danger" id="delete-component" style="width: 100%; margin-top: 20px;">
					<span class="dashicons dashicons-trash"></span>
					${nexusCircuitData.i18n.deleteComponent}
				</button>
			`;

			$panel.html(html);

			// Bind property inputs
			$('.property-input').on('change', (e) => {
				const property = $(e.target).data('property');
				const value = parseFloat($(e.target).val());
				this.selectedComponent.properties[property] = value;
				this.saveState();
				this.render();
			});

			// Bind delete button
			$('#delete-component').on('click', () => {
				if (confirm(nexusCircuitData.i18n.confirmDelete)) {
					this.removeComponent(this.selectedComponent);
				}
			});
		},

		/**
		 * New circuit
		 */
		newCircuit: function() {
			if (this.components.length > 0) {
				if (!confirm(nexusCircuitData.i18n.confirmNew)) return;
			}
			
			this.components = [];
			this.connections = [];
			this.selectedComponent = null;
			this.history = [];
			this.historyIndex = -1;
			this.updatePropertiesPanel();
			this.render();
		},

		/**
		 * Save circuit
		 */
		saveCircuit: function() {
			const name = prompt(nexusCircuitData.i18n.enterName);
			if (!name) return;

			const data = {
				action: 'nexus_save_circuit',
				nonce: nexusCircuitData.nonce,
				name: name,
				circuit_data: JSON.stringify({
					components: this.components,
					connections: this.connections
				})
			};

			$.post(ajaxurl, data, (response) => {
				if (response.success) {
					alert(nexusCircuitData.i18n.saveSuccess);
					this.loadSavedCircuits();
				} else {
					alert(nexusCircuitData.i18n.saveError);
				}
			});
		},

		/**
		 * Load saved circuits
		 */
		loadSavedCircuits: function() {
			// Implementation for loading saved circuits list
		},

		/**
		 * Simulate circuit
		 */
		simulate: function() {
			if (this.components.length === 0) {
				alert(nexusCircuitData.i18n.noComponents);
				return;
			}

			const data = {
				action: 'nexus_simulate_circuit',
				nonce: nexusCircuitData.nonce,
				circuit_data: JSON.stringify({
					components: this.components,
					connections: this.connections
				})
			};

			$('.loading-overlay').show();

			$.post(ajaxurl, data, (response) => {
				$('.loading-overlay').hide();
				
				if (response.success) {
					this.displaySimulationResults(response.data);
				} else {
					alert(response.data || nexusCircuitData.i18n.simulationError);
				}
			});
		},

		/**
		 * Display simulation results
		 */
		displaySimulationResults: function(results) {
			let html = '<h4>' + nexusCircuitData.i18n.simulationResults + '</h4>';

			// Summary
			if (results.reports && results.reports.summary) {
				html += '<div class="result-section"><h5>Summary</h5>';
				Object.keys(results.reports.summary).forEach(key => {
					html += `
						<div class="result-item">
							<span class="result-label">${key}</span>
							<span class="result-value">${results.reports.summary[key]}</span>
						</div>
					`;
				});
				html += '</div>';
			}

			// Warnings
			if (results.reports && results.reports.warnings && results.reports.warnings.length > 0) {
				html += '<div class="result-section"><h5>Warnings</h5>';
				results.reports.warnings.forEach(warning => {
					html += `<div class="warning-item">${warning}</div>`;
				});
				html += '</div>';
			}

			$('.simulation-panel').html(html).show();
		},

		/**
		 * Export circuit
		 */
		exportCircuit: function() {
			const link = document.createElement('a');
			link.download = 'circuit.png';
			link.href = this.canvas.toDataURL();
			link.click();
		},

		/**
		 * History management
		 */
		saveState: function() {
			const state = JSON.stringify({
				components: this.components,
				connections: this.connections
			});

			this.history = this.history.slice(0, this.historyIndex + 1);
			this.history.push(state);
			
			if (this.history.length > this.maxHistory) {
				this.history.shift();
			} else {
				this.historyIndex++;
			}
		},

		undo: function() {
			if (this.historyIndex > 0) {
				this.historyIndex--;
				this.restoreState(this.history[this.historyIndex]);
			}
		},

		redo: function() {
			if (this.historyIndex < this.history.length - 1) {
				this.historyIndex++;
				this.restoreState(this.history[this.historyIndex]);
			}
		},

		restoreState: function(state) {
			const data = JSON.parse(state);
			this.components = data.components;
			this.connections = data.connections;
			this.render();
		},

		/**
		 * Zoom controls
		 */
		zoomIn: function() {
			this.zoom = Math.min(this.zoom + 0.1, 2);
			this.render();
		},

		zoomOut: function() {
			this.zoom = Math.max(this.zoom - 0.1, 0.5);
			this.render();
		},

		/**
		 * Keyboard shortcuts
		 */
		onKeyDown: function(e) {
			// Delete
			if (e.key === 'Delete' && this.selectedComponent) {
				this.removeComponent(this.selectedComponent);
			}
			
			// Ctrl+Z - Undo
			if (e.ctrlKey && e.key === 'z') {
				e.preventDefault();
				this.undo();
			}
			
			// Ctrl+Y - Redo
			if (e.ctrlKey && e.key === 'y') {
				e.preventDefault();
				this.redo();
			}
		},

		/**
		 * Filter components
		 */
		filterComponents: function(query) {
			const search = query.toLowerCase();
			
			$('.component-item').each(function() {
				const name = $(this).find('.component-name').text().toLowerCase();
				const desc = $(this).find('.component-description').text().toLowerCase();
				
				if (name.includes(search) || desc.includes(search)) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		}
	};

	// Initialize on document ready
	$(document).ready(function() {
		CircuitSimulator.init();
	});

})(jQuery);
