<?php
	if($aVals['post_status'] == 1)
	{
		Phpfox::getService('contest.contest')->handlerAfterAddingEntry($sType = 'blog', $iItemId = $iId );
	}
	
?>