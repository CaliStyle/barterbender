<?php
if(!empty($aVals['email']))
{
	$aRows = Phpfox::getLib('database')
			->select('fb.*')
			->from(Phpfox::getT('feedback'), 'fb')
			->where('fb.email = "'. $aVals['email'].'"')
			->execute('getRows');
	$aUpdate = array('user_id'=>$iId, 'privacy'=>1);
	foreach($aRows as $iCnt =>$aValue)
	{
		if($aRows[$iCnt]['user_id'] == 0)
		{
			$isUpdate = Phpfox::getLib('database')->update(Phpfox::getT('feedback'), $aUpdate, 'feedback_id = '. (int) $aRows[$iCnt]['feedback_id']);
			if($isUpdate)
			{
				if($aRows[$iCnt]['is_approved'] == 1)
				{
					Phpfox::getService('user.activity')->update($iId, 'feedback');
				}
			}
		}
	}
}
 ?>