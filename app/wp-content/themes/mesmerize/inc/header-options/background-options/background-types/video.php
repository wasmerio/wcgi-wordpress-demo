<?php

add_filter('mesmerize_header_background_types', 'mesmerize_header_background_video');

function mesmerize_header_background_video($types)
{
    $types['video'] = esc_html__('Video', 'mesmerize');

    return $types;
}

add_filter("mesmerize_header_background_atts", function ($attrs, $bg_type, $inner) {
    if ($bg_type == 'video') {
        $attrs['class'] .= " cp-video-bg";
    }

    return $attrs;
}, 1, 3);

add_action("mesmerize_background", function ($bg_type, $inner, $prefix) {
    if ($bg_type == 'video') {
        $internalVideo = get_theme_mod($prefix . '_video', "");
        $video_url     = get_theme_mod($prefix . '_video_external', "");
        $videoPoster   = get_theme_mod($prefix . '_video_poster', apply_filters('mesmerize_assets_url', get_template_directory_uri() , '/') . "/assets/images/video-poster.jpg");

        if ($internalVideo) {
            $video_url = wp_get_attachment_url($internalVideo);
            // apply core filter
            $video_url = apply_filters('get_header_video_url', $video_url);
        }

        $video_type = wp_check_filetype(esc_url_raw($video_url), wp_get_mime_types());
        $header     = get_custom_header();
        $settings   = array(
            'mimeType'  => '',
            'videoUrl'  => esc_url_raw($video_url),
            'posterUrl' => esc_url_raw($videoPoster),
            'width'     => absint($header->width),
            'height'    => absint($header->height),
            'minWidth'  => 768,
            'minHeight' => 300,
            'l10n'      => array(
                'pause'      => esc_html__('Pause', 'mesmerize'),
                'play'       => esc_html__('Play', 'mesmerize'),
                'pauseSpeak' => esc_html__('Video is paused.', 'mesmerize'),
                'playSpeak'  => esc_html__('Video is playing.', 'mesmerize'),
            ),
        );

        if (preg_match('#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#', $video_url)) {
            $settings['mimeType'] = 'video/x-youtube';
        } else if ( ! empty($video_type['type'])) {
            $settings['mimeType'] = $video_type['type'];
        }

        // apply core filter
        $settings = apply_filters('header_video_settings', $settings);

        // enqueue core script for video feature //
        wp_enqueue_script('wp-custom-header');
        wp_localize_script('wp-custom-header', '_wpCustomHeaderSettings', $settings);
        wp_enqueue_script('mesmerize-video-bg', get_template_directory_uri() . "/assets/js/video-bg.js", array('wp-custom-header'));
        
       
	}
}, 1, 3);


add_action("mesmerize_header_background_type_settings", "mesmerize_header_background_type_video_settings", 2, 6);

function mesmerize_header_background_type_video_settings($section, $prefix, $group, $inner, $priority)
{
    /* video settings */

    $prefix  = $inner ? "inner_header" : "header";
    $section = $inner ? "header_image" : "header_background_chooser";

    $group = "{$prefix}_bg_options_group_button";

    mesmerize_add_kirki_field(array(
        'type'            => 'sectionseparator',
        'label'           => esc_html__('Video Background Options', 'mesmerize'),
        'section'         => $section,
        'settings'        => $prefix . '_video_background_options_separator',
        'priority'        => 2,
        'active_callback' => array(
            array(
                'setting'  => $prefix . '_background_type',
                'operator' => '==',
                'value'    => 'video',
            ),
        ),
        'group'           => $group,
    ));

    mesmerize_add_kirki_field(array(
        'type'              => 'media',
        'settings'          => $prefix . '_video',
        'label'             => esc_html__('Self hosted video (MP4)', 'mesmerize'),
        'section'           => $section,
        'mime_type'         => 'video',
        'default'           => '',
        "priority"          => 2,
        'sanitize_callback' => 'sanitize_text_field',
        'active_callback'   => array(
            array(
                'setting'  => $prefix . '_background_type',
                'operator' => '==',
                'value'    => 'video',
            ),
        ),
        'group'             => $group,
    ));

    mesmerize_add_kirki_field(array(
        'type'              => 'url',
        'settings'          => $prefix . '_video_external',
        'label'             => esc_html__('External Video', 'mesmerize'),
        'section'           => $section,
        'default'           => "",
        'priority'          => 2,
        'sanitize_callback' => 'esc_url_raw',
        'active_callback'   => array(
            array(
                'setting'  => $prefix . '_background_type',
                'operator' => '==',
                'value'    => 'video',
            ),
        ),
        'group'             => $group,
    ));

    mesmerize_add_kirki_field(array(
        'type'              => 'image',
        'settings'          => $prefix . '_video_poster',
        'label'             => esc_html__('Video Poster', 'mesmerize'),
        'section'           => $section,
        'default'           => apply_filters('mesmerize_assets_url', get_template_directory_uri() , '/') . "/assets/images/video-poster.jpg",
        "priority"          => 2,
        'sanitize_callback' => 'esc_url_raw',
        'active_callback'   => array(
            array(
                'setting'  => $prefix . '_background_type',
                'operator' => '==',
                'value'    => 'video',
            ),
        ),
        'group'             => $group,
    ));

}

function mesmerize_print_video_container()
{
    $inner  = mesmerize_is_inner(true);
    $prefix = $inner ? "inner_header" : "header";
    $bgType = get_theme_mod($prefix . "_background_type", null);
    $poster = get_theme_mod($prefix . '_video_poster', apply_filters('mesmerize_assets_url', get_template_directory_uri() , '/') . "/assets/images/video-poster.jpg");

    if ($bgType === "video"):
        ?>
        <script>
            // resize the poster image as fast as possible to a 16:9 visible ratio
            var mesmerize_video_background = {
                getVideoRect: function () {
                    var header = document.querySelector(".cp-video-bg");
                    var headerWidth = header.getBoundingClientRect().width,
                        videoWidth = headerWidth,
                        videoHeight = header.getBoundingClientRect().height;

                    videoWidth = Math.max(videoWidth, videoHeight);

                    if (videoWidth < videoHeight * 16 / 9) {
                        videoWidth = 16 / 9 * videoHeight;
                    } else {
                        videoHeight = videoWidth * 9 / 16;
                    }

                    videoWidth *= 1.2;
                    videoHeight *= 1.2;

                    var marginLeft = -0.5 * (videoWidth - headerWidth);

                    return {
                        width: Math.round(videoWidth),
                        height: Math.round(videoHeight),
                        left: Math.round(marginLeft)
                    }
                },

                resizePoster: function () {
//                        debugger;
                    var posterHolder = document.querySelector('#wp-custom-header');

                    var size = mesmerize_video_background.getVideoRect();
                    posterHolder.style.backgroundSize = size.width + 'px auto';
                    posterHolder.style.backgroundPositionX = size.left + 'px';
                    posterHolder.style.minHeight = size.height + 'px';


                }

            };

        </script>
        <div id="wp-custom-header" class="wp-custom-header cp-video-bg"></div>
        <style>
            .header-wrapper {
                background: transparent;
            }

            div#wp-custom-header.cp-video-bg {
                background-image: url('<?php echo esc_url($poster); ?>');
                background-color: #000000;
                background-position: center top;
                background-size: cover;
                position: absolute;
                z-index: -3;
                height: 100%;
                width: 100%;
                margin-top: 0;
                top: 0px;
                -webkit-transform: translate3d(0, 0, -2px);
            }

            .header-homepage.cp-video-bg,
            .header.cp-video-bg {
                background-color: transparent !important;
                overflow: hidden;
            }

            div#wp-custom-header.cp-video-bg #wp-custom-header-video {
                object-fit: cover;
                position: absolute;
                opacity: 0;
                width: 100%;
                transition: opacity 0.4s cubic-bezier(0.44, 0.94, 0.25, 0.34);
            }

            div#wp-custom-header.cp-video-bg button#wp-custom-header-video-button {
                display: none;
            }
        </style>
        <script type="text/javascript">
            setTimeout(mesmerize_video_background.resizePoster, 0);
        </script>
        <?php
    endif;
}
