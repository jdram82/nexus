# API Reference - Nexus Theme

Complete reference for all hooks, filters, functions, and APIs available in the Nexus theme.

## Table of Contents

1. [Actions (Hooks)](#actions-hooks)
2. [Filters](#filters)
3. [Functions](#functions)
4. [Classes](#classes)
5. [JavaScript API](#javascript-api)
6. [Widget API](#widget-api)
7. [Template Tags](#template-tags)
8. [Database Tables](#database-tables)

---

## Actions (Hooks)

### Core Theme Actions

#### `nexus_before_header`
Fires before the header section.

```php
do_action( 'nexus_before_header' );

// Usage
add_action( 'nexus_before_header', 'my_custom_banner' );
function my_custom_banner() {
    echo '<div class="top-banner">Special Offer!</div>';
}
```

#### `nexus_after_header`
Fires after the header section.

```php
do_action( 'nexus_after_header' );
```

#### `nexus_before_content`
Fires before the main content area.

```php
do_action( 'nexus_before_content' );
```

#### `nexus_after_content`
Fires after the main content area.

```php
do_action( 'nexus_after_content' );
```

#### `nexus_before_footer`
Fires before the footer section.

```php
do_action( 'nexus_before_footer' );
```

#### `nexus_after_footer`
Fires after the footer section.

```php
do_action( 'nexus_after_footer' );
```

### Pro Feature Actions

#### `nexus_pro_loaded`
Fires when Pro features are loaded.

```php
do_action( 'nexus_pro_loaded' );

// Usage
add_action( 'nexus_pro_loaded', 'my_pro_extension' );
function my_pro_extension() {
    // Initialize custom pro features
}
```

#### `nexus_builder_widgets_registered`
Fires after all builder widgets are registered.

```php
do_action( 'nexus_builder_widgets_registered' );
```

#### `nexus_analytics_tracked`
Fires after analytics data is recorded.

```php
do_action( 'nexus_analytics_tracked', $data );

/**
 * @param array $data Analytics data
 *   - page_id (int)
 *   - page_url (string)
 *   - metric_type (string)
 *   - metric_value (float)
 */
```

#### `nexus_ab_test_viewed`
Fires when a user views an A/B test variant.

```php
do_action( 'nexus_ab_test_viewed', $test_id, $variant );

/**
 * @param int $test_id Test ID
 * @param string $variant 'A' or 'B'
 */
```

#### `nexus_ab_test_converted`
Fires when A/B test conversion occurs.

```php
do_action( 'nexus_ab_test_converted', $test_id, $variant );
```

#### `nexus_form_submitted`
Fires when a form is submitted.

```php
do_action( 'nexus_form_submitted', $form_id, $form_data );

/**
 * @param int $form_id Form post ID
 * @param array $form_data Submitted data
 */
```

---

## Filters

### Content Filters

#### `nexus_content_width`
Modify the content width.

```php
apply_filters( 'nexus_content_width', 1200 );

// Usage
add_filter( 'nexus_content_width', 'my_custom_width' );
function my_custom_width( $width ) {
    return 1400; // Wider content area
}
```

#### `nexus_excerpt_length`
Change excerpt length.

```php
apply_filters( 'nexus_excerpt_length', 55 );

// Usage
add_filter( 'nexus_excerpt_length', function( $length ) {
    return 30; // Shorter excerpts
});
```

#### `nexus_excerpt_more`
Modify the excerpt "read more" text.

```php
apply_filters( 'nexus_excerpt_more', '...' );

// Usage
add_filter( 'nexus_excerpt_more', function( $more ) {
    return ' [Read More]';
});
```

### Builder Filters

#### `nexus_builder_widgets`
Modify available widgets in Theme Builder.

```php
apply_filters( 'nexus_builder_widgets', $widgets );

// Usage
add_filter( 'nexus_builder_widgets', 'my_custom_widgets' );
function my_custom_widgets( $widgets ) {
    $widgets['my_widget'] = array(
        'name'     => 'My Custom Widget',
        'icon'     => 'dashicons-heart',
        'category' => 'custom',
        'settings' => array(
            'title' => array(
                'type'  => 'text',
                'label' => 'Title',
            ),
        ),
    );
    return $widgets;
}
```

#### `nexus_builder_widget_output`
Filter widget output before rendering.

```php
apply_filters( 'nexus_builder_widget_output', $output, $widget_type, $settings );

/**
 * @param string $output HTML output
 * @param string $widget_type Widget type identifier
 * @param array $settings Widget settings
 */

// Usage
add_filter( 'nexus_builder_widget_output', 'wrap_widgets', 10, 3 );
function wrap_widgets( $output, $type, $settings ) {
    return '<div class="my-wrapper">' . $output . '</div>';
}
```

### Analytics Filters

#### `nexus_analytics_exclude_roles`
Exclude user roles from analytics tracking.

```php
apply_filters( 'nexus_analytics_exclude_roles', array( 'administrator' ) );

// Usage
add_filter( 'nexus_analytics_exclude_roles', function( $roles ) {
    $roles[] = 'editor';
    return $roles; // Don't track editors
});
```

#### `nexus_analytics_track_data`
Modify analytics data before saving.

```php
apply_filters( 'nexus_analytics_track_data', $data );

// Usage
add_filter( 'nexus_analytics_track_data', 'add_custom_metric' );
function add_custom_metric( $data ) {
    $data['custom_field'] = 'custom_value';
    return $data;
}
```

### SEO Filters

#### `nexus_seo_title`
Modify SEO title output.

```php
apply_filters( 'nexus_seo_title', $title, $post_id );

// Usage
add_filter( 'nexus_seo_title', 'custom_seo_title', 10, 2 );
function custom_seo_title( $title, $post_id ) {
    return $title . ' | My Brand';
}
```

#### `nexus_seo_description`
Modify SEO meta description.

```php
apply_filters( 'nexus_seo_description', $description, $post_id );
```

#### `nexus_seo_schema`
Modify schema.org JSON-LD output.

```php
apply_filters( 'nexus_seo_schema', $schema, $type, $post_id );

/**
 * @param array $schema Schema data
 * @param string $type Schema type ('Organization', 'Article', etc.)
 * @param int $post_id Post ID
 */

// Usage
add_filter( 'nexus_seo_schema', 'add_schema_property', 10, 3 );
function add_schema_property( $schema, $type, $post_id ) {
    if ( $type === 'Article' ) {
        $schema['wordCount'] = str_word_count( get_the_content() );
    }
    return $schema;
}
```

### Performance Filters

#### `nexus_performance_score_weights`
Modify performance score calculation weights.

```php
apply_filters( 'nexus_performance_score_weights', array(
    'load_time'     => 0.30,
    'queries'       => 0.25,
    'memory'        => 0.20,
    'file_size'     => 0.15,
    'optimization'  => 0.10,
) );
```

#### `nexus_performance_recommendations`
Add custom performance recommendations.

```php
apply_filters( 'nexus_performance_recommendations', $recommendations, $metrics );

// Usage
add_filter( 'nexus_performance_recommendations', 'custom_recs', 10, 2 );
function custom_recs( $recs, $metrics ) {
    if ( $metrics['custom_check'] > 100 ) {
        $recs[] = array(
            'type'    => 'warning',
            'message' => 'Custom optimization needed',
        );
    }
    return $recs;
}
```

---

## Functions

### Template Functions

#### `nexus_get_template_part()`
Load a template part.

```php
nexus_get_template_part( string $slug, string $name = '' )

// Usage
nexus_get_template_part( 'content', 'page' );
// Loads: template-parts/content-page.php
```

#### `nexus_breadcrumbs()`
Display breadcrumb navigation.

```php
nexus_breadcrumbs( array $args = array() )

// Usage
nexus_breadcrumbs( array(
    'separator' => ' › ',
    'home_text' => 'Home',
) );
```

#### `nexus_pagination()`
Display pagination links.

```php
nexus_pagination( array $args = array() )

// Usage
nexus_pagination( array(
    'prev_text' => '&laquo; Previous',
    'next_text' => 'Next &raquo;',
) );
```

### Builder Functions

#### `nexus_render_widget()`
Render a builder widget.

```php
nexus_render_widget( string $widget_type, array $settings = array() )

// Usage
nexus_render_widget( 'heading', array(
    'text'  => 'My Heading',
    'tag'   => 'h2',
    'align' => 'center',
) );
```

#### `nexus_get_widgets()`
Get all registered widgets.

```php
array nexus_get_widgets( string $category = '' )

// Usage
$all_widgets = nexus_get_widgets();
$basic_widgets = nexus_get_widgets( 'basic' );
```

### Analytics Functions

#### `nexus_track_event()`
Track custom analytics event.

```php
nexus_track_event( string $event_type, mixed $value = 1, array $meta = array() )

// Usage
nexus_track_event( 'button_click', 1, array(
    'button_id' => 'cta-signup',
    'location'  => 'homepage',
) );
```

#### `nexus_get_analytics()`
Retrieve analytics data.

```php
array nexus_get_analytics( array $args = array() )

/**
 * @param array $args Query arguments
 *   - start_date (string) Y-m-d format
 *   - end_date (string) Y-m-d format
 *   - metric_type (string) Specific metric
 *   - page_id (int) Specific page
 * 
 * @return array Analytics data
 */

// Usage
$last_30_days = nexus_get_analytics( array(
    'start_date' => date( 'Y-m-d', strtotime( '-30 days' ) ),
    'end_date'   => date( 'Y-m-d' ),
) );
```

### A/B Testing Functions

#### `nexus_create_ab_test()`
Create new A/B test.

```php
int|WP_Error nexus_create_ab_test( string $test_name, $variant_a, $variant_b )

// Usage
$test_id = nexus_create_ab_test(
    'Homepage CTA',
    'Sign Up Free',
    'Start Your Trial'
);
```

#### `nexus_get_ab_variant()`
Get variant for current user.

```php
string nexus_get_ab_variant( int $test_id )

// Usage
$variant = nexus_get_ab_variant( 123 );
if ( $variant === 'A' ) {
    echo 'Sign Up Free';
} else {
    echo 'Start Your Trial';
}
```

#### `nexus_ab_convert()`
Record A/B test conversion.

```php
bool nexus_ab_convert( int $test_id )

// Usage
if ( isset( $_POST['signup'] ) ) {
    nexus_ab_convert( 123 );
}
```

### SEO Functions

#### `nexus_get_seo_title()`
Get SEO-optimized title.

```php
string nexus_get_seo_title( int $post_id = null )

// Usage
$title = nexus_get_seo_title( get_the_ID() );
```

#### `nexus_get_seo_description()`
Get SEO meta description.

```php
string nexus_get_seo_description( int $post_id = null )
```

#### `nexus_generate_schema()`
Generate schema.org markup.

```php
string nexus_generate_schema( string $type, array $data = array() )

// Usage
$schema = nexus_generate_schema( 'Article', array(
    'headline'    => get_the_title(),
    'description' => get_the_excerpt(),
    'author'      => get_the_author(),
) );
```

---

## Classes

### Main Classes

#### `Nexus_Pro`
Main Pro features class.

**Location:** `/pro/class-nexus-pro.php`

**Methods:**
```php
Nexus_Pro::get_instance()           // Get singleton instance
Nexus_Pro::load_features()          // Load all pro features
Nexus_Pro::create_database_tables() // Create custom tables
```

**Usage:**
```php
$nexus_pro = Nexus_Pro::get_instance();
```

#### `Nexus_Builder_Widgets`
Widget registry and rendering.

**Location:** `/pro/theme-builder/class-builder-widgets.php`

**Methods:**
```php
register_widget( string $type, array $config )
get_widget( string $type )
render_widget( string $type, array $settings )
get_all_widgets()
get_widgets_by_category( string $category )
```

**Usage:**
```php
$widgets = new Nexus_Builder_Widgets();
$widgets->register_widget( 'my_widget', $config );
```

#### `Nexus_Analytics`
Analytics tracking and reporting.

**Location:** `/pro/analytics/class-analytics.php`

**Methods:**
```php
track_pageview()
track_event( $type, $value )
get_metrics( $args )
get_dashboard_data()
calculate_bounce_rate()
get_top_pages( $limit )
```

**Usage:**
```php
$analytics = new Nexus_Analytics();
$analytics->track_event( 'download', 1 );
$metrics = $analytics->get_metrics( array( 'last_30_days' => true ) );
```

#### `Nexus_AB_Testing`
A/B test management.

**Location:** `/pro/ab-testing/class-ab-testing.php`

**Methods:**
```php
create_test( $name, $variant_a, $variant_b )
get_variant( $test_id )
record_view( $test_id, $variant )
record_conversion( $test_id, $variant )
calculate_significance( $test_id )
get_results( $test_id )
```

**Usage:**
```php
$ab = new Nexus_AB_Testing();
$test_id = $ab->create_test( 'Button Color', '#blue', '#green' );
$variant = $ab->get_variant( $test_id );
```

---

## JavaScript API

### Builder API

#### `NexusBuilder`
Global builder object.

```javascript
// Initialize builder
NexusBuilder.init();

// Add widget to canvas
NexusBuilder.addWidget( 'heading', {
    text: 'My Heading',
    tag: 'h2'
});

// Get builder data
const data = NexusBuilder.getData();

// Save builder
NexusBuilder.save();
```

### Events

#### Builder Events

```javascript
// Widget added
jQuery(document).on('nexus:widget:added', function(e, widget) {
    console.log('Widget added:', widget.type);
});

// Widget removed
jQuery(document).on('nexus:widget:removed', function(e, widgetId) {
    console.log('Widget removed:', widgetId);
});

// Builder saved
jQuery(document).on('nexus:builder:saved', function(e, data) {
    console.log('Builder saved');
});
```

#### Analytics Events

```javascript
// Track custom event
nexusAnalytics.track('button_click', {
    button_id: 'signup',
    location: 'header'
});
```

---

## Widget API

### Registering Custom Widgets

```php
add_filter( 'nexus_builder_widgets', 'register_my_widget' );

function register_my_widget( $widgets ) {
    $widgets['testimonial'] = array(
        'name'     => 'Testimonial',
        'icon'     => 'dashicons-format-quote',
        'category' => 'content',
        'settings' => array(
            'quote' => array(
                'type'    => 'textarea',
                'label'   => 'Quote Text',
                'default' => '',
            ),
            'author' => array(
                'type'    => 'text',
                'label'   => 'Author Name',
                'default' => '',
            ),
            'image' => array(
                'type'    => 'image',
                'label'   => 'Author Image',
            ),
        ),
        'render' => function( $settings ) {
            ?>
            <div class="testimonial">
                <?php if ( ! empty( $settings['image'] ) ) : ?>
                    <img src="<?php echo esc_url( $settings['image'] ); ?>" alt="">
                <?php endif; ?>
                <blockquote><?php echo wp_kses_post( $settings['quote'] ); ?></blockquote>
                <cite><?php echo esc_html( $settings['author'] ); ?></cite>
            </div>
            <?php
        },
    );
    
    return $widgets;
}
```

### Widget Setting Types

Available setting types for widgets:

```php
'settings' => array(
    
    // Text input
    'text_field' => array(
        'type'        => 'text',
        'label'       => 'Text Label',
        'default'     => '',
        'placeholder' => 'Enter text...',
    ),
    
    // Textarea
    'content' => array(
        'type'    => 'textarea',
        'label'   => 'Content',
        'rows'    => 5,
    ),
    
    // Number
    'count' => array(
        'type'  => 'number',
        'label' => 'Count',
        'min'   => 0,
        'max'   => 100,
        'step'  => 1,
    ),
    
    // Checkbox
    'enabled' => array(
        'type'    => 'checkbox',
        'label'   => 'Enable Feature',
        'default' => true,
    ),
    
    // Select dropdown
    'layout' => array(
        'type'    => 'select',
        'label'   => 'Layout',
        'options' => array(
            'left'   => 'Left',
            'center' => 'Center',
            'right'  => 'Right',
        ),
    ),
    
    // Color picker
    'color' => array(
        'type'    => 'color',
        'label'   => 'Text Color',
        'default' => '#000000',
    ),
    
    // Image upload
    'background' => array(
        'type'  => 'image',
        'label' => 'Background Image',
    ),
    
    // Icon picker
    'icon' => array(
        'type'  => 'icon',
        'label' => 'Icon',
    ),
    
    // Dimension
    'padding' => array(
        'type'  => 'dimension',
        'label' => 'Padding',
        'units' => array( 'px', 'em', '%' ),
    ),
    
    // Typography
    'typography' => array(
        'type'  => 'typography',
        'label' => 'Typography',
    ),
)
```

---

## Template Tags

### Layout Tags

```php
// Container start
<?php nexus_container_open(); ?>

// Container end
<?php nexus_container_close(); ?>

// Row start
<?php nexus_row_open(); ?>

// Row end
<?php nexus_row_close(); ?>
```

### Post Tags

```php
// Post thumbnail with link
<?php nexus_post_thumbnail(); ?>

// Post meta
<?php nexus_post_meta( array(
    'author' => true,
    'date'   => true,
    'comments' => true,
) ); ?>

// Social share buttons
<?php nexus_social_share(); ?>
```

---

## Database Tables

### `wp_nexus_templates`

Stores saved templates from Template Manager.

**Columns:**
- `id` (bigint) - Primary key
- `template_name` (varchar 255) - Template name
- `template_data` (longtext) - JSON template data
- `template_type` (varchar 50) - Type: 'page', 'header', 'footer'
- `is_premium` (tinyint) - Premium template flag
- `created_at` (datetime) - Creation timestamp

**Indexes:**
- PRIMARY on `id`
- INDEX on `template_type`

### `wp_nexus_analytics`

Analytics tracking data.

**Columns:**
- `id` (bigint) - Primary key
- `page_id` (bigint) - Post/Page ID
- `page_url` (varchar 255) - Full URL
- `referrer` (varchar 255) - Referrer URL
- `user_agent` (text) - Browser user agent
- `ip_address` (varchar 45) - Visitor IP
- `session_id` (varchar 100) - Session identifier
- `metric_type` (varchar 50) - Type: 'pageview', 'event', etc.
- `metric_value` (decimal) - Numeric value
- `recorded_at` (datetime) - Timestamp

**Indexes:**
- PRIMARY on `id`
- INDEX on `page_id`
- INDEX on `metric_type`
- INDEX on `session_id`

### `wp_nexus_ab_tests`

A/B test definitions.

**Columns:**
- `id` (bigint) - Primary key
- `test_name` (varchar 255) - Test name
- `variant_a` (text) - Variant A content
- `variant_b` (text) - Variant B content
- `status` (varchar 20) - 'active', 'paused', 'completed'
- `created_at` (datetime) - Creation timestamp
- `ended_at` (datetime) - End timestamp

**Indexes:**
- PRIMARY on `id`

### `wp_nexus_ab_results`

A/B test results tracking.

**Columns:**
- `id` (bigint) - Primary key
- `test_id` (bigint) - Foreign key to ab_tests
- `variant` (varchar 1) - 'A' or 'B'
- `views` (bigint) - View count
- `conversions` (bigint) - Conversion count
- `recorded_at` (datetime) - Timestamp

**Indexes:**
- PRIMARY on `id`
- INDEX on `test_id`

### `wp_nexus_form_submissions`

Form submission data.

**Columns:**
- `id` (bigint) - Primary key
- `form_id` (bigint) - Form post ID
- `form_data` (longtext) - JSON form data
- `user_ip` (varchar 45) - Submitter IP
- `submitted_at` (datetime) - Submission timestamp

**Indexes:**
- PRIMARY on `id`
- INDEX on `form_id`

---

## Constants

### Theme Constants

```php
NEXUS_VERSION          // Theme version number
NEXUS_DIR              // Theme directory path
NEXUS_URI              // Theme directory URI
NEXUS_PRO_PATH         // Pro features path
NEXUS_PRO_URL          // Pro features URL
NEXUS_DEBUG            // Debug mode flag
```

### Usage

```php
// Enqueue script with version
wp_enqueue_script( 
    'my-script', 
    NEXUS_URI . '/js/script.js', 
    array(), 
    NEXUS_VERSION 
);

// Include file
require_once NEXUS_PRO_PATH . '/my-feature/class-my-feature.php';
```

---

**Related:** [Widget Development Guide](WIDGETS.md) | [Performance Optimization](PERFORMANCE.md)

[⬆ Back to Top](#api-reference---nexus-theme)
