(function ($) {


    if (window.location.hash === '#page-top') {
        changeUrlHash("", 5);
    }

    var __toCheckOnScroll = {
        items: {},
        eachCategory: function (callback) {
            for (var id in this.items) {

                if (!this.items.hasOwnProperty(id)) {
                    continue;
                }

                callback(this.items[id]);
            }
        },
        addItem: function (id, item) {
            if (!this.items[id]) {
                this.items[id] = [];
            }

            this.items[id].push(item);
        },

        all: function () {
            var result = [];

            for (var id in this.items) {

                if (!this.items.hasOwnProperty(id)) {
                    continue;
                }

                result = result.concat(this.items[id]);
            }

            return result;
        }
    };
    var __alreadyScrolling = false;


    function isAboveTheScreeMiddle(element) {
        var breakPoint = window.innerHeight * 0.5;
        var elPosition = {
            top: $(element)[0].getBoundingClientRect().top,
            bottom: $(element)[0].getBoundingClientRect().bottom
        };

        if (elPosition.top >= 0 && elPosition.top < breakPoint) {
            return true;
        } else {
            if (top < 0 && elPosition.bottom > 0) {
                return true;
            }

        }

        return false;
    }


    function isInView(element, fullyInView) {
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


    function getScrollToValue(elData) {
        var offset = (!isNaN(parseFloat(elData.options.offset))) ? elData.options.offset : elData.options.offset.call(elData.target);
        var scrollToValue = elData.target.offset().top - offset - $('body').offset().top;

        return scrollToValue;
    }


    function changeUrlHash(hash, timeout) {

        if (hash === location.hash.replace('#', '') || (hash === 'page-top' && '' === location.hash.replace('#', ''))) {
            return;
        }

        setTimeout(function () {
            if (hash) {
                if (hash === 'page-top') {
                    hash = " ";
                } else {
                    hash = "#" + hash;
                }
            } else {
                hash = " ";
            }
            if (history && history.replaceState) {
                history.replaceState({}, "", hash);
            } else {
            }
        }, timeout || 100);
        /* safari issue fixed by throtteling the event */
    }

    function scrollItem(elData) {
        if (__alreadyScrolling) {
            return;
        }

        __alreadyScrolling = true;
        var scrollToValue = getScrollToValue(elData);

        $('html, body').animate({scrollTop: scrollToValue}, {
                easing: 'linear',
                complete: function () {
                    // check for any updates
                    var scrollToValue = getScrollToValue(elData);
                    $('html, body').animate({scrollTop: scrollToValue}, {
                            easing: 'linear',
                            duration: 100,
                            complete: function () {
                                __alreadyScrolling = false;
                                changeUrlHash(elData.id, 5);
                            }
                        }
                    )
                }
            }
        );
    }

    function getPageBaseUrl() {
        return [location.protocol, '//', location.host, location.pathname].join('');
    }

    function fallbackUrlParse(url) {
        return url.split('?')[0].split('#')[0];
    }

    function getABaseUrl(element) {
        var href = jQuery(element)[0].href || "";
        var url = "#";

        try {

            var _url = new window.URL(href);
            url = [_url.protocol, '//', _url.host, _url.pathname].join('');

        } catch (e) {
            url = fallbackUrlParse(href);
        }

        return url;
    }

    function getTargetForEl(element) {
        var targetId = (element.attr('href') || "").split('#').pop(),
            hrefBase = getABaseUrl(element),
            target = null,
            pageURL = getPageBaseUrl();


        if (hrefBase.length && hrefBase !== pageURL) {
            return target;
        }

        if (targetId.trim().length) {
            try {
                target = $('[id="' + targetId + '"]');
            } catch (e) {
                console.log('error scrollSpy', e);
            }
        }

        if (target && target.length) {
            return target;
        }

        return null;
    }

    $.fn.smoothScrollAnchor = function (options) {
        var elements = $(this);

        options = jQuery.extend({
            offset: 0
        }, options);

        elements.each(function () {
            var element = $(this);

            var target = options.target || getTargetForEl(element);
            if (target && target.length) {

                var elData = {
                    element: element,
                    options: options,
                    target: target,
                    targetSel: options.targetSel || '[id="' + target.attr('id').trim() + '"]',
                    id: (target.attr('id') || '').trim()
                };

                element.off('click tap').on('click tap', function (event) {
                    
                    if ($(this).data('skip-smooth-scroll')) {
                        return;
                    }

                    event.preventDefault();

                    if (!$(this).data('allow-propagation')) {
                        event.stopPropagation();
                    }

                    scrollItem(elData);

                    if (elData.clickCallback) {
                        elData.clickCallback.call(this, event);
                    }
                });
            }
        });
    };

    $.fn.scrollSpy = function (options) {
        var elements = $(this);
        var id = 'spy-' + parseInt(Date.now() * Math.random());

        elements.each(function () {
            var element = $(this);
            options = jQuery.extend({
                onChange: function () {
                },
                onLeave: function () {
                },
                clickCallback: function () {
                },
                smoothScrollAnchor: false,
                offset: 0
            }, options);

            if (element.is('a') && (element.attr('href') || "").indexOf('#') !== -1) {

                var target = getTargetForEl(element);

                if (target) {
                    var elData = {
                        element: element,
                        options: options,
                        target: target,
                        targetSel: '[id="' + target.attr('id').trim() + '"]',
                        id: target.attr('id').trim()

                    };
                    __toCheckOnScroll.addItem(id, elData);
                    element.data('scrollSpy', elData);

                    if (options.smoothScrollAnchor) {
                        element.smoothScrollAnchor({
                            offset: options.offset
                        });
                    }
                }
            }
        })
    };


    function update() {
        __toCheckOnScroll.eachCategory(function (items) {
            var ordered = items.sort(function (itemA, itemB) {
                return itemA.target.offset().top - itemB.target.offset().top;
            });
            var lastItem = ordered.filter(function (item) {
                return item.target.offset().top <= window.scrollY + window.innerHeight * 0.25;
            }).pop();

            ordered.forEach((function (item) {
                if (lastItem && item.element.is(lastItem.element)) {
                    changeUrlHash(item.id, 5);
                    item.options.onChange.call(item.element);
                } else {
                    item.options.onLeave.call(item.element);
                }
            }));
        });
    }

    $(window).scroll(update);

    $(window).bind('smoothscroll.update', update);

    $(function () {
        var hash = window.location.hash.replace('#', '');
        var currentItem = __toCheckOnScroll.all().filter(function (item) {
            return item.targetSel === '[id="' + hash.trim() + '"]';
        });


        $(window).on('load', function () {
            if (currentItem.length) {
                scrollItem(currentItem[0]);
            }
            update();
        });
    });

})(jQuery);


