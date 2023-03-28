<?php

add_action("mesmerize_customize_register_options", 'mesmerize_offscreen_menu_settings', 1);

function mesmerize_offscreen_menu_settings()
{
    $prefix   = "header_offscreen_nav";
    $section  = "navigation_offscreen";
    $priority = 1;

    mesmerize_add_kirki_field(array(
        'type'     => 'sectionseparator',
        'label'    => esc_html__('Offscreen Menu Settings', 'mesmerize'),
        'settings' => "{$prefix}_settings_separator",
        'section'  => $section,
        'priority' => $priority,
    ));

    mesmerize_add_kirki_field(array(
        'type'      => 'checkbox',
        'settings'  => "{$prefix}_on_tablet",
        'label'     => esc_html__('Show offscreen navigation on tablet', 'mesmerize'),
        'section'   => $section,
        'default'   => false,
        'priority'  => $priority,
        'transport' => 'postMessage',
    ));

    mesmerize_add_kirki_field(array(
        'type'      => 'checkbox',
        'settings'  => "{$prefix}_on_desktop",
        'label'     => esc_html__('Show offscreen navigation on desktop', 'mesmerize'),
        'section'   => $section,
        'default'   => false,
        'priority'  => $priority,
        'transport' => 'postMessage',
    ));

    mesmerize_add_kirki_field(array(
        'type'      => 'ope-info-pro',
        'label'     => esc_html__('More colors and typography options available in PRO. @BTN@', 'mesmerize'),
        'section'   => $section,
        'priority'  => $priority,
        'settings'  => "{$prefix}_offscreen_pro_info",
        'default'   => true,
        'transport' => 'postMessage',
    ));

}



// APPLY OFFSCREEN FILTERS


add_filter('body_class', function ($classes) {
    $prefix = "header_offscreen_nav";

    $offscreen_on_tablet  = get_theme_mod("{$prefix}_on_tablet", false);
    $offscreen_on_desktop = get_theme_mod("{$prefix}_on_desktop", false);

    if (intval($offscreen_on_desktop)) {
        $classes[] = "offcanvas_menu-desktop";
    }
    if (intval($offscreen_on_tablet)) {
        $classes[] = "offcanvas_menu-tablet";
    }

    return $classes;
});
