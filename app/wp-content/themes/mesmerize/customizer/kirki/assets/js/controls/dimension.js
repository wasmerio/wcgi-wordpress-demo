wp.customize.controlConstructor['kirki-dimension'] = wp.customize.Control.extend({

    // When we're finished loading continue processing
    ready: function () {

        'use strict';

        var control = this,
            value;

        // Notifications.
        kirkiNotifications(control.id, 'kirki-dimension', control.params.kirkiConfig);

        var onChange = _.debounce(function (event) {
            value = jQuery(event.target).val();
            control.setting.set(value);
        }, 600);
        // Save the value
        this.container.on('change keyup paste', 'input', onChange);

    }

});
