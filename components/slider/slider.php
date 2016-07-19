<?php
/**
 * MIGHTYminnow Components
 *
 * Component: Slider
 *
 * @package mm-components
 * @since   1.0.0
 */

/**
 * Build and return the Slider component.
 *
 * @since   1.0.0
 *
 * @param   array  $args  The args.
 *
 * @return  string        The HTML.
 */
function mm_slider( $args ) {

	$component  = 'mm-slider';

	// Set our defaults and use them as needed.
	$defaults = array(
		'image_ids'         => '',
		'slider_content'    => '',
		'loop'              => true,
		'autoplay'          => true,
		'duration'          => 3000,
		'prev_next'         => true,
		'page_dots'         => true,
		'slide_class'       => '',
	);
	$args = wp_parse_args( (array)$args, $defaults );

	// Get clean param values.
	$image_ids      = $args['image_ids'];
	$slider_content = $args['slider_content'];
	$loop           = mm_true_or_false( $args['loop'] );
	$autoplay       = mm_true_or_false( $args['autoplay'] );
	$prev_next      = mm_true_or_false( $args['prev_next'] );
	$page_dots      = mm_true_or_false( $args['page_dots'] );
	$duration       = sanitize_text_field( $args['duration'] );


	// Enqueue flickity.
	wp_enqueue_script( 'mm-flickity' );
	wp_enqueue_style( 'mm-flickity' );

	$content = $slider_content;

	if ( strpos( $content, '<' ) ) {

		/* We have HTML */
		$inner_output = ( function_exists( 'wpb_js_remove_wpautop' ) ) ? wpb_js_remove_wpautop( $content, true ) : $content;

	} elseif ( mm_is_base64( $content ) ) {

		/* We have a base64 encoded string */
		$inner_output = rawurldecode( base64_decode( $content ) );

	} else {

		/* We have a non-HTML string */
		$inner_output = $content;
	}

	$slider_options = array(
		'cellSelector'    => '.mm-carousel-item',
		'pageDots'        => $page_dots,
		'prevNextButtons' => $prev_next,
		'autoPlay'        => $duration,
		'wrapAround'      => $loop,

	);

	// Convert args to data-* attributes.
	foreach ( $slider_options as $slider_option_key => $slider_option_value ) {
		if ( ! empty( $slider_option_value ) ) {
	        $slider_atts[] = '"' . $slider_option_key . '": "' . $slider_option_value . '"';
	    }
	}

	$slider_atts = esc_attr( implode( ', ', $slider_atts ) );

	// Get clean param values.
	$image_ids = ( is_array( $image_ids ) ) ? $image_ids : explode( ',', str_replace( ' ', '', $image_ids ) );

	// Get Mm classes.
	$mm_classes = apply_filters( 'mm_components_custom_classes', '', $component, $args );
	$mm_classes .= ' mm-carousel';

	ob_start() ?>

	<div class="<?php echo esc_attr( $mm_classes ); ?>" data-flickity='{ <?php echo $slider_atts; ?> }'>

	<?php
		if( ! empty( $args['image_ids'] ) ) {
			foreach ( $image_ids as $image_id ) {

				$image = wp_get_attachment_image_src( $image_id, 'full' );

				if( is_wp_error( $image ) && is_array( $image ) ) {
					continue;
				}

				printf(
					'<div class="mm-slider-image mm-carousel-item">%s</div>',
					wp_get_attachment_image( $image_id, 'large' )
				);
			}
		}
	?>

	<div class="mm-carousel-content" ><?php echo do_shortcode( $inner_output ); ?></div>

	</div>

	<?php

	return ob_get_clean();
}

add_shortcode( 'mm_slider', 'mm_slider_shortcode' );
/**
 * Slider shortcode.
 *
 * @since   1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 * @param   string  $content  Shortcode content.
 *
 * @return  string        Shortcode output.
 */
function mm_slider_shortcode( $atts = array(), $content = null ) {

	if ( $content ) {
		$atts['slider_content'] = $content;
	}

	return mm_slider( $atts );
}

add_action( 'vc_before_init', 'mm_vc_slider' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_slider() {

	vc_map( array(
		'name'         => __( 'Slider', 'mm-components' ),
		'base'         => 'mm_slider',
		'icon'         => MM_COMPONENTS_ASSETS_URL . 'component-icon.png',
		'category'     => __( 'Content', 'mm-components' ),
		'as_parent'    => array( 'except' => '' ),
		'is_container' => true,
		'params'   => array(
			array(
				'type'        => 'attach_images',
				'heading'     => __( 'Images', 'mm-components' ),
				'param_name'  => 'image_ids',
				'description' => __( 'The bigger the image size, the better.', 'mm-components' ),
				'value'       => '',
			),
		),
	'js_view' => 'VcColumnView'
	) );
}

add_action( 'register_shortcode_ui', 'mm_components_mm_slider_shortcode_ui' );
/**
 * Register UI for Shortcake.
 *
 * @since  1.0.0
 */
function mm_components_mm_slider_shortcode_ui() {

	if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
		return;
	}

	shortcode_ui_register_for_shortcode(
		'mm_slider',
		array(
			'label'         => esc_html__( 'Mm Slider', 'mm-components' ),
			'listItemImage' => MM_COMPONENTS_ASSETS_URL . 'component-icon.png',
			'attrs'         => array(
			),
		)
	);
}

// This is necessary to make any element that wraps other elements work.
if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_MM_Slider extends WPBakeryShortCodesContainer {
	}
}
