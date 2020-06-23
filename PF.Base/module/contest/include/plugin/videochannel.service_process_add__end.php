<?php
	$iVideoId = $iId;
	Phpfox::getService('contest.contest')->handlerAfterAddingEntry($sType = 'video', $iItemId = $iId , 2);
?>

