<?php
function api_connect($api_url, $request = '', $content_type = '') {
    $request = $request ? $request : "POST";
    $content_type = $content_type ? $content_type : 'Content-Type: application/x-www-form-urlencoded';
    $curl = curl_init();
    $headers = array(

    );

    curl_setopt_array($curl, array(
        CURLOPT_URL             =>  $api_url,
        CURLOPT_CUSTOMREQUEST   =>  $request,
        CURLOPT_HTTPHEADER      =>  $headers,
        CURLOPT_RETURNTRANSFER  =>  true,
        CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}