<?php


add_filter('mesmerize_header_background_types', 'mesmerize_header_background_gradient');

function mesmerize_header_background_gradient($types)
{
    $types['gradient'] = esc_html__('Gradient', 'mesmerize');
    return $types;
}


add_filter("mesmerize_header_background_atts", function ($attrs, $bg_type, $inner) {
    if ($bg_type == 'gradient') {
        $prefix         = $inner ? "inner_header" : "header";
        $bgGradient     = get_theme_mod($prefix . "_gradient", "plum_plate");
        $attrs['class'] .= " " . esc_attr($bgGradient);
    }

    return $attrs;
}, 1, 3);


add_filter("mesmerize_header_background_type_settings", 'mesmerize_header_background_type_gradient_settings', 2, 6);

function mesmerize_header_background_type_gradient_settings($section, $prefix, $group, $inner, $priority)
{

    mesmerize_add_kirki_field(array(
        'type'            => 'sectionseparator',
        'label'           => esc_html__('Gradient Background Options', 'mesmerize'),
        'section'         => $section,
        'settings'        => $prefix . '_gradient_background_options_separator',
        'priority'        => 2,
        'active_callback' => array(
            array(
                'setting'  => $prefix . '_background_type',
                'operator' => '==',
                'value'    => 'gradient',
            ),
        ),
        'group'           => $group,
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'web-gradients',
        'settings'        => $prefix . '_gradient',
        'label'           => esc_html__('Header Gradient', 'mesmerize'),
        'section'         => $section,
        'default'         => 'plum_plate',
        "priority"        => 2,
        'transport'       => 'postMessage',
        'active_callback' => array(
            array(
                'setting'  => $prefix . '_background_type',
                'operator' => '==',
                'value'    => 'gradient',
            ),
        ),
        'group'           => $group,
    ));

}
