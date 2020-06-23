<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Join extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iContestId = $this->getParam('contest_id');

		$aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);

		$this->template()->assign(array(
			'aContest' => $aContest
		));
	}
	
}

?>