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
$Behavior.yncCasualViewReload = function () {
    /*reinit when loadmore item*/
    setTimeout(function () {
        var mode_view_content = $('.ync-listing-container.ync-init-pinto');
        if(mode_view_content.length){
            ync_casual_view.destroy(mode_view_content);
            ync_casual_view.init(mode_view_content);
        }
    }, 500);
};


/*Clone new with p prefix*/
$Behavior.pCoreInit = function () {
    // init advanced search
    if ($('#form_main_search') && $('.js_p_search_wrapper') && $('#form_main_search').find('.js_p_search_wrapper').length == 0) {
        $(".js_p_search_wrapper").detach().insertBefore('#form_main_search .header_bar_search_inner .input-group .form-control-feedback');
        $('.js_p_search_result').removeClass('hide');
    }

    // -------add this class if want to popup not hide when have event
    $('.p-dropdown-not-hide').click(function (event) {
        event.stopPropagation();
    });

    //collapse
    if($('.js_p_collapse_content').length > 0){
        $('.js_p_collapse_content').each(function(){
            var max_height_content = $(this).data('max-height') || 88 ;
            if ($(this).prop('built')) {
                return;
            }
            $(this).prop('built', true);
            if($(this).outerHeight() > max_height_content){
                $(this).addClass('collapsed');
                 $(this).append('<span class="p-btn-collapse-content js_p_collapse_btn dont-unbind"><span class="item-text-action">' + oTranslations['view_more'] + '<i class="ico ico-caret-down"></i></span></span>');
                $(this).find('.js_p_collapse_btn').on('click',function(){
                    $(this).closest('.js_p_collapse_content').toggleClass('collapsed');
                    $(this).remove();
                });
              
            }

            $(this).addClass('built');
        });
    }

    //reverse dropdown
    if($('.js_p_dropdown_reverse').length > 0){
        $('.js_p_dropdown_reverse').each(function(){
            var pos = $(this).offset();
            var x_pos = pos.left;
            if(x_pos < 10){
                $(this).removeClass('dropdown-menu-right');
            }
        });
    }

    $('.p-mode-view-container').each(function(){
            p_mode_view.init($(this));
        }
    );

    if(!(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )){
        //Init scrollbar
        $(".p-mcustomscroll-h").mCustomScrollbar({
          theme: "minimal-dark",
          axis:"x" // horizontal scrollbar
        }).addClass('dont-unbind-children');

        $(".p-mcustomscroll-v").mCustomScrollbar({
          theme: "minimal-dark",
        }).addClass('dont-unbind-children');

        PF.event.on('before_cache_current_body', function() {
          $('.mCustomScrollbar').mCustomScrollbar('destroy');
        });
      }
};

//casual init
var p_casual_view = {
    init: function (mode_view_container) {
        if (!mode_view_container.hasClass('p-init-pinto')) {
            mode_view_container.addClass('p-init-pinto');
            mode_view_container.masonry();
        }
    },
    destroy: function (mode_view_container) {
        if (mode_view_container.hasClass('p-init-pinto')) {
            mode_view_container.masonry('destroy');
            mode_view_container.removeClass('p-init-pinto');
        }
    }
};

var p_core = {
    pEnableAdvSearch: function () {
        if ($('#form_main_search').find('.js_p_adv_search_wrapper').length == 0) {
            $(".js_p_adv_search_wrapper").detach().insertBefore('#js_search_input_holder');
            $('.js_p_enable_adv_search_btn').addClass('active');
            $(".js_p_adv_search_wrapper").slideDown();
        }
        else {
            $(".js_p_adv_search_wrapper").slideUp();
            $('.js_p_enable_adv_search_btn').removeClass('active');
            $(".js_p_adv_search_wrapper").detach().insertAfter('#form_main_search');
        }
        if ($('.js_p_enable_adv_search_btn').length) {
            if ($('.js_p_enable_adv_search_btn').data('callback-js') !== undefined) {
                eval($('.js_p_enable_adv_search_btn').data('callback-js'));
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

var p_mode_view = {
    init: function ($container) {
        var default_mode = p_mode_view.getDefaultMode($container);

        // switch to default mode
        p_mode_view.switch($container, default_mode);
        $container.find('.p-mode-view-btn').off('click').on('click', function () {
            //Get data-mode
            var mode = $(this).data('mode');
            p_mode_view.switch($container, mode);
        });

        //add class for title block
        $container.closest('.p-block').children('.title').addClass('has-modeview');

        if($container.length && $(window).width() > 767){
            $('body').addClass('has-modeview');
            var mode_view = $container;
            var wid = mode_view.width();
            var space = parseInt(mode_view.parents('.has-modeview').find('.header_filter_holder, .header-filter-holder').css('padding-right'));

            var $html = $('html');
            if($html.attr("dir") === "ltr"){
                mode_view.parents('.has-modeview').find('.header_filter_holder:not(.built), .header-filter-holder:not(.built)').css('padding-right', wid + space).addClass('built');
            }
            if($html.attr("dir") === "rtl"){
                mode_view.parents('.has-modeview').find('.header_filter_holder:not(.built), .header-filter-holder:not(.built)').css('padding-left', wid + space).addClass('built');
            }
        }
    },
    switch: function($container, mode) {
        //Remove class active
        $container.find('.p-mode-view-btn').removeClass('active');

        //Add class active
        $container.find('.p-mode-view-btn.' + mode).addClass('active');

        // find block need to
        var mode_view_content = $container.parent().find('.p-mode-view');
        mode_view_content.attr('data-mode-view', mode);

        if (mode === 'casual') {
            p_casual_view.init(mode_view_content);
        }
        else {
            p_casual_view.destroy(mode_view_content);
        }
        // call callback js
        if ($container.find('.p-mode-view-btn.' + mode).data('callback-js') !== undefined) {
            eval($container.find('.p-mode-view-btn.' + mode).data('callback-js'));
        }

        // Set cookie
        var mode_view_id = $container.data('mode-view-id');
        if (mode_view_id) {
            p_core.setCookie('p-mode-view-cookie-' + mode_view_id, mode);
        }
    },
    getDefaultMode: function($container) {
        var mode_view_id = $container.data('mode-view-id'), first_mode = '';
        if ( $container.find('.p-mode-view-btn').length) {
            first_mode = $container.find('.p-mode-view-btn').filter(':first').data('mode') || 'grid';
        }
        return p_core.getCookie('p-mode-view-cookie-' + mode_view_id) || $container.data('mode-view-default') || first_mode || 'grid';
    }
};

$Behavior.pCasualViewReload = function () {
    /*reinit when loadmore item*/
    setTimeout(function () {
        var mode_view_content = $('.p-listing-container.p-init-pinto');
        if(mode_view_content.length){
            p_casual_view.destroy(mode_view_content);
            p_casual_view.init(mode_view_content);
        }
    }, 500);
};

$Behavior.pInitStepScroll = function(){
    var width_step = $('.p-step-nav').width();
    var prev_item_active = $('.p-step-nav .p-step-item.active');
    $(".js_p_step_nav_outer_scroll").mCustomScrollbar({
      theme: "minimal-dark",
      axis:"x",
      callbacks: {
        onScroll: function () {
            $(this).closest('.p-step-nav-container').removeClass('not-prev');
            $(this).closest('.p-step-nav-container').removeClass('not-next');
        },
        onInit: function () {
            var scroll_parent= $(this).closest('.p-step-nav-container'),
                scroll_component= $(this);
            scroll_parent.addClass('has-scroll');
            $('.js_p_step_nav_button .nav-next').on('click', function () {
                scroll_component.mCustomScrollbar('scrollTo', "-=" + width_step);
            });
            $('.js_p_step_nav_button .nav-prev').on('click', function () {
                scroll_component.mCustomScrollbar('scrollTo', "+=" + width_step);
            });
            scroll_component.mCustomScrollbar('scrollTo', prev_item_active);
            if (this.mcs.left == 0) {
                scroll_parent.addClass('not-prev');
            }
        },
        onUpdate: function () {
            if ($(this).hasClass('mCS_no_scrollbar')) {
                $(this).closest('.p-step-nav-container').removeClass('has-scroll');
            } else {
                $(this).closest('.p-step-nav-container').addClass('has-scroll');
            }
        },
        onTotalScroll: function () {
            $(this).closest('.p-step-nav-container').addClass('not-next');
            $(this).closest('.p-step-nav-container').removeClass('not-prev');
        },
        onTotalScrollBack: function () {
            $(this).closest('.p-step-nav-container').addClass('not-prev');
            $(this).closest('.p-step-nav-container').removeClass('not-next');
        }
    }
    }).addClass('dont-unbind-children');
}