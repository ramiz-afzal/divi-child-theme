<?php if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists('ET_CT_Input_Handler') ){
    class ET_CT_Input_Handler
    {

        private static $input_fields = array();

        public function __construct()
        {
            ET_CT_Input_Handler::load_input_fields();
        }


        private static function load_input_fields()
        {
            ET_CT_Input_Handler::$input_fields = array(
                
                array(
                    'id'            => 'et_ct_child_theme_js_file',
                    'title'         => 'Include Child theme JS',
                    'type'          => 'toggle',
                    'placeholder'   => false,
                    'default'       => 'unchecked'
                ),
                array(
                    'id'            => 'et_ct_child_theme_css_file',
                    'title'         => 'Include Child theme CSS',
                    'type'          => 'toggle',
                    'placeholder'   => false,
                    'default'       => 'unchecked'
                ),
                array(
                    'id'            => 'et_ct_enable_proloader',
                    'title'         => 'Enable Preloader',
                    'type'          => 'toggle',
                    'placeholder'   => false,
                    'default'       => 'unchecked'
                ),
                array(
                    'id'            => 'et_ct_enable_duplicate_post',
                    'title'         => 'Enable Duplicate Post',
                    'type'          => 'toggle',
                    'placeholder'   => false,
                    'default'       => 'unchecked'
                ),
                array(
                    'id'            => 'et_ct_disable_emoji',
                    'title'         => 'Disable Emoji',
                    'type'          => 'toggle',
                    'placeholder'   => false,
                    'default'       => 'unchecked'
                ),
                array(
                    'id'            => 'et_ct_enable_svg_support',
                    'title'         => 'Enable SVG Support',
                    'type'          => 'toggle',
                    'placeholder'   => false,
                    'default'       => 'unchecked'
                ),
                array(
                    'id'            => 'et_ct_disable_xml_rpc',
                    'title'         => 'Disable XML-RPC',
                    'type'          => 'toggle',
                    'placeholder'   => false,
                    'default'       => 'unchecked'
                ),
                array(
                    'id'            => 'et_ct_limit_post_revisions',
                    'title'         => 'Limit Post Revisions',
                    'type'          => 'toggle',
                    'placeholder'   => false,
                    'default'       => 'unchecked'
                ),
                array(
                    'id'            => 'et_ct_post_revision_num_count',
                    'title'         => 'Limit To',
                    'type'          => 'number',
                    'placeholder'   => false,
                    'default'       => 5,
                    'parent'        => 'et_ct_limit_post_revisions',
                    'parent_value'  => 'checked',
                ),
                array(
                    'id'            => 'et_ct_disable_plugin_update',
                    'title'         => 'Disable Plugin Updates',
                    'type'          => 'toggle',
                    'placeholder'   => false,
                    'default'       => 'unchecked'
                ),
                array(
                    'id'            => 'et_ct_disable_theme_update',
                    'title'         => 'Disable Theme Updates',
                    'type'          => 'toggle',
                    'placeholder'   => false,
                    'default'       => 'unchecked'
                ),
                array(
                    'id'            => 'et_ct_disable_core_update',
                    'title'         => 'Disable Core Updates',
                    'type'          => 'toggle',
                    'placeholder'   => false,
                    'default'       => 'unchecked'
                ),

            );
        }

        public static function get_input_fields()
        {
            $fields = array();

            foreach( ET_CT_Input_Handler::$input_fields as $field ){

                if( isset( $field['parent'] ) && isset( $field['parent_value'] ) ){

                    if( get_option( $field['parent'] ) !== $field['parent_value'] ){
                        continue;
                    }
    
                }

                array_push( $fields, $field );

            }

            return $fields;
        }

        public static function render_field( $arguments = array() )
        {

            $value = get_option( $arguments['id'] );
            if( ! $value ) {
                $value = $arguments['default'];
            }
            
            switch( $arguments['type'] ){
                case 'text':
                    printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['id'], $arguments['type'], $arguments['placeholder'], $value );
                break;
                
                case 'number':
                    printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['id'], $arguments['type'], $arguments['placeholder'], $value );
                break;

                case 'toggle':
                    $checked_attr = $value == 'checked' ? 'checked' : '';
                    echo '<label class="et_ct_switch">';
                    echo '<input name="'.$arguments['id'].'" value="'.$value.'" type="checkbox" '.$checked_attr.'>';
                    echo '<span class="et_ct_slider"></span>';
                    echo '</label>';
                break;

                case 'select':
                    echo '<select name="'.$arguments['id'].'">';
                    foreach($arguments['options'] as $option){
                        $selected = ($option['value'] == $value) ? 'selected' : '';
                        echo '<option value="'.$option['value'].'" '.$selected.'>'.$option['label'].'</option>';
                    }
                    echo '</select>';
                break;
            }

        }

    }
}

new ET_CT_Input_Handler();