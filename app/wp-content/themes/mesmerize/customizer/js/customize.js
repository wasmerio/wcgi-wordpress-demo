(function (root, $) {

    // used for lazy loading images ( show make the customizer available faster )
    $(function () {
        $('img[data-src]').each(function () {
            var img = this;
            setTimeout(function () {
                img.setAttribute('src', img.getAttribute('data-src'));
            }, 5)
        });
    });


    function installPlugin(slug, successCallback, errorCallback) {
        $(document).trigger('extendthemes-plugin-status-update', [slug, 'install']);
        wp.updates.ajax('install-plugin', {
            slug: slug,
            success: successCallback,
            error: errorCallback || function () {
            }
        })
    }


    function updateLinkedSettings(newValue) {

        var toUpdate = {};

        for (var i = 0; i < this.update.length; i++) {
            var update = this.update[i];

            if (update.value === newValue) {
                _.extend(toUpdate, update.fields);
            }

        }

        var refreshAfterSet = (this.__initialTransport === 'refresh');
        for (var settingID in toUpdate) {
            var setting = wp.customize(settingID);

            if (setting) {
                var oldTransport = setting.transport;

                setting.transport = 'postMessage';
                kirkiSetSettingValue(settingID, toUpdate[settingID]);
                setting.transport = oldTransport;

                if (oldTransport === 'refresh') {
                    refreshAfterSet = true;
                }
            }
        }

        if (refreshAfterSet) {
            wp.customize.previewer.refresh();
        }

    }

    if (!root.Mesmerize) {
        root.Mesmerize = {

            activatePlugin: function (plugin, successCallback, errorCallback, alwaysCallback) {
                $(document).trigger('extendthemes-plugin-status-update', [plugin.slug, 'activate']);
                $.get(plugin.activate_link)
                    .done(successCallback || function () {
                    })
                    .fail(errorCallback || function () {

                    })
                    .always(function () {

                        if (alwaysCallback) {
                            alwaysCallback.apply(this, arguments);
                        }
                    })
            },

            installPlugin: function (plugin, callback) {
                function pluginInstalled(slug) {
                    $(document).trigger('extendthemes-plugin-status-update', [slug, 'ready']);
                    callback();
                }


                if (plugin.status === 'not-installed') {

                    installPlugin(plugin.slug, function () {
                        plugin.activate_link = arguments[0].activateUrl;
                        Mesmerize.activatePlugin(plugin, null, null, function () {
                            pluginInstalled(plugin.slug);
                        });
                    }, function () {
                        pluginInstalled(plugin.slug);
                    });
                }

                if (plugin.status === 'installed') {
                    Mesmerize.activatePlugin(plugin, null, null, function () {
                        pluginInstalled(plugin.slug);
                    });
                }

                if (plugin.status === "active") {
                    pluginInstalled(plugin.slug);
                }

            },

            Utils: {
                getGradientString: function (colors, angle) {
                    if (!isNaN(angle)) angle += "deg";
                    var gradient = angle + ", " + colors[0].color + " 0%, " + colors[1].color + " 100%";
                    gradient = 'linear-gradient(' + gradient + ')';
                    return gradient;
                },

                getValue: function (component) {
                    var value = undefined;

                    if (component instanceof wp.customize.Control) {
                        value = component.setting.get();
                    }

                    if (component instanceof wp.customize.Setting) {
                        value = component.get();
                    }

                    if (_.isString(component)) {
                        value = wp.customize(component).get();
                    }

                    if (_.isString(value)) {

                        try {
                            value = decodeURI(value);

                        } catch (e) {

                        }

                        try {
                            value = JSON.parse(value);
                        } catch (e) {

                        }

                    }

                    return value;
                }
            },

            hooks: {
                addAction: function () {
                },
                addFilter: function () {
                },
                doAction: function () {

                },
                applyFilters: function () {

                }
            },

            wpApi: root.wp.customize,

            closePopUps: function () {
                root.tb_remove();
                root.jQuery('#TB_overlay').css({
                    'z-index': '-1'
                });
            },

            options: function (optionName) {
                return root.mesmerize_customize_settings[optionName];
            },

            popUp: function (title, elementID, data) {
                var selector = "#TB_inline?inlineId=" + elementID;
                var query = [];


                $.each(data || {}, function (key, value) {
                    query.push(key + "=" + value);
                });

                selector = query.length ? selector + "&" : selector + "";
                selector += query.join("&");

                root.tb_show(title, selector);

                root.jQuery('#TB_window').css({
                    'z-index': '5000001',
                    'transform': 'opacity .4s',
                    'opacity': 0
                });

                root.jQuery('#TB_overlay').css({
                    'z-index': '5000000'
                });


                setTimeout(function () {
                    root.jQuery('#TB_window').css({
                        'margin-top': -1 * ((root.jQuery('#TB_window').outerHeight() + 50) / 2),
                        'opacity': 1
                    });
                    root.jQuery('#TB_window').find('#cp-item-ok').focus();
                }, 0);

                if (data && data.class) {
                    root.jQuery('#TB_window').addClass(data.class);
                }

                return root.jQuery('#TB_window');
            },

            addModule: function (callback) {
                var self = this;

                jQuery(document).ready(function () {
                    // this.__modules.push(callback);
                    callback(self);
                });

            },
            getCustomizerRootEl: function () {
                return root.jQuery(root.document.body).find('form#customize-controls');
            },
            openRightSidebar: function (elementId, options) {
                options = options || {};
                this.hideRightSidebar();
                var $form = this.getCustomizerRootEl();
                var self = this;
                var $container = $form.find('#' + elementId + '-popup');
                if ($container.length) {
                    $container.addClass('active');

                    if (options.floating && !_(options.y).isUndefined()) {
                        $container.css({
                            top: options.y
                        });
                    }
                } else {
                    $container = $('<li id="' + elementId + '-popup" class="customizer-right-section active"> <span data-close-right-sidebar="true" title="' + mesmerize_customize_settings.l10n.closePanelLabel + '" class="close-panel"></span> </li>');

                    if (options.floating) {
                        $container.addClass('floating');
                    }

                    $toAppend = $form.find('li#accordion-section-' + elementId + ' > ul');

                    if ($toAppend.length === 0) {
                        $toAppend = $form.find('#sub-accordion-section-' + elementId);
                    }


                    if ($toAppend.length === 0) {
                        $toAppend = $('<div class="control-wrapper" />');
                        $toAppend.append($form.find('#customize-control-' + elementId).children());
                    }

                    $form.append($container);
                    $container.append($toAppend);

                    if (options.floating && !_(options.y).isUndefined()) {
                        $container.css({
                            top: options.y
                        });
                    }


                    $container.find('span.close-panel').click(self.hideRightSidebar);

                }

                if (options.focus) {
                    $container.find(options.focus)[0].scrollIntoViewIfNeeded();
                }

                $container.css('left', jQuery('#customize-header-actions')[0].offsetWidth + 1);

                self.hooks.doAction('right_sidebar_opened', elementId, options, $container);

                $container.on('focus', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                });

                $form.find('span[data-close-right-sidebar="true"]').click(function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    self.hideRightSidebar();
                });


                $form.find('li.accordion-section').unbind('click.right-section').bind('click.right-section', function (event) {
                    if ($(event.target).is('li') || $(event.target).is('.accordion-section-title')) {
                        if ($(event.target).closest('.customizer-right-section').length === 0) {
                            self.hideRightSidebar();
                        }
                    }
                });


            },

            hideRightSidebar: function () {
                var $form = root.jQuery(root.document.body).find('#customize-controls');
                var $visibleSection = $form.find('.customizer-right-section.active');
                if ($visibleSection.length) {
                    $visibleSection.removeClass('active');
                }
            },

            linkMod: function (settingID, linkWith) {
                var setting = wp.customize(settingID);
                // debugger;
                if (setting) {
                    var options = setting.findControls().length ? jQuery.extend(true, {}, setting.findControls()) : {};
                    options.__initialTransport = setting.transport;
                    options.update = linkWith;

                    var updater = _.bind(updateLinkedSettings, options);
                    setting.transport = 'postMessage';
                    setting.bind(updater);
                }
            },

            createMod: function (name, transport) {
                if (wp.customize(name)) {
                    return wp.customize(name);
                }

                name = "CP_AUTO_SETTING[" + name + "]";
                if (wp.customize(name)) {
                    return wp.customize(name);
                }

                wp.customize.create(name, name, {}, {
                    type: 'theme_mod',
                    transport: transport || 'postMessage',
                    previewer: wp.customize.previewer
                });

                return wp.customize(name);
            },

            _canUpdatedLinkedOptions: true,

            canUpdatedLinkedOptions: function () {
                return this._canUpdatedLinkedOptions;
            },

            disableLinkedOptionsUpdater: function () {
                this._canUpdatedLinkedOptions = false;
            },

            enableLinkedOptionsUpdater: function () {
                this._canUpdatedLinkedOptions = true;
            }

        };
    }

    function openMediaBrowser(type, callback, data) {
        var cb;
        if (callback instanceof jQuery) {
            cb = function (response) {

                if (!response) {
                    return;
                }

                var value = response[0].url;
                if (data !== "multiple") {
                    if (type == "icon") {
                        value = response[0].fa
                    }
                    callback.val(value).trigger('change');
                }
            }
        } else {
            cb = callback;
        }

        switch (type) {
            case "image":
                openMultiImageManager(mesmerize_customize_settings.l10n.changeImageLabel, cb, data);
                break;
        }
    }

    function openMediaCustomFrame(extender, mode, title, single, callback) {
        var interestWindow = root;

        var frame = extender(interestWindow.wp.media.view.MediaFrame.Select);

        var custom_uploader = new frame({
            title: title,
            button: {
                text: title
            },
            multiple: !single
        });


        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function () {
            attachment = custom_uploader.state().get('selection').toJSON();
            custom_uploader.content.mode('browse');
            callback(attachment);
        });


        custom_uploader.on('close', function () {
            custom_uploader.content.mode('browse');
            callback(false);
        });

        //Open the uploader dialog
        custom_uploader.open();
        custom_uploader.content.mode(mode);
        // Show Dialog over layouts frame
        interestWindow.jQuery(custom_uploader.views.selector).parent().css({
            'z-index': '16000000'
        });

    }

    function openMultiImageManager(title, callback, single) {
        var node = false;
        var interestWindow = root;
        var custom_uploader = interestWindow.wp.media.frames.file_frame = interestWindow.wp.media({
            title: title,
            button: {
                text: mesmerize_customize_settings.l10n.chooseImagesLabel
            },
            multiple: !single
        });
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function () {
            attachment = custom_uploader.state().get('selection').toJSON();
            callback(attachment);
        });
        custom_uploader.off('close.cp').on('close.cp', function () {
            callback(false);
        });
        //Open the uploader dialog
        custom_uploader.open();

        custom_uploader.content.mode('browse');
        // Show Dialog over layouts frame
        interestWindow.jQuery(interestWindow.wp.media.frame.views.selector).parent().css({
            'z-index': '16000000'
        });
    }

    root.Mesmerize.openMediaBrowser = openMediaBrowser;
    root.Mesmerize.openMediaCustomFrame = openMediaCustomFrame;

    if (window.wp && window.wp.customize) {
        wp.customize.controlConstructor['radio-html'] = wp.customize.Control.extend({

            ready: function () {

                'use strict';

                var control = this;

                // Change the value
                this.container.on('click', 'input', function () {
                    control.setting.set(jQuery(this).val());
                });

            }


        });
    }

    var linkedSettingsBindAdded = false;


    wp.customize.bind('pane-contents-reflowed', function () {

        if (linkedSettingsBindAdded) {
            return;
        }

        linkedSettingsBindAdded = true;

        jQuery.each(wp.customize.settings.controls, function (control, options) {

            if (options.update && Mesmerize.canUpdatedLinkedOptions()) {
                var setting = wp.customize(options.settings.default);
                // debugger;
                options.__initialTransport = setting.transport;

                var updater = _.bind(updateLinkedSettings, options);
                setting.transport = 'postMessage';
                setting.bind(updater);
            }
        });

        var overlappableSetting = Mesmerize.createMod('header_overlappable_section');

        overlappableSetting.bind(function (value) {
            if (CP_Customizer && value) {
                var sectionData = CP_Customizer.options('data:sections', []).filter(function (data) {
                    return data.id === value
                }).pop();

                if (sectionData && CP_Customizer.preview.jQuery('[data-id^="' + value + '"]').length === 0) {

                    CP_Customizer.one(CP_Customizer.events.PREVIEW_LOADED, function () {
                        CP_Customizer.preview.insertSectionFromData(sectionData);
                    });

                }
            }
            overlappableSetting.set('');
        });

    });
})(window, jQuery);

// fix selectize opening
(function ($) {

    $(document).on('mouseup', '.selectize-input', function () {
        if ($(this).parent().height() + $(this).parent().offset().top > window.innerHeight) {
            $('.wp-full-overlay-sidebar-content').scrollTop($(this).parent().height() + $(this).parent().offset().top)
        }
    });

    $(document).on('change', '.customize-control-kirki-select select', function () {
        $(this).focusout();
    });

})(jQuery);

(function ($) {

    $(document).on('click', '[data-plugin-install]', function (event) {
        event.preventDefault();
        event.stopPropagation();

        var $this = $(this);
        if ($this.is('.disabled')) {
            return;
        }

        var pluginData = {
            status: $(this).data('status'),
            slug: $(this).data('slug'),
            activate_link: $(this).data('activate-href')
        };

        $this.next('.spinner').css('visibility', 'visible');
        $this.addClass('disabled');
        Mesmerize.installPlugin(pluginData, function () {
        });
    });

    $(document).on('extendthemes-plugin-status-update', function (event, slug, status) {
        var $button = $('[data-plugin-install][data-slug="' + slug + '"]');

        if (status === "ready") {
            $button.next('.spinner').css('visibility', 'hidden');
        }

    });

    jQuery(function () {
        if (!window.cpCustomizerGlobal) {

            if (!wp.customize.section('extendthemes_start_from_demo_site')) {
                return;
            }

            var predefignedSitesSection = wp.customize.section('extendthemes_start_from_demo_site').container;
            predefignedSitesSection.find('*').addBack().off();

            predefignedSitesSection.on('click', function (event) {

                event.preventDefault();
                event.stopPropagation();
                window.location = window.__mesmerizeDemoImportInfoTabUrl;
                return false;

            });
        }

    })

})(jQuery);

(function (root, $, api) {
    var binded = false;
    wp.customize.bind('pane-contents-reflowed', function () {
        if (binded) {
            return;
        }

        binded = true;

        api.previewer.bind('focus-control-for-setting', function (settingId) {
            var matchedControls = [];
            api.control.each(function (control) {
                var settingIds = _.pluck(control.settings, 'id');
                if (-1 !== _.indexOf(settingIds, settingId)) {
                    matchedControls.push(control);
                }
            });

            if (matchedControls.length) {
                var control = matchedControls[0];
                var sidebar = control.container.closest('.customizer-right-section');
                if (sidebar.length) {
                    var buttonSelectorValue = sidebar.attr('id').replace('-popup', ''),
                        buttonSelector = '[data-sidebar-container="' + buttonSelectorValue + '"]';

                    if ($(buttonSelector).length) {
                        $(buttonSelector)[0].scrollIntoView();
                        $(buttonSelector).click();
                    }

                    control.focus();
                }
            }

        })
    })
})(window, jQuery, wp.customize);
