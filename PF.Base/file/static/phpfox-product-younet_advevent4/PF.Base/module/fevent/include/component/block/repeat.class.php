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
class Fevent_Component_Block_Repeat extends Phpfox_Component
{
	public function process()
	{
		$oHelper = Phpfox::getService('fevent.helper');
		
		$value=$this->getParam("value");
		$txtrepeat=$this->getParam("txtrepeat");

		$daterepeat_hour=$this->getParam("daterepeat_hour");
		$daterepeat_min=$this->getParam("daterepeat_min");
		$daterepeat_dur_day=$this->getParam("daterepeat_dur_day");
		$daterepeat_dur_hour=$this->getParam("daterepeat_dur_hour");

		$aHours = range(0, 23);
		$aMinutes = range(0, 59);

		$daterepeat=$this->getParam("daterepeat");	
		if(strlen($daterepeat) == 0){
			$daterepeat_hour=0;
			$daterepeat_min=0;
			$daterepeat_dur_day=0;
			$daterepeat_dur_hour=0;
		}	

		$eventID=$this->getParam("eventID");		

		//	get permission
		if((int)$eventID > 0){
			$isEdit = true;
			$canEditStartTime = $oHelper->canEditStartTimeByEventID($eventID);
			$canEditEndTime = $oHelper->canEditEndTimeByEventID($eventID);
			$canEditDuration = $oHelper->canEditDurationByEventID($eventID);
		} else {
			$isEdit = false;
			$canEditStartTime = true;
			$canEditEndTime = true;
			$canEditDuration = true;
		}


		$this->template()->assign(array(
			'core_path' => phpfox::getParam("core.path"),
			'eventID' => $eventID,
			'value' => $value,
			'txtrepeat' => $txtrepeat,
			'daterepeat' => $daterepeat,
			'daterepeat_hour' => intval($daterepeat_hour),
			'daterepeat_min' => intval($daterepeat_min),
			'daterepeat_dur_day' => intval($daterepeat_dur_day),
			'daterepeat_dur_hour' => intval($daterepeat_dur_hour),
			'isEdit' => $isEdit,
			'canEditStartTime' => $canEditStartTime,
			'canEditEndTime' => $canEditEndTime,
			'canEditDuration' => $canEditDuration,
			'aHours' => $aHours,
			'aMinutes' => $aMinutes,
            'sCustomClassName' => 'ync-block'
		));
		return 'block';
	}
}
