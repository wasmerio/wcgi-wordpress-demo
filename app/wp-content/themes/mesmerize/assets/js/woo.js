(function ($) {

    var addCloseCartBind = function ($cart, $cart_button, $menu) {
        $('body').on('mouseover.ope-woo', function (event) {
            var $target = $(event.target);
            var related = isHeaderCartRelated($target, $cart, $cart_button) || $target.is($menu);
            if (!related) {
                $('body').off('mouseover.ope-woo');
                $cart.fadeOut();
            }
        });
    };

    jQuery(document).ready(function () {
        var $menu = jQuery('#main_menu');
        var $cart_button = $menu.find('li.mesmerize-menu-cart');
        var $cart = jQuery('.mesmerize-woo-header-cart');
        positionateWooCartItem($menu, $cart_button, $cart);
        addCloseCartButton($cart);

        $cart_button.children('a').on('touchstart', function (e) {
            'use strict';
            if (!$cart.is(':visible')) {
                e.preventDefault();
                showCart($cart, $cart_button, 'absolute');
            }
            else {
                window.location = $(this).attr('href');
            }
        });

    });

    $('.woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image:eq(0) .wp-post-image').on('load', function () {
        var $image = $(this);

        if ($image) {
            setTimeout(function () {
                var setHeight = $image.closest('.woocommerce-product-gallery__image').height();
                var $viewport = $image.closest('.flex-viewport');

                if (setHeight && $viewport) {
                    $viewport.height(setHeight);
                }
            }, 500);
        }
    }).each(function () {
        if (this.complete) {
            $(this).load();
        }
    });


    function addCloseCartButton($cart) {

        $cart.prepend('<a href="#" class="close-mini-cart small"><i class="fa fa-close"></i></a>');

        $('.close-mini-cart').click(function () {
            $('body').off('mouseover.ope-woo');
            $cart.fadeOut();
        });

    }

    function positionateWooCartItem($menu, $cart_button, $cart) {
        $menu.parent().append($cart);
        var $menuItems = $menu.find('li').not($cart_button);

        $cart_button.children('a').off().on('mouseover', function (event) {

            if ($cart.children().length === 0) {
                return;
            }

            $menuItems.trigger('mouseleave');

            addCloseCartBind($cart, $cart_button, $menu);
            showCart($cart, $cart_button);

        });

    }

    function showCart($cart, $cart_button) {


        if ($('body').is('.woocommerce-cart') || $('body').is('.woocommerce-checkout')) {
            return;
        }

        var top = $cart_button.offset().top + $cart_button.outerHeight() - $cart_button.closest('div').offset().top;
        var position = /*$menu.closest('[data-sticky]') ? "fixed" :*/ "absolute";

        if ($cart_button.offset().left < $cart.outerWidth()) {
            var leftPosition = $cart_button.offset().left + $cart.outerWidth() + 12;
        }
        else {
            var leftPosition = $cart_button.offset().left + $cart_button.width() + 5;
        }

        $cart.css({
            'position': position,
            'z-index': '100000',
            'top': top,
            'left': leftPosition,
        });
        $cart.fadeIn();

    }

    function isHeaderCartRelated($target, $cart, $cart_button) {

        var isMenuButtoRelated = $.contains($cart_button[0], $target[0]) || $target.is($cart_button);
        var isCartContentRelated = $.contains($cart[0], $target[0]) || $target.is($cart);

        return (isMenuButtoRelated || isCartContentRelated);
    }


})(jQuery);
