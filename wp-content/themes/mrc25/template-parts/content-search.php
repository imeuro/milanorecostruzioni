<?php
/**
 * Template part for displaying posts in a search results
 *
 * @package MRC25
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('search-result'); ?>>
    <header class="entry-header">
        <?php the_title(sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>'); ?>

        <?php if ('post' === get_post_type()) : ?>
            <div class="entry-meta">
                <span class="posted-on">
                    <?php
                    printf(
                        esc_html__('Pubblicato il %s', 'mrc25'),
                        '<time class="entry-date published updated" datetime="' . esc_attr(get_the_date(DATE_W3C)) . '">' . esc_html(get_the_date()) . '</time>'
                    );
                    ?>
                </span>
            </div><!-- .entry-meta -->
        <?php endif; ?>
    </header><!-- .entry-header -->

    <div class="entry-summary">
        <?php the_excerpt(); ?>
        <a href="<?php the_permalink(); ?>" class="read-more">
            <?php esc_html_e('Leggi di più', 'mrc25'); ?>
        </a>
    </div><!-- .entry-summary -->

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
            </div>
        <?php endif; ?>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> --> 