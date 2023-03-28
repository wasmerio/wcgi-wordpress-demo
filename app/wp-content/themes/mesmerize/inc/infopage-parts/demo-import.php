<div class="tab-cols">
    <h2 class="mesmerize-import-demo-sites"><?php _e('Import Demo sites with one click', 'mesmerize'); ?></h2>

    <div class="about-wrap ocdi wrap" style="display: block;margin: auto;max-width: 600px;">
        <div class="mesmerize_install_notice ">
            <h3 class="rp-plugin-title"><?php esc_html_e('Mesmerize Companion', 'mesmerize'); ?></h3>
            <p><?php esc_html_e('This feature requires the Mesmerize Companion plugin to be installed.', 'mesmerize'); ?></p>
            <?php
            $slug  = "mesmerize-companion";
            $state = \Mesmerize\Companion_Plugin::get_plugin_state($slug);
            
            $plugin_is_ready = $state['installed'] && $state['active'];
            if ( ! $plugin_is_ready) {
                if ($state['installed']) {
                    $mesmerize_link = \Mesmerize\Companion_Plugin::get_activate_link($slug);
                    $label        = esc_html__('Activate', 'mesmerize');
                    $btn_class    = "activate";
                } else {
                    $mesmerize_link = \Mesmerize\Companion_Plugin::get_install_link($slug);
                    $label        = esc_html__('Install', 'mesmerize');
                    $btn_class    = "install-now";
                }
            }
            
            ?>
            <a class="<?php echo esc_attr($btn_class); ?> button" href="<?php echo esc_attr($mesmerize_link); ?>"><?php echo esc_html($label); ?></a>
            <p></p>
        </div>
    </div>
</div>
