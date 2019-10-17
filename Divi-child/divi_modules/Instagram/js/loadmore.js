jQuery(function($){
    $('#true_loadmore').click(function(){
        $(this).text('loading...'); // изменяем текст кнопки, вы также можете добавить прелоадер
        var data_arr = {
            'action': 'loadmore',
            'query': true_arr,
            'offset': offset,
            'length': length
        };
        $.ajax({
            url:myajax.ajaxurl, // обработчик
            data:data_arr, // данные
            type:'POST', // тип запроса
            success:function(data){
                if( data ) {
                    $('#true_loadmore').text('see more').before(data); // вставляем новые посты
                    offset+=10; // увеличиваем номер страницы на единицу
                    if (offset >= length) $("#true_loadmore").remove(); // если последняя страница, удаляем кнопку
                    var max_height = $('.equal').height();
                    $('.large').css('height',max_height);
                } else {
                    $('#true_loadmore').remove(); // если мы дошли до последней страницы постов, скроем кнопку
                }
            }
        });
    });
});