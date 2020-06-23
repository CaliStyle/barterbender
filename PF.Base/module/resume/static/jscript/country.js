
$Behavior.authorized_country_isoChange = function()
{
	$('#authorized_country_iso').change(function()
	{
		var sChildValue = $('#authorized_js_country_child_id_value').val();
		var sExtra = '';
		$('#authorized_js_country_child_id').html('');
		$('#authorized_country_iso').after('<span id="js_cache_country_iso">' + $.ajaxProcess('no_message') + '</span>');
		if ($('#js_country_child_is_search').length > 0)
		{
			sExtra += '&country_child_filter=true';
		}		
		$.ajaxCall('resume.getChildren', 'country_iso=' + this.value + '&country_child_id=' + sChildValue + sExtra, 'GET');
	});	
}

$Behavior.countryIsoChange = function()
{
	$('#country_iso').change(function()
	{
		var sChildValue = $('#js_country_child_id_value').val();
		var sExtra = '';
		$('#js_country_child_id').html('');
		$('#country_iso').after('<span id="js_cache_country_iso">' + $.ajaxProcess('no_message') + '</span>');
		if ($('#js_country_child_is_search').length > 0)
		{
			sExtra += '&country_child_filter=true';
		}		
		$.ajaxCall('core.getChildren', 'country_iso=' + this.value + '&country_child_id=' + sChildValue + sExtra, 'GET');
	});	
}