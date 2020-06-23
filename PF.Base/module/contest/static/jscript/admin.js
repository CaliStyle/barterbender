
$Core.contest =
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
				if (confirm(oTranslations['contest.are_you_sure_this_will_delete_all_contests_that_belong_to_this_category_and_cannot_be_undone']))
				{
					window.location.href = this.sUrl + 'delete_' + aParams['id'] + '/';
				}
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
	$Core.jsConfirm = function(params, doYes, doNo) {
		var title = (params.hasOwnProperty('title') && params.title) ? params.title : oTranslations['confirm'];
		var message = (params.hasOwnProperty('message') && params.message) ? params.message : oTranslations['are_you_sure'];
		var yesBtn = (params.hasOwnProperty('btn_yes') && params.btn_yes) ? params.btn_yes : oTranslations['yes'];
		var noBtn = (params.hasOwnProperty('btn_no') && params.btn_no) ? params.btn_no : oTranslations['no'];
		var buttons = {};
		buttons[yesBtn] = {
			'class': 'button btn-success dont-unbind',
			text: yesBtn,
			click: function() {
				$(this).dialog("close");
				if (doYes && (typeof doYes === "function")) {
					doYes();
					return true;
				}
			}
		};
		buttons[noBtn] = {
			'class': 'button dont-unbind',
			text: noBtn,
			click: function() {
				$(this).dialog("close");
				if (doNo && (typeof doNo === "function")) {
					doNo();
					return false;
				}
			}
		};
		$(document.createElement('div'))
			.attr({title: title, class: 'confirm'})
			.html(message)
			.dialog({
				dialogClass: 'pf_js_confirm'
				,
				buttons: buttons
				,
				close: function() {
					$(this).remove();
				},
				draggable: true,
				modal: true,
				resizable: false,
				width: 'auto'
			});
	};
});