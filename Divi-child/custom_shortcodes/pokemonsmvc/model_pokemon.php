<?php
namespace pokemons\mvc;
class model_pokemon {
    function __construct() {
        /*Adding [poks_arch_single] shortcode*/
        add_shortcode('poks_arch_single', array( &$this, 'pokemons_arch_shortcode_handler' ));
        /*Adding [custom_poks] shortcode*/
        add_shortcode('custom_poks', array( &$this, 'custom_poks_handler' ));
        /*Adding [poks_filter] shortcode*/
        add_shortcode('poks_filter', array(&$this, 'filtering_shortcode_handler'));
        /*Scripts and Styles enques hook*/
        add_action('wp_enqueue_scripts', array(&$this, 'enqueues'));
        /*Script localize hook*/
        add_action('wp_enqueue_scripts', array(&$this, 'localize'));
        /*
         * AJAX handlers hooks*/
            /*load more button ajax handler hooks*/
        add_action('wp_ajax_load_more', array(&$this, 'poks_load'));
        add_action('wp_ajax_nopriv_load_more', array(&$this, 'poks_load'));
            /*"permalinks" ajax handler hooks*/
        add_action('wp_ajax_to_single', array(&$this, 'single_page_output'));
        add_action('wp_ajax_nopriv_to_single', array(&$this, 'single_page_output'));
            /*map view button on archive page ajax handler hooks*/
        add_action('wp_ajax_to_map', array(&$this, 'map_view_output'));
        add_action('wp_ajax_nopriv_to_map', array(&$this, 'map_view_output'));
            /*grid view button on archive page ajax handler hooks*/
        add_action('wp_ajax_to_grid', array(&$this, 'grid_view_output'));
        add_action('wp_ajax_nopriv_to_grid', array(&$this, 'grid_view_output'));
            /*"filter" button on grid view ajax handler hooks*/
        add_action('wp_ajax_show_filtered', array(&$this, 'show_filtered'));
        add_action('wp_ajax_nopriv_show_filtered', array(&$this, 'show_filtered'));
    }
    /*
     * Scripts and styles enqueue method
     * */
    function enqueues() {
        wp_enqueue_style( 'pokemon_page_styles', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemonsmvc/assets/css/pokemon_page.css');
        wp_enqueue_script('minify_js', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemonsmvc/assets/js/minify.js', array('jquery'));
        wp_enqueue_script('main_js', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemonsmvc/assets/js/main.js', array('jquery', 'minify_js'));
    }
    /*
     * localize script method
     * */
    function localize() {
        $coors_data = self::get_data_from_file('coors.json');
        $arch_link = self::get_archive_page_link();
        wp_localize_script('main_js', 'ajaxarr',
            array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'offset' => 15,
                'coors' => $coors_data,
                'arch_link' => $arch_link
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
    public static function get_pokemons_data($schema) {
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
     * Get 1st stage evolution pokemons arrays from all pokemons data (arg must be a graphQL schema and contain
     * "name" in main and "evolutions" objects)
     * */
     static function filtered_pokemons($schema = null) {
        $schema_default = '{
              pokemons(first:200) {
                image
                maxHP
                maxCP
                fleeRate
                name
                types
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
        foreach ($pokemons as $key => $pokemon) {
            if ($pokemon->evolutions && !in_array($pokemon->name, $true_evo_arr)) {
                array_push($filtered_poks,$pokemon);
            }
        }
        return $filtered_poks;
    }
    /*
     * getting unique pokemon's query array (query must be in the main schema object)
     * */
    static function get_query_arr( $poks_arr, $query ) {
        $query_arr = array();
        foreach ($poks_arr as $pok) {
            unset($pok->name);
            unset($pok->evolutions);
            if (is_array($pok->$query)) {
                foreach ($pok->$query as $type) {
                    array_push($query_arr, $type);
                }
            } else {
                array_push($query_arr, $pok->$query);
            }

        }
        $true_query_arr = array_unique($query_arr);
        return $true_query_arr;
    }
    /*
     * returns array with url queries
     * */
    static function get_url_queries () {
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $parts = parse_url($url);
        parse_str($parts['query'], $query);
        return $query;
    }
    /*
     * returns pokemons arr within url queries range
     * */
    static function get_poks_within_url_queries() {
        $query = self::get_url_queries();
        $true_poks = null;
        if ($query || $_POST !== array()) {
            $max_hp = $query ? $query['hp_val_max'] : $_POST['max_hp'];
            $min_hp = $query ? $query['hp_val_min'] : $_POST['min_hp'];
            $max_cp = $query ? $query['cp_val_max'] : $_POST['max_cp'];
            $min_cp = $query ? $query['cp_val_min'] : $_POST['min_cp'];
            $type = $query ? $query['types'] : $_POST['type'];
            $poks = self::filtered_pokemons();
            $true_poks = array();
            foreach ($poks as $pok) {
                $type_check = $type == 'All' ? true : in_array($type ,$pok->types);
                if ($pok->maxHP <= $max_hp && $pok->maxHP >= $min_hp && $pok->maxCP >= $min_cp && $pok->maxCP <= $max_cp && $type_check) {
                    array_push($true_poks, $pok);
                }
            }
        }
        return $true_poks;
    }
    /*
     * "poks_arch_single" shortcode handler
     * */
    function pokemons_arch_shortcode_handler() {
        $query = self::get_url_queries();
        $map_data = $_POST['map_data'];
        $true_poks = self::get_poks_within_url_queries() ? self::get_poks_within_url_queries() : array_slice(self::filtered_pokemons(), 0, 15);
        $show_more = count($true_poks) >= 15 ? true : null;
        $fc = fopen(__DIR__ . '/assets/filtered_data.json','w');
        fwrite($fc, json_encode($true_poks));
        fclose($fc);
        ?>
        <script>
            filtered_pokemons = '<? echo self::get_data_from_file('filtered_data.json');?>';
        </script>
        <?php
        if (!$query['id']) {
            if ($map_data) {
                ob_start();
                view_pokemon::poks_archive_map_output($true_poks, $show_more);
                $out = ob_get_clean();
                return $out;
            } else {
                ob_start();
                view_pokemon::poks_archive_output( $true_poks, $show_more );
                $out = ob_get_clean();
                return $out;
            }
        } else {
            ob_start();
            echo '<div class="pokemons"></div>';
            $out = ob_get_clean();
            return $out;
        }
    }
    /*
     * "poks_filter" shortcode handler
     * */
    function filtering_shortcode_handler() {
        ob_start();
        view_pokemon::filter_markup();
        $out = ob_get_clean();
        return $out;
    }
    /*
     * Generate array with random shufled numbers with custom range and step
     * */
    function random_numbers_arr_within_range($min, $max, $step) {
        $numbers = range($min, $max, $step);
        shuffle($numbers);
        return $numbers;
    }
    /*
     * Generate random coordinates within range and create/rewrite coors.json with that data in it
     * */
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
    /*
     * Add data from json file to array
     * */
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
    /*
     * for ajax, executes single page markup
     * */
    static function single_page_output() {
      view_pokemon::single_page_markup();
    }
    /*
     * map view button AJAX event handler
     * */
    function map_view_output() {
        $filtered_poks = array_slice(self::filtered_pokemons(), 0, 15);
        $show_more = count($filtered_poks) >= 15 ? true : null;
        view_pokemon::poks_archive_map_output($filtered_poks, $show_more);
        die();
    }
    /*
     * grid view button AJAX event handler
     * */
    function grid_view_output() {
        $filtered_poks = array_slice(self::filtered_pokemons(), 0, 15);
        $show_more = count($filtered_poks) >= 15 ? true : null;
        $link = self::get_archive_page_link();
        view_pokemon::archive_page_items_markup($filtered_poks, $link, $show_more);
        die();
    }
    /*
     * filter button AJAX event handler
     * */
    function show_filtered() {
        $link = self::get_archive_page_link();
        $max_hp = $_POST['max_hp'];
        $min_hp = $_POST['min_hp'];
        $max_cp = $_POST['max_cp'];
        $min_cp = $_POST['min_cp'];
        $type = $_POST['type'];
        $map_data = $_POST['map_data'];
        $poks = self::filtered_pokemons();
        $true_poks = array();
        foreach ($poks as $pok) {
            $type_check = $type == 'All' ? true : in_array($type ,$pok->types);
            if ($pok->maxHP <= $max_hp && $pok->maxHP >= $min_hp && $pok->maxCP >= $min_cp && $pok->maxCP <= $max_cp && $type_check) {
                array_push($true_poks, $pok);
            }
        }
        $fc = fopen(__DIR__ . '/assets/filtered_data.json','w');
        fwrite($fc, json_encode($true_poks));
        fclose($fc);
        ?>
        <script>
            filtered_pokemons = '<? echo self::get_data_from_file('filtered_data.json');?>';
        </script>
        <?php
        if ($map_data == 1) {
            view_pokemon::poks_archive_map_output($true_poks);
        } else {
            view_pokemon::archive_page_items_markup($true_poks, $link);
        }

        die();
    }
    /*
     * Initialize a map from google on map view archive page
     * */
    static function arch_map_init() {
        ?>
        <script type="text/javascript">


            function initMap() {
                let locations = JSON.parse(ajaxarr.coors);
                let true_filtered_poks = null;
                let searchParams = new URLSearchParams(window.location.search);
                if (document.getElementById('pokemons_arch_grid').dataset.filt == 1 || (document.getElementById('pokemons_arch_grid').dataset.filt == 0 && searchParams.get('hp_val_min'))) {
                    let filtered_poks = JSON.parse(filtered_pokemons);
                    true_filtered_poks = [];
                    for (let i = 0; i < locations.length; i++) {
                        for (let j = 0; j < filtered_poks.length; j++) {
                            if (locations[i].name === filtered_poks[j].name) {
                                true_filtered_poks.push(locations[i]);
                            }
                        }
                    }
                }
                let offset = Number(ajaxarr.offset);
                let first_locations = true_filtered_poks === null ? locations.splice(0, offset) : true_filtered_poks;
                let map = new google.maps.Map(document.getElementById('map_arch'), {
                    zoom: 5,
                    center: new google.maps.LatLng(48, 31),
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    disableDefaultUI: true,
                    zoomControl: true,
                    markers: []
                });

                let infowindow = new google.maps.InfoWindow();

                let marker, i;

                for (i = 0; i < first_locations.length; i++) {
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(first_locations[i].lat, first_locations[i].lng),
                        map: map,
                        icon: 'http://oshawa-dev.mifist.in.ua/wp-content/uploads/2019/10/poks_marker.png',
                        title: first_locations[i].name
                    });
                    map.markers.push(marker);

                    google.maps.event.addListener(marker, 'click', (function (marker, i) {
                        return function () {
                            let image = document.querySelectorAll('[data-name="' + first_locations[i].name + '"]')[0].innerHTML;
                            let desc = document.querySelectorAll('[data-desc="' + first_locations[i].name + '"]')[0].innerHTML;
                            infowindow.setContent(image + desc);
                            infowindow.open(map, marker);
                        }
                    })(marker, i));
                    google.maps.event.addListener(map, 'click', function() {
                            infowindow.close();
                    });
                    google.maps.event.addListener(marker, 'mouseover', (function () {
                        this.setIcon('http://oshawa-dev.mifist.in.ua/wp-content/uploads/2019/10/pikachu_icon.png');
                        this.setZIndex(google.maps.Marker.MAX_ZINDEX);
                    }));
                    google.maps.event.addListener(marker, 'mouseout', (function () {
                        this.setIcon('http://oshawa-dev.mifist.in.ua/wp-content/uploads/2019/10/poks_marker.png');
                    }));
                }
                (function($){
                    $(document).ready(function() {
                        $('.pokemon_cont').on('hover', function () {
                            let name = $(this).attr('data-name');
                            $.each(map.markers, function () {
                                this.setIcon('http://oshawa-dev.mifist.in.ua/wp-content/uploads/2019/10/poks_marker.png');
                                if(this['title'] == name) {
                                    this.setIcon('http://oshawa-dev.mifist.in.ua/wp-content/uploads/2019/10/pikachu_icon.png');
                                    this.setZIndex(google.maps.Marker.MAX_ZINDEX + 1);
                                }
                            })
                        });
                    });
                })(jQuery);
            }
        </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDaawMpqt4K0p0D2IFqSWOQmphuNblK0aM&callback=initMap"></script>
        <?php
    }
    /*
     * Map init method on single pages
     * */
    static function single_map_init () {
        ?>
        <script>
            // Initialize and add the map
            function initMap() {
                const data_arr = JSON.parse(ajaxarr.coors);
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
                        disableDefaultUI: true,
                        zoomControl: true,
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
if (model_pokemon::current_page_id('%[poks_arch_single]%') || model_pokemon::current_page_id('%[custom_poks%') || model_pokemon::current_page_id('%[poks_filter]%')) {
    new model_pokemon();
}