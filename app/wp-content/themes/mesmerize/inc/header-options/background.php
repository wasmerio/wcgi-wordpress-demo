<?php

require_once get_template_directory() . "/inc/header-options/background-options/background-type.php";
require_once get_template_directory() . "/inc/header-options/background-options/overlay-type.php";
require_once get_template_directory() . "/inc/header-options/background-options/header-separator.php";
require_once get_template_directory() . "/inc/header-options/background-options/general.php";
require_once get_template_directory() . "/inc/header-options/background-options/bottom-arrow.php";



function mesmerize_header_background_settings($inner)
{
    $prefix  = $inner ? "inner_header" : "header";
    $section = $inner ? "header_image" : "header_background_chooser";

    $group = "{$prefix}_bg_options_group_button";

    $priority = 1;
    mesmerize_add_kirki_field(array(
        'type'     => 'sectionseparator',
        'label'    => __('Background', 'mesmerize'),
        'section'  => $section,
        'priority' => $priority,
        'settings' => $prefix . "_header_1",
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter', array(), $inner),
    ));

    do_action("mesmerize_header_background_settings", $section, $prefix, $group, $inner, $priority);
}


add_action("mesmerize_customize_register_options", function () {
    mesmerize_header_background_settings(false);
    mesmerize_header_background_settings(true);
});


/*
    template functions
*/

add_filter("mesmerize_header_background_atts", function ($attrs, $bg_type, $inner) {
    if ( ! $inner) {
        $full_height_header = get_theme_mod('full_height_header', mesmerize_mod_default('full_height_header'));

        if ($full_height_header) {
            $attrs['class'] .= " header-full-height";
        }
    }

    return $attrs;
}, 1, 3);


function mesmerize_header_background_atts()
{
    $inner = mesmerize_is_inner(true);
    $attrs = array(
        'class' => $inner ? "header " : "header-homepage ",
        'style' => "",
    );

    $prefix = $inner ? "inner_header" : "header";
    $bgType = get_theme_mod($prefix . '_background_type', mesmerize_mod_default($prefix . '_background_type'));
    $bgType = apply_filters('mesmerize_' . $prefix . '_background_type', $bgType);

    do_action("mesmerize_background", $bgType, $inner, $prefix);

    $attrs = apply_filters('mesmerize_header_background_atts', $attrs, $bgType, $inner);

    $result = "";
    foreach ($attrs as $key => $value) {
        $value  = trim(esc_attr($value));
        $result .= " {$key}='" . esc_attr($value) . "'";
    }

    return $result;
}
