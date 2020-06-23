<?php
	if($aImages)
	{
		$aYnContestLastImage = end($aImages);
		if($aYnContestLastImage['photo_id'] == $aPhoto['photo_id'] )
		{
			Phpfox::getService('contest.contest')->handlerAfterAddingEntry($sType = 'photo', 
		$iPhotoItemId = $aPhoto['photo_id'] );
		}
	}
	
	
?>