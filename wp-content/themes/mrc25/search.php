<?php
/**
 * The template for displaying search results pages
 *
 * @package MRC25
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        <?php if (have_posts()) : ?>
            <header class="page-header">
                <h1 class="page-title">
                    <?php
                    printf(
                        /* translators: %s: search query. */
                        esc_html__('Risultati di ricerca per: %s', 'mrc25'),
                        '<span>' . get_search_query() . '</span>'
                    );
                    ?>
                </h1>
            </header><!-- .page-header -->

            <?php
            /* Start the Loop */
            while (have_posts()) :
                the_post();

                /*
                 * Run the loop for the search to output the results.
                 * If you want to override this in a child theme, then include a file
                 * called content-search.php and that will be used instead.
                 */
                get_template_part('template-parts/content', 'search');

            endwhile;

            the_posts_navigation();

        else :

            get_template_part('template-parts/content', 'none');

        endif;
        ?>
    </div>
</main><!-- #main -->

<?php
get_footer(); 