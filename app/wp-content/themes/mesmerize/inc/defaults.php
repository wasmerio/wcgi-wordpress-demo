<?php

function mesmerize_theme_defaults()
{
    $gradients = mesmerize_get_parsed_gradients();
    
    $defaults = array(
        
        1 => array(
            'header_nav_sticked'       => true,
            'inner_header_nav_sticked' => true,
            
            'header_nav_transparent'       => false,
            'inner_header_nav_transparent' => false,
            
            'header_nav_border'       => false,
            'inner_header_nav_border' => false,
            
            'header_nav_border_thickness'       => 2,
            'inner_header_nav_border_thickness' => 2,
            
            'header_nav_border_color'       => "rgba(255, 255, 255, 1)",
            'inner_header_nav_border_color' => "rgba(255, 255, 255, 1)",
            
            
            'header_text_box_text_width' => 80,
            "header_text_box_text_align" => "left",
            "header_content_partial"     => "media-on-right",
            
            "header_spacing"        => array(
                "top"    => "5%",
                "bottom" => "8%",
            ),
            "header_spacing_mobile" => array(
                "top"    => "10%",
                "bottom" => "10%",
            ),
            "inner_header_spacing"  => array(
                "top"    => "8%",
                "bottom" => "8%",
            ),
            
            "enable_top_bar" => true,
            
            "header_background_type"       => 'image',
            "inner_header_background_type" => 'color',
            
            "header_front_page_image"       => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/home_page_header.jpg",
            "inner_header_front_page_image" => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/home_page_header.jpg",
            
            "header_slideshow"       => array(
                array("url" => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/slideshow_slide1.jpg"),
                array("url" => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/slideshow_slide2.jpg"),
            ),
            "inner_header_slideshow" => array(
                array("url" => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/slideshow_slide1.jpg"),
                array("url" => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/slideshow_slide2.jpg"),
            ),
            
            "header_overlay_type"       => 'gradient',
            "inner_header_overlay_type" => 'gradient',
            
            'header_overlay_color'       => "#000000",
            'inner_header_overlay_color' => "#000000",
            
            'header_overlay_gradient_colors'       => $gradients['red_salvation'],
            'inner_header_overlay_gradient_colors' => $gradients['red_salvation'],
            
            'header_overlay_opacity'       => 0.5,
            'inner_header_overlay_opacity' => 0.5,
            
            'header_overlay_shape'       => "none",
            'inner_header_overlay_shape' => "circles",
            
            'header_show_separator'       => false,
            'inner_header_show_separator' => false,
            
            'header_separator'       => 'mesmerize/1.wave-and-line',
            'inner_header_separator' => 'mesmerize/1.wave-and-line',
            
            'header_separator_color'       => '#ffffff',
            'inner_header_separator_color' => '#F5FAFD',
            
            'header_separator_height'       => 154,
            'inner_header_separator_height' => 154,
            
            'full_height_header' => false,
            'header_overlap'     => true,
            
            'blog_posts_per_row' => 2,
        ),
        
        2 => array(
            'header_nav_sticked'       => true,
            'inner_header_nav_sticked' => true,
            
            'header_nav_transparent'       => true,
            'inner_header_nav_transparent' => true,
            
            'header_nav_border'       => true,
            'inner_header_nav_border' => true,
            
            'header_nav_border_thickness'       => 1,
            'inner_header_nav_border_thickness' => 1,
            
            'header_nav_border_color'       => "rgba(255, 255, 255, 0.5)",
            'inner_header_nav_border_color' => "rgba(255, 255, 255, 0.5)",
            
            'header_text_box_text_width' => 85,
            
            "header_text_box_text_align" => "center",
            "header_content_partial"     => "content-on-center",
            
            "header_spacing"        => array(
                "top"    => "14%",
                "bottom" => "14%",
            ),
            "header_spacing_mobile" => array(
                "top"    => "10%",
                "bottom" => "10%",
            ),
            "inner_header_spacing"  => array(
                "top"    => "8%",
                "bottom" => "8%",
            ),
            
            "enable_top_bar" => false,
            
            "header_background_type"       => 'image',
            "inner_header_background_type" => 'color',
            
            "header_front_page_image"       => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/home_page_header-2.jpg",
            "inner_header_front_page_image" => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/home_page_header-2.jpg",
            
            "header_slideshow"       => array(
                array("url" => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/slideshow_slide1.jpg"),
                array("url" => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/slideshow_slide2.jpg"),
            ),
            "inner_header_slideshow" => array(
                array("url" => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/slideshow_slide1.jpg"),
                array("url" => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/slideshow_slide2.jpg"),
            ),
            
            "header_overlay_type"       => 'color',
            "inner_header_overlay_type" => 'gradient',
            
            'header_overlay_color'       => "#0c0070",
            'inner_header_overlay_color' => "#0c0070",
            
            'header_overlay_gradient_colors'       => $gradients['plum_plate'],
            'inner_header_overlay_gradient_colors' => $gradients['plum_plate'],
            
            'header_overlay_opacity'       => 0.5,
            'inner_header_overlay_opacity' => 0.5,
            
            'header_overlay_shape'       => "none",
            'inner_header_overlay_shape' => "circles",
            
            'header_show_separator'       => false,
            'inner_header_show_separator' => false,
            
            'header_separator'       => 'mesmerize/1.wave-and-line',
            'inner_header_separator' => 'mesmerize/1.wave-and-line',
            
            'header_separator_color'       => '#ffffff',
            'inner_header_separator_color' => '#F5FAFD',
            
            'header_separator_height'       => 154,
            'inner_header_separator_height' => 154,
            
            'full_height_header' => false,
            'header_overlap'     => true,
            
            'blog_posts_per_row' => 2,
        ),
        
        3 => array(
            'header_nav_sticked'       => true,
            'inner_header_nav_sticked' => true,
            
            'header_nav_transparent'       => true,
            'inner_header_nav_transparent' => true,
            
            'header_nav_border'       => true,
            'inner_header_nav_border' => true,
            
            'header_nav_border_thickness'       => 1,
            'inner_header_nav_border_thickness' => 1,
            
            'header_nav_border_color'       => "rgba(255, 255, 255, 0.5)",
            'inner_header_nav_border_color' => "rgba(255, 255, 255, 0.5)",
            
            'header_text_box_text_width' => 85,
            
            "header_text_box_text_align" => "center",
            "header_content_partial"     => "content-on-center",
            
            "header_spacing"        => array(
                "top"    => "14%",
                "bottom" => "14%",
            ),
            "header_spacing_mobile" => array(
                "top"    => "10%",
                "bottom" => "10%",
            ),
            "inner_header_spacing"  => array(
                "top"    => "8%",
                "bottom" => "8%",
            ),
            
            "enable_top_bar" => false,
            
            "header_background_type"       => 'image',
            "inner_header_background_type" => 'color',
            
            "header_front_page_image"       => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/home_page_header-3.jpg",
            "inner_header_front_page_image" => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/home_page_header-3.jpg",
            
            "header_slideshow"       => array(
                array("url" => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/slideshow_slide1.jpg"),
                array("url" => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/slideshow_slide2.jpg"),
            ),
            "inner_header_slideshow" => array(
                array("url" => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/slideshow_slide1.jpg"),
                array("url" => apply_filters('mesmerize_assets_url', get_template_directory_uri(), '/') . "/assets/images/slideshow_slide2.jpg"),
            ),
            
            "header_overlay_type"       => 'color',
            "inner_header_overlay_type" => 'color',
            
            'header_overlay_color'       => "#000000",
            'inner_header_overlay_color' => "#000000",
            
            'header_overlay_gradient_colors'       => $gradients['plum_plate'],
            'inner_header_overlay_gradient_colors' => $gradients['plum_plate'],
            
            'header_overlay_opacity'       => 0.6,
            'inner_header_overlay_opacity' => 0.6,
            
            'header_overlay_shape'       => "none",
            'inner_header_overlay_shape' => "circles",
            
            'header_show_separator'       => false,
            'inner_header_show_separator' => false,
            
            'header_separator'       => 'mesmerize/1.wave-and-line',
            'inner_header_separator' => 'mesmerize/1.wave-and-line',
            
            'header_separator_color'       => '#ffffff',
            'inner_header_separator_color' => '#F5FAFD',
            
            'header_separator_height'       => 154,
            'inner_header_separator_height' => 154,
            
            'full_height_header' => false,
            'header_overlap'     => true,
            
            'blog_posts_per_row' => 2,
        ),
    );
    
    return $defaults;
}


function mesmerize_default_preset_number()
{
    return 3;
}

add_action('after_switch_theme', function () {
    
    $current_preset = mesmerize_default_preset_number();
    
    $default_preset = get_theme_mod('theme_default_preset', false);
    $modified       = mesmerize_is_modified();
    
    if ( ! $default_preset && ! $modified) {
        set_theme_mod('theme_default_preset', $current_preset);
    }
    
    if ( ! $modified) {
        set_theme_mod('show_front_page_hero_by_default', true);
    }
    
    mesmerize_clear_cached_values();
});


function mesmerize_is_modified()
{
    $mods = get_theme_mods();
    foreach ((array)$mods as $mod => $value) {
        if (strpos($mod, "header") !== false) {
            return true;
        }
    }
    
    return false;
}
