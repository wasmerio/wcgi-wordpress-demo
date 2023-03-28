(function ($) {

    function getHandler() {
        for (var id in wp.customHeader.handlers) {
            var handle = wp.customHeader.handlers[id];
            if (handle.settings) {
                return handle;
            }
        }
    }

    function resizeVideo(videoElement, animate) {
        var $videoElement = jQuery(videoElement);


        var size = mesmerize_video_background.getVideoRect();
        $videoElement.css({
            width: Math.round(size.width),
            "max-width": Math.round(size.width),
            height: Math.round(size.height),
            "opacity": 1,
            "left": size.left
        });

        if (animate === false) {
            return;
        }

    }

    window.addEventListener('resize', function () {
        var videoElement = document.querySelector('video#wp-custom-header-video') || document.querySelector('iframe#wp-custom-header-video');
        if (videoElement) {
            resizeVideo(videoElement);
            mesmerize_video_background.resizePoster()
        }
    });


    jQuery(function () {
        var videoElement = document.querySelector('video#wp-custom-header-video') || document.querySelector('iframe#wp-custom-header-video');
        if (videoElement) {
            resizeVideo(videoElement, false);
        }
    });

    __cpVideoElementFirstPlayed = false;

    document.addEventListener('wp-custom-header-video-loaded', function () {
        var videoElement = document.querySelector('video#wp-custom-header-video');

        if (videoElement) {
            resizeVideo(videoElement);
            return;
        }

        if(! document.querySelector('#wp-custom-header')){
            return
        }

        document.querySelector('#wp-custom-header').addEventListener('play', function () {
            var iframeVideo = document.querySelector('iframe#wp-custom-header-video');
            var videoElement = document.querySelector('video#wp-custom-header-video') || iframeVideo;

            if (videoElement && !__cpVideoElementFirstPlayed) {
                __cpVideoElementFirstPlayed = true;
                resizeVideo(videoElement);
            }

            var handler = getHandler();
            handler.play();

        });

    });


})(jQuery);

