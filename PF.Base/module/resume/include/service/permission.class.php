<?php

defined('PHPFOX') or exit('NO DICE!');

class Resume_Service_Permission extends Phpfox_service
{
    public function canViewResume($aResume, $iUserId = 0)
    {
       
        if (!$iUserId)
        {
            $iUserId = Phpfox::getUserId();
        }
        
        if (!$aResume)
        {
            return false;
        }
       
	$bViewResumeRegistration = Phpfox::getService('resume.account')->checkViewResumeRegistration($iUserId);	
		 
        if($aResume['user_id']==$iUserId)
        {
            return true;
        }
       
        if($aResume['privacy'] == '0')
            return true;
        
        $bIsFriend = Phpfox::isModule('friend') ? Phpfox::getService('friend')->isFriend( $iUserId, $aResume['user_id']) : 0;
         
        if($aResume['privacy']==2 && $bIsFriend)
        {
            return true;
        }
        
        if($aResume['privacy']==1 && $bViewResumeRegistration)
        {
            return true;
        }
        
        return false;
    }
}
