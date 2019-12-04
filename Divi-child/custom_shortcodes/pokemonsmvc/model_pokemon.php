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
        $google_token = get_option('google_api_option')['access_token'];
        wp_enqueue_style( 'pokemon_page_styles', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemonsmvc/assets/css/pokemon_page.css');
        wp_enqueue_script('minify_js', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemonsmvc/assets/js/minify.js', array('jquery'));
        wp_enqueue_script('main_js', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemonsmvc/assets/js/main.js', array('jquery', 'minify_js'));
        ?>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_token; ?>"></script>
        <?php
    }
    /*
     * localize script method
     * */
    function localize() {
        wp_localize_script('main_js', 'ajaxarr',
            array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'offset' => 15
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
    public static function get_pokemons_data($schema, $single = null) {
        $url = 'https://graphql-pokemon.now.sh/';
        $ret = wp_remote_post( $url, array(
            'timeout'     => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'body'        => array('query' => $schema),
            'cookies'     => array()
        ));
        $ret = json_decode($ret['body']);
        $pokemon_data_arr = $single ? $ret->data->pokemon : $ret->data->pokemons;
        return $pokemon_data_arr;
    }
    /*
     * Pokemons attacks info array
     * */
    static function get_attacks_arr ($data) {
        $attacks_arr = array();
        if ($data->attacks) {
            foreach ($data->attacks as $name => $attack) {
                $attacks_arr[$name] = $attack;
            }
        }

        return $attacks_arr;
    }
    /*
     * Pokemon schema for single pokemon data request
     * */
    public static function pokemon_schema_by_name ($name) {

        $schema = '{
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
        }';
        return $schema;
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
             if ($pokemon->evolutions) {
                 $evo = $pokemon->evolutions;
                 foreach ($evo as $e) {
                     if($e->name !== "Jigglypuff") {
                         array_push($evo_arr,$e->name);
                     }
                 }
             }
         }
         $true_evo_arr = array_unique($evo_arr);
        foreach ($pokemons as $key => $pokemon) {
            if($pokemon->name == "Jigglypuff") {
                unset($pokemon->evolutions);
            }
            if (!in_array($pokemon->name, $true_evo_arr) && $pokemon->name !== "Farfetch'd") {
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
    static function get_filtering_data_from_cookies() {
        if (isset($_COOKIE['filtering_data'])) {
            list($max_hp, $min_hp, $max_cp, $min_cp, $type) = explode(',', $_COOKIE['filtering_data']);
            $filtering_data = array();
            $filtering_data['max_hp'] = $max_hp;
            $filtering_data['min_hp'] = $min_hp;
            $filtering_data['max_cp'] = $max_cp;
            $filtering_data['min_cp'] = $min_cp;
            $filtering_data['type'] = $type;
            return $filtering_data;
        } else {
            return null;
        }
    }
    /*
     * returns pokemons arr within url queries range
     * */
    static function get_poks_within_cookie_queries() {
        $query = self::get_filtering_data_from_cookies();
        $true_poks = null;
        if ($query || $_POST !== array()) {
            $max_hp = $query ? $query['max_hp'] : $_POST['max_hp'];
            $min_hp = $query ? $query['min_hp'] : $_POST['min_hp'];
            $max_cp = $query ? $query['max_cp'] : $_POST['max_cp'];
            $min_cp = $query ? $query['min_cp'] : $_POST['min_cp'];
            $type = $query ? $query['type'] : $_POST['type'];
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
        $true_poks_sliced = self::get_poks_within_cookie_queries() ? array_slice(self::get_poks_within_cookie_queries(), 0, 15) : array_slice(self::filtered_pokemons(), 0, 15);
        $true_poks = self::get_poks_within_cookie_queries() ? self::get_poks_within_cookie_queries() : self::filtered_pokemons();
        $show_more = count($true_poks) >= 15 ? true : null;
        if (!self::get_url_queries ()['id']) {
            $fc = fopen(__DIR__ . '/assets/filtered_data.json','w');
            fwrite($fc, json_encode($true_poks));
            fclose($fc);
        }

        ?>
        <script>
            filtered_pokemons = '<? echo self::get_data_from_file('filtered_data.json');?>';
        </script>
        <?php
        if (!$query['id']) {
            if ($map_data) {
                ob_start();
                view_pokemon::poks_archive_map_output($true_poks_sliced, $show_more);
                $out = ob_get_clean();
                return $out;
            } else {
                ob_start();
                view_pokemon::poks_archive_output( $true_poks_sliced, $show_more );
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
        $schema = '{
          pokemons(first: 200) {
            name
            types
            maxHP
            maxCP
            evolutions {
               name
            }
          }
        }';
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $url_parts = parse_url($url);
        $arch_url = model_pokemon::get_archive_page_link();
        $arch_url_parts = parse_url($arch_url);
        $action = $url_parts['path'] == $arch_url_parts['path'] ? '/' : $arch_url;
        $true_type = model_pokemon::get_filtering_data_from_cookies()['type'];
        $poks_arr = model_pokemon::filtered_pokemons($schema);
        $types = model_pokemon::get_query_arr($poks_arr,'types');
        $min_hp = min(model_pokemon::get_query_arr($poks_arr,'maxHP'));
        $max_hp = max(model_pokemon::get_query_arr($poks_arr,'maxHP'));
        $min_cp = min(model_pokemon::get_query_arr($poks_arr,'maxCP'));
        $max_cp = max(model_pokemon::get_query_arr($poks_arr,'maxCP'));
        ob_start();
        view_pokemon::filter_markup($action, $types, $true_type, $min_hp, $max_hp, $min_cp, $max_cp);
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
    static function get_data_from_file($file_name) {
        $data_arr = file(__DIR__ . '/assets/' . $file_name);
        $data_arr = $data_arr[0];
        return $data_arr;
    }
    /*
     * ajax load more
     * */
    function poks_load() {
        $offset = $_POST['offset'];
        $max_hp = $_POST['max_hp'];
        $min_hp = $_POST['min_hp'];
        $max_cp = $_POST['max_cp'];
        $min_cp = $_POST['min_cp'];
        $type = $_POST['type'];
        $range_poks = self::get_filtering_range_pokemons_data_arr($max_hp, $min_hp, $max_cp, $min_cp, $type);
        $poks_arr = $range_poks ? $range_poks : self::filtered_pokemons();
        $sliced_poks_arr = array_slice($poks_arr, $offset, 15);
        $arch_query_link = self::get_archive_page_link();
        view_pokemon::archive_page_items_markup($sliced_poks_arr, $arch_query_link);
        die();
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
        $name = $_POST['name'];
        $schema = model_pokemon::pokemon_schema_by_name($name);
        $data = model_pokemon::get_pokemons_data($schema, true);
        $parent_data = json_decode(model_pokemon::get_data_from_file('filtered_data.json'));
        $parent_pok = null;
        foreach ($parent_data as $pok) {
            if($pok->evolutions[0]->name && $pok->evolutions[0]->name == $name) {
                $parent_pok = $pok;
            }
            if ($pok->evolutions[1]->name && $pok->evolutions[1]->name == $name) {
                $parent_pok = $pok->evolutions[0];
            }
            if ($pok->evolutions[2]->name && $pok->evolutions[2]->name == $name) {
                $parent_pok = $pok->evolutions[1];
            }
        }
        $evolutions = $data->evolutions;
        view_pokemon::single_page_markup($data, $evolutions, $parent_pok);
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
     * Array of pokemons within filtering range
     * */
    static function get_filtering_range_pokemons_data_arr($max_hp, $min_hp, $max_cp, $min_cp, $type) {
        if(self::current_page_id('%[poks_filter]%')) {
            $poks = self::filtered_pokemons();
            $true_poks = array();
            foreach ($poks as $pok) {
                $type_check = $type == 'All' ? true : in_array($type ,$pok->types);
                if ($pok->maxHP <= $max_hp && $pok->maxHP >= $min_hp && $pok->maxCP >= $min_cp && $pok->maxCP <= $max_cp && $type_check) {
                    array_push($true_poks, $pok);
                }
            }
            return $true_poks;
        } else {
            return null;
        }

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
        $true_poks = self::get_filtering_range_pokemons_data_arr($max_hp, $min_hp, $max_cp, $min_cp, $type);
        $sliced_poks = array();
        $show_more = null;
        if (count($true_poks) > 15) {
            $show_more = true;
            $sliced_poks = array_slice($true_poks, 0, 15);
        }
        $true_poks_check = count($sliced_poks) > 0 ? $sliced_poks : $true_poks;
        $fc = fopen(__DIR__ . '/assets/filtered_data.json','w');
        fwrite($fc, json_encode($true_poks));
        fclose($fc);
        ?>
        <script>
            filtered_pokemons = '<? echo self::get_data_from_file('filtered_data.json');?>';
        </script>
        <?php
        if ($map_data == 1) {
            view_pokemon::poks_archive_map_output($true_poks_check, $show_more);
        } else {
            view_pokemon::archive_page_items_markup($true_poks_check, $link, $show_more);
        }

        die();
    }
}
/*
 * shortcode existence checking
 * */
if (model_pokemon::current_page_id('%[poks_arch_single]%') || model_pokemon::current_page_id('%[custom_poks%') || model_pokemon::current_page_id('%[poks_filter]%')) {
    new model_pokemon();
}