;$Behavior.addNewAdvEvent = function()
{
    if (typeof($Core.dropzone.instance['fevent']) != 'undefined') {
        $Core.dropzone.instance['fevent'].files = [];
    }
	$('.js_event_change_group').click(function()
	{
		if ($(this).parent().hasClass('locked'))
		{
			return false;
		}
		
		aParts = explode('#', this.href);
		
		$('.js_event_block').hide();
		$('#js_event_block_' + aParts[1]).show();
		$(this).parents('.header_bar_menu:first').find('li').removeClass('active');
		$(this).parent().addClass('active');
		$('#js_event_add_action').val(aParts[1]);
	});
	
	$('.js_mp_fevent_category_list').change(function()
	{
        if($(this).val()=='')
        {
            var comboboxes = $("#categories .js_mp_fevent_category_list");
            for(var i=0; i<comboboxes.length; i++)
            {
                if(comboboxes[i].id==this.id && i>0)
                {
                    $(comboboxes[i-1]).change();
                }
            }
            return;
        }
        // Display custom fields if available
        var event_id = $('#category_event_id').val();
       
        
		var iParentId = parseInt(this.id.replace('js_mp_id_', ''));
           $.ajaxCall("fevent.getCustomFields", "id=" + $(this).val()+"&event_id="+event_id+"&parent_id="+iParentId);
		$('.js_mp_fevent_category_list').each(function()
		{
			if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId)
			{
				$('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();				
				
				this.value = '';
			}
		});
		$('#js_mp_holder_' + $(this).val()).show();
	});
}

function custom_js_event_form(form)
{
    $('#js_event_form_msg').html('');
    var bIsValid = true;
    var fields = eval($("#required_custom_fields").val());
    if(fields!=null)
    for(var i=0; i<fields.length; i++)
    {
        var passed = true;
        switch(fields[i]['var_type'])
        {
            case "radio":
            case "checkbox":
                if($('input[id="cf_' + fields[i]['field_name']+'"]:checked').length==0)
                {
                    passed = false;
                }
                break;
            default:
            var value = $.trim($('#cf_' + fields[i]['field_name']).val());
            if(value == '' || value == null)
            {
                passed = false;
            }
        }
        if(!passed)
        {
            bIsValid = false; 
            $('#js_event_form_msg').message(oTranslations['fevent.the_field_field_name_is_required'].replace('{field_name}', fields[i]['phrase_name']), 'error');
            $('#cf_' + fields[i]['field_name']).addClass('alert_input');
        }
    }
    if (!bIsValid)
    {
        $('#js_event_form_msg').show();
        window.scrollTo(0,0);
    } else {
        $('#js_event_form_msg').hide('');
    }
    return bIsValid && Validation_js_event_form(form);
}

var ynfeAddPage = 
{
    dataPrint : {},
    success: false,
    isEditEventConfirmBox : false,
    successIds: '',
    init: function()
    {
        // onchange event type
        $('#ynfevent_event_type input').click(function(event) {
            switch(this.value){
                case 'one_time':
                    $('#ynfevent_one_time_section').show('slow');
                    $('#ynfevent_repeat_section').hide('slow');
                    break;
                case 'repeat':
                    $('#ynfevent_repeat_section').show('slow');
                    $('#ynfevent_one_time_section').hide('slow');
                    break;
            }
        });
    }
    , onchangeSelrepeat: function()
    {
        var selrepeat = $('#selrepeat').val();
        switch(selrepeat){
            case '0': 
                 $('#durationDays').css('display', 'none');
                 $('#duraitonHoursLabel').html(oTranslations['fevent.duration'] + ':');
                 $('#hint_duration_hours').html(oTranslations['fevent.from_1_to_23']);
                break;
            case '1': 
                 $('#durationDays').css('display', 'block');
                 $('#duraitonHoursLabel').html('&nbsp;');
                 $('#hint_duration_hours').html(oTranslations['fevent.from_0_to_23']);
                 $('#hint_duration_days').html(oTranslations['fevent.from_0_to_6']);
                break;
            case '2': 
                 $('#durationDays').css('display', 'block');
                 $('#duraitonHoursLabel').html('&nbsp;');

                var sDate = $('#end_on').val();
                sDate = $.trim(sDate); 
                if(sDate.length > 0){                    
                    var month = $('#start_month').val();
                    var day = $('#start_day').val();
                    var year = $('#start_year').val();

                    var sameDayInNextMonth = ynfeAddPage.getSameDayInNextMonth(parseInt(day), parseInt(month), parseInt(year));

                    var numberOfDays = ynfeAddPage.daysToDate(parseInt(day), parseInt(month), parseInt(year), parseInt(sameDayInNextMonth.day), parseInt(sameDayInNextMonth.month), parseInt(sameDayInNextMonth.year));
                    $('#hint_duration_days').html(oTranslations['fevent.h_from_0_to_number'].replace('{number}', (numberOfDays - 1)));
                } else {
                    $('#hint_duration_days').html(oTranslations['fevent.h_from_0_to_30']);
                }
                $('#hint_duration_hours').html(oTranslations['fevent.from_0_to_23']);

                break;
            default: 
                alert(-1);
                break;
        }
    }       
    , onchangeEndOn: function()
    {
        var sDate = $('#end_on').val();
        sDate = $.trim(sDate); 
        var selrepeat = $('#selrepeat').val();

        if(sDate.length > 0 && '2' == selrepeat){            
            // var aDate = sDate.split("/");
            // var day = aDate[1];
            // var month = aDate[0];
            // var year = aDate[2];
            var month = $('#start_month').val();
            var day = $('#start_day').val();
            var year = $('#start_year').val();

            var sameDayInNextMonth = ynfeAddPage.getSameDayInNextMonth(parseInt(day), parseInt(month), parseInt(year));

            var numberOfDays = ynfeAddPage.daysToDate(parseInt(day), parseInt(month), parseInt(year), parseInt(sameDayInNextMonth.day), parseInt(sameDayInNextMonth.month), parseInt(sameDayInNextMonth.year));
            $('#hint_duration_days').html(oTranslations['fevent.h_from_0_to_number'].replace('{number}', (numberOfDays - 1)));
        }
    }       
    , getSameDayInNextMonth: function(day, month, year)
    {
        //  if date is invalid, date will be become last date in month 
        if(month == 12){
            month = 1;
            year = year + 1; 
        } else {
            month = month + 1;     
        }

        var exist = false; 
        while(exist == false){
            var date = new Date(year, month - 1, day);
            if (date.getFullYear() == year && date.getMonth() + 1 == month && date.getDate() == day) {
              exist = true;
              break;
            } else {
              exist = false;
              day = day - 1;
            }        
        }

        return {day: day, month: month, year: year};
    }       
    , daysToDate: function(day1, month1, year1, day2, month2, year2)
    {
        var oneDay = 1 * 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
        var firstDate = new Date(year1, month1 - 1, day1);
        var secondDate = new Date(year2, month2 - 1, day2);

        var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay))); 

        return diffDays;        
    }     
    , confirmEditEvent: function(){
        if(ynfeAddPage.isEditEventConfirmBox){
            return true;
        }

        sHtml = "";
        sHtml += '<div class="white-popup-block" style="width: 300px;">'; 
            sHtml += '<div class="ynfevent-edit-confirm-box-title">'; 
                sHtml += oTranslations['fevent.edit_apply_for']; 
            sHtml += '</div>'; 

            sHtml += '<div>'; 
                sHtml += oTranslations['fevent.please_choose_the_type_of_event_to_edit']; 
            sHtml += '</div>'; 

            sHtml += '<div id="ynfevent_editconfirmboxoption">'; 
                sHtml += '<input type="radio" name="popup_confirmeditevent" value="only_this_event" checked="checked" />'; 
                sHtml += oTranslations[' fevent.only_this_event']; 
                sHtml += '</br> <input type="radio" name="popup_confirmeditevent" value="all_events_uppercase" />'; 
                sHtml += oTranslations[' fevent.all_events_uppercase']; 
                sHtml += '</br> <input type="radio" name="popup_confirmeditevent" value="following_events" />'; 
                sHtml += oTranslations[' fevent.following_events']; 
            sHtml += '</div>'; 

            sHtml += '<div class="ynfevent-edit-confirm-box-hint">'; 
                sHtml += oTranslations['fevent.note_apply_only_data_in_event_details_tab'];
            sHtml += '</div>'; 

            sHtml += '<div class="ynfevent-edit-confirm-box-button">'; 
                sHtml += '<button class="btn btn-sm btn-primary" onclick="ynfeAddPage.yesConfirmEditEvent();">'; 
                    sHtml += oTranslations['fevent.confirm']; 
                sHtml += '</button>'; 
                sHtml += '<button class="btn btn-sm btn-danger" style="margin-left: 10px;" onclick="$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">'; 
                    sHtml += oTranslations['fevent.cancel']; 
                sHtml += '</button>'; 
            sHtml += '</div>'; 

        sHtml += '</div>'; 

        $.magnificPopup.open({
              items: {
                src: sHtml, 
                type: 'inline'
              }
            });

        return false;
    }
    , yesConfirmEditEvent: function(){
        var selected = $("#ynfevent_editconfirmboxoption input[type='radio']:checked");
        if (selected.length > 0) {
            $('#ynfevent_editconfirmboxoption_value').val(selected.val());
        }

        if(custom_js_event_form()){
            ynfeAddPage.isEditEventConfirmBox = true;
            $( "#js_event_form" ).submit();
        }
    },
    dropzoneOnAddedFile: function () {
        $('#js_fevent_done_upload').show();
    },

    dropzoneOnSending: function (data, xhr, formData) {
        $('#js_event_form').find('input[type="hidden"]').each(function () {
            formData.append($(this).prop('name'), $(this).val());
        });
    },

    dropzoneOnSuccess: function (ele, file, response) {
        response = JSON.parse(response);
        if (typeof response.id !== 'undefined') {
            file.item_id = response.id;
        }
        // show error message
        if (typeof response.errors != 'undefined') {
            for (var i in response.errors) {
                if (response.errors[i]) {
                    $Core.dropzone.setFileError('marketplace', file, response.errors[i]);
                    return;
                }
            }
        }
        ynfeAddPage.success = true;
        ynfeAddPage.successIds += response.id + ',';
        return file.previewElement.classList.add('dz-success');
    },

    dropzoneOnError: function (ele, file) {

    },
    dropzoneQueueComplete: function () {
        if (ynfeAddPage.success) {
            $('#js_fevent_succes_message').fadeIn().fadeOut(2000);
            ynfeAddPage.success = false;
            if ($('input[name="val[ynfevent_editconfirmboxoption_value]"]:checked').val() != 'undefined') {
                $Core.ajax('fevent.copyRecurringImage',
                    {
                        type: 'POST',
                        params:
                            {
                                'sIds' : ynfeAddPage.successIds,
                                'event_id': $('#eventID').val(),
                                'confirm_type': $('input[name="val[ynfevent_editconfirmboxoption_value]"]:checked').val()
                            },
                        success: function() {
                            $('#js_fevent_done_upload').show();
                            ynfeAddPage.successIds = '';
                        }
                    }
                );
            } else {
                ynfeAddPage.successIds = '';
                $('#js_fevent_done_upload').show();
            }
        }
    },
    deleteImage: function(ele) {
        if (!ele.data('id')) return false;

        $Core.jsConfirm({message: ele.data('message')}, function () {
            $.ajaxCall('fevent.deleteImage', 'id=' + ele.data('id') + '&event_id=' + ele.data('event-id') + '&is_reload=1');
        }, function () {
        });

        return false;
    }
};
$Behavior.initynfeAddPage = function()
{
    ynfeAddPage.init();
}
;
