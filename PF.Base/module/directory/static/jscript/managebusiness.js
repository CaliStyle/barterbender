;var managebusiness = {
	showLoading: function(iBusinessId){
		$('#yndirectory_loading').show();
	}, 
	hideLoading: function(iBusinessId){
		$('#yndirectory_loading').hide();
	},
    confirmdeleteBusiness: function(iBusinessId){
        $Core.jsConfirm({message: oTranslations['directory.are_you_sure_you_want_to_delete_this_business']}, function () {
            $.ajaxCall('directory.deleteBusiness', 'iBusinessId=' + iBusinessId);
        }, function () {

        });
        return false;
    },
	deleteBusiness: function(iBusinessId){
		managebusiness.showLoading();
		$.ajaxCall('directory.deleteBusiness', 'iBusinessId=' + iBusinessId);
	}, 
	denyBusiness: function(iBusinessId){
		managebusiness.showLoading();
		$.ajaxCall('directory.denyBusiness', 'iBusinessId=' + iBusinessId);
	}, 
	approveBusiness: function(iBusinessId){
		managebusiness.showLoading();
		$.ajaxCall('directory.approveBusiness', 'iBusinessId=' + iBusinessId);
	}, 
	openTransferownerBusiness: function(iBusinessId){
		tb_show(oTranslations['directory.transfer_owner'], $.ajaxBox('directory.openTransferownerBusiness', 'height=300&width=530&iBusinessId=' + iBusinessId));
	}
	,confirmFeaturedBackEnd : function(iBusinessId,iFeatured,isUnLimited,sExpireDate){
		if(iFeatured){
			if(isUnLimited){
				$Core.jsConfirm({message: oTranslations['directory.confirm_feature_business_unlimited']}, function () {
					managebusiness.updateFeaturedBackEnd(iBusinessId,iFeatured);
				}, function () {

				});
				return false;

			}
			else{

				$Core.jsConfirm({message: oTranslations['directory.directory_confirm_feature_business_limited'].replace('{expired_date}',sExpireDate)}, function () {
					managebusiness.updateFeaturedBackEnd(iBusinessId,iFeatured);
				}, function () {

				});
                return false;

			}
		}
		else{
			managebusiness.updateFeaturedBackEnd(iBusinessId,iFeatured);
		}



	}
	,updateFeaturedBackEnd : function(iBusinessId,iFeatured){
		managebusiness.showLoading();

		$.ajaxCall('directory.updateFeaturedBackEnd', 'iBusinessId='+iBusinessId+'&iIsFeatured='+iFeatured);
	}
	,
	transferownerBusiness: function(){
		if($("#js_custom_search_friend_placement").length > 0){
			if($("#js_custom_search_friend_placement input[name='owner[]']").length > 0){
				var iBusinessId = $('#owner_business_id').val();
				var iUserId = 0;
				$("#js_custom_search_friend_placement input[name='owner[]']").each(function(){
					iUserId = this.value;
				});
				
				managebusiness.showLoading();
				$.ajaxCall('directory.transferownerBusiness', 'iBusinessId=' + iBusinessId + '&iUserId=' + iUserId);
			}
		}
	}, 
}; 