jQuery(function($){
    $( document ).ready(function() {
        var max_height = $('.equal').height();
        $('.large').css('height',max_height);
    });

    window.onresize = function() {
        var max_height = $('.equal').height();
        $('.large').css('height',max_height);
    };
});