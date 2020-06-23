var isAddingComment = false;
var $oLastFormSubmit = null;
var ynccommentbuildingCache = false;
var ynccomment = {
    cacheShadownInfo: false,
    shadow: null,
    lastClickObj: null,
    initGif: false,
    selectingSticker: false,
    bIsMobile: false,
    resizeTextarea: function (oObj) {
        if (!oObj.length) return;
        if (this.cacheShadownInfo === false) {
            this.cacheShadownInfo = true;
            this.shadow = $('<div></div>').css(
                {
                    position: 'absolute',
                    top: -10000,
                    left: -10000,
                    wordWrap: 'break-word',
                    width: oObj.width(),
                    fontSize: oObj.css('fontSize'),
                    fontFamily: oObj.css('fontFamily'),
                    lineHeight: oObj.css('lineHeight'),
                    resize: 'none'
                }).appendTo(document.body);
        }

        var val = oObj.val().replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/&/g, '&amp;')
            .replace(/\n/g, '<br/>');
        var has_br_end = false;
        if(val.endsWith("<br/>")) {
            has_br_end = true;
        }
        this.shadow.html(val);

        var height = this.shadow.height();
        var parent = oObj.closest('.item-box-input');
        var lineHeight = parseInt(oObj.css('lineHeight'));
        var paddingTop = parseInt(oObj.css('paddingTop'));
        if(has_br_end) {
            height += lineHeight;
        }
        if (height > lineHeight) {
            if (!parent.hasClass('box-full')) {
                parent.addClass('box-full');
                this.shadow.css('width', oObj.width());
                height = this.shadow.height();
                if(has_br_end) {
                    height += lineHeight;
                }
            }
        }
        else if (parent.hasClass('box-full')) {
            this.shadow.css('width', oObj.width() - (ynccomment.bIsMobile ? parent.find('.mobile-sent-btn').innerWidth() : parent.find('.ync-comment-group-icon').innerWidth()));
            if (this.shadow.height() <= lineHeight) {
                parent.removeClass('box-full');
            }
            this.shadow.css('width', oObj.width());
        }

        // plus comment box height with padding top + padding bottom
        height += (paddingTop * 2);
        oObj.css('height', height);
    },
    checkstickerbtn: function(parent,sticker_rtl){
        var position_sticker= parent.find('.item-container');
        var position_sticker_change=parent.find('.ync-comment-full-sticker'),
            number_sticker = position_sticker_change.find('.item-header-sticker').length,
            width_sticker =  position_sticker_change.find('.item-header-sticker').width(),
            width_full_Sticker = number_sticker*width_sticker - 2;
        var offset_sticker= position_sticker.offset();
        var offset_sticker_change= position_sticker_change.offset();
        if(sticker_rtl=='right'){
            if(offset_sticker.left <= offset_sticker_change.left){
                parent.find('.ync-comment-next-sticker').css('display','none');
            }else{
                parent.find('.ync-comment-next-sticker').css('display','flex');
            }
            if((offset_sticker.left + position_sticker.outerWidth() ) < (offset_sticker_change.left + width_full_Sticker)){
                parent.find('.ync-comment-prev-sticker').css('display','flex');
                parent.find('.item-recent').css('display','none');
            }
             if((offset_sticker.left + position_sticker.outerWidth() ) == (offset_sticker_change.left + width_full_Sticker)){
                parent.find('.ync-comment-prev-sticker').css('display','none');
                parent.find('.item-recent').css('display','flex');
                parent.find('.ync-comment-next-sticker').css('display','flex');
            }
        }else{
            if(offset_sticker.left > offset_sticker_change.left){
                parent.find('.ync-comment-prev-sticker').css('display','flex');
                parent.find('.item-recent').css('display','none');
            }
            if((offset_sticker.left + position_sticker.outerWidth() ) >= (offset_sticker_change.left + width_full_Sticker)){
                parent.find('.ync-comment-next-sticker').css('display','none');
            }else{
                parent.find('.ync-comment-next-sticker').css('display','flex');
            }
             if(offset_sticker.left == offset_sticker_change.left){
                parent.find('.ync-comment-prev-sticker').css('display','none');
                parent.find('.item-recent').css('display','flex');
                parent.find('.ync-comment-next-sticker').css('display','flex');
            }
        }
        
    },
    commentFeedTextareaClick : function($oObj) {
        $($oObj).addClass('dont-unbind');
        $($oObj).blur(function() {
            $(this).removeClass('dont-unbind');
        });
        $($oObj).keydown(function(e) {
            if (isAddingComment) {
                return false;
            }
            if (e.keyCode == 13 && !e.shiftKey && !ynccomment.bIsMobile) {
                setTimeout(function() {
                    $('.chooseFriend').remove();
                }, 100);
                e.preventDefault();
                $($oObj).parents('form:first').trigger('submit');
                $($oObj).removeClass('dont-unbind');
                if (window.grecaptcha) {
                    grecaptcha.reset();
                }
                // $($oObj).unbind();
                $Core.loadInit();
                isAddingComment = true;
                return false;
            }
        }).keyup(function(){
            var oFeedFooter = $(this).closest('.js_feed_comment_form_holder');
            if (ynccomment.bIsMobile && oFeedFooter.length && $(this).val() != '') {
                oFeedFooter.find('.mobile-sent-btn:not(.has-attach)').addClass('active');
                oFeedFooter.find('.mobile-sent-btn').addClass('has-text');
            } else {
                oFeedFooter.find('.mobile-sent-btn:not(.has-attach)').removeClass('active').removeClass('has-text');
                oFeedFooter.find('.mobile-sent-btn').removeClass('has-text');
            }
            ynccomment.resizeTextarea($(this));
        }).on('paste',function(){
            ynccomment.resizeTextarea($(this));
        });

        $($oObj).addClass('js_comment_feed_textarea_focus').addClass('is_focus');
        $($oObj).parents('.comment_mini').find('.feed_comment_buttons_wrap:first').show();

        $($oObj).parent().parent().find('.comment_mini_textarea_holder:first').addClass('comment_mini_content');
    },
    deleteAttachment: function(ele, id, type, isEdit) {
        if (!id) {
            return false;
        }
        ele.closest('.has-photo-sticker').removeClass('has-photo-sticker');
        if (isEdit) {
            ele.closest('.js_adv_comment_feed_form').find('.js_feed_comment_attach_change').val(1);
        }
        if (ynccomment.bIsMobile && ele.closest('.js_feed_comment_form_holder').length) {
            ele.closest('.js_feed_comment_form_holder').find('.mobile-sent-btn:not(.has-text)').removeClass('active').removeClass('has-attach');
            ele.closest('.js_feed_comment_form_holder').find('.mobile-sent-btn').removeClass('has-attach');
        }
        if (type == 'photo') {
            ele.closest('.js_adv_comment_feed_form').find('.js_feed_comment_photo_id').val(0);
            ele.closest('.js_ync_comment_attach_preview').remove();
            if (!isEdit) {
                $.ajaxCall('ynccomment.deleteTempFile', 'id=' + id, 'post');
            }
        } else if (type == 'sticker') {
            ele.closest('.js_adv_comment_feed_form').find('.js_feed_comment_sticker_id').val(0);
            ele.closest('.js_ync_comment_attach_preview').remove();
        }

        return false;
    },
    resetCommentForm: function(feed_id) {
        var input_ele = $('#js_item_feed_' + feed_id).find('.js_feed_comment_form_holder .js_ync_comment_feed_textarea'),
            feed_form = input_ele.closest('.js_adv_comment_feed_form');
        input_ele.siblings('.mobile-sent-btn').removeClass('active').removeClass('has-text').removeClass('has-attach');
        input_ele.val('').addClass('js_comment_feed_textarea_focus').removeAttr('style');
        ynccomment.resizeTextarea(input_ele);
        feed_form.find('.js_ync_comment_box').removeClass('has-photo-sticker');
        feed_form.find('.js_feed_comment_photo_id').val(0);
        feed_form.find('.js_feed_comment_sticker_id').val(0);
        feed_form.find('.js_ync_comment_attach_emoticon').removeClass('open');
        feed_form.closest('.js_feed_comment_form_holder').find('.js_ync_comment_emoticon_container').hide();
        feed_form.find('.js_ync_comment_attach_preview').remove();
    },
    hideEmoticon: function(ele, isReply) {
        var parent = ele.closest('.js_ync_comment_emoticon_container');
        parent.fadeOut();
        if (isReply) {
            parent.closest('.js_comment_form_holder').find('.js_ync_comment_attach_emoticon').removeClass('open');
        } else {
            parent.closest('.js_feed_comment_form_holder').find('.js_ync_comment_attach_emoticon').removeClass('open');
        }
    },
    showEmojiTitle: function(ele, code) {
        var parent = ele.closest('.js_ync_comment_emoticon_container');
        parent.find('.js_hover_emoticon_info').html(code);
    },
    selectEmoji: function(ele, code, isReply, isEdit) {
        if (isEdit) {
            var oForm = ele.closest('.js_ync_comment_emoticon_container').closest('.js_ync_edit_comment_holder'),
                myField = oForm.find('.js_ync_comment_textarea_edit');
        } else {
            if (isReply) {
                var oForm = ele.closest('.js_comment_form_holder'),
                    myField = oForm.find('.js_ync_comment_feed_textarea');
            } else {
                var oForm = ele.closest('.js_feed_comment_form_holder'),
                    myField = oForm.find('.js_ync_comment_feed_textarea');
            }
        }
        var sValue = '' + code + '';
        if (myField.prop('selectionStart') || myField.prop('selectionStart') == '0')
        {
            var startPos = myField.prop('selectionStart'),
            endPos = myField.prop('selectionEnd'),
                offset = myField.val().substring(0, startPos).length
                    + sValue.length;
            myField.val(myField.val().substring(0, startPos)
                + sValue
                + myField.val().substring(endPos, myField.val().length));
            myField.focus();
            myField[0].setSelectionRange(offset,offset);
        }
        else
        {
            myField.val(myField.val() + sValue);
            myField.focus();
            var offset_end = myField.val().length*2;
            myField[0].setSelectionRange(offset_end,offset_end);
        }
        ynccomment.resizeTextarea(myField);
        return true;
    },
    hideComment: function(ele, bUnHide) {
        var obj = $(ele);
        $.ajaxCall('ynccomment.hideComment',$.param({
            id: obj.data('comment-id'),
            owner_id: obj.data('owner-id'),
            parent_id: obj.data('parent-id'),
            un_hide: bUnHide
        }),'post');
        return false;
    },
    showHiddenComments: function(ele) {
        var obj = $(ele),
            ids = obj.data('hidden-ids'),
            oIds = isNaN(ids) ? ids.split(',') : '';
        if (oIds.length) {
            oIds.forEach(function(e) {
               $('#js_comment_' + e).removeClass('hide');
            });
        } else if (ids > 0) {
            $('#js_comment_' + ids).removeClass('hide');
        }
        obj.closest('.js_hidden_comment_dot').remove();
        $('#js_global_tooltip').remove();
        ynccomment.initCanvasForSticker('.ync_comment_gif:not(.ync_built)');
        ynccomment.hideLineThreeDot();
        return false;
    },
    selectSticker: function(ele, id) {
        if (ynccomment.selectingSticker) {
            return false;
        }
        ynccomment.selectingSticker = true;
        var obj = $(ele),
            oInput = obj.closest('.js_adv_comment_feed_form').find('.js_ync_comment_feed_textarea:first');
        if (oInput.length && oInput.val() == '') {
            obj.closest('.js_comment_group_icon').removeClass('open').find('.js_ync_comment_attach_sticker').removeClass('open-list');
            obj.closest('.js_adv_comment_feed_form').find('.js_feed_comment_sticker_id').val(id);
            obj.closest('.js_adv_comment_feed_form').trigger('submit');
            ynccomment.selectingSticker = false;
            return false;
        }
        $('.js_ync_comment_attach_sticker').removeClass('open-list');
        $('.js_comment_group_icon').removeClass('open');
        obj.ajaxCall('ynccomment.appendPreviewSticker',$.param({
            feed_id: obj.data('feed-id'),
            parent_id: obj.data('parent-id'),
            edit_id: obj.data('edit-id'),
            sticker_id: id
        }),'post', null, function(){
            setTimeout(function(){
                ynccomment.selectingSticker = false;
            },500);
        });
        return false;
    },
    updateMyStickerSet: function(ele, id, is_add) {
        var obj = $(ele);
        obj.addClass('disabled');
        obj.ajaxCall('ynccomment.updateMyStickerSet',$.param({
            id: id,
            is_add: is_add,
            feed_id: obj.data('feed-id'),
            parent_id: obj.data('parent-id'),
            edit_id: obj.data('edit-id'),
        }),'post',null,function(e,self) {
            self.removeClass('disabled');
        });
    },
    updateLayoutMyStickerSets: function(id, is_add) {
        var total_ele = $('.js_ync_my_sticker_set_total'),
            total = total_ele.data('total'),
            remain = !is_add ? parseInt(total) - 1 : parseInt(total) + 1,
            remove_ele = $('.js_ync_remove_sticker_set_' + id),
            add_ele = $('.js_ync_add_sticker_set_' + id);
        if (!is_add) {
            if (remain <= 0) {
                total_ele.html('(0)').data('total', 0);
                $('#ync_comment_sticker_my').find('.js_ync_comment_none_sticker_set').show();
            } else {
                total_ele.html('(' + remain + ')').data('total',remain);
            }
            //Remove set layout
            add_ele.addClass('btn').show();
            remove_ele.removeClass('btn').hide();
            $('.js_ync_my_sticker_set_'+id).remove();
        } else {
            add_ele.removeClass('btn').hide();
            remove_ele.addClass('btn').show();
            $('#ync_comment_sticker_my').find('.js_ync_comment_none_sticker_set').hide();
            total_ele.html('(' + remain + ')').data('total',remain);
        }

        return true;
    },
    removePreviewStickerSet: function (ele) {
        var oPopup = $(ele).closest('.js_box');
        oPopup.find('.js_ync_sticker_sets_holder').removeClass('hide');
        oPopup.find('.js_ync_preview_sticker_set_holder').empty().addClass('hide');
        $(ele).remove();
        return false;
    },
    unsetAllEditComment: function(id) {
        if (typeof id != 'undefined') {
            var ele = $('#js_comment_'+id);
            ele.removeClass('ync-comment-item-edit');
            ele.find('.item-comment-options').removeClass('hide');
            ele.find('.js_ync_comment_text_holder').show();
            ele.find('.comment_mini_action').show();
            ele.find('.js_ync_edit_comment_holder').remove();
            return false;
        }
        $('.js_mini_feed_comment').each(function(){
            var ele = $(this);
            ele.removeClass('ync-comment-item-edit');
            ele.find('.item-comment-options').removeClass('hide');
            ele.find('.js_ync_comment_text_holder').show();
            ele.find('.comment_mini_action').show();
            ele.find('.js_ync_edit_comment_holder').remove();
        });
        return false;
    },
    attachFunctionTagger: function(sSelector) {
        if ($(sSelector).length && !ynccommentbuildingCache &&
            (typeof $Cache == 'undefined' || typeof $Cache.ynccomment_mentions == 'undefined')) {
            ynccommentbuildingCache = true;
            $.ajaxCall('ynccomment.buildMentionCache', '', 'post');
        }

        var customSelector = function() {
            return '_' + Math.random().toString(36).substr(2, 9);
        };
        var increment = 0;
        $(sSelector).each(function() {
            increment++;
            var t = $(this), selector = '_custom_' + customSelector() + '_' + increment;
            if (t.data('selector')) {
                t.removeClass(t.data('selector').replace('.', ''));
            }
            t.addClass(selector);
            t.data('selector', '.' + selector);
        });

        $(sSelector).keyup(function() {
            var t = $(this);
            var sInput = t.val();
            var iInputLength = sInput.length;
            var iAtSymbol = sInput.lastIndexOf('@');
            if (sInput == '@' || empty(sInput) || iAtSymbol < 0 ||
                iAtSymbol == (iInputLength - 1)) {
                $($(this).data('selector')).
                siblings('.chooseFriend').
                hide(function() {$(this).remove();});
                return;
            }

            var sNameToFind = sInput.substring(iAtSymbol + 1, $(this).prop('selectionEnd'));
            if (sNameToFind == '') {
                return;
            }
            /* loop through friends */
            var aFoundFriends = [], sOut = '';
            for (var i in $Cache.ynccomment_mentions) {
                if ($Cache.ynccomment_mentions[i]['full_name'].toLowerCase().
                    indexOf(sNameToFind.toLowerCase()) >= 0) {
                    var sNewInputStart = sInput.substr(0, iAtSymbol).
                    replace(/\'/g, '&#39;').
                    replace(/\"/g, '&#34;'),
                        sNewInputEnd = sInput.substr(iAtSymbol + 1 + sNameToFind.length, iInputLength).
                    replace(/\'/g, '&#39;').
                    replace(/\"/g, '&#34;');
                    sValueStart = sNewInputStart;
                    sValueEnd = sNewInputEnd;
                    aFoundFriends.push({
                        user_id: $Cache.ynccomment_mentions[i]['user_id'],
                        full_name: $Cache.ynccomment_mentions[i]['full_name'],
                        user_image: $Cache.ynccomment_mentions[i]['user_image'],
                    });
                    if (($Cache.ynccomment_mentions[i]['user_image'].substr(0, 5) == 'http:') || ($Cache.ynccomment_mentions[i]['user_image'].substr(0, 6) == 'https:')) {
                        PF.event.trigger('urer_image_url', $Cache.ynccomment_mentions[i]);
                        $Cache.ynccomment_mentions[i]['user_image'] = '<img src="' + $Cache.ynccomment_mentions[i]['user_image'] + '" class="_image_32 image_deferred">';
                    }
                    var sBBTag = $Cache.ynccomment_mentions[i]['is_page'] ? ($Cache.ynccomment_mentions[i]['page_type'] == 1 ? 'group' : 'page') : 'user',
                        iId = sBBTag == 'user' ? $Cache.ynccomment_mentions[i]['user_id'] : $Cache.ynccomment_mentions[i]['profile_page_id'];
                    var sExtra = '';
                    if (typeof $Cache.ynccomment_mentions[i]['is_you'] != 'undefined' && $Cache.ynccomment_mentions[i]['is_you']) {
                        sExtra = ' (' + ynccomment_phrases['you'] + ')';
                    }
                    var sItemType = '';
                    if (typeof $Cache.ynccomment_mentions[i]['is_page'] && $Cache.ynccomment_mentions[i]['is_page']) {
                        if ($Cache.ynccomment_mentions[i]['page_type'] == 1) {
                            sItemType = '<span class="item-type">'+ ynccomment_phrases['groups'] +'</span>';
                        } else {
                            sItemType = '<span class="item-type">' + ynccomment_phrases['pages'] + '</span>';
                        }
                    }
                    sOut += '<div class="tagFriendChooser" onclick="$(\'' +
                        $(this).data('selector') +
                        '\').val(sValueStart + \'\' + (false ? \'@' +
                        $Cache.ynccomment_mentions[i]['user_name'] + '\' : \'['+ sBBTag +'=' +
                        iId + ']' +
                        $Cache.ynccomment_mentions[i]['full_name'].replace(/\&#039;/g, '\\\'') +
                        '[/' + sBBTag + ']\') + sValueEnd + \' \').putCursorAtEnd();$(\'' +
                        $(this).data('selector') +
                        '\').siblings(\'.chooseFriend\').remove();"><div class="tagFriendChooserImage">' +
                        $Cache.ynccomment_mentions[i]['user_image'] + '</div><div class="tagFriendChooserInfo"><span class="item-name">' +
                        (($Cache.ynccomment_mentions[i]['full_name'].length > 25)
                            ? ($Cache.ynccomment_mentions[i]['full_name'].substr(0, 25) + '...')
                            : $Cache.ynccomment_mentions[i]['full_name']) + sExtra + '</span>' + sItemType + '</div></div>';
                    /* just delete the fancy choose your friend and recreate it */
                    sOut = sOut.replace('\n', '').replace('\r', '');
                }
            }

            $($(this).data('selector')).siblings('.chooseFriend').remove();
            if (!empty(sOut)) {
                $($(this).data('selector')).
                after('<div class="chooseFriend" style="width: ' +
                    $(this).parent().width() + 'px;">' + sOut + '</div>');
                $('.chooseFriend').mCustomScrollbar({
                    theme: "minimal-dark",
                }).addClass('dont-unbind-children');
            }
        });
    },
    appendActionAfterEdit: function(id, remove) {
        if (remove) {
            $('#js_remove_preview_action_' + id).remove();
        } else if (!$('#js_remove_preview_action_' + id).length) {
            $('#js_comment_action_' + id).find('.action-list .item-reply').after(
                '<span class="item-remove-preview" id="js_remove_preview_action_' + id + '">' +
                '<a href="#" onclick="$.ajaxCall(\'ynccomment.removePreview\',\'id=' + id + '\',\'post\'); return false;" class="comment-remove">' + ynccomment_phrases['remove_preview'] + '</a>' +
                '</span>');
        }
        if (!$('#js_view_edit_history_action_' + id).length) {
            $('#js_comment_action_' + id).find('.action-list .item-time').after(
                '<span class="item-history" id="js_view_edit_history_action_' + id + '">' +
                '<a href="#" title="'+ ynccomment_phrases['show_edit_history'] + '" class="view-edit-history" onclick="tb_show(\''+ ynccomment_phrases['edit_history'] +'\', $.ajaxBox(\'ynccomment.showEditHistory\', \'id=' + id + '&height=400&width=600\')); return false;" >' + ynccomment_phrases['edited'] + '</a>' +
                '</span>');
        }
        return false;
    },
    initFocusTextarea: function(ele) {
        var oGrand = $(ele).closest('.js_mini_feed_comment'),
            oParent = $(ele).closest('.js_ync_edit_comment_holder');
        if (!oGrand.hasClass('mobile-style')) {
            oParent.find('.js_ync_focus_edit_comment').removeClass('hide');
            oParent.find('.js_ync_not_focus_edit_comment').addClass('hide');
        }
        if (typeof ynccomment.shadow !== 'undefined' && ynccomment.shadow) {
            ynccomment.shadow.css('width', $(ele).width());
            ynccomment.shadow.css('fontSize', $(ele).css('fontSize'));
            ynccomment.shadow.css('fontFamily', $(ele).css('fontFamily'));
            ynccomment.shadow.css('lineHeight', $(ele).css('lineHeight'));
        }
        ynccomment.resizeTextarea($('.js_ync_comment_textarea_edit'));
        ynccomment.commentFeedTextareaClick(ele);
    },
    initCanvasForSticker: function (sEle) {
        $(sEle).each(function(){
            var ele = this;
            if (/.*\.gif/.test($(ele).attr('src'))) {
                var canvas = document.createElement('canvas'),
                    context = canvas.getContext("2d"),
                    width = $(ele).width(),
                    height = $(ele).height();
                if (!width) {
                    return false;
                }
                canvas.className = "ync_comment_canvas_gif";
                canvas.height = height;
                canvas.width = width;
                var image = new Image();
                image.onload = function() {
                    var steps = Math.min(Math.floor(image.width / width), 4);
                    if(steps > 1) {
                        for(var step = 1; step < steps - 1; step ++) {
                            image = ynccomment.scaleIt(image, (1 - 1/steps));
                        }
                    }
                    context.drawImage(image, 0, 0, width, height);
                };
                image.src = $(ele).attr('src');
                canvas.onclick = function(){
                    var _e = $(this),
                        _ele = $(ele);
                    _e.hide();
                    _ele.show();
                    setTimeout(function(){
                        _e.show();
                        _ele.hide();
                    },5000);
                };
                canvas.onmouseover = function(){
                    var _e = $(this),
                        _ele = $(ele);
                    _e.hide();
                    _ele.show();
                    setTimeout(function(){
                        _e.show();
                        _ele.hide();
                    },5000);
                };
                $(ele).hide();
                $(ele).parent().append(canvas);
                $(ele).addClass('ync_built');
            }
        });
    },
    scaleIt: function (source, scaleFactor) {
        var c = document.createElement('canvas');
        var ctx = c.getContext('2d');
        var w= source.width * scaleFactor;
        var h= source.height * scaleFactor;
        c.width = w;
        c.height = h;
        ctx.drawImage(source,0,0,w,h);
        return(c);
    },
    initStickerAttachBar: function (ele) {
        var parent_sticker = ele.closest('.ync-comment-group-icon'),
            number_sticker = parent_sticker.find('.ync-comment-full-sticker >.item-header-sticker ').length,
            width_sticker = parent_sticker.find('.ync-comment-full-sticker .item-header-sticker').outerWidth(),
            width_full_Sticker = number_sticker*width_sticker - 2,
            width_container_sticker=parent_sticker.find('.header-sticker-list .item-container').outerWidth(),
            number_sticker_show=parseInt(width_container_sticker/width_sticker),
            sticker_next_max = (number_sticker - number_sticker_show)*width_sticker,
            sticker_rtl = 'left';
        if ($("html").attr("dir") == "rtl") {
            sticker_rtl = 'right';
        }
        if(parent_sticker.find('.ync-comment-full-sticker').length){
            if(width_full_Sticker < width_container_sticker){
                parent_sticker.find('.ync-comment-next-sticker').css('display','none');
            }
        }else{
            parent_sticker.find('.ync-comment-next-sticker').css('display','none');
        }
        parent_sticker.find('.ync-comment-next-sticker').off('click').on( "click", function() {
            var oStickerIcon = parent_sticker.find('.icon-sticker');
            var sticker_next = oStickerIcon.data('sticker_next');
            if(sticker_next >= sticker_next_max){
                return;
            }
            var parent = $(this).closest('.header-sticker-list');
            if(sticker_rtl=='right'){
                parent.find('.ync-comment-full-sticker').css({left:left}).animate({"right":"-" + (sticker_next + width_sticker) + "px"}, 300, function(){
                    oStickerIcon.data('sticker_next', sticker_next + width_sticker);
                    ynccomment.checkstickerbtn(parent,sticker_rtl);
                });
            }else{
                parent.find('.ync-comment-full-sticker').css({left:left}).animate({"left":"-" + (sticker_next + width_sticker) + "px"}, 300, function(){
                    oStickerIcon.data('sticker_next', sticker_next + width_sticker);
                    ynccomment.checkstickerbtn(parent,sticker_rtl);
                });
            }

        });
        parent_sticker.find('.ync-comment-prev-sticker' ).off('click').on( "click", function() {
            var oStickerIcon = parent_sticker.find('.icon-sticker');
            var sticker_next = oStickerIcon.data('sticker_next');
            var parent = $(this).closest('.header-sticker-list');
            if(sticker_rtl=='right'){
                parent.find('.ync-comment-full-sticker').css({left:left}).animate({"right":"-" + (sticker_next - width_sticker) + "px"}, 300, function(){
                    oStickerIcon.data('sticker_next', sticker_next - width_sticker);
                    ynccomment.checkstickerbtn(parent,sticker_rtl);
                });
            }else{
                parent.find('.ync-comment-full-sticker').css({left:left}).animate({"left":"-" + (sticker_next - width_sticker) + "px"}, 300, function(){
                    oStickerIcon.data('sticker_next', sticker_next - width_sticker);
                    ynccomment.checkstickerbtn(parent,sticker_rtl);
                });
            }


        });
    },
    updateCommentCounter: function(module_id, item_id, str) {
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
        var sPagerId = $('#js_feed_comment_pager_' + module_id + item_id).find('.item-number');
        if (sPagerId.length) {
            var sText = sPagerId.html(),
                oText = sText.split('/');
            var iShown = str == '+' ? parseInt(oText[0]) + 1 : parseInt(oText[0]) - 1,
                iTotal = str == '+' ? parseInt(oText[1]) + 1 : parseInt(oText[1]) - 1;
            sPagerId.html(iShown + '/' + iTotal);
        }
    },
    updateReplyCounter: function(id, str, value, no_total) {
        if (!id) {
            return;
        }
        if (!value) {
            value = 1;
        }
        var sPagerId = $('.js_ync_replies_viewmore_' + id).find('.item-number');
        if (sPagerId.length) {
            sPagerId.each(function(){
                var sText = $(this).html(),
                    oText = sText.split('/');
                var iShown = str == '+' ? parseInt(oText[0]) + value : parseInt(oText[0]) - value;
                if (!no_total) {
                    var iTotal = str == '+' ? parseInt(oText[1]) + value : parseInt(oText[1]) - value;
                } else {
                    var iTotal = parseInt(oText[1]);
                }
                $(this).html(iShown + '/' + iTotal);
            });
        }
    },
    hideLoadedReplies: function(ele, id) {
        var oHolder = $('.js_ync_comment_view_more_replies_' + id),
            oHiddenSpan = oHolder.find('.js_link_href'),
            sHref = oHiddenSpan.data('href'),
            oComment = $('#js_comment_'+id),
            iAdded = oComment.find('.is_added_more').length;
        oComment.find('.reply_is_loadmore').remove();
        if (iAdded) {
            ynccomment.updateReplyCounter(id, '-', iAdded, true);
            var  oMore = $('.js_ync_comment_view_more_replies_'+id).find('.js_ync_comment_number:first');
            if (oMore.length) {
                oMore.html(parseInt(oMore.html()) + iAdded);
            }
        }
        var sContent = oHiddenSpan.html();
        $(ele).parent().remove();
        if (!oComment.find('.comment_mini_child_content .js_mini_feed_comment').length) {
            oComment.removeClass('has-replies');
        };
        sHref = sHref.replace(/&max-time=[0-9]{10}/,'&max-time=' + (new Date).getTime().toString().substr(0,10));
        oHiddenSpan.data('href',sHref);

        oHolder.prepend('<a href="' + sHref + '" class="item-viewmore ajax" onclick="$(this).addClass(\'active\');">' + (sContent != '' ?   sContent : '') + '</a>');
        oHolder.siblings('.ync-comment-viewmore').remove();
        oHolder.addClass('ync-comment-viewmore').show();
        $Core.loadInit();
        return false;
    },
    hideLineThreeDot: function() {
        $('.comment_mini_child_holder').find('.js_mini_feed_comment').removeClass('css-hide-line');
        $('.js_hidden_comment_dot').each(function(){
            var oEle = $(this),
                oNext = oEle.next(),
                bMid = false;
            oEle.removeClass('css-hide-line');
            if (oNext.is(':last-child') && oNext.hasClass('js_mini_feed_comment')) {
                oEle.addClass('css-hide-line');
            } else {
                bMid = true;
            }
            var oPrevs = oEle.prevUntil('.js_mini_feed_comment:not(.view-hidden)');
            if (!bMid) {
                if (oPrevs.length) {
                    var offset = oPrevs.length - 1;
                    $(oPrevs[offset]).prev().addClass('css-hide-line');
                } else if (oEle.prev().hasClass('js_mini_feed_comment') && !oEle.prev().hasClass('js_hidden_comment_dot')) {
                    oEle.prev().addClass('css-hide-line');
                }
            }
        });
    },
    loadStickerCollection: function(ele) {
        tb_show(ynccomment_phrases['stickers'],
            $.ajaxBox('ynccomment.loadStickerCollection',$.param({
                height: 400,
                width: 600,
                feed_id: $(ele).data('feed-id'),
                parent_id: $(ele).data('parent-id'),
                edit_id: $(ele).data('edit-id')
            }))
        ); return false;
    }
};

$Ready(function () {

    // update width of comment box when focus to input
    $('.js_ync_comment_feed_textarea').focus(function () {
        if (typeof ynccomment.shadow !== 'undefined' && ynccomment.shadow) {
            ynccomment.shadow.css('width', $(this).width());
            ynccomment.shadow.css('fontSize', $(this).css('fontSize'));
            ynccomment.shadow.css('fontFamily', $(this).css('fontFamily'));
            ynccomment.shadow.css('lineHeight', $(this).css('lineHeight'));
        }
        ynccomment.commentFeedTextareaClick(this);
    });

    $('.js_ync_comment_textarea_edit').focus(function () {
        ynccomment.initFocusTextarea(this);
    }).keyup(function (event) {
        if (event.keyCode == 27) {
            return ynccomment.unsetAllEditComment();
        }
    }).blur(function(){
        var oGrand = $(this).closest('.js_mini_feed_comment'),
            oParent = $(this).closest('.js_ync_edit_comment_holder');
        if (!oGrand.hasClass('mobile-style')) {
            setTimeout(function(){
                oParent.find('.js_ync_focus_edit_comment').addClass('hide');
                oParent.find('.js_ync_not_focus_edit_comment').removeClass('hide');
            },300);
        }
    });
    // Update height of comment box when resize window size
    $(window).resize(function () {
        $('.js_ync_comment_feed_textarea').each(function () {
            if (typeof ynccomment.shadow !== 'undefined' &&  ynccomment.shadow) {
                ynccomment.shadow.css('width', $(this).width());
            }
            ynccomment.resizeTextarea($(this));
        });
    });

    //sticker list

    $('body').off('click').on('click', function (e) {
        if (!$('.ync-comment-group-icon.dropup').is(e.target)
            && $('.ync-comment-group-icon.dropup').has(e.target).length === 0
            && $('.open').has(e.target).length === 0
            && !$('.fa-close').is(e.target)
            && !$(e.target).hasClass('ico-arrow-left')
            && !$(e.target).closest('.js_box').length
        ) {
            $('.ync-comment-group-icon.dropup').removeClass('open');
            $('.ync-comment-group-icon.dropup .icon-sticker').removeClass('open-list');
        }
    });
    $('.nav.ync-comment-sticker-header li').off('click').on('click', function (event) {
        var parent_actived_sticker = $(this).closest('.ync-comment-sticker-container');
        var actived_sticker = parent_actived_sticker.find('li.active');
        var content_sticker = $(this).find('a').attr('href');
        actived_sticker.removeClass('active');
        parent_actived_sticker.find('.tab-content.ync-comment-sticker-content > div').removeClass('active');
        parent_actived_sticker.find(content_sticker).addClass('active');
        $(this).addClass('active');
    });
    //init scroll bar
    if (!(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))) {
        //Init scrollbar
        $(".ync-comment-emoji-list").mCustomScrollbar({
            theme: "minimal-dark",
        }).addClass('dont-unbind-children');

        $(".ync-comment-sticker-list").mCustomScrollbar({
            theme: "minimal-dark",
        }).addClass('dont-unbind-children');

        $(".ync-comment-store-list, .ync-comment-store-preview-main").mCustomScrollbar({
            theme: "minimal-dark",
        }).addClass('dont-unbind-children');

        $(".ync-comment-edit-history-container").mCustomScrollbar({
            theme: "minimal-dark",
        }).addClass('dont-unbind-children');

        PF.event.on('before_cache_current_body', function () {
            $('.mCustomScrollbar').mCustomScrollbar('destroy');
        });
    }
    else {
        $('.ync-comment-item-edit').addClass('mobile-style');
        $('.ync-comment-footer').addClass('mobile-style');
        $('.ync-comment-wrapper .js_hover_title').removeClass('js_hover_title');
        $('.ync-comment-wrapper .js_hover_info').remove();
        ynccomment.bIsMobile = true;
    }

    $('.js_adv_comment_feed_form').unbind().submit(function() {
        var t = $(this);

        t.addClass('in_process');
        var edit_id = $(this).data('edit-id');
        if (typeof edit_id != 'undefined' && edit_id > 0) {
            $(this).find('.js_feed_comment_process_form:first').show();
            $(this).ajaxCall('ynccomment.updateText', null, null, null, function(e, self) {
                $(self).find('textarea').blur();
                isAddingComment = false;
                $('.js_feed_comment_process_form').fadeOut();
            });
            return false;
        }
        if ($Core.exists('#js_captcha_load_for_check')) {

            $('div#js_captcha_load_for_check').removeClass('built');

            $('#js_captcha_load_for_check').addClass('built').css({
                top: t.offset().top,
                left: '50%',
                'margin-left': '-' +
                (($('#js_captcha_load_for_check').width() / 2) + 12) + 'px',
                display: 'block',
            }).detach().appendTo('body').find('.captcha').attr('id', 'js_captcha_image').css({opacity: 0.0});

            $('#js_captcha_load_for_check.built').find('')

            $('#js_captcha_image').
            ajaxCall('captcha.reload',
                'sId=js_captcha_image&sInput=image_verification');
            $oLastFormSubmit = $(this);

            $('div#js_captcha_load_for_check:not(.built)').remove();

            return false;
        }

        if (function_exists('' + Editor.sEditor + '_wysiwyg_feed_comment_form')) {
            eval('' + Editor.sEditor + '_wysiwyg_feed_comment_form(this);');
        }

        $(this).
        parent().
        parent().
        find('.js_feed_comment_process_form:first').
        show();
        $(this).ajaxCall('ynccomment.add', null, null, null, function(e, self) {
            $(self).find('textarea').blur();
            isAddingComment = false;
            $('.js_feed_comment_process_form').fadeOut();
        });

        $(this).find('.error_message').remove();
        $(this).find('textarea:first').removeClass('dont-unbind');

        return false;
    });

    $('.js_ync_comment_feed_new_reply').off('click').on('click', function() {

        var oEle = $(this)
            .parents('#js_comment_' + $(this).attr('rel') + ':first'),
            oParent = oEle.find('.js_comment_form_holder:first'),
            oGrand = oParent.parent(),
            iOwnerId = $(this).data('owner-id'),
            iCurrentUser = $(this).data('current-user'),
            isReply = $(this).data('parent-id') > 0;
        var oEleExist = $('#js_comment_form_holder_'+$(this).attr('rel')).find('.js_ync_comment_feed_textarea:first');

        if (oEleExist.length){
            if (oEleExist.val() == '') {
                if (iOwnerId != iCurrentUser && isReply) {
                    var sOwner = $(this).closest('.js_mini_feed_comment').find('.user_profile_link_span:first > a').text();
                    oEleExist.val('[user='+iOwnerId+']'+sOwner+'[/user] ');
                }
            }
            oEleExist.focus();
            return false;
        }
        oParent.detach().appendTo(oGrand);
        if ((Editor.sEditor == 'tiny_mce' || Editor.sEditor == 'tinymce') &&
            isset(tinyMCE) && isset(tinyMCE.activeEditor)) {
            $('.js_adv_comment_feed_form').
            find('.js_feed_comment_parent_id:first').
            val($(this).attr('rel'));
            tinyMCE.activeEditor.focus();
            if (typeof($.scrollTo) == 'function') {
                $.scrollTo('.js_adv_comment_feed_form', 800);
            }
            return false;
        }

        var oCommentForm = $(this).parents('.js_feed_comment_border:first').find('.js_feed_ync_comment_form:first'),
            sCommentForm = oCommentForm.html(),
            parent_id = $(this).attr('rel');
        oParent.html(sCommentForm);
        //Is on mobile
        if (oCommentForm.closest('.js_feed_comment_form_holder').hasClass('mobile-style')) {
            oParent.addClass('mobile-style');
            oParent.find('.js_ync_comment_box').append('<div class="ync-comment-group-btn-icon"><div class="ync-comment-btn"><button class="btn btn-primary btn-xs">'+ ynccomment_phrases['submit'] +'</button></div></div>');
        }
        oParent.addClass('ync-comment-item ync-comment-item-reply ync-comment-reply-new');
        oParent.find('.js_feed_comment_parent_id:first').val(parent_id);
        ynccomment.resizeTextarea(oParent.find('.js_ync_comment_feed_textarea:first'));
        oParent.find('.js_ync_comment_feed_textarea:first').focus();
        oParent.find('.js_ync_comment_feed_textarea:first')
            .attr('placeholder', oTranslations['write_a_reply']).val('');
        ynccomment.commentFeedTextareaClick(
            oParent.find('.js_ync_comment_feed_textarea:first'));

        $('.js_feed_add_comment_button .error_message').remove();
        $('.js_ync_comment_attach_sticker').removeClass('open-list');
        $('.js_comment_group_icon').removeClass('open');
        oParent.find('.button_set_off:first').show().removeClass('button_set_off');
        if (!$(this).data('is-single')) {
            oParent.closest('.js_mini_feed_comment').addClass('has-replies');
        }
        oParent.find('.js_ync_comment_attach_photo').data('parent-id',parent_id);
        oParent.find('.js_attach_photo_input_file').data('parent-id',parent_id);
        oParent.find('.js_ync_comment_attach_emoticon').data('parent-id',parent_id).removeClass('open');
        oParent.find('.js_ync_comment_emoticon_container').remove();
        oParent.find('.js_ync_comment_attach_sticker').data('parent-id',parent_id).removeClass('open-list').addClass('js_comment_icon_sticker_parent_'+parent_id).removeClass('js_comment_icon_sticker_'+ oParent.find('.js_ync_comment_attach_sticker').data('feed-id'));
        oParent.find('.ync-comment-box').removeClass('ync-comment-box').addClass('ync-comment-box-reply').removeClass('has-photo-sticker');
        oParent.find('.js_ync_comment_attach_preview').remove();
        oParent.find('.js_ync_comment_sticker_container').remove();
        oParent.closest('.ync-comment-container-reply').find('.comment_mini_child_content').append('<div class="js_ync_comment_add_reply"></div>');
        oParent.siblings('.js_ync_comment_view_more_reply_wrapper').append('<div class="js_ync_comment_add_reply"></div>');
        if (iOwnerId != iCurrentUser && isReply) {
            var sOwnerName = $(this).closest('.js_mini_feed_comment').find('.user_profile_link_span:first > a').text();
            oParent.find('.js_ync_comment_feed_textarea:first').val('[user='+iOwnerId+']'+sOwnerName+'[/user] ');
        }
        $Core.loadInit();
        /*$Behavior.activityFeedLoader();*/

        return false;
    });

    $('.js_ync_comment_attach_photo').off('mousedown').on('mousedown',function() {
        $(this).find('.js_attach_photo_input_file:first').trigger('click');
        ynccomment.lastClickObj = $(this);
    });

    $('.js_attach_photo_input_file').off('change').on('change',function(){
       var input = $(this),
           files = input[0].files,
           feed_id = input.data('feed-id'),
           parent_id = input.data('parent-id'),
           edit_id = input.data('edit-id');
        ynccomment.selectingSticker = true;
        ynccomment.lastClickObj.addClass('loading');
       if (files.length) {
           var data = new FormData(),
               file = files[0];
           data.append('file', file);
           data.append('type', 'ynccomment_comment');
           $.ajax({
               url: PF.url.make('core/upload-temp'),
               data: data,
               cache: false,
               contentType: false,
               processData: false,
               headers: {
                   'X-File-Name': encodeURIComponent(file.name),
                   'X-File-Size': file.size,
                   'X-File-Type': file.type,
               },
               type: 'POST',
               error: function (error) {
                   tb_show(ynccomment_phrases['notice'], '', null, error);
                   if (typeof ynccomment.lastClickObj == 'object') {
                       ynccomment.lastClickObj.removeClass('loading');
                   }
                   ynccomment.selectingSticker = false;
               },
               success: function (data) {
                   var oData = JSON.parse(data);
                   input.val('');
                   if (!oData.file) {
                       if (typeof ynccomment.lastClickObj == 'object') {
                           ynccomment.lastClickObj.removeClass('loading');
                       }
                       ynccomment.selectingSticker = false;
                       if (oData.error) {
                           tb_show(ynccomment_phrases['notice'], '', null, oData.error);
                       } else {
                           tb_show(ynccomment_phrases['notice'], '', null, ynccomment_phrases['oops_something_went_wrong']);
                       }
                       return false;
                   }
                   if (parent_id) {
                       var obj_parent = $('#js_comment_form_holder_'+parent_id);
                       obj_parent.find('.js_feed_comment_photo_id').val(oData.file);
                       obj_parent.find('.js_ync_comment_attach_photo').removeClass('loading');
                       obj_parent.find('.js_comment_group_icon').removeClass('open').find('.js_ync_comment_attach_sticker').removeClass('open-list');
                       obj_parent.find('.item-edit-content').parents('.ync-comment-box-reply').addClass('has-photo-sticker');
                   } else if (feed_id) {
                       var obj_feed = $('#js_item_feed_'+feed_id);
                       obj_feed.find('.js_feed_comment_form_holder .js_feed_comment_photo_id').val(oData.file);
                       obj_feed.find('.js_ync_comment_attach_photo').removeClass('loading');
                       obj_feed.find('.js_comment_group_icon').removeClass('open').find('.js_ync_comment_attach_sticker').removeClass('open-list');
                       obj_feed.find('.js_feed_ync_comment_form .item-edit-content').parents('.ync-comment-box').addClass('has-photo-sticker');
                   } else if (edit_id) {
                       var obj_edit = $('.js_ync_comment_quick_edit_holder_'+edit_id);
                       obj_edit.find('.js_feed_comment_photo_id').val(oData.file);
                       obj_edit.find('.js_feed_comment_attach_change').val(1);
                       obj_edit.find('.js_ync_comment_attach_photo').removeClass('loading');
                       obj_edit.find('.js_comment_group_icon').removeClass('open').find('.js_ync_comment_attach_sticker').removeClass('open-list');
                       obj_edit.find('.item-edit-content').parents('.ync-comment-box-edit').addClass('has-photo-sticker');
                   } else {
                       tb_show(ynccomment_phrases['notice'], '', null, ynccomment_phrases['oops_something_went_wrong']);
                       if (typeof ynccomment.lastClickObj == 'object') {
                           ynccomment.lastClickObj.removeClass('loading');
                       }
                       ynccomment.selectingSticker = false;
                       return false;
                   }
                   $.ajaxCall('ynccomment.appendPreviewPhoto',$.param({
                       feed_id: feed_id,
                       parent_id: parent_id,
                       edit_id: edit_id,
                       id: oData.file
                   }),'post');
                   return true;
               }
           });
       }
    });

    $('.js_ync_comment_attach_sticker').off('click').on('click',function() {
        if ($(this).hasClass('loading') || ynccomment.selectingSticker) {
            return false;
        }
        var feed_id = $(this).data('feed-id'),
            parent_id = typeof $(this).data('parent-id') != 'undefined' ? $(this).data('parent-id') : 0,
            edit_id = typeof $(this).data('edit-id') != 'undefined' ? $(this).data('edit-id') : 0,
            sticker_ele = $('.js_sticker_set_' + feed_id + '_' + parent_id + '_' + edit_id + ':first'),
            icon_group = sticker_ele.closest('.js_comment_group_icon');
        if (sticker_ele.length) {
            if ($(this).hasClass('open-list')) {
                icon_group.removeClass('open');
                $(this).removeClass('open-list');
            } else {
                $('.js_ync_comment_attach_sticker').removeClass('open-list');
                $('.js_comment_group_icon').removeClass('open');
                icon_group.addClass('open');
                $(this).addClass('open-list');
            }
            return false;
        }
        $(this).addClass('loading');
        $('.js_ync_comment_attach_sticker').removeClass('open-list');
        $('.js_comment_group_icon').removeClass('open');
        $(this).ajaxCall('ynccomment.loadAttachSticker', $.param({
            feed_id: feed_id,
            parent_id: parent_id,
            edit_id: edit_id
        }),'post',null, function(e, self) {
            self.removeClass('loading');
            self.addClass('open-list');
            ynccomment.initStickerAttachBar(self);
            setTimeout(function(){
                ynccomment.initCanvasForSticker('.ync_comment_gif:not(.ync_built)');
            },100);
        });
        return false;
    });
    $('.js_ync_comment_attach_emoticon').off('click').on('click',function(){
        if ($(this).hasClass('loading')) {
            return false;
        }
        var feed_id = $(this).data('feed-id'),
            parent_id = typeof $(this).data('parent-id') != 'undefined' ? $(this).data('parent-id') : 0,
            edit_id = typeof $(this).data('edit-id') != 'undefined' ? $(this).data('edit-id') : 0,
            emoji_ele = $('.js_emoticon_container_' + feed_id + '_' + parent_id + '_' + edit_id + ':first');
        if (emoji_ele.length) {
            if ($(this).hasClass('open')) {
                emoji_ele.fadeOut();
                $(this).removeClass('open');
            } else {
                emoji_ele.fadeIn();
                $(this).addClass('open');
            }
            return false;
        }
        $(this).addClass('loading');
        $(this).ajaxCall('ynccomment.loadAttachEmoticon',$.param({
            feed_id: feed_id,
            parent_id: parent_id,
            edit_id: edit_id
        }),'post',null,function(e, self) {
            self.removeClass('loading');
            self.addClass('open');
        });
        return false;
    });
    $('.js_ync_comment_view_more_reply_holder').each(function(){
        var oEle = $(this),
            oHiddenSpan = oEle.find('.js_link_href'),
            sContent = oHiddenSpan.html(),
            sHref = oHiddenSpan.data('href');
        oEle.find('.ync-comment-viewmore').prepend('<a href="' + sHref + '" class="item-viewmore ajax" onclick="$(this).addClass(\'active\');">' + (sContent != '' ?   sContent : '') + '</a>');
        var oContent = oEle.html();
        oEle.closest('.ync-comment-container-reply').append(oContent);
        oEle.remove();
    });
    ynccomment.hideLineThreeDot();
    //Draw canvas for gif sticker
    ynccomment.initCanvasForSticker('.ync_comment_gif:not(.ync_built)');
    if (typeof $Core.dropzone.instance['photo_feed'] != 'undefined') {
        $Core.dropzone.dropzone_id = 'photo_feed';
    }
});

$Behavior.ynccommentFeedLoader = function() {
    /**
     * Click on adding a new comment link.
     */
    $('.js_feed_entry_add_comment').click(function() {
        $('.js_ync_comment_feed_textarea').each(function() {
            if ($(this).val() == $('.js_comment_feed_value').html()) {
                $(this).removeClass('js_comment_feed_textarea_focus');
                $(this).val($('.js_comment_feed_value').html());
            }

            $(this).parents('.comment_mini').find('.feed_comment_buttons_wrap').hide();
        });

        $(this).parents('.js_parent_feed_entry:first').find('.comment_mini_content_holder').show();
        $(this).parents('.js_parent_feed_entry:first').find('.feed_comment_buttons_wrap').show();

        if ($(this).parents('.js_parent_feed_entry:first').find('.js_ync_comment_feed_textarea').val() == $('.js_comment_feed_value').html()) {
            $(this).parents('.js_parent_feed_entry:first').find('.js_ync_comment_feed_textarea').val('');
        }
        $(this).parents('.js_parent_feed_entry:first').find('.js_ync_comment_feed_textarea')
            .focus().addClass('js_comment_feed_textarea_focus');
        $(this).parents('.js_parent_feed_entry:first').find('.comment_mini_textarea_holder').addClass('comment_mini_content');

        var iTotalComments = 0;
        $(this).parents('.js_parent_feed_entry:first').find('.js_mini_feed_comment').
        each(function() {
            iTotalComments++;
        });

        if (iTotalComments > 2) {
            $.scrollTo($(this).
            parents('.js_parent_feed_entry:first').
            find('.js_comment_feed_textarea_browse:first'), 340);
        }

        return false;
    });

    var selectors = '.js_ync_comment_feed_textarea, .js_comment_feed_textarea_focus';
    ynccomment.attachFunctionTagger(selectors);
    ynccomment.resizeTextarea($('.js_ync_comment_textarea_edit'));

    if (!String.prototype.endsWith) {
        String.prototype.endsWith = function(searchString, position) {
            var subjectString = this.toString();
            if (typeof position !== 'number' || !isFinite(position)
                || Math.floor(position) !== position || position > subjectString.length) {
                position = subjectString.length;
            }
            position -= searchString.length;
            var lastIndex = subjectString.indexOf(searchString, position);
            return lastIndex !== -1 && lastIndex === position;
        };
    }
};
