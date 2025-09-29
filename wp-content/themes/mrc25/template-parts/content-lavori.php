<?php
/**
 * Template part for displaying single posts
 *
 * @package MRC25
 */

// Includi le funzioni di visualizzazione della galleria
require_once WP_CONTENT_DIR . '/plugins/mrc/modules/lavori/template-example.php';
require_once WP_CONTENT_DIR . '/plugins/mrc/modules/lavori/example-carousel.php';

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <header class="entry-header entry-header-full">
        <?php 
        $thumbnail_id = get_post_thumbnail_id(9);
        if ($thumbnail_id) : ?>
            <div class="post-thumbnail">
                <?php echo wp_get_attachment_image($thumbnail_id, 'mrc25-featured'); ?>
            </div>
        <?php endif; ?>

        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
    </header><!-- .entry-header -->
    <!-- .entry-header -->

    <div class="entry-content">
        <?php if (mrc_has_lavoro_gallery()) {
            $images = mrc_get_lavoro_gallery_images(get_the_ID(), 'large');
            // Ottieni la galleria (carousel + lightbox)
            mrc_display_lavoro_with_carousel(get_the_ID());
            mrc_display_gallery_lightbox($images);

        } else {
            $description = mrc_get_lavoro_description(get_the_ID());
            echo '<p class="lavoro-description">Nessuna immagine disponibile</p>';
        } ?>
    </div><!-- .entry-content -->



    <footer class="entry-footer">

        <?php if (get_edit_post_link()) : ?>
            <div class="entry-footer-edit">
                <?php
                edit_post_link(
                    sprintf(
                        wp_kses(
                            /* translators: %s: Name of current post. Only visible to screen readers */
                            __('Modifica <span class="screen-reader-text">%s</span>', 'mrc25'),
                            array(
                                'span' => array(
                                    'class' => array(),
                                ),
                            )
                        ),
                        wp_kses_post(get_the_title())
                    ),
                    '<span class="edit-link">',
                    '</span>'
                );
                ?>
            </div>
        <?php endif; ?>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> --> 