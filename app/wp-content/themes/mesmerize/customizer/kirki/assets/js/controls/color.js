kirki.kirkiGetColorPalette = function () {
    return [];
}

kirki.kirkiGetColorPicker = function ($input, callback, options) {

    // Change color

    if (jQuery.fn.spectrum) {
        var alpha = $input.data('alpha');
        var changeOnMove = $input.data('transport') === 'postMessage';

        function setColor(color) {
            if (jQuery(this).data('alpha')) {
                callback(color.toRgbString());
            } else {
                callback(color.toHexString());
            }
        }

        $input.off('change').on('change', function (event) {
            event.preventDefault();
            event.stopPropagation();
            setColor.call(this, jQuery(this).spectrum('get'));
        });

        $input.spectrum({
            instant: options ? options.instant : false,
            preferredFormat: alpha ? "rgb" : "hex",
            showInput: true,
            showPalette: true,
            color: $input[0].getAttribute('value'),
            hideAfterPaletteSelect: false,
            showSelectionPalette: false,
            showAlpha: alpha,
            change: setColor,
            hide: setColor,
            move: (changeOnMove ? setColor : function () {

            }),

            beforeShow: function () {
                $input.spectrum("option", "palette", kirki.kirkiGetColorPalette());
            }
        });

    } else {
        $input.wpColorPicker(options);
        $input.wpColorPicker({
            change: function (event, ui) {
                // Small hack: the picker needs a small delay
                callback(ui.color.toCSS());
            }
        });
    }
};

wp.customize.controlConstructor['kirki-color'] = wp.customize.Control.extend({

    // When we're finished loading continue processing
    ready: function () {

        'use strict';

        var control = this,
            picker = this.container.find('.kirki-color-control');


        picker.data('transport', control.setting.transport);

        // If we have defined any extra choices, make sure they are passed-on to Iris.
        if (undefined !== control.params.choices) {

            kirki.kirkiGetColorPicker(picker, function (value) {
                setTimeout(function () {
                    control.setting.set(value);
                }, 100);
            }, control.params.choices);
        }
    }

});
