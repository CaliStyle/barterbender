var eventAdvSearch = {
    defaultCountryIso: '',
    advEventRangeTime: {
        defaultRangeKey: '',
        parentEle: '',
        timeTypeDefault: '',
        ranges: {},
        customRange: {},
        customRangeLabel: '',
        setDefaultParams: function(params) {
            eventAdvSearch.advEventRangeTime.parentEle = $(params['parent']);
            if(eventAdvSearch.advEventRangeTime.parentEle.length) {
                eventAdvSearch.advEventRangeTime.defaultRangeKey = params['default_range_key'];
                eventAdvSearch.advEventRangeTime.timeTypeDefault = params['time_type_default'];
                eventAdvSearch.advEventRangeTime.ranges = params['ranges'];
                if(!empty(params['custom_range'][0]) && !empty(params['custom_range'][1])) {
                    eventAdvSearch.advEventRangeTime.customRange = [moment(params['custom_range'][0], 'MM/DD/YYYY'),moment(params['custom_range'][1], 'MM/DD/YYYY')];
                }
                if(!empty(params['custom_range_label']) && typeof params['custom_range_label'] === 'string') {
                    eventAdvSearch.advEventRangeTime.customRangeLabel = params['custom_range_label'];
                }
            }
        },
        init: function() {
            if(eventAdvSearch.advEventRangeTime.parentEle.length) {
                let input = eventAdvSearch.advEventRangeTime.parentEle.find('#js_time_text');
                let timeArray = eventAdvSearch.advEventRangeTime.timeTypeDefault == eventAdvSearch.advEventRangeTime.customRangeLabel ? eventAdvSearch.advEventRangeTime.customRange : eventAdvSearch.advEventRangeTime.ranges[eventAdvSearch.advEventRangeTime.timeTypeDefault];
                let start = timeArray[0];
                let end = timeArray[1];

                let initParams = {
                    startDate: start,
                    endDate: end,
                    ranges: eventAdvSearch.advEventRangeTime.ranges,
                    locale: {}
                };
                if(typeof eventAdvSearch.advEventRangeTime.customRangeLabel === 'string') {
                    initParams.locale.customRangeLabel = eventAdvSearch.advEventRangeTime.customRangeLabel;
                }

                input.daterangepicker(initParams);

                input.on('change', function() {
                    if($('li[data-range-key="' + eventAdvSearch.advEventRangeTime.defaultRangeKey + '"]').hasClass('active')) {
                        eventAdvSearch.advEventRangeTime.parentEle.find('#js_time_text').val(eventAdvSearch.advEventRangeTime.defaultRangeKey);
                    }
                });

                input.on('apply.daterangepicker', function(ev, picker) {
                    if(picker.chosenLabel == eventAdvSearch.advEventRangeTime.defaultRangeKey  && picker.startDate.format('MM/DD/YYYY') == moment().format('MM/DD/YYYY') && picker.endDate.format('MM/DD/YYYY') == moment().format('MM/DD/YYYY')) {
                        $(this).val(eventAdvSearch.advEventRangeTime.defaultRangeKey);
                    }
                    else {
                        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                    }
                    eventAdvSearch.advEventRangeTime.parentEle.find('#js_time_type').val(picker.chosenLabel);
                });

                input.on('show.daterangepicker', function(ev, picker){
                    if(eventAdvSearch.advEventRangeTime.parentEle.find('#js_time_type').val() == eventAdvSearch.advEventRangeTime.defaultRangeKey) {
                        $(this).val(eventAdvSearch.advEventRangeTime.defaultRangeKey);
                    }

                    if(eventAdvSearch.advEventRangeTime.parentEle.find('#js_time_type').val() != eventAdvSearch.advEventRangeTime.defaultRangeKey && picker.startDate.format('MM/DD/YYYY') == moment().format('MM/DD/YYYY') && picker.endDate.format('MM/DD/YYYY') == moment().format('MM/DD/YYYY')) {
                        $('li[data-range-key="' + eventAdvSearch.advEventRangeTime.defaultRangeKey + '"]').removeClass('active');
                        $('li[data-range-key="' + eventAdvSearch.advEventRangeTime.parentEle.find('#js_time_type').val() + '"]').addClass('active');
                    }
                });

                input.on('cancel.daterangepicker', function(ev, picker) {
                    eventAdvSearch.advEventRangeTime.resetRange();
                });


                if(eventAdvSearch.advEventRangeTime.defaultRangeKey == eventAdvSearch.advEventRangeTime.timeTypeDefault) {
                    eventAdvSearch.advEventRangeTime.parentEle.find('#js_time_text').val(eventAdvSearch.advEventRangeTime.defaultRangeKey);
                }

                setTimeout(this.prepare, 100);
                setTimeout(function(){
                    if (window.matchMedia('(max-width: 767px)').matches){
                        $('.p-daterangepicker-bg-mask-modal').on('click',function(event){
                            var pObject=input.data('daterangepicker');
                            pObject.hide();
                            event.stopPropagation();
                            event.preventDefault();

                        });
                        input.on('show.daterangepicker', function(e){
                            $('body').addClass('has-show-daterangepicker');
                            $(e.target).blur();
                            $Core.disableScroll();
                        });
                        input.on('hide.daterangepicker', function(){
                            $('body').removeClass('has-show-daterangepicker');
                            $Core.enableScroll();
                        });
                    }
                 }, 200);
            }
        },
        prepare: function() {
            let datepickerContainer = $('.daterangepicker');
            if (window.matchMedia('(max-width: 767px)').matches){
                if(!($('.p-daterangepicker-mask-modal-container').length > 0)){
                    $('body').append("<div class=" + "p-daterangepicker-mask-modal-container" + "></div>" );
                    datepickerContainer.append("<div class=" + "p-daterangepicker-bg-mask-modal" + "></div>");
                    $('.p-daterangepicker-mask-modal-container').append(datepickerContainer);
                }
            }
            datepickerContainer.addClass('p-daterangepicker').css('display','none');
        },
        resetRange: function() {
            eventAdvSearch.advEventRangeTime.parentEle.find('#js_time_type').val(eventAdvSearch.advEventRangeTime.defaultRangeKey);
            let inputTime = eventAdvSearch.advEventRangeTime.parentEle.find('#js_time_text');
            let dateRangePicker = inputTime.data('daterangepicker');
            dateRangePicker.setStartDate(moment().format('MM/DD/YYYY'));
            dateRangePicker.setEndDate(moment().format('MM/DD/YYYY'));
            dateRangePicker.hideCalendars();
            inputTime.val(eventAdvSearch.advEventRangeTime.defaultRangeKey);
            eventAdvSearch.advEventRangeTime.resetActive();
        },
        resetActive: function() {
            let dropdown = $('li[data-range-key="' + eventAdvSearch.advEventRangeTime.defaultRangeKey + '"]').closest('ul');
            dropdown.find('li').removeClass('active');
            $('li[data-range-key="' + eventAdvSearch.advEventRangeTime.defaultRangeKey + '"]').addClass('active');
        }
    },
    submitForm: function(obj) {
        let thisObject = $(obj);
        let form = thisObject.closest('form');
        let time = $('#js_time_text', eventAdvSearch.advEventRangeTime.parentEle).val();
        let timeType = $('#js_time_type', eventAdvSearch.advEventRangeTime.parentEle).val();
        let glat = '';
        let glong = '';
        let isAllTime = true;
        let startTime = '';
        let endTime = '';

        if(undefined != ynfeIndexPage.glat && null != ynfeIndexPage.glat
            && undefined != ynfeIndexPage.glong && null != ynfeIndexPage.glong
        )
        {
            glat = ynfeIndexPage.ynfe_base64_encode(ynfeIndexPage.glat.toString());
            glong = ynfeIndexPage.ynfe_base64_encode(ynfeIndexPage.glong.toString());
        }
        $('#js_advsearch_glat').val(glat);
        $('#js_advsearch_glong').val(glong);


        if(!empty(time)) {
            isAllTime = false;
            let timeArray = time.split('-');
            let startDateArray = trim(timeArray[0]).split('/');
            if(startDateArray.length == 3) {
                startTime = timeArray[0];
            }
            let endDateArray = trim(timeArray[1]).split('/');
            if(endDateArray.length == 3) {
                endTime = timeArray[1];
            }
        }
        $('#js_p_start_time').val(startTime);
        $('#js_p_end_time').val(endTime);
        $('#js_advsearch_flag').val(1);
        eventAdvSearch.removeDefaultSortValue(form);
        form.submit();
    },
    resetForm: function() {
        $("input[name='search[search]']").val('');
        $("#search_address").val('');
        $("#search_city").val('');
        $("#search_range_value_from").val('');

        if($('#search_status').closest('.js_core_init_selectize_form_group').length) {
            ($("#search_status").selectize())[0].selectize.setValue('');
        }
        else {
            $('#search_status').val('');
        }

        if($('#country_iso').closest('.js_core_init_selectize_form_group').length) {
            ($("#country_iso").selectize())[0].selectize.setValue(eventAdvSearch.defaultCountryIso);
        }
        else {
            $('#country_iso').val(eventAdvSearch.defaultCountryIso);
        }

        if($('#js_country_child_id_value').closest('.js_core_init_selectize_form_group').length) {
            ($("#js_country_child_id_value").selectize())[0].selectize.setValue('');
        }
        else {
            $('#js_country_child_id_value').val('');
        }

        eventAdvSearch.advEventRangeTime.resetRange();
        return false;
    },
    removeDefaultSortValue: function(form) {
        let parent = (form.length ? form.find('.hidden:first') : '');
        if(!empty(parent) && parent.length) {
            parent.find('input[type="hidden"][name="when"]').remove();
        }
    }
};

var P_AdvEvent = {
    findEventClick: function(obj){
        let objectThis = $(obj);
        if(objectThis.length) {
            let type = objectThis.data('type');
            let startTimeObj = null;
            let endTimeObj = null;
            switch (type) {
                case 'today':
                {
                    startTimeObj = moment();
                    endTimeObj = moment();
                    break;
                }
                case 'tomorrow':
                {
                    startTimeObj = moment().add(1,'days');
                    endTimeObj = moment().add(1,'days');
                    break;
                }
                case 'this-week':
                {
                    startTimeObj = moment().startOf('isoWeek');
                    endTimeObj = moment().endOf('isoWeek');
                    break;
                }
                case 'next-week':
                {
                    startTimeObj = moment().add(1, 'weeks').startOf('isoWeek');
                    endTimeObj = moment().add(1, 'weeks').endOf('isoWeek');
                    break;
                }
            }

            let startTime = startTimeObj.format('MM/DD/YYYY');
            let endTime = endTimeObj.format('MM/DD/YYYY');

            let startDateArray = trim(startTime).split('/');
            let endDateArray = trim(endTime).split('/');
            if(startDateArray.length == 3 && endDateArray.length == 3) {
                let parent = objectThis.closest('.js_find_event_block').find('form:first');
                parent.find('.js_find_event_start_time').val(startTime);
                parent.find('.js_find_event_end_time').val(endTime);
                parent.find('.js_find_event_time_type').val(objectThis.data('text'));
                parent.closest('form').submit();
            }
        }
    },
    initSimpleDateRangePickerWithOnlyCustomRange: function(ele, callback, params) {
        let object = $(ele);
        if(object.length) {
            let startInit = moment().startOf('isoWeek');
            let endInit = moment().endOf('isoWeek');
            if(typeof params !== 'undefined') {
                startInit = params.startInit;
                endInit = params.endInit;
            }
            object.daterangepicker({
                startDate: startInit,
                endDate: endInit
            }, callback);

            object.on('apply.daterangepicker', function (daterangepicker){
                $(daterangepicker.currentTarget).closest('.js_find_event_block').find('form:first').submit();
            });
            $('.daterangepicker').addClass('p-daterangepicker').css('display','none');
            setTimeout(eventAdvSearch.advEventRangeTime.prepare, 100);
            setTimeout(function(){
                if (window.matchMedia('(max-width: 767px)').matches){
                    $('.p-daterangepicker-bg-mask-modal').on('click',function(event){
                        var pObject=object.data('daterangepicker');
                        pObject.hide();
                        event.stopPropagation();
                        event.preventDefault();

                    });
                    object.on('show.daterangepicker', function(e){
                        $('body').addClass('has-show-daterangepicker');
                        $(e.target).blur();
                        $Core.disableScroll();
                    });
                    object.on('hide.daterangepicker', function(){
                        $('body').removeClass('has-show-daterangepicker');
                        $Core.enableScroll();
                    });
                }
             }, 200);
        }
    },
    sendYourWish: function(obj) {
        let objectThis = $(obj);
        if(objectThis.length) {
            let parent = objectThis.closest('.js_send_wish_item');
            let message = parent.find('.js_send_wish_message:first').val();
            let userId = parent.data('id');
            $.ajaxCall('fevent.sendWish', 'user_id=' + userId + '&message=' + encodeURIComponent(message));
        }
    },
    showTabAttendingPeople: function(obj){
        let objectThis = $(obj);
        var iEventId = objectThis.data('event-id');
        tb_show(objectThis.data('text'), $.ajaxBox('fevent.showGuestList', 'tab=attending&event_id=' + iEventId + (objectThis.data('statistic') ? '&statistic=1' : '')));
    }
}

$Behavior.findEventBlock = function() {
    if($('.js_find_event_block').length) {
        $('.js_find_event_block').on('.js_find_event_select', 'click', function(){
             let objectThis = $(this).get(0);
             P_AdvEvent.findEventClick(objectThis);
        });
    }
}

$Behavior.processRsvpAction = function() {
    $(document).on('click', '[data-toggle="event_rsvp"]', function(){
        let parent = $(this).closest('.js_rsvp_content');
        let rsvp = $(this).prop('rel');
        let eventId = parent.data('id');
        if(parseInt(rsvp) == 0) {
            let phrase = parent.data('phrase');
            let replacedText = '<a class="btn btn-default btn-sm" data-toggle="event_rsvp" rel="2"><i class="ico ico-star-o mr-1"></i><span class="item-text">' + phrase + '</span></a>';
            parent.html(replacedText);
        }
        $.ajaxCall('fevent.addRsvp', 'id=' + eventId + '&rsvp=' + rsvp + '&inline=1&rsvp_type=list');
    });
}

$Behavior.initFeventSlideshow = function(){
    var owl = $('.p-fevent-slider-container.owl-carousel');
    if (!owl.length || owl.prop('built')) {
        return false;
    }
    owl.prop('built', true);
    owl.addClass('dont-unbind-children');
    var rtl = false;
    if ($("html").attr("dir") == "rtl") {
        rtl = true;
    }
    var item_amount = parseInt(owl.find('.item').length);
    var more_than_one_item = item_amount > 1;
    var dotseach = 1;
    var stagepadding = 0;

    if(more_than_one_item){
        if (window.matchMedia('(min-width: 1200px)').matches) {
            if($('#main.empty-right.empty-left').length > 0){
                stagepadding = 130;
            }else if (($('#main.empty-right').length > 0) || ($('#main.empty-left').length > 0)){
                stagepadding = 130;
            }
        }else if(window.matchMedia('(min-width: 992px)').matches ){
            if($('#main.empty-right.empty-left').length > 0){
                stagepadding = 130;
            }
        }
    }

    owl.owlCarousel({
        rtl: rtl,
        items: 1,
        loop: more_than_one_item,
        mouseDrag: more_than_one_item,
        margin: 16,
        autoplay: false,
        autoplayTimeout: 300,
        autoplayHoverPause: true,
        smartSpeed: 800,
        dots:true,
        stagePadding: stagepadding,
        navText: ['<i class="ico ico-angle-left"></i>','<i class="ico ico-angle-right"></i>'],
        nav:true
    });
};

PF.event.on('p_update_main_layout', function(){
    if($('.p-fevent-slider-container.owl-carousel').length > 0){
        $('.p-fevent-slider-container.owl-carousel').trigger('destroy.owl.carousel').prop('built', false);
        $Behavior.initFeventSlideshow();
    }
    if($('.p-fevent-birthday-container .item-listing-today.owl-carousel').length > 0){
        $('.p-fevent-birthday-container .item-listing-today.owl-carousel').trigger('destroy.owl.carousel').prop('built', false);
        $Behavior.initFeventBirthdaySlideshow();
    }
});