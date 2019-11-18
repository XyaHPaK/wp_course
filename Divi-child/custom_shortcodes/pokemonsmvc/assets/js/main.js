/*
* CUSTOM SCRIPTS
*/
(function($){
    /*
    * find the highest item and make others the same height
    * */
    $.fn.equalHeight = function () {
        var tallest = 0;
        this.each(function () {
            var thisHeight = $(this).height();
            tallest = (thisHeight  > tallest) ? thisHeight : tallest;
        });
        return this.height(tallest);
    };
    /*
    * This func finds the highest element with chosen class and make other elements with the same class to be equal to it
    * */
    function fixHeights() {
        $('.pokemon_image').equalHeight();
    }
    function fixHeights1() {
        let heights = [];

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
    /*
    * single page slider initialize function
    * */
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
    /*
     * single page navigation slider initialize function
     * */
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

    /* START --> document.ready */
    $(document).ready(function () {
        /* Getting URL Search Params */
        let searchParams = new URLSearchParams(window.location.search);
        /*
         * "Show More" button AJAX click event
         * */
        $(document).on('click', '#show_more', function( event ){
            let length = $('.pokemons').data('pok_length') -15;
            event.preventDefault();
            $('#show_more a').text('loading...');
            let data_arr = {
                'action': 'load_more',
                'offset': ajaxarr.offset,
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
                        $('.pokemons_arch_grid').fadeIn(3000);
                        if (ajaxarr.offset >= length) {
                            $("#show_more").remove();
                        }
                        ajaxarr.offset = Number(ajaxarr.offset) + 15;
                    } else {
                        $('#show_more').remove();
                    }
                },
                complete: function () {
                    if ($('#pokemons_arch_grid_map').length !== 0) {
                        initMap();
                    } else {
                        fixHeights();
                    }
                    slick_init();
                }
            });
        });

        /*Redirect after click event*/
        $('.pok_link').click(function ( event ) {
            event.preventDefault();
            window.location.href = event.currentTarget.attributes.href.nodeValue;
        });

        /*
        * Executes condition below when URLSearchParams has id param
        * */
        let name = searchParams.get('id');
        if (name) {
            if ($('.poks_filter').length !== 0) {
                $('.poks_filter').css('display', 'none');
            }
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
        $(document).find('.map_btn').on('click', function () {
            ajaxarr.offset = 15;
            action = $('.pokemons_arch_grid').attr('data-filt') == 1 ? 'show_filtered' : 'to_map';
            map_data =  $('#pokemons_arch_grid_map').data('map');
            map_data =  1;
            min_hp = searchParams.get('hp_val_min') ? searchParams.get('hp_val_min') : $('#hp_val_min').val();
            max_hp = searchParams.get('hp_val_max') ? searchParams.get('hp_val_max') : $('#hp_val_max').val();
            min_cp = searchParams.get('cp_val_min') ? searchParams.get('cp_val_min') : $('#cp_val_min').val();
            max_cp = searchParams.get('cp_val_max') ? searchParams.get('cp_val_max') : $('#cp_val_max').val();
            type = searchParams.get('types') ? searchParams.get('types') : $('.poks_filter .ui-selectmenu-text').text();
            if (searchParams.get('hp_val_min') && $('.pokemons_arch_grid').attr('data-filt') == 0) {
                action = 'show_filtered';
            }
            let data_arr = {
                'action': action,
                'max_hp': max_hp,
                'min_hp': min_hp,
                'max_cp': max_cp,
                'min_cp': min_cp,
                'type': type,
                'map_data' : map_data
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
                    if (action === 'show_filtered') {
                        count = $('.grid_item').length;
                        $('.counter').text(count);
                    }
                }
            })
        });
        /*
        * grid button click event
        * */
        $(document).find('.grid_btn').on('click', function () {
            let count;
            ajaxarr.offset = 15;
            action = $('.pokemons_arch_grid').attr('data-filt') == 1 ? 'show_filtered' : 'to_grid';
            map_data =  $('#pokemons_arch_grid_map').data('map');
            map_data = 0;
            min_hp = searchParams.get('hp_val_min') ? searchParams.get('hp_val_min') : $('#hp_val_min').val();
            max_hp = searchParams.get('hp_val_max') ? searchParams.get('hp_val_max') : $('#hp_val_max').val();
            min_cp = searchParams.get('cp_val_min') ? searchParams.get('cp_val_min') : $('#cp_val_min').val();
            max_cp = searchParams.get('cp_val_max') ? searchParams.get('cp_val_max') : $('#cp_val_max').val();
            type = searchParams.get('types') ? searchParams.get('types') : $('.poks_filter .ui-selectmenu-text').text();
            if (searchParams.get('hp_val_min') && $('.pokemons_arch_grid').attr('data-filt') == 0) {
                action = 'show_filtered';
            }
            let data_arr = {
                'action': action,
                'max_hp': max_hp,
                'min_hp': min_hp,
                'max_cp': max_cp,
                'min_cp': min_cp,
                'type': type,
                'map_data': map_data
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
                    if (action === 'show_filtered') {
                        count = $('.grid_item').length;
                    }
                    $('.counter').text(count);
                    if (window.innerWidth >= 700) {
                        fixHeights();
                    }
                }
            })
        });
        /*
        *jquery-ui select menu initialize
        * */
        $( "#types" ).selectmenu();
        /*
        *jquery-ui hp slider initialize
        * */
        let hp_val_min = searchParams.get('hp_val_min') ? searchParams.get('hp_val_min') : $('#hp_val_min').val();
        let hp_val_max = searchParams.get('hp_val_max') ? searchParams.get('hp_val_max') : $('#hp_val_max').val();
        $("#hp_slider").slider({
            min: Number($('#hp_val_min').val()),
            max: Number($('#hp_val_max').val()),
            values: [Number(hp_val_min), Number(hp_val_max)],
            range: true,
            create: function (event, ui) {
                $("#hp_range").val(hp_val_min + '-' + hp_val_max);
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
        /*
         *jquery-ui cp slider initialize
         * */
        let cp_val_min = searchParams.get('cp_val_min') ? searchParams.get('cp_val_min') : $('#cp_val_min').val();
        let cp_val_max = searchParams.get('cp_val_max') ? searchParams.get('cp_val_max') : $('#cp_val_max').val();
        $("#cp_slider").slider({
            min: Number($('#cp_val_min').val()),
            max: Number($('#cp_val_max').val()),
            values: [Number(cp_val_min), Number(cp_val_max)],
            range: true,
            create: function (event, ui) {
                $("#cp_range").val(cp_val_min + '-' + cp_val_max);
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
        $(document).find('.filter_btn').on('click', function (e) {
            if ($('.poks_filter').attr('action') === '/') {
                e.preventDefault();
                $('.pokemons_arch_grid').attr('data-filt', 1);
                min_hp = $('#hp_val_min').val();
                max_hp = $('#hp_val_max').val();
                min_cp = $('#cp_val_min').val();
                max_cp = $('#cp_val_max').val();
                type = $('.poks_filter .ui-selectmenu-text').text();
                map_data = $('#pokemons_arch_grid_map').data('map');
                map_data = $('#pokemons_arch_grid_map').length === 1 ? 1 : 0;
                let data_arr = {
                    'action': 'show_filtered',
                    'max_hp': max_hp,
                    'min_hp': min_hp,
                    'max_cp': max_cp,
                    'min_cp': min_cp,
                    'type': type,
                    'map_data': map_data,
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
                    error: function (e) {
                        console.log(e);
                    },
                    complete: function () {
                        $('.counter').text($('.grid_item').length);
                        if (window.innerWidth <= 700 && map_data === 1) {
                            setTimeout(fixHeights, 200);
                        } else if (window.innerWidth >= 700 && map_data === 0) {
                            setTimeout(fixHeights, 200);
                        }
                        slick_init();
                    }
                })
            }
        });
        /*
        *checking query url query params existance and then change .counter text
        * */
        if (searchParams.get('hp_val_min') && $('.pokemons_arch_grid').attr('data-filt') == 0) {
            let count = $('.grid_item').length;
            $('.counter').text(count);
        }
        /*
        *executes fixHeights function after window loads
        * */
        $(window).load(function () {
            // Fix heights on page load with screen width >700px
            if (window.innerWidth >= 700) {
                setTimeout(fixHeights, 1500);
            }
        });
        // executes FixHeights on window resize
        if (!$('.pokemons_arch_grid_items') && window.innerWidth >= 700) {
            $(window).resize(function() {
                // Needs to be a timeout function so it doesn't fire every ms of resize
                setTimeout(function() {
                    fixHeights();
                }, 120);
            });
        }
        /*
         * Initialize our slick sliders on archive page when page loads
         * */
        slick_init();
    });
    /* END --> document.ready */

})(jQuery);