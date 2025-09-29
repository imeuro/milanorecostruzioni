<?php
/**
 * Lavori Helper Functions
 *
 * Funzioni di utilità per lavorare con i post di tipo "lavori"
 *
 * @package MRC
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ottiene la descrizione di un lavoro
 *
 * @param int $post_id ID del post
 * @return string Descrizione del lavoro
 */
function mrc_get_lavoro_description($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return MRC_Lavori_Manager::get_lavoro_description($post_id);
}

/**
 * Ottiene la galleria di un lavoro
 *
 * @param int $post_id ID del post
 * @return array Array di ID delle immagini
 */
function mrc_get_lavoro_gallery($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return MRC_Lavori_Manager::get_lavoro_gallery($post_id);
}

/**
 * Ottiene le immagini della galleria con dati completi
 *
 * @param int $post_id ID del post
 * @param string $size Dimensione delle immagini (thumbnail, medium, large, full)
 * @return array Array di oggetti immagine con dati completi
 */
function mrc_get_lavoro_gallery_images($post_id = null, $size = 'full') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return MRC_Lavori_Manager::get_lavoro_gallery_images($post_id, $size);
}

/**
 * Verifica se un lavoro ha una galleria
 *
 * @param int $post_id ID del post
 * @return bool True se ha galleria, false altrimenti
 */
function mrc_has_lavoro_gallery($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $gallery = mrc_get_lavoro_gallery($post_id);
    return !empty($gallery);
}

/**
 * Verifica se un lavoro ha una descrizione
 *
 * @param int $post_id ID del post
 * @return bool True se ha descrizione, false altrimenti
 */
function mrc_has_lavoro_description($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $description = mrc_get_lavoro_description($post_id);
    return !empty(trim($description));
}

/**
 * Ottiene il numero di immagini nella galleria
 *
 * @param int $post_id ID del post
 * @return int Numero di immagini
 */
function mrc_get_lavoro_gallery_count($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $gallery = mrc_get_lavoro_gallery($post_id);
    return count($gallery);
}

/**
 * Ottiene la prima immagine della galleria
 *
 * @param int $post_id ID del post
 * @param string $size Dimensione dell'immagine
 * @return array|null Dati dell'immagine o null se non trovata
 */
function mrc_get_lavoro_first_image($post_id = null, $size = 'full') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $gallery = mrc_get_lavoro_gallery_images($post_id, $size);
    return !empty($gallery) ? $gallery[0] : null;
}

/**
 * Ottiene l'ultima immagine della galleria
 *
 * @param int $post_id ID del post
 * @param string $size Dimensione dell'immagine
 * @return array|null Dati dell'immagine o null se non trovata
 */
function mrc_get_lavoro_last_image($post_id = null, $size = 'full') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $gallery = mrc_get_lavoro_gallery_images($post_id, $size);
    return !empty($gallery) ? end($gallery) : null;
}

/**
 * Genera HTML per una singola immagine della galleria
 *
 * @param array $image Dati dell'immagine
 * @param string $class Classe CSS aggiuntiva
 * @param bool $lazy_load Se abilitare il lazy loading
 * @return string HTML dell'immagine
 */
function mrc_render_gallery_image($image, $class = '', $lazy_load = true) {
    if (!$image || !isset($image['url'])) {
        return '';
    }
    
    $attributes = array(
        'src' => $image['url'],
        'alt' => $image['alt'] ?: get_the_title(),
        'width' => $image['width'] ?? '',
        'height' => $image['height'] ?? '',
        'class' => 'mrc-gallery-image ' . $class,
        'loading' => $lazy_load ? 'lazy' : 'eager',
    );
    
    if ($image['caption']) {
        $attributes['title'] = $image['caption'];
    }
    
    $attr_string = '';
    foreach ($attributes as $key => $value) {
        if ($value) {
            $attr_string .= sprintf(' %s="%s"', $key, esc_attr($value));
        }
    }
    
    return sprintf('<img%s>', $attr_string);
}

/**
 * Genera HTML per la galleria completa
 *
 * @param int $post_id ID del post
 * @param string $size Dimensione delle immagini
 * @param string $container_class Classe CSS per il container
 * @param string $image_class Classe CSS per le immagini
 * @param bool $lazy_load Se abilitare il lazy loading
 * @return string HTML della galleria
 */
function mrc_render_lavoro_gallery($post_id = null, $size = 'full', $container_class = 'mrc-gallery', $image_class = '', $lazy_load = true) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $images = mrc_get_lavoro_gallery_images($post_id, $size);
    
    if (empty($images)) {
        return '';
    }
    
    $html = '<div class="' . esc_attr($container_class) . '">';
    
    foreach ($images as $index => $image) {
        $html .= '<div class="mrc-gallery-item" data-index="' . $index . '">';
        $html .= mrc_render_gallery_image($image, $image_class, $lazy_load);
        
        if ($image['caption']) {
            $html .= '<div class="mrc-gallery-caption">' . esc_html($image['caption']) . '</div>';
        }
        
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Genera dati JSON per JavaScript (utile per carousel)
 *
 * @param int $post_id ID del post
 * @param string $size Dimensione delle immagini
 * @return string JSON con i dati della galleria
 */
function mrc_get_lavoro_gallery_json($post_id = null, $size = 'full') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $images = mrc_get_lavoro_gallery_images($post_id, $size);
    return wp_json_encode($images);
}

/**
 * Query personalizzata per ottenere lavori con galleria
 *
 * @param array $args Argomenti per WP_Query
 * @return WP_Query Query object
 */
function mrc_get_lavori_with_gallery($args = array()) {
    $default_args = array(
        'post_type' => 'lavori',
        'post_status' => 'publish',
        'posts_per_page' => 10,
        'meta_query' => array(
            array(
                'key' => MRC_Lavori_Manager::META_GALLERY,
                'compare' => 'EXISTS'
            )
        )
    );
    
    $args = wp_parse_args($args, $default_args);
    
    return new WP_Query($args);
}

/**
 * Query personalizzata per ottenere lavori con descrizione
 *
 * @param array $args Argomenti per WP_Query
 * @return WP_Query Query object
 */
function mrc_get_lavori_with_description($args = array()) {
    $default_args = array(
        'post_type' => 'lavori',
        'post_status' => 'publish',
        'posts_per_page' => 10,
        'meta_query' => array(
            array(
                'key' => MRC_Lavori_Manager::META_DESCRIPTION,
                'compare' => 'EXISTS'
            )
        )
    );
    
    $args = wp_parse_args($args, $default_args);
    
    return new WP_Query($args);
}
