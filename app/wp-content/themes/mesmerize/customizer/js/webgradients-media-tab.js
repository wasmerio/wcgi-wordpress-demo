(function ($) {
    var wp = window.wp.media ? window.wp : parent.wp;
    var fetchedIcons = false;


    var fuzzy_match = (function () {
        var cache = _.memoize(function (str) {
            return new RegExp("^" + str.replace(/./g, function (x) {
                return /[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/.test(x) ? "\\" + x + "?" : x + "?";
            }) + "$");
        })
        return function (str, pattern) {
            return cache(str).test(pattern)
        }
    })()

    var cpWebGradientsOptions = {};

    var cpWebGratientsIconModel = Backbone.Model.extend({
        defaults: {
            buttons: {
                check: !0
            },
            can: {
                save: !1,
                remove: !1
            },
            id: null,
            title: null,
            date: null,
            modified: null,
            mime: "web-gradient/class",
            dateFormatted: null,
            height: null,
            width: null,
            orientation: null,
            filesizeInBytes: null,
            filesizeHumanReadable: null,
            size: {
                url: null
            },
            type: "web-gradient",
            icon: null,
            parsed: false
        }
    });


    var cpWebGratientsIcons = Backbone.Collection.extend({
        model: cpWebGratientsIconModel,
        initialize: function (data) {
            this.url = parent.ajaxurl + "?action=mesmerize_webgradients_list"
        },
        parse: function (data) {
            return data;
        }
    });

    var iconTemplate = _.template(''+
            '<div class="attachment-preview js--select-attachment type-image subtype-jpeg landscape">'+
                '<div class="thumbnail">'+
                        '<div class="webgradient <%= webgradient %>" aria-hidden="true"></i>'+
                        '<div class="label"><%= title %></div>'+
                '</div>'+
                '<button type="button" class="button-link check" tabindex="0"><span class="media-modal-icon"></span><span class="screen-reader-text">' + mesmerize_customize_settings.l10n.deselect + '</span></button>'+
            '</div>');


    var cpWebGratientsIconView = wp.media.view.Attachment.extend({
        tagName: "li",
        className: "attachment webgradients-image",
        template: iconTemplate,
        controller: this.controller,
        initialize: function () {
            this.render()
        },

        attributes: function () {
            return {
                'data-search': this.model.get('gradient').trim(),
                'aria-label': this.model.get('title'),
                'title': this.model.get('title'),
                'tabIndex': 0,
            }
        },
        events: {
            "click .js--select-attachment": "toggleSelectionHandler"
        },
        render: function () {
            var icon = this.model.get('gradient');
            var title = this.model.get('title');
            this.$el.html(this.template({
                'webgradient': icon,
                'title': title
            }))
        },

        toggleSelectionHandler: function (event) {
            var method = 'toggle';

            // Catch arrow events
            if (37 === event.keyCode || 38 === event.keyCode || 39 === event.keyCode || 40 === event.keyCode) {
                this.controller.trigger('attachment:keydown:arrow', event);
                return;
            }

            // Catch enter and space events
            if ('keydown' === event.type && 13 !== event.keyCode && 32 !== event.keyCode) {
                return;
            }

            event.preventDefault();


            if (event.shiftKey) {
                method = 'between';
            } else if (event.ctrlKey || event.metaKey) {
                method = 'toggle';
            }

            this.toggleSelection({
                method: 'add'
            });

            $('.media-selection.one .attachment-preview.type-fa-icon .thumbnail').html('<div class="webgradient ' + this.model.get('gredient') + '"></div>')

            this.controller.trigger('selection:toggle');
        }
    });


    var cpWebGratientsIconsView = wp.media.View.extend({
        tagName: "ul",
        className: "attachments cp-fa-images",
        attributes: {
            tabIndex: 0
        },
        initialize: function () {

            _.defaults(this.options, {
                refreshSensitivity: wp.media.isTouchDevice ? 300 : 200,
                refreshThreshold: 3,
                AttachmentView: wp.media.view.Attachment,
                sortable: false,
                resize: false,
                idealColumnWidth: 150
            });

            this._viewsByCid = {};
            this.$window = $(window);
            this.options.scrollElement = this.options.scrollElement || this.el;
            $(this.options.scrollElement).on("scroll", this.scroll);

            var iconsView = this;
            iconsView.collection.each(function (icon) {
                var iconView = new cpWebGratientsIconView({
                    controller: iconsView.controller,
                    selection: iconsView.options.selection,
                    model: icon
                });
                iconsView.views.add(iconView)
            })
        },

        scroll: function () {

        }
    });

    var cpFontAwesomeSearch = wp.media.View.extend({
        tagName: "input",
        className: "search",
        id: "media-search-input cp-fa-search",
        attributes: {
            type: "search",
            placeholder: wp.media.view.l10n.search
        },
        events: {
            input: "search",
            keyup: "search",
            change: "search",
            search: "search"
        },
        render: function () {
            return this;
        },

        search: _.debounce(function (event) {
            var value = event.target.value;
            var items = this.options.browserView.view.$el.find('li');

            function toggleSearchVisibility(index, el) {
                var $el = $(el);
                if (fuzzy_match($el.data('search'), value)) {
                    $el.show();
                } else {
                    $el.hide();
                }
            }
            items.each(toggleSearchVisibility);
        }, 50)
    });

    var cpFontAwesomeBrowser = wp.media.View.extend({
        tagName: "div",
        className: "cp-fa-media attachments-browser",

        initialize: function () {

            var browserVIew = this;
            _.defaults(this.options, {
                filters: !1,
                search: true,
                date: false,
                display: !1,
                sidebar: false,
                toolbar: true,
                AttachmentView: wp.media.view.Attachment.Library
            });

            var icons = new cpWebGratientsIcons();
            var filter = this.options.options ? this.options.options.filter : false;

            function displayIcons(icons) {
                if (filter) {
                    icons = new cpWebGratientsIcons(icons.filter(filter));
                }

                var state = browserVIew.controller.state(),
                    selection = state.get('selection');
                state.set('multiple', false);
                selection.multiple = false;
                var iconsView = new cpWebGratientsIconsView({
                    controller: browserVIew.controller,
                    selection: selection,
                    collection: icons
                });
                browserVIew.views.add(iconsView)
            }


            if (!fetchedIcons) {
                icons.fetch({
                    success: function (icons) {
                        fetchedIcons = icons;
                        displayIcons(icons);
                    }
                });
            } else {
                displayIcons(fetchedIcons);
            }

            this.createToolbar();
        },

        settings: function (view) {
            if (this._settings) {
                this._settings.remove();
            }
            this._settings = view;
            this.views.add(view);
        },
        createToolbar: function () {
            this.toolbar = new wp.media.view.Toolbar({
                controller: this.controller
            })
            this.views.add(this.toolbar);
            this.toolbar.set("search", new cpFontAwesomeSearch({
                controller: this.controller,
                browserView: this.views
            }));
        }
    });


    function extendFrameWithcpWebGratients(frame) {
        var wpMediaFrame = frame;
        var cpWebGratientsFrameExtension = {
            browseRouter: function (routerView) {
                var routes = {
                    "cp_web_gradients": {
                        text: mesmerize_customize_settings.l10n.chooseGradientLabel,
                        priority: 50
                    }
                };
                controller = routerView.controller;
                routerView.set(routes);
            },

            bindHandlers: function () {
                wpMediaFrame.prototype.bindHandlers.apply(this, arguments);
                this.on('content:create:cp_web_gradients', this.cpBrowseFontAwesome, this);
            },

            createStates: function () {
                wpMediaFrame.prototype.createStates.apply(this, arguments);
            },


            cpBrowseFontAwesome: function (contentRegion) {

                var state = this.state();

                this.$el.removeClass('hide-toolbar');

                contentRegion.view = new cpFontAwesomeBrowser({
                    controller: this,
                    options : cpWebGradientsOptions
                });
            }

        }

        return wpMediaFrame.extend(cpWebGratientsFrameExtension);
    }

    wp.media.cp = wp.media.cp || {};
    if (!wp.media.cp.extendFrameWithWebGradients) {
        wp.media.cp.extendFrameWithWebGradients = function(options) {
            cpWebGradientsOptions = options;
            return extendFrameWithcpWebGratients 
        };
    }
})(jQuery);
