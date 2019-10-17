<?php
function slug_exists($post_name){
    global $wpdb;
    if ($wpdb->get_row("SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = '" . $post_name . "'", 'ARRAY_A'))  :
        return true;
    else :
        return false;
    endif;
}