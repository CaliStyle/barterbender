var ynfundraising_map = {
    oMarker: null,
    oGeoCoder: null,
    sQueryAddress: null,
    oMap: null,
    oLatLng: null,
    bDoTrigger: false,
    inputToMap: function () {
        /* This function takes the information from the input fields and moves the map towards that location*/
        ynfundraising_map.sQueryAddress = $('#location_venue').val() + ' ' + $('#address').val() + ' ' + $('#city').val();
        if ($('#js_country_child_id_value option:selected').val() > 0) {
            ynfundraising_map.sQueryAddress += ' ' + $('#js_country_child_id_value option:selected').text();
        }
        ynfundraising_map.sQueryAddress += ' ' + ($('#country_iso option:selected').text() == 'Select:' ? '' : $('#country_iso option:selected').text());
        ynfundraising_map.oGeoCoder.geocode({
                'address': ynfundraising_map.sQueryAddress
            }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    ynfundraising_map.oLatLng = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
                    ynfundraising_map.oMarker.setPosition(ynfundraising_map.oLatLng);
                    ynfundraising_map.oMap.panTo(ynfundraising_map.oLatLng);
                    $('#input_gmap_latitude').val(ynfundraising_map.oMarker.position.lat());
                    $('#input_gmap_longitude').val(ynfundraising_map.oMarker.position.lng());
                }
            }
        );
        if (ynfundraising_map.bDoTrigger) {
            google.maps.event.trigger(ynfundraising_map.oMarker, 'dragend');
            ynfundraising_map.bDoTrigger = false;
        }
    },

    initialize: function () {
        ynfundraising_map.oGeoCoder = new google.maps.Geocoder();
        if (typeof(aInfo) == 'undefined') {
            aInfo = {latitude: 0, longitude: 0};
        }
        ynfundraising_map.oLatLng = new google.maps.LatLng(aInfo.latitude, aInfo.longitude);

        var myOptions = {
            zoom: 11,
            center: ynfundraising_map.oLatLng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl: false,
            streetViewControl: false,
            width: 400,
            height: 400
        };
        if (!document.getElementById("mapHolder")) {
            return false;
        }
        ynfundraising_map.oMap = new google.maps.Map(document.getElementById("mapHolder"), myOptions);
        ynfundraising_map.oMarker = new google.maps.Marker({
            draggable: true,
            position: ynfundraising_map.oLatLng,
            map: ynfundraising_map.oMap
        });


        /* Fake the dragend to populate the city and other input fields */
        google.maps.event.trigger(ynfundraising_map.oMarker, 'dragstart');
        google.maps.event.trigger(ynfundraising_map.oMarker, 'dragend');
        google.maps.event.addListener(ynfundraising_map.oMarker, "dragend", function () {
            $('#input_gmap_latitude').val(ynfundraising_map.oMarker.position.lat());
            $('#input_gmap_longitude').val(ynfundraising_map.oMarker.position.lng());
            ynfundraising_map.oLatLng = new google.maps.LatLng(ynfundraising_map.oMarker.position.lat(), ynfundraising_map.oMarker.position.lng());
            ynfundraising_map.oGeoCoder.geocode({
                    'latLng': ynfundraising_map.oLatLng
                },
                function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        $('#city').val('');
                        $('#postal_code').val('');
                        for (var i in results[0]['address_components']) {
                            if (results[0]['address_components'][i]['types'][0] == 'locality') {
                                $('#city').val(results[0]['address_components'][i]['long_name']);
                            }
                            if (results[0]['address_components'][i]['types'][0] == 'country') {
                                var sCountry = $('#country_iso option:selected').val();
                                $('#js_country_iso_option_' + results[0]['address_components'][i]['short_name']).attr('selected', 'selected');
                                if (sCountry != $('#country_iso option:selected').val()) {
                                    $('#country_iso').change();
                                }
                            }
                            if (results[0]['address_components'][i]['types'][0] == 'postal_code') {
                                $('#postal_code').val(results[0]['address_components'][i]['long_name']);
                            }
                            if (results[0]['address_components'][i]['types'][0] == 'street_address') {
                                $('#address').val(results[0]['address_components'][i]['long_name']);
                            }
                            if (isset($('#js_country_child_id_value')) && results[0]['address_components'][i]['types'][0] == 'administrative_area_level_1') {
                                $('#js_country_child_id_value option').each(function () {
                                    if ($(this).text() == results[0]['address_components'][i]['long_name']) {
                                        $(this).attr('selected', 'selected');
                                        bHasChanged = true;
                                    }
                                });
                            }
                        }
                    }
                });
        });
        /* Sets events for when the user inputs info */
        ynfundraising_map.inputToMap();
    },
    loadScript: function () {
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = '//maps.google.com/maps/api/js?sensor=false&callback=ynfundraising_map.initialize&key=' + googleApiKey;
        document.body.appendChild(script);
    }
};


