<?php if(Phpfox::getUserParam('suggestion.enable_friend_suggestion') && Phpfox::getUserParam('suggestion.enable_content_suggestion_popup') && Phpfox::getService('suggestion')->isAllowContentSuggestionPopup()) { 
	$iItemId = $aImages[0]['photo_id'];
	$aPhoto = Phpfox::getService("advancedphoto")->getCoverPhoto($iItemId);
	$sTitle = base64_encode(urlencode($aPhoto['title']));
	$sExpectUserId = Phpfox::getUserId();
	$sLinkCallback = Phpfox::getParam('core.path').'advancedphoto/'.$aPhoto['photo_id'];

	echo "window.parent.$(document).ready(function(){setTimeout(function(){window.parent.suggestion_and_recommendation_tb_show('...', window.parent.$.ajaxBox('suggestion.friends','iFriendId=".$iItemId."&sSuggestionType=suggestion&sModule=suggestion_advancedphoto&sLinkCallback=".$sLinkCallback."&sTitle=".$sTitle."&sPrefix=&sExpectUserId=".$sExpectUserId."'));}, 500);});";
 } ?>