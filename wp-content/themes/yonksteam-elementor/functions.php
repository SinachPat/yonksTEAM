<?php
/**
 * YonksTEAM Elementor theme.
 *
 * @package YonksTEAM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yonksteam_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'elementor' );
	add_theme_support( 'wpforms-styles' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 280,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation', 'yonksteam' ),
			'footer'  => __( 'Footer Navigation', 'yonksteam' ),
		)
	);

	load_theme_textdomain( 'yonksteam', get_template_directory() . '/languages' );
	add_image_size( 'yonksteam-card', 720, 400, true );
}
add_action( 'after_setup_theme', 'yonksteam_setup' );

function yonksteam_content_width() {
	$GLOBALS['content_width'] = 1140;
}
add_action( 'after_setup_theme', 'yonksteam_content_width', 0 );

function yonksteam_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Footer', 'yonksteam' ),
			'id'            => 'footer-1',
			'description'   => __( 'Optional widgets under the footer nav.', 'yonksteam' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'yonksteam_widgets_init' );

function yonksteam_enqueue() {
	$version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'yonksteam-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,500&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'yonksteam-style', get_stylesheet_uri(), array(), $version );
	wp_enqueue_style(
		'yonksteam-theme',
		get_template_directory_uri() . '/assets/css/theme.css',
		array( 'yonksteam-style', 'yonksteam-fonts' ),
		$version
	);
	wp_enqueue_style(
		'yonksteam-pages',
		get_template_directory_uri() . '/assets/css/pages.css',
		array( 'yonksteam-theme' ),
		$version
	);

	wp_enqueue_script(
		'yonksteam-navigation',
		get_template_directory_uri() . '/assets/js/navigation.js',
		array(),
		$version,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'yonksteam_enqueue' );

function yonksteam_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.googleapis.com' );
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'yonksteam_resource_hints', 10, 2 );

function yonksteam_register_elementor_locations( $manager ) {
	if ( method_exists( $manager, 'register_all_core_location' ) ) {
		$manager->register_all_core_location();
		return;
	}
	$manager->register_location( 'header' );
	$manager->register_location( 'footer' );
	$manager->register_location( 'single' );
	$manager->register_location( 'archive' );
}
add_action( 'elementor/theme/register_locations', 'yonksteam_register_elementor_locations' );

function yonksteam_is_elementor_page() {
	if ( ! class_exists( '\Elementor\Plugin' ) || ! is_singular() ) {
		return false;
	}
	$document = \Elementor\Plugin::$instance->documents->get( get_the_ID() );
	return $document && $document->is_built_with_elementor();
}

function yonksteam_using_elementor_header() {
	return function_exists( 'elementor_location_exits' ) && elementor_location_exits( 'header', true );
}

function yonksteam_body_classes( $classes ) {
	$classes[] = 'yonksteam';

	if ( ! yonksteam_using_elementor_header() ) {
		$classes[] = 'has-fixed-header';
	}

	if ( yonksteam_is_elementor_page() ) {
		$classes[] = 'is-elementor-page';
	}

	return $classes;
}
add_filter( 'body_class', 'yonksteam_body_classes' );

function yonksteam_elementor_notice() {
	if ( did_action( 'elementor/loaded' ) || ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	$url = admin_url( 'plugin-install.php?s=elementor&tab=search&type=term' );
	echo '<div class="notice notice-info is-dismissible"><p>';
	printf(
		wp_kses_post( __( 'YonksTEAM Elementor is built for the Elementor page builder. <a href="%s">Install Elementor</a> to edit pages visually.', 'yonksteam' ) ),
		esc_url( $url )
	);
	echo '</p></div>';
}
add_action( 'admin_notices', 'yonksteam_elementor_notice' );

function yonksteam_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'yonksteam_header',
		array(
			'title'    => __( 'Header CTA', 'yonksteam' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_setting(
		'yonksteam_cta_label',
		array(
			'default'           => __( 'Start the Conversation', 'yonksteam' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'yonksteam_cta_label',
		array(
			'label'   => __( 'Button label', 'yonksteam' ),
			'section' => 'yonksteam_header',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'yonksteam_cta_url',
		array(
			'default'           => '/contact',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'yonksteam_cta_url',
		array(
			'label'   => __( 'Button URL', 'yonksteam' ),
			'section' => 'yonksteam_header',
			'type'    => 'url',
		)
	);

	$wp_customize->add_setting(
		'yonksteam_footer_tagline',
		array(
			'default'           => __( 'Building ♾️ WeOwnNet 🌐 ecosystem', 'yonksteam' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'yonksteam_footer_tagline',
		array(
			'label'   => __( 'Footer tagline', 'yonksteam' ),
			'section' => 'title_tagline',
			'type'    => 'text',
		)
	);
}
add_action( 'customize_register', 'yonksteam_customize_register' );

function yonksteam_cta_url() {
	$url = get_theme_mod( 'yonksteam_cta_url', '/contact' );
	if ( $url && 0 === strpos( $url, '/' ) ) {
		return home_url( $url );
	}
	return $url ? $url : home_url( '/contact' );
}

function yonksteam_cta_label() {
	$label = get_theme_mod( 'yonksteam_cta_label', '' );
	return $label ? $label : __( 'Start the Conversation', 'yonksteam' );
}

function yonksteam_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'yonksteam_excerpt_more' );
