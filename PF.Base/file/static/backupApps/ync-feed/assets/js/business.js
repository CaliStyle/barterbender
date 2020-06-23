$.fn.businessCheckin = function(options) {
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
             * Backspace
             */
            if (key == 8 && typedText == '') {
                $(this).closest('.ynfeed_compose_tagging').find('.ynfeed_autocomplete').hide();
            }
            /**
             * Arrow Down
             */
            if (key == 40) {
                var $container = $(this).closest('.ynfeed_compose_business');
                if ($container.find('.ynfeed_autocomplete').is(':visible')) { // check if autocomplete is visible
                    // activate the first element
                    $(this).blur();
                    $container.find('.ynfeed_autocomplete').attr("tabindex", -1).focus();
                    if (!$container.find('.ynfeed_autocomplete').find('.ynfeed_business_item[tabindex=-1]')[0]) {
                        $container.find('.ynfeed_autocomplete').find('.ynfeed_business_item').first().attr("tabindex", -1).focus(); // set focus to the first element
                    }
                }
            }
        });

        $(document).off('click', '.ynfeed_business_item').on('click', '.ynfeed_business_item', function() {
            selectElement($(this));
        });

        $(document).off('click', '.ynfeed_btn_delete_business').on('click', '.ynfeed_btn_delete_business', function() {
            removeTaggedItem();
        });

        $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_selected_business').on('change', function() {
            var taggedValues = $.map( $(this).val().split(','), function(v){
                return v === "" ? null : v;
            });
            var inputTagging = $(this).closest('.ynfeed_compose_business').find('.ynfeed_compose_business');
            if(taggedValues.length){
                inputTagging.prop('placeholder', '');
            }else{
                inputTagging.prop('placeholder', oTranslations['business_name']);
            }

        });
        $('.ynfeed_compose_business [class=ynfeed_autocomplete]').off('keydown').on('keydown', function(e) {
            var key = e.keyCode || e.charCode || e.which;
            if (key == 40) { /*Down*/
                e.preventDefault();
                if (!$(this).find('.ynfeed_business_item[tabindex=-1]')[0]) { // check if there is focus on any tag
                    $(this).removeAttr('tabindex'); // remove the container focus
                    $(this).find('.ynfeed_business_item').first().attr("tabindex", -1).focus(); // set focus to the first element
                } else {
                    // set focus to the next item
                    if($(this).find('.ynfeed_business_item[tabindex=-1]').next().length) {
                        $(this).find('.ynfeed_business_item[tabindex=-1]').removeAttr('tabindex').next().attr("tabindex", -1).focus();
                    } else {
                        $(this).find('.ynfeed_business_item[tabindex=-1]').removeAttr('tabindex');
                        $(this).find('.ynfeed_business_item').first().attr("tabindex", -1).focus();
                    }
                }
            } else if (key === 38) { /*Up*/
                e.preventDefault();
                if($(this).find('.ynfeed_business_item[tabindex=-1]').prev().length) {
                    $(this).find('.ynfeed_business_item[tabindex=-1]').removeAttr('tabindex').prev().attr("tabindex", -1).focus();
                }else{
                    $(this).find('.ynfeed_business_item[tabindex=-1]').removeAttr('tabindex');
                    $(this).find('.ynfeed_business_item').last().attr("tabindex", -1).focus();
                }
            }
        });
        /**
         * On ENTER (selecting the tag) , fires the click trigger
         */
        $('.ynfeed_compose_business [class=ynfeed_autocomplete]').keypress(function(e) {
            var key = e.keyCode || e.charCode || e.which;
            if (key == 13) {
                e.preventDefault();
                selectElement($(this).find('.ynfeed_business_item[tabindex=-1]'));
            }
        });
    });

    function removeTaggedItem() {
        var taggedInput = $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_selected_business');
        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_business .ynfeed_tagged_items').html('');
        taggedInput.val('');
        previewTaggedItem([]);
    }

    function selectElement(item) {
        var selectedTagText = item.data("text");
        var selectedTagId = item.data("id");
        var $container = item.parent();
        var taggedItems = $container.parent().find('.ynfeed_tagged_items');
        var taggedInput = $container.parent().find('#ynfeed_input_selected_business');
        var taggedValue = selectedTagId;
        taggedInput.val(taggedValue.toString()).trigger('change');
        taggedItems.append('<span data-id="' + selectedTagId + '">' + selectedTagText + '<a href="javascript:void(0)" class="ynfeed_btn_delete_business"><i class="ico ico-close" aria-hidden="true"></i></a></span>');
        $container.parent().find('.ynfeed_input_business').val('').focus();
        $container.hide();
        $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_btn_business').removeClass('is_active');
        $Core.ynfeedCheckin.cancelCheckin();
        previewTaggedItem(taggedValue);
    }

    function previewTaggedItem(taggedValue) {
        taggedValue = parseInt(taggedValue);
        if(taggedValue) {
            var oBusiness, sTagged;
            oBusiness = getBusinessObjByAttr('business_id', taggedValue);
            sTagged = '<a href="javascript:void(0);" onclick="$(\'#ynfeed_btn_business\').trigger(\'click\')">' + oBusiness['name'] + '</a>';
            $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_input_business').hide();
            $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_extra_preview_business').html(oTranslations['at_location'].replace('{location}', sTagged)).show();
            $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_business').hide('fast');
            $('.activity_feed_form:not(.ynfeed_detached)').find('#ynfeed_btn_business').addClass('has_data');
        }else{
            $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_extra_preview_business').html('');
            $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_input_business').show();
            $('.activity_feed_form:not(.ynfeed_detached)').find('#ynfeed_btn_business').removeClass('has_data');
        }
    }

    function getBusinessObjByAttr(att, value){
        for (var i in $Cache.businesses) {
            if($Cache.businesses[i][att] == value)
                return $Cache.businesses[i];
        }
        return null;
    }
    function generateAutocomplete($editableContainer, sNameToFind) {
        var autocompleteDIV = $editableContainer.closest('.ynfeed_compose_business').find('.ynfeed_autocomplete');
        $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_autocomplete').hide();
        // show autocomplete container
        // insert search results into the container
        var taggedInput = $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_selected_business');
        var taggedValue = parseInt(taggedInput.val());
        if(taggedValue) return;
        var aFoundBusinesses = [];
        var generatedHTML = '';
        for (var i in $Cache.businesses) {
            if ($Cache.businesses[i]['name'].toLowerCase().indexOf(sNameToFind.toLowerCase()) >= 0) {
                aFoundBusinesses.push({
                    user_id: $Cache.businesses[i]['business_id'],
                    full_name: $Cache.businesses[i]['name'],
                    user_image: $Cache.businesses[i]['business_image']
                });
                if (($Cache.businesses[i]['business_image'].substr(0, 5) == 'http:') || ($Cache.businesses[i]['business_image'].substr(0, 6) == 'https:')) {
                    $Cache.businesses[i]['business_image'] = '<img src="' + $Cache.businesses[i]['business_image'] + '" class="_image_32 image_deferred">';
                }
                generatedHTML += '<div class="ynfeed_business_item tagFriendChooser" data-id="' + $Cache.businesses[i]['business_id'] +'" data-type="business" data-text="' + $Cache.businesses[i]['name'] + '"><div class="tagFriendChooserImage">' + $Cache.businesses[i]['business_image'] + '</div><span>' + $Cache.businesses[i]['name'] +'</span></div>';
            }
        }
        autocompleteDIV.html(' '); // clear the container
        if(generatedHTML != '') {
            autocompleteDIV.html(generatedHTML);
            $(autocompleteDIV).find('a').prop('href', 'javascript:void(0);');
            autocompleteDIV.show();
        }

    }
    function buildImage() {
        if(typeof $Cache.businesses != 'undefined' && $Cache.businesses != null && $Cache.businesses.length) {
            for (var i in $Cache.businesses) {
                if (($Cache.businesses[i]['business_image'].substr(0, 5) == 'http:') || ($Cache.businesses[i]['business_image'].substr(0, 6) == 'https:')) {
                    $Cache.businesses[i]['business_image'] = '<img src="' + $Cache.businesses[i]['business_image'] + '" class="_image_32 image_deferred">';
                }
            }
        }
    }

    function draw() {
        buildImage();
        var taggedItems = $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_business .ynfeed_tagged_items');
        var taggedInput = $('.activity_feed_form:not(.ynfeed_detached) #ynfeed_input_selected_business');
        var taggedValues = parseInt(taggedInput.val());
        if(taggedItems.find('span').length || isNaN(taggedValues) || taggedValues <= 0)
            return;
        var inputTagging = $('.activity_feed_form:not(.ynfeed_detached) .ynfeed_compose_feeling').find('.ynfeed_input_feeling');
        inputTagging.prop('placeholder', '');
        var obusiness = getBusinessObjByAttr('business_id', taggedValues);
        taggedItems.append('<span data-id="' + obusiness['business_id'] + '">' + obusiness['name'] + '<a href="javascript:void(0)" class="ynfeed_btn_delete_business"><i class="ico ico-close" aria-hidden="true"></i></a></span>');
        previewTaggedItem(taggedValues);
    }
};
