function kirkiNotifications(settingName, type, configID) {

    wp.customize(settingName, function (setting) {
        setting.bind(function (value) {
            var code = 'long_title',
                subs = {},
                message;

            // Dimension fields.
            if ('kirki-dimension' === type) {

                message = window.kirki.l10n['invalid-value'];

                if (false === kirkiValidateCSSValue(value)) {
                    kirkiNotificationsWarning(setting, code, message);
                } else {
                    setting.notifications.remove(code);
                }

            }

            // Spacing fields.
            if ('kirki-spacing' === type) {

                setting.notifications.remove(code);
                if ('undefined' !== typeof value.top) {
                    if (false === kirkiValidateCSSValue(value.top)) {
                        subs.top = window.kirki.l10n.top;
                    } else {
                        delete subs.top;
                    }
                }

                if ('undefined' !== typeof value.bottom) {
                    if (false === kirkiValidateCSSValue(value.bottom)) {
                        subs.bottom = window.kirki.l10n.bottom;
                    } else {
                        delete subs.bottom;
                    }
                }

                if ('undefined' !== typeof value.left) {
                    if (false === kirkiValidateCSSValue(value.left)) {
                        subs.left = window.kirki.l10n.left;
                    } else {
                        delete subs.left;
                    }
                }

                if ('undefined' !== typeof value.right) {
                    if (false === kirkiValidateCSSValue(value.right)) {
                        subs.right = window.kirki.l10n.right;
                    } else {
                        delete subs.right;
                    }
                }

                if (!_.isEmpty(subs)) {
                    message = window.kirki.l10n['invalid-value'] + ' (' + _.values(subs).toString() + ') ';
                    kirkiNotificationsWarning(setting, code, message);
                } else {
                    setting.notifications.remove(code);
                }

            }

        });

    });

}

function kirkiNotificationsWarning(setting, code, message) {

    setting.notifications.add(code, new wp.customize.Notification(
        code,
        {
            type: 'warning',
            message: message
        }
    ));

}


// jQuery(document).ready(function () {

function isConditionMet(activeCallback) {
    var show = null;
    switch (activeCallback.operator) {
        case "=":
        case "==":
            show = wp.customize(activeCallback.setting).get() == activeCallback.value;
            break;

        case"===":
            show = wp.customize(activeCallback.setting).get() === activeCallback.value;
            break;

        case "!=":
            show = wp.customize(activeCallback.setting).get() != activeCallback.value;
            break;

        case "!==":
            show = wp.customize(activeCallback.setting).get() !== activeCallback.value;
            break;

        case ">":
            show = wp.customize(activeCallback.setting).get() > activeCallback.value;
            break;

        case ">=":
            show = wp.customize(activeCallback.setting).get() >= activeCallback.value;
            break;

        case "<":
            show = wp.customize(activeCallback.setting).get() < activeCallback.value;
            break;

        case "<=":
            show = wp.customize(activeCallback.setting).get() <= activeCallback.value;
            break;

        case "in":
            show = activeCallback.value.indexOf(wp.customize(activeCallback.setting).get()) !== -1;
            break;

    }


    return show;
}

// var bindsAdded =

wp.customize.bind('pane-contents-reflowed', function () {

    jQuery.each(wp.customize.settings.controls, function (control, options) {

        if (options.active_callback) {

            var activeCallbacks = options.active_callback;
            var modifiedControl = control;

            var onChange = function (newValue, oldValue) {
                var control = wp.customize(modifiedControl).findControls()[0];

                for (var i = 0; i < activeCallbacks.length; i++) {
                    var ac = activeCallbacks[i];
                    var conditionMet = isConditionMet(ac);

                    // condition not met
                    if (conditionMet === false) {
                        // control.active.set(false);
                        control.deactivate();
                        return;

                    } else {

                        // condition undetermined
                        if (conditionMet === null) {
                            return;
                        }
                    }
                }

                // control.active.set(true);

                control.activate();
                // if (control.setting.transport === 'postMessage') {
                //     control.setting.callbacks.fireWith(control.setting, [control.setting.get(), control.params.default])
                // }
            };

            for (var i = 0; i < activeCallbacks.length; i++) {
                var ac = activeCallbacks[i];
                if (!_.isObject(ac)) {
                    continue;
                }
                wp.customize(ac['setting']).bind(onChange);
            }
        }
    });

});
// });
