<?php
/*Template name: Api*/
wp_head();
//echo '<pre>'; var_dump(get_instagram_data()); echo '</pre>';
//echo '<pre>'; var_dump(get_instagram_data()->data[0]->images->standard_resolution->url); echo '</pre>';
$args = array(
    'posts_per_page' => 2,
    'category'    => 0,
    'orderby'     => 'none',
    'order'       => 'DESC',
    'include'     => array(),
    'exclude'     => array(),
    'meta_key'    => '',
    'meta_value'  =>'',
    'post_type'   => 'instagram',
    'suppress_filters' => true, // подавление работы фильтров изменения SQL запроса
    'paged' => get_query_var('paged') ? get_query_var('paged') : 1
);
global $wp_query;

$save_wpq = $wp_query;


$wp_query = new WP_Query( $args );
while ( $wp_query->have_posts() ) {
    $wp_query->the_post();
    $url = get_the_content();
    echo '<img src="' . $url . '">';
}
//echo get_the_posts_pagination($args);
if (  $wp_query->max_num_pages > 1 ) : ?>
    <script>
        var ajaxurl = '<?php echo site_url() ?>/wp-admin/admin-ajax.php';
        var true_posts = '<?php echo serialize($wp_query->query_vars); ?>';
        var current_page = <?php echo (get_query_var('paged')) ? get_query_var('paged') : 1; ?>;
        var max_pages = '<?php echo $wp_query->max_num_pages; ?>';
    </script>
    <div id="true_loadmore" style="margin-top: 50px">Загрузить ещё</div>
<?php endif;
wp_reset_postdata();
$wp_query = $save_wpq;
