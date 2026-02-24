<?php
/**
 * Template Name: UL/NEC Landing Page
 * Description: Landing page for UL/NEC Compliance Checker
 */

// Disable WordPress admin bar for clean landing page
show_admin_bar(false);

get_header();
?>

<style>
    /* Hide default WordPress elements for landing page */
    #site-header,
    .site-header,
    .page-header,
    .entry-header,
    .breadcrumbs {
        display: none !important;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: #0a0e27;
        color: #fff;
        overflow-x: hidden;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Hero Section */
    .hero-product {
        min-height: 100vh;
        display: flex;
        align-items: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }

    .hero-product::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(255,255,255,0.1) 0%, transparent 50%);
        animation: pulse 8s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 0.5; }
        50% { opacity: 1; }
    }

    .hero-product .container {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .hero-product h1 {
        font-size: 4rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        line-height: 1.1;
        animation: fadeInUp 0.8s ease-out;
    }

    .tagline {
        font-size: 1.8rem;
        margin-bottom: 2rem;
        opacity: 0.95;
        font-weight: 300;
        animation: fadeInUp 0.8s ease-out 0.2s backwards;
    }

    .pricing-highlight {
        font-size: 1.3rem;
        margin-bottom: 3rem;
        padding: 1rem 2rem;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50px;
        display: inline-block;
        font-weight: 600;
        animation: fadeInUp 0.8s ease-out 0.3s backwards;
        backdrop-filter: blur(10px);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .cta-buttons {
        display: flex;
        gap: 1.5rem;
        justify-content: center;
        margin-bottom: 3rem;
        animation: fadeInUp 0.8s ease-out 0.4s backwards;
        flex-wrap: wrap;
    }

    .btn-primary, .btn-secondary {
        padding: 1.2rem 2.5rem;
        font-size: 1.1rem;
        font-weight: 600;
        text-decoration: none;
        border-radius: 50px;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .btn-primary {
        background: #fff;
        color: #667eea;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.4);
    }

    .btn-secondary {
        background: transparent;
        color: #fff;
        border: 2px solid #fff;
    }

    .btn-secondary:hover {
        background: rgba(255,255,255,0.1);
        transform: translateY(-3px);
    }

    .trust-badges {
        display: flex;
        gap: 2rem;
        justify-content: center;
        animation: fadeInUp 0.8s ease-out 0.6s backwards;
        flex-wrap: wrap;
    }

    .trust-badges span {
        font-size: 1rem;
        opacity: 0.9;
    }

    /* Problem Section */
    .problem-section {
        padding: 6rem 0;
        background: #0f1729;
    }

    .section-title {
        text-align: center;
        font-size: 3rem;
        margin-bottom: 4rem;
        font-weight: 700;
    }

    .problem-grid {
        display: grid;
        gap: 2rem;
        max-width: 800px;
        margin: 0 auto;
    }

    .problem-item {
        background: rgba(255,255,255,0.05);
        padding: 2rem;
        border-radius: 20px;
        border-left: 4px solid #ef4444;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .problem-item:hover {
        background: rgba(255,255,255,0.08);
        transform: translateX(10px);
    }

    .problem-item::before {
        content: '❌';
        margin-right: 1rem;
        font-size: 1.5rem;
    }

    /* Solution Section */
    .solution-section {
        padding: 6rem 0;
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    }

    .solution-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 3rem;
        margin-top: 4rem;
    }

    .solution-card {
        background: rgba(255,255,255,0.1);
        padding: 3rem 2rem;
        border-radius: 30px;
        text-align: center;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        transition: all 0.4s ease;
    }

    .solution-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .solution-card h3 {
        font-size: 2rem;
        margin-bottom: 1.5rem;
        color: #fbbf24;
    }

    .solution-card ul {
        list-style: none;
        text-align: left;
    }

    .solution-card li {
        margin-bottom: 1rem;
        padding-left: 1.5rem;
        position: relative;
    }

    .solution-card li::before {
        content: '→';
        position: absolute;
        left: 0;
        color: #fbbf24;
    }

    /* Counter Section */
    .counter-section {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        padding: 2rem 0;
        text-align: center;
        font-size: 1.3rem;
        font-weight: 700;
    }

    .counter-section span {
        color: #fef3c7;
    }

    /* Pricing Section */
    .pricing-section {
        padding: 6rem 0;
        background: #0a0e27;
    }

    .pricing-special {
        text-align: center;
        background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
        padding: 1rem;
        border-radius: 20px;
        margin-bottom: 3rem;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .pricing-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }

    .pricing-card {
        background: rgba(255,255,255,0.05);
        padding: 3rem 2rem;
        border-radius: 30px;
        text-align: center;
        border: 2px solid transparent;
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
    }

    .pricing-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    .pricing-card.featured {
        border-color: #fbbf24;
        transform: scale(1.05);
        box-shadow: 0 20px 60px rgba(251, 191, 36, 0.3);
    }

    .pricing-card.featured::before {
        background: linear-gradient(90deg, #fbbf24, #f59e0b);
        height: 8px;
    }

    .pricing-card:hover {
        border-color: #667eea;
        transform: scale(1.05);
        box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
    }

    .pricing-card.featured:hover {
        transform: scale(1.08);
    }

    .badge {
        display: inline-block;
        background: #fbbf24;
        color: #0a0e27;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .pricing-card h3 {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: #fff;
    }

    .price {
        font-size: 3.5rem;
        font-weight: 800;
        color: #fbbf24;
        margin: 1.5rem 0;
    }

    .price-note {
        font-size: 1rem;
        opacity: 0.8;
        margin-bottom: 2rem;
    }

    .features-list {
        list-style: none;
        text-align: left;
        margin: 2rem 0;
        padding: 0;
    }

    .features-list li {
        margin-bottom: 1rem;
        padding-left: 2rem;
        position: relative;
        opacity: 0.9;
    }

    .features-list li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #10b981;
        font-weight: 700;
        font-size: 1.3rem;
    }

    .btn-cta {
        display: block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        padding: 1rem 2rem;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        margin-top: 2rem;
    }

    .btn-cta:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
    }

    .pricing-card.featured .btn-cta {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #0a0e27;
    }

    /* System Requirements */
    .requirements-section {
        padding: 6rem 0;
        background: #0f1729;
    }

    .requirements-box {
        background: rgba(255,255,255,0.05);
        padding: 3rem;
        border-radius: 30px;
        max-width: 800px;
        margin: 3rem auto;
        border: 2px solid rgba(102, 126, 234, 0.3);
    }

    .requirements-box ul {
        list-style: none;
    }

    .requirements-box li {
        margin-bottom: 1.5rem;
        padding-left: 2rem;
        position: relative;
        font-size: 1.1rem;
    }

    .requirements-box li::before {
        content: '•';
        position: absolute;
        left: 0;
        color: #667eea;
        font-size: 2rem;
        line-height: 1;
    }

    /* FAQ Section */
    .faq-section {
        padding: 6rem 0;
        background: #0a0e27;
    }

    .faq-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .faq-item {
        background: rgba(255,255,255,0.05);
        margin-bottom: 1.5rem;
        border-radius: 20px;
        overflow: hidden;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .faq-item:hover {
        border-color: rgba(102, 126, 234, 0.3);
    }

    .faq-item.active {
        border-color: #667eea;
    }

    .faq-question {
        padding: 2rem;
        font-size: 1.3rem;
        font-weight: 600;
        cursor: pointer;
        position: relative;
        user-select: none;
        margin: 0;
    }

    .faq-question::after {
        content: '+';
        position: absolute;
        right: 2rem;
        font-size: 2rem;
        color: #667eea;
        transition: transform 0.3s ease;
    }

    .faq-item.active .faq-question::after {
        transform: rotate(45deg);
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease;
        padding: 0 2rem;
    }

    .faq-item.active .faq-answer {
        max-height: 1000px;
        padding: 0 2rem 2rem 2rem;
    }

    .faq-answer p {
        margin-bottom: 1rem;
        line-height: 1.6;
        opacity: 0.9;
    }

    .faq-answer ul {
        list-style: none;
        margin: 1rem 0;
    }

    .faq-answer li {
        margin-bottom: 0.75rem;
        padding-left: 2rem;
        position: relative;
    }

    .faq-answer li::before {
        content: '→';
        position: absolute;
        left: 0;
        color: #667eea;
    }

    /* Final CTA */
    .final-cta {
        padding: 6rem 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        text-align: center;
    }

    .final-cta h2 {
        font-size: 3rem;
        margin-bottom: 2rem;
    }

    .final-cta .btn-primary {
        font-size: 1.5rem;
        padding: 1.5rem 4rem;
        background: #fff;
        color: #667eea;
        margin-bottom: 2rem;
    }

    .final-cta p {
        font-size: 1.2rem;
        opacity: 0.9;
    }

    @media (max-width: 768px) {
        .hero-product h1 {
            font-size: 2.5rem;
        }

        .tagline {
            font-size: 1.3rem;
        }

        .section-title {
            font-size: 2rem;
        }

        .cta-buttons {
            flex-direction: column;
        }

        .trust-badges {
            flex-direction: column;
            gap: 1rem;
        }

        .pricing-grid {
            grid-template-columns: 1fr;
        }

        .pricing-card.featured {
            transform: scale(1);
        }
    }

    @keyframes blink {
        0%, 50%, 100% { opacity: 1; }
        25%, 75% { opacity: 0.5; }
    }
</style>

<div id="ulnec-landing-page">
    <!-- Hero Section -->
    <section class="hero-product">
        <div class="container">
            <h1>⚡ UL/NEC Compliance Checker for AutoCAD</h1>
            <p class="tagline">Save 15-20 Hours Per Panel with Automated UL508A & NEC Validation</p>
            <div class="pricing-highlight">
                🎉 Beta Launch: Lock in $75/mo Forever (Save 50% for Life)
            </div>
            
            <div class="cta-buttons">
                <a href="#pricing" class="btn-primary">
                    Start 30-Day Free Trial
                </a>
                <a href="#demo" class="btn-secondary">
                    Watch 3-Min Demo
                </a>
            </div>
            
            <div class="trust-badges">
                <span>✓ No credit card required</span>
                <span>✓ 1,200+ compliance rules</span>
                <span>✓ 10,000+ component database</span>
            </div>
        </div>
    </section>

    <!-- Problem Section -->
    <section class="problem-section">
        <div class="container">
            <h2 class="section-title">The Challenge Electrical Engineers Face</h2>
            <div class="problem-grid">
                <div class="problem-item">Manual UL508A compliance checking takes 18-22 hours per panel</div>
                <div class="problem-item">Easy to miss critical NEC violations (85% accuracy rate)</div>
                <div class="problem-item">Rework costs $5,000-$50,000 after panel production</div>
                <div class="problem-item">Inspector rejections cause 3-4 revision cycles</div>
                <div class="problem-item">Component databases scattered across 1,000+ datasheets</div>
            </div>
        </div>
    </section>

    <!-- Solution Section -->
    <section class="solution-section">
        <div class="container">
            <h2 class="section-title">Your Complete Compliance Solution</h2>
            <div class="solution-grid">
                <div class="solution-card">
                    <h3>✅ VALIDATE</h3>
                    <ul>
                        <li>1,200+ UL508A/NEC rules</li>
                        <li>Checked in 2-3 minutes</li>
                        <li>99.7% accuracy rate</li>
                        <li>Wire sizing & voltage drop</li>
                    </ul>
                </div>
                <div class="solution-card">
                    <h3>✅ DETECT</h3>
                    <ul>
                        <li>10,000+ components</li>
                        <li>11 manufacturers</li>
                        <li>Visual highlights</li>
                        <li>Error prevention</li>
                    </ul>
                </div>
                <div class="solution-card">
                    <h3>✅ REPORT</h3>
                    <ul>
                        <li>Professional PDF reports</li>
                        <li>Automated BOM generation</li>
                        <li>NEC article citations</li>
                        <li>Inspector-ready format</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Counter Section -->
    <section class="counter-section">
        <div class="container">
            ⚡ <span id="beta-urgency">Beta Launch Pricing - Save 50% for Life</span> | ⏰ <span>Lock in $75/mo Forever - Expires April 30, 2026</span>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing-section" id="pricing">
        <div class="container">
            <div class="pricing-special">🎯 BETA LAUNCH PRICING - LOCK IN 50% OFF FOR LIFE</div>
            <h2 class="section-title">Choose Your Plan</h2>
            <p style="text-align: center; font-size: 1.2rem; opacity: 0.9; margin-bottom: 3rem;">Start with a 30-day free trial. No credit card required. Cancel anytime.</p>
            
            <div class="pricing-grid">
                <!-- Plan 1: Professional -->
                <div class="pricing-card featured">
                    <span class="badge">💎 Most Popular</span>
                    <h3>PROFESSIONAL</h3>
                    <div class="price">$37.50<span style="font-size: 1.5rem;">/month</span></div>
                    <p class="price-note">First 6 months, then $75/month forever</p>
                    <div style="text-align: center; margin: 1rem 0; padding: 0.5rem; background: rgba(255,255,255,0.1); border-radius: 10px; font-size: 0.95rem;">
                        <strong>Save $888/year</strong> vs regular $149/month<br>
                        Regular price after April 30: $149/month
                    </div>
                    
                    <ul class="features-list">
                        <li>✅ Unlimited NEC/UL508A validations</li>
                        <li>✅ 1,200+ compliance rules</li>
                        <li>✅ Professional PDF reports</li>
                        <li>✅ Automated BOM generation</li>
                        <li>✅ 10,000+ component database</li>
                        <li>✅ Custom branding on reports</li>
                        <li>✅ Priority email support (48hr)</li>
                        <li>✅ All future features included</li>
                        <li>✅ Lock in $75/mo rate forever</li>
                    </ul>
                    
                    <a href="<?php echo esc_url(home_url('/register')); ?>?tier=professional" class="btn-cta">
                        Start 30-Day Free Trial
                    </a>
                    <p style="text-align: center; font-size: 0.85rem; margin-top: 1rem; opacity: 0.8;">No credit card required</p>
                </div>

                <!-- Plan 2: Team -->
                <div class="pricing-card">
                    <span class="badge">👥 Best for Teams</span>
                    <h3>TEAM (5 USERS)</h3>
                    <div class="price">$200<span style="font-size: 1.5rem;">/month</span></div>
                    <p class="price-note">Year 1, then $280/month forever</p>
                    <div style="text-align: center; margin: 1rem 0; padding: 0.5rem; background: rgba(255,255,255,0.1); border-radius: 10px; font-size: 0.95rem;">
                        <strong>Save $1,428/year</strong> vs regular $399/month<br>
                        Regular price after April 30: $399/month
                    </div>
                    
                    <ul class="features-list">
                        <li>✅ Everything in Professional</li>
                        <li>✅ <strong>5 user licenses</strong> included</li>
                        <li>✅ Team collaboration tools</li>
                        <li>✅ Shared component library</li>
                        <li>✅ Batch validation (100+ drawings)</li>
                        <li>✅ Centralized license management</li>
                        <li>✅ Phone support (24hr response)</li>
                        <li>✅ Team training session included</li>
                        <li>✅ Lock in $280/mo rate forever</li>
                    </ul>
                    
                    <a href="<?php echo esc_url(home_url('/register')); ?>?tier=team" class="btn-cta">
                        Start 30-Day Free Trial
                    </a>
                    <p style="text-align: center; font-size: 0.85rem; margin-top: 1rem; opacity: 0.8;">Try with full team for 30 days</p>
                </div>

                <!-- Plan 3: Enterprise -->
                <div class="pricing-card">
                    <span class="badge">🏢 Custom Solution</span>
                    <h3>ENTERPRISE</h3>
                    <div class="price">Custom<span style="font-size: 1.5rem;"> Pricing</span></div>
                    <p class="price-note">Starting at $12,000/year</p>
                    <div style="text-align: center; margin: 1rem 0; padding: 0.5rem; background: rgba(255,255,255,0.1); border-radius: 10px; font-size: 0.95rem;">
                        For 20+ users or custom integrations<br>
                        Volume discounts available
                    </div>
                    
                    <ul class="features-list">
                        <li>✅ Everything in Team</li>
                        <li>✅ <strong>Unlimited users</strong></li>
                        <li>✅ REST API access</li>
                        <li>✅ Custom rule engine extensions</li>
                        <li>✅ White-label option (rebrand/resell)</li>
                        <li>✅ On-premise deployment option</li>
                        <li>✅ 99.9% uptime SLA</li>
                        <li>✅ Dedicated success manager</li>
                        <li>✅ 4-hour critical bug response</li>
                    </ul>
                    
                    <a href="mailto:support@jdsancontrols.com?subject=Enterprise%20Quote" class="btn-cta">
                        Request Enterprise Quote
                    </a>
                    <p style="text-align: center; font-size: 0.85rem; margin-top: 1rem; opacity: 0.8;">Response within 24 hours</p>
                </div>
            </div>
            
            <!-- Pricing Guarantee -->
            <div style="text-align: center; margin-top: 4rem; padding: 2rem; background: rgba(255,255,255,0.05); border-radius: 20px;">
                <h3 style="font-size: 1.8rem; margin-bottom: 1rem;">💯 30-Day Money-Back Guarantee</h3>
                <p style="font-size: 1.1rem; opacity: 0.9;">Not satisfied? Email us within 30 days for a full refund. No questions asked.</p>
            </div>
        </div>
    </section>

    <!-- System Requirements -->
    <section class="requirements-section">
        <div class="container">
            <h2 class="section-title">System Requirements</h2>
            <div class="requirements-box">
                <ul>
                    <li><strong>CAD Software:</strong> AutoCAD 2024-2026 OR BricsCAD V24+</li>
                    <li><strong>Operating System:</strong> Windows 10 or Windows 11 (64-bit)</li>
                    <li><strong>RAM:</strong> 8GB minimum, 16GB recommended</li>
                    <li><strong>Disk Space:</strong> 500MB for plugin + database</li>
                    <li><strong>Internet:</strong> Required for license activation & updates</li>
                    <li><strong>.NET Framework:</strong> 8.0 or higher (included in installer)</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <div class="faq-container">
                
                <div class="faq-item">
                    <h3 class="faq-question">What happens after the 30-day trial?</h3>
                    <div class="faq-answer">
                        <p>You'll receive an email with upgrade options. Your trial won't auto-convert to paid - you choose if/when to upgrade. All your validation data and settings are preserved.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">Can I cancel anytime?</h3>
                    <div class="faq-answer">
                        <p>Yes, cancel anytime. No contracts, no commitments. Month-to-month billing. If you cancel, you can reactivate later and your locked-in beta pricing will still apply (if you signed up during beta period).</p>
                    </div>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">What if I lock in $75/month and you add more features?</h3>
                    <div class="faq-answer">
                        <p>You get ALL future features at no extra cost. Your rate stays $75/month forever. No hidden fees, no surprise price increases. This includes BricsCAD support (Q3 2026), API access upgrades, and all new compliance rules.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">Can I upgrade from Professional to Team later?</h3>
                    <div class="faq-answer">
                        <p>Yes! You'll keep your Early Adopter discount. Email us at support@jdsancontrols.com to upgrade. Pro-rated billing applies - you only pay the difference for the current month.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">What AutoCAD versions are supported?</h3>
                    <div class="faq-answer">
                        <p>AutoCAD 2020, 2021, 2022, 2023, 2024, 2025 are fully supported. BricsCAD V24+ support coming Q3 2026. Works with AutoCAD LT, Civil 3D, Mechanical, and Electrical editions.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">Do you offer annual billing?</h3>
                    <div class="faq-answer">
                        <p>Yes! Pay annually and save 10% extra:</p>
                        <ul>
                            <li>Professional: $810/year (vs $900 paid monthly) - locked in forever</li>
                            <li>Team: $3,024/year (vs $3,360 paid monthly) - locked in after Year 1</li>
                        </ul>
                        <p>Annual billing locks in your rate and you save an additional 10%.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">Can I try it on multiple machines during trial?</h3>
                    <div class="faq-answer">
                        <p>Yes, activate trial on up to 3 machines. Paid licenses are floating (1 user = use on multiple machines, just not simultaneously). Team licenses allow simultaneous use by all licensed users.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">Is my data secure? Do you upload my drawings?</h3>
                    <div class="faq-answer">
                        <p>We never upload your drawings. All validation happens locally on your machine. We only collect anonymous usage analytics (opt-out available). Your drawings stay private and secure on your computer.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">What if I need help during the trial?</h3>
                    <div Save 15-20 Hours Per Panel?</h2>
            <p style="font-size: 1.3rem; margin-bottom: 2rem;">Lock in $75/mo forever. Beta pricing expires April 30, 2026.</p>
            <a href="#pricing" class="btn-primary">Start 30-Day Free Trial</a>
            <p>Join 50+div>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">What happens after April 30, 2026 (beta launch deadline)?</h3>
                    <div class="faq-answer">
       Beta pricing urgency tracker
    const betaDeadline = new Date('2026-04-30T23:59:59');
    const now = new Date();
    const daysRemaining = Math.ceil((betaDeadline - now) / (1000 * 60 * 60 * 24));

    function updateBetaUrgency() {
        const urgencyElement = $('#beta-urgency');
        if (daysRemaining > 0) {
            if (daysRemaining <= 7) {
                urgencyElement.text(`⚠️ URGENT: Only ${daysRemaining} days to lock in 50% off forever!`);
                urgencyElement.css({
                    'color': '#fef3c7',
                    'animation': 'blink 1s infinite'
                });
            } else if (daysRemaining <= 30) {
                urgencyElement.text(`🔥 ${daysRemaining} days left - Beta Launch Pricing`);
            } else {
                urgencyElement.text('Beta Launch Pricing - Save 50% for Life');
            }
        } else {
            urgencyElement.text('Beta pricing has ended - contact us for current pricing');
        }
    }

    updateBetaUrgencynt ? $count : 0;
    ?>;
    const totalFoundersSpots = 25;
    const spotsRemaining = totalFoundersSpots - foundersSpotsTaken;

    function updateCounter() {
        const counterElement = $('#spots-remaining');
        if (spotsRemaining > 0) {
            counterElement.text(`Only ${spotsRemaining} of 25 FREE Licenses Remaining`);
            if (spotsRemaining <= 5) {
                counterElement.css({
                    'color': '#fef3c7',
                    'animation': 'blink 1s infinite'
                });
            }
        } else {
            counterElement.text('Founders Tier SOLD OUT - Early Adopter Available');
        }
    }

    updateCounter();

    // FAQ Accordion functionality
    $('.faq-question').on('click', function() {
        const faqItem = $(this).parent();
        const isActive = faqItem.hasClass('active');
        
        // Close all other items
        $('.faq-item').removeClass('active');
        
        // Toggle current item
        if (!isActive) {
            faqItem.addClass('active');
        }
    });

    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $($(this).attr('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 50
            }, 1000);
        }
    });
});
</script>

<?php get_footer(); ?>
