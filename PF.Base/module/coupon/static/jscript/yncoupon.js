	var yncoupon = {
		donate: { 
			selectPredefinedValue: function(iValue)	 {
				$('#ync_donate_amount').val(iValue);
			},
			selectOtherValue: function() {
				$('#ync_donate_amount').val('').focus();	
				
			}
		},
		confirmOnAddCoupon : function(sPhrase,sLink){

			sHtml = "";
			sHtml += '<div class="white-popup-block" style="width: 400px;">';
				sHtml += '<div style="margin-top: 20px;">';
					sHtml += sPhrase;
				sHtml += '</div>';
				sHtml += '<div style="margin-top: 10px; text-align: right;">';
					sHtml += '<button class="btn btn-sm btn-primary" onclick="$(\'#ync_is_publish\').val(1);$(\'#ync_edit_coupon_form\').submit();return true;">';
						sHtml += oTranslations['yes'];
					sHtml += '</button>';
					sHtml += '<button class="btn btn-sm btn-default" style="margin-left: 10px;" onclick="$(\'.moderation_process\').hide();$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');return false;">';
						sHtml += oTranslations['no'];
					sHtml += '</button>';
				sHtml += '</div>';
			sHtml += '</div>';

			$.magnificPopup.open({
	            items: {
				    src: sHtml,
				    type: 'inline'
	            }
	        });
		},
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
		initializeValidator: function (element) {
			jQuery.validator.messages.required = oTranslations['coupon.this_field_is_required'];
			jQuery.validator.messages.number = oTranslations['coupon.please_enter_a_valid_number'];
			jQuery.validator.messages.email = oTranslations['coupon.please_enter_a_valid_email'];
			jQuery.validator.messages.url = oTranslations['coupon.please_enter_a_valid_url'];
			
			element.validate({
				errorPlacement: function (error, element) {
					if(element.is(":radio") || element.is(":checkbox")) {
						error.appendTo(element.parent());
					} else if(element.is('.js_predefined')) {
						error.insertAfter(element.parent().parent());
					}else if(element.is('.ync_donate_amount')) {
						error.appendTo(element.parent().parent());
					}else {
						error.insertAfter(element);
					}
				}
			});

			jQuery.validator.addClassRules("ync_positive_number", {
				range:[0,10000000000]
			});
			jQuery.validator.addClassRules("ync_coupon_title_max_length", {maxlength:255});
			jQuery.validator.addClassRules("ync_coupon_short_description_max_length", {maxlength:160});
		
			
		},
		addAgreeRequired: function (){
			jQuery.validator.addMethod("agree-required", function( value, element ) {
				var result = $(element).is(":checked");
				return result;
			}, oTranslations['coupon.you_must_agree_with_terms_and_conditions']);
		},
		
		ClickAll : function () {
			$(".search-friend").each(function () {
                if(!$(this).find("input[type='checkbox']").prop("checked")) {
                    $(this).find("input[type='checkbox']").prop("checked",true);
                    $Core.searchFriend.addFriendToSelectList($(this).find("input[type='checkbox']").get(0), $(this).data('id'));
                    $(this).find('.item-outer:first').addClass('active');
                }
			});

			if($(".search-friend").length == 0)
			{
				$('input[type="checkbox"]').each(function () {
					if(!$(this).prop("checked")) {
                        $(this).prop("checked", true);
						$Core.search.addFriendToSelectList(this, $(this).val());
					}
				});
			}
			
		},

		UnClickAll : function () {
			$(".search-friend").each(function () {
                if($(this).find("input.checkbox").attr("checked") != "checked") {
                    $(this).click();
                }
			});

			if($(".search-friend").length != 0)
			{
				$('input.checkbox').each(function () {
					if($(this).attr("checked") == "checked") {
						$(this).attr('checked', false);
                        $Core.searchFriend.addFriendToSelectList(this, $(this).val());
					}
				});
			}
		},

		overridedLoadInit : function () {
			debug('$Core.loadInit() Loaded');		
		
			$('*:not(#ync_gallery_slides *, #ync_slides *)').unbind();
			
			$.each($Behavior, function() 
			{		
				this(this);
			});	
		},

		changeTypeCoupon : function (type){

			if(type == 'discount'){

				$(".special_price_block").hide();
				$(".discount_block").show();

				$("#special_price").val('');
			}
			else
			if(type == 'special_price'){

				$(".special_price_block").show();
				$(".discount_block").hide();

				$('#discount_value').val('');
				$('#discount_type').val('percentage');
				$('#discount_currency').hide();

			}
		},
		
		disable: function(a) {
			alert(1);
			if(a.attr('checked'))
	            a.parent().parent().find('.ync_disable').hide();
	        else
	            a.parent().parent().find('.ync_disable').show();
		}
	};

