
	var ynjobposting = {
        advSearchDisplay: function(title_search)
        {
            var $form = $('#jobposting_adv_search');
            var $flag = $('#flag_advancedsearch');
            if($flag.val() == 1)
            {
                $form.hide();
                $flag.val(0); 
            }
            else
            {
                $form.show();
                $flag.val(1);
            }        

            return false; 
        },
        advSearchCompanyDisplay: function(title_search)
        {
            var $form = $('#jobposting_adv_search_company');
            var $flag = $('#flag_advancedsearch_company');
            if($flag.val() == 1)
            {
                $form.hide();
                $flag.val(0); 
            }
            else
            {
                $form.show();
                $flag.val(1);
            }        

            return false; 
        },
        showmoretab_company : function()
        {
            $('#tabs2').toggle();
            $('#tabs3').toggle();
            $('#tabs4').toggle();
          //  $('#divshowmore').hide();
        },


		overridedLoadInitForTabView : function () {
			debug('$Core.loadInit() Loaded');		
			
			$('*:not(#tabs_view *)').unbind();

			
			$.each($Behavior, function() 
			{		
				this(this);
			});
		},
        /*,
		overridedLoadInitForTabViewAgain : function () {
			debug('$Core.loadInit() Loaded');
            
			$('*:not(.row_edit_bar_action *)').unbind();
            $('*:not(.star-rating, .dont-unbind)').unbind();
			$.each($Behavior, function() 
			{		
				this(this);
			});
		},

        overridedLoadInit : function () {

                debug('$Core.loadInit() in jobposting.js Loaded');


               // $('*:not(#ync_gallery_slides *, #ync_slides *)').unbind();
                $('*:not(#ync_slides *)').unbind();
                $('*:not(.star-rating, .dont-unbind)').unbind();

                $.each($Behavior, function()
                {
                    this(this);
                });


		},
		*/

        application: {
            view: function(id, tb_name) {
                tb_show(tb_name, $.ajaxBox('jobposting.blockViewApplication', 'width=450&height=400&id=' + id));
            },
            
            confirm_delete: function(id, phrase_confirm) {
                if(confirm(phrase_confirm)) {
                    $.ajaxCall('jobposting.deleteApplication', 'id=' + id);
                }
            },
            
            reject: function(id) {
                $.ajaxCall('jobposting.updateApplicationStatus', 'id=' + id + '&status=rejected');
            },
            
            pass: function(id) {
                $.ajaxCall('jobposting.updateApplicationStatus', 'id=' + id + '&status=passed');
            }
        },
        
        company: {
            popupSponsor : function (id, fee, type){
                sHtml = "";
                sHtml += '<div class="white-popup-block" style="width: 400px;">';
                sHtml += '<div class="ynjobposting-edit-confirm-box-title">';
                sHtml += oTranslations['jobposting.do_you_want_to_sponsor_your_company_width'].replace('{fee}', fee);
                sHtml += '</div>';
                sHtml += '<br/>';
                sHtml += '<div class="ynjobposting-edit-confirm-box-button">';
                sHtml += '<button class="btn btn-sm btn-primary" onclick="ynjobposting.company.confirmpopupSponsor('+ id +','+ type +');$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">';
                sHtml += oTranslations['friend.confirm'];
                sHtml += '</button>';
                if(type == '1') {
                    sHtml += '<button class="btn btn-sm btn-danger" style="margin-left: 10px;" onclick="$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');$(\'#core_js_jobposting_company_form\').submit();">';
                }
                else{
                    sHtml += '<button class="btn btn-sm btn-danger" style="margin-left: 10px;" onclick="$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">';
                }
                sHtml += oTranslations['core.cancel'];
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
            },
            confirmpopupSponsor : function(id, type){
              if(type == '1'){
                  ynjobposting.company.confirmSponsor(id);
                  $('#core_js_jobposting_company_form').submit();
              }
              else{
                  ynjobposting.company.sponsor(id);
              }
            },
        	confirmSponsor: function(permission) {
				if (permission) {
						$('#js_jc_sponsor_checkbox').attr('checked', true);
				}
				return true;
        	},
            
            sponsor: function(id) {
                    $('.js_jc_sponsor_btn').attr('disabled', true).addClass('button_off');
                    $('.js_jc_add_loading').html($.ajaxProcess(oTranslations['jobposting.processing'])).show();
                    $.ajaxCall('jobposting.sponsorCompany', 'id=' + id);

            },
            
            submitForm: function() {
                if(ynjobposting.company.totalFee() > 0) {
                    if(!confirm(oTranslations['jobposting.save_and_pay_fee_for_selected_package'].replace('{fee}', ynjobposting.company.totalFee()))) {
                        return false;
                    }
                }
                return true;
            },
            
            payPackages: function(id) {
                if(ynjobposting.company.totalCheck() > 0) {
                   	var val = $('input[name="val[packages][]"]').serialize();
                   	var currency = $('#currency_jobposting').val();
                    if(confirm(oTranslations['jobposting.pay_fee_for_selected_packages'].replace('{fee}', ynjobposting.company.totalFee() + ' ' + currency))) {
                    	$('.js_jc_package').attr('disabled', true);
                        $('.js_jc_pay_packages_btn').attr('disabled', true).addClass('button_off');
            	        $('.js_jc_add_loading').html($.ajaxProcess(oTranslations['jobposting.processing'])).show();
            	        $.ajaxCall('jobposting.payPackages', 'id=' + id + '&' + val);
                    }
                }
            },
            
            updatePayPackagesBtn: function() {
            	if(ynjobposting.company.totalCheck() > 0) {
                    $('.js_jc_pay_packages_btn').attr('disabled', false).removeClass('button_off');
                } else {
                    $('.js_jc_pay_packages_btn').attr('disabled', true).addClass('button_off');
                }
            },
            
            totalCheck: function() {
                var total = 0;
            	$('.js_jc_package').each(function() {
            		if($(this).is(':checked')) {
            			total++;
            		}
            	});
                return total;
            },
            
            totalFee: function() {
                var total = 0;
            	$('.js_jc_package').each(function() {
            		if($(this).is(':checked')) {
            			total += parseFloat($(this).attr('fee_value'));
            		}
            	});
                return total;
            },
            
            searchJobs: function(url) {
                var title = js_jc_form.search_title.value;
                var from = js_jc_form.js_from__datepicker.value.replace(/\//g,'-');
                var to = js_jc_form.js_to__datepicker.value.replace(/\//g,'-');
                var status = js_jc_form.search_status.value;
                
                var action = url + '&search=jobs&title=' + title + '&from=' + from + '&to=' + to + '&status=' + status;
                window.location.href = action;
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


            }
        },
        processCustomField: function(oObj, bIsEdit){
            $('#js_add_field_button').attr('disabled', true);
            $('#js_add_field_loading').html($.ajaxProcess(bIsEdit ? oTranslations['jobposting.updating'] : oTranslations['jobposting.adding'])).show();
            var sAjaxCall = 'jobposting.' +  (bIsEdit ? 'updateField' : 'addField');
            $.ajaxCall(sAjaxCall, $(oObj).serialize(), 'post');
            return false;
        }
	};

	window.ynjobposting = ynjobposting;

