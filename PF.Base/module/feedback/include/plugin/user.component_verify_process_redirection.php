<?php

	$iUserId = Phpfox::getLib('session')->get('cache_user_id');
	

	if (empty($iUserId) || $iUserId == 0)
	{
		return false;
	}
	$sEmail = phpfox::getLib('database')
			 ->select('u.email')
			 ->from(phpfox::getT('user'), 'u')
			 ->where('u.user_id = '.$iUserId)
			 ->execute('getSlaveField');

	if(empty($sEmail))
	{
		return false;
	}
	$aRows = Phpfox::getLib('database')
			->select('fb.*')
			->from(Phpfox::getT('feedback'), 'fb')
			->where('fb.email = "'.$sEmail.'"')
			->execute('getRows');
	if(empty($aRows))
	{
		return false;
	}
	$aUpdate = array('user_id'=>$iUserId, 'privacy'=>1);
	foreach($aRows as $iCnt =>$aValue)
	{
		if($aRows[$iCnt]['user_id'] == 0 || $aRows[$iCnt]['user_id'] == NULL)
		{
			$isUpdate = Phpfox::getLib('database')->update(Phpfox::getT('feedback'), $aUpdate, 'feedback_id = '. (int) $aRows[$iCnt]['feedback_id']);
			if($aRows[$iCnt]['is_approved'] == 1)
			{
				Phpfox::getService('user.activity')->update($iId, 'feedback');
			}
		}
		
	}

	
?>