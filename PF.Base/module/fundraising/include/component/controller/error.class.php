<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Controller_Error extends Phpfox_Component {

	public function process() {
		Phpfox::getService('fundraising')->buildMenu();
		$iStatus = $this->request()->get('status');
		$sErrorMessage  = '';
		$aErrors = Phpfox::getService('fundraising')->getAllErrorStatus();
		foreach($aErrors as $aError)
		{
			if($aError['code'] == $iStatus)
			{
				$sErrorMessage = $aError['phrase'];
				break;
			}
		}

		
		$this->template()->assign(array(
			'sErrorMessage' => $sErrorMessage
		));
	}

}

?>
