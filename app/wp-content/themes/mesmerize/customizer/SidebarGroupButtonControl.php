<?php

namespace Mesmerize;


class SidebarGroupButtonControl extends \Kirki_Customize_Control
{

    public $type = "sidebar-button-group";
    public $popupId = '';
    public $in_row_with = array();

    public function __construct($manager, $id, $args = array())
    {
        $this->popupId     = uniqid('cp-sidebar-button-group-');
        $this->in_row_with = isset($args['in_row_with']) ? $args['in_row_with'] : array();
        parent::__construct($manager, $id, $args);
    }

    public function enqueue()
    {
        
        if ( ! apply_filters('mesmerize_load_bundled_version', true)) {
        	$jsRoot = get_template_directory_uri() . "/customizer/js";
        	wp_enqueue_script('mesmerize-sb-group-button-control', $jsRoot . "/sb-group-button-control.js");
        }
    }

    public function json()
    {
        $fields  = \Kirki::$fields;
        $grouped = array();
        foreach ($fields as $field) {
            if (isset($field['group']) && $field['group'] == $this->setting->id) {
                $grouped[] = $field["settings"];
            }
        }
        if ( ! count($grouped)) {
            $grouped = $this->choices;
        }

        $grouped = apply_filters($this->setting->id . "_filter", (array)$this->choices + $grouped);
        
        if (count($grouped)) {
            $this->choices = $grouped;
        }

        $json                = parent::json();
        $json['popup']       = $this->popupId;
        $json['in_row_with'] = $this->in_row_with;

        return $json;
    }

    protected function content_template()
    {
        ?>

        <label>

            <# if ( data.description ) { #>
            <span class="title customize-control-title" style="visibility: hidden;">{{{ data.description }}}</span>
            <# } #>

            <button type="button" class="button" data-sidebar-container="{{ data.popup }}" id="group_customize-button-{{ data.popup }}">
                {{{ data.label }}}
            </button>
        </label>

        <div id="{{ data.popup }}-popup" class="customizer-right-section">
            <span data-close-right-sidebar="true" title="<?php esc_attr_e("Close Panel", 'mesmerize'); ?>" class="close-panel"></span>
            <ul class="section-settings-container accordion-section-content no-border"></ul>
        </div>
        <?php

    }
}
