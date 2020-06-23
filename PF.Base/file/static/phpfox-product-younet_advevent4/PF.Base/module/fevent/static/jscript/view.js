$Behavior.feventShowImage = function(){
    $('.js_fevent_click_image').click(function(){
        
        var oNewImage = new Image();
        oNewImage.onload = function(){
            $('#js_fevent_click_image_viewer').show();
            $('#js_fevent_click_image_viewer_inner').html('<img src="' + this.src + '" alt="" />');            
            $('#js_fevent_click_image_viewer_close').show();
        };
        oNewImage.src = $(this).attr('href');
        
        return false;
    });
    
    $('#js_fevent_click_image_viewer_close a').click(function(){
        $('#js_fevent_click_image_viewer').hide();
        return false;
    });
    var gmapViewLink = $("#js_block_border_core_menusub .header_display li:last a");
    if(gmapViewLink.size() == 0)
    {
        gmapViewLink = $("._block_menu_sub .header_display li:last a");
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

var ynfeViewPage = 
{
    dataPrint : {}
    , glat : -1
    , glong : -1
    , geocoder : null 
    , curGeoCode : null 
    , curAddress : null 
    , eventGlat : -1
    , eventGlong : -1
    , eventLocation : null 
    , eventCountry : null 
    , eventCity : null 
    , eventGeoCode : null 
    , eventAddress : null 
    , canViewMap : false
    , init: function()
    {
        ynfeViewPage.setPermission(); 
        ynfeViewPage.setEventData(); 
        if(true == ynfeViewPage.canViewMap){
            ynfeViewPage.initLocationMap(); 
            ynfeViewPage.getVisitorLocation(); 
        }
        if($('#fevent_rsvp').length) {
            $('#fevent_rsvp .item-event-option').off('click').on('click', function () {
                var t = $(this), f = $(this).parents('form:first');
                var rsvp = t.find('input[name="rsvp"]').val();
                var event_id = $("#fevent_rsvp_eventid").val();
                $('.item-event-option.active').removeClass('active');
                f.find('.js_checked').remove();
                t.find('.js_rsvp_title').prepend('<span class="ico ico-check js_checked"></span> ');
                t.addClass('active');
                t.find('input').prop('checked', true);
                f.ajaxCall('fevent.addRsvp', '&id=' + event_id + '&type_event=only_this_event&rsvp=' + rsvp)
            });
        }
    }          
    ,confirmSubmitRsvp : function(){

        sHtml = "";
        sHtml += '<div class="white-popup-block" style="width: 300px;">'; 
            sHtml += '<div class="ynfevent-edit-confirm-box-title">'; 
                sHtml += oTranslations['fevent.how_would_you_want_to_submit_your_rsvp']; 
            sHtml += '</div>'; 

            sHtml += '<div>'; 
                sHtml += oTranslations['fevent.please_choose_the_type_of_event_to_apply']; 
            sHtml += '</div>'; 

            sHtml += '<div id="ynfevent_editconfirmboxoption">'; 
                sHtml += '<input type="radio" name="popup_confirmeditevent" value="only_this_event" checked="checked" />'; 
                sHtml += ' Only this event';  
                sHtml += '</br> <input type="radio" name="popup_confirmeditevent" value="following_events" />'; 
                sHtml += ' This event and all following events'; 
            sHtml += '</div>'; 

            sHtml += '<div class="ynfevent-edit-confirm-box-button">'; 
                sHtml += '<button class="btn btn-sm btn-primary" onclick="ynfeViewPage.yesConfirmSubmitRsvp();">'; 
                    sHtml += oTranslations['fevent.confirm']; 
                sHtml += '</button>'; 
                sHtml += '<button class="btn btn-sm btn-danger" style="margin-left: 10px;" onclick="$(\'.mfp-close-btn-in .mfp-close\').trigger(\'click\');">'; 
                    sHtml += oTranslations['fevent.cancel']; 
                sHtml += '</button>'; 
            sHtml += '</div>'; 

        sHtml += '</div>'; 

        $.magnificPopup.open({
              items: {
                src: sHtml, 
                type: 'inline'
              }
            });

        return false;
    
    } 
    , yesConfirmSubmitRsvp: function(){
        var type_event = $("#ynfevent_editconfirmboxoption input[type='radio']:checked").val();
        var type_rsvp = $("#js_block_border_fevent_rsvp input[type='radio']:checked").val();
        var event_id = $("#fevent_rsvp_eventid").val();
        $('#js_event_rsvp_button').find('input:first').attr('disabled', true);
        $('#js_event_rsvp_update').html($.ajaxProcess('Updating')).show();
        $(this).ajaxCall('fevent.addRsvp', '&id='+event_id+'&type_event='+type_event+'&rsvp='+type_rsvp); 
        $('.mfp-close-btn-in .mfp-close').trigger('click');
        return false;
   
    }
    , setEventData :function()
    {        
        ynfeViewPage.eventGlat = $('#eventGlat').val();
        ynfeViewPage.eventGlong = $('#eventGlong').val();

        if($('#eventLocation').length > 0){
            ynfeViewPage.eventLocation = $('#eventLocation').val();
        }
        if($('#eventCountry').length > 0){
            ynfeViewPage.eventCountry = $('#eventCountry').val();
        }
        if($('#eventCity').length > 0){
            ynfeViewPage.eventCity = $('#eventCity').val();
        }
    }
    , setPermission :function()
    {        
        if($('#canViewMap').length > 0){
            ynfeViewPage.canViewMap = true;
        }
    }
    , initLocationMap :function()
    {
        $("body").addClass("js_stopscript");

        if(!$("body").hasClass("js_newscript")) 
        {
            var script = document.createElement('script');
            script.type= 'text/javascript';
            script.src = 'https://maps.google.com/maps/api/js?sensor=false&callback=showOnMapForLocationBlock';
            document.body.appendChild(script);
            $("body").addClass("js_newscript");
            $("body").removeClass("js_stopscript");
        }

        google.maps.event.addDomListener(window, 'load', showOnMapForLocationBlock);

        if($("body").hasClass("js_stopscript"))
        {
            showOnMapForLocationBlock();    
        }
    }
    , getVisitorLocation :function()
    {
        if (ynfeViewPage.glat != -1 && ynfeViewPage.glong != -1)
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
                    ynfeViewPage.glat = oPos.coords.latitude; 
                    ynfeViewPage.glong = oPos.coords.longitude; 

                    ynfeViewPage.getCurrentAddress(); 
                }
                , function(){ return; }
            );
        }
        else
        {
            //  get location without HTLM5
        }
    }
    , getCurrentAddress: function()
    {
        if(null == ynfeViewPage.geocoder){
            ynfeViewPage.geocoder = new google.maps.Geocoder();
        }

        var latlng = new google.maps.LatLng(ynfeViewPage.glat, ynfeViewPage.glong);
        ynfeViewPage.geocoder.geocode({'latLng': latlng}, function(results, status) {
            if(status == google.maps.GeocoderStatus.OK) {
                ynfeViewPage.curGeoCode = results; 
                ynfeViewPage.curAddress = results[0]['formatted_address']; 

                ynfeViewPage.getEventAddress();
            };
        });
    }
    , getEventAddress: function()
    {
        if(null == ynfeViewPage.eventLocation
            && null == ynfeViewPage.eventCountry
            && null == ynfeViewPage.eventCity
            ){
            if(null == ynfeViewPage.geocoder){
                ynfeViewPage.geocoder = new google.maps.Geocoder();
            }

            var latlng = new google.maps.LatLng(ynfeViewPage.eventGlat, ynfeViewPage.eventGlong);
            ynfeViewPage.geocoder.geocode({'latLng': latlng}, function(results, status) {
                if(status == google.maps.GeocoderStatus.OK) {
                    ynfeViewPage.eventGeoCode = results; 
                    ynfeViewPage.eventAddress = results[0]['formatted_address']; 

                    ynfeViewPage.updateGetDirection();
                };
            });
        } else {
            ynfeViewPage.eventAddress = ''; 
            ynfeViewPage.eventAddress += ynfeViewPage.eventLocation; 

            if(null != ynfeViewPage.eventCountry){
                ynfeViewPage.eventAddress += '+' + ynfeViewPage.eventCountry; 
            }
            if(null != ynfeViewPage.eventCity){
                ynfeViewPage.eventAddress += '+' + ynfeViewPage.eventCity; 
            }

            ynfeViewPage.updateGetDirection();
        }
    }
    , checkCurrentLocation: function()
    {
        if(null != ynfeViewPage.curAddress && null != ynfeViewPage.eventAddress)
        {
            $('#getDirectionsDiv').show();
        } else {
            $('#getDirectionsDiv').hide();
        }
    }           
    , updateGetDirection: function()
    {
        if(null == ynfeViewPage.curAddress || null == ynfeViewPage.eventAddress){
            return false;
        }

        var href = '//maps.google.com/maps?'; 
        var saddr = 'saddr=' + ynfeViewPage.curAddress;
        var daddr = '&daddr=' + ynfeViewPage.eventAddress;
        href = href + saddr + daddr;

        $('#getDirectionsAnchor').attr('href',href);

        ynfeViewPage.checkCurrentLocation();
    }           
}; 

$Behavior.initynfeViewPage = function()
{
    showOnMapForLocationBlock = function()
    {
    }    

    ynfeViewPage.init();

}

