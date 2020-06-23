;var manageevent = {
	showLoading: function(){
		$('#ynfevent_loading').show();
	}, 
	hideLoading: function(){
		$('#ynfevent_loading').hide();
	}, 
	confirmdeleteEvent: function(event_id){
        $Core.jsConfirm({message: oTranslations['are_you_sure']}, function () {
            manageevent.showLoading();
            $.ajaxCall('fevent.deleteEvent', 'event_id=' + event_id);
        }, function () {

        });
        return false;
	}, 
	deleteEvent: function(event_id){
		manageevent.showLoading();
		$.ajaxCall('fevent.deleteEvent', 'event_id=' + event_id);
	}, 
	approveEvent: function(event_id){
		manageevent.showLoading();
		$.ajaxCall('fevent.approveEvent', 'event_id=' + event_id);
	}, 
	updateFeaturedBackEnd : function(event_id,iFeatured){
		manageevent.showLoading();

		$.ajaxCall('fevent.updateFeaturedBackEnd', 'event_id='+event_id+'&iIsFeatured='+iFeatured);
	}, 
	updateSponsorBackEnd : function(event_id,iSponsor){
		manageevent.showLoading();

		$.ajaxCall('fevent.updateSponsorBackEnd', 'event_id='+event_id+'&iSponsor='+iSponsor);
	}
}; 