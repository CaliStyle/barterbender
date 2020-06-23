
$Behavior.initSlideshow = function(){
    var gmapViewLink = $("#left > .sub_section_menu li:last a");
	if(gmapViewLink.size() > 0) {
		if(gmapViewLink.attr('href').indexOf('view_gmap') > 0)
		{
			gmapViewLink.attr('href', 'javascript:void(0)');
			gmapViewLink.click(function(){
				tb_show('GoogleMap', $.ajaxBox('advancedmarketplace.gmap', 'height=300&width=730'));
				return false;
			});
		}
    }
    $('#slideshow').cycle({ 
        fx:      'scrollLeft', 
        speed:    1000, 
        timeout:  10000,
        pause: true,
        before:function(){
            $(".slide_thumbs a").removeClass("active");
            $("#thumb_" + this.id).addClass("active");
        }
    });
};

$Core.ajax = function(sCall, $oParams)
{
	var sParams = '&' + getParam('sGlobalTokenName') + '[ajax]=true&' + getParam('sGlobalTokenName') + '[call]=' + sCall;
	
	if (!sParams.match(/\[security_token\]/i))
	{
		sParams += '&' + getParam('sGlobalTokenName') + '[security_token]=' + oCore['log.security_token'];
	}
	
	if (isset($oParams['params']))
	{
		if (typeof($oParams['params']) == 'string')
		{
			sParams += $oParams['params'];
		}
		else		
		{
			$.each($oParams['params'], function($sKey, $sValue)
			{
				sParams += '&' + $sKey + '=' + encodeURIComponent($sValue) + '';
			});
		}		
	}
	
	return $.ajax(
	{
		type: (isset($oParams['type']) ? $oParams['type'] : 'GET'),
		url: getParam('sJsStatic') + "ajax.php",
		dataType: 'html',
		data: sParams,
		success: $oParams['success']
	});	
}