var fevent = {
	sCorePath : ''
	,removeBigCalendar : function(){
		var width = $(window).width();
		if (width <= 480) {
			$('div#bigcalendar').parent().parent().remove();
		}
	}
	,showDetailMapView: function(iEventId){

        $Core.ajax('fevent.loadAjaxDetailMapView',
        {
            type: 'POST',
            params:
            {
            	'iEventId' : iEventId
            },
            success: function(sOutput)
            {
            	datas = [];
				contents = [];

            	var oOutput = $.parseJSON(sOutput);
                if(oOutput.status == 'SUCCESS')
                {
            		fevent.sCorePath = oOutput.sCorePath;
                	var aData = oOutput.data;
            		$.each(aData, function(key, value){
					    item_data = [];
					    item_data['latitude'] = value[0]['latitude'];
					    item_data['longitude'] = value[0]['longitude'];
					    item_data['location'] = value[0]['location'];
						datas.push(item_data);
						contents.push(value);
					});
					var fevent_detail_map = null;
					fevent.showMapsWithData('fevent_detail_map', datas, contents,fevent_detail_map);
                }
            }
        });

	}
	,showMapView: function(typeEventMap){
        $Core.ajax('fevent.loadAjaxMapView',
        {
            type: 'POST',
            params:
            {
            	'typeEventMap' : typeEventMap
            },
            success: function(sOutput)
            {
            	datas = [];
				contents = [];
            	var oOutput = $.parseJSON(sOutput);
                if(oOutput.status == 'SUCCESS')
                {
            		fevent.sCorePath = oOutput.sCorePath;
                	var aData = oOutput.data;
            		$.each(aData, function(key, value){
					    item_data = [];
					    item_data['latitude'] = value[0]['latitude'];
					    item_data['longitude'] = value[0]['longitude'];
					    item_data['location'] = value[0]['location'];
						datas.push(item_data);
						contents.push(value);
					});
					if(typeEventMap == 'upcoming'){
						var fevent_upcoming_map = null;
						fevent.showMapsWithData('fevent_upcoming_map', datas, contents,fevent_upcoming_map);
					}
					else {
						var fevent_ongoing_map = null;
						fevent.showMapsWithData('fevent_ongoing_map', datas, contents, fevent_ongoing_map);
					}

                }
            }
        });

	}
	, showMapsWithData: function(id, datas, contents, objectMap){
		if($('#' + id).length > 0) {
			if(datas.length > 0) {
                var center = new google.maps.LatLng(datas[0]['latitude'], datas[0]['longitude']);
                var neighborhoods = [];
                var markers = [];
                var iterator = 0;
                for (var i = 0; i < datas.length; i++) {
                    neighborhoods.push(new google.maps.LatLng(datas[i]['latitude'], datas[i]['longitude']));
                }
                function showMapsWithData_initialize() {
                    var mapOptions = {
                        zoom: 15,
                        center: center
                    };
                    objectMap = new google.maps.Map(document.getElementById(id), mapOptions);
                    var bounds = new google.maps.LatLngBounds();
                    for (var i = 0; i < neighborhoods.length; i++) {
                        showMapsWithData_addMarker(i);

                        if (neighborhoods.length > 1) {

                            bounds.extend(neighborhoods[i]);
                        }
                    }
                    if (neighborhoods.length > 1) {
                        objectMap.fitBounds(bounds);
                    }
                }

                function showMapsWithData_addMarker(i) {
                    marker = new google.maps.Marker({
                        position: neighborhoods[iterator],
                        map: objectMap,
                        draggable: false,
                        animation: google.maps.Animation.DROP,
                        icon: datas[i]['icon']
                    })
                    markers.push(marker);
                    iterator++;
                    infowindow = new google.maps.InfoWindow({});
                    google.maps.event.addListener(marker, 'mouseover', function () {
                        infowindow.close();
                        infowindow.setContent(fevent.showExtraInfo(contents[i]));
                        infowindow.open(objectMap, markers[i]);
                    });
                }
                showMapsWithData_initialize();
            }
            else {
                $('#' + id).html('<p class="help-block" style="margin-top: 0">' + oTranslations['fevent.no_events_found_on_map'] + '</p>');
			}
		}
	}
	,showExtraInfo : function(info){
		sHtml = '';
		if($.isArray(info)){
			if(info.length > 1){
				sHtml += '<div class="business-item-map-header" style="background-color: #f4f4f4; color: #5f74a6">';
					sHtml += '<span style="background-color: #40474e; color: #fff; padding: 0 5px; display: inline-block; margin-right: 5px;">' + info.length + '</span>' + oTranslations['fevent.events'];
				sHtml += '</div>';
				sHtml += '<div class="business-item-map-main">';
			}

			$.each(info, function(key, aEvent){
				sHtml += '<div class="business-item-map" style="width: 300px; height: 64px; padding: 8px 5px; border-bottom: 1px solid #ebebeb; font-size: 12px; overflow: hidden; box-sizing: content-box; -webkit-box-sizing: content-box; -moz-box-sizing: content-box;">';
					sHtml += '<div class="business-item-map-image" style="position: relative; margin-right: 10px; float: left;">';
						sHtml += '<a  href="'+aEvent['url_detail']+'"><img width=64 height=64 src="'+aEvent['url_image']+'"></a>';
					sHtml += '</div>';
					sHtml += '<div class="business-item-map-title" style="color: #3b5998; font-size: 14px; font-weight: bold; margin-bottom: 10px; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><a href="'+aEvent['url_detail']+'">' + aEvent['title'] + '</a></div>';
					sHtml += '<div class="business-item-map-location" style="margin-bottom: 5px; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><i class="fa fa-map-marker"></i> ' + aEvent['location'] + '</div>';
					sHtml += '<div style="display: inline-block;"><a href="//maps.google.com/maps?daddr='+aEvent['location']+'" target="_blank"><img src="' + fevent.sCorePath + 'module/fevent/static/image/icon-getdirection.png" /> '+oTranslations['fevent.v_getdirections']+'</a></div>';

				sHtml += '</div>';
			});

			if(info.length > 1){
				sHtml += '</div>';
			}

		}
		else {
			sHtml = info;
		}
		return sHtml;
	}
	, initSubscribeBlock: function(){
		// prevent enter for submitting form
		$("#fevent_subscribe").submit(function(e) {

				return false;
		});
		// subscribe block - location - auto suggest
		// search location by google api
		if ($("#fevent_subscribe #fevent_subscribeblock_location").length == 0) {
			return false;
		}
	 	var input = ($("#fevent_subscribe #fevent_subscribeblock_location")[0]);
	 	if (window.google){
	 		// do nothing
	 	} else {
			// yndirectory.alertMessage(oTranslations['directory.cannot_load_google_api_library_please_reload_the_page_and_try_again']);
			return false;
		}
	 	var autocomplete = new google.maps.places.Autocomplete(input);
	  	google.maps.event.addListener(autocomplete, 'place_changed', function() {
	    	var place = autocomplete.getPlace();
		    if (!place.geometry) {
		     	return;
		    }

		    var $parent = $(input).closest('.table_right');
		    $parent.find('[data-inputid="subscribe_location_address"]').val($parent.find('#fevent_subscribeblock_location').val());

		    $parent.find('[data-inputid="subscribe_location_address_lat"]').val(place.geometry.location.lat());
		    $parent.find('[data-inputid="subscribe_location_address_lng"]').val(place.geometry.location.lng());

	    });

	  	$('.category_checkbox').on('change', function() {
	  	    var selected_categories = $('.category_checkbox:checked');
	  	    if(selected_categories.length == 1) {
	  	        $('.subscribe-categories__text').text(selected_categories.parent().find('span').text());
            } else if(selected_categories.length > 1) {
                $('.subscribe-categories__text').text(selected_categories.length + ' ' +oTranslations['fevent.categories_selected']);
            }
            if ($('.category_checkbox:checked').length) {
                $('.subscribe-categories span' ).addClass('active');
			} else {
				$('.subscribe-categories span' ).removeClass('active');
				$('.subscribe-categories__text').text(oTranslations['fevent.select_category']);
			}
        });
	}

	, getCurrentPositionForBlock: function(type){
		var result = null;

  		if (navigator.geolocation)
    	{
    		navigator.geolocation.getCurrentPosition(function(position){

    			if (position.coords.latitude)
    			{

    				result = {latitude: position.coords.latitude, longitude: position.coords.longitude};

    				var latLng = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
					geocoder = new google.maps.Geocoder();
					geocoder.geocode({
					        latLng: latLng
					        },
					        function(responses)
					        {
					           if (responses && responses.length > 0)
					           {
									if(type == 'subscribe'){

    									$("#fevent_subscribe #fevent_subscribeblock_location").val(responses[0].formatted_address);
									    $("#fevent_subscribe input[data-inputid='subscribe_location_address']").val(responses[0].formatted_address);
									 	$("#fevent_subscribe input[data-inputid='subscribe_location_address_lat']").val(position.coords.latitude);
									    $("#fevent_subscribe input[data-inputid='subscribe_location_address_lng']").val(position.coords.longitude);

									}
					           }

					        }
					);

    				// showMapByLatLong(position.coords.latitude, position.coords.longitude);
            	}
    			else
    			{

    				result = {latitude: -33.8688, longitude: 151.2195};
    				// showMapByLatLong('', -33.8688, 151.2195);
        		}

        	});
    	}
  		else
		{

			result = {latitude: -33.8688, longitude: 151.2195};
  			// showMapByLatLong('', -33.8688, 151.2195);
		}
		return result;
	}
}

$Ready(function(){
	$('#ynfevent-invite-option .ynfevent-invite-option-item-js').off('click').on('click', function(){
		$('#text-js').text($(this).find('a').text());
	});

    $('[data-toggle="tooltip"]').tooltip().find('a').attr('title', '');

    // slider
    $("#fevent-detail-slider").owlCarousel({
    	nav: true,
    	items: 1,
    	navText: ['<i class="ico ico-angle-left"></i>','<i class="ico ico-angle-right"></i>']
    });

    if($('.fevent-block-ongoing-js').length) {
       ync_mode_view.init('fevent-block-ongoing-js');
   	};

   	if($('.fevent-block-upcoming-js').length) {
       ync_mode_view.init('fevent-block-upcoming-js');
   	};

   	if($('.fevent-page-index-js').length) {
   		var page = $('.fevent-page-index-js').data('page');
       ync_mode_view.init('page-' + page + '-js');
   	};
})
