$.fn.fullTagger = function() {
    var caretPos, afterChar;
    return this.each(function() {
        removeGenerated();
        $(this).prop('contenteditable', true);
        $(this).on('keyup keydown mouseup', function(e) {
            $(this).focus();
            caretPos = getCaretCharacterOffsetWithin(this);
            var key = e.keyCode || e.charCode || e.which;
            var typedText = $(this).text();
            if (key !== 40 && key !== 38) {
                // check for @mention
                if ($(this).text().indexOf('@') >= 0) {
                    // get after triggerChar
                    var iSymbolAt = typedText.substr(0,caretPos).lastIndexOf('@');
                    afterChar = typedText.substr(iSymbolAt, caretPos - iSymbolAt);
                    // replace triggerChar with emptySpace
                    afterChar = afterChar.replace('@', '');
                    // check if text is not empty
                    if (afterChar.length > 0 && afterChar !== '') {
                        generateAutocomplete($(this), afterChar);
                    }
                }
            }
            /**
             * Arrow Down
             */
            if (key == 40) {

                if ($(this).parent().find('.ynfeed_autocomplete').is(':visible')) { // check if autocomplete is visible
                    // activate the first element
                    $(this).blur();
                    $(this).parent().find('.ynfeed_autocomplete').attr("tabindex", -1).focus();
                    if (!$(this).parent().find('.ynfeed_autocomplete').find('.ynfeed_mention_item[tabindex=-1]')[0]) {
                        $(this).parent().find('.ynfeed_autocomplete').find('.ynfeed_mention_item').first().attr("tabindex", -1).focus(); // set focus to the first element
                    }
                }
            }

            /**
             * Backspace
             */
            if (key == 8) {
                var value = $(this).html().replace('<br>', '');
                var lastchar = value.substring(value.length - 1);
                var checkDiv = value.substring(value.length - 2);

                if (lastchar === '@' || lastchar === '#') { // if triggerChar removed
                    $(this).parent().find('.ynfeed_autocomplete').hide(); // hide autocomplete container

                } else if (checkDiv === 'n>') {


                    $(this).find('[id=generated]').last().remove();
                    placeCaretAtEnd($(this)[0]);

                } else if ($(this).html() === '') {
                    $(this).parent().find('.ynfeed_autocomplete').hide();
                }
            }

            // hashtag highlighter
            drawHighlighter(this);
            $Core.ynfeed.updateFormValue($(this).closest('form'));

        }).bind('paste', function (e) {
            var clipboardData, pastedData;
            // Stop data actually being pasted into div
            e.stopPropagation();
            e.preventDefault();
            
            // Get pasted data via clipboard API
            if (window.clipboardData && window.clipboardData.getData) { // IE
                text = window.clipboardData.getData('Text');
                sel = window.getSelection();
                if (sel.getRangeAt && sel.rangeCount) {
                    range = sel.getRangeAt(0);
                    range.deleteContents();

                    // Range.createContextualFragment() would be useful here but is
                    // only relatively recently standardized and is not supported in
                    // some browsers (IE9, for one)
                    var el = document.createElement("div");
                    el.innerHTML = text;
                    var frag = document.createDocumentFragment(), node, lastNode;
                    while ( (node = el.firstChild) ) {
                        lastNode = frag.appendChild(node);
                    }
                    range.insertNode(frag);

                    // Preserve the selection
                    if (lastNode) {
                        range = range.cloneRange();
                        range.setStartAfter(lastNode);
                        range.collapse(true);
                        sel.removeAllRanges();
                        sel.addRange(range);
                    }
                }
            }
            else if (e.originalEvent.clipboardData && e.originalEvent.clipboardData.getData) { // other browsers
                text = e.originalEvent.clipboardData.getData('text/plain');
                document.execCommand("insertHTML", false, text);
            }
            var that = this;
            setTimeout(function () {
                handlePasteInFeed(that);
            }, 0);

        }).bind('focus', function() {
            var t = $(this);
            if (t.hasClass('_is_set')) {
                return;
            }
            t.addClass('_is_set');
            $(this).addClass('focus');
            if ($(this).closest('#ynfeed_form_share_holder').length) {
                $('body').addClass('ynfeed-form-focus');
                $('.activity_feed_form:not(.ynfeed_detached) .activity_feed_form_button').show();
                $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_select_emojis').show();
                $('.activity_feed_form_button_status_info textarea').addClass('focus');
            }
        });
        $('.ynfeed_compose_status [class=ynfeed_autocomplete]').off('keydown').on('keydown', function(e) {
            var key = e.keyCode || e.charCode || e.which;
            if (key == 40) {
                e.preventDefault();
                if (!$(this).find('.ynfeed_mention_item[tabindex=-1]')[0]) { // check if there is focus on any tag
                    $(this).removeAttr('tabindex'); // remove the container focus
                    $(this).find('.ynfeed_mention_item').first().attr("tabindex", -1).focus(); // set focus to the first element
                } else {
                    // set focus to the next item
                    if($(this).find('.ynfeed_mention_item[tabindex=-1]').next().next().length) {
                        $(this).find('.ynfeed_mention_item[tabindex=-1]').removeAttr('tabindex').next().next().attr("tabindex", -1).focus();
                    }
                }
            } if (key == 39) {
                e.preventDefault();
                if (!$(this).find('.ynfeed_mention_item[tabindex=-1]')[0]) { // check if there is focus on any tag
                    $(this).removeAttr('tabindex'); // remove the container focus
                    $(this).find('.ynfeed_mention_item').first().attr("tabindex", -1).focus(); // set focus to the first element
                } else {
                    // set focus to the next item
                    if($(this).find('.ynfeed_mention_item[tabindex=-1]').next().length) {
                        $(this).find('.ynfeed_mention_item[tabindex=-1]').removeAttr('tabindex').next().attr("tabindex", -1).focus();
                    } else {
                        $(this).find('.ynfeed_mention_item[tabindex=-1]').removeAttr('tabindex');
                        $(this).find('.ynfeed_mention_item').first().attr("tabindex", -1).focus();
                    }
                }
            }
            else if (key === 38) {
                e.preventDefault();
                if($(this).find('.ynfeed_mention_item[tabindex=-1]').prev().prev().length) {
                    $(this).find('.ynfeed_mention_item[tabindex=-1]').removeAttr('tabindex').prev().prev().attr("tabindex", -1).focus();
                }
            } else if (key === 37) {
                e.preventDefault();
                if($(this).find('.ynfeed_mention_item[tabindex=-1]').prev().length) {
                    $(this).find('.ynfeed_mention_item[tabindex=-1]').removeAttr('tabindex').prev().attr("tabindex", -1).focus();
                }else{
                    $(this).find('.ynfeed_mention_item[tabindex=-1]').removeAttr('tabindex');
                    $(this).find('.ynfeed_mention_item').last().attr("tabindex", -1).focus();
                }
            }
        });

        $(document).off('click', '.ynfeed_mention_item').on('click', '.ynfeed_mention_item', function() {
            selectElement($(this));
        });


        /**
         * On ENTER (selecting the tag) , fires the click trigger
         */
        $('.ynfeed_compose_status [class=ynfeed_autocomplete]').keypress(function(e) {
            var key = e.keyCode || e.charCode || e.which;
            if (key == 13) {
                e.preventDefault();
                selectElement($(this).find('.ynfeed_mention_item[tabindex=-1]'));
            }
        });
    });

    function selectElement(item) {
        var selectedTagText = item.data("text");
        var selectedTagId = item.data("id");
        var selectedTagType = item.data("type");
        var $container = item.parent();
        var replacedTag, beforeCaret;
        var enteredContent = $container.parent().find('.contenteditable').html(); // current HTML
        var enteredContentText = $container.parent().find('.contenteditable').text(); // current HTML
        if (enteredContent.indexOf('@') >= 0) {
            // get after triggerChar
            beforeCaret = enteredContentText.substr(0, caretPos);
            replacedTag = beforeCaret.substr(beforeCaret.lastIndexOf('@'));
            var generatedHTML = '<span id="generated" class="generatedMention" contenteditable="false" data-type="' + selectedTagType + '" data-id="' + selectedTagId +'">' + selectedTagText + '</span> ';
        }
        var newContent = enteredContent.replace(replacedTag, generatedHTML);
        $container.parent().find('.contenteditable').html(newContent);
        placeCaretAtEnd($container.parent().find('.contenteditable')[0]);
        $container.hide();
        drawHighlighter($container.parent().find('.contenteditable'));
        $Core.ynfeed.updateFormValue($container.closest('form'));

    }

    function drawHighlighter(elem) {
        var highlighterHTML = $(elem).html();
        if (!highlighterHTML.match(/#(([a-zA-Z0-9]+)|([\u0600-\u06FF]+))#/g)) { //arabic support
            highlighterHTML = highlighterHTML.replace(/#(([a-zA-Z0-9]+)|([\u0600-\u06FF]+))/g, '<span class="ynfeed_hashtag">#$1</span>');
        } else {
            highlighterHTML = highlighterHTML.replace(/#(([a-zA-Z0-9]+)|([\u0600-\u06FF]+))#(([a-zA-Z0-9]+)|([\u0600-\u06FF]+))/g, '<span class="ynfeed_hashtag">#$1</span>');
        }
        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_highlighter').html(highlighterHTML);
    }

    /**
     * check for auto generated font > span (contenteditable)
     */
    function removeGenerated() {
        if ($(this).find('font')[0] || $(this).find('span')[0]) {

            var currentText = $(this).find('font').find('span').text();
            $(this).find('font').remove();
            $(this).find('span').remove();
            $(this).html($(this).html() + currentText);
        }
    }

    function generateAutocomplete($editableContainer, sNameToFind) {
        var autocompleteDIV = $editableContainer.parent().find('.ynfeed_autocomplete');
        $('.ynfeed_autocomplete').hide();
        // show autocomplete container
        // insert search results into the container
        var aFoundFriends = [],
            generatedHTML = '',
            iLimit = 10;
        for (var i in $Cache.mentions) {
            if ($Cache.mentions[i]['full_name'].toLowerCase().indexOf(sNameToFind.toLowerCase()) >= 0) {
                aFoundFriends.push({
                    user_id: $Cache.mentions[i]['user_id'],
                    full_name: $Cache.mentions[i]['full_name'],
                    user_image: $Cache.mentions[i]['user_image']
                });
                if (($Cache.mentions[i]['user_image'].substr(0, 5) == 'http:') || ($Cache.mentions[i]['user_image'].substr(0, 6) == 'https:')) {
                    PF.event.trigger('urer_image_url', $Cache.mentions[i]);
                    $Cache.mentions[i]['user_image'] = '<img src="' + $Cache.mentions[i]['user_image'] + '" class="_image_32 image_deferred">';
                }
                var iId = $Cache.mentions[i]['user_id'], sType = 'user';
                if ($Cache.mentions[i]['is_page']) {
                    sType = ($Cache.mentions[i]['page_type'] == 1) ? 'group' : 'page';
                } else if($Cache.mentions[i]['is_car']) {
                    sType = 'car';
                    iId = $Cache.mentions[i]['business_id'];
                }
                generatedHTML += '<div class="ynfeed_mention_item tagFriendChooser" data-id="' + iId
                    +'" data-type="' + sType
                    + '" data-text="' + $Cache.mentions[i]['full_name'] + '"><div class="tagFriendChooserImage">'
                    + $Cache.mentions[i]['user_image']
                    + '</div><span>'
                    + ((sType == 'car') ? '<i class="ico ico-car ynfeed-tag-icon"></i>' : '')
                    + $Cache.mentions[i]['full_name']
                    +'</span></div>';
            }
            if (aFoundFriends.length >= iLimit) {
                break;
            }
        }

        autocompleteDIV.html(generatedHTML);
        $(autocompleteDIV).find('a').prop('href', 'javascript:void(0);');
        if(generatedHTML)
            autocompleteDIV.show();

    }

    /**
     * Place caret after the selected item
     *
     * @param {type} el
     */
    function placeCaretAtEnd(el) {
        el.focus();
        if (typeof window.getSelection != "undefined"
            && typeof document.createRange != "undefined") {
            var range = document.createRange();
            range.selectNodeContents(el);
            range.collapse(false);
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        } else if (typeof document.body.createTextRange != "undefined") {
            var textRange = document.body.createTextRange();
            textRange.moveToElementText(el);
            textRange.collapse(false);
            textRange.select();
        }
    }
    function handlePasteInFeed(oObj) {
        if ($(oObj).closest('#js_activity_feed_edit_form').length > 0) {
            return false;
        }

        if (postingFeedUrl) {
            return;
        }
        var value = oObj.innerHTML;
        value = value.replace(/<img[>]?.*[\/]?>/g, '@');
        // var value = oObj.innerText;
        var regrex = /(http[s]?:\/\/(www\.)?|ftp:\/\/(www\.)?|www\.){1}([0-9A-Za-z-\-\.@:%_\+~#=]+)+((\.[a-zA-Z])*)(\/([0-9A-Za-z-\-\.@:%_\+~#=\?])*)*/g;
        var match = value.match(regrex);
        if (!empty(match)) {
            setTimeout(function () {
                var newValue = oObj.innerHTML;
                newValue = newValue.replace(/<img[>]?.*[\/]?>/g, '@');
                var newMatch = newValue.match(regrex);
                if (empty(newMatch) || (match[0] != newMatch[0])) return;
                bCheckUrlCheck = true;
                postingFeedUrl = true;

                $('#activity_feed_submit').attr("disabled", "disabled");

                $('.activity_feed_form_share_process').show();
                $(oObj).parent().append('<div id="js_preview_link_attachment_custom_form_sub" class="js_preview_link_attachment_custom_form" style="margin-top:5px;"></div>');
                $Core.ajax('link.preview', {
                    type: 'POST',
                    params: {
                        'no_page_update': '1',
                        value: newMatch[0]
                    },
                    success: function ($sOutput) {
                        postingFeedUrl = false;
                        $('.activity_feed_form_share_process').hide();
                        if (substr($sOutput, 0, 1) == '{') {


                        }
                        else {
                            $('#js_global_attach_value').val($(oObj).val());
                            bCheckUrlForceAdd = true;
                            /* bCheckUrlCheck = false; */
                            $sOutput = '<a href="javascript:void(0)" class="ynfeed_btn_delete_link pull-right"><i class="ico ico-close" aria-hidden="true"></i></a>' + $sOutput;
                            $('#js_preview_link_attachment_custom_form_sub').html($sOutput);
                            $('a.ynfeed_btn_delete_link').off('click').on('click', function () {
                                $('#js_global_attach_value').val('');
                                bCheckUrlForceAdd = false;
                                $('#js_preview_link_attachment_custom_form_sub').html('');
                            });
                        }
                    }
                });
            }, 500);
        }
        else {
            $('#js_global_attach_value').val('');
            bCheckUrlForceAdd = false;
            $('#js_preview_link_attachment_custom_form_sub').html('');
        }
        $Core.ynfeed.updateFormValue($(oObj).closest('form'));
    }
    function getCaretCharacterOffsetWithin(element) {
        var caretOffset = 0;
        var doc = element.ownerDocument || element.document;
        var win = doc.defaultView || doc.parentWindow;
        var sel;
        if (typeof win.getSelection != "undefined") {
            sel = win.getSelection();
            if (sel.rangeCount > 0) {
                var range = win.getSelection().getRangeAt(0);
                var preCaretRange = range.cloneRange();
                preCaretRange.selectNodeContents(element);
                preCaretRange.setEnd(range.endContainer, range.endOffset);
                caretOffset = preCaretRange.toString().length;
            }
        } else if ((sel = doc.selection) && sel.type != "Control") {
            var textRange = sel.createRange();
            var preCaretTextRange = doc.body.createTextRange();
            preCaretTextRange.moveToElementText(element);
            preCaretTextRange.setEndPoint("EndToEnd", textRange);
            caretOffset = preCaretTextRange.text.length;
        }
        return caretOffset;
    }

};
