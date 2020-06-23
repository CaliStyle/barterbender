<?php 
;

if(Phpfox::isModule('directory')){
	if(isset($job_id) && (int)$job_id > 0){
		$aYnDirectoryModuleData = Phpfox::getService('directory')->getItemOfModuleInBusiness($job_id, 15, 'jobposting');
		if(isset($aYnDirectoryModuleData['data_id'])){
			Phpfox::getService('directory.process')->updateStatusItemOfModuleInBusiness($job_id, 15, 'jobposting', 'active');

			$iBusinessId = (int)$aYnDirectoryModuleData['business_id'];
			$aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
			if($iBusinessId != null && isset($aBusiness['business_id'])){
				if ($aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.approved')
					|| $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.running')
					|| $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.completed')
					)
				{
					// delete feed in home page
					if(Phpfox::isModule('feed')){
						$aFeed = Phpfox::getService('directory.helper')->getFeed('jobposting_job', $job_id);
						if(isset($aFeed['feed_id'])){
							Phpfox::getService('feed.process')->deleteFeed($aFeed['feed_id']);
						}

						// add to directory_feed
						Phpfox::getService('directory.process')->addDirectoryFeed(array(
	                        'privacy' => $aJob['privacy'],
	                        'privacy_comment' => (isset($aJob['privacy_comment']) ? (int) $aJob['privacy_comment'] : 0),
	                        'type_id' => 'jobposting_job',
	                        'user_id' => $aJob['user_id'],
	                        'parent_user_id' => $iBusinessId,
	                        'item_id' => $job_id,
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
	}
}

;
?>