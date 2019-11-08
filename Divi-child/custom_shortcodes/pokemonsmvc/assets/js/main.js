(function($){
    /*
    * This func finds the highest element with chosen class and make other elements with the same class to be equal to it
    * */
    function fixHeights() {
        let heights = new Array();

        // Loop to get all element heights
        $('.pokemon_image').each(function() {
            // Need to let sizes be whatever they want so no overflow on resize
            $(this).css('min-height', '0');
            $(this).css('max-height', 'none');
            $(this).css('height', 'auto');

            // Then add size (no units) to array
            heights.push($(this).height());
        });

        // Find max height of all elements
        let max = Math.max.apply( Math, heights );

        // Set all heights to max height
        $('.pokemon_image').each(function() {
            if (window.innerWidth < 600) {
                $(this).css('height', 'auto');
            } else {
                $(this).css('height', max + 'px');
            }

            // Note: IF box-sizing is border-box, would need to manually add border and padding to height (or tallest element will overflow by amount of vertical border + vertical padding)
        });
    }
    /*
    * equals sliders heights
    * */
     function fix_sliders_heights() {
         let true_height = $('.single_page_slider').height();
         $('.single_page_slider_nav').css('height', true_height);
     }
    /*
     * This functions execution will init slick sliders
     * */
    function slick_init() {
        $('.slider_wrap').slick({
            infinite: false,
        });
    }
    function sp_slick_init() {
        $('.single_page_slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            infinite: false,
            verticalSwiping: true,
            asNavFor: '.single_page_slider_nav',
            responsive: [{
                breakpoint: 601,
                settings: {
                    arrows: true,
                    verticalSwiping: false
                }
            }]
        });
    }
    function sp_slick_nav_init() {
        $('.single_page_slider_nav').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            vertical: true,
            verticalSwiping: true,
            asNavFor: '.single_page_slider',
            centerMode: true,
            centerPadding: false,
            focusOnSelect: true,
            infinite: false,
            arrows: true,
        });
    }
    /*
     * This func execution will destroy earlier initialized slick slider
     * */
    function destroy_slick() {
        if ($('.slider_wrap').hasClass('slick-initialized')) {
            $('.slider_wrap').slick('destroy');
        }
    }
    /*
     * We use upper functions to reinitialize our slick sliders after our ajax request
     * */



    /* START --> document.ready */
    $(document).ready(function () {
        /*
         * Initialize our slick sliders on archive page when page loads
         * */
        slick_init();
        /*
         * "Show More" button AJAX click event
         * */
        $('#show_more').click(function( event ){
            let count = 15;
            event.preventDefault();
            $('#show_more a').text('loading...');
            let data_arr = {
                'action': 'load_more',
                'query': ajaxarr.poks_arr,
                'offset': ajaxarr.offset,
                'length': ajaxarr.length
            };
            $.ajax({
                url:ajaxarr.ajaxurl,
                data:data_arr,
                type:'POST',
                success:function(data){
                    if( data ) {
                        destroy_slick();
                        $('#show_more a').text('Show More');
                        $('.pokemons_arch_grid').hide();
                        $('#show_more').before(data);
                        $('.pokemons_arch_grid').fadeIn(2000);
                        ajaxarr.offset = Number(ajaxarr.offset) + 15;
                        if (ajaxarr.offset >= ajaxarr.length) {
                            count += Number(ajaxarr.length);
                            $('.counter').text(count);
                            $("#show_more").remove();
                        } else {
                            count += Number(ajaxarr.offset);
                            $('.counter').text(count);
                        }
                    } else {
                        $('#show_more').remove();
                    }
                },
                complete: function () {
                    /*makes elents equal to each other after succesfull ajax request */
                    fixHeights();
                    slick_init();
                }
            });
        });

        /*Redirect after click event*/
        $('.pok_link').click(function ( event ) {
            event.preventDefault();
            window.location.href = event.currentTarget.attributes.href.nodeValue;
        });
        /*Getting name from URLSearchParams (id param)*/
        let searchParams = new URLSearchParams(window.location.search);
        let name = searchParams.get('id');
        /*
        * Starts below when URLSearchParams has id param
        * */
        if (name) {
            let data_arr = {
                'action': 'to_single',
                'name': name
            };
            $.ajax({
                url: ajaxarr.ajaxurl,
                data: data_arr,
                type: 'POST',
                beforeSend: function () {
                    $('.pokemons').css({
                        width: '100%',
                        height: '100vh'
                    });
                    $('.preloader').css('display', 'block');
                },
                success: function (data) {
                    $('.pokemons').css({
                        width: 'initial',
                        height: 'initial'
                    });
                    $('.preloader').css('display', 'none');
                    $('.pokemons').hide().html(data);
                },
                complete: function () {

                    setTimeout(sp_slick_init, 100);
                    if ($('.single_page_slider_nav').children().length < 2) {
                        $('.single_page_slider_nav').css('display', 'none');
                        $('.single_page_slider').css({
                            'max-width': '100%',
                            'width': '100%'
                        });
                    } else {
                    setTimeout(sp_slick_nav_init, 100);
                    }
                    setTimeout(fix_sliders_heights, 150);
                    setTimeout(fixHeights, 150);
                    $('.pokemons').fadeIn(800);
                }
            })
        }
        /*
        * Makes all children to be enabled besides clicked one after this event
        * */
        $('.view_buttons button').on('click', function () {
            $('.view_buttons').children().map(function () {
                $('.view_buttons button').prop('disabled', false);
            });
            $(this).prop('disabled',true);
        });
        /*
        * map button click event
        * */
        $('.map_btn').on('click', function () {
            let count = 15;
            ajaxarr.offset = 0;
            let data_arr = {
                'action': 'to_map',
            };
            $.ajax({
                url: ajaxarr.ajaxurl,
                data: data_arr,
                type: 'POST',
                beforeSend: function () {
                    $('.preloader').css('display', 'block');
                },
                success: function (data) {
                    $('.preloader').css('display', 'none');

                    $('.pokemons_arch_grid').hide().html(data).fadeIn(2000);
                },
                complete: function () {
                    slick_init();
                    if (window.innerWidth <= 700) {
                        fixHeights();
                    }
                    $('.counter').text(count);
                }
            })
        });
        /*
        * grid button click event
        * */
        $('.grid_btn').on('click', function () {
            let count = 15;
            ajaxarr.offset = 0;
            let data_arr = {
                'action': 'to_grid',
            };
            $.ajax({
                url: ajaxarr.ajaxurl,
                data: data_arr,
                type: 'POST',
                beforeSend: function () {
                    $('.preloader').css('display', 'block');
                },
                success: function (data) {
                    $('.preloader').css('display', 'none');
                    $('.pokemons_arch_grid').hide().html(data).fadeIn(2000);
                },
                complete: function () {
                    slick_init();
                    $('.counter').text(count);
                    if (window.innerWidth >= 700) {
                        fixHeights();
                        $('#show_more').click(function( event ){
                            event.preventDefault();
                            $('#show_more a').text('loading...');
                            let data_arr = {
                                'action': 'load_more',
                                'query': ajaxarr.poks_arr,
                                'offset': ajaxarr.offset,
                                'length': ajaxarr.length
                            };
                            $.ajax({
                                url:ajaxarr.ajaxurl,
                                data:data_arr,
                                type:'POST',
                                success:function(data){
                                    if( data ) {
                                        destroy_slick();
                                        $('#show_more a').text('Show More');
                                        $('.pokemons_arch_grid').hide();
                                        $('#show_more').before(data);
                                        $('.pokemons_arch_grid').fadeIn(2000);
                                        ajaxarr.offset = Number(ajaxarr.offset) + 15;
                                        if (ajaxarr.offset >= ajaxarr.length) {
                                            count += Number(ajaxarr.length);
                                            $('.counter').text(count);
                                            $("#show_more").remove();
                                        } else {
                                            count += Number(ajaxarr.offset);
                                            $('.counter').text(count);
                                        }
                                        console.log(count);

                                    } else {
                                        $('#show_more').remove();
                                    }
                                },
                                complete: function () {
                                    /*makes elents equal to each other after succesfull ajax request */
                                    setTimeout(fixHeights, 100);
                                    slick_init();
                                }
                            });
                        });
                    }
                }
            })
        });
        $( "#types" ).selectmenu();
        $("#hp_slider").slider({
            min: Number(ajaxarr.min_hp),
            max: Number(ajaxarr.max_hp),
            values: [Number(ajaxarr.min_hp), Number(ajaxarr.max_hp)],
            range: true,
            create: function (event, ui) {
                $("#hp_range").val($("#hp_val_min").val() + '-' + $("#hp_val_max").val());
            },
            stop: function (event, ui) {
                $("#hp_val_min").val(ui.values[0]);
                $("#hp_val_max").val(ui.values[1]);
                $("#hp_range").val(ui.values[0] + '-' + ui.values[1]);
            },
            slide: function(event, ui){
                $("#hp_val_min").val(ui.values[0]);
                $("#hp_val_max").val(ui.values[1]);
                $("#hp_range").val(ui.values[0] + '-' + ui.values[1]);
            }
        });
        $("#cp_slider").slider({
            min: Number(ajaxarr.min_cp),
            max: Number(ajaxarr.max_cp),
            values: [Number(ajaxarr.min_cp), Number(ajaxarr.max_cp)],
            range: true,
            create: function (event, ui) {
                $("#cp_range").val($("#cp_val_min").val() + '-' + $("#cp_val_max").val());
            },
            stop: function (event, ui) {
                $("#cp_val_min").val(ui.values[0]);
                $("#cp_val_max").val(ui.values[1]);
                $("#cp_range").val(ui.values[0] + '-' + ui.values[1]);
            },
            slide: function(event, ui){
                $("#cp_val_min").val(ui.values[0]);
                $("#cp_val_max").val(ui.values[1]);
                $("#cp_range").val(ui.values[0] + '-' + ui.values[1]);
            }
        });
        /*
        * filter button click event
        * */
        $('.filter_btn').on('click', function () {
            if (window.location.href !== ajaxarr.arch_link) {
                window.location.href = ajaxarr.arch_link
            }
            min_hp = $('#hp_val_min').val();
            max_hp = $('#hp_val_max').val();
            min_cp = $('#cp_val_min').val();
            max_cp = $('#cp_val_max').val();
            type = $('.poks_filter .ui-selectmenu-text').text();
            act = $('#pokemons_arch_grid_map').length === 1 ? 'show_filtered_map' : 'show_filtered_grid';
            let data_arr = {
                'action': act,
                'max_hp': max_hp,
                'min_hp': min_hp,
                'max_cp': max_cp,
                'min_cp': min_cp,
                'type': type,
                'filtered_poks': ajaxarr.filtered_poks

            };
            $.ajax({
                url: ajaxarr.ajaxurl,
                data: data_arr,
                type: 'POST',
                beforeSend: function () {
                    $('.preloader').css('display', 'block');
                },
                success: function (data) {
                    $('.preloader').css('display', 'none');

                    $('.pokemons').hide().html(data).fadeIn(2000);
                },
                error: function(e) {
                    console.log(e);
                },
                complete: function () {
                    if (act === 'show_filtered_map') {
                        $('.map_btn').prop('disabled', true);
                        $('.grid_btn').prop('disabled', false);
                        if (window.innerWidth <= 700) {
                            setTimeout(fixHeights, 100);
                        }
                    } else {
                        if (window.innerWidth >= 700) {
                            setTimeout(fixHeights, 100);
                        }
                    }
                    slick_init();
                    $('.view_buttons button').on('click', function () {
                        $('.view_buttons').children().map(function () {
                            $('.view_buttons button').prop('disabled', false);
                        });
                        $(this).prop('disabled',true);
                    });
                    $('.map_btn').on('click', function () {
                        let count = $('.counter').text();
                        let data_arr = {
                            'action': 'to_map_filtered',
                        };
                        $.ajax({
                            url: ajaxarr.ajaxurl,
                            data: data_arr,
                            type: 'POST',
                            beforeSend: function () {
                                $('.preloader').css('display', 'block');
                            },
                            success: function (data) {
                                $('.preloader').css('display', 'none');

                                $('.pokemons_arch_grid').hide().html(data).fadeIn(2000);
                            },
                            complete: function () {
                                slick_init();
                                if (window.innerWidth <= 700) {
                                    setTimeout(fixHeights, 100);
                                }
                                $('.counter').text(count);
                            }
                        })
                    });
                    $('.grid_btn').on('click', function () {
                        let count = $('.counter').text();
                        let data_arr = {
                            'action': 'to_grid_filtered',
                        };
                        $.ajax({
                            url: ajaxarr.ajaxurl,
                            data: data_arr,
                            type: 'POST',
                            beforeSend: function () {
                                $('.preloader').css('display', 'block');
                            },
                            success: function (data) {
                                $('.preloader').css('display', 'none');
                                $('.pokemons_arch_grid').hide().html(data).fadeIn(2000);
                            },
                            complete: function () {
                                slick_init();
                                $('.counter').text(count);
                                if (window.innerWidth >= 700) {
                                    fixHeights();
                                }
                            }
                        })
                    });
                }
            })
        });
        /*
         * Executes fixHeights func when window loads
         * */
        $(window).load(function() {
            // Fix heights on page load with screen width >700px
            if (window.innerWidth >= 700) {
                fixHeights();
            }
            // Fix heights on window resize
            if (!$('.pokemons_arch_grid_items') && window.innerWidth >= 700) {
                $(window).resize(function() {
                    // Needs to be a timeout function so it doesn't fire every ms of resize
                    setTimeout(function() {
                        fixHeights();
                    }, 120);
                });
            }
        });

        /* END --> document.ready */
    });



})(jQuery);