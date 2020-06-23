<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */

class Socialad_Service_Date extends Phpfox_Service
{
	public function getStartOfDay($iTimeStamp) {
		return mktime(0, 0,0, date('m', $iTimeStamp), date('d', $iTimeStamp), date('y', $iTimeStamp));
	}

	public function getEndOfDay($iTimeStamp) { 
		return mktime(23, 59, 59, date('m', $iTimeStamp), date('d', $iTimeStamp), date('y', $iTimeStamp));
	}

	public function getDateString($aData) {
		return $aData['month'] . '/' . $aData['day'] ;
	}

	public function convertTime($iTimeStamp) {
		if(!$iTimeStamp) {
			return _p('none');
		}
		return date(Phpfox::getParam('core.global_update_time'), $iTimeStamp);
	}

}



