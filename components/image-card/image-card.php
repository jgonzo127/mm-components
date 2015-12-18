<?php
/**
 * MIGHTYminnow Components
 *
 * Component: Image Grid
 *
 * @package mm-components
 * @since   1.0.0
 */

add_shortcode( 'mm_image_card', 'mm_image_card_shortcode' );

/**
 * Output Image Card.
 *
 * @since  1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_image_card( $args ) {

	$component = 'mm-image-card';

	$defaults = array(
		'title'         => '',
		'style'         => '',
		'max_in_row'    => '',
		'el_class'      => '',
		'caption'       => '',
		'caption_color' => '',
		'image'         => '',
		'author_image'  => '',
		'link'          => '',
		'link_text'     => __( 'Visit campaign', 'mm-components' ),
		'link_target'   => '',
		'banner_text'   => '',
		'content'       => '',
	);

	$args = wp_parse_args( (array)$args, $defaults );

	// Get clean param values.
	$title         = $args['title'];
	$style         = $args['style'];
	$option        = $args['option'];
	$caption       = $args['caption'];
	$caption_color = $args['caption_color'];
	$image         = $args['image'];
	$author_image  = $args['author_image'];
	$link          = $args['link'];
	$link_text     = $args['link_text'];
	$link_target   = $args['link_target'];
	$banner_text   = $args['banner_text'];
	$content       = $args['content'];

	$style = ( '' !== $atts['style'] ) ? esc_attr( $atts['style'] ) : 'style-full-image';

	// Set a global style variable to pass to the nested Image Grid Image components.
	global $mm_image_card_style;

	$mm_image_card_style = $style;

	// Fix wpautop issues in $content.
	if ( function_exists( 'wpb_js_remove_wpautop' ) ) {
		$content = wpb_js_remove_wpautop( $content, true );
	}

	// Get Mm classes.
	$mm_classes = apply_filters( 'mm_components_custom_classes', '', $component, $args );

	ob_start(); ?>

	<div class="<?php echo esc_attr( $mm_classes ); ?>">

		<?php if ( $title ) : ?>
			<h4><?php echo wp_kses_post( $title ); ?></h4>
		<?php endif; ?>

		<?php if ( $content ) : ?>
			<?php echo do_shortcode( $content ); ?>
		<?php endif; ?>

	</div>

	<?php

	// Reset global style variable in case of multiple Image Grids on a single page.
	$mm_image_card_style = '';

	return ob_get_clean();
}

add_shortcode( 'mm_image_card', 'mm_image_card_shortcode' );
/**
 * Hero Banner shortcode.
 *
 * @since   1.0.0
 *
 * @param   array  $atts  Shortcode attributes.
 *
 * @return  string        Shortcode output.
 */
function mm_image_card_shortcode( $atts = array(), $content = null ) {

	if ( $content ) {
		$atts['content'] = $content;
	}

	return mm_image_card( $atts );
}

add_action( 'vc_before_init', 'mm_vc_image_card' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_image_card() {

	// Image grid container.
	vc_map( array(
		'name'     => __( 'Image Card', 'mm-components' ),
		'base'     => 'mm_image_card',
		'icon'     => MM_COMPONENTS_ASSETS_URL . 'image-grid-icon.png',
		'category' => __( 'Content', 'mm-components' ),
		'params'   => array(
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Title', 'mm-components' ),
				'param_name' => 'title',
			),
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Style', 'mm-components' ),
				'param_name' => 'style',
				'value'      => array(
					__( 'Select an Image Style', 'mm-components' ) => '',
					__( 'Full Image', 'mm-components ')            => 'style-full-image',
					__( 'Thumbnail/Text Card', 'mm-components ')   => 'style-thumbnail-text-card',
					__( 'Polaroid', 'mm-components ')              => 'style-polaroid',
					__( 'Polaroid 2', 'mm-components ')            => 'style-polaroid-2',
				),
			),
			array(
				'type'                   => 'attach_image',
				'heading'                => __( 'Main Image', 'mm-components' ),
				'param_name'             => 'image',
				'value'                  => '',
				'mm_image_size_for_desc' => 'polaroid',
			),
			array(
				'type'                   => 'attach_image',
				'heading'                => __( 'Author Image', 'mm-components' ),
				'param_name'             => 'author_image',
				'value'                  => '',
				'mm_image_size_for_desc' => 'thumbnail',
			),
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Link Text', 'mm-components' ),
				'param_name'  => 'link_text',
				'value'       => '',
				'description' => __( 'Defaults to "Visit campaign".', 'mm-components' )
			),
			array(
				'type'       => 'vc_link',
				'heading'    => __( 'Link URL', 'mm-components' ),
				'param_name' => 'link',
				'value'      => '',
			),
			array(
				'type'       => 'textarea_html',
				'heading'    => __( 'Text', 'mm-components' ),
				'param_name' => 'content',
			)
		),
		'js_view' => 'VcColumnView'
	) );
}
