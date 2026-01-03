<?php
/**
 * Popup Editor Integration
 *
 * Integrates popup builder with theme builder for visual editing
 *
 * @package Nexus_Pro
 * @subpackage Popup_Builder
 * @since 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Popup Editor Class
 */
class Nexus_Popup_Editor {

	/**
	 * Instance
	 *
	 * @var Nexus_Popup_Editor
	 */
	private static $instance = null;

	/**
	 * Get instance
	 *
	 * @return Nexus_Popup_Editor
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
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_nexus_popup', array( $this, 'save_popup_meta' ) );
		add_filter( 'post_row_actions', array( $this, 'add_row_actions' ), 10, 2 );
	}

	/**
	 * Add meta boxes
	 */
	public function add_meta_boxes() {
		// Popup content editor
		add_meta_box(
			'nexus_popup_content',
			__( 'Popup Content', 'nexus-pro' ),
			array( $this, 'render_content_editor' ),
			'nexus_popup',
			'normal',
			'high'
		);

		// Display settings
		add_meta_box(
			'nexus_popup_display',
			__( 'Display Settings', 'nexus-pro' ),
			array( $this, 'render_display_settings' ),
			'nexus_popup',
			'side',
			'default'
		);

		// Design settings
		add_meta_box(
			'nexus_popup_design',
			__( 'Design', 'nexus-pro' ),
			array( $this, 'render_design_settings' ),
			'nexus_popup',
			'side',
			'default'
		);
	}

	/**
	 * Render content editor
	 *
	 * @param WP_Post $post Post object
	 */
	public function render_content_editor( $post ) {
		wp_nonce_field( 'nexus_popup_meta', 'nexus_popup_meta_nonce' );

		$content = get_post_meta( $post->ID, '_nexus_popup_content', true );

		?>
		<div class="nexus-popup-editor">
			<p class="description">
				<?php esc_html_e( 'Design your popup content using the editor below. You can use HTML, shortcodes, or the theme builder.', 'nexus-pro' ); ?>
			</p>

			<?php
			wp_editor(
				$content,
				'nexus_popup_content',
				array(
					'textarea_name' => 'nexus_popup_content',
					'textarea_rows' => 15,
					'media_buttons' => true,
					'teeny'         => false,
					'quicktags'     => true,
				)
			);
			?>
		</div>
		<?php
	}

	/**
	 * Render display settings
	 *
	 * @param WP_Post $post Post object
	 */
	public function render_display_settings( $post ) {
		$width = get_post_meta( $post->ID, '_nexus_popup_width', true ) ?: '600';
		$height = get_post_meta( $post->ID, '_nexus_popup_height', true ) ?: 'auto';
		$position = get_post_meta( $post->ID, '_nexus_popup_position', true ) ?: 'center';
		$overlay = get_post_meta( $post->ID, '_nexus_popup_overlay', true ) ?: 'yes';
		$close_button = get_post_meta( $post->ID, '_nexus_popup_close_button', true ) ?: 'yes';
		$close_on_overlay = get_post_meta( $post->ID, '_nexus_popup_close_overlay', true ) ?: 'yes';
		$close_on_esc = get_post_meta( $post->ID, '_nexus_popup_close_esc', true ) ?: 'yes';

		?>
		<div class="nexus-popup-display-settings">
			<p>
				<label for="nexus_popup_width">
					<strong><?php esc_html_e( 'Width (px)', 'nexus-pro' ); ?></strong>
				</label>
				<input type="number" id="nexus_popup_width" name="nexus_popup_width" 
					   value="<?php echo esc_attr( $width ); ?>" 
					   class="widefat" min="200" max="1920" />
			</p>

			<p>
				<label for="nexus_popup_height">
					<strong><?php esc_html_e( 'Height', 'nexus-pro' ); ?></strong>
				</label>
				<input type="text" id="nexus_popup_height" name="nexus_popup_height" 
					   value="<?php echo esc_attr( $height ); ?>" 
					   class="widefat" 
					   placeholder="auto" />
				<small class="description"><?php esc_html_e( 'Enter "auto" or a value in pixels', 'nexus-pro' ); ?></small>
			</p>

			<p>
				<label for="nexus_popup_position">
					<strong><?php esc_html_e( 'Position', 'nexus-pro' ); ?></strong>
				</label>
				<select id="nexus_popup_position" name="nexus_popup_position" class="widefat">
					<option value="center" <?php selected( $position, 'center' ); ?>><?php esc_html_e( 'Center', 'nexus-pro' ); ?></option>
					<option value="top" <?php selected( $position, 'top' ); ?>><?php esc_html_e( 'Top', 'nexus-pro' ); ?></option>
					<option value="bottom" <?php selected( $position, 'bottom' ); ?>><?php esc_html_e( 'Bottom', 'nexus-pro' ); ?></option>
					<option value="left" <?php selected( $position, 'left' ); ?>><?php esc_html_e( 'Left', 'nexus-pro' ); ?></option>
					<option value="right" <?php selected( $position, 'right' ); ?>><?php esc_html_e( 'Right', 'nexus-pro' ); ?></option>
				</select>
			</p>

			<p>
				<label>
					<input type="checkbox" name="nexus_popup_overlay" value="yes" <?php checked( $overlay, 'yes' ); ?> />
					<?php esc_html_e( 'Show Overlay', 'nexus-pro' ); ?>
				</label>
			</p>

			<p>
				<label>
					<input type="checkbox" name="nexus_popup_close_button" value="yes" <?php checked( $close_button, 'yes' ); ?> />
					<?php esc_html_e( 'Show Close Button', 'nexus-pro' ); ?>
				</label>
			</p>

			<p>
				<label>
					<input type="checkbox" name="nexus_popup_close_overlay" value="yes" <?php checked( $close_on_overlay, 'yes' ); ?> />
					<?php esc_html_e( 'Close on Overlay Click', 'nexus-pro' ); ?>
				</label>
			</p>

			<p>
				<label>
					<input type="checkbox" name="nexus_popup_close_esc" value="yes" <?php checked( $close_on_esc, 'yes' ); ?> />
					<?php esc_html_e( 'Close on ESC Key', 'nexus-pro' ); ?>
				</label>
			</p>
		</div>
		<?php
	}

	/**
	 * Render design settings
	 *
	 * @param WP_Post $post Post object
	 */
	public function render_design_settings( $post ) {
		$bg_color = get_post_meta( $post->ID, '_nexus_popup_bg_color', true ) ?: '#ffffff';
		$text_color = get_post_meta( $post->ID, '_nexus_popup_text_color', true ) ?: '#333333';
		$border_radius = get_post_meta( $post->ID, '_nexus_popup_border_radius', true ) ?: '8';
		$padding = get_post_meta( $post->ID, '_nexus_popup_padding', true ) ?: '30';
		$overlay_color = get_post_meta( $post->ID, '_nexus_popup_overlay_color', true ) ?: '#000000';
		$overlay_opacity = get_post_meta( $post->ID, '_nexus_popup_overlay_opacity', true ) ?: '0.7';
		$animation = get_post_meta( $post->ID, '_nexus_popup_animation', true ) ?: 'fade';

		?>
		<div class="nexus-popup-design-settings">
			<p>
				<label for="nexus_popup_bg_color">
					<strong><?php esc_html_e( 'Background Color', 'nexus-pro' ); ?></strong>
				</label>
				<input type="text" id="nexus_popup_bg_color" name="nexus_popup_bg_color" 
					   value="<?php echo esc_attr( $bg_color ); ?>" 
					   class="widefat color-picker" />
			</p>

			<p>
				<label for="nexus_popup_text_color">
					<strong><?php esc_html_e( 'Text Color', 'nexus-pro' ); ?></strong>
				</label>
				<input type="text" id="nexus_popup_text_color" name="nexus_popup_text_color" 
					   value="<?php echo esc_attr( $text_color ); ?>" 
					   class="widefat color-picker" />
			</p>

			<p>
				<label for="nexus_popup_border_radius">
					<strong><?php esc_html_e( 'Border Radius (px)', 'nexus-pro' ); ?></strong>
				</label>
				<input type="number" id="nexus_popup_border_radius" name="nexus_popup_border_radius" 
					   value="<?php echo esc_attr( $border_radius ); ?>" 
					   class="widefat" min="0" max="50" />
			</p>

			<p>
				<label for="nexus_popup_padding">
					<strong><?php esc_html_e( 'Padding (px)', 'nexus-pro' ); ?></strong>
				</label>
				<input type="number" id="nexus_popup_padding" name="nexus_popup_padding" 
					   value="<?php echo esc_attr( $padding ); ?>" 
					   class="widefat" min="0" max="100" />
			</p>

			<p>
				<label for="nexus_popup_overlay_color">
					<strong><?php esc_html_e( 'Overlay Color', 'nexus-pro' ); ?></strong>
				</label>
				<input type="text" id="nexus_popup_overlay_color" name="nexus_popup_overlay_color" 
					   value="<?php echo esc_attr( $overlay_color ); ?>" 
					   class="widefat color-picker" />
			</p>

			<p>
				<label for="nexus_popup_overlay_opacity">
					<strong><?php esc_html_e( 'Overlay Opacity', 'nexus-pro' ); ?></strong>
				</label>
				<input type="number" id="nexus_popup_overlay_opacity" name="nexus_popup_overlay_opacity" 
					   value="<?php echo esc_attr( $overlay_opacity ); ?>" 
					   class="widefat" min="0" max="1" step="0.1" />
			</p>

			<p>
				<label for="nexus_popup_animation">
					<strong><?php esc_html_e( 'Animation', 'nexus-pro' ); ?></strong>
				</label>
				<select id="nexus_popup_animation" name="nexus_popup_animation" class="widefat">
					<option value="fade" <?php selected( $animation, 'fade' ); ?>><?php esc_html_e( 'Fade', 'nexus-pro' ); ?></option>
					<option value="slide-up" <?php selected( $animation, 'slide-up' ); ?>><?php esc_html_e( 'Slide Up', 'nexus-pro' ); ?></option>
					<option value="slide-down" <?php selected( $animation, 'slide-down' ); ?>><?php esc_html_e( 'Slide Down', 'nexus-pro' ); ?></option>
					<option value="zoom" <?php selected( $animation, 'zoom' ); ?>><?php esc_html_e( 'Zoom', 'nexus-pro' ); ?></option>
					<option value="none" <?php selected( $animation, 'none' ); ?>><?php esc_html_e( 'None', 'nexus-pro' ); ?></option>
				</select>
			</p>
		</div>
		<?php
	}

	/**
	 * Save popup meta
	 *
	 * @param int $post_id Post ID
	 */
	public function save_popup_meta( $post_id ) {
		// Check nonce
		if ( ! isset( $_POST['nexus_popup_meta_nonce'] ) || 
			 ! wp_verify_nonce( $_POST['nexus_popup_meta_nonce'], 'nexus_popup_meta' ) ) {
			return;
		}

		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save content
		if ( isset( $_POST['nexus_popup_content'] ) ) {
			update_post_meta( $post_id, '_nexus_popup_content', wp_kses_post( $_POST['nexus_popup_content'] ) );
		}

		// Save display settings
		$display_fields = array(
			'nexus_popup_width'         => 'absint',
			'nexus_popup_height'        => 'sanitize_text_field',
			'nexus_popup_position'      => 'sanitize_text_field',
			'nexus_popup_overlay'       => 'sanitize_text_field',
			'nexus_popup_close_button'  => 'sanitize_text_field',
			'nexus_popup_close_overlay' => 'sanitize_text_field',
			'nexus_popup_close_esc'     => 'sanitize_text_field',
		);

		foreach ( $display_fields as $field => $sanitize ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_post_meta( $post_id, '_' . $field, call_user_func( $sanitize, $_POST[ $field ] ) );
			} else {
				// For checkboxes, set to 'no' if not checked
				if ( strpos( $field, 'nexus_popup_overlay' ) !== false || 
					 strpos( $field, 'nexus_popup_close' ) !== false ) {
					update_post_meta( $post_id, '_' . $field, 'no' );
				}
			}
		}

		// Save design settings
		$design_fields = array(
			'nexus_popup_bg_color'        => 'sanitize_hex_color',
			'nexus_popup_text_color'      => 'sanitize_hex_color',
			'nexus_popup_border_radius'   => 'absint',
			'nexus_popup_padding'         => 'absint',
			'nexus_popup_overlay_color'   => 'sanitize_hex_color',
			'nexus_popup_overlay_opacity' => 'floatval',
			'nexus_popup_animation'       => 'sanitize_text_field',
		);

		foreach ( $design_fields as $field => $sanitize ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_post_meta( $post_id, '_' . $field, call_user_func( $sanitize, $_POST[ $field ] ) );
			}
		}
	}

	/**
	 * Add row actions
	 *
	 * @param array   $actions Row actions
	 * @param WP_Post $post    Post object
	 * @return array
	 */
	public function add_row_actions( $actions, $post ) {
		if ( 'nexus_popup' === $post->post_type ) {
			$actions['duplicate'] = sprintf(
				'<a href="%s">%s</a>',
				wp_nonce_url(
					admin_url( 'admin-post.php?action=duplicate_popup&post=' . $post->ID ),
					'duplicate_popup_' . $post->ID
				),
				__( 'Duplicate', 'nexus-pro' )
			);
		}

		return $actions;
	}
}
