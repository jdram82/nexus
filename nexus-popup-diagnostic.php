<?php
/**
 * Plugin Name: Nexus Popup Diagnostic
 * Description: Diagnostic tool to check if popup builder is loading correctly
 * Version: 1.0
 */

add_action( 'admin_notices', function() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	echo '<div class="notice notice-info">';
	echo '<h3>Nexus Popup Builder Diagnostic</h3>';
	
	// Check if PRO is loaded
	echo '<p><strong>PRO Classes:</strong></p>';
	echo '<ul>';
	echo '<li>Nexus_Pro: ' . ( class_exists( 'Nexus_Pro' ) ? '✅ Loaded' : '❌ Not Loaded' ) . '</li>';
	echo '<li>Nexus_License_Manager: ' . ( class_exists( 'Nexus_License_Manager' ) ? '✅ Loaded' : '❌ Not Loaded' ) . '</li>';
	echo '<li>Nexus_Popup_Builder: ' . ( class_exists( 'Nexus_Popup_Builder' ) ? '✅ Loaded' : '❌ Not Loaded' ) . '</li>';
	echo '<li>Nexus_Popup_Triggers: ' . ( class_exists( 'Nexus_Popup_Triggers' ) ? '✅ Loaded' : '❌ Not Loaded' ) . '</li>';
	echo '<li>Nexus_Popup_Targeting: ' . ( class_exists( 'Nexus_Popup_Targeting' ) ? '✅ Loaded' : '❌ Not Loaded' ) . '</li>';
	echo '<li>Nexus_Popup_Editor: ' . ( class_exists( 'Nexus_Popup_Editor' ) ? '✅ Loaded' : '❌ Not Loaded' ) . '</li>';
	echo '</ul>';
	
	// Check license
	if ( class_exists( 'Nexus_License_Manager' ) ) {
		$license_manager = Nexus_License_Manager::instance();
		echo '<p><strong>License Status:</strong></p>';
		echo '<ul>';
		echo '<li>Has popup_builder feature: ' . ( $license_manager->has_feature( 'popup_builder' ) ? '✅ Yes' : '❌ No' ) . '</li>';
		echo '<li>Current Tier: ' . $license_manager->get_tier() . '</li>';
		echo '</ul>';
		
		// Check available features
		echo '<p><strong>Available Features:</strong></p>';
		echo '<ul>';
		$features = [ 'cloud_storage', 'template_sync', 'payment_gateway', 'theme_builder', 'popup_builder', 'mega_menu', 'api_docs', 'ab_testing', 'white_label' ];
		foreach ( $features as $feature ) {
			echo '<li>' . $feature . ': ' . ( $license_manager->has_feature( $feature ) ? '✅' : '❌' ) . '</li>';
		}
		echo '</ul>';
	}
	
	// Check if popup builder instance exists
	if ( class_exists( 'Nexus_Popup_Builder' ) ) {
		try {
			$popup_builder = Nexus_Popup_Builder::get_instance();
			echo '<p><strong>Popup Builder Instance:</strong> ✅ Created successfully</p>';
			
			// Check if hooks are registered
			global $wp_filter;
			echo '<p><strong>Registered Hooks:</strong></p>';
			echo '<ul>';
			echo '<li>admin_menu hook: ' . ( isset( $wp_filter['admin_menu'] ) ? '✅ Registered' : '❌ Not Registered' ) . '</li>';
			if ( isset( $wp_filter['admin_menu'] ) ) {
				echo '<li style="margin-left: 20px;">Callbacks: ' . count( $wp_filter['admin_menu']->callbacks ) . '</li>';
			}
			echo '</ul>';
		} catch ( Exception $e ) {
			echo '<p><strong>Error:</strong> ' . $e->getMessage() . '</p>';
		}
	}
	
	// Check constants
	echo '<p><strong>Constants:</strong></p>';
	echo '<ul>';
	echo '<li>NEXUS_PRO_DIR: ' . ( defined( 'NEXUS_PRO_DIR' ) ? NEXUS_PRO_DIR : 'Not defined' ) . '</li>';
	echo '<li>NEXUS_PRO_PATH: ' . ( defined( 'NEXUS_PRO_PATH' ) ? NEXUS_PRO_PATH : 'Not defined' ) . '</li>';
	echo '</ul>';
	
	// Check if popup files exist
	if ( defined( 'NEXUS_PRO_DIR' ) ) {
		echo '<p><strong>Popup Builder Files:</strong></p>';
		echo '<ul>';
		$files = [
			'class-popup-builder.php',
			'class-popup-triggers.php',
			'class-popup-targeting.php',
			'class-popup-editor.php'
		];
		foreach ( $files as $file ) {
			$path = NEXUS_PRO_DIR . '/popup-builder/' . $file;
			echo '<li>' . $file . ': ' . ( file_exists( $path ) ? '✅ Exists' : '❌ Missing - ' . $path ) . '</li>';
		}
		echo '</ul>';
	}
	
	echo '</div>';
});
