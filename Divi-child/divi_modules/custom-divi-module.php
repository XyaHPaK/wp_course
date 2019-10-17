<?php
function PSY_Custom_Modules(){
    if(class_exists("ET_Builder_Module")){
        include("subPageCard/SubPageCard.php");
        include ("GitHubInfo/GithubInfo.php");
        include ("linkedInApiSlider/linkedInApiSlider.php");
        include ("linkedInApiSlider/Slide.php");
        include ("Instagram/instagram.php");
    }
}

function Prep_PSY_Custom_Modules(){
    global $pagenow;

    $is_admin = is_admin();
    $action_hook = $is_admin ? 'wp_loaded' : 'wp';
    $required_admin_pages = array( 'edit.php', 'post.php', 'post-new.php', 'admin.php', 'customize.php', 'edit-tags.php', 'admin-ajax.php', 'export.php' ); // list of admin pages where we need to load builder files
    $specific_filter_pages = array( 'edit.php', 'admin.php', 'edit-tags.php' );
    $is_edit_library_page = 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && 'et_pb_layout' === $_GET['post_type'];
    $is_role_editor_page = 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'et_divi_role_editor' === $_GET['page'];
    $is_import_page = 'admin.php' === $pagenow && isset( $_GET['import'] ) && 'wordpress' === $_GET['import'];
    $is_edit_layout_category_page = 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && 'layout_category' === $_GET['taxonomy'];

    if ( ! $is_admin || ( $is_admin && in_array( $pagenow, $required_admin_pages ) && ( ! in_array( $pagenow, $specific_filter_pages ) || $is_edit_library_page || $is_role_editor_page || $is_edit_layout_category_page || $is_import_page ) ) ) {
        add_action($action_hook, 'PSY_Custom_Modules', 9789);
    }
}
Prep_PSY_Custom_Modules();
/* AJAX for instagram module*/
function true_load_posts(){
    $offset = $_POST['offset'];
    $arr = unserialize(stripslashes($_POST['query']));
    $arr1 = array_slice($arr, $offset, 10);

        echo '<div class="instagram_media_container">';
        foreach ( $arr1 as $post ):
            $url = $post->post_content;
            $url2 = get_the_post_thumbnail_url($post->ID);
            $label = get_post_meta($post->ID, 'out', true);
            echo '<div>';
            if ($url2 !== false ) {
                echo '<img class="large" src="' . $url2 . '">';
            } else {
                echo '<img class="equal" src="' . $url . '">';
            }
            echo '<span>'; echo $label; echo '</span>';
            echo '</div>';
        endforeach;
        echo '</div>' ;
    die();
}

add_action('wp_ajax_loadmore', 'true_load_posts');
add_action('wp_ajax_nopriv_loadmore', 'true_load_posts');

