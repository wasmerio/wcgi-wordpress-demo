<?php

add_action( 'wp_head', function () {
	?>
    <script>
        (function (exports, d) {
            var _isReady = false,
                _event,
                _fns = [];

            function onReady(event) {
                d.removeEventListener("DOMContentLoaded", onReady);
                _isReady = true;
                _event = event;
                _fns.forEach(function (_fn) {
                    var fn = _fn[0],
                        context = _fn[1];
                    fn.call(context || exports, window.jQuery);
                });
            }

            function onReadyIe(event) {
                if (d.readyState === "complete") {
                    d.detachEvent("onreadystatechange", onReadyIe);
                    _isReady = true;
                    _event = event;
                    _fns.forEach(function (_fn) {
                        var fn = _fn[0],
                            context = _fn[1];
                        fn.call(context || exports, event);
                    });
                }
            }

            d.addEventListener && d.addEventListener("DOMContentLoaded", onReady) ||
            d.attachEvent && d.attachEvent("onreadystatechange", onReadyIe);

            function domReady(fn, context) {
                if (_isReady) {
                    fn.call(context, _event);
                }

                _fns.push([fn, context]);
            }

            exports.mesmerizeDomReady = domReady;
        })(window, document);
    </script>
	<?php
}, 0 );

mesmerize_require( "inc/variables.php" );
mesmerize_require( "inc/defaults.php" );
mesmerize_require( "inc/theme-cache-cleaner.php" );


add_filter( 'mesmerize_upgrade_url', function ( $url ) {
	return get_option( 'mesmerize_upgrade_url', $url );
}, 0 );

if ( ! function_exists( 'mesmerize_get_upgrade_link' ) ) {
	function mesmerize_get_upgrade_link( $args = array(), $hash = "" ) {
		$base_url = "https://extendthemes.com/go/mesmerize-upgrade/";
		$url      = add_query_arg( $args, $base_url );

		if ( $hash = trim( $hash ) ) {
			$hash = "#" . $hash;
		}

		$url = $url . esc_url( $hash );

		return apply_filters( 'mesmerize_upgrade_url', $url, $base_url, $args, $hash );
	}
}


function mesmerize_get_companion_data( $key ) {

	$plugin_path = WP_PLUGIN_DIR . '/mesmerize-companion/mesmerize-companion.php';

	if ( file_exists( $plugin_path ) ) {

		if ( ! function_exists( 'get_plugin_data' ) ) {
			include_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$data = get_plugin_data( $plugin_path, false );

		if ( isset( $data[ $key ] ) ) {
			return $data[ $key ];
		}
	}

	return null;
}

if ( ! apply_filters( 'mesmerize_is_companion_installed', false ) ) {
	add_action( 'wp_ajax_cp_load_data', 'mesmerize_load_ajax_data' );
}

function mesmerize_load_ajax_data() {
	$filter      = filter_input( INPUT_GET, 'filter', FILTER_SANITIZE_STRING );
	$filter      = trim( $filter );
	$filterParts = explode( ".", $filter );

	if ( empty( $filterParts ) ) {
		wp_send_json( array( "error" => "empty_filter" ) );
	}

	$companion = null;

	$result = apply_filters( 'cloudpress\companion\ajax_cp_data', array(), $companion, $filter );

	if ( ! isset( $result[ $filter ] ) ) {
		wp_send_json( array( "error" => "invalid_filter" ) );
	}

	wp_send_json( $result[ $filter ] );
}

function mesmerize_set_in_memory( $key, $value = false ) {

	if ( ! isset( $GLOBALS['MESMERIZE_MEMORY_CACHE'] ) ) {
		$GLOBALS['MESMERIZE_MEMORY_CACHE'] = array();
	}

	$GLOBALS['MESMERIZE_MEMORY_CACHE'][ $key ] = $value;
}

function mesmerize_has_in_memory( $key ) {

	if ( isset( $GLOBALS['MESMERIZE_MEMORY_CACHE'] ) && isset( $GLOBALS['MESMERIZE_MEMORY_CACHE'][ $key ] ) ) {
		return $key;
	} else {
		return false;
	}
}

function mesmerize_get_from_memory( $key ) {
	if ( mesmerize_has_in_memory( $key ) ) {
		return $GLOBALS['MESMERIZE_MEMORY_CACHE'][ $key ];
	}

	return false;
}

function mesmerize_skip_customize_register() {
	return isset( $_REQUEST['mesmerize_skip_customize_register'] );
}

function mesmerize_get_cache_option_key() {
	return "__mesmerize_cached_values__";
}

function mesmerize_can_show_cached_value( $slug ) {
	global $wp_customize;

	if ( $wp_customize || mesmerize_is_customize_preview() || wp_doing_ajax() || WP_DEBUG || ! mesmerize_is_modified() ) {
		return false;
	}

	if ( $value = mesmerize_get_from_memory( "mesmerize_can_show_cached_value_{$slug}" ) ) {
		return $value;
	}

	$result = ( mesmerize_get_cached_value( $slug ) !== null );

	mesmerize_set_in_memory( "mesmerize_can_show_cached_value_{$slug}", $result );

	return $result;
}

function mesmerize_cache_value( $slug, $value, $cache_on_ajax = false ) {

	if ( wp_doing_ajax() ) {
		if ( ! $cache_on_ajax ) {
			return;
		}
	}

	if ( mesmerize_is_customize_preview() ) {
		return;
	}

	$cached_values = get_option( mesmerize_get_cache_option_key(), array() );

	$cached_values[ $slug ] = $value;

	update_option( mesmerize_get_cache_option_key(), $cached_values, 'yes' );

}

function mesmerize_remove_cached_value( $slug ) {
	$cached_values = get_option( mesmerize_get_cache_option_key(), array() );

	if ( isset( $cached_values[ $slug ] ) ) {
		unset( $cached_values[ $slug ] );
	}

	update_option( mesmerize_get_cache_option_key(), $cached_values, 'yes' );
}

function mesmerize_get_cached_value( $slug ) {
	$cached_values = get_option( mesmerize_get_cache_option_key(), array() );

	if ( isset( $cached_values[ $slug ] ) ) {
		return $cached_values[ $slug ];
	}

	return null;
}

function mesmerize_clear_cached_values() {
	// cleanup old cached values
	$slugs = get_option( 'mesmerize_cached_values_slugs', array() );

	if ( count( $slugs ) ) {
		foreach ( $slugs as $slug ) {
			mesmerize_remove_cached_value( $slug );
		}

		delete_option( 'mesmerize_cached_values_slugs' );
	}
	// cleanup old cached values

	delete_option( mesmerize_get_cache_option_key() );

	if ( class_exists( 'autoptimizeCache' ) ) {
		autoptimizeCache::clearall();
	}
}

add_action( 'cloudpress\companion\clear_caches', 'mesmerize_clear_cached_values' );

function mesmerize_get_var( $name ) {
	global $mesmerize_variables;

	return $mesmerize_variables[ $name ];
}

function mesmerize_wrap_with_single_quote( $element ) {
	return "&apos;{$element}&apos;";
}

function mesmerize_wrap_with_double_quotes( $element ) {
	return "&quot;{$element}&quot;";
}

function mesmerize_wp_kses_post( $text ) {
	// fix the issue with rgb / rgba colors in style atts

	$rgbRegex = "#rgb\(((?:\s*\d+\s*,){2}\s*[\d]+)\)#i";
	$text     = preg_replace( $rgbRegex, "rgb__$1__rgb", $text );

	$rgbaRegex = "#rgba\(((\s*\d+\s*,){3}[\d\.]+)\)#i";
	$text      = preg_replace( $rgbaRegex, "rgba__$1__rgb", $text );

	// fix google fonts
	$fontsOption       = apply_filters( 'mesmerize_google_fonts', mesmerize_get_general_google_fonts() );
	$fonts             = array_keys( $fontsOption );
	$singleQuotedFonts = array_map( 'mesmerize_wrap_with_single_quote', $fonts );
	$doubleQuotedFonts = array_map( 'mesmerize_wrap_with_double_quotes', $fonts );

	$text = str_replace( $singleQuotedFonts, $fonts, $text );
	$text = str_replace( $doubleQuotedFonts, $fonts, $text );

	$text = wp_kses_post( $text );

	$text = str_replace( "rgba__", "rgba(", $text );
	$text = str_replace( "rgb__", "rgb(", $text );
	$text = str_replace( "__rgb", ")", $text );

	return $text;
}

/**
 * wrapper over esc_url with small fixes
 */
function mesmerize_esc_url( $url ) {
	$url = str_replace( "^", "%5E", $url ); // fix ^ in file name before escape

	return esc_url( $url );
}

function mesmerize_setup() {
	global $content_width;

	if ( ! isset( $content_width ) ) {
		$content_width = 3840; // 4k :) - content width should be adapted from css not hardcoded
	}

	load_theme_textdomain( 'mesmerize', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );

	set_post_thumbnail_size( 1024, 0, false );

	register_default_headers( array(
		'homepage-image' => array(
			'url'           => apply_filters( 'mesmerize_assets_url', '%s/assets/images/home_page_header.jpg',
				'/assets/images/home_page_header.jpg' ),
			'thumbnail_url' => apply_filters( 'mesmerize_assets_url', '%s/assets/images/home_page_header.jpg',
				'/assets/images/home_page_header.jpg' ),
			'description'   => esc_html__( 'Homepage Header Image', 'mesmerize' ),
		),
	) );

	add_theme_support( 'custom-header', apply_filters( 'mesmerize_custom_header_args', array(
		'default-image' => mesmerize_mod_default( 'inner_header_front_page_image' ),
		'width'         => 1920,
		'height'        => 800,
		'flex-height'   => true,
		'flex-width'    => true,
		'header-text'   => false,
	) ) );

	add_theme_support( 'custom-logo', array(
		'flex-height' => true,
		'flex-width'  => true,
		'width'       => 150,
		'height'      => 70,
	) );

	add_theme_support( 'customize-selective-refresh-widgets' );

	add_image_size( 'mesmerize-full-hd', 1920, 1080 );

	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'mesmerize' ),
	) );

	include_once get_template_directory() . '/customizer/kirki/kirki.php';

	Kirki::add_config( 'mesmerize', array(
		'capability'  => 'edit_theme_options',
		'option_type' => 'theme_mod',
	) );

	mesmerize_theme_page();
	mesmerize_suggest_plugins();

	mesmerize_require( "inc/wp-forms/wp-forms.php" );
}

add_action( 'after_setup_theme', 'mesmerize_setup' );

function mesmerize_full_hd_image_size_label( $sizes ) {
	return array_merge( $sizes, array(
		'mesmerize-full-hd' => __( 'Full HD', 'mesmerize' ),
	) );
}

add_filter( 'image_size_names_choose', 'mesmerize_full_hd_image_size_label' );

function mesmerize_suggest_plugins() {

	require_once get_template_directory() . '/inc/companion.php';

	/* tgm-plugin-activation */
	require_once get_template_directory() . '/class-tgm-plugin-activation.php';


	$companion_description = esc_html__( 'Mesmerize Companion plugin adds drag and drop functionality and many other features to the Mesmerize theme.',
		'mesmerize' );

	$plugins = array(
		'mesmerize-companion' => array(
			'title'       => esc_html__( 'Mesmerize Companion', 'mesmerize' ),
			'description' => apply_filters( 'mesmerize_companion_description', $companion_description ),
			'activate'    => array(
				'label' => esc_html__( 'Activate', 'mesmerize' ),
			),
			'install'     => array(
				'label' => esc_html__( 'Install', 'mesmerize' ),
			),
		),
		'wpforms-lite'        => array(
			'title'       => esc_html__( 'Contact Form by WPForms', 'mesmerize' ),
			'description' => esc_html__( 'The Contact Form by WPForms plugin is recommended for the Mesmerize contact section.',
				'mesmerize' ),
			'activate'    => array(
				'label' => esc_html__( 'Activate', 'mesmerize' ),
			),
			'install'     => array(
				'label' => esc_html__( 'Install', 'mesmerize' ),
			),
		),
	);
	$plugins = apply_filters( 'mesmerize_theme_info_plugins', $plugins );
	\Mesmerize\Companion_Plugin::init( array(
		'slug'           => 'mesmerize-companion',
		'activate_label' => esc_html__( 'Activate Mesmerize Companion', 'mesmerize' ),
		'activate_msg'   => esc_html__( 'This feature requires the Mesmerize Companion plugin to be activated.',
			'mesmerize' ),
		'install_label'  => esc_html__( 'Install Mesmerize Companion', 'mesmerize' ),
		'install_msg'    => esc_html__( 'This feature requires the Mesmerize Companion plugin to be installed.',
			'mesmerize' ),
		'plugins'        => $plugins,
	) );
}

function mesmerize_tgma_suggest_plugins() {
	$plugins = array(
		array(
			'name'             => 'Mesmerize Companion',
			'slug'             => 'mesmerize-companion',
			'required'         => apply_filters( 'mesmerize_require_companion_plugin', '__return_false' ),
			'force_activation' => apply_filters( 'mesmerize_force_activation_companion_plugin', '__return_false' ),
			'is_automatic'     => apply_filters( 'mesmerize_is_automatic_companion_plugin', '__return_false' ),
		),

		array(
			'name'     => 'Contact Form by WPForms',
			'slug'     => 'wpforms-lite',
			'required' => false,
		),
	);

	$plugins = apply_filters( 'mesmerize_tgmpa_plugins', $plugins );

	$config = array(
		'id'           => 'mesmerize',
		'default_path' => '',
		'menu'         => 'tgmpa-install-plugins',
		'has_notices'  => false,
		'dismissable'  => true,
		'dismiss_msg'  => '',
		'is_automatic' => false,
		'message'      => '',
	);

	$config = apply_filters( 'mesmerize_tgmpa_config', $config );

	tgmpa( $plugins, $config );
}

add_action( 'tgmpa_register', 'mesmerize_tgma_suggest_plugins' );

function mesmerize_can_show_demo_content() {
	global $wp_customize;

	if ( $wp_customize || mesmerize_is_customize_preview() ) {
		return true;
	}

	return apply_filters( "mesmerize_can_show_demo_content", current_user_can( 'edit_theme_options' ) );
}

function mesmerize_get_version() {
	$theme = wp_get_theme();

	if ( $theme->get( 'Template' ) ) {
		$theme = wp_get_theme( $theme->get( 'Template' ) );
	}

	$ver = $theme->get( 'Version' );
	$ver = apply_filters( 'mesmerize_get_version', $ver );

	if ( $ver === '@@buildnumber@@' ) {
		$ver = "99.99." . time();
	}

	return $ver;
}

function mesmerize_get_text_domain() {
	$theme = wp_get_theme();

	$textDomain = $theme->get( 'TextDomain' );

	if ( $theme->get( 'Template' ) ) {
		$templateData = wp_get_theme( $theme->get( 'Template' ) );
		$textDomain   = $templateData->get( 'TextDomain' );
	}

	return $textDomain;
}

function mesmerize_file_exists( $path ) {
	$path = trim( $path, "\\/" );

	if ( file_exists( get_template_directory() . "/{$path}" ) ) {
		return get_template_directory() . "/{$path}";
	}

	return null;
}

function mesmerize_require( $path ) {
	$path = mesmerize_file_exists( $path );
	if ( $path !== null ) {
		require_once $path;

	}
}

if ( ! class_exists( "Kirki" ) ) {
	include_once get_template_directory() . '/customizer/kirki/kirki.php';
}

mesmerize_require( '/inc/templates-functions.php' );
mesmerize_require( '/inc/theme-options.php' );

function mesmerize_add_kirki_field( $args ) {
	$has_cached_values = mesmerize_can_show_cached_value( "mesmerize_cached_kirki_style_mesmerize" );

	if ( ! $has_cached_values ) {
		$args = apply_filters( 'mesmerize_kirki_field_filter', $args );
		Kirki::add_field( 'mesmerize', $args );
	}
}

// SCRIPTS AND STYLES

function mesmerize_replace_file_extension( $filename, $old_extenstion, $new_extension ) {

	return preg_replace( '#\\' . $old_extenstion . '$#', $new_extension, $filename );
}

function mesmerize_enqueue( $handle, $type = 'style', $args = array() ) {
	$theme = wp_get_theme();
	$ver   = $theme->get( 'Version' );
	$data  = array_merge( array(
		'src'        => '',
		'deps'       => array(),
		'has_min'    => false,
		'in_footer'  => true,
		'media'      => 'all',
		'ver'        => $ver,
		'in_preview' => true,
	), $args );

	if ( mesmerize_is_customize_preview() && $data['in_preview'] === false ) {
		return;
	}

	if ( $data['has_min'] ) {
		if ( $type === 'style' ) {
			$data['src'] = mesmerize_replace_file_extension( $data['src'], '.css', '.min.css' );
		}

		if ( $type === 'script' ) {
			$data['src'] = mesmerize_replace_file_extension( $data['src'], '.js', '.min.js' );
		}
	}

	if ( $type == 'style' ) {
		wp_enqueue_style( $handle, $data['src'], $data['deps'], $data['ver'], $data['media'] );
	}

	if ( $type == 'script' ) {
		wp_enqueue_script( $handle, $data['src'], $data['deps'], $data['ver'], $data['in_footer'] );
	}

}

function mesmerize_enqueue_style( $handle, $args ) {
	mesmerize_enqueue( $handle, 'style', $args );
}

function mesmerize_enqueue_script( $handle, $args ) {
	mesmerize_enqueue( $handle, 'script', $args );
}

function mesmerize_associative_array_splice( $oldArray, $offset, $key, $data ) {
	$newArray = array_slice( $oldArray, 0, $offset, true ) +
	            array( $key => $data ) +
	            array_slice( $oldArray, $offset, null, true );

	return $newArray;
}

function mesmerize_enqueue_styles( $textDomain, $ver, $is_child ) {

	mesmerize_enqueue_style(
		$textDomain . '-style',
		array(
			'src'     => get_stylesheet_uri(),
			'has_min' => apply_filters( 'mesmerize_stylesheet_has_min', ! $is_child ),
			'deps'    => apply_filters( 'mesmerize_stylesheet_deps', array() ),
		)
	);

	if ( apply_filters( 'mesmerize_load_bundled_version', true ) ) {

		mesmerize_enqueue_style(
			$textDomain . '-style-bundle',
			array(
				'src' => get_template_directory_uri() . '/assets/css/theme.bundle.min.css',
			)
		);

	} else {

		mesmerize_enqueue_style(
			$textDomain . '-font-awesome',
			array(
				'src' => get_template_directory_uri() . '/assets/font-awesome/font-awesome.min.css',
			)
		);

		mesmerize_enqueue_style(
			'animate',
			array(
				'src'     => get_template_directory_uri() . '/assets/css/animate.css',
				'has_min' => true,
			)
		);

		mesmerize_enqueue_style(
			$textDomain . '-webgradients',
			array(
				'src'     => get_template_directory_uri() . '/assets/css/webgradients.css',
				'has_min' => true,
			)
		);
	}
}

function mesmerize_defer_js_scripts( $tag ) {
	$matches = array(
		'theme.bundle.min.js',
		'companion.bundle.min.js',
		includes_url( '/js/masonry.min.js' ),
		includes_url( '/js/imagesloaded.min.js' ),
		includes_url( '/js/wp-embed.min.js' ),
	);

	foreach ( $matches as $match ) {
		if ( strpos( $tag, $match ) !== false ) {
			return str_replace( 'src', ' defer="defer" src', $tag );
		}
	}

	return $tag;

}

add_filter( 'script_loader_tag', 'mesmerize_defer_js_scripts', 11, 1 );

function mesmerize_defer_css_scripts( $tag ) {
	$matches = array(
		'fonts.googleapis.com',
		'companion.bundle.min.css',
	);

	if ( ! mesmerize_is_customize_preview() ) {
		foreach ( $matches as $match ) {
			if ( strpos( $tag, $match ) !== false ) {
				return str_replace( 'href', 'href="" data-href', $tag );
			}
		}
	}

	return $tag;
}

add_filter( 'style_loader_tag', 'mesmerize_defer_css_scripts', 11, 1 );

add_action( 'wp_head', function () {
	?>
    <script type="text/javascript" data-name="async-styles">
        (function () {
            var links = document.querySelectorAll('link[data-href]');
            for (var i = 0; i < links.length; i++) {
                var item = links[i];
                item.href = item.getAttribute('data-href')
            }
        })();
    </script>
	<?php
} );

function mesmerize_enqueue_scripts( $textDomain, $ver, $is_child ) {

	if ( apply_filters( 'mesmerize_load_bundled_version', true ) ) {
		$theme_deps = array( 'jquery', 'masonry' );
		mesmerize_enqueue_script(
			$textDomain . '-theme',
			array(
				"src"  => get_template_directory_uri() . '/assets/js/theme.bundle.min.js',
				"deps" => $theme_deps,
			)
		);

	} else {

		mesmerize_enqueue_script(
			$textDomain . '-smoothscroll',
			array(
				'src'     => get_template_directory_uri() . '/assets/js/smoothscroll.js',
				'deps'    => array( 'jquery', 'jquery-effects-core' ),
				'has_min' => true,
			)
		);

		mesmerize_enqueue_script(
			$textDomain . '-ddmenu',
			array(
				'src'     => get_template_directory_uri() . '/assets/js/drop_menu_selection.js',
				'deps'    => array( 'jquery-effects-slide', 'jquery' ),
				'has_min' => true,
			)
		);

		mesmerize_enqueue_script(
			'kube',
			array(
				'src'     => get_template_directory_uri() . '/assets/js/kube.js',
				'deps'    => array( 'jquery' ),
				'has_min' => true,
			)
		);

		mesmerize_enqueue_script(
			$textDomain . '-fixto',
			array(
				'src'     => get_template_directory_uri() . '/assets/js/libs/fixto.js',
				'deps'    => array( 'jquery' ),
				'has_min' => true,
			)
		);

		wp_enqueue_script( $textDomain . '-sticky', get_template_directory_uri() . '/assets/js/sticky.js',
			array( $textDomain . '-fixto' ), $ver, true );

		$theme_deps = array( 'jquery', 'masonry' );
		$theme_deps = apply_filters( "mesmerize_theme_deps", $theme_deps );

		mesmerize_enqueue_script(
			$textDomain . '-theme',
			array(
				"src"     => get_template_directory_uri() . '/assets/js/theme.js',
				"deps"    => $theme_deps,
				'has_min' => true,
			)
		);
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

function mesmerize_do_enqueue_assets() {

	$theme        = wp_get_theme();
	$ver          = $theme->get( 'Version' );
	$isChildTheme = $theme->get( 'Template' );
	$textDomain   = mesmerize_get_text_domain();

	mesmerize_enqueue_styles( $textDomain, $ver, $isChildTheme );
	mesmerize_enqueue_scripts( $textDomain, $ver, $isChildTheme );

	$maxheight = intval( get_theme_mod( 'logo_max_height', 70 ) );
	wp_add_inline_style( $textDomain . '-style',
		sprintf( 'img.logo.dark, img.custom-logo{width:auto;max-height:%1$s !important;}', $maxheight . "px" ) );

}

add_action( 'wp_enqueue_scripts', 'mesmerize_do_enqueue_assets' );

add_action( 'customize_controls_enqueue_scripts', function () {

	$theme = wp_get_theme();
	$ver   = $theme->get( 'Version' );

	if ( ! apply_filters( 'mesmerize_load_bundled_version', true ) ) {
		wp_enqueue_style( 'mesmerize-customizer-spectrum',
			get_template_directory_uri() . '/customizer/libs/spectrum.css', array(), $ver );
		wp_enqueue_script( 'mesmerize-customizer-spectrum',
			get_template_directory_uri() . '/customizer/libs/spectrum.js', array( 'customize-base' ), $ver, true );
	}
} );

function mesmerize_get_general_google_fonts() {
	return apply_filters( 'mesmerize_get_general_google_fonts',
		array(
			array(
				'family'  => 'Open Sans',
				"weights" => array( "300", "400", "600", "700" ),
			),

			array(
				'family'  => 'Muli',
				"weights" => array(
					"300",
					"300italic",
					"400",
					"400italic",
					"600",
					"600italic",
					"700",
					"700italic",
					"900",
					"900italic"
				),
			),
			array(
				'family'  => 'Playfair Display',
				"weights" => array( "400", "400italic", "700", "700italic" ),
			),
		) );
}

function mesmerize_do_enqueue_google_fonts() {
	$fontsURL = array();
	if ( mesmerize_can_show_cached_value( 'mesmerize_google_fonts' ) ) {

		$fontsURL = mesmerize_get_cached_value( 'mesmerize_google_fonts' );
	} else {
		$gFonts = mesmerize_get_general_google_fonts();

		$fonts = array();

		foreach ( $gFonts as $font ) {
			$fonts[ $font['family'] ] = $font;
		}

		$gFonts = apply_filters( "mesmerize_google_fonts", $fonts );

		$fontQuery = array();
		foreach ( $gFonts as $family => $font ) {
			$fontQuery[] = $family . ":" . implode( ',', $font['weights'] );
		}

		$query_args = array(
			'family'  => urlencode( implode( '|', $fontQuery ) ),
			'subset'  => urlencode( 'latin,latin-ext' ),
			'display' => 'swap'
		);

		$fontsURL = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );

		mesmerize_cache_value( 'mesmerize_google_fonts', $fontsURL );
	}

	wp_enqueue_style( 'mesmerize-fonts', $fontsURL, array(), null );
}

add_action( 'wp_enqueue_scripts', 'mesmerize_do_enqueue_google_fonts' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function mesmerize_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}

add_action( 'wp_head', 'mesmerize_pingback_header' );

/**
 * Register sidebar
 */
function mesmerize_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar widget area', 'mesmerize' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h5 class="widgettitle">',
		'after_title'   => '</h5>',
	) );

	register_sidebar( array(
		'name'          => 'Pages Sidebar',
		'id'            => "mesmerize_pages_sidebar",
		'title'         => "Pages Sidebar",
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widgettitle">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( "Footer First Box Widgets", 'mesmerize' ),
		'id'            => "first_box_widgets",
		'title'         => esc_html__( "Widget Area", 'mesmerize' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widgettitle">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( "Footer Second Box Widgets", 'mesmerize' ),
		'id'            => "second_box_widgets",
		'title'         => esc_html__( "Widget Area", 'mesmerize' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widgettitle">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( "Footer Third Box Widgets", 'mesmerize' ),
		'id'            => "third_box_widgets",
		'title'         => esc_html__( "Widget Area", 'mesmerize' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widgettitle">',
		'after_title'   => '</h4>',
	) );
}

add_action( 'widgets_init', 'mesmerize_widgets_init' );

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and
 * a 'Read more' link.
 *
 * @return string '... Read more'
 */
function mesmerize_excerpt_more( $link ) {
	if ( is_admin() ) {
		return $link;
	}

	return '&hellip; <br> <a class="read-more" href="' . esc_url( get_permalink( get_the_ID() ) ) . '">' . mesmerize_wp_kses_post( __( 'Read more',
			'mesmerize' ) ) . '</a>';
}

add_filter( 'excerpt_more', 'mesmerize_excerpt_more' );

// UTILS

function mesmerize_nomenu_fallback( $walker = '' ) {
	$preview_atts = '';
	if ( mesmerize_is_customize_preview() ) {
		$preview_atts = 'data-type="group" data-focus-control="nav_menu_locations[primary]" data-dynamic-mod="true"';
	}

	$drop_down_menu_classes      = apply_filters( 'mesmerize_primary_drop_menu_classes', array( 'default' ) );
	$drop_down_menu_classes      = array_merge( $drop_down_menu_classes, array( 'main-menu', 'dropdown-menu' ) );
	$drop_down_menu_main_classes = array_merge( $drop_down_menu_classes, array( 'row' ) );

	return wp_page_menu( array(
		"menu_class" => esc_attr( implode( " ", $drop_down_menu_main_classes ) ),
		"menu_id"    => 'mainmenu_container',
		'before'     => '<ul id="main_menu" class="' . esc_attr( implode( " ",
				$drop_down_menu_classes ) ) . '" ' . $preview_atts . '>',
		'after'      => apply_filters( 'mesmerize_nomenu_after', '' ) . "</ul>",
		'walker'     => $walker,
	) );
}

function mesmerize_nomenu_cb() {
	return mesmerize_nomenu_fallback( '' );
}

function mesmerize_no_hamburger_menu_cb() {
	return wp_page_menu( array(
		"menu_class" => 'offcanvas_menu',
		"menu_id"    => 'offcanvas_menu',
		'before'     => '<ul id="offcanvas_menu" class="offcanvas_menu">',
		'after'      => apply_filters( 'mesmerize_nomenu_after', '' ) . "</ul>",
	) );
}

function mesmerize_title() {
	$title = '';

	if ( is_404() ) {
		$title = __( 'Page not found', 'mesmerize' );
	} elseif ( is_search() ) {
		$title = sprintf( __( 'Search Results for &#8220;%s&#8221;', 'mesmerize' ), get_search_query() );
	} elseif ( is_home() ) {
		if ( is_front_page() ) {
			$title = get_bloginfo( 'name' );
		} else {
			$title = single_post_title();
		}
	} elseif ( is_archive() ) {
		if ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );
		} else {
			$title = get_the_archive_title();
		}
	} elseif ( is_single() ) {
		$title = get_bloginfo( 'name' );

		global $post;
		if ( $post ) {
			// apply core filter
			$title = apply_filters( 'single_post_title', $post->post_title, $post );
		}
	} else {
		$title = get_the_title();
	}

	$value = apply_filters( 'mesmerize_header_title', mesmerize_wp_kses_post( $title ) );

	return $value;
}

function mesmerize_bold_text( $str ) {
	$bold = get_theme_mod( 'bold_logo', true );

	if ( ! $bold ) {
		return $str;
	}

	$str   = trim( $str );
	$words = preg_split( "/(?<=[a-z])(?=[A-Z])|(?=[\s]+)/x", $str );

	$result = "";
	$c      = 0;
	for ( $i = 0; $i < count( $words ); $i ++ ) {
		$word = $words[ $i ];
		if ( preg_match( "/^\s*$/", $word ) ) {
			$result .= $words[ $i ];
		} else {
			$c ++;
			if ( $c % 2 ) {
				$result .= $words[ $i ];
			} else {
				$result .= '<span style="font-weight: 300;" class="span12">' . esc_html( $words[ $i ] ) . "</span>";
			}
		}
	}

	return $result;
}

function mesmerize_sanitize_checkbox( $val ) {
	return ( isset( $val ) && $val == true ? true : false );
}

function mesmerize_sanitize_textfield( $val ) {
	return wp_kses_post( force_balance_tags( $val ) );
}

if ( ! function_exists( 'mesmerize_post_type_is' ) ) {
	function mesmerize_post_type_is( $type ) {
		global $wp_query;

		$post_type = $wp_query->query_vars['post_type'] ? $wp_query->query_vars['post_type'] : 'post';

		if ( ! is_array( $type ) ) {

			$type = array( $type );
		}

		return in_array( $post_type, $type );
	}
}

//////////////////////////////////////////////////////////////////////////////////////

function mesmerize_footer_container( $class ) {
	$attrs = array(
		'class' => "footer " . $class . " ",
	);

	$result = "";

	$attrs = apply_filters( 'mesmerize_footer_container_atts', $attrs );

	foreach ( $attrs as $key => $value ) {
		$value  = esc_attr( trim( $value ) );
		$key    = esc_attr( $key );
		$result .= " {$key}='{$value}'";
	}

	return $result;
}

function mesmerize_footer_background( $footer_class ) {
	$attrs = array(
		'class' => $footer_class . " ",
	);

	$result = "";

	$attrs = apply_filters( 'mesmerize_footer_background_atts', $attrs );

	foreach ( $attrs as $key => $value ) {
		$value  = esc_attr( trim( $value ) );
		$key    = esc_attr( $key );
		$result .= " {$key}='{$value}'";
	}

	return $result;
}

// THEME PAGE
function mesmerize_theme_page() {
	add_action( 'admin_menu', 'mesmerize_register_theme_page' );
}

add_filter( 'mesmerize_info_page_tabs', function ( $tabs ) {

	if ( ! apply_filters( 'mesmerize_is_companion_installed', false ) ) {
		$data = array(
			'title'   => __( 'Demo Import', 'mesmerize' ),
			'partial' => get_template_directory() . "/inc/infopage-parts/demo-import.php",
		);
		$tabs = mesmerize_associative_array_splice( $tabs, 1, 'demo-imports', $data );
	}

	return $tabs;
} );

function mesmerize_load_theme_partial( $currentTab = null ) {

	$requestTab = ( isset( $_REQUEST['tab'] ) ) ? $_REQUEST['tab'] : 'getting-started';

	if ( ! $currentTab ) {
		$currentTab = $requestTab;
	}

	require_once get_template_directory() . '/inc/companion.php';
	require_once get_template_directory() . "/inc/theme-info.php";
	wp_enqueue_style( 'mesmerize-theme-info', get_template_directory_uri() . "/assets/css/theme-info.css" );
	wp_enqueue_script( 'mesmerize-theme-info', get_template_directory_uri() . "/assets/js/theme-info.js",
		array( 'jquery' ), '', true );
}

function mesmerize_register_theme_page() {
	$page_name = apply_filters( 'mesmerize_theme_page_name', __( 'Mesmerize Info', 'mesmerize' ) );
	add_theme_page( $page_name, $page_name, 'activate_plugins', 'mesmerize-welcome', 'mesmerize_load_theme_partial' );
}

function mesmerize_instantiate_widget( $widget, $args = array() ) {

	ob_start();
	the_widget( $widget, array(), $args );
	$content = ob_get_contents();
	ob_end_clean();

	if ( isset( $args['wrap_tag'] ) ) {
		$tag     = $args['wrap_tag'];
		$class   = isset( $args['wrap_class'] ) ? $args['wrap_class'] : "";
		$content = "<{$tag} class='{$class}'>{$content}</{$tag}>";
	}

	return $content;

}

// load support for woocommerce
if ( class_exists( 'WooCommerce' ) ) {
	require_once get_template_directory() . "/inc/woocommerce/woocommerce.php";
} else {
	require_once get_template_directory() . "/inc/woocommerce/woocommerce-ready.php";
}
mesmerize_require( "/inc/integrations/index.php" );

function mesmerize_is_woocommerce_page() {

	if ( function_exists( "is_woocommerce" ) && is_woocommerce() ) {
		return true;
	}

	$woocommerce_keys = array(
		"woocommerce_shop_page_id",
		"woocommerce_terms_page_id",
		"woocommerce_cart_page_id",
		"woocommerce_checkout_page_id",
		"woocommerce_pay_page_id",
		"woocommerce_thanks_page_id",
		"woocommerce_myaccount_page_id",
		"woocommerce_edit_address_page_id",
		"woocommerce_view_order_page_id",
		"woocommerce_change_password_page_id",
		"woocommerce_logout_page_id",
		"woocommerce_lost_password_page_id",
	);

	foreach ( $woocommerce_keys as $wc_page_id ) {
		if ( get_the_ID() == get_option( $wc_page_id, 0 ) ) {
			return true;
		}
	}

	return false;
}

function mesmerize_customize_save_clear_data( $value ) {

	if ( ! isset( $value['changeset_status'] ) || $value['changeset_status'] !== "auto-draft" ) {
		mesmerize_clear_cached_values();
	}

	return $value;
}

add_filter( "customize_save_response", "mesmerize_customize_save_clear_data" );

function mesmerize_skip_link_focus_fix() {
	// The following is minified via `terser --compress --mangle -- js/skip-link-focus-fix.js`.
	?>
    <script>
        /(trident|msie)/i.test(navigator.userAgent) && document.getElementById && window.addEventListener && window.addEventListener("hashchange", function () {
            var t, e = location.hash.substring(1);
            /^[A-z0-9_-]+$/.test(e) && (t = document.getElementById(e)) && (/^(?:a|select|input|button|textarea)$/i.test(t.tagName) || (t.tabIndex = -1), t.focus())
        }, !1);
    </script>
	<?php
}

add_action( 'wp_print_footer_scripts', 'mesmerize_skip_link_focus_fix' );


function mesmerize_color_picker_scripts() {
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ),
		array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, true );
	wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris', 'wp' ), false, true );

	$colorpicker_l10n = array(
		'clear'         => __( 'Clear', 'mesmerize' ),
		'defaultString' => __( 'Default', 'mesmerize' ),
		'pick'          => __( 'Select Color', 'mesmerize' ),
		'current'       => __( 'Current Color', 'mesmerize' )
	);
	wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
}

if ( is_customize_preview() ) {
	add_action( 'init', 'mesmerize_color_picker_scripts' );
}	
