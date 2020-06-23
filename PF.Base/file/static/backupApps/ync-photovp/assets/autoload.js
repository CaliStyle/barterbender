$Ready(function () {

});

var yncTimeOut, keyPressed = false;

var yncphotovp = {
    initialized: false,
    sBaseURL: getParam('sBaseURL'),
    sRegEx: /\/(photo|advancedphoto)\/(\d+)\/.*/,
    sPopupId: 'yncphotovp_popup',
    sEditClass: 'edit_mode',
    sTagClass: 'tag_mode',
    init: function () {
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            return;
        }
        var $popup = this.getPopup(), $links, $advPhotoLinks;

        if (!getParam('bIsAdminCP')) {
            if ($('body').attr('id') === 'page_photo_view' || $('body').attr('id') === 'page_advancedphoto_view') {
                $links = $('a[href^="' + yncphotovp.sBaseURL + 'photo/"]:not([onclick*="tb_show"],[href*="action=download"],[class^="addthis_button_"],[class^="stream_photo"],[class="ajax_link"],[id="next_photo"],[id="previous_photo"])');
                $advPhotoLinks = $('a[href*="' + yncphotovp.sBaseURL + 'advancedphoto/"]:not([onclick*="tb_show"],[href*="/download/"],[href$="/all/"],[class^="addthis_button_"],[class="ynadvphoto-detail-thumb-bg"],[class="ajax_link"])');
            } else {
                $links = $('a[href^="' + yncphotovp.sBaseURL + 'photo/"]:not([onclick*="tb_show"],[href*="action=download"],[class^="addthis_button_"])');
                $advPhotoLinks = $('a[href*="' + yncphotovp.sBaseURL + 'advancedphoto/"]:not([onclick*="tb_show"],[href*="/download/"],[class^="addthis_button_"])');
            }
            $links.each(yncphotovp.bindPhotoLinks);
            $advPhotoLinks.each(yncphotovp.bindPhotoLinks);
        }

        if (!$popup.length) {
            return;
        } else if (typeof screenfull !== 'undefined') {
            $popup.find('.ync-photovp-title-container a').click(yncphotovp.exitFullscreen);
        }

        // prevent loading new feed and reset the tag box
        window.bAddingFeed = true;

        $popup.find('.js_box_close a').removeAttr('onclick');
        $popup.find('.js_box_close a').off('click');
        this.initPhrases();

        var $tagContainer = $popup.find('.ync-photovp-tag-container'),
            $photoContainer = $popup.find('.ync-photovp-container')
        ;

        $popup
            .off('click')
            .on('click', yncphotovp.closeOnClickOutside)
            .on('click', '#js_ync_photovp_rotate', yncphotovp.rotatePhoto)
            .on('click', '.js_ync_photovp_edit', yncphotovp.editPhoto)
            .on('click', '.js_ync_photovp_cancel_edit', yncphotovp.cancelEdit)
            .on('click', '.js_ync_photovp_view_detail_page', yncphotovp.viewDetailPage)
            .on('click', '.js_box_close a', yncphotovp.closePopup)
            .on('click', '.js_yncphotovp_fullscreen', yncphotovp.toggleFullscreen)
            .on('click', '.js_ync_photovp_fullscreen_comment_toggle', yncphotovp.toggleComment)
            .on('click', '.btn-toggle-tag', function () {
                $tagContainer.removeClass('photovp-tag-build-toggle');
            })
            .on('mouseup', '#js_tag_photo', yncphotovp.tagPhoto)
            .on('click', '#js_ync_photovp_tag_photo_2', function () {
                $('#js_tag_photo')[0].click();
                yncphotovp.tagPhoto();
            })
        ;

        $tagContainer
            .off('DOMSubtreeModified propertychange')
            .one('DOMSubtreeModified propertychange', yncphotovp.updateTagListToggle)
        ;

        $(document).unbind('keydown').unbind('keyup').keydown(yncphotovp.keyboardNavigation).keyup(function () {
            keyPressed = false;
        });

        $popup.find('.dropup').on('show.bs.dropdown', function () {
            $(this).closest('.ync-photovp-content-info').addClass('has-open-dropdown');
        }).on('hidden.bs.dropdown', function () {
            $(this).closest('.ync-photovp-content-info').removeClass('has-open-dropdown');
        });

        $popup.find('img#js_photo_view_image').unbind('hover').hover(
            function () {
                $('.note, .notep')
                    .css('z-index', 6000)
                    .show();
            },
            function () {
                $('.note, .notep').hide();
            }
        );

        if (typeof yncphotovp.tb_remove === 'undefined') {
            yncphotovp.tb_remove = window.tb_remove;
        }
        window.tb_remove = function () {
            var $jsBoxes = $('.js_box_holder');
            if ($jsBoxes.length === 1 && $jsBoxes.attr('id') === yncphotovp.sPopupId) {
                return false;
            } else {
                yncphotovp.tb_remove();
            }
        };

        //add scroll
        $(".ync-photovp-block-info-inner").mCustomScrollbar({
            theme: "minimal-dark",
        }).addClass('dont-unbind-children');

        if (typeof screenfull !== 'undefined') {
            screenfull.off('change', yncphotovp.toggleFullscreenClass);
            screenfull.on('change', yncphotovp.toggleFullscreenClass);
        }

        var $commentForm = $popup.find('.js_feed_comment_form, .ync-comment-box-container');
        if ($commentForm.length) {
            $commentForm
                .on('keyup blur', yncphotovp.updateCommentListHeight)
                .on('DOMSubtreeModified', yncphotovp.updateCommentListHeight);
        } else {
            $photoContainer.addClass('cannot_comment');
        }

        window.addEventListener("popstate", function () {
            $popup && $popup.find('.js_box_close a').trigger('click');
        });
        window.showaddnote = this.showaddnoteOverwrite;
        $(window).off('resize').on('resize', yncphotovp.resizePhoto);

        setTimeout(function () {
            $(window).trigger('resize');
        }, 20);
        setTimeout(function () {
            $(window).trigger('resize');
        }, 200);
    },
    initPhrases: function () {
        if (typeof window.oTranslations === 'undefined') {
            window.oTranslations = {};
        }
        oTranslations['done_editing'] = ync_photovp_phrases['done_editing'];
        oTranslations['done_tagging'] = ync_photovp_phrases['done_tagging'];
        oTranslations['click_here_to_tag_as_yourself'] = ync_photovp_phrases['click_here_to_tag_as_yourself'];
        oTranslations['editing_photo_information'] = ync_photovp_phrases['editing_photo_information'];
        oTranslations['tagged_in_this_photo'] = ync_photovp_phrases['tagged_in_this_photo'];
    },
    bindPhotoLinks: function () {
        var $link = $(this),
            sLink = $link.attr('href');

        if (!yncphotovp.sRegEx.test(sLink)) {
            return;
        }

        // $link.off('click', yncphotovp.exitFullscreen);

        if ($('body').attr('id') === 'page_advancedphoto_view') {
            if ($(this).parent('li.previous, li.next').length) {
                return;
            }
        }

        $link.unbind('click').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (yncphotovp.isPopupOpen()) {
                yncphotovp.fetch(sLink);
            } else {
                yncphotovp.openPopup(sLink);
            }
        });
    },
    parseExtraParams: function (sLink) {
        var regex = /([^\/]+)_(\d+)/g;
        var matches = sLink.match(regex),
            paramsStr = '';

        if (Array.isArray(matches)) {
            var matchLength = matches.length;
            for (var i = 0; i < matchLength; i++) {
                matches[i] = matches[i].replace("_", "=");
            }
            paramsStr = '&' + matches.join('&');
        }

        return paramsStr;
    },
    openPopup: function (sLink) {
        window.yncphotovpWindowScrollY = window.scrollY;
        tb_remove();
        tb_show('', yncphotovp.getAjaxBox(sLink), '', '', false, '', false, yncphotovp.sPopupId);
        $('body').css({
            top: 0
        });
    },
    getAjaxBox: function (sLink) {
        var checkPhotoLink = sLink.match(yncphotovp.sRegEx),
            module = checkPhotoLink[1],
            photoID = checkPhotoLink[2],
            extraParams = yncphotovp.parseExtraParams(sLink);

        return $.ajaxBox('yncphotovp.view', 'slink=' + sLink + '&photo_id=' + photoID + '&module=' + module + extraParams);
    },
    closeOnClickOutside: function (e) {
        if (e.target === e.currentTarget) {
            var $popup = yncphotovp.getPopup();
            $popup && $popup.find('.js_box_close a').trigger('click');
        }
    },
    keyboardNavigation: function (e) {
        if (keyPressed) {
            return;
        }
        keyPressed = true;
        var $popup = yncphotovp.getPopup();
        var $photoContainer = $popup.find('.ync-photovp-container');
        if ($photoContainer.hasClass(yncphotovp.sEditClass)
            || $photoContainer.hasClass(yncphotovp.sTagClass)
            || $('textarea:focus').length
            || $('input[type="text"]:focus').length) {
            return; // exit this handler for other keys
        }
        switch (e.which) {
            case 34:
                if ($popup.hasClass('ync_photovp_fullscreen')) {
                    yncphotovp.toggleComment();
                }
                break;
            case 37: // left
                $popup.find('#ync_photovp_previous_photo').trigger('click');
                break;
            case 39: // right
                $popup.find('#ync_photovp_next_photo').trigger('click');
                break;
            case 38:
            case 40:
                screenfull.toggle($('body')[0]);
                break;
            case 27:
                // e.stopPropagation();
                $popup.find('.js_box_close a').trigger('click');
                break;
            default:
                return;
        }
        e.preventDefault();
    },
    closePopup: function () {
        yncphotovp.cancelTagPhoto();
        $('.note, .notep').remove();
        $('#main_core_body_holder').show();

        var $aAllBoxIndex = [],
            $aAllBoxIndexHolder = [];

        $('.js_box').each(function () {
            $aAllBoxIndex[parseInt($(this).css('z-index'))] = $(this).attr('id');
            $aAllBoxIndexHolder.push(parseInt($(this).css('z-index')));
        });

        var $iFocusBox = parseInt(Math.max.apply(Math, $aAllBoxIndexHolder));

        if (isset($aAllBoxIndex[$iFocusBox])) {
            var $focusBox = $('#' + $aAllBoxIndex[$iFocusBox]);
            if ($focusBox.length > 0) {
                var $sLink = $focusBox.find('.js_box_history:first').html();
                var holder = yncphotovp.getPopup();
                $focusBox.remove();
                delete $aBoxHistory[$sLink];
                holder.remove();
                $('body').css({
                    overflow: '',
                    position: '',
                    width: '',
                    top: ''
                });
                $(window).scrollTop(window.yncphotovpWindowScrollY);
            }
        }

        $('#global_attachment_list_inline').hide();
        $(document).unbind('keydown').unbind('keyup');
        $(window).off('resize');
        keyPressed = false;
        window.bAddingFeed = false;

        return false;
    },
    isPopupOpen: function () {
        return this.getPopup().length;
    },
    getPopup: function () {
        return $('#' + yncphotovp.sPopupId);
    },
    reload: function () {
        var currentLink = yncphotovp.getCurrentLink();
        if (currentLink) {
            yncphotovp.openPopup(currentLink);
        }
    },
    refresh: function () {
        var currentLink = yncphotovp.getCurrentLink();
        if (currentLink) {
            yncphotovp.cancelEdit();
            yncphotovp.fetch(currentLink);
        }
    },
    fetch: function (sLink) {
        var $popup = yncphotovp.getPopup();
        $('.note, .notep').remove();
        $popup.find('.js_box_content').html('<span class="js_box_loader"><i class="fa fa-spin fa-spinner"></i></span>');
        $popup.find('.js_box_content').load(yncphotovp.getAjaxBox(sLink), function () {
            $Core.loadInit();
        });
    },
    getCurrentLink: function () {
        var $popup = yncphotovp.getPopup();
        if (!$popup.length) {
            return '';
        }
        var $slink = $popup.find('#js_ync_photovp_slink');
        if (!$slink.length || !$slink.val()) {
            return '';
        }
        return $slink.val();
    },
    fetchLink: function (e) {
        var sLink = $(this).data('href');
        yncphotovp.fetch(sLink);
    },
    viewDetailPage: function () {
        var currentLink = yncphotovp.getCurrentLink();
        if (currentLink) {
            window.location.href = currentLink;
        }
    },
    rotatePhoto: function () {
        var photo_id = $(this).data('photo_id'),
            photo_cmd = $(this).data('cmd');
        if (photo_id) {
            $.ajaxCall('yncphotovp.rotate', 'photo_id=' + photo_id + '&photo_cmd=' + photo_cmd);
        }
    },
    editPhoto: function () {
        var photo_id = $(this).data('photo_id');
        if (typeof photo_id !== 'undefined' && photo_id) {
            var $popup = yncphotovp.getPopup();
            if ($popup.hasClass('ync_photovp_fullscreen')) {
                $popup.addClass('has-comment');
            }
            $popup.find('.ync-photovp-block-info-title').html(oTranslations['editing_photo_information']);
            $popup.find('.ync_photovp_edit_form').load($.ajaxBox('photo.editPhoto', 'photo_id=' + photo_id), function () {
                $popup.find('form').removeAttr('onsubmit').off('submit').submit(function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).ajaxCall('yncphotovp.updatePhoto');
                });
                $popup.find('form .privacy_setting').addClass('dropup');
                $(this).closest('.ync-photovp-container').addClass(yncphotovp.sEditClass);
                var $submitButton = $(this).find('input[type="submit"]');
                var btnGroup = $('<div class="ync_photovp_edit_btn_group"></div>');
                var cancelButton = $('<button type="cancel" class="btn btn-default btn-sm js_ync_photovp_cancel_edit">' + oTranslations['cancel'] + '</button>');
                $submitButton.val(oTranslations['done_editing']).addClass('btn-sm').before(btnGroup).appendTo(btnGroup).after(cancelButton);
            });
        }
    },
    cancelEdit: function () {
        yncphotovp.getPopup().find('.ync-photovp-container').removeClass(yncphotovp.sEditClass).find('.ync_photovp_edit_form').html('');
    },
    tagPhoto: function () {
        var $popup = yncphotovp.getPopup();
        $Core.photo_tag.aParams['id'] = '#js_photo_view_image';

        $popup.find('.ync-photovp-container').addClass(yncphotovp.sTagClass);

        if ($popup.find('.done-tagging-btn').length) {
            return;
        }

        $popup.find('.ync-photovp-block-info-title').html(oTranslations['tagged_in_this_photo']);

        var doneBtn = $('<a />', {
            'text': oTranslations['done_tagging'],
            'class': 'done-tagging-btn btn btn-default btn-sm dont-unbind',
            'href': 'javascript:void(0)'
        }).on('click', yncphotovp.cancelTagPhoto);

        $popup.find('.photovp_view').append(doneBtn);
    },
    cancelTagPhoto: function () {
        var $popup = yncphotovp.getPopup();
        var $tagContainer = $popup.find('.ync-photovp-tag-container');
        $('div#noteform').hide();
        (typeof $Core.photo_tag !== 'undefined') && $($Core.photo_tag.aParams['id']).imgAreaSelect({remove: true});
        if ($tagContainer.find('#js_photo_in_this_photo span').length) {
            $tagContainer.removeClass('only-tag');
        } else {
            $tagContainer.addClass('only-tag');
        }
        $popup.find('.ync-photovp-container').removeClass(yncphotovp.sTagClass);
        $('.done-tagging-btn').remove();
    },
    resizePhoto: function () {
        var $popup = yncphotovp.getPopup();
        if (!$popup.length) {
            return;
        }
        var photoHolder = $popup.find('.ync-photovp-container'),
            imageMaxHeight = photoHolder.height();

        photoHolder.find('img').css('max-height', imageMaxHeight + 'px');
        photoHolder.find('.ync-photovp-block-info').css('max-height', imageMaxHeight + 'px');
    },
    toggleFullscreen: function () {
        screenfull.toggle($('body')[0]);
    },
    exitFullscreen: function () {
        screenfull.exit();
    },
    toggleComment: function () {
        var $popup = yncphotovp.getPopup();
        $popup.toggleClass('has-comment');
        $(window).trigger('resize');
    },
    toggleFullscreenClass: function () {
        var $popup = yncphotovp.getPopup();
        if (screenfull.isFullscreen) {
            $popup.addClass('ync_photovp_fullscreen');
        } else {
            $popup.removeClass('ync_photovp_fullscreen');
        }
        $(window).trigger('resize');
        keyPressed = false;
    },
    showaddnoteOverwrite: function (img, area) {
        imgOffset = $(img).offset();
        imgWidth = $(img).width();
        form_left = parseInt(imgOffset.left) + parseInt(area.x1);
        form_width = 224;
        var $noteform = $('#noteform');
        if ((area.x1 + form_width) > imgWidth) {
            form_left = form_left - (form_width - parseInt(area.width));
            $noteform.addClass('is_right');
        } else {
            $noteform.removeClass('is_right');
        }
        if (imgWidth <= 224) {
            form_left = parseInt(imgOffset.left);
            form_width = imgWidth;
        }
        form_top = parseInt(imgOffset.top) + parseInt(area.y1) + parseInt(area.height) + 5;
        $noteform.css({left: form_left + 'px', top: form_top + 'px', width: form_width + 'px', 'z-index': 5020});
        $noteform.show();
        $noteform.find('#NoteNote').focus();
        $('#NoteX1').val(area.x1);
        $('#NoteY1').val(area.y1);
        $('#NoteHeight').val(area.height);
        $('#NoteWidth').val(area.width);
        $('#NotePhotoWidth').val(imgWidth);
    },
    updateTagListToggle: function () {
        //check height list tag.
        if ($(this).height() > 70) {
            $(this).addClass('photovp-tag-build-toggle');
        } else {
            $(this).removeClass('photovp-tag-build-toggle');
        }
    },
    updateCommentListHeight: function () {
        var self = this;
        clearTimeout(yncTimeOut);
        yncTimeOut = setTimeout(function () {
            var $popup = yncphotovp.getPopup(),
                $infoBottom = $popup.find('.ync-photovp-block-info-bottom');
            $infoBottom.height($(self).outerHeight());
        }, 300);
    }
};

$Behavior.initPhotoPopup = function () {
    yncphotovp.init();
};