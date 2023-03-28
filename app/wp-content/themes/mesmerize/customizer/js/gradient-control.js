(function (root, $) {
    wp.customize.controlConstructor['gradient-control'] = wp.customize.Control.extend({

        ready: function () {

            'use strict';

            var control = this;


            var val = this.getValue();

            this.container.on('click', 'button, .webgradient-icon-preview .webgradient', function () {
                Mesmerize.openMediaCustomFrame(
                    wp.media.cp.extendFrameWithWebGradients({
                        filter : function (icon) {
                            return icon.get("parsed") !== false;
                        }
                    }),
                    "cp_web_gradients",
                    mesmerize_customize_settings.l10n.selectGradient,
                    true,
                    function (attachement) {
                        if (attachement && attachement[0]) {
                            var toSet = attachement[0].parsed;
                            if (control.params.choices && control.params.choices['opacity']) {
                                toSet.colors = toSet.colors.map(function (colorData) {
                                    var _color = tinycolor(colorData.color);
                                    _color.setAlpha(control.params.choices['opacity']);
                                    colorData.color = _color.toRgbString();
                                    return colorData;
                                });
                            }
                            control.setValue(toSet);
                        }
                    }
                )
            });

        },

        getValue: function () {
            'use strict';

            // The setting is saved in JSON

            var value = [];

            if (_.isString(this.setting.get())) {
                value = JSON.parse(this.setting.get());
            } else {
                value = this.setting.get();
            }

            return value;
        },

        setValue: function (value, silent) {
            this.setting.set(JSON.stringify(value));
            this.update(value);
        },

        update: function(value) {
            this.container.find('.webgradient-icon-preview > div.webgradient').attr('style', "background:" + Mesmerize.Utils.getGradientString(value.colors, value.angle));
        }

    });

})(window, jQuery);
