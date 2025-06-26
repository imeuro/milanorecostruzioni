    <footer id="colophon" class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-navigation">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'menu_id'        => 'footer-menu',
                        'menu_class'     => 'footer-menu',
                        'container'      => false,
                        'depth'          => 1,
                    ));
                    ?>
                </div>
                <div class="footer-societa">
                    <p>
                        <strong>MILANO RE COSTRUZIONI S.R.L.</strong><br>
                        Sede legale: VIA FONTANA 17 - 20122 - MILANO (MI)<br>
                        P.IVA/CF: 13800780960<br>
                        Rea: 2744518
                    </p>
                </div>
            </div>
            <div class="site-info">
                <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. 
                <?php esc_html_e('Tutti i diritti riservati.', 'mrc25'); ?>
                - <a href="<?php echo home_url('/privacy-policy'); ?>">Privacy Policy</a> - <a href="<?php echo home_url('/cookie-policy'); ?>">Cookie Policy</a>
            </p>
            </div><!-- .site-info -->
        </div><!-- .container -->
    </footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html> 