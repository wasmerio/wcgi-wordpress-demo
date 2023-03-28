<?php


add_action("mesmerize_header_background_overlay_settings", "mesmerize_front_page_header_bottom_arrow_settings", 1, 5);

function mesmerize_front_page_header_bottom_arrow_settings($section, $prefix, $group, $inner, $priority)
{
    
    if ($inner) {
        return;
    }
    
    $priority = 5;
    
    $prefix  = $inner ? "inner_header_" : "header_";
    $section = "header_background_chooser";
    
    $group = "{$prefix}bottom_arrow_options_group_button";
    
    mesmerize_add_kirki_field(array(
        'type'            => 'checkbox',
        'settings'        => 'header_show_bottom_arrow',
        'label'           => esc_html__('Use Bottom Arrow', 'mesmerize'),
        'section'         => $section,
        'priority'        => $priority,
        'default'         => false,
        'transport'       => 'postMessage',
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter', array(), $inner),
    ));
    
    mesmerize_add_kirki_field(array(
        'type'            => 'sidebar-button-group',
        'settings'        => $group,
        'label'           => esc_html__('Options', 'mesmerize'),
        'section'         => $section,
        'priority'        => $priority,
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter',
            array(
                array(
                    'setting'  => 'header_show_bottom_arrow',
                    'operator' => '==',
                    'value'    => true,
                ),
            ),
            $inner
        ),
        'in_row_with'     => array('header_show_bottom_arrow'),
    ));
    
    mesmerize_add_kirki_field(array(
        'type'            => 'checkbox',
        'settings'        => 'header_bounce_bottom_arrow',
        'label'           => esc_html__('Bounce arrow', 'mesmerize'),
        'section'         => $section,
        'default'         => true,
        'priority'        => $priority,
        'transport'       => 'postMessage',
        'active_callback' => array(
            array(
                'setting'  => 'header_show_bottom_arrow',
                'operator' => '==',
                'value'    => true,
            ),
        ),
        'group'           => $group,
    ));
    
    mesmerize_add_kirki_field(array(
        'type'            => 'font-awesome-icon-control',
        'settings'        => 'header_bottom_arrow',
        'label'           => esc_html__('Icon', 'mesmerize'),
        'section'         => $section,
        'default'         => 'fa-angle-down',
        'priority'        => $priority,
        'transport'       => 'postMessage',
        'active_callback' => array(
            array(
                'setting'  => 'header_show_bottom_arrow',
                'operator' => '==',
                'value'    => true,
            ),
        ),
        'group'           => $group,
    ));
    
    mesmerize_add_kirki_field(array(
        'type'            => 'slider',
        'settings'        => 'header_size_bottom_arrow',
        'label'           => esc_html__('Icon Size', 'mesmerize'),
        'section'         => $section,
        'default'         => '50',
        'choices'         => array(
            'min'  => '10',
            'max'  => '100',
            'step' => '1',
        ),
        'priority'        => $priority,
        'output'          => array(
            array(
                'element'       => '.header-homepage-arrow',
                'property'      => 'font-size',
                'value_pattern' => 'calc( $px * 0.84 )',
            ),
            array(
                'element'  => '.header-homepage-arrow > i.fa',
                'property' => 'width',
                'suffix'   => 'px',
            ),
            array(
                'element'  => '.header-homepage-arrow > i.fa',
                'property' => 'height',
                'suffix'   => 'px',
            ),
        ),
        'transport'       => 'postMessage',
        'active_callback' => array(
            array(
                'setting'  => 'header_show_bottom_arrow',
                'operator' => '==',
                'value'    => true,
            ),
        ),
        'group'           => $group,
    ));
    
    
    mesmerize_add_kirki_field(array(
        'type'            => 'slider',
        'settings'        => 'header_offset_bottom_arrow',
        'label'           => esc_html__('Icon Bottom Offset', 'mesmerize'),
        'section'         => $section,
        'default'         => '20',
        'choices'         => array(
            'min'  => '0',
            'max'  => '200',
            'step' => '1',
        ),
        'priority'        => $priority,
        'output'          => array(
            array(
                'element'  => '.header-homepage-arrow',
                'property' => 'bottom',
                'suffix'   => 'px',
            ),
        ),
        'transport'       => 'postMessage',
        'active_callback' => array(
            array(
                'setting'  => 'header_show_bottom_arrow',
                'operator' => '==',
                'value'    => true,
            ),
        ),
        'group'           => $group,
    ));
    
    mesmerize_add_kirki_field(array(
        'type'            => 'color',
        'settings'        => 'header_color_bottom_arrow',
        'label'           => esc_html__('Icon Color', 'mesmerize'),
        'section'         => $section,
        'default'         => '#ffffff',
        'choices'         => array(
            'alpha' => true,
        ),
        'priority'        => $priority,
        'output'          => array(
            array(
                'element'  => '.header-homepage-arrow > i',
                'property' => 'color',
            ),
        ),
        'transport'       => 'postMessage',
        'active_callback' => array(
            array(
                'setting'  => 'header_show_bottom_arrow',
                'operator' => '==',
                'value'    => true,
            ),
        ),
        'group'           => $group,
    ));
    
    mesmerize_add_kirki_field(array(
        'type'            => 'color',
        'settings'        => 'header_background_bottom_arrow',
        'label'           => esc_html__('Icon Background Color', 'mesmerize'),
        'section'         => $section,
        'default'         => 'rgba(255,255,255,0)',
        'choices'         => array(
            'alpha' => true,
        ),
        'priority'        => $priority,
        'output'          => array(
            array(
                'element'  => '.header-homepage-arrow',
                'property' => 'background',
            ),
        ),
        'transport'       => 'postMessage',
        'active_callback' => array(
            array(
                'setting'  => 'header_show_bottom_arrow',
                'operator' => '==',
                'value'    => true,
            ),
        ),
        'group'           => $group,
    ));
    
}


function mesmerize_header_bottom_arrow()
{
    $show   = get_theme_mod('header_show_bottom_arrow', mesmerize_mod_default('header_show_bottom_arrow', false));
    $bounce = get_theme_mod('header_bounce_bottom_arrow', mesmerize_mod_default('header_bounce_bottom_arrow', true));
    
    $class       = "header-homepage-arrow ";
    $hidden_attr = "";
    
    if ( ! $show) {
        $hidden_attr = "data-reiki-hidden='true'";
    }
    
    if ($bounce) {
        $class .= "move-down-bounce";
    }
    
    if (mesmerize_is_customize_preview() || ( ! mesmerize_is_customize_preview() && $show)) {
        $icon = get_theme_mod('header_bottom_arrow', "fa-angle-down");
        ?>
        <div class="header-homepage-arrow-c" <?php echo $hidden_attr; ?>>
            <span class="<?php echo esc_attr($class); ?>"> <i class="fa arrow <?php echo esc_attr($icon); ?>" aria-hidden="true"></i>
            </span>
        </div>
        <?php
    }
}

add_action('mesmerize_after_header_content', 'mesmerize_header_bottom_arrow');
