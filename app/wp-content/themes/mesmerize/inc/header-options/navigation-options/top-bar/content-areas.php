<?php

mesmerize_require("/inc/header-options/navigation-options/top-bar/content-types/information.php");
mesmerize_require("/inc/header-options/navigation-options/top-bar/content-types/social-icons.php");

function mesmerize_get_content_types() {
    $types['none'] = esc_html__("None", 'mesmerize');
    $types = apply_filters("mesmerize_get_content_types", $types);
    return $types;
}

function mesmerize_get_content_types_options() {
    $options = apply_filters("mesmerize_get_content_types_options", array());
    return $options;
}


add_action("mesmerize_top_bar_options_after", "mesmerize_add_content_areas_options");


function mesmerize_add_content_areas_options($section)
{

    $areas   = array('area-left', 'area-right');
    $content_types = mesmerize_get_content_types();
    $content_types_options = mesmerize_get_content_types_options();

    $options = array(
        'area-left' => array(
            'title'   => esc_html__('Top bar left area', 'mesmerize'),
            'default' => 'info'
        ),

        'area-right' => array(
            'title'   => esc_html__('Top bar right area', 'mesmerize'),
            'default' => 'social'
        ),
    );

    $priority = 0;

    foreach ($areas as $area) {

        $prefix   = "header_top_bar_" . $area;
        $priority += 5;
        $default = $options[$area]['default'];

        mesmerize_add_kirki_field(array(
            'type'     => 'sectionseparator',
            'label'    => esc_attr($options[$area]['title']),
            'section'  => $section,
            'settings' => "{$prefix}_sep",
            'priority' => $priority,
            'active_callback' => array(
                array(
                    'setting'  => "enable_top_bar",
                    'operator' => '==',
                    'value'    => true,
                ),
            ),
        ));

        mesmerize_add_kirki_field(array(
            'type'     => 'select',
            'settings' => $prefix . '_content',
            'section'  => $section,
            'label'    => esc_html__('Type', 'mesmerize'),
            'default'  => $default,
            'choices'  => $content_types,
            'priority' => $priority,
            'active_callback' => array(
                array(
                    'setting'  => "enable_top_bar",
                    'operator' => '==',
                    'value'    => true,
                ),
            ),

        ));

        $options_functions = array();

        foreach ($content_types_options as $type => $options_fct) {
            $options_functions[$options_fct] = array(
                $area, $section, $priority, $prefix
            );
        }

        mesmerize_add_options_group($options_functions);

    }
}


