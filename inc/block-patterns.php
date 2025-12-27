<?php
/**
 * Nexus Block Patterns - Starter Templates
 * 
 * Register reusable block patterns for quick page building
 * 
 * @package Nexus
 * @since 1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Block Pattern Category
 */
function nexus_register_block_pattern_category() {
	register_block_pattern_category(
		'nexus-starters',
		array(
			'label' => __( 'Nexus Starter Templates', 'nexus' ),
		)
	);
}
add_action( 'init', 'nexus_register_block_pattern_category' );

/**
 * Register Block Patterns
 */
function nexus_register_block_patterns() {
	
	// Pattern 1: Hero Section with CTA
	register_block_pattern(
		'nexus/hero-section',
		array(
			'title'       => __( 'Hero Section - Tech Product', 'nexus' ),
			'description' => __( 'Eye-catching hero section with headline, description, and call-to-action buttons', 'nexus' ),
			'categories'  => array( 'nexus-starters' ),
			'content'     => '<!-- wp:cover {"url":"","dimRatio":60,"overlayColor":"primary","minHeight":600,"contentPosition":"center center","isDark":true,"align":"full"} -->
<div class="wp-block-cover alignfull is-light" style="min-height:600px"><span aria-hidden="true" class="wp-block-cover__background has-primary-background-color has-background-dim-60 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"3.5rem","fontWeight":"700"}}} -->
<h1 class="has-text-align-center" style="font-size:3.5rem;font-weight:700">Transform Your Business with AI-Powered Solutions</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.25rem"}}} -->
<p class="has-text-align-center" style="font-size:1.25rem">Cutting-edge technology for electrical systems, embedded solutions, and machine learning applications</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"2rem"}}}} -->
<div class="wp-block-buttons" style="margin-top:2rem"><!-- wp:button {"className":"is-style-fill"} -->
<div class="wp-block-button is-style-fill"><a class="wp-block-button__link wp-element-button">Get Started Free</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">View Demo</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:cover -->',
		)
	);

	// Pattern 2: Services Grid (3 Columns)
	register_block_pattern(
		'nexus/services-grid',
		array(
			'title'       => __( 'Services Grid - 3 Columns', 'nexus' ),
			'description' => __( 'Three-column services or features grid with icons and descriptions', 'nexus' ),
			'categories'  => array( 'nexus-starters' ),
			'content'     => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem","left":"2rem","right":"2rem"}}},"backgroundColor":"light"} -->
<div class="wp-block-group alignfull has-light-background-color has-background" style="padding-top:5rem;padding-right:2rem;padding-bottom:5rem;padding-left:2rem"><!-- wp:heading {"textAlign":"center","style":{"typography":{"fontSize":"2.5rem","fontWeight":"700"}}} -->
<h2 class="has-text-align-center" style="font-size:2.5rem;font-weight:700">Our Core Services</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"bottom":"3rem"}}}} -->
<p class="has-text-align-center" style="margin-bottom:3rem">Comprehensive solutions tailored to your technical needs</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"spacing":{"padding":{"top":"2rem","right":"2rem","bottom":"2rem","left":"2rem"}},"border":{"radius":"8px"}},"backgroundColor":"white"} -->
<div class="wp-block-column has-white-background-color has-background" style="border-radius:8px;padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem"><!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="has-text-align-center">⚡ Electrical Systems</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Advanced control systems and automation solutions for industrial applications</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">Learn More</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"2rem","right":"2rem","bottom":"2rem","left":"2rem"}},"border":{"radius":"8px"}},"backgroundColor":"white"} -->
<div class="wp-block-column has-white-background-color has-background" style="border-radius:8px;padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem"><!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="has-text-align-center">🤖 AI & Machine Learning</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Intelligent algorithms and predictive models for data-driven decision making</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">Learn More</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"padding":{"top":"2rem","right":"2rem","bottom":"2rem","left":"2rem"}},"border":{"radius":"8px"}},"backgroundColor":"white"} -->
<div class="wp-block-column has-white-background-color has-background" style="border-radius:8px;padding-top:2rem;padding-right:2rem;padding-bottom:2rem;padding-left:2rem"><!-- wp:heading {"textAlign":"center","level":3} -->
<h3 class="has-text-align-center">💾 Embedded Systems</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Custom firmware and hardware integration for IoT and edge computing</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">Learn More</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->',
		)
	);

	// Pattern 3: About Section with Image
	register_block_pattern(
		'nexus/about-section',
		array(
			'title'       => __( 'About Section - Image & Text', 'nexus' ),
			'description' => __( 'Two-column about section with image and content', 'nexus' ),
			'categories'  => array( 'nexus-starters' ),
			'content'     => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem","left":"2rem","right":"2rem"}}}} -->
<div class="wp-block-group alignfull" style="padding-top:5rem;padding-right:2rem;padding-bottom:5rem;padding-left:2rem"><!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","style":{"border":{"radius":"12px"}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="" alt="" style="border-radius:12px"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"style":{"typography":{"fontSize":"2.5rem"}}} -->
<h2 style="font-size:2.5rem">Innovation Driven by Excellence</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.125rem"},"spacing":{"margin":{"top":"1.5rem"}}}} -->
<p style="margin-top:1.5rem;font-size:1.125rem">We specialize in cutting-edge electrical control systems, AI/ML solutions, and embedded systems development. With over 15 years of experience, we deliver robust, scalable solutions for complex technical challenges.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"style":{"spacing":{"margin":{"top":"2rem"}}}} -->
<ul style="margin-top:2rem"><!-- wp:list-item -->
<li>✓ Industry-leading expertise in automation</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>✓ Custom AI models and machine learning pipelines</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>✓ Real-time embedded systems for IoT devices</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>✓ 24/7 support and maintenance</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"2rem"}}}} -->
<div class="wp-block-buttons" style="margin-top:2rem"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Meet Our Team</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->',
		)
	);

	// Pattern 4: Portfolio/Projects Grid
	register_block_pattern(
		'nexus/portfolio-grid',
		array(
			'title'       => __( 'Portfolio Grid - Projects Showcase', 'nexus' ),
			'description' => __( 'Grid layout for showcasing projects, case studies, or products', 'nexus' ),
			'categories'  => array( 'nexus-starters' ),
			'content'     => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"5rem","bottom":"5rem","left":"2rem","right":"2rem"}}},"backgroundColor":"light"} -->
<div class="wp-block-group alignfull has-light-background-color has-background" style="padding-top:5rem;padding-right:2rem;padding-bottom:5rem;padding-left:2rem"><!-- wp:heading {"textAlign":"center","style":{"typography":{"fontSize":"2.5rem"}}} -->
<h2 class="has-text-align-center" style="font-size:2.5rem">Featured Projects</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"bottom":"3rem"}}}} -->
<p class="has-text-align-center" style="margin-bottom:3rem">Explore our latest work and success stories</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}},"border":{"radius":"8px"}},"backgroundColor":"white"} -->
<div class="wp-block-group has-white-background-color has-background" style="border-radius:8px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="" alt=""/></figure>
<!-- /wp:image -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"1.5rem","right":"1.5rem","bottom":"1.5rem","left":"1.5rem"}}}} -->
<div class="wp-block-group" style="padding-top:1.5rem;padding-right:1.5rem;padding-bottom:1.5rem;padding-left:1.5rem"><!-- wp:heading {"level":3} -->
<h3>Industrial Automation System</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Complete control system overhaul for manufacturing facility</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">View Case Study</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}},"border":{"radius":"8px"}},"backgroundColor":"white"} -->
<div class="wp-block-group has-white-background-color has-background" style="border-radius:8px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="" alt=""/></figure>
<!-- /wp:image -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"1.5rem","right":"1.5rem","bottom":"1.5rem","left":"1.5rem"}}}} -->
<div class="wp-block-group" style="padding-top:1.5rem;padding-right:1.5rem;padding-bottom:1.5rem;padding-left:1.5rem"><!-- wp:heading {"level":3} -->
<h3>AI-Powered Analytics</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Machine learning solution for predictive maintenance</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">View Case Study</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}},"border":{"radius":"8px"}},"backgroundColor":"white"} -->
<div class="wp-block-group has-white-background-color has-background" style="border-radius:8px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="" alt=""/></figure>
<!-- /wp:image -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"1.5rem","right":"1.5rem","bottom":"1.5rem","left":"1.5rem"}}}} -->
<div class="wp-block-group" style="padding-top:1.5rem;padding-right:1.5rem;padding-bottom:1.5rem;padding-left:1.5rem"><!-- wp:heading {"level":3} -->
<h3>IoT Device Network</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Embedded systems for smart building infrastructure</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">View Case Study</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->',
		)
	);

	// Pattern 5: Call-to-Action / Contact Section
	register_block_pattern(
		'nexus/cta-section',
		array(
			'title'       => __( 'Call to Action - Contact Form', 'nexus' ),
			'description' => __( 'Eye-catching CTA section with contact information', 'nexus' ),
			'categories'  => array( 'nexus-starters' ),
			'content'     => '<!-- wp:cover {"overlayColor":"primary","minHeight":400,"contentPosition":"center center","isDark":true,"align":"full"} -->
<div class="wp-block-cover alignfull is-light" style="min-height:400px"><span aria-hidden="true" class="wp-block-cover__background has-primary-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","style":{"typography":{"fontSize":"2.5rem"}}} -->
<h2 class="has-text-align-center" style="font-size:2.5rem">Ready to Transform Your Business?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"1.25rem"}}} -->
<p class="has-text-align-center" style="font-size:1.25rem">Get in touch with our experts for a free consultation</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"margin":{"top":"3rem"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center" style="margin-top:3rem"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem"}}} -->
<h3 style="font-size:1.5rem">📞 Call Us</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>+1 (555) 123-4567</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem"}}} -->
<h3 style="font-size:1.5rem">✉️ Email Us</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>contact@yourcompany.com</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading {"level":3,"style":{"typography":{"fontSize":"1.5rem"}}} -->
<h3 style="font-size:1.5rem">📍 Visit Us</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>123 Tech Street, Innovation City</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"2rem"}}}} -->
<div class="wp-block-buttons" style="margin-top:2rem"><!-- wp:button {"backgroundColor":"white","textColor":"primary","className":"is-style-fill"} -->
<div class="wp-block-button is-style-fill"><a class="wp-block-button__link has-primary-color has-white-background-color has-text-color has-background wp-element-button">Schedule Consultation</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">Download Brochure</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:cover -->',
		)
	);
}
add_action( 'init', 'nexus_register_block_patterns' );
