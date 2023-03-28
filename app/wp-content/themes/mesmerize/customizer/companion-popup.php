<div id="mesmerize_homepage">
    <div class="mesmerize-popup" style="">
        <style>
            [id=mesmerize_homepage] .mesmerize-popup > div.footer {
                margin-top: unset !important;
                margin-bottom: unset !important;
                position: relative !important;
                z-index: 2 !important;
            }

            [id=mesmerize_homepage] .mesmerize-popup > div.footer > a {
                position: absolute !important;
                bottom: 0px !important;
            }

        </style>
        <div>
            <div class="mesmerize_cp_column">
                <h3 class="mesmerize_title"><?php esc_html_e( 'Please Install the Mesmerize Companion Plugin to Enable All the Theme Features',
						'mesmerize' ) ?></h3>
                <h4><?php esc_html_e( 'Here\'s what you\'ll get:', 'mesmerize' ); ?></h4>
                <ul class="mesmerize-features-list">
                    <li><?php esc_html_e( 'Beautiful ready-made homepage', 'mesmerize' ); ?></li>
                    <li><?php esc_html_e( 'Drag and drop page customization', 'mesmerize' ); ?></li>
                    <li><?php esc_html_e( '25+ predefined content sections', 'mesmerize' ); ?></li>
                    <li><?php esc_html_e( 'Live content editing', 'mesmerize' ); ?></li>
                    <li><?php esc_html_e( '5 header types', 'mesmerize' ); ?></li>
                    <li><?php esc_html_e( '3 footer types', 'mesmerize' ); ?></li>
                    <li><?php esc_html_e( 'and many other features', 'mesmerize' ); ?></li>
                </ul>
            </div>
            <div class="mesmerize_cp_column">
				<?php if ( apply_filters( 'mesmerize_companion_show_popup_screenshot', true ) ): ?>
                    <img class="popup-theme-screenshot"
                         src="<?php echo esc_attr( get_template_directory_uri() . "/screenshot.jpg" ) ?>"/>
				<?php else: ?>
					<?php do_action( 'mesmerize_companion_popup_screenshot' ); ?>
				<?php endif; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="footer">
            <label class="disable-popup-cb">
                <input type="checkbox" id="disable-popup-cb"/>
				<?php esc_html_e( "Don't show this popup in the future", 'mesmerize' ); ?>
            </label>
            <script type="text/javascript">
                jQuery('.mesmerize-welcome-notice').on('click', '.notice-dismiss', function () {
                    jQuery.post(
                        ajaxurl,
                        {
                            value: 1,
                            action: "companion_disable_popup",
                            companion_disable_popup_wpnonce: '<?php echo wp_create_nonce( "companion_disable_popup" ); ?>'
                        }
                    )
                });
            </script>
			<?php
			if ( \Mesmerize\Companion_Plugin::$plugin_state['installed'] ) {
				$mesmerize_link  = \Mesmerize\Companion_Plugin::get_activate_link();
				$mesmerize_label = esc_html__( 'Activate now', 'mesmerize' );
			} else {
				$mesmerize_link  = \Mesmerize\Companion_Plugin::get_install_link();
				$mesmerize_label = esc_html__( 'Install now', 'mesmerize' );
			}
			printf( '<a class="button button-hero button-primary install-now" href="%1$s">%2$s</a>', esc_url( $mesmerize_link ),
				$mesmerize_label );
			?>
        </div>
    </div>
</div>
