
$Behavior.addNewCompany = function()
{
	$('.js_jobposting_company_change_group').click(function()
	{
		if ($(this).parent().hasClass('locked'))
		{
			return false;
		}
		
		aParts = explode('#', this.href);
		
		$('.js_jobposting_company_block').hide();
		$('#js_jobposting_company_block_' + aParts[1]).show();
		$(this).parents('.header_bar_menu:first').find('li').removeClass('active');
		$(this).parent().addClass('active');
		$('#js_jobposting_company_add_action').val(aParts[1]);
	});
	
	$('.js_mp_jobpost_category_list').change(function()
	{
		var iNo = parseInt(this.id.substr(9, 1));
        var iParentId = parseInt(this.id.replace('js_mp_id_' + iNo + '_', ''));
		
		$('.js_mp_jobpost_category_list').each(function()
		{
			if (parseInt(this.id.replace('js_mp_id_' + iNo + '_', '')) > iParentId)
			{
				$('#js_mp_jobpost_holder_' + iNo + '_' + this.id.replace('js_mp_id_' + iNo + '_', '')).hide();
				
				this.value = '';
			}
		});
		
		$('#js_mp_jobpost_holder_' + iNo + '_' + $(this).val()).show();
	});	
};