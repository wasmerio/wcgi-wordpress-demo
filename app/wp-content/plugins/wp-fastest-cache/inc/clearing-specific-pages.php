<?php
	class ClearingSpecificPagesWPFC{

		public static function remove(){
			if(!wp_verify_nonce($_POST["security"], 'wpfc-save-csp-ajax-nonce')){
				die( 'Security check' );
			}

			$_POST["order"] = sanitize_text_field($_POST["order"]);

			$urls = get_option("WpFastestCacheCSP");

			if(!empty($urls)){
				foreach ($urls as $key => $value) {
					if($value->order == ($_POST["order"])){
						unset($urls[$key]);
					}
				}
			}

			if(empty($urls)){
				delete_option("WpFastestCacheCSP");
			}else{
				update_option("WpFastestCacheCSP", $urls, 1, "no");
			}

			wp_send_json_success();
		}

		public static function check_url(){
			$home_url = parse_url(get_option("home"), PHP_URL_HOST);
			$specific_url = parse_url($_POST["url"], PHP_URL_HOST);

			if($home_url == $specific_url){
				return true;
			}

			return false;
		}

		public static function check_wild_card(){
			if(preg_match("/[^\/]\(\.\*\)/", $_POST["url"])){
				return false;
			}

			if(substr_count($_POST["url"], "(.*)") > 1){
				return false;
			}

			return true;
		}

		public static function save(){
			if(!wp_verify_nonce($_POST["security"], 'wpfc-save-csp-ajax-nonce')){
				die( 'Security check' );
			}

			if(!self::check_url()){
				wp_send_json_error("The URL must start with ".parse_url(get_option("home"), PHP_URL_SCHEME)."//".parse_url(get_option("home"), PHP_URL_HOST));
			}

			if(!self::check_wild_card()){
				wp_send_json_error("Wrong Wild Card Usage");
			}

			$_POST["url"] = sanitize_url($_POST["url"]);
			$_POST["order"] = sanitize_text_field($_POST["order"]);

			$urls = get_option("WpFastestCacheCSP");
			$url = (object)array("url" => $_POST["url"], "order" => $_POST["order"]);

			if(!is_array($urls)){
				$urls = array();

				array_push($urls, $url);

				add_option("WpFastestCacheCSP", $urls, 1, "no");
			}else{
				$is_update = false;

				foreach ($urls as $key => &$value) {
					if($value->order == ($_POST["order"])){
						$is_update = true;
						$value->url = $_POST["url"];
					}
				}

				if(!$is_update){
					array_push($urls, $url);
				}

				update_option("WpFastestCacheCSP", $urls, 1, "no");
			}

			wp_send_json_success();
		}

		public static function get_list(){
			if(!wp_verify_nonce($_POST["security"], 'wpfc-save-csp-ajax-nonce')){
				die( 'Security check' );
			}

			$urls = get_option("WpFastestCacheCSP");

			if(!is_array($urls)){
				$urls = array();
			}

			wp_send_json_success($urls);

		}

	}
?>