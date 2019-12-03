<?php

/* Google api option page */

class PokemonOptions {
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
            'Pokemon Settings Admin',
            'Pokemon Settings',
            'manage_options',
            'pokemon-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        // Set class property
        $this->options = get_option( 'pokemon_api_option' );
        ?>
        <div class="pokemon_wrap">
            <h1>Pokemon Page Settings</h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields( 'pokemon_option_group' );
                do_settings_sections( 'pokemon-setting-admin' );
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
            'pokemon_option_group', // Option group
            'pokemon_api_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Request Settings', // Title
            array(), // Callback
            'pokemon-setting-admin' // Page
        );

        add_settings_field(
            'access_token',
            'Pokemon Access Token',
            array( $this, 'access_token_callback' ),
            'pokemon-setting-admin',
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
            '<input type="text" id="title" name="pokemon_api_option[access_token]" value="%s" />',
            isset( $this->options['access_token'] ) ? esc_attr( $this->options['access_token']) : ''
        );
    }
}

if( is_admin() )
    $my_settings_page = new PokemonOptions();
