<?php
/**
 * Builder Canvas - Renders the builder interface and frontend content
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Builder Canvas Class
 */
class Nexus_Builder_Canvas {

    /**
     * Render builder interface (full-screen editor)
     *
     * @param int $post_id Post ID to edit.
     */
    public function render_builder_interface( $post_id ) {
        $post = get_post( $post_id );
        
        if ( ! $post ) {
            wp_die( esc_html__( 'Post not found', 'nexus-pro' ) );
        }

        // Get builder data
        $builder_data = get_post_meta( $post_id, '_nexus_builder_data', true );
        $data = $builder_data ? json_decode( $builder_data, true ) : array();

        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo( 'charset' ); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo esc_html( sprintf( __( 'Edit: %s', 'nexus-pro' ), $post->post_title ) ); ?></title>
            <?php
            wp_head();
            do_action( 'nexus_builder_head' );
            ?>
        </head>
        <body class="nexus-builder-mode">
            
            <!-- Builder Header -->
            <div class="nexus-builder-header">
                <div class="builder-header-left">
                    <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=nexus_builder' ) ); ?>" class="builder-back">
                        <span class="dashicons dashicons-arrow-left-alt"></span>
                        <?php esc_html_e( 'Back', 'nexus-pro' ); ?>
                    </a>
                    <h1 class="builder-title"><?php echo esc_html( $post->post_title ); ?></h1>
                </div>
                
                <div class="builder-header-center">
                    <div class="device-switcher">
                        <button class="device-btn active" data-device="desktop" title="<?php esc_attr_e( 'Desktop', 'nexus-pro' ); ?>">
                            <span class="dashicons dashicons-desktop"></span>
                        </button>
                        <button class="device-btn" data-device="tablet" title="<?php esc_attr_e( 'Tablet', 'nexus-pro' ); ?>">
                            <span class="dashicons dashicons-tablet"></span>
                        </button>
                        <button class="device-btn" data-device="mobile" title="<?php esc_attr_e( 'Mobile', 'nexus-pro' ); ?>">
                            <span class="dashicons dashicons-smartphone"></span>
                        </button>
                    </div>
                </div>
                
                <div class="builder-header-right">
                    <button class="builder-btn" id="nexus-history-undo" disabled>
                        <span class="dashicons dashicons-undo"></span>
                        <?php esc_html_e( 'Undo', 'nexus-pro' ); ?>
                    </button>
                    <button class="builder-btn" id="nexus-history-redo" disabled>
                        <span class="dashicons dashicons-redo"></span>
                        <?php esc_html_e( 'Redo', 'nexus-pro' ); ?>
                    </button>
                    <button class="builder-btn builder-btn-primary" id="nexus-save-builder">
                        <span class="dashicons dashicons-saved"></span>
                        <?php esc_html_e( 'Save', 'nexus-pro' ); ?>
                    </button>
                    <a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="builder-btn" target="_blank">
                        <span class="dashicons dashicons-visibility"></span>
                        <?php esc_html_e( 'Preview', 'nexus-pro' ); ?>
                    </a>
                </div>
            </div>

            <!-- Builder Main -->
            <div class="nexus-builder-main">
                
                <!-- Widgets Panel -->
                <div class="nexus-builder-panel panel-left">
                    <div class="panel-header">
                        <h3><?php esc_html_e( 'Widgets', 'nexus-pro' ); ?></h3>
                        <input type="search" class="widget-search" placeholder="<?php esc_attr_e( 'Search widgets...', 'nexus-pro' ); ?>">
                    </div>
                    <div class="panel-content">
                        <div class="widget-categories">
                            <?php $this->render_widget_panel(); ?>
                        </div>
                    </div>
                </div>

                <!-- Canvas -->
                <div class="nexus-builder-canvas device-desktop">
                    <div class="canvas-viewport">
                        <div class="canvas-content" id="nexus-canvas" data-post-id="<?php echo esc_attr( $post_id ); ?>">
                            <?php
                            if ( empty( $data ) ) {
                                $this->render_empty_canvas();
                            } else {
                                $this->render_canvas_content( $data );
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Settings Panel -->
                <div class="nexus-builder-panel panel-right">
                    <div class="panel-header">
                        <h3><?php esc_html_e( 'Settings', 'nexus-pro' ); ?></h3>
                    </div>
                    <div class="panel-content">
                        <div id="widget-settings">
                            <p class="no-selection"><?php esc_html_e( 'Select a widget to edit its settings', 'nexus-pro' ); ?></p>
                        </div>
                    </div>
                </div>

            </div>

            <?php
            wp_footer();
            do_action( 'nexus_builder_footer' );
            ?>
        </body>
        </html>
        <?php
    }

    /**
     * Render widget panel with categories
     */
    private function render_widget_panel() {
        $widgets = Nexus_Builder_Widgets::get_instance()->get_registered_widgets();
        
        // Group by category
        $categories = array();
        foreach ( $widgets as $widget ) {
            $category = isset( $widget['category'] ) ? $widget['category'] : 'general';
            if ( ! isset( $categories[ $category ] ) ) {
                $categories[ $category ] = array();
            }
            $categories[ $category ][] = $widget;
        }

        foreach ( $categories as $category => $category_widgets ) {
            ?>
            <div class="widget-category" data-category="<?php echo esc_attr( $category ); ?>">
                <h4 class="category-title"><?php echo esc_html( ucfirst( $category ) ); ?></h4>
                <div class="widget-list">
                    <?php foreach ( $category_widgets as $widget ) : ?>
                        <div class="widget-item" 
                             data-widget-type="<?php echo esc_attr( $widget['type'] ); ?>"
                             draggable="true">
                            <span class="widget-icon dashicons <?php echo esc_attr( $widget['icon'] ); ?>"></span>
                            <span class="widget-name"><?php echo esc_html( $widget['title'] ); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php
        }
    }

    /**
     * Render empty canvas placeholder
     */
    private function render_empty_canvas() {
        ?>
        <div class="canvas-empty-state">
            <span class="dashicons dashicons-welcome-add-page"></span>
            <h2><?php esc_html_e( 'Start Building Your Page', 'nexus-pro' ); ?></h2>
            <p><?php esc_html_e( 'Drag widgets from the left panel to begin creating your page', 'nexus-pro' ); ?></p>
        </div>
        <?php
    }

    /**
     * Render canvas content from saved data
     *
     * @param array $data Saved builder data.
     */
    private function render_canvas_content( $data ) {
        if ( empty( $data['sections'] ) ) {
            $this->render_empty_canvas();
            return;
        }

        foreach ( $data['sections'] as $section ) {
            $this->render_section( $section );
        }
    }

    /**
     * Render a section
     *
     * @param array $section Section data.
     */
    private function render_section( $section ) {
        $section_id = isset( $section['id'] ) ? $section['id'] : uniqid( 'section-' );
        $columns = isset( $section['columns'] ) ? $section['columns'] : array();
        
        ?>
        <div class="nexus-section" data-section-id="<?php echo esc_attr( $section_id ); ?>">
            <div class="section-controls">
                <button class="section-control" data-action="edit" title="<?php esc_attr_e( 'Edit Section', 'nexus-pro' ); ?>">
                    <span class="dashicons dashicons-edit"></span>
                </button>
                <button class="section-control" data-action="duplicate" title="<?php esc_attr_e( 'Duplicate Section', 'nexus-pro' ); ?>">
                    <span class="dashicons dashicons-admin-page"></span>
                </button>
                <button class="section-control" data-action="delete" title="<?php esc_attr_e( 'Delete Section', 'nexus-pro' ); ?>">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
            <div class="section-content">
                <div class="nexus-row">
                    <?php foreach ( $columns as $column ) : ?>
                        <?php $this->render_column( $column ); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render a column
     *
     * @param array $column Column data.
     */
    private function render_column( $column ) {
        $column_id = isset( $column['id'] ) ? $column['id'] : uniqid( 'column-' );
        $width = isset( $column['width'] ) ? $column['width'] : '100%';
        $widgets = isset( $column['widgets'] ) ? $column['widgets'] : array();
        
        ?>
        <div class="nexus-column" 
             data-column-id="<?php echo esc_attr( $column_id ); ?>"
             style="width: <?php echo esc_attr( $width ); ?>">
            <div class="column-content widget-drop-zone">
                <?php
                if ( empty( $widgets ) ) {
                    echo '<p class="drop-placeholder">' . esc_html__( 'Drop widgets here', 'nexus-pro' ) . '</p>';
                } else {
                    foreach ( $widgets as $widget ) {
                        $this->render_widget( $widget );
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render a widget
     *
     * @param array $widget Widget data.
     */
    private function render_widget( $widget ) {
        $widget_id = isset( $widget['id'] ) ? $widget['id'] : uniqid( 'widget-' );
        $widget_type = isset( $widget['type'] ) ? $widget['type'] : 'text';
        
        ?>
        <div class="nexus-widget" 
             data-widget-id="<?php echo esc_attr( $widget_id ); ?>"
             data-widget-type="<?php echo esc_attr( $widget_type ); ?>">
            <div class="widget-controls">
                <button class="widget-control" data-action="edit">
                    <span class="dashicons dashicons-edit"></span>
                </button>
                <button class="widget-control" data-action="duplicate">
                    <span class="dashicons dashicons-admin-page"></span>
                </button>
                <button class="widget-control" data-action="delete">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
            <div class="widget-content">
                <?php
                // Render widget based on type
                $widget_instance = Nexus_Builder_Widgets::get_instance();
                echo $widget_instance->render_widget( $widget_type, $widget );
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render frontend content (non-editing mode)
     *
     * @param array $data Builder data.
     * @return string HTML output.
     */
    public function render_content( $data ) {
        if ( empty( $data['sections'] ) ) {
            return '';
        }

        ob_start();
        
        echo '<div class="nexus-builder-content">';
        
        foreach ( $data['sections'] as $section ) {
            $this->render_frontend_section( $section );
        }
        
        echo '</div>';
        
        return ob_get_clean();
    }

    /**
     * Render section for frontend
     *
     * @param array $section Section data.
     */
    private function render_frontend_section( $section ) {
        $columns = isset( $section['columns'] ) ? $section['columns'] : array();
        $settings = isset( $section['settings'] ) ? $section['settings'] : array();
        
        $classes = array( 'nexus-section' );
        if ( isset( $settings['class'] ) ) {
            $classes[] = sanitize_html_class( $settings['class'] );
        }
        
        ?>
        <div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
            <div class="nexus-row">
                <?php foreach ( $columns as $column ) : ?>
                    <?php $this->render_frontend_column( $column ); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render column for frontend
     *
     * @param array $column Column data.
     */
    private function render_frontend_column( $column ) {
        $width = isset( $column['width'] ) ? $column['width'] : '100%';
        $widgets = isset( $column['widgets'] ) ? $column['widgets'] : array();
        
        ?>
        <div class="nexus-column" style="width: <?php echo esc_attr( $width ); ?>">
            <?php
            foreach ( $widgets as $widget ) {
                $widget_instance = Nexus_Builder_Widgets::get_instance();
                echo $widget_instance->render_widget( $widget['type'], $widget );
            }
            ?>
        </div>
        <?php
    }
}
