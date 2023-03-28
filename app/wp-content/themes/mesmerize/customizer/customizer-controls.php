<?php

namespace Mesmerize;


class BaseControl extends \WP_Customize_Control {
	protected $data = null;

	public function __construct( $manager, $id, $data = array() ) {
		$this->data = $data;
		parent::__construct( $manager, $id, $data );
		$this->init();
	}

	public function init() {
		return true;
	}
}


class BackgroundTypesControl extends BaseControl {

	public function init() {
		$this->type = 'select';
		foreach ( $this->data['choices'] as $key => $value ) {
			$this->choices[ $key ] = $value['label'];
		}
	}

	public function render_content() {
		parent::render_content(); ?>
        <script>
            jQuery(document).ready(function ($) {
                $('[<?php $this->link(); ?>]').data('controlBinds', <?php echo json_encode( $this->data['choices'] ) ?>);

                function updateControlBinds() {
                    var controlBinds = $('[<?php $this->link(); ?>]').data('controlBinds');
                    var currentType = $('[<?php $this->link(); ?>]').val();

                    for (var type in controlBinds) {
                        var controls = controlBinds[type].control;
                        if (!_.isArray(controls)) {
                            controls = [controls];
                        }

                        for (var i = 0; i < controls.length; i++) {
                            var control = wp.customize.control(controls[i]);

                            if (control) {
                                var container = control.container.eq(0);
                                if (type === currentType) {
                                    container.show();
                                } else {
                                    container.hide();
                                }
                            }

                        }
                    }
                }

                wp.customize('<?php echo esc_html( $this->settings['default']->id ); ?>').bind(updateControlBinds);
                $('[<?php $this->link(); ?>]').change(updateControlBinds);
                updateControlBinds();
            });
        </script>
		<?php
	}
}

class RowsListControl extends BaseControl {
	private static $enqueued = false;

	public function init() {
		$this->type = isset( $this->data['type'] ) ? $this->data['type'] : 'mod_changer';
	}

	public function enqueue() {

		if ( static::$enqueued ) {
			return;
		}

		static::$enqueued = true;
		$jsUrl            = get_template_directory_uri() . "/customizer/js/";
		\wp_enqueue_script( 'mesmerize-row-list-control', $jsUrl . "/row-list-control.js",
			array( 'jquery', 'customize-base' ), null, true );
		\wp_localize_script( 'mesmerize-row-list-control', 'mesmerize_row_list_control_l10n', array(
			'in_pro'   => esc_html__( 'Available in PRO', 'mesmerize' ),
			'insert'   => esc_html( $this->data['insertText'] ),
			'in_page'  => esc_html__( 'Section is already in page', 'mesmerize' ),
			'pro_only' => esc_html__( 'Pro Only', 'mesmerize' ),
		) );
	}

	public function render_content() {
		?>
        <div <?php $this->dateSelection(); ?> data-type="row-list-control"
                                              data-apply="<?php echo esc_attr( $this->data['type'] ) ?>"
                                              class="list-holder">
			<?php ( $this->data['type'] === "mod_changer" ) ? $this->renderModChanger() : $this->renderPresetsChanger() ?>
        </div>

		<?php $proMessage = isset( $this->data['pro_message'] ) ? $this->data['pro_message'] : false; ?>

		<?php if ( $proMessage && apply_filters( 'mesmerize_show_inactive_plugin_infos', true ) ): ?>
            <div class="list-control-pro-message">
				<?php echo wp_kses_post( $proMessage ); ?>
            </div>
		<?php endif; ?>

		<?php
	}

	public function dateSelection() {
		$data = 'data-selection="radio"';

		if ( isset( $this->data['selection'] ) ) {
			$data = 'data-selection="' . esc_attr( $this->data['selection'] ) . '"';
		}

		echo $data;
	}

	public function renderPresetsChanger() {
		$items = $this->getSourceData();


		if ( isset( $items['use_ajax'] ) && $items['use_ajax'] && isset( $items['filter'] ) ) {

			?>
            <ul <?php $this->dataAttrs(); ?> class="list rows-list from-theme"
                                             data-ajax-data="<?php echo esc_attr( $items['filter'] ); ?>">

            </ul>
            <script type="text/template" <?php $this->dataAttrs(); ?>
                    id="tmpl-row-template-<?php echo esc_attr( $this->id ); ?>">
                <li class="item available-item {{ data.proOnly }}" data-varname="{{ data.optionsVar }}"
                    data-id="{{ data.id }}">
                    <div class="image-holder" style="background-position:center center;background-image:url()">
                        <img src="{{ data.thumb }}"/>
                    </div>

                    <# if(data.proOnly) { #>
                    <span data-id="{{ data.id }}" data-pro-only="true" class="available-item-hover-button"
                          data-setting-link="{{ data.setting }}">
                        <# print(mesmerize_row_list_control_l10n.in_pro) #>
                    </span>
                    <# } else { #>
                    <span data-id="{{ data.id }}" class="available-item-hover-button"
                          data-setting-link="{{ data.setting }}">
                        <# print(mesmerize_row_list_control_l10n.insert) #>
                    </span>
                    <# } #>

                    <div title="{{ mesmerize_row_list_control_l10n.in_page }}" class="checked-icon"></div>
                    <div title="{{ mesmerize_row_list_control_l10n.pro_only }}" class="pro-icon"></div>
                    <span class="item-preview" data-preview="{{ data.preview }}"><i class="icon"></i></span>
                    <# if(data.description) { #>
                    <span class="description"><# print(data.description) #></span>
                    <# } #>
                </li>
            </script>
			<?php
			return;
		}

		$optionsVar = uniqid( 'cp_' . $this->id . '_' ); ?>
        <script>
            var <?php echo esc_html( $optionsVar ) ?> =
            {
            }
            ;
        </script>
        <ul <?php $this->dataAttrs(); ?> class="list rows-list from-theme">
			<?php foreach ( $items as $item ):
				?>
                <script>
					<?php $settingsData = $this->filterArrayDefaults( $item['settings'] ); ?>
						<?php echo esc_html( $optionsVar ); ?>["<?php echo esc_html( $item['id'] ); ?>"] = <?php echo json_encode( $settingsData ) ?>;
                </script>

			<?php $proOnly = isset( $item['pro-only'] ) ? "pro-only" : ""; ?>


                <li class="item available-item <?php echo esc_attr( $proOnly ); ?>"
                    data-varname="<?php echo esc_attr( $optionsVar ); ?>"
                    data-id="<?php echo esc_attr( $item['id'] ); ?>">
                    <div class="image-holder" style="background-position:center center;background-image:url()">
                        <img data-src="<?php echo esc_url( $item['thumb'] ); ?>" src=""/>
                    </div>

					<?php if ( $proOnly ) : ?>
                        <span data-id="<?php echo esc_attr( $item['id'] ); ?>" data-pro-only="true"
                              class="available-item-hover-button" <?php $this->getSettingAttr(); ?> >
                                <?php esc_html_e( 'Available in PRO', 'mesmerize' ); ?>
                            </span>
					<?php else : ?>
                        <span data-id="<?php echo esc_attr( $item['id'] ); ?>"
                              class="available-item-hover-button" <?php $this->getSettingAttr(); ?> >
                                <?php echo esc_html( $this->data['insertText'] ); ?>
                            </span>
					<?php endif; ?>

                    <div title="<?php esc_attr_e( 'Section is already in page', 'mesmerize' ); ?>"
                         class="checked-icon"></div>
                    <div title="<?php esc_attr_e( 'Pro Only', 'mesmerize' ); ?>" class="pro-icon"></div>
                    <span class="item-preview" data-preview="<?php echo esc_attr( $item['preview'] ); ?>">
                                <i class="icon"></i>
                            </span>
					<?php if ( isset( $item['description'] ) ): ?>
                        <span class="description"> <?php echo esc_html( $item['description'] ); ?> </span>
					<?php endif; ?>
                </li>
			<?php endforeach; ?>
        </ul>
        <input type="hidden" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />

		<?php ;
	}

	public function getSourceData() {
		return $this->data['dataSource'];
	}

	public function dataAttrs() {
		$data = 'data-name="' . esc_attr( $this->id ) . '"';

		echo $data;
	}

	public function filterArrayDefaults( $data ) {
		foreach ( $data as $key => $value ) {
			$data[ $key ] = $this->filterDefault( $value );
		}

		return $data;
	}

	public function filterDefault( $data ) {
		if ( is_array( $data ) ) {
			$data = $this->filterArrayDefaults( $data );
		} else {
			$data = str_replace( '[tag_companion_uri]', get_template_directory_uri(), $data );
			$data = str_replace( '[tag_theme_uri]',
				apply_filters( 'mesmerize_assets_url', get_template_directory_uri(), '/' ), $data );
			$data = str_replace( '[tag_child_theme_uri]',
				apply_filters( 'mesmerize_child_assets_url', get_stylesheet_directory_uri(), '/' ), $data );
			$data = str_replace( '[tag_style_uri]', get_stylesheet_directory_uri(), $data );
		}

		return $data;
	}

	public function getSettingAttr( $setting_key = 'default' ) {
		if ( ! isset( $this->settings[ $setting_key ] ) ) {
			return '';
		}

		echo 'data-setting-link="' . esc_attr( $this->settings[ $setting_key ]->id ) . '"';
	}
}


class Activate_Companion_Control extends BaseControl {
	public function render_content() {
		$data  = $this->data;
		$label = $data['label'];
		$msg   = $data['msg'];
		$slug  = $data['slug'];
		?>
        <div class="one-page-express-enable-companion">
			<?php
			printf( '<p>%1$s</p>', $msg );
			printf( '<a class="%1$s button" href="%2$s">%3$s</a>', "activate", esc_url( $this->get_link( $slug ) ),
				$label );
			?>
        </div>
		<?php
	}

	public function get_link( $slug = false ) {
		$tgmpa = \TGM_Plugin_Activation::get_instance();
		$path  = $tgmpa->plugins[ $slug ]['file_path'];

		return add_query_arg( array(
			'action'        => 'activate',
			'plugin'        => rawurlencode( $path ),
			'plugin_status' => 'all',
			'paged'         => '1',
			'_wpnonce'      => wp_create_nonce( 'activate-plugin_' . $path ),
		), network_admin_url( 'plugins.php' ) );
	}
}

class Plugin_Control extends \WP_Customize_Control {
	public $plugin_slug;
	public $plugin_path;
	public $plugin_name;
	public $after;

	public function render_content() {
		$status = $this->getPluginStatus();
		$label  = sprintf( __( 'Install %s', 'mesmerize' ), $this->plugin_name );
		$class  = "";


		if ( $status === 'installed' ) {
			$label = sprintf( __( 'Activate %s', 'mesmerize' ), $this->plugin_name );
		}


		if ( $status === 'active' ) {
			$label = sprintf( __( '%s is active', 'mesmerize' ), $this->plugin_name );
			$class = "disabled";
		}


		?>
        <div class="extendthemes-install-plugin">
			<?php printf( '<p>%1$s</p>', $this->description ); ?>

			<?php if ( $status === "active" ): ?>
                <p class="plugin-installed"><?php echo esc_html( $label ); ?></p>
			<?php else: ?>
                <div class="button-wrapper">
					<?php
					printf( '<a  data-plugin-install="1" href="javascript:void(0)" class="%s button" data-activate-href="%s" data-slug="%s" data-status="%s">%s</a>',
						$class,
						esc_url( $this->get_activate_link() ),
						$this->plugin_slug,
						esc_attr( $status ),
						$label );
					?>
                    <span class="spinner"></span>
                </div>
			<?php endif; ?>
			<?php
			if ( $this->after ) {
				call_user_func( $this->after, $this );
			}
			?>
        </div>
		<?php
	}

	public function getPluginStatus() {
		$status = "not-installed";
		if ( $this->isPluginInstalled() ) {
			$status = "installed";

			if ( $this->isPluginActive() ) {
				$status = "active";
			}

		}

		return $status;
	}

	public function isPluginInstalled() {
		return file_exists( WP_PLUGIN_DIR . "/{$this->plugin_path}" );
	}

	public function isPluginActive() {
		return (
			in_array( $this->plugin_path, (array) get_option( 'active_plugins', array() ) )
			|| in_array( $this->plugin_path, (array) get_option( 'active_sitewide_plugins', array() ) )
		);
	}

	public function get_activate_link() {
		return add_query_arg( array(
			'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $this->plugin_path ),
			'action'   => 'activate',
			'plugin'   => $this->plugin_path,
		), network_admin_url( 'plugins.php' ) );
	}

}

class Install_Companion_Control extends BaseControl {
	public function render_content() {
		$data  = $this->data;
		$label = $data['label'];
		$msg   = $data['msg'];
		$slug  = $data['slug'];
		?>
        <div class="one-page-express-enable-companion">
			<?php
			printf( '<p>%1$s</p>', $msg );
			printf( '<a class="%1$s button" href="%2$s">%3$s</a>', "install-now", esc_url( $this->get_link( $slug ) ),
				$label );
			?>
        </div>
		<?php
	}

	public function get_link( $slug = false ) {
		return add_query_arg(
			array(
				'action'   => 'install-plugin',
				'plugin'   => $slug,
				'_wpnonce' => wp_create_nonce( 'install-plugin_' . $slug ),
			),
			network_admin_url( 'update.php' )
		);
	}
}


class Kirki_Controls_Radio_HTML_Control extends \Kirki_Controls_Radio_Image_Control {
	public $type = 'radio-html';

	protected function content_template() {
		?>
        <# if ( data.tooltip ) { #>
        <a href="#" class="tooltip hint--left" data-hint="{{ data.tooltip }}"><span
                    class='dashicons dashicons-info'></span></a>
        <# } #>
        <label class="customizer-text">
            <# if ( data.label ) { #>
            <span class="customize-control-title">{{{ data.label }}}</span>
            <# } #>
            <# if ( data.description ) { #>
            <span class="description customize-control-description">{{{ data.description }}}</span>
            <# } #>
        </label>
        <div id="input_{{ data.id }}" class="image">
            <# for ( key in data.choices ) { #>
            <input {{{ data.inputAttrs }}} class="image-select" type="radio" value="{{ key }}"
                   name="_customize-radio-{{ data.id }}" id="{{ data.id }}{{ key }}" {{{ data.link }}}<# if ( data.value
            === key ) { #> checked="checked" <# } #>>
            <label for="{{ data.id }}{{ key }}">
                <div class="{{ data.choices[ key ] }} image-clickable"></div>
            </label>
            </input>
            <# } #>
        </div>
		<?php
	}
}


class Kirki_Controls_Separator_Control extends \WP_Customize_Control {
	public $type = 'sectionseparator';

	public function content_template() {
		?>
        <# if ( data.tooltip ) { #>
        <a href="#" class="tooltip hint--left" data-hint="{{ data.tooltip }}"><span
                    class='dashicons dashicons-info'></span></a>
        <# } #>
        <div class="mesmerize-separator">
            <# if ( data.label ) { #>
            <span class="customize-control-title">{{{ data.label }}}</span>
            <# } #>
        </div>
		<?php
	}
}


class Info_Control extends \WP_Customize_Control {
	public $type = 'ope-info';


	public function render_content() {

		$proLink   = mesmerize_get_upgrade_link();
		$proText   = esc_html__( 'Check all PRO features', 'mesmerize' );
		$proButton = "<br/><a href='$proLink' class='button button-small button-orange upgrade-to-pro' target='_blank'>$proText</a>";
		$label     = str_replace( "@BTN@", $proButton, $this->label );
		?>
        <p><?php echo $label ?></p>
		<?php
	}
}

class Info_PRO_Control extends Info_Control {
	public $type = 'ope-info-pro';


	protected function render() {
		if ( ! $this->active_callback() ) {
			return;
		}
		parent::render();
	}


	public function active_callback() {
		$active = apply_filters( 'mesmerize_show_info_pro_messages', true );

		return $active;
	}

}

class Info_PRO_Section extends \WP_Customize_Section {
	public $type = "themes";

	protected function render() {
		if ( ! $this->active_callback() ) {
			echo "";

			return;
		}

		$classes     = 'try-pro accordion-section control-section control-section-' . $this->type;
		$upgrade_url = mesmerize_get_upgrade_link();

		?>
        <li id="accordion-section-<?php echo esc_attr( $this->id ); ?>" class="<?php echo esc_attr( $classes ); ?>">
            <div class="ope-pro-header accordion-section-title">
                <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank"
                   class="button"><?php _e( "Upgrade to PRO", 'mesmerize' ) ?></a>
            </div>
        </li>

		<?php ;
	}

	public function active_callback() {
		$active = apply_filters( 'mesmerize_show_main_info_pro_messages', true );

		return $active;
	}

}


class FontAwesomeIconControl extends \Kirki_Customize_Control {
	private static $enqueued = false;
	public $type = 'font-awesome-icon-control';
	public $button_label = '';

	public function __construct( $manager, $id, $args = array() ) {
		$this->button_label = esc_html__( 'Change Icon', 'mesmerize' );

		parent::__construct( $manager, $id, $args );
	}


	public function enqueue() {

		if ( static::$enqueued ) {
			return;
		}

		static::$enqueued = true;

		wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/font-awesome/font-awesome.min.css' );


		if ( ! apply_filters( 'mesmerize_load_bundled_version', true ) ) {
			wp_enqueue_style( 'font-awesome-media-tab', get_template_directory_uri() . "/customizer/css/fa-tab.css",
				array( 'media-views' ) );
			wp_enqueue_script( 'font-awesome-media-tab', get_template_directory_uri() . "/customizer/js/fa-tab.js",
				array( 'media-views' ) );
			wp_enqueue_script( 'font-awesome-icon-control',
				get_template_directory_uri() . "/customizer/js/font-awesome-icon-control.js" );
			wp_localize_script( 'font-awesome-icon-control', 'ficTexts', array(
				'media_title'        => esc_html__( 'Select FontAwesome Icon', 'mesmerize' ),
				'media_button_label' => esc_html__( 'Choose Icon', 'mesmerize' ),
			) );
		} else {
			wp_localize_script( 'customize-base', 'ficTexts', array(
				'media_title'        => esc_html__( 'Select FontAwesome Icon', 'mesmerize' ),
				'media_button_label' => esc_html__( 'Choose Icon', 'mesmerize' ),
			) );
		}

	}


	public function to_json() {
		parent::to_json();
		$this->json['button_label'] = $this->button_label;
	}


	protected function content_template() {
		?>
        <label for="{{ data.settings['default'] }}-button">
            <# if ( data.label ) { #>
            <span class="customize-control-title">{{ data.label }}</span>
            <# } #>
            <# if ( data.description ) { #>
            <span class="description customize-control-description">{{{ data.description }}}</span>
            <# } #>
        </label>

        <div class="fic-icon-container">
            <div class="fic-icon-preview">
                <i class="fa {{data.value}}"></i>
                <input type="hidden" value="{{ data.value }}" name="_customize-input-{{ data.id }}" {{{ data.link }}}/>
            </div>
            <div class="fic-controls">
                <button type="button" class="button upload-button control-focus" id="_customize-button-{{ data.id }}">
                    {{{ data.button_label }}}
                </button>
            </div>
        </div>
		<?php

	}
}

class FrontPageSection extends \WP_Customize_Section {

	protected $show_title = true;

	protected function render() {
		?>

        <li id="accordion-section-<?php echo esc_attr( $this->id ); ?>"
            class="accordion-section control-section control-section-<?php echo esc_attr( $this->type ); ?> companion-needed-section">
            <style>
                #accordion-section-<?php echo esc_attr($this->id);?> h3.accordion-section-title:after {
                    display: none;
                }

                #accordion-section-<?php echo esc_attr($this->id);?> li.install-companion {
                    padding: 10px 20px;
                    text-align: center;
                    font-size: 16px;
                    color: #6d6d6d;
                    line-height: 150%;
                }
            </style>
			<?php if ( $this->show_title ): ?>
                <h3 class="accordion-section-title" tabindex="0">
					<?php echo esc_html( $this->title ); ?>
                    <span class="screen-reader-text"><?php _e( 'Press return or enter to open this section',
							'mesmerize' ); ?></span>
                </h3>
			<?php endif; ?>
            <div class="sections-list-reorder">
                <ul id="page_full_rows" class="list list-order">
                    <li class="">
                        <div class="customize-control-ope-info">
                            <p style="text-align: center;padding: 10px;font-size: 1em;background: #b9dbf7;">
								<?php esc_html_e( 'Please Install the Mesmerize Companion Plugin to Enable All the Theme Features',
									'mesmerize' ) ?>
                                <span style="display: block">
								<?php
								if ( \Mesmerize\Companion_Plugin::$plugin_state['installed'] ) {
									$mesmerize_link  = \Mesmerize\Companion_Plugin::get_activate_link();
									$mesmerize_label = esc_html__( 'Activate now', 'mesmerize' );
								} else {
									$mesmerize_link  = \Mesmerize\Companion_Plugin::get_install_link();
									$mesmerize_label = esc_html__( 'Install now', 'mesmerize' );
								}
								printf( '<a class="button button-small button-orange upgrade-to-pro" href="%1$s">%2$s</a>',
									esc_url( $mesmerize_link ),
									$mesmerize_label );
								?>
                            </span>
                            </p>
                        </div>

                    </li>
                </ul>
            </div>
            <script type="text/javascript">
                jQuery(function ($) {
                    jQuery('.companion-needed-section,.companion-needed-section > *').off();
                    jQuery('body').on('click', '.companion-needed-section h3', function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                    });
                })
            </script>
        </li>

		<?php

	}
}
