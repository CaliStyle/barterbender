<?php 
;

if (Phpfox::isModule('directory') && isset($aVals['module_id']) && $aVals['module_id'] == 'directory' && isset($aVals['item_id']) && (int)$aVals['item_id'] > 0)
{
	$this->database()->update($this->_sTable, array('module_id' =>$aVals['module_id'], 'item_id' => (int)$aVals['item_id']), 'poll_id = ' . $iId);
	if(isset($aInsert) && isset($aInsert['view_id']) && $aInsert['view_id'] == 0){
		$iBusinessId = (int)$aVals['item_id'];
		$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
		if($iBusinessId != null && isset($aBusiness['business_id'])){
			if ($aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.approved')
				|| $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.running')
				|| $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.completed')
				)
			{
				if ($bIsUpdate)
				{
					// do nothing
				} else {
					if (!Phpfox::getUserParam('poll.poll_requires_admin_moderation'))
					{
						(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($aVals['module_id'] . '.getFeedDetails', $aVals['item_id']))->add('poll', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0), $aVals['item_id']) : null);
						
						// Update user activity
						Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'poll');
					}
				}

	            // send notification to owner 
	            Phpfox::getService('notification.process')->add('directory_postitem', $iBusinessId, $aBusiness['user_id'], Phpfox::getUserId());
		        // send notification to follower(s)
		        $aFollowers = Phpfox::getService('directory')->getFollowerIds((int) $iBusinessId);
		        foreach ($aFollowers as $keyaFollowers => $valueaFollowers) {
		            Phpfox::getService('notification.process')->add('directory_postitem', (int) $iBusinessId, $valueaFollowers['user_id'], Phpfox::getUserId());
		        }
			}
		}
	}
}

;
?>