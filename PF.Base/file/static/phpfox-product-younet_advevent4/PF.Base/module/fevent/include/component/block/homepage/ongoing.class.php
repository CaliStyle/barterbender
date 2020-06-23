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
class Fevent_Component_Block_Homepage_Ongoing extends Phpfox_Component
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
		list($iTotal, $aOngoing) = Phpfox::getService('fevent')->getOnHomepageByType('ongoing', $iLimit, $bIsPage, false, false, $pageID);

		$len = count($aOngoing);
		$formatTime = Phpfox::getParam('fevent.fevent_browse_time_stamp');
        $iCurYear = Phpfox::getTime('Y');

		for($i = 0; $i < $len; $i ++){
			$aOngoing[$i]['d_type'] = $oHelper->getTimeLineStatus($aOngoing[$i]['start_time'], $aOngoing[$i]['end_time']);
			$aOngoing[$i]['d_left'] = $oHelper->timestampToCountdownString($aOngoing[$i]['end_time'], 'ongoing');

			if((int)$aOngoing[$i]['isrepeat'] >= 0)
			{
				$aOngoing[$i]['d_repeat_time'] = $oHelper->displayRepeatTime((int)$aOngoing[$i]['isrepeat'], (int)$aOngoing[$i]['timerepeat']);
			}

			$aOngoing[$i]['date_start_time'] = $oHelper->displayTimeByFormat($formatTime, (int)$aOngoing[$i]['start_time']); //day
            $aOngoing[$i]['short_start_time'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aOngoing[$i]['start_time']); //hour

            $aOngoing[$i]['date_end_time1'] = $oHelper->displayTimeByFormat('M j', (int)$aOngoing[$i]['end_time']);
            $aOngoing[$i]['date_end_time'] = $oHelper->displayTimeByFormat($formatTime, (int)$aOngoing[$i]['end_time']);
            $aOngoing[$i]['date_end_time_hour'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aOngoing[$i]['end_time']);
            $aOngoing[$i]['year'] = $oHelper->displayTimeByFormat('Y', (int)$aOngoing[$i]['end_time']);
            $aOngoing[$i]['check'] = abs($iCurYear - $aOngoing[$i]['year']);

			list($aOngoing[$i]['count_attendees'], $aOngoing[$i]['attendees']) = Phpfox::getService('fevent')->getInvites($aOngoing[$i]['event_id'], 1, 0, 12);
		
			$oHelper->getImageDefault($aOngoing[$i],'home');
			
		}

        $this->template()->assign(array(
            'aOngoing' => $aOngoing,
            'iTotal' => $iTotal,
            'iLimit' => $iLimit,
            'sView' => 'ongoing',
            'sCustomClassName' => 'ync-block',
            'sHeader' => _p('fevent.oge_title')));

		return 'block';		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fevent.component_block_homepage_ongoing_clean')) ? eval($sPlugin) : false);
	}
}

?>