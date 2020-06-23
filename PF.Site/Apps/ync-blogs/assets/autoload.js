
$Ready(function() {
    if ($('#advancedblog_js_blog_form').length && $('#advancedblog_has_ckeditor').length === 0) {
        (function(ele){
            if(!ele.length) return;
            var dir = $('html').attr('dir');
            tinymce.init({
                selector: '#text',
                height: 500,
                theme: 'modern',
                plugins: [
                    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                    'searchreplace wordcount visualblocks visualchars code fullscreen',
                    'insertdatetime media nonbreaking save table contextmenu directionality',
                    'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc'
                ],
                toolbar: dir,
                directionality : dir,
                toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                toolbar2: 'print preview media | forecolor backcolor emoticons | codesample'
            });
        })($('#advancedblog_js_blog_form #text'));
    }

    $('.js_mp_category_list').change(function () {
        var $this = $(this);
        var iParentId = parseInt(this.id.replace('js_mp_id_', ''));
        iCatId = $this.val();
        if (!iCatId) {
            iCatId = parseInt($this.parent().attr("id").replace('js_mp_holder_', ""));
        }


        $('.js_mp_category_list').each(function () {
            if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId) {
                $('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();

                this.value = '';
            }
        });
        var $parent = $(".js_ynadvblog_add_categories > .js_mp_parent_holder").find('.js_mp_category_list');
        $('#js_mp_holder_' + $(this).val()).show();
    });

    $('.jsConfirmImportBlog').click(function() {
        var buttons = {};
        buttons[oTranslations['yes']] = {
            'class': 'button dont-unbind',
            text: oTranslations['yes'],
            click: function() {
                $(this).dialog("close");
                return true;
            }
        };

        buttons[oTranslations['no']] = {
            'class': 'button dont-unbind',
            text: oTranslations['no'],
            click: function() {
                $(this).dialog("close");
                return false;
            }
        };

        $('#ynadvblog_dialog_content')
            .attr({title: $(this).data('title'), class: 'confirm'})
            .dialog({
                dialogClass: 'pf_js_confirm',
                close: function() {
                    $(this).dialog("close");
                },
                buttons: buttons,
                draggable: true,
                modal: true,
                resizable: false,
                width: 'auto'
            });
        return false;
    });
});

var ynadvancedblog = {

    updateFavorite: function (blog_id, iType) {
        $Core.ajaxMessage();
        $.ajaxCall('ynblog.updateFavorite', $.param({iBlogId: blog_id, bFavorite: iType, global_ajax_message: true}));
    },

    checkNothingToShow: function () {
        if ($('#page_route_advanced-blog').length > 0) {
            if ($('#page_route_advanced-blog div.js_blog_parent').length == 0)
                window.location.reload();
        }

        if ($('#page_route_advanced-blog_following').length > 0) {
            if ($('#page_route_advanced-blog_following div.ynadvblog_my_following_bloggers_inner').length == 0)
                window.location.reload();
        }
    },

    updateFollow: function (iUserId, iType) {
        $Core.ajaxMessage();
        $.ajaxCall('ynblog.updateFollow', $.param({iUserId: iUserId, bFollow: iType, global_ajax_message: true}), false, 'POST', ynadvancedblog.checkNothingToShow());
        return false;
    },

    updateFollowLink: function (iUserId, iType) {
        $Core.ajaxMessage();
        $.ajaxCall('ynblog.updateFollowLink', $.param({iUserId: iUserId, bFollow: iType, global_ajax_message: true}), false, 'POST', ynadvancedblog.checkNothingToShow());
        return false;
    },

    updateFollowButton: function (iUserId, iType) {
        var newType = iType ? 0 : 1;
        var $button = $('.js_ynblog_follow_btn_' + iUserId);
        if ($button.length) {
            $button.toggleClass('followed', iType).removeClass(iType ? 'btn-primary' : 'btn-default').addClass(iType ? 'btn-default' : 'btn-primary');
            $button.attr('onclick', 'ynadvancedblog.updateFollowLink(' + iUserId + ',' + newType + ');return false;');
            $button.attr('title', newType ? oTranslations['follow'] : oTranslations['following']);
            $button.blur();
        }
    },

    approveBlog: function (iBlogId) {
        $Core.ajaxMessage();
        $.ajaxCall('ynblog.approveBlog', $.param({iBlogId: iBlogId, global_ajax_message: true}), false, 'POST', ynadvancedblog.checkNothingToShow());
        return false;
    },

    denyBlog: function (iBlogId) {
        $Core.ajaxMessage();
        $.ajaxCall('ynblog.denyBlog', $.param({iBlogId: iBlogId, global_ajax_message: true}), false, 'POST', ynadvancedblog.checkNothingToShow());
        return false;
    },

    publishBlog: function (iBlogId) {
        $Core.ajaxMessage();
        $.ajaxCall('ynblog.publishBlog', $.param({iBlogId: iBlogId, global_ajax_message: true}), false, 'POST', ynadvancedblog.checkNothingToShow());
        return false;
    },

    updateFeature: function (iBlogId, iType) {
        $Core.ajaxMessage();
        $.ajaxCall('ynblog.updateFeature', $.param({iBlogId: iBlogId, bFeature: iType, global_ajax_message: true}));
        return false;
    },

    updateSavedBlog: function (iUserId, iType) {
        $Core.ajaxMessage();
        $.ajaxCall('ynblog.updateSavedBlog', $.param({iBlogId: iUserId, bSavedBlog: iType, global_ajax_message: true}));
        return false;
    },

    updateDetailSavedBlogBtn: function(iBlogId, status) {
        var $saveBtn = $('.js_p_blog_save_btn');
        if (!$saveBtn.length) {
            return;
        }
        var newStatus = status ? 0 : 1,
            newLabel = status ? oTranslations['saved'] : oTranslations['save'];

        $saveBtn.find('.item-text').html(newLabel);
        $saveBtn.find('i').removeClass(status ? 'ico-bookmark-o' : 'ico-bookmark').addClass(status ? 'ico-bookmark' : 'ico-bookmark-o');
        $saveBtn.attr('onclick', 'ynadvancedblog.updateSavedBlog(' + iBlogId + ',' + newStatus + ');return false;');
        $saveBtn.blur();
    },

    //For Import Blog
    switchImportBlogType: function (ele) {
        var $opened_cancel_btn = $('.js_advblog_importblog_item.has-expand').find('.p-form-group-btn-container a');
        if ($opened_cancel_btn.length) {
            ynadvancedblog.cancelImportBlogType($opened_cancel_btn[0]);
        }
        $('.js_advblog_importblog_item').removeClass('has-expand');
        $(ele).closest('.js_advblog_importblog_item').addClass('has-expand');
    },

    cancelImportBlogType: function(ele) {
        var $container = $(ele).closest('.js_advblog_importblog_item');
        $container.find('select').each(function(){
            $(this).val($(this).find('option').filter(':first').val());
        });
        $container.find('select').filter(':first').trigger('change');
        $container.find('input[name="val[txt_tumblr_username]"]').val('');
        $container.find('input[type="file"]').val('');
        $container.removeClass('has-expand');
    }
};

$Behavior.advblog_init_category_slider = function () {
    var owl_array = $('.p-advblog-slider-category-container-js');
    var rtl = false;
    if ($("html").attr("dir") == "rtl") {
        rtl = true;
    }
    owl_array.each(function(){
        var owl=$(this);
        var owl_dot_container = $(this).closest('.p-advblog-category-wrapper').find('.p-advblog-slider-control-wrapper .owl-dots');
        var item_amount = parseInt(owl.find('.item').length);

        var layout_col = 1;
        if($('#main:not(.empty-right):not(.empty-left)').length > 0){
            layout_col = 3;
        }else if( ($('#main.empty-right:not(.empty-left)').length > 0) || ($('#main.empty-left:not(.empty-right)').length > 0)){
            layout_col = 2;
        }
        var item_show = 2;
        var item_margin = 16;
        if (window.matchMedia('(min-width: 1200px)').matches) {
            if ( (layout_col == 2) || (layout_col == 3) ){
                item_show = 3;
            }
            if ( (layout_col == 1) ){
                item_show = 4;
            }
        } else if( window.matchMedia('(min-width: 992px)').matches){
            if( (layout_col == 1) || (layout_col == 2)){
                item_show = 3;
            }
        }
        owl.owlCarousel({
            rtl: rtl,
            items: item_show,
            dots:true,
            dotsContainer: owl_dot_container,
            smartSpeed: 800,
            navText: ["<i class='ico ico-angle-left'></i>", "<i class='ico ico-angle-right'></i>"],
            margin: item_margin,
            autoplay: false,
            autoplayTimeout: 5500,
            loop:false,
            onInitialized:callback,
            responsive:{
                0:{
                    items: 1
                },
                481:{
                    items: item_show
                }
            }
        });
        function callback(event){
            if(owl.closest('.p-advblog-category-wrapper').find('#advblog_category_carousel_custom_dots').hasClass('disabled')){
                owl.closest('.p-advblog-category-wrapper').find('.p-advblog-slider-category-bottom').hide();
            }
        }
        owl.closest('.p-advblog-category-wrapper').find('#advblog_category_next_slide').click(function(){
            $('.p-advblog-category-container').trigger('next.owl.carousel');
        });
        owl.closest('.p-advblog-category-wrapper').find('#advblog_category_prev_slide').click(function(){
            $('.p-advblog-category-container').trigger('prev.owl.carousel');
        });

        owl.closest('.p-advblog-category-wrapper').find('.owl-dot').click(function () {
          owl.trigger('to.owl.carousel', [$(this).index(), 300]);
        });
    });
}

$Behavior.ynadvblog_init_home_sliders = function () {
    setTimeout(function(){ 
        var owl_array = $('.p-advblog-slider-container');
        if ($("html").attr("dir") == "rtl") {
            rtl = true;
        }
        owl_array.each(function(){
            var owl=$(this);
            var owl_dot_container = $(this).closest('.p-advblog-feature-container').find('.p-advblog-slider-control-wrapper .owl-dots');
            if (!owl.length || owl.prop('built')) {
                return false;
            }
            owl.prop('built', true);
            owl.addClass('dont-unbind-children');
            var rtl = false;
            var item_amount = parseInt(owl.find('.item').length);
            var more_than_one_item = item_amount > 1;
            var dotseach = 1;
            if(item_amount > 10){
                dotseach = Math.ceil(item_amount/10);
            }
            owl.owlCarousel({
                rtl: rtl,
                items: 1,
                dotsEach : dotseach,
                loop: more_than_one_item,
                mouseDrag: more_than_one_item,
                margin: false,
                autoplay: more_than_one_item,
                autoplayTimeout: 5500,
                autoplayHoverPause: true,
                smartSpeed: 800,
                dots:true,
                dotsContainer: owl_dot_container,
                onInitialized:callback
            });
            function callback(event){
                if(owl_dot_container.hasClass('disabled')){
                    owl_dot_container.closest('.p-advblog-slider-bottom').hide();
                }
            }
            owl.closest('.p-advblog-feature-container').find('#advblog_next_slide').click(function(){
                owl.trigger('next.owl.carousel');
            });
            owl.closest('.p-advblog-feature-container').find('#advblog_prev_slide').click(function(){
                owl.trigger('prev.owl.carousel');
            });

            owl.closest('.p-advblog-feature-container').find('.owl-dot').click(function () {
              owl.trigger('to.owl.carousel', [$(this).index(), 300]);
            });
        });
    }, 300);
}