<?php
/**
 * MRC25 Theme Customizer
 *
 * @package MRC25
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function mrc25_customize_register($wp_customize) {
    $wp_customize->get_setting('blogname')->transport         = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport  = 'postMessage';
    $wp_customize->get_setting('header_textcolor')->transport = 'postMessage';

    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial(
            'blogname',
            array(
                'selector'        => '.site-title a',
                'render_callback' => 'mrc25_customize_partial_blogname',
            )
        );
        $wp_customize->selective_refresh->add_partial(
            'blogdescription',
            array(
                'selector'        => '.site-description',
                'render_callback' => 'mrc25_customize_partial_blogdescription',
            )
        );
    }

    // Add section for theme options
    $wp_customize->add_section(
        'mrc25_theme_options',
        array(
            'title'    => __('Opzioni Tema', 'mrc25'),
            'priority' => 130,
        )
    );

    // Add setting for primary color
    $wp_customize->add_setting(
        'mrc25_primary_color',
        array(
            'default'           => '#0073aa',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'mrc25_primary_color',
            array(
                'label'   => __('Colore Primario', 'mrc25'),
                'section' => 'mrc25_theme_options',
            )
        )
    );

    // Add setting for footer text
    $wp_customize->add_setting(
        'mrc25_footer_text',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'mrc25_footer_text',
        array(
            'label'   => __('Testo Footer', 'mrc25'),
            'section' => 'mrc25_theme_options',
            'type'    => 'text',
        )
    );
}
add_action('customize_register', 'mrc25_customize_register');

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function mrc25_customize_partial_blogname() {
    bloginfo('name');
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function mrc25_customize_partial_blogdescription() {
    bloginfo('description');
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function mrc25_customize_preview_js() {
    wp_enqueue_script('mrc25-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array('customize-preview'), '1.0.0', true);
}
add_action('customize_preview_init', 'mrc25_customize_preview_js'); 