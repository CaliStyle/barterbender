var init_gmap = false;

$Behavior.initViewMap = function() {
    var gmapViewLink = $("body[id^=page_fevent] #js_block_border_core_menusub .header_display li a[href$='view=gmap']");
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
}

$Behavior.initFeventBirthdaySlideshow = function(){
	var owl = $('.p-fevent-birthday-container .item-listing-today.owl-carousel');
    if (!owl.length || owl.prop('built')) {
        return false;
    }
    owl.prop('built', true);
    owl.addClass('dont-unbind-children');
    var rtl = false;
    if ($("html").attr("dir") == "rtl") {
        rtl = true;
    }
    var item_amount = parseInt(owl.find('.item').length);
    var more_than_one_item = item_amount > 1;
    var more_than_two_item = item_amount > 2;
    var dotseach = 1;
    var items = 3;
    if (window.matchMedia('(min-width: 1200px)').matches) {
        if($('#main:not(.empty-right):not(.empty-left)').length > 0){
            items = 2;
        }
    }else if(window.matchMedia('(min-width: 992px)').matches ){
    	if( ($('#main.empty-right:not(.empty-left)').length > 0) || ($('#main.empty-left:not(.empty-right)').length > 0) ){
            items = 2;
        }
    }
    owl.owlCarousel({
        rtl: rtl,
        items: items,
        loop: true,
        mouseDrag: true,
        margin: 16,
        autoplay: false,
        autoplayTimeout: 300,
        autoplayHoverPause: true,
        smartSpeed: 800,
        dots:false,
        stagePadding: 0,
        navText: ['<i class="ico ico-angle-left"></i>','<i class="ico ico-angle-right"></i>'],
        nav:true,
        responsive : {
		    // breakpoint from 0 up
		    0 : {
		        items: 1
		    },
		    // breakpoint from 480 up
		    480 : {
		        items: 2
		    },
		    // breakpoint from 768 up
		    768 : {
		        items: 2
		    },
		    992 :{
		    	items: items
		    }
		}
    });
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
        ynfeIndexPage.getVisitorLocation();
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
        $(document).on('keyup', '#el_location', function(){
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
        $(document).on('keyup', '#el_city', function(){
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

};

$Behavior.feventTouchHandler = function(event){
     var touch = event.changedTouches[0];

    var simulatedEvent = document.createEvent("MouseEvent");
        simulatedEvent.initMouseEvent({
        touchstart: "mousedown",
        touchmove: "mousemove",
        touchend: "mouseup"
    }[event.type], true, true, window, 1,
        touch.screenX, touch.screenY,
        touch.clientX, touch.clientY, false,
        false, false, false, 0, null);

    touch.target.dispatchEvent(simulatedEvent);
    event.preventDefault();
}

$Behavior.feventInitCalendarPage = function(){
    var $calendar_page = $('#p_fevent_calendar_page');
    if (!$calendar_page.length) {
        return;
    }

    var $my_event_group = $('.p-fevent-checkbox-my-event'),
        $other_events_group = $('.p-fevent-checkbox-other-events'),
        $my_events_checkbox = $my_event_group.find('input:checkbox'),
        $other_events_checkbox = $other_events_group.find('input:checkbox'),
        $dropdown = $my_event_group.find('.dropdown'),
        $dropdown_link = $dropdown.find('a.dropdown-toggle').first()
    ;

    function loadEvents(from, to) {
        var params = [
            'from=' + from.getTime(),
            'to=' + to.getTime(),
            'browser_timezone=' + from.getTimezoneOffset(),
        ];

        if ($other_events_checkbox.is(':checked')) {
            params.push('other=1');
        }
        $dropdown.find('a.dropdown-item').each(function(){
            if ($(this).hasClass('active')) {
                params.push($(this).data('type') + '=1');
            }
        });
        var events = [],
            url = $.ajaxBox('fevent.loadCalendar', params.join('&'));

        $.ajax({
            url: url,
            dataType: 'json',
            type: 'GET',
            async: false,
        }).done(function(json) {
            if(!json.success) {
                $.error(json.error);
            }
            if(json.result) {
                events = json.result;
            }
        });
        return events;
    }

    calendar = $("#p_fevent_calendar_page").calendar({
        tmpl_path: oParams.sBaseURL.replace('/index.php', '') + "/PF.Site/Apps/p-advevent/assets/tmpls/",
        events_source: loadEvents,
        format12: 1,
        am_suffix: "am",
        pm_suffix: "pm",
        time_start: '00:00',
        time_end: '25:00',
        time_split: '30',
        weekbox: 0,
        language: 'en',
        onAfterViewLoad: function (view) {
            $('.item-time-title').text(this.getTitle());
            $('.item-time-group button').removeClass('active');
            $('.item-time-group button[data-calendar-view="' + view + '"]').addClass('active');
        }
    });

    $('.item-nav-group button[data-calendar-nav]').each(function() {
        var $this = $(this);
        $this.click(function() {
            calendar.navigate($this.data('calendar-nav'));
        });
    });

    $('.item-time-group button[data-calendar-view]').each(function() {
        var $this = $(this);
        $this.click(function() {
            calendar.view($this.data('calendar-view'));
        });
    });

    $dropdown_link.click(function(){
        $dropdown.toggleClass('open');
    });

    $('body').on('click', function (e) {
        if (!$dropdown.is(e.target) && $dropdown.has(e.target).length === 0) {
            $dropdown.removeClass('open');
        }
    });

    var updateEventTimeout;
    function startUpdateEvents() {
        window.clearTimeout(updateEventTimeout);
        updateEventTimeout = setTimeout(doUpdateEvents, 500);
    }
    function doUpdateEvents() {
        calendar.view();
    }

    $my_events_checkbox.change(function() {
        $dropdown.find('.dropdown-item').toggleClass('active', $(this).is(':checked'));
        startUpdateEvents();
    });
    $other_events_checkbox.change(function() {
        startUpdateEvents();
    });

    $dropdown.find('.dropdown-item').click(function(e){
        var $this = $(this);
        $this.toggleClass('active');
        $my_events_checkbox.prop(
            'checked',
            $dropdown.find('.dropdown-item').length == $dropdown.find('.dropdown-item.active').length
        );
        startUpdateEvents();
        return false;
    });
}