<?php
/**
 * Template Name: Nexus Landing Page
 * Description: Professional landing page for Nexus Theme sales
 */

get_header();
?>

<style>
/* Landing Page Styles */
.nexus-landing {
	font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
	line-height: 1.6;
	color: #2c3e50;
}

/* Hero Section */
.hero-section {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	padding: 100px 20px;
	text-align: center;
	position: relative;
	overflow: hidden;
}

.hero-section::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
	opacity: 0.3;
}

.hero-content {
	max-width: 900px;
	margin: 0 auto;
	position: relative;
	z-index: 1;
}

.hero-section h1 {
	font-size: 3.5rem;
	font-weight: 700;
	margin-bottom: 20px;
	text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.hero-section .subtitle {
	font-size: 1.5rem;
	margin-bottom: 30px;
	opacity: 0.95;
}

.hero-section .description {
	font-size: 1.1rem;
	margin-bottom: 40px;
	opacity: 0.9;
}

.hero-cta {
	display: flex;
	gap: 20px;
	justify-content: center;
	flex-wrap: wrap;
}

.btn {
	display: inline-block;
	padding: 16px 40px;
	font-size: 1.1rem;
	font-weight: 600;
	text-decoration: none;
	border-radius: 50px;
	transition: all 0.3s ease;
	cursor: pointer;
	border: 2px solid transparent;
}

.btn-primary {
	background: white;
	color: #667eea;
}

.btn-primary:hover {
	transform: translateY(-2px);
	box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

.btn-secondary {
	background: transparent;
	color: white;
	border: 2px solid white;
}

.btn-secondary:hover {
	background: rgba(255,255,255,0.1);
	transform: translateY(-2px);
}

/* Stats Section */
.stats-section {
	background: #f8f9fa;
	padding: 60px 20px;
	text-align: center;
}

.stats-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
	gap: 40px;
	max-width: 1200px;
	margin: 0 auto;
}

.stat-item h3 {
	font-size: 3rem;
	color: #667eea;
	margin-bottom: 10px;
	font-weight: 700;
}

.stat-item p {
	font-size: 1.1rem;
	color: #666;
}

/* Features Section */
.features-section {
	padding: 80px 20px;
	max-width: 1200px;
	margin: 0 auto;
}

.section-header {
	text-align: center;
	margin-bottom: 60px;
}

.section-header h2 {
	font-size: 2.5rem;
	color: #2c3e50;
	margin-bottom: 15px;
}

.section-header p {
	font-size: 1.2rem;
	color: #666;
}

.features-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
	gap: 30px;
}

.feature-card {
	background: white;
	padding: 30px;
	border-radius: 12px;
	box-shadow: 0 4px 6px rgba(0,0,0,0.1);
	transition: all 0.3s ease;
}

.feature-card:hover {
	transform: translateY(-5px);
	box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

.feature-icon {
	font-size: 3rem;
	margin-bottom: 20px;
}

.feature-card h3 {
	font-size: 1.5rem;
	color: #2c3e50;
	margin-bottom: 15px;
}

.feature-card p {
	color: #666;
	line-height: 1.8;
}

.feature-badge {
	display: inline-block;
	padding: 4px 12px;
	border-radius: 20px;
	font-size: 0.85rem;
	font-weight: 600;
	margin-top: 10px;
}

.badge-pro {
	background: #e3f2fd;
	color: #1976d2;
}

.badge-advanced {
	background: #f3e5f5;
	color: #7b1fa2;
}

.badge-agency {
	background: #fff3e0;
	color: #e65100;
}

/* Pricing Section */
.pricing-section {
	background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
	padding: 80px 20px;
}

.pricing-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
	gap: 30px;
	max-width: 1200px;
	margin: 0 auto;
}

.pricing-card {
	background: white;
	border-radius: 16px;
	padding: 40px 30px;
	text-align: center;
	box-shadow: 0 4px 6px rgba(0,0,0,0.1);
	transition: all 0.3s ease;
	position: relative;
}

.pricing-card.featured {
	border: 3px solid #667eea;
	transform: scale(1.05);
}

.pricing-card.featured::before {
	content: 'POPULAR';
	position: absolute;
	top: -15px;
	left: 50%;
	transform: translateX(-50%);
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	padding: 6px 20px;
	border-radius: 20px;
	font-size: 0.85rem;
	font-weight: 600;
}

.pricing-card:hover {
	transform: translateY(-5px);
	box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.pricing-card.featured:hover {
	transform: scale(1.05) translateY(-5px);
}

.tier-name {
	font-size: 1.8rem;
	font-weight: 700;
	color: #2c3e50;
	margin-bottom: 10px;
}

.tier-description {
	color: #666;
	margin-bottom: 20px;
	font-size: 0.95rem;
}

.price {
	font-size: 3rem;
	font-weight: 700;
	color: #667eea;
	margin-bottom: 5px;
}

.price-period {
	color: #999;
	font-size: 1rem;
	margin-bottom: 30px;
}

.features-list {
	list-style: none;
	padding: 0;
	margin: 30px 0;
	text-align: left;
}

.features-list li {
	padding: 12px 0;
	border-bottom: 1px solid #f0f0f0;
	color: #666;
	display: flex;
	align-items: start;
}

.features-list li::before {
	content: '✓';
	color: #4caf50;
	font-weight: bold;
	margin-right: 12px;
	font-size: 1.2rem;
}

.features-list li.unavailable {
	opacity: 0.4;
}

.features-list li.unavailable::before {
	content: '✗';
	color: #ccc;
}

/* Comparison Table */
.comparison-section {
	padding: 80px 20px;
	max-width: 1400px;
	margin: 0 auto;
}

.comparison-table {
	width: 100%;
	border-collapse: collapse;
	background: white;
	border-radius: 12px;
	overflow: hidden;
	box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.comparison-table thead {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
}

.comparison-table th {
	padding: 20px;
	font-weight: 600;
	text-align: center;
}

.comparison-table td {
	padding: 16px 20px;
	border-bottom: 1px solid #f0f0f0;
	text-align: center;
}

.comparison-table tbody tr:hover {
	background: #f8f9fa;
}

.comparison-table .feature-name {
	text-align: left;
	font-weight: 500;
	color: #2c3e50;
}

.comparison-table .check {
	color: #4caf50;
	font-size: 1.5rem;
}

.comparison-table .cross {
	color: #e0e0e0;
	font-size: 1.5rem;
}

.category-header td {
	background: #f8f9fa !important;
	font-weight: 700;
	color: #667eea;
	text-align: left !important;
	font-size: 1.1rem;
	padding: 20px !important;
}

/* FAQ Section */
.faq-section {
	background: #f8f9fa;
	padding: 80px 20px;
}

.faq-container {
	max-width: 800px;
	margin: 0 auto;
}

.faq-item {
	background: white;
	margin-bottom: 20px;
	border-radius: 8px;
	overflow: hidden;
	box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.faq-question {
	padding: 20px 25px;
	cursor: pointer;
	display: flex;
	justify-content: space-between;
	align-items: center;
	font-weight: 600;
	color: #2c3e50;
	transition: all 0.3s ease;
}

.faq-question:hover {
	background: #f8f9fa;
}

.faq-question::after {
	content: '+';
	font-size: 1.5rem;
	color: #667eea;
	transition: transform 0.3s ease;
}

.faq-question.active::after {
	transform: rotate(45deg);
}

.faq-answer {
	max-height: 0;
	overflow: hidden;
	transition: max-height 0.3s ease;
	padding: 0 25px;
	color: #666;
	line-height: 1.8;
}

.faq-answer.active {
	max-height: 500px;
	padding: 0 25px 20px;
}

/* CTA Section */
.cta-section {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	padding: 80px 20px;
	text-align: center;
}

.cta-section h2 {
	font-size: 2.5rem;
	margin-bottom: 20px;
}

.cta-section p {
	font-size: 1.2rem;
	margin-bottom: 40px;
	opacity: 0.9;
	max-width: 600px;
	margin-left: auto;
	margin-right: auto;
}

/* Testimonials */
.testimonials-section {
	padding: 80px 20px;
	background: white;
}

.testimonials-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
	gap: 30px;
	max-width: 1200px;
	margin: 0 auto;
}

.testimonial-card {
	background: #f8f9fa;
	padding: 30px;
	border-radius: 12px;
	border-left: 4px solid #667eea;
}

.testimonial-text {
	font-style: italic;
	color: #666;
	margin-bottom: 20px;
	line-height: 1.8;
}

.testimonial-author {
	display: flex;
	align-items: center;
	gap: 15px;
}

.author-avatar {
	width: 50px;
	height: 50px;
	border-radius: 50%;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	font-weight: 700;
	font-size: 1.2rem;
}

.author-info h4 {
	margin: 0;
	color: #2c3e50;
}

.author-info p {
	margin: 0;
	color: #999;
	font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 768px) {
	.hero-section h1 {
		font-size: 2.5rem;
	}
	
	.hero-section .subtitle {
		font-size: 1.2rem;
	}
	
	.pricing-card.featured {
		transform: scale(1);
	}
	
	.comparison-table {
		font-size: 0.9rem;
	}
	
	.comparison-table th,
	.comparison-table td {
		padding: 12px 8px;
	}
	
	.features-grid,
	.pricing-grid,
	.testimonials-grid {
		grid-template-columns: 1fr;
	}
}

/* Utility Classes */
.container {
	max-width: 1200px;
	margin: 0 auto;
	padding: 0 20px;
}

.text-center {
	text-align: center;
}

.mt-4 {
	margin-top: 2rem;
}

.mb-4 {
	margin-bottom: 2rem;
}
</style>

<div class="nexus-landing">
	
	<!-- Hero Section -->
	<section class="hero-section">
		<div class="hero-content">
			<h1>Nexus WordPress Theme</h1>
			<p class="subtitle">The Ultimate Multi-Tier WordPress Theme</p>
			<p class="description">
				22,000+ lines of enterprise-grade code. 8 powerful features. 20 theme builder widgets.
				Choose your tier and unlock the exact features you need.
			</p>
			<div class="hero-cta">
				<a href="#pricing" class="btn btn-primary">View Pricing</a>
				<a href="#features" class="btn btn-secondary">Explore Features</a>
			</div>
		</div>
	</section>

	<!-- Stats Section -->
	<section class="stats-section">
		<div class="stats-grid">
			<div class="stat-item">
				<h3>22K+</h3>
				<p>Lines of Code</p>
			</div>
			<div class="stat-item">
				<h3>20</h3>
				<p>Theme Builder Widgets</p>
			</div>
			<div class="stat-item">
				<h3>8</h3>
				<p>Enterprise Features</p>
			</div>
			<div class="stat-item">
				<h3>4</h3>
				<p>Pricing Tiers</p>
			</div>
		</div>
	</section>

	<!-- Features Section -->
	<section class="features-section" id="features">
		<div class="section-header">
			<h2>Powerful Features for Every Need</h2>
			<p>From freelancers to agencies, we've got you covered</p>
		</div>
		
		<div class="features-grid">
			<!-- Pro Features -->
			<div class="feature-card">
				<div class="feature-icon">☁️</div>
				<h3>Cloud Storage</h3>
				<p>Store templates, assets, and backups in the cloud. Access your work from anywhere, sync across sites.</p>
				<span class="feature-badge badge-pro">Pro Tier</span>
			</div>

			<div class="feature-card">
				<div class="feature-icon">💳</div>
				<h3>Payment Gateway</h3>
				<p>Accept payments with PayPal, Stripe, Razorpay. Perfect for selling digital products or services.</p>
				<span class="feature-badge badge-pro">Pro Tier</span>
			</div>

			<div class="feature-card">
				<div class="feature-icon">🪙</div>
				<h3>Credits System</h3>
				<p>Sell credits, tokens, or virtual currency. Built-in topup system with transaction tracking.</p>
				<span class="feature-badge badge-pro">Pro Tier</span>
			</div>

			<!-- Advanced Features -->
			<div class="feature-card">
				<div class="feature-icon">🎨</div>
				<h3>Theme Builder</h3>
				<p>Visual drag-and-drop builder with 20 widgets. Create custom headers, footers, and page layouts without code.</p>
				<span class="feature-badge badge-advanced">Advanced Tier</span>
			</div>

			<div class="feature-card">
				<div class="feature-icon">🤖</div>
				<h3>AI Template Generator</h3>
				<p>Generate templates using AI. Describe what you need and get instant, production-ready designs.</p>
				<span class="feature-badge badge-advanced">Advanced Tier</span>
			</div>

			<div class="feature-card">
				<div class="feature-icon">🔄</div>
				<h3>Loop Builder</h3>
				<p>Create custom query loops for posts, products, or custom post types. Advanced filtering and pagination.</p>
				<span class="feature-badge badge-advanced">Advanced Tier</span>
			</div>

			<div class="feature-card">
				<div class="feature-icon">📝</div>
				<h3>Form Builder</h3>
				<p>Build contact forms, surveys, and lead capture forms. Conditional logic, file uploads, email notifications.</p>
				<span class="feature-badge badge-advanced">Advanced Tier</span>
			</div>

			<div class="feature-card">
				<div class="feature-icon">🔍</div>
				<h3>Advanced Filtering</h3>
				<p>AJAX-powered product filters, search, and faceted navigation. Lightning-fast results with no page reload.</p>
				<span class="feature-badge badge-advanced">Advanced Tier</span>
			</div>

			<div class="feature-card">
				<div class="feature-icon">⚡</div>
				<h3>Performance Analytics</h3>
				<p>Monitor page speed, resource usage, and bottlenecks. Get actionable insights to optimize your site.</p>
				<span class="feature-badge badge-advanced">Advanced Tier</span>
			</div>

			<!-- Agency Features -->
			<div class="feature-card">
				<div class="feature-icon">🏷️</div>
				<h3>White Label</h3>
				<p>Remove Nexus branding and add your own. Perfect for client work and reselling themes.</p>
				<span class="feature-badge badge-agency">Agency Tier</span>
			</div>

			<div class="feature-card">
				<div class="feature-icon">📊</div>
				<h3>Agency Dashboard</h3>
				<p>Manage multiple client sites from one dashboard. Monitor updates, licenses, and support tickets.</p>
				<span class="feature-badge badge-agency">Agency Tier</span>
			</div>

			<div class="feature-card">
				<div class="feature-icon">🧪</div>
				<h3>A/B Testing</h3>
				<p>Split test headlines, layouts, and CTAs. Data-driven decisions to maximize conversions.</p>
				<span class="feature-badge badge-agency">Agency Tier</span>
			</div>

			<div class="feature-card">
				<div class="feature-icon">👥</div>
				<h3>Client Portal</h3>
				<p>Give clients access to projects, files, and invoices. Branded portal with your logo and colors.</p>
				<span class="feature-badge badge-agency">Agency Tier</span>
			</div>
		</div>
	</section>

	<!-- Pricing Section -->
	<section class="pricing-section" id="pricing">
		<div class="section-header">
			<h2>Choose Your Perfect Tier</h2>
			<p>Flexible pricing for individuals, businesses, and agencies</p>
		</div>

		<div class="pricing-grid">
			<!-- Free Tier -->
			<div class="pricing-card">
				<div class="tier-name">Free</div>
				<div class="tier-description">Perfect for personal projects</div>
				<div class="price">$0</div>
				<div class="price-period">Forever Free</div>
				<a href="<?php echo home_url('/download-free'); ?>" class="btn btn-secondary">Download Free</a>
				<ul class="features-list">
					<li>Complete WordPress theme</li>
					<li>Responsive & mobile-ready</li>
					<li>WooCommerce compatible</li>
					<li>Basic customizer options</li>
					<li>GPL licensed (open source)</li>
					<li>Community support</li>
					<li class="unavailable">Cloud Storage</li>
					<li class="unavailable">Payment Gateway</li>
					<li class="unavailable">Theme Builder</li>
					<li class="unavailable">AI Generator</li>
					<li class="unavailable">White Label</li>
				</ul>
			</div>

			<!-- Pro Tier -->
			<div class="pricing-card">
				<div class="tier-name">Pro</div>
				<div class="tier-description">For freelancers & consultants</div>
				<div class="price">$199</div>
				<div class="price-period">per year / 1 site</div>
				<a href="<?php echo home_url('/checkout?tier=pro'); ?>" class="btn btn-primary">Get Pro</a>
				<ul class="features-list">
					<li>Everything in Free, plus:</li>
					<li>☁️ Cloud Storage & Sync</li>
					<li>💳 Payment Gateway Integration</li>
					<li>🪙 Credits System</li>
					<li>📦 Template Library Access</li>
					<li>🗄️ Database Schema Manager</li>
					<li>1 site activation</li>
					<li>Email support</li>
					<li class="unavailable">Theme Builder (20 widgets)</li>
					<li class="unavailable">AI Template Generator</li>
					<li class="unavailable">White Label</li>
				</ul>
			</div>

			<!-- Advanced Tier -->
			<div class="pricing-card featured">
				<div class="tier-name">Advanced</div>
				<div class="tier-description">For serious businesses</div>
				<div class="price">$299</div>
				<div class="price-period">per year / 5 sites</div>
				<a href="<?php echo home_url('/checkout?tier=advanced'); ?>" class="btn btn-primary">Get Advanced</a>
				<ul class="features-list">
					<li>Everything in Pro, plus:</li>
					<li>🎨 Theme Builder (20 widgets)</li>
					<li>🤖 AI Template Generator</li>
					<li>🔄 Loop Builder</li>
					<li>📝 Form Builder</li>
					<li>🔍 Advanced Filtering</li>
					<li>📚 Documentation System</li>
					<li>⚡ Performance Analytics</li>
					<li>🔌 Plugin Orchestrator</li>
					<li>🎯 Mega Menu Builder</li>
					<li>⚙️ Circuit Simulator</li>
					<li>5 site activations</li>
					<li>Priority email support</li>
					<li class="unavailable">White Label</li>
					<li class="unavailable">Agency Dashboard</li>
				</ul>
			</div>

			<!-- Agency Tier -->
			<div class="pricing-card">
				<div class="tier-name">Agency</div>
				<div class="tier-description">For agencies & teams</div>
				<div class="price">$599</div>
				<div class="price-period">per year / unlimited sites</div>
				<a href="<?php echo home_url('/checkout?tier=agency'); ?>" class="btn btn-primary">Get Agency</a>
				<ul class="features-list">
					<li>Everything in Advanced, plus:</li>
					<li>🏷️ White Label Branding</li>
					<li>📊 Agency Dashboard</li>
					<li>🧪 A/B Testing Suite</li>
					<li>👥 Client Portal System</li>
					<li>📈 Advanced Analytics</li>
					<li>Unlimited site activations</li>
					<li>Priority support tickets</li>
					<li>Direct Slack/Discord channel</li>
					<li>Quarterly strategy calls</li>
					<li>Early access to new features</li>
					<li>Reseller rights</li>
				</ul>
			</div>
		</div>

		<div class="text-center mt-4">
			<p style="color: #666; font-size: 1.1rem;">
				All plans include automatic updates, security patches, and 30-day money-back guarantee
			</p>
		</div>
	</section>

	<!-- Comparison Table -->
	<section class="comparison-section">
		<div class="section-header">
			<h2>Feature Comparison</h2>
			<p>See exactly what's included in each tier</p>
		</div>

		<table class="comparison-table">
			<thead>
				<tr>
					<th style="text-align: left;">Feature</th>
					<th>Free</th>
					<th>Pro<br>$199/year</th>
					<th>Advanced<br>$299/year</th>
					<th>Agency<br>$599/year</th>
				</tr>
			</thead>
			<tbody>
				<!-- Core Features -->
				<tr class="category-header">
					<td colspan="5">Core WordPress Features</td>
				</tr>
				<tr>
					<td class="feature-name">Complete WordPress Theme</td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">Responsive & Mobile Ready</td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">WooCommerce Compatible</td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">Automatic Updates</td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">GPL Licensed (Open Source)</td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>

				<!-- Pro Tier Features -->
				<tr class="category-header">
					<td colspan="5">Pro Tier Features</td>
				</tr>
				<tr>
					<td class="feature-name">☁️ Cloud Storage & Sync</td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">💳 Payment Gateway (PayPal/Stripe)</td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">🪙 Credits System</td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">📦 Template Library Access</td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>

				<!-- Advanced Tier Features -->
				<tr class="category-header">
					<td colspan="5">Advanced Tier Features</td>
				</tr>
				<tr>
					<td class="feature-name">🎨 Theme Builder (20 Widgets)</td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">🤖 AI Template Generator</td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">🔄 Loop Builder</td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">📝 Advanced Form Builder</td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">🔍 Advanced Product Filtering</td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">📚 Documentation System</td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">⚡ Performance Analytics</td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">🔌 Plugin Orchestrator</td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">🎯 Mega Menu Builder</td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
					<td><span class="check">✓</span></td>
				</tr>

				<!-- Agency Tier Features -->
				<tr class="category-header">
					<td colspan="5">Agency Tier Features</td>
				</tr>
				<tr>
					<td class="feature-name">🏷️ White Label Branding</td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">📊 Multi-Site Agency Dashboard</td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">🧪 A/B Testing Suite</td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
				</tr>
				<tr>
					<td class="feature-name">👥 Client Portal System</td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
				</tr>

				<!-- Support & Activations -->
				<tr class="category-header">
					<td colspan="5">Support & Activations</td>
				</tr>
				<tr>
					<td class="feature-name">Site Activations</td>
					<td>1</td>
					<td>1</td>
					<td>5</td>
					<td>Unlimited</td>
				</tr>
				<tr>
					<td class="feature-name">Support Channel</td>
					<td>Community</td>
					<td>Email</td>
					<td>Priority Email</td>
					<td>Email + Slack</td>
				</tr>
				<tr>
					<td class="feature-name">Response Time</td>
					<td>Best Effort</td>
					<td>48 hours</td>
					<td>24 hours</td>
					<td>12 hours</td>
				</tr>
				<tr>
					<td class="feature-name">Reseller Rights</td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="cross">✗</span></td>
					<td><span class="check">✓</span></td>
				</tr>
			</tbody>
		</table>
	</section>

	<!-- FAQ Section -->
	<section class="faq-section">
		<div class="section-header">
			<h2>Frequently Asked Questions</h2>
			<p>Everything you need to know about Nexus</p>
		</div>

		<div class="faq-container">
			<div class="faq-item">
				<div class="faq-question">
					How does licensing work?
				</div>
				<div class="faq-answer">
					Each license is tied to a domain. Pro tier allows 1 site activation, Advanced allows 5 sites, and Agency allows unlimited sites. You can deactivate and move licenses between sites as needed through your account dashboard.
				</div>
			</div>

			<div class="faq-item">
				<div class="faq-question">
					Can I upgrade or downgrade my tier?
				</div>
				<div class="faq-answer">
					Yes! You can upgrade anytime by paying the difference. Downgrades are processed at your next renewal date. All data is preserved during tier changes.
				</div>
			</div>

			<div class="faq-item">
				<div class="faq-question">
					What happens when my license expires?
				</div>
				<div class="faq-answer">
					Your site continues to work, but you won't receive updates or support. Premium features (Pro/Advanced/Agency) will stop working. The free tier features remain functional. Renew anytime to restore full access.
				</div>
			</div>

			<div class="faq-item">
				<div class="faq-question">
					Is Nexus compatible with page builders?
				</div>
				<div class="faq-answer">
					Yes! Nexus works with Elementor, Beaver Builder, and other major page builders. Our built-in Theme Builder (Advanced tier) is optional but offers tighter integration and better performance.
				</div>
			</div>

			<div class="faq-item">
				<div class="faq-question">
					Do you offer refunds?
				</div>
				<div class="faq-answer">
					Yes, we offer a 30-day money-back guarantee. If Nexus doesn't meet your needs, contact us within 30 days of purchase for a full refund, no questions asked.
				</div>
			</div>

			<div class="faq-item">
				<div class="faq-question">
					Can I use Nexus on client sites?
				</div>
				<div class="faq-answer">
					Pro and Advanced tiers allow client use within your activation limits. Agency tier includes reseller rights, white label branding, and unlimited activations - perfect for agencies managing multiple client sites.
				</div>
			</div>

			<div class="faq-item">
				<div class="faq-question">
					What kind of support do you provide?
				</div>
				<div class="faq-answer">
					Free tier gets community support. Pro tier includes email support (48hr response). Advanced gets priority email (24hr response). Agency tier gets email + dedicated Slack channel (12hr response) plus quarterly strategy calls.
				</div>
			</div>

			<div class="faq-item">
				<div class="faq-question">
					How do updates work?
				</div>
				<div class="faq-answer">
					Nexus automatically checks for updates every 12 hours via GitHub. Updates appear in your WordPress dashboard just like plugin updates. One-click install for new versions, with automatic rollback if anything goes wrong.
				</div>
			</div>

			<div class="faq-item">
				<div class="faq-question">
					Is the code really GPL licensed?
				</div>
				<div class="faq-answer">
					Yes! All Nexus code is 100% GPL licensed. You have unlimited freedom to modify, extend, and redistribute. Premium features require a valid license to activate, but the code is completely open for you to learn from and customize.
				</div>
			</div>

			<div class="faq-item">
				<div class="faq-question">
					What payment methods do you accept?
				</div>
				<div class="faq-answer">
					We accept all major credit cards (Visa, Mastercard, Amex, Discover), PayPal, and Stripe. All transactions are securely processed and encrypted. Automatic renewal can be managed from your account.
				</div>
			</div>
		</div>
	</section>

	<!-- Testimonials Section -->
	<section class="testimonials-section">
		<div class="section-header">
			<h2>What Developers Say</h2>
			<p>Trusted by developers and agencies worldwide</p>
		</div>

		<div class="testimonials-grid">
			<div class="testimonial-card">
				<p class="testimonial-text">
					"Nexus saved me weeks of development. The Theme Builder alone is worth 10x the price. I've built 5 client sites in the time it used to take me to build one."
				</p>
				<div class="testimonial-author">
					<div class="author-avatar">SM</div>
					<div class="author-info">
						<h4>Sarah Martinez</h4>
						<p>Freelance WordPress Developer</p>
					</div>
				</div>
			</div>

			<div class="testimonial-card">
				<p class="testimonial-text">
					"The Agency tier is a game-changer. White label + unlimited sites means I can resell Nexus to clients and keep 100% of the margin. Best investment I've made."
				</p>
				<div class="testimonial-author">
					<div class="author-avatar">JC</div>
					<div class="author-info">
						<h4>James Chen</h4>
						<p>Founder, PixelPerfect Agency</p>
					</div>
				</div>
			</div>

			<div class="testimonial-card">
				<p class="testimonial-text">
					"I was skeptical about another WordPress theme, but Nexus is different. Clean code, excellent documentation, and features that actually work. No bloat, no broken promises."
				</p>
				<div class="testimonial-author">
					<div class="author-avatar">ER</div>
					<div class="author-info">
						<h4>Emily Rodriguez</h4>
						<p>Senior Developer, TechStart Inc</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Final CTA -->
	<section class="cta-section">
		<div class="container">
			<h2>Ready to Build Something Amazing?</h2>
			<p>Join hundreds of developers already using Nexus. Choose your tier and start building today.</p>
			<div class="hero-cta">
				<a href="#pricing" class="btn btn-primary">Choose Your Tier</a>
				<a href="<?php echo home_url('/download-free'); ?>" class="btn btn-secondary">Try Free Version</a>
			</div>
			<p style="margin-top: 30px; font-size: 0.95rem; opacity: 0.8;">
				30-day money-back guarantee • Automatic updates • GPL licensed
			</p>
		</div>
	</section>

</div>

<script>
jQuery(document).ready(function($) {
	// FAQ Accordion
	$('.faq-question').on('click', function() {
		$(this).toggleClass('active');
		$(this).next('.faq-answer').toggleClass('active');
	});

	// Smooth scroll for anchor links
	$('a[href^="#"]').on('click', function(e) {
		e.preventDefault();
		var target = $(this.getAttribute('href'));
		if(target.length) {
			$('html, body').stop().animate({
				scrollTop: target.offset().top - 80
			}, 800);
		}
	});
});
</script>

<?php
get_footer();
?>
