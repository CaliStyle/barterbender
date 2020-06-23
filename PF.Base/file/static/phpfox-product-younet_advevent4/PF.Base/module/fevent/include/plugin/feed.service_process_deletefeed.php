<?php

if (Phpfox::getLib('request')->get('module') == 'fevent')
{
	$aEvent = Phpfox::getService('fevent')->getForEdit($aFeed['parent_user_id'], true);
	if (isset($aEvent['event_id']) && $aEvent['user_id'] == Phpfox::getUserId())
	{
		define('PHPFOX_FEED_CAN_DELETE', true);
	}
}

?>
