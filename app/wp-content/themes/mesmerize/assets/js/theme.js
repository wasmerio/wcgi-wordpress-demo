(function ($) {
    if ("ontouchstart" in window) {
        document.documentElement.className = document.documentElement.className + " touch-enabled";
    }
    if (navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/i)) {
        document.documentElement.className = document.documentElement.className + " no-parallax";
    }

    // animation frame interval;

    var reqAnimFrame = (function () {
        return window.requestAnimationFrame ||
            window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            window.oRequestAnimationFrame ||
            window.msRequestAnimationFrame || function (callback, element) {
                window.setTimeout(callback, 1000 / 60);
            };
    })();


    window.requestInterval = function (fn, delay, cancelFunction) {
        if (!window.requestAnimationFrame &&
            !window.webkitRequestAnimationFrame &&
            !(window.mozRequestAnimationFrame && window.mozCancelRequestAnimationFrame) && // Firefox 5 ships without cancel support
            !window.oRequestAnimationFrame &&
            !window.msRequestAnimationFrame)
            return window.setInterval(fn, delay);

        var start = new Date().getTime(),
            handle = {};


        function loop() {
            var current = new Date().getTime(),
                delta = current - start;

            if (delta >= delay) {
                fn.call();
                start = new Date().getTime();
            }

            handle.value = reqAnimFrame(loop);

            if (delta >= delay && cancelFunction && cancelFunction.call() === true) {
                clearRequestInterval(handle);
            }
        };

        handle.value = reqAnimFrame(loop);


        return handle;
    };

    window.clearRequestInterval = function (handle) {
        window.cancelAnimationFrame ? window.cancelAnimationFrame(handle.value) :
            window.webkitCancelAnimationFrame ? window.webkitCancelAnimationFrame(handle.value) :
                window.webkitCancelRequestAnimationFrame ? window.webkitCancelRequestAnimationFrame(handle.value) : /* Support for legacy API */
                    window.mozCancelRequestAnimationFrame ? window.mozCancelRequestAnimationFrame(handle.value) :
                        window.oCancelRequestAnimationFrame ? window.oCancelRequestAnimationFrame(handle.value) :
                            window.msCancelRequestAnimationFrame ? window.msCancelRequestAnimationFrame(handle.value) :
                                clearInterval(handle);
    };

    if (!$.event.special.tap) {

        $.event.special.tap = {
            setup: function (data, namespaces) {
                var $elem = $(this);
                $elem.bind('touchstart', $.event.special.tap.handler)
                    .bind('touchmove', $.event.special.tap.handler)
                    .bind('touchend', $.event.special.tap.handler);
            },

            teardown: function (namespaces) {
                var $elem = $(this);
                $elem.unbind('touchstart', $.event.special.tap.handler)
                    .unbind('touchmove', $.event.special.tap.handler)
                    .unbind('touchend', $.event.special.tap.handler);
            },

            handler: function (event) {

                var $elem = $(this);
                $elem.data(event.type, 1);
                if (event.type === 'touchend' && !$elem.data('touchmove')) {
                    event.type = 'tap';
                    $.event.handle.apply(this, arguments);
                } else if ($elem.data('touchend')) {
                    $elem.removeData('touchstart touchmove touchend');
                }
            }
        };

    }

    if (!$.fn.isInView) {
        $.fn.isInView = function (fullyInView) {
            var element = this;
            var pageTop = $(window).scrollTop();
            var pageBottom = pageTop + $(window).height();
            var elementTop = $(element).offset().top;
            var elementBottom = elementTop + $(element).height();

            if (fullyInView === true) {
                return ((pageTop < elementTop) && (pageBottom > elementBottom));
            } else {
                return ((elementTop <= pageBottom) && (elementBottom >= pageTop));
            }
        }
    }


    if (!$.throttle) {
        $.throttle = function (fn, threshhold, scope) {
            threshhold || (threshhold = 250);
            var last,
                deferTimer;
            return function () {
                var context = scope || this;

                var now = +new Date,
                    args = arguments;
                if (last && now < last + threshhold) {
                    // hold on to it
                    clearTimeout(deferTimer);
                    deferTimer = setTimeout(function () {
                        last = now;
                        fn.apply(context, args);
                    }, threshhold);
                } else {
                    last = now;
                    fn.apply(context, args);
                }
            };
        }
    }


    if (!$.debounce) {
        $.debounce = function (func, wait, immediate) {
            var timeout;
            return function () {
                var context = this, args = arguments;
                var later = function () {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        }
    }


})(jQuery);


(function ($) {
    if (!window.mesmerize_smooth_scroll || !window.mesmerize_smooth_scroll.enabled) {
        return;
    }

    var goUp = true;
    var end = null;
    var interval = null;
    var animationInterval = 10; //lower is faster
    var steps = 10; //lower is faster
    var distance = 100;
    var step = 0;

    function handle(delta) {

        delta = Math.sign(delta) * parseInt(Math.min(Math.abs(delta, 10))); // normalize delta
        if (end === null) {
            end = $(window).scrollTop();
        }

        end -= distance * delta;
        goUp = delta > 0;

        $('body').on('mousedown.wheelscroll', 'a', function (event) {
            if (interval) {
                clearRequestInterval(interval);
                interval = null;
                end = null;
                $('body').off('mousedown.wheelscroll');
            }
        });

        function updateScroll() {
            var scrollTop = $(window).scrollTop();
            step = Math.round((end - scrollTop) / steps);
            $(window).scrollTop(scrollTop + step);
        }

        function cancelInterval() {
            var scrollTop = $(window).scrollTop();
            if (
                (scrollTop <= 0 && goUp) || scrollTop >= $(window).prop("scrollHeight") - $(window).height() ||
                (goUp && step > -1) || (!goUp && step < 1)
            ) {

                interval = null;
                end = null;
                return true;
            }
            return false;
        }

        if (interval === null) {
            interval = requestInterval(updateScroll, animationInterval, cancelInterval)
        }
    }

    function wheel(event) {

        var delta = 0;
        if (event.wheelDelta) delta = event.wheelDelta / 120;
        else if (event.detail) delta = -event.detail / 3;

        if (window.scrollY === 0 && delta > 0) { // do nothing when scrolling to top and already there
            return true;
        }

        if (((window.innerHeight + window.scrollY) >= document.body.offsetHeight) && delta < 0) { // do nothing when scrolling to bottom and already there
            return true;
        }

        handle(delta);
        if (event.preventDefault) event.preventDefault();
        event.returnValue = false;
    }

    $(window).on('load', function (event) {
        if (window.addEventListener) window.addEventListener('DOMMouseScroll', wheel, false);
        window.onmousewheel = document.onmousewheel = wheel;
    });

})(jQuery);


mesmerizeDomReady(function ($) {

    if (window.mesmerize_backstretch) {

        window.mesmerize_backstretch.duration = parseInt(window.mesmerize_backstretch.duration);
        window.mesmerize_backstretch.transitionDuration = parseInt(window.mesmerize_backstretch.transitionDuration);

        var images = mesmerize_backstretch.images;

        if (!images) {
            return;
        }

        $('.header-homepage, .header').backstretch(images, mesmerize_backstretch);
    }


    if ($.fn.smoothScrollAnchor) {
        var _$body = $('body');
        $('.header-homepage-arrow-c .header-homepage-arrow').smoothScrollAnchor({
            target: _$body.find('[data-id]').length ? _$body.find('[data-id]').first() : _$body.find('.page-content, .content').eq(0),
            targetSel: _$body.find('[data-id]').length ? '[data-id]:first' : '.page-content, .content',
            offset: function () {
                var $fixed = $('.navigation-bar.fixto-fixed');
                if ($fixed.length) {
                    return $fixed[0].getBoundingClientRect().height;
                }

                return 0;
            }
        });
    }

});


mesmerizeDomReady(function ($) {

    var doneMasonry = false;
    var masonry = $(".post-list.row");

    if (masonry.is('[data-no-masonry]')) {
        return;
    }

    function doPostsMasonry() {

        if (doneMasonry) {
            return;
        }
        doneMasonry = true;


        var images = masonry.find('img');

        function imageLoaded() {
            masonry.data().masonry.layout();
        }

        images.each(function () {
            this.onload = imageLoaded;
            setTimeout(function () {
                masonry.data().masonry.layout();
            }, 500);
        });


        var items = $(".post-list.row .post-list-item");
        var index = items.length - 1;
        items.each(function () {
            $(this).css({
                width: $(this).css('max-width')
            })
        });

        if (masonry.length) {
            masonry.masonry({
                itemSelector: '.post-list-item',
                percentPosition: true,
                columnWidth: '.' + items.eq(index).attr('data-masonry-width')
            });
        }
    }

    if (window.innerWidth >= 768) {
        doPostsMasonry();
    }

    $(window).resize(function () {
        if (window.innerWidth >= 768) {
            doPostsMasonry();
        } else {
            if (masonry.data('masonry')) {
                masonry.masonry('destroy');
            }

        }
    })

});


(function ($) {

    function toggleFooter(footer, pageContent) {
        var footerTop = footer.offset().top + footer.outerHeight();
        var pageBottom = pageContent.offset().top + pageContent.height();

        if (footerTop >= pageBottom) {
            footer.css('visibility', 'visible');
        } else {
            footer.css('visibility', '');
        }
    }

    function updateFooter(footer, pageContent) {
        pageContent.css("margin-bottom", footer.outerHeight() - 1);
        toggleFooter(footer, pageContent);
    }

    window.mesmerizeFooterParalax = function () {

        var footer = $('.footer.paralax');

        if (footer.length) {
            if (footer.parents('.no-parallax').length) {
                footer.css('visibility', 'visible');
                return;
            }

            $('.header-wrapper').css('z-index', 1);

            var pageContent = footer.prev();
            pageContent.addClass('footer-shadow');
            pageContent.css({
                'position': 'relative',
                'z-index': 1
            });

            $(window).bind('resize.footerParalax', function () {
                updateFooter(footer, pageContent);
            });

            mesmerizeDomReady(function () {
                window.setTimeout(function () {
                    updateFooter(footer, pageContent);
                }, 100);
            })

            updateFooter(footer, pageContent);

            $(window).bind('scroll.footerParalax', function () {
                toggleFooter(footer, pageContent);
            });
        }
    }

    window.mesmerizeStopFooterParalax = function () {

        var footer = $('.footer');
        var pageContent = footer.prev();

        $('.header-wrapper').css('z-index', 0);
        pageContent.removeClass('footer-shadow');
        pageContent.css('margin-bottom', '0px');

        $(window).unbind('resize.footerParalax');
        $(window).unbind('scroll.footerParalax');

    }

    mesmerizeFooterParalax();


})(jQuery);

mesmerizeDomReady(function ($) {

    var morphed = $("[data-text-effect]");
    if ($.fn.typed && morphed.length && JSON.parse(mesmerize_morph.header_text_morph)) {
        morphed.each(function () {
            $(this).empty();
            $(this).typed({
                strings: JSON.parse($(this).attr('data-text-effect')),
                typeSpeed: parseInt(mesmerize_morph.header_text_morph_speed),
                loop: true
            });

        });
    }
});

// OffScreen Menu

(function ($) {
    var $menus = $('.offcanvas_menu');
    var $offCanvasWrapper = $('#offcanvas-wrapper');

    if ($offCanvasWrapper.length) {
        $('html').addClass('has-offscreen');
        $offCanvasWrapper.appendTo('body');


        $offCanvasWrapper.on('kube.offcanvas.ready', function () {
            $offCanvasWrapper.removeClass('force-hide');
        });


        $offCanvasWrapper.on('kube.offcanvas.open', function () {
            $('html').addClass('offcanvas-opened');
        });


        $offCanvasWrapper.on('kube.offcanvas.close', function () {
            $('html').removeClass('offcanvas-opened')
        });
    }


    $menus.each(function () {

        var $menu = $(this);

        $menu.on('mesmerize.open-all', function () {
            $(this).find('.menu-item-has-children, .page_item_has_children').each(function () {
                $(this).addClass('open');
                $(this).children('ul').slideDown(100);
            });
        });

        $menu.find('.menu-item-has-children a, .page_item_has_children a').each(function () {
            if ($(this).children('i.fa.arrow').length === 0) {
                $(this).append('<i class="fa arrow"></i>');
            }
        });

        $menu.on('click tap', '.menu-item-has-children > a, .page_item_has_children > a', function (event) {
            var $this = $(this);
            var $li = $this.closest('li');
            event.stopPropagation();

            if ($li.children('ul').length === 0) {
                $('[data-component="offcanvas"]').offcanvas('close');
                return true;
            }

            if ($li.hasClass('open')) {
                if ($this.is('a')) {
                    $('[data-component="offcanvas"]').offcanvas('close');
                    return true;
                }
                $li.children('ul').slideUp(100, function () {
                    $li.find('ul').each(function () {
                        $(this).parent().removeClass('open');
                        $(this).css('display', 'none');
                    })
                });
            } else {
                $li.children('ul').slideDown(100);
            }

            $li.toggleClass('open');

            event.preventDefault();

        });

        $menu.on('click tap', '.mesmerize-menu-cart', function (event) {
            event.stopPropagation();
        });

        $menu.on('click tap', '.menu-item-has-children > a > .arrow, .page_item_has_children  > a > .arrow', function (event) {
            var $this = $(this);
            var $li = $this.closest('li');
            event.stopPropagation();
            event.preventDefault();
            $li.toggleClass('open');

            if ($li.hasClass('open')) {
                $li.children('ul').slideDown(100);
            } else {
                $li.children('ul').slideUp(100);
            }
        });


        $menu.on('click tap', 'a', function (event) {
            var $this = $(this);
            var $li = $this.closest('li');
            var $ul = $li.children('ul');

            if ($ul.length === 0) {
                $('[data-component="offcanvas"]').offcanvas('close');
            }

        });

        if ($.fn.scrollSpy) {
            $menu.find('a').each(function () {
                $(this).data('allow-propagation', true);
            });

            $menu.find('a > i').each(function () {
                $(this).data('allow-propagation', false);
                $(this).data('skip-smooth-scroll', true);
            });

            $menu.find('a').scrollSpy({
                onChange: function () {
                    $menu.find('.current-menu-item,.current_page_item').removeClass('current-menu-item current_page_item');
                    $(this).closest('li').addClass('current-menu-item');
                },
                onLeave: function () {
                    $(this).closest('li').removeClass('current-menu-item current_page_item');
                },
                clickCallback: function () {
                    $('[data-component="offcanvas"]').offcanvas('close');
                },
                smoothScrollAnchor: true,
                offset: function () {
                    if ($('.navigation-bar.fixto-fixed').length) {
                        return $('.navigation-bar.fixto-fixed')[0].getBoundingClientRect().height;
                    }

                    return 0;
                }
            });
        }

    });


    if ($.fn.smoothScrollAnchor) {
        $('#page > .page-content > .content, #page >  .content').find('a[data-cp-link]').filter(function () {
            var $el = $(this);

            if ($el.is('[role=tab]') || $el.parent().is('[role=tab]')) {
                return false;
            }

            return true;

        }).smoothScrollAnchor();


        $('#page > .footer').find('a').filter(function () {
            var $el = $(this);

            if ($el.is('[role=tab]') || $el.parent().is('[role=tab]')) {
                return false;
            }
            return true;

        }).smoothScrollAnchor();
    }
})(jQuery);


(function ($) {


    function updateCounterCircle($el) {
        var $valueHolder = $el.find('[data-countup]');
        var val = $valueHolder.text();
        val = jQuery.map(val.match(/[-]{0,1}[s\d.]*[\d]+/g), function (x) {
            return x
        }).join([]);

        var max = $valueHolder.attr('data-max') !== undefined ? $valueHolder.attr('data-max') : 100;
        var min = $valueHolder.attr('data-min') !== undefined ? $valueHolder.attr('data-min') : 0;

        if (min > max) {
            var aux = max;
            max = min;
            min = aux;
        }

        if (!val) {
            val = min;
        }

        var percentage = val / max * 100;

        var $circle = $el.find('.circle-bar');
        var r = $circle.attr('r');
        var c = Math.PI * (r * 2);

        if (percentage < 0) {
            percentage = 0;
        }
        if (percentage > 100) {
            percentage = 100;
        }
        var pct = c * (100 - percentage) / 100;
        $circle.css({strokeDashoffset: pct});

        $(function () {
            if (!wp || !wp.customize) {
                $circle.parent().height($circle.parent().width());
            }
        });
    }

    function initCounterCircle($circle) {
        updateCounterCircle($circle);


        $circle.find('[data-countup]').bind('countup.update', function () {
            updateCounterCircle($circle);
        });

        // $circle.find('[data-countup]').bind('DOMSubtreeModified', updateCircleOnDOMSubtreeModified);

        $circle.data('doCircle', function () {
            updateCounterCircle($circle);
        });
    }

    function initCountUP($self, force) {
        var min = $self.attr('data-min') !== undefined ? $self.attr('data-min') : 0,
            stopAt = $self.attr('data-stop'),
            max = $self.attr('data-max') !== undefined ? $self.attr('data-max') : 100,
            prefix = $self.attr('data-prefix') || "",
            suffix = $self.attr('data-suffix') || "",
            duration = $self.attr('data-duration') || 2000,
            decimals = $self.attr('data-decimals') || 0;

        if (stopAt !== undefined) {
            max = stopAt;
        }

        var formattedMax = '';

        try {
            var counter = new CountUp($self[0], parseInt(min), parseInt(max), parseInt(decimals), parseInt(duration) / 1000, {
                prefix: prefix,
                suffix: suffix,
                onUpdate: function (value) {
                    //$self.trigger('countup.update', [value]);
                    initCounterCircle($self.closest('.circle-counter'));
                }
            });

            formattedMax = counter.options.formattingFn(parseInt(max));
        } catch (e) {
            console.error('invalid countup args', {
                min: min,
                max: max,
                decimals: decimals,
                duration: duration,
                suffix: suffix,
                prefix: prefix
            });
        }

        $self.data('countup', counter);


        $self.attr('data-max-computed', formattedMax);

        if (force) {
            $self.data('countup').reset();
        }

        if ($self.isInView(true) || force) {
            $self.data('countup').start();
        }

        $self.data('restartCountUp', function () {
            initCountUP($self);
        })
    }


    $('.circle-counter').each(function () {
        initCounterCircle($(this));
    });

    var $countUPs = $('[data-countup]');

    $countUPs.each(function () {
        var $self = $(this);
        initCountUP($self);
    });

    $(window).on('scroll', function () {
        $countUPs.each(function () {
            var $self = $(this);

            if ($self.isInView(true) && !$self.data('one')) {
                $self.data('countup').start();
                $self.data('one', true);
            } else {
                // $self.data('countup').reset();
            }

        });
    });

    $(function () {
        if (!wp || !wp.customize) {
            $(window).on('resize', function () {
                $('.circle-counter .circle-svg').each(function () {
                    $(this).height($(this).width());
                });

            });
        }
    });


    // customizer binding
    try {
        if (parent.CP_Customizer) {
            parent.CP_Customizer.addModule(function (CP_Customizer) {
                CP_Customizer.hooks.addAction('after_node_insert', function ($node) {
                    if ($node.is('[data-countup]')) {

                        if ($node.closest('.circle-counter').length) {
                            initCounterCircle($node.closest('.circle-counter'));
                        }

                        initCountUP($node, true);
                    }

                    $node.find('[data-countup]').each(function () {

                        if ($(this).closest('.circle-counter').length) {
                            initCounterCircle($(this).closest('.circle-counter'));
                        }

                        initCountUP($(this), true);
                    })
                });
            });
        }

    } catch (e) {

    }

})(jQuery);
