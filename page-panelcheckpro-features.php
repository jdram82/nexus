<?php
/**
 * Template Name: PanelcheckPRO Features Page
 * Description: Detailed feature and pricing page for PanelcheckPRO (no menu, no testimonials)
 */

show_admin_bar( false );

$download_url   = home_url( '/login' );
$download_label = 'Login to Download';

if ( is_user_logged_in() && class_exists( 'ULNEC_Plugin' ) ) {
	$plugin = ULNEC_Plugin::instance();
	if ( $plugin && isset( $plugin->download ) && is_object( $plugin->download ) ) {
		$download_url = $plugin->download->get_download_link();
	} else {
		$download_url = add_query_arg(
			array(
				'ulnec_download' => '1',
				'version'        => 'latest',
				'token'          => wp_create_nonce( 'ulnec_download' ),
			),
			home_url()
		);
	}
	$download_label = 'Download MSI (Beta)';
}

get_header();
?>

<style>
	.site-header,
	#site-header,
	.page-header,
	.entry-header,
	#breadcrumbs,
	.site-footer,
	#site-footer,
	footer.site-footer {
		display: none !important;
	}

	* { margin: 0; padding: 0; box-sizing: border-box; }

	:root {
		--primary-color: #2563eb;
		--primary-dark: #1e40af;
		--secondary-color: #10b981;
		--accent-color: #f59e0b;
		--text-dark: #1f2937;
		--text-light: #6b7280;
		--bg-light: #f9fafb;
		--bg-white: #ffffff;
		--border-color: #e5e7eb;
	}

	body {
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
		line-height: 1.6;
		color: var(--text-dark);
		background-color: var(--bg-white);
	}

	.container { max-width: 1200px; margin: 0 auto; }

	.badge {
		display: inline-block;
		padding: 0.5rem 1rem;
		background: rgba(255,255,255,0.2);
		border-radius: 20px;
		font-size: 0.9rem;
		margin-bottom: 2rem;
		backdrop-filter: blur(10px);
	}

	.btn {
		padding: 0.75rem 1.5rem;
		border-radius: 8px;
		font-weight: 600;
		text-decoration: none;
		display: inline-block;
		transition: all 0.3s;
		border: none;
		cursor: pointer;
		font-size: 1rem;
	}

	.btn-primary { background: var(--primary-color); color: white; }
	.btn-primary:hover {
		background: var(--primary-dark);
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(37,99,235,0.3);
	}

	.btn-secondary {
		background: white;
		color: var(--primary-color);
		border: 2px solid var(--primary-color);
	}

	.btn-secondary:hover {
		background: var(--primary-color);
		color: white;
	}

	.btn-large { padding: 1rem 2.5rem; font-size: 1.1rem; }

	.hero {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		padding: 4rem 2rem;
		text-align: center;
	}

	.hero-content { max-width: 900px; margin: 0 auto; }
	.hero h1 { font-size: 3rem; margin-bottom: 1.5rem; line-height: 1.2; }
	.hero p { font-size: 1.3rem; margin-bottom: 2rem; opacity: 0.95; }

	.hero-buttons {
		display: flex;
		gap: 1rem;
		justify-content: center;
		flex-wrap: wrap;
	}

	.hero-stats {
		display: flex;
		justify-content: center;
		gap: 3rem;
		margin-top: 3rem;
		flex-wrap: wrap;
	}

	.stat { text-align: center; }
	.stat-number { font-size: 2.5rem; font-weight: 700; display: block; }
	.stat-label { font-size: 0.9rem; opacity: 0.9; }

	.features,
	.validation-categories { padding: 5rem 2rem; background: var(--bg-white); }
	.how-it-works,
	.pricing,
	.faq { padding: 5rem 2rem; background: var(--bg-light); }

	.section-header { text-align: center; margin-bottom: 3rem; }
	.section-header h2 { font-size: 2.5rem; margin-bottom: 1rem; }
	.section-header p { font-size: 1.2rem; color: var(--text-light); max-width: 700px; margin: 0 auto; }

	.features-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 2rem;
	}

	.feature-card {
		background: var(--bg-white);
		border: 1px solid var(--border-color);
		border-radius: 12px;
		padding: 2rem;
		transition: all 0.3s;
	}

	.feature-card:hover {
		transform: translateY(-5px);
		box-shadow: 0 10px 30px rgba(0,0,0,0.1);
	}

	.feature-icon {
		width: 60px;
		height: 60px;
		background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
		border-radius: 12px;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 1.8rem;
		margin-bottom: 1.5rem;
	}

	.feature-card h3 { font-size: 1.4rem; margin-bottom: 1rem; }
	.feature-card p { color: var(--text-light); line-height: 1.8; }

	.feature-list { list-style: none; margin-top: 1rem; }
	.feature-list li {
		padding: 0.5rem 0;
		padding-left: 1.5rem;
		position: relative;
		color: var(--text-light);
	}

	.feature-list li::before {
		content: "✓";
		position: absolute;
		left: 0;
		color: var(--secondary-color);
		font-weight: bold;
	}

	.steps {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 2rem;
		margin-top: 3rem;
	}

	.step { text-align: center; }
	.step-number {
		width: 60px;
		height: 60px;
		background: var(--primary-color);
		color: white;
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 1.5rem;
		font-weight: 700;
		margin: 0 auto 1.5rem;
	}

	.step h3 { font-size: 1.3rem; margin-bottom: 1rem; }
	.step p { color: var(--text-light); }

	.categories-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
		gap: 1.5rem;
		margin-top: 3rem;
	}

	.category-badge {
		background: var(--bg-light);
		border-left: 4px solid var(--primary-color);
		padding: 1.5rem;
		border-radius: 8px;
	}

	.category-badge h4 { margin-bottom: 0.5rem; }
	.category-badge p { color: var(--text-light); font-size: 0.95rem; }

	.pricing-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
		gap: 2rem;
		margin-top: 3rem;
		max-width: 1000px;
		margin-left: auto;
		margin-right: auto;
	}

	.pricing-card {
		background: var(--bg-white);
		border: 2px solid var(--border-color);
		border-radius: 12px;
		padding: 2.5rem;
		text-align: center;
		transition: all 0.3s;
		position: relative;
	}

	.pricing-card.featured {
		border-color: var(--primary-color);
		transform: scale(1.05);
		box-shadow: 0 20px 40px rgba(37,99,235,0.2);
	}

	.pricing-badge {
		position: absolute;
		top: -15px;
		left: 50%;
		transform: translateX(-50%);
		background: var(--accent-color);
		color: white;
		padding: 0.5rem 1.5rem;
		border-radius: 20px;
		font-weight: 600;
		font-size: 0.9rem;
	}

	.price { font-size: 3rem; font-weight: 700; color: var(--primary-color); }
	.price-period,
	.original-price { color: var(--text-light); }
	.original-price { text-decoration: line-through; font-size: 1.2rem; margin-bottom: 1rem; }
	.savings { color: var(--secondary-color); font-weight: 600; margin-bottom: 1.5rem; }

	.pricing-features { list-style: none; margin: 2rem 0; text-align: left; }
	.pricing-features li {
		padding: 0.75rem 0;
		padding-left: 2rem;
		position: relative;
	}
	.pricing-features li::before {
		content: "✓";
		position: absolute;
		left: 0;
		color: var(--secondary-color);
		font-weight: bold;
	}

	.deadline {
		background: #fef3c7;
		border: 1px solid var(--accent-color);
		padding: 1rem;
		border-radius: 8px;
		margin-top: 2rem;
		font-weight: 600;
	}

	.faq-list { max-width: 800px; margin: 3rem auto 0; }
	.faq-item {
		background: var(--bg-white);
		border-radius: 8px;
		margin-bottom: 1rem;
		overflow: hidden;
		border: 1px solid var(--border-color);
	}
	.faq-question { padding: 1.5rem; cursor: pointer; font-weight: 600; }
	.faq-answer { padding: 0 1.5rem 1.5rem; color: var(--text-light); line-height: 1.8; }

	.cta {
		padding: 5rem 2rem;
		background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
		color: white;
		text-align: center;
	}

	.cta h2 { font-size: 2.5rem; margin-bottom: 1rem; }
	.cta p { font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.95; }

	.panelcheck-footer {
		background: var(--text-dark);
		color: white;
		padding: 3rem 2rem 1rem;
	}

	.footer-content {
		max-width: 1200px;
		margin: 0 auto;
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 2rem;
		margin-bottom: 2rem;
	}

	.footer-section h3 { margin-bottom: 1rem; }
	.footer-section ul { list-style: none; }
	.footer-section li { margin-bottom: 0.5rem; }
	.footer-section a { color: rgba(255,255,255,0.75); text-decoration: none; }
	.footer-section a:hover { color: white; }

	.footer-bottom {
		text-align: center;
		padding-top: 2rem;
		border-top: 1px solid rgba(255,255,255,0.1);
		color: rgba(255,255,255,0.75);
	}

	@media (max-width: 768px) {
		.hero h1 { font-size: 2rem; }
		.hero p { font-size: 1.1rem; }
		.section-header h2 { font-size: 2rem; }
		.pricing-card.featured { transform: scale(1); }
	}
</style>

<section class="hero">
	<div class="hero-content">
		<div class="badge">🚀 Early Adopter Offer - Save 50% Forever!</div>
		<h1>Automated UL508A Compliance for AutoCAD</h1>
		<p>The only AutoCAD plugin that validates electrical panel designs against UL508A and NEC standards. Save 15-20 hours per panel with 99.7% accuracy.</p>
		<div class="hero-buttons">
			<a href="#cta" class="btn btn-primary btn-large">Start 30-Day Free Trial</a>
			<a href="#features" class="btn btn-secondary btn-large">See Features</a>
		</div>
		<div class="hero-stats">
			<div class="stat"><span class="stat-number">15-20</span><span class="stat-label">Hours Saved Per Panel</span></div>
			<div class="stat"><span class="stat-number">99.7%</span><span class="stat-label">Validation Accuracy</span></div>
			<div class="stat"><span class="stat-number">80+</span><span class="stat-label">Validation Rules</span></div>
			<div class="stat"><span class="stat-number">10,000+</span><span class="stat-label">Components Database</span></div>
		</div>
	</div>
</section>

<section id="features" class="features">
	<div class="container">
		<div class="section-header">
			<h2>Powerful Features Built for Panel Engineers</h2>
			<p>Everything you need to validate, document, and ensure compliance for industrial control panels</p>
		</div>
		<div class="features-grid">
			<div class="feature-card">
				<div class="feature-icon">⚡</div>
				<h3>Automated Compliance Checking</h3>
				<p>Run ULCHECK command to validate your entire panel design against UL508A and NEC rules in seconds.</p>
				<ul class="feature-list">
					<li>Wire sizing per NEC 310.16</li>
					<li>Voltage drop calculations</li>
					<li>Clearance verification</li>
					<li>Motor protection sizing</li>
				</ul>
			</div>
			<div class="feature-card">
				<div class="feature-icon">📊</div>
				<h3>Interactive Results Palette</h3>
				<p>Visual dashboard with searchable, filterable violations and one-click zoom to issues.</p>
				<ul class="feature-list">
					<li>Group by severity/category/code</li>
					<li>Search by location or description</li>
					<li>One-click zoom to violations</li>
					<li>Export to Excel/CSV</li>
				</ul>
			</div>
			<div class="feature-card">
				<div class="feature-icon">📄</div>
				<h3>Professional Reports</h3>
				<p>Generate complete compliance reports for UL submissions and documentation.</p>
				<ul class="feature-list">
					<li>Text, CSV, HTML, PDF formats</li>
					<li>Detailed code references</li>
					<li>Summary by severity/category</li>
					<li>Inspector-ready output</li>
				</ul>
			</div>
			<div class="feature-card">
				<div class="feature-icon">📦</div>
				<h3>Bill of Materials (BOM)</h3>
				<p>Automatically generate accurate BOMs with wire summaries from AutoCAD drawings.</p>
				<ul class="feature-list">
					<li>Component detection & cataloging</li>
					<li>Wire length calculations</li>
					<li>Excel/CSV/PDF export</li>
					<li>Manufacturer part numbers</li>
				</ul>
			</div>
			<div class="feature-card">
				<div class="feature-icon">🎯</div>
				<h3>Real-Time Validation</h3>
				<p>Catch compliance issues while designing, not after completion.</p>
				<ul class="feature-list">
					<li>Immediate violation feedback</li>
					<li>Live entity detection</li>
					<li>Performance optimized</li>
					<li>Toggle on/off as needed</li>
				</ul>
			</div>
			<div class="feature-card">
				<div class="feature-icon">🔄</div>
				<h3>Batch Processing</h3>
				<p>Validate multiple drawings and generate consolidated reports.</p>
				<ul class="feature-list">
					<li>Multi-drawing validation</li>
					<li>Batch report generation</li>
					<li>DXF to DWG conversion</li>
					<li>Progress tracking</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<section id="how-it-works" class="how-it-works">
	<div class="container">
		<div class="section-header">
			<h2>Get Started in 3 Simple Steps</h2>
			<p>From installation to first compliance check in under 5 minutes</p>
		</div>
		<div class="steps">
			<div class="step"><div class="step-number">1</div><h3>Install & Launch</h3><p>Install MSI and launch AutoCAD. Plugin loads automatically.</p></div>
			<div class="step"><div class="step-number">2</div><h3>Activate Free Trial</h3><p>Run ULTRIAL to activate your 30-day free trial.</p></div>
			<div class="step"><div class="step-number">3</div><h3>Run Compliance Check</h3><p>Open drawing and run ULCHECK to see violations instantly.</p></div>
		</div>
	</div>
</section>

<section class="validation-categories">
	<div class="container">
		<div class="section-header">
			<h2>7 Comprehensive Validation Categories</h2>
			<p>Complete coverage of UL508A and NEC requirements</p>
		</div>
		<div class="categories-grid">
			<div class="category-badge"><h4>⚡ Wire Sizing</h4><p>Ampacity, derating, conduit fill and continuous load checks.</p></div>
			<div class="category-badge"><h4>📉 Voltage Drop</h4><p>Branch, feeder and combined voltage drop limits.</p></div>
			<div class="category-badge"><h4>📏 Clearances</h4><p>Through-air and over-surface spacing rules.</p></div>
			<div class="category-badge"><h4>🔌 Motor Protection</h4><p>Conductor sizing, overload and branch protection checks.</p></div>
			<div class="category-badge"><h4>🔧 Grounding</h4><p>Equipment grounding conductor sizing and safety paths.</p></div>
			<div class="category-badge"><h4>🔗 Wire Paths</h4><p>Continuity and consistency validation across connections.</p></div>
			<div class="category-badge"><h4>📐 Bending Radius</h4><p>Minimum radius enforcement to prevent wire damage.</p></div>
		</div>
	</div>
</section>

<section id="pricing" class="pricing">
	<div class="container">
		<div class="section-header">
			<h2>Early Adopter Pricing</h2>
			<p>Lock in 50% discount forever - offer ends April 30, 2026</p>
		</div>
		<div class="pricing-grid">
			<div class="pricing-card">
				<h3>Professional</h3>
				<div class="original-price">Regular $149/month</div>
				<div class="price">$75<span class="price-period">/month</span></div>
				<div class="savings">Save $888/year forever</div>
				<ul class="pricing-features">
					<li>Single user license</li>
					<li>All validation rules</li>
					<li>Unlimited validations</li>
					<li>Professional reports</li>
					<li>BOM generation</li>
					<li>Email support</li>
				</ul>
				<a href="#cta" class="btn btn-primary btn-large">Start Free Trial</a>
				<div class="deadline">⏰ Offer ends April 30, 2026</div>
			</div>

			<div class="pricing-card featured">
				<div class="pricing-badge">🏆 MOST POPULAR</div>
				<h3>Team</h3>
				<div class="original-price">Regular $399/month</div>
				<div class="price">$280<span class="price-period">/month</span></div>
				<div class="savings">Save $1,428/year forever</div>
				<ul class="pricing-features">
					<li>Up to 5 user licenses</li>
					<li>Shared settings & templates</li>
					<li>Batch processing</li>
					<li>Real-time validation</li>
					<li>Priority support</li>
					<li>Free AI features (Q3 2026)</li>
				</ul>
				<a href="#cta" class="btn btn-primary btn-large">Start Free Trial</a>
				<div class="deadline">⏰ Offer ends April 30, 2026</div>
			</div>

			<div class="pricing-card">
				<h3>Enterprise</h3>
				<div class="price">Custom</div>
				<div class="price-period">Contact for quote</div>
				<ul class="pricing-features">
					<li>Unlimited users</li>
					<li>Dedicated account manager</li>
					<li>Custom integrations</li>
					<li>On-premise deployment option</li>
					<li>REST API access (pipeline)</li>
				</ul>
				<a href="mailto:support@panelcheckpro.com" class="btn btn-secondary btn-large">Contact Sales</a>
			</div>
		</div>
	</div>
</section>

<section id="faq" class="faq">
	<div class="container">
		<div class="section-header">
			<h2>Frequently Asked Questions</h2>
			<p>Everything you need to know about PanelcheckPRO</p>
		</div>
		<div class="faq-list">
			<div class="faq-item"><div class="faq-question">Which AutoCAD versions are supported?</div><div class="faq-answer">AutoCAD 2020-2025 currently. BricsCAD support is in pipeline.</div></div>
			<div class="faq-item"><div class="faq-question">How does the 30-day free trial work?</div><div class="faq-answer">Run ULTRIAL command after installation. No credit card required.</div></div>
			<div class="faq-item"><div class="faq-question">Can I use reports for UL submissions?</div><div class="faq-answer">Yes, reports include detailed violation descriptions and code references.</div></div>
			<div class="faq-item"><div class="faq-question">What support is included?</div><div class="faq-answer">Email support for Professional, priority support for Team, dedicated support for Enterprise.</div></div>
		</div>
	</div>
</section>

<section id="cta" class="cta">
	<div class="container">
		<h2>Start Your 30-Day Free Trial Today</h2>
		<p>No credit card required. Full feature access. Cancel anytime.</p>
		<div style="margin-top: 2rem;">
			<a href="<?php echo esc_url( $download_url ); ?>" class="btn btn-primary btn-large" style="background: white; color: var(--primary-color); margin-right: 1rem;"><?php echo esc_html( $download_label ); ?></a>
			<a href="<?php echo esc_url( home_url( '/register' ) ); ?>" class="btn btn-secondary btn-large">Sign Up for Beta</a>
		</div>
		<p style="margin-top: 2rem; font-size: 0.95rem; opacity: 0.9;">MSI download is served from Supabase storage for authenticated users with active license.</p>
	</div>
</section>

<section class="panelcheck-footer">
	<div class="footer-content">
		<div class="footer-section">
			<h3>PanelcheckPRO</h3>
			<p style="color: rgba(255,255,255,0.75); margin-top: 1rem;">AutoCAD plugin for automated UL508A compliance checking.</p>
		</div>
		<div class="footer-section">
			<h3>Product</h3>
			<ul>
				<li><a href="#features">Features</a></li>
				<li><a href="#pricing">Pricing</a></li>
				<li><a href="<?php echo esc_url( $download_url ); ?>">Download</a></li>
				<li><a href="#faq">FAQ</a></li>
			</ul>
		</div>
		<div class="footer-section">
			<h3>Support</h3>
			<ul>
				<li><a href="mailto:support@panelcheckpro.com">Email Support</a></li>
				<li><a href="<?php echo esc_url( home_url( '/bug-report' ) ); ?>">Report a Bug</a></li>
				<li><a href="<?php echo esc_url( home_url( '/feature-request' ) ); ?>">Feature Requests</a></li>
			</ul>
		</div>
	</div>
	<div class="footer-bottom">
		<p>&copy; 2026 PanelcheckPRO. All rights reserved.</p>
	</div>
</section>

<script>
	document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
		anchor.addEventListener('click', function(e) {
			e.preventDefault();
			var target = document.querySelector(this.getAttribute('href'));
			if (target) {
				target.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}
		});
	});

	document.querySelectorAll('.faq-question').forEach(function(question) {
		question.addEventListener('click', function() {
			var answer = this.nextElementSibling;
			var isOpen = answer.style.display === 'block';
			document.querySelectorAll('.faq-answer').forEach(function(a) { a.style.display = 'none'; });
			answer.style.display = isOpen ? 'none' : 'block';
		});
	});

	document.querySelectorAll('.faq-answer').forEach(function(answer) {
		answer.style.display = 'none';
	});
</script>

<?php get_footer(); ?>
