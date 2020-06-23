<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Entry_Set_Winning_Entries extends Phpfox_Component {

    public function process() {
        
        $aIdEntry = $this->getParam('aIdEntry');
		$number_entry_max = 0;
		$aId = explode(',', $aIdEntry);
		$aEntries = array();
		$aContestWinning = array();
		$TotalWinning = 0;
		$limit_entries = 0;
		$link = '';
		foreach($aId as $Id){
			$aEntries[] = $aContestWinning = PHpfox::getService('contest.entry')->getContestEntryById($Id);
			if($number_entry_max==0)
			{
				$number_entry_max = $aEntries[count($aEntries)-1]['number_winning_entry_max'];	
			}
		}
		if($aContestWinning){
			$TotalWinning = Phpfox::getService('contest.entry')->getTotalWinningEntries($aContestWinning['contest_id'],$aIdEntry);
			$link = Phpfox::getLib('url')->permaLink('contest', $aContestWinning['contest_id'],$aContestWinning['contest_name'])."view_winning/";
		}
		if(($TotalWinning + count($aId))>$number_entry_max)
		{
			$limit_entries = 1;
		}

        $this->template()->assign(array(
                'aEntries' => $aEntries,
                'limit_entries' => $limit_entries,
               	'abc' => $number_entry_max,
               	'link' => $link,
            )
        );
    }

}