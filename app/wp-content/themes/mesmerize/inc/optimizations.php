<?php


add_action('wp_ajax_mesmerize_apply_autoptimize_settings', function () {
    $autoptmize_settings = array(
        'autoptimize_html'               => 'on',
        'autoptimize_html_keepcomments'  => 'on',
        'autoptimize_js'                 => 'on',
        'autoptimize_js_exclude'         => 'seal.js, js/jquery/jquery.js',
        'autoptimize_css'                => 'on',
        'autoptimize_css_exclude'        => 'wp-content/cache/, wp-content/uploads/, admin-bar.min.css, dashicons.min.css',
        'autoptimize_css_datauris'       => 'on',
        'autoptimize_css_inline'         => 'on',
        'autoptimize_cache_clean'        => 'on',
        'autoptimize_cache_nogzip'       => 'on',
        'autoptimize_show_adv'           => '1',
        'autoptimize_optimize_logged'    => 'on',
        'autoptimize_extra_settings'     => array(
            'autoptimize_extra_checkbox_field_1' => '1',
            'autoptimize_extra_checkbox_field_0' => '1',
            'autoptimize_extra_radio_field_4'    => '1',
            'autoptimize_extra_text_field_2'     => '',
            'autoptimize_extra_text_field_3'     => '',
        ),
        'autoptimize_css_include_inline' => 'on',
    
    );
    
    foreach ($autoptmize_settings as $key => $value) {
        update_option($key, $value);
    }
});


function mesmerize_print_autoptimize_mesmerize_settings_button($plugin)
{
    /** @var \Mesmerize\Plugin_Control $plugin */
    $display = $plugin->isPluginActive() ? 'block' : 'none';
    ?>
    <div class="autopmize-mesmerize-settings" style="display: <?php echo esc_attr($display); ?> ">
        <p><?php esc_html_e('Please apply the Mesmerize recommended settings for Autoptimize. This will set the Autoptimize parameters to the best configuration for Mesmerize.', 'mesmerize'); ?></p>
        <button data-name="apply-mesmerize-autoptimize-settings" class="button"><?php esc_html_e('Apply Settings', 'mesmerize'); ?></button>
        <p style="display: none" data-name="autoptmize-settings-applied"><?php esc_html_e('Settings applied', 'mesmerize'); ?></p>
    </div>
    <script>
        (function ($) {
            $(document).on('extendthemes-plugin-status-update', function (event, slug, status) {
                if (slug === "autoptimize" && status === "ready") {
                    $(".autopmize-mesmerize-settings").show();
                }
            });

            $('[data-name="apply-mesmerize-autoptimize-settings"]').click(function (event) {
                event.preventDefault();
                event.stopPropagation();
                $('[data-name="autoptmize-settings-applied"]').hide();
                var data = {
                    action: 'mesmerize_apply_autoptimize_settings'
                };
                jQuery.post(ajaxurl, data).done(function (response) {
                    $('[data-name="autoptmize-settings-applied"]').show();
                });
            });
        })(jQuery)
    </script>
    <?php
}

add_action('customize_register', function ($wp_customizer) {
    
    
    mesmerize_add_kirki_field(array(
        'type'     => 'sectionseparator',
        'label'    => __('Autoptimize', 'mesmerize'),
        'settings' => "autoptmize_plugin_separator",
        'section'  => 'optimizations',
        'priority' => 1,
    ));
    
    /** @var WP_Customize_Manager $wp_customizer */
    $wp_customizer->add_control(new \Mesmerize\Plugin_Control($wp_customizer, 'optimizations-control',
        array(
            'section'     => 'optimizations',
            'settings'    => array(),
            'plugin_slug' => 'autoptimize',
            'plugin_path' => 'autoptimize/autoptimize.php',
            'plugin_name' => 'Autoptimize',
            'description' => __('Install and activate the "Autoptimize" plugin to improve your site speed.', 'mesmerize'),
            'capability'  => 'edit_theme_options',
            'after'       => 'mesmerize_print_autoptimize_mesmerize_settings_button',
            'priority'    => 2,
        )
    ));
});
