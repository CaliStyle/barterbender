<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Entry_Preview extends Phpfox_Component{

	public function process ()
	{
		$aEntryParam = $this->getParam('aEntryParam');

		$aEntry = Phpfox::getService('contest.entry')->getDataFromFoxComplyWithContestEntry($aEntryParam['iItemType'],$aEntryParam['iItemId'],$aEntryParam['iSourceId']);

		
		$sTemplateViewPath = Phpfox::getService('contest.entry')->getTemplateViewPath($aEntryParam['iItemType']);
		
		$this->template()->assign(array(
			'aEntryParam' => $aEntryParam,
			'sTemplateViewPath' => $sTemplateViewPath,
			'aYnEntry' => $aEntry
			));
	}
}