<?php


require_once get_template_directory() . "/inc/header-options/background-options/background-types/color.php";
require_once get_template_directory() . "/inc/header-options/background-options/background-types/image.php";
require_once get_template_directory() . "/inc/header-options/background-options/background-types/slideshow.php";
require_once get_template_directory() . "/inc/header-options/background-options/background-types/video.php";
require_once get_template_directory() . "/inc/header-options/background-options/background-types/gradient.php";

function mesmerize_header_background_type($inner)
{
    
    $prefix  = $inner ? "inner_header" : "header";
    $section = $inner ? "header_image" : "header_background_chooser";
    
    $group = "{$prefix}_bg_options_group_button";
    
    $priority = 2;
    
    /* background type dropdown */
    
    mesmerize_add_kirki_field(array(
        'type'              => 'select',
        'settings'          => $prefix . '_background_type',
        'label'             => esc_html__('Background Type', 'mesmerize'),
        'section'           => $section,
        'choices'           => apply_filters('mesmerize_header_background_types', array()),
        'default'           => mesmerize_mod_default($prefix . '_background_type'),
        'sanitize_callback' => 'sanitize_text_field',
        'priority'          => $priority,
        'active_callback'   => apply_filters('mesmerize_header_active_callback_filter', array(), $inner),
    ));
    
    $frontChoices = array();
    $innerChoices = array('header_image');
    
    mesmerize_add_kirki_field(array(
        'type'            => 'sidebar-button-group',
        'settings'        => $group,
        'label'           => esc_html__('Options', 'mesmerize'),
        'section'         => $section,
        'priority'        => $priority,
        'description'     => esc_html__('Options', 'mesmerize'),
        'in_row_with'     => array($prefix . '_background_type'),
        'choices'         => $inner ? $innerChoices : $frontChoices,
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter', array(), $inner),
    ));
    
    
    do_action("mesmerize_header_background_type_settings", $section, $prefix, $group, $inner, $priority);
}

add_action("mesmerize_customize_register_options", function () {
    mesmerize_header_background_type(false);
    mesmerize_header_background_type(true);
});

add_action('mesmerize_customize_register', function ($wp_customize) {
    /** @var WP_Customize_Manager $wp_customize */
    $wp_customize->get_control('header_image')->active_callback = 'mesmerize_inner_header_image_active_callback';
    $wp_customize->get_control('header_image')->priority        = 4;
    $wp_customize->get_setting('header_image')->transport       = 'postMessage';
    $wp_customize->get_setting('header_image_data')->transport  = 'postMessage';
    $wp_customize->get_setting('background_image')->transport  = 'refresh';
}, 1, 1);

function mesmerize_inner_header_image_active_callback()
{
    $currentInnerBgType = get_theme_mod('inner_header_background_type', mesmerize_mod_default('inner_header_background_type'));

    return ($currentInnerBgType === 'image');
}


add_filter('inner_header_bg_options_group_button_filter', function ($items) {
    $items = array(
        'inner_header_bg_color',
        'inner_header_bg_color_image',
        'inner_header_image_background_options_separator',
        'header_image',
        'inner_header_bg_position',
        'inner_header_show_featured_image',
        'inner_header_parallax',
        'inner_header_slideshow_background_options_separator',
        'inner_header_slideshow',
        'inner_header_slideshow_duration',
        'inner_header_slideshow_speed',
        'inner_header_video_background_options_separator',
        'inner_header_video',
        'inner_header_video_external',
        'inner_header_video_poster',
        'inner_header_gradient_background_options_separator',
        'inner_header_gradient',
    );
    
    return $items;
});
