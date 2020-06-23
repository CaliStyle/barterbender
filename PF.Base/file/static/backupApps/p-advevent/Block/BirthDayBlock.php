<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;

class BirthDayBlock extends Phpfox_Component
{
    public function process()
    {
        $bInHomepage = $this->getParam('bInHomepage', false);
        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = Phpfox::getService('fevent.helper')->isSideLocation($sBlockLocation);

        if (!($bInHomepage || $bIsSideLocation)) {
            return false;
        }


        if (!Phpfox::getParam('friend.enable_birthday_notices'))
        {
            return false;
        }

        if (!Phpfox::isUser())
        {
            return false;
        }

        $aBirthdays = Phpfox::getService('fevent')->getBirthdaysInCurrentYear(Phpfox::getuserId());

        if (empty($aBirthdays) && (Phpfox::getParam('friend.show_empty_birthdays') == false))
        {
            return false;
        }

        $birthdaysParsed = [];
        $genders = Phpfox::getService('core')->getGenders();
        foreach($aBirthdays as $key => $aBirthday) {
            foreach($aBirthday as $personKey => $personBirthday) {
                if($personBirthday['gender'] != 127) {
                    $personBirthday['gender_text'] = $genders[$personBirthday['gender']];
                }
                else {
                    $personBirthday['gender_text'] = ucfirst(_p('others'));
                }

                $personBirthday['age_text'] = null;
                if(!empty($personBirthday['new_age'])) {
                    $personBirthday['age_text'] = $personBirthday['new_age'] > 1 ? _p('fevent_years_old', ['number' => $personBirthday['new_age']]) : _p('fevent_year_old', ['number' => $personBirthday['new_age']]);
                }

                if(!Phpfox::getService('user.privacy')->hasAccess($personBirthday['user_id'], 'feed.share_on_wall')) {
                    $personBirthday['no_permission_to_send_wish'] = true;
                }

                if($key == 0) {
                    $birthdaysParsed['today'][$personBirthday['user_id']] = $personBirthday;
                }
                else {
                    $date = substr($personBirthday['birthday'], 0, 2) . '/' . substr($personBirthday['birthday'], 2, 2) . '/' . substr($personBirthday['birthday'],4, 4);
                    $personBirthday['birthdate_text'] = date('D, d M', strtotime(Phpfox::getTime('m/d',strtotime($date)). '/' . Phpfox::getTime('Y')));
                    $birthdaysParsed['others'][$personBirthday['user_id']] = $personBirthday;
                }
            }
        }

        if(!empty($birthdaysParsed['today'])) {
            $todayUserIds = array_column($birthdaysParsed['today'], 'user_id');
            $sentWishes = Phpfox::getService('fevent')->checkSentWishes($todayUserIds);
            foreach($sentWishes as $userId => $sentWish) {
                if(empty($birthdaysParsed['today'][$userId]['no_permission_to_send_wish'])){
                    $birthdaysParsed['today'][$userId] = array_merge($birthdaysParsed['today'][$userId], [
                        'is_sent_message_wish' => true,
                        'message_wish_text' => $sentWish
                    ]);
                }
            }
        }

        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = Phpfox::getService('fevent.helper')->isSideLocation($sBlockLocation);

        $backgroundImage = Phpfox::getService('fevent')->getBirthdayBackgroundImage();

        $this->template()->assign(array(
                'aBirthdays' => $birthdaysParsed,
                'sHeader' => _p('friend.birthdays'),
                'sCustomClassName' => 'p-block',
                'isSideLocation' => $bIsSideLocation,
                'shortTodayText' => date('D'),
                'shortMonthText' => date('M'),
                'todayNumber' => Phpfox::getTime('d'),
                'todayCustomClass' => !empty($birthdaysParsed['today']) ? (count($birthdaysParsed['today']) == 1 ? 'one-item' : (count($birthdaysParsed['today']) == 2 ? 'two-item' : 'three-more-item')) : '',
                'backgroundImage' => $backgroundImage
            )
        );

        return 'block';
    }
}