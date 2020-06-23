<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Pay extends Phpfox_Component{

	private function _checkIfSubmittingAForm() {
		if ($this->request()->getArray('val')) {
			return true;
		} else {
			return false;
		}
	}


	public function process ()
	{

		$iContestId = $this->getParam('contest_id');

		$aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);

		$bIsIncludePublish = false;
		if(!$aContest)
		{
			//@todo: add error handler here
			echo 'contest removed';
			return false;
		}

		$aFees = Phpfox::getService('contest.contest')->getAllFeesAndPhraseForAContest($iContestId);
		// if service fee = 0 and publish = 0 
		// 
		// 
		if(isset($aFees['publish']))
		{
			$sHeader = _p('contest.publish_contest') . ' ' . $aFees['publish']['money_text'];
			$bIsIncludePublish = true;
		}
		else
		{
			$sHeader = _p('contest.request_a_service');
		}

		
		
		$aPayContestParam = array(
			'aFees' => $aFees,
			'iContestId' => $iContestId,
			'sHeader' => $sHeader,
			'bIsIncludePublish' => $bIsIncludePublish,
			//to change text of submit button accordingly
			'bIsAlreadyPublished' => $aContest['is_published'],
			'sDefaultCurrency' => Phpfox::getService('core.currency')->getDefault()
			);


		$this->template()->assign(array(
			'aPayContestParam' => $aPayContestParam
			));


	}
}