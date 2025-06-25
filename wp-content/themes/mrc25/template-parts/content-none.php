<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @package MRC25
 */
?>

<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e('Nessun contenuto trovato', 'mrc25'); ?></h1>
    </header><!-- .page-header -->

    <div class="page-content">
        <?php
        if (is_home() && current_user_can('publish_posts')) :

            printf(
                '<p>' . wp_kses(
                    /* translators: 1: link to WP admin new post page. */
                    __('Pronto a pubblicare il tuo primo post? <a href="%1$s">Inizia qui</a>.', 'mrc25'),
                    array(
                        'a' => array(
                            'href' => array(),
                        ),
                    )
                ) . '</p>',
                esc_url(admin_url('post-new.php'))
            );

        elseif (is_search()) :
            ?>

            <p><?php esc_html_e('Spiacenti, ma nulla corrisponde ai tuoi termini di ricerca. Prova con parole diverse.', 'mrc25'); ?></p>
            <?php
            get_search_form();

        else :
            ?>

            <p><?php esc_html_e('Sembra che non riusciamo a trovare quello che stai cercando. Forse la ricerca può aiutare.', 'mrc25'); ?></p>
            <?php
            get_search_form();

        endif;
        ?>
    </div><!-- .page-content -->
</section><!-- .no-results --> 