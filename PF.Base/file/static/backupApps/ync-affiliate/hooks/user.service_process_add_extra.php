<?php
    if(Phpfox::isModule('yncaffiliate') && $iId)
    {
        $iInviteUserId = Phpfox::getCookie('ynaf_invite_user');
        $iInviteLinkId = Phpfox::getCookie('ynaf_invite_link_id');
        $iInviteTime = Phpfox::getCookie('ynaf_invite_time');
        if((int)$iInviteUserId)
        {
            $sIsAffiliate = Phpfox::getService('yncaffiliate.affiliate.affiliate')->checkIsAffiliate($iInviteUserId);
            if($sIsAffiliate && $sIsAffiliate == 'approved'){
                if(Phpfox::getService('yncaffiliate.affiliate.process')->addAssoc($iInviteUserId,$iId,$iInviteLinkId,$iInviteTime))
                {
                    Phpfox::removeCookie('ynaf_invite_user');
                    Phpfox::removeCookie('ynaf_invite_link_id');
                    Phpfox::removeCookie('ynaf_invite_time');
                }
            }
        }
        elseif(setting('ynaf_intergrate_invitation')){
            Phpfox::getService('yncaffiliate.affiliate.process')->addAssocByInvitation($aInsert);
        }
    }

?>