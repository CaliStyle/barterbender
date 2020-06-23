<?php
;

if (Phpfox::getLib('request')->get('module') == 'directory')
{
	$aBusiness = Phpfox::getService('directory')->getBusinessForEdit($aFeed['parent_user_id'], true);
	if (isset($aBusiness['business_id']) && $aBusiness['user_id'] == Phpfox::getUserId())
	{
		define('PHPFOX_FEED_CAN_DELETE', true);
	}
}

;
?>
