<?php

/**
 * Load Parent Theme's style.css
 */
add_action( 'wp_enqueue_scripts', 'et_ct_load_parent_styles' );
function et_ct_load_parent_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}


/**
 * Load Child Theme's files
 */
add_action( 'init', 'et_ct_load_child_theme_files' );
function et_ct_load_child_theme_files(){
    require_once ( get_stylesheet_directory() . '/lib/init.php' );
}


/**
 * --------------------
 * ADD CUSTOM CODE HERE
 * --------------------
 */