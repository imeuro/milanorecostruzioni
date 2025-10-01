<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;1,400&family=PT+Serif+Caption:ital@0;1&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        window.themeAssetsUrl = '<?php echo esc_js(get_template_directory_uri()); ?>/assets/img';
    </script>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Vai al contenuto', 'mrc25'); ?></a>

    <header id="masthead" class="site-header">
        <div class="header-flex">
            <div class="site-branding">
                <?php
                if (has_custom_logo()) :
                    // Mostra comunque il custom logo di WP, ma aggiungi l'id per JS
                    $custom_logo_id = get_theme_mod('custom_logo');
                    $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
                ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="custom-logo-link" rel="home" aria-current="page">
                        <img id="site-logo-desktop" width="80" height="64" src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/mrc-ico.svg" class="custom-logo" alt="Milano RE Costruzioni" decoding="async" fetchpriority="high" aria-label="Logo Milano RE Costruzioni">
                    </a>
                <?php else : ?>
                    <h1 class="site-title">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                            <?php bloginfo('name'); ?> 
                        </a>
                    </h1>
                    <?php
                    $mrc25_description = get_bloginfo('description', 'display');
                    if ($mrc25_description || is_customize_preview()) :
                        ?>
                        <p class="site-description"><?php echo $mrc25_description; ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <button id="mobile-menu-toggle" class="menu-toggle" aria-controls="mobile-menu" aria-expanded="false" aria-label="Apri menu">
                <span class="hamburger" aria-hidden="true"></span>
            </button>
            <nav id="site-navigation" class="main-navigation" aria-label="Menu principale desktop">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'menu_class'     => 'nav-menu',
                    'container'      => false,
                ));
                ?>
            </nav>
        </div>
    </header>

    <div id="mobile-menu-overlay" class="mobile-menu-overlay" tabindex="-1" aria-hidden="true"></div>
    <nav id="mobile-menu" class="mobile-menu" aria-label="Menu principale mobile">
        <div class="menu-branding">
            <?php
                if (has_custom_logo()) :
                    // Mostra comunque il custom logo di WP, ma aggiungi l'id per JS
                    $custom_logo_id = get_theme_mod('custom_logo');
                    $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
            ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="custom-logo-link" rel="home" aria-current="page">
                    <img id="site-logo-mobile" width="80" height="64" src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/mrc-ico.svg" class="custom-logo" alt="Milano RE Costruzioni" decoding="async" fetchpriority="high" aria-label="Logo Milano RE Costruzioni">
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="custom-logo-link" rel="home" aria-current="page">
                    <img id="site-logo-mobile" width="80" height="64" src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/mrc-ico.svg" class="custom-logo" alt="Milano RE Costruzioni" decoding="async" fetchpriority="high" aria-label="Logo Milano RE Costruzioni">
                </a>
            <?php endif; ?>
        </div>
        <?php
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'menu_id'        => 'mobile-menu-list',
            'menu_class'     => 'mobile-menu-list',
            'container'      => false,
        ));
        ?>
        <span class="mobile-menu-text-bottom">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></span>
    </nav>
</body>
</html> 