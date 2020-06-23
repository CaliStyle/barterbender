<?php
    if(Phpfox::isModule('yncaffiliate') && $iId)
    {
        $iInviteUserId = Phpfox::getCookie('ynaf_invite_user');
        $iInviteLinkId = Phpfox::getCookie('ynaf_invite_link_id');
        $iInviteTime = Phpfox::getCookie('ynaf_invite_time');

        if($aInsert['view_id'] === 1) {
            $data = [];

            if((int)$iInviteUserId) {
                $data = [
                    'invite_user_id' => $iInviteUserId,
                    'invite_link_id' => $iInviteLinkId,
                    'invite_time' => $iInviteTime
                ];
            }
            elseif(setting('ynaf_intergrate_invitation')){
                $user = db()->select('i.*')
                    ->from(Phpfox::getT('invite'),'i')
                    ->join(Phpfox::getT('user'), 'u', 'u.user_id = i.user_id')
                    ->where('i.email = \''. $aInsert['email'].'\'')
                    ->execute('getSlaveRow');
                if(!empty($user)) {
                    $data = [
                        'invite_user_id' => $user['user_id'],
                        'invite_link_id' => 0,
                        'invite_time' => $user['time_stamp']
                    ];
                }
            }

            if(!empty($data)) {
                storage()->set('yncaffiliate_pending_user_' . $iId, $data);
            }
        }
        else {
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
    }

?>