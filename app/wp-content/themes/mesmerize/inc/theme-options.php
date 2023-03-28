<?php

add_action( 'customize_register', 'mesmerize_customize_register', 10, 1 );
add_action( 'customize_register', 'mesmerize_customize_reorganize', PHP_INT_MAX, 1 );

require_once get_template_directory() . "/inc/general-options.php";
require_once get_template_directory() . "/inc/header-options.php";
require_once get_template_directory() . "/inc/footer-options.php";
require_once get_template_directory() . "/inc/blog-options.php";
require_once get_template_directory() . "/inc/optimizations.php";


function mesmerize_add_options_group( $options ) {

	foreach ( $options as $option => $args ) {
		do_action_ref_array( $option . "_before", $args );
		call_user_func_array( $option, $args );
		do_action_ref_array( $option . "_after", $args );
	}
}

function mesmerize_customize_register( $wp_customize ) {

	mesmerize_customize_register_controls( $wp_customize );

	do_action( 'mesmerize_customize_register', $wp_customize );
}

function mesmerize_add_sections( $wp_customize ) {
	/** @var WP_Customize_Manager $wp_customize */

	$wp_customize->add_section( 'extendthemes_start_from_demo_site', array(
		'priority'       => 1,
		'capability'     => 'edit_theme_options',
		'theme_supports' => '',
		'title'          => esc_html__( 'Import Predesigned Sites', 'mesmerize' ),
		'description'    => '',
	) );


	$wp_customize->add_section( 'header_layout', array(
		'title'    => esc_html__( 'Front Page Header Designs', 'mesmerize' ),
		'priority' => 1,
	) );

	$wp_customize->add_panel( 'navigation_panel',
		array(
			'priority'       => 2,
			'capability'     => 'edit_theme_options',
			'theme_supports' => '',
			'title'          => esc_html__( 'Navigation', 'mesmerize' ),
			'description'    => '',
		)
	);


	$wp_customize->add_section(
		new \Mesmerize\FrontPageSection(
			$wp_customize,
			'navigation_panel_install_companion',
			array(
				'priority'   => PHP_INT_MAX,
				'title'      => esc_html__( 'Front Page content', 'mesmerize' ),
				'panel'      => 'navigation_panel',
				'show_title' => false
			)
		)
	);


	$wp_customize->add_panel( 'header',
		array(
			'priority'       => 2,
			'capability'     => 'edit_theme_options',
			'theme_supports' => '',
			'title'          => esc_html__( 'Hero', 'mesmerize' ),
			'description'    => '',
		)
	);

	$wp_customize->add_section(
		new \Mesmerize\FrontPageSection(
			$wp_customize,
			'header_install_companion',
			array(
				'priority'   => PHP_INT_MAX,
				'title'      => esc_html__( 'Front Page content', 'mesmerize' ),
				'panel'      => 'header',
				'show_title' => false
			)
		)
	);

	$wp_customize->add_section( 'page_content', array(
		'priority' => 2,
		'title'    => esc_html__( 'Front Page content', 'mesmerize' ),
	) );

	$wp_customize->add_section(
		new \Mesmerize\FrontPageSection(
			$wp_customize,
			'page_content',
			array(
				'priority' => 2,
				'title'    => esc_html__( 'Front Page content', 'mesmerize' ),
			)
		)
	);


	$wp_customize->add_section( 'footer_settings', array(
		'title'    => esc_html__( 'Footer Settings', 'mesmerize' ),
		'priority' => 3,
	) );

	$wp_customize->add_section( 'blog_settings', array(
		'title'    => esc_html__( 'Blog Settings', 'mesmerize' ),
		'priority' => 4,
	) );

	$wp_customize->add_panel( 'general_settings', array(
		'title'    => esc_html__( 'General Settings', 'mesmerize' ),
		'priority' => 5,
	) );


	$wp_customize->add_section( 'optimizations', array(
		'title'    => esc_html__( 'Optimization', 'mesmerize' ),
		'panel'    => 'general_settings',
		'priority' => 40,
	) );


	do_action( 'mesmerize_add_sections', $wp_customize );

	$sections = array(

		'header_background_chooser' => array(
			'title' => esc_html__( 'Front Page Hero', 'mesmerize' ),
			'panel' => 'header',
		),

		'header_content' => array(
			'title' => esc_html__( 'Front Page Hero Content', 'mesmerize' ),
			'panel' => 'header',
		),

		'header_image'  => array(
			'title' => esc_html__( 'Inner Pages Hero', 'mesmerize' ),
			'panel' => 'header',

		),
		'page_settings' => array(
			'title' => esc_html__( 'Page Settings', 'mesmerize' ),
			'panel' => 'general_settings',
		),
	);

	foreach ( $sections as $name => $value ) {
		$wp_customize->add_section( $name, $value );
	}

}

function mesmerize_customize_register_controls( $wp_customize ) {
	/** @var WP_Customize_Manager $wp_customize */
	$wp_customize->register_control_type( '\\Mesmerize\\Kirki_Controls_Separator_Control' );
	$wp_customize->register_control_type( "\\Mesmerize\\WebGradientsControl" );
	$wp_customize->register_control_type( "\\Mesmerize\\SidebarGroupButtonControl" );
	$wp_customize->register_control_type( '\Mesmerize\Kirki_Controls_Radio_HTML_Control' );
	$wp_customize->register_control_type( '\\Mesmerize\FontAwesomeIconControl' );
	$wp_customize->register_control_type( 'Mesmerize\\GradientControl' );

	$wp_customize->get_setting( 'background_color' )->transport = "refresh";

	// Register our custom control with Kirki
	add_filter( 'kirki/control_types', function ( $controls ) {
		$controls['sectionseparator']          = '\\Mesmerize\\Kirki_Controls_Separator_Control';
		$controls['ope-info']                  = '\\Mesmerize\\Info_Control';
		$controls['ope-info-pro']              = '\\Mesmerize\\Info_PRO_Control';
		$controls['web-gradients']             = "\\Mesmerize\\WebGradientsControl";
		$controls['sidebar-button-group']      = "\\Mesmerize\\SidebarGroupButtonControl";
		$controls['radio-html']                = '\\Mesmerize\\Kirki_Controls_Radio_HTML_Control';
		$controls['font-awesome-icon-control'] = "\\Mesmerize\\FontAwesomeIconControl";
		$controls['gradient-control']          = "Mesmerize\\GradientControl";

		return $controls;
	} );

	require_once get_template_directory() . "/customizer/customizer-controls.php";
	require_once get_template_directory() . "/customizer/WebGradientsControl.php";
	require_once get_template_directory() . "/customizer/SidebarGroupButtonControl.php";
	require_once get_template_directory() . "/customizer/GradientControl.php";

	mesmerize_add_sections( $wp_customize );
	mesmerize_add_general_settings( $wp_customize );


}


function mesmerize_companion_greater_than( $version ) {
	$companion_version = mesmerize_get_companion_data( 'Version' );
	if ( ! $companion_version || version_compare( $companion_version, $version, ">" ) ) {
		return true;
	}

	return false;
}

add_action( 'customize_register', function () {

	if ( mesmerize_companion_greater_than( "1.4.3" ) ) {
		return;
	}

	$updateText = esc_html__( 'There is a newer version of the Mesmerize Companion plugin available. This feature requires an update to the latest version',
		'mesmerize' );

	$updateText .= "<br/><br/><a class='button' target='_blank' href='" . admin_url( "plugins.php" ) . "'>" . __( 'Update companion now',
			'mesmerize' ) . "</a>";

	mesmerize_add_kirki_field( array(
		'type'     => 'ope-info',
		'label'    => $updateText,
		'section'  => "extendthemes_start_from_demo_site",
		'settings' => "extendthemes_start_from_demo_site_newer_plugin",
	) );

	mesmerize_add_kirki_field( array(
		'type'     => 'ope-info',
		'label'    => $updateText,
		'section'  => "header_layout",
		'settings' => "header_layout_newer_plugin",
	) );
} );

function mesmerize_add_general_settings( $wp_customize ) {


	/* logo max height */
	mesmerize_add_kirki_field( array(
		'type'     => 'number',
		'label'    => esc_html__( 'Logo Max Height (px)', 'mesmerize' ),
		'section'  => 'title_tagline',
		'default'  => 70,
		'settings' => 'logo_max_height',
		'priority' => 8,
	) );

	$wp_customize->add_setting( 'bold_logo', array(
		'default'           => true,
		'sanitize_callback' => 'mesmerize_sanitize_boolean',
	) );

	$wp_customize->add_control( 'bold_logo', array(
		'label'    => esc_html__( 'Alternate text logo words', 'mesmerize' ),
		'section'  => 'title_tagline',
		'priority' => 9,
		'type'     => 'checkbox',
	) );

	$wp_customize->add_setting( 'logo_dark', array(
		'default'           => false,
		'sanitize_callback' => 'absint',
	) );

	$custom_logo_args = get_theme_support( 'custom-logo' );
	$wp_customize->add_control( new WP_Customize_Cropped_Image_Control( $wp_customize, 'logo_dark', array(
		'label'         => esc_html__( 'Dark Logo', 'mesmerize' ),
		'section'       => 'title_tagline',
		'priority'      => 9,
		'height'        => $custom_logo_args[0]['height'],
		'width'         => $custom_logo_args[0]['width'],
		'flex_height'   => $custom_logo_args[0]['flex-height'],
		'flex_width'    => $custom_logo_args[0]['flex-width'],
		'button_labels' => array(
			'select'       => __( 'Select logo', 'mesmerize' ),
			'change'       => __( 'Change logo', 'mesmerize' ),
			'remove'       => __( 'Remove', 'mesmerize' ),
			'default'      => __( 'Default', 'mesmerize' ),
			'placeholder'  => __( 'No logo selected', 'mesmerize' ),
			'frame_title'  => __( 'Select logo', 'mesmerize' ),
			'frame_button' => __( 'Choose logo', 'mesmerize' ),
		),
	) ) );


	// remove partial refresh to display the site name properly in customizer
	$wp_customize->selective_refresh->remove_partial( 'custom_logo' );
	$wp_customize->get_setting( 'custom_logo' )->transport = 'refresh';
}


function mesmerize_customize_reorganize( $wp_customize ) {
	$generalSettingsSections = array(
		'title_tagline',
		'colors',
		'general_site_style',
		'background_image',
		'static_front_page',
		'custom_css',
		'user_custom_widgets_areas',
	);

	$priority = 1;
	foreach ( $generalSettingsSections as $section_id ) {
		$section = $wp_customize->get_section( $section_id );

		if ( $section ) {
			$section->panel    = 'general_settings';
			$section->priority = $priority;
			$priority ++;
		}

	}
}

add_action( 'customize_controls_enqueue_scripts', function () {

	$textDomain = mesmerize_get_text_domain();

	$cssUrl = get_template_directory_uri() . "/customizer/";
	$jsUrl  = get_template_directory_uri() . "/customizer/js/";

	wp_enqueue_style( 'thickbox' );
	wp_enqueue_script( 'thickbox' );

	wp_enqueue_style( $textDomain . '-webgradients', get_template_directory_uri() . '/assets/css/webgradients.css' );

	if ( apply_filters( 'mesmerize_load_bundled_version', true ) ) {
		wp_enqueue_script( $textDomain . '-customize', $jsUrl . "/customize.bundle.min.js",
			array( 'jquery', 'customize-base', 'customize-controls', 'media-views' ), true );
		wp_enqueue_style( $textDomain . '-customizer-base', $cssUrl . '/customizer.bundle.min.css' );
	} else {
		wp_enqueue_style( $textDomain . '-customizer-base', $cssUrl . '/customizer.css' );
		wp_enqueue_script( $textDomain . '-customize', $jsUrl . "/customize.js",
			array( 'jquery', 'customize-base', 'customize-controls' ), true );
	}


	$settings = array(
		'stylesheetURL' => get_template_directory_uri(),
		'templateURL'   => get_template_directory_uri(),
		'includesURL'   => includes_url(),
		'l10n'          => array(
			'closePanelLabel'     => esc_attr__( 'Close Panel', 'mesmerize' ),
			'chooseImagesLabel'   => esc_attr__( 'Choose Images', 'mesmerize' ),
			'chooseGradientLabel' => esc_attr__( "Web Gradients", 'mesmerize' ),
			'chooseFALabel'       => esc_attr__( "Font Awesome Icons", 'mesmerize' ),
			'selectGradient'      => esc_attr__( "Select Gradient", 'mesmerize' ),
			'deselect'            => esc_attr__( "Deselect", 'mesmerize' ),
			'changeImageLabel'    => esc_attr__( 'Change image', 'mesmerize' ),
		),
		'upgrade_url'   => mesmerize_get_upgrade_link(),
		'cache_key'     => md5( get_stylesheet() . "|" . wp_get_theme()->get( "Version" ) )
	);

	// ensure correct localization script
	wp_localize_script( 'customize-base', 'mesmerize_customize_settings', $settings );
} );

add_action( 'customize_preview_init', function () {
	$textDomain = mesmerize_get_text_domain();

	$jsUrl = get_template_directory_uri() . "/customizer/js/";
	wp_enqueue_script( $textDomain . '-customize-preview', $jsUrl . "/customize-preview.js",
		array( 'jquery', 'customize-preview' ), '', true );
} );


function mesmerize_get_gradients_classes() {
	return apply_filters( "mesmerize_webgradients_list", array(
		"plum_plate",
		"ripe_malinka",
		"new_life",
		"sunny_morning",
		"red_salvation",
	) );
}

function mesmerize_get_parsed_gradients() {
	return apply_filters( "mesmerize_parsed_webgradients_list", array(
		'plum_plate' => array(
			'angle'  => '135',
			'colors' => array(
				0 => array(
					'color'    => 'rgba(102,126,234, 0.8)',
					'position' => '0%',
				),
				1 => array(
					'color'    => 'rgba(118,75,162,0.8)',
					'position' => '100%',
				),
			),
		),

		'red_salvation' => array(
			'angle'  => '142',
			'colors' => array(
				0 => array(
					'color'    => 'rgba(244,59,71, 0.8)',
					'position' => '0%',
				),
				1 => array(
					'color'    => 'rgba(69,58,148, 0.8)',
					'position' => '100%',
				),
			),
		),


	) );
}

add_action( 'wp_ajax_mesmerize_webgradients_list', function () {
	$result           = array();
	$webgradients     = mesmerize_get_gradients_classes();
	$parsed_gradients = mesmerize_get_parsed_gradients();

	foreach ( $webgradients as $icon ) {
		$parsed   = isset( $parsed_gradients[ $icon ] ) ? $parsed_gradients[ $icon ] : false;
		$title    = str_replace( '_', ' ', $icon );
		$result[] = array(
			'id'       => $icon,
			'gradient' => $icon,
			"title"    => $title,
			'mime'     => "web-gradient/class",
			'sizes'    => null,
			'parsed'   => $parsed,
		);
	}

	$result = apply_filters( "mesmerize_wp_ajax_webgradients_list", $result );

	echo json_encode( $result );
	exit;
} );


add_action( 'wp_ajax_mesmerize_list_fa', function () {
	$result = array();
	$icons  = ( require get_template_directory() . "/customizer/fa-icons-list.php" );
	foreach ( $icons as $icon ) {
		$title    = str_replace( '-', ' ', str_replace( 'fa-', '', $icon ) );
		$result[] = array(
			'id'    => $icon,
			'fa'    => $icon,
			"title" => $title,
			'mime'  => "fa-icon/font",
			'sizes' => null,
		);
	}

	echo json_encode( $result );
	exit;
} );


//TODO: needs refactoring
add_filter( 'body_class', function ( $classes ) {

	$body_class = mesmerize_is_front_page( true ) ? "mesmerize-front-page" : "mesmerize-inner-page";
	$body_class = array( $body_class );


	$classes = array_merge( $classes, $body_class );

	if ( in_array( 'mesmerize-front-page', $classes ) ) {
		$classes[] = 'mesmerize-content-padding';

	}
	// TODO: Needs Review
	if ( get_theme_mod( 'header_type', 'simple' ) == 'slider' ) {
		$classes[] = 'mesmerize-front-page-with-slider';
	}

	return $classes;
} );

// code from rest_sanitize_boolean
function mesmerize_sanitize_boolean( $value ) {
	// String values are translated to `true`; make sure 'false' is false.
	if ( is_string( $value ) ) {
		$value = strtolower( $value );
		if ( in_array( $value, array( 'false', '0' ), true ) ) {
			$value = false;
		}
	}

	// Everything else will map nicely to boolean.
	return (boolean) $value;
}


/**
 * @param      $control
 * @param bool $print
 *
 * @return bool|string
 */
function mesmerize_customizer_focus_control_attr( $control, $print = true ) {
	if ( ! mesmerize_is_customize_preview() ) {
		return false;
	}

	$control = esc_attr( $control );
	$toPrint = "data-type=\"group\" data-focus-control='{$control}'";

	if ( $print ) {
		echo $toPrint;
	}

	return $toPrint;
}

add_action( 'customize_preview_init', function () {
	if ( mesmerize_is_customize_preview() && ! apply_filters( 'mesmerize_is_companion_installed', false ) ) {
		$no_companion_preview_style = '
          [data-reiki-hidden="true"] {
            display: none !important;
          }';
		wp_add_inline_style( 'customize-preview', $no_companion_preview_style );
	}
} );
