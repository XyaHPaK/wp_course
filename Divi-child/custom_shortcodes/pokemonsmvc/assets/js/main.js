(function($){
    /*
    * This func finds the highest element with chosen class and make other elements with the same class to be equal to it
    * */
    function fixHeights() {
        var heights = new Array();

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
        var max = Math.max.apply( Math, heights );

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
         var true_height = $('.single_page_slider').height();
         $('.single_page_slider_nav').css('height', true_height);
     }
    /*
     * This functions execution will init slick sliders
     * */
    function slick_init() {
        $('.slider_wrap').slick({
            infinite: false
        });
    }
    function sp_slick_init() {
        $('.single_page_slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            infinite: false,
            asNavFor: '.single_page_slider_nav',
            responsive: [{
                breakpoint: 600,
                settings: {
                    arrows: true
                }
            }]
        });
    }
    function sp_slick_nav_init() {
        $('.single_page_slider_nav').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            vertical: true,
            asNavFor: '.single_page_slider',
            centerMode: true,
            centerPadding: '100px',
            focusOnSelect: true,
            infinite: false,
            arrows: true,
            responsive: [{
                breakpoint: 1000,
                settings: {
                    centerPadding: '50px'
                }
            },{
                breakpoint: 600,
                settings: "unslick"
            }]
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
            event.preventDefault();
            $('#show_more a').text('loading...');
            var data_arr = {
                'action': 'load_more',
                'query': fivemorepoksajax.poks_arr,
                'offset': fivemorepoksajax.offset,
                'length': fivemorepoksajax.length
            };
            $.ajax({
                url:fivemorepoksajax.ajaxurl,
                data:data_arr,
                type:'POST',
                success:function(data){
                    if( data ) {
                        destroy_slick();
                        $('#show_more a').text('Show More');
                        $('#show_more').before(data);
                        fivemorepoksajax.offset = Number(fivemorepoksajax.offset) + 15;
                        if (fivemorepoksajax.offset >= fivemorepoksajax.length) $("#show_more").remove();
                        slick_init();
                    } else {
                        $('#show_more').remove();
                    }
                },
                complete: function () {
                    /*makes elents equal to each other after succesfull ajax request */
                    setTimeout(fixHeights, 100);
                }
            });
        });

        $('.pok_link').click(function ( event ) {
            event.preventDefault();
            window.location.href = event.currentTarget.attributes.href.nodeValue;
        });

        var searchParams = new URLSearchParams(window.location.search);
        var name = searchParams.get('id');

        if (name) {
            var data_arr = {
                'action': 'to_single',
                'name': name
            };
            $.ajax({
                url: fivemorepoksajax.ajaxurl,
                data: data_arr,
                type: 'POST',
                beforeSend: function () {
                    $('#fountainG').css('display', 'block');
                },
                success: function (data) {
                    $('#fountainG').css('display', 'none');
                    $('.pokemons_arch_grid').css('display', 'block').html(data);
                    $('.single_page_slider').css('max-width', '80%');
                    $('.single_page_slider_nav').css({
                        'max-width': '15%',
                        'overflow': 'hidden'
                    });
                    fixHeights();
                    sp_slick_init();
                    sp_slick_nav_init();
                    fix_sliders_heights();
                    $('.pokemons_arch_grid .slider_contaier').css({
                        'display': 'flex',
                        'flex-direction': 'row-reverse'
                    });
                }
            })
        }




        /*
         * Execute the upper func when window loads
         * */
        $(window).load(function() {
            // Fix heights on page load
            fixHeights();
            // Fix heights on window resize
            $(window).resize(function() {
                // Needs to be a timeout function so it doesn't fire every ms of resize
                setTimeout(function() {
                    fixHeights();
                }, 120);
            });
        });


        /* END --> document.ready */
    });



})(jQuery);