<?php
namespace pokemons\mvc;
class model_pokemon {
    function __construct() {

        add_shortcode('poks_arch_single', array( &$this, 'pokemons_arch_shortcode_handler' ));
        add_shortcode('custom_poks', array( &$this, 'custom_poks_handler' ));
        add_action('wp_enqueue_scripts', array(&$this, 'enqueues'));
        add_action('wp_ajax_load_more', array(&$this, 'poks_load'));
        add_action('wp_ajax_nopriv_load_more', array(&$this, 'poks_load'));

    }
    function enqueues() {
        $filtered_poks_rest = array_slice(self::filtered_pokemons(), 15);
        wp_enqueue_style( 'pokemon_page_styles', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemonsmvc/assets/css/pokemon_page.css');
        wp_enqueue_style( 'slick_css', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemonsmvc/assets/slick/slick.css');
        wp_enqueue_style( 'slick_theme_css', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemonsmvc/assets/slick/slick-theme.css');

        wp_register_script('slick_min_js', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemonsmvc/assets/slick/slick.min.js', array('jquery'));
        wp_enqueue_script('main_js', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemonsmvc/assets/js/main.js', array('jquery', 'slick_min_js'));
        wp_localize_script('main_js', 'fivemorepoksajax',
            array(

                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'offset' => 0,
                'length' => count($filtered_poks_rest),
                'poks_arr' => $filtered_poks_rest,

            )
        );
    }
    /*Get current page id with part of content in it what we ne needed */
    public static function current_page_id($text) {
        global $wpdb;
        $current_post_id = $wpdb->get_var('SELECT ID FROM '.$wpdb->prefix.'posts WHERE post_content LIKE "'. $text .'" AND post_parent = 0');
        return $current_post_id;
    }
    /*
     * Get data from https://graphql-pokemon.now.sh/ graphQL server
     * */
    public function get_pokemons_data($schema) {
        $url = 'https://graphql-pokemon.now.sh/';
        $ret = wp_remote_post( $url, array(
            'timeout'     => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'body'        => array('query' => $schema),
            'cookies'     => array()
        ) );
        $ret = json_decode($ret['body']);
        $pokemon_data_arr = $ret->data->pokemons;
        return $pokemon_data_arr;
    }
    /*
     * Get data from https://graphql-pokemon.now.sh/ graphQL server by pokemons name
     * */
    public static function get_pokemon_data_by_name ($name) {
        $url = 'https://graphql-pokemon.now.sh/';
        $ret = wp_remote_post( $url, array(
            'timeout'     => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'body'        => array('query' =>
                '{
      pokemon(name:"' . $name . '") {
        image
        maxHP
        maxCP
        fleeRate
        name
        evolutions {
            image
            maxHP
            maxCP
            fleeRate
            name
        }
      }
    }'
            ) ,
            'cookies'     => array()
        ) );
        $ret = json_decode($ret['body']);
        $single_pokemon_data = $ret->data->pokemon;
        return $single_pokemon_data;
    }
    /*
     * Get 1st stage evolution pokemons arrays from all pokemons data
     * */
     static function filtered_pokemons() {
        $schema = '{
              pokemons(first:200) {
                image
                maxHP
                maxCP
                fleeRate
                name
                evolutions {
                   image
                   maxHP
                   maxCP
                   fleeRate
                   name
                }
              }
            }';
        $pokemons = self::get_pokemons_data($schema);
        $filtered_poks = array();
        $evo_arr = self::evolutions_array();
        foreach ($pokemons as $key => $pokemon) {
            if ($pokemon->evolutions && !in_array($pokemon->name, $evo_arr)) {
                array_push($filtered_poks,$pokemon);
            }
        }
        return $filtered_poks;
    }
    /*
     * This method creates a 2nd and more pokemons evolution stage names array
     * */
    public function evolutions_array() {
        $schema = '{
              pokemons(first:200) {
                evolutions {
                   name
                }
              }
            }';
        $pokemons = self::get_pokemons_data($schema);
        $evo_arr = array();
        foreach ($pokemons as $pokemon) {
            if ($pokemon->evolutions !== null) {
                $evo = $pokemon->evolutions;
                foreach ($evo as $e) {
                    array_push($evo_arr,$e->name);
                }
            }
        }
        $true_evo_arr = array_unique($evo_arr);
        return $true_evo_arr;
    }

    function pokemons_arch_shortcode_handler() {
        $filtered_poks = array_slice(self::filtered_pokemons(), 0, 15);
        ob_start();
        view_pokemon::poks_archive_output( $filtered_poks );
        $out = ob_get_clean();
        return $out;
    }

    function poks_load() {
        view_pokemon::poks_load_more();
    }
    function custom_poks_handler($atts) {
        extract(shortcode_atts(array(
            'names' => ''
        ), $atts));
        ob_start();
        view_pokemon::custom_poks_output($names);
        $out = ob_get_clean();
        return $out;
    }
}
/*
 * shortcode existence checking
 * */
if (model_pokemon::current_page_id('%[poks_arch_single]%') || model_pokemon::current_page_id('%[custom_poks%')) {
    new model_pokemon();
}

