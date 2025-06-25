<?php
/**
 * Template part for displaying posts
 *
 * @package MRC25
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php
        if (is_singular()) :
            the_title('<h1 class="entry-title">', '</h1>');
        else :
            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
        endif;

        if ('post' === get_post_type()) :
            ?>
            <div class="entry-meta">
                <span class="posted-on">
                    <?php
                    printf(
                        esc_html__('Pubblicato il %s', 'mrc25'),
                        '<time class="entry-date published updated" datetime="' . esc_attr(get_the_date(DATE_W3C)) . '">' . esc_html(get_the_date()) . '</time>'
                    );
                    ?>
                </span>
                <span class="byline">
                    <?php
                    printf(
                        esc_html__('da %s', 'mrc25'),
                        '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
                    );
                    ?>
                </span>
            </div><!-- .entry-meta -->
        <?php endif; ?>
    </header><!-- .entry-header -->

    <?php if (has_post_thumbnail() && !is_singular()) : ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('mrc25-thumbnail'); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="entry-content">
        <?php
        if (is_singular()) :
            the_content(
                sprintf(
                    wp_kses(
                        /* translators: %s: Name of current post. Only visible to screen readers */
                        __('Continua a leggere<span class="screen-reader-text"> "%s"</span>', 'mrc25'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    wp_kses_post(get_the_title())
                )
            );

            wp_link_pages(
                array(
                    'before' => '<div class="page-links">' . esc_html__('Pagine:', 'mrc25'),
                    'after'  => '</div>',
                )
            );
        else :
            the_excerpt();
            ?>
            <a href="<?php the_permalink(); ?>" class="read-more">
                <?php esc_html_e('Leggi di più', 'mrc25'); ?>
            </a>
        <?php
        endif;
        ?>
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <?php if ('post' === get_post_type()) : ?>
            <div class="entry-footer-meta">
                <?php
                $categories_list = get_the_category_list(esc_html__(', ', 'mrc25'));
                if ($categories_list) :
                    ?>
                    <span class="cat-links">
                        <?php
                        printf(
                            /* translators: 1: list of categories. */
                            esc_html__('Categorie: %1$s', 'mrc25'),
                            $categories_list
                        );
                        ?>
                    </span>
                <?php endif; ?>

                <?php
                $tags_list = get_the_tag_list('', esc_html_x(', ', 'list item separator', 'mrc25'));
                if ($tags_list) :
                    ?>
                    <span class="tags-links">
                        <?php
                        printf(
                            /* translators: 1: list of tags. */
                            esc_html__('Tag: %1$s', 'mrc25'),
                            $tags_list
                        );
                        ?>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> --> 