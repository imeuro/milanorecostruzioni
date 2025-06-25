<?php
/**
 * The template for displaying all single posts
 *
 * @package MRC25
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();

            get_template_part('template-parts/content', 'single');

            // If comments are open or we have at least one comment, load up the comment template.
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;

            // Post navigation
            the_post_navigation(array(
                'prev_text' => '<span class="nav-subtitle">' . esc_html__('Precedente:', 'mrc25') . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . esc_html__('Successivo:', 'mrc25') . '</span> <span class="nav-title">%title</span>',
            ));

        endwhile; // End of the loop.
        ?>
    </div>
</main><!-- #main -->

<?php
get_footer(); 