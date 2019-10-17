<?php

function create_true_arr() {
    $insta_posts = get_posts(array(
        'post_type'   => 'instagram',
        'numberposts' => -1
    ));
    $insta_small = array();
    $insta_large = array();

    foreach ($insta_posts as $post) {

        $meta = get_post_meta($post->ID, 'large', true);

        if ($meta) {
            array_push($insta_large, $post);
        } else {
            array_push($insta_small, $post);
        }

    }
    $count = 1;
    $large_count = 0;
    $count1 = 0;
    $count2 = 0;
    $true_arr = array();

    foreach ($insta_small as $post){
        if ($count == 5){
            array_push($true_arr, $insta_large[$large_count]);
            array_push($true_arr, $post);
            $large_count++;
            $count1 = $count + 3;

        } elseif ($count == $count1 ) {
            array_push($true_arr, $insta_large[$large_count]);
            array_push($true_arr, $post);
            $large_count++;
            $count2 = $count + 5;
        } elseif ($count == $count2) {
            array_push($true_arr, $insta_large[$large_count]);
            array_push($true_arr, $post);
            $large_count++;
            $count1 = $count +3;
        } else {
            array_push($true_arr, $post);
        }
        $count++;

    };
    return $true_arr;
}
//echo '<pre>'; var_dump(create_true_arr()); echo '</pre>';
