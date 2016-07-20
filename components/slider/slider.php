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
		'image_ids'           => '',
		'background_position' => "center bottom",
		'slider_content'      => '',
		'min-height'          => 360,
		'loop'                => true,
		'full_height'         => false,
		'autoplay'            => true,
		'duration'            => 6000,
		'adaptive_height'     => false,
		'nav_arrows'          => true,
		'page_dots'           => true,
		'slide_class'         => '',
		'draggable'           => true,
		'set_gallery_size'    => false,
	);
	$args = wp_parse_args( (array)$args, $defaults );

	// Get clean param values.
	$image_ids        = $args['image_ids'];
	$background_position = $args['background_position'];
	$slider_content   = $args['slider_content'];
	$min_height       = $args['min-height'];
	$full_height      = mm_true_or_false( $args['full_height'] );
	$loop             = mm_true_or_false( $args['loop'] );
	$autoplay         = mm_true_or_false( $args['autoplay'] );
	$adaptive_height  = mm_true_or_false( $args['adaptive_height'] );
	$nav_arrows       = mm_true_or_false( $args['nav_arrows'] );
	$page_dots        = mm_true_or_false( $args['page_dots'] );
	$duration         = (int)$args['duration'];
	$draggable        = mm_true_or_false( $args['draggable'] );
	$set_gallery_size = mm_true_or_false( $args['set_gallery_size'] );

	$wrap_styles = array();


	// Enqueue flickity.
	wp_enqueue_script( 'mm-flickity' );
	wp_enqueue_style( 'mm-flickity' );

	// Get clean param values.
	$image_ids = ( is_array( $image_ids ) ) ? $image_ids : explode( ',', str_replace( ' ', '', $image_ids ) );

	// Get Mm classes.
	$mm_classes = apply_filters( 'mm_components_custom_classes', '', $component, $args );
	$mm_classes .= ' mm-carousel';

	if ( $full_height ) {
		$mm_classes .= ' mm-full-window-height';
		$wrap_styles[] = '';
	} else {
		if ( 1 < (int)$min_height ) {
			$wrap_styles[] = 'height: ' . (int)$min_height . 'px;';
		}
	}

	if ( $adaptive_height ) {
		$wrap_styles[] = '';
		$set_gallery_size = true;
	}

	$wrap_styles[]= "background-position: $background_position;";

	$wrap_styles = implode( ' ', $wrap_styles );

	$slider_options = array(
		'cellSelector'    => '.mm-carousel-item',
		'pageDots'        => $page_dots,
		'prevNextButtons' => $nav_arrows,
		'adaptiveHeight'  => $adaptive_height,
		'autoPlay'        => $duration,
		'wrapAround'      => $loop,
		'draggable'       => $draggable,
		'setGallerySize'  => $set_gallery_size,
	);

	$slider_atts = json_encode( $slider_options );

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

	if ( ! $autoplay ) {
		$duration = false;
	}

	ob_start() ?>

	<div class="<?php echo esc_attr( $mm_classes ); ?>" style="<?php echo esc_attr( $wrap_styles ); ?>" data-flickity=' <?php echo esc_attr( $slider_atts ); ?> '>

	<?php
		if( ! empty( $args['image_ids'] ) ) {
			foreach ( $image_ids as $image_id ) {

				$image = wp_get_attachment_image_src( $image_id, 'full' );

				if( is_wp_error( $image ) || ! is_array( $image ) ) {
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
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Full Height', 'mm-components' ),
				'param_name'  => 'full_height',
				'description' => __( 'Slideshow images will stretch to fit slideshow container.', 'mm-components' ),
				'value'       => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Draggable', 'mm-components' ),
				'param_name'  => 'draggable',
				'std'         => 1,
				'description' => __( 'Allow the slideshow to be navigated with a click and drag.', 'mm-components' ),
				'value'       => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Wrap Slideshow?', 'mm-components' ),
				'param_name'  => 'loop',
				'std'         => 1,
				'description' => __( 'Allow the slideshow to wrap around to the first slide.', 'mm-components' ),
				'value'       => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Autoplay Slideshow?', 'mm-components' ),
				'param_name'  => 'autoplay',
				'std'         => 1,
				'description' => __( '', 'mm-components' ),
				'value'       => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Autoplay Duration', 'mm-components' ),
				'param_name'  => 'duration',
				'description' => __( '', 'mm-components' ),
				'value'       => 6000,
				'dependency' => array(
					'element'   => 'autoplay',
					'not_empty' => true,
				),
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Adaptive Height', 'mm-components' ),
				'param_name'  => 'adaptive_height',
				'description' => __( 'The slideshow height will change depending on the height of the current content.', 'mm-components' ),
				'value'       => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Show navigation arrows?', 'mm-components' ),
				'param_name'  => 'nav_arrows',
				'std'         => 1,
				'description' => __( '', 'mm-components' ),
				'value'       => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
			),
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Enable navigation dots?', 'mm-components' ),
				'param_name'  => 'page_dots',
				'std'         => 1,
				'description' => __( '', 'mm-components' ),
				'value'       => array(
					__( 'Yes', 'mm-components' ) => 1,
				),
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
				array(
					'label'       => esc_html__( 'Images', 'mm-components' ),
					'attr'        => 'image_ids',
					'type'        => 'attachment',
					'libraryType' => array( 'image' ),
					'addButton'   => esc_html__( 'Select Image', 'mm-components' ),
					'frameTitle'  => esc_html__( 'Select Image', 'mm-components' ),
				),
				array(
					'label'       => esc_html__( 'Draggable', 'mm-slider' ),
					'description' => esc_html__( 'Allow the slideshow to be navigated with a click and drag.', 'mm-components' ),
					'attr'        => 'draggable',
					'type'        => 'checkbox',
				),
				array(
					'label'       => esc_html__( 'Full Height', 'mm-slider' ),
					'description' => esc_html__( 'Slideshow images will stretch to fit slideshow container.', 'mm-components' ),
					'attr'        => 'full_height',
					'type'        => 'checkbox',
				),
				array(
					'label'       => esc_html__( 'Wrap Slideshow?', 'mm-slider' ),
					'description' => esc_html__( 'Allow the slideshow to wrap around to the first slide.', 'mm-components' ),
					'attr'        => 'loop',
					'type'        => 'checkbox',
				),
				array(
					'label'       => esc_html__( 'Autoplay Slideshow?', 'mm-slider' ),
					'attr'        => 'autoplay',
					'type'        => 'checkbox',
				),
				array(
					'label'       => esc_html__( 'Autoplay Duration', 'mm-components' ),
					'attr'        => 'duration',
					'type'        => 'text',
				),
				array(
					'label'       => esc_html__( 'Adaptive Height', 'mm-slider' ),
					'description' => esc_html__( 'The slideshow height will change depending on the height of the current content.', 'mm-components' ),
					'attr'        => 'adaptive_height',
					'type'        => 'checkbox',
				),
				array(
					'label'       => esc_html__( 'Show navigation arrows?', 'mm-slider' ),
					'attr'        => 'nav_arrows',
					'type'        => 'checkbox',
				),
				array(
					'label'       => esc_html__( 'Enable navigation dots?', 'mm-slider' ),
					'attr'        => 'page_dots',
					'type'        => 'checkbox',
				),
			),
		)
	);
}

// This is necessary to make any element that wraps other elements work.
if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_MM_Slider extends WPBakeryShortCodesContainer {
	}
}
