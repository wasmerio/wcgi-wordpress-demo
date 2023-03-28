<div template-id="wpfc-modal-cachepath" style="display:none;top: 10.5px; left: 226px; position: absolute; padding: 6px; height: auto; width: 560px; z-index: 9995;">
	<div style="height: 100%; width: 100%; background: none repeat scroll 0% 0% rgb(0, 0, 0); position: absolute; top: 0px; left: 0px; z-index: -1; opacity: 0.5; border-radius: 8px;">
	</div>
	<div style="z-index: 600; border-radius: 3px;">
		<div style="font-family:Verdana,Geneva,Arial,Helvetica,sans-serif;font-size:12px;background: none repeat scroll 0px 0px rgb(255, 161, 0); z-index: 1000; position: relative; padding: 2px; border-bottom: 1px solid rgb(194, 122, 0); height: 35px; border-radius: 3px 3px 0px 0px;">
			<table width="100%" height="100%">
				<tbody>
					<tr>
						<td valign="middle" style="vertical-align: middle; font-weight: bold; color: rgb(255, 255, 255); text-shadow: 0px 1px 1px rgba(0, 0, 0, 0.5); padding-left: 10px; font-size: 13px; cursor: move;">Cache Path Customization</td>
						<td width="20" align="center" style="vertical-align: middle;"></td>
						<td width="20" align="center" style="vertical-align: middle; font-family: Arial,Helvetica,sans-serif; color: rgb(170, 170, 170); cursor: default;">
							<div title="Close Window" class="close-wiz"></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="window-content-wrapper" style="padding: 15px;">
			<div class="window-content" style="z-index: 1000; height: auto; position: relative; display: inline-block; width: 100%;">
				<div class="wpfc-cdn-pages-container">
					<?php
						$wpfc_cache_path = get_option("WpFastestCachePathSettings");

						if(!is_array($wpfc_cache_path)){
							$wpfc_cache_path = array(
													"cachepath" => "cache",
													"optimizedpath" => "wpfc-minified"
												);
						}
					?>
					<div wpfc-cdn-page="1" class="wiz-cont">
						<h1>Cache Folder</h1>		
						<p>Hi!, You can specify a custom location for the <strong>cache path</strong> via this part. Please choose a folder and continue...</p>
						<div class="wiz-input-cont" style="text-align:center;">
							<label class="mc-input-label" style="margin-right: 5px;">
								<select disabled style="width:30%;" name="wpcontentpath">
									<option value="wp-content">wp-content</option>
								</select>
								<select style="width:65%;" name="cachepath">
									<?php
										echo '<option value="cache">cache</option>';

										foreach(glob($this->getWpContentDir()."/*", GLOB_ONLYDIR) as $f_key => $f_value){
											if(basename($f_value) == "cache"){
												continue;
											}

											if($wpfc_cache_path["cachepath"] == basename($f_value)){
												echo '<option selected="" value="'.basename($f_value).'">'.basename($f_value).'</option>';
											}else{
												echo '<option value="'.basename($f_value).'">'.basename($f_value).'</option>';
											}
											
										}

									?>
								</select>
							</label>
					    </div>
					    <p class="wpfc-bottom-note" style="margin-bottom:-10px;"><a target="_blank" href="https://www.maxcdn.com/one/tutorial/implementing-cdn-on-wordpress-with-wp-fastest-cache/">Note: Please read How to Integrate StackPath into WP Fastest Cache</a></p>
					</div>

					<div wpfc-cdn-page="2" class="wiz-cont" style="display:none">
						<h1>Optimized Sources Folder</h1>	
						<p>You can specify a custom location path for the <strong>optimized JS/CSS sources</strong> via this part.</p>
						<div class="wiz-input-cont" style="text-align:center;">
							<label class="mc-input-label" style="margin-right: 5px;">
								<select disabled name="disabled-cachepath" style="width:65%;"></select>
								<input type="text" name="optimizedpath" style="width: 30%;" value="<?php echo $wpfc_cache_path["optimizedpath"]; ?>">
							</label>
					    </div>
					    <p class="wpfc-bottom-note" style="margin-bottom:-10px;"><a target="_blank" href="https://www.maxcdn.com/one/tutorial/implementing-cdn-on-wordpress-with-wp-fastest-cache/">Note: Please read How to Integrate StackPath into WP Fastest Cache</a></p>
					</div>
				</div>
			</div>
		</div>
		<?php include WPFC_MAIN_PATH."templates/buttons.html"; ?>
	</div>
</div>

<script type="text/javascript">
	var WPFC_CACHE_PATH = {
		ajax_url: false,
		init: function(){
			jQuery("form.delete-line div.questionCon.right").click(function(){
					Wpfc_New_Dialog.dialog("wpfc-modal-cachepath", {
						close: function(){

						},
						next: "default",
						back: "default",
						finish: function(){
							let wpfc_dialog = jQuery("#" + Wpfc_New_Dialog.id);
							let cachepath = wpfc_dialog.find("select[name='cachepath']").val();
							let optimizedpath = wpfc_dialog.find("input[name='optimizedpath']").val();

							Wpfc_New_Dialog.disable_button("finish");

							jQuery.ajax({
								type: 'POST',
								url: ajaxurl,
								data: {"action" : "wpfc_cache_path_save_settings", "cachepath" : cachepath, "optimizedpath" : optimizedpath},
								dataType: "json",
								cache: false, 
								success: function(data){
									jQuery("div[template-id='wpfc-modal-cachepath']").find("select[name='cachepath'] option[value='" + cachepath + "']").attr("selected", true);
									jQuery("div[template-id='wpfc-modal-cachepath']").find("input[name='optimizedpath']").val(optimizedpath);

									Wpfc_New_Dialog.enable_button("finish");
									Wpfc_New_Dialog.clone.remove();
			                    },
			                    error: function(error){
			                    	alert("unknown error");
			                    }
			                });

							console.log(cachepath, optimizedpath, wpcontent);

						}
					}, function(dialog){
						var wpfc_dialog = jQuery("#" + Wpfc_New_Dialog.id);
						var wpcontent = wpfc_dialog.find("select[name='wpcontentpath']").val();

						wpfc_dialog.find("select[name='disabled-cachepath']").append("<option>" + (wpfc_dialog.find("select[name='cachepath']").val() == "cache" ? wpcontent + "/cache" : wpcontent + "/" + wpfc_dialog.find("select[name='cachepath']").val() + "/cache") + "</option>");

						wpfc_dialog.find("select[name='cachepath']").change(function(e){
							wpfc_dialog.find("select[name='disabled-cachepath']").find("option").remove();
							wpfc_dialog.find("select[name='disabled-cachepath']").append("<option>" + (this.value == "cache" ? wpcontent + "/cache" : wpcontent + "/" + this.value + "/cache") + "</option>");
						});

						wpfc_dialog.find("input[name='optimizedpath']").keypress(function(event){
							var keyChar = String.fromCharCode(event.which || event.keyCode);

							if(!keyChar.match(/[A-Za-z0-9]/)){
								return false;
							}
						});

						Wpfc_New_Dialog.show_page(1);
						Wpfc_New_Dialog.show_button("next");
					});
			});
		}
	};

	window.addEventListener('load', function(){
		jQuery(document).ready(function(){
			WPFC_CACHE_PATH.init();
		});
	});
</script>