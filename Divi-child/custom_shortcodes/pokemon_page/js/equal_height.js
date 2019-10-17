(function($){
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
})(jQuery);