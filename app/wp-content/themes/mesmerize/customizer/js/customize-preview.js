function liveUpdate(setting, callback) {
    var cb = function (value) {
        value.bind(callback);
    };
    var _setting = setting;
    wp.customize(_setting, cb);

    if (parent.CP_Customizer) {
        var _prefixedSetting = parent.CP_Customizer.slugPrefix() + "_" + setting;
        wp.customize(_prefixedSetting, cb);
    }
}

(function ($) {

    function toggleTopBarIcon(area, index, selector) {
        return function (value) {
            value.bind(function (newval) {
                if (newval) {
                    $('.header-top-bar .header-top-bar-area.' + area + ' ' + selector).eq(index).removeAttr('data-reiki-hidden');
                } else {
                    $('.header-top-bar .header-top-bar-area.' + area + ' ' + selector).eq(index).attr('data-reiki-hidden', 'true');
                }
            });
        }
    }

    function changeTopBarIcon(area, index, selector) {
        return function (value) {
            value.bind(function (newval, oldval) {
                $('.header-top-bar .header-top-bar-area.' + area + ' ' + selector).eq(index).find('i').removeClass(oldval).addClass(newval);
            });
        }
    }

    function updateTopBarInfo(area, index) {
        return function (value) {
            value.bind(function (html) {
                var id = 'header_top_bar_' + area + '_info_field_' + index + '_icon';
                $("[data-focus-control=" + id + "]").find('span').html(html);
            });
        }
    }

    var headerPanels = ['header', 'inner_header'];

    /* START TOP BAR */

    var topBarAreas = ['area-left', 'area-right'];

    $.each(topBarAreas, function (index, area) {

        for (var i = 0; i < 5; i++) {
            wp.customize('header_top_bar_' + area + '_social_icon_' + i + '_enabled', toggleTopBarIcon(area, i, '.social-icon'));
            wp.customize('header_top_bar_' + area + '_social_icon_' + i + '_icon', changeTopBarIcon(area, i, '.social-icon'));
        }

        for (var j = 0; j < 3; j++) {
            wp.customize('header_top_bar_' + area + '_info_field_' + j + '_enabled', toggleTopBarIcon(area, j, '.top-bar-field'));
            wp.customize('header_top_bar_' + area + '_info_field_' + j + '_icon', changeTopBarIcon(area, j, '.top-bar-field'));
            wp.customize('header_top_bar_' + area + '_info_field_' + j + '_text', updateTopBarInfo(area, j));
        }

    });

    /* END TOP BAR */

    /* START NAVIGATION */

    var navigationSelectors = {
        'header': '.mesmerize-front-page',
        'inner_header': '.mesmerize-inner-page'
    };

    $.each(headerPanels, function (index, panel) {

        wp.customize(panel + '_nav_sticked', function (value) {

            value.bind(function (newval) {

                var $navBar = $([navigationSelectors[panel], ".navigation-bar"].join(' '));

                if ($navBar.length) {

                    if (newval) {
                        $navBar.attr({
                            "data-sticky": 0,
                            "data-sticky-mobile": 1,
                            "data-sticky-to": "top"
                        });

                        if ($navBar.data().fixtoInstance) {
                            $navBar.data().fixtoInstance.start();
                        } else {
                            mesmerizeMenuSticky();
                        }
                    } else {
                        $navBar.removeAttr('data-sticky');
                        $navBar.removeAttr('data-sticky-mobile');
                        $navBar.removeAttr('data-sticky-to');

                        if ($navBar.data().fixtoInstance) {
                            $navBar.data().fixtoInstance.stop();
                        }
                    }

                }

            });

        });

        wp.customize(panel + '_nav_boxed', function (value) {
            value.bind(function (newval) {
                if (newval) {
                    $(navigationSelectors[panel] + ' .header-top-bar').addClass('no-padding').children().addClass('gridContainer');
                    $(navigationSelectors[panel] + ' .navigation-bar').addClass('boxed').find('.navigation-wrapper').addClass('gridContainer');
                } else {
                    $(navigationSelectors[panel] + ' .header-top-bar').removeClass('no-padding').children().removeClass('gridContainer');
                    $(navigationSelectors[panel] + ' .navigation-bar').removeClass('boxed').find('.navigation-wrapper').removeClass('gridContainer');
                }
            });
        });

        wp.customize(panel + '_nav_border', function (value) {
            value.bind(function (newval) {
                if (newval) {
                    $(navigationSelectors[panel] + ' .navigation-bar').addClass('bordered');
                } else {
                    $(navigationSelectors[panel] + ' .navigation-bar').removeClass('bordered');
                }
                $(navigationSelectors[panel] + ' .navigation-bar').css({
                    'border-bottom-color': (newval ? wp.customize(panel + '_nav_border_color').get() : ''),
                    'border-bottom-width': (newval ? wp.customize(panel + '_nav_border_thickness').get() + 'px' : '0px'),
                    'border-bottom-style': (newval ? 'solid' : '')
                });
            });
        });

        wp.customize(panel + '_nav_border_color', function (value) {
            value.bind(function (newval) {
                $(navigationSelectors[panel] + ' .navigation-bar').css('border-bottom-color', newval);
            });
        });

        wp.customize(panel + '_nav_border_thickness', function (value) {
            value.bind(function (newval) {
                $(navigationSelectors[panel] + ' .navigation-bar').css({
                    'border-bottom-width': newval + 'px',
                    'border-bottom-style': 'solid'
                });
            });
        });

        wp.customize(panel + '_nav_transparent', function (value) {
            value.bind(function (newval) {
                if (newval) {
                    $(navigationSelectors[panel] + ' .navigation-bar').removeClass('coloured-nav');
                } else {
                    $(navigationSelectors[panel] + ' .navigation-bar').css('background-color', '');
                    $(navigationSelectors[panel] + ' .navigation-bar').addClass('coloured-nav');
                }
            });
        });

    });

    wp.customize('header_offscreen_nav_on_tablet', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('body').addClass('offcanvas_menu-tablet');
            } else {
                $('body').removeClass('offcanvas_menu-tablet');
            }
        });
    });

    wp.customize('header_offscreen_nav_on_desktop', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('body').addClass('offcanvas_menu-desktop');
            } else {
                $('body').removeClass('offcanvas_menu-desktop');
            }
        });
    });


    /* END NAVIGATION */

    /* START HEADER BACKGROUND */

    function updateMobileBgImagePosition() {
        var position = wp.customize('header_bg_position_mobile').get(),
            positionParts = position.split(' '),
            offset = wp.customize('header_bg_position_mobile_offset').get(),
            styleHolder = jQuery('[data-name="custom-mobile-image-position"]');

        if (styleHolder.length == 0) {
            styleHolder = jQuery('<style data-name="custom-mobile-image-position"></style>');
            styleHolder.appendTo('head');
        }

        position = position + " " + offset + "px";

        styleHolder.text("" +
            "@media screen and (max-width: 767px) {\n" +
            "   .header-homepage {\n" +
            "       background-position: " + position + ";\n" +
            "   }\n" +
            "}\n");
    }


    wp.customize('header_front_page_image', function (value) {
        value.bind(function (newval) {
            $('.header-homepage').css('background-image', 'url(' + newval + ')');
        });
    });

    wp.customize('header_image', function (value) {
        var randomOptions = ['random-default-image', 'random-uploaded-image'];

        value.bind(function (newval, oldValue) {
            if (randomOptions.indexOf(newval) !== -1) {
                if (newval !== oldValue) { // trigger a refresh for random images
                    parent.wp.customize.previewer.refresh();
                }
            } else {
                $('.mesmerize-inner-page .header').css('background-image', 'url(' + newval + ')');
            }
        });
    });

    $.each(headerPanels, function (index, panel) {

        wp.customize(panel + '_parallax', function (value) {
            value.bind(function (newval) {
                if (newval) {
                    $(navigationSelectors[panel] + ' .header-wrapper > div').attr('data-parallax-depth', "20");
                } else {
                    $(navigationSelectors[panel] + ' .header-wrapper > div').removeAttr('data-parallax-depth');
                }
            });
        });

        wp.customize(panel + '_slideshow_duration', function (value) {
            value.bind(function (newval) {
                if (jQuery(".header-wrapper > div").data().backstretch) {
                    jQuery(".header-wrapper > div").data().backstretch.options.duration = parseInt(newval);
                }
            });
        });

        wp.customize(panel + '_slideshow_speed', function (value) {
            value.bind(function (newval) {
                if (jQuery(".header-wrapper > div").data().backstretch) {
                    jQuery(".header-wrapper > div").data().backstretch.options.transitionDuration = parseInt(newval);
                }
            });
        });

    });

    wp.customize('header_front_page_image_mobile', function (value) {
        value.bind(function (newval) {

            var styleHolder = $('[data-name="custom-mobile-image"]');

            if (newval) {

                if (styleHolder.length === 0) {
                    styleHolder = jQuery('<style type="text/css" data-name="custom-mobile-image"></style>');
                    styleHolder.appendTo('head');
                }

                var mobile_image_style_content =
                    '@media screen and (max-width: 767px) {\n' +
                    '.header-homepage:not(.header-slide) {\n' +
                    'background-image: url(' + newval + ') !important;\n' +
                    '}\n' +
                    '}';

                styleHolder.text(mobile_image_style_content);

            } else {
                styleHolder.remove();
            }

        });
    });

    wp.customize('header_bg_position_mobile', function (value) {
        value.bind(updateMobileBgImagePosition);
    });

    wp.customize('header_bg_position_mobile_offset', function (value) {
        value.bind(updateMobileBgImagePosition);
    });

    /*END HEADER BACKGROUND */

    /* START HEADER OVERLAY */

    $.each(headerPanels, function (index, panel) {

        wp.customize(panel + '_show_overlay', function (value) {
            value.bind(function (newval) {
                if (newval) {
                    $(navigationSelectors[panel] + ' .header-wrapper > div').addClass('color-overlay');
                } else {
                    $(navigationSelectors[panel] + ' .header-wrapper > div').removeClass('color-overlay');
                }
            });
        });

    });

    var headerSelectors = {
        'header': '.header-homepage:not(.header-slide)',
        'inner_header': '.header'
    };

    $.each(headerPanels, function (index, panel) {

        wp.customize(panel + '_overlay_shape', function (value) {

            value.bind(function (newval) {

                var shapeStyleHolder = $('[data-name="header-shapes"]');

                if (newval != "none") {

                    shapeSelector = headerSelectors[panel] + '.color-overlay:after';

                    if (shapeStyleHolder.length == 0) {
                        shapeStyleHolder = jQuery('<style type="text/css" data-name="header-shapes"></style>');
                        shapeStyleHolder.appendTo('head');
                    }

                    jQuery.post(parent.ajaxurl + "?action=mesmerize_shape_value", {'shape': newval}, function (response) {
                        var shape = JSON.parse(response);
                        var overlay_shape_content =
                            shapeSelector + '{\n' +
                            'background: ' + shape + ';\n' +
                            '}\n';
                        shapeStyleHolder.text(overlay_shape_content);
                    });

                } else {
                    shapeStyleHolder.remove();
                }

            });
        });

    });

    /* END HEADER OVERLAY */

    wp.customize('header_gradient', function (value) {
        value.bind(function (newval, oldval) {
            $('.header-homepage').removeClass(oldval);
            $('.header-homepage').addClass(newval);
        });
    });

    wp.customize('inner_header_gradient', function (value) {
        value.bind(function (newval, oldval) {
            $('.header').removeClass(oldval);
            $('.header').addClass(newval);
        });
    });

    /* START BOTTOM ARROW */

    wp.customize('header_show_bottom_arrow', function (value) {
        value.bind(function (newval) {
            if (newval) {
                var fontSize = wp.customize('header_size_bottom_arrow').get();

                $('.header-homepage-arrow-c').removeAttr('data-reiki-hidden');
                $('.header-homepage-arrow').css('font-size', parseInt(fontSize * 84 / 100) + 'px');
                $('.header-homepage-arrow').css('bottom', wp.customize('header_offset_bottom_arrow').get() + 'px');
                $('.header-homepage-arrow').css('background', wp.customize('header_background_bottom_arrow').get());
                $('.header-homepage-arrow > i').css('width', fontSize + 'px');
                $('.header-homepage-arrow > i').css('height', fontSize + 'px');
                $('.header-homepage-arrow > i').css('color', wp.customize('header_color_bottom_arrow').get());
            } else {
                $('.header-homepage-arrow-c').attr('data-reiki-hidden', 'true');
            }

        });
    });

    wp.customize('header_bounce_bottom_arrow', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('.header-homepage-arrow-c .header-homepage-arrow').addClass('move-down-bounce');
            } else {
                $('.header-homepage-arrow-c .header-homepage-arrow').removeClass('move-down-bounce');
            }
        });
    });

    wp.customize('header_bottom_arrow', function (value) {
        value.bind(function (newval, oldval) {
            $('.header-homepage-arrow-c .header-homepage-arrow .fa').removeClass(oldval).addClass(newval);
        });
    });

    wp.customize('header_size_bottom_arrow', function (value) {
        value.bind(function (newval) {
            $('.header-homepage-arrow').css('font-size', parseInt(newval * 84 / 100) + 'px');
            $('.header-homepage-arrow > i').css('width', newval + 'px');
            $('.header-homepage-arrow > i').css('height', newval + 'px');
        });
    });

    wp.customize('header_offset_bottom_arrow', function (value) {
        value.bind(function (newval) {
            $('.header-homepage-arrow').css('bottom', newval + 'px');
        });
    });

    wp.customize('header_color_bottom_arrow', function (value) {
        value.bind(function (newval) {
            $('.header-homepage-arrow > i').css('color', newval);
        });
    });

    wp.customize('header_background_bottom_arrow', function (value) {
        value.bind(function (newval) {
            $('.header-homepage-arrow').css('background', newval);
        });
    });

    /* END BOTTOM ARROW */


    wp.customize('full_height_header', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('.header-homepage').addClass('header-full-height');
            } else {
                $('.header-homepage').css('min-height', "");
                $('.header-homepage').removeClass('header-full-height');
            }
        });
    });

    wp.customize('header_overlap', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('body').addClass('overlap-first-section');
            } else {
                $('body').removeClass('overlap-first-section');
            }
        });
    });

    wp.customize('header_overlap_with', function (value) {
        value.bind(function (newval) {

            var styleHolder = jQuery('[data-name="header-overlap-with"]');

            styleHolder.text('' +
                '@media screen and (min-width: 768px) {\n' +
                '   .mesmerize-front-page.overlap-first-section:not(.mesmerize-front-page-with-slider) .header-homepage {\n' +
                '       padding-bottom: ' + newval + ';\n' +
                '   }\n' +
                '   .mesmerize-front-page.overlap-first-section:not(.mesmerize-front-page-with-slider) .page-content div[data-overlap]:first-of-type > div:not([class*="section-separator"]) {\n' +
                '       margin-top: -' + newval + ';\n' +
                '   }\n' +
                '}\n');

        });
    });

    /* START HEADER CONTENT */

    wp.customize('header_text_box_text_vertical_align', function (value) {
        value.bind(function (newVal, oldVal) {
            $('.header-hero-content-v-align').removeClass(oldVal).addClass(newVal);
        });
    });

    wp.customize('header_media_box_vertical_align', function (value) {
        value.bind(function (newVal, oldVal) {
            $('.header-hero-media-v-align').removeClass(oldVal).addClass(newVal);
        });
    });

    wp.customize('header_text_box_text_align', function (value) {
        value.bind(function (newVal, oldVal) {
            $('.mesmerize-front-page  .header-content .align-holder').removeClass(oldVal).addClass(newVal);
        });
    });

    /*
    wp.customize('header_content_image', function (value) {
        value.bind(function (newval) {
            $('.homepage-header-image').attr('src', newval);
        });
    });
    */

    // media frame //
    wp.customize('header_content_frame_offset_left', function (value) {
        value.bind(function (left) {
            var top = wp.customize('header_content_frame_offset_top').get();
            $('.mesmerize-front-page  .header-description .overlay-box-offset').css({
                'transform': 'translate(' + left + '%,' + top + '%)'
            });
        });
    });


    wp.customize('header_content_frame_offset_top', function (value) {
        value.bind(function (top) {
            var left = wp.customize('header_content_frame_offset_left').get();
            $('.mesmerize-front-page  .header-description .overlay-box-offset').css({
                'transform': 'translate(' + left + '%,' + top + '%)'
            });
        });
    });

    wp.customize('header_content_frame_width', function (value) {
        value.bind(function (width) {
            $('.mesmerize-front-page  .header-description .overlay-box-offset').css({
                'width': width + '%'
            });
        });
    });


    wp.customize('header_content_frame_height', function (value) {
        value.bind(function (height) {
            $('.mesmerize-front-page  .header-description .overlay-box-offset').css({
                'height': height + '%'
            });
        });
    });

    wp.customize('header_content_frame_thickness', function (value) {
        value.bind(function (thickness) {
            $('.mesmerize-front-page  .header-description .overlay-box-offset').css({
                'border-width': thickness + 'px'
            });
        });
    });

    wp.customize('header_content_frame_show_over_image', function (value) {
        value.bind(function (value) {
            var zIndex = value ? "1" : "-1";
            $('.mesmerize-front-page  .header-description .overlay-box-offset').css('z-index', zIndex);
        });
    });

    wp.customize('header_content_frame_shadow', function (value) {
        value.bind(function (value) {
            var shadow = "shadow-medium";
            var frame = $('.mesmerize-front-page  .header-description .overlay-box-offset');
            if (value) {
                frame.addClass(shadow);
            } else {
                frame.removeClass(shadow);
            }
        });
    });


    wp.customize('header_content_frame_color', function (value) {
        value.bind(function (color) {
            var type = wp.customize('header_content_frame_type').get();
            var property = type + "-color";
            $('.mesmerize-front-page  .header-description .overlay-box-offset').css(property, color);
        });
    });


    wp.customize('header_content_frame_hide_on_mobile', function (value) {
        value.bind(function (newVal) {
            var $items = $('' +
                '.header-description-row .overlay-box .offset-border,' +
                '.header-description-row .overlay-box .offset-background');
            if (newVal) {
                $items.addClass('hide-xs');
            } else {
                $items.removeClass('hide-xs');
            }

        });
    });

    /* END HEADER CONTENT */

    /* START FOOTER */

    function changeFooterContentIcon(selector) {
        return function (value) {
            value.bind(function (newval, oldval) {
                $('.footer div[data-focus-control="' + selector + '"]').find('i').removeClass(oldval).addClass(newval);
            });
        }
    }

    for (var k = 1; k < 4; k++) {
        wp.customize('footer_box' + k + '_content_icon', changeFooterContentIcon('footer_box' + k + '_content_icon'));
    }


    function toggleFooterSocialIcon(index, selector) {
        return function (value) {
            value.bind(function (newval) {
                if (newval) {
                    $('.footer .footer-social-icons ' + selector).eq(index).removeAttr('data-reiki-hidden');
                } else {
                    $('.footer .footer-social-icons ' + selector).eq(index).attr('data-reiki-hidden', 'true');
                }
            });
        }
    }

    function changeFooterSocialIcon(index, selector) {
        return function (value) {
            value.bind(function (newval, oldval) {
                $('.footer .footer-social-icons ' + selector).eq(index).find('i').removeClass(oldval).addClass(newval);
            });
        }
    }

    for (var ki = 0; ki < 5; ki++) {
        wp.customize('footer_content_social_icon_' + ki + '_enabled', toggleFooterSocialIcon(ki, '.social-icon'));
        wp.customize('footer_content_social_icon_' + ki + '_icon', changeFooterSocialIcon(ki, '.social-icon'));
    }


    wp.customize('footer_paralax', function (value) {
        value.bind(function (newval) {
            if (newval) {
                $('.footer').addClass('paralax');
                mesmerizeFooterParalax();
            } else {
                $('.footer').removeClass('paralax');
                mesmerizeStopFooterParalax();
            }
        });
    });

    /* END FOOTER */

})(jQuery);

(function ($) {
    function getGradientValue(setting) {
        var getValue = parent.CP_Customizer ? parent.CP_Customizer.utils.getValue : parent.Mesmerize.Utils.getValue;
        var control = parent.wp.customize.control(setting);
        var gradient = getValue(control);
        var colors = gradient.colors;
        var angle = gradient.angle;
        angle = parseFloat(angle);
        return parent.Mesmerize.Utils.getGradientString(colors, angle);
    }

    function recalculateHeaderOverlayGradient() {
        $('.header-homepage .background-overlay').css("background-image", getGradientValue('header_overlay_gradient_colors'));
    }

    function recalculateInnerHeaderOverlayGradient() {
        $('.header .background-overlay').css("background-image", getGradientValue('inner_header_overlay_gradient_colors'));
    }

    liveUpdate('header_overlay_gradient_colors', recalculateHeaderOverlayGradient);
    liveUpdate('inner_header_overlay_gradient_colors', recalculateInnerHeaderOverlayGradient);
})(jQuery);
