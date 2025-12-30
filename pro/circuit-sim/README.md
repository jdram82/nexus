# Circuit Simulator

A professional-grade interactive circuit design and simulation tool for WordPress, part of the Nexus Advanced tier.

## Overview

The Circuit Simulator provides electrical engineers and electronics enthusiasts with a powerful web-based tool for designing, simulating, and analyzing electronic circuits. Perfect for educational institutions, electronics manufacturers, and technical documentation.

## Features

### Interactive Circuit Design
- **Drag-and-Drop Interface**: Intuitive component placement on HTML5 canvas
- **Component Library**: 20+ electronic components across 6 categories
- **Visual Feedback**: Real-time circuit rendering with connection indicators
- **Grid Snapping**: Precise component alignment

### Circuit Analysis
- **DC Analysis**: Node voltage and branch current calculations
- **AC Analysis**: Impedance and phase angle calculations for reactive components
- **Transient Analysis**: Time-domain simulation for dynamic circuits
- **Component Calculations**: Automatic power dissipation and component stress analysis

### Component Categories

#### Passive Components
- Resistors (with tolerance and power ratings)
- Capacitors (ceramic, electrolytic, tantalum)
- Inductors

#### Active Components
- Diodes
- LEDs (with brightness simulation)
- NPN/PNP Transistors
- Operational Amplifiers

#### Power Sources
- DC Battery/Power Supply
- AC Voltage Source
- Ground Reference

#### Measuring Instruments
- Voltmeter
- Ammeter
- Ohmmeter

#### Switches & Controls
- SPST/SPDT/DPST/DPDT Switches
- Potentiometers

#### Logic Gates
- AND Gates
- OR Gates
- NOT Gates

### Simulation Engine

The simulation engine implements industry-standard electrical analysis:

**DC Analysis**
- Nodal analysis using Gauss-Seidel iteration
- Automatic ground reference detection
- Voltage source handling
- Branch current calculations using Ohm's law

**AC Analysis**
- Frequency-dependent impedance calculations
- Phase angle analysis
- Capacitive reactance: Xc = 1/(2πfC)
- Inductive reactance: XL = 2πfL

**Component Models**
- Resistor: Linear I-V relationship
- Diode: Exponential Shockley model
- LED: Forward voltage with brightness calculation
- Capacitor: Charge/discharge modeling
- Inductor: Magnetic flux calculations

### Validation & Warnings

Automatic circuit validation:
- Ground connection requirement
- Power source detection
- Floating node detection
- Component power rating checks
- Reverse bias warnings for diodes/LEDs
- Overvoltage protection alerts

## Usage

### Creating a Circuit

1. Navigate to **Appearance > Circuit Simulator**
2. Select components from the library sidebar
3. Drag components onto the canvas
4. Click and drag between components to create connections
5. Select components to edit properties in the right panel

### Simulating

1. Click **Simulate** in the toolbar
2. Review node voltages and component currents
3. Check warnings for component stress
4. View recommendations for circuit improvements

### Saving & Loading

**Save Circuit**
```
1. Click "Save" in toolbar
2. Enter circuit name
3. Circuit saved to WordPress database
```

**Load Circuit**
```
1. Click "Load" in toolbar
2. Select from saved circuits
3. Circuit loads onto canvas
```

### Exporting

**Export Options**
- PNG Image: High-resolution circuit diagram
- JSON Data: Circuit data for external tools
- PDF Report: Complete simulation report (Pro feature)

## Component Properties

### Resistor
| Property | Default | Range |
|----------|---------|-------|
| Resistance | 1 kΩ | 1Ω - 1TΩ |
| Tolerance | 5% | 1% - 20% |
| Power Rating | 0.25W | 0.125W - 100W |

### Capacitor
| Property | Default | Range |
|----------|---------|-------|
| Capacitance | 1 μF | 1pF - 1F |
| Voltage Rating | 50V | 6.3V - 1000V |
| Type | Ceramic | Ceramic, Electrolytic, Tantalum |

### LED
| Property | Default | Range |
|----------|---------|-------|
| Forward Voltage | 2.0V | 1.8V - 3.5V |
| Forward Current | 20mA | 2mA - 100mA |
| Color | Red | Red, Green, Blue, Yellow, White |

## Technical Implementation

### Architecture

```
Circuit Simulator
├── PHP Backend
│   ├── class-circuit-simulator.php      (Main controller)
│   ├── class-component-library.php      (Component definitions)
│   └── class-simulation-engine.php      (Circuit analysis)
├── JavaScript Frontend
│   ├── circuit-sim.js                   (UI & interaction)
│   └── libs/circuit-engine.js           (Physics engine)
└── Styles
    └── circuit-sim.css                  (UI styling)
```

### Data Storage

Circuits are stored as WordPress custom post type `nexus_circuit`:

```php
{
  'post_title': 'Circuit Name',
  'post_content': {
    'components': [
      {
        'id': 'comp_1234567890',
        'type': 'resistor',
        'x': 100,
        'y': 100,
        'properties': {
          'resistance': 1000,
          'tolerance': 5,
          'power': 0.25
        },
        'nodes': ['node_1', 'node_2']
      }
    ],
    'connections': [
      {
        'from': 'comp_1',
        'to': 'comp_2',
        'node': 'node_shared'
      }
    ]
  }
}
```

### AJAX Endpoints

All operations use WordPress AJAX with nonce verification:

| Endpoint | Action |
|----------|--------|
| `nexus_save_circuit` | Save circuit to database |
| `nexus_load_circuit` | Load saved circuit |
| `nexus_simulate_circuit` | Run simulation analysis |
| `nexus_export_circuit` | Export circuit data |
| `nexus_delete_circuit` | Delete saved circuit |

## Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| `Ctrl + Z` | Undo |
| `Ctrl + Y` | Redo |
| `Delete` | Remove selected component |
| `Ctrl + S` | Save circuit |
| `Ctrl + N` | New circuit |

## Browser Compatibility

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

Requires HTML5 Canvas support.

## Use Cases

### Educational Institutions
- Interactive electronics labs
- Circuit theory demonstrations
- Student homework submissions
- Exam preparation tools

### Electronics Manufacturers
- Product documentation
- Circuit design examples
- Technical support resources
- Customer training materials

### Engineering Firms
- Quick circuit prototyping
- Client presentations
- Design collaboration
- Technical proposals

### Hobbyists & Makers
- Project planning
- Component selection
- Circuit validation
- Learning electronics

## Performance

- **Rendering**: 60 FPS on modern browsers
- **Simulation**: <100ms for circuits with <50 components
- **Large Circuits**: Supports up to 200 components
- **Memory**: ~50MB for typical circuits

## API Integration

Extend functionality with custom components:

```php
add_filter( 'nexus_circuit_components', function( $components ) {
    $components['custom_sensor'] = array(
        'name'        => 'Temperature Sensor',
        'symbol'      => 'TS',
        'description' => 'Analog temperature sensor',
        'properties'  => array(
            'sensitivity' => array(
                'name'    => 'Sensitivity',
                'unit'    => 'mV/°C',
                'default' => 10
            )
        )
    );
    return $components;
});
```

## Troubleshooting

**Canvas Not Rendering**
- Check browser console for JavaScript errors
- Ensure HTML5 Canvas is supported
- Clear browser cache

**Simulation Errors**
- Verify circuit has ground connection
- Check for floating nodes
- Ensure power source is present

**Components Not Dragging**
- Check that component library is loaded
- Verify JavaScript is enabled
- Test with default components first

## Support

For technical support and feature requests:
- Documentation: Advanced Tier > Circuit Simulator
- Email: support@nexustheme.com
- Forum: community.nexustheme.com

## Changelog

### Version 3.0.0 (Current)
- Initial release
- 20+ electronic components
- DC/AC/Transient analysis
- Save/Load functionality
- Export to PNG
- Undo/Redo support

## License

Part of Nexus Theme Advanced Tier. Licensed under GPL v3.
