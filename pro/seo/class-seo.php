<?php
/**
 * Advanced SEO Tools - Main Class
 *
 * @package Nexus_Pro
 * @subpackage SEO
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * SEO Tools Class
 */
class Nexus_SEO {

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
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_head', array( $this, 'output_schema_markup' ), 1 );
        add_action( 'wp_head', array( $this, 'output_meta_tags' ), 2 );
        add_action( 'save_post', array( $this, 'analyze_content' ), 10, 2 );
        add_action( 'init', array( $this, 'register_sitemap' ) );
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'nexus-pro',
            __( 'SEO Tools', 'nexus-pro' ),
            __( 'SEO Tools', 'nexus-pro' ),
            'manage_options',
            'nexus-seo',
            array( $this, 'render_admin_page' )
        );

        // Add meta box to posts
        add_meta_box(
            'nexus_seo_meta',
            __( 'SEO Settings', 'nexus-pro' ),
            array( $this, 'render_meta_box' ),
            array( 'post', 'page', 'product' ),
            'normal',
            'high'
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets( $hook ) {
        if ( $hook !== 'nexus-pro_page_nexus-seo' && $hook !== 'post.php' && $hook !== 'post-new.php' ) {
            return;
        }

        wp_enqueue_style( 'nexus-seo', NEXUS_PRO_URL . 'assets/css/seo.css', array(), '3.0.0' );
        wp_enqueue_script( 'nexus-seo', NEXUS_PRO_URL . 'assets/js/seo.js', array( 'jquery' ), '3.0.0', true );

        wp_localize_script( 'nexus-seo', 'nexusSEO', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'nexus_seo' ),
        ) );
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        $site_score = $this->calculate_site_seo_score();
        $issues = $this->get_seo_issues();
        ?>
        <div class="wrap nexus-seo-wrap">
            <h1><?php esc_html_e( 'SEO Tools', 'nexus-pro' ); ?></h1>

            <!-- SEO Score -->
            <div class="nexus-seo-score-card">
                <h2><?php esc_html_e( 'Site SEO Score', 'nexus-pro' ); ?></h2>
                <div class="nexus-seo-score-display">
                    <div class="nexus-score-circle-seo" style="background: conic-gradient(<?php echo esc_attr( $this->get_score_color( $site_score ) ); ?> <?php echo esc_attr( $site_score * 3.6 ); ?>deg, #e0e0e0 0deg);">
                        <div class="nexus-score-inner">
                            <span class="nexus-score-number"><?php echo esc_html( $site_score ); ?></span>
                            <span class="nexus-score-max">/100</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Issues -->
            <div class="nexus-seo-issues">
                <h2><?php esc_html_e( 'SEO Issues', 'nexus-pro' ); ?></h2>
                <?php if ( ! empty( $issues ) ) : ?>
                    <ul class="nexus-issues-list">
                        <?php foreach ( $issues as $issue ) : ?>
                            <li class="nexus-issue-item severity-<?php echo esc_attr( $issue['severity'] ); ?>">
                                <span class="dashicons dashicons-<?php echo esc_attr( $issue['icon'] ); ?>"></span>
                                <div>
                                    <h4><?php echo esc_html( $issue['title'] ); ?></h4>
                                    <p><?php echo esc_html( $issue['description'] ); ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p><?php esc_html_e( 'No SEO issues found! Your site is well-optimized.', 'nexus-pro' ); ?></p>
                <?php endif; ?>
            </div>

            <!-- Sitemap -->
            <div class="nexus-seo-sitemap">
                <h2><?php esc_html_e( 'XML Sitemap', 'nexus-pro' ); ?></h2>
                <p><?php esc_html_e( 'Your sitemap is available at:', 'nexus-pro' ); ?></p>
                <code><?php echo esc_html( home_url( '/sitemap.xml' ) ); ?></code>
                <button class="button button-primary" id="nexus-regenerate-sitemap">
                    <?php esc_html_e( 'Regenerate Sitemap', 'nexus-pro' ); ?>
                </button>
            </div>
        </div>
        <?php
    }

    /**
     * Render meta box
     */
    public function render_meta_box( $post ) {
        wp_nonce_field( 'nexus_seo_meta', 'nexus_seo_nonce' );

        $title = get_post_meta( $post->ID, '_nexus_seo_title', true );
        $description = get_post_meta( $post->ID, '_nexus_seo_description', true );
        $keywords = get_post_meta( $post->ID, '_nexus_seo_keywords', true );
        $canonical = get_post_meta( $post->ID, '_nexus_seo_canonical', true );
        $noindex = get_post_meta( $post->ID, '_nexus_seo_noindex', true );

        $score = $this->calculate_content_seo_score( $post->ID );
        ?>
        <div class="nexus-seo-meta-box">
            <div class="nexus-seo-score-small">
                <strong><?php esc_html_e( 'SEO Score:', 'nexus-pro' ); ?></strong>
                <span class="nexus-score-badge score-<?php echo esc_attr( $score >= 70 ? 'good' : ( $score >= 40 ? 'ok' : 'bad' ) ); ?>">
                    <?php echo esc_html( $score ); ?>/100
                </span>
            </div>

            <p>
                <label for="nexus_seo_title"><strong><?php esc_html_e( 'SEO Title', 'nexus-pro' ); ?></strong></label>
                <input type="text" id="nexus_seo_title" name="nexus_seo_title" value="<?php echo esc_attr( $title ); ?>" class="widefat" />
                <span class="description"><?php esc_html_e( 'Recommended: 50-60 characters', 'nexus-pro' ); ?></span>
            </p>

            <p>
                <label for="nexus_seo_description"><strong><?php esc_html_e( 'Meta Description', 'nexus-pro' ); ?></strong></label>
                <textarea id="nexus_seo_description" name="nexus_seo_description" rows="3" class="widefat"><?php echo esc_textarea( $description ); ?></textarea>
                <span class="description"><?php esc_html_e( 'Recommended: 150-160 characters', 'nexus-pro' ); ?></span>
            </p>

            <p>
                <label for="nexus_seo_keywords"><strong><?php esc_html_e( 'Focus Keywords', 'nexus-pro' ); ?></strong></label>
                <input type="text" id="nexus_seo_keywords" name="nexus_seo_keywords" value="<?php echo esc_attr( $keywords ); ?>" class="widefat" />
                <span class="description"><?php esc_html_e( 'Comma-separated keywords', 'nexus-pro' ); ?></span>
            </p>

            <p>
                <label for="nexus_seo_canonical"><strong><?php esc_html_e( 'Canonical URL', 'nexus-pro' ); ?></strong></label>
                <input type="url" id="nexus_seo_canonical" name="nexus_seo_canonical" value="<?php echo esc_url( $canonical ); ?>" class="widefat" />
            </p>

            <p>
                <label>
                    <input type="checkbox" name="nexus_seo_noindex" value="1" <?php checked( $noindex, 1 ); ?> />
                    <?php esc_html_e( 'No Index (prevent search engines from indexing)', 'nexus-pro' ); ?>
                </label>
            </p>
        </div>
        <?php
    }

    /**
     * Output schema markup
     */
    public function output_schema_markup() {
        if ( is_front_page() ) {
            $schema = array(
                '@context' => 'https://schema.org',
                '@type'    => 'Organization',
                'name'     => get_bloginfo( 'name' ),
                'url'      => home_url(),
                'logo'     => get_theme_mod( 'custom_logo' ) ? wp_get_attachment_url( get_theme_mod( 'custom_logo' ) ) : '',
            );
            echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
        }

        if ( is_singular() ) {
            $post = get_post();
            $schema = array(
                '@context' => 'https://schema.org',
                '@type'    => 'Article',
                'headline' => get_the_title(),
                'author'   => array(
                    '@type' => 'Person',
                    'name'  => get_the_author(),
                ),
                'datePublished' => get_the_date( 'c' ),
                'dateModified'  => get_the_modified_date( 'c' ),
            );
            echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
        }
    }

    /**
     * Output meta tags
     */
    public function output_meta_tags() {
        if ( is_singular() ) {
            $post_id = get_queried_object_id();
            $title = get_post_meta( $post_id, '_nexus_seo_title', true );
            $description = get_post_meta( $post_id, '_nexus_seo_description', true );
            $canonical = get_post_meta( $post_id, '_nexus_seo_canonical', true );
            $noindex = get_post_meta( $post_id, '_nexus_seo_noindex', true );

            if ( $title ) {
                echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
                echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
            }

            if ( $description ) {
                echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
                echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
                echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
            }

            if ( $canonical ) {
                echo '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n";
            }

            if ( $noindex ) {
                echo '<meta name="robots" content="noindex,nofollow" />' . "\n";
            }
        }
    }

    /**
     * Calculate site SEO score
     */
    private function calculate_site_seo_score() {
        $score = 100;

        // Check if sitemap exists
        if ( ! file_exists( ABSPATH . 'sitemap.xml' ) ) $score -= 10;

        // Check if SSL is enabled
        if ( ! is_ssl() ) $score -= 15;

        // Check permalink structure
        if ( get_option( 'permalink_structure' ) === '' ) $score -= 20;

        return max( 0, $score );
    }

    /**
     * Calculate content SEO score
     */
    private function calculate_content_seo_score( $post_id ) {
        $score = 0;
        $post = get_post( $post_id );

        // Title length
        $title_length = strlen( get_post_meta( $post_id, '_nexus_seo_title', true ) ?: $post->post_title );
        if ( $title_length >= 50 && $title_length <= 60 ) $score += 20;

        // Description length
        $desc_length = strlen( get_post_meta( $post_id, '_nexus_seo_description', true ) );
        if ( $desc_length >= 150 && $desc_length <= 160 ) $score += 20;

        // Keywords present
        if ( get_post_meta( $post_id, '_nexus_seo_keywords', true ) ) $score += 15;

        // Content length
        $content_length = str_word_count( strip_tags( $post->post_content ) );
        if ( $content_length > 300 ) $score += 15;
        if ( $content_length > 1000 ) $score += 10;

        // Images with alt text
        preg_match_all( '/<img[^>]+>/i', $post->post_content, $matches );
        $total_images = count( $matches[0] );
        $images_with_alt = 0;
        foreach ( $matches[0] as $img ) {
            if ( strpos( $img, 'alt=' ) !== false ) $images_with_alt++;
        }
        if ( $total_images > 0 && $images_with_alt === $total_images ) $score += 20;

        return min( 100, $score );
    }

    /**
     * Get SEO issues
     */
    private function get_seo_issues() {
        $issues = array();

        if ( ! is_ssl() ) {
            $issues[] = array(
                'severity'    => 'high',
                'icon'        => 'lock',
                'title'       => __( 'SSL Not Enabled', 'nexus-pro' ),
                'description' => __( 'Your site is not using HTTPS. This affects SEO and user trust.', 'nexus-pro' ),
            );
        }

        if ( get_option( 'permalink_structure' ) === '' ) {
            $issues[] = array(
                'severity'    => 'high',
                'icon'        => 'admin-links',
                'title'       => __( 'Default Permalinks', 'nexus-pro' ),
                'description' => __( 'Using default permalinks. Switch to a SEO-friendly structure.', 'nexus-pro' ),
            );
        }

        return $issues;
    }

    /**
     * Get score color
     */
    private function get_score_color( $score ) {
        if ( $score >= 70 ) return '#4caf50';
        if ( $score >= 40 ) return '#ff9800';
        return '#f44336';
    }

    /**
     * Analyze content
     */
    public function analyze_content( $post_id, $post ) {
        if ( ! isset( $_POST['nexus_seo_nonce'] ) || ! wp_verify_nonce( $_POST['nexus_seo_nonce'], 'nexus_seo_meta' ) ) {
            return;
        }

        if ( isset( $_POST['nexus_seo_title'] ) ) {
            update_post_meta( $post_id, '_nexus_seo_title', sanitize_text_field( $_POST['nexus_seo_title'] ) );
        }

        if ( isset( $_POST['nexus_seo_description'] ) ) {
            update_post_meta( $post_id, '_nexus_seo_description', sanitize_textarea_field( $_POST['nexus_seo_description'] ) );
        }

        if ( isset( $_POST['nexus_seo_keywords'] ) ) {
            update_post_meta( $post_id, '_nexus_seo_keywords', sanitize_text_field( $_POST['nexus_seo_keywords'] ) );
        }

        if ( isset( $_POST['nexus_seo_canonical'] ) ) {
            update_post_meta( $post_id, '_nexus_seo_canonical', esc_url_raw( $_POST['nexus_seo_canonical'] ) );
        }

        update_post_meta( $post_id, '_nexus_seo_noindex', isset( $_POST['nexus_seo_noindex'] ) ? 1 : 0 );
    }

    /**
     * Register sitemap
     */
    public function register_sitemap() {
        add_rewrite_rule( '^sitemap\.xml$', 'index.php?nexus_sitemap=1', 'top' );
        add_filter( 'query_vars', function( $vars ) {
            $vars[] = 'nexus_sitemap';
            return $vars;
        } );

        add_action( 'template_redirect', function() {
            if ( get_query_var( 'nexus_sitemap' ) ) {
                $this->generate_sitemap();
                exit;
            }
        } );
    }

    /**
     * Generate sitemap
     */
    private function generate_sitemap() {
        header( 'Content-Type: application/xml; charset=utf-8' );
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Homepage
        echo '<url><loc>' . esc_url( home_url( '/' ) ) . '</loc><priority>1.0</priority></url>' . "\n";

        // Posts and Pages
        $posts = get_posts( array( 'post_type' => array( 'post', 'page' ), 'posts_per_page' => -1, 'post_status' => 'publish' ) );
        foreach ( $posts as $post ) {
            echo '<url><loc>' . esc_url( get_permalink( $post->ID ) ) . '</loc><lastmod>' . esc_html( get_the_modified_date( 'c', $post->ID ) ) . '</lastmod></url>' . "\n";
        }

        echo '</urlset>';
    }
}

// Initialize
Nexus_SEO::get_instance();
