<?php
	class VarnishWPFC{

		public static function purge_cache($data = false) {
			if(isset($GLOBALS["wpfc_varnish_purge_cache_executed"])){
				return;
			}else{
				$GLOBALS["wpfc_varnish_purge_cache_executed"] = true;
			}

			

			if(gettype($data) == "array"){
				// Clearing page cache action sends the data as Array
				if(isset($data["status"]) && $data["status"] == "pause"){
					return array("success" => true);
				}

				$server = $data["server"];
			}else{
				// Ajax request and save() function send the data as string
				$server = $data;
			}

			$home = get_option('home');
			$host = preg_replace("/(https?\:\/\/)(.+)/", "$2", $home);

			$schema = preg_replace("/(https?\:\/\/).+/", "$1", $home);
			$schema = strtolower($schema);

			$ssl_verification = $schema == 'https://' ? true : false;

			$request_url = $schema.$server."/.*";

			$request_args = array(
				'method'    => "PURGE",
				'headers'   => array(
					'Host'       => $host,
					'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
				),
				'sslverify' => $ssl_verification,
			);

			$response = wp_remote_request($request_url, $request_args );

			

			if(is_wp_error( $response ) || $response['response']['code'] != '200'){
				if($schema === 'https://'){
					$request_url = str_replace("https://", "http://", $request_url);
				}else{
					$request_url = str_replace("http://", "https://", $request_url);
				}

				$response = wp_remote_request($request_url, $request_args );

				if(is_wp_error( $response ) || $response['response']['code'] != '200'){
					return array("success" => "", "message" => $response->get_error_message());
				}
			}

			return array("success" => true);
		}

		public static function save(){
			if(!wp_verify_nonce($_POST["security"], 'wpfc-varnish-ajax-nonce')){
				die( 'Security check' );
			}

			$_POST["server"] = sanitize_text_field($_POST["server"]);

			$purce_res = self::purge_cache($_POST["server"]);

			if(!$purce_res["success"]){
				wp_send_json($purce_res);
			}

			$datas = get_option("WpFastestCacheVarnish");

			if(!is_array($datas)){
				$datas = array();
				$datas["server"] = $_POST["server"];

				add_option("WpFastestCacheVarnish", $datas, 1, "yes");
			}else{
				$datas["server"] = $_POST["server"];

				update_option("WpFastestCacheVarnish", $datas, 1, "yes");
			}

			wp_send_json_success();


		}

		public static function status(){
			$datas = get_option("WpFastestCacheVarnish");

			if(is_array($datas)){
				if(isset($datas["status"]) && $datas["status"] == "pause"){
					echo "isConnected pause";
				}else{
					echo "isConnected";
				}
			}
		}

		public static function start(){
			if(!wp_verify_nonce($_POST["security"], 'wpfc-varnish-ajax-nonce')){
				die( 'Security check' );
			}

			$datas = get_option("WpFastestCacheVarnish");

			if(is_array($datas)){
				unset($datas["status"]);

				$purce_res = self::purge_cache($datas);

				if(!$purce_res["success"]){
					wp_send_json($purce_res);
				}

				update_option("WpFastestCacheVarnish", $datas, 1, "yes");
			}

			wp_send_json_success();
		}

		public static function pause(){
			if(!wp_verify_nonce($_POST["security"], 'wpfc-varnish-ajax-nonce')){
				die( 'Security check' );
			}

			$datas = get_option("WpFastestCacheVarnish");

			if(is_array($datas)){
				$datas["status"] = "pause";
				update_option("WpFastestCacheVarnish", $datas, 1, "yes");
			}

			wp_send_json_success();
		}

		public static function remove(){
			if(!wp_verify_nonce($_POST["security"], 'wpfc-varnish-ajax-nonce')){
				die( 'Security check' );
			}

			delete_option("WpFastestCacheVarnish");

			wp_send_json_success();
		}

	}

?>