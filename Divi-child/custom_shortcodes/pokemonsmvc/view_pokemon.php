<?php
namespace pokemons\mvc;
class view_pokemon {
    /*
     * outputs markup wrapper
     * */
    public static function pok_image($image, $name) {
        $markup = sprintf(
            '<div class="pokemon_cont" data-name="%2$s">
            <div class="pokemon_image">
                <img src="%1$s" alt="%1$s">
            </div>
        </div>',
            $image,
            $name
        );
        return $markup;
    }
    static function pok_description($hp, $cp, $flee_rate, $name) {
        $markup = sprintf(
                '<div class="pokemon_description">
                <h2 class="pokemon_name">%4$s</h2>
                <ul class="pokemon_stats">
                    <li>HP : %1$s</li>
                    <li>|</li>
                    <li>CP : %2$s</li>
                    <li>|</li>
                    <li>Flee Rate : %3$s</li>
                </ul>
            </div>',
            $hp,
            $cp,
            $flee_rate,
            $name
        );
        return $markup;
    }
    /*
     * Slider markup for single page
     * */
    static function single_page_slider_inner($img_class, $image) {
        ?>
        <div class="pokemon_cont">
                <div class="<?php echo $img_class ?>">
                    <img src="<?php echo $image; ?>" alt="<?php echo $image; ?>">
                </div>
        </div>
        <?php
    }
    /*
     * Single page markup
     * */
    static function single_page_info_markup($hp , $cp, $flee_rate, $types, $weaknesses, $classification, $resistant, $attacks) {
        ?>
        <div class="pok_detailed_info">
            <div class="pok_detailed_info_attacks">
                <h2><?php echo __('Attacks'); ?></h2>
                <div class="attacks_info">
                    <?php foreach ($attacks as $attack_type => $attack_arr){ ?>
                        <div class="attack_type">
                            <h3><?php echo $attack_type . __(':') ?></h3>
                            <?php foreach ($attack_arr as $attack){ ?>
                                <ul class="attack_type_list">
                                    <li><?php echo $attack->name ?></li>
                                    <li><?php echo __('Type : ') .  $attack->type ?></li>
                                    <li><?php echo __('Damage : ') .  $attack->damage ?></li>
                                </ul>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="pok_detailed_info_attacks_other">
               <h2><?php echo __('Atributes & Statistics'); ?></h2>
                <div class="pok_detailed_info_attacks_other_list">
                    <ul>
                        <li>
                            <?php
                            echo '<span>' . __('Resistant : ') . '</span>';
                            foreach ($resistant as $res) echo $res . ' ';
                            ?>
                        </li>
                        <li>
                            <?php
                            echo '<span>' . __('Weaknesses : ') . '</span>';
                            foreach ($weaknesses as $weakness) echo $weakness . ' ';
                            ?>
                        </li>
                        <li><?php echo '<span>' . __('Classification : ') . '</span>' . $classification; ?></li>
                        <li><?php echo '<span>' . __('HP : ') . '</span>' . $hp; ?></li>
                        <li><?php echo '<span>' . __('CP : '). '</span>' . $cp; ?></li>
                        <li><?php echo '<span>' . __('Flee Rate : '). '</span>' . $flee_rate; ?></li>
                        <li>
                            <?php
                            echo '<span>' . __('Types : ') . '</span>';
                            foreach ($types as $type) echo $type . ' ';
                            ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }
    /*
     * Starts an output buffer
     * */
    static function custom_poks_output($names) {
        $names_arr = explode(' ', $names);
        $arch_link = model_pokemon::get_archive_page_link();
        if ($names !== '') { ?>
            <div class="custom_poks">
            <?php foreach ($names_arr as $name) {
                $schema = model_pokemon::pokemon_schema_by_name($name);
                $data = model_pokemon::get_pokemons_data($schema, true);
                $link = model_pokemon::get_archive_page_link(). '?id=' .($data->name);
                $evolutions = $data->evolutions;
                echo '<div class="grid_item">';
                    echo '<div class="slider_wrap">'; ?>
                        <a href="<?php echo $link; ?>">
                            <?php echo self::pok_image($data->image, $data->name); ?>
                        </a>
                            <?php
                            if ($evolutions) {
                                foreach ($evolutions as $evo_pok) {
                                    $link = model_pokemon::get_archive_page_link(). '?id=' .($evo_pok->name); ?>
                                    <a href="<?php echo $link; ?>">
                                        <?php echo self::pok_image($evo_pok->image, $evo_pok->name); ?>
                                    </a>
                                <?php }
                            }
                    echo'</div>';
                    echo self::pok_description($data->maxHP, $data->maxCP, $data->fleeRate, $data->name);
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
     * preloader html markup
     * */
    static function preloader() {
        ?>
        <div id="fountainG">
            <div id="fountainG_1" class="fountainG"></div>
            <div id="fountainG_2" class="fountainG"></div>
            <div id="fountainG_3" class="fountainG"></div>
            <div id="fountainG_4" class="fountainG"></div>
            <div id="fountainG_5" class="fountainG"></div>
            <div id="fountainG_6" class="fountainG"></div>
            <div id="fountainG_7" class="fountainG"></div>
            <div id="fountainG_8" class="fountainG"></div>
        </div>
        <?php
    }
    /*
     *  Archive page items markup output
     * */
    static function archive_page_items_markup($filtered_poks, $arch_query_link, $show_more = null) {
        foreach ($filtered_poks as $pok) {
            $evolutions = $pok->evolutions;
            $link = $arch_query_link . '?id=' . ($pok->name);
            echo '<div class="grid_item">';
            echo '<div class="slider_wrap">'; ?>
            <a class="pok_link" href="<?php echo $link; ?>">
                <?php echo self::pok_image($pok->image, $pok->name); ?>
            </a>
            <?php
            if ($evolutions) {
                foreach ($evolutions as $evo_pok) {
                    $link = $arch_query_link . '?id=' . ($evo_pok->name); ?>
                    <a class="pok_link" href="<?php echo $link; ?>">
                        <?php echo self::pok_image($evo_pok->image, $pok->name); ?>
                    </a>
                <?php }
            }
            echo '</div>';
            echo '<div data-desc="' . $pok->name . '">';
            echo self::pok_description($pok->maxHP, $pok->maxCP, $pok->fleeRate, $pok->name);
            echo '</div>';
            echo '</div>';
        }
        if ($show_more) {
            ?>
            <div id="show_more" class="pokemon_cont show_more">
                <a><?php echo __('Show More'); ?></a>
            </div>
            <?php
        }
    }
    /*
     * Pokemons counter and view buttons markup output
     * */
    static function above_content($count) {
        ?>
        <div class="above_content">
            <div class="poks_quantity">
                    <span class="counter"><?php echo $count . '</span>' . '<span>' . __(' Pokemons Shown') . '</span>'; ?>
            </div>
            <div class="view_buttons">
                <button class="grid_btn" disabled>
                    <img src="/wp-content/themes/Divi-child/custom_shortcodes/pokemonsmvc/assets/images/nine-black-tiles.png">
                </button>
                <button class="map_btn">
                    <img src="/wp-content/themes/Divi-child/custom_shortcodes/pokemonsmvc/assets/images/map.png">
                </button>
            </div>
        </div>
        <?php
    }
    /*
     * outputs data from the earlier filtered array to the screen (only first 15 from array)
     * */
    static function poks_archive_output( $filtered_poks, $show_more = null ) {
        $arch_query_link = model_pokemon::get_archive_page_link();
        if (model_pokemon::get_filtering_data_from_cookies()) {
            $length = count(model_pokemon::get_poks_within_cookie_queries());
        } else {
            $length = count(model_pokemon::filtered_pokemons());
        }
        ?>
        <div class="pokemons" data-pok_length ="<?php echo $length; ?>">
            <div class="preloader">
                <?php self::preloader(); ?>
            </div>
                <?php self::above_content($length); ?>
                <div class="pokemons_arch_grid" id="pokemons_arch_grid" data-filt="0">
                    <?php self::archive_page_items_markup($filtered_poks, $arch_query_link);
                    if ($show_more) {
                    ?>
                        <div id="show_more" class="pokemon_cont show_more">
                            <a><?php echo __('Show More'); ?></a>
                        </div>
                    <?php } ?>
                </div>
        </div>
        <?php

    }
    /*
     * map view markup output
     * */
    static function poks_archive_map_output($filtered_poks, $show_more = null) {
        $arch_query_link = model_pokemon::get_archive_page_link();
        echo '<div class="pokemons_arch_grid_items">';
            self::archive_page_items_markup($filtered_poks, $arch_query_link, $show_more);
        echo '</div>';
        echo '<div id="pokemons_arch_grid_map" class="pokemons_arch_grid_map" data-map="0">';
            echo '<div id="map_arch"></div>';
        echo '</div>';
    }
    /*
     * single page output
     * */
    static function single_page_markup($data, $evolutions, $parent_pok) {

        echo '<div class="sp_header">';
            echo '<a href="' . model_pokemon::get_archive_page_link() . '">' . __('Return to selection') . '</a>';
            echo '<h1>' . $data->name . '</h1>';
        echo '</div>';
        echo '<div class="slider_contaier">';
        echo '<a class ="pdf" href="/"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
        echo '<a class ="print-doc" href="javascript:(print());"><i class="fa fa-print" aria-hidden="true"></i></a>';
        echo '<a class ="share" href="/"><i class="fa fa-share-alt" aria-hidden="true"></i></a>';
        echo do_shortcode('[ssba-buttons]');
            echo '<div class="single_page_slider">';
                self::single_page_slider_inner('pokemon_image', $data->image);
                if ($evolutions) {
                    foreach ($evolutions as $evo_pok) {
                        $link = model_pokemon::get_archive_page_link() . '?id=' . ($evo_pok->name);
                        echo '<a href="'. $link .'">';
                            self::single_page_slider_inner('pokemon_image', $evo_pok->image);
                        echo '</a>';
                    }
                }
            echo '</div>';
            echo '<div class="single_page_slider_nav">';
                self::single_page_slider_inner('pokemon_image_nav',$data->image);
                if ($evolutions) {
                    foreach ($evolutions as $evo_pok) {
                        $link = model_pokemon::get_archive_page_link() . '?id=' . ($evo_pok->name);
                        echo '<a href="'. $link .'">';
                            self::single_page_slider_inner('pokemon_image_nav' ,$evo_pok->image);
                        echo '</a>';
                    }
                }
            echo '</div>';
        echo '</div>';
        echo  '<div class="pok_detailed">';
            self::single_page_info_markup($data->maxHP , $data->maxCP, $data->fleeRate, $data->types, $data->weaknesses, $data->classification, $data->resistant, $data->attacks);
        echo '</div>';
        echo '<h2 class="map_ttl">' . __('Estimated Habitat') . '</h2>';
        echo '<div class="map" id="map"></div>';
        echo '<div class="next_prev_evo">';
            if ($parent_pok) {
                echo '<div class="pok_evo parent_pok">';
                echo '<h2>' . __('Previous Evolution Stage') . '</h2>';
                echo '<div class="pok_evo_wrap">';
                $link = model_pokemon::get_archive_page_link() . '?id=' . ($parent_pok->name);
                ?>
                <a class="pok_evo_item .pok_link" href="<?php echo $link; ?>">
                    <img src="<?php echo $parent_pok->image; ?>" alt="<?php echo $parent_pok->image; ?>">
                    <span><?php echo $parent_pok->name ?></span>
                </a>
                <?php
                echo '</div>';
                echo '</div>';
            }
            if ($evolutions) {
                $single_plural = count($evolutions) < 2 ? 'Stage' : 'Stages';
                echo '<div class="pok_evo">';
                    echo '<h2>' . __('Next Evolution '). $single_plural . '</h2>';
                    echo '<div class="pok_evo_wrap">';
                        foreach ($evolutions as $evo_pok) {
                            $link = model_pokemon::get_archive_page_link() . '?id=' . ($evo_pok->name);
                          ?>
                            <a class="pok_evo_item .pok_link" href="<?php echo $link; ?>">
                                <img src="<?php echo $evo_pok->image; ?>" alt="<?php echo $evo_pok->image; ?>">
                                <span><?php echo $evo_pok->name ?></span>
                            </a>
                            <?php
                        }
                    echo '</div>';
                echo '</div>';
            }
        echo '</div>';
        die();
    }
    /*
     * filtering forms markup
     * */
    static function filter_markup($action, $types, $true_type, $min_hp, $max_hp, $min_cp, $max_cp) {
        ?>
        <form class="poks_filter" action="<?php echo $action; ?>">
            <fieldset>
                <label for="types"><?php echo __('Type:'); ?></label>
                <select name="types" id="types">
                    <?php foreach ($types as $key => $type) {
                        if ($key == 0) {
                            ?>
                            <option><?php echo __('All'); ?></option>
                            <?php
                        }
                        if ($type == $true_type) {
                        ?>
                            <option selected><?php echo $type; ?></option>
                        <?php } else {
                        ?>
                            <option><?php echo $type; ?></option>
                    <?php }
                    } ?>
                </select>
            </fieldset>
            <div class="jqui_slider">
                <label class="jqui_slider_label" for="hp_range"><?php echo __('HP:'); ?></label>
                <input class="jqui_slider_values" id="hp_range" type="text" readonly>
                <div id="hp_slider"></div>
                    <input id="hp_val_min" type="hidden" value="<?php echo $min_hp; ?>" name="hp_val_min">
                    <input id="hp_val_max" type="hidden" value="<?php echo $max_hp; ?>" name="hp_val_max">
            </div>
            <div class="jqui_slider">
                <label class="jqui_slider_label" for="cp_range"><?php echo __('CP:'); ?></label>
                <input class="jqui_slider_values" id="cp_range" type="text" readonly>
                <div id="cp_slider"></div>
                    <input id="cp_val_min" type="hidden" value="<?php echo $min_cp; ?>" name="cp_val_min">
                    <input id="cp_val_max" type="hidden" value="<?php echo $max_cp; ?>" name="cp_val_max">
            </div>
            <input type="submit" class="filter_btn" value="<?php echo __('Show all'); ?>">
        </form>
        <?php
    }
}