<?php
/**
 * Pro Activation - Database Schema Setup
 *
 * @package Nexus_Pro
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Pro Activation Class
 */
class Nexus_Pro_Activation {

    /**
     * Run activation
     */
    public static function activate() {
        self::create_tables();
        self::set_default_options();
        self::flush_rewrite_rules();
    }

    /**
     * Create database tables
     */
    private static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Templates table
        $templates_table = $wpdb->prefix . 'nexus_templates';
        $templates_sql = "CREATE TABLE IF NOT EXISTS $templates_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            template_name varchar(255) NOT NULL,
            template_data longtext NOT NULL,
            template_type varchar(50) NOT NULL DEFAULT 'page',
            is_premium tinyint(1) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY template_type (template_type),
            KEY created_at (created_at)
        ) $charset_collate;";

        // Form submissions table (already exists from Phase 2, but ensure it's there)
        $submissions_table = $wpdb->prefix . 'nexus_form_submissions';
        $submissions_sql = "CREATE TABLE IF NOT EXISTS $submissions_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            form_id bigint(20) unsigned NOT NULL,
            submission_data longtext NOT NULL,
            user_ip varchar(45) DEFAULT NULL,
            user_agent varchar(255) DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY form_id (form_id),
            KEY created_at (created_at)
        ) $charset_collate;";

        // Performance analytics table (for future Phase 3 feature)
        $analytics_table = $wpdb->prefix . 'nexus_analytics';
        $analytics_sql = "CREATE TABLE IF NOT EXISTS $analytics_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            page_id bigint(20) unsigned DEFAULT NULL,
            metric_type varchar(50) NOT NULL,
            metric_value float NOT NULL,
            recorded_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY page_id (page_id),
            KEY metric_type (metric_type),
            KEY recorded_at (recorded_at)
        ) $charset_collate;";

        // A/B tests table (for future Phase 3 feature)
        $ab_tests_table = $wpdb->prefix . 'nexus_ab_tests';
        $ab_tests_sql = "CREATE TABLE IF NOT EXISTS $ab_tests_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            test_name varchar(255) NOT NULL,
            variant_a longtext NOT NULL,
            variant_b longtext NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'draft',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            ended_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";

        // A/B test results table
        $ab_results_table = $wpdb->prefix . 'nexus_ab_results';
        $ab_results_sql = "CREATE TABLE IF NOT EXISTS $ab_results_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            test_id bigint(20) unsigned NOT NULL,
            variant varchar(10) NOT NULL,
            views int unsigned NOT NULL DEFAULT 0,
            conversions int unsigned NOT NULL DEFAULT 0,
            recorded_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY test_id (test_id),
            KEY variant (variant),
            KEY recorded_at (recorded_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        dbDelta( $templates_sql );
        dbDelta( $submissions_sql );
        dbDelta( $analytics_sql );
        dbDelta( $ab_tests_sql );
        dbDelta( $ab_results_sql );

        // Store the database version
        update_option( 'nexus_pro_db_version', '3.0.0' );
    }

    /**
     * Set default options
     */
    private static function set_default_options() {
        // Theme Builder defaults
        add_option( 'nexus_builder_enabled', '1' );
        add_option( 'nexus_builder_max_revisions', '50' );
        
        // Template library defaults
        add_option( 'nexus_templates_enabled', '1' );
        
        // Advanced controls defaults
        add_option( 'nexus_custom_controls_enabled', '1' );
        
        // Set activation timestamp
        add_option( 'nexus_pro_activated_at', current_time( 'mysql' ) );
    }

    /**
     * Flush rewrite rules
     */
    private static function flush_rewrite_rules() {
        // Register builder post type temporarily to flush rules
        register_post_type( 'nexus_builder', array(
            'public' => true,
            'rewrite' => array( 'slug' => 'builder-page' ),
        ) );
        
        flush_rewrite_rules();
    }

    /**
     * Run deactivation
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Run uninstall
     */
    public static function uninstall() {
        global $wpdb;

        // Only run if user explicitly wants to remove data
        if ( ! get_option( 'nexus_pro_remove_data_on_uninstall' ) ) {
            return;
        }

        // Drop tables
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}nexus_templates" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}nexus_analytics" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}nexus_ab_tests" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}nexus_ab_results" );

        // Delete options
        delete_option( 'nexus_pro_db_version' );
        delete_option( 'nexus_builder_enabled' );
        delete_option( 'nexus_builder_max_revisions' );
        delete_option( 'nexus_templates_enabled' );
        delete_option( 'nexus_custom_controls_enabled' );
        delete_option( 'nexus_pro_activated_at' );

        // Delete builder post meta
        $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_nexus_builder_%'" );

        flush_rewrite_rules();
    }
}

// Register activation/deactivation hooks
if ( file_exists( NEXUS_PRO_PATH . 'nexus-pro.php' ) ) {
    register_activation_hook( NEXUS_PRO_PATH . 'nexus-pro.php', array( 'Nexus_Pro_Activation', 'activate' ) );
    register_deactivation_hook( NEXUS_PRO_PATH . 'nexus-pro.php', array( 'Nexus_Pro_Activation', 'deactivate' ) );
}
