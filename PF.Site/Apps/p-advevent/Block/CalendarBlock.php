<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;

class CalendarBlock extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $bInHomepage = $this->getParam('bInHomepage', false);
        $blockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $isSideLocation = Phpfox::getService('fevent.helper')->isSideLocation($blockLocation);

        if (!($bInHomepage || $isSideLocation)) {
            return false;
        }

        $month =  $this->request()->get('month', Phpfox::getTime('n',PHPFOX_TIME));
        $year =  $this->request()->get('year', Phpfox::getTime('Y',PHPFOX_TIME));
        $day = $this->request()->get('day', Phpfox::getTime('j',PHPFOX_TIME));

        $sDate = str_replace('-', '/', $this->request()->get("date"));
        $aParentModule = $this->getParam('aParentModule');
        $bIsPage = $aParentModule['module_id'] == 'pages' ? $aParentModule['item_id'] : 0;
        $aUser = $this->getParam('aUser');
        $bIsProfile = !empty($aUser['user_id']) ? $aUser['user_id'] : false;

        if(Phpfox::getLib('request')->get('view') == 'pagesevents'){
            $bIsPage = true;
        }
        $aJsEvents = Phpfox::getService('fevent')->getJsEvents($bIsPage, $bIsProfile);
        $oHelper = Phpfox::getService('fevent.helper');

        $aBirthdays = Phpfox::getService('fevent')->getBirthdays(Phpfox::getuserId(), $year);
        $aTemp = [];
        foreach ($aBirthdays as $iKey => $aFriend)
        {
            $aTemp = array_merge($aTemp, $aBirthdays[$iKey]);
        }

        $aJsEvents = array_merge($aJsEvents,$aTemp);
        foreach ($aJsEvents as $iKey => $aEvent)
        {
            if (!empty($aJsEvents[$iKey]['bday'])) {
                $aJsEvents[$iKey]['calendar'][] = $aJsEvents[$iKey]['bday1'];
                $time = strtotime($aEvent['bday']);
                $aEvent['start_time'] = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m', $time), Phpfox::getTime('d', $time), Phpfox::getTime('Y', $time));
                $aEvent['end_time'] = Phpfox::getLib('date')->mktime(23, 59, 59, Phpfox::getTime('m', $time), Phpfox::getTime('d', $time), Phpfox::getTime('Y', $time));
            }
            else {
                $aJsEvents[$iKey]['calendar'][] = Phpfox::getTime('Y/m/d', $aEvent['start_time']);
            }

            //TODO: this line using short time format, please replace with core short time format
            $aJsEvents[$iKey]['d_start_time_hour'] = $oHelper->displayTimeByFormat('g:i a', (int)$aEvent['start_time']);

            //TODO: this line using short time format, please replace with core short time format
            $aJsEvents[$iKey]['d_end_time_hour'] = $oHelper->displayTimeByFormat('g:i a', (int)$aEvent['end_time']); // time hour

            $aJsEvents[$iKey]['detail_end_time'] = $oHelper->displayTimeByFormat(Phpfox::getParam('core.global_update_time'), (int)$aEvent['end_time']); //day
            $aJsEvents[$iKey]['url'] = isset($aEvent['birthday_display_name']) ? Phpfox::getLib('url')->makeUrl('profile', [$aEvent['user_name']]) : Phpfox::getLib('url')->permalink('fevent', $aEvent['event_id'], $aEvent['title']);
            $aJsEvents[$iKey]['d_type'] = $oHelper->getTimeLineStatus($aEvent['start_time'], $aEvent['end_time']);
            $aJsEvents[$iKey]['isrepeat'] = isset($aJsEvents[$iKey]['isrepeat']) ? $aJsEvents[$iKey]['isrepeat'] : -1;
        }

        $themecache = Phpfox::getLib('template')->getThemeCache();

        //get start week
        $startWeek = 0;
        if(isset($startWeek))
        {
            $code = Phpfox::getService('fevent.process') -> getStartWeekCode($startWeek);
        }
        else
        {
            $code = 1;
        }
        $this->template()->assign(array(
            'code' => $code,
            'aJsEvents' => $aJsEvents,
            'sUrlFevevnt' => Phpfox::getLib('url')->makeUrl('fevent'),
            'sHeader' => _p('calendar'),
            'sCorePath' => Phpfox::getParam('core.path'),
            'sDate' => $sDate,
            'themecache' => $themecache,
            'sPhraseEvents' => _p('menu_fevent_events'),
            'sCustomClassName' => 'ync-block'));

        return 'block';
    }
}