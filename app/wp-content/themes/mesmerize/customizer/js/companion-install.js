(function () {

    function currentFrameAbsolutePosition(currentWindow) {
        var currentParentWindow;
        var positions = [];
        var rect;

        while (currentWindow !== window.top) {
            currentParentWindow = currentWindow.parent;
            for (var idx = 0; idx < currentParentWindow.frames.length; idx++)
                if (currentParentWindow.frames[idx] === currentWindow) {
                    var iframes = currentParentWindow.document.getElementsByTagName('iframe');
                    for (var iframeIdx = 0; iframeIdx < iframes.length; iframeIdx++) {
                        var frameElement = iframes[iframeIdx];
                        if (frameElement.contentWindow === currentWindow) {
                            rect = frameElement.getBoundingClientRect();
                            positions.push({
                                x: rect.x,
                                y: rect.y
                            });
                        }
                    }
                    currentWindow = currentParentWindow;
                    break;
                }
        }
        return positions.reduce(function (accumulator, currentValue) {
            return {
                x: accumulator.x + currentValue.x,
                y: accumulator.y + currentValue.y
            };
        }, {
            x: 0,
            y: 0
        });
    }

    var popover = null;
    var $ = jQuery;
    var popoverTriggerSelector = [];

    var updatePopoverPosition = function () {
        var linkedTo = popover.data('linkedTo');
        var rect = linkedTo.getBoundingClientRect();
        var framePosition = currentFrameAbsolutePosition(linkedTo.ownerDocument.defaultView);

        var style = {
            top: (rect.top + framePosition.y + linkedTo.offsetHeight / 2 - popover.height() / 2) + "px",
            left: (rect.left + framePosition.x + linkedTo.offsetWidth + 10) + "px",
        };

        if (popover.hasClass('arrow-down')) {
            style = {
                top: (rect.top + framePosition.y - 150) + "px",
                left: (rect.left + framePosition.x + linkedTo.offsetWidth / 2 - 160) + "px",
            };
        }

        popover.css(style);
    };


    var hidePopover = function () {
        popover.fadeOut();
    };

    var showPopover = function (linkedTo, position) {
        if (!popover) {
            popover = jQuery("#extend-themes-companion-popover");
            jQuery('body').append(popover);
            popover.find('.extend-themes-companion-popover-close').click(function () {
                popover.fadeOut();
            });
        }

        position = position || 'left';

        popover.data('linkedTo', linkedTo);
        popover.removeClass('arrow-left arrow-down').addClass('arrow-' + position);
        updatePopoverPosition();
        popover.fadeIn();
        var currentWindow = linkedTo.ownerDocument.defaultView;
        var currentDocument = linkedTo.ownerDocument;

        $(currentWindow).on('resize.extend-themes-companion-popover', updatePopoverPosition);
        $(currentDocument).find('body .wp-full-overlay-sidebar-content').on('scroll.extend-themes-companion-popover', updatePopoverPosition);
        $(currentWindow).on('scroll.extend-themes-companion-popover', updatePopoverPosition);

        $(currentDocument).on('click.extend-themes-companion-popover', '*', function (event) {
            var target = event.currentTarget;
            event.stopPropagation();

            if ($(target).is(popoverTriggerSelector.join(',')) || $(target).is(popover.data('linkedTo'))) {
                event.preventDefault();

                return;
            }

            if (!popover.is(target)) {
                if (!$.contains(popover[0], target)) {
                    popover.fadeOut();
                    $(currentWindow).off('resize.extend-themes-companion-popover');
                    $(currentWindow).off('scroll.extend-themes-companion-popover');
                    $('body .wp-full-overlay-sidebar-content').off('scroll.extend-themes-companion-popover');
                    $(currentDocument).off('click.extend-themes-companion-popover');
                }
            }

        });

    };

    function initPopover() {
        $('body').on('click', popoverTriggerSelector.join(','), function (event) {
            showPopover(event.currentTarget);
        });
    }


    popoverTriggerSelector = ['.cp-add-section'];
    initPopover();


    window.ExtendThemesCompanionPopover = {
        showPopover: showPopover,
        updatePopoverPosition: updatePopoverPosition,
        hidePopover: hidePopover
    };

})();
