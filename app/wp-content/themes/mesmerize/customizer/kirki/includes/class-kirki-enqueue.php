<?php
/**
 * Enqueue the scripts that are required by the customizer.
 * Any additional scripts that are required by individual controls
 * are enqueued in the control classes themselves.
 *
 * @package     Kirki
 * @category    Core
 * @author      Aristeides Stathopoulos
 * @copyright   Copyright (c) 2016, Aristeides Stathopoulos
 * @license     http://opensource.org/licenses/https://opensource.org/licenses/MIT
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined('ABSPATH')) {
    exit;
}

if ( ! class_exists('Kirki_Enqueue')) {
    
    /**
     * Enqueues JS & CSS assets
     */
    class Kirki_Enqueue
    {
        
        /**
         * The class constructor.
         * Adds actions to enqueue our assets.
         */
        public function __construct()
        {
            add_action('admin_enqueue_scripts', array($this, 'customize_controls_l10n'), 1);
            add_action('customize_controls_enqueue_scripts', array($this, 'customize_controls_enqueue_scripts'), 7);
            add_action('customize_controls_print_scripts', array($this, 'branding'));
            add_action('customize_preview_init', array($this, 'postmessage'));
        }
        
        /**
         * L10n helper for controls.
         */
        public function customize_controls_l10n()
        {
            
            // Register the l10n script.
            wp_register_script('kirki-l10n', trailingslashit(Kirki::$url) . 'assets/js/l10n.js');
            
            // Add localization strings.
            // We'll do this on a per-config basis so that the filters are properly applied.
            $configs = Kirki::$config;
            $l10n    = array();
            foreach ($configs as $id => $args) {
                $l10n = array_merge($l10n, Kirki_l10n::get_strings($id));
            }
            
            wp_localize_script('kirki-l10n', 'kirkiL10n', $l10n);
            wp_enqueue_script('kirki-l10n');
            
        }
        
        /**
         * Assets that have to be enqueued in 'customize_controls_enqueue_scripts'.
         */
        public function customize_controls_enqueue_scripts()
        {
            
            // Get an array of all our fields.
            $fields = Kirki::$fields;
            
            // Do we have tooltips anywhere?
            $has_tooltips = false;
            foreach ($fields as $field) {
                if ($has_tooltips) {
                    continue;
                }
                // Field has tooltip.
                if (isset($field['tooltip']) && ! empty($field['tooltip'])) {
                    $has_tooltips = true;
                }
                // Backwards-compatibility ("help" argument instead of "tooltip").
                if (isset($field['help']) && ! empty($field['help'])) {
                    $has_tooltips = true;
                }
            }
            
            // If we have tooltips, enqueue the tooltips script.
            /* TODO: if ( $has_tooltips ) { */
//            wp_enqueue_script('kirki-tooltip', trailingslashit(Kirki::$url) . 'assets/js/tooltip.js', array('jquery', 'customize-controls', 'jquery-ui-tooltip'));
            /* TODO: } */
            
            // Register the color-alpha picker.
            wp_enqueue_style('wp-color-picker');
            
            // An array of control scripts and their dependencies.
            
            wp_enqueue_script('kirki-functions-all-minified', trailingslashit(Kirki::$url) . 'assets/js/functions.min.js', array('jquery', 'customize-base', 'jquery-ui-spinner', 'wp-color-picker', 'customize-controls', 'jquery-ui-tooltip'), false, true);
            wp_enqueue_script('kirki-vendor-all-minified', trailingslashit(Kirki::$url) . 'assets/js/vendor.min.js', array('kirki-functions-all-minified'), false, true);
            wp_enqueue_script('kirki-controls-all-minified', trailingslashit(Kirki::$url) . 'assets/js/controls.min.js', array('kirki-vendor-all-minified'), false, true);
            
            $scripts = array(
                'checkbox',
                'code',
                'color',
                'color-palette',
                'dashicons',
                'date',
                'dimension',
                'dropdown-pages',
                'editor',
                'generic',
                'multicheck',
                'multicolor',
                'number',
                'palette',
                'preset',
                'radio-buttonset',
                'radio-image',
                'radio',
                'repeater',
                'select',
                'slider',
                'sortable',
                'spacing',
                'switch',
                'toggle',
                'typography',
            );
            
            foreach ($scripts as $script) {
                wp_register_script('kirki-' . $script, null, array('kirki-controls-all-minified'), false, true);
                
            }
            
            // Add fonts to our JS objects.
//            $google_fonts   = Kirki_Fonts::get_google_fonts();
            $standard_fonts = Kirki_Fonts::get_standard_fonts();
            $all_variants   = Kirki_Fonts::get_all_variants();
//            $all_subsets    = Kirki_Fonts::get_google_font_subsets();
            
            $standard_fonts_final = array();
            foreach ($standard_fonts as $key => $value) {
                $standard_fonts_final[] = array(
                    'family'      => $value['stack'],
                    'label'       => $value['label'],
                    'subsets'     => array(),
                    'is_standard' => true,
                    'variants'    => array(
                        array(
                            'id'    => 'regular',
                            'label' => $all_variants['regular'],
                        ),
                        array(
                            'id'    => 'italic',
                            'label' => $all_variants['italic'],
                        ),
                        array(
                            'id'    => '700',
                            'label' => $all_variants['700'],
                        ),
                        array(
                            'id'    => '700italic',
                            'label' => $all_variants['700italic'],
                        ),
                    ),
                );
            }
            
            $final = array_merge($standard_fonts_final);
            wp_localize_script('kirki-typography', 'kirkiAllFonts', $final);
            wp_localize_script('kirki-typography', 'kirkiAllVariantsLabels', $all_variants);
        }
        
        /**
         * Enqueues the script responsible for branding the customizer
         * and also adds variables to it using the wp_localize_script function.
         * The actual branding is handled via JS.
         */
        public function branding()
        {
            
            $config = apply_filters('kirki/config', array());
            $vars   = array(
                'logoImage'   => '',
                'description' => '',
            );
            if (isset($config['logo_image']) && '' !== $config['logo_image']) {
                $vars['logoImage'] = esc_url_raw($config['logo_image']);
            }
            if (isset($config['description']) && '' !== $config['description']) {
                $vars['description'] = esc_textarea($config['description']);
            }
            
            if ( ! empty($vars['logoImage']) || ! empty($vars['description'])) {
                wp_register_script('kirki-branding', Kirki::$url . '/assets/js/branding.js');
                wp_localize_script('kirki-branding', 'kirkiBranding', $vars);
                wp_enqueue_script('kirki-branding');
            }
        }
        
        /**
         * Enqueues the postMessage script
         * and adds variables to it using the wp_localize_script function.
         * The rest is handled via JS.
         */
        public function postmessage()
        {
            wp_enqueue_script('kirki_auto_postmessage', trailingslashit(Kirki::$url) . 'assets/js/postmessage.js', array('customize-preview'), false, true);
            $js_vars_fields = array();
            $fields         = Kirki::$fields;
            foreach ($fields as $field) {
                if (isset($field['transport']) && 'postMessage' === $field['transport'] && isset($field['js_vars']) && ! empty($field['js_vars']) && is_array($field['js_vars']) && isset($field['settings'])) {
                    $js_vars_fields[$field['settings']] = $field['js_vars'];
                }
            }
            wp_localize_script('kirki_auto_postmessage', 'jsvars', $js_vars_fields);
        }
    }
}
