<?php

mesmerize_require("inc/wp-forms/mesmerize-template.php");

function mesmerize_shortcode_render_can_apply_wpforms_filters($shortcode)
{
    if ( ! class_exists("WPForms")) {
        return false;
    }
    
    if (strpos($shortcode, '[wpforms') !== false) {
        return true;
    }
    
    if (strpos($shortcode, '[mesmerize_contact_form') !== false && strpos($shortcode, 'wpforms') !== false) {
        return true;
    }
    
    return false;
}


add_action('cloudpress\customizer\before_render_shortcode', function ($shortcode) {
    
    if (mesmerize_shortcode_render_can_apply_wpforms_filters($shortcode)) {
        // remove enqueued scripts 
        remove_all_actions('wp_enqueue_scripts');
        remove_all_actions('wp_print_footer_scripts');
        remove_all_actions('wp_print_styles');
        
        WPForms::instance()->frontend->assets_css();
    }
    
    
}, 10, 1);

add_filter('cloudpress\customizer\after_render_shortcode_content', function ($content, $shortcode) {
    
    if (mesmerize_shortcode_render_can_apply_wpforms_filters($shortcode)) {
        ob_start();
//        wp_enqueue_scripts();
        wp_print_styles();
//        wp_print_head_scripts();
        $ob_content = ob_get_clean();
        $content    = "<!--header scripts-->\n{$ob_content}<!--header scripts-->\n\n{$content}\n\n";
        
        ob_start();
        wp_print_footer_scripts();
        $ob_content = ob_get_clean();
        $content    .= "<!--footer scripts-->\n{$ob_content}<!--footer scripts-->\n\n";
    }
    
    return $content;
}, 10, 2);
