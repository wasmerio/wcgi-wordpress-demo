<?php

require_once get_template_directory() . "/inc/general-options/colors.php";
require_once get_template_directory() . "/inc/general-options/companion-options.php";
require_once get_template_directory() . "/inc/general-options/page-settings.php";


add_action('customize_register', 'mesmerize_pro_section_button');


function mesmerize_pro_section_button($wp_customize)
{
    /** @var WP_Customize_Manager $wp_customize */
    $wp_customize->add_section(new Mesmerize\Info_PRO_Section(
        $wp_customize,
        'mesmerize-pro',
        array(
            "priority"   => 0,
            'capability' => "edit_theme_options",
        )));
}


add_filter('embed_defaults', 'mesmerize_embed_defaults_filter');

function mesmerize_embed_defaults_filter($size)
{

    $size['width']  = "1920";
    $size['height'] = "1080";

    return $size;
}

add_filter('embed_oembed_html', function ($result) {
    return "<div class='embed-container'>{$result}</div>";
});
