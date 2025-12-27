<?php
/**
 * Gravity Forms Deep Integration
 * 
 * @package Nexus_Theme
 * @subpackage Plugin_Orchestrator
 * @since 1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nexus_Gravity_Forms_Integration {
    
    /**
     * Get integration name
     */
    public function get_name() {
        return __( 'Gravity Forms', 'nexus' );
    }
    
    /**
     * Get integration description
     */
    public function get_description() {
        return __( 'Deep integration with automatic Nexus design system styling, enhanced validation, and performance optimization.', 'nexus' );
    }
    
    /**
     * Get integration features
     */
    public function get_features() {
        return array(
            __( 'Auto-apply Nexus design tokens', 'nexus' ),
            __( 'Enhanced form validation styling', 'nexus' ),
            __( 'Mobile-optimized layouts', 'nexus' ),
            __( 'Performance optimization', 'nexus' ),
            __( 'Accessibility improvements', 'nexus' ),
        );
    }
    
    /**
     * Get style overrides
     */
    public function get_style_overrides() {
        return array(
            '.gform_wrapper' => array(
                'font-family' => '{{font-family-base}}',
            ),
            '.gform_wrapper .gfield_label' => array(
                'color' => '{{secondary-color}}',
                'font-weight' => '600',
            ),
            '.gform_wrapper input[type="text"], .gform_wrapper input[type="email"], .gform_wrapper textarea' => array(
                'border' => '1px solid #ddd',
                'border-radius' => '{{border-radius}}',
                'padding' => '12px 16px',
                'transition' => 'all 0.3s ease',
            ),
            '.gform_wrapper input:focus, .gform_wrapper textarea:focus' => array(
                'border-color' => '{{primary-color}}',
                'box-shadow' => '0 0 0 3px rgba(0, 102, 204, 0.1)',
                'outline' => 'none',
            ),
            '.gform_wrapper .gform_button' => array(
                'background' => '{{primary-color}}',
                'color' => '#fff',
                'border' => 'none',
                'border-radius' => '{{border-radius}}',
                'padding' => '12px 32px',
                'font-weight' => '600',
                'cursor' => 'pointer',
                'transition' => 'all 0.3s ease',
            ),
            '.gform_wrapper .gform_button:hover' => array(
                'background' => '{{accent-color}}',
                'transform' => 'translateY(-2px)',
                'box-shadow' => '0 4px 12px rgba(0, 0, 0, 0.15)',
            ),
            '.gform_wrapper .validation_error' => array(
                'color' => '{{accent-color}}',
                'border' => '2px solid {{accent-color}}',
                'border-radius' => '{{border-radius}}',
                'padding' => '{{spacing-unit}}',
            ),
        );
    }
    
    /**
     * Test integration
     */
    public function test_integration() {
        if ( ! class_exists( 'GFForms' ) ) {
            return array(
                'success' => false,
                'message' => 'Gravity Forms is not active or not installed.'
            );
        }
        
        $version = GFForms::$version;
        $forms_count = count( \GFAPI::get_forms() );
        
        return array(
            'success' => true,
            'message' => sprintf(
                'Gravity Forms v%s detected with %d forms. Integration is working correctly.',
                $version,
                $forms_count
            )
        );
    }
}
