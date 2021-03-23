<?php if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists('ET_CT_INIT') ){
    class ET_CT_INIT
    {

        public function __construct()
        {
            /* CT defaults */
            define( 'ET_CT_PATH', get_stylesheet_directory() );
            define( 'ET_CT_PATH_URI', get_stylesheet_directory_uri() );
            define( 'ET_CT_PREFIX', 'et_ct_' );

            require_once ( ET_CT_PATH . '/lib/input-handler.php' );

            /* Child theme options sub-menu page */
            add_action( 'admin_menu', [ $this, 'add_child_theme_submenu_page' ], 999);

            /* Setting sections for sub-menu page */
            add_action( 'admin_init', [ $this, 'child_theme_options_setting_section_setup' ] );
            
            /* settings feilds setup */
            add_action( 'admin_init', [ $this, 'child_theme_setup_setting_fields' ] );

            if( is_admin() ){
                add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_files' ] );
            }
        }

        public function enqueue_admin_files()
        {
            //admin styles
            wp_enqueue_style( 'et-ct-admin-styles', ET_CT_PATH_URI . '/assets/css/et-ct-admin-styles.css', array(), '1.0.0');
                        
            //admin scripts
            wp_enqueue_script( 'et-ct-admin-script', ET_CT_PATH_URI . '/assets/js/et-ct-admin-scripts.js',  array('jquery'),'1.0.0',true);
        }


        public function add_child_theme_submenu_page()
        {
            $parent_slug        = 'et_divi_options';
            $page_title         = 'Child Theme Options';
            $menu_title         = 'Child Theme Options';
            $capability         = 'manage_options';
            $menu_slug          = 'et_ct_child_theme_options';
            $function           = [ $this, 'child_theme_submenu_page_callback' ];
            $position           = 99;
            add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function, $position);   
        }

        public function child_theme_submenu_page_callback()
        {
            require_once ( ET_CT_PATH . '/lib/html/template/options-page.php' );
        }


        public function child_theme_options_setting_section_setup()
        {
            $id             = 'et_ct_child_theme_setting_section';
            $title          = 'Available Options';
            $callback       = [ $this, 'child_theme_setting_section_callback' ];
            $page           = 'et_ct_child_theme_options';
            add_settings_section( $id, $title, $callback, $page );
        }

        public function child_theme_setting_section_callback()
        {
            //code...
        }


        public function child_theme_setup_setting_fields()
        {
            $settings_fields = ET_CT_Input_Handler::get_input_fields();

        
            foreach($settings_fields as $field){
                
                $id                 = $field['id'];
                $title              = $field['title'];
                $callback           = [ $this, 'et_ct_setting_fields_render_callback' ];
                $page               = 'et_ct_child_theme_options';
                $section            = 'et_ct_child_theme_setting_section';
                $args               = $field;
                add_settings_field( $id, $title, $callback, $page, $section, $args );
                
                $args = array(
                    'default'   => $field['default'],
                );
                register_setting( 'et_ct_child_theme_options', $field['id'], $args);
            }
        }


        public function et_ct_setting_fields_render_callback( $arguments )
        {
            ET_CT_Input_Handler::render_field( $arguments );
         
        }

    }
}

new ET_CT_INIT();

?>