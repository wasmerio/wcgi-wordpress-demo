<?php

    add_filter("mesmerize_overlay_types", function($types) {
        $types['color'] = esc_html__('Color', 'mesmerize');
        return $types;
    });

    add_action("mesmerize_header_background_overlay_settings", function($section, $prefix, $group, $inner, $priority) {
        $header_class = $inner ? ".header" : ".header-homepage:not(.header-slide)";

        mesmerize_add_kirki_field(array(
            'type'    => 'color',
            'label'   => esc_html__('Overlay Color', 'mesmerize'),
            'section' => $section,

            'settings'  => $prefix . '_overlay_color',
            'default'   => mesmerize_mod_default($prefix . '_overlay_color'),
            'transport' => 'postMessage',
            'priority'  => $priority,
            'choices'   => array(
                'alpha' => false,
            ),

            "output" => array(
                array(
                    'element'  => $header_class . '.color-overlay:before',
                    'property' => 'background',
                ),
            ),

            'js_vars'         => array(
                array(
                    'element'  => $header_class . ".color-overlay:before",
                    'function' => 'css',
                    'property' => 'background',
                    'suffix'   => ' !important',
                ),
            ),
            'active_callback' => array(
                array(
                    'setting'  => $prefix . '_show_overlay',
                    'operator' => '==',
                    'value'    => true,
                ),
                array(
                    'setting'  => $prefix . '_overlay_type',
                    'operator' => '==',
                    'value'    => 'color',
                ),
            ),
            'group' => $group
        ));


        mesmerize_add_kirki_field(array(
            'type'      => 'slider',
            'label'     => esc_html__('Overlay Opacity', 'mesmerize'),
            'section'   => $section,
            'priority'  => $priority,
            'settings'  => $prefix . '_overlay_opacity',
            'default'   => mesmerize_mod_default($prefix . '_overlay_opacity'),
            'choices'   => array(
                'min'  => '0',
                'max'  => '1',
                'step' => '0.01',
            ),
            'transport' => 'postMessage',
            "output" => array(
                array(
                    'element'  => array(
                        $header_class . '.color-overlay::before',
                        $header_class . ' .background-overlay'
                    ),
                    'property' => 'opacity',
                ),
            ),

            'js_vars'         => array(
                array(
                    'element'  => array(
                        $header_class . '.color-overlay::before',
                        $header_class . ' .background-overlay'
                    ),
                    'function' => 'css',
                    'property' => 'opacity',
                    'suffix'   => ' !important',
                ),
            ),
            'active_callback' => array(
                array(
                    'setting'  => $prefix . '_show_overlay',
                    'operator' => '==',
                    'value'    => true,
                ),
                array(
                    'setting'  => $prefix . '_overlay_type',
                    'operator' => '==',
                    'value'    => 'color',
                ),
            ),
            'group' => $group
        ));
    }, 1, 5);

    
