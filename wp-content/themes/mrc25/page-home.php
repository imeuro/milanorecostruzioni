<?php
/**
 * Template Name: Home
 *
 * @package MRC25
 */

get_header(); ?>

<?php echo do_shortcode('[mrc_hero_carousel]'); ?>

<main id="primary" class="site-main">
    <section class="mrc-overview">
        <div class="container">
            <?php
            while (have_posts()) :
                the_post();
                the_content();
            endwhile;
            ?>
        </div>
    </section>
    
    <!-- Sezione Attività -->
    <section class="mrc-activities">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Le Nostre Attività</h2>
                <p class="section-subtitle">Specialisti nel frazionamento e costruzione di qualità</p>
            </div>
            <div class="activities-grid">
                <div class="activity-card">
                    <div class="activity-icon">
                        <i class="fa-solid fa-ruler-combined"></i>
                    </div>
                    <h3>Razionalizzazione</h3>
                    <p>Trasformiamo spazi esistenti in ambienti moderni e funzionali, rispettando l'identità architettonica.</p>
                    <a href="<?php echo get_permalink(8); ?>#ristrutturazioni" class="activity-link">Scopri di più</a>
                </div>
                <div class="activity-card">
                    <div class="activity-icon">
                        <i class="fa-regular fa-building"></i>
                    </div>
                    <h3>Frazionamento</h3>
                    <p>Suddivisione strategica di immobili in unità abitative più piccole, massimizzando il valore della proprietà.</p>
                    <a href="<?php echo get_permalink(8); ?>" class="activity-link">Scopri di più</a>
                </div>
                <div class="activity-card">
                    <div class="activity-icon">
                       <i class="fa-solid fa-screwdriver-wrench"></i>
                    </div>
                    <h3>Materiali & finiture</h3>
                    <p>Selezione di materiali di finitura esclusivi e di alta qualità per valorizzare al meglio il tuo immobile.</p>
                    <a href="<?php echo get_permalink(8); ?>#materiali" class="activity-link">Scopri di più</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Sezione Portfolio -->
    <section class="mrc-portfolio">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">I Nostri Lavori</h2>
                <p class="section-subtitle">Portfolio dei progetti realizzati con passione e professionalità</p>
            </div>
            <div class="portfolio-grid">
                <?php
                $portfolio_query = new WP_Query(array(
                    'post_type' => 'lavori',
                    'posts_per_page' => 6,
                    'post_status' => 'publish'
                ));
                
                if ($portfolio_query->have_posts()) :
                    while ($portfolio_query->have_posts()) : $portfolio_query->the_post();
                        ?>
                        <div class="portfolio-item">
                            <div class="portfolio-image">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large'); ?>
                                <?php else : ?>
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/placeholder.jpg" alt="<?php the_title(); ?>">
                                <?php endif; ?>
                                <div class="portfolio-overlay">
                                    <div class="portfolio-content">
                                        <h3><?php the_title(); ?></h3>
                                        <p><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                                        <a href="<?php the_permalink(); ?>" class="portfolio-link">Vedi Progetto</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="portfolio-placeholder">
                        <p>Disponibili a breve...</p>
                    </div>
                    <?php
                endif;
                ?>
            </div>
            <div class="portfolio-cta">
                <a href="<?php echo get_post_type_archive_link('lavori'); ?>" class="btn-primary">Vedi Tutti i Progetti</a>
            </div>
        </div>
    </section>
</main><!-- #main -->

<?php get_footer(); ?> 