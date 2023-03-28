<?php

add_action("mesmerize_customize_register_options", function () {
    mesmerize_navigation_general_options(false);
    mesmerize_navigation_general_options(true);
});


function mesmerize_navigation_general_options($inner = false)
{
    $priority       = 1;
    $section        = $inner ? 'inner_page_navigation' : 'front_page_navigation';
    $prefix         = $inner ? 'inner_header' : 'header';
    $selector_start = $inner ? '.mesmerize-inner-page' : '.mesmerize-front-page';

    mesmerize_add_kirki_field(array(
        'type'     => 'sectionseparator',
        'label'    => $inner ? esc_html__('Inner Pages Navigation options', 'mesmerize') : esc_html__('Front Page Navigation options', 'mesmerize'),
        'settings' => "{$prefix}_nav_separator",
        'section'  => $section,
        'priority' => $priority,
    ));

    do_action('mesmerize_after_navigation_separator_option', $inner, $section, $prefix);


    mesmerize_add_kirki_field(array(
        'type'      => 'checkbox',
        'label'     => esc_html__('Stick to top', 'mesmerize'),
        'section'   => $section,
        'priority'  => $priority,
        'settings'  => "{$prefix}_nav_sticked",
        'default'   => mesmerize_mod_default("{$prefix}_nav_sticked"),
        'transport' => 'postMessage',
    ));

    mesmerize_add_kirki_field(array(
        'type'      => 'checkbox',
        'label'     => esc_html__('Boxed Navigation', 'mesmerize'),
        'section'   => $section,
        'priority'  => $priority,
        'settings'  => "{$prefix}_nav_boxed",
        'default'   => false,
        'transport' => 'postMessage',
    ));


    mesmerize_add_kirki_field(array(
        'type'      => 'checkbox',
        'label'     => esc_html__('Show Navigation Bottom Border', 'mesmerize'),
        'section'   => $section,
        'priority'  => $priority,
        'settings'  => "{$prefix}_nav_border",
        'default'   => mesmerize_mod_default("{$prefix}_nav_border"),
        'transport' => 'postMessage',
    ));


    $group = $prefix . '_nav_border_group_button';

    mesmerize_add_kirki_field(array(
        'type'            => 'sectionseparator',
        'label'           => esc_html__('Bottom Border Options', 'mesmerize'),
        'section'         => $section,
        'settings'        => $prefix . '_nav_border_color_options_separator',
        'priority'        => $priority,
        'group'           => $group,
        'active_callback' => array(
            array(
                'setting'  => "{$prefix}_nav_border",
                'operator' => '==',
                'value'    => true,
            ),
        ),
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'color',
        'label'           => esc_html__('Bottom Border Color', 'mesmerize'),
        'section'         => $section,
        'settings'        => $prefix . '_nav_border_color',
        'priority'        => $priority,
        'choices'         => array(
            'alpha' => true,
        ),
        'default'         => mesmerize_mod_default("{$prefix}_nav_border_color"),
        'transport'       => 'postMessage',
        'output'          => array(
            array(
                'element'  => "{$selector_start} .navigation-bar.bordered",
                'property' => 'border-bottom-color',
            ),
        ),
        'group'           => $group,
        'active_callback' => array(
            array(
                'setting'  => "{$prefix}_nav_border",
                'operator' => '==',
                'value'    => true,
            ),
        ),
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'number',
        'label'           => esc_html__('Bottom Border Thickness', 'mesmerize'),
        'section'         => $section,
        'settings'        => $prefix . '_nav_border_thickness',
        'choices'         => array(
            'min'  => 1,
            'max'  => 50,
            'step' => 1,
        ),
        'default'         => mesmerize_mod_default("{$prefix}_nav_border_thickness"),
        'priority'        => $priority,
        'transport'       => 'postMessage',
        'output'          => array(
            array(
                'element'  => "{$selector_start} .navigation-bar.bordered",
                'property' => 'border-bottom-width',
                'suffix'   => 'px',
            ),
            array(
                'element'       => "{$selector_start} .navigation-bar.bordered",
                'property'      => 'border-bottom-style',
                'value_pattern' => 'solid',
            ),
        ),
        'group'           => $group,
        'active_callback' => array(
            array(
                'setting'  => "{$prefix}_nav_border",
                'operator' => '==',
                'value'    => true,
            ),
        ),

    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'sidebar-button-group',
        'settings'        => $prefix . '_nav_border_group_button',
        'label'           => esc_html__('Border Options', 'mesmerize'),
        'section'         => $section,
        'priority'        => $priority,
        'active_callback' => array(
            array(
                'setting'  => "{$prefix}_nav_border",
                'operator' => '==',
                'value'    => true,
            ),
        ),
    ));

    mesmerize_add_kirki_field(array(
        'type'      => 'checkbox',
        'label'     => esc_html__('Transparent Nav Bar', 'mesmerize'),
        'section'   => $section,
        'priority'  => $priority,
        'settings'  => "{$prefix}_nav_transparent",
        'default'   => mesmerize_mod_default("{$prefix}_nav_transparent"),
        'transport' => 'postMessage',
    ));

    mesmerize_add_kirki_field(array(
        'type'      => 'ope-info-pro',
        'label'     => esc_html__('More colors and typography options available in PRO. @BTN@', 'mesmerize'),
        'section'   => $section,
        'priority'  => $priority,
        'settings'  => "{$prefix}_nav_pro_info",
        'default'   => true,
        'transport' => 'postMessage',
    ));

    mesmerize_add_kirki_field(array(
        'type'     => 'select',
        'settings' => "{$prefix}_nav_bar_type",
        'label'    => esc_html__('Navigation bar type', 'mesmerize'),
        'section'  => $section,
        'default'  => 'default',
        'choices'  => apply_filters('mesmerize_navigation_types', array(
            'default'         => esc_html__('Logo on left, Navigation on right', 'mesmerize'),
            'logo-above-menu' => esc_html__('Logo above menu', 'mesmerize'),

        )),
        'update'   => apply_filters('mesmerize_nav_bar_menu_settings_partial_update', array(
            array(
                'value'  => 'default',
                'fields' => array(
                    "{$prefix}_nav_menu_items_align"   => 'flex-end',
                    "{$prefix}_fixed_menu_items_align" => 'flex-end',
                ),
            ),
            array(
                'value'  => 'logo-above-menu',
                'fields' => array(
                    "{$prefix}_nav_menu_items_align"   => 'center',
                    "{$prefix}_fixed_menu_items_align" => 'flex-end',
                ),
            ),

        ), $prefix),
        'priority' => $priority,
    ));

    mesmerize_add_kirki_field(array(
        'type'     => 'select',
        'settings' => "{$prefix}_nav_style",
        'label'    => esc_html__('Navigation style', 'mesmerize'),
        'section'  => $section,
        'default'  => 'active-line-bottom',
        'choices'  => apply_filters('mesmerize_navigation_styles', array(
            'simple-menu-items'  => esc_html__('Simple text menu', 'mesmerize'),
            'active-line-bottom' => esc_html__('Underlined active item', 'mesmerize'),
        )),

        'priority' => $priority,
        'update'   => apply_filters('mesmerize_navigation_menu_settings_partial_update', array(
            array(
                'value'  => 'active-line-bottom',
                'fields' => array(
                    "{$prefix}_nav_menu_active_color"       => mesmerize_get_var('dd_color'),
                    "{$prefix}_nav_fixed_menu_active_color" => mesmerize_get_var('dd_fixed_color'),
                ),
            ),

            array(
                'value'  => 'simple-menu-items',
                'fields' => array(
                    "{$prefix}_nav_menu_active_color"       => mesmerize_get_var('color-1'),
                    "{$prefix}_nav_fixed_menu_active_color" => mesmerize_get_var('color-1'),
                ),
            ),

        ), $prefix),
    ));
}


/*
    template functions
*/

function mesmerize_get_offcanvas_primary_menu()
{
    ?>
    <a href="#" data-component="offcanvas" data-target="#offcanvas-wrapper" data-direction="right" data-width="300px" data-push="false">
        <div class="bubble"></div>
        <i class="fa fa-bars"></i>
    </a>
    <div id="offcanvas-wrapper" class="hide force-hide  offcanvas-right">
        <div class="offcanvas-top">
            <div class="logo-holder">
                <?php mesmerize_print_logo(); ?>
            </div>
        </div>
        <?php
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'menu_id'        => 'offcanvas_menu',
            'menu_class'     => 'offcanvas_menu',
            'container_id'   => 'offcanvas-menu',
            'fallback_cb'    => 'mesmerize_no_hamburger_menu_cb',
        ));
        ?>

        <?php do_action("mesmerize_offcanvas_primary_menu_footer"); ?>
    </div>
    <?php
}


function mesmerize_print_primary_menu($walker = '', $fallback = 'mesmerize_nomenu_cb')
{
    //add pen overlay to avoid clicking menu items and changing the page inside customizer
	if (mesmerize_is_customize_preview()) {
		?>
            <div data-type="group" data-focus-control="nav_menu_locations[primary]" data-dynamic-mod="true">
        <?php
	}


    $drop_down_menu_classes = apply_filters('mesmerize_primary_drop_menu_classes', array('default'));
    $drop_down_menu_classes = array_merge($drop_down_menu_classes, array('main-menu', 'dropdown-menu'));

    wp_nav_menu(array(
        'theme_location'  => 'primary',
        'menu_id'         => 'main_menu',
        'menu_class'      => esc_attr(implode(" ", $drop_down_menu_classes)),
        'container_id'    => 'mainmenu_container',
        'container_class' => 'row',
        'fallback_cb'     => $fallback,
        'walker'          => $walker,
    ));

    mesmerize_get_offcanvas_primary_menu();

    if (mesmerize_is_customize_preview()) {
        ?>
            </div>
        <?php
	}
}


// sticky navigation
function mesmerize_navigation_sticky_attrs()
{
    $inner = mesmerize_is_inner(true);
    $atts  = array(
        'data-sticky'        => 0,
        'data-sticky-mobile' => 1,
        'data-sticky-to'     => 'top',
    );

    $atts   = apply_filters("mesmerize_navigation_sticky_attrs", $atts);
    $prefix = $inner ? "inner_header" : "header";

    $result = "";
    if (get_theme_mod("{$prefix}_nav_sticked", mesmerize_mod_default("{$prefix}_nav_sticked"))) {
        foreach ($atts as $key => $value) {
            $result .= " " . esc_attr($key) . "='" . esc_attr($value) . "' ";
        }
    }

    echo $result;
}

function mesmerize_navigation_wrapper_class()
{
    $inner   = mesmerize_is_inner(true);
    $classes = array();

    $prefix = $inner ? "inner_header" : "header";

    if (get_theme_mod("{$prefix}_nav_boxed", false)) {
        $classes[] = "gridContainer";
    }

    $classes = apply_filters("mesmerize_navigation_wrapper_class", $classes, $inner);

    echo esc_attr(implode(" ", $classes));
}


add_filter('mesmerize_navigation', 'mesmerize_navigation_bar_type');

function mesmerize_navigation_bar_type($template)
{

    if ( ! $template) {
        $setting         = mesmerize_is_front_page(true) ? "header_nav_bar_type" : "inner_header_nav_bar_type";
        $settingTemplate = get_theme_mod($setting, 'default');

        if ($settingTemplate !== 'default') {
            $template = $settingTemplate;
        }

    }

    return $template;
}

add_filter('mesmerize_primary_drop_menu_classes', function ($classes) {
    $prefix          = mesmerize_is_front_page(true) ? "header" : "inner_header";
    $variation_class = get_theme_mod("{$prefix}_nav_style", "active-line-bottom");
    $result          = array();

    foreach ($classes as $class) {
        if ($class !== "default") {
            $result[] = $class;
        }
    }

    $result[] = $variation_class;

    return $result;
});
