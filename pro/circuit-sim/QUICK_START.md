# Circuit Simulator Quick Start

Get started with the Circuit Simulator in 5 minutes.

## Step 1: Access the Simulator

1. Log in to WordPress admin
2. Navigate to **Appearance > Circuit Simulator**
3. You'll see the circuit designer interface

## Step 2: Build Your First Circuit

### Simple LED Circuit

Let's create a basic LED circuit with a current-limiting resistor:

**1. Add a Battery**
- Find "Power Sources" category in left sidebar
- Drag "Battery" onto canvas
- Default voltage: 9V

**2. Add a Resistor**
- Find "Passive Components" category
- Drag "Resistor" onto canvas
- Click on resistor to select
- In properties panel (right), set resistance to 470Ω

**3. Add an LED**
- Find "Active Components" category
- Drag "LED" onto canvas
- Select color: Red (default)

**4. Add Ground**
- Find "Power Sources" category
- Drag "Ground" onto canvas

**5. Connect Components**
- Click on battery's right connection point
- Drag to resistor's left connection point
- Click on resistor's right connection point
- Drag to LED's left connection point
- Click on LED's right connection point
- Drag to ground

Your circuit is complete!

## Step 3: Simulate

1. Click **Simulate** button in toolbar
2. View results in bottom panel:
   - Battery voltage: 9V
   - Resistor current: ~18mA
   - LED state: ON
   - LED brightness: ~90%
   - Total power: ~162mW

## Step 4: Modify Properties

**Change Resistor Value**
1. Click on resistor
2. Properties panel shows current settings
3. Change resistance to 1000Ω
4. Click Simulate again
5. Notice LED brightness decreases

**Change Battery Voltage**
1. Click on battery
2. Change voltage to 5V
3. Simulate again
4. LED may dim or turn off

## Step 5: Save Your Circuit

1. Click **Save** in toolbar
2. Enter name: "My First LED Circuit"
3. Circuit saved to database
4. Appears in "Saved Circuits" section

## Common Circuits Examples

### Voltage Divider

**Components:**
- 1x Battery (9V)
- 2x Resistor (1kΩ each)
- 1x Voltmeter
- 1x Ground

**Expected Output:**
- Output voltage: 4.5V (half of input)

**Steps:**
1. Add battery
2. Add R1 (1kΩ)
3. Add R2 (1kΩ)
4. Add voltmeter between R1 and R2
5. Add ground after R2
6. Connect in series
7. Simulate

### RC Circuit

**Components:**
- 1x Battery (9V)
- 1x Resistor (10kΩ)
- 1x Capacitor (100μF)
- 1x Ground

**Behavior:**
- Capacitor charges through resistor
- Time constant τ = RC = 1 second
- Voltage rises exponentially

### LED Color Mixer

**Components:**
- 1x Battery (9V)
- 3x Resistor (470Ω each)
- 3x LED (Red, Green, Blue)
- 1x Ground

**Creates:**
- RGB LED demonstration
- Each LED can be controlled independently

## Keyboard Shortcuts

| Key | Action |
|-----|--------|
| `Del` | Delete selected component |
| `Ctrl+Z` | Undo last action |
| `Ctrl+Y` | Redo action |
| `Ctrl+S` | Save circuit |
| `Ctrl+N` | New circuit |

## Tips & Tricks

### Component Selection
- Click component to select
- Selected component highlighted in red
- Properties appear in right panel

### Wire Drawing
- Click and hold on component connection point
- Drag to another component
- Release to create connection
- Blue line indicates active wire drawing

### Zoom & Pan
- Use Zoom In/Out buttons in toolbar
- Zoom range: 50% to 200%
- Helps with detailed work

### Component Search
- Use search box above component library
- Type component name
- Library filters in real-time

### Undo/Redo
- 50 action history
- Undo: `Ctrl+Z` or toolbar button
- Redo: `Ctrl+Y` or toolbar button

## Validation Warnings

The simulator checks for common errors:

**Missing Ground**
> "Circuit must have at least one ground connection"

**Solution:** Add ground component

**No Power Source**
> "Circuit must have at least one power source"

**Solution:** Add battery or AC source

**Excessive Power**
> "Resistor R1 exceeds power rating (0.50W)"

**Solution:** Use higher wattage resistor or reduce voltage

**Reverse Bias**
> "LED D1 may be reverse biased"

**Solution:** Check LED polarity

## Export Options

### PNG Image
1. Click **Export** in toolbar
2. Choose PNG
3. Downloads circuit diagram
4. Use in documentation

### JSON Data
1. Click **Export**
2. Choose JSON
3. Downloads circuit data
4. Import into external tools

## Troubleshooting

**Q: Components won't drag to canvas**
- Ensure you're dragging from component library, not clicking
- Check JavaScript console for errors

**Q: Simulation shows no results**
- Verify circuit has ground connection
- Ensure power source is connected
- Check all components are properly connected

**Q: LED not lighting up**
- Check voltage is sufficient (>1.8V for red LED)
- Verify polarity (positive to anode)
- Ensure current-limiting resistor is present

**Q: Can't see all components**
- Use search box to filter
- Expand categories by clicking header
- Scroll in component library panel

## Next Steps

Now that you've mastered the basics:

1. **Experiment** with different component values
2. **Build** more complex circuits
3. **Study** simulation results
4. **Save** your favorite designs
5. **Share** circuits with your team

## Advanced Features

### Custom Component Properties

Edit component properties for specific needs:
- Resistor tolerance for precision work
- Capacitor types for different applications
- LED colors for visual indication
- Transistor beta for amplifier design

### Circuit Analysis

Review simulation results:
- **Node Voltages**: Voltage at each circuit node
- **Branch Currents**: Current through each component
- **Power Dissipation**: Heat generated by components
- **Warnings**: Component stress alerts

### Circuit Management

Organize your circuits:
- Save multiple circuits
- Load and edit existing designs
- Delete unused circuits
- Export for archival

## Support Resources

- **Full Documentation**: [README.md](README.md)
- **Component Reference**: Check properties panel
- **Examples**: Saved circuits library
- **Help**: support@nexustheme.com

## Quick Reference Card

```
TOOLBAR ACTIONS
├─ New      - Start fresh circuit
├─ Save     - Save to database
├─ Load     - Open saved circuit
├─ Undo     - Previous state
├─ Redo     - Next state
├─ Zoom In  - Enlarge view
├─ Zoom Out - Reduce view
├─ Simulate - Run analysis
└─ Export   - Download circuit

CANVAS ACTIONS
├─ Click         - Select component
├─ Drag          - Move component
├─ Click + Drag  - Draw wire
└─ Delete Key    - Remove selected

COMPONENT CATEGORIES
├─ Passive     - R, C, L
├─ Active      - Diodes, LEDs, Transistors
├─ Sources     - Battery, AC, Ground
├─ Meters      - V, A, Ω
├─ Switches    - SPST, SPDT, Pot
└─ Logic       - AND, OR, NOT
```

Happy circuit designing! 🔌
