(function ($) {

    $(document).ready(function () {

        $('.filter_shortcode .et_pb_text_inner > p').remove();

        $('.test_navigation .test_categories .cat-item').on('mouseover',function (e) {
            e.preventDefault();
            var term_id = $(this).attr('data-term_id');
            $('.test_simple_list').addClass('visible');
            $.ajax({
                url : test_ajax.ajaxURL, //window.location.origin + '/wp-admin/admin-ajax.php', // test_ajax.ajaxURL
                type : 'POST',
                data : {
                    'action' : 'test_list_ajax',
                    'current_term_id': term_id
                },
                success : function( data ){
                    if (term_id == 'by_date') {
                        $('.test_simple_list').addClass('by_date');
                        $('.test_list_wrap').addClass('by_date');
                    } else {
                        $('.test_simple_list').removeClass('by_date');
                        $('.test_list_wrap').removeClass('by_date');
                    }
                    $('.test_simple_list .test_list_wrap').html(data);
                },
                complete: function () {

                },
                error : function(jqXHR, textStatus, errorThrown) {}
            });

            return false;
        });

        $('.test_simple_list').on('mouseover', function () {
            $('.test_simple_list').addClass('visible');
        });


        $('.test_simple_list, .test_navigation').on('mouseleave', function () {
            $('.test_simple_list').removeClass('visible');
            $('.test_list_wrap').removeClass('by_date');
        });

        $(document).on('click','.test_navigation .cat-item',function (e) {
            e.preventDefault();
            var term_id = $(this).attr('data-term_id');
            $('.test_navigation .cat-item').removeClass('active');
            $(this).addClass('active');
            $.ajax({
                url : test_ajax.ajaxURL, //window.location.origin + '/wp-admin/admin-ajax.php', // test_ajax.ajaxURL
                type : 'POST',
                data : {
                    'action' : 'test_filter_ajax',
                    'current_term_id': term_id
                },
                success : function( data ){
                    $('.test_grid').hide().html(data).fadeIn();
                },
                complete: function (data) {

                },
                error : function(jqXHR, textStatus, errorThrown) {}
            });
            return false;
        });

        $(document).on('click', '.test_years_list li.test-item-year', function (e) {
            e.preventDefault();
            var term_id = $(this).attr('data-term_id'),
                current_year = $(this).attr('data-year');
            $.ajax({
                url : test_ajax.ajaxURL, //window.location.origin + '/wp-admin/admin-ajax.php', // projects_ajax.ajaxURL
                type : 'POST',
                data : {
                    'action' : 'test_filter_ajax',
                    'current_term_id': term_id,
                    'current_year': current_year
                },
                success : function( data ){
                    $('.test_grid').hide().html(data).fadeIn();
                },
                complete: function (data) {

                },
                error : function(jqXHR, textStatus, errorThrown) {}
            });
            return false;
        });

    });

})(jQuery);