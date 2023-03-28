<?php


add_action('customize_register', function ($wp_customize) {

    $panel = 'mesmerize_woocommerce_panel';

    $wp_customize->add_section(
        $panel,
        array(
            'capability' => 'edit_theme_options',
            'title'      => esc_html__('WooCommerce Options', 'mesmerize'),
        )
    );


    mesmerize_add_kirki_field(array(
        'type'     => 'ope-info',
        'label'    => mesmerize_wp_kses_post('Mesmerize theme is <b>WooCommerce ready</b>. After you install the <b>WooCommerce</b> plugin you will be able to customize the shop inside this section.', 'mesmerize'),
        'section'  => $panel,
        'settings' => "woocommerce_ready",
    ));

}, 10, 1);
