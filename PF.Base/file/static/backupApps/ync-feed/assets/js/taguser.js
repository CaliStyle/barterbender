$.fn.tagUser = function(options) {
    var typedText;
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
             * Arrow Down
             */
            if (key == 40) {
                var $container = $(this).closest('.ynfeed-tagging-input-box');
                if ($container.find('.ynfeed_autocomplete').is(':visible')) { // check if autocomplete is visible
                    // activate the first element
                    $(this).blur();
                    $container.find('.ynfeed_autocomplete').attr("tabindex", -1).focus();
                    if (!$container.find('.ynfeed_autocomplete').find('.ynfeed_tagging_item[tabindex=-1]')[0]) {
                        $container.find('.ynfeed_autocomplete').find('.ynfeed_tagging_item').first().attr("tabindex", -1).focus(); // set focus to the first element
                    }
                }
            }
            /**
             * Backspace
             */
            if (key == 8 && typedText == '') {
                $(this).closest('.ynfeed_compose_tagging').find('.ynfeed_autocomplete').hide();
            }
        }).on('keydown', function(e) {
            var key = e.keyCode || e.charCode || e.which;
            if(key == 8 && $(this).val() == '') {
                var taggedItem = $(this).closest('.ynfeed_compose_tagging').find('.ynfeed_tagged_items .ynfeed_tagged_item').last();
                removeTaggedItem(taggedItem);
            }
        });

        $(document).off('click', '.ynfeed_tagging_item').on('click', '.ynfeed_tagging_item', function() {
            selectElement($(this));
        });

        $(document).off('click', '.ynfeed_btn_delete_tagged').on('click', '.ynfeed_btn_delete_tagged', function() {
            removeTaggedItem($(this).parent('.ynfeed_tagged_item'));
        });

        $('#ynfeed_input_tagged').on('change', function() {
            var taggedValues = $.map( $(this).val().split(','), function(v){
                return v === "" ? null : v;
            });
            var inputTagging = $(this).closest('.ynfeed_compose_tagging').find('.ynfeed_input_tagging');
            if(taggedValues.length){
                inputTagging.prop('placeholder', '');
            }else{
                inputTagging.prop('placeholder', oTranslations['who_is_with_you']);
            }
        });

        $('.ynfeed_compose_tagging [class=ynfeed_autocomplete]').off('keydown').on('keydown', function(e) {
            var key = e.keyCode || e.charCode || e.which;
            if (key == 39) { /*Right*/
                e.preventDefault();
                if (!$(this).find('.ynfeed_tagging_item[tabindex=-1]')[0]) { // check if there is focus on any tag
                    $(this).removeAttr('tabindex'); // remove the container focus
                    $(this).find('.ynfeed_tagging_item').first().attr("tabindex", -1).focus(); // set focus to the first element
                } else {
                    // set focus to the next item
                    if($(this).find('.ynfeed_tagging_item[tabindex=-1]').next().length) {
                        $(this).find('.ynfeed_tagging_item[tabindex=-1]').removeAttr('tabindex').next().attr("tabindex", -1).focus();
                    } else {
                        $(this).find('.ynfeed_tagging_item[tabindex=-1]').removeAttr('tabindex');
                        $(this).find('.ynfeed_tagging_item').first().attr("tabindex", -1).focus();
                    }
                }
            } else if (key == 40) { /*Down*/
                e.preventDefault();
                if (!$(this).find('.ynfeed_tagging_item[tabindex=-1]')[0]) { // check if there is focus on any tag
                    $(this).removeAttr('tabindex'); // remove the container focus
                    $(this).find('.ynfeed_tagging_item').first().attr("tabindex", -1).focus(); // set focus to the first element
                } else {
                    // set focus to the next item
                    if($(this).find('.ynfeed_tagging_item[tabindex=-1]').next().next().length) {
                        $(this).find('.ynfeed_tagging_item[tabindex=-1]').removeAttr('tabindex').next().next().attr("tabindex", -1).focus();
                    }
                }
            } else if (key === 37) { /*Left*/
                e.preventDefault();
                if($(this).find('.ynfeed_tagging_item[tabindex=-1]').prev('.ynfeed_tagging_item').length) {
                    $(this).find('.ynfeed_tagging_item[tabindex=-1]').removeAttr('tabindex').prev().attr("tabindex", -1).focus();
                }else{
                    $(this).find('.ynfeed_tagging_item[tabindex=-1]').removeAttr('tabindex');
                    $(this).find('.ynfeed_tagging_item').last().attr("tabindex", -1).focus();
                }
            } else if (key === 38) { /*Up*/
                e.preventDefault();
                if($(this).find('.ynfeed_tagging_item[tabindex=-1]').prev().prev().length) {
                    $(this).find('.ynfeed_tagging_item[tabindex=-1]').removeAttr('tabindex').prev().prev().attr("tabindex", -1).focus();
                }
            }
        });
        /**
         * On ENTER (selecting the tag) , fires the click trigger
         */
        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_tagging [class=ynfeed_autocomplete]').keypress(function(e) {
            var key = e.keyCode || e.charCode || e.which;
            if (key == 13) {
                e.preventDefault();
                selectElement($(this).find('.ynfeed_tagging_item[tabindex=-1]'));
            }
        });
    });

    function removeTaggedItem(taggedItem) {
        var id = taggedItem.data("id");
        if(typeof id == 'undefined')
            return;
        var taggedInput = $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_tagged');
        var taggedValues = $.map( taggedInput.val().split(','), function(v){
            return v === "" ? null : v;
        });
        taggedValues.splice(taggedValues.indexOf(id.toString()), 1);
        taggedInput.val(taggedValues.toString()).trigger('change');
        taggedItem.remove();
        previewTaggedItems(taggedValues);
    }

    function selectElement(item) {
        var selectedTagText = item.data("text");
        var selectedTagId = parseInt(item.data("id"));
        var $container = item.parent();
        var taggedItems = $container.parent().find('.ynfeed_tagged_items');
        var taggedInput = $container.parent().find('#ynfeed_input_tagged');
        var taggedValues = $.map( taggedInput.val().split(','), function(v){
            return v === "" ? null : parseInt(v);
        });
        if(taggedValues.indexOf(selectedTagId) >= 0)
            return;
        taggedValues.push(selectedTagId);
        taggedInput.val(taggedValues.toString()).trigger('change');
        taggedItems.append('<span class="ynfeed_tagged_item" data-id="' + selectedTagId + '">' + selectedTagText + '<a href="javascript:void(0)" class="ynfeed_btn_delete_tagged"><i class="ico ico-close" aria-hidden="true"></i></a></span>');
        $container.parent().find('.ynfeed_input_tagging').val('').focus();
        $container.hide();
        previewTaggedItems(taggedValues);
    }

    function previewTaggedItems(taggedValues) {
        var friend0, friend1, sTagged0, sTagged1, sTooltips;
        if(taggedValues.length == 1){
            friend0 = getFriendObjByAttr('user_id', taggedValues[0]);
            sTagged0 = '<a href="javascript:void(0)" onclick="$(\'#ynfeed_btn_tag\').trigger(\'click\')">' + friend0['full_name'] + '</a>';
            $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_extra_preview_tagged').html( oTranslations['with_somebody'].replace('{somebody}', sTagged0)).show();
            $('#ynfeed_btn_tag').addClass('has_data');
        } else if(taggedValues.length == 2){
            friend0 = getFriendObjByAttr('user_id', taggedValues[0]);
            sTagged0 = '<a href="javascript:void(0)" onclick="$(\'#ynfeed_btn_tag\').trigger(\'click\')">' + friend0['full_name'] + '</a>';
            friend1 = getFriendObjByAttr('user_id', taggedValues[1]);
            sTagged1 = '<a href="javascript:void(0)" onclick="$(\'#ynfeed_btn_tag\').trigger(\'click\')">' + friend1['full_name'] + '</a>';
            $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_extra_preview_tagged').html( oTranslations['with_somebody_and_somebody'].replace('{somebody0}', sTagged0).replace('{somebody1}', sTagged1)).show();
            $('#ynfeed_btn_tag').addClass('has_data');
        } else if(taggedValues.length > 2) {
            friend0 = getFriendObjByAttr('user_id', taggedValues[0]);
            sTagged0 = '<a href="javascript:void(0)" onclick="$(\'#ynfeed_btn_tag\').trigger(\'click\')">' + friend0['full_name'] + '</a>';
            sTooltips = '';
            for(var i = 1; i < taggedValues.length; i++) {
                friend = getFriendObjByAttr('user_id', taggedValues[i]);
                sTooltips += friend['full_name'] + '<br />';
            }
            sTagged1 = '<span class="ynfeed_tooltip_element" data-toggle="popover" data-placement="bottom" data-trigger="hover" data-content="' + sTooltips +'" onclick="$(\'#ynfeed_btn_tag\').trigger(\'click\')">' + oTranslations['number_others'].replace('{number}', taggedValues.length - 1) + '</span>';
            $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_extra_preview_tagged').html(oTranslations['with_somebody_and_somebody'].replace('{somebody0}', sTagged0).replace('{somebody1}', sTagged1)).show();
            $('#ynfeed_btn_tag').addClass('has_data');
        } else {
            $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_extra_preview_tagged').html('');
            $('#ynfeed_btn_tag').removeClass('has_data');
        }
        $('[data-toggle="popover"]').popover({html:true});
    }

    function getFriendObjByAttr(att, value){
        for (var i in $Cache.friends) {
            if($Cache.friends[i][att] == value)
                return $Cache.friends[i];
        }
        if(typeof $Cache.users !== 'undefined') {
            for (var i in $Cache.users) {
                if ($Cache.users[i][att] == value)
                    return $Cache.users[i];
            }
        }
        return null;
    }
    function generateAutocomplete($editableContainer, sNameToFind) {
        var autocompleteDIV = $editableContainer.closest('.ynfeed_compose_tagging').find('.ynfeed_autocomplete');
        $('.ynfeed_autocomplete').hide();
        // show autocomplete container
        // insert search results into the container
        var taggedInput = $('#ynfeed_input_tagged');
        var taggedValues = $.map( taggedInput.val().split(','), function(v){
            return v === "" ? null : v;
        });
        var aFoundFriends = [],
            generatedHTML = '',
            iLimit = 10;

        for (var i in $Cache.friends) {
            if ($Cache.friends[i]['full_name'].toLowerCase().indexOf(sNameToFind.toLowerCase()) >= 0 && (taggedValues.indexOf($Cache.friends[i]['user_id']) == -1)) {
                aFoundFriends.push({
                    user_id: $Cache.friends[i]['user_id'],
                    full_name: $Cache.friends[i]['full_name'],
                    user_image: $Cache.friends[i]['user_image']
                });
                if (($Cache.friends[i]['user_image'].substr(0, 5) == 'http:') || ($Cache.friends[i]['user_image'].substr(0, 6) == 'https:')) {
                    PF.event.trigger('urer_image_url', $Cache.friends[i]);
                    $Cache.friends[i]['user_image'] = '<img src="' + $Cache.friends[i]['user_image'] + '" class="_image_32 image_deferred">';
                }

                generatedHTML += '<div class="ynfeed_tagging_item tagFriendChooser" data-id="' + $Cache.friends[i]['user_id'] +'" data-type="user" data-text="' + $Cache.friends[i]['full_name'] + '"><div class="tagFriendChooserImage">' + $Cache.friends[i]['user_image'] + '</div><span>' + $Cache.friends[i]['full_name'] + '</span></div>';
            }
            if (aFoundFriends.length >= iLimit) {
                break;
            }
        }
        autocompleteDIV.html(' '); // clear the container
        if(generatedHTML != '') {
            autocompleteDIV.html(generatedHTML);
            $(autocompleteDIV).find('a').prop('href', 'javascript:void(0);');
            autocompleteDIV.show();
        }
    }
    function draw() {
        if(!$('.activity_feed_form:not(.ynfeed_detached)').length)
            return;
        var taggedItems = $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_tagging .ynfeed_tagged_items');
        if(taggedItems.find('.ynfeed_tagged_item').length)
            return;
        var inputTagging = $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_tagging').find('.ynfeed_input_tagging');

        var taggedInput = $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_tagged');
        var taggedValues = $.map( taggedInput.val().split(','), function(v){
            return v === "" ? null : v;
        });
        if(taggedValues.length)
            inputTagging.prop('placeholder', '');

        taggedValues.forEach(function (id) {
            var friend = getFriendObjByAttr('user_id', id);
            if(friend) {
                taggedItems.append('<span class="ynfeed_tagged_item" data-id="' + friend['user_id'] + '">' + friend['full_name'] + '<a href="javascript:void(0)" class="ynfeed_btn_delete_tagged"><i class="ico ico-close" aria-hidden="true"></i></a></span>');
            }
        });
        previewTaggedItems(taggedValues);
    }
};
