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

class Socialad_Service_CustomInfor_Process extends Phpfox_Service
{
	public function __construct() {
		$this->_sCustominforTable = Phpfox::getT('socialad_custominfor');
	}

	public function handleSubmitForm($aVals) {
		
		$oFilter = Phpfox::getLib('parse.input');
		$aInsert = array( 
			'custominfor_type_id' => $aVals['custominfor_type_id'],
			'content' => $oFilter->clean($aVals['content']),
			'content_parsed' => $oFilter->prepare($aVals['content']),
			'last_edited_time' => PHPFOX_TIME
		);	
		$this->deleteByTypeId($aVals['custominfor_type_id']);

		$iId = $this->database()->insert($this->_sCustominforTable, $aInsert);
		return $iId;
	}


	public function deleteByTypeId($sTypeName) {
		$this->database()->delete($this->_sCustominforTable, 'custominfor_type_id = \'' . $sTypeName .'\'');

	}
}



