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
     * This func execution will init slick slider
     * */
    function slick_init() {
        $('.slider_wrap').slick();
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
         * Initialize our slick slider when page loads
         * */
        slick_init();
        /*
         * "Show More" button AJAX click event
         * */
        $('#show_more').click(function(){
            $('#show_more span').text('loading...');
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
                        $('#show_more span').text('Show More');
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