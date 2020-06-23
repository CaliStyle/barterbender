var oMarker;
var oGeoCoder;
var sQueryAddress;
var oMap;
var oLatLng;
var bDoTrigger = false;
/* This function takes the information from the input fields and moves the map towards that location*/
function ynjobposting_inputToMap()
{
	var location = '';
	if($('#location').length)
	{
		location = $('#location').val();
	}
	else{
		location = $('#working_place').val();
	}
	
	var sQueryAddress = location + ' ' +  $('#city').val();
	if ($('#js_country_child_id_value option:selected').val() > 0)
	{
		sQueryAddress += ' ' + $('#js_country_child_id_value option:selected').text();
	}
	sQueryAddress += ' ' + $('#country_iso option:selected').text();
	oGeoCoder.geocode({
		'address': sQueryAddress
		}, function(results, status)
		{
			if (status == google.maps.GeocoderStatus.OK)
			{
				oLatLng = new google.maps.LatLng(results[0].geometry.location.lat(),results[0].geometry.location.lng());
				oMarker.setPosition(oLatLng);
				oMap.panTo(oLatLng);
				$('#input_gmap_latitude').val(oMarker.position.lat());
				$('#input_gmap_longitude').val(oMarker.position.lng());
			}
		}
	);
	if (bDoTrigger)
	{
		google.maps.event.trigger(oMarker, 'dragend');
		bDoTrigger = false;
	}
}

function ynjobposting_initialize()
{
	if (!($('#mapHolder').length)){
		return;
	};
	oGeoCoder = new google.maps.Geocoder();
    if(typeof(aInfo)=='undefined')
    {
        aInfo = {latitude:0, longitude:0};
    }
	oLatLng = new google.maps.LatLng(aInfo.latitude, aInfo.longitude);
	
	var myOptions = {
		zoom: 11,
		center: oLatLng,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		mapTypeControl: false,
		streetViewControl: false
	}
	oMap = new google.maps.Map(document.getElementById("mapHolder"), myOptions);
	oMarker = new google.maps.Marker({
		draggable: true,
		'position': oLatLng,
		map: oMap
	});

	
	/* Fake the dragend to populate the city and other input fields */
	google.maps.event.trigger(oMarker, 'dragstart');
	google.maps.event.trigger(oMarker, 'dragend');
	google.maps.event.addListener(oMarker, "dragend", function()
	{
		debug('drag end');
		$('#input_gmap_latitude').val(oMarker.position.lat());
		$('#input_gmap_longitude').val(oMarker.position.lng());
		oLatLng = new google.maps.LatLng(oMarker.position.lat(), oMarker.position.lng());
		oGeoCoder.geocode({
			'latLng': oLatLng
		},
		function(results, status)
		{
			if (status == google.maps.GeocoderStatus.OK)
			{
				$('#city').val('');
				$('#postal_code').val('');
				//debug (results[0]);
				for (var i in results[0]['address_components'])
				{
					if (results[0]['address_components'][i]['types'][0] == 'street_address')
					{
						$('#location').val(results[0]['address_components'][i]['long_name']);
					}
					if (results[0]['address_components'][i]['types'][0] == 'locality')
					{
						$('#city').val(results[0]['address_components'][i]['long_name']);
					}
					if (results[0]['address_components'][i]['types'][0] == 'postal_code')
					{
						$('#postal_code').val(results[0]['address_components'][i]['long_name']);
					}
					if (results[0]['address_components'][i]['types'][0] == 'country')
					{
						var sCountry = $('#country_iso option:selected').val();
						$('#js_country_iso_option_'+results[0]['address_components'][i]['short_name']).attr('selected','selected');
						if (sCountry != $('#country_iso option:selected').val())
						{
							$('#country_iso').change();
						}
					}
					if (isset($('#js_country_child_id_value')) && results[0]['address_components'][i]['types'][0] == 'administrative_area_level_1')
					{
						$('#js_country_child_id_value option').each(function(){
							if ($(this).text() == results[0]['address_components'][i]['long_name'])
							{
								$(this).attr('selected','selected');
								bHasChanged = true;
							}
						});
					}					
				}
			}
		});
	});
	/* Sets events for when the user inputs info */
	ynjobposting_inputToMap();
}

function loadScript()
{
	var script = document.createElement('script');
	script.type= 'text/javascript';
	script.src = '//maps.google.com/maps/api/js?sensor=false&callback=ynjobposting_initialize';
	document.body.appendChild(script);


}

$Behavior.loadGoogleMap = function() {
	if (!($('#mapHolder').length)){
		return;
	};

    $('#js_country_child_id_value').change(function(){
        debug("Cleaning  city, postal_code and address");
        $('#location').val('');
        $('#city').val('');
        $('#postal_code').val('');
    });
    $('#country_iso, #js_country_child_id_value').change(ynjobposting_inputToMap);
    $('#location, #postal_code, #city').blur(ynjobposting_inputToMap);
	if (typeof google.maps == 'undefined')
	{
		loadScript();

	}else
	{
		if ($('#mapHolder').length)
		{
			ynjobposting_initialize();
		}
	}
};