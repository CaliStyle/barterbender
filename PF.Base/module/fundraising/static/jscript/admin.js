
$Core.fundraising =
{
	sUrl: '',
	
	url: function(sUrl)
	{
		this.sUrl = sUrl;
	},	
	
	action: function(oObj, sAction)
	{
		aParams = $.getParams(oObj.href);	
		
		$('.dropContent').hide();	
		
		switch (sAction)
		{
			case 'edit':
				window.location.href = this.sUrl + 'add/id_' + aParams['id'] + '/';
				break;
			case 'delete':

					linkdelete = this.sUrl + 'delete_' + aParams['id'] + '/';
					// Open directly via API
					var sHtml = '';
					sHtml += '<div class="white-popup-block" style="width: 300px;">'; 
						sHtml += '<div>'; 
							sHtml += oTranslations['confirm_delete_category_of_fundraising'];
						sHtml += '</div>'; 
						sHtml += '<div style="margin-top: 10px; text-align: right;">'; 
							sHtml += '<button onclick="window.location.href =\''+linkdelete+'\';">'; 
								sHtml += oTranslations['yes'];
							sHtml += '</button>'; 
							sHtml += '<button style="margin-left: 10px;" onclick="$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">'; 
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

				break;				
			default:
			
				break;	
		}
		
		return false;
	}
}

$(function()
{
	$('.sortable ul').sortable({
			axis: 'y',
			update: function(element, ui)
			{
				var iCnt = 0;
				$('.js_mp_order').each(function()
				{
					iCnt++;
					this.value = iCnt;
				});
			},
			opacity: 0.4
		}
	);	
	
	$('.js_drop_down').click(function()
	{		
		eleOffset = $(this).offset();
		
		aParams = $.getParams(this.href);
		
		$('#js_cache_menu').remove();
		
		$('body').prepend('<div id="js_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:100; background:red;">' + $('#js_menu_drop_down').html() + '</div>');
		
		$('#js_cache_menu .link_menu li a').each(function()
		{			
			this.href = '#?id=' + aParams['id'];			
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
	
});