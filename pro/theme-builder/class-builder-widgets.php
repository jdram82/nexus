<?php
/**
 * Builder Widgets - Manages all builder widgets
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Builder Widgets Manager Class
 */
class Nexus_Builder_Widgets {

    /**
     * Instance
     *
     * @var Nexus_Builder_Widgets
     */
    private static $instance = null;

    /**
     * Registered widgets
     *
     * @var array
     */
    private $widgets = array();

    /**
     * Get instance
     *
     * @return Nexus_Builder_Widgets
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
        $this->register_default_widgets();
        $this->load_widget_classes();
    }

    /**
     * Register default widgets
     */
    private function register_default_widgets() {
        // Basic widgets
        $this->register_widget( array(
            'type'     => 'heading',
            'title'    => __( 'Heading', 'nexus-pro' ),
            'icon'     => 'dashicons-editor-textcolor',
            'category' => 'basic',
        ) );

        $this->register_widget( array(
            'type'     => 'text',
            'title'    => __( 'Text Editor', 'nexus-pro' ),
            'icon'     => 'dashicons-editor-alignleft',
            'category' => 'basic',
        ) );

        $this->register_widget( array(
            'type'     => 'button',
            'title'    => __( 'Button', 'nexus-pro' ),
            'icon'     => 'dashicons-button',
            'category' => 'basic',
        ) );

        $this->register_widget( array(
            'type'     => 'image',
            'title'    => __( 'Image', 'nexus-pro' ),
            'icon'     => 'dashicons-format-image',
            'category' => 'basic',
        ) );

        $this->register_widget( array(
            'type'     => 'video',
            'title'    => __( 'Video', 'nexus-pro' ),
            'icon'     => 'dashicons-video-alt3',
            'category' => 'basic',
        ) );

        $this->register_widget( array(
            'type'     => 'spacer',
            'title'    => __( 'Spacer', 'nexus-pro' ),
            'icon'     => 'dashicons-minus',
            'category' => 'basic',
        ) );

        $this->register_widget( array(
            'type'     => 'divider',
            'title'    => __( 'Divider', 'nexus-pro' ),
            'icon'     => 'dashicons-ellipsis',
            'category' => 'basic',
        ) );

        // Content widgets
        $this->register_widget( array(
            'type'     => 'icon',
            'title'    => __( 'Icon', 'nexus-pro' ),
            'icon'     => 'dashicons-star-filled',
            'category' => 'content',
        ) );

        $this->register_widget( array(
            'type'     => 'icon-box',
            'title'    => __( 'Icon Box', 'nexus-pro' ),
            'icon'     => 'dashicons-admin-page',
            'category' => 'content',
        ) );

        $this->register_widget( array(
            'type'     => 'counter',
            'title'    => __( 'Counter', 'nexus-pro' ),
            'icon'     => 'dashicons-dashboard',
            'category' => 'content',
        ) );

        $this->register_widget( array(
            'type'     => 'progress-bar',
            'title'    => __( 'Progress Bar', 'nexus-pro' ),
            'icon'     => 'dashicons-chart-bar',
            'category' => 'content',
        ) );

        $this->register_widget( array(
            'type'     => 'testimonial',
            'title'    => __( 'Testimonial', 'nexus-pro' ),
            'icon'     => 'dashicons-format-quote',
            'category' => 'content',
        ) );

        $this->register_widget( array(
            'type'     => 'accordion',
            'title'    => __( 'Accordion', 'nexus-pro' ),
            'icon'     => 'dashicons-editor-justify',
            'category' => 'content',
        ) );

        $this->register_widget( array(
            'type'     => 'tabs',
            'title'    => __( 'Tabs', 'nexus-pro' ),
            'icon'     => 'dashicons-table-col-after',
            'category' => 'content',
        ) );

        // Form widgets
        $this->register_widget( array(
            'type'     => 'form',
            'title'    => __( 'Form', 'nexus-pro' ),
            'icon'     => 'dashicons-feedback',
            'category' => 'forms',
        ) );

        $this->register_widget( array(
            'type'     => 'contact-form',
            'title'    => __( 'Contact Form', 'nexus-pro' ),
            'icon'     => 'dashicons-email',
            'category' => 'forms',
        ) );

        // WordPress widgets
        $this->register_widget( array(
            'type'     => 'posts',
            'title'    => __( 'Posts Grid', 'nexus-pro' ),
            'icon'     => 'dashicons-grid-view',
            'category' => 'wordpress',
        ) );

        $this->register_widget( array(
            'type'     => 'products',
            'title'    => __( 'Products', 'nexus-pro' ),
            'icon'     => 'dashicons-products',
            'category' => 'wordpress',
        ) );

        $this->register_widget( array(
            'type'     => 'menu',
            'title'    => __( 'Navigation Menu', 'nexus-pro' ),
            'icon'     => 'dashicons-menu',
            'category' => 'wordpress',
        ) );

        $this->register_widget( array(
            'type'     => 'search',
            'title'    => __( 'Search', 'nexus-pro' ),
            'icon'     => 'dashicons-search',
            'category' => 'wordpress',
        ) );

        // Pro widgets (Technical)
        $this->register_widget( array(
            'type'     => 'code',
            'title'    => __( 'Code Block', 'nexus-pro' ),
            'icon'     => 'dashicons-editor-code',
            'category' => 'pro',
        ) );

        $this->register_widget( array(
            'type'     => 'datasheet',
            'title'    => __( 'Datasheet', 'nexus-pro' ),
            'icon'     => 'dashicons-media-spreadsheet',
            'category' => 'pro',
        ) );

        $this->register_widget( array(
            'type'     => 'specifications',
            'title'    => __( 'Specifications Table', 'nexus-pro' ),
            'icon'     => 'dashicons-list-view',
            'category' => 'pro',
        ) );
    }

    /**
     * Load widget classes
     */
    private function load_widget_classes() {
        // Load base class first
        require_once NEXUS_PRO_PATH . 'theme-builder/widgets/class-widget-base.php';
        
        $widget_files = glob( NEXUS_PRO_PATH . 'theme-builder/widgets/*.php' );
        
        if ( $widget_files ) {
            foreach ( $widget_files as $widget_file ) {
                // Skip base class as it's already loaded
                if ( strpos( $widget_file, 'class-widget-base.php' ) !== false ) {
                    continue;
                }
                require_once $widget_file;
            }
        }
    }

    /**
     * Register a widget
     *
     * @param array $args Widget arguments.
     */
    public function register_widget( $args ) {
        $defaults = array(
            'type'     => '',
            'title'    => '',
            'icon'     => 'dashicons-admin-generic',
            'category' => 'general',
        );

        $widget = wp_parse_args( $args, $defaults );
        
        if ( empty( $widget['type'] ) ) {
            return;
        }

        $this->widgets[ $widget['type'] ] = $widget;
    }

    /**
     * Get registered widgets
     *
     * @return array
     */
    public function get_registered_widgets() {
        return apply_filters( 'nexus_builder_widgets', $this->widgets );
    }

    /**
     * Render widget based on type
     *
     * @param string $type Widget type.
     * @param array  $data Widget data.
     * @return string
     */
    public function render_widget( $type, $data = array() ) {
        $settings = isset( $data['settings'] ) ? $data['settings'] : array();
        
        ob_start();
        
        switch ( $type ) {
            case 'heading':
                $this->render_heading( $settings );
                break;
            case 'text':
                $this->render_text( $settings );
                break;
            case 'button':
                $this->render_button( $settings );
                break;
            case 'image':
                $this->render_image( $settings );
                break;
            case 'video':
                $this->render_video( $settings );
                break;
            case 'icon':
                $this->render_icon( $settings );
                break;
            case 'icon-box':
                $this->render_icon_box( $settings );
                break;
            case 'counter':
                $this->render_counter( $settings );
                break;
            case 'progress-bar':
                $this->render_progress_bar( $settings );
                break;
            case 'code':
                $this->render_code( $settings );
                break;
            case 'datasheet':
                $this->render_datasheet( $settings );
                break;
            case 'specifications':
                $this->render_specifications( $settings );
                break;
            default:
                do_action( 'nexus_render_widget_' . $type, $settings );
                break;
        }
        
        return ob_get_clean();
    }

    /**
     * Render heading widget
     *
     * @param array $settings Widget settings.
     */
    private function render_heading( $settings ) {
        $tag = isset( $settings['tag'] ) ? $settings['tag'] : 'h2';
        $text = isset( $settings['text'] ) ? $settings['text'] : __( 'Your Heading', 'nexus-pro' );
        $align = isset( $settings['align'] ) ? $settings['align'] : 'left';
        
        printf(
            '<%1$s class="nexus-heading" style="text-align: %2$s">%3$s</%1$s>',
            tag_escape( $tag ),
            esc_attr( $align ),
            esc_html( $text )
        );
    }

    /**
     * Render text widget
     *
     * @param array $settings Widget settings.
     */
    private function render_text( $settings ) {
        $content = isset( $settings['content'] ) ? $settings['content'] : '';
        echo '<div class="nexus-text-editor">' . wp_kses_post( $content ) . '</div>';
    }

    /**
     * Render button widget
     *
     * @param array $settings Widget settings.
     */
    private function render_button( $settings ) {
        $text = isset( $settings['text'] ) ? $settings['text'] : __( 'Click Here', 'nexus-pro' );
        $url = isset( $settings['url'] ) ? $settings['url'] : '#';
        $style = isset( $settings['style'] ) ? $settings['style'] : 'primary';
        $size = isset( $settings['size'] ) ? $settings['size'] : 'medium';
        
        printf(
            '<a href="%s" class="nexus-button button-%s button-%s">%s</a>',
            esc_url( $url ),
            esc_attr( $style ),
            esc_attr( $size ),
            esc_html( $text )
        );
    }

    /**
     * Render image widget
     *
     * @param array $settings Widget settings.
     */
    private function render_image( $settings ) {
        $image_id = isset( $settings['image_id'] ) ? absint( $settings['image_id'] ) : 0;
        $size = isset( $settings['size'] ) ? $settings['size'] : 'full';
        $align = isset( $settings['align'] ) ? $settings['align'] : 'center';
        
        if ( $image_id ) {
            echo '<div class="nexus-image align-' . esc_attr( $align ) . '">';
            echo wp_get_attachment_image( $image_id, $size );
            echo '</div>';
        } else {
            echo '<div class="nexus-image-placeholder">' . esc_html__( 'Select an image', 'nexus-pro' ) . '</div>';
        }
    }

    /**
     * Render video widget
     *
     * @param array $settings Widget settings.
     */
    private function render_video( $settings ) {
        $url = isset( $settings['url'] ) ? $settings['url'] : '';
        
        if ( $url ) {
            echo '<div class="nexus-video">';
            echo wp_oembed_get( esc_url( $url ) );
            echo '</div>';
        } else {
            echo '<div class="nexus-video-placeholder">' . esc_html__( 'Enter a video URL', 'nexus-pro' ) . '</div>';
        }
    }

    /**
     * Render icon widget
     *
     * @param array $settings Widget settings.
     */
    private function render_icon( $settings ) {
        $icon = isset( $settings['icon'] ) ? $settings['icon'] : 'dashicons-star-filled';
        $size = isset( $settings['size'] ) ? $settings['size'] : '40px';
        $color = isset( $settings['color'] ) ? $settings['color'] : '#333';
        
        printf(
            '<div class="nexus-icon"><span class="dashicons %s" style="font-size: %s; color: %s;"></span></div>',
            esc_attr( $icon ),
            esc_attr( $size ),
            esc_attr( $color )
        );
    }

    /**
     * Render icon box widget
     *
     * @param array $settings Widget settings.
     */
    private function render_icon_box( $settings ) {
        $icon = isset( $settings['icon'] ) ? $settings['icon'] : 'dashicons-star-filled';
        $title = isset( $settings['title'] ) ? $settings['title'] : __( 'Feature Title', 'nexus-pro' );
        $description = isset( $settings['description'] ) ? $settings['description'] : '';
        
        ?>
        <div class="nexus-icon-box">
            <div class="icon-box-icon">
                <span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
            </div>
            <h3 class="icon-box-title"><?php echo esc_html( $title ); ?></h3>
            <?php if ( $description ) : ?>
                <p class="icon-box-description"><?php echo esc_html( $description ); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render counter widget
     *
     * @param array $settings Widget settings.
     */
    private function render_counter( $settings ) {
        $number = isset( $settings['number'] ) ? $settings['number'] : '100';
        $title = isset( $settings['title'] ) ? $settings['title'] : __( 'Counter', 'nexus-pro' );
        $suffix = isset( $settings['suffix'] ) ? $settings['suffix'] : '';
        
        ?>
        <div class="nexus-counter">
            <div class="counter-number" data-count="<?php echo esc_attr( $number ); ?>">
                0<span class="counter-suffix"><?php echo esc_html( $suffix ); ?></span>
            </div>
            <div class="counter-title"><?php echo esc_html( $title ); ?></div>
        </div>
        <?php
    }

    /**
     * Render progress bar widget
     *
     * @param array $settings Widget settings.
     */
    private function render_progress_bar( $settings ) {
        $title = isset( $settings['title'] ) ? $settings['title'] : __( 'Skill', 'nexus-pro' );
        $percentage = isset( $settings['percentage'] ) ? absint( $settings['percentage'] ) : 75;
        $color = isset( $settings['color'] ) ? $settings['color'] : '#007cba';
        
        ?>
        <div class="nexus-progress-bar">
            <div class="progress-title"><?php echo esc_html( $title ); ?></div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: <?php echo esc_attr( $percentage ); ?>%; background-color: <?php echo esc_attr( $color ); ?>;">
                    <span class="progress-percentage"><?php echo esc_html( $percentage ); ?>%</span>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render code widget
     *
     * @param array $settings Widget settings.
     */
    private function render_code( $settings ) {
        $code = isset( $settings['code'] ) ? $settings['code'] : '';
        $language = isset( $settings['language'] ) ? $settings['language'] : 'php';
        
        if ( $code ) {
            ?>
            <div class="nexus-code-block">
                <pre><code class="language-<?php echo esc_attr( $language ); ?>"><?php echo esc_html( $code ); ?></code></pre>
            </div>
            <?php
        }
    }

    /**
     * Render datasheet widget
     *
     * @param array $settings Widget settings.
     */
    private function render_datasheet( $settings ) {
        $file_id = isset( $settings['file_id'] ) ? absint( $settings['file_id'] ) : 0;
        $title = isset( $settings['title'] ) ? $settings['title'] : __( 'Datasheet', 'nexus-pro' );
        
        if ( $file_id ) {
            $file_url = wp_get_attachment_url( $file_id );
            ?>
            <div class="nexus-datasheet">
                <span class="dashicons dashicons-media-document"></span>
                <a href="<?php echo esc_url( $file_url ); ?>" class="datasheet-link" target="_blank">
                    <?php echo esc_html( $title ); ?>
                </a>
            </div>
            <?php
        }
    }

    /**
     * Render specifications table widget
     *
     * @param array $settings Widget settings.
     */
    private function render_specifications( $settings ) {
        $specs = isset( $settings['specifications'] ) ? $settings['specifications'] : array();
        
        if ( ! empty( $specs ) ) {
            ?>
            <div class="nexus-specifications-table">
                <table>
                    <tbody>
                        <?php foreach ( $specs as $spec ) : ?>
                            <tr>
                                <th><?php echo esc_html( $spec['label'] ); ?></th>
                                <td><?php echo esc_html( $spec['value'] ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php
        }
    }
}
