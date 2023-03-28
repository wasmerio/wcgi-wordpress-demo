<?php
	class WpFastestCacheColumn{
		public function __construct(){}

		public function add(){
			add_filter('post_row_actions', array($this, 'add_clear_cache_link'), 10, 2);
			add_filter('page_row_actions', array($this, 'add_clear_cache_link'), 10, 2);


   			add_action('admin_enqueue_scripts', array($this, 'load_js'));
   			add_action('wp_ajax_wpfc_clear_cache_column', array($this, "clear_cache_column"));
		}

		public function add_clear_cache_link($actions, $post){
			$actions['clear_cache_link'] = '<a data-id="'.$post->ID.'" data-nonce="'.wp_create_nonce('clear-cache_'.$post->ID).'" id="wpfc-clear-cache-link-'.$post->ID.'" style="cursor:pointer;">' . __('Clear Cache') . '</a>';
			return $actions;
		}

		public function clear_cache_column(){
			if(wp_verify_nonce($_GET["nonce"], 'clear-cache_'.$_GET["id"])){
				$GLOBALS["wp_fastest_cache"]->singleDeleteCache(false, esc_sql($_GET["id"]));

				die(json_encode(array("success" => true)));
			}else{
				die(json_encode(array("success" => false)));
			}
		}

		public function load_js(){
			wp_enqueue_script("wpfc-column", plugins_url("wp-fastest-cache/js/column.js"), array(), time(), true);
		}
	}
?>