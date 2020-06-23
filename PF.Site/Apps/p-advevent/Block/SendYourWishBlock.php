<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;

class SendYourWishBlock extends Phpfox_Component
{
    public function process()
    {
        $loadContent = $this->getParam('load_content', false);
        $aBirthdays = Phpfox::getService('fevent')->getBirthdaysInCurrentYear(Phpfox::getuserId());
        if(empty($aBirthdays[0])) {
            return false;
        }
        $todayBirthdays = $aBirthdays[0];
        $birthdaysParsed = [];

        $todayUserIds = array_column($todayBirthdays, 'user_id');
        $sentWishes = Phpfox::getService('fevent')->checkSentWishes($todayUserIds);
        foreach($todayBirthdays as $todayBirthday) {
            $birthdaysParsed[$todayBirthday['user_id']] = $todayBirthday;
            if(!empty($sentWishes[$todayBirthday['user_id']])) {
                $birthdaysParsed[$todayBirthday['user_id']] = array_merge($birthdaysParsed[$todayBirthday['user_id']], [
                    'is_sent_message_wish' => true,
                    'message_wish_text' => $sentWishes[$todayBirthday['user_id']]
                ]);
            }
        }

        $this->template()->assign([
            'dateFormat' => date('d M'),
            'todayBirthdays' => $birthdaysParsed,
            'loadContent' => $loadContent
        ]);

        return 'block';
    }
}