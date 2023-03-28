(function (root, $) {

    function openFAManager(title, callback, single) {

        var frame = root.wp.media.cp.extendFrameWithFA(root.wp.media.view.MediaFrame.Select);
        var custom_uploader = new frame({
            title: title,
            button: {
                text: ficTexts.media_button_label
            },
            multiple: !single
        });
        root.wp.media.cp.FAFrame = custom_uploader;


        root.wp.media.cp.FAFrame.on('select', function () {
            var attachment = custom_uploader.state().get('selection').toJSON();
            root.wp.media.cp.FAFrame.content.mode('browse');
            callback(attachment);
        });
        root.wp.media.cp.FAFrame.on('close', function () {
            root.wp.media.cp.FAFrame.content.mode('browse');
            callback(false);
        });


        root.wp.media.cp.FAFrame.open();
        root.wp.media.cp.FAFrame.content.mode('cp_font_awesome');

        root.jQuery(custom_uploader.views.selector).parent().css({
            'z-index': '16000000'
        });

    }

    wp.customize.controlConstructor['font-awesome-icon-control'] = wp.customize.Control.extend({

        ready: function () {

            'use strict';

            var control = this;

            // Change the value
            this.container.on('click', 'i.fa , button', function () {
                openFAManager(ficTexts.media_title, function (response) {

                    if (!response) {
                        return;
                    }
                    var value = response[0].fa;

                    control.container.find('i.fa').attr('class', 'fa ' + value);
                    control.setting.set(value);

                }, false);
            });

        }

    });

})(window, jQuery);
