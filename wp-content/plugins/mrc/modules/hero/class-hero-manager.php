<?php
/**
 * Hero Manager
 *
 * @package MRC
 */

if (!defined('ABSPATH')) {
    exit;
}

class MRC_Hero_Manager {
    public function __construct() {
        add_action('init', array($this, 'register_hero_slide_cpt'));
        add_shortcode('mrc_hero_carousel', array($this, 'render_hero_carousel'));
        
        // Admin columns
        add_filter('manage_hero-slide_posts_columns', array($this, 'add_admin_columns'));
        add_action('manage_hero-slide_posts_custom_column', array($this, 'render_admin_columns'), 10, 2);
        add_filter('manage_edit-hero-slide_sortable_columns', array($this, 'make_columns_sortable'));
        
        // Regenerate hero carousel images when a hero slide is updated
        add_action('save_post_hero-slide', array($this, 'regenerate_hero_image'), 10, 2);
        
        // Add admin page actions
        add_action('admin_notices', array($this, 'add_regenerate_button'));
    }

    public function register_hero_slide_cpt() {
        $labels = array(
            'name'               => _x('Hero Slides', 'post type general name', 'mrc'),
            'singular_name'      => _x('Hero Slide', 'post type singular name', 'mrc'),
            'menu_name'          => _x('Hero Slides', 'admin menu', 'mrc'),
            'name_admin_bar'     => _x('Hero Slide', 'add new on admin bar', 'mrc'),
            'add_new'            => _x('Aggiungi Nuova', 'hero slide', 'mrc'),
            'add_new_item'       => __('Aggiungi Nuova Slide', 'mrc'),
            'new_item'           => __('Nuova Slide', 'mrc'),
            'edit_item'          => __('Modifica Slide', 'mrc'),
            'view_item'          => __('Visualizza Slide', 'mrc'),
            'all_items'          => __('Tutte le Slides', 'mrc'),
            'search_items'       => __('Cerca Slides', 'mrc'),
            'not_found'          => __('Nessuna slide trovata.', 'mrc'),
            'not_found_in_trash' => __('Nessuna slide trovata nel cestino.', 'mrc'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-images-alt2',
            'supports'           => array('title', 'thumbnail', 'page-attributes'),
            'hierarchical'       => false,
            'exclude_from_search'=> true,
            'publicly_queryable' => false,
            'show_in_nav_menus'  => false,
        );
        register_post_type('hero-slide', $args);
    }

    public function add_admin_columns($columns) {
        $new_columns = array();
        
        // Aggiungi checkbox
        $new_columns['cb'] = $columns['cb'];
        
        // Aggiungi colonna thumbnail
        $new_columns['thumbnail'] = __('Immagine', 'mrc');
        
        // Aggiungi titolo
        $new_columns['title'] = $columns['title'];
        
        // Aggiungi colonna ordine
        $new_columns['menu_order'] = __('Ordine', 'mrc');
        
        // Aggiungi data
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }

    public function render_admin_columns($column, $post_id) {
        switch ($column) {
            case 'thumbnail':
                if (has_post_thumbnail($post_id)) {
                    $thumbnail_id = get_post_thumbnail_id($post_id);
                    $thumbnail_url = wp_get_attachment_image_url($thumbnail_id, 'thumbnail');
                    echo '<img src="' . esc_url($thumbnail_url) . '" style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px;" alt="Thumbnail">';
                } else {
                    echo '<span style="color: #999; font-style: italic;">Nessuna immagine</span>';
                }
                break;
                
            case 'menu_order':
                $post = get_post($post_id);
                echo '<strong>' . esc_html($post->menu_order) . '</strong>';
                break;
        }
    }

    public function make_columns_sortable($columns) {
        $columns['menu_order'] = 'menu_order';
        return $columns;
    }

    public function render_hero_carousel($atts) {
        $args = array(
            'post_type'      => 'hero-slide',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        );
        $slides = get_posts($args);
        if (empty($slides)) return '';

        ob_start();
        ?>
        <div class="mrc-hero-carousel">
            <?php foreach ($slides as $i => $slide) :
                $img_id = get_post_thumbnail_id($slide->ID);
                $img_url = '';
                
                if ($img_id) {
                    // Try to get the custom hero carousel size first
                    $img_url = wp_get_attachment_image_url($img_id, 'mrc25-hero-carousel');
                    
                    // Fallback to full size if custom size doesn't exist
                    if (!$img_url) {
                        $img_url = wp_get_attachment_image_url($img_id, 'full');
                    }
                }
                
                if (!$img_url) continue;
                ?>
                <div class="mrc-hero-slide<?php echo $i === 0 ? ' active' : ''; ?>" 
                     style="background-image:url('<?php echo esc_url($img_url); ?>');"
                     data-title="<?php echo esc_attr($slide->post_title); ?>">
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function regenerate_hero_image($post_id, $post) {
        // Skip autosaves and revisions
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (wp_is_post_revision($post_id)) return;
        
        // Only regenerate if the post has a thumbnail
        if (has_post_thumbnail($post_id)) {
            $thumbnail_id = get_post_thumbnail_id($post_id);
            
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

    public function add_regenerate_button() {
        global $pagenow, $post_type;
        
        // Only show on hero-slide admin pages
        if ($pagenow === 'edit.php' && $post_type === 'hero-slide') {
            $regenerate_url = wp_nonce_url(
                admin_url('edit.php?post_type=hero-slide&action=regenerate_hero_images'),
                'regenerate_hero_images'
            );
            
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p><strong>Hero Carousel:</strong> Se le immagini non si visualizzano correttamente, ';
            echo '<a href="' . esc_url($regenerate_url) . '" class="button button-secondary">Rigenera Immagini Carousel</a></p>';
            echo '</div>';
        }
    }
} 