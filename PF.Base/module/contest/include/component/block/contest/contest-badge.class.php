<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Contest_Contest_Badge extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
		$iContestId = $this->getParam('contest_id');
		$iStatus = $this->getParam('status');

		$bIsShowContestPhoto = false;
		$bIsShowDescription = false;

		if( $iStatus == Phpfox::getService('contest.constant')->getBadgeStatusIdByName('both') || 
			$iStatus == Phpfox::getService('contest.constant')->getBadgeStatusIdByName('photo'))
		{
			$bIsShowContestPhoto = true;
		}

		if( $iStatus == Phpfox::getService('contest.constant')->getBadgeStatusIdByName('both') || 
			$iStatus == Phpfox::getService('contest.constant')->getBadgeStatusIdByName('description'))
		{
			$bIsShowDescription = true;
		}


		$aContest = Phpfox::getService('contest.contest')->getContestByid($iContestId);
		$this->template()->assign(array(
				'iStatus' => $iStatus,
				'aContest' => $aContest,
				'bIsShowContestPhoto' => $bIsShowContestPhoto,
				'bIsShowDescription' => $bIsShowDescription
			)
		);	
    }
    
}

?>