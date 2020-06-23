{literal}
<script src="//maps.googleapis.com/maps/api/js?key={$googleApiKey}&v=3.exp&sensor=false&language=en"></script>
<script type="text/javascript">
	var oLatLng;
	var oMap;
	var oMarker;
	var ajson;
	var lat={/literal}{$lat}{literal};
	var lng={/literal}{$lng}{literal};
	var slocation = {/literal}"{$location}"{literal};
	var zoom={/literal}{$zoom}{literal};
	var iradius=0;
	var aEventCoords = new Array();
	var aCurrentEvents = new Array();
	var displayMarkers = function(){};
	var showOnMap = function(){};
	var panGmapTo = function(){};
	var gmapRequests = new Array();
	var infowindow = null;
	var googleApiKey={/literal}"{$googleApiKey}"{literal};
	var aEvents;
        $Behavior.loadJsSettingAdMarketPlace = function(){
            $("body").addClass("js_stopscript");
        
	if(!$("body").hasClass("js_newscript")) 
	{
		var script = document.createElement('script');

		script.type= 'text/javascript';
		script.src = '//maps.google.com/maps/api/js?sensor=false&callback=showOnMap&key=' + googleApiKey;
		document.body.appendChild(script);
		$("body").addClass("js_newscript");
		$("body").removeClass("js_stopscript");
	}
	
	showOnMap = function()
	{

		oLatLng = new google.maps.LatLng(lat, lng);
		oMap = new google.maps.Map(document.getElementById("gmap"), {
			zoom: zoom,
			center: oLatLng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});
        var info = '<div style="line-height:20px;">';
        info +='<b>'+oTranslations['default_location']+'</b>:<br>'+slocation;
        info += '</div>';
        default_map = new google.maps.Marker({
            "info": info,
            map: oMap,
            position: new google.maps.LatLng(lat, lng)
        });
        google.maps.event.addListener(default_map, 'click', function() {
            infowindow.setContent(this.info)
            infowindow.open(oMap, this);
        });
		oMap.bounds_changed = function(){
			var ids = new Array();
			var bound = oMap.getBounds();

			for(var i in aEventCoords)
			{
				if(bound.contains(aEventCoords[i]['latlng']))
				{
					ids.push(aEventCoords[i]['listing_id']);
				}
			}

			if(ids.length > 0)
			{

				$.ajaxCall('advancedmarketplace.getListingsForGmap', 'ids=' + ids.join(','));
			}
		};
		{/literal}
		{foreach from=$aCoords item=aCoord}
		aEventCoords.push({l}"latlng":new google.maps.LatLng({$aCoord.lat}, {$aCoord.lng}), "listing_id":"{$aCoord.listing_id}"{r});
		{/foreach}
		{literal}
	}
	
    google.maps.event.addDomListener(window, 'load', showOnMap);
    
	displayMarkers = function(json)
	{
		
		infowindow = new google.maps.InfoWindow({
		    content: ''
		});
		var aEvents = $.parseJSON(json);

		for(var i in aEvents)
		{
			var eventId = aEvents[i]['listing_id'];
			//if(typeof(aCurrentEvents[eventId]) == 'undefined')
			{
				var info = '<div style="line-height:20px;">';
				info += '<strong>'+oTranslations['advancedmarketplace.listing']+': ' + aEvents[i]['title'] + '</strong><br/>';
				info += ''+oTranslations['advancedmarketplace.location']+': ' + aEvents[i]['location'] + (aEvents[i]['address']!=null && aEvents[i]['address']!='' ? ', ' + aEvents[i]['address'] : '') + (aEvents[i]['city']!=null && aEvents[i]['city']!='' ? ', ' + aEvents[i]['city'] : '') + '<br/>';
				info += '<a target="_blank" href="'+aEvents[i]['link']+'"><strong>'+oTranslations['advancedmarketplace.view_this_listing']+'</strong></a>';
				info += '</div>';
				aCurrentEvents[eventId] = new google.maps.Marker({
					"info": info,
					map: oMap,
					position: new google.maps.LatLng(aEvents[i]['lat'], aEvents[i]['lng'])
				});
				google.maps.event.addListener(aCurrentEvents[eventId], 'click', function() {
					infowindow.setContent(this.info)
					infowindow.open(oMap, this);
				});
				
				
				
			}
			

		}
	}
	
	panGmapTo = function(lat1, lng1, iradius1)
	{
		lat=lat1;
		lng=lng1;
		zoom=13;
		iradius=iradius1;
		showOnMap();
		var newLatLng = new google.maps.LatLng(lat, lng);
		var distance = $("#distance").val();
		distance = distance * 1000;
		if(distance==0)
			distance=1609;
		var eventid = new google.maps.Marker({
					map: oMap,
					position: new google.maps.LatLng(lat, lng)
				});
				
			 // Add a Circle overlay to the map.
				var circle = new google.maps.Circle({
				  map: oMap,
				  radius: iradius*distance, // 300 km
				  //fillColor: '#AA0000'
				});

				// Since Circle and Marker both extend MVCObject, you can bind them
				// together using MVCObject's bindTo() method.  Here, we're binding
				// the Circle's center to the Marker's position.
				// http://code.google.com/apis/maps/documentation/v3/reference.html#MVCObject
				circle.bindTo('center', eventid, 'position');
					
		oMap.panTo(newLatLng);
		oMap.setZoom(13);
		
	}
	
	$("#location").keyup(function(){
		var city=$('#city').val();
		var location=$('#location').val();
		var radius=0;
		if(parseInt($('#radius').val()))
		{
			radius=$('#radius').val();
		}
		for(var i in gmapRequests)
		{
			gmapRequests[i].abort();
		}
		
		gmapRequests.push(
			$.ajaxCall('advancedmarketplace.reloadGmap', 'location=' + this.value+'&city='+city+'&radius='+radius)
		);
	});
	
	$("#city").keyup(function(){
		var city=$('#city').val();
		var location=$('#location').val();
		var radius=0;
		if(parseInt($('#radius').val()))
		{
			radius=$('#radius').val();
		}
		
		for(var i in gmapRequests)
		{
			gmapRequests[i].abort();
		}
		
		gmapRequests.push(
			$.ajaxCall('advancedmarketplace.reloadGmap', 'location=' + location+'&city='+city+'&radius='+radius)
		);
	});
	
	$("#radius").keyup(function(){
		var city=$('#city').val();
		var location=$('#location').val();
		var radius=0;
		if(parseInt($('#radius').val()))
		{
			radius=$('#radius').val();
		}
		for(var i in gmapRequests)
		{
			gmapRequests[i].abort();
		}
		
		gmapRequests.push(
			$.ajaxCall('advancedmarketplace.reloadGmap', 'location=' + location+'&city='+city+'&radius='+radius)
		);
	});
	
	$("#distance").change(function(){
		var city=$('#city').val();
		var location=$('#location').val();
		var radius=0;
		if(parseInt($('#radius').val()))
		{
			radius=$('#radius').val();
		}
		for(var i in gmapRequests)
		{
			gmapRequests[i].abort();
		}
		
		gmapRequests.push(
			$.ajaxCall('advancedmarketplace.reloadGmap', 'location=' + location+'&city='+city+'&radius='+radius)
		);
	});
	
	if($("body").hasClass("js_stopscript"))
	{
		showOnMap();	
	}}
</script>
{/literal}

<form method="post" action="{url link='admincp.advancedmarketplace.setting'}" >

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p('Settings')}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">{_p('default_location')}:</label>
                <input class="form-control" id="location" type="text" size="40" name="val[location_setting]" value="{if isset($aSettings.location_setting)}{$aSettings.location_setting}{/if}"/>
            </div>
        </div>
    </div>

    <div id="gmap" style="width:100%; height:300px;">
        {_p('GMap holder')}
    </div>

    <div class="panel panel-default">
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" name="submit" value="Submit" />
        </div>
    </div>
</form>