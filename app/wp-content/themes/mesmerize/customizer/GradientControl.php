<?php

namespace Mesmerize;

class GradientControl extends \Kirki_Customize_Control
{
    public $type = 'gradient-control';
    public $button_label = '';
    
    
    public function __construct($manager, $id, $args = array())
    {
        parent::__construct($manager, $id, $args);
        
        $this->button_label = esc_html__('Select Gradient', 'mesmerize');
    }
    
    
    public function enqueue()
    {
        $jsRoot = get_template_directory_uri() . "/customizer/js";
        wp_enqueue_script(mesmerize_get_text_domain() . '-gradient-control', $jsRoot . "/gradient-control.js", array('mesmerize-customizer-spectrum'));
    }
    
    
    public function to_json()
    {
        
        parent::to_json();
        
        $gradient = $this->json['value'];
        
        if (is_string($gradient)) {
            $gradient = json_decode($gradient, true);
        }

        if (!is_array($gradient)) {
            $gradient = array('colors' => array(), 'angle' => 0);
        }
        
        
        $this->json['button_label'] = $this->button_label;
        $this->json['gradient']     = mesmerize_get_gradient_value($gradient['colors'], $gradient['angle']);
        $this->json['angle']        = intval($gradient['angle']);
    }
    
    
    protected function content_template()
    {
        ?>
        <label for="{{ data.settings['default'] }}-button">
            <# if ( data.label ) { #>
            <span class="customize-control-title">{{ data.label }}</span>
            <# } #>
            <# if ( data.description ) { #>
            <span class="description customize-control-description">{{{ data.description }}}</span>
            <# } #>
        </label>

        <div class="webgradient-icon-container">
            <div class="webgradient-icon-preview">
                <div class="webgradient" style="background: {{data.gradient}}"></i>
                </div>
                <div class="webgradient-controls">
                    <button type="button" class="button upload-button control-focus" id="_customize-button-{{ data.id }}">{{{ data.button_label }}}</button>
                </div>
            </div>
        
        <?php
    }
}
