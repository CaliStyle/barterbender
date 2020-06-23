$.fn.feeling = function(options) {
    var typedText, defaultImg;
    return this.each(function() {
        draw();
        $(this).on('keyup', function(e) {
            typedText = $(this).val();
            var key = e.keyCode || e.charCode || e.which;
            if (key !== 40 && key !== 38) {
                if (typedText.length > 0 && typedText !== '') {
                    generateAutocomplete($(this), typedText);
                }else{

                }
            }
            /**
             * Backspace
             */
            if (key == 8 && typedText == '') {
                $(this).closest('.ynfeed_compose_tagging').find('.ynfeed_autocomplete').hide('fast');
            }

            /**
             * Arrow Down
             */
            if (key == 40) {
                var $container = $(this).closest('.ynfeed_compose_feeling');
                if ($container.find('.ynfeed_autocomplete').is(':visible')) { // check if autocomplete is visible
                    // activate the first element
                    $(this).blur();
                    $container.find('.ynfeed_autocomplete').attr("tabindex", -1).focus();
                    if (!$container.find('.ynfeed_autocomplete').find('.ynfeed_feeling_item[tabindex=-1]')[0]) {
                        $container.find('.ynfeed_autocomplete').find('.ynfeed_feeling_item').first().attr("tabindex", -1).focus(); // set focus to the first element
                    }
                }
            }
        }).on('keydown', function(e) {
            var key = e.keyCode || e.charCode || e.which;
            if(key == 8 && $(this).val() == '') {
                var taggedItem = $(this).closest('.ynfeed_compose_tagging').find('.ynfeed_tagged_items .ynfeed_tagged_item').last();
                removeFeelingItem(taggedItem);
            }
        });

        $(document).off('click', '.ynfeed_feeling_item').on('click', '.ynfeed_feeling_item', function() {
            selectElement($(this));
        });

        $(document).off('click', '.ynfeed_btn_delete_feeling').on('click', '.ynfeed_btn_delete_feeling', function() {
            removeFeelingItem($(this).parent('.ynfeed_selected_feeling'));
        });

        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_input_feeling').bind('focus, click', function () {
            generateAutocomplete($(this), $(this).val());
        });

        $('.ynfeed_compose_feeling [class=ynfeed_autocomplete]').off('keydown').on('keydown', function(e) {
            var key = e.keyCode || e.charCode || e.which;
            if (key == 40) { /*Down*/
                e.preventDefault();
                if (!$(this).find('.ynfeed_feeling_item[tabindex=-1]')[0]) { // check if there is focus on any tag
                    $(this).removeAttr('tabindex'); // remove the container focus
                    $(this).find('.ynfeed_feeling_item').first().attr("tabindex", -1).focus(); // set focus to the first element
                } else {
                    // set focus to the next item
                    if($(this).find('.ynfeed_feeling_item[tabindex=-1]').next().length) {
                        $(this).find('.ynfeed_feeling_item[tabindex=-1]').removeAttr('tabindex').next().attr("tabindex", -1).focus();
                    } else {
                        $(this).find('.ynfeed_feeling_item[tabindex=-1]').removeAttr('tabindex');
                        $(this).find('.ynfeed_feeling_item').first().attr("tabindex", -1).focus();
                    }
                }
            } else if (key === 38) { /*Up*/
                e.preventDefault();
                if($(this).find('.ynfeed_feeling_item[tabindex=-1]').prev().length) {
                    $(this).find('.ynfeed_feeling_item[tabindex=-1]').removeAttr('tabindex').prev().attr("tabindex", -1).focus();
                }else{
                    $(this).find('.ynfeed_feeling_item[tabindex=-1]').removeAttr('tabindex');
                    $(this).find('.ynfeed_feeling_item').last().attr("tabindex", -1).focus();
                }
            }
        });
        /**
         * On ENTER (selecting the tag) , fires the click trigger
         */
        $('.ynfeed_compose_feeling [class=ynfeed_autocomplete]').keypress(function(e) {
            var key = e.keyCode || e.charCode || e.which;
            if (key == 13) {
                e.preventDefault();
                selectElement($(this).find('.ynfeed_feeling_item[tabindex=-1]'));
            }
        });
    });

    function removeFeelingItem(taggedItem) {
        var id = taggedItem.data("id");
        if(typeof id == 'undefined')
            return;
        var taggedInput = $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_selected_feeling');
        var taggedValues = $.map( taggedInput.val().split(','), function(v){
            return v === "" ? null : v;
        });
        taggedValues.splice(taggedValues.indexOf(id.toString()), 1);
        taggedInput.val('').trigger('change');
        taggedItem.remove();
        previewTaggedItem(taggedValues);
    }

    function selectElement(item) {
        $('.ynfeed_compose_feeling').hide('fast');
        var selectedTagText = item.data("text");
        var selectedTagId = item.data("id");
        var $container = item.parent();
        var taggedItems = $container.parent().find('.ynfeed_tagged_items');
        var taggedInput = $container.parent().find('#ynfeed_input_selected_feeling');
        var taggedValue = selectedTagId;

        if(selectedTagId == -1) { //custom feeling
            taggedInput.val(taggedValue.toString()).trigger('change');
            taggedItems.append('<span class="ynfeed_selected_feeling" data-id="' + selectedTagId + '">' + '<img src="' + sDefaultFeelingImg + '" class="_image_32 image_deferred">' + selectedTagText + '<a href="javascript:void(0)" class="ynfeed_btn_delete_feeling"><i class="ico ico-close" aria-hidden="true"></i></a></span>');
            $container.parent().find('.ynfeed_input_feeling').val('').focus();
            $container.hide('fast');
            $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_btn_feeling').removeClass('is_active');
            $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_custom_feeling_text').val(typedText);
            $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_custom_feeling_image').val(sDefaultFeelingImg);
            previewTaggedItem(taggedValue);
        } else {
            var ofeeling = getFeelingObjByAttr('feeling_id', selectedTagId);
            taggedInput.val(taggedValue.toString()).trigger('change');
            taggedItems.append('<span class="ynfeed_selected_feeling" data-id="' + selectedTagId + '">' + ofeeling['image'] + selectedTagText + '<a href="javascript:void(0)" class="ynfeed_btn_delete_feeling"><i class="ico ico-close" aria-hidden="true"></i></a></span>');
            $container.parent().find('.ynfeed_input_feeling').val('').focus();
            $container.hide('fast');
            $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_btn_feeling').removeClass('is_active');
            $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_custom_feeling_text').val('');
            $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_custom_feeling_image').val('');
            previewTaggedItem(taggedValue);
        }

    }

    function previewTaggedItem(taggedValue) {
        taggedValue = parseInt(taggedValue);
        var ofeeling, sTagged, sImage, $ynfeed_btn_feeling = $('#ynfeed_btn_feeling');
        if(taggedValue > 0) {
            ofeeling = getFeelingObjByAttr('feeling_id', taggedValue);
            sImage = '<img src="' + ofeeling['image_url'] + '" class="_image_32 image_deferred" onclick="$Core.ynfeed.selectCustomFeelingImage(this);">';
            sTagged = sImage + '<a href="javascript:void(0);" onclick="$(\'#ynfeed_btn_feeling\').trigger(\'click\')">' + ofeeling['title_translated'] + '</a>';
            $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_input_feeling').hide('fast');
            $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_extra_preview_feeling').html(oTranslations['feeling_something'].replace('{something}', sTagged)).show();
            $ynfeed_btn_feeling.addClass('has_data');
        }else if(taggedValue == -1) {
            sImage = '<img src="' + sDefaultFeelingImg + '" class="_image_32 image_deferred" onclick="$Core.ynfeed.selectCustomFeelingImage(this);">';
            sTagged = sImage + '<a href="javascript:void(0);" onclick="$(\'#ynfeed_btn_feeling\').trigger(\'click\')">' + typedText + '</a>';
            $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_input_feeling').hide('fast');
            $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_extra_preview_feeling').html(oTranslations['feeling_something'].replace('{something}', sTagged)).show();
            $ynfeed_btn_feeling.addClass('has_data');
        }else{
            $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_extra_preview_feeling').html('');
            $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_input_feeling').show();
            $ynfeed_btn_feeling.removeClass('has_data');
        }
    }

    function getFeelingObjByAttr(att, value){
        for (var i in $Cache.feelings) {
            if($Cache.feelings[i][att] == value)
                return $Cache.feelings[i];
        }
        return null;
    }
    function generateAutocomplete($editableContainer, sNameToFind) {
        var autocompleteDIV = $editableContainer.closest('.ynfeed_compose_feeling').find('.ynfeed_autocomplete');
        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_autocomplete').hide();
        // show autocomplete container
        // insert search results into the container
        var taggedInput = $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_selected_feeling');
        var taggedValue = parseInt(taggedInput.val());
        if(taggedValue) return;
        var aFoundfeelings = [];
        var generatedHTML = '';
        for (var i in $Cache.feelings) {
            if ($Cache.feelings[i]['title_translated'].toLowerCase().indexOf(sNameToFind.toLowerCase()) >= 0) {
                aFoundfeelings.push({
                    user_id: $Cache.feelings[i]['feeling_id'],
                    full_name: $Cache.feelings[i]['title_translated'],
                    user_image: $Cache.feelings[i]['feeling_image']
                });
                if (($Cache.feelings[i]['image'].substr(0, 5) == 'http:') || ($Cache.feelings[i]['image'].substr(0, 6) == 'https:')) {
                    $Cache.feelings[i]['image'] = '<img src="' + $Cache.feelings[i]['image'] + '" class="_image_32 image_deferred">';
                }
                generatedHTML += '<div class="ynfeed_feeling_item tagFriendChooser" data-id="' + $Cache.feelings[i]['feeling_id'] +'" data-type="feeling" data-text="' + $Cache.feelings[i]['title_translated'] + '"><div class="tagFriendChooserImage">' + $Cache.feelings[i]['image'] + '</div><span>' + (($Cache.feelings[i]['title_translated'].length > 25) ? ($Cache.feelings[i]['title_translated'].substr(0, 25) + '...') : $Cache.feelings[i]['title_translated']) +'</span></div>';
            }
        }
        if(sNameToFind != "")
            generatedHTML += '<div class="ynfeed_feeling_item tagFriendChooser" data-id="' + -1 +'" data-type="feeling" data-text="' + sNameToFind + '"><div class="tagFriendChooserImage"><img src="' + sDefaultFeelingImg + '" class="_image_32 image_deferred">' + '</div><span>' + ((sNameToFind.length > 25) ? (sNameToFind.substr(0, 25) + '...') : sNameToFind) +'</span></div>';
        autocompleteDIV.html(''); // clear the container
        if(generatedHTML != '') {
            autocompleteDIV.html(generatedHTML);
            autocompleteDIV.show();
        }
    }
    function buildImage() {
        if(typeof $Cache.feelings != 'undefined' && $Cache.feelings.length) {
            for (var i in $Cache.feelings) {
                if (($Cache.feelings[i]['image'].substr(0, 5) == 'http:') || ($Cache.feelings[i]['image'].substr(0, 6) == 'https:')) {
                    $Cache.feelings[i]['image'] = '<img src="' + $Cache.feelings[i]['image'] + '" class="_image_32 image_deferred">';
                }
            }
        }
    }
    function draw() {
        buildImage();
        var taggedItems = $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_feeling .ynfeed_tagged_items');
        var taggedInput = $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_selected_feeling');
        var taggedValues = parseInt(taggedInput.val());
        if(taggedItems.find('.ynfeed_selected_feeling').length || isNaN(taggedValues) || taggedValues === 0)
            return;
        if(taggedValues === -1) {
            previewCustomItem();
            return;
        }
        var ofeeling = getFeelingObjByAttr('feeling_id', taggedValues);
        taggedItems.append('<span class="ynfeed_selected_feeling" data-id="' + ofeeling['feeling_id'] + '">' + ofeeling['image'] + ofeeling['title_translated'] + '<a href="javascript:void(0)" class="ynfeed_btn_delete_feeling"><i class="ico ico-close" aria-hidden="true"></i></a></span>');
        previewTaggedItem(taggedValues);
    }
    function previewCustomItem() {
        var text = $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_custom_feeling_text').val();
        var image = $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_custom_feeling_image').val();

        // Input selected feeling preview
        var taggedItems = $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_feeling .ynfeed_tagged_items');
        taggedItems.append('<span class="ynfeed_selected_feeling" data-id="-1">' + '<img src="' + image + '" class="_image_32 image_deferred">' + text + '<a href="javascript:void(0)" class="ynfeed_btn_delete_feeling"><i class="ico ico-close" aria-hidden="true"></i></a></span>');

        // Feeling preview
        var sImage = '<img src="' + image + '" class="_image_32 image_deferred" onclick="$Core.ynfeed.selectCustomFeelingImage(this);">';
        var sTagged = sImage + '<a href="javascript:void(0);" onclick="$(\'#ynfeed_btn_feeling\').trigger(\'click\')">' + text + '</a>';
        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_input_feeling').hide('fast');
        $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_extra_preview_feeling').html(oTranslations['feeling_something'].replace('{something}', sTagged)).show();
    }
};