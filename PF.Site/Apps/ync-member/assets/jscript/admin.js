$Core.ynmember =
{
	sUrl: '',

	url: function(sUrl)
	{
		this.sUrl = sUrl;
	},

	addSort: function()
	{
		$('.sortable ul').sortable({
				axis: 'y',
				update: function(element, ui)
				{
					$Core.ynmember.updateSort();
				},
				opacity: 0.4
			}
		);

	},
	updateSort: function()
	{
		$('.sortable').removeClass('odd');
		$('.sortable').removeClass('first');
		$('.sortable li:first').addClass('first');

		var iGroupCnt = 0;
		$('.sortable ul .group').each(function()
		{
			iGroupCnt++;
			$(this).find('input:first').val(iGroupCnt);
		});

		var iFieldCnt = 0;
		$('.sortable ul .field').each(function()
		{
			iFieldCnt++;
			$(this).find('input:first').val(iFieldCnt);
		});
	},
	actionCustomField: function(oObj, sAction, sUrl)
	{
		this.sUrl = sUrl;
		aParams = $.getParams(oObj.href);
		$('.dropContent').hide();

		switch (sAction)
		{
			case 'edit':
				$.ajaxCall('ynmember.editcustomfieldgroup	','id=' +  aParams['id'])
				break;
			case 'delete':
                $Core.jsConfirm({message: oTranslations['are_you_sure']}, function() {
					if (aParams['type'] == 'group')
					{
						$.ajaxCall('ynmember.AdminDeleteCustomFieldGroup', 'id=' + aParams['id']);
					}
					else
					{
						$.ajaxCall('ynmember.deleteField', 'id=' + aParams['id']);
					}
				});
				break;
			default:
				if (aParams['type'] == 'group')
				{
					$.ajaxCall('ynmember.toggleActiveGroup', 'id=' + aParams['id']);
				}
				else
				{
					$.ajaxCall('ynmember.toggleActiveField', 'id=' + aParams['id']);
				}
				break;
		}

		return false;
	},
	action: function(oObj, sAction)
	{
		aParams = $.getParams(oObj.href);

		$('.dropContent').hide();

		switch (sAction)
		{
			case 'edit':
				if (aParams['type'] == 'group')
				{
					window.location.href = this.sUrl + 'group/add/id_' + aParams['id'] + '/';
				}
				else
				{
					window.location.href = this.sUrl + 'add/id_' + aParams['id'] + '/';
				}
				break;

		}

		return false;
	},
	init_action_custom : function (){
		$('.js_drop_down').click(function()
		{
			eleOffset = $(this).offset();

			aParams = $.getParams(this.href);

			$('#js_cache_menu').remove();

			$('body').prepend('<div id="js_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:100; background:red;">' + $('#js_menu_drop_down').html() + '</div>');

			$('#js_cache_menu .link_menu li a').each(function()
			{
				if (this.hash == '#active' && (($('#js_field_' + aParams['id']).html() && $('#js_field_' + aParams['id']).html().match(/<del>/i)) || ($('#js_group_' + aParams['id']).html() && $('#js_group_' + aParams['id']).html().match(/<del>/i))))
				{
					$(this).html('Set to Active');
				}

				this.href = '#?id=' + aParams['id'] + '&type=' + aParams['type'] + '';
			});

			$('.dropContent').show();

			$('.dropContent').mouseover(function()
			{
				$('.dropContent').show();

				return false;
			});

			$('.dropContent').mouseout(function()
			{
				$('.dropContent').hide();
				$('.sJsDropMenu').removeClass('is_already_open');
			});

			return false;
		});

		$('.var_type').change(function()
		{
			$('#js_multi_select').hide();

			switch (this.value)
			{
				case 'select':
				case 'multiselect':
				case 'radio':
				case 'checkbox':
					$('#tbl_option_holder').show();
					$('#tbl_add_custom_option').show();
					break;
				default:
					$('#tbl_option_holder').hide();
					$('#tbl_add_custom_option').hide();
					break;
			}
		});

		if ($('.var_type').val() == 'text' || $('.var_type').val() == 'textarea')
		{
			$('#tbl_option_holder').hide();
			$('#tbl_add_custom_option').hide();
		}

		$('.js_add_custom_option').click(function()
		{
			$Core.customgroup.add();

			return false;
		});

		$('#js_create_new_group').click(function()
		{
			$('#js_field_holder').hide();
			$('#js_group_holder').show();

			return false;
		});

		$('#js_cancel_new_group').click(function()
		{
			$('#js_group_holder').hide();
			$('#js_field_holder').show();

			return false;
		});

		$('.js_delete_current_option').click(function()
		{
			if (confirm(oTranslations['custom.are_you_sure_you_want_to_delete_this_custom_option']))
			{
				aParams = $.getParams(this.href);

				$.ajaxCall('custom.deleteOption', 'id=' + aParams['id']);
			}

			return false;
		});
		$('.js_custom_change_group').click(function()
		{
			$(this).parents('ul:first').find('li').removeClass('active');
			$(this).parent().addClass('active');
			$('.js_custom_groups').hide();
			$('.js_custom_group_' + this.id.replace('group_', '')).show();

			return false;
		});
	},
	toggleGroupActivity: function(iId)
	{
		if ($('#js_group_' + aParams['id']).html().match(/<del>/i))
		{
			$('#js_group_' + aParams['id']).html($('#js_group_' + aParams['id']).html().replace('<del>', '').replace('</del>', ''));
		}
		else
		{
			$('#js_group_' + aParams['id']).html('<del>' + $('#js_group_' + aParams['id']).html() + '</del>');
		}
	},
	editCustomgroupFromPopup: function(obj)
	{
		var id = $(obj).data('id');
		$.ajaxCall('ynmember.editcustomfieldgroup	','id=' +  id);
	}
}

var SetUpMenu = function () {
	$('.sortable ul').sortable({
			axis: 'y',
			update: function (element, ui) {
				var iCnt = 0;
				$('.js_mp_order').each(function () {
					iCnt++;
					this.value = iCnt;
				});
			},
			opacity: 0.4
		}
	);

	$('.js_drop_down').on("click", function () {
		eleOffset = $(this).offset();

		aParams = $.getParams(this.href);

		$('#js_cache_menu').remove();

		$('body').prepend('<div id="js_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:100; background:red;">' + $('#js_menu_drop_down').html() + '</div>');

		$('#js_cache_menu .link_menu li a').each(function () {
			this.href = '#?id=' + aParams['id'];
		});

		$('.dropContent').show();

		$('.dropContent').mouseover(function () {
			$('.dropContent').show();

			return false;
		});

		$('.dropContent').mouseout(function () {
			$('.dropContent').hide();
			$('.sJsDropMenu').removeClass('is_already_open');
		});

		return false;
	});
}
$Ready(function () {
	if($('#ynuv_custom_field_manage').length != 0){
		$Core.ynmember.addSort();
		$Core.ynmember.init_action_custom();
	}
	else if($('#ynuv_category_manage').length != 0){
		SetUpMenu();
	}
});

