<?php
namespace Pokemons;
class CustomPokemons {

    function __construct() {

        add_shortcode('pokemons', array( &$this, 'custom_poks_output' ));

        add_action('wp_enqueue_scripts', array(&$this, 'enqueues'));
    }

    public static function current_page_id($text) {
        global $wpdb;
        $current_post_id = $wpdb->get_var('SELECT ID FROM '.$wpdb->prefix.'posts WHERE post_content LIKE "'. $text .'" AND post_parent = 0');
        return $current_post_id;
    }

    function enqueues() {
            wp_enqueue_style( 'pokemon_page_styles', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemon_page/css/pokemon_page.css');
            wp_enqueue_style( 'slick_css', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemon_page/slick/slick.css');
            wp_enqueue_style( 'slick_theme_css', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemon_page/slick/slick-theme.css');

            wp_register_script('slick_min_js', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemon_page/slick/slick.min.js', array('jquery'));
            wp_enqueue_script('main_js', get_stylesheet_directory_uri() . '/custom_shortcodes/pokemon_page/js/main.js', array('jquery', 'slick_min_js'));
    }
    /*
     * Get data from https://graphql-pokemon.now.sh/ graphQL server by pokemons name
     * */
    public function get_pokemon_data_by_name ($name) {
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
     * outputs markup wrapper
     * */
    public static function poks_markup($image, $hp, $cp, $flee_rate, $name) {
       $markup = sprintf(
           '<div class="pokemon_cont">
            <div class="pokemon_image">
                <img src="%1$s" alt="%1$s">
            </div>
            <div class="pokemon_description">
                <h2 class="pokemon_name">%5$s</h2>
                <ul class="pokemon_stats">
                    <li>HP : %2$s</li>
                    <li>|</li>
                    <li>CP : %3$s</li>
                    <li>|</li>
                    <li>Flee Rate : %4$s</li>
                </ul>
            </div>
        </div>',
           $image,
           $hp,
           $cp,
           $flee_rate,
           $name
       );
       return $markup;
    }
    /*
     * Starts an output buffer
     * */
    function custom_poks_output($atts) {
        extract(shortcode_atts(array(
            'names' => ''
        ), $atts));
        $names_arr = explode(' ', $names);
        ob_start();
        if ($names !== '') {
            echo '<div class="pokemons_arch_grid">';
            foreach ($names_arr as $name) {
                $data = $this->get_pokemon_data_by_name($name);
                $evolutions = $data->evolutions;
                echo '<div class="grid_item">';
                    echo '<div class="slider_wrap">';
                        echo $this->poks_markup($data->image, $data->maxHP, $data->maxCP, $data->fleeRate, $data->name);
                        if ($evolutions) {
                            foreach ($evolutions as $evo_pok) {
                                echo $this->poks_markup($evo_pok->image, $evo_pok->maxHP, $evo_pok->maxCP, $evo_pok->fleeRate, $evo_pok->name);
                            }
                        }
                    echo'</div>';
                echo '</div>';
            }
            ?>
            <div class="arch_link">
                <span><?php echo __('All Pokemons'); ?></span>
            </div>
            </div>
            <?php
        }
        $out = ob_get_clean();
        return $out;
    }
}
/*
 * shortcode existence check
 * */
if (CustomPokemons::current_page_id('%[pokemons%')) {
    new CustomPokemons();
}
