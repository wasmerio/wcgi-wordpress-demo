<?php

require_once get_template_directory() . "/inc/woocommerce/options.php";
require_once get_template_directory() . "/inc/woocommerce/list.php";

add_action('customize_register', function ($wp_customize) {
    
    $panel = 'mesmerize_woocommerce_panel';
    
    $wp_customize->add_panel(
        $panel,
        array(
            'capability' => 'edit_theme_options',
            'title'      => esc_html__('WooCommerce Options', 'mesmerize'),
        )
    );
    
    $priority = 30;
    
    $wp_customize->add_section('mesmerize_woocommerce_product_list', array(
        'title'    => esc_html__('Product List Options', 'mesmerize'),
        'priority' => $priority,
        'panel'    => $panel,
    ));
    
    $wp_customize->add_section('mesmerize_woocommerce_general_options', array(
        'title'    => esc_html__('General Options', 'mesmerize'),
        'priority' => $priority,
        'panel'    => $panel,
    ));
    
    do_action('mesmerize_customize_register_woocommerce_section', $wp_customize, 'mesmerize_woocommerce_panel', $priority);
    
}, 10, 1);


add_filter('loop_shop_per_page', 'new_loop_shop_per_page', 20);

function new_loop_shop_per_page($cols)
{
    // $cols contains the current number of products per page based on the value stored on Options -> Reading
    // Return the number of products you wanna show per page.
    $cols = get_theme_mod('woocommerce_products_per_page', 12);
    
    return absint($cols);
}


// woocommerce_widget_shopping_cart_button_view_cart

// view cart near menu

function mesmerize_woocommerce_cart_menu_item($items, $args = false)
{
    
    
    $isPrimaryMenu = ($args === false || (property_exists($args, 'theme_location') && $args->theme_location === "primary"));
    
    if ( ! $isPrimaryMenu) {
        return $items;
    }
    
    
    $cart_url = wc_get_cart_url();
    
    $cartContent = mesmerize_instantiate_widget("WC_Widget_Cart",
        array(
            'wrap_tag'   => 'div',
            'wrap_class' => 'mesmerize-woo-header-cart',
        )
    );
    
    $cart_id   = wc_get_page_id('cart');
    $cartLabel = get_the_title($cart_id);
    
    $item = "<li class=\"mesmerize-menu-cart\"><a href=\"{$cart_url}\"><span><i class='fa fa-shopping-cart'></i><span class='cart-label'>{$cartLabel}</span></span></a>{$cartContent}</li>";
    
    if (mesmerize_get_from_memory('mesmerize_woocommerce_cart_menu_item_rendered')) {
        $item = "<li class=\"mesmerize-menu-cart-secondary\"><a href=\"{$cart_url}\"><span><i class='fa fa-shopping-cart'></i><span class='cart-label'>{$cartLabel}</span></span></a></li>";
    } else {
        mesmerize_set_in_memory('mesmerize_woocommerce_cart_menu_item_rendered', true);
    }
    
    return $items . $item;
}

add_action('wp_loaded', function () {
    
    $display_near_menu = get_theme_mod('woocommerce_cart_display_near_menu', true);
    
    if (intval($display_near_menu)) {
        add_filter('wp_nav_menu_items', 'mesmerize_woocommerce_cart_menu_item', 10, 2);
        add_filter('mesmerize_nomenu_after', 'mesmerize_woocommerce_cart_menu_item', 10, 2);
    }
});

function mesmerize_get_woo_api_key()
{
    $dummyHash = uniqid('dummy_mesmerize_hash');
    
    return get_theme_mod('mesmerize_woocommerce_api_nonce', md5($dummyHash));
}

function is_mesmerize_woocommerce_api_key_valid($key)
{
    return $key === mesmerize_get_woo_api_key();
}
