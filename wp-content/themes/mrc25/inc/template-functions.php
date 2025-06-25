<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package MRC25
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function mrc25_body_classes($classes) {
    // Adds a class of hfeed to non-singular pages.
    if (!is_singular()) {
        $classes[] = 'hfeed';
    }
    return $classes;
}
add_filter('body_class', 'mrc25_body_classes');

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function mrc25_pingback_header() {
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
    }
}
add_action('wp_head', 'mrc25_pingback_header');

/**
 * Change the excerpt length
 */
function mrc25_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'mrc25_excerpt_length', 999);

/**
 * Change the excerpt more string
 */
function mrc25_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'mrc25_excerpt_more');

/**
 * Add custom image sizes
 */
function mrc25_image_sizes() {
    add_image_size('mrc25-featured', 800, 400, true);
    add_image_size('mrc25-thumbnail', 300, 200, true);
}
add_action('after_setup_theme', 'mrc25_image_sizes');

/**
 * Remove WordPress version from head
 */
function mrc25_remove_version() {
    return '';
}
add_filter('the_generator', 'mrc25_remove_version');

/**
 * Add preconnect for Google Fonts
 */
function mrc25_resource_hints($urls, $relation_type) {
    if (wp_style_is('mrc25-fonts', 'queue') && 'preconnect' === $relation_type) {
        $urls[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin',
        );
    }

    return $urls;
}
add_filter('wp_resource_hints', 'mrc25_resource_hints', 10, 2); 