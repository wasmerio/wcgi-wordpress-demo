<?php

require_once get_template_directory() . "/inc/header-options/navigation-options/top-bar.php";
require_once get_template_directory() . "/inc/header-options/navigation-options/nav-bar.php";
require_once get_template_directory() . "/inc/header-options/navigation-options/offscreen.php";


add_action('mesmerize_add_sections', function ($wp_customize) {
    $sections = array(
        'navigation_top_bar'    => esc_html__('Top Bar', 'mesmerize'),
        'front_page_navigation' => esc_html__('Front Page Navigation', 'mesmerize'),
        'inner_page_navigation' => esc_html__('Inner Page Navigation', 'mesmerize'),
        'navigation_offscreen'  => esc_html__('Mobile (Offscreen) Navigation', 'mesmerize'),
    );

    foreach ($sections as $id => $title) {
        $wp_customize->add_section($id, array(
            'title'    => $title,
            'panel'    => 'navigation_panel',
            'priority' => 1
        ));
    }

});
