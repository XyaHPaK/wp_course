<?php
/*cron task*/
function get_instagram_data($token) {
    $api_url = 'https://api.instagram.com/v1/users/self/media/recent?access_token=' . $token;
    $result = api_connect($api_url,'GET');
    $result = json_decode($result);
    return $result;

}
// регистрируем интервал
add_filter( 'cron_schedules', 'cron_add_3_days' );
function cron_add_3_days( $schedules ) {
    $schedules['3_days'] = array(
        'interval' => DAY_IN_SECONDS * 3,
        'display' => 'Once in 3 days'
    );
    return $schedules;
}

// добавляем запланированный хук
add_action( 'wp', 'instagram_activation' );
function instagram_activation() {
    if( ! wp_next_scheduled( 'instagram_event' ) ) {
        wp_schedule_event( time(), '3_days', 'instagram_event');
    }
}

// добавляем функцию к указанному хуку
add_action( 'instagram_event', 'instagram_data_update' );

function instagram_data_update(){
    $inst_media = get_instagram_data(get_option('instagram_api_option')['access_token'])->data;
    foreach ($inst_media as $data) {
        $slug = sanitize_title('instagram post id: ' . $data->id);

        $post_data = array(
            'post_title'    => 'instagram post id: ' . $data->id,
            'post_content'  => $data->images->standard_resolution->url,
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_category' => array(8,39),
            'post_type'     => 'instagram',
        );
        if(!slug_exists($slug)):
            wp_insert_post( wp_slash($post_data) );
        else:
            wp_update_post( $post_data);
        endif;
    }
}
