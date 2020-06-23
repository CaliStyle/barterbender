<?php

if (Phpfox::isModule('notification') 
	&& isset($sType) == true
	&& 'fevent_repeattonormalwarning' == $sType
	&& isset($iItemId) == true
	&& -1 == $iItemId
	&& isset($iOwnerUserId) == true
	&& 'getUserId' == $iOwnerUserId
	)
{
	$iOwnerUserId = Phpfox::getUserId();
}

?> 