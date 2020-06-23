
$Core.ecommerce =
{
	sUrl: '',
    sMessage: '',
    sMessageNotDelete: '',
	
	url: function(sUrl)
	{
		this.sUrl = sUrl;
	},	
	
    setMessage: function(sMessage, sMessageNotDelete)
	{
	    this.sMessageNotDelete = sMessageNotDelete;
		this.sMessage = sMessage;
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
                
            case 'showcustomfields':
				tb_show(oTranslations['ecommerce.custom_group'], $.ajaxBox('ecommerce.showPopupCustomGroup', 'height=300&width=530&category_id=' + aParams['id']));
				break;
                
			case 'delete':
			    var sUrl = this.sUrl;
                $Core.ajax('ecommerce.getAllItemBelongToCategory',
                    {
                        type: "POST",
                        params: {
                            iCategoryId: aParams['id']
                        },
                        success: function (sOutput) {
                            var oOutput = $.parseJSON(sOutput);
                            if (oOutput.status == 'SUCCESS') {
                                var iNumberItems = oOutput.iNumberItems;
                                console.log(iNumberItems);
                                var sMessage = '';
                                if (iNumberItems != '0') {
                                    sMessage = '<strong>Waring!!!</strong> ' + oTranslations['ecommerce.you_can_not_delete_this_category_because_there_are_many_items_related_to_it'];
                                } else {
                                    sMessage = oTranslations['ecommerce.are_you_sure'];
                                }

                                linkdelete = sUrl + 'delete_' + aParams['id'] + '/';

                                // Open directly via API
                                var sHtml = '';
                                sHtml += '<div class="white-popup-block" style="width: 300px;">';
                                sHtml += '<div>';
                                sHtml += sMessage;
                                sHtml += '</div>';
                                if (iNumberItems == '0') {
                                    sHtml += '<div style="margin-top: 10px; text-align: right;">';
                                    sHtml += '<button class="btn btn-sm btn-primary" onclick="window.location.href =\''+linkdelete+'\';">';
                                    sHtml += oTranslations['ecommerce.yes'];
                                    sHtml += '</button>';
                                    sHtml += '<button class="btn btn-sm btn-default" style="margin-left: 10px;" onclick="$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">';
                                    sHtml += oTranslations['ecommerce.no'];
                                    sHtml += '</button>';
                                    sHtml += '</div>';
                                }
                                sHtml += '</div>';

                                $.magnificPopup.open({
                                    items: {
                                        src: sHtml,
                                        type: 'inline'
                                    }
                                });
                            }
                            else {

                            }
                        }
                    }
                )
				break;				
			default:
			
				break;	
		}
		
		return false;
	}
}

$Behavior.initEcommerceBackEnd = function()
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
	
};

$Core.ecommerce_customgroup =
{
	iDefault: 4,
		
	aOptions: null,
	
	sUrl: '',
	
	init: function(iDefault, aOptions)
	{
		this.iDefault = iDefault;
		
		if (!empty(aOptions))
		{
			this.aOptions = aOptions;
			
			var iCnt = 0;
			for (i in aOptions)
			{
				iCnt++;
			}			
		}				
		
		this.display();			
	},
	
	url: function(sUrl)
	{
		this.sUrl = sUrl;
	},
	
	display: function()
	{		
		var sForm = $('#js_sample_option').html();
		var sForms = '';
		for (i = 0; i < this.iDefault; i++)
		{			
			sForms += sForm;
		}
		$('#js_option_holder').html(sForms).show();
		//$('#tbl_add_custom_option').show();
		
		this.update();	
	},
	
	update: function()
	{
	    //return;
		var iCnt = 0;
		var aMatches;
		$('.js_option_holder').each(function()
		{
			iCnt++;
			//return;
			$(this).find('.js_option_count').html((iCnt - 1));			
			
			$(this).find('input').each(function()
			{
				if ($Core.ecommerce_customgroup.aOptions !== null)
				{
					aMatches = $(this).attr('name').match(/val\[option\]\[(.*?)\]/i);
					if (isset(aMatches[1]) && isset($Core.ecommerce_customgroup.aOptions['option_' + (iCnt - 1) + '_' + aMatches[1]]))
					{
						$(this).val($Core.ecommerce_customgroup.aOptions['option_' + (iCnt - 1) + '_' + aMatches[1]]);
					}
				}
				
				// admincp.custom.add has a different format for 2nd run (clicking in "Add New Option")
				if ( $(this).attr('name').indexOf('val[option][0]') > (-1))
				{
					$(this).attr('name', $(this).attr('name').replace('val[option][0]', 'val[option][' + (iCnt-1) + ']'));
				}
				else if ($(this).attr('name').match(/val\[option\]\[[0-9]+\]/))
				{
					$(this).attr('name', $(this).attr('name').replace(/\[[0-9]+\]/, '[' + (iCnt-1) + ']'));
				}
				else
				{
					$(this).attr('name', $(this).attr('name').replace('#', (iCnt-1)));//(/\[option\]\[([a-z0-9]+)\]/, '[option][' + (iCnt-1) + '][$1]'));
				}
				
				
			});
			
			if ((iCnt - 1) > $Core.ecommerce_customgroup.iDefault)
			{
				$(this).find('.js_option_delete').html('<a href="#" onclick="return $Core.ecommerce_customgroup.remove(this);"><img src="' + getParam('sImagePath') + 'misc/delete.png" alt="" /></option>');				
			}
		});		
	},
	
	add: function()
	{
		$('#js_option_holder').append($('#js_sample_option').html());
		$('#tbl_option_holder').show();
		
		this.update();		
	},
	
	remove: function(oObj)
	{
		$(oObj).parents('.js_option_holder').remove();		
		if($('#js_option_holder') && $('#js_option_holder').children('.js_option_holder').length == 0 )
		{
			$('#tbl_option_holder').hide();
		}
		return false;
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
	
	action: function(oObj, sAction)
	{
		console.log(oObj.href);
		
		aParams = $.getParams(oObj.href);	
		
		$('.dropContent').hide();		
		
		switch (sAction)
		{
			case 'edit':

					window.location.href = this.sUrl + 'add/?id=' + aParams['id'] + '/';
				
				break;
			case 'delete':
                var linkdelete = this.sUrl + 'delete_' + aParams['id'] + '/';
                $Core.jsConfirm({}, function () {
                    window.location.href = linkdelete;
                }, function () {
                });
                break;
			default:
				console.log(aParams);

				if (aParams['type'] == 'group')
				{
					$.ajaxCall('ecommerce.toggleActiveGroup', 'id=' + aParams['id']);
				}
				else
				{
					$.ajaxCall('custom.toggleActiveField', 'id=' + aParams['id']);
				}				
				break;
		}
		
		return false;
	},
	
	addSort: function()
	{
		$('.sortable ul').sortable({
				axis: 'y',
				update: function(element, ui)
				{
					$Core.ecommerce_customgroup.updateSort();
				},
				opacity: 0.4
			}
		);		
	},
	
	toggleFieldActivity: function(iId)
	{
		if ($('#js_field_' + aParams['id']).html().match(/<del(.*?)>/i))
		{
			$('#js_field_' + aParams['id']).html($('#js_field_' + aParams['id']).html().replace(/<del(.*?)>/i, '').replace(/<\/del>/i, ''));
		}
		else
		{
			$('#js_field_' + aParams['id']).html('<del>' + $('#js_field_' + aParams['id']).html() + '</del>');
		}		
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
	toggleShowFeed: function(iVal)
	{
		if (iVal == 1)
		{
			$('div.add_feed').each(function(){$(this).show()});
		}
		else
		{
			$('div.add_feed').each(function(){$(this).hide()});
		}
	}
}

$Behavior.ecommerce_custom_admin_init = function(){
	$('.js_drop_down').click(function(){		
		eleOffset = $(this).offset();
		
		aParams = $.getParams(this.href);
		
		$('#js_cache_menu').remove();
		
		$('body').prepend('<div id="js_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:100; background:red;">' + $('#js_menu_drop_down').html() + '</div>');
		
		$('#js_cache_menu .link_menu li a').each(function(){			
			if (this.hash == '#active' && (($('#js_field_' + aParams['id']).html() && $('#js_field_' + aParams['id']).html().match(/<del>/i)) || ($('#js_group_' + aParams['id']).html() && $('#js_group_' + aParams['id']).html().match(/<del>/i))))
			{
				$(this).html(oTranslations['ecommerce.set_to_active']);
			}
			
			this.href = '#?id=' + aParams['id'] + '&type=' + aParams['type'] + '';			
		});
		
		$('.dropContent').show();		
		
		$('.dropContent').mouseover(function(){
			$('.dropContent').show(); 
			
			return false;
		});
		
		$('.dropContent').mouseout(function(){
			$('.dropContent').hide(); 
			$('.sJsDropMenu').removeClass('is_already_open');			
		});
		
		return false;
	});		
	
	$('.var_type').change(function(){
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
	
	$('.js_add_custom_option').click(function(){
		$Core.ecommerce_customgroup.add();
		
		return false;
	});
	
	$('#js_create_new_group').click(function(){
		$('#js_field_holder').hide();
		$('#js_group_holder').show();
		
		return false;
	});
	
	$('#js_cancel_new_group').click(function(){
		$('#js_group_holder').hide();
		$('#js_field_holder').show();		
		
		return false;
	});	
	
	$('.js_delete_current_option').click(function(){
		if (confirm(oTranslations['custom.are_you_sure_you_want_to_delete_this_custom_option']))
		{
			aParams = $.getParams(this.href);
			
			$.ajaxCall('custom.deleteOption', 'id=' + aParams['id']);
		}
		
		return false;
	});
	$('.js_custom_change_group').click(function(){
		$(this).parents('ul:first').find('li').removeClass('active');
		$(this).parent().addClass('active');
		$('.js_custom_groups').hide();
		$('.js_custom_group_' + this.id.replace('group_', '')).show();				
		
		return false;
	});
};