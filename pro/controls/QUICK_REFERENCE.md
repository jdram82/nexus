# Advanced Controls - Quick Reference

**7 Custom WordPress Customizer Controls**

---

## Control Types

### 1. Typography Control
```php
new Nexus_Typography_Control( $wp_customize, 'setting_id', $args )
```
**Output:** Font family, weight, size, line height, letter spacing, transform

### 2. Gradient Control
```php
new Nexus_Gradient_Control( $wp_customize, 'setting_id', $args )
```
**Output:** Linear/radial gradient with colors and positions

### 3. Shadow Control
```php
new Nexus_Shadow_Control( $wp_customize, 'setting_id', $args )
```
**Output:** Box shadow with offset, blur, spread, color, inset

### 4. Border Control
```php
new Nexus_Border_Control( $wp_customize, 'setting_id', $args )
```
**Output:** Border width, style, color, radius

### 5. Spacing Control
```php
new Nexus_Spacing_Control( $wp_customize, 'setting_id', $args )
```
**Output:** Top, right, bottom, left values (linked/unlinked)

### 6. Icon Picker
```php
new Nexus_Icon_Picker_Control( $wp_customize, 'setting_id', $args )
```
**Output:** Selected Dashicon class name

### 7. Image Position Control
```php
new Nexus_Image_Position_Control( $wp_customize, 'setting_id', $args )
```
**Output:** Position X/Y, size, repeat, attachment

---

## Quick Setup

### Step 1: Add Setting
```php
$wp_customize->add_setting( 'my_setting', array(
    'default'           => json_encode( /* default values */ ),
    'sanitize_callback' => 'wp_kses_post',
    'transport'         => 'postMessage', // Optional for live preview
) );
```

### Step 2: Add Control
```php
$wp_customize->add_control(
    new Nexus_Typography_Control( // Choose control type
        $wp_customize,
        'my_setting',
        array(
            'label'       => __( 'My Label', 'nexus-pro' ),
            'description' => __( 'Description text', 'nexus-pro' ),
            'section'     => 'my_section',
            'settings'    => 'my_setting',
        )
    )
);
```

### Step 3: Use Values
```php
$value = get_theme_mod( 'my_setting' );
$data = json_decode( $value, true );

// Access individual values
$font_family = $data['font-family'];
$font_size = $data['font-size'];
```

---

## Default Values

### Typography
```json
{
  "font-family": "",
  "font-weight": "400",
  "font-style": "normal",
  "font-size": "",
  "line-height": "",
  "letter-spacing": "",
  "text-transform": "none"
}
```

### Gradient
```json
{
  "type": "linear",
  "angle": "135",
  "color1": "#667eea",
  "color1-pos": "0",
  "color2": "#764ba2",
  "color2-pos": "100"
}
```

### Shadow
```json
{
  "horizontal": "0",
  "vertical": "2",
  "blur": "8",
  "spread": "0",
  "color": "rgba(0,0,0,0.1)",
  "inset": false
}
```

### Border
```json
{
  "width": "1",
  "style": "solid",
  "color": "#dddddd",
  "radius": "0"
}
```

### Spacing
```json
{
  "top": "0",
  "right": "0",
  "bottom": "0",
  "left": "0",
  "linked": true
}
```

### Image Position
```json
{
  "position-x": "50",
  "position-y": "50",
  "size": "cover",
  "repeat": "no-repeat",
  "attachment": "scroll"
}
```

---

## CSS Output Examples

### Typography
```php
$typo = json_decode( get_theme_mod( 'heading_typography' ), true );
?>
<style>
h1 {
    font-family: <?php echo esc_attr( $typo['font-family'] ); ?>;
    font-weight: <?php echo esc_attr( $typo['font-weight'] ); ?>;
    font-size: <?php echo esc_attr( $typo['font-size'] ); ?>px;
    line-height: <?php echo esc_attr( $typo['line-height'] ); ?>;
}
</style>
```

### Gradient
```php
$grad = json_decode( get_theme_mod( 'button_gradient' ), true );
$angle = $grad['type'] === 'linear' ? $grad['angle'] . 'deg' : 'circle';
?>
<style>
.button {
    background: <?php echo $grad['type']; ?>-gradient(
        <?php echo $angle; ?>,
        <?php echo esc_attr( $grad['color1'] ); ?> <?php echo esc_attr( $grad['color1-pos'] ); ?>%,
        <?php echo esc_attr( $grad['color2'] ); ?> <?php echo esc_attr( $grad['color2-pos'] ); ?>%
    );
}
</style>
```

### Shadow
```php
$shadow = json_decode( get_theme_mod( 'card_shadow' ), true );
?>
<style>
.card {
    box-shadow: <?php echo $shadow['inset'] ? 'inset ' : ''; ?>
                <?php echo esc_attr( $shadow['horizontal'] ); ?>px
                <?php echo esc_attr( $shadow['vertical'] ); ?>px
                <?php echo esc_attr( $shadow['blur'] ); ?>px
                <?php echo esc_attr( $shadow['spread'] ); ?>px
                <?php echo esc_attr( $shadow['color'] ); ?>;
}
</style>
```

---

## Common Patterns

### Responsive Typography
```php
// Desktop
$desktop = json_decode( get_theme_mod( 'desktop_typo' ), true );

// Tablet
$tablet = json_decode( get_theme_mod( 'tablet_typo' ), true );

// Mobile
$mobile = json_decode( get_theme_mod( 'mobile_typo' ), true );
?>
<style>
h1 { font-size: <?php echo $desktop['font-size']; ?>px; }

@media (max-width: 1024px) {
    h1 { font-size: <?php echo $tablet['font-size']; ?>px; }
}

@media (max-width: 768px) {
    h1 { font-size: <?php echo $mobile['font-size']; ?>px; }
}
</style>
```

### Conditional Styling
```php
$shadow = json_decode( get_theme_mod( 'element_shadow' ), true );

// Only apply if shadow has blur
if ( ! empty( $shadow['blur'] ) && $shadow['blur'] > 0 ) {
    // Output shadow CSS
}
```

---

## Files & Integration

**Control Files:**
- `class-controls-manager.php` - Main registry
- `class-typography-control.php`
- `class-gradient-control.php`
- `class-shadow-control.php`
- `class-border-control.php`
- `class-spacing-control.php`
- `class-icon-picker-control.php`
- `class-image-position-control.php`

**Assets:**
- `pro/assets/css/controls.css` - All control styles
- `pro/assets/js/controls.js` - All control JavaScript

**Auto-loaded:** Controls are automatically registered when Nexus Pro is active.

---

## Tips

1. **Always use JSON encoding** for complex control values
2. **Sanitize properly** with `wp_kses_post` or custom callback
3. **Use postMessage transport** for live preview when possible
4. **Parse JSON safely** with try/catch or wp_parse_args
5. **Cache values** if using in multiple places
6. **Provide defaults** for all control settings

---

**Version:** 3.0.0  
**Tier:** Advanced & Agency  
**Updated:** December 28, 2025
