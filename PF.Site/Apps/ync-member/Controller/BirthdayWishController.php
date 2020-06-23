<?php

/**
 * [PHPFOX_HEADER]
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		PhuongNV
 * @package  		yn_member
 */

namespace Apps\YNC_Member\Controller;

use Phpfox;
use Phpfox_Component;

class BirthdayWishController extends Phpfox_Component
{

    public function process()
    {
        Phpfox::isUser(true);

        title(_p('Send birthday wishes'));

        list(,$aTodayBirthdays,,) = Phpfox::getService('ynmember.browse')->getBirthdays(4, 4, false);
        $aSents = Phpfox::getService('ynmember.browse')->getSentBirthdayWishes();

        $this->template()->assign([
            'aUsers' => $aTodayBirthdays,
            'aSents' => $aSents
        ]);
    }
}