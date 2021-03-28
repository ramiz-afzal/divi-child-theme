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
            
            /* Implement Settings */
            $this->implement_settings();

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

        public function implement_settings()
        {
            $settings_fields = ET_CT_Input_Handler::get_input_fields();

            foreach( $settings_fields as $field ){

                $this->setting_field_handler( $field['id'] );

            }
        }

        public function setting_field_handler( string $field_id = '' )
        {
            $value = get_option( $field_id );

            if( $value == 'checked' ){

                switch( $field_id ){

                    case 'et_ct_child_theme_js_file' :

                        add_action( 'wp_enqueue_scripts', function(){

                            wp_enqueue_script( 'et-ct-scripts', ET_CT_PATH_URI . '/assets/js/et-ct-scripts.js',  array('jquery'), '1.0.0', true);

                        } );

                    break;

                    case 'et_ct_child_theme_css_file' :
                        
                        add_action( 'wp_enqueue_scripts', function(){

                            wp_enqueue_style( 'et-ct-styles', ET_CT_PATH_URI . '/assets/css/et-ct-styles.css');

                        } );

                    break;

                    case 'et_ct_enable_proloader' :

                        add_action( 'wp_body_open', [ $this, 'et_ct_insert_page_preloader' ] );

                    break;
                    
                    case 'et_ct_enable_duplicate_post' :

                        add_action( 'admin_action_et_ct_duplicate_post_as_draft', [ $this, 'et_ct_duplicate_post_action_handler' ] );
                        add_filter( 'post_row_actions', [ $this, 'et_ct_insert_duplicate_post_button' ], 10, 2 );
                        add_filter( 'page_row_actions', [ $this, 'et_ct_insert_duplicate_post_button' ], 10, 2 );

                    break;
                    
                    case 'et_ct_disable_emoji' :

                        add_action( 'init', [ $this, 'et_ct_disable_emoji' ] );

                    break;
                    
                    case 'et_ct_enable_svg_support' :

                        add_filter( 'upload_mimes', [ $this, 'et_ct_add_svg_support' ], 10, 1 );
                        add_filter('wp_prepare_attachment_for_js', [ $this, 'et_ct_svg_media_thumbnails' ], 10, 3);

                    break;
    
                }

            }

        }

        public function et_ct_insert_page_preloader()
        {
            require_once ( ET_CT_PATH . '/lib/html/template/preloader.php' );
        }

        public function et_ct_duplicate_post_action_handler()
        {
            global $wpdb;
            if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'et_ct_duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
                wp_die('No post to duplicate has been supplied!');
            }
        
            /*
            * Nonce verification
            */
            if ( !isset( $_GET['duplicate_nonce'] ) || !wp_verify_nonce( $_GET['duplicate_nonce'], basename( __FILE__ ) ) )
                return;
            /*
            * get the original post id
            */
            $post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
            /*
            * and all the original post data then
            */
            $post = get_post( $post_id );
        
            /*
            * if you don't want current user to be the new post author,
            * then change next couple of lines to this: $new_post_author = $post->post_author;
            */
            $current_user = wp_get_current_user();
            $new_post_author = $current_user->ID;
        
            /*
            * if post data exists, create the post duplicate
            */
            if (isset( $post ) && $post != null) {
        
                /*
                * new post data array
                */
                $args = array(
                    'comment_status' => $post->comment_status,
                    'ping_status'    => $post->ping_status,
                    'post_author'    => $new_post_author,
                    'post_content'   => $post->post_content,
                    'post_excerpt'   => $post->post_excerpt,
                    'post_name'      => $post->post_name,
                    'post_parent'    => $post->post_parent,
                    'post_password'  => $post->post_password,
                    'post_status'    => 'draft',
                    'post_title'     => $post->post_title,
                    'post_type'      => $post->post_type,
                    'to_ping'        => $post->to_ping,
                    'menu_order'     => $post->menu_order
                );
        
                /*
                * insert the post by wp_insert_post() function
                */
                $new_post_id = wp_insert_post( $args );
        
                /*
                * get all current post terms ad set them to the new post draft
                */
                $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
                foreach ($taxonomies as $taxonomy) {
                    $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                    wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
                }
                /*
                * duplicate all post meta just in two SQL queries
                */
                $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
                if (count($post_meta_infos)!=0) {
                    $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                    foreach ($post_meta_infos as $meta_info) {
                        $meta_key = $meta_info->meta_key;
                        if( $meta_key == '_wp_old_slug' ) continue;
                        $meta_value = addslashes($meta_info->meta_value);
                        $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
                    }
                    $sql_query.= implode(" UNION ALL ", $sql_query_sel);
                    $wpdb->query($sql_query);
                }
        
        
                /*
                * finally, redirect to the edit post screen for the new draft
                */
                wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
                exit;
            } else {
                wp_die('Post creation failed, could not find original post: ' . $post_id);
            }
        }

        public function et_ct_insert_duplicate_post_button( $actions, $post )
        {
            if (current_user_can('edit_posts')) {
                $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=et_ct_duplicate_post_as_draft&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce' ) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
            }
            return $actions;
        }

        public function et_ct_disable_emoji()
        {
            /**
             * Remove hoorks to remove emoji
             */
            remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
            remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
            remove_action( 'wp_print_styles', 'print_emoji_styles' );
            remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
            remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
            remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
            remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
            
            /**
             * filters to remove emoji
             */
            add_filter( 'tiny_mce_plugins', [ $this, 'et_ct_disable_emojis_tinymce' ] );
            add_filter( 'wp_resource_hints',  [ $this, 'et_ct_disable_emojis_remove_dns_prefetch' ] , 10, 2 );
        }

        public function et_ct_disable_emojis_tinymce( $plugins )
        {
            if ( is_array( $plugins ) ) {
                return array_diff( $plugins, array( 'wpemoji' ) );
            } else {
                return array();
            }
        }

        public function et_ct_disable_emojis_remove_dns_prefetch()
        {
            if ( 'dns-prefetch' == $relation_type ) {
                
                /**
                 * This filter is documented in wp-includes/formatting.php
                 */

                $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
                $urls = array_diff( $urls, array( $emoji_svg_url ) );

            }
            
            return $urls;
        }

        public function et_ct_add_svg_support( $file_types )
        {
            $new_filetypes          = array();
            $new_filetypes['svg']   = 'image/svg+xml';
            $file_types             = array_merge( $file_types, $new_filetypes );

            return $file_types;
        }

        public function et_ct_svg_media_thumbnails( $response, $attachment, $meta )
        {
            if( $response['type'] === 'image' && $response['subtype'] === 'svg+xml' && class_exists('SimpleXMLElement') ){
                try {
                    $path = get_attached_file($attachment->ID);
                    if(@file_exists($path))
                    {
                        $svg    = new SimpleXMLElement(@file_get_contents($path));
                        $src    = $response['url'];
                        $width  = (int) $svg['width'];
                        $height = (int) $svg['height'];

                        //media gallery
                        $response['image'] = compact( 'src', 'width', 'height' );
                        $response['thumb'] = compact( 'src', 'width', 'height' );

                        //media single
                        $response['sizes']['full'] = array(
                            'height'        => $height,
                            'width'         => $width,
                            'url'           => $src,
                            'orientation'   => $height > $width ? 'portrait' : 'landscape',
                        );
                    }
                }
                catch(Exception $e){

                    if( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG == true ){
                        error_log( $e );
                    }

                }
            }

            return $response;
        }

    }
}

new ET_CT_INIT();

?>