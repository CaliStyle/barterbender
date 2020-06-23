$Behavior.advancedmarketplaceShowImage = function(){
	$('.js_marketplace_click_image').click(function(){
		var oNewImage = new Image();
		oNewImage.onload = function(){
			$('#js_marketplace_click_image_viewer').show();
			$('#js_marketplace_click_image_viewer_inner').html('<img src="' + this.src + '" alt="" />');
			$('#js_marketplace_click_image_viewer_close').show();
		};
		oNewImage.src = $(this).attr('href');

		return false;
	});

	$('#js_marketplace_click_image_viewer_close a').click(function(){
		$('#js_marketplace_click_image_viewer').hide();
		return false;
	});
}

function follow(type, user_id, follow_id)
{
	$.ajaxCall('advancedmarketplace.follow', 'type='+type+'&user_id='+user_id+'&user_follow_id='+follow_id);
}

var advmarketplace = {
    sCorePath: ''
    , showDetailMapViewAdvMarkeplace: function (iListingId) {

        $Core.ajax('advancedmarketplace.loadAjaxDetailMapView',
            {
                type: 'POST',
                params:
                    {
                        'iListingId': iListingId
                    },
                success: function (sOutput) {
                    datas = [];
                    contents = [];

                    var oOutput = $.parseJSON(sOutput);
                    // console.log(oOutput.data['location']);
                    if (oOutput.status == 'SUCCESS') {
                        advmarketplace.sCorePath = oOutput.sCorePath;
                        var aData = oOutput.data;
                        // console.log(oOutput.data['lat']);
                        item_data = [];
                        item_data['latitude'] = oOutput.data['lat'];
                        item_data['longitude'] = oOutput.data['lng'];
                        item_data['location'] = oOutput.data['location'];
                        datas.push(item_data);
                        contents.push(oOutput);

                        var advmarketplace_detail_map = null;
                        advmarketplace.showMapsWithData('advmarketplace_detail_map', datas, contents, advmarketplace_detail_map);
                    }
                }
            });

    }
    , showMapsWithData: function (id, datas, contents, objectMap) {
        if ($('#' + id).length > 0 && datas.length > 0) {
            var center = new google.maps.LatLng(datas[0]['latitude'], datas[0]['longitude']);
            var neighborhoods = [];
            var markers = [];
            var iterator = 0;
            for (i = 0; i < datas.length; i++) {
                neighborhoods.push(new google.maps.LatLng(datas[i]['latitude'], datas[i]['longitude']));
            }

            console.log(contents[0].data['location']);
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
                    infowindow.setContent(advmarketplace.showExtraInfo(contents[0].data));
                    infowindow.open(objectMap, markers[i]);
                });
            }

            showMapsWithData_initialize();
        }
    }
    ,showExtraInfo : function(info){
        var location = info['location'];
        sHtml = '';

        sHtml = location;
        sHtml += '<div style="display: inline-block; padding-left: 10px;"><a href="https://maps.google.com/maps?daddr='+location+'" target="_blank"><img src="' + advmarketplace.sCorePath + 'module/advancedmarketplace/static/image/default/icon-getdirection.png" /> '+oTranslations['advancedmarketplace.get_directions']+'</a></div></div>';

        return sHtml;
    }
}