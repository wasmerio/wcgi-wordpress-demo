<?php

add_action("mesmerize_header_background_settings", function ($section, $prefix, $group, $inner, $priority) {
    mesmerize_header_separator_options($section, $prefix, $group, $inner, $priority);
}, 3, 5);


function mesmerize_header_separator_options($section, $prefix, $group, $inner, $priority)
{

    $priority = 4;
    $group    = "{$prefix}_options_separator_group_button";

    mesmerize_add_kirki_field(array(
        'type'     => 'checkbox',
        'label'    => esc_html__('Bottom Separator', 'mesmerize'),
        'section'  => $section,
        'settings' => $prefix . '_show_separator',
        'default'  => mesmerize_mod_default($prefix . '_show_separator'),
        'priority' => $priority,
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter', array(), $inner),
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'sidebar-button-group',
        'settings'        => $group,
        'label'           => esc_html__('Options', 'mesmerize'),
        'section'         => $section,
        'priority'        => $priority,
        'in_row_with'     => array($prefix . '_show_separator'),
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter',
            array(
                array(
                    'setting'  => $prefix . '_show_separator',
                    'operator' => '==',
                    'value'    => true,
                ),
            ),
            $inner
        ),
    ));


    mesmerize_add_kirki_field(array(
        'type'     => 'sectionseparator',
        'label'    => esc_html__('Bottom Separator Options', 'mesmerize'),
        'section'  => $section,
        'settings' => $prefix . '_separator_header_separator_2',
        'priority' => $priority,
        'group'    => $group,
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'select',
        'settings'        => $prefix . '_separator',
        'label'           => esc_html__('Type', 'mesmerize'),
        'section'         => $section,
        'default'         => mesmerize_mod_default($prefix . '_separator'),
        'choices'         => mesmerize_get_separators_list(),
        'priority'        => $priority,
        'active_callback' => array(
            array(
                'setting'  => $prefix . '_show_separator',
                'operator' => '==',
                'value'    => true,
            ),
        ),
        'group'           => $group,
    ));


    mesmerize_add_kirki_field(array(
        'type'     => 'color',
        'settings' => "{$prefix}_separator_color",
        'label'    => esc_attr__('Color', 'mesmerize'),
        'section'  => $section,
        'priority' => $priority,
        'choices'  => array(
            'alpha' => true,
        ),
        'default'  => mesmerize_mod_default("{$prefix}_separator_color"),
        'output'   => array(
            array(
                'element'  => $inner ? "body .header .svg-white-bg" : ".mesmerize-front-page .header-separator .svg-white-bg",
                'property' => 'fill',
                'suffix'   => '!important',
            ),


        ),

        'transport' => 'postMessage',
        'js_vars'   => array(
            array(
                'element'  => $inner ? "body .header .svg-white-bg" : ".mesmerize-front-page .header-separator .svg-white-bg",
                'property' => 'fill',
                'suffix'   => '!important',
            ),
        ),

        'active_callback' => array(
            array(
                'setting'  => $prefix . '_show_separator',
                'operator' => '==',
                'value'    => true,
            ),
        ),

        'group' => $group,
    ));


    mesmerize_add_kirki_field(array(
        'type'     => 'color',
        'settings' => "{$prefix}_separator_color_accent",
        'label'    => esc_attr__('Accent Color', 'mesmerize'),
        'section'  => $section,
        'priority' => $priority,
        'choices'  => array(
            'alpha' => true,
        ),
        'default'  => mesmerize_get_theme_colors('color2'),
        'output'   => array(
            array(
                'element'  => $inner ? ".mesmerize-inner-page .header .svg-accent" : ".mesmerize-front-page .header-separator path.svg-accent",
                'property' => 'stroke',
                'suffix'   => '!important',
            ),


        ),

        'transport' => 'postMessage',
        'js_vars'   => array(
            array(
                'element'  => $inner ? "body.page .header path.svg-accent" : ".mesmerize-front-page .header-separator path.svg-accent",
                'property' => 'stroke',
                'suffix'   => '!important',
            ),
        ),

        'active_callback' => array(
            array(
                'setting'  => $prefix . '_show_separator',
                'operator' => '==',
                'value'    => true,
            ),

            array(
                'setting'  => $prefix . '_separator',
                'operator' => 'in',
                'value'    => mesmerize_get_2_colors_separators(array(), true),
            ),
        ),
        'group'           => $group,
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'slider',
        'label'           => esc_html__('Height', 'mesmerize'),
        'section'         => $section,
        'settings'        => $prefix . '_separator_height',
        'default'         => mesmerize_mod_default($prefix . '_separator_height'),
        'transport'       => 'postMessage',
        'priority'        => $priority,
        'choices'         => array(
            'min'  => '0',
            'max'  => '400',
            'step' => '1',
        ),
        "output"          => array(
            array(
                "element"  => $inner ? ".header-separator svg" : ".mesmerize-front-page .header-separator svg",
                'property' => 'height',
                'suffix'   => '!important',
                'units'    => 'px',
            ),
        ),
        'js_vars'         => array(
            array(
                'element'  => $inner ? ".header-separator svg" : ".mesmerize-front-page .header-separator svg",
                'function' => 'css',
                'property' => 'height',
                'units'    => "px",
                'suffix'   => '!important',
            ),
        ),
        'active_callback' => array(
            array(
                'setting'  => $prefix . '_show_separator',
                'operator' => '==',
                'value'    => true,
            ),
        ),
        'group'           => $group,
    ));
}


function mesmerize_get_2_colors_separators($separators = array(), $onlyIDs = false)
{
    $separators = array_merge(
        $separators,
        array(
            'mesmerize/1.wave-and-line'          => esc_html__('Wave and line', 'mesmerize'),
            'mesmerize/1.wave-and-line-negative' => esc_html__('Wave and line Negative', 'mesmerize'),
        )
    );

    if ($onlyIDs) {
        return array_keys($separators);

    }

    return $separators;
}

function mesmerize_prepend_2_colors_separators($separators, $use_only_defaults)
{
    if ($use_only_defaults) {
        return $separators;
    }

    return mesmerize_get_2_colors_separators($separators);

}

add_filter('mesmerize_separators_list_prepend', 'mesmerize_prepend_2_colors_separators', 10, 2);


function mesmerize_get_separators_list($use_only_defaults = false)
{
    $extras = array(
        'mesmerize/3.waves-noCentric'           => esc_html__('Wave no centric', 'mesmerize'),
        'mesmerize/3.waves-noCentric-negative'  => esc_html__('Wave no centric Negative', 'mesmerize'),
        'mesmerize/4.clouds'                    => esc_html__('Clouds 2', 'mesmerize'),
        'mesmerize/5.triple-waves-3'            => esc_html__('Triple Waves 1', 'mesmerize'),
        'mesmerize/5.triple-waves-3-negative'   => esc_html__('Triple Waves 1 Negative', 'mesmerize'),
        'mesmerize/6.triple-waves-2'            => esc_html__('Triple Waves 2', 'mesmerize'),
        'mesmerize/6.triple-waves-2-negative'   => esc_html__('Triple Waves 2 Negative', 'mesmerize'),
        'mesmerize/7.stright-angles-1'          => esc_html__('Stright Angles 1', 'mesmerize'),
        'mesmerize/7.stright-angles-1-negative' => esc_html__('Stright Angles 1 Negative', 'mesmerize'),
        'mesmerize/8.stright-angles-2'          => esc_html__('Triple Waves 2', 'mesmerize'),
        'mesmerize/8.stright-angles-2-negative' => esc_html__('Triple Waves 2 Negative', 'mesmerize'),
    );


    $separators = array(
        'tilt'                           => esc_html__('Tilt', 'mesmerize'),
        'tilt-flipped'                   => esc_html__('Tilt Flipped', 'mesmerize'),
        'opacity-tilt'                   => esc_html__('Tilt Opacity', 'mesmerize'),
        'triangle'                       => esc_html__('Triangle', 'mesmerize'),
        'triangle-negative'              => esc_html__('Triangle Negative', 'mesmerize'),
        'triangle-asymmetrical'          => esc_html__('Triangle Asymmetrical', 'mesmerize'),
        'triangle-asymmetrical-negative' => esc_html__('Triangle Asymmetrical Negative', 'mesmerize'),
        'opacity-fan'                    => esc_html__('Fan Opacity', 'mesmerize'),
        'mountains'                      => esc_html__('Mountains', 'mesmerize'),
        'pyramids'                       => esc_html__('Pyramids', 'mesmerize'),
        'pyramids-negative'              => esc_html__('Pyramids Negative', 'mesmerize'),
        'waves'                          => esc_html__('Waves', 'mesmerize'),
        'waves-negative'                 => esc_html__('Waves Negative', 'mesmerize'),
        'wave-brush'                     => esc_html__('Waves Brush', 'mesmerize'),
        'waves-pattern'                  => esc_html__('Waves Pattern', 'mesmerize'),
        'clouds'                         => esc_html__('Clouds', 'mesmerize'),
        'clouds-negative'                => esc_html__('Clouds Negative', 'mesmerize'),
        'curve'                          => esc_html__('Curve', 'mesmerize'),
        'curve-negative'                 => esc_html__('Curve Negative', 'mesmerize'),
        'curve-asymmetrical'             => esc_html__('Curve Asymmetrical', 'mesmerize'),
        'curve-asymmetrical-negative'    => esc_html__('Curve Asymmetrical Negative', 'mesmerize'),
        'drops'                          => esc_html__('Drops', 'mesmerize'),
        'drops-negative'                 => esc_html__('Drops Negative', 'mesmerize'),
        'arrow'                          => esc_html__('Arrow', 'mesmerize'),
        'arrow-negative'                 => esc_html__('Arrow Negative', 'mesmerize'),
        'book'                           => esc_html__('Book', 'mesmerize'),
        'book-negative'                  => esc_html__('Book Negative', 'mesmerize'),
        'split'                          => esc_html__('Split', 'mesmerize'),
        'split-negative'                 => esc_html__('Split Negative', 'mesmerize'),
        'zigzag'                         => esc_html__('Zigzag', 'mesmerize'),
    );

    if ( ! $use_only_defaults) {
        $separators = array_merge($extras, $separators);
    }

    $prepend_separators = apply_filters('mesmerize_separators_list_prepend', array(), $use_only_defaults);
    $append_separators  = apply_filters('mesmerize_separators_list_append', array(), $use_only_defaults);


    $separators = array_merge($prepend_separators, $separators, $append_separators);

    return $separators;
}


function mesmerize_print_header_separator($prefix = null)
{
    $inner = mesmerize_is_inner(true);

    if ( ! $prefix) {
        $prefix = $inner ? "inner_header" : "header";
    }

    $show = get_theme_mod($prefix . '_show_separator', mesmerize_mod_default($prefix . '_show_separator'));
    if ($show) {

        $separator = get_theme_mod($prefix . '_separator', mesmerize_mod_default($prefix . '_separator'));

        $reverse = "";

        if (strpos($separator, "mesmerize/") !== false) {
            $reverse = strpos($separator, "-negative") === false ? "" : "header-separator-reverse";
        } else {
            $reverse = strpos($separator, "-negative") === false ? "header-separator-reverse" : "";
        }

        echo '<div class="header-separator header-separator-bottom ' . esc_attr($reverse) . '">';
        ob_start();

        // local svg as template ( ensure it will work with filters in child theme )
        locate_template("/assets/separators/" . $separator . ".svg", true, true);

        $content = ob_get_clean();
        echo $content;
        echo '</div>';

    }
}
