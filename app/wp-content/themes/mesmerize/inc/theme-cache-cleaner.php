<?php

add_action('after_setup_theme', function () {
    if (isset($_GET['mesmerize_clear_theme_cache']) && current_user_can('edit_theme_options')) {
        mesmerize_clear_cached_values();
        wp_redirect(site_url());
        exit;
    }
});

add_action('admin_bar_menu', function ($wp_admin_bar) {
    global $wp;
    /** @var WP_Admin_Bar $wp_admin_bar */
    $wp_admin_bar->remove_menu('extendthemes_clear_theme_cache');
    $wp_admin_bar->add_menu(array(
        'id'    => 'mesmerize_clear_theme_cache',
        'title' => sprintf('<span class="ab-icon dashicons-update" style = "line-height: 120%%;" ></span><span >%s </span >', __('Clear theme cache', 'mesmerize')),
        'href'  => add_query_arg('mesmerize_clear_theme_cache', 1, home_url("/")),
        'meta'  => array(
            'tabindex' => -1,
            'target'   => '_blank',
        ),
    ));
}, 72);
