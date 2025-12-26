<?php
/**
 * Products Custom Post Type
 *
 * @package Nexus
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Products Class
 */
class Nexus_Products {

	/**
	 * Instance
	 *
	 * @var Nexus_Products
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
		add_action( 'save_post_nexus_product', array( $this, 'save_specifications' ) );
	}

	/**
	 * Register Post Type
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => esc_html__( 'Products', 'nexus' ),
			'singular_name'         => esc_html__( 'Product', 'nexus' ),
			'menu_name'             => esc_html__( 'Products', 'nexus' ),
			'add_new'               => esc_html__( 'Add New', 'nexus' ),
			'add_new_item'          => esc_html__( 'Add New Product', 'nexus' ),
			'edit_item'             => esc_html__( 'Edit Product', 'nexus' ),
			'new_item'              => esc_html__( 'New Product', 'nexus' ),
			'view_item'             => esc_html__( 'View Product', 'nexus' ),
			'search_items'          => esc_html__( 'Search Products', 'nexus' ),
			'not_found'             => esc_html__( 'No products found', 'nexus' ),
			'not_found_in_trash'    => esc_html__( 'No products found in trash', 'nexus' ),
			'all_items'             => esc_html__( 'All Products', 'nexus' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => 'product' ),
			'capability_type'     => 'post',
			'has_archive'         => true,
			'hierarchical'        => false,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-products',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			'show_in_rest'        => true,
		);

		register_post_type( 'nexus_product', $args );
	}

	/**
	 * Register Taxonomies
	 */
	public function register_taxonomies() {
		// Product Category
		register_taxonomy(
			'product_category',
			'nexus_product',
			array(
				'labels'            => array(
					'name'          => esc_html__( 'Product Categories', 'nexus' ),
					'singular_name' => esc_html__( 'Product Category', 'nexus' ),
				),
				'hierarchical'      => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'product-category' ),
			)
		);

		// Product Tag
		register_taxonomy(
			'product_tag',
			'nexus_product',
			array(
				'labels'            => array(
					'name'          => esc_html__( 'Product Tags', 'nexus' ),
					'singular_name' => esc_html__( 'Product Tag', 'nexus' ),
				),
				'hierarchical'      => false,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'product-tag' ),
			)
		);
	}

	/**
	 * Add Meta Boxes
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'nexus_product_specifications',
			esc_html__( 'Product Specifications', 'nexus' ),
			array( $this, 'render_specifications_meta_box' ),
			'nexus_product',
			'normal',
			'high'
		);
	}

	/**
	 * Render Specifications Meta Box
	 *
	 * @param WP_Post $post Post object.
	 */
	public function render_specifications_meta_box( $post ) {
		wp_nonce_field( 'nexus_product_specifications', 'nexus_product_specifications_nonce' );
		
		$specifications = get_post_meta( $post->ID, '_nexus_specifications', true );
		$specifications = $specifications ? $specifications : array();
		?>
		<div id="nexus-specifications-wrapper">
			<table class="widefat">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Parameter', 'nexus' ); ?></th>
						<th><?php esc_html_e( 'Value', 'nexus' ); ?></th>
						<th><?php esc_html_e( 'Unit', 'nexus' ); ?></th>
						<th style="width: 50px;"></th>
					</tr>
				</thead>
				<tbody id="specifications-rows">
					<?php
					if ( ! empty( $specifications ) ) {
						foreach ( $specifications as $index => $spec ) {
							$this->render_specification_row( $index, $spec );
						}
					} else {
						$this->render_specification_row( 0 );
					}
					?>
				</tbody>
			</table>
			<p>
				<button type="button" class="button" id="add-specification-row">
					<?php esc_html_e( 'Add Row', 'nexus' ); ?>
				</button>
			</p>
		</div>

		<script>
		jQuery(document).ready(function($) {
			var rowIndex = <?php echo count( $specifications ); ?>;
			
			$('#add-specification-row').on('click', function() {
				var row = '<tr>' +
					'<td><input type="text" name="specifications[' + rowIndex + '][parameter]" class="widefat" /></td>' +
					'<td><input type="text" name="specifications[' + rowIndex + '][value]" class="widefat" /></td>' +
					'<td><input type="text" name="specifications[' + rowIndex + '][unit]" class="widefat" /></td>' +
					'<td><button type="button" class="button remove-row">Remove</button></td>' +
					'</tr>';
				
				$('#specifications-rows').append(row);
				rowIndex++;
			});
			
			$(document).on('click', '.remove-row', function() {
				$(this).closest('tr').remove();
			});
		});
		</script>
		<?php
	}

	/**
	 * Render Specification Row
	 *
	 * @param int   $index Row index.
	 * @param array $spec  Specification data.
	 */
	private function render_specification_row( $index, $spec = array() ) {
		$parameter = isset( $spec['parameter'] ) ? esc_attr( $spec['parameter'] ) : '';
		$value     = isset( $spec['value'] ) ? esc_attr( $spec['value'] ) : '';
		$unit      = isset( $spec['unit'] ) ? esc_attr( $spec['unit'] ) : '';
		?>
		<tr>
			<td>
				<input type="text" name="specifications[<?php echo $index; ?>][parameter]" value="<?php echo $parameter; ?>" class="widefat" />
			</td>
			<td>
				<input type="text" name="specifications[<?php echo $index; ?>][value]" value="<?php echo $value; ?>" class="widefat" />
			</td>
			<td>
				<input type="text" name="specifications[<?php echo $index; ?>][unit]" value="<?php echo $unit; ?>" class="widefat" />
			</td>
			<td>
				<button type="button" class="button remove-row"><?php esc_html_e( 'Remove', 'nexus' ); ?></button>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save Specifications
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_specifications( $post_id ) {
		// Check nonce
		if ( ! isset( $_POST['nexus_product_specifications_nonce'] ) ||
		     ! wp_verify_nonce( $_POST['nexus_product_specifications_nonce'], 'nexus_product_specifications' ) ) {
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

		// Save specifications
		if ( isset( $_POST['specifications'] ) && is_array( $_POST['specifications'] ) ) {
			$specifications = array();
			foreach ( $_POST['specifications'] as $spec ) {
				if ( ! empty( $spec['parameter'] ) || ! empty( $spec['value'] ) ) {
					$specifications[] = array(
						'parameter' => sanitize_text_field( $spec['parameter'] ),
						'value'     => sanitize_text_field( $spec['value'] ),
						'unit'      => sanitize_text_field( $spec['unit'] ),
					);
				}
			}
			update_post_meta( $post_id, '_nexus_specifications', $specifications );
		}
	}
}
