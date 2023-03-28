<?php

add_filter('mesmerize_header_background_types', 'mesmerize_header_background_color');

function mesmerize_header_background_color($types)
{
    $types['color'] = esc_html__('Color', 'mesmerize');
    return $types;
}

add_filter("mesmerize_header_background_atts", function ($attrs, $bg_type, $inner) {
    $prefix       = $inner ? "inner_header" : "header";
    $show_overlay = get_theme_mod("" . $prefix . "_show_overlay", true);
    if ($show_overlay) {
        $attrs['class'] .= " color-overlay ";
    }

    if ($bg_type === 'color') {
        $color = get_theme_mod("{$prefix}_bg_color", '#6a73da');
        if ( ! isset($attrs['style'])) {
            $attrs['style'] = "";
        }
        $attrs['style'] .= "; background:" . esc_attr($color);
    }

    return $attrs;
}, 1, 3);


add_action('mesmerize_header_background_type_settings', 'mesmerize_header_background_type_color_settings', 1, 6);

function mesmerize_header_background_type_color_settings($section, $prefix, $group, $inner, $priority)
{
    $prefix  = $inner ? "inner_header" : "header";
    $section = $inner ? "header_image" : "header_background_chooser";

    $group        = "{$prefix}_bg_options_group_button";
    $header_class = $inner ? ".header" : ".header-homepage";

    mesmerize_add_kirki_field(array(
        'type'            => 'sectionseparator',
        'label'           => esc_html__('Color Background Options', 'mesmerize'),
        'section'         => $section,
        'settings'        => $prefix . '_color_background_options_separator',
        'priority'        => 2,
        'active_callback' => array(
            array(
                'setting'  => $prefix . '_background_type',
                'operator' => '==',
                'value'    => 'color',
            ),
        ),
        'group'           => $group,
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'color',
        'label'           => esc_html__('Background Color', 'mesmerize'),
        'section'         => $section,
        'priority'        => 2,
        'settings'        => $prefix . '_bg_color',
        'default'         => '#6a73da',
        'transport'       => 'postMessage',
        'js_vars'         => array(
            array(
                'element'  => $header_class,
                'property' => 'background',
                'suffix'   => ' !important',
            ),
        ),
        'active_callback' => array(
            array(
                'setting'  => $prefix . '_background_type',
                'operator' => '==',
                'value'    => 'color',
            ),
        ),
        'group'           => $group,
    ));
}
