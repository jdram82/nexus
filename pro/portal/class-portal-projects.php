<?php
/**
 * Portal Projects
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Portal Projects Class
 */
class Nexus_Portal_Projects {

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
		add_action( 'add_meta_boxes_nexus_project', array( $this, 'add_client_meta_box' ) );
		add_action( 'save_post_nexus_project', array( $this, 'save_client_meta' ) );
	}

	/**
	 * Add Client Meta Box
	 */
	public function add_client_meta_box() {
		add_meta_box(
			'nexus_project_client',
			__( 'Client Access', 'nexus' ),
			array( $this, 'render_client_meta_box' ),
			'nexus_project',
			'side',
			'default'
		);
	}

	/**
	 * Render Client Meta Box
	 */
	public function render_client_meta_box( $post ) {
		wp_nonce_field( 'nexus_project_client', 'nexus_project_client_nonce' );

		$client_id = get_post_meta( $post->ID, '_client_id', true );
		$clients   = get_users( array( 'role' => 'customer' ) );
		?>
		<p>
			<label><?php esc_html_e( 'Assign to Client:', 'nexus' ); ?></label>
			<select name="client_id" class="widefat">
				<option value=""><?php esc_html_e( '-- Select Client --', 'nexus' ); ?></option>
				<?php foreach ( $clients as $client ) : ?>
					<option value="<?php echo esc_attr( $client->ID ); ?>" <?php selected( $client_id, $client->ID ); ?>>
						<?php echo esc_html( $client->display_name . ' (' . $client->user_email . ')' ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	/**
	 * Save Client Meta
	 */
	public function save_client_meta( $post_id ) {
		if ( ! isset( $_POST['nexus_project_client_nonce'] ) || ! wp_verify_nonce( $_POST['nexus_project_client_nonce'], 'nexus_project_client' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['client_id'] ) ) {
			update_post_meta( $post_id, '_client_id', intval( $_POST['client_id'] ) );
		} else {
			delete_post_meta( $post_id, '_client_id' );
		}
	}
}
