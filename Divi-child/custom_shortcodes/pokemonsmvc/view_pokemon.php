<?php
namespace pokemons\mvc;
//use pokemons\mvc\model_pokemon;
class view_pokemon {
    /*
     * outputs markup wrapper
     * */
    public function poks_markup($image, $hp, $cp, $flee_rate, $name) {
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
    static function custom_poks_output($names) {
        $names_arr = explode(' ', $names);
        $arch_link = model_pokemon::get_archive_page_link();
        if ($names !== '') { ?>
            <div class="pokemons_arch_grid">
            <?php foreach ($names_arr as $name) {
                $data = model_pokemon::get_pokemon_data_by_name($name);
                $link = model_pokemon::get_archive_page_link(). '?id=' .($data->name);
                $evolutions = $data->evolutions;
                echo '<div class="grid_item">';
                    echo '<div class="slider_wrap">'; ?>
                        <a href="<?php echo $link; ?>">
                            <?php echo self::poks_markup($data->image, $data->maxHP, $data->maxCP, $data->fleeRate, $data->name); ?>
                        </a>
                            <?php
                            if ($evolutions) {
                                foreach ($evolutions as $evo_pok) {
                                    $link = model_pokemon::get_archive_page_link(). '?id=' .($evo_pok->name); ?>
                                    <a href="<?php echo $link; ?>">
                                    <?php echo self::poks_markup($evo_pok->image, $evo_pok->maxHP, $evo_pok->maxCP, $evo_pok->fleeRate, $evo_pok->name); ?>
                                    </a>
                                <?php }
                            }
                    echo'</div>';
                echo '</div>';
            }?>
                <div class="arch_link">
                    <a href="<?php echo $arch_link; ?>"><?php echo __('All Pokemons'); ?></a>
                </div>
            </div>
            <?php
        }
    }
    /*
     * outputs data from the earlier filtered array to the screen (only first 15 from array)
     * */
    static function poks_archive_output( $filtered_poks ) {
        ?>
        <div class="pokemons_arch_grid">
        <?php foreach ($filtered_poks as $pok) {
            $evolutions = $pok->evolutions;
            $link = model_pokemon::get_archive_page_link(). '?id=' .($pok->name);
            echo '<div class="grid_item">';
                echo '<div class="slider_wrap">'; ?>
                    <a class="pok_link" href="<?php echo $link; ?>">
                        <?php echo self::poks_markup($pok->image, $pok->maxHP, $pok->maxCP, $pok->fleeRate, $pok->name); ?>
                    </a>
                    <?php
                    if ($evolutions) {
                        foreach ($evolutions as $evo_pok) {
                            $link = model_pokemon::get_archive_page_link(). '?id=' .($evo_pok->name); ?>
                            <a class="pok_link" href="<?php echo $link; ?>">
                                <?php echo self::poks_markup($evo_pok->image, $evo_pok->maxHP, $evo_pok->maxCP, $evo_pok->fleeRate, $evo_pok->name); ?>
                            </a>
                        <?php }
                    }
                echo'</div>';
            echo '</div>';
            }
            ?>
            <div id="show_more" class="pokemon_cont show_more">
                <a><?php echo __('Show More'); ?></a>
            </div>
        </div>
        <?php

    }
    /*
     * outputs data from the earlier filtered array to the screen when "Show More" button si clicked (next 15 from array)
     * */
    static function poks_load_more() {
        $offset = $_POST['offset'];
        $poks_arr = $_POST['query'];
        $sliced_poks_arr = array_slice($poks_arr, $offset, 15);
        foreach ($sliced_poks_arr as $pok) {
            $evolutions = $pok['evolutions'];
            echo '<div class="grid_item">';
                echo '<div class="slider_wrap">';
                    echo self::poks_markup($pok['image'], $pok['maxHP'], $pok['maxCP'], $pok['fleeRate'], $pok['name']);
                    if ($evolutions) {
                        foreach ($evolutions as $evo_pok) {
                            echo self::poks_markup($evo_pok['image'], $evo_pok['maxHP'], $evo_pok['maxCP'], $evo_pok['fleeRate'], $evo_pok['name']);
                        }
                    }
                echo'</div>';
            echo '</div>';
        }
        die();
    }
}