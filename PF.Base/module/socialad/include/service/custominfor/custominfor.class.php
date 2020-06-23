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

class Socialad_Service_Custominfor_Custominfor extends Phpfox_Service
{
	public function __construct() {
		$this->_sCustominforTable = Phpfox::getT('socialad_custominfor');
	}

	public function getAllCustomInfors() { 
		$aRows = $this->database()->select('*')
			->from($this->_sCustominforTable)
			->execute('getRows');

		$aReturn = array();
		$aReturn['data'] = $aRows;

		$aReturn['terms_and_conditions_parsed'] = '';
		$aReturn['terms_and_conditions'] = '';
		$aReturn['pay_later_instructions'] = '';
		$aReturn['pay_later_instructions_parsed'] = '';

		foreach($aRows as $aRow) {
			switch($aRow['custominfor_type_id']) {
			case 'terms_and_conditions':
				$aReturn['terms_and_conditions_parsed'] = $aRow['content_parsed'];
				$aReturn['terms_and_conditions'] = $aRow['content'];
				break;
			case 'pay_later_instructions':
				$aReturn['pay_later_instructions'] = $aRow['content'];
				$aReturn['pay_later_instructions_parsed'] = $aRow['content_parsed'];

				break;
			}
		}

		return $aReturn;

	}

	public function getTermsAndConditions() {
		return $this->getContentParsedById('terms_and_conditions');	
	}

	public function getManualPaymentInstructions() {
		return $this->getContentParsedById('pay_later_instructions');	
	}
	public function getContentParsedById($sId) { 
		$aRow = $this->database()->select('*')
			->from($this->_sCustominforTable)
			->where('custominfor_type_id = \'' . $sId . '\'')
			->execute('getRows');

		return $aRow ? $aRow[0]['content_parsed'] : '';
	}
}



