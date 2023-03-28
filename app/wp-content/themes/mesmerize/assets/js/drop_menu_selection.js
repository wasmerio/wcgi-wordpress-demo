/* global jQuery */

(function ($) {

    // tap event
	 if (!$.event.special.tap) {
		$.event.special.tap = {
		  setup: function(data, namespaces) {
			var $elem = $(this);
			$elem
			  .bind('touchstart', $.event.special.tap.handler)
			  .bind('touchmove', $.event.special.tap.handler)
			  .bind('touchend', $.event.special.tap.handler);
		  },

		  teardown: function(namespaces) {
			var $elem = $(this);
			$elem
			  .unbind('touchstart', $.event.special.tap.handler)
			  .unbind('touchmove', $.event.special.tap.handler)
			  .unbind('touchend', $.event.special.tap.handler);
		  },

		  handler: function (event) {
			var $elem = $(this),
			  handleObj = event.handleObj,
			  result;
			$elem.data(event.type, 1);
			if (event.type === 'touchend' && !$elem.data('touchmove')) {
			  event.type = 'tap';
			  result = handleObj.handler.call(this, event);
			} else if ($elem.data('touchend')) {
			  $elem.removeData('touchstart touchmove touchend');
			}

			return result;
		  },
		};
	  }



    function deselectItems($menu) {
        $menu.find('[data-selected-item]').each(function () {
            var $item = $(this);
            $item.removeAttr('data-selected-item');
            var $submenu = $menu.children('ul');
            if ($menu.is('.mobile-menu')) {
                $submenu.slideDown();
            }
        });

    }

    function clearSelectionWhenTapOutside($this, $menu) {
        $('body').off('tap.navigation-clear-selection');
        $(window).off('scroll.navigation-clear-selection');
        if ($this.is($menu) || $.contains($menu[0], this)) {
            return;
        }
        clearMenuHovers($menu);
    }

    function selectItem($menu, $item) {
        deselectItems($menu);
        $item.attr('data-selected-item', true);
        clearMenuHovers($menu, $item);
        $item.addClass('hover');
        setOpenReverseClass($menu, $item);
        $('body').on('tap.navigation-clear-selection', '*', function () {
            var $this = jQuery(this);
            clearSelectionWhenTapOutside($this, $menu);
        });

        $(window).on('scroll.navigation-clear-selection', function () {
            var $this = jQuery(this);
            clearSelectionWhenTapOutside($this, $menu);
        });
    }

    function isSelectedItem($item) {
        return $item.is('[data-selected-item]');
    }


    function containsSelectedItem($item) {
        return (($item.find('[data-selected-item]').length > 0) || $item.is('[data-selected-item]'));
    }

    function clearMenuHovers($menu, except) {

        $menu.find('li.hover').each(function () {
            if (except && containsSelectedItem($(this))) {
                return;
            }
            $(this).removeClass('hover');
        });

    }

    function getItemLevel($menu, $item) {
        return $item.parentsUntil('ul.dorpdown-menu').filter('li').length;
    }

    function setOpenReverseClass($menu, $item) {
        // level 0 - not in dropdown
        if (getItemLevel($menu, $item) > 0) {
            var $submenu = $item.children('ul');
            var subItemDoesNotFit = ($submenu.length && ($item.offset().left + $item.width() + 300) > window.innerWidth);
            var parentsAreReversed = ($submenu.length && $item.closest('.open-reverse').length);

            if (subItemDoesNotFit || parentsAreReversed) {
                $submenu.addClass('open-reverse');
            } else {
                if ($submenu.length) {
                    $submenu.removeClass('open-reverse');
                }
            }
        }
    }

    $('ul.dropdown-menu').each(function () {
        var $menu = $(this);

        if ($menu.hasClass('mobile-menu')) {
            var $mobileToggler = $('<a href="#" data-menu-toggler="">Menu</a>');
            $menu.before($mobileToggler);

            // mobile
            $mobileToggler.click(function () {

                if ($mobileToggler.hasClass('opened')) {

                    $menu.slideUp(300, function () {
                        $menu.css('display', '');
                    });
                    $menu.removeClass('mobile-menu');
                    $mobileToggler.removeClass('opened');

                }
                else {

                    $mobileToggler.addClass('opened');
                    $menu.slideDown();
                    $menu.addClass('mobile-menu');

                    clearMenuHovers($menu);
                    deselectItems($menu);
                }

            });
        }

        var $currentSelectedItem = $("");
        // on tablet
        $menu.on('tap.navigation', 'li.menu-item > a, li.page_item > a', function (event) {
            var $link = $(this);
            var $item = $link.parent();
            var $submenu = $item.children('ul');

            if ($submenu.length) {

                if (isSelectedItem($item)) {
                    var href = $link.attr('href');

                    // do nothing if nothing
                    if (href.indexOf('#') === 0) {
                        var anchor = href.replace('#', '').trim();

                        if (!anchor || !$('#' + anchor).length) {
                            return;
                        }
                    }content: "\f0da"
                    deselectItems($menu);
                } else {
                    selectItem($menu, $item);
                    event.preventDefault();
                    event.stopPropagation();
                }

            } else {
                event.stopPropagation();
                deselectItems($menu);
            }

        });


        $menu.on('mouseover.navigation', 'li', function () {
            $menu.find('li.hover').removeClass('hover');
            setOpenReverseClass($menu, $(this));
        });

        addMenuScrollSpy($menu);
    });

    function addMenuScrollSpy(startFrom) {

        var $menu = startFrom;

        if ($.fn.scrollSpy) {
            $menu.find('a').scrollSpy({
                onChange: function () {
                    $menu.find('.current-menu-item,.current_page_item').removeClass('current-menu-item current_page_item');
                    $(this).closest('li').addClass('current-menu-item');
                },
                onLeave: function () {
                    $(this).closest('li').removeClass('current-menu-item current_page_item');
                },
                smoothScrollAnchor: true,
                offset: function () {
                    var $fixed = $('.navigation-bar.fixto-fixed');
                    if ($fixed.length) {
                        return $fixed[0].getBoundingClientRect().height;
                    }

                    return 0;
                }
            });
        }

        $(window).trigger('smoothscroll.update');
    }

    $(function () {

        if (window.wp && window.wp.customize) {
            jQuery('.offcanvas_menu').find('li > ul').eq(0).each(function () {
                jQuery(this).show();
                jQuery(this).parent().addClass('open')
            });

            window.wp.customize.selectiveRefresh.bind('render-partials-response', function (response) {
                var menuKeys = Object.getOwnPropertyNames(response.contents).filter(function (key) {
                    return key.indexOf('nav_menu_instance[') !== -1;
                });

                if (menuKeys.length) {

                    setTimeout(function () {
                        $('ul.dropdown-menu').each(function () {
                            addMenuScrollSpy($(this));
                        });
                    }, 1000);

                }
            });
        }

    });

})(jQuery);
