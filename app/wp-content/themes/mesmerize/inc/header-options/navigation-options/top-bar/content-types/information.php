<?php


add_filter("mesmerize_get_content_types", function($types) {
    $types['info'] = esc_html__("Information Fields", 'mesmerize');
    return $types;
});

add_filter("mesmerize_get_content_types_options", function($options) {
    $options['info'] = "mesmerize_top_bar_information_fields_options";
    return $options;
});

function mesmerize_top_bar_fields_defaults() {
    return array(
        array(
            "icon" => "fa-map-marker",
            "text" => __("Location,TX 75035,USA", 'mesmerize'),
        ),

        array(
            "icon" => "fa-phone",
            "text" => __("+1234567890", 'mesmerize'),
        ),

        array(
            "icon" => "fa-envelope",
            "text" => __("info@yourmail.com", 'mesmerize')
        ),
    );
}

function mesmerize_top_bar_information_fields_options($area, $section, $priority, $prefix)
{

    mesmerize_add_kirki_field(array(
        'type'     => 'sectionseparator',
        'label'    => esc_html__('Information fields', 'mesmerize'),
        'section'  => $section,
        'priority' => $priority,
        'settings' => "{$prefix}_info_fields_icons_separator",
    ));


    $mesmerize_top_bar_fields_defaults = mesmerize_top_bar_fields_defaults();

    $group_choices                     = array(
        "{$prefix}_info_fields_colors_separator",
        "{$prefix}_information_fields_text_color",
        "{$prefix}_information_fields_icon_color",
        "{$prefix}_info_fields_icons_separator",
    );

    for ($i = 0; $i < 3; $i++) {
        mesmerize_add_kirki_field(array(
            'type'      => 'checkbox',
            'label'     => sprintf(esc_html__('Show Field %d', 'mesmerize'), ($i + 1)),
            'section'   => $section,
            'priority'  => $priority,
            'settings'  => "{$prefix}_info_field_{$i}_enabled",
            'default'   => true,
            'transport' => 'postMessage'
        ));

        $group_choices[] = "{$prefix}_info_field_{$i}_enabled";

        mesmerize_add_kirki_field(array(
            'type'      => 'font-awesome-icon-control',
            'settings'  => "{$prefix}_info_field_{$i}_icon",
            'label'     => sprintf(esc_html__('Field %d icon', 'mesmerize'), ($i + 1)),
            'section'   => $section,
            'priority'  => $priority,
            'default'   => $mesmerize_top_bar_fields_defaults[$i]['icon'],
            'transport' => 'postMessage',
            'active_callback' => array(
                array(
                    'setting'  => "{$prefix}_info_field_{$i}_enabled",
                    'operator' => '==',
                    'value'    => true,
                ),
            ),
        ));

        $group_choices[] = "{$prefix}_info_field_{$i}_icon";

        mesmerize_add_kirki_field(array(
            'type'     => 'textarea',
            'settings' => "{$prefix}_info_field_{$i}_text",
            'label'    => sprintf(esc_html__('Field %d text', 'mesmerize'), ($i + 1)),
            'section'  => $section,
            'priority' => $priority,
            'default'  => $mesmerize_top_bar_fields_defaults[$i]['text'],
            'sanitize_callback' => 'mesmerize_wp_kses_post',
            'transport' => 'postMessage',
            'active_callback' => array(
                array(
                    'setting'  => "{$prefix}_info_field_{$i}_enabled",
                    'operator' => '==',
                    'value'    => true,
                ),
            ),
        ));

        $group_choices[] = "{$prefix}_info_field_{$i}_text";
    }


    mesmerize_add_kirki_field(array(
        'type'            => 'sidebar-button-group',
        'settings'        => "{$prefix}_info_fields_group_button",
        'label'           => esc_html__('Info Options', 'mesmerize'),
        'section'         => $section,
        'priority'        => $priority,
        "choices"         => $group_choices,
        'active_callback' => array(
            array(
                'setting'  => "{$prefix}_content",
                'operator' => '==',
                'value'    => 'info',
            ),
            array(
                'setting'  => "enable_top_bar",
                'operator' => '==',
                'value'    => true,
            ),
        ),
    ));

}

/*
    template functions
*/



add_action("mesmerize_header_top_bar_content_print", function($areaName, $type) {
    if ($type == 'info') {
        mesmerize_print_header_top_bar_info_fields($areaName);
    }
}, 1, 2);


function mesmerize_print_header_top_bar_info_fields($area)
{

    $defaults = mesmerize_top_bar_fields_defaults();

    for ($i = 0; $i < count($defaults); $i++) {
        $preview_atts = "";
        if (mesmerize_is_customize_preview()) {
            $setting      = "header_top_bar_{$area}_info_field_{$i}_icon";
            $preview_atts = "data-focus-control='".esc_attr($setting)."'";
        }

        $can_show = mesmerize_can_show_demo_content();
        $is_enabled = get_theme_mod("header_top_bar_{$area}_info_field_{$i}_enabled", true);
        $icon       = get_theme_mod("header_top_bar_{$area}_info_field_{$i}_icon", $defaults[$i]['icon']);
        $text       = get_theme_mod("header_top_bar_{$area}_info_field_{$i}_text", $can_show ? $defaults[$i]['text'] : "");

        $hidden_attr = "";

        if ( ! intval($is_enabled)) {
            $hidden_attr = "data-reiki-hidden='true'";
        }

        if (!$can_show && !$text) {
            continue;
        }

        if(mesmerize_is_customize_preview() || (!mesmerize_is_customize_preview() && intval($is_enabled))) {
          ?>
          <div class="top-bar-field" data-type="group" <?php echo $hidden_attr ?> <?php echo $preview_atts; ?> data-dynamic-mod="true">
              <i class="fa <?php echo esc_attr($icon) ?>"></i>
              <span><?php echo mesmerize_wp_kses_post($text); ?></span>
          </div>
          <?php
        }

    }

}
