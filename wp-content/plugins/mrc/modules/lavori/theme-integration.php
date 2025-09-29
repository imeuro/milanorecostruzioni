<?php
/**
 * Integrazione con il Tema - Esempi di utilizzo
 * 
 * Questo file mostra come integrare il modulo lavori nel tuo tema WordPress
 * 
 * @package MRC
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Esempio di integrazione nel functions.php del tema
 */
function mrc_theme_integration_examples() {
    
    // 1. Aggiungi supporto per il post type lavori
    add_theme_support('post-thumbnails');
    
    // 2. Registra dimensioni immagini personalizzate per i lavori
    add_image_size('lavori-thumbnail', 300, 200, true);
    add_image_size('lavori-medium', 600, 400, true);
    add_image_size('lavori-large', 1200, 800, true);
    
    // 3. Personalizza il numero massimo di immagini
    add_filter('mrc_max_gallery_images', function($max) {
        return 15; // Riduci a 15 immagini
    });
    
    // 4. Personalizza la lunghezza massima della descrizione
    add_filter('mrc_max_description_length', function($length) {
        return 300; // Riduci a 300 caratteri
    });
    
    // 5. Abilita/disabilita funzionalità
    add_filter('mrc_enable_carousel', '__return_true');
    add_filter('mrc_enable_lightbox', '__return_true');
    add_filter('mrc_auto_play_interval', function($interval) {
        return 3000; // 3 secondi invece di 5
    });
}

// Esegui l'integrazione
add_action('after_setup_theme', 'mrc_theme_integration_examples');

/**
 * Template per single-lavori.php
 */
function mrc_single_lavori_template() {
    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php wp_title('|', true, 'right'); ?></title>
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
        <div class="container">
            <?php while (have_posts()): the_post(); ?>
                <article class="single-lavoro">
                    <header class="lavoro-header">
                        <h1><?php the_title(); ?></h1>
                        
                        <?php if (mrc_has_lavoro_description()): ?>
                            <div class="lavoro-description">
                                <?php echo wp_kses_post(wpautop(mrc_get_lavoro_description())); ?>
                            </div>
                        <?php endif; ?>
                    </header>
                    
                    <?php if (mrc_has_lavoro_gallery()): ?>
                        <section class="lavoro-gallery">
                            <?php
                            // Usa il carousel se disponibile
                            if (function_exists('mrc_display_lavoro_with_carousel')) {
                                mrc_display_lavoro_with_carousel();
                            } else {
                                // Fallback: griglia semplice
                                $images = mrc_get_lavoro_gallery_images(get_the_ID(), 'large');
                                if (!empty($images)) {
                                    echo '<div class="gallery-grid">';
                                    foreach ($images as $image) {
                                        echo '<div class="gallery-item">';
                                        echo '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($image['alt']) . '">';
                                        if ($image['caption']) {
                                            echo '<p class="gallery-caption">' . esc_html($image['caption']) . '</p>';
                                        }
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                }
                            }
                            ?>
                        </section>
                    <?php endif; ?>

                </article>
            <?php endwhile; ?>
        </div>
        <?php wp_footer(); ?>
    </body>
    </html>
    <?php
}

/**
 * Template per archive-lavori.php
 */
function mrc_archive_lavori_template() {
    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Lavori - <?php bloginfo('name'); ?></title>
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
        <div class="container">
            <header class="archive-header">
                <h1>I Nostri Lavori</h1>
                <p>Scopri i nostri progetti più recenti</p>
            </header>
            
            <div class="lavori-grid">
                <?php
                $query = new WP_Query(array(
                    'post_type' => 'lavori',
                    'posts_per_page' => 12,
                    'paged' => get_query_var('paged') ? get_query_var('paged') : 1
                ));
                
                if ($query->have_posts()):
                    while ($query->have_posts()): $query->the_post();
                        $first_image = mrc_get_lavoro_first_image(get_the_ID(), 'lavori-medium');
                        $description = mrc_get_lavoro_description(get_the_ID());
                        $gallery_count = mrc_get_lavoro_gallery_count(get_the_ID());
                        ?>
                        <article class="lavoro-card">
                            <div class="card-image">
                                <?php if ($first_image): ?>
                                    <img src="<?php echo esc_url($first_image['url']); ?>" 
                                         alt="<?php echo esc_attr($first_image['alt'] ?: get_the_title()); ?>"
                                         loading="lazy">
                                    
                                    <?php if ($gallery_count > 1): ?>
                                        <div class="gallery-indicator">
                                            <span class="dashicons dashicons-images-alt2"></span>
                                            <?php echo $gallery_count; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="no-image">
                                        <span class="dashicons dashicons-format-image"></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-content">
                                <h2 class="card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <?php if ($description): ?>
                                    <div class="card-description">
                                        <?php echo wp_kses_post(wp_trim_words($description, 20, '...')); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-meta">
                                    <span class="date"><?php echo get_the_date('j M Y'); ?></span>
                                    <a href="<?php the_permalink(); ?>" class="read-more">Leggi tutto</a>
                                </div>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    
                    // Paginazione
                    $pagination = paginate_links(array(
                        'total' => $query->max_num_pages,
                        'current' => max(1, get_query_var('paged')),
                        'format' => '?paged=%#%',
                        'show_all' => false,
                        'type' => 'list',
                        'end_size' => 2,
                        'mid_size' => 1,
                        'prev_next' => true,
                        'prev_text' => __('« Precedente'),
                        'next_text' => __('Successiva »'),
                    ));
                    
                    if ($pagination) {
                        echo '<nav class="pagination">' . $pagination . '</nav>';
                    }
                    
                    wp_reset_postdata();
                else:
                    echo '<p>Nessun lavoro trovato.</p>';
                endif;
                ?>
            </div>
        </div>
        <?php wp_footer(); ?>
    </body>
    </html>
    <?php
}

/**
 * Widget per la sidebar
 */
function mrc_lavori_sidebar_widget() {
    ?>
    <aside class="widget mrc-lavori-widget">
        <h3 class="widget-title">Lavori Recenti</h3>
        <div class="widget-content">
            <?php
            $query = mrc_get_lavori_with_gallery(array(
                'posts_per_page' => 3
            ));
            
            if ($query->have_posts()):
                while ($query->have_posts()): $query->the_post();
                    $first_image = mrc_get_lavoro_first_image(get_the_ID(), 'thumbnail');
                    $description = mrc_get_lavoro_description(get_the_ID());
                    ?>
                    <div class="widget-item">
                        <?php if ($first_image): ?>
                            <div class="widget-image">
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo esc_url($first_image['url']); ?>" 
                                         alt="<?php echo esc_attr($first_image['alt'] ?: get_the_title()); ?>"
                                         loading="lazy">
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="widget-content">
                            <h4 class="widget-item-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h4>
                            
                            <?php if ($description): ?>
                                <p class="widget-description">
                                    <?php echo wp_kses_post(wp_trim_words($description, 15, '...')); ?>
                                </p>
                            <?php endif; ?>
                            
                            <span class="widget-date"><?php echo get_the_date('j M Y'); ?></span>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
            else:
                echo '<p>Nessun lavoro disponibile.</p>';
            endif;
            ?>
        </div>
    </aside>
    <?php
}

/**
 * Shortcode per mostrare lavori
 */
function mrc_lavori_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 6,
        'show_description' => 'true',
        'show_gallery_count' => 'true',
        'layout' => 'grid' // grid, carousel, list
    ), $atts);
    
    $query = mrc_get_lavori_with_gallery(array(
        'posts_per_page' => intval($atts['limit'])
    ));
    
    if (!$query->have_posts()) {
        return '<p>Nessun lavoro trovato.</p>';
    }
    
    ob_start();
    ?>
    <div class="mrc-lavori-shortcode mrc-layout-<?php echo esc_attr($atts['layout']); ?>">
        <?php while ($query->have_posts()): $query->the_post(); ?>
            <div class="lavoro-item">
                <?php
                $first_image = mrc_get_lavoro_first_image(get_the_ID(), 'medium');
                $description = mrc_get_lavoro_description(get_the_ID());
                $gallery_count = mrc_get_lavoro_gallery_count(get_the_ID());
                ?>
                
                <?php if ($first_image): ?>
                    <div class="item-image">
                        <a href="<?php the_permalink(); ?>">
                            <img src="<?php echo esc_url($first_image['url']); ?>" 
                                 alt="<?php echo esc_attr($first_image['alt'] ?: get_the_title()); ?>"
                                 loading="lazy">
                        </a>
                        
                        <?php if ($atts['show_gallery_count'] === 'true' && $gallery_count > 1): ?>
                            <div class="gallery-count">
                                <?php echo $gallery_count; ?> immagini
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="item-content">
                    <h3 class="item-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    
                    <?php if ($atts['show_description'] === 'true' && $description): ?>
                        <div class="item-description">
                            <?php echo wp_kses_post(wp_trim_words($description, 20, '...')); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="item-meta">
                        <span class="date"><?php echo get_the_date('j M Y'); ?></span>
                        <a href="<?php the_permalink(); ?>" class="read-more">Leggi tutto</a>
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
add_shortcode('mrc_lavori', 'mrc_lavori_shortcode');

/**
 * Hook per aggiungere supporto al tema
 */
add_action('after_setup_theme', function() {
    // Aggiungi supporto per il post type lavori
    add_theme_support('post-thumbnails');
    
    // Registra dimensioni immagini per i lavori
    add_image_size('lavori-thumbnail', 300, 200, true);
    add_image_size('lavori-medium', 600, 400, true);
    add_image_size('lavori-large', 1200, 800, true);
});

/**
 * Hook per personalizzare la query principale
 */
add_action('pre_get_posts', function($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_post_type_archive('lavori')) {
            $query->set('posts_per_page', 12);
            $query->set('orderby', 'date');
            $query->set('order', 'DESC');
        }
    }
});
