<?php
/**
 * Ajax Product Filter
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Ajax Filter Class
 */
class Nexus_Ajax_Filter {

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
		add_action( 'wp_ajax_nexus_filter_products', array( $this, 'filter_products' ) );
		add_action( 'wp_ajax_nopriv_nexus_filter_products', array( $this, 'filter_products' ) );
	}

	/**
	 * Filter Products
	 */
	public function filter_products() {
		check_ajax_referer( 'nexus-filter-nonce', 'nonce' );

		$search      = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
		$sort        = isset( $_POST['sort'] ) ? sanitize_key( $_POST['sort'] ) : 'date';
		$categories  = isset( $_POST['categories'] ) ? array_map( 'intval', $_POST['categories'] ) : array();
		$tags        = isset( $_POST['tags'] ) ? array_map( 'intval', $_POST['tags'] ) : array();
		$specs       = isset( $_POST['specs'] ) ? $_POST['specs'] : array();
		$price_min   = isset( $_POST['price_min'] ) ? floatval( $_POST['price_min'] ) : 0;
		$price_max   = isset( $_POST['price_max'] ) ? floatval( $_POST['price_max'] ) : 0;
		$page        = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
		$per_page    = isset( $_POST['per_page'] ) ? intval( $_POST['per_page'] ) : 12;

		// Build query args
		$args = array(
			'post_type'      => 'nexus_product',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'post_status'    => 'publish',
		);

		// Search
		if ( ! empty( $search ) ) {
			$args['s'] = $search;
		}

		// Sort
		switch ( $sort ) {
			case 'title':
				$args['orderby'] = 'title';
				$args['order']   = 'ASC';
				break;
			case 'title_desc':
				$args['orderby'] = 'title';
				$args['order']   = 'DESC';
				break;
			case 'price':
				$args['meta_key'] = '_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';
				break;
			case 'price_desc':
				$args['meta_key'] = '_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			default:
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
		}

		// Tax Query
		$tax_query = array( 'relation' => 'AND' );

		if ( ! empty( $categories ) ) {
			$tax_query[] = array(
				'taxonomy' => 'product_category',
				'field'    => 'term_id',
				'terms'    => $categories,
			);
		}

		if ( ! empty( $tags ) ) {
			$tax_query[] = array(
				'taxonomy' => 'product_tag',
				'field'    => 'term_id',
				'terms'    => $tags,
			);
		}

		if ( count( $tax_query ) > 1 ) {
			$args['tax_query'] = $tax_query;
		}

		// Meta Query (Specifications)
		if ( ! empty( $specs ) ) {
			$meta_query = array( 'relation' => 'AND' );

			foreach ( $specs as $spec_key => $spec_values ) {
				if ( ! empty( $spec_values ) && is_array( $spec_values ) ) {
					$meta_query[] = array(
						'key'     => $spec_key,
						'value'   => $spec_values,
						'compare' => 'IN',
					);
				}
			}

			// Price range
			if ( $price_min > 0 || $price_max > 0 ) {
				if ( $price_min > 0 && $price_max > 0 ) {
					$meta_query[] = array(
						'key'     => '_price',
						'value'   => array( $price_min, $price_max ),
						'compare' => 'BETWEEN',
						'type'    => 'NUMERIC',
					);
				} elseif ( $price_min > 0 ) {
					$meta_query[] = array(
						'key'     => '_price',
						'value'   => $price_min,
						'compare' => '>=',
						'type'    => 'NUMERIC',
					);
				} elseif ( $price_max > 0 ) {
					$meta_query[] = array(
						'key'     => '_price',
						'value'   => $price_max,
						'compare' => '<=',
						'type'    => 'NUMERIC',
					);
				}
			}

			if ( count( $meta_query ) > 1 ) {
				$args['meta_query'] = $meta_query;
			}
		}

		// Execute query
		$query = new WP_Query( $args );

		ob_start();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				get_template_part( 'template-parts/content/content', 'nexus_product' );
			}
		} else {
			echo '<p class="no-products">' . esc_html__( 'No products found.', 'nexus' ) . '</p>';
		}

		$html = ob_get_clean();
		wp_reset_postdata();

		wp_send_json_success(
			array(
				'html'       => $html,
				'found'      => $query->found_posts,
				'max_pages'  => $query->max_num_pages,
				'page'       => $page,
			)
		);
	}
}
