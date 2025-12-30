# A/B Testing System

Comprehensive split testing system for optimizing conversions and user experience.

## Features

- **Test Management**: Create, start, pause, and end A/B tests
- **Traffic Splitting**: Weighted random distribution across variants
- **Conversion Tracking**: Track clicks, form submissions, purchases, custom events
- **Statistical Analysis**: Calculate confidence levels and determine winners
- **Real-time Results**: Live dashboard with conversion rates and visitor counts
- **Multiple Variants**: Test 2+ variants simultaneously

## Quick Start

1. Navigate to **A/B Tests** in WordPress admin
2. Click **Create Test**
3. Configure test details and variants
4. Start the test
5. Monitor results in real-time

## Creating a Test

```php
// Create test programmatically
$test_manager = Nexus_Test_Manager::get_instance();
$test_id = $test_manager->create_test([
    'name' => 'Homepage CTA Button',
    'variants' => [
        ['id' => 0, 'name' => 'Control', 'traffic' => 50, 'content' => 'Buy Now'],
        ['id' => 1, 'name' => 'Variant A', 'traffic' => 50, 'content' => 'Get Started']
    ]
]);
```

## Tracking Conversions

### Frontend JavaScript
```javascript
// Track conversion when user clicks button
document.querySelector('.cta-button').addEventListener('click', function() {
    if (window.nexusTrackConversion) {
        nexusTrackConversion(testId);
    }
});
```

### Using Data Attributes
```html
<button data-ab-test="123">Click Me</button>
```

### Programmatic Tracking
```php
$tracker = Nexus_Analytics_Tracker::get_instance();
$tracker->track_conversion($test_id, $variant_id, $value);
```

## Statistical Significance

Confidence levels are calculated using z-score:

- **99%**: z ≥ 2.58 (very high confidence)
- **95%**: z ≥ 1.96 (high confidence)
- **90%**: z ≥ 1.645 (moderate confidence)
- **80%**: z ≥ 1.28 (low confidence)

Minimum 30 visitors per variant required for statistical analysis.

## Best Practices

1. **Run tests for sufficient duration** (minimum 1-2 weeks)
2. **Ensure adequate sample size** (at least 100 visitors per variant)
3. **Test one variable at a time** for clear insights
4. **Wait for 95%+ confidence** before declaring winner
5. **Avoid peeking early** - let tests run to completion

## API Reference

### Test Manager

```php
$test_manager = Nexus_Test_Manager::get_instance();

// Get all tests
$tests = $test_manager->get_all_tests();

// Get active tests
$active = $test_manager->get_active_tests();

// Start test
$test_manager->start_test($test_id);

// End test
$test_manager->end_test($test_id);
```

### Analytics Tracker

```php
$tracker = Nexus_Analytics_Tracker::get_instance();

// Get test results
$results = $tracker->get_test_results($test_id);

// Get overview stats
$stats = $tracker->get_overview_stats();
```

## Results Interpretation

```php
$results = [
    'variants' => [
        [
            'variant_id' => 0,
            'visitors' => 523,
            'conversions' => 78,
            'conversion_rate' => 14.91,
            'total_value' => 780.00
        ],
        [
            'variant_id' => 1,
            'visitors' => 517,
            'conversions' => 92,
            'conversion_rate' => 17.79,
            'total_value' => 920.00
        ]
    ],
    'winner' => [...],
    'confidence' => 96.5,
    'improvement' => 19.32
];
```

## Troubleshooting

**Tests not showing on frontend:**
- Check test status is 'active'
- Verify admin users aren't excluded (Settings > Exclude Administrators)
- Clear browser cache and cookies

**Conversions not tracking:**
- Check browser console for JavaScript errors
- Verify nonce is valid
- Ensure user has cookies enabled

**Low confidence levels:**
- Increase sample size
- Run test for longer duration
- Check conversion rate differences are meaningful

## Database Schema

### Tests Table
```sql
wp_nexus_ab_tests
- id: Test ID
- name: Test name
- status: draft|active|paused|completed
- variants: JSON array of variants
- start_date: Test start timestamp
- end_date: Test end timestamp
```

### Analytics Table
```sql
wp_nexus_ab_analytics
- id: Event ID
- test_id: Foreign key to test
- variant_id: Variant identifier
- event_type: view|conversion
- conversion_value: Numeric value
- user_hash: MD5 hash for deduplication
- timestamp: Event timestamp
```

## Support

For questions and support, visit the [Nexus documentation](https://example.com/docs).
