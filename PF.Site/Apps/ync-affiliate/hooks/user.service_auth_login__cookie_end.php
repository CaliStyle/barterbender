<?php
if(Phpfox::isAppActive('YNC_Affiliate') && (int)$aRow['view_id'] === 0 && ($data = storage()->get('yncaffiliate_pending_user_' . $aRow['user_id']))) {
    $valueObject = $data->value;
    if((int)$valueObject->invite_link_id)
    {
        $sIsAffiliate = Phpfox::getService('yncaffiliate.affiliate.affiliate')->checkIsAffiliate($valueObject->invite_user_id);
        if($sIsAffiliate && $sIsAffiliate == 'approved'){
            if(Phpfox::getService('yncaffiliate.affiliate.process')->addAssoc((int)$valueObject->invite_user_id, $aRow['user_id'], (int)$valueObject->invite_link_id, (int)$valueObject->invite_time))
            {
                Phpfox::removeCookie('ynaf_invite_user');
                Phpfox::removeCookie('ynaf_invite_link_id');
                Phpfox::removeCookie('ynaf_invite_time');
            }
        }
    }
    elseif(setting('ynaf_intergrate_invitation')){
        Phpfox::getService('yncaffiliate.affiliate.process')->addAssocByInvitation(['email' => $aRow['email'], 'user_id' => $aRow['user_id']]);
    }
    storage()->del('yncaffiliate_pending_user_' . $aRow['user_id']);
}