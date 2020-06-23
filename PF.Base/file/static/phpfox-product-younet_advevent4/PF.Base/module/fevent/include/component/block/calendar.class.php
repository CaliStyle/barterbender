<?php

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Fevent_Component_Block_Calendar extends Phpfox_Component
{
    public function process()
    {

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
        $aTemp = array();
        foreach ($aBirthdays as $iKey => $aFriend)
        {
            $aTemp = array_merge($aTemp, $aBirthdays[$iKey]);
        }

        $aJsEvents = array_merge($aJsEvents,$aTemp);

        foreach ($aJsEvents as $iKey => $aEvent)
        {
            if (!empty($aJsEvents[$iKey]['bday'])) {
                $aJsEvents[$iKey]['calendar'][] = $aJsEvents[$iKey]['bday1'];
            }
            else {
                $aJsEvents[$iKey]['calendar'][] = Phpfox::getTime('Y/m/d', $aEvent['start_time']);
            }
            $aJsEvents[$iKey]['d_start_time_hour'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aEvent['start_time']);
            $aJsEvents[$iKey]['d_end_time_hour'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aEvent['end_time']); // time hour
            $aJsEvents[$iKey]['detail_end_time'] = $oHelper->displayTimeByFormat(Phpfox::getParam('core.global_update_time'), (int)$aEvent['end_time']); //day
        }

        $themecache = Phpfox::getLib('template')->getThemeCache();
        
		//get start week
		$startWeek = Phpfox::getParam('fevent.fevent_start_week');
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

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
    }
}

?>