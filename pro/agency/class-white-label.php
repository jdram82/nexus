<?php
/**
 * Nexus White-Label System (Advanced/Agency Tier)
 * 
 * Allows rebranding of Nexus theme for client delivery:
 * - Replace theme name, author, description
 * - Custom branding in WordPress admin
 * - Hide/replace Nexus branding in frontend
 * - Custom login screen
 * - Agency credits/links
 * 
 * @package Nexus_Theme
 * @since 1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nexus_White_Label {
    
    /**
     * Instance
     */
    private static $instance = null;
    
    /**
     * White label settings
     */
    private $settings = array();
    
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
        
        $this->settings = get_option( 'nexus_white_label', array() );
        
        add_action( 'admin_menu', array( $this, 'add_admin_page' ), 100 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        
        // Apply white labeling
        if ( $this->is_enabled() ) {
            $this->apply_white_label();
        }
    }
    
    /**
     * Check if white labeling is enabled
     */
    private function is_enabled() {
        return ! empty( $this->settings['enabled'] );
    }
    
    /**
     * Apply white label modifications
     */
    private function apply_white_label() {
        // Replace theme info
        add_filter( 'wp_get_theme', array( $this, 'filter_theme_info' ), 10, 2 );
        
        // Admin branding
        add_action( 'admin_head', array( $this, 'admin_branding' ) );
        add_action( 'admin_bar_menu', array( $this, 'admin_bar_branding' ), 999 );
        add_action( 'admin_footer_text', array( $this, 'admin_footer_text' ) );
        
        // Login screen
        add_action( 'login_enqueue_scripts', array( $this, 'login_branding' ) );
        add_filter( 'login_headerurl', array( $this, 'login_logo_url' ) );
        add_filter( 'login_headertext', array( $this, 'login_logo_title' ) );
        
        // Email branding
        add_filter( 'wp_mail_from_name', array( $this, 'email_from_name' ) );
        
        // Remove Nexus promotional content
        add_action( 'admin_init', array( $this, 'remove_nexus_promotions' ) );
        
        // Replace theme links
        add_filter( 'theme_row_meta', array( $this, 'theme_row_meta' ), 10, 2 );
    }
    
    /**
     * Add admin page
     */
    public function add_admin_page() {
        add_submenu_page(
            'nexus-theme-options',
            __( 'White Label', 'nexus' ),
            __( 'White Label (Advanced)', 'nexus' ),
            'manage_options',
            'nexus-white-label',
            array( $this, 'render_admin_page' )
        );
    }
    
    /**
     * Enqueue assets
     */
    public function enqueue_assets( $hook ) {
        if ( 'nexus-options_page_nexus-white-label' !== $hook ) {
            return;
        }
        
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        
        wp_enqueue_style( 'nexus-white-label', get_template_directory_uri() . '/pro/assets/css/white-label.css', array(), '1.5.0' );
        wp_enqueue_script( 'nexus-white-label', get_template_directory_uri() . '/pro/assets/js/white-label.js', array( 'jquery', 'wp-color-picker' ), '1.5.0', true );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting( 'nexus_white_label_group', 'nexus_white_label' );
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        $tier = Nexus_License_Manager::get_tier();
        $settings = $this->settings;
        
        ?>
        <div class="wrap nexus-white-label-wrap">
            <h1><?php esc_html_e( 'White Label Settings', 'nexus' ); ?></h1>
            
            <!-- Tier Info -->
            <div class="nexus-tier-info">
                <span class="tier-badge <?php echo esc_attr( $tier ); ?>">
                    <?php echo esc_html( ucfirst( $tier ) ); ?> Tier
                </span>
                <span><?php esc_html_e( 'Rebrand Nexus with your own company identity', 'nexus' ); ?></span>
            </div>
            
            <form method="post" action="options.php" class="white-label-form">
                <?php settings_fields( 'nexus_white_label_group' ); ?>
                
                <div class="white-label-grid">
                    
                    <!-- Enable/Disable -->
                    <div class="white-label-section">
                        <h2><?php esc_html_e( 'White Label Status', 'nexus' ); ?></h2>
                        
                        <label class="toggle-switch">
                            <input 
                                type="checkbox" 
                                name="nexus_white_label[enabled]" 
                                value="1"
                                <?php checked( $settings['enabled'] ?? false, 1 ); ?>
                            >
                            <span class="toggle-slider"></span>
                            <span class="toggle-label">
                                <?php esc_html_e( 'Enable White Labeling', 'nexus' ); ?>
                            </span>
                        </label>
                        
                        <p class="description">
                            <?php esc_html_e( 'When enabled, Nexus branding will be replaced with your custom branding throughout WordPress admin.', 'nexus' ); ?>
                        </p>
                    </div>
                    
                    <!-- Theme Information -->
                    <div class="white-label-section">
                        <h2><?php esc_html_e( 'Theme Information', 'nexus' ); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th><?php esc_html_e( 'Theme Name:', 'nexus' ); ?></th>
                                <td>
                                    <input 
                                        type="text" 
                                        name="nexus_white_label[theme_name]" 
                                        value="<?php echo esc_attr( $settings['theme_name'] ?? 'Nexus' ); ?>"
                                        class="regular-text"
                                    >
                                    <p class="description"><?php esc_html_e( 'The name displayed in WordPress admin', 'nexus' ); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><?php esc_html_e( 'Theme Description:', 'nexus' ); ?></th>
                                <td>
                                    <textarea 
                                        name="nexus_white_label[theme_description]" 
                                        rows="3"
                                        class="large-text"
                                    ><?php echo esc_textarea( $settings['theme_description'] ?? 'A powerful WordPress theme' ); ?></textarea>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><?php esc_html_e( 'Author Name:', 'nexus' ); ?></th>
                                <td>
                                    <input 
                                        type="text" 
                                        name="nexus_white_label[author_name]" 
                                        value="<?php echo esc_attr( $settings['author_name'] ?? 'Your Company' ); ?>"
                                        class="regular-text"
                                    >
                                </td>
                            </tr>
                            
                            <tr>
                                <th><?php esc_html_e( 'Author URL:', 'nexus' ); ?></th>
                                <td>
                                    <input 
                                        type="url" 
                                        name="nexus_white_label[author_url]" 
                                        value="<?php echo esc_url( $settings['author_url'] ?? 'https://yourcompany.com' ); ?>"
                                        class="regular-text"
                                    >
                                </td>
                            </tr>
                            
                            <tr>
                                <th><?php esc_html_e( 'Theme Version:', 'nexus' ); ?></th>
                                <td>
                                    <input 
                                        type="text" 
                                        name="nexus_white_label[theme_version]" 
                                        value="<?php echo esc_attr( $settings['theme_version'] ?? '1.0.0' ); ?>"
                                        class="small-text"
                                    >
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Admin Branding -->
                    <div class="white-label-section">
                        <h2><?php esc_html_e( 'Admin Branding', 'nexus' ); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th><?php esc_html_e( 'Admin Logo:', 'nexus' ); ?></th>
                                <td>
                                    <div class="logo-upload">
                                        <div class="logo-preview">
                                            <?php if ( ! empty( $settings['admin_logo'] ) ) : ?>
                                                <img src="<?php echo esc_url( $settings['admin_logo'] ); ?>" alt="Logo">
                                            <?php else : ?>
                                                <div class="no-logo"><?php esc_html_e( 'No logo set', 'nexus' ); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <input 
                                            type="hidden" 
                                            name="nexus_white_label[admin_logo]" 
                                            id="admin-logo-url"
                                            value="<?php echo esc_url( $settings['admin_logo'] ?? '' ); ?>"
                                        >
                                        <button type="button" class="button upload-logo" data-target="admin-logo-url">
                                            <?php esc_html_e( 'Upload Logo', 'nexus' ); ?>
                                        </button>
                                        <button type="button" class="button remove-logo" data-target="admin-logo-url">
                                            <?php esc_html_e( 'Remove', 'nexus' ); ?>
                                        </button>
                                        <p class="description"><?php esc_html_e( 'Displayed in WordPress admin header', 'nexus' ); ?></p>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><?php esc_html_e( 'Primary Color:', 'nexus' ); ?></th>
                                <td>
                                    <input 
                                        type="text" 
                                        name="nexus_white_label[primary_color]" 
                                        value="<?php echo esc_attr( $settings['primary_color'] ?? '#0066cc' ); ?>"
                                        class="color-picker"
                                    >
                                    <p class="description"><?php esc_html_e( 'Used for admin interface accents', 'nexus' ); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><?php esc_html_e( 'Admin Footer Text:', 'nexus' ); ?></th>
                                <td>
                                    <input 
                                        type="text" 
                                        name="nexus_white_label[footer_text]" 
                                        value="<?php echo esc_attr( $settings['footer_text'] ?? 'Built by Your Company' ); ?>"
                                        class="large-text"
                                    >
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Login Screen -->
                    <div class="white-label-section">
                        <h2><?php esc_html_e( 'Login Screen Branding', 'nexus' ); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th><?php esc_html_e( 'Login Logo:', 'nexus' ); ?></th>
                                <td>
                                    <div class="logo-upload">
                                        <div class="logo-preview">
                                            <?php if ( ! empty( $settings['login_logo'] ) ) : ?>
                                                <img src="<?php echo esc_url( $settings['login_logo'] ); ?>" alt="Logo">
                                            <?php else : ?>
                                                <div class="no-logo"><?php esc_html_e( 'No logo set', 'nexus' ); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <input 
                                            type="hidden" 
                                            name="nexus_white_label[login_logo]" 
                                            id="login-logo-url"
                                            value="<?php echo esc_url( $settings['login_logo'] ?? '' ); ?>"
                                        >
                                        <button type="button" class="button upload-logo" data-target="login-logo-url">
                                            <?php esc_html_e( 'Upload Logo', 'nexus' ); ?>
                                        </button>
                                        <button type="button" class="button remove-logo" data-target="login-logo-url">
                                            <?php esc_html_e( 'Remove', 'nexus' ); ?>
                                        </button>
                                        <p class="description"><?php esc_html_e( 'Recommended: 320x80px', 'nexus' ); ?></p>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><?php esc_html_e( 'Login Background:', 'nexus' ); ?></th>
                                <td>
                                    <input 
                                        type="text" 
                                        name="nexus_white_label[login_background]" 
                                        value="<?php echo esc_attr( $settings['login_background'] ?? '#f0f0f1' ); ?>"
                                        class="color-picker"
                                    >
                                </td>
                            </tr>
                            
                            <tr>
                                <th><?php esc_html_e( 'Login URL:', 'nexus' ); ?></th>
                                <td>
                                    <input 
                                        type="url" 
                                        name="nexus_white_label[login_url]" 
                                        value="<?php echo esc_url( $settings['login_url'] ?? home_url() ); ?>"
                                        class="regular-text"
                                    >
                                    <p class="description"><?php esc_html_e( 'Logo click destination', 'nexus' ); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Hide Elements -->
                    <div class="white-label-section">
                        <h2><?php esc_html_e( 'Hide Elements', 'nexus' ); ?></h2>
                        
                        <table class="form-table">
                            <tr>
                                <th><?php esc_html_e( 'WordPress Branding:', 'nexus' ); ?></th>
                                <td>
                                    <label>
                                        <input 
                                            type="checkbox" 
                                            name="nexus_white_label[hide_wp_logo]" 
                                            value="1"
                                            <?php checked( $settings['hide_wp_logo'] ?? false, 1 ); ?>
                                        >
                                        <?php esc_html_e( 'Hide WordPress logo in admin bar', 'nexus' ); ?>
                                    </label>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><?php esc_html_e( 'Theme Links:', 'nexus' ); ?></th>
                                <td>
                                    <label>
                                        <input 
                                            type="checkbox" 
                                            name="nexus_white_label[hide_theme_links]" 
                                            value="1"
                                            <?php checked( $settings['hide_theme_links'] ?? false, 1 ); ?>
                                        >
                                        <?php esc_html_e( 'Hide Nexus documentation/support links', 'nexus' ); ?>
                                    </label>
                                </td>
                            </tr>
                            
                            <tr>
                                <th><?php esc_html_e( 'Update Notices:', 'nexus' ); ?></th>
                                <td>
                                    <label>
                                        <input 
                                            type="checkbox" 
                                            name="nexus_white_label[hide_update_notices]" 
                                            value="1"
                                            <?php checked( $settings['hide_update_notices'] ?? false, 1 ); ?>
                                        >
                                        <?php esc_html_e( 'Hide theme update notices for non-admins', 'nexus' ); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Export White Label Package -->
                    <div class="white-label-section">
                        <h2><?php esc_html_e( 'Export White Label Package', 'nexus' ); ?></h2>
                        
                        <p><?php esc_html_e( 'Create a rebranded version of Nexus theme with your custom branding applied. Perfect for delivering to clients.', 'nexus' ); ?></p>
                        
                        <div class="export-options">
                            <label>
                                <input 
                                    type="checkbox" 
                                    name="nexus_white_label[include_pro]" 
                                    value="1"
                                    <?php checked( $settings['include_pro'] ?? true, 1 ); ?>
                                >
                                <?php esc_html_e( 'Include Pro features', 'nexus' ); ?>
                            </label>
                            
                            <label>
                                <input 
                                    type="checkbox" 
                                    name="nexus_white_label[remove_license_check]" 
                                    value="1"
                                    <?php checked( $settings['remove_license_check'] ?? false, 1 ); ?>
                                >
                                <?php esc_html_e( 'Remove license validation (Agency only)', 'nexus' ); ?>
                                <?php if ( 'agency' !== $tier ) : ?>
                                    <span class="badge-locked">🔒 Agency</span>
                                <?php endif; ?>
                            </label>
                        </div>
                        
                        <button type="button" id="export-white-label" class="button button-secondary button-large">
                            <span class="dashicons dashicons-download"></span>
                            <?php esc_html_e( 'Export White Label Package', 'nexus' ); ?>
                        </button>
                    </div>
                    
                </div>
                
                <?php submit_button( __( 'Save White Label Settings', 'nexus' ), 'primary large' ); ?>
            </form>
            
            <!-- Preview -->
            <div class="white-label-preview">
                <h2><?php esc_html_e( 'Preview', 'nexus' ); ?></h2>
                <p><?php esc_html_e( 'See how your branding will appear:', 'nexus' ); ?></p>
                
                <div class="preview-tabs">
                    <button class="preview-tab active" data-preview="login"><?php esc_html_e( 'Login Screen', 'nexus' ); ?></button>
                    <button class="preview-tab" data-preview="admin"><?php esc_html_e( 'Admin Panel', 'nexus' ); ?></button>
                </div>
                
                <div class="preview-container">
                    <iframe id="white-label-preview" src="about:blank"></iframe>
                </div>
            </div>
            
        </div>
        <?php
    }
    
    /**
     * Filter theme info
     */
    public function filter_theme_info( $theme, $theme_name ) {
        if ( 'nexus-theme' !== $theme_name && 'nexus' !== $theme_name ) {
            return $theme;
        }
        
        $theme->name = $this->settings['theme_name'] ?? $theme->name;
        $theme->description = $this->settings['theme_description'] ?? $theme->description;
        $theme->author = $this->settings['author_name'] ?? $theme->author;
        $theme->{'Author URI'} = $this->settings['author_url'] ?? $theme->{'Author URI'};
        $theme->version = $this->settings['theme_version'] ?? $theme->version;
        
        return $theme;
    }
    
    /**
     * Admin branding
     */
    public function admin_branding() {
        if ( empty( $this->settings['admin_logo'] ) && empty( $this->settings['primary_color'] ) ) {
            return;
        }
        
        ?>
        <style>
            <?php if ( ! empty( $this->settings['admin_logo'] ) ) : ?>
            #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
                content: '' !important;
                background-image: url(<?php echo esc_url( $this->settings['admin_logo'] ); ?>) !important;
                background-size: contain !important;
                background-repeat: no-repeat !important;
                background-position: center !important;
            }
            <?php endif; ?>
            
            <?php if ( ! empty( $this->settings['primary_color'] ) ) : ?>
            .wp-core-ui .button-primary {
                background: <?php echo esc_attr( $this->settings['primary_color'] ); ?> !important;
                border-color: <?php echo esc_attr( $this->settings['primary_color'] ); ?> !important;
            }
            #wpadminbar {
                background: <?php echo esc_attr( $this->settings['primary_color'] ); ?> !important;
            }
            <?php endif; ?>
        </style>
        <?php
    }
    
    /**
     * Admin bar branding
     */
    public function admin_bar_branding( $wp_admin_bar ) {
        if ( ! empty( $this->settings['hide_wp_logo'] ) ) {
            $wp_admin_bar->remove_node( 'wp-logo' );
        }
    }
    
    /**
     * Admin footer text
     */
    public function admin_footer_text( $text ) {
        if ( ! empty( $this->settings['footer_text'] ) ) {
            return esc_html( $this->settings['footer_text'] );
        }
        return $text;
    }
    
    /**
     * Login branding
     */
    public function login_branding() {
        ?>
        <style>
            <?php if ( ! empty( $this->settings['login_logo'] ) ) : ?>
            .login h1 a {
                background-image: url(<?php echo esc_url( $this->settings['login_logo'] ); ?>) !important;
                background-size: contain !important;
                width: 320px !important;
                height: 80px !important;
            }
            <?php endif; ?>
            
            <?php if ( ! empty( $this->settings['login_background'] ) ) : ?>
            body.login {
                background: <?php echo esc_attr( $this->settings['login_background'] ); ?> !important;
            }
            <?php endif; ?>
            
            <?php if ( ! empty( $this->settings['primary_color'] ) ) : ?>
            .wp-core-ui .button-primary {
                background: <?php echo esc_attr( $this->settings['primary_color'] ); ?> !important;
                border-color: <?php echo esc_attr( $this->settings['primary_color'] ); ?> !important;
            }
            input:focus {
                border-color: <?php echo esc_attr( $this->settings['primary_color'] ); ?> !important;
            }
            <?php endif; ?>
        </style>
        <?php
    }
    
    /**
     * Login logo URL
     */
    public function login_logo_url() {
        return ! empty( $this->settings['login_url'] ) ? $this->settings['login_url'] : home_url();
    }
    
    /**
     * Login logo title
     */
    public function login_logo_title() {
        return ! empty( $this->settings['theme_name'] ) ? $this->settings['theme_name'] : get_bloginfo( 'name' );
    }
    
    /**
     * Email from name
     */
    public function email_from_name( $name ) {
        return ! empty( $this->settings['author_name'] ) ? $this->settings['author_name'] : $name;
    }
    
    /**
     * Remove Nexus promotions
     */
    public function remove_nexus_promotions() {
        if ( ! empty( $this->settings['hide_theme_links'] ) ) {
            // Remove dashboard widgets, notices, etc.
            remove_action( 'admin_notices', 'nexus_admin_notices' );
        }
    }
    
    /**
     * Theme row meta
     */
    public function theme_row_meta( $meta, $theme_file ) {
        if ( ! empty( $this->settings['hide_theme_links'] ) ) {
            return array();
        }
        return $meta;
    }
}

// Initialize
Nexus_White_Label::get_instance();
