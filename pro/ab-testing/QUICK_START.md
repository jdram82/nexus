# A/B Testing Quick Start Guide

Get started with A/B testing in 5 minutes.

## Step 1: Create Your First Test (2 min)

1. Go to **WordPress Admin → A/B Tests → Create Test**
2. Enter test name: "Homepage Headline Test"
3. Select test type: "Headline Test"
4. Choose conversion goal: "Button Click"

## Step 2: Configure Variants (2 min)

**Variant A (Control):**
- Name: "Current Headline"
- Traffic: 50%
- Content: "Transform Your Business Today"

**Variant B:**
- Name: "New Headline"
- Traffic: 50%
- Content: "Grow Your Business 10x Faster"

Click **Add Variant** for more variants.

## Step 3: Start Test (10 sec)

1. Click **Create Test**
2. On dashboard, click **Start Test**
3. Test is now live!

## Step 4: Add Tracking Code (30 sec)

Add to your page/template:

```html
<button data-ab-test="123" class="cta-button">
    Click Here
</button>
```

Or use JavaScript:

```javascript
document.querySelector('.cta-button').addEventListener('click', () => {
    nexusTrackConversion(123); // Replace 123 with your test ID
});
```

## Step 5: Monitor Results (ongoing)

- Go to **A/B Tests → Dashboard**
- View real-time conversion rates
- Wait for 95%+ confidence
- Declare winner when significant

## Understanding Results

**Conversion Rate**: Percentage of visitors who converted
**Confidence**: Statistical certainty (aim for 95%+)
**Improvement**: % increase vs control variant

### Example Results

```
Variant A (Control): 12.5% conversion rate (250/2000 visitors)
Variant B: 15.8% conversion rate (316/2000 visitors)
Confidence: 97.3%
Improvement: +26.4%
```

**Interpretation**: Variant B performs 26.4% better with 97.3% confidence. Switch to Variant B!

## Best Practices

✅ **DO:**
- Run tests for 1-2 weeks minimum
- Wait for 95%+ confidence
- Test one thing at a time
- Get at least 100 visitors per variant

❌ **DON'T:**
- Stop tests early
- Test too many variants at once
- Change test mid-flight
- Trust results below 90% confidence

## Common Test Types

### 1. Headline Test
Test different headlines to improve engagement.

### 2. CTA Button Test
Compare button colors, text, placement.

### 3. Pricing Test
Test different price points or layouts.

### 4. Layout Test
Compare page layouts and structures.

### 5. Form Test
Optimize form fields and length.

## Troubleshooting

**Problem**: Test not appearing on site
**Solution**: Check test status is "Active", clear cache

**Problem**: Zero conversions tracking
**Solution**: Verify tracking code, check browser console for errors

**Problem**: Low confidence even with traffic
**Solution**: Need larger sample size or more time

## Next Steps

1. ✅ Create first test
2. ✅ Monitor results daily
3. ✅ Implement winner
4. 🔄 Create next test
5. 🔄 Repeat and optimize

## Need Help?

- 📖 Full documentation: `/pro/ab-testing/README.md`
- 💡 View example tests in dashboard
- 🎯 Recommended minimum: 100 visitors/variant

---

**Pro Tip**: Start with high-traffic pages for faster results!
