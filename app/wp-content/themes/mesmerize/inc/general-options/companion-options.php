<?php


function mesmerize_register_companion_control($wp_customize)
{
    \Mesmerize\Companion_Plugin::check_companion($wp_customize);
}

//add_action('customize_register', 'one_page_express_customize_register_controls');
add_action('customize_register', 'mesmerize_register_companion_control', 999);
