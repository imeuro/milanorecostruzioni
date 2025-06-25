<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package MRC25
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        <section class="error-404 not-found">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('Oops! Questa pagina non può essere trovata.', 'mrc25'); ?></h1>
            </header><!-- .page-header -->

            <div class="page-content">
                <p><?php esc_html_e('Sembra che nulla sia stato trovato in questa posizione. Forse prova uno dei link qui sotto o una ricerca?', 'mrc25'); ?></p>

                <?php
                get_search_form();

                the_widget('WP_Widget_Recent_Posts');
                ?>

                <div class="widget widget_categories">
                    <h2 class="widget-title"><?php esc_html_e('Categorie più utilizzate', 'mrc25'); ?></h2>
                    <ul>
                        <?php
                        wp_list_categories(array(
                            'orderby'    => 'count',
                            'order'      => 'DESC',
                            'show_count' => 1,
                            'title_li'   => '',
                            'number'     => 10,
                        ));
                        ?>
                    </ul>
                </div><!-- .widget -->

                <?php
                /* translators: %1$s: smiley */
                $mrc25_archive_content = '<p>' . sprintf(esc_html__('Prova a guardare negli archivi mensili. %1$s', 'mrc25'), convert_smilies(':)')) . '</p>';
                the_widget('WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$mrc25_archive_content");

                the_widget('WP_Widget_Tag_Cloud');
                ?>

            </div><!-- .page-content -->
        </section><!-- .error-404 -->
    </div>
</main><!-- #main -->

<?php
get_footer(); 