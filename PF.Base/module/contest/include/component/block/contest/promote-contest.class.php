<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Contest_Promote_Contest extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
		$iContestId = $this->getParam('contest_id');
		$sFrameUrl = Phpfox::getService('contest.contest')->getFrameUrl($iContestId, $iStatus = Phpfox::getService('contest.constant')->getBadgeStatusIdByName('both')); 
		$sBadgeCode = Phpfox::getService('contest.contest')->getBadgeCode($sFrameUrl); 
		$this->template()->assign(array(
				'sHeader' => _p('contest.promote_contest'),
				'sBadgeCode' => $sBadgeCode,
				'iContestId' => $iContestId
			)
		);
    }
    
}

?>