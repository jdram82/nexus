<?php
/**
 * Google Maps Widget
 *
 * Display embedded Google Maps
 *
 * @package Nexus_Pro
 * @subpackage Theme_Builder
 * @since 3.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Google Maps Widget Class
 */
class Nexus_Google_Maps_Widget extends Nexus_Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'google-maps';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return __( 'Google Maps', 'nexus-pro' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'dashicons-location-alt';
	}

	/**
	 * Get widget categories
	 */
	public function get_categories() {
		return array( 'content' );
	}

	/**
	 * Register widget controls
	 */
	protected function register_controls() {
		// Location controls
		$this->start_controls_section(
			'location_section',
			array(
				'label' => __( 'Location', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'address',
			array(
				'label'       => __( 'Address', 'nexus-pro' ),
				'type'        => 'text',
				'default'     => 'New York, NY',
				'placeholder' => __( 'Enter address or coordinates', 'nexus-pro' ),
				'description' => __( 'Enter an address or coordinates (lat,lng)', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'zoom',
			array(
				'label'   => __( 'Zoom Level', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 12,
				'min'     => 1,
				'max'     => 20,
			)
		);

		$this->add_control(
			'map_type',
			array(
				'label'   => __( 'Map Type', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'roadmap'   => __( 'Roadmap', 'nexus-pro' ),
					'satellite' => __( 'Satellite', 'nexus-pro' ),
					'hybrid'    => __( 'Hybrid', 'nexus-pro' ),
					'terrain'   => __( 'Terrain', 'nexus-pro' ),
				),
				'default' => 'roadmap',
			)
		);

		$this->end_controls_section();

		// Marker controls
		$this->start_controls_section(
			'marker_section',
			array(
				'label' => __( 'Marker', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'show_marker',
			array(
				'label'   => __( 'Show Marker', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'marker_title',
			array(
				'label'       => __( 'Marker Title', 'nexus-pro' ),
				'type'        => 'text',
				'default'     => '',
				'placeholder' => __( 'Location Name', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'info_window',
			array(
				'label'       => __( 'Info Window Content', 'nexus-pro' ),
				'type'        => 'textarea',
				'default'     => '',
				'placeholder' => __( 'Content to show when marker is clicked', 'nexus-pro' ),
			)
		);

		$this->end_controls_section();

		// Map controls section
		$this->start_controls_section(
			'controls_section',
			array(
				'label' => __( 'Map Controls', 'nexus-pro' ),
			)
		);

		$this->add_control(
			'zoom_control',
			array(
				'label'   => __( 'Zoom Control', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'street_view',
			array(
				'label'   => __( 'Street View', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => false,
			)
		);

		$this->add_control(
			'map_type_control',
			array(
				'label'   => __( 'Map Type Control', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => false,
			)
		);

		$this->add_control(
			'fullscreen',
			array(
				'label'   => __( 'Fullscreen Control', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'draggable',
			array(
				'label'   => __( 'Draggable', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => true,
			)
		);

		$this->add_control(
			'scroll_wheel',
			array(
				'label'   => __( 'Scroll Wheel Zoom', 'nexus-pro' ),
				'type'    => 'checkbox',
				'default' => false,
			)
		);

		$this->end_controls_section();

		// Style controls
		$this->start_controls_section(
			'style_section',
			array(
				'label' => __( 'Style', 'nexus-pro' ),
				'tab'   => 'style',
			)
		);

		$this->add_control(
			'height',
			array(
				'label'   => __( 'Map Height', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 400,
				'min'     => 200,
				'max'     => 800,
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'   => __( 'Border Radius', 'nexus-pro' ),
				'type'    => 'slider',
				'default' => 4,
				'min'     => 0,
				'max'     => 50,
			)
		);

		$this->add_control(
			'map_style',
			array(
				'label'   => __( 'Map Style', 'nexus-pro' ),
				'type'    => 'select',
				'options' => array(
					'standard'   => __( 'Standard', 'nexus-pro' ),
					'silver'     => __( 'Silver', 'nexus-pro' ),
					'retro'      => __( 'Retro', 'nexus-pro' ),
					'dark'       => __( 'Dark', 'nexus-pro' ),
					'night'      => __( 'Night', 'nexus-pro' ),
					'aubergine'  => __( 'Aubergine', 'nexus-pro' ),
				),
				'default' => 'standard',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get map style JSON
	 *
	 * @param string $style Style name.
	 * @return string
	 */
	private function get_map_style( $style ) {
		$styles = array(
			'silver' => '[{"elementType":"geometry","stylers":[{"color":"#f5f5f5"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#f5f5f5"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#bdbdbd"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#eeeeee"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#ffffff"}]},{"featureType":"road.arterial","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#dadada"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#eeeeee"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#c9c9c9"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]}]',
			'dark' => '[{"elementType":"geometry","stylers":[{"color":"#212121"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#212121"}]},{"featureType":"administrative","elementType":"geometry","stylers":[{"color":"#757575"}]},{"featureType":"administrative.country","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"color":"#bdbdbd"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#181818"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#2c2c2c"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#8a8a8a"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#373737"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#3c3c3c"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry","stylers":[{"color":"#4e4e4e"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"transit","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#3d3d3d"}]}]',
		);

		return isset( $styles[ $style ] ) ? $styles[ $style ] : '[]';
	}

	/**
	 * Render widget output
	 */
	protected function render() {
		$settings = $this->get_settings();

		if ( empty( $settings['address'] ) ) {
			echo '<div class="nexus-maps-placeholder">' . esc_html__( 'Please enter an address or coordinates', 'nexus-pro' ) . '</div>';
			return;
		}

		$map_id = 'map-' . uniqid();
		$map_config = array(
			'address'         => $settings['address'],
			'zoom'            => intval( $settings['zoom'] ),
			'mapType'         => $settings['map_type'],
			'showMarker'      => (bool) $settings['show_marker'],
			'markerTitle'     => $settings['marker_title'],
			'infoWindow'      => $settings['info_window'],
			'zoomControl'     => (bool) $settings['zoom_control'],
			'streetView'      => (bool) $settings['street_view'],
			'mapTypeControl'  => (bool) $settings['map_type_control'],
			'fullscreen'      => (bool) $settings['fullscreen'],
			'draggable'       => (bool) $settings['draggable'],
			'scrollWheel'     => (bool) $settings['scroll_wheel'],
			'mapStyle'        => $this->get_map_style( $settings['map_style'] ),
		);

		?>
		<div class="nexus-google-maps" 
			 style="height: <?php echo esc_attr( $settings['height'] ); ?>px; 
					border-radius: <?php echo esc_attr( $settings['border_radius'] ); ?>px;">
			
			<div id="<?php echo esc_attr( $map_id ); ?>" 
				 class="map-container"
				 data-map-config='<?php echo esc_attr( wp_json_encode( $map_config ) ); ?>'
				 style="width: 100%; height: 100%; border-radius: inherit;">
			</div>
			
			<noscript>
				<div class="map-fallback">
					<p><?php esc_html_e( 'Please enable JavaScript to view the map.', 'nexus-pro' ); ?></p>
					<a href="https://www.google.com/maps/search/?api=1&query=<?php echo esc_attr( urlencode( $settings['address'] ) ); ?>" 
					   target="_blank" 
					   rel="noopener noreferrer">
						<?php esc_html_e( 'View on Google Maps', 'nexus-pro' ); ?>
					</a>
				</div>
			</noscript>
		</div>
		<?php
	}
}
