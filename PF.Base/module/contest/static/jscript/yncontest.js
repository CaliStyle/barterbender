var yncontest = {
	announcement: {
		editAnnouncement : function(id) {
        $('#contest_announcement_headline').val($('#contest_headline_'+id).html());
        $('#contest_announcement_link').val($('#contest_link_'+id).html());
        $('#contest_announcement_content').val($('#contest_content_'+id).html());
        $('#yncontest_announcement_id').val(id);
        $('#contest_add_announcement').hide();
        $('#contest_update_announcement').show();
        $('#tabs-3').scrollTop(0);

		},
		cancelEditAnnouncement : function() {
            $('#contest_announcement_headline').val('');
            $('#contest_announcement_link').val('');
            $('#contest_announcement_content').val('');
            $('#yncontest_announcement_id').val('');
            $('#contest_add_announcement').show();
        	$('#contest_update_announcement').hide();
		},
		deleteAnnouncement : function(id, phrase) {
			if(confirm(phrase)) 
			{
				$.ajaxCall('contest.deleteAnnouncement', 'announcement_id=' + id); 

				if( $('#yncontest_announcement_id').val() == id) 
				{	
					$('#core_js_contest_form_announcement')[0].reset(); 
					$('#contest_add_announcement').show();
        			$('#contest_update_announcement').hide();
				}
			}

			return false;
		}

	},
	confirmActionOnContest: function(iContestId,sType){
		if(['close', 'delete', 'publish', 'approve', 'deny'].indexOf(sType) < 0) {
			return;
		}
        $Core.jsConfirm({}, function() {
            $.ajaxCall('contest.' + sType + 'Contest', '&contest_id='+iContestId+'&amp;is_owner=1', 'GET');
        }, function(){});
	},	
	confirmOnAddConstest : function(sLink){
        $Core.jsConfirm({message: oTranslations['contest.warning_before_publishing']}, function() {
            $('#yncontest_main_info_form').submit();
        }, function(){});
	},
	initForTabView : function () {
		if($('#init_tab_one_time').val() == '0'){
			$('#tabs_view').tabs();
			$('#tabs_view *').addClass('dont-unbind');
			$('#init_tab_one_time').val(1);
		}
	},
	showErrorMessageAndHide: function(jEle) {
		//jEle is a jquery object
		jEle.show();
		setTimeout(function() {
			jEle.hide(500);
		}, 4000);
	},
	initDatePickerEvent: function() {
		if($('#yncontest_main_info_form').length) {
            $('.js_date_picker', $('#yncontest_main_info_form')).each(function () {
                var $this = $(this),
                    $holder = $this.closest('.js_datepicker_holder'),
                    $year = $('.js_datepicker_year', $holder),
                    minYear,
                    maxYear,
                    sFormat = oParams['sDateFormat'];

                if (typeof pf_select_date_sort_desc !== 'undefined' && pf_select_date_sort_desc) {
                    minYear = $('option:last', $year).val() || 0;
                    maxYear = $('option:eq(0)', $year).val() || $('option:eq(1)', $year).val() || 0;
                } else {
                    minYear = $('option:eq(0)', $year).val() || $('option:eq(1)', $year).val() || 0;
                    maxYear = $('option:last', $year).val() || 0;
                }

                sFormat = sFormat.charAt(0) + '/' + sFormat.charAt(1) + '/' + sFormat.charAt(2);
                sFormat = sFormat.replace('D', 'd').replace('M', 'm').replace('Y', 'yy');

                $this.datepicker('destroy');

                if (!minYear) {
                    minYear = '-100';
                }
                if (!maxYear) {
                    maxYear = '+10';
                }

                $('.js_date_picker', $holder).prop('readonly', true);

                $this.datepicker({
                    dateFormat: sFormat,
                    changeYear: true,
                    yearRange: minYear + ':' + maxYear,
                    beforeShow: function () {
                        $this.trigger("datepicker.before_show", minYear, maxYear);
                    },
                    onSelect: function (dateText) {
                        var aParts = explode('/', dateText),
                            sMonth,
                            sDay,
                            sYear;

                        switch (oParams['sDateFormat']) {
                            case 'YMD':
                                sMonth = ltrim(aParts[1], '0');
                                sDay = ltrim(aParts[2], '0');
                                sYear = aParts[0];
                                break;
                            case 'DMY':
                                sMonth = ltrim(aParts[1], '0');
                                sDay = ltrim(aParts[0], '0');
                                sYear = aParts[2];
                                break;
                            default:
                                sMonth = ltrim(aParts[0], '0');
                                sDay = ltrim(aParts[1], '0');
                                sYear = aParts[2];
                                break;
                        }

                        $('.js_datepicker_month', $holder).val(sMonth).trigger('change');
                        $('.js_datepicker_day', $holder).val(sDay).trigger('change');
                        $('.js_datepicker_year', $holder).val(sYear).trigger('change');
                    }
                });

                $holder.find('.js_datepicker_image').click(function () {
                    $this.datepicker('show');
                });
            });

            $('#begin_time_hour, #begin_time_minute, #end_time_hour, #end_time_minute, #start_time_hour, #start_time_minute, #stop_time_hour, #stop_time_minute, #start_vote_hour, #start_vote_minute, #stop_vote_hour, #stop_vote_minute, .js_datepicker_month, .js_datepicker_day, .js_datepicker_year', $('#yncontest_main_info_form')).on('change', function() {
                yncontest.checkInvalidDatePicker($('#yncontest_main_info_form').get(0));
            });
		}
	},
	checkInvalidDatePicker: function(obj) {
		if($(obj).length && $(obj).data('validate')) {
			let form = $(obj);
			let date = new Date();
            let currentTime = date.getTime() + (date.getTimezoneOffset() * 60000);
            let beginDate  = form.find('#begin_time_month').val() + '/' + form.find('#begin_time_day').val() + '/' + form.find('#begin_time_year').val();
            if(form.find('#begin_time_hour').length && form.find('#begin_time_minute').length) {
                beginDate += ' ' + form.find('#begin_time_hour').val() + ':' + form.find('#begin_time_minute').val() + ':00';
            }
            let beginTime = (new Date(beginDate)).getTime();

            let endDate  = form.find('#end_time_month').val() + '/' + form.find('#end_time_day').val() + '/' + form.find('#end_time_year').val();
            if(form.find('#end_time_hour').length && form.find('#end_time_minute').length) {
                endDate += ' ' + form.find('#end_time_hour').val() + ':' + form.find('#end_time_minute').val() + ':00';
            }
            let endTime = (new Date(endDate)).getTime();

            let startDate  = form.find('#start_time_month').val() + '/' + form.find('#start_time_day').val() + '/' + form.find('#start_time_year').val();
            if(form.find('#start_time_hour').length && form.find('#start_time_minute').length) {
                startDate += ' ' + form.find('#start_time_hour').val() + ':' + form.find('#start_time_minute').val() + ':00';
            }
            let startTime = (new Date(startDate)).getTime();

            let stopDate  = form.find('#stop_time_month').val() + '/' + form.find('#stop_time_day').val() + '/' + form.find('#stop_time_year').val();
            if(form.find('#stop_time_hour').length && form.find('#stop_time_minute').length) {
                stopDate += ' ' + form.find('#stop_time_hour').val() + ':' + form.find('#stop_time_minute').val() + ':00';
            }
            let stopTime = (new Date(stopDate)).getTime();

            let startVoteDate  = form.find('#start_vote_month').val() + '/' + form.find('#start_vote_day').val() + '/' + form.find('#start_vote_year').val();
            if(form.find('#start_vote_hour').length && form.find('#start_vote_minute').length) {
                startVoteDate += ' ' + form.find('#start_vote_hour').val() + ':' + form.find('#start_vote_minute').val() + ':00';
            }
            let startVoteTime = (new Date(startVoteDate)).getTime();

            let stopVoteDate  = form.find('#stop_vote_month').val() + '/' + form.find('#stop_vote_day').val() + '/' + form.find('#stop_vote_year').val();
            if(form.find('#stop_vote_hour').length && form.find('#stop_vote_minute').length) {
                stopVoteDate += ' ' + form.find('#stop_vote_hour').val() + ':' + form.find('#stop_vote_minute').val() + ':00';
            }
            let stopVoteTime = (new Date(stopVoteDate)).getTime();
			
            let valid = true;
            let errorText = '<label class="error js_datepicker_validate_error_message">{text}</label>';
            let errorElement = '';
            if (startTime < beginTime) {
                valid = false;
                errorElement = 'input[name="js_start_time__datepicker"]';
                errorText = errorText.replace('{text}', oTranslations['contest.the_start_time_of_submitting_must_be_greater_than_or_equal_to_the_start_time_of_contest']);
            } else if (stopTime <= startTime) {
                valid = false;
                errorElement = 'input[name="js_stop_time__datepicker"]';
                errorText = errorText.replace('{text}', oTranslations['contest.the_end_time_of_submitting_must_be_greater_than_the_start_time_of_it']);
            } else if (stopTime <= currentTime) {
                valid = false;
                errorElement = 'input[name="js_stop_time__datepicker"]';
                errorText = errorText.replace('{text}', oTranslations['contest.the_end_time_of_submitting_must_be_greater_than_current_time']);
            } else if (stopVoteTime <= startVoteTime) {
                valid = false;
                errorElement = 'input[name="js_stop_vote__datepicker"]';
                errorText = errorText.replace('{text}', oTranslations['contest.the_end_time_of_voting_must_be_greater_than_the_start_time_of_it']);
            } else if (stopVoteTime < stopTime) {
                valid = false;
                errorElement = 'input[name="js_stop_vote__datepicker"]';
                errorText = errorText.replace('{text}', oTranslations['contest.the_end_time_of_voting_must_be_greater_than_or_equal_to_the_end_time_of_submitting']);
            } else if (endTime < stopVoteTime) {
                valid = false;
                errorElement = 'input[name="js_end_time__datepicker"]';
                errorText = errorText.replace('{text}', oTranslations['contest.the_end_time_of_contest_must_be_greater_than_or_equal_to_the_end_time_of_voting']);
            }

            form.find('.js_datepicker_validate_error_message').remove();

			if(!valid) {
                form.find(errorElement).closest('.ynsui_timepicker_withlabel').append(errorText);
			}

			return !valid;
		}
		return false;
	},
	initializeValidator: function (element) {
		jQuery.validator.messages.required = oTranslations['contest.this_field_is_required'];
		jQuery.validator.messages.url = oTranslations['contest.please_enter_a_valid_url'];
		jQuery.validator.messages.range = oTranslations['contest.please_enter_an_amount_greater_or_equal'] + ' {0} ' + "" ;
		jQuery.validator.messages.accept = oTranslations['contest.please_enter_a_value_with_a_valid_extension'] ;
		
		element.validate({
			errorPlacement: function (error, element) {
				if(element.is(":radio") || element.is(":checkbox")) {
					error.appendTo(element.parent());
				} else {
					error.insertAfter(element);
				}
			}
		});

		jQuery.validator.addClassRules("yn_positive_number", {
			range:[0,10000000000]
		});

		jQuery.validator.addClassRules("yn_positive_number_greater_than_0", {
			range:[1,10000000000]
		});

		jQuery.validator.addClassRules("yn_validation_file_type", {
			accept: "jpg|gif|jpeg|png"
		});


		jQuery.validator.addClassRules("yn_contest_title_max_length", {maxlength:255});
		jQuery.validator.addClassRules("yn_contest_short_description_max_length", {maxlength:160});
	
		
	},
	addEntry : {
		createAjaxUrlForAddEntry: function () {
			var title = encodeURIComponent($('#yncontest_entry_title').val());
			var summary = encodeURIComponent($('#yncontest_entry_summary').val());
			var item_id = $('#yncontest_item_id').val();
			var item_type = $('#yncontest_item_type').val();
			var contest_id = $('#yncontest_contest_id').val();
			if($("#ync_select_adv_module").length){
				var iSource = $("#ync_select_adv_module").val();
			}
			else
			{
				var iSource = $("#ync_source_add_entry_id").val();
			}
			return $.ajaxBox('contest.submitEntry', "height=400&width=500" + 
				'&summary=' + summary +
				'&title=' + title + 
				'&item_id=' + item_id + 
				'&item_type=' + item_type + 
				'&contest_id=' + contest_id  + 
				'&source_id=' + iSource
				);
		},

		createAjaxUrlForSubmitEntry: function () {
			var sUrl = yncontest.addEntry.createAjaxUrlForAddEntry();
			return sUrl + '&is_submit=1';
		},

		initializeClickOnEntryItem: function() {
			$('.yncontest_add_entry_item').click(function() {
				$('#yncontest_item_id').val($(this).attr('entry_item_id'));
				yncontest.addEntry.removeSelectedEntryItem();
				$(this).addClass('select');
			});
		},

		removeSelectedEntryItem: function() {
			$('.yncontest_add_entry_item.select').removeClass('select');
		},

		previewEntry: function(tb_phrase) {
			if(yncontest.addEntry.validateAddEntryForm())
			{
				tb_show(tb_phrase, yncontest.addEntry.createAjaxUrlForAddEntry());
			}
			
		},

		validateAddEntryForm: function() {
			if(parseInt($('#yncontest_item_id').val()) == 0)
			{
				yncontest.showErrorMessageAndHide($('#yncontest_must_select_an_item'));
				return false;
			}

			if($('#yncontest_entry_title').val() == '' || $('#yncontest_entry_summary').val() == '')
			{
				yncontest.showErrorMessageAndHide($('#yncontest_title_summary_required'));
				return false;
			}

			if($('#yncontest_entry_title').val().length > 255 )
			{
				yncontest.showErrorMessageAndHide($('#yncontest_title_max_length'));
				return false;
			}


			return true;
		},
		submitAddEntry: function() {
			if(yncontest.addEntry.validateAddEntryForm())
			{	
				$('#yncontest_submit_add_entry_button').attr('disabled', 'disabled');
				$.ajaxCall(yncontest.addEntry.createAjaxUrlForSubmitEntry());
			}

		},
		setChosenItem: function(item_id) {
			$('#yncontest_entry_item_' + item_id).trigger('click');
		},
		addAjaxForCreateNewItem: function(contest_id, contest_type) {
			//contest_type is integer
			$('#yncontest_create_new_item a').click(function() {
				$.ajaxCall('contest.setContestSession', 'contest_id=' + contest_id + '&contest_type=' + contest_type, 'GET');
				window.location = $(this).attr('href');
			});

			
		},
		changeVideoSource : function(obj,iContestId){
			var sCurrentURL = $(obj).data('url'),
				sNewCurrentURL = sCurrentURL.replace(/&source=[0-9]+/g,""),
				sNewCurrentURL = sNewCurrentURL +'&source=' + $(obj).val();
				window.location = sNewCurrentURL;
			return false;		
		},
        changeBlogSource : function(obj,iContestId){
            var sCurrentURL = $(obj).data('url'),
                sNewCurrentURL = sCurrentURL.replace(/&source=[0-9]+/g,""),
                sNewCurrentURL = sNewCurrentURL +'&source=' + $(obj).val();
            window.location = sNewCurrentURL;
            return false;
        }
	},

	pay : {
		addRemoveFees: function() {
			var total = 0;
			$('.yncontest_fee').each(function() {

				if($(this).is(':checked') || $(this).attr('ynchecked')== 'checked')
				{
					var current = parseFloat($(this).attr('fee_value'));
					total = total + current;
				}
			});

			$('#yn_contest_total_fee').html(total);

		},

		bindOnclickAddRemoveFees : function() {
			$('.yncontest_fee').click(function() {
				yncontest.pay.addRemoveFees();
			});
		}
	},

	join : {
		showJoinContestPopup : function(contest_id, popup_phrase) {
			tb_show(popup_phrase, $.ajaxBox('contest.showJoinPopup', "width=450&contest_id=" + contest_id));
		},
		submitJoinContest : function(contest_id) {
			if(yncontest.join.validateJoinForm())
			{
				$('#yncontest_join_button').attr('disabled', 'disabled');
				$('#yn_contest_waiting_join').show();
				$.ajaxCall('contest.joinContest', 'contest_id=' + contest_id, 'GET');
			}
			
		},
		validateJoinForm : function() {
			if($('#yncontest_join_agree_term_condition').is(':checked'))				
			{
				return true;
			}	
			else
			{
				$('#yncontest_must_agree').show();
				return false;
			}
		}
	},

	invite : {
		clickAll : function () {
            $(".search-friend").each(function () {
                var th = $(this);
                var oCheckbox = $('#js_friends_checkbox_' + th.data('id'));
                if (!oCheckbox.prop('checked')) {
                    oCheckbox.prop('checked', true);
                    var oCheckboxDomElement = oCheckbox.get(0);
                    if(oCheckboxDomElement)
					{
                        $Core.searchFriend.addFriendToSelectList(oCheckboxDomElement, th.data('id'));
                        th.find('.item-outer:first').addClass('active');
					}
                }
            });

            if($(".search-friend").length == 0)
            {
                $('input.checkbox').each(function () {
                    if($(this).attr("checked") != "checked") {
                        $(this).attr('checked', 'checked');
                        addFriendToSelectList(this, $(this).val());
                    }
                });
            }


		},

		unClickAll : function () {
            $(".search-friend").each(function () {
                var th = $(this);
                if ($('#js_friends_checkbox_' + th.data('id')).prop('checked')) {
                    $(this).click();
                }
            });

            if($(".search-friend").length == 0)
            {
                $('input.checkbox').each(function () {
                    if($(this).attr("checked") == "checked") {
                        $(this).attr('checked', false);
                        addFriendToSelectList(this, $(this).val());
                    }
                });
            }
		},
	},

	addContest : {
		addCategoryJsEventListener: function() {
			$('.js_mp_category_list').change(function()
			{	  
				var iParentId = parseInt(this.id.replace('js_mp_id_', ''));
		
				$('.js_mp_category_list').each(function()
				{
					if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId)
					{
						$('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();				
				
						this.value = '';
					}
				});
		
				$('#js_mp_holder_' + $(this).val()).show();
			});		

	
			$('.hover_action').each(function()
			{
				$(this).parents('.js_outer_video_div:first').css('width', this.width + 'px');
			});	
		},

		publishContest : function() {
			if($("#yncontest_main_info_form").valid())
			{	
				$("#yncontest_main_info_form").ajaxCall('contest.addNewContest');
			}
		},

		showPayPopup: function(contest_id) {
			tb_show("Register Services", $.ajaxBox('contest.showPayPopup', "width=400&contest_id=" + contest_id));
		},
		submitPayForm: function(contest_id) {
			$('#yn_contest_waiting_pay').show();
			$('#yncontest_pay_publish').attr('disabled', 'disabled');
			$('#yncontest_pay_cancel').attr('disabled', 'disabled');
			$("#yncontest_pay_form").ajaxCall('contest.processPayForPublishContest', 'contest_id=' + contest_id);
		},
		disableFields: function() {
			if(parseInt($('#yncontest_is_should_disable').val()))
			{
				$('.js_mp_category_list').attr("disabled", "disabled");
				$('.contest_add.contest_type_radio').attr("disabled", "disabled");
				$('#yncontest_add_contest_name').attr("disabled", "disabled");
				$('#image').attr("disabled", "disabled");
				$('.js_date_picker ').attr("disabled", "disabled");
				$('#start_submit_time_hour').attr("disabled", "disabled");
				$('#start_submit_time_minute').attr("disabled", "disabled");

				$('#stop_submit_time_hour').attr("disabled", "disabled");
				$('#stop_submit_time_minute').attr("disabled", "disabled");

				$('#end_time_hour').attr("disabled", "disabled");
				$('#end_time_minute').attr("disabled", "disabled");

				$('.yncontest_start_submit_time *').unbind('click');
				$('.yncontest_stop_submit_time *').unbind('click');
				$('.yncontest_end_time *').unbind('click');

				$('#maximum_entry').attr("disabled", "disabled");
				$('.privacy_setting_active').unbind('click');
				$('.privacy_setting_active').click(function(){
					return false;
				})
			}
		}
	},
	
    homepage: {
        changeFilter: function() {
            if ($('#entries-filter').val() == 'recent') {
                $('.most-voted-entries').hide();
                $('.recent-entries').show();
            }
            if ($('#entries-filter').val() == 'most_voted') {
                $('.recent-entries').hide();
                $('.most-voted-entries').show();
            }
        },
        
        changeType: function() {
            var url = window.location.href;
            url = url.replace(/\&type=.*/g, '');

            url = url + '&type=' + $('#js_select_type').val();

            window.location.href = url;
        },
    },

};

window.yncontest = yncontest;

$("#yncontest_promote_contest_badge_code_textarea").on('focus', function() {
    var $this = $(this);
    $this.select();

    // Work around Chrome's little problem
    $this.mouseup(function() {
        // Prevent further mouseup intervention
        $this.unbind("mouseup");
        return false;
    });
});