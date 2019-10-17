<?php
class PokemonArchive extends CustomPokemons{

    function __construct() {

        add_shortcode('pokemons_archive', array( &$this, 'poks_archive_output' ));
        add_action('wp_enqueue_scripts', array(&$this, 'enqueues'));
        add_action('wp_ajax_load_more', array(&$this, 'poks_load'));
        add_action('wp_ajax_nopriv_load_more', array(&$this, 'poks_load'));

    }

    function enqueues() {
        if (self::current_page_id('%[pokemons_archive%')) {
            $filtered_poks_rest = array_slice($this->filtered_pokemons(), 15);
            wp_enqueue_style( 'pokemon_page_styles', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemon_page/css/pokemon_page.css');
            wp_enqueue_style( 'slick_css', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemon_page/slick/slick.css');
            wp_enqueue_style( 'slick_theme_css', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemon_page/slick/slick-theme.css');

            wp_enqueue_script('equal_height', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemon_page/js/equal_height.js', array('jquery'));
            wp_enqueue_script('slick_min_js', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemon_page/slick/slick.min.js', array('jquery'));
            wp_enqueue_script('single_poke_slider', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemon_page/js/single_poke_slider.js', array('slick_min_js'));
            wp_enqueue_script('LoadMorePokemons', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemon_page/js/load_more.js', array('jquery'));
            wp_localize_script('LoadMorePokemons', 'fivemorepoksajax',
                array(

                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
                    'offset' => 0,
                    'length' => count($filtered_poks_rest),
                    'poks_arr' => $filtered_poks_rest,

                )
            );
        }
    }
    /*
     * Get data from https://graphql-pokemon.now.sh/ graphQL server
     * */
    function get_pokemons_data($schema) {
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
     * Get 1st stage evolution pokemons arrays from all pokemons data
     * */
    function filtered_pokemons() {
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
        $pokemons = $this->get_pokemons_data($schema);
        $filtered_poks = array();
        $evo_arr = $this->evolutions_array();
        foreach ($pokemons as $key => $pokemon) {
            if ($pokemon->evolutions !== null && !in_array($pokemon->name, $evo_arr)) {
                array_push($filtered_poks,$pokemon);
            }
        }
        return $filtered_poks;
    }
    /*
     * This method creates a 2nd and more pokemons evolution stage names array
     * */
    function evolutions_array() {
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
        $pokemons = $this->get_pokemons_data($schema);
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
     * outputs data from the earlier filtered array to the screen (only first 15 from array)
     * */
    function poks_archive_output() {
        $filtered_poks = array_slice($this->filtered_pokemons(), 0, 15);
        ob_start();
        echo '<div class="pokemons_arch_grid">';
        foreach ($filtered_poks as $pok) {
            $evolutions = $pok->evolutions;
            echo '<div class="grid_item">';
                echo '<div class="slider_wrap">';
                    echo $this->poks_markup($pok->image, $pok->maxHP, $pok->maxCP, $pok->fleeRate, $pok->name);
                    if ($evolutions !== null) {
                        foreach ($evolutions as $evo_pok) {
                            echo $this->poks_markup($evo_pok->image, $evo_pok->maxHP, $evo_pok->maxCP, $evo_pok->fleeRate, $evo_pok->name);
                        }
                    }
                echo'</div>';
            echo '</div>';
        }
        ?>
        <div id="show_more" class="pokemon_cont show_more">
            <span>Show More</span>
        </div>
        </div>
        <?php
        $out = ob_get_clean();
        return $out;
    }
    /*
     * outputs data from the earlier filtered array to the screen when "Show More" button si clicked (next 15 from array)
     * */
    function poks_load()
    {
        $offset = $_POST['offset'];
        $poks_arr = $_POST['query'];
        $sliced_poks_arr = array_slice($poks_arr, $offset, 15);
        foreach ($sliced_poks_arr as $pok) {
            $evolutions = $pok['evolutions'];
            echo '<div class="grid_item">';
                echo '<div class="slider_wrap">';
                    echo $this->poks_markup($pok['image'], $pok['maxHP'], $pok['maxCP'], $pok['fleeRate'], $pok['name']);
                    if ($evolutions !== null) {
                        foreach ($evolutions as $evo_pok) {
                            echo $this->poks_markup($evo_pok['image'], $evo_pok['maxHP'], $evo_pok['maxCP'], $evo_pok['fleeRate'], $evo_pok['name']);
                        }
                    }
                echo'</div>';
            echo '</div>';
        }
        die();
    }
}
new PokemonArchive();