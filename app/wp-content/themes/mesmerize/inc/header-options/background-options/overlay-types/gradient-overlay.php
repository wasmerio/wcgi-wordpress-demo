<?php

add_filter("mesmerize_overlay_types", function ($types) {
    $types['gradient'] = esc_html__('Gradient', 'mesmerize');
    return $types;
});

add_action("mesmerize_header_background_overlay_settings", function($section, $prefix, $group, $inner, $priority) {
    mesmerize_add_kirki_field(array(
        'type'            => 'gradient-control',
        'label'           => esc_html__('Gradient', 'mesmerize'),
        'section'         => $section,
        'settings'        => $prefix . '_overlay_gradient_colors',
        'default'         => json_encode(mesmerize_mod_default($prefix . '_overlay_gradient_colors')),
        'choices'         => array(
            'opacity' => 0.8,
        ),
        'active_callback' => array(
            array(
                'setting'  => $prefix . '_overlay_type',
                'operator' => '==',
                'value'    => 'gradient',
            ),
            array(
                'setting'  => $prefix . '_show_overlay',
                'operator' => '==',
                'value'    => true,
            ),
        ),
        'priority'        => $priority,
        'transport'       => 'postMessage',
        'group'           => $group,
    ));
}, 1, 5);

function mesmerize_print_background_overlay()
{
    $inner  = mesmerize_is_inner(true);
    $prefix = $inner ? "inner_header" : "header";
    $type   = get_theme_mod($prefix . '_overlay_type', mesmerize_mod_default($prefix . '_overlay_type'));
    $overlay_enabled = get_theme_mod($prefix . '_show_overlay', true);
    if ($type == "gradient" && $overlay_enabled) {
        echo '<div class="background-overlay"></div>';
    }
}

add_action("mesmerize_before_header_background", "mesmerize_print_background_overlay");

function mesmerize_get_gradient_value($colors, $angle)
{

    if(!is_array($colors) || empty($colors)){
        return '';
    }

    $angle    = intval($angle);
    $color1 = $colors[0]['color'];
    $color2 = $colors[1]['color'];
    $gradient = "{$angle}deg , {$color1} 0%, {$color2} 100%";
    $gradient = 'linear-gradient(' . $gradient . ')';
    return $gradient;
}

// print gradient overlay option
add_action('wp_head', function () {
    
    $inner = mesmerize_is_inner(true);

    if ($inner) {
        $prefix = 'inner_header';
    } else {
        $prefix = 'header';
    }

    $type = get_theme_mod($prefix.'_overlay_type', mesmerize_mod_default($prefix . '_overlay_type'));
    if ($type != "gradient") {
        return;
    }

    $colors = get_theme_mod($prefix . '_overlay_gradient_colors', "");
    
    if ($colors == "") {
        $colors = mesmerize_mod_default($prefix . '_overlay_gradient_colors');
    } else {
        if(is_string($colors)) {
            $colors = json_decode($colors, true);
        }
    }

    $gradient = mesmerize_get_gradient_value($colors['colors'], $colors['angle']);
    $selector = $inner ? ".header" : ".header-homepage";

    ?>
        <style data-name="header-gradient-overlay">
            <?php echo esc_attr($selector); ?> .background-overlay {
                background: <?php echo esc_attr($gradient); ?>;
            }
        </style>
    <?php
});
