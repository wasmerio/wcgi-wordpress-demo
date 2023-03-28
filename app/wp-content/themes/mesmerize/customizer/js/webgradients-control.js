wp.customize.controlConstructor['web-gradients'] = wp.customize.Control.extend({

    ready: function () {

        'use strict';

        var control = this;

        // Change the value
        this.container.on('click', 'button, .webgradient-icon-preview .webgradient', function () {

            Mesmerize.openMediaCustomFrame(
                wp.media.cp.extendFrameWithWebGradients(),
                "cp_web_gradients",
                mesmerize_customize_settings.l10n.selectGradient,
                true,
                function (attachement) {

                    if (attachement && attachement[0]) {
                        control.setting.set(attachement[0].gradient);
                        control.container.find('.webgradient-icon-preview > div.webgradient').attr('class', 'webgradient ' + attachement[0].gradient);
                        control.container.find('.webgradient-icon-preview > div.webgradient + .label').text(attachement[0].gradient.replace(/_/ig, ' '));
                    }
                }
            )
        });

    }

});
