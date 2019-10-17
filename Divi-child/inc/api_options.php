<?php

/* LinkedIn option page */
class LinkedInApiSettingPage {
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * getting access token
     */

    function linked_in_api_curl_connect($api_url, $request = '', $content_type = '') {
        $request = $request ? $request : "POST";
        $content_type = $content_type ? $content_type : 'Content-Type: application/x-www-form-urlencoded';
        $curl = curl_init();
        $headers = array(
        $content_type,
        'x-li-format: json',
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL             =>  $api_url,
            CURLOPT_CUSTOMREQUEST   =>  $request,
            CURLOPT_HTTPHEADER      =>  $headers,
            CURLOPT_RETURNTRANSFER  =>  true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    function get_linked_in_token() {
        $api_url = 'https://www.linkedin.com/oauth/v2/accessToken?grant_type=authorization_code&code=AQQeHDSxI4ER1c5G4iHywyREhnDT92eU4dEwdxEom-kZ-u9GmfS-JvixHrNjJBEeDvMvaLSlz0p1xgiXhS3Q-iRf1LVebIIUKPQWzzjbqH2btPf9zRgycO1EvwRA_XOdEolyiLAZohJEBFQlxIYAEYuZRcfJgwbJBfzDtQElA00TEMsB2r4z9t8oFEF-Kg&state=DCEeFWf45A53sdfKef498&redirect_uri=http://oshawa-dev.mifist.in.ua/&client_id=86hzfpgmn5amhv&client_secret=RiBG5NzRI8cU2jeV';
        $result = linked_in_api_curl_connect($api_url);
        $result = json_decode($result);
        return $result;

    }

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
            'LinkedIn Settings Admin',
            'LinkedIn API Settings',
            'manage_options',
            'linkedin-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        // Set class property
        $this->options = get_option( 'linkedin_api_option' );
        ?>
        <div class="linkedin_wrap">
            <h1>LinkedIn API Settings</h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'my-setting-admin' );
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
            'my_option_group', // Option group
            'linkedin_api_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Request Settings', // Title
            array(), // Callback
            'my-setting-admin' // Page
        );

        add_settings_field(
            'redirect_url', // ID
            'Redirect URL', // Title
            array( $this, 'redirect_url_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section
        );

        add_settings_field(
        'client_id',
        'Client ID',
        array( $this, 'client_id_callback' ),
        'my-setting-admin',
        'setting_section_id'
        );

        add_settings_field(
            'client_secret',
            'Client Secret',
            array( $this, 'client_secret_callback' ),
            'my-setting-admin',
            'setting_section_id'
        );

        add_settings_field(
            'authorization_code',
            'Authorization Code For An Access Token',
            array( $this, 'authorization_code_callback' ),
            'my-setting-admin',
            'setting_section_id'
        );

        add_settings_field(
            'access_token',
            'Access Token',
            array( $this, 'access_token_callback' ),
            'my-setting-admin',
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
        if( isset( $input['redirect_url'] ) )
            $new_input['redirect_url'] =  $input['redirect_url'];

        if( isset( $input['client_id'] ) )
            $new_input['client_id'] = $input['client_id'];

        if( isset( $input['client_secret'] ) )
            $new_input['client_secret'] = $input['client_secret'];

        if( isset( $input['authorization_code'] ) )
            $new_input['authorization_code'] = $input['authorization_code'];

        if( isset( $input['access_token'] ) )
            $new_input['access_token'] = $input['access_token'];

        return $new_input;
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function redirect_url_callback()
    {
        printf(
            '<input type="text" id="redirect_url_callback" name="linkedin_api_option[redirect_url_callback]" value="%s" />',
            isset( $this->options['redirect_url_callback'] ) ? esc_attr( $this->options['redirect_url_callback']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
        public function client_id_callback()
    {
        printf(
            '<input type="text" id="title" name="linkedin_api_option[client_id]" value="%s" />',
            isset( $this->options['client_id'] ) ? esc_attr( $this->options['client_id']) : ''
        );
    }

    public function client_secret_callback()
    {
        printf(
            '<input type="text" id="title" name="linkedin_api_option[client_secret]" value="%s" />',
            isset( $this->options['client_secret'] ) ? esc_attr( $this->options['client_secret']) : ''
        );
    }

    public function authorization_code_callback()
    {
        printf(
            '<input type="text" id="title" name="linkedin_api_option[authorization_code]" value="%s" />',
            isset( $this->options['authorization_code'] ) ? esc_attr( $this->options['authorization_code']) : ''
        );
    }

    public function access_token_callback()
    {
        printf(
            '<input type="text" id="title" name="linkedin_api_option[access_token]" value="%s" />',
            isset( $this->options['access_token'] ) ? esc_attr( $this->options['access_token']) : ''
        );
    }
}

if( is_admin() )
    $my_settings_page = new LinkedInApiSettingPage();
