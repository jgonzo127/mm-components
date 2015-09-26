<?php
/**
 * Mm Components Widget Class.
 *
 * This class is designed for sub-classing. It extends the main WP_Widget
 * class with functions to output various field types.
 *
 * @since  1.0.0
 */
class Mm_Components_Widget extends WP_Widget {

	/**
	 * Initialize an instance of the parent class.
	 *
	 * @since  1.0.0
	 */
	public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() ) {

		parent::__construct(
			$id_base,
			$name,
			$widget_options,
			$control_options
		);
	}

	/**
	 * Output a text input.
	 *
	 * @since  1.0.0
	 */
	public function field_text( $label = '', $classes = '', $key = '', $value = '' ) {

		echo '<p><label>' . esc_html( $label ) . '</label>';

		printf(
			'<input type="text" class="%s" name="%s" value="%s" />',
			$classes,
			$this->get_field_name( $key ),
			$value
		);

		echo '</p>';
	}

	/**
	 * Output a textarea input.
	 *
	 * @since  1.0.0
	 */
	public function field_textarea( $label = '', $classes = '', $key = '', $value = '', $rows = '4', $cols = '4' ) {

		echo '<p><label>' . esc_html( $label ) . '</label>';

		printf(
			'<textarea class="%s" name="%s" rows="%s" cols="%s">%s</textarea>',
			$classes,
			$this->get_field_name( $key ),
			$rows,
			$cols,
			$value
		);

		echo '</p>';
	}

	/**
	 * Output a select dropdown.
	 *
	 * @since  1.0.0
	 */
	public function field_select( $label = '', $classes = '', $key = '', $value = '', $options = array() ) {

		echo '<p><label>' . esc_html( $label ) . '</label>';

		printf(
			'<select class="%s" name="%s">',
			$classes,
			$this->get_field_name( $key )
		);

		// Test whether we have an associative or indexed array.
		if ( array_values( $options ) === $options ) {

			// We have an indexed array.
			foreach ( $options as $option ) {

				printf(
					'<option value="%s" %s>%s</option>',
					$option,
					selected( $value, $option, false ),
					$option
				);
			}

		} else {

			// We have an associative array.
			foreach ( $options as $option_value => $option_display_name ) {

				printf(
					'<option value="%s" %s>%s</option>',
					$option_value,
					selected( $value, $option_value, false ),
					$option_display_name
				);
			}
		}

		echo '</select>';

		echo '</p>';
	}
	
	/**
	 * Outputs a checkbox input field.
	 *
	 * @since  1.0.0
	 */
	public function field_checkbox( $label = '', $classes = '', $key = '', $value = '' ) {

		if ( mm_true_or_false( $value ) ) {
			$val = 1;
		} else {
			$val = 0;
		}

		echo '<p>';

			printf(
				'<input type="checkbox" class="%s" name="%s" value="1" %s /> <label class="%s">%s</label><br />',
				$classes,
				$this->get_field_name( $key ),
				checked( $val, 1, false ),
				'radio-label',
				$label
			);

		echo '</p>';

	}

	/**
	 * Output a group of radio button input elements.
	 *
	 * @since  1.0.0
	 */
	public function field_radio( $label = '', $classes = '', $key = '', $value = '', $options = array() ) {

		echo '<p><label class="radio-group-label">' . esc_html( $label ) . '</label><br />';

		// Test whether we have an associative or indexed array.
		if ( array_values( $options ) === $options ) {

			// We have an indexed array.
			foreach ( $options as $option ) {

				printf(
					'<input type="radio" class="%s" name="%s" value="%s" %s /> <label class="%s">%s</label><br />',
					$classes,
					$this->get_field_name( $key ),
					$option,
					checked( $value, $option, false ),
					'radio-option-label',
					$option
				);
			}

		} else {

			// We have an associative array.
			foreach ( $options as $option_value => $option_display_name ) {

				printf(
					'<input type="radio" class="%s" name="%s" value="%s" %s /> <label class="%s">%s</label><br />',
					$classes,
					$this->get_field_name( $key ),
					$option_value,
					checked( $value, $option_value, false ),
					'radio-option-label',
					$option_display_name
				);
			}
		}

		echo '</p>';

	}

}


