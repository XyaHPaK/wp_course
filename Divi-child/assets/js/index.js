(function ($) {
    $(document).ready(function () {
        var currentLocation = window.location.href;
        $('a.sp_card_container').each(function () {
            var this_href = $(this).attr('href');
            if ( currentLocation === this_href ) {
                $(this).addClass('active');
            }
        });

    });
})(jQuery);
