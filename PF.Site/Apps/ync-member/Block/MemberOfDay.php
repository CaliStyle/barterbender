<?php

/**
 * [PHPFOX_HEADER]
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		PhuongNV
 * @package  		yn_member
 */

namespace Apps\YNC_Member\Block;

use \Phpfox;

class MemberOfDay extends \Phpfox_Component
{
    public function process()
    {
        $aUsers = Phpfox::getService('ynmember.browse')->getMemberOfDay();

        if (!count($aUsers))
            return false;

        $aUser = $aUsers[0];
        Phpfox::getService('ynmember.member')->processUser($aUser);

        if ($aUser['total_mutual_friends'] > 5) {
            $aUser['mutual_friends'] = array_slice($aUser['mutual_friends'], 0, 5);
        }

        $this->template()->assign([
            'sHeader' => _p('Member Of Day'),
            'aUser' => $aUser,
            'aMutualFriends' => $aUser['mutual_friends'],
        ]);

        return 'block';
    }
}