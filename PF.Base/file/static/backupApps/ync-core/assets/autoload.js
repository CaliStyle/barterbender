$Ready(function () {
    // init advanced search
    if ($('#form_main_search') && $('#js_ync_search_wrapper') && $('#form_main_search').find('#js_ync_search_wrapper').length == 0) {
        $("#js_ync_search_wrapper").detach().insertBefore('#form_main_search .header_bar_search_inner .input-group .form-control-feedback');
        $('#js_ync_search_result').removeClass('hide');
    }

    // -------add this class if want to popup not hide when have event
    $('.yn-dropdown-not-hide').click(function (event) {
        event.stopPropagation();
    });
});

//casual init
var ync_casual_view = {
    init: function (mode_view_container) {
        if (!mode_view_container.hasClass('ync-init-pinto')) {
            mode_view_container.addClass('ync-init-pinto');
            mode_view_container.masonry();
        }
    },
    destroy: function (mode_view_container) {
        if (mode_view_container.hasClass('ync-init-pinto')) {
            mode_view_container.masonry('destroy');
            mode_view_container.removeClass('ync-init-pinto');
        }
    }
}

var ync_core = {
    yncEnableAdvSearch: function () {
        if ($('#form_main_search').find('#js_ync_adv_search_wrapper').length == 0) {
            $("#js_ync_adv_search_wrapper").detach().insertBefore('#js_search_input_holder');
            $('#js_ync_enable_adv_search_btn').addClass('active');
            $("#js_ync_adv_search_wrapper").slideDown();
        }
        else {
            $("#js_ync_adv_search_wrapper").slideUp();
            $('#js_ync_enable_adv_search_btn').removeClass('active');
            $("#js_ync_adv_search_wrapper").detach().insertAfter('#form_main_search');
        }
        if ($('#js_ync_enable_adv_search_btn').length) {
            if ($('#js_ync_enable_adv_search_btn').data('callback-js') !== undefined) {
                eval($('#js_ync_enable_adv_search_btn').data('callback-js'));
            }
        }
    },
    setCookie: function (cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    },
    getCookie: function (cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }
};

// define function set variable css3
$.fn.cssVar = function (property, value) {
    $.each($(this), function(key, ele) {
        ele.style.setProperty(property, value);
    });
};

var ync_mode_view = {
    init: function (page_id) {
        $('.' + page_id + ' .ync-mode-view-btn').off('click').on('click', function () {
            //Get data-mode
            var ync_viewmode_data = $(this).data('mode');
            var parent = $(this).parent();

            //Remove class active
            parent.find('.ync-mode-view-btn').removeClass('active');

            //Add class active
            $(this).addClass('active');

            // find block need to
            var mode_view_container = parent.siblings('.ync-view-modes-js');
            mode_view_container.attr('data-mode-view', ync_viewmode_data);

            if (ync_viewmode_data == 'casual') {
                ync_casual_view.init(mode_view_container);
            }
            else {
                ync_casual_view.destroy(mode_view_container);
            }
            // call callback js
            if ($('.' + page_id + ' .ync-mode-view-btn.' + ync_viewmode_data).data('callback-js') !== undefined) {
                eval($('.' + page_id + ' .ync-mode-view-btn.' + ync_viewmode_data).data('callback-js'));
            }

            // Set cookie
            ync_core.setCookie(page_id + '-ync-mode-view-cookie', ync_viewmode_data);
        });

        var ync_viewmode_data = ync_core.getCookie(page_id + '-ync-mode-view-cookie') || $('.ync-view-modes-js').data('mode-view') || $('.ync-view-modes-js').data('mode-view-default') || 'casual';
        if(!$('.' + page_id + ' .ync-mode-view-btn.' + ync_viewmode_data).hasClass('active')) {
            $('.' + page_id + ' .ync-mode-view-btn.' + ync_viewmode_data).trigger('click');
        }
        //add class for title block
        $('.ync-mode-view-container').closest('.ync-block').children('.title').addClass('has-modeview');

        if($('.ync-mode-view-container').length && $(window).width() > 767){
            $('body').addClass('has-modeview');
            var mode_view = $('.ync-mode-view-container');
            var wid = mode_view.width();
            var space = parseInt(mode_view.parents('.has-modeview').find('.header_filter_holder, .header-filter-holder').css('padding-right'));

            if($('html').attr("dir") == "ltr"){
                mode_view.parents('.has-modeview').find('.header_filter_holder:not(.built), .header-filter-holder:not(.built)').css('padding-right', wid + space).addClass('built');
            }
            if($('html').attr("dir") == "rtl"){
                mode_view.parents('.has-modeview').find('.header_filter_holder:not(.built), .header-filter-holder:not(.built)').css('padding-left', wid + space).addClass('built');
            }
        }
    }
}