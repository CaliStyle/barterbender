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
class Fevent_Component_Block_ViewMore_Events extends Phpfox_Component
{
    public function process()
    {
    	$month = $this->getParam('month');
    	$year = $this->getParam('year');
    	$day = $this->getParam('day');

        $start_date = mktime(0,0,0,$month,$day,$year);
        $end_date = mktime(23,59,59,$month,$day,$year);

        $oHelper = Phpfox::getService('fevent.helper'); 

        $aRows = Phpfox::getLib('database')->select('*')
				        ->from(Phpfox::getT('fevent'),'m')
				        ->where('m.view_id = 0 AND m.start_time >= '.$oHelper->convertFromUserTimeZone($start_date) .' AND m.start_time < '.$oHelper->convertFromUserTimeZone($end_date))
                        ->execute('getRows');

        $aRows = Phpfox::getService('fevent')->checkPrivacy($aRows, false, false);

        $aBirthdays = Phpfox::getService('fevent')->getBirthdays(Phpfox::getuserId(), $year);
        $aTemp = array();
        foreach ($aBirthdays as $iKey => $aFriend)
        {
            foreach ($aBirthdays[$iKey] as $iKey1 => $aFriend1)
            {
                if ($aBirthdays[$iKey][$iKey1]['day'] == $day && $aBirthdays[$iKey][$iKey1]['month'] == $month)
                    $aTemp = array_merge($aTemp, $aBirthdays[$iKey]);
                    break;
            }
        }

        $aRows = array_merge($aRows,$aTemp);

        $oHelper = Phpfox::getService('fevent.helper');
        $formatTime = Phpfox::getParam('fevent.fevent_browse_time_stamp');
        $iCount = 0;
		if(count($aRows)){
			foreach ($aRows as $key => $aEvent) {
                $iCount++;
                if (!empty($aRows[$key]['user_name'])){
                    $aRows[$key]['url'] = Phpfox::getLib('url')->makeUrl($aRows[$key]['user_name']);
                }
                if($aEvent['start_time'] != 0){
                    $aRows[$key]['start_time_format'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'),$aEvent['start_time']);
                    $aRows[$key]['url'] = Phpfox::getLib('url')->makeUrl('fevent',
                            array($aEvent['event_id'], $aEvent['title']));
                    $aRows[$key]['date_end_time'] = $oHelper->displayTimeByFormat($formatTime, (int)$aRows[$key]['end_time']);
                    $aRows[$key]['date_end_time_hour'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aRows[$key]['end_time']);
                }
                if($aEvent['isrepeat'] >= 0) {
                    $aRows[$key]['d_repeat_time'] = $oHelper->displayRepeatTime((int)$aRows[$key]['isrepeat'],
                        (int)$aRows[$key]['timerepeat']);
                }
            }
		}
	    $this->template()->assign(array(
            'iCount'  => $iCount,
            'aEvents' => $aRows,
        ));

    }
}