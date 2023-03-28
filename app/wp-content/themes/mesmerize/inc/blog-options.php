<?php

function mesmerize_add_blog_options($section)
{
    $priority = 1;
    
    mesmerize_add_kirki_field(array(
        'type'     => 'sectionseparator',
        'label'    => esc_html__('Blog Settings', 'mesmerize'),
        'section'  => $section,
        'settings' => "blog_section_settings_separator",
        'priority' => $priority,
    ));
    
    mesmerize_add_kirki_field(array(
        'type'     => 'checkbox',
        'settings' => 'blog_sidebar_enabled',
        'label'    => esc_html__('Show blog sidebar', 'mesmerize'),
        'section'  => $section,
        'default'  => true,
    ));
    
    
    mesmerize_add_kirki_field(array(
        'type'     => 'checkbox',
        'settings' => 'show_single_item_title',
        'label'    => esc_html__('Show post title in post page', 'mesmerize'),
        'section'  => $section,
        'default'  => true,
    ));
    
    mesmerize_add_kirki_field(array(
        'type'     => 'checkbox',
        'settings' => 'blog_post_meta_enabled',
        'label'    => esc_html__('Show post meta', 'mesmerize'),
        'section'  => $section,
        'default'  => true,
    ));
    
    
    mesmerize_add_kirki_field(array(
        'type'     => 'checkbox',
        'settings' => 'blog_post_highlight_enabled',
        'label'    => esc_html__('Highlight first post', 'mesmerize'),
        'section'  => $section,
        'default'  => true,
    ));
    
    
    $posts_per_row = array();
    
    foreach (array(1, 2, 3, 4) as $class) {
        $posts_per_row[$class] = sprintf(_n('%s item', '%s items', $class, "mesmerize"), $class);
    }
    
    
    mesmerize_add_kirki_field(array(
        'type'     => 'select',
        'settings' => 'blog_posts_per_row',
        'label'    => esc_html__('Post per row', 'mesmerize'),
        'section'  => $section,
        'default'  => mesmerize_mod_default('blog_posts_per_row'),
        'choices'  => $posts_per_row,
    ));
    
    mesmerize_add_kirki_field(array(
        'type'        => 'checkbox',
        'settings'    => 'blog_show_post_featured_image',
        'label'       => esc_html__('Use post featured image as hero background when available', 'mesmerize'),
        'description' => esc_html__('The inner pages hero background should be set to image', 'mesmerize'),
        'section'     => $section,
        'priority'    => 3,
        'default'     => true,
    ));
    
    
    mesmerize_add_kirki_field(array(
        'type'     => 'checkbox',
        'settings' => 'blog_show_post_thumb_placeholder',
        'label'    => esc_html__('Show thumbnail placeholder', 'mesmerize'),
        'section'  => $section,
        'default'  => true,
    ));
    
    mesmerize_add_kirki_field(array(
        'type'            => 'color',
        'label'           => esc_html__('Placeholder Background Color', 'mesmerize'),
        'section'         => $section,
        'settings'        => 'blog_post_thumb_placeholder_color',
        'default'         => mesmerize_get_theme_colors('color2'),
        'active_callback' => array(
            array(
                'setting'  => 'blog_show_post_thumb_placeholder',
                'operator' => '==',
                'value'    => true,
            ),
        ),
    ));
    
}

mesmerize_add_blog_options('blog_settings');


function mesmerize_show_post_meta_setting_filter($value)
{
    
    $value = get_theme_mod('blog_post_meta_enabled', $value);
    
    return $value;
}

add_filter('mesmerize_show_post_meta', 'mesmerize_show_post_meta_setting_filter');


function mesmerize_posts_per_row_setting_filter($value)
{
    
    $value = get_theme_mod('blog_posts_per_row', $value);
    
    return $value;
}

add_filter('mesmerize_posts_per_row', 'mesmerize_posts_per_row_setting_filter');

function mesmerize_archive_post_highlight_setting_filter($value)
{
    
    $value = get_theme_mod('blog_post_highlight_enabled', $value);
    
    return $value;
}

add_filter('mesmerize_archive_post_highlight', 'mesmerize_archive_post_highlight_setting_filter');


function mesmerize_blog_sidebar_enabled_setting_filter($value)
{
    
    $value = intval(get_theme_mod('blog_sidebar_enabled', $value));
    
    return ($value === 1);
}

add_filter('mesmerize_blog_sidebar_enabled', 'mesmerize_blog_sidebar_enabled_setting_filter');
