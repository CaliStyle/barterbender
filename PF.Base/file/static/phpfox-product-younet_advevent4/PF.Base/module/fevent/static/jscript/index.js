var init_gmap = false;
$Behavior.initFeventSlideshow = function(){
	$("#fevent-feature").owlCarousel({
		nav: true,
		loop: false,
		items: 1,
		dots: false,
		autoplayTimeout: 3000,
		autoplay: true,
		lazyContent: true,
		loop: true,
		navText: ['<i class="ico ico-angle-left"></i>','<i class="ico ico-angle-right"></i>'],
		responsive:{
			0:{
				smartSpeed: 500,
				autoplayTimeout: 2500
			},
			767:{
				smartSpeed: 800,
				nav: true,
				autoplayTimeout: 3000
			}
		}
	});

	var gmapViewLink = $("#js_block_border_core_menusub .header_display li a[href$='view=gmap']");
	if(gmapViewLink.size() == 0)
	{
		gmapViewLink = $("._block_menu_sub .header_display li a[href$='view=gmap']");
	}
	if(gmapViewLink.size() > 0 && ((gmapViewLink.attr('href') !== undefined && gmapViewLink.attr('href').indexOf('view=gmap') > 0) || !gmapViewLink.prop('gmap')))
	{
		gmapViewLink.attr('href', 'javascript:void(0)');
		gmapViewLink.prop('gmap',true).addClass('dont-unbind');
		gmapViewLink.on('click',function(){
			tb_show('GoogleMap', $.ajaxBox('fevent.gmap', 'height=300&width=730'));
			return false;
		});
	}
};


var ynfeIndexPage = 
{
    dataPrint : {}
    , glat : -1
    , glong : -1
    , zoom : 13
    , oMap : undefined 
    , infowindow : undefined
    , showIn : 0
    , gmapRequests : new Array()
    , init: function()
    {
        ynfeIndexPage.checkEditYourCurrentLocation(); 
    }           
    , checkEditYourCurrentLocation: function()
    {
        if (parseInt(ynfeIndexPage.glat) != -1 
        	&& parseInt(ynfeIndexPage.glong) != -1
        	&& parseInt(ynfeIndexPage.glat) != 0
        	&& parseInt(ynfeIndexPage.glong) != 0
        	)
    	{
			$('#editYourCurrentLocation').show();
    	} else {
    		$('#editYourCurrentLocation').hide();
    	}
    }           
    , getVisitorLocation :function()
    {
        if (parseInt(ynfeIndexPage.glat) != -1 
        	&& parseInt(ynfeIndexPage.glong) != -1
        	&& parseInt(ynfeIndexPage.glat) != 0
        	&& parseInt(ynfeIndexPage.glong) != 0
        	)
        {
            /* We already have a location */
            $('#editYourCurrentLocation').show();
            return false;
        }
        // Get the visitors location
        if(navigator.geolocation)
        {
            navigator.geolocation.getCurrentPosition(function(oPos)
                {
                    if (oPos.coords.latitude == 0 && oPos.coords.longitude == 0)
                    {
                        return;
                    }
                    ynfeIndexPage.glat = oPos.coords.latitude; 
                    ynfeIndexPage.glong = oPos.coords.longitude; 

                    $('#editYourCurrentLocation').show();
                }
                , function(){ return; }
            );
        }
        else
        {
            //	get location without HTLM5
        }
    }
    , editYourCurrentLocationClick :function()
    {
    	ynfeIndexPage.showIn ++; 
    	tb_show('GoogleMap', $.ajaxBox('fevent.showEditYourCurrentLocationBlock', 'height=300&width=730'));
    	return false;
    }
    , initLocationBlock : function()
    {
    	if(ynfeIndexPage.showIn == 1 && ynfeIndexPage.glat == -1 && ynfeIndexPage.glong == -1){
	    	$("body").addClass("js_stopscript");

			if(!$("body").hasClass("js_newscript")) 
			{
				var script = document.createElement('script');
				script.type= 'text/javascript';
				script.src = '//maps.google.com/maps/api/js?sensor=false&callback=showOnMapForLocationBlock';
				document.body.appendChild(script);
				$("body").addClass("js_newscript");
				$("body").removeClass("js_stopscript");
			}

			google.maps.event.addDomListener(window, 'load', showOnMapForLocationBlock());

			if($("body").hasClass("js_stopscript"))
			{
				showOnMapForLocationBlock();	
			}
    	} else {
    		ynfeIndexPage.panGmapTo(ynfeIndexPage.glat, ynfeIndexPage.glong);
    	}
		ynfeIndexPage.bindEventForLocationBlock(); 
    }
    , bindEventForLocationBlock : function()
    {
		$("#el_location").keyup(function(){
			var city=$('#el_city').val();
			var location=$('#el_location').val();

			for(var i in ynfeIndexPage.gmapRequests)
			{
				ynfeIndexPage.gmapRequests[i].abort();
			}
			
			ynfeIndexPage.gmapRequests.push(
				$.ajaxCall('fevent.reloadGmapLocationBlock', 'location=' + this.value 
					+ '&city=' + city 
				)
			);
		});
		
		$("#el_city").keyup(function(){
			var city=$('#el_city').val();
			var location=$('#el_location').val();
			
			for(var i in ynfeIndexPage.gmapRequests)
			{
				ynfeIndexPage.gmapRequests[i].abort();
			}
			
			ynfeIndexPage.gmapRequests.push(
				$.ajaxCall('fevent.reloadGmapLocationBlock', 'location=' + location 
					+ '&city=' + city
				)
			);
		});		
    }
    , displayMarkers : function()
    {
    }
    , panGmapTo : function(lat1, lng1)
    {
		ynfeIndexPage.glat = lat1;
		ynfeIndexPage.glong = lng1;
		var iradius= 0;

		showOnMapForLocationBlock();

		var newLatLng = new google.maps.LatLng(ynfeIndexPage.glat, ynfeIndexPage.glong);

		var distance = 0;
		distance = distance * 1000;
		if(distance==0)
			distance=1609;

		var eventid = new google.maps.Marker({
					map: ynfeIndexPage.oMap,
					position: new google.maps.LatLng(ynfeIndexPage.glat, ynfeIndexPage.glong)
		});
				
	 // Add a Circle overlay to the map.
		var circle = new google.maps.Circle({
		  map: ynfeIndexPage.oMap,
		  radius: iradius*distance, // 300 km
		  //fillColor: '#AA0000'
		});

		// Since Circle and Marker both extend MVCObject, you can bind them
		// together using MVCObject's bindTo() method.  Here, we're binding
		// the Circle's center to the Marker's position.
		// http://code.google.com/apis/maps/documentation/v3/reference.html#MVCObject
		circle.bindTo('center', eventid, 'position');
					
		ynfeIndexPage.oMap.panTo(newLatLng);
		ynfeIndexPage.oMap.setZoom(ynfeIndexPage.zoom);    	
    }
    , ynfe_base64_encode : function(data)
    {
		  // http://kevin.vanzonneveld.net
		  // +   original by: Tyler Akins (http://rumkin.com)
		  // +   improved by: Bayron Guevara
		  // +   improved by: Thunder.m
		  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		  // +   bugfixed by: Pellentesque Malesuada
		  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		  // +   improved by: Rafal Kukawski (http://kukawski.pl)
		  // *     example 1: base64_encode('Kevin van Zonneveld');
		  // *     returns 1: 'S2V2aW4gdmFuIFpvbm5ldmVsZA=='
		  // mozilla has this native
		  // - but breaks in 2.0.0.12!
		  //if (typeof this.window['btoa'] === 'function') {
		  //    return btoa(data);
		  //}
		  var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
		  var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
		    ac = 0,
		    enc = "",
		    tmp_arr = [];

		  if (!data) {
		    return data;
		  }

		  do { // pack three octets into four hexets
		    o1 = data.charCodeAt(i++);
		    o2 = data.charCodeAt(i++);
		    o3 = data.charCodeAt(i++);

		    bits = o1 << 16 | o2 << 8 | o3;

		    h1 = bits >> 18 & 0x3f;
		    h2 = bits >> 12 & 0x3f;
		    h3 = bits >> 6 & 0x3f;
		    h4 = bits & 0x3f;

		    // use hexets to index into b64, and append result to encoded string
		    tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
		  } while (i < data.length);

		  enc = tmp_arr.join('');

		  var r = data.length % 3;

		  return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);    	
    }
}; 

$Behavior.initynfeIndexPage = function()
{
	showOnMapForLocationBlock = function()
	{
		var oLatLng = new google.maps.LatLng(ynfeIndexPage.glat, ynfeIndexPage.glong);
		ynfeIndexPage.oMap = new google.maps.Map(document.getElementById("gmap"), {
			zoom: ynfeIndexPage.zoom,
			center: oLatLng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});
	}    
	
    ynfeIndexPage.init();

}

