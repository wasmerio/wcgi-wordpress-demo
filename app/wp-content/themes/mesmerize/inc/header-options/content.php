<?php

require_once get_template_directory() . "/inc/header-options/content-options/content-type.php";
require_once get_template_directory() . "/inc/header-options/content-options/inner-pages.php";
require_once get_template_directory() . "/inc/header-options/content-options/title.php";
require_once get_template_directory() . "/inc/header-options/content-options/subtitle.php";
require_once get_template_directory() . "/inc/header-options/content-options/buttons.php";


add_action("mesmerize_customize_register_options", function () {
    mesmerize_add_options_group(array(
        "mesmerize_front_page_header_title_options" => array(
            // section, prefix, priority
            "header_background_chooser",
            "header",
            6,
        ),
        
        "mesmerize_front_page_header_subtitle_options" => array(
            "header_background_chooser",
            "header",
            6,
        ),
        
        "mesmerize_front_page_header_buttons_options" => array(
            "header_background_chooser",
            "header",
            6,
        ),
        
        "mesmerize_front_page_header_content_options" => array(
            "header_background_chooser",
            "header",
            5,
        ),
        
        "mesmerize_inner_pages_header_content_options" => array(
            "header_image",
            "inner_header",
            9,
        ),
    ));
});


if ( ! function_exists("mesmerize_print_header_content")) {
    function mesmerize_print_header_content()
    {
        do_action("mesmerize_print_header_content");
    }
}

function mesmerize_get_media_types()
{
    return apply_filters('mesmerize_media_type_choices', array(
        "image" => esc_html__("Image", "mesmerize"),
    ));
}

function mesmerize_get_partial_types()
{
    return apply_filters('mesmerize_header_content_partial', array(
        "content-on-center" => esc_html__("Text on center", "mesmerize"),
        "content-on-right"  => esc_html__("Text on right", "mesmerize"),
        "content-on-left"   => esc_html__("Text on left", "mesmerize"),
        "media-on-left"     => esc_html__("Text with media on left", "mesmerize"),
        "media-on-right"    => esc_html__("Text with media on right", "mesmerize"),
    ));
}

function mesmerize_get_front_page_header_media_and_partial()
{
    $partial   = get_theme_mod('header_content_partial', mesmerize_mod_default('header_content_partial'));
    $mediaType = get_theme_mod('header_content_media', 'image');
    
    if ( ! array_key_exists($partial, mesmerize_get_partial_types())) {
        $partial = mesmerize_mod_default('header_content_partial');
    }
    
    
    if ( ! array_key_exists($mediaType, mesmerize_get_media_types())) {
        $is_handled = apply_filters('mesmerize_media_type_custom_handled', false, $mediaType);
        if ( ! $is_handled) {
            $mediaType = 'image';
        }
    }
    
    
    return array(
        'partial' => $partial,
        'media'   => $mediaType,
    );
    
}

function mesmerize_print_front_page_header_content()
{
    $headerContent = mesmerize_get_front_page_header_media_and_partial();
    $partial       = $headerContent['partial'];
    $classes       = apply_filters('mesmerize_header_description_classes', $partial);
    
    do_action('mesmerize_before_front_page_header_content');
    
    ?>

    <div class="header-description gridContainer <?php echo esc_attr($classes); ?>">
        <?php get_template_part('template-parts/header/hero', $partial); ?>
    </div>
    
    <?php
    
    do_action('mesmerize_after_front_page_header_content');
}

function mesmerize_print_header_media_frame($media)
{
    $frame_type = get_theme_mod('header_content_frame_type', "border");
    if ($frame_type === "none") {
        echo $media;
        
        return;
    }
    
    
    $frame_width  = intval(get_theme_mod('header_content_frame_width', "100"));
    $frame_height = intval(get_theme_mod('header_content_frame_height', "100"));
    
    $frame_offset_left = intval(get_theme_mod('header_content_frame_offset_left', "-13"));
    $frame_offset_top  = intval(get_theme_mod('header_content_frame_offset_top', "10"));
    $frame_over_image  = get_theme_mod('header_content_frame_show_over_image', false);
    $frame_color       = get_theme_mod('header_content_frame_color', "rgba(255,255,255,0.726)");
    $frame_thickness   = intval(get_theme_mod('header_content_frame_thickness', 11));
    $frame_shadow      = get_theme_mod('header_content_frame_shadow', true);
    $frame_hide        = get_theme_mod('header_content_frame_hide_on_mobile', true);
    
    $z_index = $frame_over_image ? 1 : -1;
    
    $style = "transform:translate($frame_offset_left%, $frame_offset_top%);";
    $style .= "width:{$frame_width}%;height:{$frame_height}%;";
    $style .= "{$frame_type}-color:{$frame_color};";
    $style .= "z-index:$z_index;";
    
    if ($frame_type == "border") {
        $style .= "border-width:{$frame_thickness}px;";
    }
    
    $classes = "overlay-box-offset  offset-" . $frame_type . " ";
    
    if ($frame_shadow) {
        $classes .= "shadow-medium ";
    }
    
    if ($frame_hide) {
        $classes .= "hide-xs ";
    }
    
    $headerContent = mesmerize_get_front_page_header_media_and_partial();
    $partial       = $headerContent['partial'];
    
    $align = "";
    if (in_array($partial, array("media-on-right", "media-on-left"))) {
        $align = "end-sm";
    }
    ?>
    <div class="flexbox center-xs <?php echo $align; ?> middle-xs">
        <div class="overlay-box">
            <div class="<?php echo esc_attr($classes); ?>" style="<?php echo esc_attr($style); ?>"></div>
            <?php echo $media; ?>
        </div>
    </div>
    <?php
}

add_action("mesmerize_print_header_media", function ($mediaType) {
    if ($mediaType == "image") {
        $roundImage   = get_theme_mod('header_content_image_rounded', false);
        $extraClasses = "";
        if (intval($roundImage)) {
            $extraClasses .= " round";
        }
        
        $image = get_theme_mod('header_content_image', apply_filters('mesmerize_assets_url', get_template_directory_uri() , '/') . "/assets/images/media-image-default.jpg");
        
        $customizerLink = "";
        
        if (mesmerize_is_customize_preview()) {
            $customizerLink = "data-type=\"group\" data-focus-control=\"header_content_image\"";
        }
        
        if (is_numeric($image)) {
            $image = wp_get_attachment_image_src(absint($image), 'full', false);
            if ($image) {
                list($src, $width, $height) = $image;
                $image = $src;
            } else {
                $image = "#";
            }
        }
        
        if ( ! empty($image)) {
            $image = sprintf('<img class="homepage-header-image %2$s" %3$s src="%1$s"/>', esc_url($image), esc_attr($extraClasses), $customizerLink);
            mesmerize_print_header_media_frame($image);
        }
    }
});

if ( ! function_exists('mesmerize_print_header_media')) {
    function mesmerize_print_header_media()
    {
        $headerContent = mesmerize_get_front_page_header_media_and_partial();
        $mediaType     = $headerContent['media'];
        
        do_action('mesmerize_print_header_media', $mediaType);
        
    }
}


add_action('mesmerize_after_front_page_header_content', 'mesmerize_print_default_after_header_content');
add_action('mesmerize_after_inner_page_header_content', 'mesmerize_print_default_after_header_content');

function mesmerize_get_header_top_spacing_script()
{
    ob_start();
    ?>
    <script>
        (function () {
            function setHeaderTopSpacing() {

                setTimeout(function() {
                  var headerTop = document.querySelector('.header-top');
                  var headers = document.querySelectorAll('.header-wrapper .header,.header-wrapper .header-homepage');

                  for (var i = 0; i < headers.length; i++) {
                      var item = headers[i];
                      item.style.paddingTop = headerTop.getBoundingClientRect().height + "px";
                  }

                    var languageSwitcher = document.querySelector('.mesmerize-language-switcher');

                    if(languageSwitcher){
                        languageSwitcher.style.top = "calc( " +  headerTop.getBoundingClientRect().height + "px + 1rem)" ;
                    }
                    
                }, 100);

             
            }

            window.addEventListener('resize', setHeaderTopSpacing);
            window.mesmerizeSetHeaderTopSpacing = setHeaderTopSpacing
            mesmerizeDomReady(setHeaderTopSpacing);
        })();
    </script>
    <?php
    
    $content = ob_get_clean();
    $content = strip_tags($content);
    
    return $content;
}

add_action('wp_enqueue_scripts', 'mesmerize_enqueue_header_top_spacing_script', 40);
function mesmerize_enqueue_header_top_spacing_script()
{
    wp_add_inline_script('jquery', mesmerize_get_header_top_spacing_script());
}

function mesmerize_print_default_after_header_content()
{
    //  execute top spacing script as soon as possible to prevent repositioning flicker
    ?>
    <script>
		if (window.mesmerizeSetHeaderTopSpacing) {
			window.mesmerizeSetHeaderTopSpacing();
		}
    </script>
    <?php
}


add_action('wp_head', 'mesmerize_print_background_content_color', PHP_INT_MAX);

function mesmerize_print_background_content_color()
{
    $background_color = '#' . str_replace("#", "", (get_background_color() ? get_background_color() : 'F5FAFD'));
	$background_image  = get_background_image();
	//if page has background image do not set page background to not hide it
    if ($background_image) $background_color = 'transparent';
    ?>
    <style data-name="background-content-colors">
        .mesmerize-inner-page .page-content,
        .mesmerize-inner-page .content,
        .mesmerize-front-page.mesmerize-content-padding .page-content {
            background-color: <?php echo $background_color;?>;
        }
    </style>
    <?php
}
