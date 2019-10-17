<?php
add_action('wp_enqueue_scripts', function(){
    wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
    wp_enqueue_style('custom', get_stylesheet_directory_uri() . '/assets/css/custom.css', array('parent-style'));

    wp_enqueue_script('index', get_stylesheet_directory_uri() . '/assets/js/index.js', array('jquery'));
});

function load_custom_wp_admin_style() {
    wp_register_style( 'custom_wp_admin_css', get_stylesheet_directory_uri() . '/inc/css/api_options.css', false, '1.0.0' );
    wp_enqueue_style( 'custom_wp_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );