<?php
add_action('customize_register', 'psy_customize_register', 10, 3);
function psy_customize_register($wp_customize) {
    $wp_customize->add_section('header_elements', array(
        "title" => __('Other Header Elements', 'psycho'),
    ));
    $wp_customize->add_setting('free_phone', array(
        'default'=> '',
        'transport'=> 'postMessage',
    ));
    $wp_customize->add_control('free_phone', array(
        'label'=> __('Free phone','psycho'),
        'section'=> 'header_elements',
        'setting'=> 'header_elements_code',
        'type'=> 'text',
    ));
    $wp_customize->add_setting('phone', array(
        'default'=> '',
        'transport'=> 'postMessage',
    ));
    $wp_customize->add_control('phone', array(
        'label'=> __('Phone','psycho'),
        'section'=> 'header_elements',
        'setting'=> 'header_elements_code',
        'type'=> 'text',
    ));
    $wp_customize->add_setting('description', array(
        'default'=> '',
        'transport'=> 'postMessage',
    ));
    $wp_customize->add_control('description', array(
        'label'=> __('Description','psycho'),
        'section'=> 'header_elements',
        'setting'=> 'header_elements_code',
        'type'=> 'textarea',
    ));
}
