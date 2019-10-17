<?php

wp_enqueue_script('Update', get_stylesheet_directory_uri() . '/inc/js/update_button.js', array('jquery'));
wp_localize_script('Update', 'instajax',
    array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
    )
);

add_action('wp_ajax_update_button', 'instagram_data_update');
add_action('wp_ajax_nopriv_update_button', 'instagram_data_update');
/* Instagram option page */
class InstagramApiSettingPage {
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page() {
        // This page will be under "Settings"
        add_options_page(
            'Instagram Settings Admin',
            'Instagram API Settings',
            'manage_options',
            'instagram-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        // Set class property
        $this->options = get_option( 'instagram_api_option' );
        ?>
        <div class="linkedin_wrap">
            <h1>Instagram API Settings</h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields( 'instagram_option_group' );
                do_settings_sections( 'instagram-setting-admin' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'instagram_option_group', // Option group
            'instagram_api_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Request Settings', // Title
            array(), // Callback
            'instagram-setting-admin' // Page
        );

        add_settings_field(
            'access_token',
            'Access Token',
            array( $this, 'access_token_callback' ),
            'instagram-setting-admin',
            'setting_section_id'
        );

        add_settings_field(
            'update_btn',
            'Add/Update Instagram Posts',
            array( $this, 'update_btn_callback' ),
            'instagram-setting-admin',
            'setting_section_id'
        );

    }
    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();

        if( isset( $input['access_token'] ) )
            $new_input['access_token'] = $input['access_token'];

        return $new_input;
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function access_token_callback()
    {
        printf(
            '<input type="text" id="title" name="instagram_api_option[access_token]" value="%s" />',
            isset( $this->options['access_token'] ) ? esc_attr( $this->options['access_token']) : ''
        );
    }

    public function update_btn_callback()
    {
        printf(
            '<button type="button" id="inst_update_btn" name="instagram_api_option[update_btn]" />Add/Update',
            isset( $this->options['update_btn'] ) ? esc_attr( $this->options['update_btn']) : ''
        );
    }
}

if( is_admin() )
    $my_settings_page = new InstagramApiSettingPage();
