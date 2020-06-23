<?php

$aShareFeedInsert = array(
	         	'sType' => 'resume',
	            'iItemId' => $aResume['resume_id'],
	            'bIsCallback' => false,
	            'aCallback' => null,
	            'iPrivacy' => 0,
	            'iPrivacyComment' => 0,
        	);
			$sIdCache = Phpfox::getLib('cache')->set("socialpublishers_feed_" . Phpfox::getUserId());
            Phpfox::getLib('cache')->save($sIdCache, $aShareFeedInsert);
			
			?>