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
    
    // Carica le Dashicons per il frontend
    
    wp_enqueue_script('mrc25-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), '1.0.0', true);
    
    // Carica gli script solo per il post type 'lavori'
    if (is_singular('lavori') || is_post_type_archive('lavori')) {
        wp_enqueue_style('dashicons');
        wp_enqueue_script('mrc25-hero-carousel', get_template_directory_uri() . '/assets/js/hero-carousel.js', array(), '1.0.0', true);
        wp_enqueue_script('mrc25-effects', get_template_directory_uri() . '/assets/js/effects.js', array(), '1.0.0', true);
        wp_enqueue_script('mrc25-gallery-lightbox', get_template_directory_uri() . '/assets/js/gallery-lightbox.js', array(), '1.0.0', true);
        wp_enqueue_script('mrc25-carousel', get_template_directory_uri() . '/assets/js/carousel.js', array(), '1.0.0', true);
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

/**
 * Regenerate hero carousel images when theme is activated
 */
function mrc25_regenerate_hero_images() {
    // Get all hero slides
    $hero_slides = get_posts(array(
        'post_type' => 'hero-slide',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    
    foreach ($hero_slides as $slide) {
        if (has_post_thumbnail($slide->ID)) {
            $thumbnail_id = get_post_thumbnail_id($slide->ID);
            
            // Force regeneration of the hero carousel size
            $image_data = wp_get_attachment_metadata($thumbnail_id);
            if ($image_data) {
                // Remove existing hero carousel size if it exists
                if (isset($image_data['sizes']['mrc25-hero-carousel'])) {
                    unset($image_data['sizes']['mrc25-hero-carousel']);
                }
                
                // Regenerate the image sizes
                $new_image_data = wp_generate_attachment_metadata($thumbnail_id, get_attached_file($thumbnail_id));
                if ($new_image_data) {
                    wp_update_attachment_metadata($thumbnail_id, $new_image_data);
                }
            }
        }
    }
}

// Regenerate images on theme activation
add_action('after_switch_theme', 'mrc25_regenerate_hero_images');

/**
 * Add admin action to manually regenerate hero carousel images
 */
function mrc25_admin_regenerate_hero_images() {
    if (isset($_GET['action']) && $_GET['action'] === 'regenerate_hero_images' && 
        isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'regenerate_hero_images')) {
        
        mrc25_regenerate_hero_images();
        
        // Redirect back to admin with success message
        wp_redirect(admin_url('edit.php?post_type=hero-slide&regenerated=1'));
        exit;
    }
}
add_action('admin_init', 'mrc25_admin_regenerate_hero_images');

/**
 * Add admin notice for regeneration success
 */
function mrc25_admin_notices() {
    if (isset($_GET['regenerated']) && $_GET['regenerated'] === '1') {
        echo '<div class="notice notice-success is-dismissible"><p>Immagini del carousel hero rigenerate con successo!</p></div>';
    }
}
add_action('admin_notices', 'mrc25_admin_notices');

/**
 * Shortcode per mostrare la griglia dei lavori
 */
function mrc25_lavori_grid_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 6,
        'show_description' => 'true'
    ), $atts);
    
    $portfolio_query = new WP_Query(array(
        'post_type' => 'lavori',
        'posts_per_page' => intval($atts['limit']),
        'post_status' => 'publish'
    ));
    
    if (!$portfolio_query->have_posts()) {
        return '<div class="portfolio-placeholder"><p>Nessun lavoro disponibile al momento.</p></div>';
    }
    
    ob_start();
    ?>
    <div class="portfolio-grid">
        <?php while ($portfolio_query->have_posts()) : $portfolio_query->the_post(); ?>
            <div class="portfolio-item">
                <div class="portfolio-image">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('large'); ?>
                    <?php else : ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/placeholder.jpg" alt="<?php the_title(); ?>">
                    <?php endif; ?>
                    <div class="portfolio-overlay">
                        <div class="portfolio-content">
                            <h3><?php the_title(); ?></h3>
                            <?php if ($atts['show_description'] === 'true') : ?>
                                <p><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                            <?php endif; ?>
                            <a href="<?php the_permalink(); ?>" class="portfolio-link">Vedi Progetto</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <?php
    
    wp_reset_postdata();
    return ob_get_clean();
}

// Registra lo shortcode
add_shortcode('lavori_grid', 'mrc25_lavori_grid_shortcode'); 