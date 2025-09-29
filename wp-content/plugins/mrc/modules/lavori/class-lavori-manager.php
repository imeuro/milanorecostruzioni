<?php
/**
 * Lavori Manager
 *
 * Gestisce la personalizzazione del backend per i post di tipo "lavori"
 * con descrizione breve e galleria immagini riordinabile
 *
 * @package MRC
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * MRC Lavori Manager Class
 */
class MRC_Lavori_Manager {
    
    /**
     * Meta key per la descrizione breve
     */
    const META_DESCRIPTION = '_mrc_lavori_description';
    
    /**
     * Meta key per la galleria immagini
     */
    const META_GALLERY = '_mrc_lavori_gallery';
    
    /**
     * Constructor
     */
    public function __construct() {
        // Carica la configurazione
        $config_file = MRC_PLUGIN_DIR . 'modules/lavori/config.php';
        if (file_exists($config_file)) {
            require_once $config_file;
        }
        
        // Carica le funzioni helper
        $functions_file = MRC_PLUGIN_DIR . 'modules/lavori/functions.php';
        if (file_exists($functions_file)) {
            require_once $functions_file;
        }
        
        // Carica i test (solo in admin)
        if (is_admin()) {
            $test_file = MRC_PLUGIN_DIR . 'modules/lavori/test-module.php';
            if (file_exists($test_file)) {
                require_once $test_file;
            }
        }
        
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_mrc_reorder_gallery', array($this, 'ajax_reorder_gallery'));
        add_action('wp_ajax_mrc_upload_image', array($this, 'ajax_upload_image'));
    }
    
    /**
     * Aggiunge le meta box per i post di tipo lavori
     */
    public function add_meta_boxes() {
        add_meta_box(
            'mrc-lavori-description',
            __('Descrizione Lavoro', 'mrc'),
            array($this, 'render_description_meta_box'),
            'lavori',
            'normal',
            'high'
        );
        
        add_meta_box(
            'mrc-lavori-gallery',
            __('Galleria Immagini', 'mrc'),
            array($this, 'render_gallery_meta_box'),
            'lavori',
            'normal',
            'high'
        );
    }
    
    /**
     * Renderizza la meta box per la descrizione
     */
    public function render_description_meta_box($post) {
        wp_nonce_field('mrc_lavori_meta_box', 'mrc_lavori_meta_box_nonce');
        
        $description = get_post_meta($post->ID, self::META_DESCRIPTION, true);
        
        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th scope="row"><label for="mrc_lavori_description">' . __('Descrizione Breve', 'mrc') . '</label></th>';
        echo '<td>';
        echo '<textarea id="mrc_lavori_description" name="mrc_lavori_description" rows="4" cols="50" style="width: 100%; max-width: 600px;">' . esc_textarea($description) . '</textarea>';
        echo '<p class="description">' . __('Inserisci una breve descrizione del lavoro (massimo 500 caratteri)', 'mrc') . '</p>';
        echo '</td>';
        echo '</tr>';
        echo '</table>';
    }
    
    /**
     * Renderizza la meta box per la galleria
     */
    public function render_gallery_meta_box($post) {
        $gallery_images = get_post_meta($post->ID, self::META_GALLERY, true);
        $gallery_images = is_array($gallery_images) ? $gallery_images : array();
        
        echo '<div id="mrc-gallery-container">';
        echo '<div id="mrc-gallery-upload-area" class="mrc-upload-area">';
        echo '<p>' . __('Trascina qui le immagini o clicca per selezionarle', 'mrc') . '</p>';
        echo '<input type="file" id="mrc-gallery-upload" multiple accept="image/*" style="display: none;">';
        echo '<button type="button" id="mrc-gallery-upload-btn" class="button button-primary">' . __('Seleziona Immagini', 'mrc') . '</button>';
        echo '</div>';
        
        echo '<div id="mrc-gallery-preview" class="mrc-gallery-preview">';
        if (!empty($gallery_images)) {
            foreach ($gallery_images as $index => $image_id) {
                $this->render_gallery_image_item($image_id, $index);
            }
        }
        echo '</div>';
        
        echo '<input type="hidden" id="mrc-gallery-data" name="mrc_lavori_gallery" value="' . esc_attr(json_encode($gallery_images)) . '">';
        echo '<input type="hidden" id="mrc-lavori-nonce" value="' . wp_create_nonce('mrc_lavori_nonce') . '">';
        echo '</div>';
    }
    
    /**
     * Renderizza un singolo elemento immagine della galleria
     */
    private function render_gallery_image_item($image_id, $index) {
        $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        
        if (!$image_url) {
            return;
        }
        
        echo '<div class="mrc-gallery-item" data-image-id="' . esc_attr($image_id) . '" data-index="' . esc_attr($index) . '">';
        echo '<div class="mrc-gallery-item-handle">';
        echo '<span class="dashicons dashicons-menu"></span>';
        echo '</div>';
        echo '<div class="mrc-gallery-item-image">';
        echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '">';
        echo '</div>';
        echo '<div class="mrc-gallery-item-actions">';
        echo '<button type="button" class="button button-small mrc-remove-image" title="' . __('Rimuovi immagine', 'mrc') . '">';
        echo '<span class="dashicons dashicons-trash"></span>';
        echo '</button>';
        echo '</div>';
        echo '</div>';
    }
    
    /**
     * Salva i dati delle meta box
     */
    public function save_meta_boxes($post_id) {
        // Verifica nonce
        if (!isset($_POST['mrc_lavori_meta_box_nonce']) || 
            !wp_verify_nonce($_POST['mrc_lavori_meta_box_nonce'], 'mrc_lavori_meta_box')) {
            return;
        }
        
        // Verifica permessi
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Verifica che non sia un autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Salva descrizione
        if (isset($_POST['mrc_lavori_description'])) {
            $description = sanitize_textarea_field($_POST['mrc_lavori_description']);
            // Limita a 500 caratteri
            if (strlen($description) > 500) {
                $description = substr($description, 0, 500);
            }
            update_post_meta($post_id, self::META_DESCRIPTION, $description);
        }
        
        // Salva galleria
        if (isset($_POST['mrc_lavori_gallery'])) {
            $gallery_data = json_decode(stripslashes($_POST['mrc_lavori_gallery']), true);
            if (is_array($gallery_data)) {
                // Verifica che tutti gli ID siano validi
                $valid_ids = array();
                foreach ($gallery_data as $image_id) {
                    if (is_numeric($image_id) && wp_attachment_is_image($image_id)) {
                        $valid_ids[] = intval($image_id);
                    }
                }
                update_post_meta($post_id, self::META_GALLERY, $valid_ids);
            }
        }
    }
    
    /**
     * Carica script e stili per l'admin
     */
    public function enqueue_admin_scripts($hook) {
        global $post_type;
        
        if ($post_type !== 'lavori' || !in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }
        
        wp_enqueue_media();
        
        wp_enqueue_script(
            'mrc-lavori-admin',
            MRC_PLUGIN_URL . 'modules/lavori/js/admin.js',
            array(), // Nessuna dipendenza - Vanilla JS
            MRC_PLUGIN_VERSION,
            true
        );
        
        wp_enqueue_style(
            'mrc-lavori-admin',
            MRC_PLUGIN_URL . 'modules/lavori/css/admin.css',
            array(),
            MRC_PLUGIN_VERSION
        );
        
        // Localizza script
        wp_localize_script('mrc-lavori-admin', 'mrcLavori', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mrc_lavori_nonce'),
            'maxImages' => apply_filters('mrc_max_gallery_images', 20),
            'strings' => array(
                'selectImages' => __('Seleziona Immagini', 'mrc'),
                'removeImage' => __('Rimuovi immagine', 'mrc'),
                'uploadError' => __('Errore durante il caricamento', 'mrc'),
                'maxImages' => sprintf(__('Massimo %d immagini per galleria', 'mrc'), apply_filters('mrc_max_gallery_images', 20)),
            )
        ));
    }
    
    /**
     * Gestisce il riordinamento della galleria via AJAX
     */
    public function ajax_reorder_gallery() {
        check_ajax_referer('mrc_lavori_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_die(__('Permessi insufficienti', 'mrc'));
        }
        
        $post_id = intval($_POST['post_id']);
        $gallery_order = json_decode(stripslashes($_POST['gallery_order']), true);
        
        if (is_array($gallery_order)) {
            $valid_ids = array();
            foreach ($gallery_order as $image_id) {
                if (is_numeric($image_id) && wp_attachment_is_image($image_id)) {
                    $valid_ids[] = intval($image_id);
                }
            }
            update_post_meta($post_id, self::META_GALLERY, $valid_ids);
            wp_send_json_success();
        }
        
        wp_send_json_error();
    }
    
    /**
     * Gestisce l'upload di immagini via AJAX
     */
    public function ajax_upload_image() {
        // Verifica nonce
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'mrc_lavori_nonce')) {
            wp_send_json_error(array('message' => __('Nonce verification failed', 'mrc')));
        }
        
        // Verifica permessi
        if (!current_user_can('upload_files')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'mrc')));
        }
        
        // Verifica che ci sia un file
        if (!isset($_FILES['async-upload']) || $_FILES['async-upload']['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(array('message' => __('No file uploaded or upload error', 'mrc')));
        }
        
        $file = $_FILES['async-upload'];
        
        // Verifica che sia un'immagine
        $file_type = wp_check_filetype($file['name']);
        if (!in_array($file_type['type'], array('image/jpeg', 'image/png', 'image/gif', 'image/webp'))) {
            wp_send_json_error(array('message' => __('Invalid file type. Only images are allowed.', 'mrc')));
        }
        
        // Verifica dimensioni file
        if ($file['size'] > wp_max_upload_size()) {
            wp_send_json_error(array('message' => __('File too large', 'mrc')));
        }
        
        // Prepara i dati per l'upload
        $upload_data = array(
            'name' => $file['name'],
            'type' => $file['type'],
            'tmp_name' => $file['tmp_name'],
            'error' => $file['error'],
            'size' => $file['size']
        );
        
        // Upload del file
        $attachment_id = media_handle_sideload($upload_data, 0);
        
        if (is_wp_error($attachment_id)) {
            wp_send_json_error(array('message' => $attachment_id->get_error_message()));
        }
        
        // Ottieni i dati dell'attachment
        $attachment = get_post($attachment_id);
        $attachment_url = wp_get_attachment_url($attachment_id);
        $attachment_thumb = wp_get_attachment_image_url($attachment_id, 'thumbnail');
        
        // Risposta con i dati dell'immagine
        wp_send_json_success(array(
            'id' => $attachment_id,
            'url' => $attachment_url,
            'thumbnail' => $attachment_thumb,
            'alt' => get_post_meta($attachment_id, '_wp_attachment_image_alt', true),
            'title' => $attachment->post_title,
            'caption' => $attachment->post_excerpt
        ));
    }
    
    /**
     * Ottiene la descrizione di un lavoro
     */
    public static function get_lavoro_description($post_id) {
        return get_post_meta($post_id, self::META_DESCRIPTION, true);
    }
    
    /**
     * Ottiene la galleria di un lavoro
     */
    public static function get_lavoro_gallery($post_id) {
        $gallery = get_post_meta($post_id, self::META_GALLERY, true);
        return is_array($gallery) ? $gallery : array();
    }
    
    /**
     * Ottiene le immagini della galleria con dati completi
     */
    public static function get_lavoro_gallery_images($post_id, $size = 'full') {
        $gallery_ids = self::get_lavoro_gallery($post_id);
        $images = array();
        
        foreach ($gallery_ids as $image_id) {
            $image_data = wp_get_attachment_image_src($image_id, $size);
            if ($image_data) {
                $images[] = array(
                    'id' => $image_id,
                    'url' => $image_data[0],
                    'width' => $image_data[1],
                    'height' => $image_data[2],
                    'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
                    'caption' => wp_get_attachment_caption($image_id),
                );
            }
        }
        
        return $images;
    }
}