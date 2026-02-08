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
        margin-bottom: 3rem;
        opacity: 0.95;
        font-weight: 300;
        animation: fadeInUp 0.8s ease-out 0.2s backwards;
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
            <p class="tagline">Validate electrical panel designs instantly</p>
            
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
                <span>✓ Full features enabled</span>
                <span>✓ Windows 10/11 compatible</span>
            </div>
        </div>
    </section>

    <!-- Problem Section -->
    <section class="problem-section">
        <div class="container">
            <h2 class="section-title">The Challenge Electrical Engineers Face</h2>
            <div class="problem-grid">
                <div class="problem-item">Manual UL508A compliance checking takes 4-6 hours</div>
                <div class="problem-item">Easy to miss critical NEC violations</div>
                <div class="problem-item">Rework costs $5,000-$50,000 after panel production</div>
                <div class="problem-item">Component SCCR ratings scattered across datasheets</div>
                <div class="problem-item">No automated validation in AutoCAD</div>
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
                        <li>80 UL508A/NEC rules</li>
                        <li>Checked in seconds</li>
                        <li>SCCR calculation</li>
                        <li>Wire gauge check</li>
                    </ul>
                </div>
                <div class="solution-card">
                    <h3>✅ DETECT</h3>
                    <ul>
                        <li>Real-time checking</li>
                        <li>As you draw</li>
                        <li>Visual highlights</li>
                        <li>Error prevention</li>
                    </ul>
                </div>
                <div class="solution-card">
                    <h3>✅ REPORT</h3>
                    <ul>
                        <li>PDF documentation</li>
                        <li>Auto-export</li>
                        <li>Professional</li>
                        <li>Compliance ready</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Counter Section -->
    <section class="counter-section">
        <div class="container">
            ⚡ <span id="spots-remaining">Only 25 FREE Licenses Available</span> | ⏰ <span>Offer Expires: March 31, 2026</span>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing-section" id="pricing">
        <div class="container">
            <div class="pricing-special">🎯 EXCLUSIVE BETA LAUNCH TIERS</div>
            <h2 class="section-title">Choose Your Beta Tier</h2>
            
            <div class="pricing-grid">
                <!-- Tier 1: Founders (Free) -->
                <div class="pricing-card featured">
                    <span class="badge">🏆 Most Exclusive</span>
                    <h3>FOUNDERS TIER</h3>
                    <div class="price">FREE<span style="font-size: 1.5rem;"> Forever</span></div>
                    <p class="price-note">First 25 Beta Testers Only</p>
                    
                    <ul class="features-list">
                        <li>FREE 1-year license ($299 value)</li>
                        <li>Renew at 50% off forever ($149/year)</li>
                        <li>Direct Slack access to dev team</li>
                        <li>Priority feature voting</li>
                        <li>Name in Hall of Fame</li>
                        <li>All features + updates</li>
                    </ul>
                    
                    <a href="<?php echo esc_url(home_url('/founders-application')); ?>" class="btn-cta">
                        🎯 Apply for FREE License
                    </a>
                </div>

                <!-- Tier 2: Early Adopter -->
                <div class="pricing-card">
                    <span class="badge">💎 Best Value</span>
                    <h3>EARLY ADOPTER</h3>
                    <div class="price">$149<span style="font-size: 1.5rem;">/year</span></div>
                    <p class="price-note">Locked in forever (50% off retail)</p>
                    
                    <ul class="features-list">
                        <li>$149/year locked-in pricing</li>
                        <li>Priority email support (48hr)</li>
                        <li>Feature request voting</li>
                        <li>All updates included</li>
                        <li>Beta tester recognition</li>
                        <li>Commercial use allowed</li>
                    </ul>
                    
                    <a href="<?php echo esc_url(home_url('/register')); ?>?tier=early_adopter" class="btn-cta">
                        Start Free Trial
                    </a>
                </div>

                <!-- Tier 3: Beta Tester -->
                <div class="pricing-card">
                    <span class="badge">⚡ Last Chance</span>
                    <h3>BETA TESTER</h3>
                    <div class="price">$224<span style="font-size: 1.5rem;">/year</span></div>
                    <p class="price-note">Locked in forever (25% off retail)</p>
                    
                    <ul class="features-list">
                        <li>$224/year locked-in pricing</li>
                        <li>Email support (72hr)</li>
                        <li>All compliance features</li>
                        <li>Regular updates</li>
                        <li>Knowledge base access</li>
                        <li>Community forum</li>
                    </ul>
                    
                    <a href="<?php echo esc_url(home_url('/register')); ?>?tier=beta" class="btn-cta">
                        Start Free Trial
                    </a>
                </div>
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
                    <h3 class="faq-question">How do I earn a FREE lifetime license in Founders Tier?</h3>
                    <div class="faq-answer">
                        <p>To earn a FREE lifetime license, you must complete all four requirements within 60 days of installing the beta:</p>
                        <ul>
                            <li>Submit at least 3 bug reports or feature suggestions</li>
                            <li>Record a 30-second video testimonial about your experience</li>
                            <li>Allow us to create a case study featuring your company (with approval rights)</li>
                            <li>Post about the product on LinkedIn (we'll provide talking points)</li>
                        </ul>
                        <p>If you complete these requirements, your license becomes FREE forever with 50% off all renewals.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">What happens if I start as Founders Tier but don't complete requirements?</h3>
                    <div class="faq-answer">
                        <p>No problem! You'll automatically be moved to the Early Adopter tier at $149/year. You keep all your trial data and settings. We'll send you reminders at 30 and 45 days to help you complete the requirements if you want the free license.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">What's included in the 30-day free trial?</h3>
                    <div class="faq-answer">
                        <p>Everything! You get full access to all 80 UL508A/NEC compliance rules, SCCR calculations, real-time validation, PDF report generation, and priority support. No features are locked. No credit card required.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">Does this work with BricsCAD or only AutoCAD?</h3>
                    <div class="faq-answer">
                        <p>Yes! The plugin works with AutoCAD 2024-2026 AND BricsCAD V24+. Both platforms are fully supported with identical features and performance.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">What happens after the beta period ends?</h3>
                    <div class="faq-answer">
                        <p>Your locked-in beta pricing continues forever! Founders Tier stays FREE with 50% off renewals. Early Adopters keep their $149/year pricing. Beta Testers keep $224/year. Once we exit beta (estimated Q3 2026), the standard price will be $299/year, so your savings continue for life.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">Can multiple team members use one license?</h3>
                    <div class="faq-answer">
                        <p>Each license is for one user/one computer. However, we offer volume discounts for teams: 3-5 licenses get 15% off, 6-10 licenses get 25% off, 10+ licenses get 35% off. Beta pricing applies to all team licenses purchased during the beta period.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <h3 class="faq-question">What kind of support do I get as a beta tester?</h3>
                    <div class="faq-answer">
                        <p>Founders Tier gets direct Slack/email access to our dev team (response within 24 hours). Early Adopters get priority email support (response within 48 hours). All beta testers get access to our knowledge base, video tutorials, and community forum.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="final-cta">
        <div class="container">
            <h2>Ready to Eliminate Compliance Headaches?</h2>
            <a href="#pricing" class="btn-primary">Get Started - 30 Days Free</a>
            <p>Join 25 electrical engineers already using UL/NEC Compliance Checker</p>
        </div>
    </section>
</div>

<script>
jQuery(document).ready(function($) {
    // Real-time counter for Founders Tier spots
    // Update from database
    let foundersSpotsTaken = <?php 
        global $wpdb;
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ulnec_users WHERE tier = 'founders'");
        echo $count ? $count : 0;
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
