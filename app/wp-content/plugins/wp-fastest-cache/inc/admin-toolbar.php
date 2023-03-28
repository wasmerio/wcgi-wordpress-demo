<?php
	class WpFastestCacheAdminToolbar{
		private $is_multi = false;

		public function __construct($is_multi){
			$this->is_multi = $is_multi;
		}

		public function add(){
			if(is_admin()){
				add_action('wp_before_admin_bar_render', array($this, "wpfc_tweaked_toolbar_on_admin_panel"));
				add_action('admin_enqueue_scripts', array($this, 'load_toolbar_js'));
				add_action('admin_enqueue_scripts', array($this, 'load_toolbar_css'));
			}else{
				if(is_admin_bar_showing()){
					add_action('wp_before_admin_bar_render', array($this, "wpfc_tweaked_toolbar_on_frontpage"));
					add_action('wp_enqueue_scripts', array($this, 'load_toolbar_js'));
					add_action('wp_enqueue_scripts', array($this, 'load_toolbar_css'));
					add_action('wp_footer', array($this, 'print_my_inline_script'));
				}
			}
		}

		public function load_toolbar_js(){
			wp_enqueue_script("wpfc-toolbar", plugins_url("wp-fastest-cache/js/toolbar.js"), array('jquery'), time(), true);
		}

		public function load_toolbar_css(){
			wp_enqueue_style("wp-fastest-cache-toolbar", plugins_url("wp-fastest-cache/css/toolbar.css"), array(), time(), "all");
		}

		public function print_my_inline_script() {
			?>
			<script type="text/javascript">
				var wpfc_ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
				var wpfc_nonce = "<?php echo wp_create_nonce("wpfc"); ?>";
			</script>
			<?php
		}

		public function wpfc_tweaked_toolbar_on_frontpage() {
			global $wp_admin_bar;

			$wp_admin_bar->add_node(array(
				'id'    => 'wpfc-toolbar-parent',
				'title' => 'Clear Cache'
			));

			$wp_admin_bar->add_menu( array(
				'id'    => 'wpfc-toolbar-parent-clear-cache-of-this-page',
				'title' => 'Clear Cache of This Page',
				'parent'=> 'wpfc-toolbar-parent',
				'meta' => array("class" => "wpfc-toolbar-child")
			));

			$wp_admin_bar->add_menu( array(
				'id'    => 'wpfc-toolbar-parent-delete-cache',
				'title' => 'Delete Cache',
				'parent'=> 'wpfc-toolbar-parent',
				'meta' => array("class" => "wpfc-toolbar-child")
			));

			$wp_admin_bar->add_menu( array(
				'id'    => 'wpfc-toolbar-parent-delete-cache-and-minified',
				'title' => 'Delete Cache and Minified CSS/JS',
				'parent'=> 'wpfc-toolbar-parent',
				'meta' => array("class" => "wpfc-toolbar-child")
			));

			if($this->is_multi){
				$wp_admin_bar->add_menu( array(
					'id'    => 'wpfc-toolbar-parent-clear-cache-of-allsites',
					'title' => __("Clear Cache of All Sites", "wp-fastest-cache"),
					'parent'=> 'wpfc-toolbar-parent',
					'meta' => array("class" => "wpfc-toolbar-child")
				));
			}
		}

		public function wpfc_tweaked_toolbar_on_admin_panel() {
			global $wp_admin_bar;

			$wp_admin_bar->add_node(array(
				'id'    => 'wpfc-toolbar-parent',
				'title' => __("Delete Cache", "wp-fastest-cache"),
			));

			$wp_admin_bar->add_menu( array(
				'id'    => 'wpfc-toolbar-parent-delete-cache',
				'title' => __("Clear All Cache", "wp-fastest-cache"),
				'parent'=> 'wpfc-toolbar-parent',
				'meta' => array("class" => "wpfc-toolbar-child")
			));

			$wp_admin_bar->add_menu( array(
				'id'    => 'wpfc-toolbar-parent-delete-cache-and-minified',
				'title' => __("Delete Cache and Minified CSS/JS", "wp-fastest-cache"),
				'parent'=> 'wpfc-toolbar-parent',
				'meta' => array("class" => "wpfc-toolbar-child")
			));

			if($this->is_multi){
				$wp_admin_bar->add_menu( array(
					'id'    => 'wpfc-toolbar-parent-clear-cache-of-allsites',
					'title' => __("Clear Cache of All Sites", "wp-fastest-cache"),
					'parent'=> 'wpfc-toolbar-parent',
					'meta' => array("class" => "wpfc-toolbar-child")
				));
			}else{
				if(isset($_GET["page"]) && $_GET["page"] == "wpfastestcacheoptions"){
					$wp_admin_bar->add_menu( array(
						'id'    => 'wpfc-toolbar-parent-settings',
						'title' => __("Settings", "wp-fastest-cache"),
						'parent'=> 'wpfc-toolbar-parent',
						'meta' => array("class" => "wpfc-toolbar-child")
					));
				}
			}
		}
	}
?>