wp.customize.controlConstructor['sidebar-button-group'] = wp.customize.Control.extend({
    ready: function () {
        var control = this;
        var components = this.params.choices;
        var popupId = this.params.popup;
        var in_row_with = this.params.in_row_with || [];

        control.container.find('#group_customize-button-' + popupId).click(function () {

            if (window.CP_Customizer) {
                CP_Customizer.openRightSidebar(popupId);
            } else {
                Mesmerize.openRightSidebar(popupId);
            }
        });

        control.container.find('#' + popupId + '-popup > ul').on('focus', function (event) {
            return false;
        });

        wp.customize.bind('pane-contents-reflowed', function () {
            var holder = control.container.find('#' + popupId + '-popup > ul');


            var controls = [];


            _.each(components, function (c) {
                var _c = wp.customize.control(c);
                if (_c) {
                    controls.push(_c);
                }
            });


            _.each(controls, function (c) {
                holder.append(c.container);
                c.container.on('focus', 'input,textarea', function () {
                    control.currentFocusedElement = this;
                });

                // c.container.on('blur', 'input,textarea', function () {
                //     control.currentFocusedElement = false;
                // });
            });


            if (in_row_with && in_row_with.length) {
                _.each(in_row_with, function (c) {
                    control.container.css({
                        "width": "40%",
                        "clear": "right",
                        "float": "right",
                    });

                    var ct = wp.customize.control(c);
                    if (ct) {
                        ct.container.css({
                            "width": "auto",
                            "max-width": "calc(60% - 12px)"
                        })
                    }
                })
            }

            if (!_.isArray(control.params.choices)) {
                return;
            }

            var hasActiveItems = true;

            if (_.isArray(control.params.choices) && control.params.choices.length) {
                hasActiveItems = control.params.choices.map(function (setting) {
                    return wp.customize.control(setting) ? wp.customize.control(setting).active() : false;
                }).reduce(function (a, b) {
                    return a || b
                });

            } else {
                hasActiveItems = false;
            }

            if (control.active()) {
                if (hasActiveItems) {
                    control.activate();
                } else {
                    control.deactivate();
                }
            }

            if (control.currentFocusedElement) {
                control.currentFocusedElement.focus();
                control.currentFocusedElement = false;
            }
        });
    }
});
