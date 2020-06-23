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
class Fevent_Component_Block_Info extends Phpfox_Component
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
        $aEventPast = '';
        if ('past' == $aEvent['d_type']) {
            $aEventPast = $oHelper->timestampToCountdownString($aEvent['end_time'], 'past');
        }

        $aEventAddress = '';

        if ($aEvent['lat'] !=0 && $aEvent['lng'] !=0) {
            $sAddress = '';
            if (!empty($aEvent['location'])) {
                $sAddress .= $aEvent['location'] . ' ';
            }
            if (!empty($aEvent['address'])) {
                $sAddress .= $aEvent['address'] . ', ';
            }
            if (!empty($aEvent['city'])) {
                $sAddress .= $aEvent['city'];
            }

            $aEvent['saddress'] = $sAddress;
            $aEventAddress = $aEvent['saddress'];
        }

		$sPhraseRecurrent = "";
		if($aEvent['isrepeat'] >= 0){
			switch ($aEvent['isrepeat']) {
				case 0:
					$sPhraseRecurrent = _p('this_event_repeats_everyday');
					break;
				case 1:
					$sPhraseRecurrent = _p('this_event_repeats_every_week_on')." ".Phpfox::getTime('D',$aEvent['start_time']);
					break;
				case 2:
					$sPhraseRecurrent = _p('this_event_repeats_every_month');
					break;
				default:
					# code...
					break;
			}	
		}
		
		$iEventId = $aEvent['event_id'];
		$iOrginId = $aEvent['org_event_id'];;
		 
		$iPage = 0;
		$iPageSize = 3;

		list($iCnt,$aCurrentEvents) =  Phpfox::getService('fevent')->getAjaxBrotherEventByEventId($iEventId,$iOrginId,$iPage,$iPageSize);
        list($iCntAll,$aAllCurrentEvents) =  Phpfox::getService('fevent')->getAjaxBrotherEventByEventId($iEventId,$iOrginId,$iPage,$iCnt);

		foreach ($aCurrentEvents as $iKey => $aCurrentEvent) {
            $aCurrentEvents[$iKey] = Phpfox::getService('fevent.helper')->retrieveEventPermissions($aCurrentEvents[$iKey]);
        }
        foreach ($aAllCurrentEvents as $iKey => $aCurrentEvent) {
            $aAllCurrentEvents[$iKey] = Phpfox::getService('fevent.helper')->retrieveEventPermissions($aAllCurrentEvents[$iKey]);
        }

		$sFullDescription = $aEvent['description'];
		$iLengthDescription = strlen($sFullDescription);

		$this->template()->assign(array(
				'iEventId' => $iEventId ,
				'iOrginId' => $iOrginId ,
				'iPage' => $iPage ,
				'iPageSize' => $iPageSize ,
				'iCount' => $iCnt ,
				'iLengthDescription' => $iLengthDescription ,
				'sPhraseRecurrent' => $sPhraseRecurrent,
				'aItems'	=> $aCurrentEvents,
                'aAllItems' => $aAllCurrentEvents,
                'aEventAddress' => $aEventAddress
			));

		if (!($aEvent = $this->getParam('aEvent')))
		{
			return false;
		}

        $aImages = Phpfox::getService('fevent')->getImages($aEvent['event_id']);
        $this->template()->assign(array(
                'aImages' => $aImages ,
                'sCorePath' => Phpfox::getParam('core.path'),
                'sDefaultPhoto' => Phpfox::getService('fevent')->getDefaultPhoto(),
                'aEvent' => $aEvent,
                'aEventPast' => $aEventPast
            )
        );

	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fevent.component_block_info_clean')) ? eval($sPlugin) : false);
	}
}

?>