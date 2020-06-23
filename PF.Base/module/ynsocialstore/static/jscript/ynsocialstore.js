// PF.event.on('on_page_column_init_end', function() {
//   console.log('Loading socket event...');
// });

$Core.ynsocialstore =
    {
        sUrl: '',

        url: function (sUrl) {
            this.sUrl = sUrl;
        },

        action: function (oObj, sAction) {
            aParams = $.getParams(oObj.href);

            $('.dropContent').hide();

            switch (sAction) {
                case 'edit':
                    window.location.href = this.sUrl + 'add/id_' + aParams['id'] + '/';
                    break;
                case 'delete':
                    var url = this.sUrl;
                    $Core.jsConfirm({}, function () {
                        window.location.href = url + 'delete_' + aParams['id'] + '/';
                    }, function () {
                    });
                    break;
                default:

                    break;
            }

            return false;
        },

        dropzoneOnSending: function (data, xhr, formData) {
            $('#js_ynsocialstore_form_upload_images').find('input[type="hidden"]').each(function () {
                formData.append($(this).prop('name'), $(this).val());
            });
        },

        dropzoneOnSuccess: function (ele, file, response) {
            $Core.ynsocialstore.processResponse(ele, file, response);
        },

        dropzoneOnError: function (ele, file) {

        },
        dropzoneQueueComplete: function () {
            $('#js_listing_done_upload').show();
        },
        processResponse: function (t, file, response) {
            response = JSON.parse(response);
            if (typeof response.id !== 'undefined') {
                file.item_id = response.id;
                if (typeof t.data('submit-button') !== 'undefined') {
                    var ids = '';
                    if (typeof $(t.data('submit-button')).data('ids') !== 'undefined') {
                        ids = $(t.data('submit-button')).data('ids');
                    }
                    $(t.data('submit-button')).data('ids', ids + ',' + response.id);
                }
            }
            // show error message
            if (typeof response.errors != 'undefined') {
                for (var i in response.errors) {
                    if (response.errors[i]) {
                        $Core.dropzone.setFileError('ynsocialstore', file, response.errors[i]);
                        return;
                    }
                }
            }
            return file.previewElement.classList.add('dz-success');
        }
    };

var ynsocialstore = {
    initSlide: false,

    pt: [],
    myCartChanged: {deletedcart:'',usercartid: '',updatedcart:[],tempdelete:{}},
    myCartLimit: {rawLimit:{},realLimit:{},templimit:{}},
    cookieCompareStoreName: 'ynsocialstore_compare_store_name',
    cookieCompareProductName: 'ynsocialstore_compare_product_name',
    params: false,
    setParams: function (params) {
        ynsocialstore.params = JSON.parse(params);
    },
    initStoretype: function () {
        // bind click to redirect "add" page
        $('#ynsocialstore_storetype .ynsocialstore-createastore').click(function () {
            if ($(this).data('module') != '' && $(this).data('item') != '') {
                window.location.href = $(this).data('url') + 'package_' + $(this).data('packageid') + '/module_' + $(this).data('module') + '/item_' + $(this).data('item');
            }
            else {
                window.location.href = $(this).data('url') + 'package_' + $(this).data('packageid');
            }
        });
    },
    init: function () {
        if ($('#ynsocialstore_pagename').length > 0) {
            var ynsocialstore_pagename = $('#ynsocialstore_pagename').val();
            switch (ynsocialstore_pagename) {
                case 'storetype':
                    ynsocialstore.initStoretype();
                    break;
                case 'addstore':
                    ynsocialstore.initAddStore();
                    break;
                case 'addproduct':
                    ynsocialstore.initAddProduct();
                    break;
                case 'indexstore':
                    ynsocialstore.initIndexStore();
                    break;
                case 'indexproduct':
                    ynsocialstore.initIndexProduct();
                    break;
                case 'detailstore':
                    ynsocialstore.initDetailStore();
                    break;
                case 'detailproduct':
                    ynsocialstore.initDetailProduct();
                    break;
            }
        }

    },
    initIndexProduct: function () {
        ynsocialstore.initValidator($('#ynsocialstore_adv_search_product_form'));
        $('#ynsocialstore_adv_search_product_form #price_from').rules('add', {
            number: true,
            min: 0
        });
        $('#ynsocialstore_adv_search_product_form #price_to').rules('add', {
            number: true,
            min: 0
        });
    },
    initIndexStore: function () {
        // search location by google api
        if ($("#page_ynsocialstore_store_index #ynsocialstore_location").length > 0) {
            var input = $("#page_ynsocialstore_store_index #ynsocialstore_location")[0];

            if (window.google) {
                // do nothing
                var autocomplete = new google.maps.places.Autocomplete(input);
                google.maps.event.addListener(autocomplete, 'place_changed', function () {
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        return;
                    }

                    var $parent = $(input).closest('.ynsocialstore-location');
                    $parent.find('[data-inputid="address"]').val($parent.find('[data-inputid="fulladdress"]').val());
                    $parent.find('[data-inputid="lat"]').val(place.geometry.location.lat());
                    $parent.find('[data-inputid="lng"]').val(place.geometry.location.lng());
                });
            }
        }

        ynsocialstore.initValidator($('#ynsocialstore_adv_search_form'));
        $('#ynsocialstore_adv_search_form #radius').rules('add', {
            number: true,
            min: 1
        });
    },
    initDetailStore: function () {
        ynsocialstore.loadAjaxMapDetail($('#ynsocialstore_detail_store_id').val());
    },
    sCorePath: '',
    loadAjaxMapDetail: function (iStoreId) {
        $Core.ajax('ynsocialstore.loadAjaxMapDetail',
            {
                type: 'POST',
                params: {
                    iStoreId: iStoreId
                },
                success: function (sOutput) {
                    datas = [];
                    contents = [];

                    var oOutput = $.parseJSON(sOutput);
                    if (oOutput.status == 'SUCCESS') {
                        ynsocialstore.sCorePath = oOutput.sCorePath;
                        var aData = oOutput.data;

                        $.each(aData, function (key, value) {
                            item_data = [];
                            item_data['latitude'] = value['latitude'];
                            item_data['longitude'] = value['longitude'];
                            item_data['location'] = value['title'];
                            datas.push(item_data);
                            contents.push(value['title'] + ' , ' + value['address']);
                        });
                        ynsocialstore.showMapsWithData('ynsocialstore_detail_mapview', datas, contents);

                    }
                }
            });

    }, showMapsWithData: function (id, datas, contents) {
        if ($('#' + id).length > 0 && datas.length > 0) {
            var center = new google.maps.LatLng(datas[0]['latitude'], datas[0]['longitude']);
            var neighborhoods = [];
            var markers = [];
            var iterator = 0;
            for (i = 0; i < datas.length; i++) {
                neighborhoods.push(new google.maps.LatLng({
                    lat: parseFloat(datas[i]['latitude']),
                    lng: parseFloat(datas[i]['longitude'])
                }));
            }

            function showMapsWithData_initialize() {
                var mapOptions = {
                    zoom: 15,
                    center: center,
                };

                map = new google.maps.Map(document.getElementById(id), mapOptions);
                var bounds = new google.maps.LatLngBounds();

                for (var i = 0; i < neighborhoods.length; i++) {
                    showMapsWithData_addMarker(i);
                    if (neighborhoods.length > 1) {
                        bounds.extend(neighborhoods[i]);
                    }

                }

                if (neighborhoods.length > 1) {
                    map.fitBounds(bounds);
                }
            }

            function showMapsWithData_addMarker(i) {
                marker = new google.maps.Marker({
                    position: neighborhoods[iterator],
                    map: map,
                    draggable: false,
                    animation: google.maps.Animation.DROP,
                    icon: datas[i]['icon']
                })
                markers.push(marker);
                iterator++;
                infowindow = new google.maps.InfoWindow({});
                google.maps.event.addListener(marker, 'mouseover', function () {
                    infowindow.close();
                    infowindow.setContent(ynsocialstore.showExtraInfo(contents[i]));
                    infowindow.open(map, markers[i]);
                });
            }

            showMapsWithData_initialize();
        }
    }, showExtraInfo: function (info) {


        sHtml = '';

        if ($.isArray(info)) {

            if (info.length > 1) {
                sHtml += '<div class="ynsocialstore-item-map-header" style="background-color: #f4f4f4; color: #5f74a6">';
                sHtml += '<span style="background-color: #40474e; color: #fff; padding: 0 5px; display: inline-block; margin-right: 5px;">' + info.length + '</span>' + oTranslations['ynsocialstore.stores'];
                sHtml += '</div>';
                sHtml += '<div class="ynsocialstore-item-map-main">';
            }

            $.each(info, function (key, aBus) {
                sHtml += '<div class="ynsocialstore-item-map" style="width: 300px;margin-top: 5px; height: 75px; border-bottom: 1px solid #ebebeb; font-size: 12px; overflow: hidden; box-sizing: border-box;">';
                sHtml += '<div class="ynsocialstore-item-map-image" style="position: relative; margin-right: 10px; float: left;">';
                sHtml += '<a href="' + aBus['url_detail'] + '"><img width=64 height=64 src="' + aBus['url_image'] + '"></a>';
                if (aBus['featured']) {
                    sHtml += '<div class="ynsocialstore-item-map-featured" style="background-color: #39b2ea; color: #fff; text-transform: uppercase; position: absolute; top: 0; left: -4px; display: block; height: 18px; line-height: 18px; padding: 0 6px 0 10px; font-weight: bold; font-size: 10px; -webkit-box-shadow: inset 4px 0 0 rgba(0, 0, 0, 0.3), 2px 2px 0 rgba(0, 0, 0, 0.2); -moz-box-shadow: inset 4px 0 0 rgba(0, 0, 0, 0.3), 2px 2px 0 rgba(0, 0, 0, 0.2); box-shadow: inset 4px 0 0 rgba(0, 0, 0, 0.3), 2px 2px 0 rgba(0, 0, 0, 0.2);">' + oTranslations['ynsocialstore.featured'] + '</div>';
                }
                sHtml += '</div>';
                sHtml += '<div class="ynsocialstore-item-map-title" style="color: #3b5998; font-size: 14px; font-weight: bold; margin-bottom: 3px; max-width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><a href="' + aBus['url_detail'] + '">' + aBus['title'] + '</a></div>';
                sHtml += '<div class="ynsocialstore-item-map-location" style="max-width: 250px;margin-bottom:4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><i class="fa fa-map-marker"></i> ' + aBus['location'] + '</div>';
                sHtml += '<div class="ynsocialstore-item-map-info" style="display: inline-block;">';
                sHtml += '<span class="ynstore-rating">';
                for (i = 0; i < 5; i++) {
                    if (i < aBus['rating']) {
                        sHtml += '<i class="ico ico-star" style="color: red; margin-right: 1px;"></i>';
                    } else {
                        sHtml += '<i class="ico ico-star" style="color: #ccc; margin-right: 1px;"></i>';
                    }
                }
                sHtml += '</span>';
                sHtml += '<span style="vertical-align: top; padding-left: 5px;">(' + aBus['reviews'] + ')</span>';
                sHtml += '</div">';

                sHtml += '<div style="display: inline-block; padding-left: 10px;"><a href="https://maps.google.com/maps?daddr=' + aBus['location_address'] + '" target="_blank"> <i class="ico ico-compass"></i> ' + oTranslations['ynsocialstore.get_directions'] + '</a></div></div>';

                sHtml += '</div>';
            });

            if (info.length > 1) {
                sHtml += '</div>';
            }

        }
        else {
            sHtml = info;
            sHtml += '<div style="display: inline-block; padding-left: 10px;"><a href="https://maps.google.com/maps?daddr=' + info + '" target="_blank"><i class="ico ico-compass"></i> ' + oTranslations['ynsocialstore.get_directions'] + '</a></div></div>';
        }

        return sHtml;
    },
    appendPredefined: function (ele, classname) {
        var now = +new Date();
        switch (classname) {
            case 'location':
                var count = $('#ynsocialstore_add #ynsocialstore_locationlist .ynsocialstore-location').length + 1;

                var oCloned = $('#ynsocialstore_add #ynsocialstore_locationlist .ynsocialstore-location:first').clone();
                oCloned.find('input').attr('value', '');
                oCloned.find('#ynsocialstore_location_99999').attr('id', 'ynsocialstore_location_' + now);
                oCloned.find('#ynsocialstore_delete').show();
                oCloned.find('#ynsocialstore_add').remove();
                oCloned.find('span.text-danger').remove();
                var oFirst = oCloned.clone();
                var firstAnswer = oFirst.html();
                $(ele).closest('#ynsocialstore_locationlist').append('<div data-item="' + now + '" class="ynsocialstore-location">' + firstAnswer + '</div>');

                // search location by google api
                var input = ($("#ynsocialstore_add #ynsocialstore_locationlist #ynsocialstore_location_" + now)[0]);
                if (window.google) {
                    // do nothing
                } else {
                    // ynsocialstore.alertMessage(oTranslations['ynsocialstore.cannot_load_google_api_library_please_reload_the_page_and_try_again']);
                    return false;
                }
                var autocomplete = new google.maps.places.Autocomplete(input);
                google.maps.event.addListener(autocomplete, 'place_changed', function () {
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        return;
                    }

                    var $parent = $(input).closest('.ynsocialstore-location');
                    $parent.find('[data-inputid="address"]').val($parent.find('[data-inputid="fulladdress"]').val());
                    $parent.find('[data-inputid="lat"]').val(place.geometry.location.lat());
                    $parent.find('[data-inputid="lng"]').val(place.geometry.location.lng());
                    $parent.find('.text-danger').last().remove();
                    $parent.find('.text-danger').removeClass('text-danger');
                });
                break;
            case 'addinfo':
                var oCloned = $('#ynsocialstore_add #ynsocialstore_addinfolist .ynsocialstore-addinfo:first').clone();
                oCloned.find('input').attr('value', '');
                oCloned.find('#ynsocialstore_delete').show();
                oCloned.find('#ynsocialstore_add').remove();
                oCloned.find('span.text-danger').remove();
                var oFirst = oCloned.clone();
                var firstAnswer = oFirst.html();
                $(ele).closest('#ynsocialstore_addinfolist').append('<div data-item="' + now + '" class="ynsocialstore-addinfo">' + firstAnswer + '</div>');
                break;
            case 'phone':
                var oCloned = $(ele).closest('.ynstore_store-phone').clone();
                oCloned.find('input').attr('value', '');
                oCloned.find('#ynsocialstore_delete').show();
                oCloned.find('#ynsocialstore_add').remove();
                oCloned.find('span.text-danger').remove();
                var oFirst = oCloned.clone();
                var firstAnswer = oFirst.html();
                $(ele).closest('#ynsocialstore_phonelist').append('<div class="ynstore_store-phone">' + firstAnswer + '</div>');
                break;
            case 'fax':
                var oCloned = $(ele).closest('.ynstore_store-fax').clone();
                oCloned.find('input').attr('value', '');
                oCloned.find('#ynsocialstore_delete').show();
                oCloned.find('#ynsocialstore_add').remove();
                var oFirst = oCloned.clone();
                var firstAnswer = oFirst.html();
                $(ele).closest('#ynsocialstore_faxlist').append('<div class="ynstore_store-fax">' + firstAnswer + '</div>');
                break;
            case 'web_address':
                var oCloned = $(ele).closest('.ynstore_store-website').clone();
                oCloned.find('input').attr('value', '');
                oCloned.find('#ynsocialstore_delete').show();
                oCloned.find('#ynsocialstore_add').remove();
                var oFirst = oCloned.clone();
                var firstAnswer = oFirst.html();
                $(ele).closest('#ynsocialstore_websitelist').append('<div class="ynstore_store-website">' + firstAnswer + '</div>');
                break;
        }
    },
    removePredefined: function (ele, classname) {
        switch (classname) {
            case 'location':
                $(ele).closest('.ynsocialstore-location').remove();
                break;
            case 'phone':
                $(ele).closest('.ynstore_store-phone').remove();
                break;
            case 'fax':
                $(ele).closest('.ynstore_store-fax').remove();
                break;
            case 'web_address':
                $(ele).closest('.ynstore_store-website').remove();
                break;
            case 'addinfo':
                $(ele).closest('.ynsocialstore-addinfo').remove();
                break;

        }
    },
    initAddStore: function () {
        if ($('#ynsocialstore_edit_store_form').length == 0) {
            return false;
        }

        // auto select theme
        var selected = $("#ynsocialstore_add #ynsocialstore_theme input[type='radio']:checked");
        if (selected.length == 0) {
            selected = $("#ynsocialstore_add #ynsocialstore_theme input[type='radio']");
            if (selected.length > 0) {
                jQuery(selected[0]).attr('checked', 'checked');
            }
        }

        // search location by google api
        if ($("#ynsocialstore_add #ynsocialstore_locationlist #ynsocialstore_location_99999").length > 0) {
            var input = ($("#ynsocialstore_add #ynsocialstore_locationlist #ynsocialstore_location_99999")[0]);

            if (window.google) {
                // do nothing
                var autocomplete = new google.maps.places.Autocomplete(input);
                google.maps.event.addListener(autocomplete, 'place_changed', function () {
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        return;
                    }

                    var $parent = $(input).closest('.ynsocialstore-location');
                    $parent.find('[data-inputid="address"]').val($parent.find('[data-inputid="fulladdress"]').val());
                    $parent.find('[data-inputid="lat"]').val(place.geometry.location.lat());
                    $parent.find('[data-inputid="lng"]').val(place.geometry.location.lng());
                    $parent.find('.text-danger').last().remove();
                    $parent.find('.text-danger').removeClass('text-danger');
                });
            }
        } else {
            $("#ynsocialstore_add #ynsocialstore_locationlist").find('[data-inputid="fulladdress"]').each(function () {

                var input = this;
                if (window.google) {
                    // do nothing
                    var autocomplete = new google.maps.places.Autocomplete(input);
                    google.maps.event.addListener(autocomplete, 'place_changed', function () {
                        var place = autocomplete.getPlace();
                        if (!place.geometry) {
                            return;
                        }

                        var $parent = $(input).closest('.ynsocialstore-location');
                        $parent.find('[data-inputid="address"]').val($parent.find('[data-inputid="fulladdress"]').val());
                        $parent.find('[data-inputid="lat"]').val(place.geometry.location.lat());
                        $parent.find('[data-inputid="lng"]').val(place.geometry.location.lng());
                        $parent.find('.text-danger').last().remove();
                        $parent.find('.text-danger').removeClass('text-danger');
                    });
                }
            });
        }
        if ($("#ynsocialstore_add #ynsocialstore_locationlist #ynsocialstore_location_1").length > 0) {
            var input = ($("#ynsocialstore_add #ynsocialstore_locationlist #ynsocialstore_location_1")[0]);
            if (window.google) {
                // do nothing
            } else {
                // ynsocialstore.alertMessage(oTranslations['directory.cannot_load_google_api_library_please_reload_the_page_and_try_again']);
                return false;
            }
            var autocomplete = new google.maps.places.Autocomplete(input);
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }

                var $parent = $(input).closest('.ynsocialstore-location');
                $parent.find('[data-inputid="address"]').val($parent.find('[data-inputid="fulladdress"]').val());
                $parent.find('[data-inputid="lat"]').val(place.geometry.location.lat());
                $parent.find('[data-inputid="lng"]').val(place.geometry.location.lng());
                $parent.find('.text-danger').last().remove();
                $parent.find('.text-danger').removeClass('text-danger');
            });
        }
        // update number of feature fee following numbers of days
        $('#ynsocialstore_feature_number_days').on('keyup', ynsocialstore.onChangeFeatureFeeTotal);

        // validate form
        ynsocialstore.initValidator($('#ynsocialstore_edit_store_form'));
        jQuery.validator.addMethod('checkLocation', function () {
            var result = false;
            $('#ynsocialstore_edit_store_form #ynsocialstore_locationlist').find('[data-inputid="fulladdress"]').each(function () {
                if (this.value.length > 0) {
                    result = true;
                }
                else {
                    $('#ynsocialstore_submit_buttons').show();
                }
            });

            return result;
        }, oTranslations['ynsocialstore.address_is_required']);
        jQuery.validator.addMethod('checkPhone', function () {
            var result = false;
            $('#ynsocialstore_edit_store_form #ynsocialstore_phonelist').find('input').each(function () {
                if (this.value.length > 0) {
                    result = true;
                }
                else {
                    $('#ynsocialstore_submit_buttons').show();
                }
            });

            return result;
        }, oTranslations['this_field_is_required']);

        $('#ynsocialstore_edit_store_form #name').rules('add', {
            required: true,
            maxlength: 255
        });
        $('#ynsocialstore_edit_store_form #categories').rules('add', {
            required: true
        });
        $('#ynsocialstore_edit_store_form #ship_payment_info').rules('add', {
            required: true
        });
        $('#ynsocialstore_edit_store_form #return_policy').rules('add', {
            required: true
        });
        $('#ynsocialstore_edit_store_form #buyer_protection').rules('add', {
            required: true
        });
        $('#ynsocialstore_edit_store_form #tax').rules('add', {
            number: true,
            min: 0,
            max: 999999999,
        });
        var currentTime = new Date();
        $('#ynsocialstore_edit_store_form #established_year').rules('add', {
            digits: true,
            min: 0,
            max: currentTime.getFullYear()
        });
        $('#ynsocialstore_edit_store_form #short_description').rules('add', {
            required: true,
            maxlength: 500
        });

        $('#ynsocialstore_edit_store_form #ynsocialstore_phonelist .ynstore_store-phone:first input[name="val[phone][]"]').rules('add', {
            checkPhone: true
        });
        $('#ynsocialstore_edit_store_form #ynsocialstore_email').rules('add', {
            required: true,
            email: true
        });
        $('#ynsocialstore_edit_store_form #ynsocialstore_addinfolist').find('[data-inputid="addinfo"]').each(function () {
            $(this).rules('add', {
                maxlength: 150
            });
        });
        // preview button
        $('#ynsocialstore_add #ynsocialstore_preview').click(function () {
            ynsocialstore.showPreivewNewStore();
            return false;
        });

    },
    onChangeFeatureFeeTotal: function () {
        if ('' == $('#ynsocialstore_feature_number_days').val() || (isNaN(parseInt($('#ynsocialstore_feature_number_days').val())))) {
            $('#ynsocialstore_feature_number_days').val('');
            $('#ynsocialstore_feature_fee_total').val('');
            $('#ynsocialstore_feature_fee_total').html('0');
        } else {
            $('#ynsocialstore_feature_number_days').val(parseInt($('#ynsocialstore_feature_number_days').val()));
            $('#ynsocialstore_feature_fee_total').val(parseInt($('#ynsocialstore_feature_number_days').val()) * parseInt($('#ynsocialstore_defaultfeaturefee').val()));
            $('#ynsocialstore_feature_fee_total').html(parseInt($('#ynsocialstore_feature_number_days').val()) * parseInt($('#ynsocialstore_defaultfeaturefee').val()));
        }
    },
    viewMap: function (ele) {
        if ($('#ynsocialstore_pagename').length > 0) {
            var ynsocialstore_pagename = $('#ynsocialstore_pagename').val();
            switch (ynsocialstore_pagename) {
                case 'addstore':
                    var item = $(ele).closest('.ynsocialstore-location').data('item');
                    var obj = $('#ynsocialstore_add #ynsocialstore_locationlist').find('[data-item="' + item + '"]');
                    var latitude = obj.find('[data-inputid="lat"]').val();
                    var longitude = obj.find('[data-inputid="lng"]').val();
                    var address = obj.find('[data-inputid="address"]').val();
                    if (latitude == '' || longitude == '' || address == '') {
                        // Open directly via API
                        $.magnificPopup.open({
                            items: {
                                src: '<div class="white-popup-block" style="width: 300px;">' + oTranslations['ynsocialstore.please_enter_location'] + '</div>', // can be a HTML string, jQuery object, or CSS selector
                                type: 'inline'
                            }
                        });
                        return false;
                    }
                    else {
                        $.magnificPopup.open({
                            items: {
                                src: '<div class="white-popup-block-without-width" >' + '<div id="ynsocialstore_viewmap_' + item + '" style="height: 450px;"></div>' + '</div>', // can be a HTML string, jQuery object, or CSS selector
                                type: 'inline'
                            }
                        });

                        ynsocialstore.showMapsWithData('ynsocialstore_viewmap_' + item, [{
                            latitude: latitude,
                            longitude: longitude
                        }], [address]);
                    }
                    break;
                case 'editstore':
                    var item = $(ele).closest('.ynsocialstore-location').data('item');
                    var obj = $('#ynsocialstore_edit #ynsocialstore_locationlist').find('[data-item="' + item + '"]');
                    var latitude = obj.find('[data-inputid="lat"]').val();
                    var longitude = obj.find('[data-inputid="lng"]').val();
                    var address = obj.find('[data-inputid="address"]').val();
                    if (latitude == '' || longitude == '' || address == '') {
                        // Open directly via API
                        $.magnificPopup.open({
                            items: {
                                src: '<div class="white-popup-block" style="width: 300px;">' + oTranslations['ynsocialstore.please_enter_location'] + '</div>', // can be a HTML string, jQuery object, or CSS selector
                                type: 'inline'
                            }
                        });
                        return false;
                    }
                    else {
                        $.magnificPopup.open({
                            items: {
                                src: '<div class="white-popup-block-without-width" >' + '<div id="ynsocialstore_viewmap_' + item + '" style="height: 450px;"></div>' + '</div>', // can be a HTML string, jQuery object, or CSS selector
                                type: 'inline'
                            }
                        });

                        ynsocialstore.showMapsWithData('ynsocialstore_viewmap_' + item, [{
                            latitude: latitude,
                            longitude: longitude
                        }], [address]);
                    }
                    break;
            }
        }
    },
    viewMapSuccess: function (item) {
        if ($('#ynsocialstore_pagename').length > 0) {
            var ynsocialstore_pagename = $('#ynsocialstore_pagename').val();
            switch (ynsocialstore_pagename) {
                case 'add':
                    var obj = $('#ynsocialstore_add #ynsocialstore_locationlist').find('[data-item="' + item + '"]');
                    if (obj.length > 0) {
                        var latitude = obj.find('[data-inputid="lat"]').val();
                        var longitude = obj.find('[data-inputid="lng"]').val();
                        var address = obj.find('[data-inputid="address"]').val();
                        if (latitude == '' || longitude == '' || address == '') {
                            ynsocialstore.getCurrentPosition();
                        }
                        else {
                            ynsocialstore.showMapByLatLong(address, latitude, longitude);
                        }
                    }
                    break;
            }
        }
    },
    initValidator: function (element) {
        jQuery.validator.messages.required = oTranslations['this_field_is_required'];
        jQuery.validator.messages.url = oTranslations['please_enter_a_valid_url_for_example_http_example_com'];
        jQuery.validator.messages.accept = oTranslations['please_enter_a_value_with_a_valid_extension'];
        jQuery.validator.messages.minlength = oTranslations['please_enter_at_least_0_characters'];
        jQuery.validator.messages.min = oTranslations['please_enter_a_value_greater_than_or_equal_to_0'];
        jQuery.validator.messages.number = oTranslations['please_enter_a_valid_number'];
        jQuery.validator.messages.email = oTranslations['please_enter_a_valid_email_address'];
        jQuery.validator.messages.maxlength = oTranslations['please_enter_no_more_than_0_characters'];
        jQuery.validator.messages.digits = oTranslations['please_enter_only_digits'];
        $.data(element[0], 'validator', null);
        element.validate({
            errorPlacement: function (error, element) {
                if (element.is(":radio") || element.is(":checkbox") || element.is("textarea") || element.is('#ynstore_product_discount_value')) {
                    error.appendTo($(element).closest('.form-group'));
                } else {
                    error.appendTo(element.parent());
                }
            },
            errorClass: 'text-danger',
            errorElement: 'span',
            debug: false
        });
    },
    repositionCoverPhoto: function (iId, iOwnerId) {
        $('.profiles_banner').addClass('editing');
        $('#change_cover_function').hide();
        var y1 = $('.profiles_banner_bg .cover').height();
        var y2 = $('.profiles_banner_bg .cover img').height();
        $('.profiles_banner_bg .cover img').draggable({
            axis: 'y',
            scroll: false,
            drag: function (event, ui) {
                if (ui.position.top >= 0) {
                    ui.position.top = 0;
                }
                else if (ui.position.top <= y1 - y2) {
                    ui.position.top = y1 - y2;
                }
            },
            stop: function (evt, ui) {
                $.ajaxCall('ynsocialstore.repositionCoverPhoto', $.param({
                    id: iId,
                    position: ui.position.top,
                    iOwnerId: iOwnerId
                }));
            }
        }).parent().parent().append('<div id="save_reposition_cover" class="btn btn-primary" onclick="$(\'#change_cover_function\').show(); $(\'.pages_header_cover img, .profiles_banner_bg .cover img\').draggable(\'destroy\');$(\'.profiles_banner\').removeClass(\'editing\');$(\'#save_reposition_cover\').remove();">' + oTranslations['save'] + '</div>');
    },
    showFullAddress: function (ele) {
        ynstore_btn_viewmore = $('.ynstore-multiple-address');
        ynstore_btn_viewmore.toggleClass('active');
        if ((ynstore_btn_viewmore).hasClass('active')) {
            $(ele).html('view less <i class="ico ico-angle-up"></i>');
        } else {
            $(ele).html('view more <i class="ico ico-angle-down"></i>');
        }
    },
    initViewMode: function (block_id) {

        var yn_viewmodes_block = $('.ynstore-view-modes-block');

        var yn_cookie_viewmodes = getCookie(block_id + 'ynviewmodes');
        //Check if have cookie
        if (!yn_cookie_viewmodes) {
            yn_cookie_viewmodes = 'grid';
        }
        else if (yn_cookie_viewmodes == 'map') {
            ynsocialstore.showMapView('newstore');
        }

        yn_viewmodes_block.attr('class', 'ynstore-view-modes-block');
        yn_viewmodes_block.addClass('yn-viewmode-' + yn_cookie_viewmodes);

        $('.ynstore-view-modes-block span[data-mode=' + yn_cookie_viewmodes + ']').addClass('active');

        $('#' + block_id + ' .yn-view-mode').click(function () {
            //Get data-mode
            var yn_viewmode_data = $(this).attr('data-mode');

            //Remove class active
            $(this).parent('.yn-view-modes').find('.yn-view-mode').removeClass('active');

            //Add class active
            $(this).addClass('active');
            if (yn_viewmode_data == 'map') {
                ynsocialstore.showMapView('newstore');
            }
            //Set view mode
            yn_viewmodes_block.attr('class', 'ynstore-view-modes-block');
            yn_viewmodes_block.addClass('yn-viewmode-' + yn_viewmode_data);
            setCookie(block_id + 'ynviewmodes', yn_viewmode_data);
        });
    },
    addAjaxForCreateNewItem: function (store_id, type) {
        $('#ynsocialstore_add_new_item').click(function () {
            $.ajaxCall('ynsocialstore.setStoreSession', $.param({store_id: store_id, type: type}), 'GET');
            return false;
        });
    },
    featureStoreInBox: function (ele, store_id) {
        tb_show(oTranslations['ynsocialstore.feature_this_store'], $.ajaxBox('ynsocialstore.featureStoreInBox', 'height=300&width=420&iStoreId=' + store_id));
        return false;
    },
    updateFeatureStore: function (ele, store_id, iType) {
        $.ajaxCall('ynsocialstore.featureStore', $.param({
            iStoreId: store_id,
            bIsFeatured: iType,
            onAdmin: false
        }), 'POST');
        return false;
    },
    closeStore: function (store_id, owner_id, status) {
        $.ajaxCall('ynsocialstore.closeStore', $.param({
            iStoreId: store_id,
            iOwnerId: owner_id,
            sStatus: status,
            onAdmin: false
        }));
        return false;

    },
    openStore: function (store_id, owner_id, status) {
        $.ajaxCall('ynsocialstore.reopenStore', $.param({
            iStoreId: store_id,
            iOwnerId: owner_id,
            sStatus: status,
            onAdmin: false
        }));
        return false;
    },
    updateFavorite: function (store_id, iType) {
        $.ajaxCall('ynsocialstore.updateFavorite', $.param({iStoreId: store_id, bFavorite: iType}));
        return false;
    },
    updateFollow: function (store_id, iType) {
        $.ajaxCall('ynsocialstore.updateFollow', $.param({iStoreId: store_id, bFollowing: iType}));
        return false;
    },
    approveStore: function (store_id, status) {
        $.ajaxCall('ynsocialstore.approveStore', $.param({iStoreId: store_id, sStatus: status}));
        return false;
    },
    denyStore: function (store_id, status) {
        $.ajaxCall('ynsocialstore.denyStore', $.param({iStoreId: store_id, sStatus: status}));
        return fa
    },
    confirmDeleteStore: function (store_id, owner_id, is_detail) {
        $Core.jsConfirm({message: oTranslations['ynsocialstore.are_you_sure_you_want_to_delete_this_store']}, function(){
            ynsocialstore.deleteStore(store_id, owner_id, is_detail);
        });

    },
    deleteStore: function (iStoreId, owner_id, is_detail) {
        $.ajaxCall('ynsocialstore.deleteStore', $.param({
            iStoreId: iStoreId,
            iOwnerId: owner_id,
            bIsDetail: is_detail
        }));
    },
    advSearchDisplay: function () {
        var $form = $('#ynsocialstore_adv_search');
        var $flag = $('#flag_advancedsearch');
        var $btn = $('#stAdvSearch');
        if ($flag.val() == 1) {
            $form.slideUp(200);
            $flag.val(0);
            $btn.removeClass('active');
        }
        else {
            $form.slideDown(200);
            $btn.addClass('active');
            $flag.val(1);
        }

        return false;
    },

    advSearchProductDisplay: function () {
        var $form = $('#ynsocialstore_adv_searchproduct');
        var $flag = $('#flag_advancedsearchproduct');
        var $btn = $('#stAdvSearchProduct');
        if ($flag.val() == 1) {
            $form.slideUp(200);
            $flag.val(0);
            $btn.removeClass('active');
        }
        else {
            $form.slideDown(200);
            $btn.addClass('active');
            $flag.val(1);
        }

        return false;
    },
    getIndexCurrentPosition: function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                if (position.coords.latitude) {

                    result = {latitude: position.coords.latitude, longitude: position.coords.longitude};

                    var latLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                            latLng: latLng
                        },
                        function (responses) {
                            if (responses && responses.length > 0) {
                                $("#ynsocialstore_adv_search_form #ynsocialstore_location").val(responses[0].formatted_address);
                                $("#ynsocialstore_adv_search_form input[data-inputid='address']").val(responses[0].formatted_address);
                                $("#ynsocialstore_adv_search_form input[data-inputid='lat']").val(position.coords.latitude);
                                $("#ynsocialstore_adv_search_form input[data-inputid='lng']").val(position.coords.longitude);

                            }

                        }
                    );

                }
            });
        }
    },
    showMapView: function (typeStore) {

        $Core.ajax('ynsocialstore.loadAjaxMapView',
            {
                type: 'POST',
                params: {
                    typeStore: typeStore
                },
                success: function (sOutput) {
                    datas = [];
                    contents = [];

                    var oOutput = $.parseJSON(sOutput);
                    if (oOutput.status == 'SUCCESS') {
                        ynsocialstore.sCorePath = oOutput.sCorePath;
                        var aData = oOutput.data;
                        $.each(aData, function (key, value) {
                            item_data = [];
                            item_data['latitude'] = value[0]['latitude'];
                            item_data['longitude'] = value[0]['longitude'];
                            item_data['location'] = value[0]['location'];
                            item_data['location_address'] = value[0]['location_address'];
                            datas.push(item_data);
                            contents.push(value);
                        });
                        if (typeStore == 'newstore') {
                            var ynstore_new_store_map = null;
                            ynsocialstore.showMapsWithData('ynstore_new_store_map', datas, contents, ynstore_new_store_map);
                        }
                        else {
                            var ynstore_new_arrivals_map = null;
                            ynsocialstore.showMapsWithData('ynstore_new_arrivals_map', datas, contents, ynstore_new_arrivals_map);
                        }

                    }
                }
            });
    },
    copy_embed_code: function (ele) {
        var block = $('.ynstore_store_html_code_block');
        $('textarea', block).get(0).select();
    },
    initRating: function () {

        var holder = $('#ynstore-rating-holder'),
            star = $('#ynstore-rating-holder .yn-star'),
            sect = '#ynstore-rating-section .yn-star',
            rate = $('#ynstore-current-rating');
        if (holder.lenght == 0) return false;
        ynsocialstore.updateCountDownCharacter();
        $('#review-content').change(ynsocialstore.updateCountDownCharacter);
        $('#review-content').keyup(ynsocialstore.updateCountDownCharacter);
        $(document).on('mouseenter', sect, function () {
            var ele = $(this);
            $(sect).removeClass('hover').removeClass('yn-rating');
            ele.prevAll().addClass('yn-rating');
            ele.addClass('yn-rating');
            ele.prevAll().removeClass('yn-rating-disable');
            ele.removeClass('yn-rating-disable');

        }).on('mouseout', sect, function () {
            var ele = $(this),
                lastval = parseInt(rate.val());
            $(sect).removeClass('yn-rating').addClass('yn-rating-disable');
            if (lastval > 0) {
                var i = 0;
                $('#ynstore-rating-section').find('.yn-star').each(function () {
                    if (i < lastval) {
                        i++;
                        $(this).addClass('hover');
                    }
                });
            }
        });
        if ($('#form-rating').length != 0) {
            ynsocialstore.initValidator($('#form-rating'));
            $('#form-rating #review-content').rules('add', {
                maxlength: 500
            });
        }
        star.bind('click', function () {
            rate.val($(this).data('value'));
            $('#ynstore-rating-section').find('.yn-rating').each(function () {
                $(this).addClass('hover');
            });
            ;
        });
    },
    showReviewForm: function () {
        $('#ynstore-rating-result').addClass('hide');
        $('#ynstore-review-store-form').removeClass('hide');
    },
    updateCountDownCharacter: function () {
        if ($('#review-content').length == 0) return false;
        var remaining = 500 - $('#review-content').val().length;
        if (remaining < 0) {
            remaining = 0;
        }
        $('#ynstore-review-countdown').text(remaining);
    },
    editFAQ: function (faq_id, store_id) {
        tb_show(oTranslations['ynsocialstore.edit_faq'], $.ajaxBox('ynsocialstore.AddFaqStoreBlock', 'height=300&width=500&action=edit&faq_id=' + faq_id + '&store_id=' + store_id))
    },
    confirmDeleteFAQs: function (faq_id, store_id) {
        $Core.jsConfirm({}, function(){
            ynsocialstore.deleteFAQ(faq_id);
        });
    },
    deleteFAQ: function (faq_id) {
        $.ajaxCall('ynsocialstore.deleteFAQ', $.param({faq_id: faq_id}));
    },
    confirmChooseRenewPackage: function (is_different, store_id, package_id) {
        if (is_different) {
            sHtml = "";
            sHtml += '<div class="white-popup-block" id="warning_packages" style="width: 300px;">';
            sHtml += '<div class="extra_info">';
            sHtml += '<label>';
            sHtml += oTranslations['ynsocialstore.warning'];
            sHtml += '</label><br>';
            sHtml += oTranslations['ynsocialstore.warning_package'];
            sHtml += '</div>';
            sHtml += '<div style="margin-top: 10px; text-align: right;">';
            sHtml += '<button class="btn btn-primary btn-sm" onclick="ynsocialstore.processChoosePackage(' + store_id + ',' + package_id + ');">';
            sHtml += oTranslations['ynsocialstore.continue'];
            sHtml += '</button>';
            sHtml += '<button id="close_confirm_upgrade" class="btn btn-default btn-sm" style="margin-left: 10px;" onclick="$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">';
            sHtml += oTranslations['ynsocialstore.cancel'];
            sHtml += '</button>';
            sHtml += '</div>';
            sHtml += '</div>';

            $.magnificPopup.open({
                items: {
                    src: sHtml,
                    type: 'inline'
                }
            });
        } else {
            ynsocialstore.processChoosePackage(store_id, package_id);
        }
    },
    processChoosePackage: function (store_id, package_id) {
        if ($('#close_confirm_upgrade').length > 0) {
            $('#close_confirm_upgrade').trigger('click')
        }
        $.ajaxCall('ynsocialstore.upgradePackages', $.param({store_id: store_id, package_id: package_id}));
    },
    addToCompare: function (item_id, sType) {
        var cookiename = (sType == 'store') ? ynsocialstore.cookieCompareStoreName : ynsocialstore.cookieCompareProductName,
            data = getCookie(cookiename),
            aData = [],
            idx = 0;

        if (null === data) {
            data = '';
        } else {
            aData = data.split(",");
        }
        var $body = $('body');
        var $ele = (sType == 'store') ? $body.find('[data-comparestoreid="' + item_id + '"]') : $body.find('[data-compareproductid="' + item_id + '"]');
        if ($ele.hasClass('ynstore-active')) {
            for (idx = 0; idx < aData.length; idx++) {
                if (item_id == aData[idx]) {
                    aData.splice(idx, 1);
                    break;
                }
            }
            deleteCookie(cookiename);
            setCookie(cookiename, aData.join(), 1);
            $ele.removeClass('ynstore-active');
        }
        else {
            data += ',' + item_id;
            data = ynsocialstorehelper.trim(data, ',');
            setCookie(cookiename, data, 1);
            $ele.addClass('ynstore-active');
        }
        ynsocialstore.updateCompareDashboard();
    },
    updateCompareDashboard: function () {
        $.ajaxCall('ynsocialstore.updateCompareBar', '', 'POST');
        return true;
    },
    initCompareStore: function () {
        var ele = $('#ynstore-compare-dashboard'),
            cookie = getCookie(ynsocialstore.cookieCompareStoreName),
            aData = [];
        if (ele.length == 0)
            return false;
        if (null === cookie) {
            return false;
        } else {
            aData = cookie.split(",");
        }
        for (var i = 0; i < aData.length; i++) {
            $('body').find('[data-comparestoreid="' + aData[i] + '"]').addClass('ynstore-active');
        }
    },
    switchCompareTab: function (ele) {
        var store = $('#ynstore-compare-store-section'),
            product = $('#ynstore-compare-product-section'),
            data = $(ele).data('typecompare'),
            comparetab = 'ynsocialstore_compare_tab';
        $(ele).closest('ul').find('li').removeClass('ynstore-active');
        $(ele).closest('li').addClass('ynstore-active');
        if (data == 'store') {
            store.removeClass('hide');
            product.addClass('hide');
            setCookie(comparetab, 'store', 1);
        }
        else {
            product.removeClass('hide');
            store.addClass('hide');
            setCookie(comparetab, 'product', 1);
        }
        return false;
    },
    removeStoreFromCompare: function (ele, store_id) {
        var cookiename = ynsocialstore.cookieCompareStoreName,
            data = getCookie(cookiename),
            aData = [],
            idx = 0;
        if (null === data) {
            data = '';
        } else {
            aData = data.split(",");
        }
        var $body = $('body');
        var $ele = $body.find('[data-comparestoreid="' + store_id + '"]');
        for (idx = 0; idx < aData.length; idx++) {
            if (store_id == aData[idx]) {
                aData.splice(idx, 1);
                break;
            }
        }
        deleteCookie(cookiename);
        setCookie(cookiename, aData.join(), 1);
        $ele.removeClass('ynstore-active');
        $body.find('#ynstore_compare_page_item_' + store_id).remove();
        ynsocialstore.updateCompareDashboard();
        return false;
    },
    toggleCompareDasBoard: function (ele) {
        var data = $(ele).attr('data-type'),
            holder = $('#ynstore-compare-item-list'),
            comparebox = 'ynsocialstore_compare_store_box';

        if (data === 'show') {
            $(ele).attr('data-type', 'hide');
            setCookie(comparebox, 'max', 1);
            if($('#ynstore_my_cart_btn').length && !$('#ynstore_my_cart_btn').hasClass('ynstore-hide'))
            {
                $('#ynstore_my_cart_btn').trigger('click');
            }
            holder.removeClass('ynstore-hide');
            $(ele).removeClass('ynstore-hide');
        }
        else {
            $(ele).attr('data-type', 'show');
            setCookie(comparebox, 'min', 1);
            holder.addClass('ynstore-hide');
            $(ele).addClass('ynstore-hide');
        }
    },
    removeAllStoreFromCompare: function () {
        setCookie(ynsocialstore.cookieCompareStoreName, '', 1);
        setCookie('ynsocialstore_compare_store_box', 'min', 1);
        $('.ynstore-compare-content').html(oTranslations['ynsocialstore.no_stores_to_compare']);
        $('.ynstore-check-compare').removeClass('ynstore-active');
        $('.ynstore-compare-btn').removeClass('ynstore-active');
        ynsocialstore.updateCompareDashboard();
    },
    validReviewForm: function () {
        if ($('#ynstore-current-rating').val() == 0) {
            $('#ynstore-rating-section .text-danger').html(oTranslations['ynsocialstore.you_cannot_post_a_review_without_rating'])
            return false;
        }
        return true;
    },
    changeReNewBefore: function (ele, iStoreId) {
        if ($(ele).val() < 0) {
            return false;
        }
        else {
            $.ajaxCall('ynsocialstore.changeReNewBefore', $.param({number_of_days: $(ele).val(), store_id: iStoreId}))
        }
    },
    initAddProduct: function () {
        if ($('#ynsocialstore_add_product_form').length == 0) {
            return false;
        }
        ynsocialstore.initValidator($('#ynsocialstore_add_product_form'));

        //Change product type
        $('#ynsocialstore_add_product_form #product_type').on('change', function () {
            if ($('#product_type').val() == 'physical') {
                $('#ynstore_product_inventory').closest('.ynstore-product-add-block').removeClass('hide');
                $('#ynstore_product_link').closest('.ynstore-product-add-block').addClass('hide');
                $('#ynstore_product_uom').removeClass('hide');
            }
            else {
                $('#ynstore_product_inventory').closest('.ynstore-product-add-block').addClass('hide');
                $('#ynstore_product_link').closest('.ynstore-product-add-block').removeClass('hide');
                $('#ynstore_product_uom').addClass('hide');
            }
        });

        //Change store name
        $('#ynsocialstore_add_product_form #store_name').on('change', function () {
            ynsocialstore.changeStoreInAddProduct();
        });
        ynsocialstore.changeStoreInAddProduct();
        //Change price
        $('#ynsocialstore_add_product_form #product_price').on('keyup', function (ele) {
            ynsocialstore.changeSellingPrice(ele);
        });
        $('#ynsocialstore_add_product_form #ynstore_product_discount_value').on('keyup', function (ele) {
            ynsocialstore.changeSellingPrice(ele);
        });
        $('#ynsocialstore_add_product_form #ynstore_discount_type').on('change', function (ele) {
            ynsocialstore.changeSellingPrice(ele);
        });
        $('.js_item_active').on('click', function () {
            if ($(this).closest('.item_is_active_holder.ynstore_inventory_enable').hasClass('item_selection_not_active') === false) {
                $('#ynstore_product_inventory_detail').removeClass('hide');
            }
            else {
                $('#ynstore_product_inventory_detail').addClass('hide');
            }
        });


        $('#ynsocialstore_feature_number_days').on('keyup', ynsocialstore.onChangeFeatureFeeTotal);

        jQuery.validator.addMethod('checkCategory', function () {
            var result = false;
            if ($('#ynsocialstore_add_product_form #ynstore_categorylist .js_ynstore_add_categories:first #js_mp_id_0').val() != '') {
                result = true;
            }
            return result;
        }, oTranslations['this_field_is_required']);
        jQuery.validator.addMethod('checkCategory', function () {
            var result = false;
            if ($('#ynsocialstore_add_product_form #ynstore_categorylist .js_ynstore_add_categories:first #js_mp_id_0').val() != '') {
                result = true;
            }
            return result;
        }, oTranslations['this_field_is_required']);
        jQuery.validator.addMethod('checkCustomFieldText', function (value, element, params) {
            var result = false;
            if (element.value.length > 0) {
                result = true;
            }
            return result;
        }, oTranslations['this_field_is_required']);
        jQuery.validator.addMethod('checkCustomFieldTextarea', function (value, element, params) {
            var result = false;
            if ($(element).val().length > 0) {
                result = true;
            }
            return result;
        }, oTranslations['this_field_is_required']);
        jQuery.validator.addMethod('checkCustomFieldSelect', function (value, element, params) {
            var result = false;
            if ($(element).val().length > 0) {
                result = true;
            }
            return result;
        }, oTranslations['this_field_is_required']);
        jQuery.validator.addMethod('checkCustomFieldMultiselect', function (value, element, params) {
            var result = false;
            var select = $(element).val();
            if (undefined != select && null != select && select.length > 0) {
                result = true;
            }
            return result;
        }, oTranslations['this_field_is_required']);
        jQuery.validator.addMethod('checkCustomFieldCheckbox', function (value, element, params) {
            var result = false;
            var name = element.name;
            if ($('input[name="' + name + '"]:checkbox').is(':checked')) {
                result = true;
            }
            return result;
        }, oTranslations['this_field_is_required']);
        jQuery.validator.addMethod('checkCustomFieldRadio', function (value, element, params) {
            var result = false;
            var name = element.name;
            if ($('input[name="' + name + '"]:radio').is(':checked')) {
                result = true;
            }
            return result;
        }, oTranslations['this_field_is_required']);

        $('#ynsocialstore_add_product_form #name').rules('add', {
            required: true
        });
        if ($('#ynsocialstore_add_product_form #store_name').length != 0) {
            $('#ynsocialstore_add_product_form #store_name').rules('add', {
                required: true
            });
        }
        $('#ynsocialstore_add_product_form #description').rules('add', {
            required: true
        });

        $('#ynsocialstore_add_product_form #link_download').rules('add', {
            url: true
        });

        $('#ynsocialstore_add_product_form #product_price').rules('add', {
            required: true,
            number: true,
            min: 0,
            max: 9999999999.99
        });
        $('#ynsocialstore_add_product_form #ynstore_product_discount_value').rules('add', {
            number: true,
            min: 0,
        });
        $('#ynsocialstore_add_product_form #min_order').rules('add', {
            number: true,
            min: 0,
            max: 9999999999
        });

        $('#ynsocialstore_add_product_form #max_order').rules('add', {
            number: true,
            min: 0,
            max: 9999999999
        });

        if($('#ynstore_total_quantity').length != 0 && $('#ynstore_total_quantity').val() != ""){
            var total = $('#ynstore_total_quantity').val(),
                link = $('#ynstore_manage_attr_link').val();
            if(total == "unlimited"){
                jQuery.validator.addMethod('checkStockQuantity', function () {
                    var result = true;
                    if ($('#ynsocialstore_add_product_form #product_quantity_main').val() != 0 && $('#ynsocialstore_add_product_form #product_quantity_main').val() != "") {
                        result = false;
                    }
                    return result;
                }, oTranslations['ynsocialstore.warning_change_stock_quantity_have_unlimited_attribute'].replace('{link}',link));
            }
            else{
                jQuery.validator.addMethod('checkStockQuantity', function () {
                    var result = true,
                        total = parseInt($('#ynstore_total_quantity').val());
                    if ($('#ynsocialstore_add_product_form #product_quantity_main').val() != 0 && parseInt($('#ynsocialstore_add_product_form #product_quantity_main').val()) < total) {
                        result = false;
                    }
                    return result;
                }, oTranslations['ynsocialstore.warning_when_edit_stock_quantity_of_product'].replace('{total}', total).replace('{link}',link));
            }
            $('#ynsocialstore_add_product_form #product_quantity_main').rules('add', {
                number: true,
                checkStockQuantity: true,
            });
        }
        else{
            $('#ynsocialstore_add_product_form #product_quantity_main').rules('add', {
                number: true,
                min: 0,
                max: 9999999999
            });
        }

        $('#ynsocialstore_add_product_form #ynstore_categorylist .js_ynstore_add_categories:first #js_mp_id_0').rules('add', {
            checkCategory: true
        });
        $('.js_mp_category_list').change(function () {
            var $this = $(this);
            var iParentId = parseInt(this.id.replace('js_mp_id_', ''));
            iCatId = $this.val();
            if (!iCatId) {
                iCatId = parseInt($this.parent().attr("id").replace('js_mp_holder_', ""));
            }


            $('.js_mp_category_list').each(function () {
                if (parseInt(this.id.replace('js_mp_id_', '')) > iParentId) {
                    $('#js_mp_holder_' + this.id.replace('js_mp_id_', '')).hide();

                    this.value = '';
                }
            });
            var $parent = $(".js_ynstore_add_categories > .js_mp_parent_holder").find('.js_mp_category_list');
            $('#js_mp_holder_' + $(this).val()).show();
            ynsocialstore.changeCustomFieldByCategory($parent.val());
        });

    },
    changeCustomFieldByCategory: function (iCategoryId) {
        iProductId = $('#ynstore_product_id').val();

        $Core.ajax('ynsocialstore.changeCustomFieldByCategory',
            {
                type: 'POST',
                params: {
                    action: 'changeCustomFieldByCategory'
                    , iCategoryId: iCategoryId
                    , iProductId: iProductId
                },
                success: function (sOutput) {
                    var oOutput = $.parseJSON(sOutput);
                    if (oOutput.status == 'SUCCESS') {
                        if (oOutput.content.trim() != '') {
                            $('#ynstore_customfield_category').html('<h3>' + oTranslations['ynsocialstore.additional_information'] + '</h3><div class="form-group">' + oOutput.content + '</div>');
                            $('#ynstore_customfield_category').addClass('ynstore-product-add-block');
                        }
                        else {
                            $('#ynstore_customfield_category').removeClass('ynstore-product-add-block');
                            $('#ynstore_customfield_category').html('<div class="form-group">' + oOutput.content + '</div>');
                        }

                        // add validate each custom field
                        $('#ynstore_customfield_category').find('[data-isrequired="1"]').each(function () {
                            var type = $(this).data('type');
                            switch (type) {
                                case 'text':
                                    $(this).rules('add', {
                                        checkCustomFieldText: true
                                    });
                                    break;
                                case 'textarea':
                                    $(this).rules('add', {
                                        checkCustomFieldTextarea: true
                                    });
                                    break;
                                case 'select':
                                    $(this).rules('add', {
                                        checkCustomFieldSelect: true
                                    });
                                    break;
                                case 'multiselect':
                                    $(this).rules('add', {
                                        checkCustomFieldMultiselect: true
                                    });
                                    break;
                                case 'radio':
                                    $(this).rules('add', {
                                        checkCustomFieldRadio: true
                                    });
                                    break;
                                case 'checkbox':
                                    $(this).rules('add', {
                                        checkCustomFieldCheckbox: true
                                    });
                                    break;
                            }
                        });

                    } else {
                    }
                }
            });
    },
    changeStoreInAddProduct: function () {
        var store_id = $("#ynsocialstore_add_product_form #store_name").val(),
            currency_id = $('#ynstore_currency_id').html();

        if(parseInt(store_id) > 0) {
            $('#ynsocialstore_add_product_form #ynstore_store_id').val(store_id);
        }
        else {
            store_id = $('#ynsocialstore_add_product_form #ynstore_store_id').val();
        }

        $Core.ajax('ynsocialstore.changeStoreInAddProduct',
            {
                type: "POST",
                params: {
                    iStoreId: store_id,
                    iCurrencyId: currency_id
                },
                success: function (sOutput) {
                    var oOutput = $.parseJSON(sOutput);
                    if (oOutput.status == 'SUCCESS') {
                        $('#ynstore_add_product_feature_fee').html(oOutput.content);
                        $('#ynsocialstore_defaultfeaturefee').val(oOutput.fee);
                        ynsocialstore.onChangeFeatureFeeTotal();
                    }
                }
            }
        )
    },
    checkNoDefineDiscountPeriod: function(ele){
        if(ele.is(':checked')) {
            $('#ynstore_discount_time').addClass('hide');
        }
        else
            $('#ynstore_discount_time').removeClass('hide');
    },
    confirmDeleteReview: function (review_id, store_id) {
        $Core.jsConfirm({}, function(){
            ynsocialstore.deleteReview(review_id, store_id);
        });
    },
    deleteReview: function (iReviewId, iStoreId) {
        $.ajaxCall('ynsocialstore.deleteReview', $.param({iReviewId: iReviewId, iStoreId: iStoreId}));
        return false;
    },
    updateWishList: function (iProductId, iType, iNotDetail) {
        $.ajaxCall('ynsocialstore.updateWishlist', $.param({
            iProductId: iProductId,
            bWishlist: iType,
            iNotDetail: iNotDetail
        }), false, 'POST', ynsocialstore.checkNothingToShow());
        return false;
    },
    featureProductInBox: function (ele, product_id) {
        tb_show(oTranslations['ynsocialstore.social_store'], $.ajaxBox('ynsocialstore.featureProductInBox', 'height=300&width=420&iProductId=' + product_id));
        return false;
    },
    updateFeatureProduct: function (ele, product_id, iType) {
        $.ajaxCall('ynsocialstore.featureProduct', $.param({
            iProductId: product_id,
            bIsFeatured: iType,
            onAdmin: false
        }), 'POST');
        return false;
    },
    closeProduct: function (product_id, owner_id, status, sType) {
        $.ajaxCall('ynsocialstore.closeProduct', $.param({
            iProductId: product_id,
            iOwnerId: owner_id,
            sStatus: status,
            onAdmin: false,
            sType: sType
        }));
        return false;

    },
    openProduct: function (product_id, owner_id, status, sType) {
        $.ajaxCall('ynsocialstore.reopenProduct', $.param({
            iProductId: product_id,
            iOwnerId: owner_id,
            sStatus: status,
            onAdmin: false,
            sType: sType
        }));
        return false;
    },
    approveProduct: function (product_id, status) {
        $.ajaxCall('ynsocialstore.approveProduct', $.param({iProductId: product_id, sStatus: status}));
        return false;
    },
    denyProduct: function (product_id, status) {
        $.ajaxCall('ynsocialstore.denyProduct', $.param({iProductId: product_id, sStatus: status}));
        return fa
    },
    confirmDeleteProduct: function (product_id, owner_id, is_detail) {
        $Core.jsConfirm({message: oTranslations['ynsocialstore.are_you_sure_want_to_delete_this_product_this_action_cannot_be_reverted']}, function(){
            ynsocialstore.deleteProduct(product_id, owner_id, is_detail);
        });
    },
    deleteProduct: function (product_id, owner_id, is_detail) {
        var is_detail = (typeof is_detail !== 'undefined') ? is_detail : 0;
        $.ajaxCall('ynsocialstore.deleteProduct', $.param({
            iProductId: product_id,
            iOwnerId: owner_id,
            bIsDetail: is_detail
        }));
    },
    confirmDeleteReviewProduct: function (review_id, product_id) {
        $Core.jsConfirm({}, function(){
            ynsocialstore.deleteReviewProduct(review_id, product_id);
        });
    },
    deleteReviewProduct: function (iReviewId, iProductId) {
        $.ajaxCall('ynsocialstore.deleteReviewProduct', $.param({iReviewId: iReviewId, iProductId: iProductId}));
        return false;
    },
    confirmDeleteElementAttr: function (element_id, product_id) {
        $Core.jsConfirm({}, function(){
            ynsocialstore.deleteElementAttr(element_id, product_id);
        });
    },
    deleteElementAttr: function (element_id, product_id) {
        $.ajaxCall('ynsocialstore.deleteElementAttr', $.param({iElementId: element_id, iProductId: product_id}));
        return false;
    },
    checkAllProducts: function () {
        var checked = document.getElementById('js_check_box_all').checked;
        $('.js_row_checkbox').each(function (index, element) {
            element.checked = checked;
            var sIdName = '#js_ynstore_product_' + element.value;
            if (element.checked == true) {
                $(sIdName).css({
                    'backgroundColor': '#FFFF88'
                });
            }
            else {
                if (element.value % 2 == 0) {
                    $(sIdName).css({
                        'backgroundColor': '#F0f0f0'
                    });
                }
                else {
                    $(sIdName).css({
                        'backgroundColor': '#F9F9F9'
                    });
                }
            }
        });
        ynsocialstore.setButtonStatus(checked);
        return checked;
    },
    setButtonStatus: function (status) {
        if (status) {
            $('#delete_selected').removeClass('disabled');
            $('#delete_selected').attr('disabled', false);
        }
        else {
            $('#delete_selected').addClass('disabled');
            $('#delete_selected').attr('disabled', true);
        }
    },
    confirmDeleteProducts: function (sElementId) {
        var iCnt = 0;
        $("input:checkbox").each(function () {
            if (this.checked) {
                iCnt++;
            }
        });
        if (iCnt == 0) {
            return;
        }
        let message = (iCnt == 1 ? oTranslations['ynsocialstore.are_you_sure_want_to_delete_this_product_this_action_cannot_be_reverted'] : oTranslations['ynsocialstore.are_you_sure_want_to_delete_these_products_this_action_cannot_be_reverted']);
        let element = $('#' + sElementId);
        $Core.jsConfirm({message: message}, function(){
            element.submit();
        });
    },
    checkDisableStatus: function () {
        var status = false;
        $('.js_row_checkbox').each(function (index, element) {
            var sIdName = '#js_ynstore_product_' + element.value;
            if (element.checked == true) {
                status = true;
                $(sIdName).css({
                    'backgroundColor': '#FFFF88'
                });
            }
            else {
                if (element.value % 2 == 0) {
                    $(sIdName).css({
                        'backgroundColor': '#F0f0f0'
                    });
                }
                else {
                    $(sIdName).css({
                        'backgroundColor': '#F9F9F9'
                    });
                }
            }
        });
        ynsocialstore.setButtonStatus(status);
        return status;
    },
    initCompareProduct: function () {
        var ele = $('#ynstore-compare-dashboard'),
            cookie = getCookie(ynsocialstore.cookieCompareProductName),
            aData = [];
        if (ele.length == 0)
            return false;
        if (null === cookie) {
            return false;
        } else {
            aData = cookie.split(",");
        }
        for (var i = 0; i < aData.length; i++) {
            $('body').find('[data-compareproductid="' + aData[i] + '"]').addClass('ynstore-active');
        }
    },
    removeProductFromCompare: function (ele, product_id) {
        var cookiename = ynsocialstore.cookieCompareProductName,
            data = getCookie(cookiename),
            aData = [],
            idx = 0;
        if (null === data) {
            data = '';
        } else {
            aData = data.split(",");
        }
        var $body = $('body');
        var $ele = $body.find('[data-compareproductid="' + product_id + '"]');
        for (idx = 0; idx < aData.length; idx++) {
            if (product_id == aData[idx]) {
                aData.splice(idx, 1);
                break;
            }
        }
        deleteCookie(cookiename);
        setCookie(cookiename, aData.join(), 1);
        $ele.removeClass('ynstore-active');
        $body.find('#ynstore_compare_page_item_' + product_id).remove();
        if ($('#ynstore_category_id').length != 0) {
            var cateId = $('#ynstore_category_id').val();
            $.ajaxCall('ynsocialstore.updateCategoryInCompare', $.param({categoryId: cateId}));
        }
        ynsocialstore.updateCompareDashboard();
        return false;
    },
    removeCategoryListFromCompare: function (categoryId) {
        var child = $('#ynstore-category-product-tab-' + categoryId);
        $('#ynstore-ptab-' + categoryId).remove();
        child.find('.js_ynstore_list_product_compare').each(function () {
            ynsocialstore.removeProductFromCompare(this, $(this).data('productid'), true);

        })
        child.remove();
        $('#ynstore-confirm-detele-category').addClass('hide');
        return false;
    },
    showDeleteCategoryCompareConfirm: function (ele) {

        $('#js_ynstore_check_delete_product').on('click', function () {
            return ynsocialstore.removeCategoryListFromCompare($(ele).data('categoryid'));
        })
        $('#ynstore-confirm-detele-category').removeClass('hide');
    },
    /**
     *    Form id
     */
    initColorPicker: function (form_id) {
        // Colorpicker
        $(form_id + ' ._colorpicker:not(.c_built)').each(function () {
            var t = $(this),
                h = t.parent().find('._colorpicker_holder');

            t.addClass('c_built');
            h.css('background-color', t.val());

            h.colpick({
                layout: 'hex',
                submit: false,
                onChange: function(hsb,hex,rgb,el,bySetColor) {
                    t.val('#' + hex);
                    h.css('background-color', '#' + hex);
                    t.trigger('change');
                },
                onHide: function() {
                    t.trigger('change');
                }
            });

            var cal_of_h = $('div.colpick#' + h.data('colpickId'));
            if (cal_of_h.hasClass('dont-unbind-children') === false) {
                cal_of_h.addClass('dont-unbind-children');
            }
        });
    },
    initAddAttributeElement: function (limit) {
        ynsocialstore.initValidator($('#js_add_element_page'));
        $('#js_add_element_page #price').rules('add', {
            required: true,
            number: true,
            min: 0,
            max: 9999999999.99
        });

        if (limit > 0) {
            $('#js_add_element_page #amount').rules('add', {
                number: true,
                min: 1,
                digit: true,
                max: limit,
                messages: {
                    min: oTranslations['ynsocialstore.please_enter_a_value_greater_than_or_equal_to_0'],
                    max: oTranslations['ynsocialstore.please_enter_a_value_less_than_or_equal_to_0_total_quantity_of_elements_can_not_exceed_available_in_stock']
                }
            });
        }
        else {
            $('#quantity').change(function () {
                var selectedValue = $('#quantity option:selected').val();
                if (selectedValue == '0') {
                    $("#amount").prop("disabled", true);
                    $("#amount").prop("required", false);
                    $("#amount").val('0');
                } else {
                    $("#amount").prop("disabled", false);
                    $("#amount").prop("required", true);
                }
            });

            $('#js_add_element_page #amount').rules('add', {
                number: true,
                min: 1,
                digit: true,
                max: 999999999,
                messages: {
                    min: oTranslations['ynsocialstore.please_enter_a_value_greater_than_or_equal_to_0'],
                    max: oTranslations['ynsocialstore.please_enter_a_value_less_than_or_equal_to_0_total_quantity_of_elements_can_not_exceed_available_in_stock']
                }
            });
        }

        ynsocialstore.initColorPicker('#js_add_element_page');
    },
    initDetailProduct: function () {
        var qtt = $('#current_quantity'),
            price_ele = $('#product_total_price'),
            inventory = $('#enable_inventory').val();
        qtt.on('change', function () {
            if (inventory == 1) {
                if (parseInt($('#max_order_by_attribute').val()) == 0) {
                    qtt.val(0);
                }
                else if (parseInt($('#max_order_by_attribute').val()) > 0 && parseInt(qtt.val()) > parseInt($('#max_order_by_attribute').val())) {
                    qtt.val(parseInt($('#max_order_by_attribute').val()));
                }
                else if (isNaN(parseInt(qtt.val())) || parseInt(qtt.val()) < 0) {
                    qtt.val(1);
                }
            }
            else {
                if (parseInt(qtt.val()) < 1 || isNaN(parseInt(qtt.val()))) {
                    qtt.val(1);
                }
            }
            price_ele.html(parseFloat(qtt.val()).toFixed(2) * parseFloat($('#current_price').val()).toFixed(2));
        });
        if ($('#js_ynstore_product_checkout-quantity').length != 0) {
            $('#quantity_minus').on('click', function () {
                var cur_quantity = parseInt(qtt.val());
                if (cur_quantity == 1 || cur_quantity == 0) return;
                if (!isNaN(cur_quantity)) {
                    qtt.val(cur_quantity - 1);
                }
                price_ele.html(parseInt(qtt.val()) * parseFloat($('#current_price').val()));
            });
            $('#quantity_add').on('click', function () {
                var cur_quantity = parseInt(qtt.val());
                if (cur_quantity == parseInt($('#max_order_by_attribute').val()) && $('#max_order_by_attribute').val() != 'unlimited') return;
                if (!isNaN(cur_quantity)) {
                    qtt.val(cur_quantity + 1);
                }
                price_ele.html(parseInt(qtt.val()) * parseFloat($('#current_price').val()));
            });
        }
    },
    changeNumberOfFeatureDays: function (ele) {
        if ($('#ynstore_minus_day').length != 0) {
            var fpd = $('#ynsocialstore_feature_number_days');
            if ($(ele).data('type') == 'minus') {
                event.preventDefault();
                var cur_day = parseInt(fpd.val());
                if (cur_day == 0) return;
                if (!isNaN(cur_day)) {
                    fpd.val(cur_day - 1);
                }
                ynsocialstore.onChangeFeatureFeeTotal();
            }
            else if ($(ele).data('type') == 'add') {
                event.preventDefault();
                var cur_day = parseInt(fpd.val());
                if (!isNaN(cur_day)) {
                    fpd.val(cur_day + 1);
                }
                ynsocialstore.onChangeFeatureFeeTotal();
            }
            if (fpd.val() == 1) {
                $('#ynsocialstore_number_unit').html(oTranslations['ynsocialstore.l_day'])
            }
            else {
                $('#ynsocialstore_number_unit').html(oTranslations['ynsocialstore.l_day_s'])
            }
            return false;
        }
    },
    selectAttributeInDetail: function (ele) {
        $('#ynstore_product_detail_attribute').find('.js_selected_attribute.ynstore-active').removeClass('js_selected_attribute').removeClass('ynstore-active');
        $(ele).closest('div').addClass('js_selected_attribute ynstore-active');
        $('#current_quantity').val(1);
        var elem = $(ele),
            maxele = $('#max_order_by_attribute'),
            elementid = elem.data('elementid'),
            remain = elem.data('remain'),
            realremain = elem.data('realremain'),
            price = elem.data('price'),
            main_price = parseFloat($('#js_product_main_price').html()).toFixed(2),
            quantity = elem.data('quantity'),
            enable_inventory = $('#enable_inventory').val(),
            max_quantity_can_add = $('#max_quantity_can_add').val(),
            max_order = $('#max_order').val(),
            attribute_price = elem.data('price'),
            product_total_price = $('#product_total_price');
        $('#product_total_price').html(attribute_price);
        $('#current_price').val(price);
        if (enable_inventory == 0) {
            maxele.val('unlimited');
        }
        else {
            $('#js_product_discount_price').html(price);
            var percent = parseInt((main_price - parseFloat(price).toFixed(2)) * 100 / main_price);
            if (percent > 100 || percent < 0) {
                $('#js_product_discount_percentage').html('');
            }
            else {
                $('#js_product_discount_percentage').html('<span class="ynstore-discount">' + percent + '%<b>' + oTranslations['ynsocialstore.off']+'</b></span>');
            }
            if (quantity == 0) {
                $('#js_product_remain_quantity').html(oTranslations['ynsocialstore.unlimited']);
            }
            else {
                $('#js_product_remain_quantity').html('<b class="ynstore-green">' + oTranslations['ynsocialstore.available_in_stock'] + '</b> . ' + oTranslations['ynsocialstore.remain'] + ' ' + realremain + '/' + quantity);
            }
            if (max_quantity_can_add != "unlimited" && ((max_quantity_can_add < remain) || quantity == 0)) {
                maxele.val(max_quantity_can_add);
            }
            else if (max_quantity_can_add == 'unlimited' && quantity == 0) {
                maxele.val('unlimited');
            }
            else if (quantity != 0 && (max_quantity_can_add >= remain || max_quantity_can_add == "unlimited")) {
                maxele.val(remain);
            }
            if (maxele.val() == 0) {
                $('#product_total_price').html(0);
                $('#current_quantity').val(0);
            }
        }
        return false;
    },
    addToCart: function (ele, product_id, product_type, is_entry,type) {
        if (is_entry == true) {
            $.ajaxCall('ynsocialstore.addToCartFromEntry', $.param({
                iProductId: product_id,
                sProductType: product_type
            }));
            return false;
        }
        else {
            if(typeof type == 'undefined'){
                type = 'addcart';
            }
            if (product_type == 'digital') {
                var price = parseFloat($('#js_product_discount_price').html()).toFixed(2);
                $.ajaxCall('ynsocialstore.addToCartDigital', $.param({iProductId: product_id, price: price,type:type}));
                return false;
            }
            else {
                var total_price = parseFloat($('#product_total_price').html()).toFixed(2),
                    total_quantity = parseInt($('#current_quantity').val()),
                    current_max_of_attribute = parseInt($('#max_order_by_attribute').val()),
                    current_max_of_product = $('#max_quantity_can_add').val();
                if ($('#ynstore_product_detail_attribute').length != 0) {
                    var selected = $('#ynstore_product_detail_attribute').find('.js_selected_attribute > a'),
                        attribute_id = selected.data('elementid'),
                        attribute_remain = selected.data('remain');

                    $.ajaxCall('ynsocialstore.addToCartPhysical', $.param({
                        iAttribute: 1,
                        iProductId: product_id,
                        iTotalPrice: total_price,
                        iQuantity: total_quantity,
                        iCurrentAttributeLimit: current_max_of_attribute,
                        iCurrentProductLimit: current_max_of_product,
                        iAttributeId: attribute_id,
                        iAttributeRemain: attribute_remain,
                        type:type
                    }));
                    return false;
                }
                else {
                    $.ajaxCall('ynsocialstore.addToCartPhysical', $.param({
                        iAttribute: 0,
                        iProductId: product_id,
                        iTotalPrice: total_price,
                        iQuantity: total_quantity,
                        iCurrentAttributeLimit: current_max_of_attribute,
                        iCurrentProductLimit: current_max_of_product,
                        type:type
                    }));
                    return false;
                }
            }

        }
    },
    changeSellingPrice: function (ele) {
        var price = parseFloat($('#ynsocialstore_add_product_form #product_price').val()).toFixed(2),
            discount_ele = $('#ynsocialstore_add_product_form #ynstore_product_discount_value'),
            selling_ele = $('#ynsocialstore_add_product_form #selling_price'),
            discount = parseFloat(discount_ele.val()).toFixed(2),
            type = $('#ynsocialstore_add_product_form #ynstore_discount_type').val();
        if (isNaN((price)) || price < 0) {
            selling_ele.val('0');
            return false;
        }
        if (type.trim() == "amount") {
            if (!isNaN(discount)) {
                selling_ele.val(price - discount);
            }
            else {
                selling_ele.val(price);
            }
        }
        else if (type.trim() == "percentage") {
            if (!isNaN(discount)) {
                selling_ele.val((price - (price * discount / 100)).toFixed(2));
            }
            else {
                selling_ele.val(price);
            }
        }
    },
    checkNothingToShow: function () {
        if ($('#js_block_border_ynsocialstore_product_mywishlistproduct').length > 0) {
            if ($('#js_block_border_ynsocialstore_product_mywishlistproduct .ynstore-items li').length == 0)
                window.location.reload();
        }
    },
    initMyCartUpdateQuantity: function () {
        if($('#ynstore_cartid').length == 0) return false;
        if($('#ynstore_checkout_form .ynstore_contact_item').length){
            $('#ynstore_checkout_form .ynstore_contact_item').first().addClass('selected');
            $('#ynstore_checkout_form .ynstore_contact_list input:radio[name="val[selected_address]"]').bind('click',function(){
                $('#ynstore_checkout_form .ynstore_contact_item').removeClass('selected');
                $(this).parent().parent().parent().addClass('selected');
            });
        }
        ynsocialstore.myCartChanged.usercartid = $('#ynstore_cartid').val();
        var total_money = 0;
        var sellerMoney = {};
        $('.mycart_quantity_product').each(function (idx) {
            attrid = $(this).parent().find('.mycart_attributeid').val();
            maxquan = attrid != 0 ? ynsocialstore.myCartLimit.realLimit[attrid] : parseInt($(this).parent().find('.mycart_maxquantity').val());
            if(parseInt($(this).val()) > maxquan)
            {
                $(this).val(maxquan);
                $(this).parent().find('.mycart_max_quantity_noti').text(oTranslations['ynsocialstore.you_can_add_maximum_quantity_item_s_for_this_product'].replace('{quantity}',maxquan)).removeClass('hide');
            }
            total_money += parseFloat($(this).val()) * parseFloat($(this).parent().find('.mycart_price_product').val());

            /*update total money on seller*/
            total_seller_money = 0;
            seller_id = $(this).parent().find('.mycart_seller').val();
            $('.mycart_seller_' + seller_id).each(function (idx) {
                total_seller_money += parseFloat($(this).parent().find('.mycart_price_product').val()) * parseFloat($(this).parent().find('.mycart_quantity_product').val());
            });
            sellerMoney[seller_id] = {
                currency: $('#js_selected_currency_seller_'+ seller_id).val(),
                money: total_seller_money
            };

        });
        /*update total money*/
        let defaultCurrency = $('#js_selected_currency').val();
        $Core.ajax('ynsocialstore.formatMoney', {
            method: 'POST',
            params: {
                currency: defaultCurrency,
                money: total_money,
                seller_money: JSON.stringify(sellerMoney)
            },
            success: function(response){
                let output = $.parseJSON(response);
                if(output.status) {
                    $('#ynstore_mycart #mycart_total').text(output.formatted_money);
                    $('#ynstore_checkout #checkout_total').text(output.formatted_money);
                    if(!empty(output.formatted_seller_money)) {
                        for (let sellerKey in output.formatted_seller_money) {
                            let sellerMoney = output.formatted_seller_money[sellerKey];
                            $('#ynstore_mycart #mycart_total_seller_' + sellerKey).text(sellerMoney);
                            if($('#ynstore_checkout').length){
                                $('#ynstore_checkout #checkout_total_seller_' + sellerKey).text(sellerMoney);
                            }
                        }
                    }
                }
            }
        });

        $('.mycart_quantity_product').change(function () {
            attrid = $(this).parent().find('.mycart_attributeid').val();
            maxquan = attrid != 0 ? ynsocialstore.myCartLimit.realLimit[attrid] : parseInt($(this).parent().find('.mycart_maxquantity').val());
            if(maxquan != "unlimited" && parseInt($(this).val()) > parseInt(maxquan))
            {
                $(this).val(maxquan);
                $(this).parent().find('.mycart_max_quantity_noti').text(oTranslations['ynsocialstore.you_can_add_maximum_quantity_item_s_for_this_product'].replace('{quantity}',maxquan)).removeClass('hide');
            }
            if (parseInt($(this).val()) < 0 || $(this).val() == '' || isNaN(parseInt($(this).val()))) {
                $(this).val('1');
            }
            if($('#ynstore_checkout').length && (parseInt($(this).val()) <= 0 || $(this).val() == '' || isNaN(parseInt($(this).val()))))
            {
                $(this).val('1');
            }
            productID = $(this).parent().find('.mycart_productid').val();
            cartID = $(this).parent().find('.mycart_cartproductid').val();
            symbol = $(this).parent().data('symbol');
            let currency = $(this).closest('.ynstore_cart_item').data('currency');
            let productItem = {
                currency: currency,
                money: parseFloat($(this).val()) * parseFloat($(this).parent().find('.mycart_price_product').val())
            }

            /*update total money*/
            total_money = 0;
            $('.mycart_quantity_product').each(function (idx) {
                total_money += parseFloat($(this).val()) * parseFloat($(this).parent().find('.mycart_price_product').val());
            });

            total_seller_money = 0;
            let sellerMoneyChange = {};
            seller_id = $(this).parent().find('.mycart_seller').val();
            $('.mycart_seller_' + seller_id).each(function (idx) {
                total_seller_money += parseFloat($(this).parent().find('.mycart_price_product').val()) * parseFloat($(this).parent().find('.mycart_quantity_product').val());
            });
            sellerMoneyChange[seller_id] = {
                currency: $('#js_selected_currency_seller_'+ seller_id).val(),
                money: total_seller_money
            };

            let defaultCurrencyChange = $('#js_selected_currency').val();
            $Core.ajax('ynsocialstore.formatMoney', {
                method: 'POST',
                params: {
                    currency: defaultCurrencyChange,
                    money: total_money,
                    seller_money: JSON.stringify(sellerMoneyChange),
                    product_money: JSON.stringify(productItem)
                },
                success: function(response){
                    let output = $.parseJSON(response);
                    if(output.status) {
                        $('#ynstore_mycart #mycart_total').text(output.formatted_money);
                        $('#ynstore_checkout #checkout_total').text(output.formatted_money);
                        if(!empty(output.formatted_seller_money)) {
                            for (let sellerKey in output.formatted_seller_money) {
                                let sellerMoney = output.formatted_seller_money[sellerKey];
                                $('#ynstore_mycart #mycart_total_seller_' + sellerKey).text(sellerMoney);
                                if($('#ynstore_checkout').length){
                                    $('#ynstore_checkout #checkout_total_seller_' + sellerKey).text(sellerMoney);
                                }
                            }
                        }
                        if(!empty(output.formatted_product_money)) {
                            $('.item_price_' + cartID).text(output.formatted_product_money);
                        }
                    }
                }
            });
            ynsocialstore.updateCurrentMaximumOfElement(attrid,cartID);
        });
        $('.mycart_quantity_product').focusin(function(){
            attrid = $(this).parent().find('.mycart_attributeid').val();
            cartID = $(this).parent().find('.mycart_cartproductid').val();
            ynsocialstore.updateCurrentMaximumOfElement(attrid,cartID);
        });
    },
    changeAttributeInMyCart: function(ele)
    {
        var selected = $(ele).find('option:selected'),
            cartproduct = selected.data('cartid'),
            attr_id = selected.val(),
            price = selected.data('price'),
            hiddensection = $('#js_cart_product_id-' + cartproduct),
            current_quantity = hiddensection.find('.mycart_quantity_product').val(),
            max_of_product = hiddensection.find('.mycart_maxquantity').val(),
            ele_quantity = hiddensection.find('.mycart_quantity_product');
        ynsocialstore.updateCurrentMaximumOfElement(attr_id,cartproduct);
        hiddensection.find('.mycart_max_quantity_noti').text('').addClass('hide');
        var max_real = attr_id != 0 ? ynsocialstore.myCartLimit.realLimit[attr_id] : max_of_product;
        hiddensection.find('.mycart_price_product').val(price);
        hiddensection.find('.mycart_maxquantity').val(max_real);
        hiddensection.find('.mycart_attributeid').val(attr_id);
        if(max_real > current_quantity && !isNaN(parseInt(max_real)))
        {
            ele_quantity.val(current_quantity).change();
        }
        else if(max_real <= current_quantity && !isNaN(parseInt(max_real))){
            ele_quantity.val(max_real).change();
        }
        else if(max_real == "unlimited"){
            ele_quantity.val(1).change();
        }

    },
    updateQuantityInMyCart: function(ele){
        $(ele).parent().find('.mycart_quantity_product').focusin();
        attr_id = $(ele).parent().find('.mycart_attributeid').val();
        cart_id = $(this).parent().find('.mycart_cartproductid').val();
        var quantity = parseInt($(ele).parent().find('.mycart_quantity_product').val());
        if($(ele).data('type') == "minus"){
            if(quantity <= 0) return false;
            $(ele).parent().find('.mycart_quantity_product').val(quantity - 1).change();
        }
        else{
            if(quantity >= ynsocialstore.myCartLimit.realLimit[attr_id] && ynsocialstore.myCartLimit.realLimit[attr_id] != "unlimited") {
                $(ele).parent().find('.mycart_max_quantity_noti').text(oTranslations['ynsocialstore.you_can_add_maximum_quantity_item_s_for_this_product'].replace('{quantity}',ynsocialstore.myCartLimit.realLimit[attr_id])).removeClass('hide');
                return false;
            }
            $(ele).parent().find('.mycart_quantity_product').val(quantity + 1).change();
        }
        return false;
    },
    tempRemoveCart : function(ele,cartproduct_id,store_id){
        if($(ele).parents('.ynstore_card_body').find(".ynstore_cart_item").length == 1){
            $(ele).parents('.ynstore_cart_section').remove();
        }
        else {
            $('#js_cart_item_' + cartproduct_id).remove();
        }
        ynsocialstore.myCartChanged.deletedcart += ',' + cartproduct_id;
        ynsocialstore.myCartChanged.deletedcart = ynsocialstorehelper.trim(ynsocialstore.myCartChanged.deletedcart, ',');
        ynsocialstore.myCartChanged.tempdelete[store_id] = cartproduct_id;
        var total_money = 0;
        let sellerMoney = {};
        $('.mycart_quantity_product').each(function (idx) {
            total_money += parseFloat($(this).val()) * parseFloat($(this).parent().find('.mycart_price_product').val());

            /*update total money on seller*/
            total_seller_money = 0;
            seller_id = $(this).parent().find('.mycart_seller').val();
            $('.mycart_seller_' + seller_id).each(function (idx) {
                total_seller_money += parseFloat($(this).parent().find('.mycart_price_product').val()) * parseFloat($(this).parent().find('.mycart_quantity_product').val());
            });
            sellerMoney[seller_id] = {
                currency: $('#js_selected_currency_seller_'+ seller_id).val(),
                money: total_seller_money
            };
        });

        let defaultCurrency = $('#js_selected_currency').val();
        $Core.ajax('ynsocialstore.formatMoney', {
            method: 'POST',
            params: {
                currency: defaultCurrency,
                money: total_money,
                seller_money: JSON.stringify(sellerMoney),
            },
            success: function(response){
                let output = $.parseJSON(response);
                if(output.status) {
                    $('#ynstore_mycart #mycart_total').text(output.formatted_money);
                    if(!empty(output.formatted_seller_money)) {
                        for (let sellerKey in output.formatted_seller_money) {
                            let sellerMoney = output.formatted_seller_money[sellerKey];
                            $('#ynstore_mycart #mycart_total_seller_' + sellerKey).text(sellerMoney);
                        }
                    }
                }
            }
        });

        return false;
    },
    updateMyCartData: function(noRefesh,store_id){
        if(typeof store_id != "undefined" && store_id > 0)
        {
            $.each(ynsocialstore.myCartChanged.tempdelete,function(idx){
                if(idx != store_id)
                {
                    ynsocialstore.myCartChanged.deletedcart = ynsocialstore.myCartChanged.deletedcart.replace(ynsocialstore.myCartChanged.tempdelete[idx],0);
                }
            });
        }
        $.ajaxCall('ynsocialstore.deleteCart', $.param({sDeleted: ynsocialstore.myCartChanged.deletedcart}),'POST');

        ynsocialstore.myCartChanged.updatedcart = [];
        $('.mycart_quantity_product').each(function (idx) {
            cartID = $(this).parent().find('.mycart_cartproductid').val();
            attrID = $(this).parent().find('.mycart_attributeid').val();
            price = $(this).parent().find('.mycart_price_product').val();
            productID = $(this).parent().find('.mycart_productid').val();
            storeID = $(this).parent().find('.mycart_seller').val();
            quantity = $(this).val();
            if(typeof store_id != "undefined" && store_id > 0)
            {
                if(store_id == storeID)
                {
                    ynsocialstore.myCartChanged.updatedcart.push({cart_id:cartID,cart_attr_id:attrID,cart_attr_price:price,cart_quantity:quantity,cart_product_id:productID});
                }
                else{
                    ynsocialstore.myCartChanged.deletedcart.replace(cartID,0);
                }
            }
            else{
                ynsocialstore.myCartChanged.updatedcart.push({cart_id:cartID,cart_attr_id:attrID,cart_attr_price:price,cart_quantity:quantity,cart_product_id:productID});
            }
        });
        $.ajaxCall('ynsocialstore.updateMyCart', $.param({cart_id:ynsocialstore.myCartChanged.usercartid,cart_data:ynsocialstore.myCartChanged.updatedcart,no_refesh:noRefesh}),'POST');
        return false;
    },
    updateCurrentMaximumOfElement: function(attr_id,cartproduct_id){
        var attrList = [];
        ynsocialstore.myCartLimit.templimit = {};
        $('.mycart_quantity_product').each(function (idx) {
            attrid = $(this).parent().find('.mycart_attributeid').val();
            cartid = $(this).parent().find('.mycart_cartproductid').val();
            if(typeof ynsocialstore.myCartLimit.templimit[attrid] != 'undefined' && cartid != cartproduct_id && attrid == attr_id)
            {
                ynsocialstore.myCartLimit.templimit[attrid] = parseInt(ynsocialstore.myCartLimit.templimit[attrid]) + parseInt($(this).val());
            }
            else if(cartid != cartproduct_id && attrid == attr_id){
                attrList.push(attrid);
                ynsocialstore.myCartLimit.templimit[attrid] = parseInt($(this).val());
            }
            for(var i = 0; i < attrList.length; i++)
            {
                if(!isNaN(parseInt(ynsocialstore.myCartLimit.rawLimit[attrList[i]]))){
                    ynsocialstore.myCartLimit.realLimit[attrList[i]] = parseInt(ynsocialstore.myCartLimit.rawLimit[attrList[i]]) - parseInt(ynsocialstore.myCartLimit.templimit[attrList[i]]);
                }
            }
        });
    },
    toggleMycartDashBoard: function (ele,type) {
        var data = $(ele).attr('data-type'),
            holder = $('#ynstore-my-cart-item-list'),
            mycartbox = 'ynsocialstore_my_cart_box';

        if (data === 'show') {
            $(ele).attr('data-type', 'hide');
            setCookie(mycartbox, 'max', 1);
            if($('#ynstore_compare_btn').length && !$('#ynstore_compare_btn').hasClass('ynstore-hide'))
            {
                $('#ynstore_compare_btn').trigger('click');
            }
            holder.removeClass('ynstore-hide');
            $(ele).removeClass('ynstore-hide');
        }
        else if(data === 'hide' || type === 'hide'){
            $(ele).attr('data-type', 'show');
            setCookie(mycartbox, 'min', 1)
            holder.addClass('ynstore-hide');
            $(ele).addClass('ynstore-hide');
        }
    },
    initMyCartCallout: function(){
        var total_money = 0;
        if($('#ynstore-my-cart-dashboard').length == 0) return false;
        $('.js_ynstore_my_cart-quantity').each(function (idx) {
            maxquantity = $(this).parent().find('.js_ynstore_my_cart-maxquantity').val();
            if(parseInt($(this).val()) > parseInt(maxquantity))
            {
                $(this).val(maxquantity);
                $(this).parent().find('.js_ynstore_my_cart_max_noti').text(oTranslations['ynsocialstore.you_can_add_maximum_quantity_item_s_for_this_product'].replace('{quantity}',maxquantity)).removeClass('hide');
            }
            total_money += parseFloat($(this).val()) * parseFloat($(this).closest('.js_ynstore_my_cart-item').find('.js_ynstore_my_cart-price').val());
        });
        let defaultCurrency = $('#js_selected_currency').val();
        $Core.ajax('ynsocialstore.formatMoney', {
           method: 'POST',
           params: {
               currency: defaultCurrency,
               money: total_money
           },
           success: function(response){
               let output = $.parseJSON(response);
               if(output.status) {
                   $('#ynstore-my-cart-dashboard #js_ynstore_my_cart-total').text(output.formatted_money);
               }
           }
        });

        $('.js_ynstore_my_cart-quantity').change(function(){
            var total_money = 0;
            maxquan = $(this).closest('.js_ynstore_my_cart-item').find('.js_ynstore_my_cart-maxquantity').val();
            if(maxquan != "unlimited" && parseInt($(this).val()) > parseInt(maxquan))
            {
                $(this).val(maxquan);
                $(this).parent().find('.js_ynstore_my_cart_max_noti').text(oTranslations['ynsocialstore.you_can_add_maximum_quantity_item_s_for_this_product'].replace('{quantity}',maxquan)).removeClass('hide');
            }
            if (parseInt($(this).val()) <= 0 || $(this).val() == '' || isNaN(parseInt($(this).val()))) {
                $(this).val('1');
            }
            $('.js_ynstore_my_cart-quantity').each(function (idx) {
                total_money += parseFloat($(this).val()) * parseFloat($(this).closest('.js_ynstore_my_cart-item').find('.js_ynstore_my_cart-price').val());
            });

            let defaultCurrencyChange = $('#js_selected_currency').val();
            $Core.ajax('ynsocialstore.formatMoney', {
                method: 'POST',
                params: {
                    currency: defaultCurrencyChange,
                    money: total_money
                },
                success: function(response){
                    let output = $.parseJSON(response);
                    if(output.status) {
                        $('#ynstore-my-cart-dashboard #js_ynstore_my_cart-total').text(output.formatted_money);
                    }
                }
            });

            $.ajaxCall('ynsocialstore.updateCartQuantity', $.param({cartproduct_id:$(this).data('cartproductid'),cartproduct_quantity:$(this).val()}));
        });
    },
    updateQuantityMyCartCallout: function(ele){

        var pcart_id = $(ele).data('cartproductid'),
            input = $(ele).closest('.js_ynstore_my_cart-item').find('.js_ynstore_my_cart-quantity'),
            quantity = parseInt(input.val()),
            limit = $(ele).closest('.js_ynstore_my_cart-item').find('.js_ynstore_my_cart-maxquantity').val();
        if($(ele).data('type') == "minus"){
            if(quantity <= 1) return false;
            input.val(quantity - 1).change();
        }
        else{
            if(quantity >= limit && limit != "unlimited") {
                $(ele).closest('.js_ynstore_my_cart-item').find('.js_ynstore_my_cart_max_noti').text(oTranslations['ynsocialstore.you_can_add_maximum_quantity_item_s_for_this_product'].replace('{quantity}',limit)).removeClass('hide');
                return false;
            }
            input.val(quantity + 1).change();
        }
        return false;
    },
    confirmdeleteOneCart: function(ele){
        var sHtml = oTranslations['ynsocialstore.are_you_sure_want_to_remove_this_item'];
        sHtml += '<div class="ynstore-confirm-btns">';
        sHtml += '<span class="ynstore-confirm-btn"><i class="ico ico-ban" onclick="$(\'#js-ynstore-my-cart-confirm\').addClass(\'hide\'); return false;"></i></span>';
        sHtml += '<span class="ynstore-confirm-btn"><i class="ico ico-check" onclick="return ynsocialstore.deleteOneCart('+$(ele).data('cartproductid')+');"></i></span>';
        sHtml += '</div>';
        $('#js-ynstore-my-cart-confirm').html(sHtml).removeClass('hide');
    },
    confirmdeleteAllCart: function(ele){
        var sHtml = oTranslations['ynsocialstore.are_you_sure_want_to_remove_all_items'];
        sHtml += '<div class="ynstore-confirm-btns">';
        sHtml += '<span class="ynstore-confirm-btn"><i class="ico ico-ban" onclick="$(\'#js-ynstore-my-cart-confirm\').addClass(\'hide\'); return false;"></i></span>';
        sHtml += '<span class="ynstore-confirm-btn"><i class="ico ico-check" onclick="return ynsocialstore.deleteAllCart('+$(ele).data('cartid')+');"></i></span>';
        sHtml += '</div>';
        $('#js-ynstore-my-cart-confirm').html(sHtml).removeClass('hide');
    },
    deleteOneCart: function(pcart_id){
        $.ajaxCall('ynsocialstore.deleteOneCart', $.param({cartproduct_id: pcart_id}),'POST');
        return false;
    },
    deleteAllCart: function(cart_id){
        $.ajaxCall('ynsocialstore.deleteAllCart', $.param({cart_id: cart_id}),'POST');
        return false;
    },
    checkMinOrder: function(ele,store_id,type)
    {
        $('#ynstore_mycart_loading').show();
        $('#ynstore_mycart_buy_all').addClass('disabled');
        $('#ynstore_checkout_place_order').addClass('disabled');
        if(parseInt(store_id) > 0){
            $('#js_error_message_store_'+ store_id).html('').addClass('hide');
        }
        else{
            $('.js_ynstore_error').html('').addClass('hide');
        }
        var myCartSeller = [];
        $('.mycart_productid').each(function(){
            storeid = $(this).parent().find('.mycart_seller').val();
            quantity = $(this).parent().find('.mycart_quantity_product').val();
            if(store_id == 0){
                myCartSeller.push({storeid: storeid, productid: $(this).val(),quantity: quantity});
            }
            else if(storeid == store_id) {
                myCartSeller.push({storeid: storeid, productid: $(this).val(),quantity: quantity});
            }
        });
        $.ajaxCall('ynsocialstore.checkMinOrderWithQuantity', $.param({store_id:store_id,list_product:myCartSeller,sType:type}));
        return false;
    },

    initSlider: function() {
        PF.event.on('on_page_column_init_end', function() {

            if ($('#ynstore-products-featured-block').length > 0) {
                if ($('#ynstore-products-featured-block').data('initSlide')) {
                    return false;
                }
                $('#ynstore-products-featured-block').data('initSlide', 1);
                $('#ynstore-products-featured-block').addClass('dont-unbind-children');
                var initSlider = function() {
                    var ele = $('#ynstore-products-featured-block');
                    var item_amount = parseInt(ele.find('.item').length);
                    var true_false = 0;
                    if (item_amount > 1) {
                        true_false = true;
                    }else{
                        true_false = false;
                    }
                    var rtl = false;
                    if ($("html").attr("dir") == "rtl") {
                        rtl = true;
                    }
                    ele.fadeIn();
                    ele.owlCarousel({
                        rtl: rtl,
                        loop:true_false,
                        margin:0,
                        nav:true_false,
                        autoplay:true_false,
                        autoplayHoverPause:true_false,
                        items:1,
                        navText: ['<i class="ico ico-angle-left"></i>','<i class="ico ico-angle-right"></i>'],
                    });

                    $('.owl-buttons').addClass('dont-unbind');
                    $('.owl-buttons .owl-prev').addClass('dont-unbind');
                    $('.owl-buttons .owl-prev').addClass('dont-unbind');
                    $('.owl-carousel').addClass('dont-unbind');
                };

                if (typeof($.fn.owlCarousel) == 'undefined') {
                    var script = document.createElement('script');
                    script.src = ynsocialstore.sCorePath + 'module/ynsocialstore/static/jscript/owl.carousel.min.js';
                    script.onload = initSlider;
                    document.getElementsByTagName("head")[0].appendChild(script);
                }else {
                    initSlider();
                }
            };
            if ($('#ynstore-featured-block').length > 0) {
                if ($('#ynstore-featured-block').data('initSlide')) {
                    return false;
                }
                $('#ynstore-featured-block').data('initSlide', 1);
                $('#ynstore-featured-block').addClass('dont-unbind-children');
                var initSlider = function() {
                    var ele = $('#ynstore-featured-block');
                    var item_amount = parseInt(ele.find('.item').length);
                    var true_false = 0;
                    if (item_amount > 1) {
                        true_false = true;
                    }else{
                        true_false = false;
                    }
                    var rtl = false;
                    if ($("html").attr("dir") == "rtl") {
                        rtl = true;
                    }
                    ele.fadeIn();
                    ele.owlCarousel({
                        rtl: rtl,
                        loop:true_false,
                        margin:0,
                        nav:true_false,
                        dots: false,
                        //autoplay:2000,
                        autoplayHoverPause:true_false,
                        items:1,
                        navText: ['<i class="ico ico-angle-left"></i>','<i class="ico ico-angle-right"></i>'],
                    });

                    $('.owl-buttons').addClass('dont-unbind');
                    $('.owl-buttons .owl-prev').addClass('dont-unbind');
                    $('.owl-buttons .owl-prev').addClass('dont-unbind');
                    $('.owl-carousel').addClass('dont-unbind');
                };

                $('#ynstore-featured-block').on('shown.bs.dropdown', function (){
                    $('.owl-stage-outer').css('z-index','1');
                });

                $('#ynstore-featured-block').on('hidden.bs.dropdown', function (){
                    $('.owl-stage-outer').css('z-index','initial');
                })

                if (typeof($.fn.owlCarousel) == 'undefined') {
                    var script = document.createElement('script');
                    script.src = ynsocialstore.sCorePath + 'module/ynsocialstore/static/jscript/owl.carousel.min.js';
                    script.onload = initSlider;
                    document.getElementsByTagName("head")[0].appendChild(script);
                }else {
                    initSlider();
                }
            };
            if ($('.ynstore-hot-seller-slideshow').length > 0) {
                if ($('#ynstore-hot-seller-slideshow').data('initSlide')) {
                    return false;
                }
                $('#ynstore-hot-seller-slideshow').data('initSlide', 1);
                $('#ynstore-hot-seller-slideshow').addClass('dont-unbind-children');
                var initSlider = function() {
                    var ele = $('.ynstore-hot-seller-slideshow');
                    var item_amount = parseInt(ele.find('.item').length);
                    var true_false = 0;
                    if (item_amount > 1) {
                        true_false = true;
                    }else{
                        true_false = false;
                    }
                    var rtl = false;
                    if ($("html").attr("dir") == "rtl") {
                        rtl = true;
                    }
                    ele.owlCarousel({
                        rtl: rtl,
                        stagePadding: 0,
                        loop:true_false,
                        autoplay: 1500,
                        margin:10,
                        nav:true_false,
                        items:2,
                        dots:false,
                        navText: ['<i class="ico ico-angle-left"></i>','<i class="ico ico-angle-right"></i>'],
                    });

                    $('.owl-buttons').addClass('dont-unbind');
                    $('.owl-buttons .owl-prev').addClass('dont-unbind');
                    $('.owl-buttons .owl-prev').addClass('dont-unbind');
                    $('.owl-carousel').addClass('dont-unbind');
                };

                if (typeof($.fn.owlCarousel) == 'undefined') {
                    var script = document.createElement('script');
                    script.src = ynsocialstore.sCorePath + 'module/ynsocialstore/static/jscript/owl.carousel.min.js';
                    script.onload = initSlider;
                    document.getElementsByTagName("head")[0].appendChild(script);
                }else {
                    initSlider();
                }
            }
            if ($('#ynstore-product-detail-images-big').length > 0){
                if ($('#ynstore-product-detail-images-big').data('initSlide')) {
                    return false;
                }
                $('#ynstore-product-detail-images-big').data('initSlide', 1);
                $('#ynstore-product-detail-images-big').addClass('dont-unbind-children');
                $('#ynstore-product-detail-images-small').addClass('dont-unbind-children');

                var initSlider = function() {
                    var sync1 = $('#ynstore-product-detail-images-big'),
                        sync2 = $('#ynstore-product-detail-images-small'),
                        flag = false,
                        duration = 300;
                    var rtl = false;
                    if ($("html").attr("dir") == "rtl") {
                        rtl = true;
                    }
                    //Init slide big images
                    sync1.owlCarousel({
                        rtl: rtl,
                        items: 1,
                        margin: 0,
                        nav: true,
                        navText: ["<i class='ico ico-angle-left'></i>","<i class='ico ico-angle-right'></i>"],
                        dots: false,
                        responsiveRefreshRate : 200,
                    })

                    //Init slide small images
                    sync2.owlCarousel({
                        rtl: rtl,
                        margin: 10,
                        items: 5,
                        nav: false,
                        navText: ["<i class='ico ico-angle-left'></i>","<i class='ico ico-angle-right'></i>"],
                        dots: false,
                        responsive : {
                            // breakpoint from 0 up
                            0 : {
                                items: 2
                            },
                            // breakpoint from 480 up
                            480 : {
                                items: 4
                            },
                            // breakpoint from 768 up
                            768 : {
                                items: 5
                            }
                        }
                    });

                    sync2.find(".owl-item").eq(0).addClass("current");

                    sync1.on('changed.owl.carousel', function (e) {
                        if (!flag) {
                            flag = true;
                            sync2.trigger('to.owl.carousel', [e.item.index, duration, true]);
                            flag = false;
                            sync2.find(".owl-item").removeClass('current');
                            sync2.find(".owl-item").eq(e.item.index).addClass("current");
                        }
                    });

                    sync2.on('changed.owl.carousel', function (e) {
                        if (!flag) {
                            flag = true;
                            sync1.trigger('to.owl.carousel', [e.item.index, duration, true]);
                            flag = false;
                        }
                    });

                    sync2.on('click', '.owl-item', function () {
                        sync1.trigger('to.owl.carousel', [$(this).index(), duration, true]);
                    });
                    $('.ynstore-product-detail-images').css('visibility','visible');
                    //Init Zoom
                    setTimeout(function(){
                        $('.easyzoom').easyZoom();
                    },500)
                };
                if (typeof($.fn.owlCarousel) == 'undefined') {
                    var script = document.createElement('script');
                    script.src = ynsocialstore.sCorePath + 'module/ynsocialstore/static/jscript/owl.carousel.min.js';
                    script.onload = initSlider;
                    document.getElementsByTagName("head")[0].appendChild(script);
                }else {
                    initSlider();
                }
            }
            else if($('.ynstore-product-detail-images').length){
                $('.ynstore-product-detail-images').css('visibility','visible');
            }
            if ($('#ynstore-store-featured-products-block').length > 0){
                if ($('#ynstore-store-featured-products-block').data('initSlide')) {
                    return false;
                }
                $('#ynstore-store-featured-products-block').data('initSlide', 1);
                $('#ynstore-store-featured-products-block').addClass('dont-unbind-children');
                var initSlider = function() {
                    var ele = $('#ynstore-store-featured-products-block'),
                        item_amount = parseInt(ele.find('.item').length),
                        true_false = 0,
                        stage_padding = 0;
                    if (item_amount > 1) {
                        true_false = true;
                        stage_padding = 75;
                    } else {
                        true_false = false;
                        stage_padding: 0;
                    }

                    ele.on('initialized.owl.carousel', function (e) {
                        $('.owl-controls').insertBefore('#ynstore-store-featured-products-block');
                        if (item_amount == 1) {
                            $('.owl-stage-outer').css('margin-left', '0px');
                        }
                    });
                    ele.fadeIn();
                    var rtl = false;
                    if ($("html").attr("dir") == "rtl") {
                        rtl = true;
                    }
                    ele.owlCarousel({
                        rtl: rtl,
                        stagePadding: 0,
                        loop: true_false,
                        margin: 10,
                        nav: true_false,
                        items: 1,
                        dots: false,
                        navText: ['<i class="ico ico-angle-left"></i>', '<i class="ico ico-angle-right"></i>'],
                        responsive: {
                            991: {
                                stagePadding: stage_padding,
                            }
                        }
                    });
                }
                if (typeof($.fn.owlCarousel) == 'undefined') {
                    var script = document.createElement('script');
                    script.src = ynsocialstore.sCorePath + 'module/ynsocialstore/static/jscript/owl.carousel.min.js';
                    script.onload = initSlider;
                    document.getElementsByTagName("head")[0].appendChild(script);
                }else {
                    initSlider();
                }
            }
        });
    }
}
$Ready(function() {
    ynsocialstore.initSlider();
    $(document).tooltip({
        selector: '.ynstore-att-item-link[data-toggle="tooltip"]',
        container:'body'
    });
});

$Behavior.readyYnSocialstore = function() {
    ynsocialstore.sCorePath = ($("#ynsocialstore_corepath").length) ? $("#ynsocialstore_corepath").val() : '';
    ynsocialstore.init();
    new Clipboard('.yns-copy-btn');
    ynsocialstore.initRating();
    ynsocialstore.initCompareStore();
    ynsocialstore.initCompareProduct();
    ynsocialstore.initMyCartCallout();
};
