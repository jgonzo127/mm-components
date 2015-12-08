<?php
/**
 * MIGHTYminnow Components
 *
 * Component: Icon Box
 *
 * @package mm-components
 * @since   1.0.0
 */

/**
 * Build and return the Icon Box component.
 *
 * @since   1.0.0
 *
 * @param   array  $args  The args.
 *
 * @return  string        The HTML.
 */
function mm_icon_box( $args ) {

	$component = 'mm-icon-box';

	// Set our defaults and use them as needed.
	$defaults = array(
		'icon_type'        => 'fontawesome',
		'icon_fontawesome' => '',
		'icon_openiconic'  => '',
		'icon_typicons'    => '',
		'icon_entypo'      => '',
		'icon_linecons'    => '',
		'icon_size'        => 'normal',
		'heading_text'     => '',
		'content'          => '',
		'link'             => '',
		'link_text'        => '',
		'link_title'       => '',
		'link_target'      => '',
	);
	$args = wp_parse_args( (array)$args, $defaults );

	// Get clean param values.
	$icon_type        = $args['icon_type'];
	$icon_fontawesome = $args['icon_fontawesome'];
	$icon_openiconic  = $args['icon_openiconic'];
	$icon_typicons    = $args['icon_typicons'];
	$icon_entypo      = $args['icon_entypo'];
	$icon_linecons    = $args['icon_linecons'];
	$icon_size        = $args['icon_size'];
	$heading_text     = $args['heading_text'];
	$content          = $args['content'];
	$link_url         = $args['link'];
	$link_title       = $args['link_title'];
	$link_target      = $args['link_target'];

	// Handle a VC link array.
	if ( 'url' === substr( $args['link'], 0, 3 ) && function_exists( 'vc_build_link' ) ) {
		$link_array  = vc_build_link( $args['link'] );
		$link_url    = $link_array['url'];
		$link_title  = $link_array['title'];
		$link_target = $link_array['target'];
	}

	// Fix wpautop issues in $content.
	if ( function_exists( 'wpb_js_remove_wpautop' ) ) {
		$content = wpb_js_remove_wpautop( $content, true );
	}

	// Get Mm classes.
	$mm_classes = apply_filters( 'mm_components_custom_classes', '', $component, $args );

	// Add icon size class.
	if ( $icon_size ) {
		$mm_classes .= ' icon-size-' . $icon_size;
	}

	// Get the icon classes.
	switch ( $icon_type ) {
		case 'fontawesome':
			$icon = ! empty( $icon_fontawesome ) ? $icon_fontawesome : 'fa fa-adjust';
			break;
		case 'openiconic':
			$icon = $icon_openiconic;
			break;
		case 'typicons':
			$icon = $icon_typicons;
			break;
		case 'entypo':
			$icon = $icon_entypo;
			break;
		case 'linecons':
			$icon = $icon_linecons;
			break;
		default:
			$icon = 'fa fa-adjust';
	}

	wp_enqueue_style( 'mm-icon-font-' . $icon_type );

	ob_start(); ?>

	<div class="<?php echo esc_attr( $mm_classes ); ?>">

		<i class="mm-icon <?php echo esc_attr( $icon ); ?>"></i>

		<?php if ( ! empty( $atts['heading_text'] ) ) : ?>
			<h3 class="icon-box-heading"><?php echo esc_html( $atts['heading_text'] ); ?></h3>
		<?php endif; ?>

		<?php if ( $content ) : ?>
			<div class="icon-box-content"><?php echo wp_kses_post( $content ); ?></div>
		<?php endif; ?>

		<?php if ( ! empty( $link_text ) && ! empty( $link_url ) ) {
			printf( '<a href="%s" title="%s" target="%s" class="icon-box-link">%s</a>',
				esc_url( $link_url ),
				esc_attr( $link_title ),
				esc_attr( $link_target ),
				esc_html( $atts['link_text'] )
			);
		} ?>

	</div>

	<?php

	return ob_get_clean();
}

add_shortcode( 'mm_icon_box', 'mm_icon_box_shortcode' );
/**
 * Icon Box Shortcode.
 *
 * @since  1.0.0
 *
 * @param   array   $atts     Shortcode attributes.
 * @param   string  $content  Shortcode content.
 *
 * @return  string            Shortcode output.
 */
function mm_icon_box_shortcode( $atts = array(), $content = null ) {

	if ( $content ) {
		$atts['content'] = $content;
	}

	return mm_icon_box( $atts );
}

add_action( 'vc_before_init', 'mm_vc_icon_box' );
/**
 * Visual Composer add-on.
 *
 * @since  1.0.0
 */
function mm_vc_icon_box() {

	$icon_box_sizes = mm_get_icon_box_sizes_for_vc( 'mm-icon_box' );
	$icon_font_types = mm_get_icon_font_types_for_vc( 'mm-icon_box' );

	vc_map( array(
		'name'     => __( 'Icon Box', 'mm-components' ),
		'base'     => 'mm_icon_box',
		'class'    => '',
		'icon'     => MM_COMPONENTS_ASSETS_URL . 'component-icon.png',
		'category' => __( 'Content', 'mm-components' ),
		'params'   => array(
			array(
				'type'    => 'dropdown',
				'heading' => __( 'Icon library', 'mm-components' ),
				'value'   => $icon_font_types,
				'param_name'  => 'icon_type',
				'description' => __( 'Select an icon library.', 'mm-components' ),
			),
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Icon', 'mm-components' ),
				'param_name' => 'icon_fontawesome',
				'settings'   => array(
					'emptyIcon'    => false, // default true, display an "EMPTY" icon?
					'iconsPerPage' => 200, // default 100, how many icons per/page to display
				),
				'dependency'  => array(
					'element' => 'icon_type',
					'value'   => 'fontawesome',
				),
				'description' => __( 'Select an icon from the library.', 'mm-components' ),
			),
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Icon', 'mm-components' ),
				'param_name' => 'icon_openiconic',
				'settings'   => array(
					'emptyIcon'    => false,
					'type'         => 'openiconic',
					'iconsPerPage' => 200,
				),
				'dependency'  => array(
					'element' => 'icon_type',
					'value'   => 'openiconic',
				),
				'description' => __( 'Select an icon from the library.', 'mm-components' ),
			),
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Icon', 'mm-components' ),
				'param_name' => 'icon_typicons',
				'settings'   => array(
					'emptyIcon'    => false,
					'type'         => 'typicons',
					'iconsPerPage' => 200,
				),
				'dependency'  => array(
					'element' => 'icon_type',
					'value'   => 'typicons',
				),
				'description' => __( 'Select an icon from the library.', 'mm-components' ),
			),
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Icon', 'mm-components' ),
				'param_name' => 'icon_entypo',
				'settings'   => array(
					'emptyIcon'    => false,
					'type'         => 'entypo',
					'iconsPerPage' => 300,
				),
				'dependency'  => array(
					'element' => 'icon_type',
					'value'   => 'entypo',
				),
			),
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Icon', 'mm-components' ),
				'param_name' => 'icon_linecons',
				'settings'   => array(
					'emptyIcon'    => false,
					'type'         => 'linecons',
					'iconsPerPage' => 200,
				),
				'dependency'  => array(
					'element' => 'icon_type',
					'value'   => 'linecons',
				),
				'description' => __( 'Select an icon from the library.', 'mm-components' ),
			),
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Icon Box Size', 'mm-components' ),
				'param_name'  => 'icon_size',
				'description' => __( 'Select a general size for the icon box', 'mm-components' ),
				'value'       => $icon_box_sizes,
			),
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Heading', 'mm-components' ),
				'param_name'  => 'heading_text',
				'admin_label' => true,
			),
			array(
				'type'       => 'textarea_html',
				'heading'    => __( 'Paragraph Text', 'mm-components' ),
				'param_name' => 'content',
			),
			array(
				'type'       => 'vc_link',
				'heading'    => __( 'Link URL', 'mm-components' ),
				'param_name' => 'link',
			),
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Link Text', 'mm-components' ),
				'param_name' => 'link_text',
			),
		)
	) );
}

add_action( 'widgets_init', 'mm_components_register_icon_box_widget' );
/**
 * Register the widget.
 *
 * @since  1.0.0
 */
function mm_components_register_icon_box_widget() {

	register_widget( 'mm_icon_box_widget' );
}

/**
 * Hero Banner widget.
 *
 * @since  1.0.0
 */
class Mm_Icon_Box_Widget extends Mm_Components_Widget {

	/**
	 * Global options for this widget.
	 *
	 * @since  1.0.0
	 */
	protected $options;

	/**
	 * Initialize an instance of the widget.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {

		// Set up the options to pass to the WP_Widget constructor.
		$this->options = array(
			'classname'   => 'mm-icon-box-widget',
			'description' => __( 'An Icon Box', 'mm-components' ),
		);

		parent::__construct(
			'mm_icon_box_widget',
			__( 'Mm Icon Box', 'mm-components' ),
			$this->options
		);
	}

	/**
	 * Output the widget.
	 *
	 * @since  1.0.0
	 *
	 * @param  array  $args      The global options for the widget.
	 * @param  array  $instance  The options for the widget instance.
	 */
	public function widget( $args, $instance ) {

		$defaults = array(
			'icon_type'        => 'fontawesome',
			'icon_fontawesome' => '',
			'icon_openiconic'  => '',
			'icon_typicons'    => '',
			'icon_entypo'      => '',
			'icon_linecons'    => '',
			'icon_size'        => 'normal',
			'heading_text'     => '',
			'content'          => '',
			'link'             => '',
			'link_text'        => '',
			'link_target'      => '',
		);

		// Use our instance args if they are there, otherwise use the defaults.
		$instance = wp_parse_args( $instance, $defaults );

		echo $args['before_widget'];

		echo mm_hero_banner( $instance );

		echo $args['after_widget'];
	}

	/**
	 * Output the Widget settings form.
	 *
	 * @since  1.0.0
	 *
	 * @param  array  $instance  The options for the widget instance.
	 */
	public function form( $instance ) {

		$defaults = array(
			'icon_type'        => 'fontawesome',
			'icon_fontawesome' => '',
			'icon_openiconic'  => '',
			'icon_typicons'    => '',
			'icon_entypo'      => '',
			'icon_linecons'    => '',
			'icon_size'        => 'normal',
			'heading_text'     => '',
			'content'          => '',
			'link'             => '',
			'link_text'        => '',
			'link_target'      => '',
		);

		// Use our instance args if they are there, otherwise use the defaults.
		$instance = wp_parse_args( $instance, $defaults );

		$icon_type        = $instance['icon_type'];
		$icon_fontawesome = $instance['icon_fontawesome'];
		$icon_openiconic  = $instance['icon_openiconic'];
		$icon_typicons    = $instance['icon_typicons'];
		$icon_entypo      = $instance['icon_entypo'];
		$icon_linecons    = $instance['icon_linecons'];
		$icon_size        = $instance['icon_size'];
		$heading_text     = $instance['heading_text'];
		$content          = $instance['content'];
		$link             = $instance['link'];
		$link_text        = $instance['link_text'];
		$link_target      = $instance['link_target'];
		$classname        = $this->options['classname'];

		// Icon type.
		$this->field_select(
			__( 'Icon Type', 'mm-components' ),
			__( 'Select an icon library.', 'mm-components' ),
			$classname . '-icon-type widefat',
			'icon_type',
			$icon_type,
			array(
				'fontawesome' => __( 'Font Awesome', 'mm-components' ),
				'openiconic'  => __( 'Open Iconic', 'mm-components' ),
				'typicons'    => __( 'Typicons', 'mm-components' ),
				'entypo'      => __( 'Entypo', 'mm-components' ),
				'linecons'    => __( 'Linecons', 'mm-components' ),
			)
		);

		// Icon Font Awesome.
		$this->field_select(
			__( 'Icon Font Awesome', 'mm-components' ),
			__( 'Select an icon.', 'mm-components' ),
			$classname . '-icon-fontawesome widefat',
			'icon_fontawesome',
			$icon_fontawesome,
			mm_components_load_bfa()
		);

		// Icon Font Awesome.
		$this->field_select(
			__( 'Icon Font Awesome', 'mm-components' ),
			__( 'Select an icon.', 'mm-components' ),
			$classname . '-icon-fontawesome widefat',
			'icon_openiconic',
			$icon_openiconic,
			mm_components_load_bfa()
		);

		// Icon Typicons.
		$this->field_select(
			__( 'Icon Typicons', 'mm-components' ),
			__( 'Select an icon.', 'mm-components' ),
			$classname . '-icon-typicons widefat',
			'icon_typicons',
			$icon_typicons,
			mm_components_load_bfa()
		);

		// Icon Entypo.
		$this->field_select(
			__( 'Icon Entypo', 'mm-components' ),
			__( 'Select an icon.', 'mm-components' ),
			$classname . '-icon-entypo widefat',
			'icon_entypo',
			$icon_entypo,
			mm_components_load_bfa()
		);

		// Icon Linecons.
		$this->field_select(
			__( 'Icon Linecons', 'mm-components' ),
			__( 'Select an icon.', 'mm-components' ),
			$classname . '-icon-linecons widefat',
			'icon_linecons',
			$icon_linecons,
			mm_components_load_bfa()
		);

		// Icon Size.
		$this->field_select(
			__( 'Icon Size', 'mm-components' ),
			__( 'Select a general size for the icon box.', 'mm-components' ),
			$classname . '-icon-size widefat',
			'icon_size',
			$icon_size,
			mm_get_icon_box_sizes( 'mm-icon-box' )
		);

		// Heading Text.
		$this->field_text(
			__( 'Heading', 'mm-components' ),
			__( '', 'mm-components' ),
			$classname . '-heading widefat',
			'heading_text',
			$heading_text
		);

		// Content.
		$this->field_textarea(
			__( 'Paragraph Text', 'mm-components' ),
			__( '', 'mm-components' ),
			$classname . '-content widefat',
			'content',
			$content
		);

		// Link URL.
		$this->field_text(
			__( 'Link URL', 'mm-components' ),
			'',
			$classname . '-link widefat',
			'link',
			$link
		);

		// Link Text.
		$this->field_text(
			__( 'Link Text', 'mm-components' ),
			'',
			$classname . '-link-text widefat',
			'link_text',
			$link_text
		);

		// Link target.
		$this->field_select(
			__( 'Link Target', 'mm-components' ),
			'',
			$classname . '-link-target widefat',
			'link_target',
			$link_target,
			mm_get_link_targets( 'mm-icon-box' )
		);

	}

	/**
	 * Update the widget settings.
	 *
	 * @since   1.0.0
	 *
	 * @param   array  $new_instance  The new settings for the widget instance.
	 * @param   array  $old_instance  The old settings for the widget instance.
	 *
	 * @return  array                 The sanitized settings.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance                      = $old_instance;
		$instance['icon_type']         = sanitize_text_field( $new_instance['icon_type'] );
		$instance['icon_fontawesome']  = sanitize_text_field( $new_instance['icon_fontawesome'] );
		$instance['icon_openiconic']   = sanitize_text_field( $new_instance['icon_openiconic'] );
		$instance['icon_typicons']     = sanitize_text_field( $new_instance['icon_typicons'] );
		$instance['icon_entypo']       = sanitize_text_field( $new_instance['icon_entypo'] );
		$instance['icon_linecons']     = sanitize_text_field( $new_instance['icon_linecons'] );
		$instance['icon_size']         = sanitize_text_field( $new_instance['icon_size'] );
		$instance['heading_text']      = sanitize_text_field( $new_instance['heading_text'] );
		$instance['content']           = wp_kses_post( $new_instance['content'] );
		$instance['link']    		   = sanitize_text_field( $new_instance['link'] );
		$instance['link_text']         = sanitize_text_field( $new_instance['link_text'] );
		$instance['link_target']       = sanitize_text_field( $new_instance['link_target'] );
	

		return $instance;
	}
}