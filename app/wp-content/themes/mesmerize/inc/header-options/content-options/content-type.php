<?php

function mesmerize_get_column_width_kirki_output($selector, $args = array(), $js_vars = false)
{

    $result = array();
    $base   = array_merge(array(
        "element"     => $selector,
        "property"    => null,
        "units"       => "%",
        "media_query" => null,
    ), $args);

    $props = array(
        "-webkit-flex-basis",
        "-moz-flex-basis",
        "-ms-flex-preferred-size",
        "flex-basis",
        "max-width",
        "width",
    );


    if ($js_vars) {
        $propData = array_merge($base, array(
            'property' => implode(',', $props),
            'function' => 'style',
        ));

        $result[] = $propData;
    } else {

        foreach ($props as $prop) {
            $propData = array_merge($base, array(
                "property" => $prop,
            ));


            $result[] = $propData;
        }
    }

    return $result;
}


function mesmerize_header_media_box_vertical_align()
{
    return array(
        'top-sm'    => esc_html__('Top', 'mesmerize'),
        'middle-sm' => esc_html__('Middle', 'mesmerize'),
        'bottom-sm' => esc_html__('Bottom', 'mesmerize'),
    );
}

function mesmerize_front_page_header_media_box_options($section, $prefix, $priority)
{

    $group = "header_media_box_settings";

    mesmerize_add_kirki_field(array(
        'type'            => 'sidebar-button-group',
        'settings'        => $group,
        'label'           => esc_html__('Media box settings', 'mesmerize'),
        'section'         => $section,
        'priority'        => $priority,
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter', array(
            array(
                'setting'  => 'header_content_partial',
                'operator' => 'contains',
                'value'    => 'media',
            ),

        ), false),
    ));

    mesmerize_add_kirki_field(array(
        'type'     => 'sectionseparator',
        'label'    => esc_html__('Media Box Settings', 'mesmerize'),
        'section'  => $section,
        'settings' => "header_media_box_media_separator",
        'priority' => $priority,
        'group'    => $group,
    ));

    mesmerize_add_kirki_field(array(
        'type'     => 'slider',
        'settings' => 'header_media_box_top_bottom_width',
        'label'    => esc_html__('Media width', 'mesmerize'),
        'choices'  => array(
            'min'  => '0',
            'max'  => '100',
            'step' => '1',
        ),

        'default' => 50,

        'transport' => 'postMessage',
        'section'   => $section,
        'priority'  => $priority,
        "group"     => $group,

        "output" => array(
            array(
                'element'     => '.media-on-bottom .header-media-container, .media-on-top .header-media-container',
                'property'    => 'width',
                "units"       => "%",
                "media_query" => "@media only screen and (min-width: 768px)",
            ),
        ),

        "js_vars" => array(
            array(
                'element'     => '.media-on-bottom .header-media-container, .media-on-top .header-media-container',
                'property'    => 'width',
                'function'    => 'css',
                "units"       => "%",
                "media_query" => "@media only screen and (min-width: 768px)",
            ),
        ),


        'active_callback' => array(
            array(
                'setting'  => 'header_content_partial',
                'operator' => 'in',
                'value'    => array('media-on-top', 'media-on-bottom'),
            ),
        ),
    ));


    mesmerize_add_kirki_field(array(
        'type'      => 'select',
        'settings'  => 'header_media_box_vertical_align',
        'label'     => esc_html__('Media Vertical Align', 'mesmerize'),
        'section'   => $section,
        'default'   => 'middle-sm',
        'transport' => 'postMessage',
        'choices'   => mesmerize_header_media_box_vertical_align(),

        'active_callback' => array(
            array(
                'setting'  => 'header_content_partial',
                'operator' => 'in',
                'value'    => array('media-on-left', 'media-on-right'),
            ),
        ),

        "group" => $group,
    ));


    add_filter('mesmerize_hero_media_vertical_align', function ($align) {
        $value = get_theme_mod('header_media_box_vertical_align', $align);
        if ( ! array_key_exists($value, mesmerize_header_media_box_vertical_align())) {
            $value = $align;
        }

        return $value;
    });

    mesmerize_add_kirki_field(array(
        'type'        => 'cropped_image',
        'settings'    => 'header_content_image',
        'label'       => esc_html__('Image', 'mesmerize'),
        'section'     => $section,
        'default'     => apply_filters('mesmerize_assets_url', get_template_directory_uri() , '/') . "/assets/images/media-image-default.jpg",
        'height'      => '600',
        'width'       => '420',
        'flex_height' => true,
        'flex_width'  => true,
        'active_callback' => array(
            array(
                'setting'  => 'header_content_media',
                'operator' => 'in',
                'value'    => array('image'),
            ),
        ),
        "group"           => $group,
    ));


    mesmerize_add_kirki_field(array(
        'type'     => 'slider',
        'label'    => esc_html__('Image width', 'mesmerize'),
        'section'  => $section,
        'settings' => 'header_column_width',

        'choices' => array(
            'min'  => '0',
            'max'  => '100',
            'step' => '1',
        ),

        'default' => 29,

        'transport' => 'postMessage',

        "output" => array_merge(mesmerize_get_column_width_kirki_output(".header-homepage:not(.header-slide) .header-hero-media", array(
            "media_query" => "@media only screen and (min-width: 768px)",
        )), mesmerize_get_column_width_kirki_output(".header-homepage:not(.header-slide) .header-hero-content", array(
            'prefix'      => 'calc(100% - ',
            'suffix'      => ')!important',
            "media_query" => "@media only screen and (min-width: 768px)",
        ))),

        "js_vars"         => array_merge(mesmerize_get_column_width_kirki_output(".header-homepage:not(.header-slide) .header-hero-media", array(
            "media_query" => "@media only screen and (min-width: 768px)",
        ), true), mesmerize_get_column_width_kirki_output(".header-homepage:not(.header-slide) .header-hero-content", array(
            'prefix'      => 'calc(100% - ',
            'suffix'      => ')!important',
            "media_query" => "@media only screen and (min-width: 768px)",
        ), true)),
        'active_callback' => array(
            array(
                'setting'  => 'header_content_partial',
                'operator' => 'in',
                'value'    => array('media-on-left', 'media-on-right'),
            ),
        ),
        "group"           => $group,
    ));


    mesmerize_add_kirki_field(array(
        'type'      => 'spacing',
        'settings'  => 'header_content_media_spacing',
        'label'     => esc_html__('Media Box Spacing', 'mesmerize'),
        'section'   => $section,
        'default'   => array(
            'top'    => '0px',
            'bottom' => '0px',
        ),
        'transport' => 'postMessage',
        'output'    => array(
            array(
                'element'  => '.header-description-bottom.media, .header-description-top.media',
                'property' => 'margin',
            ),
        ),
        'js_vars'   => array(
            array(
                'element'  => '.header-description-bottom.media, .header-description-top.media',
                'function' => 'style',
                'property' => 'margin',
            ),
        ),

        'active_callback' => array(
            array(
                'setting'  => 'header_content_partial',
                'operator' => 'in',
                'value'    => array('media-on-top', 'media-on-bottom'),
            ),
        ),

        "group" => $group,
    ));
}

function mesmerize_get_medias_with_frame()
{
    return apply_filters("mesmerize_get_medias_with_frame", array('media-on-left', 'media-on-right', 'media-on-top', 'media-on-bottom'));
}

function mesmerize_front_page_header_frame_options($section, $prefix, $priority)
{

    $group = "header_media_box_settings";

    $media_with_frame = mesmerize_get_medias_with_frame();

    $active_callback = array(
        array(
            'setting'  => 'header_content_media',
            'operator' => 'in',
            'value'    => array('image'),
        ),

        array(
            'setting'  => 'header_content_partial',
            'operator' => 'in',
            'value'    => $media_with_frame,
        ),

        array(
            'setting'  => 'header_content_frame_type',
            'operator' => 'in',
            'value'    => array('border', 'background'),
        ),


    );

    mesmerize_add_kirki_field(array(
        'type'            => 'sectionseparator',
        'label'           => esc_html__('Frame Options', 'mesmerize'),
        'section'         => $section,
        'settings'        => $prefix . 'header_content_frame_separator',
        'priority'        => $priority,
        'group'           => $group,
        'active_callback' => array(
            array(
                'setting'  => 'header_content_media',
                'operator' => 'in',
                'value'    => array('image'),
            ),
        ),
    ));

    mesmerize_add_kirki_field(array(
        'type'              => 'select',
        'settings'          => 'header_content_frame_type',
        'label'             => esc_html__('Frame Type', 'mesmerize'),
        'section'           => $section,
        'choices'           => apply_filters('mesmerize_header_header_content_frame_types', array(
            "none"       => esc_html__("None", 'mesmerize'),
            "background" => esc_html__("Background", 'mesmerize'),
            "border"     => esc_html__("Border", 'mesmerize'),
        )),
        'default'           => 'border',
        'sanitize_callback' => 'sanitize_text_field',
        'priority'          => $priority,
        "group"             => $group,
        'active_callback'   => array(
            array(
                'setting'  => 'header_content_media',
                'operator' => 'in',
                'value'    => array('image'),
            ),
        ),
    ));

    mesmerize_add_kirki_field(array(
        'type'     => 'slider',
        'label'    => esc_html__('Width', 'mesmerize'),
        'section'  => $section,
        'settings' => 'header_content_frame_width',
        'priority' => $priority,
        'choices'  => array(
            'min'  => '0',
            'max'  => '200',
            'step' => '1',
        ),

        'default' => 100,

        'transport' => 'postMessage',

        'active_callback' => $active_callback,
        "group"           => $group,
    ));

    mesmerize_add_kirki_field(array(
        'type'     => 'slider',
        'label'    => esc_html__('Height', 'mesmerize'),
        'section'  => $section,
        'settings' => 'header_content_frame_height',
        'priority' => $priority,
        'choices'  => array(
            'min'  => '0',
            'max'  => '200',
            'step' => '1',
        ),

        'default' => 100,

        'transport' => 'postMessage',

        'active_callback' => $active_callback,
        "group"           => $group,
    ));

    mesmerize_add_kirki_field(array(
        'type'     => 'slider',
        'label'    => esc_html__('Offset left', 'mesmerize'),
        'section'  => $section,
        'settings' => 'header_content_frame_offset_left',
        'priority' => $priority,
        'choices'  => array(
            'min'  => '-50',
            'max'  => '50',
            'step' => '1',
        ),

        'default' => -13,

        'transport' => 'postMessage',

        'active_callback' => $active_callback,
        "group"           => $group,
    ));

    mesmerize_add_kirki_field(array(
        'type'     => 'slider',
        'label'    => esc_html__('Offset top', 'mesmerize'),
        'section'  => $section,
        'settings' => 'header_content_frame_offset_top',
        'priority' => $priority,
        'choices'  => array(
            'min'  => '-50',
            'max'  => '50',
            'step' => '1',
        ),

        'default' => 10,

        'transport' => 'postMessage',

        'active_callback' => $active_callback,
        "group"           => $group,
    ));

    mesmerize_add_kirki_field(array(
        'type'     => 'slider',
        'label'    => esc_html__('Frame thickness', 'mesmerize'),
        'section'  => $section,
        'settings' => 'header_content_frame_thickness',
        'priority' => $priority,
        'choices'  => array(
            'min'  => '1',
            'max'  => '50',
            'step' => '1',
        ),

        'default' => 11,

        'transport' => 'postMessage',

        'active_callback' => array(
            array(
                'setting'  => 'header_content_media',
                'operator' => 'in',
                'value'    => array('image'),
            ),

            array(
                'setting'  => 'header_content_partial',
                'operator' => 'in',
                'value'    => $media_with_frame,
            ),

            array(
                'setting'  => 'header_content_frame_type',
                'operator' => 'in',
                'value'    => array('border'),
            ),


        ),
        "group"           => $group,
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'color',
        'label'           => esc_html__('Frame Color', 'mesmerize'),
        'section'         => $section,
        'priority'        => $priority,
        'settings'        => 'header_content_frame_color',
        'default'         => 'rgba(255,255,255,0.726)',
        'transport'       => 'postMessage',
        'choices'         => array('alpha' => true),
        'active_callback' => $active_callback,
        'group'           => $group,
    ));


    mesmerize_add_kirki_field(array(
        'type'      => 'checkbox',
        'label'     => esc_html__('Show frame over image', 'mesmerize'),
        'section'   => $section,
        'priority'  => $priority,
        'settings'  => 'header_content_frame_show_over_image',
        'default'   => false,
        'transport' => 'postMessage',

        'active_callback' => $active_callback,
        'group'           => $group,
    ));


    mesmerize_add_kirki_field(array(
        'type'      => 'checkbox',
        'label'     => esc_html__('Show frame shadow', 'mesmerize'),
        'section'   => $section,
        'priority'  => $priority,
        'settings'  => 'header_content_frame_shadow',
        'default'   => true,
        'transport' => 'postMessage',

        'active_callback' => $active_callback,
        'group'           => $group,
    ));

    mesmerize_add_kirki_field(array(
        'type'      => 'checkbox',
        'label'     => esc_html__('Hide frame on mobile', 'mesmerize'),
        'section'   => $section,
        'priority'  => $priority,
        'settings'  => 'header_content_frame_hide_on_mobile',
        'default'   => true,
        'transport' => 'postMessage',

        'active_callback' => $active_callback,
        'group'           => $group,
    ));
}

function mesmerize_front_page_header_text_options()
{

    $priority = 5;

    $prefix  = "header";
    $section = "header_background_chooser";

    $group = "header_text_box_settings";

    mesmerize_add_kirki_field(array(
        'type'            => 'sidebar-button-group',
        'settings'        => $group,
        'label'           => esc_html__('Text box settings', 'mesmerize'),
        'section'         => $section,
        'priority'        => $priority,
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter', array(), false),
    ));


    mesmerize_add_kirki_field(array(
        'type'     => 'sectionseparator',
        'label'    => esc_html__('Text Box Settings', 'mesmerize'),
        'section'  => $section,
        'settings' => "header_text_box_text_separator",
        'priority' => $priority,
        'group'    => $group,
    ));

    mesmerize_add_kirki_field(array(
        'type'     => 'radio-buttonset',
        'label'    => esc_html__('Text Align', 'mesmerize'),
        'section'  => $section,
        'settings' => 'header_text_box_text_align',
        'default'  => mesmerize_mod_default('header_text_box_text_align'),
        'priority' => $priority,
        "choices"  => array(
            "left"   => esc_html__("Left", "mesmerize"),
            "center" => esc_html__("Center", "mesmerize"),
            "right"  => esc_html__("Right", "mesmerize"),
        ),


        'transport' => 'postMessage',


        'group' => $group,
    ));


    mesmerize_add_kirki_field(array(
        'type'     => 'slider',
        'label'    => esc_html__('Text Width', 'mesmerize'),
        'section'  => $section,
        'settings' => 'header_text_box_text_width',
        'priority' => $priority,
        'choices'  => array(
            'min'  => '0',
            'max'  => '100',
            'step' => '1',
        ),

        'default'   => mesmerize_mod_default("header_text_box_text_width"),
        'transport' => 'postMessage',

        "js_vars" => array(
            array(
                "element"  => ".header-content .align-holder",
                "function" => "css",
                "property" => "width",
                'suffix'   => '!important',
                "units"    => "%",
            ),
        ),

        "output" => array(
            array(
                "element"     => ".header-content .align-holder",
                "property"    => "width",
                'suffix'      => '!important',
                "units"       => "%",
                "media_query" => "@media only screen and (min-width: 768px)",
            ),
        ),

        'group' => $group,
    ));

    mesmerize_add_kirki_field(array(
        'type'      => 'select',
        'settings'  => 'header_text_box_text_vertical_align',
        'label'     => esc_html__('Text Vertical Align', 'mesmerize'),
        'section'   => $section,
        'default'   => 'middle-sm',
        'transport' => 'postMessage',
        'priority'  => $priority,
        'choices'   => array(
            'top-sm'    => esc_html__('Top', 'mesmerize'),
            'middle-sm' => esc_html__('Middle', 'mesmerize'),
            'bottom-sm' => esc_html__('Bottom', 'mesmerize'),
        ),

        'active_callback' => array(
            array(
                'setting'  => 'header_content_partial',
                'operator' => 'in',
                'value'    => array('media-on-left', 'media-on-right'),
            ),
        ),

        'group' => $group,
    ));
}

function mesmerize_front_page_header_content_options($section, $prefix, $priority)
{
    mesmerize_add_kirki_field(array(
        'type'            => 'sectionseparator',
        'label'           => esc_html__('Content Options', 'mesmerize'),
        'section'         => $section,
        'settings'        => "header_content_separator",
        'priority'        => $priority,
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter', array(), false),
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'select',
        'settings'        => 'header_content_partial',
        'label'           => esc_html__('Content layout', 'mesmerize'),
        'section'         => $section,
        'default'         => mesmerize_mod_default('header_content_partial'),
        'choices'         => mesmerize_get_partial_types(),
        'priority'        => $priority,
        'update'          => apply_filters('mesmerize_header_content_partial_update', array(
            array(
                "value"  => "content-on-center",
                "fields" => array(
                    'header_text_box_text_align' => 'center',
                    'header_spacing'             => apply_filters(
                        'mesmerize_header_content_spacing_partial_update',
                        array(
                            'top'    => '10%',
                            'bottom' => '10%',
                        ),
                        1
                    ),
                ),
            ),
            array(
                "value"  => "content-on-right",
                "fields" => array(
                    'header_text_box_text_align' => 'right',
                    'header_spacing'             => apply_filters(
                        'mesmerize_header_content_spacing_partial_update',
                        array(
                            'top'    => '10%',
                            'bottom' => '10%',
                        ),
                        1
                    ),
                ),
            ),
            array(
                "value"  => "content-on-left",
                "fields" => array(
                    'header_text_box_text_align' => 'left',
                    'header_spacing'             => apply_filters(
                        'mesmerize_header_content_spacing_partial_update',
                        array(
                            'top'    => '10%',
                            'bottom' => '10%',
                        ),
                        1
                    ),
                ),
            ),
            array(
                "value"  => "media-on-right",
                "fields" => array(
                    'header_text_box_text_align' => 'left',
                    'header_spacing'             => apply_filters(
                        'mesmerize_header_content_spacing_partial_update',
                        array(
                            'top'    => '5%',
                            'bottom' => '5%',
                        ),
                        2
                    ),
                ),
            ),
            array(
                "value"  => "media-on-left",
                "fields" => array(
                    'header_text_box_text_align' => 'left',
                    'header_spacing'             => apply_filters(
                        'mesmerize_header_content_spacing_partial_update',
                        array(
                            'top'    => '5%',
                            'bottom' => '5%',
                        ),
                        2
                    ),
                ),
            ),
        )),
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter', array(), false),
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'select',
        'settings'        => 'header_content_media',
        'label'           => esc_html__('Media Type', 'mesmerize'),
        'section'         => $section,
        'default'         => 'image',
        'choices'         => mesmerize_get_media_types(),
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter', array(
            array(
                'setting'  => 'header_content_partial',
                'operator' => 'contains',
                'value'    => 'media-on-',
            ),

        ), false),
        'priority'        => $priority,
    ));

    mesmerize_add_kirki_field(array(
        'type'            => 'ope-info-pro',
        'label'           => esc_html__('More content layouts and media types available in PRO. @BTN@', 'mesmerize'),
        'section'         => $section,
        'priority'        => $priority,
        'settings'        => "header_content_pro_info",
        'default'         => true,
        'transport'       => 'postMessage',
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter', array(), false),
    ));


    mesmerize_add_kirki_field(array(
        'type'     => 'spacing',
        'label'    => esc_html__('Content Spacing', 'mesmerize'),
        'section'  => $section,
        'settings' => 'header_spacing',
        'default' => mesmerize_mod_default('header_spacing'),
        "output" => array(
            array(
                "element"  => ".header-homepage .header-description-row",
                "property" => "padding",
            ),
        ),
        'transport' => 'postMessage',
        'js_vars'         => array(
            array(
                'element'  => '.header-homepage .header-description-row',
                'function' => 'css',
                'property' => 'padding',
            ),
        ),
        'priority'        => $priority,
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter', array(), false),
    ));


    mesmerize_add_kirki_field(array(
        'type'     => 'spacing',
        'label'    => esc_html__('Mobile Content Spacing', 'mesmerize'),
        'section'  => $section,
        'settings' => 'header_spacing_mobile',
        'default' => mesmerize_mod_default('header_spacing_mobile'),
        "output" => array(
            array(
                "element"     => ".header-homepage .header-description-row",
                "property"    => "padding",
                "media_query" => "@media screen and (max-width:767px)",
            ),
        ),
        'transport' => 'postMessage',
        'js_vars'         => array(
            array(
                'element'     => '.header-homepage .header-description-row',
                'function'    => 'css',
                'property'    => 'padding',
                "media_query" => "@media screen and (max-width:767px)",
            ),
        ),
        'priority'        => $priority,
        'active_callback' => apply_filters('mesmerize_header_active_callback_filter', array(), false),
    ));


    add_filter('mesmerize_hero_content_vertical_align', function ($align_class) {
        return get_theme_mod('header_text_box_text_vertical_align', $align_class);
    });


    mesmerize_front_page_header_text_options();

    mesmerize_add_options_group(array(
        "mesmerize_front_page_header_media_box_options" => array(
            $section,
            $prefix,
            $priority,
        ),

        "mesmerize_front_page_header_frame_options" => array(
            $section,
            $prefix,
            $priority + 1,
        ),
    ));
}
