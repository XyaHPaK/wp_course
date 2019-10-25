<?php
namespace pokemons\mvc;
class model_pokemon {
    function __construct() {

        add_shortcode('poks_arch_single', array( &$this, 'pokemons_arch_shortcode_handler' ));
        add_shortcode('custom_poks', array( &$this, 'custom_poks_handler' ));
        add_action('wp_enqueue_scripts', array(&$this, 'enqueues'));
        add_action('wp_ajax_load_more', array(&$this, 'poks_load'));
        add_action('wp_ajax_nopriv_load_more', array(&$this, 'poks_load'));
        add_action('wp_ajax_to_single', array(&$this, 'single_page_output'));
        add_action('wp_ajax_nopriv_to_single', array(&$this, 'single_page_output'));

    }
    function enqueues() {
        $filtered_poks_rest = array_slice(self::filtered_pokemons(), 15);
        $json_data = self::get_data_from_file('coors.json');
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
                'coors' => $json_data

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
        types
        weaknesses
        classification
        resistant
        attacks {
          special {
            name
            type
            damage
          }
          fast {
            name
            type
            damage
          }
        }
        evolutions {
            image
            maxHP
            maxCP
            fleeRate
            name
            types
            weaknesses
            classification
            resistant
            attacks {
              special {
                name
                type
                damage
              }
              fast {
                name
                type
                damage
              }
            }
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
     * Pokemons attacks info array
     * */
    static function get_attacks_arr ($data) {
        if ($data->attacks) {
            $attacks_arr = array();
            foreach ($data->attacks as $name => $attack) {
                $attacks_arr[$name] = $attack;
            }
        }

        return $attacks_arr;
    }
    /*
     * Get 1st stage evolution pokemons arrays from all pokemons data
     * */
     static function filtered_pokemons($schema = null) {
        $schema_default = '{
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
        $true_schema = $schema ? $schema : $schema_default;
        $pokemons = self::get_pokemons_data($true_schema);
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
    /*
     * "poks_arch_single" shortcode handler
     * */
    function pokemons_arch_shortcode_handler() {

        $filtered_poks = array_slice(self::filtered_pokemons(), 0, 15);
        ob_start();
        view_pokemon::poks_archive_output( $filtered_poks );
        $out = ob_get_clean();
        return $out;
    }

    function random_numbers_arr_within_range($min, $max, $step) {
        $numbers = range($min, $max, $step);
        shuffle($numbers);
        return $numbers;
    }

    function generate_coors_json_data() {
        $schema = '{
            pokemons(first: 200) {
                name
                evolutions {
                     name
                }
          }
        }';
        $lat_arr = self::random_numbers_arr_within_range(44.36, 51.30,0.01);
        $lng_arr = self::random_numbers_arr_within_range(22.18,37.48,0.01);
        $data = self::filtered_pokemons($schema);
        foreach ($data as $key => $pok) {
            $pok->lat = $lat_arr[$key];
            $pok->lng = $lng_arr[$key];
        }
        $fc = fopen(__DIR__ . '/assets/coors.json','w');
        fwrite($fc, json_encode($data));
        fclose($fc);
    }

    function get_data_from_file($file_name) {
        $data_arr = file(__DIR__ . '/assets/' . $file_name);
        $data_arr = $data_arr[0];
        return $data_arr;
    }
    /*
     * ajax load more
     * */
    function poks_load() {
        view_pokemon::poks_load_more();
    }
    /*
     * "custom_poks" shortcode handler
     * */
    function custom_poks_handler($atts) {
        extract(shortcode_atts(array(
            'names' => ''
        ), $atts));
        ob_start();
        view_pokemon::custom_poks_output($names);
        $out = ob_get_clean();
        return $out;
    }
    /*
     * returns a link to archive page
     * */
    static function get_archive_page_link() {
        $arch_page_id = self::current_page_id('%[poks_arch_single]%');
        $arch_page_link = get_page_link($arch_page_id);
        return $arch_page_link;
    }
    static function single_page_output () {
      view_pokemon::single_page_markup();
    }
    /*
     * Initialize a map from google
     * */
    static function map_init () {
        ?>
        <script>
            // Initialize and add the map
            function initMap() {
                const data_arr = JSON.parse(fivemorepoksajax.coors);
                let searchParams = new URLSearchParams(window.location.search);
                let name = searchParams.get('id');
                let true_coors = {};
                for (let i = 0; i < data_arr.length; i++) {
                    for (let j=0; j<data_arr[i].evolutions.length; j++) {
                        if (data_arr[i].name == name || data_arr[i].evolutions[j].name == name) {
                            let lat = Number(data_arr[i].lat.toFixed(2));
                            let lng = Number(data_arr[i].lng.toFixed(2));
                            true_coors = {lat, lng};
                        }
                    }
                }


                let coors = true_coors;


                // The map, centered at cors
                let map = new google.maps.Map(
                    document.getElementById('map'), {
                        zoom: 7,
                        center: coors,
                        styles: [
                            {
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#ebe3cd"
                                    }
                                ]
                            },
                            {
                                "elementType": "labels.text.fill",
                                "stylers": [
                                    {
                                        "color": "#523735"
                                    }
                                ]
                            },
                            {
                                "elementType": "labels.text.stroke",
                                "stylers": [
                                    {
                                        "color": "#f5f1e6"
                                    }
                                ]
                            },
                            {
                                "featureType": "administrative",
                                "elementType": "geometry.stroke",
                                "stylers": [
                                    {
                                        "color": "#c9b2a6"
                                    }
                                ]
                            },
                            {
                                "featureType": "administrative.land_parcel",
                                "elementType": "geometry.stroke",
                                "stylers": [
                                    {
                                        "color": "#dcd2be"
                                    }
                                ]
                            },
                            {
                                "featureType": "administrative.land_parcel",
                                "elementType": "labels.text.fill",
                                "stylers": [
                                    {
                                        "color": "#ae9e90"
                                    }
                                ]
                            },
                            {
                                "featureType": "landscape.natural",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#dfd2ae"
                                    }
                                ]
                            },
                            {
                                "featureType": "poi",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#dfd2ae"
                                    }
                                ]
                            },
                            {
                                "featureType": "poi",
                                "elementType": "labels.text.fill",
                                "stylers": [
                                    {
                                        "color": "#93817c"
                                    }
                                ]
                            },
                            {
                                "featureType": "poi.park",
                                "elementType": "geometry.fill",
                                "stylers": [
                                    {
                                        "color": "#a5b076"
                                    }
                                ]
                            },
                            {
                                "featureType": "poi.park",
                                "elementType": "labels.text.fill",
                                "stylers": [
                                    {
                                        "color": "#447530"
                                    }
                                ]
                            },
                            {
                                "featureType": "road",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#f5f1e6"
                                    }
                                ]
                            },
                            {
                                "featureType": "road.arterial",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#fdfcf8"
                                    }
                                ]
                            },
                            {
                                "featureType": "road.highway",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#f8c967"
                                    }
                                ]
                            },
                            {
                                "featureType": "road.highway",
                                "elementType": "geometry.stroke",
                                "stylers": [
                                    {
                                        "color": "#e9bc62"
                                    }
                                ]
                            },
                            {
                                "featureType": "road.highway.controlled_access",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#e98d58"
                                    }
                                ]
                            },
                            {
                                "featureType": "road.highway.controlled_access",
                                "elementType": "geometry.stroke",
                                "stylers": [
                                    {
                                        "color": "#db8555"
                                    }
                                ]
                            },
                            {
                                "featureType": "road.local",
                                "elementType": "labels.text.fill",
                                "stylers": [
                                    {
                                        "color": "#806b63"
                                    }
                                ]
                            },
                            {
                                "featureType": "transit.line",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#dfd2ae"
                                    }
                                ]
                            },
                            {
                                "featureType": "transit.line",
                                "elementType": "labels.text.fill",
                                "stylers": [
                                    {
                                        "color": "#8f7d77"
                                    }
                                ]
                            },
                            {
                                "featureType": "transit.line",
                                "elementType": "labels.text.stroke",
                                "stylers": [
                                    {
                                        "color": "#ebe3cd"
                                    }
                                ]
                            },
                            {
                                "featureType": "transit.station",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#dfd2ae"
                                    }
                                ]
                            },
                            {
                                "featureType": "water",
                                "elementType": "geometry.fill",
                                "stylers": [
                                    {
                                        "color": "#b9d3c2"
                                    }
                                ]
                            },
                            {
                                "featureType": "water",
                                "elementType": "labels.text.fill",
                                "stylers": [
                                    {
                                        "color": "#92998d"
                                    }
                                ]
                            }
                        ]
                    });
                // The marker, positioned at cors
                let marker = new google.maps.Marker({
                    position: coors,
                    map: map,
                    title: name,
                    animation: google.maps.Animation.BOUNCE,
                    icon: 'http://oshawa-dev.mifist.in.ua/wp-content/uploads/2019/10/poks_marker.png'
                });
            }
        </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDaawMpqt4K0p0D2IFqSWOQmphuNblK0aM&callback=initMap"></script>
        <?php
    }
}
/*
 * shortcode existence checking
 * */
if (model_pokemon::current_page_id('%[poks_arch_single]%') || model_pokemon::current_page_id('%[custom_poks%')) {
    new model_pokemon();
}