
$Core.directory =
{
	sUrl: '',
	
	url: function(sUrl)
	{
		this.sUrl = sUrl;
	},
}

$Core.customgroup =
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
				if ($Core.customgroup.aOptions !== null)
				{
					aMatches = $(this).attr('name').match(/val\[option\]\[(.*?)\]/i);
					if (isset(aMatches[1]) && isset($Core.customgroup.aOptions['option_' + (iCnt - 1) + '_' + aMatches[1]]))
					{
						$(this).val($Core.customgroup.aOptions['option_' + (iCnt - 1) + '_' + aMatches[1]]);
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
			
			if ((iCnt - 1) > $Core.customgroup.iDefault)
			{
				$(this).find('.js_option_delete').html('<a href="#" onclick="return $Core.customgroup.remove(this);"><img src="' + getParam('sImagePath') + 'misc/delete.png" alt="" /></option>');				
			}
		});		
	},
	
	add: function()
	{	
	    
		$('#js_option_holder').append($('#js_sample_option').html());	
		
		this.update();		
	},
	
	remove: function(oObj)
	{
		$(oObj).parents('.js_option_holder').remove();		
		
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
		aParams = $.getParams(oObj.href);
		
		$('.dropContent').hide();		
		
		switch (sAction)
		{
			case 'edit':
					window.location.href = this.sUrl + 'add/id_' + aParams['id'] + '/';
				break;
			case 'delete':
				var url = this.sUrl;
                $Core.jsConfirm({}, function () {
                    window.location.href = url + 'delete_' + aParams['id'] + '/';
                }, '');
				break;
			default:
				if (aParams['type'] == 'group') {
					$.ajaxCall('directory.toggleActiveGroup', 'id=' + aParams['id']);
				}
				else {
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
					$Core.customgroup.updateSort();
				},
				opacity: 0.4
			}
		);
        $('.sortable ul').addClass('dont-unbind');
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

$Behavior.custom_admin_init = function()
{	
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
        $Core.jsConfirm({}, function () {
            aParams = $.getParams(this.href);
            $.ajaxCall('custom.deleteOption', 'id=' + aParams['id']);
        }, function () {
        });
		
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
};