<?php
/**
 * Nexus AI Template Generator (Advanced Tier)
 * 
 * Natural language → Template conversion
 * Monthly credit limits: 100 (Advanced), 500 (Agency)
 * 
 * @package Nexus_Theme
 * @since 1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nexus_AI_Template_Generator {
    
    /**
     * Instance
     */
    private static $instance = null;
    
    /**
     * Credit limits
     */
    const ADVANCED_CREDITS = 100;
    const AGENCY_CREDITS = 500;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        if ( ! Nexus_License_Manager::is_tier_or_higher( 'advanced' ) ) {
            return;
        }
        
        add_action( 'admin_menu', array( $this, 'add_admin_page' ), 100 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        
        // AJAX handlers
        add_action( 'wp_ajax_nexus_ai_generate_template', array( $this, 'ajax_generate_template' ) );
        add_action( 'wp_ajax_nexus_ai_refine_template', array( $this, 'ajax_refine_template' ) );
        add_action( 'wp_ajax_nexus_ai_get_credits', array( $this, 'ajax_get_credits' ) );
    }
    
    /**
     * Add admin page
     */
    public function add_admin_page() {
        add_submenu_page(
            'nexus-theme-options',
            __( 'AI Template Generator', 'nexus' ),
            __( 'AI Generator (Advanced)', 'nexus' ),
            'manage_options',
            'nexus-ai-generator',
            array( $this, 'render_admin_page' )
        );
    }
    
    /**
     * Enqueue assets
     */
    public function enqueue_assets( $hook ) {
        if ( 'nexus-options_page_nexus-ai-generator' !== $hook ) {
            return;
        }
        
        wp_enqueue_style( 'nexus-ai-generator', get_template_directory_uri() . '/pro/assets/css/ai-generator.css', array(), '1.5.0' );
        wp_enqueue_script( 'nexus-ai-generator', get_template_directory_uri() . '/pro/assets/js/ai-generator.js', array( 'jquery' ), '1.5.0', true );
        
        wp_localize_script( 'nexus-ai-generator', 'nexusAI', array(
            'ajax_url'     => admin_url( 'admin-ajax.php' ),
            'nonce'        => wp_create_nonce( 'nexus_ai_nonce' ),
            'credits_used' => $this->get_credits_used(),
            'credits_limit' => $this->get_credits_limit(),
            'tier'         => Nexus_License_Manager::get_tier(),
        ) );
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        $credits_used = $this->get_credits_used();
        $credits_limit = $this->get_credits_limit();
        $credits_remaining = $credits_limit - $credits_used;
        $tier = Nexus_License_Manager::get_tier();
        
        ?>
        <div class="wrap nexus-ai-generator-wrap">
            <h1><?php esc_html_e( 'AI Template Generator', 'nexus' ); ?></h1>
            
            <!-- Tier Info -->
            <div class="nexus-tier-info">
                <div class="tier-info-left">
                    <span class="tier-badge <?php echo esc_attr( $tier ); ?>">
                        <?php echo esc_html( ucfirst( $tier ) ); ?> Tier
                    </span>
                    <span class="credits-info">
                        <strong><?php echo esc_html( $credits_remaining ); ?></strong> credits remaining this month
                    </span>
                </div>
                <div class="tier-info-right">
                    <button id="reset-generator" class="button">Reset Generator</button>
                    <a href="admin.php?page=nexus-templates" class="button">View Templates</a>
                </div>
            </div>
            
            <!-- Credit Warning -->
            <?php if ( $credits_remaining <= 10 ) : ?>
                <div class="notice notice-warning">
                    <p>
                        <?php esc_html_e( 'You are running low on AI generation credits.', 'nexus' ); ?>
                        <?php if ( 'advanced' === $tier ) : ?>
                            <a href="admin.php?page=nexus-license"><?php esc_html_e( 'Upgrade to Agency for 500 credits/month', 'nexus' ); ?></a>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
            
            <!-- Generator Interface -->
            <div class="ai-generator-container">
                
                <!-- Step 1: Describe Template -->
                <div class="generator-step active" data-step="1">
                    <div class="step-header">
                        <span class="step-number">1</span>
                        <h2><?php esc_html_e( 'Describe Your Template', 'nexus' ); ?></h2>
                    </div>
                    
                    <div class="step-content">
                        <div class="prompt-examples">
                            <h3><?php esc_html_e( 'Try these examples:', 'nexus' ); ?></h3>
                            <button class="example-prompt" data-prompt="A modern SaaS landing page with a hero section, feature grid, pricing table, and testimonials. Use a gradient background and clean typography.">
                                SaaS Landing Page
                            </button>
                            <button class="example-prompt" data-prompt="A professional portfolio website with a full-width header, project showcase grid, about section with skills, and contact form. Use dark theme with accent colors.">
                                Portfolio Website
                            </button>
                            <button class="example-prompt" data-prompt="A documentation site with a sticky sidebar navigation, code syntax highlighting, search functionality, and breadcrumbs. Use a light, clean design.">
                                Documentation Site
                            </button>
                            <button class="example-prompt" data-prompt="An e-commerce product page with an image gallery, product details, reviews section, related products, and add-to-cart functionality.">
                                Product Page
                            </button>
                        </div>
                        
                        <textarea 
                            id="ai-prompt" 
                            rows="8" 
                            placeholder="<?php esc_attr_e( 'Describe the template you want to create. Be specific about sections, features, style, and functionality...', 'nexus' ); ?>"
                        ></textarea>
                        
                        <div class="advanced-options">
                            <h3><?php esc_html_e( 'Advanced Options (Optional)', 'nexus' ); ?></h3>
                            
                            <label>
                                <?php esc_html_e( 'Primary Color:', 'nexus' ); ?>
                                <input type="color" id="ai-primary-color" value="#0066cc">
                            </label>
                            
                            <label>
                                <?php esc_html_e( 'Typography Style:', 'nexus' ); ?>
                                <select id="ai-typography">
                                    <option value="modern">Modern (Sans-serif)</option>
                                    <option value="classic">Classic (Serif)</option>
                                    <option value="playful">Playful (Rounded)</option>
                                    <option value="minimal">Minimal (Thin)</option>
                                </select>
                            </label>
                            
                            <label>
                                <?php esc_html_e( 'Layout Style:', 'nexus' ); ?>
                                <select id="ai-layout">
                                    <option value="full-width">Full Width</option>
                                    <option value="boxed">Boxed</option>
                                    <option value="grid">Grid-based</option>
                                    <option value="asymmetric">Asymmetric</option>
                                </select>
                            </label>
                            
                            <label>
                                <?php esc_html_e( 'Content Density:', 'nexus' ); ?>
                                <select id="ai-density">
                                    <option value="compact">Compact</option>
                                    <option value="normal" selected>Normal</option>
                                    <option value="spacious">Spacious</option>
                                </select>
                            </label>
                        </div>
                        
                        <button id="generate-template" class="button button-primary button-hero">
                            <span class="dashicons dashicons-superhero"></span>
                            <?php esc_html_e( 'Generate Template with AI', 'nexus' ); ?>
                        </button>
                    </div>
                </div>
                
                <!-- Step 2: Preview & Refine -->
                <div class="generator-step" data-step="2">
                    <div class="step-header">
                        <span class="step-number">2</span>
                        <h2><?php esc_html_e( 'Preview & Refine', 'nexus' ); ?></h2>
                    </div>
                    
                    <div class="step-content">
                        <div class="preview-controls">
                            <button id="back-to-prompt" class="button">
                                <span class="dashicons dashicons-arrow-left-alt"></span>
                                <?php esc_html_e( 'Back', 'nexus' ); ?>
                            </button>
                            
                            <div class="preview-devices">
                                <button class="device-toggle active" data-device="desktop">
                                    <span class="dashicons dashicons-desktop"></span>
                                </button>
                                <button class="device-toggle" data-device="tablet">
                                    <span class="dashicons dashicons-tablet"></span>
                                </button>
                                <button class="device-toggle" data-device="mobile">
                                    <span class="dashicons dashicons-smartphone"></span>
                                </button>
                            </div>
                            
                            <button id="refine-template" class="button">
                                <span class="dashicons dashicons-edit"></span>
                                <?php esc_html_e( 'Refine with AI', 'nexus' ); ?>
                            </button>
                        </div>
                        
                        <div class="preview-container">
                            <div id="template-preview" class="device-desktop">
                                <!-- Template preview will be rendered here -->
                            </div>
                        </div>
                        
                        <div class="refinement-panel">
                            <h3><?php esc_html_e( 'Request Changes', 'nexus' ); ?></h3>
                            <textarea 
                                id="refinement-prompt" 
                                rows="3" 
                                placeholder="<?php esc_attr_e( 'E.g., Make the header taller, add a newsletter signup, change colors to blue...', 'nexus' ); ?>"
                            ></textarea>
                            <button id="apply-refinement" class="button button-primary">
                                <?php esc_html_e( 'Apply Changes (1 credit)', 'nexus' ); ?>
                            </button>
                        </div>
                        
                        <div class="template-actions">
                            <button id="save-template" class="button button-primary button-hero">
                                <span class="dashicons dashicons-yes"></span>
                                <?php esc_html_e( 'Save Template', 'nexus' ); ?>
                            </button>
                            <button id="export-code" class="button button-hero">
                                <span class="dashicons dashicons-download"></span>
                                <?php esc_html_e( 'Export Code', 'nexus' ); ?>
                            </button>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- Generation History -->
            <div class="generation-history">
                <h2><?php esc_html_e( 'Recent Generations', 'nexus' ); ?></h2>
                <div id="history-list">
                    <?php $this->render_history(); ?>
                </div>
            </div>
            
        </div>
        <?php
    }
    
    /**
     * AJAX: Generate template
     */
    public function ajax_generate_template() {
        check_ajax_referer( 'nexus_ai_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Insufficient permissions.' ) );
        }
        
		// Check credits using Credit Manager
		$credit_manager = Nexus_Credit_Manager::get_instance();
		if ( ! $credit_manager->has_credits( 1 ) ) {
			wp_send_json_error( array( 
				'message' => 'No AI credits remaining. Purchase more credits to continue.',
				'redirect' => admin_url( 'admin.php?page=nexus-credits' )
			) );
		}
		
		$prompt = sanitize_textarea_field( $_POST['prompt'] );
		$options = array(
			'primary_color' => sanitize_text_field( $_POST['primary_color'] ?? '#0066cc' ),
			'typography'    => sanitize_text_field( $_POST['typography'] ?? 'modern' ),
			'layout'        => sanitize_text_field( $_POST['layout'] ?? 'full-width' ),
			'density'       => sanitize_text_field( $_POST['density'] ?? 'normal' ),
		);
		
		// Generate template with AI
		$template = $this->generate_template( $prompt, $options );
		
		// Use credit
		$result = $credit_manager->use_credits( 1 );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}
		
		// Save to history
		$this->save_to_history( $prompt, $template );
		
		wp_send_json_success( array(
			'template'          => $template,
			'credits_available' => $credit_manager->get_available_credits(),
            wp_send_json_error( array( 'message' => 'Insufficient permissions.' ) );
        }
        
        // Check credits
        if ( ! $this->has_credits() ) {
            wp_send_json_error( array( 'message' => 'No credits remaining this month.' ) );
        }
        
        $current_template = json_decode( stripslashes( $_POST['template'] ), true );
        $refinement_prompt = sanitize_textarea_field( $_POST['prompt'] );
        
        // Refine template
        $refined_template = $this->refine_template( $current_template, $refinement_prompt );
        
        // Increment credits
        $this->increment_credits();
        
        wp_send_json_success( array(
            'template'          => $refined_template,
            'credits_used'      => $this->get_credits_used(),
            'credits_remaining' => $this->get_credits_limit() - $this->get_credits_used(),
        ) );
    }
    
    /**
     * AJAX: Get credits
     */
    public function ajax_get_credits() {
        check_ajax_referer( 'nexus_ai_nonce', 'nonce' );
        
        wp_send_json_success( array(
            'credits_used'  => $this->get_credits_used(),
            'credits_limit' => $this->get_credits_limit(),
        ) );
    }
    
    /**
     * Generate template from prompt
     */
    private function generate_template( $prompt, $options ) {
        // In production, this would call OpenAI API
        // For now, return mock template based on prompt analysis
        
        $sections = $this->extract_sections( $prompt );
        $style = $this->analyze_style( $prompt, $options );
        
        $template = array(
            'version'  => '1.0',
            'name'     => $this->extract_template_name( $prompt ),
            'type'     => $this->detect_template_type( $prompt ),
            'sections' => $sections,
            'style'    => $style,
            'html'     => $this->generate_html( $sections, $style ),
            'css'      => $this->generate_css( $sections, $style ),
        );
        
        return $template;
    }
    
    /**
     * Extract sections from prompt
     */
    private function extract_sections( $prompt ) {
        $sections = array();
        
        // Common section patterns
        $patterns = array(
            'hero'         => '/(hero|header|banner|intro)/i',
            'features'     => '/(feature|benefit|service)/i',
            'pricing'      => '/(pric|plan|package)/i',
            'testimonials' => '/(testimonial|review|feedback)/i',
            'about'        => '/(about|story|mission)/i',
            'contact'      => '/(contact|form|touch)/i',
            'portfolio'    => '/(portfolio|project|work|showcase)/i',
            'gallery'      => '/(gallery|image)/i',
            'cta'          => '/(cta|call.to.action|signup)/i',
            'footer'       => '/(footer)/i',
        );
        
        foreach ( $patterns as $section => $pattern ) {
            if ( preg_match( $pattern, $prompt ) ) {
                $sections[] = array(
                    'type'    => $section,
                    'enabled' => true,
                    'order'   => count( $sections ),
                    'config'  => $this->get_section_config( $section ),
                );
            }
        }
        
        // Ensure at least header and footer
        if ( empty( $sections ) ) {
            $sections = array(
                array( 'type' => 'hero', 'enabled' => true, 'order' => 0, 'config' => $this->get_section_config( 'hero' ) ),
                array( 'type' => 'content', 'enabled' => true, 'order' => 1, 'config' => $this->get_section_config( 'content' ) ),
                array( 'type' => 'footer', 'enabled' => true, 'order' => 2, 'config' => $this->get_section_config( 'footer' ) ),
            );
        }
        
        return $sections;
    }
    
    /**
     * Get section config
     */
    private function get_section_config( $type ) {
        $configs = array(
            'hero' => array(
                'height'     => 'tall',
                'background' => 'gradient',
                'alignment'  => 'center',
                'overlay'    => true,
            ),
            'features' => array(
                'columns' => 3,
                'layout'  => 'grid',
                'icons'   => true,
            ),
            'pricing' => array(
                'columns'   => 3,
                'highlight' => 2,
                'toggle'    => true,
            ),
            'testimonials' => array(
                'layout'    => 'slider',
                'columns'   => 3,
                'avatars'   => true,
            ),
            'portfolio' => array(
                'columns' => 3,
                'filter'  => true,
                'lightbox' => true,
            ),
            'contact' => array(
                'form_fields' => array( 'name', 'email', 'message' ),
                'map'         => true,
            ),
            'footer' => array(
                'columns' => 4,
                'social'  => true,
                'widgets' => true,
            ),
        );
        
        return $configs[ $type ] ?? array();
    }
    
    /**
     * Analyze style from prompt
     */
    private function analyze_style( $prompt, $options ) {
        $style = array(
            'primary_color'   => $options['primary_color'],
            'typography'      => $options['typography'],
            'layout'          => $options['layout'],
            'density'         => $options['density'],
            'border_radius'   => '8px',
            'shadow_style'    => 'medium',
        );
        
        // Detect style keywords
        if ( preg_match( '/(modern|contemporary|sleek)/i', $prompt ) ) {
            $style['border_radius'] = '12px';
            $style['shadow_style'] = 'subtle';
        }
        
        if ( preg_match( '/(minimal|clean|simple)/i', $prompt ) ) {
            $style['border_radius'] = '4px';
            $style['shadow_style'] = 'none';
        }
        
        if ( preg_match( '/(bold|vibrant|colorful)/i', $prompt ) ) {
            $style['shadow_style'] = 'strong';
        }
        
        return $style;
    }
    
    /**
     * Generate HTML from sections
     */
    private function generate_html( $sections, $style ) {
        $html = '';
        
        foreach ( $sections as $section ) {
            $html .= $this->get_section_html( $section['type'], $section['config'], $style );
        }
        
        return $html;
    }
    
    /**
     * Get section HTML
     */
    private function get_section_html( $type, $config, $style ) {
        // Return mock HTML for section
        $templates = array(
            'hero' => '<section class="hero-section"><div class="container"><h1>Welcome to Our Product</h1><p>Transform your business with our innovative solution</p><button class="cta-button">Get Started</button></div></section>',
            'features' => '<section class="features-section"><div class="container"><div class="features-grid"><div class="feature"><h3>Feature 1</h3><p>Description here</p></div><div class="feature"><h3>Feature 2</h3><p>Description here</p></div><div class="feature"><h3>Feature 3</h3><p>Description here</p></div></div></div></section>',
            'pricing' => '<section class="pricing-section"><div class="container"><div class="pricing-grid"><div class="price-card"><h3>Basic</h3><p class="price">$29/mo</p><button>Choose Plan</button></div><div class="price-card featured"><h3>Pro</h3><p class="price">$99/mo</p><button>Choose Plan</button></div><div class="price-card"><h3>Enterprise</h3><p class="price">$299/mo</p><button>Choose Plan</button></div></div></div></section>',
            'footer' => '<footer class="site-footer"><div class="container"><p>&copy; 2024 Your Company</p></div></footer>',
        );
        
        return $templates[ $type ] ?? '<section class="content-section"><div class="container"><p>Content here</p></div></section>';
    }
    
    /**
     * Generate CSS
     */
    private function generate_css( $sections, $style ) {
        $css = ":root {\n";
        $css .= "  --primary-color: {$style['primary_color']};\n";
        $css .= "  --border-radius: {$style['border_radius']};\n";
        $css .= "}\n\n";
        
        $css .= ".container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }\n";
        $css .= "section { padding: 80px 0; }\n";
        $css .= ".hero-section { background: linear-gradient(135deg, var(--primary-color) 0%, #764ba2 100%); color: white; text-align: center; }\n";
        
        return $css;
    }
    
    /**
     * Refine template
     */
    private function refine_template( $template, $prompt ) {
        // In production, call AI API with current template + refinement prompt
        // For now, simulate refinements
        
        if ( preg_match( '/color/i', $prompt ) ) {
            $template['style']['primary_color'] = '#ff6600';
        }
        
        if ( preg_match( '/taller|bigger|larger/i', $prompt ) ) {
            // Increase section heights in config
        }
        
        return $template;
    }
    
    /**
     * Utility functions
     */
    private function extract_template_name( $prompt ) {
        // Extract first few words or detect type
        $words = explode( ' ', $prompt );
        return ucwords( implode( ' ', array_slice( $words, 0, 3 ) ) );
    }
    
    private function detect_template_type( $prompt ) {
        if ( preg_match( '/landing|page/i', $prompt ) ) return 'landing-page';
        if ( preg_match( '/portfolio/i', $prompt ) ) return 'portfolio';
        if ( preg_match( '/docs|documentation/i', $prompt ) ) return 'documentation';
        if ( preg_match( '/product/i', $prompt ) ) return 'product-page';
        return 'general';
    }
    
    /**
     * Credits management
     */
    private function get_credits_used() {
        $month_key = 'nexus_ai_credits_' . date( 'Y_m' );
        return get_option( $month_key, 0 );
    }
    
    private function get_credits_limit() {
        $tier = Nexus_License_Manager::get_tier();
        return ( 'agency' === $tier ) ? self::AGENCY_CREDITS : self::ADVANCED_CREDITS;
    }
    
    private function has_credits() {
        return $this->get_credits_used() < $this->get_credits_limit();
    }
    
    private function increment_credits() {
        $month_key = 'nexus_ai_credits_' . date( 'Y_m' );
        $current = $this->get_credits_used();
        update_option( $month_key, $current + 1 );
    }
    
    /**
     * History
     */
    private function save_to_history( $prompt, $template ) {
        $history = get_option( 'nexus_ai_history', array() );
        
        $history[] = array(
            'timestamp' => time(),
            'prompt'    => $prompt,
            'template'  => $template,
        );
        
        // Keep last 50
        $history = array_slice( $history, -50 );
        
        update_option( 'nexus_ai_history', $history );
    }
    
    private function render_history() {
        $history = get_option( 'nexus_ai_history', array() );
        $history = array_reverse( $history );
        
        if ( empty( $history ) ) {
            echo '<p class="no-history">No generation history yet.</p>';
            return;
        }
        
        foreach ( array_slice( $history, 0, 10 ) as $item ) {
            ?>
            <div class="history-item">
                <div class="history-date"><?php echo esc_html( date( 'M j, Y g:i a', $item['timestamp'] ) ); ?></div>
                <div class="history-prompt"><?php echo esc_html( wp_trim_words( $item['prompt'], 15 ) ); ?></div>
                <div class="history-actions">
                    <button class="button button-small load-history" data-template='<?php echo esc_attr( json_encode( $item['template'] ) ); ?>'>
                        Load
                    </button>
                </div>
            </div>
            <?php
        }
    }
}

// Initialize
Nexus_AI_Template_Generator::get_instance();
