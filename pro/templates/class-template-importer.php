<?php
/**
 * Template Importer - One-click template import
 *
 * @package Nexus_Pro
 * @subpackage Templates
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Template Importer Class
 *
 * Handles importing templates with media, settings, and content
 */
class Nexus_Template_Importer {

    /**
     * Instance
     *
     * @var Nexus_Template_Importer
     */
    private static $instance = null;

    /**
     * Import log
     *
     * @var array
     */
    private $import_log = array();

    /**
     * Get instance
     *
     * @return Nexus_Template_Importer
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
        add_action( 'wp_ajax_nexus_import_template_full', array( $this, 'ajax_import_template' ) );
        add_action( 'wp_ajax_nexus_import_media', array( $this, 'ajax_import_media' ) );
        add_action( 'wp_ajax_nexus_import_settings', array( $this, 'ajax_import_settings' ) );
    }

    /**
     * Import template
     *
     * @param array  $template_data Template data.
     * @param array  $options Import options.
     * @return array|WP_Error
     */
    public function import_template( $template_data, $options = array() ) {
        $this->import_log = array();

        $defaults = array(
            'import_media'    => true,
            'import_settings' => true,
            'import_content'  => true,
            'overwrite'       => false,
        );

        $options = wp_parse_args( $options, $defaults );

        // Validate template data
        if ( ! $this->validate_template( $template_data ) ) {
            return new WP_Error( 'invalid_template', __( 'Invalid template data.', 'nexus-pro' ) );
        }

        $results = array(
            'success' => true,
            'pages'   => array(),
            'media'   => array(),
            'errors'  => array(),
        );

        // Import content
        if ( $options['import_content'] && isset( $template_data['sections'] ) ) {
            $page_result = $this->import_content( $template_data['sections'], $template_data );
            
            if ( is_wp_error( $page_result ) ) {
                $results['errors'][] = $page_result->get_error_message();
                $results['success'] = false;
            } else {
                $results['pages'] = $page_result;
                $this->log( 'Imported content: ' . count( $page_result ) . ' pages' );
            }
        }

        // Import media
        if ( $options['import_media'] && isset( $template_data['media'] ) ) {
            $media_result = $this->import_media( $template_data['media'] );
            
            if ( ! is_wp_error( $media_result ) ) {
                $results['media'] = $media_result;
                $this->log( 'Imported media: ' . count( $media_result ) . ' items' );
            }
        }

        // Import settings
        if ( $options['import_settings'] && isset( $template_data['settings'] ) ) {
            $settings_result = $this->import_settings( $template_data['settings'] );
            
            if ( is_wp_error( $settings_result ) ) {
                $results['errors'][] = $settings_result->get_error_message();
            } else {
                $this->log( 'Imported settings' );
            }
        }

        $results['log'] = $this->import_log;

        return $results;
    }

    /**
     * Validate template data
     *
     * @param array $template_data Template data.
     * @return bool
     */
    private function validate_template( $template_data ) {
        // Required fields
        $required = array( 'name', 'sections' );

        foreach ( $required as $field ) {
            if ( ! isset( $template_data[ $field ] ) ) {
                $this->log( 'Missing required field: ' . $field, 'error' );
                return false;
            }
        }

        return true;
    }

    /**
     * Import content/sections
     *
     * @param array $sections Template sections.
     * @param array $template_data Full template data.
     * @return array|WP_Error
     */
    private function import_content( $sections, $template_data ) {
        $pages = array();

        // Create main page
        $page_title = isset( $template_data['name'] ) ? $template_data['name'] : 'Imported Template';
        
        $page_data = array(
            'post_title'   => $page_title,
            'post_content' => $this->build_content_from_sections( $sections ),
            'post_status'  => 'draft',
            'post_type'    => 'page',
        );

        $page_id = wp_insert_post( $page_data );

        if ( is_wp_error( $page_id ) ) {
            return $page_id;
        }

        // Save template meta
        update_post_meta( $page_id, '_nexus_template_data', json_encode( $sections ) );
        update_post_meta( $page_id, '_nexus_template_id', $template_data['id'] );
        update_post_meta( $page_id, '_nexus_imported_at', current_time( 'mysql' ) );

        // Save customizer settings if provided
        if ( isset( $template_data['customizer_settings'] ) ) {
            update_post_meta( $page_id, '_nexus_customizer_settings', $template_data['customizer_settings'] );
        }

        $pages[] = array(
            'id'    => $page_id,
            'title' => $page_title,
            'url'   => get_permalink( $page_id ),
        );

        return $pages;
    }

    /**
     * Build HTML content from sections
     *
     * @param array $sections Template sections.
     * @return string
     */
    private function build_content_from_sections( $sections ) {
        $content = '';

        foreach ( $sections as $section ) {
            $content .= $this->render_section( $section );
        }

        return $content;
    }

    /**
     * Render a section
     *
     * @param array $section Section data.
     * @return string
     */
    private function render_section( $section ) {
        $html = '';

        $section_id = isset( $section['id'] ) ? $section['id'] : '';
        $section_class = 'nexus-section';

        $html .= sprintf( '<div id="%s" class="%s">', esc_attr( $section_id ), esc_attr( $section_class ) );

        if ( isset( $section['columns'] ) && is_array( $section['columns'] ) ) {
            $html .= '<div class="nexus-row">';
            
            foreach ( $section['columns'] as $column ) {
                $column_width = isset( $column['width'] ) ? $column['width'] : '100%';
                $html .= sprintf( '<div class="nexus-column" style="width: %s;">', esc_attr( $column_width ) );

                if ( isset( $column['widgets'] ) && is_array( $column['widgets'] ) ) {
                    foreach ( $column['widgets'] as $widget ) {
                        $html .= $this->render_widget( $widget );
                    }
                }

                $html .= '</div>';
            }

            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Render a widget
     *
     * @param array $widget Widget data.
     * @return string
     */
    private function render_widget( $widget ) {
        $type = isset( $widget['type'] ) ? $widget['type'] : 'text';
        $settings = isset( $widget['settings'] ) ? $widget['settings'] : array();

        switch ( $type ) {
            case 'heading':
                $tag = isset( $settings['tag'] ) ? $settings['tag'] : 'h2';
                $text = isset( $settings['text'] ) ? $settings['text'] : '';
                return sprintf( '<%s>%s</%s>', esc_html( $tag ), esc_html( $text ), esc_html( $tag ) );

            case 'text':
                $content = isset( $settings['content'] ) ? $settings['content'] : '';
                return wpautop( $content );

            case 'button':
                $text = isset( $settings['text'] ) ? $settings['text'] : 'Button';
                $url = isset( $settings['url'] ) ? $settings['url'] : '#';
                $style = isset( $settings['style'] ) ? $settings['style'] : 'primary';
                return sprintf(
                    '<a href="%s" class="nexus-button nexus-button-%s">%s</a>',
                    esc_url( $url ),
                    esc_attr( $style ),
                    esc_html( $text )
                );

            case 'image':
                $url = isset( $settings['url'] ) ? $settings['url'] : '';
                $alt = isset( $settings['alt'] ) ? $settings['alt'] : '';
                return sprintf( '<img src="%s" alt="%s" />', esc_url( $url ), esc_attr( $alt ) );

            default:
                return '<!-- Unsupported widget type: ' . esc_html( $type ) . ' -->';
        }
    }

    /**
     * Import media files
     *
     * @param array $media_items Media items to import.
     * @return array
     */
    private function import_media( $media_items ) {
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $imported = array();

        foreach ( $media_items as $media ) {
            $url = isset( $media['url'] ) ? $media['url'] : '';
            
            if ( empty( $url ) ) {
                continue;
            }

            // Download and import
            $attachment_id = $this->import_media_file( $url, $media );

            if ( ! is_wp_error( $attachment_id ) ) {
                $imported[] = array(
                    'id'  => $attachment_id,
                    'url' => wp_get_attachment_url( $attachment_id ),
                );
            }
        }

        return $imported;
    }

    /**
     * Import single media file
     *
     * @param string $url Media URL.
     * @param array  $media_data Media metadata.
     * @return int|WP_Error
     */
    private function import_media_file( $url, $media_data = array() ) {
        // Download file
        $tmp = download_url( $url );

        if ( is_wp_error( $tmp ) ) {
            return $tmp;
        }

        $file_array = array(
            'name'     => basename( $url ),
            'tmp_name' => $tmp,
        );

        // Import to media library
        $attachment_id = media_handle_sideload( $file_array, 0 );

        if ( is_wp_error( $attachment_id ) ) {
            @unlink( $file_array['tmp_name'] );
            return $attachment_id;
        }

        // Set alt text
        if ( isset( $media_data['alt'] ) ) {
            update_post_meta( $attachment_id, '_wp_attachment_image_alt', $media_data['alt'] );
        }

        return $attachment_id;
    }

    /**
     * Import settings
     *
     * @param array $settings Template settings.
     * @return bool|WP_Error
     */
    private function import_settings( $settings ) {
        // Import theme mods
        if ( isset( $settings['theme_mods'] ) ) {
            foreach ( $settings['theme_mods'] as $key => $value ) {
                set_theme_mod( $key, $value );
            }
        }

        // Import options
        if ( isset( $settings['options'] ) ) {
            foreach ( $settings['options'] as $key => $value ) {
                update_option( 'nexus_' . $key, $value );
            }
        }

        // Import widgets
        if ( isset( $settings['widgets'] ) ) {
            $this->import_widgets( $settings['widgets'] );
        }

        return true;
    }

    /**
     * Import widgets
     *
     * @param array $widgets Widget data.
     * @return void
     */
    private function import_widgets( $widgets ) {
        foreach ( $widgets as $sidebar_id => $sidebar_widgets ) {
            $sidebars_widgets = get_option( 'sidebars_widgets', array() );
            $sidebars_widgets[ $sidebar_id ] = array();

            foreach ( $sidebar_widgets as $widget ) {
                $widget_type = $widget['type'];
                $widget_data = $widget['data'];

                // Get widget options
                $widget_options = get_option( 'widget_' . $widget_type, array() );
                
                // Find next available ID
                $widget_id = 1;
                while ( isset( $widget_options[ $widget_id ] ) ) {
                    $widget_id++;
                }

                // Save widget
                $widget_options[ $widget_id ] = $widget_data;
                update_option( 'widget_' . $widget_type, $widget_options );

                // Add to sidebar
                $sidebars_widgets[ $sidebar_id ][] = $widget_type . '-' . $widget_id;
            }

            update_option( 'sidebars_widgets', $sidebars_widgets );
        }
    }

    /**
     * Log import message
     *
     * @param string $message Log message.
     * @param string $level Log level (info, warning, error).
     * @return void
     */
    private function log( $message, $level = 'info' ) {
        $this->import_log[] = array(
            'message' => $message,
            'level'   => $level,
            'time'    => current_time( 'mysql' ),
        );
    }

    /**
     * Get import log
     *
     * @return array
     */
    public function get_log() {
        return $this->import_log;
    }

    /**
     * AJAX: Import template
     */
    public function ajax_import_template() {
        check_ajax_referer( 'nexus_templates', 'nonce' );

        if ( ! current_user_can( 'edit_theme_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
        }

        $template_id = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : '';
        
        if ( empty( $template_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Template ID required.', 'nexus-pro' ) ) );
        }

        // Get template data
        $manager = Nexus_Template_Manager::get_instance();
        $template_data = $manager->get_template( $template_id );

        if ( ! $template_data ) {
            wp_send_json_error( array( 'message' => __( 'Template not found.', 'nexus-pro' ) ) );
        }

        // Import options
        $options = array(
            'import_media'    => isset( $_POST['import_media'] ) ? (bool) $_POST['import_media'] : true,
            'import_settings' => isset( $_POST['import_settings'] ) ? (bool) $_POST['import_settings'] : true,
            'import_content'  => isset( $_POST['import_content'] ) ? (bool) $_POST['import_content'] : true,
        );

        // Perform import
        $result = $this->import_template( $template_data, $options );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array(
            'message' => __( 'Template imported successfully!', 'nexus-pro' ),
            'result'  => $result,
        ) );
    }

    /**
     * AJAX: Import media
     */
    public function ajax_import_media() {
        check_ajax_referer( 'nexus_templates', 'nonce' );

        if ( ! current_user_can( 'upload_files' ) ) {
            wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
        }

        $media_items = isset( $_POST['media'] ) ? json_decode( stripslashes( $_POST['media'] ), true ) : array();

        if ( empty( $media_items ) ) {
            wp_send_json_error( array( 'message' => __( 'No media items provided.', 'nexus-pro' ) ) );
        }

        $result = $this->import_media( $media_items );

        wp_send_json_success( array(
            'message' => __( 'Media imported successfully!', 'nexus-pro' ),
            'media'   => $result,
        ) );
    }

    /**
     * AJAX: Import settings
     */
    public function ajax_import_settings() {
        check_ajax_referer( 'nexus_templates', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'nexus-pro' ) ) );
        }

        $settings = isset( $_POST['settings'] ) ? json_decode( stripslashes( $_POST['settings'] ), true ) : array();

        if ( empty( $settings ) ) {
            wp_send_json_error( array( 'message' => __( 'No settings provided.', 'nexus-pro' ) ) );
        }

        $result = $this->import_settings( $settings );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 'message' => __( 'Settings imported successfully!', 'nexus-pro' ) ) );
    }
}

// Initialize
Nexus_Template_Importer::get_instance();
