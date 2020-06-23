
var ynfundraising = {
	donate: {
		selectPredefinedValue: function(iValue)	 {
			$('#ynfr_donate_amount').val(iValue);
		},
		selectOtherValue: function() {
			$('#ynfr_donate_amount').val('').focus();

		}
	},
	addCategoryJsEventListener: function() {
		if ($('#ynfr_edit_campaign_form').length == 0)
			return;

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
		jQuery.validator.messages.required = oTranslations['this_field_is_required'];
		jQuery.validator.messages.number = oTranslations['please_enter_a_valid_number'];
		jQuery.validator.messages.email = oTranslations['please_enter_a_valid_email'];
		jQuery.validator.messages.url = oTranslations['please_enter_a_valid_url'];

		element.validate({
			errorPlacement: function (error, element) {
				if(element.is(":radio") || element.is(":checkbox")) {
					error.appendTo(element.parent());
				} else if(element.is('.js_predefined')) {
					error.insertAfter(element.parent().parent());
				}else if(element.is('.ynfr_donate_amount')) {
					error.appendTo(element.parent().parent());
				}else {
					error.insertAfter(element);
				}
			}
		});

		jQuery.validator.addClassRules("ynfr_positive_number", {
			range:[0,10000000000]
		});
		jQuery.validator.addClassRules("ynfr_campaign_title_max_length", {maxlength:255});
		jQuery.validator.addClassRules("ynfr_campaign_short_description_max_length", {maxlength:500});


	},
	addAgreeRequired: function (){
		jQuery.validator.addMethod("agree-required", function( value, element ) {
			var result = $(element).is(":checked");
			return result;
		}, oTranslations['you_must_agree_with_terms_and_conditions']);
	},

	ClickAll : function () {
        $('#js_friend_loader input.js_global_item_moderate').each(function(i,e) {
            if($(e).is(':checked') === false) {
                var parent = $(e).closest('.search-friend');
                parent.data('can-message','true');
                $Core.searchFriend.selectFriend(parent);
            }
        });

	},

	UnClickAll : function () {
        if ($('#deselect_all_friends').length) {
            $('#deselect_all_friends').trigger('click');
        } else {
            $('#js_friend_loader input.js_global_item_moderate').each(function(i,e) {
                if($(e).is(':checked') === true) {
                    var parent = $(e).closest('.search-friend');
                    $Core.searchFriend.selectFriend(parent);
                }
            });
        }
	},
};
$Ready(function () {
        ynfundraising.addCategoryJsEventListener();
    }
);