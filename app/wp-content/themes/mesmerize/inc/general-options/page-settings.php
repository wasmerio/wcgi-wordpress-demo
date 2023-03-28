<?php

function mesmerize_smooth_scroll()
{
    $section = "page_settings";

    mesmerize_add_kirki_field(array(
        'type'     => 'checkbox',
        'settings' => 'enable_smooth_scroll',
        'label'    => esc_html__('Enable smooth scrolling', 'mesmerize'),
        'section'  => $section,
        'default'  => false,
        'transport' => 'postMessage',
    ));
}

//mesmerize_smooth_scroll();


add_action('wp_head', 'mesmerize_add_smooth_scroll');

function mesmerize_add_smooth_scroll()
{
    $enable_smooth_scroll = false;//get_theme_mod("enable_smooth_scroll", false);
    if ($enable_smooth_scroll) {
        $mesmerize_smooth_scroll = array("enabled" => true);
    	wp_localize_script(mesmerize_get_text_domain() . '-theme', 'mesmerize_smooth_scroll', $mesmerize_smooth_scroll);
    }
}
