<style type="text/css">
	.wpfc-csp-item:hover{
		background-color: #E5E5E5;
	}
	.wpfc-csp-item{
		float: left;
		width: 330.5px;
		margin-right: 7px;
		margin-left: 20px;
	    -moz-border-radius:5px 5px 5px 5px;
	    -webkit-border-radius:5px 5px 5px 5px;
	    border-radius:5px 5px 5px 5px;
	    border:1px solid transparent;
	    cursor:pointer;
	    padding:9px;
		outline:none !important;
		list-style: outside none none;
	}

	.wpfc-csp-item-form-title{
	    max-width:380px;
		font-weight:bold;
	    white-space:nowrap;
	    max-width:615px;
	    margin-bottom:3px;
	    text-overflow:ellipsis;
	    -o-text-overflow:ellipsis;
	    -moz-text-overflow:ellipsis;
	    -webkit-text-overflow:ellipsis;
	    line-height:1em;
	    font-family: Verdana,Geneva,Arial,Helvetica,sans-serif;
	}
	.wpfc-csp-item-details{
	    font-size:11px;
	    color:#888;
		display:block;
	    white-space:nowrap;
	    font-family: Verdana,Geneva,Arial,Helvetica,sans-serif;
	    line-height:1.5em;
	}
	.wpfc-csp-item-details b {
		display:inline;
		margin-left: 1px;

	}
	.wpfc-csp-item-right{
		margin-right: 0;
		margin-left: 0;
	}
</style>

<!-- item sample -->
<div class="wpfc-csp-item" tabindex="1" style="position: relative;display:none;">
	<div class="app">
		<div class="wpfc-csp-item-form-title">Specific Page - <span style="color: green;">When Updating or Posting</span></div>
		<span class="wpfc-csp-item-details wpfc-csp-item-url"></span>
	</div>
</div>
<!-- samples end -->


<div template-id="wpfc-modal-csp" style="display:none; top: 10.5px; left: 226px; position: absolute; padding: 6px; height: auto; width: 560px; z-index: 10001;">
	<div style="height: 100%; width: 100%; background: none repeat scroll 0% 0% rgb(0, 0, 0); position: absolute; top: 0px; left: 0px; z-index: -1; opacity: 0.5; border-radius: 8px;">
	</div>
	<div style="z-index: 600; border-radius: 3px;">
		<div style="font-family:Verdana,Geneva,Arial,Helvetica,sans-serif;font-size:12px;background: none repeat scroll 0px 0px rgb(255, 161, 0); z-index: 1000; position: relative; padding: 2px; border-bottom: 1px solid rgb(194, 122, 0); height: 35px; border-radius: 3px 3px 0px 0px;">
			<table width="100%" height="100%">
				<tbody>
					<tr>
						<td valign="middle" style="vertical-align: middle; font-weight: bold; color: rgb(255, 255, 255); text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.5); padding-left: 10px; font-size: 13px; cursor: move;">Clearing Specific Pages Settings</td>
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


				<div id="wpfc-wizard-csp" class="wpfc-cdn-pages-container">
					<div wpfc-cdn-page="1" class="wiz-cont">

						<h1>Enter a Url</h1>		
						<p>Please enter a url which you want cleared from cache whenever a post is created or updated.</p>
						<div class="wiz-input-cont">
							<input placeholder="<?php echo get_site_url('', 'sample-page'); ?>" type="text" name="url" class="api-key" style="width: 100%;">
					    	<label class="wiz-error-msg"></label>
					    </div>

					    <p class="wpfc-bottom-note" style="margin-bottom:-10px;"><a target="_blank" href="https://www.wpfastestcache.com/features/clear-cache-of-specific-urls-when-updating-or-posting/">Note: Please read this article to learn about this feature.</a></p>



					</div>


				</div>
			</div>
		</div>
		<?php include WPFC_MAIN_PATH."templates/buttons.html"; ?>
	</div>
</div>

<script type="text/javascript">
	var WpFcCsp = {
		init: function(){
			this.click_event_for_add_button();
			this.show_list();
		},
		show_list: function(){
			var self = this;

			jQuery.ajax({
				type: 'POST',
				dataType: "json",
				url: ajaxurl,
				data : {"action": "wpfc_get_list_csp", security: '<?php echo wp_create_nonce( "wpfc-save-csp-ajax-nonce" ); ?>'},
			    success: function(res){
			    	if(typeof res.success != "undefined" && res.success){

			    		jQuery.each(res.data, function( index, value ) {
			    			self.add_item(value);
			    		});
			    	}
			    },
			    error: function(e) {
			    	alert("unknown error");
			    }
			});

		},
		add_item: function(data){
			var self = this;
			var existing_item = jQuery("div.wpfc-csp-item[order='" + data.order + "']");

			if(existing_item.length == 1){
				existing_item.find("span.wpfc-csp-item-url").text(data.url);
			}else{
				var item = jQuery(".wpfc-csp-item").first().clone();

				item.find(".wpfc-csp-item-url").html(data.url);

				item.attr("url", data.url);
				item.attr("order", data.order);

				item.click(function(){
					self.open_modal(this);
				});

				item.show();

				if(jQuery(".wpfc-csp-list > div").length%2){
					item.addClass("wpfc-csp-item-right");
				}

				jQuery(".wpfc-csp-list").append(item);

				self.reorder();
			}

		},
		reorder: function(type){
			jQuery(".wpfc-csp-list > div").each(function(i, e){
				if(i%2 == 0){
					jQuery(e).removeClass("wpfc-csp-item-right");
				}else{
					jQuery(e).addClass("wpfc-csp-item-right");
				}
			});
		},
		open_modal: function(item){
			var self = this;


			Wpfc_New_Dialog.dialog("wpfc-modal-csp", {
				close: function(){

				},
				remove: function(e){
					let modal = jQuery(e).closest("div[id^='wpfc-modal-csp']");
					let modal_id = modal.attr("id");
					let order = modal.find("input[name='order']").val();

					Wpfc_New_Dialog.id = modal_id;
					Wpfc_New_Dialog.disable_button("remove");


					jQuery.ajax({
						type: 'POST',
						dataType: "json",
						url: ajaxurl,
						data : {"action": "wpfc_remove_csp", "order" : order, security: '<?php echo wp_create_nonce( "wpfc-save-csp-ajax-nonce" ); ?>'},
					    success: function(res){
					    	if(res.success === true){
					    		console.log(res);

					    		Wpfc_New_Dialog.id = modal_id;
					    		Wpfc_New_Dialog.enable_button("remove");

					    		jQuery(".wpfc-csp-list > .wpfc-csp-item[order='" + order + "']").remove();

					    		self.reorder();
					    		
					    		modal.remove();
					    	}
					    },
					    error: function(e) {
					    	alert("unknown error");
					    }
					});

				},
				finish: function(e){
					let modal = jQuery(e).closest("div[id^='wpfc-modal-csp']");
					let modal_id = modal.attr("id");
					let url = modal.find("input[name='url']").val();
					let order = modal.find("input[name='order']").val();
					let error_label = modal.find("label.wiz-error-msg");

					if(typeof order == "undefined"){
						if(jQuery(".wpfc-csp-list > .wpfc-csp-item:last-child").length == 1){
							order = parseInt(jQuery(".wpfc-csp-list > .wpfc-csp-item:last-child").attr("order"))+1;
						}else{
							order = 1;
						}
					}

					if(url.length == 0){
						error_label.text("Please enter a url");
						return;
					}

					error_label.text("");

					Wpfc_New_Dialog.id = modal_id;
					Wpfc_New_Dialog.disable_button("finish");

					jQuery.ajax({
						type: 'POST',
						dataType: "json",
						url: ajaxurl,
						data : {"action": "wpfc_save_csp", "url" : url, "order" : order, security: '<?php echo wp_create_nonce( "wpfc-save-csp-ajax-nonce" ); ?>'},
					    success: function(res){
					    	Wpfc_New_Dialog.id = modal_id;
					    	Wpfc_New_Dialog.enable_button("finish");

					    	if(res.success === true){
					    		self.add_item({"url" : url, "order" : order});

					    		modal.remove();
					    	}else{
					    		error_label.text(res.data);
					    	}
					    },
					    error: function(e) {
					    	alert("unknown error");
					    }
					});

				}
			}, function(dialog){
				let url = jQuery(item).attr("url");
				let order = jQuery(item).attr("order");

				if(typeof url != "undefined" && typeof order != "undefined"){
					Wpfc_New_Dialog.show_button("remove");
					jQuery("#" + dialog.id).find("input[name='url']").val(url);

					jQuery("<input type='hidden' name='order' value='" + order + "' > ").insertAfter(jQuery("#" + dialog.id).find("input[name='url']"));
				}

				Wpfc_New_Dialog.show_button("finish");

			});
		},
		click_event_for_add_button: function(){
			let self = this;

			jQuery(".wpfc-add-new-csp-button").click(function(){
				self.open_modal();

			});

		}
	}

	WpFcCsp.init();
</script>


