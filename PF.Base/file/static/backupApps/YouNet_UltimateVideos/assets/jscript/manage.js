
var ultimatevideo = {

	deleteVideo: function(iVideoId) {
        var message = oTranslations['are_you_sure'];
        $Core.jsConfirm({message: message}, function () {
            $.ajaxCall('ultimatevideo.deleteVideoInAdmin', 'iVideoId=' + iVideoId);
        }, function () {
        });
        return false;
	},

	updateApproved: function(iVideoId, iIsApproved) 
	{
		$.ajaxCall('ultimatevideo.updateApprovedInAdmin', 'iVideoId=' + iVideoId + '&iIsApproved=' + iIsApproved);	
	},

	updateFeatured: function(iVideoId, iIsFeatured)
	{
		$.ajaxCall('ultimatevideo.updateFeaturedInAdmin', 'iVideoId=' + iVideoId + '&iIsFeatured=' + iIsFeatured + '&global_ajax_message=true');
	},
	checkDisableStatus: function()
	{
		var status = false;
		$('.video_row_checkbox').each(function(index, element) {
			var sIdName = '#ynuv_video_' + element.value;
			if (element.checked == true) {
				status = true;
				$(sIdName).css({
					'backgroundColor' : '#FFFF88'
				});
			}
			else {
				if(element.value % 2 == 0){
					$(sIdName).css({
						'backgroundColor' : '#F0f0f0'
					});
				}
				else{
					$(sIdName).css({
						'backgroundColor' : '#F9F9F9'
					});
				}
			}
		});
		ultimatevideo.setButtonStatus(status);
		return status;
	},

	setButtonStatus: function(status)
	{
		if (status) 
		{
			$('.delete_selected').removeClass('disabled');
			$('.delete_selected').attr('disabled', false);
			$('.approve_selected').removeClass('disabled');
			$('.approve_selected').attr('disabled', false);
			$('.unapprove_selected').removeClass('disabled');
			$('.unapprove_selected').attr('disabled', false);
		}
		else 
		{
			$('.delete_selected').addClass('disabled');
			$('.delete_selected').attr('disabled', true);
			$('.approve_selected').addClass('disabled');
			$('.approve_selected').attr('disabled', true);
			$('.unapprove_selected').addClass('disabled');
			$('.unapprove_selected').attr('disabled', true);
		}
	},
	
	checkAllVideo: function()
	{
		var checked = document.getElementById('ynuv_video_list_check_all').checked;
		$('.video_row_checkbox').each(function(index,element){
			element.checked = checked;
			var sIdName = '#ynuv_video_' + element.value;
			if (element.checked == true) {
				$(sIdName).css({
					'backgroundColor' : '#FFFF88'
				});
			}
			else {
				if(element.value % 2 == 0){
					$(sIdName).css({
						'backgroundColor' : '#F0f0f0'
					});
				}
				else{
					$(sIdName).css({
						'backgroundColor' : '#F9F9F9'
					});
				}
			}
		});
		ultimatevideo.setButtonStatus(checked);
		return checked;
	},
	actionMultiSelect : function(obj)
	{
		$.ajaxCall('ultimatevideo.actionMultiSelectVideo',$(obj).serialize(),'post');
		return false;
	},
	switchAction : function (obj,sType)
	{
		switch(sType){
			case 'delete':
				$("#ynuv_multi_select_action").val('1');
				break;
			case 'approve':
				$("#ynuv_multi_select_action").val('2');
				break;
			case 'unapprove':
				$("#ynuv_multi_select_action").val('3');
				break;
		}
        var message = oTranslations['are_you_sure'];
		if (($(obj).is('input[type="submit"]') || $(obj).is('button')) && $(obj).closest('form').length > 0) {
            var form = $(obj).closest('form');
            $Core.jsConfirm({message: message}, function() {
                form.submit();
            }, function(){});
            return false;
        }
        return confirm(message);
	},
	getSearchData: function (obj)
	{
		$.ajaxCall('ultimatevideo.filterAdminFilterVideo',$(obj).serialize(),'post');
		return false;
	}
}