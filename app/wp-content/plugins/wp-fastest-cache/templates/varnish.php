<div template-id="wpfc-modal-varnish" style="display:none; top: 10.5px; left: 226px; position: absolute; padding: 6px; height: auto; width: 560px; z-index: 10001;">
	<div style="height: 100%; width: 100%; background: none repeat scroll 0% 0% rgb(0, 0, 0); position: absolute; top: 0px; left: 0px; z-index: -1; opacity: 0.5; border-radius: 8px;">
	</div>
	<div style="z-index: 600; border-radius: 3px;">
		<div style="font-family:Verdana,Geneva,Arial,Helvetica,sans-serif;font-size:12px;background: none repeat scroll 0px 0px rgb(255, 161, 0); z-index: 1000; position: relative; padding: 2px; border-bottom: 1px solid rgb(194, 122, 0); height: 35px; border-radius: 3px 3px 0px 0px;">
			<table width="100%" height="100%">
				<tbody>
					<tr>
						<td valign="middle" style="vertical-align: middle; font-weight: bold; color: rgb(255, 255, 255); text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.5); padding-left: 10px; font-size: 13px; cursor: move;">Varnish Cache Settings</td>
						<td width="20" align="center" style="vertical-align: middle;"></td>
						<td width="20" align="center" style="vertical-align: middle; font-family: Arial,Helvetica,sans-serif; color: rgb(170, 170, 170); cursor: default;">
							<div title="Close Window" class="close-wiz"></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="window-content-wrapper" style="padding: 8px;">
			<div style="z-index: 1000; height: auto; position: relative; display: inline-block; width: 100%;" class="window-content">


				<div id="wpfc-wizard-varnish" class="wpfc-cdn-pages-container">
					<div wpfc-cdn-page="1" class="wiz-cont">

						<h1>Varnish Server</h1>		
						<p>Specify the IP address of your varnish instance. If you do not know the ip address, you can ask your hosting provider on what to set here.</p>
						<div class="wiz-input-cont">
							<?php
								$datas = get_option("WpFastestCacheVarnish");
								$ip = isset($datas["server"]) && $datas["server"] ? $datas["server"] : "127.0.0.1";
							?>
							<input value="<?php echo $ip; ?>" type="text" name="server" class="api-key" style="width: 100%;">
					    	<label class="wiz-error-msg"></label>
					    </div>

					    <p class="wpfc-bottom-note" style="margin-bottom:-10px;"><a target="_blank" href="https://www.wpfastestcache.com/features/clear-cache-of-specific-urls-when-updating-or-posting/">Note: Please read this article to learn about this feature.</a></p>



					</div>

					<img style="border-radius:100px;" class="wiz-bg-img" src="data:image/png;base64,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"/>

				</div>
			</div>
		</div>
		<?php include WPFC_MAIN_PATH."templates/buttons.html"; ?>
	</div>
</div>
<script type="text/javascript">
	var WpFcVarnish = {
		init: function(){
			this.click_event_for_add_button();
		},
		click_event_for_add_button: function(){
			let self = this;

			jQuery(".varnish-cache-list > div").click(function(){
				self.open_modal();

			});
		},
		open_modal: function(item){
			var self = this;

			Wpfc_New_Dialog.dialog("wpfc-modal-varnish", {
				close: function(e){
					let modal = jQuery(e).closest("div[id^='wpfc-modal-varnish']");
					let modal_id = modal.attr("id");

					modal.find("label.wiz-error-msg").text("");
					
					Wpfc_New_Dialog.id = modal_id;
					Wpfc_New_Dialog.disable_button("close");

					jQuery.ajax({
						type: 'POST',
						dataType: "json",
						url: ajaxurl,
						data : {"action": "wpfc_purgecache_varnish", security: '<?php echo wp_create_nonce( "wpfc-varnish-ajax-nonce" ); ?>'},
					    success: function(res){
					    	if(!res.success){
					    		modal.find("label.wiz-error-msg").text(res.message);
					    	}

					    	Wpfc_New_Dialog.enable_button("close");
					    },
					    error: function(e) {
					    	alert("unknown error");
					    }
					});

				},
				start: function(e){
					let modal = jQuery(e).closest("div[id^='wpfc-modal-varnish']");
					let modal_id = modal.attr("id");

					modal.find("label.wiz-error-msg").text("");

					Wpfc_New_Dialog.id = modal_id;
					Wpfc_New_Dialog.disable_button("start");

					jQuery.ajax({
						type: 'POST',
						dataType: "json",
						url: ajaxurl,
						data : {"action": "wpfc_start_varnish", security: '<?php echo wp_create_nonce( "wpfc-varnish-ajax-nonce" ); ?>'},
					    success: function(res){
					    	Wpfc_New_Dialog.enable_button("start");

					    	if(res.success === true){
					    		jQuery("div.varnish-cache-list").find("div.meta").removeClass("pause");

					    		Wpfc_New_Dialog.show_button("pause");
					    		Wpfc_New_Dialog.hide_button("start");
					    	}else{
					    		modal.find("label.wiz-error-msg").text(res.message);
					    	}
					    },
					    error: function(e) {
					    	alert("unknown error");
					    }
					});
				},
				pause: function(e){
					let modal = jQuery(e).closest("div[id^='wpfc-modal-varnish']");
					let modal_id = modal.attr("id");

					modal.find("label.wiz-error-msg").text("");

					Wpfc_New_Dialog.id = modal_id;
					Wpfc_New_Dialog.disable_button("pause");

					jQuery.ajax({
						type: 'POST',
						dataType: "json",
						url: ajaxurl,
						data : {"action": "wpfc_pause_varnish", security: '<?php echo wp_create_nonce( "wpfc-varnish-ajax-nonce" ); ?>'},
					    success: function(res){
					    	if(res.success === true){
					    		Wpfc_New_Dialog.enable_button("pause");

					    		jQuery("div.varnish-cache-list").find("div.meta").addClass("pause");

					    		Wpfc_New_Dialog.show_button("start");
					    		Wpfc_New_Dialog.hide_button("pause");
					    	}
					    },
					    error: function(e) {
					    	alert("unknown error");
					    }
					});
				},
				remove: function(e){
					let modal = jQuery(e).closest("div[id^='wpfc-modal-varnish']");
					let modal_id = modal.attr("id");

					Wpfc_New_Dialog.id = modal_id;
					Wpfc_New_Dialog.disable_button("remove");

					jQuery.ajax({
						type: 'POST',
						dataType: "json",
						url: ajaxurl,
						data : {"action": "wpfc_remove_varnish", security: '<?php echo wp_create_nonce( "wpfc-varnish-ajax-nonce" ); ?>'},
					    success: function(res){
					    	if(res.success === true){
					    		Wpfc_New_Dialog.enable_button("remove");

					    		jQuery("div.varnish-cache-list").find("div.meta").removeClass("isConnected");
					    		jQuery("div.varnish-cache-list").find("div.meta").removeClass("pause");
					    		
					    		modal.remove();
					    	}
					    },
					    error: function(e) {
					    	alert("unknown error");
					    }
					});

				},
				finish: function(e){
					let modal = jQuery(e).closest("div[id^='wpfc-modal-varnish']");
					let modal_id = modal.attr("id");
					let server = modal.find("input[name='server']").val();
					let error_label = modal.find("label.wiz-error-msg");

					if(server.length == 0){
						server = "127.0.0.1";
						modal.find("input[name='server']").val(server);
					}

					modal.find("label.wiz-error-msg").text("");

					Wpfc_New_Dialog.id = modal_id;
					Wpfc_New_Dialog.disable_button("finish");

					jQuery.ajax({
						type: 'POST',
						dataType: "json",
						url: ajaxurl,
						data : {"action": "wpfc_save_varnish", "server" : server, security: '<?php echo wp_create_nonce( "wpfc-varnish-ajax-nonce" ); ?>'},
					    success: function(res){
					    	Wpfc_New_Dialog.enable_button("finish");

					    	if(!res.success){
					    		modal.find("label.wiz-error-msg").text(res.message);
					    	}else{
						    	jQuery("div.varnish-cache-list").find("div.meta").addClass("isConnected");

						    	modal.remove();
					    	}
					    },
					    error: function(e) {
					    	alert("unknown error");
					    }
					});

				}
			}, function(dialog){
				if(jQuery("div.varnish-cache-list").find("div.meta.isConnected.pause").length == 1){
					Wpfc_New_Dialog.show_button("remove");
					Wpfc_New_Dialog.show_button("start");
					Wpfc_New_Dialog.show_button("close");
				}else if(jQuery("div.varnish-cache-list").find("div.meta.isConnected").length == 1){
					Wpfc_New_Dialog.show_button("remove");
					Wpfc_New_Dialog.show_button("pause");
					Wpfc_New_Dialog.show_button("close");
				}

				Wpfc_New_Dialog.clone.find("div.window-buttons-wrapper").find("button[action='close']").text("Purge Varnish Cache");
				Wpfc_New_Dialog.show_button("finish");
			});
		}
	}

	WpFcVarnish.init();
</script>
