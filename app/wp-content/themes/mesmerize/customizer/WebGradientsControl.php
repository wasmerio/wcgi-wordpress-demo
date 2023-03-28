<?php

namespace Mesmerize;


class WebGradientsControl extends \Kirki_Customize_Control {
	public $type = "web-gradients";

	public function __construct( $manager, $id, $args = array() ) {
		$this->button_label = __( 'Change Gradient', 'mesmerize' );
		parent::__construct( $manager, $id, $args );
	}

	public function enqueue()
    {
        $jsRoot = get_template_directory_uri() . "/customizer/js";
        
        if ( ! apply_filters('mesmerize_load_bundled_version', true)) {
            wp_enqueue_script('mesmerize-webgradients-media-tab', $jsRoot . "/webgradients-media-tab.js", array('media-views'));
            wp_enqueue_script('mesmerize-webgradients-control', $jsRoot . "/webgradients-control.js", array("mesmerize-webgradients-media-tab"));
        }
    }

	public function to_json() {
		parent::to_json();
		$this->json['button_label'] = $this->button_label;
	}


	protected function content_template() {
		?>
        <# if ( data.tooltip ) { #>
            <a href="#" class="tooltip hint--left" data-hint="{{ data.tooltip }}"><span class='dashicons dashicons-info'></span></a>
            <# } #>
                <label>
                    <# if ( data.label ) { #>
                        <span class="customize-control-title">{{{ data.label }}}</span>
                        <# } #>
                            <# if ( data.description ) { #>
                                <span class="description customize-control-description">{{{ data.description }}}</span>
                                <# } #>
                </label>

                <div class="webgradient-icon-container">
                    <div class="webgradient-icon-preview">
                        <div class="webgradient {{data.value}}"></i>
                            <input type="hidden" value="{{ data.value }}" name="_customize-input-{{ data.id }}" {{{ data.link }}}/>
                        </div>
                        <div class="label">{{data.value.replace(/_/ig,' ')}}</div>
                        <div class="webgradient-controls">
                            <button type="button" class="button upload-button control-focus" id="_customize-button-{{ data.id }}">{{{ data.button_label }}}</button>
                        </div>
                    </div>
		<?php

	}
}
