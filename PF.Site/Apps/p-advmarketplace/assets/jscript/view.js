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