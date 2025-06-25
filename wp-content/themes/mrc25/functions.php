<?php
/**
 * MRC25 functions and definitions
 *
 * @package MRC25
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sets up theme defaults and registers support for various WordPress features
 */
function mrc25_setup() {
    // Make theme available for translation
    load_theme_textdomain('mrc25', get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');

    // Add support for responsive embeds
    add_theme_support('responsive-embeds');

    // Add support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 250,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
    ));

    // Add support for custom background
    add_theme_support('custom-background', array(
        'default-color' => 'ffffff',
    ));

    // Add support for HTML5 markup
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for editor styles
    add_theme_support('editor-styles');
    add_editor_style('style.css');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => esc_html__('Menu Principale', 'mrc25'),
        'footer'  => esc_html__('Menu Footer', 'mrc25'),
    ));
}
add_action('after_setup_theme', 'mrc25_setup');

/**
 * Set the content width in pixels
 */
function mrc25_content_width() {
    $GLOBALS['content_width'] = apply_filters('mrc25_content_width', 1200);
}
add_action('after_setup_theme', 'mrc25_content_width', 0);

/**
 * Register widget area
 */
function mrc25_widgets_init() {
    // Sidebar e widget area rimossi: nessuna registrazione widget
}
add_action('widgets_init', 'mrc25_widgets_init');

/**
 * Enqueue scripts and styles
 */
function mrc25_scripts() {
    wp_enqueue_style('mrc25-style', get_stylesheet_uri(), array(), '1.0.0');
    
    wp_enqueue_script('mrc25-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), '1.0.0', true);
    wp_enqueue_script('mrc25-hero-carousel', get_template_directory_uri() . '/assets/js/hero-carousel.js', array(), '1.0.0', true);
    wp_enqueue_script('mrc25-effects', get_template_directory_uri() . '/assets/js/effects.js', array(), '1.0.0', true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'mrc25_scripts');

/**
 * Custom template tags for this theme
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file
 */
if (defined('JETPACK__VERSION')) {
    require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Add custom body classes
 */
// RIMOSSO: Funzione mrc25_body_classes e add_filter('body_class', 'mrc25_body_classes');

/**
 * Add preconnect for Google Fonts
 */
// RIMOSSO: Funzione mrc25_resource_hints e add_filter('wp_resource_hints', ...)

/**
 * Add hero carousel assets
 */
function mrc25_enqueue_hero_carousel_assets() {
    if (is_front_page() || is_home()) {
        wp_enqueue_style('mrc25-hero-carousel', get_template_directory_uri() . '/assets/css/hero-carousel.css', array(), '1.0.0');
        wp_enqueue_script('mrc25-hero-carousel', get_template_directory_uri() . '/assets/js/hero-carousel.js', array(), '1.0.0', true);
    }
}
add_action('wp_enqueue_scripts', 'mrc25_enqueue_hero_carousel_assets'); 