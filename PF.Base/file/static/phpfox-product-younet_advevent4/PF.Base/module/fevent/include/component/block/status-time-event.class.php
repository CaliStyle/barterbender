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
 
class Fevent_Component_Block_Status_Time_Event extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aEvent = $this->getParam('aEvent');

		$oHelper = Phpfox::getService('fevent.helper');
		//	get extra information in event
        $aEvent['d_type'] = $oHelper->getTimeLineStatus($aEvent['start_time'], $aEvent['end_time']);
        if('upcoming' == $aEvent['d_type']){
            $aEvent['d_start_in'] = $oHelper->timestampToCountdownString($aEvent['start_time'], 'upcoming');
            $aEvent['d_start_in'] = str_replace(':', '', $aEvent['d_start_in']);
        } if('ongoing' == $aEvent['d_type']){
            $aEvent['d_left'] = $oHelper->timestampToCountdownString($aEvent['end_time'], 'ongoing');
        } if ('past' == $aEvent['d_type']) {
            $aEvent['d_left_past'] = $oHelper->timestampToCountdownString($aEvent['end_time'], 'past');
        }

		$this->template()->assign(array(
		        'sHeader' => '',
				'aEvent'	=> $aEvent,
                'sCustomClassName' => 'ync-block'
			));

        return 'block';
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fevent.component_block_attending_clean')) ? eval($sPlugin) : false);
	}
}

?>