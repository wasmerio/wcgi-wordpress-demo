if(window.attachEvent) {
    window.attachEvent('onload', wpfc_column_button_action);
} else {
    if(window.onload) {
        var curronload_1 = window.onload;
        var newonload_1 = function(evt) {
            curronload_1(evt);
            wpfc_column_button_action(evt);
        };
        window.onload = newonload_1;
    } else {
        window.onload = wpfc_column_button_action;
    }
}
function wpfc_column_button_action(){
	jQuery(document).ready(function(){
        jQuery("a[id^='wpfc-clear-cache-link']").click(function(e){
            var post_id = jQuery(e.target).attr("data-id");
            var nonce = jQuery(e.target).attr("data-nonce");

            jQuery("#wpfc-clear-cache-link-" + post_id).css('cursor', 'wait');

            jQuery.ajax({
                type: 'GET',
                url: ajaxurl,
                data : {"action": "wpfc_clear_cache_column", "id" : post_id, "nonce" : nonce},
                dataType : "json",
                cache: false, 
                success: function(data){
                    jQuery("#wpfc-clear-cache-link-" + post_id).css('cursor', 'pointer');

                    if(typeof data.success != "undefined" && data.success == true){
                        //
                    }else{
                        alert("Clear Cache Error");
                    }
                }
            });

            return false;
        });
	});
}