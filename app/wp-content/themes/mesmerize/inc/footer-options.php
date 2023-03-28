<?php


function mesmerize_get_footer_contact_boxes($index = 0)
{

    $contact_boxes = array(
        array(
            'icon_mod'     => 'footer_box1_content_icon',
            'icon_default' => 'fa-map-marker',
            'text_mod'     => 'footer_box1_content_text',
            'text_default' => esc_html__('San Francisco - Adress - 18 California Street 1100.', 'mesmerize'),
        ),
        array(
            'icon_mod'     => 'footer_box2_content_icon',
            'icon_default' => 'fa-envelope-o',
            'text_mod'     => 'footer_box2_content_text',
            'text_default' => esc_html__('hello@mycoolsite.com', 'mesmerize'),
        ),
        array(
            'icon_mod'     => 'footer_box3_content_icon',
            'icon_default' => 'fa-phone',
            'text_mod'     => 'footer_box3_content_text',
            'text_default' => esc_html__('+1 (555) 345 234343', 'mesmerize'),
        ),
    );

    return $contact_boxes[$index];

}

function mesmerize_footer_filter()
{
    $footer_template = get_theme_mod("footer_template", "simple");

    $theme      = wp_get_theme();
    $textDomain = mesmerize_get_text_domain();

    if ($footer_template == 'simple') {
        $footer_template = '';
    }

    return $footer_template;
}

add_filter('mesmerize_footer', 'mesmerize_footer_filter');

function mesmerize_footer_settings()
{

    $section = 'footer_settings';

    mesmerize_add_kirki_field(array(
        'type'     => 'sectionseparator',
        'label'    => esc_html__('Footer Content', 'mesmerize'),
        'section'  => $section,
        'settings' => 'footer_content_separator',
        'priority' => 1,
    ));

    mesmerize_add_kirki_field(array(
        'type'     => 'checkbox',
        'settings' => 'footer_paralax',
        'label'    => esc_html__('Use footer parallax', 'mesmerize'),
        'section'  => $section,
        'default'  => false,
        'priority' => 4,
        'transport' => 'postMessage',
    ));


    mesmerize_add_kirki_field(array(
        'type'      => 'ope-info-pro',
        'label'     => esc_html__('More colors and typography options available in PRO. @BTN@', 'mesmerize'),
        'section'   => $section,
        'priority'  => 4,
        'settings'  => 'footer_content_typography_pro_info',
        'default'   => true,
        'transport' => 'postMessage',
    ));

    mesmerize_add_kirki_field(array(
        'type'     => 'select',
        'settings' => 'footer_template',
        'label'    => esc_html__('Footer Template', 'mesmerize'),
        'section'  => $section,
        'priority' => 1,
        'default'  => 'simple',
        'choices'  => apply_filters('mesmerize_footer_templates', array(
            'simple'        => esc_html__('Simple', 'mesmerize'),
            'contact-boxes' => esc_html__('Contact Boxes', 'mesmerize'),
            'content-lists' => esc_html__('Widgets Boxes', 'mesmerize'),
        )),

        'update' => apply_filters('mesmerize_footer_templates_update', array()),

    ));

    // Contact Boxes options button and section

    $group = "footer_content_contact_boxes_group_button";

    mesmerize_add_kirki_field(array(
        'type'            => 'sectionseparator',
        'label'           => esc_html__('Box 1 Content', 'mesmerize'),
        'section'         => $section,
        'settings'        => 'footer_box1_content_separator',
        'priority'        => 1,
        'group'           => $group,
        'active_callback' => array(
            array(
                'setting'  => 'footer_template',
                'operator' => '==',
                'value'    => 'contact-boxes',
            ),
        ),
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'font-awesome-icon-control',
        'settings'        => 'footer_box1_content_icon',
        'label'           => esc_html__('Icon', 'mesmerize'),
        'section'         => $section,
        'priority'        => 1,
        'group'           => $group,
        'default'         => 'fa-map-marker',
        'transport' => 'postMessage',
        'active_callback' => array(
            array(
                'setting'  => 'footer_template',
                'operator' => '==',
                'value'    => 'contact-boxes',
            ),
        ),
    ));

    mesmerize_add_kirki_field(array(
        'type'              => 'textarea',
        'settings'          => 'footer_box1_content_text',
        'label'             => esc_html__('Text', 'mesmerize'),
        'section'           => $section,
        'priority'          => 1,
        'group'             => $group,
        'default'           => 'San Francisco - Adress - 18 California Street 1100.',
        'sanitize_callback' => 'wp_kses_post',
        'active_callback'   => array(
            array(
                'setting'  => 'footer_template',
                'operator' => '==',
                'value'    => 'contact-boxes',
            ),
        ),
        'transport'         => 'postMessage',
        'js_vars'           => array(
            array(
                'element'  => '[data-focus-control="footer_box1_content_icon"] > p',
                'function' => 'html',
            ),
        ),
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'sectionseparator',
        'label'           => esc_html__('Box 2 Content', 'mesmerize'),
        'section'         => $section,
        'settings'        => 'footer_box2_content_separator',
        'priority'        => 1,
        'group'           => $group,
        'active_callback' => array(
            array(
                'setting'  => 'footer_template',
                'operator' => '==',
                'value'    => 'contact-boxes',
            ),
        ),
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'font-awesome-icon-control',
        'settings'        => 'footer_box2_content_icon',
        'label'           => esc_html__('Icon', 'mesmerize'),
        'section'         => $section,
        'priority'        => 1,
        'group'           => $group,
        'default'         => 'fa-envelope-o',
        'transport' 	  => 'postMessage',
        'active_callback' => array(
            array(
                'setting'  => 'footer_template',
                'operator' => '==',
                'value'    => 'contact-boxes',
            ),
        ),
    ));

    mesmerize_add_kirki_field(array(
        'type'              => 'textarea',
        'settings'          => 'footer_box2_content_text',
        'label'             => esc_html__('Text', 'mesmerize'),
        'section'           => $section,
        'priority'          => 1,
        'group'             => $group,
        'default'           => 'hello@mycoolsite.com',
        'sanitize_callback' => 'wp_kses_post',
        'active_callback'   => array(
            array(
                'setting'  => 'footer_template',
                'operator' => '==',
                'value'    => 'contact-boxes',
            ),
        ),
        'transport'         => 'postMessage',
        'js_vars'           => array(
            array(
                'element'  => '[data-focus-control="footer_box2_content_icon"] > p',
                'function' => 'html',

            ),
        ),
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'sectionseparator',
        'label'           => esc_html__('Box 3 Content', 'mesmerize'),
        'section'         => $section,
        'settings'        => 'footer_box3_content_separator',
        'priority'        => 1,
        'group'           => $group,
        'active_callback' => array(
            array(
                'setting'  => 'footer_template',
                'operator' => '==',
                'value'    => 'contact-boxes',
            ),
        ),
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'font-awesome-icon-control',
        'settings'        => 'footer_box3_content_icon',
        'label'           => esc_html__('Icon', 'mesmerize'),
        'section'         => $section,
        'priority'        => 1,
        'group'           => $group,
        'default'         => 'fa-phone',
        'transport' 	  => 'postMessage',
        'active_callback' => array(
            array(
                'setting'  => 'footer_template',
                'operator' => '==',
                'value'    => 'contact-boxes',
            ),
        ),
    ));

    mesmerize_add_kirki_field(array(
        'type'              => 'textarea',
        'settings'          => 'footer_box3_content_text',
        'label'             => esc_html__('Text', 'mesmerize'),
        'section'           => $section,
        'priority'          => 1,
        'group'             => $group,
        'default'           => '+1 (555) 345 234343',
        'sanitize_callback' => 'wp_kses_post',
        'active_callback'   => array(
            array(
                'setting'  => 'footer_template',
                'operator' => '==',
                'value'    => 'contact-boxes',
            ),
        ),
        'transport'         => 'postMessage',
        'js_vars'           => array(
            array(
                'element'  => '[data-focus-control="footer_box3_content_icon"] > p',
                'function' => 'html',

            ),
        ),
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'sidebar-button-group',
        'settings'        => 'footer_content_contact_boxes_group_button',
        'label'           => esc_html__('Contact Boxes Options', 'mesmerize'),
        'section'         => $section,
        'priority'        => 1,
        'active_callback' => array(
            array(
                'setting'  => 'footer_template',
                'operator' => '==',
                'value'    => 'contact-boxes',
            ),
        ),
    ));

    // Social icons options button and section

    $footers_with_social_icons = apply_filters("mesmerize_footer_templates_with_social", array("contact-boxes", "content-lists"));

    $group = "footer_content_social_icons_group_button";

    $mesmerize_footer_socials_icons = mesmerize_default_icons();

    $count = 0;
    foreach ($mesmerize_footer_socials_icons as $social) {
        $socialid   = 'social_icon_' . $count;
        $social_url = $social['link'];
        $count++;

        $social_separator_label = sprintf(__('Social Icon %s Options', 'mesmerize'), $count);

        $social_enable_label = sprintf(__('Show Icon %s', 'mesmerize'), $count);
        $social_url_label    = sprintf(__('Icon %s url', 'mesmerize'), $count);
        $social_url_icon     = sprintf(__('Icon %s icon', 'mesmerize'), $count);

        mesmerize_add_kirki_field(array(
            'type'            => 'sectionseparator',
            'label'           => esc_html($social_separator_label),
            'section'         => $section,
            'settings'        => 'footer_content_' . $socialid . '_separator',
            'priority'        => 1,
            'group'           => $group,
            'active_callback' => array(
                array(
                    'setting'  => 'footer_template',
                    'operator' => 'in',
                    'value'    => $footers_with_social_icons,
                ),
            ),
        ));

        mesmerize_add_kirki_field(array(
            'type'            => 'checkbox',
            'settings'        => 'footer_content_' . $socialid . '_enabled',
            'label'           => esc_html($social_enable_label),
            'section'         => $section,
            'priority'        => 1,
            'group'           => $group,
            'default'         => true,
            'transport'       => 'postMessage',
            'active_callback' => array(
                array(
                    'setting'  => 'footer_template',
                    'operator' => 'in',
                    'value'    => $footers_with_social_icons,
                ),
            ),
        ));

        mesmerize_add_kirki_field(array(
            'type'            => 'url',
            'settings'        => 'footer_content_' . $socialid . '_link',
            'label'           => esc_html($social_url_label),
            'section'         => $section,
            'priority'        => 1,
            'group'           => $group,
            'default'         => '#',
            'transport'       => 'postMessage',
            'active_callback' => array(
                array(
                    'setting'  => 'footer_content_' . $socialid . '_enabled',
                    'operator' => '==',
                    'value'    => true,
                ),
                array(
                    'setting'  => 'footer_template',
                    'operator' => 'in',
                    'value'    => $footers_with_social_icons,
                ),
            ),
        ));

        mesmerize_add_kirki_field(array(
            'type'            => 'font-awesome-icon-control',
            'settings'        => 'footer_content_' . $socialid . '_icon',
            'label'           => esc_html($social_url_icon),
            'section'         => $section,
            'priority'        => 1,
            'group'           => $group,
            'default'         => $social['icon'],
            'transport'       => 'postMessage',
            'active_callback' => array(
                array(
                    'setting'  => 'footer_content_' . $socialid . '_enabled',
                    'operator' => '==',
                    'value'    => true,
                ),
                array(
                    'setting'  => 'footer_template',
                    'operator' => 'in',
                    'value'    => $footers_with_social_icons,
                ),
            ),
        ));

    }

    mesmerize_add_kirki_field(array(
        'type'            => 'sidebar-button-group',
        'settings'        => 'footer_content_social_icons_group_button',
        'label'           => esc_html__('Social Icons Options', 'mesmerize'),
        'section'         => $section,
        'priority'        => 1,
        'active_callback' => array(
            array(
                'setting'  => 'footer_template',
                'operator' => 'in',
                'value'    => $footers_with_social_icons,
            ),
        ),
    ));


}

function mesmerize_print_widget($id)
{
    if ( ! is_active_sidebar($id) && is_customize_preview()) {
        $focusAttr = mesmerize_customizer_focus_control_attr("sidebars_widgets[{$id}]", false);
        echo "<div {$focusAttr}>" . esc_html__("Go to widgets section to add a widget here.", 'mesmerize') . "</div>";
    } else {
        dynamic_sidebar($id);
    }
}

add_filter("mesmerize_footer_container_atts", function ($attrs) {
    $paralax = get_theme_mod("footer_paralax", false);
    if ($paralax) {
        $attrs['class'] .= " paralax ";
    }

    return $attrs;
});

/* start contact boxes */

add_filter("mesmerize_footer_contact_boxes_content_print", function () {
    mesmerize_print_contact_boxes();
}, 1, 2);

function mesmerize_print_contact_boxes($index = 0)
{

    $fields = mesmerize_get_footer_contact_boxes($index);

    $preview_atts = "";

    if (mesmerize_is_customize_preview()) {
        $setting      = esc_attr($fields['icon_mod']);
        $preview_atts = "data-focus-control='{$setting}'";
    }

    ?>
    <div data-type="group" <?php echo $preview_atts; ?> data-dynamic-mod="true">
        <i class="big-icon fa <?php echo get_theme_mod($fields['icon_mod'], $fields['icon_default']); ?>"></i>
        <p>
            <?php echo wp_kses_post(get_theme_mod($fields['text_mod'], $fields['text_default'])); ?>
        </p>
    </div>
    <?php
}

/* end contact boxes */

add_action("mesmerize_customize_register_options", function () {
    mesmerize_footer_settings();
});
