<?php

function mesmerize_get_header_presets()
{
    global $MESMERIZE_HEADER_PRESETS;


    $result       = array();
    $presets_file = get_template_directory() . '/customizer/presets.php';
    if (file_exists($presets_file) && ! isset($MESMERIZE_HEADER_PRESETS)) {
        $MESMERIZE_HEADER_PRESETS = require $presets_file;
    }

    if (isset($MESMERIZE_HEADER_PRESETS)) {
        $result = $MESMERIZE_HEADER_PRESETS;
    }


    $result = apply_filters('mesmerize_header_presets', $result);
    $result = mesmerize_filter_defaults($result);

    return $result;

}

function mesmerize_filter_defaults($data)
{
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $data[$key] = mesmerize_filter_defaults($value);
        } else {
            if (is_string($value)) {
                $data[$key] = str_replace(
                    array('[tag_companion_uri]', '[tag_theme_uri]', '[tag_style_uri]'),
                    apply_filters('mesmerize_assets_url',get_template_directory_uri(),'/'),
                    $value
                );
            }
        }
    }

    return $data;
}

add_filter('cloudpress\companion\ajax_cp_data', function ($data, $companion, $filter) {

    if ($filter !== "headers") {
        return $data;
    }

    $data['headers'] = isset($data['headers']) ? $data['headers'] : array();
    $data['headers'] = array_merge($data['headers'], mesmerize_get_header_presets());

    return $data;
}, 10, 3);


add_action("mesmerize_customize_register", function ($wp_customize) {
    /** @var WP_Customize_Manager $wp_customize */
    $wp_customize->add_setting('header_presets', array(
        'default'           => "",
        'sanitize_callback' => 'esc_html',
        "transport"         => "postMessage",
    ));

    $wp_customize->add_control(new Mesmerize\RowsListControl($wp_customize, 'header_presets', array(
        'label'       => esc_html__('Background Type', 'mesmerize'),
        'section'     => 'header_layout',
        "insertText"  => esc_html__("Apply Preset", "mesmerize"),
        'pro_message' => false,
        "type"        => "presets_changer",
        "dataSource"  => array(
            "use_ajax" => true,
            "filter"   => "headers",
        ),
        "priority"    => 2,
    )));


    $wp_customize->add_setting('frontpage_header_presets_pro', array(
        'default'           => "",
        'sanitize_callback' => 'esc_html',
        "transport"         => "postMessage",
    ));


    if ( ! apply_filters('mesmerize_is_companion_installed', false)) {
        $wp_customize->add_control(new Mesmerize\Info_PRO_Control($wp_customize, 'frontpage_header_presets_pro',
            array(
                'label'     => esc_html__('18 more beautiful header designs are available in the PRO version. @BTN@', 'mesmerize'),
                'section'   => 'header_layout',
                'priority'  => 10,
                'transport' => 'postMessage',
            )));
    }
});
