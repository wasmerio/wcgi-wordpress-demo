<?php

add_action("mesmerize_header_background_overlay_settings", "mesmerize_front_page_header_general_settings", 4, 5);

function mesmerize_front_page_header_general_settings($section, $prefix, $group, $inner, $priority)
{

    if ($inner) return;

    $priority = 5;
    $prefix   = "header";
    $section  = "header_background_chooser";
    $group = "";

    mesmerize_add_kirki_field(array(
        'type'      => 'checkbox',
        'label'     => esc_html__('Full Height Background', 'mesmerize'),
        'settings'  => 'full_height_header',
        'default'   => mesmerize_mod_default('full_height_header'),
        'transport' => 'postMessage',
        'section'   => $section,
        'priority'  => $priority,
        'group' => $group,
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter', array(), $inner),
    ));

}
