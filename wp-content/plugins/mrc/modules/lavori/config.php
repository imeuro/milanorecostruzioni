<?php
/**
 * Configurazione del Modulo Lavori
 *
 * @package MRC
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configurazione del modulo lavori
 */
class MRC_Lavori_Config {
    
    /**
     * Numero massimo di immagini per galleria
     */
    const MAX_GALLERY_IMAGES = 20;
    
    /**
     * Lunghezza massima della descrizione
     */
    const MAX_DESCRIPTION_LENGTH = 500;
    
    /**
     * Dimensioni immagini supportate
     */
    const SUPPORTED_IMAGE_SIZES = array(
        'thumbnail' => 'Thumbnail',
        'medium' => 'Medium',
        'large' => 'Large',
        'full' => 'Full Size'
    );
    
    /**
     * Ottiene la configurazione del modulo
     */
    public static function get_config() {
        return array(
            'max_gallery_images' => apply_filters('mrc_max_gallery_images', self::MAX_GALLERY_IMAGES),
            'max_description_length' => apply_filters('mrc_max_description_length', self::MAX_DESCRIPTION_LENGTH),
            'supported_image_sizes' => apply_filters('mrc_supported_image_sizes', self::SUPPORTED_IMAGE_SIZES),
            'enable_carousel' => apply_filters('mrc_enable_carousel', true),
            'enable_lightbox' => apply_filters('mrc_enable_lightbox', true),
            'auto_play_interval' => apply_filters('mrc_auto_play_interval', 5000),
        );
    }
}
