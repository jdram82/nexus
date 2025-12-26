<?php
/**
 * Advanced Product Filter
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Product Filter Class
 */
class Nexus_Product_Filter {

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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_shortcode( 'nexus_product_filter', array( $this, 'filter_shortcode' ) );
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
	}

	/**
	 * Enqueue Scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'nexus-product-filter',
			NEXUS_PRO_URI . '/assets/js/product-filter.js',
			array( 'jquery' ),
			NEXUS_PRO_VERSION,
			true
		);

		wp_localize_script(
			'nexus-product-filter',
			'nexusFilter',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'nexus-filter-nonce' ),
			)
		);

		wp_enqueue_style(
			'nexus-product-filter',
			NEXUS_PRO_URI . '/assets/css/product-filter.css',
			array(),
			NEXUS_PRO_VERSION
		);
	}

	/**
	 * Filter Shortcode
	 */
	public function filter_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'layout'       => 'sidebar', // sidebar, top, popup
				'show_search'  => 'yes',
				'show_sort'    => 'yes',
				'show_filters' => 'yes',
				'show_count'   => 'yes',
			),
			$atts
		);

		ob_start();
		$this->render_filter( $atts );
		return ob_get_clean();
	}

	/**
	 * Render Filter
	 */
	public function render_filter( $args = array() ) {
		$defaults = array(
			'layout'       => 'sidebar',
			'show_search'  => true,
			'show_sort'    => true,
			'show_filters' => true,
			'show_count'   => true,
		);

		$args = wp_parse_args( $args, $defaults );
		?>
		<div class="nexus-product-filter" data-layout="<?php echo esc_attr( $args['layout'] ); ?>">
			
			<?php if ( $args['show_search'] ) : ?>
				<div class="filter-section filter-search">
					<input type="text" class="filter-search-input" placeholder="<?php esc_attr_e( 'Search products...', 'nexus' ); ?>">
				</div>
			<?php endif; ?>

			<?php if ( $args['show_sort'] ) : ?>
				<div class="filter-section filter-sort">
					<label><?php esc_html_e( 'Sort By:', 'nexus' ); ?></label>
					<select class="filter-sort-select">
						<option value="date"><?php esc_html_e( 'Newest First', 'nexus' ); ?></option>
						<option value="title"><?php esc_html_e( 'Name (A-Z)', 'nexus' ); ?></option>
						<option value="title_desc"><?php esc_html_e( 'Name (Z-A)', 'nexus' ); ?></option>
						<?php if ( class_exists( 'WooCommerce' ) ) : ?>
							<option value="price"><?php esc_html_e( 'Price (Low to High)', 'nexus' ); ?></option>
							<option value="price_desc"><?php esc_html_e( 'Price (High to Low)', 'nexus' ); ?></option>
						<?php endif; ?>
					</select>
				</div>
			<?php endif; ?>

			<?php if ( $args['show_filters'] ) : ?>
				
				<!-- Categories -->
				<?php
				$categories = get_terms(
					array(
						'taxonomy'   => 'product_category',
						'hide_empty' => true,
					)
				);
				if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
					?>
					<div class="filter-section filter-categories">
						<h4 class="filter-title"><?php esc_html_e( 'Categories', 'nexus' ); ?></h4>
						<ul class="filter-list">
							<?php foreach ( $categories as $category ) : ?>
								<li>
									<label>
										<input type="checkbox" name="category[]" value="<?php echo esc_attr( $category->term_id ); ?>">
										<span><?php echo esc_html( $category->name ); ?></span>
										<?php if ( $args['show_count'] ) : ?>
											<span class="count">(<?php echo esc_html( $category->count ); ?>)</span>
										<?php endif; ?>
									</label>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<!-- Tags -->
				<?php
				$tags = get_terms(
					array(
						'taxonomy'   => 'product_tag',
						'hide_empty' => true,
					)
				);
				if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) :
					?>
					<div class="filter-section filter-tags">
						<h4 class="filter-title"><?php esc_html_e( 'Tags', 'nexus' ); ?></h4>
						<ul class="filter-list filter-tags-list">
							<?php foreach ( $tags as $tag ) : ?>
								<li>
									<label class="filter-tag">
										<input type="checkbox" name="tag[]" value="<?php echo esc_attr( $tag->term_id ); ?>">
										<span><?php echo esc_html( $tag->name ); ?></span>
									</label>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<!-- Specifications Filter -->
				<?php $this->render_specification_filters(); ?>

				<!-- Price Range (if WooCommerce) -->
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<div class="filter-section filter-price">
						<h4 class="filter-title"><?php esc_html_e( 'Price Range', 'nexus' ); ?></h4>
						<div class="price-range-inputs">
							<input type="number" class="price-min" placeholder="<?php esc_attr_e( 'Min', 'nexus' ); ?>">
							<span>-</span>
							<input type="number" class="price-max" placeholder="<?php esc_attr_e( 'Max', 'nexus' ); ?>">
						</div>
						<div class="price-range-slider"></div>
					</div>
				<?php endif; ?>

			<?php endif; ?>

			<!-- Actions -->
			<div class="filter-actions">
				<button type="button" class="button filter-apply"><?php esc_html_e( 'Apply Filters', 'nexus' ); ?></button>
				<button type="button" class="button-link filter-reset"><?php esc_html_e( 'Reset', 'nexus' ); ?></button>
			</div>

			<!-- Active Filters -->
			<div class="active-filters" style="display: none;">
				<h5><?php esc_html_e( 'Active Filters:', 'nexus' ); ?></h5>
				<div class="active-filters-list"></div>
			</div>

		</div>
		<?php
	}

	/**
	 * Render Specification Filters
	 */
	private function render_specification_filters() {
		global $wpdb;

		// Get all unique specification keys
		$spec_keys = $wpdb->get_col(
			"SELECT DISTINCT meta_key 
			FROM {$wpdb->postmeta} 
			WHERE meta_key LIKE '_nexus_spec_%' 
			AND meta_key != '_nexus_specifications'"
		);

		if ( empty( $spec_keys ) ) {
			return;
		}

		// Group specifications
		$specifications = array();
		foreach ( $spec_keys as $key ) {
			$spec_name = str_replace( '_nexus_spec_', '', $key );
			$spec_name = str_replace( '_', ' ', $spec_name );
			$spec_name = ucwords( $spec_name );

			// Get unique values for this specification
			$values = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT meta_value 
					FROM {$wpdb->postmeta} 
					WHERE meta_key = %s 
					AND meta_value != ''
					ORDER BY meta_value",
					$key
				)
			);

			if ( ! empty( $values ) ) {
				$specifications[ $key ] = array(
					'name'   => $spec_name,
					'values' => $values,
				);
			}
		}

		if ( empty( $specifications ) ) {
			return;
		}
		?>
		<div class="filter-section filter-specifications">
			<h4 class="filter-title"><?php esc_html_e( 'Specifications', 'nexus' ); ?></h4>
			<?php foreach ( $specifications as $key => $spec ) : ?>
				<div class="filter-spec-group">
					<h5 class="filter-spec-title"><?php echo esc_html( $spec['name'] ); ?></h5>
					<ul class="filter-list">
						<?php foreach ( $spec['values'] as $value ) : ?>
							<li>
								<label>
									<input type="checkbox" name="spec[<?php echo esc_attr( $key ); ?>][]" value="<?php echo esc_attr( $value ); ?>">
									<span><?php echo esc_html( $value ); ?></span>
								</label>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Register Widget
	 */
	public function register_widget() {
		register_widget( 'Nexus_Product_Filter_Widget' );
	}
}

/**
 * Product Filter Widget
 */
class Nexus_Product_Filter_Widget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(
			'nexus_product_filter',
			__( 'Nexus Product Filter', 'nexus' ),
			array( 'description' => __( 'Advanced product filtering', 'nexus' ) )
		);
	}

	/**
	 * Widget Output
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		$filter = Nexus_Product_Filter::instance();
		$filter->render_filter(
			array(
				'layout'       => 'sidebar',
				'show_search'  => isset( $instance['show_search'] ) ? $instance['show_search'] : true,
				'show_sort'    => isset( $instance['show_sort'] ) ? $instance['show_sort'] : true,
				'show_filters' => isset( $instance['show_filters'] ) ? $instance['show_filters'] : true,
				'show_count'   => isset( $instance['show_count'] ) ? $instance['show_count'] : true,
			)
		);

		echo $args['after_widget'];
	}

	/**
	 * Widget Form
	 */
	public function form( $instance ) {
		$title        = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Filter Products', 'nexus' );
		$show_search  = isset( $instance['show_search'] ) ? (bool) $instance['show_search'] : true;
		$show_sort    = isset( $instance['show_sort'] ) ? (bool) $instance['show_sort'] : true;
		$show_filters = isset( $instance['show_filters'] ) ? (bool) $instance['show_filters'] : true;
		$show_count   = isset( $instance['show_count'] ) ? (bool) $instance['show_count'] : true;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'nexus' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_search ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_search' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_search' ) ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_search' ) ); ?>"><?php esc_html_e( 'Show Search', 'nexus' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_sort ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_sort' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_sort' ) ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_sort' ) ); ?>"><?php esc_html_e( 'Show Sort', 'nexus' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_filters ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_filters' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_filters' ) ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_filters' ) ); ?>"><?php esc_html_e( 'Show Filters', 'nexus' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_count ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_count' ) ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>"><?php esc_html_e( 'Show Count', 'nexus' ); ?></label>
		</p>
		<?php
	}

	/**
	 * Update Widget
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                 = array();
		$instance['title']        = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['show_search']  = ! empty( $new_instance['show_search'] ) ? 1 : 0;
		$instance['show_sort']    = ! empty( $new_instance['show_sort'] ) ? 1 : 0;
		$instance['show_filters'] = ! empty( $new_instance['show_filters'] ) ? 1 : 0;
		$instance['show_count']   = ! empty( $new_instance['show_count'] ) ? 1 : 0;
		return $instance;
	}
}
