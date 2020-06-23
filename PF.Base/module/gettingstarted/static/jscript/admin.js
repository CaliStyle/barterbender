$Core.gettingstarted =
{
	sUrl: '',
	
	param: function(sUrl, lang_id)
	{
		this.sUrl = sUrl;
        this.lang_id = lang_id;
	},	
	
	action: function(oObj, sAction)
	{
		aParams = $.getParams(oObj.href);	
		
		$('.dropContent').hide();	
		
		switch (sAction)
		{
			case 'edit':
				window.location.href = this.sUrl + 'addarticlecategory/id_' + aParams['id'] + '/';
				break;
			case 'delete':
				if (confirm(oTranslations['gettingstarted.are_you_sure_this_will_delete_all_categories_that_belong_to_this_category_and_cannot_be_undone']))
				{
					window.location.href = this.sUrl + 'managearticlecategory/lang_' + this.lang_id + '/delete_' + aParams['id'] + '/';
				}
				break;			
			default:
			
				break;	
		}
		
		return false;
	}
}