<?php
/*
Plugin Name: WP Fastest Cache
Plugin URI: http://wordpress.org/plugins/wp-fastest-cache/
Description: The simplest and fastest WP Cache system
Version: 1.1.0
Author: Emre Vona
Author URI: https://www.wpfastestcache.com/
Text Domain: wp-fastest-cache
Domain Path: /languages/

Copyright (C)2013 Emre Vona

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

	if (!defined('WPFC_WP_CONTENT_BASENAME')) {
		if (!defined('WPFC_WP_PLUGIN_DIR')) {
			if(preg_match("/(\/trunk\/|\/wp-fastest-cache\/)$/", plugin_dir_path( __FILE__ ))){
				define("WPFC_WP_PLUGIN_DIR", preg_replace("/(\/trunk\/|\/wp-fastest-cache\/)$/", "", plugin_dir_path( __FILE__ )));
			}else if(preg_match("/\\\wp-fastest-cache\/$/", plugin_dir_path( __FILE__ ))){
				//D:\hosting\LINEapp\public_html\wp-content\plugins\wp-fastest-cache/
				define("WPFC_WP_PLUGIN_DIR", preg_replace("/\\\wp-fastest-cache\/$/", "", plugin_dir_path( __FILE__ )));
			}
		}
		define("WPFC_WP_CONTENT_DIR", dirname(WPFC_WP_PLUGIN_DIR));
		define("WPFC_WP_CONTENT_BASENAME", basename(WPFC_WP_CONTENT_DIR));
	}

	if (!defined('WPFC_MAIN_PATH')) {
		define("WPFC_MAIN_PATH", plugin_dir_path( __FILE__ ));
	}

	if(!isset($GLOBALS["wp_fastest_cache_options"])){
		if($wp_fastest_cache_options = get_option("WpFastestCache")){
			$GLOBALS["wp_fastest_cache_options"] = json_decode($wp_fastest_cache_options);
		}else{
			$GLOBALS["wp_fastest_cache_options"] = array();
		}
	}

	function wpfastestcache_activate(){
		if($options = get_option("WpFastestCache")){
			$post = json_decode($options, true);

			include_once('inc/admin.php');
			$wpfc = new WpFastestCacheAdmin();
			$wpfc->modifyHtaccess($post);
		}
	}

	function wpfastestcache_deactivate(){
		$wpfc = new WpFastestCache();

		$path = ABSPATH;
		
		if($wpfc->is_subdirectory_install()){
			$path = $wpfc->getABSPATH();
		}

		if(is_file($path.".htaccess") && is_writable($path.".htaccess")){
			$htaccess = file_get_contents($path.".htaccess");
			$htaccess = preg_replace("/#\s?BEGIN\s?WpFastestCache.*?#\s?END\s?WpFastestCache/s", "", $htaccess);
			$htaccess = preg_replace("/#\s?BEGIN\s?GzipWpFastestCache.*?#\s?END\s?GzipWpFastestCache/s", "", $htaccess);
			$htaccess = preg_replace("/#\s?BEGIN\s?LBCWpFastestCache.*?#\s?END\s?LBCWpFastestCache/s", "", $htaccess);
			$htaccess = preg_replace("/#\s?BEGIN\s?WEBPWpFastestCache.*?#\s?END\s?WEBPWpFastestCache/s", "", $htaccess);
			@file_put_contents($path.".htaccess", $htaccess);
		}

		$wpfc->deleteCache();
	}

	register_activation_hook( __FILE__, "wpfastestcache_activate");
	register_deactivation_hook( __FILE__, "wpfastestcache_deactivate");

	class WpFastestCache{
		private $systemMessage = "";
		private $options = array();
		public $noscript = "";
		public $content_url = "";
		public $deleted_before = false;

		public function __construct(){
			$this->set_content_url();
			
			$optimize_image_ajax_requests = array("wpfc_revert_image_ajax_request", 
												  "wpfc_statics_ajax_request",
												  "wpfc_optimize_image_ajax_request",
												  "wpfc_update_image_list_ajax_request"
												  );

			add_action('wp_ajax_wpfc_delete_cache', array($this, "deleteCacheToolbar"));
			add_action('wp_ajax_wpfc_delete_cache_and_minified', array($this, "deleteCssAndJsCacheToolbar"));
			add_action('wp_ajax_wpfc_delete_current_page_cache', array($this, "delete_current_page_cache"));

			add_action('wp_ajax_wpfc_clear_cache_of_allsites', array($this, "wpfc_clear_cache_of_allsites_callback"));

			add_action('wp_ajax_wpfc_toolbar_save_settings', array($this, "wpfc_toolbar_save_settings_callback"));
			add_action('wp_ajax_wpfc_toolbar_get_settings', array($this, "wpfc_toolbar_get_settings_callback"));

			//add_action('wp_ajax_wpfc_cache_path_save_settings', array($this, "wpfc_cache_path_save_settings_callback"));

			add_action( 'wp_ajax_wpfc_save_timeout_pages', array($this, 'wpfc_save_timeout_pages_callback'));
			add_action( 'wp_ajax_wpfc_save_exclude_pages', array($this, 'wpfc_save_exclude_pages_callback'));
			add_action( 'wp_ajax_wpfc_cdn_options', array($this, 'wpfc_cdn_options_ajax_request_callback'));
			add_action( 'wp_ajax_wpfc_remove_cdn_integration', array($this, 'wpfc_remove_cdn_integration_ajax_request_callback'));
			add_action( 'wp_ajax_wpfc_pause_cdn_integration', array($this, 'wpfc_pause_cdn_integration_ajax_request_callback'));
			add_action( 'wp_ajax_wpfc_start_cdn_integration', array($this, 'wpfc_start_cdn_integration_ajax_request_callback'));
			add_action( 'wp_ajax_wpfc_save_cdn_integration', array($this, 'wpfc_save_cdn_integration_ajax_request_callback'));
			add_action( 'wp_ajax_wpfc_cdn_template', array($this, 'wpfc_cdn_template_ajax_request_callback'));
			add_action( 'wp_ajax_wpfc_check_url', array($this, 'wpfc_check_url_ajax_request_callback'));
			add_action( 'wp_ajax_wpfc_cache_statics_get', array($this, 'wpfc_cache_statics_get_callback'));
			add_action( 'wp_ajax_wpfc_db_statics', array($this, 'wpfc_db_statics_callback'));
			add_action( 'wp_ajax_wpfc_db_fix', array($this, 'wpfc_db_fix_callback'));
			add_action( 'rate_post', array($this, 'wp_postratings_clear_fastest_cache'), 10, 2);
			add_action( 'user_register', array($this, 'modify_htaccess_for_new_user'), 10, 1);
			add_action( 'profile_update', array($this, 'modify_htaccess_for_new_user'), 10, 1);
			add_action( 'edit_terms', array($this, 'delete_cache_of_term'), 10, 1);

			add_action( 'wp_ajax_wpfc_save_csp', array($this, 'wpfc_save_csp_callback'));
			add_action( 'wp_ajax_wpfc_remove_csp', array($this, 'wpfc_remove_csp_callback'));
			add_action( 'wp_ajax_wpfc_get_list_csp', array($this, 'wpfc_get_list_csp_callback'));


			add_action( 'wp_ajax_wpfc_save_varnish', array($this, 'wpfc_save_varnish_callback'));
			add_action( 'wp_ajax_wpfc_remove_varnish', array($this, 'wpfc_remove_varnish_callback'));
			add_action( 'wp_ajax_wpfc_pause_varnish', array($this, 'wpfc_pause_varnish_callback'));
			add_action( 'wp_ajax_wpfc_start_varnish', array($this, 'wpfc_start_varnish_callback'));
			add_action( 'wp_ajax_wpfc_purgecache_varnish', array($this, 'wpfc_purgecache_varnish_callback'));


			if(defined("WPFC_CLEAR_CACHE_AFTER_SWITCH_THEME") && WPFC_CLEAR_CACHE_AFTER_SWITCH_THEME){
				add_action('after_switch_theme', array($this, 'clear_cache_after_switch_theme'));
			}

			if(defined("WPFC_CLEAR_CACHE_AFTER_ACTIVATE_DEACTIVATE_PLUGIN") && WPFC_CLEAR_CACHE_AFTER_ACTIVATE_DEACTIVATE_PLUGIN){
				add_action('activate_plugin', array($this, 'clear_cache_after_activate_plugin'));
				add_action('deactivate_plugin', array($this, 'clear_cache_after_deactivate_plugin'));
			}


			add_action('upgrader_process_complete', array($this, 'clear_cache_after_update_plugin'), 10, 2);
			add_action('upgrader_process_complete', array($this, 'clear_cache_after_update_theme'), 10, 2);


			if(defined("WPFC_DISABLE_CLEARING_CACHE_AFTER_WOOCOMMERCE_CHECKOUT_ORDER_PROCESSED") && WPFC_DISABLE_CLEARING_CACHE_AFTER_WOOCOMMERCE_CHECKOUT_ORDER_PROCESSED){
			}else if(defined("WPFC_DISABLE_CLEARING_CACHE_AFTER_WOOCOMMERCE_ORDER_STATUS_CHANGED") && WPFC_DISABLE_CLEARING_CACHE_AFTER_WOOCOMMERCE_ORDER_STATUS_CHANGED){
			}else{
				// to clear cache after new Woocommerce orders
				add_action('woocommerce_order_status_changed', array($this, 'clear_cache_after_woocommerce_order_status_changed'), 1, 1);
			}


			// kk Star Ratings: to clear the cache of the post after voting
			add_action('kksr_rate', array($this, 'clear_cache_on_kksr_rate'));

			// Elementor: to clear cache after Maintenance Mode activation/deactivation
			add_action('elementor/maintenance_mode/mode_changed', array($this, 'deleteCache'));

			// clearing cache hooks
			add_action("wpfc_clear_all_cache", array($this, 'deleteCache'), 10, 1);
			add_action("wpfc_clear_all_site_cache", array($this, 'wpfc_clear_cache_of_allsites_callback'));
			add_action("wpfc_clear_post_cache_by_id", array($this, 'singleDeleteCache'), 10, 2);

			// to enable Auto Cache Panel for the classic editor
			add_action( 'admin_init', array($this, 'enable_auto_cache_settings_panel'));

			// to add settings link
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array($this, 'action_links'));

			// to clear cache after ajax request by other plugins
			if(isset($_POST["action"])){
				// All In One Schema.org Rich Snippets
				if(preg_match("/bsf_(update|submit)_rating/i", $_POST["action"])){
					if(isset($_POST["post_id"])){
						$this->singleDeleteCache(false, $_POST["post_id"]);
					}
				}

				// Yet Another Stars Rating
				if($_POST["action"] == "yasr_send_visitor_rating"){
					if(isset($_POST["post_id"])){
						// to need call like that because get_permalink() does not work if we call singleDeleteCache() directly
						add_action('init', array($this, "singleDeleteCache"));
					}
				}
			}

			// to clear /tmpWpfc folder
			if(is_dir($this->getWpContentDir("/cache/tmpWpfc"))){
				$this->rm_folder_recursively($this->getWpContentDir("/cache/tmpWpfc"));
			}


			if($this->isPluginActive('wp-polls/wp-polls.php')){
					//for WP-Polls 
					require_once "inc/wp-polls.php";
					$wp_polls = new WpPollsForWpFc();
					$wp_polls->hook();
			}

			if(isset($_GET) && isset($_GET["action"]) && in_array($_GET["action"], $optimize_image_ajax_requests)){
				if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
					include_once $this->get_premium_path("image.php");
					$img = new WpFastestCacheImageOptimisation();
					$img->hook();
				}
			}else if(isset($_GET) && isset($_GET["action"])  && $_GET["action"] == "wpfastestcache"){
				if(isset($_GET) && isset($_GET["type"])  && $_GET["type"] == "preload"){
					// /?action=wpfastestcache&type=preload
					
					add_action('init', array($this, "create_preload_cache"), 11);
				}

				if(isset($_GET) && isset($_GET["type"]) && preg_match("/^clearcache(andminified|allsites)*$/i", $_GET["type"])){
					// /?action=wpfastestcache&type=clearcache&token=123
					// /?action=wpfastestcache&type=clearcacheandminified&token=123

					if(isset($_GET["token"]) && $_GET["token"]){
						if(defined("WPFC_CLEAR_CACHE_URL_TOKEN") && WPFC_CLEAR_CACHE_URL_TOKEN){
							if(WPFC_CLEAR_CACHE_URL_TOKEN == $_GET["token"]){
								if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
									include_once $this->get_premium_path("mobile-cache.php");
								}

								if($_GET["type"] == "clearcache"){
									$this->deleteCache();
								}

								if($_GET["type"] == "clearcacheandminified"){
									$this->deleteCache(true);
								}

								if($_GET["type"] == "clearcacheallsites"){
									$this->wpfc_clear_cache_of_allsites_callback();
								}

								die("Done");
							}else{
								die("Wrong token");
							}
						}else{
							die("WPFC_CLEAR_CACHE_URL_TOKEN must be defined");
						}
					}else{
						die("Security token must be set.");
					}
				}
			}else{
				$this->setCustomInterval();

				$this->options = $this->getOptions();

				add_action('transition_post_status',  array($this, 'on_all_status_transitions'), 10, 3 );
				
				// when the regular price is updated, the "transition_post_status" action cannot catch it
				add_action('woocommerce_update_product',  array($this, 'clear_cache_after_woocommerce_update_product'), 10, 1);

				$this->commentHooks();

				$this->checkCronTime();

				if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
					include_once $this->get_premium_path("mobile-cache.php");

					if(file_exists(WPFC_WP_PLUGIN_DIR."/wp-fastest-cache-premium/pro/library/statics.php")){
						include_once $this->get_premium_path("statics.php");
					}

					if(!defined('DOING_AJAX')){
						include_once $this->get_premium_path("powerful-html.php");
					}
				}

				if(is_admin()){
					add_action('wp_loaded', array($this, "load_column"));
					
					if(defined('DOING_AJAX') && DOING_AJAX){
						//do nothing
					}else{
						// to avoid loading menu and optionPage() twice
						if(!class_exists("WpFastestCacheAdmin")){
							//for wp-panel
							
							if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
								include_once $this->get_premium_path("image.php");
							}

							if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
								include_once $this->get_premium_path("logs.php");
							}

							add_action('plugins_loaded', array($this, 'wpfc_load_plugin_textdomain'));
							add_action('wp_loaded', array($this, "load_admin_toolbar"));

							$this->admin();
						}
					}
				}else{
					if(preg_match("/\/([^\/]+)\/([^\/]+(\.css|\.js))(\?.+)?$/", $this->current_url(), $path)){
						// for security
						if(preg_match("/\.{2,}/", $this->current_url())){
							die("May be Directory Traversal Attack");
						}

						if(is_dir($this->getWpContentDir("/cache/wpfc-minified/").$path[1])){
							if($sources = @scandir($this->getWpContentDir("/cache/wpfc-minified/").$path[1], 1)){
								if(isset($sources[0])){
									// $exist_url = str_replace($path[2], $sources[0], $this->current_url());
									// header('Location: ' . $exist_url, true, 301);
									// exit;

									if(preg_match("/\.css/", $this->current_url())){
										header('Content-type: text/css');
									}else if(preg_match("/\.js/", $this->current_url())){
										header('Content-type: text/js');
									}

									echo file_get_contents($this->getWpContentDir("/cache/wpfc-minified/").$path[1]."/".$sources[0]);
									exit;
								}
							}
						}


						if(preg_match("/".basename($this->getWpContentDir("/cache/wpfc-minified/"))."/i", $this->current_url())){
							//for non-exists minified files
							if(preg_match("/\.css/", $this->current_url())){
								header('Content-type: text/css');
								die("/* File not found */");
							}else if(preg_match("/\.js/", $this->current_url())){
								header('Content-type: text/js');
								die("//File not found");
							}
						}

					}else{
						// to show if the user is logged-in
						add_action('wp_loaded', array($this, "load_admin_toolbar"));

						//for cache
						$this->cache();
					}
				}
			}
		}

		public function enable_auto_cache_settings_panel(){
			if($this->isPluginActive('classic-editor/classic-editor.php') || $this->isPluginActive('disable-gutenberg/disable-gutenberg.php') || has_filter("use_block_editor_for_post", "__return_false")){
				add_action("add_meta_boxes", array($this, "add_meta_box"), 10, 2);
				add_action('admin_notices', array($this, 'single_preload_inline_js'));
				add_action('wp_ajax_wpfc_preload_single_save_settings', array($this, "wpfc_preload_single_save_settings_callback"));
				add_action('wp_ajax_wpfc_preload_single', array($this, "wpfc_preload_single_callback"));
			}
		}

		public function clear_cache_after_activate_plugin(){
			$this->deleteCache(true);
		}

		public function clear_cache_after_deactivate_plugin(){
			$this->deleteCache(true);
		}

		public function clear_cache_after_switch_theme(){
			$this->deleteCache(true);
		}

		public function clear_cache_after_update_plugin($upgrader_object, $options){
			if($options['action'] == 'update'){
				if($options['type'] == 'plugin' && (isset($options['plugins']) || isset($options['plugin']))){

					$options_json = json_encode($options);

					if(preg_match("/elementor\\\\\/elementor\.php/i", $options_json)){
						$this->deleteCache(true);
					}

					if(defined("WPFC_CLEAR_CACHE_AFTER_PLUGIN_UPDATE") && WPFC_CLEAR_CACHE_AFTER_PLUGIN_UPDATE){
						$this->deleteCache(true);
					}
				}
			}
		}

		public function clear_cache_after_update_theme($upgrader_object, $options){
			if($options['action'] == 'update'){
				if($options['type'] == 'theme' && isset($options['themes'])){

					if(defined("WPFC_CLEAR_CACHE_AFTER_THEME_UPDATE") && WPFC_CLEAR_CACHE_AFTER_THEME_UPDATE){
						$this->deleteCache(true);
					}

				}
			}
		}

		public function action_links($actions){
			$actions['powered_settings'] = sprintf(__( '<a href="%s">Settings</a>', 'wp-fastest-cache'), esc_url( admin_url( 'admin.php?page=wpfastestcacheoptions')));
			return array_reverse($actions);
		}

		public function wpfc_preload_single_callback(){
			include_once('inc/single-preload.php');
			SinglePreloadWPFC::create_cache();
		}

		public function single_preload_inline_js(){
			include_once('inc/single-preload.php');
			SinglePreloadWPFC::init();
			SinglePreloadWPFC::put_inline_js();
		}
		public function add_meta_box(){
			include_once('inc/single-preload.php');
			SinglePreloadWPFC::add_meta_box();
		}

		public function wpfc_preload_single_save_settings_callback(){
			include_once('inc/single-preload.php');
			SinglePreloadWPFC::save_settings();
		}

		public function notify($message = array()){
			if(isset($message[0]) && $message[0]){
				if(function_exists("add_settings_error")){
					add_settings_error('wpfc-notice', esc_attr( 'settings_updated' ), $message[0], $message[1]);
				}
			}
		}

		public function set_content_url(){
			$content_url = content_url();

			// Hide My WP
			if($this->isPluginActive('hide_my_wp/hide-my-wp.php')){
				$hide_my_wp = get_option("hide_my_wp");

				if(isset($hide_my_wp["new_content_path"]) && $hide_my_wp["new_content_path"]){
					$hide_my_wp["new_content_path"] = trim($hide_my_wp["new_content_path"], "/");
					$content_url = str_replace(basename(WPFC_WP_CONTENT_DIR), $hide_my_wp["new_content_path"], $content_url);
				}
			}

			// to change content url if a different url is used for other langs
			if($this->isPluginActive('polylang/polylang.php')){
				$url =  parse_url($content_url);

				if($url["host"] != $_SERVER['HTTP_HOST']){
					$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
					$content_url = $protocol.$_SERVER['HTTP_HOST'].$url['path'];
				}
			}

			if (!defined('WPFC_WP_CONTENT_URL')) {
				define("WPFC_WP_CONTENT_URL", $content_url);
			}

			$this->content_url = $content_url;
		}

		public function clear_cache_on_kksr_rate($id){
			$this->singleDeleteCache(false, $id);
		}

		public function clear_cache_after_woocommerce_order_status_changed($order_id = false){
			if(function_exists("wc_get_order")){
				if($order_id){
					$order = wc_get_order($order_id);

					if($order){
						foreach($order->get_items() as $item_key => $item_values ){
							if(method_exists($item_values, 'get_product_id')){
								$this->singleDeleteCache(false, $item_values->get_product_id());
							}
						}
					}
				}
			}
		}

		public function wpfc_db_fix_callback(){
			if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
				include_once $this->get_premium_path("db.php");

				if(class_exists("WpFastestCacheDatabaseCleanup")){
					WpFastestCacheDatabaseCleanup::clean($_GET["type"]);
				}else{
					die(json_encode(array("success" => false, "showupdatewarning" => true, "message" => "Only available in Premium version")));
				}

			}else{
				die(json_encode(array("success" => false, "message" => "Only available in Premium version")));
			}
		}

		public function wpfc_db_statics_callback(){
			global $wpdb;

            $statics = array("all_warnings" => 0,
                             "post_revisions" => 0,
                             "trashed_contents" => 0,
                             "trashed_spam_comments" => 0,
                             "trackback_pingback" => 0,
                             "transient_options" => 0
                            );


            $statics["post_revisions"] = $wpdb->get_var("SELECT COUNT(*) FROM `$wpdb->posts` WHERE post_type = 'revision';");
            $statics["all_warnings"] = $statics["all_warnings"] + $statics["post_revisions"];

            $statics["trashed_contents"] = $wpdb->get_var("SELECT COUNT(*) FROM `$wpdb->posts` WHERE post_status = 'trash';");
            $statics["all_warnings"] = $statics["all_warnings"] + $statics["trashed_contents"];

            $statics["trashed_spam_comments"] = $wpdb->get_var("SELECT COUNT(*) FROM `$wpdb->comments` WHERE comment_approved = 'spam' OR comment_approved = 'trash' ;");
            $statics["all_warnings"] = $statics["all_warnings"] + $statics["trashed_spam_comments"];

            $statics["trackback_pingback"] = $wpdb->get_var("SELECT COUNT(*) FROM `$wpdb->comments` WHERE comment_type = 'trackback' OR comment_type = 'pingback' ;");
            $statics["all_warnings"] = $statics["all_warnings"] + $statics["trackback_pingback"];

            $element = "SELECT COUNT(*) FROM `$wpdb->options` WHERE option_name LIKE '%\_transient\_%' ;";
            $statics["transient_options"] = $wpdb->get_var( $element ) > 100 ? $wpdb->get_var( $element ) : 0;
            $statics["all_warnings"] = $statics["all_warnings"] + $statics["transient_options"];

            die(json_encode($statics));
		}

		public function is_trailing_slash(){
			// no need to check if Custom Permalinks plugin is active (https://tr.wordpress.org/plugins/custom-permalinks/)
			if($this->isPluginActive("custom-permalinks/custom-permalinks.php")){
				return false;
			}

			if($permalink_structure = get_option('permalink_structure')){
				if(preg_match("/\/$/", $permalink_structure)){
					return true;
				}
			}

			return false;
		}

		public function wpfc_cache_statics_get_callback(){
			if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
				if(file_exists(WPFC_WP_PLUGIN_DIR."/wp-fastest-cache-premium/pro/library/statics.php")){
					include_once $this->get_premium_path("statics.php");
					
					$cache_statics = new WpFastestCacheStatics();
					$res = $cache_statics->get();
					echo json_encode($res);
					exit;
				}
			}
		}

		public function wpfc_check_url_ajax_request_callback(){
			include_once('inc/cdn.php');
			CdnWPFC::check_url();
		}

		public function wpfc_cdn_template_ajax_request_callback(){
			include_once('inc/cdn.php');
			CdnWPFC::cdn_template();
		}

		public function wpfc_save_cdn_integration_ajax_request_callback(){
			include_once('inc/cdn.php');
			CdnWPFC::save_cdn_integration();
		}

		public function wpfc_start_cdn_integration_ajax_request_callback(){
			include_once('inc/cdn.php');
			CdnWPFC::start_cdn_integration();
		}

		public function wpfc_pause_cdn_integration_ajax_request_callback(){
			include_once('inc/cdn.php');
			CdnWPFC::pause_cdn_integration();
		}

		public function wpfc_remove_cdn_integration_ajax_request_callback(){
			include_once('inc/cdn.php');
			CdnWPFC::remove_cdn_integration();
		}

		public function wpfc_cdn_options_ajax_request_callback(){
			include_once('inc/cdn.php');
			CdnWPFC::cdn_options();
		}

		public function wpfc_save_exclude_pages_callback(){
			if(!wp_verify_nonce($_POST["security"], 'wpfc-save-exclude-ajax-nonce')){
				die( 'Security check' );
			}
			
			if(current_user_can('manage_options')){
				if(isset($_POST["rules"])){
					foreach ($_POST["rules"] as $key => &$value) {
						$value["prefix"] = strip_tags($value["prefix"]);
						$value["content"] = strip_tags($value["content"]);

						$value["prefix"] = preg_replace("/\'|\"/", "", $value["prefix"]);

						if($value["prefix"] == "regex"){
							$value["content"] = stripslashes($value["content"]);

							$value["content"] = esc_attr($value["content"]);
						}else{
							$value["content"] = preg_replace("/\'|\"/", "", $value["content"]);
							$value["content"] = preg_replace("/(\#|\s|\(|\)|\*)/", "", $value["content"]);
						}

						if($value["prefix"] == "homepage"){
							$this->deleteHomePageCache(false);
						}
					}

					$data = json_encode($_POST["rules"]);

					if(get_option("WpFastestCacheExclude")){
						update_option("WpFastestCacheExclude", $data);
					}else{
						add_option("WpFastestCacheExclude", $data, null, "yes");
					}
				}else{
					delete_option("WpFastestCacheExclude");
				}

				$this->modify_htaccess_for_exclude();

				echo json_encode(array("success" => true));
				exit;
			}else{
				wp_die("Must be admin");
			}
		}

		public function modify_htaccess_for_exclude(){
			$path = ABSPATH;

			if($this->is_subdirectory_install()){
				$path = $this->getABSPATH();
			}

			$htaccess = @file_get_contents($path.".htaccess");

			if(preg_match("/\#\s?Start\sWPFC\sExclude/", $htaccess)){
				$exclude_rules = $this->excludeRules();

				$htaccess = preg_replace("/\#\s?Start\sWPFC\sExclude[^\#]*\#\s?End\sWPFC\sExclude\s+/", $exclude_rules, $htaccess);
			}

			@file_put_contents($path.".htaccess", $htaccess);
		}

		public function wpfc_purgecache_varnish_callback(){
			if($varnish_datas = get_option("WpFastestCacheVarnish")){
				include_once('inc/varnish.php');
				$res_arr = VarnishWPFC::purge_cache($varnish_datas["server"]);
				
				wp_send_json($res_arr);
			}
		}

		public function wpfc_save_varnish_callback(){
			include_once('inc/varnish.php');
			VarnishWPFC::save();
		}

		public function wpfc_remove_varnish_callback(){
			include_once('inc/varnish.php');
			VarnishWPFC::remove();
		}

		public function wpfc_start_varnish_callback(){
			include_once('inc/varnish.php');
			VarnishWPFC::start();
		}

		public function wpfc_pause_varnish_callback(){
			include_once('inc/varnish.php');
			VarnishWPFC::pause();
		}

		public function wpfc_status_varnish(){
			include_once('inc/varnish.php');
			VarnishWPFC::status();
		}

		public function wpfc_get_list_csp_callback(){
			include_once('inc/clearing-specific-pages.php');
			ClearingSpecificPagesWPFC::get_list();
		}

		public function wpfc_save_csp_callback(){
			include_once('inc/clearing-specific-pages.php');
			ClearingSpecificPagesWPFC::save();
		}

		public function wpfc_remove_csp_callback(){
			include_once('inc/clearing-specific-pages.php');
			ClearingSpecificPagesWPFC::remove();
		}

		public function wpfc_save_timeout_pages_callback(){
			if(!wp_verify_nonce($_POST["security"], 'wpfc-save-timeout-ajax-nonce')){
				die( 'Security check' );
			}

			if(current_user_can('manage_options')){
				$this->setCustomInterval();
			
		    	$crons = _get_cron_array();

		    	foreach ($crons as $cron_key => $cron_value) {
		    		foreach ( (array) $cron_value as $hook => $events ) {
		    			if(preg_match("/^wp\_fastest\_cache(.*)/", $hook, $id)){
		    				if(!$id[1] || preg_match("/^\_(\d+)$/", $id[1])){
		    					foreach ( (array) $events as $event_key => $event ) {
			    					if($id[1]){
			    						wp_clear_scheduled_hook("wp_fastest_cache".$id[1], $event["args"]);
			    					}else{
			    						wp_clear_scheduled_hook("wp_fastest_cache", $event["args"]);
			    					}
		    					}
		    				}
		    			}
		    		}
		    	}

				if(isset($_POST["rules"]) && count($_POST["rules"]) > 0){
					$i = 0;

					foreach ($_POST["rules"] as $key => $value) {
						if(preg_match("/^(daily|onceaday)$/i", $value["schedule"]) && isset($value["hour"]) && isset($value["minute"]) && strlen($value["hour"]) > 0 && strlen($value["minute"]) > 0){
							$args = array("prefix" => $value["prefix"], "content" => $value["content"], "hour" => $value["hour"], "minute" => $value["minute"]);

							$timestamp = mktime($value["hour"],$value["minute"],0,date("m"),date("d"),date("Y"));

							$timestamp = $timestamp > time() ? $timestamp : $timestamp + 60*60*24;
						}else{
							$args = array("prefix" => $value["prefix"], "content" => $value["content"]);
							$timestamp = time();
						}

						wp_schedule_event($timestamp, $value["schedule"], "wp_fastest_cache_".$i, array(json_encode($args)));
						$i = $i + 1;
					}
				}

				echo json_encode(array("success" => true));
				exit;
			}else{
				wp_die("Must be admin");
			}
		}

		public function wp_postratings_clear_fastest_cache($rate_userid, $post_id){
			// to remove cache if vote is from homepage or category page or tag
			if(isset($_SERVER["HTTP_REFERER"]) && $_SERVER["HTTP_REFERER"]){
				$url =  parse_url($_SERVER["HTTP_REFERER"]);

				$url["path"] = isset($url["path"]) ? $url["path"] : "/index.html";

				if(isset($url["path"])){
					if($url["path"] == "/"){
						$this->rm_folder_recursively($this->getWpContentDir("/cache/all/index.html"));
					}else{
						// to prevent changing path with ../ or with another method
						if($url["path"] == realpath(".".$url["path"])){
							$this->rm_folder_recursively($this->getWpContentDir("/cache/all").$url["path"]);
						}
					}
				}
			}

			if($post_id){
				$this->singleDeleteCache(false, $post_id);
			}
		}

		private function admin(){			
			if(isset($_GET["page"]) && $_GET["page"] == "wpfastestcacheoptions"){
				include_once('inc/admin.php');
				$wpfc = new WpFastestCacheAdmin();
				$wpfc->addMenuPage();
			}else{
				add_action('admin_menu', array($this, 'register_my_custom_menu_page'));
			}
		}

		public function load_column(){
			if(!defined('WPFC_HIDE_CLEAR_CACHE_BUTTON') || (defined('WPFC_HIDE_CLEAR_CACHE_BUTTON') && !WPFC_HIDE_CLEAR_CACHE_BUTTON)){
				include_once plugin_dir_path(__FILE__)."inc/column.php";

				$column = new WpFastestCacheColumn();
				$column->add();
			}
		}

		public function load_admin_toolbar(){
			if(!defined('WPFC_HIDE_TOOLBAR') || (defined('WPFC_HIDE_TOOLBAR') && !WPFC_HIDE_TOOLBAR)){
				$user = wp_get_current_user();
				$allowed_roles = array('administrator');

				$wpfc_role_status = get_option("WpFastestCacheToolbarSettings");
				if(is_array($wpfc_role_status) && !empty($wpfc_role_status)){
					foreach ($wpfc_role_status as $key => $value){
						$key = str_replace("wpfc_toolbar_", "", $key);
						array_push($allowed_roles, strtolower($key));
					}
				}

				
				if(array_intersect($allowed_roles, $user->roles)){
					include_once plugin_dir_path(__FILE__)."inc/admin-toolbar.php";

					if(preg_match("/\/cache\/all/", $this->getWpContentDir("/cache/all"))){
						$is_multi = false;
					}else{
						$is_multi = true;
					}

					$toolbar = new WpFastestCacheAdminToolbar($is_multi);
					$toolbar->add();
				}
			}
		}

		public function tmp_saveOption(){
			if(!empty($_POST)){
				if(isset($_POST["wpFastestCachePage"])){
					include_once('inc/admin.php');
					$wpfc = new WpFastestCacheAdmin();
					$wpfc->optionsPageRequest();
				}
			}
		}

		public function register_mysettings(){
			register_setting('wpfc-group', 'wpfc-group', array($this, 'tmp_saveOption'));
		}

		public function register_my_custom_menu_page(){
			if(function_exists('add_menu_page')){ 
				add_menu_page("WP Fastest Cache Settings", "WP Fastest Cache", 'manage_options', "wpfastestcacheoptions", array($this, 'optionsPage'), plugins_url("wp-fastest-cache/images/icon.svg"));
				add_action('admin_init', array($this, 'register_mysettings'));

				wp_enqueue_style("wp-fastest-cache", plugins_url("wp-fastest-cache/css/style.css"), array(), time(), "all");
			}
						
			if(isset($_GET["page"]) && $_GET["page"] == "wpfastestcacheoptions"){
				wp_enqueue_style("wp-fastest-cache-buycredit", plugins_url("wp-fastest-cache/css/buycredit.css"), array(), time(), "all");
				wp_enqueue_style("wp-fastest-cache-flaticon", plugins_url("wp-fastest-cache/css/flaticon.css"), array(), time(), "all");
				wp_enqueue_style("wp-fastest-cache-dialog", plugins_url("wp-fastest-cache/css/dialog.css"), array(), time(), "all");
			}
		}

		public function deleteCacheToolbar(){
			$this->deleteCache();
		}

		public function deleteCssAndJsCacheToolbar(){
			$this->deleteCache(true);
		}

		public function wpfc_toolbar_get_settings_callback(){
			if(current_user_can('manage_options')){
				$result = array("success" => true, "roles" => false);

				$wpfc_role_status = get_option("WpFastestCacheToolbarSettings");
				if(is_array($wpfc_role_status) && !empty($wpfc_role_status)){
					$result["roles"] = $wpfc_role_status;
				}

				die(json_encode($result));
			}else{
				wp_die("Must be admin");
			}
		}

		public function wpfc_cache_path_save_settings_callback(){
			if(current_user_can('manage_options')){
				foreach($_POST as $key => &$value){
					$value = esc_html(esc_sql($value));
				}

				$path_arr = array(
								  "cachepath" => sanitize_text_field($_POST["cachepath"]),
							  	  "optimizedpath" => sanitize_text_field($_POST["optimizedpath"])
							);

				if(get_option("WpFastestCachePathSettings") === false){
					add_option("WpFastestCachePathSettings", $path_arr, 1, "no");
				}else{
					update_option("WpFastestCachePathSettings", $path_arr);
				}

				die(json_encode(array("success" => true)));
			}else{
				wp_die("Must be admin");
			}
		}

		public function wpfc_toolbar_save_settings_callback(){
			if(current_user_can('manage_options')){
				if(is_array($_GET["roles"]) && !empty($_GET["roles"])){
					$roles_arr = array();

					foreach($_GET["roles"] as $key => $value){
						$value = esc_html(esc_sql($value));
						$key = esc_html(esc_sql($key));

						$roles_arr[$key] = $value;
					}

					if(get_option("WpFastestCacheToolbarSettings") === false){
						add_option("WpFastestCacheToolbarSettings", $roles_arr, 1, "no");
					}else{
						update_option("WpFastestCacheToolbarSettings", $roles_arr);
					}
				}else{
					delete_option("WpFastestCacheToolbarSettings");
				}


				die(json_encode(array("Saved","success")));
			}else{
				wp_die("Must be admin");
			}
		}

		public function wpfc_clear_cache_of_allsites_callback(){
			include_once('inc/cdn.php');
			CdnWPFC::cloudflare_clear_cache();

			$path = $this->getWpContentDir("/cache/*");

			$files = glob($this->getWpContentDir("/cache/*"));

			if(!is_dir($this->getWpContentDir("/cache/tmpWpfc"))){
				if(@mkdir($this->getWpContentDir("/cache/tmpWpfc"), 0755, true)){
					//tmpWpfc has been created
				}
			}
				
			foreach ((array)$files as $file){
				@rename($file, $this->getWpContentDir("/cache/tmpWpfc/").basename($file)."-".time());
			}

			if (is_admin() && defined('DOING_AJAX') && DOING_AJAX){
				die(json_encode(array("The cache of page has been cleared","success")));
			}
		}

		public function delete_current_page_cache(){
			if(!wp_verify_nonce($_GET["nonce"], "wpfc")){
				die(json_encode(array("Security Error!", "error", "alert")));
			}

			if($varnish_datas = get_option("WpFastestCacheVarnish")){
				include_once('inc/varnish.php');
				VarnishWPFC::purge_cache($varnish_datas);
			}

			include_once('inc/cdn.php');
			CdnWPFC::cloudflare_clear_cache();

			if(isset($_GET["path"])){
				if($_GET["path"]){
					if($_GET["path"] == "/"){
						$_GET["path"] = $_GET["path"]."index.html";
					}
				}else{
					$_GET["path"] = "/index.html";
				}

				$_GET["path"] = urldecode(esc_url_raw($_GET["path"]));

				// for security
				if(preg_match("/\.{2,}/", $_GET["path"])){
					die("May be Directory Traversal Attack");
				}

				$paths = array();

				array_push($paths, $this->getWpContentDir("/cache/all").$_GET["path"]);

				if(class_exists("WpFcMobileCache")){
					$wpfc_mobile = new WpFcMobileCache();
					array_push($paths, $this->getWpContentDir("/cache/wpfc-mobile-cache").$_GET["path"]);
				}

				foreach ($paths as $key => $value){
					if(file_exists($value)){
						if(preg_match("/\/(all|wpfc-mobile-cache)\/index\.html$/i", $value)){
							@unlink($value);
						}else{
							$this->rm_folder_recursively($value);
						}
					}
				}

				$this->delete_multiple_domain_mapping_cache();

				die(json_encode(array("The cache of page has been cleared","success")));
			}else{
				die(json_encode(array("Path has NOT been defined", "error", "alert")));
			}

			exit;
		}

		private function cache(){
			if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']){
				include_once('inc/cache.php');
				$wpfc = new WpFastestCacheCreateCache();
				$wpfc->createCache();
			}
		}

		protected function slug(){
			return "wp_fastest_cache";
		}

		public function getWpContentDir($path = false){
			/*
			Sample Paths;

			/cache/

			/cache/all/
			/cache/all
			/cache/all/page
			/cache/all/index.html

			/cache/wpfc-minified

			/cache/wpfc-widget-cache

			/cache/wpfc-mobile-cache/
			/cache/wpfc-mobile-cache/page
			/cache/wpfc-mobile-cache/index.html
			
			/cache/tmpWpfc
			/cache/tmpWpfc/
			/cache/tmpWpfc/mobile_
			/cache/tmpWpfc/m
			/cache/tmpWpfc/w

			
			/cache/testWpFc/

			/cache/all/testWpFc/
			
			/cache/wpfc-widget-cache/
			/cache/wpfc-widget-cache
			/cache/wpfc-widget-cache/".$args["widget_id"].".html
			*/
			
			if($path){
				if(preg_match("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", $path)){
					//WPML language switch
					//https://wpml.org/forums/topic/wpml-language-switch-wp-fastest-cache-issue/
					$language_negotiation_type = apply_filters('wpml_setting', false, 'language_negotiation_type');
					if(($language_negotiation_type == 2) && $this->isPluginActive('sitepress-multilingual-cms/sitepress.php')){
						$my_home_url = apply_filters('wpml_home_url', get_option('home'));
						$my_home_url = preg_replace("/https?\:\/\//i", "", $my_home_url);
						$my_home_url = trim($my_home_url, "/");
						
					    $path = preg_replace("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", "/cache/".$my_home_url."/$1", $path);
					}else if(($language_negotiation_type == 1) && $this->isPluginActive('sitepress-multilingual-cms/sitepress.php')){
						$my_current_lang = apply_filters('wpml_current_language', NULL);

						if($my_current_lang){
							$path = preg_replace("/\/cache\/wpfc-widget-cache\/(.+)/", "/cache/wpfc-widget-cache/".$my_current_lang."-"."$1", $path);
						}
					}

					if($this->isPluginActive('multiple-domain-mapping-on-single-site/multidomainmapping.php')){
						$path = preg_replace("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", "/cache/".$_SERVER['HTTP_HOST']."/$1", $path);
					}

					if($this->isPluginActive('polylang/polylang.php')){
						$path = preg_replace("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", "/cache/".$_SERVER['HTTP_HOST']."/$1", $path);
					}

					if($this->isPluginActive('multiple-domain/multiple-domain.php')){
						$path = preg_replace("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", "/cache/".$_SERVER['HTTP_HOST']."/$1", $path);
					}

					if(is_multisite()){
						$path = preg_replace("/\/cache\/(all|wpfc-minified|wpfc-widget-cache|wpfc-mobile-cache)/", "/cache/".$_SERVER['HTTP_HOST']."/$1", $path);
					}
				}

				return WPFC_WP_CONTENT_DIR.$path;
			}else{
				return WPFC_WP_CONTENT_DIR;
			}
		}

		protected function getOptions(){
			return $GLOBALS["wp_fastest_cache_options"];
		}

		protected function getSystemMessage(){
			return $this->systemMessage;
		}

		protected function get_excluded_useragent(){
			return "facebookexternalhit|WP_FASTEST_CACHE_CSS_VALIDATOR|Twitterbot|LinkedInBot|WhatsApp|Mediatoolkitbot";
		}

		// protected function detectNewPost(){
		// 	if(isset($this->options->wpFastestCacheNewPost) && isset($this->options->wpFastestCacheStatus)){
		// 		add_filter ('save_post', array($this, 'deleteCache'));
		// 	}
		// }

		public function deleteWidgetCache(){
			$widget_cache_path = $this->getWpContentDir("/cache/wpfc-widget-cache");
			
			if(is_dir($widget_cache_path)){
				if(!is_dir($this->getWpContentDir("/cache/tmpWpfc"))){
					if(@mkdir($this->getWpContentDir("/cache/tmpWpfc"), 0755, true)){
						//tmpWpfc has been created
					}
				}

				if(@rename($widget_cache_path, $this->getWpContentDir("/cache/tmpWpfc/w").time())){
					//DONE
				}
			}
		}

		public function clear_cache_after_woocommerce_update_product($product_id){
			if(!$this->deleted_before){
				$this->singleDeleteCache(false, $product_id);
			}
		}

		public function on_all_status_transitions($new_status, $old_status, $post){
			$this->deleted_before = true;

			if(!wp_is_post_revision($post->ID)){
				if(isset($post->post_type)){
					if($post->post_type == "nf_sub"){
						return 0;
					}
				}

				if($new_status == "publish" && $old_status != "publish"){
					
					$this->specificDeleteCache();
					
					if(isset($this->options->wpFastestCacheNewPost) && isset($this->options->wpFastestCacheStatus)){
						if(isset($this->options->wpFastestCacheNewPost_type) && $this->options->wpFastestCacheNewPost_type){
							if($this->options->wpFastestCacheNewPost_type == "all"){
								$this->deleteCache();
							}else if($this->options->wpFastestCacheNewPost_type == "homepage"){
								$this->deleteHomePageCache();

								//to clear category cache and tag cache
								$this->singleDeleteCache(false, $post->ID);

								//to clear widget cache
								$this->deleteWidgetCache();
							}
						}else{
							$this->deleteCache();
						}
					}
				}

				if(isset($this->options->wpFastestCacheStatus) && $new_status == "publish" && $old_status == "publish"){

					// if(isset($this->options->wpFastestCacheUpdatePost) && isset($this->options->wpFastestCacheStatus)){

					// 	if($this->options->wpFastestCacheUpdatePost_type == "post"){
					// 		$this->singleDeleteCache(false, $post->ID);
					// 	}else if($this->options->wpFastestCacheUpdatePost_type == "all"){
					// 		$this->deleteCache();
					// 	}
						
					// }

					$this->specificDeleteCache();

					if(isset($this->options->wpFastestCacheUpdatePost)){

						if($this->options->wpFastestCacheUpdatePost_type == "post"){
							$this->singleDeleteCache(false, $post->ID);
						}else if($this->options->wpFastestCacheUpdatePost_type == "all"){
							$this->deleteCache();
						}
						
					}else{
						// to clear only cache of content even though the "update post" option is disabled
						$this->singleDeleteCache(false, $post->ID, false);
					}
				}

				if($new_status == "trash" && $old_status == "publish"){
					$this->singleDeleteCache(false, $post->ID);
				}else if(($new_status == "draft" || $new_status == "pending" || $new_status == "private") && $old_status == "publish"){
					$this->deleteCache();
				}
			}
		}

		protected function commentHooks(){
			//it works when the status of a comment changes
			add_action('wp_set_comment_status', array($this, 'singleDeleteCache'), 10, 1);

			//it works when a comment is saved in the database
			add_action('comment_post', array($this, 'detectNewComment'), 10, 2);

			// it work when a commens is updated
			add_action('edit_comment', array($this, 'detectEditComment'), 10, 2);
		}

		public function detectEditComment($comment_id, $comment_data){
			if($comment_data["comment_approved"] == 1){
				$this->singleDeleteCache($comment_id);
			}
		}

		public function detectNewComment($comment_id, $comment_approved){
			// if(current_user_can( 'manage_options') || !get_option('comment_moderation')){
			if($comment_approved === 1){
				$this->singleDeleteCache($comment_id);
			}
		}

		public function specificDeleteCache(){
			$urls = get_option("WpFastestCacheCSP");
			$files = array();

			if(!empty($urls)){
				foreach ($urls as $key => $value) {

					if(preg_match("/https?:\/\/[^\/]+\/(.+)/", $value->url, $out)){

						if(preg_match("/\/\(\.\*\)/", $out[1])){
							$out[1] = str_replace("(.*)", "", $out[1]);

							$path = $this->getWpContentDir("/cache/all/").$out[1];
							$mobile_path = $this->getWpContentDir("/cache/wpfc-mobile-cache/").$out[1];
						}else{
							$out[1] = $out[1]."index.html";

							$path = $this->getWpContentDir("/cache/all/").$out[1];
							$mobile_path = $this->getWpContentDir("/cache/wpfc-mobile-cache/").$out[1];
						}

						if(!is_dir($this->getWpContentDir("/cache/tmpWpfc"))){
							@mkdir($this->getWpContentDir("/cache/tmpWpfc"), 0755, true);
						}


						rename($path, $this->getWpContentDir("/cache/tmpWpfc/").time());
						rename($mobile_path, $this->getWpContentDir("/cache/tmpWpfc/mobile_").time());
						
					}

				}
			}
		}

		public function singleDeleteCache($comment_id = false, $post_id = false, $to_clear_parents = true){
			if($varnish_datas = get_option("WpFastestCacheVarnish")){
				include_once('inc/varnish.php');
				VarnishWPFC::purge_cache($varnish_datas);
			}

			include_once('inc/cdn.php');
			CdnWPFC::cloudflare_clear_cache();

			$to_clear_feed = true;

			// not to clear cache of homepage/cats/tags after ajax request by other plugins
			if(isset($_POST) && isset($_POST["action"])){
				// kk Star Rating
				if($_POST["action"] == "kksr_ajax"){
					$to_clear_parents = false;
				}

				// All In One Schema.org Rich Snippets
				if(preg_match("/bsf_(update|submit)_rating/i", $_POST["action"])){
					$to_clear_parents = false;
				}

				// Yet Another Stars Rating
				if($_POST["action"] == "yasr_send_visitor_rating"){
					$to_clear_parents = false;
					$post_id = sanitize_text_field($_POST["post_id"]);
				}

				// All In One Schema.org Rich Snippets
				if(preg_match("/bsf_(update|submit)_rating/i", $_POST["action"])){
					$to_clear_feed = false;
				}
			}

			if($comment_id){
				$comment_id = intval($comment_id);
				
				$comment = get_comment($comment_id);
				
				if($comment && $comment->comment_post_ID){
					$post_id = $comment->comment_post_ID;
				}
			}

			if($post_id){
				$post_id = intval($post_id);

				$permalink = get_permalink($post_id);

				$permalink = urldecode(get_permalink($post_id));



				//for trash contents
				if(preg_match("/\/\?p\=\d+/i", $permalink)){
					$post = get_post($post_id);

					$clone_post = clone $post;
					$clone_post->post_status = 'publish';

					$permalink = get_permalink($clone_post);
					$permalink = rtrim($permalink, "/");

					$permalink = preg_replace("/__trashed$/", "", $permalink);
					//for /%postname%/%post_id% : sample-url__trashed/57595
					$permalink = preg_replace("/__trashed\/(\d+)$/", "/$1", $permalink);
				}



				if(preg_match("/https?:\/\/[^\/]+\/(.+)/", $permalink, $out)){
					$path = $this->getWpContentDir("/cache/all/").$out[1];
					$mobile_path = $this->getWpContentDir("/cache/wpfc-mobile-cache/").$out[1];

					if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
						include_once $this->get_premium_path("logs.php");
						$log = new WpFastestCacheLogs("delete");
						$log->action();
					}

					$files = array();

					if(is_dir($path)){
						array_push($files, $path);
					}

					if(is_dir($mobile_path)){
						array_push($files, $mobile_path);
					}

					if(defined('WPFC_CACHE_QUERYSTRING') && WPFC_CACHE_QUERYSTRING){
						$files_with_query_string = glob($path."\?*");
						$mobile_files_with_query_string = glob($mobile_path."\?*");

						if(is_array($files_with_query_string) && (count($files_with_query_string) > 0)){
							$files = array_merge($files, $files_with_query_string);
						}

						if(is_array($mobile_files_with_query_string) && (count($mobile_files_with_query_string) > 0)){
							$files = array_merge($files, $mobile_files_with_query_string);
						}
					}

					if($to_clear_feed){
						// to clear cache of /feed
						if(preg_match("/https?:\/\/[^\/]+\/(.+)/", get_feed_link(), $feed_out)){
							array_push($files, $this->getWpContentDir("/cache/all/").$feed_out[1]);
						}

						// to clear cache of /comments/feed/
						if(preg_match("/https?:\/\/[^\/]+\/(.+)/", get_feed_link("comments_"), $comment_feed_out)){
							array_push($files, $this->getWpContentDir("/cache/all/").$comment_feed_out[1]);
						}
					}

					foreach((array)$files as $file){
						$this->rm_folder_recursively($file);
					}
				}

				if($to_clear_parents){
					// to clear cache of homepage
					$this->deleteHomePageCache();

					// to clear cache of author page
					$this->delete_author_page_cache($post_id);

					// to clear sitemap cache
					$this->delete_sitemap_cache();

					// to clear cache of cats and  tags which contains the post (only first page)
					global $wpdb;
					$terms = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."term_relationships` WHERE `object_id`=".$post_id, ARRAY_A);

					foreach ($terms as $term_key => $term_val){
						$this->delete_cache_of_term($term_val["term_taxonomy_id"]);
					}
				}

				$this->delete_multiple_domain_mapping_cache();
			}
		}

		public function delete_sitemap_cache(){
			//to clear sitemap.xml and sitemap-(.+).xml
			$files = array_merge(glob($this->getWpContentDir("/cache/all/")."sitemap*.xml"), glob($this->getWpContentDir("/cache/wpfc-mobile-cache/")."sitemap*.xml"));

			foreach((array)$files as $file){
				$this->rm_folder_recursively($file);
			}
		}

		public function delete_multiple_domain_mapping_cache($minified = false){
			//https://wordpress.org/plugins/multiple-domain-mapping-on-single-site/
			if($this->isPluginActive("multiple-domain-mapping-on-single-site/multidomainmapping.php")){
				$multiple_arr = get_option('falke_mdm_mappings');

				if(isset($multiple_arr) && isset($multiple_arr["mappings"]) && isset($multiple_arr["mappings"][0])){
					foreach($multiple_arr["mappings"] as $mapping_key => $mapping_value){
						if($minified){
							$mapping_domain_path = preg_replace("/(\/cache\/[^\/]+\/all\/)/", "/cache/".$mapping_value["domain"]."/", $this->getWpContentDir("/cache/all/"));

							if(is_dir($mapping_domain_path)){
								if(@rename($mapping_domain_path, $this->getWpContentDir("/cache/tmpWpfc/").$mapping_value["domain"]."_".time())){

								}
							}
						}else{
							$mapping_domain_path = preg_replace("/(\/cache\/[^\/]+\/all)/", "/cache/".$mapping_value["domain"]."/all", $this->getWpContentDir("/cache/all/index.html"));

							@unlink($mapping_domain_path);
						}
					}
				}
			}
		}

		public function delete_author_page_cache($post_id){
			$author_id = get_post_field ('post_author', $post_id);
			$permalink = get_author_posts_url($author_id);

			if(preg_match("/https?:\/\/[^\/]+\/(.+)/", $permalink, $out)){
				$path = $this->getWpContentDir("/cache/all/").$out[1];
				$mobile_path = $this->getWpContentDir("/cache/wpfc-mobile-cache/").$out[1];
					
				$this->rm_folder_recursively($path);
				$this->rm_folder_recursively($mobile_path);
			}
		}

		public function delete_cache_of_term($term_taxonomy_id){
			$term = get_term_by("term_taxonomy_id", $term_taxonomy_id);

			if(!$term || is_wp_error($term)){
				return false;
			}

			//if(preg_match("/cat|tag|store|listing/", $term->taxonomy)){}

			$url = get_term_link($term->term_id, $term->taxonomy);

			if(preg_match("/^http/", $url)){
				$path = preg_replace("/https?\:\/\/[^\/]+/i", "", $url);
				$path = trim($path, "/");
				$path = urldecode($path);

				// to remove the cache of tag/cat
				if(file_exists($this->getWpContentDir("/cache/all/").$path."/index.html")){
					@unlink($this->getWpContentDir("/cache/all/").$path."/index.html");
				}

				if(file_exists($this->getWpContentDir("/cache/wpfc-mobile-cache/").$path."/index.html")){
					@unlink($this->getWpContentDir("/cache/wpfc-mobile-cache/").$path."/index.html");
				}


				// to remove the cache of the pages
				$this->rm_folder_recursively($this->getWpContentDir("/cache/all/").$path."/page");
				$this->rm_folder_recursively($this->getWpContentDir("/cache/wpfc-mobile-cache/").$path."/page");

				// to remove the cache of the feeds
				$this->rm_folder_recursively($this->getWpContentDir("/cache/all/").$path."/feed");
				$this->rm_folder_recursively($this->getWpContentDir("/cache/wpfc-mobile-cache/").$path."/feed");
			}

			if($term->parent > 0){
				$parent = get_term_by("id", $term->parent, $term->taxonomy);
				$this->delete_cache_of_term($parent->term_taxonomy_id);
			}



		}

		public function deleteHomePageCache($log = true){
			if($varnish_datas = get_option("WpFastestCacheVarnish")){
				include_once('inc/varnish.php');
				VarnishWPFC::purge_cache($varnish_datas);
			}

			include_once('inc/cdn.php');
			CdnWPFC::cloudflare_clear_cache();

			$site_url_path = preg_replace("/https?\:\/\/[^\/]+/i", "", site_url());
			$home_url_path = preg_replace("/https?\:\/\/[^\/]+/i", "", home_url());

			if($site_url_path){
				$site_url_path = trim($site_url_path, "/");

				if($site_url_path){
					@unlink($this->getWpContentDir("/cache/all/").$site_url_path."/index.html");
					@unlink($this->getWpContentDir("/cache/wpfc-mobile-cache/").$site_url_path."/index.html");

					//to clear pagination of homepage cache
					$this->rm_folder_recursively($this->getWpContentDir("/cache/all/").$site_url_path."/page");
					$this->rm_folder_recursively($this->getWpContentDir("/cache/wpfc-mobile-cache/").$site_url_path."/page");
				}
			}

			if($home_url_path){
				$home_url_path = trim($home_url_path, "/");

				if($home_url_path){
					@unlink($this->getWpContentDir("/cache/all/").$home_url_path."/index.html");
					@unlink($this->getWpContentDir("/cache/wpfc-mobile-cache/").$home_url_path."/index.html");

					//to clear pagination of homepage cache
					$this->rm_folder_recursively($this->getWpContentDir("/cache/all/").$home_url_path."/page");
					$this->rm_folder_recursively($this->getWpContentDir("/cache/wpfc-mobile-cache/").$home_url_path."/page");
				}
			}

			if(function_exists("wc_get_page_id")){
				if($shop_id = wc_get_page_id('shop')){
					$store_url_path = preg_replace("/https?\:\/\/[^\/]+/i", "", get_permalink($shop_id));

					if($store_url_path){
						$store_url_path = trim($store_url_path, "/");

						if($store_url_path){
							if(file_exists($this->getWpContentDir("/cache/all/").$store_url_path."/index.html")){
								@unlink($this->getWpContentDir("/cache/all/").$store_url_path."/index.html");
							}

							if(file_exists($this->getWpContentDir("/cache/wpfc-mobile-cache/").$store_url_path."/index.html")){
								@unlink($this->getWpContentDir("/cache/wpfc-mobile-cache/").$store_url_path."/index.html");
							}

							//to clear pagination of store homepage cache
							$this->rm_folder_recursively($this->getWpContentDir("/cache/all/").$store_url_path."/page");
							$this->rm_folder_recursively($this->getWpContentDir("/cache/wpfc-mobile-cache/").$store_url_path."/page");
						}
					}
				}
			}

			if(file_exists($this->getWpContentDir("/cache/all/index.html"))){
				@unlink($this->getWpContentDir("/cache/all/index.html"));
			}

			if(file_exists($this->getWpContentDir("/cache/wpfc-mobile-cache/index.html"))){
				@unlink($this->getWpContentDir("/cache/wpfc-mobile-cache/index.html"));
			}

			//to clear pagination of homepage cache
			$this->rm_folder_recursively($this->getWpContentDir("/cache/all/page"));
			$this->rm_folder_recursively($this->getWpContentDir("/cache/wpfc-mobile-cache/page"));

			// options-reading.php - static posts page
			if($page_for_posts_id = get_option('page_for_posts')){
				$page_for_posts_permalink = urldecode(get_permalink($page_for_posts_id));

				$page_for_posts_permalink = rtrim($page_for_posts_permalink, "/");
				$page_for_posts_permalink = preg_replace("/__trashed$/", "", $page_for_posts_permalink);
				//for /%postname%/%post_id% : sample-url__trashed/57595
				$page_for_posts_permalink = preg_replace("/__trashed\/(\d+)$/", "/$1", $page_for_posts_permalink);

				if(preg_match("/https?:\/\/[^\/]+\/(.+)/", $page_for_posts_permalink, $out)){
					$page_for_posts_path = $this->getWpContentDir("/cache/all/").$out[1];
					$page_for_posts_mobile_path = $this->getWpContentDir("/cache/wpfc-mobile-cache/").$out[1];
					
					$this->rm_folder_recursively($page_for_posts_path);
					$this->rm_folder_recursively($page_for_posts_mobile_path);
				}
			}


			if($log){
				if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
					include_once $this->get_premium_path("logs.php");

					$log = new WpFastestCacheLogs("delete");
					$log->action();
				}
			}
		}

		public function deleteCache($minified = false){
			if($varnish_datas = get_option("WpFastestCacheVarnish")){
				include_once('inc/varnish.php');
				VarnishWPFC::purge_cache($varnish_datas);
			}

			include_once('inc/cdn.php');
			CdnWPFC::cloudflare_clear_cache();

			$this->set_preload();

			$created_tmpWpfc = false;
			$cache_deleted = false;
			$minifed_deleted = false;

			$cache_path = $this->getWpContentDir("/cache/all");
			$minified_cache_path = $this->getWpContentDir("/cache/wpfc-minified");

			if(class_exists("WpFcMobileCache")){




				if(is_dir($this->getWpContentDir("/cache/wpfc-mobile-cache"))){
					if(is_dir($this->getWpContentDir("/cache/tmpWpfc"))){
						rename($this->getWpContentDir("/cache/wpfc-mobile-cache"), $this->getWpContentDir("/cache/tmpWpfc/mobile_").time());
					}else if(@mkdir($this->getWpContentDir("/cache/tmpWpfc"), 0755, true)){
						rename($this->getWpContentDir("/cache/wpfc-mobile-cache"), $this->getWpContentDir("/cache/tmpWpfc/mobile_").time());
					}
				}


			}
			
			if(!is_dir($this->getWpContentDir("/cache/tmpWpfc"))){
				if(@mkdir($this->getWpContentDir("/cache/tmpWpfc"), 0755, true)){
					$created_tmpWpfc = true;
				}else{
					$created_tmpWpfc = false;
					//$this->systemMessage = array("Permission of <strong>/wp-content/cache</strong> must be <strong>755</strong>", "error");
				}
			}else{
				$created_tmpWpfc = true;
			}

			//to clear widget cache path
			$this->deleteWidgetCache();

			$this->delete_multiple_domain_mapping_cache($minified);

			if(is_dir($cache_path)){
				if(@rename($cache_path, $this->getWpContentDir("/cache/tmpWpfc/").time())){
					delete_option("WpFastestCacheHTML");
					delete_option("WpFastestCacheHTMLSIZE");
					delete_option("WpFastestCacheMOBILE");
					delete_option("WpFastestCacheMOBILESIZE");

					$cache_deleted = true;
				}
			}else{
				$cache_deleted = true;
			}

			if($minified){
				if(is_dir($minified_cache_path)){
					if(@rename($minified_cache_path, $this->getWpContentDir("/cache/tmpWpfc/m").time())){
						delete_option("WpFastestCacheCSS");
						delete_option("WpFastestCacheCSSSIZE");
						delete_option("WpFastestCacheJS");
						delete_option("WpFastestCacheJSSIZE");

						$minifed_deleted = true;
					}
				}else{
					$minifed_deleted = true;
				}
			}else{
				$minifed_deleted = true;
			}

			if($created_tmpWpfc && $cache_deleted && $minifed_deleted){
				do_action('wpfc_delete_cache');
				
				$this->notify(array("All cache files have been deleted", "updated"));

				if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
					include_once $this->get_premium_path("logs.php");

					$log = new WpFastestCacheLogs("delete");
					$log->action();
				}
			}else{
				$this->notify(array("Permissions Problem: <a href='http://www.wpfastestcache.com/warnings/delete-cache-problem-related-to-permission/' target='_blank'>Read More</a>", "error"));
			}

			// for ajax request
			if(isset($_GET["action"]) && in_array($_GET["action"], array("wpfc_delete_cache", "wpfc_delete_cache_and_minified"))){
				die(json_encode($this->systemMessage));
			}
		}

		public function checkCronTime(){
			$crons = _get_cron_array();

	    	foreach ((array)$crons as $cron_key => $cron_value) {
	    		foreach ( (array) $cron_value as $hook => $events ) {
	    			if(preg_match("/^wp\_fastest\_cache(.*)/", $hook, $id)){
	    				if(!$id[1] || preg_match("/^\_(\d+)$/", $id[1])){
		    				foreach ( (array) $events as $event_key => $event ) {
		    					add_action("wp_fastest_cache".$id[1],  array($this, 'setSchedule'));
		    				}
		    			}
		    		}
		    	}
		    }

		    add_action($this->slug()."_Preload",  array($this, 'create_preload_cache'), 11);
		}

		public function set_preload(){
			include_once('inc/preload.php');
			PreloadWPFC::set_preload($this->slug());
		}

		public function create_preload_cache(){
			$this->options = $this->getOptions();
			
			include_once('inc/preload.php');
			PreloadWPFC::create_preload_cache($this->options);
		}

		public function wpfc_remote_get($url, $user_agent){
			//$response = wp_remote_get($url, array('timeout' => 10, 'sslverify' => false, 'headers' => array("cache-control" => array("no-store, no-cache, must-revalidate", "post-check=0, pre-check=0"),'user-agent' => $user_agent)));
			$response = wp_remote_get($url, array('user-agent' => $user_agent, 'timeout' => 10, 'sslverify' => false, 'headers' => array("cache-control" => "no-store, no-cache, must-revalidate, post-check=0, pre-check=0")));

			if (!$response || is_wp_error($response)){
				echo $response->get_error_message()." - ";

				return false;
			}else{
				if(wp_remote_retrieve_response_code($response) != 200){
					return false;
				}
			}

			return true;
		}

		public function setSchedule($args = ""){
			if($args){
				$rule = json_decode($args);

				if($rule->prefix == "all"){
					$this->deleteCache();
				}else if($rule->prefix == "homepage"){
					@unlink($this->getWpContentDir("/cache/all/index.html"));
					@unlink($this->getWpContentDir("/cache/wpfc-mobile-cache/index.html"));

					if(isset($this->options->wpFastestCachePreload_homepage) && $this->options->wpFastestCachePreload_homepage){
						$this->wpfc_remote_get(get_option("home"), "WP Fastest Cache Preload Bot - After Cache Timeout");
						$this->wpfc_remote_get(get_option("home"), "WP Fastest Cache Preload iPhone Mobile Bot - After Cache Timeout");
					}
				}else if($rule->prefix == "startwith"){
						if(!is_dir($this->getWpContentDir("/cache/tmpWpfc"))){
							if(@mkdir($this->getWpContentDir("/cache/tmpWpfc"), 0755, true)){}
						}

						$rule->content = trim($rule->content, "/");

						$files = glob($this->getWpContentDir("/cache/all/").$rule->content."*");

						foreach ((array)$files as $file) {
							$mobile_file = str_replace("/cache/all/", "/cache/wpfc-mobile-cache/", $file);
							
							@rename($file, $this->getWpContentDir("/cache/tmpWpfc/").time());
							@rename($mobile_file, $this->getWpContentDir("/cache/tmpWpfc/mobile_").time());
						}
				}else if($rule->prefix == "exact"){
					$rule->content = trim($rule->content, "/");

					@unlink($this->getWpContentDir("/cache/all/").$rule->content."/index.html");
					@unlink($this->getWpContentDir("/cache/wpfc-mobile-cache/").$rule->content."/index.html");
				}

				if($rule->prefix != "all"){
					if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
						include_once $this->get_premium_path("logs.php");
						$log = new WpFastestCacheLogs("delete");
						$log->action($rule);
					}
				}
			}else{
				//for old cron job
				$this->deleteCache();
			}
		}

		public function modify_htaccess_for_new_user($user_id){
			$path = ABSPATH;

			if($this->is_subdirectory_install()){
				$path = $this->getABSPATH();
			}

			$htaccess = @file_get_contents($path.".htaccess");

			if(preg_match("/\#\s?Start_WPFC_Exclude_Admin_Cookie/", $htaccess)){
				$rules = $this->excludeAdminCookie();

				$htaccess = preg_replace("/\#\s?Start_WPFC_Exclude_Admin_Cookie[^\#]*\#\s?End_WPFC_Exclude_Admin_Cookie\s+/", $rules, $htaccess);
			}

			@file_put_contents($path.".htaccess", $htaccess);
		}

		public function excludeAdminCookie(){
			$rules = "";
			$users_groups = array_chunk(get_users(array("role" => "administrator", "fields" => array("user_login"))), 5);

			foreach ($users_groups as $group_key => $group) {
				$tmp_users = "";
				$tmp_rule = "";

				foreach ($group as $key => $value) {
					if($tmp_users){
						$tmp_users = $tmp_users."|".sanitize_user(wp_unslash($value->user_login), true);
					}else{
						$tmp_users = sanitize_user(wp_unslash($value->user_login), true);
					}

					// to replace spaces with \s
					$tmp_users = preg_replace("/\s/", "\s", $tmp_users);

					if(!next($group)){
						$tmp_rule = "RewriteCond %{HTTP:Cookie} !wordpress_logged_in_[^\=]+\=".$tmp_users;
					}
				}

				if($rules){
					$rules = $rules."\n".$tmp_rule;
				}else{
					$rules = $tmp_rule;
				}
			}

			return "# Start_WPFC_Exclude_Admin_Cookie\n".$rules."\n# End_WPFC_Exclude_Admin_Cookie\n";
		}

		public function excludeRules(){
			$htaccess_page_rules = "";
			$htaccess_page_useragent = "";
			$htaccess_page_cookie = "";

			if($rules_json = get_option("WpFastestCacheExclude")){
				if($rules_json != "null"){
					$rules_std = json_decode($rules_json);

					foreach ($rules_std as $key => $value) {
						$value->type = isset($value->type) ? $value->type : "page";

						// escape the chars
						$value->content = str_replace("?", "\?", $value->content);

						if($value->type == "page"){
							if($value->prefix == "startwith"){
								$value->content = ltrim($value->content, "/");

								$htaccess_page_rules = $htaccess_page_rules."RewriteCond %{REQUEST_URI} !^/".$value->content." [NC]\n";
							}

							if($value->prefix == "contain"){
								$htaccess_page_rules = $htaccess_page_rules."RewriteCond %{REQUEST_URI} !".$value->content." [NC]\n";
							}

							if($value->prefix == "exact"){
								$value->content = trim($value->content, "/");
								
								$htaccess_page_rules = $htaccess_page_rules."RewriteCond %{REQUEST_URI} !\/".$value->content." [NC]\n";
							}
						}else if($value->type == "useragent"){
							$htaccess_page_useragent = $htaccess_page_useragent."RewriteCond %{HTTP_USER_AGENT} !".$value->content." [NC]\n";
						}else if($value->type == "cookie"){
							$htaccess_page_cookie = $htaccess_page_cookie."RewriteCond %{HTTP:Cookie} !".$value->content." [NC]\n";
						}
					}
				}
			}

			return "# Start WPFC Exclude\n".$htaccess_page_rules.$htaccess_page_useragent.$htaccess_page_cookie."# End WPFC Exclude\n";
		}

		public function getABSPATH(){
			$path = ABSPATH;
			$siteUrl = site_url();
			$homeUrl = home_url();
			$diff = str_replace($homeUrl, "", $siteUrl);
			$diff = trim($diff,"/");

		    $pos = strrpos($path, $diff);

		    if($pos !== false){
		    	$path = substr_replace($path, "", $pos, strlen($diff));
		    	$path = trim($path,"/");
		    	$path = "/".$path."/";
		    }
		    return $path;
		}

		public function rm_folder_recursively($dir, $i = 1) {
			if(is_dir($dir)){
				$files = @scandir($dir);
			    foreach((array)$files as $file) {
			    	if($i > 50 && !preg_match("/wp-fastest-cache-premium/i", $dir)){
			    		return true;
			    	}else{
			    		$i++;
			    	}
			        if ('.' === $file || '..' === $file) continue;
			        if (is_dir("$dir/$file")){
			        	$this->rm_folder_recursively("$dir/$file", $i);
			        }else{
			        	if(file_exists("$dir/$file")){
			        		@unlink("$dir/$file");
			        	}
			        }
			    }
			}
	
		    if(is_dir($dir)){
			    $files_tmp = @scandir($dir);
			    
			    if(!isset($files_tmp[2])){
			    	@rmdir($dir);
			    }
		    }

		    return true;
		}

		public function is_subdirectory_install(){
			if(strlen(site_url()) > strlen(home_url())){
				return true;
			}
			return false;
		}

		protected function getMobileUserAgents(){
			return implode("|", $this->get_mobile_browsers())."|".implode("|", $this->get_operating_systems());
		}

		public function get_premium_path($name){
			return WPFC_WP_PLUGIN_DIR."/wp-fastest-cache-premium/pro/library/".$name;
		}

		public function cron_add_minute( $schedules ) {
			$schedules['everyminute'] = array(
			    'interval' => 60*1,
			    'display' => __( 'Once Every 1 Minute' ),
			    'wpfc' => true
		    );

			$schedules['everyfiveminute'] = array(
			    'interval' => 60*5,
			    'display' => __( 'Once Every 5 Minutes' ),
			    'wpfc' => true
		    );

		   	$schedules['everyfifteenminute'] = array(
			    'interval' => 60*15,
			    'display' => __( 'Once Every 15 Minutes' ),
			    'wpfc' => true
		    );

		    $schedules['twiceanhour'] = array(
			    'interval' => 60*30,
			    'display' => __( 'Twice an Hour' ),
			    'wpfc' => true
		    );

		    $schedules['onceanhour'] = array(
			    'interval' => 60*60,
			    'display' => __( 'Once an Hour' ),
			    'wpfc' => true
		    );

		    $schedules['everytwohours'] = array(
			    'interval' => 60*60*2,
			    'display' => __( 'Once Every 2 Hours' ),
			    'wpfc' => true
		    );

		    $schedules['everythreehours'] = array(
			    'interval' => 60*60*3,
			    'display' => __( 'Once Every 3 Hours' ),
			    'wpfc' => true
		    );

		    $schedules['everyfourhours'] = array(
			    'interval' => 60*60*4,
			    'display' => __( 'Once Every 4 Hours' ),
			    'wpfc' => true
		    );

		    $schedules['everyfivehours'] = array(
			    'interval' => 60*60*5,
			    'display' => __( 'Once Every 5 Hours' ),
			    'wpfc' => true
		    );

		    $schedules['everysixhours'] = array(
			    'interval' => 60*60*6,
			    'display' => __( 'Once Every 6 Hours' ),
			    'wpfc' => true
		    );

		    $schedules['everysevenhours'] = array(
			    'interval' => 60*60*7,
			    'display' => __( 'Once Every 7 Hours' ),
			    'wpfc' => true
		    );

		    $schedules['everyeighthours'] = array(
			    'interval' => 60*60*8,
			    'display' => __( 'Once Every 8 Hours' ),
			    'wpfc' => true
		    );

		    $schedules['everyninehours'] = array(
			    'interval' => 60*60*9,
			    'display' => __( 'Once Every 9 Hours' ),
			    'wpfc' => true
		    );

		    $schedules['everytenhours'] = array(
			    'interval' => 60*60*10,
			    'display' => __( 'Once Every 10 Hours' ),
			    'wpfc' => true
		    );

		    $schedules['onceaday'] = array(
			    'interval' => 60*60*24,
			    'display' => __( 'Once a Day' ),
			    'wpfc' => true
		    );

		    $schedules['everythreedays'] = array(
			    'interval' => 60*60*24*3,
			    'display' => __( 'Once Every 3 Days' ),
			    'wpfc' => true
		    );

		    $schedules['everysevendays'] = array(
			    'interval' => 60*60*24*7,
			    'display' => __( 'Once Every 7 Days' ),
			    'wpfc' => true
		    );

		    $schedules['everytendays'] = array(
			    'interval' => 60*60*24*10,
			    'display' => __( 'Once Every 10 Days' ),
			    'wpfc' => true
		    );

		    $schedules['everyfifteendays'] = array(
			    'interval' => 60*60*24*15,
			    'display' => __( 'Once Every 15 Days' ),
			    'wpfc' => true
		    );

		    $schedules['montly'] = array(
			    'interval' => 60*60*24*30,
			    'display' => __( 'Once a Month' ),
			    'wpfc' => true
		    );

		    $schedules['yearly'] = array(
			    'interval' => 60*60*24*30*12,
			    'display' => __( 'Once a Year' ),
			    'wpfc' => true
		    );

		    return $schedules;
		}

		public function setCustomInterval(){
			add_filter( 'cron_schedules', array($this, 'cron_add_minute'));
		}

		public function isPluginActive( $plugin ) {
			return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || $this->isPluginActiveForNetwork( $plugin );
		}
		
		public function isPluginActiveForNetwork( $plugin ) {
			if ( !is_multisite() )
				return false;

			$plugins = get_site_option( 'active_sitewide_plugins');
			if ( isset($plugins[$plugin]) )
				return true;

			return false;
		}

		public function current_url(){
			global $wp;
		    $current_url = home_url($_SERVER['REQUEST_URI']);

		    return $current_url;


			// if(defined('WP_CLI')){
			// 	$_SERVER["SERVER_NAME"] = isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : "";
			// 	$_SERVER["SERVER_PORT"] = isset($_SERVER["SERVER_PORT"]) ? $_SERVER["SERVER_PORT"] : 80;
			// }
			
		 //    $pageURL = 'http';
		 
		 //    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
		 //        $pageURL .= 's';
		 //    }
		 
		 //    $pageURL .= '://';
		 
		 //    if($_SERVER['SERVER_PORT'] != '80'){
		 //        $pageURL .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		 //    }else{
		 //        $pageURL .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		 //    }
		 
		 //    return $pageURL;
		}

		public function wpfc_load_plugin_textdomain(){
			load_plugin_textdomain('wp-fastest-cache', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
		}

		public function cdn_replace_urls($matches){
			if(count($this->cdn) > 0){
				foreach ($this->cdn as $key => $cdn) {
					if($cdn->id == "cloudflare"){
						continue;
					}

					if(isset($cdn->status) && $cdn->status == "pause"){
						continue;
					}

					if(preg_match("/manifest\.json\.php/i", $matches[0])){
						return $matches[0];
					}

					//https://site.com?brizy_media=AttachmentName.jpg&brizy_crop=CropSizes&brizy_post=TheCurrentPost
					if(preg_match("/brizy_media\=/i", $matches[0])){
						return $matches[0];
					}

					//https://cdn.shortpixel.ai/client/q_glossy,ret_img,w_736/http://wpfc.com/stories.png
					if(preg_match("/cdn\.shortpixel\.ai\/client/i", $matches[0])){
						return $matches[0];
					}


					if(preg_match("/^\/\/random/", $cdn->cdnurl) || preg_match("/\/\/i\d\.wp\.com/", $cdn->cdnurl)){
						// Photon will no longer be supported
						continue;
					}

					$cdnurl = $cdn->cdnurl;

					$cdn->file_types = str_replace(",", "|", $cdn->file_types);

					if(preg_match("/\.(".$cdn->file_types.")(\"|\'|\?|\)|\s|\&quot\;)/i", $matches[0])){
						//nothing
					}else{
						if(preg_match("/js/", $cdn->file_types)){
							if(!preg_match("/\/revslider\/public\/assets\/js/", $matches[0])){
								continue;
							}
						}else{
							continue;
						}
					}

					if($cdn->keywords){
						$cdn->keywords = str_replace(",", "|", $cdn->keywords);

						if(!preg_match("/".preg_quote($cdn->keywords, "/")."/i", $matches[0])){
							continue;
						}
					}

					if(isset($cdn->excludekeywords) && $cdn->excludekeywords){
						$cdn->excludekeywords = str_replace(",", "|", $cdn->excludekeywords);

						if(preg_match("/".preg_quote($cdn->excludekeywords, "/")."/i", $matches[0])){
							continue;
						}
					}

					if(preg_match("/(data-product_variations|data-siteorigin-parallax)\=[\"\'][^\"\']+[\"\']/i", $matches[0])){
						$cdnurl = preg_replace("/(https?\:)?(\/\/)(www\.)?/", "", $cdnurl);

						// if(preg_match("/i\d\.wp\.com/i", $cdnurl)){
						// 	$matches[0] = preg_replace("/(quot\;|\s)(https?\:)?(\\\\\/\\\\\/|\/\/)(www\.)?".$cdn->originurl."/i", "$1$2$3".$cdnurl, $matches[0]);
						// }else{
						// 	$matches[0] = preg_replace("/(quot\;|\s)(https?\:)?(\\\\\/\\\\\/|\/\/)(www\.)?".$cdn->originurl."/i", "$1$2$3$4".$cdnurl, $matches[0]);
						// }

						$matches[0] = preg_replace("/(quot\;|\s)(https?\:)?(\\\\\/\\\\\/|\/\/)(www\.)?".$cdn->originurl."/i", '${1}${2}${3}'.$cdnurl, $matches[0]);

						
					}else if(preg_match("/\{\"concatemoji\"\:\"[^\"]+\"\}/i", $matches[0])){
						$matches[0] = preg_replace("/(http(s?)\:)?".preg_quote("\/\/", "/")."(www\.)?/i", "", $matches[0]);
						$matches[0] = preg_replace("/".preg_quote($cdn->originurl, "/")."/i", $cdnurl, $matches[0]);
					}else if(isset($matches[2]) && preg_match("/".preg_quote($cdn->originurl, "/")."/", $matches[2])){
						$matches[0] = preg_replace("/(http(s?)\:)?\/\/(www\.)?".preg_quote($cdn->originurl, "/")."/i", $cdnurl, $matches[0]);
					}else if(isset($matches[2]) && preg_match("/^(\/?)(wp-includes|wp-content)/", $matches[2])){
						$matches[0] = preg_replace("/(\/?)(wp-includes|wp-content)/i", $cdnurl."/"."$2", $matches[0]);
					}else if(preg_match("/[\"\']https?\:\\\\\/\\\\\/[^\"\']+[\"\']/i", $matches[0])){
						if(preg_match("/^(logo|url|image)$/i", $matches[1])){
							//If the url is called with "//", it causes an error on https://search.google.com/structured-data/testing-tool/u/0/
							//<script type="application/ld+json">"logo":{"@type":"ImageObject","url":"\/\/cdn.site.com\/image.png"}</script>
							//<script type="application/ld+json">{"logo":"\/\/cdn.site.com\/image.png"}</script>
							//<script type="application/ld+json">{"image":"\/\/cdn.site.com\/image.jpg"}</script>
						}else{
							//<script>var loaderRandomImages=["https:\/\/www.site.com\/wp-content\/uploads\/2016\/12\/image.jpg"];</script>
							$matches[0] = preg_replace("/\\\\\//", "/", $matches[0]);
							
							if(preg_match("/".preg_quote($cdn->originurl, "/")."/", $matches[0])){
								$matches[0] = preg_replace("/(http(s?)\:)?\/\/(www\.)?".preg_quote($cdn->originurl, "/")."/i", $cdnurl, $matches[0]);
								$matches[0] = preg_replace("/\//", "\/", $matches[0]);
							}
						}
					}
				}
			}

			return $matches[0];
		}

		public function read_file($url){
			if(!preg_match("/\.php/", $url)){
				$url = preg_replace("/\?.*/", "", $url);

				if(preg_match("/wp-content/", $url)){
					$path = preg_replace("/.+\/wp-content\/(.+)/", WPFC_WP_CONTENT_DIR."/"."$1", $url);
				}else if(preg_match("/wp-includes/", $url)){
					$path = preg_replace("/.+\/wp-includes\/(.+)/", ABSPATH."wp-includes/"."$1", $url);
				}

				if(isset($path)){
					if(@file_exists($path)){
						$filesize = filesize($path);

						if($filesize > 0){
							$myfile = fopen($path, "r") or die("Unable to open file!");
							$data = fread($myfile, $filesize);
							fclose($myfile);

							return $data;
						}else{
							return false;
						}
					}
				}


			}

			return false;
		}

		public function get_operating_systems(){
			$operating_systems  = array(
									'Android',
									'blackberry|\bBB10\b|rim\stablet\sos',
									'PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino',
									'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b',
									'Windows\sCE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window\sMobile|Windows\sPhone\s[0-9.]+|WCE;',
									'Windows\sPhone\s10.0|Windows\sPhone\s8.1|Windows\sPhone\s8.0|Windows\sPhone\sOS|XBLWP7|ZuneWP7|Windows\sNT\s6\.[23]\;\sARM\;',
									'\biPhone.*Mobile|\biPod|\biPad',
									'Apple-iPhone7C2',
									'MeeGo',
									'Maemo',
									'J2ME\/|\bMIDP\b|\bCLDC\b', // '|Java/' produces bug #135
									'webOS|hpwOS',
									'\bBada\b',
									'BREW'
							    );
			return $operating_systems;
		}

		public function get_mobile_browsers(){
			$mobile_browsers  = array(
								'\bCrMo\b|CriOS|Android.*Chrome\/[.0-9]*\s(Mobile)?',
								'\bDolfin\b',
								'Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR\/[0-9.]+|Coast\/[0-9.]+',
								'Skyfire',
								'Mobile\sSafari\/[.0-9]*\sEdge',
								'IEMobile|MSIEMobile', // |Trident/[.0-9]+
								'fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile|FxiOS',
								'bolt',
								'teashark',
								'Blazer',
								'Version.*Mobile.*Safari|Safari.*Mobile|MobileSafari',
								'Tizen',
								'UC.*Browser|UCWEB',
								'baiduboxapp',
								'baidubrowser',
								'DiigoBrowser',
								'Puffin',
								'\bMercury\b',
								'Obigo',
								'NF-Browser',
								'NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger',
								'Android.*PaleMoon|Mobile.*PaleMoon'
							    );
			return $mobile_browsers;
		}


	}

	// Load WP CLI command(s) on demand.
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
	    require_once "inc/cli.php";
	}

	function wpfc_clear_all_site_cache(){
		do_action("wpfc_clear_all_cache");
	}

	function wpfc_clear_all_cache($minified = false){
		do_action("wpfc_clear_all_cache", $minified);
	}

	function wpfc_exclude_current_page(){
		do_action("wpfc_exclude_current_page");
	}

	function wpfc_clear_post_cache_by_id($post_id = false){
		if($post_id){
			do_action("wpfc_clear_post_cache_by_id", false, $post_id);
		}
	}

	$GLOBALS["wp_fastest_cache"] = new WpFastestCache();
?>