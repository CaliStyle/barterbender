<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Controller_Error extends Phpfox_Component {

	public function process() {
		$iStatus = $this->request()->get('status');

		$sErrorMessage  = '';
		$aErrors = Phpfox::getService('contest.constant')->getAllErrorStatus();
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
