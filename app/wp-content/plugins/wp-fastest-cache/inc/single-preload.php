<?php
	class SinglePreloadWPFC{
		public static $id = 0;
		public static $urls = array();

		public static function init(){
			SinglePreloadWPFC::set_id();
			SinglePreloadWPFC::set_urls();
			SinglePreloadWPFC::set_urls_with_terms();
		}

	    public static function add_meta_box(){
	          add_meta_box( 
	             'auto_cache_custom_meta_box', // this is HTML id
	             'Auto Cache Settings', 
	             array("SinglePreloadWPFC", "auto_cache_custom_box_html"), // the callback function
	             array('page', 'post', 'product'),
	             'side',
	             'high'
	          );
	    }

	    public static function auto_cache_custom_box_html(){
	    	$yes_selected = "";
	    	$no_selected = "";
	    	
	        if($data = get_option("WpFastestCache_autocache")){
	            if($data == "yes"){
	                $yes_selected = "selected";
	            }else if($data == "no"){
	                $no_selected = "selected";
	            }
	        }else{
	            $no_selected = "selected";
	        }

	      ?>
	        <p>
	            <label>Enable:</label>
	            <select data-type='auto-cache-enable'>
	                <option <?php echo $yes_selected; ?> value="yes">Yes</option>
	                <option <?php echo $no_selected; ?> value="no">No</option>
	            </select>
	            <span class="spinner" style="float: none;display: inline-block;margin:0;"></span>
	        </p>

	        <p id="wpfc-single-preload-process" style="display: none;">
	        	<label>Status: <span id="wpfc-single-preload-status-runnig">Running</span><span id="wpfc-single-preload-status-completed" style="display: none; color: #33CD32; font-weight: bold;">Completed</span></label><br>
		        <label>Total: <span id="wpfc-single-preload-total-number"><?php echo count(self::$urls); ?></span></label><br>
		        <label>Cached: <span id="wpfc-single-preload-cached-number">0</span></label><br>
		        <label>Errors: <span id="wpfc-single-preload-error-number">0</span></label>
	        </p>



	            <p class="post-attributes-help-text">The cache will be created automatically after the contents are saved. <a href="https://www.wpfastestcache.com/features/automatic-cache/" target="_blank">More Info</a></p>

	            <script type="text/javascript">
	              var Wpfc_Single_Preload_save_settings = function(){
	                var enable_form = jQuery("#auto_cache_custom_meta_box select[data-type='auto-cache-enable']");

	                if(enable_form.length == 1){
	                  enable_form.change(function(){
	                    enable_form.attr("disabled", true);
	                    jQuery("#auto_cache_custom_meta_box span.spinner").css({"visibility" : "visible"});

	                    jQuery.ajax({
	                      type: 'POST',
	                      url: ajaxurl,
	                      data: {"action": "wpfc_preload_single_save_settings", "is_enable": jQuery(this).val()},
	                      dataType: "json",
	                      cache: false, 
	                      success: function(data){
	                        enable_form.attr("disabled", false);
	                        jQuery("#auto_cache_custom_meta_box span.spinner").css({"visibility" : "hidden"});

	                        console.log(data);
	                      },
	                      error: function(error){
	                        enable_form.attr("disabled", false);
	                        jQuery("#auto_cache_custom_meta_box span.spinner").css({"visibility" : "hidden"});
	                        
	                        console.log(error.statusText);
	                      }
	                    });

	                  });
	                }
	              };

	              Wpfc_Single_Preload_save_settings();
	       
	        </script>

	        <?php  
	        // Add the HTML for the post meta
	    }

	    public static function save_settings(){
	        if(current_user_can('manage_options')){
	            $res = array("success" => true);

	            if(get_option("WpFastestCache_autocache")){
	                update_option("WpFastestCache_autocache", sanitize_text_field($_POST["is_enable"]));
	            }else{
	                add_option("WpFastestCache_autocache", sanitize_text_field($_POST["is_enable"]), null, "yes");
	            }

	            wp_send_json($res);
	        }

	        wp_die("Must be admin");
	    }

		public static function set_id(){
			if(isset($_GET["post"]) && $_GET["post"]){
				
				static::$id = (int) $_GET["post"];

				if(get_post_status(static::$id) != "publish"){
					static::$id = 0;
				}
			}
		}

		public static function create_cache(){
			$res = $GLOBALS["wp_fastest_cache"]->wpfc_remote_get($_GET["url"], $_GET["user_agent"]);

			if($res){
				die("true");
			}
		}

		public static function is_mobile_active(){
			if(isset($GLOBALS["wp_fastest_cache_options"]->wpFastestCacheMobile) && isset($GLOBALS["wp_fastest_cache_options"]->wpFastestCacheMobileTheme)){
				return true;
			}else{
				return false;
			}
		}

		public static function set_term_urls($term_taxonomy_id){
			$term = get_term_by("term_taxonomy_id", $term_taxonomy_id);

			if($term && !is_wp_error($term)){
				$url = get_term_link($term->term_id, $term->taxonomy);

				array_push(static::$urls, array("url" => $url, "user-agent" => "WP Fastest Cache Preload Bot"));

				if(self::is_mobile_active()){
					array_push(static::$urls, array("url" => $url, "user-agent" => "WP Fastest Cache Preload iPhone Mobile Bot"));
				}

				if($term->parent > 0){
					$parent = get_term_by("id", $term->parent, $term->taxonomy);

					static::set_term_urls($parent->term_taxonomy_id);
				}
			}
		}

		public static function set_urls_with_terms(){
			global $wpdb;
			$terms = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."term_relationships` WHERE `object_id`=".static::$id, ARRAY_A);

			foreach ($terms as $term_key => $term_val){
				static::set_term_urls($term_val["term_taxonomy_id"]);
			}
		}

		public static function set_urls(){
			if(static::$id){
				$permalink = get_permalink(static::$id);

				array_push(static::$urls, array("url" => $permalink, "user-agent" => "WP Fastest Cache Preload Bot"));

				if(self::is_mobile_active()){
					array_push(static::$urls, array("url" => $permalink, "user-agent" => "WP Fastest Cache Preload iPhone Mobile Bot"));
				}
			}
		}

		public static function put_inline_js(){
			if($data = get_option("WpFastestCache_autocache")){
				if($data == "no"){
					return false;
				}
			}else{
				return false;
			}
		?>

	        <script type="text/javascript">
	        	var WpfcSinglePreload = {
	        		error_message: "",
	        		init: function(){
	        			jQuery("#wpfc-single-preload-process").show("slow")
	        		},
	        		change_status: function(){
	        			var error_number = jQuery("#wpfc-single-preload-error-number").text();
	        			var cached_number = jQuery("#wpfc-single-preload-cached-number").text();
	        			var total_number = jQuery("#wpfc-single-preload-total-number").text();

	        			error_number = parseInt(error_number);
	        			cached_number = parseInt(cached_number);
	        			total_number = parseInt(total_number);

	        			if(total_number == (cached_number + error_number)){
	        				jQuery("#wpfc-single-preload-status-completed").show();
	        				jQuery("#wpfc-single-preload-status-runnig").hide();
	        			}	
	        		},
	        		increase_error: function(){
	        			var number = jQuery("#wpfc-single-preload-error-number").text();
	        			number = parseInt(number) + 1;

	        			jQuery("#wpfc-single-preload-error-number").text(number);

	        			WpfcSinglePreload.change_status();
	        		},
	        		increase_cached: function(){
	        			var number = jQuery("#wpfc-single-preload-cached-number").text();
	        			number = parseInt(number) + 1;

	        			jQuery("#wpfc-single-preload-cached-number").text(number);

	        			WpfcSinglePreload.change_status();
	        		},
	        		running_animation: function(){
	        			let label = jQuery("#wpfc-single-preload-status-runnig");
						let text = label.text();
						let dot = 0;

						label.text(text + ".");

						self.interval = setInterval(function(){
							text = label.text();
							dot = text.match(/\./g);

							if(dot){
								if(dot.length < 3){
									label.text(text + ".");
								}else{
									label.text(text.replace(/\.+$/, ""));
								}
							}else{
								label.text(text + ".");
							}
						}, 300);
	        		},
	        		create_cache: function(list){
	        			var action = function(url, user_agent, list){
		        			var self = this;
		        			jQuery("#wpfc-single-preload").show();

			        		jQuery.ajax({
								type: 'GET',
								url: ajaxurl,
								data: {"action": "wpfc_preload_single", "url": url, "user_agent": user_agent},
								dataType: "html",
								timeout: 10000,
								cache: false, 
								success: function(data){
									if(data == "true"){
										WpfcSinglePreload.increase_cached();
									}else{
										self.error_message = data;
										WpfcSinglePreload.increase_error();
									}

									if(typeof list == "object"){
										WpfcSinglePreload.create_cache(list);
									}
								},
								error: function(error){
									self.error_message = error.statusText;
									WpfcSinglePreload.increase_error();

									if(typeof list == "object"){
										WpfcSinglePreload.create_cache(list);
									}
								}
							});

	        			};

	        			let number_to_used = 3;
	        			let sliced = list.slice(0, number_to_used);

	        			list.splice(0, number_to_used);

	        			jQuery.each(sliced, function( index, value ) {
	        				console.log( value["url"] );

	        				if(index == (number_to_used-1)){
	        					setTimeout(function(){
		        					action(value["url"], value["user-agent"], list);
		        				}, 500);
	        				}else{
		        				setTimeout(function(){
		        					action(value["url"], value["user-agent"], false);
		        				}, 500);
	        				}
	        			});
	        		}
	        	};


	        	jQuery(document).ready(function(){
	        		if(!jQuery("#auto_cache_custom_meta_box").is(":visible")){
	        			return;
	        		}
	        		
	        		if(jQuery("#message").find("a").attr("href")){
			        	WpfcSinglePreload.init();
			        	WpfcSinglePreload.running_animation();
			        	WpfcSinglePreload.create_cache(<?php echo json_encode(self::$urls); ?>);
	        		}
	        	});
	        </script>

	        <?php
		}
	}
?>