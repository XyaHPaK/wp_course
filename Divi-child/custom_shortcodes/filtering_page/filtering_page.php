<?php
/*styles/scripts enqueues*/

add_action( 'wp_enqueue_scripts', 'test_filter_enqueue_styles' );
function test_filter_enqueue_styles() {
    wp_enqueue_style( 'test_filtering-style', get_stylesheet_directory_uri() . '/custom_shortcodes/filtering_page/css/filtering_page.css');

    wp_enqueue_script('test_filtering-scripts', get_stylesheet_directory_uri() . '/custom_shortcodes/filtering_page/js/filtering_page.js', array('jquery'), NULL, true);

    $jp = array(
        'nonce' => wp_create_nonce( 'nonce' ),
        'ajaxURL' => admin_url( 'admin-ajax.php' )
    );
    wp_localize_script( 'test_filtering-scripts', 'test_ajax', $jp );
}

function get_cat_data() {

    $terms = get_terms( array(
        'taxonomy'      => array( 'test_tax' ),
        'orderby'       => 'id',
        'order'         => 'ASC',
        'fields'        => 'all',
        'hide_empty'    => true,
        'parent'      => 0,
    ) );
    ?>

    <ul class="test_categories">
        <?php
        $last_key = count($terms)-1;
        foreach( $terms as $key => $term ) : ?>
            <?php
            echo  $key == 0 ? '<li class="cat-item active" data-term_id="all"><a href="#">'.__('All').'</a></li>' : '';
            ?>
            <li class="cat-item" data-term_id="<?php echo $term->term_id; ?>">
                <a href="#"><?php echo $term->name; ?></a>
            </li>
            <?php
            if( $key == $last_key ) :
                echo '<li class="cat-item" data-term_id="by_date"><a href="#">'.__('By date').'</a></li>';
            endif;
            ?>
        <?php endforeach; ?>
    </ul>

    <?php
}

function get_test_sub_categories_by_term_id( $term_id = 'all' ) {

    if ($term_id == 'by_date') {
        return;
    }

    $terms = get_terms( array(
        'taxonomy'      => array( 'test_tax' ),
        'orderby'       => 'id',
        'order'         => 'ASC',
        'fields'        => 'all'
    ) );
    $taxonomy_name = "test_tax";

    echo '<ul class="test_sub_categories">';
    if ( $term_id == 'all') {
        foreach ($terms as $key => $term) {
            if ( $key == 0 ) {
                echo '<li class="test-item test-header">';
                echo '<span><span class="list-cat">'.__('Categories').'</span></span>';
                echo '</li>';
            }
            $termchildren = get_term_children( $term->term_id, $taxonomy_name );
            foreach ($termchildren as $child) {
                $term = get_term_by( 'id', $child, $taxonomy_name );
//                $link = get_term_link( $term->term_id, $term->taxonomy );
                echo '<li class="test-item cat-item" data-term_id="'. $term->term_id . '"><a href="#">';
                echo '<span>' . $term->name . '</span>';
                echo '</a></li>';
            }
        }
    } else {
        $termchildren = get_term_children( $term_id, $taxonomy_name );
        foreach ($termchildren as $key => $child) {
            if ( $key == 0 ) {
                echo '<li class="test-item test-header">';
                echo '<span><span class="list-cat">'.__('Categories').'</span></span>';
                echo '</li>';
            }
            $term = get_term_by( 'id', $child, $taxonomy_name );
            $link = get_term_link( $term->term_id, $term->taxonomy );
            echo '<li class="test-item cat-item"><a href="#">';
            echo '<span>' . $term->name . '</span>';
            echo '</a></li>';
        }
    }

    echo '</ul>';
}

function get_test_posts_date_by_term_id($term_id = 'all') {
    $args = array(
        'numberposts' => -1,
        'orderby'     => 'date',
        'order'       => 'DESC',
        'post_type'   => 'test',
        'suppress_filters' => true
    );
    if ( $term_id != 'all' && $term_id != 'by_date' ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'test_tax',
                'field' => 'id',
                'terms' => $term_id
            )
        );
    }
    $posts = get_posts( $args );
    $project_years = array();
    foreach( $posts as $key => $post ){
        $project_year = get_the_date('Y',$post->ID);
        array_push($project_years, $project_year);
    }
    $unique_years = array_unique($project_years);

    echo '<ul class="test_years_list">';
    foreach ( $unique_years as $key => $year ) :
        if ( $key == 0 ) {
            echo '<li class="test-item test-header">';
            echo '<span><span class="list-cat">'.__('Date').'</span></span>';
            echo '</li>';
        }
        echo '<li class="test-item test-item-year" data-year="'.$year.'" data-term_id="'.$term_id.'"><a href="#">';
        echo '<span>' . $year . '</span>';
        echo '</a></li>';
    endforeach;
    echo '</ul>';
}

function get_test_list_by_term_id( $term_id = 'all' ) {
    if( $term_id != 'by_date' ) :
        $args = array(
            'numberposts' => 12,
            'orderby'     => 'date',
            'order'       => 'DESC',
            'post_type'   => 'test',
            'suppress_filters' => true,
        );
        if ( $term_id != 'by_date' ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'test_tax',
                    'field' => 'id',
                    'terms' => $term_id
                )
            );
        }
        $posts = get_posts( $args );
        echo '<ul class="test_list">';
        foreach( $posts as $key => $post ){

            $link = get_the_permalink($post->ID);
            $term = $term_id != 'all' ? get_term_by( 'id', $term_id, 'test_tax' ) : '';
            $cat_name = !$term->name ? __('All') : $term->name;
            if ( $key == 0 ) {
                echo '<li class="test-item test-header">';
                echo '<span><span class="list-cat">'.__('Recent').'</span></span>';
                echo '</li>';
            }
            echo '<li class="test-item"><a href="' . $link . '">';
            echo '<span>' . $post->post_title . '</span>';
            echo '</a></li>';
        }
    echo '</ul>';
    endif;
}


function get_test_data_by_term_id( $term_id = 'all', $current_year = '' ) {
    $args = array(
        'numberposts' => -1,
        'orderby'     => 'date',
        'order'       => 'DESC',
        'post_type'   => 'test',
        'suppress_filters' => true
    );
    if ( $term_id != 'all' && $term_id != 'by_date' ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'test_tax',
                'field' => 'id',
                'terms' => $term_id
            )
        );
    }
    if ( $current_year ) {
        $args['date_query'] = array(
            array(
                'year'  => $current_year,
            ),
        );
    }
    $posts = get_posts( $args );
    echo '<div class="test_wrapper">';
    foreach( $posts as $key => $post ){
        $test_year = get_the_date('Y',$post->ID);
        $link = get_the_permalink($post->ID);
        $term = $term_id != 'all' ? get_term_by( 'id', $term_id, 'test_tax' ) : '';
        $cat_name = !$term->name ? __('All') : $term->name;
        ?>
        <div class="test-item">
            <a href="<?php echo $link; ?>" class="test-link">
				<span class="test-thumbnail">
					<?php echo get_the_post_thumbnail( $post->ID, 'full', array('class' => 'test-image') ); ?>
				</span>
                <span class="test-title"><?php echo get_the_title($post->ID); ?></span>
            </a>
        </div>
        <?php
    }
    echo '</div>';

}


function test_template() {
    ?>

    <div class="test_navigation">
        <?php get_cat_data(); ?>
        <div class="test_simple_list">
            <div class="test_list_wrap">
                <?php get_test_sub_categories_by_term_id(); ?>
                <?php get_test_list_by_term_id(); ?>
                <?php get_test_posts_date_by_term_id(); ?>
            </div>
        </div>
    </div>


    <div class="test_grid">
        <?php get_test_data_by_term_id(); ?>
    </div>

    <?php
}

function test_shortcode() {
    ob_start();
    test_template();
    $test_filter = ob_get_clean();
    return $test_filter;
}
add_shortcode( 'test_filter', 'test_shortcode' );



// ajax load more posts for categories
add_action( 'wp_ajax_test_list_ajax', 'test_list_ajax_func' );
add_action( 'wp_ajax_nopriv_test_list_ajax', 'test_list_ajax_func' );
function test_list_ajax_func() {
    $current_term_id = isset($_POST['current_term_id']) ? $_POST['current_term_id'] : 'all';
    get_test_sub_categories_by_term_id ( $current_term_id );
    get_test_list_by_term_id( $current_term_id );
    get_test_posts_date_by_term_id($current_term_id);
    die();
}

add_action( 'wp_ajax_test_filter_ajax', 'test_filter_ajax_func' );
add_action( 'wp_ajax_nopriv_test_filter_ajax', 'test_filter_ajax_func' );
function test_filter_ajax_func() {
    $current_term_id = isset($_POST['current_term_id']) ? $_POST['current_term_id'] : 'all';
    $current_year = isset($_POST['current_year']) ? $_POST['current_year'] : 'by_date';
    get_test_data_by_term_id( $current_term_id, $current_year );
    die();
}