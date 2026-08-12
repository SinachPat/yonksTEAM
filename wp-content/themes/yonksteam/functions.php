<?php
/**
 * YonksTEAM Theme Functions
 * 
 * Custom block theme for Jason & Tyler Younker.
 * Pre-compiled CSS is committed — works on one-click install with no build step.
 *
 * @package YonksTEAM
 */

// Theme setup
function yonksteam_setup() {
    // Block theme support
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('editor-styles');
    
    // Editor styles
    add_editor_style('assets/css/editor.css');
    
    // Navigation menus (for fallback)
    register_nav_menus([
        'primary' => __('Primary Navigation', 'yonksteam'),
        'footer'  => __('Footer Navigation', 'yonksteam'),
    ]);
    
    // Translation ready
    load_theme_textdomain('yonksteam');
}
add_action('after_setup_theme', 'yonksteam_setup');

// Register custom blocks
function yonksteam_register_blocks() {
    $blocks = [
        'hero-block',
        'recognition-block',
        'empathy-story',
        'authority-block',
        'plan-steps',
        'success-failure-split',
        'transformation-statement',
        'two-paths',
        'testimonial-block',
        'cta-section',
    ];
    
    foreach ($blocks as $block) {
        $block_path = __DIR__ . "/blocks/{$block}";
        if (file_exists($block_path . '/block.json')) {
            register_block_type($block_path);
        }
    }
}
add_action('init', 'yonksteam_register_blocks');

// Enqueue pre-compiled assets
function yonksteam_enqueue_assets() {
    $theme_version = wp_get_theme()->get('Version');
    
    // Theme stylesheet (style.css — theme header)
    wp_enqueue_style('yonksteam-style', get_stylesheet_uri(), [], $theme_version);
    
    // Main compiled CSS (all custom styles, Tailwind utilities, block styles)
    // Pre-compiled and committed — works on one-click install
    $main_css = get_template_directory_uri() . '/assets/css/style.css';
    if (file_exists(get_template_directory() . '/assets/css/style.css')) {
        wp_enqueue_style('yonksteam-main', $main_css, ['yonksteam-style'], $theme_version);
    }
    
    // Navigation JS
    wp_enqueue_script(
        'yonksteam-navigation',
        get_template_directory_uri() . '/assets/js/navigation.js',
        [],
        $theme_version,
        true
    );
}
add_action('wp_enqueue_scripts', 'yonksteam_enqueue_assets');

// ACF JSON sync directory (auto-syncs field groups from JSON files)
function yonksteam_acf_json_save($path) {
    return get_template_directory() . '/acf-json';
}
add_filter('acf/settings/save_json', 'yonksteam_acf_json_save');

function yonksteam_acf_json_load($paths) {
    $paths[] = get_template_directory() . '/acf-json';
    return $paths;
}
add_filter('acf/settings/load_json', 'yonksteam_acf_json_load');

// Register block patterns
function yonksteam_register_block_patterns() {
    register_block_pattern_category('yonksteam', [
        'label' => __('YonksTEAM', 'yonksteam'),
    ]);
    
    $patterns = [
        'hero-default'        => 'Hero — Default (Homepage)',
        'hero-for-advisors'   => 'Hero — For Advisors',
        'hero-exit-to-client' => 'Hero — Exit to Client',
        'hero-about'          => 'Hero — About',
        'hero-newsletter'     => 'Hero — Newsletter',
        'hero-contact'        => 'Hero — Contact',
        'cta-section'         => 'CTA Section',
        'newsletter-signup'   => 'Newsletter Sign-up',
    ];
    
    foreach ($patterns as $slug => $title) {
        $pattern_file = __DIR__ . "/patterns/{$slug}.php";
        if (file_exists($pattern_file)) {
            register_block_pattern(
                "yonksteam/{$slug}",
                [
                    'title'      => __($title, 'yonksteam'),
                    'categories' => ['yonksteam'],
                    'content'    => file_get_contents($pattern_file),
                ]
            );
        }
    }
}
add_action('init', 'yonksteam_register_block_patterns');