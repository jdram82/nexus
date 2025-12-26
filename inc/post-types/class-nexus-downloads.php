<?php
/**
 * Downloads Custom Post Type
 *
 * @package Nexus
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Downloads Class
 */
class Nexus_Downloads {

	/**
	 * Instance
	 *
	 * @var Nexus_Downloads
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
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_nexus_download', array( $this, 'save_file_meta' ) );
	}

	/**
	 * Register Post Type
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => esc_html__( 'Downloads', 'nexus' ),
			'singular_name'         => esc_html__( 'Download', 'nexus' ),
			'menu_name'             => esc_html__( 'Downloads', 'nexus' ),
			'add_new'               => esc_html__( 'Add New', 'nexus' ),
			'add_new_item'          => esc_html__( 'Add New Download', 'nexus' ),
			'edit_item'             => esc_html__( 'Edit Download', 'nexus' ),
			'new_item'              => esc_html__( 'New Download', 'nexus' ),
			'view_item'             => esc_html__( 'View Download', 'nexus' ),
			'search_items'          => esc_html__( 'Search Downloads', 'nexus' ),
			'not_found'             => esc_html__( 'No downloads found', 'nexus' ),
			'not_found_in_trash'    => esc_html__( 'No downloads found in trash', 'nexus' ),
			'all_items'             => esc_html__( 'All Downloads', 'nexus' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => 'download' ),
			'capability_type'     => 'post',
			'has_archive'         => true,
			'hierarchical'        => false,
			'menu_position'       => 7,
			'menu_icon'           => 'dashicons-download',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'show_in_rest'        => true,
		);

		register_post_type( 'nexus_download', $args );
	}

	/**
	 * Register Taxonomies
	 */
	public function register_taxonomies() {
		// Download Category
		register_taxonomy(
			'download_category',
			'nexus_download',
			array(
				'labels'            => array(
					'name'          => esc_html__( 'Download Categories', 'nexus' ),
					'singular_name' => esc_html__( 'Download Category', 'nexus' ),
				),
				'hierarchical'      => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'download-category' ),
			)
		);
	}

	/**
	 * Add Meta Boxes
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'nexus_download_file',
			esc_html__( 'Download File', 'nexus' ),
			array( $this, 'render_file_meta_box' ),
			'nexus_download',
			'normal',
			'high'
		);
	}

	/**
	 * Render File Meta Box
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_file_meta_box( $post ) {
		wp_nonce_field( 'nexus_download_file', 'nexus_download_file_nonce' );
		
		$file_url     = get_post_meta( $post->ID, '_nexus_file_url', true );
		$file_version = get_post_meta( $post->ID, '_nexus_file_version', true );
		$file_size    = get_post_meta( $post->ID, '_nexus_file_size', true );
		?>
		<p>
			<label for="nexus_file_url"><?php esc_html_e( 'File URL:', 'nexus' ); ?></label><br>
			<input type="text" id="nexus_file_url" name="nexus_file_url" value="<?php echo esc_attr( $file_url ); ?>" class="widefat" />
			<button type="button" class="button" id="upload_file_button"><?php esc_html_e( 'Upload File', 'nexus' ); ?></button>
		</p>
		
		<p>
			<label for="nexus_file_version"><?php esc_html_e( 'Version:', 'nexus' ); ?></label><br>
			<input type="text" id="nexus_file_version" name="nexus_file_version" value="<?php echo esc_attr( $file_version ); ?>" class="regular-text" />
		</p>
		
		<p>
			<label for="nexus_file_size"><?php esc_html_e( 'File Size:', 'nexus' ); ?></label><br>
			<input type="text" id="nexus_file_size" name="nexus_file_size" value="<?php echo esc_attr( $file_size ); ?>" class="regular-text" placeholder="e.g., 2.5 MB" />
		</p>

		<script>
		jQuery(document).ready(function($) {
			var fileFrame;
			
			$('#upload_file_button').on('click', function(e) {
				e.preventDefault();
				
				if (fileFrame) {
					fileFrame.open();
					return;
				}
				
				fileFrame = wp.media({
					title: '<?php esc_html_e( 'Select File', 'nexus' ); ?>',
					button: {
						text: '<?php esc_html_e( 'Use this file', 'nexus' ); ?>'
					},
					multiple: false
				});
				
				fileFrame.on('select', function() {
					var attachment = fileFrame.state().get('selection').first().toJSON();
					$('#nexus_file_url').val(attachment.url);
					
					// Auto-fill file size if available
					if (attachment.filesizeHumanReadable) {
						$('#nexus_file_size').val(attachment.filesizeHumanReadable);
					}
				});
				
				fileFrame.open();
			});
		});
		</script>
		<?php
	}

	/**
	 * Save File Meta
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_file_meta( $post_id ) {
		// Check nonce
		if ( ! isset( $_POST['nexus_download_file_nonce'] ) ||
		     ! wp_verify_nonce( $_POST['nexus_download_file_nonce'], 'nexus_download_file' ) ) {
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

		// Save file URL
		if ( isset( $_POST['nexus_file_url'] ) ) {
			update_post_meta( $post_id, '_nexus_file_url', esc_url_raw( $_POST['nexus_file_url'] ) );
		}

		// Save version
		if ( isset( $_POST['nexus_file_version'] ) ) {
			update_post_meta( $post_id, '_nexus_file_version', sanitize_text_field( $_POST['nexus_file_version'] ) );
		}

		// Save file size
		if ( isset( $_POST['nexus_file_size'] ) ) {
			update_post_meta( $post_id, '_nexus_file_size', sanitize_text_field( $_POST['nexus_file_size'] ) );
		}
	}
}
