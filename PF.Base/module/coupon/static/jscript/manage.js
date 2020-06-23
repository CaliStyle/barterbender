var coupon = {
    /**
     *  Update news feature status
     * @author TienNPL
     * @param <int> iCouponId is the Id of selected news
     * @param <int> iIsFeatured is the feature status of the news
     */
    updateFeatured: function(iCouponId, iIsFeatured)
    {
        $.ajaxCall('coupon.updateFeatured', 'iCouponId=' + iCouponId + '&iIsFeatured=' + iIsFeatured);
    },
    /**
     *  Update news publish status
     * @author TienNPL
     * @param <int> iCouponId is the Id of selected news
     * @param <int> iIsFeatured is the publish status of the news
     */
    updatePublished: function(iCouponId, iIsPublished)
    {
        $.ajaxCall('coupon.updatePublished', 'iCouponId=' + iCouponId + '&iIsPublished=' + iIsPublished);
    },
	/**
	 * Delete coupon when admin click on delete button
	 * @author TienNPL
	 * @param int iCouponId is the coupon id need to be deleted
	 * @return false
	 */
	deleteCoupon: function(iCouponId) {
		var message = oTranslations['coupon.are_you_sure_you_want_to_delete_this_coupon'];
		$Core.jsConfirm({message: message}, function(){
            $.ajaxCall('coupon.deleteCoupon', 'iCouponId=' + iCouponId);
		});
		return false;
	},
	/**
	 * Pause coupon when admin click on pause button
	 * @author TienNPL
	 * @param int iCouponId is the coupon id need to be paused
	 * @return false
	 */
	pauseCoupon: function(iCouponId) {
		if (confirm(oTranslations['coupon.are_you_sure_you_want_to_pause_this_coupon'])) 
		{
			$.ajaxCall('coupon.pauseCoupon', 'iCouponId=' + iCouponId);
		}
		return false;
	},
	/**
	 * Resume coupon when admin click on resume button
	 * @author TienNPL
	 * @param int iCouponId is the coupon id need to be resumed
	 * @return false
	 */
	resumeCoupon: function(iCouponId) {
		if (confirm(oTranslations['coupon.are_you_sure_you_want_to_resume_this_coupon'])) 
		{
			$.ajaxCall('coupon.resumeCoupon', 'iCouponId=' + iCouponId);
		}
		return false;
	},
	/**
	 * Close coupon when admin click on close button
	 * @author TienNPL
	 * @param int iCouponId is the coupon id need to be closed
	 * @return false
	 */
	closeCoupon: function(iCouponId) {
		if (confirm(oTranslations['coupon.are_you_sure_you_want_to_close_this_coupon'])) 
		{
			$.ajaxCall('coupon.closeCoupon', 'iCouponId=' + iCouponId);
		}
		return false;
	},
	/**
	 * Close coupon when admin click on close button
	 * @author TienNPL
	 * @param int iCouponId is the coupon id need to be closed
	 * @return false
	 */
	denyCoupon: function(iCouponId) {
		if (confirm(oTranslations['coupon.are_you_sure_you_want_to_deny_this_coupon'])) 
		{
			$.ajaxCall('coupon.denyCoupon', 'iCouponId=' + iCouponId);
		}
		return false;
	},
	/**
	 * Approve coupon when admin click on approve button
	 * @author TienNPL
	 * @param int iCouponId is the coupon id need to be approved
	 * @return false
	 */
	approveCoupon: function(iCouponId) {
		if (confirm(oTranslations['coupon.are_you_sure_you_want_to_approve_this_coupon'])) 
		{
			$.ajaxCall('coupon.approveCoupon', 'iCouponId=' + iCouponId);
		}
		return false;
	},
	/**
	 * Update coupon feature status
	 * @author TienNPL 
 	 * @param <int> iCouponId is the Id of selected coupon
 	 * @param <int> iIsFeatured is the feature status of the coupon
	 */
	updateFeatured: function(iCouponId, iIsFeatured)
	{
		$.ajaxCall('coupon.updateFeatured', 'iCouponId=' + iCouponId + '&iIsFeatured=' + iIsFeatured);
	},
	/**
	 * Check/Uncheck all recording row when press on checkbox in the table head
	 * @author TienNPL
	 * @return boolean check is the status of the checking
	 */
	checkDisableStatus: function()
	{
		var status = false;
		$('.coupon_row_checkbox').each(function(index, element) {
			var sIdName = '#coupon_' + element.value;
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
		coupon.setButtonStatus(status);
		return status;
	},
	/**
	 * Disable/enable  buttons
	 * @author TienNPL
	 * @param boolean status is the status for button (true = enabled| false = disabled) 
	 * @return none
	 */
	setButtonStatus: function(status)
	{
		if (status) 
		{
			$('.delete_selected').removeClass('disabled');
			$('.delete_selected').attr('disabled', false);
			$('.approve_selected').removeClass('disabled');
			$('.approve_selected').attr('disabled', false);
		}
		else 
		{
			$('.delete_selected').addClass('disabled');
			$('.delete_selected').attr('disabled', true);
			$('.approve_selected').addClass('disabled');
			$('.approve_selected').attr('disabled', true);
		}
	},
	
	checkAllCoupon: function()
	{
		var checked = document.getElementById('coupon_list_check_all').checked;
		$('.coupon_row_checkbox').each(function(index,element){
			element.checked = checked;
			var sIdName = '#coupon_' + element.value;
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
		coupon.setButtonStatus(checked);
		return checked;
	},
};