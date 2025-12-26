<?php
/**
 * Documentation Search
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Docs Search Class
 */
class Nexus_Docs_Search {

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
		add_shortcode( 'nexus_docs_search', array( $this, 'search_shortcode' ) );
		add_action( 'wp_ajax_nexus_search_docs', array( $this, 'ajax_search' ) );
		add_action( 'wp_ajax_nopriv_nexus_search_docs', array( $this, 'ajax_search' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue Scripts
	 */
	public function enqueue_scripts() {
		if ( is_post_type_archive( 'nexus_doc' ) || is_singular( 'nexus_doc' ) ) {
			wp_enqueue_script(
				'nexus-docs-search',
				NEXUS_PRO_URI . '/assets/js/docs-search.js',
				array( 'jquery' ),
				NEXUS_PRO_VERSION,
				true
			);

			wp_localize_script(
				'nexus-docs-search',
				'nexusDocsSearch',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'nexus-docs-search' ),
				)
			);
		}
	}

	/**
	 * Search Shortcode
	 */
	public function search_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'placeholder' => __( 'Search documentation...', 'nexus' ),
				'show_popular' => 'yes',
			),
			$atts
		);

		ob_start();
		?>
		<div class="nexus-docs-search">
			<form class="docs-search-form" role="search">
				<div class="search-input-wrapper">
					<input type="search" 
						class="docs-search-input" 
						placeholder="<?php echo esc_attr( $atts['placeholder'] ); ?>"
						autocomplete="off">
					<button type="submit" class="docs-search-submit">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
							<path d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"/>
						</svg>
					</button>
				</div>
				<div class="docs-search-results" style="display: none;">
					<div class="results-list"></div>
				</div>
			</form>

			<?php if ( 'yes' === $atts['show_popular'] ) : ?>
				<div class="popular-docs">
					<h4><?php esc_html_e( 'Popular Documentation', 'nexus' ); ?></h4>
					<?php $this->render_popular_docs(); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render Popular Docs
	 */
	private function render_popular_docs() {
		$popular = new WP_Query(
			array(
				'post_type'      => 'nexus_doc',
				'posts_per_page' => 5,
				'meta_key'       => '_nexus_doc_views',
				'orderby'        => 'meta_value_num',
				'order'          => 'DESC',
			)
		);

		if ( $popular->have_posts() ) {
			echo '<ul class="popular-docs-list">';
			while ( $popular->have_posts() ) {
				$popular->the_post();
				printf(
					'<li><a href="%s">%s</a></li>',
					esc_url( get_permalink() ),
					esc_html( get_the_title() )
				);
			}
			echo '</ul>';
			wp_reset_postdata();
		}
	}

	/**
	 * Ajax Search
	 */
	public function ajax_search() {
		check_ajax_referer( 'nexus-docs-search', 'nonce' );

		$search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';

		if ( empty( $search ) || strlen( $search ) < 3 ) {
			wp_send_json_error( __( 'Search term too short', 'nexus' ) );
		}

		$args = array(
			'post_type'      => 'nexus_doc',
			'posts_per_page' => 10,
			's'              => $search,
			'orderby'        => 'relevance',
		);

		$query = new WP_Query( $args );
		$results = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$categories = get_the_terms( get_the_ID(), 'doc_category' );
				$category_name = '';
				if ( $categories && ! is_wp_error( $categories ) ) {
					$category_name = $categories[0]->name;
				}

				$results[] = array(
					'id'       => get_the_ID(),
					'title'    => get_the_title(),
					'url'      => get_permalink(),
					'excerpt'  => wp_trim_words( get_the_excerpt(), 20 ),
					'category' => $category_name,
				);
			}
			wp_reset_postdata();
		}

		if ( ! empty( $results ) ) {
			wp_send_json_success( $results );
		} else {
			wp_send_json_error( __( 'No results found', 'nexus' ) );
		}
	}
}
