<?php
/**
 * Nexus AI Documentation Generator (Advanced Tier)
 * 
 * Generates comprehensive documentation sites from:
 * - README files
 * - API specs (OpenAPI/Swagger)
 * - Code comments (PHPDoc, JSDoc)
 * - Markdown files
 * 
 * @package Nexus_Theme
 * @since 1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nexus_AI_Docs_Generator {
    
    /**
     * Instance
     */
    private static $instance = null;
    
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
        add_action( 'wp_ajax_nexus_ai_analyze_docs', array( $this, 'ajax_analyze_docs' ) );
        add_action( 'wp_ajax_nexus_ai_generate_docs', array( $this, 'ajax_generate_docs' ) );
        add_action( 'wp_ajax_nexus_ai_import_github', array( $this, 'ajax_import_github' ) );
    }
    
    /**
     * Add admin page
     */
    public function add_admin_page() {
        add_submenu_page(
            'nexus-theme-options',
            __( 'AI Docs Generator', 'nexus' ),
            __( 'AI Docs (Advanced)', 'nexus' ),
            'manage_options',
            'nexus-ai-docs',
            array( $this, 'render_admin_page' )
        );
    }
    
    /**
     * Enqueue assets
     */
    public function enqueue_assets( $hook ) {
        if ( 'nexus-options_page_nexus-ai-docs' !== $hook ) {
            return;
        }
        
        wp_enqueue_style( 'nexus-ai-docs', get_template_directory_uri() . '/pro/assets/css/ai-docs.css', array(), '1.5.0' );
        wp_enqueue_script( 'nexus-ai-docs', get_template_directory_uri() . '/pro/assets/js/ai-docs.js', array( 'jquery' ), '1.5.0', true );
        
        wp_localize_script( 'nexus-ai-docs', 'nexusAIDocs', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'nexus_ai_docs_nonce' ),
        ) );
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        ?>
        <div class="wrap nexus-ai-docs-wrap">
            <h1><?php esc_html_e( 'AI Documentation Generator', 'nexus' ); ?></h1>
            
            <div class="docs-generator-grid">
                
                <!-- Input Panel -->
                <div class="input-panel">
                    <h2><?php esc_html_e( 'Source Content', 'nexus' ); ?></h2>
                    
                    <div class="input-tabs">
                        <button class="input-tab active" data-tab="upload">
                            <span class="dashicons dashicons-upload"></span>
                            Upload Files
                        </button>
                        <button class="input-tab" data-tab="github">
                            <span class="dashicons dashicons-admin-site-alt3"></span>
                            GitHub Import
                        </button>
                        <button class="input-tab" data-tab="paste">
                            <span class="dashicons dashicons-editor-paste-text"></span>
                            Paste Content
                        </button>
                    </div>
                    
                    <!-- Upload Tab -->
                    <div class="input-tab-content active" data-tab="upload">
                        <div class="upload-zone" id="docs-upload-zone">
                            <span class="dashicons dashicons-cloud-upload"></span>
                            <h3><?php esc_html_e( 'Drop files here or click to browse', 'nexus' ); ?></h3>
                            <p><?php esc_html_e( 'Supported: README.md, API specs (JSON/YAML), Markdown files', 'nexus' ); ?></p>
                            <input type="file" id="docs-file-input" multiple accept=".md,.json,.yaml,.yml,.txt">
                        </div>
                        
                        <div id="uploaded-files" class="uploaded-files-list"></div>
                    </div>
                    
                    <!-- GitHub Tab -->
                    <div class="input-tab-content" data-tab="github">
                        <div class="github-import">
                            <label>
                                <?php esc_html_e( 'GitHub Repository URL:', 'nexus' ); ?>
                                <input 
                                    type="text" 
                                    id="github-repo-url" 
                                    placeholder="https://github.com/username/repo"
                                    class="widefat"
                                >
                            </label>
                            
                            <label>
                                <?php esc_html_e( 'Branch (optional):', 'nexus' ); ?>
                                <input 
                                    type="text" 
                                    id="github-branch" 
                                    value="main"
                                    class="widefat"
                                >
                            </label>
                            
                            <label>
                                <?php esc_html_e( 'Docs Path (optional):', 'nexus' ); ?>
                                <input 
                                    type="text" 
                                    id="github-docs-path" 
                                    placeholder="docs/ or leave empty for root README"
                                    class="widefat"
                                >
                            </label>
                            
                            <button id="import-from-github" class="button button-primary">
                                <span class="dashicons dashicons-download"></span>
                                <?php esc_html_e( 'Import from GitHub', 'nexus' ); ?>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Paste Tab -->
                    <div class="input-tab-content" data-tab="paste">
                        <label>
                            <?php esc_html_e( 'Paste your content:', 'nexus' ); ?>
                            <textarea 
                                id="paste-content" 
                                rows="15" 
                                class="widefat code"
                                placeholder="Paste README, API spec, or documentation content here..."
                            ></textarea>
                        </label>
                        
                        <label>
                            <?php esc_html_e( 'Content Type:', 'nexus' ); ?>
                            <select id="paste-type">
                                <option value="markdown">Markdown</option>
                                <option value="openapi">OpenAPI/Swagger</option>
                                <option value="text">Plain Text</option>
                            </select>
                        </label>
                    </div>
                    
                    <!-- Generation Options -->
                    <div class="generation-options">
                        <h3><?php esc_html_e( 'Documentation Options', 'nexus' ); ?></h3>
                        
                        <label>
                            <input type="checkbox" id="include-search" checked>
                            <?php esc_html_e( 'Include search functionality', 'nexus' ); ?>
                        </label>
                        
                        <label>
                            <input type="checkbox" id="include-navigation" checked>
                            <?php esc_html_e( 'Generate sidebar navigation', 'nexus' ); ?>
                        </label>
                        
                        <label>
                            <input type="checkbox" id="include-toc" checked>
                            <?php esc_html_e( 'Add table of contents', 'nexus' ); ?>
                        </label>
                        
                        <label>
                            <input type="checkbox" id="include-api-ref" checked>
                            <?php esc_html_e( 'Generate API reference pages', 'nexus' ); ?>
                        </label>
                        
                        <label>
                            <input type="checkbox" id="include-code-examples">
                            <?php esc_html_e( 'Add interactive code examples', 'nexus' ); ?>
                        </label>
                        
                        <label>
                            <?php esc_html_e( 'Documentation Style:', 'nexus' ); ?>
                            <select id="docs-style">
                                <option value="modern">Modern (Default)</option>
                                <option value="gitbook">GitBook Style</option>
                                <option value="readthedocs">Read the Docs</option>
                                <option value="minimal">Minimal</option>
                            </select>
                        </label>
                    </div>
                    
                    <button id="analyze-docs" class="button button-secondary button-large">
                        <span class="dashicons dashicons-analytics"></span>
                        <?php esc_html_e( 'Analyze Content', 'nexus' ); ?>
                    </button>
                </div>
                
                <!-- Preview Panel -->
                <div class="preview-panel">
                    <div class="preview-header">
                        <h2><?php esc_html_e( 'Preview', 'nexus' ); ?></h2>
                        <div class="preview-actions">
                            <button id="generate-docs-site" class="button button-primary" disabled>
                                <span class="dashicons dashicons-yes-alt"></span>
                                <?php esc_html_e( 'Generate Docs Site', 'nexus' ); ?>
                            </button>
                        </div>
                    </div>
                    
                    <div id="docs-preview" class="docs-preview-container">
                        <div class="preview-placeholder">
                            <span class="dashicons dashicons-media-document"></span>
                            <p><?php esc_html_e( 'Upload or paste content to preview your documentation site', 'nexus' ); ?></p>
                        </div>
                    </div>
                    
                    <div id="analysis-results" class="analysis-results" style="display:none;">
                        <h3><?php esc_html_e( 'Content Analysis', 'nexus' ); ?></h3>
                        <div class="analysis-grid">
                            <div class="analysis-stat">
                                <span class="stat-label"><?php esc_html_e( 'Pages Detected:', 'nexus' ); ?></span>
                                <span class="stat-value" id="pages-count">0</span>
                            </div>
                            <div class="analysis-stat">
                                <span class="stat-label"><?php esc_html_e( 'API Endpoints:', 'nexus' ); ?></span>
                                <span class="stat-value" id="endpoints-count">0</span>
                            </div>
                            <div class="analysis-stat">
                                <span class="stat-label"><?php esc_html_e( 'Code Examples:', 'nexus' ); ?></span>
                                <span class="stat-value" id="examples-count">0</span>
                            </div>
                            <div class="analysis-stat">
                                <span class="stat-label"><?php esc_html_e( 'Sections:', 'nexus' ); ?></span>
                                <span class="stat-value" id="sections-count">0</span>
                            </div>
                        </div>
                        
                        <div id="detected-structure" class="detected-structure"></div>
                    </div>
                </div>
                
            </div>
            
            <!-- Generated Sites -->
            <div class="generated-sites">
                <h2><?php esc_html_e( 'Your Documentation Sites', 'nexus' ); ?></h2>
                <div id="docs-sites-list">
                    <?php $this->render_docs_sites(); ?>
                </div>
            </div>
            
        </div>
        <?php
    }
    
    /**
     * AJAX: Analyze documentation content
     */
    public function ajax_analyze_docs() {
        check_ajax_referer( 'nexus_ai_docs_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Insufficient permissions.' ) );
        }
        
        $content = isset( $_POST['content'] ) ? wp_kses_post( $_POST['content'] ) : '';
        $type = sanitize_text_field( $_POST['type'] ?? 'markdown' );
        
        $analysis = $this->analyze_content( $content, $type );
        
        wp_send_json_success( $analysis );
    }
    
    /**
     * AJAX: Generate documentation site
     */
    public function ajax_generate_docs() {
        check_ajax_referer( 'nexus_ai_docs_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Insufficient permissions.' ) );
        }
        
        $content = isset( $_POST['content'] ) ? wp_kses_post( $_POST['content'] ) : '';
        $type = sanitize_text_field( $_POST['type'] ?? 'markdown' );
        $options = array(
            'search'       => isset( $_POST['include_search'] ),
            'navigation'   => isset( $_POST['include_navigation'] ),
            'toc'          => isset( $_POST['include_toc'] ),
            'api_ref'      => isset( $_POST['include_api_ref'] ),
            'code_examples' => isset( $_POST['include_code_examples'] ),
            'style'        => sanitize_text_field( $_POST['docs_style'] ?? 'modern' ),
        );
        
        // Use AI credit
        $ai_generator = Nexus_AI_Template_Generator::get_instance();
        if ( method_exists( $ai_generator, 'has_credits' ) && ! $ai_generator->has_credits() ) {
            wp_send_json_error( array( 'message' => 'No AI credits remaining.' ) );
        }
        
        $docs_site = $this->generate_docs_site( $content, $type, $options );
        
        wp_send_json_success( array(
            'site' => $docs_site,
            'url'  => $docs_site['url'],
        ) );
    }
    
    /**
     * AJAX: Import from GitHub
     */
    public function ajax_import_github() {
        check_ajax_referer( 'nexus_ai_docs_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Insufficient permissions.' ) );
        }
        
        $repo_url = esc_url_raw( $_POST['repo_url'] );
        $branch = sanitize_text_field( $_POST['branch'] ?? 'main' );
        $docs_path = sanitize_text_field( $_POST['docs_path'] ?? '' );
        
        $content = $this->fetch_github_docs( $repo_url, $branch, $docs_path );
        
        if ( is_wp_error( $content ) ) {
            wp_send_json_error( array( 'message' => $content->get_error_message() ) );
        }
        
        wp_send_json_success( array(
            'content' => $content,
            'files'   => $content['files'] ?? array(),
        ) );
    }
    
    /**
     * Analyze content
     */
    private function analyze_content( $content, $type ) {
        $analysis = array(
            'pages'     => 0,
            'endpoints' => 0,
            'examples'  => 0,
            'sections'  => 0,
            'structure' => array(),
        );
        
        if ( 'markdown' === $type ) {
            $analysis = $this->analyze_markdown( $content );
        } elseif ( 'openapi' === $type ) {
            $analysis = $this->analyze_openapi( $content );
        }
        
        return $analysis;
    }
    
    /**
     * Analyze markdown content
     */
    private function analyze_markdown( $content ) {
        $lines = explode( "\n", $content );
        $structure = array();
        $current_section = null;
        
        $pages = 0;
        $examples = 0;
        $sections = 0;
        
        foreach ( $lines as $line ) {
            // H1 headers = new page
            if ( preg_match( '/^#\s+(.+)/', $line, $matches ) ) {
                $pages++;
                $current_section = array(
                    'title'    => $matches[1],
                    'level'    => 1,
                    'children' => array(),
                );
                $structure[] = $current_section;
                $sections++;
            }
            
            // H2-H6 headers = subsections
            elseif ( preg_match( '/^#{2,6}\s+(.+)/', $line, $matches ) ) {
                $sections++;
            }
            
            // Code blocks = examples
            if ( preg_match( '/^```/', $line ) ) {
                $examples++;
            }
        }
        
        return array(
            'pages'     => max( 1, $pages ),
            'endpoints' => 0,
            'examples'  => intval( $examples / 2 ), // Opening + closing
            'sections'  => $sections,
            'structure' => $structure,
        );
    }
    
    /**
     * Analyze OpenAPI spec
     */
    private function analyze_openapi( $content ) {
        $spec = json_decode( $content, true );
        
        if ( ! $spec ) {
            return array(
                'pages'     => 0,
                'endpoints' => 0,
                'examples'  => 0,
                'sections'  => 0,
                'structure' => array(),
            );
        }
        
        $endpoints = 0;
        $structure = array();
        
        if ( isset( $spec['paths'] ) ) {
            foreach ( $spec['paths'] as $path => $methods ) {
                $endpoints += count( $methods );
                
                foreach ( $methods as $method => $details ) {
                    $structure[] = array(
                        'title' => strtoupper( $method ) . ' ' . $path,
                        'type'  => 'endpoint',
                    );
                }
            }
        }
        
        return array(
            'pages'     => ceil( $endpoints / 5 ), // Group 5 endpoints per page
            'endpoints' => $endpoints,
            'examples'  => $endpoints * 2, // Request + response
            'sections'  => count( $spec['tags'] ?? array() ),
            'structure' => $structure,
        );
    }
    
    /**
     * Generate documentation site
     */
    private function generate_docs_site( $content, $type, $options ) {
        $analysis = $this->analyze_content( $content, $type );
        
        // Create documentation pages
        $pages = $this->create_doc_pages( $content, $type, $analysis, $options );
        
        // Create navigation structure
        $navigation = $this->create_navigation( $analysis['structure'], $options );
        
        // Generate homepage
        $homepage_id = $this->create_homepage( $analysis, $options );
        
        // Save docs site configuration
        $site_id = $this->save_docs_site( array(
            'name'       => 'Documentation Site ' . date( 'Y-m-d H:i' ),
            'homepage'   => $homepage_id,
            'pages'      => $pages,
            'navigation' => $navigation,
            'style'      => $options['style'],
            'options'    => $options,
        ) );
        
        return array(
            'id'    => $site_id,
            'url'   => get_permalink( $homepage_id ),
            'pages' => count( $pages ),
        );
    }
    
    /**
     * Create documentation pages
     */
    private function create_doc_pages( $content, $type, $analysis, $options ) {
        $pages = array();
        
        if ( 'markdown' === $type ) {
            $pages = $this->create_markdown_pages( $content, $options );
        } elseif ( 'openapi' === $type ) {
            $pages = $this->create_api_pages( $content, $options );
        }
        
        return $pages;
    }
    
    /**
     * Create pages from markdown
     */
    private function create_markdown_pages( $content, $options ) {
        $pages = array();
        $sections = preg_split( '/^#\s+/m', $content );
        
        foreach ( $sections as $index => $section ) {
            if ( empty( trim( $section ) ) ) continue;
            
            // Extract title (first line)
            $lines = explode( "\n", $section );
            $title = array_shift( $lines );
            $body = implode( "\n", $lines );
            
            // Create page
            $page_id = wp_insert_post( array(
                'post_title'   => $title,
                'post_content' => apply_filters( 'the_content', $body ),
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ) );
            
            if ( $page_id ) {
                $pages[] = $page_id;
                
                // Add TOC if requested
                if ( $options['toc'] ) {
                    update_post_meta( $page_id, '_nexus_show_toc', true );
                }
            }
        }
        
        return $pages;
    }
    
    /**
     * Create pages from API spec
     */
    private function create_api_pages( $content, $options ) {
        $spec = json_decode( $content, true );
        $pages = array();
        
        if ( ! isset( $spec['paths'] ) ) {
            return $pages;
        }
        
        // Group by tags
        $grouped = array();
        foreach ( $spec['paths'] as $path => $methods ) {
            foreach ( $methods as $method => $details ) {
                $tags = $details['tags'] ?? array( 'General' );
                $tag = $tags[0];
                
                if ( ! isset( $grouped[ $tag ] ) ) {
                    $grouped[ $tag ] = array();
                }
                
                $grouped[ $tag ][] = array(
                    'path'    => $path,
                    'method'  => $method,
                    'details' => $details,
                );
            }
        }
        
        // Create page per tag
        foreach ( $grouped as $tag => $endpoints ) {
            $content = $this->format_api_reference( $tag, $endpoints, $spec );
            
            $page_id = wp_insert_post( array(
                'post_title'   => $tag . ' API',
                'post_content' => $content,
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ) );
            
            if ( $page_id ) {
                $pages[] = $page_id;
                update_post_meta( $page_id, '_nexus_is_api_ref', true );
            }
        }
        
        return $pages;
    }
    
    /**
     * Format API reference
     */
    private function format_api_reference( $tag, $endpoints, $spec ) {
        $html = '<div class="api-reference">';
        
        foreach ( $endpoints as $endpoint ) {
            $method = strtoupper( $endpoint['method'] );
            $path = $endpoint['path'];
            $details = $endpoint['details'];
            
            $html .= '<div class="api-endpoint">';
            $html .= '<h3><span class="method method-' . strtolower( $method ) . '">' . $method . '</span> ' . esc_html( $path ) . '</h3>';
            $html .= '<p>' . esc_html( $details['summary'] ?? '' ) . '</p>';
            
            // Parameters
            if ( isset( $details['parameters'] ) ) {
                $html .= '<h4>Parameters</h4>';
                $html .= '<table class="params-table">';
                foreach ( $details['parameters'] as $param ) {
                    $html .= '<tr>';
                    $html .= '<td><code>' . esc_html( $param['name'] ) . '</code></td>';
                    $html .= '<td>' . esc_html( $param['description'] ?? '' ) . '</td>';
                    $html .= '</tr>';
                }
                $html .= '</table>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Create navigation structure
     */
    private function create_navigation( $structure, $options ) {
        if ( ! $options['navigation'] ) {
            return array();
        }
        
        // Build hierarchical navigation from structure
        return $structure;
    }
    
    /**
     * Create homepage
     */
    private function create_homepage( $analysis, $options ) {
        $content = '<div class="docs-homepage">';
        $content .= '<h1>Documentation</h1>';
        $content .= '<p>Welcome to the documentation. Browse ' . $analysis['pages'] . ' pages of content.</p>';
        
        if ( $options['search'] ) {
            $content .= '<div class="docs-search">';
            $content .= '<input type="search" placeholder="Search documentation...">';
            $content .= '</div>';
        }
        
        $content .= '</div>';
        
        $page_id = wp_insert_post( array(
            'post_title'   => 'Documentation Home',
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ) );
        
        return $page_id;
    }
    
    /**
     * Save docs site configuration
     */
    private function save_docs_site( $config ) {
        $sites = get_option( 'nexus_docs_sites', array() );
        
        $site_id = uniqid( 'docs_' );
        $config['id'] = $site_id;
        $config['created'] = time();
        
        $sites[ $site_id ] = $config;
        update_option( 'nexus_docs_sites', $sites );
        
        return $site_id;
    }
    
    /**
     * Fetch GitHub docs
     */
    private function fetch_github_docs( $repo_url, $branch, $docs_path ) {
        // Parse GitHub URL
        if ( ! preg_match( '#github\.com/([^/]+)/([^/]+)#', $repo_url, $matches ) ) {
            return new WP_Error( 'invalid_url', 'Invalid GitHub URL' );
        }
        
        $owner = $matches[1];
        $repo = $matches[2];
        
        // Fetch README via GitHub API
        $api_url = "https://api.github.com/repos/{$owner}/{$repo}/contents/" . ( $docs_path ? $docs_path : 'README.md' );
        
        $response = wp_remote_get( $api_url, array(
            'headers' => array(
                'Accept' => 'application/vnd.github.v3+json',
            ),
        ) );
        
        if ( is_wp_error( $response ) ) {
            return $response;
        }
        
        $data = json_decode( wp_remote_retrieve_body( $response ), true );
        
        if ( isset( $data['content'] ) ) {
            return array(
                'content' => base64_decode( $data['content'] ),
                'files'   => array( $data['name'] ),
            );
        }
        
        return new WP_Error( 'fetch_failed', 'Failed to fetch GitHub content' );
    }
    
    /**
     * Render documentation sites list
     */
    private function render_docs_sites() {
        $sites = get_option( 'nexus_docs_sites', array() );
        
        if ( empty( $sites ) ) {
            echo '<p class="no-sites">No documentation sites generated yet.</p>';
            return;
        }
        
        foreach ( array_reverse( $sites ) as $site ) {
            ?>
            <div class="docs-site-card">
                <div class="site-info">
                    <h3><?php echo esc_html( $site['name'] ); ?></h3>
                    <p class="site-meta">
                        <?php echo esc_html( count( $site['pages'] ) ); ?> pages •
                        Created <?php echo esc_html( human_time_diff( $site['created'] ) ); ?> ago
                    </p>
                </div>
                <div class="site-actions">
                    <a href="<?php echo esc_url( $site['url'] ); ?>" class="button" target="_blank">
                        <?php esc_html_e( 'View Site', 'nexus' ); ?>
                    </a>
                    <button class="button delete-docs-site" data-id="<?php echo esc_attr( $site['id'] ); ?>">
                        <?php esc_html_e( 'Delete', 'nexus' ); ?>
                    </button>
                </div>
            </div>
            <?php
        }
    }
}

// Initialize
Nexus_AI_Docs_Generator::get_instance();
