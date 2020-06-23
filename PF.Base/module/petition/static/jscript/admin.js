
$(function()
{
    $('.js_item_directsign_link').click(function()
    {
    	aParams = $.getParams(this.href);
    	var sParams = '';
	$('.js_item_directsign_active').hide();
	$('.js_item_directsign_not_active').show();
	
    	return false;
    });
});