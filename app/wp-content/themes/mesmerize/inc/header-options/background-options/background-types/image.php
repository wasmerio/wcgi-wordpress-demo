<?php


add_filter('mesmerize_header_background_types', 'mesmerize_header_background_image');

function mesmerize_header_background_image($types)
{
    $types['image'] = esc_html__('Image', 'mesmerize');

    return $types;
}


add_filter('mesmerize_override_with_thumbnail_image', function ($value) {

    global $post;

    if (isset($post) && $post->post_type === 'page') {
        $value = get_theme_mod('inner_header_show_featured_image', true);
        $value = (intval($value) === 1);
    }

    return $value;
});

add_filter('mesmerize_override_with_thumbnail_image', function ($value) {

    global $post;

    if (isset($post) && $post->post_type === 'post' && is_single()) {
        $value = get_theme_mod('blog_show_post_featured_image', true);
        $value = (intval($value) === 1);
    }

    return $value;
});


add_filter("mesmerize_header_background_atts", function ($attrs, $bg_type, $inner) {

    if ($bg_type == 'image') {
        $prefix        = $inner ? "inner_header" : "header";
        $bgImage       = $inner ? get_header_image() : get_theme_mod($prefix . '_front_page_image', mesmerize_mod_default($prefix . '_front_page_image'));
        $bgImageMobile = $inner ? get_header_image() : get_theme_mod($prefix . '_front_page_image_mobile', false);

        $bgColor = get_theme_mod($prefix . '_bg_color_image', "#6a73da");

        if ($inner && apply_filters('mesmerize_override_with_thumbnail_image', false)) {
            global $post;
            if ($post) {
                $thumbnail = get_the_post_thumbnail_url($post->ID, 'mesmerize-full-hd');

                $thumbnail = apply_filters('mesmerize_overriden_thumbnail_image', $thumbnail);

                if ($thumbnail) {
                    $bgImage = $thumbnail;
                }
            }
        }

        $attrs['style'] .= '; background-image:url("' . mesmerize_esc_url($bgImage) . '")';
        $attrs['style'] .= '; background-color:' . $bgColor;

        if ($bgImageMobile) {
            $attrs['class'] = isset($attrs['class']) ? $attrs['class'] . " custom-mobile-image " : "custom-mobile-image ";
        }

        $parallax = get_theme_mod($prefix . "_parallax", true);
        if ($parallax) {
            $attrs['data-parallax-depth'] = "20";
        }
    }

    return $attrs;
}, 1, 3);


function mesmerize_header_background_mobile_image()
{
    $inner = mesmerize_is_inner(true);

    if ($inner) {
        return;
    }

    $prefix                 = $inner ? "inner_header" : "header";
    $bgType                 = get_theme_mod($prefix . '_background_type', mesmerize_mod_default($prefix . '_background_type'));
    $bgImageMobile          = $inner ? get_header_image() : get_theme_mod($prefix . '_front_page_image_mobile', false);
    $bgMobilePosition       = get_theme_mod($prefix . "_bg_position_mobile", '50%');
    $bgMobilePositionOffset = get_theme_mod($prefix . "_bg_position_mobile_offset", '0');

    $bgMobilePosition = str_replace(array('%', 'px',), "", $bgMobilePosition);

    $bgMobilePosition = $bgMobilePosition . "% " . $bgMobilePositionOffset . "px";
    ?>

    <?php if ($bgType === "image"): ?>

    <style type="text/css" data-name="custom-mobile-image-position">
        @media screen and (max-width: 767px) {
            /*Custom mobile position*/
        <?php echo $inner ? '.header' : '.header-homepage' ?> {
            background-position: <?php echo  esc_attr($bgMobilePosition) ?>;
        }
        }
    </style>

    <?php if ($bgImageMobile): ?>
        <style type="text/css" data-name="custom-mobile-image">

            /*Custom mobile image*/
            @media screen and (max-width: 767px) {
                .custom-mobile-image:not(.header-slide) {
                    background-image: url(<?php echo esc_url_raw(  $bgImageMobile) ?>) !important;
                }
            }


        </style>

    <?php endif; ?>

    <?php endif;

}

add_action('wp_head', 'mesmerize_header_background_mobile_image');

add_action("mesmerize_header_background_type_settings", 'mesmerize_header_background_type_image_settings', 1, 6);

function mesmerize_header_background_type_image_settings($section, $prefix, $group, $inner, $priority)
{
    $prefix  = $inner ? "inner_header" : "header";
    $section = $inner ? "header_image" : "header_background_chooser";

    $group = "{$prefix}_bg_options_group_button";

    /* image settings */

    mesmerize_add_kirki_field(array(
        'type'            => 'sectionseparator',
        'label'           => esc_html__('Image Background Options', 'mesmerize'),
        'section'         => $section,
        'settings'        => $prefix . '_image_background_options_separator',
        'priority'        => 2,
        'active_callback' => array(
            array(
                'setting'  => $prefix . '_background_type',
                'operator' => '==',
                'value'    => 'image',
            ),
        ),
        'group'           => $group,
    ));

    if ( ! $inner) {
        mesmerize_add_kirki_field(array(
            'type'              => 'image',
            'settings'          => $prefix . '_front_page_image',
            'label'             => esc_html__('Header Image', 'mesmerize'),
            'section'           => $section,
            'sanitize_callback' => 'esc_url_raw',
            'default'           => mesmerize_mod_default($prefix . '_front_page_image'),
            "priority"          => 2,
            'group'             => $group,
            'transport'         => 'postMessage',
            'active_callback'   => array(
                array(
                    'setting'  => $prefix . '_background_type',
                    'operator' => '==',
                    'value'    => 'image',
                ),
            ),
        ));

    }

    mesmerize_add_kirki_field(array(
        'type'            => 'select',
        'settings'        => $prefix . '_bg_position',
        'label'           => esc_html__('Background Position', 'mesmerize'),
        'section'         => $section,
        'priority'        => 2,
        'default'         => "center center",
        'choices'         => array(
            "left top"    => "left top",
            "left center" => "left center",
            "left bottom" => "left bottom",

            "center top"    => "center top",
            "center center" => "center center",
            "center bottom" => "center bottom",

            "right top"    => "right top",
            "right center" => "right center",
            "right bottom" => "right bottom",

        ),
        "output"          => array(
            array(
                'element'     => $inner ? '.header' : '.header-homepage',
                'media_query' => '@media screen and (min-width: 768px)',
                'property'    => 'background-position',
            ),
        ),
        'transport'       => 'postMessage',
        'js_vars'         => array(
            array(
                'element'     => $inner ? '.header' : '.header-homepage',
                'media_query' => '@media screen and (min-width: 768px)',
                'property'    => 'background-position',
            ),
        ),
        'active_callback' => array(
            array(
                'setting'  => $prefix . '_background_type',
                'operator' => '==',
                'function' => 'style',
                'value'    => 'image',
            ),
        ),
        'group'           => $group,
    ));


    mesmerize_add_kirki_field(array(
        'type'            => 'color',
        'label'           => esc_html__('Background Color', 'mesmerize'),
        'section'         => $section,
        'priority'        => 2,
        'settings'        => $prefix . '_bg_color_image',
        'default'         => '#6a73da',
        'transport'       => 'postMessage',
        'active_callback' => array(
            array(
                'setting'  => $prefix . '_background_type',
                'operator' => '==',
                'value'    => 'image',
            ),
        ),
        'js_vars'         => array(
            array(
                'element'  => $inner ? '.header' : '.header-homepage',
                'property' => 'background-color',
                'suffix'   => ' !important',
            ),
        ),
        'group'           => $group,
    ));

    if ($inner) {
        mesmerize_add_kirki_field(array(
            'type'            => 'checkbox',
            'settings'        => $prefix . '_show_featured_image',
            'label'           => esc_html__('Show page featured image when available', 'mesmerize'),
            'section'         => $section,
            'priority'        => 3,
            'default'         => true,
            'active_callback' => array(
                array(
                    'setting'  => $prefix . '_background_type',
                    'operator' => '==',
                    'value'    => 'image',
                ),
            ),
            'group'           => $group,
        ));

    }

    mesmerize_add_kirki_field(array(
        'type'            => 'checkbox',
        'settings'        => $prefix . '_parallax',
        'label'           => esc_html__('Enable parallax effect', 'mesmerize'),
        'section'         => $section,
        'priority'        => 3,
        'default'         => true,
        'transport'       => 'postMessage',
        'active_callback' => array(
            array(
                'setting'  => $prefix . '_background_type',
                'operator' => '==',
                'value'    => 'image',
            ),
        ),
        'group'           => $group,
    ));


    if ( ! $inner) {


        mesmerize_add_kirki_field(array(
            'type'            => 'sectionseparator',
            'label'           => esc_html__('Mobile Image Background Options', 'mesmerize'),
            'section'         => $section,
            'settings'        => $prefix . '_image_mobile_background_options_separator',
            'priority'        => 2,
            'active_callback' => array(
                array(
                    'setting'  => $prefix . '_background_type',
                    'operator' => '==',
                    'value'    => 'image',
                ),
            ),
            'group'           => $group,
        ));

        mesmerize_add_kirki_field(array(
            'type'              => 'image',
            'settings'          => $prefix . '_front_page_image_mobile',
            'label'             => esc_html__('Mobile Only Image', 'mesmerize'),
            'description'       => esc_html__('Leave this field empty if you want to use the main image header image', 'mesmerize'),
            'section'           => $section,
            'sanitize_callback' => 'esc_url_raw',
            "priority"          => 2,
            'group'             => $group,
            'transport'         => 'postMessage',
            'active_callback'   => array(
                array(
                    'setting'  => $prefix . '_background_type',
                    'operator' => '==',
                    'value'    => 'image',
                ),
            ),
        ));


        mesmerize_add_kirki_field(array(
            'type'     => 'select',
            'settings' => $prefix . '_bg_position_mobile',
            'label'    => esc_html__('Mobile Bg. Horizontal Position', 'mesmerize'),
            'section'  => $section,
            'priority' => 2,
            'default'  => "50%",
            'choices'  => array(
                "0%"   => "left",
                "50%"  => "center",
                "100%" => "right",
            ),
            'transport'       => 'postMessage',
            'active_callback' => array(
                array(
                    'setting'  => $prefix . '_background_type',
                    'operator' => '==',
                    'value'    => 'image',
                ),
            ),
            'group'           => $group,
        ));


        mesmerize_add_kirki_field(array(
            'type'      => 'slider',
            'label'     => esc_html__('Mobile Bg. Vertical Offset', 'mesmerize'),
            'section'   => $section,
            'priority'  => $priority,
            'settings'  => $prefix . '_bg_position_mobile_offset',
            'default'   => "0",
            'transport' => 'postMessage',
            'choices'   => array(
                'min'  => '-500',
                'max'  => '500',
                'step' => '1',
            ),
            'js_vars' => array(
                array(
                    'element'  => '.mesmerize-fake-selector',
                    'property' => 'backgroun-position-t',
                ),
            ),
            'active_callback' => array(
                array(
                    'setting'  => $prefix . '_background_type',
                    'operator' => '==',
                    'value'    => 'image',
                ),
            ),
            'group'           => $group,
        ));


    }


    add_filter($group . "_filter", function ($settings) use ($prefix) {

        $new_settings = array(
            "_parallax_pro",
        );

        foreach ($new_settings as $key => $value) {
            $settings[] = $prefix . $value;
        }

        return $settings;
    });

}
