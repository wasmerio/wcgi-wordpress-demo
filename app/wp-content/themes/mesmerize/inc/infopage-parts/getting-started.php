<div class="tab-cols">
    <div class="two-col">
        <div class="col bordered-right">
            <h2 class="col-title"> <?php _e('Get Started in 3 Easy Steps', 'mesmerize'); ?></h2>
            <h3 class="col-subtitle"><?php _e('1. Install the recommended plugins', 'mesmerize'); ?></h3>
            <div class="recommended-plugins">
                <?php
                $config          = \Mesmerize\Companion_Plugin::$config;
                $mesmerize_plugins = $config['plugins'];

                foreach ($mesmerize_plugins as $slug => $plugin) {
                    $state = \Mesmerize\Companion_Plugin::get_plugin_state($slug);

                    $plugin_is_ready = $state['installed'] && $state['active'];
                    if ( ! $plugin_is_ready) {
                        if ($state['installed']) {
                            $mesmerize_link = \Mesmerize\Companion_Plugin::get_activate_link($slug);
                            $label        = $plugin['activate']['label'];
                            $btn_class    = "activate";
                        } else {
                            $mesmerize_link = \Mesmerize\Companion_Plugin::get_install_link($slug);
                            $label        = $plugin['install']['label'];
                            $btn_class    = "install-now";
                        }
                    }

                    $mesmerize_title = $plugin['title'];
                    $description   = $plugin['description'];
                    ?>
                    <div class="mesmerize_install_notice <?php if ($plugin_is_ready) {
                        echo 'blue';
                    } ?>">
                        <h3 class="rp-plugin-title"><?php echo $mesmerize_title ?></h3>
                        <?php
                        printf('<p>%1$s</p>', $description);
                        if ( ! $plugin_is_ready) {
                            printf('<a class="%1$s button" href="%2$s">%3$s</a>', esc_attr($btn_class), esc_url($mesmerize_link), esc_html($label));
                        } else {
                            _e('Plugin is installed and active.', 'mesmerize');
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
            </div>
            <h3 class="col-subtitle">
                <?php
                $customize_link = add_query_arg(
                    array(
                        'url' => get_home_url(),
                    ),
                    network_admin_url('customize.php')
                );

                printf('2. <a class="button" href="%s"> %s </a> your site', esc_url($customize_link), esc_html__('Customize', 'mesmerize')); ?></h3>
            <h3 class="col-subtitle"><?php _e('3. Enjoy! :)', 'mesmerize'); ?></h3>
        </div>
        <div class="col">
        
        </div>
    </div>
</div>
