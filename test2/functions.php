<?php

function test2_scripts() {
    wp_enqueue_style('style-css', get_stylesheet_uri(), ['bootstrap'] );
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css');
    wp_enqueue_style('animate', get_template_directory_uri() . '/css/animate.min.css');
    wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/font-awesome.min.css');

    wp_enqueue_script('jquery');
    wp_enqueue_script('interface', get_template_directory_uri() . '/js/interface.js', ['bootstrapjs']);
    wp_enqueue_script('bootstrapjs', get_template_directory_uri() . '/js/bootstrap.min.js', ['jquery']);
    // wp_enqueue_script('agency', get_template_directory_uri() . '/js/agency.js');
    // wp_enqueue_script('custom', get_template_directory_uri() . '/js/custom.js');
    wp_enqueue_script('some-scripts', get_template_directory_uri() . '/js/some_scripts.js', ['interface']);



}
add_action('wp_enqueue_scripts', 'test2_scripts');

function test2_setup(){
    add_theme_support('post-thumbnails');

    add_theme_support('custom-logo', array(
        'height' => 31, 
        'width' => 134, 
        'flex-height' => true
    ));

    register_nav_menu('primary', 'Primary menu');
}
add_action('after_setup_theme', 'test2_setup');

add_filter( 'nav_menu_css_class', 'change_menu_item_css_classes', 10, 4 );

function change_menu_item_css_classes( $classes, $item, $args, $depth ) {
	if ( $args->theme_location === 'primary' ) {
		$classes = [ 'js-target-scroll' ];
	} else {
		$classes = [];
	}

	return $classes;
}

function test2_customize_register( $wp_customize ) {
    $wp_customize->add_setting( 'header_social' , array(
        'default'   => __('Share Your Favorite Mobile Apps With Your Friends','test2'),
        'transport' => 'refresh',
    ));
    $wp_customize->add_setting( 'footer_social_backgroundcolor' , array(
        'default'   => '#000000',
        'transport' => 'refresh',
    ));
    $wp_customize->add_setting( 'footer_copy_backgroundcolor' , array(
        'default'   => '#000000',
        'transport' => 'refresh',
    ));
    $wp_customize->add_setting( 'facebook_social' , array(
        'default'   => __('Url facebook','test2'),
        'transport' => 'refresh',
    ));
    $wp_customize->add_setting( 'twitter_social' , array(
        'default'   => __('Url twitter','test2'),
        'transport' => 'refresh',
    ));
    $wp_customize->add_setting( 'linkedin_social' , array(
        'default'   => __('Url linkedin','test2'),
        'transport' => 'refresh',
    ));
    $wp_customize->add_setting( 'googleplus_social' , array(
        'default'   => __('Url googleplus','test2'),
        'transport' => 'refresh',
    ));
    $wp_customize->add_setting( 'youtube_social' , array(
        'default'   => __('Url youtube','test2'),
        'transport' => 'refresh',
    ));
    $wp_customize->add_setting( 'instagram_social' , array(
        'default'   => __('Url instagram','test2'),
        'transport' => 'refresh',
    ));
    $wp_customize->add_setting( 'footer_copy' , array(
        'default'   => __('copyright text','test2'),
        'transport' => 'refresh',
    ));
    $wp_customize->add_section( 'social_section' , array(
        'title'      => __( 'social section', 'test2' ),
        'priority'   => 30,
    ));
    $wp_customize->add_section( 'footer_settings' , array(
        'title'      => __( 'Footer settings', 'test2' ),
        'priority'   => 31,
    ));
    $wp_customize->add_control(
        'header_social', 
        array(
            'label'    => __( 'Social header in footer', 'test2' ),
            'section'  => 'social_section',
            'settings' => 'header_social',
            'type'     => 'text',
            ));
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_color', array(
        'label'      => __( 'Footer social Color', 'test2' ),
        'section'    => 'social_section',
        'settings'   => 'footer_social_backgroundcolor',
    ) ) );
    $wp_customize->add_control(
        'facebook_social', 
        array(
            'label'    => __( 'facebook profile url', 'test2' ),
            'section'  => 'social_section',
            'settings' => 'facebook_social',
            'type'     => 'text',
            ));
    $wp_customize->add_control(
        'twitter_social', 
        array(
            'label'    => __( 'twitter profile url', 'test2' ),
            'section'  => 'social_section',
            'settings' => 'twitter_social',
            'type'     => 'text',
            ));
    $wp_customize->add_control(
        'linkedin_social', 
        array(
            'label'    => __( 'linkedin profile url', 'test2' ),
            'section'  => 'social_section',
            'settings' => 'linkedin_social',
            'type'     => 'text',
            ));
    $wp_customize->add_control(
        'googleplus_social', 
        array(
            'label'    => __( 'googleplus profile url', 'test2' ),
            'section'  => 'social_section',
            'settings' => 'googleplus_social',
            'type'     => 'text',
            ));
    $wp_customize->add_control(
        'youtube_social', 
        array(
            'label'    => __( 'youtube profile url', 'test2' ),
            'section'  => 'social_section',
            'settings' => 'youtube_social',
            'type'     => 'text',
            ));
    $wp_customize->add_control(
        'instagram_social', 
        array(
            'label'    => __( 'instagram profile url', 'test2' ),
            'section'  => 'social_section',
            'settings' => 'instagram_social',
            'type'     => 'text',
            ));
    $wp_customize->add_control(
        'footer_copy', 
        array(
            'label'    => __( 'Footer settings', 'test2' ),
            'section'  => 'footer_settings',
            'settings' => 'footer_copy',
            'type'     => 'textarea',
            ));
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_color', array(
        'label'      => __( 'Footer copy Color', 'test2' ),
        'section'    => 'footer_settings',
        'settings'   => 'footer_copy_backgroundcolor',
    ) ) );
 }
 add_action( 'customize_register', 'test2_customize_register' );