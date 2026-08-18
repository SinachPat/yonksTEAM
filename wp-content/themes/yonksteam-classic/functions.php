<?php
/**
 * Theme Name: YonksTEAM Classic
 * Theme URI: https://yonks.team
 * Author: Jason & Tyler Younker
 * Author URI: https://yonks.team
 * Description: Classic theme for YonksTEAM. Fully compatible with Elementor, WPForms, Yoast SEO, FluentCRM, and FluentCart.
 * Version: 1.0.0
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * License: GPL v2 or later
 * Text Domain: yonksteam
 */

// Theme setup
function yonksteam_classic_setup() {
    // Title tag support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    
    // Register nav menus
    register_nav_menus([
        'primary' => __('Primary Navigation', 'yonksteam'),
        'footer'  => __('Footer Navigation', 'yonksteam'),
    ]);
    
    // Load text domain
    load_theme_textdomain('yonksteam');
}
add_action('after_setup_theme', 'yonksteam_classic_setup');

// Enqueue styles and scripts
function yonksteam_classic_enqueue() {
    $theme = wp_get_theme();
    $version = $theme->get('Version');
    
    // Google Fonts
    wp_enqueue_style('yonksteam-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,500&display=swap', [], null);
    
    // Main stylesheet
    wp_enqueue_style('yonksteam-classic-style', get_stylesheet_uri(), [], $version);
    
    // Theme CSS
    wp_enqueue_style('yonksteam-classic-theme', get_template_directory_uri() . '/assets/css/theme.css', ['yonksteam-classic-style'], $version);
    
    // Navigation JS
    wp_enqueue_script('yonksteam-classic-navigation', get_template_directory_uri() . '/assets/js/navigation.js', [], $version, true);
}
add_action('wp_enqueue_scripts', 'yonksteam_classic_enqueue');

// Elementor compatibility
function yonksteam_classic_elementor_support() {
    // Tell Elementor this theme supports its canvas
    add_theme_support('elementor');
}
add_action('after_setup_theme', 'yonksteam_classic_elementor_support');

// Full width class for Elementor
function yonksteam_classic_elementor_full_width() {
    if (function_exists('elementor_theme_do_location') || did_action('elementor/loaded')) {
        // Elementor handles its own containers
    }
}

// Allow Elementor to take over the content area
function yonksteam_classic_register_elementor_locations($elementor_theme_manager) {
    $elementor_theme_manager->register_location('header');
    $elementor_theme_manager->register_location('footer');
    $elementor_theme_manager->register_location('single');
    $elementor_theme_manager->register_location('archive');
}
add_action('elementor/theme/register_locations', 'yonksteam_classic_register_elementor_locations');

// WPForms compatibility
add_theme_support('wpforms-styles');

// FluentCRM / FluentCart compatibility (no special hooks needed, they work with any theme)

// Remove default block styles (we're using Elementor, not blocks)
function yonksteam_classic_remove_block_styles() {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
}
add_action('wp_enqueue_scripts', 'yonksteam_classic_remove_block_styles', 100);