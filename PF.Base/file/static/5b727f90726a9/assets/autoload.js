var $FbClone = {
    profileMenu: false,
    subMenu: false,
    headerTopSpace: 0,
    columnHeight: {
        'left': 0,
        'right': 0,
        'middle': 0
    },
    checkColumnHeight: {
        'left': false,
        'right': false,
        'middle': false
    },
    columnOffset: {
        'left': {
            'top': 0,
            'left': 0
        },
        'right': {
            'top': 0,
            'left': 0
        }
    },
    scrollPosition: 0,
    space: 16,
    initFixedElement: function(){
        if($(window).width() <= 991) {
            setTimeout(function () {
                $FbClone.updateContentMinHeight();
            }, 100);
        }
        else {
            $FbClone.updateContentMinHeight();
        }
        $(window).off('scroll');
        $('body').attr("data-header","fixed");
        $('body').attr('data-submenu','');
        $('body').attr('data-profile','');
        $('body').attr('data-left','');
        $('body').attr('data-right','');

        var getOffsetTop = function(ele) {
            if ($(ele).length == 0) return false;
            if (!$(ele).is(':visible')) return false;
            return $(ele).offset().top;
        };

        $FbClone.profileMenu = getOffsetTop('.profiles-menu.set_to_fixed');
        $FbClone.subMenu 	= getOffsetTop('.location_6 #js_block_border_core_menusub');
        var header_top_space = 0;
        var header = $('#section-header');
        var sticky_bar = $('#section-header .sticky-bar');

        if (header.length > 0 && header.css('position') == 'fixed') {
            header_top_space += header.height();
        } else if (sticky_bar.length > 0 && sticky_bar.css('position') == 'fixed') {
            header_top_space += sticky_bar.height();
        }

        $FbClone.scrollPosition = $(window).scrollTop();
        $FbClone.headerTopSpace = header_top_space;
        $FbClone.setColumnHeight('left');
        $FbClone.setColumnHeight('right');
        $FbClone.setColumnHeight('middle');

        $FbClone.setColumnOffset('left');
        $FbClone.setColumnOffset('right');
        //move error modal shoutbox tobody
        if ($('#shoutbox_error').length > 0) {
          $('#shoutbox_error').detach().appendTo('body');
        }
    },

    updateContentMinHeight: function () {
        if($('body').attr('id') == 'page_theme_sample') {
            return false;
        }
        if ($('body').attr('id') == 'page_core_index-visitor') {
            var ft = $('#section-footer'), m = $('#main');

            if (ft.length && m.length && ((ft.offset().top + ft.height()) < $(window).height())) {
                m.css({
                    minHeight: $(window).height() - m.offset().top - ft.height() - parseInt(ft.css('margin-top'))
                });
            }
            else {
                m.css({
                    minHeight: 'auto'
                });
            }
        }
        else if($('body').attr('id') != 'page_route_flavors_manage') {
            // fix min-height
            var ft = $('#section-footer'), st = $('#content-stage');
            var lm = st.parent();
            var old_min_height = parseInt(st.css('min-height'));
            var min_height = $(window).height() - st.offset().top - ft.outerHeight(true) - parseInt(lm.css('margin-bottom')) - 1;

            if (ft.length && st.length && ((ft.offset().top + ft.outerHeight(true)) < $(window).height()) || isNaN(old_min_height) || min_height < old_min_height) {
                st.css({
                    minHeight: min_height
                });
            }
        }
    },

    fixedColumnLR: function(){
        if($('body').attr('id') == 'page_theme_sample') {
            return false;
        }
        var elem_left	= $('#main .yncfbclone-layout-left');
        var elem_right 	= $('#main .yncfbclone-layout-right');

        if (elem_left.length > 0 && $('body').hasClass('yncfbclone-has-left-menu')) {
            $FbClone.setColumnFixed('left', elem_left);
        }

        if (elem_right.length > 0 && $('body').hasClass('yncfbclone-has-right-column')) {
            $FbClone.setColumnFixed('right', elem_right);
        }
    },

    fixedSection: function () {
        if($('body').attr('id') == 'page_theme_sample') {
            return false;
        }
        $FbClone.setSectionFixed($FbClone.profileMenu, 'profile', $FbClone.headerTopSpace);
        if($(window).width() <= 991) {
            $FbClone.setSectionFixed($FbClone.subMenu, 'submenu', $FbClone.headerTopSpace);
        }
    },

    setColumnHeight: function(column) {
        var elem = $('#main .yncfbclone-layout-' + column);
        var elem_height = elem.height();
        if(column == 'left' && !$('body').hasClass('yncfbclone-has-left-menu')) {
            elem_height = 0;
        }
        var height = $FbClone.columnHeight[column];
        if (elem_height != height) {
            $FbClone.columnHeight[column] = elem_height;
        }

        if (!$FbClone.checkColumnHeight[column]) {
            $FbClone.checkColumnHeight[column] = true;
            setInterval($FbClone.setColumnHeight(column), 250);
        }

        if (!$FbClone.checkColumnHeight.middle) {
            $FbClone.checkColumnHeight.middle = true;
            setInterval($FbClone.setColumnHeight('middle'), 250);
        }

        return elem_height;
    },

    getColumnHeight: function(column) {
        var height = $FbClone.columnHeight[column];
        if (height == 0) {
            return $FbClone.setColumnHeight(column);
        }
        return height;
    },

    getMaxColumnHeight: function () {
        return Math.max($FbClone.getColumnHeight('left'),  $FbClone.getColumnHeight('right'),  $FbClone.getColumnHeight('middle'));
    },

    getColumnOffset: function(column, offset) {
        if (typeof $FbClone.columnOffset[column][offset] != 'undefined') {
            return $FbClone.columnOffset[column][offset];
        }
        return 0;
    },
    setColumnOffset: function(column) {
        var elem 	= $('#main .yncfbclone-layout-' + column);
        if (elem.css('position') == 'fixed') {
            return false;
        }
        $FbClone.columnOffset[column] = elem.length > 0 ? elem.offset() : {};
    },

    // Function Set Header, Sub menu, Profile fixed.
    setSectionFixed: function(topOffset, datafixed, header_top_space){
        if (topOffset === false) {
            return false;
        }

        var old_stat = $('body').attr('data-'+datafixed);
        var new_stat = '';

        if($(window).scrollTop() + header_top_space > topOffset) {
            new_stat = 'fixed';
        }

        if (old_stat != new_stat) {
            $('body').attr('data-'+ datafixed, new_stat);
            $FbClone.setColumnOffset('left');
            $FbClone.setColumnOffset('right');
        }
    },
    setColumnFixed: function(left_right, elem){
        $(document).on('click', '#main .yncfbclone-layout-' + left_right, function(){
            setTimeout(function () { $FbClone.setColumnHeight(left_right); $(window).scroll(); }, 500);
        });

        var checkFixedColumn = function () {
            if ($('body').attr('id') == 'page_core_index-visitor') {
                return false;
            }
            var offset_top = $FbClone.getColumnOffset(left_right, 'top');
            var offset_left = $FbClone.getColumnOffset(left_right, 'left');
            var elem_height = $FbClone.getColumnHeight(left_right);
            var wd_height = $(window).height();
            var max_height = $FbClone.getMaxColumnHeight();
            var scroll = $(window).scrollTop();

            var offset_footer = $('#section-footer').length > 0 ? $('#section-footer').offset().top : 0;
            var new_offset_top = elem.offset().top;
            var header = $('#section-header');
            var sticky_bar = $('#section-header .sticky-bar');
            var sub_menu = $('._block.location_6 #js_block_border_core_menusub');
            var profiles_menu = $('._is_profile_view  .profiles-menu');
            var header_height = header.height();
            var space = $FbClone.space;
            var top_fix = 0;
            var offset_bot = elem_height + offset_top;
            var bottom = 0;
            if (wd_height + scroll >= offset_footer) {
                bottom = wd_height + scroll - offset_footer;
            }

            if (header.length > 0 && header.css('position') == 'fixed') { // fix header + sub menu
                top_fix += header_height;
            }
            else if (sticky_bar.length > 0 && sticky_bar.css('position') == 'fixed') { // only fix site header
                top_fix += sticky_bar.height();
            }
            else if (sub_menu.length > 0 && sub_menu.css('position') == 'fixed') { // only fix sub menu
                top_fix += sub_menu.height();
            }

            if (profiles_menu.length > 0 && profiles_menu.css('position') == 'fixed') { // fix profile menu
                top_fix += profiles_menu.height();
            }

            var top_space = top_fix + space;
            if(elem_height < max_height && (!$('#main').hasClass('empty-left') || !$('#main').hasClass('empty-right'))) {
                var elem_total = elem_height + new_offset_top;
                if (elem_total >= offset_footer) {
                    var top = offset_footer - (scroll + elem_height);
                    if(left_right == 'right') {
                        top = top - space;
                    }
                    elem.css({'top': top + 'px', 'left': offset_left + 'px'});
                    $('body').attr('data-' + left_right, 'fixed');
                }
                else {
                    if ((elem_height + top_space + bottom) < wd_height) {
                        var offset_top_compare = offset_top - 2*space;
                        if((profiles_menu.length && profiles_menu.css('position') !== 'fixed') || (header.length && header.css('position') !== 'fixed' && $('.location_6 #js_block_border_core_menusub').length)) {
                            offset_top_compare = offset_top;
                        }
                        else if($('._block.location_6 .app-addnew-block').length && !$('.location_6 #js_block_border_core_menusub').length) {
                            offset_top_compare = offset_top - space;
                        }

                        if ((scroll + top_fix) > offset_top_compare) {
                            elem.css({'top': top_space + 'px', 'left': offset_left + 'px'});
                            $('body').attr('data-' + left_right, 'fixed');
                        }
                        else {
                            $('body').attr('data-' + left_right, '');
                        }
                    }
                    else {
                        var elem_top = parseInt(elem.css('top'));
                        var positionUpdate = scroll - $FbClone.scrollPosition;
                        $FbClone.scrollPosition = scroll;
                        var new_top = 0;
                        if(left_right == 'left' && !isNaN(elem_top)) {
                            new_top = elem_top - positionUpdate;
                            if(scroll <= header_height) {
                                new_top = top_space;
                            }
                            if (new_top <= top_space) {
                                elem.css({'top': new_top + 'px', 'left': offset_left + 'px'});
                            }
                        }
                        if (scroll + wd_height > offset_bot + space) {
                            var top = wd_height - (elem_height + space + bottom);
                            if(left_right == 'left' && !isNaN(elem_top)) {
                                if(positionUpdate < 0) {
                                    if(new_top >= top_space) {
                                        return false;
                                    }
                                }
                                if(new_top > top && new_top < top_space) {
                                    top = new_top;
                                }
                            }
                            elem.css({'top': top + 'px', 'left': offset_left + 'px'});
                            $('body').attr('data-' + left_right, 'fixed');
                        } else {
                            if(left_right == 'left' && !isNaN(elem_top) && scroll >= header_height) {
                                if(header_height == scroll) {
                                    elem.css({'top': top_space + 'px', 'left': offset_left + 'px'});
                                }
                                $('body').attr('data-left', 'fixed');
                            }
                            else {
                                $('body').attr('data-' + left_right, '');
                            }
                        }
                    }
                }
            }
            else {
                $('body').attr('data-'+left_right,'');
            }
        };

        $(window).scroll(checkFixedColumn);
    },

    updateFbLayout: function () {
        var has_empty_left = $('#main').hasClass('has-empty-left');
        $('#yncfbclone-sub-menu-js').html('');
        $('#main').removeClass('has-move-left');
        $('#main').removeClass('has-empty-left');
        if($(window).width() <= 991) {
            $FbClone.moveFeaturedMembers();
            if(has_empty_left) {
                $('#main').addClass('empty-left');
            }
            $('body').removeClass('yncfbclone-has-left-menu');
            $('.yncfbclone-footer-bottom').css('display', '');
        }
        else {
            $FbClone.setHasLeftMemnu();
            $FbClone.moveSubMenuToLeft();
            if($(window).width() > 1280) {
                $FbClone.moveShoutboxFriendOnline();
            }
        }
    },

    // Function move shoutbox and friend online blocks
    moveShoutboxFriendOnline: function() {
        if ($('body').attr('id') !== 'page_core_index-member') {
            $('body').removeClass('yncfbclone-has-right-placeholder');
            return false;
        }
        $('#js_block_border__apps_phpfox_shoutbox_block_chat').hide();
        $('#js_block_border_friend_mini').hide();
        $('#yncfbclone_right_placeholder_js').show();

        if(!$('#yncfbclone_right_placeholder_js').length) {
            return false;
        }
        if($('#js_block_border__apps_phpfox_shoutbox_block_chat').length && $('#yncfbclone_shoutbox_js').html() == '') {
            $('#yncfbclone_shoutbox_js').append($('#js_block_border__apps_phpfox_shoutbox_block_chat .title '));
            $('#yncfbclone_shoutbox_js').append($('#js_block_border__apps_phpfox_shoutbox_block_chat .content '));
        }
        if($('#js_block_border_friend_mini').length && $('#yncfbclone_friend_online_js').html() == '') {
            $('#yncfbclone_friend_online_js').append($('#js_block_border_friend_mini .title'));
            $('#yncfbclone_friend_online_js').append($('#js_block_border_friend_mini .content'));
        }

        if($('#yncfbclone_shoutbox_js').html() == '' && $('#yncfbclone_friend_online_js').html() == '') {
            $('#yncfbclone_right_placeholder_js').hide();
            $('body').removeClass('yncfbclone-has-right-placeholder');
        }
        else {
            if( $('#yncfbclone_friend_online_js').html() == '') {
                $('#yncfbclone_shoutbox_js').addClass('no-friend-online-block')
            }
            if( $('#yncfbclone_shoutbox_js').html() == '') {
                $('#yncfbclone_shoutbox_js').addClass('no-content')
            }
            $('body').addClass('yncfbclone-has-right-placeholder');
        }
    },

    moveBackShoutboxFriendOnline: function() {
        if ($('body').attr('id') !== 'page_core_index-member') {
            return false;
        }
        if($('#js_block_border__apps_phpfox_shoutbox_block_chat').length && $('#yncfbclone_shoutbox_js').html() != '') {
            $('#js_block_border__apps_phpfox_shoutbox_block_chat').append($('#yncfbclone_shoutbox_js .title '));
            $('#js_block_border__apps_phpfox_shoutbox_block_chat').append($('#yncfbclone_shoutbox_js .content '));
        }

        if($('#js_block_border_friend_mini').length && $('#yncfbclone_friend_online_js').html() != '') {
            $('#js_block_border_friend_mini').append($('#yncfbclone_friend_online_js .title'));
            $('#js_block_border_friend_mini').append($('#yncfbclone_friend_online_js .content'));
        }

        $('#yncfbclone_right_placeholder_js').hide();
        $('#js_block_border__apps_phpfox_shoutbox_block_chat').show();
        $('#js_block_border_friend_mini').show();
    },

    // Function move featured users block
    moveFeaturedMembers: function() {
        if ($('body').attr('id') == 'page_core_index-visitor') {
            if ($('#js_block_border_user_featured').length && $('._block.location_3').length) {
                $('._block.location_3').append($('#js_block_border_user_featured'));
            }
        }
    },

    moveBackFeaturedMembers: function() {
        if ($('body').attr('id') == 'page_core_index-visitor') {
            if ($('#js_block_border_user_featured').length && $('._block.location_1').length) {
                $('._block.location_1').append($('#js_block_border_user_featured'));
            }
        }
    },

    setHasLeftMemnu: function () {
        if($('._block.location_6 #js_block_border_core_menusub').length || $('#page_core_index-member').length) {
            if(!$('body').hasClass('yncfbclone-has-left-menu')) {
                $('body').addClass('yncfbclone-has-left-menu');
            }
            $('.yncfbclone-footer-bottom').css('display', '');
            if(!$('#main').hasClass('empty-left')) {
                $('#main').removeClass('empty-right');
            }
            else {
                $('#main').addClass('has-empty-left');
            }
            $('#main').removeClass('empty-left');
        }
        else if(!$('body').hasClass('yncfbclone-has-left-menu')) {
            if(!$('#main').hasClass('empty-left') && !$('#main').hasClass('empty-right')) {
                $('#main').addClass('empty-left has-move-left');
            }
            if($('#yncfbclone-left').css('display') == 'none') {
                $('.yncfbclone-footer-bottom').show();
            }
        }
    },

    moveSubMenuToLeft: function () {
        if($('._block.location_6 #js_block_border_core_menusub').length) {
            $('#yncfbclone-sub-menu-js #js_block_border_core_menusub').remove();
            $('#yncfbclone-sub-menu-js').append($('#js_block_border_core_menusub'));

            if($('._block.location_6 .app-addnew-block').length) {
                $('#js_block_border_core_menusub').append($('.app-addnew-block'));
            }
        }
    },

    moveBackSubMenuToTop: function () {
        if($('#yncfbclone-sub-menu-js #js_block_border_core_menusub').length) {
            $('._block.location_6').prepend($('#js_block_border_core_menusub'));

            if($('#js_block_border_core_menusub .app-addnew-block').length) {
                $('#breadcrumbs_menu').append($('.app-addnew-block'));
            }

            $('#yncfbclone-sub-menu-js').html('');
        }
    },

    buildMenuSub: function() {
        $('[data-component="yncfbclone-menusub"]:not(\'.built\')').each(function() {
            var th = $(this),
                firstMenuItem = $('li:first', th),
                lastMenuItem = $('li:not(.explorer):last', th);

            if (typeof firstMenuItem.offset() === 'undefined' || typeof lastMenuItem.offset()  === 'undefined') {
              return;
            } else {
              var checkOffsetTop = firstMenuItem.offset().top + 20, // 20 for threshold
                  lastItemOffsetTop = lastMenuItem.offset().top;
            }

            if (checkOffsetTop > lastItemOffsetTop) {
              $('>div', th).hide();
              th.addClass('built');
              th.css('overflow', 'visible');

              return;
            }

            var explorerItem = $('>li.explorer', th).removeClass('hide'),
                itemSize = $('>li:not(.explorer)', th).length,
                explorerMenu = $('ul', explorerItem);

            function shouldMoveMenuItem() {
              th.find('>li:not(.explorer):last').prependTo(explorerMenu);
              return checkOffsetTop < explorerItem.offset().top;
            }

            for (var i = 0; i < itemSize; i++) {
              if (!shouldMoveMenuItem()) {
                $('>div', th).fadeOut();
                th.addClass('built');
                th.css('overflow', 'visible');

                return;
              }
            }
        });
    },

    fixActiveMenu: function () {
        if($('body#page_mail_index').length > 0) {
            $('.menu_messenger-js a').addClass('menu_is_selected');
        }
        if($('body#page_profile_index').length > 0) {
            $('a.profile-menu-timeline-js').addClass('active');
        }
        if($('body#page_profile_info').length > 0) {
            $('a.profile-menu-info-js').addClass('active');
        }
        if($('body#page_friend_profile').length > 0) {
            $('a.profile-menu-friend-js').addClass('active');
        }
    }
};

$Ready(function() {
    //set max-height for div friend online
    var height_friendonline= $('#yncfbclone_friend_online_js').height() - 24;
    $('#yncfbclone_friend_online_js .content').css('max-height',height_friendonline);
    //Header form search. clear search
    $.fn.clearSearch = function(options) {
        var settings = $.extend({
            'clearClass' : 'clear_input',
            'focusAfterClear' : true,
            'linkText' : '<span class="ico ico-close"></span>'
        }, options);
        return this.each(function() {
            var $this = $(this), btn,
                divClass = settings.clearClass + '_div';

            if (!$this.parent().hasClass(divClass)) {
                $this.wrap('<div style="position: relative;" class="' + divClass + '"></div>');
                $this.after('<a style="position: absolute; cursor: pointer;" class="'
                    + settings.clearClass + '">' + settings.linkText + '</a>');
            }
            btn = $this.next();

            function clearField() {
                $this.val('').change();
                triggerBtn();
                if (settings.focusAfterClear) {
                    $this.focus();
                }
                if (typeof (settings.callback) === 'function') {
                    settings.callback();
                }
            }

            function triggerBtn() {
                if (hasText()) {
                    btn.show();
                } else {
                    btn.hide();
                }
                update();
            }

            function hasText() {
                return $this.val().replace(/^\s+|\s+$/g, '').length > 0;
            }

            function update() {
            }

            if ($this.prop('autofocus')) {
                $this.focus();
            }

            btn.on('click', clearField);
            $this.on('keyup keydown change focus', triggerBtn);
            triggerBtn();
        });
    };

    $('#header_sub_menu_search_input').clearSearch({});
    //scale auto text area in block invite friend
    var textarea = document.getElementById('personal_message');
    if(textarea){
        textarea.addEventListener('keydown', autosize);
        function autosize(){
            var el = this;
            setTimeout(function(){
                el.style.cssText = 'height:auto; padding:8px';
                el.style.cssText = 'height:' + el.scrollHeight + 'px';
            },0);
        }
    };
    //init tooltip
    $(document).tooltip({
       selector: '[data-toggle="tooltip"]'
   });

    //remove tootip of right-side-friend
    $('#yncfbclone_friend_online_js [data-toggle="tooltip"]').removeAttr('data-toggle');

    //Navigation responsive
    $('.btn-nav-toggle').on("click", function(){
        $(".nav-mask-modal").addClass("in");
        $('body').addClass("overlap");
    });

    $('.nav-mask-modal').on("click touchend", function(){
        $(".main-navigation").removeClass("in");
        $('body').removeClass("overlap");
        $(this).removeClass("in");
    });

    $(".site-menu-small .ajax_link, .site-logo-link,.site-menu-small .user-icon .item-user a").on("click", function(){
        $(".nav-mask-modal").removeClass("in");
        $(".main-navigation").removeClass("in");
        $('body').removeClass("overlap");
    });

    //Sticky bar search on mobile
    $('.form-control-feedback').on("click", function(){
        $('.sticky-bar-inner').toggleClass('overlap');
    });

    //return search on mobile
    $('.btn-globalsearch-return').on("click", function(){
        $('.sticky-bar-inner').removeClass('overlap');
    });

    //Add class when input focus
    $('.form-control').on('focus', function() {
        var parent = $(this).parent('.input-group');
        if(parent){
            parent.addClass('focus');
        }
    });

    $('.form-control').on('blur', function() {
        $('.input-group').removeClass('focus');
    });

    // Just init custom scrollbar on desktop view.
    if(!(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )){
        //Init scrollbar
        $("#yncfbclone_friend_online_js .content, .user-sticky-bar .panel-items, .friend-search-invite-container, #js_main_mail_thread_holder .mail_messages, .js_box_content .item-membership-container, .dropdown-menu-limit, .attachment-form-holder .js_attachment_list").mCustomScrollbar({
            theme: "minimal-dark",
        }).addClass('dont-unbind-children');

        $("#div_compare_wrapper").mCustomScrollbar({
            theme: "minimal-dark",
            axis:"x" // horizontal scrollbar
        }).addClass('dont-unbind-children');

        $(".attachment_holder_view").mCustomScrollbar({
            theme: "dark"
        }).addClass('dont-unbind-children');

        //Init scrollbar landingpage
        $('#js_block_border_user_register .content .register_container_scroll').mCustomScrollbar({
            theme: "dark",
            scrollbarPosition: "inside",
        }).addClass('dont-unbind-children');

        PF.event.on('before_cache_current_body', function() {
            $('.mCustomScrollbar').mCustomScrollbar('destroy');
        });
    }

    //toggle for sign-up/sign-in form in landing page
    $(document).on('click', '.js-slide-visitor-form a.js-slide-btn', function(){
        $('.js-slide-visitor-form').toggle();
        var parent = $('.js-slide-visitor-form:visible:first'),
            block_title = parent.data('title');

        if (block_title && $('#js_block_border_user_register').length > 0) {
            $('#js_block_border_user_register').find('.title:first').html(block_title);
        }
    });

    //add class for category when collapse
    $(".core-block-categories ul.collapse").on('shown.bs.collapse', function(){
        $(this).closest('li.category').addClass('opened');
    });

    $(".core-block-categories ul.collapse").on('hidden.bs.collapse', function(){
        $(this).closest('li.category').removeClass('opened');
    });

    setTimeout(function () {
        $FbClone.setColumnHeight('middle');
        $FbClone.setColumnHeight('right');
    }, 500)
});

$(document).on('click', '[data-action="submit_search_form"]', function() {
    $(this).closest('form').submit();
});

$(document).on('click', '#hd-notification [data-dismiss="alert"]', function(evt) {
    evt.stopPropagation();
});


function page_scroll2top(){
    $('html,body').animate({
        scrollTop: 0
    }, 'fast');
}

$FbClone.updateFbLayout();

$Core.updateCommentCounter = function(module_id, item_id, str) {
    var sId = '#js_feed_like_holder_' + module_id + '_' + item_id + ', #js_feed_mini_action_holder_' + module_id + '_' + item_id;
    if ($(sId).length && $(sId).find('.feed-comment-link .counter').length) {
        $(sId).each(function(){
            var count = $(this).find('.feed-comment-link .counter').first().text();
            if (!count) {
                count = 0;
            }
            if (str == '+') {
                count = parseInt(count) + 1;
            }
            else {
                count = parseInt(count) - 1;
            }
            count = count <= 0 ? '' : count;
            $(this).find('.feed-comment-link .counter').first().text(count);
        })
    }
};

$FbClone.initFixedElement();
$FbClone.fixedSection();
if($(window).width() >= 768) {
    $FbClone.fixedColumnLR();
}
$FbClone.fixActiveMenu();
if($(window).width() > 600 && $(window).width() <= 991)  {
    $FbClone.buildMenuSub();
}

PF.event.on('on_page_change_end', function() {
    $FbClone.updateFbLayout();

    $FbClone.initFixedElement();
    $FbClone.fixedSection();
    if($(window).width() >= 768) {
        $FbClone.fixedColumnLR();
    }
    $FbClone.fixActiveMenu();
    if($(window).width() > 600 && $(window).width() <= 991)  {
        $FbClone.buildMenuSub();
    }
});

$(window).resize(function() {
    if($(window).width() > 1280) {
        $FbClone.moveShoutboxFriendOnline();
    }
    else {
        $FbClone.moveBackShoutboxFriendOnline();
    }
    if($(window).width() <= 991) {
        $FbClone.moveFeaturedMembers();
        $FbClone.moveBackSubMenuToTop();
        if($('#main').hasClass('has-empty-left')) {
            $('#main').addClass('empty-left');
            $('#main').removeClass('has-empty-left');
        }
        $('body').removeClass('yncfbclone-has-left-menu');
        $('.yncfbclone-footer-bottom').css('display', '');
    }
    else {
        $FbClone.setHasLeftMemnu();
        $FbClone.moveBackFeaturedMembers();
        $FbClone.moveSubMenuToLeft();
    }

    $FbClone.initFixedElement();
    $FbClone.fixedSection();
    if($(window).width() >= 768) {
        $FbClone.fixedColumnLR();
        $(window).scroll();
    }
});

$(document).on('scroll', window, function(){
    $FbClone.fixedSection();
    if($(window).scrollTop() >= 10) {
        $('.btn-scrolltop').fadeIn();
    } else {
        $('.btn-scrolltop').fadeOut();
    }

    //check scroll and fix height right side bar
    // 48 = height main menu
    if($(window).scrollTop() > 48){
        $('#yncfbclone_right_placeholder_js').addClass('scroll');
    }else{
        $('#yncfbclone_right_placeholder_js').removeClass('scroll');
    }
});

$Core.FriendRequest = {
    panel: {
        accept: function(requestId, message) {
            var requestRow = $('#drop_down_' + requestId, '#request-panel-body');

            $('.info', requestRow).text(message);
            $('.panel-actions', requestRow).remove();
            requestRow.addClass('friend-request-accepted');

            // update counter
            $Core.FriendRequest.panel.descreaseCounter();

            setTimeout(function() {
                $('.panel-item-content', requestRow).slideUp(200, function() {
                    requestRow.remove();
                    $Core.FriendRequest.panel.checkAndClosePanel();
                });
            }, 2e3);
        },

        deny: function(requestId) {
            var requestRow = $('#drop_down_' + requestId, '#request-panel-body');

            // update counter
            $Core.FriendRequest.panel.descreaseCounter();

            $('.panel-item-content', requestRow).fadeOut(400, function() {
                requestRow.remove();
                $Core.FriendRequest.panel.checkAndClosePanel();
            });
        },

        descreaseCounter: function() {
            var friendRequestCounter = $('#js_total_friend_requests');
            if (friendRequestCounter.length === 0) {
                return;
            }

            var total = friendRequestCounter.text().match(/\(([0-9]*)\)/);
            if (typeof total === 'object' && typeof total[1] !== 'undefined') {
                total = total[1] - 1;
                if (total > 0) {
                    friendRequestCounter.text('(' + total + ')');
                    $('#request-view-all-count').text(total);
                } else {
                    friendRequestCounter.remove();
                }
            }
        },

        checkAndClosePanel: function() {
            if ($('li', '#request-panel-body').length === 0) {
                $('#hd-request').trigger('click');
            }
        }
    },

    manageAll: {
        accept: function(requestId, message) {
            var requestRow = $('#request-' + requestId);

            $('.moderation_row', requestRow).remove();
            $('.item-info', requestRow).text(message);
            $('#drop_down_' + requestId, requestRow).remove();
            requestRow.addClass('friend-request-accepted');
            setTimeout(function() {
                requestRow.fadeOut(400, function() {
                    $(this).remove();
                    $Core.FriendRequest.manageAll.checkReload();
                });
            }, 2e3);
        },

        deny: function(requestId) {
            $('#request-' + requestId).slideUp(400, function() {
                $('#request-' + requestId).remove();
                $Core.FriendRequest.manageAll.checkReload();
            });
        },

        checkReload: function() {
            if ($('#collection-friends-incoming').children().length === 0) {
                window.location.reload();
            }
        }
    }
};
