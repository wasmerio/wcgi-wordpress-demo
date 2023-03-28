<?php


mesmerize_add_kirki_field(array(
    'type'     => 'sectionseparator',
    'label'    => esc_html__('Shop Page Settings', 'mesmerize'),
    'settings' => "woocommerce_shop_page_separator_options",
    'section'  => 'mesmerize_woocommerce_product_list',
    'priority' => '1',

));

add_action('customize_register', 'mesmerize_add_shop_page_setting_options');

function mesmerize_add_shop_page_setting_options()
{
    do_action('mesmerize_customizer_prepend_woocommerce_list_options', 'mesmerize_woocommerce_product_list');
}

mesmerize_add_kirki_field(array(
    'type'     => 'number',
    'settings' => "woocommerce_products_per_page",
    'label'    => esc_html__('Products per page', 'mesmerize'),
    'section'  => 'mesmerize_woocommerce_product_list',
    'default'  => 12,
    'priority' => '10',
));

mesmerize_add_kirki_field(array(
    'type'     => 'number',
    'settings' => "woocommerce_list_item_desktop_cols",
    'label'    => esc_html__('Products per row on desktop', 'mesmerize'),
    'section'  => 'mesmerize_woocommerce_product_list',
    'default'  => 4,
    'priority' => '10',
));


mesmerize_add_kirki_field(array(
    'type'     => 'number',
    'settings' => "woocommerce_list_item_tablet_cols",
    'label'    => esc_html__('Products per row on tablet', 'mesmerize'),
    'section'  => 'mesmerize_woocommerce_product_list',
    'default'  => 2,
    'priority' => '10',
));


mesmerize_add_kirki_field(array(
    'type'     => 'sectionseparator',
    'label'    => esc_html__('Related products Settings', 'mesmerize'),
    'settings' => "woocommerce_related_products_separator_options",
    'section'  => 'mesmerize_woocommerce_product_list',
    'priority' => '21',

));

mesmerize_add_kirki_field(array(
    'type'      => 'number',
    'settings'  => "woocommerce_related_list_item_desktop_cols",
    'label'     => esc_html__('Related products per row on desktop', 'mesmerize'),
    'section'   => 'mesmerize_woocommerce_product_list',
    'default'   => 4,
    'priority'  => '30',
));


mesmerize_add_kirki_field(array(
    'type'     => 'number',
    'settings' => "woocommerce_related_list_item_tablet_cols",
    'label'    => esc_html__('Related products per row on tablet', 'mesmerize'),
    'section'  => 'mesmerize_woocommerce_product_list',
    'default'  => 2,
    'priority' => '30',
));

mesmerize_add_kirki_field(array(
    'type'     => 'sectionseparator',
    'label'    => esc_html__('Upsell products Settings', 'mesmerize'),
    'settings' => "woocommerce_up_sell_products_separator_options",
    'section'  => 'mesmerize_woocommerce_product_list',
    'priority' => '41',

));

mesmerize_add_kirki_field(array(
    'type'     => 'ope-info',
    'label'    => esc_html__('The upsell product list appears in the product page, before the related products list', 'mesmerize'),
    'section'  => 'mesmerize_woocommerce_product_list',
    'settings' => "woocommerce_upsells_list_item_desktop_cols_info",
    'priority' => '50',
));

mesmerize_add_kirki_field(array(
    'type'     => 'number',
    'settings' => "woocommerce_upsells_list_item_desktop_cols",
    'label'    => esc_html__('Upsell products per row on desktop', 'mesmerize'),
    'section'  => 'mesmerize_woocommerce_product_list',
    'default'  => 4,
    'priority' => '50',
));

mesmerize_add_kirki_field(array(
    'type'     => 'number',
    'settings' => "woocommerce_upsells_list_item_tablet_cols",
    'label'    => esc_html__('Upsell products per row on tablet', 'mesmerize'),
    'section'  => 'mesmerize_woocommerce_product_list',
    'default'  => 2,
    'priority' => '50',
));

mesmerize_add_kirki_field(array(
    'type'     => 'sectionseparator',
    'label'    => esc_html__('Cross-sell products Settings', 'mesmerize'),
    'settings' => "woocommerce_cross_sell_products_separator_options",
    'section'  => 'mesmerize_woocommerce_product_list',
    'priority' => '61',

));


mesmerize_add_kirki_field(array(
    'type'     => 'ope-info',
    'label'    => esc_html__('The cross-sell product list appears in the shopping cart page', 'mesmerize'),
    'section'  => 'mesmerize_woocommerce_product_list',
    'settings' => "woocommerce_cross_sell_list_item_desktop_cols_info",
    'priority' => '70',
));


mesmerize_add_kirki_field(array(
    'type'     => 'number',
    'settings' => "woocommerce_cross_sells_product_no",
    'label'    => esc_html__('Number of cross-sell products to display', 'mesmerize'),
    'section'  => 'mesmerize_woocommerce_product_list',
    'default'  => 2,
    'priority' => '70',
));

mesmerize_add_kirki_field(array(
    'type'     => 'number',
    'settings' => "woocommerce_cross_sell_list_item_desktop_cols",
    'label'    => esc_html__('Cross-sell products per row on desktop', 'mesmerize'),
    'section'  => 'mesmerize_woocommerce_product_list',
    'default'  => 2,
    'priority' => '70',
));


mesmerize_add_kirki_field(array(
    'type'     => 'number',
    'settings' => "woocommerce_cross_sell_list_item_tablet_cols",
    'label'    => esc_html__('Cross-sell products per row on tablet', 'mesmerize'),
    'section'  => 'mesmerize_woocommerce_product_list',
    'default'  => 2,
    'priority' => '70',
));


add_filter('cloudpress\customizer\global_data', function ($data) {

    $key = wp_create_nonce('mesmerize_woocommerce_api_nonce');
    set_theme_mod('mesmerize_woocommerce_api_nonce', $key);

    if ( ! isset($_REQUEST['mesmerize_woocommerce_api_nonce'])) {
        $data['mesmerize_woocommerce_api_nonce'] = $key;
    }

    return $data;
});

mesmerize_add_kirki_field(array(
    'type'     => 'sectionseparator',
    'label'    => esc_html__('Shop Header Settings', 'mesmerize'),
    'settings' => "woocommerce_shop_header_separator_options",
    'section'  => 'mesmerize_woocommerce_general_options',

));

mesmerize_add_kirki_field(array(
    'type'     => 'checkbox',
    'settings' => 'woocommerce_cart_display_near_menu',
    'label'    => esc_html__('Show cart button in menu', 'mesmerize'),
    'section'  => 'mesmerize_woocommerce_general_options',
    'default'  => true,
));


mesmerize_add_kirki_field(array(
    'type'     => 'select',
    'settings' => 'woocommerce_header_type',
    'label'    => esc_html__('Shop header', 'mesmerize'),
    'section'  => 'mesmerize_woocommerce_general_options',
    'default'  => 'default',
    'choices'  => apply_filters('mesmerize_woocommerce_shop_header_type_choices', array(
        "default" => esc_html__("Large header with title", "mesmerize"),
        "small"   => esc_html__("Navigation only", "mesmerize"),
    )),
));


mesmerize_add_kirki_field(array(
    'type'     => 'select',
    'settings' => 'woocommerce_product_header_type',
    'label'    => esc_html__('Product detail header', 'mesmerize'),
    'section'  => 'mesmerize_woocommerce_general_options',
    'default'  => 'default',
    'choices'  => apply_filters('mesmerize_woocommerce_shop_header_type_choices', array(
        "default" => esc_html__("Large header with title", "mesmerize"),
        "small"   => esc_html__("Navigation only", "mesmerize"),
    )),
));


mesmerize_add_kirki_field(array(
    'type'            => 'checkbox',
    'settings'        => 'woocommerce_product_header_image',
    'label'           => esc_html__('On product page set product image as header background', 'mesmerize'),
    'section'         => 'mesmerize_woocommerce_general_options',
    'default'         => true,
    'active_callback' => array(
        array(
            'setting'  => 'woocommerce_product_header_type',
            'operator' => '!=',
            'value'    => 'small',
        ),
    ),
));


add_filter("mesmerize_inner_header_background_type", function ($type) {
    if (mesmerize_is_woocommerce_product_page() && get_theme_mod("woocommerce_product_header_image", true)) {
        return "image";
    }

    return $type;
}, 1);
