<?php
/**
 * Plugin Name: MIGHTYminnow Components
 * Plugin URI: http://mightyminnow.com
 * Description: Custom components and functionality for WordPress.
 * Version: 1.0.0
 * Author: MIGHTYminnow Web Studio
 * Author URI: http://mightyminnow.com
 * License: GPL2+
 * Text Domain: mm-components
 * Domain Path: /languages
 */

define( 'MM_COMPONENTS_VERSION', '1.0.0' );
define( 'MM_COMPONENTS_PATH', plugin_dir_path( __FILE__ ) );
define( 'MM_COMPONENTS_URL', plugin_dir_url( __FILE__ ) );
define( 'MM_COMPONENTS_ASSETS_URL', MM_COMPONENTS_URL . 'assets/' );

add_action( 'plugins_loaded', 'mm_components_startup' );
/**
 * Start the plugin.
 *
 * @since    1.0.0
 */
function mm_components_startup() {

	// Set up text domain.
	load_plugin_textdomain( 'mm-components', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );

	// Set up array of all the components.
	$mm_components = array(
		'blockquote'         => __( 'Blockquote', 'mm-components' ),
		'button'             => __( 'Button', 'mm-components' ),
		'countdown'          => __( 'Countdown', 'mm-components' ),
		'custom-heading'     => __( 'Custom Heading', 'mm-components' ),
		'expandable-content' => __( 'Expandable Content', 'mm-components' ),
		'hero-banner'        => __( 'Hero Banner', 'mm-components' ),
		'highlight-box'      => __( 'Highlight Box', 'mm-components' ),
		'icon-box'           => __( 'Icon Box', 'mm-components' ),
		'image-grid'         => __( 'Image Grid', 'mm-components' ),
		'logo-strip'         => __( 'Logo Strip', 'mm-components' ),
		'polaroid'           => __( 'Polaroid', 'mm-components' ),
		'polaroid-2'         => __( 'Polaroid 2', 'mm-components' ),
		'posts'              => __( 'Posts', 'mm-components' ),
		'twitter-feed'       => __( 'Twitter Feed', 'mm-components' ),
	);

	// Allow the theme to turn off specific components.
	$mm_active_components = apply_filters( 'mm_components_active_components', $mm_components );

	// Store array of active components as a global.
	$GLOBALS['mm_active_components'] = $mm_active_components;

	// Include general functionality.
	require_once MM_COMPONENTS_PATH . 'functions.php';

	// Include active components.
	if ( array_key_exists( 'blockquote', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/blockquote.php';
	}
	if ( array_key_exists( 'button', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/button.php';
	}
	if ( array_key_exists( 'countdown', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/countdown/countdown.php';
	}
	if ( array_key_exists( 'custom-heading', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/custom-heading.php';
	}
	if ( array_key_exists( 'expandable-content', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/expandable-content.php';
	}
	if ( array_key_exists( 'hero-banner', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/hero-banner.php';
	}
	if ( array_key_exists( 'highlight-box', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/highlight-box.php';
	}
	if ( array_key_exists( 'icon-box', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/icon-box.php';
	}
	if ( array_key_exists( 'image-grid', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/image-grid.php';
	}
	if ( array_key_exists( 'logo-strip', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/logo-strip.php';
	}
	if ( array_key_exists( 'polaroid', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/polaroid.php';
	}
	if ( array_key_exists( 'polaroid-2', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/polaroid-2.php';
	}
	if ( array_key_exists( 'posts', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/posts.php';
	}
	if ( array_key_exists( 'twitter-feed', $mm_active_components ) ) {
		require_once MM_COMPONENTS_PATH . 'components/twitter-feed.php';
	}

	// Include Visual Composer Integration if VC is activated.
	if ( defined( 'WPB_VC_VERSION' ) ) {
		require_once MM_COMPONENTS_PATH . 'integrations/visual-composer/vc-functions.php';
	}

	// Load front-end scripts and styles.
	add_action( 'wp_enqueue_scripts', 'mm_components_scripts_and_styles' );
}

/**
 * Enqueue front-end scripts and styles.
 *
 * @since  1.0.0
 */
function mm_components_scripts_and_styles() {

	// General styles.
	wp_enqueue_style(
		'mm-components-css',
		MM_COMPONENTS_URL . 'css/mm-components-public.css',
		array(),
		MM_COMPONENTS_VERSION
	);

	// General scripts.
	wp_enqueue_script(
		'mm-components-js',
		MM_COMPONENTS_URL . 'js/mm-components-public.js',
		array( 'jquery' ),
		MM_COMPONENTS_VERSION,
		true
	);
}
