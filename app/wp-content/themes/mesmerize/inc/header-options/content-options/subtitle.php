<?php

function mesmerize_front_page_header_subtitle_options($section, $prefix, $priority)
{
    $companion = apply_filters('mesmerize_is_companion_installed', false);
    
    
    mesmerize_add_kirki_field(array(
        'type'            => 'checkbox',
        'settings'        => 'header_content_show_subtitle',
        'label'           => esc_html__('Show subtitle', 'mesmerize'),
        'section'         => $section,
        'default'         => true,
        'priority'        => $priority,
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter', array(), false),
    ));
    
    mesmerize_add_kirki_field(array(
        'type'            => 'sidebar-button-group',
        'settings'        => 'header_content_subtitle_group',
        'label'           => esc_html__('Options', 'mesmerize'),
        'section'         => $section,
        'priority'        => $priority,
        "choices"         => array(
            "header_subtitle",
            "header_content_subtitle_typography",
            "header_content_subtitle_spacing",
        ),
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter', array(
            array(
                'setting'  => 'header_content_show_subtitle',
                'operator' => '==',
                'value'    => true,
            ),
        
        ), false),
        
        'in_row_with' => array('header_content_show_subtitle'),
    ));
    
    if ( ! $companion) {
        
        mesmerize_add_kirki_field(array(
            'type'              => 'textarea',
            'settings'          => 'header_subtitle',
            'label'             => esc_html__('Subtitle', 'mesmerize'),
            'section'           => $section,
            'default'           => "",
            'sanitize_callback' => 'wp_kses_post',
            'priority'          => $priority,
            'partial_refresh'   => array(
                'header_subtitle' => array(
                    'selector'        => ".header-homepage .header-subtitle",
                    'render_callback' => function () {
                        return get_theme_mod('header_subtitle');
                    },
                ),
            ),
        ));
    }
}


add_action("mesmerize_print_header_content", function () {
    mesmerize_print_header_subtitle();
}, 1);


function mesmerize_print_header_subtitle()
{
    $subtitle = get_theme_mod('header_subtitle', "");
    $show     = get_theme_mod('header_content_show_subtitle', true);
    
    if (mesmerize_can_show_demo_content()) {
        if ($subtitle == "") {
            $subtitle = esc_html__('You can set this subtitle from the customizer.', 'mesmerize');
        }
    } else {
        if ($subtitle == "") {
            $subtitle = get_bloginfo('description');
        }
    }
    if ($show) {
        printf('<p class="header-subtitle">%1$s</p>', mesmerize_wp_kses_post($subtitle));
    }
}
