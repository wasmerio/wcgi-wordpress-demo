<?php

if ( ! defined('ABSPATH')) {
    die('Silence is golden');
}


function mesmerize_get_integration_modules()
{
    $integrationModules = wp_cache_get('mesmerize_integration_modules');
    
    if ( ! $integrationModules) {
        $integrationModules = apply_filters('mesmerize_integration_modules', array());
        wp_cache_set('mesmerize_integration_modules', $integrationModules);
    }
    
    return $integrationModules;
}

function mesmerize_load_integration_modules()
{
    $modules = mesmerize_get_integration_modules();
    
    foreach ($modules as $module) {
        
        $module = wp_normalize_path($module);
        
        if (file_exists("{$module}/integration.php")) {
            require "{$module}/integration.php";
        } else {
            mesmerize_require("{$module}/integration.php");
        }
    }
}

add_action('after_setup_theme', 'mesmerize_load_integration_modules', 2);
