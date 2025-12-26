<?php
/**
 * Documentation Manager
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Docs Manager Class
 */
class Nexus_Docs_Manager {

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
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_nexus_doc', array( $this, 'save_meta' ) );
		add_filter( 'the_content', array( $this, 'enhance_content' ) );
	}

	/**
	 * Register Post Type
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => __( 'Documentation', 'nexus' ),
			'singular_name'      => __( 'Documentation', 'nexus' ),
			'menu_name'          => __( 'Documentation', 'nexus' ),
			'add_new'            => __( 'Add New', 'nexus' ),
			'add_new_item'       => __( 'Add New Documentation', 'nexus' ),
			'edit_item'          => __( 'Edit Documentation', 'nexus' ),
			'new_item'           => __( 'New Documentation', 'nexus' ),
			'view_item'          => __( 'View Documentation', 'nexus' ),
			'search_items'       => __( 'Search Documentation', 'nexus' ),
			'not_found'          => __( 'No documentation found', 'nexus' ),
			'not_found_in_trash' => __( 'No documentation found in trash', 'nexus' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => 'docs' ),
			'capability_type'     => 'post',
			'has_archive'         => true,
			'hierarchical'        => true,
			'menu_position'       => 26,
			'menu_icon'           => 'dashicons-book',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions', 'page-attributes', 'custom-fields' ),
			'show_in_rest'        => true,
		);

		register_post_type( 'nexus_doc', $args );
	}

	/**
	 * Register Taxonomies
	 */
	public function register_taxonomies() {
		// Documentation Category
		register_taxonomy(
			'doc_category',
			'nexus_doc',
			array(
				'label'        => __( 'Categories', 'nexus' ),
				'hierarchical' => true,
				'rewrite'      => array( 'slug' => 'docs-category' ),
				'show_in_rest' => true,
			)
		);

		// Documentation Version
		register_taxonomy(
			'doc_version',
			'nexus_doc',
			array(
				'label'        => __( 'Versions', 'nexus' ),
				'hierarchical' => false,
				'rewrite'      => array( 'slug' => 'docs-version' ),
				'show_in_rest' => true,
			)
		);
	}

	/**
	 * Add Meta Boxes
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'nexus_doc_settings',
			__( 'Documentation Settings', 'nexus' ),
			array( $this, 'render_settings_meta_box' ),
			'nexus_doc',
			'side',
			'high'
		);

		add_meta_box(
			'nexus_doc_toc',
			__( 'Table of Contents', 'nexus' ),
			array( $this, 'render_toc_meta_box' ),
			'nexus_doc',
			'side',
			'default'
		);
	}

	/**
	 * Render Settings Meta Box
	 */
	public function render_settings_meta_box( $post ) {
		wp_nonce_field( 'nexus_doc_settings', 'nexus_doc_settings_nonce' );

		$difficulty   = get_post_meta( $post->ID, '_nexus_doc_difficulty', true );
		$read_time    = get_post_meta( $post->ID, '_nexus_doc_read_time', true );
		$external_url = get_post_meta( $post->ID, '_nexus_doc_external_url', true );
		?>
		<p>
			<label for="nexus_doc_difficulty"><?php esc_html_e( 'Difficulty:', 'nexus' ); ?></label>
			<select name="nexus_doc_difficulty" id="nexus_doc_difficulty" class="widefat">
				<option value="beginner" <?php selected( $difficulty, 'beginner' ); ?>><?php esc_html_e( 'Beginner', 'nexus' ); ?></option>
				<option value="intermediate" <?php selected( $difficulty, 'intermediate' ); ?>><?php esc_html_e( 'Intermediate', 'nexus' ); ?></option>
				<option value="advanced" <?php selected( $difficulty, 'advanced' ); ?>><?php esc_html_e( 'Advanced', 'nexus' ); ?></option>
			</select>
		</p>
		<p>
			<label for="nexus_doc_read_time"><?php esc_html_e( 'Estimated Read Time (minutes):', 'nexus' ); ?></label>
			<input type="number" name="nexus_doc_read_time" id="nexus_doc_read_time" value="<?php echo esc_attr( $read_time ); ?>" class="widefat" min="1">
		</p>
		<p>
			<label for="nexus_doc_external_url"><?php esc_html_e( 'External URL:', 'nexus' ); ?></label>
			<input type="url" name="nexus_doc_external_url" id="nexus_doc_external_url" value="<?php echo esc_url( $external_url ); ?>" class="widefat" placeholder="https://">
			<small><?php esc_html_e( 'Optional: Link to external documentation', 'nexus' ); ?></small>
		</p>
		<?php
	}

	/**
	 * Render TOC Meta Box
	 */
	public function render_toc_meta_box( $post ) {
		$auto_toc = get_post_meta( $post->ID, '_nexus_doc_auto_toc', true );
		?>
		<p>
			<label>
				<input type="checkbox" name="nexus_doc_auto_toc" value="1" <?php checked( $auto_toc, '1' ); ?>>
				<?php esc_html_e( 'Auto-generate table of contents', 'nexus' ); ?>
			</label>
		</p>
		<p class="description">
			<?php esc_html_e( 'Automatically create a table of contents from H2 and H3 headings.', 'nexus' ); ?>
		</p>
		<?php
	}

	/**
	 * Save Meta
	 */
	public function save_meta( $post_id ) {
		if ( ! isset( $_POST['nexus_doc_settings_nonce'] ) || ! wp_verify_nonce( $_POST['nexus_doc_settings_nonce'], 'nexus_doc_settings' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Difficulty
		if ( isset( $_POST['nexus_doc_difficulty'] ) ) {
			update_post_meta( $post_id, '_nexus_doc_difficulty', sanitize_text_field( $_POST['nexus_doc_difficulty'] ) );
		}

		// Read time
		if ( isset( $_POST['nexus_doc_read_time'] ) ) {
			update_post_meta( $post_id, '_nexus_doc_read_time', intval( $_POST['nexus_doc_read_time'] ) );
		}

		// External URL
		if ( isset( $_POST['nexus_doc_external_url'] ) ) {
			update_post_meta( $post_id, '_nexus_doc_external_url', esc_url_raw( $_POST['nexus_doc_external_url'] ) );
		}

		// Auto TOC
		$auto_toc = isset( $_POST['nexus_doc_auto_toc'] ) ? '1' : '0';
		update_post_meta( $post_id, '_nexus_doc_auto_toc', $auto_toc );
	}

	/**
	 * Enhance Content
	 */
	public function enhance_content( $content ) {
		if ( ! is_singular( 'nexus_doc' ) ) {
			return $content;
		}

		$auto_toc = get_post_meta( get_the_ID(), '_nexus_doc_auto_toc', true );

		if ( '1' === $auto_toc ) {
			$toc = $this->generate_toc( $content );
			if ( ! empty( $toc ) ) {
				$content = $toc . $content;
			}
		}

		// Add anchor links to headings
		$content = $this->add_heading_anchors( $content );

		return $content;
	}

	/**
	 * Generate Table of Contents
	 */
	private function generate_toc( $content ) {
		preg_match_all( '/<h([2-3])(?:.*?)>(.+?)<\/h\1>/i', $content, $matches, PREG_SET_ORDER );

		if ( empty( $matches ) ) {
			return '';
		}

		$toc = '<div class="doc-table-of-contents">';
		$toc .= '<h2 class="toc-title">' . __( 'Table of Contents', 'nexus' ) . '</h2>';
		$toc .= '<ul class="toc-list">';

		foreach ( $matches as $heading ) {
			$level = $heading[1];
			$title = strip_tags( $heading[2] );
			$slug  = sanitize_title( $title );

			$toc .= sprintf(
				'<li class="toc-item toc-level-%d"><a href="#%s">%s</a></li>',
				$level,
				$slug,
				$title
			);
		}

		$toc .= '</ul>';
		$toc .= '</div>';

		return $toc;
	}

	/**
	 * Add Heading Anchors
	 */
	private function add_heading_anchors( $content ) {
		$content = preg_replace_callback(
			'/<h([2-3])(?:.*?)>(.+?)<\/h\1>/i',
			function( $matches ) {
				$level = $matches[1];
				$title = $matches[2];
				$slug  = sanitize_title( strip_tags( $title ) );

				return sprintf(
					'<h%1$d id="%2$s">%3$s <a href="#%2$s" class="heading-anchor" aria-label="%4$s"><span>#</span></a></h%1$d>',
					$level,
					$slug,
					$title,
					__( 'Link to this section', 'nexus' )
				);
			},
			$content
		);

		return $content;
	}
}
