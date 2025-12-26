<?php
/**
 * Form Builder
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Form Builder Class
 */
class Nexus_Form_Builder {

	/**
	 * Instance
	 */
	private static $instance;

	/**
	 * Get Instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_nexus_form', array( $this, 'save_form_meta' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_shortcode( 'nexus_form', array( $this, 'form_shortcode' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Register Post Type
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => __( 'Forms', 'nexus' ),
			'singular_name'      => __( 'Form', 'nexus' ),
			'menu_name'          => __( 'Forms', 'nexus' ),
			'add_new'            => __( 'Add New', 'nexus' ),
			'add_new_item'       => __( 'Add New Form', 'nexus' ),
			'edit_item'          => __( 'Edit Form', 'nexus' ),
			'new_item'           => __( 'New Form', 'nexus' ),
			'view_item'          => __( 'View Form', 'nexus' ),
			'search_items'       => __( 'Search Forms', 'nexus' ),
			'not_found'          => __( 'No forms found', 'nexus' ),
			'not_found_in_trash' => __( 'No forms found in trash', 'nexus' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 27,
			'menu_icon'           => 'dashicons-feedback',
			'supports'            => array( 'title' ),
		);

		register_post_type( 'nexus_form', $args );
	}

	/**
	 * Add Meta Boxes
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'nexus_form_builder',
			__( 'Form Builder', 'nexus' ),
			array( $this, 'render_builder_meta_box' ),
			'nexus_form',
			'normal',
			'high'
		);

		add_meta_box(
			'nexus_form_settings',
			__( 'Form Settings', 'nexus' ),
			array( $this, 'render_settings_meta_box' ),
			'nexus_form',
			'side',
			'default'
		);

		add_meta_box(
			'nexus_form_shortcode',
			__( 'Shortcode', 'nexus' ),
			array( $this, 'render_shortcode_meta_box' ),
			'nexus_form',
			'side',
			'default'
		);
	}

	/**
	 * Render Builder Meta Box
	 */
	public function render_builder_meta_box( $post ) {
		wp_nonce_field( 'nexus_form_builder', 'nexus_form_builder_nonce' );

		$fields = get_post_meta( $post->ID, '_nexus_form_fields', true );
		if ( ! is_array( $fields ) ) {
			$fields = array();
		}
		?>
		<div class="nexus-form-builder">
			<div class="form-builder-toolbar">
				<h3><?php esc_html_e( 'Add Field', 'nexus' ); ?></h3>
				<div class="field-types">
					<button type="button" class="button add-field" data-type="text">
						<?php esc_html_e( 'Text', 'nexus' ); ?>
					</button>
					<button type="button" class="button add-field" data-type="email">
						<?php esc_html_e( 'Email', 'nexus' ); ?>
					</button>
					<button type="button" class="button add-field" data-type="tel">
						<?php esc_html_e( 'Phone', 'nexus' ); ?>
					</button>
					<button type="button" class="button add-field" data-type="textarea">
						<?php esc_html_e( 'Textarea', 'nexus' ); ?>
					</button>
					<button type="button" class="button add-field" data-type="select">
						<?php esc_html_e( 'Dropdown', 'nexus' ); ?>
					</button>
					<button type="button" class="button add-field" data-type="radio">
						<?php esc_html_e( 'Radio', 'nexus' ); ?>
					</button>
					<button type="button" class="button add-field" data-type="checkbox">
						<?php esc_html_e( 'Checkbox', 'nexus' ); ?>
					</button>
					<button type="button" class="button add-field" data-type="file">
						<?php esc_html_e( 'File Upload', 'nexus' ); ?>
					</button>
				</div>
			</div>

			<div class="form-builder-fields">
				<div id="form-fields-list">
					<?php
					if ( ! empty( $fields ) ) {
						foreach ( $fields as $index => $field ) {
							$this->render_field_editor( $field, $index );
						}
					}
					?>
				</div>
			</div>

			<input type="hidden" name="form_fields" id="form-fields-data" value="<?php echo esc_attr( wp_json_encode( $fields ) ); ?>">
		</div>

		<script type="text/template" id="field-template">
			<?php $this->render_field_template(); ?>
		</script>
		<?php
	}

	/**
	 * Render Field Editor
	 */
	private function render_field_editor( $field, $index ) {
		$type        = isset( $field['type'] ) ? $field['type'] : 'text';
		$label       = isset( $field['label'] ) ? $field['label'] : '';
		$name        = isset( $field['name'] ) ? $field['name'] : '';
		$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
		$required    = isset( $field['required'] ) ? $field['required'] : false;
		$options     = isset( $field['options'] ) ? $field['options'] : '';
		?>
		<div class="form-field-item" data-index="<?php echo esc_attr( $index ); ?>">
			<div class="field-header">
				<span class="dashicons dashicons-menu"></span>
				<strong><?php echo esc_html( $label ? $label : ucfirst( $type ) . ' Field' ); ?></strong>
				<div class="field-actions">
					<button type="button" class="button-link toggle-field">
						<span class="dashicons dashicons-arrow-down"></span>
					</button>
					<button type="button" class="button-link delete-field">
						<span class="dashicons dashicons-trash"></span>
					</button>
				</div>
			</div>

			<div class="field-content" style="display: none;">
				<p>
					<label><?php esc_html_e( 'Field Type:', 'nexus' ); ?></label>
					<input type="text" value="<?php echo esc_attr( $type ); ?>" readonly>
				</p>
				<p>
					<label><?php esc_html_e( 'Label:', 'nexus' ); ?></label>
					<input type="text" class="field-label" value="<?php echo esc_attr( $label ); ?>" placeholder="<?php esc_attr_e( 'Field Label', 'nexus' ); ?>">
				</p>
				<p>
					<label><?php esc_html_e( 'Name:', 'nexus' ); ?></label>
					<input type="text" class="field-name" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( 'field_name', 'nexus' ); ?>">
				</p>
				<p>
					<label><?php esc_html_e( 'Placeholder:', 'nexus' ); ?></label>
					<input type="text" class="field-placeholder" value="<?php echo esc_attr( $placeholder ); ?>">
				</p>
				<?php if ( in_array( $type, array( 'select', 'radio', 'checkbox' ), true ) ) : ?>
					<p>
						<label><?php esc_html_e( 'Options (one per line):', 'nexus' ); ?></label>
						<textarea class="field-options" rows="4"><?php echo esc_textarea( $options ); ?></textarea>
					</p>
				<?php endif; ?>
				<p>
					<label>
						<input type="checkbox" class="field-required" <?php checked( $required, true ); ?>>
						<?php esc_html_e( 'Required', 'nexus' ); ?>
					</label>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Field Template
	 */
	private function render_field_template() {
		?>
		<div class="form-field-item" data-index="{{index}}">
			<div class="field-header">
				<span class="dashicons dashicons-menu"></span>
				<strong>{{type}} Field</strong>
				<div class="field-actions">
					<button type="button" class="button-link toggle-field">
						<span class="dashicons dashicons-arrow-down"></span>
					</button>
					<button type="button" class="button-link delete-field">
						<span class="dashicons dashicons-trash"></span>
					</button>
				</div>
			</div>
			<div class="field-content">
				<p>
					<label><?php esc_html_e( 'Field Type:', 'nexus' ); ?></label>
					<input type="text" value="{{type}}" readonly>
				</p>
				<p>
					<label><?php esc_html_e( 'Label:', 'nexus' ); ?></label>
					<input type="text" class="field-label" placeholder="<?php esc_attr_e( 'Field Label', 'nexus' ); ?>">
				</p>
				<p>
					<label><?php esc_html_e( 'Name:', 'nexus' ); ?></label>
					<input type="text" class="field-name" placeholder="<?php esc_attr_e( 'field_name', 'nexus' ); ?>">
				</p>
				<p>
					<label><?php esc_html_e( 'Placeholder:', 'nexus' ); ?></label>
					<input type="text" class="field-placeholder">
				</p>
				<p class="field-options-wrap" style="display: none;">
					<label><?php esc_html_e( 'Options (one per line):', 'nexus' ); ?></label>
					<textarea class="field-options" rows="4"></textarea>
				</p>
				<p>
					<label>
						<input type="checkbox" class="field-required">
						<?php esc_html_e( 'Required', 'nexus' ); ?>
					</label>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Settings Meta Box
	 */
	public function render_settings_meta_box( $post ) {
		$submit_text     = get_post_meta( $post->ID, '_nexus_form_submit_text', true );
		$success_message = get_post_meta( $post->ID, '_nexus_form_success_message', true );
		$email_to        = get_post_meta( $post->ID, '_nexus_form_email_to', true );
		$email_subject   = get_post_meta( $post->ID, '_nexus_form_email_subject', true );
		?>
		<p>
			<label><?php esc_html_e( 'Submit Button Text:', 'nexus' ); ?></label>
			<input type="text" name="form_submit_text" value="<?php echo esc_attr( $submit_text ? $submit_text : __( 'Submit', 'nexus' ) ); ?>" class="widefat">
		</p>
		<p>
			<label><?php esc_html_e( 'Success Message:', 'nexus' ); ?></label>
			<textarea name="form_success_message" class="widefat" rows="3"><?php echo esc_textarea( $success_message ? $success_message : __( 'Thank you! Your submission has been received.', 'nexus' ) ); ?></textarea>
		</p>
		<p>
			<label><?php esc_html_e( 'Send Email To:', 'nexus' ); ?></label>
			<input type="email" name="form_email_to" value="<?php echo esc_attr( $email_to ? $email_to : get_option( 'admin_email' ) ); ?>" class="widefat">
		</p>
		<p>
			<label><?php esc_html_e( 'Email Subject:', 'nexus' ); ?></label>
			<input type="text" name="form_email_subject" value="<?php echo esc_attr( $email_subject ? $email_subject : __( 'New Form Submission', 'nexus' ) ); ?>" class="widefat">
		</p>
		<?php
	}

	/**
	 * Render Shortcode Meta Box
	 */
	public function render_shortcode_meta_box( $post ) {
		if ( 'publish' === $post->post_status ) {
			?>
			<p><?php esc_html_e( 'Use this shortcode to display the form:', 'nexus' ); ?></p>
			<input type="text" value='[nexus_form id="<?php echo esc_attr( $post->ID ); ?>"]' readonly class="widefat" onclick="this.select()">
			<?php
		} else {
			?>
			<p><?php esc_html_e( 'Publish the form to get the shortcode.', 'nexus' ); ?></p>
			<?php
		}
	}

	/**
	 * Save Form Meta
	 */
	public function save_form_meta( $post_id ) {
		if ( ! isset( $_POST['nexus_form_builder_nonce'] ) || ! wp_verify_nonce( $_POST['nexus_form_builder_nonce'], 'nexus_form_builder' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save fields
		if ( isset( $_POST['form_fields'] ) ) {
			$fields = json_decode( stripslashes( $_POST['form_fields'] ), true );
			update_post_meta( $post_id, '_nexus_form_fields', $fields );
		}

		// Save settings
		if ( isset( $_POST['form_submit_text'] ) ) {
			update_post_meta( $post_id, '_nexus_form_submit_text', sanitize_text_field( $_POST['form_submit_text'] ) );
		}

		if ( isset( $_POST['form_success_message'] ) ) {
			update_post_meta( $post_id, '_nexus_form_success_message', sanitize_textarea_field( $_POST['form_success_message'] ) );
		}

		if ( isset( $_POST['form_email_to'] ) ) {
			update_post_meta( $post_id, '_nexus_form_email_to', sanitize_email( $_POST['form_email_to'] ) );
		}

		if ( isset( $_POST['form_email_subject'] ) ) {
			update_post_meta( $post_id, '_nexus_form_email_subject', sanitize_text_field( $_POST['form_email_subject'] ) );
		}
	}

	/**
	 * Form Shortcode
	 */
	public function form_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts
		);

		$form_id = intval( $atts['id'] );
		if ( ! $form_id ) {
			return '';
		}

		$form = get_post( $form_id );
		if ( ! $form || 'nexus_form' !== $form->post_type || 'publish' !== $form->post_status ) {
			return '';
		}

		$fields       = get_post_meta( $form_id, '_nexus_form_fields', true );
		$submit_text  = get_post_meta( $form_id, '_nexus_form_submit_text', true );

		if ( ! is_array( $fields ) || empty( $fields ) ) {
			return '';
		}

		ob_start();
		?>
		<form class="nexus-form" data-form-id="<?php echo esc_attr( $form_id ); ?>" method="post">
			<?php wp_nonce_field( 'nexus_form_submit_' . $form_id, 'nexus_form_nonce' ); ?>
			
			<div class="form-fields">
				<?php foreach ( $fields as $field ) : ?>
					<?php Nexus_Form_Fields::render_field( $field ); ?>
				<?php endforeach; ?>
			</div>

			<div class="form-submit">
				<button type="submit" class="button button-primary">
					<?php echo esc_html( $submit_text ? $submit_text : __( 'Submit', 'nexus' ) ); ?>
				</button>
			</div>

			<div class="form-messages" style="display: none;"></div>
		</form>
		<?php
		return ob_get_clean();
	}

	/**
	 * Enqueue Scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'nexus-forms',
			NEXUS_PRO_URI . '/assets/js/forms.js',
			array( 'jquery' ),
			NEXUS_PRO_VERSION,
			true
		);

		wp_localize_script(
			'nexus-forms',
			'nexusForms',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			)
		);

		wp_enqueue_style(
			'nexus-forms',
			NEXUS_PRO_URI . '/assets/css/forms.css',
			array(),
			NEXUS_PRO_VERSION
		);
	}

	/**
	 * Admin Enqueue Scripts
	 */
	public function admin_enqueue_scripts( $hook ) {
		global $post_type;

		if ( 'nexus_form' !== $post_type ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-sortable' );

		wp_enqueue_script(
			'nexus-form-builder',
			NEXUS_PRO_URI . '/assets/js/form-builder.js',
			array( 'jquery', 'jquery-ui-sortable' ),
			NEXUS_PRO_VERSION,
			true
		);

		wp_enqueue_style(
			'nexus-form-builder',
			NEXUS_PRO_URI . '/assets/css/form-builder.css',
			array(),
			NEXUS_PRO_VERSION
		);
	}
}
