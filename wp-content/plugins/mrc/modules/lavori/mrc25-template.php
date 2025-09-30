<?php
/**
 * Esempio di template per visualizzare i lavori
 * 
 * Questo file mostra come utilizzare le funzioni helper del modulo lavori
 * in un template personalizzato
 * 
 * @package MRC
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template per la visualizzazione di un singolo lavoro
 */
function mrc_single_lavoro_template() {
    $post_id = get_the_ID();
    
    // Verifica che sia un post di tipo lavori
    if (get_post_type($post_id) !== 'lavori') {
        return;
    }
    
    $title = get_the_title($post_id);
    $description = mrc_get_lavoro_description($post_id);
    $gallery_images = mrc_get_lavoro_gallery_images($post_id, 'full');
    $has_gallery = mrc_has_lavoro_gallery($post_id);
    $has_description = mrc_has_lavoro_description($post_id);
    
    ?>
    <article class="mrc-single-lavoro" id="lavoro-<?php echo esc_attr($post_id); ?>">
        <header class="mrc-lavoro-header">
            <h1 class="mrc-lavoro-title"><?php echo esc_html($title); ?></h1>
            
            <?php if ($has_description): ?>
                <div class="mrc-lavoro-description">
                    <?php echo wp_kses_post(wpautop($description)); ?>
                </div>
            <?php endif; ?>
        </header>
        
        <?php if ($has_gallery): ?>
            <div class="mrc-lavoro-gallery">
                <h2 class="mrc-gallery-title">Galleria Immagini</h2>
                
                <?php
                // Opzione 1: Carousel (richiede l'inclusione di example-carousel.php)
                // mrc_display_lavoro_with_carousel($post_id);
                
                // Opzione 2: Griglia semplice
                mrc_display_gallery_grid($gallery_images);
                
                // Opzione 3: Lightbox
                // mrc_display_gallery_lightbox($gallery_images);
                ?>
            </div>
        <?php endif; ?>
        
        <footer class="mrc-lavoro-footer">
            <div class="mrc-lavoro-meta">
                <span class="mrc-lavoro-date">
                    Pubblicato il <?php echo get_the_date('j F Y', $post_id); ?>
                </span>
                
                <?php if ($has_gallery): ?>
                    <span class="mrc-lavoro-count">
                        <?php echo mrc_get_lavoro_gallery_count($post_id); ?> immagini
                    </span>
                <?php endif; ?>
            </div>
        </footer>
    </article>
    <?php
}

/**
 * Visualizza la galleria come griglia
 */
function mrc_display_gallery_grid($images) {
    if (empty($images)) {
        return;
    }
    
    ?>
    <div class="mrc-gallery-grid">
        <?php foreach ($images as $index => $image): ?>
            <div class="mrc-gallery-item" data-index="<?php echo esc_attr($index); ?>">
                <img src="<?php echo esc_url($image['url']); ?>" 
                     alt="<?php echo esc_attr($image['alt']); ?>"
                     loading="<?php echo $index < 3 ? 'eager' : 'lazy'; ?>"
                     class="mrc-gallery-image">
                
                <?php if ($image['caption']): ?>
                    <div class="mrc-gallery-caption">
                        <?php echo esc_html($image['caption']); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * Visualizza la galleria con lightbox
 */
function mrc_display_gallery_lightbox($images) {
    if (empty($images)) {
        return;
    }
    
    ?>
    <div class="mrc-gallery-lightbox">
        <div class="mrc-gallery-thumbnails">
            <?php foreach ($images as $index => $image): ?>
                <div class="mrc-gallery-thumb" data-index="<?php echo esc_attr($index); ?>">
                    <img src="<?php echo esc_url($image['url']); ?>" 
                         alt="<?php echo esc_attr($image['alt']); ?>"
                         data-caption="<?php echo esc_attr($image['caption']); ?>"
                         loading="<?php echo $index < 3 ? 'eager' : 'lazy'; ?>">
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mrc-lightbox-modal" id="mrc-lightbox">
            <div class="mrc-lightbox-content">
                <button class="mrc-lightbox-close" aria-label="Chiudi lightbox">
                    <span class="dashicons dashicons-no-alt"></span>
                </button>
                
                <div class="mrc-lightbox-image-container">
                    <img class="mrc-lightbox-image" src="" alt="">
                    <div class="mrc-lightbox-caption"></div>
                </div>
                
                <button class="mrc-lightbox-prev" aria-label="Immagine precedente">
                    <span class="dashicons dashicons-arrow-left-alt2"></span>
                </button>
                
                <button class="mrc-lightbox-next" aria-label="Immagine successiva">
                    <span class="dashicons dashicons-arrow-right-alt2"></span>
                </button>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Template per l'archivio dei lavori
 */
function mrc_lavori_archive_template() {
    $query = new WP_Query(array(
        'post_type' => 'lavori',
        'post_status' => 'publish',
        'posts_per_page' => 12,
        'paged' => get_query_var('paged') ? get_query_var('paged') : 1
    ));
    
    if (!$query->have_posts()) {
        echo '<p>Nessun lavoro trovato.</p>';
        return;
    }
    
    ?>
    <div class="mrc-lavori-archive">
        <div class="mrc-lavori-grid">
            <?php while ($query->have_posts()): $query->the_post(); ?>
                <?php mrc_lavoro_card_template(); ?>
            <?php endwhile; ?>
        </div>
        
        <?php
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
            echo '<nav class="mrc-pagination">' . $pagination . '</nav>';
        }
        ?>
    </div>
    <?php
    
    wp_reset_postdata();
}

/**
 * Template per una card di lavoro nell'archivio
 */
function mrc_lavoro_card_template() {
    $post_id = get_the_ID();
    $title = get_the_title($post_id);
    $description = mrc_get_lavoro_description($post_id);
    $first_image = mrc_get_lavoro_first_image($post_id, 'medium');
    $gallery_count = mrc_get_lavoro_gallery_count($post_id);
    
    ?>
    <article class="mrc-lavoro-card">
        <div class="mrc-lavoro-card-image">
            <?php if ($first_image): ?>
                <img src="<?php echo esc_url($first_image['url']); ?>" 
                     alt="<?php echo esc_attr($first_image['alt'] ?: $title); ?>"
                     loading="lazy">
                
                <?php if ($gallery_count > 1): ?>
                    <div class="mrc-lavoro-gallery-count">
                        <span class="dashicons dashicons-images-alt2"></span>
                        <?php echo $gallery_count; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="mrc-lavoro-no-image">
                    <span class="dashicons dashicons-format-image"></span>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="mrc-lavoro-card-content">
            <h3 class="mrc-lavoro-card-title">
                <a href="<?php the_permalink(); ?>"><?php echo esc_html($title); ?></a>
            </h3>
            
            <?php if ($description): ?>
                <div class="mrc-lavoro-card-description">
                    <?php echo wp_kses_post(wp_trim_words($description, 20, '...')); ?>
                </div>
            <?php endif; ?>
            
            <div class="mrc-lavoro-card-meta">
                <span class="mrc-lavoro-date">
                    <?php echo get_the_date('j M Y'); ?>
                </span>
            </div>
        </div>
    </article>
    <?php
}

/**
 * Query personalizzata per lavori con galleria
 */
function mrc_get_featured_lavori($limit = 6) {
    $query = mrc_get_lavori_with_gallery(array(
        'posts_per_page' => $limit,
        'meta_key' => '_thumbnail_id', // Solo lavori con immagine in evidenza
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    return $query;
}

/**
 * Widget per mostrare lavori recenti
 */
function mrc_lavori_recent_widget($args = array()) {
    $defaults = array(
        'title' => 'Lavori Recenti',
        'limit' => 4,
        'show_description' => true,
        'show_gallery_count' => true
    );
    
    $args = wp_parse_args($args, $defaults);
    
    $query = mrc_get_lavori_with_gallery(array(
        'posts_per_page' => $args['limit']
    ));
    
    if (!$query->have_posts()) {
        return;
    }
    
    ?>
    <div class="mrc-lavori-widget">
        <?php if ($args['title']): ?>
            <h3 class="mrc-widget-title"><?php echo esc_html($args['title']); ?></h3>
        <?php endif; ?>
        
        <div class="mrc-lavori-widget-list">
            <?php while ($query->have_posts()): $query->the_post(); ?>
                <div class="mrc-lavori-widget-item">
                    <div class="mrc-widget-image">
                        <?php
                        $first_image = mrc_get_lavoro_first_image(get_the_ID(), 'thumbnail');
                        if ($first_image):
                        ?>
                            <img src="<?php echo esc_url($first_image['url']); ?>" 
                                 alt="<?php echo esc_attr($first_image['alt'] ?: get_the_title()); ?>"
                                 loading="lazy">
                        <?php endif; ?>
                    </div>
                    
                    <div class="mrc-widget-content">
                        <h4 class="mrc-widget-item-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                        
                        <?php if ($args['show_description']): ?>
                            <?php
                            $description = mrc_get_lavoro_description(get_the_ID());
                            if ($description):
                            ?>
                                <p class="mrc-widget-description">
                                    <?php echo wp_kses_post(wp_trim_words($description, 15, '...')); ?>
                                </p>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php if ($args['show_gallery_count']): ?>
                            <?php
                            $gallery_count = mrc_get_lavoro_gallery_count(get_the_ID());
                            if ($gallery_count > 0):
                            ?>
                                <span class="mrc-widget-gallery-count">
                                    <?php echo $gallery_count; ?> immagini
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php
    
    wp_reset_postdata();
}
