/* This script is for "add/update" button on "Instagram API Settings" option page*/
jQuery(function($){
    $('#inst_update_btn').click(function(){
        var data_arr = {
            'action': 'update_button'
        };
        $.ajax({
            url:instajax.ajaxurl, // обработчик
            data:data_arr, // данные
            type:'POST' // тип запроса
        });
    });
});