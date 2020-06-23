<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Controller_Pay extends Phpfox_Component{

	private function _checkIfSubmittingAForm() {
		if ($this->request()->getArray('val')) {
			return true;
		} else {
			return false;
		}
	}


	public function process ()
	{

		$iContestId = $this->request()->get('id');


		if($this->_checkIfSubmittingAForm())
		{
			$aVals = $this->request()->getArray('val');
			if($iTransactionId = Phpfox::getService('contest.contest.process')->payForPublishContest($aVals, $iContestId)) { 
				
			}

		}


		$iTotalFee = Phpfox::getService('contest.contest')->getTotalFees();

		$aFees = Phpfox::getService('contest.contest')->getAllFees();

		$this->template()->assign(array(
			'aYnContestFees' => $aFees
			));

		$this->template()->setHeader(
			array(
				'yncontest.js' => 'module_contest',
				)
			);

	}
}