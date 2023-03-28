<?php


if (class_exists('WPForms_Template')) {
    
    class Mesmerize_WPForms_Template_Contact extends WPForms_Template
    {
        
        /**
         * Primary class constructor.
         *
         * @since 1.0.0
         */
        public function init()
        {
            
            $this->name        = esc_html__('Mesmerize Contact Form Template', 'mesmerize');
            $this->slug        = 'mesmerize-contact';
            $this->description = esc_html__('This is a simple contact form template designed to best fit the Mesmerize theme design. You can add and remove fields as needed.', 'mesmerize');
            $this->includes    = '';
            $this->icon        = '';
            $this->modal       = '';
            $this->core        = true;
            $this->data        = array(
                'field_id' => '3',
                'fields'   => array(
                    '0' => array(
                        'id'       => '0',
                        'type'     => 'name',
                        'label'    => 'Name',
                        'required' => '1',
                        'size'     => 'large',
                    ),
                    '1' => array(
                        'id'       => '1',
                        'type'     => 'email',
                        'label'    => 'Email',
                        'required' => '1',
                        'size'     => 'large',
                    ),
                    '2' => array(
                        'id'          => '2',
                        'type'        => 'textarea',
                        'label'       => 'Comment or Message',
                        'description' => '',
                        'required'    => '1',
                        'size'        => 'large',
                        'placeholder' => '',
                        'css'         => '',
                    ),
                ),
                'settings' => array(
                    'notifications'               => array(
                        '1' => array(
                            'replyto'        => '{field_id="1"}',
                            'sender_name'    => '{field_id="0"}',
                            'sender_address' => '{admin_email}',
                        ),
                    ),
                    "submit_class"                => "button color1",
                    'honeypot'                    => '1',
                    'confirmation_message_scroll' => '1',
                    'submit_text_processing'      => 'Sending...',
                ),
                'meta'     => array(
                    'template' => $this->slug,
                ),
            );
        }
    }
    
    new Mesmerize_WPForms_Template_Contact;
}
