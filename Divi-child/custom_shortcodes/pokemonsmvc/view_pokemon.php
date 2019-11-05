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
     * markup for single page slider (didn't used in the project)
     * */
    function single_poks_slider_markup($data, $image, $hp, $cp, $flee_rate, $name, $types, $weaknesses, $classification, $resistant) {
        $attacks_arr = model_pokemon::get_attacks_arr($data);
        ?>
        <div class="pokemon_cont">
            <div class="pokemon_cont_inner">
                <div class="pokemon_image">
                    <img src="<?php echo $image; ?>" alt="<?php echo $image; ?>">
                </div>
                <div class="attacks">
                    <h3><?php echo __('Attacks') ?></h3>
                    <div class="attacks_info">
                        <?php foreach ($attacks_arr as $attack_type => $attack_arr){ ?>
                            <div class="attack_type">
                                <h4><?php echo $attack_type ?></h4>
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
                    <div class="attacks_types">
                        <?php
                        echo '<span>' . __('Resistant : ') . '</span>';
                        foreach ($resistant as $res) echo $res . ' ';
                        ?>
                    </div>
                    <div class="attacks_weak">
                        <?php
                        echo '<span>' . __('Weaknesses : ') . '</span>';
                        foreach ($weaknesses as $weakness) echo $weakness . ' ';
                        ?>
                    </div>
                </div>
            </div>
            <div class="pokemon_description">
                <h2 class="pokemon_name"><?php echo $name; ?></h2>
                <ul class="pokemon_stats">
                    <li><?php echo __('Classification : ') . $classification; ?></li>
                    <li>|</li>
                    <li><?php echo __('HP : ') . $hp; ?></li>
                    <li>|</li>
                    <li><?php echo __('CP : ') . $cp; ?></li>
                    <li>|</li>
                    <li><?php echo __('Flee Rate : ') . $flee_rate; ?></li>
                    <li>|</li>
                    <li>
                        <?php
                        echo __('Types : ');
                        foreach ($types as $type) echo $type . ' ';
                        ?>
                    </li>
                </ul>
            </div>
        </div>
        <?php
    }
    /*
     * Slider markup for single page
     * */
    function single_page_slider_inner($img_class,$image) {
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
    function single_page_info_markup($hp , $cp, $flee_rate, $name, $types, $weaknesses, $classification, $resistant, $attacks) {
        ?>
        <h1><?php echo $name ?></h1>
        <div class="pok_detailed_info">
            <div class="pok_detailed_info_attacks">
                <h2><?php echo __('Attacks'); ?></h2>
                <div class="attacks_info">
                    <?php foreach ($attacks as $attack_type => $attack_arr){ ?>
                        <div class="attack_type">
                            <h3><?php echo $attack_type ?></h3>
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
                $data = model_pokemon::get_pokemon_data_by_name($name);
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
                    echo self::pok_description($evo_pok->maxHP, $evo_pok->maxCP, $evo_pok->fleeRate, $evo_pok->name);
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
    function preloader() {
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
     *
     * */
    static function archive_page_items_markup($filtered_poks, $arch_query_link, $filtered = null) {
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
        if (!$filtered) {
            ?>
            <div id="show_more" class="pokemon_cont show_more">
                <a><?php echo __('Show More'); ?></a>
            </div>
            <?php
        }
    }
    static function above_content($filtered_poks) {
        ?>
        <div class="above_content">
            <div class="poks_quantity">
                    <span class="counter"><?php echo count($filtered_poks) . '</span>' . '<span>' . __(' Pokemons Shown') . '</span>'; ?>
            </div>
            <div class="view_buttons">
                <button class="grid_btn" disabled>
                    <img src="http://oshawa-dev.mifist.in.ua/wp-content/uploads/2019/10/nine-black-tiles_icon-icons.com_73478.png">
                </button>
                <button class="map_btn">
                    <img src="http://oshawa-dev.mifist.in.ua/wp-content/uploads/2019/10/map.png">
                </button>
            </div>
        </div>
        <?php
    }
    /*
     * outputs data from the earlier filtered array to the screen (only first 15 from array)
     * */
    static function poks_archive_output( $filtered_poks ) {
        $arch_query_link = model_pokemon::get_archive_page_link();
        $path = parse_url($arch_query_link, PHP_URL_PATH); ?>
        <div class="pokemons">
            <div class="preloader">
                <?php self::preloader(); ?>
            </div>
            <?php if ($path == $_SERVER['REQUEST_URI']) {
                self::above_content($filtered_poks);
                ?>
                <div class="pokemons_arch_grid">
                <?php
               self::archive_page_items_markup($filtered_poks, $arch_query_link);
               ?></div><?php
            } ?>
        </div>
        <?php
    }
    /*
     *
     * */
    static function poks_archive_map_output($filtered_poks) {
        $arch_query_link = model_pokemon::get_archive_page_link();
        echo '<div class="pokemons_arch_grid_items">';
            self::archive_page_items_markup($filtered_poks, $arch_query_link);
        echo '</div>';
        echo '<div class="pokemons_arch_grid_map">';
            echo '<div id="map_arch"></div>';
            model_pokemon::arch_map_init();
        echo '</div>';
    }
    /*
     * outputs data from the earlier filtered array to the screen when "Show More" button si clicked (next 15 from array)
     * */
    static function poks_load_more() {
        $offset = $_POST['offset'];
        $poks_arr = array_slice($_POST['query'],15);
        $sliced_poks_arr = array_slice($poks_arr, $offset, 15);
        foreach ($sliced_poks_arr as $pok) {
            $evolutions = $pok['evolutions'];
            $link = model_pokemon::get_archive_page_link() . '?id=' . $pok['name'];
            echo '<div class="grid_item">';
                echo '<div class="slider_wrap">';
                    ?><a class="pok_link" href="<?php echo $link; ?>"><?php
                        echo self::pok_image($pok['image'], $pok['name']);
                    ?></a><?php
                    if ($evolutions) {
                        foreach ($evolutions as $evo_pok) {
                            $link = model_pokemon::get_archive_page_link() . '?id=' . $evo_pok['name'];
                            ?><a class="pok_link" href="<?php echo $link; ?>"><?php
                                echo self::pok_image($evo_pok['image'], $pok['name']);
                            ?></a><?php
                        }
                    }
                echo'</div>';
                echo '<div data-desc="' . $pok['name'] . '">';
                    echo self::pok_description($pok['maxHP'], $pok['maxCP'], $pok['fleeRate'], $pok['name']);
                echo '</div>';
            echo '</div>';
        }
        die();
    }
    /*
     * single page output
     * */
    static function single_page_markup() {
        $name = $_POST['name'];
        $data = model_pokemon::get_pokemon_data_by_name($name);
        $evolutions = $data->evolutions;
        echo '<div class="slider_contaier">';
            echo '<div class="single_page_slider">';
                    self::single_page_slider_inner('pokemon_image',$data->image);
                if ($evolutions) {
                        foreach ($evolutions as $evo_pok) {
                            self::single_page_slider_inner('pokemon_image',$evo_pok->image);
                    }
                }
            echo '</div>';
            echo '<div class="single_page_slider_nav">';
                self::single_page_slider_inner('pokemon_image_nav',$data->image);
                if ($evolutions) {
                    foreach ($evolutions as $evo_pok) {
                        self::single_page_slider_inner('pokemon_image_nav' ,$evo_pok->image);
                    }
                }
            echo '</div>';
        echo '</div>';
        echo  '<div class="pok_detailed">';
            self::single_page_info_markup($data->maxHP , $data->maxCP, $data->fleeRate, $data->name, $data->types, $data->weaknesses, $data->classification, $data->resistant, $data->attacks);
        echo '</div>';
        if ($evolutions) {
            echo '<div class="pok_evo">';
                echo '<h2>' . __('Next Evolution Stages') . '</h2>';
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
        echo '<div class="map" id="map"></div>';
        model_pokemon::single_map_init();
        ?><a class ="print-doc" href="javascript:(print());"><?php echo __('Get/Print PDF'); ?></a><?php
        die();
    }
    /*
     * filtering forms markup
     * */
    static function filter_markup() {
        $types = model_pokemon::get_query_arr('types');
        $min_hp = min(model_pokemon::get_query_arr('maxHP'));
        $max_hp = max(model_pokemon::get_query_arr('maxHP'));
        $min_cp = min(model_pokemon::get_query_arr('maxCP'));
        $max_cp = max(model_pokemon::get_query_arr('maxCP'));
        ?>
        <div class="poks_filter">
            <fieldset>
                <label for="types"><?php echo __('Type:'); ?></label>
                <select name="types" id="types">
                    <?php foreach ($types as $key => $type) {
                        if ($key == 0) {
                        ?>
                            <option><?php echo __('All'); ?></option>
                        <?php } ?>
                        <option><?php echo $type; ?></option>
                    <?php } ?>
                </select>
            </fieldset>
            <div class="jqui_slider">
                <label class="jqui_slider_label" for="hp_range"><?php echo __('HP:'); ?></label>
                <input class="jqui_slider_values" id="hp_range" type="text" readonly>
                <div id="hp_slider"></div>
                <input id="hp_val_min" type="hidden" value="<?php echo $min_hp; ?>">
                <input id="hp_val_max" type="hidden" value="<?php echo $max_hp; ?>">
            </div>
            <div class="jqui_slider">
                <label class="jqui_slider_label" for="cp_range"><?php echo __('CP:'); ?></label>
                <input class="jqui_slider_values" id="cp_range" type="text" readonly>
                <div id="cp_slider"></div>
                <input id="cp_val_min" type="hidden" value="<?php echo $min_cp; ?>">
                <input id="cp_val_max" type="hidden" value="<?php echo $max_cp; ?>">
            </div>
            <input type="submit" class="filter_btn" value="<?php echo __('Show'); ?>">
        </div>
        <?php
    }
}