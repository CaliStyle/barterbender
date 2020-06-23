
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
    initModeView: function (block_id, default_view) {
        var yn_viewmodes_block = $('#' + block_id + ' .ynadvblog-view-modes-block');

        var yn_cookie_viewmodes = getCookie(block_id + 'ynviewmodes');

        //Check if have cookie
        if (!yn_cookie_viewmodes) {
            yn_cookie_viewmodes = default_view;
        }

        yn_viewmodes_block.attr('class', 'ynadvblog-view-modes-block');
        yn_viewmodes_block.addClass('yn-viewmode-' + yn_cookie_viewmodes);

        $('#' + block_id + ' .ynadvblog-view-modes-block span[data-mode=' + yn_cookie_viewmodes + ']').addClass('active');

        $('#' + block_id + ' .yn-view-mode').click(function () {
            //Get data-mode
            var yn_viewmode_data = $(this).attr('data-mode');

            //Remove class active
            $(this).parent('.yn-view-modes').find('.yn-view-mode').removeClass('active');

            //Add class active
            $(this).addClass('active');

            //Set view mode
            yn_viewmodes_block.attr('class', 'ynadvblog-view-modes-block');
            yn_viewmodes_block.addClass('yn-viewmode-' + yn_viewmode_data);
            setCookie(block_id + 'ynviewmodes', yn_viewmode_data);
        });
    },

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
        $.ajaxCall('ynblog.updateSavedBlog', $.param({iUserId: iUserId, bSavedBlog: iType, global_ajax_message: true}));
        return false;
    },

    //For Import Blog
    switchImportBlogType: function (ele) {
        var isValid = false;
        switch ($(ele).data('type')) {
            case 1:
            case 2:
                isValid = true;
                $('#txt_tumblr_username-wrapper').hide();
                $('#ynblog_file_import-wrapper').show();
                $('#txt_tumblr_username').prop('required', false);
                $('#ynblog_file_import').prop('required', true);
                break;
            case 3:
                isValid = true;
                $('#txt_tumblr_username-wrapper').show();
                $('#ynblog_file_import-wrapper').hide();
                $('#txt_tumblr_username').prop('required', true);
                $('#ynblog_file_import').prop('required', false);
                break;
        }

        if (isValid) {
            $('#import_type').val($(ele).data('type'));
            if ($('#js_ynadvblog_import').length > 0 && $('#js_ynadvblog_import_choosefile').length > 0) {
                $('#js_ynadvblog_import').hide();
                $('#js_ynadvblog_import_choosefile').show();
            }
        }
    }
}