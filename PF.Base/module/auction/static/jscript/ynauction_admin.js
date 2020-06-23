var ynauction_admin = {
	pt : []
    , params : false
	, setParams : function(params) {
		ynauction.params = JSON.parse(params);
	}
	, init: function()
	{
	}
    , confirmActivateDeactivate: function(sElementId){
		sHtml = "";
		sHtml += '<div class="white-popup-block" style="width: 300px;">';
			sHtml += '<div>';
				sHtml += '<span style="font-weight: bold; font-size: 16px;">';
				sHtml += oTranslations['auction.confirm'];
				sHtml += '</span>';
				sHtml += '<br>';
				sHtml += oTranslations['auction.are_you_sure_you_want_to_un_feature_this_auction'];
			sHtml += '</div>';
			sHtml += '<div style="margin-top: 10px; text-align: right;">';
				sHtml += '<button class="btn btn-sm btn-primary" onclick="ynauction_admin.callAjaxActivateDeactivate(\'' + sElementId + '\');">';
					sHtml += oTranslations['auction.yes'];
				sHtml += '</button>';
				sHtml += '<button class="btn btn-sm btn-default" style="margin-left: 10px;" onclick="$(\'.moderation_process\').hide();$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">';
					sHtml += oTranslations['auction.no'];
				sHtml += '</button>';
			sHtml += '</div>';
		sHtml += '</div>';

		$.magnificPopup.open({
            items: {
			    src: sHtml,
			    type: 'inline'
            }
        });
	}
    , callAjaxActivateDeactivate: function(sElementId){
        var e = $('#' + sElementId);
        if (e.length == 0)
        {
            return;
        }

        var aParams = $.getParams(e.attr('link_ajax'));
        var sParams = '';
        for (sVar in aParams)
        {			
            sParams += '&' + sVar + '=' + aParams[sVar] + '';
        }
        sParams = sParams.substr(1, sParams.length);

        if (e.hasClass('js_remove_default'))
        {
            $('.js_remove_default').each(function(){
                e.parent().parent().find('.js_item_is_active:first').hide();
                e.parent().parent().find('.js_item_is_not_active:first').show();
            });
        }		

        if (aParams['active'] == '1')
        {
            e.parent().parent().find('.js_item_is_not_active:first').hide();
            e.parent().parent().find('.js_item_is_active:first').show();
        }
        else
        {
            e.parent().parent().find('.js_item_is_active:first').hide();
            e.parent().parent().find('.js_item_is_not_active:first').show();
        }

        $('.mfp-close-btn-in .mfp-close').trigger('click');

        $Core.ajaxMessage();
        
        $.ajaxCall(aParams['call'], sParams + '&global_ajax_message=true');
        
        return false;
    }
    , confirmDeleteAuctions: function(sElementId){
        var iCnt = 0;
   		$("input:checkbox").each(function()
   		{
    		if (this.checked)
    		{
   				iCnt++;
    		}	
   		});
        if (iCnt == 0)
        {
            return;
        }

		sHtml = "";
		sHtml += '<div class="white-popup-block" style="width: 300px;">'; 
			sHtml += '<div>';
			sHtml += '<span style="font-weight: bold; font-size: 16px;">';
			sHtml += oTranslations['auction.confirm'];
			sHtml += '</span>';
			sHtml += '<br>';
                if (iCnt == 1)
                {
                    sHtml += oTranslations['auction.are_you_sure_you_want_to_delete_this_auction'];
                }
                else
                {
                    sHtml += oTranslations['auction.are_you_sure_you_want_to_delete_these_auctions'];
                }
			sHtml += '</div>'; 
			sHtml += '<div style="margin-top: 10px; text-align: right;">'; 
				sHtml += '<button class="btn btn-sm btn-primary" onclick="$(\'#' + sElementId + '\').submit();">'; 
					sHtml += oTranslations['auction.yes']; 
				sHtml += '</button>'; 
				sHtml += '<button class="btn btn-sm btn-default" style="margin-left: 10px;" onclick="$(\'.moderation_process\').hide();$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">'; 
					sHtml += oTranslations['auction.no']; 
				sHtml += '</button>'; 
			sHtml += '</div>'; 
		sHtml += '</div>'; 

		$.magnificPopup.open({
            items: {
			    src: sHtml, 
			    type: 'inline'
            }
        });
	}
    , confirmSingleDelete: function(sHref){
		sHtml = "";
		sHtml += '<div class="white-popup-block" style="width: 300px;">'; 
			sHtml += '<div>';
				sHtml += '<span style="font-weight: bold; font-size: 16px;">';
				sHtml += oTranslations['auction.confirm'];
				sHtml += '</span>';
				sHtml += '<br>';
       			 sHtml += oTranslations['auction.are_you_sure_you_want_to_delete_this_auction'];
			sHtml += '</div>'; 
			sHtml += '<div style="margin-top: 10px; text-align: right;">'; 
				sHtml += '<button class="btn btn-sm btn-primary" onclick="window.location.href=\'' + sHref + '\';">'; 
					sHtml += oTranslations['auction.yes']; 
				sHtml += '</button>'; 
				sHtml += '<button class="btn btn-sm btn-default" style="margin-left: 10px;" onclick="$(\'.moderation_process\').hide();$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">'; 
					sHtml += oTranslations['auction.no']; 
				sHtml += '</button>'; 
			sHtml += '</div>'; 
		sHtml += '</div>'; 

		$.magnificPopup.open({
            items: {
			    src: sHtml, 
			    type: 'inline'
            }
        });
	}
};