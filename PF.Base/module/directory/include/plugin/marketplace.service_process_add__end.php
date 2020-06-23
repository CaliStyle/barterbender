<?php 
;

if(Phpfox::isModule('directory')){
    $module = Phpfox::getLib('request')->get('module');
    $item = Phpfox::getLib('request')->get('item');
	if($module == 'directory' && (int)$item > 0 && isset($iId) && (int)$iId > 0){
		$status = 'inactive';
		if (!Phpfox::getUserParam('marketplace.listing_approve')){
			$status = 'active';
			
			$iBusinessId = (int)$item;
			$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
			if($iBusinessId != null && isset($aBusiness['business_id'])){
				if ($aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.approved')
					|| $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.running')
					|| $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.completed')
					)
				{
					// delete feed in home page
					if(Phpfox::isModule('feed')){
						$aFeed = Phpfox::getService('directory.helper')->getFeed('marketplace', $iId);
						if(isset($aFeed['feed_id'])){
							Phpfox::getService('feed.process')->deleteFeed($aFeed['feed_id']);
						}
						
						// add to directory_feed
						Phpfox::getService('directory.process')->addDirectoryFeed(array(
	                        'privacy' => $aVals['privacy'],
	                        'privacy_comment' => (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0),
	                        'type_id' => 'marketplace',
	                        'user_id' => Phpfox::getUserId(),
	                        'parent_user_id' => $iBusinessId,
	                        'item_id' => $iId,
	                        'parent_feed_id' => 0,
	                        'parent_module_id' => null,
						));
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
		Phpfox::getService('directory.process')->addItemOfModuleToBusiness(array(
            'module_id' => 16, // marketplace
            'business_id' => $item,
            'core_module_id' => 'marketplace',
            'item_id' => $iId,
            'status' => $status,
		));
	}
}

;
?>