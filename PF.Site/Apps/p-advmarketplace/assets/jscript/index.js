$Behavior.initSlideshow = function() {
    var liSubMenus = $('body[id^=page_advancedmarketplace] #js_block_border_core_menusub ul.action li');
    var liSubMenusDevice = $('#js_block_border_core_menusub ul.dropdown-menu li');

    liSubMenus.each(function () {
        var link = $('a', $(this));

        if (link.length === 0 || link.attr('href') === undefined || link.attr('href').indexOf('view=gmap') === -1) {
            return;
        }

        link.attr('href', 'javascript:void(0)');
        link.attr('id', 'advancedmarketplace-google-map');
        $(document).on('click', '#advancedmarketplace-google-map', function () {
            tb_show('GoogleMap',
                $.ajaxBox('advancedmarketplace.gmap', 'height=300&width=730'));
        });
    });


    liSubMenusDevice.each(function () {
        var link = $('a', $(this));

        if (link.length === 0 || link.attr('href') === undefined || link.attr('href').indexOf('view=gmap') === -1) {
            return;
        }

        link.attr('href', 'javascript:void(0)');
        link.attr('id', 'advancedmarketplace-google-map');
        $(document).on('click', '#advancedmarketplace-google-map', function () {
            tb_show('GoogleMap',
                $.ajaxBox('advancedmarketplace.gmap', 'height=300&width=730'));
        });
    });

    if ($('#slideshow').length) {
        $('#slideshow').cycle({
            fx: 'scrollLeft',
            speed: 1000,
            timeout: 10000,
            pause: true,
            before: function () {
                $('.slide_thumbs a').removeClass('active');
                $('#thumb_' + this.id).addClass('active');
            },
        });
    }
};