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
class Fevent_Component_Block_Sponsored extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{
		$oHelper = Phpfox::getService('fevent.helper'); 

		if (!Phpfox::isModule('ad'))
		{
			return false;
		}	    
		
		if (defined('PHPFOX_IS_GROUP_VIEW') || defined('PHPFOX_IS_PAGEs_VIEW') || defined('PHPFOX_IS_USER_PROFILE'))
	    {
			return false;
	    }
	    
	    $aSponsorEvents = Phpfox::getService('fevent')->getRandomSponsored();
	    if (empty($aSponsorEvents))
	    {
			return false;
	    }

	    $formatTime = Phpfox::getParam('fevent.fevent_browse_time_stamp');
	    
	    $aSponsorEvents['d_type'] = $oHelper->getTimeLineStatus($aSponsorEvents['start_time'], $aSponsorEvents['end_time']);
		$aSponsorEvents['d_left'] = $oHelper->timestampToCountdownString($aSponsorEvents['end_time'], 'ongoing');

		if((int)$aSponsorEvents['isrepeat'] >= 0)
		{
			$aSponsorEvents['d_repeat_time'] = $oHelper->displayRepeatTime((int)$aSponsorEvents['isrepeat'], (int)$aSponsorEvents['timerepeat']);
		}
		
		$aSponsorEvents['d_start_time'] = $oHelper->displayTimeByFormat($formatTime, (int)$aSponsorEvents['start_time']);
		//	any status event (upcoming, ongoing, past) has start time
		//	with: upcoming event: start time at this time is next start time
		$aSponsorEvents['d_next_start_time'] = $aSponsorEvents['d_start_time'];
		$aSponsorEvents['d_end_time'] = $oHelper->displayTimeByFormat($formatTime, (int)$aSponsorEvents['end_time']);
	
		$aSponsorEvents['date_start_time'] = $oHelper->displayTimeByFormat($formatTime, (int)$aSponsorEvents['start_time']); //day
        $aSponsorEvents['M_start_time'] = $oHelper->displayTimeByFormat('M', (int)$aSponsorEvents['start_time']); //month
        $aSponsorEvents['short_start_time'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aSponsorEvents['start_time']); //hour

        $aSponsorEvents['date_end_time'] = $oHelper->displayTimeByFormat(Phpfox::getParam('core.global_update_time'), (int)$aSponsorEvents['end_time']);
        $aSponsorEvents['date_end_time_hour'] = $oHelper->displayTimeByFormat(Phpfox::getParam('fevent.fevent_basic_information_time_short'), (int)$aSponsorEvents['end_time']);
		
		list($aSponsorEvents['count_attendees'], $aSponsorEvents['attendees']) = Phpfox::getService('fevent')->getInvites($aSponsorEvents['event_id'], 1, 0, 12);
	
		$oHelper->getImageDefault($aSponsorEvents,'home');
	    
	    Phpfox::getService('ad.process')->addSponsorViewsCount($aSponsorEvents['sponsor_id'], 'fevent');
		
	    $this->template()->assign(array(
				'sHeader' => _p('fevent.sponsored_event'),
				'aSponsorEvents' => $aSponsorEvents,
		    )
		);
	    if (Phpfox::getUserParam('fevent.can_sponsor_fevent') || Phpfox::getUserParam('fevent.can_purchase_sponsor')) {
	        $this->template()->assign([
                'aFooter' => array(
                    _p('encourage_sponsor_fevents') => $this->url()->makeUrl('fevent',['sponsor' => 1, 'view' => 'my'])
                ),
                'sCustomClassName' => 'ync-block'
            ]);
        }
		
	    return 'block';
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fevent.component_block_sponsored_clean')) ? eval($sPlugin) : false);
	}
}

?>