(function () {
    var api = wp.customize;

    _.each(jsvars, function (jsVars, setting) {

        var css = '',
            cssArray = {};

        api(setting, function (value) {

            value.bind(_.debounce(
                function (newval) {

                    if (undefined !== jsVars && 0 < jsVars.length) {

                        _.each(jsVars, function (jsVar) {

                            var val = newval;

                            // Make sure element is defined.
                            if (undefined === jsVar.element) {
                                jsVar.element = '';
                            }

                            // Make sure property is defined.
                            if (undefined === jsVar.property) {
                                jsVar.property = '';
                            }

                            // Use empty prefix if undefined
                            if (undefined === jsVar.prefix) {
                                jsVar.prefix = '';
                            }

                            // Use empty suffix if undefined
                            if (undefined === jsVar.suffix) {
                                jsVar.suffix = '';
                            }

                            // Use empty units if undefined
                            if (undefined === jsVar.units) {
                                jsVar.units = '';
                            }

                            // Use css if method is undefined
                            if (undefined === jsVar['function']) {
                                jsVar['function'] = 'css';
                            }

                            // Use $ (just the value) if value_pattern is undefined
                            if (undefined === jsVar.value_pattern) {
                                jsVar.value_pattern = '$';
                            }

                            _.each(jsVars, function (args, i) {

                                // Value is a string
                                if ('string' === typeof newval) {

                                    // Process the value pattern
                                    if (undefined !== args.value_pattern) {
                                        val = args.value_pattern.replace(/\$/g, args.prefix + newval + args.units + args.suffix);
                                    } else {
                                        val = args.prefix + newval + args.units + args.suffix;
                                    }

                                    // Simple tweak for background-image properties.
                                    if ('background-image' === args.property) {
                                        if (0 > val.indexOf('url(')) {
                                            val = 'url("' + val + '")';
                                        }
                                    }

                                    // Inject HTML
                                    if ('html' === args['function']) {
                                        if ('undefined' !== typeof args.attr && undefined !== args.attr) {
                                            jQuery(args.element).attr(args.attr, val);
                                        } else {
                                            jQuery(args.element).html(val);
                                        }

                                        // Add CSS
                                    } else {

                                        // If we have new value, replace style contents with custom css
                                        if ('' !== val) {
                                            var cssNewContent = '';
                                            var styleProps = args.property.split(',');

                                            for (var propIndex = 0; propIndex < styleProps.length; propIndex++) {
                                                cssNewContent += styleProps[propIndex] + ':' + val + ';'
                                            }


                                            cssArray[i] = args.element + '{' + cssNewContent + '}';
                                        }

                                        // Else let's clear it out
                                        else {
                                            cssArray[i] = '';
                                        }

                                    }

                                    // Value is an object
                                } else if ('object' === typeof newval) {

                                    cssArray[i] = '';
                                    _.each(newval, function (subValueValue, subValueKey) {

                                        if (undefined !== args.choice) {
                                            if (args.choice === subValueKey) {
                                                cssArray[i] += args.element + '{' + args.property + ':' + args.prefix + subValueValue + args.units + args.suffix + ';}';
                                            }
                                        } else {
                                            if (_.contains(['top', 'bottom', 'left', 'right'], subValueKey)) {
                                                cssArray[i] += args.element + '{' + args.property + '-' + subValueKey + ':' + args.prefix + subValueValue + args.units + args.suffix + ';}';
                                            } else {
                                                if (subValueKey === "variant") {

                                                    var fontStyle = (subValueValue.indexOf('italic') !== -1) ? "italic" : "normal";
                                                    cssArray[i] += args.element + '{font-style:' + fontStyle + ';}';

                                                    var fontWeight = (subValueValue.indexOf('regular') !== -1) ? "400" : subValueValue.replace(/([a-zA-Z]+)/ig, "");
                                                    cssArray[i] += args.element + '{font-weight:' + fontWeight + ';}';

                                                } else {
                                                    if (subValueKey === 'addwebfont' || subValueKey == 'subsets') {
                                                        return;
                                                    } else {
                                                        if (subValueKey === 'mobile-font-size') {
                                                            cssArray[i] += '@media screen and (max-width: 767px) {' + args.element + '{font-size:' + args.prefix + subValueValue + args.units + args.suffix + ';}}';
                                                        } else {
                                                            cssArray[i] += args.element + '{' + subValueKey + ':' + args.prefix + subValueValue + args.units + args.suffix + ';}';
                                                        }
                                                    }

                                                }
                                            }
                                        }
                                    });

                                }

                                if (args.media_query) {
                                    cssArray[i] = args.media_query + " {\n\n" + cssArray[i] + "\n\n}";
                                }


                            });

                        });

                        css = _.toArray(cssArray).reduce(function (a, s) {
                            return a + s
                        }, '');

                        setTimeout(function () {

                            if ('' !== css) {

                                // Make sure we have a stylesheet with the defined ID.
                                // If we don't then add it.
                                if (!jQuery('#kirki-customizer-postmessage' + setting.replace(/\[/g, '-').replace(/\]/g, '')).size()) {
                                    jQuery('head').append('<style id="kirki-customizer-postmessage' + setting.replace(/\[/g, '-').replace(/\]/g, '') + '"></style>');
                                }
                                jQuery('#kirki-customizer-postmessage' + setting.replace(/\[/g, '-').replace(/\]/g, '')).text(css);
                            }

                        }, 100);

                    }

                }
            ), 100);

        });

    });

})(jQuery);
