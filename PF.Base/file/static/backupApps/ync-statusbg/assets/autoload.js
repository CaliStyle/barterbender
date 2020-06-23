var yncstatusbg = {
    sListCollectionId: '#js_ync_statusbg_collection_list',
    sToggleBtn: '.js_ync_status_bg_toggle',
    sBackgroundPost: '.js_ync_textarea_background',
    sBackgroundId: '#js_ync_status_background_id',
    bHasPreview: false,
    bHasEgift: false,
    bUseAdvFeed: false,
    sInputDiv: '#js_activity_feed_form textarea[name="val[user_status]"]',
    sEditDiv: '#js_activity_feed_edit_form textarea[name="val[user_status]"]',
    sSubmitEdit: '#js_activity_feed_edit_form #activity_feed_submit',
    sEditForm: '#js_activity_feed_edit_form',
    bIsStatus: true,
    bReset: false,
    bSubmitting: false,
    bDisabling: false,
    currentSelection: null,
    initCollectionSelection: function () {
        setTimeout(function () {
            if ($(yncstatusbg.sListCollectionId).length) {
                var oToggleIcon = '<div class="ync-statusbg-toggle-holder" style="display: none;"><span class="ync-statusbg-toggle-collection ' + yncstatusbg.sToggleBtn.replace('.', '') + ' active" onclick="$(\'' + yncstatusbg.sListCollectionId + '\').toggle(400);$(\''+ yncstatusbg.sToggleBtn +'\').toggleClass(\'active\');"><i class="ico ico-color-palette"></i></span></div>';
                if (!yncstatusbg.bUseAdvFeed && !$('.ync-fbclone-feed-form').length) {
                    var oInput = $('#global_attachment_status textarea');
                    if (!oInput.closest(yncstatusbg.sBackgroundPost).length) {
                        var sInput = oInput[0],
                            oContainer = document.createElement('div'),
                            oParent = sInput.parentNode;
                        oParent.replaceChild(oContainer, sInput);
                        oContainer.appendChild(sInput);
                        $(oContainer).addClass(yncstatusbg.sBackgroundPost.replace('.','') + ' ync-statusbg-bg-container');
                        if (!$(oContainer).find(yncstatusbg.sToggleBtn).length && !$(oContainer).closest('#js_activity_feed_edit_form').length) {
                            $(oContainer).append(oToggleIcon);
                        }
                        setTimeout(function(){
                            $(oContainer).find('textarea').addClass('dont-unbind');
                            $Core.attachFunctionTagger($(oContainer).find('textarea')[0]);
                        }, 100);
                    }
                } else {
                    //Is adv.feed
                    var oHolderParent = yncstatusbg.bUseAdvFeed ? $('.ynfeed_compose_status') : $('.ync-fbclone-feed-form');
                    if (oHolderParent.length && !$(yncstatusbg.sBackgroundPost).length) {
                        oHolderParent.addClass(yncstatusbg.sBackgroundPost.replace('.','') + ' ync-statusbg-bg-container');
                        if (!oHolderParent.find(yncstatusbg.sToggleBtn).length) {
                            oHolderParent.append(oToggleIcon);
                            var oEmoji = $('.ynfeed_select_emojis');
                            if (oEmoji.length) {
                                var oParent = oEmoji[0].parentNode,
                                    oClone = oEmoji[0].cloneNode(true);
                                oParent.replaceChild($('.ync-statusbg-toggle-holder')[0], oEmoji[0]);
                                $('.ync-statusbg-toggle-holder')[0].appendChild(oEmoji[0]);
                                $(oEmoji).addClass('ync-status-bg-emoji');
                                oParent.appendChild(oClone);
                                $(oClone).addClass('ync-status-bg-original-emoji').removeClass('ynfeed_select_emojis').hide();
                            }
                        }
                    }
                }
                $('.js_ync_textarea_background').unbind('append').bind('append', function(event){
                   var obj = event.target;
                   if ($(obj).hasClass('js_preview_link_attachment_custom_form') && $(obj).html().length) {
                       yncstatusbg.bDisabling = true;
                       yncstatusbg.disableBackground();
                       yncstatusbg.bHasPreview = true;

                   }
                });
                $(document).on('change','.ynfeed_extra_preview', function(){
                    var bShow = false;
                    $(this).find('span').each(function(){
                        if ($(this).html().length) {
                            bShow = true;
                        }
                    });
                    if (!bShow) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
                if ($(yncstatusbg.sInputDiv).closest('.activity-feed-status-form-active').length) {
                    yncstatusbg.showCollections();
                }
                $(document).off('DOMSubtreeModified').on('keyup change paste DOMSubtreeModified', yncstatusbg.sEditDiv,  function() {
                    var _this = this;
                    setTimeout(function(){
                        yncstatusbg.handlePaste(_this, true);
                    },100);
                });
                $(document).on('DOMSubtreeModified', '.ynfeed_extra_preview', function(){
                    if($('#ynfeed_extra_preview_feeling').is(':empty') && $('#ynfeed_extra_preview_tagged').is(':empty') && $('#ynfeed_extra_preview_checkin').is(':empty') && $('#ynfeed_extra_preview_business').is(':empty')){
                        if($('.ync-statusbg-bg-container').length){
                            $(this).addClass('empty-info');
                        }else{
                            $(this).removeClass('empty-info');
                        }

                    }else{
                        $(this).removeClass('empty-info');
                    }
                });

                $(document).on('DOMSubtreeModified', '.activity_feed_form .js_tagged_review', function(){
                    if($(this).html().length) {
                        $('#js_location_feedback').addClass('has-tagged');
                    } else {
                        $('#js_location_feedback').removeClass('has-tagged');
                    }
                });

                $(document).on('click','a.ynfeed_btn_delete_link', function(){
                    yncstatusbg.bHasPreview = false;
                    yncstatusbg.handlePaste($(yncstatusbg.sInputDiv)[0]);
                });

                $(document).on('mousedown', yncstatusbg.sSubmitEdit,  function() {
                    if (yncstatusbg.bSubmitting) return true;
                    yncstatusbg.bSubmitting = true;
                    var oForm = $(this).closest('form'),
                        oStatusText = oForm.find('textarea[name="val[user_status]"]'),
                        oModule = oForm.find('input[name="val[callback_module]"]'),
                        oTypeId = oForm.find('input[name="val[type_id]"]'),
                        oDisabled = oForm.find('input[name="val[disabled_status_background]"]'),
                        sAjax = oForm.find('#custom_ajax_form_submit').text();
                   if (oStatusText.length && oStatusText.val().length && (oModule.length || oTypeId.length || sAjax == 'feed.updatePost') && oDisabled.length) {
                       $(this).ajaxCall('yncstatusbg.editStatusBackground',$.param({
                           'module': oModule.val(),
                           'feed_id': oForm.find('input[name="val[feed_id]"]').val(),
                           'item_id': oForm.find('input[name="val[parent_user_id]"]').val(),
                           'is_disabled': oDisabled.val(),
                           'url_ajax': sAjax
                       }),'post',null, function(){
                           yncstatusbg.bSubmitting = false;
                           if (!yncstatusbg.bUseAdvFeed && $('.activity_feed_link_form_ajax').text().match(/addFeedComment/).length) {
                               setTimeout(function(){
                                   window.location.reload();
                               },2000);
                           }
                       })
                   }
                   return true;
                });

                $(document).on('focus click', yncstatusbg.sInputDiv , function () {
                    if (yncstatusbg.bUseAdvFeed) {
                        var selection = window.getSelection(),
                        sel = window.getSelection && window.getSelection();
                        if (sel && sel.rangeCount > 0) {
                            var range = selection.getRangeAt(0);
                            yncstatusbg.currentSelection = {"startContainer": range.startContainer, "startOffset":range.startOffset,"endContainer":range.endContainer, "endOffset":range.endOffset};
                        }
                    }
                    if (yncstatusbg.bUseAdvFeed && $('.core-egift-wrapper').length) {
                        $(this).closest('.activity_feed_form_holder').addClass('egift-focus');
                    }
                    yncstatusbg.showCollections();
                });

                $(document).on('keydown paste keyup DOMSubtreeModified', yncstatusbg.sInputDiv, function() {
                    if (yncstatusbg.bUseAdvFeed) {
                        var selection = window.getSelection(),
                            sel = window.getSelection && window.getSelection();
                        if (sel && sel.rangeCount > 0) {
                            var range = selection.getRangeAt(0);
                            yncstatusbg.currentSelection = {"startContainer": range.startContainer, "startOffset":range.startOffset,"endContainer":range.endContainer, "endOffset":range.endOffset};
                        }
                    }
                    var _this = this;
                    setTimeout(function(){
                        yncstatusbg.handlePaste(_this);
                    },100);
                });

                $('.js_ync_statusbg_header_nav .item-next').off('click').on('click', function () {
                    var parent_statusbg = $(this).closest('.ync-statusbg-collection-header'),
                        statusbg_active = parent_statusbg.find('li.active');
                    if (statusbg_active.is(':last-of-type')) {
                        return false;
                    } else {
                        var oNext = statusbg_active.next();
                        oNext.addClass('active');
                        $('.ync-statusbg-collection-content .tab-pane').removeClass('active');
                        $(oNext.find('a').attr('href')).addClass('active');
                        oNext.find('a').trigger('click');
                        if(oNext.is(':last-of-type')){
                            $(this).addClass('disabled');
                            $('.js_ync_statusbg_header_nav .item-prev').removeClass('disabled');
                        }
                        statusbg_active.removeClass('active');
                    }
                });
                $('.js_ync_statusbg_header_nav .item-prev').off('click').on('click', function () {
                    var parent_statusbg = $(this).closest('.ync-statusbg-collection-header'),
                        statusbg_active = parent_statusbg.find('li.active');
                    if (statusbg_active.is(':first-of-type')) {
                        return false;
                    } else {
                        var oPrev = statusbg_active.prev();
                        oPrev.addClass('active');
                        $('.ync-statusbg-collection-content .tab-pane').removeClass('active');
                        $(oPrev.find('a').attr('href')).addClass('active');
                        oPrev.find('a').trigger('click');
                        if(oPrev.is(':first-of-type')){
                            $(this).addClass('disabled');
                            $('.js_ync_statusbg_header_nav .item-next').removeClass('disabled');
                        }
                        statusbg_active.removeClass('active');
                    }
                });
                if ($('#js_activity_feed_form #js_core_egift_preview').length) {
                    $(document).on('DOMSubtreeModified','#js_activity_feed_form #js_core_egift_preview', function(){
                        setTimeout(function(){
                            if ($('#js_activity_feed_form #js_core_egift_id').val() > 0) {
                                yncstatusbg.bDisabling = true;
                                yncstatusbg.disableBackground();
                                yncstatusbg.bHasEgift = true;
                            } else {
                                yncstatusbg.bHasEgift = false;
                                yncstatusbg.handlePaste($(yncstatusbg.sInputDiv)[0]);
                            }
                        },100);
                    });
                }
            }
        }, 500);
    },
    showFullCollection: function (ele) {
        var oEle = $(ele),
            iCollectionId = oEle.data('collection_id');
        if (!iCollectionId) {
            return false;
        }
        $('.js_ync_bg_hide_' + iCollectionId).removeClass('hide');
        oEle.hide();
        return true;
    },
    selectBackground: function (ele) {
        var currentSelect = yncstatusbg.currentSelection;
        var pointer = $(yncstatusbg.sInputDiv).prop('selectionEnd');
        var oEle = $(ele),
            iBgId = oEle.data('background_id'),
            sImage = oEle.data('image_url').replace('_48','_1024').replace('-sm','-min') || '',
            oContainer = oEle.closest('#js_activity_feed_form').find(yncstatusbg.sBackgroundPost),
            iCurrentBg = parseInt($(yncstatusbg.sBackgroundId).val());
        if (!oContainer.length) return false;
        $('.ync-statusbg-collection-listing').find('.collection-item').removeClass('active');
        oEle.addClass('active');
        if (iBgId == 0) {
            oContainer.removeClass('has-background').removeAttr('style');
        } else {
            oContainer.addClass('has-background').css('background-image', 'url(' + sImage + ')');
        }
        yncstatusbg.resizeTextarea(false, iCurrentBg, iBgId);
        $(yncstatusbg.sBackgroundId).val(iBgId);
        $(yncstatusbg.sInputDiv).focus();
        if (yncstatusbg.bUseAdvFeed) {
            yncstatusbg.placeCaretAtPosition($(yncstatusbg.sInputDiv)[0], currentSelect);
        } else {
            $(yncstatusbg.sInputDiv)[0].setSelectionRange(pointer, pointer);
        }
        return true;
    },
    showCollections: function () {
        if (!yncstatusbg.bIsStatus || $(yncstatusbg.sInputDiv).closest('#js_activity_feed_edit_form').length || $(yncstatusbg.sInputDiv).closest('#ynfeed_form_edit').length) return false;
        if ($(yncstatusbg.sListCollectionId).length) {
            if (yncstatusbg.bReset) {
                $(yncstatusbg.sToggleBtn).addClass('active');
                yncstatusbg.bReset = false;
            }
            if ($(yncstatusbg.sToggleBtn + ':not(.force-hide)').hasClass('active')) {
                $(yncstatusbg.sListCollectionId + ':not(.force-hide)').show(400);
            }
            $(yncstatusbg.sToggleBtn + ':not(.force-hide)').parent().show().css('display', 'flex');
        }
    },
    handlePaste: function (oObj, bEdit) {
        if (!$(yncstatusbg.sListCollectionId).length || !yncstatusbg.bIsStatus) return false;
        var value = yncstatusbg.bUseAdvFeed ? oObj.innerHTML : $(oObj).val(),
            regrex_mention_1 = /\[user=(\d+)\](.+?)\[\/user\]/g,
            value_actual = value.replace(regrex_mention_1,'$2'),
            break_line = value_actual.match(/\n/g) || [],
            break_line_1 = [],
            bPass = true;
        if (yncstatusbg.bUseAdvFeed) {
            var regrex_mention_2 = /(?:<span id="generated" class="generatedMention")(?:[^<>]*)(?:data-type="([\w]+)")(?:[^<>]*)(?:data-id="([\d]+)")(?:[^<>]*)>([a-zA-Z0-9\s-!$%^&*()_+|~=`{}\[\]:";'<>?,.#@\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)(?:<\/span>)&nbsp;/g,
                regrex_mention_3 = /(?:<span id="generated" class="generatedMention")(?:[^<>]*)(?:data-type="([\w]+)")(?:[^<>]*)(?:data-id="([\d]+)")(?:[^<>]*)>([a-zA-Z0-9\s-!$%^&*()_+|~=`{}\[\]:";'<>?,.#@\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)(?:<\/span>)/g,
                regrex_hashtag = /(?:<span class="ynfeed_hashtag")(?:[^<>]*)>([a-zA-Z0-9\s-!$%^&*()_+|~=`{}\[\]:";'<>?,.#@\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)(?:<\/span>)/g,
                regrex_emoji = /<img([\w\W]+?)class="ynfeed_content_emoji"[\/]?>/g,
                regrex_breakline = /(?:<div)(?:[^<>]*)>([a-zA-Z0-9\s-!$%^&*()_+|~=`{}\[\]:";'<>?,.#@\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)(?:<\/div>)/g;
            value_actual = value_actual.replace(regrex_emoji, '@');
            value_actual = value_actual.replace(regrex_mention_2, '$3 ');
            value_actual = value_actual.replace(regrex_mention_3, '$3 ');
            value_actual = value_actual.replace(regrex_hashtag, '$1');
            break_line_1 = value_actual.match(regrex_breakline) || [];
            value_actual = value_actual.replace(regrex_breakline,'$1' + '@');
            value_actual = value_actual.replace(/<br>/g,'');
            if (typeof yncstatusbg_emoji_regex != 'undefined') {
                for (var key in yncstatusbg_emoji_regex) {
                    value_actual = value_actual.replace(yncstatusbg_emoji_regex[key], '@');
                }
            }
        }
        // console.log(value_actual, value_actual.length, (break_line.length + break_line_1.length));
        //Check input length
        if (value_actual.length > 150 || (break_line.length + break_line_1.length) > 3 || (yncstatusbg.bHasPreview && yncstatusbg.bUseAdvFeed) || ($('.js_preview_link_attachment_custom_form').length && $('.js_preview_link_attachment_custom_form').html().length) || yncstatusbg.bHasEgift) {
            if (yncstatusbg.bDisabling) return false;
            yncstatusbg.bDisabling = true;
            bPass = false;
            if (bEdit) {
                if (!$('#js_ync_statusbg_check_edit').length) {
                    $(yncstatusbg.sEditForm).append('<input type="hidden" id="js_ync_statusbg_check_edit" name="val[disabled_status_background]" value="1"/>');
                } else {
                    $(yncstatusbg.sEditForm).find('#js_ync_statusbg_check_edit').val(1);
                }
            } else {
                yncstatusbg.disableBackground();
            }
        } else {
            if (!yncstatusbg.bDisabling) return false;
            yncstatusbg.bDisabling = false;
            bPass = true;
            if (bEdit) {
                if (!$('#js_ync_statusbg_check_edit').length) {
                    $(yncstatusbg.sEditForm).append('<input type="hidden" id="js_ync_statusbg_check_edit" name="val[disabled_status_background]" value="0"/>');
                } else {
                    $(yncstatusbg.sEditForm).find('#js_ync_statusbg_check_edit').val(0);
                }
            } else {
                $(yncstatusbg.sBackgroundPost).addClass('ync-statusbg-bg-container');
                if ($('.ync-status-bg-original-emoji').length) {
                    
                    if($('#ynfeed_extra_preview_feeling').is(':empty') && $('#ynfeed_extra_preview_tagged').is(':empty') && $('#ynfeed_extra_preview_checkin').is(':empty') && $('#ynfeed_extra_preview_business').is(':empty')){
					  	$('.ynfeed_extra_preview').addClass('empty-info');
					}
                    $('.ync-status-bg-original-emoji').hide().removeClass('ynfeed_select_emojis');
                    $('.ync-status-bg-emoji').show();
                }
                if ((!$('.js_preview_link_attachment_custom_form').length || !$('.js_preview_link_attachment_custom_form:first').html().length) && !yncstatusbg.bReset) {
                    $(yncstatusbg.sToggleBtn).removeClass('force-hide').addClass('active').parent().show().css('display', 'flex');
                    $(yncstatusbg.sListCollectionId).removeClass('force-hide').show();
                    $('.js_ync_switch_collection_li.active > a').trigger('click');
                    var sHref = $('.js_ync_switch_collection_li.active').find('.js_ync_switch_collection').attr('href'),
                        oSelected = $(sHref).find('.collection-item.active');
                    yncstatusbg.selectBackground(oSelected[0]);
                }
            }
        }
        setTimeout(function(){
             yncstatusbg.checkStatusFormoversize();
        },100);
        
        return bPass;
    },
    resizeTextarea: function (bForce, iCurrentBg, iBgId) {
        if (yncstatusbg.bUseAdvFeed || $('#js_activity_feed_edit_form').length) return false;
        if (cacheShadownInfo !== false && shadow !== null) {
            shadow.css('word-break', 'break-word');
        }
        if (iCurrentBg == 0 || (iCurrentBg > 0 && iBgId == 0) || bForce) {
            setTimeout(function () {
                var oInput = $(yncstatusbg.sInputDiv);
                if (cacheShadownInfo !== false && shadow !== null) {
                    shadow.css('font-size', oInput.css('font-size'));
                    shadow.css('line-height', oInput.css('line-height'));
                    shadow.css('width', oInput.width());
                    // shadow.css('min-height', (parseFloat(oInput.css('line-height').replace('px','')) + parseFloat(oInput.css('padding-top').replace('px','')) + parseFloat(oInput.css('padding-bottom').replace('px',''))) + 'px');
                }
                $Core.resizeTextarea(oInput);
            }, 100);
        }
    },
    disableBackground: function () {
        $(yncstatusbg.sToggleBtn).addClass('force-hide').removeClass('active').parent().hide();
        $(yncstatusbg.sListCollectionId).addClass('force-hide').hide();
        $('#js_activity_feed_form').find(yncstatusbg.sBackgroundPost).removeClass('has-background').removeAttr('style');
        if ($(yncstatusbg.sBackgroundId).val()) {
            setTimeout(function(){
                yncstatusbg.resizeTextarea(true);
            },50);
        }
        $(yncstatusbg.sBackgroundId).val(0);
        $(yncstatusbg.sBackgroundPost).removeClass('ync-statusbg-bg-container');
        if ($('.ync-status-bg-original-emoji').length) {
            
            $('.ynfeed_extra_preview').removeClass('empty-info');
            $('.ync-status-bg-original-emoji').addClass('ynfeed_select_emojis').show();
            $('.ync-status-bg-emoji').hide();
        }
    },
    appendCollectionList: function (sHtml) {
       if (!$(yncstatusbg.sListCollectionId).length) {
           if (yncstatusbg.bUseAdvFeed) {
               $('#js_activity_feed_form .activity_feed_form_button .ynfeed_extra_preview').after(sHtml);
               setTimeout(function(){
                   $(yncstatusbg.sListCollectionId).addClass('ync-statusbg-collectionlist-advfeed');
               },100);
           } else {
            $('#js_activity_feed_form .activity_feed_form_button').before(sHtml);
           }
       }
    },
    checkStatusoversize: function(){
        var compare_height=parseFloat($('.ync-statusbg-feed').width()) * 0.5625 ;
        $('.ync-statusbg-feed:not(.statusbg-built)').each(function(){
          if ($(this).length > 0) {
            if($(this).find('.activity_feed_content_status').outerHeight() > compare_height){
                $(this).addClass('statusbg-bigsize');
            }else{
                $(this).removeClass('statusbg-bigsize');
            }
          }
          $(this).find('.activity_feed_content_status').css('opacity','1');
          $(this).addClass('statusbg-built');
        });
    },
    checkStatusFormoversize: function(){
        var height_bg= $('.ync-statusbg-bg-container.has-background textarea').outerHeight();
        var height_bg_advfeed= $('.ync-statusbg-bg-container.has-background .contenteditable').outerHeight();
        if($('.ynfeed_compose_status.has-background').length > 0){
            if( height_bg_advfeed >= $('.ync-statusbg-bg-container.has-background').outerHeight()){
                $('.ync-statusbg-bg-container.has-background').css('min-height', height_bg_advfeed);
            }else{
                $('.ync-statusbg-bg-container.has-background').css('min-height', 'auto');
            }
        }else{
            if( height_bg >= $('.ync-statusbg-bg-container.has-background').outerHeight()){
                $('.ync-statusbg-bg-container.has-background').css('min-height', height_bg);
            }else{
                $('.ync-statusbg-bg-container.has-background').css('min-height', 'auto');
            }
        }
    },
    placeCaretAtPosition: function(ele, currentSelection) {
        ele.focus();
        if (currentSelection.startOffset > 150) return false;
        if (typeof window.getSelection != "undefined"
            && typeof document.createRange != "undefined") {
            var range = document.createRange();
            range.selectNodeContents(ele);
            range.collapse(true);
            range.setStart(currentSelection.startContainer, currentSelection.startOffset);
            range.setEnd(currentSelection.endContainer, currentSelection.endOffset);
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        } else if (typeof document.body.createTextRange != "undefined") {
            var textRange = document.body.createTextRange();
            textRange.moveToElementText(ele);
            textRange.collapse(false);
            textRange.select();
        }
        yncstatusbg.currentSelection = currentSelection;
    }
};

//check when content status overlap size 16/9
$(window).resize(function() { 
    $('.ync-statusbg-feed').removeClass('statusbg-built');
    yncstatusbg.checkStatusoversize();
    yncstatusbg.checkStatusFormoversize();
    yncstatusbg.resizeTextarea(true);
});

yncstatusbg.checkStatusoversize();

PF.event.on('on_page_change_end', function() {
    yncstatusbg.checkStatusoversize();
});

$Ready(function () {
    yncstatusbg.checkStatusoversize();
	if($('.ync-statusbg-bg-container').length){
		if($('#ynfeed_extra_preview_feeling').is(':empty') && $('#ynfeed_extra_preview_tagged').is(':empty') && $('#ynfeed_extra_preview_checkin').is(':empty') && $('#ynfeed_extra_preview_business').is(':empty')){
		  	$('.ynfeed_extra_preview').addClass('empty-info');
		}
	}
    if ($('.ynfeed_activity_feed_form').length) {
        yncstatusbg.bUseAdvFeed = true;
        yncstatusbg.sInputDiv = '.ynfeed_activity_feed_form .ynfeed_compose_status .contenteditable';
        yncstatusbg.sEditDiv = '.ynfeed_form_edit .ynfeed_compose_status .contenteditable';
        yncstatusbg.sSubmitEdit = '.ynfeed_form_edit #js_activity_feed_form #activity_feed_submit';
        yncstatusbg.sEditForm = '.ynfeed_form_edit #js_activity_feed_form';
    }
    //Change to our function
    if ($('.activity_feed_link_form_ajax').length) {
        if ($('.activity_feed_link_form_ajax').text() == 'user.updateStatus') {
            $('.activity_feed_link_form_ajax').text('yncstatusbg.updateStatus');
        } else if ($('.activity_feed_link_form_ajax').text() == 'feed.addComment') {
            $('.activity_feed_link_form_ajax').text('yncstatusbg.addComment');
        } else if ($('.activity_feed_link_form_ajax').text() == 'ynfeed.addComment') {
            $('.activity_feed_link_form_ajax').text('yncstatusbg.addCommentWithAdvFeed');
        }
    }
    if ($('#global_attachment_status').length && !$(yncstatusbg.sListCollectionId).length) {
        $('#global_attachment_status').ajaxCall('yncstatusbg.loadCollectionsList', '', 'post', null, function () {
            yncstatusbg.initCollectionSelection();
        });
    }
    if (!yncstatusbg.bReset) {
        yncstatusbg.initCollectionSelection();
    }
    $('.activity_feed_form_attach li a').click(function () {
        if ($(this).attr('rel') != 'global_attachment_status') {
            yncstatusbg.bIsStatus = false;
            $(yncstatusbg.sBackgroundPost).removeClass('has-background').removeAttr('style');
            $(yncstatusbg.sListCollectionId).hide();
            $(yncstatusbg.sToggleBtn).parent().hide();
            $(yncstatusbg.sBackgroundId).val(0);
        } else {
            yncstatusbg.bIsStatus = true;
            if (yncstatusbg.bDisabling) {
                return true;
            }
            if ($(yncstatusbg.sToggleBtn).hasClass('active')) {
                $(yncstatusbg.sListCollectionId + ':not(.force-hide)').show();
            }
            $(yncstatusbg.sToggleBtn + ':not(.force-hide)').parent().show().css('display', 'flex');
            var sHref = $('.js_ync_switch_collection_li.active').find('.js_ync_switch_collection').attr('href'),
                oSelected = $(sHref).find('.collection-item.active');
            yncstatusbg.selectBackground(oSelected[0]);
        }
    });
    //Reset status form
    $ActivityFeedCompleted.ync_background = function(){
        $('.ync-statusbg-collection-listing:first').find('.collection-item:first').trigger('click');
        $(yncstatusbg.sBackgroundId).val(0);
        $(yncstatusbg.sBackgroundPost).addClass('ync-statusbg-bg-container').removeClass('has-background').removeAttr('style');
        $(yncstatusbg.sToggleBtn).removeClass('active').removeClass('force-hide').parent().show();
        $(yncstatusbg.sListCollectionId).removeClass('force-hide').hide();
        $(yncstatusbg.sInputDiv).closest('.activity-feed-status-form-active').removeClass('activity-feed-status-form-active');
        yncstatusbg.bReset = true;
        yncstatusbg.bIsStatus = true;
        yncstatusbg.bHasPreview = false;
        yncstatusbg.bHasEgift = false;
        yncstatusbg.bDisabling = false;
    };
});

(function($) {
    var origAppend = $.fn.append;
    $.fn.append = function () {
        return origAppend.apply(this, arguments).trigger("append");
    };
})(jQuery);

