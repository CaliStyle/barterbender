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
class Fevent_Component_Block_Homepage_Upcoming extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
        $bInHomepage = $this->getParam('bInHomepage', false);
        if(!$bInHomepage) {
            return false;
        }

		$oHelper = Phpfox::getService('fevent.helper'); 
		$aParentModule = $this->getParam('aParentModule');
		$bIsPage = $aParentModule['module_id'] == 'pages' ? true : false;

		$pageID = $aParentModule['module_id'] == 'pages' ? $aParentModule['item_id'] : -1;

		$iLimit = Phpfox::getParam('fevent.fevent_number_of_event_upcoming_ongoing_block_home_page');

		if (!$iLimit) {
		    return false;
        }
		list($iTotal, $aUpcoming) = Phpfox::getService('fevent')->getOnHomepageByType('upcoming', $iLimit, $bIsPage, false, false, $pageID);
		$len = count($aUpcoming);
        $formatTime = Phpfox::getParam('fevent.fevent_browse_time_stamp');
        $iCurYear = Phpfox::getTime('Y');

		for($i = 0; $i < $len; $i ++){
			$aUpcoming[$i]['d_type'] = $oHelper->getTimeLineStatus($aUpcoming[$i]['start_time'], $aUpcoming[$i]['end_time']);
			$aUpcoming[$i]['d_start_in'] = $oHelper->timestampToCountdownString($aUpcoming[$i]['start_time'], 'upcoming');

			if((int)$aUpcoming[$i]['isrepeat'] >= 0)
			{
				$aUpcoming[$i]['d_repeat_time'] = $oHelper->displayRepeatTime((int)$aUpcoming[$i]['isrepeat'], (int)$aUpcoming[$i]['timerepeat']);
			}

		    $aUpcoming[$i]['date_start_time'] = $oHelper->displayTimeByFormat($formatTime, (int)$aUpcoming[$i]['start_time']);
            $aUpcoming[$i]['short_start_time'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aUpcoming[$i]['start_time']); //hour

            $aUpcoming[$i]['date_end_time1'] = $oHelper->displayTimeByFormat('M j', (int)$aUpcoming[$i]['end_time']);
            $aUpcoming[$i]['date_end_time'] = $oHelper->displayTimeByFormat($formatTime, (int)$aUpcoming[$i]['end_time']);
            $aUpcoming[$i]['date_end_time_hour'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aUpcoming[$i]['end_time']);
            $aUpcoming[$i]['year'] = $oHelper->displayTimeByFormat('Y', (int)$aUpcoming[$i]['end_time']);
            $aUpcoming[$i]['check'] = abs($iCurYear - $aUpcoming[$i]['year']);

            list($aUpcoming[$i]['count_attendees'], $aUpcoming[$i]['attendees']) = Phpfox::getService('fevent')->getInvites($aUpcoming[$i]['event_id'], 1, 0, 12);
			
			$oHelper->getImageDefault($aUpcoming[$i],'home');

		}
		
        $this->template()->assign(array(
            'aUpcoming' => $aUpcoming,
            'iTotal' => $iTotal,
            'iLimit' => $iLimit,
            'sView' => 'upcoming',
            'sCustomClassName' => 'ync-block',
            'sHeader' => _p('fevent.ue_title')));

		return 'block';		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fevent.component_block_homepage_upcoming_clean')) ? eval($sPlugin) : false);
	}
}

?>