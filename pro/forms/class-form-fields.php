<?php
/**
 * Form Fields
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nexus Form Fields Class
 */
class Nexus_Form_Fields {

	/**
	 * Render Field
	 */
	public static function render_field( $field ) {
		$type        = isset( $field['type'] ) ? $field['type'] : 'text';
		$label       = isset( $field['label'] ) ? $field['label'] : '';
		$name        = isset( $field['name'] ) ? $field['name'] : '';
		$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
		$required    = isset( $field['required'] ) && $field['required'];
		$options     = isset( $field['options'] ) ? $field['options'] : '';

		if ( empty( $name ) ) {
			return;
		}

		echo '<div class="form-field field-' . esc_attr( $type ) . ( $required ? ' required' : '' ) . '">';

		if ( $label ) {
			echo '<label for="field-' . esc_attr( $name ) . '">';
			echo esc_html( $label );
			if ( $required ) {
				echo ' <span class="required-mark">*</span>';
			}
			echo '</label>';
		}

		switch ( $type ) {
			case 'textarea':
				self::render_textarea( $name, $placeholder, $required );
				break;

			case 'select':
				self::render_select( $name, $options, $required );
				break;

			case 'radio':
				self::render_radio( $name, $options, $required );
				break;

			case 'checkbox':
				self::render_checkbox( $name, $options, $required );
				break;

			case 'file':
				self::render_file( $name, $required );
				break;

			default:
				self::render_input( $type, $name, $placeholder, $required );
		}

		echo '</div>';
	}

	/**
	 * Render Input
	 */
	private static function render_input( $type, $name, $placeholder, $required ) {
		printf(
			'<input type="%s" id="field-%s" name="%s" placeholder="%s"%s>',
			esc_attr( $type ),
			esc_attr( $name ),
			esc_attr( $name ),
			esc_attr( $placeholder ),
			$required ? ' required' : ''
		);
	}

	/**
	 * Render Textarea
	 */
	private static function render_textarea( $name, $placeholder, $required ) {
		printf(
			'<textarea id="field-%s" name="%s" placeholder="%s" rows="5"%s></textarea>',
			esc_attr( $name ),
			esc_attr( $name ),
			esc_attr( $placeholder ),
			$required ? ' required' : ''
		);
	}

	/**
	 * Render Select
	 */
	private static function render_select( $name, $options, $required ) {
		printf(
			'<select id="field-%s" name="%s"%s>',
			esc_attr( $name ),
			esc_attr( $name ),
			$required ? ' required' : ''
		);

		echo '<option value="">' . esc_html__( '-- Select --', 'nexus' ) . '</option>';

		$options_array = explode( "\n", $options );
		foreach ( $options_array as $option ) {
			$option = trim( $option );
			if ( ! empty( $option ) ) {
				printf( '<option value="%s">%s</option>', esc_attr( $option ), esc_html( $option ) );
			}
		}

		echo '</select>';
	}

	/**
	 * Render Radio
	 */
	private static function render_radio( $name, $options, $required ) {
		$options_array = explode( "\n", $options );
		echo '<div class="radio-group">';

		foreach ( $options_array as $index => $option ) {
			$option = trim( $option );
			if ( ! empty( $option ) ) {
				printf(
					'<label><input type="radio" name="%s" value="%s"%s> %s</label>',
					esc_attr( $name ),
					esc_attr( $option ),
					$required && 0 === $index ? ' required' : '',
					esc_html( $option )
				);
			}
		}

		echo '</div>';
	}

	/**
	 * Render Checkbox
	 */
	private static function render_checkbox( $name, $options, $required ) {
		$options_array = explode( "\n", $options );
		echo '<div class="checkbox-group">';

		foreach ( $options_array as $index => $option ) {
			$option = trim( $option );
			if ( ! empty( $option ) ) {
				printf(
					'<label><input type="checkbox" name="%s[]" value="%s"%s> %s</label>',
					esc_attr( $name ),
					esc_attr( $option ),
					$required && 0 === $index ? ' required' : '',
					esc_html( $option )
				);
			}
		}

		echo '</div>';
	}

	/**
	 * Render File
	 */
	private static function render_file( $name, $required ) {
		printf(
			'<input type="file" id="field-%s" name="%s"%s>',
			esc_attr( $name ),
			esc_attr( $name ),
			$required ? ' required' : ''
		);
	}
}
